<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 28-April-2026 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Util;

use GF_Query;
use GF_Query_Condition;

/**
 * Helper for working with `GF_Query*` classes.
 *
 * @since 2.9.0
 */
final class QueryHelper {
	/**
	 * Extracts SQL parts from a GF_Query without executing it.
	 *
	 * @since 2.9.0
	 *
	 * @param GF_Query $query The query to extract SQL from.
	 *
	 * @return array The SQL parts including select, from, where, join, etc.
	 */
	public static function get_sql_from_query( GF_Query $query ): array {
		$sql = [];

		add_filter(
			'gform_gf_query_sql',
			$select = static function ( $original_sql ) use ( &$sql, &$select ): array {
				// Single use filter.
				remove_filter( 'gform_gf_query_sql', $select );
				// Get a copy of the query.
				$sql = $original_sql;

				// Prevent the original query from running.
				return [];
			}
		);

		// Trigger SQL.
		$clone = clone $query; // Prevent any action on the original query.
		$clone->get();

		return $sql;
	}

	/**
	 * Replace conditions with a replacement, based on a conditional callback.
	 *
	 * Note that this method is recursive as conditions can contain other conditions.
	 *
	 * @since 2.9.0
	 *
	 * @param GF_Query_Condition|null          $condition    Search on this and child-expressions.
	 * @param callable                         $find         A callback that returns true if the condition should be
	 *                                                       replaced.
	 * @param callable|GF_Query_Condition|null $replace_with What the particular column conditional should be replaced
	 *                                                       with.
	 *
	 * @return GF_Query_Condition|null The replaced condition, or null.
	 */
	public static function replace_condition(
		GF_Query_Condition $condition,
		callable $find,
		$replace_with
	): ?GF_Query_Condition {
		// Actually does the replacing.
		$nested = (array) ( $condition->expressions );
		if ( ! $nested && $find( $condition ) ) {
			if ( is_callable( $replace_with ) ) {
				$replace_with = $replace_with( $condition );
			}

			return $replace_with instanceof GF_Query_Condition ? $replace_with : $condition;
		}

		$expressions = array_map(
			static function ( $condition ) use ( $replace_with, $find ) {
				return self::replace_condition( $condition, $find, $replace_with );
			},
			$nested
		);

		// Conditions cannot be modified, only re-created.
		if ( GF_Query_Condition::_AND === $condition->operator ) {
			return GF_Query_Condition::_and( ...$expressions );
		}

		if ( GF_Query_Condition::_OR === $condition->operator ) {
			return GF_Query_Condition::_or( ...$expressions );
		}

		return $condition;
	}
}
