<?php

/**
 * @file class-gravityview-inline-edit-field-multi-choice.php
 *
 * @since 2.6.0
 */
class GravityView_Inline_Edit_Field_Multi_Choice extends GravityView_Inline_Edit_Field {
	var $gv_field_name = 'multi_choice';

	var $inline_edit_type = 'radiolist'; // Default to radio, but will be dynamic.

	var $set_value = true;

	/**
	 * Adds value and type inline attributes, and enqueue custom field scripts.
	 *
	 * @since 2.6.0
	 *
	 * @param array                    $wrapper_attributes The attributes of the container <div> or <span>.
	 * @param string                   $field_input_type   The field input type.
	 * @param int                      $field_id           The field ID.
	 * @param array                    $entry              The entry.
	 * @param array                    $current_form       The current form.
	 * @param GF_Field_Multiple_Choice $gf_field           Gravity Forms field object.
	 *
	 * @return array $wrapper_attributes with additional `data-` attributes.
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {
		$input_type = $gf_field->get_input_type();

		if ( 'radio' === $input_type ) {
			$this->inline_edit_type = 'radiolist';

			return ( new GravityView_Inline_Edit_Field_Radio() )->modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );
		} else {
			$this->inline_edit_type = 'checklist';
			$this->set_value        = false;

			return ( new GravityView_Inline_Edit_Field_Checkbox() )->modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );
		}
	}
}

new GravityView_Inline_Edit_Field_Multi_Choice();
