<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Querying\Field\Exception;

use RuntimeException;

/**
 * Thrown when the requested field cannot be located on its owning form.
 *
 * @since 2.14.0
 */
final class FieldNotFoundException extends RuntimeException {
	/**
	 * Construct an exception describing the missing field.
	 *
	 * @since 2.14.0
	 *
	 * @param int    $form_id  The form ID.
	 * @param string $field_id The field ID that was requested.
	 *
	 * @return self The exception.
	 */
	public static function for_field( int $form_id, string $field_id ): self {
		return new self( sprintf( 'Field "%s" was not found on form %d.', $field_id, $form_id ) );
	}
}
