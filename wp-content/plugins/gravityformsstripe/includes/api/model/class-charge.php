<?php
namespace Gravity_Forms_Stripe\API\Model;

require_once( 'class-base.php' );
require_once( 'class-paymentintent.php' );
require_once( 'class-invoice.php' );
require_once( 'class-customer.php' );
require_once( 'class-customerbalancetransaction.php' );

/**
 * Object representing a Charge.
 *
 * @since 5.5.0
 */
class Charge extends Base {

	/**
	 * Returns the API endpoint for this object.
	 *
	 * @since 5.5.0
	 *
	 * @return string Returns the api endpoint for this object.
	 */
	public function api_endpoint() {
		return 'charges';
	}

	/**
	 * Gets the supported parameters for the update endpoint.
	 *
	 * @since 5.5.0
	 *
	 * @return array Return an array of supported parameters for the update endpoint.
	 */
	public function get_update_parameters() {

		$supported_params = array(
			'customer',
			'description',
			'metadata',
			'receipt_email',
			'shipping',
			'fraud_details',
		);

		return $this->serialize_parameters( $supported_params );
	}

	/**
	 * Gets the supported parameters for the capture endpoint.
	 *
	 * @since 5.5.0
	 *
	 * @return array Return an array of supported parameters for the capture endpoint.
	 */
	public function get_capture_parameters() {

		$supported_params = array(
			'amount',
			'receipt_email',
			'statement_descriptor',
			'statement_descriptor_suffix',
		);

		return $this->serialize_parameters( $supported_params );
	}

	/**
	 * Gets the nested object that should be expanded when this object is created.
	 *
	 * @since 5.5.0
	 *
	 * @return array Returns an array of nested objects that should be expanded when this object is created.
	 */
	public function get_nested_objects() {

		return array(
			'payment_intent'      => '\Gravity_Forms_Stripe\API\Model\PaymentIntent',
			'invoice'             => '\Gravity_Forms_Stripe\API\Model\Invoice',
			'customer'            => '\Gravity_Forms_Stripe\API\Model\Customer',
			'balance_transaction' => '\Gravity_Forms_Stripe\API\Model\CustomerBalanceTransaction',
		);
	}
}
