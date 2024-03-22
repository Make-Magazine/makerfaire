<?php

namespace Gravity_Forms_Stripe\API\Model;

require_once( 'class-base.php' );

/**
 * Object representing a Product.
 *
 * @since 5.5.0
 */
class Product extends Base {

	/**
	 * Returns the API endpoint for this object.
	 *
	 * @since 5.5.0
	 *
	 * @return string Returns the api endpoint for this object.
	 */
	public function api_endpoint() {
		return 'products';
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
			'active',
			'default_price',
			'description',
			'metadata',
			'name',
			'features',
			'images',
			'package_dimensions',
			'shippable',
			'statement_descriptor',
			'tax_code',
			'unit_label',
			'url',
		);

		return $this->serialize_parameters( $supported_params );
	}
}
