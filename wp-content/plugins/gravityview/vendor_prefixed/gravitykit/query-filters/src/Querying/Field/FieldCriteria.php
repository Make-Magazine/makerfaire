<?php

namespace GravityKit\GravityView\QueryFilters\Querying\Field;

use InvalidArgumentException;

/**
 * Value object describing a field-choice search query.
 *
 * @since 2.14.0
 */
final class FieldCriteria {
	/**
	 * The default number of results returned.
	 *
	 * @since 2.14.0
	 *
	 * @var int
	 */
	public const DEFAULT_LIMIT = 10;

	/**
	 * The maximum number of results allowed in a single query.
	 *
	 * @since 2.14.0
	 *
	 * @var int
	 */
	public const MAX_LIMIT = 1000;

	/**
	 * The owning form ID.
	 *
	 * @since 2.14.0
	 *
	 * @var int
	 */
	private int $form_id;

	/**
	 * The field ID (e.g. `5` or `5.3` for sub-inputs).
	 *
	 * @since 2.14.0
	 *
	 * @var string
	 */
	private string $field_id;

	/**
	 * The search needle matched against label and value.
	 *
	 * @since 2.14.0
	 *
	 * @var string
	 */
	private string $search;

	/**
	 * The maximum number of choices to return.
	 *
	 * @since 2.14.0
	 *
	 * @var int
	 */
	private int $limit;

	/**
	 * The number of choices to skip before returning results.
	 *
	 * @since 2.14.0
	 *
	 * @var int
	 */
	private int $offset;

	/**
	 * Explicit list of values to match. When non-empty, narrows the result set to these values;
	 * `search` and pagination still apply on top.
	 *
	 * @since 2.14.0
	 *
	 * @var string[]
	 */
	private array $values;

	/**
	 * Count that triggers the search endpoint; a source may stop counting once it reaches this when
	 * an exact count is too expensive. Null asks for an exact count.
	 *
	 * @since 2.14.0
	 *
	 * @var int|null
	 */
	private ?int $threshold = null;

	/**
	 * Creates the criteria.
	 *
	 * @since 2.14.0
	 *
	 * @param int      $form_id  The owning form ID.
	 * @param string   $field_id The field ID.
	 * @param string   $search   The search needle.
	 * @param int      $limit    The maximum number of results.
	 * @param int      $offset   The number of choices to skip.
	 * @param string[] $values   Optional explicit value filter.
	 *
	 * @throws InvalidArgumentException If form_id, field_id, limit, or offset are invalid.
	 */
	public function __construct(
		int $form_id,
		string $field_id,
		string $search = '',
		int $limit = self::DEFAULT_LIMIT,
		int $offset = 0,
		array $values = []
	) {
		if ( $form_id < 1 ) {
			throw new InvalidArgumentException( 'Form ID must be a positive integer.' );
		}

		$field_id = trim( $field_id );
		if ( '' === $field_id ) {
			throw new InvalidArgumentException( 'Field ID cannot be empty.' );
		}

		if ( $limit < 1 ) {
			throw new InvalidArgumentException( 'Limit must be greater than zero.' );
		}

		if ( $offset < 0 ) {
			throw new InvalidArgumentException( 'Offset cannot be negative.' );
		}

		$this->form_id  = $form_id;
		$this->field_id = $field_id;
		$this->search   = trim( $search );
		$this->limit    = min( $limit, self::MAX_LIMIT );
		$this->offset   = $offset;
		$this->values   = self::sanitize_values( $values );
	}

	/**
	 * Convenience constructor for value-based hydration — bypasses pagination entirely.
	 *
	 * @since 2.14.0
	 *
	 * @param int      $form_id  The owning form ID.
	 * @param string   $field_id The field ID.
	 * @param string[] $values   The values to hydrate.
	 *
	 * @return self The criteria.
	 */
	public static function for_values( int $form_id, string $field_id, array $values ): self {
		$count = max( count( $values ), 1 );

		return new self( $form_id, $field_id, '', max( self::DEFAULT_LIMIT, $count ), 0, $values );
	}

	/**
	 * Returns the owning form ID.
	 *
	 * @since 2.14.0
	 *
	 * @return int The form ID.
	 */
	public function form_id(): int {
		return $this->form_id;
	}

	/**
	 * Returns the field ID.
	 *
	 * @since 2.14.0
	 *
	 * @return string The field ID.
	 */
	public function field_id(): string {
		return $this->field_id;
	}

	/**
	 * Returns the search needle.
	 *
	 * @since 2.14.0
	 *
	 * @return string The search needle.
	 */
	public function search(): string {
		return $this->search;
	}

	/**
	 * Returns the maximum number of results.
	 *
	 * @since 2.14.0
	 *
	 * @return int The result limit.
	 */
	public function limit(): int {
		return $this->limit;
	}

	/**
	 * Returns the number of choices to skip.
	 *
	 * @since 2.14.0
	 *
	 * @return int The offset.
	 */
	public function offset(): int {
		return $this->offset;
	}

	/**
	 * Returns the explicit value filter.
	 *
	 * @since 2.14.0
	 *
	 * @return string[] The values.
	 */
	public function values(): array {
		return $this->values;
	}

	/**
	 * Returns the count threshold, or null when an exact count is requested.
	 *
	 * @since 2.14.0
	 *
	 * @return int|null The threshold.
	 */
	public function threshold(): ?int {
		return $this->threshold;
	}

	/**
	 * Returns a copy with the count threshold set.
	 *
	 * @since 2.14.0
	 *
	 * @param int|null $threshold The threshold, or null for an exact count.
	 *
	 * @return self The copy.
	 *
	 * @throws InvalidArgumentException If a non-null threshold is less than one.
	 */
	public function with_threshold( ?int $threshold ): self {
		if ( null !== $threshold && $threshold < 1 ) {
			throw new InvalidArgumentException( 'Threshold must be greater than zero.' );
		}

		$clone            = clone $this;
		$clone->threshold = $threshold;

		return $clone;
	}

	/**
	 * Normalize the raw values list into deduplicated, trimmed strings.
	 *
	 * @since 2.14.0
	 *
	 * @param mixed[] $values The raw values.
	 *
	 * @return string[] The sanitized values.
	 */
	private static function sanitize_values( array $values ): array {
		$out = [];
		foreach ( $values as $value ) {
			if ( ! is_scalar( $value ) ) {
				continue;
			}

			$trimmed = trim( (string) $value );
			if ( '' === $trimmed || in_array( $trimmed, $out, true ) ) {
				continue;
			}

			$out[] = $trimmed;
		}

		return $out;
	}
}
