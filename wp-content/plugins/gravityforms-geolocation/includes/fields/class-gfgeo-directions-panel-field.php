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
			'size_settings',
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

			$content  = '<div class="gfgeo-admin-hidden-container">';
			$content .= '<p>' . __( 'This field will be hidden when the form first load in the front-end and will be dynamically generated with the directions data when available.', 'gfgeo' ) . '</p>';
			$content .= '</div>';

			return $content;

			// Front-end form.
		} else {

			$form_id         = absint( $form['id'] );
			$is_entry_detail = $this->is_entry_detail();
			$is_form_editor  = $this->is_form_editor();

			$id       = (int) $this->id;
			$field_id = $is_entry_detail || $is_form_editor || 0 === $form_id ? "input_$id" : 'input_' . $form_id . "_$id";
			$gfgeo_id = ! empty( $this->gfgeo_id ) ? esc_attr( $this->gfgeo_id ) : $form_id . '_' . $id;
			$size     = $this->size;

			$input = "<input type='hidden' name='input_{$id}' id='{$field_id}' class='gfgeo-directions-panel-field-value' data-field_id='{$gfgeo_id}' value='' />";

			return sprintf( "<div id='gfgeo-directions-panel-holder-%s' class='ginput_container ginput_container_gfgeo_directions_panel gfgeo-directions-panel-holder {$size}'>%s</div>", $gfgeo_id, $input );
		}
	}

	/**
	 * Save the directions data in the entry.
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

		if ( empty( $value ) ) {
			return $value;
		}

		// Sanitize value.
		$value = $this->sanitize_entry_value( $value, $form['id'] );

		// This should be a JSON encoded array value.
		$value = json_decode( stripslashes( $value ), true );

		if ( empty( $value ) || ! is_array( $value ) ) {
			return '';
		}

		return maybe_serialize( $value );
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
		return GFGEO_Helper::get_directions_link( $value );
	}

	/**
	 * Generate directions link for notifications when using merge tags.
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
		return GFGEO_Helper::get_directions_link( $raw_value );
	}

	/**
	 * Display the directions link in the entry page.
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
		return GFGEO_Helper::get_directions_link( $value );
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
	/*public function get_value_export( $entry, $input_id = '', $use_text = false, $is_csv = false ) {
		return $value;
	}*/
}
GF_Fields::register( new GFGEO_Directions_Panel_Field() );
