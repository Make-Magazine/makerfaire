<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Querying\Field;

/**
 * Read model representing a single static choice on a Gravity Forms field.
 *
 * @since 2.14.0
 */
final class FieldChoice {
	/**
	 * The stored value (what gets persisted on the entry).
	 *
	 * @since 2.14.0
	 *
	 * @var string
	 */
	private string $value;

	/**
	 * The human-readable label.
	 *
	 * @since 2.14.0
	 *
	 * @var string
	 */
	private string $label;

	/**
	 * Creates the read model.
	 *
	 * @since 2.14.0
	 *
	 * @param string $value The stored value.
	 * @param string $label The human-readable label.
	 */
	public function __construct( string $value, string $label ) {
		$this->value = $value;
		$this->label = $label;
	}

	/**
	 * Returns the stored value.
	 *
	 * @since 2.14.0
	 *
	 * @return string The stored value.
	 */
	public function value(): string {
		return $this->value;
	}

	/**
	 * Returns the human-readable label.
	 *
	 * @since 2.14.0
	 *
	 * @return string The label.
	 */
	public function label(): string {
		return $this->label;
	}

	/**
	 * Returns a serializable representation of the choice.
	 *
	 * @since 2.14.0
	 *
	 * @return array{value:string,label:string} The serialized choice.
	 */
	public function to_array(): array {
		return $this->to_choice();
	}

	/**
	 * Returns the choice as a Choice-shaped array.
	 *
	 * @since 2.14.0
	 *
	 * @return array{value:string,label:string} The choice representation.
	 */
	public function to_choice(): array {
		return [
			'value' => $this->value,
			'label' => $this->label,
		];
	}
}
