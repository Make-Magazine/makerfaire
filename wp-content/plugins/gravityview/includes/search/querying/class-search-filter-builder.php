<?php

namespace GV\Search\Querying;

use GV\Logger;
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
			$request->mode(),
			$search_criteria,
			self::$logger ?? gravityview()->log,
		);
		$search_filter->accept( $visitor );

		return $visitor->get_criteria();
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
}
