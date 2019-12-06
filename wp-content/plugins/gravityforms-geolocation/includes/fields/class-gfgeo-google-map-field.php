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
			'gfgeo-geocoder-id',
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
		$map_width              = ! empty( $this->gfgeo_map_width ) ? esc_attr( $this->gfgeo_map_width ) : '100%';
		$map_height             = ! empty( $this->gfgeo_map_height ) ? esc_attr( $this->gfgeo_map_height ) : '300px';
		$map_type               = ! empty( $this->gfgeo_map_type ) ? esc_attr( $this->gfgeo_map_type ) : 'ROADMAP';
		$zoom_level             = ! empty( $this->gfgeo_zoom_level ) ? esc_attr( $this->gfgeo_zoom_level ) : '12';
		$map_marker             = ! empty( $this->gfgeo_map_marker ) ? esc_url( $this->gfgeo_map_marker ) : '';
		$draggable              = ! empty( $this->gfgeo_draggable_marker ) ? 'true' : 'false';
		$drag_on_click          = ! empty( $this->gfgeo_set_marker_on_click ) ? 'true' : 'false';
		$scrollwheel            = ! empty( $this->gfgeo_map_scroll_wheel ) ? 'true' : 'false';
		$disable_address_output = ! empty( $this->gfgeo_disable_address_output ) ? 'true' : 'false';

		// default coords.
		$latitude  = ! empty( $this->gfgeo_map_default_latitude ) ? esc_attr( $this->gfgeo_map_default_latitude ) : '40.7827096';
		$longitude = ! empty( $this->gfgeo_map_default_longitude ) ? esc_attr( $this->gfgeo_map_default_longitude ) : '-73.965309';

		// geocoder ID.
		$geocoder_id = ! empty( $this->gfgeo_geocoder_id ) ? esc_attr( $this->gfgeo_geocoder_id ) : '';

		// Geocoder ID input.
		$input_geocoder_id = ! empty( $this->gfgeo_geocoder_id ) ? esc_attr( $form['id'] . '_' . $geocoder_id ) : '';

		// field ID.
		$field_id = esc_attr( $form['id'] . '_' . $this->id );

		if ( IS_ADMIN ) {
			$draggable  = 'false';
			$map_height = '200px';
			$map_width  = '100%';
		}

		$input  = '<div id="gfgeo-map-wrapper-' . $field_id . '" class="gfgeo-map-wrapper">';
		$input .= '<div id="gfgeo-map-' . $field_id . '" class="gfgeo-map" data-geocoder_id="' . $input_geocoder_id . '" data-map_id="' . $field_id . '" data-latitude="' . $latitude . '" data-longitude="' . $longitude . '" data-map_type="' . $map_type . '" data-zoom_level="' . $zoom_level . '" data-draggable="' . $draggable . '" data-drag_on_click="' . $drag_on_click . '" data-scrollwheel="' . $scrollwheel . '" data-disable_address_output="' . $disable_address_output . '" data-map_marker="' . $map_marker . '" style="height:' . $map_height . ';width:' . $map_width . '"></div>';
		$input .= '</div>';

		return sprintf( "<div class='ginput_container ginput_container_gfgeo_map'>%s</div>", $input );
	}

	/**
	 * Save the map coords in serialized array.
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

		$coords = array(
			'status'                => 0,
			'directions'            => 0,
			'latitude'              => '',
			'longitude'             => '',
			'destination_latitude'  => '',
			'destination_longitude' => '',
		);

		$geocoder_id = absint( $this->gfgeo_geocoder_id );

		if ( empty( $geocoder_id ) || empty( $_POST[ 'input_' . $geocoder_id ] ) ) { // WPCS: CSRF ok.
			return maybe_serialize( $coords );
		}

		$geocoder = $_POST[ 'input_' . $geocoder_id ]; // WPCS: CSRF ok, Sanitization ok.
		$geocoder = maybe_unserialize( $geocoder );

		if ( empty( $geocoder['latitude'] ) || empty( $geocoder['longitude'] ) ) {
			return maybe_serialize( $coords );
		}

		$coords['status']    = 1;
		$coords['latitude']  = $geocoder['latitude'];
		$coords['longitude'] = $geocoder['longitude'];

		// Look for destination coords.
		foreach ( $form['fields'] as $field ) {

			if ( 'gfgeo_geocoder' === $field->type && absint( $field->id ) === $geocoder_id ) {

				if ( ! empty( $field->gfgeo_distance_destination_geocoder_id ) && ! empty( $_POST[ 'input_' . $field->gfgeo_distance_destination_geocoder_id ] ) ) { // WPCS: CSRF ok.

					$dest_geocoder = absint( $_POST[ 'input_' . $field->gfgeo_distance_destination_geocoder_id ] ); // WPCS: CSRF ok.
					$dest_geocoder = maybe_unserialize( $dest_geocoder );

					if ( ! empty( $dest_geocoder['latitude'] ) && ! empty( $dest_geocoder['longitude'] ) ) {

						$coords['directions']            = 1;
						$coords['destination_latitude']  = $dest_geocoder['latitude'];
						$coords['destination_longitude'] = $dest_geocoder['longitude'];
					}
				}
			}
		}

		$coords = maybe_serialize( $coords );

		$_POST[ 'input_' . $this->id ] = $coords;

		return $coords;
	}

	/**
	 * Display geocoded in entry list page.
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

		if ( is_array( $value ) && isset( $value['status'] ) ) {

			if ( ! empty( $value['latitude'] ) && ! empty( $value['longitude'] ) ) {

				// generate the map.
				return __( 'View map in entry page', 'gfgeo' );

			} else {
				return $map_na;
			}
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
	 * Get the coordinats values and generate the static map.
	 *
	 * @param  [type] $value [description].
	 * @param  [type] $where [description].
	 * @return [type]        [description].
	 */
	public function get_static_map( $value, $where ) {

		if ( ! empty( $value['latitude'] ) && ! empty( $value['longitude'] ) ) {

			$map_src = self::generate_static_map( $value, $where );
			$output  = '<div class="gfgeo-static-map-warpper"><img style="width:100%;height:auto;" src="' . $map_src . '" />';

			// Get directions link if available.
			if ( ! empty( $value['directions'] ) ) {

				$link    = 'https://www.google.com/maps/dir/?api=1&origin=' . $value['latitude'] . ',' . $value['longitude'] . '&destination=' . $value['destination_latitude'] . ',' . $value['destination_longitude'];
				$output .= '<br /><a href="' . esc_url( $link ) . '" target="_blank" class="gfgeo-directions-link">' . esc_html__( 'View Directions on Google Maps', 'gfgeo' ) . '</a>';
			}

			$output .= '</div>';

			return $output;

		} else {
			return $this->map_na();
		}
	}

	/**
	 * Generate Google static map.
	 *
	 * @param  array  $value  map's coords.
	 * @param  string $where  page in which the map is generated.
	 *
	 * @return [type]        [description]
	 */
	public static function generate_static_map( $value, $where ) {

		$lat      = ! empty( $value['latitude'] ) ? $value['latitude'] : '';
		$lng      = ! empty( $value['longitude'] ) ? $value['longitude'] : '';
		$url_args = array(
			'markers' => 'color:red|' . $lat . ',' . $lng,
			'size'    => '500x200',
			'zoom'    => '13',
			'key'     => GFGEO_GOOGLE_MAPS_API,
		);

		if ( ! empty( $value['destination_latitude'] ) && ! empty( $value['destination_longitude'] ) ) {

			$dest_lat = $value['destination_latitude'];
			$dest_lng = $value['destination_longitude'];

			$url_args['path']     = 'color:0x0000ff|weight:5|' . $lat . ',' . $lng . '|' . $dest_lat . ',' . $dest_lng;
			$url_args['markers'] .= '|' . $dest_lat . ',' . $dest_lng;

			unset( $url_args['zoom'] );
		}

		// build the map query. Map settings can be modified via the filters below.
		$map_args = apply_filters(
			'gfgeo_google_map_field_map_settings',
			array(
				'protocol' => 'http',
				'url_base' => '://maps.googleapis.com/maps/api/staticmap?',
				'url_data' => urldecode(
					http_build_query(
						apply_filters(
							'gfgeo_google_map_field_map_settings_args',
							$url_args,
							$where
						),
						'',
						'&amp;'
					)
				),
			),
			$where
		);

		return esc_url( implode( '', $map_args ) );
	}

	/**
	 * Generate coordinates data for email template tags.
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

		$raw_value = maybe_unserialize( $raw_value );

		if ( is_array( $raw_value ) && isset( $raw_value['status'] ) ) {
			return $this->get_static_map( $raw_value, 'merge_tag' );
		}

		// below is code for older versions where the map coords are not saved in map's field.
		$map_na = $this->map_na();

		if ( empty( $this->gfgeo_geocoder_id ) ) {
			return $map_na;
		}

		$geocoder_id = $this->gfgeo_geocoder_id;

		if ( ! empty( $_POST[ 'input_' . $geocoder_id ] ) ) { // WPCS: CSRF ok.

			$geocoded_data = maybe_unserialize( $_POST[ 'input_' . $geocoder_id ] ); // WPCS: CSRF ok, sanitization ok.

			// solution for Gravity View?
		} elseif ( isset( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], 'entry' ) !== false ) {

			$url_values = explode( '/', $_SERVER['REQUEST_URI'] ); // WPCS: CSRF ok, sanitization ok.
			$key        = array_search( 'entry', $url_values, true );
			$key++;

			if ( ! is_numeric( $url_values[ $key ] ) ) {
				return $map_na;
			}

			// Entry details.
			$entry = GFAPI::get_entry( $url_values[ $key++ ] );

			// geocoded data.
			$geocoded_data = maybe_unserialize( $entry[ $geocoder_id ] );

		} else {
			return $map_na;
		}

		// verify coords.
		if ( empty( $geocoded_data['latitude'] ) || empty( $geocoded_data['longitude'] ) ) {
			return $map_na;
		}

		$src = self::generate_static_map( $geocoded_data, 'merge_tag' );

		// generate the map.
		$output = '<div><img style="width:100%;height:auto;" src="' . $src . '" /></div>';

		return $output;
	}

	/**
	 * Display map in entry page.
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

		$value = maybe_unserialize( $value );

		if ( is_array( $value ) && ! empty( $value['status'] ) ) {
			return $this->get_static_map( $value, 'entries' );
		}

		// below is code for older versions where the map coords are not saved in map's field.
		$map_na = $this->map_na();

		if ( empty( $this->gfgeo_geocoder_id ) ) {
			return $map_na;
		}

		// map geocoder ID.
		$geocoder_id = $this->gfgeo_geocoder_id;

		// if in entry details page.
		if ( is_admin() ) {

			if ( empty( $_GET['lid'] ) ) { // WPCS: CSRF ok.
				return $map_na;
			}

			// Entry details.
			$entry = GFAPI::get_entry( $_GET['lid'] ); // WPCS: CSRF ok, sanitization.

			// geocoded data.
			$geocoded_data = maybe_unserialize( $entry[ $geocoder_id ] );

			// on form submission.
		} else {

			if ( empty( $_POST[ 'input_' . $geocoder_id ] ) ) { // WPCS: CSRF ok.
				return $map_na;
			}

			$geocoded_data = maybe_unserialize( $_POST[ 'input_' . $geocoder_id ] ); // WPCS: CSRF ok, sanitization.
		}

		// verify coords.
		if ( empty( $geocoded_data['latitude'] ) || empty( $geocoded_data['longitude'] ) ) {
			return $map_na;
		}

		$src = self::generate_static_map( $geocoded_data, 'entries' );

		// generate the map.
		$value = '<div><img style="width:100%;height:auto;" src="' . $src . '" /></div>';

		return $value;
	}

	/**
	 * Allow HTML.
	 *
	 * @return [type] [description]
	 */
	public function allow_html() {
		return true;
	}
}
GF_Fields::register( new GFGEO_Google_Map_Field() );
