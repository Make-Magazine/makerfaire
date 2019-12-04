<?php
/**
 * Gravity Forms Geolocation - Form editor class.
 *
 * @author  Eyal Fitoussi.
 *
 * @package gravityforms-geolocation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * GFGEO_Form_Editor
 *
 * Modify the "Form Editor" page of a form; Apply GGF settings to this page
 */
class GFGEO_Form_Editor {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// abort if not editor page.
		if ( empty( $_GET['id'] ) || ! isset( $_GET['page'] ) || 'gf_edit_forms' !== $_GET['page'] || ! empty( $_GET['view'] ) ) { // WPCS: CSRF ok.
			return;
		}

		add_action( 'gform_field_standard_settings', array( $this, 'fields_settings' ), 10, 2 );
		add_filter( 'gform_tooltips', array( $this, 'tooltips' ) );
		add_action( 'gform_editor_js_set_default_values', array( $this, 'set_default_labels' ) );
		add_action( 'gform_admin_pre_render', array( $this, 'render_form' ) );
		add_filter( 'gform_noconflict_scripts', array( $this, 'no_conflict_scripts' ) );
		add_filter( 'gform_noconflict_styles', array( $this, 'no_conflict_styles' ) );
	}

	/**
	 * Allow GFGEO scripts to load in no conflict mode
	 *
	 * @param  [type] $args [description].
	 *
	 * @return [type]       [description]
	 */
	public function no_conflict_scripts( $args ) {

		$args[] = 'gfgeo';
		$args[] = 'google-maps';
		$args[] = 'gfgeo-form-editor';

		return $args;
	}

	/**
	 * Allow GFGEO styles to load in no conflict mode
	 *
	 * @param  [type] $args [description].
	 *
	 * @return [type]       [description]
	 */
	public function no_conflict_styles( $args ) {

		$args[] = 'gfgeo';

		return $args;
	}

	/**
	 * Geolocation fields options
	 *
	 * @param  [type] $position [description].
	 * @param  [type] $form_id  [description].
	 */
	public function fields_settings( $position, $form_id ) {

		$position = absint( $position );

		if ( 10 === $position ) {
			?>

			<li class="field_setting gfgeo-geocoder-settings gfgeo-default-coordinates-settings-group gfgeo-settings-group">

				<label for="gfgeo-default-coordinates" class="section_label">
					<?php esc_attr_e( 'Default Coordinates', 'gfgeo' ); ?>
				</label>

				<ul class="gfgeo-settings-group-inner">

					<!-- default latitude -->
					<li class="gfgeo-default-latitude gfgeo-settings-section">
						<label for="gfgeo-default-latitude"> 
							<?php esc_html_e( 'Default Latitude', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_default_latitude_tt' ); ?>
						</label> 
						<input 
							type="text" 
							id="gfgeo-default-latitude" 
							size="25" 
							onkeyup="SetFieldProperty( 'gfgeo_default_latitude', this.value );">
					</li>

					<!-- default longitude -->

					<li class="gfgeo-default-longitude gfgeo-settings-section">
						<label for="gfgeo-default-longitude"> 
							<?php esc_html_e( 'Default Longitude', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_default_longitude_tt' ); ?>
						</label> 
						<input 
							type="text" 
							id="gfgeo-default-longitude" 
							size="25" 
							onkeyup="SetFieldProperty( 'gfgeo_default_longitude', this.value );">
					</li>
				</ul>
			</li>

			<!-- Page locator -->
			<li class="field_setting gfgeo-settings gfgeo-geocoder-settings gfgeo-page-locator" >

				<label for="gfgeo-page-locator" class="section_label">
					<?php esc_attr_e( 'Page Auto-Locator', 'gfgeo' ); ?>
				</label>

				<input 
					type="checkbox" 
					id="gfgeo-page-locator" 
					onclick="SetFieldProperty( 'gfgeo_page_locator', this.checked );" 
				/>
				<label for="gfgeo-page-locator" class="inline"> 
					<?php esc_html_e( 'Enable Page Locator', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_page_locator_tt' ); ?>
				</label>
			</li>

		<?php } ?>

		<?php if ( 800 === $position ) { ?>

			<!-- User meta field -->
			<li class="field_setting gfgeo-settings gfgeo-geocoder-settings gfgeo-user-meta-field" >

				<label for="gfgeo-user-meta-field"> 
					<?php esc_html_e( 'User Meta Field Name', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_user_meta_field_tt' ); ?>
				</label>

				<input 
					type="text" 
					size="35" 
					id="gfgeo-user-meta-field" 
					class="" 
					onkeyup="SetFieldProperty( 'gfgeo_user_meta_field', this.value );"
				/>
			</li>

			<!-- geocoder meta fields -->
			<li class="field_setting gfgeo-settings gfgeo-geocoder-settings gfgeo-geocoder-custom-meta" >
				<label>
					<?php esc_html_e( 'Meta Fields Setup', 'gfgeo' ); ?>
					<?php gform_tooltip( 'gfgeo_geocoder_meta_fields_setup_tt' ); ?>
					<a 
						href="#" 
						title="show fields" 
						onclick="event.preventDefault();jQuery( this ).closest( 'li' ).find( '.gfgeo-geocoder-meta-fields-wrapper' ).slideToggle();">
						<?php esc_html_e( 'Show Fields', 'gfgeo' ); ?>
					</a>
				</label> 
				<?php
				GFGEO_Helper::custom_meta_fields( 'custom_field' );
				?>
			</li>

		<?php } ?>

		<?php if ( 300 === $position ) { ?>

			<li class="field_setting gfgeo-settings gfgeo-custom-field-method">
				<input 
					type="checkbox" 
					id="gfgeo-custom-field-method" 
					onclick="SetFieldProperty( 'gfgeo_custom_field_method', this.checked );" 
				/>
				<label for="gfgeo-custom-field-method" class="inline"> 
					<?php esc_html_e( 'Save custom field as serialized array', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_custom_field_method_tt' ); ?>
				</label>
			</li>

		<?php } ?>

		<?php if ( 10 === $position ) { ?>

			<!-- Location output fields -->

			<li class="field_setting gfgeo-settings gfgeo-dynamic-location-field">

				<label for="gfgeo-dynamic-location-field" class="section_label">
					<?php esc_html_e( 'Dynamic Location Field', 'gfgeo' ); ?>
					<?php gform_tooltip( 'gfgeo_dynamic_location_field_tt' ); ?>
				</label> 

				<select name="gfgeo_dynamic_location_field" id="gfgeo-dynamic-location-field" onchange="SetFieldProperty( 'gfgeo_dynamic_location_field', jQuery(this).val() );">
					<?php
					foreach ( GFGEO_Helper::get_location_fields() as $value => $name ) {

						if ( 'status' === $value ) {
							continue;
						}

						echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $name ) . '</option>';
					}
					?>
				</select>
			</li>

			<!-- gecoder fields ID -  -->

			<li class="field_setting gfgeo-settings gfgeo-geocoder-id">
				<label for="gfgeo-geocoder-id" class="section_label"> 
					<?php esc_html_e( 'Geocoder ID', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_geocoder_id_tt' ); ?>
				</label> 
				<select 
					name="gfgeo_geocoder_id" 
					id="gfgeo-geocoder-id"
					class="gfgeo-geocoder-id"
					onchange="SetFieldProperty( 'gfgeo_geocoder_id', jQuery( this ).val() );"
				>
				<!-- values for this field generate by jquery function -->
				<option value=""><?php esc_html_e( 'N/A', 'gfgeo' ); ?></option>
				</select>
			</li>

			<!-- gecoder distance travel mode  -->


			<!-- distance field  -->

			<?php /* <li class="field_setting gfgeo-settings gfgeo-distance-field-settings gfgeo-distance-geocoders">
				<label for="gfgeo-distance-geocoders"> 
					<?php esc_html_e( 'Geocoders ID', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_distance_geocoders_tt' ); ?>
				</label>
				<ul class="gfgeo-distance-geocoders-items"></ul>
			</li> */ ?>

			<!-- disable geocoding -->

			<li class="field_setting gfgeo-settings gfgeo-disable-field-geocoding">
				<input 
					type="checkbox" 
					id="gfgeo-disable-field-geocoding" 
					onclick="SetFieldProperty( 'gfgeo_disable_field_geocoding', this.checked );" 
				/>
				<label for="gfgeo-disable-field-geocoding" class="inline"> 
					<?php esc_html_e( 'Disable Geocoding ( use as dynamic field only )', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_disable_field_geocoding_tt' ); ?>
				</label>
			</li>

			<!-- Locator button label -->

			<li class="field_setting gfgeo-settings gfgeo-locator-button-label">

				<label for="gfgeo-locator-button-label" class="section_label"> 
					<?php esc_html_e( 'Button Label', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_locator_button_label_tt' ); ?>
				</label> 
				<input 
					type="text" 
					size="35" 
					id="gfgeo-locator-button-label" 
					class="" 
					onkeyup="SetFieldProperty( 'gfgeo_locator_button_label', this.value );"
				/>

				<label for="gfgeo-locator-button-options" class="section_label" style="margin-top: 20px;margin-bottom: 0 ! important;"> 
					<?php esc_html_e( 'Loctor Options', 'gfgeo' ); ?> 
				</label> 
			</li>

			<!-- infield locator button -->

			<li class="field_setting gfgeo-settings gfgeo-infield-locator-button">

				<label for="gfgeo-locator-button" class="section_label">
					<?php esc_attr_e( 'Locator Button', 'gfgeo' ); ?>
				</label>

				<input 
					type="checkbox" 
					id="gfgeo-infield-locator-button" 
					onclick="SetFieldProperty( 'gfgeo_infield_locator_button', this.checked );" 
				/>
				<label for="gfgeo-infield-locator-button" class="inline"> 
					<?php esc_html_e( 'Enable locator button', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_infield_locator_button_tt' ); ?>
				</label>
			</li>

			<!-- IP locator status  -->

			<?php $ip_locator_status = ! GFGEO_IP_LOCATOR ? 'disabled="disabled"' : ''; ?>

			<li class="field_setting gfgeo-settings gfgeo-ip-locator-status">
				<label for="gfgeo-ip-locator-status"> 
					<?php esc_html_e( 'IP Address Locator', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_ip_locator_status_tt' ); ?>
				</label> 
				<select 
					<?php echo $ip_locator_status; // WPCS: XSS ok. ?>
					name="gfgeo_ip_locator_status" 
					id="gfgeo-ip-locator-status"
					class="gfgeo-ip-locator-status"
					onchange="SetFieldProperty( 'gfgeo_ip_locator_status', jQuery( this ).val() );"
				>
					<!-- values for this field generate by jquery function -->
					<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
					<option value="default"><?php esc_html_e( 'Default', 'gfgeo' ); ?></option>
					<option value="fallback"><?php esc_html_e( 'Fall-back', 'gfgeo' ); ?></option>

				</select>

				<?php if ( ! GFGEO_IP_LOCATOR ) { ?>
					<br />
					<em style="color:red;font-size: 11px">To enabled this feature navigate to the Gravity Forms Settings page and under the Geolocation tab select the IP Address service that you would like to use.</em>
				<?php } ?>

			</li>

			<!-- Locator found message option -->

			<li class="field_setting gfgeo-settings gfgeo-location-found-message">
				<label for="gfgeo-location-found-message"> 
					<?php esc_html_e( 'Location Found Message', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_location_found_message_tt' ); ?>
				</label> 
				<input 
					type="text" 
					size="35" 
					id="gfgeo-location-found-message" 
					class="" 
					onkeyup="SetFieldProperty( 'gfgeo_location_found_message', this.value );"
				/>
			</li>

			<!-- Disable locator failed message -->

			<li class="field_setting gfgeo-settings gfgeo-hide-location-failed-message">
				<input 
					type="checkbox" 
					id="gfgeo-hide-location-failed-message" 
					class="" 
					onclick="SetFieldProperty( 'gfgeo_hide_location_failed_message', this.checked );"
				/>
				<label for="gfgeo-hide-location-failed-message" class="inline"> 
					<?php esc_html_e( 'Disable Location Failed Message', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_hide_location_failed_message_tt' ); ?>
				</label> 
			</li>

			<!-- gecoder distance  -->

			<li class="field_setting gfgeo-geocoder-settings gfgeo-distance-settings-group gfgeo-settings-group">

				<label for="gfgeo-distance" class="section_label">
					<?php esc_attr_e( 'Driving Distance', 'gfgeo' ); ?>
				</label>

				<ul class="gfgeo-settings-group-inner">

					<li class="gfgeo-distance-destination-geocoder gfgeo-settings-section">

						<label for="gfgeo-geocoder-id"> 
							<?php esc_html_e( 'Destination Geocoder', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_distance_destination_geocoder_id_tt' ); ?>
						</label> 
						<select 
							name="gfgeo_distance_destination_geocoder_id" 
							id="gfgeo-distance-destination-geocoder-id"
							class="gfgeo-distance-destination-geocoder-id"
							onchange="SetFieldProperty( 'gfgeo_distance_destination_geocoder_id', jQuery( this ).val() );"
						>
						<!-- values for this field generate by jquery function -->
						<option value=""><?php esc_html_e( 'N/A', 'gfgeo' ); ?></option>
						</select>
					</li>

					<li class="gfgeo-distance-travel-mode gfgeo-settings-section">
						<label for="gfgeo-travel-mode"> 
							<?php esc_html_e( 'Travel Mode', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_distance_travel_mode_tt' ); ?>
						</label> 
						<select 
							name="gfgeo_distance_travel_mode" 
							id="gfgeo-distance-travel-mode"
							class="gfgeo-distance-travel-mode"
							onchange="SetFieldProperty( 'gfgeo_distance_travel_mode', jQuery( this ).val() );"
						>
							<option value="DRIVING"><?php esc_html_e( 'Driving', 'gfgeo' ); ?></option>
							<option value="WALKING"><?php esc_html_e( 'Walking', 'gfgeo' ); ?></option>
							<option value="BICYCLING"><?php esc_html_e( 'Bicycling', 'gfgeo' ); ?></option>
							<option value="TRANSIT"><?php esc_html_e( 'Transit', 'gfgeo' ); ?></option>
						</select>
					</li>

					<li class="gfgeo-distance-unit-system gfgeo-settings-section">
						<label for="gfgeo-unit-system"> 
							<?php esc_html_e( 'Unit System', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_distance_unit_system_tt' ); ?>
						</label> 
						<select 
							name="gfgeo_distance_unit_system" 
							id="gfgeo-distance-unit-system"
							class="gfgeo-distance-unit-system"
							onchange="SetFieldProperty( 'gfgeo_distance_unit_system', jQuery( this ).val() );"
						>
							<option value="imperial"><?php esc_html_e( 'Imperial ( Miles )', 'gfgeo' ); ?></option>
							<option value="metric"><?php esc_html_e( 'Metric ( Kilometers )', 'gfgeo' ); ?></option>
						</select>
					</li>

					<!-- distance map ID -  -->

					<?php /* <li class="gfgeo-distance-travel-route-map-id gfgeo-settings-section">
						<label for="gfgeo-distance-travel-route-map-id"> 
							<?php esc_html_e( 'Display Driving Route', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_distance_travel_route_map_id_tt' ); ?>
						</label> 
						<select 
							name="gfgeo_distance_travel_route_map_id" 
							id="gfgeo-distance-travel-route-map-id"
							class="gfgeo-distance-travel-route-map-id"
							onchange="SetFieldProperty( 'gfgeo_distance_travel_route_map_id', jQuery( this ).val() );"
						>
						<!-- values for this field generate by jquery function -->
						<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
						</select>
					</li> */ ?>

					<li class="gfgeo-distance-travel-show-route-on-map gfgeo-settings-section">
						<input 
							type="checkbox" 
							id="gfgeo-distance-travel-show-route-on-map" 
							onclick="SetFieldProperty( 'gfgeo_distance_travel_show_route_on_map', this.checked );" 
						/>
						<label for="gfgeo-distance-travel-show-route-on-map" class="inline"> 
							<?php esc_html_e( 'Display Route On the Map', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_distance_travel_show_route_on_map_tt' ); ?>
						</label>
					</li>

					<li class="gfgeo-distance-directions-panel-id gfgeo-settings-section">
						<label for="gfgeo-distance-directions-panel-id"> 
							<?php esc_html_e( 'Display Driving Directions', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_distance_directions_panel_id_tt' ); ?>
						</label> 
						<select 
							name="gfgeo_distance_directions_panel_id" 
							id="gfgeo-distance-directions-panel-id"
							class="gfgeo-distance-directions-panel-id"
							onchange="SetFieldProperty( 'gfgeo_distance_directions_panel_id', jQuery( this ).val() );"
						>
						<!-- values for this field generate by jquery function -->
						<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
						</select>
					</li>
				</ul>

			</li>

			<!-- Geocoder GEO my WP  post integrations -->
			<?php
			if ( class_exists( 'GEO_my_WP' ) ) {
				$disabled = false;
				$message  = '';
			} else {
				$disabled = true;
				$message  = __( 'This feature requires <a href="https://wordpress.org/plugins/geo-my-wp/" target="_blank">GEO my WP</a> plugin', 'gfgeo' );
			}
			?>

			<li class="field_setting gfgeo-geocoder-settings gfgeo-gmw-integration-settings-group gfgeo-settings-group">

				<label for="gfgeo-gmw-integration" class="section_label">
					<?php esc_attr_e( 'GEO my WP Integration', 'gfgeo' ); ?>
				</label>

				<ul class="gfgeo-settings-group-inner">

					<li class="gfgeo-gmw-post-integration gfgeo-settings-section">	
						<?php if ( ! $disabled ) { ?>
							<input 
								type="checkbox" 
								id="gfgeo-gmw-post-integration" 
								onclick="SetFieldProperty( 'gfgeo_gmw_post_integration', this.checked );"
							/>
						<?php } else { ?>
							<span class="dashicons dashicons-no" style="width:15px;line-height: 1.1;color: red;"></span>
						<?php } ?>

						<label for="gfgeo-gmw-post-integration" class="inline"> 
							<?php esc_html_e( 'GEO my WP Post Integration', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_gmw_post_integration_tt' ); ?>
						</label>
						<small style="display: block;color: red;margin-top: 2px;"><?php echo $message; // WPCS: XSS ok. ?></small>
					</li>

					<!-- GMW Phone  -->

					<li class="gfgeo-gmw-post-integration-wrapper gfgeo-gmw-post-integration-phone gfgeo-settings-section">
						<label for="gfgeo-gmw-post-integration-phone"> 
							<?php esc_html_e( 'GEO my WP - Phone', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_gmw_post_integration_phone_tt' ); ?>
						</label> 
						<select 
							name="gfgeo_gmw_post_integration_phone" 
							id="gfgeo-gmw-post-integration-phone"
							class="gfgeo-gmw-post-integration-phone"
							onchange="SetFieldProperty( 'gfgeo_gmw_post_integration_phone', jQuery( this ).val() );"
						>
						<!-- values for this field generate by jquery function -->
						<option value=""><?php esc_html_e( 'N/A', 'gfgeo' ); ?></option>
						</select>
					</li>

					<!-- GMW Fax  -->

					<li class="gfgeo-gmw-post-integration-wrapper gfgeo-gmw-post-integration-fax gfgeo-settings-section">
						<label for="gfgeo-gmw-post-integration-fax"> 
							<?php esc_html_e( 'GEO my WP - Fax', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_gmw_post_integration_fax_tt' ); ?>
						</label> 
						<select 
							name="gfgeo_gmw_post_integration_fax" 
							id="gfgeo-gmw-post-integration-fax"
							class="gfgeo-gmw-post-integration-fax"
							onchange="SetFieldProperty( 'gfgeo_gmw_post_integration_fax', jQuery( this ).val() );"
						>
						<!-- values for this field generate by jquery function -->
						<option value=""><?php esc_html_e( 'N/A', 'gfgeo' ); ?></option>
						</select>
					</li>

					<!-- GMW Email  -->

					<li class="gfgeo-gmw-post-integration-wrapper gfgeo-gmw-post-integration-email gfgeo-settings-section">
						<label for="gfgeo-gmw-post-integration-email"> 
							<?php esc_html_e( 'GEO my WP - Email', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_gmw_post_integration_email_tt' ); ?>
						</label> 
						<select 
							name="gfgeo_gmw_post_integration_email" 
							id="gfgeo-gmw-post-integration-email"
							class="gfgeo-gmw-post-integration-email"
							onchange="SetFieldProperty( 'gfgeo_gmw_post_integration_email', jQuery( this ).val() );"
						>
						<!-- values for this field generate by jquery function -->
						<option value=""><?php esc_html_e( 'N/A', 'gfgeo' ); ?></option>
						</select>
					</li>

					<!-- GMW website  -->

					<li class="gfgeo-gmw-post-integration-wrapper gfgeo-gmw-post-integration-website gfgeo-settings-section">
						<label for="gfgeo-gmw-post-integration-website"> 
							<?php esc_html_e( 'GEO my WP - Website', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_gmw_post_integration_website_tt' ); ?>
						</label> 
						<select 
							name="gfgeo_gmw_post_integration_website" 
							id="gfgeo-gmw-post-integration-website"
							class="gfgeo-gmw-post-integration-website"
							onchange="SetFieldProperty( 'gfgeo_gmw_post_integration_website', jQuery( this ).val() );"
						>
						<!-- values for this field generate by jquery function -->
						<option value=""><?php esc_html_e( 'N/A', 'gfgeo' ); ?></option>
						</select>
					</li>

					<!-- GEO my WP User integrations -->
					<li class="gfgeo-gmw-user-integration gfgeo-settings-section">	

						<?php if ( ! $disabled ) { ?>

							<input 
								type="checkbox" 
								id="gfgeo-gmw-user-integration" 
								onclick="SetFieldProperty( 'gfgeo_gmw_user_integration', this.checked );" 
								<?php echo $disabled; // WPCS: XSS ok. ?>
							/>

						<?php } else { ?>

							<span class="dashicons dashicons-no" style="width:15px;line-height: 1.1;color: red;"></span>

						<?php } ?>

						<label for="gfgeo-gmw-user-integration" class="inline"> 
							<?php esc_html_e( 'GEO my WP User Integration', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_gmw_user_integration_tt' ); ?>
						</label>

						<small style="display: block;color: red;margin-top: 2px;"><?php echo $message; // WPCS: XSS ok. ?></small>

					</li>
				</ul>

			</li>

			<!-- latitude placehoolder --> 

			<li class="field_setting gfgeo-settings gfgeo-latitude-placeholder">

				<label for="gfgeo-coords-placeholder" class="section_label"> 
					<?php esc_html_e( 'Fields Placeholder', 'gfgeo' ); ?> 
				</label>

				<label for="gfgeo-latitude-placeholder"> 
					<?php esc_html_e( 'Latitude Placeholder', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_latitude_placeholder_tt' ); ?>
				</label> 
				<input 
					type="text" 
					size="35" 
					id="gfgeo-latitude-placeholder" 
					data-field="latitude"
					class="coordinates-placeholder"
					onkeyup="SetFieldProperty( 'gfgeo_latitude_placeholder', this.value );"
				/>
			</li>

			<!-- longitude placehoolder --> 
			<li class="field_setting gfgeo-settings gfgeo-longitude-placeholder">
				<label for="gfgeo-longitude-placeholder"> 
					<?php esc_html_e( 'Longitude Placeholder', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_longitude_placeholder_tt' ); ?>
				</label> 
				<input 
					type="text" 
					size="35" 
					id="gfgeo-longitude-placeholder" 
					class="coordinates-placeholder"
					data-field="longitude"
					onkeyup="SetFieldProperty( 'gfgeo_longitude_placeholder', this.value );" />
			</li>

			<!--  Map fields -->

			<li class="field_setting gfgeo-settings gfgeo-map-settings gfgeo-settings-group">

				<label for="gfgeo-map-default-coordinates" class="section_label">
					<?php esc_attr_e( 'Default Coordinates', 'gfgeo' ); ?>
				</label>

				<ul class="gfgeo-settings-group-inner">

					<li class="gfgeo-map-default-latitude gfgeo-settings-section">
						<label for="gfgeo-map-default-latitude"> 
							<?php esc_html_e( 'Default Latitude', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_map_default_latitude_tt' ); ?>
						</label> 
						<input 
							type="text" 
							id="gfgeo-map-default-latitude" 
							size="15" 
							onkeyup="SetFieldProperty( 'gfgeo_map_default_latitude', this.value );"
						/>
					</li>

					<li class="gfgeo-map-default-longitude gfgeo-settings-section">
						<label for="gfgeo-map-default-longitude"> 
							<?php esc_html_e( 'Default Longitude', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_map_default_longitude_tt' ); ?>
						</label> 
						<input 
							type="text" 
							id="gfgeo-map-default-longitude" 
							size="15" 
							onkeyup="SetFieldProperty( 'gfgeo_map_default_longitude', this.value );"
						/>
					</li>
				</ul>
			</li>

			<li class="field_setting gfgeo-settings gfgeo-map-settings">

				<label for="gfgeo-map-default-latitude" class="section_label"> 
					<?php esc_html_e( 'Map Options', 'gfgeo' ); ?>
				</label> 

				<label for="gfgeo-map-type">
					<?php esc_html_e( 'Map Type', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_map_type_tt' ); ?>
				</label> 
				<select name="gfgeo_map_type" id="gfgeo-map-type" onchange="SetFieldProperty( 'gfgeo_map_type', jQuery(this).val() );">
						<option value="ROADMAP">ROADMAP</option>
						<option value="SATELLITE">SATELLITE</option>
						<option value="HYBRID">HYBRID</option>
						<option value="TERRAIN">TERRAIN</option>
				</select>
			</li>

			<li class="field_setting gfgeo-settings gfgeo-map-settings">
				<label for="gfgeo-zoom-level"> 
					<?php esc_html_e( 'Zoom Level', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_zoom_level_tt' ); ?>
				</label> 
				<select name="gfgeo_zoom_level" id="gfgeo-zoom-level" onchange="SetFieldProperty( 'gfgeo_zoom_level', jQuery(this).val() );">
						<?php $count = 18; ?>
						<?php
						for ( $x = 1; $x <= 18; $x++ ) {
							echo '<option value="' . $x . '">' . $x . '</option>'; // WPCS: XSS ok.
						}
						?>
				</select>
			</li>

			<li class="field_setting gfgeo-settings gfgeo-map-settings">
				<label for="gfgeo-map-width"> 
					<?php esc_html_e( 'Map Width', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_map_width_tt' ); ?>
				</label> 
				<input 
					type="text" 
					id="gfgeo-map-width" 
					size="15" 
					onkeyup="SetFieldProperty( 'gfgeo_map_width', this.value );"
				/>
			</li>

			<li class="field_setting gfgeo-settings gfgeo-map-settings">
				<label for="gfgeo-map-height"> 
					<?php esc_html_e( 'Map Height', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_map_height_tt' ); ?>
				</label> 
				<input 
					type="text" 
					id="gfgeo-map-height" 
					size="15" 
					onkeyup="SetFieldProperty( 'gfgeo_map_height', this.value );">
			</li>

			<li class="field_setting gfgeo-settings gfgeo-map-settings">
				<label for="gfgeo-map-styles"> 
					<?php esc_html_e( 'Map Styles', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_map_styles_tt' ); ?>
				</label>
				<textarea 
					id="gfgeo-map-styles" 
					class="fieldwidth-3 fieldheight-2" 
					onblur="SetFieldProperty( 'gfgeo_map_styles', this.value );"></textarea>
			</li>

			<li class="field_setting gfgeo-settings gfgeo-map-settings">
				<label for="gfgeo-map-marker"> 
					<?php esc_html_e( 'Map Marker URL', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_map_marker_tt' ); ?>
				</label> 
				<input 
					type="text" 
					id="gfgeo-map-marker" 
					size="25" 
					onkeyup="SetFieldProperty( 'gfgeo_map_marker', this.value );">
			</li>

			<li class="field_setting gfgeo-settings gfgeo-map-settings">
				<input 
					type="checkbox" 
					id="gfgeo-draggable-marker" 
					onclick="SetFieldProperty( 'gfgeo_draggable_marker', this.checked );" 
				/>
				<label for="gfgeo-draggable-marker" class="inline"> 
					<?php esc_html_e( 'Draggable Map Marker', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_draggable_marker_tt' ); ?>
				</label>
			</li>

			<li class="field_setting gfgeo-settings gfgeo-map-settings">
				<input 
					type="checkbox" 
					id="gfgeo-set-marker-on-click" 
					onclick="SetFieldProperty( 'gfgeo_set_marker_on_click', this.checked );" 
				/>
				<label for="gfgeo-set-marker-on-click" class="inline"> 
					<?php esc_html_e( 'Set Map Marker on Click', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_set_marker_on_click_tt' ); ?>
				</label>
			</li>

			<li class="field_setting gfgeo-settings gfgeo-map-settings">
				<input 
					type="checkbox" 
					id="gfgeo-map-scroll-wheel" 
					onclick="SetFieldProperty( 'gfgeo_map_scroll_wheel', this.checked );" 
				/>
				<label for="gfgeo-map-scroll-wheel" class="inline"> 
					<?php esc_html_e( 'Enable Mouse Scroll-Wheel Zoom', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_map_scroll_wheel_tt' ); ?>
				</label>
			</li>

			<li class="field_setting gfgeo-settings gfgeo-map-settings">
				<input 
					type="checkbox" 
					id="gfgeo-disable-address-output" 
					onclick="SetFieldProperty( 'gfgeo_disable_address_output', this.checked );" 
				/>
				<label for="gfgeo-disable-address-output" class="inline"> 
					<?php esc_html_e( 'Disable Address Output ( beta )', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_disable_address_output_tt' ); ?>
				</label>
			</li>

			<!-- autocomplete options -->

			<li class="field_setting gfgeo-settings gfgeo-address-autocomplete">

				<label for="gfgeo-address-autocomplete" class="section_label">
					<?php esc_attr_e( 'Address Autocomplete', 'gfgeo' ); ?>
				</label>

				<input 
					type="checkbox" 
					id="gfgeo-address-autocomplete" 
					onclick="SetFieldProperty( 'gfgeo_address_autocomplete', this.checked );" 
				/>
				<label for="gfgeo-address-autocomplete" class="inline"> 
					<?php esc_html_e( 'Enable Google Address Autocomplete', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_address_autocomplete_tt' ); ?>
				</label>
			</li>

			<li class="field_setting gfgeo-settings gfgeo-address-autocomplete-types">
				<label for="gfgeo-address-autocomplete-types"> 
					<?php esc_html_e( 'Autocomplete Results Types', 'gfgeo' ); ?>
					<?php gform_tooltip( 'gfgeo_address_autocomplete_types_tt' ); ?>
				</label> 
				&#32;&#32;
				<select 
					name="gfgeo_address_autocomplete_types" 
					id="gfgeo-address-autocomplete-types"
					class="gfgeo-address-autocomplete-types"
					onchange="SetFieldProperty( 'gfgeo_address_autocomplete_types', jQuery( this ).val() );"
				>	
					<option value="">All types</option>
					<option value="geocode">Geocode</option>
					<option value="address">Address</option>
					<option value="establishment">Establishment</option>
					<option value="(regions)">Regions</option>
					<option value="(cities)">Cities</option>
				</select>
			</li>

			<li class="field_setting gfgeo-settings gfgeo-address-autocomplete-bounds">
				<label for="gfgeo-address-autocomplete-bounds"> 
					<?php esc_html_e( 'Address Autocomplete Bounds', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_address_autocomplete_bounds_tt' ); ?>
				</label> 
				<input 
					type="text" 
					size="45" 
					id="gfgeo-address-autocomplete-bounds" 
					onkeyup="SetFieldProperty( 'gfgeo_address_autocomplete_bounds', this.value );"
					placeholder="Ex: -33.8902,151.1759|-33.8902,151.1759"
				/>
			</li>

			<li class="field_setting gfgeo-settings gfgeo-address-autocomplete-locator-bounds">
				<input 
					type="checkbox" 
					id="gfgeo-address-autocomplete-locator-bounds" 
					onclick="SetFieldProperty( 'gfgeo_address_autocomplete_locator_bounds', this.checked );" 
				/>
				<label for="gfgeo-address-autocomplete-locator-bounds" class="inline"> 
					<?php esc_html_e( 'Enable Page Locator Bounds', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_address_autocomplete_locator_bounds_tt' ); ?>
				</label>
			</li>

			<li class="field_setting gfgeo-settings gfgeo-address-autocomplete-placeholder">
				<label for="gfgeo-address-autocomplete-placeholder"> 
					<?php esc_html_e( 'Autocomplete Placeholder', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_address_autocomplete_placeholder_tt' ); ?>
				</label> 
				<input 
					type="text" 
					size="35" 
					id="gfgeo-address-autocomplete-placeholder" 
					onkeyup="SetFieldProperty( 'gfgeo_address_autocomplete_placeholder', this.value );"
				/>
			</li>

			<li class="field_setting gfgeo-settings gfgeo-address-autocomplete-desc">
				<label for="gfgeo-address-autocomplete-desc"> 
					<?php esc_html_e( 'Field description', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_address_autocomplete_desc_tt' ); ?>
				</label> 
				<input 
					type="text" 
					size="35" 
					id="gfgeo-address-autocomplete-desc" 
					onkeyup="SetFieldProperty('gfgeo_address_autocomplete_desc', this.value);">
			</li>

			<li class="field_setting gfgeo-settings gfgeo-address-autocomplete-country">
				<label for="gfgeo-address-autocomplete-country"> 
					<?php esc_html_e( 'Restrict Autocomplete Results', 'gfgeo' ); ?>
					<?php gform_tooltip( 'gfgeo_address_autocomplete_country_tt' ); ?>
				</label> 
				&#32;&#32;
				<select 
					multiple="multiple"
					name="gfgeo_address_autocomplete_country" 
					id="gfgeo-address-autocomplete-country"
					class="gfgeo-address-autocomplete-country"
					onchange="SetFieldProperty( 'gfgeo_address_autocomplete_country', jQuery(this).val());"
				>
				<?php
				foreach ( GFGEO_Helper::get_countries() as $value => $name ) {
					echo '<option value="' . $value . '">' . $name . '</option>'; // WPCS: XSS ok.
				}
				?>
				</select>
			</li>

			<li class="field_setting gfgeo-settings gfgeo-geocoder-settings gfgeo-google-maps-link" >
				<input 
					type="checkbox" 
					id="gfgeo-google-maps-link" 
					onclick="SetFieldProperty( 'gfgeo_google_maps_link', this.checked );" 
				/>
				<label for="gfgeo-google-maps-link" class="inline"> 
					<?php esc_html_e( 'Enable Google Maps Link', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_google_maps_link_tt' ); ?>
				</label>
			</li>

			<li class="field_setting gfgeo-settings gfgeo-geocoder-section-end"></li>
		<?php } ?>
		<?php
	}

	/**
	 * Tooltips.
	 *
	 * @param  [type] $tooltips [description].
	 *
	 * @return [type]           [description]
	 */
	public function tooltips( $tooltips ) {

		// page locator.
		$tooltips['gfgeo_page_locator_tt'] = __( 'The plugin will try to dynamically retrieve the user\'s current position when the form first loads. If location was found, it will auto-populate in the location fields attached to this geocoder. Note that if the page locator is enabled the default coordinates above will not take place.', 'gfgeo' );

		$tooltips['gfgeo_google_maps_link_tt'] = __( 'Display link to Google Maps in the form entry and notifications.', 'gfgeo' );

		// default coords.
		$tooltips['gfgeo_default_latitude_tt']  = __( 'Enter the latitude of the initial location that will be displayed in the geolocation fields, attached to this geocoder, when the form first loads. Otherwise, leave  the field blank.', 'gfgeo' );
		$tooltips['gfgeo_default_longitude_tt'] = __( 'Enter the longitude of the initial location that will be displayed in the geolocation fields, attached to this geocoder, when the form first loads. Otherwise, leave  the field blank.', 'gfgeo' );

		// Distance.
		$tooltips['gfgeo_distance_destination_geocoder_id_tt']  = __( 'Select the geocoder which you would like to calculate the distance to.', 'gfgeo' );
		$tooltips['gfgeo_distance_travel_mode_tt']              = __( 'Select the travel mode.', 'gfgeo' );
		$tooltips['gfgeo_distance_unit_system_tt']              = __( 'Select the unit system that will be used when calculating the distance.', 'gfgeo' );
		$tooltips['gfgeo_distance_travel_show_route_on_map_tt'] = __( 'Display driving route on a map.', 'gfgeo' );
		$tooltips['gfgeo_distance_directions_panel_id_tt']      = __( 'Display driving directions.', 'gfgeo' );

		// GEO my WP integration.
		$tooltips['gfgeo_gmw_post_integration_tt'] = __( 'Check this checkbox if you\'d like to sync this geocoder with GEO my WP Posts Locator add-on. This location will then be saved in GEO my WP database and the post attached to it ( if at all created or udpated )will be searchable via GEO my WP search forms', 'gfgeo' );

		$tooltips['gfgeo_gmw_post_integration_phone_tt'] = __( 'Select the field that will be used for the GEO my WP Phone.', 'gfgeo' );

		$tooltips['gfgeo_gmw_post_integration_fax_tt'] = __( 'Select the field that will be used for the GEO my WP Fax.', 'gfgeo' );

		$tooltips['gfgeo_gmw_post_integration_email_tt'] = __( 'Select the field that will be used for the GEO my WP Email.', 'gfgeo' );

		$tooltips['gfgeo_gmw_post_integration_website_tt'] = __( 'Select the field that will be used for the GEO my WP Website.', 'gfgeo' );

		$tooltips['gfgeo_gmw_user_integration_tt'] = __( 'Check this checkbox if you\'d like to sync this geocoder with GEO my WP users database. This location will then be saved in GEO my WP database and the user attached to it will be searchable via GEO my WP search forms', 'gfgeo' );

		// user meta field.
		$tooltips['gfgeo_user_meta_field_tt'] = __( 'Enter a user meta field where you\'d like to save the complete geocoded information as an array. Otherwise, leave  the field blank.', 'gfgeo' );

		// meta fields setup.
		$tooltips['gfgeo_geocoder_meta_fields_setup_tt'] = __( 'Click the "Show Fields" link to see the list of the geocoded fields which you can save each of one of them into post custom field, user meta field and BuddyPress profile field ( BuddyPress plugin required ).', 'gfgeo' );

		// dynamic fiedlds.
		$tooltips['gfgeo_dynamic_location_field_tt'] = __( 'Dynamically populate this field with the selected location field everytime geocoding takes place.', 'gfgeo' );

		// Geocoder ID.
		$tooltips['gfgeo_geocoder_id_tt'] = __( 'Select the geocoder field ID that you would like to sync this field with.', 'gfgeo' );

		// locator button.
		$tooltips['gfgeo_locator_button_label_tt']         = __( 'Enter the locator button label.', 'gfgeo' );
		$tooltips['gfgeo_infield_locator_button_tt']       = __( 'Display a locator icon, inside the text field, that will retrieve the user\'s current position once clicked.', 'gfgeo' );
		$tooltips['gfgeo_location_found_message_tt']       = __( 'Enable alert message that will show once the user poisiton was found.', 'gfgeo' );
		$tooltips['gfgeo_hide_location_failed_message_tt'] = __( 'Hide the alert message showing when the user poisiton was not found. Instead, it will show in the developer console log.', 'gfgeo' );
		$tooltips['gfgeo_ip_locator_status_tt']            = __( '( Beta feature ) Enable this feature so the plugin will retrive the user\'s current location based on the IP address. Choose "Default" to use the IP address instead of the browser\'s locator ( HTML5 geolocation ) or "Fallback" to use the IP address when the browser fails to retrive the location. Please note that while the IP address locator does not require the user\'s permission to retrive his location, like how the browser does, it is also not accurate compare to the HTML5 geolocation.', 'gfgeo' );

		// corrdinates.
		$tooltips['gfgeo_latitude_placeholder_tt']  = __( 'Enter a placeholder text for the latitude textbox.', 'gfgeo' );
		$tooltips['gfgeo_longitude_placeholder_tt'] = __( 'Enter a placeholder text for the longitude textbox.', 'gfgeo' );
		$tooltips['gfgeo_custom_field_method_tt']   = __( 'By default the coordinates value will be saved comma separated: latitude,longitude ( ex 12345,6789 ). Check this checkbox if you\'d like to save the value as serialized array', 'gfgeo' );

		// map fields tooltips.
		$tooltips['gfgeo_map_default_latitude_tt']   = __( 'Enter the latitude of the point that will show on the map when the form first loads.', 'gfgeo' );
		$tooltips['gfgeo_map_default_longitude_tt']  = __( 'Enter the longitude of the point that will show on the map when the form first loads.', 'gfgeo' );
		$tooltips['gfgeo_map_width_tt']              = __( 'Enter the map width in pixels or percentage.', 'gfgeo' );
		$tooltips['gfgeo_map_height_tt']             = __( 'Enter the map height in pixels or percentage.', 'gfgeo' );
		$tooltips['gfgeo_map_styles_tt']             = __( 'Enter custom map style. <a href="https://snazzymaps.com" target="_blank">Snazzy Maps website</a> has a large collection of map styles that you can use.', 'gfgeo' );
		$tooltips['gfgeo_map_marker_tt']             = __( 'Enter the URL of the icon that will be used as the map marker.', 'gfgeo' );
		$tooltips['gfgeo_map_type_tt']               = __( 'Choose the map type.', 'gfgeo' );
		$tooltips['gfgeo_zoom_level_tt']             = __( 'Set the zoom level of the map.', 'gfgeo' );
		$tooltips['gfgeo_draggable_marker_tt']       = __( 'Making marker draggable allows the front-end users to set location by dragging the map marker to the desired position.', 'gfgeo' );
		$tooltips['gfgeo_set_marker_on_click_tt']    = __( 'Set marker\'s location by a click on the map.', 'gfgeo' );
		$tooltips['gfgeo_map_scroll_wheel_tt']       = __( 'Allow map zoom via mouse scroll-wheel.', 'gfgeo' );
		$tooltips['gfgeo_disable_address_output_tt'] = __( 'Disable the output of the address fields when updating the marker\'s location. This way only the coordinates will be dynamically updated. This can be useful for a specific scenario where one wants to first find the location on the map by entering an address. Then, if the address entered is correct but the marker is not on the exact location on the map or the coordinates are not the exact desired coordinates, the visitor can drag the marker to find the exact coordinates without changing the address.', 'gfgeo' );

		// disable field geocoding.
		$tooltips['gfgeo_disable_field_geocoding_tt'] = __( 'When checked, the address field will be treated as a dynamic field only. Which means, that when the address changes it will not be geocoded and will not effect the rest of the fields in the form. However, when another geolocation field is updated, the address field will be populated with the new location values. This will also disable the locator button and address autocomplete of this field.', 'gfgeo' );

		// address autocomplete.
		$tooltips['gfgeo_address_autocomplete_tt']                = __( 'Enable live suggested results, by Google Places API, while the user is typing an address.', 'gfgeo' );
		$tooltips['gfgeo_address_autocomplete_types_tt']          = __( 'Select the type of results that will be displayed in the suggested results. <a href="https://developers.google.com/maps/documentation/javascript/places-autocomplete#add_autocomplete" target="_blank">Click here</a> to read more about the autocomplete types.', 'gfgeo' );
		$tooltips['gfgeo_address_autocomplete_country_tt']        = __( 'Select the countries that you would like to restrict the address autocomplete suggested results to.', 'gfgeo' );
		$tooltips['gfgeo_address_autocomplete_bounds_tt']         = __( 'Set bounds to display suggested results based on. Enter single or multiple sets of coordinates ( latitude and longitude comma separated ) when each set is follow by the | character.', 'gfgeo' );
		$tooltips['gfgeo_address_autocomplete_locator_bounds_tt'] = __( 'Display the address autocomplete suggested results based on the location returned from the page locator.', 'gfgeo' );
		$tooltips['gfgeo_address_autocomplete_placeholder_tt']    = __( 'Enter the placeholder text for the address autocomplete text field.', 'gfgeo' );
		$tooltips['gfgeo_address_autocomplete_desc_tt']           = __( 'Enter a description that you would like to display below the address autocomplete text field.', 'gfgeo' );

		return $tooltips;
	}

	/**
	 * New field default options
	 */
	public function set_default_labels() {
		?>
		case "gfgeo_geocoder" :
			field.label 					 		 = "Geocoder";
			field.gfgeo_page_locator 		 		 = false;
			field.gfgeo_location_found_message 		 = "";
			field.gfgeo_hide_location_failed_message = "";
			field.gfgeo_google_maps_link 	 		 = "";
			field.gfgeo_default_latitude 	 		 = "";
			field.gfgeo_default_longitude 	 		 = "";
			field.gfgeo_gmw_post_integration 		 = false;
			field.gfgeo_gmw_user_integration 		 = false;
			field.gfgeo_user_meta_field 	 		 = '';
			field.gfgeo_ip_locator_status 	   		 = "";
		break;

		case "gfgeo_locator_button" :
			field.label 					   		 = "Locator Button";
			field.gfgeo_locator_button_label   		 = "Get my current position";
			field.gfgeo_location_found_message 		 = "Location found.";
			field.gfgeo_hide_location_failed_message = "";
			field.gfgeo_ip_locator_status 	   		 = "";
		break;

		case "gfgeo_map" :
			field.label 			 	       = "Map";
			field.gfgeo_map_default_latitude   = "40.7827096";
			field.gfgeo_map_default_longitude  = "-73.965309";
			field.gfgeo_map_width  		 	   = "100%";
			field.gfgeo_map_height 		 	   = "300px";
			field.gfgeo_zoom_level 		 	   = "12";
			field.gfgeo_map_type   		 	   = "ROADMAP";
			field.gfgeo_map_styles   		   = "";
			field.gfgeo_map_icon         	   = "";
			field.gfgeo_draggable_marker 	   = true;
			field.gfgeo_set_marker_on_click    = false;
			field.gfgeo_map_scroll_wheel 	   = true;
			field.gfgeo_disable_address_output = false;
		break;

		case "gfgeo_address" :
			field.label 							 = "Address";
			field.gfgeo_address_autocomplete 		 = true;
			field.gfgeo_address_autocomplete_types 	 = '';
			field.gfgeo_address_autocomplete_bounds  = '';
			field.gfgeo_infield_locator_button 		 = true;
			field.gfgeo_location_found_message 		 = "Location found.";
			field.gfgeo_hide_location_failed_message = "";
			field.gfgeo_google_maps_link 	 		 = "";
		break;

		case "gfgeo_coordinates" :
			field.label 					  = "Coordinates";
			field.gfgeo_latitude_placeholder  = "Latitude";
			field.gfgeo_longitude_placeholder = "longitude";
			field.gfgeo_custom_field_method   = false;
		break;

		case "gfgeo_gmw_map_icons" :
			field.label = "Map Icons";
		break;
		<?php
	}

	/**
	 * On form load load scripts and styles
	 *
	 * @param  [type] $form [description].
	 * @return [type]       [description]
	 */
	public function render_form( $form ) {

		wp_enqueue_script( 'google-maps' );
		wp_enqueue_script( 'gfgeo-form-editor' );
		wp_enqueue_style( 'gfgeo' );

		return $form;
	}
}
$gfgeo_form_editor = new GFGEO_Form_Editor();
