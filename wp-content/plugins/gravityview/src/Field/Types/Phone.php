<?php
/**
 * Phone field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Phone class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * @since 3.0.0
 */
class Phone extends \GravityView_Field {

	var $name = 'phone';

	var $is_searchable = true;

	var $search_operators = ['is', 'isnot', 'contains', 'starts_with', 'ends_with'];

	/** @see \GF_Field_Phone */
	var $_gf_field_class_name = 'GF_Field_Phone';

	var $group = 'advanced';

	var $icon = 'dashicons-smartphone';

	public function __construct() {
		$this->label = esc_html__( 'Phone', 'gk-gravityview' );
		parent::__construct();
	}

	/**
	 * Add option to link phone number.
	 *
	 * @since 1.17
	 *
	 * @param array  $field_options
	 * @param string $template_id
	 * @param string $field_id
	 * @param string $context
	 * @param string $input_type
	 * @param int    $form_id
	 *
	 * @return array
	 */
	public function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id ) {

		if ( 'edit' === $context ) {
			return $field_options;
		}

		$field_options['link_phone'] = [
			'type'     => 'checkbox',
			'label'    => __( 'Make Phone Number Clickable', 'gk-gravityview' ),
			'desc'     => __( 'Allow dialing a number by clicking it?', 'gk-gravityview' ),
			'value'    => true,
			'group'    => 'display',
			'priority' => 100,
		];

		return $field_options;
	}
}
