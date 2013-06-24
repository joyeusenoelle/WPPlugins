<?php

function WRSVP_init() {
	register_setting('wrsvp_options','wrsvp','WRSVP_validate');
}

function WRSVP_validate($input) {
	return $input;	
}

function WRSVP_addPage() {
	add_options_page('Wedding RSVP', 'Wedding RSVP', 'manage_options', 'wrsvp_options', 'WRSVP_doPage');
}

function WRSVP_doPage() {
?>
	<div class="wrap">
    <h2>Wedding RSVP Management</h2>
	<?php 
	settings_fields('wrsvp_options');
	$options = get_option('wrsvp');
}


?>