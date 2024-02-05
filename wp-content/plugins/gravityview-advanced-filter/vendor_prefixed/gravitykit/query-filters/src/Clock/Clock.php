<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 01-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Clock;

use DateTimeInterface;

/**
 * Object that represents a clock.
 * @since 2.0.0
 */
interface Clock {
	/**
	 * Returns the current time as a DateTimeImmutable Object
	 * @since 2.0.0
	 */
	public function now(): DateTimeInterface;
}
