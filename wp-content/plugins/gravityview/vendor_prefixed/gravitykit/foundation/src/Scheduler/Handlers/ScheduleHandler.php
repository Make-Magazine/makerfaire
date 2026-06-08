<?php
/**
 * Job schedule handler.
 * *
 * @license GPL-2.0-or-later
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Scheduler\Handlers;

use ActionScheduler;
use ActionScheduler_Store;
use Exception;
use GravityKit\GravityView\Foundation\Scheduler\Exceptions\TaskException;
use GravityKit\GravityView\Foundation\Scheduler\JobScheduler;
use GravityKit\GravityView\Foundation\Scheduler\Models\AbstractAction;
use GravityKit\GravityView\Foundation\Scheduler\Models\Job;
use GravityKit\GravityView\Foundation\Scheduler\Models\JobInstance;
use GravityKit\GravityView\Foundation\Scheduler\Models\JobProgress;
use GravityKit\GravityView\Foundation\Scheduler\Models\HealthCheck;
use GravityKit\GravityView\Foundation\Scheduler\Models\JobResult;
use GravityKit\GravityView\Foundation\Scheduler\Models\NextRunRules;
use GravityKit\GravityView\Foundation\Scheduler\Models\Task;
use GravityKit\GravityView\Foundation\Scheduler\Store\DbStore;
use GravityKit\GravityView\Foundation\Scheduler\Traits\LoggerTrait;
use Throwable;

class ScheduleHandler {

	use LoggerTrait;

	const TYPE_ASYNC  = 'async';
	const TYPE_SINGLE = 'single';

	/**
	 * Per-request identifier for correlating task runs in the activity log.
	 *
	 * Set once per PHP process. Runs sharing the same ID executed within
	 * one Action Scheduler batch (same HTTP request). A different ID means
	 * a new HTTP request was dispatched.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	private static $request_id;

	/**
	 * Returns a short identifier for the current PHP execution context.
	 *
	 * Stable within one HTTP request (PHP-FPM resets static properties
	 * between requests). Changes when Action Scheduler dispatches a new
	 * loopback request for the next batch.
	 *
	 * Uses the process ID + request start time to produce a deterministic
	 * ID that is unique per request without relying on randomness.
	 *
	 * @since 1.13.0
	 *
	 * @return string 8-character hex ID.
	 */
	public static function get_request_id(): string {
		if ( ! self::$request_id ) {
			$pid              = function_exists( 'getmypid' ) ? ( getmypid() ?: 0 ) : 0;
			$start            = $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime( true );
			self::$request_id = substr( md5( $pid . '|' . $start ), 0, 8 );
		}

		return self::$request_id;
	}

	const TYPE_RECURRING = 'recurring';
	const TYPE_CRON      = 'cron';

	/**
	 * The time threshold (in seconds) after which a job is considered stuck.
	 *
	 * Default: 1200 seconds (20 minutes).
	 *
	 * @since 1.12.0
	 *
	 * @var int
	 */
	const JOB_STUCK_THRESHOLD = 1200;

	/**
	 * The maximum number of retry attempts allowed per task before it is marked as failed.
	 *
	 * Used only on the error path (handle_task_exception). The normal execution
	 * path uses the no-progress watchdog instead.
	 *
	 * @since 1.12.0
	 *
	 * @var int
	 */
	const MAX_TASK_RETRIES = 10;

	/**
	 * Maximum consecutive reruns without args changes before failing the task.
	 *
	 * The no-progress watchdog hashes the task's product-facing args after each
	 * rerun. If the hash is identical for this many consecutive reruns, the task
	 * is considered stuck (returning rerun() without advancing state).
	 *
	 * @since 1.12.0
	 *
	 * @var int
	 */
	const MAX_NO_PROGRESS_RERUNS = 5;

	/**
	 * Returns the job stuck threshold in seconds.
	 *
	 * @since 1.12.0
	 *
	 * @return int The threshold in seconds (default: 1200 = 20 minutes).
	 */
	public static function get_job_stuck_threshold(): int {
		/**
		 * Filters the threshold (in seconds) after which a job is considered stuck.
		 *
		 * @since 1.12.0
		 *
		 * @param int $threshold The stuck threshold in seconds. Default: 1200 (20 minutes).
		 */
		return (int) apply_filters( 'gk/foundation/scheduler/job/stuck-threshold', self::JOB_STUCK_THRESHOLD );
	}

	/**
	 * @var DbStore
	 */
	protected $store;

	/**
	 * Cache for restored JobInstance objects.
	 *
	 * @since 1.12.0
	 *
	 * @var array<int, JobInstance|null>
	 */
	protected $job_cache = [];

	/**
	 * Guard flag to prevent recursive fail_job calls.
	 *
	 * @since 1.12.0
	 *
	 * @var bool
	 */
	protected $failing_job = false;

	/**
	 * RequestHandler object.
	 *
	 * @since 1.12.0
	 *
	 * @var RequestHandler|null
	 * */
	protected $request;

	/**
	 * TaskExecutor object.
	 *
	 * @since 1.12.0
	 *
	 * @var TaskExecutor|null
	 */
	protected $task_executor;

	/**
	 * ScheduleHandler constructor.
	 *
	 * @since 1.12.0
	 *
	 * @param DbStore        $store Task store object.
	 * @param RequestHandler $request_handler RequestHandler object.
	 */
	public function __construct( DbStore $store, RequestHandler $request_handler ) {
		$this->store   = $store;
		$this->request = $request_handler;

		$store->on_instance_persisted( [ $this, 'clear_job_cache' ] );
	}

	/**
	 * Registers all job and task execution callbacks in WordPress.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 * @throws Exception
	 */
	public function register_actions(): void {
		// Register job callbacks in WordPress.
		foreach ( $this->get_scheduled_jobs() as $job ) {
			$this->register_job_action( $job );
		}

		// Register the task executor hook.
		$this->task_executor()->register();

		/**
		 * Fires when all callbacks are registered in WordPress.
		 *
		 * @since 1.12.0
		 *
		 * @param ScheduleHandler $schedule_handler Schedule handler object.
		 */
		do_action( 'gk/foundation/scheduler/callbacks/registered', $this );
	}

	/**
	 * Ensures the callback for a specific AS action is registered.
	 *
	 * Called when jobs were already registered but a new GK action may have
	 * been created after the initial registration (e.g., AS auto-rescheduled
	 * a recurring job in the same batch).
	 *
	 * @since 1.12.0
	 *
	 * @param int $action_id The AS action ID about to execute.
	 *
	 * @return void
	 */
	public function ensure_action_registered( int $action_id ): void {
		$action = ActionScheduler::store()->fetch_action( $action_id );

		if ( ! $action || is_a( $action, 'ActionScheduler_NullAction' ) ) { // @phpstan-ignore booleanNot.alwaysFalse
			return;
		}

		$hook = $action->get_hook();

		if ( has_action( $hook ) ) {
			return;
		}

		// Restore the job from args to get its priority.
		$job = $this->get_job( $action_id );

		if ( ! $job ) {
			return;
		}

		$this->register_job_action( $job );
	}

	/**
	 * Executes the job - schedules the first task via Action Scheduler.
	 *
	 * @since 1.12.0
	 *
	 * @param string $job_name The job name.
	 * @param array  $tasks The tasks data to execute.
	 *
	 * @return void
	 */
	public function execute_job( string $job_name, array $tasks ): void {
		$this->logger()->debug( __METHOD__, compact( 'job_name', 'tasks' ) );

		if ( ! JobScheduler::is_enabled() ) {
			$this->logger()->debug( 'Background processing is disabled. Skipping job: ' . $job_name );

			return;
		}

		if ( empty( $tasks ) ) {
			$this->logger()->error( 'No job tasks found. Job: ' . $job_name );

			return;
		}

		add_action( 'action_scheduler_completed_action', [ $this, 'run_job_tasks' ] );
	}

	/**
	 * Runs the job tasks.
	 * This function is triggered when the job execution is completed, and we know the job ID.
	 *
	 * @since 1.12.0
	 *
	 * @param int $job_id The job ID.
	 *
	 * @return void
	 */
	public function run_job_tasks( int $job_id ) {
		$job = $this->get_job( $job_id );

		// Make sure this is the job instance.
		if ( ! $job ) {
			return;
		}

		// We need to remove the action to prevent it from being triggered again.
		remove_action( 'action_scheduler_completed_action', [ $this, 'run_job_tasks' ] );

		// Check if a sibling instance of the same job is already running.
		// This prevents recurring jobs from piling up when AS auto-reschedules
		// the next instance before the current one's task chain finishes.
		if ( $this->should_skip_overlapping_instance( $job_id, $job ) ) {
			ActionScheduler::logger()->log(
				$job_id,
				'[job_skipped] ' . __( 'Skipped: previous instance still running.', 'gk-gravityview' )
			);

			return;
		}

		// Skip dispatch — AS is still inside process_action() and hasn't called
		// schedule_next_instance() yet. A second runner would race and duplicate
		// the next recurring occurrence.
		$task_id = $this->task_executor()->schedule_task( $job_id, $job->name(), false );

		if ( ! $task_id ) {
			ActionScheduler::logger()->log( $job_id, __( 'Failed to schedule the first task action.', 'gk-gravityview' ) );
			$this->fail_job( $job );

			return;
		}

		// Record the real start time in the job args. This is the authoritative
		// "started at" timestamp for the UI. We cannot rely on last_attempt_gmt
		// because extend_job_timeout() overwrites it with a future heartbeat value.
		$job->set_started_at( time() );
		$this->store()->update_instance_args( $job_id, $job->to_array() );

		// Set the job to RUNNING so the UI shows it as in-progress while tasks
		// execute asynchronously. This is safe because action_scheduler_completed_action
		// fires before schedule_next_instance(), and AS only re-claims pending actions.
		$this->store()->update_instance_status( $job_id, DbStore::STATUS_RUNNING );

		ActionScheduler::logger()->log( $job_id, '[job_started] ' . __( 'Job started.', 'gk-gravityview' ) );
	}

	/**
	 * Checks whether a running sibling instance exists for the same job hook.
	 *
	 * When a recurring job's task chain takes longer than the recurrence interval,
	 * AS auto-reschedules the next instance while the previous is still running.
	 * This detects that overlap and allows the caller to skip the new instance.
	 *
	 * Stuck siblings (expired heartbeat) are ignored so recovery isn't blocked.
	 *
	 * @since 1.12.0
	 *
	 * @param int         $job_id The current job instance ID.
	 * @param JobInstance $job    The current job instance.
	 *
	 * @return bool True if a running sibling exists and this instance should be skipped.
	 */
	protected function should_skip_overlapping_instance( int $job_id, JobInstance $job ): bool {
		$running = $this->store()->get_running_instances( $job->name() );

		if ( empty( $running ) ) {
			return false;
		}

		$stuck_rows = $this->store()->get_stuck_running_jobs();
		$stuck_ids  = [];

		foreach ( $stuck_rows as $row ) {
			$stuck_ids[] = (int) $row->action_id; // @phpstan-ignore property.notFound
		}

		foreach ( $running as $sibling_id => $sibling ) {
			if ( (int) $sibling_id === $job_id ) {
				continue;
			}

			// Sibling is stuck — let recovery handle it, don't block this instance.
			if ( in_array( (int) $sibling_id, $stuck_ids, true ) ) {
				continue;
			}

			/**
			 * Whether to skip a recurring job instance that overlaps with a running sibling.
			 *
			 * @since 1.12.0
			 *
			 * @param bool   $should_skip Whether to skip. Default true.
			 * @param int    $job_id      The current job instance ID.
			 * @param string $job_name    The job hook name.
			 * @param int    $sibling_id  The running sibling's action ID.
			 */
			return (bool) apply_filters( 'gk/foundation/scheduler/recurring/skip-overlap', true, $job_id, $job->name(), (int) $sibling_id );
		}

		return false;
	}

	/**
	 * Skips the task.
	 *
	 * @since 1.12.0
	 *
	 * @param Task        $task The task object.
	 * @param JobInstance $job The job instance.
	 *
	 * @return void
	 */
	public function skip_task( Task $task, JobInstance $job ) {
		$this->logger()->debug(
            __METHOD__,
            [
				'task' => $task->name(),
				'job'  => $job->name(),
			]
        );

		$this->update_progress( $job, $task, Task::STATUS_SKIPPED );

		// translators: [task] is replaced with the task name.
		ActionScheduler::logger()->log( $job->id(), '[task_skipped] ' . strtr( __( 'Skipped task [task].', 'gk-gravityview' ), [ '[task]' => $task->name() ] ) );
	}

	/**
	 * Handles the task exception.
	 *
	 * @since 1.12.0
	 *
	 * @param Throwable   $e The exception object.
	 * @param Task        $task The task object.
	 * @param JobInstance $job The job instance.
	 *
	 * @return void
	 */
	public function handle_task_exception( Throwable $e, Task $task, JobInstance $job ) {
		$this->logger()->error(
			'Task exception.',
			[
				'message' => $e->getMessage(),
				'task'    => $task->name(),
			]
		);

		$status = Task::STATUS_FAILED;

		if ( $e instanceof TaskException && $e->next_run_rules() ) {
			$this->update_next_run_data( $e->next_run_rules(), $job );
			$status = $e->next_run_rules()->should_rerun() ? Task::STATUS_PENDING : Task::STATUS_FAILED;
		}

		// Enforce retry limit to prevent infinite rerun loops.
		if ( Task::STATUS_PENDING === $status ) {
			$max_retries = $this->get_max_task_retries( $task );

			if ( $task->get_retry_count() >= $max_retries ) {
				$this->logger()->warning(
					strtr(
                        'Task [task] exceeded max retries ([max]). Marking as failed.',
                        [
							'[task]' => $task->name(),
							'[max]'  => $max_retries,
						]
                    ),
					[ 'job' => $job->id() ]
				);

				$status = Task::STATUS_FAILED;
			} else {
				$task->increment_retry_count();
				$task->set_meta( 'reruns', (int) ( $task->meta( 'reruns' ) ?? 0 ) + 1 );
			}
		}

		$error_text = $e->getMessage() ?: __( 'unknown error', 'gk-gravityview' );

		// Store the error message in task meta so the serializer can read it
		// directly without parsing translated log messages.
		if ( Task::STATUS_FAILED === $status ) {
			$task->set_meta( 'error', $error_text );
		}

		if ( Task::STATUS_FAILED === $status ) {
			// [task:name] routes to the task's Activity log; [task_failed] tells the
			// serializer to hide it when the task already has a dedicated Error field.
			// [error_raw:...] stores the raw error for machine parsing by get_action_error().
			// translators: [error] is replaced with the error message.
			$msg = '[task:' . $task->name() . '] [task_failed] ' . strtr(
				__( 'Failed: [error].', 'gk-gravityview' ),
				[ '[error]' => $error_text ]
			) . ' [error_raw:' . $error_text . ']';
		} else {
			// translators: [error] is replaced with the error message.
			$msg = '[task:' . $task->name() . '] ' . strtr(
				__( 'Retrying (error: [error]).', 'gk-gravityview' ),
				[ '[error]' => $error_text ]
			);
		}

		ActionScheduler::logger()->log( $job->id(), $msg );

		$this->update_progress( $job, $task, $status );

		if ( Task::STATUS_FAILED === $status ) {
			/**
			 * Fires when a task fails after exhausting retries.
			 *
			 * @since 1.12.0
			 *
			 * @param Task       $task      The failed task.
			 * @param JobInstance $job      The job instance.
			 * @param Throwable  $exception The exception that caused the failure.
			 */
			do_action( 'gk/foundation/scheduler/task/execute/failed', $task, $job, $e );
		}

		// If the task cannot fail and retries are exhausted, stop the job.
		if ( Task::STATUS_FAILED === $status && ! $task->can_fail() ) {
			try {
				$this->fail_job( $job );
			} catch ( \Throwable $fail_e ) {
				// fail_job() re-throws on DB transaction failure. Log and continue
				// so the exception doesn't escape to ActionScheduler's process_action(),
				// which would only mark the TASK action as failed, not the JOB.
				$this->logger()->error(
					'fail_job threw during handle_task_exception — job may be stuck.',
					[
						'job_id' => $job->id(),
						'error'  => $fail_e->getMessage(),
					]
				);

				// Last resort: force status to FAILED directly to prevent deadlock.
				try {
					$this->store()->update_instance_status( $job->id(), \ActionScheduler_Store::STATUS_FAILED );
				} catch ( \Throwable $last_resort_e ) {
					$this->logger()->error(
						'Last-resort status update also failed — job is stuck.',
						[
							'job_id' => $job->id(),
							'error'  => $last_resort_e->getMessage(),
						]
					);
				}
			}
		}
	}

	/**
	 * Executes the task.
	 *
	 * @since 1.12.0
	 *
	 * @param Task        $task The task object.
	 * @param JobInstance $job The job instance.
	 *
	 * @return void
	 */
	public function execute_task( Task $task, JobInstance $job ) {

		// Check if task is already completed to prevent duplicate execution.
		if ( isset( $job->progress()->completed()[ $task->name() ] ) ) {
			$this->logger()->debug( strtr( 'Task [task] already completed, skipping duplicate execution.', [ '[task]' => $task->name() ] ), [ 'job' => $job->id() ] );
			return;
		}

		// Clear alloptions cache so each task sees fresh option values.
		// Without this, stale data persists across multiple tasks in the same request.
		wp_cache_delete( 'alloptions', 'options' );

		// Job data is a common data that is shared between all tasks. Provide it to the task.
		// Automatically inject job_id so tasks always have access to it.
		$job_data = $job->data();
		if ( $job->id() ) {
			$job_data['job_id'] = $job->id();
		}
		$task->set_job_data( $job_data );

		/**
		 * Fires before a task is executed.
		 *
		 * @since 1.12.0
		 *
		 * @param Task        $task The task being executed.
		 * @param JobInstance $job  The job instance.
		 */
		do_action( 'gk/foundation/scheduler/task/execute/before', $task, $job );

		// Mark the task as running and persist to DB so the dashboard shows
		// real-time status instead of "pending" during long-running callbacks.
		$this->update_progress( $job, $task, Task::STATUS_RUNNING );

		$task_prefix = '[task:' . $task->name() . '] ';

		// Only log "Started." on the first attempt. Reruns already have a
		// "Continuing (run N)." or "Retrying (error: ...)" entry
		// that makes a duplicate "Started." redundant noise.
		if ( ! $task->meta( 'reruns' ) && ! $task->get_retry_count() ) {
			$task->set_meta( 'batch_id', self::get_request_id() );

			ActionScheduler::logger()->log(
				$job->id(),
				$task_prefix . __( 'Started.', 'gk-gravityview' )
			);
		}

		$next_rules = $task->execute();

		/**
		 * Fires after a task is executed.
		 *
		 * @since 1.12.0
		 *
		 * @param Task              $task       The task that was executed.
		 * @param JobInstance       $job        The job instance.
		 * @param NextRunRules|null $next_rules The next run rules returned by the task.
		 */
		do_action( 'gk/foundation/scheduler/task/execute/after', $task, $job, $next_rules );

		// If the task returned the next run rules, update the next run.
		if ( $next_rules ) {
			$this->update_next_run_data( $next_rules, $job );
		}

		$status = $next_rules && $next_rules->should_rerun() ? Task::STATUS_PENDING : Task::STATUS_COMPLETED;

		// No-progress watchdog: detect stuck reruns (callback returns rerun()
		// without changing its product-facing args). The error path
		// (handle_task_exception) uses _meta.retries instead.
		if ( Task::STATUS_PENDING === $status ) {
			$task->set_meta( 'reruns', (int) ( $task->meta( 'reruns' ) ?? 0 ) + 1 );

			if ( $this->check_no_progress( $task, $job ) ) {
				return;
			}
		}

		$rerun_count    = (int) ( $task->meta( 'reruns' ) ?? 0 );
		$current_batch  = self::get_request_id();
		$previous_batch = $task->meta( 'batch_id' ) ?? $current_batch;
		$is_new_request = $current_batch !== $previous_batch;

		// Track the batch ID so the next rerun can detect a request boundary.
		$task->set_meta( 'batch_id', $current_batch );

		if ( Task::STATUS_COMPLETED === $status ) {
			$status_msg = __( 'Completed.', 'gk-gravityview' );
		} elseif ( $is_new_request ) {
			$status_msg = strtr( __( 'Run [count] (new request).', 'gk-gravityview' ), [ '[count]' => $rerun_count ] );
		} else {
			$status_msg = strtr( __( 'Run [count].', 'gk-gravityview' ), [ '[count]' => $rerun_count ] );
		}

		// Append compact checkpoint args so the activity log shows what
		// changed between reruns (e.g. {"offset":6000}).
		$checkpoint_args = $next_rules ? $next_rules->next_task_args() : null;

		if ( Task::STATUS_PENDING === $status && $checkpoint_args ) {
			$checkpoint = array_filter(
				$checkpoint_args,
				function ( $key ) {
					return 0 !== strpos( (string) $key, '_' );
				},
				ARRAY_FILTER_USE_KEY
			);

			if ( $checkpoint ) {
				$status_msg .= ' — ' . wp_json_encode( $checkpoint, JSON_UNESCAPED_UNICODE );
			}
		}

		$msg = $task_prefix . $status_msg;

		ActionScheduler::logger()->log( $job->id(), $msg );

		$this->logger()->debug( $msg, [ 'job' => $job->id() ] );

		$this->update_progress( $job, $task, $status );
	}

	/**
	 * Handles the next run rules and data.
	 *
	 * @param NextRunRules $rules The next run rules.
	 * @param JobInstance  $job The job instance.
	 *
	 * @return void
	 */
	protected function update_next_run_data( NextRunRules $rules, JobInstance $job ) {
		$task_to_update = $rules->should_rerun() ? $this->get_running_task( $job ) : $this->get_pending_task( $job );

		$update_job = false;

		// Maybe update the task arguments.
		if ( $task_to_update && $rules->next_task_args() ) {
			$task_to_update->add_args( $rules->next_task_args() );
			$update_job = true;
		}

		// Maybe update the job data.
		if ( ! is_null( $rules->job_data() ) ) {
			$job->set_data( $rules->job_data() );
			$update_job = true;
		}

		// If anything changed, we need to update the job args that store all the data.
		if ( $update_job ) {
			$updated = $this->store()->update_instance_args( $job->id(), $job->to_array() );

			if ( ! $updated ) {
				$this->logger()->error(
					'Failed to persist next run data.',
					[ 'job_id' => $job->id() ]
				);
			}
		}
	}

	/**
	 * Gets the running task.
	 *
	 * @since 1.12.0
	 *
	 * @param JobInstance $job The job instance.
	 *
	 * @return Task|null
	 */
	protected function get_running_task( JobInstance $job ): ?Task {
		$running_tasks = $job->progress()->running();

		$task_name = $running_tasks ? key( $running_tasks ) : null;

		if ( ! $task_name || ! is_string( $task_name ) ) {
			return null;
		}

		return $job->get_task( $task_name );
	}

	/**
	 * Gets the first pending task to run.
	 *
	 * @since 1.12.0
	 *
	 * @param JobInstance $job The job instance.
	 *
	 * @return Task|null
	 */
	public function get_pending_task( JobInstance $job ): ?Task {
		$pending_tasks   = $job->progress()->pending();
		$completed_tasks = $job->progress()->completed();

		$this->logger()->debug(
			'get_pending_task: pending tasks',
			[
				'pending'   => array_keys( $pending_tasks ),
				'completed' => array_keys( $completed_tasks ),
			]
		);

		$pending_task_name = $pending_tasks ? key( $pending_tasks ) : null;

		if ( ! $pending_task_name || ! is_string( $pending_task_name ) ) {
			return null;
		}

		// Double-check: if task is already completed, don't return it.
		if ( isset( $completed_tasks[ $pending_task_name ] ) ) {
			$this->logger()->warning( strtr( 'Task [task] found in both pending and completed — this should not happen.', [ '[task]' => $pending_task_name ] ), [ 'job' => $job->id() ] );
			return null;
		}

		return $job->get_task( $pending_task_name );
	}

	/**
	 * Marks the job as completed.
	 *
	 * @since 1.12.0
	 *
	 * @param JobInstance $job The job instance.
	 *
	 * @return void
	 */
	public function complete_job( JobInstance $job ): void {
		$this->logger()->info(
			'Job completed.',
			[
				'job' => $job->name(),
				'id'  => $job->id(),
			]
		);

		ActionScheduler::logger()->log( $job->id(), '[job_completed] ' . __( 'Job completed.', 'gk-gravityview' ) );

		$this->store()->mark_job_completed( $job->name(), $job->id() );

		// Cancel any remaining task actions for this job.
		$this->task_executor()->cancel_pending_tasks( $job->id(), $job->name() );

		$this->reschedule_if_recurring( $job );

		/**
		 * Fires after a job is marked as completed.
		 *
		 * @since 1.12.0
		 *
		 * @param JobInstance $job The completed job instance.
		 */
		do_action( 'gk/foundation/scheduler/job/completed', $job );
	}

	/**
	 * Schedules the next occurrence if the completed job had a recurring or cron schedule.
	 *
	 * Action Scheduler normally auto-reschedules recurring/cron actions when its
	 * queue runner completes them. However, the Foundation scheduler's task chain
	 * completes asynchronously (via separate task actions), so AS's auto-rescheduling
	 * fires before all tasks finish. If the job was paused/resumed or the original
	 * AS auto-reschedule didn't happen, this ensures the next occurrence is created.
	 *
	 * @since 1.12.0
	 *
	 * @param JobInstance $job The completed job instance.
	 *
	 * @return void
	 */
	protected function reschedule_if_recurring( JobInstance $job ): void {
		$action = ActionScheduler::store()->fetch_action( $job->id() );

		if ( ! $action || is_a( $action, 'ActionScheduler_NullAction' ) ) { // @phpstan-ignore booleanNot.alwaysFalse
			return;
		}

		$schedule = $action->get_schedule();

		if ( ! $schedule instanceof \ActionScheduler_Schedule || ! method_exists( $schedule, 'get_recurrence' ) ) {
			return;
		}

		$recurrence = $schedule->get_recurrence();

		if ( ! $recurrence ) {
			return;
		}

		// Check if AS already created the next occurrence.
		$existing = as_get_scheduled_actions(
			[
				'hook'     => $job->name(),
				'status'   => ActionScheduler_Store::STATUS_PENDING,
				'group'    => DbStore::GROUP_ID,
				'per_page' => 1,
			]
		);

		if ( ! empty( $existing ) ) {
			return;
		}

		// Build clean args with progress reset for the new occurrence.
		$args             = $this->store()->get_job_args( $job->id() );
		$args['progress'] = [];

		if ( $schedule instanceof \ActionScheduler_CronSchedule ) {
			$this->store()->create_job_cron( time(), (string) $recurrence, $job->name(), $args, false );
		} else {
			$this->store()->create_job_recurring( time(), (int) $recurrence, $job->name(), $args, false );
		}

		$this->logger()->info(
			'Rescheduled recurring job.',
			[
				'job' => $job->name(),
				'id'  => $job->id(),
			]
		);
	}

	/**
	 * Updates the job progress.
	 *
	 * @since 1.12.0
	 *
	 * @param JobInstance $job The job object.
	 * @param Task        $task The task object.
	 * @param string      $status The task status.
	 *
	 * @return JobProgress
	 */
	public function update_progress( JobInstance $job, Task $task, string $status ): JobProgress {
		$job->progress()->update_task_status( $task->name(), $status );

		$updated = $this->store()->update_instance_args( $job->id(), $job->to_array() );

		if ( ! $updated ) {
			$this->logger()->error(
				'Failed to persist task progress update.',
				[
					'job_id' => $job->id(),
					'task'   => $task->name(),
					'status' => $status,
				]
			);
		}

		// Re-populate cache with the in-memory job to avoid a redundant DB fetch.
		// update_instance_args fires args_updated which clears the cache entry.
		$this->job_cache[ (int) $job->id() ] = $job;

		return $job->progress();
	}

	/**
	 * Gets a job instance by ID.
	 *
	 * Fetches raw data from DbStore and restores it to a JobInstance object.
	 * Results are cached for the duration of the request.
	 *
	 * @since 1.12.0
	 *
	 * @param int $job_id The job ID.
	 *
	 * @return JobInstance|null The job instance or null if not found.
	 */
	public function get_job( int $job_id ): ?JobInstance {
		if ( array_key_exists( $job_id, $this->job_cache ) ) {
			return $this->job_cache[ $job_id ];
		}

		try {
			$job_args = $this->store->get_job_args( $job_id );

			if ( ! $job_args ) {
				$this->job_cache[ $job_id ] = null;

				return null;
			}

			$job = JobInstance::restore( $job_args, $job_id );
			$job->set_status( $this->store->get_instance_status( $job_id ) );
		} catch ( Exception $e ) {
			$this->logger()->error(
				'Error restoring job.',
				[
					'job_id' => $job_id,
					'error'  => $e->getMessage(),
				]
			);

			$this->job_cache[ $job_id ] = null;

			return null;
		}

		$this->job_cache[ $job_id ] = $job;

		return $job;
	}

	/**
	 * Clears the job cache for a specific job or all jobs.
	 *
	 * @since 1.12.0
	 *
	 * @param int|null $job_id The job ID to clear, or null to clear all.
	 *
	 * @return void
	 */
	public function clear_job_cache( ?int $job_id = null ): void {
		if ( null === $job_id ) {
			$this->job_cache = [];

			return;
		}

		unset( $this->job_cache[ $job_id ] );
	}

	/**
	 * Gets all running jobs as JobInstance objects.
	 *
	 * Fetches raw running instances from DbStore and restores them to JobInstance objects.
	 * Used to monitor job health.
	 *
	 * @since 1.12.0
	 *
	 * @return JobInstance[] Array of running job instances keyed by action ID.
	 */
	public function get_running_jobs(): array {
		$actions = $this->store->get_running_instances();
		$jobs    = [];

		foreach ( $actions as $action_id => $action ) {
			try {
				$args = $action->get_args();
				if ( $args && isset( $args['job'] ) ) {
					$job = JobInstance::restore( $args, $action_id );
					$job->set_status( DbStore::STATUS_RUNNING );
					$jobs[ $action_id ] = $job;
				}
			} catch ( Exception $e ) {
				$this->logger()->error( 'Error restoring running job.', [ 'error' => $e->getMessage() ] );
				continue;
			}
		}

		return $jobs;
	}

	/**
	 * Gets the scheduled jobs.
	 *
	 * @since 1.12.0
	 *
	 * @return JobInstance[]
	 */
	public function get_scheduled_jobs(): array {
		$actions = $this->store->get_pending_instances();
		$jobs    = [];

		// Keyed by job name — each name maps to one canonical pending instance.
		foreach ( $actions as $action_id => $action ) {
			try {
				$args = $action->get_args();
				if ( $args ) {
					$jobs[ $args['job'] ] = JobInstance::restore( $args, $action_id );
				}
			} catch ( Exception $e ) {
				$this->logger()->error( __METHOD__ . ': Error restoring job.', [ 'error' => $e->getMessage() ] );

				continue;
			}
		}

		/**
		 * Filters the list of scheduled job instances before returning.
		 *
		 * @since 1.12.0
		 *
		 * @param JobInstance[] $jobs Job objects.
		 */
		return apply_filters( 'gk/foundation/scheduler/jobs/scheduled', $jobs );
	}

	/**
	 * Filters the task enabled status.
	 *
	 * @since 1.12.0
	 *
	 * @param string $schedule_type The schedule type (single, recurring, etc.).
	 * @param string $name The job name.
	 * @param ?array $args The task args.
	 * @param bool   $unique The Action unique property.
	 * @param int    $priority The Action priority.
	 *
	 * @return bool True if the task is enabled.
	 */
	protected function enabled_filter( string $schedule_type, string $name, ?array $args, bool $unique, int $priority ): bool {
		/**
		 * Modifies the GravityKit Scheduler job enabled status.
		 *
		 * @since 1.12.0
		 *
		 * @param bool   $enable  Whether the job is enabled. Default: true.
		 * @param ?array $args    The job args.
		 * @param bool   $unique  Whether the job is unique.
		 * @param int    $priority The job priority.
		 */
		return apply_filters( 'gk/foundation/scheduler/job/' . $name . '/' . $schedule_type . '/enable', true, $args, $unique, $priority );
	}

	/**
	 * Schedule the job to run once, as soon as possible.
	 *
	 * @since 1.12.0
	 *
	 * @param Job $job The job object.
	 *
	 * @return JobResult Result containing the job ID and any execution warning.
	 */
	public function schedule_job_async( Job $job ): JobResult {
		$job_enabled = $this->enabled_filter( self::TYPE_ASYNC, $job->name(), $job->to_array(), $job->unique(), $job->priority() );

		if ( ! $job_enabled ) {
			return $this->make_result( 0 );
		}

		$this->logger()->debug( __METHOD__, compact( 'job' ) );

		$job_id = $this->store()->create_job_async( $job->name(), $job->to_array(), $job->unique(), $job->priority() );

		return $this->make_result( $job_id, $this->check_health() );
	}

	/**
	 * The same as schedule_job_async(), but makes an additional request to execute the job immediately.
	 *
	 * @since 1.12.0
	 *
	 * @param Job $job The job object.
	 *
	 * @return JobResult Result containing the job ID and any execution warning.
	 */
	public function run_job( Job $job ): JobResult {
		$this->logger()->debug( __METHOD__, compact( 'job' ) );

		$result = $this->schedule_job_async( $job );

		if ( $result->succeeded() ) {
			$this->request()->dispatch();

			// Rebuild result from post-dispatch health. Dispatch may clear a stale
			// loopback failure transient (success) or set a new one (failure).
			$result = $this->make_result( $result->job_id(), $this->check_health() );
		}

		return $result;
	}

	/**
	 * Schedules a single job.
	 *
	 * @since 1.12.0
	 *
	 * @param int $timestamp When the job will run.
	 * @param Job $job The job object.
	 *
	 * @return JobResult Result containing the job ID and any execution warning.
	 */
	public function schedule_job_single( int $timestamp, Job $job ): JobResult {
		$job_enabled = $this->enabled_filter( self::TYPE_SINGLE, $job->name(), $job->to_array(), $job->unique(), $job->priority() );

		if ( ! $job_enabled ) {
			return $this->make_result( 0 );
		}

		$this->logger()->debug( __METHOD__, compact( 'timestamp', 'job' ) );

		$job_id = $this->store()->create_job_single( $timestamp, $job->name(), $job->to_array(), $job->unique(), $job->priority() );

		return $this->make_result( $job_id, $this->check_health() );
	}

	/**
	 * Schedules a recurring job.
	 *
	 * @since 1.12.0
	 *
	 * @param int $timestamp When the first instance of the job will run.
	 * @param int $interval_in_seconds How long to wait between runs.
	 * @param Job $job The job object.
	 *
	 * @return JobResult Result containing the job ID and any execution warning.
	 */
	public function schedule_job_recurring( int $timestamp, int $interval_in_seconds, Job $job ): JobResult {
		$job_enabled = $this->enabled_filter( self::TYPE_RECURRING, $job->name(), $job->to_array(), $job->unique(), $job->priority() );

		if ( ! $job_enabled ) {
			return $this->make_result( 0 );
		}

		$this->logger()->debug( __METHOD__, compact( 'timestamp', 'interval_in_seconds', 'job' ) );

		$job_id = $this->store()->create_job_recurring( $timestamp, $interval_in_seconds, $job->name(), $job->to_array(), $job->unique(), $job->priority() );

		return $this->make_result( $job_id, $this->check_health() );
	}

	/**
	 * The same as schedule_job_recurring(), but starts immediately and makes an additional request to execute the job immediately.
	 *
	 * @since 1.12.0
	 *
	 * @param int $interval_in_seconds How long to wait between runs.
	 * @param Job $job The job object.
	 *
	 * @return JobResult Result containing the job ID and any execution warning.
	 */
	public function run_job_recurring( int $interval_in_seconds, Job $job ): JobResult {
		$this->logger()->debug( __METHOD__, compact( 'interval_in_seconds', 'job' ) );

		$result = $this->schedule_job_recurring( time(), $interval_in_seconds, $job );

		if ( $result->succeeded() ) {
			$this->request()->dispatch();

			// Rebuild result from post-dispatch health. Dispatch may clear a stale
			// loopback failure transient (success) or set a new one (failure).
			$result = $this->make_result( $result->job_id(), $this->check_health() );
		}

		return $result;
	}

	/**
	 * Registers the job action callback.
	 *
	 * @param Job $job Job object.
	 *
	 * @return void
	 */
	protected function register_job_action( Job $job ) {
		add_action( $job->name(), [ $this, 'execute_job' ], $job->priority(), 2 );
	}

	/**
	 * Schedules a job that recurs on a cron-like schedule.
	 *
	 * @since 1.12.0
	 *
	 * @param int    $timestamp The first instance of the task will be scheduled
	 *                      to run at a time calculated after this timestamp matching the cron
	 *                      expression. This can be used to delay the first instance of the job.
	 * @param string $schedule A cron schedule string.
	 * @param Job    $job The job object.
	 *
	 * @return JobResult Result containing the job ID and any execution warning.
	 */
	public function schedule_job_cron( int $timestamp, string $schedule, Job $job ): JobResult {
		$job_enabled = $this->enabled_filter( self::TYPE_CRON, $job->name(), $job->to_array(), $job->unique(), $job->priority() );

		if ( ! $job_enabled ) {
			return $this->make_result( 0 );
		}

		$this->logger()->debug( __METHOD__, compact( 'timestamp', 'schedule', 'job' ) );

		$job_id = $this->store()->create_job_cron( $timestamp, $schedule, $job->name(), $job->to_array(), $job->unique(), $job->priority() );

		return $this->make_result( $job_id, $this->check_health() );
	}

	/**
	 * Pauses a running or pending job instance.
	 *
	 * Changes the instance status to paused and cancels any pending task
	 * actions so the next queued task does not execute while paused.
	 *
	 * @since 1.12.0
	 *
	 * @param JobInstance $job The job instance to pause.
	 *
	 * @return void
	 * @throws \Exception If the status transition is invalid (e.g., job already completed).
	 */
	public function pause_job( JobInstance $job ): void {
		$this->store->pause_job_instance( $job->id() );
		$this->task_executor()->cancel_pending_tasks( $job->id(), $job->name() );

		/**
		 * Fires after a job is paused.
		 *
		 * @since 1.12.0
		 *
		 * @param JobInstance $job The paused job instance.
		 */
		do_action( 'gk/foundation/scheduler/job/paused', $job );
	}

	/**
	 * Resumes a paused job instance.
	 *
	 * If the job was scheduled for a future time, it stays pending and
	 * Action Scheduler will pick it up naturally. Otherwise, a task is
	 * scheduled immediately to resume the task chain.
	 *
	 * @since 1.12.0
	 *
	 * @param JobInstance $job The job instance to resume.
	 *
	 * @return bool True if the job was successfully resumed.
	 * @throws \Exception If the status transition is invalid (e.g., job not paused).
	 */
	public function resume_job( JobInstance $job ): bool {
		$this->store->unpause_job_instance( $job->id() );

		// If scheduled for a future time, let AS pick it up naturally.
		$action   = $this->store->fetch_action( $job->id() );
		$schedule = $action->get_schedule();

		if ( $schedule && method_exists( $schedule, 'get_date' ) && $schedule->get_date() ) {
			if ( $schedule->get_date()->getTimestamp() > time() ) {
				/**
				 * Fires after a paused job is resumed.
				 *
				 * @since 1.12.0
				 *
				 * @param JobInstance $job The resumed job instance.
				 */
				do_action( 'gk/foundation/scheduler/job/resumed', $job );

				return true;
			}
		}

		// Scheduled time passed or job was already running — reschedule immediately.
		$task_id = $this->task_executor()->schedule_task( $job->id(), $job->name() );

		if ( ! $task_id ) {
			return false;
		}

		$this->store->update_instance_status( $job->id(), DbStore::STATUS_RUNNING );

		/** This action is documented above. */
		do_action( 'gk/foundation/scheduler/job/resumed', $job );

		return true;
	}

	/**
	 * Cancels a job instance and removes any pending task actions.
	 *
	 * @since 1.12.0
	 *
	 * @param JobInstance $job The job instance to cancel.
	 *
	 * @return void
	 */
	public function cancel_job( JobInstance $job ): void {
		$this->store->update_instance_status( $job->id(), ActionScheduler_Store::STATUS_CANCELED );

		$this->task_executor()->cancel_pending_tasks( $job->id(), $job->name() );

		/**
		 * Fires after a job is canceled from the UI or programmatically.
		 *
		 * @since 1.12.0
		 *
		 * @param JobInstance $job The canceled job instance.
		 */
		do_action( 'gk/foundation/scheduler/job/canceled', $job );
	}

	/**
	 * Marks a job instance as failed and removes any pending task actions.
	 *
	 * @since 1.12.0
	 *
	 * @param JobInstance $job The job instance to mark as failed.
	 *
	 * @return void
	 * @throws \Throwable If the database transaction fails.
	 */
	public function fail_job( JobInstance $job ): void {
		// Prevent recursive calls (e.g., fail_job throws -> handle_task_exception -> fail_job).
		if ( $this->failing_job ) {
			$this->logger()->warning( 'fail_job called recursively, skipping.', [ 'job' => $job->id() ] );

			return;
		}

		$this->failing_job = true;

		ActionScheduler::logger()->log( $job->id(), '[job_failed] ' . __( 'Job failed.', 'gk-gravityview' ) );

		global $wpdb;

		// Mark running tasks as failed (they were interrupted mid-execution).
		foreach ( array_keys( $job->progress()->running() ) as $task_name ) {
			$job->progress()->update_task_status( $task_name, Task::STATUS_FAILED );
		}

		// Mark pending tasks as skipped — they never ran and never will.
		foreach ( array_keys( $job->progress()->pending() ) as $task_name ) {
			$job->progress()->update_task_status( $task_name, Task::STATUS_SKIPPED );
		}

		// Wrap status + args updates in a transaction so they are atomic.
		// $wpdb->query() returns 0 (not true) for START TRANSACTION because
		// it falls into the "else" branch of wpdb::query() and returns $num_rows.
		// Use strict false-check so 0 is treated as success.
		$tx_started = false !== $wpdb->query( 'START TRANSACTION' );

		try {
			$status_updated = $this->store->update_instance_status( $job->id(), ActionScheduler_Store::STATUS_FAILED );

			if ( ! $status_updated ) {
				$this->logger()->error(
					'fail_job: update_instance_status returned false — job status may not be updated.',
					[ 'job_id' => $job->id() ]
				);
			}

			$this->store()->update_instance_args( $job->id(), $job->to_array() );

			if ( $tx_started ) {
				$wpdb->query( 'COMMIT' );
			}
		} catch ( \Throwable $e ) {
			if ( $tx_started ) {
				$wpdb->query( 'ROLLBACK' );
			}

			$this->logger()->error( 'Transaction failed in fail_job.', [ 'error' => $e->getMessage() ] );
			$this->failing_job = false;

			throw $e;
		}

		$this->failing_job = false;

		// Cancel any remaining task actions for this job.
		$this->task_executor()->cancel_pending_tasks( $job->id(), $job->name() );

		/**
		 * Fires after a job is marked as failed.
		 *
		 * @since 1.12.0
		 *
		 * @param JobInstance $job The failed job instance.
		 */
		do_action( 'gk/foundation/scheduler/job/failed', $job );

		$this->reschedule_if_recurring( $job );
	}

	/**
	 * Retries a failed job by resetting its tasks and restarting the task chain.
	 *
	 * Resets failed tasks to pending and clears their error/retry metadata.
	 * Resets skipped tasks to pending. Then schedules a new AS action to
	 * restart the task chain.
	 *
	 * @since 1.12.0
	 *
	 * @param JobInstance $job The failed job instance to retry.
	 *
	 * @return void
	 * @throws Exception If the job is not in a failed state.
	 */
	public function retry_job( JobInstance $job ): void {
		$status = $this->store()->get_instance_status( $job->id() );

		if ( ActionScheduler_Store::STATUS_FAILED !== $status ) {
			throw new Exception(
				strtr(
					'Cannot retry job [id]: status is [status], expected failed.',
					[
						'[id]'     => $job->id(),
						'[status]' => $status ?? 'unknown',
					]
				)
			);
		}

		// Reset failed tasks to pending and clear error metadata.
		foreach ( array_keys( $job->progress()->failed() ) as $task_name ) {
			$job->progress()->update_task_status( $task_name, Task::STATUS_PENDING );

			$task = $job->get_task( $task_name );

			if ( $task ) {
				$task->set_meta( 'retries', 0 );
				$task->set_meta( 'reruns', 0 );
				$task->set_meta( 'error', null );
				$task->set_meta( 'no_progress', 0 );
				$task->set_meta( 'fingerprint', null );
			}
		}

		// Reset skipped tasks to pending.
		foreach ( array_keys( $job->progress()->skipped() ) as $task_name ) {
			$job->progress()->update_task_status( $task_name, Task::STATUS_PENDING );
		}

		// Persist the reset progress and args.
		$this->store()->update_instance_args( $job->id(), $job->to_array() );

		// Set the job back to running.
		$this->store()->update_instance_status( $job->id(), DbStore::STATUS_RUNNING );

		// Record the retry start time.
		$job->set_started_at( time() );
		$this->store()->update_instance_args( $job->id(), $job->to_array() );

		// Schedule the first task action to restart the chain.
		$this->task_executor()->schedule_task( $job->id(), $job->name() );

		/**
		 * Fires after a failed job is retried.
		 *
		 * @since 1.12.0
		 *
		 * @param JobInstance $job The retried job instance.
		 */
		do_action( 'gk/foundation/scheduler/job/retried', $job );
	}

	/**
	 * Unschedules all pending job actions.
	 *
	 * @since 1.12.0
	 *
	 * @param Job $job The job object.
	 *
	 * @return void
	 */
	public function unschedule_all( Job $job ): void {
		$this->logger()->debug( __METHOD__, compact( 'job' ) );

		$this->store()->unschedule_job( $job->name() );
	}

	/**
	 * Deletes all pending job instances (physical removal).
	 *
	 * Use this instead of unschedule_all() when replacing a schedule
	 * (e.g., interval change) so no ghost canceled entries remain
	 * in the Background Jobs UI.
	 *
	 * @since 1.12.0
	 *
	 * @param Job $job The job object.
	 *
	 * @return void
	 */
	public function delete_all( Job $job ): void {
		$this->logger()->debug( __METHOD__, compact( 'job' ) );

		$this->store()->delete_job( $job->name() );
	}

	/**
	 * Unschedules the latest pending job run.
	 *
	 * @since 1.12.0
	 *
	 * @param Job $job The job object.
	 *
	 * @return int|null Run ID if a pending run was found, or null if no matching run found.
	 */
	public function unschedule_latest( Job $job ): ?int {
		$this->logger()->debug( __METHOD__, compact( 'job' ) );

		return $this->store()->unschedule_latest( $job->name() );
	}

	/**
	 * Checks if the job has scheduled runs (pending or in-progress).
	 *
	 * @since 1.12.0
	 *
	 * @param Job $job The job object.
	 *
	 * @return bool
	 */
	public function is_scheduled( Job $job ): bool {
		return $this->store()->is_scheduled( $job->name(), null );
	}

	/**
	 * Checks if a job is currently paused.
	 *
	 * @since 1.12.0
	 *
	 * @param Job $job The job object.
	 *
	 * @return bool
	 */
	public function is_paused( Job $job ): bool {
		return $this->store()->is_job_paused( $job->name() );
	}

	/**
	 * Pauses all job runs.
	 *
	 * @since 1.12.0
	 *
	 * @param Job $job The job object.
	 *
	 * @return bool
	 */
	public function pause( Job $job ): bool {
		$this->logger()->debug( __METHOD__, compact( 'job' ) );

		return $this->store()->pause_job( $job->name() );
	}

	/**
	 * Unpauses all job runs.
	 *
	 * @since 1.12.0
	 *
	 * @param Job $job The job object.
	 *
	 * @return bool
	 */
	public function unpause( Job $job ): bool {
		$this->logger()->debug( __METHOD__, compact( 'job' ) );

		return $this->store()->unpause_job( $job->name() );
	}

	/**
	 * Gets the maximum number of retry attempts allowed for a task.
	 *
	 * @since 1.12.0
	 *
	 * @param Task $task The task object.
	 *
	 * @return int The max retries.
	 */
	protected function get_max_task_retries( Task $task ): int {
		/**
		 * Filters the maximum number of retry attempts per task.
		 *
		 * @since 1.12.0
		 *
		 * @param int  $max_retries The maximum retries. Default: 10.
		 * @param Task $task        The task object.
		 */
		return (int) apply_filters( 'gk/foundation/scheduler/task/max-retries', self::MAX_TASK_RETRIES, $task );
	}

	/**
	 * Checks if a task is stuck (returning rerun without advancing state).
	 *
	 * Computes a fingerprint of the task's product-facing args (excluding
	 * internal _gk_* keys) and compares to the stored fingerprint. If
	 * identical for MAX_NO_PROGRESS_RERUNS consecutive reruns, the task is
	 * marked as failed.
	 *
	 * @since 1.12.0
	 *
	 * @param Task        $task The task requesting rerun.
	 * @param JobInstance $job  The job instance.
	 *
	 * @return bool True if the task was failed (caller should return early).
	 */
	protected function check_no_progress( Task $task, JobInstance $job ): bool {
		$current_hash = $task->compute_args_fingerprint();
		$stored_hash  = $task->get_progress_hash();

		if ( $current_hash === $stored_hash ) {
			$count = $task->increment_no_progress_count();
		} else {
			$task->reset_no_progress_count();
			$task->set_progress_hash( $current_hash );
			$count = 0;
		}

		$max = $this->get_max_no_progress_reruns( $task );

		if ( $count < $max ) {
			return false;
		}

		$this->logger()->warning(
			strtr(
                'Task [task] returned rerun() [count] times without changing args. Marking as failed.',
                [
					'[task]'  => $task->name(),
					'[count]' => $count,
				]
            ),
			[ 'job' => $job->id() ]
		);

		// translators: [count] is replaced with the number of attempts.
		ActionScheduler::logger()->log(
			$job->id(),
			'[task:' . $task->name() . '] ' . strtr(
				_n(
					'Stopped — no progress after [count] attempt.',
					'Stopped — no progress after [count] attempts.',
					$count,
					'gk-gravityview'
				),
				[ '[count]' => $count ]
			)
		);

		$this->update_progress( $job, $task, Task::STATUS_FAILED );

		if ( ! $task->can_fail() ) {
			$this->fail_job( $job );
		}

		return true;
	}

	/**
	 * Gets the maximum consecutive no-progress reruns before failing a task.
	 *
	 * @since 1.12.0
	 *
	 * @param Task $task The task object.
	 *
	 * @return int The max no-progress reruns.
	 */
	protected function get_max_no_progress_reruns( Task $task ): int {
		/**
		 * Filters the maximum consecutive no-progress reruns per task.
		 *
		 * @since 1.12.0
		 *
		 * @param int  $max  The maximum reruns without progress. Default: 5.
		 * @param Task $task The task object.
		 */
		return (int) apply_filters( 'gk/foundation/scheduler/task/max-no-progress-reruns', self::MAX_NO_PROGRESS_RERUNS, $task );
	}

	/**
	 * Runs the execution health check.
	 *
	 * @since 1.12.0
	 *
	 * @return HealthCheck
	 */
	protected function check_health(): HealthCheck {
		return HealthCheck::run();
	}

	/**
	 * Creates a JobResult with a cancel callback for the given action ID.
	 *
	 * @since 1.12.0
	 *
	 * @param int              $job_id The AS action ID.
	 * @param HealthCheck|null $health Health check result, or null to skip warning.
	 *
	 * @return JobResult
	 */
	protected function make_result( int $job_id, ?HealthCheck $health = null ): JobResult {
		$cancel = null;

		if ( $job_id > 0 ) {
			$cancel = static function () use ( $job_id ) {
				ActionScheduler_Store::instance()->delete_action( $job_id );
			};
		}

		return new JobResult(
			$job_id,
			$health ? $health->message() : null,
			$health ? $health->failure_code() : null,
			$cancel
		);
	}

	/**
	 * Gets TaskStore object.
	 *
	 * @since 1.12.0
	 *
	 * @return DbStore
	 */
	protected function store(): DbStore {
		return $this->store;
	}

	/**
	 * Gets RequestHandler object.
	 *
	 * @since 1.12.0
	 *
	 * @return RequestHandler
	 */
	protected function request(): RequestHandler {
		return $this->request;
	}

	/**
	 * Gets TaskExecutor object.
	 *
	 * @since 1.12.0
	 *
	 * @return TaskExecutor
	 */
	public function task_executor(): TaskExecutor {
		if ( ! $this->task_executor ) {
			$this->task_executor = new TaskExecutor( $this->store, $this, $this->request );
		}

		return $this->task_executor;
	}

	/**
	 * Registers the stale sentinel check on admin page loads.
	 *
	 * @since 1.13.0
	 *
	 * @return void
	 */
	public function register_sentinel_check(): void {
		add_action(
            'admin_init',
            function () {
				TaskExecutor::check_stale_sentinel( $this );
			}
        );
	}
}
