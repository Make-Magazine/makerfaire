<?php

namespace GravityKit\GravityMaps;

use Exception;
use GravityKit\GravityMaps\Foundation\Helpers\Arr;
use WP_Error;
use GFForms;
use GFFormsModel;
use GFCommon;
use GF_Field_Address;
use GFAPI;
use GV\GF_Form;
use GravityView_Roles_Capabilities;

/**
 * Gravity Forms entry meta box logic
 *
 * @since     1.6
 */
class GF_Entry_Geocoding extends Component {
	/**
	 * @var string AJAX action to geocode address
	 */
	const AJAX_ACTION_GEOCODE_ADDRESS = 'gravityview_maps_gf_entry_geocoding_geocode_address';

	/**
	 * @var string AJAX action to save geocode data
	 */
	const AJAX_ACTION_SAVE_LATLONG = 'gravityview_maps_gf_entry_geocoding_save';

	/**
	 * @var string Unique nonce reference
	 */
	const NONCE_HANDLE = 'gravityview_maps_gf_entry_geocoding_nonce';

	/**
	 * @var string Unique reference name for UI script
	 */
	const ASSETS_HANDLE = 'gravityview_maps_gf_entry_geocoding';

	/**
	 * @var array Array of GF_Field_Address fields
	 */
	public $address_fields = array();

	/**
	 * @var array Capabilities required to edit entry
	 */
	private $edit_caps = array(
		'gravityforms_edit_entries',
		'gravityview_edit_others_entries',
		'gravityview_edit_entry',
		'gravityview_edit_entries',
	);

	/**
	 * @var array Capabilities required to edit entry
	 */
	private $read_caps = array(
		'gravityforms_view_entries',
	);

	/**
	 * Callback run when Maps plugin is being loaded; requires 'gravityforms_edit_entries' capability
	 *
	 * @return void
	 */
	public function load() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'gform_noconflict_styles', array( $this, 'register_no_conflicts' ) );
		add_filter( 'gform_noconflict_scripts', array( $this, 'register_no_conflicts' ) );
		add_filter( 'gravityview_noconflict_styles', array( $this, 'register_no_conflicts' ) );
		add_filter( 'gravityview_noconflict_scripts', array( $this, 'register_no_conflicts' ) );

		add_action( 'wp_ajax_' . self::AJAX_ACTION_GEOCODE_ADDRESS, array( $this, 'geocode_address' ) );
		add_action( 'wp_ajax_' . self::AJAX_ACTION_SAVE_LATLONG, array( $this, 'save_latlong' ) );
		add_filter( 'gform_entry_meta', array( $this, 'add_entry_meta' ), 10, 2 );

		add_filter( 'gform_entry_field_value', array( $this, 'maybe_append_geocoding_to_field_value' ), 10, 4 );
	}

	/**
	 * @param string   $display_value
	 * @param GF_Field $field
	 * @param array    $entry
	 * @param array    $form
	 *
	 * @return string
	 */
	public function maybe_append_geocoding_to_field_value( $display_value, $field, $entry, $form ) {
		$display_value = $display_value ?: '';

		// TODO: Support adding input fields to the full Edit Entry form 'entry_detail_edit' that will be saved with rest of entry via $_POST
		if ( 'entry_detail' !== GFForms::get_page() ) {
			return $display_value;
		}

		if ( ! $field instanceof GF_Field_Address ) {
			return $display_value;
		}

		$output = $this->get_geocoding_form_html( $field, $entry, $form );

		$display_value .= $output;

		return $display_value;
	}

	/**
	 * Returns whether an entry has background geocoding
	 *
	 * @since 1.7
	 *
	 * @see   https://github.com/gravityview/Maps/pull/99#issuecomment-492667997
	 *
	 * @param array $entry GF entry array
	 *
	 * @return bool True: has background process; false: does not have, or it expired
	 */
	private function entry_has_active_background_process( $entry ) {
		$deadline = gform_get_meta( $entry['id'], 'gvmaps_deadline' );

		$has_deadline = ! empty( $deadline ) && $deadline > time();

		return $has_deadline;
	}

	/**
	 * Generate meta box content to be displayed when editing GF Entry
	 *
	 * @param GF_Field_Address $field
	 * @param array            $entry
	 * @param array            $form
	 *
	 * @return string HTML output
	 */
	public function get_geocoding_form_html( GF_Field_Address $field, $entry = array(), $form = array() ) {
		$output   = '';
		$field_id = $field->id;
		$entry_id = (int) rgar( $entry, 'id' );
		$form_id  = (int) rgar( $form, 'id' );

		$value   = GFFormsModel::get_lead_field_value( $entry, $field );
		$address = GFCommon::get_lead_field_display( $field, $value, '', false, 'text' );

		$address_class = ( ! empty( $address ) ) ? "address_exists" : 'address_is_empty';

		$gv_maps_cache_markers = $GLOBALS['gravityview_maps']->component_instances['Cache_Markers'];

		$strings = self::strings();

		list( $lat, $long ) = $gv_maps_cache_markers->get_cache_position( $entry_id, $field_id );

		$lat  = esc_html( $lat );
		$long = esc_html( $long );

		$output .= '<br>';

		$has_background_process = $this->entry_has_active_background_process( $entry );

		if ( $has_background_process ) {
			$position_string = '<span class="description">' . esc_html__( 'In progress.', 'gk-gravitymaps' ) . '</span>';
		} elseif ( empty( $lat ) || empty( $long ) ) {
			$has_geocoding   = false;
			$position_string = esc_html__( 'Not yet geocoded.', 'gk-gravitymaps' );
		} else {
			$has_geocoding   = true;
			$position_string = $lat . ', ' . $long;
		}

		$output .= sprintf( esc_html__( 'Geocoding: %s', 'gk-gravitymaps' ), '<span class="gv-maps-geocoding-position" aria-live="polite">' . $position_string . '</span>' );

		// If no edit capabilities, don't show edit link
		if ( $has_background_process || ! GravityView_Roles_Capabilities::has_cap( $this->edit_caps ) ) {
			return $output;
		}

		// Do not display geocoding info when lat/long data or address are not available
		$geocoding_info_class = $has_geocoding && ! empty( $address ) ? '' : 'hidden';

		$geocoding_not_available_info_class = ! $has_geocoding ? 'not-geocoded' : '';

		$google_maps_link = ( $lat && $long ) ? "https://www.google.com/maps/search/{$lat},{$long}" : 'https://www.google.com/maps';
		$google_maps_link = esc_url( $google_maps_link );

		$output .= <<<HTML
<div class="gv-maps-geocoding-container ${address_class} hide-if-no-js" data-field-id="{$field_id}" data-entry-id="{$entry_id}" data-form-id="{$form_id}">
    <div class="gv-maps-geocoding-status hide-if-js hide-if-no-js below-h2 notice is-dismissible" aria-live="polite">
    	<!-- Populated in JS -->
    </div>

    <div class="input_field_container">
        <div class="geocoding_info">
	        <div class="input_field long">
	            <label for="lat_{$field_id}">{$strings['lat_label']}</label>
	            <div>
	            	<input type="text" class="code" id="lat_{$field_id}" name="lat" value="{$lat}" maxlength="15" aria-live="assertive" placeholder="{$strings['lat_label']}" />
				</div>
	        </div>
	        <div class="input_field lat">
	            <label for="long_{$field_id}">{$strings['long_label']}</label>
				<div>
					<input type="text" class="code" id="long_{$field_id}" name="long" value="{$long}" maxlength="16" aria-live="assertive" placeholder="{$strings['long_label']}" />
				</div>
	        </div>
	        <div class="input_field update">
	            <a href="#" data-original-data="{$lat}{$long}" class="button button-secondary update hidden" role="button"><span class="spinner"></span>{$strings['update_label']}</a>
	        </div>
		</div>
    </div>

    <div class="input_field">
		<a href="#" class="button geocode button-secondary geocode-address {$geocoding_not_available_info_class}" role="button"><span class="spinner"></span><span class="label">{$strings['geocode_label']}</span></a>
		<span class="gv-maps-google_map_link {$geocoding_info_class}">
			<a class="button button-link" href="${google_maps_link}">{$strings['open_google_maps_notice']}</a>
   		</span>
	</div>
</div>
HTML;

		return $output;
	}

	/**
	 * Returns translation strings for the maps geocoding inputs
	 *
	 * @return array Array of strings, all sanitized by esc_html()
	 */
	protected static function strings() {
		return array(
			'field_label'                    => esc_html_x( '%s (ID #%d)', 'Used to identify address field: %s is replaced with field label & %d is replaced with field ID', 'gk-gravitymaps' ),
			'lat_label'                      => esc_html__( 'Latitude', 'gk-gravitymaps' ),
			'long_label'                     => esc_html__( 'Longitude', 'gk-gravitymaps' ),
			'update_label'                   => esc_html_x( 'Update', 'Update latitude/longitude coordinates', 'gk-gravitymaps' ),
			'updated_success_notice'         => esc_html__( 'Coordinates were successfully updated.', 'gk-gravitymaps' ),
			'saved_success_notice'           => esc_html__( 'Coordinates were successfully saved.', 'gk-gravitymaps' ),
			'cleared_success_notice'         => esc_html__( 'Coordinates were successfully cleared.', 'gk-gravitymaps' ),
			'updated_error_notice'           => esc_html__( 'Unable to update coordinates.', 'gk-gravitymaps' ),
			'saved_error_notice'             => esc_html__( 'Unable to save coordinates.', 'gk-gravitymaps' ),
			'geocode_label'                  => esc_html_x( 'Geocode This Address', 'Get latitude/longitude from address', 'gk-gravitymaps' ),
			'save_coordinates_label'         => esc_html__( 'Save Latitude/Longitude', 'gk-gravitymaps' ),
			'geocode_refresh_label'          => esc_html_x( 'Refresh Geocoding', 'Get latitude/longitude from address', 'gk-gravitymaps' ),
			'geocode_refresh_title'          => esc_attr__( 'Fetch the longitude and latitude for this address.', 'gk-gravitymaps' ),
			'geocoded_success_notice'        => esc_html__( 'Coordinates were fetched and saved for this address.', 'gk-gravitymaps' ),
			'geocoded_error_notice'          => esc_html__( 'Unable to retrieve coordinates for this address.', 'gk-gravitymaps' ),
			'remote_request_fail_notice'     => esc_html__( 'Unable to perform this action. Please try again or contact support.', 'gk-gravitymaps' ),
			'geocoding_not_available_notice' => esc_html__( 'This address has not yet been geocoded.', 'gk-gravitymaps' ),
			'geocoding_not_yet'              => esc_html__( 'Not yet geocoded.', 'gk-gravitymaps' ),
			'open_google_maps_notice'        => esc_html__( 'See on Google Maps', 'gk-gravitymaps' ),
		);
	}

	/**
	 * Add the custom entry meta key to make it searchable and sortable
	 *
	 * @param array $entry_meta Array of custom entry meta keys with associative arrays
	 *
	 * @return array $entry_meta Updated meta entry object with latitude/longitude
	 */
	public function add_entry_meta( $entry_meta, $form_id ) {
		$form = GF_Form::by_id( $form_id );

		if ( ! $form ) {
			return $entry_meta;
		}

		// Get form fields with address type and save them for future use
		$address_fields = GFAPI::get_fields_by_type( $form->form, array( 'address' ) );
		$this->set_address_fields( $address_fields );

		if ( empty( $address_fields ) ) {
			return $entry_meta;
		}

		foreach ( $address_fields as $address_field ) {
			$entry_meta[ 'gvmaps_lat_' . $address_field->id ]  = array(
				'label'             => sprintf( esc_html_x( '%s Latitude', '%s is replaced by the field label of an address field', 'gravityview_maps', 'gk-gravitymaps' ), esc_html( $address_field->label ) ),
				'is_numeric'        => true,
				'is_default_column' => false
			);
			$entry_meta[ 'gvmaps_long_' . $address_field->id ] = array(
				'label'             => sprintf( esc_html_x( '%s Longitude', '%s is replaced by the field label of an address field', 'gravityview_maps', 'gk-gravitymaps' ), esc_html( $address_field->label ) ),
				'is_numeric'        => true,
				'is_default_column' => false
			);
		}

		return $entry_meta;
	}

	/**
	 * Fetch and cache address field coordinates
	 *
	 * @return void Exit with JSON response or terminate request with error code
	 */
	public function geocode_address() {
		// Validate AJAX request, meta data and latitude/longitude
		$is_valid_nonce  = wp_verify_nonce( rgar( $_POST, 'nonce' ), self::NONCE_HANDLE );
		$is_valid_action = self::AJAX_ACTION_GEOCODE_ADDRESS === rgar( $_POST, 'action' );
		$is_valid_meta   = ! empty( $_POST['meta'] );

		if ( ! $is_valid_nonce || ! $is_valid_action || ! $is_valid_meta ) {
			// Return 'forbidden' response if nonce is invalid, otherwise it's a 'bad request'
			wp_die( false, false, array( 'response' => ( ! $is_valid_nonce ) ? 403 : 400 ) );
		}

		$form_id  = intval( rgar( $_POST['meta'], 'formId' ) );
		$entry_id = intval( rgar( $_POST['meta'], 'entryId' ) );
		$field_id = intval( rgar( $_POST['meta'], 'fieldId' ) );

		// Validate form/field/entry IDs
		if ( ! $form_id || ! $entry_id || ! $field_id ) {
			wp_send_json_error();
		}

		if ( ! GravityView_Roles_Capabilities::has_cap( $this->edit_caps, $entry_id ) ) {
			wp_send_json_error( new WP_Error( 'no_access', esc_html__( 'You do not have permission to edit this entry.', 'gk-gravitymaps' ) ), false, array( 'response' => 400 ) );
		}

		$field = GFAPI::get_field( $form_id, $field_id );
		$entry = GFAPI::get_entry( $entry_id );

		// Get the address fields as an array (.3, .6, etc.)
		$value = GFFormsModel::get_lead_field_value( $entry, $field );

		// Get the text output (without map link)
		$address = GFCommon::get_lead_field_display( $field, $value, '', false, 'text' );

		// Replace new lines with spaces
		$address = str_replace( array( '\n', "\n" ), ' ', $address );
		$address = normalize_whitespace( $address );

		// Geocode address
		$gv_maps_geocoding = $GLOBALS['gravityview_maps']->component_instances['Geocoding'];
		$geocoded_address  = $gv_maps_geocoding->geocode( $address );
		if ( is_array( $geocoded_address ) && ! empty( $geocoded_address[0] ) && ! empty( $geocoded_address[1] ) ) {
			$gv_maps_cache_markers = $GLOBALS['gravityview_maps']->component_instances['Cache_Markers'];
			$gv_maps_cache_markers->set_cache_position( $entry_id, $field_id, $geocoded_address, $form_id );

			wp_send_json_success( array_combine( array( 'lat', 'long' ), $geocoded_address ) );
		} elseif ( $geocoded_address instanceof Exception ) {
			wp_send_json_error( array(
				'code'    => $geocoded_address->getCode(),
				'message' => $geocoded_address->getMessage(),
			) );
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Save (cache) address field coordinates
	 *
	 * @return void Exit with JSON response or terminate request with error code
	 */
	public function save_latlong() {
		// Shouldn't be accessible if the $_POST isn't correct
		if ( self::AJAX_ACTION_SAVE_LATLONG !== rgpost( 'action' ) ) {
			return;
		}

		// Return 'forbidden' response if nonce is invalid
		if ( ! wp_verify_nonce( rgpost( 'nonce' ), self::NONCE_HANDLE ) ) {
			wp_die( 'Invalid nonce', false, array( 'response' => 403 ) );
		}

		// Return 'bad request' if metadata is invalid
		if ( empty( $_POST['meta'] ) ) {
			wp_die( false, false, array( 'response' => 400 ) );
		}

		$lat      = (double) rgpost( 'lat' );
		$long     = (double) rgpost( 'long' );
		$form_id  = (int) rgar( $_POST['meta'], 'formId' );
		$entry_id = (int) rgar( $_POST['meta'], 'entryId' );
		$field_id = (int) rgar( $_POST['meta'], 'fieldId' );

		/** @var Cache_Markers $gv_maps_cache_markers */
		$gv_maps_cache_markers = $GLOBALS['gravityview_maps']->component_instances['Cache_Markers'];

		if ( empty( $lat ) && empty( $long ) ) {
			$gv_maps_cache_markers->delete_cache_position( $entry_id, $field_id );

			wp_send_json_success( array( 'lat' => '', 'long' => '' ) );
		}

		// Return 'bad request' if coordinates is invalid
		if ( empty( $lat ) || empty( $long ) ) {
			wp_send_json_error( new WP_Error( 'invalid_coordinates', esc_html__( 'The coordinates are not formatted properly.', 'gk-gravitymaps' ) ), false, array( 'response' => 400 ) );
		}

		if ( $lat > 90 || $lat < -90 ) {
			wp_send_json_error( new WP_Error( 'invalid_coordinates', esc_html__( 'The latitude is out of bounds.', 'gk-gravitymaps' ) ), false, array( 'response' => 400 ) );
		}

		if ( $long > 180 || $long < -180 ) {
			wp_send_json_error( new WP_Error( 'invalid_coordinates', esc_html__( 'The longitude is out of bounds.', 'gk-gravitymaps' ) ), false, array( 'response' => 400 ) );
		}

		if ( ! GravityView_Roles_Capabilities::has_cap( $this->edit_caps, $entry_id ) ) {
			wp_send_json_error( new WP_Error( 'no_access', esc_html__( 'You do not have permission to edit this entry.', 'gk-gravitymaps' ) ), false, array( 'response' => 400 ) );
		}

		// Validate form/field/entry IDs
		if ( ! $form_id || ! $entry_id || ! $field_id ) {
			wp_send_json_error();
		}

		$gv_maps_cache_markers->set_cache_position( $entry_id, $field_id, array( $lat, $long ), $form_id );

		wp_send_json_success( array( 'lat' => $lat, 'long' => $long ) );
	}

	/**
	 * Define and localize UI assets
	 *
	 * @param string $hook_suffix The current admin page.
	 *
	 * @return void
	 */
	public function enqueue_scripts( $hook_suffix = '' ) {
		if ( 'forms_page_gf_entries' !== $hook_suffix || 'entry' !== Arr::get( $_GET, 'view' ) ) {
			return;
		}

		$script_debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_style( self::ASSETS_HANDLE, plugins_url( 'assets/css/gv-maps-gf-entry-geocoding.css', $this->loader->path ), array(), $this->loader->plugin_version );

		wp_enqueue_script( self::ASSETS_HANDLE, plugins_url( 'assets/js/gv-maps-gf-entry-geocoding' . $script_debug . '.js', $this->loader->path ), array( 'jquery' ), $this->loader->plugin_version, true );

		$options = array(
			'nonce'          => wp_create_nonce( self::NONCE_HANDLE ),
			'action_save'    => self::AJAX_ACTION_SAVE_LATLONG,
			'action_geocode' => self::AJAX_ACTION_GEOCODE_ADDRESS,
			'localization'   => self::strings(),
		);

		wp_localize_script( self::ASSETS_HANDLE, 'GV_MAPS_GEOCODING', $options );
	}

	/**
	 * Add GravityView scripts and styles to Gravity Forms and GravityView No-Conflict modes
	 *
	 * @param array $registered Existing scripts or styles that have been registered (array of the handles)
	 *
	 * @return array $registered
	 */
	public function register_no_conflicts( $registered ) {
		$registered[] = self::ASSETS_HANDLE;

		return $registered;
	}

	/**
	 * Set array of GF_Field_Address fields
	 *
	 * @param array $address_fields
	 *
	 * @return void
	 */
	public function set_address_fields( $address_fields ) {
		$this->address_fields = $address_fields;
	}

	/**
	 * Get array of GF_Field_Address fields
	 *
	 * @return array $this->address_fields Array of GF_Field_Address fields
	 */
	public function get_address_fields() {
		return $this->address_fields;
	}
}
