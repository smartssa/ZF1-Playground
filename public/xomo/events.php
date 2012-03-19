<?php
/*
{
 "events" : [
		{ "id": "8", "name": "Terminator", "thumb_img_url": "http://xomo.ca/img64_64.jpg", 
		"large_img_url": "http://xomo.ca/img320_420.jpg", 
		"description": "A human-looking, apparently unstoppable cyborg is sent from the future to kill Sarah Connor", 
		"youtube_url": "http://www.youtube.com/watch?v=c4Jo8QoOTQ4", "mp3_url": "http://xomo.ca/sound.mp3", 
		"website_url": "http://www.xomo.ca", "categories": ["action", "classic"], "Director": "James Cameron", 
		"Runtime": "107", "Cast": "Arnold Schwarzenegger, Linda Hamilton and Michael Biehn" }
	]
}

*/

require_once 'common.php';


// get eventss
$eventssql = "SELECT * FROM wp_posts where post_type = 'events' and post_status = 'publish'";
$eventsset = mysql_query($eventssql, $db);

$eventsreturn = array();
while ($eventsrow = mysql_fetch_assoc($eventsset)) {
	// get images attached to events
	$obj = new stdClass();
	$obj->id          = $eventsrow['ID'];
	$obj->name        = $eventsrow['post_title'];
	$obj->website_url = "http://www.luminato.com/events/" . $eventsrow['post_name'];
	$obj->description = $eventsrow['post_excerpt'] ?: $eventsrow['post_content'];
	// fetch image for this post (_thumnail_id) attachment
	$thumbnailsql = "SELECT pm2.meta_value as value FROM wp_postmeta pm 
			LEFT JOIN wp_posts p on (pm.meta_value = p.ID) 
			LEFT JOIN wp_postmeta pm2 ON (p.ID = pm2.post_id) 
			WHERE pm.post_id = {$eventsrow['ID']} AND pm.meta_key = '_thumbnail_id' 
			AND pm2.meta_key = '_wp_attachment_metadata'";
	$thumbnailset = mysql_query($thumbnailsql);
	if ($thumbnailset) {
		$thumbnailrow = mysql_fetch_assoc($thumbnailset);
		$img = unserialize($thumbnailrow['value']);
		$path = dirname($img['file']);
		$file = $img['sizes']['medium']['file'];
		$obj->large_img_url = "http://www.luminato.com/events/" . $path . '/' . $file;
	}
	$catsql = "select `name` from wp_term_relationships tr 
			left join wp_terms t on (tr.term_taxonomy_id = t.term_id)
			left join wp_term_taxonomy tt on (tt.term_id = t.term_id)
			where object_id = {$eventsrow['ID']} and tt.taxonomy = 'eventtype';";
	$catset = mysql_query($catsql);
	$categories = array();
	if ($catset) {
		while ($catrow = mysql_fetch_assoc($catset)) {
			$categories[] = $catrow['name'];
		}
	}
	$obj->categories  = $categories; // fetch categories
	$eventsreturn[] = $obj;
}

$return = array('events' => $eventsreturn);
// spit out json

echo json_encode($return);