<?php
/**
 * Gravity Forms Geolocation address field.
 *
 * @package gravityforms-geolocation.
 */

if ( ! class_exists( 'GFForms' ) ) {
	die(); // abort if accessed directly.
}

/**
 * Register Address field
 *
 * @since  2.0
 */
class GFGEO_Full_Address_Field extends GF_Field {

	/**
	 * Field type
	 *
	 * @var string
	 */
	public $type = 'gfgeo_address';

	/**
	 * Field button
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'gfgeo_geolocation_fields',
			'text'  => __( 'Address', 'gfgeo' ),
		);
	}

	/**
	 * Field label
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_field_title() {
		return __( 'Address', 'gfgeo' );
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
			'gfgeo-address-autocomplete',
			'gfgeo-address-autocomplete-country',
			'gfgeo-address-autocomplete-types',
			'gfgeo-address-autocomplete-locator-bounds',
			'gfgeo-address-autocomplete-bounds',
			'gfgeo-infield-locator-button',
			'gfgeo-location-found-message',
			// gform options.
			'gfgeo-hide-location-failed-message',
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
			'placeholder_setting',
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
		$geocoder_id           = ! empty( $this->gfgeo_geocoder_id ) ? esc_attr( $form_id . '_' . $this->gfgeo_geocoder_id ) : '';
		$value                 = sanitize_text_field( stripslashes( esc_attr( $value ) ) );
		$address_autocomplete  = ! empty( $this->gfgeo_address_autocomplete ) ? '1' : '';
		$autocomplete_country  = ! empty( $this->gfgeo_address_autocomplete_country ) ? $this->gfgeo_address_autocomplete_country : '';
		$autocomplete_country  = is_array( $autocomplete_country ) ? implode( ',', $autocomplete_country ) : $autocomplete_country;
		$autocomplete_country  = esc_attr( $autocomplete_country );
		$autocomplete_types    = ! empty( $this->gfgeo_address_autocomplete_types ) ? esc_attr( $this->gfgeo_address_autocomplete_types ) : '';
		$autocomplete_bounds   = ! empty( $this->gfgeo_address_autocomplete_bounds ) ? esc_attr( $this->gfgeo_address_autocomplete_bounds ) : '';
		$locator_bounds        = ! empty( $this->gfgeo_address_autocomplete_locator_bounds ) ? '1' : '';
		$locator_button        = ! empty( $this->gfgeo_infield_locator_button ) ? '1' : '';

		$input = '<input name="input_' . $id . '" id="' . $field_id . '" type="text" data-address_field_id="' . $form_id . '_' . $id . '" data-geocoder_id="' . $geocoder_id . '" data-address_autocomplete="' . $address_autocomplete . '" data-autocomplete_country="' . $autocomplete_country . '" data-locator_enabled="' . $locator_button . '" data-autocomplete_types="' . $autocomplete_types . '" data-autocomplete_bounds="' . $autocomplete_bounds . '" data-autocomplete_locator_bounds="' . $locator_bounds . '" value="' . $value . '" class="gfgeo-address-field ' . $class . '" ' . $max_length . ' ' . $tabindex . ' ' . $placeholder_attribute . ' ' . $disabled_text . ' />';

		if ( ! empty( $this->gfgeo_infield_locator_button ) ) {

			$input .= GFGEO_Helper::get_locator_button( $form_id, $this, 'infield' );
		}

		return sprintf( "<div id='gfgeo-address-locator-wrapper-%s' class='ginput_container ginput_container_gfgeo_address gfgeo-address-locator-wrapper'>%s</div>", $form_id . '_' . $id, $input );
	}

	/**
	 * Generate geocoder data for email template tags
	 *
	 * @param  [type] $address       [description].
	 * @param  [type] $input_id      [description].
	 * @param  [type] $entry         [description].
	 * @param  [type] $form          [description].
	 * @param  [type] $modifier      [description].
	 * @param  [type] $raw_value     [description].
	 * @param  [type] $url_encode    [description].
	 * @param  [type] $esc_html      [description].
	 * @param  [type] $format        [description].
	 * @param  [type] $nl2br         [description].
	 *
	 * @return [type]                [description]
	 */
	public function get_value_merge_tag( $address, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br ) {

		if ( empty( $address ) ) {
			return $address;
		}

		$map_link = '';

		// create google maps only if enabled.
		if ( ! empty( $this->gfgeo_google_maps_link ) ) {

			// link to google map.
			$map_link = ' ' . GFGEO_Helper::get_map_link( $address );

		}

		$address = esc_html( $address ) . $map_link;

		return $address;
	}

	/**
	 * Display geocoded in entry list page.
	 *
	 * @param  [type] $address  [description].
	 * @param  [type] $entry    [description].
	 * @param  [type] $field_id [description].
	 * @param  [type] $columns  [description].
	 * @param  [type] $form     [description].
	 *
	 * @return [type]           [description]
	 */
	public function get_value_entry_list( $address, $entry, $field_id, $columns, $form ) {

		if ( empty( $address ) ) {
			return '';
		}

		// add link to google map.
		$map_link = GFGEO_Helper::get_map_link( $address );

		$address = '<span style="white-space: normal;margin-bottom:4px;">' . esc_html( $address ) . '</span><br /> ' . $map_link;

		return $address;
	}

	/**
	 * Display geocoded data in entry page.
	 *
	 * @param  [type]  $address  [description].
	 * @param  string  $currency [description].
	 * @param  boolean $use_text [description].
	 * @param  string  $format   [description].
	 * @param  string  $media    [description].
	 *
	 * @return [type]            [description]
	 */
	public function get_value_entry_detail( $address, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {

		if ( empty( $address ) ) {
			return '';
		}

		// link to google map.
		$map_link = GFGEO_Helper::get_map_link( $address );

		$address = esc_html( $address ) . ' - ' . $map_link;

		return $address;
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
GF_Fields::register( new GFGEO_Full_Address_Field() );
