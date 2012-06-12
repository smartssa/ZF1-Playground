<?php

require_once 'common.php';
// get eventss

$id = intval($_GET['event']);

if ($id > 0) {
	$eventssql = "SELECT
p.ID AS id,
pm.meta_value AS dates, 
pm_times.meta_value AS times,
p.post_title AS title,
p.post_excerpt AS description,
p.post_name AS post_name,
lr.location_id AS location_id
FROM wp_postmeta pm
LEFT JOIN wp_posts p ON (pm.post_id = p.ID)
LEFT JOIN wp_postmeta pm_times ON (pm_times.meta_key = 'start_time' AND pm_times.post_id = p.ID)
LEFT JOIN wp_geo_mashup_location_relationships lr ON (lr.object_id = p.ID)
WHERE pm.post_id = '$id' and pm.meta_key = 'event_date' AND p.post_status = 'publish' AND p.post_type = 'events';";

	$eventsset = mysql_query($eventssql, $db);
	
	header("Content-Type: text/Calendar");
	header("Content-Disposition: inline; filename=calendar.ics");
	// header("Content-Type: text/plain");
	
	echo "BEGIN:VCALENDAR\n";
	echo "VERSION:2.0\n";
	echo "PRODID:-//Luminato//NONSGML Luminato//EN\n";
	echo "METHOD:REQUEST\n"; // requied by Outlook
	
	while ($eventsrow = mysql_fetch_assoc($eventsset)) {
	// get the event
	// get the slot
	$eventTitle       = $eventsrow['title'];
	$eventDescription = $eventsrow['description'];
	
	$eventUrl = 'http://www.luminato.com/events/' . $eventsrow['post_name'];
	$times = unserialize($eventsrow['times']);
	$x = 0;
	$i = 0;
	foreach (unserialize($eventsrow['dates']) as $key => $date) {
		$i++;
		if (count($times) > 0) {
			foreach ($times[$key] as $time) {
				$x++;
				$day_time = $date . ' ' . $time;
				$ntime = strtotime($day_time);
				$eventStart = date('Ymd', $ntime).'T'.date('His', $ntime);			
				echo "BEGIN:VEVENT\n";
				echo "ORGANIZER:www.luminato.com\n";
				echo "UID:".date('Ymd').'T'.date('His')."-".rand().$x."-luminato.com\n"; // required by Outlook
				echo "DTSTAMP:".date('Ymd').'T'.date('His')."\n"; // required by Outlook
				echo "DTSTART:$eventStart\n";
				echo "SUMMARY:$eventTitle\n";
				echo "DESCRIPTION:$eventDescription\n";
				echo "URL:$eventUrl\n";
				echo "END:VEVENT\n";
			}
		} else {
			$x++;
			$day_time = $date . ' ' . $time;
			$ntime = strtotime($day_time);
			$eventStart = date('Ymd', $ntime).'T'.date('His', $ntime);			
						
			echo "BEGIN:VEVENT\n";
			echo "ORGANIZER:www.luminato.com\n";
			echo "UID:".date('Ymd').'T'.date('His')."-".rand().$x."-luminato.com\n"; // required by Outlook
			echo "DTSTAMP:".date('Ymd').'T'.date('His')."\n"; // required by Outlook
			echo "DTSTART:$eventStart\n";
			echo "SUMMARY:$eventTitle\n";
			echo "DESCRIPTION:$eventDescription\n";
			echo "URL:$eventUrl\n";
			echo "END:VEVENT\n";
		}
	}
	
	echo "END:VCALENDAR\n";
	exit();
	}
} else {
 // die silently.
}
