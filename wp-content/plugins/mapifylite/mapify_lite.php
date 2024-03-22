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
 * Version:           4.3.4
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

/**
 * Promote upgrade to MapifyPro within the plugin in the list of plugins.
 * Will be displayed when there is no update notification for the plugin.
 */
function mpfy_lite_promotion_in_the_list_of_plugins( $plugin_file, $plugin_data, $status) {
	$update_plugins    = get_site_transient( 'update_plugins' );
	$available_updates = isset( $update_plugins->response ) ? $update_plugins->response : array();
	$file              = basename( __FILE__ );
	$folder            = basename( dirname( __FILE__ ) );
	$current_plugin    = "$folder/$file";
	
    if ( $current_plugin === $plugin_file && ! isset($available_updates[ $current_plugin ])  ) {
		echo "<tr class='plugin-update-tr active'><td colspan=4 class='plugin-update colspanchange'><div class='update-message notice inline notice-warning notice-alt mapify-plugin-list-promotion'><p>";
		printf( __( 'Upgrade to MapifyPro for PrettyRoutes, unlimited maps, custom unique map pins, SEO support, image maps, 3D Buildings Theme, and bulk uploading of map locations). %sClick here for a full feature list%s.' ), '<a href="https://mapifypro.com/full-feature-list/" target="_blank">', '</a>' );
		echo "</p></div></td></tr>";
	}	
}
add_action( 'after_plugin_row', 'mpfy_lite_promotion_in_the_list_of_plugins', 10, 3 ); 