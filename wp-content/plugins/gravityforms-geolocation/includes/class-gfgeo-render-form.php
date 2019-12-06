<?php
/**
 * Gravity Geolocation render form class.
 *
 * @author Eyal Fitoussi.
 *
 * @package gravityforms-geolocation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * GFGEO_Render_Form class
 *
 * The class responsible for the modification of the form and fields in the front-end.
 *
 * @author Fitoussi Eyal
 */
class GFGEO_Render_Form {

	/**
	 * Forms collector to pass to JavaScript
	 *
	 * @var array
	 */
	public static $gforms = array();

	/**
	 * Post ID of the post being updated
	 * When using Gravity Forms Update post plugin
	 *
	 * @since   2.0
	 * @var     String
	 */
	public static $update_post_id = 0;

	/**
	 * __constructor
	 */
	public function __construct() {

		// When using Gravity Forms Update Post plugin.
		if ( class_exists( 'gform_update_post' ) ) {

			// Look for post ID in URL.
			if ( ! empty( $_GET['gform_post_id'] ) ) {

				self::$update_post_id = absint( wp_unslash( $_GET['gform_post_id'] ) ); // WPCS: CSRF ok.

				// otherwise look in shortcode/link.
			} else {

				add_filter( 'gform_update_post/setup_form', array( $this, 'get_updated_post_id' ), 10 );
			}
		}

		// Modify the form before it is being displayed.
		add_filter( 'gform_pre_render', array( $this, 'render_form' ) );

		// modify the advanced address field.
		add_filter( 'gform_field_content', array( $this, 'modify_advanced_address_field' ), 10, 5 );
	}

	/**
	 * Update post ID.
	 *
	 * Get the post ID of the post being updated when
	 * updating form using Gravity Form Update Post plugin.
	 *
	 * @param array $args post args.
	 */
	public function get_updated_post_id( $args ) {

		if ( is_array( $args ) ) {

			// get post id from shortcode "update" attibute.
			if ( ! empty( $args['post_id'] ) ) {

				self::$update_post_id = $args['post_id'];

				// get post ID of the post being displayed.
			} elseif ( ! empty( $GLOBALS['post'] ) ) {

				self::$update_post_id = $GLOBALS['post']->ID;
			}

			// get post ID from URL.
		} elseif ( ! empty( $args ) ) {

			self::$update_post_id = $args;
		}
	}

	/**
	 * Execute function on form load.
	 *
	 * @param array $form the processed form.
	 *
	 * @return unknown|string
	 */
	public function render_form( $form ) {

		if ( empty( $form['fields'] ) || ! is_array( $form['fields'] ) ) {
			$form['fields'] = array();
		}

		$geo_fields_enabled = false;

		foreach ( $form['fields'] as $key => $field ) {

			$geocoder_id = ! empty( $field['gfgeo_geocoder_id'] ) ? esc_attr( $form['id'] . '_' . $field['gfgeo_geocoder_id'] ) : '';

			if ( 'gfgeo_address' === $field['type'] ) {

				$geo_fields_enabled = true;

				// look for saved value in custom field if updating post.
				if ( empty( $_POST[ 'is_submit_' . $form['id'] ] ) && ! empty( self::$update_post_id ) && ! empty( $field->postCustomFieldName ) ) { // WPCS: CSRF ok.

					$saved_address = get_post_meta( self::$update_post_id, $field->postCustomFieldName, true );

					$form['fields'][ $key ]['defaultValue'] = $saved_address;
				}
			}

			if ( 'gfgeo_coordinates' === $field['type'] ) {

				$geo_fields_enabled = true;

				// look for saved value in custom field if updating post.
				if ( empty( $_POST[ 'is_submit_' . $form['id'] ] ) && ! empty( self::$update_post_id ) && ! empty( $field->postCustomFieldName ) ) {

					$saved_coords = get_post_meta( self::$update_post_id, $field->postCustomFieldName, true );

					$form['fields'][ $key ]['defaultValue'] = maybe_unserialize( $saved_coords );
				}
			}

			// add class to advanced address field.
			if ( 'address' === $field['type'] ) {

				if ( ! empty( $field['gfgeo_geocoder_id'] ) ) {

					$geo_fields_enabled = true;

					$form['fields'][ $key ]['cssClass'] .= ' gfgeo-advanced-address gfgeo-advanced-address-geocoder-id-' . $geocoder_id;
				}

				if ( empty( $_POST[ 'is_submit_' . $form['id'] ] ) && ! empty( self::$update_post_id ) && ! empty( $field->postCustomFieldName ) ) { // WPCS: CSRF ok.

					$new_inputs = $form['fields'][ $key ]['inputs'];

					$address = get_post_meta( self::$update_post_id, $field->postCustomFieldName, true );

					$new_inputs[0]['defaultValue'] = ! empty( $address[ $field['id'] . '.1' ] ) ? sanitize_text_field( stripslashes( $address[ $field['id'] . '.1' ] ) ) : '';

					$new_inputs[1]['defaultValue'] = ! empty( $address[ $field['id'] . '.2' ] ) ? sanitize_text_field( stripslashes( $address[ $field['id'] . '.2' ] ) ) : '';

					$new_inputs[2]['defaultValue'] = ! empty( $address[ $field['id'] . '.3' ] ) ? sanitize_text_field( stripslashes( $address[ $field['id'] . '.3' ] ) ) : '';

					$new_inputs[3]['defaultValue'] = ! empty( $address[ $field['id'] . '.4' ] ) ? sanitize_text_field( stripslashes( $address[ $field['id'] . '.4' ] ) ) : '';

					$new_inputs[4]['defaultValue'] = ! empty( $address[ $field['id'] . '.5' ] ) ? sanitize_text_field( stripslashes( $address[ $field['id'] . '.5' ] ) ) : '';

					$new_inputs[5]['defaultValue'] = ! empty( $address[ $field['id'] . '.6' ] ) ? sanitize_text_field( stripslashes( $address[ $field['id'] . '.6' ] ) ) : '';

					$form['fields'][ $key ]['inputs'] = $new_inputs;
				}
			}

			// add class to geocoder fields.
			if ( 'gfgeo_geocoder' === $field['type'] ) {

				$geo_fields_enabled = true;

				// clear label.
				$field['label'] = '';

				$form['fields'][ $key ]['cssClass'] .= ' gfgeo-hidden-fields ' . $field['type'];
			}

			// add class to geocoder fields.
			if ( 'gfgeo_directions_panel' === $field['type'] ) {

				$form['fields'][ $key ]['cssClass'] .= ' gfgeo-hidden-fields ' . $field['type'];
			}

			// add class to dynamic fields.
			if ( ! empty( $field['gfgeo_geocoder_id'] ) && ! empty( $field['gfgeo_dynamic_location_field'] ) ) {
				$form['fields'][ $key ]['cssClass'] .= ' gfgeo-geocoded-field-' . $geocoder_id . ' gfgeo-' . $field['gfgeo_dynamic_location_field'];
			}

			if ( 'gfgeo_locator_button' === $field['type'] || 'gfgeo_map' === $field['type'] ) {
				$geo_fields_enabled = true;
			}
		}

		// set some form settings.
		$form['gfgeo_page_load']   = false;
		$form['gfgeo_form_update'] = false;
		$form['gfgeo_submitted']   = ! empty( $_POST['gform_submit'] ) ? true : false; // WPCS: CSRF ok.
		$form['gfgeo_post_update'] = ! empty( self::$update_post_id ) ? true : false;
		$form['gfgeo_user_update'] = GFGEO_Helper::is_update_user_form( $form['id'] ) ? true : false;
		$form['gfgeo_is_mobile']   = wp_is_mobile() ? true : false;

		// trigger page locator only if form is no update form of any sort or after submission.
		if ( ! $form['gfgeo_submitted'] || ( $form['gfgeo_submitted'] && ! empty( $_POST[ 'gform_target_page_number_' . $form['id'] ] ) ) ) { // WPCS: CSRF ok.

			if ( $form['gfgeo_user_update'] || $form['gfgeo_post_update'] ) {

				$form['gfgeo_form_update'] = true;

				// Allow page locator on update forms.
				$force_enable_locator = apply_filters( 'gfgeo_enable_page_locator_on_update_forms', array() );

				if ( ! empty( $force_enable_locator ) ) {

					if ( in_array( 'user', $force_enable_locator, true ) && $form['gfgeo_user_update'] ) {
						$form['gfgeo_page_load'] = true;
					}

					if ( in_array( 'post', $force_enable_locator, true ) && $form['gfgeo_post_update'] ) {
						$form['gfgeo_page_load'] = true;
					}
				}

				// if form is not loaded from saved and continue.
			} elseif ( empty( $_GET['gf_token'] ) ) { // WPCS: CSRF ok.
				$form['gfgeo_page_load'] = true;
			}
		}

		// collect forms data.
		self::$gforms[ $form['id'] ] = $form;

				// collect forms data.
		self::$gforms[ $form['id'] ] = $form;

		if ( $geo_fields_enabled ) {
			add_action( 'gform_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		return $form;
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function enqueue_scripts() {

		// enqueue scripts.
		if ( ! wp_script_is( 'google-maps', 'enqueued' ) ) {
			wp_enqueue_script( 'google-maps' );
		}

		if ( ! wp_script_is( 'gfgeo', 'enqueued' ) ) {
			wp_enqueue_script( 'gfgeo' );
		}

		if ( ! wp_style_is( 'gfgeo', 'enqueued' ) ) {
			wp_enqueue_style( 'gfgeo' );
		}

		if ( 'maxmind' == GFGEO_IP_LOCATOR && ! wp_script_is( 'gfgeo-maxmind', 'enqueued' ) ) {
			wp_enqueue_script( 'gfgeo-maxmind' );
		}

		// localize plugin's options.
		$plugin_options = apply_filters(
			'gfgeo_render_form_options',
			array(
				'field_autocomplete'           => apply_filters( 'gfgeo_enable_address_field_autocomplete_attr', true, self::$gforms ) ? '1' : '0',
				'protocol'                     => is_ssl() ? 'https' : 'http',
				'country_code'                 => GFGEO_GOOGLE_MAPS_COUNTRY,
				'language_code'                => GFGEO_GOOGLE_MAPS_LANGUAGE,
				'high_accuracy'                => GFGEO_HIGH_ACCURACY_MODE,
				'ip_locator'                   => GFGEO_IP_LOCATOR,
				'ip_token'                     => GFGEO_IP_TOKEN,
				'address_field_event_triggers' => 'keydown focusout',
				'hide_error_messages'          => false,
			)
		);

		// pass data to JavaScript.
		wp_localize_script( 'gfgeo', 'gfgeo_options', $plugin_options );
		wp_localize_script( 'gfgeo', 'gfgeo_gforms', self::$gforms );
	}

	/**
	 * Modify the advanced address field and append the autocompelte field.
	 *
	 * @param  mixed   $content field content.
	 * @param  object  $field   field object.
	 * @param  mixed   $value   field value.
	 * @param  integer $lead_id entry ID.
	 * @param  integer $form_id form ID.
	 *
	 * @return [type]          [description]
	 */
	public function modify_advanced_address_field( $content, $field, $value, $lead_id, $form_id ) {

		if ( 'address' !== $field->type ) {
			return $content;
		}

		$geocoder_id         = ! empty( $field->gfgeo_geocoder_id ) ? esc_attr( $form_id . '_' . $field->gfgeo_geocoder_id ) : '';
		$autocomplete        = ! empty( $field->gfgeo_address_autocomplete ) ? '1' : '';
		$autocomplete_types  = ! empty( $field->gfgeo_address_autocomplete_types ) ? esc_attr( $field->gfgeo_address_autocomplete_types ) : '';
		$autocomplete_bounds = ! empty( $field->gfgeo_address_autocomplete_bounds ) ? esc_attr( $field->gfgeo_address_autocomplete_bounds ) : '';
		$locator_bounds      = ! empty( $field->gfgeo_address_autocomplete_locator_bounds ) ? '1' : '';
		$country             = ! empty( $field->gfgeo_address_autocomplete_country ) ? $field->gfgeo_address_autocomplete_country : '';
		$country             = is_array( $country ) ? implode( ',', $country ) : $country;
		$locator             = ! empty( $field->gfgeo_infield_locator_button ) ? '1' : '';
		$placeholder         = ! empty( $field->gfgeo_address_autocomplete_placeholder ) ? esc_attr( $field->gfgeo_address_autocomplete_placeholder ) : '';
		$description         = ! empty( $field->gfgeo_address_autocomplete_desc ) ? esc_attr( $field->gfgeo_address_autocomplete_desc ) : '';
		$field_id            = esc_attr( $form_id . '_' . $field->id );

		$output = '';

		if ( ! empty( $field->gfgeo_address_autocomplete ) ) {

			// modify description position.
			$desc_top = apply_filters( 'gfgeo_advanced_address_description_top', false );

			if ( $desc_top ) {
				$desc_class = 'desc-top';
			} else {
				$desc_class = 'desc-bottom';
			}

			// desc field.
			$desc_field = '<label for="gfgeo-advanced-address-autocomplete-wrapper-' . $field_id . '">' . $description . '</label>';

			$output .= '<div id="gfgeo-advanced-address-autocomplete-wrapper-' . $field_id . '" class="ginput_full address_autocomplete ' . $desc_class . '" style="display:none;position:relative;">';

			// description above autocomplete field.
			if ( $desc_top ) {
				$output .= $desc_field;
			}

			// inner wrapper.
			$output .= '<div class="gfgeo-advanced-address-autocomplete-inner">';

			$output .= '<input id="input_' . $field_id . '_advanced_address_autocomplete" type="text" data-address_field_id="' . $field_id . '" data-geocoder_id="' . $geocoder_id . '" data-address_autocomplete="' . $autocomplete . '" data-autocomplete_types="' . $autocomplete_types . '" data-autocomplete_bounds="' . $autocomplete_bounds . '" data-autocomplete_locator_bounds="' . $locator_bounds . '" data-autocomplete_country="' . esc_attr( $country ) . '" data-locator_enabled="' . $locator . '" class="advanced-address-autocomplete" placeholder="' . $placeholder . '" />';

			if ( ! empty( $field->gfgeo_infield_locator_button ) ) {
				$output .= GFGEO_Helper::get_locator_button( $form_id, $field, 'infield' );
			}

			$output .= '</div>';

			// description below autocomplete field.
			if ( ! $desc_top ) {
				$output .= $desc_field;
			}

			$output .= '</div>';
		}

		$output .= '<div id="gfgeo-advanced-address-' . $field_id . '-geocoder-id" class="gfgeo-advanced-address-geocoder-id" data-geocoder_id="' . $geocoder_id . '" style="display:none" ></div>';
		$content = $output . $content;

		return $content;
	}
}
$gfge_render_form = new GFGEO_Render_Form();
