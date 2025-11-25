<?php

class GW_Telemetry {
	/**
	 * @var string The endpoint to send telemetry data to.
	 */
	private $endpoint = 'https://in.gravitywiz.com/v1/telemetry';

	/**
	 * @var string The cron hook used to schedule the telemetry send.
	 */
	private $schedule_hook = 'spellbook_telemetry_send';

	/**
	 * The option name used to store the last sent timestamp.
	 *
	 * @var string
	 */
	private $option_name = 'spellbook_telemetry_last_sent';

	/**
	 * @var int The minimum interval between telemetry sends. This is intentionally smaller than the schedule interval
	 *   on the off-chance the cron were to roll and the passed time would be less than the schedule interval.
	 */
	private $min_interval = 6 * DAY_IN_SECONDS;

	public function __construct() {
		if ( $this->is_enabled() ) {
			add_action( 'init', array( $this, 'schedule_send' ), 15 );
			add_action( $this->schedule_hook, array( $this, 'maybe_send' ) );
		}
	}

	public function get_data() {
		$form_counts = GFFormsModel::get_form_count();
		$active_count = $form_counts['active'];
		$inactive_count = $form_counts['inactive'];
		$fc = abs( $active_count ) + abs( $inactive_count );
		$entry_count = GFFormsModel::get_entry_count_all_forms( 'active' );
		$meta_counts = GFFormsModel::get_entry_meta_counts();

		$data = array(
			// Basic Site Info
			'site_url'     => get_site_url(),
			'php_version'  => phpversion(),
			'env'          => defined( 'WP_ENV' ) ? WP_ENV : '',
			'wp_version'   => get_bloginfo( 'version' ),
			'is_multisite' => is_multisite(),
			'lang'         => get_locale(),
			'db_version'   => $this->get_db_version(),

			// Theme Info
			'theme'        => $this->get_theme_info(),

			// Plugin Info
			'plugins'      => $this->get_plugins(),

			// Spellbook-specific
			// TODO entered_free_plugins_email

			// Licenses
			'licenses'     => array(
				'perks'   => array(
					'key'       => GWPerks::get_api()->get_license_key( 'perk' ),
					'is_active' => $this->has_active_license( 'perk' ),
				),
				'connect' => array(
					'key'       => GWPerks::get_api()->get_license_key( 'connect' ),
					'is_active' => $this->has_active_license( 'connect' ),
				),
				'shop'    => array(
					'key'       => GWPerks::get_api()->get_license_key( 'shop' ),
					'is_active' => $this->has_active_license( 'shop' ),
				),
				'wiz-bundle'    => array(
					'key'       => GWPerks::get_api()->get_license_key( 'wiz-bundle' ),
					'is_active' => $this->has_active_license( 'wiz-bundle' ),
				)
			),

			// Generic Usage
			'usage'        => array(
				'total_forms'         => (int) $fc,
				'total_entries'       => (int) $entry_count,
				'entry_meta_count'    => (int) $meta_counts['meta'],
				'entry_details_count' => (int) $meta_counts['details'],
				'entry_notes_count'   => (int) $meta_counts['notes'],
			),
		);

		return $data;
	}

	public function maybe_send() {
		$last_sent = get_site_option( $this->option_name, 0 );

		// Only send if enough time has passed
		if ( time() - $last_sent >= $this->min_interval ) {
			$this->send();
			update_site_option( $this->option_name, time() );
		}
	}

	private function send() {
		wp_remote_post( $this->endpoint, array(
			'body'    => wp_json_encode( $this->get_data() ),
			'headers' => array(
				'Content-Type' => 'application/json',
			),
			'timeout' => 15,
		) );
	}

	public function schedule_send() {
		if ( ! wp_next_scheduled( $this->schedule_hook ) ) {
			wp_schedule_event( time(), 'weekly', $this->schedule_hook );
		}
	}

	private function is_enabled() {
		$enabled = false;
		$api     = GravityPerks::get_api();

		// Enable if we have a license key or registered email.
		$email = get_option( 'gwp_spellbook_email' );

		if ( empty( $email ) ) {
			// License keys
			$licensed_products = array_filter( GWAPI::$product_config, function ($product) {
				return ! isset( $product['has_license'] ) || $product['has_license'];
			} );

			foreach ( $licensed_products as $product_type => $product ) {
				$license_key = $api->get_license_key( $product_type );
				if ( ! empty( $license_key ) ) {
					$enabled = true;
					break;
				}
			}
		} else {
			$enabled = true;
		}

		return apply_filters( 'spellbook_enable_telemetry', $enabled );
	}

	private function get_theme_info() {
		$theme = wp_get_theme();
		return array(
			'name'    => $theme->get( 'Name' ),
			'version' => $theme->get( 'Version' ),
			'author'  => $theme->get( 'Author' )
		);
	}

	private function get_plugins() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$active_plugins = get_option( 'active_plugins' );
		$plugins = array();

		foreach ( get_plugins() as $key => $plugin ) {
			if ( ! in_array( $key, $active_plugins ) ) {
				continue;
			}

			$slug = substr( $key, 0, strpos( $key, '/' ) ) ?: str_replace( '.php', '', $key );
			$plugins[] = array(
				'name'      => $plugin['Name'],
				'slug'      => $slug,
				'version'   => $plugin['Version'],
				'is_active' => in_array( $key, $active_plugins ),
			);
		}

		return $plugins;
	}

	private function has_active_license( $type ) {
		$license_key = GWPerks::get_api()->get_license_key( $type );

		if ( empty( $license_key ) ) {
			return false;
		}

		$license_data = GravityPerks::get_api()->get_license_data( $type );

		if ( empty( $license_data ) || empty( $license_data['license'] ) ) {
			return false;
		}

		return $license_data['valid'];
	}

	private function get_db_version() {
		global $wpdb;
		return $wpdb->db_version();
	}
}
