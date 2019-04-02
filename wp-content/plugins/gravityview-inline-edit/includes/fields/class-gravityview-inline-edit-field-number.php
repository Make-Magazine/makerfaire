<?php

/**
 * @file class-gravityview-inline-edit-field-number.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Number extends GravityView_Inline_Edit_Field {

	var $gv_field_name = 'number';

	var $inline_edit_type = 'number';

	var $set_value = true;

	/**
	 * Update calculation fields and add live-update response
	 *
	 * @since 1.0
	 *
	 * @param bool|WP_Error $update_result
	 * @param array $entry The Entry Object that's been updated
	 * @param int $form_id The Form ID
	 * @param GF_Field_Number $gf_field GF_Field The field that's been updated
	 *
	 * @return bool|WP_Error|array Returns original result, if not a number field. Otherwise, returns a response array. Empty if no calculation fields, otherwise multi-dimensional array with `data` and `selector` keys
	 */
	public function updated_result( $update_result, $entry = array(), $form_id = 0, GF_Field $gf_field ) {
		global $wpdb;

		$return = $update_result;

		if ( version_compare( GFFormsModel::get_database_version(), '2.3-dev-1', '>=' ) ) {
			$entry_meta_table  = GFFormsModel::get_entry_meta_table_name();
			$current_fields    = $wpdb->get_results( $wpdb->prepare( "SELECT id, meta_key FROM $entry_meta_table WHERE entry_id=%d", $entry['id'] ) );
		} else {
			$lead_detail_table = GFFormsModel::get_lead_details_table_name();
			$current_fields    = $wpdb->get_results( $wpdb->prepare( "SELECT id, field_number FROM $lead_detail_table WHERE lead_id=%d", $entry['id'] ) );
		}

		$form              = GFAPI::get_form( $form_id );
		$response          = array();

		/** @var GF_Field $field */
		foreach ( $form['fields'] as $field ) {

			if ( $field->has_calculation() ) {

				GFFormsModel::save_input( $form, $field, $entry, $current_fields, $field->id );

				// Refresh the entry after saving the input value
				$entry      = GFAPI::get_entry( $entry['id'] );

				$response[] = array(
					'value'    => GFCommon::get_calculation_value( $field->id, $form, $entry ),
					'selector' => ".gv-inline-edit-live-{$entry['id']}-{$entry['form_id']}-{$field->id}",
				);

				$return = $response;
			}
		}

		return $return;
	}
}

new GravityView_Inline_Edit_Field_Number;
