<?php
/**
 * Post Excerpt field type.
 *
 * PSR-4 migration of the legacy GravityView_Post_Excerpt class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

class PostExcerpt extends \GravityView_Field {

	var $name = 'post_excerpt';

	var $is_searchable = true;

	var $search_operators = ['contains', 'is', 'isnot', 'starts_with', 'ends_with'];

	var $_gf_field_class_name = 'GF_Field_Post_Excerpt';

	var $group = 'post';

	var $icon = 'dashicons-format-quote';

	public function __construct() {
		$this->label = esc_html__( 'Post Excerpt', 'gk-gravityview' );
		parent::__construct();
	}

	public function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id ) {

		unset( $field_options['show_as_link'] );

		if ( 'edit' === $context ) {
			return $field_options;
		}

		$this->add_field_support( 'dynamic_data', $field_options );

		return $field_options;
	}
}
