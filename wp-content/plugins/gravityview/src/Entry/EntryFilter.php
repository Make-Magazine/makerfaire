<?php
/**
 * Entry filtering settings.
 *
 * @package GravityKit\GravityView\Entry
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry;

/**
 * Entry filtering settings.
 *
 * A proposed long-term vision for this would be an API
 *  similar to the new-school ORMs out there.
 *
 * new Entry_Filter(
 *  Field::by_id( 3 )->eq( 99 )->and(
 *      Field::by_id( 4 )->neq( null )->or( Field::by_id( 4 )->lte( 0 ) )
 *  )->and(
 *      Field::by_id( 5 )->like( "%search%" )->and( Field::by_id( 6 )->between( $t, $f ) )
 *  )
 * );
 *
 * Very flexible in code, but unserialization could be a pain in the neck.
 *
 * For now we use the Gravity Forms backend for this, since developing an ORM
 *  will take us another year :)
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Entry namespace.
 */
abstract class EntryFilter {
}
