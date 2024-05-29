<?php

function em_install() {
	global $wp_rewrite, $em_do_not_finalize_upgrade;
	switch_to_locale(EM_ML::$wplang); //switch to blog language (if applicable)
   	$wp_rewrite->flush_rules();
	$old_version = get_option('dbem_version');
	//Won't upgrade <4.300 anymore
   	if( $old_version != '' && $old_version < 4.1 ){
		function em_update_required_notification(){
			global $EM_Booking;
			?><div class="error"><p><strong>Events Manager upgrade not complete, please upgrade to the version 4.300 or higher first from <a href="http://wordpress.org/extend/plugins/events-manager/download/">here</a> before upgrading to this version.</strong></p></div><?php
		}
		add_action ( 'admin_notices', 'em_update_required_notification' );
		return;
   	}
	if( version_compare(EM_VERSION, $old_version, '>') || $old_version == '' || (is_multisite() && !EM_MS_GLOBAL && get_option('em_ms_global_install')) ){
		if( get_option('dbem_upgrade_throttle') <= time() || !get_option('dbem_upgrade_throttle') ){
		 	// Creates the events table if necessary
		 	if( !EM_MS_GLOBAL || (EM_MS_GLOBAL && is_main_site()) ){
				em_create_events_table();
				em_create_events_meta_table();
				em_create_locations_table();
			  	em_create_bookings_table();
			    em_create_bookings_meta_table();
				em_create_tickets_table();
				em_create_tickets_bookings_table();
			    em_create_tickets_bookings_meta_table();
		 		delete_option('em_ms_global_install'); //in case for some reason the user changed global settings
			    add_action('em_ml_init', 'EM_ML::toggle_languages_index');
		 	}else{
		 		update_option('em_ms_global_install',1); //in case for some reason the user changes global settings in the future
		 	}	
			//New install, or Migrate?
			if( empty($old_version) ){
				em_create_events_page();
				update_option('dbem_hello_to_user',1);
			}			
			//set caps and options
			em_set_capabilities();
			em_add_options();
			em_upgrade_current_installation();
			if( empty($em_do_not_finalize_upgrade) ){
				do_action('events_manager_updated', $old_version );
				//Update Version
			    update_option('dbem_version', EM_VERSION);
				delete_option('dbem_upgrade_throttle');
				delete_option('dbem_upgrade_throttle_time');
				//last but not least, flush the toilet
				global $wp_rewrite;
				$wp_rewrite->flush_rules();
				update_option('dbem_flush_needed',1);
			}
		}else{
			function em_upgrading_in_progress_notification(){
				global $EM_Booking;
				?><div class="error"><p>Events Manager upgrade still in progress. Please be patient, this message should disappear once the upgrade is complete.</p></div><?php
			}
			add_action ( 'admin_notices', 'em_upgrading_in_progress_notification' );
			add_action ( 'network_admin_notices', 'em_upgrading_in_progress_notification' );
			return;
		}
	}
	restore_previous_locale(); //now that we're done, switch back to current language (if applicable)
}

/**
 * Magic function that takes a table name and cleans all non-unique keys not present in the $clean_keys array. if no array is supplied, all but the primary key is removed.
 * @param string $table_name
 * @param array $clean_keys
 */
function em_sort_out_table_nu_keys($table_name, $clean_keys = array()){
	global $wpdb;
	//sort out the keys
	$new_keys = $clean_keys;
	$table_key_changes = array();
	$table_keys = $wpdb->get_results("SHOW KEYS FROM $table_name WHERE Key_name != 'PRIMARY'", ARRAY_A);
	foreach($table_keys as $table_key_row){
		if( !in_array($table_key_row['Key_name'], $clean_keys) ){
			$table_key_changes[] = "ALTER TABLE $table_name DROP INDEX ".$table_key_row['Key_name'];
		}elseif( in_array($table_key_row['Key_name'], $clean_keys) ){
			foreach($clean_keys as $key => $clean_key){
				if($table_key_row['Key_name'] == $clean_key){
					unset($new_keys[$key]);
				}
			}
		}
	}
	//delete duplicates
	foreach($table_key_changes as $sql){
		$wpdb->query($sql);
	}
	//add new keys
	foreach($new_keys as $key){
		if( preg_match('/\(/', $key) ){
			$wpdb->query("ALTER TABLE $table_name ADD INDEX $key");
		}else{
			$wpdb->query("ALTER TABLE $table_name ADD INDEX ($key)");
		}
	}
}

/**
 * Since WP 4.2 tables are created with utf8mb4 collation. This creates problems when storing content in previous utf8 tables such as when using emojis. 
 * This function checks whether the table in WP was changed 
 * @return boolean
 */
function em_check_utf8mb4_tables(){
		global $wpdb, $em_check_utf8mb4_tables;
		
		if( $em_check_utf8mb4_tables || $em_check_utf8mb4_tables === false ) return $em_check_utf8mb4_tables;
		
		$column = $wpdb->get_row( "SHOW FULL COLUMNS FROM {$wpdb->posts} WHERE Field='post_content';" );
		if ( ! $column ) {
			return false;
		}
		
		//if this doesn't become true further down, that means we couldn't find a correctly converted utf8mb4 posts table 
		$em_check_utf8mb4_tables = false;
		
		if ( $column->Collation ) {
			list( $charset ) = explode( '_', $column->Collation );
			$em_check_utf8mb4_tables = ( 'utf8mb4' === strtolower( $charset ) );
		}
		return $em_check_utf8mb4_tables;
		
}

function em_create_events_table() {
	global  $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$table_name = $wpdb->prefix.'em_events';
	$sql = "CREATE TABLE ".$table_name." (
		event_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		post_id bigint(20) unsigned NOT NULL,
		event_parent bigint(20) unsigned NULL DEFAULT NULL,
		event_slug VARCHAR( 200 ) NULL DEFAULT NULL,
		event_owner bigint(20) unsigned DEFAULT NULL,
		event_status tinyint(1) NULL DEFAULT NULL,
		event_active_status tinyint(2) NULL DEFAULT 1,
		event_name text NULL DEFAULT NULL,
		event_start_date date NULL DEFAULT NULL,
		event_end_date date NULL DEFAULT NULL,
		event_start_time time NULL DEFAULT NULL,
		event_end_time time NULL DEFAULT NULL,
 		event_all_day tinyint(1) unsigned NULL DEFAULT NULL,
		event_start datetime NULL DEFAULT NULL,
		event_end datetime NULL DEFAULT NULL,
		event_timezone tinytext NULL DEFAULT NULL,
		post_content longtext NULL DEFAULT NULL,
		event_rsvp tinyint(1) unsigned NOT NULL DEFAULT 0,
		event_rsvp_date date NULL DEFAULT NULL,
		event_rsvp_time time NULL DEFAULT NULL,
		event_rsvp_spaces int(5) NULL DEFAULT NULL,
		event_spaces int(5) NULL DEFAULT 0,
		event_private tinyint(1) unsigned NOT NULL DEFAULT 0,
		location_id bigint(20) unsigned NULL DEFAULT NULL,
		event_location_type VARCHAR(15) NULL DEFAULT NULL,
		recurrence_id bigint(20) unsigned NULL DEFAULT NULL,
  		event_date_created datetime NULL DEFAULT NULL,
  		event_date_modified datetime NULL DEFAULT NULL,
		recurrence tinyint(1) unsigned NOT NULL DEFAULT 0,
		recurrence_interval int(4) NULL DEFAULT NULL,
		recurrence_freq tinytext NULL DEFAULT NULL,
		recurrence_byday tinytext NULL DEFAULT NULL,
		recurrence_byweekno int(4) NULL DEFAULT NULL,
		recurrence_days int(4) NULL DEFAULT NULL,
		recurrence_rsvp_days int(3) NULL DEFAULT NULL,
		blog_id bigint(20) unsigned NULL DEFAULT NULL,
		group_id bigint(20) unsigned NULL DEFAULT NULL,
		event_language varchar(14) NULL DEFAULT NULL,
		event_translation tinyint(1) unsigned NOT NULL DEFAULT 0,
		PRIMARY KEY  (event_id)
		) DEFAULT CHARSET=utf8 ;";

	if( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ){
		dbDelta($sql);
	}elseif( get_option('dbem_version') != '' ){
		if( get_option('dbem_version') < 5.984 ){
			// change the recurrence flag to a required field defaulting to 0, to avoid missing recurrences in EM_Events::get() due to wayward null values
			$wpdb->query("UPDATE $table_name SET recurrence = 0 WHERE recurrence IS NULL");
			$wpdb->query("ALTER TABLE $table_name CHANGE `recurrence` `recurrence` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'");
			$wpdb->query("ALTER TABLE $table_name CHANGE `event_status` `event_status` TINYINT(1) NULL DEFAULT NULL;");
		}
		dbDelta($sql);
	}
	em_sort_out_table_nu_keys($table_name, array('event_status','event_active_status','post_id','blog_id','group_id','location_id','event_start', 'event_end', 'event_start_date', 'event_end_date'));
	if( em_check_utf8mb4_tables() ) maybe_convert_table_to_utf8mb4( $table_name );
}

function em_create_events_meta_table(){
	global  $wpdb;
	$table_name = $wpdb->prefix.'em_meta';

	// Creating the events table
	$sql = "CREATE TABLE ".$table_name." (
		meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		object_id bigint(20) unsigned NOT NULL,
		meta_key varchar(255) DEFAULT NULL,
		meta_value longtext,
		meta_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (meta_id)
		) DEFAULT CHARSET=utf8 ";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	dbDelta($sql);
	em_sort_out_table_nu_keys($table_name, array('object_id','meta_key'));
	if( em_check_utf8mb4_tables() ) maybe_convert_table_to_utf8mb4( $table_name );
}

function em_create_locations_table() {

	global  $wpdb;
	$table_name = $wpdb->prefix.'em_locations';

	// Creating the events table
	$sql = "CREATE TABLE ".$table_name." (
		location_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		post_id bigint(20) unsigned NOT NULL,
		blog_id bigint(20) unsigned NULL DEFAULT NULL,
		location_parent bigint(20) unsigned NULL DEFAULT NULL,
		location_slug VARCHAR( 200 ) NULL DEFAULT NULL,
		location_name text NULL DEFAULT NULL,
		location_owner bigint(20) unsigned NOT NULL DEFAULT 0,
		location_address VARCHAR( 200 ) NULL DEFAULT NULL,
		location_town VARCHAR( 200 ) NULL DEFAULT NULL,
		location_state VARCHAR( 200 ) NULL DEFAULT NULL,
		location_postcode VARCHAR( 10 ) NULL DEFAULT NULL,
		location_region VARCHAR( 200 ) NULL DEFAULT NULL,
		location_country CHAR( 2 ) NULL DEFAULT NULL,
		location_latitude DECIMAL( 9, 6 ) NULL DEFAULT NULL,
		location_longitude DECIMAL( 9, 6 ) NULL DEFAULT NULL,
		post_content longtext NULL DEFAULT NULL,
		location_status int(1) NULL DEFAULT NULL,
		location_private tinyint(1) unsigned NOT NULL DEFAULT 0,
		location_language varchar(14) NULL DEFAULT NULL,
		location_translation tinyint(1) unsigned NOT NULL DEFAULT 0,
		PRIMARY KEY  (location_id)
		) DEFAULT CHARSET=utf8 ;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	if( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {
		dbDelta($sql);
	}else{
		if( get_option('dbem_version') != '' && get_option('dbem_version') < 4.938 ){
			$wpdb->query("ALTER TABLE $table_name CHANGE location_description post_content longtext NULL DEFAULT NULL");
		}
		dbDelta($sql);
		if( get_option('dbem_version') != '' && get_option('dbem_version') < 4.93 ){
			//if updating from version 4 (4.93 is beta v5) then set all statuses to 1 since it's new
			$wpdb->query("UPDATE ".$table_name." SET location_status=1");
		}
	}
	if( em_check_utf8mb4_tables() ){
		maybe_convert_table_to_utf8mb4( $table_name );
		em_sort_out_table_nu_keys($table_name, array('location_state (location_state(191))','location_region (location_region(191))','location_country','post_id','blog_id'));
	}else{
		em_sort_out_table_nu_keys($table_name, array('location_state','location_region','location_country','post_id','blog_id'));
	}
}

function em_create_bookings_table() {

	global  $wpdb;
	$table_name = $wpdb->prefix.'em_bookings';

	$sql = "CREATE TABLE ".$table_name." (
		booking_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		booking_uuid char(32) NOT NULL,
		event_id bigint(20) unsigned NULL,
		person_id bigint(20) unsigned NOT NULL,
		booking_spaces int(5) NOT NULL,
		booking_comment text DEFAULT NULL,
		booking_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		booking_status int(2) NOT NULL DEFAULT 1,
		booking_rsvp_status int(1) NULL DEFAULT NULL,
 		booking_price decimal(14,4) unsigned NOT NULL DEFAULT 0,
 		booking_tax_rate decimal(7,4) NULL DEFAULT NULL,
 		booking_taxes decimal(14,4) NULL DEFAULT NULL,
		booking_meta LONGTEXT NULL,
		PRIMARY KEY  (booking_id)
		) DEFAULT CHARSET=utf8 ;";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	em_sort_out_table_nu_keys($table_name, array('event_id','person_id','booking_status', 'booking_rsvp_status'));
	if( em_check_utf8mb4_tables() ) maybe_convert_table_to_utf8mb4( $table_name );
}

function em_create_bookings_meta_table() {
	
	global  $wpdb;
	$table_name = $wpdb->prefix.'em_bookings_meta';
	
	// Creating the events table
	$sql = "CREATE TABLE ".$table_name." (
		meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		booking_id bigint(20) unsigned NOT NULL,
		meta_key varchar(255) DEFAULT NULL,
		meta_value longtext,
		PRIMARY KEY  (meta_id)
		) DEFAULT CHARSET=utf8 ";
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	em_sort_out_table_nu_keys($table_name, array('booking_id','meta_key'));
	if( em_check_utf8mb4_tables() ) maybe_convert_table_to_utf8mb4( $table_name );
}


//Add the categories table
function em_create_tickets_table() {

	global  $wpdb;
	$table_name = $wpdb->prefix.'em_tickets';

	// Creating the events table
	$sql = "CREATE TABLE {$table_name} (
		ticket_id BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_id BIGINT( 20 ) UNSIGNED NOT NULL ,
		ticket_name TINYTEXT NOT NULL ,
		ticket_description TEXT NULL ,
		ticket_price DECIMAL( 14 , 4 ) NULL ,
		ticket_start DATETIME NULL ,
		ticket_end DATETIME NULL ,
		ticket_min INT( 10 ) NULL ,
		ticket_max INT( 10 ) NULL ,
		ticket_spaces INT NULL ,
		ticket_members INT( 1 ) NULL ,
		ticket_members_roles LONGTEXT NULL,
		ticket_guests INT( 1 ) NULL ,
		ticket_required INT( 1 ) NULL ,
		ticket_parent BIGINT( 20 ) UNSIGNED NULL,
		ticket_order INT( 2 ) UNSIGNED NULL,
		ticket_meta LONGTEXT NULL,
		PRIMARY KEY  (ticket_id)
		) DEFAULT CHARSET=utf8 ;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	em_sort_out_table_nu_keys($table_name, array('event_id'));
	if( em_check_utf8mb4_tables() ) maybe_convert_table_to_utf8mb4( $table_name );
}

//Add the categories table
function em_create_tickets_bookings_table() {
	global  $wpdb;
	$table_name = $wpdb->prefix.'em_tickets_bookings';

	// Creating the events table
	$sql = "CREATE TABLE {$table_name} (
		  ticket_booking_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  ticket_uuid char(32) NOT NULL,
		  booking_id bigint(20) unsigned NOT NULL,
		  ticket_id bigint(20) unsigned NOT NULL,
		  ticket_booking_spaces int(6) NOT NULL,
		  ticket_booking_price decimal(14,4) NOT NULL,
		  ticket_booking_order int(2) NULL,
		  PRIMARY KEY  (ticket_booking_id)
		) DEFAULT CHARSET=utf8 ;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	em_sort_out_table_nu_keys($table_name, array('ticket_uuid', 'booking_id','ticket_id'));
	if( em_check_utf8mb4_tables() ) maybe_convert_table_to_utf8mb4( $table_name );
}

function em_create_tickets_bookings_meta_table() {
	global  $wpdb;
	$table_name = $wpdb->prefix.'em_tickets_bookings_meta';
	
	// Creating the events table
	$sql = "CREATE TABLE ".$table_name." (
		meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		ticket_booking_id bigint(20) unsigned NOT NULL,
		meta_key varchar(255) DEFAULT NULL,
		meta_value longtext,
		PRIMARY KEY  (meta_id)
		) DEFAULT CHARSET=utf8 ";
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	em_sort_out_table_nu_keys($table_name, array('ticket_booking_id','meta_key'));
	if( em_check_utf8mb4_tables() ) maybe_convert_table_to_utf8mb4( $table_name );
}

function em_add_options() {
	global $wp_locale, $wpdb;
	$already_installed = get_option('dbem_version', false) !== false;
	if( !$already_installed ) {
		$advice = sprintf( __("<p>Events Manager is ready to go! It is highly recommended you read the <a href='%s'>Getting Started</a> guide on our site, as well as checking out the <a href='%s'>Settings Page</a></p>", 'events-manager'), 'https://wp-events-plugin.com/documentation/getting-started-guide/?utm_source=em&utm_medium=plugin&utm_content=installationlink&utm_campaign=plugin_links', EM_ADMIN_URL .'&amp;page=events-manager-options');
		$EM_Admin_Notice = new EM_Admin_Notice(array( 'name' => 'hello', 'who' => 'admin', 'where' => 'all', 'message' => $advice, 'what'=>'success' ));
		EM_Admin_Notices::add($EM_Admin_Notice, is_multisite());
	}
	$decimal_point = !empty($wp_locale->number_format['decimal_point']) ? $wp_locale->number_format['decimal_point']:'.';
	$thousands_sep = !empty($wp_locale->number_format['thousands_sep']) ? $wp_locale->number_format['thousands_sep']:',';
	$email_footer = '<br/><br/>-------------------------------<br/>Powered by Events Manager - http://wp-events-plugin.com';
	$respondent_email_body_localizable = __("Dear #_BOOKINGNAME, <br/>You have successfully reserved #_BOOKINGSPACES space/spaces for #_EVENTNAME.<br/>When : #_EVENTDATES @ #_EVENTTIMES<br/>Where : #_LOCATIONNAME - #_LOCATIONFULLLINE<br/>Yours faithfully,<br/>#_CONTACTNAME",'events-manager').$email_footer;
	$respondent_email_pending_body_localizable = __("Dear #_BOOKINGNAME, <br/>You have requested #_BOOKINGSPACES space/spaces for #_EVENTNAME.<br/>When : #_EVENTDATES @ #_EVENTTIMES<br/>Where : #_LOCATIONNAME - #_LOCATIONFULLLINE<br/>Your booking is currently pending approval by our administrators. Once approved you will receive an automatic confirmation.<br/>Yours faithfully,<br/>#_CONTACTNAME",'events-manager').$email_footer;
	$respondent_email_rejected_body_localizable = __("Dear #_BOOKINGNAME, <br/>Your requested booking for #_BOOKINGSPACES spaces at #_EVENTNAME on #_EVENTDATES has been rejected.<br/>Yours faithfully,<br/>#_CONTACTNAME",'events-manager').$email_footer;
	$respondent_email_cancelled_body_localizable = __("Dear #_BOOKINGNAME, <br/>Your requested booking for #_BOOKINGSPACES spaces at #_EVENTNAME on #_EVENTDATES has been cancelled.<br/>Yours faithfully,<br/>#_CONTACTNAME",'events-manager').$email_footer;
	$respondent_email_event_cancelled = __("Dear Guest, <br/>We regret to inform you that #_EVENTNAME on #_EVENTDATES has been cancelled.<br/>Yours faithfully,<br/>#_CONTACTNAME",'events-manager').$email_footer;
	$event_approved_email_body = EM_Formats::get_email_format('dbem_event_approved_email_body') .$email_footer;
	$event_submitted_email_body = __("A new event has been submitted by #_CONTACTNAME.<br/>Name : #_EVENTNAME <br/>Date : #_EVENTDATES <br/>Time : #_EVENTTIMES <br/>Please visit #_EDITEVENTURL to review this event for approval.",'events-manager').$email_footer;
	$event_submitted_email_body = str_replace('#_EDITEVENTURL', admin_url().'post.php?action=edit&post=#_EVENTPOSTID', $event_submitted_email_body);
	$event_published_email_body = __("A new event has been published by #_CONTACTNAME.<br/>Name : #_EVENTNAME <br/>Date : #_EVENTDATES <br/>Time : #_EVENTTIMES <br/>Edit this event - #_EDITEVENTURL <br/> View this event - #_EVENTURL",'events-manager').$email_footer;
	$event_published_email_body = str_replace('#_EDITEVENTURL', admin_url().'post.php?action=edit&post=#_EVENTPOSTID', $event_published_email_body);
	$event_resubmitted_email_body = __("A previously published event has been modified by #_CONTACTNAME, and this event is now unpublished and pending your approval.<br/>Name : #_EVENTNAME <br/>Date : #_EVENTDATES <br/>Time : #_EVENTTIMES <br/>Please visit #_EDITEVENTURL to review this event for approval.",'events-manager').$email_footer;
	$event_resubmitted_email_body = str_replace('#_EDITEVENTURL', admin_url().'post.php?action=edit&post=#_EVENTPOSTID', $event_resubmitted_email_body);

	//event admin emails - new format to the above, standard format plus one unique line per booking status at the top of the body and subject line
	$contact_person_email_body_template = '#_EVENTNAME - #_EVENTDATES @ #_EVENTTIMES'.'<br/>'
 		    .__('Now there are #_BOOKEDSPACES spaces reserved, #_AVAILABLESPACES are still available.','events-manager').'<br/>'.
 		    strtoupper(__('Booking Details','events-manager')).'<br/>'.
 	 		__('Name','events-manager').' : #_BOOKINGNAME'.'<br/>'.
 		    __('Email','events-manager').' : #_BOOKINGEMAIL'.'<br/>'.
 		    '#_BOOKINGSUMMARY'.'<br/>'.
 		    '<br/>Powered by Events Manager - http://wp-events-plugin.com';
	$contact_person_emails['confirmed'] = sprintf(__('The following booking is %s :','events-manager'),strtolower(__('Confirmed','events-manager'))).'<br/>'.$contact_person_email_body_template;
	$contact_person_emails['pending'] = sprintf(__('The following booking is %s :','events-manager'),strtolower(__('Pending','events-manager'))).'<br/>'.$contact_person_email_body_template;
	$contact_person_emails['cancelled'] = sprintf(__('The following booking is %s :','events-manager'),strtolower(__('Cancelled','events-manager'))).'<br/>'.$contact_person_email_body_template;
	$contact_person_emails['rejected'] = sprintf(__('The following booking is %s :','events-manager'),strtolower(__('Rejected','events-manager'))).'<br/>'.$contact_person_email_body_template;
	//registration email content
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	$booking_registration_email_subject = sprintf(__('[%s] Your username and password', 'events-manager'), $blogname);
	$booking_registration_email_body = sprintf(__('You have successfully created an account at %s', 'events-manager'), $blogname).
		'<br/>'.sprintf(__('You can log into our site here : %s', 'events-manager'), get_bloginfo('wpurl').'/wp-login.php').
		'<br/>'.__('Username', 'events-manager').' : %username%'.
		'<br/>'.__('Password', 'events-manager').' : %password%'.
		'<br/>'.sprintf(__('To view your bookings, please visit %s after logging in.', 'events-manager'), em_get_my_bookings_url());
	//all the options
	$dbem_options = array(
		'dbem_data' => array(), //used to store admin-related data such as notice flags and other row keys that may not always exist in the wp_options table
		//time formats
		'dbem_time_format' => get_option('time_format'),
		'dbem_date_format' => get_option('date_format'),
		'dbem_date_format_js' => 'dd/mm/yy',
		'dbem_datepicker_format' => 'Y-m-d',
		'dbem_dates_separator' => ' - ',
		'dbem_dates_range_double_inputs' => 0,
		'dbem_times_separator' => ' - ',
		//defaults
		'dbem_default_category'=>0,
		'dbem_default_location'=>0,
		//Event List Options
		'dbem_events_default_orderby' => 'event_start_date,event_start_time,event_name',
		'dbem_events_default_order' => 'ASC',
		'dbem_events_default_limit' => 10,
		//Event Search Options
		'dbem_search_form_main' => 1,
		'dbem_search_form_responsive' => $already_installed ? 'one-line' : 'multi-line',
		'dbem_search_form_sorting' => !$already_installed,
		'dbem_search_form_cookies' => false, // not in admin yet
		'dbem_search_form_submit' => __('Search','events-manager'),
		'dbem_search_form_views' => array('list', 'list-grouped', 'grid', 'map', 'calendar'),
		'dbem_search_form_view' => 'list',
        'dbem_search_form_saved_searches' => !$already_installed,
		'dbem_search_form_advanced' => 1,
		'dbem_search_form_advanced_mode' => 'modal',
		'dbem_search_form_advanced_style' => $already_installed ? 'accordion' : 'headings',
		'dbem_search_form_advanced_hidden' => 1,
        'dbem_search_form_advanced_trigger' => 1,
		'dbem_search_form_advanced_show' => __('Show Advanced Search','events-manager'),
		'dbem_search_form_advanced_hide' => __('Hide Advanced Search','events-manager'),
		'dbem_search_form_text' => 1,
		'dbem_search_form_text_label' => __('Search','events-manager'),
		'dbem_search_form_text_advanced' => 1,
		'dbem_search_form_text_label_advanced' => __('Search','events-manager'),
		'dbem_search_form_text_hide_s' => false,
		'dbem_search_form_text_hide_m' => false,
		'dbem_search_form_geo' => 1,
		'dbem_search_form_geo_label' => __('Near...','events-manager'),
		'dbem_search_form_geo_hide_s' => $already_installed,
		'dbem_search_form_geo_hide_m' => $already_installed,
		'dbem_search_form_geo_advanced' => 1,
		'dbem_search_form_geo_label_advanced' => __('Near...','events-manager'),
		'dbem_search_form_geo_units' => 1,
		'dbem_search_form_geo_units_label' => __('Within','events-manager'),
		'dbem_search_form_geo_unit_default' => 'mi',
		'dbem_search_form_geo_distance_default' => 25,
	    'dbem_search_form_geo_distance_options' => '5,10,25,50,100',
		'dbem_search_form_dates' => 1,
		'dbem_search_form_dates_label' => __('Dates','events-manager'),
		'dbem_search_form_dates_hide_s' => $already_installed,
		'dbem_search_form_dates_hide_m' => $already_installed,
		'dbem_search_form_dates_separator' => __('and','events-manager'),
		'dbem_search_form_dates_format' => 'M j',
		'dbem_search_form_dates_advanced' => 1,
		'dbem_search_form_dates_label_advanced' => __('Dates','events-manager'),
		'dbem_search_form_dates_separator_advanced' => __('and','events-manager'),
		'dbem_search_form_dates_format_advanced' => 'M j',
		'dbem_search_form_categories' => 1,
		'dbem_search_form_categories_label' => __('All Categories','events-manager'),
		'dbem_search_form_category_label' => __('Categories','events-manager'),
		'dbem_search_form_categories_placeholder' => sprintf(__( 'Search %s...', 'events-manager'),__('Categories','events-manager')),
		'dbem_search_form_categories_include' => '',
		'dbem_search_form_categories_exclude' => '',
		'dbem_search_form_tags' => 1,
		'dbem_search_form_tags_label' => __('All Tags','events-manager'),
		'dbem_search_form_tag_label' => __('Tags','events-manager'),
		'dbem_search_form_tags_placeholder' => sprintf(__( 'Search %s...', 'events-manager'),__('Tags','events-manager')),
		'dbem_search_form_tags_include' => '',
		'dbem_search_form_tags_exclude' => '',
		'dbem_search_form_countries' => 1,
		'dbem_search_form_default_country' => get_option('dbem_location_default_country',''),
		'dbem_search_form_countries_label' => __('All Countries','events-manager'),
		'dbem_search_form_country_label' => __('Country','events-manager'),
		'dbem_search_form_regions' => 1,
		'dbem_search_form_regions_label' => __('All Regions','events-manager'),
		'dbem_search_form_region_label' => __('Region','events-manager'),
		'dbem_search_form_states' => 1,
		'dbem_search_form_states_label' => __('All States','events-manager'),
		'dbem_search_form_state_label' => __('State/County','events-manager'),
		'dbem_search_form_towns' => 0,
		'dbem_search_form_towns_label' => __('All Cities/Towns','events-manager'),
		'dbem_search_form_town_label' => __('City/Town','events-manager'),
		/*
		//GeoCoding
		'dbem_geo' => 1,
		'dbem_geonames_username' => '',
		*/
		//Event Form and Anon Submissions
		'dbem_events_form_editor' => 1,
		'dbem_events_form_reshow' => 1,
		'dbem_events_form_result_success' => __('You have successfully submitted your event, which will be published pending approval.','events-manager'),
		'dbem_events_form_result_success_updated' => __('You have successfully updated your event, which will be republished pending approval.','events-manager'),
		'dbem_events_anonymous_submissions' => 0,
		'dbem_events_anonymous_user' => 0,
		'dbem_events_anonymous_result_success' => __('You have successfully submitted your event, which will be published pending approval.','events-manager'),
		//Event Emails
		'dbem_event_submitted_email_admin' => '',
		'dbem_event_submitted_email_subject' => __('Submitted Event Awaiting Approval', 'events-manager'),
		'dbem_event_submitted_email_body' => str_replace("<br/>", "\n\r", $event_submitted_email_body),
		'dbem_event_resubmitted_email_subject' => __('Re-Submitted Event Awaiting Approval', 'events-manager'),
		'dbem_event_resubmitted_email_body' => str_replace("<br/>", "\n\r", $event_resubmitted_email_body),
		'dbem_event_published_email_subject' => __('Published Event', 'events-manager').' - #_EVENTNAME',
		'dbem_event_published_email_body' => str_replace("<br/>", "\n\r", $event_published_email_body),
		'dbem_event_approved_email_subject' => __("Event Approved",'events-manager'). " - #_EVENTNAME" ,
		'dbem_event_approved_email_body' => str_replace("<br/>", "\n\r", $event_approved_email_body),
		'dbem_event_reapproved_email_subject' => __("Event Approved",'events-manager'). " - #_EVENTNAME" ,
		'dbem_event_reapproved_email_body' => str_replace("<br/>", "\n\r", $event_approved_email_body),
		//Event Formatting
		'dbem_events_page_title' => __('Events','events-manager'),
		'dbem_events_page_scope' => 'future',
		'dbem_events_page_search_form' => 1,
		'dbem_event_list_item_format_header' => EM_Formats::dbem_event_list_item_format_header(''),
		'dbem_event_list_item_format' => EM_Formats::dbem_event_list_item_format(''),
		'dbem_event_list_item_format_footer' => EM_Formats::dbem_event_list_item_format_footer(''),
		'dbem_event_grid_item_format_header' => EM_Formats::dbem_event_grid_item_format_header(''),
		'dbem_event_grid_item_format' => EM_Formats::dbem_event_grid_item_format(''),
		'dbem_event_grid_item_format_footer' => EM_Formats::dbem_event_grid_item_format_footer(''),
		'dbem_event_grid_item_width' => '250',
		'dbem_event_list_groupby' => 'monthly',
		'dbem_event_list_groupby_format' => 'F Y',
		'dbem_event_list_groupby_header_format' => '<h2>#s</h2>',
		'dbem_display_calendar_in_events_page' => 0,
		'dbem_single_event_format' => EM_Formats::dbem_single_event_format(''),
	    'dbem_event_excerpt_format' => EM_Formats::dbem_event_excerpt_format(''),
	    'dbem_event_excerpt_alt_format' => EM_Formats::dbem_event_excerpt_alt_format(''),
		'dbem_event_page_title_format' => '#_EVENTNAME',
		'dbem_event_all_day_message' => __('All Day','events-manager'),
		'dbem_no_events_message' => sprintf(__( 'No %s', 'events-manager'),__('Events','events-manager')),
		//Location Formatting
		'dbem_locations_default_orderby' => 'location_name',
		'dbem_locations_default_order' => 'ASC',
		'dbem_locations_default_limit' => 10,
		'dbem_locations_page_title' => __('Event','events-manager')." ".__('Locations','events-manager'),
		'dbem_locations_page_search_form' => 1,
		'dbem_no_locations_message' => sprintf(__( 'No %s', 'events-manager'),__('Locations','events-manager')),
		'dbem_location_default_country' => '',
		'dbem_location_list_item_format_header' => EM_Formats::dbem_location_list_item_format_header(''),
		'dbem_location_list_item_format' => EM_Formats::dbem_location_list_item_format(''),
		'dbem_location_list_item_format_footer' => EM_Formats::dbem_location_list_item_format_footer(''),
		'dbem_location_grid_item_format_header' => EM_Formats::dbem_location_grid_item_format_header(''),
		'dbem_location_grid_item_format' => EM_Formats::dbem_location_grid_item_format(''),
		'dbem_location_grid_item_format_footer' => EM_Formats::dbem_location_grid_item_format_footer(''),
		'dbem_location_grid_item_width' => '250',
		'dbem_location_page_title_format' => '#_LOCATIONNAME',
		'dbem_single_location_format' => EM_Formats::dbem_single_location_format(''),
	    'dbem_location_excerpt_format' => EM_Formats::dbem_location_excerpt_format(''),
	    'dbem_location_excerpt_alt_format' => EM_Formats::dbem_location_excerpt_alt_format(''),
		'dbem_location_no_events_message' => __('No events in this location', 'events-manager'),
		'dbem_location_event_list_item_header_format' => EM_Formats::dbem_location_event_list_item_header_format(''),
		'dbem_location_event_list_item_format' => EM_Formats::dbem_location_event_list_item_format(''),
		'dbem_location_event_list_item_footer_format' => EM_Formats::dbem_location_event_list_item_footer_format(''),
		'dbem_location_event_list_limit' => 20,
		'dbem_location_event_list_orderby' => 'event_start_date,event_start_time,event_name',
		'dbem_location_event_list_order' => 'ASC',
		'dbem_location_event_single_format' => '#_EVENTLINK - #_EVENTDATES - #_EVENTTIMES',
		'dbem_location_no_event_message' => __('No events in this location', 'events-manager'),
		//Category page options
		'dbem_categories_default_limit' => 10,
		'dbem_categories_default_orderby' => 'name',
		'dbem_categories_default_order' =>  'ASC',
		//Categories Page Formatting
		'dbem_categories_list_item_format_header' => EM_Formats::dbem_categories_list_item_format_header(''),
		'dbem_categories_list_item_format' => EM_Formats::dbem_categories_list_item_format(''),
		'dbem_categories_list_item_format_footer' => EM_Formats::dbem_categories_list_item_format_footer(''),
		'dbem_no_categories_message' =>  sprintf(__( 'No %s', 'events-manager'),__('Categories','events-manager')),
		//Category Formatting
		'dbem_category_page_title_format' => '#_CATEGORYNAME',
		'dbem_category_page_format' => EM_Formats::dbem_category_page_format(''),
		'dbem_category_no_events_message' =>  __('No events in this category', 'events-manager'),
		'dbem_category_event_list_item_header_format' => EM_Formats::dbem_category_event_list_item_header_format(''),
		'dbem_category_event_list_item_format' => EM_Formats::dbem_category_event_list_item_format(''),
		'dbem_category_event_list_item_footer_format' => EM_Formats::dbem_category_event_list_item_footer_format(''),
		'dbem_category_event_list_limit' => 20,
		'dbem_category_event_list_orderby' => 'event_start_date,event_start_time,event_name',
		'dbem_category_event_list_order' => 'ASC',
		'dbem_category_event_single_format' => '#_EVENTLINK - #_EVENTDATES - #_EVENTTIMES',
		'dbem_category_no_event_message' => __('No events in this category', 'events-manager'),
		'dbem_category_default_color' => '#a8d144',
		//Tags page options
		'dbem_tags_default_limit' => 10,
		'dbem_tags_default_orderby' => 'name',
		'dbem_tags_default_order' =>  'ASC',
		// Event Status and Cancellations
		'dbem_event_cancelled_email' => !$already_installed,
		'dbem_event_cancelled_email_subject' => __('Cancelled', 'events-manager') . ' - #_EVENTNAME',
		'dbem_event_cancelled_email_body' => str_replace("<br/>", "\n\r", $respondent_email_event_cancelled),
		'dbem_event_cancelled_bookings' => !$already_installed,
		'dbem_event_cancelled_bookings_email' => !$already_installed,
		//Tags Page Formatting
		'dbem_tags_list_item_format_header' => EM_Formats::dbem_tags_list_item_format_header(''),
		'dbem_tags_list_item_format' => EM_Formats::dbem_tags_list_item_format(''),
		'dbem_tags_list_item_format_footer' => EM_Formats::dbem_tags_list_item_format_footer(''),
		'dbem_no_tags_message' =>  sprintf(__( 'No %s', 'events-manager'),__('Tags','events-manager')),
		//Tag Page Formatting
		'dbem_tag_page_title_format' => '#_TAGNAME',
		'dbem_tag_page_format' => EM_Formats::dbem_tag_page_format(''),
		'dbem_tag_no_events_message' => __('No events with this tag', 'events-manager'),
		'dbem_tag_event_list_item_header_format' => EM_Formats::dbem_tag_event_list_item_header_format(''),
		'dbem_tag_event_list_item_format' => EM_Formats::dbem_tag_event_list_item_format(''),
		'dbem_tag_event_list_item_footer_format' => EM_Formats::dbem_tag_event_list_item_footer_format(''),
		
		'dbem_tag_event_single_format' => '#_EVENTLINK - #_EVENTDATES - #_EVENTTIMES',
		'dbem_tag_no_event_message' => __('No events with this tag', 'events-manager'),
		'dbem_tag_event_list_limit' => 20,
		'dbem_tag_event_list_orderby' => 'event_start_date,event_start_time,event_name',
		'dbem_tag_event_list_order' => 'ASC',
		'dbem_tag_default_color' => '#a8d145',
		//RSS Stuff
		'dbem_rss_limit' => 50,
		'dbem_rss_scope' => 'future',
		'dbem_rss_main_title' => get_bloginfo('title')." - ".__('Events', 'events-manager'),
		'dbem_rss_main_description' => get_bloginfo('description')." - ".__('Events', 'events-manager'),
		'dbem_rss_description_format' => "#_EVENTDATES - #_EVENTTIMES <br/>#_LOCATIONNAME <br/>#_LOCATIONADDRESS <br/>#_LOCATIONTOWN",
		'dbem_rss_title_format' => "#_EVENTNAME",
		'dbem_rss_order' => get_option('dbem_events_default_order', 'ASC'), //get event order and orderby or use same new installation defaults
		'dbem_rss_orderby' => get_option('dbem_events_default_orderby', 'event_start_date,event_start_time,event_name'),
		'em_rss_pubdate' => date('D, d M Y H:i:s +0000'),
		//iCal Stuff
		'dbem_ical_limit' => 50,
		'dbem_ical_scope' => "future",
		'dbem_ical_description_format' => "#_EVENTNAME",
		'dbem_ical_real_description_format' => "#_EVENTEXCERPT",
		'dbem_ical_location_format' => "#_LOCATIONNAME, #_LOCATIONFULLLINE, #_LOCATIONCOUNTRY",
		//Google Maps
		'dbem_gmap_is_active'=> 1,
		'dbem_google_maps_browser_key'=> '',
		'dbem_map_default_width'=> '400px', //eventually will use %
		'dbem_map_default_height'=> '300px',
		'dbem_location_baloon_format' => EM_Formats::dbem_location_baloon_format(''),
		'dbem_map_text_format' => EM_Formats::dbem_map_text_format(''),
		//Email Config
		'dbem_email_disable_registration' => 0,
		'dbem_rsvp_mail_port' => 465,
		'dbem_smtp_host' => 'localhost',
		'dbem_mail_sender_name' => '',
		'dbem_rsvp_mail_send_method' => 'wp_mail',
		'dbem_rsvp_mail_SMTPAuth' => 1,
		'dbem_smtp_html' => 1,
		'dbem_smtp_html_br' => 1,
		'dbem_smtp_encryption' => 'tls',
		'dbem_smtp_autotls' => true,
		//Image Manipulation
		'dbem_image_max_width' => 700,
		'dbem_image_max_height' => 700,
		'dbem_image_min_width' => 50,
		'dbem_image_min_height' => 50,
		'dbem_image_max_size' => 204800,
		//Calendar Options
		'dbem_list_date_title' => __('Events', 'events-manager').' - #j #M #y',
		'dbem_full_calendar_month_format' => 'M Y',
		'dbem_full_calendar_long_events' => '0',
		'dbem_full_calendar_initials_length' => 0,
		'dbem_full_calendar_abbreviated_weekdays' => true,
		'dbem_display_calendar_day_single_yes' => 1,
		'dbem_small_calendar_initials_length' => 1,
		'dbem_small_calendar_abbreviated_weekdays' => false,
		'dbem_small_calendar_long_events' => '0',
		'dbem_display_calendar_order' => 'ASC',
		'dbem_display_calendar_orderby' => 'event_name,event_start_time',
		'dbem_display_calendar_events_limit' => get_option('dbem_full_calendar_events_limit',3),
		'dbem_display_calendar_events_limit_msg' => __('more...','events-manager'),
		'dbem_calendar_direct_links' => 1,
		'dbem_calendar_preview_mode' => 'modal',
		'dbem_calendar_preview_mode_date' => 'modal',
		'dbem_calendar_preview_modal_date_format' => EM_Formats::dbem_calendar_preview_modal_date_format(''),
		'dbem_calendar_preview_modal_event_format' => EM_Formats::dbem_calendar_preview_modal_event_format(''),
		'dbem_calendar_preview_tooltip_event_format' => EM_Formats::dbem_calendar_preview_tooltip_event_format(''),
		'dbem_calendar_large_pill_format' => '#_12HSTARTTIME - #_EVENTLINK',
		//General Settings
		'dbem_timezone_enabled' => 1,
		'dbem_timezone_default' => EM_DateTimeZone::create()->getName(),
		'dbem_require_location' => 0,
		'dbem_locations_enabled' => 1,
		'dbem_location_types' => array('location' => 1, 'url' => 1),
		'dbem_use_select_for_locations' => 0,
		'dbem_attributes_enabled' => 1,
		'dbem_recurrence_enabled'=> 1,
		'dbem_rsvp_enabled'=> 1,
		'dbem_categories_enabled'=> 1,
		'dbem_tags_enabled' => 1,
		'dbem_placeholders_custom' => '',
		'dbem_location_attributes_enabled' => 1,
		'dbem_location_placeholders_custom' => '',
		//Bookings
		'dbem_bookings_registration_disable' => 0,
		'dbem_bookings_registration_disable_user_emails' => 0,
		'dbem_bookings_approval' => 1, //approval is on by default
		'dbem_bookings_approval_reserved' => 0, //overbooking before approval?
		'dbem_bookings_approval_overbooking' => 0, //overbooking possible when approving?
		'dbem_bookings_double'=>0,//double bookings or more, users can't double book by default
		'dbem_bookings_user_cancellation' => 1, //can users cancel their booking?
		'dbem_bookings_user_cancellation_time' => '', //can users cancel their booking?
		'dbem_bookings_currency' => 'USD',
		'dbem_bookings_currency_decimal_point' => $decimal_point,
		'dbem_bookings_currency_thousands_sep' => $thousands_sep,
		'dbem_bookings_currency_format' => '@#',
		'dbem_bookings_tax' => 0, //extra tax
		'dbem_bookings_tax_auto_add' => 0, //adjust prices to show tax?
			//Form Options
			'dbem_bookings_submit_button' => __('Submit Booking', 'events-manager'),
			'dbem_bookings_submit_button_paid' => __('Submit Booking', 'events-manager') .' - %s',
			'dbem_bookings_submit_button_processing' => __('Processing ...', 'events-manager'),
			'dbem_bookings_login_form' => 1, //show login form on booking area
			'dbem_bookings_form_hide_dynamic' => get_option('dbem_version', false) === false, //show login form on booking area
			'dbem_bookings_summary' => 1,
			'dbem_bookings_summary_taxes_itemized' => !get_option('dbem_bookings_tax_auto_add', 0),
			'dbem_bookings_summary_free' => 1,
			'dbem_bookings_summary_message' => __('Please select at least one space to proceed with your booking.', 'events-manager'),
			'dbem_bookings_anonymous' => 1,
			'dbem_bookings_form_max' => 20,
			'dbem_bookings_header_tickets' => esc_html__('Tickets', 'events-manager'),
			'dbem_bookings_header_reg_info' => esc_html__('Registration Information', 'events-manager'),
			'dbem_bookings_header_summary' => esc_html__('Booking Summary', 'events-manager'),
			'dbem_bookings_header_confirm' => '', // blank so appears below summary section
			'dbem_bookings_header_confirm_free' => '', // blank so appears below summary section
			//Messages
			'dbem_bookings_form_msg_disabled' => __('Online bookings are not available for this event.','events-manager'),
			'dbem_bookings_form_msg_closed' => __('Bookings are closed for this event.','events-manager'),
			'dbem_bookings_form_msg_cancelled' => __('This event has been cancelled.','events-manager'). ' ' . __('Bookings are closed for this event.','events-manager'),
			'dbem_bookings_form_msg_full' => __('This event is fully booked.','events-manager'),
			'dbem_bookings_form_msg_attending'=>__('You are currently attending this event.','events-manager'),
			'dbem_bookings_form_msg_bookings_link'=>__('Manage my bookings','events-manager'),
			//messages
			'dbem_booking_warning_cancel' => __('Are you sure you want to cancel your booking?','events-manager'),
			'dbem_booking_feedback_cancelled' =>sprintf(__('Booking %s','events-manager'), __('Cancelled','events-manager')),
			'dbem_booking_feedback_pending' =>__('Booking successful, pending confirmation (you will also receive an email once confirmed).', 'events-manager'),
			'dbem_booking_feedback' => __('Booking successful.', 'events-manager'),
			'dbem_booking_feedback_full' => __('Booking cannot be made, not enough spaces available!', 'events-manager'),
			'dbem_booking_feedback_log_in' => __('You must log in or register to make a booking.','events-manager'),
			'dbem_booking_feedback_nomail' => __('However, there were some problems whilst sending confirmation emails to you and/or the event contact person. You may want to contact them directly and letting them know of this error.', 'events-manager'),
			'dbem_booking_feedback_error' => __('Booking could not be created','events-manager').':',
			'dbem_booking_feedback_email_exists' => __('This email already exists in our system, please log in to register to proceed with your booking.','events-manager'),
			'dbem_booking_feedback_new_user' => __('A new user account has been created for you. Please check your email for access details.','events-manager'),
			'dbem_booking_feedback_reg_error' => __('There was a problem creating a user account, please contact a website administrator.','events-manager'),
			'dbem_booking_feedback_already_booked' => __('You already have booked a seat at this event.','events-manager'),
			'dbem_booking_feedback_min_space' => __('You must request at least one space to book an event.','events-manager'),
			'dbem_booking_feedback_spaces_limit' => __('You cannot book more than %d spaces for this event.','events-manager'),
			//button messages
			'dbem_booking_button_msg_book' => __('Book Now', 'events-manager'),
			'dbem_booking_button_msg_booking' => __('Booking...','events-manager'),
			'dbem_booking_button_msg_booked' => sprintf(__('%s Submitted','events-manager'), __('Booking','events-manager')),
			'dbem_booking_button_msg_already_booked' => __('Already Booked','events-manager'),
			'dbem_booking_button_msg_error' => sprintf(__('%s Error. Try again?','events-manager'), __('Booking','events-manager')),
			'dbem_booking_button_msg_full' => __('Sold Out', 'events-manager'),
            'dbem_booking_button_msg_closed' => ucwords(__( 'Bookings closed', 'events-manager')), //ucwords it to prevent extra translation
			'dbem_booking_button_msg_event_cancelled' => ucwords(__('Event cancelled', 'events-manager')), //ucwords it to prevent extra translation
			'dbem_booking_button_msg_cancel' => __('Cancel', 'events-manager'),
			'dbem_booking_button_msg_canceling' => __('Canceling...','events-manager'),
			'dbem_booking_button_msg_cancelled' => __('Cancelled','events-manager'),
			'dbem_booking_button_msg_cancel_error' => sprintf(__('%s Error. Try again?','events-manager'), __('Cancellation','events-manager')),
			//Emails
			'dbem_bookings_notify_admin' => 0,
			'dbem_bookings_contact_email' => 1,
			'dbem_bookings_replyto_owner_admins' => 0,
			'dbem_bookings_replyto_owner' => 0,
			'dbem_bookings_contact_email_pending_subject' => __("Booking Pending",'events-manager'),
			'dbem_bookings_contact_email_pending_body' => str_replace("<br/>", "\n\r", $contact_person_emails['pending']),
			'dbem_bookings_contact_email_confirmed_subject' => __('Booking Confirmed','events-manager'),
			'dbem_bookings_contact_email_confirmed_body' => str_replace("<br/>", "\n\r", $contact_person_emails['confirmed']),
			'dbem_bookings_contact_email_rejected_subject' => __("Booking Rejected",'events-manager'),
			'dbem_bookings_contact_email_rejected_body' => str_replace("<br/>", "\n\r", $contact_person_emails['rejected']),
			'dbem_bookings_contact_email_cancelled_subject' => __("Booking Cancelled",'events-manager'),
			'dbem_bookings_contact_email_cancelled_body' => str_replace("<br/>", "\n\r", $contact_person_emails['cancelled']),
			'dbem_bookings_email_pending_subject' => __("Booking Pending",'events-manager'),
			'dbem_bookings_email_pending_body' => str_replace("<br/>", "\n\r", $respondent_email_pending_body_localizable),
			'dbem_bookings_email_rejected_subject' => __("Booking Rejected",'events-manager'),
			'dbem_bookings_email_rejected_body' => str_replace("<br/>", "\n\r", $respondent_email_rejected_body_localizable),
			'dbem_bookings_email_confirmed_subject' => __('Booking Confirmed','events-manager'),
			'dbem_bookings_email_confirmed_body' => str_replace("<br/>", "\n\r", $respondent_email_body_localizable),
			'dbem_bookings_email_cancelled_subject' => __('Booking Cancelled','events-manager'),
			'dbem_bookings_email_cancelled_body' => str_replace("<br/>", "\n\r", $respondent_email_cancelled_body_localizable),
			//Registration Email
			'dbem_bookings_email_registration_subject' => $booking_registration_email_subject,
			'dbem_bookings_email_registration_body' => str_replace("<br/>", "\n\r", $booking_registration_email_body),
			//Ticket Specific Options
			'dbem_bookings_tickets_ordering' => 1,
			'dbem_bookings_tickets_orderby' => 'ticket_price DESC, ticket_name ASC',
			'dbem_bookings_tickets_priority' => 0,
			'dbem_bookings_tickets_show_unavailable' => 0,
			'dbem_bookings_tickets_show_loggedout' => 1,
			'dbem_bookings_tickets_single' => 0,
			'dbem_bookings_tickets_single_form' => 0,
			//My Bookings Page
			'dbem_bookings_my_title_format' => __('My Bookings','events-manager'),
			// stats
			'dbem_booking_charts_wpdashboard' => true,
			'dbem_booking_charts_dashboard' => true,
			'dbem_booking_charts_event' => true,
		//BP Settings
		'dbem_bp_events_list_format_header' => '<ul class="em-events-list">',
		'dbem_bp_events_list_format' => '<li>#_EVENTLINK - #_EVENTDATES - #_EVENTTIMES<ul><li>#_LOCATIONLINK - #_LOCATIONADDRESS, #_LOCATIONTOWN</li></ul></li>',
		'dbem_bp_events_list_format_footer' => '</ul>',
		'dbem_bp_events_list_none_format' => '<p class="em-events-list">'.__('No Events','events-manager').'</p>',
		//custom CSS options for public pages
		'dbem_css' => 1,
		'dbem_css_theme' => 1,
		'dbem_css_theme_font_family' => 0,
		'dbem_css_theme_font_size' => 0,
		'dbem_css_theme_font_weight' => 0,
		'dbem_css_theme_line_height' => 0,
		'dbem_css_calendar' => 1,
		'dbem_css_editors' => 1,
		'dbem_css_rsvp' => 1, //my bookings page
		'dbem_css_rsvpadmin' => 1, //my event bookings page
		'dbem_css_evlist' => 1,
		'dbem_css_search' => 1,
		'dbem_css_loclist' => 1,
		'dbem_css_catlist' => 1,
		'dbem_css_taglist' => 1,
		'dbem_css_events' => 1,
		'dbem_css_locations' => 1,
		'dbem_css_categories' => 1,
		'dbem_css_tags' => 1,
		'dbem_css_myrsvp' => 1,
		/*
		 * Custom Post Options - set up to mimick old EM settings and install with minimal setup for most users
		 */
		//slugs
		'dbem_cp_events_slug' => 'events',
		'dbem_cp_locations_slug' => 'locations',
		'dbem_taxonomy_category_slug' => 'events/categories',
		'dbem_taxonomy_tag_slug' => 'events/tags',
		//event cp options
		'dbem_cp_events_template' => 'page',
		//'dbem_cp_events_template_page' => 0, DEPREICATED
		'dbem_cp_events_body_class' => '',
		'dbem_cp_events_post_class' => '',
		'dbem_cp_events_formats' => 1,
		'dbem_cp_events_has_archive' => 1,
		'dbem_events_default_archive_orderby' => '_event_start',
		'dbem_events_default_archive_order' => 'ASC',
		'dbem_events_archive_scope' => 'past',
		'dbem_cp_events_archive_formats' => 1,
	    'dbem_cp_events_excerpt_formats' => 1,
		'dbem_cp_events_search_results' => 0,
		'dbem_cp_events_custom_fields' => 0,
		'dbem_cp_events_comments' => 1,
		//location cp options
		'dbem_cp_locations_template' => '',
		//'dbem_cp_locations_template_page' => 0, DEPREICATED
		'dbem_cp_locations_body_class' => '',
		'dbem_cp_locations_post_class' => '',
		'dbem_cp_locations_formats' => 1,
		'dbem_cp_locations_has_archive' => 1,
		'dbem_locations_default_archive_orderby' => 'title',
		'dbem_locations_default_archive_order' => 'ASC',
		'dbem_cp_locations_archive_formats' => 1,
	    'dbem_cp_locations_excerpt_formats' => 1,
		'dbem_cp_locations_search_results' => 0,
		'dbem_cp_locations_custom_fields' => 0,
		'dbem_cp_locations_comments' => 1,
		//category cp options
		'dbem_cp_categories_formats' => 1,
		'dbem_categories_default_archive_orderby' => 'event_start_date,event_start_time,event_name',
		'dbem_categories_default_archive_order' => 'ASC',
		//category cp options
		'dbem_cp_tags_formats' => 1,
		'dbem_tags_default_archive_orderby' => 'event_start_date,event_start_time,event_name',
		'dbem_tags_default_archive_order' => 'ASC',
	    //optimization options
	    'dbem_disable_thumbnails'=> false,
	    //feedback reminder
	    'dbem_feedback_reminder' => time(),
	    'dbem_events_page_ajax' => 0,
	    'dbem_conditional_recursions' => 2,
        //data privacy/protection
        'dbem_data_privacy_consent_text' => esc_html__('I consent to my submitted data being collected and stored as outlined by the site %s.','events-manager'),
        'dbem_data_privacy_consent_remember' => 1,
		'dbem_data_privacy_consent_events' => 1,
		'dbem_data_privacy_consent_locations' => 1,
		'dbem_data_privacy_consent_bookings' => 1,
		'dbem_data_privacy_export_events' => 1,
		'dbem_data_privacy_export_locations' => 1,
		'dbem_data_privacy_export_bookings' => 1,
		'dbem_data_privacy_erase_events' => 1,
		'dbem_data_privacy_erase_locations' => 1,
		'dbem_data_privacy_erase_bookings' => 1,
		'dbem_advanced_formatting' => 0,
	);
	
	//do date js according to locale:
	$locale_code = substr ( get_locale (), 0, 2 );
	$locale_dates = array('nl' => 'dd/mm/yy', 'af' => 'dd/mm/yy', 'ar' => 'dd/mm/yy', 'az' => 'dd.mm.yy', 'bg' => 'dd.mm.yy', 'bs' => 'dd.mm.yy', 'cs' => 'dd.mm.yy', 'da' => 'dd-mm-yy', 'de' => 'dd.mm.yy', 'el' => 'dd/mm/yy', 'en-GB' => 'dd/mm/yy', 'eo' => 'dd/mm/yy', 'et' => 'dd.mm.yy', 'eu' => 'yy/mm/dd', 'fa' => 'yy/mm/dd', 'fo' => 'dd-mm-yy', 'fr' => 'dd.mm.yy', 'fr' => 'dd/mm/yy', 'he' => 'dd/mm/yy', 'hu' => 'yy.mm.dd.', 'hr' => 'dd.mm.yy.', 'ja' => 'yy/mm/dd', 'ro' => 'dd.mm.yy', 'sk' =>  'dd.mm.yy', 'sq' => 'dd.mm.yy', 'sr' => 'dd/mm/yy', 'sr' => 'dd/mm/yy', 'sv' => 'yy-mm-dd', 'ta' => 'dd/mm/yy', 'th' => 'dd/mm/yy', 'vi' => 'dd/mm/yy', 'zh' => 'yy/mm/dd', 'es' => 'dd/mm/yy', 'it' => 'dd/mm/yy');
	if( array_key_exists($locale_code, $locale_dates) ){
		$dbem_options['dbem_date_format_js'] = $locale_dates[$locale_code];
	}
	
	//add new options
	foreach($dbem_options as $key => $value){
		add_option($key, $value);
	}
		
	//set time localization for first time depending on current settings
	if( get_option('dbem_time_24h','not set') == 'not set'){
		//Localise vars regardless
		$locale_code = substr ( get_locale(), 0, 2 );
		if (preg_match('/^en_(?:GB|IE|AU|NZ|ZA|TT|JM)$/', get_locale())) {
		    $locale_code = 'en-GB';
		}
		//Set time
		$show24Hours = ( !preg_match("/en|sk|zh|us|uk/", $locale_code ) );	// Setting 12 hours format for those countries using it
		update_option('dbem_time_24h', $show24Hours);
	}
}

function em_upgrade_current_installation(){
	global $wpdb, $wp_locale, $EM_Notices;
	$current_version = get_option('dbem_version', 0);
	
	// add review popup
	$data = get_site_option('dbem_data', array());
	if ( !is_array($data) ) $data = array();
	
	if( empty($current_version) || !isset($data['admin-modals']) ){ // if admin-modals isn't set, it was never added before
		if( empty($data['admin-modals']) ) $data['admin-modals'] = array();
		if( !is_array($data['admin-modals']) ) $data['admin-modals'] = array();
		$data['admin-modals']['review-nudge'] = time() + (DAY_IN_SECONDS * 14);
		update_site_option('dbem_data', $data);
	}
	// temp promo
	if( time() < 1713949200 && ( version_compare($current_version, '6.4.7', '<') || !empty($data['admin-modals']['review-nudge']) )  ) {
		if( empty($data['admin-modals']) ) $data['admin-modals'] = array();
		$data['admin-modals']['promo-popup'] = true;
		update_site_option('dbem_data', $data);
	}
	
	// Check EM Pro update min
	if( defined('EMP_VERSION') && EMP_VERSION < EM_PRO_MIN_VERSION && !defined('EMP_DISABLE_WARNINGS') ) {
		$message = esc_html__('There is a newer version of Events Manager Pro which is recommended for this current version of Events Manager as new features have been added. Please go to the plugin website and download the latest update.','events-manager');
		$EM_Admin_Notice = new EM_Admin_Notice(array('name' => 'em-pro-updates', 'who' => 'admin', 'where' => 'all', 'message' => "$message"));
		EM_Admin_Notices::add($EM_Admin_Notice, is_multisite());
	}
	
	if ( !empty($current_version) ) {
		
		add_option('dbem_credits',1);
		
		if( $current_version != '' && $current_version < 5 ){
			//make events, cats and locs pages
			update_option('dbem_cp_events_template_page',1);
			update_option('dbem_cp_locations_template_page',1);
			//reset orderby, or convert fields to new fieldnames
			$EM_Event = new EM_Event();
			$orderbyvals = explode(',', get_option('dbem_events_default_orderby'));
			$orderby = array();
			foreach($orderbyvals as $val){
				if(array_key_exists('event_'.$val, $EM_Event->fields)){
					$orderby[] = 'event_'.$val;
				}
			}
			$orderby = (count($orderby) > 0) ? implode(',',$orderby): get_option('dbem_events_default_orderby');
			update_option('dbem_events_default_orderby',$orderby);
			//Locations and categories weren't controlled in v4, so just reset them
			update_option('dbem_locations_default_orderby','location_name');
			update_option('dbem_categories_default_orderby','name');
			//Update the slugs if necessary
			$events_page_id = get_option ( 'dbem_events_page' );
			$events_page = get_post($events_page_id);
			update_option('dbem_cp_events_slug', $events_page->post_name);
			update_option('dbem_taxonomy_tag_slug', $events_page->post_name.'/tags');
			if( defined('EM_LOCATIONS_SLUG') && EM_LOCATIONS_SLUG != 'locations' ) update_option('dbem_cp_locations_slug', EM_LOCATIONS_SLUG);
			if( defined('EM_CATEGORIES_SLUG') && EM_CATEGORIES_SLUG != 'categories' ) update_option('dbem_taxonomy_category_slug', $events_page->post_name.'/'.EM_CATEGORIES_SLUG);
		}
		if( $current_version != '' && $current_version < 5.19 ){
			update_option('dbem_event_reapproved_email_subject',  get_option('dbem_event_approved_email_subject'));
			update_option('dbem_event_reapproved_email_body', get_option('dbem_event_approved_email_body'));
		}
		if( $current_version != '' && $current_version <= 5.21 ){
			//just remove all rsvp cut-off info
			$wpdb->query("UPDATE ".$wpdb->postmeta." SET meta_value = NULL WHERE meta_key IN ('_event_rsvp_date','_event_rsvp_time') AND post_id IN (SELECT post_id FROM ".EM_EVENTS_TABLE." WHERE recurrence_id > 0)");
			$wpdb->query("UPDATE ".EM_EVENTS_TABLE." SET event_rsvp_time = NULL, event_rsvp_date = NULL WHERE recurrence_id > 0");
		}
		if( $current_version != '' && $current_version < 5.364 ){
			if( get_option('dbem_cp_events_template_page') ){
				update_option('dbem_cp_events_template', 'page');
				delete_option('dbem_cp_events_template_page');
			}
			if( get_option('dbem_cp_locations_template_page') ){
				update_option('dbem_cp_locations_template', 'page');
				delete_option('dbem_cp_locations_template_page');
			}
			update_option('dbem_events_archive_scope', get_option('dbem_events_page_scope'));
			update_option('em_last_modified', current_time('timestamp', true));
			update_option('dbem_category_event_single_format',get_option('dbem_category_event_list_item_header_format').get_option('dbem_category_event_list_item_format').get_option('dbem_category_event_list_item_footer_format'));
			update_option('dbem_category_no_event_message',get_option('dbem_category_event_list_item_header_format').get_option('dbem_category_no_events_message').get_option('dbem_category_event_list_item_footer_format'));
			update_option('dbem_location_event_single_format',get_option('dbem_location_event_list_item_header_format').get_option('dbem_location_event_list_item_format').get_option('dbem_location_event_list_item_footer_format'));
			update_option('dbem_location_no_event_message',get_option('dbem_location_event_list_item_header_format').get_option('dbem_location_no_events_message').get_option('dbem_location_event_list_item_footer_format'));
			update_option('dbem_tag_event_single_format',get_option('dbem_tag_event_list_item_header_format').get_option('dbem_tag_event_list_item_format').get_option('dbem_tag_event_list_item_footer_format'));
			update_option('dbem_tag_no_event_message',get_option('dbem_tag_event_list_item_header_format').get_option('dbem_tag_no_events_message').get_option('dbem_tag_event_list_item_footer_format'));
		}
		if( $current_version != '' && $current_version < 5.38 ){
			update_option('dbem_dates_separator', get_option('dbem_dates_Seperator', get_option('dbem_dates_seperator',' - ')));
			update_option('dbem_times_separator', get_option('dbem_times_Seperator', get_option('dbem_times_seperator',' - ')));
			delete_option('dbem_dates_Seperator');
			delete_option('dbem_times_Seperator');
			delete_option('dbem_dates_seperator');
			delete_option('dbem_times_seperator');
		}
		if( $current_version != '' && $current_version < 5.4 ){
			//tax rates now saved at booking level, so that alterations to tax rates don't change previous booking prices
			//any past bookings that don't get updated will adhere to these two values when calculating prices
			update_option('dbem_legacy_bookings_tax_auto_add', get_option('dbem_bookings_tax_auto_add'));
			update_option('dbem_legacy_bookings_tax', get_option('dbem_bookings_tax'));
		}
		if( $current_version != '' && $current_version < 5.422 ){
			//copy registration email content into new setting
			update_option('dbem_rss_limit',0);
		}
		if( $current_version != '' && $current_version < 5.4425 ){
			//copy registration email content into new setting
			update_option('dbem_css_editors',0);
			update_option('dbem_css_rsvp',0);
			update_option('dbem_css_evlist',0);
			update_option('dbem_css_loclist',0);
			update_option('dbem_css_rsvpadmin',0);
			update_option('dbem_css_catlist',0);
			update_option('dbem_css_taglist',0);
			if( locate_template('plugins/events-manager/templates/events-search.php') ){
				update_option('dbem_css_search', 0);
				update_option('dbem_search_form_hide_advanced',0);
			}
			update_option('dbem_events_page_search_form',get_option('dbem_events_page_search'));
			update_option('dbem_search_form_dates_separator',get_option('dbem_dates_separator'));
			delete_option('dbem_events_page_search'); //avoids the double search form on overridden templates
			update_option('dbem_locations_page_search_form',0); //upgrades shouldn't get extra surprises
		}
		if( $current_version != '' && $current_version < 5.512 ){
			update_option('dbem_search_form_geo_units',0); //don't display units search for previous installs
			//correcting the typo
			update_option('dbem_search_form_submit', get_option('dbem_serach_form_submit'));
			//if template isn't overridden, assume it is still being used
			if( !locate_template('plugins/events-manager/templates/events-search.php') ){
				delete_option('dbem_serach_form_submit', 0);
			}
			//ML translation
			if( get_option('dbem_serach_form_submit_ml') ){
				update_option('dbem_search_form_submit_ml', get_option('dbem_serach_form_submit_ml'));
				delete_option('dbem_serach_form_submit_ml'); //we can assume this isn't used in templates
			}
		}
		if( $current_version != '' && $current_version < 5.54 ){
			update_option('dbem_cp_events_excerpt_formats',0); //don't override excerpts in previous installs
			update_option('dbem_cp_locations_excerpt_formats',0);
		}
		if( $current_version != '' && $current_version < 5.55 ){
			//rename email templates sent to admins on new bookings
			update_option('dbem_bookings_contact_email_cancelled_subject',get_option('dbem_contactperson_email_cancelled_subject'));
			update_option('dbem_bookings_contact_email_cancelled_body',get_option('dbem_contactperson_email_cancelled_body'));
			if( get_option('dbem_bookings_approval') ){
				//if approvals ENABLED, we should make the old 'New Booking' email the one for a pending booking
				update_option('dbem_bookings_contact_email_pending_subject',get_option('dbem_bookings_contact_email_subject'));
				update_option('dbem_bookings_contact_email_pending_body',get_option('dbem_bookings_contact_email_body'));
			}else{
				//if approvals DISABLED, we should make the old 'New Booking' email the one for a confirmed booking
				update_option('dbem_bookings_contact_email_confirmed_subject',get_option('dbem_bookings_contact_email_subject'));
				update_option('dbem_bookings_contact_email_confirmed_body',get_option('dbem_bookings_contact_email_body'));
			}
			delete_option('dbem_contactperson_email_cancelled_subject');
			delete_option('dbem_contactperson_email_cancelled_body');
			delete_option('dbem_bookings_contact_email_subject');
			delete_option('dbem_bookings_contact_email_body');
		}
		if( $current_version != '' && $current_version < 5.62 ){
			//delete all _event_created_date and _event_date_modified records in post_meta, we don't need them anymore, they were never accurate to begin with, refer to the records in em_events table if still needed
			$wpdb->query('DELETE FROM '.$wpdb->postmeta." WHERE (meta_key='_event_date_created' OR meta_key='_event_date_modified') AND post_id IN (SELECT ID FROM ".$wpdb->posts." WHERE post_type='".EM_POST_TYPE_EVENT."' OR post_type='event-recurring')");
			$wpdb->query('ALTER TABLE '. $wpdb->prefix.'em_bookings CHANGE event_id event_id BIGINT(20) UNSIGNED NULL');
		}
		if( $current_version != '' && $current_version < 5.66 ){
			if( get_option('dbem_ical_description_format') == "#_EVENTNAME - #_LOCATIONNAME - #_EVENTDATES - #_EVENTTIMES" ) update_option('dbem_ical_description_format',"#_EVENTNAME");
			if( get_option('dbem_ical_location_format') == "#_LOCATION" ) update_option('dbem_ical_location_format', "#_LOCATIONNAME, #_LOCATIONFULLLINE, #_LOCATIONCOUNTRY");
			$old_values = array(
					'dbem_ical_description_format' => "#_EVENTNAME - #_LOCATIONNAME - #_EVENTDATES - #_EVENTTIMES",
					'dbem_ical_location_format' => "#_LOCATION",
			);
		}
		if( $current_version != '' && $current_version < 5.6636 ){
			$sql = $wpdb->prepare("DELETE FROM {$wpdb->postmeta} WHERE meta_key='_post_id' AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type=%s OR post_type=%s)", array(EM_POST_TYPE_EVENT, 'event-recurring'));
			$wpdb->query($sql);
			remove_filter('pre_option_dbem_bookings_registration_user', 'EM_People::dbem_bookings_registration_user');
			$no_user = get_option('dbem_bookings_registration_user');
			if( get_option('dbem_bookings_registration_disable') && is_numeric($no_user) ){
				if( $wpdb->update(EM_BOOKINGS_TABLE, array('person_id'=>0), array('person_id'=>$no_user), '%d', '%d') ){
					delete_option('dbem_bookings_registration_user');
				}
			}else{
				delete_option('dbem_bookings_registration_user');
			}
		}
		if( $current_version != '' && $current_version < 5.821 ){
			$admin_data = get_option('dbem_data');
			//upgrade tables only if we didn't do it before during earlier dev versions
			if( empty($admin_data['datetime_backcompat']) ){
				$migration_result = em_migrate_datetime_timezones( false );
				if( $migration_result !== true ){
					$EM_Notices->add_error($migration_result);
				}
				//migrate certain options
				$opt = get_option('dbem_tags_default_archive_orderby');
				if( $opt == '_start_ts' ) update_option('dbem_tags_default_archive_orderby', '_event_start');
				$opt = get_option('dbem_categories_default_archive_orderby');
				if( $opt == '_start_ts' ) update_option('dbem_categories_default_archive_orderby', '_event_start');
				$opt = get_option('dbem_events_default_archive_orderby');
				if( $opt == '_start_ts' ) update_option('dbem_events_default_archive_orderby', '_event_start');
			}else{
				//we're doing this at multisite level instead within dev versions, so fix this for dev versions
				unset( $admin_data['datetime_backcompat'] );
				update_option('dbem_data', $admin_data);
			}
			//add backwards compatability settings and warnings
			$admin_data = get_site_option('dbem_data');
			if( empty($admin_data['updates']) ) $admin_data['updates'] = array();
			$admin_data['updates']['timezone-backcompat'] = true;
			update_site_option('dbem_data', $admin_data);
			if( !is_multisite() || em_wp_is_super_admin() ){
				$message = __('Events Manager now supports multiple timezones for your events! Your events will initially match your blog timezone.','events-manager');
				if( is_multisite() ){
					$url = network_admin_url('admin.php?page=events-manager-options#general+admin-tools');
					$admin_tools_link = '<a href="'.$url.'">'.__('Network Admin').' &gt; '.__('Events Manager','events-manager').' &gt; '.__('Admin Tools','events-manager').'</a>';
					$options_link = '<a href="'.network_admin_url('admin.php?page=events-manager-update').'">'.__('Update Network','events-manager').'</a>';
					$message .= '</p><p>' . sprintf(__("Please update your network and when you're happy with the changes you can also finalize the migration by deleting unecessary data in the %s page.", 'events-manager'), $options_link);
					$message .= '</p><p>' . sprintf(__('You can also reset all events of a blog to a new timezone in %s', 'events-manager'), $admin_tools_link);
				}else{
					$options_link = get_admin_url(null, 'edit.php?post_type=event&page=events-manager-options#general+admin-tools');
					$options_link = '<a href="'.$options_link.'">'.__('Settings','events-manager').' &gt; '.__('General','events-manager').' &gt; '.__('Admin Tools','events-manager').'</a>';
					$message .= '</p><p>' . sprintf(__('You can reset your events to a new timezone and also complete the final migration step by deleting unecessary data in %s', 'events-manager'), $options_link);
				}
				$EM_Admin_Notice = new EM_Admin_Notice(array(
					'name' => 'v-update',
					'who' => 'admin',
					'where' => 'all',
					'message' => $message
				));
				EM_Admin_Notices::add($EM_Admin_Notice, is_multisite());
			}
		}
		if( $current_version != '' && $current_version == 5.9 && is_multisite() && !EM_MS_GLOBAL && (is_network_admin() || is_main_site()) ){
			//warning just for users who upgraded to 5.9 on multisite without global tables enabled
			$message = 'Due to a bug in 5.9 when updating to new timezones in MultiSite installations, you may notice some of your events are missing from lists.<br><br>To fix this problem, visit %s choose your timezone, select %s and click %s to update all your blogs to the desired timezone.';
			$url = network_admin_url('admin.php?page=events-manager-options#general+admin-tools');
			$admin_tools_link = '<a href="'.$url.'">'.esc_html(__('Network Admin').' &gt; '.__('Events Manager','events-manager').' &gt; '.__('Admin Tools','events-manager')).'</a>';
			$message = sprintf($message, $admin_tools_link, '<em><strong>'.esc_html__('All Blogs', 'events-manager').'</strong></em>', '<em><strong>'.esc_html__('Reset Event Timezones','events-manager').'</strong></em>');
			$EM_Admin_Notice = new EM_Admin_Notice(array(
			'name' => 'date_time_migration_5.9_multisite',
			'who' => 'admin',
			'what' => 'warning',
			'where' => 'all',
			'message' => $message
			));
			EM_Admin_Notices::add($EM_Admin_Notice, is_multisite());
		}
		if( $current_version != '' && $current_version < 5.93 ){
			$message = __('Events Manager has introduced new privacy tools to help you comply with international laws such as the GDPR, <a href="%s">see our documentation</a> for more information.','events-manager');
			$message = sprintf( $message, 'https://wp-events-plugin.com/documentation/data-privacy-gdpr-compliance/?utm_source=plugin&utm_campaign=gdpr_update');
			$EM_Admin_Notice = new EM_Admin_Notice(array( 'name' => 'v-update', 'who' => 'admin', 'where' => 'all', 'message' => $message ));
			EM_Admin_Notices::add($EM_Admin_Notice, is_multisite());
		}
		if( $current_version != '' && $current_version < 5.95 ){
			$message = esc_html__('Google has introduced new pricing for displaying maps on your site. If you have moderate traffic levels, this may likely affect you with surprise and unexpected costs!', 'events-manager');
			$message2 = esc_html__('Events Manager has implemented multiple ways to help prevent or reduce these costs drastically, please check our %s page for more information.', 'events-manager');
			$message2 = sprintf($message2, '<a href="https://wp-events-plugin.com/documentation/google-maps/api-usage/?utm_source=plugin&utm_source=medium=settings&utm_campaign=gmaps-update">'.esc_html__('documentation', 'events-manager') .'</a>');
			$EM_Admin_Notice = new EM_Admin_Notice(array( 'name' => 'v-update', 'who' => 'admin', 'where' => 'all', 'message' => "<p>$message</p><p>$message2</p>" ));
			EM_Admin_Notices::add($EM_Admin_Notice, is_multisite());
		}
		if( $current_version != '' && $current_version < 5.9618 ){
			$multisite_cond = '';
			if( EM_MS_GLOBAL ){
				if( is_main_site() ){
					$multisite_cond = ' AND (blog_id='.absint(get_current_blog_id()).' OR blog_id=0)';
				}else{
					$multisite_cond = ' AND blog_id='.absint(get_current_blog_id());
				}
			}
			$wpdb->query( $wpdb->prepare('UPDATE '.EM_EVENTS_TABLE.' SET event_language=%s WHERE event_language IS NULL'.$multisite_cond, EM_ML::$wplang) );
			$wpdb->query( $wpdb->prepare('UPDATE '.EM_LOCATIONS_TABLE.' SET location_language=%s WHERE location_language IS NULL'.$multisite_cond, EM_ML::$wplang) );
			$host = get_option('dbem_smtp_host');
			//if port is supplied via the host address, give that precedence over the port setting
			if( preg_match('/^(tls|ssl):\/\//', $host, $host_port_matches) ){
				update_option('dbem_smtp_encryption', $host_port_matches[1]);
			}else{
				update_option('dbem_smtp_encryption', 0);
			}
		}
		if( $current_version != '' && $current_version < 5.975 ){
			update_option('dbem_location_types', array('location'=>1));
			$message = esc_html__('Events Manager has introduced location types, which can include online locations such as a URL or integrations with webinar platforms such as Zoom! Enable different location types in your settings page, for more information see our %s.', 'events-manager');
			$message = sprintf( $message, '<a href="http://wp-events-plugin.com/documentation/location-types/" target="_blank">'. esc_html__('documentation', 'events-manager')).'</a>';
			$EM_Admin_Notice = new EM_Admin_Notice(array( 'name' => 'v-update', 'who' => 'admin', 'where' => 'all', 'message' => "$message" ));
			EM_Admin_Notices::add($EM_Admin_Notice, is_multisite());
		}
		if( $current_version != '' && $current_version < 5.9821 ){
			// recreate all event_parent records in post meta
			$sql = "DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_event_parent' AND post_id IN (SELECT post_id FROM ".EM_EVENTS_TABLE.")";
			$wpdb->query($sql);
			$sql = "INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) SELECT post_id, '_event_parent', event_parent FROM ".EM_EVENTS_TABLE." WHERE event_parent IS NOT NULL";
			if( EM_MS_GLOBAL ){
				// do just this blog, other blogs will update themselves when loaded
				if( is_main_site() ){
					$sql .= ' AND (blog_id='.absint(get_current_blog_id()).' OR blog_id=0)';
				}else{
					$sql .= ' AND blog_id='.absint(get_current_blog_id());
				}
			}
			$wpdb->query($sql);
		}
		// last version check in numbers, 6 onwwards uses version_compare
		if( $current_version != '' && version_compare($current_version, '6.0', '<') ){
			// convert jQuery UI DateFormat for max back-compat
			$dateformat = get_option('dbem_date_format_js');
			// check if there's any kind of non-convertable placeholders
			$non_convertables = '(o|oo|!|TICKS)';
			if( !preg_match("/$non_convertables/", $dateformat) ){
				$placeholder_convertables = array('ATOM' => 'yy-mm-dd', 'COOKIE' => 'D, dd M yy', 'ISO_8601' => 'yy-mm-dd', 'RFC_822' => 'D, d M y', 'RFC_850' => 'DD, dd-M-y', 'RFC_1036' => 'D, d M y', 'RFC_1123' => 'D, d M yy', 'RFC_2822' => 'D, d M yy', 'RSS' => 'D, d M y', 'TIMESTAMP' => '@', 'W3C' => 'yy-mm-dd',);
				foreach( $placeholder_convertables as $k => $v ){
					$dateformat = str_replace($k, $v, $dateformat);
				}
				$convertable = array( 'dd' => 'XzX' /* d */, 'd' => 'j', 'XzX' => 'd', 'DD' => 'l', 'mm' => 'ZxZ' /* m */, 'm' => 'n', 'ZxZ' => 'm', 'MM' => 'F', 'yy' => 'Y', '@' => 'U' );
				foreach( $convertable as $k => $v ){
					$dateformat = str_replace($k, $v, $dateformat);
				}
				update_option('dbem_datepicker_format', $dateformat);
			}
			update_option('dbem_search_form_tags', 0); // disable tags searching by default for previous users
			// it'd be nice to do 2 sweeps for our new templates, so we'll up the recursions to 2 so we can nest once, it'll also cover most use cases
			if( get_option('dbem_conditional_recursions') <= 1 ){
				update_option('dbem_conditional_recursions', 2);
			}
			// admin optiosn for upgrade/migration
			$admin_data = get_option('dbem_data', array());
			$admin_data['v6'] = false;
			update_option('dbem_data', $admin_data);
			// notify user of new update
			$message = esc_html__('Welcome to Events Manager v6! This latest update includes some major UI improvements, which requires you to update your formats.', 'events-manager');
			$settings_page_url = '<a href="'.admin_url('admin.php?page=events-manager-options').'">'. esc_html__('settings page', 'events-manager-google').'</a>';
			$message2 = sprintf(esc_html__('Please visit the %s to see migration options.', 'events-manager'), $settings_page_url);
			$EM_Admin_Notice = new EM_Admin_Notice(array( 'name' => 'v-update', 'who' => 'admin', 'where' => 'all', 'message' => "$message $message2" ));
			EM_Admin_Notices::add($EM_Admin_Notice, is_multisite());
		}
		if( $current_version != '' && version_compare($current_version, '6.0', '>=') && version_compare($current_version, '6.0.1.1', '<') ){
			$admin_data = get_option('dbem_data', array());
			$admin_data['v6'] = false;
			update_option('dbem_data', $admin_data);
			// notify user of new update
			$message = "<strong>We've made some changes to our template files since the 6.0 update which may break your currently saved formats. We're re-enabling preview/migration for you on the %s to have the chance to preview and reload our newly updated templates. Sorry for the inconvenience!</strong>";
			$settings_page_url = '<a href="'.admin_url('admin.php?page=events-manager-options').'">'. esc_html__('settings page', 'events-manager-google').'</a>';
			$message = sprintf($message, $settings_page_url);
			$message .= '</p><p>'."We've also added some extra features to help transition, which can be found in <code>Settings > General > Styling Options</code> and a new Default/Advanced mode in <code>Settings > Formatting</code> which will allow your formats to automatically update themselves directly from our plugin rather than settings page.";
			$EM_Admin_Notice = new EM_Admin_Notice(array( 'name' => 'v-update', 'who' => 'admin', 'where' => 'all', 'message' => $message, 'what'=>'warning' ));
			EM_Admin_Notices::add($EM_Admin_Notice, is_multisite());
		}
		if( $current_version != '' && version_compare($current_version, '6.0.1.1', '<') ){
			// enable advanced formatting for any previous users, looks like before, they can disable afterwards.
			update_option('dbem_advanced_formatting', 2);
			update_option('dbem_css_theme_font_weight', 1);
			update_option('dbem_css_theme_font_family', 1);
			update_option('dbem_css_theme_font_size', 1);
			update_option('dbem_css_theme_line_height', 1);
			update_option('dbem_dates_range_double_inputs', 1);
		}
		if( $current_version != '' && version_compare($current_version, '6.0.1.2', '<') ){
			function v6012_sql_check_error( $result, $query, $table ){
				global $wpdb;
				if( $result === false ){
					$message = "<strong>Events Manager is trying to update your database, but the following error occured:</strong>";
					$message .= '</p><p>'.'<code>'. $wpdb->last_error .'</code>';
					$message .= '</p><p>It might be that reloading this page one or more times may complete the process, if you have a large number of bookings in your database. Alternatively, you can run one of these two queries directly into your WP database:';
					$message .= '</p><p>'.'<code>'. $query .'</code>';
					$message .= '</p>OR<p>'.'<code>'. "UPDATE ". $table ." SET ticket_uuid= UUID()" .'</code>';
					$EM_Admin_Notice = new EM_Admin_Notice(array( 'name' => 'v6.1-'.$table.'atomic-error', 'who' => 'admin', 'where' => 'all', 'message' => $message, 'what'=>'warning' ));
					EM_Admin_Notices::add($EM_Admin_Notice, is_multisite());
					global $em_do_not_finalize_upgrade;
					$em_do_not_finalize_upgrade = true;
				}else{
					EM_Admin_Notices::remove('v6.1-'.$table.'atomic-error', is_multisite());
				}
			}
			// slated for 6.1 - atomic tickets - tweaked for mariadb < 10.0 compatiability
			$query = "UPDATE ". EM_TICKETS_BOOKINGS_TABLE ." SET ticket_uuid= MD5(RAND())";
			//$query = "UPDATE ". EM_TICKETS_BOOKINGS_TABLE ." SET ticket_uuid= LOWER(CONCAT( HEX(RANDOM_BYTES(4)), '', HEX(RANDOM_BYTES(2)), '4', SUBSTR(HEX(RANDOM_BYTES(2)), 2, 3), '', HEX(FLOOR(ASCII(RANDOM_BYTES(1)) / 64) + 8), SUBSTR(HEX(RANDOM_BYTES(2)), 2, 3), '', hex(RANDOM_BYTES(6)) ))";
			$result = $wpdb->query($query. " WHERE ticket_uuid=''");
			if( $result !== false ) {
				// check for duplicates, md5 has much more chnace of collision
				$duplicate_check = 'SELECT ticket_booking_id FROM ' . EM_TICKETS_BOOKINGS_TABLE . ' GROUP BY ticket_uuid HAVING COUNT(ticket_uuid) > 1 LIMIT 1';
				while( $result !== false && $wpdb->get_var($duplicate_check) !== null ) {
					$query_recheck = 'UPDATE ' . EM_TICKETS_BOOKINGS_TABLE . ' SET ticket_uuid= MD5(RAND()) WHERE ticket_uuid IN (
					    SELECT ticket_uuid FROM (SELECT ticket_uuid FROM ' . EM_TICKETS_BOOKINGS_TABLE . ' GROUP BY ticket_uuid HAVING COUNT(ticket_uuid) > 1) t2
					)';
					$result = $wpdb->query($query_recheck);
				}
			}
			v6012_sql_check_error($result, $query, EM_TICKETS_BOOKINGS_TABLE);
			// do the same for regular bookings, allowing for unique IDs that can be used by guest users to access (future feature)
			$query = "UPDATE ". EM_BOOKINGS_TABLE ." SET booking_uuid= MD5(RAND())";
			//$query = "UPDATE ". EM_BOOKINGS_TABLE ." SET booking_uuid= LOWER(CONCAT( HEX(RANDOM_BYTES(4)), '', HEX(RANDOM_BYTES(2)), '4', SUBSTR(HEX(RANDOM_BYTES(2)), 2, 3), '', HEX(FLOOR(ASCII(RANDOM_BYTES(1)) / 64) + 8), SUBSTR(HEX(RANDOM_BYTES(2)), 2, 3), '', hex(RANDOM_BYTES(6)) ))";
			$result = $wpdb->query( $query . " WHERE booking_uuid=''" );
			if( $result !== false ) {
				// check for duplicates, md5 has much more chnace of collision
				$duplicate_check = 'SELECT booking_id FROM ' . EM_BOOKINGS_TABLE . ' GROUP BY booking_uuid HAVING COUNT(booking_uuid) > 1 LIMIT 1';
				while( $result !== false && $wpdb->get_var($duplicate_check) !== null ) {
					$query_recheck = 'UPDATE ' . EM_BOOKINGS_TABLE . ' SET booking_uuid= MD5(RAND()) WHERE booking_uuid IN (
					    SELECT booking_uuid FROM (SELECT booking_uuid FROM ' . EM_BOOKINGS_TABLE . ' GROUP BY booking_uuid HAVING COUNT(booking_uuid) > 1) t2
					)';
					$result = $wpdb->query($query_recheck);
				}
			}
			v6012_sql_check_error($result, $query, EM_BOOKINGS_TABLE);
			// Now go through current bookings and split the tickets up, 100 at a time
			$query = 'SELECT ticket_booking_id, ticket_id, booking_id, ticket_booking_spaces, ticket_booking_price FROM '.EM_TICKETS_BOOKINGS_TABLE .' WHERE ticket_booking_spaces > 1 LIMIT 100';
			$results = $wpdb->get_results( $query, ARRAY_A );
			while( !empty($results) ){
				$tickets_to_delete = array();
				foreach( $results as $ticket_booking ) {
					// first check that we maybe didn't die halfway through this and there aren't others with the same ticket/bookingid combo by simply deleting these
					$wpdb->query('DELETE FROM '. EM_TICKETS_BOOKINGS_TABLE .' WHERE booking_id='. $ticket_booking['booking_id'] .' AND ticket_id='. $ticket_booking['ticket_id'] .' AND ticket_booking_id !='. $ticket_booking['ticket_booking_id'] .' AND ticket_booking_spaces = 1');
					// now we generate split tickets, one space per ticket
					$split_tickets = array();
					$split_price = round($ticket_booking['ticket_booking_price'] / $ticket_booking['ticket_booking_spaces'], 4);
					for( $i = 1; $i <= $ticket_booking['ticket_booking_spaces']; $i++ ){
						$uuid = str_replace('-', '', wp_generate_uuid4());
						$split_tickets[] = "('{$uuid}', '{$ticket_booking['ticket_id']}', '{$ticket_booking['booking_id']}', $split_price , 1)";
					}
					// insert the new split tickets and delete the old one, rinse and repeat
					$wpdb->query('INSERT INTO '. EM_TICKETS_BOOKINGS_TABLE . ' (ticket_uuid, ticket_id, booking_id, ticket_booking_price, ticket_booking_spaces) VALUES '. implode(',', $split_tickets) );
					$wpdb->query('DELETE FROM '. EM_TICKETS_BOOKINGS_TABLE . " WHERE ticket_booking_id='{$ticket_booking['ticket_booking_id']}'");
				}
				$results = $wpdb->get_results( $query, ARRAY_A );
			}
		}
		// some users experienced issues with the following updates, so we'll do a check for the booking_meta_migrated field and see if everything went through...
		$cols = $wpdb->get_row('SELECT * FROM '. EM_BOOKINGS_TABLE . ' LIMIT 1', ARRAY_A);
		if( is_array($cols) && array_key_exists('booking_meta_migrated', $cols) ) {
			$query = 'SELECT count(*) FROM ' . EM_BOOKINGS_TABLE . " WHERE booking_meta_migrated IS NULL";
			$migrated_count = $wpdb->get_var($query);
			if ($migrated_count !== null && $migrated_count > 0) {
				// we didn't fully migrate, so we need to go back and re-migrate the missing data
				$current_version = '6.1';
				update_option('dbem_version', $current_version);
				if( get_option('em_pro_version') ){
					update_option('em_pro_version', '2.9999'); // retrigger EM Pro after as well
				}
			}
		}
		
		if( $current_version != '' && version_compare($current_version, '6.1.0.1', '<') ){
			$cols = $wpdb->get_row('SELECT * FROM '. EM_BOOKINGS_TABLE . ' LIMIT 1', ARRAY_A);
			if( is_array($cols) && !array_key_exists('booking_meta_migrated', $cols) ) {
				$result = $wpdb->query('ALTER TABLE ' . EM_BOOKINGS_TABLE . ' ADD `booking_meta_migrated` INT(1) NULL');
				if( $result === false ){
					$message = "<strong>Events Manager is trying to update your database, but the following error occured whilst trying to create a new field in the ".EM_BOOKINGS_TABLE." table:</strong>";
					$message .= '</p><p>'.'<code>'. $wpdb->last_error .'</code>';
					$message .= '</p><p>This may likely need some sort of intervention, please get in touch with our support for more advice, we are sorry for the inconveneince.';
					$EM_Admin_Notice = new EM_Admin_Notice(array( 'name' => 'v6.1-booking-atomic-meta-error', 'who' => 'admin', 'where' => 'all', 'message' => $message, 'what'=>'warning' ));
					EM_Admin_Notices::add($EM_Admin_Notice, is_multisite());
					global $em_do_not_finalize_upgrade;
					$em_do_not_finalize_upgrade = true;
				}
			}
			// atomic booking meta! for 6.1
			// let's go through every booking and split it all up
			$query = 'SELECT booking_id, booking_meta FROM '. EM_BOOKINGS_TABLE ." WHERE booking_meta_migrated IS NULL LIMIT 500";
			$results = $wpdb->get_results( $query, ARRAY_A );
			$timeout = ini_get('max_execution_time') >= 30 ? ini_get('max_execution_time') : 30;
			while( !empty($results) ){
				set_time_limit($timeout); // reset the timer, let it run for 30 if we keep hitting the loop, if it gets stuck inside this then let it time out
				$migrated_bookings = $booking_meta_split = array();
				foreach( $results as $booking ) {
					// now we generate split meta, any meta in an array should be dealt with by corresponding plugin (e.g. Pro for form field meta)
					if( !empty($booking['booking_meta']) ) {
						$booking_meta = unserialize($booking['booking_meta']);
						foreach( $booking_meta as $k => $v ){
							if( is_array($v) ) {
								// we go down one level for automated array combining
								$prefix = '_'.$k.'_';
								foreach( $v as $kk => $vv ){
									$kk = $prefix . $kk;
									if( is_array($vv) ) $vv = serialize($vv);
									// handle emojis - copied check from wpdb
									if ( (function_exists( 'mb_check_encoding' ) && !mb_check_encoding( $vv, 'ASCII' )) || preg_match( '/[^\x00-\x7F]/', $vv ) ) {
										$vv = wp_encode_emoji($vv);
									}
									$booking_meta_split[] = $wpdb->prepare("({$booking['booking_id']}, %s, %s)", $kk, $vv);
								}
							}else{
								// handle emojis - copied check from wpdb
								if ( (function_exists( 'mb_check_encoding' ) && !mb_check_encoding( $v, 'ASCII' )) || preg_match( '/[^\x00-\x7F]/', $v ) ) {
									$v = wp_encode_emoji($v);
								}
								$booking_meta_split[] = $wpdb->prepare("({$booking['booking_id']}, %s, %s)", $k, $v);
							}
						}
						// insert the new split tickets and delete the old one, rinse and repeat
					}
					// finally update the booking again so we know it was migrated
					$migrated_bookings[] = absint($booking['booking_id']);
				}
				// first check that we maybe didn't die halfway through this and there aren't others with the same ticket/bookingid combo by simply deleting these
				$wpdb->query('DELETE FROM '. EM_BOOKINGS_META_TABLE .' WHERE booking_id IN ('. implode(',', $migrated_bookings).')');
				// now add the batch
				$result = $wpdb->query('INSERT INTO '. EM_BOOKINGS_META_TABLE . ' (booking_id, meta_key, meta_value) VALUES '. implode(',', $booking_meta_split) );
				if( $result === false ){
					$message = "<strong>Events Manager is trying to update your database, but the following error occured whilst copying booking meta to the new ".EM_BOOKINGS_META_TABLE." table:</strong>";
					$message .= '</p><p>'.'<code>'. $wpdb->last_error .'</code>';
					$message .= '</p><p>This may likely need some sort of intervention, please get in touch with our support for more advice, we are sorry for the inconveneince.';
					$EM_Admin_Notice = new EM_Admin_Notice(array( 'name' => 'v6.1-booking-atomic-meta-error', 'who' => 'admin', 'where' => 'all', 'message' => $message, 'what'=>'warning' ));
					EM_Admin_Notices::add($EM_Admin_Notice, is_multisite());
					global $em_do_not_finalize_upgrade;
					$em_do_not_finalize_upgrade = true;
					break;
				} else {
					$result = $wpdb->query('UPDATE '. EM_BOOKINGS_TABLE . ' SET booking_meta_migrated=1 WHERE booking_id IN ('. implode(',', $migrated_bookings).')');
					if( $result === false ){
						$message = "<strong>Events Manager is trying to update your database, but the following error occured whilst migrating to the new ".EM_BOOKINGS_META_TABLE." table:</strong>";
						$message .= '</p><p>'.'<code>'. $wpdb->last_error .'</code>';
						$message .= '</p><p>This may likely need some sort of intervention, please get in touch with our support for more advice, we are sorry for the inconveneince.';
						$EM_Admin_Notice = new EM_Admin_Notice(array( 'name' => 'v6.1-booking-atomic-meta-error', 'who' => 'admin', 'where' => 'all', 'message' => $message, 'what'=>'warning' ));
						EM_Admin_Notices::add($EM_Admin_Notice, is_multisite());
						global $em_do_not_finalize_upgrade;
						$em_do_not_finalize_upgrade = true;
						break;
					} else {
						$results = $wpdb->get_results($query, ARRAY_A);
					}
				}
			}
			$wpdb->query('ALTER TABLE '. EM_BOOKINGS_TABLE . ' DROP `booking_meta_migrated`'); // flag done
			EM_Admin_Notices::remove('v6.1-booking-atomic-meta-error', is_multisite());
			EM_Admin_Notices::remove('v6.1-atomic-error', is_multisite());
			update_option('dbem_version', '6.1.0.2'); // we don't need to make this happen again
		}
		if( $current_version != '' && version_compare($current_version, '6.1.1', '<') ){
			EM_Admin_Notices::remove('v6.1-atomic-error', is_multisite());
		}
		
		
		if( $current_version != '' && version_compare($current_version, '6.1.1.4', '<') ){
			global $em_do_not_finalize_upgrade;
			// we're going to fix a potential duplicate data issue that emerged in a recent update, cause unknown, fix know as below...ç
			$sql_part = '
					 FROM '. EM_BOOKINGS_META_TABLE .' AS t1
					INNER JOIN '. EM_BOOKINGS_META_TABLE .' AS t2
					WHERE t1.meta_id > t2.meta_id AND t2.booking_id = t1.booking_id AND t2.meta_key = t1.meta_key AND t2.meta_value = t1.meta_value
				';
			$duplicate_check = $wpdb->query('SELECT * '.$sql_part);
			if( $duplicate_check !== false && $wpdb->num_rows > 0 ) {
				// we have a problem...
				$em_do_not_finalize_upgrade = true;
				// first see if there's even a problem
				$table_copy = 'wp_em_bookings_meta_copy';
				$create_result = $wpdb->query('CREATE TABLE '.$table_copy.' LIKE ' . EM_BOOKINGS_META_TABLE);
				if ($create_result === false) {
					// try once more in case mid-dev updates were attempted between EM v6.1.1.2 and v6.1.2
					$table_copy = $table_copy . '_2';
					$create_result = $wpdb->query('CREATE TABLE '.$table_copy.' LIKE ' . EM_BOOKINGS_META_TABLE);
				}
				if ($create_result !== false) {
					// copy all the data to the duplicate table
					$copy_result = $wpdb->query('INSERT INTO '.$table_copy.' SELECT * FROM ' . EM_BOOKINGS_META_TABLE);
					if ($copy_result) {
						// verify the number of rows match the original table
						$original_count = $wpdb->get_var('SELECT count(*) FROM ' . EM_BOOKINGS_META_TABLE);
						$copy_count = $wpdb->get_var('SELECT count(*) FROM '.$table_copy.'');
						if ( $copy_count !== null && $original_count !== null && $copy_count === $original_count) {
							$deletion_result = $wpdb->query('DELETE t1 ' . $sql_part);
							if( $deletion_result !== false ){
								$em_do_not_finalize_upgrade = false;
								// all done! just warn the user about the deletion and the copied table, just in case
								$message = sprintf('You have successfully updated and migrated to Events Manager %s', EM_VERSION);
								EM_Admin_Notices::add(new EM_Admin_Notice(array( 'name' => 'v6.1.2-duplicate-update', 'who' => 'admin', 'where' => 'settings', 'message' => $message.$message2 )), is_multisite());
								EM_Admin_Notices::remove('v6.1.2-update-error');
							}else{
								$message = 'There was an error upgrading your database. We could not delete redundant data from <code>'.EM_BOOKINGS_META_TABLE.'</code> for the Events Manager v6.1.2 upgrade. Please contact Events Manager Pro support for further assistance and provide this entire error. MySQL error: <code>'. $wpdb->last_error .'</code>';
								EM_Admin_Notices::add(new EM_Admin_Notice(array( 'name' => 'v6.1.2-update-error', 'who' => 'admin', 'where' => 'settings', 'message' => $message )), is_multisite());
							}
						}else{
							$message = 'There was an error upgrading your database. We could not copy backup data from <code>'.EM_BOOKINGS_META_TABLE.'</code> for the Events Manager v6.1.2 upgrade, origin/destination quantities do not match and we will not proceed. Please contact Events Manager Pro support for further assistance and provide this entire error.';
							EM_Admin_Notices::add(new EM_Admin_Notice(array( 'name' => 'v6.1.2-update-error', 'who' => 'admin', 'where' => 'settings', 'message' => $message )), is_multisite());
						}
					}else{
						$message = 'There was an error upgrading your database. We could not copy backup data from <code>'.EM_BOOKINGS_META_TABLE.'</code> for the Events Manager v6.1.2 upgrade. Please contact Events Manager Pro support for further assistance and provide this entire error. MySQL error: <code>'. $wpdb->last_error .'</code>';
						EM_Admin_Notices::add(new EM_Admin_Notice(array( 'name' => 'v6.1.2-update-error', 'who' => 'admin', 'where' => 'settings', 'message' => $message )), is_multisite());
					}
				}else{
					$message = 'There was an error upgrading your database. We could not create a table copy of <code>'.EM_BOOKINGS_META_TABLE.'</code> for the Events Manager v6.1.2 upgrade. Please contact Events Manager Pro support for further assistance and provide this entire error. MySQL error: <code>'. $wpdb->last_error .'</code>';
					EM_Admin_Notices::add(new EM_Admin_Notice(array( 'name' => 'v6.1.2-update-error', 'who' => 'admin', 'where' => 'settings', 'message' => $message )), is_multisite());
				}
			}elseif( $duplicate_check === false ){
				$em_do_not_finalize_upgrade = true;
				$message = 'There was an error upgrading your database. We could not check the integrity of <code>'.EM_BOOKINGS_META_TABLE.'</code> to ensure updates were successful. Please contact Events Manager Pro support for further assistance.';
				EM_Admin_Notices::add(new EM_Admin_Notice(array( 'name' => 'v6.1.2-update-error', 'who' => 'admin', 'where' => 'settings', 'message' => $message )), is_multisite());
			}else{
				$message = sprintf(esc_html__('You have successfully updated to Events Manager %s', 'events-manager'), EM_VERSION);
				EM_Admin_Notices::add(new EM_Admin_Notice(array( 'name' => 'v6.1.2-update', 'who' => 'admin', 'where' => 'settings', 'message' => $message )), is_multisite());
			}
		}
		
		if( $current_version != '' && version_compare($current_version, '6.2.2', '<') ){
			if( !get_option('dbem_css') ){
				// prevent grids just to avoid styling snafus
				update_option('dbem_search_form_views', array('list', 'list-grouped', 'map', 'calendar'));
			}
			// groups now are part of view settings for a list type
			if( !get_option('dbem_event_list_groupby')){
				update_option('dbem_event_list_groupby', 'monthly');
			}else{
				update_option('dbem_search_form_view', 'list-grouped');
			}
		}
		
		// do pro retrofit of bookings that previously had a uuid in booking meta, make that the uuid of the booking itself so we can transition out of uuid in booking meta
		if( version_compare($current_version, '6.3.0.5', '<') ){
			// get all bookings with a uuid meta, leave that meta for backcompat old code but move the same uuid into the booking and overwrite
			$uuid_sql = 'UPDATE ' . EM_BOOKINGS_TABLE . ' b INNER JOIN ' . EM_BOOKINGS_META_TABLE . " bm ON b.booking_id=bm.booking_id AND meta_key='uuid' SET booking_uuid=REPLACE(meta_value,'-','') WHERE meta_value IS NOT NULL AND REPLACE(meta_value,'-','') != booking_uuid";
			$wpdb->query($uuid_sql);
			// add new advanced search option values to default to main/previous settings for user
			$search_advanced = array(
				'dbem_search_form_text_advanced' => get_option('dbem_search_form_text'),
				'dbem_search_form_text_label_advanced' => get_option('dbem_search_form_text_label'),
				'dbem_search_form_geo_advanced' => get_option('dbem_search_form_geo'),
				'dbem_search_form_geo_label_advanced' => get_option('dbem_search_form_geo_label'),
				'dbem_search_form_dates_advanced' => get_option('dbem_search_form_dates'),
				'dbem_search_form_dates_label_advanced' => get_option('dbem_search_form_dates_label'),
				'dbem_search_form_dates_separator_advanced' => get_option('dbem_search_form_dates_separator'),
				'dbem_search_form_dates_format_advanced' => get_option('dbem_search_form_dates_format'),
			);
			foreach ( $search_advanced as $k => $v ){
				update_option($k, $v);
			}
		}
		if( version_compare( $current_version, '6.4', '<') ){
			// new 'pay' button will by default be worded same as free button
			update_option('dbem_bookings_submit_button_paid', get_option('dbem_bookings_submit_button') );
			// update message
			$settings_page_url = '<a href="'.admin_url('admin.php?page=events-manager-options').'">Settings > Bookings > Booking Form Options</a>';
			$message = 'Welcome to Events Manager 6.4! This version introduces some new booking form upgrades, including a dynamic price breakdown before confirming a booking! Enable this feature in <a href=""><em>'. $settings_page_url .'</em></a>';
			EM_Admin_Notices::add(new EM_Admin_Notice(array( 'name' => 'v-update', 'who' => 'admin', 'where' => 'plugin', 'message' => $message )), is_multisite());
		}
		if( version_compare( $current_version, '6.4.2', '<') ){
			// update message
			$settings_page_url = '<a href="'.admin_url('admin.php?page=events-manager-options#general+general').'">'. esc_html__('Settings', 'events-manager').' > '. esc_html__('General','events-manager') .' > '. esc_html__('General Options', 'events-manager') . '</a>' . ' > ' . esc_html__('Enable Event Status?', 'events-manager');
			$message = 'Welcome to Events Manager 6.4.2! This version introduces event status options including cancellation of an event! Enable this feature in <a href=""><em>'. $settings_page_url .'</em></a>';
			EM_Admin_Notices::add(new EM_Admin_Notice(array( 'name' => 'v-update', 'who' => 'admin', 'where' => 'plugin', 'message' => $message )), is_multisite());
		}
		if( version_compare( $current_version, '6.4.7.2', '<') ){
			// remove flag for admin notice
			delete_option('dbem_hello_to_user');
		}
	}
}

function em_set_mass_caps( $roles, $caps ){
	global $wp_roles;
	foreach( $roles as $user_role ){
		foreach($caps as $cap){
			$wp_roles->add_cap($user_role, $cap);
		}
	}
}

function em_set_capabilities(){
	//Get default roles
	global $wp_roles;
	if( get_option('dbem_version') == '' ){
		//Assign caps in groups, as we go down, permissions are "looser"
		$caps = array(
			/* Event Capabilities */
			'publish_events', 'delete_others_events', 'edit_others_events', 'manage_others_bookings',
			/* Recurring Event Capabilties */
			'publish_recurring_events', 'delete_others_recurring_events', 'edit_others_recurring_events',
			/* Location Capabilities */
			'publish_locations', 'delete_others_locations',	'delete_locations', 'edit_others_locations',
			/* Category Capabilities */
			'delete_event_categories', 'edit_event_categories'
		);
		em_set_mass_caps( array('administrator','editor'), $caps );

		//Add all the open caps
		$loose_caps = array(
			'manage_bookings', 'upload_event_images',
			/* Event Capabilities */
			'delete_events', 'edit_events', 'read_private_events',
			/* Recurring Event Capabilties */
			'delete_recurring_events', 'edit_recurring_events',
			/* Location Capabilities */
			'edit_locations', 'read_private_locations', 'read_others_locations',
		);
		em_set_mass_caps( array('administrator','editor','contributor','author'), $loose_caps);
		
		//subscribers can read private stuff, nothing else
		$wp_roles->add_cap('subscriber', 'read_private_locations');
		$wp_roles->add_cap('subscriber', 'read_private_events');
	}
	if( get_option('dbem_version')  && get_option('dbem_version') < 5 ){
		//Add new caps that are similar to old ones
		$conditional_caps = array(
			'publish_events' => 'publish_locations,publish_recurring_events',
			'edit_others_events' => 'edit_others_recurring_events',
			'delete_others_events' => 'delete_others_recurring_events',
			'edit_categories' => 'edit_event_categories,delete_event_categories',
			'edit_recurrences' => 'edit_recurring_events,delete_recurring_events',
			'edit_events' => 'upload_event_images'
		);
		$default_caps = array( 'read_private_events', 'read_private_locations' );
		foreach($conditional_caps as $cond_cap => $new_caps){
			foreach( $wp_roles->role_objects as $role_name => $role ){
				if($role->has_cap($cond_cap)){
					foreach(explode(',', $new_caps) as $new_cap){
						$role->add_cap($new_cap);
					}
				}
			}
		}
		em_set_mass_caps( array('administrator','editor','contributor','author','subscriber'), $default_caps);
	}
}

function em_create_events_page(){
	global $wpdb,$current_user;
	$event_page_id = get_option('dbem_events_page');
	if( empty($event_page_id) ){
		$post_data = array(
			'post_status' => 'publish',
			'post_type' => 'page',
			'ping_status' => get_option('default_ping_status'),
			'post_content' => 'CONTENTS',
			'post_excerpt' => 'CONTENTS',
			'post_title' => __('Events','events-manager')
		);
		$post_id = wp_insert_post($post_data, false);
	   	if( $post_id > 0 ){
	   		update_option('dbem_events_page', $post_id);
	   		//Now Locations Page
	   		$post_data = array(
				'post_status' => 'publish',
	   			'post_parent' => $post_id,
				'post_type' => 'page',
				'ping_status' => get_option('default_ping_status'),
				'post_content' => 'CONTENTS',
				'post_excerpt' => '',
				'post_title' => __('Locations','events-manager')
			);
			$loc_id = wp_insert_post($post_data, false);
	   		update_option('dbem_locations_page', $loc_id);
	   		//Now Categories Page
	   		$post_data = array(
				'post_status' => 'publish',
	   			'post_parent' => $post_id,
				'post_type' => 'page',
				'ping_status' => get_option('default_ping_status'),
				'post_content' => 'CONTENTS',
				'post_excerpt' => '',
				'post_title' => __('Categories','events-manager')
			);
			$cat_id = wp_insert_post($post_data, false);
	   		update_option('dbem_categories_page', $cat_id);
	   		//Now Tags Page
	   		$post_data = array(
				'post_status' => 'publish',
	   			'post_parent' => $post_id,
				'post_type' => 'page',
				'ping_status' => get_option('default_ping_status'),
				'post_content' => 'CONTENTS',
				'post_excerpt' => '',
				'post_title' => __('Tags','events-manager')
			);
			$tag_id = wp_insert_post($post_data, false);
	   		update_option('dbem_tags_page', $tag_id);
		   	//Now Bookings Page
		   	$post_data = array(
				'post_status' => 'publish',
		   		'post_parent' => $post_id,
				'post_type' => 'page',
				'ping_status' => get_option('default_ping_status'),
				'post_content' => 'CONTENTS',
				'post_excerpt' => '',
				'post_title' => __('My Bookings','events-manager'),
		   		'post_slug' => 'my-bookings'
			);
			$bookings_post_id = wp_insert_post($post_data, false);
	   		update_option('dbem_my_bookings_page', $bookings_post_id);
	   	}
	}
}

function em_migrate_datetime_timezones( $reset_new_fields = true, $migrate_date_fields = true, $timezone = false ){
	global $wpdb;
	//Table names
	$db = EM_MS_GLOBAL ? $wpdb->base_prefix : $wpdb->prefix;
	//create AND and WHERE conditions for blog IDs if we're in Multisite Glboal Mode
	$blog_id_where = $blog_id_and = '';
	if( EM_MS_GLOBAL ){
		if( is_main_site() ){
			$blog_id_cond = $wpdb->prepare('(blog_id = %d OR blog_id IS NULL OR blog_id = 0)', get_current_blog_id());
		}else{
			$blog_id_cond = $wpdb->prepare('blog_id = %d', get_current_blog_id());
		}
		$blog_id_where = ' WHERE '.$blog_id_cond;
		$blog_id_and = ' AND '.$blog_id_cond;
	}
	//reset all the data for these purposes
	if( $reset_new_fields || $migrate_date_fields ) $wpdb->query('UPDATE '. $db.'em_events' .' SET event_start = NULL, event_end = NULL, event_timezone = NULL'.$blog_id_where);
	if( !$migrate_date_fields ) return true;
	
	//start migration of old date formats to new datetime formats in local and UTC mode along with a declared timezone
	$migration_results = $migration_meta_results = $migration_errors = array();
	//firstly, we do a query for all-day events and reset the times, so that UTC times are correct relative to the local time
	$migration_result = $wpdb->query('UPDATE '. $db.'em_events'." SET event_start_time = '00:00:00', event_end_time = '23:59:59' WHERE event_all_day = 1".$blog_id_and);
	if( $migration_result === false ) $migration_errors[] = array('Local datetime allday event times modification errors', $wpdb->last_error);
	
	//migration procedure depends on whether we have an actual timezone or just a manual offset of hours in the WP settings page
	if( empty($timezone) ){
		$timezone = get_option('timezone_string');
		if( empty($timezone) ){ 
			$timezone = get_option('gmt_offset');
			$timezone = preg_match('/[+\-]/', $timezone) ? 'UTC'.$timezone : 'UTC+'.$timezone;
		}
	}
	if( !preg_match('/^UTC/', $timezone) ){
		//we'll get the minimum start/end dates in our events, and get the transitions for this range
		$transitions = em_migrate_get_tz_transitions($timezone, $blog_id_where);
		//now, build the SQL statements with the transitions
		$query_data = array();
		$where_start = $where_end = array();
		//go through each transition and add it to the right offset array key
		foreach( $transitions as $t ){
			//format start/end transitions for mysql DATETIME format IN CORRECT TIMEZONE
			$start = $t['start'] ? date('Y-m-d H:i:s', $t['start'] + $t['offset']) : false;
			$end = $t['end'] ? date('Y-m-d H:i:s', $t['end'] + $t['offset']) : false;
			//set up SQL statement for offset if it doesn't exist, but without the WHERE clause, which we'll add later
			if( empty($query_data[$t['offset']]) ){
				$query_data[$t['offset']] = array(
						'start' => array(
								'sql' => $wpdb->prepare('UPDATE '. $db.'em_events'. ' SET event_start = DATE_SUB(TIMESTAMP(event_start_date,event_start_time), INTERVAL %d SECOND)', $t['offset']),
								'where' => array()
						),
						'end' => array(
								'sql' => $wpdb->prepare('UPDATE '. $db.'em_events'. ' SET event_end = DATE_SUB(TIMESTAMP(event_end_date, event_end_time), INTERVAL %d SECOND)', $t['offset']),
								'where' => array()
						)
				);
			}
			//create array of conditions, which we'll join into single statements for each unique offset amount
			if( $start && $end ){
				$query_data[$t['offset']]['start']['where'][] = $wpdb->prepare("(TIMESTAMP(event_start_date,event_start_time) BETWEEN %s AND %s)", $start, $end);
				$query_data[$t['offset']]['end']['where'][] = $wpdb->prepare("(TIMESTAMP(event_end_date, event_end_time) BETWEEN %s AND %s)", $start, $end);
			}elseif( $start ){
				$query_data[$t['offset']]['start']['where'][] = $wpdb->prepare("(TIMESTAMP(event_start_date,event_start_time) > %s)", $start);
				$query_data[$t['offset']]['end']['where'][] = $wpdb->prepare("(TIMESTAMP(event_end_date, event_end_time) > %s)", $start);
			}elseif( $end ){
				$query_data[$t['offset']]['start']['where'][] = $wpdb->prepare("(TIMESTAMP(event_start_date,event_start_time) < %s)", $end);
				$query_data[$t['offset']]['end']['where'][] = $wpdb->prepare("(TIMESTAMP(event_end_date, event_end_time) < %s)", $end);
			}
		}
		//glue the where clauses together with SQLs and create the minimum required statements to run this update
		$sql_array = array();
		foreach( $query_data as $offset => $statements ){
			$migration_result = $wpdb->query($statements['start']['sql'] .' WHERE event_start IS NULL AND ('. implode(' OR ', $statements['start']['where']).')'.$blog_id_and);
			if( $migration_result === false ) $migration_errors[] = array('Event start UTC transition',$wpdb->last_error);
			$migration_result = $wpdb->query($statements['end']['sql'] ." WHERE event_end IS NULL AND (". implode(' OR ', $statements['end']['where']).')'.$blog_id_and);
			if( $migration_result === false ) $migration_errors[] = array('Event end UTC transation', $wpdb->last_error);
		}
	}else{
		//This gets very easy... just do a single query that copies over all the times to right columns with relevant offset
		$EM_DateTimeZone = EM_DateTimeZone::create($timezone);
		$offset = $timezone == 'UTC' ? 0 : $EM_DateTimeZone->getOffset( new EM_DateTime() ) / MINUTE_IN_SECONDS;
		$timezone = $EM_DateTimeZone->getName();
		$migration_result = $wpdb->query($wpdb->prepare('UPDATE '. $db.'em_events'. ' SET event_start = DATE_SUB(TIMESTAMP(event_start_date,event_start_time), INTERVAL %d MINUTE), event_end = DATE_SUB(TIMESTAMP(event_end_date, event_end_time), INTERVAL %d MINUTE) WHERE event_end IS NULL '.$blog_id_and, $offset, $offset));
		if( $migration_result === false ) $migration_errors[] = array('Event start/end UTC offset', $wpdb->last_error);
	}
	
	//set the timezone (on initial migration all events have same timezone of blog)
	$migration_result = $wpdb->query($wpdb->prepare('UPDATE '. $db.'em_events' .' SET event_timezone = %s WHERE event_timezone IS NULL'.$blog_id_and, $timezone));
	if( $migration_result === false ) $migration_errors[] = array('Event timezone setting', $wpdb->last_error);
	
	//reave meta data - at this point once we've copied over all of the dates, so we do 5 queries to postmeta, one for each field we've created above start/end times in local/utc and timezone
	if( empty($migration_errors) ){
		//delete all previously added fields, in case they were added before
		$sql = 'DELETE FROM '.$wpdb->postmeta." WHERE meta_key IN ('_event_start','_event_end','_event_timezone', '_event_start_local', '_event_end_local') AND post_id IN (SELECT ID FROM ".$wpdb->posts." WHERE post_type='".EM_POST_TYPE_EVENT."' OR post_type='event-recurring')";
		$migration_result = $wpdb->query($sql);
		if( $migration_result === false ) $migration_errors[] = array('Previous meta deletion', $wpdb->last_error);
		foreach( array('event_start', 'event_end', 'event_timezone', 'start', 'end') as $field ){
			if( $field == 'start' || $field == 'end' ){
				//create a timestamp combining two given fields, which we'll now use 
				$sql = 'INSERT INTO '.$wpdb->postmeta." (post_id, meta_key, meta_value) SELECT post_id, '_event_{$field}_local', TIMESTAMP(event_{$field}_date, event_{$field}_time) FROM ".$db . 'em_events'. $blog_id_where;
				$field = "event_".$field."_local";
			}else{
				$sql = 'INSERT INTO '.$wpdb->postmeta." (post_id, meta_key, meta_value) SELECT post_id, '_{$field}', {$field} FROM ". $db.'em_events'. $blog_id_where;
			}
			$migration_result = $wpdb->query($sql);
			if( $migration_result === false ) $migration_errors[] = array('Adding new meta data key <em>_'.$field.'</em>', $wpdb->last_error);
		}
	}
	
	//return the result of this migration, either true for no errors, or a string of errors.
	if( !empty($migration_errors) ){
		$string = __('There was an error whilst migrating your times to our new timezone-aware formats. Below is a list of errors:', 'events-manager');
		$string .= '<ul>';
		foreach( $migration_errors as $err ){
			$string .= '<li><strong>'. $err[0] .': </strong>'. $err[1] .'</li>';
		}
		$string .= '</ul>';
		return $string;
	}
	return true;
}

function em_migrate_get_tz_transitions( $timezone, $blog_id_where = '' ){
	global $wpdb;
	$db = EM_MS_GLOBAL ? $wpdb->base_prefix : $wpdb->prefix;
	$minmax_dates = $wpdb->get_row('SELECT MIN(event_start_date) AS mindate, MAX(event_end_date) AS maxdate FROM '. $db.'em_events' .$blog_id_where);
	$DTZ = new EM_DateTimeZone( $timezone );
	$start = strtotime($minmax_dates->mindate, current_time('timestamp')) - 60*60*24;
	$end = strtotime($minmax_dates->maxdate, current_time('timestamp')) + 60*60*24; //we add a day just to get the most comprehensive range possible
	$DTZ_Transitions = $DTZ->getTransitions($start, $end);
	//get first and next transitions to create a range, if there's only one transition we create a fake transition
	$transitions = array();
	if( count($DTZ_Transitions) == 1 ){
		$current_transition = current($DTZ_Transitions);
		$dst_offset = $current_transition['isdst'] ? 60*60 : 0;
		$transitions[] = array(
			'start' => $start,
			'end' => false,
			'offset' => $current_transition['offset']
		);
	}else{
		do{
			$current_transition = current($DTZ_Transitions);
			$transition_key = key($DTZ_Transitions);
			$next_transition = next($DTZ_Transitions);
			if( $current_transition['ts'] < $start ) continue;
			$dst_offset = $current_transition['isdst'] ? 60*60 : 0;
			if( $transition_key == 0 ){
				//add the final transition and break this loop
				$transitions[] = array(
				'start' => false,
				'end' => $next_transition['ts'] - 1 - $dst_offset,
				'offset' => $current_transition['offset']
				);
			}else{
				if( empty($next_transition) ){
					//add the final transition and break this loop
					$transitions[] = array(
					'start' => $current_transition['ts'] - $dst_offset,
					'end' => false,
					'offset' => $current_transition['offset']
					);
					break;
				}else{
					$transitions[] = array(
							'start' => $current_transition['ts'] - $dst_offset,
							'end' => $next_transition['ts'] - 1 - $dst_offset,
							'offset' => $current_transition['offset']
					);
				}
			}
			if( $current_transition['ts'] > $end ) break;
		} while( $next_transition !== false );
	}
	return $transitions;
}
?>