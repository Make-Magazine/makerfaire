<?php

/**
 * Include Advanced Custom Field (AFC) on the plugin
 * 
 * @since    2.0.0
 */

/**
 * Class Mapify_ACF
 * 
 * @since    2.0.0
 */
class Mapify_ACF {

	/**
	 * Initialize the class and set its properties.
	 * 
	 * @since    2.0.0
	 */
	public function __construct() {
		// Define path and URL to the ACF plugin.
		define( 'MAPIFY_ACF_PATH', MAPIFY_PLUGIN_DIR_PATH . '/lib/advanced-custom-fields/' );
		define( 'MAPIFY_ACF_URL',  MAPIFY_PLUGIN_DIR_URL . '/lib/advanced-custom-fields/' );

		// Include the ACF plugin.
		if( ! class_exists( 'ACF' ) ) {
			define( 'ACF_LITE' , true );
			include_once( MAPIFY_ACF_PATH . 'acf.php' );

			// Hooks for modify ACF assets url and setting url
			add_action( 'acf/settings/url', array( $this, 'acf_settings_url' ) );
			add_action( 'acf/settings/show_admin', array( $this, 'acf_settings_show_admin' ) );
		}

		// Include ACF Mapifylite Plugin
		if( class_exists( 'ACF' ) ) {
			include_once( MAPIFY_PLUGIN_DIR_PATH . '/lib/acf-mapifylite/acf-mapifylite.php' );
		}
	}

	/**
	 * Customize the url setting to fix incorrect asset URLs.
	 * 
	 * @since    2.0.0
	 */
	function acf_settings_url( $url ) {
		return MAPIFY_ACF_URL;
	}

	/**
	 * Hide the ACF admin menu item.
	 * 
	 * @since    2.0.0
	 */
	function acf_settings_show_admin( $show_admin ) {
		return false;
	}
}