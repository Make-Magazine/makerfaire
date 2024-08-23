<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by GravityKit on 18-July-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityRevisions\Foundation\State;

/**
 * State manager backed by an array.
 *
 * @used-by UserStateManager
 *
 * @since   1.2.14
 */
final class ArrayStateManager implements StateManager {
	/**
	 * Contains the state by key/value pairs.
     *
	 * @since 1.2.14
	 *
	 * @var array
	 */
	private $state;

	/**
	 * Creates an instance with possible values.
     *
	 * @since 1.2.14
	 *
	 * @param array $state The initial state.
	 *
	 * @return void
	 */
	public function __construct( array $state = [] ) {
		$this->state = $state;
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
	 * @return void
	 */
	public function add( string $key, $value = null ): void {
		$this->state[ $key ] = $value;
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
		return array_key_exists( $key, $this->state );
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
		if ( ! $this->has( $key ) ) {
			return $default;
		}

		return $this->state[ $key ] ?? $default;
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
		unset( $this->state[ $key ] );
	}

	/**
	 * Returns an iterable (like an array) of key => value pairs.
	 *
	 * @since 1.2.14
	 *
	 * @return array<string, mixed> The result.
	 */
	public function all(): array {
		return $this->state;
	}
}
