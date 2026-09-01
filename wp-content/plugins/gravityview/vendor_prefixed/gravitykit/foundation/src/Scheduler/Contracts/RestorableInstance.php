<?php

namespace GravityKit\GravityView\Foundation\Scheduler\Contracts;

/**
 * Used to restore the scheduled jobs from the database.
 */
interface RestorableInstance {
	/**
	 * Restores the object from the database.
	 *
	 * @param array $args The database arguments.
	 * @param int   $instance_id The instance ID.
	 *
	 * @return object
	 */
	public static function restore( array $args, int $instance_id ): object;
}
