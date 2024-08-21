<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by gravityview on 14-August-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\State;

use WP_User;

/**
 * A state manager scoped to a provided user.
 *
 * @since 1.2.14
 */
final class UserStateManager implements StateManager {
	/**
	 * The user.
     *
	 * @since 1.2.14
	 * @var WP_User|null
	 */
	private $user;

	/**
	 * The meta key used on the user object that stores the state.
     *
	 * @since 1.2.14
	 */
	private const META_KEY = 'gk_state';

	/**
	 * Internal state that is managed by an array state.
     *
	 * @since 1.2.14
	 * @var ArrayStateManager
	 */
	private $internal_state;

	/**
	 * Initializes the manager.
     *
	 * @since 1.2.14
	 *
	 * @param WP_User|null $user An (option) user object.
	 */
	public function __construct( WP_User $user = null ) {
		$this->set_user( $user );
		$this->internal_state = new ArrayStateManager();
		$this->initialize();
	}

	/**
	 * Adds the key to the state manager.
	 *
	 * Note: overwrites the value if the key already exists.
	 *
	 * @since 1.2.14
	 *
	 * @param string $key   The key of the state.
	 * @param mixed  $value The (optional) value of the state.
	 */
	public function add( string $key, $value = null ): void {
		$this->internal_state->add( $key, $value );
		$this->save();
	}

	/**
	 * Returns whether the state key is registered.
	 *
	 * @since 1.2.14
	 *
	 * @param string $key The key of the state.
	 *
	 * @return bool Whether the state key is registered.
	 */
	public function has( string $key ): bool {
		return $this->internal_state->has( $key );
	}

	/**
	 * Returns the value for the provided state key. Returns the $default value if it is not set or `null`.
	 *
	 * @param string      $key The key of the state.
	 * @param string|null $default The default value to return if the key is not set.
	 *
	 * @return mixed The value.
	 */
	public function get( string $key, $default = null ) {
		return $this->internal_state->get( $key, $default );
	}

	/**
	 * Removes the value for the provided key.
	 *
	 * @since 1.2.14
	 *
	 * @param string $key The key to remove.
	 *
	 * @return void
	 */
	public function remove( string $key ): void {
		$this->internal_state->remove( $key );
		$this->save();
	}

	/**
	 * Retrieves the current stored state for the user.
     *
	 * @since 1.2.14
	 *
	 * @return void
	 */
	private function initialize(): void {
		if ( ! $this->user ) {
			$this->internal_state = new ArrayStateManager();

			return;
		}

		$result               = get_user_meta( $this->user->ID, self::META_KEY, true );
		$this->internal_state = new ArrayStateManager( $result ?: [] );
	}

	/**
	 * Persists the current state to the user meta.
     *
	 * @since 1.2.14
	 *
	 * @return void
	 */
	private function save(): void {
		if ( ! $this->user ) {
			return;
		}

		$state = $this->internal_state->all();
		update_user_meta( $this->user->ID, self::META_KEY, $state, null );
	}

	/**
	 * Sets the current user.
	 *
	 * @since 1.2.14
	 *
	 * @param WP_User|null $user The user.
	 *
	 * @return void
	 */
	private function set_user( ?WP_User $user ): void {
		if ( ! $user ) {
			$user = wp_get_current_user();
		}

		if ( ! $user || ! $user->exists() ) {
			return;
		}

		$this->user = $user;
	}

	/**
	 * Returns an iterable (like an array) of key => value pairs.
	 *
	 * @since 1.2.14
	 *
	 * @return array<string, mixed> The result.
	 */
	public function all(): array {
		return $this->internal_state->all();
	}
}
