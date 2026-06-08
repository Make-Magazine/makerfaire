<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 28-April-2026 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Filter\Source;

use GravityKit\AdvancedFilter\QueryFilters\Filter\Filter;

/**
 * Interface for filter sources that convert string values into Filter objects.
 *
 * @since 2.9.0
 */
interface FilterSource {
	/**
	 * Converts a string value into a Filter object.
	 *
	 * @since 2.9.0
	 *
	 * @param string $value The input value to convert.
	 *
	 * @return Filter|null The Filter object or null if conversion fails.
	 */
	public function __invoke( string $value ): ?Filter;
}
