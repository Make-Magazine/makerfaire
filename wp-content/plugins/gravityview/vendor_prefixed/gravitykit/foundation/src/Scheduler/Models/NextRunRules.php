<?php
/**
 * The next run rules class. Used as tasks returns to update the next run behavior.
 * *
 * @license GPL-2.0-or-later
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Scheduler\Models;

class NextRunRules {

	/**
	 * If you want to run the same task again.
	 *
	 * @var bool
	 */
	protected $rerun_task = false;

	/**
	 * New job data.
	 *
	 * @var array|null
	 */
	protected $job_data;

	/**
	 * Next task args
	 *
	 * @var array
	 */
	protected $next_task_args = [];

	/**
	 * Sets the next task rerun option.
	 *
	 * @since 1.12.0
	 *
	 * @param bool $rerun If you want to run the same task again.
	 *
	 * @return self
	 */
	public function rerun( bool $rerun = true ): self {
		$this->rerun_task = $rerun;

		return $this;
	}

	/**
	 * Checks if the current task should be executed again.
	 *
	 * @return bool
	 */
	public function should_rerun(): bool {
		return $this->rerun_task;
	}

	/**
	 * Sets the new job data.
	 *
	 * @since 1.12.0
	 *
	 * @param array $args The new job data.
	 *
	 * @return self
	 */
	public function set_job_data( array $args ): self {
		$this->job_data = $args;

		return $this;
	}

	/**
	 * Gets the new job data.
	 *
	 * @since 1.12.0
	 *
	 * @return array|null
	 */
	public function job_data(): ?array {
		return $this->job_data;
	}

	/**
	 * Sets the next task args.
	 *
	 * @since 1.12.0
	 *
	 * @param array $args The next task args.
	 *
	 * @return self
	 */
	public function set_next_task_args( array $args ): self {
		$this->next_task_args = $args;

		return $this;
	}

	/**
	 * Gets the next task args.
	 *
	 * @since 1.12.0
	 *
	 * @return array|null
	 */
	public function next_task_args(): ?array {
		return $this->next_task_args;
	}
}
