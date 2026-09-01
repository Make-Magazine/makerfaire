<?php
/**
 * Post Title field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Post_Title class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * Add custom options for date fields
 */
class PostTitle extends \GravityView_Field {

	var $name = 'post_title';

	var $is_searchable = true;

	var $search_operators = ['is', 'isnot', 'contains', 'starts_with', 'ends_with'];

	/** @see GF_Field_Post_Title */
	var $_gf_field_class_name = 'GF_Field_Post_Title';

	var $group = 'post';

	var $icon = 'dashicons-edit';

	public function __construct() {
		$this->label = esc_html__( 'Post Title', 'gk-gravityview' );
		parent::__construct();
	}

	public function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id ) {

		if ( 'edit' === $context ) {
			return $field_options;
		}

		$this->add_field_support( 'link_to_post', $field_options );

		$this->add_field_support( 'dynamic_data', $field_options );

		return $field_options;
	}
}
