<?php

/**
 * @file class-gravityview-inline-edit-field-select.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Created_By extends GravityView_Inline_Edit_Field {

	var $gv_field_name = 'created_by';

	var $inline_edit_type = 'select2';

	var $set_value = true;


	/**
	 * @since 1.0
	 *
	 * @param array $wrapper_attributes
	 * @param string $field_input_type
	 * @param int $field_id
	 * @param array $entry
	 * @param $current_form
	 * @param GF_Field_Date $gf_field
	 *
	 * @return array
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {

		$wrapper_attributes['class'] = $wrapper_attributes['class']. ' gv-inline-edit-user-select2';

		wp_enqueue_style( 'gv-inline-edit-select2' );
		wp_enqueue_script( 'gv-inline-edit-select2' );

		return parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );
	}


}

new GravityView_Inline_Edit_Field_Created_By;
