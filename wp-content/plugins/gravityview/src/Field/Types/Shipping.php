<?php
/**
 * Shipping field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Shipping class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * @since 2.17
 */
class Shipping extends \GravityView_Field {

	var $name = 'shipping';

	var $is_searchable = true;

	var $is_numeric = false;

	var $search_operators = ['is', 'isnot', 'greater_than', 'less_than'];

	var $group = 'product';

	var $icon = 'dashicons-cart';

	/** @see GF_Field_Shipping */
	var $_gf_field_class_name = 'GF_Field_Shipping';

	public function __construct() {
		$this->label       = esc_html__( 'Shipping', 'gk-gravityview' );
		$this->description = esc_html__( 'The shipping fee for the payment.', 'gk-gravityview' );
		parent::__construct();
	}
}
