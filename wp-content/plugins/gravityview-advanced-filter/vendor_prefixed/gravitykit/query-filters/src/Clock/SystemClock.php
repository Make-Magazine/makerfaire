<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 10-February-2025 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Clock;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Clock that uses the system time.
 * @since 2.0.0
 */
final class SystemClock implements Clock
{
    /**
     * @inheritDoc
     * @since 2.0.0
     */
    public function now() : DateTimeInterface
    {
        return new DateTimeImmutable();
    }
}
