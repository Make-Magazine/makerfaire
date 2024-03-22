<?php

namespace Gravity_Forms_Stripe\API\Model;

require_once( 'class-base.php' );
require_once( 'class-customer.php' );
require_once( 'class-invoice.php' );
require_once( 'class-plan.php' );

/**
 * Object representing a Subscription.
 *
 * @since 5.5.0
 */
class Subscription extends Base {

	/**
	 * Returns the API endpoint for this object.
	 *
	 * @since 5.5.0
	 *
	 * @return string Returns the api endpoint for this object.
	 */
	public function api_endpoint() {
		return 'subscriptions';
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
			'cancel_at_period_end',
			'default_payment_method',
			'description',
			'items',
			'metadata',
			'payment_behavior',
			'proration_behavior',
			'add_invoice_items',
			'application_fee_percent',
			'automatic_tax',
			'billing_cycle_anchor',
			'billing_thresholds',
			'cancel_at',
			'cancellation_details',
			'collection_method',
			'coupon',
			'days_until_due',
			'default_source',
			'default_tax_rates',
			'on_behalf_of',
			'pause_collection',
			'payment_settings',
			'pending_invoice_item_interval',
			'promotion_code',
			'proration_date',
			'transfer_data',
			'trial_end',
			'trial_from_plan',
			'trial_settings',
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
			'customer'       => '\Gravity_Forms_Stripe\API\Model\Customer',
			'latest_invoice' => '\Gravity_Forms_Stripe\API\Model\Invoice',
			'plan'           => '\Gravity_Forms_Stripe\API\Model\Plan',
		);
	}
}
