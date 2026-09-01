<?php
/**
 * MySQL advisory lock for serializing View writes.
 *
 * @package     GravityKit\GravityView\View\Concurrency
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\View\Concurrency;

/**
 * Per-View advisory lock encapsulating `GET_LOCK()` / `RELEASE_LOCK()`.
 *
 * **Multisite safety:** lock name is namespaced by blog id (`gv_view_{blog_id}_{view_id}`)
 * so two sites with the same View id on a network do NOT contend on the same
 * MySQL advisory lock. The legacy `gv_view_{id}` name (pre-3.1.0) had a
 * latent multisite race that Codex round 5 surfaced.
 *
 * The lock is acquired on every write — `If-Match` precondition checks
 * happen INSIDE the lock so the version comparison + write are atomic.
 *
 * @since 3.0.0
 */
final class ViewLock {

	/**
	 * Suggested lock timeout (seconds).
	 *
	 * @since 3.0.0
	 */
	private const LOCK_TIMEOUT_SECONDS = 5;

	/**
	 * Lock-name prefix. MySQL GET_LOCK names have a 64-byte limit. With this
	 * 8-char prefix + `_{blog_id}_{view_id}` (up to ~20 chars for large
	 * multisite networks + large post ids), we stay well under the limit.
	 *
	 * @since 3.0.0
	 */
	private const LOCK_NAME_PREFIX = 'gv_view_';

	/**
	 * Acquire a lock for the given View id.
	 *
	 * Blocks up to LOCK_TIMEOUT_SECONDS waiting for the lock. Returns true on
	 * acquire, false on timeout. Callers should treat false as a 503
	 * (transient contention).
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View post id.
	 *
	 * @return bool True if lock acquired.
	 */
	public function acquire( int $view_id ): bool {
		if ( $view_id <= 0 ) {
			return false;
		}
		global $wpdb;
		$name   = $this->lock_name( $view_id );
		$result = $wpdb->get_var(
			$wpdb->prepare( 'SELECT GET_LOCK(%s, %d)', $name, self::LOCK_TIMEOUT_SECONDS )
		);

		// GET_LOCK returns 1 on success, 0 on timeout, NULL on error. Treat
		// anything other than '1' as failure.
		return '1' === (string) $result;
	}

	/**
	 * Release the lock for the given View id.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View post id.
	 *
	 * @return bool True if released; false if the lock wasn't held by this session.
	 */
	public function release( int $view_id ): bool {
		if ( $view_id <= 0 ) {
			return false;
		}
		global $wpdb;
		$name   = $this->lock_name( $view_id );
		$result = $wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $name ) );

		// RELEASE_LOCK returns 1 if released, 0 if the lock wasn't held by
		// this thread, NULL if the lock didn't exist.
		return '1' === (string) $result;
	}

	/**
	 * Compute the MySQL lock name for a given View id, namespaced by blog id
	 * on multisite. Format: `gv_view_{blog_id}_{view_id}`.
	 *
	 * On single-site (`get_current_blog_id() === 1`), the format is still
	 * `gv_view_1_{view_id}` — the namespacing applies uniformly so behavior
	 * is consistent across site types.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View post id.
	 *
	 * @return string Lock name (max 64 chars per MySQL `GET_LOCK` limit).
	 */
	private function lock_name( int $view_id ): string {
		$blog_id = function_exists( 'get_current_blog_id' ) ? (int) get_current_blog_id() : 1;
		return sprintf( '%s%d_%d', self::LOCK_NAME_PREFIX, $blog_id, $view_id );
	}
}
