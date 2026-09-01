<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Condition\FactoryHandler;

use GF_Field;
use GF_Query_Column;
use GF_Query_Condition;
use GFAPI;
use GFCommon;
use GravityKit\AdvancedFilter\QueryFilters\Condition\Field_Comparison_Condition;
use GravityKit\AdvancedFilter\QueryFilters\MergeTag\FormMergeModifier;

/**
 * Handles a filter whose value is a field merge tag, comparing two fields' columns.
 *
 * A value such as `{:1:form:4}` compares the filtered field against field 1 of form 4 in the query
 * itself; the fields may belong to different forms (e.g. a joined form). Declined for any value that
 * is not a single field merge tag, or when either field is missing.
 *
 * @since 2.14.0
 */
final class FieldComparisonFactoryHandler {
	/**
	 * Filter operators whose SQL equivalent differs from the operator itself.
	 *
	 * Every other comparison operator (`<`, `<=`, `>`, `>=`, `=`, `!=`) is already valid SQL and is
	 * used as-is.
	 *
	 * @since 2.14.0
	 *
	 * @var array<string,string>
	 */
	private const OPERATOR_ALIASES = [
		'is'    => '=',
		'isnot' => '!=',
	];

	/**
	 * Handles the filter.
	 *
	 * @since 2.14.0
	 *
	 * @param array $filter The filter array with resolved form_id.
	 *
	 * @return false|GF_Query_Condition
	 */
	public function __invoke( array $filter ) {
		$value           = $filter['value'] ?? null;
		$target_field_id = FormMergeModifier::extract_field_id( $value );
		if ( null === $target_field_id ) {
			return false;
		}

		$form_id        = (int) ( $filter['form_id'] ?? 0 );
		$target_form_id = FormMergeModifier::extract_form_id( $value ) ?: $form_id;

		$source_field = GFAPI::get_field( $form_id, $filter['key'] ?? null ) ?: null;
		$target_field = GFAPI::get_field( $target_form_id, (int) $target_field_id ) ?: null;
		if ( ! $source_field || ! $target_field ) {
			return false;
		}

		$operator = self::OPERATOR_ALIASES[ $filter['operator'] ?? '' ] ?? ( $filter['operator'] ?? '' );

		return Field_Comparison_Condition::create(
			new GF_Query_Column( $filter['key'], $form_id ),
			new GF_Query_Column( $target_field_id, $target_form_id ),
			$operator,
			$this->is_numeric_field( $source_field ) && $this->is_numeric_field( $target_field )
		);
	}

	/**
	 * Whether the provided field is numeric.
	 *
	 * @since 2.14.0
	 *
	 * @param GF_Field $field The field.
	 *
	 * @return bool
	 */
	private function is_numeric_field( GF_Field $field ): bool {
		return 'number' === $field->type || GFCommon::is_product_field( $field->type );
	}
}
