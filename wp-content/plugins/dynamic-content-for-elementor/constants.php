<?php

define( 'DCE_VERSION', '1.16.9' );
define( 'DCE__FILE__', __FILE__ );
define( 'DCE_URL', plugins_url( '/', __FILE__ ) );
define( 'DCE_PATH', plugin_dir_path( __FILE__ ) );
define( 'DCE_PLUGIN_BASE', plugin_basename( DCE__FILE__ ) );
define( 'DCE_MINIMUM_ELEMENTOR_VERSION', '2.9.11' );
define( 'DCE_ELEMENTOR_PRO_VERSION_REQUIRED', '2.10.0' );
define( 'DCE_PHP_VERSION_REQUIRED', '7.0' );
define( 'DCE_PHP_VERSION_SUGGESTED', '7.3' );
define( 'DCE_OPTIONS', 'dyncontel_options' );
define( 'DCE_BACKUP_PATH', WP_CONTENT_DIR . '/backup' );
define( 'DCE_BACKUP_URL', site_url() . '/wp-content/backup' );
// License and update
define( 'DCE_LICENSE_URL', 'https://license.dynamic.ooo' );
$protocol = ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] || ( ! empty( $_SERVER['SERVER_PORT'] ) && 443 === $_SERVER['SERVER_PORT'] ) ) ? 'https://' : 'http://';
define( 'DCE_INSTANCE', str_replace( $protocol, '', get_bloginfo( 'wpurl' ) ) );
define( 'DCE_LICENSE', get_option( 'dce_license_key' ) );
