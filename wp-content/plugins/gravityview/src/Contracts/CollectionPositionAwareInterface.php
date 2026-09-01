<?php
/**
 * Interface for collections that support position filtering.
 *
 * @package GravityKit\GravityView\Contracts
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Contracts;

/**
 * Represents a collection that has a position (fields, widgets and search fields).
 *
 * @since 2.42
 * @since 3.0.0 Migrated to GravityKit\GravityView\Contracts namespace.
 */
interface CollectionPositionAwareInterface {
	/**
	 * Get a copy of this collection filtered by position.
	 *
	 * @since 2.42
	 *
	 * @param string $position The position to get the fields for.
	 *                         Can be a wildcard *
	 *
	 * @return static|Collection A filtered collection, filtered by position.
	 */
	public function by_position( $position );
}
