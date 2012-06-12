<?php 
/*
{
	"sponsors" : [
		{"id": "1", "title": "Presenting Partner", "img_url": "http://xomo.ca/sponsor.jpg", "url": "http://xomo.ca/sponsor.html". "sort":"50"; },
		{"id": "2", "title": "Someone Else", "img_url": "http://xomo.ca/sponsor.jpg", "url": "http://xomo.ca/sponsor.html". "sort":"50"; }
	]
}
*/

require_once 'common.php';


// get sponsors
$sponsorsql = "SELECT * FROM wp_pod_tbl_sponsors";
$sponsorset = mysql_query($sponsorsql, $db);

$sponsorreturn = array();
while ($sponsorrow = mysql_fetch_assoc($sponsorset)) {
	// get images attached to sponsors
	$podsql = "SELECT wp_pod_rel.tbl_row_id FROM wp_pod LEFT JOIN wp_pod_rel ON (wp_pod.id = wp_pod_rel.pod_id) WHERE datatype = 1 and wp_pod.tbl_row_id = {$sponsorrow['id']} LIMIT 1;";
	$podset = mysql_query($podsql);
	if ($podset) {
		$podrow = mysql_fetch_assoc($podset);
		$attachment_id = $podrow['tbl_row_id'];
		// fetch the image info from wp table.
		$imgsql = "SELECT meta_value FROM wp_postmeta where meta_key = '_wp_attachment_metadata' AND post_id = '{$attachment_id}' LIMIT 1;";
		$imgset = mysql_query($imgsql);
		if ($imgset) {
			$imgmeta = mysql_fetch_assoc($imgset);
			$imgdata = unserialize($imgmeta['meta_value']);
			if ($imgdata) {
				$path = dirname($imgdata['file']);
				$obj = new stdClass();
				$obj->id      = $sponsorrow['id'];
				$obj->title   = $sponsorrow['name'];
				$obj->url     = $sponsorrow['sponsor_website'];
				// $path
				if (isset($imgdata['sizes']['post-thumb']['file'])) {
					$file = $path . '/' . $imgdata['sizes']['post-thumb']['file'];
				} else {
					$file = $imgdata['file'];
				}
				$obj->img_url = 'http://www.luminato.com/wp-content/uploads/' . $file;

				// 64x64.. gross.
				$sponsorreturn[] = $obj;
			}
		}
	}
}

$return = array('sponsors' => $sponsorreturn);
// spit out json

echo json_encode($return);
