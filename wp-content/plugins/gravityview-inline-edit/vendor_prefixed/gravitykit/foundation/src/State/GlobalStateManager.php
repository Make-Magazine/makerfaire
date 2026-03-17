<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\State;

use GravityKit\GravityEdit\Foundation\Exceptions\BaseException;

/**
 * A state manager scoped to global WP options.
 *
 * @since 1.3.0
 */
final class GlobalStateManager implements StateManager {
	/**
	 * The option name used to store the global state.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	private $option_name;

	/**
	 * Internal state that is managed by an array state.
	 *
	 * @since 1.3.0
	 *
	 * @var ArrayStateManager
	 */
	private $internal_state;


	/**
	 * Initializes the manager.
	 *
	 * @since 1.3.0
	 *
	 * @param string $option_name (optional) Option name to use for storage. Default: `gk_state`.
	 */
	public function __construct( $option_name = 'gk_state' ) {
		$this->option_name = $option_name;

		$this->internal_state = new ArrayStateManager();

		$this->initialize();
	}

	/**
	 * Adds the key to the state manager.
	 *
	 * @since 1.3.0
	 *
	 * @param string $key   The key of the state.
	 * @param mixed  $value (optional) State value. Default: `null`.
	 *
	 * @return void
	 * @throws BaseException When persistence fails.
	 */
	public function add( string $key, $value = null ): void {
		$this->internal_state->add( $key, $value );

		$this->save();
	}

	/**
	 * Returns whether the state key is registered.
	 *
	 * @since 1.3.0
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
	 * @since 1.3.0
	 *
	 * @param string      $key     The key of the state.
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
	 * @since 1.3.0
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
	 * Returns an iterable (like an array) of key => value pairs.
	 *
	 * @since 1.3.0
	 *
	 * @return array<string, mixed> The result.
	 */
	public function all(): array {
		return $this->internal_state->all();
	}

	/**
	 * Retrieves the current stored state from WP options.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	private function initialize(): void {
		$result = get_option( $this->option_name, [] );

		$this->internal_state = new ArrayStateManager( $result ?: [] );
	}

	/**
	 * Persists the current state to WP options.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 * @throws BaseException When update_option fails.
	 */
	private function save(): void {
		$state = $this->internal_state->all();

		if ( ! update_option( $this->option_name, $state ) ) {
			throw new BaseException(
				'state_save_failed',
				'Failed to save state to options table',
				[ 'option_name' => $this->option_name ]
			);
		}
	}
}
