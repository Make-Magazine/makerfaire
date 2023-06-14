<?php

namespace GravityKit\GravityMaps;

/**
 * Base class for GravityMaps components
 *
 * @since 0.1.0
 */
abstract class Component {
	/**
	 * Instance of component loader.
	 *
	 * @since 0.1.0
	 *
	 * @var Loader
	 */
	protected $loader;

	/**
	 * Constructor.
	 *
	 * Component doesn't need to implement __construct when extending this class.
	 *
	 * @since 0.1.0
	 *
	 * @param  object $extension Instance of GravityView_Ratings_Reviews_Loader
	 * @return void
	 */
	public function __construct( Loader $loader ) {
		$this->loader = $loader;
	}

	/**
	 * Callback method that component MUST implements.
	 *
	 * This method will be invoked by Loader.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	abstract public function load();
}
