<?php 
/*
{
	"venues" : [
		 { "id": "1", "name": "Cineplex Vancouver", "street": "1 West Ave", "city": "Vancouver", "province": "BC", "country": "Canada", "zip": "V6K 4R5", "lat": "49.2553", "lng": "-122.242121", "phone": "+16042745897", "website_url": "http://Cineplex.com", "description": "20 Theatres, Cineplex Vancouver is ...", "thumb_img_url": "http://xomo.ca/img64_64.jpg", "large_img_url": "http://xomo.ca/img320_420.jpg", "category": "Cinema" }
	]
}
*/

require_once 'common.php';

/** class stubs for unserializing **/

class Mappress_Map {}
class Mappress_Poi {}

// get locations
$locationsql = "SELECT * FROM wp_mappress_maps";
$locationset = mysql_query($locationsql, $db);

$locationreturn = array();

$names = array();
$ids   = array();
while ($locationrow = mysql_fetch_assoc($locationset)) {
	// get images attached to locations
	$obj = new stdClass();
	$obj->id       = $locationrow['mapid'];
	
	$stuff = unserialize($locationrow['obj']);
	$obj->name     = $stuff->pois[0]->title;
	$obj->street   = $stuff->pois[0]->address;
	$obj->city     = 'Toronto';
	$obj->country  = 'CA';
	$obj->province = 'ON';
	
    $obj->lat      = $stuff->pois[0]->point['lat'];
    $obj->lng      = $stuff->pois[0]->point['lng'];
    if (! in_array($obj->name, $names) && $obj->name != null ) {
	    $locationreturn[] = $obj; 
        $names[]          = $obj->name;
        $parentId         = $obj->id;
    } else {
        // ignore it, track it
        if ($parentId != null) { 
            $ids[$obj->id]    = $parentId;
        }
    }
}

$return = array('venues' => $locationreturn, 'duplicate_mapping' => $ids);
// spit out json

echo json_encode($return);