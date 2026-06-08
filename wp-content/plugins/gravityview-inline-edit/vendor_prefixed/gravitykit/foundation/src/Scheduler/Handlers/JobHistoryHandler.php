<?php
/**
 * Job history handler.
 * *
 * @license GPL-2.0-or-later
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Scheduler\Handlers;

use ActionScheduler_Action;
use ActionScheduler_FinishedAction;
use ActionScheduler_Store;
use Exception;
use GravityKit\GravityEdit\Foundation\Scheduler\Models\Job;
use GravityKit\GravityEdit\Foundation\Scheduler\Models\JobInstance;
use GravityKit\GravityEdit\Foundation\Scheduler\Store\DbStore;
use GravityKit\GravityEdit\Foundation\Scheduler\Traits\LoggerTrait;

class JobHistoryHandler {

	use LoggerTrait;

	/**
	 * @var string
	 */
	protected $job_name;

	/**
	 * @var DbStore
	 */
	protected $store;

	/**
	 * Minimum action ID for filtering results (inclusive).
	 *
	 * @since 1.12.0
	 *
	 * @var int
	 */
	protected $since_action_id = 0;

	/**
	 * RunsHandler constructor.
	 *
	 * @param string  $job_name Job object.
	 * @param DbStore $store Task store object.
	 */
	public function __construct( string $job_name, DbStore $store ) {
		$this->job_name = $job_name;
		$this->store    = $store;
	}

	/**
	 * Filters subsequent queries to only include actions with IDs >= the given action ID.
	 *
	 * Useful for scoping history to a specific schedule chain. When a job is
	 * rescheduled, the first action ID of the new chain can be used to exclude
	 * history from previous schedules.
	 *
	 * @since 1.12.0
	 *
	 * @param int $action_id The minimum action ID (inclusive).
	 *
	 * @return self
	 */
	public function since( int $action_id ): self {
		$this->since_action_id = $action_id;

		return $this;
	}

	/**
	 * Filters an array of actions keyed by action ID, removing entries below the since threshold.
	 *
	 * @since 1.12.0
	 *
	 * @param array $actions Actions keyed by action ID.
	 *
	 * @return array Filtered actions.
	 */
	protected function filter_since( array $actions ): array {
		if ( ! $this->since_action_id ) {
			return $actions;
		}

		$since = $this->since_action_id;

		return array_filter(
			$actions,
			static function ( $key ) use ( $since ) {
				return $key >= $since;
			},
			ARRAY_FILTER_USE_KEY
		);
	}

	/**
	 * Get job runs with all possible statuses.
	 *
	 * @since 1.12.0
	 *
	 * @param array|null $query_args Query args. Use them if you want to narrow down the results.
	 *
	 * @return ActionScheduler_Action[]
	 */
	public function all( ?array $query_args = null ): array {
		return $this->filter_since( $this->store->get_all_instances( $this->job_name, $query_args ) );
	}

	/**
	 * Gets pending actions.
	 *
	 * @since 1.12.0
	 *
	 * @param array|null $query_args Query args.
	 *
	 * @return ActionScheduler_Action[] Pending tasks list.
	 */
	public function pending( ?array $query_args = null ): array {
		return $this->filter_since( $this->store->get_pending_instances( $this->job_name, $query_args ) );
	}

	/**
	 * Gets completed actions.
	 *
	 * @since 1.12.0
	 *
	 * @param array|null $query_args Query args.
	 *
	 * @return ActionScheduler_Action[] Completed actions list.
	 */
	public function completed( ?array $query_args = null ): array {
		return $this->filter_since( $this->store->get_completed_instances( $this->job_name, $query_args ) );
	}

	/**
	 * Gets running actions.
	 *
	 * @since 1.12.0
	 *
	 * @param array|null $query_args Query args.
	 *
	 * @return ActionScheduler_Action[] Running actions list (always one if the job is unique).
	 */
	public function running( ?array $query_args = null ): array {
		return $this->filter_since( $this->store->get_running_instances( $this->job_name, $query_args ) );
	}

	/**
	 * Gets the running job instance.
	 *
	 * @since 1.12.0
	 *
	 * @param array|null $query_args Query args.
	 *
	 * @return JobInstance|null Running job instance.
	 */
	public function running_job( ?array $query_args = [] ): ?JobInstance {
		$actions = $this->store->get_running_instances( $this->job_name, $query_args );

		if ( ! $actions ) {
			return null;
		}

		// Get the latest action, which is the first in the list.
		$id     = key( $actions );
		$action = $actions[ $id ];

		$job = $this->restore_job_instance( $id, $action );

		if ( ! $job ) {
			return null;
		}

		$job->set_status( ActionScheduler_Store::STATUS_RUNNING );

		return $job;
	}

	/**
	 * Gets the latest job instance.
	 *
	 * @since 1.12.0
	 *
	 * @param array|null $query_args Query args.
	 * @param bool       $with_status If true, the job instance will have its status set.
	 *
	 * @return JobInstance|null Running job instance.
	 */
	public function latest_job( ?array $query_args = [], bool $with_status = false ): ?JobInstance {
		$query_args = array_merge(
			$query_args ?? [],
			[
				'order' => 'DESC',
			]
		);

		$actions = $this->store->get_all_instances( $this->job_name, $query_args );

		if ( ! $actions ) {
			return null;
		}

		// Get the latest action, which is the first in the list.
		$id     = key( $actions );
		$action = $actions[ $id ] ?? null;

		if ( ! $action ) {
			return null;
		}

		$job = $this->restore_job_instance( $id, $action );

		if ( $job && $with_status ) {
			$job->set_status( $this->store->get_instance_status( $id ) );
		}

		return $job;
	}

	/**
	 * Restores the job instance from the action.
	 *
	 * @param int                    $action_id Action ID.
	 * @param ActionScheduler_Action $action Action object.
	 *
	 * @return JobInstance|null
	 */
	protected function restore_job_instance( int $action_id, ActionScheduler_Action $action ): ?JobInstance {
		try {
			$job = JobInstance::restore( $action->get_args(), $action_id );
		} catch ( Exception $e ) {
			$this->logger()->error(
				'Failed to restore the job instance.',
				[
					'job_name' => $this->job_name,
					'error'    => $e->getMessage(),
				]
			);
		}

		return $job ?? null;
	}

	/**
	 * Gets the running job instance.
	 *
	 * @since 1.12.0
	 *
	 * @param array|null $query_args Query args.
	 *
	 * @return JobInstance|null Running job instance.
	 */
	public function active_job( ?array $query_args = null ): ?JobInstance {
		$actions = $this->store->get_running_instances( $this->job_name, $query_args );
		$status  = ActionScheduler_Store::STATUS_RUNNING;

		if ( ! $actions ) {
			$actions = $this->store->get_pending_instances( $this->job_name, $query_args );
			$status  = ActionScheduler_Store::STATUS_PENDING;
		}

		if ( ! $actions ) {
			return null;
		}

		$id     = key( $actions );
		$action = $actions[ $id ];

		$job = $this->restore_job_instance( $id, $action );

		if ( $job ) {
			$job->set_status( $status );
		}

		return $job;
	}

	/**
	 * Gets failed actions.
	 *
	 * @since 1.12.0
	 *
	 * @param array|null $query_args Query args.
	 *
	 * @return ActionScheduler_Action[] Failed actions list.
	 */
	public function failed( ?array $query_args = null ): array {
		return $this->filter_since( $this->store->get_failed_instances( $this->job_name, $query_args ) );
	}

	/**
	 * Gets canceled actions.
	 *
	 * @since 1.12.0
	 *
	 * @param array|null $query_args Query args.
	 *
	 * @return ActionScheduler_Action[] Canceled actions list.
	 */
	public function canceled( ?array $query_args = null ): array {
		return $this->filter_since( $this->store->get_canceled_instances( $this->job_name, $query_args ) );
	}

	/**
	 * Checks if the job has scheduled instances (pending or in-progress).
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public function is_scheduled(): bool {
		return $this->store->is_scheduled( $this->job_name, null );
	}

	/**
	 * Checks if the job is currently paused.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public function is_paused(): bool {
		return $this->store->is_job_paused( $this->job_name );
	}

	/**
	 * Returns the most recent completed action ID.
	 *
	 * Action IDs are auto-incrementing, so the highest ID is the most recent.
	 *
	 * @since 1.12.0
	 *
	 * @return int The action ID, or 0 if no completed actions exist.
	 */
	public function last_completed_id(): int {
		$actions = $this->completed();

		return $actions ? (int) max( array_keys( $actions ) ) : 0;
	}

	/**
	 * Returns the most recent failed action ID.
	 *
	 * Action IDs are auto-incrementing, so the highest ID is the most recent.
	 *
	 * @since 1.12.0
	 *
	 * @return int The action ID, or 0 if no failed actions exist.
	 */
	public function last_failed_id(): int {
		$actions = $this->failed();

		return $actions ? (int) max( array_keys( $actions ) ) : 0;
	}

	/**
	 * Returns the relevant date for an action.
	 *
	 * For pending actions, returns the scheduled date (when it will run).
	 * For completed/failed actions, returns the last attempt date (when it actually ran).
	 *
	 * @since 1.12.0
	 *
	 * @param int $action_id The action ID.
	 *
	 * @return \DateTime|null The action date, or null if unavailable.
	 */
	public function get_action_date( int $action_id ): ?\DateTime {
		try {
			return $this->store->get_date( $action_id ); // @phpstan-ignore method.notFound
		} catch ( \Exception $e ) {
			return null;
		}
	}

	/**
	 * Returns the error message from a failed action's log entries.
	 *
	 * Searches the action's log for a task_failed entry and extracts the
	 * human-readable error message.
	 *
	 * @since 1.12.0
	 *
	 * @param int $action_id The action ID.
	 *
	 * @return string The error message, or empty string if none found.
	 */
	public function get_action_error( int $action_id ): string {
		try {
			$logs = \ActionScheduler_Logger::instance()->get_logs( $action_id ); // @phpstan-ignore staticMethod.notFound
		} catch ( \Exception $e ) {
			return '';
		}

		foreach ( array_reverse( $logs ) as $log ) {
			$message = $log->get_message();

			if ( false === strpos( $message, '[task_failed]' ) ) {
				continue;
			}

			// Extract the raw error from the [error_raw:...] tag (locale-independent).
			if ( preg_match( '/\[error_raw:(.+)\]$/', $message, $matches ) ) {
				return $matches[1];
			}

			// Fallback for log entries created before the [error_raw:] tag was added.
			$marker_pos = strpos( $message, '[task_failed] ' );

			if ( false !== $marker_pos ) {
				return rtrim( substr( $message, $marker_pos + 14 ), '.' );
			}

			return '';
		}

		return '';
	}
}
