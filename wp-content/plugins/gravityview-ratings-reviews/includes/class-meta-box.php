<?php
/**
 * Component that has responsibility to render meta box for discussion settings
 * in GravityView edit screen.
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

class GravityView_Ratings_Reviews_Meta_Box extends GravityView_Ratings_Reviews_Component {

	/**
	 * Callback when this component is loaded by the loader.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function load() {
		// Filter default settings for discussion-for-entry fields.
		add_filter( 'gravityview_default_args', array( $this, 'default_args' ) );

		// Adds the meta box.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

		$this->load_admin();
	}

	/**
	 * Modify default settings for new Views.
	 *
	 * @filter gravityview_default_args
	 *
	 * @since 0.1.0
	 *
	 * @param array $args Default settings for new Views
	 *
	 * @return array Modified default settings for new Views.
	 */
	public function default_args( $args ) {
		$args['allow_entry_reviews'] = array(
			'label'             => __( 'Enable entry reviews', 'gravityview-ratings-reviews' ),
			'type'              => 'checkbox',
			'group'	            => 'ratings_reviews',
			'value'             => 0,
			'show_in_shortcode' => true,
		);
		$args['hide_ratings'] = array(
			'label'             => __( 'Hide ratings fields', 'gravityview-ratings-reviews' ),
			'type'              => 'checkbox',
			'group'	            => 'ratings_reviews',
			'value'             => false,
			'show_in_shortcode' => false,
			'requires'          => 'allow_entry_reviews',
		);
		/**
		 * @since 1.3
		 */
		$args['allow_empty_reviews'] = array(
			'label'             => __( 'Allow empty review text', 'gravityview-ratings-reviews' ),
			'desc'              => __( 'Comments on reviews always require text.', 'gravityview-ratings-reviews' ),
			'type'              => 'checkbox',
			'group'	            => 'ratings_reviews',
			'value'             => false,
			'show_in_shortcode' => false,
			'requires'          => 'allow_entry_reviews',
		);
		// TODO: Hide when hide_ratings is enabled
		$args['limit_one_review_per_person'] = array(
			'label'             => __( 'Limit to one review per person per entry', 'gravityview-ratings-reviews' ),
			'type'              => 'checkbox',
			'group'	            => 'ratings_reviews',
			'value'             => true,
			'show_in_shortcode' => false,
			'requires'          => 'allow_entry_reviews',
		);
		// TODO: Hide when hide_ratings is enabled
		$args['entry_review_type'] = array(
			'label'             => __( 'Review type', 'gravityview-ratings-reviews' ),
			'type'              => 'select',
			'group'	            => 'ratings_reviews',
			'value'             => 'stars',
			'options' => array(
				'vote'  => __( 'Vote (&uarr;/&darr;)', 'gravityview-ratings-reviews' ),
				'stars' => __( '5-Star Rating', 'gravityview-ratings-reviews' ),
			),
			'show_in_shortcode' => true,
			'requires'          => 'allow_entry_reviews',
		);

		return $args;
	}

	/**
	 * Adds the meta box for entry's discussion.
	 *
	 * @since 0.1.0
	 *
	 * @param  object $post WP_Post
	 * @return void
	 */
	public function add_meta_box( $post ) {

		$m = array(
			'id' => 'ratings_reviews_entry',
			'title' => __( 'Ratings & Reviews', 'gravityview-ratings-reviews' ),
			'callback' => array( $this, 'render_meta_box' ),
			'icon-class' => 'dashicons-star-half',
			'file' => '',
			'callback_args' => '',
			'screen' => 'gravityview',
			'context' => 'side',
			'priority' => 'default',
		);

		if ( class_exists( 'GravityView_Metabox_Tab' ) ) {

			$metabox = new GravityView_Metabox_Tab( $m['id'], $m['title'], $m['file'], $m['icon-class'], $m['callback'], $m['callback_args'] );

			GravityView_Metabox_Tabs::add( $metabox );

		} else {

			add_meta_box( 'gravityview_'.$m['id'], $m['title'], $m['callback'], $m['screen'], $m['context'], $m['priority'] );

		}

	}

	/**
	 * Display the meta box.
	 *
	 * @since 0.1.0
	 *
	 * @param  object $post WP_Post
	 * @return void
	 */
	public function render_meta_box( $post ) {
		$settings = gravityview_get_template_settings( $post->ID );
		$defaults = GravityView_View_Data::get_default_args( false, 'ratings_reviews' );

		$current_settings = wp_parse_args( $settings, $defaults );

		include $this->loader->locate_template('meta-box.php');
	}


	/**
	 * Register admin scripts.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function register_scripts() {

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.js' : '.min.js';
		if ( ! wp_script_is( 'gv-ratings-reviews-admin', 'registered' ) ) {
			wp_register_script( 'gv-ratings-reviews-admin', $this->loader->js_url . "admin{$suffix}", array(), $this->loader->_version, true );
		}
	}

	/**
	 * Enqueue the admin scripts if needed.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function enqueue_when_needed() {
		if ( ! function_exists( 'gravityview' ) || ! gravityview()->request->is_admin( 'single' ) ) {
			return;
		}

		global $wp_scripts;

		$params = array(
			'screen_id' => 'gravityview',
		);

		wp_enqueue_script( 'gv-ratings-reviews-admin' );

		// Encode parameters for use in the script
		$exports = 'var GV_RATINGS_REVIEWS_ADMIN = ' . json_encode( $params );
		$wp_scripts->add_data( 'gv-ratings-reviews-admin', 'data', $exports );
	}
}
