<?php

namespace GV;

if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

class Edit_Entry_DIY {
	public function __construct() {
		add_filter( 'gravityview/edit_entry/form_fields', array( $this, 'wrap_custom_content_field_in_container' ), 10, 3 );
	}

	/**
	 * Wraps Custom Content fields in a container by conditionally applying the "gform_field_container" filter to Edit Entry fields.
	 *
	 * @since 2.4
	 *
	 * @param GF_Field[] $gf_fields   Gravity Forms form fields.
	 * @param array|null $edit_fields Edit Entry fields as configured in the View editor.
	 * @param array      $form        Gravity Forms form.
	 *
	 * @return GF_Field[]
	 */
	public function wrap_custom_content_field_in_container( $gf_fields, $edit_fields = null, $form = array() ) {
		if ( ! $edit_fields ) {
			return $gf_fields;
		}

		$edit_field_index = 0;

		foreach ( $edit_fields as $edit_field_id => $edit_field ) {
			// We only want to wrap Custom Content fields.
			if ( 'custom' !== $edit_field['id'] || empty( $edit_field['container'] ) ) {
				$edit_field_index++;

				continue;
			}

			$diy_container = $edit_field['container'];

			add_filter( "gform_field_container_{$form['id']}", function ( $gf_field_container, $gf_field ) use ( $edit_field_index, $edit_field_id, $edit_field, $diy_container ) {
				// Custom Content fields do not have IDs when converted to GF_Field objects, so to identify them we use a "custom_id" property that's applied by GravityView_Field_Custom::show_field_in_edit_entry().
				if ( ! empty( $gf_field->custom_id ) && $gf_field->custom_id === $edit_field_id ) {
					$gf_field_container = sprintf(
						'<%1$s id="gv_diy_%2$s" class="%3$s">{FIELD_CONTENT}</%1$s>',
						$diy_container,
						$edit_field_index,
						! empty( $gf_field['cssClass'] ) ? $gf_field['cssClass'] : ''
					);
				}

				return $gf_field_container;
			}, 10, 2 );

			$edit_field_index++;
		}

		return $gf_fields;
	}
}

new Edit_Entry_DIY();
