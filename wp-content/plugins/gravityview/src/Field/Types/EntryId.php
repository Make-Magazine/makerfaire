<?php
/**
 * Entry ID field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_ID class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * @since 3.0.0
 */
class EntryId extends \GravityView_Field {

	var $name = 'id';

	var $is_searchable = true;

	var $search_operators = ['is', 'isnot', 'greater_than', 'less_than', 'in', 'not_in'];

	var $group = 'meta';

	var $icon = 'dashicons-code-standards';

	var $is_numeric = true;

	public function __construct() {
		$this->label       = esc_html__( 'Entry ID', 'gk-gravityview' );
		$this->description = __( 'The unique ID of the entry.', 'gk-gravityview' );
		parent::__construct();
	}

	public function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id ) {

		if ( 'edit' === $context ) {
			return $field_options;
		}

		if ( 'single' === $context ) {
			unset( $field_options['new_window'] );
		}

		return $field_options;
	}
}
