<?php
/**
 * Frontend bulk action lifecycle events.
 *
 * @package GravityKit\GravityView\Entry\BulkActions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions;

/**
 * Emits the small public lifecycle event surface for background bulk actions.
 *
 * @since 3.0.0
 */
final class LifecycleEvents {
	/**
	 * Emits the background job started event.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $result Stored result data.
	 * @param string $token  Result token.
	 *
	 * @return void
	 */
	public static function job_started( array $result, $token ) {
		self::emit( 'job-started', $result, $token );
	}

	/**
	 * Emits a terminal lifecycle event when a job status changes.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $result          Stored result data.
	 * @param string $previous_status Previous status.
	 * @param string $token           Result token.
	 *
	 * @return void
	 */
	public static function terminal_transition( array $result, $previous_status, $token ) {
		$status = sanitize_key( (string) ( $result['status'] ?? '' ) );

		if ( sanitize_key( (string) $previous_status ) === $status ) {
			return;
		}

		if ( 'complete' === $status ) {
			self::emit( 'job-completed', $result, $token );
			return;
		}

		if ( 'failed' === $status ) {
			self::emit( 'job-failed', $result, $token );
			return;
		}

		if ( 'canceled' === $status ) {
			self::emit( 'job-canceled', $result, $token );
		}
	}

	/**
	 * Emits the result dismissed event.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $result Stored result data.
	 * @param string $token  Result token.
	 *
	 * @return void
	 */
	public static function result_dismissed( array $result, $token ) {
		self::emit( 'result-dismissed', $result, $token );
	}

	/**
	 * Emits a lifecycle action.
	 *
	 * @since 3.0.0
	 *
	 * @param string $event  Event suffix.
	 * @param array  $result Stored result data.
	 * @param string $token  Result token.
	 *
	 * @return void
	 */
	private static function emit( $event, array $result, $token ) {
		$event      = sanitize_key( $event );
		$token      = sanitize_key( (string) $token );
		$view_id    = (int) ( $result['view_id'] ?? 0 );
		$action_key = sanitize_key( (string) ( $result['action_key'] ?? '' ) );
		$payload    = self::get_public_payload( $result, $token );

		/**
		 * Fires when a frontend bulk action background job changes lifecycle state.
		 *
		 * Event names:
		 * - gk/gravityview/bulk-actions/job-started
		 * - gk/gravityview/bulk-actions/job-completed
		 * - gk/gravityview/bulk-actions/job-failed
		 * - gk/gravityview/bulk-actions/job-canceled
		 * - gk/gravityview/bulk-actions/result-dismissed
		 *
		 * @since 3.0.0
		 *
		 * @param array  $payload    Stable lifecycle payload.
		 * @param int    $view_id    View ID.
		 * @param string $action_key Action key.
		 * @param string $token      Result token.
		 */
		do_action( 'gk/gravityview/bulk-actions/' . $event, $payload, $view_id, $action_key, $token );
	}

	/**
	 * Returns the stable public payload for lifecycle observers.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $result Stored result data.
	 * @param string $token  Result token.
	 *
	 * @return array
	 */
	private static function get_public_payload( array $result, $token ) {
		return [
			'token'          => sanitize_key( (string) $token ),
			'view_id'        => (int) ( $result['view_id'] ?? 0 ),
			'blog_id'        => (int) ( $result['blog_id'] ?? get_current_blog_id() ),
			'action_key'     => sanitize_key( (string) ( $result['action_key'] ?? '' ) ),
			'status'         => sanitize_key( (string) ( $result['status'] ?? '' ) ),
			'selected_count' => max( 0, (int) ( $result['selected_count'] ?? $result['total'] ?? 0 ) ),
			'total'          => max( 0, (int) ( $result['total'] ?? $result['selected_count'] ?? 0 ) ),
			'processed'      => max( 0, (int) ( $result['processed'] ?? 0 ) ),
			'failed'         => max( 0, (int) ( $result['failed'] ?? 0 ) ),
			'job_id'         => max( 0, (int) ( $result['job_id'] ?? 0 ) ),
		];
	}
}
