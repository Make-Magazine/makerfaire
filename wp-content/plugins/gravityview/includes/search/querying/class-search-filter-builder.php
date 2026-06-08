<?php

namespace GV\Search\Querying;

use GravityKit\GravityView\QueryFilters\Filter\FilterFactory;
use GravityKit\GravityView\QueryFilters\Filter\RandomFilterIdGenerator;
use GravityKit\GravityView\QueryFilters\QueryFilters;
use GV\Logger;
use GV\Search\Querying\Visitors\Query_Filter_Visitor;
use GV\Search\Querying\Visitors\Search_Criteria_Visitor;
use GV\View;

/**
 * Converts a {@see Search_Request} into various output types.
 *
 * @since $ver$
 */
final class Search_Filter_Builder {
	/**
	 * The logger instance.
	 *
	 * @since $ver$
	 *
	 * @var Logger|null
	 */
	private static ?Logger $logger = null;

	/**
	 * Creates the instance.
	 *
	 * @since $ver$
	 */
	private function __construct() {
	}

	/**
	 * Transforms a Search Request into Gravity Forms Search Criteria.
	 *
	 * @since $ver$
	 *
	 * @param Search_Request $request         The search request.
	 * @param View |null     $view            The View.
	 * @param array          $search_criteria The initial search criteria.
	 *
	 * @return array The search criteria.
	 */
	public static function to_search_criteria(
		Search_Request $request,
		?View $view = null,
		array $search_criteria = []
	): array {
		$search_filter = $request->to_filter();

		$visitor = new Search_Criteria_Visitor(
			$view,
			$search_criteria,
			self::get_logger(),
		);
		$visitor->set_search_mode( $request->mode() );

		$search_filter->accept( $visitor );

		return $visitor->get_criteria();
	}

	/**
	 * Transforms a Search Request into a Query Filters object.
	 *
	 * @since 2.55.0
	 *
	 * @param Search_Request $request The search request.
	 * @param View|null      $view    The View.
	 *
	 * @return QueryFilters|null The Query Filters object.
	 */
	public static function to_query_filters( Search_Request $request, ?View $view = null ): ?QueryFilters {
		$search_filter = $request->to_filter();

		$visitor = new Query_Filter_Visitor(
			new FilterFactory( new RandomFilterIdGenerator() ),
			$view,
			self::get_logger(),
		);
		$visitor->set_search_mode( $request->mode() );

		$search_filter->accept( $visitor );

		return $visitor->get_query_filters();
	}

	/**
	 * Transforms a search_criteria array into a Query Filters object.
	 *
	 * This allows modified search criteria (e.g. from the deprecated `gravityview_fe_search_criteria` filter)
	 * to still benefit from the Query Filters pipeline, including special-case handling for product fields,
	 * operator normalization, date ranges, and other adjustments performed by the {@see Query_Filter_Visitor}.
	 *
	 * @since 2.55.0
	 *
	 * @param array     $search_criteria The search criteria array with optional 'field_filters', 'start_date',
	 *                                   'end_date'.
	 * @param View|null $view            The View.
	 *
	 * @return QueryFilters|null The Query Filters object, or null if no filters could be built.
	 */
	public static function from_search_criteria( array $search_criteria, ?View $view = null ): ?QueryFilters {
		$search_filter = Search_Filter::from_search_criteria( $search_criteria );

		if ( ! $search_filter->is_group() ) {
			return null;
		}

		$visitor = new Query_Filter_Visitor(
			new FilterFactory( new RandomFilterIdGenerator() ),
			$view,
			self::get_logger(),
			true,
		);
		$visitor->set_search_mode( $search_filter->mode() ?? Search_Filter::MODE_OR );

		$search_filter->accept( $visitor );

		return $visitor->get_query_filters();
	}

	/**
	 * Sets a logger instance.
	 *
	 * @since    $ver$
	 *
	 * @param Logger|null $logger The logger.
	 *
	 * @internal Do not rely on this method.
	 */
	public static function set_logger( ?Logger $logger ): void {
		self::$logger = $logger;
	}

	/**
	 * Returns the current logger.
	 *
	 * @since 2.55.0
	 *
	 * @return Logger The logger instance.
	 */
	private static function get_logger(): Logger {
		return self::$logger ??= gravityview()->log;
	}
}
