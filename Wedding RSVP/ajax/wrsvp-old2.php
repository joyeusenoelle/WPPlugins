<?

include_once("classes/output_classes.php");
require_once("functions.php");

$options = get_option('wrsvp_opts');	// Options from the settings page
$wo = new MUDiv("wrsvp-main");			// Main display div
$wm = new MUMaster();					// Output manipulation object

if($_POST) {

	if($_POST['pullData'] || $_POST['gotData']) {
		// We're on screen 2.
		/*************************** SCREEN 2 ***************************\
		|*	Screen 2 is the group editor, seen when users RSVP "yes".	*|
		|* 	It should do the following things:							*|
		|*		IF POST['pullData'] IS SET:								*|
		|*		* Pull * from wrsvp_guests where the group ID matches	*|
		|*		IF POST['gotData'] IS SET:								*|
		|*		* Assemble data from gotData to mimic data pull from 	*|
		|*			pullData instructions								*|
		|*			* Data in the format key1^value1|key2^value2|...	*|
		|*		EITHER WAY:												*|
		|*		* Pull maxguests from group ID in wrsvp_groups 			*|
		|*		* Return a row of inputs for each guest:				*|
		|*			* Given/first name (wrsvp_guests.givenname)(text)	*|
		|*			* Family/last name (wrsvp_guests.familyname)(text)	*|
		|*			* Meal selection (wrsvp_guests.meal)(numeric)		*|
		|*			* Dietary restrictions (wrsvp_guests.dietary)(text) *|
		|*	It needs the following information:							*|
		|*		EITHER:													*|
		|*		* Group ID: POST['pullData'] from Screen 1				*|
		|*		OR:														*|
		|*		* Existing info: POST['gotData'] from Screen 3			*|
		\****************************************************************/

		if($_POST['pullData']) {
			// We're coming from screen 1.
			// Pull * from wrsvp_guests where the group ID matches
			$post = $_POST['pullData'];	// $post should be the group ID
		} else {
			// We're coming from screen 3.
			// Assemble data from gotData to mimic data pull from pullData instructions	
			$post = $_POST['gotData']; // $post should be data in the format key1^value1|key2^value2|...
		}
			
	} elseif($_POST['confData']) {
		// We're on screen 3.
		$post = $_POST['confData'];
	/*************************** SCREEN 3 ***************************\
	|*	Screen 3 is the confirmation screen, shown after users have *|
	|*	selected their choices but before they are committed to DB.	*|
	|*	It should do the following things:							*|
	|*		* Assemble the data from POST['confData']				*|
	|*			* Data in the format key1^value1|key2^value2|...	*|
	|*		* Display the data from POST['confData']				*|
	|*		* Display two buttons:									*|
	|*			* Confirm: "This is correct"						*|
	|*				* Pass all data to POST->putData				*|
	|*				* Data in the format key1^value1|key2^...		*|
	|*			* Reject: "I need to edit something"				*|
	|*				* Pass all data to POST->gotData				*|
	|*				* Data in the format key1^value1|key2...		*|
	|*	It needs the following information:							*|
	|*		* Existing info: POST['confData'] from Screen 2			*|
	|*		* Meal selections: options['wrsvp_meals']				*|
	\****************************************************************/
		
	} elseif($_POST['putData']) {
		// We're on screen 4.
	/*************************** SCREEN 4 ***************************\
	|*	Screen 4 is the final screen, shown after users have 		*|
	|*	confirmed that the data they entered is correct.			*|
	|*	It should do the following things:							*|
	|*		* Assemble the data from POST['putData']				*|
	|*			* Data in the format key1^value1|key2^value2|...	*|
	|*		* Update the wrsvp_guests table with all data			*|
	|*		* Display a "thank you" message							*|
	|*	It needs the following information:							*|
	|*		* Existing info: POST['putData']						*|
	|*			* Data in the format key1^value1|key2^value2|...	*|
	|*		* Thank you message: options['wrsvp_thankyou']			*|
	\****************************************************************/
	
		$message = $options['wrsvp_thankyou'];
		$post = $_POST['putData'];
		$items = array();
		$users = split("@",$post);
		foreach($users as $user) {
			$infos = split("|",$user);
			foreach($infos as $info) {
				$bits = split("^",$info);
				
			}
		}
		$postbits = split("|",$post);
		$items = array();
		$i = 0;
		foreach($postbits as $bit) {
			$bitbits = split("^",$bit);
			$items[$i]['key'] = $bitbits[0];
			$items[$i]['value'] = $bitbits[1];
			$i++;
		}
		
				
	} else {
		// We're on screen 5.
	/*************************** SCREEN 5 ***************************\
	|*	Screen 5 is the screen users get when they RSVP "no".		*|
	|*	It should do the following things:							*|
	|*		* Update the wrsvp_groups table to set attending to 0	*|
	|*		* Display a regret message, pulled from plugin options	*|
	|*	It needs the following information:							*|
	|*		* Regret message: options['wrsvp_regret']				*|
	|*		* Group ID: POST['noData']								*|
	\****************************************************************/
	
		$message = $options['wrsvp_regret'];
		$group = $_POST['noData'];
		$sql = "UPDATE wrsvp_groups SET attending=0 where id={$group}";
		$res = $wpdb->query($sql);
		if($res === FALSE) { $message = "We were unable to store your RSVP. We apologize for the inconvenience. Please contact the hosts by email."; }
		
		$woh = new MUHdr("2","wrsvp-regret");
		$woh->append($message);
		$wo->append($woh);
		echo $wo;

	}
		
		/************************ SETUP *************************\
		|*	* Get welcome message from options					*|
		|*	* Get givenname, familyname, group 					*|
		|*		from wrsvp_guests								*|
		|*	* Put names/groups into Javascript array			*|
		\********************************************************/

}

?>