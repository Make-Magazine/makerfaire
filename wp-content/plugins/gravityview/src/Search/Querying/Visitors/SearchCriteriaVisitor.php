<?php

namespace GravityKit\GravityView\Search\Querying\Visitors;

use GravityKit\GravityView\Search\Querying\SearchFilter;
use GravityKit\GravityView\Search\Querying\SearchFilterVisitor;
use GV\Logger;
use GV\View;
use GravityView_Deprecated_Hook_Notices;

/**
 * Converts a {@see SearchRequest} into various filter options.
 *
 * @since 3.0.0
 */
final class SearchCriteriaVisitor extends AbstractSearchFilterVisitor {
	/**
	 * The search criteria object.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	private array $search_criteria;

	/**
	 * Creates the visitor.
	 *
	 * @since 3.0.0
	 *
	 * @param View|null   $view            The View.
	 * @param array       $search_criteria The initial search criteria.
	 * @param Logger|null $logger          The logger.
	 */
	public function __construct(
		?View $view = null,
		array $search_criteria = [],
		?Logger $logger = null
	) {
		parent::__construct( $view, $logger );
		$this->search_criteria = $search_criteria;
	}

	/**
	 * Retrieves the search mode from the provided data array.
	 *
	 * @since 3.0.0
	 *
	 * @return "any"|"all" The search mode.
	 */
	public function get_mode(): string {
		$mode = SearchFilter::MODE_AND === $this->search_mode ? 'all' : 'any';

		/**
		 * @deprecated 3.0.0 Use `gk/gravityview/search/criteria/mode`.
		 */
		$mode = GravityView_Deprecated_Hook_Notices::apply_filters(
			'gravityview/search/mode',
			[ $mode ],
			'2.55',
			'gk/gravityview/search/criteria/mode'
		);

		/**
		 * Modifies the search criteria mode.
		 *
		 * @since  3.0.0
		 *
		 * @param string $mode Search mode (`any` vs `all`).
		 */
		$mode = strtolower( apply_filters( 'gk/gravityview/search/criteria/mode', $mode ) );

		return in_array( $mode, [ 'any', 'all' ], true ) ? $mode : 'any';
	}

	/**
	 * Handles a single filter and adds it to the search criteria.
	 *
	 * @since 3.0.0
	 *
	 * @param SearchFilter $filter The filter data.
	 */
	private function handle_filter( SearchFilter $filter ): void {
		$filter = $this->maybe_trim_value( $filter );

		if ( ! $filter->has_value() && $this->should_ignore_empty_values( $filter ) ) {
			$this->logger->debug( 'Ignoring empty filter value for "{key}".', [ 'key' => $filter->key() ] );

			return;
		}

		// We handle the date range differently because it is outside the `field_filters`.
		if ( 'entry_date' === $filter->key() ) {
			$this->handle_date_range( $filter );

			return;
		}

		$this->handle_field( $filter );
	}

	/**
	 * Handles a form field filter.
	 *
	 * @since 3.0.0
	 *
	 * @param SearchFilter $filter The filter.
	 */
	private function handle_field( SearchFilter $filter ): void {
		// Adjust the filter through the search field.
		$filter = $this->adjust_filter( $filter );
		if ( ! $filter ) {
			return;
		}

		$operator = $this->resolve_operator( $filter );

		$field_filter = [
			'key'      => $filter->key(),
			'value'    => $filter->value(),
			'operator' => $operator,
		];

		if ( $filter->is_numeric() ) {
			$field_filter['is_numeric'] = true;
		}

		if ( $filter->is_required() ) {
			$field_filter['required'] = true;
		}

		$form_id = $filter->form_id();
		if ( $form_id && ! empty( $filter->key() ) ) {
			// Don't set the form ID for global search.
			$field_filter['form_id'] = $form_id;
		}

		$this->search_criteria['field_filters'][] = $field_filter;
	}

	/**
	 * Handles an `entry_date` filter.
	 *
	 * @since 3.0.0
	 *
	 * @param SearchFilter $search_filter The search filter.
	 */
	private function handle_date_range( SearchFilter $search_filter ): void {
		[ $start, $end ] = $this->resolve_date_range( $search_filter );

		if ( $start ) {
			$this->search_criteria['start_date'] = $start->format( 'Y-m-d H:i:s' );
		}

		if ( $end ) {
			$this->search_criteria['end_date'] = $end->format( 'Y-m-d H:i:s' );
		}
	}

	/**
	 * Handles a group filter.
	 *
	 * @since 3.0.0
	 *
	 * @param SearchFilter $filter The group filter.
	 */
	private function handle_group( SearchFilter $filter ): void {
		/**
		 * If range type, set search mode to AND.
		 * Note: this will change the behavior for additional search filters as well.
		 *
		 * We are keeping this for backwards compatibility. When moving to Query Filters, this won't be a problem.
		 */
		if ( $this->is_range_search( $filter ) ) {
			$this->logger->debug( 'Switched mode to AND ("all") due to range search.' );

			$this->search_mode = SearchFilter::MODE_AND;

			return;
		}

		/**
		 * If OR group (e.g., repeater subfield expansion), switch search mode to OR.
		 *
		 * GF's flat search criteria cannot express nested groups, so when a repeater
		 * field expands into OR'd subfields, we must switch the entire mode to OR.
		 * Same trade-off as the range search override above.
		 */
		if ( SearchFilter::MODE_OR === $filter->mode() ) {
			$this->logger->debug( 'Switched mode to OR ("any") due to OR group (e.g., repeater field).' );

			$this->set_search_mode( SearchFilter::MODE_OR );
		}
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 3.0.0
	 */
	public function visit( SearchFilter $search_filter ): void {
		if ( $search_filter->is_group() ) {
			$this->handle_group( $search_filter );

			return;
		}

		if ( null === $search_filter->key() ) {
			// Empty filter (e.g., a group with no conditions, or `search_all` with no value); nothing to apply.
			return;
		}

		if ( ! $this->is_searchable_field( $search_filter->key(), $search_filter->form_id() ) ) {
			$this->logger->debug(
				'Field "{key}" is not searchable on form {form_id}.',
				[
					'key'     => $search_filter->key(),
					'form_id' => (int) $search_filter->form_id(),
				]
			);

			return;
		}

		$this->handle_filter( $search_filter );
	}

	/**
	 * Returns the search criteria.
	 *
	 * @since 3.0.0
	 *
	 * @return array The search criteria.
	 */
	public function get_criteria(): array {
		$criteria = $this->search_criteria;
		unset( $criteria['field_filters']['mode'] );

		$criteria['field_filters'] = array_merge(
			[ 'mode' => $this->get_mode() ],
			array_values( array_unique( $criteria['field_filters'] ?? [], SORT_REGULAR ) )
		);

		$this->logger->debug( 'Returned Search Criteria: ', [ 'data' => $criteria ] );

		return $criteria;
	}

	/**
	 * Returns whether the filter(group) is a range filter.
	 *
	 * @since 3.0.0
	 *
	 * @param SearchFilter $filter The filter to test.
	 *
	 * @return bool Whether the filter is a range filter.
	 */
	private function is_range_search( SearchFilter $filter ): bool {
		if (
			$filter->is_group()
			&& SearchFilter::MODE_AND === $filter->mode()
			&& count( $filter->conditions() ) > 1
		) {
			foreach ( $filter->conditions() as $condition ) {
				if ( $this->is_range_search( $condition ) ) {
					return true;
				}
			}

			return false;
		}

		return in_array( $filter->operator(), [ '>=', '<=', '>', '<' ], true );
	}

	/**
	 * Returns the order in which the visitor needs to be applied.
	 *
	 * @since 3.0.0
	 *
	 * @return string The visitor order.
	 */
	public function get_order(): string {
		return SearchFilterVisitor::ORDER_PRE;
	}
}
