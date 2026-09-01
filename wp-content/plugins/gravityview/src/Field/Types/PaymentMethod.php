<?php
/**
 * Payment Method field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Payment_Method class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * @since 1.16
 */
class PaymentMethod extends \GravityView_Field {

	var $name = 'payment_method';

	var $is_searchable = true;

	var $is_numeric = false;

	var $search_operators = ['is', 'isnot', 'contains'];

	var $group = 'pricing';

	var $_custom_merge_tag = 'payment_method';

	var $icon = 'dashicons-cart';

	/**
	 * GravityView_Field_Date_Created constructor.
	 */
	public function __construct() {
		$this->label       = esc_html__( 'Payment Method', 'gk-gravityview' );
		$this->description = esc_html__( 'The way the entry was paid for (ie "Credit Card", "PayPal", etc.)', 'gk-gravityview' );
		parent::__construct();
	}
}
