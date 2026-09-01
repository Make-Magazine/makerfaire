<?php
/**
 * Admin Notifier example class.
 */

namespace GravityKit\GravityView\Foundation\Scheduler\Example;

use Exception;
use GravityKit\GravityView\Foundation\Scheduler\Traits\LoggerTrait;

class AdminNotifier {
	use LoggerTrait;

	/**
	 * Notify the admin.
	 *
	 * @param array $args The arguments.
	 * @param array $job_data The job data.
	 * @return void
	 */
	public static function notify( array $args, ?array $job_data = null ): void {
		$me = new self();

		foreach ( range( 1, 3 ) as $item ) {
			$me->logger()->debug( __METHOD__ . ': ', [ 'args: ' . wp_json_encode( $args ), 'item: ' . $item ] );
			$me->logger()->debug( __METHOD__ . ': ', [ 'JOB name: ' . $args['task_level_argument'], 'item: ' . $item ] );
			sleep( 2 );
		}
	}

	/**
	 * Dependent task.
	 *
	 * @return void
	 */
	public static function dependent_task(): void {
		// Will never be executed.
	}

	/**
	 * Throws an exception.
	 *
	 * @return void
     * @throws Exception
	 */
	public static function throws_exception(): void {
		throw new Exception( 'Test exception' );
	}
}
