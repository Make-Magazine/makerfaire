<?php

/**
 * Plugin conflict check on init hook
 */
function route_plugin_check_on_init_hook() {
	$is_conflict = defined( 'PRETTYROUTES_PLUGIN_FILE' ) || function_exists( 'routes_get_version' );
	$is_conflict = $is_conflict && !defined( 'PRETTYROUTES_PLUGIN_INTERNAL' );
	
	if ( $is_conflict ) {
		add_action( 'admin_notices', 'route_notification_on_plugin_conflict' );
		if ( ! mpfy_get_conflict_status( 'prettyroutes' ) ) {
			mpfy_set_conflict_status( 'prettyroutes', true );
		}
	} elseif ( ! $is_conflict && mpfy_get_conflict_status( 'prettyroutes' ) ) {
		add_action( 'admin_notices', 'route_notification_on_plugin_not_conflict' );
		mpfy_set_conflict_status( 'prettyroutes', false );
	}
}
add_action( 'init', 'route_plugin_check_on_init_hook' );

/**
 * Admin notification on plugin conflict
 */
function route_notification_on_plugin_conflict() {
	?>	
	<div class="notice notice-error mapifypro-notice">
		<p><b>MapifyPro: </b><?php _e( 'PrettyRoutes has now been merged into MapifyPro! Please deactivate and delete the PrettyRoutes plugin to get the latest features. Your PrettyRoutes entries will be automatically migrated. 🙂', 'mpfy' ); ?></p>
	</div>	
	<?php
}

/**
 * Admin notification on plugin not conflict
 */
function route_notification_on_plugin_not_conflict() {
	?>	
	<div class="notice notice-success mapifypro-notice">
		<p><b>MapifyPro: </b><?php _e( 'Refresh this page to start using PrettyRoutes! 🙂', 'mpfy' ); ?></p>
	</div>	
	<?php
}

/**
 * Bail out a plugin activation (in this case the PrettyRoutes standalone plugin), even before the plugin file is loaded.
 * Using this hook will prevent any PHP fatal error, because of any defined constants, class names or functions.
 */
function route_bail_out_plugin_activation( $action, $result ) {
	$plugin_name                = 'PrettyRoutesPro';
	$plugin_file_name           = '/routes.php';
	$activate_plugin_referer    = 'activate-plugin_';
	$is_activate_plugin_referer = strpos( $action, $activate_plugin_referer );
	$is_activated_routes_php    = strpos( $action, $plugin_file_name );

	if ( false !== $is_activate_plugin_referer && false !== $is_activated_routes_php  ) {
		$plugin_file      = str_replace( $activate_plugin_referer, '', $action );
		$plugin_file_path = WP_PLUGIN_DIR . '/' . $plugin_file;
		$plugin_headers   = file_exists( $plugin_file_path ) ? get_plugin_data( $plugin_file_path ) : false;

		if ( $plugin_headers && $plugin_name === $plugin_headers['Name'] ) {
			wp_redirect( admin_url( 'plugins.php?action=mapifypro_prettyroutes_activation' ) );
			exit;
		}
	}
}
add_action( 'check_admin_referer', 'route_bail_out_plugin_activation', 10, 2 );

/**
 * Bail out a plugin from a bulk activation (in this case the PrettyRoutes standalone plugin), even before the plugin file is loaded.
 * Using this hook will prevent any PHP fatal error, because of any defined constants, class names or functions.
 */
function route_bail_out_plugin_bulk_activation( $action, $result ) {
	if ( 'bulk-plugins' !== $action ) return;
	if ( ! isset( $_POST['action'] ) || 'activate-selected' !== $_POST['action'] ) return;

	$plugin_name      = 'PrettyRoutesPro';
	$plugin_file_name = '/routes.php';
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
add_action( 'check_admin_referer', 'route_bail_out_plugin_bulk_activation', 10, 2 );

/**
 * Bail out a plugin activation message
 */
function route_bail_out_plugin_activation_message() {
	$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : false;

	if ( 'mapifypro_prettyroutes_activation' === $action ) {
		?>
		<div class="notice notice-error is-dismissible mapifypro-notice">
			<p><b>MapifyPro: </b><?php _e( 'Whoa! You should know that PrettyRoutes is now a part of MapifyPro! Please deactivate and delete the PrettyRoutes plugin. Your PrettyRoutes entries have already been migrated. 🙂', 'mpfy' ); ?></p>
		</div>		
		<?php
	}
}
add_action( 'admin_notices', 'route_bail_out_plugin_activation_message' );