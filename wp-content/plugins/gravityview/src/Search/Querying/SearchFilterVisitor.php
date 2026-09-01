<?php

namespace GravityKit\GravityView\Search\Querying;

/**
 * Represents a Filter Visitor, that get's applied recursively down the tree.
 *
 * @since 3.0.0
 */
interface SearchFilterVisitor {
	/**
	 * Order of operations.
	 *
	 * @since 3.0.0
	 */
	public const ORDER_PRE  = 'pre-order';
	public const ORDER_POST = 'post-order';

	/**
	 * Visits the Search Filter.
	 *
	 * @since 3.0.0
	 *
	 * @param SearchFilter $search_filter The Search Filter.
	 */
	public function visit( SearchFilter $search_filter ): void;

	/**
	 * Returns the order the visitor should visit.
	 *
	 * @since 3.0.0
	 *
	 * @return string|null
	 */
	public function get_order(): ?string;
}
