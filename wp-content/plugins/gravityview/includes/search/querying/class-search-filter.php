<?php

namespace GV\Search\Querying;

/**
 * Immutable Value Object representing a search filter.
 *
 * Use static factory methods to create instances. Direct instantiation via `new` is not allowed.
 *
 * @since $ver$
 */
final class Search_Filter {
	/**
	 * AND mode constant.
	 *
	 * @since $ver$
	 */
	public const MODE_AND = 'and';

	/**
	 * OR mode constant.
	 *
	 * @since $ver$
	 */
	public const MODE_OR = 'or';

	/**
	 * The default operator value.
	 *
	 * @since $ver$
	 *
	 * @var string
	 */
	public const DEFAULT_OPERATOR = 'contains';

	/**
	 * The field key.
	 *
	 * @since $ver$
	 *
	 * @var null|string
	 */
	private ?string $key = null;

	/**
	 * The filter value.
	 *
	 * @since $ver$
	 *
	 * @var mixed
	 */
	private $value;

	/**
	 * The comparison operator.
	 *
	 * @since $ver$
	 *
	 * @var string
	 */
	private string $operator = self::DEFAULT_OPERATOR;

	/**
	 * The original request parameter key (for BC compatibility).
	 *
	 * @since $ver$
	 *
	 * @var string|null
	 */
	private ?string $request_key = null;

	/**
	 * The form ID.
	 *
	 * @since $ver$
	 *
	 * @var int|null
	 */
	private ?int $form_id = null;

	/**
	 * The field ID (when extracted from a compound key).
	 *
	 * @since $ver$
	 *
	 * @var string|null
	 */
	private ?string $field_id = null;

	/**
	 * Whether this filter is required (for global search).
	 *
	 * @since $ver$
	 *
	 * @var bool
	 */
	private bool $required = false;

	/**
	 * Whether this filter uses numeric comparison.
	 *
	 * @since $ver$
	 *
	 * @var bool
	 */
	private bool $is_numeric = false;

	/**
	 * The logical mode for groups ('and' or 'or').
	 *
	 * @since $ver$
	 *
	 * @var string|null
	 */
	private ?string $mode = null;

	/**
	 * Nested filters for groups.
	 *
	 * @since $ver$
	 *
	 * @var Search_Filter[]
	 */
	private array $conditions = [];

	/**
	 * The allowed operators.
	 *
	 * @since $ver$
	 *
	 * @var array|string[]
	 */
	private array $allowed_operators;

	/**
	 * Whether this filter's value has been normalized.
	 *
	 * @since $ver$
	 *
	 * @var bool
	 */
	private bool $normalized = false;

	/**
	 * Generic context storage for field-specific metadata.
	 *
	 * @since $ver$
	 *
	 * @var array<string, mixed>
	 */
	private array $context = [];

	/**
	 * Private constructor - use static factory methods.
	 *
	 * @since $ver$
	 */
	private function __construct() {
	}

	/**
	 * Creates a Search_Filter from an array.
	 *
	 * @since $ver$
	 *
	 * @param array $data The filter data array.
	 *
	 * @return self The Search_Filter instance.
	 *
	 * @throws \InvalidArgumentException When the data is neither a valid group nor a valid leaf filter.
	 */
	public static function from_array( array $data ): self {
		$is_group = isset( $data['mode'] ) || isset( $data['conditions'] );
		$is_leaf  = isset( $data['key'] ) && array_key_exists( 'value', $data );

		if ( ! $is_group && ! $is_leaf ) {
			throw new \InvalidArgumentException(
				'Invalid filter data: must contain either mode/conditions (group) or key/value (leaf filter).'
			);
		}

		$filter = new self();

		// Group handling.
		if ( $is_group ) {
			$filter->mode       = self::normalize_mode( $data['mode'] ?? self::MODE_OR );
			$filter->conditions = array_map(
				static fn( array $condition ): self => self::from_array( $condition ),
				$data['conditions'] ?? []
			);

			return $filter;
		}

		// Leaf filter properties.
		$filter->key   = (string) $data['key'];
		$filter->value = $data['value'];
		$operator      = (string) ( $data['operator'] ?? '' );

		$filter->operator = $operator ?: self::DEFAULT_OPERATOR;

		// BC compatibility.
		$filter->request_key = isset( $data['request_key'] ) ? (string) $data['request_key'] : null;

		// Optional context.
		$filter->form_id  = isset( $data['form_id'] ) ? (int) $data['form_id'] : null;
		$filter->field_id = isset( $data['field_id'] ) ? (string) $data['field_id'] : null;

		// Flags.
		$filter->required   = (bool) ( $data['required'] ?? false );
		$filter->is_numeric = (bool) ( $data['is_numeric'] ?? false );

		return $filter;
	}

	/**
	 * Creates a leaf filter with the minimum required properties.
	 *
	 * @since $ver$
	 *
	 * @param string      $key      The field key.
	 * @param mixed       $value    The filter value.
	 * @param string|null $operator The comparison operator.
	 *
	 * @return self The Search_Filter instance.
	 */
	public static function create( string $key, $value, ?string $operator = null ): self {
		return self::from_array(
			[
				'key'      => $key,
				'value'    => $value,
				'operator' => $operator,
			]
		);
	}

	/**
	 * Creates a filter group with the specified mode.
	 *
	 * @since $ver$
	 *
	 * @param string $mode       The logical mode ('and' or 'or').
	 * @param self   ...$filters The filters to group.
	 *
	 * @return self The Search_Filter group.
	 */
	public static function group( string $mode, self ...$filters ): self {
		$filter = new self();

		$filter->mode       = self::normalize_mode( $mode );
		$filter->conditions = $filters;

		return $filter;
	}

	/**
	 * Creates an AND group.
	 *
	 * @since $ver$
	 *
	 * @param self ...$filters The filters to combine with AND.
	 *
	 * @return self The Search_Filter group.
	 */
	public static function and( self ...$filters ): self {
		return self::group( self::MODE_AND, ...$filters );
	}

	/**
	 * Creates an OR group.
	 *
	 * @since $ver$
	 *
	 * @param self ...$filters The filters to combine with OR.
	 *
	 * @return self The Search_Filter group.
	 */
	public static function or( self ...$filters ): self {
		return self::group( self::MODE_OR, ...$filters );
	}

	/**
	 * Normalizes the mode value.
	 *
	 * @since $ver$
	 *
	 * @param string $mode The mode to normalize.
	 *
	 * @return string The normalized mode ('and' or 'or').
	 */
	private static function normalize_mode( string $mode ): string {
		$mode = strtolower( trim( $mode ) );

		// Support legacy 'all'/'any' terminology.
		if ( 'all' === $mode ) {
			return self::MODE_AND;
		}

		if ( 'any' === $mode ) {
			return self::MODE_OR;
		}

		return in_array( $mode, [ self::MODE_AND, self::MODE_OR ], true ) ? $mode : self::MODE_OR;
	}

	/**
	 * Returns a new instance with the specified key.
	 *
	 * @since $ver$
	 *
	 * @param null|string $key The field key.
	 *
	 * @return self A new Search_Filter instance.
	 */
	public function with_key( ?string $key ): self {
		$clone      = clone $this;
		$clone->key = $key;

		return $clone;
	}

	/**
	 * Returns a new instance with the specified value.
	 *
	 * @since $ver$
	 *
	 * @param mixed $value The filter value.
	 *
	 * @return self A new Search_Filter instance.
	 */
	public function with_value( $value ): self {
		$clone        = clone $this;
		$clone->value = $value;

		return $clone;
	}

	/**
	 * Returns a new instance with the specified operator.
	 *
	 * @since $ver$
	 *
	 * @param string $operator The comparison operator.
	 *
	 * @return self A new Search_Filter instance.
	 */
	public function with_operator( string $operator, ?array $allowed_operators = null ): self {
		$clone           = clone $this;
		$clone->operator = $operator;

		if ( null !== $allowed_operators ) {
			$clone->allowed_operators = $allowed_operators;
		}

		return $clone;
	}

	/**
	 * Returns a new instance with the specified request key.
	 *
	 * @since $ver$
	 *
	 * @param string|null $request_key The request key.
	 *
	 * @return self A new Search_Filter instance.
	 */
	public function with_request_key( ?string $request_key ): self {
		$clone              = clone $this;
		$clone->request_key = $request_key;

		return $clone;
	}

	/**
	 * Returns a new instance with the specified form ID.
	 *
	 * @since $ver$
	 *
	 * @param int|null $form_id The form ID.
	 *
	 * @return self A new Search_Filter instance.
	 */
	public function with_form_id( ?int $form_id ): self {
		$clone          = clone $this;
		$clone->form_id = $form_id;

		return $clone;
	}

	/**
	 * Returns a new instance with the specified field ID.
	 *
	 * @since $ver$
	 *
	 * @param string|null $field_id The field ID.
	 *
	 * @return self A new Search_Filter instance.
	 */
	public function with_field_id( ?string $field_id ): self {
		$clone           = clone $this;
		$clone->field_id = $field_id;

		return $clone;
	}

	/**
	 * Returns a new instance with the required flag.
	 *
	 * @since $ver$
	 *
	 * @param bool $required Whether this filter is required.
	 *
	 * @return self A new Search_Filter instance.
	 */
	public function with_required( bool $required = true ): self {
		$clone           = clone $this;
		$clone->required = $required;

		return $clone;
	}

	/**
	 * Returns a new instance with the numeric flag.
	 *
	 * @since $ver$
	 *
	 * @param bool $is_numeric Whether this filter uses numeric comparison.
	 *
	 * @return self A new Search_Filter instance.
	 */
	public function with_numeric( bool $is_numeric = true ): self {
		$clone             = clone $this;
		$clone->is_numeric = $is_numeric;

		return $clone;
	}

	/**
	 * Returns a new instance with the specified mode.
	 *
	 * @since $ver$
	 *
	 * @param string $mode The logical mode ('and' or 'or').
	 *
	 * @return self A new Search_Filter instance.
	 */
	public function with_mode( string $mode ): self {
		$clone       = clone $this;
		$clone->mode = self::normalize_mode( $mode );

		return $clone;
	}

	/**
	 * Returns a new instance with added conditions.
	 *
	 * @since $ver$
	 *
	 * @param self ...$conditions The conditions to add.
	 *
	 * @return self A new Search_Filter instance.
	 */
	public function with_conditions( self ...$conditions ): self {
		$clone             = clone $this;
		$clone->conditions = array_merge( $clone->conditions, $conditions );

		return $clone;
	}

	/**
	 * Returns the field key.
	 *
	 * @since $ver$
	 *
	 * @return null|string The field key.
	 */
	public function key(): ?string {
		return $this->key;
	}

	/**
	 * Returns the filter value.
	 *
	 * @since $ver$
	 *
	 * @return mixed The filter value.
	 */
	public function value() {
		return $this->value;
	}

	/**
	 * Returns the comparison operator.
	 *
	 * @since $ver$
	 *
	 * @return string The operator.
	 */
	public function operator(): string {
		return $this->operator;
	}

	/**
	 * Returns the original request key (for BC compatibility).
	 *
	 * @since $ver$
	 *
	 * @return string|null The request key.
	 */
	public function request_key(): ?string {
		return $this->request_key;
	}

	/**
	 * Returns the form ID.
	 *
	 * @since $ver$
	 *
	 * @return int|null The form ID.
	 */
	public function form_id(): ?int {
		return $this->form_id;
	}

	/**
	 * Returns the field ID.
	 *
	 * @since $ver$
	 *
	 * @return string|null The field ID.
	 */
	public function field_id(): ?string {
		return $this->field_id;
	}

	/**
	 * Returns whether this filter is required (for global search).
	 *
	 * @since $ver$
	 *
	 * @return bool Whether the filter is required.
	 */
	public function is_required(): bool {
		return $this->required;
	}

	/**
	 * Returns whether this filter uses numeric comparison.
	 *
	 * @since $ver$
	 *
	 * @return bool Whether numeric comparison is used.
	 */
	public function is_numeric(): bool {
		return $this->is_numeric;
	}

	/**
	 * Returns whether this filter is a group (has nested conditions).
	 *
	 * @since $ver$
	 *
	 * @return bool Whether this is a group filter.
	 */
	public function is_group(): bool {
		return null !== $this->mode && [] !== $this->conditions;
	}

	/**
	 * Returns the logical mode for groups.
	 *
	 * @since $ver$
	 *
	 * @return string|null The mode ('and' or 'or'), or null for leaf filters.
	 */
	public function mode(): ?string {
		return $this->mode;
	}

	/**
	 * Returns the nested conditions.
	 *
	 * @since $ver$
	 *
	 * @return Search_Filter[] The nested filters.
	 */
	public function conditions(): array {
		return $this->conditions;
	}

	/**
	 * Returns whether the filter has a non-empty value.
	 *
	 * @since $ver$
	 *
	 * @return bool Whether the filter has a value.
	 */
	public function has_value(): bool {
		if ( is_array( $this->value ) ) {
			$value = array_filter(
				$this->value,
				static fn( $v ): bool => '' !== $v && null !== $v
			);

			return [] !== $value;
		}

		return '' !== $this->value && null !== $this->value;
	}

	/**
	 * Returns the allowed operators.
	 *
	 * @since $ver$
	 *
	 * @return string[] The allowed operators.
	 */
	public function allowed_operators(): array {
		return array_values( $this->allowed_operators ?? [ self::DEFAULT_OPERATOR ] );
	}

	/**
	 * Returns whether this filter's value has been normalized.
	 *
	 * @since $ver$
	 *
	 * @return bool Whether the value is normalized.
	 */
	public function is_normalized(): bool {
		return $this->normalized;
	}

	/**
	 * Returns a new instance marked as normalized.
	 *
	 * @since $ver$
	 *
	 * @return self A new Search_Filter instance.
	 */
	public function as_normalized(): self {
		$clone             = clone $this;
		$clone->normalized = true;

		return $clone;
	}

	/**
	 * Retrieves a context value.
	 *
	 * @since $ver$
	 *
	 * @param string $key      The context key.
	 * @param mixed  $fallback The default value if the key doesn't exist.
	 *
	 * @return mixed The context value or default.
	 */
	public function context( string $key, $fallback = null ) {
		return $this->context[ $key ] ?? $fallback;
	}

	/**
	 * Returns a new instance with the specified context value.
	 *
	 * @since $ver$
	 *
	 * @param string $key   The context key.
	 * @param mixed  $value The context value.
	 *
	 * @return self A new Search_Filter instance.
	 */
	public function with_context( string $key, $value ): self {
		$clone                  = clone $this;
		$clone->context[ $key ] = $value;

		return $clone;
	}

	/**
	 * Returns a new instance with context removed.
	 *
	 * @since $ver$
	 *
	 * @param string|null $key The context key to remove, or null to remove all context.
	 *
	 * @return self A new Search_Filter instance.
	 */
	public function without_context( ?string $key = null ): self {
		$clone = clone $this;

		if ( null === $key ) {
			$clone->context = [];
		} else {
			unset( $clone->context[ $key ] );
		}

		return $clone;
	}

	/**
	 * Accepts a visitor to the filter.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter_Visitor $visitor The visitor.
	 * @param string                $order   The default visiting order. Will be overwritten by the visitor.
	 */
	public function accept( Search_Filter_Visitor $visitor, string $order = Search_Filter_Visitor::ORDER_PRE ): void {
		// Use the default order when no order is provided on the visitor.
		$order = $visitor->get_order() ?? $order;
		if ( Search_Filter_Visitor::ORDER_PRE === $order ) {
			$visitor->visit( $this );
		}

		if ( $this->is_group() ) {
			foreach ( $this->conditions as $condition ) {
				$condition->accept( $visitor, $order );
			}
		}

		if ( Search_Filter_Visitor::ORDER_POST === $order ) {
			$visitor->visit( $this );
		}
	}

	/**
	 * Converts the filter to an array (BC compatibility).
	 *
	 * @since $ver$
	 *
	 * @return array The filter as an array.
	 */
	public function to_array(): array {
		// For groups, return the group structure.
		if ( $this->is_group() ) {
			// If the group only has one condition, the group is not required.
			if ( count( $this->conditions ) === 1 ) {
				return $this->conditions[0]->to_array();
			}

			return [
				'mode'       => $this->mode,
				'conditions' => array_map(
					static fn( self $filter ): array => $filter->to_array(),
					$this->conditions
				),
			];
		}

		// Build the base filter array.
		$array = [
			'key'      => $this->key,
			'operator' => $this->operator,
			'value'    => $this->value,
		];

		// Add optional properties only if set.
		if ( null !== $this->request_key ) {
			$array['request_key'] = $this->request_key;
		}

		if ( null !== $this->form_id ) {
			$array['form_id'] = $this->form_id;
		}

		if ( null !== $this->field_id ) {
			$array['field_id'] = $this->field_id;
		}

		if ( $this->required ) {
			$array['required'] = true;
		}

		if ( $this->is_numeric ) {
			$array['is_numeric'] = true;
		}

		return $array;
	}

	/**
	 * Returns more compact debug information.
	 *
	 * @since    $ver$
	 *
	 * @return array
	 * @internal Do not rely on this method. It can be removed in any version.
	 */
	public function __debugInfo(): array {
		if ( $this->is_group() ) {
			return [
				'mode'       => $this->mode,
				'conditions' => $this->conditions,
			];
		}

		return $this->to_array();
	}
}
