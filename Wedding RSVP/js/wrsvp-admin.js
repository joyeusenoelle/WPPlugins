$wj = jQuery.noConflict();
var armeals = meals.split(";");

function AJAXAddGuest(data) {
	if(data) {
		//alert(data);
		var output = JSON.parse(data);
		if(output.error) {
			prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXAddNew function, an error was returned: " + output.error);
		} else if(output.success) {
			//alert(output.success);
			var gname = $wj('#gname-input-new').val();
			var fname = $wj('#fname-input-new').val();
			var meal = $wj('#meal-input-new').val();
			var diet = $wj('#diet-input-new').val();
			var group = $wj('#group-input-new').val();
			var newuser = "";
			newuser += "<tr id='" + output.success + "' class='edit_tr'><td class='edit_td'><span id='gname_" + output.success + "' class='text'>" + gname + "</span><input type='text' value='" + gname + "' class='editbox' id='gname_input_" + output.success + "' /></td><td class='edit_td'><span id='fname_" + output.success + "' class='text'>" + fname + "</span><input type='text' value='" + fname + "' class='editbox' id='fname_input_" + output.success + "' /></td><td class='edit_td'><span id='meal_" + output.success + "' class='text'>" + armeals[meal] + "</span><select id='meal_input_" + output.success + "' class='editbox'><option value='-1'";
			if(meal == -1) { newuser += " SELECTED"; } 
			newuser += ">No meal</option>";
			for(var n=0;n<armeals.length;n++) {
				newuser += "<option value='" + meals;
				if(meals == n) { newuser += " SELECTED"; }
				newuser += armeals[n] + "</option>";
			}
			newuser += "</select></td><td class='edit_td'><span id='diet_" + output.success + "' class='text'>" + diet + "</span><input type='text' value='" + diet + "' class='editbox' id='diet_input_" + output.success + "' /></td><td class='edit_td'><span id='group_" + output.success + "' class='text'>" + group + "</span><input type='text' value='" + group + "' class='editbox' id='group_input_" + output.success + "' /></td></tr>";
			$wj('#existing-users').append(newuser);
			$wj('#gname-input-new').val("");
			$wj('#fname-input-new').val("");
			$wj('#meal-input-new').val(-1);
			$wj('#diet-input-new').val("");
			$wj('#group-input-new').val("");
		} else {
			prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXAddNew function, no recognizable output was returned: " + output);			
		}
	} else {
		prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXAddNew function, no data was returned.");
	}
}

function addGuest() {
	var gname = $wj('#gname-input-new').val();
	var fname = $wj('#fname-input-new').val();
	var meal = $wj('#meal-input-new').val();
	var diet = $wj('#diet-input-new').val();
	var group = $wj('#group-input-new').val();
	var dataString = 'addGuest=true&givenname=' + gname + '&familyname=' + fname + '&meal=' + meal + '&diet=' + diet + '&group=' + group;
	$wj.ajax({
		type: "POST",
		url: wajaxurl,
		data: dataString,
		cache: false,
		success: function(data) {
			AJAXAddGuest(data);
		}
	});
}

$wj(document).ready(function() {
	$wj(".edit_tr").click(function() {
		var ID=$wj(this).attr('id');
		$wj("#gname_"+ID).hide();
		$wj("#fname_"+ID).hide();
		$wj("#meal_"+ID).hide();
		$wj("#diet_"+ID).hide();
		$wj("#group_"+ID).hide();
		$wj("#gname_input_"+ID).show();
		$wj("#fname_input_"+ID).show();
		$wj("#meal_input_"+ID).show();
		$wj("#diet_input_"+ID).show();
		$wj("#group_input_"+ID).show();
	}).change(function() {
		var ID=$wj(this).attr('id');
		var first=$wj("#gname_input_"+ID).val();
		var last=$wj("#fname_input_"+ID).val();
		var meal=$wj("#meal_input_"+ID).val();
		var diet=$wj("#diet_input_"+ID).val();
		var group=$wj("#group_input_"+ID).val();
		var dataString = 'putGuest=true&id='+ ID +'&givenname='+first+'&familyname='+last+'&meal='+meal+'&diet='+diet+'&group='+group;
		//alert(dataString);
		$wj("#first_"+ID).html('<img src="load.gif" />'); // Loading image

		$wj.ajax({
			type: "POST",
			url: wajaxurl,
			data: dataString,
			cache: false,
			success: function(html){
				$wj("#gname_"+ID).html(first);
				$wj("#fname_"+ID).html(last);
				$wj("#meal_"+ID).html(armeals[meal]);
				$wj("#diet_"+ID).html(diet);
				$wj("#group_"+ID).html(group);
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