<?php
/**
 * @license MIT
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\QueryFilters\Filter;

use GravityKit\GravityView\QueryFilters\Filter\Visitor\FilterVisitor;
use InvalidArgumentException;
use RuntimeException;

/**
 * Entity that represents a single Filter.
 *
 * @since 2.0.0
 */
final class Filter {
	public const MODE_AND = 'and';
	public const MODE_OR  = 'or';

	/**
	 * Map of virtual operators to GF_Query operators
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	private static array $_proxy_operators_map = [
		'isempty'    => 'is',
		'isnotempty' => 'isnot',
		'ison'       => 'is',
		'isnoton'    => 'isnot',
		'isbefore'   => '<',
		'isafter'    => '>',
		'='          => 'is',
		'!='         => 'isnot',
		'has_any'    => 'in',
		'has_none'   => 'notin',
	];

	/**
	 * The entity ID.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	private $id;

	/**
	 * The form ID.
	 *
	 * @since 2.7.0
	 *
	 * @var int|null
	 */
	private ?int $form_id = null;

	/**
	 * The field key.
	 *
	 * @since 2.0.0
	 *
	 * @var string|int|null
	 */
	private $key;

	/**
	 * The filter version.
	 *
	 * @since 2.0.0
	 *
	 * @var int|null
	 */
	private ?int $version = null;

	/**
	 * The mode.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	private $mode;

	/**
	 * The filter value.
	 *
	 * @since 2.0.0
	 *
	 * @var mixed
	 */
	private $value = null;

	/**
	 * The operator for the filter.
	 *
	 * @since 2.0.0
	 *
	 * @var string|null
	 */
	private ?string $operator = null;

	/**
	 * Nested filters for this filter.
	 *
	 * @since 2.0.0
	 *
	 * @var Filter[]
	 */
	private array $conditions = [];

	/**
	 * Whether the current filter is enabled.
	 *
	 * @since 2.0.0
	 *
	 * @var bool
	 */
	private bool $is_enabled = true;

	/**
	 * Creates a filter instance.
	 *
	 * @since 2.0.0
	 */
	private function __construct() {
	}

	/**
	 * Checks whether the given visitor implements the visitor pattern via duck typing.
	 *
	 * @since 2.10
	 *
	 * @param mixed $visitor The visitor to check.
	 *
	 * @return bool Whether the visitor has a visit_filter method.
	 */
	private static function is_visitor( $visitor ): bool {
		return is_object( $visitor ) && is_callable( [ $visitor, 'visit_filter' ] );
	}

	/**
	 * Checks whether the given visitor is a valid filter visitor.
	 *
	 * @since 2.10
	 *
	 * @param mixed $visitor The visitor to check.
	 *
	 * @return bool Whether the visitor is valid.
	 */
	public static function is_valid_visitor( $visitor ): bool {
		return self::is_visitor( $visitor ) || is_callable( $visitor );
	}

	/**
	 * Checks whether the given visitor supports entry awareness.
	 *
	 * @since 2.10
	 *
	 * @param mixed $visitor The visitor to check.
	 *
	 * @return bool Whether the visitor supports setting an entry.
	 */
	public static function is_entry_aware_visitor( $visitor ): bool {
		// Needs to be a visitor first.
		if ( ! self::is_valid_visitor( $visitor ) ) {
			return false;
		}

		// Must be a class to implement the method.
		if ( ! is_object( $visitor ) ) {
			return false;
		}

		return method_exists( $visitor, 'set_entry' );
	}

	/**
	 * Creates a filter from an array.
	 *
	 * @since 2.0.0
	 *
	 * @param array $filter The filter array.
	 *
	 * @return self The filter.
	 */
	public static function from_array( array $filter ): self {
		$instance = new self();
		$instance->set_id( $filter['_id'] ?? '' );
		$instance->form_id = isset( $filter['form_id'] ) ? (int) $filter['form_id'] : null;

		if ( isset( $filter['key'] ) ) {
			$instance->set_key( $filter['key'] );
		}

		// Mode and conditions are mutually exclusive.
		$conditions = $filter['conditions'] ?? [];
		$mode       = $filter['mode'] ?? '';
		if ( $mode === 'all' ) {
			$mode = null;
		}

		if ( $conditions || $mode ) {
			$instance->set_conditions( $conditions );
			$instance->set_mode( $mode );
		}

		if ( $version = $filter['version'] ?? 0 ) {
			$instance->set_version( $version );
		}

		foreach ( [ 'operator', 'value' ] as $key ) {
			if ( ( $filter[ $key ] ?? null ) && ! isset( $filter['key'] ) ) {
				throw new InvalidArgumentException( 'A "key" value must be set for non-logical filters.' );
			}
		}

		if ( isset( $filter['value'] ) ) {
			$instance->set_value( $filter['value'] );
		}

		if ( $operator = ( $filter['operator'] ?? '' ) ) {
			$instance->set_operator( $operator );
		}

		return $instance;
	}

	/**
	 * Formats the filter as an array.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function to_array(): array {
		if ( ! $this->is_enabled() ) {
			return [];
		}

		// Group output.
		if ( $this->is_logic() ) {
			return array_filter( [
				'_id'        => $this->id,
				'version'    => $this->version,
				'mode'       => $this->mode,
				'conditions' => array_map(
					static function ( Filter $filter ): array {
						return $filter->to_array();
					},
					$this->conditions
				),
			] );
		}

		return array_filter(
			[
				'_id'      => $this->id,
				'form_id'  => $this->form_id,
				'version'  => $this->version,
				'key'      => $this->key,
				'value'    => $this->value,
				'operator' => $this->operator,
			],
			static function ( $c ) {
				return ! is_null( $c ) && ! ( is_array( $c ) && ! $c );
			}
		);
	}

	/**
	 * Set this filter as enabled.
	 *
	 * @since 2.0.0
	 */
	public function disable(): void {
		$this->is_enabled = false;
	}

	/**
	 * Set this filter as enabled.
	 *
	 * @since 2.0.0
	 */
	public function enable(): void {
		$this->is_enabled = true;
	}

	/**
	 * Whether the current filter is enabled.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return $this->is_enabled;
	}

	/**
	 * Dispatches the filter to the given visitor.
	 *
	 * @since 2.10
	 *
	 * @param FilterVisitor|callable $visitor The visitor.
	 * @param string                 $level   The current nesting level.
	 */
	private function call_visitor( $visitor, string $level ): void {
		if ( self::is_visitor( $visitor ) ) {
			$visitor->visit_filter( $this, $level );

			return;
		}

		if ( is_callable( $visitor ) ) {
			$visitor( $this, $level );
		}
	}

	/**
	 * Accepts a visitor and double dispatches the current instance to the visitor.
	 *
	 * @since 2.0.0
	 *
	 * @param FilterVisitor|callable $visitor The visitor.
	 * @param string                 $level   The current nesting level.
	 * @param string                 $order   The tree traversal order.
	 */
	public function accept( $visitor, string $level = '0', string $order = FilterVisitor::PRE_ORDER ): void {
		if ( ! in_array( $order, [ FilterVisitor::PRE_ORDER, FilterVisitor::POST_ORDER ], true ) ) {
			throw new InvalidArgumentException( 'Invalid tree traversal order.' );
		}

		if ( ! self::is_valid_visitor( $visitor ) ) {
			throw new InvalidArgumentException( 'Invalid visitor.' );
		}

		if ( $order === FilterVisitor::PRE_ORDER ) {
			$this->call_visitor( $visitor, $level );
		}

		// Recursively visit children as well.
		foreach ( $this->conditions as $i => $filter ) {
			$next_level = $level === '0' ? $i + 1 : $level . '.' . ( $i + 1 );
			$filter->accept( $visitor, (string) $next_level );
		}

		if ( $order === FilterVisitor::POST_ORDER ) {
			$this->call_visitor( $visitor, $level );
		}
	}

	/**
	 * A filter that is designed to not match anything.
	 *
	 * @since 2.0.0
	 */
	public static function locked(): Filter {
		$filter = Filter::from_array( [
			'_id'      => 'locked',
			'key'      => 'created_by',
			'operator' => 'is',
			'value'    => 'Query Filters - This is the "force zero results" filter, designed to not match anything.',
		] );

		// Locked filters don't need an id.
		$filter->id = null;

		return $filter;
	}

	/**
	 * Wraps this filter with others in an AND logic group.
	 *
	 * @since 2.9.0
	 *
	 * @param Filter ...$others The other filters to combine with this one.
	 *
	 * @return Filter A new AND logic group containing all filters.
	 *
	 * @throws InvalidArgumentException If no other filters provided.
	 */
	public function and( Filter ...$others ): Filter {
		return $this->combine_filters( self::MODE_AND, ...$others );
	}

	/**
	 * Wraps this filter with others in an OR logic group.
	 *
	 * At least one filter (this one or the others) must match for the group to match.
	 *
	 * @since 2.9.0
	 *
	 * @param Filter ...$others The other filters to combine with this one.
	 *
	 * @return Filter A new logic group containing all filters.
	 *
	 * @throws InvalidArgumentException If no other filters provided.
	 */
	public function or( Filter ...$others ): Filter {
		return $this->combine_filters( self::MODE_OR, ...$others );
	}

	/**
	 * Combines this filter with others in a logic group.
	 *
	 * @since 2.9.0
	 *
	 * @param string $mode      The logic mode (AND or OR).
	 * @param Filter ...$others The other filters to combine with this one.
	 *
	 * @return Filter A new logic group containing all filters.
	 *
	 * @throws InvalidArgumentException If no other filters provided.
	 */
	private function combine_filters( string $mode, Filter ...$others ): Filter {
		if ( empty( $others ) ) {
			throw new InvalidArgumentException( 'At least one other filter must be provided.' );
		}

		$instance       = new self();
		$instance->id   = null; // Groups don't need IDs.
		$instance->mode = $mode;

		if ( isset( $this->version ) ) {
			$instance->set_version( $this->version );
		}

		$should_flatten = true;
		foreach ( $others as $other ) {
			// Inherit the version.
			if ( isset( $other->version ) && ! isset( $instance->version ) ) {
				$instance->set_version( $other->version );
			}

			// We can't flatten this group.
			if ( $other->is_logic() && $mode !== $other->mode ) {
				$should_flatten = false;
			}
		}

		// If the current filter is already a logic group of the same type, we can append the others.
		if ( $this->is_logic() && $mode === $this->mode ) {
			$instance->conditions = array_merge( $this->conditions, $others );

			return $instance;
		}

		if ( $should_flatten ) {
			// Combine all nested conditions into a new array, in a single merge.
			$others = array_merge( ... array_map( static function ( Filter $filter ) {
				return $filter->is_logic() ? $filter->conditions : [ $filter ];
			}, $others ) );
		}

		$instance->conditions   = $others;
		$instance->conditions[] = $this;

		return $instance;
	}

	/**
	 * Locks the current filter, making the query not return any results.
	 *
	 * @since 2.0.0
	 */
	public function lock(): void {
		if ( $this->is_logic() ) {
			foreach ( $this->conditions as $filter ) {
				$filter->lock();
			}

			return;
		}

		$locked = self::locked();
		foreach ( [ 'id', 'key', 'operator', 'value' ] as $key ) {
			$this->{$key} = $locked->{$key};
		}
	}

	/**
	 * Whether this filter is a logic group.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_logic(): bool {
		return $this->mode !== null && $this->conditions !== [];
	}

	/**
	 * Sets the mode for the filter.
	 *
	 * @since 2.0.0
	 *
	 * @param string $mode The filter mode.
	 */
	private function set_mode( string $mode ): void {
		$mode = strtolower( $mode );

		if ( ! in_array( $mode, $modes = [ self::MODE_OR, self::MODE_AND ], true ) ) {
			throw new InvalidArgumentException( sprintf(
					'A filter mode can only be one of: "%s"; "%s" given.',
					implode( ', ', $modes ),
					$mode
				)
			);
		}

		$this->mode = $mode;
	}

	/**
	 * Returns the filter mode.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function mode(): string {
		if ( ! $this->is_logic() ) {
			throw new RuntimeException( 'A non-logic filter does not have a mode.' );
		}

		return $this->mode;
	}

	/**
	 * Sets the ID.
	 *
	 * @since 2.0.0
	 *
	 * @param string $id The id.
	 */
	private function set_id( string $id ): void {
		if ( trim( $id ) === '' ) {
			throw new InvalidArgumentException( 'Filter ID must contain a value.' );
		}

		$this->id = $id;
	}

	/**
	 * Sets the field key.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key The field key.
	 */
	public function set_key( string $key ): void {
		if ( trim( $key ) === '' ) {
			throw new InvalidArgumentException( 'Filter key must contain a value.' );
		}

		$this->key = $key;
	}

	/**
	 * Returns the child filters.
	 *
	 * @since 2.0.0
	 *
	 * @return Filter[]
	 */
	public function conditions(): array {
		if ( ! $this->is_logic() ) {
			throw new RuntimeException( 'Only a logic filter contains conditions' );
		}

		return $this->conditions;
	}

	/**
	 * Sets the conditions, and upgrades them to filters instances.
	 *
	 * @since 2.0.0
	 *
	 * @param array $conditions The conditions.
	 */
	private function set_conditions( array $conditions ): void {
		if ( ! $conditions ) {
			throw new InvalidArgumentException( 'A logic filter needs at least one condition.' );
		}

		$mode           = '';
		$sub_conditions = [];

		foreach ( $conditions as $filter ) {
			if ( is_array( $filter ) ) {
				$sub_conditions = $filter['conditions'] ?? [];
				$mode           = $filter['mode'] ?? '';

				// Remove conditions so we can increase the nesting properly.
				unset ( $filter['conditions'], $filter['mode'] );

				$filter = Filter::from_array( $filter );
			}

			if ( ! $filter instanceof Filter ) {
				throw new InvalidArgumentException( 'A filter condition can only be a filter or an array.' );
			}

			// Add conditions and mode back with proper nesting.
			if ( $mode || $sub_conditions ) {
				$filter->set_conditions( $sub_conditions );
				$filter->set_mode( $mode );
			}

			if ( null === $filter->key && ! $filter->is_logic() ) {
				continue;
			}

			$this->conditions[] = $filter;
		}
	}

	/**
	 * Sets the version.
	 *
	 * @since 2.0.0
	 *
	 * @param int $version The version.
	 */
	private function set_version( int $version ): void {
		if ( $version < 1 ) {
			throw new InvalidArgumentException( 'Filter version must be higher than 0.' );
		}

		$this->version = $version;
	}

	/**
	 * Returns the value of this filter.
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	public function value() {
		$this->guard_logical_getter( __FUNCTION__ );

		return $this->value;
	}

	/**
	 * Sets the value for the filter.
	 *
	 * @since 2.0.0
	 *
	 * @param mixed $value The value of the filter.
	 */
	public function set_value( $value ): void {
		if ( $this->is_logic() ) {
			throw new RuntimeException( 'Cannot set value for logical filter.' );
		}

		$this->value = $value;
	}

	/**
	 * Returns the form ID.
	 *
	 * @since 2.7.0
	 *
	 * @return int The form ID, or 0 if not provided.
	 */
	public function form_id(): int {
		$this->guard_logical_getter( __FUNCTION__ );

		return (int) $this->form_id;
	}

	/**
	 * Returns the key of the filter.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function key(): string {
		$this->guard_logical_getter( __FUNCTION__ );

		return (string) $this->key;
	}

	/**
	 * Returns the key of the filter.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function operator(): string {
		$this->guard_logical_getter( __FUNCTION__ );

		$operator = $this->operator ?: 'is';

		return self::$_proxy_operators_map[ $operator ] ?? $operator;
	}

	/**
	 * Guards against calling getter on logical filter.
	 *
	 * @since 2.0.0
	 *
	 * @param string $method_name The getter method name.
	 */
	private function guard_logical_getter( string $method_name ): void {
		if ( $this->is_logic() ) {
			throw new RuntimeException( sprintf( 'Cannot retrieve %s from logical filter.', $method_name ) );
		}
	}

	/**
	 * Sets the operator for the filter.
	 *
	 * @since 2.0.0
	 *
	 * @param string $operator The operator.
	 */
	public function set_operator( string $operator ): void {
		$this->operator = strtolower( $operator );

		// For "empty" operators, the value should be empty.
		if ( false !== strpos( $this->operator, 'empty' ) ) {
			$this->set_value( '' );
		}
	}

	/**
	 * Whether this filter is equal to another filter.
	 *
	 * @since 2.0.0
	 *
	 * @param Filter $other The other filter to test against.
	 *
	 * @return bool Whether the filters are considered the same.
	 */
	public function equals( Filter $other ): bool {
		if ( $this->id === $other->id ) {
			return true;
		}

		if ( ! $this->is_logic() ) {
			return false;
		}

		// In case of a logic group, we test all child filters.
		// This can be useful testing a locked filter.
		foreach ( $this->conditions as $child_filter ) {
			if ( ! $child_filter->equals( $other ) ) {
				// At least one child-filter does not match
				return false;
			}
		}

		return true;
	}

	/**
	 * Helper method to debug filter objects more easily.
	 *
	 * @since 2.0.0
	 *
	 * @return array The debug info.
	 */
	public function __debugInfo(): array {
		return $this->to_array();
	}
}
