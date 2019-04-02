<?php

/**
 * @file class-gravityview-inline-edit-field-name.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Name extends GravityView_Inline_Edit_Field {

	var $gv_field_name = 'name';

	/** @see GF_Field_Name $gf_field */
	var $inline_edit_type = 'name';

	/**
	 * GF_Field_Name is stored in the format {FIELD_ID}.{INPUT NUMBER}. ( i.e. "1.3" ) where each
	 * {INPUT NUMBER} corresponds to a particular input.
	 * 2 => prefix, 3 => first, 4 => middle, 6 => last and 8 => suffix
	 * Save the keys for use throughout the plugin
	 *
	 * @since 1.0
	 *
	 * @var array
	 */
	private static $GFX_NAME_KEYS = array( 2, 3, 4, 6, 8 );

	public $standard_live_update = true;

	/**
	 * @since 1.0
	 *
	 * @param $wrapper_attributes
	 * @param $field_input_type
	 * @param $field_id
	 * @param $entry
	 * @param $current_form
	 * @param GF_Field_Name $gf_field
	 *
	 * @return mixed
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {

		$wrapper_attributes['data-value'] = $this->_get_inline_edit_value( $gf_field, $entry );

		parent::add_field_template( $this->inline_edit_type, $gf_field->get_field_input( $current_form, $wrapper_attributes['data-value'], $entry ), $current_form['id'], $field_id );

		$prefix_input    = GFFormsModel::get_input( $gf_field, $field_id . '.2' );
		$prefix_tabindex = GFCommon::get_tabindex();

		parent::add_field_template( $this->inline_edit_type . 'prefixes', $gf_field->get_name_prefix_field( $prefix_input, $field_id, $field_id, '', '', $prefix_tabindex ) );
		parent::add_field_template( $this->inline_edit_type . 'prefixes', $gf_field->get_name_prefix_field( $prefix_input, $field_id, $field_id, '', '', $prefix_tabindex ), $current_form['id'], $field_id );

		return parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );
	}

	/**
	 * Convert a GF_Field name entry into a format that's easier to use
	 * in X-editable. The final form is a json-encoded array of the form: { prefix: "Sir", first: "Jonathan", middle: "Xavier", last: "Doe", suffix: "II" }
	 *
	 * @since 1.0
	 *
	 * @param GF_Field $gf_field Gravity Forms field object. Is an instance of GF_Field
	 * @param array $entry The entry
	 *
	 * @return string json-encoded array or an empty string if the name isn't set
	 */
	protected function _get_inline_edit_value( $gf_field, $entry ) {
		$inline_editable_name = array();

		foreach ( self::$GFX_NAME_KEYS as $input_number ) {
			$name_value = rgar( $entry, $gf_field->id . '.' . $input_number );

			if ( ! empty( $name_value ) ) {
				$name_value                            = str_replace( '  ', ' ', normalize_whitespace( $name_value ) );
				$inline_editable_name[ $input_number ] = $name_value;
			}

		}

		return empty( $inline_editable_name ) ? '' : json_encode( $inline_editable_name );
	}

}

new GravityView_Inline_Edit_Field_Name;
