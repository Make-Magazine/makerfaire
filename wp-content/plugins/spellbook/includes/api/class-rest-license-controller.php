<?php

class GravityPerks_REST_License_Controller extends WP_REST_Controller {
	protected $namespace = 'gwiz/v1';
	protected $rest_base = 'license';
	private $api;

	public function __construct() {
		$this->api = GWPerks::get_api();
	}

	public function register_routes() {
		// GET /license - Get all licenses
		register_rest_route($this->namespace, '/' . $this->rest_base, [
			'methods' => WP_REST_Server::READABLE,
			'callback' => [$this, 'get_licenses'],
			'permission_callback' => [$this, 'check_permission'],
		]);

		// GET /license/{product_type} - Get license for product type
		register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<product_type>[\w-]+)', [
			'methods' => WP_REST_Server::READABLE,
			'callback' => [$this, 'get_license'],
			'permission_callback' => [$this, 'check_permission'],
			'args' => [
				'product_type' => [
					'required' => true,
					'type' => 'string',
					'enum' => array_keys( GWPerks::get_api()::$product_config ),
				],
			],
		]);

		// POST /license/{product_type}/validate - Validate/register license
		register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<product_type>[\w-]+)/validate', [
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => [$this, 'validate_license'],
			'permission_callback' => [$this, 'check_permission'],
			'args' => [
				'product_type' => [
					'required' => true,
					'type' => 'string',
					'enum' => array_keys( GWPerks::get_api()::$product_config ),
				],
				'license_key' => [
					'required' => true,
					'type' => 'string',
				],
			],
		]);

		// POST /license/{product_type}/deactivate - Deactivate license
		register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<product_type>[\w-]+)/deactivate', [
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => [$this, 'deactivate_license'],
			'permission_callback' => [$this, 'check_permission'],
			'args' => [
				'product_type' => [
					'required' => true,
					'type' => 'string',
					'enum' => array_keys( GWPerks::get_api()::$product_config ),
				],
			],
		]);

		// POST /license/{product_type}/products/{id}/register - Register product
		register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<product_type>[\w-]+)/products/(?P<id>[\w-]+)/register', [
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => [$this, 'register_product'],
			'permission_callback' => [$this, 'check_permission'],
			'args' => [
				'product_type' => [
					'required' => true,
					'type' => 'string',
					'enum' => array_keys( GWPerks::get_api()::$product_config ),
				],
				'id' => [
					'required' => true,
					'type' => 'string',
				],
			],
		]);

		// POST /license/{product_type}/products/{id}/deregister - Deregister product
		register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<product_type>[\w-]+)/products/(?P<id>[\w-]+)/deregister', [
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => [$this, 'deregister_product'],
			'permission_callback' => [$this, 'check_permission'],
			'args' => [
				'product_type' => [
					'required' => true,
					'type' => 'string',
					'enum' => array_keys( GWPerks::get_api()::$product_config ),
				],
				'id' => [
					'required' => true,
					'type' => 'string',
				],
			],
		]);
	}

	/**
	 * Get all licenses
	 */
	public function get_licenses($request) {
		$licenses = [];
		$product_types = array_keys($this->api::$product_config);
		$force = $request->get_param('force');

		foreach ($product_types as $type) {
			if (
				isset($this->api::$product_config[$type]['has_license'])
				&& $this->api::$product_config[$type]['has_license'] === false
			) {
				continue;
			}

			$license_key = $this->api->get_license_key($type);

			if (empty($license_key)) {
				continue;
			}

			$license_data = $this->api->get_license_data($type, !!$force);

			if (!is_wp_error($license_data)) {
				$licenses[$type] = $this->prepare_license_for_response($license_data, $type);
			}
		}

		return rest_ensure_response($licenses);
	}

	/**
	 * Get license for specific product type
	 */
	public function get_license($request) {
		$product_type = $request['product_type'];
		$force = $request->get_param('force');
		$license_data = $this->api->get_license_data($product_type, !!$force);

		if (is_wp_error($license_data)) {
			return $license_data;
		}

		return rest_ensure_response($this->prepare_license_for_response($license_data, $product_type));
	}

	/**
	 * Validate a license key
	 */
	public function validate_license($request) {
		$product_type = $request['product_type'];
		$license_key = $request['license_key'];

		// Set the license key for the product type
		$this->api->set_license_key($product_type, $license_key);

		// Force check by passing true to flush cache
		$license_data = $this->api->get_license_data( $product_type, true );

		if ($license_data['license'] === 'invalid') {
			// Clear the license key if it's invalid
			$this->api->remove_license_key($product_type);

			return $this->format_error(
				'invalid_license',
				__('The provided license key is invalid.', 'spellbook'),
				400
			);
		}

		// Check if the API returned Wiz Bundle data when we asked for perk/connect
		// This means the license was already upgraded on the backend
		$returned_item_name = urldecode($license_data['item_name'] ?? '');
		$is_bundle_response = $returned_item_name === 'Wiz Bundle' && in_array($product_type, ['perk', 'connect']);

		if ($is_bundle_response) {
			// The license has been upgraded to Wiz Bundle
			// Perform the migration to update local state
			$this->api->handle_wiz_bundle_upgrade($license_data['license_key'] ?? $license_key, $product_type);

			// Get fresh bundle license data
			$bundle_license_data = $this->api->get_license_data('wiz-bundle', true);
			$prepared = $this->prepare_license_for_response($bundle_license_data, 'wiz-bundle');

			return rest_ensure_response([
				'is_valid' => true,
				'migrated' => true,
				'from_type' => $product_type,
				'to_type' => 'wiz-bundle',
				'message' => __('Your license has been upgraded to the Wiz Bundle!', 'spellbook'),
				'license_data' => $prepared,
			]);
		}

		// Check for bundle upgrade via mismatch (legacy path)
		$bundle_license_data = $this->api->check_and_handle_wiz_bundle_upgrade( $license_data, $product_type );

		if ( $bundle_license_data ) {
			$prepared = $this->prepare_license_for_response($bundle_license_data, 'wiz-bundle');

			return rest_ensure_response([
				'is_valid' => true,
				'migrated' => true,
				'from_type' => $product_type,
				'to_type' => 'wiz-bundle',
				'message' => __('Your license has been upgraded to the Wiz Bundle!', 'spellbook'),
				'license_data' => $prepared,
			]);
		}

		// Handle regular mismatch
		if ($license_data['license'] === 'item_name_mismatch') {
			$this->api->remove_license_key($product_type);
			return $this->format_error(
				'license_mismatch',
				__('The provided license key does not match this product.', 'spellbook'),
				400
			);
		}

		// Valid license
		$license_data = $this->api->get_license_data($product_type);
		$prepared = $this->prepare_license_for_response($license_data, $product_type);

		return rest_ensure_response([
			'is_valid' => true,
			'message' => __('License key is valid', 'spellbook'),
			'license_data' => $prepared,
		]);
	}

	/**
	 * Deactivate a license
	 */
	/**
	 * Deactivate a license
	 */
	public function deactivate_license($request) {
		$product_type = $request['product_type'];
		$result = $this->api->deactivate_license($product_type);

		if (!$result) {
			return $this->format_error(
				'deactivation_failed',
				__('Failed to deactivate license.', 'spellbook'),
				400
			);
		}

		return rest_ensure_response([
			'success' => true,
			'message' => __('License deactivated successfully', 'spellbook'),
			'license_data' => null
		]);
	}

	/**
	 * Register a product under the current license
	 */
	public function register_product($request) {
		$product_type = $request['product_type'];
		$product_id = $request['id'];

		$result = $this->api->register_product($product_id, $product_type);

		if (!$result) {
			return $this->format_error(
				'product_registration_failed',
				__('Failed to register product.', 'spellbook'),
				400
			);
		}

		// Get updated license data
		$license_data = $this->api->get_license_data($product_type, true);

		return rest_ensure_response([
			'success' => true,
			'message' => __('Product registered successfully', 'spellbook'),
			'license_data' => $this->prepare_license_for_response($license_data, $product_type),
			'product' => [
				'id' => $product_id,
				'is_registered' => true,
				'registered_at' => current_time('c'),
			],
		]);
	}

	/**
	 * Deregister a product from the current license
	 */
	public function deregister_product($request) {
		$product_type = $request['product_type'];
		$product_id = $request['id'];

		$result = $this->api->deregister_product($product_id, $product_type);

		if (!$result) {
			return $this->format_error(
				'product_deregistration_failed',
				__('Failed to deregister product.', 'spellbook'),
				400
			);
		}

		return rest_ensure_response([
			'success' => true,
			'message' => __('Product deregistered successfully', 'spellbook'),
			'product' => [
				'id' => $product_id,
				'is_registered' => false,
				'deregistered_at' => current_time('c'),
			],
		]);
	}

	/**
	 * Check if user has permission to access endpoints
	 */
	public function check_permission($request) {
		return current_user_can('manage_options');
	}

	/**
	 * Format license data for response
	 *
	 * @param array{
	 *   success: bool,
	 *   license: 'valid'|'invalid'|'expired'|'site_inactive'|'item_name_mismatch',
	 *   item_id: false,
	 *   item_name: 'Gravity+Perks',
	 *   checksum: string,
	 *   expires: string|'lifetime',
	 *   payment_id: int,
	 *   customer_name: string,
	 *   customer_email: string,
	 *   license_limit: int,
	 *   site_count: int,
	 *   activations_left: int|'unlimited',
	 *   price_id: string,
	 *   ID: int,
	 *   price_name: string,
	 *   valid: bool,
	 *   registered_perks: array<string>,
	 *   perk_limit: int,
	 *   gcgs_eligible: string
	 *   extend_url: string
	 *   manage_url: string
	 *   upgrade_url: string
	 * }|array{
	 *   success: bool,
	 *   license: 'valid'|'invalid'|'expired'|'site_inactive'|'item_name_mismatch',
	 *   item_id: false,
	 *   item_name: 'Gravity+Connect',
	 *   checksum: string,
	 *   expires: string|'lifetime',
	 *   payment_id: int,
	 *   customer_name: string,
	 *   customer_email: string,
	 *   license_limit: int,
	 *   site_count: int,
	 *   activations_left: int|'unlimited',
	 *   price_id: string,
	 *   ID: int,
	 *   price_name: string,
	 *   valid: bool,
	 *   registered_connections: array<string>,
	 *   connection_limit: int
	 *   extend_url: string
	 *   manage_url: string
	 *   upgrade_url: string
	 * }|array{
	 *   success: bool,
	 *   license: 'valid'|'invalid'|'expired'|'site_inactive'|'item_name_mismatch',
	 *   item_id: false,
	 *   item_name: 'GS+Product+Configurator',
	 *   checksum: string,
	 *   expires: string|'lifetime',
	 *   payment_id: int,
	 *   customer_name: string,
	 *   customer_email: string,
	 *   license_limit: int,
	 *   site_count: int,
	 *   activations_left: int|'unlimited',
	 *   price_id: string,
	 *   ID: int,
	 *   price_name: string,
	 *   valid: bool
	 *   extend_url: string
	 *   manage_url: string
	 *   upgrade_url: string
	 * }|array{
	 *   success: bool,
	 *   license: 'valid'|'invalid'|'expired'|'site_inactive'|'item_name_mismatch',
	 *   item_id: false,
	 *   item_name: 'Wiz+Bundle',
	 *   checksum: string,
	 *   expires: string|'lifetime',
	 *   payment_id: int,
	 *   customer_name: string,
	 *   customer_email: string,
	 *   license_limit: int,
	 *   site_count: int,
	 *   activations_left: int|'unlimited',
	 *   price_id: string,
	 *   ID: int,
	 *   price_name: string,
	 *   valid: bool,
	 *   registered_plugins: array<string>,
	 *   plugin_limit: int
	 *   extend_url: string
	 *   manage_url: string
	 *   upgrade_url: string
	 * }
	 * @param string $product_type
	 * @return array
	 */
	private function prepare_license_for_response($license_data, $product_type) {
		$registered_products = null;
		$registered_products_limit = null;

		// Handle different registration types based on product configuration
		$registration_type = $this->api->get_registration_type($product_type);

		switch ($registration_type) {
			case 'plugin':
				// Bundle licenses use registered_plugins + plugin_limit
				$registered_products = $license_data['registered_plugins'] ?? [];
				$registered_products_limit = $license_data['plugin_limit'] ?? 0;
				break;

			case 'category':
			default:
				// Individual licenses use category-specific arrays
				if ($product_type === 'perk') {
					$registered_products = $license_data['registered_perks'] ?? [];
					$registered_products_limit = $license_data['perk_limit'] ?? 0;
				} elseif ($product_type === 'connect') {
					$registered_products = $license_data['registered_connections'] ?? [];
					$registered_products_limit = $license_data['connection_limit'] ?? 0;
				}
				break;
		}

		return [
			'key' => $this->api->get_license_key($product_type),
			'status' => $license_data['license'],
			'registered_products' => $registered_products,
			'registered_products_limit' => $registered_products_limit,
			'valid' => $license_data['valid'],
			'product_type' => $product_type,
			'is_bundle' => $this->api->is_bundle_type($product_type),
			'type' => rgar( $license_data, 'price_name' ),
			'expiration' => rgar( $license_data, 'expires' ),
			'site_count' => rgar( $license_data, 'site_count' ),
			'site_limit' => rgar( $license_data, 'license_limit' ),
			'manage_url' => rgar( $license_data, 'manage_url' ),
			'extend_url' => rgar( $license_data, 'extend_url' ),
			'upgrade_url' => rgar( $license_data, 'upgrade_url' ),
			'gcgs_eligible' => $product_type === 'perk' ? rgar( $license_data, 'gcgs_eligible' ) : null,
		];
	}

	/**
	 * Format error response
	 */
	private function format_error($code, $message, $status = 400) {
		return new WP_Error($code, $message, ['status' => $status]);
	}
}
