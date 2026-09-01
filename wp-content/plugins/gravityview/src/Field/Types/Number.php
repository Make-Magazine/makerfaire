<?php
/**
 * Number field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Number class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * Add custom options for number fields.
 *
 * @since 1.13
 * @since 3.0.0 Migrated to PSR-4.
 */
class Number extends \GravityView_Field {

	var $name = 'number';

	var $is_searchable = true;

	var $search_operators = ['is', 'isnot', 'greater_than', 'less_than'];

	/** @see \GF_Field_Number */
	var $_gf_field_class_name = 'GF_Field_Number';

	var $group = 'standard';

	var $icon = 'dashicons-editor-ol';

	public function __construct() {
		$this->label = esc_html__( 'Number', 'gk-gravityview' );
		parent::__construct();
	}

	public function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id ) {

		$field_options['number_format'] = [
			'type'  => 'checkbox',
			'label' => __( 'Format number?', 'gk-gravityview' ),
			'desc'  => __( 'Display numbers with thousands separators.', 'gk-gravityview' ),
			'value' => false,
			'group' => 'field',
		];

		$field_options['decimals'] = [
			'type'       => 'number',
			'label'      => __( 'Decimals', 'gk-gravityview' ),
			'desc'       => __( 'Precision of the number of decimal places. Leave blank to use existing precision.', 'gk-gravityview' ),
			'value'      => '',
			'merge_tags' => false,
			'group'      => 'field',
		];

		return $field_options;
	}
}
