<?php
/**
 * Singleton trait for classes that need to implement the singleton pattern.
 */

namespace GravityKit\GravityView\Foundation\Scheduler\Traits;

use Exception;

trait SingletonTrait {

	/**
	 * Registry of class instances.
	 *
	 * @since 1.12.0
	 *
	 * @var array<string, object>
	 */
	private static $instances = [];

	/**
	 * Returns class instance.
	 *
	 * @since 1.12.0
	 *
	 * @return static
	 */
	public static function get_instance() {
		$class = static::class;
		if ( ! isset( self::$instances[ $class ] ) ) {
			self::$instances[ $class ] = new $class();
		}

		/** @var static */
		return self::$instances[ $class ];
	}

	/**
	 * Protected constructor to prevent direct instantiation.
	 * Calls init() for custom initialization.
	 *
	 * @since 1.12.0
	 */
	protected function __construct() {
	}

	/**
	 * Protected clone method to prevent cloning of the instance.
	 *
	 * @since 1.12.0
	 */
	protected function __clone() {}

	/**
	 * Prevent unserializing of the instance.
	 *
	 * @since 1.12.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing is forbidden.', 'gk-gravityview' ), '%ver%' );
	}
}
