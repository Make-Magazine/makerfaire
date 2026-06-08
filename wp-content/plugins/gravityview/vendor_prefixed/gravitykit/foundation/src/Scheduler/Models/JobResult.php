<?php
/**
 * Job scheduling result.
 *
 * @license GPL-2.0-or-later
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Scheduler\Models;

/**
 * Value object wrapping the outcome of a scheduling operation.
 *
 * Returned by all JobHandler scheduling methods (run, schedule_single, etc.)
 * to provide both the job ID and any execution warnings.
 *
 * @since 1.12.0
 */
class JobResult {

	/**
	 * The Action Scheduler action ID (0 = scheduling failed).
	 *
	 * @since 1.12.0
	 *
	 * @var int
	 */
	protected $job_id;

	/**
	 * Execution warning message, or null if everything is healthy.
	 *
	 * @since 1.12.0
	 *
	 * @var string|null
	 */
	protected $warning;

	/**
	 * Typed failure code from HealthCheck, or null if healthy.
	 *
	 * Products can use this to customize their UI/messages for specific
	 * failure scenarios instead of parsing the warning string.
	 *
	 * @since 1.12.0
	 *
	 * @see HealthCheck::LOOPBACK_AND_CRON_DISABLED
	 * @see HealthCheck::LOOPBACK_AND_CRON_SPAWN
	 *
	 * @var string|null
	 */
	protected $failure_code;

	/**
	 * Callback to cancel the scheduled job.
	 *
	 * Injected by the scheduling layer so JobResult stays decoupled
	 * from the underlying queue implementation.
	 *
	 * @since 1.12.0
	 *
	 * @var callable|null
	 */
	protected $cancel_callback;

	/**
	 * JobResult constructor.
	 *
	 * @since 1.12.0
	 *
	 * @param int           $job_id           The AS action ID. Zero means scheduling failed.
	 * @param string|null   $warning          Execution warning message, or null.
	 * @param string|null   $failure_code     Typed failure code, or null if healthy.
	 * @param callable|null $cancel_callback  Callback to cancel the job. Receives no arguments.
	 */
	public function __construct( int $job_id, ?string $warning = null, ?string $failure_code = null, ?callable $cancel_callback = null ) {
		$this->job_id          = $job_id;
		$this->warning         = $warning;
		$this->failure_code    = $failure_code;
		$this->cancel_callback = $cancel_callback;
	}

	/**
	 * Returns the job instance ID.
	 *
	 * @since 1.12.0
	 *
	 * @return int The AS action ID. Zero means scheduling failed.
	 */
	public function job_id(): int {
		return $this->job_id;
	}

	/**
	 * Returns the execution warning message.
	 *
	 * @since 1.12.0
	 *
	 * @return string|null Warning message, or null if healthy.
	 */
	public function warning(): ?string {
		return $this->warning;
	}

	/**
	 * Whether an execution warning is present.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public function has_warning(): bool {
		return null !== $this->warning;
	}

	/**
	 * Returns the typed failure code from HealthCheck.
	 *
	 * Products can use this to customize their error messages or UI
	 * for specific failure scenarios.
	 *
	 * @since 1.12.0
	 *
	 * @see HealthCheck::LOOPBACK_AND_CRON_DISABLED
	 * @see HealthCheck::LOOPBACK_AND_CRON_SPAWN
	 *
	 * @return string|null One of the HealthCheck failure code constants, or null if healthy.
	 */
	public function failure_code(): ?string {
		return $this->failure_code;
	}

	/**
	 * Whether the job was successfully scheduled.
	 *
	 * @since 1.12.0
	 *
	 * @return bool True if job_id > 0.
	 */
	public function succeeded(): bool {
		return $this->job_id > 0;
	}

	/**
	 * Cancels the scheduled job.
	 *
	 * Use this when the caller determines the job should not remain
	 * in the queue (e.g., after detecting an execution warning).
	 *
	 * @since 1.12.0
	 *
	 * @return bool True if the job was cancelled, false if not cancellable.
	 */
	public function cancel(): bool {
		if ( ! $this->cancel_callback || ! $this->job_id ) {
			return false;
		}

		call_user_func( $this->cancel_callback );

		return true;
	}
}
