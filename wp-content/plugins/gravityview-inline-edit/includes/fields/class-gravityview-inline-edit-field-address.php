<?php

/**
 * @file class-gravityview-inline-edit-field-address.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Address extends GravityView_Inline_Edit_Field {

	var $gv_field_name = 'address';

	/** @see GF_Field_Address $gf_field */
	var $inline_edit_type = 'address';

	var $standard_live_update = true;

	/**
	 * @since 1.0
	 *
	 * @param $wrapper_attributes
	 * @param $field_input_type
	 * @param $field_id
	 * @param $entry
	 * @param $current_form
	 * @param GF_Field_Address $gf_field
	 *
	 * @return mixed
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {

		$wrapper_attributes['data-value']   = $this->_get_inline_edit_value( $gf_field, $entry, $field_id );
		$wrapper_attributes['data-hidden']  = $this->get_hidden_address_inputs( $gf_field );
		$wrapper_attributes['data-tplmode'] = $gf_field->addressType;

		parent::add_field_template( $this->inline_edit_type, $gf_field->get_field_input( $current_form, '', $entry ), $current_form['id'], $field_id  );

		$address_types = $gf_field->get_address_types( $current_form['id'] );

		foreach ( $address_types as $addr_type => $address_details ) {

			if ( 'international' == $addr_type ) {
				$addr_list = $gf_field->get_country_dropdown();
			} else if ( 'canadian' == $addr_type ) {
				$addr_list = $gf_field->get_canadian_provinces_dropdown();
			} else if ( 'us' == $addr_type ) {
				$addr_list = $gf_field->get_us_state_dropdown();
			} else {
				$addr_list = $this->_get_new_country_dropdown( $address_details['states'] );
			}

			parent::add_field_template( $this->inline_edit_type . $addr_type, $addr_list, $current_form['id'], $field_id );
		}

		return parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );
	}

	/**
	 * Convert a GF_Field address entry into a format that's easier to use
	 * in X-editable. The final form is a json-encoded array of the form: { street: "The Street", street_line2: "The second line" }
	 *
	 * @since 1.0
	 *
	 * @param GF_Field $gf_field Gravity Forms field object. Is an instance of GF_Field
	 * @param array $entry The entry
	 * @param int $field_id The field ID
	 *
	 * @return array|string json-encoded array or an empty string if the address isn't set
	 */
	public function _get_inline_edit_value( $gf_field, $entry, $field_id = 0 ) {

		$inline_editable_address = array();

		for ( $input_number = 1; $input_number < 7; $input_number ++ ) {
			$address_value = rgar( $entry, $gf_field->id . '.' . $input_number );

			if ( ! empty( $address_value ) ) {
				$inline_editable_address[ $input_number ] = str_replace( '  ', ' ', trim( $address_value ) );
			}

		}

		return empty( $inline_editable_address ) ? '' : json_encode( $inline_editable_address );
	}

	/**
	 * Get which inputs in the address field are hidden_fields
	 *
	 * @since 1.0
	 *
	 * @param GF_Field $gf_field Gravity Forms field object. Is an instance of GF_Field
	 *
	 * @return string Empty string if there are no hidden inputs; JSON-encoded array of inputs if there are
	 */
	public function get_hidden_address_inputs( $gf_field ) {
		$hidden_inputs = array();
		foreach ( $gf_field->inputs as $input ) {
			if ( ! empty( $input['isHidden'] ) ) {
				$hidden_inputs[] = substr( $input['id'], - 1 );
			}
		}

		return empty( $hidden_inputs ) ? '' : json_encode( $hidden_inputs );
	}

	/**
	 * Create an options list for countries provided for the address field
	 * via the `gform_address_types` filter
	 *
	 * @since 1.0
	 *
	 * @param array $states Dropdown options
	 *
	 * @return string HTML list of <option>s for the country <select>
	 */
	private function _get_new_country_dropdown( $states ) {
		$country_options = '';
		foreach ( $states as $state ) {
			$country_options .= "<option value='{$state}'>{$state}</option>";
		}

		return $country_options;
	}

}

new GravityView_Inline_Edit_Field_Address;
