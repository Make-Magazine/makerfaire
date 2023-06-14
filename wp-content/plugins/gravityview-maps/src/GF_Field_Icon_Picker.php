<?php

namespace GravityKit\GravityMaps;

use GFCommon;
use GF_Field;

class GF_Field_Icon_Picker extends GF_Field {
	public $type = 'gvmaps_icon_picker';

	public function get_form_editor_field_title() {
		return __( 'Map Marker Icon', 'gk-gravitymaps' );
	}

	public function get_form_editor_button() {
		return array(
			'group'       => 'gravityview_fields',
			'text'        => $this->get_form_editor_field_title(),
			'icon'        => $this->get_form_editor_field_icon(),
			'description' => $this->get_form_editor_field_description(),
		);
	}

	function get_form_editor_field_description() {
		return '';
	}

	function get_form_editor_field_icon() {
		return 'dashicons-location-alt';
	}

	function get_form_editor_field_settings() {
		return array(
			'conditional_logic_field_setting',
			'prepopulate_field_setting',
			'error_message_setting',
			'label_setting',
			'label_placement_setting',
			'admin_label_setting',
			//'size_setting',
			//'number_format_setting',
			//'range_setting',
			'rules_setting',
			'visibility_setting',
			//'duplicate_setting',
			//'default_value_setting',
			//'placeholder_setting',
			'description_setting',
			'css_class_setting',
			//'calculation_setting',
		);
	}

	public function is_conditional_logic_supported() {
		return true;
	}

	public function validate( $value, $form ) {
		if ( empty( $value ) ) {
			$value = '';
			if ( $this->isRequired ) {
				$this->failed_validation  = true;
				$this->validation_message = empty( $this->errorMessage ) ? __( 'This field is required.', 'gk-gravitymaps' ) : $this->errorMessage;
			}
		}

		if ( ! empty( $value ) && ! GFCommon::is_valid_url( $value ) ) {
			$this->failed_validation  = true;
			$this->validation_message = empty( $this->errorMessage ) ? __( 'Please select a valid icon.', 'gk-gravitymaps' ) : $this->errorMessage;
		}
		// maybe check if the image is a png and if it is being loaded from the local server
	}

	// Render field
	public function get_field_input( $form, $value = '', $entry = null ) {
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();

		if ( $is_form_editor ) {
			// form editor (admin)
			return $this->render_field_input_on( 'form-editor', $value );
		} elseif ( $is_entry_detail ) {
			// edit entry (admin)
			return $this->render_field_input_on( 'entry-detail', $value );
		}

		// public form
		return $this->render_field_input_on( 'form-public', $value );

	}

	public function render_field_input_on( $part = 'form-editor', $value = null ) {
		ob_start();
		require_once dirname( __FILE__ ) . '/parts/gf-field-icon-picker-' . $part . '.php';

		return ob_get_clean();
	}

	public function get_default_icon() {
		global $gravityview_maps;

		return $gravityview_maps->component_instances['Available_Icons']->get_default_icon_url();
	}

	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
		return GFCommon::is_valid_url( $value ) && $format == 'html' ? '<img src="' . $value . '" height="28">' : $value;
	}

	/**
	 * @inheritDoc
	 */
	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {
		return GFCommon::is_valid_url( $value ) ? '<img src="' . $value . '" height="28">' : esc_html( $value );
	}

	/**
	 * Prepare the value before saving it to the lead.
	 *
	 * @param $value
	 * @param $form
	 * @param $input_name
	 * @param $lead_id
	 * @param $lead
	 *
	 * @return mixed
	 */
	public function get_value_save_entry( $value, $form, $input_name, $lead_id, $lead ) {
		return $value;
	}
}
