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
	 *
	 * @return void
	 */
	public function __construct() {

		// abort if not editor page.
		if ( ! GFCommon::is_form_editor() ) {
			return;
		}

		// Gravity Forms version lower than 2.5.
		if ( ! GFGEO_GF_2_5 ) {

			add_action( 'gform_field_standard_settings', array( $this, 'fields_settings_2_4' ), 10, 2 );

			// Gravity Forms v2.5+.
		} else {

			add_filter( 'gform_field_settings_tabs', array( $this, 'geolocation_settings_tab' ), 50, 2 );
			add_action( 'gform_field_settings_tab_content', array( $this, 'geolocation_settings_tab_content' ), 50, 2 );
			add_action( 'gform_field_standard_settings', array( $this, 'standard_settings' ), 10, 2 );
			add_action( 'gform_field_appearance_settings', array( $this, 'appearance_settings' ), 10, 2 );
		}

		add_filter( 'gform_tooltips', array( $this, 'tooltips' ) );
		add_action( 'gform_editor_js_set_default_values', array( $this, 'set_default_labels' ) );
		add_action( 'gform_admin_pre_render', array( $this, 'render_form' ) );
		add_filter( 'gform_noconflict_scripts', array( $this, 'no_conflict_scripts' ) );
		add_filter( 'gform_noconflict_styles', array( $this, 'no_conflict_styles' ) );
	}

	/**
	 * Geolocation field options tab ( for Gravity Geolocation v2.5+ );
	 *
	 * @param  array $tabs core tabs.
	 *
	 * @param  array $form form data.
	 *
	 * @return [type]       [description]
	 */
	public function geolocation_settings_tab( $tabs, $form ) {

		$tabs[] = apply_filters(
			'gfgeo_field_settings_args',
			array(
				'id'             => 'gfgeo_geolocation',
				'title'          => esc_html__( 'Geolocation', 'gfgeo' ),
				'toggle_classes' => array(),
			)
		);

		return $tabs;
	}

	/**
	 * Geolocation field options tab content.
	 *
	 * @param  array $form form data.
	 *
	 * @param  int   $tab_id tab ID.
	 *
	 * @return [type]         [description]
	 */
	public function geolocation_settings_tab_content( $form, $tab_id ) {

		if ( 'gfgeo_geolocation' !== $tab_id ) {
			return;
		}
		?>
		<style type="text/css">
			#gfgeo_geolocation_tab_toggle {
				display: block ! important;
			}
		</style>
		<?php
		$this->geolocation_settings( $form );
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
	 * Auto locator field options.
	 *
	 * For Gravity Forms v2.5+
	 * Since 3.0
	 *
	 * @param  string $field [description].
	 */
	public function auto_locator_field_option( $field = '' ) {
		?>
		<?php $ip_locator_status = ! GFGEO_IP_LOCATOR ? 'disabled="disabled"' : ''; ?>

		<li class="field_setting gfgeo-locator-settings gfgeo-locator-button-field-settings gfgeo-geocoder-settings gfgeo-address-field-settings gfgeo-settings-group-wrapper">

			<ul class="gfgeo-settings-group-inner">

				<li class="gfgeo-setting gfgeo-ip-locator-status-setting gfgeo-locator-button-option">

					<label for="gfgeo-<?php echo '' !== $field ? $field . '-' : ''; // WPCS =: XSS ok. ?>ip-locator-status" class="section_label"> 
						<?php esc_html_e( 'IP Address Locator', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_ip_locator_status_tt' ); ?>
					</label>

					<select 
						<?php echo $ip_locator_status; // WPCS: XSS ok. ?>
						name="gfgeo_<?php echo '' !== $field ? $field . '_' : ''; // WPCS =: XSS ok. ?>ip_locator_status" 
						id="gfgeo-<?php echo '' !== $field ? $field . '-' : ''; // WPCS =: XSS ok. ?>ip-locator-status"
						class="gfgeo-<?php echo '' !== $field ? $field . '-' : ''; // WPCS =: XSS ok. ?>ip-locator-status fieldwidth-3"
						onchange="SetFieldProperty( 'gfgeo_<?php echo '' !== $field ? $field . '_' : ''; // WPCS =: XSS ok. ?>ip_locator_status', jQuery( this ).val() );">

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

				<li class="gfgeo-setting gfgeo-location-found-message gfgeo-locator-button-option">
					<label for="gfgeo-<?php echo '' !== $field ? $field . '-' : ''; // WPCS =: XSS ok. ?>location-found-message" class="section_label"> 
						<?php esc_html_e( 'Location Found Message', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_location_found_message_tt' ); ?>
					</label> 
					<input 
						type="text" 
						id="gfgeo-<?php echo '' !== $field ? $field . '-' : ''; // WPCS =: XSS ok. ?>location-found-message" 
						class="fieldwidth-3" 
						onkeyup="SetFieldProperty( 'gfgeo_<?php echo '' !== $field ? $field . '_' : ''; // WPCS =: XSS ok. ?>location_found_message', this.value );"
					/>
				</li>

				<li class="gfgeo-setting gfgeo-hide-location-failed-message gfgeo-locator-button-option">
					<input 
						type="checkbox" 
						id="gfgeo-hide-location-failed-message" 
						class="" 
						onclick="SetFieldProperty( 'gfgeo_hide_location_failed_message', this.checked );"
					/>
					<label for="gfgeo-hide-location-failed-message" class="inline"> 
						<?php esc_html_e( 'Hide Location Failed Message', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_hide_location_failed_message_tt' ); ?>
					</label> 
				</li>
		</ul>
	</li>
	<?php
	}

	/**
	 * Geolocation fields options added to the standard options area.
	 *
	 * For Gravity Forms v2.5+.
	 *
	 * Since 3.0
	 */
	public function standard_settings( $position, $form_id ) {

		$position = absint( $position );
		?>
		<?php if ( 10 === $position ) { ?> 
			<!-- Locator button options -->

			<li class="gfgeo-setting field_setting gfgeo-locator-button-field-settings gfgeo-locator-button-label-setting">

				<label for="gfgeo-locator-button-label" class="section_label"> 
					<?php esc_html_e( 'Button Label', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_locator_button_label_tt' ); ?>
				</label> 
				<input 
					type="text" 
					id="gfgeo-locator-button-label" 
					class="gfgeo-locator-button-label fieldwidth-3 gfgeo-dynamic-text-toggle"
					data-dynamic_text_target=".gfgeo-locator-button"
					data-dynamic_text_target_type="button"
					onkeyup="SetFieldProperty( 'gfgeo_locator_button_label', this.value );"
				/>
			</li>

			<!-- Reset Location button -->

			<li class="field_setting gfgeo-reset-location-button-field-settings gfgeo-setting gfgeo-reset-location-button-label">

				<label for="gfgeo-reset-location-button-label" class="section_label"> 
					<?php esc_html_e( 'Button Label', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_reset_location_button_label_tt' ); ?>
				</label> 
				<input 
					type="text" 
					size="35" 
					id="gfgeo-reset-location-button-label"
					data-dynamic_text_target=".gfgeo-reset-location-button"
					data-dynamic_text_target_type="button"
					class="gfgeo-dynamic-text-toggle" 
					onkeyup="SetFieldProperty( 'gfgeo_reset_location_button_label', this.value );"
				/>
			</li>

		<?php } ?>

		<?php if ( 700 === $position ) { ?> 

			<!-- Coordinates field --> 

			<li class="field_setting gfgeo-coordinates-field-settings gfgeo-settings-group-wrapper">

				<ul class="gfgeo-settings-group-inner">

					<li class="gfgeo-setting gfgeo-custom-field-method-setting">
						<input 
							type="checkbox" 
							id="gfgeo-custom-field-method"
							class="gfgeo-custom-field-method"
							onclick="SetFieldProperty( 'gfgeo_custom_field_method', this.checked );" 
						/>
						<label for="gfgeo-custom-field-method" class="inline"> 
							<?php esc_html_e( 'Save custom field as serialized array', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_custom_field_method_tt' ); ?>
						</label>
					</li>
				</ul>
			</li>

			<li class="field_setting gfgeo-geocoder-settings gfgeo-settings-group-wrapper">

				<ul class="gfgeo-settings-group-inner">

					<!-- <li class="gfgeo-section-label-wrapper">
						<label class="section_label">
							<?php esc_attr_e( 'Meta Fields Options', 'gfgeo' ); ?>
						</label>
					</li> -->

					<li class="gfgeo-setting gfgeo-user-meta-field-setting">
						<label for="gfgeo-user-meta-field" class="section_label"> 
							<?php esc_html_e( 'User Meta Field Name', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_user_meta_field_tt' ); ?>
						</label>

						<input 
							type="text" 
							id="gfgeo-user-meta-field" 
							class="gfgeo-user-meta-field fieldwidth-3" 
							onkeyup="SetFieldProperty( 'gfgeo_user_meta_field', this.value );"
						/>
					</li>

					<!-- geocoder meta fields -->

					<li class="gfgeo-setting gfgeo-geocoder-meta-field-setting">
						<input 
							type="checkbox" 
							id="gfgeo-geocoder-meta-field-setting-toggle"
							class="gfgeo-geocoder-meta-field-setting-toggle"
						/>
						<label for="gfgeo-geocoder-meta-field-setting-toggle" class="inline"> 
							<?php esc_html_e( 'Show Additional Meta Fields Options', 'gfgeo' ); ?>
							<?php gform_tooltip( 'gfgeo_geocoder_meta_fields_setup_tt' ); ?>
						</label>
						<?php GFGEO_Helper::custom_meta_fields( 'custom_field' ); ?>
					</li>

					<?php
					if ( class_exists( 'GEO_my_WP' ) ) {
						$disabled = false;
						$message  = '';
					} else {
						$disabled = true;
						$message  = __( 'This feature requires <a href="https://wordpress.org/plugins/geo-my-wp/" target="_blank">GEO my WP</a> plugin', 'gfgeo' );
					}
					?>

					<li class="gfgeo-section-label-wrapper">
						<label for="gfgeo-gmw-integration" class="section_label">
							<?php esc_attr_e( 'GEO my WP Integration', 'gfgeo' ); ?>
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-gmw-post-integration-trigger-wrapper">
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
							<?php esc_html_e( 'Integrate GMW Posts Locator', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_gmw_post_integration_tt' ); ?>
						</label>
						<small style="display: block;color: red;margin-top: 2px;"><?php echo $message; // WPCS: XSS ok. ?></small>
						<small class="gfgeo-single-geocoder-option-message"><?php echo esc_html( 'This feature can only be enabled in one geocoder field and it is already enabled in geocoder with ID ' ); ?><span></span></small>
					</li>

					<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-gmw-post-integration-wrapper">

						<div class="gfgeo-gmw-post-integration-phone gfgeo-multiple-left-setting">

							<label for="gfgeo-gmw-post-integration-phone" class="section_label"> 
								<?php esc_html_e( 'GMW Phone', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_gmw_post_integration_phone_tt' ); ?>
							</label> 
							<select 
								name="gfgeo_gmw_post_integration_phone" 
								id="gfgeo-gmw-post-integration-phone"
								class="gfgeo-gmw-post-integration-phone"
								onchange="SetFieldProperty( 'gfgeo_gmw_post_integration_phone', jQuery( this ).val() );"
							>
							<!-- values for this field generate by jquery function -->
							<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
							</select>
						</div>


						<div class="gfgeo-gmw-post-integration-fax gfgeo-multiple-right-setting">
							<label for="gfgeo-gmw-post-integration-fax" class="section_label"> 
								<?php esc_html_e( 'GMW Fax', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_gmw_post_integration_fax_tt' ); ?>
							</label> 
							<select 
								name="gfgeo_gmw_post_integration_fax" 
								id="gfgeo-gmw-post-integration-fax"
								class="gfgeo-gmw-post-integration-fax"
								onchange="SetFieldProperty( 'gfgeo_gmw_post_integration_fax', jQuery( this ).val() );"
							>
							<!-- values for this field generate by jquery function -->
							<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
							</select>
						</div>
					</li>

					<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-gmw-post-integration-wrapper ">

						<div class="gfgeo-gmw-post-integration-email gfgeo-multiple-left-setting">
							<label for="gfgeo-gmw-post-integration-email" class="section_label"> 
								<?php esc_html_e( 'GMW Email', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_gmw_post_integration_email_tt' ); ?>
							</label> 
							<select 
								name="gfgeo_gmw_post_integration_email" 
								id="gfgeo-gmw-post-integration-email"
								class="gfgeo-gmw-post-integration-email"
								onchange="SetFieldProperty( 'gfgeo_gmw_post_integration_email', jQuery( this ).val() );"
							>
							<!-- values for this field generate by jquery function -->
							<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
							</select>
						</div>

						<!-- GMW website  -->

						<div class="gfgeo-gmw-post-integration-website gfgeo-multiple-right-setting">
							<label for="gfgeo-gmw-post-integration-website" class="section_label"> 
								<?php esc_html_e( 'GMW Website', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_gmw_post_integration_website_tt' ); ?>
							</label> 
							<select 
								name="gfgeo_gmw_post_integration_website" 
								id="gfgeo-gmw-post-integration-website"
								class="gfgeo-gmw-post-integration-website"
								onchange="SetFieldProperty( 'gfgeo_gmw_post_integration_website', jQuery( this ).val() );"
							>
							<!-- values for this field generate by jquery function -->
							<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
							</select>
						</div>
					</li>

					<!-- GEO my WP User integrations -->
					<li class="gfgeo-setting gfgeo-gmw-user-integration">	

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
							<?php esc_html_e( 'Integrate GMW Users Locator', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_gmw_user_integration_tt' ); ?>
						</label>
						<small style="display: block;color: red;margin-top: 2px;"><?php echo $message; // WPCS: XSS ok. ?></small>
						<small class="gfgeo-single-geocoder-option-message"><?php echo esc_html( 'This feature can only be enabled in one geocoder field and it is already enabled in geocoder with ID ' ); ?><span></span></small>
					</li>

					<?php if ( ! $disabled && function_exists( 'gmw_get_registered_location_types' ) && class_exists( 'GMW_Multiple_Locations_Addon' ) ) { ?>

						<li class="gfgeo-setting gfgeo-gmw-user-location-usage">	

							<label for="gfgeo-gmw-user-location-usage" class="section_label"> 
								<?php esc_html_e( 'Location Usage', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_gmw_user_location_usage_tt' ); ?>
							</label>

							<select 
								name="gfgeo_gmw_user_location_usage" 
								id="gfgeo-gmw-user-location-usage"
								class="gfgeo-gmw-user-location-usage"
								onchange="SetFieldProperty( 'gfgeo_gmw_user_location_usage', jQuery( this ).val() );"
							>
								<!-- values for this field generate by jquery function -->
								<option value="update_location" selected="selected"><?php esc_html_e( 'Update existing location', 'gfgeo' ); ?></option>
								<option value="new_location"><?php esc_html_e( 'Create new location', 'gfgeo' ); ?></option>
								<option value="location_type"><?php esc_html_e( 'Sync with a location type', 'gfgeo' ); ?></option>

							</select>

						</li>

						<?php $location_types = gmw_get_registered_location_types(); ?>

						<li class="gfgeo-setting gfgeo-gmw-user-location-type">	

							<label for="gfgeo-gmw-user-location-type" class="section_label"> 
								<?php esc_html_e( 'Select Location Type', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_gmw_user_location_type_tt' ); ?>
							</label>
							<select 
								name="gfgeo_gmw_user_location_type" 
								id="gfgeo-gmw-user-location-type"
								class="gfgeo-gmw-user-location-type"
								onchange="SetFieldProperty( 'gfgeo_gmw_user_location_type', jQuery( this ).val() );"
							>
								<?php foreach ( $location_types as $location_type ) { ?>
									<option value="<?php echo $location_type->ID; ?>"><?php echo esc_html( $location_type->title ); ?></option>
								<?php } ?>
							</select>

						</li>

					<?php } ?>

				</ul>
			</li>

		<?php } ?>
		<?php
	}

	/**
	 * Geolocation fields options added to the Appearance options tab.
	 *
	 * For Gravity Forms v2.5+.
	 *
	 * @Since 3.0.
	 *
	 * @param  [type] $position [description].
	 * @param  [type] $form_id  [description].
	 */
	public function appearance_settings( $position, $form_id ) {

		$position = absint( $position );
		?>

		<?php if ( 20 === $position ) { ?>

			<li class="field_setting gfgeo-coordinates-field-settings gfgeo-settings-group-wrapper">

				<ul class="gfgeo-settings-group-inner">

					<li class="gfgeo-section-label-wrapper">
						<label class="section_label"> 
							<?php esc_html_e( 'Placeholders', 'gfgeo' ); ?> 
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-cooridnates-placeholder-options">

						<div class="gfgeo-setting gfgeo-latitude-placeholder-setting gfgeo-multiple-left-setting">

							<label for="gfgeo-latitude-placeholder" class="section_label"> 
								<?php esc_html_e( 'Latitude', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_latitude_placeholder_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-latitude-placeholder" 
								data-field="latitude"
								class="coordinates-placeholder gfgeo-dynamic-text-toggle"
								style="width: 100%"
								data-dynamic_text_target=".gfgeo-latitude-field"
								data-dynamic_text_target_type="placeholder"
								onkeyup="SetFieldProperty( 'gfgeo_latitude_placeholder', this.value );"
							/>
						</div>

						<!-- longitude placehoolder --> 
						<div class="gfgeo-setting gfgeo-latitude-placeholder-setting gfgeo-multiple-right-setting">
							<label for="gfgeo-longitude-placeholder" class="section_label"> 
								<?php esc_html_e( 'Longitude', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_longitude_placeholder_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-longitude-placeholder" 
								class="coordinates-placeholder gfgeo-dynamic-text-toggle"
								data-field="longitude"
								style="width: 100%"
								data-dynamic_text_target=".gfgeo-longitude-field"
								data-dynamic_text_target_type="placeholder"
								onkeyup="SetFieldProperty( 'gfgeo_longitude_placeholder', this.value );" />
						</div>
					</li>
				</ul>
			</li>
		<?php } ?>

		<?php
	}

	/**
	 * Geolocation fields options for Gravity Forms v2.5+.
	 *
	 * @param array $form form data.
	 */
	public function geolocation_settings( $form ) {

		?>
		<!-- gecoder fields ID option --->

		<li class="gfgeo-setting gfgeo-no-geolocation-options">
			<label for="gfgeo-geocoder-id" class="section_label"> 
				<?php esc_html_e( 'Geolocation options are not available for this field.', 'gfgeo' ); ?> 
			</label> 
		</li>

		<li class="field_setting gfgeo-setting gfgeo-geocoder-id">
			<label for="gfgeo-geocoder-id" class="section_label"> 
				<?php esc_html_e( 'Geocoder ID', 'gfgeo' ); ?> 
				<?php gform_tooltip( 'gfgeo_geocoder_id_tt' ); ?>
			</label> 
			<select 
				name="gfgeo_geocoder_id" 
				id="gfgeo-geocoder-id"
				class="gfgeo-geocoder-id single-geocoder fieldwidth-3"
				onchange="SetFieldProperty( 'gfgeo_geocoder_id', jQuery( this ).val() );"
			>
			<!-- values for this field generate by jquery function -->
			<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
			</select>
		</li>

		<!-- gecoder fields ID - multiple geocoders  -->

		<li class="field_setting gfgeo-setting gfgeo-geocoder-id-multiple">
			<label for="gfgeo-geocoder-id" class="section_label"> 
				<?php esc_html_e( 'Geocoder IDs', 'gfgeo' ); ?> 
				<?php gform_tooltip( 'gfgeo_geocoder_ids_tt' ); ?>
			</label> 
			<select 
				multiple="multiple"
				name="gfgeo_geocoder_id" 
				id="gfgeo-geocoder-id"
				class="gfgeo-geocoder-id multiple-geocoders fieldwidth-3"
				onchange="SetFieldProperty( 'gfgeo_geocoder_id', jQuery( this ).val() );"
			>
			<!-- values for this field generate by jquery function -->
			</select>
		</li>

		<!-- disable geocoding - for advanced address field -->

		<li class="field_setting gfgeo-setting gfgeo-disable-field-geocoding-setting">
			<input 
				type="checkbox" 
				id="gfgeo-disable-field-geocoding" 
				onclick="SetFieldProperty( 'gfgeo_disable_field_geocoding', this.checked );jQuery( this ).closest( 'li' ).find( 'em' ).slideToggle();" 
			/>
			<label for="gfgeo-disable-field-geocoding" class="inline"> 
				<?php esc_html_e( 'Disable Geocoding ( use as dynamic field only )', 'gfgeo' ); ?> 
				<?php gform_tooltip( 'gfgeo_disable_field_geocoding_tt' ); ?>
			</label>
			<br />
			<!--<em style="font-size: 11px;color: red;margin-top: 5px;display: none"><?php esc_html_e( 'Note: when this checkbox is checked, the locator button and address autocomplete features below will be ignored.', 'gfgeo' ); ?></em> -->
		</li>

		<!-- Locator options for geocoder field -->

		<li class="field_setting gfgeo-section-label-wrapper gfgeo-geocoder-settings">
			<label class="section_label">
				<?php esc_attr_e( 'Auto Locator', 'gfgeo' ); ?>
				<?php gform_tooltip( 'gfgeo_page_locator_label_tt' ); ?>
			</label>
		</li>

		<li class="field_setting gfgeo-setting gfgeo-geocoder-settings gfgeo-page-locator-setting">

			<input 
				type="checkbox" 
				id="gfgeo-page-locator"
				class="gfgeo-page-locator"
				onclick="SetFieldProperty( 'gfgeo_page_locator', this.checked );"
			/>
			<label for="gfgeo-page-locator" class="inline"> 
				<?php esc_html_e( 'Enable Auto Locator', 'gfgeo' ); ?> 
				<?php gform_tooltip( 'gfgeo_page_locator_tt' ); ?>
			</label>
			<small class="gfgeo-single-geocoder-option-message"><?php echo esc_html( 'This feature can only be enabled in one geocoder field and it is already enabled in geocoder with ID ' ); ?><span></span></small>
		</li>

		<!-- Locator options for address field -->

		<li class="field_setting gfgeo-address-field-settings gfgeo-infield-locator-button">

			<div class="gfgeo-section-label-wrapper">
				<label for="gfgeo-locator-button" class="section_label">
					<?php esc_attr_e( 'Locator Button', 'gfgeo' ); ?>
				</label>
			</div>

			<input 
				type="checkbox" 
				id="gfgeo-infield-locator-button" 
				onclick="SetFieldProperty( 'gfgeo_infield_locator_button', this.checked);" onkeypress="SetFieldProperty( 'gfgeo_infield_locator_button', this.checked );"
			/>
			<label for="gfgeo-infield-locator-button" class="inline"> 
				<?php esc_html_e( 'Enable locator button', 'gfgeo' ); ?> 
				<?php gform_tooltip( 'gfgeo_infield_locator_button_tt' ); ?>
			</label>
		</li>

		<?php $this->auto_locator_field_option(); ?>

		<!-- Geocoder field settings -->

		<li class="field_setting gfgeo-geocoder-settings gfgeo-settings-group-wrapper">

			<ul class="gfgeo-settings-group-inner">

				<li class="gfgeo-section-label-wrapper">
					<label class="section_label">
						<?php esc_attr_e( 'Default Coordinates', 'gfgeo' ); ?>
						<?php gform_tooltip( 'gfgeo_default_coordinates_label_tt' ); ?>
					</label>
				</li>

				<li class="gfgeo-setting gfgeo-multiple-settings">

					<div class="gfgeo-setting gfgeo-multiple-left-setting">
						<label for="gfgeo-default-latitude" class="section_label"> 
							<?php esc_html_e( 'Default Latitude', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_default_latitude_tt' ); ?>
						</label> 
						<input 
							type="text" 
							id="gfgeo-default-latitude" 
							class="gfgeo-default-latitude"
							onkeyup="SetFieldProperty( 'gfgeo_default_latitude', this.value );">
					</div>

					<div class="gfgeo-multiple-right-setting">
						<label for="gfgeo-default-longitude" class="section_label"> 
							<?php esc_html_e( 'Default Longitude', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_default_longitude_tt' ); ?>
						</label> 
						<input 
							type="text" 
							id="gfgeo-default-longitude" 
							class="gfgeo-default-longitude"
							onkeyup="SetFieldProperty( 'gfgeo_default_longitude', this.value );">
					</div>
				</li>

				<li class="gfgeo-section-label-wrapper">
					<label class="section_label">
						<?php esc_attr_e( 'Map Marker', 'gfgeo' ); ?>
					</label>
				</li>

				<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-map-marker-default-coords-setting">

					<div class="gfgeo-setting gfgeo-map-marker-default-latitude-setting gfgeo-multiple-left-setting">
						<label for="gfgeo-map-marker-default-latitude" class="section_label"> 
							<?php esc_html_e( 'Default Latitude', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_map_marker_default_latitude_tt' ); ?>
						</label> 
						<input 
							type="text" 
							id="gfgeo-map-marker-default-latitude"
							class="gfgeo-map-marker-default-latitude"
							style="width:100%;"
							onkeyup="SetFieldProperty( 'gfgeo_map_marker_default_latitude', this.value );"
						/>
					</div>

					<div class="gfgeo-setting gfgeo-map-marker-default-longitude-setting gfgeo-multiple-right-setting">
						<label for="gfgeo-map-marker-default-longitude" class="section_label"> 
							<?php esc_html_e( 'Default Longitude', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_map_marker_default_longitude_tt' ); ?>
						</label> 
						<input 
							type="text" 
							id="gfgeo-map-marker-default-longitude" 
							class="gfgeo-map-marker-default-longitude"
							style="width:100%;"
							onkeyup="SetFieldProperty( 'gfgeo_map_marker_default_longitude', this.value );"
						/>
					</div>
				</li>

				<li class="gfgeo-setting gfgeo-map-marker-url-settings">
					<label for="gfgeo-map-marker-url" class="section_label"> 
						<?php esc_html_e( 'Marker URL', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_map_marker_url_tt' ); ?>
					</label> 
					<input 
						type="text"
						id="gfgeo-map-marker-url" 
						class="gfgeo-map-marker-url fieldwidth-3" 
						onkeyup="SetFieldProperty( 'gfgeo_map_marker_url', this.value );">
				</li>

				<li class="gfgeo-setting gfgeo-marker-info-window-setting">
					<label for="gfgeo-marker-info-window" class="section_label"> 
						<?php esc_html_e( 'Marker Info Window Content', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_marker_info_window_tt' ); ?>
					</label> 
					<input 
						type="text"
						id="gfgeo-marker-info-window" 
						class="gfgeo-marker-info-window fieldwidth-3" 
						onkeyup="SetFieldProperty( 'gfgeo_marker_info_window', this.value );">
				</li>

				<li class="gfgeo-setting gfgeo-map-marker-hidden-setting">
					<input 
						type="checkbox"
						id="gfgeo-map-marker-hidden" 
						class="gfgeo-map-marker-hidden" 
						onClick="SetFieldProperty( 'gfgeo_map_marker_hidden', this.checked );"
					/>
					<label for="gfgeo-map-marker-hidden" class="inline"> 
						<?php esc_html_e( 'Hide Marker on Initial Load', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_map_marker_hidden_tt' ); ?>
					</label> 
				</li>

				<li class="gfgeo-setting gfgeo-disable-marker-drag-setting">
					<input 
						type="checkbox" 
						id="gfgeo-disable-marker-drag"
						class="gfgeo-disable-marker-drag"
						onclick="SetFieldProperty( 'gfgeo_disable_marker_drag', this.checked );" 
					/>
					<label for="gfgeo-disable-marker-drag" class="inline"> 
						<?php esc_html_e( 'Disable Marker Drag', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_disable_marker_drag_tt' ); ?>
					</label>
				</li>

				<li class="gfgeo-setting gfgeo-move-marker-via-map-click-setting">
					<input 
						type="checkbox" 
						id="gfgeo-move-marker-via-map-click"
						class="gfgeo-move-marker-via-map-click"
						onclick="SetFieldProperty( 'gfgeo_move_marker_via_map_click', this.checked );" 
					/>
					<label for="gfgeo-move-marker-via-map-click" class="inline"> 
						<?php esc_html_e( 'Move Marker Using Map Click', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_move_marker_via_map_click_tt' ); ?>
					</label>
				</li>

				<li class="gfgeo-setting gfgeo-address-output-disabled-setting">
					<input 
						type="checkbox" 
						id="gfgeo-address-output-disabled" 
						onclick="SetFieldProperty( 'gfgeo_address_output_disabled', this.checked );"
					/>
					<label for="gfgeo-address-output-disabled" class="inline"> 
						<?php esc_html_e( 'Disable Address Output', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_address_output_disabled_tt' ); ?>
					</label>
				</li>
			</ul>
		</li>

		<!-- Deprecated directions -->
		<li class="field_setting gfgeo-settings-group-wrapper gfgeo-geocoder-field-directions-settings-group">

			<ul class="gfgeo-settings-group-inner">

				<li class="gfgeo-section-label-wrapper">
					<label for="gfgeo-distance" class="section_label">
						<?php esc_attr_e( 'Driving Directions/Distance & Routes', 'gfgeo' ); ?>
					</label>
				</li>
				<li class="gfgeo-setting">
					<em class="gfgeo-deprecated-message">The driving directions feature of the Geocoder field is deprecated. Please use the Directions field instead and set the "Destination Geocoder" option in this field to "Disabled".</em>
				</li>

				<li class="gfgeo-setting">

					<label for="gfgeo-distance-destination-geocoder-id" class="section_label"> 
						<?php esc_html_e( 'Destination Geocoder', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_distance_destination_geocoder_id_tt' ); ?>
					</label> 
					<select 
						name="gfgeo_distance_destination_geocoder_id" 
						id="gfgeo-distance-destination-geocoder-id"
						class="gfgeo-distance-destination-geocoder-id fieldwidth-3"
						onchange="SetFieldProperty( 'gfgeo_distance_destination_geocoder_id', jQuery( this ).val() );"
					>
					<!-- values for this field generate by jquery function -->
					<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
					</select>
				</li>

				<li class="gfgeo-setting">
					<label for="gfgeo-distance-travel-mode" class="section_label"> 
						<?php esc_html_e( 'Travel Mode', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_distance_travel_mode_tt' ); ?>
					</label> 

					<select 
						name="gfgeo_distance_travel_mode" 
						id="gfgeo-distance-travel-mode"
						class="gfgeo-distance-travel-mode fieldwidth-3"
						onchange="SetFieldProperty( 'gfgeo_distance_travel_mode', jQuery( this ).val() );"
					>
						<option value="DRIVING"><?php esc_html_e( 'Driving', 'gfgeo' ); ?></option>
						<option value="WALKING"><?php esc_html_e( 'Walking', 'gfgeo' ); ?></option>
						<option value="BICYCLING"><?php esc_html_e( 'Bicycling', 'gfgeo' ); ?></option>
						<option value="TRANSIT"><?php esc_html_e( 'Transit', 'gfgeo' ); ?></option>
					</select>
				</li>

				<li class="gfgeo-setting">
					<label for="gfgeo-distance-unit-system" class="section_label"> 
						<?php esc_html_e( 'Unit System', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_distance_unit_system_tt' ); ?>
					</label> 
					<select 
						name="gfgeo_distance_unit_system" 
						id="gfgeo-distance-unit-system"
						class="gfgeo-distance-unit-system fieldwidth-3"
						onchange="SetFieldProperty( 'gfgeo_distance_unit_system', jQuery( this ).val() );"
					>
						<option value="imperial"><?php esc_html_e( 'Imperial ( Miles )', 'gfgeo' ); ?></option>
						<option value="metric"><?php esc_html_e( 'Metric ( Kilometers )', 'gfgeo' ); ?></option>
					</select>
				</li>

				<li class="gfgeo-setting">
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

				<li class="gfgeo-setting">
					<label for="gfgeo-distance-directions-panel-id" class="section_label"> 
						<?php esc_html_e( 'Display Driving Directions', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_distance_directions_panel_id_tt' ); ?>
					</label> 
					<select 
						name="gfgeo_distance_directions_panel_id" 
						id="gfgeo-distance-directions-panel-id"
						class="gfgeo-distance-directions-panel-id fieldwidth-3"
						onchange="SetFieldProperty( 'gfgeo_distance_directions_panel_id', jQuery( this ).val() );"
					>
					<!-- values for this field generate by jquery function -->
					<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
					</select>
				</li>
			</ul>

		</li>	

		<!-- Address Field -->

		<li class="field_setting gfgeo-address-field-settings gfgeo-settings-group-wrapper">

			<ul class="gfgeo-settings-group-inner">

				<li class="gfgeo-section-label-wrapper">
					<label class="section_label">
						<?php esc_attr_e( 'Google Places Address Autocomplete', 'gfgeo' ); ?>
						<?php gform_tooltip( 'gfgeo_address_autocomplete_feature_tt' ); ?>
					</label>
				</li>

				<li class="gfgeo-setting gfgeo-address-autocomplete-setting">
					<input 
						type="checkbox" 
						id="gfgeo-address-autocomplete"
						class="gfgeo-address-autocomplete"
						onclick="SetFieldProperty( 'gfgeo_address_autocomplete', this.checked );" 
					/>
					<label for="gfgeo-address-autocomplete" class="inline"> 
						<?php esc_html_e( 'Enable Address Autocomplete', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_address_autocomplete_tt' ); ?>
					</label>
				</li>

				<li id="gfgeo-address-autocomplete-options-wrapper">

					<ul>
						<li class="gfgeo-setting gfgeo-force-autocomplete-selection-setting">
							<input
								type="checkbox"
								id="gfgeo-force-autocomplete-selection"
								class="gfgeo-force-autocomplete-selection"
								onclick="SetFieldProperty( 'gfgeo_force_autocomplete_selection', this.checked );"
							/>
							<label for="gfgeo-force-autocomplete-selection" class="inline">
								<?php esc_html_e( 'Force Address Selection From Autocomplete', 'gfgeo' ); ?>
								<?php gform_tooltip( 'gfgeo_force_autocomplete_selection_tt' ); ?>
							</label>
						</li>

						<li class="gfgeo-setting gfgeo-force-autocomplete-selection-message-setting">
							<label for="gfgeo-force-autocomplete-selection-message" class="section_label">
								<?php esc_html_e( 'Force Address Selection Message', 'gfgeo' ); ?>
								<?php gform_tooltip( 'gfgeo_force_autocomplete_selection_message_tt' ); ?>
							</label>
							<input 
								type="text" 
								id="gfgeo-force-autocomplete-selection-message"
								class="gfgeo-force-autocomplete-selection-message fieldwidth-3"
								onkeyup="SetFieldProperty( 'gfgeo_force_autocomplete_selection_message', this.value );"
							/>
						</li>

						<!--
						<li class="field_setting gfgeo-setting gfgeo-address-autocomplete-usage-setting">
							<label for="gfgeo-address-autocomplete-usage" class="section_label"> 
								<?php esc_html_e( 'Autocomplete Usage', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_address_autocomplete_usage_tt' ); ?>
							</label>
							<select 
								name="gfgeo_address_autocomplete_usage" 
								id="gfgeo-address-autocomplete-usage"
								class="gfgeo-address-autocomplete-usage fieldwidth-3"
								onchange="SetFieldProperty( 'gfgeo_address_autocomplete_usage', jQuery( this ).val() );"
							>
								<option value="extra_field"><?php esc_html_e( 'Add additional text field for the autocomplete', 'gfgeo' ); ?></option>
								<option value="same_field"><?php esc_html_e( 'Use the first text field of the Address Field', 'gfgeo' ); ?></option>
							</select>
						</li>

						<li class="field_setting gfgeo-setting gfgeo-address-autocomplete-placeholder-setting">
							<label for="gfgeo-address-autocomplete-placeholder" class="section_label"> 
								<?php esc_html_e( 'Autocomplete Placeholder', 'gfgeo' ); ?> 
									<?php gform_tooltip( 'gfgeo_address_autocomplete_placeholder_tt' ); ?>
							</label> 
							<input 
								type="text" 
								class="gfgeo-address-autocomplete-placeholder fieldwidth-3"
								id="gfgeo-address-autocomplete-placeholder"
								onkeyup="SetFieldProperty( 'gfgeo_address_autocomplete_placeholder', this.value );"
							/>
						</li>

						<li class="field_setting gfgeo-setting gfgeo-address-autocomplete-desc-setting">
							<label for="gfgeo-address-autocomplete-desc" class="section_label"> 
								<?php esc_html_e( 'Autocomplete Field description', 'gfgeo' ); ?> 
									<?php gform_tooltip( 'gfgeo_address_autocomplete_desc_tt' ); ?>
							</label> 
							<input 
								type="text" 
								class="fieldwidth-3 gfgeo-address-autocomplete-desc"
								id="gfgeo-address-autocomplete-desc"
								onkeyup="SetFieldProperty('gfgeo_address_autocomplete_desc', this.value);">
						</li>
						-->
						<li class="gfgeo-setting gfgeo-address-autocomplete-types-setting">
							<label for="gfgeo-address-autocomplete-types" class="section_label"> 
								<?php esc_html_e( 'Autocomplete Results Types', 'gfgeo' ); ?>
								<?php gform_tooltip( 'gfgeo_address_autocomplete_types_tt' ); ?>
							</label>
							<select 
								name="gfgeo_address_autocomplete_types" 
								id="gfgeo-address-autocomplete-types"
								class="gfgeo-address-autocomplete-types fieldwidth-3"
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

						<li class="gfgeo-section-label-wrapper">
							<label class="section_label" class="section_label"> 
								<?php esc_html_e( 'Address Autocomplete Restrictions', 'gfgeo' ); ?>
								<?php gform_tooltip( 'gfgeo_autocomplete_restriction_usage_tt' ); ?>
							</label>
						</li>

						<li class="gfgeo-setting gfgeo-address-autocomplete-restriction-usage">
							<!-- <label for="gfgeo-autocomplete-restriction-usage"> 
								<?php esc_html_e( 'Restriction Type', 'gfgeo' ); ?>
								<?php gform_tooltip( 'gfgeo_autocomplete_restriction_usage_tt' ); ?>
							</label> 
							&#32;&#32; -->
							<select 
								name="gfgeo_autocomplete_restriction_usage" 
								id="gfgeo-autocomplete-restriction-usage"
								class="gfgeo-autocomplete-restriction-usage fieldwidth-3"
								onchange="SetFieldProperty( 'gfgeo_autocomplete_restriction_usage', jQuery( this ).val() );"
							>	
								<option value="">Disabled</option>
								<option value="countries">Countries</option>
								<option value="proximity">Proximity</option>
								<option value="area_bounds">Area Bounds</option>
								<option value="page_locator">Auto-Locator Bounds</option>
							</select>
						</li>

						<li class="gfgeo-setting gfgeo-address-autocomplete-country-setting restriction-usage-option option-countries">
							<label for="gfgeo-address-autocomplete-country" class="section_label"> 
								<?php esc_html_e( 'Restrict By Countries', 'gfgeo' ); ?>
								<?php gform_tooltip( 'gfgeo_address_autocomplete_country_tt' ); ?>
							</label> 
							<select 
								multiple="multiple"
								name="gfgeo_address_autocomplete_country" 
								id="gfgeo-address-autocomplete-country"
								class="gfgeo-address-autocomplete-country fieldwidth-3"
								onchange="SetFieldProperty( 'gfgeo_address_autocomplete_country', jQuery(this).val());"
							>
							<?php
							foreach ( GFGEO_Helper::get_countries() as $value => $name ) {
								echo '<option value="' . $value . '">' . $name . '</option>'; // WPCS: XSS ok.
							}
							?>
							</select>
						</li>

						<li class="gfgeo-setting gfgeo-address-autocomplete-locator-bounds-setting restriction-usage-option option-page_locator">
							<input 
								type="checkbox" 
								id="gfgeo-address-autocomplete-locator-bounds"
								class="gfgeo-address-autocomplete-locator-bounds" 
								onclick="SetFieldProperty( 'gfgeo_address_autocomplete_locator_bounds', this.checked );" 
							/>
							<label for="gfgeo-address-autocomplete-locator-bounds" class="inline"> 
								<?php esc_html_e( 'Enable Page Locator Bounds', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_address_autocomplete_locator_bounds_tt' ); ?>
							</label>
						</li>

						<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-autocomplete-proximity-restriction-settings restriction-usage-option option-proximity">

							<div class="gfgeo-section-label-wrapper">
								<label for="gfgeo-autocomplete-proximity-lat" class="section_label"> 
									<?php esc_html_e( 'Restrict By Proximity', 'gfgeo' ); ?>
									<?php gform_tooltip( 'gfgeo_autocomplete_proximity_restriction_tt' ); ?>
								</label>
							</div>

							<div class="gfgeo-setting gfgeo-autocomplete-proximity-lat gfgeo-multiple-left-setting">
								<label for="gfgeo-autocomplete-proximity-lat" class="section_label"> 
									<?php esc_html_e( 'Latitude', 'gfgeo' ); ?> 
									<?php gform_tooltip( 'gfgeo_autocomplete_proximity_lat_tt' ); ?>
								</label> 
								<input 
									type="text" 
									id="gfgeo-autocomplete-proximity-lat" 
									style="width:100%;"
									class="gfgeo-autocomplete-proximity-lat"
									onkeyup="SetFieldProperty( 'gfgeo_autocomplete_proximity_lat', this.value );"
									placeholder="26.423277"
								/>
							</div>

							<div class="gfgeo-setting gfgeo-autocomplete-proximity-lng gfgeo-multiple-right-setting options-proximity">
								<label for="gfgeo-autocomplete-proximity-lng" class="section_label"> 
									<?php esc_html_e( 'Longitude', 'gfgeo' ); ?> 
									<?php gform_tooltip( 'gfgeo_autocomplete_proximity_lng_tt' ); ?>
								</label> 
								<input 
									type="text" 
									id="gfgeo-autocomplete-proximity-lng" 
									style="width:100%;"
									class="gfgeo-autocomplete-proximity-lng"
									onkeyup="SetFieldProperty( 'gfgeo_autocomplete_proximity_lng', this.value );"
									placeholder="-82.0217760"
								/>
							</div>
						</li>

						<li class="gfgeo-setting gfgeo-autocomplete-proximity-radius restriction-usage-option option-proximity">

							<label for="gfgeo-autocomplete-proximity-radius" class="section_label"> 
								<?php esc_html_e( 'Radius', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_autocomplete_proximity_radius_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-autocomplete-proximity-radius" 
								style="width:100%;"
								class="gfgeo-autocomplete-proximity-radius"
								onkeyup="SetFieldProperty( 'gfgeo_autocomplete_proximity_radius', this.value );"
								placeholder="100"
							/>
						</li>

						<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-autocomplete-bounds-settings restriction-usage-option option-area_bounds">

							<div class="gfgeo-section-label-wrapper">
								<label for="gfgeo-autocomplete-bounds-sw-point" class="section_label"> 
									<?php esc_html_e( 'Restrict By Area', 'gfgeo' ); ?>
									<?php gform_tooltip( 'gfgeo_autocomplete_bounds_restriction_tt' ); ?>
								</label> 
							</div>

							<div class="gfgeo-setting gfgeo-autocomplete-bounds-sw-point gfgeo-multiple-left-setting">
								<label for="gfgeo-autocomplete-bounds-sw-point" class="section_label"> 
									<?php esc_html_e( 'Southwest Point', 'gfgeo' ); ?> 
									<?php gform_tooltip( 'gfgeo_bounds_sw_point_tt' ); ?>
								</label> 
								<input 
									type="text" 
									id="gfgeo-autocomplete-bounds-sw-point" 
									style="width:100%;"
									class="gfgeo-autocomplete-bounds-sw-point"
									onkeyup="SetFieldProperty( 'gfgeo_autocomplete_bounds_sw_point', this.value );"
									placeholder="26.423277,-82.137132"
								/>
							</div>

							<div class="gfgeo-setting gfgeo-autocomplete-bounds-ne-point gfgeo-multiple-right-setting options-area_bounds">
								<label for="gfgeo-autocomplete-bounds-ne-point" class="section_label"> 
									<?php esc_html_e( 'Northeast Point', 'gfgeo' ); ?> 
									<?php gform_tooltip( 'gfgeo_bounds_ne_point_tt' ); ?>
								</label> 
								<input 
									type="text" 
									id="gfgeo-autocomplete-bounds-ne-point" 
									style="width:100%;"
									class="gfgeo-autocomplete-bounds-ne-point"
									onkeyup="SetFieldProperty( 'gfgeo_autocomplete_bounds_ne_point', this.value );"
									placeholder="26.4724595,-82.0217760"
								/>
							</div>
						</li>

						<li class="gfgeo-setting gfgeo-address-autocomplete-strict-bounds-setting restriction-usage-option option-area_bounds option-proximity">
							<input 
								type="checkbox" 
								id="gfgeo-address-autocomplete-strict-bounds"
								class="gfgeo-address-autocomplete-strict-bounds"
								onclick="SetFieldProperty( 'gfgeo_address_autocomplete_strict_bounds', this.checked );" 
							/>
							<label for="gfgeo-address-autocomplete-strict-bounds" class="inline"> 
								<?php esc_html_e( 'Limit Suggested Results To Restricted Area', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_address_autocomplete_restrict_bounds_tt' ); ?>
							</label>
						</li>
					</ul>
				</li>
			</ul>
		</li>

		<!--  Map field options -->

		<li class="field_setting gfgeo-map-settings gfgeo-settings-group-wrapper">

			<ul class="gfgeo-settings-group-inner">

				<li class="gfgeo-section-label-wrapper">
					<label class="section_label"> 
						<?php esc_html_e( 'Default Coordinates', 'gfgeo' ); ?>
						<?php gform_tooltip( 'gfgeo_map_default_coordinates_tt' ); ?>
					</label>
				</li>

				<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-map-default-coordiantes-settings">

					<div class="gfgeo-setting gfgeo-map-default-latitude-setting gfgeo-multiple-left-setting">
						<label for="gfgeo-map-default-latitude" class="section_label"> 
							<?php esc_html_e( 'Default Latitude', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_map_default_latitude_tt' ); ?>
						</label> 
						<input 
							type="text" 
							id="gfgeo-map-default-latitude" 
							style="width:100%;"
							class="gfgeo-map-default-latitude"
							onkeyup="SetFieldProperty( 'gfgeo_map_default_latitude', this.value );"
						/>
					</div>

					<div class="gfgeo-setting gfgeo-map-default-longitude-setting gfgeo-multiple-right-setting">
						<label for="gfgeo-map-default-longitude" class="section_label"> 
							<?php esc_html_e( 'Default Longitude', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_map_default_longitude_tt' ); ?>
						</label> 
						<input 
							type="text" 
							id="gfgeo-map-default-longitude" 
							class="gfgeo-map-default-longitude"
							style="width:100%;"
							onkeyup="SetFieldProperty( 'gfgeo_map_default_longitude', this.value );"
						/>
					</div>
				</li>

				<li class="gfgeo-section-label-wrapper">
					<label class="section_label"> 
						<?php esc_html_e( 'Map Options', 'gfgeo' ); ?>
					</label>
				</li>

				<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-map-options-settings">

					<div class="gfgeo-setting gfgeo-map-type-setting gfgeo-multiple-left-setting">
						<label for="gfgeo-map-type" class="section_label">
							<?php esc_html_e( 'Map Type', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_map_type_tt' ); ?>
						</label> 
						<select 
							name="gfgeo_map_type" 
							id="gfgeo-map-type"
							class="gfgeo-map-type"
							onchange="SetFieldProperty( 'gfgeo_map_type', jQuery(this).val() );"
						>
								<option value="ROADMAP">ROADMAP</option>
								<option value="SATELLITE">SATELLITE</option>
								<option value="HYBRID">HYBRID</option>
								<option value="TERRAIN">TERRAIN</option>
						</select>
					</div>

					<div class="gfgeo-setting gfgeo-zoom-level-setting gfgeo-multiple-right-setting">
						<label for="gfgeo-zoom-level" class="section_label"> 
							<?php esc_html_e( 'Zoom Level', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_zoom_level_tt' ); ?>
						</label> 
						<select 
							name="gfgeo_zoom_level"
							class="gfgeo-zoom-level"
							id="gfgeo-zoom-level"
							onchange="SetFieldProperty( 'gfgeo_zoom_level', jQuery(this).val() );"
						>
							<?php $count = 18; ?>
							<?php
							for ( $x = 1; $x <= 18; $x++ ) {
								echo '<option value="' . $x . '">' . $x . '</option>'; // WPCS: XSS ok.
							}
							?>
						</select>
					</div>
				</li>

				<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-map-size-options">

					<div class="gfgeo-setting gfgeo-map-width-setting gfgeo-multiple-left-setting">
						<label for="gfgeo-map-width" class="section_label"> 
							<?php esc_html_e( 'Map Width', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_map_width_tt' ); ?>
						</label> 
						<input 
							type="text" 
							id="gfgeo-map-width" 
							class="gfgeo-map-width"  
							onkeyup="SetFieldProperty( 'gfgeo_map_width', this.value );"
						/>
					</div>

					<div class="gfgeo-setting gfgeo-map-height-setting gfgeo-multiple-right-setting">
						<label for="gfgeo-map-height" class="section_label"> 
							<?php esc_html_e( 'Map Height', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_map_height_tt' ); ?>
						</label> 
						<input 
							type="text" 
							id="gfgeo-map-height" 
							class="gfgeo-map-height" 
							onkeyup="SetFieldProperty( 'gfgeo_map_height', this.value );">
					</div>
				</li>

				<li class="gfgeo-setting gfgeo-map-styles-setting">
					<label for="gfgeo-map-styles" class="section_label"> 
						<?php esc_html_e( 'Map Styles', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_map_styles_tt' ); ?>
					</label>
					<textarea 
						id="gfgeo-map-styles" 
						class="gfgeo-map-styles fieldwidth-3 fieldheight-2" 
						onblur="SetFieldProperty( 'gfgeo_map_styles', this.value );"></textarea>
				</li>

				<li class="gfgeo-setting gfgeo-map-scroll-wheel-setting">
					<input 
						type="checkbox" 
						id="gfgeo-map-scroll-wheel"
						class="gfgeo-map-scroll-wheel"
						onclick="SetFieldProperty( 'gfgeo_map_scroll_wheel', this.checked );" 
					/>
					<label for="gfgeo-map-scroll-wheel" class="inline"> 
						<?php esc_html_e( 'Enable Mouse Scroll-Wheel Zoom', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_map_scroll_wheel_tt' ); ?>
					</label>
				</li>

				<li class="gfgeo-section-label-wrapper">
					<label class="section_label"> 
						<?php esc_html_e( 'Map Bounds Restriction', 'gfgeo' ); ?>
						<?php gform_tooltip( 'gfgeo_map_bounds_restriction_tt' ); ?>
					</label>
				</li>

				<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-map-strict-bounds-settings">

					<div class="gfgeo-setting gfgeo-map-bounds-sw-point gfgeo-multiple-left-setting">
						<label for="gfgeo-map-bounds-sw-point" class="section_label"> 
							<?php esc_html_e( 'Southwest Point', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_bounds_sw_point_tt' ); ?>
						</label> 
						<input 
							type="text" 
							id="gfgeo-map-bounds-sw-point" 
							style="width:100%;"
							class="gfgeo-map-bounds-sw-point"
							onkeyup="SetFieldProperty( 'gfgeo_map_bounds_sw_point', this.value );"
							placeholder="26.423277,-82.137132"
						/>
					</div>

					<div class="gfgeo-setting gfgeo-map-bounds-ne-point gfgeo-multiple-right-setting">
						<label for="gfgeo-map-bounds-ne-point" class="section_label"> 
							<?php esc_html_e( 'Northeast Point', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_bounds_ne_point_tt' ); ?>
						</label> 
						<input 
							type="text" 
							id="gfgeo-map-bounds-ne-point" 
							style="width:100%;"
							class="gfgeo-map-bounds-ne-point"
							onkeyup="SetFieldProperty( 'gfgeo_map_bounds_ne_point', this.value );"
							placeholder="26.4724595,-82.0217760"
						/>
					</div>
				</li>

				<li class="gfgeo-setting gfgeo-map-marker-settings">

					<label class="section_label">
						<?php esc_attr_e( 'Map Marker', 'gfgeo' ); ?>
					</label>

					<ul class="gfgeo-multiple-map-marker-settings-message">
						<li class="field_setting gfgeo-map-settings">
							<em><?php esc_html_e( 'When selecting multiple geocoder fields you can set the marker options in each of the Geocoder fields options.', 'gfgeo' ); ?></em>
						</li>
					</ul>

					<ul class="gfgeo-map-marker-setting-section-wrapper">

						<li class="gfgeo-setting">
							<em class="gfgeo-deprecated-message"><?php echo esc_html__( 'The map marker options in the map field are deprecated. You should now set the map marker options in the Geocoder field options.', 'gfgeo' ); ?></em>
						</li>

						<li class="gfgeo-setting gfgeo-map-marker-setting">
							<label for="gfgeo-map-marker" class="section_label"> 
								<?php esc_html_e( 'Map Marker URL', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_map_marker_tt' ); ?>
							</label> 
							<input 
								type="text"
								id="gfgeo-map-marker" 
								class="gfgeo-map-marker fieldwidth-3" 
								onkeyup="SetFieldProperty( 'gfgeo_map_marker', this.value );">
						</li>

						<li class="gfgeo-setting gfgeo-map-marker-setting">
							<input 
								type="checkbox"
								class="gfgeo-map-marker"
								id="gfgeo-draggable-marker" 
								onclick="SetFieldProperty( 'gfgeo_draggable_marker', this.checked );" 
							/>
							<label for="gfgeo-draggable-marker" class="inline"> 
								<?php esc_html_e( 'Draggable Map Marker', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_draggable_marker_tt' ); ?>
							</label>
						</li>

						<li class="gfgeo-setting gfgeo-set-marker-on-click-setting">
							<input 
								type="checkbox" 
								id="gfgeo-set-marker-on-click"
								class="gfgeo-set-marker-on-click"
								onclick="SetFieldProperty( 'gfgeo_set_marker_on_click', this.checked );" 
							/>
							<label for="gfgeo-set-marker-on-click" class="inline"> 
								<?php esc_html_e( 'Move Marker on Map Click', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_set_marker_on_click_tt' ); ?>
							</label>
						</li>

						<li class="gfgeo-setting gfgeo-disable-address-output-setting">
							<input 
								type="checkbox" 
								id="gfgeo-disable-address-output"
								class="gfgeo-disable-address-output"
								onclick="SetFieldProperty( 'gfgeo_disable_address_output', this.checked );" 
							/>
							<label for="gfgeo-disable-address-output" class="inline"> 
								<?php esc_html_e( 'Disable Address Output', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_disable_address_output_tt' ); ?>
							</label>
						</li>
					</ul>
				</li>
			</ul>
		</li>

		<!-- Driving Directions Field  -->

		<li class="field_setting gfgeo-directions-field-settings-group gfgeo-stright-line-distance-settings-group gfgeo-settings-group-wrapper">

			<ul class="gfgeo-settings-group-inner">

				<li class="gfgeo-setting gfgeo-directions-field-usage">

					<label for="gfgeo-directions-field-usage" class="section_label"> 
						<?php esc_html_e( 'Field Usage', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_directions_field_usage_tt' ); ?>
					</label> 
					<select 
						id="gfgeo-directions-field-usage"
						class="gfgeo-directions-field-usage fieldwidth-3"
						name="gfgeo_directions_field_usage" 
						onchange="SetFieldProperty( 'gfgeo_directions_field_usage', jQuery( this ).val() );"
					>
						<option value="driving_directions"><?php esc_html_e( 'Driving Directions, Distance & Routes', 'gfgeo' ); ?></option>
						<option value="straight_line"><?php esc_html_e( 'Straight Line Distance & Routes', 'gfgeo' ); ?></option>
					</select>
				</li>

				<li class="gfgeo-section-label-wrapper">
					<label class="section_label"> 
						<?php esc_html_e( 'Directions Points', 'gfgeo' ); ?>
						<?php gform_tooltip( 'gfgeo_directions_field_directions_points_tt' ); ?>
					</label>
				</li>

				<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-directions-geocoders-options">

					<div class="gfgeo-setting gfgeo-origin-geocoder-id-setting gfgeo-multiple-left-setting">

						<label for="gfgeo-origin-geocoder-id" class="section_label"> 
							<?php esc_html_e( 'Origin Geocoder', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_origin_geocoder_id_tt' ); ?>
						</label> 
						<select 
							id="gfgeo-origin-geocoder-id"
							class="gfgeo-origin-geocoder-id fieldwidth-3"
							name="gfgeo_origin_geocoder_id"
							onchange="SetFieldProperty( 'gfgeo_origin_geocoder_id', jQuery( this ).val() );"
						>
						<!-- values for this field generate by jquery function -->
						<option value=""><?php esc_html_e( 'Select an option', 'gfgeo' ); ?></option>
						</select>
					</div>

					<div class="gfgeo-setting gfgeo-destination-geocoder-id-setting gfgeo-multiple-right-setting">

						<label for="gfgeo-destination-geocoder-id" class="section_label"> 
							<?php esc_html_e( 'Destination Geocoder', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_destination_geocoder_id_tt' ); ?>
						</label> 
						<select 
							id="gfgeo-destination-geocoder-id"
							class="gfgeo-destination-geocoder-id fieldwidth-3"
							name="gfgeo_destination_geocoder_id"
							onchange="SetFieldProperty( 'gfgeo_destination_geocoder_id', jQuery( this ).val() );"
						>
						<!-- values for this field generate by jquery function -->
						<option value=""><?php esc_html_e( 'Select an option', 'gfgeo' ); ?></option>
						</select>
					</div>
				</li>

				<li class="gfgeo-setting gfgeo-waypoints-geocoders-setting">
					<label for="gfgeo-waypoints-geocoders" class="section_label"> 
						<?php esc_html_e( 'Waypoints Geocoders', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_waypoints_geocoders_tt' ); ?>
					</label> 
					<select
						multiple="multiple"
						id="gfgeo-waypoints-geocoders"
						class="gfgeo-waypoints-geocoders fieldwidth-3"
						name="gfgeo_waypoints_geocoders" 
						onchange="SetFieldProperty( 'gfgeo_waypoints_geocoders', jQuery( this ).val() );"
					>
					<!-- values for this field generate by jquery function -->
					</select>
				</li>

				<li class="gfgeo-section-label-wrapper">
					<label class="section_label"> 
						<?php esc_html_e( 'Travel Options', 'gfgeo' ); ?>
					</label>
				</li>

				<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-travel-mode-options-setting">

					<div class="gfgeo-setting gfgeo-travel-mode-setting gfgeo-multiple-left-setting">

						<label for="gfgeo-travel-mode" class="section_label"> 
							<?php esc_html_e( 'Travel Mode', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_travel_mode_tt' ); ?>
						</label> 
						<select 
							id="gfgeo-travel-mode"
							class="gfgeo-travel-mode fieldwidth-3"
							name="gfgeo_travel_mode" 
							onchange="SetFieldProperty( 'gfgeo_travel_mode', jQuery( this ).val() );"
						>
							<option value="DRIVING"><?php esc_html_e( 'Driving', 'gfgeo' ); ?></option>
							<option value="WALKING"><?php esc_html_e( 'Walking', 'gfgeo' ); ?></option>
							<option value="BICYCLING"><?php esc_html_e( 'Bicycling', 'gfgeo' ); ?></option>
							<option value="TRANSIT"><?php esc_html_e( 'Transit', 'gfgeo' ); ?></option>
						</select>
					</div>

					<div class="gfgeo-setting gfgeo-unit-system-setting gfgeo-multiple-right-setting">
						<label for="gfgeo-unit-system" class="section_label"> 
							<?php esc_html_e( 'Unit System', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_unit_system_tt' ); ?>
						</label> 
						<select 
							id="gfgeo-unit-system"
							class="gfgeo-unit-system fieldwidth-3"
							name="gfgeo_unit_system"
							onchange="SetFieldProperty( 'gfgeo_unit_system', jQuery( this ).val() );"
						>
							<option value="imperial"><?php esc_html_e( 'Imperial ( Miles )', 'gfgeo' ); ?></option>
							<option value="metric"><?php esc_html_e( 'Metric ( Kilometers )', 'gfgeo' ); ?></option>
						</select>
					</div>
				</li>

				<li class="gfgeo-section-label-wrapper gfgeo-map-marker-options-label-wrapper">
					<label class="section_label"> 
						<?php esc_html_e( 'Map Options', 'gfgeo' ); ?>
					</label>
				</li>

				<li class="gfgeo-setting gfgeo-route-map-id-setting">
					<label for="gfgeo-route-map-id" class="section_label"> 
						<?php esc_html_e( 'Map ID', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_route_map_id_tt' ); ?>
					</label> 
					<select 
						id="gfgeo-route-map-id"
						class="gfgeo-route-map-id fieldwidth-3"
						name="gfgeo_route_map_id"
						onchange="SetFieldProperty( 'gfgeo_route_map_id', jQuery( this ).val() );"
					>
					<!-- values for this field generate by jquery function -->
					<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
					</select>
				</li>

				<li class="gfgeo-setting gfgeo-route-polyline-options-setting">

					<label for="gfgeo-route-polyline-options" class="section_label"> 
						<?php esc_html_e( 'Polyline Options', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_route_polyline_options_tt' ); ?>
					</label> 
					<input 
						type="text" 
						id="gfgeo-route-polyline-options" 
						class="gfgeo-route-polyline-options fieldwidth-3" 
						onkeyup="SetFieldProperty( 'gfgeo_route_polyline_options', this.value );"
						placeholder="strokeColor:'#0088FF',strokeWeight:6,strokeOpacity:0.6" 
					/>
				</li>

				<li class="gfgeo-setting gfgeo-directions-panel-id-setting">
					<label for="gfgeo-directions-panel-id" class="section_label"> 
						<?php esc_html_e( 'Directions Panel', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_directions_panel_id_tt' ); ?>
					</label> 
					<select 
						id="gfgeo-directions-panel-id"
						class="gfgeo-directions-panel-id fieldwidth-3"
						name="gfgeo_directions_panel_id"
						onchange="SetFieldProperty( 'gfgeo_directions_panel_id', jQuery( this ).val() );"
					>
					<!-- values for this field generate by jquery function -->
					<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
					</select>
				</li>

				<li class="gfgeo-section-label-wrapper gfgeo-directions-trigger-options-label">
					<label class="section_label"> 
						<?php esc_html_e( 'Directions Trigger Options', 'gfgeo' ); ?>
					</label>
				</li>

				<li class="gfgeo-setting gfgeo-trigger-directions-method-setting">

					<label for="gfgeo-trigger-directions-method" class="section_label"> 
						<?php esc_html_e( 'Directions Trigger Method', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_trigger_directions_method_tt' ); ?>
					</label> 
					<select 
						id="gfgeo-trigger-directions-method"
						class="gfgeo-trigger-directions-method fieldwidth-3"
						name="gfgeo_trigger_directions_method"
						onchange="SetFieldProperty( 'gfgeo_trigger_directions_method', jQuery( this ).val() );"
					>
						<option value="dynamically"><?php esc_html_e( 'Dynamically', 'gfgeo' ); ?></option>
						<option value="button"><?php esc_html_e( 'Using a button', 'gfgeo' ); ?></option>
					</select>
				</li>

				<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-get-directions-button-label-setting">

					<div class="gfgeo-section-label-wrapper gfgeo-directions-trigger-options-label">
						<label class="section_label"> 
							<?php esc_html_e( 'Buttons Labels', 'gfgeo' ); ?>
						</label>
					</div>

					<div class="gfgeo-setting gfgeo-get-directions-button-setting gfgeo-multiple-left-setting">

						<label for="gfgeo-get-directions-button-label" class="section_label"> 
							<?php esc_html_e( 'Get Directions', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_get_directions_button_label_tt' ); ?>
						</label> 
						<input 
							type="text" 
							id="gfgeo-get-directions-button-label" 
							class="gfgeo-get-directions-button-label fieldwidth-3 gfgeo-dynamic-text-toggle" 
							onkeyup="SetFieldProperty( 'gfgeo_get_directions_button_label', this.value );"
							data-dynamic_text_target=".gfgeo-get-directions-button"
							data-dynamic_text_target_type="button"
						/>
					</div>

					<div class="gfgeo-setting gfgeo-reset-direction-button-label-setting gfgeo-multiple-right-setting">

						<label for="gfgeo-reset-direction-button-label" class="section_label"> 
							<?php esc_html_e( 'Clear Directions', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_clear_directions_button_label_tt' ); ?>
						</label> 
						<input 
							type="text" 
							id="gfgeo-clear-directions-button-label" 
							class="gfgeo-clear-directions-button-label fieldwidth-3 gfgeo-dynamic-text-toggle" 
							onkeyup="SetFieldProperty( 'gfgeo_clear_directions_button_label', this.value );"
							data-dynamic_text_target=".gfgeo-clear-directions-button"
							data-dynamic_text_target_type="button"
						/>
					</div>
				</li>
			</ul>

		</li>

		<!-- Dynamic fields --->
		<li class="field_setting gfgeo-setting gfgeo-dynamic-field-usage-setting gfgeo-dynamic-field-options">

			<div class="gfgeo-section-label-wrapper">
				<label class="section_label">
					<?php esc_attr_e( 'Dynamic Field Options', 'gfgeo' ); ?>
					<?php gform_tooltip( 'gfgeo_dynamic_field_options_label_tt' ); ?>
				</label>
			</div>

			<label for="gfgeo-dynamic-field-usage" class="section_label"> 
				<?php esc_html_e( 'Dynamic Field Usage', 'gfgeo' ); ?> 
				<?php gform_tooltip( 'gfgeo_dynamic_field_usage_tt' ); ?>
			</label> 
			<select 
				name="gfgeo_dynamic_field_usage" 
				class="gfgeo-dynamic-field-usage fieldwidth-3"
				id="gfgeo-dynamic-field-usage"
				onchange="SetFieldProperty( 'gfgeo_dynamic_field_usage', jQuery( this ).val() );"
			>
			<!-- values for this field generate by jquery function -->
				<option value="location">Dynamic Location Field</option>
				<option value="directions">Dynamic Directions Field</option>
			</select>
		</li>

		<li class="field_setting gfgeo-setting gfgeo-multiple-settings gfgeo-dynamic-location-field-options gfgeo-dynamic-field-options">

			<div class="gfgeo-setting gfgeo-dynamic-field-geocoder-id gfgeo-multiple-left-setting">
				<label for="gfgeo-dynamic-field-geocoder-id" class="section_label"> 
					<?php esc_html_e( 'Geocoder Field ID', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_geocoder_id_tt' ); ?>
				</label> 
				<select 
					name="gfgeo_geocoder_id" 
					class="gfgeo-dynamic-field-geocoder-id fieldwidth-3"
					id="gfgeo-dynamic-field-geocoder-id"
					onchange="SetFieldProperty( 'gfgeo_geocoder_id', jQuery( this ).val() );"
				>
				<!-- values for this field generate by jquery function -->
				<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
				</select>
			</div>

			<!-- dynamic location field -->
			<div class="gfgeo-setting gfgeo-dynamic-location-field gfgeo-multiple-right-setting">

				<label for="gfgeo-dynamic-location-field" class="section_label">
					<?php esc_html_e( 'Field Value', 'gfgeo' ); ?>
					<?php gform_tooltip( 'gfgeo_dynamic_location_field_tt' ); ?>
				</label> 

				<select
					name="gfgeo_dynamic_location_field"
					id="gfgeo-dynamic-location-field"
					class="gfgeo-dynamic-location-field fieldwidth-3"
					onchange="SetFieldProperty( 'gfgeo_dynamic_location_field', jQuery(this).val() );">
					<?php
					foreach ( GFGEO_Helper::get_location_fields() as $value => $name ) {

						if ( 'status' === $value ) {
							continue;
						}

						echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $name ) . '</option>';
					}
					?>
				</select>
			</div>
		</li>

		<!-- Location output fields -->

		<li class="field_setting gfgeo-setting gfgeo-multiple-settings gfgeo-dynamic-directions-field-options gfgeo-dynamic-field-options">

			<!-- longitude placehoolder --> 
			<div class="gfgeo-setting gfgeo-directions-field-id-setting gfgeo-multiple-left-setting">
				<label for="gfgeo-directions-field-id" class="section_label"> 
					<?php esc_html_e( 'Directions Field', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_directions_field_id_tt' ); ?>
				</label> 
				<select 
					name="gfgeo_directions_field_id" 
					id="gfgeo-directions-field-id"
					class="gfgeo-directions-field-id"
					onchange="SetFieldProperty( 'gfgeo_directions_field_id', jQuery( this ).val() );"
				>
				<!-- values for this field generate by jquery function -->
				<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
				</select>
			</div>

			<div class="gfgeo-setting gfgeo-dynamic-directions-field-setting gfgeo-multiple-right-setting">

				<label for="gfgeo-dynamic-directions-field" class="section_label">
					<?php esc_html_e( 'Field Value', 'gfgeo' ); ?>
					<?php gform_tooltip( 'gfgeo_dynamic_directions_field_tt' ); ?>
				</label> 

				<select 
					name="gfgeo_dynamic_directions_field"
					id="gfgeo-dynamic-directions-field"
					class="gfgeo-dynamic-directions-field"
					onchange="SetFieldProperty( 'gfgeo_dynamic_directions_field', jQuery(this).val() );">
					<?php
					foreach ( GFGEO_Helper::get_dynamic_directions_fields() as $value => $name ) {

						if ( 'status' === $value ) {
							continue;
						}

						echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $name ) . '</option>';
					}
					?>
				</select>
			</div>
		</li>
		<?php
	}

	/**
	 * Geolocation fields options for Gravity Forms previously to v2.5.
	 *
	 * @param  [type] $position [description].
	 *
	 * @param  [type] $form_id  [description].
	 */
	public function fields_settings_2_4( $position, $form_id ) {

		$position = absint( $position );
		?>
		<?php if ( 20 === $position ) { ?>

			<!-- Geocoder field settings -->

			<li class="field_setting gfgeo-geocoder-settings gfgeo-settings-group-wrapper">

				<ul class="gfgeo-settings-group-inner" style="display: inline-block; width: 375px;">

					<li class="gfgeo-section-label-wrapper">
						<label class="section_label">
							<?php esc_attr_e( 'Default Coordinates', 'gfgeo' ); ?>
							<?php gform_tooltip( 'gfgeo_default_coordinates_label_tt' ); ?>
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-multiple-settings">

						<div class="gfgeo-setting" style="width: 49%; float: left; display: list-item;">
							<label for="gfgeo-default-latitude"> 
								<?php esc_html_e( 'Default Latitude', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_default_latitude_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-default-latitude" 
								class="gfgeo-default-latitude"
								onkeyup="SetFieldProperty( 'gfgeo_default_latitude', this.value );">
						</div>

						<div style="width: 49%; float: right; display: list-item;">
							<label for="gfgeo-default-longitude"> 
								<?php esc_html_e( 'Default Longitude', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_default_longitude_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-default-longitude" 
								class="gfgeo-default-longitude"
								onkeyup="SetFieldProperty( 'gfgeo_default_longitude', this.value );">
						</div>
					</li>

					<li class="gfgeo-section-label-wrapper">
						<label class="section_label">
							<?php esc_attr_e( 'Page Auto-Locator', 'gfgeo' ); ?>
							<?php gform_tooltip( 'gfgeo_page_locator_label_tt' ); ?>
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-page-locator-setting">

						<input 
							type="checkbox" 
							id="gfgeo-page-locator"
							class='gfgeo-page-locator'
							onclick="SetFieldProperty( 'gfgeo_page_locator', this.checked );" 
						/>
						<label for="gfgeo-page-locator" class="inline"> 
							<?php esc_html_e( 'Enable Page Locator', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_page_locator_tt' ); ?>
						</label>
					</li>

					<?php $ip_locator_status = ! GFGEO_IP_LOCATOR ? 'disabled="disabled"' : ''; ?>

					<li class="gfgeo-setting gfgeo-ip-locator-status-setting">

						<label for="gfgeo-ip-locator-status"> 
							<?php esc_html_e( 'IP Address Locator', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_ip_locator_status_tt' ); ?>
						</label>

						<select 
							<?php echo $ip_locator_status; // WPCS: XSS ok. ?>
							name="gfgeo_ip_locator_status" 
							id="gfgeo-ip-locator-status"
							class="gfgeo-ip-locator-status fieldwidth-3"
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
				</ul>	
			</li>

		<?php } ?>

		<?php if ( 800 === $position ) { ?>

			<!-- GEO my WP meta fields -->
			<li class="field_setting gfgeo-geocoder-settings gfgeo-settings-group-wrapper">

				<ul class="gfgeo-settings-group-inner">

					<li class="gfgeo-section-label-wrapper">
						<label class="section_label">
							<?php esc_attr_e( 'GEO my WP Meta Fields Options', 'gfgeo' ); ?>
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-user-meta-field-setting">
						<label for="gfgeo-user-meta-field"> 
							<?php esc_html_e( 'User Meta Field Name', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_user_meta_field_tt' ); ?>
						</label>

						<input 
							type="text" 
							id="gfgeo-user-meta-field" 
							class="gfgeo-user-meta-field fieldwidth-3" 
							onkeyup="SetFieldProperty( 'gfgeo_user_meta_field', this.value );"
						/>
					</li>

					<!-- geocoder meta fields -->
					<li class="gfgeo-setting gfgeo-geocoder-meta-field-setting">
						<label>
							<?php esc_html_e( 'Additional Meta Fields Setup', 'gfgeo' ); ?>
							<?php gform_tooltip( 'gfgeo_geocoder_meta_fields_setup_tt' ); ?>
							<a 
								href="#" 
								title="show fields"
								class="gfgeo-geocoder-meta-field-setting-toggle"
								id="gfgeo-geocoder-meta-field-setting-toggle"
								>
								<?php esc_html_e( 'Show Fields', 'gfgeo' ); ?>
							</a>
						</label> 
						<?php
						GFGEO_Helper::custom_meta_fields( 'custom_field' );
						?>
					</li>
				</ul>
			</li>

		<?php } ?>

		<?php if ( 20 === $position ) { ?>

			<!-- gecoder fields ID --->

			<li class="field_setting gfgeo-setting gfgeo-geocoder-id">
				<label for="gfgeo-geocoder-id" class="section_label"> 
					<?php esc_html_e( 'Geocoder ID', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_geocoder_id_tt' ); ?>
				</label> 
				<select 
					name="gfgeo_geocoder_id" 
					id="gfgeo-geocoder-id"
					class="gfgeo-geocoder-id single-geocoder fieldwidth-3"
					onchange="SetFieldProperty( 'gfgeo_geocoder_id', jQuery( this ).val() );"
				>
				<!-- values for this field generate by jquery function -->
				<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
				</select>
			</li>

			<li class="field_setting gfgeo-setting gfgeo-dynamic-field-usage-setting gfgeo-dynamic-field-options">

				<div class="gfgeo-section-label-wrapper">
					<label class="section_label">
						<?php esc_attr_e( 'Dynamic Field Options', 'gfgeo' ); ?>
						<?php gform_tooltip( 'gfgeo_dynamic_field_options_label_tt' ); ?>
					</label>
				</div>

				<label for="gfgeo-dynamic-field-usage"> 
					<?php esc_html_e( 'Dynamic Field Usage', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_dynamic_field_usage_tt' ); ?>
				</label> 
				<select 
					name="gfgeo_dynamic_field_usage" 
					class="gfgeo-dynamic-field-usage fieldwidth-3"
					id="gfgeo-dynamic-field-usage"
					onchange="SetFieldProperty( 'gfgeo_dynamic_field_usage', jQuery( this ).val() );"
				>
				<!-- values for this field generate by jquery function -->
					<option value="location">Dynamic Location Field</option>
					<option value="directions">Dynamic Directions Field</option>
				</select>
			</li>

			<li class="field_setting gfgeo-setting gfgeo-multiple-settings gfgeo-dynamic-location-field-options gfgeo-dynamic-field-options">

				<div class="gfgeo-setting gfgeo-dynamic-field-geocoder-id gfgeo-multiple-left-setting">
					<label for="gfgeo-dynamic-field-geocoder-id"> 
						<?php esc_html_e( 'Geocoder Field ID', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_geocoder_id_tt' ); ?>
					</label> 
					<select 
						name="gfgeo_geocoder_id" 
						class="gfgeo-dynamic-field-geocoder-id fieldwidth-3"
						id="gfgeo-dynamic-field-geocoder-id"
						onchange="SetFieldProperty( 'gfgeo_geocoder_id', jQuery( this ).val() );"
					>
					<!-- values for this field generate by jquery function -->
					<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
					</select>
				</div>

				<!-- dynamic location field -->
				<div class="gfgeo-setting gfgeo-dynamic-location-field gfgeo-multiple-right-setting">

					<label for="gfgeo-dynamic-location-field">
						<?php esc_html_e( 'Field Value', 'gfgeo' ); ?>
						<?php gform_tooltip( 'gfgeo_dynamic_location_field_tt' ); ?>
					</label> 

					<select
						name="gfgeo_dynamic_location_field"
						id="gfgeo-dynamic-location-field"
						class="gfgeo-dynamic-location-field fieldwidth-3"
						onchange="SetFieldProperty( 'gfgeo_dynamic_location_field', jQuery(this).val() );">
						<?php
						foreach ( GFGEO_Helper::get_location_fields() as $value => $name ) {

							if ( 'status' === $value ) {
								continue;
							}

							echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $name ) . '</option>';
						}
						?>
					</select>
				</div>
			</li>

			<!-- Location output fields -->

			<li class="field_setting gfgeo-setting gfgeo-multiple-settings gfgeo-dynamic-directions-field-options gfgeo-dynamic-field-options">

				<!-- longitude placehoolder --> 
				<div class="gfgeo-setting gfgeo-directions-field-id-setting gfgeo-multiple-left-setting">
					<label for="gfgeo-directions-field-id"> 
						<?php esc_html_e( 'Directions Field', 'gfgeo' ); ?> 
						<?php gform_tooltip( 'gfgeo_directions_field_id_tt' ); ?>
					</label> 
					<select 
						name="gfgeo_directions_field_id" 
						id="gfgeo-directions-field-id"
						class="gfgeo-directions-field-id"
						onchange="SetFieldProperty( 'gfgeo_directions_field_id', jQuery( this ).val() );"
					>
					<!-- values for this field generate by jquery function -->
					<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
					</select>
				</div>

				<div class="gfgeo-setting gfgeo-dynamic-directions-field-setting gfgeo-multiple-right-setting">

					<label for="gfgeo-dynamic-directions-field">
						<?php esc_html_e( 'Field Value', 'gfgeo' ); ?>
						<?php gform_tooltip( 'gfgeo_dynamic_directions_field_tt' ); ?>
					</label> 

					<select 
						name="gfgeo_dynamic_directions_field"
						id="gfgeo-dynamic-directions-field"
						class="gfgeo-dynamic-directions-field"
						onchange="SetFieldProperty( 'gfgeo_dynamic_directions_field', jQuery(this).val() );">
						<?php
						foreach ( GFGEO_Helper::get_dynamic_directions_fields() as $value => $name ) {

							if ( 'status' === $value ) {
								continue;
							}

							echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $name ) . '</option>';
						}
						?>
					</select>
				</div>
			</li>

			<!-- gecoder fields ID - multiple geocoders  -->

			<li class="field_setting gfgeo-setting gfgeo-geocoder-id-multiple">
				<label for="gfgeo-geocoder-id" class="section_label"> 
					<?php esc_html_e( 'Geocoder IDs', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_geocoder_ids_tt' ); ?>
				</label> 
				<select multiple
					name="gfgeo_geocoder_id" 
					id="gfgeo-geocoder-id"
					class="gfgeo-geocoder-id multiple-geocoders fieldwidth-3"
					onchange="SetFieldProperty( 'gfgeo_geocoder_id', jQuery( this ).val() );"
				>
				<!-- values for this field generate by jquery function -->
				</select>
			</li>

			<!-- disable geocoding - for advanced address field -->

			<li class="field_setting gfgeo-setting gfgeo-disable-field-geocoding-setting">
				<input 
					type="checkbox" 
					id="gfgeo-disable-field-geocoding" 
					onclick="SetFieldProperty( 'gfgeo_disable_field_geocoding', this.checked );jQuery( this ).closest( 'li' ).find( 'em' ).slideToggle();" 
				/>
				<label for="gfgeo-disable-field-geocoding" class="inline"> 
					<?php esc_html_e( 'Disable Geocoding ( use as dynamic field only )', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_disable_field_geocoding_tt' ); ?>
				</label>
				<br />
				<em style="font-size: 11px;color: red;margin-top: 5px;display: none"><?php esc_html_e( 'Note: when this checkbox is checked, the locator button and address autocomplete features below will be ignored.', 'gfgeo' ); ?></em>
			</li>

			<!-- Locator button label -->

			<li class="field_setting gfgeo-setting gfgeo-locator-button-label-setting">

				<label for="gfgeo-locator-button-label" class="section_label"> 
					<?php esc_html_e( 'Button Label', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_locator_button_label_tt' ); ?>
				</label> 
				<input 
					type="text" 
					id="gfgeo-locator-button-label" 
					class="gfgeo-locator-button-label fieldwidth-3" 
					onkeyup="SetFieldProperty( 'gfgeo_locator_button_label', this.value );"
				/>

				<label for="gfgeo-locator-button-options" class="section_label" style="margin-top: 20px;margin-bottom: 0 ! important;"> 
					<?php esc_html_e( 'Locator Options', 'gfgeo' ); ?> 
				</label> 
			</li>

			<!-- infield locator button -->

			<li class="field_setting gfgeo-setting gfgeo-infield-locator-button">

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

			<!-- Locator found message option -->

			<li class="field_setting gfgeo-setting gfgeo-location-found-message">
				<label for="gfgeo-location-found-message"> 
					<?php esc_html_e( 'Location Found Message', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_location_found_message_tt' ); ?>
				</label> 
				<input 
					type="text" 
					id="gfgeo-location-found-message" 
					class="fieldwidth-3" 
					onkeyup="SetFieldProperty( 'gfgeo_location_found_message', this.value );"
				/>
			</li>

			<!-- Disable locator failed message -->

			<li class="field_setting gfgeo-setting gfgeo-hide-location-failed-message">
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

			<!-- Reset Location button label -->

			<li class="field_setting gfgeo-settings gfgeo-reset-location-button-label">

				<label for="gfgeo-reset-location-button-label" class="section_label"> 
					<?php esc_html_e( 'Button Label', 'gfgeo' ); ?> 
					<?php gform_tooltip( 'gfgeo_reset_location_button_label_tt' ); ?>
				</label> 
				<input 
					type="text" 
					size="35" 
					id="gfgeo-reset-location-button-label" 
					class="" 
					onkeyup="SetFieldProperty( 'gfgeo_reset_location_button_label', this.value );"
				/>
			</li>

			<!-- gecoder field map marker section  -->

			<li class="field_setting gfgeo-geocoder-settings gfgeo-settings-group-wrapper ">

				<ul class="gfgeo-settings-group-inner" style="max-width: 375px;">

					<li class="gfgeo-section-label-wrapper">
						<label class="section_label">
							<?php esc_attr_e( 'Map Marker', 'gfgeo' ); ?>
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-map-marker-default-coords-setting">

						<div class="gfgeo-settings gfgeo-map-marker-default-latitude-setting gfgeo-multiple-left-setting">
							<label for="gfgeo-map-marker-default-latitude"> 
								<?php esc_html_e( 'Default Latitude', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_map_marker_default_latitude_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-map-marker-default-latitude"
								class="gfgeo-map-marker-default-latitude"
								style="width:100%;"
								onkeyup="SetFieldProperty( 'gfgeo_map_marker_default_latitude', this.value );"
							/>
						</div>

						<div class="gfgeo-settings gfgeo-map-marker-default-longitude-setting gfgeo-multiple-right-setting">
							<label for="gfgeo-map-marker-default-longitude"> 
								<?php esc_html_e( 'Default Longitude', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_map_marker_default_longitude_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-map-marker-default-longitude" 
								class="gfgeo-map-marker-default-longitude"
								style="width:100%;"
								onkeyup="SetFieldProperty( 'gfgeo_map_marker_default_longitude', this.value );"
							/>
						</div>
					</li>

					<li class="gfgeo-settings gfgeo-map-marker-url-settings">
						<label for="gfgeo-map-marker-url"> 
							<?php esc_html_e( 'Marker URL', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_map_marker_url_tt' ); ?>
						</label> 
						<input 
							type="text"
							id="gfgeo-map-marker-url" 
							class="gfgeo-map-marker-url fieldwidth-3" 
							onkeyup="SetFieldProperty( 'gfgeo_map_marker_url', this.value );">
					</li>

					<li class="gfgeo-settings gfgeo-marker-info-window-setting">
						<label for="gfgeo-marker-info-window"> 
							<?php esc_html_e( 'Marker Info Window Content', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_marker_info_window_tt' ); ?>
						</label> 
						<input 
							type="text"
							id="gfgeo-marker-info-window" 
							class="gfgeo-marker-info-window fieldwidth-3" 
							onkeyup="SetFieldProperty( 'gfgeo_marker_info_window', this.value );">
					</li>

					<li class="gfgeo-settings gfgeo-map-marker-hidden-setting">
						<input 
							type="checkbox"
							id="gfgeo-map-marker-hidden" 
							class="gfgeo-map-marker-hidden" 
							onClick="SetFieldProperty( 'gfgeo_map_marker_hidden', this.checked );"
						/>
						<label for="gfgeo-map-marker-hidden" class="inline"> 
							<?php esc_html_e( 'Set Marker To Hidden On Map Load', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_map_marker_hidden_tt' ); ?>
						</label> 
					</li>

					<li class="gfgeo-settings gfgeo-disable-marker-drag-setting">
						<input 
							type="checkbox" 
							id="gfgeo-disable-marker-drag"
							class="gfgeo-disable-marker-drag"
							onclick="SetFieldProperty( 'gfgeo_disable_marker_drag', this.checked );" 
						/>
						<label for="gfgeo-disable-marker-drag" class="inline"> 
							<?php esc_html_e( 'Disable Marker Drag', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_disable_marker_drag_tt' ); ?>
						</label>
					</li>

					<li class="gfgeo-settings gfgeo-move-marker-via-map-click-setting">
						<input 
							type="checkbox" 
							id="gfgeo-move-marker-via-map-click"
							class="gfgeo-move-marker-via-map-click"
							onclick="SetFieldProperty( 'gfgeo_move_marker_via_map_click', this.checked );" 
						/>
						<label for="gfgeo-move-marker-via-map-click" class="inline"> 
							<?php esc_html_e( 'Move Marker Using Map Click', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_move_marker_via_map_click_tt' ); ?>
						</label>
					</li>

					<li class="gfgeo-settings gfgeo-address-output-disabled-setting">
						<input 
							type="checkbox" 
							id="gfgeo-address-output-disabled" 
							onclick="SetFieldProperty( 'gfgeo_address_output_disabled', this.checked );" 
						/>
						<label for="gfgeo-address-output-disabled" class="inline"> 
							<?php esc_html_e( 'Disable Address Output', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_address_output_disabled_tt' ); ?>
						</label>
					</li>
				</ul>
			</li>

			<!-- Gecoder field directions section - Deprecated  -->

			<li class="field_setting gfgeo-settings-group-wrapper gfgeo-geocoder-field-directions-settings-group">

				<ul class="gfgeo-settings-group-inner">

					<li class="gfgeo-section-label-wrapper">
						<label for="gfgeo-distance" class="section_label">
							<?php esc_attr_e( 'Driving Directions/Distance & Routes', 'gfgeo' ); ?>
						</label>
					</li>
					<li class="gfgeo-setting">
						<em class="gfgeo-deprecated-message">The driving directions feature of the Geocoder field is deprecated. Please use the "Direction" field instead.</em>
					</li>

					<li class="gfgeo-setting">

						<label for="gfgeo-distance-destination-geocoder-id"> 
							<?php esc_html_e( 'Destination Geocoder', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_distance_destination_geocoder_id_tt' ); ?>
						</label> 
						<select 
							name="gfgeo_distance_destination_geocoder_id" 
							id="gfgeo-distance-destination-geocoder-id"
							class="gfgeo-distance-destination-geocoder-id fieldwidth-3"
							onchange="SetFieldProperty( 'gfgeo_distance_destination_geocoder_id', jQuery( this ).val() );"
						>
						<!-- values for this field generate by jquery function -->
						<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
						</select>
					</li>

					<li class="gfgeo-setting">
						<label for="gfgeo-distance-travel-mode"> 
							<?php esc_html_e( 'Travel Mode', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_distance_travel_mode_tt' ); ?>
						</label> 

						<select 
							name="gfgeo_distance_travel_mode" 
							id="gfgeo-distance-travel-mode"
							class="gfgeo-distance-travel-mode fieldwidth-3"
							onchange="SetFieldProperty( 'gfgeo_distance_travel_mode', jQuery( this ).val() );"
						>
							<option value="DRIVING"><?php esc_html_e( 'Driving', 'gfgeo' ); ?></option>
							<option value="WALKING"><?php esc_html_e( 'Walking', 'gfgeo' ); ?></option>
							<option value="BICYCLING"><?php esc_html_e( 'Bicycling', 'gfgeo' ); ?></option>
							<option value="TRANSIT"><?php esc_html_e( 'Transit', 'gfgeo' ); ?></option>
						</select>
					</li>

					<li class="gfgeo-setting">
						<label for="gfgeo-distance-unit-system"> 
							<?php esc_html_e( 'Unit System', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_distance_unit_system_tt' ); ?>
						</label> 
						<select 
							name="gfgeo_distance_unit_system" 
							id="gfgeo-distance-unit-system"
							class="gfgeo-distance-unit-system fieldwidth-3"
							onchange="SetFieldProperty( 'gfgeo_distance_unit_system', jQuery( this ).val() );"
						>
							<option value="imperial"><?php esc_html_e( 'Imperial ( Miles )', 'gfgeo' ); ?></option>
							<option value="metric"><?php esc_html_e( 'Metric ( Kilometers )', 'gfgeo' ); ?></option>
						</select>
					</li>

					<li class="gfgeo-setting">
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

					<li class="gfgeo-setting">
						<label for="gfgeo-distance-directions-panel-id"> 
							<?php esc_html_e( 'Display Driving Directions', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_distance_directions_panel_id_tt' ); ?>
						</label> 
						<select 
							name="gfgeo_distance_directions_panel_id" 
							id="gfgeo-distance-directions-panel-id"
							class="gfgeo-distance-directions-panel-id fieldwidth-3"
							onchange="SetFieldProperty( 'gfgeo_distance_directions_panel_id', jQuery( this ).val() );"
						>
						<!-- values for this field generate by jquery function -->
						<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
						</select>
					</li>
				</ul>

			</li>

			<!-- Address Field - autocomplete options -->

			<li class="field_setting gfgeo-address-field-settings gfgeo-settings-group-wrapper">

				<ul class="gfgeo-settings-group-inner" style="max-width: 375px;">

					<li class="gfgeo-section-label-wrapper">
						<label class="section_label">
							<?php esc_attr_e( 'Address Autocomplete', 'gfgeo' ); ?>
							<?php gform_tooltip( 'gfgeo_address_autocomplete_feature_tt' ); ?>
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-address-autocomplete-setting">
						<input 
							type="checkbox" 
							id="gfgeo-address-autocomplete"
							class="gfgeo-address-autocomplete"
							onclick="SetFieldProperty( 'gfgeo_address_autocomplete', this.checked );" 
						/>
						<label for="gfgeo-address-autocomplete" class="inline"> 
							<?php esc_html_e( 'Enable Google Address Autocomplete', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_address_autocomplete_tt' ); ?>
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-address-autocomplete-setting gfgeo-force-autocomplete-selection-setting">
						<input
							type="checkbox"
							id="gfgeo-force-autocomplete-selection"
							class="gfgeo-force-autocomplete-selection"
							onclick="SetFieldProperty( 'gfgeo_force_autocomplete_selection', this.checked );"
						/>
						<label for="gfgeo-force-autocomplete-selection" class="inline">
							<?php esc_html_e( 'Force Address Selection From Autocomplete', 'gfgeo' ); ?>
							<?php gform_tooltip( 'gfgeo_force_autocomplete_selection_tt' ); ?>
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-address-autocomplete-setting gfgeo-force-autocomplete-selection-message-setting">
						<label for="gfgeo-force-autocomplete-selection-message" class="inline">
							<?php esc_html_e( 'Force Address Selection Message', 'gfgeo' ); ?>
							<?php gform_tooltip( 'gfgeo_force_autocomplete_selection_message_tt' ); ?>
						</label>
						<input 
							type="text" 
							id="gfgeo-force-autocomplete-selection-message"
							class="gfgeo-force-autocomplete-selection-message fieldwidth-3"
							onkeyup="SetFieldProperty( 'gfgeo_force_autocomplete_selection_message', this.value );"
						/>
					</li>

					<li class="field_setting gfgeo-setting gfgeo-address-autocomplete-usage-setting">
						<label for="gfgeo-address-autocomplete-usage" class="inline"> 
							<?php esc_html_e( 'Autocomplete Usage', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_address_autocomplete_usage_tt' ); ?>
						</label>
						<select 
							name="gfgeo_address_autocomplete_usage" 
							id="gfgeo-address-autocomplete-usage"
							class="gfgeo-address-autocomplete-usage fieldwidth-3"
							onchange="SetFieldProperty( 'gfgeo_address_autocomplete_usage', jQuery( this ).val() );"
						>
							<option value="extra_field"><?php esc_html_e( 'Add additional text field for the autocomplete', 'gfgeo' ); ?></option>
							<option value="same_field"><?php esc_html_e( 'Use the first text field of the Address Field', 'gfgeo' ); ?></option>
						</select>
					</li>

					<li class="field_setting gfgeo-setting gfgeo-address-autocomplete-placeholder-setting">
						<label for="gfgeo-address-autocomplete-placeholder"> 
							<?php esc_html_e( 'Autocomplete Placeholder', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_address_autocomplete_placeholder_tt' ); ?>
						</label> 
						<input 
							type="text" 
							class="gfgeo-address-autocomplete-placeholder fieldwidth-3"
							id="gfgeo-address-autocomplete-placeholder"
							onkeyup="SetFieldProperty( 'gfgeo_address_autocomplete_placeholder', this.value );"
						/>
					</li>

					<li class="field_setting gfgeo-setting gfgeo-address-autocomplete-desc-setting">
						<label for="gfgeo-address-autocomplete-desc"> 
							<?php esc_html_e( 'Autocomplete Field description', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_address_autocomplete_desc_tt' ); ?>
						</label> 
						<input 
							type="text" 
							class="fieldwidth-3 gfgeo-address-autocomplete-desc"
							id="gfgeo-address-autocomplete-desc"
							onkeyup="SetFieldProperty('gfgeo_address_autocomplete_desc', this.value);">
					</li>

					<li class="gfgeo-setting gfgeo-address-autocomplete-types-setting">
						<label for="gfgeo-address-autocomplete-types"> 
							<?php esc_html_e( 'Autocomplete Results Types', 'gfgeo' ); ?>
							<?php gform_tooltip( 'gfgeo_address_autocomplete_types_tt' ); ?>
						</label> 
						&#32;&#32;
						<select 
							name="gfgeo_address_autocomplete_types" 
							id="gfgeo-address-autocomplete-types"
							class="gfgeo-address-autocomplete-types fieldwidth-3"
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

					<li class="gfgeo-section-label-wrapper">
						<label class="section_label"> 
							<?php esc_html_e( 'Address Autocomplete Restrictions', 'gfgeo' ); ?>
							<?php gform_tooltip( 'gfgeo_autocomplete_restriction_usage_tt' ); ?>
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-address-autocomplete-restriction-usage">
						<!-- <label for="gfgeo-autocomplete-restriction-usage"> 
							<?php esc_html_e( 'Restriction Type', 'gfgeo' ); ?>
							<?php gform_tooltip( 'gfgeo_autocomplete_restriction_usage_tt' ); ?>
						</label> 
						&#32;&#32; -->
						<select 
							name="gfgeo_autocomplete_restriction_usage" 
							id="gfgeo-autocomplete-restriction-usage"
							class="gfgeo-autocomplete-restriction-usage fieldwidth-3"
							onchange="SetFieldProperty( 'gfgeo_autocomplete_restriction_usage', jQuery( this ).val() );"
						>	
							<option value="">Disabled</option>
							<option value="countries">Countries</option>
							<option value="proximity">Proximity</option>
							<option value="area_bounds">Area Bounds</option>
							<option value="page_locator">Auto-Locator Bounds</option>
						</select>
					</li>

					<li class="gfgeo-setting gfgeo-address-autocomplete-country-setting restriction-usage-option option-countries">
						<label for="gfgeo-address-autocomplete-country"> 
							<?php esc_html_e( 'Restrict By Countries', 'gfgeo' ); ?>
							<?php gform_tooltip( 'gfgeo_address_autocomplete_country_tt' ); ?>
						</label> 
						&#32;&#32;
						<select 
							multiple="multiple"
							name="gfgeo_address_autocomplete_country" 
							id="gfgeo-address-autocomplete-country"
							class="gfgeo-address-autocomplete-country fieldwidth-3"
							onchange="SetFieldProperty( 'gfgeo_address_autocomplete_country', jQuery(this).val());"
						>
						<?php
						foreach ( GFGEO_Helper::get_countries() as $value => $name ) {
							echo '<option value="' . $value . '">' . $name . '</option>'; // WPCS: XSS ok.
						}
						?>
						</select>
					</li>

					<li class="gfgeo-setting gfgeo-address-autocomplete-locator-bounds-setting restriction-usage-option option-page_locator">
						<input 
							type="checkbox" 
							id="gfgeo-address-autocomplete-locator-bounds"
							class="gfgeo-address-autocomplete-locator-bounds" 
							onclick="SetFieldProperty( 'gfgeo_address_autocomplete_locator_bounds', this.checked );" 
						/>
						<label for="gfgeo-address-autocomplete-locator-bounds" class="inline"> 
							<?php esc_html_e( 'Enable Page Locator Bounds', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_address_autocomplete_locator_bounds_tt' ); ?>
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-autocomplete-proximity-restriction-settings restriction-usage-option option-proximity">

						<label for="gfgeo-autocomplete-proximity-lat"> 
							<?php esc_html_e( 'Restrict By Proximity', 'gfgeo' ); ?>
							<?php gform_tooltip( 'gfgeo_autocomplete_proximity_restriction_tt' ); ?>
						</label> 
						&#32;&#32;
						<div class="gfgeo-setting gfgeo-autocomplete-proximity-lat gfgeo-multiple-left-setting">
							<label for="gfgeo-autocomplete-proximity-lat"> 
								<?php esc_html_e( 'Latitude', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_autocomplete_proximity_lat_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-autocomplete-proximity-lat" 
								style="width:100%;"
								class="gfgeo-autocomplete-proximity-lat"
								onkeyup="SetFieldProperty( 'gfgeo_autocomplete_proximity_lat', this.value );"
								placeholder="26.423277"
							/>
						</div>

						<div class="gfgeo-setting gfgeo-autocomplete-proximity-lng gfgeo-multiple-right-setting options-proximity">
							<label for="gfgeo-autocomplete-proximity-lng"> 
								<?php esc_html_e( 'Longitude', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_autocomplete_proximity_lng_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-autocomplete-proximity-lng" 
								style="width:100%;"
								class="gfgeo-autocomplete-proximity-lng"
								onkeyup="SetFieldProperty( 'gfgeo_autocomplete_proximity_lng', this.value );"
								placeholder="-82.0217760"
							/>
						</div>
					</li>

					<li class="gfgeo-autocomplete-proximity-radius restriction-usage-option option-proximity">

						<label for="gfgeo-autocomplete-proximity-radius"> 
							<?php esc_html_e( 'Radius', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_autocomplete_proximity_radius_tt' ); ?>
						</label> 
						<input 
							type="text" 
							id="gfgeo-autocomplete-proximity-radius" 
							style="width:100%;"
							class="gfgeo-autocomplete-proximity-radius"
							onkeyup="SetFieldProperty( 'gfgeo_autocomplete_proximity_radius', this.value );"
							placeholder="100"
						/>
					</li>

					<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-autocomplete-bounds-settings restriction-usage-option option-area_bounds">

						<label for="gfgeo-autocomplete-bounds-sw-point"> 
							<?php esc_html_e( 'Restrict By Area', 'gfgeo' ); ?>
							<?php gform_tooltip( 'gfgeo_autocomplete_bounds_restriction_tt' ); ?>
						</label> 
						&#32;&#32;
						<div class="gfgeo-setting gfgeo-autocomplete-bounds-sw-point gfgeo-multiple-left-setting">
							<label for="gfgeo-autocomplete-bounds-sw-point"> 
								<?php esc_html_e( 'Southwest Point', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_bounds_sw_point_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-autocomplete-bounds-sw-point" 
								style="width:100%;"
								class="gfgeo-autocomplete-bounds-sw-point"
								onkeyup="SetFieldProperty( 'gfgeo_autocomplete_bounds_sw_point', this.value );"
								placeholder="26.423277,-82.137132"
							/>
						</div>

						<div class="gfgeo-setting gfgeo-autocomplete-bounds-ne-point gfgeo-multiple-right-setting options-area_bounds">
							<label for="gfgeo-autocomplete-bounds-ne-point"> 
								<?php esc_html_e( 'Northeast Point', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_bounds_ne_point_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-autocomplete-bounds-ne-point" 
								style="width:100%;"
								class="gfgeo-autocomplete-bounds-ne-point"
								onkeyup="SetFieldProperty( 'gfgeo_autocomplete_bounds_ne_point', this.value );"
								placeholder="26.4724595,-82.0217760"
							/>
						</div>
					</li>

					<li class="gfgeo-setting gfgeo-address-autocomplete-strict-bounds-setting restriction-usage-option option-area_bounds option-proximity">
						<input 
							type="checkbox" 
							id="gfgeo-address-autocomplete-strict-bounds"
							class="gfgeo-address-autocomplete-strict-bounds"
							onclick="SetFieldProperty( 'gfgeo_address_autocomplete_strict_bounds', this.checked );" 
						/>
						<label for="gfgeo-address-autocomplete-strict-bounds" class="inline"> 
							<?php esc_html_e( 'Limit Suggested Results To Restricted Area', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_address_autocomplete_restrict_bounds_tt' ); ?>
						</label>
					</li>

					<?php /* <li class="gfgeo-setting gfgeo-google-maps-link-setting">
						<input 
							type="checkbox"
							class="gfgeo-google-maps-link"
							id="gfgeo-google-maps-link" 
							onclick="SetFieldProperty( 'gfgeo_google_maps_link', this.checked );" 
						/>
						<label for="gfgeo-google-maps-link" class="inline"> 
							<?php esc_html_e( 'Enable Google Maps Link', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_google_maps_link_tt' ); ?>
						</label>
					</li> */ ?>
				</ul>
			</li>

			<!-- Coordinates field --> 

			<li class="field_setting gfgeo-coordinates-field-settings gfgeo-settings-group-wrapper" style="padding-bottom: 0;">

				<ul class="gfgeo-settings-group-inner" style="display: inline-block; width: 375px;">

					<li class="gfgeo-section-label-wrapper">
						<label class="section_label"> 
							<?php esc_html_e( 'Fields Placeholder', 'gfgeo' ); ?> 
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-cooridnates-placeholder-options">

						<div class="gfgeo-setting gfgeo-latitude-placeholder-setting gfgeo-multiple-left-setting">

							<label for="gfgeo-latitude-placeholder"> 
								<?php esc_html_e( 'Latitude Placeholder', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_latitude_placeholder_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-latitude-placeholder" 
								data-field="latitude"
								class="coordinates-placeholder"
								style="width: 100%"
								onkeyup="SetFieldProperty( 'gfgeo_latitude_placeholder', this.value );"
							/>
						</div>

						<!-- longitude placehoolder --> 
						<div class="gfgeo-setting gfgeo-latitude-placeholder-setting gfgeo-multiple-right-setting">
							<label for="gfgeo-longitude-placeholder"> 
								<?php esc_html_e( 'Longitude Placeholder', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_longitude_placeholder_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-longitude-placeholder" 
								class="coordinates-placeholder"
								data-field="longitude"
								style="width: 100%"
								onkeyup="SetFieldProperty( 'gfgeo_longitude_placeholder', this.value );" />
						</div>
					</li>

					<li class="gfgeo-section-label-wrapper">
						<label class="section_label"> 
							<?php esc_html_e( 'Field Output', 'gfgeo' ); ?> 
						</label>
					</li>

					<li class="gfgeo-settings gfgeo-custom-field-method-setting">
						<input 
							type="checkbox" 
							id="gfgeo-custom-field-method"
							class="gfgeo-custom-field-method"
							onclick="SetFieldProperty( 'gfgeo_custom_field_method', this.checked );" 
						/>
						<label for="gfgeo-custom-field-method" class="inline"> 
							<?php esc_html_e( 'Save custom field as serialized array', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_custom_field_method_tt' ); ?>
						</label>
					</li>
				</ul>
			</li>

			<!--  Map field options -->

			<li class="field_setting gfgeo-map-settings gfgeo-settings-group-wrapper">

				<ul class="gfgeo-settings-group-inner" style="max-width: 375px;">

					<li class="gfgeo-section-label-wrapper">
						<label class="section_label"> 
							<?php esc_html_e( 'Default Coordinates', 'gfgeo' ); ?>
							<?php gform_tooltip( 'gfgeo_map_default_coordinates_tt' ); ?>
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-map-default-coordiantes-settings">

						<div class="gfgeo-setting gfgeo-map-default-latitude-setting gfgeo-multiple-left-setting">
							<label for="gfgeo-map-default-latitude"> 
								<?php esc_html_e( 'Default Latitude', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_map_default_latitude_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-map-default-latitude" 
								style="width:100%;"
								class="gfgeo-map-default-latitude"
								onkeyup="SetFieldProperty( 'gfgeo_map_default_latitude', this.value );"
							/>
						</div>

						<div class="gfgeo-setting gfgeo-map-default-longitude-setting gfgeo-multiple-right-setting">
							<label for="gfgeo-map-default-longitude"> 
								<?php esc_html_e( 'Default Longitude', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_map_default_longitude_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-map-default-longitude" 
								class="gfgeo-map-default-longitude"
								style="width:100%;"
								onkeyup="SetFieldProperty( 'gfgeo_map_default_longitude', this.value );"
							/>
						</div>
					</li>

					<li class="gfgeo-section-label-wrapper">
						<label class="section_label"> 
							<?php esc_html_e( 'Map Options', 'gfgeo' ); ?>
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-map-options-settings">

						<div class="gfgeo-setting gfgeo-map-type-setting gfgeo-multiple-left-setting">
							<label for="gfgeo-map-type">
								<?php esc_html_e( 'Map Type', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_map_type_tt' ); ?>
							</label> 
							<select 
								name="gfgeo_map_type" 
								id="gfgeo-map-type"
								class="gfgeo-map-type"
								onchange="SetFieldProperty( 'gfgeo_map_type', jQuery(this).val() );"
							>
									<option value="ROADMAP">ROADMAP</option>
									<option value="SATELLITE">SATELLITE</option>
									<option value="HYBRID">HYBRID</option>
									<option value="TERRAIN">TERRAIN</option>
							</select>
						</div>

						<div class="gfgeo-setting gfgeo-zoom-level-setting gfgeo-multiple-right-setting">
							<label for="gfgeo-zoom-level"> 
								<?php esc_html_e( 'Zoom Level', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_zoom_level_tt' ); ?>
							</label> 
							<select 
								name="gfgeo_zoom_level"
								class="gfgeo-zoom-level"
								id="gfgeo-zoom-level"
								onchange="SetFieldProperty( 'gfgeo_zoom_level', jQuery(this).val() );"
							>
								<?php $count = 18; ?>
								<?php
								for ( $x = 1; $x <= 18; $x++ ) {
									echo '<option value="' . $x . '">' . $x . '</option>'; // WPCS: XSS ok.
								}
								?>
							</select>
						</div>
					</li>

					<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-map-size-options">

						<div class="gfgeo-settings gfgeo-map-width-setting gfgeo-multiple-left-setting">
							<label for="gfgeo-map-width"> 
								<?php esc_html_e( 'Map Width', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_map_width_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-map-width" 
								class="gfgeo-map-width"  
								onkeyup="SetFieldProperty( 'gfgeo_map_width', this.value );"
							/>
						</div>

						<div class="gfgeo-settings gfgeo-map-height-setting gfgeo-multiple-right-setting">
							<label for="gfgeo-map-height"> 
								<?php esc_html_e( 'Map Height', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_map_height_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-map-height" 
								class="gfgeo-map-height" 
								onkeyup="SetFieldProperty( 'gfgeo_map_height', this.value );">
						</div>
					</li>

					<li class="gfgeo-setting gfgeo-map-styles-setting">
						<label for="gfgeo-map-styles"> 
							<?php esc_html_e( 'Map Styles', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_map_styles_tt' ); ?>
						</label>
						<textarea 
							id="gfgeo-map-styles" 
							class="gfgeo-map-styles fieldwidth-3 fieldheight-2" 
							onblur="SetFieldProperty( 'gfgeo_map_styles', this.value );"></textarea>
					</li>

					<li class="gfgeo-setting gfgeo-map-scroll-wheel-setting">
						<input 
							type="checkbox" 
							id="gfgeo-map-scroll-wheel"
							class="gfgeo-map-scroll-wheel"
							onclick="SetFieldProperty( 'gfgeo_map_scroll_wheel', this.checked );" 
						/>
						<label for="gfgeo-map-scroll-wheel" class="inline"> 
							<?php esc_html_e( 'Enable Mouse Scroll-Wheel Zoom', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_map_scroll_wheel_tt' ); ?>
						</label>
					</li>

					<li class="gfgeo-section-label-wrapper">
						<label class="section_label"> 
							<?php esc_html_e( 'Map Bounds Restriction', 'gfgeo' ); ?>
							<?php gform_tooltip( 'gfgeo_map_bounds_restriction_tt' ); ?>
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-map-strict-bounds-settings">

						<div class="gfgeo-setting gfgeo-map-bounds-sw-point gfgeo-multiple-left-setting">
							<label for="gfgeo-map-bounds-sw-point"> 
								<?php esc_html_e( 'Southwest Point', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_bounds_sw_point_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-map-bounds-sw-point" 
								style="width:100%;"
								class="gfgeo-map-bounds-sw-point"
								onkeyup="SetFieldProperty( 'gfgeo_map_bounds_sw_point', this.value );"
								placeholder="26.423277,-82.137132"
							/>
						</div>

						<div class="gfgeo-setting gfgeo-map-bounds-ne-point gfgeo-multiple-right-setting">
							<label for="gfgeo-map-bounds-ne-point"> 
								<?php esc_html_e( 'Northeast Point', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_bounds_ne_point_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-map-bounds-ne-point" 
								style="width:100%;"
								class="gfgeo-map-bounds-ne-point"
								onkeyup="SetFieldProperty( 'gfgeo_map_bounds_ne_point', this.value );"
								placeholder="26.4724595,-82.0217760"
							/>
						</div>
					</li>

					<li class="gfgeo-settings gfgeo-map-marker-settings">

						<label class="section_label">
							<?php esc_attr_e( 'Map Marker', 'gfgeo' ); ?>
						</label>

						<ul class="gfgeo-multiple-map-marker-settings-message">
							<li class="field_setting gfgeo-map-settings">
								<em><?php esc_html_e( 'When selecting multiple geocoder fields you can set the marker options in each of the Geocoder fields options.', 'gfgeo' ); ?></em>
							</li>
						</ul>

						<ul class="gfgeo-map-marker-setting-section-wrapper">

							<li class="gfgeo-setting">
								<em class="gfgeo-deprecated-message"><?php echo esc_html__( 'The map marker options in the map field are deprecated. You should now set the map marker options in the Geocoder field options.', 'gfgeo' ); ?></em>
							</li>

							<li class="gfgeo-setting gfgeo-map-marker-setting">
								<label for="gfgeo-map-marker"> 
									<?php esc_html_e( 'Map Marker URL', 'gfgeo' ); ?> 
									<?php gform_tooltip( 'gfgeo_map_marker_tt' ); ?>
								</label> 
								<input 
									type="text"
									id="gfgeo-map-marker" 
									class="gfgeo-map-marker fieldwidth-3" 
									onkeyup="SetFieldProperty( 'gfgeo_map_marker', this.value );">
							</li>

							<li class="gfgeo-setting gfgeo-map-marker-setting">
								<input 
									type="checkbox"
									class="gfgeo-map-marker"
									id="gfgeo-draggable-marker" 
									onclick="SetFieldProperty( 'gfgeo_draggable_marker', this.checked );" 
								/>
								<label for="gfgeo-draggable-marker" class="inline"> 
									<?php esc_html_e( 'Draggable Map Marker', 'gfgeo' ); ?> 
									<?php gform_tooltip( 'gfgeo_draggable_marker_tt' ); ?>
								</label>
							</li>

							<li class="gfgeo-setting gfgeo-set-marker-on-click-setting">
								<input 
									type="checkbox" 
									id="gfgeo-set-marker-on-click"
									class="gfgeo-set-marker-on-click"
									onclick="SetFieldProperty( 'gfgeo_set_marker_on_click', this.checked );" 
								/>
								<label for="gfgeo-set-marker-on-click" class="inline"> 
									<?php esc_html_e( 'Move Marker on Map Click', 'gfgeo' ); ?> 
									<?php gform_tooltip( 'gfgeo_set_marker_on_click_tt' ); ?>
								</label>
							</li>

							<li class="gfgeo-setting gfgeo-disable-address-output-setting">
								<input 
									type="checkbox" 
									id="gfgeo-disable-address-output"
									class="gfgeo-disable-address-output"
									onclick="SetFieldProperty( 'gfgeo_disable_address_output', this.checked );" 
								/>
								<label for="gfgeo-disable-address-output" class="inline"> 
									<?php esc_html_e( 'Disable Address Output', 'gfgeo' ); ?> 
									<?php gform_tooltip( 'gfgeo_disable_address_output_tt' ); ?>
								</label>
							</li>
						</ul>
					</li>
				</ul>
			</li>

			<!-- Driving Directions Field  -->

			<li class="field_setting gfgeo-directions-field-settings-group gfgeo-stright-line-distance-settings-group gfgeo-settings-group-wrapper">

				<ul class="gfgeo-settings-group-inner">

					<li class="gfgeo-settings gfgeo-directions-field-usage">

						<label for="gfgeo-directions-field-usage" class="section_label"> 
							<?php esc_html_e( 'Field Usage', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_directions_field_usage_tt' ); ?>
						</label> 
						<select 
							id="gfgeo-directions-field-usage"
							class="gfgeo-directions-field-usage fieldwidth-3"
							name="gfgeo_directions_field_usage" 
							onchange="SetFieldProperty( 'gfgeo_directions_field_usage', jQuery( this ).val() );"
						>
							<option value="driving_directions"><?php esc_html_e( 'Driving Directions, Distance & Routes', 'gfgeo' ); ?></option>
							<option value="straight_line"><?php esc_html_e( 'Straight Line Distance & Routes', 'gfgeo' ); ?></option>
						</select>
					</li>

					<li class="gfgeo-section-label-wrapper">
						<label class="section_label"> 
							<?php esc_html_e( 'Directions Points', 'gfgeo' ); ?>
							<?php gform_tooltip( 'gfgeo_directions_field_directions_points_tt' ); ?>
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-directions-geocoders-options">

						<div class="gfgeo-settings gfgeo-origin-geocoder-id-setting gfgeo-multiple-left-setting">

							<label for="gfgeo-origin-geocoder-id"> 
								<?php esc_html_e( 'Origin Geocoder', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_origin_geocoder_id_tt' ); ?>
							</label> 
							<select 
								id="gfgeo-origin-geocoder-id"
								class="gfgeo-origin-geocoder-id fieldwidth-3"
								name="gfgeo_origin_geocoder_id"
								onchange="SetFieldProperty( 'gfgeo_origin_geocoder_id', jQuery( this ).val() );"
							>
							<!-- values for this field generate by jquery function -->
							<option value=""><?php esc_html_e( 'Select an option', 'gfgeo' ); ?></option>
							</select>
						</div>

						<div class="gfgeo-settings gfgeo-destination-geocoder-id-setting gfgeo-multiple-right-setting">

							<label for="gfgeo-destination-geocoder-id"> 
								<?php esc_html_e( 'Destination Geocoder', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_destination_geocoder_id_tt' ); ?>
							</label> 
							<select 
								id="gfgeo-destination-geocoder-id"
								class="gfgeo-destination-geocoder-id fieldwidth-3"
								name="gfgeo_destination_geocoder_id"
								onchange="SetFieldProperty( 'gfgeo_destination_geocoder_id', jQuery( this ).val() );"
							>
							<!-- values for this field generate by jquery function -->
							<option value=""><?php esc_html_e( 'Select an option', 'gfgeo' ); ?></option>
							</select>
						</div>
					</li>

					<li class="gfgeo-setting gfgeo-waypoints-geocoders-setting">
						<label for="gfgeo-waypoints-geocoders"> 
							<?php esc_html_e( 'Waypoints Geocoders', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_waypoints_geocoders_tt' ); ?>
						</label> 
						<select multiple
							id="gfgeo-waypoints-geocoders"
							class="gfgeo-waypoints-geocoders fieldwidth-3"
							name="gfgeo_waypoints_geocoders" 
							onchange="SetFieldProperty( 'gfgeo_waypoints_geocoders', jQuery( this ).val() );"
						>
						<!-- values for this field generate by jquery function -->
						</select>
					</li>

					<li class="gfgeo-section-label-wrapper">
						<label class="section_label"> 
							<?php esc_html_e( 'Travel Options', 'gfgeo' ); ?>
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-travel-mode-options-setting">

						<div class="gfgeo-settings gfgeo-travel-mode-setting gfgeo-multiple-left-setting">

							<label for="gfgeo-travel-mode"> 
								<?php esc_html_e( 'Travel Mode', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_travel_mode_tt' ); ?>
							</label> 
							<select 
								id="gfgeo-travel-mode"
								class="gfgeo-travel-mode fieldwidth-3"
								name="gfgeo_travel_mode" 
								onchange="SetFieldProperty( 'gfgeo_travel_mode', jQuery( this ).val() );"
							>
								<option value="DRIVING"><?php esc_html_e( 'Driving', 'gfgeo' ); ?></option>
								<option value="WALKING"><?php esc_html_e( 'Walking', 'gfgeo' ); ?></option>
								<option value="BICYCLING"><?php esc_html_e( 'Bicycling', 'gfgeo' ); ?></option>
								<option value="TRANSIT"><?php esc_html_e( 'Transit', 'gfgeo' ); ?></option>
							</select>
						</div>

						<div class="gfgeo-settings gfgeo-unit-system-setting gfgeo-multiple-right-setting">
							<label for="gfgeo-unit-system"> 
								<?php esc_html_e( 'Unit System', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_unit_system_tt' ); ?>
							</label> 
							<select 
								id="gfgeo-unit-system"
								class="gfgeo-unit-system fieldwidth-3"
								name="gfgeo_unit_system"
								onchange="SetFieldProperty( 'gfgeo_unit_system', jQuery( this ).val() );"
							>
								<option value="imperial"><?php esc_html_e( 'Imperial ( Miles )', 'gfgeo' ); ?></option>
								<option value="metric"><?php esc_html_e( 'Metric ( Kilometers )', 'gfgeo' ); ?></option>
							</select>
						</div>
					</li>

					<li class="gfgeo-section-label-wrapper gfgeo-map-marker-options-label-wrapper">
						<label class="section_label"> 
							<?php esc_html_e( 'Map Options', 'gfgeo' ); ?>
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-route-map-id-setting">
						<label for="gfgeo-route-map-id"> 
							<?php esc_html_e( 'Map ID', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_route_map_id_tt' ); ?>
						</label> 
						<select 
							id="gfgeo-route-map-id"
							class="gfgeo-route-map-id fieldwidth-3"
							name="gfgeo_route_map_id"
							onchange="SetFieldProperty( 'gfgeo_route_map_id', jQuery( this ).val() );"
						>
						<!-- values for this field generate by jquery function -->
						<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
						</select>
					</li>

					<li class="gfgeo-setting gfgeo-route-polyline-options-setting">

						<label for="gfgeo-route-polyline-options"> 
							<?php esc_html_e( 'Polyline Options', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_route_polyline_options_tt' ); ?>
						</label> 
						<input 
							type="text" 
							id="gfgeo-route-polyline-options" 
							class="gfgeo-route-polyline-options fieldwidth-3" 
							onkeyup="SetFieldProperty( 'gfgeo_route_polyline_options', this.value );"
							placeholder="strokeColor:'#0088FF',strokeWeight:6,strokeOpacity:0.6" 
						/>
					</li>

					<li class="gfgeo-setting gfgeo-directions-panel-id-setting">
						<label for="gfgeo-directions-panel-id" class="section_label"> 
							<?php esc_html_e( 'Directions Panel', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_directions_panel_id_tt' ); ?>
						</label> 
						<select 
							id="gfgeo-directions-panel-id"
							class="gfgeo-directions-panel-id fieldwidth-3"
							name="gfgeo_directions_panel_id"
							onchange="SetFieldProperty( 'gfgeo_directions_panel_id', jQuery( this ).val() );"
						>
						<!-- values for this field generate by jquery function -->
						<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
						</select>
					</li>

					<li class="gfgeo-section-label-wrapper gfgeo-directions-trigger-options-label">
						<label class="section_label"> 
							<?php esc_html_e( 'Directions Trigger Options', 'gfgeo' ); ?>
						</label>
					</li>

					<li class="gfgeo-setting gfgeo-trigger-directions-method-setting">

						<label for="gfgeo-trigger-directions-method"> 
							<?php esc_html_e( 'Directions Trigger Method', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_trigger_directions_method_tt' ); ?>
						</label> 
						<select 
							id="gfgeo-trigger-directions-method"
							class="gfgeo-trigger-directions-method fieldwidth-3"
							name="gfgeo_trigger_directions_method"
							onchange="SetFieldProperty( 'gfgeo_trigger_directions_method', jQuery( this ).val() );"
						>
							<option value="dynamically"><?php esc_html_e( 'Dynamically', 'gfgeo' ); ?></option>
							<option value="button"><?php esc_html_e( 'Using a button', 'gfgeo' ); ?></option>
						</select>
					</li>

					<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-get-directions-button-label-setting">

						<div class="gfgeo-settings gfgeo-get-directions-button-setting gfgeo-multiple-left-setting">

							<label for="gfgeo-get-directions-button-label"> 
								<?php esc_html_e( 'Get Directions Button Label', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_get_directions_button_label_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-get-directions-button-label" 
								class="gfgeo-get-directions-button-label fieldwidth-3" 
								onkeyup="SetFieldProperty( 'gfgeo_get_directions_button_label', this.value );"
							/>
						</div>

						<div class="gfgeo-setting gfgeo-reset-direction-button-label-setting gfgeo-multiple-right-setting">

							<label for="gfgeo-reset-direction-button-label"> 
								<?php esc_html_e( 'Clear Directions Button Label', 'gfgeo' ); ?> 
								<?php gform_tooltip( 'gfgeo_clear_directions_button_label_tt' ); ?>
							</label> 
							<input 
								type="text" 
								id="gfgeo-clear-directions-button-label" 
								class="gfgeo-clear-directions-button-label fieldwidth-3" 
								onkeyup="SetFieldProperty( 'gfgeo_clear_directions_button_label', this.value );"
							/>
						</div>
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

			<li class="field_setting gfgeo-geocoder-settings gfgeo-settings-group-wrapper">

				<ul class="gfgeo-settings-group-inner">

					<li class="gfgeo-section-label-wrapper">
						<label for="gfgeo-gmw-integration" class="section_label">
							<?php esc_attr_e( 'GEO my WP Integration', 'gfgeo' ); ?>
						</label>
					</li>

					<li class="gfgeo-setting">
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
							<?php esc_html_e( 'GEO my WP Posts Locator Integration', 'gfgeo' ); ?> 
							<?php gform_tooltip( 'gfgeo_gmw_post_integration_tt' ); ?>
						</label>
						<small style="display: block;color: red;margin-top: 2px;"><?php echo $message; // WPCS: XSS ok. ?></small>
					</li>

					<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-gmw-post-integration-wrapper">

						<div class="gfgeo-gmw-post-integration-phone gfgeo-multiple-left-setting">

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
							<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
							</select>
						</div>


						<div class="gfgeo-gmw-post-integration-fax gfgeo-multiple-right-setting">
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
							<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
							</select>
						</div>
					</li>

					<li class="gfgeo-setting gfgeo-multiple-settings gfgeo-gmw-post-integration-wrapper ">

						<div class="gfgeo-gmw-post-integration-email gfgeo-multiple-left-setting">
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
							<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
							</select>
						</div>

						<!-- GMW website  -->

						<div class="gfgeo-gmw-post-integration-website gfgeo-multiple-right-setting">
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
							<option value=""><?php esc_html_e( 'Disabled', 'gfgeo' ); ?></option>
							</select>
						</div>
					</li>

					<!-- GEO my WP User integrations -->
					<li class="gfgeo-setting gfgeo-gmw-user-integration">	

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

		$tooltips['gfgeo_google_maps_link_tt'] = __( 'Check this checkbox to display a link to Google Maps next to the address in the form entry and email notifications.', 'gfgeo' );

		// dynamic fiedlds.
		$tooltips['gfgeo_dynamic_field_options_label_tt'] = __( 'Dynamically populate this field with a location or directions ( distance & duration ) value.', 'gfgeo' );
		$tooltips['gfgeo_dynamic_field_usage_tt']         = __( 'Select the type of field that you would like to dynamically populate.', 'gfgeo' );
		$tooltips['gfgeo_dynamic_location_field_tt']      = __( 'Select the location field that you would like to dynacmilly populate.', 'gfgeo' );
		$tooltips['gfgeo_dynamic_directions_field_tt']    = __( 'Select the directions field that you would like to dynacmilly populate.', 'gfgeo' );
		$tooltips['gfgeo_directions_field_id_tt']         = __( 'Select the Directions field that you would like to sync this field with.', 'gfgeo' );

		// Geocoder ID.
		$tooltips['gfgeo_geocoder_id_tt']  = __( 'Select the Geocoder field that you would like to sync this field with.', 'gfgeo' );
		$tooltips['gfgeo_geocoder_ids_tt'] = __( 'Select the Geocoder fields that you would like to sync this field with.', 'gfgeo' );

		// locator button.
		$tooltips['gfgeo_locator_button_label_tt']         = __( 'Enter the locator button label.', 'gfgeo' );
		$tooltips['gfgeo_infield_locator_button_tt']       = __( 'Display a locator icon inside the address field that will retrieve the user\'s current position on click.', 'gfgeo' );
		$tooltips['gfgeo_location_found_message_tt']       = __( 'Enter the message that will show once the user\'s position was found. Leave this text box blank to disable the message.', 'gfgeo' );
		$tooltips['gfgeo_hide_location_failed_message_tt'] = __( 'Hide the alert message showing when the user position was not found. Instead, it will show in the developer console log.', 'gfgeo' );

		// reset button.
		$tooltips['gfgeo_reset_location_button_label_tt'] = __( 'Enter the button label.', 'gfgeo' );

		// corrdinates.
		$tooltips['gfgeo_latitude_placeholder_tt']  = __( 'Enter a placeholder text for the latitude textbox.', 'gfgeo' );
		$tooltips['gfgeo_longitude_placeholder_tt'] = __( 'Enter a placeholder text for the longitude textbox.', 'gfgeo' );
		$tooltips['gfgeo_custom_field_method_tt']   = __( 'By default, the coordinates value will be saved comma separated: latitude,longitude ( ex 12345,6789 ). Check this checkbox if you\'d like to save the value as serialized array', 'gfgeo' );

		// Different bounds settings.
		$tooltips['gfgeo_bounds_sw_point_tt'] = __( 'Enter a set of coordinates, comma-separated ( ex. 26.423277,-82.1371324 ), that represents the southwest point of the area that you would like to restrict.', 'gfgeo' );
		$tooltips['gfgeo_bounds_ne_point_tt'] = __( 'Enter a set of coordinates, comma-separated ( ex. 26.4724595,-82.0217760 ), that represents the northeast point of the area that you would like to restrict.', 'gfgeo' );

		// map fields tooltips.
		$tooltips['gfgeo_map_default_coordinates_tt'] = __( 'Enter the coordinates of the default location to center the map on when the map first loads.', 'gfgeo' );
		$tooltips['gfgeo_map_default_latitude_tt']    = __( 'Enter the latitude of the initial location of the map ( when the form first loads ).', 'gfgeo' );
		$tooltips['gfgeo_map_default_longitude_tt']   = __( 'Enter the longitude of the initial location of map ( when the form first loads ).', 'gfgeo' );
		$tooltips['gfgeo_map_width_tt']               = __( 'Enter the map width in pixels or percentage.', 'gfgeo' );
		$tooltips['gfgeo_map_height_tt']              = __( 'Enter the map height in pixels or percentage.', 'gfgeo' );
		$tooltips['gfgeo_map_styles_tt']              = __( 'Enter custom map style. <a href="https://snazzymaps.com" target="_blank">Snazzy Maps website</a> has a large collection of map styles that you can use.', 'gfgeo' );
		$tooltips['gfgeo_map_bounds_restriction_tt']  = __( 'Restrict the map view to a specific area by providing the Southwest and northeast points of this area.', 'gfgeo' );
		$tooltips['gfgeo_map_styles_tt']              = __( 'Enter custom map style. <a href="https://snazzymaps.com" target="_blank">Snazzy Maps website</a> has a large collection of map styles that you can use.', 'gfgeo' );
		$tooltips['gfgeo_map_marker_tt']              = __( 'Enter the URL of the image that will be used as the map marker.', 'gfgeo' );
		$tooltips['gfgeo_map_type_tt']                = __( 'Select the map type.', 'gfgeo' );
		$tooltips['gfgeo_zoom_level_tt']              = __( 'Set the zoom level of the map.', 'gfgeo' );
		$tooltips['gfgeo_draggable_marker_tt']        = __( 'Making marker draggable allows the front-end users to set location by dragging the map marker to the desired position.', 'gfgeo' );
		$tooltips['gfgeo_set_marker_on_click_tt']     = __( 'Set marker\'s location by a click on the map.', 'gfgeo' );
		$tooltips['gfgeo_map_scroll_wheel_tt']        = __( 'Allow map zoom via mouse scroll-wheel.', 'gfgeo' );
		$tooltips['gfgeo_disable_address_output_tt']  = __( 'Disable the output of the address fields when updating the marker\'s location. This way only the coordinates will be dynamically updated. This can be useful for a specific scenario where one wants to first find the location on the map by entering an address. Then, if the address entered is correct but the marker is not on the exact location on the map or the coordinates are not the exact desired coordinates, the visitor can drag the marker to find the exact coordinates without changing the address.', 'gfgeo' );

		// disable field geocoding.
		$tooltips['gfgeo_disable_field_geocoding_tt'] = __( 'When checked, the address field will be treated as a dynamic field only. Which means, that when the address changes it will not be geocoded and will not effect the rest of the fields in the form. However, when another geolocation field is updated, this address field will be populated with the new location values. By checking this checkbox you will also disable the locator button and address autocomplete of this field.', 'gfgeo' );

		// address autocomplete.
		$tooltips['gfgeo_address_autocomplete_feature_tt']         = __( 'Use Google Places API to display suggested addresses while typing an address.', 'gfgeo' );
		$tooltips['gfgeo_address_autocomplete_tt']                 = __( 'Enable live suggested results by Google Maps Places API while the user is typing an address.', 'gfgeo' );
		$tooltips['gfgeo_force_autocomplete_selection_tt']         = __( 'Trigger this field only when an address was selected from the address autocomplete suggested results and not when entering an address manually.', 'gfgeo' );
		$tooltips['gfgeo_force_autocomplete_selection_message_tt'] = __( 'Enter the message to display when the user tries to use an address that was entered manually instead of selecting an address from the suggested results.', 'gfgeo' );
		$tooltips['gfgeo_address_autocomplete_usage_tt']           = __( 'Select "Use the first text field of the Address Field" to use the first text input field of the Address field for the address autocomplete or "Add additional text field for the autocomplete" to add an extra text field that will be used for the address autocomplete.', 'gfgeo' );
		$tooltips['gfgeo_address_autocomplete_types_tt']           = __( 'Select the type of results that will be displayed in the suggested results. <a href="https://developers.google.com/maps/documentation/javascript/places-autocomplete#add_autocomplete" target="_blank">Click here</a> to read more about the different autocomplete types.', 'gfgeo' );
		$tooltips['gfgeo_autocomplete_restriction_usage_tt']       = __( 'Use the select dropdown menu to restrict the address autocomplete suggested results based on specific countries, proximity, specific area, or based on the location returned by the page-locator feature.', 'gfgeo' );
		$tooltips['gfgeo_address_autocomplete_country_tt']         = __( 'Select the countries that you would like to restrict the address autocomplete suggested results to.', 'gfgeo' );
		$tooltips['gfgeo_autocomplete_proximity_restriction_tt']   = __( 'Restrict the address autocomplete to display suggested results nearby a specific location by providing the coordinates of the location and the radius to which you would like to restrict the results.', 'gfgeo' );
		$tooltips['gfgeo_autocomplete_proximity_lat_tt']           = __( 'Enter the latitude of the location that you would like to restrict.', 'gfgeo' );
		$tooltips['gfgeo_autocomplete_proximity_lng_tt']           = __( 'Enter the longitude of the location that you would like to restrict.', 'gfgeo' );
		$tooltips['gfgeo_autocomplete_proximity_radius_tt']        = __( 'Enter the radius of the area that you would like to restrict in meters.', 'gfgeo' );
		$tooltips['gfgeo_address_autocomplete_locator_bounds_tt']  = __( 'Display the address autocomplete suggested results based on the location returned from the page locator.', 'gfgeo' );
		$tooltips['gfgeo_address_autocomplete_restrict_bounds_tt'] = __( 'By default, the plugin will prioritize the suggested results based on addresses nearest to the restricted area. But it will not limit the results to that area. Check this checkbox if you would like to limit the suggested results to the restricted area only.', 'gfgeo' );
		$tooltips['gfgeo_address_autocomplete_placeholder_tt']     = __( 'Enter the placeholder for the address autocomplete text field.', 'gfgeo' );
		$tooltips['gfgeo_address_autocomplete_desc_tt']            = __( 'Enter a description that you would like to display below the address autocomplete text field.', 'gfgeo' );
		$tooltips['gfgeo_autocomplete_bounds_restriction_tt']      = __( 'Restrict the adderss autocomplete suggested results to a specific area by providing the Southwest and northeast points of this area. ', 'gfgeo' );

		// Geocoder Field.

			$tooltips['gfgeo_default_coordinates_label_tt'] = __( 'You can enter a set of coordiantes of the initial location that will be dynamically geocoded and displayed in the geolocation fields attached to this geocoder when the form first loads.', 'gfgeo' );
			$tooltips['gfgeo_default_latitude_tt']          = __( 'Enter the latitude of the initial location.', 'gfgeo' );
			$tooltips['gfgeo_default_longitude_tt']         = __( 'Enter the longitude of the initial location.', 'gfgeo' );
			$tooltips['gfgeo_page_locator_label_tt']        = __( 'The page auto locator will try to automatically retrieve the user\'s current position when the form first loads. If a location was found, it will be dynamically populated in the location fields attached to this geocoder. Note that when the page locator feature is enabled, the default coordinates setting above will be ignored.', 'gfgeo' );
			$tooltips['gfgeo_page_locator_tt']              = __( 'Check this checkbox to enable the page auto locator feature.', 'gfgeo' );
			$tooltips['gfgeo_ip_locator_status_tt']         = __( 'enable this feature to retrieve the user\'s current location based on his/her IP address. Select "Default" to use the IP address instead of the browser\'s locator ( HTML5 geolocation ) or "Fallback" to use the IP address only when the browser fails to retrieve the location. Please note that while the IP address locator does not require the user\'s permission to retrieve the location ( same way as the browser does ), it is also not as accurate compare to the browser\'s geolocation.', 'gfgeo' );

			// Map Marker.
			$tooltips['gfgeo_map_marker_default_latitude_tt']  = __( 'Enter the latitude of the initial location where the marker will be display when the form first loads.', 'gfgeo' );
			$tooltips['gfgeo_map_marker_default_longitude_tt'] = __( 'Enter the longitude of the initial location where the marker will be display when the form first loads.', 'gfgeo' );
			$tooltips['gfgeo_map_marker_url_tt']               = __( 'Enter the URL of an image that will be used as the map marker.', 'gfgeo' );
			$tooltips['gfgeo_marker_info_window_tt']           = __( 'Enter content for the marker info-window which opens with a click on a marker. Otherwise, leave this textbox blank to disable the info-window.', 'gfgeo' );
			$tooltips['gfgeo_map_marker_hidden_tt']            = __( 'Check this checkbox to hide the marker when the form first load. It will become visible once directions or geocoding takes place ( using another location/directions field synced with this geocoder ).', 'gfgeo' );
			$tooltips['gfgeo_disable_marker_drag_tt']          = __( 'By checking this checkbox, users will not be able to drag the marker on the map.', 'gfgeo' );
			$tooltips['gfgeo_move_marker_via_map_click_tt']    = __( 'Move the marker with a click on the map.', 'gfgeo' );
			$tooltips['gfgeo_address_output_disabled_tt']      = __( 'Disable the output to the Address fields in the form when moving the marker on the map. This way, only the Coordinates fields, if exist in the form, will be updated with the new location. This can be useful for a specific scenario where a user wants to first set the location by entering an address. Then, if the address entered is correct but the marker is not on the exact location on the map or the coordinates are not the exact desired coordinates, the user can drag the marker to find the exact coordinates without affecting the value of the Address field.', 'gfgeo' );

			// GEO my WP integration.
			$tooltips['gfgeo_gmw_post_integration_tt']         = __( 'Check this checkbox to sync this Geocoder field with GEO my WP Posts Locator extension. The location from this Geocoder will then be saved in GEO my WP\'s locations database and will be synced with the post created by this Gravity Forms.', 'gfgeo' );
			$tooltips['gfgeo_gmw_post_integration_phone_tt']   = __( 'You can select a field that will be used as the GEO my WP Phone field.', 'gfgeo' );
			$tooltips['gfgeo_gmw_post_integration_fax_tt']     = __( 'You can select a field that will be used as the GEO my WP Fax field.', 'gfgeo' );
			$tooltips['gfgeo_gmw_post_integration_email_tt']   = __( 'You can select a field that will be used as the GEO my WP Email field.', 'gfgeo' );
			$tooltips['gfgeo_gmw_post_integration_website_tt'] = __( 'You can select a field that will be used as the GEO my WP Website field.', 'gfgeo' );
			$tooltips['gfgeo_gmw_user_integration_tt']         = __( 'Check this checkbox to sync this Geocoder field with GEO my WP users database. The location from this Geocoder will then be saved in GEO my WP database and the user attached to it will be searchable via GEO my WP search forms', 'gfgeo' );

			// user meta field.
			$tooltips['gfgeo_user_meta_field_tt']            = __( 'You may enter a user meta field where you\'d like to save the complete geocoded information as an array. Otherwise, leave the field blank.', 'gfgeo' );
			$tooltips['gfgeo_geocoder_meta_fields_setup_tt'] = __( 'Click the "Show Fields" to see the list of the location fields that you can save into post custom fields, user meta fields, and BuddyPress xprofile fields ( BuddyPress plugin required ).', 'gfgeo' );

			// Distance - deprecated.
			$tooltips['gfgeo_distance_destination_geocoder_id_tt']  = __( 'Select the geocoder which you would like to calculate the distance to.', 'gfgeo' );
			$tooltips['gfgeo_distance_travel_mode_tt']              = __( 'Select the travel mode.', 'gfgeo' );
			$tooltips['gfgeo_distance_unit_system_tt']              = __( 'Select the unit system that will be used when calculating the distance.', 'gfgeo' );
			$tooltips['gfgeo_distance_travel_show_route_on_map_tt'] = __( 'Display driving route on a map.', 'gfgeo' );
			$tooltips['gfgeo_distance_directions_panel_id_tt']      = __( 'Display driving directions.', 'gfgeo' );

		// End Geocoder Field.

		// Directions field.
		$tooltips['gfgeo_directions_field_usage_tt']             = __( 'Select between driving directions and distance calcualted by Google Maps API and a straight line ( "as the crow flies" ) distance.', 'gfgeo' );
		$tooltips['gfgeo_directions_field_directions_points_tt'] = __( 'You must select at least the geocoders that will be used as the origin and destination points. In addition, you can also select the geocoders that will be used as waypoints.', 'gfgeo' );
		$tooltips['gfgeo_origin_geocoder_id_tt']                 = __( 'Select the Geocoder field that will be used as the origin location.', 'gfgeo' );
		$tooltips['gfgeo_destination_geocoder_id_tt']            = __( 'Select the Geocoder field that will be used as the destination location.', 'gfgeo' );
		$tooltips['gfgeo_waypoints_geocoders_tt']                = __( 'Select the geocoder fields that will be used as waypoints.', 'gfgeo' );
		$tooltips['gfgeo_travel_mode_tt']                        = __( 'Select the travel mode.', 'gfgeo' );
		$tooltips['gfgeo_unit_system_tt']                        = __( 'Select the unit system.', 'gfgeo' );
		$tooltips['gfgeo_route_map_id_tt']                       = __( 'Select a Map field if you would like to display the routes.', 'gfgeo' );
		$tooltips['gfgeo_route_polyline_options_tt']             = __( 'Set the polyline attributes. You can set the color using "strokeColor", the weight using "strokeWeight", and the opacity using "strokeOpacity". You can set one or more attributes comma separated. For example: strokeColor:\'#0088FF\',strokeWeight:6,strokeOpacity:0.6.', 'gfgeo' );
		$tooltips['gfgeo_directions_panel_id_tt']                = __( 'Select a Directions Panel field if you would like to display the directions steps.', 'gfgeo' );
		$tooltips['gfgeo_trigger_directions_method_tt']          = __( 'Select "Dynamically" to dynamically trigger the directions when geocoding takes place in the form or select "Using a button" to trigger the directions with a click on a button.', 'gfgeo' );
		$tooltips['gfgeo_get_directions_button_label_tt']        = __( 'Enter the button label.', 'gfgeo' );
		$tooltips['gfgeo_clear_directions_button_label_tt']      = __( 'Enter the button label or leave it blank to disable this button.', 'gfgeo' );

		return $tooltips;
	}

	/**
	 * New field default options
	 */
	public function set_default_labels() {
		?>
		case "gfgeo_geocoder" :
			field.label 					 		 = "Geocoder";
			field.gfgeo_page_locator 		 		 = "";
			field.gfgeo_ip_locator_status            = "";
			field.gfgeo_location_found_message 		 = "Location Found";
			field.gfgeo_hide_location_failed_message = "";
			field.gfgeo_default_latitude 	 		 = "";
			field.gfgeo_default_longitude 	 		 = "";
			field.gfgeo_map_marker_default_latitude  = "";
			field.gfgeo_map_marker_default_longitude = "";
			field.gfgeo_map_marker_url         	     = "";
			field.gfgeo_marker_info_window           = "";
			field.gfgeo_map_marker_hidden            = false;
			field.gfgeo_disable_marker_drag 	     = false;
			field.gfgeo_move_marker_via_map_click    = false;
			field.gfgeo_address_output_disabled      = false;
			field.gfgeo_user_meta_field 	 		 = "";
			field.gfgeo_gmw_post_integration 		 = false;
			field.gfgeo_gmw_post_integration_phone   = "";
			field.gfgeo_gmw_post_integration_fax     = "";
			field.gfgeo_gmw_post_integration_email   = "";
			field.gfgeo_gmw_post_integration_website = "";
			field.gfgeo_gmw_user_integration 		 = false;
			field.gfgeo_gmw_user_location_usage      = "update_location";
			field.gfgeo_gmw_user_location_type 		 = "";
		break;

		case "gfgeo_address" :
			field.label 							         = "Address";
			field.gfgeo_geocoder_id                          = "";
			field.gfgeo_infield_locator_button 		         = true;
			field.gfgeo_ip_locator_status                    = "";
			field.gfgeo_location_found_message 		         = "Location Found";
			field.gfgeo_hide_location_failed_message         = "";
			field.gfgeo_address_autocomplete 		         = true;
			field.gfgeo_force_autocomplete_selection         = "";
			field.gfgeo_force_autocomplete_selection_message = "Please select an address from the suggested results.";
			field.gfgeo_address_autocomplete_types 	         = "";
			field.gfgeo_autocomplete_restriction_usage	     = "";
			field.gfgeo_address_autocomplete_strict_bounds   = false;
			field.gfgeo_autocomplete_bounds_sw_point         = "";
			field.gfgeo_autocomplete_bounds_ne_point         = "";
			field.gfgeo_autocomplete_proximity_lat           = "";
			field.gfgeo_autocomplete_proximity_lng           = "";
			field.gfgeo_autocomplete_proximity_radius        = "";
		break;

		case "gfgeo_map" :
			field.label 			 	       = "Map";
			field.gfgeo_geocoder_id            = "";
			field.gfgeo_map_default_latitude   = "40.7827096";
			field.gfgeo_map_default_longitude  = "-73.965309";
			field.gfgeo_map_type   		 	   = "ROADMAP";
			field.gfgeo_zoom_level 		 	   = "12";
			field.gfgeo_map_width  		 	   = "100%";
			field.gfgeo_map_height 		 	   = "300px";
			field.gfgeo_map_styles   		   = "";
			field.gfgeo_map_scroll_wheel 	   = true;
			field.gfgeo_map_marker         	   = "";
			field.gfgeo_draggable_marker 	   = true;
			field.gfgeo_set_marker_on_click    = false;
			field.gfgeo_disable_address_output = false;
		break;

		case "gfgeo_locator_button" :
			field.label 					   		 = "Locator Button";
			field.gfgeo_geocoder_id                  = "";
			field.gfgeo_ip_locator_status 	   		 = "";
			field.gfgeo_locator_button_label   		 = "Get my current position";
			field.gfgeo_location_found_message 		 = "Location found.";
			field.gfgeo_hide_location_failed_message = "";
		break;

		case "gfgeo_reset_location_button" :
			field.label 					   		 = "Reset Location Button";
			field.gfgeo_geocoder_id                  = "";
			field.gfgeo_reset_location_button_label  = "Reset Location";
		break;

		case "gfgeo_coordinates" :
			field.label 					  = "Coordinates";
			field.gfgeo_geocoder_id           = "";
			field.gfgeo_latitude_placeholder  = "Latitude";
			field.gfgeo_longitude_placeholder = "longitude";
			field.gfgeo_custom_field_method   = false;
		break;

		case "gfgeo_directions" :
			field.label 					   		  = "Directions, Distance & Routes";
			field.description 					      = "";
			field.gfgeo_origin_geocoder_id            = "";
			field.gfgeo_destination_geocoder_id       = "";
			field.gfgeo_waypoints_geocoders           = "";
			field.gfgeo_travel_mode                   = "DRIVING";
			field.gfgeo_unit_system                   = "imperial";
			field.gfgeo_route_map_id                  = "";
			field.gfgeo_route_polyline_options        = "";
			field.gfgeo_directions_panel_id           = "";
			field.gfgeo_trigger_directions_method     = "button";
			field.gfgeo_get_directions_button_label   = "Get Directions";
			field.gfgeo_clear_directions_button_label = "Clear Directions";
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
		return $form;
	}
}
$gfgeo_form_editor = new GFGEO_Form_Editor();
