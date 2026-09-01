<?php
/**
 * Credit Card field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_CreditCard class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * @since 3.0.0
 */
class CreditCard extends \GravityView_Field {

	var $name = 'creditcard';

	var $is_searchable = false;

	var $_gf_field_class_name = 'GF_Field_CreditCard';

	var $group = 'payment';

	var $icon = 'dashicons-cart';

	public function __construct() {
		$this->label = esc_html__( 'Credit Card', 'gk-gravityview' );
		parent::__construct();
	}
}
