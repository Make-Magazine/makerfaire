<?php
/*
Plugin Name: WordPress Notification Bar
Plugin URI: http://seedprod.com/wordpress-notification-bar/
Description: Global Notification Bar for WordPress
Version:  1.3.8
Text Domain: wordpress-notification-bar
Domain Path: /languages
Author: SeedProd
Author URI: http://www.seedprod.com
License: GPLv2
Copyright 2012  John Turner (email : john@seedprod.com, twitter : @johnturner)
*/

/**
 * Init
 *
 * @package WordPress
 * @subpackage seed_wnb
 * @since 0.1.0
 */

/**
 * Default Constants
 */
define( 'SEED_WNB_SHORTNAME', 'seed_wnb' ); // Used to reference namespace functions.
define( 'SEED_WNB_FILE', 'wordpress-notification-bar/wordpress-notification-bar.php' ); // Used for settings link.
define( 'SEED_WNB_TEXTDOMAIN', 'wordpress-notification-bar' ); // Your textdomain
define( 'SEED_WNB_PLUGIN_NAME', __( 'WordPress Notification Bar', 'seed_wnb' ) ); // Plugin Name shows up on the admin settings screen.
define( 'SEED_WNB_VERSION', '1.3.8' ); // Plugin Version Number. Recommend you use Semantic Versioning http://semver.org/
define( 'SEED_WNB_REQUIRED_WP_VERSION', '3.0' ); // Required Version of WordPress
define( 'SEED_WNB_PLUGIN_PATH', plugin_dir_path( __FILE__ ) ); // Example output: /Applications/MAMP/htdocs/wordpress/wp-content/plugins/seed_wnb/
define( 'SEED_WNB_PLUGIN_URL', plugin_dir_url( __FILE__ ) ); // Example output: http://localhost:8888/wordpress/wp-content/plugins/seed_wnb/

/**
 * Upon activation of the plugin, see if we are running the required version and deploy theme in defined.
 *
 * @since 0.1.0
 */
function seed_wnb_activation( )
{
    if ( version_compare( get_bloginfo( 'version' ), SEED_WNB_REQUIRED_WP_VERSION, '<' ) ) {
        deactivate_plugins( __FILE__ );
        wp_die( sprintf( __( "WordPress %s and higher required. The plugin has now disabled itself. On a side note why are you running an old version :( Upgrade!", 'seed_wnb' ), SEED_WNB_REQUIRED_WP_VERSION ) );
    }
}
register_activation_hook( __FILE__, 'seed_wnb_activation' );

/**
 * Load Required Files
 */
require_once( 'framework/framework.php' );
require_once( 'inc/config.php' );
require_once( 'inc/class-plugin.php' );

//var_dump( $seed_wnb->get_options() );

/**
 * Load Translation
 */

function seed_wnb_load_textdomain() {
    load_plugin_textdomain( 'wordpress-notification-bar', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('plugins_loaded', 'seed_wnb_load_textdomain');
