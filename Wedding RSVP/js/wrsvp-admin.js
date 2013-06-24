var $j = jQuery.noConflict();
var styleObj = new Object();
var tooLong;
var postIDs = {};
$j.valHooks.textarea = {
	get: function( elem ) {
		return elem.value.replace( /\r?\n/g, "\r\n" );
	}
};

function Desanitize(input) {
	input = decodeURIComponent(input);
	input = input.replace(/&#58/g,":");
	input = input.replace(/&#59/g,";");
	input = input.replace(/&#60em&#62/gi,"<em>");
	input = input.replace(/&#60\/em&#62/gi,"</em>");
	input = input.replace(/&#60strong&#62/gi,"<strong>");
	input = input.replace(/&#60\/strong&#62/gi,"</strong>");
	input = input.replace(/&#60b&#62/gi,"<b>");
	input = input.replace(/&#60\/b&#62/gi,"</b>");
	input = input.replace(/&#60i&#62/gi,"<i>");
	input = input.replace(/&#60\/i&#62/gi,"</i>");
	input = input.replace(/&#60u&#62/gi,"<u>");
	input = input.replace(/&#60\/u&#62/gi,"</u>");
	input = input.replace(/&#60br&#62/gi,"<br>");
	input = input.replace(/&#60/g,"&lt;");
	input = input.replace(/&#62/g,"&gt;");
	input = input.replace(/%20/g," ");
	input = input.replace(/\\'/g,"'");
	input = input.replace(/\\"/g,'"');
	return input;
}
function AJAXSavePost(data) {
	$j('.admin_image_submitbutton').removeAttr('disabled');
	if(data) {
		//alert(data);
		var output = JSON.parse(data);
		if(output.error) {
			prompt("This text can be copied in case of a recurring error. You can click OK to continue.","In the post editor of the KA-FrontPage plugin, in the AJAXSaveFile function, an error was generated:" + output.error);
			$j('#admin_info_text').html("");
			$j('#admin_info').hide();
		} else if(output.success) {
			$j('#admin_spin').hide();
			$j('#admin_info_text').html("File saved successfully.");
			$j('#admin_info').delay(2600).fadeOut(400);
			GetAllPosts();
		} else {
			prompt("This text can be copied in case of a recurring error. You can click OK to continue.","In the post editor of the KA-FrontPage plugin, in the AJAXSaveFile function, the output did not have any of the expected values.");
			$j('#admin_info_text').html("");
			$j('#admin_info').hide();
		}
	} else {
		prompt("This text can be copied in case of a recurring error. You can click OK to continue.","In the post editor of the KA-FrontPage plugin, in the AJAXSaveFile function, no output was returned.");
		$j('#admin_info_text').html("");
		$j('#admin_info').hide();
	}
}
function AJAXReadPost(data) {
	$j('.admin_image_submitbutton').removeAttr('disabled');
	if(data) {
		//alert(data);
		var output = JSON.parse(data);
		if(output.error) {
			prompt("This text can be copied in case of a recurring error. You can click OK to continue.","In the post editor of the KA-FrontPage plugin, in the AJAXReadFile function, an error was generated: " + output.error);
			$j('#admin_info_text').html("");
			$j('#admin_info').hide();
		} else if(output.content) {
			var fcont = Desanitize(output.content);
			var fimg = output.image;
			$j('#admin_textbox').val(fcont);
			if(fimg == "") {
				$j('#admin_image_current').html("").hide();
			} else {
				$j('#admin_image_current').html("<strong>Current image:</strong> <a href=\"" + kasiteurl + "/wp-content/plugins/ka-frontpage/images/" + fimg + "\">" + fimg + "</a>").show();
				$j('#admin_postimage').val(fimg);
			}
			$j('#admin_spin').hide();
			$j('#admin_info_text').html("File loaded successfully.");
			$j('#admin_info').delay(2600).fadeOut(400);
		} else {
			prompt("This text can be copied in case of a recurring error. You can click OK to continue.","In the post editor of the KA-FrontPage plugin, in the AJAXReadPost function, the output did not have any of the expected values.");
			$j('#admin_info_text').html("");
			$j('#admin_info').hide();
		}
	} else {
		prompt("This text can be copied in case of a recurring error. You can click OK to continue.","In the post editor of the KA-FrontPage plugin, in the AJAXReadPost function, no output was returned.");
		$j('#admin_info_text').html("");
		$j('#admin_info').hide();
	}		
}
function AJAXGetAllPosts(data) {
	$j('.admin_image_submitbutton').removeAttr('disabled');
	if(data) {
		//alert(data);
		var output = JSON.parse(data);
		if(output.error) {
			prompt("This text can be copied in case of a recurring error. You can click OK to continue.","In the post editor of the KA-FrontPage plugin, in the AJAXGetAllPosts function, an error was generated: " + output.error);
			$j('#admin_info_text').html("");
			$j('#admin_info').hide();
		} else if(output.posts) {
			$j('#admin_selpost').html("<option value=\"new\">-- New post --</option>");
			var curid = $j('#admin_postid').val();
			for(i=0;i<output.posts.length;i++) {
				var curpost = output.posts[i].title;
				var curdate = output.posts[i].date;
				var appendStr = "<option value=\"" + curpost + "\"";
				if(output.posts[i].id == curid){
					appendStr = appendStr + " SELECTED";
				}
				appendStr = appendStr + ">" + curpost + " (" + curdate + ")</option>\n";
				
				$j('#admin_selpost').append(appendStr);
				postIDs[curpost] = output.posts[i].id;
			}
			$j('#admin_spin').hide();
			$j('#admin_info_text').html("Posts loaded successfully.");
			$j('#admin_info').delay(2600).fadeOut(400);
		} else {
			prompt("This text can be copied in case of a recurring error. You can click OK to continue.","In the post editor of the KA-FrontPage plugin, in the AJAXGetAllPosts function, the output did not have any of the expected values.");
			$j('#admin_info_text').html("");
			$j('#admin_info').hide();
		}
	} else {
		prompt("This text can be copied in case of a recurring error. You canclick OK to continue.","In the post editor of the KA-FrontPage plugin, in the AJAXGetAllPosts function, no output was returned.");
		$j('#admin_info_text').html("");
		$j('#admin_info').hide();
	}
}
function SavePost() {
	if($j('#admin_postname').val() == "") {
		alert("Post title cannot be empty.");
	} else if ($j('#admin_textbox').val() == "") {
		alert("Post body cannot be empty.");
	} else {
		var postid = $j('#admin_postid').val();
		var postname = $j('#admin_posttitle').val();
		var contents = $j('#admin_textbox').val();
		var image = $j('#admin_postimage').val();
		$j('#admin_info_text').html("Saving " + postname + " ...");
		$j('#admin_spin').show();
		$j('#admin_info').show();
		$j('.admin_image_submitbutton').attr('disabled','disabled');
		var ajaxstr = "putPost=" + postid + "&postTitle=" + postname + "&postContents=" + contents + "&postImage=" + image;
		$j.post(kaajaxurl,ajaxstr,function(data){AJAXSavePost(data);});
	}
	return false;
}
function GetPost() {
	var goahead = confirm("This will discard all unsaved changes to the current text.\nClick OK to discard, Cancel to continue editing.");
	if(goahead == false) return false;
	var filename = $j('#admin_selpost').val();
	if(filename == "new") {
		$j('#admin_posttitle').val("");
		$j('#admin_textbox').val("");
		$j('#admin_postid').val("0");
		$j('#admin_postimage').val("");
		$j('#admin_image_current').html("")
	} else {
		$j('#admin_posttitle').val(filename);
		var curid = postIDs[filename];
		$j('#admin_postid').val(curid);
		var ajaxstr = "readPost=" + curid;
		$j('#admin_info_text').html("Retrieving " + filename + " ...");
		$j('#admin_spin').show();
		$j('#admin_info').show();
		$j('.admin_image_submitbutton').attr('disabled','disabled');
		var control = $j('#admin_image_form_bigfile');
		control.replaceWith(control=control.clone(true));

		$j.post(kaajaxurl,ajaxstr,function(data){AJAXReadPost(data);});
	}
	return false;
}
function GetAllPosts() {
	$j('#admin_info_text').html("Retrieving posts...");
	$j('#admin_spin').show();
	$j('#admin_info').show();
	$j('.admin_image_submitbutton').attr('disabled','disabled');
	$j.post(kaajaxurl,"getPosts=true",function(data){AJAXGetAllPosts(data);});
}

var formdata = false;
var intvl;
var inti = 0;
var intq = 0;
$j(document).ready(function(){
	if(window.FormData) {
		formdata = new FormData();
	} else {
		$j("#admin_image").html("Your browser does not support features that this plugin needs.");
	}
	document.getElementById('admin_image_form_submit').addEventListener("click",function(evt) {
		//alert("Received the click.");
		//$j("#mu-postbox-top").html("Received the click.");
		var bigfile = document.getElementById('admin_image_form_bigfile');
		if(document.getElementById('admin_image_form_bigfile').files[0]) {
			file = document.getElementById('admin_image_form_bigfile').files[0];
			if(window.FileReader) {
				reader = new FileReader();
				reader.readAsDataURL(file);
			}	
			if(formdata) {
				formdata.append("file[]",file);
				formdata.append("siteurl",kasiteurl);
				formdata.append("directory",directory);
				intvl = setInterval(function(){UploadText();},1000);
				$j("#admin_info_text").html("This may take a while. Uploading...").show();
				$j('.admin_image_submitbutton').attr('disabled','disabled');

				$j.ajax({
					url: kaimgurl,
					type: "POST",
					data: formdata,
					processData: false,
					contentType: false,
					success: function(data){
						clearInterval(intvl);
						var control = $j('#admin_image_form_bigfile');
						control.replaceWith(control=control.clone(true));
						$j('.admin_image_submitbutton').removeAttr('disabled');
						$j("#admin_info_text").html("");
						if(data) {
							//alert("MassiveUpload reports that it has received the following data:\n"+data);
							outobj = JSON.parse(data);
							//alert("MassiveUpload reports that it has parsed the data.");
							if(outobj.error) {
								prompt("This text can be copied in case of a recurring error. You can click OK to continue.","In the image uploader of the KA-FrontPage plugin, in the submit button event listener, an error was generated:" + outobj.error);
							} else if(outobj.success) {
								//alert("MassiveUpload reports success!\n"+outobj.success);
								$j("#admin_info_text").html("Success!");
								$j('#admin_image_current').html("<strong>Current image:</strong> <a href=\"" + kasiteurl + "/wp-content/plugins/ka-frontpage/images/" + outobj.filename + "\">" + outobj.filename + "</a>").show();
								$j('#admin_postimage').val(outobj.filename);
								SavePost();
							} else {
								prompt("This text can be copied in case of a recurring error. You can click OK to continue.","In the image uploader of the KA-FrontPage plugin, in the submit button event listener, the output did not have any of the expected values.");
							}
						} else {
							prompt("This text can be copied in case of a recurring error. You can click OK to continue.","In the image uploader of the KA-FrontPage plugin, in the submit button event listener, no data object was returned.");

						}
					}
				});
				//alert(".ajax didn't have any errors.");
			}
		} else {
			alert("You must select a file first.");
		}
		if(evt.preventDefault) {
			evt.preventDefault();
		}
		evt.returnValue = false;
		return false;
	}, false);
});


function UploadText(){
	var ultext = "This may take a while. Uploading...";
	ultext = ultext + " <img src=\"" + spinner + "\" style=\"vertical-align: middle;\">\n";
	$j("#admin_info_text").html(ultext); 
	$j("#admin_info").show();
}	    

$j(document).ready(function(){/*alert("Hello world");*/GetAllPosts();});
