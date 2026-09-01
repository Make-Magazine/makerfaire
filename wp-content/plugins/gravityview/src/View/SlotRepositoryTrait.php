<?php
/**
 * Shared lock/precondition/version helpers for View slot repositories.
 *
 * @package     GravityKit\GravityView\View
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\View;

use GravityKit\GravityView\View\Concurrency\ViewLock;
use GravityKit\GravityView\View\Concurrency\ViewPreconditionChecker;
use GravityKit\GravityView\View\Concurrency\ViewVersionComputer;
use WP_Error;

/**
 * Boilerplate shared by standalone View repositories.
 *
 * @since 3.0.0
 */
trait SlotRepositoryTrait {

	/**
	 * View-lock service.
	 *
	 * @since 3.0.0
	 *
	 * @var ViewLock
	 */
	private ViewLock $lock;

	/**
	 * Precondition checker.
	 *
	 * @since 3.0.0
	 *
	 * @var ViewPreconditionChecker
	 */
	private ViewPreconditionChecker $precondition;

	/**
	 * Version computer.
	 *
	 * @since 3.0.0
	 *
	 * @var ViewVersionComputer
	 */
	private ViewVersionComputer $version;

	/**
	 * Initialize shared services.
	 *
	 * @since 3.0.0
	 *
	 * @param ViewLock|null                $lock         View-lock service.
	 * @param ViewPreconditionChecker|null $precondition Precondition checker.
	 * @param ViewVersionComputer|null     $version      Version computer.
	 */
	protected function init_slot_repository_services(
		?ViewLock $lock = null,
		?ViewPreconditionChecker $precondition = null,
		?ViewVersionComputer $version = null
	): void {
		$this->lock         = $lock ?? new ViewLock();
		$this->precondition = $precondition ?? new ViewPreconditionChecker();
		$this->version      = $version ?? new ViewVersionComputer();
	}

	/**
	 * Run work while holding the View lock.
	 *
	 * @since 3.0.0
	 *
	 * @param int      $view_id View post id.
	 * @param callable $work    Work to run.
	 * @return mixed|WP_Error
	 */
	protected function with_lock( int $view_id, callable $work ) {
		if ( ! $this->lock->acquire( $view_id ) ) {
			return new WP_Error(
				'gv_rest_concurrency_lock',
				__( 'Another change is being applied to this View. Try again in a moment.', 'gk-gravityview' ),
				[ 'status' => 503 ]
			);
		}

		try {
			return $work();
		} finally {
			$this->lock->release( $view_id );
		}
	}

	/**
	 * Check optimistic concurrency.
	 *
	 * @since 3.0.0
	 *
	 * @param int         $view_id  View post id.
	 * @param string|null $if_match Optimistic-concurrency token.
	 * @return true|WP_Error
	 */
	protected function check_precondition( int $view_id, ?string $if_match ) {
		return $this->precondition->check( $view_id, $if_match );
	}

	/**
	 * Compute the current View version.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View post id.
	 * @return string
	 */
	protected function compute_version( int $view_id ): string {
		return $this->version->compute( $view_id );
	}

	/**
	 * Bump and return the new View version.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View post id.
	 * @return string
	 */
	protected function bump_version( int $view_id ): string {
		return $this->version->bump( $view_id );
	}

	/**
	 * Build a 400 invalid-input WP_Error.
	 *
	 * @since 3.0.0
	 *
	 * @param string $message Human-readable message.
	 * @return WP_Error
	 */
	protected function invalid_input( string $message ): WP_Error {
		return new WP_Error( 'gv_rest_invalid_input', $message, [ 'status' => 400 ] );
	}

	/**
	 * Build a 500 version-bump-failure WP_Error.
	 *
	 * Returned by batch_apply (and the legacy singleton add/patch paths) when
	 * the slot/widget/grid write committed but the View version bump failed.
	 * Callers must treat this as a hard failure — the View's persisted state
	 * may not match its version token.
	 *
	 * @since 3.0.0
	 *
	 * @return WP_Error
	 */
	protected function version_bump_failed( ?string $message = null ): WP_Error {
		return new WP_Error(
			'gv_rest_version_bump_failed',
			null === $message ? __( 'The View was changed but could not be marked updated.', 'gk-gravityview' ) : $message,
			[ 'status' => 500 ]
		);
	}

	/**
	 * Persist a meta-keyed value with no-op short-circuit and DB-failure detection.
	 *
	 * `update_post_meta()` returns `false` for two non-error cases (the new value
	 * equals the stored value; the row already exists with the same value) and for
	 * one error case (the DB write failed). Comparing a hash of the persisted
	 * value before and after the call disambiguates: when the hashes already
	 * match, no write is attempted; when they differ and the post-call read still
	 * reports the old hash, the DB write failed and the caller gets a 500.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id  View post id.
	 * @param string $meta_key Post-meta key being written.
	 * @param mixed  $value    Value to persist.
	 *
	 * @return bool|WP_Error True when the new value persisted, false when the call
	 *                       was a no-op (stored value already matched), or a
	 *                       gk_batch_persist_failed WP_Error on DB failure.
	 */
	protected function persist_meta( int $view_id, string $meta_key, $value ) {
		$existing = get_post_meta( $view_id, $meta_key, true );
		if ( $this->meta_value_hash( $existing ) === $this->meta_value_hash( $value ) ) {
			return false;
		}

		update_post_meta( $view_id, $meta_key, $value );

		$after = get_post_meta( $view_id, $meta_key, true );
		if ( $this->meta_value_hash( $after ) !== $this->meta_value_hash( $value ) ) {
			return new WP_Error(
				'gk_batch_persist_failed',
				__( 'Failed to persist the batch.', 'gk-gravityview' ),
				[ 'status' => 500 ]
			);
		}

		return true;
	}

	/**
	 * Compute a stable hash of a meta value for no-op detection.
	 *
	 * Arrays serialize to canonical-ish JSON; scalars/strings hash directly.
	 *
	 * @param mixed $value Stored or candidate meta value.
	 *
	 * @return string
	 */
	private function meta_value_hash( $value ): string {
		if ( is_string( $value ) ) {
			return sha1( $value );
		}

		return sha1( (string) wp_json_encode( $value ) );
	}

	/**
	 * Normalize a caller-provided batch version token.
	 *
	 * Trims whitespace and strips a surrounding pair of double-quotes so
	 * clients can pass the value as either a bare token or an HTTP ETag-style
	 * quoted string. Both round-trip through view-config-get/view-widget-
	 * settings-get untouched.
	 *
	 * @since 3.0.0
	 *
	 * @param string $token Caller-supplied version token.
	 * @return string
	 */
	protected function normalize_version_token( string $token ): string {
		$token = trim( $token );
		if ( strlen( $token ) >= 2 && '"' === $token[0] && '"' === substr( $token, -1 ) ) {
			return substr( $token, 1, -1 );
		}

		return $token;
	}

	/**
	 * Build a 409 batch version-mismatch WP_Error.
	 *
	 * @since 3.0.0
	 *
	 * @param string $current_version Current View version.
	 * @param string $version_token   Caller-provided token.
	 * @return WP_Error
	 */
	protected function batch_version_mismatch( string $current_version, string $version_token ): WP_Error {
		return new WP_Error(
			'gk_batch_version_mismatch',
			__( 'Someone else changed this View. Reload and try again.', 'gk-gravityview' ),
			[
				'status'          => 409,
				'current_version' => $current_version,
				'version'         => $version_token,
			]
		);
	}

	/**
	 * Build a per-item batch failure row.
	 *
	 * @since 3.0.0
	 *
	 * @param int      $index Batch item index.
	 * @param WP_Error $error Item failure.
	 * @return array<string,mixed>
	 */
	protected function batch_failure( int $index, WP_Error $error ): array {
		return [
			'index'   => $index,
			'code'    => $error->get_error_code(),
			'message' => $error->get_error_message(),
			'data'    => is_array( $error->get_error_data() ) ? $error->get_error_data() : [],
		];
	}

	/**
	 * Build the aggregated 400 batch validation-failure WP_Error.
	 *
	 * @since 3.0.0
	 *
	 * @param array $failures Per-item failure rows from batch_failure().
	 * @param int   $total    Total item count submitted in the batch.
	 * @return WP_Error
	 */
	protected function batch_validation_failed( array $failures, int $total ): WP_Error {
		/* translators: placeholders in [brackets] are replaced and must not be translated. */
		$message = strtr(
			__( '[failed] of [total] batch items failed validation.', 'gk-gravityview' ),
			[
				'[failed]' => (string) count( $failures ),
				'[total]'  => (string) $total,
			]
		);

		return new WP_Error(
			'gk_batch_validation_failed',
			$message,
			[
				'status'   => 400,
				'failures' => $failures,
			]
		);
	}
}
