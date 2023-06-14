<?php

namespace GravityKit\GravityMaps;

use GravityKit\GravityMaps\Foundation\Helpers\Arr;
use GravityKit\GravityMaps\Foundation\Helpers\Core as CoreHelpers;
use GravityKitFoundation;
use Exception;

/**
 * Manages plugin settings
 */
class Settings extends Component {
	/**
	 * @var string Unique reference name for nonce and UI assets
	 */
	const UNIQUE_HANDLE = 'gk-gravitymaps';

	/**
	 * @var string Key used to store plugin settings
	 */
	const PLUGIN_SETTINGS_ID = 'gravitymaps';

	/**
	 * @var string AJAX action to verify key
	 */
	const AJAX_ROUTE_VERIFY_API_KEY = 'verify_api_key';

	/**
	 * @var array Geocoding provider information: [settings key] => name of constant
	 */
	private $providers = [];

	/**
	 * @since 1.8
	 *
	 * @var SettingsFramework GravityKit Settings instance.
	 */
	public $gk_settings = [];

	function load() {
		$this->gk_settings = GravityKitFoundation::get_instance()->settings();

		$this->providers = $this->get_providers_api_keys();

		$router = self::UNIQUE_HANDLE;

		add_filter( 'gk/foundation/settings/data/plugins', [ $this, 'register_settings' ] );
		add_filter( 'gk/foundation/settings/' . self::PLUGIN_SETTINGS_ID . '/validation/after', [ $this, 'save_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ], 1 );
		add_filter( "gk/foundation/ajax/{$router}/routes", function ( $routes ) {
			return array_merge( $routes, [
				self::AJAX_ROUTE_VERIFY_API_KEY => [ $this, 'verify_api_key' ]
			] );
		} );
	}

	/**
	 * Get an array of geocoding providers with their API keys
	 *
	 * If constants are set for the provider, uses that value.
	 *
	 * @see   https://docs.gravityview.co/article/304-setting-up-geocoding-services
	 *
	 * Can be overridden using filters:
	 * add_filter( 'gravityview/maps/geocoding/providers/mapquest-api-key/api_key', function() { return 'example'; } );
	 *
	 * @since 1.8 Refactored to work with GravityKit Settings
	 * @since 1.7
	 *
	 * @param array Providers with keys set to setting key name, values set to API key values (empty string if not set)
	 */
	public function get_providers_api_keys() {
		// Don't process after initialization.
		if ( ! empty( $this->providers ) ) {
			return $this->providers;
		}

		$plugin_settings = $this->gk_settings->get_plugin_settings( self::PLUGIN_SETTINGS_ID );

		$providers = [];

		$settings = [
			'google_maps/key',
			'google_maps/key_unrestricted',
			'google_maps/business/client_id',
			'google_maps/business/key',
			'bing_maps/key',
			'mapquest/key'
		];

		foreach ( $settings as $setting ) {
			// Google Maps API key/unrestricted key is retrieved in a separate method that runs additional checks (e.g., if it is a network subsite and the key is shared by the network administrator)/
			if ( in_array( $setting, [ 'google_maps/key', 'google_maps/key_unrestricted' ] ) ) {
				$key = $this->get_google_maps_api_key_setting( $setting );
			} else {
				// For all other keys, we first check if it is overridden by a constant and only then get it from the DB settings.
				$key = $this->get_setting_from_constant( $setting );
				$key = $key ?: Arr::get( $plugin_settings, $setting );
			}

			// Migrate legacy settings only for non-MS sites or the main network site (GravityView settings were not available on network subsites).
			if ( is_null( $key ) && ( ! is_multisite() || CoreHelpers::is_main_network_site() ) ) {
				$key = $this->get_and_migrate_legacy_setting( $setting );
			}

			/**
			 * @filter  Modifies the API key used for a geocoding provider
			 *
			 * @since   1.7
			 *
			 * @TODO    Refactor this filter (e.g., 'gk/gravitymaps/geocoding/[provider]/keys/[key]')
			 *
			 * @param string $key API key pulled from GravityView Maps settings or a constant
			 */
			$key = apply_filters( 'gravityview/maps/geocoding/providers/' . $setting . '/api_key', trim( $key ?: '' ) );

			$providers[ $setting ] = $key;
		}

		return $providers;
	}

	/**
	 * Migrates and returns a legacy setting.
	 *
	 * @since 1.8
	 *
	 * @param string $setting
	 *
	 * @return array|null
	 */
	public function get_and_migrate_legacy_setting( $setting ) {
		static $legacy_plugin_settings = null;

		if ( ! $legacy_plugin_settings ) {
			$legacy_plugin_settings = get_option( 'gravityformsaddon_gravityview_app_settings' );
		}

		$current_to_legacy_setting_map = [
			'google_maps/key'                => 'googlemaps-api-key',
			'google_maps/key_unrestricted'   => '',
			'google_maps/business/client_id' => 'googlemapsbusiness-api-clientid',
			'google_maps/business/key'       => 'googlemapsbusiness-api-key',
			'bing_maps/key'                  => 'bingmaps-api-key',
			'mapquest/key'                   => 'mapquest-api-key',
		];

		$legacy_setting_value = Arr::get( $legacy_plugin_settings, $current_to_legacy_setting_map[ $setting ], $this->get_default_settings( $setting ) );

		$this->gk_settings->save_plugin_setting( self::PLUGIN_SETTINGS_ID, $setting, $legacy_setting_value );

		return $legacy_setting_value;
	}

	/**
	 * Gets setting from constant.
	 *
	 * @since 1.8
	 *
	 * @param string $setting
	 *
	 * @return string|null
	 */
	public function get_setting_from_constant( $setting ) {
		$setting_to_legacy_constant_map = [
			'google_maps/key'                => 'GRAVITYVIEW_GOOGLEMAPS_KEY',
			'google_maps/business/client_id' => 'GRAVITYVIEW_GOOGLEBUSINESSMAPS_CLIENTID',
			'google_maps/business/key'       => 'GRAVITYVIEW_GOOGLEBUSINESSMAPS_KEY',
			'bing_maps/key'                  => 'GRAVITYVIEW_BING_KEY',
			'mapquest/key'                   => 'GRAVITYVIEW_MAPQUEST_KEY'
		];

		// New constant takes precedence, followed by the legacy constant.
		if ( defined( 'GRAVITYMAPS_KEYS' ) && Arr::get( GRAVITYMAPS_KEYS, $setting ) ) {
			return Arr::get( GRAVITYMAPS_KEYS, $setting );
		} else if ( isset( $setting_to_legacy_constant_map[ $setting ] ) && defined( $setting_to_legacy_constant_map[ $setting ] ) ) {
			return constant( $setting_to_legacy_constant_map[ $setting ] );
		} else {
			return null;
		}
	}

	/**
	 * Returns setting for the Google Maps provider based conditional logic (e.g., key sharing on network sites).
	 *
	 * @param string $setting Setting key (e.g., google_maps/key).
	 *
	 * @return array|null|string
	 */
	public function get_google_maps_api_key_setting( $setting ) {
		$plugin_settings = $this->gk_settings->get_plugin_settings( self::PLUGIN_SETTINGS_ID );

		// No further processing is needed for a non-MS site or the main network site.
		if ( ! is_multisite() || ! CoreHelpers::is_not_main_network_site() ) {
			$setting_value = $this->get_setting_from_constant( $setting ) ?: Arr::get( $plugin_settings, $setting );

			if ( 'google_maps/key_unrestricted' === $setting ) {
				$setting_value = $this->decrypt_key( $setting_value );
			}

			return $setting_value;
		}

		$network_settings = $this->gk_settings->get_plugin_settings( self::PLUGIN_SETTINGS_ID, get_main_site_id() );

		// Main network site setting.
		$share_key_with_network = Arr::get( $network_settings, 'google_maps/share_key_with_network', $this->get_default_settings( 'google_maps/share_key_with_network' ) );
		// Subsite setting (only turned on if the main network site setting is on).
		$use_network_key = $share_key_with_network ? Arr::get( $plugin_settings, 'google_maps/use_network_key', $this->get_default_settings( 'google_maps/use_network_key' ) ) : false;

		// Where to get the settings from.
		$settings = $use_network_key ? $network_settings : $plugin_settings;

		$setting_override = $use_network_key ? $this->get_setting_from_constant( $setting ) : null;

		if ( 'google_maps/key_unrestricted' === $setting ) {
			$use_unrestricted_key = Arr::get( $settings, 'google_maps/use_unrestricted_key' );

			$key = $use_unrestricted_key
				? $this->decrypt_key( Arr::get( $settings, 'google_maps/key_unrestricted' ) )
				: $this->get_default_settings( $setting );

			return $setting_override ?: $key;
		}

		return $setting_override ?: Arr::get( $settings, $setting );
	}

	/**
	 * Gets the default settings.
	 *
	 * @since 1.8
	 *
	 * @param string|null $setting (optional) Setting to return. Defaults to all settings.
	 *
	 * @return array|mixed|null
	 */
	public function get_default_settings( $setting = null ) {
		$default_settings = [
			'google_maps/key'                    => '',
			'google_maps/share_key_with_network' => 1,
			'google_maps/use_network_key'        => 1,
			'google_maps/use_unrestricted_key'   => 0,
			'google_maps/key_unrestricted'       => '',
			'google_maps/business/client_id'     => '',
			'google_maps/business/key'           => '',
			'bing_maps/key'                      => '',
			'mapquest/key'                       => '',
		];

		return $setting ? Arr::get( $default_settings, $setting ) : $default_settings;
	}

	/**
	 * Encrypts a key (e.g., "unrestricted" Google Maps API key).
	 *
	 * @since $ver$
	 *
	 * @param mixed $key
	 *
	 * @return null|string
	 */
	public function encrypt_key( $key ) {
		return GravityKitFoundation::encryption()->encrypt( $key );
	}

	/**
	 * Decrypts a key (e.g., "unrestricted" Google Maps API key).
	 *
	 * @since $ver$
	 *
	 * @param mixed $key
	 * @param bool  $key (optional) Mask the decrypted key before returning it. Defaults to false.
	 *
	 * @return null|string
	 */
	public function decrypt_key( $key, $mask = false ) {
		if ( is_null( $key ) ) {
			return null;
		}

		if ( '' === $key ) {
			return '';
		}

		$decrypted_key = GravityKitFoundation::encryption()->decrypt( $key ) ?: $key;

		return $mask ? $this->mask_key( $decrypted_key ) : $decrypted_key;
	}

	/**
	 * Masks part of the key.
	 *
	 * @since $ver$
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public function mask_key( $key ) {
		$length        = strlen( $key );
		$visible_count = (int) round( $length / 8 );
		$hidden_count  = $length - ( $visible_count * 4 );

		return sprintf( '%s%s%s',
			substr( $key, 0, $visible_count ),
			str_repeat( '✽', $hidden_count ),
			substr( $key, ( $visible_count * -1 ), $visible_count )
		);
	}

	public function save_settings( $settings ) {
		$unrestricted_key = Arr::get( $settings, 'google_maps/key_unrestricted' );

		if ( ! $unrestricted_key ) {
			return $settings;
		}

		$unrestricted_key = ( $unrestricted_key === $this->providers['google_maps/key_unrestricted'] || $unrestricted_key === $this->mask_key( $this->providers['google_maps/key_unrestricted'] ) )
			? $this->providers['google_maps/key_unrestricted']
			: $unrestricted_key;

		$settings['google_maps/key_unrestricted'] = $this->encrypt_key( $unrestricted_key );

		return $settings;
	}

	/**
	 * Add GravityView Maps settings
	 *
	 * @since 1.8 Refactored to work with GravityKit Settings
	 *
	 * @param array $gk_settings GravityKit plugins settings.
	 *
	 * @return array
	 */
	public function register_settings( $gk_settings ) {
		$plugin_settings = wp_parse_args( $this->gk_settings->get_plugin_settings( self::PLUGIN_SETTINGS_ID ), $this->get_default_settings() );

		$network_db_settings = wp_parse_args( $this->gk_settings->get_plugin_settings( self::PLUGIN_SETTINGS_ID, get_main_site_id() ), $this->get_default_settings() );

		$google_maps_settings = [];

		$share_key_with_network = Arr::get( $network_db_settings, 'google_maps/share_key_with_network' );

		$unrestricted_key = ! CoreHelpers::is_not_main_network_site()
			// The key for a non-MS site or the main network site can be overridden by a constant.
			? ( $this->get_setting_from_constant( 'google_maps/key_unrestricted' ) ?: Arr::get( $plugin_settings, 'google_maps/key_unrestricted' ) )
			: Arr::get( $plugin_settings, 'google_maps/key_unrestricted', '' );

		$unrestricted_key = $this->decrypt_key( $unrestricted_key, true );

		if ( CoreHelpers::is_not_main_network_site() && $share_key_with_network ) {
			$notice = esc_html__( 'The Network Administrator has enabled Google Maps API key(s) for this site. You can use the shared key(s) or specify your own.', 'gk-gravitymaps' );

			$notice = <<<HTML
<div class="bg-yellow-50 p-4">
	<div class="flex">
		<div class="flex-shrink-0">
			<svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
				<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
			</svg>
		</div>
	    <div class="ml-3">
			<p class="text-sm text-yellow-700">
			{$notice}
			</p>
		</div>
	</div>
</div>
HTML;

			$google_maps_settings = [
				[
					'id'              => 'google_maps/network-key-notice',
					'html'            => $notice,
					'excludeFromSave' => true,
				],
				[
					'id'    => 'google_maps/use_network_key',
					'type'  => 'checkbox',
					'value' => Arr::get( $plugin_settings, 'google_maps/use_network_key' ),
					'title' => esc_html__( 'Use Shared Key(s)', 'gk-gravitymaps' ),
				]
			];
		}

		$google_maps_settings = array_merge( $google_maps_settings, [
				array_merge(
					array_merge(
						[
							'id'          => 'google_maps/key',
							'type'        => 'text',
							'component'   => 'gk-settings-google-maps-api-key',
							'title'       => esc_html__( 'Google Maps API Key', 'gk-gravitymaps' ),
							'description' => strtr(
								                 esc_html_x( 'GravityView will attempt to convert addresses into longitude and latitude values. This process is called geocoding, and is required to display entries on a map. [link]Learn more about GravityView Maps geocoding[/link].', 'Placeholders inside [] are not to be translated.', 'gk-gravitymaps' ),
								                 [
									                 '[link]'  => '<a href="https://docs.gravitykit.com/article/304-setting-up-geocoding-services" class="gk-link" data-beacon-article-modal="56057b6dc6979105f62b0216">',
									                 '[/link]' => '</a>'
								                 ]
							                 ) . '<br><br><span class="dashicons dashicons-info"></span> <a href="https://docs.gravitykit.com/article/306-signing-up-for-a-google-maps-api-key" class="gk-link" data-beacon-article-modal="5605872bc6979105f62b023a">' . esc_html__( 'How to get a Google Maps API key', 'gk-gravitymaps' ) . '</a>',
							'required'    => true,
							'value'       => ! CoreHelpers::is_not_main_network_site()
								// The key for a non-MS site or the main network site can be overridden by a constant.
								? ( $this->get_setting_from_constant( 'google_maps/key' ) ?: Arr::get( $plugin_settings, 'google_maps/key' ) )
								: Arr::get( $plugin_settings, 'google_maps/key', '' ),
							'ajaxRoute'   => self::AJAX_ROUTE_VERIFY_API_KEY,
						],
						( $share_key_with_network && CoreHelpers::is_not_main_network_site() ) ? [
							'requires' => [
								'id'       => 'google_maps/use_network_key',
								'operator' => '!=',
								'value'    => '1',
							]
						] : []
					), GravityKitFoundation::get_ajax_params( self::UNIQUE_HANDLE ) ),
				array_merge(
					[
						'id'          => 'google_maps/use_unrestricted_key',
						'type'        => 'checkbox',
						'value'       => Arr::get( $plugin_settings, 'google_maps/use_unrestricted_key', 0 ),
						'title'       => esc_html__( 'Use Unrestricted Key For Geocoding', 'gk-gravitymaps' ),
						'description' => strtr(
							esc_html_x( 'Geocoding will not work if you have HTTP referer or [link]other restrictions[/link] enabled for the API key. Enable this option to use an unrestricted key just for geocoding. This key will never be exposed to site visitors.', 'Placeholders inside [] are not to be translated.', 'gk-gravitymaps' ),
							[
								'[link]'  => '<a class="gk-link" href="https://developers.google.com/maps/api-security-best-practices">',
								'[/link]' => '</a>'
							]
						),
					],
					( $share_key_with_network && CoreHelpers::is_not_main_network_site() ) ? [
						'requires' => [
							'id'       => 'google_maps/use_network_key',
							'operator' => '!=',
							'value'    => '1',
						]
					] : []
				),
				array_merge( [
					'id'        => 'google_maps/key_unrestricted',
					'type'      => 'text',
					'component' => 'gk-settings-google-maps-api-key',
					'title'     => esc_html__( 'Unrestricted Google Maps API Key', 'gk-gravitymaps' ),
					'required'  => true,
					'value'     => $unrestricted_key,
					'ajaxRoute' => self::AJAX_ROUTE_VERIFY_API_KEY,
					'requires'  => [
						'id'       => 'google_maps/use_unrestricted_key',
						'operator' => '=',
						'value'    => '1',
					],
				], GravityKitFoundation::get_ajax_params( self::UNIQUE_HANDLE ) )
				// NOT CURRENTLY USED
				/*array(
					'id'          => 'googlemapsbusiness-api-clientid',
					'type'        => 'text',
					'title'       => esc_html__( 'Google Maps API for Work Key', 'gk-gravitymaps' ),
					'description' => sprintf( esc_html__( 'Read more about [how to obtain](%s) the key.', 'gk-gravitymaps' ), 'https://developers.google.com/maps/documentation/business/' ),
					'required'    => true,
					'value'       => $this->providers['google_maps_business_api_client_id']
				),
				array(
					'id'          => 'googlemapsbusiness-api-key',
					'type'        => 'text',
					'title'       => esc_html__( 'Google Maps API for Work Client ID', 'gk-gravitymaps' ),
					'description' => sprintf( esc_html__( 'Read more about [how to obtain](%s) the key.', 'gk-gravitymaps' ), 'https://developers.google.com/maps/documentation/business/' ),
					'required'    => true,
					'value'       => $this->providers['googlemapsbusiness-api-key']
				),*/
			]
		);

		if ( CoreHelpers::is_main_network_site() ) {
			$google_maps_settings = array_merge( $google_maps_settings, [
				[
					'id'          => 'google_maps/share_key_with_network',
					'type'        => 'checkbox',
					'value'       => $share_key_with_network,
					'title'       => esc_html__( 'Share Key With Network Subsites', 'gk-gravitymaps' ),
					'description' => esc_html__( 'Enable this option to share your Google Maps API key(s) with network subsites. Subsite administrators will have an option to use your key(s) or specify their own.', 'gk-gravitymaps' ),
				]
			] );
		}

		$sections = [
			[
				'title'    => esc_html__( 'Google Maps', 'gk-gravitymaps' ),
				'settings' => $google_maps_settings,
			]
			// NOT CURRENTLY USED
			/*,
					array(
						'title'    => 'Bing Maps',
						'settings' => array(
							array(
								'id'          => 'bingmaps-api-key',
								'type'        => 'text',
								'title'       => esc_html__( 'Bing Maps Locations API Key', 'gk-gravitymaps' ),
								'description' => sprintf( esc_html__( 'Read more about [how to obtain](%s) the key.', 'gk-gravitymaps' ), esc_html__( 'https://docs.gravityview.co/article/307-signing-up-for-a-bing-maps-api-key' ) ),
								'required'    => true,
								'value'       => $this->providers['bingmaps-api-key']
							),
						)
					),
					array(
						'title'    => 'MapQuest',
						'settings' => array(
							array(
								'id'          => 'mapquest-api-key',
								'type'        => 'text',
								'title'       => esc_html__( 'MapQuest Geocoding API Key', 'gk-gravitymaps' ),
								'description' => sprintf( esc_html__( 'Read more about [how to obtain](%s) the key.', 'gk-gravitymaps' ), 'https://docs.gravityview.co/article/305-signing-up-for-a-mapquest-geocoding-api-key' ),
								'required'    => true,
								'value'       => $this->providers['mapquest-api-key']
							),
						)
					),*/
		];

		// `gravityview_maps` key will ensure that this menu item is added after `gravityview` (GravityView) during ksort()
		$gk_settings['gravityview_maps'] = [
			'id'       => self::PLUGIN_SETTINGS_ID,
			'title'    => esc_html__( 'GravityView Maps', 'gk-gravitymaps' ),
			'icon'     => 'data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iODAiIGhlaWdodD0iODAiPjxzdHlsZT4uc3Qwe2ZpbGw6bm9uZTtzdHJva2U6I2ZmMWI2NztzdHJva2Utd2lkdGg6My44MTk1O3N0cm9rZS1saW5lY2FwOnJvdW5kO3N0cm9rZS1saW5lam9pbjpyb3VuZDtzdHJva2UtbWl0ZXJsaW1pdDoxMH0uc3Qxe2ZpbGw6I2ZmMWI2N308L3N0eWxlPjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik00MCA1OS4xVjU0bTIwLjQtMzMuMWgyLjVjMS40IDAgMi42LjUgMy42IDEuNVM2OCAyNC43IDY4IDI2djM1LjZjMCAxLjQtLjUgMi42LTEuNSAzLjZzLTIuMyAxLjUtMy42IDEuNUgxNy4xYy0xLjQgMC0yLjYtLjUtMy42LTEuNVMxMiA2Mi45IDEyIDYxLjZWMjZjMC0xLjQuNS0yLjYgMS41LTMuNnMyLjMtMS41IDMuNi0xLjVoMy44Ii8+PHBhdGggY2xhc3M9InN0MCIgZD0iTTYwLjQgMjkuOHYyOS4zSDE5LjZWMjkuOG0wIDI5LjNsMTUuNC0xOG0yNS40IDE4TDQ1IDQxLjEiLz48cGF0aCBjbGFzcz0ic3QwIiBkPSJNNTIuNyAyNmMwIDcuOS0xMi43IDIwLjctMTIuNyAyMC43UzI3LjMgMzMuOSAyNy4zIDI2YzAtMy40IDEuMy02LjYgMy43LTkgMi40LTIuNCA1LjYtMy43IDktMy43czYuNiAxLjMgOSAzLjdjMi40IDIuNCAzLjcgNS42IDMuNyA5eiIvPjxwYXRoIGNsYXNzPSJzdDEiIGQ9Ik00MCAyOC41YzEuNCAwIDIuNS0xLjEgMi41LTIuNXMtMS4xLTIuNS0yLjUtMi41LTIuNSAxLjEtMi41IDIuNSAxLjEgMi41IDIuNSAyLjV6Ii8+PC9zdmc+',
			'sections' => $sections,
			'defaults' => $this->get_default_settings(),
		];

		return $gk_settings;
	}

	/**
	 * Define and localize UI assets
	 *
	 * @since 1.8 Added $page param
	 *
	 * @param string $page Requested page ID.
	 *
	 * @return void
	 */
	function enqueue_scripts( $page ) {
		if ( strpos( $page, constant( get_class( $this->gk_settings ) . '::ID' ) ) === false ) {
			return;
		}

		$asset_name = 'gravitymaps-admin-settings';

		wp_enqueue_script(
			self::UNIQUE_HANDLE . '_js',
			"{$this->loader->js_url}{$asset_name}.js",
			[],
			$this->loader->plugin_version, true
		);

		wp_enqueue_style(
			self::UNIQUE_HANDLE . '_css',
			"{$this->loader->css_url}{$asset_name}.css",
			[],
			$this->loader->plugin_version, false
		);
	}

	/**
	 * Fetch and cache address field coordinates.
	 *
	 * @since 1.8 Added $data method parameter; POST object is no longer used.
	 *
	 * @param array $data API key and other data.
	 *
	 * @throws Exception
	 *
	 * @return void Exit with JSON response or terminate request with an error code.
	 */
	public function verify_api_key( $data ) {
		$api_key = Arr::get( $data, 'api_key' );
		$id      = Arr::get( $data, 'id' );

		if ( '' === $api_key ) {
			throw new Exception( esc_html__( 'API key is missing', 'gk-gravitymaps' ) );
		}

		if ( 'google_maps/key_unrestricted' === $id && ! empty( $this->providers['google_maps/key_unrestricted'] ) ) {
			$api_key = ( $api_key === $this->providers['google_maps/key_unrestricted'] || $api_key === $this->mask_key( $this->providers['google_maps/key_unrestricted'] ) )
				? $this->providers['google_maps/key_unrestricted']
				: $api_key;
		}

		$http_adapter = new HTTP_Adapter();

		try {
			$address     = sprintf( \GravityKit\GravityMaps\Geocoder\Provider\GoogleMapsProvider::ENDPOINT_URL_SSL, 'Paris' );
			$api_request = $http_adapter->getContent( $address . '&key=' . $api_key );
			$api_request = json_decode( $api_request, true );

			GravityKitFoundation::logger( self::PLUGIN_SETTINGS_ID )->info( 'Google Maps API key verification request response:', $api_request );

			$geocoding_success = esc_html__( 'This Google API key is able to convert addresses into longitude and latitude.', 'gk-gravitymaps' );

			$geocoding_error = esc_html__( 'This Google API key is unable to convert addresses into longitude and latitude. To fix this, please ensure that your key is correct and [link]enable Geocoding API[/link].', 'gk-gravitymaps' );
			$geocoding_error = str_replace( '[link]', '<a class="gk-link" href="' . esc_url( 'https://console.cloud.google.com/apis/library/geocoding-backend.googleapis.com' ) . '">', $geocoding_error );
			$geocoding_error = str_replace( '[/link]', '</a>', $geocoding_error );

			$api_key_success = esc_html__( 'This Google API Key supports embedding a map on your site.', 'gk-gravitymaps' );
			$api_key_error   = [
				'referrer_restriction' => esc_html__( 'This Google API Key is has settings that restrict access based on "HTTP referrers". The Maps plugin requires the use of "IP addresses" restrictions in the API key settings.', 'gk-gravitymaps' ),
				'invalid'              => esc_html__( 'This Google API Key is invalid. Please verify that you entered the correct key.', 'gk-gravitymaps' ),
			];

			$response = [
				'mapping'   => [
					'capability' => esc_html_x( 'Mapping (required)', 'Google Maps API capability', 'gk-gravitymaps' ),
					'enabled'    => true,
					'message'    => $api_key_success,
				],
				'geocoding' => [
					'capability' => esc_html_x( 'Geocoding', 'Google Maps API capability', 'gk-gravitymaps' ),
					'enabled'    => true,
					'message'    => $geocoding_success
				],
			];

			if ( ! empty( $api_request['error_message'] ) ) {
				// API key "Application restrictions" like HTTP referrer
				if ( preg_match( '/API keys with referer restrictions/', $api_request['error_message'] ) ) {
					$response['geocoding'] = array_merge( $response['geocoding'], [ 'enabled' => false, 'message' => $api_key_error['referrer_restriction'] ] );
					$response['mapping']   = array_merge( $response['mapping'], [ 'enabled' => false, 'message' => esc_html( $api_key_error['referrer_restriction'] ) ] );
				} // API key "Application restrictions" like IP address limitations
				elseif ( preg_match( '/(cannot be used with this API)|(Request received from IP)/', $api_request['error_message'] ) ) {
					$response['geocoding'] = array_merge( $response['geocoding'], [ 'enabled' => false, 'message' => esc_html( $api_request['error_message'] ) ] );
					$response['mapping']   = array_merge( $response['mapping'], [ 'enabled' => false, 'message' => esc_html( $api_request['error_message'] ) ] );
				} // API key "API restrictions" settings in Google Cloud
				elseif ( preg_match( '/not authorized/', $api_request['error_message'] ) ) {
					$response['geocoding'] = array_merge( $response['geocoding'], [ 'enabled' => false, 'message' => $geocoding_error ] );
				} // Purely wrong API key?
				else {
					$response['geocoding'] = array_merge( $response['geocoding'], [ 'enabled' => false, 'message' => $geocoding_error ] );
					$response['mapping']   = array_merge( $response['mapping'], [ 'enabled' => false, 'message' => $api_key_error['invalid'] ] );
				}
			}

			return $response;
		} catch ( Exception $e ) {
			throw new Exception( $e->getMessage() );
		}
	}
}
