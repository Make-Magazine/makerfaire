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

		// create the map element.
		$input = '<div id="gfgeo-map-wrapper-' . $field_id . '" class="gfgeo-map-wrapper">';

		$input .= '<div id="gfgeo-map-' . $field_id . '" class="gfgeo-map" data-geocoder_id="' . $input_geocoder_id . '" data-map_id="' . $field_id . '" data-latitude="' . $latitude . '" data-longitude="' . $longitude . '" data-map_type="' . $map_type . '" data-zoom_level="' . $zoom_level . '" data-draggable="' . $draggable . '" data-drag_on_click="' . $drag_on_click . '" data-scrollwheel="' . $scrollwheel . '" data-disable_address_output="' . $disable_address_output . '" data-map_marker="' . $map_marker . '" style="height:' . $map_height . ';width:' . $map_width . '"></div>';
		$input .= '</div>';

		return sprintf( "<div class='ginput_container ginput_container_gfgeo_map'>%s</div>", $input );
	}

	/**
	 * Display geocoded in entry list page.
	 *
	 * @param  [type] $geocoded_data [description].
	 * @param  [type] $entry         [description].
	 * @param  [type] $field_id      [description].
	 * @param  [type] $columns       [description].
	 * @param  [type] $form          [description].
	 *
	 * @return [type]                [description]
	 */
	public function get_value_entry_list( $geocoded_data, $entry, $field_id, $columns, $form ) {

		$map_na = $this->map_na();

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
			return __( 'See map in entry page', 'gfgeo' );
		}
	}

	/**
	 * Generate Google static map.
	 *
	 * @param  [type] $lat   [description].
	 * @param  [type] $lng   [description].
	 * @param  [type] $where [description].
	 *
	 * @return [type]        [description]
	 */
	public static function generate_static_map( $lat, $lng, $where ) {

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
							array(
								'markers' => 'color:red|' . $lat . ',' . $lng,
								'size'    => '500x200',
								'zoom'    => '13',
								'key'     => GFGEO_GOOGLE_MAPS_API,
							),
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

		$map_na = $this->map_na();

		if ( empty( $this->gfgeo_geocoder_id ) ) {
			return $map_na;
		}

		// map geocoder ID.
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

		$src = self::generate_static_map( $geocoded_data['latitude'], $geocoded_data['longitude'], 'merge_tag' );

		// generate the map.
		$output = '<div><img style="width:100%;height:auto;" src="' . $src . '" /></div>';

		return $output;
	}

	/**
	 * Display geocoded data in entry page.
	 *
	 * @param  [type]  $output   [description].
	 * @param  string  $currency [description].
	 * @param  boolean $use_text [description].
	 * @param  string  $format   [description].
	 * @param  string  $media    [description].
	 *
	 * @return [type]            [description]
	 */
	public function get_value_entry_detail( $output, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {

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

		$src = self::generate_static_map( $geocoded_data['latitude'], $geocoded_data['longitude'], 'entries' );

		// generate the map.
		$output = '<div><img style="width:100%;height:auto;" src="' . $src . '" /></div>';

		return $output;
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
