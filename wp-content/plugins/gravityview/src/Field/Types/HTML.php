<?php
/**
 * HTML field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_HTML class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * Add custom options for HTML field
 */
class HTML extends \GravityView_Field {

	var $name = 'html';

	var $is_searchable = false;

	var $is_sortable = false;

	var $_gf_field_class_name = 'GF_Field_HTML';

	var $group = 'standard';

	var $icon = 'dashicons-media-code';

	public function __construct() {
		$this->label = esc_html__( 'HTML', 'gk-gravityview' );
		parent::__construct();
	}

	public function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id ) {

		unset( $field_options['search_filter'], $field_options['show_as_link'] );

		return $field_options;
	}
}
