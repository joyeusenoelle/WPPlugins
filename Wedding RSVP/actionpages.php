<?php

require_once("classes/output_classes.php");

function WRSVP_page_body() {
	echo "<script type=\"text/javascript\">
	var wajaxurl = \"" . plugins_url("ajax/wrsvp.php",__FILE__) . "\";
	var wsiteurl = \"" . site_url() . "\";
	var spinner = \"" . admin_url( 'images/wpspin_light.gif' ) . "\";
	var msgwelcome = \"" . $options['wrsvp_welcome'] . "\";
	var msgregret = \"" . $options['wrsvp_regret'] . "\";
	var msgconfirm = \"" . $options['wrsvp_confirm'] . "\";
	var contact = \"" . $options['contact'] . "\";
	var meals = \"" . $options['meals'] . "\";
</script>";

	echo "<script type=\"text/javascript\" src=\"" . plugins_url("js/wrsvp.js",__FILE__) . "\"></script>";
	$wm = new MUMaster();
	$wo = new MUDiv("wrsvp-overall");
	$options = get_option('wrsvp_opts');
	$wo->append($wm->nonce("div","","wrsvp-info"));
	$wo->append($wm->nonce("div","","wrsvp-main"));
	//$wo->append($wm->nonce("div","","wrsvp-buttons"));
	echo $wo;
}


?>