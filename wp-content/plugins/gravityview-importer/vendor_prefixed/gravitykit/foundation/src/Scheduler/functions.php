<?php
/**
 * Scheduler utility functions for consuming products.
 *
 * These functions provide the public API for cooperative time budgeting.
 * Task callbacks use gk_scheduler_should_continue() to check whether
 * they should keep processing or checkpoint and yield.
 *
 * @since 1.12.0
 *
 * @license GPL-2.0-or-later
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

if ( ! function_exists( 'gk_scheduler_should_continue' ) ) {
	/**
	 * Checks whether a task callback should continue processing.
	 *
	 * Compares the current wall-clock time against the task's injected deadline.
	 * Call this in loops or before expensive operations to support cooperative
	 * time budgeting.
	 *
	 * Returns true (keep going) when no deadline is set, so tasks work
	 * correctly even without time budget enforcement.
	 *
	 * @since 1.12.0
	 *
	 * @param array $args   The task args (deadline lives in $args['_meta']['deadline']).
	 * @param int   $margin Seconds before the deadline to stop. Default: 2.
	 *
	 * @return bool True if there is still time remaining.
	 */
	function gk_scheduler_should_continue( array $args, int $margin = 2 ): bool {
		$deadline = $args['_meta']['deadline'] ?? null;

		if ( null === $deadline ) {
			return true;
		}

		return microtime( true ) < ( (float) $deadline - max( 0, $margin ) );
	}
}

if ( ! function_exists( 'gk_scheduler_checkpoint' ) ) {
	/**
	 * Creates a NextRunRules object to checkpoint and continue in a new execution.
	 *
	 * Convenience wrapper for the common pattern of returning a rerun with
	 * updated args. Pass only the keys that changed (e.g., offset); existing
	 * args are merged automatically by the scheduler.
	 *
	 * Usage:
	 *
	 *     function my_import( array $args, array $job_data ): ?NextRunRules {
	 *         $offset = $args['offset'] ?? 0;
	 *         $rows = get_rows( $offset, 100 );
	 *
	 *         foreach ( $rows as $i => $row ) {
	 *             if ( ! gk_scheduler_should_continue( $args ) ) {
	 *                 return gk_scheduler_checkpoint( [ 'offset' => $offset + $i ] );
	 *             }
	 *             process( $row );
	 *         }
	 *
	 *         return null; // Done.
	 *     }
	 *
	 * @since 1.12.0
	 *
	 * @param array $next_args Keys to merge for the next execution (e.g., ['offset' => 500]).
	 *
	 * @return \GravityKit\GravityImport\Foundation\Scheduler\Models\NextRunRules
	 */
	function gk_scheduler_checkpoint( array $next_args = [] ): \GravityKit\GravityImport\Foundation\Scheduler\Models\NextRunRules {
		$rules = new \GravityKit\GravityImport\Foundation\Scheduler\Models\NextRunRules();
		$rules->rerun( true );

		if ( ! empty( $next_args ) ) {
			$rules->set_next_task_args( $next_args );
		}

		return $rules;
	}
}

if ( ! function_exists( 'gk_scheduler_checkpoint_with_data' ) ) {
	/**
	 * Checkpoints with both updated task args and shared job data.
	 *
	 * Like gk_scheduler_checkpoint(), but also updates the job-level data
	 * that is shared across all tasks in the job. Use this when a task needs
	 * to both save its own progress (e.g., offset) and pass results to
	 * downstream tasks (e.g., processed count, generated file path).
	 *
	 * Usage:
	 *
	 *     function my_import( array $args, array $job_data ): ?NextRunRules {
	 *         $offset = $args['offset'] ?? 0;
	 *         $count  = $job_data['processed'] ?? 0;
	 *
	 *         foreach ( get_rows( $offset, 100 ) as $i => $row ) {
	 *             if ( ! gk_scheduler_should_continue( $args ) ) {
	 *                 return gk_scheduler_checkpoint_with_data(
	 *                     [ 'offset' => $offset + $i ],
	 *                     [ 'processed' => $count + $i ]
	 *                 );
	 *             }
	 *             process( $row );
	 *             $count++;
	 *         }
	 *
	 *         return null;
	 *     }
	 *
	 * @since 1.12.0
	 *
	 * @param array $next_args Keys to merge into task args for the next execution.
	 * @param array $job_data  Keys to merge into job-level shared data.
	 *
	 * @return \GravityKit\GravityImport\Foundation\Scheduler\Models\NextRunRules
	 */
	function gk_scheduler_checkpoint_with_data( array $next_args, array $job_data ): \GravityKit\GravityImport\Foundation\Scheduler\Models\NextRunRules {
		$rules = gk_scheduler_checkpoint( $next_args );
		$rules->set_job_data( $job_data );

		return $rules;
	}
}
