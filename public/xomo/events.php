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
	$content = $eventsrow['post_excerpt'];
	$obj->description = $content;
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
		$obj->large_img_url = "http://www.luminato.com/wp-content/uploads/" . $path . '/' . $file;
		$file = $img['sizes']['thumbnail']['file'];
		$obj->small_img_url = "http://www.luminato.com/wp-content/uploads/" . $path . '/' . $file;
	}
	// video url
	$videosql = "SELECT meta_value FROM wp_postmeta WHERE post_id = {$eventsrow['ID']} AND meta_key = 'video_url' LIMIT 1";
	$videoset = mysql_query($videosql);
	$vidurls = array();
	if ($videoset) {
		$videorow = mysql_fetch_assoc($videoset);
		$vals = unserialize($videorow['meta_value']);
		if (is_array($vals)) {
			foreach ($vals as $key => $vid) {
				if (strlen($vid) > 10) {
					$vidurls[] = 'http://youtu.be/' . $vid;
				}
			}
		}
	}
	$obj->youtube_url = $vidurls;
	// and audio attachments.
	$obj->mp3_url = '';
	$mp3sql = "SELECT guid FROM wp_posts WHERE post_parent = {$eventsrow['ID']} AND post_type = 'attachment' AND post_mime_type = 'audio/mpeg' LIMIT 1";
	$mp3set = mysql_query($mp3sql);
	// $mp3urls = array();
	if ($mp3set) {
		$mp3row = mysql_fetch_assoc($mp3set);
		$obj->mp3_url = $mp3row['guid'];
	}
	// categories
	$catsql = "SELECT tt.taxonomy, t.name FROM wp_term_relationships tr 
		LEFT JOIN wp_term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
		LEFT JOIN wp_terms t ON (tt.term_id = t.term_id)
		WHERE tr.object_id = {$eventsrow['ID']}";
	$catset = mysql_query($catsql);
	$categories = array();
	$tags = array();
	$french = false;
	if ($catset) {
		while ($catrow = mysql_fetch_assoc($catset)) {
			if ($catrow['name'] != 'EN' && $catrow['taxonomy'] == 'eventtype') {
				$categories[] = $catrow['name'];
			}
			if ($catrow['taxonomy'] == 'post_tag') {
				$tags[] = $catrow['name'];
			}
			if ($catrow['name'] == 'FR') {
				$french = true;
				break;
			}
		}
	}
	$obj->categories  = $categories; // fetch categories
	$obj->tags        = $tags;
	if (! $french) {
		$eventsreturn[] = $obj;
	}
}

$return = array('events' => $eventsreturn);
// spit out json

echo json_encode($return);
