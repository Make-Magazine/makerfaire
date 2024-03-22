<?php

namespace Gravity_Forms_Stripe\API\Model;

require_once( 'class-base.php' );
require_once( 'class-paymentintent.php' );

/**
 * Object representing an Invoice.
 *
 * @since 5.5.0
 */
class Invoice extends Base {

	/**
	 * Returns the API endpoint for this object.
	 *
	 * @since 5.5.0
	 *
	 * @return string Returns the api endpoint for this object.
	 */
	public function api_endpoint() {
		return 'invoices';
	}

	/**
	 * Gets the supported update endpoint parameters.
	 *
	 * @since 5.5.0
	 *
	 * @return array Return an array of supported parameters for the update endpoint.
	 */
	public function get_update_parameters() {

		$supported_params = array(
			'auto_advance',
			'collection_method',
			'description',
			'metadata',
			'account_tax_ids',
			'automatic_tax',
			'custom_fields',
			'days_until_due',
			'default_payment_method',
			'default_source',
			'default_tax_rates',
			'discounts',
			'due_date',
			'effective_at',
			'footer',
			'payment_settings',
			'rendering',
			'shipping_cost',
			'shipping_details',
			'statement_descriptor',
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
			'payment_intent' => '\Gravity_Forms_Stripe\API\Model\PaymentIntent',
		);
	}
}
