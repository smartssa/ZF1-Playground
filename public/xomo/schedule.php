<?php
/*
 {
 "schedule": [
 {
 "id": "22",
 "name": "Terminator - Screen 1",
 "time_start": "2011-03-12 17:00:00",
 "time_stop": "2011-03-12 19:20:00",
 "venue_id_ref": "1",
 "event_id_ref": "8",
 "price": "$12.99",
 "ticket_url": "http://tickets.ca/358/",
 "sponsors": [ "1", "2" ]
 }
 ]
 }
 */

require_once 'common.php';


// get eventss
$eventssql = "SELECT 
p.ID AS id,
pm.meta_value AS dates, 
pm_times.meta_value AS times,
pm_price.meta_value AS price,
p.post_title AS title,
pm_tickets.meta_value AS tickets_url,
pm_s1.meta_value AS partner_sponsor,
pm_s2.meta_value AS presenting_sponsor,
pm_s3.meta_value AS sponsors
FROM wp_postmeta pm
LEFT JOIN wp_posts p ON (pm.post_id = p.ID)
LEFT JOIN wp_postmeta pm_times ON (pm_times.meta_key = 'start_time' AND pm_times.post_id = p.ID)
LEFT JOIN wp_postmeta pm_price ON (pm_price.meta_key = 'event_price' AND pm_price.post_id = p.ID)
LEFT JOIN wp_postmeta pm_tickets ON (pm_tickets.meta_key = 'ticketmaster_url' AND pm_tickets.post_id = p.ID)
LEFT JOIN wp_postmeta pm_s1 ON (pm_s1.meta_key = 'partner_name' AND pm_s1.post_id = p.ID)
LEFT JOIN wp_postmeta pm_s2 ON (pm_s2.meta_key = 'presenting_sponsor_name' AND pm_s2.post_id = p.ID)
LEFT JOIN wp_postmeta pm_s3 ON (pm_s3.meta_key = 'sponsor_name' AND pm_s3.post_id = p.ID)
WHERE pm.meta_key = 'event_date' AND p.post_status = 'publish' AND p.post_type = 'events';";

$goodevents   = json_decode(file_get_contents('https://www.luminato.com/xomo/events.php'));
$allevents    = array();
foreach ($goodevents->events as $ev) {
	$allevents[] = $ev->id;
}

$goodsponsors = json_decode(file_get_contents('https://www.luminato.com/xomo/sponsors.php'));
$allsponsors  = array();
foreach ($goodsponsors->sponsors as $sp) {
	$allsponsors[] = $sp->id;
}

$duplicatemap = json_decode(file_get_contents('https://www.luminato.com/xomo/venues.php'), true);
$map = $duplicatemap['duplicate_mapping'];

// fetch all map id's and post id's to map them.
$mapssql = "SELECT * FROM wp_mappress_posts";
$mapset  = mysql_query($mapssql, $db);
$postids = array();
while ($maprow = mysql_fetch_assoc($mapset)) {
    $postids[$maprow['postid']] = $maprow['mapid'];
    if (isset($map[$maprow['mapid']])) {
        $postids[$maprow['postid']] = $map[$maprow['mapid']];    
    }
}

$eventsset = mysql_query($eventssql, $db);
$eventsreturn = array();
while ($eventsrow = mysql_fetch_assoc($eventsset)) {
	// only use events that are found in the events list
	if (! in_array($eventsrow['id'], $allevents)) {
		continue;
	}
	// get images attached to events
	$times = unserialize($eventsrow['times']);
	// every day has times.
	// $times = $times[$key];

	// fetch sponsors via name via pods table. ugh.
	$sponsors = array();
	$s1 = unserialize($eventsrow['partner_sponsor']);
	$s2 = unserialize($eventsrow['presenting_sponsor']);
	$s3 = unserialize($eventsrow['sponsors']);

	foreach ($s1 as $sponsor) {
		if ($sponsor != 'None' && $sponsor != '') {
			$sponsors[] = $sponsor;
		}
	}
	foreach ($s2 as $sponsor) {

		if ($sponsor != 'None' && $sponsor != '') {
			$sponsors[] = $sponsor;
		}
	}
	foreach ($s3 as $sponsor) {
		if (is_array($sponsor)) {
			foreach ($sponsor as $s) {
				if ($s != 'None' && $s != '') {
					$sponsors[] = $s;
				}
			}
		} else {
			if ($sponsor != 'None' && $sponsor != '') {
				$sponsors[] = $sponsor;
			}
		}
	}
	// uniquify the sponsor array and make sure these sponsors are served in sponsors.php
	// convert these names into IDs from pods.
	$names = array_unique($sponsors);
	$where = implode('","', $names);
	$idquery = 'SELECT id FROM wp_pod_tbl_sponsors WHERE name IN ("' . $where . '");';
	$spset = mysql_query($idquery);
	$spIds = array();

	while ($sprow = mysql_fetch_assoc($spset)) {
		if (in_array($sprow['id'], $allsponsors)) {
			$spIds[] = $sprow['id'];
		}
	}

	$x = 0;
	$i = 0;
	foreach (unserialize($eventsrow['dates']) as $key => $date) {
		$i++;
		if (count($times) > 0) {
			foreach ($times[$key] as $time) {
				$x++;
				$day_time = $date . ' ' . $time;
				$obj = new stdClass();
				$obj->id           = $eventsrow['id'] . str_pad($i, 2, '0', STR_PAD_LEFT) . str_pad($x, 2, '0', STR_PAD_LEFT);
				$obj->name         = $eventsrow['title'];
				$obj->time_start   = date('Y-m-d H:i:s', strtotime($day_time));
				if (date('H:i:s', strtotime($day_time)) == '00:00:00') {
					$obj->all_day = true;
				} else {
					$obj->all_day = false;
				}
				$obj->event_id_ref = $eventsrow['id'];
				$obj->venue_id_ref = $postids[$eventsrow['id']];
				$obj->ticket_url   = $eventsrow['tickets_url'];
				$obj->price        = $eventsrow['price'];
				$obj->sponsors     = $spIds;
				$eventsreturn[] = $obj;
			}
		} else {
			$x++;
			$day_time = $date . ' ' . $time;
			$obj = new stdClass();
			$obj->id           = $eventsrow['id'] . str_pad($i, 2, '0', STR_PAD_LEFT) . str_pad($x, 2, '0', STR_PAD_LEFT);
			$obj->name         = $eventsrow['title'];
			$obj->time_start   = date('Y-m-d H:i:s', strtotime($day_time));
			if (date('H:i:s', strtotime($day_time)) == '00:00:00') {
				$obj->all_day = true;
			} else {
				$obj->all_day = false;
			}
			$obj->event_id_ref = $eventsrow['id'];
			$obj->venue_id_ref = $postids[$eventsrow['id']];
			$obj->ticket_url   = $eventsrow['tickets_url'];
			$obj->price        = $eventsrow['price'];
			$obj->sponsors     = $spIds;
			$eventsreturn[] = $obj;
		}
	}
}

$return = array('schedule' => $eventsreturn);
// spit out json

echo json_encode($return);
