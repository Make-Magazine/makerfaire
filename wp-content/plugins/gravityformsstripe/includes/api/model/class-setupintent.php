<?php

namespace Gravity_Forms_Stripe\API\Model;

require_once( 'class-base.php' );

/**
 * Object representing a Setup Intent.
 *
 * @since 5.5.0
 */
class SetupIntent extends Base {

	/**
	 * Returns the API endpoint for this object.
	 *
	 * @since 5.5.0
	 *
	 * @return string Returns the api endpoint for this object.
	 */
	public function api_endpoint() {
		return 'setup_intents';
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
			'customer',
			'description',
			'metadata',
			'payment_method',
			'attach_to_self',
			'flow_directions',
			'payment_method_configuration',
			'payment_method_data',
			'payment_method_options',
			'payment_method_types',
		);

		return $this->serialize_parameters( $supported_params );
	}
}
