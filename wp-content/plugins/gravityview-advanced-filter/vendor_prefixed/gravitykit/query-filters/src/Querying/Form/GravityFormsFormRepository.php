<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Querying\Form;

use GFFormsModel;
use wpdb;

/**
 * Form repository backed by direct SQL against the Gravity Forms table.
 *
 * @since 2.12.0
 */
final class GravityFormsFormRepository {
	/**
	 * The WordPress database abstraction.
	 *
	 * @since 2.12.0
	 *
	 * @var wpdb
	 */
	private wpdb $wpdb;

	/**
	 * Creates the repository.
	 *
	 * @since 2.12.0
	 *
	 * @param wpdb $wpdb The WordPress database abstraction.
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/**
	 * Returns the forms matching the criteria.
	 *
	 * @since 2.12.0
	 *
	 * @param FormCriteria $criteria The search criteria.
	 *
	 * @return Form[] The matching forms.
	 */
	public function search( FormCriteria $criteria ): array {
		$table = GFFormsModel::get_form_table_name();
		$where = $this->build_where( $criteria );

		// $where is the output of $wpdb->prepare() and may contain `%` literals — pass it as a %s
		// argument so sprintf treats those bytes as a value rather than parsing them as tokens.
		$sql = sprintf(
			"SELECT id, title FROM {$table} %s ORDER BY title ASC, id ASC LIMIT %d OFFSET %d",
			$where,
			$criteria->limit(),
			$criteria->offset()
		);

		$rows = $this->wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		if ( ! is_array( $rows ) ) {
			return [];
		}

		$forms = [];
		foreach ( $rows as $row ) {
			$forms[] = new Form( (int) $row->id, (string) $row->title );
		}

		return $forms;
	}

	/**
	 * Counts the forms matching the criteria. Ignores limit/offset.
	 *
	 * @since 2.12.0
	 *
	 * @param FormCriteria $criteria The criteria.
	 *
	 * @return int The total matching count.
	 */
	public function count( FormCriteria $criteria ): int {
		$table = GFFormsModel::get_form_table_name();
		$where = $this->build_where( $criteria );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return (int) $this->wpdb->get_var( "SELECT COUNT(*) FROM {$table} {$where}" );
	}

	/**
	 * Build the WHERE clause for the criteria.
	 *
	 * @since 2.12.0
	 *
	 * @param FormCriteria $criteria The criteria.
	 *
	 * @return string The WHERE fragment (empty when no filters apply).
	 */
	private function build_where( FormCriteria $criteria ): string {
		$where_parts = [];

		if ( ! $criteria->include_trash() ) {
			$where_parts[] = $this->wpdb->prepare( 'is_trash = %d', 0 );
		}

		$match_parts = [];

		if ( '' !== $criteria->needle() ) {
			$search = $criteria->needle();

			// AND-join per-token LIKEs so "foo bar" matches titles where both tokens appear in any order.
			$tokens      = preg_split( '/\s+/', trim( $search ) ) ?: [];
			$title_parts = [];
			foreach ( $tokens as $token ) {
				if ( '' === $token ) {
					continue;
				}

				$title_parts[] = $this->wpdb->prepare(
					'title LIKE %s',
					'%' . $this->wpdb->esc_like( $token ) . '%'
				);
			}

			if ( [] !== $title_parts ) {
				$match_parts[] = 1 === count( $title_parts )
					? $title_parts[0]
					: '(' . implode( ' AND ', $title_parts ) . ')';
			}

			if ( $criteria->search_ids() && ctype_digit( $search ) ) {
				$match_parts[] = $this->wpdb->prepare( 'id = %d', (int) $search );
			}
		}

		if ( [] !== $criteria->ids() ) {
			$placeholders  = implode( ',', array_fill( 0, count( $criteria->ids() ), '%d' ) );
			$match_parts[] = $this->wpdb->prepare( "id IN ({$placeholders})", $criteria->ids() ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}

		if ( [] !== $match_parts ) {
			$where_parts[] = 1 === count( $match_parts )
				? $match_parts[0]
				: '(' . implode( ' OR ', $match_parts ) . ')';
		}

		return $where_parts ? 'WHERE ' . implode( ' AND ', $where_parts ) : '';
	}
}
