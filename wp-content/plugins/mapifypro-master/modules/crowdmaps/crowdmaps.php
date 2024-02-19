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
 * Stop here if there is standalone crowdmaps plugin installed
 */
if ( mpfy_get_conflict_status( 'crowdmaps' ) ) {
	return;
}

/**
 * Define crowdmaps variables
 */
define( 'CROWD_PLUGIN_FILE', __FILE__ );
define( 'CROWD_PLUGIN_DIR', dirname( CROWD_PLUGIN_FILE ) );
define( 'CROWD_PLUGIN_DIR_PATH', plugin_dir_path( CROWD_PLUGIN_FILE ) );
define( 'CROWD_PLUGIN_VERSION', MAPIFY_PLUGIN_VERSION );
define( 'CROWD_PLUGIN_INTERNAL', true );

/**
 * Load crowdmaps
 */
include_once( 'core.php' );
include_once( 'modules/plugin.php' );
