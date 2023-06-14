<?php
/**
 * Gravity Forms Geolocation Geocoder field.
 *
 * @package gravityforms-geolocation.
 */

if ( ! class_exists( 'GFForms' ) ) {
	die(); // abort if accessed directly.
}

/**
 * Register Geocoder Field
 *
 * @since  2.0
 */
class GFGEO_Geocoder_Field extends GF_Field {

	/**
	 * Field type
	 *
	 * @var string
	 */
	public $type = 'gfgeo_geocoder';

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
	 * Field button.
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'gfgeo_geolocation_fields',
			'text'  => __( 'Geocoder', 'gfgeo' ),
		);
	}

	/**
	 * Field label.
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_field_title() {
		return __( 'Geocoder', 'gfgeo' );
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
				'gfgeo-geocoder-settings',
				'label_setting',
				'admin_label_setting',
				'prepopulate_field_setting',
				'post_custom_field_setting',
				'prepopulate_field_setting',
				'error_message_setting',
				'rules_setting',
				'duplicate_setting',
				'css_class_setting',
			);

		}

		return array(
			'label_setting',
			'admin_label_setting',
			'gfgeo-geocoder-settings',
			'gfgeo-location-found-message',
			'gfgeo-hide-location-failed-message',
			'prepopulate_field_setting',
			'post_custom_field_setting',
			'prepopulate_field_setting',
			'gfgeo-ip-locator-status',
			'error_message_setting',
			'gfgeo-google-maps-link',
			'rules_setting',
			'duplicate_setting',
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
	 * Save value as an array.
	 *
	 * @return boolean [description]
	 */
	public function is_value_submission_array() {
		return true;
	}

	/**
	 * Field Merge Tag.
	 *
	 * @var array
	 */
	public $geocoder_fields_tags = array(
		'',
		'place_name',
		'street_number',
		'street_name',
		'street',
		'premise',
		'subpremise',
		'neighborhood',
		'city',
		'county',
		'region_code',
		'region_name',
		'postcode',
		'country_code',
		'country_name',
		'address',
		'formatted_address',
		'latitude',
		'longitude',
		'distance_text',
		'distance_value',
		'duration_text',
		'duration_value',
	);

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

			$content  = '<div class="gfgeo-admin-hidden-container">';
			$content .= '<p>' . __( 'This field will be hidden in the front-end form.', 'gfgeo' ) . '</p>';
			$content .= '</div>';

			return $content;

			// Front-end form.
		} else {

			$form_id         = absint( $form['id'] );
			$is_entry_detail = $this->is_entry_detail();
			$is_form_editor  = $this->is_form_editor();
			$id              = (int) $this->id;
			$field_id        = $is_entry_detail || $is_form_editor || 0 === $form_id ? "input_$id" : 'input_' . $form_id . "_$id";
			$value           = ! empty( $value ) ? maybe_unserialize( $value ) : array();
			$gfgeo_id        = ! empty( $this->gfgeo_id ) ? esc_attr( $this->gfgeo_id ) : $form_id . '_' . $id;
			$input           = '';

			// if not a form submission or edit entry page looks for default coords to eocode.
			if ( ! $is_entry_detail && ! $form['gfgeo_args']['is_submitted'] && ( $form['gfgeo_args']['is_page_load'] || apply_filters( 'gfgeo_enable_geocoder_default_location', false, $form, $this ) ) ) { // WPCS: CSRF ok.

				$default_lat = '';
				$default_lng = '';

				// check if default coords pass by URL. Possible from another from.
				if ( ! empty( $this->allowsPrepopulate ) && ! empty( $this->inputName ) && ! empty( $_GET[ $this->inputName ] ) && strpos( $_GET[ $this->inputName ], '|' ) !== false ) { // WPCS: CSRF ok, sanitization ok.

					$input_coords = explode( '|', str_replace( ' ', '', $_GET[ $this->inputName ] ) ); // WPCS: CSRF ok, sanitization ok.

					if ( ! empty( $input_coords[0] ) && ! empty( $input_coords[1] ) ) {
						$default_lat = sanitize_text_field( $input_coords[0] );
						$default_lng = sanitize_text_field( $input_coords[1] );
					}

					// check if default coords pass via the "field_values" shortcode parameter.
				} elseif ( ! empty( $this->allowsPrepopulate ) && ! empty( $this->inputName ) && ! empty( $value ) && ! is_array( $value ) && strpos( $value, '|' ) !== false ) { // WPCS: CSRF ok, sanitization ok.

					$input_coords = explode( '|', trim( str_replace( ' ', '', $value ) ) ); // WPCS: CSRF ok, sanitization ok.

					if ( ! empty( $input_coords[0] ) && ! empty( $input_coords[1] ) ) {
						$default_lat = sanitize_text_field( $input_coords[0] );
						$default_lng = sanitize_text_field( $input_coords[1] );
					}

					// if value exists already. Possibly from custom fields or user meta when updating a user.
					// When value already exists, we won't geocode it by default. BUt this can be changed with the filter below.
				} elseif ( ! empty( $value['latitude'] ) && ! empty( $value['longitude'] ) ) {

					if ( apply_filters( 'gfgeo_enable_user_update_form_default_geocoding', true, $this ) ) {
						$default_lat = sanitize_text_field( esc_attr( $value['latitude'] ) );
						$default_lng = sanitize_text_field( esc_attr( $value['longitude'] ) );
					}

					// Otherwise, check for default coordinates in form options.
				} elseif ( ! empty( $this->gfgeo_default_latitude ) && ! empty( $this->gfgeo_default_longitude ) ) {

					$default_lat = sanitize_text_field( esc_attr( $this->gfgeo_default_latitude ) );
					$default_lng = sanitize_text_field( esc_attr( $this->gfgeo_default_longitude ) );
				}

				// generate default coords if set. Only on first page load.
				if ( ! empty( $default_lat ) && ! empty( $default_lng ) ) {
					$input .= "<span id='gfgeo-geocoder-default-coordinates-{$gfgeo_id}' class='gfgeo-geocoder-default-coordinates' data-geocoder_id='{$gfgeo_id}' data-latitude='{$default_lat}' data-longitude='{$default_lng}'></span>";
				}
			}

			// loop through location fields and create hidden geocoded fields.
			foreach ( array_keys( GFGEO_Helper::get_location_fields() ) as $field_name ) {

				if ( '' === $field_name ) {
					continue;
				}

				$field_name = esc_attr( $field_name );

				// get default field value.
				$field_value = ! empty( $value[ $field_name ] ) ? esc_attr( sanitize_text_field( stripslashes( $value[ $field_name ] ) ) ) : '';

				$input .= "<input type='hidden' name='input_{$id}[{$field_name}]' id='{$field_id}_{$field_name}' class='gfgeo-geocoded-field-{$gfgeo_id} {$field_name} gfgeo-geocoded-field-{$field_name}' data-field_id='{$gfgeo_id}' value='{$field_value}'>";
			}

			$page_loaded = ! empty( $_POST[ "gfgeo_page_{$this->pageNumber}_loaded" ] ) ? '1' : ''; // WPCS: CSRF ok.

			$input .= "<input name='input_{$id}[0]' id='{$field_id}_0' type='hidden' />";

			return sprintf( '<div id="gfgeo-geocoded-hidden-fields-wrapper-%s" class="ginput_container ginput_container_gfgeo_geocoder gfgeo-geocoded-hidden-fields-wrapper" data-geocoder_id="%s" style="display:none">%s</div>', $gfgeo_id, $gfgeo_id, $input );
		}
	}

	/**
	 * Generate geocoded data output.
	 *
	 * @param  [type] $geocoder_data [description].
	 *
	 * @param  URL    $map_link      map link.
	 *
	 * @param  array  $entry         the form entry.
	 *
	 * @param  string $type          page vew type.
	 *
	 * @return [type]                [description]
	 */
	public function get_geocoded_data_output( $geocoder_data, $map_link = false, $entry = array(), $type = false ) {

		// unserialize data.
		$geocoder_data = maybe_unserialize( $geocoder_data );

		if ( empty( $geocoder_data ) || ! is_array( $geocoder_data ) ) {
			return $geocoder_data;
		}

		$map_it            = '';
		$org_geocoder_data = $geocoder_data;

		$origin_lat = $geocoder_data['latitude'];
		$origin_lng = $geocoder_data['longitude'];

		$geocoder_data['distance_destination'] = ! empty( $this->gfgeo_distance_destination_geocoder_id ) ? 'Geocoder ID ' . absint( $this->gfgeo_distance_destination_geocoder_id ) : __( 'N/A', 'gfgeo' );

		// create google maps only if enabled.
		if ( $map_link && ! empty( $origin_lat ) && ! empty( $origin_lng ) ) {
			$geocoder_data['google_map_link'] = GFGEO_Helper::get_map_link( $geocoder_data );
		}

		$default_geo_fields = GFGEO_Helper::get_location_fields();

		unset( $default_geo_fields[''], $default_geo_fields['status'], $geocoder_data['status'] );

		$default_geo_fields['distance_destination'] = 'Distance destination';
		$default_geo_fields['google_map_link']      = 'google_map_link';

		$default_geo_fields = apply_filters( 'gfgeo_geocoder_field_fields_output', $default_geo_fields, $geocoder_data, $entry );

		// replace keys of the original array with address fields labels
		// this function uses array_intersect because by default array_combine
		// return false if the lenght is unequal in both array.
		// and since the number of output fields chagned is some versions
		// the lenght of array is different io older data.
		$geocoder_data = array_combine( array_intersect_key( $default_geo_fields, $geocoder_data ), array_intersect_key( $geocoder_data, $default_geo_fields ) );

		$output = '<ol style="list-style:none;">';

		// generate the output list of geocoded fields.
		foreach ( $geocoder_data as $name => $value ) {

			// skip the status value.
			if ( 'status' === $name || '' === $name || 'google_map_link' === $name ) {
				continue;
			}

			$value = ! empty( $value ) ? esc_html( $value ) : __( 'N/A', 'gfgeo' );

			$output .= '<li><strong>' . esc_attr( $name ) . ':</strong> ' . $value . '</li>';
		}

		if ( isset( $default_geo_fields['google_map_link'] ) && ! empty( $geocoder_data['google_map_link'] ) ) {
			$output .= '<li>' . $geocoder_data['google_map_link'] . '</li>';
		}

		// Generate directions link if location data is available.
		if ( ! empty( $this->gfgeo_distance_destination_geocoder_id ) && ! empty( $entry[ $this->gfgeo_distance_destination_geocoder_id ] ) ) {

			$dest_geocoder = maybe_unserialize( $entry[ $this->gfgeo_distance_destination_geocoder_id ] );
			$label         = __( 'Directions to destination', 'gfgeo' );

			if ( ! empty( $dest_geocoder['latitude'] ) && ! empty( $dest_geocoder['longitude'] ) ) {

				$link = 'https://www.google.com/maps/dir/?api=1&origin=' . $origin_lat . ',' . $origin_lng . '&destination=' . $dest_geocoder['latitude'] . ',' . $dest_geocoder['longitude'];

				$output .= '<li><strong>' . $label . '</strong>: <a href="' . esc_url( $link ) . '" target="_blank">' . __( 'View in Google Map', ' gfgeo' ) . '</a></li>';

				$map_it = false;
			} else {
				$output .= '<li><strong>' . $label . '</strong>: ' . __( 'N/A', ' gfgeo' ) . '</a></li>';
			}
		}

		//if ( ! empty( $map_it ) ) {
		//	$output .= '<li>' . $map_it . '</li>';
		//}

		$output .= '</ol>';

		return apply_filters( 'gfgeo_geocoder_field_geocoded_data_output', $output, $geocoder_data, $org_geocoder_data, $map_it, $type );
	}

	/**
	 * Generate geocoder data for email template tags.
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

		$geocoder_data = $raw_value;

		if ( empty( $value ) ) {

			if ( empty( $_POST[ 'input_' . $this->id ] ) ) { // WPCS: CSRF ok.
				return '';
			}

			$geocoder_data = $_POST[ 'input_' . $this->id ]; // WPCS: CSRF ok, sanitization ok.
		}

		$geocoder_data = maybe_unserialize( $geocoder_data );

		if ( is_array( $geocoder_data ) ) {
			$geocoder_data = array_map( 'sanitize_text_field', $geocoder_data );
		}

		/**
		 * Display specific fields based on the shortcode tag.
		 *
		 * Will be used in confirmation page, email, and query strings.
		 */
		if ( strpos( $input_id, '.' ) !== false ) {

			$tag_field_id = substr( $input_id, strpos( $input_id, '.' ) + 1 );

			if ( 0 === absint( $tag_field_id ) ) {

				return GFGEO_Helper::get_map_link( $geocoder_data );

			} elseif ( ! empty( $geocoder_data[ $this->geocoder_fields_tags[ $tag_field_id ] ] ) ) {

				return $geocoder_data[ $this->geocoder_fields_tags[ $tag_field_id ] ];

			} else {

				return '';
			}

			// if passing value as a whole.
		} else {

			// if passing via querystring.
			if ( ! empty( $form['confirmation']['queryString'] ) ) {

				if ( is_array( $geocoder_data ) ) {

					$output = '';

					unset( $geocoded_data['status'] );

					foreach ( $geocoder_data as $key => $value ) {

						$output .= $key . ':';
						$output .= ! empty( $value ) ? $value . '|' : 'n/a|';
					}

					return $output;

				} else {

					return $geocoder_data;
				}
			} else {

				$map_link = apply_filters( 'gfgeo_geocoder_field_output_map_link', true );

				return $this->get_geocoded_data_output( $geocoder_data, $map_link, $entry, 'merge_tags' );
			}
		}
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

		if ( empty( $input_id ) ) {
			$input_id = $this->id;
		}

		$value = rgar( $entry, $input_id );

		if ( ! $is_csv ) {
			return $value;
		}

		if ( empty( $value ) ) {
			return '';
		}

		$format = apply_filters( 'gfgeo_geocoder_field_export_format', 'serialized', $value, $entry, $input_id, $use_text, $is_csv );

		if ( empty( $format ) ) {
			return $value;
		}

		if ( 'serialized' === $format ) {

			$value = maybe_serialize( $value );

		} else {

			$value = maybe_unserialize( $value );

			if ( ! is_array( $value ) ) {
				return $value;
			}

			$output = '';

			foreach ( $value as $key => $fvalue ) {
				$output .= $key . ':';
				$output .= ! empty( $fvalue ) ? $fvalue . $format : 'n/a' . $format;
			}

			$value = $output;
		}

		return apply_filters( 'gfgeo_geocoder_field_value_export', $value, $entry, $input_id, $use_text, $is_csv, $format );
	}

	/**
	 * Serialize the geocoded array before saving to entry. Gform not allow saving unserialized arrays.
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

		if ( is_array( $value ) ) {

			unset( $value[0] );

			foreach ( $value as &$v ) {
				$v = $this->sanitize_entry_value( $v, $form['id'] );
			}
		} else {
			$value = $this->sanitize_entry_value( $value, $form['id'] );
		}

		if ( empty( $value ) ) {

			return '';

		} elseif ( is_array( $value ) ) {

			return maybe_serialize( $value );

		} else {
			return $value;
		}
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

		if ( empty( $geocoded_data ) ) {
			return '';
		}

		return __( 'See data in entry page', 'gfgeo' );
	}

	/**
	 * Display geocoded data in entry page.
	 *
	 * @param  [type]  $geocoder_data [description].
	 * @param  string  $currency      [description].
	 * @param  boolean $use_text      [description].
	 * @param  string  $format        [description].
	 * @param  string  $media         [description].
	 *
	 * @return [type]                 [description]
	 */
	public function get_value_entry_detail( $geocoder_data, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {

		if ( empty( $geocoder_data ) || 'text' === $format ) {
			return $geocoder_data;
		}

		// In admin, get the entry using the entry ID.
		if ( is_admin() ) {
			$entry = ! empty( $_GET['lid'] ) ? GFAPI::get_entry( absint( $_GET['lid'] ) ) : array(); // WPCS: CSRF ok.

			// Otherwise, use the $_POST global.
		} else {

			$entry = array();

			if ( ! empty( $_POST ) ) { // WPCS: CSRF ok.

				foreach ( $_POST as $key => $field ) { // WPCS: CSRF ok.

					$new_key = str_replace( 'input_', '', $key );

					$entry[ $new_key ] = $field;
				}
			}
		}

		return $this->get_geocoded_data_output( $geocoder_data, true, $entry, 'entry_view' );
	}
}
GF_Fields::register( new GFGEO_Geocoder_Field() );
