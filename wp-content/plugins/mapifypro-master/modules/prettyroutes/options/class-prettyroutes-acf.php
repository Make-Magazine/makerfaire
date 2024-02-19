<?php

/**
 * Class Prettyroutes_ACF
 * 
 * @since    2.0.0
 */
class Prettyroutes_ACF {

	/**
	 * Initialize the class and set its properties.
	 * 
	 * @since    2.0.0
	 */
	public function __construct() {
		// Define path and URL to the ACF plugin.
		define( 'PRETTYROUTES_ACF_PATH', MAPIFY_PLUGIN_DIR_PATH . '/lib/advanced-custom-fields-pro/' );
		define( 'PRETTYROUTES_ACF_URL',  MAPIFY_PLUGIN_DIR_URL . '/lib/advanced-custom-fields-pro/' );

		// Include the ACF plugin.
		if( ! class_exists( 'ACF' ) ) {
			define( 'ACF_PRO' , true );
			include_once( PRETTYROUTES_ACF_PATH . 'acf.php' );

			// Hooks for modify ACF assets url and setting url
			add_action( 'acf/settings/url', array( $this, 'acf_settings_url' ) );
			add_action( 'acf/settings/show_admin', array( $this, 'acf_settings_show_admin' ) );
		}		
		
		// Include ACF MapifyPro Plugin
		if( class_exists( 'ACF' ) ) {
			include_once( PRETTYROUTES_PLUGIN_DIR_PATH . '/lib/acf-prettyroutes/acf-prettyroutes.php' );
		}
	}

	/**
	 * Customize the url setting to fix incorrect asset URLs.
	 * 
	 * @since    2.0.0
	 */
	function acf_settings_url( $url ) {
		return PRETTYROUTES_ACF_URL;
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

/**
 * Actions to include Advanced Custom Field (AFC) on the plugin
 * Also include the ACF PrettyRoutes plugin
 */
$prettyroutes_acf = new Prettyroutes_ACF();