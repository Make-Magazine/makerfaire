<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Condition;

use GF_Query_Column;
use GF_Query_Condition;
use GF_Query_Literal;

/**
 * Compares a field's stored value, matching only entries that store one.
 *
 * Gravity Forms answers a negative comparison on a field by also asking whether the entry stores the
 * field at all, in a subquery keyed on the form's own table. A form joined through meta has no table,
 * so that subquery names one the query never selects from. This condition compares the stored value
 * and nothing else, leaving {@see Field_Presence_Condition} to ask the other half correctly.
 *
 * @since 2.16.0
 */
final class Field_Match_Condition extends GF_Query_Condition {
	/**
	 * The field being compared.
	 *
	 * @since 2.16.0
	 *
	 * @var GF_Query_Column
	 */
	private $column;

	/**
	 * The comparison operator.
	 *
	 * @since 2.16.0
	 *
	 * @var string
	 */
	private $compare;

	/**
	 * The value compared against.
	 *
	 * @since 2.16.0
	 *
	 * @var GF_Query_Literal
	 */
	private $literal;

	/**
	 * Creates the condition.
	 *
	 * @since 2.16.0
	 *
	 * @param GF_Query_Column  $column  The field being compared.
	 * @param string           $compare The comparison operator.
	 * @param GF_Query_Literal $literal The value compared against.
	 */
	public function __construct( GF_Query_Column $column, string $compare, GF_Query_Literal $literal ) {
		parent::__construct( $column, $compare, $literal );

		$this->column  = $column;
		$this->compare = $compare;
		$this->literal = $literal;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.16.0
	 */
	public function sql( $query ) {
		$alias = $query->_alias( $this->column->field_id, $this->column->source, 'm' );

		$meta_key = ( new GF_Query_Condition(
			new GF_Query_Column( 'meta_key', $this->column->source, $alias ),
			GF_Query_Condition::EQ,
			new GF_Query_Literal( $this->column->field_id )
		) )->sql( $query );

		$value = ( new GF_Query_Column( 'meta_value', $this->column->source, $alias ) )->sql( $query );

		return sprintf(
			'(%s AND %s %s %s)',
			$meta_key,
			$value,
			$this->compare,
			$this->literal->sql( $query )
		);
	}
}
