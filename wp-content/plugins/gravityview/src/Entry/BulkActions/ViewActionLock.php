<?php
/**
 * Frontend bulk action View lock.
 *
 * @package GravityKit\GravityView\Entry\BulkActions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions;

use GravityKit\GravityView\BackgroundJobs\ResultStore;
use GravityKit\GravityView\BackgroundJobs\Scheduler;
use GravityKit\GravityView\Foundation\Helpers\WP as WPHelper;
use GravityKit\GravityView\View\View;
use WP_Error;

/**
 * Prevents conflicting bulk actions from running against the same View.
 *
 * The lock is stored as a non-autoloaded WordPress option. Lock creation uses
 * an insert-only query against the unique `option_name` index so only one
 * request can create the row. Maintenance operations (`touch`, `release`, and
 * stale cleanup) are serialized by a short-lived mutation mutex that uses the
 * same insert-only primitive. Reads are intentionally side-effect-free; stale
 * cleanup happens only through explicit cleanup/release paths.
 *
 * @since 3.0.0
 */
final class ViewActionLock {
	const OPTION_PREFIX                   = 'gravityview_bulk_action_view_lock_';
	const MISSING_RESULT_GRACE            = 60;
	const MUTATION_LOCK_PREFIX            = 'gravityview_bulk_action_view_lock_mutex_';
	const MUTATION_LOCK_TTL               = 30;
	const MUTATION_LOCK_WAIT_MICROSECONDS = 50000;
	const MUTATION_LOCK_ATTEMPTS          = 40;

	/**
	 * @since 3.0.0
	 * @var ResultStore
	 */
	private $result_store;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param ResultStore|null $result_store Result store.
	 */
	public function __construct( ?ResultStore $result_store = null ) {
		$this->result_store = $result_store ? $result_store : new ResultStore();
	}

	/**
	 * Acquires the View lock for a bulk action.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view       View.
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 * @param bool   $background Whether the action is being queued.
	 * @param string $token      Optional result token.
	 *
	 * @return array|WP_Error Lock data or error when another action is active.
	 */
	public function acquire( View $view, $action_key, array $action, $background = false, $token = '' ) {
		$view_id = (int) $view->ID;
		$token   = sanitize_key( $token ? $token : $this->result_store->create_token() );
		$now     = time();
		$ttl     = Config::get_view_lock_ttl( $view, (bool) $background );
		$lock    = [
			'view_id'       => $view_id,
			'result_token'  => $token,
			'action_key'    => sanitize_key( $action_key ),
			'action_label'  => sanitize_text_field( (string) ( $action['label'] ?? $action_key ) ),
			'actor_user_id' => get_current_user_id(),
			'background'    => (bool) $background,
			'created_at'    => $now,
			'updated_at'    => $now,
			'ttl'           => $ttl,
			'expires_at'    => $now + $ttl,
		];

		if ( WPHelper::insert_option_if_absent( $this->key( $view_id ), $lock ) ) {
			return $lock;
		}

		$this->cleanup_if_stale( $view );

		if ( WPHelper::insert_option_if_absent( $this->key( $view_id ), $lock ) ) {
			return $lock;
		}

		$active_lock = $this->get( $view );

		return $active_lock ? $this->get_locked_error( $active_lock ) : new WP_Error(
			'gravityview_bulk_action_view_lock_failed',
			__( 'The bulk action could not be started. Refresh the page and try again.', 'gk-gravityview' )
		);
	}

	/**
	 * Returns the active lock for a View.
	 *
	 * @since 3.0.0
	 *
	 * @param View|int $view View or View ID.
	 *
	 * @return array|null
	 */
	public function get( $view ) {
		$view_id = $this->view_id( $view );

		if ( ! $view_id ) {
			return null;
		}

		$lock = get_option( $this->key( $view_id ) );

		if ( ! is_array( $lock ) || (int) ( $lock['view_id'] ?? 0 ) !== $view_id ) {
			return null;
		}

		if ( $this->is_expired( $lock ) ) {
			return null;
		}

		if ( ! $this->has_active_result( $lock ) ) {
			return null;
		}

		return $lock;
	}

	/**
	 * Returns whether a View has an active bulk action lock.
	 *
	 * @since 3.0.0
	 *
	 * @param View|int $view View or View ID.
	 *
	 * @return bool
	 */
	public function is_locked( $view ) {
		return null !== $this->get( $view );
	}

	/**
	 * Releases a stale lock for a View.
	 *
	 * @since 3.0.0
	 *
	 * @param View|int $view View or View ID.
	 *
	 * @return bool Whether a stale lock was released.
	 */
	public function cleanup_if_stale( $view ) {
		$view_id = $this->view_id( $view );

		if ( ! $view_id ) {
			return false;
		}

		if ( ! $this->acquire_mutation_lock( $view_id, 'cleanup' ) ) {
			return false;
		}

		try {
			$lock = get_option( $this->key( $view_id ) );

			if ( ! is_array( $lock ) || (int) ( $lock['view_id'] ?? 0 ) !== $view_id ) {
				return delete_option( $this->key( $view_id ) );
			}

			if ( ! $this->is_expired( $lock ) && $this->has_active_result( $lock ) && ! $this->has_terminal_foundation_status( $lock ) ) {
				return false;
			}

			return delete_option( $this->key( $view_id ) );
		} finally {
			$this->release_mutation_lock( $view_id );
		}
	}

	/**
	 * Extends the active lock lifetime.
	 *
	 * @since 3.0.0
	 *
	 * @param View|int $view       View or View ID.
	 * @param string   $token      Result token.
	 * @param bool     $background Whether the action is running in the background.
	 *
	 * @return bool
	 */
	public function touch( $view, $token, $background = true ) {
		$view_id = $this->view_id( $view );
		$token   = sanitize_key( $token );
		$lock    = $view_id ? get_option( $this->key( $view_id ) ) : null;

		if ( ! $view_id || ! $token || ! is_array( $lock ) || sanitize_key( (string) ( $lock['result_token'] ?? '' ) ) !== $token ) {
			return false;
		}

		if ( ! $this->acquire_mutation_lock( $view_id, 'touch' ) ) {
			return false;
		}

		try {
			$lock = get_option( $this->key( $view_id ) );

			if ( ! is_array( $lock ) || sanitize_key( (string) ( $lock['result_token'] ?? '' ) ) !== $token || ! $this->has_active_result( $lock ) ) {
				return false;
			}

			$ttl                = max( 1, (int) ( $lock['ttl'] ?? Config::get_view_lock_ttl( $view instanceof View ? $view : null, (bool) $background ) ) );
			$lock['updated_at'] = time();
			$lock['expires_at'] = time() + $ttl;

			return update_option( $this->key( $view_id ), $lock, false );
		} finally {
			$this->release_mutation_lock( $view_id );
		}
	}

	/**
	 * Releases a View lock.
	 *
	 * @since 3.0.0
	 *
	 * @param View|int $view  View or View ID.
	 * @param string   $token Optional result token. When passed, only the matching lock is released.
	 *
	 * @return bool
	 */
	public function release( $view, $token = '' ) {
		$view_id = $this->view_id( $view );
		$token   = sanitize_key( $token );

		if ( ! $view_id ) {
			return false;
		}

		if ( ! $this->acquire_mutation_lock( $view_id, 'release' ) ) {
			return false;
		}

		try {
			if ( $token ) {
				$lock = get_option( $this->key( $view_id ) );

				if ( ! is_array( $lock ) || sanitize_key( (string) ( $lock['result_token'] ?? '' ) ) !== $token ) {
					return false;
				}
			}

			return delete_option( $this->key( $view_id ) );
		} finally {
			$this->release_mutation_lock( $view_id );
		}
	}

	/**
	 * Returns whether a stored lock still points at active work.
	 *
	 * @since 3.0.0
	 *
	 * @param array $lock Lock data.
	 *
	 * @return bool
	 */
	private function has_active_result( array $lock ) {
		$token = sanitize_key( (string) ( $lock['result_token'] ?? '' ) );

		if ( ! $token ) {
			return false;
		}

		$result = $this->result_store->get( $token );

		if ( ! is_array( $result ) ) {
			if ( empty( $lock['background'] ) ) {
				return ! $this->is_expired( $lock );
			}

			return time() - max( 0, (int) ( $lock['created_at'] ?? 0 ) ) <= self::MISSING_RESULT_GRACE;
		}

		if ( ! empty( $result['consumed'] ) ) {
			return false;
		}

		return in_array( (string) ( $result['status'] ?? '' ), [ 'queued', 'running' ], true );
	}

	/**
	 * Returns whether Foundation reports the locked job as terminal or missing.
	 *
	 * @since 3.0.0
	 *
	 * @param array $lock Lock data.
	 *
	 * @return bool
	 */
	private function has_terminal_foundation_status( array $lock ) {
		$token  = sanitize_key( (string) ( $lock['result_token'] ?? '' ) );
		$result = $token ? $this->result_store->get( $token ) : null;
		$job_id = is_array( $result ) ? (int) ( $result['job_id'] ?? 0 ) : 0;

		if ( $job_id <= 0 ) {
			return time() - max( 0, (int) ( $lock['created_at'] ?? 0 ) ) > self::MISSING_RESULT_GRACE;
		}

		$status = Scheduler::instance()->get_job_status( $job_id );

		if ( is_wp_error( $status ) ) {
			return 'gravityview_background_job_missing' === $status->get_error_code()
				|| (bool) preg_match( '/^Job [0-9]+ not found\\.?$/', trim( $status->get_error_message() ) );
		}

		if ( null === $status ) {
			return true;
		}

		return in_array( sanitize_key( (string) $status ), [ 'complete', 'completed', 'failed', 'failure', 'canceled', 'cancelled', 'deleted', 'missing', 'not_found' ], true );
	}

	/**
	 * Returns the error shown when another action owns the View lock.
	 *
	 * @since 3.0.0
	 *
	 * @param array $lock Lock data.
	 *
	 * @return WP_Error
	 */
	private function get_locked_error( array $lock ) {
		$same_user = get_current_user_id() > 0 && (int) ( $lock['actor_user_id'] ?? 0 ) === get_current_user_id();

		return new WP_Error(
			'gravityview_bulk_action_view_locked',
			$same_user
				? __( 'A bulk action is already running for this View. Wait for it to finish before starting another bulk action.', 'gk-gravityview' )
				: __( 'Another bulk action is already running for this View. Wait for it to finish before starting another bulk action.', 'gk-gravityview' )
		);
	}

	/**
	 * Returns whether a lock is past its safety TTL.
	 *
	 * @since 3.0.0
	 *
	 * @param array $lock Lock data.
	 *
	 * @return bool
	 */
	private function is_expired( array $lock ) {
		return time() > max( 0, (int) ( $lock['expires_at'] ?? 0 ) );
	}

	/**
	 * Returns the lock option key.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View ID.
	 *
	 * @return string
	 */
	private function key( $view_id ) {
		return self::OPTION_PREFIX . absint( $view_id );
	}

	/**
	 * Acquires a short-lived mutation mutex for this View lock.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id   View ID.
	 * @param string $operation Operation being guarded.
	 *
	 * @return bool
	 */
	private function acquire_mutation_lock( $view_id, $operation = '' ) {
		$key       = $this->mutation_key( $view_id );
		$operation = sanitize_key( $operation );

		for ( $attempt = 0; $attempt < self::MUTATION_LOCK_ATTEMPTS; ++$attempt ) {
			if ( WPHelper::insert_option_if_absent( $key, (string) microtime( true ) ) ) {
				if ( $attempt > 0 ) {
					$this->log_mutex_event(
						'Acquired View bulk action lock mutation mutex after waiting.',
						[
							'view_id'   => absint( $view_id ),
							'operation' => $operation,
							'attempts'  => $attempt + 1,
						]
					);
				}

				return true;
			}

			$locked_at = (float) get_option( $key );

			if ( $locked_at > 0 && microtime( true ) - $locked_at > self::MUTATION_LOCK_TTL ) {
				delete_option( $key );
				$this->log_mutex_event(
					'Removed stale View bulk action lock mutation mutex.',
					[
						'view_id'   => absint( $view_id ),
						'operation' => $operation,
						'lock_age'  => microtime( true ) - $locked_at,
					]
				);
				continue;
			}

			usleep( self::MUTATION_LOCK_WAIT_MICROSECONDS );
		}

		$this->log_mutex_event(
			'Could not acquire View bulk action lock mutation mutex.',
			[
				'view_id'    => absint( $view_id ),
				'operation'  => $operation,
				'attempts'   => self::MUTATION_LOCK_ATTEMPTS,
				'wait_micro' => self::MUTATION_LOCK_WAIT_MICROSECONDS,
			]
		);

		return false;
	}

	/**
	 * Logs mutation mutex events.
	 *
	 * @since 3.0.0
	 *
	 * @param string $message Message.
	 * @param array  $context Context.
	 *
	 * @return void
	 */
	private function log_mutex_event( $message, array $context = [] ) {
		if ( ! function_exists( 'gravityview' ) || ! gravityview() || empty( gravityview()->log ) || ! method_exists( gravityview()->log, 'debug' ) ) {
			return;
		}

		gravityview()->log->debug( $message, $context );
	}

	/**
	 * Releases the mutation mutex for this View lock.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View ID.
	 *
	 * @return void
	 */
	private function release_mutation_lock( $view_id ) {
		delete_option( $this->mutation_key( $view_id ) );
	}

	/**
	 * Returns the mutation mutex option key.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View ID.
	 *
	 * @return string
	 */
	private function mutation_key( $view_id ) {
		return self::MUTATION_LOCK_PREFIX . absint( $view_id );
	}

	/**
	 * Normalizes a View or View ID.
	 *
	 * @since 3.0.0
	 *
	 * @param View|int|null $view View or View ID.
	 *
	 * @return int
	 */
	private function view_id( $view ) {
		if ( $view instanceof View ) {
			return (int) $view->ID;
		}

		return absint( $view );
	}
}
