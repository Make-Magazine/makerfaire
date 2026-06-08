<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Licenses\Integrity;

/**
 * Registry of Ed25519 public keys and their purposes.
 *
 * Each key is a handle (ID string) mapped to structured metadata. The ID carries no policy —
 * `purpose` is the authoritative discriminator used for cryptographic domain separation. A key
 * whose purpose is `package` MUST NOT verify a signature produced for any other purpose, even if
 * both use the same algorithm.
 *
 * Purposes:
 *   - `package`: signs plugin ZIP releases delivered to customer sites.
 *   - `root`:    signs out-of-band authorization messages (currently: the revocation manifest).
 *                The root key is never used to sign packages. It lives on a different access list.
 *
 * Key IDs are reserved forever. Once an ID has ever appeared here, it must never be rebound to a
 * different public key — reuse would silently re-trust old signatures.
 *
 * @since 1.15.0
 */
final class KeyRing {
	const PURPOSE_PACKAGE = 'package';
	const PURPOSE_ROOT    = 'root';

	const STATUS_ACTIVE    = 'active';
	const STATUS_PRESTAGED = 'prestaged';
	const STATUS_RETIRED   = 'retired';

	/**
	 * Structured key registry.
	 *
	 * @since 1.15.0
	 *
	 * @var array<string, array{purpose: string, algorithm: string, public_key: string, status: string, created: string}>
	 */
	const KEYS = [
		'gk-sign-v1' => [
			'purpose'    => self::PURPOSE_PACKAGE,
			'algorithm'  => 'ed25519',
			'public_key' => 'x8WwPr2bNEMgDHrkhxB5jdY5ZQDzkar8ff1kY4wmjLM=',
			'status'     => self::STATUS_ACTIVE,
			'created'    => '2026-04-16',
		],
		'gk-sign-v2' => [
			'purpose'    => self::PURPOSE_PACKAGE,
			'algorithm'  => 'ed25519',
			'public_key' => 'aq+1TY6Ptnxim9CnnLZA0r+kQxkCvmW1/3Dz/tx5G2I=',
			'status'     => self::STATUS_PRESTAGED,
			'created'    => '2026-04-16',
		],
		'gk-root'    => [
			'purpose'    => self::PURPOSE_ROOT,
			'algorithm'  => 'ed25519',
			'public_key' => 'yCtbN+zAj/0jtJ8AkCkVwJ8O7yh/GFyrv8DUIrIhM6M=',
			'status'     => self::STATUS_ACTIVE,
			'created'    => '2026-04-16',
		],
	];

	/**
	 * Resolves a key record, enforcing purpose domain separation and retirement.
	 *
	 * A caller that expects a `package` key MUST NOT receive a `root` key and vice versa. Callers
	 * must always pass a `$purpose` argument; passing the wrong purpose returns null even if the
	 * key ID exists under a different purpose.
	 *
	 * Retired keys never resolve. Removing a key from active rotation should immediately invalidate
	 * any signature it produced, without waiting for the next Foundation release to drop the entry
	 * from KEYS. Prestaged keys still resolve so a fresh key can be trusted before it has signed
	 * anything in production.
	 *
	 * @since 1.15.0
	 *
	 * @param string $key_id  Key identifier.
	 * @param string $purpose Expected purpose (one of the PURPOSE_* constants).
	 *
	 * @return array{purpose: string, algorithm: string, public_key: string, status: string, created: string}|null
	 */
	public static function get_key( string $key_id, string $purpose ): ?array {
		if ( ! isset( self::KEYS[ $key_id ] ) ) {
			return null;
		}

		$record = self::KEYS[ $key_id ];

		if ( $record['purpose'] !== $purpose ) {
			return null;
		}

		// PHPStan narrows status to currently-used literals; this guard must keep working the
		// moment a key is flagged retired without waiting for a Foundation release.
		// @phpstan-ignore-next-line — narrowing exempts retired but the runtime check must remain.
		if ( self::STATUS_RETIRED === $record['status'] ) {
			return null;
		}

		return $record;
	}

	/**
	 * Returns the base64-encoded public key for the given ID and purpose.
	 *
	 * @since 1.15.0
	 *
	 * @param string $key_id  Key identifier.
	 * @param string $purpose Expected purpose.
	 *
	 * @return string|null Base64-encoded public key, or null if not found or purpose mismatch.
	 */
	public static function get_public_key( string $key_id, string $purpose ): ?string {
		$record = self::get_key( $key_id, $purpose );

		return $record ? $record['public_key'] : null;
	}

	/**
	 * Returns all key IDs registered for a given purpose.
	 *
	 * @since 1.15.0
	 *
	 * @param string $purpose Purpose filter.
	 *
	 * @return string[]
	 */
	public static function get_key_ids_for_purpose( string $purpose ): array {
		$ids = [];

		foreach ( self::KEYS as $id => $record ) {
			if ( $record['purpose'] === $purpose ) {
				$ids[] = $id;
			}
		}

		return $ids;
	}
}
