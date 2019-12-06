<?php
/**
 * Gravity Forms Geolocation Directions Panel field.
 *
 * @package gravityforms-geolocation.
 */

if ( ! class_exists( 'GFForms' ) ) {
	die(); // abort if accessed directly.
}

/**
 * Register Directions Panel Field
 *
 * @since  2.0
 */
class GFGEO_Directions_Panel_Field extends GF_Field {

	/**
	 * Field type
	 *
	 * @var string
	 */
	public $type = 'gfgeo_directions_panel';

	/**
	 * Field button.
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'gfgeo_geolocation_fields',
			'text'  => __( 'Directions Panel', 'gfgeo' ),
		);
	}

	/**
	 * Field label.
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_field_title() {
		return __( 'Directions Panel', 'gfgeo' );
	}

	/**
	 * Field settings.
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_field_settings() {
		return array(
			'conditional_logic_field_setting',
			'label_setting',
			'label_placement_setting',
			'admin_label_setting',
			'visibility_setting',
			'duplicate_setting',
			'description_setting',
			'css_class_setting',
		);
	}

	/**
	 * Conditional logic.
	 *
	 * @return boolean [description]
	 */
	public function is_conditional_logic_supported() {
		return false;
	}

	/**
	 * Allow HTML.
	 *
	 * @return [type] [description]
	 */
	public function allow_html() {
		return true;
	}

	/**
	 * Field input
	 *
	 * @param  [type] $form  [description].
	 * @param  string $value [description].
	 * @param  [type] $entry [description].
	 *
	 * @return [type]        [description]
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {

		// Form Editor.
		if ( $this->is_form_editor() ) {

			$content  = '<div class="gfgeo-hidden-container" style="border: 1px solid #E4E4E4;padding: 20px;background-color: #F6F6F6">';
			$content .= __( 'Note: This field will be hidden when the form first load in the front-end and will be dynamically generated with the directions data when available.', 'gfgeo' );
			$content .= '</span></div>';

			return $content;

			// Front-end form.
		} else {

			$field_id = esc_attr( $form['id'] . '_' . $this->id );

			return '<div id="gfgeo-directions-panel-holder-' . $field_id . '" class="gfgeo-directions-panel-holder"></div>';
		}
	}

	/**
	 * Generate field data for email template tags.
	 *
	 * @param  [type] $value      [description].
	 * @param  [type] $input_id   [description].
	 * @param  [type] $entry      [description].
	 * @param  [type] $form       [description].
	 * @param  [type] $modifier   [description].
	 * @param  [type] $raw_value  [description].
	 * @param  [type] $url_encode [description].
	 * @param  [type] $esc_html   [description].
	 * @param  [type] $format     [description].
	 * @param  [type] $nl2br      [description].
	 *
	 * @return [type]             [description]
	 */
	public function get_value_merge_tag( $value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br ) {
		return $this->get_directions_link( $raw_value );
	}

	/**
	 * Modify value for CSV export.
	 *
	 * @param  [type]  $entry    [description].
	 * @param  string  $input_id [description].
	 * @param  boolean $use_text [description].
	 * @param  boolean $is_csv   [description].
	 *
	 * @return [type]            [description]
	 */
	public function get_value_export( $entry, $input_id = '', $use_text = false, $is_csv = false ) {
		return $value;
	}

	/**
	 * Save the directions link in the entry.
	 *
	 * @param  [type] $value      [description].
	 * @param  [type] $form       [description].
	 * @param  [type] $input_name [description].
	 * @param  [type] $entry_id   [description].
	 * @param  [type] $entry      [description].
	 *
	 * @return [type]             [description]
	 */
	public function get_value_save_entry( $value, $form, $input_name, $entry_id, $entry ) {

		$directions = array(
			'status'      => 0,
			'origin'      => array(
				'latitude'  => '',
				'longitude' => '',
			),
			'destination' => array(
				'latitude'  => '',
				'longitude' => '',
			),
		);

		foreach ( $form['fields'] as $field ) {

			if ( 'gfgeo_geocoder' === $field->type ) {

				if ( ! empty( $field->gfgeo_distance_directions_panel_id ) && absint( $field->gfgeo_distance_directions_panel_id ) === absint( $this->id ) && ! empty( $field->gfgeo_distance_destination_geocoder_id ) ) {

					$origin_geocoder      = ! empty( $_POST[ 'input_' . $field->id ] ) ? maybe_unserialize( $_POST[ 'input_' . $field->id ] ) : array(); // WPCS: CSRF ok, sanitization ok.
					$destination_geocoder = ! empty( $_POST[ 'input_' . $field->gfgeo_distance_destination_geocoder_id ] ) ? maybe_unserialize( $_POST[ 'input_' . $field->gfgeo_distance_destination_geocoder_id ] ) : array(); // WPCS: CSRF ok, sanitization ok.

					if ( ! empty( $origin_geocoder['latitude'] ) && ! empty( $origin_geocoder['longitude'] ) && ! empty( $destination_geocoder['latitude'] ) && ! empty( $destination_geocoder['longitude'] ) ) {

						$directions['status']                   = 1;
						$directions['origin']['latitude']       = $origin_geocoder['latitude'];
						$directions['origin']['longitude']      = $origin_geocoder['longitude'];
						$directions['destination']['latitude']  = $destination_geocoder['latitude'];
						$directions['destination']['longitude'] = $destination_geocoder['longitude'];

						return maybe_serialize( $directions );
					}
				}
			}
		}

		return maybe_serialize( $directions );
	}

	/**
	 * Display directions link in entry list page.
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
		return $this->get_directions_link( $value );
	}

	/**
	 * Display field data in entry page.
	 *
	 * This will output a directions link in the entry page.
	 *
	 * @param  [type]  $value         [description].
	 * @param  string  $currency      [description].
	 * @param  boolean $use_text      [description].
	 * @param  string  $format        [description].
	 * @param  string  $media         [description].
	 *
	 * @return [type]                 [description]
	 */
	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
		return $this->get_directions_link( $value );
	}

	/**
	 * Generate directions link.
	 *
	 * @param  [type] $value [description].
	 * @return [type]        [description].
	 */
	public function get_directions_link( $value ) {

		$value = maybe_unserialize( $value );

		if ( ! empty( $value['status'] ) ) {

			$link = 'https://www.google.com/maps/dir/?api=1&origin=' . $value['origin']['latitude'] . ',' . $value['origin']['longitude'] . '&destination=' . $value['destination']['latitude'] . ',' . $value['destination']['longitude'];

			return '<a href="' . esc_url( $link ) . '" target="_blank">' . esc_html__( 'View Directions on Google Maps', 'gfgeo' ) . '</a>';
		} else {
			return __( 'Directions not available', 'gfgeo' );
		}
	}
}
GF_Fields::register( new GFGEO_Directions_Panel_Field() );
