<?php

/**
 * @file class-gravityview-inline-edit-field-time.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Time extends GravityView_Inline_Edit_Field {

	var $gv_field_name = 'time';

	var $inline_edit_type = 'gvtime';


	/**
	 * @since 1.0
	 *
	 * @param array $wrapper_attributes
	 * @param string $field_input_type
	 * @param int $field_id
	 * @param array $entry
	 * @param $current_form
	 * @param GF_Field_Time $gf_field
	 *
	 * @return array
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {

		$wrapper_attributes['data-value'] = $this->_get_inline_edit_value( rgar( $entry, $field_id ) );

		parent::add_field_template( $this->inline_edit_type, $gf_field->get_field_input( $current_form, '', $entry ) );

		return parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );
	}

	/**
	 * Special input structure: 0 => hour, 1 => minute, 2: am/pm
	 *
	 * @since 1.0
	 *
	 * @param bool|WP_Error $update_result
	 * @param array $entry
	 * @param int $form_id
	 * @param GF_Field $gf_field
	 *
	 * @return array|bool|WP_Error
	 */
	public function updated_result( $update_result, $entry = array(), $form_id = 0, GF_Field $gf_field ) {

		$gvtime = $this->_get_inline_edit_value( rgar( $entry, $gf_field->id ), false );

		$return = array(
			array(
				'selector' => "#gv-inline-editable-{$entry['id']}-{$entry['form_id']}-{$gf_field->id}",
				'value'    => json_encode( $gvtime ),
			),
		);

		foreach ( $gvtime as $key => $value ) {
			$return[] = array(
				'selector' => "#gv-inline-editable-{$entry['id']}-{$entry['form_id']}-{$gf_field->id}-{$key}",
				'value'    => json_encode( array( $key => $value ) ),
			);
		}

		return $return;
	}

	/**
	 * Convert a GF_Field time entry, which is either of the form `HH:MM AM` or an array into a format
	 * that's easier for X-editable manipulation. The final form is a json-encoded array
	 * of the form { hh: 11, mm: 35 period: pm }
	 *
	 * @since 1.0
	 *
	 * @param  string $gftime Time of the form `11:35 PM`
	 * @param  bool $json_encode Whether to JSON-encode the output
	 *
	 * @return string|array If $json_encode, json-encoded array of the form { 0: 11, 1: 35, 2: pm }. If $json_encode is false, unencoded array. If $gftime isn't set, empty string.
	 */
	private function _get_inline_edit_value( $gftime, $json_encode = true ) {

		if ( empty( $gftime ) ) {
			return '';
		}

		$value = array();

		if ( ! is_array( $gftime ) ) {
			preg_match( '/^(\d*):(\d*) ?(.*)$/', $gftime, $matches );
			$value[1] = isset( $matches[1] ) ? $matches[1] : '';
			$value[2] = isset( $matches[2] ) ? $matches[2] : '';
			$value[3] = isset( $matches[3] ) ? strtolower( $matches[3] ) : '';
		} else {
			$value[1] = rgar( $gftime, 0 );
			$value[2] = rgar( $gftime, 1 );
			$value[3] = strtolower( rgar( $gftime, 2, '' ) );
		}

		return $json_encode ? ( empty( $value ) ? '' : json_encode( $value ) ) : $value;
	}

}

new GravityView_Inline_Edit_Field_Time;
