<?php
/*
 * sync a list of events given by the mobile app
 * 
*/
require_once 'common.php';
$time_start = microtime(true);

if ($_GET['data']) {
	$request_body = $_GET['data'];
} else {
	$request_body = $_POST['data'];
}
error_log(' xomo sync request: ' . $request_body);

header('content-type: application/json');

// pull the users' active list
if (! isset($request_body) || $request_body == '') {
	// not set. bail out.
	echo json_encode(array('error' => -6, 'note' => 'request body was empty'));
} else {
	$data = json_decode($request_body);
	if (is_object($data)) {
		$userId = intval($data->UserDoc->fbid);

		$original_data = $data;

		$finalschedule = array();
		$finalevents   = array();

		$addschedule    = array();
		$addevents      = array();

		$removeschedule = array();
		$removeevents   = array();

		$logevents      = array();

		if ($userId > 0) {
			$logevents[] = 'Syncing userId: ' . $userId;
			$logevents[] = 'Processing ' . count($data->UserDoc->schedule) . ' events from application sync.';
			foreach ($data->UserDoc->schedule as $key => $fromxomo) {
				// each from xomo should have a 'serial' (aka schedule id)
				if ($fromxomo->serial > 0) {
					$newstring = substr($fromxomo->serial, 0, -4);
					if ($fromxomo->active == 0) {
						$logevents[] = 'Removing event/schedule: ' . $newstring;
						$removeevents[$newstring] = $newstring;
						$removeschedule[$newstring] = $fromxomo;
					} else {
						$logevents[] = 'Keeping event/schedule: ' . $newstring;
						$addevents[$newstring]['id'] = $newstring;
						$addschedule[$newstring] = $fromxomo;
					}
					// a timestamp (when the user modified it)
					// and 'active' - whether it's enabled or not.
					$timestamps[$newstring] = $fromxomo->timestamp;
				} else {
					$logevents[] = 'Invalid `serial` for record #' . $key;
				}
			}

			// find a meta key with the fb id
			// get a real user
			$wpuserselect = "SELECT user_id FROM wp_usermeta WHERE meta_value = '{$userId}' AND meta_key = 'facebook_user_id';";
			$wpset = mysql_query($wpuserselect);
			if (mysql_num_rows($wpset) == 1) {
				// one real user
				$wpuserrow = mysql_fetch_assoc($wpset);
				$wpuserId = $wpuserrow['user_id'];
				$logevents[] = 'Matched with wordpress userId: ' . $wpuserId;

				// fetch the real users data
				$existingsql = "SELECT post_id, UNIX_TIMESTAMP(updated) timestamp, active FROM user_events WHERE user_id = {$wpuserId}";
				// merge their web selected events
				$existingset = mysql_query($existingsql);
				$existing = array();
				if (mysql_num_rows($existingset) > 0) {
					$logevents[] = 'Found ' . mysql_num_rows($existingset) . ' existing events for WP user.';
					// yay!
					$db_timestamps = array();
					while ($erow = mysql_fetch_assoc($existingset)) {
						$db_timestamps[$erow['post_id']] = $erow['timestamp'];
						// if this is in xomo's original request and it's disabled, ignore it when timestamp is greater
						// if (in_array($erow['post_id'], $removeevents)) {
						if ($db_timestamps[$erow['post_id']] >= $timestamps[$erow['post_id']]) {
							// database row is newer!
							if (in_array($erow['post_id'], $removeevents) && $erow['active'] == 1) {
								// active in db, newer in db, keep it.
								$logevents[] = 'local timestamp is >= for event ' . $erow['post_id'] . ' using local event, ignoring remove request.';
								unset($removeevents[$erow['post_id']]);
							} elseif ($erow['active'] == 0) {
								// newer in db, inactive in db, force remove
								$logevents[] = 'event ' . $erow['post_id'] . ' is newer and inactive in db. forcing remove.';
								$removeevents[] = $erow['post_id'];
								unset($addevents[$erow['post_id']]);
								unset($addschedule[$erow['post_id']]);
								continue;
							}
							if (isset($addevents[$erow['post_id']])) {
								// newer in db, also from xomo as add.
								$logevents[] = 'Using event ' . $erow['post_id'] . ' from local db.';
								continue;
							}
							$logevents[] = 'Using event ' . $erow['post_id'] . ' from local db.';
							$eobj = new stdClass();
							$eobj->timestamp = $erow['timestamp'];
							$eobj->active    = "1";
							$eobj->serial    = $erow['post_id'] . '0101';
							$addevents[$erow['post_id']]['id'] = $erow['post_id'];
							$addschedule[$erow['post_id']]     = $eobj;
						} else {
							$logevents[] = 'xomo timestamp for event ' . $erow['post_id'] . ' is greater than local, using xomo.';
							continue;
						}
						// }

					}
				}
				// update the wordpress users's list with the added events
				foreach ($addevents as $added) {
					$id = $added['id'];
					$insql = 'INSERT INTO user_events (user_id, post_id, updated) VALUES ("'.$wpuserId.'", "'.$id.'", NOW()) ON DUPLICATE KEY UPDATE active = 1';
					mysql_query($insql);
				}
				// remove the wordpress users's saved ones with the removed events
				foreach ($removeevents as $remove) {
					$id = $remove;
					$delsql = 'UPDATE user_events SET active = 0 WHERE user_id = "'.$wpuserId.'" AND post_id = "'.$id.'" LIMIT 1';
					mysql_query($delsql);
				}
			} else {
				$logevents[] = 'No match for wordpress user. No sync required. Application owns the data.';
			}

			$time_end = microtime(true);
			$time = round($time_end - $time_start, 4);

			$logevents[] = '** Operation complete (' . $time . ' sec) .';
			// strip the keys for a valid json object
			$finalevents   = array_values($addevents);
			$finalschedule = array_values($addschedule); //array_diff($addschedule, $removeschedule);

			$data->UserDoc->schedule = $finalschedule;
			// $data->UserDoc->events   = $finalevents;

			// debug data.
			$data->debug->event_log         = $logevents;
			$data->debug->json_base64       = base64_encode($request_body);
			$data->debug->json_length       = strlen($request_body);
			$data->debug->timestamps->xomo  = $timestamps;
			$data->debug->timestamps->db    = $db_timestamps;

			$newdata = json_encode($data);
			$insert = "INSERT INTO user_favorites (id, json_data, updated) VALUES({$userId}, '". mysql_real_escape_string($newdata, $db) . "', NOW()) ON DUPLICATE KEY update json_data = '". mysql_real_escape_string($newdata, $db) ."', updated = now();";
			$result = mysql_query($insert, $db);
			// save the new list
			$return = $newdata;
		} else {
			$return = json_encode(array('error'=> -2, 'note' => 'no user id supplied.'));
		}
	} else {
		$return = json_encode(array(
                        'error'=> -3,
                        'note' => 'invalid json object found',
                        'original_json_base64' => base64_encode($data),
                        'original_json_length' => strlen($data),
                        'original_post_serialized_base64'   => base64_encode(serialize($_POST))
                ));
	}
	// return the existing list back to the mobile app
	echo $return;
}
