<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Querying\Field\Exception;

use RuntimeException;

/**
 * Thrown when the requested form cannot be located.
 *
 * @since 2.14.0
 */
final class FormNotFoundException extends RuntimeException {
	/**
	 * Construct an exception describing the missing form.
	 *
	 * @since 2.14.0
	 *
	 * @param int $form_id The form ID that was requested.
	 *
	 * @return self The exception.
	 */
	public static function for_id( int $form_id ): self {
		return new self( sprintf( 'Form %d was not found.', $form_id ) );
	}
}
