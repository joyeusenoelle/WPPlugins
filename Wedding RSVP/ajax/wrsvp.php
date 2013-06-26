<?

require_once("header.php");
require_once("functions.php");

//$options = get_option('wrsvp_opts');	// Options from the settings page

if($_POST) {
	if(isset($_POST['getNames'])) {
		if($_POST['getNames'] == "") {
			die("{\"warning\":\"You must type your name in the input box.\"}");
		} else {
			$sql = "SELECT * FROM wrsvp_guests WHERE familyname LIKE '%" . $wpdb->escape($_POST['getNames']) . "%'";
			$results = $wpdb->get_results($sql);
			if($results === NULL) {
				die("{\"warning\":\"No names found matching " . $_POST['getNames'] . ".\"}");
			}
			$ret = "{\"names\":[";
			$i = 0;
			foreach($results as $result) {
				if($i == 0) { $i = 1; } else { $ret .= ","; }
				$ret .= "{";
				$ret .= "\"gname\":\"" . $result->givenname . "\",";
				$ret .= "\"fname\":\"" . $result->familyname . "\",";
				$ret .= "\"id\":\"" . $result->id . "\",";
				$ret .= "\"group\":\"" . $result->grp . "\"";
				$ret .= "}";
			}
			$ret .= "]}";
			echo $ret;
		}
	}elseif(isset($_POST['getGroup'])) {
		if($_POST['getGroup'] == "" || intval($_POST['getGroup'] == 0)) {
			die("{\"error\":\"No group submitted.\"}");
		}
		$group = intval($_POST['getGroup']);
		// We're on screen 2.
		/*************************** SCREEN 2 ***************************\
		|*	Screen 2 is the group editor, seen when users RSVP "yes".	*|
		|* 	This code should do the following things:					*|
		|*		* Pull * from wrsvp_guests where the group ID matches	*|
		|*		* Pull maxguests from group ID in wrsvp_groups 			*|
		|*		* Echo JSON-formatted text:								*|
		|*			* groupinfo is an array with one row for each guest	*|
		|*				* Given/first name (wrsvp_guests.givenname)		*|
		|*				* Family/last name (wrsvp_guests.familyname)	*|
		|*				* Meal selection (wrsvp_guests.meal)			*|
		|*				* Dietary restrictions (wrsvp_guests.dietary) 	*|
		|*			* groupmax is a value (wrsvp_groups.maxguests		*|
		|*	It needs the following information:							*|
		|*		* Group ID: POST['getGroup'] from Screen 1				*|
		\****************************************************************/
		
		$sqlgroup = $wpdb->escape("SELECT maxguests FROM wrsvp_groups WHERE id='{$group}'");
		$sqlguests = $wpdb->escape("SELECT * FROM wrsvp_guests WHERE grp='{$group}'");
		$maxguests = $wpdb->get_var($sqlgroup);
		$guests = $wpdb->get_results($sqlguests);
		$ret = "{";
		$ret .= "\"groupinfo\":[";
		$i = 0;
		foreach($guests as $guest) {
			if($i == 0) {
				$i++;
			} else {
				$ret .= ",";
			}
			$ret .= "{";
			$ret .= "\"id\":\"" . $guest->id . "\",";
			$ret .= "\"gname\":\"" . $guest->givenname . "\",";
			$ret .= "\"fname\":\"" . $guest->familyname . "\",";
			$ret .= "\"meal\":\"" . $guest->meal . "\",";
			$ret .= "\"diet\":\"" . $guest->dietary . "\"";
			$ret .= "}";
		}
		$ret .= "],\"group\":\"{$group}\",\"groupmax\":\"{$maxguests}\"";
		$ret .= "}";
		echo $ret;
			
	} elseif(isset($_POST['putGroup'])) {
		if($_POST['putGroup'] == "") {
			die("{\"error\":\"No updates submitted.\"}");
		}
		$post = $_POST['putGroup'];
		$post = str_replace("\\\"","\"",$post);
		// We're on screen 4.
		/*************************** SCREEN 4 ***************************\
		|*	Screen 4 is the final screen, shown after users have 		*|
		|*	confirmed that the data they entered is correct.			*|
		|*	It should do the following things:							*|
		|*		* Assemble the data from POST['putGroup']				*|
		|*			* Data in JSON format								*|
		|*		* Update the wrsvp_guests table with all data			*|
		|*	It needs the following information:							*|
		|*		* Existing info: POST['putGroup']						*|
		|*			* Data in JSON format								*|
		\****************************************************************/

		$mpost = preg_replace("/\{[^\[]*\[/","",$post);		// Strips out '{"groupinfo":['
		$mpost = preg_replace("/\][^\}]*\}/","",$mpost);	// Strips out '],"groupmax":"*"}'
		$pguests = explode("},{",$mpost); 	// pguests is now an array of '{"id":"1","gname":"Bob","fname":"Peterson","meal":"2","diet":"No shellfish"}'
											// except that all but the outermost braces have been stripped
		$guests = array();
		$results = array();
		$error = false;
		$group = "";
		for($i=0;$i<sizeof($pguests);$i++) {
			$pguest = trim($pguests[$i],"{}");
			$pguest = preg_replace("/\"/","",$pguest);
			$pguesta = explode(",",$pguest);	// pguesta is now an array of 'id:1','gname:Bob','fname:Peterson','meal:2','diet:No shellfish'
			for($j=0;$j<sizeof($pguesta);$j++) {
				list($key,$value) = explode(":",$pguesta[$j]);
				$guests[$i][$key] = $value;
				if($key == "group" && $group == "") {
					$group = $value;
				}
			}
			if($guests[$i]['id'] != 0) {
				$sql = "UPDATE wrsvp_guests SET givenname='" . $guests[$i]['gname'] . "', familyname='" . $guests[$i]['fname'] . "', meal='" . $guests[$i]['meal'] . "', dietary='" . $guests[$i]['diet'] . "' WHERE id='" . $guests[$i]['id'] . "'";
			} elseif($guests[$i]['fname'] != "" && $guests[$i]['gname'] != "") {
				$sql = "INSERT INTO wrsvp_guests (givenname,familyname,meal,dietary,grp) VALUES ('" . $guests[$i]['gname'] . "','" . $guests[$i]['fname'] . "','" . $guests[$i]['meal'] . "','" . $guests[$i]['diet'] . "','" . $guests[$i]['group'] . "')";
			}
			$sql = $wpdb->escape($sql);
			$results[$i] = $wpdb->query($sql);
			if($results[$i] === FALSE) {
				$error = true;
			}
		}
		$wpdb->query($wpdb->escape("UPDATE wrsvp_groups SET attending=1 WHERE id='{$group}'"));
		// Now we should have a multidimensional array called $guests with [n]["id"],[n]["gname"],[n]["fname"],[n]["meal"],[n]["diet"]
		// We should also have an array called $results with the number of rows affected for each query; it should be 1 or FALSE.
		// Not that we need to do anything with them, but they might be useful for further revisions.

		if($error === false) {
			echo "{\"success\":\"Data stored successfully.\"}";
		} else {
			echo "{\"error\":\"There was at least one error adding the data to the database.\"}";
		}
	} elseif(isset($_POST['regGroup'])) {
		$group = $wpdb->escape($_POST['regGroup']);
		$result = $wpdb->query("UPDATE wrsvp_groups SET attending=0 WHERE id='{$group}'");
		if($result === FALSE) {
			echo "{\"error\":\"Could not update database.\"}";
		} else {
			echo "{\"success\":\"Database successfully updated.\"}";	
		}
	}
}

?>