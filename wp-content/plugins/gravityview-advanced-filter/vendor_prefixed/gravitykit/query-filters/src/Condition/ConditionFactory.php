<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 28-April-2026 using {@see https://github.com/BrianHenryIE/strauss}.
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
use GravityKit\AdvancedFilter\QueryFilters\Condition\FactoryHandler\CreatedByFactoryHandler;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Filter;

/**
 * Factory that creates a {@see GF_Query_Condition} from a set of {@see Filter}.
 *
 * @since 2.0.0
 */
final class ConditionFactory {
	/**
	 * @param Filter $filter
	 * @param int    $form_id
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
	 * Whether the filter is a negative search.
	 *
	 * @since 2.2.0
	 *
	 * @param Filter             $filter The filter.
	 * @param GF_Query_Condition $where  The condition.
	 */
	private function is_negative_lookup( Filter $filter, GF_Query_Condition $where ): bool {
		if ( empty( $filter->value() ) ) {
			return GF_Query_Condition::EQ === $where->operator;
		}

		return in_array( $where->operator, [
			GF_Query_Condition::NLIKE,
			GF_Query_Condition::NBETWEEN,
			GF_Query_Condition::NEQ,
			GF_Query_Condition::NIN,
		], true );
	}

	/**
	 * @param Filter $filter
	 * @param int    $form_id
	 *
	 * @return GF_Query_Condition|null
	 */
	private function process_filter( Filter $filter, int $form_id ): ?GF_Query_Condition {
		if ( $filter->key() === null || $filter->value() === null ) {
			return null;
		}

		// Take the form ID from the filter if available.
		$form_id = $filter->form_id() ?: $form_id;

		// Allow external handlers to claim this filter.
		$filter_array            = $filter->to_array();
		$filter_array['form_id'] = $form_id;

		/**
		 * Modifies the list of condition factory handlers for a filter.
		 *
		 * Each handler is a callable that receives the filter as a plain array and returns one of the following:
		 * - `false`              — does not handle this filter; the next handler is tried.
		 * - `null`               — claims this filter but produces no condition; built-in logic is skipped.
		 * - `GF_Query_Condition` — claims this filter with the given condition; built-in logic is skipped.
		 *
		 * @since 2.10
		 *
		 * @param array<callable(array $filter_array): false|null|GF_Query_Condition> $handlers Ordered list of condition factory handler callables.
		 */
		$handlers = apply_filters(
			'gk/query-filters/condition/factory-handlers',
			[ new CreatedByFactoryHandler() ]
		);

		// Locked filters are not handled by custom handlers.
		if ( is_array( $handlers ) && ! $filter->equals( Filter::locked() ) ) {
			foreach ( $handlers as $handler ) {
				if ( ! is_callable( $handler ) ) {
					continue;
				}

				$result = $handler( $filter_array );

				if ( null !== $result && ! $result instanceof GF_Query_Condition ) {
					continue;
				}

				return $result;
			}
		}

		$value    = $filter->value();
		$operator = $filter->operator();

		if ( $this->is_not_contains( $filter ) ) {
			$value    = '%' . $value . '%';
			$operator = GF_Query_Condition::NLIKE;
		}

		$field      = GFAPI::get_field( $form_id, $filter->key() ) ?: null;
		$is_numeric = $field && $this->is_numeric_field( $field ) && is_numeric( $value );

		$condition = array_filter(
			[
				'key'        => $filter->key(),
				// Value needs to be `-1` to avoid database results.
				'value'      => $filter->equals( Filter::locked() ) ? - 1 : $value,
				'operator'   => $operator,
				'is_numeric' => $is_numeric,
			],
			static function ( $v, $k ) {
				return 'value' === $k || is_numeric( $v ) || ! empty( $v );
			},
			ARRAY_FILTER_USE_BOTH
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
			if ( $is_numeric ) {
				$where = $this->update_product_condition( $where, $field );
			}
		}

		if ( ! is_numeric( $filter->key() ) || 0 === (int) $filter->key() ) {
			return $where;
		}

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

		// In case of a negative operator, entries without the meta key should also be excluded.
		if ( $this->is_negative_lookup( $filter, $where ) ) {
			return GF_Query_Condition::_or(
				$where,
				new GF_Query_Condition( new GF_Query_Call( 'NOT EXISTS', [ $sub_query ] ) )
			);
		}

		// In case of `isnotempty` search, the meta key MUST exist.
		if ( empty( $filter->value() ) && GF_Query_Condition::NEQ === $where->operator ) {
			$where = GF_Query_Condition::_and(
				$where,
				new GF_Query_Condition( new GF_Query_Call( 'EXISTS', [ $sub_query ] ) )
			);
		}

		return $where;
	}

	/**
	 * @param Filter $filter
	 * @param int    $form_id
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
	 * @param Filter             $filter The filter.
	 * @param GF_Query_Condition $where  The query condition.
	 * @param GF_Field           $field  The field
	 *
	 * @return GF_Query_Condition The modified condition.
	 */
	private function update_empty_numeric_filter_condition(
		Filter $filter,
		GF_Query_Condition $where,
		GF_Field $field
	): GF_Query_Condition {
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
	 * @since 2.0.0
	 *
	 * @param GF_Field $field
	 *
	 * @return bool
	 */
	private function is_numeric_field( GF_Field $field ): bool {
		return
			$field->type === 'number'
			|| GFCommon::is_product_field( $field->type );
	}

	/**
	 * Returns whether the filter is a NOT CONTAINS filter.
	 *
	 * @since 2.5.0
	 *
	 * @return bool Whether the filter is a NOT CONTAINS filter.
	 */
	private function is_not_contains( Filter $filter ): bool {
		return in_array( $filter->operator(), [ 'ncontains', 'notcontains' ], true );
	}

	/**
	 * Wraps the condition in a product price condition if applicable.
	 *
	 * @since 2.9.0
	 *
	 * @param GF_Query_Condition $condition The condition to update.
	 * @param GF_Field           $field     The Gravity Forms field.
	 *
	 * @return GF_Query_Condition The updated condition.
	 */
	private function update_product_condition(
		GF_Query_Condition $condition,
		GF_Field $field
	): GF_Query_Condition {
		if (
			! $this->is_product_field( $field )
			|| in_array( $field->type, [ 'quantity', 'total' ], true )
		) {
			return $condition;
		}

		return Product_Price_Condition::wraps( $condition );
	}

	/**
	 * Whether this product is split up into multiple fields.
	 *
	 * @since 2.9.0
	 *
	 * @param GF_Field $field The Gravity Forms field.
	 *
	 * @return bool
	 */
	private function is_multipart_product_field( GF_Field $field ): bool {
		return in_array( $field->get_input_type(), [ 'singleproduct', 'hiddenproduct' ], true );
	}

	/**
	 * Returns whether the field is a product field.
	 *
	 * @since 2.9.0
	 *
	 * @param GF_Field $field The Gravity Forms field.
	 *
	 * @return bool
	 */
	private function is_product_field( GF_Field $field ): bool {
		if ( 'quantity' === $field->type ) {
			return false;
		}

		if (
			'number' === $field->get_input_type()
			&& 'currency' === ( $field->numberFormat ?? null )
		) {
			return true;
		}

		return GFCommon::is_product_field( $field->type ?? '' );
	}
}
