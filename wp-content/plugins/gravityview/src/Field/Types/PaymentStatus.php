<?php
/**
 * Payment Status field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Payment_Status class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * @since 1.16
 */
class PaymentStatus extends \GravityView_Field {

	var $name = 'payment_status';

	var $is_searchable = true;

	var $search_operators = ['is', 'in', 'not in', 'isnot'];

	var $group = 'pricing';

	var $_custom_merge_tag = 'payment_status';

	var $icon = 'dashicons-cart';

	/**
	 * GravityView_Field_Payment_Status constructor.
	 */
	public function __construct() {
		$this->label       = esc_html__( 'Payment Status', 'gk-gravityview' );
		$this->description = esc_html__( 'The current payment status of the entry (ie "Processing", "Failed", "Cancelled", "Approved").', 'gk-gravityview' );
		parent::__construct();
	}
}
