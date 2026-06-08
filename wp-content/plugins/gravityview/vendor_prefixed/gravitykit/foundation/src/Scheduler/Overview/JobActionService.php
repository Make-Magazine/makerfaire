<?php
/**
 * Headless job action service.
 *
 * Executes job actions without depending on WP_List_Table or JobAjaxController.
 * Calls ScheduleHandler methods directly and is consumed by both the AJAX
 * controller and WP-CLI commands.
 *
 * @license GPL-2.0-or-later
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Scheduler\Overview;

use ActionScheduler;
use ActionScheduler_Store;
use Exception;
use GravityKit\GravityView\Foundation\Scheduler\Handlers\ScheduleHandler;
use GravityKit\GravityView\Foundation\Scheduler\Store\DbStore;

/**
 * Executes job lifecycle actions.
 *
 * @since 1.12.0
 */
class JobActionService {

	/**
	 * Schedule handler instance.
	 *
	 * @since 1.12.0
	 *
	 * @var ScheduleHandler
	 */
	private $schedule_handler;

	/**
	 * Database store instance.
	 *
	 * @since 1.12.0
	 *
	 * @var DbStore
	 */
	private $store;

	/**
	 * Constructor.
	 *
	 * @since 1.12.0
	 *
	 * @param ScheduleHandler $schedule_handler Schedule handler instance.
	 * @param DbStore         $store            Database store instance.
	 */
	public function __construct( ScheduleHandler $schedule_handler, DbStore $store ) {
		$this->schedule_handler = $schedule_handler;
		$this->store            = $store;
	}

	/**
	 * Cancels a running or pending job.
	 *
	 * @since 1.12.0
	 *
	 * @param int $id The job/action ID.
	 *
	 * @return void
	 * @throws Exception If the job is not found.
	 */
	public function cancel( int $id ): void {
		$job = $this->get_job_or_fail( $id );

		$this->schedule_handler->cancel_job( $job );
	}

	/**
	 * Deletes a job from the store.
	 *
	 * @since 1.12.0
	 *
	 * @param int $id The job/action ID.
	 *
	 * @return void
	 * @throws Exception If the action cannot be deleted.
	 */
	public function delete( int $id ): void {
		try {
			$this->store->delete_action( $id );
		} catch ( \InvalidArgumentException $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// Action was already deleted — treat as success.
		}
	}

	/**
	 * Retries a failed job by resetting tasks and restarting the chain.
	 *
	 * @since 1.12.0
	 *
	 * @param int $id The job/action ID.
	 *
	 * @return void
	 * @throws Exception If the job is not found or not in a failed state.
	 */
	public function retry( int $id ): void {
		$job = $this->get_job_or_fail( $id );

		$this->schedule_handler->retry_job( $job );
	}

	/**
	 * Pauses a running or pending job.
	 *
	 * @since 1.12.0
	 *
	 * @param int $id The job/action ID.
	 *
	 * @return void
	 * @throws Exception If the job is not found or cannot be paused.
	 */
	public function pause( int $id ): void {
		$job = $this->get_job_or_fail( $id );

		$this->schedule_handler->pause_job( $job );
	}

	/**
	 * Resumes a paused job.
	 *
	 * @since 1.12.0
	 *
	 * @param int $id The job/action ID.
	 *
	 * @return void
	 * @throws Exception If the job is not found or resume fails.
	 */
	public function unpause( int $id ): void {
		$job = $this->get_job_or_fail( $id );

		$result = $this->schedule_handler->resume_job( $job );

		if ( ! $result ) {
			throw new Exception(
				strtr( 'Failed to resume job [id].', [ '[id]' => $id ] )
			);
		}
	}

	/**
	 * Executes a job immediately.
	 *
	 * For recurring/cron jobs, creates a one-off copy and leaves the schedule
	 * untouched. For one-time (single/async) jobs, executes the original action.
	 *
	 * @since 1.12.0
	 *
	 * @param int $id The job/action ID.
	 *
	 * @return void
	 * @throws Exception If the job is not found or execution fails.
	 */
	public function run_now( int $id ): void {
		$existing_action = $this->fetch_action_or_fail( $id );

		$is_recurring = $existing_action->get_schedule()->is_recurring();

		if ( ! $is_recurring ) {
			ActionScheduler::runner()->process_action( $id, 'Admin' );

			spawn_cron();

			return;
		}

		// Recurring/cron: create a one-off copy so the schedule stays intact.
		$one_off_id = 0;

		try {
			$one_off_id = (int) as_enqueue_async_action(
				$existing_action->get_hook(),
				$existing_action->get_args(),
				$existing_action->get_group(),
				false,
				$existing_action->get_priority()
			);

			if ( ! $one_off_id ) {
				throw new Exception(
					strtr( 'Failed to create one-off copy for job [id].', [ '[id]' => $id ] )
				);
			}

			ActionScheduler::runner()->process_action( $one_off_id, 'Admin' );
		} catch ( \Throwable $e ) {
			// Clean up the orphaned one-off action.
			if ( $one_off_id ) {
				try {
					ActionScheduler_Store::instance()->delete_action( $one_off_id );
				} catch ( \Throwable $cleanup ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
					// Best-effort cleanup.
				}
			}

			throw new Exception(
				strtr(
					'Failed to execute job [id]: [error]',
					[
						'[id]'    => $id,
						'[error]' => $e->getMessage(),
					]
				)
			);
		}

		spawn_cron();
	}

	/**
	 * Executes a recurring/cron job and fixes the next instance timestamp.
	 *
	 * Only valid for recurring/cron schedules. One-time jobs have nothing
	 * to reschedule.
	 *
	 * @since 1.12.0
	 *
	 * @param int $id The job/action ID.
	 *
	 * @return void
	 * @throws Exception If the job is not found, not recurring, or execution fails.
	 */
	public function run_reschedule( int $id ): void {
		$existing_action = $this->fetch_action_or_fail( $id );

		$schedule = $existing_action->get_schedule();

		if ( ! $schedule->is_recurring() ) {
			throw new Exception(
				strtr( 'Job [id] is not a recurring job.', [ '[id]' => $id ] )
			);
		}

		$recurrence = $schedule->get_recurrence();

		// Only interval-based schedules need the fix-up. Cron schedules return
		// a cron expression string and AS handles their next time correctly.
		$interval = is_numeric( $recurrence ) ? (int) $recurrence : 0;

		// Execute the action normally (AS will call schedule_next_instance).
		ActionScheduler::runner()->process_action( $id, 'Admin' );

		// Fix the next instance timestamp for interval-based schedules.
		if ( $interval > 0 ) {
			$this->fix_next_interval_instance( $existing_action, $interval );
		}

		spawn_cron();
	}

	/**
	 * Loads a job instance or throws if not found.
	 *
	 * @since 1.12.0
	 *
	 * @param int $id The job/action ID.
	 *
	 * @return \GravityKit\GravityView\Foundation\Scheduler\Models\JobInstance
	 * @throws Exception If the job is not found.
	 */
	private function get_job_or_fail( int $id ): \GravityKit\GravityView\Foundation\Scheduler\Models\JobInstance {
		$job = $this->schedule_handler->get_job( $id );

		if ( ! $job ) {
			throw new Exception(
				strtr( 'Job [id] not found.', [ '[id]' => $id ] )
			);
		}

		return $job;
	}

	/**
	 * Fetches an AS action or throws if not found.
	 *
	 * @since 1.12.0
	 *
	 * @param int $id The action ID.
	 *
	 * @return \ActionScheduler_Action
	 * @throws Exception If the action is not found.
	 */
	private function fetch_action_or_fail( int $id ): \ActionScheduler_Action {
		$action = $this->store->fetch_action( $id );

		if ( $action instanceof \ActionScheduler_NullAction ) {
			throw new Exception(
				strtr( 'Action [id] not found.', [ '[id]' => $id ] )
			);
		}

		return $action;
	}

	/**
	 * Fixes the next scheduled instance for an interval-based recurring job.
	 *
	 * When AS auto-reschedules after process_action(), it may use the old
	 * scheduled time as the base instead of now. This deletes the wrongly-timed
	 * next instance and creates one at now + interval.
	 *
	 * @since 1.12.0
	 *
	 * @param \ActionScheduler_Action $existing_action The original recurring action.
	 * @param int                     $interval        Recurrence interval in seconds.
	 *
	 * @return void
	 */
	private function fix_next_interval_instance( \ActionScheduler_Action $existing_action, int $interval ): void {
		$next_actions = as_get_scheduled_actions(
			[
				'hook'     => $existing_action->get_hook(),
				'group'    => $existing_action->get_group(),
				'status'   => ActionScheduler_Store::STATUS_PENDING,
				'per_page' => 1,
				'orderby'  => 'date',
				'order'    => 'DESC',
			]
		);

		if ( empty( $next_actions ) ) {
			return;
		}

		$next_id        = key( $next_actions );
		$next_action    = current( $next_actions );
		$next_scheduled = $next_action->get_schedule()->get_date();
		$correct_time   = time() + $interval;

		// If AS scheduled it at the old time (not now + interval), fix it.
		if ( $next_scheduled && abs( $next_scheduled->getTimestamp() - $correct_time ) > 60 ) {
			$this->store->delete_action( $next_id );

			as_schedule_recurring_action(
				$correct_time,
				$interval,
				$existing_action->get_hook(),
				$existing_action->get_args(),
				$existing_action->get_group(),
				false,
				$existing_action->get_priority()
			);
		}
	}
}
