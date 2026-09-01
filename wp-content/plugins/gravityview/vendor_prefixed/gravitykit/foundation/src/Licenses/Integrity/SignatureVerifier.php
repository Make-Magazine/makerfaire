<?php

namespace GravityKit\GravityView\Foundation\Licenses\Integrity;

use InvalidArgumentException;
use SodiumException;

use function sodium_crypto_sign_verify_detached;

/**
 * Verifies Ed25519 signatures using the gk.sig envelope.
 *
 * Envelope format:
 *
 *     gk.sig.v1.{purpose}:{algorithm}:{key_id}:{canonical-json-body}
 *
 * Fields:
 *   - `gk.sig.v1` — envelope version. Bumps only when field layout or encoding rules change.
 *   - `{purpose}` — cryptographic domain tag (e.g., `pkg`, `rev`). The verifier refuses to verify
 *                  a signature whose purpose differs from the context it was called for, even if
 *                  the signature would otherwise validate.
 *   - `{algorithm}` — signing suite (e.g., `ed25519`). Allows future suites without an envelope bump.
 *   - `{key_id}` — handle into the KeyRing.
 *   - `{canonical-json-body}` — purpose-specific payload, encoded as canonical JSON (sorted keys,
 *                              no whitespace, UTF-8). Canonical JSON is used instead of delimited
 *                              strings to eliminate injection via field contents that contain the
 *                              delimiter (e.g., a filename containing a colon).
 *
 * @since 1.15.0
 */
final class SignatureVerifier {
	const ENVELOPE_VERSION = 'gk.sig.v1';

	const ENVELOPE_PURPOSE_PACKAGE = 'pkg';
	const ENVELOPE_PURPOSE_ROOT    = 'rev';

	const ALGORITHM_ED25519 = 'ed25519';

	/**
	 * Permitted character set for `key_id` in the outer envelope header.
	 *
	 * The envelope is `gk.sig.v1.{purpose}:{algorithm}:{key_id}:{canonical-json-body}`. The body is
	 * canonical JSON and is therefore injection-safe by construction; the outer header fields are
	 * not, so `key_id` must be restricted to characters that cannot collide with the `:` and `.`
	 * delimiters or otherwise reshape the envelope. Today every KeyRing entry conforms to this
	 * pattern; the regex makes it a verified property at the verification boundary.
	 *
	 * @since 1.17.0
	 */
	const KEY_ID_PATTERN = '/\A[a-z0-9-]{1,64}\z/';

	/**
	 * Maps envelope purpose tags to KeyRing purposes.
	 *
	 * @since 1.15.0
	 *
	 * @var array<string, string>
	 */
	const PURPOSE_TO_KEYRING = [
		self::ENVELOPE_PURPOSE_PACKAGE => KeyRing::PURPOSE_PACKAGE,
		self::ENVELOPE_PURPOSE_ROOT    => KeyRing::PURPOSE_ROOT,
	];

	/**
	 * Verifies a signature over a canonical JSON body.
	 *
	 * @since 1.15.0
	 *
	 * @param string               $signature_hex Hex-encoded 64-byte Ed25519 signature.
	 * @param string               $purpose       Domain tag (one of the PURPOSE_* constants).
	 * @param string               $key_id        Key identifier to look up in the KeyRing.
	 * @param array<string, mixed> $body          Fields to bind to the signature.
	 *
	 * @return bool True iff all envelope fields validate and the signature is cryptographically valid.
	 */
	public static function verify( string $signature_hex, string $purpose, string $key_id, array $body ): bool {
		if ( ! isset( self::PURPOSE_TO_KEYRING[ $purpose ] ) ) {
			return false;
		}

		if ( ! self::is_valid_key_id( $key_id ) ) {
			return false;
		}

		$keyring_purpose = self::PURPOSE_TO_KEYRING[ $purpose ];
		$record          = KeyRing::get_key( $key_id, $keyring_purpose );

		if ( null === $record ) {
			return false;
		}

		if ( self::ALGORITHM_ED25519 !== $record['algorithm'] ) {
			return false;
		}

		if ( 0 !== strlen( $signature_hex ) % 2 || ! ctype_xdigit( $signature_hex ) ) {
			return false;
		}

		$signature = hex2bin( $signature_hex );

		// hex2bin cannot return false here — the ctype_xdigit + even-length pre-check rules out every
		// failure mode — but PHPStan sees it as string|false, so normalise before length-checking.
		if ( false === $signature || SODIUM_CRYPTO_SIGN_BYTES !== strlen( $signature ) ) {
			return false;
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- Decoding a trusted Ed25519 public key from the keyring, not obfuscated payload.
		$public_key = (string) base64_decode( $record['public_key'] );

		if ( SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES !== strlen( $public_key ) ) {
			return false;
		}

		$message = self::build_message( $purpose, $key_id, $body );

		try {
			return sodium_crypto_sign_verify_detached( $signature, $message, $public_key );
		} catch ( SodiumException $e ) {
			return false;
		}
	}

	/**
	 * Verifies a signature against the exact JSON bytes served on the wire.
	 *
	 * Used for revocation manifests where the body is fetched remotely. The body must be valid
	 * canonical JSON and the signature must verify against those exact bytes. This prevents
	 * accepting a modified body that decodes to the same PHP array as the signed canonical form
	 * (for example, reordered keys, whitespace, or duplicate-key tricks).
	 *
	 * @since 1.18.0
	 *
	 * @param string $signature_hex Hex-encoded 64-byte Ed25519 signature.
	 * @param string $purpose       Domain tag (one of the PURPOSE_* constants).
	 * @param string $key_id        Key identifier to look up in the KeyRing.
	 * @param string $body_bytes    Canonical JSON body bytes as received.
	 *
	 * @return bool True iff the body is canonical and the signature validates.
	 */
	public static function verify_with_raw_body( string $signature_hex, string $purpose, string $key_id, string $body_bytes ): bool {
		$decoded = json_decode( $body_bytes, true );

		if ( JSON_ERROR_NONE !== json_last_error() ) {
			return false;
		}

		if ( self::canonicalize( $decoded ) !== $body_bytes ) {
			return false;
		}

		if ( ! isset( self::PURPOSE_TO_KEYRING[ $purpose ] ) ) {
			return false;
		}

		if ( ! self::is_valid_key_id( $key_id ) ) {
			return false;
		}

		$keyring_purpose = self::PURPOSE_TO_KEYRING[ $purpose ];
		$record          = KeyRing::get_key( $key_id, $keyring_purpose );

		if ( null === $record || self::ALGORITHM_ED25519 !== $record['algorithm'] ) {
			return false;
		}

		if ( 0 !== strlen( $signature_hex ) % 2 || ! ctype_xdigit( $signature_hex ) ) {
			return false;
		}

		$signature = hex2bin( $signature_hex );

		if ( false === $signature || SODIUM_CRYPTO_SIGN_BYTES !== strlen( $signature ) ) {
			return false;
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- Decoding a trusted Ed25519 public key from the keyring, not obfuscated payload.
		$public_key = (string) base64_decode( $record['public_key'] );

		if ( SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES !== strlen( $public_key ) ) {
			return false;
		}

		$message = self::ENVELOPE_VERSION . '.' . $purpose . ':' . self::ALGORITHM_ED25519 . ':' . $key_id . ':' . $body_bytes;

		try {
			return sodium_crypto_sign_verify_detached( $signature, $message, $public_key );
		} catch ( SodiumException $e ) {
			return false;
		}
	}

	/**
	 * Builds the envelope message that is signed/verified.
	 *
	 * The body is serialised as canonical JSON (sorted keys, no whitespace) so callers cannot
	 * produce divergent representations of the same logical payload. The outer header fields are
	 * delimiter-sensitive, so `key_id` is validated against KEY_ID_PATTERN before assembly.
	 *
	 * @since 1.15.0
	 *
	 * @param string               $purpose Domain tag.
	 * @param string               $key_id  Key identifier.
	 * @param array<string, mixed> $body    Fields to bind to the signature.
	 *
	 * @throws InvalidArgumentException If `$key_id` does not match KEY_ID_PATTERN.
	 *
	 * @return string The envelope message.
	 */
	public static function build_message( string $purpose, string $key_id, array $body ): string {
		if ( ! self::is_valid_key_id( $key_id ) ) {
			throw new InvalidArgumentException( 'Invalid key_id format' );
		}

		return self::ENVELOPE_VERSION . '.' . $purpose . ':' . self::ALGORITHM_ED25519 . ':' . $key_id . ':' . self::canonicalize( $body );
	}

	/**
	 * Returns true iff `$key_id` matches KEY_ID_PATTERN.
	 *
	 * @since 1.17.0
	 *
	 * @param string $key_id Key identifier candidate.
	 *
	 * @return bool
	 */
	public static function is_valid_key_id( string $key_id ): bool {
		return 1 === preg_match( self::KEY_ID_PATTERN, $key_id );
	}

	/**
	 * Serialises an associative array as canonical JSON (sorted keys, no whitespace).
	 *
	 * Recursive to support nested structures. Array ordering is preserved for list-style arrays;
	 * only string-keyed maps are sorted.
	 *
	 * Determinism note: PHP 7.2's `ksort()` is not stable (8.0+ is). This is safe here because
	 * map keys are unique by definition, so sort order is fully determined by the keys alone.
	 * Do NOT extend this function to sort lists of duplicate-keyed pairs without adding a
	 * tiebreaker — sort stability would otherwise diverge between 7.2 and 8.x and produce
	 * different envelopes across PHP versions.
	 *
	 * @since 1.15.0
	 *
	 * @param mixed $value Value to serialise.
	 *
	 * @return string Canonical JSON encoding.
	 */
	public static function canonicalize( $value ): string {
		if ( is_array( $value ) ) {
			if ( self::is_list( $value ) ) {
				$parts = array_map( [ self::class, 'canonicalize' ], $value );

				return '[' . implode( ',', $parts ) . ']';
			}

			ksort( $value, SORT_STRING );

			$parts = [];

			foreach ( $value as $key => $item ) {
				// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- Canonical signing envelope requires byte-exact JSON; wp_json_encode may mutate strings via its sanity check.
				$parts[] = json_encode( (string) $key, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ':' . self::canonicalize( $item );
			}

			return '{' . implode( ',', $parts ) . '}';
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- Canonical signing envelope requires byte-exact JSON; wp_json_encode may mutate strings via its sanity check.
		return (string) json_encode( $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	}

	/**
	 * Polyfill for array_is_list() on PHP < 8.1.
	 *
	 * @since 1.15.0
	 *
	 * @param array $value Array to test.
	 *
	 * @return bool True if the array is a zero-indexed sequential list.
	 */
	private static function is_list( array $value ): bool {
		if ( function_exists( 'array_is_list' ) ) {
			return array_is_list( $value );
		}

		$i = 0;

		foreach ( $value as $k => $_ ) {
			if ( $k !== $i ) {
				return false;
			}

			++$i;
		}

		return true;
	}

	/**
	 * Checks whether sodium signing functions are available, loading WordPress's
	 * sodium_compat polyfill if the native extension is missing.
	 *
	 * @since 1.15.0
	 *
	 * @return bool True if sodium_crypto_sign_verify_detached() is available.
	 */
	public static function is_available(): bool {
		if ( function_exists( 'sodium_crypto_sign_verify_detached' ) ) {
			return true;
		}

		if ( defined( 'ABSPATH' ) && defined( 'WPINC' ) ) {
			$polyfill = ABSPATH . WPINC . '/sodium_compat/autoload.php';

			if ( file_exists( $polyfill ) ) {
				require_once $polyfill;
			}
		}

		return function_exists( 'sodium_crypto_sign_verify_detached' );
	}
}
