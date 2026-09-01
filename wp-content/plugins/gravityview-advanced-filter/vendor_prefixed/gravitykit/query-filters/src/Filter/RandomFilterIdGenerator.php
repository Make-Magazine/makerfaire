<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Filter;

/**
 * Filter id generator that returns a random id.
 *
 * @since 2.0.0
 */
final class RandomFilterIdGenerator implements FilterIdGenerator {
	/**
	 * @inheritDoc
	 * @since 2.0.0
	 */
	public function get_id(): string {
		// Generate a cryptographically secure random ID.
		$characters       = '0123456789abcdefghijklmnopqrstuvwxyz';
		$charactersLength = strlen( $characters );
		$randomString     = '';

		for ( $i = 0; $i < 9; $i ++ ) {
			// Use random_int for cryptographically secure randomness
			$randomString .= $characters[ random_int( 0, $charactersLength - 1 ) ];
		}

		return $randomString;
	}
}
