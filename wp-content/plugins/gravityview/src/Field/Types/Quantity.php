<?php
/**
 * Quantity field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Quantity class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * @since 2.17
 */
class Quantity extends \GravityView_Field {

	var $name = 'quantity';

	var $is_searchable = true;

	var $is_numeric = false;

	var $search_operators = ['is', 'isnot', 'greater_than', 'less_than'];

	var $group = 'product';

	var $icon = 'dashicons-cart';

	/** @see GF_Field_Quantity */
	var $_gf_field_class_name = 'GF_Field_Quantity';

	public function __construct() {
		$this->label       = esc_html__( 'Quantity', 'gk-gravityview' );
		$this->description = esc_html__( 'The quantity of a specific product field.', 'gk-gravityview' );
		parent::__construct();
	}
}
