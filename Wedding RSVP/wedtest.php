<?php
		$post = "{\"groupinfo\":[{\"id\":\"1\",\"gname\":\"Bob\",\"fname\":\"Peterson\",\"meal\":\"1\",\"diet\":\"No shellfish\"},{\"id\":\"2\",\"gname\":\"Lisa\",\"fname\":\"Peterson\",\"meal\":\"2\",\"diet\":\"\"}],\"groupmax\":\"7\"}";
		$mpost = preg_replace("/\{[^\[]*\[/","",$post);		// Strips out '{"groupinfo":['
		$mpost = preg_replace("/\][^\}]*\}/","",$mpost);	// Strips out '],"groupmax":"*"}'
		$pguests = explode("},{",$mpost); 	// pguests is now an array of '{"id":"1","gname":"Bob","fname":"Peterson","meal":"2","diet":"No shellfish"}'
		//print_r($pguests);
		echo "<br>";
		$guests = array();
		for($i=0;$i<sizeof($pguests);$i++) {
			$pguest = trim($pguests[$i],"{}");
			$pguest = preg_replace("/\"/","",$pguest);
			$pguesta = explode(",",$pguest);	// pguesta is now an array of 'id:1','gname:Bob','fname:Peterson','meal:2','diet:No shellfish'
			for($j=0;$j<sizeof($pguesta);$j++) {
				list($key,$value) = explode(":",$pguesta[$j]);
				$guests[$i][$key] = $value;
			}
		}
		// Now we should have a multidimensional array called $guests with [n]["id"],[n]["gname"],[n]["fname"],[n]["meal"],[n]["diet"]
		//print_r($guests);
		echo "Guest 0's given name is " . $guests[0]['gname'];
		echo "<br>";
		echo "Guest 1's meal is " . $guests[1]['meal'];
?>