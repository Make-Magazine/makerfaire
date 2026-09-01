<?php
/**
 * The Internal Source class.
 *
 * Data that comes from within the View itself (like custom content).
 *
 * @package GravityKit\GravityView\Field
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field;

use function gravityview;

/**
 * The \GV\Internal_Source class.
 *
 * Data that comes from within the View itself (like custom content).
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Field namespace.
 */
class InternalSource extends \GV\Source {

	/**
	 * @var string The identifier of the backend used for this source.
	 *
	 * @api
	 * @since 2.0
	 */
	public static $backend = self::BACKEND_INTERNAL;

	/**
	 * Get a \GV\Field by Field ID for this data source.
	 *
	 * @param int $field_id The internal field ID (custom content, etc.)
	 *
	 * @return \GV\Field|null The requested field or null if not found.
	 */
	public static function get_field( /** varargs */ ) {
		$args = func_get_args();

		if ( ! is_array( $args ) || 1 != count( $args ) ) {
			gravityview()->log->error( '{source} expects 1 arguments for ::get_field ($field_id)', [ 'source' => __CLASS__ ] );
			return null;
		}

		/** Unwrap the arguments. */
		list( $field_id ) = $args;

		/** Wrap it up into a \GV\Field. */
		return \GV\Internal_Field::by_id( $field_id );
	}
}
