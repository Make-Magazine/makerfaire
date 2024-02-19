<?php

/**
 * Notification for admin about the license verification after plugin update.
 */
function mpfy_show_verify_license_notice(){	
	if ( mpfy_is_new_license_version() ) {
		$title   = __( 'MapifyPro is verifying your API key in the background.', 'mpfy' );
		$message = __( 'It will take a moment, so please be patient.', 'mpfy' );

		// print notice
		printf( '<div class="notice notice-info mpfy-notice mapifypro-notice"><p><strong>%s</strong> %s</p></div>', $title, $message );

		// print nonce
		wp_nonce_field( 'sAEerQLA9aDbTbo', 'mpfy_nonce' );
	}
}
add_action( 'admin_notices', 'mpfy_show_verify_license_notice' );

/**
 * Enqueue verify-license.js
 */
function mpfy_enqueue_verify_license_js( $hook ) {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'mpfy-verify-license', plugins_url( 'assets/js/verify-license.js', MAPIFY_PLUGIN_FILE ), array( 'jquery' ), MAPIFY_PLUGIN_VERSION, false );
	wp_localize_script( 'mpfy-verify-license', 'mpfy_verify_license_object', array( 
		'ajaxurl'                => admin_url( 'admin-ajax.php' ),
		'is_new_license_version' => mpfy_is_new_license_version(),
	) );

}
add_action( 'admin_enqueue_scripts', 'mpfy_enqueue_verify_license_js' );

/**
 * Ajax verify license
 */
function mpfy_ajax_verify_license() {
    check_ajax_referer( 'sAEerQLA9aDbTbo', 'mpfy_nonce' );
	
	$upgrade_message = mpfy_reactivate_license(); // verify (reactivate) license	
	
	// messages to display
	if ( 'success' === $upgrade_message['type'] ) {
		$notice_class   = 'notice notice-success mapifypro-notice';
		$notice_title   = __( 'Thank you for updating MapifyPro!', 'mpfy' );
		$notice_content = sprintf( '%s %s', __( 'Your license has been verified.', 'mpfy' ), $upgrade_message['message'] );
	} elseif ( 'error_connection' === $upgrade_message['type'] ) {
		$notice_class   = 'notice notice-warning mapifypro-notice';
		$notice_title   = __( 'Error verifying your MapifyPro API key.', 'mpfy' );
		$notice_content = sprintf( '%s %s', $upgrade_message['message'], __( 'We will retry the process on the next time you reloading the admin page.', 'mpfy' ) );
	}
	
	// print notice
	if ( 'invalid_license' === $upgrade_message['type'] ) {
		global $wcam_lib;
		$wcam_lib->inactive_notice();
	} else {
		printf( '<div class="%s"><p><b>%s</b> %s</p></div>', esc_attr( $notice_class ), esc_html( $notice_title ), esc_html( $notice_content ) ); 
	}

    wp_die();
}
add_action( 'wp_ajax_verify_license', 'mpfy_ajax_verify_license' ); /* for logged in user */

/**
 * Re-activate MapifyPro license
 * @return array Activation result's message.
 */
function mpfy_reactivate_license() {
	global $wcam_lib;

	$api_key       = mpfy_get_api_key();
	$activate_args = array( 'api_key' => $api_key );

	// re-activate license
	if ( '' !== $api_key ) {
		$wcam_lib->license_key_deactivation();
		$activate_results = json_decode( $wcam_lib->activate( $activate_args ), true );

		// the license is successfully activated
		if ( $activate_results[ 'success' ] === true && $activate_results[ 'activated' ] === true ) {
			$upgrade_message = array(
				'type'    => 'success',
				'message' => $activate_results['message'],
			);

			update_option( $wcam_lib->wc_am_activated_key, 'Activated' );
			update_option( $wcam_lib->wc_am_deactivate_checkbox_key, 'off' );
						
			mpfy_update_license_version(); // update license version
		}

		// connection failed. the plugin will try again the license activation later. 
		if ( $activate_results == false && ! empty( $wcam_lib->data ) && ! empty( $wcam_lib->wc_am_activated_key ) ) {
			$upgrade_message = array(
				'type'    => 'error_connection',
				'message' => __( 'Connection failed to the License Key API server. There may be a problem on your server preventing outgoing requests, or the store is blocking your request to activate MapifyPro.', 'mpfy' ),
			);
		}

		// invalid license
		if ( isset( $activate_results[ 'data' ][ 'error_code' ] ) && ! empty( $wcam_lib->data ) && ! empty( $wcam_lib->wc_am_activated_key ) ) {
			$upgrade_message = array(
				'type'    => 'invalid_license',
				'message' => $activate_results['data']['error'],
			);

			update_option( $wcam_lib->wc_am_activated_key, 'Deactivated' );

			/**
			 * Because the API key has been deactivated by system (not manually by user),
			 * then we need to make sure this `deactivate_checkbox` setting is switched to `off`.
			 */
			update_option( $wcam_lib->data_key . '_deactivate_checkbox', 'off' );
		}
	}

	return $upgrade_message;
}