<?php
/**
 * Add GravityView integration to LifterLMS.
 *
 * @since 2.20
 * @since 3.0.0 Migrated to PSR-4 namespace.
 * @license GPL2+
 *
 * @package GravityKit\GravityView\Integration
 */

namespace GravityKit\GravityView\Integration;

/**
 * Registers the GravityView integration with LifterLMS.
 *
 * Unlike most integrations, LifterLMS requires a class name string passed to the
 * `lifterlms_integrations` filter. That class must extend `LLMS_Abstract_Integration`,
 * which only exists when LifterLMS is active. The inner class is defined inside the
 * filter callback so it is only parsed when LifterLMS is loaded.
 *
 * @since 2.20
 * @since 3.0.0 Migrated to PSR-4 namespace.
 */
final class LifterLMS extends AbstractPluginHooks implements EarlyHookRegistration {

	/**
	 * @since 2.20
	 * @var string
	 */
	protected $function_name = 'llms';

	/**
	 * Register the GravityView integration filter before LifterLMS caches its list.
	 *
	 * Unlike most integrations, the `lifterlms_integrations` filter is read by
	 * LifterLMS at `init` priority 1 — earlier than the `wp_loaded` hook that
	 * {@see AbstractPluginHooks} uses to wire integration hooks. LifterLMS caches
	 * the filtered list in its `LLMS_Integrations` singleton, so a callback added
	 * at `wp_loaded` arrives too late and the GravityView row never appears under
	 * LifterLMS → Settings → Integrations.
	 *
	 * {@see AbstractPluginHooks::register_all()} invokes this at `plugins_loaded`,
	 * guaranteeing the filter is registered before `init`. The filter is harmless
	 * when LifterLMS is inactive — nothing applies it.
	 *
	 * @since 3.1.0
	 *
	 * @return void
	 */
	public static function register_early() {
		add_filter( 'lifterlms_integrations', [ self::class, 'register_integration' ], 20 );
	}

	/**
	 * Register the GravityView integration class with LifterLMS.
	 *
	 * The LLMS_Integration_GravityView class is defined on demand so that
	 * LLMS_Abstract_Integration is guaranteed to exist when PHP parses it — the
	 * filter only fires while LifterLMS is active.
	 *
	 * @since 2.20
	 * @since 3.0.0 Refactored from standalone file-level callback.
	 * @since 3.1.0 Converted to a static callback registered at `plugins_loaded` via register_early().
	 *
	 * @param array $integrations LifterLMS integration class names.
	 *
	 * @return array
	 */
	public static function register_integration( $integrations = [] ) {
		if ( ! class_exists( 'LLMS_Integration_GravityView' ) ) {
			require_once __DIR__ . '/LifterLMS/Integration.php';
		}

		$integrations[] = 'LLMS_Integration_GravityView';

		return $integrations;
	}
}
