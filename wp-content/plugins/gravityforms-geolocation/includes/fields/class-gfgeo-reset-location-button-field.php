<?php
/**
 * Gravity Forms Geolocation Reset Location button field.
 *
 * @package gravityforms-geolocation.
 */

if ( ! class_exists( 'GFForms' ) ) {
	die(); // abort if accessed directly.
}

/**
 * Register Reset Location button
 *
 * @since  3.0
 */
class GFGEO_Reset_Location_Button_Field extends GF_Field {

	/**
	 * Field type
	 *
	 * @var string
	 */
	public $type = 'gfgeo_reset_location_button';

	/**
	 * Returns the field's form editor icon.
	 *
	 * This could be an icon url or a gform-icon class.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_form_editor_field_icon() {
		return 'gform-icon--place';
	}

	/**
	 * Field button
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'gfgeo_geolocation_fields',
			'text'  => __( 'Reset Location', 'gfgeo' ),
		);
	}

	/**
	 * Field title
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_field_title() {
		return __( 'Reset Location', 'gfgeo' );
	}

	/**
	 * Field settings.
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_field_settings() {

		// Gravity Forms v2.5+.
		if ( GFGEO_GF_2_5 ) {

			return array(
				// ggf options.
				'gfgeo-geocoder-id-multiple',
				'gfgeo-reset-location-button-field-settings',
				// gform options.
				'conditional_logic_field_setting',
				'label_setting',
				'label_placement_setting',
				'admin_label_setting',
				'size_setting',
				'visibility_setting',
				'description_setting',
				'css_class_setting',
			);
		}

		return array(
			// ggf options.
			'gfgeo-geocoder-id-multiple',
			'gfgeo-reset-location-button-label',
			// gform options.
			'conditional_logic_field_setting',
			'label_setting',
			'description_setting',
			'css_class_setting',
			'visibility_setting',
		);
	}

	/**
	 * Conditional logic
	 *
	 * @return boolean [description]
	 */
	public function is_conditional_logic_supported() {
		return true;
	}

	/**
	 * Generate field input
	 *
	 * @param  [type] $form  [description].
	 * @param  string $value [description].
	 * @param  [type] $entry [description].
	 *
	 * @return [type]        [description]
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {

		$form_id      = absint( $form['id'] );
		$field_id     = ! empty( $this->gfgeo_id ) ? esc_attr( $this->gfgeo_id ) : $form_id . '_' . (int) $this->id;
		$button_label = ! empty( $this->gfgeo_reset_location_button_label ) ? $this->gfgeo_reset_location_button_label : '';
		$button_label = apply_filters( 'gfgeo_reset_location_button_label', $button_label, $form_id, $this );
		$size         = $this->size;
		$geocoders_id = '';

		// Set geocoder/s ID.
		if ( ! empty( $this->gfgeo_geocoder_id ) ) {

			if ( is_array( $this->gfgeo_geocoder_id ) ) {

				$geocoders_id = implode( ',', $this->gfgeo_geocoder_id );

			} else {
				$geocoders_id = esc_attr( $this->gfgeo_geocoder_id );
			}
		}

		// generate the button element.
		$input = "<input type='button' id='gfgeo-reset-location-button-{$field_id}' data-geocoders_id='{$geocoders_id}' data-field_id='{$field_id}' class='gfgeo-reset-location-button gfgeo-form-button {$size}' value='{$button_label}' />";

		return sprintf( "<div id='gfgeo-reset-location-button-wrapper-{$field_id}' class='ginput_container ginput_container_gfgeo_reset_location_button gfgeo-reset-location-button-wrapper'>%s</div>", $input );
	}

	/**
	 * Display message in entry list page.
	 *
	 * @param  [type] $value         [description].
	 * @param  [type] $entry         [description].
	 * @param  [type] $field_id      [description].
	 * @param  [type] $columns       [description].
	 * @param  [type] $form          [description].
	 *
	 * @return [type]                [description]
	 */
	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {
		return __( 'Data is not available for this field', 'gfgeo' );
	}
}
GF_Fields::register( new GFGEO_Reset_Location_Button_Field() );
