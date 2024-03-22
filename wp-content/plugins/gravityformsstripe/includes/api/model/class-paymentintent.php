<?php

namespace Gravity_Forms_Stripe\API\Model;

require_once( 'class-base.php' );
require_once( 'class-paymentmethod.php' );
require_once( 'class-invoice.php' );

/**
 * Object representing a Payment Intent.
 *
 * @since 5.5.0
 */
class PaymentIntent extends Base {

	const OBJECT_NAME = 'payment_intent';

	/**
	 * Returns the API endpoint for this object.
	 *
	 * @since 5.5.0
	 *
	 * @return string Returns the api endpoint for this object.
	 */
	public function api_endpoint() {
		return 'payment_intents';
	}

	/**
	 * Gets the supported parameters for the confirm endpoint.
	 *
	 * @since 5.5.0
	 *
	 * @return array Return an array of supported parameters for the confirm endpoint.
	 */
	public function get_confirm_parameters() {

		$supported_params = array(
			'payment_method',
			'receipt_email',
			'setup_future_usage',
			'shipping',
			'error_on_requires_action',
			'mandate_data',
			'payment_method_data',
			'return_url',
			'use_stripe_sdk',
		);

		return $this->serialize_parameters( $supported_params );
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
			'amount',
			'currency',
			'customer',
			'description',
			'metadata',
			'payment_method',
			'receipt_email',
			'setup_future_usage',
			'shipping',
			'statement_descriptor',
			'statement_descriptor_suffix',
			'payment_method_data',
			'payment_method_options',
			'payment_method_types',
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
			'amount_to_capture',
			'metadata',
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
			'payment_method' => '\Gravity_Forms_Stripe\API\Model\PaymentMethod',
			'invoice'        => '\Gravity_Forms_Stripe\API\Model\Invoice',
		);
	}
}
