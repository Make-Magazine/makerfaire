<?php

/**
 * @file class-gravityview-inline-edit-field-file-upload.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_File_Upload extends GravityView_Inline_Edit_Field {

	var $gv_field_name = 'fileupload';

	var $inline_edit_type = 'file';


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

		if ( $gf_field->multipleFiles === true ) {
			$wrapper_attributes['data-multiple'] = true;
		}

		return parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );

	}


}

new GravityView_Inline_Edit_Field_File_Upload;
