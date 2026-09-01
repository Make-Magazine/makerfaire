<?php

/**
 * @file  class-gravityview-inline-edit-field-image-choice.php
 *
 * @since 2.3.0
 */
class GravityView_Inline_Edit_Field_Image_Choice extends GravityView_Inline_Edit_Field {
	public $gv_field_name = 'image_choice';

	public $inline_edit_type = 'image_choice';

	public $set_value = false;

	/**
	 * Add value and type inline attributes, and enqueue custom field scripts
	 *
	 * @since 2.3.0
	 *
	 * @param array                 $wrapper_attributes The attributes of the container <div> or <span>
	 * @param string                $field_input_type   The field input type
	 * @param int                   $field_id           The field ID
	 * @param array                 $entry              The entry
	 * @param array                 $current_form       The current Form
	 * @param GF_Field_Image_Choice $gf_field           Gravity Forms field object
	 *
	 * @return array $wrapper_attributes with additional `data-` attributes
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {
		if ( $gf_field instanceof GF_Field_Radio ) {
			$this->set_value    = true;

			$image_choice_value = rgar( $entry, $field_id );
		} else {
			$this->set_value    = false;

			$image_choice_value = self::_get_inline_edit_value( $gf_field, $entry, false );
		}

		$wrapper_attributes['data-value'] = wp_json_encode( $image_choice_value );

		// Add image HTML to choices.
		$decorator = new ChoiceDecorator( $gf_field );

		foreach ( $gf_field->choices as $choice_number => $choice ) {
			$gf_field->choices[ $choice_number ]['image_html'] = apply_filters(
				'gravityview-inline-edit/fields/image_choice/image_markup',
				$decorator->get_image_markup( $choice, $field_id, $choice_number, $current_form ),
				$choice,
				$current_form,
				$gf_field,
			);
		}

		$wrapper_attributes['data-source'] = wp_json_encode( $gf_field->choices );

		parent::add_field_template( $this->inline_edit_type, $gf_field->get_field_input( $current_form, $image_choice_value, $entry ), $current_form['id'], $field_id );

		return parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );
	}

	/**
	 * Get the value used in Inline Edit `data-value` attribute
	 *
	 * @param GF_Field_Checkbox $gf_field
	 * @param array             $entry   Entry object
	 * @param bool              $as_json Whether to return as JSON-encoded string or raw array
	 *
	 * @return array|string JSON-encoded array, or array (depending on $as_json)
	 */
	public static function _get_inline_edit_value( $gf_field, $entry, $as_json = true ) {
		$field_id = $gf_field->id;

		/** @var GF_Field_Checkbox $gf_field */
		$checklist_value = [];
		$choice_number   = 1;

		for ( $i = 0; $i < count( $gf_field->choices ); $i++ ) {
			if ( 0 === $choice_number % 10 ) { // Skip numbers ending in 0 so that 5.1 doesn't conflict with 5.10
				$choice_number++;
			}
			$input_id = $field_id . '.' . $choice_number;

			$current_checklist_entry = rgar( $entry, $input_id, false );

			if ( $current_checklist_entry ) {
				$checklist_value[] = $current_checklist_entry;
			}

			$choice_number++;
		}

		if ( empty( $checklist_value ) ) {
			return '';
		}

		return $as_json ? wp_json_encode( $checklist_value ) : $checklist_value;
	}
}

new GravityView_Inline_Edit_Field_Image_Choice();
