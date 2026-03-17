<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
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

		if ( $http_status < 200 || $http_status >= 300 ) {
			throw new Exception( self::get_http_error_message( (int) $http_status, $http_headers, $body ) );
		}

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			throw new Exception( esc_html__( 'Unable to process remote request. Invalid response body.', 'gk-gravityedit' ) );
		}

		return $response;
	}

	/**
	 * Returns an actionable error message for non-2xx HTTP responses from the GravityKit API.
	 *
	 * Checks for known server-side security tool signatures, then falls back to
	 * status-code-based messages.
	 *
	 * @since 1.10.0
	 *
	 * @param int                                                     $http_status HTTP status code.
	 * @param \WpOrg\Requests\Utility\CaseInsensitiveDictionary|array $headers     Response headers.
	 * @param string                                                  $body        Response body.
	 *
	 * @return string Error message.
	 */
	private static function get_http_error_message( int $http_status, $headers, string $body ): string {
		// Check for known security tools that block requests.
		$blocked_by = self::detect_security_block( $http_status, $headers, $body );

		if ( false !== $blocked_by ) {
			return sprintf(
				/* translators: %s is the name of the security software (e.g., "Monarx", "Wordfence"). */
				esc_html__( 'Security software (%s) is blocking the connection to the GravityKit license server. Please contact GravityKit support for assistance.', 'gk-gravityedit' ),
				$blocked_by
			);
		}

		switch ( $http_status ) {
			case 403:
				if ( false !== stripos( $body, 'cloudflare' ) ) {
					return esc_html__( 'The request to the GravityKit license server was blocked by Cloudflare. Please try again later or contact GravityKit support if the issue persists.', 'gk-gravityedit' );
				}

				return esc_html__( 'The request to the GravityKit license server was blocked (HTTP 403). This is typically caused by a firewall or security plugin. Please contact GravityKit support for assistance.', 'gk-gravityedit' );

			case 500:
			case 502:
			case 503:
				return esc_html__( 'The GravityKit license server is temporarily unavailable. Please try again later.', 'gk-gravityedit' );

			default:
				return sprintf(
					/* translators: %d is the HTTP status code. */
					esc_html__( 'The GravityKit license server returned an unexpected response (HTTP %d). Please try again later or contact support if the issue persists.', 'gk-gravityedit' ),
					$http_status
				);
		}
	}

	/**
	 * Detects known server-side security tools from HTTP response signatures.
	 *
	 * @since 1.10.0
	 *
	 * @param int                                                     $http_status HTTP status code.
	 * @param \WpOrg\Requests\Utility\CaseInsensitiveDictionary|array $headers     Response headers.
	 * @param string                                                  $body        Response body.
	 *
	 * @return string|false Security tool name if detected, false otherwise.
	 */
	private static function detect_security_block( int $http_status, $headers, string $body ) {
		if ( 403 !== $http_status ) {
			return false;
		}

		// Header-based detection.
		$header_signatures = [
			'x-rasp-block' => 'RASP',
		];

		$normalized_headers = is_object( $headers ) ? $headers : array_change_key_case( (array) $headers, CASE_LOWER );

		foreach ( $header_signatures as $header_name => $name ) {
			$value = is_object( $normalized_headers ) ? $normalized_headers[ $header_name ] : ( $normalized_headers[ $header_name ] ?? '' );

			if ( ! empty( $value ) ) {
				return $name;
			}
		}

		// Body-based detection.
		$signatures = [
			'monarx'      => 'Monarx',
			'wordfence'   => 'Wordfence',
			'sucuri'      => 'Sucuri',
			'imunify'     => 'Imunify360',
			'modsecurity' => 'ModSecurity',
			'malcare'     => 'MalCare',
			'bitninja'    => 'BitNinja',
			'sitelock'    => 'SiteLock',
			'cpguard'     => 'cPGuard',
			'getastra'    => 'Astra',
		];

		$body_lower = strtolower( $body );

		foreach ( $signatures as $needle => $name ) {
			if ( false !== strpos( $body_lower, $needle ) ) {
				return $name;
			}
		}

		return false;
	}
}
