<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Components;

use Exception;
use GravityKit\GravityView\Foundation\Helpers\WP;
use GravityKit\GravityView\Foundation\Encryption\Encryption;

/**
 * Secure file download component that allows the creation of encrypted download links.
 *
 * @since 1.3.0
 */
class SecureDownload {
	/**
	 * Component ID.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	const ID = 'secure_download';

	/**
	 * Rewrite endpoint.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	const REWRITE_ENDPOINT = 'gk-download';

	/**
	 * Class instance.
	 *
	 * @since 1.3.0
	 *
	 * @var SecureDownload|null
	 */
	private static $_instance = null;

	/**
	 * Encryption instance.
	 *
	 * @since 1.3.0
	 *
	 * @var Encryption
	 */
	private $encryption;

	/**
	 * Last token validation failure code.
	 *
	 * @since 1.21.0
	 *
	 * @var string|null
	 */
	private $last_failure_code = null;

	/**
	 * Buffer size for file streaming (1MB).
	 *
	 * @since 1.3.0
	 *
	 * @var int
	 */
	const BUFFER_SIZE = 1048576;

	/**
	 * Maximum buffer size for large files (16MB).
	 *
	 * @since 1.3.0
	 *
	 * @var int
	 */
	const MAX_BUFFER_SIZE = 16777216;

	/**
	 * Minimum buffer size for small files (64KB).
	 *
	 * @since 1.3.0
	 *
	 * @var int
	 */
	const MIN_BUFFER_SIZE = 65536;

	/**
	 * Flush frequency (every N chunks).
	 *
	 * @since 1.3.0
	 *
	 * @var int
	 */
	const FLUSH_FREQUENCY = 4;

	/**
	 * Gets the optimal flush frequency based on buffer size.
	 *
	 * @since 1.3.0
	 *
	 * @param int $buffer_size The buffer size in bytes.
	 *
	 * @return int The optimal flush frequency.
	 */
	private function get_optimal_flush_frequency( $buffer_size ) {
		if ( $buffer_size >= 16777216 ) {      // 16MB+ chunks: flush every 8 chunks (128MB).
			return 8;
		} elseif ( $buffer_size >= 4194304 ) { // 4MB+ chunks: flush every 6 chunks (24MB).
			return 6;
		} else {                               // Smaller chunks: flush every 4 chunks.
			return self::FLUSH_FREQUENCY;
		}
	}

	/**
	 * Default token expiration time (1 hour).
	 *
	 * @since 1.3.0
	 *
	 * @var int
	 */
	const DEFAULT_EXPIRATION = HOUR_IN_SECONDS;

	/**
	 * Default cache expiration time for file downloads (1 month).
	 *
	 * @since 1.3.0
	 *
	 * @var int
	 */
	const DEFAULT_CACHE_EXPIRATION = MONTH_IN_SECONDS;

	/**
	 * Maximum download history entries per token.
	 *
	 * @since 1.3.0
	 *
	 * @var int
	 */
	const MAX_HISTORY_PER_TOKEN = 100;

	/**
	 * Length of short token IDs.
	 *
	 * @since 1.3.0
	 *
	 * @var int
	 */
	const SHORT_ID_LENGTH = 12;

	/**
	 * History key prefix for download tracking.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	const HISTORY_KEY_PREFIX = 'gk_download_history_';

	/**
	 * Class constructor.
	 *
	 * @since 1.3.0
	 */
	private function __construct() {
		$this->encryption = Encryption::get_instance(
			'',
			[ 'base64_variant' => SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING ]
		);
	}

	/**
	 * Returns class instance.
	 *
	 * @since 1.3.0
	 *
	 * @return SecureDownload
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Gets the download endpoint.
	 *
	 * @since TBD
	 *
	 * @return string The download endpoint.
	 */
	public function get_endpoint() {
		/**
		 * Filters the secure download endpoint.
		 *
		 * Allows customizing the URL path used for secure downloads.
		 * Default is 'gk-download', resulting in URLs like /gk-download/{token}/.
		 *
		 * Important: changing this requires flushing rewrite rules.
		 *
		 * @since TBD
		 *
		 * @param string $endpoint The download endpoint. Default 'gk-download'.
		 */
		return apply_filters( 'gk/foundation/secure-download/endpoint', self::REWRITE_ENDPOINT );
	}

	/**
	 * Initializes the component.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', array( $this, 'maybe_process_download_early' ), 0 );
		add_action( 'init', array( $this, 'register_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_action( 'template_redirect', array( $this, 'handle_rewrite_request' ) );
	}

	/**
	 * Processes download requests early in the WordPress lifecycle.
	 *
	 * This method hooks at 'init' priority 0 to handle secure downloads before
	 * WordPress fully loads, significantly improving performance for small/medium files.
	 * By exiting early, we skip theme loading, widget initialization, and other
	 * unnecessary overhead for file downloads.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	public function maybe_process_download_early() {
		$token = $this->extract_token_from_request();

		if ( empty( $token ) ) {
			return;
		}

		$this->handle_download_request( $token );
	}

	/**
	 * Extracts the download token from the current request.
	 *
	 * Checks multiple sources in order of priority:
	 * 1. URL path matching the rewrite endpoint pattern
	 * 2. Query parameter 'gk_download_token'
	 *
	 * @since TBD
	 *
	 * @return string|null The token if found, null otherwise.
	 */
	private function extract_token_from_request() {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';

		$endpoint = $this->get_endpoint();

		// Fast bailout - skip regex on requests that can't be downloads.
		if ( strpos( $request_uri, '/' . $endpoint . '/' ) === false && ! isset( $_GET['gk_download_token'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return null;
		}

		// Check for rewrite URL pattern.
		$pattern = '#/' . preg_quote( $endpoint, '#' ) . '/([^/\?]+)/?#';

		if ( preg_match( $pattern, $request_uri, $matches ) ) {
			return sanitize_text_field( rawurldecode( $matches[1] ) );
		}

		// Fallback: check for direct query parameter.
		if ( isset( $_GET['gk_download_token'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return sanitize_text_field( wp_unslash( $_GET['gk_download_token'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		return null;
	}

	/**
	 * Generates a short ID from a token.
	 *
	 * @since 1.3.0
	 *
	 * @param string $token The encrypted token.
	 *
	 * @return string The short token ID.
	 */
	private function get_token_id( $token ) {
		$full_hash = $this->encryption->hash( $token );

		return substr( $full_hash, 0, self::SHORT_ID_LENGTH );
	}

	/**
	 * Generates a secure download URL for a file.
	 *
	 * @since 1.3.0
	 *
	 * @param string $file_path The absolute path to the file, or a remote URL when source_type is 'remote'.
	 * @param array  $args {
	 *     Optional arguments for the download URL.
	 *
	 *     @type string       $source_type         Source type: 'local' (default), or 'remote' for remote URLs. URLs require explicit 'remote' value.
	 *     @type int          $expires_in          Time in seconds until the link expires. Default 3600 (1 hour). Set to 0 for no expiration.
	 *     @type int          $limit               Maximum number of downloads allowed. 0 = unlimited. Default 0.
	 *     @type array        $capabilities        Array of capabilities required to download. Default empty.
	 *     @type string|array $ips                 Single IP or array of IPs allowed to download. Default empty (no restriction).
	 *     @type int|array    $users               Single user ID or array of user IDs allowed to download. Default empty (no restriction).
	 *     @type bool|array   $track               Track downloads. true = track with history, array = track specific data (e.g. ['ip', 'user_agent', 'history']). Default false.
	 *     @type array        $meta                Additional metadata to include in the token. Default empty.
	 *     @type string       $filename            Custom filename to use when downloading. Default is the original filename.
	 *     @type int          $cache_duration      Cache duration in seconds. 0 = no cache (private), > 0 = specific duration. If not set, auto-detects based on file type.
	 *     @type string       $disposition         Content disposition: 'inline' (default) or 'attachment'. Inline displays in browser and preserves filename for "Save As"; attachment forces download.
	 * }
	 *
	 * @return array {
	 *     Download URL and token information.
	 *
	 *     @type string $url The secure download URL.
	 *     @type string $id  Short hash identifier for the token (first 12 chars).
	 * }
	 *
	 * @throws Exception If the file path is invalid, URL validation fails, or encryption fails.
	 */
	public function generate_download_url( $file_path, $args = [] ) {
		$defaults = [
			'source_type'  => 'local',
			'expires_in'   => self::DEFAULT_EXPIRATION,
			'limit'        => 0,
			'capabilities' => [],
			'ips'          => [],
			'users'        => [],
			'track'        => false,
			'meta'         => [],
			'filename'     => '',
			'disposition'  => 'inline',
		];

		$args = wp_parse_args( $args, $defaults );

		$source_type = $args['source_type'];

		$is_url = preg_match( '#^https?://#i', $file_path );

		// Fail-fast: URL detected but source_type not explicitly 'remote'.
		if ( $is_url && 'remote' !== $source_type ) {
			throw new Exception(
				esc_html__(
					"URLs require source_type='remote'. Pass ['source_type' => 'remote'] to confirm remote URL handling.",
					'gk-gravityview'
				)
			);
		}

		// Handle remote URLs explicitly (source_type='remote').
		$remote_source = null;

		if ( 'remote' === $source_type ) {
			// Validate URL format.
			if ( ! $is_url ) {
				throw new Exception( esc_html__( 'Invalid URL format for remote source.', 'gk-gravityview' ) );
			}

			// Build remote source config (same structure as filter-based approach).
			$remote_source = [
				'url' => $file_path,
			];

			// Extract filename.
			if ( ! empty( $args['filename'] ) ) {
				$remote_source['filename'] = basename( $args['filename'] );
			} else {
				$url_path                  = wp_parse_url( $file_path, PHP_URL_PATH );
				$remote_source['filename'] = $url_path ? basename( $url_path ) : 'download';
			}

			// Skip local file checks - proceed directly to token generation.
		} else {
			// Local file handling (unchanged default behavior).

			// Basic security check for directory traversal attempts.
			if ( strpos( $file_path, '..' ) !== false ) {
				throw new Exception( esc_html__( 'Invalid file path provided.', 'gk-gravityview' ) );
			}

			// Convert relative path to absolute.
			if ( substr( $file_path, 0, 1 ) !== '/' ) {
				$file_path = ABSPATH . ltrim( $file_path, '/' );
			}

			// Verify file exists or allow remote source via filter (virtual path support).
			if ( ! file_exists( $file_path ) ) {
				/**
				 * Allows serving files from remote URLs when local file doesn't exist.
				 *
				 * Return an array with remote source configuration to enable remote downloads.
				 * The array should contain:
				 * - 'url' (required): The remote URL to download from.
				 * - 'filename' (optional): Custom filename to use for download.
				 * - 'size' (optional): File size in bytes for Content-Length header.
				 *
				 * @since TBD
				 *
				 * @param array|false $remote_source Remote source config or false to fail. Default false.
				 * @param string      $file_path     The original file path that wasn't found.
				 * @param array       $args          The arguments passed to generate_download_url.
				 */
				$remote_source = apply_filters( 'gk/foundation/secure-download/remote-source', false, $file_path, $args );

				if ( ! $remote_source || ! is_array( $remote_source ) || empty( $remote_source['url'] ) ) {
					throw new Exception( esc_html__( 'File not found.', 'gk-gravityview' ) );
				}
			}
		}

		// Normalize IP and user restrictions to arrays.
		if ( ! empty( $args['ips'] ) && ! is_array( $args['ips'] ) ) {
			$args['ips'] = [ $args['ips'] ];
		}
		if ( ! empty( $args['users'] ) && ! is_array( $args['users'] ) ) {
			$args['users'] = [ $args['users'] ];
		}

		// Calculate expiration timestamp.
		// Positive: expires in N seconds from now.
		// Negative: already expired N seconds ago (useful for testing).
		// Zero: no expiration.
		if ( $args['expires_in'] > 0 ) {
			$expires = time() + $args['expires_in'];
		} elseif ( $args['expires_in'] < 0 ) {
			$expires = time() + $args['expires_in']; // Results in past timestamp.
		} else {
			$expires = 0; // No expiration.
		}

		// Create token data with only non-default values to minimize token size.
		$token_data = array_filter(
			[
				'file'         => $file_path,
				'expires'      => $expires,
				'limit'        => $args['limit'],
				'capabilities' => $args['capabilities'],
				'ips'          => $args['ips'],
				'users'        => $args['users'],
				'track'        => $args['track'],
				'meta'         => $args['meta'],
				'filename'     => $args['filename'],
			],
			function ( $value, $key ) {
				// 'file' is always required.
				if ( 'file' === $key ) {
					return true;
				}

				// Numeric fields: include only if > 0.
				if ( in_array( $key, [ 'expires', 'limit' ], true ) ) {
					return $value > 0;
				}

				// Everything else: include if not empty.
				return ! empty( $value );
			},
			ARRAY_FILTER_USE_BOTH
		);

		if ( isset( $args['cache_duration'] ) ) {
			$token_data['cache_duration'] = $args['cache_duration'];
		}

		// Store disposition only if 'attachment' (since 'inline' is the default) to minimize token size.
		if ( 'attachment' === $args['disposition'] ) {
			$token_data['disposition'] = 'attachment';
		}

		// Add remote source data if file is served from remote URL.
		if ( $remote_source ) {
			$token_data['remote_url'] = $remote_source['url'];

			// Add source_type metadata for logging/policy (only for direct URL mode).
			if ( 'remote' === $source_type ) {
				$token_data['source_type'] = 'remote';
			}

			// Override filename if provided by remote source.
			if ( ! empty( $remote_source['filename'] ) && empty( $args['filename'] ) ) {
				$token_data['filename'] = $remote_source['filename'];
			}

			// Store remote file size if provided.
			if ( isset( $remote_source['size'] ) ) {
				$token_data['remote_size'] = (int) $remote_source['size'];
			}
		}

		/**
		 * Filters the token data before encryption.
		 *
		 * @filter `gk/foundation/secure-download/token-data`
		 *
		 * @since 1.3.0
		 *
		 * @param array  $token_data The token data.
		 * @param string $file_path  The absolute path to the file.
		 * @param array  $args       The arguments passed to generate_download_url.
		 */
		$token_data = apply_filters( 'gk/foundation/secure-download/token-data', $token_data, $file_path, $args );

		$json_data = wp_json_encode( $token_data );

		if ( false === $json_data ) {
			throw new Exception( esc_html__( 'Failed to encode token data.', 'gk-gravityview' ) );
		}

		$token = $this->encryption->encrypt( $json_data );

		if ( ! $token ) {
			throw new Exception( esc_html__( 'Failed to generate secure token.', 'gk-gravityview' ) );
		}

		$token_id = $this->get_token_id( $token );

		// Mark token for single-use or limited-use tracking with minimal storage.
		if ( $args['limit'] > 0 ) {
			// Store token ID for tracking.
			$token_data['token_id'] = $token_id;
		}

		// Use pretty URL if permalinks are enabled, otherwise fall back to query parameter.
		if ( get_option( 'permalink_structure' ) ) {
			$download_url = home_url( $this->get_endpoint() . '/' . rawurlencode( $token ) . '/' );
		} else {
			$download_url = add_query_arg( 'gk_download_token', rawurlencode( $token ), home_url() );
		}

		$result = [
			'url' => $download_url,
			'id'  => $token_id,
		];

		return $result;
	}

	/**
	 * Handles the download request.
	 *
	 * @since 1.3.0
	 * @since TBD Make the method private and add $token parameter.
	 *
	 * @param string $token The download token.
	 *
	 * @phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.Missing -- Exceptions are caught internally.
	 *
	 * @return void
	 */
	private function handle_download_request( $token ) {
		try {
			if ( empty( $token ) ) {
				throw new Exception( esc_html__( 'No download token provided.', 'gk-gravityview' ), 400 );
			}

			// Validate and decrypt token.
			$token_data = $this->validate_token( $token );

			if ( ! $token_data ) {
				throw new Exception( esc_html__( 'Invalid or expired download token.', 'gk-gravityview' ), $this->get_invalid_token_response_code() );
			}

			$token_data['token_id'] = $this->get_token_id( $token );

			/**
			 * Fires before a file download starts.
			 *
			 * @action `gk/foundation/secure-download/before-download`
			 *
			 * @since 1.3.0
			 *
			 * @param string $file_path  The absolute path to the file.
			 * @param int    $user_id    The user ID downloading the file.
			 * @param array  $token_data The decrypted token data.
			 */
			do_action( 'gk/foundation/secure-download/before-download', $token_data['file'], get_current_user_id(), $token_data );

			$this->stream_file( $token_data['file'], $token_data );

			$this->record_download( $token_data );

			/**
			 * Fires after a file download completes.
			 *
			 * @action `gk/foundation/secure-download/after-download`
			 *
			 * @since 1.3.0
			 *
			 * @param string $file_path  The absolute path to the file.
			 * @param int    $user_id    The user ID that downloaded the file.
			 * @param array  $token_data The decrypted token data.
			 */
			do_action( 'gk/foundation/secure-download/after-download', $token_data['file'], get_current_user_id(), $token_data );

			exit;
		} catch ( Exception $e ) {
			$error_code    = $e->getCode() ?: 500;
			$error_message = $e->getMessage();

			/**
			 * Filters the download error response.
			 * Return null to indicate the error has been handled and prevent default processing.
			 *
			 * @filter `gk/foundation/secure-download/error-response`
			 *
			 * @since 1.3.0
			 *
			 * @param array|null $error_response {
			 *     Error response data or null if handled.
			 *
			 *     @type int    $code    HTTP status code.
			 *     @type string $message Error message.
			 * }
			 * @param Exception $exception The exception that was thrown.
			 * @param string    $token     The download token that was provided.
			 */
			$error_response = apply_filters(
                'gk/foundation/secure-download/error-response',
                [
					'code'    => $error_code,
					'message' => $error_message,
                ],
                $e,
                $token
            );

			// If null is returned, the error was handled by a filter.
			if ( null === $error_response ) {
				exit;
			}

			/**
			 * Fires when a download error occurs.
			 *
			 * @action `gk/foundation/secure-download/error`
			 *
			 * @since 1.3.0
			 *
			 * @param Exception $exception The exception that was thrown.
			 * @param string    $token     The download token that was provided.
			 */
			do_action( 'gk/foundation/secure-download/error', $e, $token );

			// Default error handling.
			if ( is_array( $error_response ) && isset( $error_response['code'], $error_response['message'] ) ) {
				wp_die( esc_html( $error_response['message'] ), '', [ 'response' => (int) $error_response['code'] ] );
			} else {
				// Fallback if filter returns invalid data.
				wp_die( esc_html( $error_message ), '', [ 'response' => (int) $error_code ] );
			}
		}
	}

	/**
	 * Validates and decrypts a download token.
	 *
	 * @since 1.3.0
	 *
	 * @param string $token The encrypted token.
	 *
	 * @phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.Missing
	 *
	 * @return array|false The decrypted token data or false if invalid.
	 */
	public function validate_token( $token ) {
		$this->last_failure_code = null;

		$token_data        = null;
		$validation_result = false;
		$exception         = null;
		$failure_code      = null;

		try {
			$decrypted = $this->encryption->decrypt( $token );

			if ( ! $decrypted ) {
				throw new Exception( 'failed_decryption' );
			}

			$decoded = json_decode( $decrypted, true );

			if ( ! is_array( $decoded ) ) {
				throw new Exception( 'invalid_format' );
			}

			$token_data = $decoded;

			// Check expiration (0 means no expiration).
			if ( isset( $token_data['expires'] ) && $token_data['expires'] > 0 && time() > $token_data['expires'] ) {
				throw new Exception( 'expired' );
			}

			// Check allowed users restriction.
			if ( ! empty( $token_data['users'] ) ) {
				$current_user_id = get_current_user_id();

				if ( ! in_array( $current_user_id, $token_data['users'], true ) ) {
					throw new Exception( 'user_not_allowed' );
				}
			}

			// Check allowed IPs restriction.
			if ( ! empty( $token_data['ips'] ) ) {
				$current_ip = $this->get_visitor_ip();

				if ( ! in_array( $current_ip, $token_data['ips'], true ) ) {
					throw new Exception( 'ip_not_allowed' );
				}
			}

			// Check capabilities.
			if ( ! empty( $token_data['capabilities'] ) ) {
				foreach ( $token_data['capabilities'] as $capability ) {
					if ( ! current_user_can( $capability ) ) {
						throw new Exception( 'missing_capability' );
					}
				}
			}

			// Check single-use or limited-use token.
			if ( ! empty( $token_data['limit'] ) ) {
				$token_id    = $this->get_token_id( $token );
				$history_key = self::HISTORY_KEY_PREFIX . $token_id;

				$history = is_multisite()
					? WP::get_site_transient( $history_key )
					: WP::get_transient( $history_key );

				$download_count = is_array( $history ) ? count( $history ) : 0;

				// Check if limit exceeded.
				if ( $download_count >= $token_data['limit'] ) {
					throw new Exception( 'download_limit_exceeded' );
				}
			}

			$validation_result = $token_data;
		} catch ( Exception $e ) {
			$failure_code            = $e->getMessage();
			$this->last_failure_code = $failure_code;

			$exception = $e;
		}

		/**
		 * Allows overriding the token validation result.
		 *
		 * @filter `gk/foundation/secure-download/validate-token`
		 *
		 * @since 1.3.0
		 *
		 * @param array|false     $validation_result The validation result (token data array or false).
		 * @param array|null      $token_data        Raw token data array (may be invalid or partial).
		 * @param string          $token             The original encrypted token.
		 * @param string|null     $failure_code      Optional machine-readable failure code (e.g. 'expired').
		 * @param Exception|null  $exception         The thrown exception instance, if any.
		 */
		return apply_filters(
			'gk/foundation/secure-download/validate-token',
			$validation_result,
			$token_data,
			$token,
			$failure_code,
			$exception
		);
	}

	/**
	 * Returns the HTTP status code for the last invalid token failure.
	 *
	 * @since 1.21.0
	 *
	 * @return int
	 */
	private function get_invalid_token_response_code() {
		if ( in_array( $this->last_failure_code, [ 'expired', 'download_limit_exceeded', 'limit_reached' ], true ) ) {
			return 404;
		}

		return 403;
	}

	/**
	 * Gets the optimal buffer size based on file size.
	 *
	 * @since 1.3.0
	 *
	 * @param int $file_size The file size in bytes.
	 *
	 * @return int The optimal buffer size.
	 */
	private function get_optimal_buffer_size( $file_size ) {
		if ( $file_size < 1048576 ) {          // < 1MB: use 64KB chunks.
			return self::MIN_BUFFER_SIZE;
		} elseif ( $file_size < 104857600 ) {  // < 100MB: use 1MB chunks.
			return self::BUFFER_SIZE;
		} elseif ( $file_size < 1073741824 ) { // < 1GB: use 4MB chunks.
			return 4194304;
		} else {                               // > 1GB: use 16MB chunks.
			return self::MAX_BUFFER_SIZE;
		}
	}

	/**
	 * Builds HTTP headers for file download response.
	 *
	 * @since 1.3.0
	 *
	 * @param array $context {
	 *     Context data required to build headers.
	 *
	 *     @type string  $file_path       The absolute path to the file.
	 *     @type string  $file_name       Filename to use in response.
	 *     @type string  $mime_type       MIME type of the file.
	 *     @type int     $file_size       Total file size in bytes.
	 *     @type int     $range_start     Byte offset to start from.
	 *     @type int     $range_end       Byte offset to end at.
	 *     @type array   $token_data      Token payload data.
	 *     @type bool    $partial_content Whether this is a partial content response.
	 * }
	 *
	 * @return array Array of headers to be sent.
	 */
	private function build_download_headers( array $context ) {
		$file_path       = $context['file_path'] ?? '';
		$file_name       = $context['file_name'] ?? '';
		$mime_type       = $context['mime_type'] ?? 'application/octet-stream';
		$file_size       = $context['file_size'] ?? 0;
		$range_start     = $context['range_start'] ?? 0;
		$range_end       = $context['range_end'] ?? $file_size;
		$token_data      = $context['token_data'] ?? [];
		$partial_content = $context['partial_content'] ?? false;

		$headers = [];

		// Range header for partial content.
		if ( $partial_content ) {
			$headers['Content-Range'] = "bytes $range_start-$range_end/$file_size";
		}

		// Determine content disposition: 'inline' (default) or 'attachment' if explicitly set.
		$disposition = isset( $token_data['disposition'] ) && 'attachment' === $token_data['disposition'] ? 'attachment' : 'inline';

		// Basic download headers with UTF-8 filename support.
		$headers['Content-Type']        = $mime_type;
		$headers['Content-Disposition'] = $disposition . '; filename="' . rawurlencode( $file_name ) . '"; filename*=UTF-8\'\'' . rawurlencode( $file_name );
		$headers['Content-Length']      = (string) ( $range_end - $range_start + 1 );
		$headers['Accept-Ranges']       = 'bytes';

		// Security headers.
		$headers['X-Content-Type-Options']  = 'nosniff';
		$headers['Content-Security-Policy'] = "default-src 'none';";

		// Determine cache strategy based on cache_duration.
		if ( ! isset( $token_data['cache_duration'] ) ) {
			// Auto-detect cache duration based on file type.
			$cache_duration = $this->get_default_cache_duration( $file_path );
		} else {
			$cache_duration = $token_data['cache_duration'];
		}

		// Restrict caching for tokens with limits or expiration to ensure enforcement.
		if ( ! empty( $token_data['limit'] ) ) {
			// Limited downloads must never be cached.
			$cache_duration = 0;
		} elseif ( ! empty( $token_data['expires'] ) && $token_data['expires'] > 0 ) {
			// Cache duration must not exceed token expiration time.
			$cache_duration = min( $cache_duration, max( 0, $token_data['expires'] - time() ) );
		}

		if ( $cache_duration > 0 ) {
			// Public caching with specified duration.
			$headers['Cache-Control'] = 'public, max-age=' . $cache_duration . ', immutable';
			$headers['Expires']       = gmdate( 'D, d M Y H:i:s', time() + $cache_duration ) . ' GMT';
		} else {
			// Private (no caching).
			$headers['Cache-Control'] = 'no-store, no-cache, must-revalidate';
			$headers['Pragma']        = 'no-cache';
			$headers['Expires']       = '0';
		}

		/**
		 * Filters the headers sent for a secure file download.
		 *
		 * @filter `gk/foundation/secure-download/headers`
		 *
		 * @since 1.3.0
		 *
		 * @param array $headers Array of headers to be sent.
		 * @param array $context {
		 *     Context data for the download.
		 *
		 *     @type string $file_path       The absolute path to the file.
		 *     @type string $file_name       Filename to use in response.
		 *     @type string $mime_type       MIME type of the file.
		 *     @type int    $file_size       Total file size in bytes.
		 *     @type int    $range_start     Byte offset to start from.
		 *     @type int    $range_end       Byte offset to end at.
		 *     @type array  $token_data      Token payload data.
		 *     @type bool   $partial_content Whether this is a partial content response.
		 * }
		 */
		return apply_filters( 'gk/foundation/secure-download/headers', $headers, $context );
	}

	/**
	 * Gets the default cache duration for a file based on its extension.
	 *
	 * @since 1.3.0
	 *
	 * @param string $file_path The absolute path to the file.
	 *
	 * @return int Cache duration in seconds.
	 */
	private function get_default_cache_duration( $file_path ) {
		$extension = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );

		if ( in_array( $extension, [ 'woff', 'woff2', 'ttf', 'otf', 'eot' ], true ) ) {
			return MONTH_IN_SECONDS * 6;
		}

		if ( in_array( $extension, [ 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico', 'bmp', 'tiff', 'tif', 'avif', 'heic', 'heif', 'mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv', 'm4v', '3gp', 'ogv', 'mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a', 'wma', 'oga', 'zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'xz' ], true ) ) {
			return MONTH_IN_SECONDS * 3;
		}

		if ( in_array( $extension, [ 'pdf', 'txt', 'md', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp', 'rtf', 'css', 'js' ], true ) ) {
			return self::DEFAULT_CACHE_EXPIRATION;
		}

		if ( in_array( $extension, [ 'html', 'htm', 'xml', 'json', 'csv', 'php', 'py', 'ts', 'jsx', 'tsx', 'vue', 'svelte' ], true ) ) {
			return WEEK_IN_SECONDS;
		}

		return self::DEFAULT_CACHE_EXPIRATION;
	}

	/**
	 * Streams a file to the browser with support for large files and range requests.
	 *
	 * @since 1.3.0
	 *
	 * @param string $file_path  The absolute path to the file.
	 * @param array  $token_data Optional. The token data containing custom filename.
	 *
	 * @return void
	 */
	public function stream_file( $file_path, $token_data = [] ) {
		// Check if this is a remote URL download.
		if ( ! empty( $token_data['remote_url'] ) ) {
			$this->stream_remote_file( $token_data['remote_url'], $token_data );

			return;
		}

		// Clean any output buffers.
		while ( ob_get_level() ) {
			ob_end_clean();
		}

		if ( headers_sent() ) {
			wp_die( esc_html__( 'Cannot stream file: headers already sent.', 'gk-gravityview' ), '', [ 'response' => 500 ] );
		}

		// Disable WordPress output buffering for large downloads.
		if ( function_exists( 'wp_ob_end_flush_all' ) ) {
			wp_ob_end_flush_all();
		}

		// Optimize for large downloads.
		set_time_limit( 0 );

		if ( function_exists( 'ini_set' ) ) {
			// phpcs:ignore WordPress.PHP.IniSet.Risky, WordPress.PHP.NoSilencedErrors.Discouraged
			@ini_set( 'zlib.output_compression', 'Off' );
		}

		// Get file info.
		clearstatcache( true, $file_path );

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- suppressing warnings is intentional here for race condition handling.
		$file_size = @filesize( $file_path );

		if ( false === $file_size ) {
			// File might have been deleted between token validation and now.
			wp_die( esc_html__( 'File not found or cannot be accessed.', 'gk-gravityview' ), '', [ 'response' => 404 ] );
		}
		$file_name = ! empty( $token_data['filename'] ) ? $token_data['filename'] : basename( $file_path );
		$mime_type = wp_check_filetype( $file_path );
		$mime_type = $mime_type['type'] ?: 'application/octet-stream';

		// Handle range requests.
		$range_start     = 0;
		$range_end       = $file_size - 1;
		$partial_content = false;

		if ( isset( $_SERVER['HTTP_RANGE'] ) ) {
			$range_header = $_SERVER['HTTP_RANGE'];

			if ( preg_match( '/bytes=(\d+)-(\d*)/', $range_header, $matches ) ) {
				$range_start = (int) $matches[1];

				if ( ! empty( $matches[2] ) ) {
					$range_end = (int) $matches[2];
				}

				// Validate range bounds.
				if ( $range_start >= $file_size || $range_start > $range_end ) {
					status_header( 416 ); // Range Not Satisfiable.

					header( "Content-Range: bytes */$file_size" );

					exit;
				}

				// Clamp range_end to file size.
				if ( $range_end >= $file_size ) {
					$range_end = $file_size - 1;
				}

				$partial_content = true;
			}
		}

		// Set status header.
		if ( $partial_content ) {
			status_header( 206 );
		} else {
			status_header( 200 );
		}

		// Build and apply headers.
		$headers = $this->build_download_headers(
            [
				'file_path'       => $file_path,
				'file_name'       => $file_name,
				'mime_type'       => $mime_type,
				'file_size'       => $file_size,
				'range_start'     => $range_start,
				'range_end'       => $range_end,
				'token_data'      => $token_data,
				'partial_content' => $partial_content,
			]
        );

		foreach ( $headers as $header_name => $header_value ) {
			header( $header_name . ': ' . $header_value );
		}

		// For HEAD requests, we only need to send headers, not the file content.
		if ( 'HEAD' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
			return;
		}

		// Use direct file operations for performance reasons.
		// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_operations_fread
		// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		$handle = fopen( $file_path, 'rb' );

		if ( ! $handle ) {
			wp_die( esc_html__( 'File not found or cannot be opened.', 'gk-gravityview' ), '', [ 'response' => 404 ] );
		}

		// Seek to start position.
		if ( $range_start > 0 ) {
			fseek( $handle, $range_start );
		}

		// Stream file in chunks.
		$bytes_remaining    = $range_end - $range_start + 1;
		$optimal_buffer     = $this->get_optimal_buffer_size( $bytes_remaining );
		$optimal_flush_freq = $this->get_optimal_flush_frequency( $optimal_buffer );
		$chunk_count        = 0;

		while ( $bytes_remaining > 0 && ! feof( $handle ) ) {
			$chunk_size = min( $optimal_buffer, $bytes_remaining );

			// Ensure chunk size is positive for fread().
			if ( $chunk_size <= 0 ) {
				break;
			}

			$chunk = fread( $handle, $chunk_size );

			if ( false === $chunk ) {
				// Log error for debugging.
				error_log( sprintf( 'SecureDownload: Failed to read chunk at position %d for file %s', ftell( $handle ), $file_path ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log

				fclose( $handle );

				wp_die(
					esc_html__( 'An error occurred while reading the file. Please try again.', 'gk-gravityview' ),
					'',
					[ 'response' => 500 ]
				);
			}

			if ( 0 === strlen( $chunk ) && ! feof( $handle ) ) {
				// Unexpected empty read when not at EOF - file may have been truncated.
				error_log( sprintf( 'SecureDownload: Unexpected empty read at position %d for file %s', ftell( $handle ), $file_path ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log

				// Clean up resources.
				fclose( $handle );

				// Inform the user about the corruption.
				wp_die(
					esc_html__( 'The file appears to be corrupted or was modified during download.', 'gk-gravityview' ),
					'',
					[ 'response' => 500 ]
				);
			}

			echo $chunk; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			$bytes_remaining -= strlen( $chunk );
			++$chunk_count;

			// Check for client disconnect after each chunk for better resource management.
			if ( connection_aborted() ) {
				fclose( $handle );
				exit;
			}

			// Optimized flushing - only flush every N chunks.
			if ( 0 === $chunk_count % $optimal_flush_freq ) {
				flush();
			}
		}

		// Final flush.
		flush();

		fclose( $handle );

		// phpcs:enable WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		// phpcs:enable WordPress.WP.AlternativeFunctions.file_system_operations_fread
		// phpcs:enable WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	}

	/**
	 * Streams a remote file to the browser using temp file for scalability.
	 *
	 * @since TBD
	 *
	 * @param string $url        The remote URL to stream.
	 * @param array  $token_data The token data containing filename and other settings.
	 *
	 * @return void
	 */
	private function stream_remote_file( $url, $token_data = [] ) {
		// Create temp file for streaming with entropy in prefix to prevent predictability.
		// Use native tempnam() since wp_tempnam() is only available in admin context.
		$tmp_file = tempnam( sys_get_temp_dir(), 'gksd_' . wp_rand() . '_' );

		/**
		 * Filters the number of redirects to follow for remote downloads.
		 *
		 * @since TBD
		 *
		 * @param int    $redirection Number of redirects to follow. Default 0 (safest).
		 * @param string $url         The remote URL.
		 * @param array  $token_data  The token data.
		 */
		$redirection = apply_filters(
			'gk/foundation/secure-download/remote-redirection',
			0, // Default: no redirects (safest).
			$url,
			$token_data
		);

		/**
		 * Filters the remote request args.
		 *
		 * @since TBD
		 *
		 * @param array  $args       Request arguments for wp_safe_remote_get().
		 * @param string $url        The remote URL.
		 * @param array  $token_data The token data.
		 */
		$request_args = apply_filters(
			'gk/foundation/secure-download/remote-request-args',
			[
				'timeout'     => 300,
				'sslverify'   => true,
				'stream'      => true,
				'filename'    => $tmp_file,
				'redirection' => $redirection,
			],
			$url,
			$token_data
		);

		$response = wp_safe_remote_get( $url, $request_args );

		if ( is_wp_error( $response ) ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- File may not exist.
			@unlink( $tmp_file );

			wp_die(
				esc_html(
					strtr(
						// translators: [error] is replaced with the error message.
						__( 'Remote file fetch failed: [error]', 'gk-gravityview' ),
						[ '[error]' => $response->get_error_message() ]
					)
				),
				'',
				[ 'response' => 500 ]
			);
		}

		$status = wp_remote_retrieve_response_code( $response );

		if ( $status < 200 || $status >= 400 ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- File may not exist.
			@unlink( $tmp_file );

			$http_status = ( $status >= 400 && $status < 500 ) ? 404 : 500;
			wp_die( esc_html__( 'Remote file not available.', 'gk-gravityview' ), '', [ 'response' => (int) $http_status ] );
		}

		// Validate file size to prevent disk exhaustion attacks.
		$file_size = filesize( $tmp_file );

		if ( false === $file_size ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- File may not exist.
			@unlink( $tmp_file );

			wp_die( esc_html__( 'Failed to read downloaded file.', 'gk-gravityview' ), '', [ 'response' => 500 ] );
		}

		/**
		 * Filters the maximum allowed remote file size.
		 *
		 * @since TBD
		 *
		 * @param int    $max_size   Maximum file size in bytes. Default 104857600 (100MB).
		 * @param string $url        The remote URL.
		 * @param array  $token_data The token data.
		 */
		$max_size = apply_filters( 'gk/foundation/secure-download/max-remote-size', 100 * 1024 * 1024, $url, $token_data );

		if ( $file_size > $max_size ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- File may not exist.
			@unlink( $tmp_file );

			wp_die(
				esc_html(
					strtr(
						// translators: [actual_size] and [max_size] are replaced with file sizes.
						__( 'Remote file too large ([actual_size]). Maximum allowed: [max_size].', 'gk-gravityview' ),
						[
							'[actual_size]' => size_format( $file_size ),
							'[max_size]'    => size_format( $max_size ),
						]
					)
				),
				'',
				[ 'response' => 413 ]
			);
		}

		// Register cleanup to run regardless of how script exits.
		register_shutdown_function(
			function () use ( $tmp_file ) {
				if ( file_exists( $tmp_file ) ) {
					// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Best effort cleanup.
					@unlink( $tmp_file );
				}
			}
		);

		// Determine filename.
		$filename = '';

		if ( ! empty( $token_data['filename'] ) ) {
			$filename = $token_data['filename'];
		} else {
			// Try to extract filename from URL.
			$url_path = wp_parse_url( $url, PHP_URL_PATH );
			$filename = $url_path ? basename( $url_path ) : 'download';
		}

		// Stream as local file. Unset remote_url to prevent recursion back to this method.
		unset( $token_data['remote_url'] );

		$token_data['file']     = $tmp_file;
		$token_data['filename'] = $filename;

		$this->stream_file( $tmp_file, $token_data );
	}

	/**
	 * Records a download for tracking purposes.
	 *
	 * @since 1.3.0
	 *
	 * @param array $token_data The token data.
	 *
	 * @return void
	 */
	public function record_download( $token_data ) {
		$history_record = [
			'token_id'  => isset( $token_data['token_id'] ) ? $token_data['token_id'] : '',
			'file'      => $token_data['file'],
			'user_id'   => get_current_user_id(),
			'timestamp' => current_time( 'timestamp' ), // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
			'meta'      => isset( $token_data['meta'] ) ? $token_data['meta'] : [],
		];

		if ( empty( $token_data['token_id'] ) ) {
			return;
		}

		// Handle built-in tracking based on 'track' parameter.
		if ( ! empty( $token_data['track'] ) ) {
			$track_options = is_array( $token_data['track'] ) ? $token_data['track'] : [ 'ip', 'user_agent', 'history' ];

			// Track IP address if requested.
			if ( in_array( 'ip', $track_options, true ) ) {
				$history_record['ip_address'] = $this->get_visitor_ip();
			}

			// Track user agent if requested.
			if ( in_array( 'user_agent', $track_options, true ) ) {
				$history_record['user_agent'] = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
			}

			// Track referrer if requested.
			if ( in_array( 'referrer', $track_options, true ) ) {
				$history_record['referrer'] = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';
			}
		}

		/**
		 * Filters download information to allow adding tracking data.
		 *
		 * @filter `gk/foundation/secure-download/history-record`
		 *
		 * @since 1.3.0
		 *
		 * @param array $history_record Minimal download information.
		 * @param array $token_data    The token data.
		 */
		$history_record = apply_filters( 'gk/foundation/secure-download/history-record', $history_record, $token_data );

		/**
		 * Filters whether to store download history.
		 *
		 * @filter `gk/foundation/secure-download/save-history`
		 *
		 * @since 1.3.0
		 *
		 * @param bool  $should_save Whether to record the download. Default false.
		 * @param array $history_record The download record to save.
		 */
		$should_save = apply_filters( 'gk/foundation/secure-download/save-history', false, $history_record );

		// Auto-enable history if 'history' is in track options.
		if ( ! empty( $token_data['track'] ) ) {
			$track_options = is_array( $token_data['track'] ) ? $token_data['track'] : [ 'ip', 'user_agent', 'history' ];

			if ( in_array( 'history', $track_options, true ) ) {
				$should_save = true;
			}
		}

		// Determine if we need to store anything.
		$has_limit = ! empty( $token_data['limit'] );

		if ( ! $should_save && ! $has_limit ) {
			return;
		}

		// Store download history per token.
		$history_key = self::HISTORY_KEY_PREFIX . $token_data['token_id'];
		$history     = is_multisite() ? WP::get_site_transient( $history_key ) : WP::get_transient( $history_key );

		if ( ! is_array( $history ) ) {
			$history = [];
		}

		// Add download record.
		if ( $should_save ) {
			// Store full download info when history is enabled.
			$history[] = $history_record;
		} else {
			// Store minimal data when only tracking count for limits.
			$history[] = [
				'timestamp' => $history_record['timestamp'],
			];
		}

		/**
		 * Filters the maximum number of history entries to keep per token.
		 *
		 * @filter `gk/foundation/secure-download/history-length`
		 *
		 * @since 1.3.0
		 *
		 * @param int    $max_history Maximum number of history entries to keep. Default 100.
		 * @param string $token_id    The token ID for the download.
		 * @param array  $token_data  The full token data array.
		 */
		$max_history = apply_filters( 'gk/foundation/secure-download/history-length', self::MAX_HISTORY_PER_TOKEN, $token_data['token_id'], $token_data );

		if ( ! $max_history ) {
			return;
		}

		$history = array_slice( $history, -$max_history );

		// Calculate transient expiration.
		if ( isset( $token_data['expires'] ) && $token_data['expires'] > 0 ) {
			$expires_in = $token_data['expires'] - time();
		} else {
			$expires_in = 0;
		}

		if ( is_multisite() ) {
			WP::set_site_transient( $history_key, $history, $expires_in );
		} else {
			WP::set_transient( $history_key, $history, $expires_in );
		}
	}

	/**
	 * Gets download history for a specific token.
	 *
	 * @since 1.3.0
	 *
	 * @param string|null $token Optional. Full token or short ID to filter by. If null, returns history for all tokens.
	 * @param array       $args {
	 *     Optional arguments for filtering results.
	 *
	 *     @type int        $user_id    Filter by user ID.
	 *     @type string|int $after      Return downloads after this date (Y-m-d H:i:s string or Unix timestamp).
	 *     @type string|int $before     Return downloads before this date (Y-m-d H:i:s string or Unix timestamp).
	 *     @type int        $limit      Maximum number of results to return.
	 * }
	 *
	 * @return array Array of download records.
	 */
	public function get_download_history( $token = null, $args = [] ) {
		$defaults = [
			'user_id' => null,
			'after'   => null,
			'before'  => null,
			'limit'   => null,
		];

		$args = wp_parse_args( $args, $defaults );

		if ( is_null( $token ) ) {
			return $this->get_all_download_history( $args );
		}

		// Get token ID.
		if ( strlen( $token ) > self::SHORT_ID_LENGTH ) {
			$token_id = $this->get_token_id( $token );
		} else {
			$token_id = $token;
		}

		// Get history for this token.
		$history_key = self::HISTORY_KEY_PREFIX . $token_id;
		$history     = is_multisite() ? WP::get_site_transient( $history_key ) : WP::get_transient( $history_key );

		if ( ! is_array( $history ) ) {
			return [];
		}

		// Filter by user ID.
		if ( ! is_null( $args['user_id'] ) ) {
			$history = array_filter(
				$history,
				function ( $record ) use ( $args ) {
					return isset( $record['user_id'] ) && $record['user_id'] === $args['user_id'];
				}
			);
		}

		// Filter by date range.
		if ( ! is_null( $args['after'] ) ) {
			$after_timestamp = is_numeric( $args['after'] ) ? $args['after'] : strtotime( $args['after'] );

			$history = array_filter(
				$history,
				function ( $record ) use ( $after_timestamp ) {
					return isset( $record['timestamp'] ) && $record['timestamp'] > $after_timestamp;
				}
			);
		}

		if ( ! is_null( $args['before'] ) ) {
			$before_timestamp = is_numeric( $args['before'] ) ? $args['before'] : strtotime( $args['before'] );

			$history = array_filter(
				$history,
				function ( $record ) use ( $before_timestamp ) {
					return isset( $record['timestamp'] ) && $record['timestamp'] < $before_timestamp;
				}
			);
		}

		// Sort by timestamp descending (newest first).
		usort(
			$history,
			function ( $a, $b ) {
				$time_a = isset( $a['timestamp'] ) ? $a['timestamp'] : 0;
				$time_b = isset( $b['timestamp'] ) ? $b['timestamp'] : 0;

				return $time_b - $time_a;
			}
		);

		// Apply limit.
		if ( ! is_null( $args['limit'] ) && $args['limit'] > 0 ) {
			$history = array_slice( $history, 0, $args['limit'] );
		}

		return array_values( $history );
	}

	/**
	 * Get download history across all tokens.
	 *
	 * @since 1.3.0
	 *
	 * @param array $args {
	 *     Optional arguments for filtering results.
	 *
	 *     @type int        $user_id    Filter by user ID.
	 *     @type string|int $after      Return downloads after this date (Y-m-d H:i:s string or Unix timestamp).
	 *     @type string|int $before     Return downloads before this date (Y-m-d H:i:s string or Unix timestamp).
	 *     @type int        $limit      Maximum number of results to return.
	 * }
	 *
	 * @return array Array of download records from all tokens.
	 */
	public function get_all_download_history( $args = [] ) {
		global $wpdb;

		// Get all download history transients.
		$history_prefix = self::HISTORY_KEY_PREFIX;

		// Build LIMIT clause if needed.
		$limit_clause = '';

		if ( ! empty( $args['limit'] ) ) {
			// Fetch extra records to account for post-retrieval filtering.
			$fetch_limit  = absint( $args['limit'] ) * 3; // 3x to ensure we have enough after filtering.
			$limit_clause = $wpdb->prepare( ' LIMIT %d', $fetch_limit );
		}

		if ( is_multisite() ) {
			if ( ! empty( $limit_clause ) ) {
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $limit_clause is already prepared.
				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT meta_key, meta_value
						 FROM {$wpdb->sitemeta}
						 WHERE meta_key LIKE %s AND site_id = %d
						 ORDER BY meta_id DESC",
						$wpdb->esc_like( $history_prefix ) . '%',
						get_current_network_id()
					) . $limit_clause
				);
			} else {
				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT meta_key, meta_value
						 FROM {$wpdb->sitemeta}
						 WHERE meta_key LIKE %s AND site_id = %d
						 ORDER BY meta_id DESC",
						$wpdb->esc_like( $history_prefix ) . '%',
						get_current_network_id()
					)
				);
			}
		} else {
			if ( ! empty( $limit_clause ) ) {
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $limit_clause is already prepared.
				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT option_name, option_value
						 FROM {$wpdb->options}
						 WHERE option_name LIKE %s
						 ORDER BY option_id DESC",
						$wpdb->esc_like( '_transient_' . $history_prefix ) . '%'
					) . $limit_clause
				);
			} else {
				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT option_name, option_value
						 FROM {$wpdb->options}
						 WHERE option_name LIKE %s
						 ORDER BY option_id DESC",
						$wpdb->esc_like( '_transient_' . $history_prefix ) . '%'
					)
				);
			}
		}

		$all_history = [];

		foreach ( $results as $result ) {
			$value         = is_multisite() ? $result->meta_value : $result->option_value;
			$token_history = maybe_unserialize( $value );

			if ( is_array( $token_history ) ) {
				// Add token_id to each record for reference.
				$key      = is_multisite() ? $result->meta_key : $result->option_name;
				$token_id = str_replace( [ '_transient_' . $history_prefix, $history_prefix ], '', $key );

				foreach ( $token_history as &$record ) {
					$record['token_id'] = $token_id;
				}

				$all_history = array_merge( $all_history, $token_history );
			}
		}

		// Filter by user ID.
		if ( ! is_null( $args['user_id'] ) ) {
			$all_history = array_filter(
				$all_history,
				function ( $record ) use ( $args ) {
					return isset( $record['user_id'] ) && $record['user_id'] === $args['user_id'];
				}
			);
		}

		// Filter by date range.
		if ( ! is_null( $args['after'] ) ) {
			$after_timestamp = is_numeric( $args['after'] ) ? $args['after'] : strtotime( $args['after'] );

			$all_history = array_filter(
				$all_history,
				function ( $record ) use ( $after_timestamp ) {
					return isset( $record['timestamp'] ) && $record['timestamp'] > $after_timestamp;
				}
			);
		}

		if ( ! is_null( $args['before'] ) ) {
			$before_timestamp = is_numeric( $args['before'] ) ? $args['before'] : strtotime( $args['before'] );

			$all_history = array_filter(
				$all_history,
				function ( $record ) use ( $before_timestamp ) {
					return isset( $record['timestamp'] ) && $record['timestamp'] < $before_timestamp;
				}
			);
		}

		// Sort by timestamp descending (newest first).
		usort(
			$all_history,
			function ( $a, $b ) {
				$time_a = isset( $a['timestamp'] ) ? $a['timestamp'] : 0;
				$time_b = isset( $b['timestamp'] ) ? $b['timestamp'] : 0;

				return $time_b - $time_a;
			}
		);

		// Apply limit.
		if ( ! is_null( $args['limit'] ) && $args['limit'] > 0 ) {
			$all_history = array_slice( $all_history, 0, $args['limit'] );
		}

		return array_values( $all_history );
	}

	/**
	 * Registers rewrite rules for pretty download URLs.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public function register_rewrite_rules() {
		add_rewrite_rule(
			'^' . $this->get_endpoint() . '/([^/]+)/?$',
			'index.php?gk_download_token=$matches[1]',
			'top'
		);
	}

	/**
	 * Adds query variables for rewrite rules.
	 *
	 * @since 1.3.0
	 *
	 * @param array $query_vars Existing query variables.
	 *
	 * @return array Modified query variables.
	 */
	public function add_query_vars( $query_vars ) {
		$query_vars[] = 'gk_download_token';

		return $query_vars;
	}

	/**
	 * Handles download requests from rewrite rules.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public function handle_rewrite_request() {
		$token = get_query_var( 'gk_download_token' );

		if ( ! empty( $token ) ) {
			$this->handle_download_request( $token );
		}
	}

	/**
	 * Gets the visitor IP address.
	 *
	 * @since 1.3.0
	 *
	 * @return string The visitor IP address.
	 */
	private function get_visitor_ip() {
		$ip = '';

		// Check for various IP headers in order of preference.
		if ( ! empty( $_SERVER['HTTP_X_REAL_IP'] ) ) {
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			// X-Forwarded-For can contain multiple IPs, get the first one.
			$ips = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
			$ip  = trim( $ips[0] );
		} elseif ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		/**
		 * Filters the visitor IP address.
		 *
		 * @filter `gk/foundation/secure-download/visitor-ip`
		 *
		 * @since 1.3.0
		 *
		 * @param string $ip The detected IP address.
		 */
		$ip = apply_filters( 'gk/foundation/secure-download/visitor-ip', $ip );

		return $ip;
	}
}
