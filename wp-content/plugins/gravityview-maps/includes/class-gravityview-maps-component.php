<?php
/**
 * Base class of GravityView_Maps's component.
 *
 * @package   GravityView_Ratings_Reviews
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @since 0.1.0
 */
abstract class GravityView_Maps_Component {

	/**
	 * Instance of component loader.
	 *
	 * @since 0.1.0
	 *
	 * @var GravityView_Maps_Loader
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
	public function __construct( GravityView_Maps_Loader $loader ) {
		$this->loader = $loader;
	}

	/**
	 * Callback method that component MUST implements.
	 *
	 * This method will be invoked by GravityView_Maps_Loader.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	abstract public function load();


}
