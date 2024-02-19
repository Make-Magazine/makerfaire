<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

Class MapifyPro_Installer_WC_AM_Client {

	/**
	 * MapifyPro API url
	 */
	public $api_url;
	
	/**
	 * MapifyPro API instance for this installation
	 */
	public $instance;

	/**
	 * MapifyPro API key
	 */
	public $api_key;

	/**
	 * MapifyPro product ID
	 */
	public $product_id;

	/**
	 * Constructor
	 * 
	 * @param string $api_key MapifyPro API key.
	 * @param integer $product_id MapifyPro product ID.
	 * @param string|null $instance API instance.
	 */
	public function __construct( $api_key, $product_id, $instance = null ) {
		$this->api_url    = 'https://mapifypro.com/';
		$this->instance   = $instance ? $instance : $this->get_api_instance( $product_id );
		$this->api_key    = $api_key;
		$this->product_id = $product_id;
	}

	/**
	 * Get or generate API instance.
	 * 
	 * @param integer $product_id MapifyPro product ID.
	 */
	private function get_api_instance( $product_id ) {
		$instance_name  = sprintf( 'wc_am_client_%s_instance', $product_id );
		$instance_value = get_option( $instance_name );

		if ( ! $instance_value ) {
			$instance_value = wp_generate_password( 12, false );
			update_option( $instance_name, $instance_value );
		}

		return $instance_value;
	}

	/**
	 * Get status of the API key subscription.
	 */
	public function get_status() {
		$response = $this->request( 'status' );

		if ( ! isset( $response['success'] ) || ! $response['success'] ) {
			return false;
		} else {
			return $response;
		} 
	}

	/**
	 * Activate API key subscription.
	 */
	public function activate_api_key() {
		return $this->request( 'activate' );
	}

	/**
	 * Get plugin file url from the server.
	 */
	public function get_plugin_url() {
		$response =  $this->request( 'update' );

		if ( ! isset( $response['success'] ) || ! $response['success'] ) {
			return false;
		} elseif ( '' == $response['data']['package']['package'] ) {
			return false;
		} else {
			return $response['data']['package']['package'];
		}
	}

	/**
	 * API request function.
	 * 
	 * @param string $action Type of the request.
	 */
	private function request( $action ) {
		$args = array(
			'wc_am_action' => $action,
			'product_id'   => $this->product_id,
			'api_key'      => $this->api_key,
			'instance'     => $this->instance,
			'plugin_name'  => 'mapifypro/mapify_pro.php',
			'slug'         => 'mapifypro',
			'version'      => '4.0.0',
		);

		$target_url = esc_url_raw( add_query_arg( 'wc-api', 'wc-am-api', $this->api_url ) . '&' . http_build_query( $args ) );
		$request    = wp_safe_remote_post( $target_url, array( 'timeout' => 15 ) );

		// request failed
		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			$this->update_log( __( "Request timeout, probably there's problem with MapifyPro server. Please try again later.", 'mpfy' ) );
			return false;
		}

		$response = wp_remote_retrieve_body( $request );
		$response = json_decode( $response, true );

		// add `instance` variable to the response
		$response = array_merge( $response, array( 'instance' => $this->instance ) );

		// error loging if any
		$this->update_log( $response );

		return $response;
	}

	/**
	 * Logging.
	 */
	private function update_log( $response ) {
		if ( is_array( $response ) && isset( $response['error'] ) ) {
			$message = $response['error'];	
		} else {
			$message = $response;
		}

		update_option( 'mapifypro_installer_log', $message );
	}

}