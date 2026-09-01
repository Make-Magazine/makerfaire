<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Querying\User;

/**
 * Read model representing a WordPress user.
 *
 * @since 2.12.0
 */
final class User {
	/**
	 * The user ID.
	 *
	 * @since 2.12.0
	 *
	 * @var int
	 */
	private int $id;

	/**
	 * The display label for the user.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 */
	private string $label;

	/**
	 * Creates the read model.
	 *
	 * @since 2.12.0
	 *
	 * @param int    $id    The user ID.
	 * @param string $label The display label.
	 */
	public function __construct( int $id, string $label ) {
		$this->id    = $id;
		$this->label = $label;
	}

	/**
	 * Returns the user ID.
	 *
	 * @since 2.12.0
	 *
	 * @return int The user ID.
	 */
	public function id(): int {
		return $this->id;
	}

	/**
	 * Returns the display label.
	 *
	 * @since 2.12.0
	 *
	 * @return string The display label.
	 */
	public function label(): string {
		return $this->label;
	}

	/**
	 * Returns a serializable representation of the user.
	 *
	 * @since 2.12.0
	 *
	 * @return array{id:int,label:string} The serialized user.
	 */
	public function to_array(): array {
		return [
			'id'    => $this->id,
			'label' => $this->label,
		];
	}

	/**
	 * Returns the user as a Choice-shaped array.
	 *
	 * @since 2.12.0
	 *
	 * @return array{value:string,label:string} The choice representation.
	 */
	public function to_choice(): array {
		return [
			'value' => (string) $this->id,
			'label' => $this->label,
		];
	}
}
