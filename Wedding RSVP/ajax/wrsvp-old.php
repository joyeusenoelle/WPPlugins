<?php
require_once("header.php");

if($_POST['getPosts']) {
	// Needs to return 'error' OR
	// 'posts' >
	//		'id'
	// 		'title'
	//		'date'
	
	$query = "SELECT * FROM ka_fp_blog ORDER BY id ASC";
	$result = $wpdb->get_results($query);
		// Object! Should be:
		//   ->id - post ID
		//   ->post_date - post date/time
		//   ->title - post title
		//   ->body - post text
		//   ->image - post image
		// Iterate with foreach to get individual rows
	if($result) {
		$ret = "{\"posts\":[";
		$i = 0;
		foreach($result as $post) {
			if($i == 0) { $i = 1; } else { $ret .= ","; }
			$ret .= "{";
			$ret .= "\"id\":\"" . $post->id . "\",";
			$ret .= "\"date\":\"" . $post->post_date . "\",";
			$ret .= "\"title\":\"" . $post->title . "\",";
			$ret .= "\"image\":\"" . $post->image . "\"";
			$ret .= "}";
		}
		$ret .= "]}";
	} else {
		$ret = "{\"error\":\"Database did not return results.\"}";
	}
	echo $ret;
	
} elseif($_POST['readPost']) {
	// Needs to return 'error' OR 'content'
	if($_POST['readPost'] == "") {
		$ret = "{\"error\":\"No post ID submitted.\"}";
	} else {
		$query = "SELECT body, image FROM ka_fp_blog WHERE id = " . $_POST['readPost'];
		$result = $wpdb->get_row($query);
		if($result == $null) {
			$ret = "{\"error\":\"No post with that ID.\"}";
		} else {
			$ret = "{\"content\":\"" . rawurlencode($result->body) . "\",\"image\":\"" . $result->image . "\"}";
		}
	}
	echo $ret;
	
} elseif(isset($_POST['putPost'])) {
	// Needs to return 'error' OR 'success'
	if($_POST['putPost'] == "") {
		$ret = "{\"error\":\"No post ID submitted.\"}";
	} elseif($_POST['postTitle'] == "") {
		$ret = "{\"error\":\"Post must have a title.\"}";
	} elseif($_POST['postContents'] == "") {
		$ret = "{\"error\":\"Post must have a body.\"}";
	} else {
		$pid = intval($_POST['putPost']);
		$ptitle = $_POST['postTitle'];
		$pcontent = $_POST['postContents'];
		$pimage = $_POST['postImage'];
		$pdate = date("m/d/Y g:i A");
		if($pid == 0) {
			//$result = $wpdb->insert('ka_fp_blog',array('title'=>$ptitle,'post_date'=>$pdate,'body'=>$pcontent,'image'=>$pimage));
			$sql = "INSERT INTO ka_fp_blog (post_date,title,body,image) VALUES ('$pdate','$ptitle','$pcontent','$pimage')";
			$result = $wpdb->query($sql);
			if($result) {
				$ret = "{\"success\":\"Successfully inserted post into ka_fp_blog.\"}";
			} else {
				$ret = "{\"error\":\"Could not insert post into ka_fp_blog.\"}";
			}
		} else {
			//$result = $wpdb->update('ka_fp_blog',array('title'=>$ptitle,'body'=>$pcontent,'image'=>$pimage),array('id'=>$pid));
			$sql = "UPDATE ka_fp_blog SET title='$ptitle', body='$pcontent', image='$pimage' WHERE id='$pid'";
			$result = $wpdb->query($sql);
			if($result) {
				$ret = "{\"success\":\"Successfully updated post in ka_fp_blog.\"}";
			} else {
				$ret = "{\"error\":\"Could not update post in ka_fp_blog.\"}";
			}
		}
	}
	echo $ret;
} else {
	echo "{\"error\":\"No valid POST value submitted.\"}";	
}
?>