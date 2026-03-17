<?php

namespace GV\Search\Querying;

/**
 * Represents a Filter Visitor, that get's applied recursively down the tree.
 *
 * @since $ver$
 */
interface Search_Filter_Visitor {
	/**
	 * Order of operations.
	 *
	 * @since $ver$
	 */
	public const ORDER_PRE  = 'pre-order';
	public const ORDER_POST = 'post-order';

	/**
	 * Visits the Search Filter.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $search_filter The Search Filter.
	 */
	public function visit( Search_Filter $search_filter ): void;

	/**
	 * Returns the order the visitor should visit.
	 *
	 * @since $ver$
	 *
	 * @return string|null
	 */
	public function get_order(): ?string;
}
