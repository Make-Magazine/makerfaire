<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 01-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Filter;

/**
 * Generates a filter id.
 * @since 2.0.0
 */
interface FilterIdGenerator {
	/**
	 * Returns the filter id.
	 * @since 2.0.0
	 * @return string
	 */
	public function get_id(): string;
}
