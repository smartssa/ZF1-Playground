<?php 
/*
{
	"venues" : [
		 { "id": "1", "name": "Cineplex Vancouver", "street": "1 West Ave", "city": "Vancouver", "province": "BC", "country": "Canada", "zip": "V6K 4R5", "lat": "49.2553", "lng": "-122.242121", "phone": "+16042745897", "website_url": "http://Cineplex.com", "description": "20 Theatres, Cineplex Vancouver is ...", "thumb_img_url": "http://xomo.ca/img64_64.jpg", "large_img_url": "http://xomo.ca/img320_420.jpg", "category": "Cinema" }
	]
}
*/

require_once 'common.php';


// get locations
$locationsql = "SELECT * FROM wp_geo_mashup_locations";
$locationset = mysql_query($locationsql, $db);

$locationreturn = array();
while ($locationrow = mysql_fetch_assoc($locationset)) {
	// get images attached to locations
	$obj = new stdClass();
	$obj->id       = $locationrow['id'];
	$obj->name     = $locationrow['saved_name'];
	$obj->street   = $locationrow['address'];
	$obj->city     = $locationrow['locality_name'];
	$obj->country  = $locationrow['country_code'];
	$obj->province = $locationrow['admin_code'];
	$obj->lat      = $locationrow['lat'];
	$obj->lng      = $locationrow['lng'];
	$locationreturn[] = $obj;
}

$return = array('venues' => $locationreturn);
// spit out json

echo json_encode($return);