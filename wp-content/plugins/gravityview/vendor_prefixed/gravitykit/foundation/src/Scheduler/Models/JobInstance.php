<?php
/**
 * Job DB instance class.
 * */

namespace GravityKit\GravityView\Foundation\Scheduler\Models;

use Exception;
use GravityKit\GravityView\Foundation\Scheduler\Contracts\RestorableInstance;

class JobInstance extends Job implements RestorableInstance {

	/**
	 * @var int|null
	 */
	protected $id;

	/**
	 * @var string|null
	 */
	protected $status;

	/**
	 * JobInstance constructor.
	 *
	 * @since 1.12.0
	 *
	 * @param string   $job_name Job name.
	 * @param int|null $job_id Job ID.
	 */
	public function __construct( string $job_name, ?int $job_id ) {
		parent::__construct( $job_name );

		$this->id = $job_id;
	}

	/**
	 * Sets the job instance status.
	 *
	 * @param string $status The job status to set.
	 *
	 * @return void
	 */
	public function set_status( string $status ): void {
		$this->status = $status;
	}

	/**
	 * Gets the job instance status.
	 *
	 * @since 1.12.0
	 *
	 * @return string $status The Job status.
	 */
	public function status(): ?string {
		return $this->status;
	}

	/**
	 * Restores a job object using database args.
	 * Reverse function of to_array().
	 *
	 * @since 1.12.0
	 *
	 * @param array $args The job args.
	 * @param int   $instance_id The job instance ID.
	 *
	 * @return self
	 * @throws Exception
	 */
	public static function restore( array $args, int $instance_id ): object {
		$job = new self( $args['job'], $instance_id );

		$tasks = $args['tasks'] ?? [];

		$job->set_data( $args['data'] ?? [] );

		if ( ! empty( $args['label'] ) ) {
			$job->set_label( $args['label'] );
		}

		if ( ! empty( $args['product'] ) ) {
			$job->set_product( $args['product'] );
		}

		foreach ( $tasks as $task_args ) {
			$task = Task::restore( $task_args );
			$job->register_task( $task );
			$job->enqueue_task( $task );
		}

		$job->set_progress( $args['progress'] ?? [] );

		$meta = $args['_meta'] ?? [];

		if ( ! empty( $meta['started_at'] ) ) {
			$job->set_started_at( (int) $meta['started_at'] );
		}

		return $job;
	}

	/**
	 * Gets the job ID.
	 *
	 * @since 1.12.0
	 *
	 * @return int|null
	 */
	public function id(): ?int {
		return $this->id;
	}
}
