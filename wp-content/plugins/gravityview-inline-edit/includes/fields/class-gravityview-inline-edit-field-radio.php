<?php

/**
 * @file  class-gravityview-inline-edit-field-radio.php
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
	 * @param array          $wrapper_attributes The attributes of the container <div> or <span>
	 * @param string         $field_input_type   The field input type
	 * @param int            $field_id           The field ID
	 * @param array          $entry              The entry
	 * @param array          $current_form       The current Form
	 * @param GF_Field_Radio $gf_field           Gravity Forms field object
	 *
	 * @return array $wrapper_attributes with additional `data-` attributes
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {
		$radio_field_value = rgar( $entry, $field_id );

		if ( $gf_field->enableOtherChoice ) {
			$is_other_choice = true;

			foreach ( $gf_field->choices as $choice ) {
				if ( $radio_field_value === $choice['value'] ) {
					$is_other_choice = false;
				}
			}

			// GF's template for radio fields has 2 elements: (1) Radio input ("gf_other_choice" value) and (2) Text input ("Other" or custom value)
			// Inline edit only sees Radio inputs, so to automatically select "Other", we need to set its value to "gf_other_choice" and then in UI set the Text input's value from the "data-other-choice" attribute
			if ( $is_other_choice ) {
				$wrapper_attributes['data-other-choice'] = $radio_field_value;
				$entry[ $field_id ]                      = 'gf_other_choice';
			}

			// Set the "Other" choice value to be default and then swap it with the actual entry's value in the UI
			$radio_field_value = GFCommon::get_other_choice_value( $gf_field );
		}

		$wrapper_attributes['data-source'] = json_encode( $gf_field->choices );

		parent::add_field_template( $this->inline_edit_type, $gf_field->get_field_input( $current_form, $radio_field_value, $entry ), $current_form['id'], $field_id );

		return parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );
	}
}

new GravityView_Inline_Edit_Field_Radio();
