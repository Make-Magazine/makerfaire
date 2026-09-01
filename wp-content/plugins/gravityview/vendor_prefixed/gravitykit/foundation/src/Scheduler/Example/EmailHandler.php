<?php
/**
 * Email Handler example class.
 */

namespace GravityKit\GravityView\Foundation\Scheduler\Example;

use GravityKit\GravityView\Foundation\Scheduler\Traits\LoggerTrait;

class EmailHandler {
	use LoggerTrait;

	/**
	 * Send an email.
	 *
	 * @param array $args The arguments.
	 * @return void
	 */
	public static function send( array $args ): void {
		$me = new self();

		foreach ( range( 1, 3 ) as $item ) {
			$me->logger()->debug( __METHOD__ . ': ', [ 'JOB name: ' . $args['job_level_argument'], 'item: ' . $item ] );
			sleep( 2 );
		}
	}
}
