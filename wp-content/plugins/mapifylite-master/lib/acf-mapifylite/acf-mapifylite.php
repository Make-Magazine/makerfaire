<?php

/**
 * A plugin that contains all required and essentials custom ACF (AdvancedCustomFields) Fields for Mapify.
 * This plugin runs as the Mapify library.
 *
 * @link              https://mapifypro.com/
 * @since             1.0.0
 * @package           Acf_Mapifylite
 *
 * @wordpress-plugin
 * Plugin Name:       ACF MapifyLite
 * Plugin URI:        https://mapifypro.com/
 * Description:       OpenStreetMap fields for Advanced Custom Fields (ACF)
 * Version:           1.1.0
 * Author:            Haris Ainur Rozak
 * Author URI:        https://mapifypro.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       acf-mapifylite
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ACF_MAPIFYLITE_VERSION', '1.1.0' );
define( 'ACF_MAPIFYLITE_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'ACF_MAPIFYLITE_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-acf-mapifylite-activator.php
 */
function activate_acf_mapifylite() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-acf-mapifylite-activator.php';
	Acf_Mapifylite_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-acf-mapifylite-deactivator.php
 */
function deactivate_acf_mapifylite() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-acf-mapifylite-deactivator.php';
	Acf_Mapifylite_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_acf_mapifylite' );
register_deactivation_hook( __FILE__, 'deactivate_acf_mapifylite' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-acf-mapifylite.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_acf_mapifylite() {

	$plugin = new Acf_Mapifylite();
	$plugin->run();

}
run_acf_mapifylite();
