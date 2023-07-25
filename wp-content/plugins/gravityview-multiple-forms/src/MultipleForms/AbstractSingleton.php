<?php

namespace GravityKit\MultipleForms;

/**
 * Class AbstractSingleton.
 *
 * @since 0.3
 *
 * @package GravityKit\MultipleForms
 */
abstract class AbstractSingleton {
	/**
	 * List of all instances created.
	 *
	 * @since 0.3
	 *
	 * @var array
	 */
	protected static $instances = [];

	/**
	 * AbstractSingleton constructor
	 * Don't extend this unless very specific usage.
	 *
	 * @since 0.3
	 */
	protected function __construct() {
		// Initializes the singleton, with a method that is always called.
		$this->register();
	}

	/**
	 * Creates the instance of the object that extends this abstract.
	 *
	 * @since 0.3
	 *
	 * @return mixed
	 */
	public static function instance() {
		if ( ! isset( static::$instances[ static::class ] ) ) {
			static::$instances[ static::class ] = new static();
		}

		return static::$instances[ static::class ];
	}

	/**
	 * Every singleton needs to have a register method, which will be called when the singleton is initialized.
	 *
	 * @since 0.3
	 *
	 * @return void
	 */
	abstract protected function register(): void;

	/**
	 * When dealing with Singletons we cannot clone.
	 *
	 * @since 0.3
	 */
	public function __clone() {

	}

	/**
	 * Singletons cannot be serialized.
	 *
	 * @since 0.3
	 */
	public function __wakeup() {

	}
}
