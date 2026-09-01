<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Filter;

/**
 * Factory to create {@see Filter} instances.
 *
 * @since 2.0.0
 */
final class FilterFactory {
	/**
	 * The filter id generator.
	 *
	 * @since 2.0.0
	 * @var FilterIdGenerator
	 */
	private $id_generator;

	/**
	 * Creates the factory.
	 *
	 * @since 2.0.0
	 *
	 * @param FilterIdGenerator $id_generator The filter id generator.
	 *
	 */
	public function __construct( FilterIdGenerator $id_generator ) {
		$this->id_generator = $id_generator;
	}

	/**
	 * Creates a {@see Filter} from any given array.
	 *
	 * @since 2.0.0
	 *
	 * @param array $filters The filters.
	 *
	 * @return Filter The filter.
	 */
	public function from_array( array $filters ): Filter {
		if ( $this->should_upgrade( $filters ) ) {
			return $this->from_version_1( $filters );
		}

		$filters = $this->add_filter_ids( $filters );

		return Filter::from_array( $filters );
	}

	/**
	 * Creates a filter from a version 1 array.
	 *
	 * @since 2.0.0
	 *
	 * @param array $filters The filters.
	 *
	 * @return void
	 */
	private function from_version_1( array $filters ): Filter {
		$mode = strtolower( $filters['mode'] ?? Filter::MODE_AND );
		if ( $mode === 'any' ) {
			$mode = Filter::MODE_OR;
		}

		unset( $filters['version'], $filters['mode'] );

		$conditions = [];
		foreach ( $filters as $filter ) {
			if ( ! $filter ) {
				continue;
			}

			$filter['_id'] = $this->id_generator->get_id();
			$conditions[]  = $filter;
		}

		if ( ! $conditions ) {
			// Empty filter.
			return $this->from_array( [ 'version' => 2 ] );
		}

		$filter = [
			'mode'       => Filter::MODE_AND,
			'version'    => 2,
			'conditions' => [],
		];

		if ( $mode === Filter::MODE_OR ) {
			$filter['conditions'][] = $this->create_condition_array( $conditions );
		} else {
			// and mode
			foreach ( $conditions as $condition ) {
				$filter['conditions'][] = $this->create_condition_array( [ $condition ] );
			}
		}

		return $this->from_array( $filter );
	}

	/**
	 * Wraps an array of conditions into a condition array for the `conditions` key.
	 *
	 * @since 2.0.0
	 *
	 * @param array $conditions The conditions to wrap.
	 *
	 * @return array The conditions array.
	 */
	private function create_condition_array( array $conditions ): array {
		return [
			'_id'        => $this->id_generator->get_id(),
			'mode'       => Filter::MODE_OR,
			'conditions' => $conditions,
		];
	}

	/**
	 * Whether the filters should be upgraded to a higher version.
	 *
	 * @since 2.0.0
	 *
	 * @param array $filters The filters.
	 *
	 * @return bool
	 */
	private function should_upgrade( array $filters ): bool {
		return ( $filters['version'] ?? 1 ) === 1;
	}

	/**
	 * Creates a date range filter.
	 *
	 * Creates a filter for date ranges with inclusive boundaries. By default, start dates are
	 * set to 00:00:00 and end dates to 23:59:59 to ensure full day coverage. When
	 * `$use_exact_time` is true, the provided date objects are used as-is to preserve any
	 * timezone adjustments applied by the caller.
	 *
	 * @since 2.9.0
	 *
	 * @param \DateTimeInterface|null $start          The start date (inclusive).
	 * @param \DateTimeInterface|null $end            The end date (inclusive).
	 * @param string                  $key            The field key (default: 'date_created').
	 * @param bool                    $use_exact_time Whether to preserve the exact time from the provided objects.
	 *
	 * @return Filter The date range filter.
	 *
	 * @throws \InvalidArgumentException If both start and end are null.
	 */
	public function date_range(
		?\DateTimeInterface $start = null,
		?\DateTimeInterface $end = null,
		string $key = 'date_created',
		bool $use_exact_time = false
	): Filter {
		if ( null === $start && null === $end ) {
			throw new \InvalidArgumentException( 'At least one of start or end date must be provided.' );
		}

		// Ensure we don't mutate the input objects by converting to DateTimeImmutable.
		if ( $start && ! $start instanceof \DateTimeImmutable ) {
			/** @var \DateTime $start */
			$start = \DateTimeImmutable::createFromMutable( $start );
		}

		if ( $end && ! $end instanceof \DateTimeImmutable ) {
			/** @var \DateTime $end */
			$end = \DateTimeImmutable::createFromMutable( $end );
		}

		$start_value = $start
			? ( $use_exact_time ? $start : $start->setTime( 0, 0, 0 ) )->format( 'Y-m-d H:i:s' )
			: null;

		$end_value = $end
			? ( $use_exact_time ? $end : $end->setTime( 23, 59, 59 ) )->format( 'Y-m-d H:i:s' )
			: null;

		$start_filter = $start_value
			? $this->from_array( [
				'version'  => 2,
				'key'      => $key,
				'operator' => '>=',
				'value'    => $start_value,
			] )
			: null;

		$end_filter = $end_value
			? $this->from_array( [
				'version'  => 2,
				'key'      => $key,
				'operator' => '<=',
				'value'    => $end_value,
			] )
			: null;

		if ( $start_filter && $end_filter ) {
			return $end_filter->and( $start_filter );
		}

		return $start_filter ?? $end_filter;
	}

	/**
	 * Recursively add missing random ID to any filter.
	 *
	 * @since 2.0.0
	 *
	 * @param array $filters The filters.
	 *
	 * @return array The filters with proper id's.
	 */
	private function add_filter_ids( array $filters ): array {
		if ( ! isset( $filters['_id'] ) ) {
			$filters['_id'] = $this->id_generator->get_id();
			ksort( $filters );
		}

		$conditions = $filters['conditions'] ?? [];

		if ( ! is_array( $conditions ) ) {
			$filters['conditions'] = [];

			return $filters;
		}

		foreach ( $conditions as $i => $filter ) {
			$filters['conditions'][ $i ] = $this->add_filter_ids( $filter );
		}

		return $filters;
	}
}
