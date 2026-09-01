<?php
/**
 * Transaction ID field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Transaction_ID class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * @since 1.16
 */
class TransactionId extends \GravityView_Field {

	var $name = 'transaction_id';

	var $is_searchable = true;

	var $is_numeric = true;

	var $search_operators = ['is', 'isnot', 'starts_with', 'ends_with'];

	var $group = 'pricing';

	var $_custom_merge_tag = 'transaction_id';

	var $icon = 'dashicons-cart';

	/**
	 * GravityView_Field_Payment_Amount constructor.
	 */
	public function __construct() {
		$this->label = esc_html__( 'Transaction ID', 'gk-gravityview' );
		parent::__construct();
	}
}
