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

		// When updating an entry via Gravity View.
		if ( ! empty( $_GET['gvid'] ) && ! empty( $_GET['edit'] ) ) {
			self::$update_post_id = absint( wp_unslash( $_GET['gvid'] ) ); // WPCS: CSRF ok.
		}

		// Modify the form before it is being displayed.
		add_filter( 'gform_pre_render', array( $this, 'render_form' ) );
		add_filter( 'gform_admin_pre_render', array( $this, 'admin_render_form' ) );

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
	 * Modify form object when in the Edit Entry page.
	 *
	 * @param  [type] $form [description].
	 *
	 * @return [type]       [description]
	 */
	public function admin_render_form( $form ) {

		// Disable the CSS cleaner.
		if ( ! apply_filters( 'gfgeo_disable_css_class_cleaner', false ) ) {

			if ( GFCommon::is_form_editor() ) {

				foreach ( $form['fields'] as $key => $field ) {

					// Clear CSS classes that were added to the field's CSS Class by accident.
					if ( strpos( $form['fields'][ $key ]['cssClass'], 'gfgeo' ) !== false ) {
						$form['fields'][ $key ]['cssClass'] = preg_replace( '/gfgeo-([\S]+)/', '', $form['fields'][ $key ]['cssClass'] );
						$form['fields'][ $key ]['cssClass'] = preg_replace( '/field_type_gfgeo([\S]+)/', '', $form['fields'][ $key ]['cssClass'] );
					}

					if ( 'address' === $field['type'] ) {
						$form['fields'][ $key ]['cssClass'] = str_replace( 'field_type_address', '', $form['fields'][ $key ]['cssClass'] );
					}

					if ( 'gfgeo_geocoder' === $field['type'] ) {
						$form['fields'][ $key ]['visibility'] = 'hidden';
					}
				}

				return $form;
			}
		}

		return GFCommon::is_entry_detail_edit() ? $this->render_form( $form ) : $form;
	}

	/**
	 * Execute function on form load.
	 *
	 * @param array $form the processed form.
	 *
	 * @return unknown|string
	 */
	public function render_form( $form ) {

		if ( ! empty( self::$gforms[ $form['id'] ] ) ) {

			if ( ! empty( self::$gforms[ $form['id'] ]['gfgeo_args'] ) && empty( $form['gfgeo_args'] ) ) {
				$form['gfgeo_args'] = self::$gforms[ $form['id'] ]['gfgeo_args'];
			}

			return $form;
		}

		// No need to load during confirmation.
		if ( empty( $form['fields'] ) || ! is_array( $form['fields'] ) ) {
			$form['fields'] = array();
		}

		$form_id            = absint( $form['id'] );
		$geo_fields_enabled = false;
		$geolocation_fields = array(
			'gfgeo_geocoder',
			'gfgeo_address',
			'gfgeo_coordinates',
			'gfgeo_map',
			'gfgeo_directions',
			'gfgeo_directions_panel',
			'gfgeo_locator_button',
			'gfgeo_reset_location_button',
		);

		// Init some variables.
		$is_edit_entry  = false;
		$is_page_load   = false;
		$is_form_update = false;
		$is_submitted   = false;
		$is_post_update = false;
		$is_user_update = false;

		// If on edit entry page.
		if ( GFCommon::is_entry_detail_edit() ) {

			$is_edit_entry = true;

		} else {

			$is_submitted   = ! empty( $_POST[ 'is_submit_' . $form_id ] ) ? true : false; // WPCS: CSRF ok.
			$is_post_update = ! empty( self::$update_post_id ) ? true : false;
			$is_user_update = GFGEO_Helper::is_update_user_form( $form_id ) ? true : false;

			// trigger page locator only if form is no update form of any sort or after submission.
			if ( ! $is_submitted || ( $is_submitted && ! empty( $_POST[ 'gform_target_page_number_' . $form_id ] ) ) ) { // WPCS: CSRF ok.

				if ( $is_user_update || $is_post_update ) {

					$is_form_update = true;

					// Allow page locator on update forms.
					$force_enable_locator = apply_filters( 'gfgeo_enable_page_locator_on_update_forms', array() );

					if ( ! empty( $force_enable_locator ) ) {

						if ( in_array( 'user', $force_enable_locator, true ) && $is_user_update ) {
							$is_page_load = true;
						}

						if ( in_array( 'post', $force_enable_locator, true ) && $is_post_update ) {
							$is_page_load = true;
						}
					}

					// if form is not loaded from saved and continue.
				} elseif ( empty( $_GET['gf_token'] ) ) { // WPCS: CSRF ok.
					$is_page_load = true;
				}
			}
		}

		// Collect geocoders that are set to calculate the distance ( from previous version of the plugin ).
		// To be removed in the future.
		$form['geocoder_directions_fields'] = array();
		$form['geolocation_fields']         = array();
		$form['directions_fields']          = array();

		// Loop through fields, collect some data and do some tasks.
		foreach ( $form['fields'] as $key => $field ) {

			$field_id = absint( $field['id'] );

			// Collect the field to then pass to Javascript.
			$collect_field = false;

			// Check if we need to look for default value in custom field ( when updating a post ).
			$get_post_meta = ( ! $is_submitted && $is_post_update && ! empty( $field->postCustomFieldName ) ) ? true : false;

			// Field ID made up of form ID and Field ID for geolocation usage.
			$gfgeo_id = $form_id . '_' . $field_id;

			// Hide geocoder field.
			if ( 'gfgeo_geocoder' === $field['type'] ) {

				$form['fields'][ $key ]['visibility'] = 'hidden';

				if ( ! empty( $field['gfgeo_distance_destination_geocoder_id'] ) ) {

					$destination_geocoder = $form_id . '_' . $field['gfgeo_distance_destination_geocoder_id'];

					// Collect directions field.
					$form['geocoder_directions_fields'][ $gfgeo_id ]                       = $field;
					$form['geocoder_directions_fields'][ $gfgeo_id ]['gfgeo_id']           = $gfgeo_id;
					$form['geocoder_directions_fields'][ $gfgeo_id ]['gfgeo_route_map_id'] = '';

					$form['geocoder_directions_fields'][ $gfgeo_id ]['gfgeo_distance_destination_geocoder_id'] = $destination_geocoder;
					$form['geocoder_directions_fields'][ $gfgeo_id ]['total_geocoders']                        = array( $gfgeo_id, $destination_geocoder );

					// If map enabled, we need to find the map that is synced with this geocoder.
					if ( ! empty( $field['gfgeo_distance_travel_show_route_on_map'] ) ) {

						foreach ( $form['fields'] as $fv ) {

							if ( 'gfgeo_map' === $fv['type'] && ! empty( $fv['gfgeo_geocoder_id'] ) && ! is_array( $fv['gfgeo_geocoder_id'] ) && $fv['gfgeo_geocoder_id'] == $field['id'] ) {
								$form['geocoder_directions_fields'][ $gfgeo_id ]['gfgeo_route_map_id'] = $form_id . '_' . $fv['id'];
							}
						}
					}

					if ( ! empty( $field['gfgeo_distance_directions_panel_id'] ) ) {
						$field['gfgeo_distance_directions_panel_id'] = $form_id . '_' . $field['gfgeo_distance_directions_panel_id'];
					}
				}
			}

			// Generate geocoder ID based on form ID and field ID.
			if ( isset( $field['gfgeo_geocoder_id'] ) ) {

				if ( ! empty( $field['gfgeo_geocoder_id'] ) ) {

					$field['gfgeo_geocoder_id'] = is_array( $field['gfgeo_geocoder_id'] ) ? preg_filter( '/^/', $form_id . '_', $field['gfgeo_geocoder_id'] ) : esc_attr( $form_id . '_' . $field['gfgeo_geocoder_id'] );

				} else {
					$field['gfgeo_geocoder_id'] = '';
				}
			}

			// Set default value from custom field if updating a post.
			if ( $get_post_meta && in_array( $field['type'], array( 'gfgeo_geocoder', 'gfgeo_address', 'gfgeo_coordinates', 'gfgeo_directions' ), true ) ) {

				$saved_value = get_post_meta( self::$update_post_id, sanitize_key( $field->postCustomFieldName ), true );

				if ( ! empty( $saved_value ) ) {
					$saved_value = is_array( $saved_value ) ? maybe_serialize( $saved_value ) : $saved_value;
				} else {
					$saved_value = '';
				}

				$form['fields'][ $key ]['defaultValue'] = $saved_value;
			}

			// Advanced address field tasks.
			if ( 'address' === $field['type'] ) {

				// Add classes to field.
				if ( ! empty( $field['gfgeo_geocoder_id'] ) ) {
					$form['fields'][ $key ]['cssClass'] .= ' gfgeo-advanced-address gfgeo-advanced-address-geocoder-id-' . $field['gfgeo_geocoder_id'];
				}

				// populate field value saved in custom field.
				if ( $get_post_meta ) { // WPCS: CSRF ok.

					$new_inputs = $form['fields'][ $key ]['inputs'];
					$address    = get_post_meta( self::$update_post_id, $field->postCustomFieldName, true );

					$new_inputs[0]['defaultValue'] = ! empty( $address[ $field_id . '.1' ] ) ? sanitize_text_field( stripslashes( $address[ $field_id . '.1' ] ) ) : '';
					$new_inputs[1]['defaultValue'] = ! empty( $address[ $field_id . '.2' ] ) ? sanitize_text_field( stripslashes( $address[ $field_id . '.2' ] ) ) : '';
					$new_inputs[2]['defaultValue'] = ! empty( $address[ $field_id . '.3' ] ) ? sanitize_text_field( stripslashes( $address[ $field_id . '.3' ] ) ) : '';
					$new_inputs[3]['defaultValue'] = ! empty( $address[ $field_id . '.4' ] ) ? sanitize_text_field( stripslashes( $address[ $field_id . '.4' ] ) ) : '';
					$new_inputs[4]['defaultValue'] = ! empty( $address[ $field_id . '.5' ] ) ? sanitize_text_field( stripslashes( $address[ $field_id . '.5' ] ) ) : '';
					$new_inputs[5]['defaultValue'] = ! empty( $address[ $field_id . '.6' ] ) ? sanitize_text_field( stripslashes( $address[ $field_id . '.6' ] ) ) : '';

					$form['fields'][ $key ]['inputs'] = $new_inputs;
				}
			}

			// Directions field tasks.
			if ( 'gfgeo_directions' === $field['type'] ) {

				// Hide field if dynamically triggered.
				$form['fields'][ $key ]['visibility'] = ( empty( $field['gfgeo_trigger_directions_method'] ) || 'dynamically' === $field['gfgeo_trigger_directions_method'] ) ? 'hidden' : 'visible';

				$convert_options = array(
					'gfgeo_origin_geocoder_id',
					'gfgeo_destination_geocoder_id',
					'gfgeo_waypoints_geocoders',
					'gfgeo_route_map_id',
					'gfgeo_directions_panel_id',
				);

				// Convert geocoder ID into form_id + geocoder ID.
				foreach ( $convert_options as $option ) {

					if ( ! empty( $field[ $option ] ) ) {
						$field[ $option ] = is_array( $field[ $option ] ) ? preg_filter( '/^/', $form_id . '_', $field[ $option ] ) : esc_attr( $form_id . '_' . $field[ $option ] );
					}
				}

				$total_geocoders = array(
					$field['gfgeo_origin_geocoder_id'],
					$field['gfgeo_destination_geocoder_id'],
				);

				if ( ! empty( $field['gfgeo_waypoints_geocoders'] ) ) {
					$total_geocoders = array_merge( $total_geocoders, $field['gfgeo_waypoints_geocoders'] );
				}

				$form['fields'][ $key ]['total_geocoders'] = $total_geocoders;

				// Collect directions field.
				$form['directions_fields'][ $gfgeo_id ]             = $field;
				$form['directions_fields'][ $gfgeo_id ]['gfgeo_id'] = $gfgeo_id;
			}

			// Do some tasks for geolocation fields.
			if ( in_array( $field['type'], $geolocation_fields, true ) || 'address' === $field['type'] ) {

				$collect_field = true;

				// Set form geolocation to enabled if a geolcoation field exists in the form.
				if ( 'address' !== $field['type'] ) {
					$geo_fields_enabled = true;
				}

				$form['fields'][ $key ]['cssClass'] .= ' gfgeo_field_type_' . $field['type'];

				// Enable dynamic fields.
			} else {

				// Dynamic fields tasks.
				if ( ! empty( $field['gfgeo_dynamic_field_usage'] ) && 'directions' === $field['gfgeo_dynamic_field_usage'] ) {

					// add class to dynamic direction fields.
					if ( ! empty( $field['gfgeo_directions_field_id'] ) && ! empty( $field['gfgeo_dynamic_directions_field'] ) ) {

						$collect_field = true;
						$directions_id = $form_id . '_' . $field['gfgeo_directions_field_id'];
						$field_name    = esc_attr( $field['gfgeo_dynamic_directions_field'] );

						$form['fields'][ $key ]['cssClass'] = str_replace( 'field_type_' . $field['type'], '', $form['fields'][ $key ]['cssClass'] );

						$form['fields'][ $key ]['cssClass'] .= " gfgeo-dynamic-directions-field gfgeo-dynamic-field-{$directions_id} gfgeo-dynamic-{$field_name}-{$directions_id}"; // WPCS: XSS ok. Formidable escaping it.
					}

					// Dynamic location field.
				} elseif ( ! empty( $field['gfgeo_geocoder_id'] ) && ! empty( $field['gfgeo_dynamic_location_field'] ) ) {

					$collect_field = true;
					$geocoder_id   = $field['gfgeo_geocoder_id'];
					$field_name    = esc_attr( $field['gfgeo_dynamic_location_field'] );

					$form['fields'][ $key ]['cssClass'] .= " gfgeo-dynamic-location-field gfgeo-dynamic-field-{$geocoder_id} gfgeo-dynamic-{$field_name}-{$geocoder_id}"; // WPCS: XSS ok. Formidable escaping it.
				}
			}

			if ( in_array( $form['fields'][ $key ]['type'], $geolocation_fields, true ) ) {
				$form['fields'][ $key ] = apply_filters( 'gfgeo_modify_field_object', $form['fields'][ $key ], $form );
			}

			// Collect field.
			if ( $collect_field ) {
				$form['geolocation_fields'][ $gfgeo_id ]             = $field;
				$form['geolocation_fields'][ $gfgeo_id ]['gfgeo_id'] = $gfgeo_id;
			}
		}

		// Abort if geolocation fields do not exists in this form.
		if ( empty( $geo_fields_enabled ) ) {
			return $form;
		}

		$form['gfgeo_args'] = apply_filters(
			'gfgeo_modify_form_obejct',
			array(
				'is_page_load'                           => $is_page_load,
				'is_form_update'                         => $is_form_update,
				'is_edit_entry'                          => $is_edit_entry,
				'is_submitted'                           => $is_submitted,
				'is_post_update'                         => $is_post_update,
				'is_user_update'                         => $is_user_update,
				'geocode_default_location'               => apply_filters( 'gfgeo_geocode_default_location', true ),
				'verify_map_geocoding'                   => false,
				'verify_coords_geocoding'                => true,
				'hide_error_messages'                    => false,
				'debugger_enabled'                       => false,
				'disable_directions_cache'               => false,
				'address_field_event_triggers'           => 'keydown focusout',
				'navigator_timeout'                      => 10000,
				'geocoder_timeout'                       => 10000,
				'geocoder_timeout_message'               => __( 'The request to verify the location timed out.', 'gfgeo' ),
				'field_autocomplete'                     => apply_filters( 'gfgeo_enable_address_field_autocomplete_attr', true, $form ) ? '1' : '0',
				'place_details_enabled'                  => false,
				'init_geocoding_delay'                   => 2000,
				'high_accuracy'                          => GFGEO_HIGH_ACCURACY_MODE,
				'autocomplete_returned_fields'           => array(
					'address_component',
					'adr_address',
					'formatted_address',
					'geometry',
					'icon',
					'name',
					'photo',
					'place_id',
					'plus_code',
					'type',
					'url',
					'vicinity',
				),
				'get_details_returned_fields'            => array(
					'address_component',
					'adr_address',
					'formatted_address',
					'geometry',
					'icon',
					'name',
					'photo',
					'place_id',
					'plus_code',
					'type',
					'url',
					'vicinity',
				),
				'navigator_error_messages'               => array(
					'1' => __( 'User denied the request for Geolocation.', 'gfgeo' ),
					'2' => __( 'Location information is unavailable.', 'gfgeo' ),
					'3' => __( 'The request to get the user\'s location timed out.', 'gfgeo' ),
					'4' => __( 'An unknown error occurred', 'gfgeo' ),
					'5' => __( 'Sorry! Geolocation is not supported by this browser.', 'gfgeo' ),
				),
				'geocoder_failed_error_message'          => array(
					'advanced_address_geocoder' => __( 'We could not verify the address you entered.', 'gfgeo' ),
					'address_geocoder'          => __( 'We could not verify the address you entered.', 'gfgeo' ),
					'coords_geocoder'           => __( 'We could not verify the coordinates you entered.', 'gfgeo' ),
					'map'                       => __( 'We could not verify the location you selected.', 'gfgeo' ),
				),
				/* translators: %origin%: origin address, %destination%: destination address */
				'directions_failed_message'              => __( 'No route could be found between %origin% and %destination%.', 'gfgeo' ),
				'directions_missing_both_message'        => __( 'Please provide the origin and destination locations.', 'gfgeo' ),
				'directions_missing_origin_message'      => __( 'Please provide the origin location.', 'gfgeo' ),
				'directions_missing_destination_message' => __( 'Please provide the destination location.', 'gfgeo' ),
			),
			$form
		);

		// collect forms data.
		self::$gforms[ $form['id'] ] = $form;

		//if ( $geo_fields_enabled ) {
			add_action( 'gform_enqueue_scripts', array( $this, 'enqueue_scripts' ) ); // Front-end.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) ); // Admin's Edit Entry page.
		//}

		return $form;
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function enqueue_scripts() {

		global $gfgeo_enqueued;

		// Prevent this from running multiple times when multiple forms exist on the page.
		if ( empty( $gfgeo_enqueued ) ) {

			$gfgeo_enqueued = true;

			// localize plugin's options.
			$plugin_options = apply_filters(
				'gfgeo_render_form_options',
				array(
					'disable_script_init' => apply_filters( 'gfgeo_disable_script_init', false ) ? 'true' : 'false',
					'protocol'            => is_ssl() ? 'https' : 'http',
					'country_code'        => GFGEO_GOOGLE_MAPS_COUNTRY,
					'language_code'       => GFGEO_GOOGLE_MAPS_LANGUAGE,
					'ip_locator'          => GFGEO_IP_LOCATOR,
					'ip_token'            => GFGEO_IP_TOKEN,
					'is_admin'            => is_admin() ? 'true' : 'false',
					'edit_entry_form_id'  => GFCommon::is_entry_detail_edit() ? absint( $_GET['id'] ) : 0, // WPCS: CSRF ok.
					'form_prefix'         => GFGEO_PREFIX,
					// 'page_number'         => $edit_entry ? absint( $_GET['paged'] ) : 1, // WPCS: CSRF ok.
				)
			);

			wp_localize_script( 'gfgeo', 'gfgeo_options', $plugin_options );
		}

		wp_localize_script( 'gfgeo', 'gfgeo_gforms', self::$gforms );
	}

	/**
	 * Modify the advanced address field and append the autocomplete field.
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

		if ( 'address' !== $field->type || empty( $field->gfgeo_infield_locator_button ) ) {
			return $content;
		}

		$field_id = ! empty( $field->gfgeo_id ) ? esc_attr( $field->gfgeo_id ) : esc_attr( $form_id . '_' . $field->id );

		$button = '<div id="gfgeo-advanced-address-field-locator-button-holder-' . $field_id . '" class="gfgeo-advanced-address-field-locator-button-holder" style="display:none;">' . GFGEO_Helper::get_locator_button( $form_id, $field, 'infield' ) . '</div>';

		return $button . $content;
	}
}
$gfge_render_form = new GFGEO_Render_Form();
