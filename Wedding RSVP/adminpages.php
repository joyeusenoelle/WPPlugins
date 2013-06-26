<?php

function WRSVP_init() {
	register_setting('wrsvp_options','wrsvp_options','WRSVP_validate');
	add_settings_section('WRSVP_settings_gen','General Settings', 'WRSVP_settings_gen','WRSVP_settings');
	add_settings_field('WRSVP_setting_welcome','Welcome message','WRSVP_setting_welcome','WRSVP_settings','WRSVP_settings_gen');
	add_settings_field('WRSVP_setting_regret','Regret message','WRSVP_setting_regret','WRSVP_settings','WRSVP_settings_gen');
	add_settings_field('WRSVP_setting_confirm','Confirmation message','WRSVP_setting_confirm','WRSVP_settings','WRSVP_settings_gen');
	add_settings_field('WRSVP_setting_contact','Contact address','WRSVP_setting_contact','WRSVP_settings','WRSVP_settings_gen');
	add_settings_field('WRSVP_setting_meals','Meal options','WRSVP_setting_meals','WRSVP_settings','WRSVP_settings_gen');
}

function WRSVP_validate($input) {
	return $input;	
}

function WRSVP_settings_gen() {
//This function deliberately left blank	
}

function WRSVP_setting_welcome() {
	$options = get_option('wrsvp_options');	
	echo "<input type=\"text\" size=\"40\" name=\"wrsvp_options[wrsvp_welcome]\" value=\"{$options['wrsvp_welcome']}\"><br><span style=\"font-size: 90%; color: grey;\">This is the welcome message displayed to guests when they first visit the RSVP page.</span>";
}

function WRSVP_setting_regret() {
	$options = get_option('wrsvp_options');	
	echo "<input type=\"text\" size=\"40\" name=\"wrsvp_options[wrsvp_regret]\" value=\"{$options['wrsvp_regret']}\"><br><span style=\"font-size: 90%; color: grey;\">This is the message displayed to guests when they indicate that they can't attend.</span>";
}

function WRSVP_setting_confirm() {
	$options = get_option('wrsvp_options');	
	echo "<input type=\"text\" size=\"40\" name=\"wrsvp_options[wrsvp_confirm]\" value=\"{$options['wrsvp_confirm']}\"><br><span style=\"font-size: 90%; color: grey;\">This is the message displayed to guests when they confirm their attendance.</span>";
}

function WRSVP_setting_contact() {
	$options = get_option('wrsvp_options');	
	echo "<input type=\"text\" size=\"40\" name=\"wrsvp_options[wrsvp_contact]\" value=\"{$options['wrsvp_contact']}\"><br><span style=\"font-size: 90%; color: grey;\">This is your contact email address. <strong>This will only be displayed in error messages.</strong></span>";
}

function WRSVP_setting_meals() {
	$options = get_option('wrsvp_options');	
	echo "<input type=\"text\" size=\"40\" name=\"wrsvp_options[wrsvp_meals]\" value=\"{$options['wrsvp_meals']}\"><br><span style=\"font-size: 90%; color: grey;\">This is a list of meal selections. Separate individual meal options with a semicolon (;).<br>Example: <tt>Potato-breaded tilapia;Barbecued chicken;Sizzling steak;House salad</tt></span>";
}


function WRSVP_addPage() {
	add_menu_page('Wedding RSVP', 'Wedding RSVP Options', 'manage_options', 'wrsvp_options', 'WRSVP_doOpts');
	add_submenu_page('wrsvp_options','Wedding RSVP - Add/Edit Guests', 'Add/Edit Guests', 'manage_options', 'wrsvp_guest', 'WRSVP_doOptsGuests');
	add_submenu_page('wrsvp_options', 'Wedding RSVP - Add/Edit Groups', 'Add/Edit Groups', 'manage_options', 'wrsvp_group', 'WRSVP_doOptsGroups');
}

function WRSVP_doOpts() {
?>
	<div class="wrap">
    <h2>Wedding RSVP Management</h2>
	<h3>To finish installing Wedding RSVP:</h3>
    <ol><li>Create a new empty page in WordPress.</li>
    <li> Make sure the pages is set to a WIDE template (without a sidebar).</li>
    <li> Enter the HTML Editor (NOT the Visual Editor).</li>
    <li> In the page editor, paste this: <tt>[wrsvp_page]</tt></li>
    <li> Publish the page.</li>
    </ol>
    <br>
    That's it! Your front page should now be installed.<br>
    <br>
    <form action="options.php" method="post">
	<?php 
	settings_fields('wrsvp_options');
	$options = get_option('wrsvp_options');
	do_settings_sections('WRSVP_settings');
	?>
   	<p><input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
	</form></p>
	<?php
}

function WRSVP_doOptsGuests() {
	global $wpdb;
	$options = get_option('wrsvp_options');
	$meals = explode(";",$options['wrsvp_meals']);
	$sql = "SELECT * FROM wrsvp_guests ORDER BY grp,familyname ASC";
	$guests = $wpdb->get_results($sql);
?>
	<div class="wrap">	
    <h2>Wedding RSVP - Add/Edit Guests</h2>
   	<script type="text/javascript">
		var wajaxurl = "<?php echo plugins_url("ajax/wrsvp-admin.php",__FILE__); ?>";
		var wsiteurl = "<?php echo site_url(); ?>";
		var spinner = "<?php echo admin_url( 'images/wpspin_light.gif' ); ?>";
		var contact = "<?php echo $options['wrsvp_contact']; ?>";
		var meals = "<?php echo $options['wrsvp_meals']; ?>";
	</script>

    <script type="text/javascript" src="<?php echo plugins_url('js/wrsvp-admin.js',__FILE__); ?>"></script>
    <style type="text/css">
		tbody#existing-users input,tbody#existing-users select { display: none; }
		tr:nth-child(even) { background-color: #eeeeee; }
		td { padding-left: 5px; border-left: 1px black solid; }
		td:nth-child(first) { border: none;}
	</style>
    <h3>Add new user</h3>
    
    <table border=0 cellpadding="5" cellspacing="2"><tbody>
    <tr>
    	<th>First/Given Name</th>
        <th>Last/Family Name</th>
        <th>Meal</th>
        <th>Diet Restriction</th>
        <th>Group</th>
        <th></th>
    </tr>
	<tr>
    	<td>
        	<input type="text" value="" id="gname-input-new" />
        </td>
    	<td>
        	<input type="text" value="" id="fname-input-new" />
        </td>
    	<td>
        	<select id="meal-input-new">
            <option value="-1">No meal (use as default)</option>
            <?php for($i=0;$i<sizeof($meals);$i++){ ?>
				<option value="<?php echo $i; ?>"><?php echo $meals[$i]; ?></option>
			<?php } ?>
            </select>
        </td>
    	<td>
        	<input type="text" value="" id="diet-input-new" />
        </td>
    	<td>
        	<input type="text" value="" id="group-input-new" />
        </td>
        <td>
        	<input type="submit" value="Add" onClick="addGuest();" />
        </td>
    </tr>   
    </tbody>
    </table> 
    <h3>Existing users</h3>
    
    <table><tbody id='existing-users'>
    <tr>
    	<th>First/Given Name</th>
        <th>Last/Family Name</th>
        <th>Meal</th>
        <th>Diet Restriction</th>
        <th>Group</th>
    </tr>
    <?php
	foreach($guests as $guest) {
		$cm = $guest->meal;
		$gname = str_replace("\'","'",$guest->givenname);
		$fname = str_replace("\'","'",$guest->familyname);
		$diet = str_replace("\'","'",$guest->dietary);
	?>
    <tr id="<?php echo $guest->id; ?>" class="edit_tr">
    	<td class="edit_td">
        	<span id="gname_<?php echo $guest->id; ?>" class="text"><?php echo $gname; ?></span>
            <input type="text" value="<?php echo $gname; ?>" class="editbox" id="gname_input_<?php echo $guest->id; ?>" />
        </td>
    	<td class="edit_td">
        	<span id="fname_<?php echo $guest->id; ?>" class="text"><?php echo $fname; ?></span>
            <input type="text" value="<?php echo $fname; ?>" class="editbox" id="fname_input_<?php echo $guest->id; ?>" />
        </td>
    	<td class="edit_td">
        	<span id="meal_<?php echo $guest->id; ?>" class="text"><?php echo $meals[$cm]; ?></span>
            <select id="meal_input_<?php echo $guest->id; ?>" class="editbox">
            <option value="-1" <?php if($cm == -1) { echo "SELECTED"; } ?>>No meal</option>
            <?php
			for($n=0;$n<sizeof($meals);$n++) {
				?>
                <option value="<?php echo $n; ?>" <?php if($cm == $n) { echo "SELECTED"; } ?>><?php echo $meals[$n]; ?></option>
                <?php
			}
			?>
			</select>
        </td>
    	<td class="edit_td">
        	<span id="diet_<?php echo $guest->id; ?>" class="text"><?php echo $diet; ?></span>
            <input type="text" value="<?php echo $diet; ?>" class="editbox" id="diet_input_<?php echo $guest->id; ?>" />
        </td>
    	<td class="edit_td">
        	<span id="group_<?php echo $guest->id; ?>" class="text"><?php echo $guest->grp; ?></span>
            <input type="text" value="<?php echo $guest->grp; ?>" class="editbox" id="group_input_<?php echo $guest->id; ?>" />
        </td>
	</tr>
    <?php
	}
	?>
    </tbody>
    </table>
    <?php
}

function WRSVP_doOptsGroups() {
	global $wpdb;
	$options = get_option('wrsvp_options');
	$sql = "SELECT * FROM wrsvp_groups ORDER BY id ASC";
	$groups = $wpdb->get_results($sql);
	$j = sizeof($groups) - 1;
	$nextid = $groups[$j]->id;
	$nextid++;
?>
	<div class="wrap">	
    <h2>Wedding RSVP - Add/Edit Groups</h2>
   	<script type="text/javascript">
		var wajaxurl = "<?php echo plugins_url("ajax/wrsvp-admin.php",__FILE__); ?>";
		var wsiteurl = "<?php echo site_url(); ?>";
		var spinner = "<?php echo admin_url( 'images/wpspin_light.gif' ); ?>";
		var contact = "<?php echo $options['wrsvp_contact']; ?>";
	</script>

    <script type="text/javascript" src="<?php echo plugins_url('js/wrsvp-admin-groups.js',__FILE__); ?>"></script>
    <style type="text/css">
		tbody#existing-groups input,tbody#existing-groups select { display: none; }
		tr:nth-child(even) { background-color: #eeeeee; }
		td,th { padding: 2px 10px; }
		td:first-child,th:first-child { border: none;}
		
	</style>
    <h3>Add new group</h3>
    
    <table border=0><tbody>
    <tr>
    	<th>Group ID</th>
    	<th>Attending<br />(leave blank unless you know)</th>
        <th>Max Guests</th>
        <th></th>
    </tr>
	<tr>
    	<td><?php echo $nextid; ?></td>
    	<td>
        	<input type="text" value="" id="att-input-new" />
        </td>
    	<td>
        	<input type="text" value="" id="max-input-new" />
        </td>
        <td>
        	<input type="submit" value="Add" onClick="addGroup();" />
        </td>
    </tr>   
    </tbody>
    </table> 
    <h3>Existing groups</h3>
    
    <table><tbody id='existing-groups'>
    <tr>
    	<th>Group ID</th>
        <th>Attending<br />0 = No, 1 = Yes</th>
        <th>Max Guests</th>
    </tr>
    <?php
	foreach($groups as $group) {
	?>
    <tr id="<?php echo $group->id; ?>" class="edit_tr">
    	<td class="edit_td">
        	<span id="gid_<?php echo $group->id; ?>" class="text"><?php echo $group->id; ?></span>
            <input type="text" value="<?php echo $group->id; ?>" class="editbox" id="gname_input_<?php echo $group->id; ?>" />
        </td>
    	<td class="edit_td">
        	<span id="att_<?php echo $group->id; ?>" class="text"><?php echo $group->attending; ?></span>
            <input type="text" value="<?php echo $group->attending; ?>" class="editbox" id="att_input_<?php echo $group->id; ?>" />
        </td>
    	<td class="edit_td">
        	<span id="max_<?php echo $group->id; ?>" class="text"><?php echo $group->maxguests; ?></span>
            <input type="text" value="<?php echo $group->maxguests; ?>" class="editbox" id="max_input_<?php echo $group->id; ?>" />
        </td>
	</tr>
    <?php
	}
	?>
    </tbody>
    </table>
    <?php
}

?>