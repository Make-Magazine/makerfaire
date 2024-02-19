<?php

/**
 * Plugin conflict check on init hook
 */
function crowdmaps_plugin_check_on_init_hook() {
	$is_conflict = defined( 'CROWD_PLUGIN_FILE' ) || function_exists( 'crowd_load_textdomain' );
	$is_conflict = $is_conflict && !defined( 'CROWD_PLUGIN_INTERNAL' );
	
	if ( $is_conflict ) {
		add_action( 'admin_notices', 'crowdmaps_notification_on_plugin_conflict' );
		if ( ! mpfy_get_conflict_status( 'crowdmaps' ) ) {
			mpfy_set_conflict_status( 'crowdmaps', true );
		}
	} elseif ( ! $is_conflict && mpfy_get_conflict_status( 'crowdmaps' ) ) {
		add_action( 'admin_notices', 'crowdmaps_notification_on_plugin_not_conflict' );
		mpfy_set_conflict_status( 'crowdmaps', false );
	}
}
add_action( 'init', 'crowdmaps_plugin_check_on_init_hook' );

/**
 * Admin notification on plugin conflict
 */
function crowdmaps_notification_on_plugin_conflict() {
	?>	
	<div class="notice notice-error mapifypro-notice">
		<p><b>MapifyPro: </b><?php _e( 'CrowdMaps has now been merged into MapifyPro! Please deactivate and delete the CrowdMaps plugin to get the latest features. Your CrowdMaps entries will be automatically migrated. 🙂', 'mpfy' ); ?></p>
	</div>	
	<?php
}

/**
 * Admin notification on plugin not conflict
 */
function crowdmaps_notification_on_plugin_not_conflict() {
	?>	
	<div class="notice notice-success mapifypro-notice">
		<p><b>MapifyPro: </b><?php _e( 'Refresh this page to start using CrowdMaps! 🙂', 'mpfy' ); ?></p>
	</div>	
	<?php
}

/**
 * Bail out a plugin activation (in this case the CrowdMaps standalone plugin), even before the plugin file is loaded.
 * Using this hook will prevent any PHP fatal error, because of any defined constants, class names or functions.
 */
function crowdmaps_bail_out_plugin_activation( $action, $result ) {
	$plugin_name                = 'CrowdMaps';
	$plugin_file_name           = '/crowdmaps.php';
	$activate_plugin_referer    = 'activate-plugin_';
	$is_activate_plugin_referer = strpos( $action, $activate_plugin_referer );
	$is_activated_routes_php    = strpos( $action, $plugin_file_name );

	if ( false !== $is_activate_plugin_referer && false !== $is_activated_routes_php  ) {
		$plugin_file      = str_replace( $activate_plugin_referer, '', $action );
		$plugin_file_path = WP_PLUGIN_DIR . '/' . $plugin_file;
		$plugin_headers   = file_exists( $plugin_file_path ) ? get_plugin_data( $plugin_file_path ) : false;

		if ( $plugin_headers && $plugin_name === $plugin_headers['Name'] ) {
			wp_redirect( admin_url( 'plugins.php?action=mapifypro_crowdmaps_activation' ) );
			exit;
		}
	}
}
add_action( 'check_admin_referer', 'crowdmaps_bail_out_plugin_activation', 10, 2 );

/**
 * Bail out a plugin from a bulk activation (in this case the CrowdMaps standalone plugin), even before the plugin file is loaded.
 * Using this hook will prevent any PHP fatal error, because of any defined constants, class names or functions.
 */
function crowdmaps_bail_out_plugin_bulk_activation( $action, $result ) {
	if ( 'bulk-plugins' !== $action ) return;
	if ( ! isset( $_POST['action'] ) || 'activate-selected' !== $_POST['action'] ) return;

	$plugin_name      = 'CrowdMaps';
	$plugin_file_name = '/crowdmaps.php';
	$plugins          = isset( $_POST['checked'] ) ? (array) wp_unslash( $_POST['checked'] ) : array();

	foreach ( $plugins as $key => $plugin ) {
		if ( false !== strpos( $plugin, $plugin_file_name ) ) {
			$plugin_headers = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );

			if ( $plugin_name === $plugin_headers['Name'] ) {
				unset( $_POST['checked'][ $key ] );
			}
		}
	}
}
add_action( 'check_admin_referer', 'crowdmaps_bail_out_plugin_bulk_activation', 10, 2 );

/**
 * Bail out a plugin activation message
 */
function crowdmaps_bail_out_plugin_activation_message() {
	$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : false;

	if ( 'mapifypro_crowdmaps_activation' === $action ) {
		?>
		<div class="notice notice-error is-dismissible mapifypro-notice">
			<p><b>MapifyPro: </b><?php _e( 'Whoa! You should know that CrowdMaps is now a part of MapifyPro! Please deactivate and delete the CrowdMaps plugin. Your CrowdMaps entries have already been migrated. 🙂', 'mpfy' ); ?></p>
		</div>		
		<?php
	}
}
add_action( 'admin_notices', 'crowdmaps_bail_out_plugin_activation_message' );