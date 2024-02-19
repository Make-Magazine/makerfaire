<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://mapifypro.com/
 * @since             1.0.0
 * @package           Acf_Prettyroutes
 *
 * @wordpress-plugin
 * Plugin Name:       ACF PrettyRoutes
 * Plugin URI:        http://www.mapifypro.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.1.0
 * Author:            Haris Ainur Rozak
 * Author URI:        https://mapifypro.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       acf-prettyroutes
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
define( 'ACF_PRETTYROUTES_VERSION', '1.1.0' );
define( 'ACF_PRETTYROUTES_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'ACF_PRETTYROUTES_DIR_PATH', plugin_dir_path( __FILE__ ) );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-acf-prettyroutes-activator.php
 */
function activate_acf_prettyroutes() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-acf-prettyroutes-activator.php';
	Acf_Prettyroutes_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-acf-prettyroutes-deactivator.php
 */
function deactivate_acf_prettyroutes() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-acf-prettyroutes-deactivator.php';
	Acf_Prettyroutes_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_acf_prettyroutes' );
register_deactivation_hook( __FILE__, 'deactivate_acf_prettyroutes' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-acf-prettyroutes.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_acf_prettyroutes() {

	$plugin = new Acf_Prettyroutes();
	$plugin->run();

}
run_acf_prettyroutes();
