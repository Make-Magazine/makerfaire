<?php
/**
 * Gravity Forms Geolocation Directions field.
 *
 * @author Eyal Fitoussi - fitoussi_eya@hotmail.com
 *
 * @since   3.0
 *
 * @package gravityforms-geolocation.
 */

if ( ! class_exists( 'GFForms' ) ) {
	die(); // abort if accessed directly.
}

/**
 * Register Directions Field
 *
 * @since  3.0
 */
class GFGEO_Directions_Field extends GF_Field {

	/**
	 * Field type
	 *
	 * @var string
	 */
	public $type = 'gfgeo_directions';

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
			'text'  => __( 'Directions', 'gfgeo' ),
		);
	}

	/**
	 * Field label.
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_field_title() {
		return __( 'Directions, Distance, and Routes', 'gfgeo' );
	}

	/**
	 * Field settings.
	 *
	 * @return [type] [description]
	 */
	public function get_form_editor_field_settings() {
		return array(
			'gfgeo-directions-field-settings-group',
			'conditional_logic_field_setting',
			'post_custom_field_setting',
			'label_setting',
			'label_placement_setting',
			'admin_label_setting',
			'size_setting',
			'visibility_setting',
			'description_setting',
			'css_class_setting',
			'rules_setting',
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
	 * Field input
	 *
	 * @param  [type] $form  [description].
	 * @param  string $value [description].
	 * @param  [type] $entry [description].
	 *
	 * @return [type]        [description]
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {

		$trigger_dynamically = false;
		$trigger_class       = '';

		if ( empty( $this->gfgeo_trigger_directions_method ) || 'dynamically' === $this->gfgeo_trigger_directions_method ) {

			if ( $this->is_form_editor() ) {

				$content  = '<div class="gfgeo-admin-hidden-container">';
				$content .= '<p>' . __( 'This field will be hidden in the front-end form when the "Directions Trigger Method" select dropdown is set to "Dynamically"', 'gfgeo' ) . '</p>';
				$content .= '</div>';

				return $content;
			}

			$trigger_dynamically = true;
			$trigger_class       = 'trigger-dynamically';
		}

		$form_id         = absint( $form['id'] );
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();

		$id       = (int) $this->id;
		$field_id = $is_entry_detail || $is_form_editor || 0 === $form_id ? "input_$id" : 'input_' . $form_id . "_$id";

		$size               = $this->size;
		$class_suffix       = $is_entry_detail ? '_admin' : '';
		$required_attribute = $this->isRequired ? 'aria-required="true"' : '';
		$aria_describedby   = $this->get_aria_describedby();

		$geocoder_id = ! empty( $this->gfgeo_geocoder_id ) ? esc_attr( $this->gfgeo_geocoder_id ) : '';
		$gfgeo_id    = ! empty( $this->gfgeo_id ) ? esc_attr( $this->gfgeo_id ) : $form_id . '_' . $id;

		// Front-end form.
		$gfgeo_id    = ! empty( $this->gfgeo_id ) ? esc_attr( $this->gfgeo_id ) : esc_attr( $form['id'] . '_' . $this->id );
		$field_usage = ! empty( $this->gfgeo_directions_field_usage ) ? esc_attr( $this->gfgeo_directions_field_usage ) : 'driving_directions';

		$input = '';

		if ( ! $trigger_dynamically ) {

			$button_label = ! empty( $this->gfgeo_get_directions_button_label ) ? esc_attr( $this->gfgeo_get_directions_button_label ) : '';

			// Get Directions button.
			$input .= "<span id='{$field_id}_get_directions_button_container' class='gfgeo-get-directions-button-wrapper ginput_left'>";
			$input .= "<input type='button' id='gfgeo-get-directions-button-{$gfgeo_id}' class='gfgeo-get-directions-button gfgeo-form-button' value='{$button_label}' data-field_id='{$gfgeo_id}' {$aria_describedby} />";
			$input .= '</span>';

			if ( ! empty( $this->gfgeo_clear_directions_button_label ) ) {

				$button_label = esc_attr( $this->gfgeo_clear_directions_button_label );

				// Get Directions button.
				$input .= "<span id='{$field_id}_clear_directions_button_container' class='gfgeo-clear-directions-button-wrapper ginput_right'>";
				$input .= "<input type='button' id='gfgeo-clear-directions-button-{$gfgeo_id}' class='gfgeo-clear-directions-button gfgeo-form-button' value='{$button_label}' data-field_id='{$gfgeo_id}' {$aria_describedby} />";
				$input .= '</span>';
			}

			$input .= "<input type='hidden' id='{$field_id}_triggered' class='gfgeo-directions-field-triggered' data-field_id='{$gfgeo_id}' value='{$value}' />";
		}

		$value = ! empty( $value ) ? sanitize_text_field( $value ) : '';

		$input .= "<input type='hidden' name='input_{$id}' id='{$field_id}' class='gfgeo-directions-field-value' data-field_id='{$gfgeo_id}' value='{$value}' {$required_attribute} />";

		return sprintf( "<div id='gfgeo-directions-field-wrapper-%s' class='ginput_complex{$class_suffix} ginput_container {$size} ginput_container_gfgeo_directions gfgeo_complex gfgeo-directions-field-wrapper {$trigger_class} {$field_usage}' data-trigger_dynamically='%s' data-field_usage='%s'>%s</div>", $gfgeo_id, $trigger_dynamically, $field_usage, $input );
	}

	/**
	 * Save entry's field value on form submission.
	 *
	 * @param  [type] $value      [description].
	 *
	 * @param  [type] $form       [description].
	 *
	 * @param  [type] $input_name [description].
	 *
	 * @param  [type] $entry_id   [description].
	 *
	 * @param  [type] $entry      [description].
	 *
	 * @return [type]             serialized array.
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
	 * Display Directions details in notifications when using merge tags.
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

		if ( empty( $raw_value ) ) {
			return __( 'No data is available', 'gfgeo' );
		}

		return GFGEO_Helper::get_directions_details_output( $raw_value );
	}

	/**
	 * Display data in entry list page.
	 *
	 * @param  [type] $value [description].
	 * @param  [type] $entry         [description].
	 * @param  [type] $field_id      [description].
	 * @param  [type] $columns       [description].
	 * @param  [type] $form          [description].
	 *
	 * @return [type]                [description]
	 */
	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {

		if ( empty( $value ) ) {
			return '';
		}

		return __( 'See data in entry page', 'gfgeo' );
	}

	/**
	 * Display directions details in entry page.
	 *
	 * @param  [type]  $value [description].
	 * @param  string  $currency      [description].
	 * @param  boolean $use_text      [description].
	 * @param  string  $format        [description].
	 * @param  string  $media         [description].
	 *
	 * @return [type]                 [description]
	 */
	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {

		if ( empty( $value ) || 'text' === $format ) {
			return $value;
		}

		return GFGEO_Helper::get_directions_details_output( $value );
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

		$format = apply_filters( 'gfgeo_directions_field_export_format', 'serialized', $value, $entry, $input_id, $use_text, $is_csv );

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

			$value = GFGEO_Helper::get_directions_details_output( $value );
		}

		return $value;
	}
}
GF_Fields::register( new GFGEO_Directions_Field() );
