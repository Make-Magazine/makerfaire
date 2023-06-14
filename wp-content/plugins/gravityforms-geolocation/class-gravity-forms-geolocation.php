<?php
/**
 * Gravity Forms main class - Gravity_Forms_Geolocatio.
 *
 * @author  Eyal Fitoussi.
 *
 * @package gravityforms-geolocation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Include Gravity Forms add-on framework
 */
GFForms::include_addon_framework();

/**
 * Gravity Forms Geolocation child class
 *
 * @since 2.0
 *
 * @author Eyal Fitoussi
 */
class Gravity_Forms_Geolocation extends GFAddOn {

	/**
	 * Version.
	 *
	 * @var [type]
	 */
	protected $_version = GFGEO_VERSION;

	/**
	 * Min GF version.
	 *
	 * @var string.
	 */
	protected $_min_gravityforms_version = '1.9';

	/**
	 * Plugin's name.
	 *
	 * @var string
	 */
	protected $_title = 'Gravity Forms Geolocation';

	/**
	 * Slug.
	 *
	 * @var string
	 */
	protected $_slug = 'gravityforms_geolocation';

	/**
	 * Full Path.
	 *
	 * @var [type]
	 */
	protected $_full_path = __FILE__;

	/**
	 * Short title.
	 *
	 * @var string
	 */
	protected $_short_title = 'Geolocation';

	/**
	 * Instance.
	 *
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Creates a new instance of the Gravity_Forms_Geolocation.
	 *
	 * Only creates a new instance if it does not already exist
	 *
	 * @static
	 *
	 * @return object The Gravity_Forms_Geolocation class object
	 */
	public static function get_instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * LK Status
	 *
	 * @var boolean
	 */
	protected $license = false;

	/**
	 * Construct.
	 */
	public function __construct() {

		$this->_capabilities               = array(
			'gravityforms_gfgeo_settings',
			'gravityforms_gfgeo_form_settings',
			'gravityforms_gfgeo_uninstall',
		);
		$this->_capabilities_settings_page = 'gravityforms_gfgeo_settings';
		$this->_capabilities_form_settings = 'gravityforms_gfgeo_form_settings';
		$this->_capabilities_uninstall     = 'gravityforms_gfgeo_uninstall';

		parent::__construct();
	}

	/**
	 * Pre init.
	 */
	public function pre_init() {

		parent::pre_init();

		$this->license = self::verify_license();

		// define globals.
		define( 'GFGEO_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( $this->_full_path ) ), basename( $this->_full_path ) ) ) );
		define( 'GFGEO_PATH', untrailingslashit( plugin_dir_path( $this->_full_path ) ) );

		// get Google API key from settings.
		$api_key        = $this->get_plugin_setting( 'gfgeo_google_maps_api_key' );
		$server_api_key = $this->get_plugin_setting( 'gfgeo_google_maps_server_api_key' );
		$api_country    = $this->get_plugin_setting( 'gfgeo_google_maps_country' );
		$api_language   = $this->get_plugin_setting( 'gfgeo_google_maps_language' );
		$high_accuracy  = $this->get_plugin_setting( 'gfgeo_enable_high_accuracy_mode' );
		$ip_locator     = $this->get_plugin_setting( 'gfgeo_enable_ip_locator' );
		$prefix         = get_option( 'gfgeo_prefix' );

		define( 'GFGEO_GOOGLE_MAPS_API', ! empty( $api_key ) ? $api_key : '' );
		define( 'GFGEO_GOOGLE_MAPS_SERVER_API_KEY', ! empty( $server_api_key ) ? $server_api_key : '' );
		define( 'GFGEO_GOOGLE_MAPS_COUNTRY', ! empty( $api_country ) ? $api_country : '' );
		define( 'GFGEO_GOOGLE_MAPS_LANGUAGE', ! empty( $api_language ) ? $api_language : '' );
		define( 'GFGEO_HIGH_ACCURACY_MODE', ! empty( $high_accuracy ) ? true : false );
		define( 'GFGEO_IP_LOCATOR', ! empty( $ip_locator ) ? $ip_locator : false );
		define( 'GFGEO_GF_2_5', version_compare( GFForms::$version, '2.4.99', '>' ) ? true : false );
		define( 'GFGEO_PREFIX', ! empty( $prefix ) ? $prefix : 'gfgeo_' );

		if ( false != GFGEO_IP_LOCATOR ) {
			$ip_token = $this->get_plugin_setting( 'gfgeo_token_' . GFGEO_IP_LOCATOR );
		}

		define( 'GFGEO_IP_TOKEN', ! empty( $ip_token ) ? $ip_token : false );

		// Tasks in admin only.
		if ( is_admin() ) {
			add_action( 'gform_settings_gravityforms_geolocation', array( $this, 'geolocation_settings' ), 5 );
		}

		// include files in both front and back-end.
		include_once 'includes/class-gfgeo-helper.php';

		if ( is_admin() ) {
			include_once 'includes/admin/class-gfgeo-updater.php';
		}

		if ( $this->is_gravityforms_supported() && class_exists( 'GF_Field' ) ) {
			include_once 'includes/fields/class-gfgeo-fields-group.php';
			include_once 'includes/fields/class-gfgeo-geocoder-field.php';
			include_once 'includes/fields/class-gfgeo-address-field.php';
			include_once 'includes/fields/class-gfgeo-google-map-field.php';
			include_once 'includes/fields/class-gfgeo-coordinates-field.php';
			include_once 'includes/fields/class-gfgeo-locator-button-field.php';
			include_once 'includes/fields/class-gfgeo-reset-location-button-field.php';
			include_once 'includes/fields/class-gfgeo-directions-field.php';
			include_once 'includes/fields/class-gfgeo-directions-panel-field.php';
			include_once 'includes/class-gfgeo-mashup-map.php';
		}
	}

	/**
	 * Initiate add-on.
	 */
	public function init() {

		parent::init();

		$disable_google_api = $this->get_plugin_setting( 'gfgeo_disable_google_maps_api' );
		$disable_google_api = ! empty( $disable_google_api ) ? true : false;
		$disable_google_api = is_admin() ? false : $disable_google_api;

		define( 'GFGEO_DISABLE_GOOGLE_MAPS_API', apply_filters( 'gfgeo_disable_google_maps_api', $disable_google_api ) );

		// load textdomain.
		load_plugin_textdomain( 'gfgeo', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		include_once 'includes/class-gfgeo-form-submission.php';
		include_once 'includes/class-gfgeo-render-form.php';
	}

	/**
	 * Admin init
	 *
	 * @return void
	 */
	public function init_admin() {

		parent::init_admin();

		// include form editor page class.
		include 'includes/admin/class-gfgeo-form-editor.php';
	}

	/**
	 * Generate License Key Settings.
	 *
	 * @author Eyal Fitoussi
	 *
	 * @since 3.0
	 */
	public function geolocation_settings() {
		do_action( 'gfgeo_license_element' );
		?>
		<!-- Dynamically replace the input text fields generated by Gravity Forms Settings with the license key settings -->
		<script type="text/javascript">

			jQuery( document).ready( function() {

				// Toggle IP Locator token input boxes when using the "Ip Address Locator" dropdown.
				jQuery( document ).on( 'change', '#gfgeo_enable_ip_locator', function() {

					var tBody = jQuery( this ).closest( '.gform-settings-panel__content, tbody' );
					var value = jQuery( this ).val();

					tBody.find( 'div[id^=gform_setting_gfgeo_token_]' ).hide();
					tBody.find( '#gform_setting_gfgeo_token_' + value ).show();

					// GF 2.4
					tBody.find( '.ip-locator-token-input' ).closest( 'tr' ).hide();
					tBody.find( 'tr#gaddon-setting-row-gfgeo_token_' + value ).show();
				});

				jQuery( '#gfgeo_enable_ip_locator' ).trigger( 'change' );

			});
		</script>

		<?php if ( empty( $this->license ) ) { ?>

		<style type="text/css">
			#tab_gravityforms_geolocation form .gaddon-section:not( .gaddon-first-section ) table {
				position: relative;
			}

			#tab_gravityforms_geolocation form .gaddon-section:not( .gaddon-first-section ) table:before {
				content: '';
				background: #f6fbfd;
				width: 100%;
				height: 100%;
				position: absolute;
				opacity: 0.8;
				z-index: 1;
			}

			#gform-settings fieldset.gform-settings-panel:not(:first-child) {
				position: relative;
				overflow: hidden;
			}

			#gform-settings fieldset.gform-settings-panel:not(:first-child):before {
				content: '';
				background: #f6fbfd;
				width: 100%;
				height: 100%;
				position: absolute;
				opacity: 0.7;
				z-index: 1;
				top: 47px;
				overflow: hidden;
			}
		</style>
		<?php
		}
	}

	/**
	 * Return the plugin's icon for the plugin/form settings menu.
	 *
	 * @return string
	 */
	public function get_menu_icon() {
		return 'dashicons-location';
	}

	/**
	 * Geolocation settings tab in Settings page
	 *
	 * @return array
	 */
	public function plugin_settings_fields() {

		// Load settings for Gravity Forms lower than v2.5.
		if ( ! GFGEO_GF_2_5 ) {
			return $this->plugin_settings_fields_2_4();
		}

		$settings   = array();
		/*$settings[] = array(
			'title'  => esc_html__( 'Gravity Geolocation License Key', 'gfgeo' ),
			'id'     => 'gfgeo_license_element_placeholder',
			'class'  => 'gfgeo_license_element_placeholder',
			'fields' => array(
				array(
					'name'              => 'gfgeo_license_key',
					'label'             => esc_html__( 'Gravity Geolocation License Key', 'gfgeo' ),
					'type'              => 'text',
					'class'             => 'large',
					'feedback_callback' => array( $this, 'is_valid_setting' ),
				),
			),
		);*/

		$settings[] = array(
			'id'          => 'section_gfgeo_google_maps_api_key',
			'title'       => esc_html__( 'Google Maps Browser API Key ( * Required )', 'gfgeo' ),
			/* Translators: %1$s : link, %2$s : link */
			'description' => sprintf( __( 'Enter your Google Map Browser API key. If you don\'t have a Browser API key, <a href="%1$s" target="_blank">click here</a> to create one. You can also follow <a href="%2$s" target="_blank">this tutorial</a> to learn on how to generate you Google Maps Browser API key.', 'gfgeo' ), 'https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key', 'http://docs.gravitygeolocation.com/article/101-create-google-map-api-key' ),
			'class'       => 'gform-settings-panel--half',
			'fields'      => array(
				array(
					'name' => 'gfgeo_google_maps_api_key',
					'type' => 'text',
				),
			),
		);

		$settings[] = array(
			'id'          => 'section_gfgeo_google_maps_country',
			'title'       => esc_html__( 'Google Maps Country Code', 'gfgeo' ),
			/* Translators: %s : link */
			'description' => sprintf( __( 'Enter the country code of the default country that will be used with Google Maps Geocoding services. See <a href="%s" target="_blank">this page</a> for the list of country codes.', 'gfgeo' ), 'https://docs.gravitygeolocation.com/article/209-google-maps-country-codes' ),
			'class'       => 'gform-settings-panel--half',
			'fields'      => array(
				array(
					'name' => 'gfgeo_google_maps_country',
					'type' => 'text',
				),
			),
		);

		$settings[] = array(
			'id'          => 'section_gfgeo_google_maps_language',
			'title'       => esc_html__( 'Google Maps Language', 'gfgeo' ),
			/* Translators: %s : link */
			'description' => sprintf( __( 'Enter the language code of the default language that will be used with Google Maps API. This will affect the language of the map and the address autocomplete suggested results. See <a href="%s" target="_blank">this page</a> for the list of language codes.', 'gfgeo' ), 'https://sites.google.com/site/tomihasa/google-language-codes' ),
			'class'       => 'gform-settings-panel--half',
			'fields'      => array(
				array(
					'name' => 'gfgeo_google_maps_language',
					'type' => 'text',
				),
			),
		);

		$settings[] = array(
			'id'          => 'section_gfgeo_enable_high_accuracy_mode',
			'title'       => esc_html__( 'High Accuracy Location Mode', 'gfgeo' ),
			'description' => esc_html__( 'By enabling this, the auto-locator ( when using the locator button and the page locator ) might retrieve a more accurate user current location. However, it might also result in slower performance of the auto-lcoator. Note that this feature only effects the browser\'s HTML5 geolocation feature, not the IP address locator. ', 'gfgeo' ),
			'class'       => 'gform-settings-panel--half',
			'fields'      => array(
				array(
					'name'         => 'gfgeo_enable_high_accuracy_mode',
					'type'         => 'toggle',
					'toggle_label' => esc_html__( 'Enable High Accuracy Mode', 'gravityforms' ),
				),
			),
		);

		$settings[] = array(
			'id'          => 'section_gfgeo_enable_ip_locator',
			'title'       => esc_html__( 'IP Address Locator', 'gfgeo' ),
			'description' => esc_html__( 'Use this option to enable the IP address locator services. You can do so by selecting the IP Address service provider that you would like to use. Once this feature is enabled, you will be able to use it with the Locator Button field and the auto-locator form option in the form builder. You can use it instead of the HTML5 geolocation feature or as a fall-back option for when the HTML5 geolocation fails. Please note that the accuracy level of the location returned by the IP address might vary and it depends solely on the IP Address service provider. Also note that some IP Address service providers might require registration and/or a token/API key.', 'gfgeo' ),
			'class'       => 'gform-settings-panel--half',
			'fields'      => array(
				array(
					'name'        => 'gfgeo_enable_ip_locator',
					'type'        => 'select',
					'label'       => esc_html__( 'IP Address Provider', 'gfgeo' ),
					'enhanced_ui' => true,
					'choices'     => array(
						array(
							'label' => esc_html__( 'Disable', 'gfgeo' ),
							'value' => '',
						),
						array(
							'label' => esc_html__( 'Ipapi.co', 'gfgeo' ),
							'value' => 'ipapico',
						),
						array(
							'label' => esc_html__( 'ipinfo.io', 'gfgeo' ),
							'value' => 'ipinfo',
						),
						array(
							'label' => esc_html__( 'Ipregistry', 'gfgeo' ),
							'value' => 'ipregistry',
						),
						array(
							'label' => esc_html__( 'MaxMind', 'gfgeo' ),
							'value' => 'maxmind',
						),
					),
				),
				array(
					'name'        => 'gfgeo_token_ipinfo',
					'type'        => 'text',
					'label'       => esc_html__( 'ipinfo.io Token', 'gfgeo' ),
					/* Translators: %s : link */
					'description' => sprintf( __( '<a href="%s" target="_blank">Click here</a> to go to the offical ipinfo.io site for more information, to register, and to generate a token.', 'gfgeo' ), 'https://ipinfo.io/' ),
					'class'       => 'ip-locator-token-input',
				),
				array(
					'name'        => 'gfgeo_token_ipregistry',
					'type'        => 'text',
					'label'       => esc_html__( 'Ipregistry API Key', 'gfgeo' ),
					/* Translators: %s : link */
					'description' => sprintf( __( '<a href="%s" target="_blank">Click here</a> to go to the offical Ipregistry site for more information, to register, and to generate an API key.', 'gfgeo' ), 'https://ipregistry.co/' ),
					'class'       => 'ip-locator-token-input',

				),
				array(
					'name'        => 'gfgeo_token_ipapico',
					'type'        => 'hidden',
					/* Translators: %s : link */
					'label'       => sprintf( __( 'Ipapi.co does not require an API key nor registration for its free plan. <a href="%s" target="_blank">Click here</a> to go to the offical ipapi.co site for more information about the plans and pricing.', 'gfgeo' ), 'https://ipregistry.co/' ),
					/* Translators: %s : link */
					'description' => sprintf( __( '<a href="%s" target="_blank">Click here</a> to go to the offical Ipregistry site for more information, to register, and to generate an API key.', 'gfgeo' ), 'https://ipregistry.co/' ),
					'class'       => 'ip-locator-token-input',
				),
			),
		);

		$settings[] = array(
			'id'          => 'section_gfgeo_google_maps_server_api_key',
			'title'       => esc_html__( 'Google Maps Server API Key', 'gfgeo' ),
			/* Translators: %1$s : link, %2$s : link */
			'description' => sprintf( __( 'This Google Maps Server API key is NOT REQUIRED and the plugin will work without it. This key is necessary only when using the server-side geocoder of the plugin to geocode locations using custom functions. <br /><a href="%1$s" target="_blank">Click here</a> to create your Google Maps Server API key. You can follow steps 9 to 12 of <a href="%2$s" target="_blank">this tutorial</a> to learn how to generate your Google Maps Server API key.', 'gfgeo' ), 'https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key', 'https://docs.geomywp.com/article/141-generate-and-setup-google-maps-api-keys' ),
			'class'       => 'gform-settings-panel--half',
			'fields'      => array(
				array(
					'name' => 'gfgeo_google_maps_server_api_key',
					'type' => 'text',
				),
			),
		);

		$settings[] = array(
			'id'          => 'section_gfgeo_disable_google_maps_api',
			'title'       => esc_html__( 'Disable Google Maps API', 'gfgeo' ),
			'description' => esc_html__( 'Check this checkbox if you\'d like to prevent the Gravity Geolocation plugin from loading the Google Maps API. This feature can be useful when there is another plugin or theme that is also loading the Google Maps API on your site. This way, you can prevent conflicts that usually caused when loading the Google Maps API multiple times.', 'gfgeo' ),
			'class'       => 'gform-settings-panel--half',
			'fields'      => array(
				array(
					'name'         => 'gfgeo_disable_google_maps_api',
					'type'         => 'toggle',
					'toggle_label' => esc_html__( 'Disable Google Maps API', 'gfgeo' ),
				),
			),
		);

		return $settings;
	}

	/**
	 * Geolocation settings tab in Settings page
	 *
	 * @return array
	 */
	public function plugin_settings_fields_2_4() {

		$settings   = array();
		$settings[] = array(
			/* Translators: %1$s : link, %2$s : link */
			'title'  => sprintf( esc_html__( 'License Key %1$s %2$s', 'gfgeo' ), '<a class="gfgeo-docs-link gf_tooltip tooltip" href="https://docs.gravitygeolocation.com/article/206-activating-your-license-key" target="_blank" title="Click to visit the docs page"><span class="dashicons dashicons-media-document"></span>Docs</a>', '<a class="gfgeo-docs-link gf_tooltip tooltip" href="https://gravitygeolocation.com/support" target="_blank" title="Click for support"><span class="dashicons dashicons-sos"></span>Support</a>' ),
			'class'  => 'gfgeo-license-key-settings-section gfgeo-settings-section',
			'id'     => 'gform_setting_gfgeo_license_key',
			'fields' => array(
				array(
					'name'              => 'gfgeo_license_key',
					'id'                => 'gfgeo_license_element_placeholder',
					/* Translators: %1$s : link */
					'tooltip'           => sprintf( esc_html__( 'Enter your Gravity Geolocation license key. A license key is required for the activation of the plugin. An expired license key will work as well, but you will not have access to support and updates. You can retrieve or manage your license key from <a href="%s" target="_blank">your account page</a>.', 'gfgeo' ), 'https://geomywp.com/your-account/license-keys/' ),
					'label'             => esc_html__( 'License Key', 'gfgeo' ),
					'type'              => 'text',
					'class'             => 'large',
					'feedback_callback' => array( $this, 'is_valid_setting' ),
				),
				/*array(
					'name'              => 'gfgeo_plugin_activation_status',
					'label'             => esc_html__( 'Plugin status', 'gfgeo' ),
					'type'              => 'text',
					'class'             => 'large',
					'feedback_callback' => array( $this, 'is_valid_setting' ),
				),
				array(
					'name'              => 'gfgeo_license_key_status',
					'label'             => esc_html__( 'License Key Status', 'gfgeo' ),
					'type'              => 'text',
					'class'             => 'large',
					'feedback_callback' => array( $this, 'is_valid_setting' ),
				),*/
			),
		);

		$settings[] = array(
			/* Translators: %1$s : link, %2$s : link */
			'title'  => sprintf( esc_html__( 'General Settings %1$s %2$s', 'gfgeo' ), '<a class="gfgeo-docs-link gf_tooltip tooltip" href="https://docs.gravitygeolocation.com/article/207-general-settings" target="_blank" title="Click to visit the docs page"><span class="dashicons dashicons-media-document"></span>Docs</a>', '<a class="gfgeo-docs-link gf_tooltip tooltip" href="https://gravitygeolocation.com/support" target="_blank" title="Click for support"><span class="dashicons dashicons-sos"></span>Support</a>' ),
			'class'  => 'gfgeo-general-settings-section gfgeo-settings-section',
			'fields' => array(
				array(
					'name'              => 'gfgeo_google_maps_api_key',
					/* Translators: %1$s : link */
					'tooltip'           => sprintf( esc_html__( 'Enter your Google Map Browser API key. If you don\'t have a Browser API key yet, click <a href="%1$s" target="_blank">here</a> to create one. You can follow <a href="%2$s" target="_blank">this tutorial</a> to learn on how to generate you Google Maps Browser API key.', 'gfgeo' ), 'https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key', 'http://docs.gravitygeolocation.com/article/101-create-google-map-api-key' ),
					'label'             => esc_html__( 'Google Maps Browser API Key', 'gfgeo' ),
					'type'              => 'text',
					'feedback_callback' => array( $this, 'is_valid_setting' ),
					'description'       => esc_html__( 'This Google Maps Browser API key is REQUIRED and the plugin will not work without it.', 'gfgeo' ),
					'size'              => '40',
				),
				array(
					'name'              => 'gfgeo_google_maps_country',
					'tooltip'           => sprintf( esc_html__( 'Enter the country code of the default country to be used with Google Maps API. When geocoding addresses, the plugin will use this country code as the default country with the geocoder. On <a href="%s" target="_blank">this page</a>, you can find the list of country codes that you could use.', 'gfgeo' ), 'https://docs.gravitygeolocation.com/article/209-google-maps-country-codes' ),
					'label'             => esc_html__( 'Google Maps Country Code', 'gfgeo' ),
					'type'              => 'text',
					'size'              => '5',
					'feedback_callback' => array( $this, 'is_valid_setting' ),
				),
				array(
					'name'              => 'gfgeo_google_maps_language',
					'tooltip'           => sprintf( esc_html__( 'Enter the language code of the default language to be used with Google Maps API. This will affect the language of the map and the address autocomplete suggested results. On <a href="%s" target="_blank">this page</a>, you can find the list of language codes that you could use.', 'gfgeo' ), 'https://sites.google.com/site/tomihasa/google-language-codes' ),
					'label'             => esc_html__( 'Google Maps Language', 'gfgeo' ),
					'type'              => 'text',
					'size'              => '5',
					'feedback_callback' => array( $this, 'is_valid_setting' ),
				),
				array(
					'name'    => 'gfgeo_enable_high_accuracy_mode',
					'tooltip' => esc_html__( 'Check this checkbox to enable high accuracy location mode for the auto-locator feature ( when using the auto-locator button and auto-locator on page load ). By doing so, the auto-locator feature (which uses the browser\'s HTML5 geolocation feature ) might retrieve a more accurate user current location. However, it might also result in slower performance of the geolocator.', 'gfgeo' ),
					'label'   => esc_html__( 'High Accuracy Location Mode', 'gfgeo' ),
					'type'    => 'checkbox',
					'choices' => array(
						array(
							'name'   => 'gfgeo_enable_high_accuracy_mode',
							'label'  => esc_html__( 'Enable', 'gfgeo' ),
							'values' => '1',
						),
					),
				),
				array(
					'name'    => 'gfgeo_enable_ip_locator',
					'tooltip' => sprintf( esc_html__( 'Use this option to enable the IP address locator services. You can do so by selecting the IP Address service provider that you would like to use. Once this feature is enabled, you will be able to set it in the Locator Button form field and in the Page Locator option of the Geocoder field in the form editor. You can use it instead of the HTML5 geolocation feature or as a fall-back option when the HTML5 geolocation fails. Please note that the location returned by the IP address, in most cases, is not very accurate and depends solely on the IP Address service provider. Please note that the IP Address service providers might require registration and/or a token/API key.', 'gfgeo' ), 'http://dev.maxmind.com/geoip/geoip2/' ),
					'label'   => esc_html__( 'IP Address Locator', 'gfgeo' ),
					'type'    => 'select',
					'style'   => 'max-width:120px',
					'choices' => array(
						array(
							'label' => esc_html__( 'Disable', 'gfgeo' ),
							'value' => '',
						),
						array(
							'label' => esc_html__( 'ipinfo.io', 'gfgeo' ),
							'value' => 'ipinfo',
						),
						array(
							'label' => esc_html__( 'MaxMind', 'gfgeo' ),
							'value' => 'maxmind',
						),
						array(
							'label' => esc_html__( 'Ipapi.co', 'gfgeo' ),
							'value' => 'ipapico',
						),
						array(
							'label' => esc_html__( 'Ipregistry', 'gfgeo' ),
							'value' => 'ipregistry',
						),
					),
				),
				/**
				Array(
					'name'              => '',
					'label'             => sprintf( esc_html__( 'Please note that MaxMind requires an API in order to use its services, and this plugin requires the Insights plan. You can find more info about it <a href="%s" target="_blank">here</a>. If you do not have an API key you can try using the ipinfo services which offers a free plan and does not require an API key.', 'gfgeo' ), 'https://www.maxmind.com/en/geoip2-precision-services' ),
					'tooltip'           => '',
					'type'              => 'text',
					'size'              => '5',
					'feedback_callback' => array( $this, 'is_valid_setting' ),
					'disabled'          => 'disabled',
				),
				*/
				array(
					'name'              => 'gfgeo_token_ipinfo',
					'label'             => esc_html__( 'ipinfo.io Token', 'gfgeo' ),
					/* Translators: %s : link */
					'tooltip'           => sprintf( esc_html__( 'Enter your ipinfo token, if you have one, in this input box. A token is not required in order to use the free ipinfo service, which provides you with 1000 queries per day. If you need more than a 1000 queries per day, you should look into subscribing to one of the ipinfo premium plans which will provide you with a token. Click <a href="%s" target="_blank">here</a> for more information.', 'gfgeo' ), 'https://ipinfo.io/' ),
					'type'              => 'text',
					'size'              => '40',
					'class'             => 'ip-locator-token-input',
					'feedback_callback' => array( $this, 'is_valid_setting' ),
				),
				array(
					'name'              => 'gfgeo_token_ipregistry',
					'label'             => esc_html__( 'Ipregistry API Key', 'gfgeo' ),
					/* Translators: %s : link */
					'tooltip'           => sprintf( esc_html__( 'Enter your Ipregistry API key. Or Click <a href="%s" target="_blank">here</a> to go to the offical Ipregistry site for more information, to register, and to generate an API key.', 'gfgeo' ), 'https://ipregistry.co/' ),
					'type'              => 'text',
					'size'              => '40',
					'class'             => 'ip-locator-token-input',
					'feedback_callback' => array( $this, 'is_valid_setting' ),
				),
			),
		);

		$settings[] = array(
			/* Translators: %1$s : link, %2$s : link */
			'title'  => sprintf( esc_html__( 'Advanced Settings %1$s %2$s', 'gfgeo' ), '<a class="gfgeo-docs-link gf_tooltip tooltip" href="https://docs.gravitygeolocation.com/article/207-general-settings" target="_blank" title="Click to visit the docs page"><span class="dashicons dashicons-media-document"></span>Docs</a>', '<a class="gfgeo-docs-link gf_tooltip tooltip" href="https://gravitygeolocation.com/support" target="_blank" title="Click for support"><span class="dashicons dashicons-sos"></span>Support</a>' ),
			'class'  => 'gfgeo-advanced-settings-section gfgeo-settings-section',
			'fields' => array(
				array(
					'name'              => 'gfgeo_google_maps_server_api_key',
					/* Translators: %1$s : link */
					'tooltip'           => sprintf( esc_html__( 'Enter your Google Map Server API key. If you don\'t have a Server API key yet, click <a href="%1$s" target="_blank">here</a> to create one. You can follow steps 9 to 12 of <a href="%2$s" target="_blank">this tutorial</a> to learn how to generate your Google Maps Server API key.', 'gfgeo' ), 'https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key', 'https://docs.geomywp.com/article/141-generate-and-setup-google-maps-api-keys' ),
					'label'             => esc_html__( 'Google Maps Server API Key', 'gfgeo' ),
					'type'              => 'text',
					'feedback_callback' => array( $this, 'is_valid_setting' ),
					'description'       => esc_html__( 'This Google Maps Server API key is NOT REQUIRED and the plugin will work without it. This key is necessary only when using the server-side geocoder of the plugin to geocode locations using custom functions.', 'gfgeo' ),
					'size'              => '40',
				),
				array(
					'name'    => 'gfgeo_disable_google_maps_api',
					'tooltip' => esc_html__( 'Check this checkbox if you\'d like to prevent the Gravity Geolocation plugin from loading the Google Maps API. This feature can be useful when there is another plugin or theme that is also loading the Google Maps API on your site. This way, you can prevent conflicts that usually caused when loading the Google Maps API multiple times.', 'gfgeo' ),
					'label'   => esc_html__( 'Disable Google Maps API', 'gfgeo' ),
					'type'    => 'checkbox',
					'choices' => array(
						array(
							'name'   => 'gfgeo_disable_google_maps_api',
							'label'  => esc_html__( 'Disable', 'gfgeo' ),
							'values' => '1',
						),
					),
				),
			),
		);

		return $settings;
	}

	/**
	 * Register Script
	 *
	 * @return [type] [description]
	 */
	public function scripts() {

		$scripts = array();

		// Register Google Maps API.
		if ( ! GFGEO_DISABLE_GOOGLE_MAPS_API && ( ! class_exists( 'GEO_my_WP' ) || ! wp_script_is( 'google-maps', 'registered' ) ) ) {

			$google_url = GFGEO_Helper::get_google_maps_url();

			// Google Maps library.
			if ( ! is_admin() ) {

				// Front-end and only when one of the geolocation fields exist in the form.
				$scripts[] = array(
					'handle'    => 'google-maps',
					'src'       => implode( '', $google_url ),
					'version'   => $this->_version,
					'deps'      => array( 'jquery' ),
					'in_footer' => true,
					'enqueue'   => array(
						array(
							'field_types' => array(
								'gfgeo_address',
								'gfgeo_coordinates',
								'gfgeo_geocoder',
								'gfgeo_directions_panel',
								'gfgeo_locator_button',
							),
						),
					),
				);

				// back-end.
			} else {

				// for the form-editor only.
				$scripts[] = array(
					'handle'    => 'google-maps',
					'src'       => implode( '', $google_url ),
					'version'   => $this->_version,
					'deps'      => array( 'jquery' ),
					'in_footer' => true,
					'enqueue'   => array(
						array(
							'admin_page' => array(
								'form_editor',
							),
						),
					),
				);

				// Edit entry page and only if geolcoation fields exist.
				$scripts[] = array(
					'handle'    => 'google-maps',
					'src'       => implode( '', $google_url ),
					'version'   => $this->_version,
					'deps'      => array( 'jquery' ),
					'in_footer' => true,
					'enqueue'   => array(
						array(
							'query'       => 'page=gf_entries&view=entry',
							'field_types' => array(
								'gfgeo_address',
								'gfgeo_coordinates',
								'gfgeo_geocoder',
								'gfgeo_directions_panel',
								'gfgeo_locator_button',
							),
						),
					),
				);
			}
		}

		$handles = GFGEO_DISABLE_GOOGLE_MAPS_API ? '' : 'google-maps';

		if ( class_exists( 'Google_Maps_Builder' ) ) {
			$handles = 'google-maps-builder-gmaps';
		}

		// Front-end only.
		if ( ! is_admin() ) {

			// Main plugin JS file. Load only if geolcoation fields exist.
			$scripts[] = array(
				'handle'    => 'gfgeo',
				'src'       => GFGEO_URL . '/assets/js/gfgeo.min.js',
				'version'   => $this->_version,
				'deps'      => ! empty( $handles ) ? array( 'jquery', $handles ) : array( 'jquery' ),
				'in_footer' => true,
				'enqueue'   => array(
					array(
						'field_types' => array(
							'gfgeo_address',
							'gfgeo_coordinates',
							'gfgeo_geocoder',
							'gfgeo_directions_panel',
							'gfgeo_locator_button',
						),
					),
				),
			);

			$scripts[] = array(
				'handle'    => 'gfgeo-loader',
				'src'       => GFGEO_URL . '/assets/js/gfgeo.loader.min.js',
				'version'   => $this->_version,
				'deps'      => array( 'gfgeo' ),
				'in_footer' => true,
				'enqueue'   => array(
					array(
						'field_types' => array(
							'gfgeo_address',
							'gfgeo_coordinates',
							'gfgeo_geocoder',
							'gfgeo_directions_panel',
							'gfgeo_locator_button',
						),
					),
				),
			);

			// Maxmind. Load only if geolcoation fields exist.
			if ( 'maxmind' == GFGEO_IP_LOCATOR ) {

				$scripts[] = array(
					'handle'    => 'gfgeo-maxmind',
					'src'       => '//geoip-js.com/js/apis/geoip2/v2.1/geoip2.js',
					'version'   => $this->_version,
					'deps'      => array( 'jquery' ),
					'in_footer' => true,
					'enqueue'   => array(
						array(
							'field_types' => array(
								'gfgeo_address',
								'gfgeo_coordinates',
								'gfgeo_geocoder',
								'gfgeo_directions_panel',
								'gfgeo_locator_button',
							),
						),
					),
				);
			}
		} else {

			// Edit entry page script. Load in Edit Entry page and only if geolcoation fields exist.
			$scripts[] = array(
				'handle'    => 'gfgeo',
				'src'       => GFGEO_URL . '/assets/js/gfgeo.min.js',
				'version'   => $this->_version,
				'deps'      => ! empty( $handles ) ? array( 'jquery', $handles ) : array( 'jquery' ),
				'in_footer' => true,
				'enqueue'   => array(
					array(
						'query'       => 'page=gf_entries&view=entry',
						'field_types' => array(
							'gfgeo_address',
							'gfgeo_coordinates',
							'gfgeo_geocoder',
							'gfgeo_directions_panel',
							'gfgeo_locator_button',
						),
					),
				),
			);

			$scripts[] = array(
				'handle'    => 'gfgeo-loader',
				'src'       => GFGEO_URL . '/assets/js/gfgeo.loader.min.js',
				'version'   => $this->_version,
				'deps'      => array( 'gfgeo' ),
				'in_footer' => true,
				'enqueue'   => array(
					array(
						'query'       => 'page=gf_entries&view=entry',
						'field_types' => array(
							'gfgeo_address',
							'gfgeo_coordinates',
							'gfgeo_geocoder',
							'gfgeo_directions_panel',
							'gfgeo_locator_button',
						),
					),
				),
			);

			// Form editor script. Load only on the Form Builder page.
			$scripts[] = array(
				'handle'    => 'gfgeo-form-editor',
				'src'       => GFGEO_GF_2_5 ? GFGEO_URL . '/assets/js/gfgeo.form.editor.min.js' : GFGEO_URL . '/assets/js/gfgeo.form.editor.2-4.min.js',
				'version'   => $this->_version,
				'deps'      => GFGEO_DISABLE_GOOGLE_MAPS_API ? array( 'jquery' ) : array( 'jquery', 'google-maps' ),
				'in_footer' => true,
				'enqueue'   => array(
					array(
						'admin_page' => array(
							'form_editor',
						),
					),
				),
			);
		}

		return array_merge( parent::scripts(), $scripts );
	}

	/**
	 * Register styles.
	 *
	 * @return [type] [description]
	 */
	public function styles() {

		$styles = array(
			// Form Editor page.
			array(
				'handle'  => 'gfgeo',
				'src'     => GFGEO_URL . '/assets/css/gfgeo.min.css',
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'admin_page' => array(
							'form_editor',
							'plugin_settings',
						),
					),
				),
			),
			// Edit entry page.
			array(
				'handle'  => 'gfgeo',
				'src'     => GFGEO_URL . '/assets/css/gfgeo.min.css',
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'query'       => 'page=gf_entries&view=entry',
						'field_types' => array(
							'gfgeo_address',
							'gfgeo_coordinates',
							'gfgeo_geocoder',
							'gfgeo_directions_panel',
							'gfgeo_locator_button',
						),
					),
				),
			),
		);

		if ( ! is_admin() ) {

			// Front-end.
			$styles[] = array(
				'handle'  => 'gfgeo',
				'src'     => GFGEO_URL . '/assets/css/gfgeo.min.css',
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'field_types' => array(
							'gfgeo_address',
							'gfgeo_coordinates',
							'gfgeo_geocoder',
							'gfgeo_directions_panel',
							'gfgeo_locator_button',
						),
					),
				),
			);
		}

		return array_merge( parent::styles(), $styles );
	}

	/**
	 * Verify license.
	 *
	 * @return [type] [description]
	 */
	public static function verify_license() {

		$license_data = get_option( 'gfgeo_license' );

		if ( ! empty( $license_data['status'] ) && ( 'valid' === $license_data['status'] || 'expired' === $license_data['status'] ) ) {
			return true;
		}

		return false;
	}
}
