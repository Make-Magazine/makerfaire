<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 02-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Helpers;

use Closure;
use Exception;

class Core {
	/**
	 * Processes return object based on the request type (e.g., Ajax) and status.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed|Exception $return_object Return object (default: 'true').
	 *
	 * @throws Exception
	 *
	 * @return void|mixed Send JSON response if an Ajax request or return the response as is.
	 */
	public static function process_return( $return_object = true ) {
		$is_error = $return_object instanceof Exception;

		if ( wp_doing_ajax() ) {
			$buffer = ob_get_clean();

			if ( $buffer ) {
				error_log( "[GravityKit] Buffer output before returning Ajax response: {$buffer}" );

				header( 'GravityKit: ' . json_encode( $buffer ) );
			}

			if ( $is_error ) {
				wp_send_json_error( $return_object->getMessage() );
			} else {
				wp_send_json_success( $return_object );
			}
		}

		if ( $is_error ) {
			throw new Exception( $return_object->getMessage() );
		}

		return $return_object;
	}

	/**
	 * Returns path to UI assets.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file (optional) File name to append to path.
	 *
	 * @return string
	 */
	public static function get_assets_path( $file = '' ) {
		$path = realpath( __DIR__ . '/../../assets' );

		return $file ? trailingslashit( $path ) . $file : $path;
	}

	/**
	 * Returns URL to UI assets.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file (optional) File name to append to URL.
	 *
	 * @return string
	 */
	public static function get_assets_url( $file = '' ) {
		$url = plugin_dir_url( self::get_assets_path() ) . 'assets';

		return $file ? trailingslashit( $url ) . $file : $url;
	}

	/**
	 * Checks if the current page is a network admin area.
	 * The Ajax check is not to be fully relied upon as the referer can be changed.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_network_admin() {
		return ! wp_doing_ajax()
			? is_network_admin()
			: is_multisite() && strpos( wp_get_referer(), network_admin_url() ) !== false;
	}

	/**
	 * Checks if the current page is a main network site, but not the network admin area.
	 *
	 * @since 1.0.4
	 *
	 * @return bool
	 */
	public static function is_main_network_site() {
		return is_multisite() && is_main_site() && ! self::is_network_admin();
	}

	/**
	 * Checks if the current page is not a main network site.
	 *
	 * @since 1.0.4
	 *
	 * @return bool
	 */
	public static function is_not_main_network_site() {
		return is_multisite() && ! is_main_site();
	}

	/**
	 * Wrapper for WP's get_plugins() function.
	 *
	 * @see   https://github.com/WordPress/wordpress-develop/blob/2bb5679d666474d024352fa53f07344affef7e69/src/wp-admin/includes/plugin.php#L274-L411
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Added $skip_cache parameter.
	 *
	 * @param bool $skip_cache (optional) Whether to skip cache when getting plugins data. Default: false.
	 *
	 * @return array[]
	 */
	public static function get_plugins( $skip_cache = false ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( $skip_cache ) {
			wp_cache_delete( 'plugins', 'plugins' );
		}

		return get_plugins();
	}

	/**
	 * Returns a list of installed products keyed by text domain.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Moved from GravityKit\Foundation\Licenses\ProductManager to GravityKit\Foundation\Helpers\Core.
	 * @since 1.2.0 Added $skip_cache parameter.
	 *
	 * @param bool $skip_cache (optional) Whether to skip cache when getting plugins data. Default: false.
	 *
	 * @return array{name:string, path: string, plugin_file:string, installed: bool, installed_version: string, version: string, text_domain: string, active: bool, network_active: bool, free: bool, has_update: bool, download_link: string|null}
	 */
	public static function get_installed_plugins( $skip_cache = false ) {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins = [];

		foreach ( self::get_plugins( $skip_cache ) as $path => $plugin ) {
			if ( empty( $plugin['TextDomain'] ) ) {
				continue;
			}

			$plugins[ $plugin['TextDomain'] ] = array(
				'name'              => $plugin['Name'],
				'path'              => $path,
				'plugin_file'       => file_exists( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $path ) ? WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $path : null,
				'installed'         => true,
				'installed_version' => $plugin['Version'],
				'version'           => $plugin['Version'],
				'text_domain'       => $plugin['TextDomain'],
				'active'            => is_plugin_active( $path ),
				'network_activated' => is_plugin_active_for_network( $path ),
				'free'              => true, // @TODO: possibly handle this differently.
				'has_update'        => false, // @TODO: detect if there's an update available.
				'download_link'     => null, // @TODO: get the download link if there's an update available.
			);

			$dependencies = [
				'0.0.1' => [
					'system' => [],
					'plugin' => [],
				]
			];

			$required_php_version = $plugin['RequiresPHP'] ?? null;
			$required_wp_version  = $plugin['RequiresWP'] ?? null;

			if ( $required_php_version ) {
				$dependencies['0.0.1']['system'][] = [
					'name'    => 'PHP',
					'version' => $required_php_version,
					'icon'    => 'https://www.gravitykit.com/wp-content/uploads/2023/08/wordpress-alt.svg',
				];
			}


			if ( $required_wp_version ) {
				$dependencies['0.0.1']['system'][] = [
					'name'    => 'WordPress',
					'version' => $required_wp_version,
					'icon'    => 'https://www.gravitykit.com/wp-content/uploads/2023/08/wordpress-alt.svg',
				];
			}

			$plugins[ $plugin['TextDomain'] ]['dependencies'] = $dependencies;
		}

		return $plugins;
	}

	/**
	 * Searches installed plugin by text domain(s) and returns its data.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Moved from GravityKit\Foundation\Licenses\ProductManager to GravityKit\Foundation\Helpers\Core.
	 * @since 1.2.0 Added $skip_cache parameter.
	 *
	 * @param string $text_domains_str Text domain(s). Optionally pipe-separated (e.g. 'gravityview|gk-gravtiyview').
	 * @param bool   $skip_cache       (optional) Whether to skip cache when getting plugins data. Default: false.
	 *
	 * @return array|null An array with plugin data or null if not installed.
	 */
	public static function get_installed_plugin_by_text_domain( $text_domains_str, $skip_cache = false ) {
		$installed_plugins = self::get_installed_plugins( $skip_cache );

		$text_domains_arr = explode( '|', $text_domains_str );

		foreach ( $text_domains_arr as $text_domain ) {
			if ( isset( $installed_plugins[ $text_domain ] ) ) {
				return $installed_plugins[ $text_domain ];
			}
		}

		return null;
	}

	/**
	 * Wrapper for WP's get_plugin_data() function.
	 *
	 * @see   https://github.com/WordPress/wordpress-develop/blob/2bb5679d666474d024352fa53f07344affef7e69/src/wp-admin/includes/plugin.php#L72-L118
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_file Absolute path to the main plugin file.
	 * @param bool   $markup      (optional) If the returned data should have HTML markup applied. Default is true.
	 * @param bool   $translate   (optional) If the returned data should be translated. Default is true.
	 *
	 * @return array[]
	 */
	public static function get_plugin_data( $plugin_file, $markup = true, $translate = true ) {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return get_plugin_data( $plugin_file, $markup, $translate );
	}

	/**
	 * Checks if value is a callable function.
	 *
	 * @since 1.0.0
	 *
	 * @param string|mixed $value Value to check.
	 *
	 * @return bool
	 */
	public static function is_callable_function( $value ) {
		return ( is_string( $value ) && function_exists( $value ) ) || $value instanceof Closure;
	}

	/**
	 * Checks if value is a callable class method.
	 *
	 * @since 1.0.0
	 *
	 * @param array|mixed $value Value to check.
	 *
	 * @return bool
	 */
	public static function is_callable_class_method( $value ) {
		if ( ! is_array( $value ) || count( $value ) !== 2 ) {
			return false;
		}

		$value = array_values( $value );

		return ( is_object( $value[0] ) || is_string( $value[0] ) ) &&
		       method_exists( $value[0], $value[1] );
	}

	/**
	 * Checks if script is executed in a WP CLI environment.
	 *
	 * @since 1.2.0
	 *
	 * @return bool
	 */
	public static function is_wp_cli() {
		return defined( 'WP_CLI' ) && WP_CLI;
	}

	/**
	 * Checks if script is executed in a CLI environment.
	 *
	 * @return bool
	 */
	public static function is_cli() {
		return php_sapi_name() === 'cli';
	}
}
