<?php

/**
 * @file class-gravityview-inline-edit-field-radio.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Radio extends GravityView_Inline_Edit_Field {

	var $gv_field_name = 'radio';

	var $inline_edit_type = 'radiolist';

	var $set_value = true;

	/**
	 * Add value and type inline attributes, and enqueue custom field scripts
	 *
	 * @since 1.0
	 *
	 * @param array $wrapper_attributes The attributes of the container <div> or <span>
	 * @param string $field_input_type The field input type
	 * @param int $field_id The field ID
	 * @param array $entry The entry
	 * @param array $current_form The current Form
	 * @param GF_Field $gf_field Gravity Forms field object. Is an instance of GF_Field
	 *
	 * @return array $wrapper_attributes, with `data-type` and `data-value` atts added
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {

		$radio_field_value = rgar( $entry, $field_id );

		parent::add_field_template( $this->inline_edit_type, $gf_field->get_field_input( $current_form, $radio_field_value, $entry ), $current_form['id'], $field_id );

		return parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );
	}
}

new GravityView_Inline_Edit_Field_Radio;
