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

// ── Preemptive AS initialization to prevent old-copy hijacking ────────────
//
// Action Scheduler uses a version resolution system: each plugin that bundles
// AS registers its version at plugins_loaded priority 0, then the highest
// version is initialized at priority 1.
//
// There are two patterns for loading AS:
//
// Pattern A (correct, recommended by AS docs):
// require_once action-scheduler.php during plugin file loading, BEFORE
// plugins_loaded fires. The p0 registration callback is in place before
// the hook starts. We use this pattern in this Loader.php.
//
// Pattern B (problematic):
// add_action('plugins_loaded', 'load_as', -10) — loads action-scheduler.php
// from inside a plugins_loaded callback at a negative priority. The p0
// registration callback is added DURING plugins_loaded execution.
//
// The problem: AS < 3.2.1 has a bug in its theme support block. It checks
// did_action('plugins_loaded') without doing_action('plugins_loaded'). WordPress
// increments did_action at the START of do_action, so during any plugins_loaded
// callback, did_action('plugins_loaded') is already 1. When a Pattern B plugin
// (e.g., WP Mail SMTP Pro) loads an old AS copy at priority -10, the broken
// theme support block fires immediately — initializing the old classes and
// setting the autoloader to the old copy's paths, before version resolution at
// priority 1 can pick the newest version.
//
// The bug was fixed in AS 3.2.1 by adding a doing_action('plugins_loaded') guard:
// @see https://github.com/woocommerce/action-scheduler/issues/714
// @see https://github.com/woocommerce/action-scheduler/pull/715
//
// Fix: at plugins_loaded priority -11 (before any Pattern B plugin at -10), fire
// all already-registered p0 version callbacks (from Pattern A plugins) and call
// initialize_latest_version(). This initializes the correct classes, making
// class_exists('ActionScheduler') true so that old theme support blocks skip.
//
// Tradeoff: if a NEWER AS is loaded via Pattern B (negative-priority callback),
// its p0 callback isn't registered yet at -11 and it misses the resolution.
// This is acceptable because:
// - Pattern B is an anti-pattern; AS docs recommend Pattern A.
// - A newer AS (>= 3.2.1) has the doing_action guard, so its theme support
// block won't fire even without our fix.
// - AS's own fail-safe (theme support block) exhibits the same version-lock.
// - Pattern A plugins (WooCommerce, etc.) all register before plugins_loaded
// and are fully visible at -11.
add_action(
	'plugins_loaded',
	static function () {
		if ( ! class_exists( 'ActionScheduler_Versions', false ) || class_exists( 'ActionScheduler', false ) ) {
			return;
		}

		// Fire ALL version registration callbacks hooked at priority 0 so
		// initialize_latest_version can pick the true winner — not just ours.
		global $wp_filter;

		if ( isset( $wp_filter['plugins_loaded']->callbacks[0] ) ) {
			foreach ( $wp_filter['plugins_loaded']->callbacks[0] as $callback ) {
				$name = is_string( $callback['function'] ) ? $callback['function'] : '';

				if ( 0 === strpos( $name, 'action_scheduler_register_' ) ) {
					call_user_func( $callback['function'] );
				}
			}
		}

		$versions = \ActionScheduler_Versions::instance();

		if ( $versions->latest_version() ) {
			\ActionScheduler_Versions::initialize_latest_version();
		}
	},
	-11
);
