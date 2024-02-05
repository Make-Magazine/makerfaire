<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 01-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Filter;

/**
 * Factory to create {@see Filter} instances.
 * @since 2.0.0
 */
final class FilterFactory {
	/**
	 * The filter id generator.
	 * @since 2.0.0
	 * @var FilterIdGenerator
	 */
	private $id_generator;

	/**
	 * Creates the factory.
	 *
	 * @param FilterIdGenerator $id_generator The filter id generator.
	 *
	 * @since 2.0.0
	 */
	public function __construct( FilterIdGenerator $id_generator ) {
		$this->id_generator = $id_generator;
	}

	/**
	 * Creates a {@see Filter} from any given array.
	 *
	 * @param array $filters The filters.
	 *
	 * @return Filter The filter.
	 * @since 2.0.0
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
	 * @param array $filters The filters.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	private function from_version_1( array $filters ): Filter {
		$mode = strtolower($filters['mode'] ?? Filter::MODE_AND);
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
	 * @param array $conditions The conditions to wrap.
	 *
	 * @return array The conditions array.
	 * @since 2.0.0
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
	 * @param array $filters The filters.
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	private function should_upgrade( array $filters ): bool {
		return ( $filters['version'] ?? 1 ) === 1;
	}

	/**
	 * Recursively add missing random ID to any filter.
	 *
	 * @param array $filters The filters.
	 *
	 * @return array The filters with proper id's.
	 * @since 2.0.0
	 */
	private function add_filter_ids( array $filters ): array {
		if ( ! isset( $filters['_id'] ) ) {
			$filters['_id'] = $this->id_generator->get_id();
			ksort( $filters );
		}

		foreach ( $filters['conditions'] ?? [] as $i => $filter ) {
			$filters['conditions'][ $i ] = $this->add_filter_ids( $filter );
		}

		return $filters;
	}
}
