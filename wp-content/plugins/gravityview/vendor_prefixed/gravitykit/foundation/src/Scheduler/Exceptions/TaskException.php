<?php
/**
 * TaskException class.
 * This exception is thrown when a task needs to be restarted.
 */

namespace GravityKit\GravityView\Foundation\Scheduler\Exceptions;

use Exception;
use GravityKit\GravityView\Foundation\Scheduler\Models\NextRunRules;

class TaskException extends Exception {

	/**
	 * The new task arguments.
	 *
	 * @var NextRunRules|null
	 */
	protected $next_run_rules;

	/**
	 * TaskException constructor.
	 *
	 * @param string            $message Exception message.
	 * @param NextRunRules|null $next_run_rules The next run rules.
	 */
	public function __construct( string $message = '', ?NextRunRules $next_run_rules = null ) {
		parent::__construct( $message );

		$this->next_run_rules = $next_run_rules;
	}

	/**
	 * Gets the next run rules.
	 *
	 * @return NextRunRules|null
	 */
	public function next_run_rules(): ?NextRunRules {
		return $this->next_run_rules;
	}
}
