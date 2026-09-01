<?php
/**
 * Backward-compatibility shim.
 *
 * This file was moved in GravityView 3.0. It is intentionally empty.
 * GravityView classes and functions load automatically. You no longer
 * need to require GravityView files in your code.
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
