<?php

/**
 * @file  class-gravityview-inline-edit-field-price.php
 *
 * @since 1.4
 */
class GravityView_Inline_Edit_Field_Product extends GravityView_Inline_Edit_Field {

	var $gv_field_name = 'product';

	var $inline_edit_type = 'product';

	/**
	 * @since 1.4
	 *
	 * @param array         $wrapper_attributes
	 * @param string        $field_input_type
	 * @param int           $field_id
	 * @param array         $entry
	 * @param               $current_form
	 * @param GF_Field_Time $gf_field
	 *
	 * @return array
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {
		if ( 'price' !== $gf_field->inputType ) {
			unset( $wrapper_attributes['class'] );

			return $wrapper_attributes;
		}

		$currency = new RGCurrency( $entry['currency'] );

		$wrapper_attributes['data-value'] = $currency->to_number( rgar( $entry, $field_id ) );

		return parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );
	}

	/**
	 * @since 1.4
	 *
	 * @param bool|WP_Error $update_result
	 * @param array $entry The Entry Object that's been updated
	 * @param int $form_id The Form ID
	 * @param GF_Field_Number $gf_field GF_Field The field that's been updated
	 *
	 * @return bool|WP_Error|array Returns original result, if not a number field. Otherwise, returns a response array. Empty if no calculation fields, otherwise multi-dimensional array with `data` and `selector` keys
	 */
	public function updated_result( $update_result, $entry = array(), $form_id = 0, GF_Field $gf_field = null ) {
		$display_value = \GFCommon::get_lead_field_display( $gf_field, $entry[$gf_field->id], $entry['currency'], false, 'html' );

		if ( ! is_bool( $update_result ) ) {
			return $update_result;
		}

		return array(
			array(
				'value'    => $entry[ $gf_field->id ],
				'selector' => ".gv-inline-editable-field-{$entry['id']}-{$entry['form_id']}-{$gf_field->id}",
				'data'     => array( 'display_value' => $display_value ),
			)
		);
	}
}

new GravityView_Inline_Edit_Field_Product;
