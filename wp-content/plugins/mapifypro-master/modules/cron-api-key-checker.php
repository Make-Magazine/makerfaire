<?php

/**
 * Cron API key checker.
 * These functions will add wp-cron to check the API Key status.
 * If the API key subscription has expired, then the plugin will be disabled.
 */

/**
 * Register the cron on plugin loaded.
 * @return void
 */
function mpfy_register_api_key_checker_scheduled_event() {
	$event_name = 'mpfy_api_key_checker';
	$next_event = wp_get_scheduled_event( $event_name );

	/**
	 * Clear any non-weekly mpfy_api_key_checker event.
	 * This is a backward compability of the previous 'twicedaily' cron event.
	 */
	if ( $next_event && 'weekly' !== $next_event->schedule ) {
		wp_clear_scheduled_hook( $event_name );
	}

	// register the cron event
    if ( ! $next_event ) {
		wp_schedule_event( time(), 'weekly', $event_name );
    }
}
add_action( 'mpfy_plugin_loaded', 'mpfy_register_api_key_checker_scheduled_event' );

/**
 * Unregister the cron on plugin deactivation.
 * Also unregister when the API key is not verified, so the plugin is inactive.
 * @return void
 */
function mpfy_unregister_api_key_checker_scheduled_event() {
    wp_clear_scheduled_hook( 'mpfy_api_key_checker' );
}
register_deactivation_hook( MAPIFY_PLUGIN_FILE, 'mpfy_unregister_api_key_checker_scheduled_event' );
add_action( 'mpfy_plugin_failed_to_load', 'mpfy_unregister_api_key_checker_scheduled_event' );

/**
 * The cron function to check the API Key.
 * This function will send a request to the WooCommerce API Manager's server to check the API status.
 * If the current API Key is not valid, then deactivate the plugin.
 * @return void
 */
function mpfy_api_key_checker_function() {
	global $wcam_lib;
	
	$is_api_key_activated = mpfy_is_api_key_activated();

	if ( false === $is_api_key_activated ) {
		$activation_status = get_option( $wcam_lib->wc_am_activated_key );

		// Set status to deactivated
		if ( 'Deactivated' !== $activation_status ) {
			update_option( $wcam_lib->wc_am_activated_key, 'Deactivated' );

			/**
			 * Because the API key has been deactivated by system (not manually by user),
			 * then we need to make sure this `deactivate_checkbox` setting is switched to `off`.
			 */
			update_option( $wcam_lib->data_key . '_deactivate_checkbox', 'off' );
		}
	} elseif ( is_wp_error( $is_api_key_activated ) ) {
		// Record the error message for debugging purpose
		error_log( $is_api_key_activated->get_error_message() );
	}
}
add_action( 'mpfy_api_key_checker', 'mpfy_api_key_checker_function', 10 );

/**
 * Send a request to the WooCommerce API Manager's to check whether the API key is activated or not.
 * Return WP_Error if the request has failed (connection error or server problem)
 * @return boolean|WP_Error
 */
function mpfy_is_api_key_activated() {
	global $wcam_lib;

	/**
	 * On class WC_AM_Client_25, below variable is not set by default if not is_admin().
	 * So we need to set them manually here to run this function on front-end.
	 */
	if ( '' === $wcam_lib->wc_am_api_key_key ) {
		$wcam_lib->wc_am_api_key_key  = $wcam_lib->data_key . '_api_key';
		$wcam_lib->wc_am_instance_key = $wcam_lib->data_key . '_instance';
		$wcam_lib->data               = get_option( $wcam_lib->data_key );
		$wcam_lib->wc_am_instance_id  = get_option( $wcam_lib->wc_am_instance_key );
	}

	// Get license status
	$license_status = $wcam_lib->license_key_status();

	if ( ! $license_status ) {
		$is_activated = new WP_Error( 'connection_failed', __( "Failed to connect to the MapifyPro API key server.", "mpfy" ) );
	} else {
		$is_activated = ( ! empty( $license_status[ 'status_check' ] ) && 'active' === $license_status[ 'status_check' ] ) ? true : false;
	}

	return $is_activated;
}

/**
 * Add weekly wp_cron interval
 * @return array
 */
function mpfy_add_weekly_interval( $schedules ) {
    $schedules['weekly'] = array(
        'interval' => 604800,
        'display'  => __( 'Once Weekly', 'mpfy' ),
    );

    return $schedules;
}
add_filter( 'cron_schedules', 'mpfy_add_weekly_interval' ); 