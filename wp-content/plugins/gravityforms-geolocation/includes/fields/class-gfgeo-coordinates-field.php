<?php
/**
 * Gravity Forms Geolocation Coordinates field.
 *
 * @author  Eyal Fitoussi.
 *
 * @package gravityforms-geolocation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register coordinates field
 *
 * @since  2.0
 */
class GFGEO_Coordinates_Field extends GF_Field {

	/**
	 * Field type
	 *
	 * @var string
	 */
	public $type = 'gfgeo_coordinates';

	/**
	 * Field button
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'gfgeo_geolocation_fields',
			'text'  => __( 'Coordinates', 'gfgeo' ),
		);
	}

	/**
	 * Field label
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_field_title() {
		return __( 'Coordinates', 'gfgeo' );
	}

	/**
	 * Field settings
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_field_settings() {
		return array(
			// ggf options.
			'gfgeo-geocoder-id',
			'gfgeo-latitude-placeholder',
			'gfgeo-longitude-placeholder',
			'gfgeo-custom-field-method',
			// gform options.
			'post_custom_field_setting',
			'conditional_logic_field_setting',
			'prepopulate_field_setting',
			'error_message_setting',
			'label_setting',
			'label_placement_setting',
			'admin_label_setting',
			'size_setting',
			'rules_setting',
			'visibility_setting',
			'duplicate_setting',
			'description_setting',
			'css_class_setting',
			'gfgeo-google-maps-link',
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
	 * Generate the front-end input field
	 *
	 * @param  [type] $form  [description].
	 * @param  string $value [description].
	 * @param  [type] $entry [description].
	 *
	 * @return [type]        [description]
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {

		$form_id         = absint( $form['id'] );
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();
		$id              = (int) $this->id;
		$field_id        = $is_entry_detail || $is_form_editor || 0 === absint( $form_id ) ? "input_$id" : 'input_' . $form_id . "_$id";
		$size            = esc_attr( $this->size );
		$class_suffix    = $is_entry_detail ? '_admin' : '';
		$class           = $class_suffix;
		$max_length      = is_numeric( $this->maxLength ) ? "maxlength='{$this->maxLength}'" : '';
		$tabindex        = $this->get_tabindex();
		$disabled_text   = $is_form_editor ? 'disabled="disabled"' : '';
		$geocoder_id     = ! empty( $this->gfgeo_geocoder_id ) ? esc_attr( $form_id . '_' . $this->gfgeo_geocoder_id ) : '';

		$lat_placeholder_value = ! empty( $this->gfgeo_latitude_placeholder ) ? $this->gfgeo_latitude_placeholder : __( 'Latitude', 'gfgeo' );
		$lng_placeholder_value = ! empty( $this->gfgeo_longitude_placeholder ) ? $this->gfgeo_longitude_placeholder : __( 'Longitude', 'gfgeo' );
		$lat_placeholder       = sprintf( "placeholder='%s'", esc_attr( $lat_placeholder_value ) );
		$lng_placeholder       = sprintf( "placeholder='%s'", esc_attr( $lng_placeholder_value ) );

		if ( ! is_array( $value ) && ! is_serialized( $value ) ) {

			if ( strpos( $value, '|' ) !== false ) {

				$latlng = explode( '|', $value );

			} elseif ( strpos( $value, ',' ) !== false ) {

				$latlng = explode( ',', $value );

			} else {

				$latlng = array( '', '' );
			}

			$value = array(
				'latitude'  => $latlng[0],
				'longitude' => $latlng[1],
			);

		} else {

			$value = maybe_unserialize( $value );
		}

		$latitude_val  = ! empty( $value['latitude'] ) ? sanitize_text_field( esc_attr( $value['latitude'] ) ) : '';
		$longitude_val = ! empty( $value['longitude'] ) ? sanitize_text_field( esc_attr( $value['longitude'] ) ) : '';

		$input  = '<span id="' . $field_id . '_latitude_container" class="ginput_left">';
		$input .= '<input name="input_' . $id . '[latitude]" id="' . $field_id . '_latitude" data-field_id="' . $form_id . '_' . $id . '" data-coords_field="latitude" data-geocoder_id="' . $geocoder_id . '" type="text" value="' . $latitude_val . '" class="' . $class . ' gfgeo-coordinates-field gfgeo-latitude-field" ' . $max_length . ' ' . $tabindex . ' ' . $lat_placeholder . ' ' . $disabled_text . ' />';
		$input .= '</span>';

		$input .= '<span id="' . $field_id . '_longitude_container" class="ginput_right">';
		$input .= '<input name="input_' . $id . '[longitude]" id="' . $field_id . '_longitude" data-field_id="' . $form_id . '_' . $id . '" data-coords_field="longitude" data-geocoder_id="' . $geocoder_id . '" type="text" value="' . $longitude_val . '" class="' . $class . ' gfgeo-coordinates-field gfgeo-longitude-field" ' . $max_length . ' ' . $tabindex . ' ' . $lng_placeholder . ' ' . $disabled_text . '/>';

		$input .= '</span>';

		return sprintf( "<div id='gfgeo-coordinates-wrapper-%s' class='ginput_complex ginput_container gfgeo-coordinates-wrapper' data-geocoder_id='%s' data-field_id='%s'>%s</div>", $form_id . '_' . $id, $geocoder_id, $form_id . '_' . $id, $input );
	}

	/**
	 * Modify value when exporting to CSV file.
	 *
	 * @param  array   $entry    form entry.
	 * @param  integer $input_id field ID.
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

		$format = apply_filters( 'gfgeo_coordinates_field_export_format', '|', $value, $entry, $input_id, $use_text, $is_csv );

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

		return $value;
	}

	/**
	 * Generate coordinates data for email template tags.
	 *
	 * @param  mixed   $value      value.
	 * @param  integer $input_id   input ID.
	 * @param  array   $entry      entry.
	 * @param  array   $form       the form.
	 * @param  mixed   $modifier   modifier.
	 * @param  mixed   $raw_value  the field raw value.
	 * @param  [type]  $url_encode [description].
	 * @param  [type]  $esc_html   [description].
	 * @param  [type]  $format     [description].
	 * @param  [type]  $nl2br      [description].
	 *
	 * @return [type]             [description]
	 */
	public function get_value_merge_tag( $value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br ) {

		$coordinates = $raw_value;

		if ( empty( $coordinates ) ) {

			if ( empty( $_POST[ 'input_' . $this->id ] ) ) { // WPCS: CSRF ok.
				return '';
			}

			$coordinates = $_POST[ 'input_' . $this->id ]; // WPCS: CSRF ok, XSS ok, sanitization ok.
		}

		$coordinates = maybe_unserialize( $coordinates );

		if ( is_array( $coordinates ) ) {
			$coordinates = array_map( 'sanitize_text_field', $coordinates );
		} else {
			$coordinates = sanitize_text_field( $coordinates );
		}

		/**
		 * Display specific fields based on the shortcode tag.
		 *
		 * Will be used in confirmation page, email, and query strings.
		 */
		if ( strpos( $input_id, '.' ) !== false ) {

			$tag_field_id = substr( $input_id, strpos( $input_id, '.' ) + 1 );

			if ( 1 === absint( $tag_field_id ) && ! empty( $coordinates['latitude'] ) ) {

				return $coordinates['latitude'];

			} elseif ( 2 === absint( $tag_field_id ) && ! empty( $coordinates['longitude'] ) ) {

				return $coordinates['longitude'];

			} else {

				return '';
			}

			// otherwise show all fields.
		} else {

			// if passing via querystring.
			if ( ! empty( $form['confirmation']['queryString'] ) ) {

				if ( is_array( $coordinates ) ) {

					return $coordinates['latitude'] . '|' . $coordinates['longitude'];

				} else {

					return $coordinates;
				}

				// confirmation page or email.
			} else {

				$map_link = ! empty( $this->gfgeo_google_maps_link ) ? true : false;

				return $this->get_output_coordinates( $coordinates, $map_link );
			}
		}
	}

	/**
	 * Serialize the coordinates array before saving to entry. Gform does not allow saving unserialized arrays.
	 *
	 * @param  [type] $value      [description].
	 * @param  [type] $form       [description].
	 * @param  [type] $input_name [description].
	 * @param  [type] $lead_id    [description].
	 * @param  [type] $lead       [description].
	 *
	 * @return [type]             [description]
	 */
	public function get_value_save_entry( $value, $form, $input_name, $lead_id, $lead ) {

		if ( is_array( $value ) ) {

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
	 * Display coordinates in entry list page.
	 *
	 * @param  [type] $value    [description].
	 * @param  [type] $entry    [description].
	 * @param  [type] $field_id [description].
	 * @param  [type] $columns  [description].
	 * @param  [type] $form     [description].
	 *
	 * @return [type]           [description]
	 */
	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {

		if ( empty( $value ) ) {
			return '';
		}

		return $this->get_output_coordinates( $value, true );
	}

	/**
	 * Display coordinates in entry details page.
	 *
	 * @param  [type]  $value    [description].
	 * @param  string  $currency [description].
	 * @param  boolean $use_text [description].
	 * @param  string  $format   [description].
	 * @param  string  $media    [description].
	 *
	 * @return [type]            [description]
	 */
	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {

		if ( empty( $value ) || 'text' === $format ) {
			return $value;
		}

		// if in front-end submission display map link only if needed.
		if ( ! empty( $_POST['gform_submit'] ) ) { // WPCS: CSRF ok.

			$map_link = ! empty( $this->gfgeo_google_maps_link ) ? true : false;

			return $this->get_output_coordinates( $value, $map_link );

			// in back end entry page display it all the time.
		} else {

			return $this->get_output_coordinates( $value, true );
		}
	}

	/**
	 * Generate the coordinates output.
	 *
	 * @param  mixed   $value    array or serialied array of coords..
	 * @param  boolean $map_link [description].
	 *
	 * @return [type]            [description]
	 */
	public function get_output_coordinates( $value, $map_link = false ) {

		$value = maybe_unserialize( $value );

		if ( empty( $value ) || ! is_array( $value ) ) {
			return $value;
		}

		$output  = '';
		$output .= '<li><strong>' . __( 'Latitude', 'gfgeo' ) . ':</strong> ' . $value['latitude'] . '</li>';
		$output .= '<li><strong>' . __( 'Longitude', 'gfgeo' ) . ':</strong> ' . $value['longitude'] . '</li>';

		if ( $map_link ) {

			$map_it = GFGEO_Helper::get_map_link( $value );

			$output .= '<li>' . $map_it . '</li>';
		}

		return "<ul class='bulleted'>{$output}</ul>";
	}

	/**
	 * Allow HTML.
	 *
	 * @return [type] [description]
	 */
	public function allow_html() {
		return false;
	}
}
GF_Fields::register( new GFGEO_Coordinates_Field() );
