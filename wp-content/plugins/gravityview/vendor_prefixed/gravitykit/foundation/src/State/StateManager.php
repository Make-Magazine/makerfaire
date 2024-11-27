<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by gravityview on 26-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\State;

/**
 * Represents a StateManager that manages values based on a key.
 *
 * @since 1.2.14
 */
interface StateManager {
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
	public function add( string $key, $value = null ): void;

	/**
	 * Returns whether the state key is registered.
	 *
	 * @since 1.2.14
	 *
	 * @param string $key The key of the state.
	 *
	 * @return bool Whether the state key is registered.
	 */
	public function has( string $key ): bool;

	/**
	 * Returns the value for the provided state key. Returns the $default value if it is not set or `null`.
	 *
	 * @since 1.2.14
	 *
	 * @param string      $key     The key of the state.
	 * @param string|null $default The default value to return if the key is not set.
	 *
	 * @return mixed The value.
	 */
	public function get( string $key, $default = null );

	/**
	 * Removes the value for the provided key.
	 *
	 * @since 1.2.14
	 *
	 * @param string $key The key to remove.
	 *
	 * @return void
	 */
	public function remove( string $key ): void;

	/**
	 * Returns an iterable (like an array) of key => value pairs.
	 *
	 * @since 1.2.14
	 *
	 * @return array<string, mixed> The result.
	 */
	public function all(): array;
}
