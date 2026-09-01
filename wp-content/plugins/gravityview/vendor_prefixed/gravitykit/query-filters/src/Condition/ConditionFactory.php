<?php

namespace GravityKit\GravityView\QueryFilters\Condition;

use GF_Field;
use GF_Query_Column;
use GF_Query_Literal;
use GFAPI;
use GF_Query;
use GF_Query_Condition;
use GFCommon;
use GravityKit\GravityView\QueryFilters\Condition\FactoryHandler\CreatedByFactoryHandler;
use GravityKit\GravityView\QueryFilters\Condition\FactoryHandler\FieldComparisonFactoryHandler;
use GravityKit\GravityView\QueryFilters\Filter\Filter;

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
			[
				new CreatedByFactoryHandler(),
				new FieldComparisonFactoryHandler(),
			]
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

		$is_has_all = 'has_all' === $operator;
		if ( $is_has_all ) {
			$operator = 'in';
		}

		if ( $this->is_not_contains( $filter ) ) {
			$value    = '%' . $value . '%';
			$operator = GF_Query_Condition::NLIKE;
		}

		// GF_Query_Condition requires a Series (array) on the right-hand side for IN/NOT IN; coerce scalars.
		if ( in_array( $operator, [ 'in', 'notin' ], true ) && ! is_array( $value ) ) {
			$value = [ $value ];
		}

		$field      = GFAPI::get_field( $form_id, $filter->key() ) ?: null;
		$is_numeric = $field && $this->is_numeric_field( $field ) && is_numeric( $value );

		if ( ! $is_has_all ) {
			$choice_condition = $this->from_choice_inputs( $field, $form_id, $filter->key(), $value, $operator );
			if ( $choice_condition ) {
				return $choice_condition;
			}
		}

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
		$field       = GFAPI::get_field( $form_id, $filter->key() ) ?: null;
		$where       = $query_parts['where'] ?? null;
		if ( ! $where instanceof GF_Query_Condition ) {
			return null;
		}

		if ( $field ) {
			$where = $this->update_empty_numeric_filter_condition( $filter, $where, $field );
			if ( $is_numeric ) {
				$where = $this->update_product_condition( $where, $field );
			}
		}

		if ( $is_has_all ) {
			$where = Has_All_Condition::wraps( $where );
		}

		if ( ! is_numeric( $filter->key() ) || 0 === (int) $filter->key() ) {
			return $where;
		}

		$column = new GF_Query_Column( (string) (int) $filter->key(), $form_id );
		$where  = Owned_Entry_Condition::wraps( $where, $form_id );

		$absent_or_match = $this->from_absent_or_match( $field, $form_id, $filter->key(), $value, $operator );
		if ( $absent_or_match && $this->is_negative_lookup( $filter, $where ) ) {
			return $absent_or_match;
		}

		if ( $this->is_negative_lookup( $filter, $where ) ) {
			return GF_Query_Condition::_or( $where, Field_Presence_Condition::absent( $column ) );
		}

		if ( empty( $filter->value() ) && GF_Query_Condition::NEQ === $where->operator ) {
			$where = GF_Query_Condition::_and( $where, Field_Presence_Condition::present( $column ) );
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
	 * Returns a condition matching a choice field on the values stored across its inputs.
	 *
	 * A field storing its choices across entry inputs (a checkbox) keeps no row under its parent ID,
	 * so Gravity Forms matches the parent ID with a subquery correlated to the form's own table. A
	 * form joined through meta has no such table, and the subquery references one the query never
	 * selects from. Naming the owning entry directly needs no table of its own.
	 *
	 * Returns null for anything but a membership comparison against non-empty values on a choice
	 * field, leaving the filter to the regular path, which answers whether the field is filled.
	 *
	 * @since 2.16.0
	 *
	 * @param GF_Field|null $field    The field being filtered.
	 * @param int           $form_id  The form ID.
	 * @param string|int    $key      The filter key.
	 * @param mixed         $value    The filter value(s).
	 * @param string        $operator The filter operator.
	 *
	 * @return GF_Query_Condition|null The condition, or null to fall through.
	 */
	private function from_choice_inputs( ?GF_Field $field, int $form_id, $key, $value, string $operator ): ?GF_Query_Condition {
		if ( ! $field || ! in_array( $operator, [ 'in', 'is', '=' ], true ) ) {
			return null;
		}

		$key = (string) $key;
		if ( ! ctype_digit( $key ) ) {
			return null;
		}

		if ( ! is_array( $field->get_entry_inputs() ) || ! is_array( $field->choices ?? null ) || ! $field->choices ) {
			return null;
		}

		$values = [];
		foreach ( (array) $value as $choice_value ) {
			if ( ! is_scalar( $choice_value ) || '' === (string) $choice_value ) {
				return null;
			}

			$values[] = (string) $choice_value;
		}

		if ( [] === $values ) {
			return null;
		}

		return new Choice_Value_Condition( new GF_Query_Column( $key, $form_id ), $values );
	}

	/**
	 * Returns a negative comparison that also matches entries storing no value for the field.
	 *
	 * Gravity Forms answers "this entry holds no value for the field" with a subquery keyed on the
	 * form's own table. A form joined through meta has no table, so that subquery names one the query
	 * never selects from; building the comparison here resolves the entry while the SQL renders.
	 *
	 * Returns null for anything but a plain negative comparison on a single-value text field, leaving
	 * the filter to the regular path, which compares numeric and product values on their own terms.
	 *
	 * @since 2.16.0
	 *
	 * @param GF_Field|null $field    The field being filtered.
	 * @param int           $form_id  The form ID.
	 * @param string|int    $key      The filter key.
	 * @param mixed         $value    The filter value.
	 * @param string        $operator The filter operator.
	 *
	 * @return GF_Query_Condition|null The condition, or null to fall through.
	 */
	private function from_absent_or_match( ?GF_Field $field, int $form_id, $key, $value, string $operator ): ?GF_Query_Condition {
		$compare = [
			'isnot'                   => GF_Query_Condition::NEQ,
			'is_not'                  => GF_Query_Condition::NEQ,
			'!='                      => GF_Query_Condition::NEQ,
			GF_Query_Condition::NEQ   => GF_Query_Condition::NEQ,
			GF_Query_Condition::NLIKE => GF_Query_Condition::NLIKE,
		][ $operator ] ?? null;

		if (
			! $field
			|| null === $compare
			|| ! is_scalar( $value )
			|| ! ctype_digit( (string) $key )
			|| is_array( $field->get_entry_inputs() )
			|| $this->is_numeric_field( $field )
			|| $this->is_product_field( $field )
		) {
			return null;
		}

		$column = new GF_Query_Column( (string) $key, $form_id );

		return GF_Query_Condition::_or(
			new Field_Match_Condition( $column, $compare, new GF_Query_Literal( (string) $value ) ),
			Field_Presence_Condition::absent( $column )
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
		if ( in_array( $field->type, [ 'quantity', 'number' ], true ) ) {
			return false;
		}

		return GFCommon::is_product_field( $field->type ?? '' );
	}
}
