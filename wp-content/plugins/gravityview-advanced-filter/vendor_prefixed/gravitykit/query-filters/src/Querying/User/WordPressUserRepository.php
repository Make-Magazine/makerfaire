<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Querying\User;

use wpdb;

/**
 * User repository backed by direct SQL against `wp_users` + `wp_usermeta`.
 *
 * Mirrors the JOIN strategy used by {@see \GravityKit\QueryFilters\Condition\Created_By_Condition},
 * so search semantics stay consistent across the codebase.
 *
 * @since 2.12.0
 */
final class WordPressUserRepository {
	/**
	 * Built-in `wp_users` columns that can be searched against.
	 *
	 * @since 2.12.0
	 */
	private const COLUMN_FIELDS = [
		UserCriteria::FIELD_LOGIN,
		UserCriteria::FIELD_EMAIL,
		UserCriteria::FIELD_NICENAME,
		UserCriteria::FIELD_DISPLAY_NAME,
		UserCriteria::FIELD_URL,
	];

	/**
	 * Fields backed by `wp_usermeta`.
	 *
	 * @since 2.12.0
	 */
	private const META_FIELDS = [
		UserCriteria::FIELD_FIRST_NAME,
		UserCriteria::FIELD_LAST_NAME,
		UserCriteria::FIELD_NICKNAME,
	];

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
	 * @param wpdb|null $wpdb The WordPress database abstraction. Defaults to the global instance.
	 */
	public function __construct( ?wpdb $wpdb = null ) {
		$this->wpdb = $wpdb ?? $GLOBALS['wpdb'];
	}

	/**
	 * Returns the users matching the criteria.
	 *
	 * @since 2.12.0
	 *
	 * @param UserCriteria $criteria The search criteria.
	 *
	 * @return User[] The matching users.
	 */
	public function search( UserCriteria $criteria ): array {
		$where_sql        = $this->build_where( $criteria );
		$label_joins      = $this->build_label_joins( $criteria->label_fields() );
		$label_expression = $this->build_label_expression( $criteria->label_fields() );

		// Identifiers below are derived from allowlisted constants; values are bound via prepare.
		$sql = sprintf(
			'SELECT u.ID AS id, %1$s AS label
			 FROM %2$s u
			 %3$s
			 WHERE %4$s
			 ORDER BY label ASC, u.ID ASC
			 LIMIT %5$d OFFSET %6$d',
			$label_expression,
			$this->wpdb->users,
			$label_joins,
			$where_sql,
			$criteria->limit(),
			$criteria->offset()
		);

		$rows = $this->wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		if ( ! is_array( $rows ) ) {
			return [];
		}

		$out = [];
		foreach ( $rows as $row ) {
			$out[] = new User( (int) $row->id, (string) ( $row->label ?? '' ) );
		}

		return $out;
	}

	/**
	 * Counts the users matching the criteria. Ignores limit/offset.
	 *
	 * @since 2.12.0
	 *
	 * @param UserCriteria $criteria The criteria.
	 *
	 * @return int The total matching count.
	 */
	public function count( UserCriteria $criteria ): int {
		$sql = sprintf( 'SELECT COUNT(*) FROM %1$s u WHERE %2$s', $this->wpdb->users, $this->build_where( $criteria ) );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return (int) $this->wpdb->get_var( $sql );
	}

	/**
	 * Build the WHERE clause body for the criteria.
	 *
	 * @since 2.12.0
	 *
	 * @param UserCriteria $criteria The criteria.
	 *
	 * @return string The prepared WHERE fragment (without the leading `WHERE`).
	 */
	private function build_where( UserCriteria $criteria ): string {
		$clauses = [];

		if ( $criteria->current_site() ) {
			$clauses[] = $this->build_blog_scope();
		}

		if ( $criteria->ids() !== [] ) {
			$placeholders = implode( ',', array_fill( 0, count( $criteria->ids() ), '%d' ) );
			$clauses[]    = $this->wpdb->prepare( "u.ID IN ({$placeholders})", $criteria->ids() ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		} elseif ( $criteria->needle() !== '' ) {
			$search_clause = $this->build_search_exists( $criteria );
			if ( $search_clause !== null ) {
				$clauses[] = $search_clause;
			}
		}

		return $clauses === [] ? '1=1' : implode( ' AND ', $clauses );
	}

	/**
	 * Build the EXISTS clause that restricts the result set to users with a role on the current blog.
	 *
	 * @since 2.12.0
	 *
	 * @return string The prepared scope clause.
	 */
	private function build_blog_scope(): string {
		return $this->wpdb->prepare(
			"EXISTS (SELECT 1 FROM {$this->wpdb->usermeta} um_scope WHERE um_scope.user_id = u.ID AND um_scope.meta_key = %s)", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$this->wpdb->get_blog_prefix() . 'capabilities'
		);
	}

	/**
	 * Build the `EXISTS (…)` subquery that filters rows down to users matching the search needle
	 * across every configured search field.
	 *
	 * @since 2.12.0
	 *
	 * @param UserCriteria $criteria The criteria.
	 *
	 * @return string|null The prepared EXISTS clause, or null when the search list is empty.
	 */
	private function build_search_exists( UserCriteria $criteria ): ?string {
		$columns = array_values( array_intersect( $criteria->search_fields(), self::COLUMN_FIELDS ) );
		$meta    = array_values( array_intersect( $criteria->search_fields(), self::META_FIELDS ) );
		$by_id   = in_array( UserCriteria::FIELD_ID, $criteria->search_fields(), true )
			&& ctype_digit( $criteria->needle() );

		if ( $columns === [] && $meta === [] && ! $by_id ) {
			return null;
		}

		$conditions = [];
		$needle     = '%' . $this->wpdb->esc_like( $criteria->needle() ) . '%';

		foreach ( $columns as $column ) {
			$conditions[] = $this->wpdb->prepare( "u2.`{$column}` LIKE %s", $needle ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		}

		foreach ( $meta as $key ) {
			$conditions[] = $this->wpdb->prepare(
				'(um2.meta_key = %s AND um2.meta_value LIKE %s)',
				$key,
				$needle
			);
		}

		if ( $by_id ) {
			$conditions[] = $this->wpdb->prepare( 'u2.ID = %d', (int) $criteria->needle() );
		}

		$where = '(' . implode( ' OR ', $conditions ) . ')';

		return sprintf(
			'EXISTS (SELECT 1 FROM %1$s u2 LEFT JOIN %2$s um2 ON u2.ID = um2.user_id WHERE u2.ID = u.ID AND %3$s)',
			$this->wpdb->users,
			$this->wpdb->usermeta,
			$where
		);
	}

	/**
	 * Build the LEFT JOIN clauses needed to expose every meta label field on the outer query.
	 *
	 * @since 2.12.0
	 *
	 * @param string[] $label_fields The label candidates.
	 *
	 * @return string The joined LEFT JOIN clauses (empty string when no meta labels are used).
	 */
	private function build_label_joins( array $label_fields ): string {
		$joins = [];
		foreach ( $label_fields as $field ) {
			if ( ! in_array( $field, self::META_FIELDS, true ) ) {
				continue;
			}

			$alias           = 'um_' . $field;
			$joins[ $field ] = $this->wpdb->prepare(
				"LEFT JOIN {$this->wpdb->usermeta} `{$alias}` ON `{$alias}`.user_id = u.ID AND `{$alias}`.meta_key = %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$field
			);
		}

		return implode( "\n", $joins );
	}

	/**
	 * Build the COALESCE expression that resolves to the first non-empty label candidate.
	 *
	 * @since 2.12.0
	 *
	 * @param string[] $label_fields The label candidates.
	 *
	 * @return string The SQL expression.
	 */
	private function build_label_expression( array $label_fields ): string {
		$expressions = [];
		foreach ( $label_fields as $field ) {
			if ( in_array( $field, self::META_FIELDS, true ) ) {
				$expressions[] = "NULLIF(`um_{$field}`.meta_value, '')";
			} else {
				$expressions[] = "NULLIF(u.`{$field}`, '')";
			}
		}

		return 'COALESCE(' . implode( ', ', $expressions ) . ", '')";
	}
}
