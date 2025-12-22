<?php
namespace ElementorPro\License\Data\Endpoints;

use ElementorPro\Core\Data\Endpoints\Base;
use ElementorPro\Core\Data\Interfaces\Endpoint;
use ElementorPro\License\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Get_License_Status extends Base implements Endpoint {

	public function get_name(): string {
		return 'get-license-status';
	}

	public function get_route(): string {
		return 'get-license-status';
	}

	protected function register() {
		register_rest_route(
			$this->controller->get_namespace(),
			'/' . $this->controller->get_name() . '/' . $this->get_route(),
			[
				[
					'methods' => 'GET',
					'callback' => [ $this, 'get_status' ],
					'permission_callback' => [ $this, 'permission_callback' ],
				],
			]
		);
	}

	public function get_status( $request ) {
		return new \WP_REST_Response(
			[
				'isExpired' => API::is_license_expired(),
			],
			200
		);
	}

	public function permission_callback( $request ) {
		return is_user_logged_in();
	}
}
