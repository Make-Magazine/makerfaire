<?php
/**
 * Custom Plugin Updater
 *
 * @package gravitywp-license-handler
 *
 * Modified by __root__ on 26-July-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityWP\Advanced_Merge_Tags\GravityWP\Updater;

use stdClass;

// phpcs:disable WordPress.Security.NonceVerification.Recommended

/**
 * Allows plugins to use their own update API.
 * ported from EDD's updater
 *
 * @class Plugin_Updater
 */
class Plugin_Updater {

	/**
	 * Update endpoint of the API
	 *
	 * @var $result_count
	 */
	private $api_url = '';

	/**
	 * Update endpoint of the API
	 *
	 * @var $result_count
	 */
	private $api_url_update = 'https://my.gravitywp.com/wp-json/paddlepress-api/v1/update';

	/**
	 * GravityWP license url endpoint of the API
	 *
	 * @var $result_count
	 */
	private $api_url_license = 'https://my.gravitywp.com/wp-json/paddlepress-api/v1/license';

	/**
	 * HTTP parameters on API requests
	 *
	 * @var $api_data
	 */
	private $api_data = array();

	/**
	 * Plugin Name.
	 *
	 * @var $name
	 */
	private $name = '';

	/**
	 * Plugin slug.
	 *
	 * @var $slug
	 */
	private $slug = '';

	/**
	 * Whether update from the beta version or not
	 *
	 * @var $beta
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
	private $download_tag = '';

	/**
	 * Error messages
	 *
	 * @var array<mixed>|string|false|null Response of last request
	 */

	public $error_messages;

	/**
	 * Store the Handler class
	 *
	 * @since  2.0.4
	 * @access private
	 * @var    string $_addon_class the GravityWP GF Addon classname.
	 */
	private $handler_class;

	/**
	 * Health check timeout
	 *
	 * @var $health_check_timeout
	 */
	private $health_check_timeout = 5;

	/**
	 * Class constructor.
	 *
	 * @param string $_plugin_file Path to the plugin file.
	 * @param array  $_api_data    Optional data to send with API calls.
	 *
	 * @uses hook()
	 *
	 * @uses plugin_basename()
	 */
	public function __construct( $_plugin_file, $_api_data = null ) {

		global $paddlepress_plugin_data;

		$this->api_url                          = trailingslashit( $this->api_url_update );
		$this->api_url_license                  = $this->api_url_license;
		$this->api_data                         = $_api_data;
		$this->name                             = plugin_basename( $_plugin_file );
		$this->version                          = $_api_data['version'];
		$this->download_tag                     = $_api_data['download_tag'];
		$this->slug                             = $_api_data['download_tag']; // the slug of download term
		$this->wp_override                      = isset( $_api_data['wp_override'] ) ? (bool) $_api_data['wp_override'] : false;
		$this->beta                             = ! empty( $this->api_data['beta'] ) ? true : false;
		$this->cache_key                        = 'paddlepress_' . md5( serialize( $this->slug . $this->api_data['license_key'] . $this->beta ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
		$this->handler_class                    = $_api_data['handler_class'];
		$paddlepress_plugin_data[ $this->slug ] = $this->api_data;

		/**
		 * Fires after the $paddlepress_plugin_data is setup.
		 *
		 * @param array $paddlepress_plugin_data Array of plugin data.
		 *
		 * @since 1.0
		 */
		do_action( 'post_paddlepress_plugin_updater_setup', $paddlepress_plugin_data );

		// Set up hooks.
		$this->init();
	}

	/**
	 * Set up WordPress filters to hook into WP's update process.
	 *
	 * @return void
	 * @uses add_filter()
	 */
	public function init() {
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update_license' ) );
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
		add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
		remove_action( 'after_plugin_row_' . $this->name, 'wp_plugin_update_row', 10 );
		add_action( 'after_plugin_row_' . $this->name, array( $this, 'show_update_notification' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'show_changelog' ) );
	}

	/**
	 * Send a request to activate a license key.
	 *
	 * This function sends a POST request to the license API to activate a given license key.
	 * It verifies the SSL status, constructs the request arguments, and processes the response.
	 * If the request is successful and the license is activated, it returns true. Otherwise,
	 * it sets error messages and returns false.
	 *
	 * @param string $field_setting The license key to be activated.
	 *
	 * @return bool True if the license is successfully activated, false otherwise.
	 */
	public function request_is_activate( $field_setting ) {
		$verify_ssl = $this->verify_ssl();
		if ( empty( $field_setting ) ) {
			return false;
		}

		$args = array(
			'body'      => array(
				'license_key'  => $field_setting,
				'license_url'  => home_url(),
				'action'       => 'activate',
				'download_tag' => $this->download_tag,
			),
			'timeout'   => '15',
			'sslverify' => $verify_ssl,
		);

		// Sending the POST request.
		$response = wp_remote_post( $this->api_url_license, $args );
		if ( is_wp_error( $response ) ) {
			$error_message        = $response->get_error_message();
			$this->error_messages = "Something went wrong: $error_message";
		} else {
			$body      = wp_remote_retrieve_body( $response );
			$json_data = json_decode( $body, true );
			if ( isset( $json_data['success'] ) && $json_data['success'] === true && isset( $json_data['license_status'] ) && $json_data['license_status'] === 'valid' ) {
				return true;
			} elseif ( isset( $json_data['errors'] ) ) {
				if ( count( $json_data['errors'] ) === 1 && ! empty( $json_data['errors']['unregistered_license_domain'] ) ) {
					return true;
				}
				$this->error_messages = nl2br( $this->generateErrorMessage( $json_data['errors'] ) );
				return false;
			} elseif ( isset( $json_data['message'] ) ) {
				$this->error_messages = nl2br( $this->generateErrorMessage( $json_data['message'] ) );
				return false;
			} else {
				$this->error_messages = nl2br( $this->generateErrorMessage( 'Please try again later. If the issue persists, please contact support.' ) );
				return false;
			}
		}
	}

	/**
	 * Generate a formatted error message from an array of error messages.
	 *
	 * This function takes an array of error messages, sanitizes each message,
	 * and formats them into a single string prefixed with "Something went wrong:".
	 * Each error message is listed on a new line and is sanitized to ensure
	 * no harmful content is included.
	 *
	 * @param array $errorArray An associative array of error messages.
	 *                          The keys are error identifiers, and the values
	 *                          are arrays of error messages associated with each key.
	 *
	 * @return string A formatted error message string.
	 */
	public function generateErrorMessage( $error ) {
		// Initialize the error message string with a prefix.
		$error_message = "Something went wrong:\n";
		if ( is_array( $error ) ) {
			// Loop through each entry in the error array.
			foreach ( $error as $key => $messages ) {
				if ( is_array( $messages ) ) {
					// Loop through each message in the current entry.
					foreach ( $messages as $message ) {
						// Sanitize the error message to remove any harmful content.
						$sanitized_message = sanitize_text_field( $message );
						// Append the sanitized message to the error message string.
						$error_message .= "- $sanitized_message\n";
					}
				} else {
					$sanitized_message = sanitize_text_field( $messages );
					$error_message     .= "- $sanitized_message\n";
				}
			}
		} else {
			$error_message .= $error;
		}

		// Return the formatted error message string.
		return $error_message;
	}

	/**
	 * Validates the plugin's license key by checking against the API endpoint.
	 *
	 * This method ensures that the plugin's license key is valid by either retrieving
	 * validation status from a locally stored cache or making an API request to verify the status.
	 * If the cached status has expired or is unavailable, it will make an API call using the
	 * 'request_is_activate' function to fetch the current status and update the cache accordingly.
	 * The check includes parameters such as the license key, which can be passed directly or
	 * fetched from the stored API data within the class.
	 *
	 * The method returns true if the license key is validated successfully; otherwise, it returns false.
	 *
	 * @param bool        $cached Unused parameter but kept for maintaining signature consistency.
	 * @param string|null $key Optional. License key to be used instead of the one stored in the object.
	 *
	 * @return bool True if the license key is valid, false otherwise.
	 *
	 * @uses request_is_activate() Makes an API request to validate the license key.
	 * @uses set_version_info_cache() Caches the result of the license key validation.
	 */
	public function gwp_is_valid( $cached, $key = null ) {

		$license_key = ! empty( $key ) ? $key : $this->api_data['license_key'];
		if ( empty( $license_key ) ) {
			return false;
		}

		$status_cache_key = 'paddlepress_status_request_' . md5( serialize( $this->slug . $this->api_data['license_key'] . $this->beta ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize

		$cache = get_option( $status_cache_key );

		$cache_status = true;
		if ( empty( $cache ) || ! isset( $cache['timeout'] ) || empty( $cache['timeout'] ) || time() > $cache['timeout'] ) {
			$cache_status = false; // Cache is expired.
		}
		if ( $cache_status === true ) {
			if ( $cache['value'] === 'true' ) {
				return true;
			}
		} else {
			$license_key = ! empty( $key ) ? $key : $this->api_data['license_key'];
			$status      = $this->request_is_activate( $license_key );
			$this->set_version_info_cache( $status, $status_cache_key );
			if ( $status === true ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks the plugin's license status and updates the transient data accordingly.
	 *
	 * This method verifies the validity of the plugin's license key and updates the cached 
	 * license status. It also manages admin notices based on the validation result. If the 
	 * license key is valid, it removes any existing admin notices. Otherwise, it adds an 
	 * admin notice to inform the user about the invalid license status.
	 *
	 * Key Details:
	 * - Verifies if the current context is not the plugins page in a multisite network, 
	 *   and returns early if true.
	 * - Checks existing transient data for license status response, and skips further checks
	 *   if the data is already populated unless overridden.
	 * - Retrieves the license key and generates a unique cache key for the status request.
	 * - Calls the API to check the license activation status.
	 * - Updates the license status cache with the API response.
	 * - Based on the license validation result, either removes or adds admin notices.
	 *
	 * @param stdClass $_transient_data Transient data object containing update information.
	 *                                  If not provided as an object, it initializes a new stdClass object.
	 *
	 * @global string $pagenow Current admin page identifier.
	 *
	 * @return stdClass The (possibly modified) transient data object.
	 *
	 * @uses request_is_activate() Makes an API request to validate the license key.
	 * @uses set_version_info_cache() Caches the result of the license key validation.
	 * @uses gwp_is_valid() Validates the license key using cached data or via API.
	 */
	public function check_update_license($_transient_data) {
		global $pagenow;

		// Ensure $_transient_data is an object.
		if (!is_object($_transient_data)) {
			$_transient_data = new stdClass();
		}

		// Return early if on the plugins page in a multisite network.
		if ('plugins.php' === $pagenow && is_multisite()) {
			return $_transient_data;
		}

		// Check if the transient data already has a response and is not being overridden.
		if (!empty($_transient_data->response) && !empty($_transient_data->response[$this->name]) && false === $this->wp_override) {
			return $_transient_data;
		}

		// Retrieve license key and generate a unique cache key.
		$license_key      = $this->api_data['license_key'];
		$status_cache_key = 'paddlepress_status_request_' . md5(serialize($this->slug . $this->api_data['license_key'] . $this->beta)); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize

		// Validate the license key through the API and update the cache.
		$status = $this->request_is_activate($license_key);
		$this->set_version_info_cache($status, $status_cache_key);

		// Validate the license key and manage admin notices based on validity.
		if ($this->gwp_is_valid(false, $license_key)) {
			remove_action('admin_notices', array($this->handler_class, 'action_admin_notices'));
		} else {
			add_action('admin_notices', array($this->handler_class, 'action_admin_notices'));
		}

		return $_transient_data;
	}



	/**
	 * Check for Updates at the defined API endpoint and modify the update array.
	 *
	 * This function dives into the update API just when WordPress creates its update array,
	 * then adds a custom API call and injects the custom plugin data retrieved from the API.
	 * It is reassembled from parts of the native WordPress plugin update code.
	 * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
	 *
	 * @param array $_transient_data Update array build by WordPress.
	 *
	 * @return array Modified update array with custom plugin data.
	 * @uses api_request()
	 */
	public function check_update( $_transient_data ) {

		global $pagenow;

		if ( ! is_object( $_transient_data ) ) {
			$_transient_data = new stdClass();
		}

		if ( 'plugins.php' === $pagenow && is_multisite() ) {
			return $_transient_data;
		}

		if ( ! empty( $_transient_data->response ) && ! empty( $_transient_data->response[ $this->name ] ) && false === $this->wp_override ) {
			return $_transient_data;
		}

		$version_info = false; // $this->get_cached_version_info();

		if ( false === $version_info ) {
			$version_info = $this->api_request(
				'get_version',
				array(
					'download_tag' => $this->download_tag,
					'beta'         => $this->beta,
					'license_url'  => $this->api_data['license_url'],
				)
			);

			// Since we disabled our filter for the transient, we aren't running our object conversion on banners, sections, or icons. Do this now:
			if ( isset( $version_info->banners ) && ! is_array( $version_info->banners ) ) {
				$version_info->banners = $this->convert_object_to_array( $version_info->banners );
			}

			if ( isset( $version_info->sections ) && ! is_array( $version_info->sections ) ) {
				$version_info->sections = $this->convert_object_to_array( $version_info->sections );
			}

			if ( isset( $version_info->icons ) && ! is_array( $version_info->icons ) ) {
				$version_info->icons = $this->convert_object_to_array( $version_info->icons );
			}

			if ( isset( $version_info->contributors ) && ! is_array( $version_info->contributors ) ) {
				$version_info->contributors = $this->convert_object_to_array( $version_info->contributors );
			}

			$this->set_version_info_cache( $version_info );
		}

		if ( false !== $version_info && is_object( $version_info ) && isset( $version_info->new_version ) ) {

			$no_update = false;
			if ( version_compare( $this->version, $version_info->new_version, '<' ) ) {

				$_transient_data->response[ $this->name ] = $version_info;

				// Make sure the plugin property is set to the plugin's name/location.
				$_transient_data->response[ $this->name ]->plugin = $this->name;
			} else {
				$no_update              = new stdClass();
				$no_update->id          = '';
				$no_update->slug        = $this->slug;
				$no_update->plugin      = $this->name;
				$no_update->new_version = $version_info->new_version;
				$no_update->icons       = $version_info->icons;
				$no_update->banners     = $version_info->banners;
				$no_update->banners_rtl = array();

				if ( isset( $version_info->homepage ) ) {
					$no_update->url = $version_info->homepage;
				}

				if ( isset( $version_info->package ) ) {
					$no_update->package = $version_info->package;
				}
			}

			$_transient_data->last_checked           = time();
			$_transient_data->checked[ $this->name ] = $this->version;

			if ( $no_update ) {
				$_transient_data->no_update[ $this->name ] = $no_update;
			}
		}

		return $_transient_data;
	}

	/**
	 * show update nofication row -- needed for multisite subsites, because WP won't tell you otherwise!
	 *
	 * @param string $file   Path to the plugin file relative to the plugins directory.
	 * @param array  $plugin An array of plugin data.
	 */
	public function show_update_notification( $file, $plugin ) {

		if ( is_network_admin() ) {
			return;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		if ( ! is_multisite() ) {
			return;
		}

		if ( $this->name !== $file ) {
			return;
		}

		// Remove our filter on the site transient
		remove_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ), 10 );

		$update_cache = get_site_transient( 'update_plugins' );

		$update_cache = is_object( $update_cache ) ? $update_cache : new stdClass();

		if ( empty( $update_cache->response ) || empty( $update_cache->response[ $this->name ] ) ) {

			$version_info = $this->get_cached_version_info(); //  || false;

			if ( false === $version_info ) {
				$version_info = $this->api_request(
					'get_version',
					array(
						'download_tag' => $this->download_tag,
						'beta'         => $this->beta,
						'license_url'  => $this->api_data['license_url'],
					)
				);

				// Since we disabled our filter for the transient, we aren't running our object conversion on banners, sections, or icons. Do this now:
				if ( isset( $version_info->banners ) && ! is_array( $version_info->banners ) ) {
					$version_info->banners = $this->convert_object_to_array( $version_info->banners );
				}

				if ( isset( $version_info->sections ) && ! is_array( $version_info->sections ) ) {
					$version_info->sections = $this->convert_object_to_array( $version_info->sections );
				}

				if ( isset( $version_info->icons ) && ! is_array( $version_info->icons ) ) {
					$version_info->icons = $this->convert_object_to_array( $version_info->icons );
				}

				if ( isset( $version_info->contributors ) && ! is_array( $version_info->contributors ) ) {
					$version_info->contributors = $this->convert_object_to_array( $version_info->contributors );
				}

				$this->set_version_info_cache( $version_info );
			}

			if ( ! is_object( $version_info ) ) {
				return;
			}

			$no_update = false;
			if ( version_compare( $this->version, $version_info->new_version, '<' ) ) {

				$update_cache->response[ $this->name ] = $version_info;

			} else {
				$no_update              = new stdClass();
				$no_update->id          = '';
				$no_update->slug        = $this->slug;
				$no_update->plugin      = $this->name;
				$no_update->new_version = $version_info->new_version;
				$no_update->url         = $version_info->homepage;
				$no_update->package     = $version_info->package;
				$no_update->icons       = $version_info->icons;
				$no_update->banners     = $version_info->banners;
				$no_update->banners_rtl = array();
			}

			$update_cache->last_checked           = time();
			$update_cache->checked[ $this->name ] = $this->version;
			if ( $no_update ) {
				$update_cache->no_update[ $this->name ] = $no_update;
			}

			set_site_transient( 'update_plugins', $update_cache );

		} else {
			$version_info = $update_cache->response[ $this->name ];
		}

		// Restore our filter
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );

		if ( ! empty( $update_cache->response[ $this->name ] ) && version_compare( $this->version, $version_info->new_version, '<' ) ) {

			// build a plugin list row, with update notification
			$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
			echo '<tr class="plugin-update-tr" id="' . esc_attr( $this->slug ) . '-update" data-slug="' . esc_attr( $this->slug ) . '" data-plugin="' . esc_attr( $this->slug ) . '/' . esc_attr( $file ) . '">';
			echo '<td colspan="3" class="plugin-update colspanchange">';
			echo '<div class="update-message notice inline notice-warning notice-alt">';

			$changelog_link = self_admin_url( 'index.php?paddlepress_action=view_plugin_changelog&plugin=' . $this->name . '&slug=' . $this->slug . '&TB_iframe=true&width=772&height=911' );

			if ( empty( $version_info->download_link ) ) {
				printf(
				/* translators: %1$s: Plugin name. */
					esc_html__( 'There is a new version of %1$s available. %2$sView version %3$s details%4$s.', 'paddlepress' ),
					esc_html( $version_info->name ),
					'<a target="_blank" class="thickbox" href="' . esc_url( $changelog_link ) . '">',
					esc_html( $version_info->new_version ),
					'</a>'
				);
			} else {
				printf(
				/* translators: %1$s: Plugin name. */
					esc_html__( 'There is a new version of %1$s available. %2$sView version %3$s details%4$s or %5$supdate now%6$s.', 'paddlepress' ),
					esc_html( $version_info->name ),
					'<a target="_blank" class="thickbox" href="' . esc_url( $changelog_link ) . '">',
					esc_html( $version_info->new_version ),
					'</a>',
					'<a href="' . esc_url( wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $this->name, 'upgrade-plugin_' . $this->name ) ) . '">',
					'</a>'
				);
			}

			do_action( "in_plugin_update_message-{$file}", $plugin, $version_info ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

			echo '</div></td></tr>';
		}
	}

	/**
	 * Updates information on the "View version x.x details" page with custom data.
	 *
	 * @param mixed  $_data   The result object or array. Default false.
	 * @param string $_action The type of information being requested from the Plugin Installation API.
	 * @param object $_args   Plugin API arguments.
	 *
	 * @return object $_data
	 * @uses api_request()
	 */
	public function plugins_api_filter( $_data, $_action = '', $_args = null ) {

		if ( 'plugin_information' !== $_action ) {

			return $_data;
		}

		if ( ! isset( $_args->slug ) || ( $_args->slug !== $this->slug ) ) {

			return $_data;

		}

		$to_send = array(
			'download_tag' => $this->download_tag,
			'slug'         => $this->slug,
			'is_ssl'       => is_ssl(),
			'fields'       => array(
				'banners' => array(),
				'reviews' => false,
				'icons'   => array(),
			),
		);

		$cache_key = 'paddlepress_api_request_' . md5( serialize( $this->slug . $this->api_data['license_key'] . $this->beta ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize

		// Get the transient where we store the api request for this plugin for 24 hours
		$paddlepress_api_request_transient = $this->get_cached_version_info( $cache_key );

		// If we have no transient-saved value, run the API, set a fresh transient with the API value, and return that value too right now.
		if ( empty( $paddlepress_api_request_transient ) ) {

			$api_response = $this->api_request( 'plugin_information', $to_send );

			// Expires in 3 hours
			$this->set_version_info_cache( $api_response, $cache_key );

			if ( false !== $api_response ) {
				$_data = $api_response;
			}
		} else {
			$_data = $paddlepress_api_request_transient;
		}

		// Convert sections into an associative array, since we're getting an object, but Core expects an array.
		if ( isset( $_data->sections ) && ! is_array( $_data->sections ) ) {
			$_data->sections = $this->convert_object_to_array( $_data->sections );
		}

		// Convert banners into an associative array, since we're getting an object, but Core expects an array.
		if ( isset( $_data->banners ) && ! is_array( $_data->banners ) ) {
			$_data->banners = $this->convert_object_to_array( $_data->banners );
		}

		// Convert icons into an associative array, since we're getting an object, but Core expects an array.
		if ( isset( $_data->icons ) && ! is_array( $_data->icons ) ) {
			$_data->icons = $this->convert_object_to_array( $_data->icons );
		}

		// Convert contributors into an associative array, since we're getting an object, but Core expects an array.
		if ( isset( $_data->contributors ) && ! is_array( $_data->contributors ) ) {
			$_data->contributors = $this->convert_object_to_array( $_data->contributors );
		}

		if ( ! isset( $_data->plugin ) ) {
			$_data->plugin = $this->name;
		}

		return $_data;
	}

	/**
	 * Convert some objects to arrays when injecting data into the update API
	 *
	 * Some data like sections, banners, and icons are expected to be an associative array, however due to the JSON
	 * decoding, they are objects. This method allows us to pass in the object and return an associative array.
	 *
	 * @param stdClass $data plugin api sections
	 *
	 * @return array
	 * @since 1.0
	 */
	private function convert_object_to_array( $data ) {
		$new_data = array();
		foreach ( $data as $key => $value ) {
			$new_data[ $key ] = is_object( $value ) ? $this->convert_object_to_array( $value ) : $value;
		}

		return $new_data;
	}

	/**
	 * Disable SSL verification in order to prevent download update failures
	 *
	 * @param array  $args HTTP Req. args
	 * @param string $url  URL
	 *
	 * @return object $array
	 */
	public function http_request_args( $args, $url ) {

		$verify_ssl = $this->verify_ssl();
		if ( strpos( $url, 'https://' ) !== false && strpos( $url, 'action=download' ) ) {
			$args['sslverify'] = $verify_ssl;
		}

		return $args;
	}

	/**
	 * Calls the API and, if successfull, returns the object delivered by the API.
	 *
	 * @param string $_action The requested action.
	 * @param array  $_data   Parameters for the API action.
	 *
	 * @return false|object
	 * @uses get_bloginfo()
	 * @uses wp_remote_post()
	 * @uses is_wp_error()
	 */
	private function api_request( $_action, $_data ) {

		global $wp_version, $paddlepress_plugin_url_available;

		$verify_ssl = $this->verify_ssl();

		// Do a quick status check on this domain if we haven't already checked it.
		$store_hash = md5( $this->api_url );
		if ( ! is_array( $paddlepress_plugin_url_available ) || ! isset( $paddlepress_plugin_url_available[ $store_hash ] ) ) {
			$test_url_parts = wp_parse_url( $this->api_url );

			$scheme = ! empty( $test_url_parts['scheme'] ) ? $test_url_parts['scheme'] : 'http';
			$host   = ! empty( $test_url_parts['host'] ) ? $test_url_parts['host'] : '';
			$port   = ! empty( $test_url_parts['port'] ) ? ':' . $test_url_parts['port'] : '';

			if ( empty( $host ) ) {
				$paddlepress_plugin_url_available[ $store_hash ] = false;
			} else {
				$test_url                                        = $scheme . '://' . $host . $port;
				$response                                        = wp_remote_get(
					$test_url,
					array(
						'timeout'   => $this->health_check_timeout,
						'sslverify' => $verify_ssl,
					)
				);
				$paddlepress_plugin_url_available[ $store_hash ] = is_wp_error( $response ) ? false : true;
			}
		}

		if ( false === $paddlepress_plugin_url_available[ $store_hash ] ) {
			return;
		}

		$data = array_merge( $this->api_data, $_data );

		if ( $data['download_tag'] !== $this->slug ) {
			return;
		}

		$api_params = array(
			'action'       => $_action,
			'license_key'  => ! empty( $data['license_key'] ) ? $data['license_key'] : '',
			'license_url'  => ! empty( $data['license_url'] ) ? $data['license_url'] : '',
			'download_tag' => $this->download_tag,
			'version'      => isset( $data['version'] ) ? $data['version'] : false,
			'beta'         => ! empty( $data['beta'] ),
		);

		$request = wp_remote_post(
			$this->api_url,
			array(
				'timeout'   => 15,
				'sslverify' => $verify_ssl,
				'body'      => $api_params,
			)
		);

		if ( ! is_wp_error( $request ) ) {
			$request = json_decode( wp_remote_retrieve_body( $request ) );
		}

		if ( $request && isset( $request->sections ) ) {
			$request->sections = maybe_unserialize( $request->sections );
		} else {
			$request = false;
		}

		if ( $request && isset( $request->banners ) ) {
			$request->banners = maybe_unserialize( $request->banners );
		}

		if ( $request && isset( $request->icons ) ) {
			$request->icons = maybe_unserialize( $request->icons );
		}

		if ( ! empty( $request->sections ) ) {
			foreach ( $request->sections as $key => $section ) {
				$request->$key = (array) $section;
			}
		}

		return $request;
	}

	/**
	 * Changelog output
	 */
	public function show_changelog() {

		global $paddlepress_plugin_data;

		if ( empty( $_REQUEST['paddlepress_action'] ) || 'view_plugin_changelog' !== $_REQUEST['paddlepress_action'] ) {
			return;
		}

		if ( empty( $_REQUEST['plugin'] ) ) {
			return;
		}

		if ( empty( $_REQUEST['slug'] ) ) {
			return;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			wp_die( esc_html__( 'You do not have permission to install plugin updates', 'paddlepress' ), esc_html__( 'Error', 'paddlepress' ), array( 'response' => 402 ) );
		}

		$data         = $paddlepress_plugin_data[ $_REQUEST['slug'] ];
		$beta         = ! empty( $data['beta'] ) ? true : false;
		$cache_key    = md5( 'paddlepress_plugin_' . sanitize_key( $_REQUEST['plugin'] ) . '_' . $beta . '_version_info' );
		$version_info = $this->get_cached_version_info( $cache_key );

		if ( false === $version_info ) {

			$api_params = array(
				'action'       => 'plugin_information',
				'slug'         => $_REQUEST['slug'],
				'beta'         => ! empty( $data['beta'] ),
				'download_tag' => $this->download_tag,
			);

			$verify_ssl = $this->verify_ssl();
			$request    = wp_remote_post(
				$this->api_url,
				array(
					'timeout'   => 15,
					'sslverify' => $verify_ssl,
					'body'      => $api_params,
				)
			);

			if ( ! is_wp_error( $request ) ) {
				$version_info = json_decode( wp_remote_retrieve_body( $request ) );
			}

			if ( ! empty( $version_info ) && isset( $version_info->sections ) ) {
				$version_info->sections = maybe_unserialize( $version_info->sections );
			} else {
				$version_info = false;
			}

			if ( ! empty( $version_info ) ) {
				foreach ( $version_info->sections as $key => $section ) {
					$version_info->$key = (array) $section;
				}
			}

			$this->set_version_info_cache( $version_info, $cache_key );

		}

		if ( ! empty( $version_info ) && isset( $version_info->sections ) ) {
			// Ensure sections are converted to an array if they are an object.
			$sections = (array) $version_info->sections;

			if ( isset( $sections['changelog'] ) ) {
				echo '<div style="background:#fff;padding:10px;">' . wp_kses_post( $sections['changelog'] ) . '</div>';
			}
		}

		exit;
	}

	/**
	 * Get version info from cache
	 *
	 * @param string $cache_key Cache key
	 *
	 * @return bool|mixed
	 */
	public function get_cached_version_info( $cache_key = '' ) {

		if ( empty( $cache_key ) ) {
			$cache_key = $this->cache_key;
		}

		$cache = get_option( $cache_key );

		if ( empty( $cache['timeout'] ) || time() > $cache['timeout'] ) {
			return false; // Cache is expired
		}

		// We need to turn the icons into an array, thanks to WP Core forcing these into an object at some point.
		$cache['value'] = json_decode( $cache['value'] );
		if ( ! empty( $cache['value']->icons ) ) {
			$cache['value']->icons = (array) $cache['value']->icons;
		}

		return $cache['value'];
	}

	/**
	 * Set cache data and save in option with TTL
	 *
	 * @param string $value     Value
	 * @param string $cache_key Key
	 */
	public function set_version_info_cache( $value = '', $cache_key = '' ) {

		if ( empty( $cache_key ) ) {
			$cache_key = $this->cache_key;
		}

		$data = array(
			'timeout' => strtotime( '+6 hours', time() ),
			'value'   => wp_json_encode( $value ),
		);

		update_option( $cache_key, $data, 'no' );
	}

	/**
	 * Returns if the SSL of the store should be verified.
	 *
	 * @return bool
	 * @since  1.0
	 */
	private function verify_ssl() {
		return (bool) apply_filters( 'paddlepress_api_request_verify_ssl', true, $this );
	}
}
