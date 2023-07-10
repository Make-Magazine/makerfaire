<?php

/**
 * @file class-gravityview-inline-edit-field-date-created.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Date_Created extends GravityView_Inline_Edit_Field {

	var $gv_field_name = 'date_created';

	var $inline_edit_type = 'date';

	/**
	 * @since 1.0
	 *
	 * @param array $wrapper_attributes
	 * @param string $field_input_type
	 * @param int $field_id
	 * @param array $entry
	 * @param $current_form
	 * @param $gf_field
	 *
	 * @return array
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {

		$wrapper_attributes['data-dateformat'] = ( empty( $gf_field->dateFormat ) ? 'fdy' : $gf_field->dateFormat );

		wp_enqueue_style( 'gv-inline-edit-datepicker' );
		wp_enqueue_script( 'jquery-ui-datepicker' );

		return parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );
	}


}

new GravityView_Inline_Edit_Field_Date_Created;
