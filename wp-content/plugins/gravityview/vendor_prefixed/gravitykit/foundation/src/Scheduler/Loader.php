<?php

namespace GravityKit\GravityView\Foundation\Scheduler;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

// Resolve the bundled Action Scheduler across vendor/, Strauss vendor_prefixed/, and
// standalone layouts without being fooled by an ancestor folder named like "vendor…".
// Every load goes through is_readable + include_once so a file that is missing or
// half-written during a plugin update (the window after the check, before the load)
// degrades to "background jobs paused" (see Core::init) instead of a site-wide fatal.
$resolver = __DIR__ . '/VendorPathResolver.php';

if ( is_readable( $resolver ) ) {
	include_once $resolver;
}

if ( class_exists( VendorPathResolver::class, false ) ) {
	$action_scheduler = VendorPathResolver::resolve( __DIR__ );

	// Must load early, before the 'plugins_loaded' action at priority 0.
	if ( null !== $action_scheduler && is_readable( $action_scheduler ) ) {
		include_once $action_scheduler;
	}
}

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
