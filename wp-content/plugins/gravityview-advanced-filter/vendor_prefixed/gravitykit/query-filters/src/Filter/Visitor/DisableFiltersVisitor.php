<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 01-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor;

use GravityKit\AdvancedFilter\QueryFilters\Filter\Filter;

/**
 * Disables filter groups and group conditions based on a 1-index value.
 *
 * @param array $filters The filters.
 *
 * @return array The filters with the disabled filters set to `null`.
 */
final class DisableFiltersVisitor implements FilterVisitor {
	/**
	 * The filters to disable.
	 * @since 2.0.0
	 * @var array
	 */
	private $disabled_filters;

	/**
	 * Creates the filter.
     *
     * To disable a group, you add the group number. To disable a field, provide the field number inside the group.
     * For example: `['2', '3.4']` would disable the second group completely and the 4th field in the 3rd group.
     *
	 * @filter `gk/query-filters/filter/disable-filters` Add disabled filters.
     *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->disabled_filters = apply_filters( 'gk/query-filters/filter/disable-filters', [] );
	}

	/**
	 * @inheritDoc
	 * @since 2.0.0
	 */
	public function visit_filter( Filter $filter, string $level = '0' ) {
		if ( in_array( $level, $this->disabled_filters, false ) ) {
			$filter->disable();
		}
	}
}
