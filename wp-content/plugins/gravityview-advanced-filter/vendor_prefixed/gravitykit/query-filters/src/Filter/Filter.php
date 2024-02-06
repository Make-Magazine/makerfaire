<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 01-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Filter;

use GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor\FilterVisitor;
use InvalidArgumentException;
use RuntimeException;

/**
 * Entity that represents a single Filter.
 * @since 2.0.0
 */
final class Filter {
	const MODE_AND = 'and';
	const MODE_OR = 'or';

	/**
	 * Map of virtual operators to GF_Query operators
	 * @since 2.0.0
	 * @var array
	 */
	private static $_proxy_operators_map = [
		'isempty'    => 'is',
		'isnotempty' => 'isnot',
	];

	/**
	 * The entity ID.
	 * @since $ver4
	 * @var string
	 */
	private $id;

	/**
	 * The field key.
	 * @since 2.0.0
	 * @var string|int|null
	 */
	private $key;

	/**
	 * The filter version.
	 * @since 2.0.0
	 * @var int
	 */
	private $version;

	/**
	 * The mode.
	 * @since 2.0.0
	 * @var string
	 */
	private $mode;

	/**
	 * The filter value.
	 * @since 2.0.0
	 * @var mixed
	 */
	private $value = null;

	/**
	 * The operator for the filter.
	 * @since 2.0.0
	 * @var string
	 */
	private $operator;

	/**
	 * Nested filters for this filter.
	 * @since 2.0.0
	 * @var Filter[]
	 */
	private $conditions = [];

	/**
	 * Whether the current filter is enabled.
	 * @since 2.0.0
	 * @var bool
	 */
	private $is_enabled = true;

	/**
	 * Creates a filter instance.
	 * @since 2.0.0
	 */
	private function __construct() {
	}

	/**
	 * Creates a filter from an array.
	 *
	 * @param array $filter The filter array.
	 *
	 * @return self The filter.
	 * @since 2.0.0
	 */
	public static function from_array( array $filter ): self {
		$instance = new self();
		$instance->set_id( $filter['_id'] ?? '' );

		if ( isset( $filter['key'] ) ) {
			$instance->set_key( $filter['key'] ?? '' );
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
	 * @return array
	 * @since 2.0.0
	 */
	public function to_array(): array {
		if ( ! $this->is_enabled() ) {
			return [];
		}

		return array_filter(
			[
				'_id'        => $this->id,
				'version'    => $this->version,
				'mode'       => $this->mode,
				'key'        => $this->key,
				'value'      => $this->value,
				'operator'   => $this->operator,
				'conditions' => array_map(
					static function ( Filter $filter ): array {
						return $filter->to_array();
					},
					$this->conditions
				),
			],
			static function ( $c ) {
				return ! is_null( $c ) && ! ( is_array( $c ) && ! $c );
			}
		);
	}

	/**
	 * Set this filter as enabled.
	 * @since 2.0.0
	 */
	public function disable() {
		$this->is_enabled = false;
	}

	/**
	 * Set this filter as enabled.
	 * @since 2.0.0
	 */
	public function enable() {
		$this->is_enabled = true;
	}

	/**
	 * Whether the current filter is enabled.
	 * @return bool
	 * @since 2.0.0
	 */
	public function is_enabled(): bool {
		return $this->is_enabled;
	}

	/**
	 * Accepts a visitor and double dispatches the current instance to the visitor.
	 *
	 * @param FilterVisitor $visitor The visitor.
	 *
	 * @return void
	 */
	public function accept( FilterVisitor $visitor, string $level = '0', string $order = FilterVisitor::PRE_ORDER ) {
		if ( ! in_array( $order, [ FilterVisitor::PRE_ORDER, FilterVisitor::POST_ORDER ], true ) ) {
			throw new InvalidArgumentException( 'Invalid tree traversal order.' );
		}

		if ( $order === FilterVisitor::PRE_ORDER ) {
			$visitor->visit_filter( $this, $level );
		}

		// Recursively visit children as well.
		foreach ( $this->conditions as $i => $filter ) {
			$next_level = $level === '0' ? $i + 1 : $level . '.' . ( $i + 1 );
			$filter->accept( $visitor, (string) $next_level );
		}

		if ( $order === FilterVisitor::POST_ORDER ) {
			$visitor->visit_filter( $this, $level );
		}
	}

	/**
	 * A filter that is designed to not match anything.
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
	 * Locks the current filter, making the query not return any results.
	 * @return void
	 * @since 2.0.0
	 */
	public function lock() {
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
	 * @since 2.0.0
	 */
	public function is_logic(): bool {
		return $this->mode !== null && $this->conditions !== [];
	}

	/**
	 * Sets the mode for the filter.
	 * @return void
	 * @since 2.0.0
	 */
	private function set_mode( string $mode ) {
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
	 * @return string
	 * @since 2.0.0
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
	 * @param string $id The id.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	private function set_id( string $id ) {
		if ( trim( $id ) === '' ) {
			throw new InvalidArgumentException( 'Filter ID must contain a value.' );
		}

		$this->id = $id;
	}

	/**
	 * Sets the field key.
	 *
	 * @param string $key The field key.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function set_key( string $key ) {
		if ( trim( $key ) === '' ) {
			throw new InvalidArgumentException( 'Filter key must contain a value.' );
		}

		$this->key = $key;
	}

	/**
	 * Returns the child filters.
	 * @return Filter[]
	 * @since 2.0.0
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
	 * @param array $conditions The conditions.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	private function set_conditions( array $conditions ) {
		if ( ! $conditions ) {
			throw new InvalidArgumentException( 'A logic filter needs at least one condition.' );
		}

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

			if ( ! $filter->is_logic() && $filter->key === null ) {
				continue;
			}
			$this->conditions[] = $filter;
		}
	}

	/**
	 * Sets the version.
	 *
	 * @param int $version The version.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	private function set_version( int $version ) {
		if ( $version < 1 ) {
			throw new InvalidArgumentException( 'Filter version must be higher than 0.' );
		}

		$this->version = $version;
	}

	/**
	 * Returns the value of this filter.
	 * @return mixed
	 * @since 2.0.0
	 */
	public function value() {
		$this->guard_logical_getter( __FUNCTION__ );

		return $this->value;
	}

	/**
	 * Sets the value for the filter.
	 *
	 * @param mixed $value The value of the filter.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function set_value( $value ) {
		if ( $this->is_logic() ) {
			throw new RuntimeException( 'Cannot set value for logical filter.' );
		}

		$this->value = $value;
	}

	/**
	 * Returns the key of the filter.
	 * @return string
	 * @since 2.0.0
	 */
	public function key(): string {
		$this->guard_logical_getter( __FUNCTION__ );

		return (string) $this->key;
	}


	/**
	 * Returns the key of the filter.
	 * @return string
	 * @since 2.0.0
	 */
	public function operator(): string {
		$this->guard_logical_getter( __FUNCTION__ );

		$operator = $this->operator ?: 'is';

		return self::$_proxy_operators_map[ $operator ] ?? $operator;
	}

	/**
	 * Guards against calling getter on logical filter.
	 *
	 * @param string $method_name The getter method name.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	private function guard_logical_getter( string $method_name ) {
		if ( $this->is_logic() ) {
			throw new RuntimeException( sprintf( 'Cannot retrieve %s from logical filter.', $method_name ) );
		}
	}

	/**
	 * Sets the operator for the filter.
	 *
	 * @param string $operator The operator.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function set_operator( string $operator ) {
		$this->operator = strtolower( $operator );

		// For empty operators the value should be empty.
		if ( in_array( $this->operator, array_keys( self::$_proxy_operators_map ), true ) ) {
			$this->set_value( '' );
		}
	}

	/**
	 * Whether this filter is equal to another filter.
	 *
	 * @param Filter $other The other filter to test against.
	 *
	 * @return bool Whether the filters are considered the same.
	 * @since 2.0.0
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
	 * @return array The debug info.
	 * @since 2.0.0
	 */
	public function __debugInfo() {
		return $this->to_array();
	}
}
