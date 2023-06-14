<?php
/**
 * Gravity Forms Geolocation Google Map field.
 *
 * @package gravityforms-geolocation.
 */

if ( ! class_exists( 'GFForms' ) ) {
	die(); // abort if accessed directly.
}

/**
 * Register Map field
 *
 * @since 2.0
 */
class GFGEO_Google_Map_Field extends GF_Field {

	/**
	 * Field type
	 *
	 * @var string
	 */
	public $type = 'gfgeo_map';

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
	 * Not availabe message.
	 *
	 * @return [type] [description]
	 */
	public function map_na() {
		return __( 'Map not available', 'gfgeo' );
	}

	/**
	 * Field Title.
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_field_title() {
		return __( 'Google Map', 'gfgeo' );
	}

	/**
	 * Field button.
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'gfgeo_geolocation_fields',
			'text'  => __( 'Google Map', 'gfgeo' ),
		);
	}

	/**
	 * Field settings.
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_field_settings() {
		return array(
			// ggf options.
			'gfgeo-geocoder-id-multiple',
			'gfgeo-map-settings',
			// gform options.
			'conditional_logic_field_setting',
			'label_setting',
			'description_setting',
			'css_class_setting',
			'visibility_setting',
		);
	}

	/**
	 * Conditional logic.
	 *
	 * @return boolean [description]
	 */
	public function is_conditional_logic_supported() {
		return true;
	}

	/**
	 * Generate field input.
	 *
	 * @param  [type] $form  [description].
	 * @param  string $value [description].
	 * @param  [type] $entry [description].
	 *
	 * @return [type]        [description]
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {

		// field settings.
		$map_width    = ! empty( $this->gfgeo_map_width ) ? esc_attr( $this->gfgeo_map_width ) : '100%';
		$map_height   = ! empty( $this->gfgeo_map_height ) ? esc_attr( $this->gfgeo_map_height ) : '300px';
		$geocoders_id = '';
		$gfgeo_id     = ! empty( $this->gfgeo_id ) ? esc_attr( $this->gfgeo_id ) : absint( $form['id'] ) . '_' . (int) $this->id;

		// Set geocoder/s ID.
		if ( ! empty( $this->gfgeo_geocoder_id ) ) {

			if ( is_array( $this->gfgeo_geocoder_id ) ) {

				$geocoders_id = implode( ',', $this->gfgeo_geocoder_id );

			} else {
				$geocoders_id = esc_attr( $this->gfgeo_geocoder_id );
			}
		}

		// field ID.
		$field_id = esc_attr( $form['id'] . '_' . $this->id );

		if ( IS_ADMIN && $this->is_form_editor() ) {
			$map_height = '250px';
			$map_width  = '100%';
		}

		$input = "<div id='gfgeo-map-{$field_id}' class='gfgeo-map' data-geocoder_id='{$geocoders_id}' data-map_id='{$gfgeo_id}' style='height:{$map_height};width:{$map_width}'></div>";

		return sprintf( "<div id='gfgeo-map-wrapper-{$field_id}' class='ginput_container ginput_container_gfgeo_google_map gfgeo-map-wrapper'>%s</div>", $input );
	}

	/**
	 * Save the map data in serialized array.
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

		$map_data = array(
			'status'  => 0,
			'markers' => array(),
		);

		// Abort if no geocders were attached to the map.
		if ( empty( $this->gfgeo_geocoder_id ) ) {
			return maybe_serialize( $map_data );
		}

		$geocoders_id = $this->gfgeo_geocoder_id;

		// Make sure we have an array to support older versions of the plugin.
		if ( ! is_array( $geocoders_id ) ) {
			$geocoders_id = array( $geocoders_id );
		}

		// Loop through form field looking for geocoder field.
		foreach ( $form['fields'] as $field ) {

			if ( 'gfgeo_geocoder' === $field->type && in_array( $field->id, $geocoders_id ) && ! empty( $_POST[ 'input_' . $field->id ] ) ) { // WPCS: CSRF OK.

				$field_values = maybe_unserialize( $_POST[ 'input_' . $field->id ] ); // WPCS: sanitization OK, CSRF ok.

				if ( ! empty( $field_values['latitude'] ) && ! empty( $field_values['longitude'] ) ) {

					$map_data['status']     = 1;
					$map_data['map_center'] = array(
						'lat' => ! empty( $this->gfgeo_map_default_latitude ) ? $this->gfgeo_map_default_latitude : '40.7827096',
						'lng' => ! empty( $this->gfgeo_map_default_longitude ) ? $this->gfgeo_map_default_longitude : '-73.965309',
					);
					$map_data['zoom_level'] = ! empty( $this->gfgeo_zoom_level ) ? $this->gfgeo_zoom_level : '7';
					$map_data['map_type']   = ! empty( $this->gfgeo_map_type ) ? $this->gfgeo_map_type : 'ROADMAP';
					$map_data['markers'][]  = array(
						'lat'        => sanitize_text_field( stripslashes( $field_values['latitude'] ) ),
						'lng'        => sanitize_text_field( stripslashes( $field_values['longitude'] ) ),
						'marker_url' => ! empty( $field->gfgeo_map_marker_url ) ? $field->gfgeo_map_marker_url : 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
					);
				}
			}

			/*if ( 'gfgeo_directions' === $field->type && ! empty( $field->gfgeo_route_map_id ) && absint( $field->gfgeo_route_map_id ) === absint( $this->id ) ) { // WPCS: CSRF OK.

				if ( ! empty( $_POST[ 'input_' . $field->id ] ) ) {

					// This should be a JSON encoded array value.
					$directions = json_decode( stripslashes( $_POST[ 'input_' . $field->id ] ), true );

					if ( ! empty( $directions ) && is_array( $directions ) ) {
						$map_data['directions'] = $directions;
					}
				}
			}*/
		}

		return maybe_serialize( $map_data );
	}

	/**
	 * Display map message in entry list page.
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

		$map_na = $this->map_na();
		$value  = maybe_unserialize( $value );

		if ( ! empty( $value ) && is_array( $value ) && ! empty( $value['status'] ) ) {

			// generate the map.
			return __( 'View map in entry page', 'gfgeo' );

		} else {
			return $map_na;
		}

		// below is code for older versions where the map coords are not saved in map's field.
		if ( empty( $this->gfgeo_geocoder_id ) ) {
			return $map_na;
		}

		// map geocoder ID.
		$geocoder_id = $this->gfgeo_geocoder_id;

		// geocoded data.
		$geocoded_data = maybe_unserialize( $entry[ $geocoder_id ] );

		// verify coords.
		if ( empty( $geocoded_data['latitude'] ) || empty( $geocoded_data['longitude'] ) ) {
			return $map_na;
		} else {
			return __( 'View map in entry page', 'gfgeo' );
		}
	}

	/**
	 * Generate map in notifications using when merge tags.
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

		$value       = maybe_unserialize( $raw_value );
		$has_markers = false;

		// For older versions.
		if ( ! empty( $value ) && is_array( $value ) ) {

			if ( isset( $value['directions'] ) && ! empty( $value['latitude'] ) && ! empty( $value['longitude'] ) ) {

				$value['markers'][] = array(
					'lat'        => $value['latitude'],
					'lng'        => $value['longitude'],
					'marker_url' => 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
				);
			}
		}

		if ( ! empty( $value['markers'][0]['lat'] ) && ! empty( $value['markers'][0]['lng'] ) ) {
			$has_markers = true;
		}

		return GFGEO_Helper::generate_static_map( $value, $this, $has_markers, 'merge_tags' );
	}

	/**
	 * Display map in Entry page.
	 *
	 * @param  array   $value    [description].
	 * @param  string  $currency [description].
	 * @param  boolean $use_text [description].
	 * @param  string  $format   [description].
	 * @param  string  $media    [description].
	 *
	 * @return [type]            [description]
	 */
	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {

		$value       = maybe_unserialize( $value );
		$has_markers = false;

		// For older versions.
		if ( ! empty( $value ) && is_array( $value ) ) {

			if ( isset( $value['directions'] ) && ! empty( $value['latitude'] ) && ! empty( $value['longitude'] ) ) {

				$value['markers'][] = array(
					'lat'        => $value['latitude'],
					'lng'        => $value['longitude'],
					'marker_url' => 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
				);
			}
		}

		if ( ! empty( $value['markers'][0]['lat'] ) && ! empty( $value['markers'][0]['lng'] ) ) {
			$has_markers = true;
		}

		return GFGEO_Helper::generate_static_map( $value, $this, $has_markers, 'entries' );
	}
}
GF_Fields::register( new GFGEO_Google_Map_Field() );
