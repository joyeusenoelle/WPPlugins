<?php 
require_once("header.php");
require_once("functions.php");

if($_POST['putGuest']) {
	$gid = $_POST['id'];
	$gname = $_POST['givenname'];
	$fname = $_POST['familyname'];
	$meal = $_POST['meal'];
	$diet = $_POST['diet'];
	$group = $_POST['group'];
	$sql = "UPDATE wrsvp_guests SET givenname='{$gname}',familyname='{$fname}',meal={$meal},dietary='{$diet}',grp={$group} WHERE id='{$gid}'";
	$res = $wpdb->query($sql);
	if($res === FALSE) {
		echo "{\"error\":\"Could not update table.\"}";
	} else {
		echo "{\"success\":\"Table successfully updated.\"}";
	}
} elseif($_POST['addGuest']) {
	$gname = $_POST['givenname'];
	$fname = $_POST['familyname'];
	$meal = $_POST['meal'];
	$diet = $_POST['diet'];
	$group = $_POST['group'];	
	$res = $wpdb->insert('wrsvp_guests',array('givenname'=>$gname,'familyname'=>$fname,'meal'=>$meal,'dietary'=>$diet,'grp'=>$group));
	if($res === FALSE) {
		echo "{\"error\":\"Could not insert new user.\"}";
	} else {
		$gid = $wpdb->insert_id;
		echo "{\"success\":\"{$gid}\"}";
	}
} elseif($_POST['putGroup']) {
	$gid = $_POST['id'];
	$att = $_POST['attending'];
	$maxg = $_POST['maxguests'];
	$sql = "UPDATE wrsvp_groups SET id='{$gid}',attending='{$att}',maxguests={$maxg} WHERE id='{$gid}'";
	$res = $wpdb->query($sql);
	if($res === FALSE) {
		echo "{\"error\":\"Could not update table.\"}";
	} else {
		echo "{\"success\":\"Table successfully updated.\"}";
	}
} elseif($_POST['addGroup']) {
	$att = $_POST['attending'];
	$maxg = $_POST['maxguests'];
	$res = $wpdb->insert('wrsvp_groups',array('attending'=>$att,'maxguests'=>$maxg));
	if($res === FALSE) {
		echo "{\"error\":\"Could not insert new group.\"}";
	} else {
		$gid = $wpdb->insert_id;
		echo "{\"success\":\"{$gid}\"}";
	}
}
?>