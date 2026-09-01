<?php

class GravityPerks_REST_Spellbook_Controller extends WP_REST_Controller {
	protected $namespace = 'gwiz/v1';
	protected $rest_base = 'spellbook';

	public function register_routes() {
		register_rest_route($this->namespace, '/' . $this->rest_base . '/register', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'register_email' ),
			'permission_callback' => array( $this, 'check_permission' ),
			'args'                => array(
				'email' => array(
					'required' => true,
					'type'     => 'string',
					'format'   => 'email',
				),
				'name'  => array(
					'required' => true,
					'type'     => 'string',
				),
			),
		));

		register_rest_route($this->namespace, '/' . $this->rest_base . '/registration-status', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_registration_status' ),
			'permission_callback' => array( $this, 'check_permission' ),
		));
	}

	public function check_permission( $request ) {
		return current_user_can( 'manage_options' );
	}

	public function register_email( $request ) {
		$email = $request['email'];
		$name  = $request['name'];

		// Forward registration to in.gravitywiz.com
		$response = wp_remote_post('https://in.gravitywiz.com/v1/spellbook/register', array(
			'body'    => wp_json_encode(array(
				'email' => $email,
				'name'  => $name,
			)),
			'headers' => array( 'Content-Type' => 'application/json' ),
			'timeout' => 15,
		));

		if ( is_wp_error( $response ) ) {
			return new WP_Error(
				'registration_failed',
				$response->get_error_message(),
				array( 'status' => 500 )
			);
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $body['success'] ) {
			// Store email for both UI state and potential GWAPI usage
			update_option( 'gwp_spellbook_email', $email );
		}

		return rest_ensure_response( $body );
	}

	public function get_registration_status( $request ) {
		$email = get_option( 'gwp_spellbook_email' );

		return rest_ensure_response(array(
			'is_registered' => ! empty( $email ),
			'email'         => $email,
		));
	}
}
