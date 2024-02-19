<?php

/**
 * MapifyLite (by MapifyPro)
 *
 * MapifyLite is an elite plugin for WordPress that implements fully-customized maps on your site. 
 * It enhances Google maps with custom pin-point graphics and pop-up galleries, but also allows ANY custom map image of your choosing, 
 * all while keeping the great zoom and pan effect of Google maps! Perfect for creating a store locator, travel routes, tours, journals, and more.
 *
 * @link              https://mapifypro.com/
 * @since             1.0.0
 * @package           mpfy
 *
 * @wordpress-plugin
 * Plugin Name:       MapifyLite
 * Plugin URI:        https://mapifypro.com/product/mapifylite/
 * Description:       MapifyLite is an elite plugin for WordPress that implements fully-customized maps on your site.
 * Version:           4.3.3
 * Author:            MapifyPro
 * Author URI:        https://mapifypro.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mpfy
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Load plugin if no plugin conflict occured
 */
if ( ! defined( 'MAPIFY_PLUGIN_FILE' ) ) {
	define( 'MAPIFY_PLUGIN_FILE', __FILE__ );

	/**
	 * Include plugin activation hooks
	 */
	include_once( 'modules/plugin-activation.php' );

	/**
	 * Required plugin utility functions
	 */
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

	/**
	 * Load plugin
	 */
	include_once( 'load.php' );
} else {
	add_action( 'admin_notices', 'mpfy_lite_plugin_conflict' );
}

/**
 * Message about the plugin conflict
 */
function mpfy_lite_plugin_conflict() {
	$plugin_data = get_file_data( __FILE__, array( 
		'Plugin Name' => 'Plugin Name', 
		'Version'     => 'Version' 
	) );

	$conflicted_plugin_data = get_file_data( MAPIFY_PLUGIN_FILE, array( 
		'Plugin Name' => 'Plugin Name', 
		'Version'     => 'Version' 
	) );

	if ( $conflicted_plugin_data['Plugin Name'] ) {
		$message = sprintf( __( 'The %s plugin will be inactive until you deactivate the %s plugin.' ), $plugin_data['Plugin Name'], $conflicted_plugin_data['Plugin Name'] );
	} else {
		$message = sprintf( __( 'The %s plugin will be inactive as there is a conflicting plugin.' ), $plugin_data['Plugin Name'] );
	}

	?><div class="error">
		<p><?php echo $message; ?></p>
	</div><?php
}