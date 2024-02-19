<?php

require_once( 'updater/all.php' );

/**
 * Notification for admin after plugin updated.
 */
function mpfy_show_update_notice(){
	if ( version_compare( mpfy_get_version(), MAPIFY_PLUGIN_VERSION, '>=' ) ) {
		return false;
	}
	
	/**
	 * Show a notification with an action to update the MapifyPro database.
	 * If any functions need to run upon update, then we convice user to backup their data first before proceed.
	 * The database updater functions are located here: MAPIFY_PLUGIN_DIR . '/updater.php'
	 */
	if ( mpfy_get_functions_to_run_upon_update() ) {
		$update_url         = add_query_arg( 'action', 'mpfy_update', admin_url( 'admin.php' ) );
		$secured_update_url = wp_nonce_url( $update_url, 'OuUkMrSs7nz6b' );
		$message            = sprintf( '<strong>%s</strong> %s', __( 'Warning!.', 'mpfy' ), __( 'Your MapifyPro plugin data must be updated. Please backup your data and', 'mpfy' ) );
		
		printf( '<div class="notice notice-warning mapifypro-notice"><p>%s <a href="%s"><strong>%s</strong></a> %s.</p></div>', $message, $secured_update_url, __( 'click here', 'mpfy' ), __( 'to proceed', 'mpfy' ) );
	} else {		
		mpfy_update_plugin_version(); // update saved version to latest
	}
}
add_action( 'admin_notices', 'mpfy_show_update_notice');

/**
 * Execute update and then reactivate the API key.
 */
function mpfy_update() {
	wp_verify_nonce( 'OuUkMrSs7nz6b' );

	$functions_to_run = mpfy_get_functions_to_run_upon_update();

	// runs update functions if any
	foreach ( $functions_to_run as $function_to_run ) {
		call_user_func( $function_to_run );
	}

	// update saved version to latest
	mpfy_update_plugin_version();

	// set transient to show the update notification
	set_transient( 'mpfy_just_updated_database', true );

	// redirect
    wp_redirect( $_SERVER['HTTP_REFERER'] ); exit();
}
add_action( 'admin_action_mpfy_update', 'mpfy_update' );

/**
 * Get the functions to run upon update, if any.
 */
function mpfy_get_functions_to_run_upon_update() {
	include_once( MAPIFY_PLUGIN_DIR . '/updater.php' );

	$updater_functions = apply_filters( 'mpfy_updater_functions', $updater_functions );
	$functions_to_run  = array();

	foreach ( $updater_functions as $version => $function_name ) {
		if ( version_compare( mpfy_get_version(), $version, '<' ) && $function_name && function_exists( $function_name ) ) {
			$functions_to_run[] = $function_name;
		}
	}

	return $functions_to_run;
}

/**
 * Notice admin after updated the database
 */
function mpfy_notice_after_update_database() {		
	if ( ! get_transient( 'mpfy_just_updated_database' ) ) return;

	global $wcam_lib;
	
	// dont show again this notification
	delete_transient( 'mpfy_just_updated_database' );

	// message
	$title   = __( 'Your MapifyPro data has been updated.', 'mpfy' );	
	$message = __( 'Thank you for updating MapifyPro!.', 'mpfy' );
	
	// print notice
	printf( '<div class="notice notice-success is-dismissible mapifypro-notice"><p><b>%s</b> %s</p></div>', esc_html( $title ), esc_html( $message ) ); 
}
add_action( 'admin_notices', 'mpfy_notice_after_update_database' );