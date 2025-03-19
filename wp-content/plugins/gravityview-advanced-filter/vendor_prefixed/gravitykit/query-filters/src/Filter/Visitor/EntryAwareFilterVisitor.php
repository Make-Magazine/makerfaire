<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 10-February-2025 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor;

/**
 * A {@see FilterVisitor} that knows about a specific entry object.
 *
 * This interface is used by visitors that need the entry to adjust the filter value.
 *
 * @since 2.1.2
 */
interface EntryAwareFilterVisitor extends FilterVisitor {
	/**
	 * Records the current entry object.
	 *
	 * @since 2.1.2
	 *
	 * @param array $entry The entry object.
	 */
	public function set_entry( array $entry ): void;
}
