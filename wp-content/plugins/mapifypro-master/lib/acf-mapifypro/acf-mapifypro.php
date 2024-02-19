<?php

/**
 * A plugin that contains all required and essentials custom ACF (AdvancedCustomFields) Fields for Mapify.
 * This plugin runs as the Mapify library.
 *
 * @link              https://mapifypro.com/
 * @since             1.0.0
 * @package           Acf_Mapifypro
 *
 * @wordpress-plugin
 * Plugin Name:       ACF MapifyPro
 * Plugin URI:        https://mapifypro.com/
 * Description:       OpenStreetMap fields for Advanced Custom Fields (ACF)
 * Version:           1.1.8
 * Author:            Haris Ainur Rozak
 * Author URI:        https://mapifypro.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       acf-mapifypro
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
define( 'ACF_MAPIFYPRO_VERSION', '1.1.8' );
define( 'ACF_MAPIFYPRO_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'ACF_MAPIFYPRO_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACF_MAPIFYPRO_DEFAULT_LAT', '36.10049188776103' );
define( 'ACF_MAPIFYPRO_DEFAULT_LNG', '-112.08123421649363' );
define( 'ACF_MAPIFYPRO_DEFAULT_ZOOM', 8 );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-acf-mapifypro-activator.php
 */
function activate_acf_mapifypro() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-acf-mapifypro-activator.php';
	Acf_Mapifypro_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-acf-mapifypro-deactivator.php
 */
function deactivate_acf_mapifypro() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-acf-mapifypro-deactivator.php';
	Acf_Mapifypro_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_acf_mapifypro' );
register_deactivation_hook( __FILE__, 'deactivate_acf_mapifypro' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-acf-mapifypro.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_acf_mapifypro() {

	$plugin = new Acf_Mapifypro();
	$plugin->run();

}
run_acf_mapifypro();
