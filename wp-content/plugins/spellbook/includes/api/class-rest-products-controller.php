<?php

class GravityPerks_REST_Products_Controller extends WP_REST_Controller {
	protected $namespace = 'gwiz/v1';
	protected $rest_base = 'products';
	private $api;

	public function __construct() {
		$this->api = GWPerks::get_api();
	}

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_products' ],
				'permission_callback' => [ $this, 'check_permission' ],
				'schema'              => [ $this, 'get_product_schema' ],
			],
		] );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\w-]+)', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_product' ],
				'permission_callback' => [ $this, 'check_permission' ],
				'schema'              => [ $this, 'get_product_schema' ],
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_product' ],
				'permission_callback' => [ $this, 'check_permission' ],
				'args'               => [
					'id' => [
						'required'          => true,
						'type'             => 'string',
						'validate_callback' => [ $this, 'validate_product_exists' ],
					],
				],
			],
		] );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)/details', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_product_details' ],
				'permission_callback' => [ $this, 'check_permission' ],
				'args'               => [
					'id' => [
						'required'          => true,
						'type'             => 'integer',
						'minimum'          => 1,
					],
				],
			],
		] );

		// Register plugin management endpoints
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\w-]+)/activate', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'activate_product' ],
				'permission_callback' => [ $this, 'check_permission' ],
				'args'               => [
					'id' => [
						'required'          => true,
						'type'             => 'string',
						'validate_callback' => [ $this, 'validate_product_exists' ],
					],
				],
			],
		] );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\w-]+)/deactivate', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'deactivate_product' ],
				'permission_callback' => [ $this, 'check_permission' ],
				'args'               => [
					'id' => [
						'required'          => true,
						'type'             => 'string',
						'validate_callback' => [ $this, 'validate_product_exists' ],
					],
				],
			],
		] );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\w-]+)/install', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'install_product' ],
				'permission_callback' => [ $this, 'check_permission' ],
				'args'               => [
					'id' => [
						'required'          => true,
						'type'             => 'string',
						'validate_callback' => [ $this, 'validate_product_exists' ],
					],
				],
			],
		] );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\w-]+)/uninstall', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'uninstall_product' ],
				'permission_callback' => [ $this, 'check_permission' ],
				'args'               => [
					'id' => [
						'required'          => true,
						'type'             => 'string',
						'validate_callback' => [ $this, 'validate_product_exists' ],
					],
				],
			],
		] );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\w-]+)/update', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'update_product' ],
				'permission_callback' => [ $this, 'check_permission' ],
				'args'               => [
					'id' => [
						'required'          => true,
						'type'             => 'string',
						'validate_callback' => [ $this, 'validate_product_exists' ],
					],
				],
			],
		] );
	}

	/**
	 * Remove a product's files and optionally run its uninstall routine
	 *
	 * @param string $plugin_file Plugin file path
	 * @param bool   $run_uninstall Whether to run the uninstall routine
	 * @return true|WP_Error True on success, WP_Error on failure
	 */
	private function remove_product( $plugin_file, $run_uninstall = false ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';

		// Check if plugin is installed
		if ( ! GWPerk::is_installed( $plugin_file ) ) {
			return new WP_Error(
				'plugin_not_installed',
				__( 'Plugin is not installed.', 'spellbook' ),
				[ 'status' => 400 ]
			);
		}

		// Check if plugin is network activated
		if ( ! is_network_admin() && is_plugin_active_for_network( $plugin_file ) ) {
			return new WP_Error(
				'network_activated_plugin',
				__( 'This plugin can only be managed from the network admin\'s Plugins page.', 'spellbook' ),
				[ 'status' => 400 ]
			);
		}

		// Deactivate plugin first
		deactivate_plugins( $plugin_file, true );

		// Run uninstall routine if requested
		if ( $run_uninstall ) {
			$perk = GWPerk::get_perk( $plugin_file );
			if ( ! is_wp_error( $perk ) && method_exists( $perk, 'uninstall' ) ) {
				$perk->uninstall();
			}

			if ( method_exists( 'GFAddOn', 'get_addon_by_slug' ) ) {
				$addon = GFAddOn::get_addon_by_slug( basename( $plugin_file, '.php' ) );

				if ( $addon ) {
					$addon->uninstall();
				}
			}
		}

		// Delete plugin files
		$result = delete_plugins( array( $plugin_file ) );

		if ( ! $result ) {
			return new WP_Error(
				'remove_failed',
				__( 'Failed to remove plugin.', 'spellbook' ),
				[ 'status' => 500 ]
			);
		}

		return true;
	}

	/**
	 * Uninstall a product
	 *
	 * Uninstalls a product by running its uninstall routine to clean up any data/settings,
	 * then removes the plugin files. This provides a complete removal of the product.
	 * For removing just the files without cleaning data, use the delete endpoint.
	 *
	 * @param WP_REST_Request $request Request object
	 * @return WP_REST_Response|WP_Error Response object or WP_Error
	 */
	public function uninstall_product( $request ) {
		$plugin_file = $this->get_plugin_file_from_id( $request['id'] );
		if ( is_wp_error( $plugin_file ) ) {
			return $plugin_file;
		}

		$result = $this->remove_product( $plugin_file, true );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Get updated product data with fresh plugin cache
		GWPerks::get_plugins( true );
		$product = $this->get_product_by_id( $request['id'] );
		if ( is_wp_error( $product ) ) {
			return $product;
		}

		return rest_ensure_response( $this->prepare_product_for_response( $product, $request ) );
	}

	/**
	 * Install a product
	 *
	 * @param WP_REST_Request $request Request object
	 * @return WP_REST_Response|WP_Error Response object or WP_Error
	 */
	public function install_product( $request ) {
		$product = $this->get_product_by_id( $request['id'] );
		if ( is_wp_error( $product ) ) {
			return $product;
		}

		// Check if plugin is already installed
		if ( GWPerk::is_installed( $product->plugin_file ) ) {
			return new WP_Error(
				'plugin_already_installed',
				__( 'Plugin is already installed.', 'spellbook' ),
				[ 'status' => 400 ]
			);
		}

		// Check if we have a download URL
		if ( empty( $product->download_link ) ) {
			return new WP_Error(
				'no_download_link',
				__( 'No download URL available for this plugin.', 'spellbook' ),
				[ 'status' => 400 ]
			);
		}

		// Make sure we have the needed functions
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		// Download and install the plugin
		$skin = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$result = $upgrader->install( $product->download_link );

		if ( is_wp_error( $result ) ) {
			return new WP_Error(
				'installation_failed',
				$result->get_error_message(),
				[ 'status' => 500 ]
			);
		}

		if ( is_wp_error( $skin->result ) ) {
			return new WP_Error(
				'installation_failed',
				$skin->result->get_error_message(),
				[ 'status' => 500 ]
			);
		}

		if ( $skin->get_errors()->has_errors() ) {
			return new WP_Error(
				'installation_failed',
				$skin->get_error_messages(),
				[ 'status' => 500 ]
			);
		}

		if ( is_null( $result ) ) {
			return new WP_Error(
				'installation_failed',
				__( 'Plugin installation failed for an unknown reason.', 'spellbook' ),
				[ 'status' => 500 ]
			);
		}

		// Get updated product data with fresh plugin cache
		GWPerks::get_plugins( true );
		$product = $this->get_product_by_id( $request['id'] );
		if ( is_wp_error( $product ) ) {
			return $product;
		}

		return rest_ensure_response( $this->prepare_product_for_response( $product, $request ) );
	}

	/**
	 * Deactivate a product
	 *
	 * @param WP_REST_Request $request Request object
	 * @return WP_REST_Response|WP_Error Response object or WP_Error
	 */
	public function deactivate_product( $request ) {
		$plugin_file = $this->get_plugin_file_from_id( $request['id'] );
		if ( is_wp_error( $plugin_file ) ) {
			return $plugin_file;
		}

		// Check if plugin is already inactive
		if ( ! is_plugin_active( $plugin_file ) ) {
			return new WP_Error(
				'plugin_already_inactive',
				__( 'Plugin is already inactive.', 'spellbook' ),
				[ 'status' => 400 ]
			);
		}

		// Check if plugin is network activated
		if ( ! is_network_admin() && is_plugin_active_for_network( $plugin_file ) ) {
			return new WP_Error(
				'network_activated_plugin',
				__( 'This plugin can only be managed from the network admin\'s Plugins page.', 'spellbook' ),
				[ 'status' => 400 ]
			);
		}

		// Deactivate the plugin
		deactivate_plugins( $plugin_file, false, is_network_admin() );

		// Update recently_activated option
		if ( ! is_network_admin() ) {
			update_option( 'recently_activated', array( $plugin_file => time() ) + (array) get_option( 'recently_activated' ) );
		}

		// Get updated product data
		$product = $this->get_product_by_id( $request['id'] );
		if ( is_wp_error( $product ) ) {
			return $product;
		}

		return rest_ensure_response( $this->prepare_product_for_response( $product, $request ) );
	}

	/**
	 * Activate a product
	 *
	 * @param WP_REST_Request $request Request object
	 * @return WP_REST_Response|WP_Error Response object or WP_Error
	 */
	public function activate_product( $request ) {
		$plugin_file = $this->get_plugin_file_from_id( $request['id'] );
		if ( is_wp_error( $plugin_file ) ) {
			return $plugin_file;
		}

		// Check if plugin is already active
		if ( is_plugin_active( $plugin_file ) ) {
			return new WP_Error(
				'plugin_already_active',
				__( 'Plugin is already active.', 'spellbook' ),
				[ 'status' => 400 ]
			);
		}

		// Check if plugin is installed
		if ( ! GWPerk::is_installed( $plugin_file ) ) {
			return new WP_Error(
				'plugin_not_installed',
				__( 'Plugin must be installed before it can be activated.', 'spellbook' ),
				[ 'status' => 400 ]
			);
		}

		// Activate the plugin
		$result = activate_plugin( $plugin_file, '', is_network_admin() );

		if ( is_wp_error( $result ) ) {
			return new WP_Error(
				'activation_failed',
				$result->get_error_message(),
				[ 'status' => 500 ]
			);
		}

		// Update recently_activated option
		if ( ! is_network_admin() ) {
			$recent = (array) get_option( 'recently_activated' );
			unset( $recent[ $plugin_file ] );
			update_option( 'recently_activated', $recent );
		}

		// Get updated product data
		$product = $this->get_product_by_id( $request['id'] );
		if ( is_wp_error( $product ) ) {
			return $product;
		}

		return rest_ensure_response( $this->prepare_product_for_response( $product, $request ) );
	}

	/**
	 * Delete a product
	 *
	 * Deletes the plugin files without running the uninstall routine. This is useful when you want to remove
	 * the plugin files but keep any data/settings. For a complete removal including data, use the uninstall endpoint.
	 *
	 * The key differences between delete and uninstall:
	 * - Delete: Just removes plugin files, preserves data
	 * - Uninstall: Runs uninstall routine to clean up data, then removes files
	 *
	 * @param WP_REST_Request $request Request object
	 * @return WP_REST_Response|WP_Error Response object or WP_Error
	 */
	public function delete_product( $request ) {
		$plugin_file = $this->get_plugin_file_from_id( $request['id'] );
		if ( is_wp_error( $plugin_file ) ) {
			return $plugin_file;
		}

		$result = $this->remove_product( $plugin_file, false );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		// Get updated product data with fresh plugin cache
		GWPerks::get_plugins( true );
		$product = $this->get_product_by_id( $request['id'] );
		if ( is_wp_error( $product ) ) {
			return $product;
		}

		return rest_ensure_response( $this->prepare_product_for_response( $product, $request ) );
	}

	/**
	 * Get plugin file path from product ID
	 *
	 * @param string $id Product ID
	 * @return string|WP_Error Plugin file path or WP_Error
	 */
	private function get_plugin_file_from_id( $id ) {
		$product = $this->get_product_by_id( $id );
		if ( is_wp_error( $product ) ) {
			return $product;
		}

		return $product->plugin_file;
	}

	/**
	 * Validate that a product exists
	 *
	 * @param string $value Product ID to validate
	 * @return bool|WP_Error True if valid, WP_Error if not
	 */
	public function validate_product_exists( $value ) {
		$products = $this->api->get_products();
		if ( is_wp_error( $products ) ) {
			return $products;
		}

		foreach ( $products as $product ) {
			if ( $product->slug === $value ) {
				return true;
			}
		}

		return new WP_Error(
			'rest_plugin_invalid',
			__( 'Plugin not found.', 'spellbook' ),
			[ 'status' => 404 ]
		);
	}

	/**
	 * Get a product by its ID
	 *
	 * @param string $id Product ID
	 * @return object|WP_Error Product object or WP_Error if not found
	 */
	private function get_product_by_id( $id ) {
		$products = $this->api->get_products();
		if ( is_wp_error( $products ) ) {
			return $products;
		}

		foreach ( $products as $product ) {
			if ( $product->slug === $id ) {
				return $product;
			}
		}

		return new WP_Error(
			'plugin_not_found',
			__( 'Plugin not found.', 'spellbook' ),
			[ 'status' => 404 ]
		);
	}

	/**
	 * Update a product
	 *
	 * @param WP_REST_Request $request Request object
	 * @return WP_REST_Response|WP_Error Response object or WP_Error
	 */
	public function update_product( $request ) {
		$plugin_file = $this->get_plugin_file_from_id( $request['id'] );
		if ( is_wp_error( $plugin_file ) ) {
			return $plugin_file;
		}

		// Check if plugin is installed
		if ( ! GWPerk::is_installed( $plugin_file ) ) {
			return new WP_Error(
				'plugin_not_installed',
				__( 'Plugin must be installed before it can be updated.', 'spellbook' ),
				[ 'status' => 400 ]
			);
		}

		// Check if plugin is network activated
		if ( ! is_network_admin() && is_plugin_active_for_network( $plugin_file ) ) {
			return new WP_Error(
				'network_activated_plugin',
				__( 'This plugin can only be managed from the network admin\'s Plugins page.', 'spellbook' ),
				[ 'status' => 400 ]
			);
		}

		// Get product data to check for updates
		$product = $this->get_product_by_id( $request['id'] );
		if ( is_wp_error( $product ) ) {
			return $product;
		}

		// Check if update is available
		if ( ! isset( $product->new_version ) || ! version_compare( $product->new_version, $this->api->get_local_product_version( $plugin_file ), '>' ) ) {
			return new WP_Error(
				'no_update_available',
				__( 'No update available for this plugin.', 'spellbook' ),
				[ 'status' => 400 ]
			);
		}

		// Make sure we have the needed functions
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		// Store active state before update
		$was_active = is_plugin_active( $plugin_file );

		// Ensure we have remote plugin data. After updating a plugin, the cache will be purged.
		wp_update_plugins();

		// Update the plugin
		$skin = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$result = $upgrader->upgrade( $plugin_file );

		if ( is_wp_error( $result ) ) {
			return new WP_Error(
				'update_failed',
				$result->get_error_message(),
				[ 'status' => 500 ]
			);
		}

		if ( is_wp_error( $skin->result ) ) {
			return new WP_Error(
				'update_failed',
				$skin->result->get_error_message(),
				[ 'status' => 500 ]
			);
		}

		if ( $skin->get_errors()->has_errors() ) {
			return new WP_Error(
				'update_failed',
				$skin->get_error_messages(),
				[ 'status' => 500 ]
			);
		}

		if ( is_null( $result ) ) {
			return new WP_Error(
				'update_failed',
				__( 'Plugin update failed for an unknown reason.', 'spellbook' ),
				[ 'status' => 500 ]
			);
		}

		// Reactivate if it was active
		if ( $was_active ) {
			$activate_result = activate_plugin( $plugin_file );
			if ( is_wp_error( $activate_result ) ) {
				return new WP_Error(
					'reactivation_failed',
					__( 'Plugin updated but reactivation failed: ', 'spellbook' ) . $activate_result->get_error_message(),
					[ 'status' => 500 ]
				);
			}
		}

		// Get updated product data
		$product = $this->get_product_by_id( $request['id'] );
		if ( is_wp_error( $product ) ) {
			return $product;
		}

		return rest_ensure_response( $this->prepare_product_for_response( $product, $request ) );
	}

	public function check_permission( $request ) {
		$method = $request->get_method();
		$route = $request->get_route();

		// Handle different capabilities based on route and method
		if ( strpos( $route, '/activate' ) !== false ) {
			return current_user_can( 'activate_plugins' );
		}

		if ( $method === 'DELETE' || strpos( $route, '/uninstall' ) !== false ) {
			return current_user_can( 'delete_plugins' );
		}

		if ( strpos( $route, '/install' ) !== false || strpos( $route, '/update' ) !== false ) {
			return current_user_can( 'install_plugins' );
		}

		return current_user_can( 'manage_options' );
	}

	public function get_products( $request ) {
		$force = $request->get_param('force');
		$products = $this->api->get_products(!!$force);

		if ( is_wp_error( $products ) ) {
			return $products;
		}

		$products_data = [];

		$excluded_slugs = [
			'gravityperks',
			'spellbook',
		];

		foreach ( $products as $product ) {
			if ( in_array( $product->slug, $excluded_slugs, true ) ) {
				continue;
			}

			$products_data[] = $this->prepare_product_for_response( $product, $request );
		}

		// Sort products by name
		usort( $products_data, function ( $a, $b ) {
			return strcasecmp( $a['name'], $b['name'] );
		} );

		return rest_ensure_response( $products_data );
	}

	public function get_product( $request ) {
		$id = $request['id'];
		$products = $this->api->get_products();

		if ( is_wp_error( $products ) ) {
			return $products;
		}

		foreach ( $products as $product ) {
			if ( $product->slug === $id ) {
				return rest_ensure_response( $this->prepare_product_for_response( $product, $request ) );
			}
		}

		return new WP_Error(
			'plugin_not_found',
			__( 'Plugin not found.', 'spellbook' ),
			[ 'status' => 404 ]
		);
	}

	/**
	 * Get detailed product information including changelog
	 *
	 * @param WP_REST_Request $request Request object
	 * @return WP_REST_Response|WP_Error Response object or WP_Error
	 */
	public function get_product_details( $request ) {
		$product_id = (int) $request['id'];

		// Get detailed product data with changelog
		$detailed_product = $this->api->get_product( $product_id );
		if ( is_wp_error( $detailed_product ) ) {
			return $detailed_product;
		}

		if ( ! $detailed_product ) {
			return new WP_Error(
				'plugin_details_not_found',
				__( 'Plugin details not found.', 'spellbook' ),
				[ 'status' => 404 ]
			);
		}

		return rest_ensure_response( $this->prepare_product_for_response( $detailed_product, $request ) );
	}

	public function is_installed( $product ) {
		return GWPerk::is_installed( $product->plugin_file );
	}

	public function is_deprecated( $product ) {
		if ( in_array( 'deprecated', $product->categories, true ) ) {
			return true;
		}
		return false;
	}

	public function prepare_product_for_response( $product, $request ) {
		$is_legacy_free_plugin = $this->api->is_legacy_free_plugin( $product );

		// Convert object to array and add computed fields
		$data = array(
			'ID'            => $product->ID,
			'name'          => $product->name,
			'version'       => GWPerk::is_installed( $product->plugin_file ) ? $this->api->get_local_product_version( $product->plugin_file ) : $product->version,
			'new_version'   => isset( $product->new_version ) ? $product->new_version : $product->version,
			'has_update'    => isset( $product->new_version ) && GWPerk::is_installed( $product->plugin_file ) && version_compare( $product->new_version, $this->api->get_local_product_version( $product->plugin_file ), '>' ),
			'slug'          => $product->slug,
			'plugin_file'   => $product->plugin_file,
			'plugin'        => $product->plugin_file, // For backwards compatibility
			'homepage'      => $product->homepage,
			'documentation' => $product->documentation,
			'sections'      => $product->sections,
			'banners'       => isset( $product->banners ) ? $product->banners : array(),
			'icons'         => isset( $product->icons ) ? $product->icons : array(),
			'categories'    => $product->categories,
			'type'          => $product->type,
			'last_updated'  => $product->last_updated,
			'download_link' => $product->download_link,
			'can_uninstall' => GWPerk::is_perk( $product->plugin_file )
				&& GWPerk::is_installed( $product->plugin_file )
				&& GWPerk::get_perk( $product->plugin_file )
				&& method_exists( GWPerk::get_perk( $product->plugin_file ), 'uninstall' ),
			'is_legacy_free_plugin' => $is_legacy_free_plugin,
			'is_installed'  => GWPerk::is_installed( $product->plugin_file ) || $is_legacy_free_plugin,
			'is_active'     => is_plugin_active( $product->plugin_file ),
			'is_deprecated' => $this->is_deprecated( $product ),
			/*
			 * Performance Note: Loading perk classes to check for settings is expensive.
			 * We're keeping this for now since only a few perks like GP Better User Activation
			 */
			'has_settings'  => $this->has_settings( $product->plugin_file )
		);

		return $data;
	}

	private function has_settings( $plugin_file ) {
		/*
		 * Since this functionality is deprecated, we will just use $perk->method_is_overridden() for
		 * the perks that have settings like GP Better User Activation.
		 */
		$plugins_with_settings = array(
			'gp-better-user-activation/gp-better-user-activation.php',
			'gwexpandtextareas/gwexpandtextareas.php',
		);

		if ( ! in_array( $plugin_file, $plugins_with_settings, true ) ) {
			return false;
		}

		$perk = GWPerk::get_perk( $plugin_file );
		return $perk && ! is_wp_error( $perk ) && $perk->method_is_overridden( 'settings' );
	}

	public function get_product_schema() {
		return [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'product',
			'type'       => 'object',
			'properties' => [
				'ID'            => [
					'description' => __( 'Unique identifier for the plugin.', 'spellbook' ),
					'type'        => 'integer',
					'readonly'    => true,
				],
				'name'          => [
					'description' => __( 'Plugin name.', 'spellbook' ),
					'type'        => 'string',
				],
				'version'       => [
					'description' => __( 'Plugin version.', 'spellbook' ),
					'type'        => 'string',
				],
				'new_version'   => [
					'description' => __( 'Available update version.', 'spellbook' ),
					'type'        => 'string',
				],
				'slug'          => [
					'description' => __( 'Plugin slug.', 'spellbook' ),
					'type'        => 'string',
				],
				'plugin_file'   => [
					'description' => __( 'Plugin file path.', 'spellbook' ),
					'type'        => 'string',
				],
				'plugin'        => [
					'description' => __( 'Plugin file path (alias for backwards compatibility).', 'spellbook' ),
					'type'        => 'string',
				],
				'homepage'      => [
					'description' => __( 'Plugin homepage URL.', 'spellbook' ),
					'type'        => 'string',
					'format'      => 'uri',
				],
				'documentation' => [
					'description' => __( 'Documentation URL.', 'spellbook' ),
					'type'        => 'string',
					'format'      => 'uri',
				],
				'sections'      => [
					'description' => __( 'Plugin sections including description and changelog.', 'spellbook' ),
					'type'        => 'object',
					'properties'  => [
						'description' => [ 'type' => 'string' ],
						'changelog'   => [ 'type' => 'string' ],
					],
				],
				'banners'       => [
					'description' => __( 'Plugin banner images.', 'spellbook' ),
					'type'        => 'object',
					'properties'  => [
						'high' => [
							'type'   => 'string',
							'format' => 'uri',
						],
						'low'  => [
							'type'   => 'string',
							'format' => 'uri',
						],
					],
				],
				'icons'         => [
					'description' => __( 'Plugin icons.', 'spellbook' ),
					'type'        => 'object',
					'properties'  => [
						'1x' => [
							'type'   => 'string',
							'format' => 'uri',
						],
						'2x' => [
							'type'   => 'string',
							'format' => 'uri',
						],
					],
						],
				'categories'    => [
					'description' => __( 'Plugin categories.', 'spellbook' ),
					'type'        => 'array',
					'items'       => [ 'type' => 'string' ],
				],
				'is_installed'  => [
					'description' => __( 'Whether the plugin is installed.', 'spellbook' ),
					'type'        => 'boolean',
				],
				'is_active'     => [
					'description' => __( 'Whether the plugin is active.', 'spellbook' ),
					'type'        => 'boolean',
				],
				'type'          => [
					'description' => __( 'Plugin type.', 'spellbook' ),
					'type'        => 'string',
					'enum'        => [ 'perk', 'connect', 'shop' ],
				],
				'last_updated'  => [
					'description' => __( 'Last updated date.', 'spellbook' ),
					'type'        => 'string',
				],
				'download_link' => [
					'description' => __( 'Download URL for the plugin.', 'spellbook' ),
					'type'        => 'string',
					'format'      => 'uri',
				],
				'has_settings' => [
					'description' => __( 'Whether the plugin has settings.', 'spellbook' ),
					'type'        => 'boolean',
				],
			]
		];
	}
}
