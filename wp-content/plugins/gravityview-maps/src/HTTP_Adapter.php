<?php

namespace GravityKit\GravityMaps;

use HttpException;
use GravityKit\GravityMaps\Geocoder\HttpAdapter\HttpAdapterInterface;

/**
 * HTTP adapter for geocoder
 *
 * @since 1.2
 */
class HTTP_Adapter implements HttpAdapterInterface {
	/**
	 * {@inheritDoc}
	 */
	public function getContent( $url ) {
		$request_settings = array(
			'user-agent' => 'GravityView Maps', // OpenStreetMap requests unique User-Agent ID
			'sslverify'  => false,
		);

		/**
		 * @filter `gravityview/maps/request_settings` Modify request settings used to get content
		 * @since  1.2
		 * @see    WP_Http::request()
		 *
		 * @param array  $request_settings Args passed to wp_remote_request()
		 * @param string $url              URL to fetch
		 */
		$request_settings = apply_filters( 'gravityview/maps/request_settings', $request_settings, $url );

		$response = wp_remote_request( $url, $request_settings );

		if ( is_wp_error( $response ) ) {
			throw new HttpException( $response->get_error_message() );
		}

		$status = wp_remote_retrieve_response_code( $response );

		if ( 200 !== $status ) {
			throw new HttpException( sprintf( 'The server returned a %s status.', $status ) );
		}

		$content = wp_remote_retrieve_body( $response );

		$this->_log_http_errors( $content, $url );

		return $content;
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
	 *
	 * @param string $status  The URL of the request
	 *
	 * @param string $content Error content
	 * @param string $content Response from provider
	 */
	private function _log_http_errors( $content, $url = '' ) {
		$json = json_decode( $content );

		// Not all providers return JSON; Open Street Map returns XML
		if ( empty( $json ) ) {
			return;
		}

		// Log any failed requests
		if ( ! empty( $json->error_message ) || ( isset( $json->success ) && ! $json->success ) ) {
			do_action( 'gravityview_log_error', sprintf( '%s: Unsuccessful request to %s', __METHOD__, $url ), $content );
		}
	}
}
