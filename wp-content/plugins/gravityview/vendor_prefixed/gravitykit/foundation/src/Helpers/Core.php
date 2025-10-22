<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by gravityview on 10-October-2025 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Helpers;

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
		// Treat WP_Error objects the same way we treat Exceptions when returning a response.
		$is_wp_error  = function_exists( 'is_wp_error' ) && is_wp_error( $return_object );
		$is_exception = $return_object instanceof Exception;
		$is_error     = $is_wp_error || $is_exception;

		if ( wp_doing_ajax() ) {
			$buffer = ob_get_clean();

			if ( $buffer ) {
				error_log( "[GravityKit] Buffer output before returning Ajax response: {$buffer}" ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log

				header( 'GravityKit: ' . wp_json_encode( $buffer ) );
			}

			if ( ! $is_error ) {
				wp_send_json_success( $return_object );
			}

			if ( $is_wp_error ) {
				// Build a consistent error payload for WP_Error responses.
				$payload = [
					'code'    => $return_object->get_error_code(),
					'message' => $return_object->get_error_message(),
					'data'    => $return_object->get_error_data(),
				];

				// Allow custom HTTP status via `status` key inside error data.
				$status = is_array( $payload['data'] ) && isset( $payload['data']['status'] )
					? (int) $payload['data']['status']
					: 400;

				wp_send_json_error( $payload, $status );
			} else { // Exception.
				wp_send_json_error( $return_object->getMessage() );
			}
		}

		if ( $is_exception ) {
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
		$path = realpath( __DIR__ . '/../../assets' ) ?: '';

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
			: is_multisite() && strpos( wp_get_referer() ?: '', network_admin_url() ) !== false;
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
	 * @since TODO  Use static variable to cache plugins data.
	 *
	 * @param bool $skip_cache (optional) Whether to skip cache when getting plugins data. Default: false.
	 *
	 * @return array[]
	 */
	public static function get_plugins( $skip_cache = false ) {
		static $plugins;

		if ( $plugins && ! $skip_cache ) {
			return $plugins;
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( $skip_cache ) {
			wp_cache_delete( 'plugins', 'plugins' );
		}

		$plugins = get_plugins();

		return $plugins;
	}

	/**
	 * Returns a list of installed products keyed by text domain.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Moved from GravityKit\Foundation\Licenses\ProductManager to GravityKit\Foundation\Helpers\Core.
	 * @since 1.2.0 Added $skip_cache parameter.
	 * @since 1.2.12 Result is now keyed by plugin path instead of text domain.
	 *
	 * @param bool $skip_cache (optional) Whether to skip cache when getting plugins data. Default: false.
	 *
	 * @return array[] Array of installed plugins with their metadata.
	 */
	public static function get_installed_plugins( $skip_cache = false ) {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins    = [];
		$wp_plugins = self::get_plugins( $skip_cache );

		foreach ( $wp_plugins as $path => $plugin ) {
			if ( empty( $plugin['TextDomain'] ) ) {
				continue;
			}

			$plugins[ $path ] = [
				'name'              => $plugin['Name'],
				'author'            => $plugin['Author'],
				'path'              => $path,
				'plugin_file'       => file_exists( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $path ) ? WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $path : null,
				'installed'         => true,
				'installed_version' => $plugin['Version'],
				'version'           => $plugin['Version'],
				'text_domain'       => $plugin['TextDomain'],
				'active'            => is_plugin_active( $path ),
				'network_activated' => is_plugin_active_for_network( $path ),
				'has_admin_menu'    => false, // @TODO: possibly handle this differently.
				'free'              => true, // @TODO: possibly handle this differently.
				'has_update'        => false, // @TODO: detect if there's an update available.
				'download_link'     => null, // @TODO: get the download link if there's an update available.
			];

			$dependencies = [
				'0.0.1' => [
					'system' => [],
					'plugin' => [],
				],
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

			$plugins[ $path ]['dependencies'] = $dependencies;
		}

		return array_values( $plugins );
	}

	/**
	 * Searches installed plugin by text domain(s) and returns its data.
	 *
	 * @since 1.0.0
	 * @since 1.0.4 Moved from GravityKit\Foundation\Licenses\ProductManager to GravityKit\Foundation\Helpers\Core.
	 * @since 1.2.0 Added $skip_cache parameter.
	 * @since 1.2.12 Added $author_str & $return_multiple parameters.
	 *
	 * @param string $text_domains_str Text domain(s). Optionally pipe-separated (e.g. 'gravityview|gk-gravtiyview').
	 * @param bool   $skip_cache       (optional) Whether to skip cache when getting plugins data. Default: false.
	 * @param string $author_str       (optional) Plugins author(s). Optionally pipe-separated (e.g. 'GravityView|GravityKit|Katz Web Services, Inc.').
	 * @param bool   $return_multiple  (optional) Whether to return multiple plugins that may share the same author/text domain. Default: false.
	 *
	 * @return array|null An array with plugin data, array of arrays with multiple plugins data, or null if not installed.
	 */
	public static function get_installed_plugin_by_text_domain( $text_domains_str, $skip_cache = false, $author_str = '', $return_multiple = false ) {
		$installed_plugins = self::get_installed_plugins( $skip_cache );

		$plugins      = [];
		$text_domains = explode( '|', strtolower( $text_domains_str ) );
		$authors      = '' === $author_str ? [] : explode( '|', strtolower( $author_str ) );

		foreach ( $installed_plugins as $plugin ) {
			if ( ! in_array( strtolower( $plugin['text_domain'] ), $text_domains, true ) ) {
				continue;
			}

			if ( ! empty( $authors ) && ! in_array( strtolower( $plugin['author'] ), $authors, true ) ) {
				continue;
			}

			if ( ! $return_multiple ) {
				return $plugin;
			}

			$plugins[] = $plugin;
		}

		return ! empty( $plugins ) ? $plugins : null;
	}

	/**
	 * Wrapper for WP's get_plugin_data() function.
	 *
	 * @see   https://github.com/WordPress/wordpress-develop/blob/2bb5679d666474d024352fa53f07344affef7e69/src/wp-admin/includes/plugin.php#L72-L118
	 *
	 * @since 1.0.0
	 * @since 1.2.21 Set the $translate parameter to false by default.
	 *
	 * @param string $plugin_file Absolute path to the main plugin file.
	 * @param bool   $markup      (optional) If the returned data should have HTML markup applied. Default is true.
	 * @param bool   $translate   (optional) If the returned data should be translated. Default is true.
	 *
	 * @return array<string, mixed> Associative array of plugin data.
	 */
	public static function get_plugin_data( $plugin_file, $markup = true, $translate = false ) {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return get_plugin_data( $plugin_file, $markup, $translate );
	}

	/**
	 * Returns the plugin slug from its text domain.
	 *
	 * @since 1.2.6
	 *
	 * @param string $text_domain Text domain.
	 *
	 * @return string|null
	 */
	public static function get_plugin_slug_from_text_domain( $text_domain ) {
		$installed_plugins = self::get_installed_plugins();

		foreach ( $installed_plugins as $plugin ) {
			if ( $plugin['text_domain'] === $text_domain ) {
				return dirname( plugin_basename( $plugin['plugin_file'] ) );
			}
		}

		return null;
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
	 * @since 1.2.0
	 *
	 * @return bool
	 */
	public static function is_cli() {
		return php_sapi_name() === 'cli';
	}

	/**
	 * Checks if we're debugging Foundation.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public static function is_foundation_debug() {
		return defined( 'GK_FOUNDATION_DEBUG' ) && GK_FOUNDATION_DEBUG;
	}

	/**
	 * Compares two version strings after trimming any trailing Git-style commit hashes.
	 * Uses PHP's built-in version_compare() function.
	 *
	 * @since 1.2.24
	 *
	 * @param mixed       $version1 First version to compare. Will be cast to string.
	 * @param mixed       $version2 Second version to compare. Will be cast to string.
	 * @param string|null $operator (optional) Comparison operator.
	 *
	 * @return int|bool Returns -1, 0, or 1 if no operator is given; otherwise, returns a boolean.
	 */
	public static function version_compare( $version1, $version2, $operator = null ) {
		$sanitize = function ( $version ) {
			$version = trim( (string) $version );

			return preg_replace( '/-[a-zA-Z0-9]{7,40}$/', '', $version );
		};

		$clean1 = $sanitize( $version1 );
		$clean2 = $sanitize( $version2 );

		return $operator
			? version_compare( $clean1, $clean2, $operator )
			: version_compare( $clean1, $clean2 );
	}

	/**
	 * Checks if the WordPress site is accessible by performing HTTP requests to various endpoints.
	 * Useful for verifying site health after configuration changes.
	 *
	 * @since 1.5.0
	 *
	 * @param array<string, mixed> $args {
	 *     Optional. Arguments to customize the health check.
	 *
	 *     @type array  $custom_checks     Additional endpoint checks to perform.
	 *     @type array  $request_args      Additional arguments to pass to wp_remote_get().
	 * }
	 *
	 * @return bool True if site is accessible, false otherwise.
	 */
	public static function is_site_accessible( $args = [] ) {
		$defaults = [
			'custom_checks' => [],
			'request_args'  => [],
		];

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filters if site health check should be skipped. This is useful if loopback is restricted.
		 *
		 * @since 1.5.0
		 *
		 * @param bool $skip_site_health_check Whether to skip site health check.
		 *
		 * @return bool True if site health check should be skipped.
		 */
		if ( true === apply_filters( 'gk/foundation/skip-site-health-check', false ) ) {
			return true;
		}

		// Determine the correct admin URL based on context.
		$admin_url = self::is_network_admin() ? network_admin_url( 'admin-ajax.php' ) : admin_url( 'admin-ajax.php' );

		$checks_to_try = [
			// Try admin-ajax.php first.
			[
				'url'              => add_query_arg(
					[
						'action' => 'heartbeat',
						'_nonce' => wp_create_nonce( 'heartbeat-nonce' ),
					],
					$admin_url
				),
				'acceptable_codes' => [ 200, 400 ],
			],
			// Try the home URL as fallback.
			[
				'url'              => home_url( '/?nocache=' . time() ),
				'acceptable_codes' => [ 200, 301, 302 ],
			],
		];

		// In network admin context, also check the main network site.
		// network_home_url() is available in multisite installs, which is when is_network_admin() would be true.
		if ( self::is_network_admin() ) {
			$checks_to_try[] = [
				'url'              => network_home_url( '/?nocache=' . time() ),
				'acceptable_codes' => [ 200, 301, 302 ],
			];
		}

		// Try wp-login.php as last resort.
		$checks_to_try[] = [
			'url'              => wp_login_url(),
			'acceptable_codes' => [ 200 ],
		];

		// Add custom checks if provided.
		if ( ! empty( $args['custom_checks'] ) ) {
			$checks_to_try = array_merge( $checks_to_try, $args['custom_checks'] );
		}

		// Prepare default request arguments.
		$default_request_args = [
			'timeout'     => 3,
			'redirection' => 0,
			'sslverify'   => false,
			'headers'     => [
				'Cache-Control' => 'no-cache',
			],
		];

		// Merge with any custom request arguments provided.
		$request_args = wp_parse_args( $args['request_args'], $default_request_args );

		foreach ( $checks_to_try as $check ) {
			$response = wp_remote_get(
				$check['url'],
				$request_args
			);

			if ( ! is_wp_error( $response ) ) {
				$status_code = wp_remote_retrieve_response_code( $response );

				if ( in_array( $status_code, $check['acceptable_codes'], true ) ) {
					return true;
				}
			}
		}

		return false;
	}
}
