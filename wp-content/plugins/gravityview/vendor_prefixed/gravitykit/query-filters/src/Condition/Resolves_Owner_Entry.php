<?php

namespace GravityKit\GravityView\QueryFilters\Condition;

use GF_Query;
use GF_Query_Column;

/**
 * Names the entry that owns a column while the SQL renders.
 *
 * On a joined form the owner is the joined entry rather than the entry being selected. A form joined
 * through a table of its own is reached at that table's `id`; a form joined through meta has no table
 * and is reached at the join's `entry_id`. Which of the two applies is only known once the running
 * query is at hand.
 *
 * @since 2.16.0
 */
trait Resolves_Owner_Entry {
	/**
	 * Renders the column naming the entry that owns a form's fields.
	 *
	 * Whether the source has a derived table of its own can only be told from rendered SQL, and a
	 * condition is rendered *by* that render: asking mid-flight re-enters the condition and
	 * recurses until the stack gives out. Callers that can answer outside a render pass it in;
	 * conditions cannot, and reach the joined entry through its meta alias.
	 *
	 * @since 2.16.0
	 *
	 * @param GF_Query $query            The running query.
	 * @param int      $source           The form whose owning entry is named.
	 * @param bool     $source_has_table Whether the query exposes a derived table for the source.
	 *
	 * @return string The SQL.
	 */
	private function owner_entry_sql( GF_Query $query, int $source, bool $source_has_table = false ): string {
		$parts = $query->_introspect();

		$is_selected = in_array( $source, array_map( 'intval', (array) ( $parts['from'] ?? [] ) ), true );

		if ( ! $is_selected && ! $source_has_table ) {
			foreach ( (array) ( $parts['joins'] ?? [] ) as $join ) {
				foreach ( (array) $join as $join_column ) {
					if ( ! $join_column instanceof GF_Query_Column || (int) $join_column->source !== $source ) {
						continue;
					}

					return sprintf(
						'`%s`.`entry_id`',
						$query->_alias( $join_column->field_id, $join_column->source, 'm' )
					);
				}
			}
		}

		return sprintf( '`%s`.`id`', $query->_alias( null, $source ) );
	}
}
