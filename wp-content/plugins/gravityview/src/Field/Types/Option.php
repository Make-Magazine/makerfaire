<?php
/**
 * Option field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Option class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * @since 2.17
 */
class Option extends \GravityView_Field {

	var $name = 'option';

	var $is_searchable = true;

	var $is_numeric = false;

	var $search_operators = ['is', 'isnot', 'contains', 'in', 'not_in'];

	var $group = 'product';

	var $icon = 'dashicons-cart';

	/** @see GF_Field_Option */
	var $_gf_field_class_name = 'GF_Field_Option';

	public function __construct() {
		$this->label       = esc_html__( 'Option', 'gk-gravityview' );
		$this->description = esc_attr__( 'Options for a specific product field.', 'gk-gravityview' );
		parent::__construct();
	}
}
