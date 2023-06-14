<?php
/**
 * Plugin Name: Gravity Forms Geolocation Add-on
 * Plugin URI: http://www.geomywp.com
 * Description: Enhance Gravity Forms plugin with geolocation features
 * Version: 3.1.4
 * Author: Eyal Fitoussi
 * Author URI: http://www.geomywp.com
 * Requires at least: 4.0
 * Requires PHP: 5.6
 * Tested up to: 6.0
 * Gravity Forms: 2.5+
 * Text Domain: gfgeo
 * Domain Path: /languages/
 *
 * Copyright: 2022 Gravity Geolocation
 *
 * @package gravityforms-geolocation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'GFGEO_VERSION', '3.1.4' );

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
