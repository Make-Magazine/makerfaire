<?php

namespace GravityKit\GravityView\Foundation\Scheduler\Contracts;

/**
 * Schedulable interface. Used to define job classes.
 */
interface Schedulable {
	/**
	 * Gets the action name.
	 *
	 * @return string
	 */
	public function name(): string;

	/**
	 * If the action unique.
	 *
	 * @return bool
	 */
	public function unique(): bool;

	/**
	 * Gets the action priority.
	 *
	 * @return int
	 */
	public function priority(): int;

	/**
	 * Converts the object to an array.
	 *
	 * @return array
	 */
	public function to_array(): array;
}
