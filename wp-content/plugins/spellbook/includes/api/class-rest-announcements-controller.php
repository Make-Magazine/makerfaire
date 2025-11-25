<?php

class GravityPerks_REST_Announcements_Controller extends WP_REST_Controller {
	protected $namespace = 'gwiz/v1';
	protected $rest_base = 'announcements';
	private $api;

	public function __construct() {
		$this->api = GWPerks::get_api();
	}

	public function register_routes() {
		// GET /announcements - Get all announcements
		register_rest_route($this->namespace, '/' . $this->rest_base, [
			'methods' => WP_REST_Server::READABLE,
			'callback' => [$this, 'get_announcements'],
			'permission_callback' => [$this, 'check_permission'],
			'args' => [
				'force' => [
					'required' => false,
					'type' => 'boolean',
					'default' => false,
				],
			],
		]);

		// POST /announcements/{id}/dismiss - Dismiss an announcement
		register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<id>[\w-]+)/dismiss', [
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => [$this, 'dismiss_announcement'],
			'permission_callback' => [$this, 'check_permission'],
			'args' => [
				'id' => [
					'required' => true,
					'type' => 'string',
				],
			],
		]);
	}

	/**
	 * Get all announcements with conditional filtering
	 */
	public function get_announcements($request) {
		$force = $request->get_param('force');

		// Get system context for conditional filtering
		$context = $this->get_system_context();

		// Use GWAPI's get_spellbook_announcements method with built-in caching
		$announcements = $this->api->get_spellbook_announcements($force);

		// If API request fails, return empty announcements
		if (!$announcements || !isset($announcements['announcements'])) {
			return rest_ensure_response(['announcements' => []]);
		}

		return rest_ensure_response($this->filter_announcements($announcements, $context));
	}

	/**
	 * Dismiss an announcement
	 */
	public function dismiss_announcement($request) {
		$id = $request->get_param('id');
		$dismissed = get_option('gwp_dismissed_announcements', []);

		if (!in_array($id, $dismissed)) {
			$dismissed[] = $id;
			update_option('gwp_dismissed_announcements', $dismissed);
		}

		return rest_ensure_response([
			'success' => true,
			'dismissed' => $dismissed,
		]);
	}

	/**
	 * Get system context for conditional filtering
	 */
	private function get_system_context() {
		return [
			'php_version' => PHP_VERSION,
			'wp_version' => get_bloginfo('version'),
			'gf_version' => class_exists('GFForms') ? GFForms::$version : null,
			'spellbook_version' => SPELLBOOK_VERSION,
			'has_perks_license' => $this->api->has_valid_license(false, 'perk'),
			'has_connect_license' => $this->api->has_valid_license(false, 'connect'),
			'has_shop_license' => $this->api->has_valid_license(false, 'shop'),
			'has_wiz_bundle_license' => $this->api->has_valid_license(false, 'wiz-bundle'),
		];
	}

	/**
	 * Filter announcements based on conditions and dismissal status
	 */
	private function filter_announcements($data, $context) {
		if (!isset($data['announcements']) || !is_array($data['announcements'])) {
			return ['announcements' => []];
		}

		$dismissed = get_option('gwp_dismissed_announcements', []);
		$current_time = current_time('timestamp');

		$filtered = array_filter($data['announcements'], function($announcement) use ($dismissed, $current_time, $context) {
			// Filter out dismissed announcements
			if (in_array($announcement['id'], $dismissed)) {
				return false;
			}

			// Filter by date range
			if ( ! empty( $announcement['start_date'] ) ) {
				$start = strtotime($announcement['start_date']);

				if ( $current_time < $start ) {
					return false;
				}
			}

			if ( ! empty( $announcement['end_date'] ) ) {
				$end = strtotime($announcement['end_date']);

				if ( $current_time > $end ) {
					return false;
				}
			}

			// Filter by conditions
			if (isset($announcement['conditions'])) {
				if (!$this->evaluate_conditions($announcement['conditions'], $context)) {
					return false;
				}
			}

			return true;
		});

		return ['announcements' => array_values($filtered)];
	}

	/**
	 * Evaluate announcement conditions against system context
	 */
	private function evaluate_conditions($conditions, $context) {
		if (empty($conditions) || !is_array($conditions)) {
			return true;
		}

		foreach ($conditions as $key => $value) {
			switch ($key) {
				case 'php_version':
				case 'wp_version':
				case 'gf_version':
				case 'spellbook_version':
					if (!$this->compare_versions($context[$key], $value)) {
						return false;
					}
					break;

				case 'has_perks_license':
				case 'has_connect_license':
				case 'has_shop_license':
				case 'has_wiz_bundle_license':
					if ($context[$key] !== $value) {
						return false;
					}
					break;
			}
		}

		return true;
	}

	/**
	 * Compare versions with operator support
	 */
	private function compare_versions($current_version, $requirement) {
		if ($current_version === null) {
			return false;
		}

		// Parse operator and version from requirement (e.g., ">=7.4", "<3.0", "2.5")
		preg_match('/^([<>=!]+)?(.+)$/', $requirement, $matches);
		$operator = !empty($matches[1]) ? $matches[1] : '>=';
		$required_version = $matches[2];

		return version_compare($current_version, $required_version, $operator);
	}

	/**
	 * Check if user has permission to access endpoints
	 */
	public function check_permission($request) {
		return current_user_can('manage_options');
	}
}
