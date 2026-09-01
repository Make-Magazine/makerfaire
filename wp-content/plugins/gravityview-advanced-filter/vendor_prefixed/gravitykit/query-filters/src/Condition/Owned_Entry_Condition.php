<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Condition;

use GF_Query_Condition;

/**
 * Points a condition's entry correlation at the entry that owns the field.
 *
 * Gravity Forms compares a field's value through the meta alias the query already joins, which is
 * correct, but answers "does this entry hold the field at all" with a subquery correlated to
 * `<form table>`.`id`. A form joined through meta has no table, so that names an alias the query
 * never selects from and the comparison either fails outright or silently tests the wrong entry.
 *
 * The comparison itself is left alone, including the casts Gravity Forms applies to numeric and
 * product fields; only the correlation is redirected. A form reached through a table of its own
 * already correlates correctly and passes through untouched.
 *
 * @since 2.16.0
 */
final class Owned_Entry_Condition extends GF_Query_Condition {
	use Resolves_Owner_Entry;

	/**
	 * The condition being redirected.
	 *
	 * @since 2.16.0
	 *
	 * @var GF_Query_Condition
	 */
	private $inner;

	/**
	 * The form whose owning entry the condition is about.
	 *
	 * @since 2.16.0
	 *
	 * @var int
	 */
	private $source;

	/**
	 * Require the "::wraps" notation.
	 *
	 * @since 2.16.0
	 */
	private function __construct() {
	}

	/**
	 * Creates a new instance redirecting the provided condition.
	 *
	 * @since 2.16.0
	 *
	 * @param GF_Query_Condition $inner  The condition to redirect.
	 * @param int                $source The form whose owning entry the condition is about.
	 *
	 * @return self The redirected condition.
	 */
	public static function wraps( GF_Query_Condition $inner, int $source ): self {
		$condition         = new self();
		$condition->inner  = $inner;
		$condition->source = $source;

		return $condition;
	}

	/**
	 * Proxies property access to the inner condition.
	 *
	 * @since 2.16.0
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
	 * @since 2.16.0
	 */
	public function get_columns(): array {
		return $this->inner->get_columns();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.16.0
	 */
	public function sql( $query ) {
		$sql   = $this->inner->sql( $query );
		$owner = $this->owner_entry_sql( $query, $this->source );
		$table = sprintf( '`%s`.`id`', $query->_alias( null, $this->source ) );

		if ( $table === $owner ) {
			return $sql;
		}

		return str_replace( $table, $owner, $sql );
	}
}
