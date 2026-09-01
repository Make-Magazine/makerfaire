<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\AccountConnection;

use GravityKit\GravityImport\Foundation\Encryption\Encryption;
use Throwable;
use WP_Error;

/**
 * Drives the site side of the account-connection handshake: begins the PKCE
 * flow, exchanges the authorization code for a signed connection, and revokes
 * it. Connection kernel plumbing only — domain-blind: consumers integrate
 * through the established/disconnected events and the signed channel.
 *
 * @since 1.26.0
 */
final class ConnectClient {
	/**
	 * Non-autoloaded option holding the in-flight handshake state.
	 *
	 * @since 1.26.0
	 */
	public const PENDING_OPTION = 'gk_connection_pending';

	/**
	 * Seconds a pending handshake stays valid.
	 *
	 * @since 1.26.0
	 */
	public const PENDING_TTL = 600;

	/**
	 * REST path prefix on the gk-connect server.
	 *
	 * @since 1.26.0
	 */
	public const REST_PREFIX = '/wp-json/gk-connect/v1';

	/**
	 * Default server base URL when GK_CONNECT_URL is undefined.
	 *
	 * @since 1.26.0
	 */
	public const DEFAULT_SERVER = 'https://www.gravitykit.com';

	/**
	 * Immutable path segment appended to the network home URL so a network-scope connection advertises a
	 * synthetic URL distinct from a same-account main-site connection. Never change it: it keys account-side
	 * identity, storage, and EDD activation, so changing it post-release would strand all three.
	 *
	 * @since 1.26.0
	 */
	public const NETWORK_URL_MARKER = '/.gravitykit-network';

	/**
	 * Network option persisting the server clock offset learned from a signed `timestamp_skew` response, so a
	 * skewed clock stays reconciled across requests. Network-scoped because the skew is install-wide (one PHP
	 * clock for every blog); single-site collapses it to the site option.
	 *
	 * @since 1.26.0
	 */
	public const CLOCK_OFFSET_OPTION = 'gk_connect_clock_offset';

	/**
	 * Largest absolute clock offset, in seconds, adopted from a verified skew response. Bounds a
	 * signed-but-absurd server_time (~366 days) while allowing real-world clock drift.
	 *
	 * @since 1.26.0
	 */
	public const MAX_CLOCK_OFFSET_SECONDS = 31622400;

	/**
	 * Request-scoped memo of the persisted clock offset.
	 *
	 * @since 1.26.0
	 *
	 * @var int|null
	 */
	private static $clock_offset_memo = null;

	/**
	 * Connection store.
	 *
	 * @since 1.26.0
	 *
	 * @var TokenStore
	 */
	private $store;

	/**
	 * HTTP transport: fn( string $url, array $args ): array{body:string,headers:array}|WP_Error.
	 *
	 * @since 1.26.0
	 *
	 * @var callable
	 */
	private $http;

	/**
	 * @since 1.26.0
	 *
	 * @param TokenStore    $store Connection store.
	 * @param callable|null $http  Optional HTTP transport. Defaults to a wp_remote_post wrapper.
	 */
	public function __construct( TokenStore $store, ?callable $http = null ) {
		$this->store = $store;
		$this->http  = $http ?? [ $this, 'default_http' ];
	}

	/**
	 * Returns the URL a connection of the given scope advertises to the account server. A network connection
	 * advertises a synthetic URL (the network home URL plus the reserved marker) so it stays a distinct EDD
	 * activation and isolated storage owner from a same-account main-site connection sharing the real URL.
	 *
	 * @since 1.26.0
	 *
	 * @param string $scope Resolved connection scope.
	 *
	 * @return string
	 */
	public static function connection_url( string $scope ): string {
		if ( TokenStore::SCOPE_NETWORK === $scope ) {
			return untrailingslashit( network_home_url() ) . self::NETWORK_URL_MARKER;
		}

		return untrailingslashit( home_url() );
	}

	/**
	 * Begins the handshake: generates the keypair + PKCE material, persists the
	 * pending record, and requests an authorization URL from the server.
	 *
	 * @since 1.26.0
	 *
	 * @param string        $redirect_uri Where the server redirects after authorization.
	 * @param array<string> $scopes       Requested scopes (caller-supplied; the kernel has no default).
	 * @param string        $scope        Caller-decided connection scope. Default: 'site'. Accepts 'network' or 'site'.
	 *
	 * @return array{authorize_url:string}|WP_Error
	 */
	public function begin( string $redirect_uri, array $scopes, string $scope = TokenStore::SCOPE_SITE ) {
		$keypair         = sodium_crypto_sign_keypair();
		$private_key_hex = bin2hex( sodium_crypto_sign_secretkey( $keypair ) );
		$public_key_hex  = bin2hex( sodium_crypto_sign_publickey( $keypair ) );

		$code_verifier  = sodium_bin2base64( random_bytes( 32 ), SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING );
		$code_challenge = sodium_bin2base64( hash( 'sha256', $code_verifier, true ), SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING );
		$state          = sodium_bin2base64( random_bytes( 32 ), SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING );

		$private_key_enc = Encryption::get_instance()->encrypt( $private_key_hex );

		if ( false === $private_key_enc ) {
			return new WP_Error(
				'gk_connection_encrypt_failed',
				$this->error_message( 'gk_connection_encrypt_failed', __( "This site couldn't secure the connection key. Please try again.", 'gk-foundation' ) )
			);
		}

		// Normalize the caller-decided scope and pin it into the pending record: the server response is never
		// trusted to set it, so a compromised server cannot widen a per-site connection to network on exchange.
		$scope = TokenStore::SCOPE_NETWORK === $scope ? TokenStore::SCOPE_NETWORK : TokenStore::SCOPE_SITE;

		// A network-scope connection identifies by a synthetic URL (network home URL + the reserved marker), so
		// it coexists with a same-account main-site connection as an independent activation and isolated storage.
		// Pin the resolved URL into the pending record so /token replays the exact value /request registered (the
		// exchange can run in a different admin context, where home_url() would resolve to a different site).
		$connection_home_url = self::connection_url( $scope );

		// The state must travel inside the redirect_uri: the server appends the one-time code to the
		// stored redirect_uri and never adds state itself, so the return leg only carries state back
		// when it is part of this URL.
		$redirect_uri = add_query_arg( 'state', $state, $redirect_uri );

		$persisted = update_option(
			self::PENDING_OPTION,
			[
				'state'           => $state,
				'code_verifier'   => $code_verifier,
				'private_key_enc' => $private_key_enc,
				'public_key_hex'  => $public_key_hex,
				'scopes'          => $scopes,
				'scope'           => $scope,
				'home_url'        => $connection_home_url,
				'redirect_uri'    => $redirect_uri,
				'created_at'      => time(),
			],
			false
		);

		if ( ! $persisted ) {
			return new WP_Error(
				'gk_connection_pending_failed',
				$this->error_message( 'gk_connection_pending_failed', __( "This site couldn't save the connection request. Please try again.", 'gk-foundation' ) )
			);
		}

		$result = $this->request(
			'/request',
			[
				'home_url'              => $connection_home_url,
				'redirect_uri'          => $redirect_uri,
				'code_challenge'        => $code_challenge,
				'code_challenge_method' => 'S256',
				'flow'                  => 'redirect',
				'requested_scopes'      => $scopes,
				'site_products'         => $this->site_products(),
				'install_context'       => $this->install_context( $scope ),
			]
		);

		// A handshake that never produced an authorize URL is dead — drop its pending record so it neither
		// lingers as a phantom "in flight" nor blocks a later connect attempt until the TTL expires. Only ever
		// delete THIS invocation's pending (matched by its one-time state) so a failed begin never wipes a
		// concurrent begin that overwrote the shared single-slot pending option.
		if ( $result instanceof WP_Error ) {
			$this->delete_pending_if_owned( $state );

			// A received-but-unusable response (a firewall/WAF 403, a 5xx, or a non-JSON body) carries an HTTP
			// status; a bare transport failure carries none. Only the latter is truly "offline".
			$error_data = $result->get_error_data();
			$status     = is_array( $error_data ) ? (int) ( $error_data['status'] ?? 0 ) : 0;

			if ( $status > 0 ) {
				/* translators: [status] is replaced with the HTTP status (e.g. "HTTP 403") and must not be translated. */
				$message = $this->error_message( 'gk_connection_blocked', __( 'GravityKit.com returned an unexpected response ([status]) when starting the connection — a firewall or security rule may be blocking it.', 'gk-foundation' ) );

				return new WP_Error(
					'gk_connection_blocked',
					strtr( $message, [ '[status]' => 'HTTP ' . $status ] ),
					[ 'status' => $status ]
				);
			}

			return new WP_Error(
				'gk_connection_offline',
				$this->error_message( 'gk_connection_offline', __( "We couldn't reach GravityKit.com to start the connection. Check that your site can make outbound requests.", 'gk-foundation' ) )
			);
		}

		$authorize_url = (string) ( $result['data']['authorize_url'] ?? '' );

		if ( '' === $authorize_url ) {
			$this->delete_pending_if_owned( $state );

			if ( 'rate_limited' === (string) ( $result['data']['code'] ?? '' ) ) {
				return new WP_Error(
					'gk_connection_rate_limited',
					__( 'Too many connection attempts. Please try again in about an hour.', 'gk-foundation' )
				);
			}

			return new WP_Error(
				'gk_connection_begin_failed',
				__( "GravityKit.com couldn't start the connection. Please try again from this page.", 'gk-foundation' )
			);
		}

		return [ 'authorize_url' => $authorize_url ];
	}

	/**
	 * Deletes the pending record only when it is still the one this begin() invocation created, matched by its
	 * one-time state. The pending option is a single per-blog slot, so a failed handshake must not delete a
	 * concurrent handshake that overwrote it in the meantime.
	 *
	 * @since 1.26.0
	 *
	 * @param string $state The one-time state generated by this invocation.
	 *
	 * @return void
	 */
	public function delete_pending_if_owned( string $state ): void {
		$pending = get_option( self::PENDING_OPTION );
		$stored  = is_array( $pending ) ? (string) ( $pending['state'] ?? '' ) : '';

		if ( '' !== $stored && hash_equals( $stored, $state ) ) {
			delete_option( self::PENDING_OPTION );
		}
	}

	/**
	 * Resolves the user-facing copy for a connection error code.
	 *
	 * Consumers supply presentation copy via the filter; the kernel default stays neutral.
	 *
	 * @since 1.26.0
	 *
	 * @param string $code    WP_Error code.
	 * @param string $default Neutral default message.
	 *
	 * @return string
	 */
	private function error_message( string $code, string $default ): string {
		/**
		 * Filters the user-facing messages for connection error codes.
		 *
		 * @since 1.26.0
		 *
		 * @param array<string,string> $messages Messages keyed by WP_Error code.
		 */
		$messages = (array) apply_filters( 'gk/foundation/connection/error-messages', [] );

		$message = $messages[ $code ] ?? '';

		return is_string( $message ) && '' !== $message ? $message : $default;
	}

	/**
	 * Returns the connection store this client operates on.
	 *
	 * @since 1.26.0
	 *
	 * @return TokenStore
	 */
	public function store(): TokenStore {
		return $this->store;
	}

	/**
	 * Exchanges the authorization code for a connection: validates the pending
	 * record (state, expiry, single-use), verifies the signed response, stores
	 * the connection, and fires the established event.
	 *
	 * @since 1.26.0
	 *
	 * @param string $code  Authorization code from the server.
	 * @param string $state State returned by the server.
	 *
	 * @return array{connection:array, data:array}|WP_Error
	 */
	public function exchange( string $code, string $state ) {
		// Peek, do NOT consume yet: a forged return callback (attacker URL opened by a logged-in
		// admin, random state) must not be able to destroy a legitimate in-flight handshake. Only
		// the owner — proven by the single-use state token below — consumes the pending record.
		$pending = get_option( self::PENDING_OPTION );

		if ( ! is_array( $pending ) || empty( $pending ) ) {
			return new WP_Error( 'gk_connection_no_pending', 'No pending connection was found.' );
		}

		$created_at = (int) ( $pending['created_at'] ?? 0 );
		$now        = time();

		if ( $created_at <= 0 || $created_at > $now || $now - $created_at >= self::PENDING_TTL ) {
			return new WP_Error( 'gk_connection_expired', 'The connection request has expired.' );
		}

		$expected_state = (string) ( $pending['state'] ?? '' );

		if ( '' === $state || '' === $expected_state || ! hash_equals( $expected_state, $state ) ) {
			return new WP_Error( 'gk_connection_bad_state', 'The connection state did not match.' );
		}

		// State verified: this caller owns the pending record. Consume it now (single-use) —
		// compare-and-consume, so only a legitimate exchange deletes it.
		$this->delete_pending_if_owned( $state );

		$private_key_hex = Encryption::get_instance()->decrypt( (string) ( $pending['private_key_enc'] ?? '' ) );

		if ( null === $private_key_hex ) {
			return new WP_Error( 'gk_connection_decrypt_failed', 'Could not decrypt the connection key.' );
		}

		$result = $this->request(
			'/token',
			[
				'code'          => $code,
				'code_verifier' => (string) ( $pending['code_verifier'] ?? '' ),
				'site_pubkey'   => (string) ( $pending['public_key_hex'] ?? '' ),
				// Replay the exact URL registered at /request (synthetic marker for network scope) so both legs match.
				'home_url'      => (string) ( $pending['home_url'] ?? home_url() ),
			]
		);

		if ( $result instanceof WP_Error ) {
			return $result;
		}

		$data              = $result['data'];
		$connection_id     = (string) ( $data['connection_id'] ?? '' );
		$server_pubkey_hex = (string) ( $data['server_pubkey'] ?? '' );

		if ( '' === $connection_id || '' === $server_pubkey_hex ) {
			return new WP_Error( 'gk_connection_incomplete', 'The server response was missing connection details.' );
		}

		$verifier = new SignedRequest( $connection_id, $private_key_hex, $server_pubkey_hex );

		if ( ! $verifier->verify_response( $result['body'], $result['headers'] ) ) {
			return new WP_Error( 'gk_connection_bad_signature', 'The server response signature was invalid.' );
		}

		// Restore the locally-decided scope from the pending record; never derive it from the server response.
		$scope = TokenStore::SCOPE_NETWORK === ( $pending['scope'] ?? '' ) ? TokenStore::SCOPE_NETWORK : TokenStore::SCOPE_SITE;

		// The granted scopes come from the verified response so consumers can feature-detect locally
		// (e.g. an account_storage call fails fast without a network round trip).
		$granted_scopes = array_values( array_filter( array_map( 'strval', (array) ( $data['scopes'] ?? [] ) ) ) );

		$connection = [
			'connection_id' => $connection_id,
			'private_key'   => $private_key_hex,
			'server_pubkey' => $server_pubkey_hex,
			'account_email' => (string) ( $data['account']['email'] ?? '' ),
			'scope'         => $scope,
			'scopes'        => $granted_scopes,
			'created_at'    => time(),
		];

		if ( ! $this->store->save( $connection ) ) {
			return new WP_Error( 'gk_connection_save_failed', 'Could not persist the established connection.' );
		}

		// The verified response minus connection plumbing: domain sections (per granted scope) live here
		// for consumers; the kernel does not interpret them.
		$sections = $data;

		unset( $sections['connection_id'], $sections['server_pubkey'] );

		/**
		 * Fires after a connection is established and persisted.
		 *
		 * @since 1.26.0
		 *
		 * @param array $context {
		 *     @type string $scope         Connection scope: 'network' or 'site'.
		 *     @type string $account_email Connected account email.
		 *     @type array  $scopes        Requested scopes from the pending record.
		 *     @type array  $data          Verified token response minus connection plumbing; domain
		 *                                 sections for consumers to interpret.
		 * }
		 */
		do_action(
			'gk/foundation/connection/established',
			[
				'scope'         => $scope,
				'account_email' => $connection['account_email'],
				'scopes'        => is_array( $pending['scopes'] ?? null ) ? array_values( $pending['scopes'] ) : [],
				'data'          => $sections,
			]
		);

		return [
			'connection' => $connection,
			'data'       => $sections,
		];
	}

	/**
	 * Best-effort signed revoke on the server, then wipes local connection state. Consumers react
	 * to the disconnected event (e.g. dropping domain data tied to the connection).
	 *
	 * @since 1.26.0
	 *
	 * @return array{disconnected:bool, revoked_remotely:bool, remote_confirmed:bool, remote_failures:int}
	 */
	public function disconnect(): array {
		$connection = $this->store->get();
		$remote     = [
			'revoked'   => false,
			'confirmed' => false,
			'failed'    => 0,
		];

		if ( is_array( $connection ) && empty( $connection['decrypt_failed'] ) && ! empty( $connection['connection_id'] ) ) {
			$remote = $this->revoke_remote();
		}

		/**
		 * Fires on an explicit disconnect, before local state is wiped.
		 *
		 * Fires even for a decrypt-failed connection: its domain data is equally dead. Consumers
		 * drop whatever they tied to this scope's connection.
		 *
		 * @since 1.26.0
		 *
		 * @param array $context {
		 *     @type string $scope            Connection scope: 'network' or 'site'.
		 *     @type bool   $revoked_remotely Whether the server verifiably revoked the connection.
		 * }
		 */
		do_action(
			'gk/foundation/connection/disconnected',
			[
				'scope'            => $this->store->scope(),
				'revoked_remotely' => (bool) $remote['revoked'],
			]
		);

		// Drop the health record last: revoke_remote() above polls the signed channel, which records a
		// final (revoked/offline) state we must not leave behind for the next connection. Scope-targeted, so a
		// sibling connection in the other scope keeps its own health.
		if ( is_array( $connection ) ) {
			ConnectionHealth::forget( $connection );
		}

		return [
			'disconnected'     => $this->store->wipe(),
			'revoked_remotely' => (bool) $remote['revoked'],
			'remote_confirmed' => (bool) $remote['confirmed'],
			'remote_failures'  => (int) $remote['failed'],
		];
	}

	/**
	 * Sends a best-effort signed revoke request over the signed channel; failures are swallowed.
	 *
	 * @since 1.26.0
	 *
	 * @return array{revoked:bool, confirmed:bool, failed:int} Whether the server considers the
	 *                                                          connection dead, whether this request
	 *                                                          verifiably performed the deactivation
	 *                                                          sweep, and how many entries failed.
	 */
	private function revoke_remote(): array {
		try {
			// Route through the signed channel so the revoke hits the correct endpoint with the persisted
			// clock offset and a verified response. Best-effort: a remote failure never blocks the local wipe.
			$result = $this->signed_request( 'POST', '/connection/revoke' );
		} catch ( Throwable $e ) {
			// Revoke is best-effort; the local wipe is what matters.
			return [
				'revoked'   => false,
				'confirmed' => false,
				'failed'    => 0,
			];
		}

		if ( is_wp_error( $result ) ) {
			// A lost race (already_revoked) or a verifier 410 (connection_revoked) both mean the
			// server already considers the connection dead — but THIS request performed no sweep
			// (a supersede winner deactivates nothing), so the deactivation is not confirmed.
			return [
				'revoked'   => in_array( $result->get_error_code(), [ 'already_revoked', 'connection_revoked' ], true ),
				'confirmed' => false,
				'failed'    => 0,
			];
		}

		$failed = 0;

		foreach ( (array) ( $result['results'] ?? [] ) as $entry ) {
			if ( empty( $entry['success'] ) ) {
				++$failed;
			}
		}

		return [
			'revoked'   => true,
			'confirmed' => true,
			'failed'    => $failed,
		];
	}

	/**
	 * Performs a JSON POST to a gk-connect endpoint and returns the decoded
	 * body alongside the raw body and headers (needed for signature checks).
	 *
	 * @since 1.26.0
	 *
	 * @param string $path    REST path (e.g. '/token').
	 * @param array  $payload Request payload.
	 *
	 * @return array{data:array, body:string, headers:array}|WP_Error
	 */
	private function request( string $path, array $payload ) {
		$url = $this->server_base() . self::REST_PREFIX . $path;

		$response = ( $this->http )(
			$url,
			[
				'body'    => wp_json_encode( $payload ) ?: '',
				'headers' => [
					'Content-Type' => 'application/json',
					'Accept'       => 'application/json',
				],
			]
		);

		if ( $response instanceof WP_Error ) {
			return $response;
		}

		$raw_body = (string) ( $response['body'] ?? '' );
		$data     = json_decode( $raw_body, true );

		if ( ! is_array( $data ) ) {
			return new WP_Error(
				'gk_connection_bad_response',
				'The server response could not be parsed.',
				[ 'status' => (int) ( $response['code'] ?? 0 ) ]
			);
		}

		return [
			'data'    => $data,
			'body'    => $raw_body,
			'headers' => is_array( $response['headers'] ?? null ) ? $response['headers'] : [],
		];
	}

	/**
	 * Performs a signed request to a gk-connect endpoint, verifies the request-bound response
	 * signature, and returns the decoded body. Never returns a parsed body that failed
	 * verification. On a verified `timestamp_skew` response it adopts+persists the server clock
	 * offset and retries once, so a skewed site clock cannot brick the connection.
	 *
	 * @since 1.26.0
	 *
	 * @param string     $method HTTP method.
	 * @param string     $path   REST path (e.g. '/token').
	 * @param array|null $body   Optional request body to JSON-encode.
	 *
	 * @return array|WP_Error
	 */
	public function signed_request( string $method, string $path, ?array $body = null ) {
		$connection = $this->store->get();

		if ( ! is_array( $connection ) || ! empty( $connection['decrypt_failed'] ) || empty( $connection['connection_id'] ) ) {
			return new WP_Error( 'gk_connection_missing', 'No usable account connection is available.' );
		}

		$result = $this->dispatch_signed( $connection, $method, $path, $body );

		// A request that never reached the wire proves nothing about the connection: recording it
		// would classify OK and could clear a genuine trust-broken state.
		if ( $result instanceof WP_Error && 'gk_connection_encode_failed' === $result->get_error_code() ) {
			return $result;
		}

		// Every completed signed poll updates connection health (the badge + page notice read from it).
		ConnectionHealth::record( $connection, ConnectionHealth::classify( $result ) );

		return $result;
	}

	/**
	 * Performs the signed request against a resolved connection: signs, sends, verifies, and handles one
	 * clock-skew retry. Split from signed_request() so the latter is the single health-recording choke point.
	 *
	 * @since 1.26.0
	 *
	 * @param array      $connection Stored connection (connection_id, private_key, server_pubkey, scope).
	 * @param string     $method     HTTP method.
	 * @param string     $path       REST path (relative to the signed prefix).
	 * @param array|null $body       Request body, or null for none.
	 *
	 * @return array|WP_Error
	 */
	private function dispatch_signed( array $connection, string $method, string $path, ?array $body = null ) {
		$url      = $this->server_base() . self::REST_PREFIX . $path;
		$raw_body = '';

		if ( null !== $body ) {
			$encoded = wp_json_encode( $body );

			// A body that cannot be JSON-encoded (e.g. non-UTF8 bytes) must fail loudly here: the
			// old `?: ''` fallback would sign and send an EMPTY body, silently mangling the request.
			if ( false === $encoded ) {
				return new WP_Error( 'gk_connection_encode_failed', 'The request body could not be encoded.' );
			}

			$raw_body = $encoded;
		}

		$req_path = (string) wp_parse_url( $url, PHP_URL_PATH );

		$attempt = $this->attempt_signed( $connection, $method, $url, $req_path, $raw_body, $this->clock_offset() );

		if ( $attempt instanceof WP_Error ) {
			return $attempt;
		}

		// Not a skew notice: the first attempt is fully verified, so finish it.
		if ( empty( $attempt['skew'] ) ) {
			return $this->finish_signed( $attempt );
		}

		// Clock-skew recovery: the skew response is authenticated (Fix 8's bound canonical), so its
		// server_time is trusted. Adopt+persist the offset and retry ONCE with a fresh nonce.
		$offset = (int) ( $attempt['server_time'] ?? 0 ) - time();

		if ( abs( $offset ) > self::MAX_CLOCK_OFFSET_SECONDS ) {
			return new WP_Error( 'gk_connection_clock_skew', 'The site clock is too far from the server to reconcile.' );
		}

		$this->persist_clock_offset( $offset );

		$retry = $this->attempt_signed( $connection, $method, $url, $req_path, $raw_body, $offset );

		if ( $retry instanceof WP_Error ) {
			return $retry;
		}

		// One retry only: a second skew after reconciling is a hard failure, never a loop.
		if ( ! empty( $retry['skew'] ) ) {
			return new WP_Error( 'gk_connection_clock_skew', 'The server rejected the request after clock reconciliation.' );
		}

		return $this->finish_signed( $retry );
	}

	/**
	 * Signs and sends one signed-channel request, verifying the request-bound response signature.
	 *
	 * Preserves strict ordering: a response is only decoded once authenticated. Returns a
	 * discriminated result — a fully verified response `[ 'skew' => false, 'data', 'code' ]`, an
	 * authenticated `timestamp_skew` notice `[ 'skew' => true, 'server_time', 'code' ]`, or a
	 * WP_Error for transport, signature, freshness, or parse failures.
	 *
	 * @since 1.26.0
	 *
	 * @param array  $connection Stored connection (connection_id, private_key, server_pubkey).
	 * @param string $method     HTTP method.
	 * @param string $url        Absolute request URL.
	 * @param string $req_path   REST path bound into the response canonical.
	 * @param string $raw_body   Raw request body.
	 * @param int    $offset     Server clock offset applied to the signed timestamp and freshness check.
	 *
	 * @return array{skew:bool,data?:array,code:int,server_time?:int}|WP_Error
	 */
	private function attempt_signed( array $connection, string $method, string $url, string $req_path, string $raw_body, int $offset ) {
		$signer = new SignedRequest(
			(string) $connection['connection_id'],
			(string) ( $connection['private_key'] ?? '' ),
			(string) ( $connection['server_pubkey'] ?? '' ),
			$offset
		);

		$signed  = $signer->sign( $method, $url, $raw_body );
		$headers = $signed['headers'];
		$nonce   = (string) $signed['nonce'];

		$headers['Accept'] = 'application/json';

		if ( '' !== $raw_body ) {
			$headers['Content-Type'] = 'application/json';
		}

		$response = ( $this->http )(
			$url,
			[
				'method'  => $method,
				'body'    => $raw_body,
				'headers' => $headers,
			]
		);

		if ( $response instanceof WP_Error ) {
			return new WP_Error( 'gk_connection_offline', $response->get_error_message() );
		}

		$raw_response = (string) ( $response['body'] ?? '' );
		$resp_headers = is_array( $response['headers'] ?? null ) ? $response['headers'] : [];
		$code         = (int) ( $response['code'] ?? 0 );

		$context = [
			'method' => $method,
			'path'   => $req_path,
			'nonce'  => $nonce,
			'status' => $code,
		];

		// A fully verified response (signature + request binding + freshness) is safe to decode.
		if ( $signer->verify_response( $raw_response, $resp_headers, $context ) ) {
			$data = json_decode( $raw_response, true );

			if ( ! is_array( $data ) ) {
				return new WP_Error( 'gk_connection_bad_response', 'The server response could not be parsed.' );
			}

			return [
				'skew' => false,
				'data' => $data,
				'code' => $code,
			];
		}

		// Not fully verified. Authenticate the bytes (signature + binding, no freshness) so a genuine
		// skew notice can still be trusted enough to read the server clock. Anything not authenticated
		// is a hard signature failure — never act on it.
		if ( ! $signer->verify_response_signature( $raw_response, $resp_headers, $context ) ) {
			return new WP_Error( 'gk_connection_bad_signature', 'The server response signature was invalid.' );
		}

		$data = json_decode( $raw_response, true );

		if ( is_array( $data ) && 403 === $code && 'timestamp_skew' === ( $data['code'] ?? '' ) && isset( $data['server_time'] ) && is_numeric( $data['server_time'] ) ) {
			return [
				'skew'        => true,
				'server_time' => (int) $data['server_time'],
				'code'        => $code,
			];
		}

		// Authenticated but stale (freshness) or otherwise unusable: reject rather than act on it.
		return new WP_Error( 'gk_connection_bad_signature', 'The server response signature was invalid.' );
	}

	/**
	 * Maps a verified signed-channel attempt to its decoded body, or a WP_Error for a non-2xx status.
	 *
	 * @since 1.26.0
	 *
	 * @param array{skew:bool,data?:array,code:int} $attempt Verified attempt result.
	 *
	 * @return array|WP_Error
	 */
	private function finish_signed( array $attempt ) {
		$data = is_array( $attempt['data'] ?? null ) ? $attempt['data'] : [];
		$code = (int) $attempt['code'];

		if ( $code < 200 || $code >= 300 ) {
			$error_code    = (string) ( $data['code'] ?? 'gk_connection_error' );
			$error_message = (string) ( $data['message'] ?? 'The server rejected the request.' );

			// Carry the HTTP status (health fails closed on a verified 401 even without a known code)
			// AND the verified body: conflict responses carry the current entry state the caller
			// needs to merge, and dropping it here would strand every such contract.
			return new WP_Error(
				$error_code,
				$error_message,
				[
					'status' => $code,
					'body'   => $data,
				]
			);
		}

		return $data;
	}

	/**
	 * Returns the persisted server clock offset (seconds), memoized for the request.
	 *
	 * @since 1.26.0
	 *
	 * @return int
	 */
	private function clock_offset(): int {
		if ( null !== self::$clock_offset_memo ) {
			return self::$clock_offset_memo;
		}

		// The skew is between the gk-connect server and this install's clock, identical for every blog (one PHP
		// process), so it is learned once network-wide rather than re-learned per subsite. Single-site collapses.
		self::$clock_offset_memo = (int) get_site_option( self::CLOCK_OFFSET_OPTION, 0 );

		return self::$clock_offset_memo;
	}

	/**
	 * Persists the server clock offset and refreshes the request-scoped memo.
	 *
	 * @since 1.26.0
	 *
	 * @param int $offset Offset in seconds to add to the local clock.
	 *
	 * @return void
	 */
	private function persist_clock_offset( int $offset ): void {
		self::$clock_offset_memo = $offset;

		update_site_option( self::CLOCK_OFFSET_OPTION, $offset );
	}

	/**
	 * Default HTTP transport wrapping wp_remote_post.
	 *
	 * @since 1.26.0
	 *
	 * @param string $url  Request URL.
	 * @param array  $args Request args (body, headers).
	 *
	 * @return array{body:string, headers:array, code:int}|WP_Error
	 */
	private function default_http( string $url, array $args ) {
		$response = wp_remote_post(
			$url,
			array_merge(
				[
					'sslverify'   => true,
					'redirection' => 0,
					'timeout'     => 15,
				],
				$args
			)
		);

		if ( $response instanceof WP_Error ) {
			return $response;
		}

		return [
			'code'    => (int) wp_remote_retrieve_response_code( $response ),
			'body'    => (string) wp_remote_retrieve_body( $response ),
			'headers' => $this->normalize_headers( wp_remote_retrieve_headers( $response ) ),
		];
	}

	/**
	 * Normalizes response headers (which may be a Requests dictionary) to a
	 * plain array so signature verification can iterate them.
	 *
	 * @since 1.26.0
	 *
	 * @param mixed $headers Raw headers from wp_remote_retrieve_headers().
	 *
	 * @return array<string,mixed>
	 */
	private function normalize_headers( $headers ): array {
		if ( is_array( $headers ) ) {
			return $headers;
		}

		if ( ! is_iterable( $headers ) ) {
			return [];
		}

		$normalized = [];

		foreach ( $headers as $key => $value ) {
			$normalized[ $key ] = $value;
		}

		return $normalized;
	}

	/**
	 * Returns the configured gk-connect server base URL: the GK_CONNECT_URL override, or the default.
	 *
	 * @since 1.26.0
	 *
	 * @return string
	 */
	public static function server_base_url(): string {
		return defined( 'GK_CONNECT_URL' ) ? (string) GK_CONNECT_URL : self::DEFAULT_SERVER;
	}

	/**
	 * Returns the account dashboard URL on the configured server, so "Manage on GravityKit.com" links
	 * follow the GK_CONNECT_URL override instead of hardcoding production.
	 *
	 * @since 1.26.0
	 *
	 * @return string
	 */
	public static function account_url(): string {
		return untrailingslashit( self::server_base_url() ) . '/account';
	}

	/**
	 * Returns the gk-connect server base URL.
	 *
	 * @since 1.26.0
	 *
	 * @return string
	 */
	private function server_base(): string {
		return self::server_base_url();
	}

	/**
	 * Returns the active GravityKit product identifiers for the /request call.
	 *
	 * @since 1.26.0
	 *
	 * @return array<string>
	 */
	private function site_products(): array {
		/**
		 * Filters the active GravityKit product identifiers sent when beginning a connection.
		 *
		 * @since 1.26.0
		 *
		 * @param array<string> $products Product text-domains/slugs. Default: [].
		 */
		$products = apply_filters( 'gk/foundation/connection/site-products', [] );

		return array_values( array_map( 'strval', (array) $products ) );
	}

	/**
	 * Derives the site's multisite install context for the /request payload. Cosmetic, site-declared claim.
	 *
	 * @since 1.26.0
	 *
	 * @param string $scope Resolved connection scope.
	 *
	 * @return string One of 'single', 'network', or 'site'.
	 */
	private function install_context( string $scope ): string {
		if ( ! is_multisite() ) {
			return 'single';
		}

		if ( TokenStore::SCOPE_NETWORK === $scope ) {
			return 'network';
		}

		return 'site';
	}
}
