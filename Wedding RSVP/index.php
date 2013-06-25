<?php

/*
 Plugin Name: Wedding RSVP
 Plugin URI: http://www.mischievess.com/
 Description: Manage wedding RSVPs and allow guests to confirm attendance and meals
 Author: Noëlle D. Anthony
 Version: 0.1
 Author URI: http://www.noelleanthony.com/
*/
include_once("classes/output_classes.php");
include_once("adminpages.php");
include_once("actionpages.php");
include_once("functions.php");
$WRSVP_db_version = "1.0.0";

function WRSVP_activate() {
	global $wpdb;
	global $WRSVP_db_version;
	$sql = "CREATE TABLE wrsvp_guests (
		id INT(11) NOT NULL AUTO_INCREMENT,
		givenname TEXT,
		familyname TEXT,
		meal INT(3),
		dietary TEXT,
		grp INT(11),
		UNIQUE KEY id (id)
	);";
	$sql2 = "CREATE TABLE wrsvp_groups (
		id INT(11) NOT NULL AUTO_INCREMENT,
		attending INT(2),
		maxguests INT(3),
		UNIQUE KEY id (id)
	);";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	dbDelta($sql2);
	add_option('wrsvp_db_version',$WRSVP_db_version);
}


if (is_admin()) {
	add_action('admin_init', 'WRSVP_init');
	add_action('admin_menu', 'WRSVP_addPage');
}
add_shortcode( 'wrsvp_page', 'WRSVP_page_body' );
register_activation_hook(__FILE__,'WRSVP_activate');

?>