<?php
/**
 * @license MIT
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\QueryFilters\Querying\Form;

/**
 * Read model representing a Gravity Forms form.
 *
 * @since 2.12.0
 */
final class Form {
	/**
	 * The form ID.
	 *
	 * @since 2.12.0
	 *
	 * @var int
	 */
	private int $id;

	/**
	 * The form title.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 */
	private string $title;

	/**
	 * Creates the read model.
	 *
	 * @since 2.12.0
	 *
	 * @param int    $id    The form ID.
	 * @param string $title The form title.
	 */
	public function __construct( int $id, string $title ) {
		$this->id    = $id;
		$this->title = $title;
	}

	/**
	 * Returns the form ID.
	 *
	 * @since 2.12.0
	 *
	 * @return int The form ID.
	 */
	public function id(): int {
		return $this->id;
	}

	/**
	 * Returns the form title.
	 *
	 * @since 2.12.0
	 *
	 * @return string The form title.
	 */
	public function title(): string {
		return $this->title;
	}

	/**
	 * Returns a serializable representation of the form.
	 *
	 * @since 2.12.0
	 *
	 * @return array{id:int,title:string} The serialized form.
	 */
	public function to_array(): array {
		return [
			'id'    => $this->id,
			'title' => $this->title,
		];
	}

	/**
	 * Returns the form as a Choice-shaped array.
	 *
	 * @since 2.12.0
	 *
	 * @return array{value:string,label:string} The choice representation.
	 */
	public function to_choice(): array {
		return [
			'value' => (string) $this->id,
			'label' => $this->title,
		];
	}
}
