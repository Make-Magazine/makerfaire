<?php
/**
 * GravityView background job scheduler adapter.
 *
 * @package GravityKit\GravityView\BackgroundJobs
 * @since 3.0.0
 */

namespace GravityKit\GravityView\BackgroundJobs;

use GravityKit\GravityView\Entry\BackgroundJobs\EntryBatchJob;
use GravityKit\GravityView\Entry\BackgroundJobs\EntrySelection;
use WP_Error;

/**
 * Thin adapter around Foundation's Scheduler.
 *
 * This class intentionally does not hide Foundation's job model. It centralizes
 * GravityView naming and product attribution, then leaves advanced job control
 * to Foundation.
 *
 * @since 3.0.0
 */
class Scheduler {
	public const PRODUCT = 'gk-gravityview';

	public const JOB_PREFIX = 'gravityview';

	/**
	 * Singleton instance.
	 *
	 * @since 3.0.0
	 *
	 * @var self|null
	 */
	private static $instance;

	/**
	 * Returns the shared adapter instance.
	 *
	 * @since 3.0.0
	 *
	 * @return self
	 */
	public static function instance(): self {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Returns whether Foundation's scheduler is available.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function is_available(): bool {
		return class_exists( '\GravityKitFoundation' ) && is_callable( [ '\GravityKitFoundation', 'scheduler' ] );
	}

	/**
	 * Returns whether Foundation background processing is enabled.
	 *
	 * This mirrors Foundation's global Background Processing setting and its
	 * `gk/foundation/scheduler/enabled` filter.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		$scheduler = $this->foundation();

		if ( is_wp_error( $scheduler ) ) {
			return false;
		}

		return $this->is_scheduler_enabled( $scheduler );
	}

	/**
	 * Returns Foundation's scheduler instance.
	 *
	 * @since 3.0.0
	 *
	 * @return object|WP_Error
	 */
	public function foundation() {
		if ( ! $this->is_available() ) {
			return new WP_Error(
				'gravityview_background_scheduler_unavailable',
				__( 'Background processing is not available.', 'gk-gravityview' )
			);
		}

		return \GravityKitFoundation::scheduler();
	}

	/**
	 * Returns whether jobs can be dispatched asynchronously.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $fresh Whether to refresh Foundation's health probe.
	 *
	 * @return bool
	 */
	public function can_dispatch( bool $fresh = false ): bool {
		$scheduler = $this->foundation();

		if ( is_wp_error( $scheduler ) || ! is_callable( [ $scheduler, 'can_dispatch' ] ) ) {
			return false;
		}

		return (bool) $scheduler->can_dispatch( $fresh );
	}

	/**
	 * Returns a dispatch error when background processing cannot run jobs now.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $fresh Whether to refresh Foundation's health probe.
	 *
	 * @return WP_Error|null
	 */
	public function dispatch_error( bool $fresh = false ): ?WP_Error {
		if ( ! $this->is_available() ) {
			return new WP_Error(
				'gravityview_background_scheduler_unavailable',
				__( 'Background processing is not available.', 'gk-gravityview' )
			);
		}

		$scheduler = $this->foundation();

		if ( is_wp_error( $scheduler ) ) {
			return $scheduler;
		}

		if ( ! $this->is_scheduler_enabled( $scheduler ) ) {
			return new WP_Error(
				'gravityview_background_scheduler_disabled',
				__( 'Background processing is disabled.', 'gk-gravityview' )
			);
		}

		if ( ! is_callable( [ $scheduler, 'can_dispatch' ] ) ) {
			return new WP_Error(
				'gravityview_background_scheduler_dispatch_unavailable',
				__( 'Background processing dispatch checks are not available.', 'gk-gravityview' )
			);
		}

		if ( $scheduler->can_dispatch( $fresh ) ) {
			return null;
		}

		$message = __( 'Background processing is enabled but cannot currently dispatch jobs.', 'gk-gravityview' );

		if ( is_callable( [ $scheduler, 'health' ] ) ) {
			$health = $scheduler->health( $fresh );

			if ( ! is_wp_error( $health ) && is_callable( [ $health, 'message' ] ) && $health->message() ) {
				$message = (string) $health->message();
			}
		}

		return new WP_Error(
			'gravityview_background_scheduler_cannot_dispatch',
			$message
		);
	}

	/**
	 * Returns Foundation scheduler health details.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $fresh Whether to refresh Foundation's health probe.
	 *
	 * @return object|WP_Error
	 */
	public function health( bool $fresh = false ) {
		$scheduler = $this->foundation();

		if ( is_wp_error( $scheduler ) ) {
			return $scheduler;
		}

		if ( ! is_callable( [ $scheduler, 'health' ] ) ) {
			return new WP_Error(
				'gravityview_background_scheduler_health_unavailable',
				__( 'Background processing health checks are not available.', 'gk-gravityview' )
			);
		}

		return $scheduler->health( $fresh );
	}

	/**
	 * Creates a Foundation job handler with GravityView defaults.
	 *
	 * @since 3.0.0
	 *
	 * @param string $name    Job name. The `gravityview_` prefix is added when missing.
	 * @param string $label   Human-readable job label.
	 * @param string $product Product text domain for Foundation attribution.
	 * @param array  $data    Job-level data.
	 *
	 * @return object|WP_Error
	 */
	public function create_job( string $name, string $label = '', string $product = self::PRODUCT, array $data = [] ) {
		$scheduler = $this->foundation();

		if ( is_wp_error( $scheduler ) ) {
			return $scheduler;
		}

		try {
			$job = $scheduler->job()->create( $this->normalize_job_name( $name ) );

			if ( '' !== $label ) {
				$job->set_label( $label );
			}

			if ( '' !== $product ) {
				$job->set_product( $product );
			}

			foreach ( $data as $key => $value ) {
				$job->set_data( (string) $key, $value );
			}

			return $job;
		} catch ( \Throwable $e ) {
			return new WP_Error(
				'gravityview_background_job_create_failed',
				$e->getMessage()
			);
		}
	}

	/**
	 * Runs a Foundation job handler.
	 *
	 * @since 3.0.0
	 *
	 * @param object $job Foundation job handler.
	 *
	 * @return object|WP_Error
	 */
	public function run( $job ) {
		if ( ! is_object( $job ) || ! is_callable( [ $job, 'run' ] ) ) {
			return new WP_Error(
				'gravityview_background_job_invalid',
				__( 'The background job could not be scheduled.', 'gk-gravityview' )
			);
		}

		try {
			return $job->run();
		} catch ( \Throwable $e ) {
			return new WP_Error(
				'gravityview_background_job_schedule_failed',
				$e->getMessage()
			);
		}
	}

	/**
	 * Cancels a Foundation background job.
	 *
	 * @since 3.0.0
	 *
	 * @param int $job_id Foundation job/action ID.
	 *
	 * @return true|WP_Error
	 */
	public function cancel( int $job_id ) {
		if ( $job_id <= 0 ) {
			return new WP_Error(
				'gravityview_background_job_cancel_invalid',
				__( 'The background job could not be canceled.', 'gk-gravityview' )
			);
		}

		$scheduler = $this->foundation();

		if ( is_wp_error( $scheduler ) ) {
			return $scheduler;
		}

		if ( ! is_callable( [ $scheduler, 'manager' ] ) ) {
			return new WP_Error(
				'gravityview_background_job_cancel_unavailable',
				__( 'Background job cancellation is not available.', 'gk-gravityview' )
			);
		}

		try {
			$manager = $scheduler->manager();

			if ( ! is_callable( [ $manager, 'get_job' ] ) || ! is_callable( [ $manager, 'cancel_job' ] ) ) {
				return new WP_Error(
					'gravityview_background_job_cancel_unavailable',
					__( 'Background job cancellation is not available.', 'gk-gravityview' )
				);
			}

			$job = $manager->get_job( $job_id );

			if ( ! is_object( $job ) ) {
				return new WP_Error(
					'gravityview_background_job_missing',
					strtr(
						/* translators: [id] is the background job ID. */
						__( 'Job [id] not found.', 'gk-gravityview' ),
						[
							'[id]' => (string) $job_id,
						]
					)
				);
			}

			$manager->cancel_job( $job );

			if ( is_callable( [ $manager, 'clear_job_cache' ] ) ) {
				$manager->clear_job_cache( $job_id );
			}
		} catch ( \Throwable $e ) {
			if ( $this->is_missing_job_message( $e->getMessage() ) ) {
				return new WP_Error(
					'gravityview_background_job_missing',
					$e->getMessage()
				);
			}

			return new WP_Error(
				'gravityview_background_job_cancel_failed',
				$e->getMessage()
			);
		}

		return true;
	}

	/**
	 * Returns the current Foundation status for a scheduled job.
	 *
	 * @since 3.0.0
	 *
	 * @param int $job_id Foundation job/action ID.
	 *
	 * @return string|WP_Error|null Status string, null when the job is not found, or WP_Error on lookup failure.
	 */
	public function get_job_status( int $job_id ) {
		if ( $job_id <= 0 ) {
			return new WP_Error(
				'gravityview_background_job_status_invalid',
				__( 'The background job status could not be checked.', 'gk-gravityview' )
			);
		}

		$scheduler = $this->foundation();

		if ( is_wp_error( $scheduler ) ) {
			return $scheduler;
		}

		try {
			if ( is_callable( [ $scheduler, 'manager' ] ) ) {
				$job = $scheduler->manager()->get_job( $job_id );

				if ( is_object( $job ) && is_callable( [ $job, 'status' ] ) ) {
					$status = $job->status();

					return is_string( $status ) ? $status : null;
				}
			}

			if ( is_callable( [ $scheduler, 'store' ] ) ) {
				$store = $scheduler->store();

				if ( is_object( $store ) && is_callable( [ $store, 'get_instance_status' ] ) ) {
					return $store->get_instance_status( $job_id );
				}
			}
		} catch ( \Throwable $e ) {
			return new WP_Error(
				'gravityview_background_job_status_failed',
				$e->getMessage()
			);
		}

		return null;
	}

	/**
	 * Returns whether an error message identifies a deleted or missing Foundation job.
	 *
	 * @since 3.0.0
	 *
	 * @param string $message Error message.
	 *
	 * @return bool
	 */
	private function is_missing_job_message( string $message ): bool {
		return (bool) preg_match( '/^Job [0-9]+ not found\\.?$/', trim( $message ) );
	}

	/**
	 * Returns whether the active Foundation scheduler instance is enabled.
	 *
	 * @since 3.0.0
	 *
	 * @param object $scheduler Foundation scheduler instance.
	 *
	 * @return bool
	 */
	private function is_scheduler_enabled( $scheduler ): bool {
		$scheduler_class = is_object( $scheduler ) ? get_class( $scheduler ) : '';

		if ( $scheduler_class && is_callable( [ $scheduler_class, 'is_enabled' ] ) ) {
			return (bool) $scheduler_class::is_enabled();
		}

		if ( is_callable( [ $scheduler, 'can_dispatch' ] ) ) {
			return (bool) $scheduler->can_dispatch( false );
		}

		return false;
	}

	/**
	 * Schedules a View entry-batch job.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View       $view      View whose result set should be processed.
	 * @param EntrySelection $selection Entry selection descriptor.
	 * @param array          $args      Entry batch job options.
	 *
	 * @return array|WP_Error
	 */
	public function schedule_entry_batch( \GV\View $view, EntrySelection $selection, array $args ) {
		return EntryBatchJob::schedule( $view, $selection, $args, $this );
	}

	/**
	 * Normalizes a job name for Foundation.
	 *
	 * @since 3.0.0
	 *
	 * @param string $name Raw job name.
	 *
	 * @return string
	 */
	public function normalize_job_name( string $name ): string {
		$name = strtolower( trim( str_replace( '-', '_', $name ) ) );
		$name = preg_replace( '/[^a-z0-9_]+/', '_', $name );
		$name = trim( (string) $name, '_' );

		if ( '' === $name ) {
			$name = 'entry_batch';
		}

		if ( 0 !== strpos( $name, self::JOB_PREFIX . '_' ) ) {
			$name = self::JOB_PREFIX . '_' . $name;
		}

		return $name;
	}
}
