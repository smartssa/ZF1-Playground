<?php 
/**
 *  db stuff goes here.
 *  Welcome back, 1999. I love this coding style. (not really, but ity's fun.)
 */

date_default_timezone_set('America/New_York');

define('DB_NAME', 'wordpress');
define('DB_USER', 'wordpress');
define('DB_PASSWORD', 'wordpress');
define('DB_HOST', '127.0.0.1');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db(DB_NAME);

// set header: application/json

?>