var $wj = jQuery.noConflict();
var formdata = false;
var maxrows = 0;
var armeals = meals.split(';');

/* Utility Functions */

function clearGuest(id) {
	var gname = "#wrsvp-gname-" + id;
	var fname = "#wrsvp-fname-" + id;
	var meal = "#wrsvp-meal-" + id;
	var diet = "#wrsvp-diet-" + id;
	$wj(gname).val("");
	$wj(fname).val("");
	$wj(meal).val("-1");
	$wj(diet).val("");
	return false;
}

function getMeals(id) {
	var mealsdiv = "#wrsvp-meals-" + id;
	var mealsref = "#wrsvp-curmeal-" + id;
	var tdoffset = $wj(mealsref).closest("td").offset();
	$wj(mealsdiv).css("position","absolute").offset(tdoffset).show();
	return false;
}

function setMeal(id,theval) {
	var mealsdiv = "#wrsvp-meals-" + id;
	var mealscur = "#wrsvp-curmeal-" + id;
	$wj(mealsdiv).hide();
	if(theval == -1) {
		$wj(mealsdiv).closest("td").children("a").html("No meal");
		$wj('#wrsvp-mealitem-' + i + '-x').attr("checked","checked");
	} else {
		$wj(mealsdiv).closest("td").children("a").html(armeals[theval]);
		$wj('#wrsvp-mealitem-'+i+'-'+theval).attr("checked","checked");
	}
	$wj(mealscur).val(theval);
}

/* AJAX Return Functions */

function AJAXGetNames(data) {
	//alert(data);
	if(data) {
		//alert(data);
		var output = JSON.parse(data);
		if(output.error) {
			prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXGetNames function, an error was returned: " + output.error);
		} else if(output.warning) {
			$wj('#wrsvp-namelist').html(output.warning);
		} else if(output.names) {
			$wj('#wrsvp-namelist').html("<strong>Select your name:</strong><br>");
			for(var i = 0; i < output.names.length; i++) {
				var gname = output.names[i].gname;
				var fname = output.names[i].fname;
				var nid = output.names[i].id;
				var grp = output.names[i].group;
				$wj('#wrsvp-namelist').append("<input type='radio' id='wrsvp-name-" + nid + "' name='wrsvp-name-radio' value='" + grp + "'> " + gname + " " + fname + "<br>");
			}
			$wj('#wrsvp-main').append("<div id='wrsvp-submit'><strong>If any member of your invited group will attend, please select Yes.<br>You will be able to let us know who is attending on the next page.</strong><br><input type='submit' id='wrsvp-rsvpyes' name='wrsvp-rsvpyes' value='Yes, at least one member of my group will attend.'><br><input type='submit' id='wrsvp-rsvpno' name='wrsvp-rsvpno' value='No, none of the members of my group can attend.'></div>");
			document.getElementById('wrsvp-rsvpyes').addEventListener("click",function(){var grp = $wj('input[name=wrsvp-name-radio]:checked').val(); ScreenTwo(grp);});
			document.getElementById('wrsvp-rsvpno').addEventListener("click",function(){var grp = $wj('input[name=wrsvp-name-radio]:checked').val(); ScreenFive(grp);});
		} else {
			prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXGetNames function, no recognizable output was returned: " + output);			
		}
	} else {
		prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXGetNames function, no data was returned.");
	}
}

function AJAXGetGroup(data) {
	if(data) {
		var output = JSON.parse(data);
		if(output.error) {
			prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXGetGroup function, an error was returned: " + output.error);
		} else if(output.groupinfo) {
			// groupinfo needs id, gname, fname, meal, diet
			maxrows = output.groupinfo.length;
			if(output.groupmax && output.groupmax > output.groupinfo.length) {
				maxrows = output.groupmax;
			}
			var mainbox = "";
			mainbox += "<div id='wrsvp-groupinfo'>";
			mainbox += "<input type='hidden' name='wrsvp-groupmax' id='wrsvp-groupmax' value='" + output.groupmax + "'>";
			mainbox += "<table cellspacing='5' cellpadding='3'><tr><th>First/given name</th><th>Last/family name</th><th>Meal selection</th><th>Dietary restrictions</th><th>Guest not attending</th></tr>";
			for(var i = 0; i < maxrows; i++) {
				if(output.groupinfo[i]) {
					var gid = output.groupinfo[i].id;
					var gname = output.groupinfo[i].gname;
					var fname = output.groupinfo[i].fname;
					var meal = output.groupinfo[i].meal;
					var diet = output.groupinfo[i].diet;
					var group = output.group;
				} else {
					var gid = "0";
					var gname = "";
					var fname = "";
					var meal = -1;
					var diet = "";
					var group = output.group;
				}
				mainbox += "<tr><td><input type='hidden' name='wrsvp-id-" + i + "' id='wrsvp-id-" + i + "' value='" + gid + "'><input type='hidden' name-'wrsvp-group-" + i + "' id='wrsvp-group-" + i + "' value='" + group + "'><input type='text' name='wrsvp-gname-" + i + "' id='wrsvp-gname-" + i + "' size='30' value='" + gname + "'></td><td><input type='text' name='wrsvp-fname-" + i + "' id='wrsvp-fname-" + i + "' size='30' value='" + fname + "'></td><td><input type='hidden' id='wrsvp-curmeal-" + i + "' value='" + meal + "'><a href='' onClick='return getMeals(" + i + ");'>Select meal</a><div class='wrsvp-meals' id='wrsvp-meals-" + i + "' style='display: none;'><p><strong>Select a meal option:</strong></p>";
				
				var mealbox = "";
				mealbox += "<input type='radio' value='-1' name='wrsvp-mealitem-" + i + "' id='wrsvp-mealitem-" + i + "-x'";
				if(meal == -1) {
					mealbox +=" CHECKED";
				}
				mealbox += " onClick='setMeal(" + i + ",-1);'> No meal<br>";
				for(var j=0;j<armeals.length;j++) {
					mealbox += "<input type='radio' value='" + j + "' name='wrsvp-mealitem" + i + "' id='wrsvp-mealitem-" + i + "-" + j + "'";
					if(meal == j) {
						mealbox += " CHECKED";
					}
					mealbox += " onClick='setMeal(" + i + "," + j + ");'> " + armeals[j] + "<br>";
				}
				
				mainbox += mealbox;
				mainbox += "</div></td><td><input type='text' name='wrsvp-diet-" + i + "' id='wrsvp-diet-" + i + "' size='30' value='" + diet + "'></td><td><input type='submit' name='wrsvp-clear-" + i + "' id='wrsvp-clear-" + i + "' value='Not attending' onClick='return clearGuest(" + i + ");'></td></tr>"; 
			}
			mainbox += "</table></div>";
			mainbox += "<div id='wrsvp-submit'><strong><br>If all of this information is correct, click the button below to continue.</strong><br><input type='submit' id='wrsvp-s2continue' name='wrsvp-s2continue' value='All of this information is correct.'></div>";
			$wj('#wrsvp-main').append(mainbox);
			document.getElementById('wrsvp-s2continue').addEventListener("click",function(){ScreenThree();});

		} else {
			prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXGetGroup function, no recognizable output was returned: " + output);			
		}
	} else {
		prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXGetGroup function, no data was returned.");
	}
}

function AJAXConfirmGroup(data) {
	if(data) {
		var output = JSON.parse(data);
		if(output.error) {
			prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXConfirmGroup function, an error was returned: " + output.error);
		} else if(output.groupinfo) {
			// groupinfo needs id, gname, fname, meal, diet
			var mainbox = "";
			mainbox += "<div id='wrsvp-groupinfo'>";
			mainbox += "<input type='hidden' name='wrsvp-passval' id='wrsvp-passval' value='" + data + "'>";
			mainbox += "<table cellspacing='5' cellpadding='3'><tr><th>First/given name</th><th>Last/family name</th><th>Meal selection</th><th>Dietary restrictions</th></tr>";
			for(var i = 0; i < output.groupinfo.length; i++) {
				var gid = output.groupinfo[i].id;
				var gname = output.groupinfo[i].gname;
				var fname = output.groupinfo[i].fname;
				var meal = output.groupinfo[i].meal;
				var diet = output.groupinfo[i].diet;
				var selmeal = "";
				if(gname != "" && fname != "") {
					if(meal == -1) {
						selmeal = "No meal";
					} else {
						selmeal = armeals[meal];
					}
					mainbox += "<tr><td>" + gname + "</td><td>" + fname + "</td><td>" + selmeal + "</td><td>" + diet + "</td></tr>"; 
				}
			}
			mainbox += "</table></div>";
			mainbox += "<div id='wrsvp-submit'><strong><br>Is all of this information correct?</strong><br><input type='submit' id='wrsvp-continue' name='wrsvp-continue' value='Yes, this is correct.'><br><input type='submit' id='wrsvp-goback' name='wrsvp-goback' value='No, I need to make changes.'></div>";
			$wj('#wrsvp-main').append(mainbox);
			document.getElementById('wrsvp-continue').addEventListener("click",function(){ScreenFour();});
			document.getElementById('wrsvp-goback').addEventListener("click",function(){ScreenTwoAgain();});
		} else {
			prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXConfirmGroup function, no recognizable output was returned: " + data);			
		}
	} else {
		prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXConfirmGroup function, no data was returned.");
	}
	
}

function AJAXPutGroup(data) {
	if(data) {
		//alert(data);
		var output = JSON.parse(data);
		if(output.error) {
			prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXPutGroup function, an error was returned: " + output.error);
		} else if(output.success) {
			//alert(output.success);
			// groupinfo needs id, gname, fname, meal, diet
			$wj('#wrsvp-info').html("<h2>" + msgconfirm + "</h2>"); // Disable for testing
			//$wj('#wrsvp-info').html("<h2>Data successfully stored!</h2>"); // Disable for live
			$wj('#wrsvp-main').html("");
		} else {
			prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXPutGroup function, no recognizable output was returned: " + output);			
		}
	} else {
		prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXPutGroup function, no data was returned.");
	}
	
}

function AJAXRegGroup(data) {
	if(data) {
		//alert(data);
		var output = JSON.parse(data);
		if(output.error) {
			prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXRegGroup function, an error was returned: " + output.error);
		} else if(output.success) {
			//alert(output.success);
			// groupinfo needs id, gname, fname, meal, diet
			//$wj('#wrsvp-info').html("<h2>" + msgconfirm + "</h2>"); // Disable for testing
			//$wj('#wrsvp-info').html("<h2>Data successfully stored!</h2>"); // Disable for live
			//$wj('#wrsvp-main').html("");
		} else {
			prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXRegGroup function, no recognizable output was returned: " + output);			
		}
	} else {
		prompt("Please copy the text in this text box and email it to " + contact + " .","There has been an error on " + wsiteurl + "! In the Wedding RSVP plugin, in the AJAXRegGroup function, no data was returned.");
	}
	
}

/* AJAX Retrieval Functions */

function getNames() {
	var fname = $wj('#wrsvp-fname').val();
	$wj('#wrsvp-namelist').html("");
	$wj('#wrsvp-submit').html("");
	var ajaxstr = "getNames=" + fname;
	//alert("Passing " + ajaxstr + " to " + wajaxurl);
	$wj.post(wajaxurl,ajaxstr,function(data){AJAXGetNames(data);});
}

function getGroup(group) {
	var ajaxstr = "getGroup=" + group;
	$wj.post(wajaxurl,ajaxstr,function(data){AJAXGetGroup(data);});	
}

function putGroup(passval) {
	var ajaxstr = "putGroup=" + passval;
	$wj.post(wajaxurl,ajaxstr,function(data){AJAXPutGroup(data);});
}

function regGroup(group) {
	var ajaxstr = "regGroup=" + group;
	$wj.post(wajaxurl,ajaxstr,function(data){AJAXRegGroup(data);});
}

/* Display Manipulation Functions */

function ScreenOne() {
	$wj('#wrsvp-info').html("<h2>" + msgwelcome + "</h2>");
	$wj('#wrsvp-main').html("<strong>Please type your last/family name:</strong><br> <input type='text' name='wrsvp-fname' id='wrsvp-fname' size='40'> <input type='submit' name='wrsvp-findname' id='wrsvp-findname' value='Find my name'><br><div id='wrsvp-namelist'></div>");
	document.getElementById('wrsvp-findname').addEventListener("click",getNames);
}

function ScreenTwo(group) {
	$wj('#wrsvp-info').html("<h2>Manage group membership</h2><br>If a member of your group cannot attend, click \"Not attending\" to remove them.");
	$wj('#wrsvp-main').html("");
	getGroup(group);
}

function ScreenThree() {
	var passvals = "";
	passvals += "{\"groupinfo\":[";
	for(var i=0;i<maxrows;i++) {
		var gid = "#wrsvp-id-" + i;
		var gname = "#wrsvp-gname-" + i;
		var fname = "#wrsvp-fname-" + i;
		var meal = "#wrsvp-curmeal-" + i;
		var diet = "#wrsvp-diet-" + i;
		var group = "#wrsvp-group-" + i;
		var temp = "";
		if(i>0) {
			temp += ",";
		}
		//temp += "{\"line\":\"" + i + "\"";
		temp += "{\"id\":\"" + $wj(gid).val() + "\"";
		temp += ",\"gname\":\"" + $wj(gname).val() + "\"";
		temp += ",\"fname\":\"" + $wj(fname).val() + "\"";
		temp += ",\"meal\":\"" + $wj(meal).val() + "\"";
		temp += ",\"diet\":\"" + $wj(diet).val() + "\"";
		temp += ",\"group\":\"" + $wj(group).val() + "\"";
		temp += "}";
		passvals += temp;
	}
	passvals += "],\"groupmax\":\"" + $wj('#wrsvp-groupmax').val() + "\"}";
	$wj('#wrsvp-info').html("<h2>Confirm group membership</h2><br>Please confirm that your group is correct. Then click \"Yes, this is correct\" to confirm your attendance, or \"I need to make changes\" to go back one screen.");
	$wj('#wrsvp-main').html("");
	AJAXConfirmGroup(passvals);
}

function ScreenFour() {
	var passval = $wj('#wrsvp-passval').val();
	//$wj('#wrsvp-info').html("");
	//$wj('#wrsvp-main').html("");
	putGroup(passval);
}

function ScreenFive(group) {
	$wj('#wrsvp-info').html("<h2>" + msgregret + "</h2>");
	$wj('#wrsvp-main').html("");
	regGroup(group);
}

function ScreenTwoAgain() {
	var passval = $wj('#wrsvp-passval').val();
	$wj('#wrsvp-info').html("<h2>Manage group membership</h2><br>If a member of your group cannot attend, click \"Not attending\" to remove them.");
	$wj('#wrsvp-main').html("");
	AJAXGetGroup(passval);
}

$wj(document).ready(function(){ScreenOne();});