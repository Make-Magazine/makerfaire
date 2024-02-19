<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * The standalone plugin checker.
 * - Will disable this build-in module if there is PretyRoutes plugin activated.
 * - Show notification to deactivate PretyRoutes plugin if activated.
 * - Will prevent PretyRoutes plugin to be activated.
 */
include_once( 'standalone-plugin-checker.php' );

/**
 * Stop here if there is standalone prettyroutes plugin installed
 */
if ( mpfy_get_conflict_status( 'prettyroutes' ) ) {
	return;
}

/**
 * Define prettyroutes variables
 */
define( 'PRETTYROUTES_PLUGIN_FILE' , __FILE__ );
define( 'PRETTYROUTES_PLUGIN_DIR', dirname( PRETTYROUTES_PLUGIN_FILE ) );
define( 'PRETTYROUTES_PLUGIN_DIR_PATH', plugin_dir_path( PRETTYROUTES_PLUGIN_FILE ) );
define( 'PRETTYROUTES_PLUGIN_DIR_URL', plugin_dir_url( PRETTYROUTES_PLUGIN_FILE ) );
define( 'PRETTYROUTES_PLUGIN_VERSION' , MAPIFY_PLUGIN_VERSION );
define( 'PRETTYROUTES_PLUGIN_INTERNAL' , true );

/**
 * Load prettyroutes
 */
include_once( 'core.php' );
include_once( 'updater.php' );