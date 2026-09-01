<?php

namespace GravityKit\GravityView\QueryFilters\Querying\User;

use InvalidArgumentException;

/**
 * Value object describing a user search query.
 *
 * Construct via {@see self::search()} or {@see self::for_ids()}; tune via the `with_*` withers.
 *
 * @since 2.12.0
 */
final class UserCriteria {
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
	public const MAX_LIMIT = 100;

	/**
	 * Built-in `wp_users` columns that can be searched against.
	 *
	 * @since 2.12.0
	 */
	public const FIELD_LOGIN        = 'user_login';
	public const FIELD_EMAIL        = 'user_email';
	public const FIELD_NICENAME     = 'user_nicename';
	public const FIELD_DISPLAY_NAME = 'display_name';
	public const FIELD_URL          = 'user_url';

	/**
	 * `wp_usermeta` keys exposed as searchable/label fields.
	 *
	 * @since 2.12.0
	 */
	public const FIELD_FIRST_NAME = 'first_name';
	public const FIELD_LAST_NAME  = 'last_name';
	public const FIELD_NICKNAME   = 'nickname';

	/**
	 * Pseudo-field that matches the numeric user ID directly.
	 *
	 * @since 2.12.0
	 */
	public const FIELD_ID = 'id';

	/**
	 * Searchable fields allowed via {@see self::with_search_fields()}.
	 *
	 * @since 2.12.0
	 */
	private const ALLOWED_SEARCH_FIELDS = [
		self::FIELD_LOGIN,
		self::FIELD_EMAIL,
		self::FIELD_NICENAME,
		self::FIELD_DISPLAY_NAME,
		self::FIELD_URL,
		self::FIELD_FIRST_NAME,
		self::FIELD_LAST_NAME,
		self::FIELD_NICKNAME,
		self::FIELD_ID,
	];

	/**
	 * Fields that may be used as a display label. `FIELD_ID` and `FIELD_URL` are excluded because
	 * neither produces a meaningful human-readable label.
	 *
	 * @since 2.12.0
	 */
	private const ALLOWED_LABEL_FIELDS = [
		self::FIELD_DISPLAY_NAME,
		self::FIELD_LOGIN,
		self::FIELD_EMAIL,
		self::FIELD_NICENAME,
		self::FIELD_FIRST_NAME,
		self::FIELD_LAST_NAME,
		self::FIELD_NICKNAME,
	];

	/**
	 * Default searchable fields.
	 *
	 * @since 2.12.0
	 */
	public const DEFAULT_SEARCH_FIELDS = [
		self::FIELD_LOGIN,
		self::FIELD_EMAIL,
		self::FIELD_NICENAME,
		self::FIELD_DISPLAY_NAME,
		self::FIELD_URL,
		self::FIELD_FIRST_NAME,
		self::FIELD_LAST_NAME,
		self::FIELD_NICKNAME,
		self::FIELD_ID,
	];

	/**
	 * Default label fields, evaluated in order — first non-empty value wins.
	 *
	 * @since 2.12.0
	 */
	public const DEFAULT_LABEL_FIELDS = [
		self::FIELD_DISPLAY_NAME,
		self::FIELD_LOGIN,
	];

	/**
	 * The search needle.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 */
	private string $needle = '';

	/**
	 * The maximum number of users to return.
	 *
	 * @since 2.12.0
	 *
	 * @var int
	 */
	private int $limit = self::DEFAULT_LIMIT;

	/**
	 * The number of users to skip before returning results.
	 *
	 * @since 2.12.0
	 *
	 * @var int
	 */
	private int $offset = 0;

	/**
	 * Explicit list of user IDs to match.
	 *
	 * @since 2.12.0
	 *
	 * @var int[]
	 */
	private array $ids = [];

	/**
	 * Fields matched against the search needle.
	 *
	 * @since 2.12.0
	 *
	 * @var string[]
	 */
	private array $search_fields = self::DEFAULT_SEARCH_FIELDS;

	/**
	 * Ordered list of label candidates — first non-empty value wins.
	 *
	 * @since 2.12.0
	 *
	 * @var string[]
	 */
	private array $label_fields = self::DEFAULT_LABEL_FIELDS;

	/**
	 * Whether the search is restricted to users that have a role on the current site.
	 *
	 * @since 2.12.0
	 *
	 * @var bool
	 */
	private bool $current_site = true;

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
	 * @param int[] $ids The user IDs to resolve.
	 *
	 * @return self The criteria.
	 */
	public static function for_ids( array $ids ): self {
		$criteria        = new self();
		$criteria->ids   = array_values( array_unique( array_map( 'intval', $ids ) ) );
		$criteria->limit = min( count( $criteria->ids ) ?: 1, self::MAX_LIMIT );

		return $criteria;
	}

	/**
	 * Returns a copy with the given pagination applied.
	 *
	 * @since 2.12.0
	 *
	 * @param int $limit  The maximum number of results. Clamped to {@see self::MAX_LIMIT}.
	 * @param int $offset The number of users to skip.
	 *
	 * @return self The new criteria.
	 *
	 * @throws InvalidArgumentException When the limit or offset is out of range.
	 */
	public function with_pagination( int $limit, int $offset = 0 ): self {
		if ( $limit < 1 ) {
			throw new InvalidArgumentException( 'Limit must be greater than zero.' );
		}

		if ( $offset < 0 ) {
			throw new InvalidArgumentException( 'Offset cannot be negative.' );
		}

		$clone         = clone $this;
		$clone->limit  = min( $limit, self::MAX_LIMIT );
		$clone->offset = $offset;

		return $clone;
	}

	/**
	 * Returns a copy with the given searchable fields.
	 *
	 * @since 2.12.0
	 *
	 * @param string ...$fields The fields to search against.
	 *
	 * @return self The new criteria.
	 *
	 * @throws InvalidArgumentException When an unknown field is supplied.
	 */
	public function with_search_fields( string ...$fields ): self {
		$clone                = clone $this;
		$clone->search_fields = self::validate_fields( $fields, self::ALLOWED_SEARCH_FIELDS, 'search' );

		return $clone;
	}

	/**
	 * Returns a copy with the given label candidates.
	 *
	 * @since 2.12.0
	 *
	 * @param string ...$fields The ordered label candidates — first non-empty value wins.
	 *
	 * @return self The new criteria.
	 *
	 * @throws InvalidArgumentException When an unknown field is supplied or the list is empty.
	 */
	public function with_label_fields( string ...$fields ): self {
		$validated = self::validate_fields( $fields, self::ALLOWED_LABEL_FIELDS, 'label' );

		if ( $validated === [] ) {
			throw new InvalidArgumentException( 'At least one label field is required.' );
		}

		$clone               = clone $this;
		$clone->label_fields = $validated;

		return $clone;
	}

	/**
	 * Returns a copy that toggles the current-site role scope.
	 *
	 * @since 2.12.0
	 *
	 * @param bool $enabled Whether the result set must contain a role on the current site.
	 *
	 * @return self The new criteria.
	 */
	public function with_current_site( bool $enabled = true ): self {
		$clone               = clone $this;
		$clone->current_site = $enabled;

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
	 * Returns the number of users to skip.
	 *
	 * @since 2.12.0
	 *
	 * @return int The offset.
	 */
	public function offset(): int {
		return $this->offset;
	}

	/**
	 * Returns the explicit ID filter.
	 *
	 * @since 2.12.0
	 *
	 * @return int[] The IDs.
	 */
	public function ids(): array {
		return $this->ids;
	}

	/**
	 * Returns the searchable field list.
	 *
	 * @since 2.12.0
	 *
	 * @return string[] The fields to search against.
	 */
	public function search_fields(): array {
		return $this->search_fields;
	}

	/**
	 * Returns the ordered label candidate list.
	 *
	 * @since 2.12.0
	 *
	 * @return string[] The label candidates.
	 */
	public function label_fields(): array {
		return $this->label_fields;
	}

	/**
	 * Whether the search is restricted to users that have a role on the current site.
	 *
	 * @since 2.12.0
	 *
	 * @return bool True when current-site scoping is enabled.
	 */
	public function current_site(): bool {
		return $this->current_site;
	}

	/**
	 * Validate that every entry in the supplied list is part of the allowed set.
	 *
	 * @since 2.12.0
	 *
	 * @param string[] $fields  The fields to validate.
	 * @param string[] $allowed The allowed values.
	 * @param string   $context Human-readable context for the error message.
	 *
	 * @return string[] The validated, de-duplicated fields.
	 *
	 * @throws InvalidArgumentException When an unknown field is supplied.
	 */
	private static function validate_fields( array $fields, array $allowed, string $context ): array {
		$out = [];
		foreach ( $fields as $field ) {
			if ( ! in_array( $field, $allowed, true ) ) {
				throw new InvalidArgumentException( sprintf( 'Unknown %s field "%s".', $context, $field ) );
			}

			if ( ! in_array( $field, $out, true ) ) {
				$out[] = $field;
			}
		}

		return $out;
	}
}
