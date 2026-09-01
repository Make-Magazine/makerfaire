<?php
/**
 * GravityView Extension -- DataTables -- AJAX response envelope
 *
 * @since     3.11.0
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      https://www.gravitykit.com
 *
 * @package   GravityView
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Guarantees the DataTables AJAX endpoint answers with parseable JSON.
 *
 * DataTables has no recovery path for a body it cannot parse: it throws
 * "DataTables warning: Invalid JSON response" to the console and leaves the table
 * on "Loading data…" forever. Anything that can reach the browser instead of JSON
 * therefore has to be intercepted here: stray output from another plugin, a fatal
 * mid-request, or a bail that would otherwise answer with a zero-byte body.
 *
 * @since 3.12.0
 */
class GV_Extension_DataTables_Response {

	/**
	 * Error codes this envelope can carry.
	 */
	const CODE_FATAL         = 'fatal';
	const CODE_INVALID_NONCE = 'invalid_nonce';
	const CODE_INVALID_VIEW  = 'invalid_view';
	const CODE_ENCODE_FAILED = 'encode_failed';
	const CODE_STALE_CONFIG  = 'stale_config';

	/**
	 * The response owning the current request, if any.
	 *
	 * The shutdown handler must be inert for every request that is not this AJAX
	 * action, so it resolves the active response through this property rather than
	 * being registered globally.
	 *
	 * @var self|null
	 */
	private static $active;

	/**
	 * Identifier shared between the response body and the log entry, so an admin can
	 * tie a visitor's report to the server-side detail.
	 *
	 * @var string
	 */
	private $correlation_id;

	/**
	 * Whether the response already emitted a body. Disarms the shutdown handler.
	 *
	 * @var bool
	 */
	private $finished = false;

	/**
	 * Plugin directory of the first PHP error raised during the request.
	 *
	 * Attribution has to be captured while the error fires. A backtrace taken after
	 * the output is buffered no longer contains the offender.
	 *
	 * @var string
	 */
	private $suspected_source = 'unknown';

	/**
	 * Request start, in seconds with microsecond precision.
	 *
	 * @var float
	 */
	private $started_at;

	/**
	 * Error state injected by tests, since a real fatal cannot be raised inside PHPUnit.
	 *
	 * @var array|null
	 */
	private $last_error_override;

	/**
	 * Output-buffer level begin() opened, or null once that buffer has been closed.
	 *
	 * discard_buffer() may be invoked twice for the same response (finish() falls
	 * through to error() on an encode failure), and it also runs alongside whatever
	 * ambient buffer WordPress or another plugin already had open when begin() ran.
	 * Recording the exact level lets teardown close only the buffer this response
	 * owns, and become a no-op once that buffer is gone, instead of grabbing
	 * whatever buffer happens to be on top.
	 *
	 * @var int|null
	 */
	private $buffer_level;

	private function __construct() {
		$this->correlation_id = wp_generate_uuid4();
		$this->started_at     = microtime( true );
	}

	/**
	 * Opens the response: buffers output, arms the fatal handler, starts the timer.
	 *
	 * @since 3.12.0
	 *
	 * @return self
	 */
	public static function begin() {
		$response = new self();

		self::$active = $response;

		ob_start();

		$response->buffer_level = ob_get_level();

		$response->watch_for_php_errors();

		register_shutdown_function( array( __CLASS__, 'handle_shutdown' ) );

		return $response;
	}

	/**
	 * Records the source of the first PHP error without suppressing normal handling.
	 *
	 * @return void
	 */
	private function watch_for_php_errors() {
		$response = $this;

		set_error_handler(
			function ( $errno, $errstr, $errfile = '' ) use ( $response ) {
				$response->note_error_source( $errfile );

				// Returning false hands the error back to PHP so logging and display behave as configured.
				return false;
			},
			E_NOTICE | E_WARNING | E_DEPRECATED | E_USER_NOTICE | E_USER_WARNING | E_USER_DEPRECATED
		);
	}

	/**
	 * Attributes an error to the plugin directory it came from.
	 *
	 * @since 3.12.0
	 *
	 * @param string $file Absolute path of the file that raised the error.
	 *
	 * @return void
	 */
	public function note_error_source( $file ) {
		$already_attributed = 'unknown' !== $this->suspected_source;

		if ( $already_attributed || '' === (string) $file ) {
			return;
		}

		$plugins_dir  = trailingslashit( wp_normalize_path( WP_PLUGIN_DIR ) );
		$normalized   = wp_normalize_path( (string) $file );
		$is_in_plugin = 0 === strpos( $normalized, $plugins_dir );

		if ( ! $is_in_plugin ) {
			return;
		}

		$relative = substr( $normalized, strlen( $plugins_dir ) );
		$segments = explode( '/', $relative );

		$this->suspected_source = $segments[0];
	}

	/**
	 * Converts a fatal into a parseable body, as a fallback.
	 *
	 * Uncaught Errors are caught in the handler itself, which is the reliable path.
	 * This one covers what cannot be caught (memory exhaustion, timeouts) and is
	 * best-effort only: WordPress registers its own fatal handler during bootstrap and
	 * flushes output buffers on `shutdown` before this runs, so on a true crash its
	 * "critical error" HTML can already be on the wire. `wp_should_handle_php_error`
	 * cannot prevent that, because core returns true for E_ERROR before applying the
	 * filter (see WP_Fatal_Error_Handler::should_handle_error()).
	 *
	 * @since 3.12.0
	 *
	 * @return void
	 */
	public static function handle_shutdown() {
		$response = self::$active;

		if ( ! $response instanceof self ) {
			return;
		}

		$response->emit_fatal_envelope();
	}

	/**
	 * @return void
	 */
	private function emit_fatal_envelope() {
		if ( $this->finished ) {
			return;
		}

		$last_error = null === $this->last_error_override ? error_get_last() : $this->last_error_override;
		$fatal_types = array( E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR );
		$is_fatal    = is_array( $last_error ) && in_array( $last_error['type'], $fatal_types, true );

		if ( ! $is_fatal ) {
			return;
		}

		// WordPress's own fatal output is already in these buffers; none of it may reach the body.
		while ( ob_get_level() > 0 ) {
			ob_end_clean();
		}

		$this->finished = true;

		$this->log(
			'Fatal error during DataTables response',
			array(
				'message' => $last_error['message'],
				'file'    => $last_error['file'],
				'line'    => $last_error['line'],
			)
		);

		// The message, file and line are what identify the offending plugin, and they are the
		// whole reason an administrator needs a different answer here than a visitor does.
		$payload = $this->maybe_add_diagnostics(
			$this->error_payload( self::CODE_FATAL ),
			'',
			array(
				'code'    => self::CODE_FATAL,
				'message' => $last_error['message'],
				'file'    => $last_error['file'],
				'line'    => $last_error['line'],
			)
		);

		echo $this->encode_or_bare_error( $payload, self::CODE_FATAL );
	}

	/**
	 * Seams the fatal path for tests, which cannot raise a real fatal.
	 *
	 * @since 3.12.0
	 *
	 * @param array|null $error An `error_get_last()`-shaped array.
	 *
	 * @return void
	 */
	public function set_last_error_for_testing( $error ) {
		if ( ! defined( 'DOING_GRAVITYVIEW_TESTS' ) ) {
			return;
		}

		$this->last_error_override = $error;
	}

	/**
	 * Builds the error body.
	 *
	 * The visitor-facing shape is deliberately minimal: this endpoint is
	 * nopriv-reachable, so it must return nothing usable for reconnaissance. Detail
	 * lives in the log, tied to the correlation ID.
	 *
	 * @since 3.12.0
	 *
	 * @param string $code One of the CODE_* constants.
	 *
	 * @return array
	 */
	private function error_payload( $code ) {
		$payload = array(
			'draw'            => $this->requested_draw(),
			'recordsTotal'    => 0,
			'recordsFiltered' => 0,
			'data'            => array(),
			'error'           => __( 'This table could not load. Please try again.', 'gv-datatables' ),
			'gv_error'        => array(
				'code'           => $code,
				'correlation_id' => $this->correlation_id,
			),
		);

		/**
		 * Modifies the error payload returned to the client.
		 *
		 * Unrestricted for administrators only. This endpoint is nopriv-reachable, so
		 * anyone -- not only whoever registered this filter -- can trigger it; for every
		 * other requester, {@see self::restrict_to_visitor_shape()} discards anything the
		 * callback adds or changes beyond the `error` message text, so entry data, paths,
		 * or SQL returned here cannot reach an anonymous response.
		 *
		 * @since 3.12.0
		 *
		 * @param array  $payload The error payload.
		 * @param string $code    The error code.
		 */
		$filtered = apply_filters( 'gk/gravityview/datatables/response/error-payload', $payload, $code );

		if ( current_user_can( 'manage_options' ) && is_array( $filtered ) ) {
			return $filtered;
		}

		return $this->restrict_to_visitor_shape( $payload, $filtered );
	}

	/**
	 * Limits a filtered error payload to its documented shape for a non-privileged
	 * requester.
	 *
	 * `gk/gravityview/datatables/response/error-payload` runs on every request,
	 * including anonymous ones, before any capability check narrows what a callback
	 * may add. Without this, a filter registered for legitimate logging purposes (or a
	 * malicious one exploiting the same nopriv-reachable hook) could bolt file paths,
	 * entry data, or other diagnostics onto every visitor's error response. Everything
	 * except the human-readable `error` string reverts to the payload this class built;
	 * the filter's only effect for a non-admin is customizing that one message.
	 *
	 * @param array $baseline The unfiltered payload.
	 * @param mixed $filtered Whatever the filter returned.
	 *
	 * @return array
	 */
	private function restrict_to_visitor_shape( array $baseline, $filtered ) {
		$restricted = $baseline;

		if ( is_array( $filtered ) && isset( $filtered['error'] ) && is_string( $filtered['error'] ) ) {
			$restricted['error'] = $filtered['error'];
		}

		return $restricted;
	}

	/**
	 * DataTables discards a response whose `draw` does not match the request it sent.
	 *
	 * @return int
	 */
	private function requested_draw() {
		$draw = isset( $_POST['draw'] ) ? (int) $_POST['draw'] : 0;

		return $draw > 0 ? $draw : 0;
	}

	/**
	 * Answers with an error body and stops the response.
	 *
	 * No `Content-Length` is sent: a length computed for a different body truncates or
	 * stalls this one.
	 *
	 * @since 3.12.0
	 *
	 * @param string $code    One of the CODE_* constants.
	 * @param array  $context Extra detail for administrators, e.g. a caught throwable's
	 *                        message, file and line. Never reaches a non-admin.
	 *
	 * @return string The JSON body.
	 */
	public function error( $code, array $context = array() ) {
		$stray_output = $this->discard_buffer();

		$this->finished = true;
		self::$active   = null;

		restore_error_handler();

		$payload = $this->error_payload( $code );

		// Unconditional for an administrator: a failure with no stray output (a refused
		// nonce, an encode failure) still leaves them looking at the same sentence a visitor
		// gets, with nothing to act on.
		$payload = $this->maybe_add_diagnostics( $payload, $stray_output, array_merge( array( 'code' => $code ), $context ) );

		GV_Extension_DataTables_Data::send_response_header( 'Content-Type: application/json; charset=UTF-8' );

		$this->log( 'DataTables response error: ' . $code, array( 'stray_output_bytes' => strlen( $stray_output ) ) );

		return $this->encode_or_bare_error( $payload, $code );
	}

	/**
	 * Encodes an error body, degrading to the visitor-shaped one rather than to nothing.
	 *
	 * Diagnostics carry strings this plugin did not author -- a throwable's message, an
	 * excerpt of another plugin's output -- so malformed UTF-8 can reach the encoder.
	 * Without substitution `wp_json_encode()` returns false, which casts to an empty body:
	 * the exact "Invalid JSON response" dead end this envelope exists to prevent, and it
	 * would strike administrators only, since they are the ones who get diagnostics.
	 *
	 * @since 3.12.0
	 *
	 * @param array  $payload The error payload, possibly carrying diagnostics.
	 * @param string $code    One of the CODE_* constants.
	 *
	 * @return string A non-empty JSON body.
	 */
	private function encode_or_bare_error( array $payload, $code ) {
		$json = wp_json_encode( $payload, JSON_INVALID_UTF8_SUBSTITUTE );

		if ( false !== $json ) {
			return $json;
		}

		// Something in the payload defeated even substitution (a resource, a cycle). The
		// visitor-shaped body is built entirely from this class's own strings.
		$this->log( 'Failed to encode DataTables error payload', array( 'json_error' => json_last_error_msg() ) );

		$bare = wp_json_encode(
			array(
				'draw'            => $this->requested_draw(),
				'recordsTotal'    => 0,
				'recordsFiltered' => 0,
				'data'            => array(),
				'error'           => __( 'This table could not load. Please try again.', 'gv-datatables' ),
				'gv_error'        => array(
					'code'           => $code,
					'correlation_id' => $this->correlation_id,
				),
			),
			JSON_INVALID_UTF8_SUBSTITUTE
		);

		if ( false !== $bare ) {
			return $bare;
		}

		// Nothing left to encode from, so hand-assemble the same envelope shape. The draw
		// counter still has to echo the request or DataTables discards the response, and the
		// client keys its failure handling off `gv_error`.
		return sprintf(
			'{"draw":%d,"recordsTotal":0,"recordsFiltered":0,"data":[],"error":"This table could not load. Please try again.","gv_error":{"code":"%s","correlation_id":"%s"}}',
			$this->requested_draw(),
			preg_replace( '/[^a-z_]/', '', (string) $code ),
			preg_replace( '/[^a-f0-9\-]/', '', (string) $this->correlation_id )
		);
	}

	/**
	 * Completes a successful response.
	 *
	 * Encoding happens here so malformed UTF-8 in entry data degrades to substitution
	 * characters instead of making `wp_json_encode()` return false, which would ship as a
	 * zero-byte success.
	 *
	 * @since 3.12.0
	 *
	 * @param array $output The response data.
	 *
	 * @return string The JSON body.
	 */
	public function finish( array $output ) {
		$stray_output = $this->discard_buffer();

		if ( '' !== $stray_output ) {
			$this->log(
				'Output generated during DataTables response was discarded',
				array(
					'bytes'            => strlen( $stray_output ),
					'suspected_source' => $this->suspected_source,
					'output'           => substr( $stray_output, 0, 500 ),
				)
			);

			$output = $this->maybe_add_diagnostics( $output, $stray_output );
		}

		$json           = wp_json_encode( $output, JSON_INVALID_UTF8_SUBSTITUTE );
		$encode_failed  = false === $json;

		if ( $encode_failed ) {
			$this->log( 'Failed to encode DataTables response', array( 'json_error' => json_last_error_msg() ) );

			return $this->error( self::CODE_ENCODE_FAILED );
		}

		$this->finished = true;
		self::$active   = null;

		restore_error_handler();

		GV_Extension_DataTables_Data::send_response_header( 'Content-Type: application/json; charset=UTF-8' );
		GV_Extension_DataTables_Data::send_response_header(
			sprintf( 'Server-Timing: gvdt;dur=%.1f', ( microtime( true ) - $this->started_at ) * 1000 )
		);

		return $json;
	}

	/**
	 * Appends diagnostics for administrators only.
	 *
	 * Everything here is troubleshooting detail an anonymous requester must never see, so
	 * this endpoint being nopriv-reachable is exactly why the capability check is the first
	 * thing it does.
	 *
	 * @param array  $payload      The payload to extend.
	 * @param string $stray_output Output captured during the request, if any.
	 * @param array  $context      Extra fields to expose, e.g. a fatal's message/file/line.
	 *
	 * @return array
	 */
	private function maybe_add_diagnostics( array $payload, $stray_output = '', array $context = array() ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $payload;
		}

		$diagnostics = array(
			'correlation_id'   => $this->correlation_id,
			'suspected_source' => $this->suspected_source,
		);

		// Only a polluted response has this, and an empty excerpt reads as "nothing was
		// written" rather than "this failure has no output to show".
		if ( '' !== (string) $stray_output ) {
			$diagnostics['polluted_output'] = substr( $stray_output, 0, 500 );
			$diagnostics['polluted_bytes']  = strlen( $stray_output );
		}

		// An extension may already have added its own structured diagnostics through the
		// error-payload filter, which runs unrestricted for administrators. Overwriting the
		// key would discard theirs.
		$existing = isset( $payload['diagnostics'] ) && is_array( $payload['diagnostics'] ) ? $payload['diagnostics'] : array();

		$payload['diagnostics'] = array_merge( $existing, $diagnostics, $context );

		return $payload;
	}

	/**
	 * Closes the buffer this response opened and returns whatever was written to it.
	 *
	 * Idempotent and level-aware: a second call (finish() falling through to error()
	 * on an encode failure) is a no-op rather than consuming whatever ambient
	 * WordPress or plugin buffer happens to be open by then, and this never closes a
	 * buffer below the level begin() recorded, so a buffer that was already running
	 * before this response started survives untouched.
	 *
	 * @return string
	 */
	private function discard_buffer() {
		if ( null === $this->buffer_level || ob_get_level() < $this->buffer_level ) {
			$this->buffer_level = null;

			return '';
		}

		// Closes this response's own buffer, plus any buffer nested above it that another
		// plugin opened without closing; never descends below the level begin() recorded.
		$captured = '';

		while ( ob_get_level() >= $this->buffer_level ) {
			$captured = (string) ob_get_clean() . $captured;
		}

		$this->buffer_level = null;

		return $captured;
	}

	/**
	 * @param string $message Log message.
	 * @param array  $context Additional context.
	 *
	 * @return void
	 */
	private function log( $message, array $context = array() ) {
		if ( ! function_exists( 'gravityview' ) ) {
			return;
		}

		$context['correlation_id'] = $this->correlation_id;

		gravityview()->log->error( $message, array( 'data' => $context ) );
	}

	/**
	 * @since 3.12.0
	 *
	 * @return string
	 */
	public function get_correlation_id() {
		return $this->correlation_id;
	}
}
