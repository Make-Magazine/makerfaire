<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by The GravityKit Team on 11-September-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\State;

use WP_User;

/**
 * Creates state manager instances with caching support.
 *
 * @since 1.3.0
 */
final class StateManagerFactory {
	/**
	 * Cache of global state managers by option name.
	 *
	 * @since 1.3.0
	 *
	 * @var array<string, GlobalStateManager>
	 */
	private static $global_managers = [];

	/**
	 * Cache of user state managers by user ID and meta key.
	 *
	 * @since 1.3.0
	 *
	 * @var array<string, UserStateManager>
	 */
	private static $user_managers = [];


	/**
	 * Creates or returns a cached global state manager instance.
	 *
	 * @since 1.3.0
	 *
	 * @param string $option_name (optional) Option name to use for storage. Default: `gk_state`.
	 *
	 * @return GlobalStateManager The global state manager instance.
	 */
	public static function make_global( $option_name = 'gk_state' ) {
		if ( isset( self::$global_managers[ $option_name ] ) ) {
			return self::$global_managers[ $option_name ];
		}

		self::$global_managers[ $option_name ] = new GlobalStateManager( $option_name );

		return self::$global_managers[ $option_name ];
	}

	/**
	 * Creates or returns a cached user state manager instance.
	 *
	 * @since 1.3.0
	 *
	 * @param WP_User|null $user     (optional) User object. Default: current user.
	 * @param string       $meta_key (optional) Meta key to use for storage. Default: `gk_state`.
	 *
	 * @return UserStateManager The user state manager instance.
	 */
	public static function make_user( ?WP_User $user = null, $meta_key = 'gk_state' ) {
		$target_user = $user ?: wp_get_current_user();
		$user_id     = $target_user->exists() ? $target_user->ID : 0;

		// Create cache key from user ID and meta key.
		$cache_key = $user_id . ':' . $meta_key;

		if ( isset( self::$user_managers[ $cache_key ] ) ) {
			return self::$user_managers[ $cache_key ];
		}

		self::$user_managers[ $cache_key ] = new UserStateManager( $user, $meta_key );

		return self::$user_managers[ $cache_key ];
	}

	/**
	 * Clears all cached state manager instances.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public static function clear_cache(): void {
		self::$global_managers = [];
		self::$user_managers   = [];
	}
}
