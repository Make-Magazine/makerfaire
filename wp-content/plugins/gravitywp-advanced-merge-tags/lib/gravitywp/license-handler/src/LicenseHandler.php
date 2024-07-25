<?php
/**
 * GravityWP License handler.
 *
 * @package gravitywp-license-handler
 * @license MIT
 *
 *
 * Modified by __root__ on 17-July-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityWP\Advanced_Merge_Tags\GravityWP\LicenseHandler;

use GFCommon;
use GravityWP\Advanced_Merge_Tags\GravityWP\Updater\Plugin_Updater;

defined( 'ABSPATH' ) || die();

/**
 * Handles GWP Licenses.
 *
 * @version 2.0.3
 */
class LicenseHandler {
	/**
	 * Update endpoint of the API
	 *
	 * @var $result_count
	 */
	private $api_url = '';

	/**
	 * HTTP parameters on API requests
	 *
	 * @var $api_data
	 */
	private $api_data = array();

	/**
	 * Plugin Name
	 *
	 * @var $name
	 */
	private $name = '';

	/**
	 * Whether update from the beta version or not
	 *
	 * @var $slug
	 */
	private $beta;

	/**
	 * Current version of plugin
	 *
	 * @var mixed|string
	 */
	private $version = '';

	/**
	 * WP Override flag
	 *
	 * @var $wp_override
	 */
	private $wp_override = false;

	/**
	 * Cache key
	 *
	 * @var string
	 */
	private $cache_key = '';

	/**
	 * Product slug
	 *
	 * @var $download_tag
	 */
	private $_download_tag = '';

	/**
	 * Health check timeout
	 *
	 * @var $health_check_timeout
	 */
	private $health_check_timeout = 5;

	/**
	 * Store the initialized paddlepress_pro client
	 *
	 * @since  1.0
	 * @access private
	 * @var    Object $_paddlepress_client The paddlepress_pro client instance.
	 */
	private $_paddlepress_client = null;

	/**
	 * Store the initialized paddlepress_pro license handler
	 *
	 * @since  1.0
	 * @access private
	 * @var    Object $_paddlepress_client The paddlepress_pro client instance.
	 */
	private $_license_handler = null;

	/**
	 * Store the GravityWP GF Addon classname
	 *
	 * @since  1.0
	 * @access private
	 * @var    string $_addon_class the GravityWP GF Addon classname.
	 */
	private $_addon_class = '';

	/**
	 * Store the GravityWP GF Addon slug
	 *
	 * @since  1.0
	 * @access private
	 * @var    string $_addon_slug the GravityWP GF Addon slug.
	 */
	private $_addon_slug = '';

	/**
	 * Store the GravityWP GF Addon license
	 *
	 * @since  1.0
	 * @access private
	 * @var    string $_addon_license the GravityWP GF Addon license.
	 */
	private $_addon_license = '';

	/**
	 * Store the GravityWP GF Addon license hash.
	 *
	 * @since  1.0
	 * @access private
	 * @var    string $_addon_license_hash the GravityWP GF Addon license hash.
	 */
	private $_addon_license_hash = '';

	/**
	 * Store the GravityWP GF Addon title
	 *
	 * @since  1.0
	 * @access private
	 * @var    string $_addon_title the GravityWP GF Addon title.
	 */
	private $_addon_title = '';

	/**
	 * Store the GravityWP GF Addon path
	 *
	 * @since  1.0
	 * @access private
	 * @var    string $_addon_file_path The GravityWP GF Addon path.
	 */
	private $_addon_file_path = '';

	/**
	 * Store the GravityWP constants
	 *
	 * @since  1.0
	 * @access protected
	 * @var    array<mixed>|string|false|null $_gwp_get_defined_constants The GravityWP constants.
	 */
	protected $_gwp_get_defined_constants;

	/**
	 * Constructor.
	 *
	 * @since  1.0
	 *
	 * @param string $gwp_addon_class GravityWP GF Addon classname.
	 * @param string $plugin_file_path Path to main plugin file.
	 *
	 * @return void
	 */
	public function __construct( $gwp_addon_class, $plugin_file_path ) {
		$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
		if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
			return;
		}
		$this->_addon_class     = $gwp_addon_class;
		$this->_addon_file_path = $plugin_file_path;
		$this->_addon_slug      = $gwp_addon_class::get_instance()->get_slug();
		$this->_addon_license   = $gwp_addon_class::get_instance()->get_plugin_setting( $this->_addon_slug . '_license_key' );
		$this->_addon_title     = $gwp_addon_class::get_instance()->plugin_page_title();
		$this->initialize_paddlepress_client();
	}

	/**
	 * Initialize or reinitialize the Paddlepress client.
	 *
	 * @return bool
	 */
	public function initialize_paddlepress_client( $field_setting = null ) {
		try {
			unset( $this->_paddlepress_client );
			unset( $this->_license_handler );
			$license_key            = ! empty( $field_setting ) ? $field_setting : $this->_addon_license;
			$this->_license_handler = new Plugin_Updater(
				$this->_addon_file_path,
				array(
					'version'      => $this->_addon_class::get_instance()->get_version(), // current version number.
					'license_key'  => $license_key,                 // license key (used get_option above to retrieve from DB)..'error'
					'license_url'  => home_url(),                   // license domain.
					'download_tag' => $this->_addon_slug, // download tag slug.
					'beta'         => false,
				)
			);

			$use_cached_info = ! empty( $field_setting ) ? false : true;
			if ( $this->_license_handler->gwp_is_valid( $use_cached_info, $license_key ) ) {
				remove_action( 'admin_notices', array( $this, 'action_admin_notices' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'action_admin_notices' ) );
			}
		} catch ( \Exception $e ) {
			$this->_addon_class::get_instance()->log_error( __CLASS__ . '::' . __METHOD__ . '(): License client failed to initialize: ' . $e->getMessage() );
			return false;
		}

		return true;
	}

	/**
	 * Display an admin notice.
	 *
	 * @since  1.0
	 *
	 * @return void
	 */
	public function action_admin_notices() {
		$site_slug           = $this->_addon_class::get_instance()->gwp_site_slug;
		$primary_button_link = admin_url( 'admin.php?page=gf_settings&subview=' . $this->_addon_slug );
		if ( ! empty( $site_slug ) ) {
			$url = "https://gravitywp.com/add-on/{$site_slug}/?utm_source=admin_notice&utm_medium=admin&utm_content=inactive&utm_campaign=Admin%20Notice";
		} else {
			$url = 'https://gravitywp.com/add-ons/?utm_source=admin_notice&utm_medium=admin&utm_content=inactive&utm_campaign=Admin%20Notice';
		}

		/* translators: button tags */
		$message = esc_html__( 'Your %1$s license has not been activated. This means you are missing out on security fixes, updates and support.%2$sActivate your license%3$s or %4$sget a license here%5$s', 'gravitywp-license-handler' );
		$message = sprintf( $message, $this->_addon_title, '<br /><br /><a href="' . esc_url( $primary_button_link ) . '" class="button button-primary">', '</a>', '<a href="' . esc_url( $url ) . '" class="button button-secondary">', '</a>' );

		$key = $this->_addon_slug . '_license_message_notice';

		GFCommon::add_dismissible_message( $message, $key, 'warning', false, true );
	}

	/**
	 * Define plugin settings fields.
	 *
	 * @since  1.0
	 *
	 * @return array
	 */
	public function plugin_settings_license_fields() {
		$this->_addon_license = $this->_addon_class::get_instance()->get_plugin_setting( $this->_addon_slug . '_license_key' );

		$license_field = array(
			'name'                => $this->_addon_slug . '_license_key',
			'tooltip'             => esc_html__( 'Enter the license key you received after purchasing the plugin.', 'gravitywp-license-handler' ),
			'label'               => esc_html__( 'License Key', 'gravitywp-license-handler' ),
			'type'                => 'text',
			'input_type'          => 'password',
			'class'               => 'medium',
			'default_value'       => '',
			'required'            => true,
			'validation_callback' => array( $this, 'license_validation' ),
			'feedback_callback'   => array( $this, 'license_feedback' ),
			'error_message'       => esc_html__( 'Invalid or expired license', 'gravitywp-license-handler' ),
		);

		$license_field['title']  = esc_html__( 'To unlock plugin updates and support, please enter your license key below', 'gravitywp-license-handler' );
		$license_field['fields'] = array( $license_field );

		return $license_field;
	}

	/**
	 * Handle license key activation or deactivation and on save the settings.
	 *
	 * @since  1.0
	 *
	 * @param array  $field The field properties.
	 * @param string $field_setting The submitted value of the license_key field.
	 */
	public function license_validation( $field, $field_setting ) {
		if ( empty( $field_setting ) ) {
			return;
		}
		if ( $this->_license_handler->request_is_activate( $field_setting ) ) {
			GFCommon::remove_dismissible_message( $this->_addon_slug . '_license_message_notice' );
		} else {
			$message = 'Failed to activate this license key.';
			if ( ! empty( $this->_license_handler->error_messages ) ) {
				$message = $this->_license_handler->error_messages;
			}
			$this->_addon_class::get_instance()->set_field_error( $field, $message );
			$this->action_admin_notices();
		}
	}

	/**
	 * Determine if the license key is valid so the appropriate icon can be displayed next to the field.
	 *
	 * @param string $value The current value of the license_key field.
	 * @param array  $field The field properties.
	 *
	 * @since  1.0
	 *
	 * @return bool|null
	 */
	public function license_feedback( $value, $field ) {
		if ( empty( $value ) ) {
			return null;
		}
		if ( $this->_license_handler->request_is_activate( $value ) ) {
			GFCommon::remove_dismissible_message( $this->_addon_slug . '_license_message_notice' );
			return true;
		} else {
			$message = $this->_license_handler->error_messages;
			if ( ! empty( $this->_license_handler->error_messages ) ) {
				$message = $this->_license_handler->error_messages;
			}
			$this->_addon_class::get_instance()->set_field_error( $field, $message );
			$this->action_admin_notices();
			return false;
		}
	}
}
