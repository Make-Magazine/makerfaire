<?php

/**
 * MapifyPro
 *
 * MapifyPro is an elite plugin for WordPress that implements fully-customized maps on your site, 
 * designed and developed by Mapify LLC.
 *
 * @link              https://mapifypro.com/
 * @since             1.0.0
 * @package           mpfy
 *
 * @wordpress-plugin
 * Plugin Name:       MapifyPro
 * Plugin URI:        https://mapifypro.com/
 * Description:       MapifyPro is an elite plugin for WordPress that implements fully-customized maps on your site.
 * Version:           4.7.1
 * Author:            Mapify LLC
 * Author URI:        https://mapifypro.com/
 * License:           GPL-2.0
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
	 * Load WC AM client
	 */
	mpfy_pro_wc_am_client();

	/**
	 * Include plugin utility functions
	 */
	include_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	include_once( 'lib/utils.php' );

	/**
	 * Include plugin cron events
	 */
	include_once( 'modules/cron-api-key-checker.php' );
	include_once( 'modules/cron-api-key-activator.php' );

	/**
	 * Include plugin activation hooks
	 */
	include_once( 'modules/plugin-activation.php' );

	/**
	 * Load plugin
	 */
	add_action( 'plugins_loaded', 'mpfy_pro_load', 1400 );
} else {
	add_action( 'admin_notices', 'mpfy_pro_plugin_conflict' );	
}

/**
 * Load WC AM client
 */
function mpfy_pro_wc_am_client() {
	/**
	 * Load WC_AM product ID constant.
	 */
	require_once( plugin_dir_path( __FILE__ ) . 'mpfy-api-manager-product-id.php' );

	/**
	 * Load WC_AM_Client class if it exists.
	 */
	if ( ! class_exists( 'WC_AM_Client_25' ) ) {
		require_once( plugin_dir_path( __FILE__ ) . 'wc-am-client.php' );
	}

	/**
	 * Instantiate WC_AM_Client class object if the WC_AM_Client class is loaded.
	 */
	if ( class_exists( 'WC_AM_Client_25' ) ) {
		global $wcam_lib;

		/**
		 * This file is only an example that includes a plugin header, and this code used to instantiate the client object. The variable $wcam_lib
		 * can be used to access the public properties from the WC_AM_Client class, but $wcam_lib must have a unique name. To find data saved by
		 * the WC_AM_Client in the options table, search for wc_am_client_{product_id}, so in this example it would be wc_am_client_13.
		 *
		 * All data here is sent to the WooCommerce API Manager API, except for the $software_title, which is used as a title, and menu label, for
		 * the API Key activation form the client will see.
		 *
		 * ****
		 * NOTE
		 * ****
		 * If $product_id is empty, the customer can manually enter the product_id into a form field on the activation screen.
		 *
		 * @param string $file             Must be __FILE__ from the root plugin file, or theme functions, file locations.
		 * @param int    $product_id       Must match the Product ID number (integer) in the product.
		 * @param string $software_version This product's current software version.
		 * @param string $plugin_or_theme  'plugin' or 'theme'
		 * @param string $api_url          The URL to the site that is running the API Manager. Example: https://www.toddlahman.com/
		 * @param string $software_title   The name, or title, of the product. The title is not sent to the API Manager APIs, but is used for menu titles.
		 * @param string $text_domain      The plugin's text domain.
		 *
		 * Example:
		 *
		 * $wcam_lib = new WC_AM_Client_25( $file, $product_id, $software_version, $plugin_or_theme, $api_url, $software_title, $text_domain );
		 */

		$wcam_lib = new WC_AM_Client_25( __FILE__, MAPIFY_AM_PRODUCT_ID, '4.7.1', 'plugin', 'https://mapifypro.com', 'MapifyPro', 'mpfy' );
	}
}

/**
 * Load the plugin.
 */
function mpfy_pro_load() {	
	/**
	 * Bail out if the API key is not activated.
	 * But if there are an API key, then we should try to activate the API key with the daily cron.
	 * The cron / scheduled event will be named `mpfy_api_key_activator`
	 */
	if ( ! mpfy_api_is_activated() ) {
		do_action( 'mpfy_plugin_failed_to_load' );
		return;
	}
		
	// Load plugin files
	include_once( 'load.php' );

	// Successfully loaded hook
	do_action( 'mpfy_plugin_loaded' );
}

/**
 * Message about the plugin conflict
 */
function mpfy_pro_plugin_conflict() {
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