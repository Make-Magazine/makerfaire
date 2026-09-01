<?php

namespace GravityKit\GravityView\QueryFilters\Querying\Field\Exception;

use RuntimeException;

/**
 * Thrown when the field's type does not expose a static choice list.
 *
 * @since 2.14.0
 */
final class UnsupportedFieldTypeException extends RuntimeException {
	/**
	 * Construct an exception describing the unsupported field.
	 *
	 * @since 2.14.0
	 *
	 * @param string $field_type The unsupported field type.
	 *
	 * @return self The exception.
	 */
	public static function for_type( string $field_type ): self {
		return new self( sprintf( 'Field type "%s" does not expose a static choice list.', $field_type ) );
	}
}
