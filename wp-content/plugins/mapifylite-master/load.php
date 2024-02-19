<?php
$plugin_data = get_file_data(MAPIFY_PLUGIN_FILE, array('Plugin Name'=>'Plugin Name', 'Version'=>'Version'));

if ( ! defined( 'MAPIFY_PLUGIN_NAME' ) ) {
	define( 'MAPIFY_PLUGIN_NAME', $plugin_data['Plugin Name'] );
}

if ( ! defined( 'MAPIFY_PLUGIN_VERSION' ) ) {
	define( 'MAPIFY_PLUGIN_VERSION', $plugin_data['Version'] );
}

if ( ! defined( 'MAPIFY_PLUGIN_DIR' ) ) {
	define( 'MAPIFY_PLUGIN_DIR', dirname(MAPIFY_PLUGIN_FILE) );
}

if ( ! defined( 'MAPIFY_PLUGIN_DIR_PATH' ) ) {
	define( 'MAPIFY_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'MAPIFY_PLUGIN_DIR_URL' ) ) {
	define( 'MAPIFY_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
}

// The minimum Map Locations count to reach before we ask user to do the plugin review.
if ( ! defined( 'MAPIFY_MINIMUM_ITEMS_TO_REVIEW' ) ) {
	define( 'MAPIFY_MINIMUM_ITEMS_TO_REVIEW', 5 );
}

if ( ! defined('MPFY_IS_AJAX') ) {
	$is_ajax = (bool) ( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' );
	define('MPFY_IS_AJAX', $is_ajax);
}

/* Mapify General */
include_once('lib/utils.php');
if (!class_exists('Mpfy_Carbon_Video')) {
	include_once('lib/carbon-video.php');
}

include_once('core.php');
include_once('modules/plugin.php');
include_once('mapifypro-installer/mapifypro-installer.php');