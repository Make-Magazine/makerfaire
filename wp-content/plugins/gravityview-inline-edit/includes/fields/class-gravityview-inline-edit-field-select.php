<?php

/**
 * @file class-gravityview-inline-edit-field-select.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Select extends GravityView_Inline_Edit_Field {

	var $gv_field_name = 'select';

	/** @var GF_Field_Select $gf_field */
	var $inline_edit_type = 'select';

	var $set_value = true;

	/**
	 * @since 1.4.4
	 *
	 * @param $wrapper_attributes
	 * @param $field_input_type
	 * @param $field_id
	 * @param $entry
	 * @param $current_form
	 * @param GF_Field_List      $gf_field
	 *
	 * @return mixed
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {
		$wrapper_attributes['data-source'] = json_encode( $gf_field->choices );

		return parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );
	}

}

new GravityView_Inline_Edit_Field_Select();
