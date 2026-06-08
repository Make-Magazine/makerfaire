<?php
/**
 * This class is used inside the Job object to track the progress of the tasks.
 * *
 * @license GPL-2.0-or-later
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Scheduler\Models;

use Exception;
use GravityKit\GravityEdit\Foundation\Scheduler\Contracts\Restorable;

class JobProgress implements Restorable {

	/**
	 * @var array
	 */
	protected $progress = [];


	/**
	 * JobProgress constructor.
	 *
	 * @since 1.12.0
	 *
	 * @param array $progress The progress array.
	 */
	public function __construct( array $progress ) {
		$this->progress = $progress;
	}

	/**
	 * Adds a pending task.
	 *
	 * @param string $task_name The task name.
	 *
	 * @return void
	 */
	public function add_pending( string $task_name ): void {
		$this->progress[ Task::STATUS_PENDING ][ $task_name ] = current_time( 'mysql', true );
	}

	/**
	 * Adds a pending task to the first position. Is needed when updating the running task back to pending.
	 *
	 * @param string $task_name The task name.
	 *
	 * @return void
	 */
	public function add_pending_first( string $task_name ): void {
		$pending = $this->progress[ Task::STATUS_PENDING ] ?? [];

		$reversed = array_reverse( $pending, true );

		$reversed[ $task_name ] = current_time( 'mysql', true );

		$this->progress[ Task::STATUS_PENDING ] = array_reverse( $reversed, true );
	}

	/**
	 * Sets the pending tasks from an array of Task objects.
	 *
	 * Converts each Task to the expected progress format (task_name => datetime).
	 *
	 * @since 1.12.0
	 *
	 * @param Task[] $pending The pending tasks.
	 *
	 * @return self
	 */
	public function set_pending_tasks( array $pending ): self {
		$entries = [];

		foreach ( $pending as $task ) {
			$entries[ $task->name() ] = current_time( 'mysql', true );
		}

		$this->progress[ Task::STATUS_PENDING ] = $entries;

		return $this;
	}

	/**
	 * Gets the pending tasks.
	 *
	 * @since 1.12.0
	 *
	 * @return array [['task_name' => 'time']]
	 */
	public function pending(): array {
		return $this->progress[ Task::STATUS_PENDING ] ?? [];
	}

	/**
	 * Gets the running tasks.
	 *
	 * @since 1.12.0
	 *
	 * @return array [['task_name' => 'time']]
	 */
	public function running(): array {
		return $this->progress[ Task::STATUS_RUNNING ] ?? [];
	}


	/**
	 * Gets the completed tasks.
	 *
	 * @since 1.12.0
	 *
	 * @return array [['task_name' => 'time']]
	 */
	public function completed(): array {
		return $this->progress[ Task::STATUS_COMPLETED ] ?? [];
	}

	/**
	 * Gets the failed tasks.
	 *
	 * @since 1.12.0
	 *
	 * @return array [['task_name' => 'time']]
	 */
	public function failed(): array {
		return $this->progress[ Task::STATUS_FAILED ] ?? [];
	}

	/**
	 * Gets the skipped tasks.
	 *
	 * @since 1.12.0
	 *
	 * @return array
	 */
	public function skipped(): array {
		return $this->progress[ Task::STATUS_SKIPPED ] ?? [];
	}

	/**
	 * Gets the total number of tasks.
	 *
	 * @since 1.12.0
	 *
	 * @return int
	 */
	public function total(): int {
		return count( $this->pending() ) +
		       count( $this->running() ) +
		       count( $this->completed() ) +
		       count( $this->skipped() ) +
		       count( $this->failed() );
	}

	/**
	 * Gets the failed tasks.
	 *
	 * @since 1.12.0
	 *
	 * @param string $task_name The task name.
	 *
	 * @return bool
	 */
	public function is_failed( string $task_name ): bool {
		return isset( $this->progress[ Task::STATUS_FAILED ][ $task_name ] );
	}

	/**
	 * Updates the task status.
	 *
	 * @since 1.12.0
	 *
	 * @param string $task_name The task name.
	 * @param string $status    The task status.
	 *
	 * @return void
	 */
	public function update_task_status( string $task_name, string $status ): void {
		$this->remove_from_statuses( $task_name );

		// The pending task should be added to the first position, because it means this is a rerun task.
		if ( Task::STATUS_PENDING === $status ) {
			$this->add_pending_first( $task_name );
			return;
		}
		$this->progress[ $status ][ $task_name ] = current_time( 'mysql', true );
	}

	/**
	 * Remove task from all statuses.
	 *
	 * @param string $task_name Task name.
	 *
	 * @return void
	 */
	public function remove_from_statuses( string $task_name ): void {
		foreach ( $this->progress as $status => $tasks ) {
			if ( isset( $this->progress[ $status ][ $task_name ] ) ) {
				unset( $this->progress[ $status ][ $task_name ] );
			}
		}
	}

	/**
	 * Converts the job progress to an array.
	 *
	 * @since 1.12.0
	 *
	 * @return array
	 */
	public function to_array(): array {
		return [
			Task::STATUS_PENDING   => $this->pending(),
			Task::STATUS_RUNNING   => $this->running(),
			Task::STATUS_COMPLETED => $this->completed(),
			Task::STATUS_FAILED    => $this->failed(),
			Task::STATUS_SKIPPED   => $this->skipped(),
		];
	}

	/**
	 * Checks if the job progress is empty.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public function is_empty(): bool {
		return ! $this->pending() &&
		       ! $this->completed() &&
		       ! $this->running() &&
		       ! $this->skipped() &&
		       ! $this->failed();
	}

	/**
	 * Converts the job progress to a percentage.
	 *
	 * @since 1.12.0
	 *
	 * @return int
	 */
	public function to_percent(): int {
		$total = $this->total();

		if ( 0 === $total ) {
			return 0;
		}

		return (int) ( count( $this->completed() ) / $total * 100 );
	}

	/**
	 * Gets the MySQL timestamp when a task entered its current status.
	 *
	 * @since 1.12.0
	 *
	 * @param string $task_name The task name.
	 *
	 * @return string MySQL datetime string, or empty if not found.
	 */
	public function task_time( string $task_name ): string {
		$statuses = [
			Task::STATUS_COMPLETED,
			Task::STATUS_FAILED,
			Task::STATUS_RUNNING,
			Task::STATUS_SKIPPED,
			Task::STATUS_PENDING,
		];

		foreach ( $statuses as $status ) {
			if ( isset( $this->progress[ $status ][ $task_name ] ) ) {
				return (string) $this->progress[ $status ][ $task_name ];
			}
		}

		return '';
	}

	/**
	 * Gets the status of a task by name.
	 *
	 * @since 1.12.0
	 *
	 * @param string $task_name The task name.
	 *
	 * @return string One of the Task::STATUS_* constants, or Task::STATUS_UNKNOWN.
	 */
	public function task_status( string $task_name ): string {
		if ( isset( $this->progress[ Task::STATUS_PENDING ][ $task_name ] ) ) {
			return Task::STATUS_PENDING;
		}

		if ( isset( $this->progress[ Task::STATUS_RUNNING ][ $task_name ] ) ) {
			return Task::STATUS_RUNNING;
		}

		if ( isset( $this->progress[ Task::STATUS_COMPLETED ][ $task_name ] ) ) {
			return Task::STATUS_COMPLETED;
		}

		if ( isset( $this->progress[ Task::STATUS_FAILED ][ $task_name ] ) ) {
			return Task::STATUS_FAILED;
		}

		if ( isset( $this->progress[ Task::STATUS_SKIPPED ][ $task_name ] ) ) {
			return Task::STATUS_SKIPPED;
		}

		return Task::STATUS_UNKNOWN;
	}


	/**
	 * Moves all pending and running tasks to skipped status.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	public function mark_remaining_skipped(): void {
		foreach ( array_keys( $this->pending() ) as $name ) {
			$this->update_task_status( (string) $name, Task::STATUS_SKIPPED );
		}

		foreach ( array_keys( $this->running() ) as $name ) {
			$this->update_task_status( (string) $name, Task::STATUS_SKIPPED );
		}
	}

	/**
	 * This function is used to restore the job progress from the database.
	 *
	 * @since 1.12.0
	 *
	 * @param array $args The progress array.
	 *
	 * @return self
	 */
	public static function restore( array $args ): object {
		return new self( $args );
	}
}
