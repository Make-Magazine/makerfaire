<?php
/**
 * Contract for integrations that must register hooks before `wp_loaded`.
 *
 * @package GravityKit\GravityView\Integration
 * @license GPL2+
 * @author  GravityKit <hello@gravitykit.com>
 * @link    http://www.gravitykit.com
 *
 * @since 3.1.0
 */

namespace GravityKit\GravityView\Integration;

/**
 * Implemented by integrations whose host plugin reads a registration filter
 * *before* the `wp_loaded` action that {@see AbstractPluginHooks} normally uses
 * to wire integration hooks.
 *
 * {@see AbstractPluginHooks::register_all()} runs at `plugins_loaded` and calls
 * {@see EarlyHookRegistration::register_early()} on every implementer, so those
 * hooks are in place before `init` and `wp_loaded`.
 *
 * Example: LifterLMS reads `lifterlms_integrations` at `init` priority 1 and
 * caches the result in its `LLMS_Integrations` singleton. A callback added at
 * `wp_loaded` arrives too late, so the GravityView row must be registered early.
 *
 * @since 3.1.0
 */
interface EarlyHookRegistration {

	/**
	 * Register hooks that must be in place before `wp_loaded`.
	 *
	 * Called once, at `plugins_loaded`. Implementations should be safe to call
	 * even when the host plugin is inactive — the registered hook simply never
	 * fires — mirroring how integration filters are registered unconditionally.
	 *
	 * @since 3.1.0
	 *
	 * @return void
	 */
	public static function register_early();
}
