<?php

namespace Gravity_Forms_Stripe\API\Model;

require_once( 'class-base.php' );

/**
 * Represents a Stripe Customer.
 *
 * @since 5.5.0
 */
class Customer extends Base {

	/**
	 * Gets the supported parameters for the update endpoint.
	 *
	 * @since 5.5.0
	 *
	 * @return array Return an array of supported parameters for the update endpoint.
	 */
	public function get_update_parameters() {

		$supported_params = array(
			'address',
			'description',
			'email',
			'metadata',
			'name',
			'phone',
			'shipping',
			'balance',
			'coupon',
			'default_source',
			'invoice_prefix',
			'invoice_settings',
			'next_invoice_sequence',
			'preferred_locales',
			'promotion_code',
			'source',
			'tax',
			'tax_exempt',
		);

		return $this->serialize_parameters( $supported_params );
	}

	/**
	 * Returns the API endpoint for this object.
	 *
	 * @since 5.5.0
	 *
	 * @return string Returns the api endpoint for this object.
	 */
	public function api_endpoint() {
		return 'customers';
	}


	/**
	 * Creates a new invoice item for this customer.
	 *
	 * @since 5.5.0
	 *
	 * @param $data array The data to create the invoice item with.
	 *
	 * @return Model\InvoiceItem Returns the invoice item object, or a WP_Error if there was an error.
	 */
	public function addInvoiceItem( $data ) {

		$data['customer'] = $this->id;
		return $this->api->add_invoice_item( $data );
	}
}
