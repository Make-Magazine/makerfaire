<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

Class MapifyPro_Installer {

	/**
	 * Plugin slug
	 * @var string
	 */
	public $plugin_slug;

	/**
	 * Product ids
	 * @var array
	 */
	public $product_ids;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_slug = '/mapify_pro.php';
		$this->product_ids = array(
			8672  => 'Monthly Normal License',
			8739  => 'Monthly Developer License',
			46018 => 'Yearly Normal License',
			46019 => 'Yearly Developer License',
		);

		// admin hooks
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'wp_ajax_mapifypro_installer_action', array( $this, 'ajax_installer_action' ) );
		add_action( 'admin_action_activate_mapfypro', array( $this, 'action_activate_mapfypro' ) );
		add_action( 'admin_action_activate_mapfypro_api', array( $this, 'action_activate_mapfypro_api' ) );
	}

	/**
	 * Setup WordPress admin menu
	 */
	public function admin_menu() {
		add_submenu_page( 
			'mapify.php', 
			__( 'MapifyPro Installer', 'mpfy' ), 
			__( 'MapifyPro Installer', 'mpfy' ),
			'administrator', 
			'mapifypro-installer', 
			array( $this, 'admin_menu_callback' ) 
		);
	}

	/**
	 * Setup WordPress admin page
	 */
	public function admin_menu_callback() {
		$data    = get_option( 'example-data', '' );
		$updated = isset( $_GET['updated'] ) ? $_GET['updated'] : false;
		$api_key = $this->get_installer_api_key();
		
		include_once( plugin_dir_path( __FILE__ ) . 'admin-page.php' );
	}

	/**
	 * Enqueue admin scripts and styles
	 * 
	 * @param string $hook Page location identifier. 
	 */
	public function admin_enqueue_scripts( $hook ) {
		if( 'mapifylite_page_mapifypro-installer' !== $hook ) return;

		wp_enqueue_style( 'mapifypro-installer-admin', plugin_dir_url( __FILE__ ) . 'style-admin.css' );
		wp_enqueue_script( 'mapifypro-installer-admin', plugin_dir_url( __FILE__ ) . 'script-admin.js' );
		wp_localize_script( 'mapifypro-installer-admin', 'mapifypro_installer', array(
	        'product_ids'         => $this->product_ids,
			'verified_message'    => __( 'You have set the installation for %s.', 'mpfy' ),
			'invalid_api_message' => __( 'Invalid API key. Operation terminated.', 'mpfy' ),
	    ));
	}

	/**
	 * Ajax responses for the installer javascript request
	 */
	public function ajax_installer_action() {
		check_ajax_referer( 'mapifypro_installer_action_nonce', 'nonce' );

		$current_api_key = sanitize_text_field( $_POST['api_key'] );
		$saved_api_key   = $this->get_installer_api_key();

		if ( $this->is_plugin_installed() ) {
			if ( $saved_api_key === $current_api_key && $saved_api_key !== '' ) {
				$response = array( 
					'status'  => 'plugin_already_installed',
					'message' => sprintf( 
						__( 'MapifyPro is already installed. Please click the button below to activate MapifyPro. %sActivate MapifyPro%s', 'mpfy' ),
						sprintf( '<br><a href="%s" class="button activate-mapifypro">', $this->get_activation_url() ), '</a>'
					),
				);
			} else {
				$response = array( 
					'status'  => 'plugin_already_installed',
					'message' => __( 'You hava a MapifyPro installed but not for your API key. Please delete it first before install the new one.', 'mpfy' ),
				);
			}
		} else {
			$response = $this->ajax_request_handler();
		}

		wp_send_json( $response );
	}

	/**
	 * Check whether the plugin is installed or not
	 * 
	 * @return bool Whether the plugin is installed or note.
	 */
	private function is_plugin_installed() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
	
		$plugin_is_installed = false;
		$all_plugins         = get_plugins();

		foreach ( $all_plugins as $plugin_slug => $plugin_data ) {
			if ( false !== strpos( $plugin_slug, $this->plugin_slug ) && 'MapifyPro' === $plugin_data['Name'] ) {
				$plugin_is_installed = true;
				$this->plugin_slug   = $plugin_slug;
				break;
			}
		}
	
		return $plugin_is_installed;
	}

	/**
	 * Get activation url
	 * 
	 * @return string 
	 */
	private function get_activation_url() {
		$is_installed = $this->is_plugin_installed();
		$plugin_slug  = $is_installed ? $this->plugin_slug : 'mapifypro' . $this->plugin_slug;

		return wp_nonce_url( 
			admin_url( 'admin.php?action=activate_mapfypro&slug=' . $plugin_slug ), 
			'activate_mapfypro' 
		);
	}

	/**
	 * Handle all ajax request by it `action` variable.
	 * 
	 * @return array Ajax response.
	 */
	private function ajax_request_handler() {
		$api_key  = sanitize_text_field( $_POST['api_key'] );
		$action   = sanitize_text_field( $_POST['action_request'] );
		$variable = sanitize_text_field( $_POST['request_variable'] );

		switch ( $action ) {
			case 'verify_api_key':
				$product_ids_data = $this->get_available_product_ids( $api_key );
				$product_ids      = array_keys( $product_ids_data );
				$response_status  = 'api_key_verified';
				
				if ( count( $product_ids ) > 1 ) {
					$message = __( 'We found multiple license available for your API key. Which one do you want to install?', 'mpfy' );
				} elseif ( count( $product_ids ) === 1 )  {
					$message = sprintf( __( 'API key is verified for %s.', 'mpfy' ), $this->product_ids[ $product_ids[0] ] );
				} else {
					$response_status = 'invalid_api_keys';
					$message         = $this->get_log();
				}

				// save API key data
				if ( 'invalid_api_keys' !== $response_status ) {
					$this->set_installer_api_key( $api_key );
				}

				$return = array( 
					'status'           => $response_status,
					'message'          => $message,
					'product_ids'      => $product_ids,
					'product_ids_data' => $product_ids_data,
				);
				break;

			case 'activate_api_key':
				$product_ids_data = $this->get_available_product_ids( $api_key );
				$product_id       = absint( $variable );
				$api_instance     = $this->activate_api_key( $api_key, $product_id );
				$response_status  = 'api_key_activated';
				$message          = __( 'Dowloading MapifyPro plugin...', 'mpfy' );
				$download_url     = null;

				if ( $api_instance ) {
					$download_url = $this->get_plugin_url( $api_key, $product_id );

					// save the activated plugin data to the database
					update_option( 'mapifypro_installer', array(
						'product_id'   => $product_id,
						'api_key'      => $api_key,
						'api_instance' => $api_instance,
					) );

					if ( ! $download_url ) {
						$response_status = 'failed_getting_download_url';
						$message         = $this->get_log();
					}
				} else {
					$response_status = 'failed_activating_api_key';
					$message         = $this->get_log();
				}

				$return = array( 
					'status'       => $response_status,
					'message'      => $message,
					'download_url' => $download_url,
				);
				break;

			case 'install_plugin':
				$plugin_url      = $variable;
				$downloaded      = $this->install_plugin( $plugin_url );
				$response_status = 'plugin_downloaded';
				$message         = sprintf( 
					__( 'MapifyPro has been installed successfully. Please click the button below to activate MapifyPro. %sActivate MapifyPro%s', 'mpfy' ),
					sprintf( '<br><a href="%s" class="button activate-mapifypro">', $this->get_activation_url() ), '</a>'
				);
				
				if ( is_wp_error( $downloaded ) || ! $downloaded ) {
					$response_status = 'failed_install_plugin';
					$message         = $this->get_log();
				}

				$return = array( 
					'status'  => $response_status,
					'message' => $message,
				);
				break;
			
			default:
				$return = array( 'message' => __( 'Operation terminated', 'mpfy' ) );
				break;
		}

		return $return;
	}

	/**
	 * Set installer API key data
	 * 
	 * @param string $api_key MapifyPro API key.
	 */
	private function set_installer_api_key( $api_key ) {
		$options            = get_option( 'mapifypro_installer', array() );
		$options['api_key'] = sanitize_text_field( $api_key );

		update_option( 'mapifypro_installer', $options );
	}

	/**
	 * Get installer API key data
	 * 
	 * @param string $api_key MapifyPro API key.
	 */
	private function get_installer_api_key() {
		$options = get_option( 'mapifypro_installer', array() );
		return isset( $options['api_key'] ) ? $options['api_key'] : '';
	}

	/**
	 * Get available product IDs by testing all IDs for the API key.
	 * 
	 * @param string $api_key MapifyPro API key.
	 * @return array Available product IDs.
	 */
	private function get_available_product_ids( $api_key ) {
		$available_product_ids = array();
		
		foreach ( $this->product_ids as $product_id => $product_label ) {
			$wc_am_client = new \MapifyPro_Installer_WC_AM_Client( $api_key, $product_id );
			$status       = $wc_am_client->get_status();
			
			if ( $status && $status['data']['activations_remaining'] > 0 ) {
				$available_product_ids[ $product_id ] = $status;
			}
		}

		return $available_product_ids;
	}

	/**
	 * Send request to activate the API with current product ID.
	 * Also activate the plugin on the current installation.
	 * 
	 * @param string $api_key MapifyPro API key.
	 * @param integer $product_id MapifyPro product ID.
	 * @param string|null $instance API instance to activate.
	 * @return string|false Activated API instance string or false.
	 */
	private function activate_api_key( $api_key, $product_id, $instance = null ) {
		$wc_am_client = new \MapifyPro_Installer_WC_AM_Client( $api_key, $product_id, $instance );
		$status       = $wc_am_client->get_status();

		if ( ! $status ) {
			return false;
		} elseif ( $status && 'inactive' === $status['status_check'] ) {
			$activate = $wc_am_client->activate_api_key();
			$instance = $status['instance'];

			if ( ! isset( $activate['activated'] ) || false === $activate['activated'] ) {
				return false;
			}
		}

		// set activation status on the database
		update_option( sprintf( 'wc_am_client_%s_deactivate_checkbox', $product_id ), 'off' );
		update_option( sprintf( 'wc_am_client_%s_activated', $product_id ), 'Activated' );
		update_option( sprintf( 'wc_am_client_%s_instance', $product_id ), $instance );
		update_option( sprintf( 'wc_am_client_%s', $product_id ), array(
			sprintf( 'wc_am_client_%s_api_key', $product_id ) => $api_key, 
		) );

		return $instance;
	}

	/**
	 * Get plugin file (zip) url.
	 * 
	 * @param string $api_key MapifyPro API key.
	 * @param integer $product_id MapifyPro product ID.
	 * @return string Plugin file url.
	 */
	private function get_plugin_url( $api_key, $product_id ) {
		$wc_am_client = new \MapifyPro_Installer_WC_AM_Client( $api_key, $product_id );
		return $wc_am_client->get_plugin_url();
	}

	/**
	 * Download and then install the plugin.
	 * 
	 * @param string $plugin_url Url of the plugin zip file to download.
	 * @return WP_Error|bool Whether the plugin has successfully installed or not. 
	 */
	private function install_plugin( $plugin_url ) {
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
		wp_cache_flush();
		
		$upgrader  = new \Plugin_Upgrader( new \WP_Ajax_Upgrader_Skin() );
		$installed = $upgrader->install( $plugin_url );
	
		return $installed;
	}

	/**
	 * WordPress action for the activate_mapfypro request.
	 * Will activate MapifyPro if the plugin exist.
	 */
	public function action_activate_mapfypro() {
		check_admin_referer( 'activate_mapfypro' );

		$plugin_slug  = sanitize_text_field( $_GET['slug'] );
		$plugin_path  = WP_PLUGIN_DIR . '/' . $plugin_slug;
		$redirect_url = admin_url( 'plugins.php' );
		$options      = get_option( 'mapifypro_installer', array() );
		$product_id   = is_array( $options ) && isset( $options['product_id'] ) ? $options['product_id'] : '';

		// activate MapifyPro
		if ( file_exists( $plugin_path ) ) {
			activate_plugin( $plugin_path );
			$redirect_url = add_query_arg( 
				array(
					'action'      => 'activate_mapfypro_api', 
					'_wpnonce'    => wp_create_nonce( 'activate_mapfypro_api' ), 
					'option_page' => sprintf( 'wc_am_client_%s_deactivate_checkbox', $product_id ), 
				), 
				admin_url( 'admin.php' )
			);
		}

		if ( wp_redirect( $redirect_url ) ) {
			exit;
		}
	}

	/**
	 * WordPress action for the activate_mapfypro_api request to activate MapifyPro API key.
	 * This should be called after the plugin has been activated.
	 */
	public function action_activate_mapfypro_api() {
		check_admin_referer( 'activate_mapfypro_api' );

		$options      = get_option( 'mapifypro_installer', array() );
		$product_id   = is_array( $options ) && isset( $options['product_id'] ) ? $options['product_id'] : null;
		$api_key      = is_array( $options ) && isset( $options['api_key'] ) ? $options['api_key'] : null;
		$instance     = is_array( $options ) && isset( $options['api_instance'] ) ? $options['api_instance'] : null;
		$redirect_url = admin_url( 'plugins.php' );

		// activate api key
		if ( $product_id ) {
			$this->activate_api_key( $api_key, $product_id, $instance );
			$redirect_url = admin_url( 'options-general.php?page=wc_am_client_' . $product_id . '_dashboard' );
		}

		// deactivate MapifyLite
		deactivate_plugins( MAPIFYPRO_INSTALLER_BASENAME );
		
		if ( wp_redirect( $redirect_url ) ) {
			exit;
		}
	}

	/**
	 * Get log message.
	 * 
	 * @return string Error log message.
	 */
	private function get_log() {
		$message = get_option( 'mapifypro_installer_log', false );
		
		if ( ! $message ) {
			$message = __( 'Unidentified error occured. Please try again later.', 'mpfy' );
		} else {
			$message.= '&nbsp;' . __( 'Operation terminated.', 'mpfy' );
		}

		return $message;
	}

}

// include wc-am-client library
include_once( plugin_dir_path( __FILE__ ) . 'wc-am-client.php' );

// plugin basename
define( 'MAPIFYPRO_INSTALLER_BASENAME', plugin_basename( MAPIFY_PLUGIN_FILE ) );

// execute the plugin
new MapifyPro_Installer();