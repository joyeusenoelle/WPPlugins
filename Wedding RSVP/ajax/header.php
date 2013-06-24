<?php 

require_once("functions.php");
$pathparts = pathinfo(__FILE__,PATHINFO_DIRNAME);
$pathbits = explode("/",$pathparts);
$i = 1;
$pathchunk = "";
while($pathbits[$i] != "wp-content") {
	$pathchunk .= "/" . $pathbits[$i];
	$i++;
}
define('ABSPATH',$pathchunk.'/');
if(file_exists($pathchunk.'/wp-config.php')) { require_once($pathchunk.'/wp-config.php'); }
else { require_once(dirname($pathchunk).'/wp-config.php'); }
require_once($pathchunk.'/wp-includes/wp-db.php');
require_once($pathchunk.'/wp-includes/post.php');
if(file_exists($pathchunk.'/wp-includes/option.php')) {
	require_once($pathchunk.'/wp-includes/option.php');
}
global $wpdb;
if(!isset($wpdb)) {
	die("{\"error\":\"wpdb isn't set, so this script can't do its job.\"}");
}

if(!$_POST) {
	die("{\"error\":\"You're not supposed to be here.\"}");
}

?>