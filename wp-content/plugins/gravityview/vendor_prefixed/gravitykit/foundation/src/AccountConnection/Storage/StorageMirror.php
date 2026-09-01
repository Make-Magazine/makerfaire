<?php

namespace GravityKit\GravityView\Foundation\AccountConnection\Storage;

/**
 * Two-slot local mirror of account-storage entries.
 *
 * Serves reads without a network round trip and preserves the last-synced entries when the
 * server is unreachable. The mirror is fingerprint-bound to the producing connection and is
 * re-validated on every access (including memo hits): a fingerprint mismatch — a different
 * connection now occupies the slot — purges the mirror so one account's data can never be
 * served under another's connection.
 *
 * @since 1.26.0
 */
final class StorageMirror {
	/**
	 * Non-autoloaded option holding the mirrored entries (per scope slot).
	 *
	 * @since 1.26.0
	 *
	 * @var string
	 */
	const OPTION = 'gk_connection_storage_mirror';

	/**
	 * Maximum mirrored entries per namespace; the oldest-synced entry is evicted first.
	 *
	 * @since 1.26.0
	 *
	 * @var int
	 */
	const MAX_ENTRIES_PER_NAMESPACE = 100;

	/**
	 * Values whose encoded size exceeds this are not mirrored (re-fetched instead), bounding the
	 * option's total footprint: large blobs must not accrete into a multi-megabyte row.
	 *
	 * @since 1.26.0
	 *
	 * @var int
	 */
	const MAX_MIRRORED_VALUE_BYTES = 8192;

	/**
	 * Maximum mirrored entries across ALL namespaces; globally oldest-synced evicted first, so
	 * the option's footprint stays bounded no matter how many products mirror through it.
	 *
	 * @since 1.26.0
	 *
	 * @var int
	 */
	const MAX_TOTAL_ENTRIES = 300;

	/**
	 * Storage scope slot: 'network' (sitemeta) or 'site' (per-blog option).
	 *
	 * @since 1.26.0
	 *
	 * @var string
	 */
	private $scope;

	/**
	 * Request-scoped memo of decoded mirror payloads, keyed by scope slot so the two slots of a
	 * multisite can never bleed into each other.
	 *
	 * @since 1.26.0
	 *
	 * @var array<string, array|null>
	 */
	private static $memo = [];

	/**
	 * Constructor.
	 *
	 * @since 1.26.0
	 *
	 * @param string $scope Scope slot. Accepts 'network' or 'site'; anything else collapses to 'site'.
	 */
	public function __construct( string $scope ) {
		$this->scope = 'network' === $scope ? 'network' : 'site';
	}

	/**
	 * Memo key for this slot. The site slot is per-blog: switch_to_blog() retargets get_option,
	 * so a scope-only key would bleed one blog's payload into another within a request.
	 *
	 * @since 1.26.0
	 *
	 * @return string
	 */
	private function memo_key(): string {
		// Single-site collapses both option slots onto one row; two memo keys would serve stale
		// values across mixed scope calls in one request.
		if ( function_exists( 'is_multisite' ) && ! is_multisite() ) {
			return 'single';
		}

		if ( 'network' === $this->scope ) {
			return 'network';
		}

		return 'site:' . ( function_exists( 'get_current_blog_id' ) ? (int) get_current_blog_id() : 0 );
	}

	/**
	 * Returns a mirrored entry, or null when absent or when the fingerprint does not match.
	 *
	 * @since 1.26.0
	 *
	 * @param string $fingerprint Producing connection fingerprint.
	 * @param string $namespace   Namespace.
	 * @param string $key         Storage key.
	 *
	 * @return array|null Entry array {value, version, schema_version, updated_at, synced_at} or null.
	 */
	public function get( string $fingerprint, string $namespace, string $key ): ?array {
		$payload = $this->payload( $fingerprint );

		if ( null === $payload ) {
			return null;
		}

		$entry = $payload['entries'][ $namespace ][ $key ] ?? null;

		return is_array( $entry ) ? $entry : null;
	}

	/**
	 * Stores a mirrored entry, evicting the oldest-synced entry when the namespace is full.
	 *
	 * @since 1.26.0
	 *
	 * @param string $fingerprint Producing connection fingerprint.
	 * @param string $namespace   Namespace.
	 * @param string $key         Storage key.
	 * @param array  $entry       Entry array {value, version, schema_version, updated_at, synced_at}.
	 *
	 * @return void
	 */
	public function put( string $fingerprint, string $namespace, string $key, array $entry ): void {
		$encoded_value = wp_json_encode( $entry['value'] ?? null );

		if ( false === $encoded_value || strlen( $encoded_value ) > self::MAX_MIRRORED_VALUE_BYTES ) {
			// An unmirrored entry may still have a stale predecessor under this key; drop it so a
			// later offline read serves nothing rather than an older value.
			$this->forget( $namespace, $key );

			return;
		}

		$payload = $this->payload( $fingerprint );

		if ( null === $payload ) {
			$payload = [
				'fingerprint' => $fingerprint,
				'entries'     => [],
			];
		}

		$payload['entries'][ $namespace ][ $key ] = $entry;

		if ( count( $payload['entries'][ $namespace ] ) > self::MAX_ENTRIES_PER_NAMESPACE ) {
			uasort(
				$payload['entries'][ $namespace ],
				static function ( $a, $b ) {
					return (int) ( $a['synced_at'] ?? 0 ) <=> (int) ( $b['synced_at'] ?? 0 );
				}
			);

			$oldest_key = array_key_first( $payload['entries'][ $namespace ] );
			unset( $payload['entries'][ $namespace ][ $oldest_key ] );
		}

		$this->evict_over_total( $payload );

		$this->write( $payload );
	}

	/**
	 * Evicts globally oldest-synced entries until the total is within the ceiling.
	 *
	 * @since 1.26.0
	 *
	 * @param array $payload Mirror payload, modified in place.
	 *
	 * @return void
	 */
	private function evict_over_total( array &$payload ): void {
		$total = 0;

		foreach ( $payload['entries'] as $entries ) {
			$total += count( $entries );
		}

		while ( $total > self::MAX_TOTAL_ENTRIES ) {
			$oldest_ns  = null;
			$oldest_key = null;
			$oldest_at  = PHP_INT_MAX;

			foreach ( $payload['entries'] as $ns => $entries ) {
				foreach ( $entries as $entry_key => $entry ) {
					$at = (int) ( $entry['synced_at'] ?? 0 );

					if ( $at < $oldest_at ) {
						$oldest_at  = $at;
						$oldest_ns  = $ns;
						$oldest_key = $entry_key;
					}
				}
			}

			if ( null === $oldest_ns ) {
				break;
			}

			unset( $payload['entries'][ $oldest_ns ][ $oldest_key ] );

			if ( empty( $payload['entries'][ $oldest_ns ] ) ) {
				unset( $payload['entries'][ $oldest_ns ] );
			}

			--$total;
		}
	}

	/**
	 * Removes one mirrored entry.
	 *
	 * @since 1.26.0
	 *
	 * @param string $namespace Namespace.
	 * @param string $key       Storage key.
	 *
	 * @return void
	 */
	public function forget( string $namespace, string $key ): void {
		$payload = $this->raw_payload();

		if ( null === $payload || ! isset( $payload['entries'][ $namespace ][ $key ] ) ) {
			return;
		}

		unset( $payload['entries'][ $namespace ][ $key ] );

		$this->write( $payload );
	}

	/**
	 * Drops the request-scoped memo for every slot (tests and long-running processes).
	 *
	 * @since 1.26.0
	 *
	 * @return void
	 */
	public static function flush_memo(): void {
		self::$memo = [];
	}

	/**
	 * Deletes this slot's entire mirror.
	 *
	 * @since 1.26.0
	 *
	 * @return void
	 */
	public function purge(): void {
		unset( self::$memo[ $this->memo_key() ] );

		if ( 'network' === $this->scope ) {
			delete_site_option( self::OPTION );

			return;
		}

		delete_option( self::OPTION );
	}

	/**
	 * Returns the decoded mirror payload after fingerprint validation; a mismatch purges.
	 *
	 * @since 1.26.0
	 *
	 * @param string $fingerprint Producing connection fingerprint.
	 *
	 * @return array|null
	 */
	private function payload( string $fingerprint ): ?array {
		$payload = $this->raw_payload();

		if ( null === $payload ) {
			return null;
		}

		if ( ( $payload['fingerprint'] ?? '' ) !== $fingerprint ) {
			$this->purge();

			return null;
		}

		return $payload;
	}

	/**
	 * Returns the decoded mirror payload without fingerprint validation, memoized per request.
	 *
	 * @since 1.26.0
	 *
	 * @return array|null
	 */
	private function raw_payload(): ?array {
		if ( array_key_exists( $this->memo_key(), self::$memo ) ) {
			$memoized = self::$memo[ $this->memo_key() ];

			return is_array( $memoized ) ? $memoized : null;
		}

		$stored = 'network' === $this->scope
			? get_site_option( self::OPTION, null )
			: get_option( self::OPTION, null );

		$payload = is_array( $stored ) ? $stored : null;

		self::$memo[ $this->memo_key() ] = $payload;

		return $payload;
	}

	/**
	 * Persists the mirror payload and refreshes the memo.
	 *
	 * @since 1.26.0
	 *
	 * @param array $payload Mirror payload.
	 *
	 * @return void
	 */
	private function write( array $payload ): void {
		self::$memo[ $this->memo_key() ] = $payload;

		if ( 'network' === $this->scope ) {
			update_site_option( self::OPTION, $payload );

			return;
		}

		update_option( self::OPTION, $payload, false );
	}
}
