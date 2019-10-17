<?php
/**
 * Gravity Forms Geolocation locator button field.
 *
 * @package gravityforms-geolocation.
 */

if ( ! class_exists( 'GFForms' ) ) {
	die(); // abort if accessed directly.
}

/**
 * Register Locator button
 *
 * @since  2.0
 */
class GFGEO_Locator_Button_Field extends GF_Field {

	/**
	 * Field type
	 *
	 * @var string
	 */
	public $type = 'gfgeo_locator_button';

	/**
	 * Field button
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'gfgeo_geolocation_fields',
			'text'  => __( 'Locator Button', 'gfgeo' ),
		);
	}

	/**
	 * Field title
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_field_title() {
		return __( 'Locator Button', 'gfgeo' );
	}

	/**
	 * Field settings.
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_field_settings() {
		return array(
			// ggf options.
			'gfgeo-location-found-message',
			'gfgeo-hide-location-failed-message',
			'gfgeo-geocoder-id',
			'gfgeo-locator-button-label',
			// gform options.
			'gfgeo-ip-locator-status',
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

		// get the button element.
		$input = GFGEO_Helper::get_locator_button( $form['id'], $this, 'button' );

		return sprintf( "<div class='ginput_container ginput_container_gfgeo_locator_button'>%s</div>", $input );
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
GF_Fields::register( new GFGEO_Locator_Button_Field() );
