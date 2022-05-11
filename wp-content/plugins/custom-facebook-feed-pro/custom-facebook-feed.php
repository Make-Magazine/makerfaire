<?php
/*
Plugin Name: Custom Facebook Feed Pro Smash
Plugin URI: https://smashballoon.com/custom-facebook-feed
Description: Add a completely customizable Facebook feed to your WordPress site
Version: 4.0.6
Author: Smash Balloon
Author URI: http://smashballoon.com/
*/
/*
Copyright 2021 Smash Balloon (email: hey@smashballoon.com)
This program is paid software; you may not redistribute it under any
circumstances without the expressed written consent of the plugin author.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'CFFVER', '4.0.6' );
define( 'CFFWELCOME_VER', '3.17' );
define( 'WPW_SL_STORE_URL', 'https://smashballoon.com/' );
define( 'WPW_SL_ITEM_NAME', 'Custom Facebook Feed WordPress Plugin Smash' ); //*!*Update Plugin Name at top of file*!*

// The ID of the product. Used for renewals
$cff_download_id = 13384; //210, 299, 300, 13384

// Plugin Folder Path.
if ( ! defined( 'CFF_PLUGIN_DIR' ) ) {
	define( 'CFF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// Plugin Folder URL.
if ( ! defined( 'CFF_PLUGIN_URL' ) ) {
	define( 'CFF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'CFF_DBVERSION' ) ) {
	define( 'CFF_DBVERSION', '2.2' );
}

if ( ! defined( 'CFF_UPLOADS_NAME' ) ) {
	define( 'CFF_UPLOADS_NAME', 'sb-facebook-feed-images' );
}

// Name of the database table that contains instagram posts
if ( ! defined( 'CFF_POSTS_TABLE' ) ) {
	define( 'CFF_POSTS_TABLE', 'cff_posts' );
}

// Name of the database table that contains feed ids and the ids of posts
if ( ! defined( 'CFF_FEEDS_POSTS_TABLE' ) ) {
	define( 'CFF_FEEDS_POSTS_TABLE', 'cff_feeds_posts' );
}

if ( ! defined( 'CFF_MAX_RECORDS' ) ) {
	define( 'CFF_MAX_RECORDS', 200 );
}

if ( ! defined( 'CFF_MINIMUM_WALL_VERSION' ) ) {
	define( 'CFF_MINIMUM_WALL_VERSION', '1.0.3' );
}

// Plugin File.
if ( ! defined( 'CFF_FILE' ) ) {
    define( 'CFF_FILE',  __FILE__ );
}

if ( ! defined( 'CFF_FILE' ) ) {
	define( 'CFF_PLUGIN_BASE', plugin_basename( CFF_FILE ) );
}

if ( ! defined( 'CFF_FEED_LOCATOR' ) ) {
    define( 'CFF_FEED_LOCATOR', 'cff_facebook_feed_locator' );
}
if ( ! defined( 'CFF_BUILDER_DIR' ) ) {
    define( 'CFF_BUILDER_DIR', CFF_PLUGIN_DIR . 'admin/builder/' );
}

if ( ! defined( 'CFF_BUILDER_URL' ) ) {
    define( 'CFF_BUILDER_URL', CFF_PLUGIN_URL . 'admin/builder/' );
}

if ( ! defined( 'CFF_VIEWS_DIR' ) ) {
    define( 'CFF_VIEWS_DIR', CFF_PLUGIN_DIR . 'admin/views/' );
}

if ( ! defined( 'CFF_VIEWS_URL' ) ) {
    define( 'CFF_VIEWS_URL', CFF_PLUGIN_URL . 'admin/views/' );
}

/**
 * Check PHP version
 *
 * Check for minimum PHP 5.6 version
 *
 * @since 3.18
*/
if ( version_compare( phpversion(), '5.6', '<' ) ) {
    if( !function_exists( 'cff_check_php_notice' ) ){
        include CFF_PLUGIN_DIR . 'admin/enqueu-script.php';
        function cff_check_php_notice(){
            $include_revert = ( version_compare( phpversion(), '5.6', '<' ) &&  version_compare( phpversion(), '5.3', '>' ) );

            $revert_url = ''; $plugin_name = strtolower(WPW_SL_ITEM_NAME);
            if (strpos($plugin_name , 'personal') !== false)  $revert_url = 'https://smashballoon.com/wp-content/uploads/revert/CFF-3.17.1-Personal.zip';
            if (strpos($plugin_name , 'business') !== false)  $revert_url = 'https://smashballoon.com/wp-content/uploads/revert/CFF-3.17.1-Business.zip';
            if (strpos($plugin_name , 'developer') !== false)  $revert_url = 'https://smashballoon.com/wp-content/uploads/revert/CFF-3.17.1-Developer.zip';
            if (strpos($plugin_name , 'smash') !== false)  $revert_url = 'https://smashballoon.com/wp-content/uploads/revert/CFF-3.17.1-Smash.zip';
            ?>
                <div class="notice notice-error">
                    <div>
                        <p><strong><?php echo esc_html__('Important:','custom-facebook-feed') ?> </strong><?php echo esc_html__('Your website is using an outdated version of PHP. The Custom Facebook Feed plugin requires PHP version 5.6 or higher and so has been temporarily deactivated.','custom-facebook-feed') ?></p>

                        <p>
                            <?php
                            echo esc_html__('To continue using the plugin','custom-facebook-feed') . ', ';

                            if($include_revert):
                                echo esc_html__('either use the button below to revert back to the previous version','custom-facebook-feed') . ', ';
                            else:
                                echo sprintf( __('you can either manually reinstall the previous version of the plugin (%s) ','custom-facebook-feed' ), '<a href="'.$revert_url.'">'. __( 'download', 'custom-facebook-feed' ).'</a>' );
                            endif;

                            echo esc_html__('or contact your host to request that they upgrade your PHP version to 5.6 or higher.','custom-facebook-feed');
                            ?>
                        </p>

                        <?php
                            if($include_revert):
                        ?>
                            <p><button data-plugin="<?php echo $revert_url ?>" data-type="plugin" class="cff-notice-admin-btn status-download button button-primary"><?php echo esc_html__('Revert Back to Previous Version','custom-facebook-feed') ?></button></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php
        }
    }
    add_action( 'admin_notices', 'cff_check_php_notice' );
    return; //Stop until PHP version is fixed
}

if ( function_exists('cff_main') || function_exists('display_cff') ){
    if ( isset( $_POST['oth'] ) ) {
        return;
    } else {
	    wp_die( "Please deactivate the free version of the Custom Facebook Feed plugin before activating this version.<br /><br />Back to the WordPress <a href='".get_admin_url(null, 'plugins.php')."'>Plugins page</a>." );

    }

} else {
    include dirname( __FILE__ ) .'/cff-init.php';
}

include CFF_PLUGIN_DIR . 'inc/Custom_Facebook_Feed_Pro.php';

function cff_main_pro() {
	return CustomFacebookFeed\Custom_Facebook_Feed_Pro::instance();
}
cff_main_pro();
