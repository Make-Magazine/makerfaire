<?php
/**
 * Scheduler Job class.
 * */

namespace GravityKit\GravityView\Foundation\Scheduler\Models;

use Exception;
use OverflowException;

class Job extends AbstractAction {

	/**
	 * @var Task[]
	 */
	protected $registered_tasks = [];

	/**
	 * @var Task[]
	 */
	protected $enqueued_tasks = [];

	/**
	 * @var array Stores the additional job data.
	 */
	protected $data = [];

	/**
	 * @var JobProgress|null Stores the progress data.
	 */
	protected $progress;

	/**
	 * Internal metadata for scheduler bookkeeping.
	 *
	 * Serialized as `_meta` in the job args. Keys include:
	 * - `started_at`: Unix timestamp when the job started running.
	 *
	 * @since 1.12.0
	 *
	 * @var array
	 */
	protected $meta = [];


	/**
	 * Register a task before adding.
	 *
	 * @param Task $task Task object.
	 *
	 * @return void
	 */
	public function register_task( Task $task ) {
		$this->registered_tasks[ $task->name() ] = $task;
	}

	/**
	 * Add a task to the job.
	 *
	 * @since 1.12.0
	 *
	 * @param Task $task Task object.
	 *
	 * @return void
	 *
	 * @throws OverflowException When the maximum task count is exceeded.
	 */
	public function enqueue_task( Task $task ) {
		/**
		 * Filters the maximum number of tasks allowed per job.
		 *
		 * @since 1.12.0
		 *
		 * @param int $max_tasks Maximum task count. Default 1000.
		 * @param Job $job       The job instance.
		 */
		$max_tasks = (int) apply_filters( 'gk/foundation/scheduler/job/max-tasks', 1000, $this );

		if ( count( $this->enqueued_tasks ) >= $max_tasks ) {
			throw new OverflowException(
				strtr(
                    'Job "[job]" exceeded maximum task count of [max].',
                    [
						'[job]' => $this->name(),
						'[max]' => $max_tasks,
					]
                )
			);
		}

		$this->enqueued_tasks[ $task->name() ] = $task;
		$this->progress()->add_pending( $task->name() );
	}

	/**
	 * If the task is registered.
	 *
	 * @param string $task_name Task name.
	 *
	 * @return bool
	 */
	public function registered( string $task_name ): bool {
		return array_key_exists( $task_name, $this->registered_tasks );
	}

	/**
	 * If the task is enqueued.
	 *
	 * @param string $task_name Task name.
	 *
	 * @return bool
	 */
	public function enqueued( string $task_name ): bool {
		return array_key_exists( $task_name, $this->enqueued_tasks );
	}

	/**
	 * Gets registered tasks.
	 *
	 * @return Task[]
	 */
	public function registered_tasks(): array {
		return $this->registered_tasks;
	}

	/**
	 * Gets enqueued tasks.
	 *
	 * @return Task[]
	 */
	public function enqueued_tasks(): array {
		return $this->enqueued_tasks;
	}

	/**
	 * Gets registered task by name.
	 *
	 * @param string $task_name Task name.
	 *
	 * @return Task|null
	 */
	public function get_task( string $task_name ): ?Task {
		return $this->registered_tasks[ $task_name ] ?? null;
	}

	/**
	 * Prepared the job data to be stored in the database.
	 *
	 * @since 1.12.0
	 *
	 * @return array
	 */
	public function to_array(): array {
		$tasks = [];

		foreach ( $this->enqueued_tasks() as $task ) {
			$tasks[ $task->name() ] = $task->to_array();
		}

		$result = [
			'job'      => $this->name(),
			'tasks'    => $tasks,
			'data'     => $this->data(),
			'progress' => $this->progress()->to_array(),
		];

		if ( $this->label() ) {
			$result['label'] = $this->label();
		}

		if ( $this->product() ) {
			$result['product'] = $this->product();
		}

		if ( ! empty( $this->meta ) ) {
			$result['_meta'] = $this->meta;
		}

		return $result;
	}

	/**
	 * Checks if the job is unique.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public function unique(): bool {
		/**
		 * Filters the job unique mode.
		 *
		 * @since 1.12.0
		 *
		 * @param bool $is_unique Whether the job is unique.
		 * @param Job  $instance  The job object.
		 */
		return apply_filters( 'gk/foundation/scheduler/job/' . $this->name() . '/unique', $this->unique, $this );
	}

	/**
	 * Gets the job priority.
	 *
	 * @since 1.12.0
	 *
	 * @return int
	 */
	public function priority(): int {
		/**
		 * Filters the job priority.
		 *
		 * @since 1.12.0
		 *
		 * @param int $priority The job priority.
		 * @param Job $instance The job object.
		 */
		return apply_filters( 'gk/foundation/scheduler/job/' . $this->name() . '/priority', $this->priority, $this );
	}

	/**
	 * Gets the job data.
	 *
	 * @since 1.12.0
	 *
	 * @return array
	 */
	public function data(): array {
		/**
		 * Filters the job data.
		 *
		 * @since 1.12.0
		 *
		 * @param array $data     The job data.
		 * @param Job   $instance The job object.
		 */
		return apply_filters( 'gk/foundation/scheduler/job/' . $this->name() . '/data', $this->data, $this );
	}

	/**
	 * Gets the job progress.
	 *
	 * @since 1.12.0
	 *
	 * @return JobProgress
	 */
	public function progress(): JobProgress {
		if ( ! $this->progress ) {
			$this->progress = JobProgress::restore( [] );
		}

		/**
		 * Filters the job progress.
		 *
		 * @since 1.12.0
		 *
		 * @param JobProgress $progress The job progress object.
		 * @param Job         $instance The job object.
		 */
		return apply_filters( 'gk/foundation/scheduler/job/' . $this->name() . '/progress', $this->progress, $this );
	}

	/**
	 * Gets the Unix timestamp when the job started running.
	 *
	 * @since 1.12.0
	 *
	 * @return int|null Unix timestamp, or null if not yet started.
	 */
	public function started_at(): ?int {
		$value = $this->meta( 'started_at' );

		return null !== $value ? (int) $value : null;
	}

	/**
	 * Records when the job started running.
	 *
	 * @since 1.12.0
	 *
	 * @param int $timestamp Unix timestamp.
	 *
	 * @return void
	 */
	public function set_started_at( int $timestamp ): void {
		$this->set_meta( 'started_at', $timestamp );
	}

	/**
	 * Reads a value from the _meta internal data array.
	 *
	 * @since 1.12.0
	 *
	 * @param string $key The sub-key within _meta.
	 *
	 * @return mixed|null The value, or null if not set.
	 */
	public function meta( string $key ) {
		return $this->meta[ $key ] ?? null;
	}

	/**
	 * Writes a value to the _meta internal data array.
	 *
	 * @since 1.12.0
	 *
	 * @param string $key   The sub-key within _meta.
	 * @param mixed  $value The value to store.
	 *
	 * @return void
	 */
	public function set_meta( string $key, $value ): void {
		$this->meta[ $key ] = $value;
	}

	/**
	 * Sets the job data. Can be used to store additional data common for all tasks.
	 *
	 * @since 1.12.0
	 *
	 * @param array $data Job data.
	 *
	 * @return Job
	 */
	public function set_data( array $data ): Job {
		$this->data = $data;

		return $this;
	}

	/**
	 * Sets the job progress.
	 *
	 * @since 1.12.0
	 *
	 * @param array $progress_args Job progress array.
	 *
	 * @return Job
	 */
	public function set_progress( array $progress_args ): Job {
		$this->progress = JobProgress::restore( $progress_args );

		if ( $this->progress->is_empty() ) {
			$this->progress->set_pending_tasks( $this->enqueued_tasks() );
		}

		return $this;
	}
}
