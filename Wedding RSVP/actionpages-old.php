<?php
function WRSVP_page_body() {
			$welcome = $options['wrsvp_welcome'];
		$sql = "SELECT givenname, familyname, group FROM wrsvp_guests";
		$guests = $wpdb->get_results($sql);
		// $guests is now an object with properties givenname, familyname, group
		// Iterate across $guests using foreach
		?>
		<script type="text/javascript">
		var $wj = jQuery.noConflict();	// Just in case there's another $j floating around
		<?php echo "var guests = ["; // Dreamweaver doesn't like it when I put this outside the PHP tags.
		$i = 0;
		foreach($guests as $guest) {
			// Make sure there's a comma before every object definition but the first
			if($i == 0) {
				$i++;
			} else {
				echo ",";
			}
			// Populate the object
			echo "{";
			echo "gname:\"" . $guest->givenname . "\",";
			echo "fname:\"" . $guest->familyname . "\",";
			echo "group:\"" . $guest->group . "\"";
			echo "}";
		}
		echo "];" // Dreamweaver doesn't like it when I put this outside the PHP tags.
		?>
		function getNames() {
			var curname = $wj('#wrsvp-input-name').val(); // The name they've typed in
			var cnrx = '/' + curname + '/i'; // Turn it into a regex for .match(), i means it's case-insensitive
			var fname = '';
			for(var i=0;i<guests.length;i++) { // Iterate through the guests array
				fname = guests[i].fname; 
				if(fname.match(cnrx) != null) { // "Smith" will match "Smith", "Smithson", "Hammersmith" (remember, case-insensitive
					$wj('#wrsvp-namelist').append("<input type=\"radio\" id=\"" + fname + i + "\" name=\"" + fname + "\" onClick=\"changeName(" + i + ");\"> " + guests[i].gname + " " + guests[i].fname + "<br>"); // Build the radio-button listing
				}
				fname = '';
			}
			$wj('#wrsvp-nameselect').show(); // Actually show the name-select div
		}
		function changeName(i) {
			// Set the hidden inputs to the selected guest's group number
			$wj('#wrsvp-input-yesgroup').val(guests[i].group); // input name = pullData
			$wj('#wrsvo-input-nogroup').val(guests[i].group); // input name = noData
		}
		</script>
        <?php
		
		/*********************** DISPLAY ************************\
		|*	* Display text box for family/last name				*|
		|*	* Display button: "Find my name"					*|
		|* 	* When button is clicked:							*|
		|*		* If no name entered: "Must enter your name."	*|
		|*		* Display list of matching names with 			*|
		|*			radio buttons								*|
		|*		* Display "Will your group attend?"				*|
		|*		* Display two buttons:							*|
		|*			* Yes, we will attend						*|
		|*			* No, we will not attend					*|
		|*			* If no name selected: "Must select a name"	*|
		\********************************************************/
		$wo->append("Please type your last/family name:");
		$wo->append($wm->nonceinput("text",array("name"=>"wrsvp-input-name","id"=>"wrsvp-input-name","size"=>"40","value"=>"")));
		$wo->append($wm->nonceinput("submit",array("name"=>"wrsvp-input-search","id"=>"wrsvp-input-search","value"=>"Find my name","onClick"=>"return getNames();")));
		$wos = new MUDiv("wrsvp-nameselect");
		$wol = new MUDiv("wrsvp-namelist");
		$wos->append($wol);
		$wos->append("If at least one member of your party will be attending, please select Yes.");
		$wos->appendbr();
		$wos->append("<form action=\"\" method=\"POST\">");
		$wos->append($wm->nonceinput("hidden",array("name"=>"pullData","id"=>"wrsvp-input-yesgroup","value"=>"")));
		$wos->append($wm->nonceinput("submit",array("name"=>"wrsvp-input-yes","id"=>"wrsvp-input-yes","value"=>"Yes, we will be attending")));
		$wos->append("</form><form action=\"\" method=\"POST\">");
		$wos->append($wm->nonceinput("hidden",array("name"=>"noData","id"=>"wrsvp-input-nogroup","value"=>"")));
		$wos->append($wm->nonceinput("submit",array("name"=>"wrsvp-input-no","id"=>"wrsvp-input-no","value"=>"No, we will not be attending")));
		$wos->append("</form>");
		$wo->append($wos);
		echo $wo;
}


		/*************************** SCREEN 1 ***************************\
		|*	Screen 1 is the welcome screen.								*|
		|*	It should do the following things:							*|
		|*		* Display a welcome message, pulled from plugin options	*|
		|*		* Select givenname, familyname, and group from the		*|
		|*			wrsvp_guests table									*|
		|*		* Display a text box in which the user can type their	*|
		|*			family/last name									*|
		|*		* Display a list of names from the database matching 	*|
		|*			the last name the user has typed in, with radio		*|
		|*			buttons to select one								*|
		|*		* Allow the user to select a name from the list			*|
		|*		* Display two buttons ("Will your group attend?"):		*|
		|*			* "Yes, we will attend"								*|
		|*				* Pass group ID to POST->pullData				*|
		|*			* "No, we will not attend"							*|
		|*				* Pass group ID to POST->noData					*|
		|*	It needs the following information:							*|
		|*		* Welcome message: options['wrsvp_welcome']				*|
		\****************************************************************/
?>