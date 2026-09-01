<?php

namespace GravityKit\GravityView\QueryFilters\Condition;

use GF_Query_Column;
use GF_Query_Condition;
use GF_Query_Literal;
use GFFormsModel;

/**
 * Matches entries by whether they store a value for a field.
 *
 * The entry it asks about is the one owning the field, which on a joined form is the joined entry
 * and not the entry being selected. Gravity Forms keys the same question on the form's own table,
 * which a form joined through meta does not have. The entry is named while the SQL renders, when the
 * running query knows how each form is reached.
 *
 * @since 2.16.0
 */
final class Field_Presence_Condition extends GF_Query_Condition {
	use Resolves_Owner_Entry;

	/**
	 * The field being tested.
	 *
	 * @since 2.16.0
	 *
	 * @var GF_Query_Column
	 */
	private $column;

	/**
	 * Whether the entry must store no value rather than a value.
	 *
	 * @since 2.16.0
	 *
	 * @var bool
	 */
	private $absent;

	/**
	 * Require the named constructors.
	 *
	 * @since 2.16.0
	 */
	private function __construct() {
		parent::__construct();
	}

	/**
	 * Matches entries storing a value for the field.
	 *
	 * @since 2.16.0
	 *
	 * @param GF_Query_Column $column The field being tested.
	 *
	 * @return self The condition.
	 */
	public static function present( GF_Query_Column $column ): self {
		$condition         = new self();
		$condition->column = $column;
		$condition->absent = false;

		return $condition;
	}

	/**
	 * Matches entries storing no value for the field.
	 *
	 * @since 2.16.0
	 *
	 * @param GF_Query_Column $column The field being tested.
	 *
	 * @return self The condition.
	 */
	public static function absent( GF_Query_Column $column ): self {
		$condition         = new self();
		$condition->column = $column;
		$condition->absent = true;

		return $condition;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.16.0
	 */
	public function sql( $query ) {
		$field_id = (string) $this->column->field_id;

		return sprintf(
			'%sEXISTS(SELECT 1 FROM `%s` WHERE (`meta_key` LIKE %s OR `meta_key` = %s) AND `entry_id` = %s)',
			$this->absent ? 'NOT ' : '',
			GFFormsModel::get_entry_meta_table_name(),
			( new GF_Query_Literal( $field_id . '.%' ) )->sql( $query ),
			( new GF_Query_Literal( $field_id ) )->sql( $query ),
			$this->owner_entry_sql( $query, (int) $this->column->source )
		);
	}
}
