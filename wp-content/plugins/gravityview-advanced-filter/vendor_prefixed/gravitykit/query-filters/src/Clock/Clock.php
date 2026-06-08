<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 28-April-2026 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Clock;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Object that represents a clock.
 * @since 2.0.0
 */
interface Clock {
	/**
	 * Returns the current time as a DateTimeImmutable object.
	 *
	 * @since 2.0.0
	 *
	 * @return DateTimeImmutable
	 */
	public function now(): DateTimeInterface;
}
