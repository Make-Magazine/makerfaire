<?php
/**
 * Plugin Name: GP Populate Anything
 * Description: Populate fields from posts, users, entries, or databases.
 * Plugin URI: https://gravitywiz.com/documentation/gravity-forms-populate-anything/
 * Version: 2.1.75
 * Author: Gravity Wiz
 * Author URI: https://gravitywiz.com/
 * License: GPL2
 * Perk: True
 * Update URI: https://gravitywiz.com/updates/gp-populate-anything
 * Text Domain: gp-populate-anything
 * Domain Path: /languages
 */

define( 'GPPA_VERSION', '2.1.75' );

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload_packages.php';

\Spellbook\Bootstrap::register( __FILE__ );

function gp_populate_anything() {
	return \GP_Populate_Anything::get_instance();
}

// Register export hooks on gform_loaded so they're attached
// before Gravity Forms handles form export requests on init.
add_action( 'gform_loaded', 'gppa_export', 5 );

