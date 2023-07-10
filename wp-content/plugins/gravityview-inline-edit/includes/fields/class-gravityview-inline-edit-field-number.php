<?php

/**
 * @file class-gravityview-inline-edit-field-number.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Number extends GravityView_Inline_Edit_Field {

	var $gv_field_name = 'number';

	var $inline_edit_type = 'number';

	var $set_value = true;

	/**
	 * Update calculation fields and add live-update response
	 *
	 * @since 1.0
	 *
	 * @param bool|WP_Error   $update_result
	 * @param array           $entry The Entry Object that's been updated
	 * @param int             $form_id The Form ID
	 * @param GF_Field_Number $gf_field GF_Field The field that's been updated
	 *
	 * @return bool|WP_Error|array Returns original result, if not a number field. Otherwise, returns a response array. Empty if no calculation fields, otherwise multi-dimensional array with `data` and `selector` keys
	 */
	public function updated_result( $update_result, $entry = array(), $form_id = 0, GF_Field $gf_field = null ) {

		if ( ! is_bool( $update_result ) ) {
			return $update_result;
		}

		$form = GFAPI::get_form( $form_id );

		$display_value = \GFCommon::get_lead_field_display( $gf_field, $entry[ $gf_field->id ], $entry['currency'], false, 'html' );

		$response = array(
			array(
				'value'    => $entry[ $gf_field->id ],
				'selector' => ".gv-inline-editable-field-{$entry['id']}-{$entry['form_id']}-{$gf_field->id}",
				'data'     => array( 'display_value' => $display_value ),
			),
		);

		/** @var GF_Field $field */
		foreach ( $form['fields'] as $field ) {

			if ( ! $field->has_calculation() ) {
				continue;
			}

			$value = GFFormsModel::get_prepared_input_value( $form, $field, $entry, $field->id );

			GFAPI::update_entry_field( $entry['id'], $field->id, $value );

			/**
			 * Fetch entry after updating the field, in case there are multiple calculations.
			 * @see https://github.com/gravityview/Inline-Edit/issues/131
			 */
			$entry = GFAPI::get_entry( $entry['id'] );

			// Make sure $value is a number, not a string. Problem is, this is now a float and floats are wild.
			$display_value = (float) $value;

			$precision = self::get_number_precision( $display_value );

			// Floating point errors can look terrible (1.49999999999999 instead of 1.5), so make sure
			// the displayed output has the same decimal precision as the input.
			$display_value = GFCommon::round_number( $display_value, $precision );

			if ( ! empty( $field->numberFormat ) ) {

				/**
				 * Bring $field->clean_number() inline
				 * @see https://github.com/gravityview/GravityEdit/issues/200
				 */
				if ( $field->numberFormat == 'currency' ) {
					$display_value = GFCommon::to_number( $display_value );
				} else {
					$display_value = GFCommon::clean_number( $display_value, $field->numberFormat );
				}
			}

			$response[] = array(
				'value'           => $value,
				'selector'        => ".gv-inline-edit-live-{$entry['id']}-{$entry['form_id']}-{$field->id}",
				'data'            => array( 'display_value' => $display_value ),
				'has_calculation' => true,
			);
		}

		return $response;
	}

	/**
	 * Get the level of precision of a number to counteract floating point errors.
	 *
	 * @since 1.7
	 *
	 * @param float|int $number The number to get the precision of.
	 *
	 * @return int The precision of the number (number of decimals).
	 */
	private static function get_number_precision( $number = 0 ) {

		// Will leave only the decimals and "0." (eg: 0.2248)
		$decimals = abs( floor( $number ) - $number );

		// Subtract two, since that's the length of the zero and period ("0.")
		$precision = strlen( (string) $decimals ) - 2;

		return $precision;
	}
}

new GravityView_Inline_Edit_Field_Number();
