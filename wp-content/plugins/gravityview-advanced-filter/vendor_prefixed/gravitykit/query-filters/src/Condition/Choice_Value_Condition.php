<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Condition;

use GF_Query_Column;
use GF_Query_Condition;
use GF_Query_Literal;
use GFFormsModel;

/**
 * Matches entries holding any of the given values for a field stored across entry inputs.
 *
 * The input a choice is stored under is fixed when the entry is saved, and reordering or inserting a
 * choice moves the remaining choices onto other inputs. The same choice therefore lives at different
 * input ids depending on when the entry was saved, so the value is matched under any of the field's
 * inputs rather than the one its current position maps to.
 *
 * @since 2.16.0
 */
final class Choice_Value_Condition extends GF_Query_Condition {
	use Resolves_Owner_Entry;

	/**
	 * The field being matched.
	 *
	 * @since 2.16.0
	 *
	 * @var GF_Query_Column
	 */
	private $column;

	/**
	 * The values to match.
	 *
	 * @since 2.16.0
	 *
	 * @var string[]
	 */
	private $values;

	/**
	 * Creates the condition.
	 *
	 * @since 2.16.0
	 *
	 * @param GF_Query_Column $column The field being matched.
	 * @param string[]        $values The values to match.
	 */
	public function __construct( GF_Query_Column $column, array $values ) {
		parent::__construct();

		$this->column = $column;
		$this->values = $values;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.16.0
	 */
	public function sql( $query ): string {
		global $wpdb;

		if ( [] === $this->values ) {
			return '1 = 0';
		}

		$values = implode(
			', ',
			array_map(
				static function ( string $value ) use ( $query ): string {
					return ( new GF_Query_Literal( $value ) )->sql( $query );
				},
				$this->values
			)
		);

		return $wpdb->prepare(
			sprintf(
				'EXISTS (SELECT 1 FROM `%s` WHERE `meta_key` LIKE %%s AND `meta_value` IN (%s) AND `entry_id` = %s)',
				GFFormsModel::get_entry_meta_table_name(),
				str_replace( '%', '%%', $values ),
				$this->owner_entry_sql( $query, (int) $this->column->source )
			),
			sprintf( '%d.%%', $this->column->field_id )
		);
	}
}
