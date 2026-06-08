<?php
/**
 * @license MIT
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\QueryFilters\Clock;

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
