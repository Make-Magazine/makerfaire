<?php
/**
 * Computes the optimistic-concurrency version token for a View.
 *
 * @package     GravityKit\GravityView\View\Concurrency
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\View\Concurrency;

/**
 * Derives a stable version string from a View's `post_modified_gmt` plus a
 * counter, mirroring the legacy `InspectorRoute::compute_version` behavior.
 * Returned both as an `ETag` header and as the `version` field on response
 * payloads.
 *
 * Phase 4f's ViewConfigApplier bumps the version after every write by
 * updating `post_modified_gmt`; the counter is incremented when consecutive
 * writes land within the same second.
 *
 * @since 3.0.0
 */
final class ViewVersionComputer {

	/**
	 * Post-meta key for the in-second collision counter.
	 *
	 * MUST match InspectorRoute::META_VERSION_COUNTER — the legacy key.
	 * Diverging would cause the abilities-side ViewVersionComputer and
	 * the REST-side InspectorRoute counter to drift, producing version
	 * tokens the other surface would reject as stale.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private const META_COUNTER = '_gravityview_config_version_counter';

	/**
	 * Compute the current version token for a View.
	 *
	 * MUST match the format InspectorRoute::compute_version() emits — the REST
	 * surface exposes the ISO form to clients via view-config-get/etc., and
	 * those clients round-trip the token back to abilities that compare via
	 * this computer. Raw MySQL datetime would not match.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View post id.
	 *
	 * @return string `{post_modified_gmt_iso}:{counter}` (e.g., `2026-05-20T12:34:56Z:3`).
	 */
	public function compute( int $view_id ): string {
		if ( $view_id <= 0 ) {
			return '';
		}

		// Returning '' here means "this View has no version" — every
		// optimistic-concurrency check downstream is then forced into a 409.
		// InspectorRoute::compute_version() (the REST surface clients read
		// the token from) has a three-step fallback (post_modified_gmt →
		// post_date_gmt → request time). Mirror that chain exactly so a
		// fresh-draft View (post_modified_gmt = '0000-00-00 00:00:00') can
		// round-trip read → write through the batch path without 409'ing
		// every call.
		$post = get_post( $view_id );
		if ( ! $post ) {
			return '';
		}

		$timestamp = $this->normalize_gmt( $post->post_modified_gmt );
		if ( '' === $timestamp ) {
			$timestamp = $this->normalize_gmt( $post->post_date_gmt );
		}
		if ( '' === $timestamp ) {
			// Fresh draft with neither column populated. Use request time as
			// InspectorRoute::compute_version() does, so writes succeed.
			$timestamp = gmdate( 'Y-m-d\TH:i:s\Z' );
		}

		$counter = (int) get_post_meta( $view_id, self::META_COUNTER, true );

		return sprintf( '%s:%d', $timestamp, $counter );
	}

	/**
	 * Normalize a raw MySQL GMT datetime column to ISO 8601 Z form, or '' when
	 * the column is empty / NULL / the WP empty-date sentinel.
	 *
	 * @param mixed $raw_gmt MySQL datetime string.
	 *
	 * @return string ISO 8601 timestamp or empty string.
	 */
	private function normalize_gmt( $raw_gmt ): string {
		if ( ! is_string( $raw_gmt ) ) {
			return '';
		}
		if ( '' === $raw_gmt || '0000-00-00 00:00:00' === $raw_gmt ) {
			return '';
		}

		return gmdate( 'Y-m-d\TH:i:s\Z', strtotime( $raw_gmt . ' UTC' ) );
	}

	/**
	 * Bump the version: refresh `post_modified_gmt` to now and increment the
	 * counter. Returns the new version token.
	 *
	 * **Caller MUST hold the corresponding `ViewLock` for $view_id.** The counter
	 * increment is read-modify-write (`get_post_meta`, `update_post_meta`) and is
	 * only safe when serialized externally — concurrent calls without the lock
	 * will race and one of the increments will be lost. Every batch write path
	 * already runs inside `SlotRepositoryTrait::with_lock()`; singleton paths run
	 * inside `InspectorRoute::with_view_lock()`.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View post id.
	 *
	 * @return string The new version token.
	 */
	public function bump( int $view_id ): string {
		if ( $view_id <= 0 ) {
			return '';
		}
		global $wpdb;

		$now = current_time( 'mysql', true );

		// Direct $wpdb->update bypasses save_post / post_updated hooks so
		// the bump doesn't recursively fire write-side hooks that may call
		// bump() again. Cache invalidated via clean_post_cache below.
		$rows = $wpdb->update(
			$wpdb->posts,
			[
				'post_modified_gmt' => $now,
				'post_modified'     => get_date_from_gmt( $now ),
			],
			[ 'ID' => $view_id ],
			[ '%s', '%s' ],
			[ '%d' ]
		);
		if ( false === $rows ) {
			// SQL error (not "no rows changed"). View existed but write
			// failed — surface this distinctly.
			return '';
		}

		clean_post_cache( $view_id );

		// Counter is incremented inside the ViewLock acquired by the caller.
		// (Field/Widget/Search/Grid slot repositories, ViewDeleter,
		// ViewConfigApplier — all acquire the lock first).
		// That means the read-then-write below is atomic at the application
		// level even though it isn't SQL-atomic — concurrent writers are
		// serialized through the MySQL advisory lock.
		//
		// (Earlier attempt used INSERT ... ON DUPLICATE KEY UPDATE on
		// wp_postmeta, but wp_postmeta has no UNIQUE(post_id, meta_key)
		// constraint, so DUPLICATE KEY never fires — that would have
		// silently inserted a new postmeta row per bump).
		$counter = (int) get_post_meta( $view_id, self::META_COUNTER, true );
		++$counter;
		update_post_meta( $view_id, self::META_COUNTER, $counter );

		return sprintf( '%s:%d', gmdate( 'Y-m-d\TH:i:s\Z', strtotime( $now . ' UTC' ) ), $counter );
	}
}
