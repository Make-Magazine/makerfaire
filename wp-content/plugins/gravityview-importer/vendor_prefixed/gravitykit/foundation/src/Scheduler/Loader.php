<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Scheduler;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

$dir_path      = __DIR__;
$vendor_folder = 'vendor';
$pos           = strpos( $dir_path, $vendor_folder );
$in_vendor     = false !== $pos;

// It is autoloaded by Composer and needs to be required early, before the 'plugins_loaded' action with priority 0.
$vendor_path = $in_vendor
	? substr( $dir_path, 0, $pos + strlen( $vendor_folder ) )
	: dirname( __DIR__, 2 ) . '/vendor';

require_once $vendor_path . '/woocommerce/action-scheduler/action-scheduler.php';

// Scheduler utility functions (gk_scheduler_should_continue, gk_scheduler_checkpoint, etc.).
// Composer's files autoload handles this for standalone Foundation, but consuming plugins
// using Strauss may strip the files entry. Each function has a function_exists() guard.
require_once __DIR__ . '/functions.php';
