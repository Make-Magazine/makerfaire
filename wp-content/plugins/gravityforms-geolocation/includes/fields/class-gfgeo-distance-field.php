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
class GFGEO_Distance_Field extends GF_Field {

	/**
	 * Field type
	 *
	 * @var string
	 */
	public $type = 'gfgeo_distance';

	/**
	 * Field button
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'gfgeo_geolocation_fields',
			'text'  => __( 'Distance', 'gfgeo' ),
		);
	}

	/**
	 * Field label
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_field_title() {
		return __( 'Distance', 'gfgeo' );
	}

	/**
	 * Field settings
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_field_settings() {
		return array(
			// ggf options.
			'gfgeo-distance-field-settings',
			// gform options.
			'gfgeo-custom-field-method',
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
	 * Generate the input field
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
		$size            = $this->size;
		$class_suffix    = $is_entry_detail ? '_admin' : '';
		$class           = $size . $class_suffix;

		$max_length            = is_numeric( $this->maxLength ) ? "maxlength='{$this->maxLength}'" : '';
		$tabindex              = $this->get_tabindex();
		$disabled_text         = $is_form_editor ? 'disabled="disabled"' : '';
		$placeholder_attribute = $this->get_field_placeholder_attribute();
		$geocoder_id           = ! empty( $this->gfgeo_distance_geocoders ) ? implode( ',', $this->gfgeo_distance_geocoders ) : '';
		$value                 = sanitize_text_field( stripslashes( esc_attr( $value ) ) );

		$input = '<input name="input_' . $id . '" id="' . $field_id . '" type="text" data-address_field_id="' . $form_id . '_' . $id . '" data-geocoders_id="' . $geocoder_id . '" ' . $class . '" ' . $max_length . ' ' . $tabindex . ' ' . $placeholder_attribute . ' ' . $disabled_text . ' />';

		if ( ! empty( $this->gfgeo_infield_locator_button ) ) {

			$input .= GFGEO_Helper::get_locator_button( $form_id, $this, 'infield' );
		}

		return sprintf( "<div id='gfgeo-address-locator-wrapper-%s' class='ginput_container ginput_container_gfgeo_address gfgeo-address-locator-wrapper'>%s</div>", $form_id . '_' . $id, $input );
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
GF_Fields::register( new GFGEO_Distance_Field() );
