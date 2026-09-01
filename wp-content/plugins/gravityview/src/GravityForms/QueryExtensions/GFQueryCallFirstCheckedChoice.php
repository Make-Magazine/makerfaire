<?php
namespace GravityKit\GravityView\GravityForms\QueryExtensions;

use GF_Query;
use GF_Query_Call;
use GF_Query_Column;
use GFAPI;
use GFFormsModel;

if ( ! class_exists( 'GF_Query_Call', false ) ) {
	return;
}

/**
 * Orders by the first checked choice of a multi-input field (e.g. Checkbox).
 *
 * Emits a correlated subquery over the entry meta table instead of one LEFT JOIN
 * per choice: a COALESCE over per-choice joins hits MySQL's 61-table join limit
 * on large choice lists. The single column parameter is the parent field; passing
 * it through keeps join/union rewriters (which remap columns) working.
 *
 * @since 3.2.0
 */
class GFQueryCallFirstCheckedChoice extends GF_Query_Call implements RewritableCall {
	/**
	 * @since 3.2.0
	 *
	 * @param GF_Query_Column $column The parent field column (whole-number field ID).
	 */
	public function __construct( GF_Query_Column $column ) {
		parent::__construct( 'FIRST_CHECKED_CHOICE', array( $column ) );
	}

	/**
	 * Returns a same-class call over the given column.
	 *
	 * @since 3.2.0
	 *
	 * @param array $parameters The replacement parameters.
	 *
	 * @return GF_Query_Call
	 */
	public function with_parameters( array $parameters ): GF_Query_Call {
		$column = reset( $parameters );

		if ( ! $column instanceof GF_Query_Column ) {
			return new GF_Query_Call( $this->function_name, $parameters );
		}

		return new static( $column );
	}

	/**
	 * Generate the correlated subquery SQL.
	 *
	 * Selections live in per-input meta rows (`6.1`, `6.2`, …); the row with the
	 * lowest input number is the first checked choice. The suffix is compared
	 * numerically (`0 +` coercion) so `6.10` sorts after `6.2`. Only the field's
	 * current input keys are matched: a prefix scan would let stale rows (deleted
	 * choices) or bogus keys (`6.audit`, coerced to 0) win the LIMIT 1. No ` AS `
	 * may appear in this SQL (rules out CAST): Multiple Forms rewrites ORDER BY
	 * calls with a `/\(.+? AS/is` replace that would corrupt it.
	 *
	 * @since 3.2.0
	 *
	 * @param GF_Query $query The query.
	 *
	 * @return string The generated SQL.
	 */
	public function first_checked_choice_sql( $query ) {
		// The magic `columns` getter returns a temporary; reset() needs a real variable.
		$columns = $this->columns;
		$column  = reset( $columns );

		if ( ! $column instanceof GF_Query_Column ) {
			// A constant keeps the ORDER BY clause valid; it just doesn't sort.
			return 'NULL';
		}

		$field_id = $column->field_id;

		// Only a whole-number parent field ID is valid here.
		if ( ! ctype_digit( (string) $field_id ) ) {
			return 'NULL';
		}

		$meta_keys = $this->input_meta_keys( $column );

		if ( ! $meta_keys ) {
			return 'NULL';
		}

		return sprintf(
			"(SELECT `em`.`meta_value` FROM `%s` `em` WHERE `em`.`entry_id` = `%s`.`id` AND `em`.`meta_key` IN (%s) ORDER BY 0 + SUBSTRING_INDEX(`em`.`meta_key`, '.', -1) LIMIT 1)",
			GFFormsModel::get_entry_meta_table_name(),
			$query->_alias( null, $column->source ),
			implode( ', ', $meta_keys )
		);
	}

	/**
	 * Returns the field's current input meta keys, quoted for an IN list.
	 *
	 * Resolved at SQL-generation time from the column, so union/join rewrites that
	 * remap the column onto another form pick up that form's inputs. Keys are
	 * validated to `<parentId>.<digits>`, which makes the quoted list injection-safe.
	 *
	 * @since 3.2.0
	 *
	 * @param GF_Query_Column $column The parent field column.
	 *
	 * @return string[] The quoted meta keys, empty when the field has no valid inputs.
	 */
	private function input_meta_keys( GF_Query_Column $column ) {
		$source  = $column->source;
		$form_id = is_array( $source ) ? reset( $source ) : $source;
		$field   = GFAPI::get_field( $form_id, $column->field_id );

		if ( ! $field || empty( $field->inputs ) || ! is_array( $field->inputs ) ) {
			return array();
		}

		$keys = array();

		foreach ( $field->inputs as $input ) {
			$key = (string) ( $input['id'] ?? '' );

			if ( ! preg_match( '/^' . (int) $column->field_id . '\.\d+$/', $key ) ) {
				continue;
			}

			$keys[] = "'" . $key . "'";
		}

		return $keys;
	}
}
