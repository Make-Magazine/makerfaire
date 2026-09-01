<?php

namespace GravityKit\GravityView\QueryFilters\Condition;

use GF_Query_Column;
use GF_Query_Condition;
use GF_Query_Series;
use GFFormsModel;

/**
 * Represents a query condition that matches entries containing every selected choice.
 *
 * Wraps the regular `IN` membership condition and upgrades it from "contains any of"
 * to "contains all of" by requiring the number of distinct matched values to equal the
 * number of selected values. Extra, non-selected values on the entry are ignored.
 *
 * @since 2.14.0
 */
final class Has_All_Condition extends GF_Query_Condition {
	use Resolves_Owner_Entry;

	/**
	 * The inner membership condition being wrapped.
	 *
	 * @since 2.14.0
	 *
	 * @var GF_Query_Condition
	 */
	private $inner;

	/**
	 * Require the "::wraps" notation.
	 *
	 * @since 2.14.0
	 */
	private function __construct() {
	}

	/**
	 * Creates a new instance wrapping the provided membership condition.
	 *
	 * @since 2.14.0
	 *
	 * @param GF_Query_Condition $inner The `IN` condition to wrap.
	 *
	 * @return self The wrapped condition.
	 */
	public static function wraps( GF_Query_Condition $inner ): self {
		$condition        = new self();
		$condition->inner = $inner;

		return $condition;
	}

	/**
	 * Proxies property access to the inner condition.
	 *
	 * @since 2.14.0
	 *
	 * @param string $key The property name.
	 *
	 * @return mixed The property value.
	 */
	public function __get( $key ) {
		return $this->inner->__get( $key );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.14.0
	 */
	public function get_columns(): array {
		return $this->inner->get_columns();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.14.0
	 */
	public function sql( $query ): string {
		global $wpdb;

		$left  = $this->inner->left;
		$right = $this->inner->right;

		if ( ! $left instanceof GF_Query_Column || ! $right instanceof GF_Query_Series ) {
			return $this->inner->sql( $query );
		}

		$count = count( $right->values );
		if ( $count < 1 ) {
			return $this->inner->sql( $query );
		}

		$values = str_replace( '%', '%%', $right->sql( $query, ', ' ) );

		return $wpdb->prepare(
			sprintf(
				'EXISTS (SELECT 1 FROM `%s` WHERE `meta_key` LIKE %%s AND `meta_value` IN (%s) AND `entry_id` = %s HAVING COUNT(DISTINCT `meta_value`) = %d)',
				GFFormsModel::get_entry_meta_table_name(),
				$values,
				$this->owner_entry_sql( $query, (int) $left->source ),
				$count
			),
			sprintf( '%d.%%', $left->field_id )
		);
	}
}
