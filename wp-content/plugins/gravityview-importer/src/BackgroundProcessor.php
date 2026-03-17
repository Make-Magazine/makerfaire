<?php

namespace GravityKit\GravityImport;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use GravityKit\GravityImport\Foundation\Scheduler\Models\HealthCheck;
use GravityKit\GravityImport\Notices\ImportNotices;

/**
 * Background processor for import batches.
 *
 * Bridges GravityImport's Processor to Foundation's Scheduler via the
 * `gravityview/import/has_resources` filter, enabling cooperative time-budgeted
 * background processing without modifying Processor.php.
 *
 * Listens to Foundation's scheduler lifecycle hooks for immediate reaction
 * to job state changes (cancel, pause, resume, fail). The polling fallback
 * in {@see maybe_mark_failed_job()} catches edge cases where hooks didn't fire.
 *
 * @since 2.9.0
 */
class BackgroundProcessor {
	/**
	 * @since 2.9.0
	 *
	 * @var string Scheduler job name.
	 */
	const JOB_NAME = 'gravityimport_import';

	/**
	 * @since 2.9.0
	 *
	 * @var string[] Batch statuses that indicate active processing.
	 */
	const ACTIVE_STATUSES = [ 'process', 'processing', 'parsing' ];

	/**
	 * @since 2.9.0
	 *
	 * @var BackgroundProcessor|null
	 */
	private static $instance;

	/**
	 * Constructor. Registers scheduler lifecycle hooks.
	 *
	 * @since 2.9.0
	 */
	public function __construct() {
		add_action( 'gk/foundation/scheduler/job/canceled', [ $this, 'on_job_canceled' ] );
		add_action( 'gk/foundation/scheduler/job/paused', [ $this, 'on_job_paused' ] );
		add_action( 'gk/foundation/scheduler/job/resumed', [ $this, 'on_job_resumed' ] );
		add_action( 'gk/foundation/scheduler/job/failed', [ $this, 'on_job_failed' ] );
	}

	/**
	 * Returns singleton instance.
	 *
	 * @since 2.9.0
	 *
	 * @return BackgroundProcessor
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	// -------------------------------------------------------------------------
	// Scheduler lifecycle hooks
	// -------------------------------------------------------------------------

	/**
	 * Handles job cancellation (from Foundation UI or programmatic cancel).
	 *
	 * @since 2.9.0
	 *
	 * @param object $job The cancelled JobInstance.
	 */
	public function on_job_canceled( $job ) {
		$batch_id = self::get_batch_id_from_job( $job );

		if ( ! $batch_id ) {
			return;
		}

		Batch::update( [
			'id'     => $batch_id,
			'status' => 'halted',
		] );

		ImportNotices::get_instance()->remove( $batch_id );
	}

	/**
	 * Handles job failure.
	 *
	 * Extracts the error from task metadata when available, then transitions
	 * the batch to 'error' status.
	 *
	 * @since 2.9.0
	 *
	 * @param object $job The failed JobInstance.
	 */
	public function on_job_failed( $job ) {
		$batch_id = self::get_batch_id_from_job( $job );

		if ( ! $batch_id ) {
			return;
		}

		$error = __( 'The background import job failed unexpectedly.', 'gk-gravityimport' );

		try {
			$store      = \GravityKitFoundation::scheduler()->store();
			$task_error = self::get_task_error( $store, $job->id() );

			if ( $task_error ) {
				$error = $task_error;
			}
		} catch ( \Throwable $e ) {
			// Task metadata unavailable — use generic message.
		}

		Batch::update( [
			'id'     => $batch_id,
			'status' => 'error',
			'error'  => $error,
		] );
	}

	/**
	 * Handles job pause (from Foundation UI).
	 *
	 * No batch status change needed — the live notice callback and frontend
	 * polling detect paused state via {@see maybe_mark_failed_job()}.
	 *
	 * @since 2.9.0
	 *
	 * @param object $job The paused JobInstance.
	 */
	public function on_job_paused( $job ) {
		// Intentionally empty. Paused state is read from the scheduler
		// during polling since it's a transient display state, not a
		// batch status change (the job can be resumed).
	}

	/**
	 * Handles job resume (from Foundation UI).
	 *
	 * No batch status change needed — the scheduler automatically picks up
	 * the job and the polling detects progress resuming.
	 *
	 * @since 2.9.0
	 *
	 * @param object $job The resumed JobInstance.
	 */
	public function on_job_resumed( $job ) {
		// Intentionally empty. Resume is handled by the scheduler.
	}

	/**
	 * Extracts batch_id from a scheduler JobInstance.
	 *
	 * Returns null if the job doesn't belong to GravityImport.
	 *
	 * @since 2.9.0
	 *
	 * @param object $job The JobInstance.
	 *
	 * @return int|null Batch ID or null.
	 */
	private static function get_batch_id_from_job( $job ) {
		if ( self::JOB_NAME !== $job->name() ) {
			return null;
		}

		$data     = $job->data();
		$batch_id = $data['batch_id'] ?? null;

		return $batch_id ? (int) $batch_id : null;
	}

	// -------------------------------------------------------------------------
	// Health check
	// -------------------------------------------------------------------------

	/**
	 * Checks whether the background scheduler is available and reliable.
	 *
	 * Uses Foundation's HealthCheck to probe loopback connectivity and
	 * WP-Cron configuration. Returns true only when loopback works —
	 * ALTERNATE_WP_CRON is too unreliable (page-visit-dependent) for imports.
	 *
	 * @since 2.9.0
	 *
	 * @return bool True if background scheduling should be used.
	 */
	public static function is_available() {
		try {
			// Flush cached result so config changes (DISABLE_WP_CRON, loopback) are detected immediately.
			HealthCheck::flush();

			$health = HealthCheck::run();

			return ! $health->has_failure() && ! $health->is_loopback_blocked();
		} catch ( \Throwable $e ) {
			return false;
		}
	}

	// -------------------------------------------------------------------------
	// Polling fallback for stale batch detection
	// -------------------------------------------------------------------------

	/**
	 * Detects stale batches whose scheduler job state has changed.
	 *
	 * This is a polling fallback for cases where the lifecycle hooks didn't
	 * fire (e.g., plugin wasn't loaded when the event occurred). For 'failed'
	 * and 'canceled' jobs it updates the batch status. For 'paused' and
	 * 'pending' it annotates the in-memory batch array for display purposes.
	 *
	 * @since 2.9.0
	 *
	 * @param array $batch The batch data.
	 *
	 * @return array The batch data, possibly updated.
	 */
	public static function maybe_mark_failed_job( $batch ) {
		if ( ! in_array( $batch['status'], self::ACTIVE_STATUSES, true ) ) {
			return $batch;
		}

		$job_id = Batch::get_job_id( $batch['id'] );

		if ( ! $job_id ) {
			return $batch;
		}

		try {
			$scheduler = \GravityKitFoundation::scheduler();

			if ( ! $scheduler ) {
				return $batch;
			}

			$store      = $scheduler->store();
			$job_status = $store->get_instance_status( (int) $job_id );

			// Hooks normally handle 'failed' and 'canceled', but this catches
			// edge cases where the hook didn't fire (e.g., during cron).
			if ( 'failed' === $job_status ) {
				$task_error = self::get_task_error( $store, (int) $job_id );
				$error      = $task_error ?: __( 'The background import job failed unexpectedly.', 'gk-gravityimport' );

				Batch::update( [
					'id'     => $batch['id'],
					'status' => 'error',
					'error'  => $error,
				] );

				$batch['status'] = 'error';
				$batch['error']  = $error;
			} elseif ( 'canceled' === $job_status ) {
				Batch::update( [
					'id'     => $batch['id'],
					'status' => 'halted',
				] );

				$batch['status'] = 'halted';

				ImportNotices::get_instance()->remove( $batch['id'] );
			} elseif ( 'paused' === $job_status ) {
				$batch['scheduler_status'] = 'paused';
			} elseif ( 'pending' === $job_status ) {
				// Job was unpaused but scheduler hasn't picked it up yet.
				$progress  = $batch['progress'] ?? [];
				$processed = ( $progress['processed'] ?? 0 ) + ( $progress['skipped'] ?? 0 ) + ( $progress['error'] ?? 0 );

				if ( $processed > 0 ) {
					$batch['scheduler_status'] = 'pending';
				}
			}
		} catch ( \Throwable $e ) {
			// Scheduler unavailable — leave batch as-is.
		}

		return $batch;
	}

	// -------------------------------------------------------------------------
	// Scheduling and processing
	// -------------------------------------------------------------------------

	/**
	 * Schedules a batch for background processing.
	 *
	 * @since 2.9.0
	 *
	 * @param int $batch_id The batch ID.
	 * @param int $user_id  The user who initiated the import.
	 *
	 * @return array|\WP_Error Schedule result or error.
	 */
	public function schedule( $batch_id, $user_id ) {
		$batch = Batch::get( $batch_id );

		if ( ! $batch ) {
			return new \WP_Error( 'gravityview/import/errors/not_found', __( 'Batch not found.', 'gk-gravityimport' ) );
		}

		// Remove any previous import notices before scheduling a new one.
		ImportNotices::get_instance()->remove_all();

		try {
			$scheduler = \GravityKitFoundation::scheduler();

			$job = $scheduler->job()->create( self::JOB_NAME );
		} catch ( \Throwable $e ) {
			return new \WP_Error( 'gravityview/import/errors/scheduler_unavailable', $e->getMessage() );
		}

		$filename  = $batch['original_filename'] ?? '';
		$form_name = $batch['form_title'] ?? '';

		if ( $filename && $form_name ) {
			$label = sprintf( '%s import to %s', $filename, $form_name );
		} elseif ( $filename ) {
			$label = sprintf( '%s import', $filename );
		} else {
			$label = 'GravityImport';
		}

		$job->set_product( 'gk-gravityimport' );
		$job->set_data( 'label', $label );
		$job->set_data( 'batch_id', $batch_id );
		$job->set_data( 'user_id', $user_id );

		$task_handler = $job->task();
		$task_handler->create(
			'process_batch',
			[ self::class, 'process_task' ],
			[ 'batch_id' => $batch_id ]
		)->set_label( 'Process Batch' )->queue();

		$result = $job->run();

		if ( ! $result->succeeded() ) {
			return new \WP_Error( 'gravityview/import/errors/schedule_failed', __( 'Failed to schedule import.', 'gk-gravityimport' ) );
		}

		$job_id = $result->job_id();

		Batch::set_job_id( $batch_id, $job_id );

		$notice = ImportNotices::get_instance()->add_import_live_notice( $user_id, $job_id, $batch_id );

		return [
			'job_id'    => $job_id,
			'notice_id' => $notice ? $notice->get_slug() : null,
		];
	}

	/**
	 * Scheduler task callback. Processes batch rows within the time budget.
	 *
	 * Hooks into `gravityview/import/has_resources` to relay Foundation's
	 * cooperative time budget into the Processor's resource-checking loop.
	 *
	 * @since 2.9.0
	 *
	 * @param array $args     Task args (includes _meta.deadline from Scheduler).
	 * @param array $job_data Job data including batch_id.
	 *
	 * @return \GravityKit\Foundation\Scheduler\Models\NextRunRules|null Checkpoint to continue, or null when done.
	 */
	public static function process_task( $args, $job_data ) {
		$batch_id = $args['batch_id'] ?? ( $job_data['batch_id'] ?? 0 );

		if ( ! $batch_id ) {
			return null;
		}

		// Hook into Processor's resource check to use Scheduler's time budget.
		$resource_filter = static function ( $result, $processor ) use ( $args ) {
			if ( false === $result ) {
				return false;
			}

			return gk_scheduler_should_continue( $args, 2 );
		};

		add_filter( 'gravityview/import/has_resources', $resource_filter, 10, 2 );

		try {
			$processor = new Processor( [
				'batch_id' => $batch_id,
				'timeout'  => 0,
				'memory'   => 0,
			] );

			$processor->run();
		} catch ( \Throwable $e ) {
			error_log( sprintf(
				'[GravityImport] process_task failed for batch %d: %s in %s:%d',
				$batch_id,
				$e->getMessage(),
				$e->getFile(),
				$e->getLine()
			) );

			throw $e;
		} finally {
			remove_filter( 'gravityview/import/has_resources', $resource_filter, 10 );
		}

		// Determine if processing is complete.
		$batch = Batch::get( $batch_id );

		if ( ! $batch ) {
			return null;
		}

		$terminal_statuses = [ 'done', 'error', 'halted', 'rolledback' ];

		if ( in_array( $batch['status'], $terminal_statuses, true ) ) {
			return null;
		}

		// Not done yet — checkpoint for next run.
		// Include processed count so the scheduler's no-progress watchdog sees changes between runs.
		$progress  = $batch['progress'] ?? [];
		$processed = ( $progress['processed'] ?? 0 ) + ( $progress['skipped'] ?? 0 ) + ( $progress['error'] ?? 0 );

		return gk_scheduler_checkpoint( [ 'processed' => $processed ] );
	}

	// -------------------------------------------------------------------------
	// Import controls (cancel, pause, resume)
	// -------------------------------------------------------------------------

	/**
	 * Cancels a background import.
	 *
	 * Sets the batch status to 'halt' so the Processor stops on its current
	 * tick, then cancels the scheduler job. The `gk/foundation/scheduler/job/canceled`
	 * hook handles transitioning the batch to 'halted' and removing the notice.
	 *
	 * @since 2.9.0
	 *
	 * @param int $batch_id The batch ID.
	 *
	 * @return bool True if cancelled.
	 */
	public function cancel( $batch_id ) {
		$batch = Batch::get( $batch_id );

		if ( ! $batch ) {
			return false;
		}

		// Tell the Processor to stop on next tick.
		Batch::update( [
			'id'     => $batch_id,
			'status' => 'halt',
		] );

		$job = $this->get_scheduler_job( $batch_id );

		if ( is_wp_error( $job ) ) {
			// No scheduler job found — transition directly to terminal status.
			Batch::update( [
				'id'     => $batch_id,
				'status' => 'halted',
			] );

			ImportNotices::get_instance()->remove( $batch_id );

			return true;
		}

		try {
			$scheduler = \GravityKitFoundation::scheduler()->manager();

			// Fires gk/foundation/scheduler/job/canceled → on_job_canceled().
			$scheduler->cancel_job( $job );
		} catch ( \Throwable $e ) {
			// Scheduler unavailable — handle cancellation directly.
			Batch::update( [
				'id'     => $batch_id,
				'status' => 'halted',
			] );

			ImportNotices::get_instance()->remove( $batch_id );
		}

		return true;
	}

	/**
	 * Pauses a background import.
	 *
	 * @since 2.9.0
	 *
	 * @param int $batch_id The batch ID.
	 *
	 * @return bool|\WP_Error True if paused.
	 */
	public function pause( $batch_id ) {
		$job = $this->get_scheduler_job( $batch_id );

		if ( is_wp_error( $job ) ) {
			return $job;
		}

		try {
			$scheduler = \GravityKitFoundation::scheduler()->manager();

			// Fires gk/foundation/scheduler/job/paused → on_job_paused().
			$scheduler->pause_job( $job );
		} catch ( \Throwable $e ) {
			return new \WP_Error( 'gravityview/import/errors/pause_failed', $e->getMessage() );
		}

		return true;
	}

	/**
	 * Resumes a paused background import.
	 *
	 * @since 2.9.0
	 *
	 * @param int $batch_id The batch ID.
	 *
	 * @return bool|\WP_Error True if resumed.
	 */
	public function resume( $batch_id ) {
		$job = $this->get_scheduler_job( $batch_id );

		if ( is_wp_error( $job ) ) {
			return $job;
		}

		try {
			$scheduler = \GravityKitFoundation::scheduler()->manager();

			// Fires gk/foundation/scheduler/job/resumed → on_job_resumed().
			$result = $scheduler->resume_job( $job );

			if ( ! $result ) {
				return new \WP_Error( 'gravityview/import/errors/resume_failed', __( 'Failed to resume import.', 'gk-gravityimport' ) );
			}
		} catch ( \Throwable $e ) {
			return new \WP_Error( 'gravityview/import/errors/resume_failed', $e->getMessage() );
		}

		return true;
	}

	/**
	 * Retrieves the scheduler JobInstance for a batch.
	 *
	 * @since 2.9.0
	 *
	 * @param int $batch_id The batch ID.
	 *
	 * @return object|\WP_Error The JobInstance or error.
	 */
	private function get_scheduler_job( $batch_id ) {
		$job_id = Batch::get_job_id( $batch_id );

		if ( ! $job_id ) {
			return new \WP_Error( 'gravityview/import/errors/no_job', __( 'No background job found for this import.', 'gk-gravityimport' ) );
		}

		try {
			$scheduler = \GravityKitFoundation::scheduler()->manager();
			$job       = $scheduler->get_job( (int) $job_id );

			if ( ! $job ) {
				return new \WP_Error( 'gravityview/import/errors/no_job', __( 'No background job found for this import.', 'gk-gravityimport' ) );
			}

			return $job;
		} catch ( \Throwable $e ) {
			return new \WP_Error( 'gravityview/import/errors/scheduler_unavailable', $e->getMessage() );
		}
	}

	// -------------------------------------------------------------------------
	// Active import detection
	// -------------------------------------------------------------------------

	/**
	 * Returns the most recent active background import.
	 *
	 * Returns the latest batch that is still actively processing via the
	 * Scheduler. This allows the frontend to show progress for active imports.
	 *
	 * @since 2.9.0
	 *
	 * @return array|null Import info or null.
	 */
	public function get_active_import() {
		// Batch::all() status filter uses post_status (always 'draft'),
		// not the batch's actual status stored in post meta. Fetch recent
		// batches and filter by actual status in PHP.
		$batches = Batch::all( [
			'limit' => 10,
			'order' => 'ID',
			'sort'  => 'DESC',
		] );

		if ( empty( $batches ) ) {
			return null;
		}

		foreach ( $batches as $batch ) {
			if ( ! in_array( $batch['status'], self::ACTIVE_STATUSES, true ) ) {
				continue;
			}

			$job_id = Batch::get_job_id( $batch['id'] );

			if ( ! $job_id ) {
				continue;
			}

			// Polling fallback — catches stale batches if hooks didn't fire.
			$batch = self::maybe_mark_failed_job( $batch );

			if ( ! in_array( $batch['status'], self::ACTIVE_STATUSES, true ) ) {
				continue;
			}

			return [
				'batch_id'          => $batch['id'],
				'form_id'           => $batch['form_id'],
				'form_title'        => $batch['form_title'] ?? '',
				'original_filename' => $batch['original_filename'] ?? '',
				'progress'          => $batch['progress'],
				'status'            => $batch['status'],
				'scheduler_status'  => $batch['scheduler_status'] ?? null,
			];
		}

		return null;
	}

	// -------------------------------------------------------------------------
	// Helpers
	// -------------------------------------------------------------------------

	/**
	 * Extracts the error message from a failed scheduler job's task metadata.
	 *
	 * @since 2.9.0
	 *
	 * @param object $store  The scheduler store instance.
	 * @param int    $job_id The job ID.
	 *
	 * @return string|null The error message, or null if not available.
	 */
	private static function get_task_error( $store, $job_id ) {
		try {
			$job_args = $store->get_job_args( $job_id );
			$tasks    = $job_args['tasks'] ?? [];

			foreach ( $tasks as $task ) {
				$error = $task['_meta']['error'] ?? null;

				if ( $error ) {
					return $error;
				}
			}
		} catch ( \Throwable $e ) {
			// Task metadata unavailable.
		}

		return null;
	}
}
