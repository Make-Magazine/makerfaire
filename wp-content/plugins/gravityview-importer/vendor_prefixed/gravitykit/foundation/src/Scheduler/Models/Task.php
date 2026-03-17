<?php
/**
 * Abstract Action class.
 * *
 * @license GPL-2.0-or-later
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Scheduler\Models;

use Exception;
use GravityKit\GravityImport\Foundation\Scheduler\Contracts\Restorable;
use GravityKit\GravityImport\Foundation\Scheduler\Traits\LoggerTrait;

class Task implements Restorable {

	use LoggerTrait;

	public const STATUS_PENDING = 'pending';

	public const STATUS_COMPLETED = 'completed';

	public const STATUS_RUNNING = 'running';

	public const STATUS_FAILED = 'failed';

	public const STATUS_SKIPPED = 'skipped';

	public const STATUS_UNKNOWN = 'unknown';

	/**
	 * Top-level key in task args containing all Foundation internal data.
	 *
	 * Products never need to read or write this key. All internal state
	 * (deadline, retry count, fingerprint, no-progress count) lives here
	 * so compute_args_fingerprint() only needs to exclude one key.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	const META_KEY = '_meta';


	/**
	 * The task name.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 * */
	protected $name = '';

	/**
	 * The task enabled status.
	 *
	 * @since 1.12.0
	 *
	 * @var bool
	 * */
	protected $enabled = true;

	/**
	 * Keeps the task args.
	 *
	 * @since 1.12.0
	 *
	 * @var array
	 */
	protected $args = [];

	/**
	 * Keeps the job data - share data between all job tasks.
	 *
	 * @since 1.12.0
	 *
	 * @var array
	 */
	protected $job_data = [];

	/**
	 * Keeps the task dependency names.
	 *
	 * @since 1.12.0
	 *
	 * @var string[]
	 */
	protected $dependencies = [];

	/**
	 * The task callback.
	 *
	 * @since 1.12.0
	 *
	 * @var callable
	 */
	protected $callback;

	/**
	 * If the task can fail without affecting the job status.
	 *
	 * @since 1.12.0
	 *
	 * @var bool
	 */
	protected $can_fail = false;

	/**
	 * Human-readable label for display in the UI.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	protected $label = '';

	/**
	 * Task constructor.
	 *
	 * @since 1.12.0
	 *
	 * @param string   $name Action name.
	 * @param callable $callback Action callback.
	 * @param array    $args Action args.
	 * @param array    $dependencies The names of the tasks that this task depends on.
	 * @param bool     $can_fail If true, the task can fail without affecting the job status.
	 *
	 * @throws Exception
	 */
	public function __construct( string $name, callable $callback, array $args = [], array $dependencies = [], bool $can_fail = false ) {
		$this->name = $name;

		$this->set_callback( $callback );
		$this->set_args( $args );
		$this->set_dependencies( $dependencies );
		$this->set_can_fail( $can_fail );
	}

	/**
	 * Provides the task name.
	 *
	 * @since 1.12.0
	 *
	 * @return string
	 */
	public function name(): string {
		return $this->name;
	}

	/**
	 * Provides the task callback.
	 *
	 * @since 1.12.0
	 *
	 * @return callable
	 */
	public function callback(): callable {
		/**
		 * Filters the task callback.
		 *
		 * The filtered value must be callable (function, closure, or class method).
		 * Invalid values are silently ignored and the original callback is used.
		 *
		 * @since 1.12.0
		 *
		 * @param callable $callback The task callback.
		 * @param Task     $instance The task object.
		 */
		$filtered = apply_filters( 'gk/foundation/scheduler/task/' . $this->name . '/callback', $this->callback, $this );

		// @phpstan-ignore-next-line Filters can return any type at runtime.
		if ( is_callable( $filtered ) ) {
			return $filtered;
		}

		// @phpstan-ignore-next-line Defensive fallback for invalid filter return values.
		return $this->callback;
	}

	/**
	 * Executes the task.
	 *
	 * @since 1.12.0
	 *
	 * @return NextRunRules|null
	 */
	public function execute(): ?NextRunRules {
		$this->logger()->debug( 'Executing task.', [ 'task' => $this->name ] );

		if ( $this->enabled() ) {
			$next_rules = call_user_func( $this->callback(), $this->args(), $this->job_data() );
		}

		return $next_rules ?? null;
	}

	/**
	 * Controls the Action activity.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public function enabled(): bool {
		/**
		 * Filters the task enabled status.
		 *
		 * @since 1.12.0
		 *
		 * @param bool $enabled  Whether the task is enabled.
		 * @param Task $instance The task object.
		 */
		return apply_filters( 'gk/foundation/scheduler/task/' . $this->name . '/enabled', $this->enabled, $this );
	}

	/**
	 * Controls the task activity.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public function disabled(): bool {
		return ! $this->enabled();
	}

	/**
	 * Disables the task.
	 *
	 * @since 1.12.0
	 *
	 * @return Task
	 */
	public function disable(): Task {
		$this->enabled = false;

		return $this;
	}

	/**
	 * Enables the task if it was disabled before.
	 *
	 * @since 1.12.0
	 *
	 * @return Task
	 */
	public function enable(): Task {
		$this->enabled = true;

		return $this;
	}

	/**
	 * Sets the task args.
	 *
	 * @since 1.12.0
	 *
	 * @param array $args Task args.
	 *
	 * @return Task
	 */
	public function set_args( array $args ): Task {
		/**
		 * Filters the task args before setting them.
		 *
		 * @since 1.12.0
		 *
		 * @param array $args     The task args.
		 * @param Task  $instance The task object.
		 */
		$this->args = apply_filters( 'gk/foundation/scheduler/task/set/args', $args, $this );

		return $this;
	}

	/**
	 * Merges the provided args with the current task args.
	 *
	 * @since 1.12.0
	 *
	 * @param array $args Task args.
	 *
	 * @return Task
	 */
	public function add_args( array $args ): Task {
		$args = array_merge( $this->args(), $args );

		return $this->set_args( $args );
	}

	/**
	 * Sets the job data.
	 *
	 * @since 1.12.0
	 *
	 * @param array $data Job data.
	 *
	 * @return Task
	 */
	public function set_job_data( array $data ): Task {
		/**
		 * Filters the job data before setting it.
		 *
		 * @since 1.12.0
		 *
		 * @param array $data     The job data.
		 * @param Task  $instance The task object.
		 */
		$this->job_data = apply_filters( 'gk/foundation/scheduler/task/set/job-data', $data, $this );

		return $this;
	}

	/**
	 * Sets if the task can fail without affecting the job status.
	 *
	 * @since 1.12.0
	 *
	 * @param bool $can_fail If true, the task can fail without affecting the job status.
	 *
	 * @return Task
	 */
	public function set_can_fail( bool $can_fail = true ): Task {
		$this->can_fail = $can_fail;

		return $this;
	}

	/**
	 * Returns the human-readable label.
	 *
	 * @since 1.12.0
	 *
	 * @return string
	 */
	public function label(): string {
		return $this->label;
	}

	/**
	 * Sets the human-readable label.
	 *
	 * @since 1.12.0
	 *
	 * @param string $label The label to display in the UI.
	 *
	 * @return Task
	 */
	public function set_label( string $label ): Task {
		$this->label = $label;

		return $this;
	}

	/**
	 * Returns whether this task can fail without stopping the job.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public function can_fail(): bool {
		/**
		 * Filters whether the task can fail without stopping the job.
		 *
		 * @since 1.12.0
		 *
		 * @param bool $can_fail Whether the task can fail without affecting the job status.
		 * @param Task $instance The task object.
		 */
		return apply_filters( 'gk/foundation/scheduler/task/get/can-fail', $this->can_fail, $this );
	}

	/**
	 * Gets the retry count for this task.
	 *
	 * @since 1.12.0
	 *
	 * @return int The retry count (0 if never retried).
	 */
	public function get_retry_count(): int {
		return $this->meta( 'retries' ) ?? 0;
	}

	/**
	 * Increments the retry count for this task.
	 *
	 * @since 1.12.0
	 *
	 * @return int The new retry count.
	 */
	public function increment_retry_count(): int {
		$count = $this->get_retry_count() + 1;
		$this->set_meta( 'retries', $count );

		return $count;
	}

	/**
	 * Computes a fingerprint of the task's product-facing args.
	 *
	 * Excludes _meta (all Foundation internal state) so the fingerprint only
	 * changes when the product's actual state advances. Used by the
	 * no-progress watchdog to detect stuck reruns.
	 *
	 * @since 1.12.0
	 *
	 * @return string MD5 hash of the filtered, sorted args.
	 */
	public function compute_args_fingerprint(): string {
		$args = $this->args;

		unset( $args[ self::META_KEY ] );

		ksort( $args );

		return md5( (string) wp_json_encode( $args ) );
	}

	/**
	 * Gets the stored args fingerprint from the previous execution.
	 *
	 * @since 1.12.0
	 *
	 * @return string The stored fingerprint, or empty string if none.
	 */
	public function get_progress_hash(): string {
		return $this->meta( 'fingerprint' ) ?? '';
	}

	/**
	 * Stores the current args fingerprint for comparison on next execution.
	 *
	 * @since 1.12.0
	 *
	 * @param string $hash The fingerprint to store.
	 *
	 * @return void
	 */
	public function set_progress_hash( string $hash ): void {
		$this->set_meta( 'fingerprint', $hash );
	}

	/**
	 * Gets the consecutive no-progress rerun count.
	 *
	 * @since 1.12.0
	 *
	 * @return int The count (0 if never stuck).
	 */
	public function get_no_progress_count(): int {
		return $this->meta( 'no_progress' ) ?? 0;
	}

	/**
	 * Increments the no-progress rerun counter.
	 *
	 * @since 1.12.0
	 *
	 * @return int The new count.
	 */
	public function increment_no_progress_count(): int {
		$count = $this->get_no_progress_count() + 1;
		$this->set_meta( 'no_progress', $count );

		return $count;
	}

	/**
	 * Resets the no-progress counter.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	public function reset_no_progress_count(): void {
		$this->set_meta( 'no_progress', 0 );
	}

	/**
	 * Reads a value from the _meta internal data sub-array.
	 *
	 * @since 1.12.0
	 *
	 * @param string $key The sub-key within _meta.
	 *
	 * @return mixed|null The value, or null if not set.
	 */
	public function meta( string $key ) {
		return $this->args[ self::META_KEY ][ $key ] ?? null;
	}

	/**
	 * Writes a value to the _meta internal data sub-array.
	 *
	 * @since 1.12.0
	 *
	 * @param string $key   The sub-key within _meta.
	 * @param mixed  $value The value to store.
	 *
	 * @return void
	 */
	public function set_meta( string $key, $value ): void {
		if ( ! isset( $this->args[ self::META_KEY ] ) ) {
			$this->args[ self::META_KEY ] = [];
		}

		$this->args[ self::META_KEY ][ $key ] = $value;
	}

	/**
	 * Gets the job data.
	 *
	 * @since 1.12.0
	 *
	 * @return array
	 */
	public function job_data(): array {
		return $this->job_data;
	}

	/**
	 * Sets the task args.
	 *
	 * @since 1.12.0
	 *
	 * @param array $dependencies Task args.
	 *
	 * @return Task
	 */
	public function set_dependencies( array $dependencies ): Task {
		/**
		 * Filters the task dependencies before setting them.
		 *
		 * @since 1.12.0
		 *
		 * @param array $dependencies The task dependency names.
		 * @param Task  $instance     The task object.
		 */
		$this->dependencies = apply_filters( 'gk/foundation/scheduler/task/set/dependencies', $dependencies, $this );

		return $this;
	}

	/**
	 * Sets the task callback.
	 *
	 * @since 1.12.0
	 *
	 * @param callable $callback Task callback.
	 *
	 * @return Task
	 * @throws Exception
	 */
	public function set_callback( callable $callback ): Task {
		$this->check_callback( $callback );
		$this->callback = $callback;

		return $this;
	}

	/**
	 * Gets the task args.
	 *
	 * @since 1.12.0
	 *
	 * @return array
	 */
	public function args(): array {
		/**
		 * Filters the task args.
		 *
		 * @since 1.12.0
		 *
		 * @param array $args     The task args.
		 * @param Task  $instance The task object.
		 */
		return apply_filters( 'gk/foundation/scheduler/task/' . $this->name . '/args', $this->args, $this );
	}

	/**
	 * Converts the task to an array.
	 *
	 * @since 1.12.0
	 *
	 * @return array
	 */
	public function to_array(): array {
		$data = [
			'name'         => $this->name,
			'callback'     => $this->callback,
			'args'         => $this->args,
			'dependencies' => $this->dependencies,
			'can_fail'     => $this->can_fail,
		];

		if ( '' !== $this->label ) {
			$data['label'] = $this->label;
		}

		return $data;
	}

	/**
	 * Restores a task object using the task args.
	 * Reverse function of to_array().
	 *
	 * @since 1.12.0
	 *
	 * @param array $args The task args.
	 *
	 * @return Task
	 * @throws \Exception
	 */
	public static function restore( array $args ): object {
		// Check if the callback is callable.
		if ( empty( $args['callback'] ) || ! is_callable( $args['callback'] ) ) {
			throw new Exception( 'The task callback is not callable. Task name: ' . $args['name'] );
		}

		// Enforce array-only callback restriction (same as check_callback).
		if ( ! is_array( $args['callback'] ) ) {
			throw new Exception( 'Only array-based callbacks are allowed. Task name: ' . $args['name'] );
		}

		// Check if the callback has required arguments.
		if ( empty( $args['name'] ) ) {
			throw new Exception( 'The task name should not be empty.' );
		}

		$task = new self(
			$args['name'],
			$args['callback'],
			$args['args'] ?? [],
			$args['dependencies'] ?? [],
			$args['can_fail'] ?? false
		);

		if ( isset( $args['job_data'] ) ) {
			$task->set_job_data( $args['job_data'] );
		}

		if ( ! empty( $args['label'] ) ) {
			$task->set_label( $args['label'] );
		}

		return $task;
	}

	/**
	 * Returns the task's dependency names.
	 *
	 * @since 1.12.0
	 *
	 * @return string[]
	 */
	public function get_dependencies(): array {
		return $this->dependencies;
	}

	/**
	 * Checks if the task meets its dependencies.
	 *
	 * @since 1.12.0
	 *
	 * @param array $completed_tasks The completed tasks.
	 *
	 * @return bool
	 */
	public function meets_dependencies( array $completed_tasks ): bool {
		return empty( array_diff( $this->dependencies, array_keys( $completed_tasks ) ) );
	}

	/**
	 * Makes sure the callback is callable.
	 *
	 * @since 1.12.0
	 *
	 * @param callable $callback Task callback.
	 *
	 * @return void
	 * @throws Exception
	 */
	protected function check_callback( callable $callback ) {
		if ( ! is_array( $callback ) ) {
			throw new Exception( 'Only array-based callbacks are allowed.' );
		}
	}
}
