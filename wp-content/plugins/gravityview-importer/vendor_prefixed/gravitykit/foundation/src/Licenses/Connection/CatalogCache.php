<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Licenses\Connection;

use GravityKit\GravityImport\Foundation\AccountConnection\TokenStore;

/**
 * Caches the account license catalog in the connection's network or site scope so the Licenses page renders
 * without a synchronous network pull. The activatable `available` list is stored only for a self_service pull;
 * every other permission caches just the permission + read-only flag (so its guidance renders on first paint).
 * Full license keys are never stored — the signed `available` list carries only masked keys.
 *
 * @since 1.26.0
 */
final class CatalogCache {
	/**
	 * Option holding the cached catalog in either the network or current-blog slot.
	 *
	 * @since 1.26.0
	 */
	public const OPTION = 'gk_foundation/connection/catalog';

	/**
	 * Permission level that can activate any account license on this site.
	 *
	 * @since 1.26.0
	 */
	public const PERMISSION_SELF_SERVICE = 'self_service';

	/**
	 * Permission level that can toggle only the licenses allocated to this site.
	 *
	 * @since 1.26.0
	 */
	public const PERMISSION_MANAGE_ALLOCATED = 'manage_allocated';

	/**
	 * Permissions that act on licenses and therefore cache the `available` list (masked keys). A read-only
	 * `view` connection is excluded — it caches only its permission so its guidance renders on first paint.
	 *
	 * @since 1.26.0
	 */
	public const ACTIONABLE_PERMISSIONS = [ self::PERMISSION_SELF_SERVICE, self::PERMISSION_MANAGE_ALLOCATED ];

	/**
	 * Fixed cache scope, or null to resolve it from the current admin context.
	 *
	 * @since 1.26.0
	 *
	 * @var string|null
	 */
	private $scope;

	/**
	 * Request-scoped memoized reads, keyed by network slot or site + blog id. A present key mapping to `null`
	 * means "read, no cache".
	 *
	 * @since 1.26.0
	 *
	 * @var array<string,array|null>
	 */
	private static $memo = [];

	/**
	 * @since 1.26.0
	 *
	 * @param string|null $scope Force cache operations to this scope ('network' or 'site'), or null for context.
	 */
	public function __construct( ?string $scope = null ) {
		$this->scope = ( TokenStore::SCOPE_NETWORK === $scope || TokenStore::SCOPE_SITE === $scope ) ? $scope : null;
	}

	/**
	 * Stores a verified catalog pull: the `available` list for an actionable permission, permission-only
	 * for read-only `view`, or a purge when the pull carries no permission at all.
	 *
	 * A failed pull never reaches here (callers only pass a verified, non-error payload), so the
	 * last-good cache is only ever replaced by a fresh success.
	 *
	 * @since 1.26.0
	 *
	 * @param array       $pulled      Decoded, signature-verified payload from ConnectClient::list_licenses().
	 * @param string|null $fingerprint Fingerprint of the connection that produced this payload. Pass the value
	 *                                 captured before the pull so the cache is stamped with the producer
	 *                                 connection even if the stored connection changed while it was in flight.
	 *                                 Falls back to the current connection only when not supplied.
	 *
	 * @return void
	 */
	public function store( array $pulled, ?string $fingerprint = null ): void {
		$permission = (string) ( $pulled['permission'] ?? '' );
		$available  = is_array( $pulled['available'] ?? null ) ? $pulled['available'] : [];

		// A pull with no permission at all carries nothing worth caching — drop any stale record.
		if ( '' === $permission ) {
			$this->purge();

			return;
		}

		// Actionable permissions (self_service, manage_allocated) cache their `available` list (masked keys)
		// so the card and chooser render licenses on first paint. A read-only `view` permission caches only
		// the permission + read-only flag so its guidance line renders on first paint — never a license list
		// it cannot act on, and never the stale list a self_service→view downgrade must drop.
		$is_actionable = in_array( $permission, self::ACTIONABLE_PERMISSIONS, true );

		$scope   = $this->effective_scope();
		$catalog = [
			'permission'  => $permission,
			'read_only'   => (bool) ( $pulled['read_only'] ?? false ),
			'available'   => $is_actionable ? $available : [],
			'fetched_at'  => time(),
			// Bind the cache to the connection that produced it, so a per-blog cache left by a previous
			// connection or account can never render as current (see get()).
			'fingerprint' => null === $fingerprint ? ( new TokenStore( $scope ) )->fingerprint() : $fingerprint,
		];

		if ( TokenStore::SCOPE_NETWORK === $scope ) {
			update_site_option( self::OPTION, $catalog );
		} else {
			update_option( self::OPTION, $catalog, false );
		}

		self::$memo[ $this->memo_key( $scope ) ] = $catalog;
	}

	/**
	 * Returns the cached catalog, or `null` when none is cached. Request-scoped memoized so the
	 * per-page render reads the option at most once per bundled Foundation copy.
	 *
	 * @since 1.26.0
	 *
	 * @return array|null
	 */
	public function get(): ?array {
		$scope    = $this->effective_scope();
		$memo_key = $this->memo_key( $scope );

		if ( ! array_key_exists( $memo_key, self::$memo ) ) {
			$stored = TokenStore::SCOPE_NETWORK === $scope
				? get_site_option( self::OPTION )
				: get_option( self::OPTION );

			self::$memo[ $memo_key ] = is_array( $stored ) && ! empty( $stored ) ? $stored : null;
		}

		$catalog = self::$memo[ $memo_key ];

		if ( null === $catalog ) {
			return null;
		}

		// Re-validate on EVERY read, including a memo hit: the connection can change within a request, and on
		// multisite a disconnect only purges the current blog's cache, so a stale per-blog catalog from a prior
		// connection/account can survive elsewhere. A fingerprint mismatch (or absent connection) is rejected.
		$fingerprint = ( new TokenStore( $scope ) )->fingerprint();

		if ( null === $fingerprint || ( $catalog['fingerprint'] ?? null ) !== $fingerprint ) {
			$this->purge_scope( $scope );

			return null;
		}

		return $catalog;
	}

	/**
	 * Deletes the cached catalog in this instance's effective scope.
	 *
	 * @since 1.26.0
	 *
	 * @return void
	 */
	public function purge(): void {
		$this->purge_scope( $this->effective_scope() );
	}

	/**
	 * Returns the forced scope, or the scope resolved from the current admin context.
	 *
	 * @since 1.26.0
	 *
	 * @return string
	 */
	private function effective_scope(): string {
		return null === $this->scope ? ( new TokenStore() )->scope() : $this->scope;
	}

	/**
	 * Deletes and clears the memo for one resolved cache scope.
	 *
	 * @since 1.26.0
	 *
	 * @param string $scope Resolved cache scope.
	 *
	 * @return void
	 */
	private function purge_scope( string $scope ): void {
		if ( TokenStore::SCOPE_NETWORK === $scope ) {
			delete_site_option( self::OPTION );
		} else {
			delete_option( self::OPTION );
		}

		self::$memo[ $this->memo_key( $scope ) ] = null;
	}

	/**
	 * Returns the request memo key for one resolved cache scope.
	 *
	 * @since 1.26.0
	 *
	 * @param string $scope Resolved cache scope.
	 *
	 * @return string
	 */
	private function memo_key( string $scope ): string {
		return TokenStore::SCOPE_NETWORK === $scope ? 'network' : 'site:' . $this->blog_id();
	}

	/**
	 * Current blog id, or 0 outside a WordPress runtime (keeps the per-blog memo keyable in any context).
	 *
	 * @since 1.26.0
	 *
	 * @return int
	 */
	private function blog_id(): int {
		return function_exists( 'get_current_blog_id' ) ? (int) get_current_blog_id() : 0;
	}
}
