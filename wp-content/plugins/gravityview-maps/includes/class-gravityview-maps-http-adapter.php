<?php
/**
 * GravityView Maps Extension - Geocoding
 *
 * Using the Geocoder php lib
 *
 * @package   GravityView_Maps
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @since 1.2
 */

use Geocoder\Exception\ExtensionNotLoadedException;
use Geocoder\Exception\HttpException;

/**
 * Use WordPress HTTP API
 * @author Zack Katz <admin@gravityview.co>
 */
class GravityView_Maps_HTTP_Adapter implements Geocoder\HttpAdapter\HttpAdapterInterface {

	/**
	 * {@inheritDoc}
	 */
	public function getContent( $url ) {

		$request_settings = array(
			'user-agent' => 'GravityView Maps', // OpenStreetMap requests unique User-Agent ID
			'sslverify' => false,
		);

		/**
		 * @filter `gravityview/maps/request_settings` Modify request settings used to get content
		 * @since 1.2
		 * @see WP_Http::request()
		 * @param array $request_settings Args passed to wp_remote_request()
		 * @param string $url URL to fetch
		 */
		$request_settings = apply_filters( 'gravityview/maps/request_settings', $request_settings, $url );

		$response = wp_remote_request( $url, $request_settings );

		if( is_wp_error( $response ) ) {
			throw new HttpException( $response->get_error_message() );
		}

		$status = wp_remote_retrieve_response_code( $response );

		if ( $status !== 200 ) {
			throw new HttpException( sprintf( 'The server return a %s status.', $status ) );
		}

		$content = wp_remote_retrieve_body( $response );

		$this->_log_http_errors( $content );

		return empty( $content ) ? NULL : $content;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return 'gravityview';
	}

	/**
	 * The Geocoding library doesn't trigger exceptions if any providers work. We want to log some errors.
	 *
	 * @since 1.2
	 * @param string $content Response from provider
	 */
	private function _log_http_errors( $content ) {

		$json = json_decode( $content );

		// Check Google errors
		if ( ! empty( $json->error_message ) ) {
			do_action( 'gravityview_log_error', __METHOD__ . ': ' . sprintf( 'The server returned an error: %s', $json->error_message ), $json );
		}
	}
}