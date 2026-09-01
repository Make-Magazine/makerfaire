<?php
/**
 * Post Tags field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Post_Tags class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * Add custom options for date fields
 */
class PostTags extends \GravityView_Field {

	var $name = 'post_tags';

	var $is_searchable = true;

	var $search_operators = ['is', 'in', 'not in', 'isnot', 'contains'];

	var $_gf_field_class_name = 'GF_Field_Post_Tags';

	var $group = 'post';

	var $icon = 'dashicons-tag';

	public function __construct() {
		$this->label = esc_html__( 'Post Tags', 'gk-gravityview' );
		parent::__construct();
	}

	public function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id ) {

		if ( 'edit' === $context ) {
			return $field_options;
		}

		$this->add_field_support( 'dynamic_data', $field_options );
		$this->add_field_support( 'link_to_term', $field_options );
		$this->add_field_support( 'new_window', $field_options );

		$field_options['new_window']['requires'] = 'link_to_term';

		return $field_options;
	}
}
