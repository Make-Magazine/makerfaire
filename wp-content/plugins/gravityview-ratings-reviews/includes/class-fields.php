<?php
/**
 * Component that injects fields to GV admin-views.
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

class GravityView_Ratings_Reviews_Fields extends GravityView_Ratings_Reviews_Component {

	/**
	 * Callback when this component is loaded by the loader.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function load() {
		add_filter( 'gravityview_template_reviews_link_options',  array( $this, 'field_options' ) );
		add_filter( 'gravityview_entry_default_fields',           array( $this, 'add_ratings_reviews_fields' ), 10, 3 );
		add_filter( 'gravityview_template_paths',                 array( $this, 'add_template_paths' ) );
		add_filter( 'gravityview/template/fields_template_paths', array( $this, 'add_template_paths' ) );
	}

	/**
	 * Field options for comments_link field.
	 *
	 * @filter gravityview_template_comments_link_options
	 *
	 * @since 0.1.0
	 *
	 * @param  array $field_options
	 * @return array
	 */
	public function field_options( $field_options ) {

		// Always a link!
		unset( $field_options['show_as_link'], $field_options['search_filter'] );

		$field_options['no_comment_text'] = array(
			'type'  => 'text',
			'label' => __( 'No review text:', 'gravityview-ratings-reviews' ),
			'desc'  => __( 'Text to display when there are no reviews.', 'gravityview-ratings-reviews' ),
			'value' => __( 'No Review. Be the First!', 'gravityview-ratings-reviews'),
		);

		$field_options['one_comment_text'] = array(
			'type'  => 'text',
			'label' => __( 'One review text:', 'gravityview-ratings-reviews' ),
			'desc'  => __( 'Text to display when there is one review.', 'gravityview-ratings-reviews' ),
			'value' => __( '1 Review', 'gravityview-ratings-reviews'),
		);

		$field_options['more_comments_text'] = array(
			'type'  => 'text',
			'label' => __( 'More reviews text:', 'gravityview-ratings-reviews' ),
			'desc'  => sprintf( _x( 'Text to display when there are more than one reviews. %s is replaced by the number of reviews.', 'The placeholder symbol that will be replaced', 'gravityview-ratings-reviews' ), '<code>%</code>'),
			'value' => __( '% Reviews', 'gravityview-ratings-reviews'),
		);

		$field_options['show_average_rating'] = array(
			'type'  => 'checkbox',
			'label' => __( 'Show average rating (stars or votes)', 'gravityview-ratings-reviews' ),
			'value' => true,
		);

		return $field_options;
	}

	/**
	 * Adds fields for gv_ratings_reviews extension.
	 *
	 * @filter gravityview_entry_default_fields
	 *
	 * @since 0.1.0
	 *
	 * @param array        $fields Default fields
	 * @param string|array $form   Form ID or form object
	 * @param string       $zone   Either 'single', 'directory', 'header', 'footer'
	 *
	 * @return array
	 */
	public function add_ratings_reviews_fields( $fields, $form, $zone ) {
		$fields['reviews_link'] = array(
			'label' => esc_html__( 'Reviews Link', 'gravityview-ratings-reviews' ),
			'type'  => 'reviews_link',
			'icon'  => 'dashicons-star-half',
			'desc'	=> esc_html__('Display ratings and link to reviews.', 'gravityview-ratings-reviews'),
		);

		return $fields;
	}

	/**
	 * Adds template path for discussion fields.
	 *
	 * @since 0.1.0
	 *
	 * @param  array $var Default is directory in child theme at index 1, parent theme at 10, and plugin at 100
	 * @return array
	 */
	public function add_template_paths( $file_paths ) {
		$key_id = isset( $file_paths[ 505 ] ) ? : 506;
		$file_paths[ $key_id ] = $this->loader->templates_dir;
		$file_paths[ $key_id + 1 ] = $this->loader->templates_dir . 'fields';
		return $file_paths;
	}
}
