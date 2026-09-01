<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\AccountConnection;

use InvalidArgumentException;
use SodiumException;

/**
 * Signs management-channel requests to the gk-connect server and verifies the
 * server's signed responses using Ed25519 detached signatures.
 *
 * The canonical string built here MUST byte-match the server-side verifier
 * (see gk-connect Task 7). Any change to field order, separators, casing or the
 * body-hash algorithm is a breaking protocol change that must be mirrored on
 * both sides.
 *
 * @since 1.26.0
 */
final class SignedRequest {
	/**
	 * The connection identifier issued by the server.
	 *
	 * @since 1.26.0
	 *
	 * @var string
	 */
	private $connection_id;

	/**
	 * The site's Ed25519 secret key (hex-encoded 64-byte key).
	 *
	 * @since 1.26.0
	 *
	 * @var string
	 */
	private $private_key_hex;

	/**
	 * The pinned server Ed25519 public key (raw 32 bytes), or empty when unknown.
	 *
	 * @since 1.26.0
	 *
	 * @var string
	 */
	private $server_pubkey_bin;

	/**
	 * Seconds to add to the local clock to align with server time.
	 *
	 * @since 1.26.0
	 *
	 * @var int
	 */
	private $time_offset = 0;

	/**
	 * Maximum absolute skew, in seconds, allowed between the offset-adjusted local clock and a
	 * signed response's server timestamp. Mirrors the server's request-side acceptance window.
	 *
	 * @since 1.26.0
	 *
	 * @var int
	 */
	private const MAX_SKEW_SECONDS = 300;

	/**
	 * @since 1.26.0
	 *
	 * @param string $connection_id     Connection identifier.
	 * @param string $private_key_hex   Site Ed25519 secret key, hex-encoded.
	 * @param string $server_pubkey_hex Pinned server Ed25519 public key, hex-encoded.
	 * @param int    $time_offset       Seconds to add to the local clock to align with server time. Default: 0.
	 */
	public function __construct( string $connection_id, string $private_key_hex, string $server_pubkey_hex, int $time_offset = 0 ) {
		$this->connection_id   = $connection_id;
		$this->private_key_hex = $private_key_hex;
		$this->time_offset     = $time_offset;

		try {
			$this->server_pubkey_bin = '' === $server_pubkey_hex ? '' : sodium_hex2bin( $server_pubkey_hex );
		} catch ( SodiumException $e ) {
			$this->server_pubkey_bin = '';
		}
	}

	/**
	 * Builds the canonical string the server signs and verifies.
	 *
	 * Seven fields joined by a single 0x0A, no trailing newline, in this order:
	 * connection_id, uppercase method, host, path, timestamp, nonce,
	 * sha256hex(raw_body).
	 *
	 * @since 1.26.0
	 *
	 * @param string $connection_id Connection identifier.
	 * @param string $method        HTTP method (any case; uppercased here).
	 * @param string $host          Request host, including :port iff non-default.
	 * @param string $path          Request path.
	 * @param int    $timestamp     Unix timestamp.
	 * @param string $nonce         Base64url nonce.
	 * @param string $raw_body      Raw request body (hashed with sha256).
	 *
	 * @return string
	 */
	public static function canonical_string( string $connection_id, string $method, string $host, string $path, int $timestamp, string $nonce, string $raw_body ): string {
		return implode(
			"\n",
			[
				$connection_id,
				strtoupper( $method ),
				$host,
				$path,
				(string) $timestamp,
				$nonce,
				hash( 'sha256', $raw_body ),
			]
		);
	}

	/**
	 * Signs a canonical string with the given secret key and returns the
	 * detached signature as unpadded base64url.
	 *
	 * @since 1.26.0
	 *
	 * @param string $canonical       Canonical string to sign.
	 * @param string $private_key_hex Site Ed25519 secret key, hex-encoded.
	 *
	 * @throws InvalidArgumentException When the secret key is not a valid Ed25519 key.
	 *
	 * @return string
	 */
	public static function sign_canonical( string $canonical, string $private_key_hex ): string {
		try {
			$secret_key = sodium_hex2bin( $private_key_hex );
		} catch ( SodiumException $e ) {
			throw new InvalidArgumentException( 'The private key is not a valid Ed25519 secret key.', 0, $e );
		}

		if ( SODIUM_CRYPTO_SIGN_SECRETKEYBYTES !== strlen( $secret_key ) ) {
			throw new InvalidArgumentException( 'The private key is not a valid Ed25519 secret key.' );
		}

		$signature = sodium_crypto_sign_detached( $canonical, $secret_key );

		return sodium_bin2base64( $signature, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING );
	}

	/**
	 * Signs a request and returns the management-channel headers.
	 *
	 * Host/path derivation: host is the URL host, plus `:port` only when the
	 * port is present and non-default for the scheme (443/https, 80/http). This
	 * matches the Host header the server reconstructs. Path is the URL path.
	 *
	 * @since 1.26.0
	 *
	 * @param string $method   HTTP method.
	 * @param string $url      Absolute request URL.
	 * @param string $raw_body Raw request body. Default: ''.
	 *
	 * @return array{headers: array<string,string>, nonce: string}
	 */
	public function sign( string $method, string $url, string $raw_body = '' ): array {
		$host      = $this->host_from_url( $url );
		$path      = (string) wp_parse_url( $url, PHP_URL_PATH );
		$timestamp = time() + $this->time_offset;
		$nonce     = sodium_bin2base64( random_bytes( 32 ), SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING );

		$canonical = self::canonical_string( $this->connection_id, $method, $host, $path, $timestamp, $nonce, $raw_body );

		return [
			'headers' => [
				'X-GK-Connection-Id' => $this->connection_id,
				'X-GK-Timestamp'     => (string) $timestamp,
				'X-GK-Nonce'         => $nonce,
				'X-GK-Signature'     => self::sign_canonical( $canonical, $this->private_key_hex ),
			],
			'nonce'   => $nonce,
		];
	}

	/**
	 * Verifies the server's signed response.
	 *
	 * Handshake responses (no request context) are verified over the legacy `timestamp\nraw_body`
	 * canonical. Signed-channel responses ($context provided) are verified over the request-bound
	 * canonical AND must fall inside the freshness window, so a captured signed body cannot be
	 * replayed onto another route, connection, or status, nor served stale.
	 *
	 * @since 1.26.0
	 *
	 * @param string                                                        $raw_body Raw response body.
	 * @param array<string,mixed>                                           $headers  Response headers (case-insensitive lookup).
	 * @param array{method:string,path:string,nonce:string,status:int}|null $context Originating request context, or null for the legacy handshake.
	 *
	 * @return bool
	 */
	public function verify_response( string $raw_body, array $headers, ?array $context = null ): bool {
		if ( null === $context ) {
			return $this->verify_signature( $this->legacy_canonical( $raw_body, $headers ), $headers );
		}

		return $this->verify_response_signature( $raw_body, $headers, $context ) && $this->is_response_fresh( $headers );
	}

	/**
	 * Verifies a signed-channel response's signature over the request-bound canonical, WITHOUT the
	 * freshness gate. Lets the caller authenticate a `timestamp_skew` response (intentionally stale
	 * on a skewed clock) enough to trust its server clock before adopting an offset.
	 *
	 * @since 1.26.0
	 *
	 * @param string                                                   $raw_body Raw response body.
	 * @param array<string,mixed>                                      $headers  Response headers.
	 * @param array{method:string,path:string,nonce:string,status:int} $context Originating request context.
	 *
	 * @return bool
	 */
	public function verify_response_signature( string $raw_body, array $headers, array $context ): bool {
		return $this->verify_signature( $this->bound_canonical( $raw_body, $headers, $context ), $headers );
	}

	/**
	 * Builds the legacy handshake response canonical (`server_timestamp\nraw_body`).
	 *
	 * @since 1.26.0
	 *
	 * @param string              $raw_body Raw response body.
	 * @param array<string,mixed> $headers  Response headers.
	 *
	 * @return string
	 */
	private function legacy_canonical( string $raw_body, array $headers ): string {
		return $this->header( $headers, 'X-GK-Server-Timestamp' ) . "\n" . $raw_body;
	}

	/**
	 * Builds the request-bound response canonical the server signs for signed-channel requests:
	 * connection_id, request nonce, uppercase method, path, HTTP status, server timestamp,
	 * sha256hex(raw_body), joined by single 0x0A with no trailing newline.
	 *
	 * @since 1.26.0
	 *
	 * @param string                                                   $raw_body Raw response body.
	 * @param array<string,mixed>                                      $headers  Response headers.
	 * @param array{method:string,path:string,nonce:string,status:int} $context Originating request context.
	 *
	 * @return string
	 */
	private function bound_canonical( string $raw_body, array $headers, array $context ): string {
		return implode(
			"\n",
			[
				$this->connection_id,
				(string) $context['nonce'],
				strtoupper( (string) $context['method'] ),
				(string) $context['path'],
				(string) (int) $context['status'],
				$this->header( $headers, 'X-GK-Server-Timestamp' ),
				hash( 'sha256', $raw_body ),
			]
		);
	}

	/**
	 * Verifies the server's detached signature over a canonical string.
	 *
	 * @since 1.26.0
	 *
	 * @param string              $canonical Canonical bytes the signature must cover.
	 * @param array<string,mixed> $headers   Response headers carrying the signature and timestamp.
	 *
	 * @return bool
	 */
	private function verify_signature( string $canonical, array $headers ): bool {
		if ( SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES !== strlen( $this->server_pubkey_bin ) ) {
			return false;
		}

		$timestamp = $this->header( $headers, 'X-GK-Server-Timestamp' );
		$signature = $this->header( $headers, 'X-GK-Server-Signature' );

		if ( '' === $timestamp || '' === $signature ) {
			return false;
		}

		try {
			$signature_bin = sodium_base642bin( $signature, SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING );
		} catch ( SodiumException $e ) {
			return false;
		}

		if ( SODIUM_CRYPTO_SIGN_BYTES !== strlen( $signature_bin ) ) {
			return false;
		}

		return sodium_crypto_sign_verify_detached( $signature_bin, $canonical, $this->server_pubkey_bin );
	}

	/**
	 * Whether a signed response's server timestamp is within the freshness window of the
	 * offset-adjusted local clock.
	 *
	 * @since 1.26.0
	 *
	 * @param array<string,mixed> $headers Response headers.
	 *
	 * @return bool
	 */
	private function is_response_fresh( array $headers ): bool {
		$timestamp = $this->header( $headers, 'X-GK-Server-Timestamp' );

		if ( '' === $timestamp ) {
			return false;
		}

		return abs( ( time() + $this->time_offset ) - (int) $timestamp ) <= self::MAX_SKEW_SECONDS;
	}

	/**
	 * Records the offset between local and server time so signed timestamps
	 * stay inside the server's acceptance window. The caller persists it.
	 *
	 * @since 1.26.0
	 *
	 * @param int $server_time Server Unix timestamp.
	 *
	 * @return void
	 */
	public function apply_time_offset( int $server_time ): void {
		$this->time_offset = $server_time - time();
	}

	/**
	 * Derives the signing host from a URL, appending `:port` only for a
	 * non-default port.
	 *
	 * @since 1.26.0
	 *
	 * @param string $url Absolute URL.
	 *
	 * @return string
	 */
	private function host_from_url( string $url ): string {
		$host = (string) wp_parse_url( $url, PHP_URL_HOST );
		$port = wp_parse_url( $url, PHP_URL_PORT );

		if ( null === $port ) {
			return $host;
		}

		$scheme     = strtolower( (string) wp_parse_url( $url, PHP_URL_SCHEME ) );
		$is_default = ( 'https' === $scheme && 443 === $port ) || ( 'http' === $scheme && 80 === $port );

		if ( $is_default ) {
			return $host;
		}

		return $host . ':' . $port;
	}

	/**
	 * Case-insensitive header lookup returning a scalar string.
	 *
	 * @since 1.26.0
	 *
	 * @param array<string,mixed> $headers Response headers.
	 * @param string              $name    Header name to find.
	 *
	 * @return string
	 */
	private function header( array $headers, string $name ): string {
		$name = strtolower( $name );

		foreach ( $headers as $key => $value ) {
			if ( strtolower( (string) $key ) !== $name ) {
				continue;
			}

			if ( is_array( $value ) ) {
				return (string) reset( $value );
			}

			return (string) $value;
		}

		return '';
	}
}
