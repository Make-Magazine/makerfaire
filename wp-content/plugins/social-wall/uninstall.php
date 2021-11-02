<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

//If the user is preserving the settings then don't delete them
$options = get_option( 'sbsw_settings' );
$sbsw_preserve_settings = isset( $options[ 'preserve_settings' ] ) ? $options[ 'preserve_settings' ] : false;

// allow the user to preserve their settings in case they are upgrading
if ( ! $sbsw_preserve_settings ) {

	// clear cron jobs
	wp_clear_scheduled_hook( 'sbsw_feed_update' );

	// clean up options from the database
	delete_option( 'sbsw_settings' );
	delete_option( 'sbsw_cron_report' );
	delete_option( 'sbsw_errors' );
	delete_option( 'sbsw_db_version' );

	// delete role
	global $wp_roles;
	$wp_roles->remove_cap( 'administrator', 'manage_social_wall_options' );
}


