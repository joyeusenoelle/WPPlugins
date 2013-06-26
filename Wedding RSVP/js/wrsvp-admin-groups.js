$wj = jQuery.noConflict();

function AJAXAddGroup(data) {
	if(data) {
		//alert(data);
		var output = JSON.parse(data);
		if(output.error) {
			prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXAddNewGroup function, an error was returned: " + output.error);
		} else if(output.success) {
			//alert(output.success);
			var att = $wj('#att-input-new').val();
			var maxg = $wj('#max-input-new').val();
			var newuser = "";
			newuser += "<tr id='" + output.success + "' class='edit_tr'><td class='edit_td'><span id='gid_" + output.success + "' class='text'>" + output.success + "</span><input type='text' value='" + output.success + "' class='editbox' id='gid_input_" + output.success + "' /></td><td class='edit_td'><span id='att_" + output.success + "' class='text'>" + att + "</span><input type='text' value='" + att + "' class='editbox' id='att_input_" + output.success + "' /></td><<td class='edit_td'><span id='max_" + output.success + "' class='text'>" + maxg + "</span><input type='text' value='" + maxg + "' class='editbox' id='max_input_" + output.success + "' /></td></tr>";
			$wj('#existing-groups').append(newuser);
			$wj('#att-input-new').val("");
			$wj('#max-input-new').val("");
		} else {
			prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXAddGroup function, no recognizable output was returned: " + output);			
		}
	} else {
		prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXAddGroup function, no data was returned.");
	}
}

function addGroup() {
	var att = $wj('#att-input-new').val();
	var maxg = $wj('#max-input-new').val();
	var dataString = 'addGroup=true&attending=' + att + '&maxguests=' + maxg;
	$wj.ajax({
		type: "POST",
		url: wajaxurl,
		data: dataString,
		cache: false,
		success: function(data) {
			AJAXAddGroup(data);
		}
	});
}

$wj(document).ready(function() {
	$wj(".edit_tr").click(function() {
		var ID=$wj(this).attr('id');
		$wj("#gid_"+ID).hide();
		$wj("#att_"+ID).hide();
		$wj("#max_"+ID).hide();
		$wj("#gid_input_"+ID).show();
		$wj("#att_input_"+ID).show();
		$wj("#max_input_"+ID).show();
	}).change(function() {
		var ID=$wj(this).attr('id');
		var att=$wj("#att_input_"+ID).val();
		var maxg=$wj("#max_input_"+ID).val();
		var dataString = 'putGroup=true&id='+ ID +'&attending='+att+'&maxgroup='+maxg;
		//alert(dataString);
		//$wj("#first_"+ID).html('<img src="load.gif" />'); // Loading image

		$wj.ajax({
			type: "POST",
			url: wajaxurl,
			data: dataString,
			cache: false,
			success: function(html){
				$wj("#gid_"+ID).html(ID);
				$wj("#att_"+ID).html(att);
				$wj("#max_"+ID).html(maxg);
			}
		});

	});

	// Edit input box click action
	$wj(".editbox").mouseup(function() {
		return false;
	});

	// Outside click action
	$wj(document).mouseup(function() {
		$wj(".editbox").hide();
		$wj(".text").show();
	});

});