<?php

namespace GravityKit\GravityView\Foundation\Scheduler\Contracts;

/**
 * Used to restore the scheduled jobs from the database.
 */
interface Restorable {
	/**
	 * Restores the object from the database.
	 *
	 * @param array $args The database arguments.
	 *
	 * @return object
	 */
	public static function restore( array $args ): object;
}
