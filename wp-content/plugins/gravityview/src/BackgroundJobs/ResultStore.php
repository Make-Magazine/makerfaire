<?php
/**
 * Background job result storage.
 *
 * @package GravityKit\GravityView\BackgroundJobs
 * @since 3.0.0
 */

namespace GravityKit\GravityView\BackgroundJobs;

use GravityKit\GravityView\Foundation\Helpers\WP;

/**
 * Stores lightweight result state for GravityView background jobs.
 *
 * Foundation owns job history. This store is only for product-level status
 * payloads that GravityView needs to retrieve by a stable token.
 *
 * @since 3.0.0
 */
final class ResultStore {
	public const PREFIX = 'gravityview_background_result_';

	public const DEFAULT_TTL = DAY_IN_SECONDS;

	private const LOCK_PREFIX            = 'gravityview_background_result_lock_';
	private const LOCK_TTL               = 30;
	private const LOCK_WAIT_MICROSECONDS = 50000;
	private const LOCK_ATTEMPTS          = 40;

	/**
	 * Stores a result payload.
	 *
	 * @since 3.0.0
	 *
	 * @param string $token Result token.
	 * @param array  $data  Result payload.
	 * @param int    $ttl   Expiration in seconds.
	 *
	 * @return bool
	 */
	public function set( string $token, array $data, int $ttl = self::DEFAULT_TTL ): bool {
		$token = $this->normalize_token( $token );

		if ( '' === $token ) {
			return false;
		}

		$data['updated_at'] = time();

		return WP::set_transient( $this->key( $token ), $data, max( 1, $ttl ) );
	}

	/**
	 * Updates a result payload from the latest stored value.
	 *
	 * Return an array from the mutator to store an updated payload. Return null
	 * to leave the latest payload unchanged.
	 *
	 * @since 3.0.0
	 *
	 * @param string   $token   Result token.
	 * @param callable $mutator Callback receiving the latest payload.
	 * @param int      $ttl     Expiration in seconds.
	 * @param array    $context Additional log context.
	 *
	 * @return array|null Latest stored payload after the update attempt.
	 */
	public function update( string $token, callable $mutator, int $ttl = self::DEFAULT_TTL, array $context = [] ): ?array {
		$token = $this->normalize_token( $token );

		if ( '' === $token ) {
			return null;
		}

		$lock_value = $this->acquire_lock( $token );

		if ( null === $lock_value ) {
			gravityview()->log->debug(
				'Skipped background result update because the result lock could not be acquired.',
				array_merge(
					$context,
					[
						'token_hash' => substr( hash( 'sha256', $token ), 0, 12 ),
					]
				)
			);

			return $this->get( $token );
		}

		try {
			$current = $this->get( $token );
			$next    = call_user_func( $mutator, is_array( $current ) ? $current : [] );

			if ( ! is_array( $next ) ) {
				return $current;
			}

			return $this->set( $token, $next, $ttl ) ? $this->get( $token ) : $current;
		} finally {
			$this->release_lock( $token, $lock_value );
		}
	}

	/**
	 * Reads a result payload.
	 *
	 * @since 3.0.0
	 *
	 * @param string $token Result token.
	 *
	 * @return array|null
	 */
	public function get( string $token ): ?array {
		$token = $this->normalize_token( $token );

		if ( '' === $token ) {
			return null;
		}

		$result = WP::get_transient( $this->key( $token ) );

		return is_array( $result ) ? $result : null;
	}

	/**
	 * Deletes a result payload.
	 *
	 * @since 3.0.0
	 *
	 * @param string $token Result token.
	 *
	 * @return bool
	 */
	public function delete( string $token ): bool {
		$token = $this->normalize_token( $token );

		if ( '' === $token ) {
			return false;
		}

		return WP::delete_transient( $this->key( $token ) );
	}

	/**
	 * Builds the transient key for a token.
	 *
	 * @since 3.0.0
	 *
	 * @param string $token Result token.
	 *
	 * @return string
	 */
	public function key( string $token ): string {
		return self::PREFIX . $this->normalize_token( $token );
	}

	/**
	 * Creates a new result token.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function create_token(): string {
		return wp_generate_uuid4();
	}

	/**
	 * Normalizes a token for storage.
	 *
	 * @since 3.0.0
	 *
	 * @param string $token Result token.
	 *
	 * @return string
	 */
	private function normalize_token( string $token ): string {
		$token = preg_replace( '/[^a-zA-Z0-9_-]/', '', $token );

		return $token ? $token : '';
	}

	/**
	 * Acquires a short-lived option lock for a result token.
	 *
	 * The lock uses an insert-only option query instead of add_option(), because
	 * some WordPress versions implement add_option() with an upsert internally.
	 *
	 * @since 3.0.0
	 *
	 * @param string $token Result token.
	 *
	 * @return string|null The lock value on success, null on failure.
	 */
	private function acquire_lock( string $token ): ?string {
		$lock_key   = $this->lock_key( $token );
		$lock_value = sprintf( '%F:%s', microtime( true ), wp_generate_password( 12, false ) );

		for ( $attempt = 0; $attempt < self::LOCK_ATTEMPTS; ++$attempt ) {
			if ( WP::insert_option_if_absent( $lock_key, $lock_value ) ) {
				return $lock_value;
			}

			$existing  = (string) get_option( $lock_key );
			$parts     = explode( ':', $existing, 2 );
			$locked_at = (float) ( $parts[0] ?? 0 );

			if ( $locked_at > 0 && microtime( true ) - $locked_at > self::LOCK_TTL ) {
				$this->delete_lock_if_matches( $lock_key, $existing );
				continue;
			}

			usleep( self::LOCK_WAIT_MICROSECONDS );
		}

		return null;
	}

	/**
	 * Atomically deletes a lock row only if its value still matches the one we hold.
	 *
	 * Both stale-cleanup and normal release route through here. Without value-matched
	 * deletion, a worker that ran past LOCK_TTL could release another worker's fresh
	 * lock when its finally block fires, or a stale-cleanup pass could evict a lock
	 * acquired between the staleness check and the delete.
	 *
	 * @since 3.0.0
	 *
	 * @param string $lock_key      Option name.
	 * @param string $expected_value Value that must still match for deletion.
	 *
	 * @return void
	 */
	private function delete_lock_if_matches( string $lock_key, string $expected_value ): void {
		global $wpdb;

		$deleted = $wpdb->query(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"DELETE FROM `{$wpdb->options}` WHERE `option_name` = %s AND `option_value` = %s",
				$lock_key,
				$expected_value
			)
		);

		if ( $deleted ) {
			wp_cache_delete( $lock_key, 'options' );
		}
	}

	/**
	 * Releases a result token option lock only if we still hold it.
	 *
	 * A worker that ran past LOCK_TTL may have had its lock stale-cleaned and
	 * reacquired by another worker; releasing unconditionally would evict the
	 * new holder. The compare-and-delete here makes that a no-op instead.
	 *
	 * @since 3.0.0
	 *
	 * @param string $token      Result token.
	 * @param string $lock_value Value returned by acquire_lock().
	 *
	 * @return void
	 */
	private function release_lock( string $token, string $lock_value ): void {
		$this->delete_lock_if_matches( $this->lock_key( $token ), $lock_value );
	}

	/**
	 * Builds the lock option key for a result token.
	 *
	 * @since 3.0.0
	 *
	 * @param string $token Result token.
	 *
	 * @return string
	 */
	private function lock_key( string $token ): string {
		return self::LOCK_PREFIX . $this->normalize_token( $token );
	}
}
