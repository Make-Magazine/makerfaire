<?php

if ( ! class_exists( 'GFForms' ) ) {
    return;
}

GFForms::include_addon_framework();

class GravityView_Inline_Edit_GFAddon extends GFAddOn {

	/**
	 * @var string Minimum required GF version
	 */
	protected $_min_gravityforms_version = '2.0';

	/**
	 * @var string Slug for settings page URL
	 */
	protected $_slug = 'gravityview-inline-edit';

	/**
	 * @var string Path used by GF to add Settings link to addon settings in Plugins page
	 */
	protected $_path = 'gravityview-inline-edit/gravityview-inline-edit.php';

	protected $_full_path = __FILE__;

	protected $_title = 'GravityView Inline Edit';

	protected $_short_title = 'GravityView Plugins';

	private static $_instance = null;

	/**
	 * GravityView_Inline_Edit_Settings constructor.
	 */
	public function __construct() {

		if ( self::$_instance ) {
			return self::$_instance;
		}

		$this->_title = esc_html__( 'GravityView Inline Edit', 'gravityview-inline-edit' );

		$this->_short_title = esc_html__( 'Inline Edit', 'gravityview-inline-edit' );

		parent::__construct();
	}

	/**
	 * Returns TRUE if the settings "Save" button was pressed
     *
     * @since 1.0.3 Fixes conflict with Import Entries plugin
     *
     * @return bool True: Settings form is being saved and the Inline Edit setting is in the posted values (form settings)
	 */
	public function is_save_postback() {
		return ! rgempty( 'gform-settings-save' ) && ( isset( $_POST['gv_inline_edit_enable'] ) || isset( $_POST['_gravityview-inline-edit_save_settings_nonce'] ) || isset( $_POST['_gaddon_setting_inline-edit-mode'] ) );
	}

	/**
	 * Get the one instance of the object
	 *
	 * @since 1.0
	 *
	 * @return GravityView_Inline_Edit_GFAddon
	 */
	public static function get_instance() {

		if ( self::$_instance == null ) {

			self::$_instance = new self();

			GFAddOn::register( 'GravityView_Inline_Edit_GFAddon' );
		}

		return self::$_instance;
	}

	/**
	 * Returns HTML tooltip for the     edit mode setting
	 *
	 * @since 1.0
	 *
	 * @return string HTML for the tooltip about the edit modes
	 */
	private function _get_edit_mode_tooltip_html() {

		$tooltips = array(
			'popup' => array(
				'image' => 'gf-popup',
				'description' => esc_html__('Popup: The edit form will appear above the content.', 'gravityview-inline-edit'),
			),
			'inline' => array(
				'image' => 'gf-in-place',
				'description' => esc_html__('In-Place: The edit form for the field will show in the same place as the content.', 'gravityview-inline-edit'),
			),
		);

		$tooltip_format = '<p class="gv-inline-edit-mode-image" data-edit-mode="%s"><img src="%s" height="150" style="display: block; margin-bottom: .5em;" /><strong>%s</strong></p>';

		$tooltip_html = '';

		foreach ( $tooltips as $mode => $tooltip ) {

			$image_link = plugins_url( "assets/images/{$tooltip['image']}.png", GRAVITYVIEW_INLINE_FILE );

			$tooltip_html .= sprintf( $tooltip_format, $mode, $image_link, $tooltip['description'] );
		}

		return $tooltip_html;
	}

	/**
	 * Register the settings field for the EDD License field type
	 *
	 * @since 1.0
	 *
	 * @param array $field
	 * @param bool $echo Whether to echo the
	 *
	 * @return string
	 */
	protected function settings_edd_license( $field, $echo = true ) {

		// Otherwise, it'd be output as an attribute. Didn't want to use the `gaddon_no_output_field_properties` filter
		unset( $field['description'] );

		$return = self::settings_text( $field, false );

		if ( $echo ) {
			echo $return;
		}

		return $return;
	}

	/**
	 * Add print_styles hook in the admin
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function init_admin() {

		// enqueues admin scripts
		add_action( 'admin_head', array( $this, 'print_select_scripts' ), 10 );

		parent::init_admin();
	}

	/**
	 * Print inline CSS and JS to make improve how <select> works
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function print_select_scripts() {
		?>
        <script>
            jQuery( document ).ready( function( $ ) {
               $('select[name*="edit-mode"]').on('change', function() {
                   $( '.gv-inline-edit-mode-image' ).hide().filter('[data-edit-mode="' + $( this ).val() + '"]').show();
               }).trigger('change');
            });
        </script>

        <style>
            #gaddon-setting-row-inline-edit-mode .gv-inline-edit-mode-image {
                display: none;
                margin-top: 15px;
            }
        </style>
        <?php
	}

	/**
	 * Perform the call to EDD based on the AJAX call or passed data
	 *
	 * @since 1.0
	 *
	 * @param array $settings {
	 * @type string $edd_action The EDD action to perform, like `check_license`
	 * @type string $license The license key
	 * @type string $format If `object`, return the object of the license data. `array` for array, `json` to return the JSON-encoded object. [Default: "array"]
	 * }
	 *
	 * @return mixed|WP_Error
	 */
	public function license_call( $settings = array() ) {

		$current_settings = $this->get_current_settings();

		$data = wp_parse_args( $settings, array(
			'edd_action' => 'activate_license',
		    'license_key'    => rgar( $current_settings, 'license_key' ),
		    'format'     => 'array',
		) );

		$api_params = array(
			'edd_action' => rgar( $data, 'edd_action' ),
			'version'   => GravityView_Inline_Edit::get_version(),
			'license'   => rgar( $data, 'license_key' ),
			'item_name' => GravityView_Inline_Edit::get_title(),
			'item_id'   => GravityView_Inline_Edit::get_item_id(),
			'author'    => GravityView_Inline_Edit::get_author(),
			'url'       => home_url(),
		);

		$url = add_query_arg( $api_params, GravityView_Inline_Edit::get_remote_update_url() );

		$response = wp_remote_get( $url, array(
			'timeout'   => 15,
			'sslverify' => false,
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// Not JSON; there's been an error.
		if ( empty( $license_data ) ) {
			return new WP_Error( 'invalid_response', 'There was a problem connecting to the GravityView licensing server.' );
		}

		$license_data->message = $this->get_license_message( $license_data );

		// Store the license key inside the data array
		$license_data->license_key = rgar( $data, 'license_key' );

		switch( rgar( $data, 'format' ) ) {
			case 'object':
				$return = $license_data;
				break;
			case 'json':
				$return = json_encode( $license_data );
				break;
			case 'array':
			default:
				$return = (array) $license_data;
				break;
		}

		return $return;
	}

	/**
	 * Generate the status message displayed in the license field
	 *
	 * @since 1.0
	 *
	 * @param object|null $license_data Remote response from GravityView license call, or NULL if bad JSON returned
	 *
	 * @return string
	 */
	private function get_license_message( $license_data ) {

		if ( empty( $license_data ) ) {
			$message = $this->strings( 'error' );
		} else {
			$key = ! empty( $license_data->error ) ? $license_data->error : $license_data->license;

			$message = $this->strings( $key, $license_data );
		}

		$message = sprintf( '<p><strong>%s: %s</strong></p>', $this->strings( 'status' ), $message );

		return $message;
	}

	/**
	 * Have one place for all the various strings involved in license validation
	 *
	 * @since 1.0
	 *
	 * @param  array|null $status Status to get. If empty, get all strings.
	 * @param  object|null $license_data Response from GravityView license server
	 *
	 * @return array          Modified array of content
	 */
	public function strings( $status = NULL, $license_data = null ) {

		$renewal_url = ! empty( $license_data->renewal_url ) ? $license_data->renewal_url : 'https://gravityview.co/account/';

		$strings = array(
			'status'              => esc_html__( 'Status', 'gravityview-inline-edit' ),
			'error'               => esc_html__( 'There was an error processing the request.', 'gravityview-inline-edit' ),
			'failed'              => esc_html__( 'Could not deactivate the license. The license key you attempted to deactivate may not be active or valid.', 'gravityview-inline-edit' ),
			'site_inactive'       => esc_html__( 'The license key is valid, but it has not been activated for this site.', 'gravityview-inline-edit' ),
			'no_activations_left' => esc_html__( 'Invalid: this license has reached its activation limit.', 'gravityview-inline-edit' ),
			'deactivated'         => esc_html__( 'The license has been deactivated.', 'gravityview-inline-edit' ),
			'valid'               => esc_html__( 'The license key is valid and active.', 'gravityview-inline-edit' ),
			'invalid'             => esc_html__( 'The license key entered is invalid.', 'gravityview-inline-edit' ),
			'missing'             => esc_html__( 'The license key entered is invalid.', 'gravityview-inline-edit' ), // Missing is "the license couldn't be found", not "you submitted an empty license"
			'revoked'             => esc_html__( 'This license key has been revoked.', 'gravityview-inline-edit' ),
			'expired'             => sprintf( esc_html__( 'This license key has expired. %sRenew your license on the GravityView website%s', 'gravityview-inline-edit' ), '<a href="' . esc_url( $renewal_url ) . '" rel="external">', '</a>' ),
			'verifying_license'   => esc_html__( 'Verifying license&hellip;', 'gravityview-inline-edit' ),
			'activate_license'    => esc_html__( 'Activate License', 'gravityview-inline-edit' ),
			'deactivate_license'  => esc_html__( 'Deactivate License', 'gravityview-inline-edit' ),
			'check_license'       => esc_html__( 'Verify License', 'gravityview-inline-edit' ),
		);

		if ( empty( $status ) ) {
			return $strings;
		}

		if ( isset( $strings[ $status ] ) ) {
			return $strings[ $status ];
		}

		return NULL;
	}

	/**
	 * When the plugin settings are saved, validate the license key
	 *
	 * @since 1.0
	 *
	 * @param array $field Setting field meta
	 * @param array $settings Submitted settings
	 *
	 * @return bool|null True if valid or license key is empty; false if any other situation occurs
	 */
	public function validate_edd_license_settings( $field = array(), $settings = array() ) {

		if( '' === rgar( $settings, 'license_key' ) ) {
			return true;
		}

		$license = GravityView_Inline_Edit::get_instance()->get_license( false, array(
			'license'    => rgar( $settings, 'license_key' ),
			'format'     => 'array',
		    'edd_action' => 'activate_license',
		) );

		$status = rgar( $license, 'license' );

		switch ( $status ) {
			case 'valid':
				$return = true;
				break;
			case 'invalid':
			default:
				$return = false;
				$this->set_field_error( $field, $license['message'] );
				break;
		}

		// Regardless of the results, save the settings with posted values, because by default,
		// GF won't save if there's an error.
		$this->update_plugin_settings( $settings );

		return $return;
	}

	/**
	 * Show a x or check if valid or invalid, and if error, set the field error message
	 *
	 * If the plugin hasn't been saved, no error or checkbox
	 *
	 * @since 1.0
	 *
	 * @param string $value The setting for a specific field/input
	 * @param array $field The current plugin setting field (from GravityView_Inline_Edit_GFAddon::plugin_settings_fields)
	 *
	 * @return bool|null True: no errors, valid. False: errors, not valid. NULL: settings not yet saved.
	 */
	public function setup_field_validation_feedback( $value, $field ) {

		// If the plugin settings haven't been saved yet, no error
		if ( ! $this->get_plugin_settings() ) {
			return null;
		}

		$license = GravityView_Inline_Edit::get_instance()->get_license();

		// Show the stored error message
		if( ! $success = rgar( $license, 'success' ) ) {
			$this->set_field_error( $field, rgar( $license, 'message' ) );
		}

		$errors = $this->get_field_errors( $field );

		return empty( $errors );
	}

	/**
	 * Override the invalid field icon so there aren't duplicate icons, and so we can format the error message
	 *
	 * @since 1.0
	 *
	 * @param array $field - The field meta.
	 *
	 * @return string - The full markup for the error
	 */
	public function get_error_icon( $field ) {

		$error = $this->get_field_errors( $field );

		return '<div class="alert_red clear" style="padding: 6px 10px 10px;">' . $error . '</div>';
	}

	/**
	 * Define the plugin addon settings
	 *
	 * @since 1.0
	 *
	 * @return array Array that contains plugin settings
	 */
	public function plugin_settings_fields() {
		return array(
			array(
				'title'  => '',
				'fields' => array(
					array(
						'name'          => 'license_key',
						'required'      => true,
						'label'         => __( 'Support License Key', 'gravityview-inline-edit' ),
						'description'   => '<p class="clear">'.__( 'Enter the license key that was sent to you on purchase. This enables plugin updates &amp; support.', 'gravityview-inline-edit' ).'</p>',
						'type'          => 'edd_license',
						'feedback_callback' => array( $this, 'setup_field_validation_feedback' ),
						'placeholder'   => esc_html__('Enter your license key here', 'gravityview-inline-edit' ),
						'default_value' => '',
						'class'         => 'activate code regular-text edd-license-key',
					),
					array(
						'name'          => 'inline-edit-mode',
						'type'          => 'select',
						'label'         => __( 'Inline Edit Mode', 'gravityview-inline-edit' ),
						'tooltip'       => $this->_get_edit_mode_tooltip_html(),
                        'after_select'  => $this->_get_edit_mode_tooltip_html(),
						'description'   => esc_html__( 'Change where the Inline Edit form appears &ndash; above the content or in its place.', 'gravityview-inline-edit' ),
						'default_value' => 'popup',
						'horizontal'    => 1,
						'choices'       => array(
							array
							(
								'label' => esc_html__( 'Popup', 'gravityview-inline-edit' ),
								'tooltip' => esc_html__('Popup: The edit form will appear above the content.', 'gravityview-inline-edit'),
								'value' => 'popup',
								'name'  => 'inline-edit-mode-popup',
							    'icon'  => plugins_url( "assets/images/popup.png", GRAVITYVIEW_INLINE_FILE ),
							),
							array
							(
								'label' => esc_html__('In-Place', 'gravityview-inline-edit'),
								'tooltip' => esc_html__( 'In-Place: The edit form for the field will show in the same place as the content.', 'gravityview-inline-edit'),
								'value' => 'inline',
								'name'  => 'inline-edit-mode-inline',
								'icon'  => plugins_url( "assets/images/in-place.png", GRAVITYVIEW_INLINE_FILE ),
							),

						),
					)
				)
			)
		);
	}

	/**
	 * Don't show the uninstall form
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function render_uninstall() {}

}