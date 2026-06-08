<?php
/**
 * @license MIT
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\QueryFilters\Querying\Form;

use InvalidArgumentException;

/**
 * Value object describing a form search query.
 *
 * Construct via {@see self::search()} or {@see self::for_ids()}; tune via the `with_*` withers.
 *
 * @since 2.12.0
 */
final class FormCriteria {
	/**
	 * The default number of results returned.
	 *
	 * @since 2.12.0
	 */
	public const DEFAULT_LIMIT = 10;

	/**
	 * The maximum number of results allowed in a single query.
	 *
	 * @since 2.12.0
	 */
	public const MAX_LIMIT = 1000;

	/**
	 * The search needle matched against the form title.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 */
	private string $needle = '';

	/**
	 * The maximum number of forms to return.
	 *
	 * @since 2.12.0
	 *
	 * @var int
	 */
	private int $limit = self::DEFAULT_LIMIT;

	/**
	 * The number of forms to skip before returning results.
	 *
	 * @since 2.12.0
	 *
	 * @var int
	 */
	private int $offset = 0;

	/**
	 * Whether to include trashed forms in the result.
	 *
	 * @since 2.12.0
	 *
	 * @var bool
	 */
	private bool $include_trash = false;

	/**
	 * Explicit list of form IDs to match.
	 *
	 * @since 2.12.0
	 *
	 * @var int[]
	 */
	private array $ids = [];

	/**
	 * Whether a numeric search needle should also match against the form ID.
	 *
	 * @since 2.12.0
	 *
	 * @var bool
	 */
	private bool $search_ids = true;

	/**
	 * Private — use {@see self::search()} or {@see self::for_ids()}.
	 *
	 * @since 2.12.0
	 */
	private function __construct() {
	}

	/**
	 * Build a text-search criteria.
	 *
	 * @since 2.12.0
	 *
	 * @param string $needle The search needle.
	 *
	 * @return self The criteria.
	 */
	public static function search( string $needle = '' ): self {
		$criteria         = new self();
		$criteria->needle = trim( $needle );

		return $criteria;
	}

	/**
	 * Build an ID-hydration criteria.
	 *
	 * @since 2.12.0
	 *
	 * @param int[] $ids The form IDs to resolve.
	 *
	 * @return self The criteria.
	 */
	public static function for_ids( array $ids ): self {
		$criteria        = new self();
		$criteria->ids   = array_values( array_unique( array_map( 'intval', $ids ) ) );
		$criteria->limit = min( max( self::DEFAULT_LIMIT, count( $criteria->ids ) ?: 1 ), self::MAX_LIMIT );

		return $criteria;
	}

	/**
	 * Returns a copy with the given pagination applied.
	 *
	 * @since 2.12.0
	 *
	 * @param int $limit  The maximum number of results. Clamped to {@see self::MAX_LIMIT}.
	 * @param int $offset The number of forms to skip.
	 *
	 * @return self The new criteria.
	 *
	 * @throws InvalidArgumentException When the limit or offset is out of range.
	 */
	public function with_pagination( int $limit, int $offset = 0 ): self {
		if ( 1 > $limit ) {
			throw new InvalidArgumentException( 'Limit must be greater than zero.' );
		}

		if ( 0 > $offset ) {
			throw new InvalidArgumentException( 'Offset cannot be negative.' );
		}

		$clone         = clone $this;
		$clone->limit  = min( $limit, self::MAX_LIMIT );
		$clone->offset = $offset;

		return $clone;
	}

	/**
	 * Returns a copy that toggles inclusion of trashed forms.
	 *
	 * @since 2.12.0
	 *
	 * @param bool $enabled Whether trashed forms should also be returned.
	 *
	 * @return self The new criteria.
	 */
	public function with_trash( bool $enabled = true ): self {
		$clone                = clone $this;
		$clone->include_trash = $enabled;

		return $clone;
	}

	/**
	 * Returns a copy where a numeric search needle no longer also matches the form ID.
	 *
	 * @since 2.12.0
	 *
	 * @return self The new criteria.
	 */
	public function without_id_matching(): self {
		$clone             = clone $this;
		$clone->search_ids = false;

		return $clone;
	}

	/**
	 * Returns the search needle.
	 *
	 * @since 2.12.0
	 *
	 * @return string The search needle.
	 */
	public function needle(): string {
		return $this->needle;
	}

	/**
	 * Returns the maximum number of results.
	 *
	 * @since 2.12.0
	 *
	 * @return int The result limit.
	 */
	public function limit(): int {
		return $this->limit;
	}

	/**
	 * Returns the number of forms to skip.
	 *
	 * @since 2.12.0
	 *
	 * @return int The offset.
	 */
	public function offset(): int {
		return $this->offset;
	}

	/**
	 * Whether to include trashed forms.
	 *
	 * @since 2.12.0
	 *
	 * @return bool True when trashed forms should be included.
	 */
	public function include_trash(): bool {
		return $this->include_trash;
	}

	/**
	 * Returns the explicit ID filter, or an empty array when none was set.
	 *
	 * @since 2.12.0
	 *
	 * @return int[] The IDs.
	 */
	public function ids(): array {
		return $this->ids;
	}

	/**
	 * Whether a numeric search needle should also match the form ID.
	 *
	 * @since 2.12.0
	 *
	 * @return bool True when ID matching is enabled.
	 */
	public function search_ids(): bool {
		return $this->search_ids;
	}
}
