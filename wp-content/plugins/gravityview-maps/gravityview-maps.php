<?php
/*
Plugin Name: GravityView - Maps
Plugin URI: https://gravityview.co/extensions/maps/
Description: Display your GravityView entries on a map.
Version: 1.4.2
Author: Katz Web Services, Inc.
Author URI: https://gravityview.co
Text Domain: gravityview-maps
Domain Path: /languages/
*/


defined( 'ABSPATH' ) || exit;

function gravityview_extension_maps_loader() {

	if( !class_exists( 'GravityView_Extension' ) ) {

		if( class_exists('GravityView_Plugin') && is_callable( array( 'GravityView_Plugin', 'include_extension_framework' ) ) ) {
			GravityView_Plugin::include_extension_framework();
		} else {
			// We prefer to use the one bundled with GravityView, but if it doesn't exist, go here.
			include_once plugin_dir_path( __FILE__ ) . 'lib/class-gravityview-extension.php';
		}
	}

	require_once dirname( __FILE__ ) . '/includes/class-gravityview-maps-loader.php';

	// Make sure PHP 5.3 is supported
	if( version_compare( phpversion(), '5.3' ) <= 0) {

		$message = sprintf( __("%s requires PHP Version 5.3 or higher. Please contact your web host and ask them to upgrade your server.", 'gravityview-maps'), 'GravityView Maps' );

		GravityView_Maps_Loader::add_notice( array(
			'message' => wpautop( $message ),
			'class' => 'error',
		));

	} else {

		$GLOBALS['gravityview_maps'] = new GravityView_Maps_Loader( __FILE__, '1.4.2' );

	}
}

add_action( 'plugins_loaded', 'gravityview_extension_maps_loader' );
