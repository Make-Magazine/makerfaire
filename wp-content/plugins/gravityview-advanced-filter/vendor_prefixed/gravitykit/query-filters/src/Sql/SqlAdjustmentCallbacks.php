<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 01-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Sql;

/**
 * Class that contains all raw SQL adjustment callbacks.
 * @since 2.0.0
 */
final class SqlAdjustmentCallbacks {
	/**
	 * Adjusts the WHERE clause in the query for empty date meta fields.
	 *
	 * @param array $query The query object.
	 *
	 * @return array The adjusted query object.
	 * @since 2.0.0
	 */
	public static function sql_empty_date_adjustment( array $query ): array {
		// Depending on the database configuration, a statement like "date_updated = ''" may throw an "incorrect DATETIME value" error
		// Also, "date_updated" is always populated with the "date_created" value when an entry is created, so an empty "date_updated" (that is, it was never changed) should equal "date_created"
		// $match[0] = `table_name`.`date_updated|date_created|payment_date` = ''
		// $match[1] = `table_name`.`date_updated|date_created|payment_date`
		// $match[2] = `table_name`
		preg_match( "/((`\w+`)\.`(?:date_updated|date_created|payment_date)`) !?= ''/ism", $query['where'] ?? null, $match );

		if ( empty( $query['where'] ) || ! $match ) {
			return $query;
		}

		$operator      = strpos( $match[0], '!=' ) !== false ? '!=' : '=';
		$new_condition = sprintf( 'UNIX_TIMESTAMP(%s) %s 0', $match[1], $operator );

		// Change "date_updated = ''" to "UNIX_TIMESTAMP(date_updated) = 0" (or "!= 0) depending on the operator
		$query['where'] = str_replace( $match[0], $new_condition, $query['where'] );

		if ( strpos( $match[0], 'date_updated' ) !== false ) {
			// Add "OR date_updated = date_created" condition
			if ( '=' === $operator ) {
				$query['where'] = str_replace( $new_condition, sprintf( '(%s OR %s = %s.`date_created`)', $new_condition, $match[1], $match[2] ), $query['where'] );
			} else {
				// Add "AND date_updated != date_created" condition
				$query['where'] = str_replace( $new_condition, sprintf( '(%s AND %s != %s.`date_created`)', $new_condition, $match[1], $match[2] ), $query['where'] );
			}
		}

		return $query;
	}
}
