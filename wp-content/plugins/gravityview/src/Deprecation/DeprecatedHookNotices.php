<?php
/**
 * Displays admin notices when deprecated GravityView hooks are used by external code.
 *
 * Provides wrapper methods around WordPress's apply_filters_deprecated() and
 * do_action_deprecated() that suppress PHP trigger_error for internal GravityKit
 * callbacks while registering Foundation stored notices for external ones.
 *
 * Note: This class is a candidate for migration to Foundation in the future,
 * so that all GravityKit products can share the same deprecation notice infrastructure.
 *
 * @package GravityKit\GravityView\Deprecation
 * @since 2.55.0
 * @since 3.0.0 Migrated to PSR-4 namespace.
 */

namespace GravityKit\GravityView\Deprecation;

use GravityKit\GravityView\Field\Types\GravityViewField;
use GravityKit\GravityView\Widget\Widget;
use GravityKitFoundation;

/**
 * Handles admin notices for deprecated GravityView hooks.
 *
 * @since 2.55.0
 * @since 3.0.0 Migrated to PSR-4 namespace.
 */
class DeprecatedHookNotices {
	/**
	 * Tracks hooks already processed in the current request to avoid redundant work.
	 *
	 * @since 2.55.0
	 *
	 * @var array<string, true>
	 */
	private static $processed = [];

	/**
	 * Nesting depth counter for re-entrant suppression safety.
	 *
	 * @since 2.55.0
	 *
	 * @var int
	 */
	private static $suppress_depth = 0;

	/**
	 * Whether push_suppression() added the trigger-error filter itself.
	 *
	 * @since 3.1.0
	 *
	 * @var bool
	 */
	private static $added_suppression_filter = false;

	/**
	 * Cached callback analysis results keyed by callback identity.
	 *
	 * @since 2.55.0
	 *
	 * @var array<string, array{file: string, line: int, internal: bool}|null>
	 */
	private static $callback_cache = [];

	/**
	 * Cached list of GravityKit product directories, built once per request.
	 *
	 * @since 2.55.0
	 *
	 * @var string[]|null
	 */
	private static $product_dirs = null;

	/**
	 * Whether the product directories cache includes Foundation data and is final.
	 *
	 * @since 2.55.0
	 *
	 * @var bool
	 */
	private static $product_dirs_final = false;

	/**
	 * Initialize the handler.
	 *
	 * @since 2.55.0
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'deprecated_hook_run', [ __CLASS__, 'handle_deprecated_hook' ], 10, 4 );
	}

	/**
	 * Wrapper for apply_filters_deprecated() that suppresses PHP trigger_error
	 * when all callbacks are from GravityKit products.
	 *
	 * @since 2.55.0
	 *
	 * @param string $hook        The deprecated hook name.
	 * @param array  $args        Arguments passed to the hook.
	 * @param string $version     The version when the hook was deprecated.
	 * @param string $replacement The replacement hook name.
	 * @param string $message     Optional additional message.
	 *
	 * @return mixed The filtered value.
	 */
	public static function apply_filters( $hook, $args, $version, $replacement = '', $message = '' ) {
		$suppress = self::should_suppress_trigger_error( $hook );

		if ( $suppress ) {
			self::push_suppression();
		}

		try {
			$value = apply_filters_deprecated( $hook, $args, $version, $replacement, $message );
		} finally {
			if ( $suppress ) {
				self::pop_suppression();
			}
		}

		return $value;
	}

	/**
	 * Wrapper for do_action_deprecated() that suppresses PHP trigger_error
	 * when all callbacks are from GravityKit products.
	 *
	 * @since 2.55.0
	 *
	 * @param string $hook        The deprecated hook name.
	 * @param array  $args        Arguments passed to the hook.
	 * @param string $version     The version when the hook was deprecated.
	 * @param string $replacement The replacement hook name.
	 * @param string $message     Optional additional message.
	 *
	 * @return void
	 */
	public static function do_action( $hook, $args, $version, $replacement = '', $message = '' ) {
		$suppress = self::should_suppress_trigger_error( $hook );

		if ( $suppress ) {
			self::push_suppression();
		}

		try {
			do_action_deprecated( $hook, $args, $version, $replacement, $message );
		} finally {
			if ( $suppress ) {
				self::pop_suppression();
			}
		}
	}

	/**
	 * Wrapper for _deprecated_hook() that suppresses PHP trigger_error
	 * when all callbacks are from GravityKit products.
	 *
	 * Use this for hooks that call _deprecated_hook() manually (e.g., when
	 * using apply_filters_ref_array instead of apply_filters_deprecated).
	 *
	 * @since 2.55.0
	 *
	 * @param string $hook        The deprecated hook name.
	 * @param string $version     The version when the hook was deprecated.
	 * @param string $replacement The replacement hook name.
	 * @param string $message     Optional additional message.
	 *
	 * @return void
	 */
	public static function deprecated_hook( $hook, $version, $replacement = '', $message = '' ) {
		$suppress = self::should_suppress_trigger_error( $hook );

		if ( $suppress ) {
			self::push_suppression();
		}

		try {
			_deprecated_hook( $hook, $version, $replacement, $message );
		} finally {
			if ( $suppress ) {
				self::pop_suppression();
			}
		}
	}

	/**
	 * Increment the suppression depth and add the filter if this is the first level.
	 *
	 * @since 2.55.0
	 *
	 * @return void
	 */
	private static function push_suppression() {
		if ( 0 === self::$suppress_depth ) {
			// Only add (and later remove) the filter if nobody else registered it,
			// so an identical pre-existing suppression is not clobbered on pop.
			self::$added_suppression_filter = false === has_filter( 'deprecated_hook_trigger_error', '__return_false' );

			if ( self::$added_suppression_filter ) {
				add_filter( 'deprecated_hook_trigger_error', '__return_false' );
			}
		}

		self::$suppress_depth++;
	}

	/**
	 * Decrement the suppression depth and remove the filter at the last level.
	 *
	 * @since 2.55.0
	 *
	 * @return void
	 */
	private static function pop_suppression() {
		self::$suppress_depth--;

		if ( 0 === self::$suppress_depth && self::$added_suppression_filter ) {
			remove_filter( 'deprecated_hook_trigger_error', '__return_false' );
			self::$added_suppression_filter = false;
		}
	}

	/**
	 * Determine whether to suppress the PHP deprecation notice for a hook.
	 *
	 * Returns true when all registered callbacks on the hook are from GravityKit
	 * products. If any external callback exists, returns false so the PHP notice
	 * fires normally for debugging.
	 *
	 * @since 2.55.0
	 *
	 * @param string $hook The hook name to check.
	 *
	 * @return bool Whether to suppress the trigger_error.
	 */
	private static function should_suppress_trigger_error( $hook ) {
		global $wp_filter;

		if ( empty( $wp_filter[ $hook ] ) ) {
			return true;
		}

		foreach ( $wp_filter[ $hook ]->callbacks as $priority => $callbacks ) {
			foreach ( $callbacks as $id => $callback_info ) {
				if ( self::is_auto_registered_options_callback( $hook, $priority, $callback_info['function'] ) ) {
					continue;
				}

				$analysis = self::analyze_callback( $id, $callback_info['function'] );

				if ( null === $analysis ) {
					continue;
				}

				// External callback found — let PHP notice fire.
				if ( ! $analysis['internal'] ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Handle a deprecated hook notification.
	 *
	 * Called by WordPress when apply_filters_deprecated() or do_action_deprecated() fires
	 * for a hook that has registered callbacks. Registers Foundation admin notices
	 * for external callbacks only.
	 *
	 * @since 2.55.0
	 *
	 * @param string $hook        The deprecated hook name.
	 * @param string $replacement The replacement hook name.
	 * @param string $version     The version when the hook was deprecated.
	 * @param string $message     Optional additional message.
	 *
	 * @return void
	 */
	public static function handle_deprecated_hook( $hook, $replacement, $version, $message = '' ) {
		if ( ! class_exists( 'GravityKitFoundation' ) || ! GravityKitFoundation::notices() ) {
			return;
		}

		global $wp_filter;

		if ( empty( $wp_filter[ $hook ] ) ) {
			return;
		}

		foreach ( $wp_filter[ $hook ]->callbacks as $priority => $callbacks ) {
			foreach ( $callbacks as $id => $callback_info ) {
				if ( self::is_auto_registered_options_callback( $hook, $priority, $callback_info['function'] ) ) {
					continue;
				}

				$analysis = self::analyze_callback( $id, $callback_info['function'] );

				if ( null === $analysis || $analysis['internal'] ) {
					continue;
				}

				$location_key = $analysis['file'] . ':' . $analysis['line'];
				$process_key  = $hook . '|' . $location_key;

				if ( isset( self::$processed[ $process_key ] ) ) {
					continue;
				}

				self::$processed[ $process_key ] = true;

				$is_debug = defined( 'WP_DEBUG' ) && WP_DEBUG;

				if ( $is_debug ) {
					$log_message = sprintf(
						'GravityView: The "%s" hook is deprecated. Use "%s" instead. Called by %s.',
						$hook,
						$replacement ?: 'nothing',
						$location_key
					);

					error_log( $log_message );
				}

				/**
				 * Controls whether a deprecated hook notice should be registered.
				 *
				 * When WP_DEBUG is off, this defaults to false so that deprecation
				 * warnings are not shown to site administrators who are not developers.
				 *
				 * @since 2.55.0
				 *
				 * @param bool   $register     Whether to register the notice. Default: value of WP_DEBUG.
				 * @param string $hook         The deprecated hook name.
				 * @param string $replacement  The replacement hook name.
				 * @param string $location_key The source file:line where the callback is defined.
				 */
				$register = apply_filters( 'gk/gravityview/deprecated-hook-notices/register', $is_debug, $hook, $replacement, $location_key );

				if ( ! $register ) {
					continue;
				}

				self::register_notice( $hook, $replacement, $location_key, $message );
			}
		}
	}

	/**
	 * Whether a callback is one GravityView's own base classes auto-register on a
	 * deprecated per-type options hook on behalf of the field/widget author.
	 *
	 * GravityViewField and Widget wire the subclass's options method onto these
	 * hooks from their constructors, so GravityView core is the registrant even
	 * when the overriding method lives in a third-party file; these are not
	 * external deprecated-hook usage.
	 *
	 * Matching is by callback shape rather than provenance, so an extension that
	 * re-registers the same method on the same hook at the default priority counts
	 * as internal too. The blind spot is deliberate: that notice would name a
	 * callback the author cannot act on.
	 *
	 * @since 3.1.0
	 *
	 * @param string $hook     The deprecated hook name.
	 * @param int    $priority The priority the callback is registered at.
	 * @param mixed  $callback The registered callback.
	 *
	 * @return bool
	 */
	private static function is_auto_registered_options_callback( $hook, $priority, $callback ) {
		// Both base classes register at the default priority; an explicit
		// registration at another priority is genuine external usage.
		if ( 10 !== (int) $priority ) {
			return false;
		}

		if ( ! is_array( $callback ) || 2 !== count( $callback ) || ! is_object( $callback[0] ) ) {
			return false;
		}

		list( $object, $method ) = $callback;

		if ( $object instanceof GravityViewField
			&& 'field_options' === $method
			&& $hook === sprintf( 'gravityview_template_%s_options', $object->name )
		) {
			return true;
		}

		if ( $object instanceof Widget
			&& 'assign_widget_options' === $method
			&& 'gravityview_template_widget_options' === $hook
		) {
			return true;
		}

		return false;
	}

	/**
	 * Analyze a callback: get its location and whether it's internal.
	 *
	 * Results are cached per callback identity for the duration of the request.
	 *
	 * @since 2.55.0
	 *
	 * @param string   $id       The callback identity key from WP_Hook.
	 * @param callable $callback The callback to analyze.
	 *
	 * @return array{file: string, line: int, internal: bool}|null Analysis result or null.
	 */
	private static function analyze_callback( $id, $callback ) {
		if ( array_key_exists( $id, self::$callback_cache ) ) {
			return self::$callback_cache[ $id ];
		}

		$location = self::get_callback_location( $callback );

		if ( ! $location ) {
			self::$callback_cache[ $id ] = null;

			return null;
		}

		$result = [
			'file'     => $location['file'],
			'line'     => $location['line'],
			'internal' => self::is_internal_callback( $location['file'] ),
		];

		self::$callback_cache[ $id ] = $result;

		return $result;
	}

	/**
	 * Get the source file and line of a callback.
	 *
	 * @since 2.55.0
	 *
	 * @param callable $callback The callback to inspect.
	 *
	 * @return array{file: string, line: int}|null Source location or null if undetermined.
	 */
	private static function get_callback_location( $callback ) {
		try {
			if ( is_string( $callback ) ) {
				if ( false !== strpos( $callback, '::' ) ) {
					list( $class, $method ) = explode( '::', $callback, 2 );
					$reflector = new \ReflectionMethod( $class, $method );
				} else {
					$reflector = new \ReflectionFunction( $callback );
				}
			} elseif ( is_array( $callback ) && 2 === count( $callback ) ) {
				$reflector = new \ReflectionMethod( $callback[0], $callback[1] );
			} elseif ( $callback instanceof \Closure ) {
				$reflector = new \ReflectionFunction( $callback );
			} elseif ( is_object( $callback ) && method_exists( $callback, '__invoke' ) ) {
				$reflector = new \ReflectionMethod( $callback, '__invoke' );
			} else {
				return null;
			}

			$file = $reflector->getFileName();
			$line = $reflector->getStartLine();

			if ( ! $file ) {
				return null;
			}

			return [
				'file' => $file,
				'line' => $line,
			];
		} catch ( \ReflectionException $e ) {
			return null;
		}
	}

	/**
	 * Build and cache the list of GravityKit product directories.
	 *
	 * @since 2.55.0
	 *
	 * @return string[] Absolute directory paths for all GravityKit products.
	 */
	private static function get_product_dirs() {
		// Return cached result if Foundation has fully loaded or we've already built with Foundation data.
		if ( null !== self::$product_dirs && self::$product_dirs_final ) {
			return self::$product_dirs;
		}

		self::$product_dirs = [];

		$plugin_dir = realpath( GRAVITYVIEW_DIR );

		if ( $plugin_dir ) {
			self::$product_dirs[] = $plugin_dir . DIRECTORY_SEPARATOR;
		}

		if ( class_exists( 'GravityKitFoundation' ) ) {
			try {
				$foundation = GravityKitFoundation::get_instance();

				if ( is_object( $foundation ) && is_callable( [ $foundation, 'get_registered_plugins' ] ) ) {
					foreach ( $foundation->get_registered_plugins() as $plugin_file => $plugin_data ) {
						$product_dir = realpath( dirname( $plugin_file ) );

						if ( $product_dir ) {
							self::$product_dirs[] = $product_dir . DIRECTORY_SEPARATOR;
						}
					}

					// Only final once Foundation has fully initialized, so products
					// registering during boot are not locked out of the cache.
					$is_final = (bool) did_action( 'gk/foundation/initialized' );

					if ( $is_final && ! self::$product_dirs_final ) {
						// Re-evaluate callbacks classified before the product list was complete.
						self::$callback_cache = [];
					}

					self::$product_dirs_final = $is_final;
				}
			} catch ( \Exception $e ) {
				// Foundation not ready yet; GRAVITYVIEW_DIR is still checked.
			}
		}

		/**
		 * Filters the directories treated as GravityKit product directories.
		 *
		 * Useful for legacy GravityKit plugins that do not register with Foundation.
		 *
		 * @since 3.1.0
		 *
		 * @param string[] $product_dirs Absolute directory paths with trailing separator.
		 */
		self::$product_dirs = (array) apply_filters( 'gk/gravityview/deprecated-hook-notices/product-dirs', self::$product_dirs );

		return self::$product_dirs;
	}

	/**
	 * Check if a file path belongs to a GravityKit product.
	 *
	 * Uses a cached list of GravityKit product directories built from
	 * Foundation's registered plugins. The result can be overridden via
	 * the `gk/gravityview/deprecated-hook-notices/is-internal` filter,
	 * which is useful for showing notices for GK products during development.
	 *
	 * @since 2.55.0
	 *
	 * @param string $file The file path to check.
	 *
	 * @return bool Whether the file belongs to a GravityKit product.
	 */
	private static function is_internal_callback( $file ) {
		$file = realpath( $file );

		if ( ! $file ) {
			return false;
		}

		$is_internal = false;

		foreach ( self::get_product_dirs() as $dir ) {
			if ( 0 === strpos( $file, $dir ) ) {
				$is_internal = true;

				break;
			}
		}

		/**
		 * Overrides whether a callback is treated as internal (from a GK product).
		 *
		 * Returning false forces the callback to be treated as external, which
		 * means deprecation notices will be shown. This is useful for development
		 * and debugging.
		 *
		 * Example usage:
		 *     add_filter( 'gk/gravityview/deprecated-hook-notices/is-internal', '__return_false' );
		 *
		 * @since 2.55.0
		 *
		 * @param bool   $is_internal Whether the callback is internal. Default: result of directory check.
		 * @param string $file        The absolute file path of the callback.
		 */
		return apply_filters( 'gk/gravityview/deprecated-hook-notices/is-internal', $is_internal, $file );
	}

	/**
	 * Register a Foundation stored notice for a deprecated hook usage.
	 *
	 * @since 2.55.0
	 *
	 * @param string $hook         The deprecated hook name.
	 * @param string $replacement  The replacement hook name.
	 * @param string $location_key The source file:line where the callback is defined.
	 * @param string $message      Optional additional guidance appended to the notice.
	 *
	 * @return void
	 */
	private static function register_notice( $hook, $replacement, $location_key, $message = '' ) {
		$slug = sprintf(
			'deprecated_hook_%s_%s',
			sanitize_title( $hook ),
			substr( md5( $location_key ), 0, 8 )
		);

		$hook_code        = '<code>' . esc_html( $hook ) . '</code>';
		$replacement_code = '<code>' . esc_html( $replacement ) . '</code>';

		if ( 'unknown location' === $location_key ) {
			$notice_message = strtr(
				/* translators: [hook]: deprecated hook name, [replacement]: replacement hook name. */
				__( 'The [hook] hook has been deprecated. Use [replacement] instead.', 'gk-gravityview' ),
				[
					'[hook]'        => $hook_code,
					'[replacement]' => $replacement_code,
				]
			);
		} else {
			$relative_path = self::get_relative_path( $location_key );

			$notice_message = strtr(
				/* translators: [hook]: deprecated hook name, [location]: file location, [replacement]: replacement hook name. */
				__( 'The [hook] hook used in [location] has been deprecated. Use [replacement] instead.', 'gk-gravityview' ),
				[
					'[hook]'        => $hook_code,
					'[location]'    => '<code>' . esc_html( $relative_path ) . '</code>',
					'[replacement]' => $replacement_code,
				]
			);
		}

		if ( $message ) {
			$notice_message .= ' ' . esc_html( $message );
		}

		try {
			GravityKitFoundation::notices()->add_stored( [
				'namespace'            => 'gk-gravityview',
				'slug'                 => $slug,
				'message'              => $notice_message,
				'severity'             => 'warning',
				'globally_dismissible' => true,
				'capabilities'         => [ 'manage_options' ],
				'context'              => 'all',
				'screens'              => [ 'dashboard' ],
			] );
		} catch ( \Exception $e ) {
			// Silently fail — don't crash the site over a notice.
		}
	}

	/**
	 * Convert an absolute file:line path to a relative path.
	 *
	 * @since 2.55.0
	 *
	 * @param string $location_key Absolute file path with :line suffix.
	 *
	 * @return string Relative path with :line suffix.
	 */
	private static function get_relative_path( $location_key ) {
		// Split at the last colon to handle Windows drive letters (e.g., C:/path/file.php:42).
		$last_colon = strrpos( $location_key, ':' );
		$file       = substr( $location_key, 0, $last_colon );
		$line       = substr( $location_key, $last_colon + 1 );

		$file        = wp_normalize_path( $file );
		$content_dir = wp_normalize_path( WP_CONTENT_DIR ) . '/';

		// Inside wp-content (plugins, mu-plugins, themes, etc.).
		if ( 0 === strpos( $file, $content_dir ) ) {
			return substr( $file, strlen( $content_dir ) ) . ':' . $line;
		}

		// Symlinked plugin or mu-plugin — plugin_basename() resolves through symlinks.
		$basename = plugin_basename( $file );

		if ( $basename !== $file ) {
			$mu_dir = wp_normalize_path( WPMU_PLUGIN_DIR ) . '/';

			// Detect whether the resolved path is under mu-plugins or plugins.
			if ( 0 === strpos( wp_normalize_path( realpath( $file ) ?: $file ), wp_normalize_path( realpath( WPMU_PLUGIN_DIR ) ?: WPMU_PLUGIN_DIR ) . '/' ) ) {
				$prefix = basename( $mu_dir );
			} else {
				$prefix = basename( wp_normalize_path( WP_PLUGIN_DIR ) );
			}

			return $prefix . '/' . $basename . ':' . $line;
		}

		// Inside ABSPATH (e.g., wp-config.php, root-level files).
		$abspath = wp_normalize_path( ABSPATH );

		if ( 0 === strpos( $file, $abspath ) ) {
			return substr( $file, strlen( $abspath ) ) . ':' . $line;
		}

		// Completely outside WordPress (e.g., Composer autoloaded package).
		return basename( $file ) . ':' . $line;
	}
}
