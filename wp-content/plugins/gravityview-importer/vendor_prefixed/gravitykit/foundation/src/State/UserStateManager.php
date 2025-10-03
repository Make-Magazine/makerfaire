<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by The GravityKit Team on 11-September-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\State;

use WP_User;
use GravityKit\GravityImport\Foundation\Exceptions\BaseException;

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
	 *
	 * @var WP_User|null
	 */
	private $user;

	/**
	 * The meta key used on the user object that stores the state.
     *
	 * @since 1.2.14
	 * @since 1.3.0 Changed from const to private property.
	 *
	 * @var string
	 */
	private $meta_key = 'gk_state';

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
	 * @since 1.3.0 Added $meta_key parameter.
	 *
	 * @param WP_User|null $user (optional) User object.
	 * @param string       $meta_key (optional) Meta key to use for storage. Default: `gk_state`.
	 */
	public function __construct( ?WP_User $user = null, $meta_key = 'gk_state' ) {
		$this->meta_key       = $meta_key;
		$this->internal_state = new ArrayStateManager();

		$this->set_user( $user );
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
	 *
	 * @throws BaseException When persistence fails.
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
	 * @throws BaseException When persistence fails.
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

		$result               = get_user_meta( $this->user->ID, $this->meta_key, true );
		$this->internal_state = new ArrayStateManager( $result ?: [] );
	}

	/**
	 * Persists the current state to the user meta.
     *
	 * @since 1.2.14
	 *
	 * @return void
	 * @throws BaseException When user meta update fails.
	 */
	private function save(): void {
		if ( ! $this->user ) {
			throw new BaseException(
				'user_state_save_failed',
				'Cannot save state: no user provided',
				[ 'meta_key' => $this->meta_key ]
			);
		}

		$state = $this->internal_state->all();

		if ( ! update_user_meta( $this->user->ID, $this->meta_key, $state, null ) ) {
			throw new BaseException(
				'user_state_save_failed',
				'Failed to save user state to meta table',
				[
					'user_id'  => $this->user->ID,
					'meta_key' => $this->meta_key,
				]
			);
		}
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

		if ( ! $user->exists() ) {
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
