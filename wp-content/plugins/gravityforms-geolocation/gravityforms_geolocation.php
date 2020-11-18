<?php
/**
 * Plugin Name: Gravity Forms Geolocation Add-on
 * Plugin URI: http://www.geomywp.com
 * Description: Enhance Gravity Forms plugin with geolocation features
 * Version: 2.7.0.1
 * Author: Eyal Fitoussi
 * Author URI: http://www.geomywp.com
 * Requires at least: 4.0
 * Tested up to: 5.0
 * Gravity Forms: 2.0+
 * Gravity Forms User Registration: 3.0+
 * GEO my WP: 2.6.1+
 * Text Domain: gfgeo
 * Domain Path: /languages/
 *
 * @package gravityforms-geolocation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'GFGEO_VERSION', '2.7.0.1' );

add_action( 'gform_loaded', array( 'GF_Geolocation_AddOn_Bootstrap', 'load' ), 8 );

/**
 * Init class for Gravitu Forms Geolocation.
 */
class GF_Geolocation_AddOn_Bootstrap {

	/**
	 * Load the plugin.
	 *
	 * @return [type] [description]
	 */
	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
			return;
		}

		require_once 'class-gravity-forms-geolocation.php';

		GFAddOn::register( 'Gravity_Forms_Geolocation' );
	}
}

/**
 * Init plugin instance.
 *
 * @return [type] [description]
 */
function gf_geolocation_addon() {
	return Gravity_Forms_Geolocation::get_instance();
}
