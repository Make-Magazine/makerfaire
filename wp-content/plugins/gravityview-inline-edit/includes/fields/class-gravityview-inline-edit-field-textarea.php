<?php

/**
 * @file class-gravityview-inline-edit-field-textarea.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Textarea extends GravityView_Inline_Edit_Field {

	var $gv_field_name = 'textarea';

	var $inline_edit_type = 'textarea';

	var $set_value = true;

	/**
	 * @since 1.0
	 * 
	 * @param array $wrapper_attributes
	 * @param string $field_input_type
	 * @param int $field_id
	 * @param array $entry
	 * @param $current_form
	 * @param GF_Field_Textarea $gf_field
	 *
	 * @return array
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {

		$wrapper_attributes['data-maxlength'] = $gf_field->maxLength;

		return parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );
	}

}

new GravityView_Inline_Edit_Field_Textarea;
