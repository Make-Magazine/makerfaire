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

		// Gravity Forms v2.5+.
		if ( GFGEO_GF_2_5 ) {

			return array(
				// ggf options.
				'gfgeo-geocoder-id',
				'gfgeo-address-field-settings',
				// gform options.
				'default_value_setting',
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
			);
		}

		return array(
			// ggf options.
			'gfgeo-geocoder-id',
			'gfgeo-address-field-settings',
			'gfgeo-infield-locator-button',
			'gfgeo-location-found-message',
			// gform options.
			'default_value_setting',
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
	 * Enable / Disable map link in address field.
	 *
	 * @var boolean
	 */
	public $google_map_link = array(
		'merge_tag'    => false,
		'entry_list'   => false,
		'entry_detail' => true,
	);

	/**
	 * Generate the input field.
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

		$id       = (int) $this->id;
		$field_id = $is_entry_detail || $is_form_editor || 0 === $form_id ? "input_$id" : 'input_' . $form_id . "_$id";

		$value        = sanitize_text_field( stripslashes( esc_attr( $value ) ) );
		$size         = $this->size;
		$class_suffix = $is_entry_detail ? '_admin' : '';
		$class        = $size . $class_suffix;
		$class        = esc_attr( $class );

		$max_length = is_numeric( $this->maxLength ) ? "maxlength='{$this->maxLength}'" : '';

		$tabindex              = $this->get_tabindex();
		$disabled_text         = $is_form_editor ? 'disabled="disabled"' : '';
		$placeholder_attribute = $this->get_field_placeholder_attribute();
		$required_attribute    = $this->isRequired ? 'aria-required="true"' : '';
		$invalid_attribute     = $this->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';
		$aria_describedby      = $this->get_aria_describedby();
		$autocomplete          = $this->enableAutocomplete ? $this->get_field_autocomplete_attribute() : '';

		$gfgeo_id       = esc_attr( $this->gfgeo_id );
		$geocoder_id    = ! empty( $this->gfgeo_geocoder_id ) ? esc_attr( $this->gfgeo_geocoder_id ) : '';
		$locator_button = ! empty( $this->gfgeo_infield_locator_button ) ? '1' : '';
		$locator_bounds = ( ! empty( $this->gfgeo_autocomplete_restriction_usage ) && 'page_locator' === $this->gfgeo_autocomplete_restriction_usage && ! empty( $this->gfgeo_address_autocomplete_locator_bounds ) ) ? 1 : 0;

		// For Post Tags, Use the WordPress built-in class "howto" in the form editor.
		$text_hint = '';

		if ( 'post_tags' === $this->type ) {
			$text_hint_class = $is_form_editor ? 'howto' : 'gfield_post_tags_hint';
			$text_hint       = '<p class="' . $text_hint_class . '" id="' . $field_id . '_desc">' . gf_apply_filters(
				array(
					'gform_post_tags_hint',
					$form_id,
					$this->id,
				),
				esc_html__( 'Separate tags with commas', 'gravityforms' ),
				$form_id
			) . '</p>';
		}

		$input = "<input name='input_{$id}' id='{$field_id}' type='text' value='{$value}' class='gfgeo-address-field {$class}' {$max_length} {$aria_describedby} {$tabindex} {$placeholder_attribute} {$required_attribute} {$invalid_attribute} {$disabled_text} {$autocomplete} data-field_id='{$gfgeo_id}' data-geocoder_id='{$geocoder_id}' /> {$text_hint}";

		$locator_class = '';

		if ( ! empty( $this->gfgeo_infield_locator_button ) ) {

			$locator_class = 'gfgeo-address-locator-wrapper';

			$input .= GFGEO_Helper::get_locator_button( $form_id, $this, 'infield' );
		}

		return sprintf( "<div id='gfgeo-address-locator-wrapper-%s' class='ginput_container ginput_container_gfgeo_address %s'>%s</div>", $gfgeo_id, $locator_class, $input );
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

		// No link if passing via querystring.
		if ( ! empty( $form['confirmation']['type'] ) && 'message' === $form['confirmation']['type'] ) {

			$this->google_map_link = apply_filters( 'gfgeo_full_address_field_output_map_link', $this->google_map_link, $this );

			if ( $this->google_map_link['merge_tag'] ) {
				$address .= GFGEO_Helper::get_map_link( $address );
			}
		}

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

		$this->google_map_link = apply_filters( 'gfgeo_full_address_field_output_map_link', $this->google_map_link );

		if ( $this->google_map_link['entry_list'] ) {
			$address .= '<br /> ' . GFGEO_Helper::get_map_link( $address );
		}

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

		$this->google_map_link = apply_filters( 'gfgeo_full_address_field_output_map_link', $this->google_map_link );

		if ( $this->google_map_link['entry_detail'] ) {
			$address .= ' - ' . GFGEO_Helper::get_map_link( $address );
		}

		return $address;
	}
}
GF_Fields::register( new GFGEO_Full_Address_Field() );
