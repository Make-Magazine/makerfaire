<?php
/**
 * Base class of GravityView_Ratings_Reviews's component.
 *
 * @package   GravityView_Ratings_Reviews
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @since 0.1.0
 */

defined( 'ABSPATH' ) || exit;

abstract class GravityView_Ratings_Reviews_Component {

	/**
	 * Instance of component loader.
	 *
	 * @since 0.1.0
	 *
	 * @var GravityView_Ratings_Reviews_Loader
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
	public function __construct( GravityView_Ratings_Reviews_Loader $loader ) {
		$this->loader = $loader;
	}

	/**
	 * Callback method that component MUST implements.
	 *
	 * This method will be invoked by GravityView_Ratings_Reviews_Loader.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	abstract public function load();

	public function load_admin() {
		// Styles and scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue statics (JS and CSS).
	 *
	 * @action wp_enqueue_scripts
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		/**
		 * Fires before required scripts and styles are enqueued.
		 *
		 * @since 0.1.0
		 */
		do_action( 'gravityview_ratings_reviews_before_enqueue' );

		$this->register_scripts();
		$this->enqueue_when_needed();

		/**
		 * Fires after required scripts and styles are enqueued.
		 *
		 * @since 0.1.0
		 */
		do_action( 'gravityview_ratings_reviews_after_enqueue' );
	}


	/**
	 * Register admin scripts.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function register_scripts() {}

	/**
	 * Enqueue the admin scripts if needed.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function enqueue_when_needed() {}
}
