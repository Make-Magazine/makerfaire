<?php
/**
 * Backward-compatibility shim for the legacy bootstrap entry point.
 *
 * This file was moved in GravityView 3.0. GravityView classes and
 * functions load automatically. You no longer need to require this file.
 *
 * For safety, if a caller has defined GRAVITYVIEW_DIR and GRAVITYVIEW_FILE
 * and required this file before GravityView has fully bootstrapped, this
 * shim replicates the historical side effects (Composer autoload + Foundation
 * registration) so the bootstrap can complete.
 *
 * @deprecated 3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( function_exists( '_deprecated_file' ) ) {
	_deprecated_file(
		plugin_basename( __FILE__ ),
		'3.0',
		'',
		'This file was moved in GravityView 3.0 and will be removed in a future release. GravityView classes and functions load automatically. You no longer need to require them.'
	);
}

if ( defined( 'GV_PLUGIN_VERSION' ) ) {
	return;
}

if ( ! defined( 'GRAVITYVIEW_DIR' ) || ! defined( 'GRAVITYVIEW_FILE' ) ) {
	return;
}

if ( file_exists( GRAVITYVIEW_DIR . 'vendor/autoload.php' ) ) {
	require_once GRAVITYVIEW_DIR . 'vendor/autoload.php';
}

if ( file_exists( GRAVITYVIEW_DIR . 'vendor_prefixed/autoload.php' ) ) {
	require_once GRAVITYVIEW_DIR . 'vendor_prefixed/autoload.php';
}

if ( class_exists( 'GravityKit\\GravityView\\Foundation\\Core' ) ) {
	GravityKit\GravityView\Foundation\Core::register( GRAVITYVIEW_FILE );
}
