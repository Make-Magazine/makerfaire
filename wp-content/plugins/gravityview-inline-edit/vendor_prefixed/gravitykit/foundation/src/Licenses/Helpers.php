<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 05-December-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Licenses;

use Exception;
use GravityKit\GravityEdit\Foundation\Logger\Framework as LoggerFramework;
use GravityKit\GravityEdit\Foundation\Helpers\Core as CoreHelpers;

class Helpers {
	/**
	 * Performs remote call to GravityKit's EDD API.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url API URL.
	 * @param array  $args Request arguments.
	 *
	 * @throws Exception
	 *
	 * @return array|null Response body.
	 */
	public static function query_api( $url, array $args = [] ) {
		$request_parameters = [
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $args,
		];

		$http_response = wp_remote_post(
			$url,
			$request_parameters
		);

		if ( CoreHelpers::is_foundation_debug() ) {
			LoggerFramework::get_instance()->debug(
                'GK API Request',
                [
					'url'                => $url,
					'request_body'       => $args,
					'request_parameters' => $request_parameters,
				]
            );
		}

		if ( is_wp_error( $http_response ) ) {
			if ( CoreHelpers::is_foundation_debug() ) {
				LoggerFramework::get_instance()->debug(
                    'GK API Request Error',
                    [
						'url'        => $url,
						'error'      => $http_response->get_error_message(),
						'error_code' => $http_response->get_error_code(),
						'error_data' => $http_response->get_error_data(),
					]
                );
			}

			throw new Exception( $http_response->get_error_message() );
		}

		$body         = wp_remote_retrieve_body( $http_response );
		$http_status  = wp_remote_retrieve_response_code( $http_response );
		$http_headers = wp_remote_retrieve_headers( $http_response );
		$response     = json_decode( $body, true );

		if ( CoreHelpers::is_foundation_debug() ) {
			LoggerFramework::get_instance()->debug(
                'GK API Response',
                [
					'url'              => $url,
					'http_status'      => $http_status,
					'response_headers' => is_object( $http_headers ) ? $http_headers->getAll() : $http_headers,
					'response_body'    => $response,
					'raw_body_length'  => strlen( $body ),
				]
            );
		}

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			throw new Exception( esc_html__( 'Unable to process remote request. Invalid response body.', 'gk-gravityedit' ) );
		}

		return $response;
	}
}
