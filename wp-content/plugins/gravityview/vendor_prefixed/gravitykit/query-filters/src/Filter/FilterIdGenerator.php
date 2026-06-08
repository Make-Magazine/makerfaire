<?php
/**
 * @license MIT
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\QueryFilters\Filter;

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
