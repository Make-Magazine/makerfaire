<?php
/**
 * The next run rules class. Used as tasks returns to update the next run behavior.
 * */

namespace GravityKit\GravityView\Foundation\Scheduler\Models;

use ReflectionException;
use ReflectionMethod;
use UnexpectedValueException;

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

	/**
	 * Normalizes a task-callback return value into this Foundation copy's NextRunRules.
	 *
	 * Every GravityKit plugin bundles its own Strauss-prefixed Foundation copy, so a
	 * task callback compiled against plugin B's copy returns B's
	 * `...\Scheduler\Models\NextRunRules` while the running (winning) Foundation
	 * expects its own class. The copies are identical apart from the namespace
	 * prefix, so any object whose class is named `NextRunRules` and exposes the
	 * public accessors is rehydrated into this copy's class instead of being
	 * rejected by a type check.
	 *
	 * @since 1.29.0
	 *
	 * @param mixed $value The value returned by a task callback.
	 *
	 * @return self|null Null stays null; a same-class instance passes through unchanged;
	 *                   a foreign copy's instance is rehydrated into this class.
	 *
	 * @throws UnexpectedValueException When the value is neither null nor a NextRunRules.
	 */
	public static function from_task_return( $value ): ?self {
		if ( null === $value ) {
			return null;
		}

		if ( $value instanceof self ) {
			return $value;
		}

		$is_foreign_copy = self::is_foreign_next_run_rules( $value );

		if ( ! $is_foreign_copy ) {
			$type = is_object( $value ) ? get_class( $value ) : gettype( $value );

			throw new UnexpectedValueException(
				'Task callbacks must return NextRunRules or null; got ' . $type . '.'
			);
		}

		// Validated, not cast: `(bool) 'false'` is true, which would rerun a job
		// that asked to finish. Read before any write, so a bad value leaves the
		// normalized object untouched.
		$rerun = $value->should_rerun();

		if ( ! is_bool( $rerun ) ) {
			throw new UnexpectedValueException(
				'NextRunRules::should_rerun() must return bool; got ' . gettype( $rerun ) . '.'
			);
		}

		$job_data = $value->job_data();

		if ( null !== $job_data && ! is_array( $job_data ) ) {
			throw new UnexpectedValueException(
				'NextRunRules::job_data() must return array or null; got ' . gettype( $job_data ) . '.'
			);
		}

		$next_task_args = $value->next_task_args();

		if ( null !== $next_task_args && ! is_array( $next_task_args ) ) {
			throw new UnexpectedValueException(
				'NextRunRules::next_task_args() must return array or null; got ' . gettype( $next_task_args ) . '.'
			);
		}

		$normalized = new self();
		$normalized->rerun( $rerun );

		if ( null !== $job_data ) {
			$normalized->set_job_data( $job_data );
		}

		if ( null !== $next_task_args ) {
			$normalized->set_next_task_args( $next_task_args );
		}

		return $normalized;
	}

	/**
	 * Whether a value is a NextRunRules instance from another bundled Foundation copy.
	 *
	 * Matches on the unqualified class name plus the public accessors that
	 * every Foundation copy's NextRunRules has carried since 1.12.0, so a
	 * differently-prefixed copy is recognized while unrelated values are not.
	 *
	 * @since 1.29.0
	 *
	 * @param mixed $value The value to check.
	 *
	 * @return bool
	 */
	private static function is_foreign_next_run_rules( $value ): bool {
		if ( ! is_object( $value ) ) {
			return false;
		}

		// The whole chain, so a plugin's subclass of its own rules is recognized.
		// A subclass from this copy is already accepted by `instanceof self`
		// above, and the same shape from another copy has to match it.
		$lineage  = array_merge( [ get_class( $value ) ], array_values( (array) class_parents( $value ) ) );
		$is_named = false;

		foreach ( $lineage as $class ) {
			$separator_position = strrpos( $class, '\\' );
			$unqualified_name   = false === $separator_position ? $class : substr( $class, $separator_position + 1 );

			if ( 'NextRunRules' === $unqualified_name ) {
				$is_named = true;

				break;
			}
		}

		if ( ! $is_named ) {
			return false;
		}

		foreach ( [ 'should_rerun', 'job_data', 'next_task_args' ] as $accessor ) {
			if ( ! self::is_invocable_accessor( $value, $accessor ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Whether a method can actually be called on an object from outside it.
	 *
	 * `method_exists()` is true for private and protected methods, and for public
	 * ones that require arguments; calling either raises an `Error` rather than
	 * returning a value.
	 *
	 * @since 1.29.0
	 *
	 * @param object $value  The object.
	 * @param string $method The method name.
	 *
	 * @return bool Whether it is public, non-static and callable with no arguments.
	 */
	public static function is_invocable_accessor( $value, string $method ): bool {
		if ( ! is_object( $value ) || ! method_exists( $value, $method ) ) {
			return false;
		}

		try {
			$reflection = new ReflectionMethod( $value, $method );
		} catch ( ReflectionException $e ) {
			return false;
		}

		return $reflection->isPublic()
			&& ! $reflection->isStatic()
			&& 0 === $reflection->getNumberOfRequiredParameters();
	}
}
