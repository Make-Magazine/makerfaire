<?php
/**
 * Filtering window settings: Offset and limit, pagination.
 *
 * @package GravityKit\GravityView\Entry
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry;

/**
 * Filtering window settings:
 *
 * Offset and limit, pagination.
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Entry namespace.
 */
class EntryOffset {

	/** @var int The offset. */
	public $offset = 0;

	/** @var int The limit. */
	public $limit = 20;

	/**
	 * Return a search_criteria format for this offset.
	 *
	 * @param int $page The page. Default: 1
	 *
	 * @return array ['page_size' => N, 'offset' => N]
	 */
	public function to_paging( $page = 1 ) {
		return [
			'page_size' => $this->limit,
			'offset'    => ( ( $page - 1 ) * $this->limit ) + $this->offset,
		];
	}
}
