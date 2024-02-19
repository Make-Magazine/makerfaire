<?php

/**
 * Cron API key activator.
 * These functions will add wp-cron to activate existed API Key.
 * The cron should run daily and only if the API Key is present but it's deactivated.
 */

/**
 * Register the cron when plugin is failed to load.
 * @return void
 */
function mpfy_register_api_key_activator_scheduled_event() {
	$event_name = 'mpfy_api_key_activator';
	$next_event = wp_get_scheduled_event( $event_name );

	// register the cron event
    if ( ! $next_event ) {
		wp_schedule_event( time(), 'daily', $event_name );
    }
}
add_action( 'mpfy_plugin_failed_to_load', 'mpfy_register_api_key_activator_scheduled_event' );

/**
 * Unregister the cron on plugin deactivation.
 * Also unregister when the API key has been verified, so the plugin become active.
 * @return void
 */
function mpfy_unregister_api_key_activator_scheduled_event() {
    wp_clear_scheduled_hook( 'mpfy_api_key_activator' );
}
register_deactivation_hook( MAPIFY_PLUGIN_FILE, 'mpfy_unregister_api_key_activator_scheduled_event' );
add_action( 'mpfy_plugin_loaded', 'mpfy_unregister_api_key_activator_scheduled_event' );

/**
 * The cron function activate existed API Key.
 * This function will send a request to the WooCommerce API Manager's server to activate the API key.
 * @return void
 */
function mpfy_api_key_activator_function() {
	global $wcam_lib;

	$api_key                 = mpfy_get_api_key();
	$deactivate_checkbox_key = get_option( $wcam_lib->data_key . '_deactivate_checkbox', 'off' );

	if ( ! mpfy_api_is_activated() && '' !== $api_key && 'off' === $deactivate_checkbox_key ) {		
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

		/**
		 * We need to make sure the API key is deactivated before we activating it.
		 * Because activating the already activated API key on the server will get us an error (code 100) response. 
		 * Also this useful for updating the user's plugin version to our database for support purpose.
		 */
		$wcam_lib->deactivate( array( 'api_key' => $api_key ) );

		mpfy_activate_api_key( $api_key );
	}
}
add_action( 'mpfy_api_key_activator', 'mpfy_api_key_activator_function', 10 );