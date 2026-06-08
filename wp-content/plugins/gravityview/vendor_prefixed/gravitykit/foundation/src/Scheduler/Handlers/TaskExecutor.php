<?php
/**
 * Task executor.
 *
 * Each task runs as a separate Action Scheduler action. After a task completes,
 * the next eligible task is scheduled as a new AS action. This lets AS handle
 * time budgeting, loopback chaining, failure detection, and memory monitoring.
 *
 * @license GPL-2.0-or-later
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Scheduler\Handlers;

use ActionScheduler;
use GravityKit\GravityView\Foundation\Scheduler\Models\JobInstance;
use GravityKit\GravityView\Foundation\Scheduler\Models\Task;
use GravityKit\GravityView\Foundation\Scheduler\Store\DbStore;
use GravityKit\GravityView\Foundation\Scheduler\Traits\LoggerTrait;
use GravityKit\GravityView\Foundation\Helpers\Core as CoreHelpers;
use Throwable;

class TaskExecutor {

	use LoggerTrait;

	/**
	 * The Action Scheduler hook name for task execution actions.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	const HOOK = 'gk_scheduler_run_task';

	/**
	 * The Action Scheduler hook name for the recovery check.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	const RECOVERY_HOOK = 'gk_scheduler_maintenance';

	/**
	 * How often (in seconds) the recovery check runs.
	 *
	 * @since 1.12.0
	 *
	 * @var int
	 */
	const RECOVERY_INTERVAL = 120;

	/**
	 * Option name for the task sentinel (dead man's switch).
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	const TASK_SENTINEL = 'gk_scheduler_task_sentinel';

	/**
	 * Hard cap on PHP execution time (seconds) for individual tasks.
	 *
	 * Generous safety net — the cooperative deadline (_meta.deadline) is the
	 * primary time enforcement mechanism. This only fires for CPU-bound
	 * runaways (infinite loops, regex backtracking) that ignore the deadline.
	 *
	 * On Linux, set_time_limit() measures CPU time only — sleep, DB queries,
	 * and HTTP requests do NOT count toward this limit.
	 *
	 * @since 1.12.0
	 *
	 * @var int
	 */
	const TASK_TIME_LIMIT = 300;

	/**
	 * @var DbStore
	 */
	protected $store;

	/**
	 * @var ScheduleHandler
	 */
	protected $schedule_handler;

	/**
	 * @var RequestHandler
	 */
	protected $request;

	/**
	 * Whether recovery scheduling has been confirmed for this request.
	 *
	 * Prevents redundant database queries when schedule_task() is called
	 * multiple times in a single request (e.g., during recovery).
	 *
	 * @since 1.12.0
	 *
	 * @var bool
	 */
	protected $recovery_ensured = false;

	/**
	 * Whether the timeout shutdown handler is armed for the current task.
	 *
	 * @since 1.12.0
	 *
	 * @var bool
	 */
	protected $timeout_armed = false;

	/**
	 * TaskExecutor constructor.
	 *
	 * @since 1.12.0
	 *
	 * @param DbStore         $store            The database store.
	 * @param ScheduleHandler $schedule_handler The schedule handler.
	 * @param RequestHandler  $request          The request handler.
	 */
	public function __construct( DbStore $store, ScheduleHandler $schedule_handler, RequestHandler $request ) {
		$this->store            = $store;
		$this->schedule_handler = $schedule_handler;
		$this->request          = $request;
	}

	/**
	 * Registers the task execution hook callback.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( self::HOOK, [ $this, 'execute' ], 10, 2 );
		add_action( self::RECOVERY_HOOK, [ $this, 'recover_stuck_jobs' ] );
		add_filter( 'action_scheduler_queue_runner_time_limit', [ $this, 'get_queue_time_limit' ] );
	}

	/**
	 * Caps the AS queue runner time limit based on the server's max_execution_time.
	 *
	 * Uses the formula min(max_execution_time, 30) * 0.7, reserving 30% for
	 * overhead (progress saving, scheduling next action, cleanup). Only reduces
	 * the limit — never increases it beyond what AS or other plugins have set.
	 *
	 * @since 1.12.0
	 *
	 * @param int $limit The current queue runner time limit in seconds.
	 *
	 * @return int The (possibly reduced) time limit.
	 */
	public function get_queue_time_limit( int $limit ): int {
		$max_execution_time = (int) ini_get( 'max_execution_time' );

		// Unlimited or unset — use AS default.
		if ( $max_execution_time <= 0 ) {
			return $limit;
		}

		// Cap at 30s to prevent oversized budgets on servers that report
		// very high values (e.g., 210s on PHP-FPM/Valet).
		$effective = min( $max_execution_time, 30 );

		// Reserve 30% for overhead.
		$budget = (int) ( $effective * 0.7 );

		return min( $budget, $limit );
	}

	/**
	 * Ensures the recovery check is scheduled as a recurring AS action.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	protected function ensure_recovery_scheduled(): void {
		if ( $this->recovery_ensured ) {
			return;
		}

		if ( as_next_scheduled_action( self::RECOVERY_HOOK, [], DbStore::TASK_GROUP_ID ) ) {
			$this->recovery_ensured = true;

			return;
		}

		as_schedule_recurring_action(
			time() + self::RECOVERY_INTERVAL,
			self::RECOVERY_INTERVAL,
			self::RECOVERY_HOOK,
			[],
			DbStore::TASK_GROUP_ID
		);

		$this->recovery_ensured = true;
	}

	/**
	 * Recovers jobs stuck by a crashed task process.
	 *
	 * When a PHP process dies mid-task (OOM, host timeout, disable_functions),
	 * no next task gets scheduled and the job is orphaned. This check finds
	 * RUNNING jobs whose timeout has expired and either schedules their next
	 * task or fails them.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	public function recover_stuck_jobs(): void {
		$stuck_jobs = $this->store->get_stuck_running_jobs();

		if ( empty( $stuck_jobs ) ) {
			return;
		}

		foreach ( $stuck_jobs as $row ) {
			/** @var object{action_id: int|string, hook: string} $row */
			$job_id   = (int) $row->action_id;
			$job_name = $row->hook;

			// Check if a task action is already pending/in-progress for this job.
			$has_active_task = as_next_scheduled_action( self::HOOK, [ $job_id, $job_name ], DbStore::TASK_GROUP_ID );

			if ( $has_active_task ) {
				// Task action exists — just extend the timeout.
				$this->extend_job_timeout( $job_id );

				continue;
			}

			// Load the job to check whether any tasks still need execution.
			$job = $this->schedule_handler->get_job( $job_id );

			if ( $job
				&& empty( $job->progress()->pending() )
				&& empty( $job->progress()->running() )
			) {
				// All tasks are done but the job is still RUNNING — finalize it.
				$this->logger()->warning(
					'Recovery: all tasks finished but job still running, finalizing.',
					compact( 'job_id', 'job_name' )
				);

				try {
					if ( ! empty( $job->progress()->failed() ) ) {
						$this->schedule_handler->fail_job( $job );
					} else {
						$this->schedule_handler->complete_job( $job );
					}

					$this->maybe_unschedule_recovery();
				} catch ( Throwable $e ) {
					$this->logger()->error(
						'Recovery: failed to finalize job.',
						[
							'job_id' => $job_id,
							'error'  => $e->getMessage(),
						]
					);
				}

				continue;
			}

			$this->logger()->warning(
				'Recovery: stuck job detected, scheduling next task.',
				compact( 'job_id', 'job_name' )
			);

			// Extend timeout before scheduling to prevent re-detection.
			$this->extend_job_timeout( $job_id );

			$this->schedule_task( $job_id, $job_name );
		}
	}

	/**
	 * Executes the next eligible task for a job.
	 *
	 * Called by Action Scheduler when a task action fires. Loads the job,
	 * finds the next eligible task (respecting dependencies), executes it,
	 * then schedules the next task or completes the job.
	 *
	 * @since 1.12.0
	 *
	 * @param int    $job_id   The job ID.
	 * @param string $job_name The job name.
	 *
	 * @return void
	 */
	public function execute( int $job_id, string $job_name ): void {
		$this->logger()->debug( __METHOD__, compact( 'job_id', 'job_name' ) );

		$job = $this->schedule_handler->get_job( $job_id );

		if ( ! $job ) {
			$this->logger()->error(
				'Task executor: job not found in database.',
				compact( 'job_id', 'job_name' )
			);

			return;
		}

		// Query fresh status from DB — cached status may be stale.
		$current_status = $this->store->get_instance_status( $job_id );

		if ( null === $current_status ) {
			$this->logger()->error(
				'Task executor: job deleted from database.',
				compact( 'job_id', 'job_name' )
			);

			return;
		}

		// Also accept "complete" — AS marks recurring actions complete before
		// Foundation processes them.
		if ( DbStore::STATUS_RUNNING !== $current_status
			&& \ActionScheduler_Store::STATUS_COMPLETE !== $current_status
		) {
			$this->logger()->debug(
				'Task executor: job no longer running, skipping.',
				[
					'job_id' => $job_id,
					'status' => $current_status,
				]
			);

			return;
		}

		// Extend job timeout so AS doesn't mark it as failed while tasks run.
		$this->extend_job_timeout( $job_id );

		// Find next executable task, skipping those with unmet dependencies.
		$task = $this->find_executable_task( $job );

		if ( ! $task ) {
			// If any task failed, the job should be failed — not completed.
			if ( ! empty( $job->progress()->failed() ) ) {
				$this->schedule_handler->fail_job( $job );
			} else {
				$this->schedule_handler->complete_job( $job );
			}

			$this->maybe_unschedule_recovery();

			return;
		}

		$job_failed = false;

		$ok_statuses = [ DbStore::STATUS_RUNNING, \ActionScheduler_Store::STATUS_COMPLETE ];

		try {
			// Layer 2: Hard cap via set_time_limit(). Catches CPU-bound runaways
			// that ignore the cooperative deadline. On Linux this only counts CPU
			// time — sleep/I/O don't count. May be disabled via disable_functions.
			// Skipped under CLI (WP-CLI, system cron) where there is no execution
			// time limit and CPU-heavy tasks may legitimately run for minutes.
			if ( function_exists( 'set_time_limit' ) && ! CoreHelpers::is_cli() && ! CoreHelpers::is_wp_cli() ) {
				@set_time_limit( self::TASK_TIME_LIMIT ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			}

			// Register shutdown handler to detect timeout kills (E_ERROR from
			// set_time_limit) and mark the task as failed in the DB.
			$this->register_timeout_handler( $task, $job );

			// Layer 1: Inject cooperative deadline into task args. Well-behaved
			// callbacks check Core::scheduler()->should_continue() and checkpoint via
			// Core::scheduler()->checkpoint() when time runs low.
			$task->set_meta( 'deadline', microtime( true ) + $this->get_task_time_budget() );

			// Write sentinel before execution. If the process dies, the sentinel
			// persists and is detected on the next admin page load.
			$this->set_task_sentinel( $job_id, $job_name );

			$this->schedule_handler->execute_task( $task, $job );

			// Check if execute_task failed the job (e.g., retry exhaustion on can_fail=false).
			if ( ! in_array( $this->store->get_instance_status( $job_id ), $ok_statuses, true ) ) {
				$job_failed = true;
			}
		} catch ( Throwable $e ) {
			$this->schedule_handler->handle_task_exception( $e, $task, $job );

			// Only treat as job failure if the handler actually failed the job
			// (e.g., retries exhausted on a non-optional task). When the task
			// is marked for retry (STATUS_PENDING), we continue the chain.
			if ( ! in_array( $this->store->get_instance_status( $job_id ), $ok_statuses, true ) ) {
				$job_failed = true;
			}
		}

		// Task finished (success or caught exception) — clear the sentinel.
		$this->clear_task_sentinel();

		// Disarm the timeout handler so it won't falsely fail this task if a
		// later action in the same PHP process hits a timeout.
		$this->disarm_timeout_handler();

		if ( $job_failed ) {
			$this->maybe_unschedule_recovery();

			return;
		}

		// Re-check status after task execution. The user may have paused or
		// cancelled the job while the task was running.
		$current_status = $this->store->get_instance_status( $job_id );

		if ( ! in_array( $current_status, $ok_statuses, true ) ) {
			$this->logger()->debug(
				'Job status changed during task execution, stopping chain.',
				[
					'job_id' => $job_id,
					'status' => $current_status,
				]
			);

			$this->maybe_unschedule_recovery();

			return;
		}

		// Check for more tasks.
		$next = $this->schedule_handler->get_pending_task( $job );

		if ( $next ) {
			$this->schedule_task( $job_id, $job_name );
		} else {
			if ( ! empty( $job->progress()->failed() ) ) {
				$this->schedule_handler->fail_job( $job );
			} else {
				$this->schedule_handler->complete_job( $job );
			}

			$this->maybe_unschedule_recovery();
		}
	}

	/**
	 * Schedules the next task as an AS async action.
	 *
	 * @since 1.12.0
	 *
	 * @param int    $job_id   The job ID.
	 * @param string $job_name The job name.
	 * @param bool   $dispatch Whether to dispatch the AS queue runner. Default true.
	 *
	 * @return int The task action ID. Zero on failure.
	 */
	public function schedule_task( int $job_id, string $job_name, bool $dispatch = true ): int {
		$this->logger()->debug( __METHOD__, compact( 'job_id', 'job_name' ) );

		$this->ensure_recovery_scheduled();

		$action_id = intval(
			as_enqueue_async_action(
				self::HOOK,
				[ $job_id, $job_name ],
				DbStore::TASK_GROUP_ID
			)
		);

		// Skip dispatch when called from run_job_tasks() to avoid racing
		// against AS's schedule_next_instance() for recurring jobs.
		if ( $action_id && $dispatch ) {
			$this->request->dispatch();
		}

		return $action_id;
	}

	/**
	 * Cancels any pending task actions for a job.
	 *
	 * @since 1.12.0
	 *
	 * @param int    $job_id   The job ID.
	 * @param string $job_name The job name.
	 *
	 * @return void
	 */
	public function cancel_pending_tasks( int $job_id, string $job_name ): void {
		$this->logger()->debug( __METHOD__, compact( 'job_id', 'job_name' ) );

		as_unschedule_all_actions( self::HOOK, [ $job_id, $job_name ], DbStore::TASK_GROUP_ID );
	}

	/**
	 * Removes the recovery action when no running jobs remain.
	 *
	 * Called at terminal exit points in execute() where no next task is
	 * scheduled (job completed or failed). Uses hard deletion instead of
	 * cancellation to keep the admin UI clean. Clears the runtime flag
	 * so recovery can be re-scheduled on the next job start.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	protected function maybe_unschedule_recovery(): void {
		if ( $this->store->has_running_jobs() ) {
			return;
		}

		$this->store->remove_actions_by_hook( self::RECOVERY_HOOK );

		$this->recovery_ensured = false;
	}

	/**
	 * Finds the next executable task, skipping those with unmet dependencies.
	 *
	 * Iterates through pending tasks, marking those with unmet dependencies as
	 * skipped, until it finds one whose dependencies are satisfied or runs out.
	 *
	 * @since 1.12.0
	 *
	 * @param JobInstance $job The job instance.
	 *
	 * @return Task|null The next executable task, or null if none remain.
	 */
	protected function find_executable_task( JobInstance $job ): ?Task {
		$completed         = $job->progress()->completed();
		$failed_or_skipped = array_keys( $job->progress()->failed() + $job->progress()->skipped() );
		$pending_names     = array_keys( $job->progress()->pending() );

		foreach ( $pending_names as $task_name ) {
			$task = $job->get_task( $task_name );

			if ( ! $task ) {
				continue;
			}

			if ( $task->meets_dependencies( $completed ) ) {
				return $task;
			}

			// Only skip tasks whose dependencies can never be met (failed/skipped).
			// If deps are pending/running, they may still complete — leave them for later.
			$unmet_deps = array_diff( $task->get_dependencies(), array_keys( $completed ) );

			if ( array_intersect( $unmet_deps, $failed_or_skipped ) ) {
				$this->schedule_handler->skip_task( $task, $job );
			}
		}

		return null;
	}

	/**
	 * Extends the job's heartbeat so it isn't marked stuck while tasks run.
	 *
	 * Writes `time() + stuck_threshold` to `last_attempt_gmt`, pushing it into
	 * the future. The stuck-job detector queries for RUNNING jobs where
	 * `last_attempt_gmt < NOW() - threshold` — a future value never matches,
	 * so the job stays alive. If the process crashes and no task refreshes the
	 * heartbeat, the value eventually expires into the past and recovery kicks in.
	 *
	 * This overwrites `last_attempt_gmt` with a non-historical value. The real
	 * start time is preserved in the job's `_meta.started_at` arg for display purposes.
	 *
	 * @since 1.12.0
	 *
	 * @param int $job_id The job ID.
	 *
	 * @return void
	 */
	protected function extend_job_timeout( int $job_id ): void {
		$future_time = time() + ScheduleHandler::get_job_stuck_threshold();
		$this->store->update_last_attempt( $job_id, $future_time );
	}

	/**
	 * Computes the cooperative time budget for a single task.
	 *
	 * Uses the same formula as the AS queue runner time limit:
	 * min(max_execution_time, 30) * 0.7, with a minimum floor of 10s.
	 * This aligns task checkpointing with the AS runner's expectations.
	 *
	 * @since 1.12.0
	 *
	 * @return float Time budget in seconds.
	 */
	public static function get_task_time_budget(): float {
		$max_execution_time = (int) ini_get( 'max_execution_time' );

		if ( $max_execution_time <= 0 ) {
			// Unlimited or unset — use a sensible default.
			$budget = 25.0;
		} else {
			$effective = min( $max_execution_time, 30 );
			$budget    = $effective * 0.7;
		}

		// Floor at 10s so tasks have a minimum useful time window.
		$budget = max( 10.0, $budget );

		/**
		 * Filters the cooperative time budget for individual tasks.
		 *
		 * @since 1.12.0
		 *
		 * @param float $budget The time budget in seconds.
		 */
		return max( 5.0, (float) apply_filters( 'gk/foundation/scheduler/task/time-budget', $budget ) );
	}

	/**
	 * Registers a shutdown handler that detects timeout kills.
	 *
	 * Handles all PHP E_ERROR fatals: timeouts, OOM kills, and other fatal
	 * errors that bypass catch(Throwable). Marks the task as failed, skips
	 * remaining pending tasks, and fails the entire job.
	 *
	 * The handler is armed via a shared flag that gets disarmed after the task
	 * finishes (success or exception). This prevents false positives when AS's
	 * queue runner executes multiple actions in one PHP process — only the
	 * currently executing task can be affected by the handler.
	 *
	 * Kept minimal — avoid deep object graph operations in shutdown context.
	 *
	 * @since 1.12.0
	 *
	 * @param Task        $task The task being executed.
	 * @param JobInstance $job  The job instance.
	 *
	 * @return void
	 */
	protected function register_timeout_handler( Task $task, JobInstance $job ): void {
		$store     = $this->store;
		$job_id    = $job->id();
		$task_name = $task->name();

		// Armed before task executes, disarmed after (see disarm_timeout_handler).
		// Only the currently executing task should be affected by the handler.
		$this->timeout_armed = true;

		$handler = function () use ( $store, $job_id, $task_name, $job ) {
			if ( ! $this->timeout_armed ) {
				return;
			}

			$error = error_get_last();

			if ( ! $error || E_ERROR !== $error['type'] ) {
				return;
			}

			// Only act if the task is still running. A completed task still has
			// a task object, so checking existence alone would cause false positives.
			$running = $job->progress()->running();

			if ( ! isset( $running[ $task_name ] ) ) {
				return;
			}

			// Build a descriptive error message from the actual PHP fatal.
			$is_timeout = false !== strpos( $error['message'], 'Maximum execution time' );

			if ( $is_timeout ) {
				$error_msg = sprintf( 'Exceeded %ds CPU time limit.', self::TASK_TIME_LIMIT );
			} else {
				$error_msg = sprintf( 'Fatal error: %s in %s:%d', $error['message'], $error['file'], $error['line'] );
			}

			// Mark the sentinel as failed so the next admin page load detects it.
			$this->fail_task_sentinel( $job_id, $job->name(), $error_msg );

			// Store the error in task meta so the serializer can read it directly.
			$task_obj = $job->get_task( $task_name );

			if ( $task_obj ) {
				$task_obj->set_meta( 'error', $error_msg );
			}

			// Mark task as failed in the job's progress.
			$job->progress()->update_task_status( $task_name, Task::STATUS_FAILED );

			// Mark pending tasks as skipped — they will never run.
			foreach ( array_keys( $job->progress()->pending() ) as $pending_task ) {
				$job->progress()->update_task_status( $pending_task, Task::STATUS_SKIPPED );
			}

			// Persist progress and fail the job status atomically.
			$store->update_instance_args( $job_id, $job->to_array() );
			$store->update_instance_status( $job_id, \ActionScheduler_Store::STATUS_FAILED );

			// Log via AS logger for visibility in the admin UI.
			if ( class_exists( 'ActionScheduler' ) ) {
				ActionScheduler::logger()->log(
					$job_id,
					// [task:name] routes to the task's Activity; [task_failed] tells
					// the serializer to hide it when the task has a dedicated Error field.
					// translators: [error] is replaced with the error message.
					'[task:' . $task_name . '] [task_failed] ' . strtr(
						__( 'Killed: [error]', 'gk-gravityview' ),
						[ '[error]' => $error_msg ]
					)
				);

				ActionScheduler::logger()->log(
					$job_id,
					'[job_failed] ' . __( 'Job failed.', 'gk-gravityview' )
				);
			}
		};

		register_shutdown_function( $handler );
	}

	/**
	 * Disarms the timeout shutdown handler after a task finishes.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	protected function disarm_timeout_handler(): void {
		$this->timeout_armed = false;
	}

	/**
	 * Writes the task sentinel before execution begins.
	 *
	 * Uses a raw INSERT ... ON DUPLICATE KEY UPDATE to bypass the options API
	 * and ensure the sentinel is written even under constrained shutdown conditions.
	 *
	 * @since 1.13.0
	 *
	 * @param int    $job_id   The job ID.
	 * @param string $job_name The job name.
	 *
	 * @return void
	 */
	protected function set_task_sentinel( int $job_id, string $job_name ): void {
		global $wpdb;

		$value = maybe_serialize(
            [
				'job_id'   => $job_id,
				'job_name' => $job_name,
				'time'     => time(),
				'pid'      => function_exists( 'getmypid' ) ? ( getmypid() ?: 0 ) : 0,
			]
        );

		$wpdb->query(
            $wpdb->prepare(
                "INSERT INTO {$wpdb->options} (option_name, option_value, autoload) VALUES (%s, %s, 'no')
			ON DUPLICATE KEY UPDATE option_value = VALUES(option_value)",
                self::TASK_SENTINEL,
                $value
            )
        );

		wp_cache_delete( self::TASK_SENTINEL, 'options' );
	}

	/**
	 * Marks the sentinel as failed after a fatal error.
	 *
	 * Called from the shutdown handler when a PHP fatal is detected. Adds the
	 * `failed` flag and error message to the existing sentinel row.
	 *
	 * @since 1.13.0
	 *
	 * @param int    $job_id    The job ID.
	 * @param string $job_name  The job name.
	 * @param string $error_msg The error message from the fatal.
	 *
	 * @return void
	 */
	protected function fail_task_sentinel( int $job_id, string $job_name, string $error_msg ): void {
		global $wpdb;

		$value = maybe_serialize(
            [
				'job_id'   => $job_id,
				'job_name' => $job_name,
				'time'     => time(),
				'failed'   => true,
				'error'    => $error_msg,
			]
        );

		$wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s",
                $value,
                self::TASK_SENTINEL
            )
        );

		wp_cache_delete( self::TASK_SENTINEL, 'options' );
	}

	/**
	 * Clears the task sentinel after successful execution.
	 *
	 * @since 1.13.0
	 *
	 * @return void
	 */
	protected function clear_task_sentinel(): void {
		global $wpdb;

		$wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name = %s",
                self::TASK_SENTINEL
            )
        );

		wp_cache_delete( self::TASK_SENTINEL, 'options' );
	}

	/**
	 * Checks for a stale task sentinel on admin page load.
	 *
	 * Two detection modes:
	 * 1. Failed flag: the shutdown handler ran and set `failed = true` on the
	 *    sentinel before PHP exited. Definitive crash signal — PHP itself
	 *    detected a fatal (memory exhaustion, timeout, uncaught Throwable).
	 * 2. Age timeout: sentinel is older than `max(60, budget * 3)` seconds,
	 *    capped at 600s. Catches SIGKILL-class kills (kernel OOM-killer,
	 *    worker restart, segfault) where the shutdown handler could not run.
	 *    A healthy task clears the sentinel within one budget cycle, so this
	 *    threshold only trips on truly dead processes.
	 *
	 * @since 1.13.0
	 *
	 * @param ScheduleHandler $schedule_handler The schedule handler.
	 *
	 * @return void
	 */
	public static function check_stale_sentinel( ScheduleHandler $schedule_handler ): void {
		$sentinel = get_option( self::TASK_SENTINEL );

		if ( ! $sentinel || ! is_array( $sentinel ) ) {
			return;
		}

		$job_id   = (int) ( $sentinel['job_id'] ?? 0 );
		$job_name = (string) ( $sentinel['job_name'] ?? '' );

		if ( ! $job_id ) {
			delete_option( self::TASK_SENTINEL );

			return;
		}

		// Mode 1: shutdown handler already ran — act immediately.
		if ( ! empty( $sentinel['failed'] ) ) {
			$error = (string) ( $sentinel['error'] ?? __( 'Task failed (sentinel).', 'gk-gravityview' ) );

			self::handle_stale_sentinel( $schedule_handler, $job_id, $error );

			return;
		}

		// Age-based timeout. 3× cooperative budget, floor 60s, ceiling 600s
		// (10 min) so a misconfigured budget filter can't stretch recovery
		// indefinitely. A healthy task clears its sentinel within one budget
		// cycle; anything older than the timeout is presumed dead.
		$age     = time() - (int) ( $sentinel['time'] ?? 0 );
		$budget  = (int) self::get_task_time_budget();
		$timeout = min( 600, max( 60, $budget * 3 ) );

		if ( $age >= $timeout ) {
			$error = strtr(
				__( 'Task did not complete within [seconds]s (sentinel timeout).', 'gk-gravityview' ),
				[ '[seconds]' => $age ]
			);

			self::handle_stale_sentinel( $schedule_handler, $job_id, $error );
		}
	}

	/**
	 * Fails a job detected by a stale sentinel and fires the failed hook.
	 *
	 * @since 1.13.0
	 *
	 * @param ScheduleHandler $schedule_handler The schedule handler.
	 * @param int             $job_id           The job ID.
	 * @param string          $error            The error description.
	 *
	 * @return void
	 */
	private static function handle_stale_sentinel( ScheduleHandler $schedule_handler, int $job_id, string $error ): void {
		delete_option( self::TASK_SENTINEL );

		$job = $schedule_handler->get_job( $job_id );

		if ( ! $job ) {
			return;
		}

		$status = $job->status();

		// Already completed or cancelled — nothing to do.
		if ( in_array( $status, [ \ActionScheduler_Store::STATUS_COMPLETE, \ActionScheduler_Store::STATUS_CANCELED ], true ) ) {
			return;
		}

		// If the shutdown handler already failed the job, just fire the hook.
		if ( \ActionScheduler_Store::STATUS_FAILED !== $status ) {
			// Set error on the running task so products can read it.
			$running_tasks = array_keys( $job->progress()->running() );

			if ( ! empty( $running_tasks ) ) {
				$crashed_task = $job->get_task( $running_tasks[0] );

				if ( $crashed_task && ! $crashed_task->meta( 'error' ) ) {
					$crashed_task->set_meta( 'error', $error );
				}
			}

			try {
				$schedule_handler->fail_job( $job );
			} catch ( Throwable $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
				// fail_job() failed — don't fire hook with inconsistent state.
				return;
			}
		}

		/**
		 * Fires after a job is marked as failed.
		 *
		 * @since 1.12.0
		 *
		 * @param JobInstance $job The failed job instance.
		 */
		do_action( 'gk/foundation/scheduler/job/failed', $job );
	}
}
