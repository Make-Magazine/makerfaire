<?php

/**
 * @file class-gravityview-inline-edit-field-checkbox.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Checkbox extends GravityView_Inline_Edit_Field {

	var $gv_field_name = 'checkbox';

	var $inline_edit_type = 'checklist';

	var $standard_live_update = true;

	var $live_update_json_encode = false;

	/**
	 * @since 1.0
	 *
	 * @param $wrapper_attributes
	 * @param $field_input_type
	 * @param $field_id
	 * @param $entry
	 * @param $current_form
	 * @param GF_Field_Checkbox $gf_field
	 *
	 * @return mixed
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {

		$checklist_value = self::_get_inline_edit_value( $gf_field, $entry, false );

		parent::add_field_template( $this->inline_edit_type, $gf_field->get_field_input( $current_form, $checklist_value, $entry ), $current_form['id'], $field_id );

		if ( ! empty( $checklist_value ) ) {
			$wrapper_attributes['data-value'] = json_encode( $checklist_value );
		}

		return parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );
	}

	/**
	 * Return the display value for live update after save.
	 *
	 * Passes use_text=true to get_value_export() so that choice labels are returned
	 * instead of raw values. This matters for dynamically populated fields (e.g., via
	 * GP Populate Anything) where the stored value (a post ID) differs from the label.
	 *
	 * @since 2.9.2
	 *
	 * @param GF_Field_Checkbox $gf_field Field data.
	 * @param array             $entry    Entry data.
	 *
	 * @return array
	 */
	protected function get_inline_edit_extra_data( $gf_field, $entry ) {
		return array( 'display_value' => $gf_field->get_value_export( $entry, '', true ) );
	}

	/**
	 * Get the value used in Inline Edit `data-value` attribute
	 *
	 * @param GF_Field_Checkbox $gf_field
	 * @param array $entry Entry object
	 * @param bool $as_json Whether to return as JSON-encoded string or raw array
	 *
	 * @return array|string JSON-encoded array, or array (depending on $as_string)
	 */
	public static function _get_inline_edit_value( $gf_field, $entry, $as_json = true ) {

		$field_id = $gf_field->id;

		/** @var GF_Field_Checkbox $gf_field */
		$checklist_value = array();
		$choice_number   = 1;
		foreach ( $gf_field->choices as $choice ) {
			if ( $choice_number % 10 == 0 ) { //hack to skip numbers ending in 0. so that 5.1 doesn't conflict with 5.10
				$choice_number ++;
			}
			$input_id                = $field_id . '.' . $choice_number;
			
			$current_checklist_entry = rgar( $entry, $input_id, false );

			if ( $current_checklist_entry ) {
				$checklist_value[] = $current_checklist_entry;
			}
			$choice_number ++;
		}

		return $as_json ? json_encode( $checklist_value ) : $checklist_value;
	}

}

new GravityView_Inline_Edit_Field_Checkbox;
