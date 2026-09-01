<?php
/**
 * Post ID field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Post_ID class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * Add custom options for Post ID fields
 *
 * @since 1.7
 */
class PostId extends \GravityView_Field {

	var $name = 'post_id';

	var $is_searchable = true;

	var $search_operators = ['is', 'isnot', 'greater_than', 'less_than'];

	var $group = 'post';

	/**
	 * PostId constructor.
	 */
	public function __construct() {
		$this->label = esc_html__( 'Post ID', 'gk-gravityview' );
		parent::__construct();
	}

	public function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id ) {

		$this->add_field_support( 'link_to_post', $field_options );

		return $field_options;
	}
}
