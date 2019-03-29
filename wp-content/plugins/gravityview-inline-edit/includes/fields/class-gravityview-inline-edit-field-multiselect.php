<?php

/**
 * @file class-gravityview-inline-edit-field-multiselect.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Multiselect extends GravityView_Inline_Edit_Field {

	var $gv_field_name = 'multiselect';

	/** @see GF_Field_MultiSelect $gf_field */
	var $inline_edit_type = 'multiselect';

	var $set_value = true;

	/**
	 * @since 1.0
	 *
	 * @param $wrapper_attributes
	 * @param $field_input_type
	 * @param $field_id
	 * @param $entry
	 * @param $current_form
	 * @param GF_Field_MultiSelect $gf_field
	 *
	 * @return mixed
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {

		$field_value = rgar( $entry, $field_id );

		parent::add_field_template( $this->inline_edit_type, $gf_field->get_field_input( $current_form, $field_value, $entry ), $current_form['id'], $field_id );

		return parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );
	}

}

new GravityView_Inline_Edit_Field_Multiselect;
