<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 11-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Filter;

/**
 * Filter id generator that returns a random id.
 * @since 2.0.0
 */
final class RandomFilterIdGenerator implements FilterIdGenerator {
	/**
	 * @inheritDoc
	 * @since 2.0.0
	 */
	public function get_id(): string {
		return wp_generate_password( 9, false );
	}
}
