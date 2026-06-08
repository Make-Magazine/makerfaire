<?php
/**
 * Job controller.
 * *
 * @license GPL-2.0-or-later
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Scheduler\Handlers;

use Exception;
use GravityKit\GravityView\Foundation\Scheduler\Models\Job;
use GravityKit\GravityView\Foundation\Scheduler\Models\JobResult;
use GravityKit\GravityView\Foundation\Scheduler\Store\DbStore;

class JobHandler {

	/**
	 * @var ScheduleHandler
	 */
	protected $manager;

	/**
	 * @var Job|null
	 */
	protected $job;

	/**
	 * @var TaskHandler|null
	 * */
	protected $task_handler;

	/**
	 * @var JobHistoryHandler|null
	 * */
	protected $runs_handler;

	/**
	 * @var DbStore
	 * */
	protected $store;


	/**
	 * JobHandler constructor.
	 *
	 * @param ScheduleHandler $manager Job manager object.
	 * @param DbStore         $store   Database store object.
	 */
	public function __construct( ScheduleHandler $manager, DbStore $store ) {
		$this->manager = $manager;
		$this->store   = $store;
	}

	/**
	 * Creates a new job.
	 *
	 * @param string $name Job name.
	 *
	 * @return JobHandler
	 */
	public function create( string $name ): JobHandler {
		$this->job          = new Job( $name );
		$this->task_handler = null;
		$this->runs_handler = null;

		return $this;
	}

	/**
	 * An alias for the schedule_single() method.
	 *
	 * @since 1.12.0
	 *
	 * @param int $timestamp When the task will run.
	 *
	 * @return JobResult Result containing the job ID and any execution warning.
	 * @throws Exception
	 */
	public function schedule( int $timestamp ): JobResult {
		return $this->schedule_single( $timestamp );
	}

	/**
	 * Schedules single job.
	 *
	 * @since 1.12.0
	 *
	 * @param int $timestamp When the task will run.
	 *
	 * @return JobResult Result containing the job ID and any execution warning.
	 * @throws Exception
	 */
	public function schedule_single( int $timestamp ): JobResult {
		$this->check_job();

		return $this->manager->schedule_job_single( $timestamp, $this->job() );
	}

	/**
	 * An alias for the schedule_async() method.
	 * Schedules the job to run once, as soon as possible.
	 *
	 * @since 1.12.0
	 *
	 * @return JobResult Result containing the job ID and any execution warning.
	 * @throws Exception
	 */
	public function async(): JobResult {
		return $this->schedule_async();
	}

	/**
	 * Schedules the job to run once, as soon as possible.
	 *
	 * @since 1.12.0
	 *
	 * @return JobResult Result containing the job ID and any execution warning.
	 * @throws Exception
	 */
	public function schedule_async(): JobResult {
		$this->check_job();

		return $this->manager->schedule_job_async( $this->job );
	}

	/**
	 * The same as async(), but makes an additional request to execute the job immediately.
	 *
	 * @since 1.12.0
	 *
	 * @return JobResult Result containing the job ID and any execution warning.
	 * @throws Exception
	 */
	public function run(): JobResult {
		$this->check_job();

		$result = $this->manager->run_job( $this->job );

		if ( $result->succeeded() ) {
			$data           = $this->job->data();
			$data['job_id'] = $result->job_id();
			$this->job->set_data( $data );
		}

		return $result;
	}

	/**
	 * Schedules a recurring job.
	 *
	 * @since 1.12.0
	 *
	 * @param int $timestamp When the first instance of the job will run.
	 * @param int $interval_in_seconds How long to wait between runs.
	 *
	 * @return JobResult Result containing the job ID and any execution warning.
	 * @throws Exception
	 */
	public function schedule_recurring( int $timestamp, int $interval_in_seconds ): JobResult {
		$this->check_job();

		return $this->manager->schedule_job_recurring( $timestamp, $interval_in_seconds, $this->job );
	}

	/**
	 * Starts the recurring job and makes an additional request to execute the job immediately.
	 *
	 * @since 1.12.0
	 *
	 * @param int $interval_in_seconds How long to wait between runs.
	 *
	 * @return JobResult Result containing the job ID and any execution warning.
	 * @throws Exception
	 */
	public function run_recurring( int $interval_in_seconds ): JobResult {
		$this->check_job();

		return $this->manager->run_job_recurring( $interval_in_seconds, $this->job );
	}

	/**
	 * Schedules a job that recurs on a cron-like schedule.
	 *
	 * @since 1.12.0
	 *
	 * @param int    $timestamp The first instance of the job will be scheduled
	 *                          to run at a time calculated after this timestamp matching the cron
	 *                          expression. This can be used to delay the first instance of the job.
	 * @param string $schedule A cron schedule string.
	 *
	 * @return JobResult Result containing the job ID and any execution warning.
	 * @throws Exception
	 */
	public function schedule_cron( int $timestamp, string $schedule ): JobResult {
		$this->check_job();

		return $this->manager->schedule_job_cron( $timestamp, $schedule, $this->job );
	}

	/**
	 * Unschedules all pending job instances.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 * @throws Exception
	 */
	public function unschedule(): void {
		$this->check_job();

		$this->manager->unschedule_all( $this->job );
	}

	/**
	 * Deletes all pending job instances (physical removal).
	 *
	 * Use this instead of unschedule() when replacing a schedule
	 * (e.g., interval change) so no ghost canceled entries remain
	 * in the Background Jobs UI.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 * @throws Exception
	 */
	public function delete(): void {
		$this->check_job();

		$this->manager->delete_all( $this->job );
	}

	/**
	 * Unschedules the latest job run. Can be useful for non-unique jobs that can schedule multiple runs.
	 *
	 * @since 1.12.0
	 *
	 * @return int|null job instance ID if a scheduled run was found, or null if no matching runs found.
	 * @throws Exception
	 */
	public function unschedule_latest(): ?int {
		$this->check_job();

		return $this->manager->unschedule_latest( $this->job );
	}

	/**
	 * Checks if the job has scheduled instances (pending or in-progress).
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function is_scheduled(): bool {
		$this->check_job();

		return $this->manager->is_scheduled( $this->job );
	}

	/**
	 * Checks if the job is currently paused.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function is_paused(): bool {
		$this->check_job();

		return $this->manager->is_paused( $this->job );
	}

	/**
	 * Pauses all pending job instances.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function pause(): bool {
		$this->check_job();

		return $this->manager->pause( $this->job );
	}

	/**
	 * Unpauses job instances.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function unpause(): bool {
		$this->check_job();

		return $this->manager->unpause( $this->job );
	}

	/**
	 * Gets the job task handler object.
	 *
	 * @since 1.12.0
	 *
	 * @return TaskHandler Task handler object.
	 * @throws Exception
	 */
	public function task(): TaskHandler {
		$this->check_job();

		if ( empty( $this->task_handler ) ) {
			$this->task_handler = new TaskHandler( $this->job, $this->store );
		}

		return $this->task_handler;
	}

	/**
	 * Gets the job history handler.
	 *
	 * @since 1.12.0
	 *
	 * @return JobHistoryHandler Job history handler object.
	 * @throws Exception
	 */
	public function history(): JobHistoryHandler {
		$this->check_job();

		if ( empty( $this->runs_handler ) ) {
			$this->runs_handler = new JobHistoryHandler( $this->job->name(), $this->store );
		}

		return $this->runs_handler;
	}

	/**
	 * Gets the job object.
	 *
	 * @since 1.12.0
	 *
	 * @return Job Job object.
	 */
	public function job(): Job {
		return $this->job;
	}

	/**
	 * Makes sure the job object exists.
	 *
	 * @return void
	 * @throws Exception
	 */
	protected function check_job(): void {
		if ( ! $this->job ) {
			throw new Exception( 'Job is not set. Please use create() method first.' );
		}

		$registered = array_keys( $this->job->registered_tasks() );
		$enqueued   = array_keys( $this->job->enqueued_tasks() );
		$forgotten  = array_diff( $registered, $enqueued );

		if ( $forgotten ) {
			throw new Exception(
				'Tasks registered but never queued: ' . implode( ', ', $forgotten )
				. '. Call ->queue() after ->create(), or remove the unused create() calls.'
			);
		}
	}

	/**
	 * Sets the job label for display in the Background Jobs UI.
	 *
	 * @since 1.12.0
	 *
	 * @param string $label The human-readable job label.
	 *
	 * @return self
	 */
	public function set_label( string $label ): self {
		$this->check_job();

		$this->job->set_label( $label );

		return $this;
	}

	/**
	 * Sets the product text domain that owns this job.
	 *
	 * Used by the execution notice to display which products are affected
	 * when background processing is unavailable.
	 *
	 * @since 1.12.0
	 *
	 * @param string $text_domain Product text domain (e.g., 'gk-gravityexport').
	 *
	 * @return self
	 */
	public function set_product( string $text_domain ): self {
		$this->check_job();

		$this->job->set_product( $text_domain );

		return $this;
	}

	/**
	 * Sets a job data key.
	 *
	 * Useful for passing metadata to the job (not to individual tasks).
	 * Values are serialized to JSON by the storage layer, so arrays,
	 * integers, and booleans are preserved through the round-trip.
	 *
	 * @since 1.12.0
	 *
	 * @param string $key  Data key.
	 * @param mixed  $data Data value.
	 *
	 * @return self
	 */
	public function set_data( string $key, $data ): self {
		$this->check_job();

		$job_data         = $this->job->data();
		$job_data[ $key ] = $data;

		$this->job->set_data( $job_data );

		return $this;
	}
}
