<?php
/**
 * Job task handler.
 * *
 * @license GPL-2.0-or-later
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Scheduler\Handlers;

use Exception;
use GravityKit\GravityImport\Foundation\Scheduler\Models\Task;
use GravityKit\GravityImport\Foundation\Scheduler\Models\Job;
use GravityKit\GravityImport\Foundation\Scheduler\Store\DbStore;

class TaskHandler {

	/**
	 * @var Job
	 */
	protected $job;

	/**
	 * @var DbStore
	 */
	protected $store;

	/**
	 * @var Task
	 * */
	protected $last_task;

	/**
	 * TaskHandler constructor.
	 *
	 * @param Job     $job Job object.
	 * @param DbStore $store Task store object.
	 */
	public function __construct( Job $job, DbStore $store ) {
		$this->job   = $job;
		$this->store = $store;
	}

	/**
	 * Creates and registers a new task in the job object. Please don't forget to queue it afterward.
	 *
	 * @param string   $name Action name.
	 * @param callable $callback Action callback.
	 * @param array    $args Callback args.
	 * @param string[] $dependencies The names of the tasks that this task depends on.
	 * @param bool     $can_fail If true, the task can fail without affecting the job status.
	 *
	 * @return TaskHandler
	 * @throws Exception
	 */
	public function create( string $name, callable $callback, array $args = [], array $dependencies = [], bool $can_fail = false ): TaskHandler {
		$this->last_task = new Task( $name, $callback, $args, $dependencies, $can_fail );

		$this->job->register_task( $this->last_task );

		return $this;
	}

	/**
	 * Sets the last created task's can_fail property.
	 *
	 * @param bool $can_fail If true, the task can fail without affecting the job status.
	 *
	 * @return TaskHandler
	 */
	/**
	 * Sets the last created task's label.
	 *
	 * @since 1.12.0
	 *
	 * @param string $label Human-readable label for display in the UI.
	 *
	 * @return TaskHandler
	 */
	public function set_label( string $label ): TaskHandler {
		$this->last_task()->set_label( $label );

		return $this;
	}

	/**
	 * Sets whether the last registered task can fail without stopping the job.
	 *
	 * @since 1.12.0
	 *
	 * @param bool $can_fail Whether the task can fail. Default: true.
	 *
	 * @return TaskHandler
	 */
	public function can_fail( bool $can_fail = true ): TaskHandler {
		$this->last_task()->set_can_fail( $can_fail );
		return $this;
	}

	/**
	 * Enqueues previously registered task.
	 *
	 * @param string     $name Action name.
	 * @param array|null $args Callback args.
	 *
	 * @return TaskHandler
	 * @throws Exception
	 */
	public function queue( string $name = '', ?array $args = null ): TaskHandler {
		$task = $name ? $this->get_task( $name ) : $this->last_task();

		if ( isset( $args ) ) {
			$task->set_args( $args );
		}

		$this->job->enqueue_task( $task );

		return $this;
	}

	/**
	 * Gets the task object by name.
	 *
	 * @since 1.12.0
	 *
	 * @param string $name Task name to check.
	 *
	 * @return Task
	 * @throws Exception
	 */
	protected function get_task( string $name ): Task {
		$task = $this->job->get_task( $name );

		if ( ! $task ) {
			throw new Exception( 'Task is not registered yet. Please use the create() function first.' );
		}

		return $task;
	}

	/**
	 * Gets the last created task object.
	 *
	 * @since 1.12.0
	 *
	 * @return Task
	 * @throws Exception
	 */
	protected function last_task(): Task {
		if ( ! $this->last_task instanceof Task ) {
			throw new Exception( 'The task is not created yet. Please use the create() function first.' );
		}

		return $this->last_task;
	}
}
