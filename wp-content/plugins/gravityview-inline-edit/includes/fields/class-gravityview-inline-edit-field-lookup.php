<?php

/**
 * @file  class-gravityview-inline-edit-field-lookup.php
 *
 * @since 2.4.0
 */
class GravityView_Inline_Edit_Field_Lookup extends GravityView_Inline_Edit_Field {
	var $gv_field_name = 'lookup';

	/** @var GF_Field_Select $gf_field */
	var $inline_edit_type = 'select';

	var $set_value = true;

	/**
	 * @since 2.4.0
	 *
	 * @param array         $wrapper_attributes
	 * @param string        $field_input_type
	 * @param string        $field_id
	 * @param array         $entry
	 * @param array         $current_form
	 * @param GF_Field_List $gf_field
	 *
	 * @return array
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {
		$gf_field->storageFormat = 'id';
		$lookup_field_value      = rgar( $entry, $field_id );
		$this->set_value         = true;

		if ( 'dropdown' === $gf_field->lookupInputType ) {
			$this->inline_edit_type = 'select';

		} elseif ( 'radio' === $gf_field->lookupInputType ) {
			$this->inline_edit_type = 'radiolist';

			parent::add_field_template( $this->inline_edit_type, $gf_field->get_field_input( $current_form, $lookup_field_value, $entry ), $current_form['id'], $field_id );
		} else {
			$lookup_field_value     = self::_get_inline_edit_value( $gf_field, $entry, false );
			$this->inline_edit_type = 'checklist';
			$this->set_value        = false;

			if ( ! empty( $lookup_field_value ) ) {
				$wrapper_attributes['data-value'] = json_encode( $lookup_field_value );
			}

			parent::add_field_template( $this->inline_edit_type, $gf_field->get_field_input( $current_form, $lookup_field_value, $entry ), $current_form['id'], $field_id );
		}

		$wrapper_attributes['data-source'] = json_encode( $gf_field->get_lookup_choices() );

		$wrapper_attributes['class'] = $wrapper_attributes['class'] . ' gv-inline-edit-lookup';

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
		$checklist_value = array();
		$choice_number   = 1;

		foreach ( $gf_field->get_lookup_choices() as $choice ) {
			if ( 0 == $choice_number % 10 ) { // hack to skip numbers ending in 0. so that 5.1 doesn't conflict with 5.10
				++$choice_number;
			}
			$input_id = $field_id . '.' . $choice_number;

			$current_checklist_entry = rgar( $entry, $input_id, false );

			if ( $current_checklist_entry ) {
				$checklist_value[] = $current_checklist_entry;
			}
			++$choice_number;
		}

		return $as_json ? json_encode( $checklist_value ) : $checklist_value;
	}
}

new GravityView_Inline_Edit_Field_Lookup();
