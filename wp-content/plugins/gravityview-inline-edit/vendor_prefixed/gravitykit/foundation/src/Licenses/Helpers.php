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
		// This is an EXTERNAL call to gravitykit.com — strict cert verification in production
		// protects against MitM on customer networks. Loopback probes intentionally use a
		// different knob (`https_local_ssl_verify`); do NOT "unify" these without understanding
		// the distinction. See HealthCheck::probe_loopback() and Core::is_site_accessible().
		// Derive the timeout from PHP's max_execution_time instead of hardcoding it, so a slow or
		// unreachable Store cannot stack multiple sequential requests (products + licenses) into a
		// fatal timeout on admin page loads. Budget each request to ~1/3 of the limit, leaving room
		// for a second sequential fetch plus the rest of the page; clamp to a sane 5–15s range. When
		// there is no limit (CLI / max_execution_time = 0), use the generous upper bound.
		$max_execution_time = (int) ini_get( 'max_execution_time' );
		$default_timeout    = $max_execution_time > 0 ? (int) max( 5, min( 15, floor( $max_execution_time / 3 ) ) ) : 15;

		/**
		 * Filters the timeout (in seconds) for Store API metadata requests (product and license data).
		 *
		 * Defaults to roughly a third of PHP's max_execution_time (clamped to 5–15s), or 15s when there
		 * is no limit, so a slow or unreachable Store cannot exhaust the request budget. Raise it on slow
		 * connections. Package downloads use their own (longer) timeout, not this value.
		 *
		 * @since 1.22.0
		 *
		 * @param int    $timeout Request timeout in seconds.
		 * @param string $url     The Store API URL being requested.
		 */
		$timeout = (int) apply_filters( 'gk/foundation/licenses/api-request-timeout', $default_timeout, $url );

		$request_parameters = [
			'timeout'   => $timeout,
			'sslverify' => CoreHelpers::is_production_environment(),
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

		$body         = (string) wp_remote_retrieve_body( $http_response );
		$http_status  = (int) wp_remote_retrieve_response_code( $http_response );
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
			// The EDD API returns non-2xx status codes (e.g., 404 for invalid keys) with a valid JSON body. Distinguish genuine EDD responses from WAF/CDN blocks by checking for the `license` field.
			if ( is_array( $response ) && isset( $response['license'] ) && ! empty( $response['message'] ) ) {
				throw new Exception( esc_html( $response['message'] ) );
			}

			throw new Exception( self::get_http_error_message( (int) $http_status, $http_headers, $body ) );
		}

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			throw new Exception( esc_html__( 'Unable to process remote request. Invalid response body.', 'gk-foundation' ) );
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
				esc_html__( 'Security software (%s) is blocking the connection to the GravityKit license server. Please contact GravityKit support for assistance.', 'gk-foundation' ),
				$blocked_by
			);
		}

		switch ( $http_status ) {
			case 403:
				if ( false !== stripos( $body, 'cloudflare' ) ) {
					return esc_html__( 'The request to the GravityKit license server was blocked by Cloudflare. Please try again later or contact GravityKit support if the issue persists.', 'gk-foundation' );
				}

				return esc_html__( 'The request to the GravityKit license server was blocked (HTTP 403). This is typically caused by a firewall or security plugin. Please contact GravityKit support for assistance.', 'gk-foundation' );

			case 500:
			case 502:
			case 503:
				return esc_html__( 'The GravityKit license server is temporarily unavailable. Please try again later.', 'gk-foundation' );

			default:
				return sprintf(
					/* translators: %d is the HTTP status code. */
					esc_html__( 'The GravityKit license server returned an unexpected response (HTTP %d). Please try again later or contact support if the issue persists.', 'gk-foundation' ),
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
