<?php

/**
 * @file class-gravityview-inline-edit-field-list.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_List extends GravityView_Inline_Edit_Field {

	var $gv_field_name = 'list';

	var $inline_edit_type = 'gvlist';

	/**
	 * @since 1.0
	 *
	 * @param $wrapper_attributes
	 * @param $field_input_type
	 * @param $field_id
	 * @param $entry
	 * @param $current_form
	 * @param GF_Field_List $gf_field
	 *
	 * @return mixed
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {

		$list_source_raw = $this->_get_inline_edit_value( $gf_field, $entry, $field_id );
		$mode            = '';

		//Multicolumn support. Don't combine the next check, isset( $list_source_raw[0] ) && is_array( $list_source_raw[0]  ),
		// with this because there are cases where multicolumn is enabled but we can't retrieve data-colcount
		if ( $gf_field->enableColumns ) {
			$mode                                = 'multi';
			$wrapper_attributes['data-colcount'] = count( $gf_field->choices );
		}

		$wrapper_attributes['data-tplmode'] = $mode;
		$wrapper_attributes['data-source']  = json_encode( $list_source_raw );

		parent::add_field_template( $this->inline_edit_type . $mode, $gf_field->get_field_input( $current_form, '', $entry ) );

		return parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );
	}

	/**
	 * Get the list array for use as a src attribute
	 *
	 * @since 1.0
	 *
	 * @param GF_Field $gf_field Gravity Forms field object. Is an instance of GF_Field
	 * @param array $entry The entry
	 * @param int $field_id The field ID
	 *
	 * @return array|string array or an empty string if the list isn't set
	 */
	public function _get_inline_edit_value( $gf_field, $entry, $field_id ) {

		$input_id = 0;

		if ( ! ctype_digit( $field_id ) ) {
			$field_id_array = explode( '.', $field_id );
			$input_id       = rgar( $field_id_array, 0 );
		}
		$value = rgar( $entry, $input_id );

		return empty( $value ) ? '' : maybe_unserialize( $value );
	}

	/**
	 * Sanitize input list array
	 *
	 * @param  array $list_array Submitted list array
	 *
	 * @return array Array with sanitized data
	 */
	public function sanitize_gv_list( $list_array ) {
		$value           = array();
		$is_multi_column = ( isset( $list_array[0] ) && is_array( $list_array[0] ) ? true : false );

		foreach ( $list_array as $list_key => $list_item ) {
			if ( ! $is_multi_column && empty( $list_item ) ) {//Don't leave out blank entries in multi-column layouts
				continue;
			}
			if ( is_array( $list_item ) ) {
				foreach ( $list_item as $column_name => $list_value ) {
					$value[ $list_key ][ $column_name ] = wp_filter_post_kses( $list_value );
				}
			} else {
				$value[ $list_key ] = wp_filter_post_kses( $list_item );
			}
		}

		return $value;
	}

}

new GravityView_Inline_Edit_Field_List;
