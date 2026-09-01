<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Condition;

use GF_Query;
use GF_Query_Call;
use GF_Query_Column;
use GF_Query_Condition;
use GF_Query_Literal;

/**
 * Represents a query condition that compares one field's value against another field's value.
 *
 * A field merge tag used as a filter value (e.g. `{Reorder threshold:2}`) has no literal value at
 * query time. This condition renders both fields as their `meta_value` columns so two fields are
 * compared directly in SQL. Each column carries its own source form, so the fields may belong to
 * different forms (e.g. a joined form). Aliases are resolved against the running query at render
 * time, mirroring how Gravity Forms aliases a single field column.
 *
 * Gravity Forms only rewrites a field column into its `meta_value` column on the left-hand side of a
 * condition, so a field column on the right renders empty. This subclass renders both sides itself
 * while leaving column extraction and JOIN inference to the parent.
 *
 * @since 2.14.0
 */
final class Field_Comparison_Condition extends GF_Query_Condition {
	/**
	 * Whether the values are compared numerically.
	 *
	 * @since 2.14.0
	 *
	 * @var bool
	 */
	private $is_numeric;

	/**
	 * @since 2.14.0
	 *
	 * @param GF_Query_Column $source       The column on the left of the comparison.
	 * @param GF_Query_Column $target       The column on the right of the comparison.
	 * @param string          $sql_operator The SQL comparison operator.
	 * @param bool            $is_numeric   Whether the values are compared numerically.
	 */
	private function __construct(
		GF_Query_Column $source,
		GF_Query_Column $target,
		string $sql_operator,
		bool $is_numeric
	) {
		parent::__construct( $source, $sql_operator, $target );

		$this->is_numeric = $is_numeric;
	}

	/**
	 * Creates a condition comparing two field columns.
	 *
	 * @since 2.14.0
	 *
	 * @param GF_Query_Column $source       The column on the left of the comparison.
	 * @param GF_Query_Column $target       The column on the right of the comparison.
	 * @param string          $sql_operator The SQL comparison operator.
	 * @param bool            $is_numeric   Whether the values are compared numerically.
	 *
	 * @return self
	 */
	public static function create(
		GF_Query_Column $source,
		GF_Query_Column $target,
		string $sql_operator,
		bool $is_numeric
	): self {
		return new self( $source, $target, $sql_operator, $is_numeric );
	}

	/**
	 * {@inheritDoc}
	 *
	 * The `meta_key` guards are emitted alongside the comparison. Gravity Forms folds them into the
	 * JOIN for a single-source query but expects them in the WHERE for a multi-source (joined) query;
	 * emitting them here keeps both paths correct.
	 *
	 * @since 2.14.0
	 */
	public function sql( $query ) {
		return sprintf(
			'(%s AND %s AND %s %s %s)',
			$this->meta_key_sql( $query, $this->left ),
			$this->meta_key_sql( $query, $this->right ),
			$this->column_sql( $query, $this->left ),
			$this->operator,
			$this->column_sql( $query, $this->right )
		);
	}

	/**
	 * Renders the `meta_key` guard that ties a column's alias to its field.
	 *
	 * @since 2.14.0
	 *
	 * @param GF_Query        $query  The running query.
	 * @param GF_Query_Column $column The field column.
	 *
	 * @return string
	 */
	private function meta_key_sql( GF_Query $query, GF_Query_Column $column ): string {
		$alias = $query->_alias( $column->field_id, $column->source, 'm' );

		return ( new GF_Query_Condition(
			new GF_Query_Column( 'meta_key', $column->source, $alias ),
			GF_Query_Condition::EQ,
			new GF_Query_Literal( $column->field_id )
		) )->sql( $query );
	}

	/**
	 * Renders a field's `meta_value` column, optionally cast to a decimal for numeric comparison.
	 *
	 * @since 2.14.0
	 *
	 * @param GF_Query        $query  The running query.
	 * @param GF_Query_Column $column The field column.
	 *
	 * @return string
	 */
	private function column_sql( GF_Query $query, GF_Query_Column $column ): string {
		if ( $this->is_numeric ) {
			return GF_Query_Call::CAST( $column, GF_Query::TYPE_DECIMAL )->sql( $query );
		}

		$alias = $query->_alias( $column->field_id, $column->source, 'm' );

		return ( new GF_Query_Column( 'meta_value', $column->source, $alias ) )->sql( $query );
	}
}
