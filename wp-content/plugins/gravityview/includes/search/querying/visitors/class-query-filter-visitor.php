<?php

namespace GV\Search\Querying\Visitors;

use Closure;
use Exception;
use GravityKit\GravityView\QueryFilters\Filter\Filter;
use GravityKit\GravityView\QueryFilters\Filter\FilterFactory;
use GravityKit\GravityView\QueryFilters\QueryFilters;
use GV\Logger;
use GV\Search\Querying\Search_Filter;
use GV\Search\Querying\Search_Filter_Visitor;
use GV\View;
use GravityView_Deprecated_Hook_Notices;

/**
 * Converts {@see Search_Filter} objects into Query Filter {@see Filter} objects.
 *
 * This visitor is designed to be applied in post-order, meaning children are
 * visited before their parent groups.
 *
 * @since 2.55.0
 */
final class Query_Filter_Visitor extends Abstract_Search_Filter_Visitor {
	/**
	 * Whether the hooks were initialized for the view.
	 *
	 * @since 2.55.0
	 *
	 * @var array<int, bool>
	 */
	private static array $is_initialized = [];

	/**
	 * The filter factory.
	 *
	 * @since 2.55.0
	 *
	 * @var FilterFactory
	 */
	private FilterFactory $filter_factory;

	/**
	 * Stack of built filters during post-order traversal.
	 *
	 * @since 2.55.0
	 *
	 * @var Filter[]
	 */
	private array $stack = [];

	/**
	 * Tracks which Search_Filters actually pushed to the stack.
	 *
	 * Maps spl_object_id => true for each filter that contributed an item.
	 *
	 * @since 2.55.0
	 *
	 * @var array<int, true>
	 */
	private array $pushed_set = [];

	/**
	 * Tracks whether the filters are based off search criteria.
	 *
	 * @since 2.55.0
	 *
	 * @var bool
	 */
	private bool $is_from_search_criteria;

	/**
	 * Creates the visitor.
	 *
	 * @since 2.55.0
	 *
	 * @param FilterFactory $filter_factory          The filter factory.
	 * @param View|null     $view                    The View.
	 * @param Logger|null   $logger                  The logger.
	 * @param bool          $is_from_search_criteria Whether the filters originate from a search criteria array.
	 */
	public function __construct(
		FilterFactory $filter_factory,
		?View $view = null,
		?Logger $logger = null,
		bool $is_from_search_criteria = false
	) {
		parent::__construct( $view, $logger );
		$this->init_hooks();

		$this->filter_factory          = $filter_factory;
		$this->is_from_search_criteria = $is_from_search_criteria;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.55.0
	 */
	public function visit( Search_Filter $search_filter ): void {
		$stack_before = count( $this->stack );

		if ( $search_filter->is_group() ) {
			$this->handle_group( $search_filter );
		} else {
			$this->handle_field( $search_filter );
		}

		if ( count( $this->stack ) > $stack_before ) {
			$this->pushed_set[ spl_object_id( $search_filter ) ] = true;
		}
	}

	/**
	 * Handles a group filter (post-order: children already on stack).
	 *
	 * @since 2.55.0
	 *
	 * @param Search_Filter $search_filter The group filter.
	 */
	private function handle_group( Search_Filter $search_filter ): void {
		// Count how many direct children actually pushed to the stack.
		$actual_count = 0;
		foreach ( $search_filter->conditions() as $child ) {
			if ( isset( $this->pushed_set[ spl_object_id( $child ) ] ) ) {
				++$actual_count;
			}
		}

		// If no children were added to the stack, skip this group.
		if ( 0 === $actual_count ) {
			return;
		}

		// Pop only the children that actually pushed (visited first in post-order).
		$children = array_splice( $this->stack, -$actual_count );

		// Convert children to arrays for the factory.
		$conditions = array_map(
			static fn( Filter $child ): array => $child->to_array(),
			$children
		);

		$group_array = [
			'version'    => 2,
			'mode'       => $this->convert_mode( $search_filter->mode() ),
			'conditions' => $conditions,
		];

		$this->stack[] = $this->filter_factory->from_array( $group_array );

		$this->pushed_set[ spl_object_id( $search_filter ) ] = true;
	}

	/**
	 * Handles a field filter.
	 *
	 * @since 2.55.0
	 *
	 * @param Search_Filter $search_filter The field filter.
	 */
	private function handle_field( Search_Filter $search_filter ): void {
		$search_filter = $this->maybe_trim_value( $search_filter );

		if ( ! $search_filter->has_value() && $this->should_ignore_empty_values( $search_filter ) ) {
			$this->logger->debug( 'Ignoring empty filter value for "{key}".', [ 'key' => $search_filter->key() ] );

			return;
		}

		// Handle entry_date especially using the date_range factory method.
		if ( 'entry_date' === $search_filter->key() ) {
			$this->handle_entry_date( $search_filter );

			return;
		}

		// Check if the field is searchable.
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

		// Adjust the filter through the search field.
		$search_filter = $this->adjust_filter( $search_filter );
		if ( ! $search_filter ) {
			return;
		}

		$this->create_leaf_filter( $search_filter );
	}

	/**
	 * Creates a leaf filter and adds it to the stack.
	 *
	 * @since 2.55.0
	 *
	 * @param Search_Filter $search_filter The leaf filter.
	 */
	private function create_leaf_filter( Search_Filter $search_filter ): void {
		$operator = $this->resolve_operator( $search_filter );

		$leaf_array = [
			'version'  => 2,
			'key'      => $search_filter->key() ?? '0',
			'value'    => $search_filter->value(),
			'operator' => $operator,
		];

		if ( $search_filter->form_id() ) {
			$leaf_array['form_id'] = $search_filter->form_id();
		}

		$this->stack[] = $this->filter_factory->from_array( $leaf_array );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.55.0
	 */
	protected function resolve_operator( Search_Filter $filter ): string {
		$operator = $this->is_from_search_criteria
			? $filter->operator()
			: parent::resolve_operator( $filter );

		// GF_Query understands `ncontains`.
		if ( in_array( strtolower( $operator ), [ 'notcontains', 'not contains' ], true ) ) {
			$operator = 'ncontains';
		}

		return $operator;
	}

	/**
	 * Handles an entry_date filter using the date_range factory method.
	 *
	 * @since 2.55.0
	 *
	 * @param Search_Filter $filter The entry_date filter.
	 */
	private function handle_entry_date( Search_Filter $filter ): void {
		[ $start, $end ] = $this->resolve_date_range( $filter );

		if ( null === $start && null === $end ) {
			return;
		}

		$this->stack[] = $this->filter_factory->date_range( $start, $end, 'date_created', true );
	}

	/**
	 * Converts the Search_Filter mode to Filter mode.
	 *
	 * @since 2.55.0
	 *
	 * @param string|null $mode The Search_Filter mode.
	 *
	 * @return string The Filter mode.
	 */
	private function convert_mode( ?string $mode ): string {
		return Search_Filter::MODE_AND === $mode ? Filter::MODE_AND : Filter::MODE_OR;
	}

	/**
	 * Returns the built Filter object.
	 *
	 * @since 2.55.0
	 *
	 * @return Filter|null The Filter, or null if no filter was visited.
	 */
	public function get_filter(): ?Filter {
		if ( [] === $this->stack ) {
			return null;
		}

		// After full traversal, stack should contain exactly one root filter.
		return $this->stack[0] ?? null;
	}

	/**
	 * Returns the Query Filters instance with the filters applied.
	 *
	 * @since 2.55.0
	 *
	 * @return QueryFilters|null The Query Filters instance.
	 */
	public function get_query_filters(): ?QueryFilters {
		$filter = $this->get_filter();
		if ( ! $filter || ! $this->view || ! $this->view->form ) {
			return null;
		}

		try {
			$query_filters = QueryFilters::create();

			return $query_filters
				->with_form( $this->view->form->form )
				->with_filters( $filter->to_array() );
		} catch ( Exception $e ) {
			$this->logger->error( $e->getMessage() );
		}

		return null;
	}

	/**
	 * Resets the visitor state for reuse.
	 *
	 * @since 2.55.0
	 */
	public function reset(): void {
		$this->stack      = [];
		$this->pushed_set = [];
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.55.0
	 */
	public function get_order(): string {
		return Search_Filter_Visitor::ORDER_POST;
	}

	/**
	 * Registers hooks for backwards compatibility with existing gravityview/ filters.
	 *
	 * @since 2.55.0
	 */
	private function init_hooks(): void {
		$view_id = $this->view ? $this->view->ID : 0;
		if ( self::$is_initialized[ $view_id ] ?? false ) {
			return;
		}

		self::$is_initialized[ $view_id ] = true;

		add_filter(
			'gk/query-filters/condition/created-by/user-meta-fields',
			Closure::fromCallable( [ $this, 'filter_created_by_user_meta_fields' ] ),
		);

		add_filter(
			'gk/query-filters/condition/created-by/user-fields',
			Closure::fromCallable( [ $this, 'filter_created_by_user_fields' ] ),
		);
	}

	/**
	 * Bridges the Query Filters hook to the existing `gravityview/` filter for backwards compatibility.
	 *
	 * @since 2.55.0
	 *
	 * @param array $user_meta_fields The user meta fields.
	 *
	 * @return array The filtered user meta fields.
	 */
	private function filter_created_by_user_meta_fields( array $user_meta_fields ): array {
		/**
		 * Filters the user meta fields used for the created-by search condition.
		 *
		 * @since      2.5
		 *
		 * @param array    $user_meta_fields The user meta field keys.
		 * @param \GV\View $view             The current View.
		 *
		 * @deprecated 2.55.0 Use the {@see 'gk/query-filters/condition/created-by/user-meta-fields'} filter instead.
		 */
		return GravityView_Deprecated_Hook_Notices::apply_filters(
			'gravityview/widgets/search/created_by/user_meta_fields',
			[ $user_meta_fields, $this->view ],
			'2.55',
			'gk/query-filters/condition/created-by/user-meta-fields'
		);
	}

	/**
	 * Bridges the Query Filters hook to the existing `gravityview/` filter for backwards compatibility.
	 *
	 * @since 2.55.0
	 *
	 * @param array $user_fields The user fields.
	 *
	 * @return array The filtered user fields.
	 */
	private function filter_created_by_user_fields( array $user_fields ): array {
		/**
		 * Filters the user fields used for the created-by search condition.
		 *
		 * @since      2.5
		 *
		 * @param array    $user_fields The user field keys.
		 * @param \GV\View $view        The current View.
		 *
		 * @deprecated 2.55.0 Use the {@see 'gk/query-filters/condition/created-by/user-fields'} filter instead.
		 *
		 */
		return GravityView_Deprecated_Hook_Notices::apply_filters(
			'gravityview/widgets/search/created_by/user_fields',
			[ $user_fields, $this->view ],
			'2.55',
			'gk/query-filters/condition/created-by/user-fields'
		);
	}
}
