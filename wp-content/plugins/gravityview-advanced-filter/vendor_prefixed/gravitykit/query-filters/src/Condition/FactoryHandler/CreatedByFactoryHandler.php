<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Condition\FactoryHandler;

use GF_Query_Condition;
use GravityKit\AdvancedFilter\QueryFilters\Condition\Created_By_Condition;

/**
 * Handles a `created_by` filter with a string search value.
 *
 * This handler creates a {@see Created_By_Condition} when the filter key is `created_by`
 * and the value is a non-empty, non-numeric string. Numeric strings (e.g., "42") and
 * empty values are declined so built-in logic can handle them as user IDs.
 *
 * @since 2.10
 */
final class CreatedByFactoryHandler {
	/**
	 * Handles the filter.
	 *
	 * @since 2.10
	 *
	 * @param array $filter The filter array with resolved form_id.
	 *
	 * @return false|GF_Query_Condition
	 */
	public function __invoke( array $filter ) {
		if ( 'created_by' !== ( $filter['key'] ?? null ) ) {
			return false;
		}

		$value = $filter['value'] ?? null;
		if ( ! is_string( $value ) || ctype_digit( $value ) || empty( $value ) ) {
			return false;
		}

		return new Created_By_Condition( $value, (int) $filter['form_id'] );
	}
}
