<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 01-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Condition;

use GF_Field;
use GF_Query_Column;
use GF_Query_Literal;
use GFAPI;
use GF_Query;
use GF_Query_Call;
use GF_Query_Condition;
use GFCommon;
use GFFormsModel;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Filter;

/**
 * Factory that creates a {@see GF_Query_Condition} from a set of {@see Filter}.
 * @since 2.0.0
 */
final class ConditionFactory {
	/**
	 * @param Filter $filter
	 * @param int $form_id
	 *
	 * @return GF_Query_Condition|null
	 */
	public function from_filter( Filter $filter, int $form_id ): ?GF_Query_Condition {
		if ( ! $filter->is_enabled() ) {
			return null;
		}

		if ( $filter->is_logic() ) {
			return $this->process_logic_filter( $filter, $form_id );
		}

		return $this->process_filter( $filter, $form_id );
	}

	/**
	 * @param Filter $filter
	 * @param int $form_id
	 *
	 * @return GF_Query_Condition|null
	 */
	private function process_filter( Filter $filter, int $form_id ): ?GF_Query_Condition {
		if ( $filter->key() === null || $filter->value() === null ) {
			return null;
		}

		$condition = array_filter(
			[
				'key'      => $filter->key(),
				// Value needs to be `-1` to avoid database results.
				'value'    => $filter->equals( Filter::locked() ) ? - 1 : $filter->value(),
				'operator' => $filter->operator(),
			],
			function ( $v ) {
				return is_numeric( $v ) || ! empty( $v );
			}
		);

		$query = new GF_Query( $form_id, [ 'field_filters' => [ 'mode' => 'all', $condition ] ] );
		if ( ! is_callable( [ $query, '_introspect' ] ) ) {
			// fall back if this method gets removed in the future.
			return null;
		}

		$query_parts = $query->_introspect();
		$where       = $query_parts['where'];
		$field       = GFAPI::get_field( $form_id, $filter->key() ) ?: null;

		if ( $field ) {
			$where = $this->update_empty_numeric_filter_condition( $filter, $where, $field );
		}

		// In case of a negative operator, entries without the meta key should also be excluded.
		if (
			is_numeric( $filter->key() )
			&& in_array( $where->operator, [
				GF_Query_Condition::NLIKE,
				GF_Query_Condition::NBETWEEN,
				GF_Query_Condition::NEQ,
				GF_Query_Condition::NIN,
			] )
			&& ! empty( $filter->value() )
		) {
			global $wpdb;

			$sub_query = $wpdb->prepare(
				sprintf(
					"SELECT 1 FROM `%s` WHERE (`meta_key` LIKE %%s OR `meta_key` = %%d) AND `entry_id` = `%s`.`id`",
					GFFormsModel::get_entry_meta_table_name(),
					$query->_alias( null, $form_id )
				),
				sprintf( '%d.%%', $filter->key() ),
				$filter->key()
			);

			$where = GF_Query_Condition::_or(
				$where,
				new GF_Query_Condition( new GF_Query_Call( 'NOT EXISTS', [ $sub_query ] ) )
			);
		}

		return $where;
	}

	/**
	 * @param Filter $filter
	 * @param int $form_id
	 *
	 * @return GF_Query_Condition|null
	 */
	private function process_logic_filter( Filter $filter, int $form_id ): ?GF_Query_Condition {
		$conditions = array_filter( array_map(
			function ( Filter $filter ) use ( $form_id ) {
				return $this->from_filter( $filter, $form_id );
			},
			$filter->conditions()
		) );

		if ( ! $conditions ) {
			return null;
		}

		// Remove redundant groups to keep the filter as concise as possible.
		if ( count( $conditions ) === 1 ) {
			return reset( $conditions );
		}

		return $filter->mode() === Filter::MODE_OR
			? GF_Query_Condition::_or( ...$conditions )
			: GF_Query_Condition::_and( ...$conditions );
	}

	/**
	 * Updates the condition for numeric filters that compare against and empty value.
	 *
	 * @param Filter $filter The filter.
	 * @param GF_Query_Condition $where The query condition.
	 * @param GF_Field $field The field
	 *
	 * @return GF_Query_Condition The modified condition.
	 */
	private function update_empty_numeric_filter_condition( Filter $filter, GF_Query_Condition $where, GF_Field $field ): GF_Query_Condition {
		if (
			'' !== $filter->value()
			|| ! in_array( $where->operator, [
				GF_Query_Condition::EQ,
				GF_Query_Condition::IS,
				GF_Query_Condition::ISNOT,
				GF_Query_Condition::NEQ,
				GF_Query_Condition::GT,
				GF_Query_Condition::GTE,
				GF_Query_Condition::LT,
				GF_Query_Condition::LTE,
			] )
			|| ! $this->is_numeric_field( $field )
		) {
			return $where;
		}

		// GF force-casts all numeric fields to float even if the value is empty, so '' becomes '0.0' and is later dropped when converted to SQL.
		// The resulting query is "CAST(`m2`.`meta_value` AS DECIMAL(65, 6)" (i.e., matches all entries) rather than CAST(`m2`.`meta_value` AS DECIMAL(65, 6) = '' (i.e., matches only entries with empty values)
		// Ref: https://github.com/gravityforms/gravityforms/blob/2cb2c07d5c61dbc876ec34709e6a57b6a212d2c4/includes/query/class-gf-query.php#L184,L193
		return new GF_Query_Condition(
			new GF_Query_Column( $filter->key(), $field->formId ),
			in_array( $where->operator, [ GF_Query_Condition::EQ, GF_Query_Condition::IS ], true )
				? GF_Query_Condition::EQ
				: GF_Query_Condition::NEQ,
			new GF_Query_Literal( '' )
		);
	}

	/**
	 * Whether the provided field is numeric.
	 *
	 * @param GF_Field $field
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	private function is_numeric_field( GF_Field $field ): bool {
		return
			$field->type === 'number'
			|| GFCommon::is_product_field( $field->type );
	}
}
