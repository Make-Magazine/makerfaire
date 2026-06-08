<?php
/**
 * Schedule example.
 *
 * @license GPL-2.0-or-later
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Scheduler\Example;

use Exception;
use GravityKit\GravityView\Foundation\Core as GravityKitFoundation;
use GravityKit\GravityView\Foundation\Scheduler\Traits\LoggerTrait;

// phpcs:disable

class ScheduleExample {

	use LoggerTrait;

	/**
	 * Example constructor.
	 */
	public function __construct() {
//	    add_action( 'init', [ $this, 'schedule_example' ] );
//	    add_action( 'init', [ $this, 'schedule_example_with_dependencies' ] );
//		add_action( 'init', [ $this, 'get_pending_instances_example' ] );
	}

	/**
	 * Schedules the job.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function schedule_example() {
		$job_name = 'example_job';

		// Create a job. Job is a container for the tasks.
		$job = GravityKitFoundation::scheduler()
		                           ->job()
		                           ->create( $job_name );
		// Now create the job tasks.
		$job->task()
			// Create tasks.
			->create(
				'send_confirmation_email',
				[ EmailHandler::class, 'send' ]
			)
		    ->create(
			    'notify_admin',
			    [ AdminNotifier::class, 'notify' ],
			    [ 'task_level_argument' => $job_name ] // You can provide args when creating the task.
		    )
			// Enqueue created tasks.
			->queue( 'send_confirmation_email', [ 'job_level_argument' => $job_name ] ) // You can provide args when enqueuing the task.
		    ->queue( 'notify_admin' );

		// Schedule the job.
		$result = $job->schedule_single( time() + 2 );

		if ( $result->has_warning() ) {
			$result->cancel();
			$this->logger()->warning( 'Scheduling warning: ' . $result->warning() );
		}
	}

	/**
	 * Schedules the job.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function schedule_example_with_dependencies() {
		$job_name = 'example_job_dependencies';

		// Create a job. Job is a container for the tasks.
		$job = GravityKitFoundation::scheduler()
		                           ->job()
		                           ->create( $job_name );
		// Now create the job tasks.
		$job->task()
			// Create tasks.
			->create(
				'send_confirmation_email',
				[ EmailHandler::class, 'send' ]
			)
		    ->create(
			    'test_failed_task',
			    [ AdminNotifier::class, 'throws_exception' ]
		    )
			->create(
				'test_dependent_task',
				[ AdminNotifier::class, 'dependent_task' ],
				[],
				[ 'test_failed_task' ] // This task depends on the test_failed_task.
			)
		    ->create(
			    'notify_admin',
			    [ AdminNotifier::class, 'notify' ],
			    [ 'job' => $job_name ] // You can provide args when creating the task.
		    )
			// Enqueue created tasks.
			->queue( 'send_confirmation_email', [ 'job' => $job_name ] ) // You can provide args when enqueuing the task.
			->queue( 'test_failed_task' ) // This task will fail.
			->queue( 'test_dependent_task' ) // This task will be skipped.
		    ->queue( 'notify_admin' );

		// Schedule the job.
		$result = $job->schedule_single( time() + 2 );

		if ( $result->has_warning() ) {
			$result->cancel();
			$this->logger()->warning( 'Scheduling warning: ' . $result->warning() );
		}
	}

	/**
	 * Gets the job instances using job name.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function get_pending_instances_example(): void {
		$pending = GravityKitFoundation::scheduler()
		                               ->history( 'example_job_with_2_tasks' )
		                               ->pending();

		$this->logger()->debug( 'Pending job instances:', [ 'pending' => print_r( $pending, true ) ] );
	}
}

// phpcs:enable
