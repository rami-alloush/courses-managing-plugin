<?php
/*
Plugin Name: Courses Managing Plugin (CMP)
Plugin URI: https://github.com/MrNoComment/courses-managing-plugin
Description: Full integrated system to manage and organise courses data and information (Depend on Zend Framework) 
Version: 3.8.4
Author: Eng. Rami Alloush
Author URI: mailto:rami.m.alloush@gmail.com
License: GPLv2

******************************************************************************

Issues to be addressed:
	dbDelta not executed on plugin activation
	Options Initialization: in no expiration entered, days to show expiration
	
******************************************************************************

Change Log:
3.8.4:	Work on updater info
3.8.3:	Work on updater
3.8.2:	Attendees Custom Fields 
3.8.1:	Fix attendees coding
3.8 :	Fix taxonomy for instructors to use registered 'category' from courses
3.7 :	Finalize Attendees and Instructors 
3.6 :	Add Certificate SN support and company data to attendee 
3.5 :	Complete compatibility with CPT system (Google Calendar Disabled)
3.4 :	Admin Search custom fields capability 
3.3 :	Menus and pages primary update
3.2 :	Make it for training only
3.1 :	Depend on ACF  
3.0 :   Complete restructure and Post Types implementation 
2.4 :   Make dependable plug-in
2.3 :	Coding system edited for Consultants, "Edit Course" Section Edits
2.2 :	Letterhead edits and Minor CBD printing changes
2.1	:	Print Course EDIT to PDF using TCPDF
2.0 :	OUT OF BETA ,, Warnings System (Courses, Users Codes and Certificates Expiration)
1.9 :	Google Calendar Fully Operating (Add, Edit, Remove Course)
1.8 : 	Reporting Query added (not functioning yet)
1.5	:	Course Edit and basic Evaluation charts added
1.0 :	Basic Functions of Users Codes and Courses Codes

*******************************************************************************/
			
if(!class_exists('cmp_plugin'))
{
    class cmp_plugin
    {
        /* Construct the plugin object */
        public function __construct()
        {
			// register actions
			add_action('admin_init', array(&$this, 'admin_init'));
			add_action('admin_menu', array(&$this, 'manage_menu'));
            
			require_once(sprintf("%s/post-types/course-post-type.php", dirname(__FILE__)));
			require_once(sprintf("%s/post-types/instructor-post-type.php", dirname(__FILE__)));
			require_once(sprintf("%s/post-types/attendee-post-type.php", dirname(__FILE__)));
			$CoursePostTypeTemplate 		= new CoursePostTypeTemplate();		  	//register the taxonomy
			$InstructorPostTypeTemplate 	= new InstructorPostTypeTemplate();		// Add existing taxonomy to instructor
			$AttendeePostTypeTemplate 		= new AttendeePostTypeTemplate();
			
			
        } // END public function __construct

		/* hook into WP's admin_init action hook */
		public function admin_init()
		{
			// Set up the settings for this plugin
			$this->init_settings();
			// var_dump(get_plugin_data( __FILE__ ));
			require_once(sprintf("%s/includes/BFIGitHubPluginUploader.php", dirname(__FILE__)));
			// if ( is_admin() ) {
				new BFIGitHubPluginUpdater( __FILE__, 'MrNoComment', "courses-managing-plugin" );
			// }			
			// Possibly do additional admin_init tasks
		} // END public static function activate
		
		/* add a menu */     
		public function manage_menu()
		{
			add_options_page('CMP', 'CMP Settings', 'edsc_training_coordinator', 'cmp_plugin_settings', array(&$this, 'cmp_plugin_settings_page'));
			add_menu_page( 'CMP Home', 'CMP Dashboard', 'edsc_training_coordinator', 'cmp_training_landing', array(&$this, 'cmp_plugin_landing_page'),'',5);
			add_submenu_page('cmp_training_landing','Home','Home','edsc_training_coordinator','cmp_training_landing',array(&$this, 'cmp_plugin_landing_page')); //Rename Duplication
			add_submenu_page('cmp_training_landing', 'Run a New Course','Run a Course', 'edsc_training_coordinator','cmp_run_course', array(&$this, 'cmp_run_course_page') );
			add_submenu_page('cmp_training_landing','Manage Courses','Manage Courses','edsc_training_coordinator','cmp_manage_courses', array(&$this, 'cmp_manage_courses_page'));			
			add_submenu_page('cmp_training_landing', 'Letter Head','Letter Head', 'edsc_training_coordinator','cmp_letter_head', array(&$this, 'cmp_letter_head_page'));
			add_submenu_page('cmp_training_landing', 'Reporting','Reporting', 'edsc_training_coordinator','cmp_reporting', array(&$this, 'cmp_reporting_page'));
			
			//Remove Menus
			global $menu;
			// check if admin and hide these for admins
			if( (current_user_can('administrator')) ) {
				$restricted = array(
					// __('Dashboard'),
					// __('Posts'),
					// __('Media'),
					// __('Links'),
					// __('Pages'),
					// __('Appearance'),
					// __('Tools'),
					// __('Users'),
					// __('Settings'),
					// __('Comments'),
					// __('Plugins')
				);
			}
			// hide these for other roles
			else {
				$restricted = array(
					__('Dashboard'),
					__('Posts'),
					__('Media'),
					__('Links'),
					__('Pages'),
					__('Appearance'),
					__('Tools'),
					__('Users'),
					__('Settings'),
					__('Comments'),
					__('Plugins')
				);
			}
			end ($menu);
			while (prev($menu)){
				$value = explode(' ',$menu[key($menu)][0]);
				if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
			}
		}// END public function manage_menu()
		
		/* Menu Callback */     
		public function cmp_plugin_settings_page()
		{
			if(!current_user_can('manage_options'))
			{
				wp_die(__('You do not have sufficient permissions to access this page. OR PAGE DOES NOT EXIST'));
			}

			// Render the settings template
			include(sprintf("%s/templates/settings.php", dirname(__FILE__)));
		} // END public function cmp_plugin_settings_page()
		
		// Landing Callback 
		public function cmp_plugin_landing_page()
		{
			if(!current_user_can('manage_options'))
			{ wp_die(__('You do not have sufficient permissions to access this page. OR PAGE DOES NOT EXIST!')); }

			// Render the landing template
				include(sprintf("%s/templates/landing_page.php", dirname(__FILE__)));
		} // END public function cmp_plugin_landing_page()
		
		// Run a Course Callback 
		public function cmp_run_course_page()
		{
			if(!current_user_can('manage_options'))
			{ wp_die(__('You do not have sufficient permissions to access this page. OR PAGE DOES NOT EXIST!')); }

			// Render the landing template
				include(sprintf("%s/templates/run_course.inc.php", dirname(__FILE__)));
		} // END public function cmp_run_course_page()
		
		// Letter Head Callback 
		public function cmp_letter_head_page()
		{
			if(!current_user_can('manage_options'))
			{ wp_die(__('You do not have sufficient permissions to access this page. OR PAGE DOES NOT EXIST!')); }

			// Render the landing template
				include(sprintf("%s/templates/landing_page.php", dirname(__FILE__)));
		} // END public function cmp_letter_head_page()
		
		// Reporting Callback 
		public function cmp_reporting_page()
		{
			if(!current_user_can('manage_options'))
			{ wp_die(__('You do not have sufficient permissions to access this page. OR PAGE DOES NOT EXIST!')); }

			// Render the landing template
				include(sprintf("%s/templates/landing_page.php", dirname(__FILE__)));
		} // END public function cmp_reporting_page()
		
		// Manage Courses Callback 
		public function cmp_manage_courses_page()
		{
			if(!current_user_can('manage_options'))
			{
				wp_die(__('You do not have sufficient permissions to access this page. OR PAGE DOES NOT EXIST!'));
			}

			// Render the landing template
				include(sprintf("%s/templates/manage_courses.php", dirname(__FILE__)));
			
		} // END public function cmp_manage_courses_page()
		
		/* Initialize some custom settings */     
		public function init_settings()
		{
			// register the settings for this plugin
			register_setting('cmp_plugin-group', 'setting_a');
			register_setting('cmp_plugin-group', 'setting_b');
		} // END public function init_custom_settings()
		
		/* Activate the plugin  */
        public static function activate()
        {
			global $wpdb;
			$tablename = $wpdb->prefix . "courses";
			$sql = "CREATE TABLE $tablename (
			course_id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
			created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			course_cat TEXT,
			course_code VARCHAR(15),
			course_name TEXT,
			inst_code TEXT,
			inst_name TEXT,
			location TEXT,
			course_start DATE,
			course_end DATE,
			course_days TINYINT,
			status INT,
			part_code TEXT,
			part_paid TEXT,
			eval TEXT,
			task_cost TEXT,
			task_done TEXT,
			gcal_link TEXT,
			part_eval TEXT
			)";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
            // Do nothing
        } // END public static function activate
		
        /* Deactivate the plugin */     
        public static function deactivate()
        {
            // Do nothing
        } // END public static function deactivate
		
    } // END class cmp_plugin
} // END if(!class_exists('cmp_plugin'))

//Execute the plugin if its class is set
if(class_exists('cmp_plugin'))
{
    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('cmp_plugin', 'activate'));
    register_deactivation_hook(__FILE__, array('cmp_plugin', 'deactivate'));

    // instantiate the plugin class
    $cmp_plugin = new cmp_plugin();
}

####################################
## Extra Functions
####################################

if(isset($cmp_plugin))
{
    // Add the settings link to the plugins page
    function plugin_settings_link($links)
    {
        $settings_link = '<a href="options-general.php?page=cmp_plugin_settings">Settings</a>'; 
        array_unshift($links, $settings_link);
        return $links; 
    }

    $plugin = plugin_basename(__FILE__); 
    add_filter("plugin_action_links_$plugin", 'plugin_settings_link');
	
	function appthemes_check_user_role( $role, $user_id = null )
	{
 
		if ( is_numeric( $user_id ) )
		$user = get_userdata( $user_id );
		else
			$user = wp_get_current_user();
	 
		if ( empty( $user ) )
		return false;
	 
		return in_array( $role, (array) $user->roles );
	}

	function get_countries_list() {
		include(sprintf("%s/templates/countries.inc.php", dirname(__FILE__)));
	}
	
	// ########################################################################
	// Search custom fields from admin keyword searches

	if (isset($_GET['s'])) {
		add_filter('posts_join', 'cmp_cpt_search_join' );
		function cmp_cpt_search_join ($join){
			global $pagenow, $wpdb;
			// I want the filter only when performing a search on edit page of Custom Post Type named "course"
			if ( is_admin() && $pagenow=='edit.php' && $_GET['s'] != '') {    
				$join .='LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
			}
			return $join;
		}
		
		add_filter( 'posts_where', 'cmp_cpt_search_where' );
		function cmp_cpt_search_where( $where ){
			global $pagenow, $wpdb;
			// I want the filter only when performing a search on edit page of Custom Post Type named "course"
			if ( is_admin() && $pagenow=='edit.php' && $_GET['s'] != '') {
				$where = preg_replace(
			   "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
			   "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
			}
			return $where;
		}
			
		add_filter( 'posts_groupby', 'my_post_limits' );
		function my_post_limits($groupby) {
			global $pagenow, $wpdb;
			if ( is_admin() && $pagenow == 'edit.php' && $_GET['s'] != '' ) {
				$groupby = "$wpdb->posts.ID";
			}
			return $groupby;
		}
	}
	
	// ########################################################################
	//Load Main Scripts
	
	function cmp_load_scripts() {
		wp_enqueue_style('jquery.ui.theme', 'http://code.jquery.com/ui/1.11.2/themes/flick/jquery-ui.css');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-datepicker' );
		wp_enqueue_script('jquery-ui-autocomplete' );
		wp_enqueue_script('cmp_global_script', plugins_url( 'courses-managing-plugin/js/global.js' ), array('jquery'), '1.1.0');
		wp_enqueue_script('cmp_localization_script', plugins_url( 'courses-managing-plugin/js/add-participant.js' ), array('jquery'), '1.0.0');
		wp_localize_script('cmp_localization_script', 'cmp_localized_vars', localize_vars()); //Send PHP array to "my_script" java file
	}

	add_action('wp_enqueue_scripts', 'cmp_load_scripts');
	add_action('admin_enqueue_scripts', 'cmp_load_scripts');

	function localize_vars() {
		if(function_exists('cmp_return_attendees') && function_exists('cmp_return_companys')) {
			return array(
				'SendCompanysData' => cmp_return_companys(), //array('r','ra','rami | dsdwe')
				'SendAttendeesData' => cmp_return_attendees(), //array('r','ra','rami | dsdwe')
				);
		}
	} //End localize_vars
	
	function cmp_autocomplete_script() {
		echo '<script>
			(function( $ ) {
			  "use strict";
				$(function() {
					$( "#part_code_source" ).autocomplete({
						source: cmp_localized_vars.SendAttendeesData,
						change: function (event, ui) {
								if(!ui.item){
									$(event.target).val("");
								}
							}, 
						});
					$( "#part_comp_source" ).autocomplete({source: cmp_localized_vars.SendCompanysData,
						change: function (event, ui) {
								if(!ui.item){
									$(event.target).val("");
								}
							}, });
					$(".clickableRow").click( function() {
						window.document.location = $(this).attr("href");
						}).hover( function() {
							$(this).toggleClass("ui-state-hover");
							$(this).css("cursor","pointer");
						});
				});
			}(jQuery));
			</script>';
	}
	
	// Returns table of participants for run courses HTML
	if(!function_exists('edsdb_return_run_courses_part')) {
		function edsdb_return_run_courses_part($current_course) {
			$participants = explode(",", $current_course['part_code']);
			$paid = explode(",", $current_course['part_paid']);
			$part_cmop = explode(",", $current_course['part_cmop']);
			$part_cert = explode(",", $current_course['part_cert']);
			
			//Start table construction
				echo '<div class="wrap"><div class="wrap">
						<table id="participants" class="widefat">
						<h4>Manage Registered Attendees</h4><hr>
						<thead><tr>
						<th>Participant Code</th><th>Amount Paid (EGP)</th><th>Company</th><th>Certificate S.N.</th><th>Delete Participant</th>
						</tr></thead><tbody>'; // Table Header !-->
			
			if (!empty($participants[0])) { //Ensuring participants registration
				foreach ($participants as $key => $participant) {//Key: index
					echo'<tr id="singleparticipant'.$key.'">
							<td><input type="text" value="'.$participant.'" id="part_code[]" name="part_code[]"/></td>
							<td><input type="text" value="'.$paid[$key].'" id="part_paid'.$key.'" name="part_paid[]"/></td>
							<td><input type="text" value="'.$part_comp[$key].'" id="part_comp'.$key.'" name="part_comp[]"/></td>
							<td><input type="text" value="'.$part_cert[$key].'" id="part_cert'.$key.'" name="part_cert[]"/></td>
							<td><input type="submit" value="x" id="'.$key.'" onclick="RemoveParticipant('.$key.')"/></td></tr>';
				}
			} else if (isset($_GET['id'])) { //to ensure we are not on the Add Course page
			echo '<tr><td>There are no users registered on this course yet!</td><td><td></td></td></tr></tbody></table>';
			}
		}
	}
	
	if(!function_exists('cmp_course_attendees_html')) {
		function cmp_course_attendees_html($current_course) {
			echo '<div class="wrap">
					<table class="form-table"><tr valign="top">
						<h4>Add Course Attendees</h4><hr>
						<tr>
							<td>TYPE Participant Name (or code)<br/>
								<input id="part_code_source" name="part_code_source" size="30"></td>
							<td>Paid (EGP)<br/>
								<input type="text" id="part_paid_source" name="part_paid_source" value="0" size="9" maxlength="9"
								onfocus="clearMe(this)" onBlur="NoEmpty(this.id)" onkeypress="validate(event)"/></td>
							<td>Company<br/>
								<input type="text" id="part_comp_source" name="part_comp_source" value="" onfocus="clearMe(this)"/></td>
							<td>Certificate SN<br/>
								<input type="text" id="part_cert_source" name="part_cert_source" value="0" onfocus="clearMe(this)"/></td>
								
							<td><input type="button" onclick="AddParticipant()" name="addparticipant" id="addparticipant" value="Add Participant"></td>
						</tr><tr></tr>';

			if(function_exists('edsdb_return_run_courses_part')) {
				edsdb_return_run_courses_part($current_course); }
			echo '</table></div>';
		}
	}
	
	// Returns option HTML for ALL Companys
	if(!function_exists('cmp_return_companys')) {
		function cmp_return_companys() {
			if ( is_multisite() ) {
				switch_to_blog(1); //switch to main site
				$companys = get_posts( array( 'post_type' => 'company', 'post_status' => 'publish','posts_per_page'   => -1, ) );
				foreach ( $companys as $key => $company ) {
					$company_name		= get_the_title( $company );
					$company_code  		= get_field('product_code', $company);
					$companys_list[$key]	= $company_code .' | '. $company_name;
				}
				restore_current_blog();
				return $companys_list;		
			}
		}
	}
}