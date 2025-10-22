<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 16-October-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Settings;

use GravityKit\GravityEdit\Foundation\Helpers\Arr;
use GravityKit\GravityEdit\Foundation\Helpers\Core;
use GravityKit\GravityEdit\Foundation\Components\SecureDownload;
use Exception;

/**
 * Manages WP Debug settings - allows enabling WP Debug logging and generates download links for log files.
 *
 * @since 1.5.0
 */
class WPDebugSettings {
	/**
	 * Path to wp-config.php file.
	 *
	 * @since 1.5.0
	 *
	 * @var string|null
	 */
	private $_wp_config_path;

	/**
	 * Marker for modified constants.
	 *
	 * @since 1.5.0
	 */
	const MARKER_MODIFIED = 'gk-foundation-modified';

	/**
	 * Marker for added constants.
	 *
	 * @since 1.5.0
	 */
	const MARKER_ADDED = 'gk-foundation-added';

	/**
	 * Begin marker for debug settings block.
	 *
	 * @since 1.5.0
	 */
	const BLOCK_BEGIN = '// gk-foundation-start';

	/**
	 * End marker for debug settings block.
	 *
	 * @since 1.5.0
	 */
	const BLOCK_END = '// gk-foundation-end';

	/**
	 * Debug log filename.
	 *
	 * @since 1.5.0
	 */
	const LOG_FILENAME = 'gravitykit-wp-debug.log';


	/**
	 * Constructor.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->_wp_config_path = $this->locate_wp_config();

		add_filter( 'gk/foundation/settings', [ $this, 'register_settings' ], 20, 2 );
		add_filter( 'gk/foundation/settings/gk_foundation/save/before', [ $this, 'handle_settings_save' ] );
	}

	/**
	 * Registers WP Debug Settings section.
	 *
	 * @since 1.5.0
	 * @since 1.6.0 Added $payload parameter.
	 *
	 * @param array<string, mixed> $gk_settings GravityKit settings array.
	 * @param array                $payload     Request payload, if this is an Ajax request.
	 *
	 * @return array<string, mixed> Modified settings array.
	 */
	public function register_settings( $gk_settings, $payload = [] ) {
		// On multisite, restrict to super admins on main site only.
		// This is because wp-config.php is global and affects all sites.
		if ( is_multisite() && ( ! is_main_site() || ! is_super_admin() ) ) {
			return $gk_settings;
		}

		// Show if GK_FOUNDATION_DEBUG constant is defined and true.
		$show_debug_settings = Core::is_foundation_debug();

		if ( ! $show_debug_settings && current_user_can( 'manage_options' ) ) {
			// Show if `?wp_debug` parameter is present.
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['wp_debug'] ) || isset( $payload['extra']['wp_debug'] ) ) {
				$show_debug_settings = true;
			}
			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			// Show if current user has a @gravitykit.com email address.
			if ( ! $show_debug_settings ) {
				$current_user = wp_get_current_user();

				if ( $current_user->user_email && preg_match( '/@gravitykit\.com$/', $current_user->user_email ) ) {
					$show_debug_settings = true;
				}
			}
		}

		if ( ! $show_debug_settings ) {
			return $gk_settings;
		}

		// Only show section if wp-config.php is accessible.
		if ( ! $this->_wp_config_path || ! $this->is_wp_config_accessible() ) {
			return $gk_settings;
		}

		$debug_status = $this->get_debug_status();

		$fields = [];

		if ( $this->is_wp_config_writable() ) {
			// Check if debug was enabled by host.
			if ( $debug_status['is_enabled'] && ! $debug_status['enabled_by_foundation'] ) {
				$this->add_log_info_fields( $fields, $debug_status, false );
			} else {
				// Enable/disable toggle (only show if not enabled by host).
				$fields[] = [
					'id'    => 'wp_debug_enabled',
					'type'  => 'checkbox',
					'title' => esc_html__( 'Enable WordPress Debug Logging', 'gk-gravityedit' ),
					'value' => $debug_status['is_enabled'],
				];

				// Log file info (only show when enabled).
				if ( $debug_status['is_enabled'] && $debug_status['log_path'] ) {
					$fields[] = $this->build_log_file_info( $debug_status['log_path'] );
				}
			}
		} else {
			// wp-config.php is not writable.
			if ( ! $debug_status['is_enabled'] ) {
				// Show read-only notice when debug is disabled.
				$fields[] = [
					'id'   => 'wp_debug_readonly',
					'html' => $this->build_readonly_notice(),
				];
			} else {
				// Debug is enabled in read-only mode - show log info.
				$this->add_log_info_fields( $fields, $debug_status, false );
			}
		}

		// Add WP Debug section to the end of sections.
		$sections = Arr::get( $gk_settings, 'gk_foundation.sections', [] );

		// Add WP Debug section at the end.
		$sections[] = [
			'id'       => 'wp_debug',
			'title'    => esc_html__( 'WP Debug', 'gk-gravityedit' ),
			'settings' => $fields,
		];

		Arr::set( $gk_settings, 'gk_foundation.sections', $sections );

		add_filter(
			'gk/foundation/settings/data/extra',
			function ( $extra ) {
				$extra['wp_debug'] = true;

				return $extra;
			}
		);

		return $gk_settings;
	}

	/**
	 * Handle settings save.
	 *
	 * @since 1.5.0
	 *
	 * @param array<string, mixed> $new_settings Settings being saved.
	 *
	 * @throws Exception
	 *
	 * @return array<string, mixed> Potentially modified settings.
	 */
	public function handle_settings_save( $new_settings ) {
		// This is an additional security check even if UI was bypassed.
		if ( is_multisite() && ! is_super_admin() ) {
			unset( $new_settings['wp_debug_enabled'] );

			return $new_settings;
		}

		// Check if wp_debug_enabled setting is present.
		if ( ! isset( $new_settings['wp_debug_enabled'] ) ) {
			return $new_settings;
		}

		// Re-check that wp-config.php is writable at save time (avoid race conditions).
		if ( ! $this->is_wp_config_writable() ) {
			unset( $new_settings['wp_debug_enabled'] );

			return $new_settings;
		}

		$enable_debug   = ! empty( $new_settings['wp_debug_enabled'] );
		$current_status = $this->get_debug_status();

		// Only act if state is changing.
		if ( $enable_debug === $current_status['is_enabled'] ) {
			return $new_settings;
		}

		// If disabling, but we didn't enable it, don't disable.
		if ( ! $enable_debug && ! $current_status['enabled_by_foundation'] ) {
			// Don't disable if we didn't enable it.
			unset( $new_settings['wp_debug_enabled'] );

			return $new_settings;
		}

		// Make the change.
		if ( $enable_debug ) {
			$this->enable_debug_mode();
		} else {
			$this->disable_debug_mode();
		}

		return $new_settings;
	}

	/**
	 * Enables debug mode.
	 *
	 * @since 1.5.0
	 *
	 * @throws Exception If operation fails.
	 *
	 * @return void
	 */
	private function enable_debug_mode() {
		if ( ! $this->_wp_config_path ) {
			throw new Exception( 'wp-config.php path not found' );
		}

		$config_content = file_get_contents( $this->_wp_config_path );

		if ( false === $config_content ) {
			throw new Exception( 'Failed to read wp-config.php' );
		}

		// Prepare debug log path.
		$log_path = $this->get_debug_log_path();

		// Step 1: Comment out existing debug constants.
		$config_content = $this->comment_out_existing_constants( $config_content );

		// Step 2: Add our new debug constants.
		$config_content = $this->add_new_debug_constants( $config_content, $log_path );

		// Write changes with file locking.
		if ( false === file_put_contents( $this->_wp_config_path, $config_content, LOCK_EX ) ) {
			throw new Exception( 'Failed to write to wp-config.php' );
		}

		// Verify site health after modification.
		if ( ! Core::is_site_accessible() ) {
			// Site is not accessible - revert the changes immediately.
			$config_content = file_get_contents( $this->_wp_config_path );
			if ( false !== $config_content ) {
				$config_content = $this->remove_added_constants( $config_content );
				$config_content = $this->uncomment_original_constants( $config_content );
			}

			file_put_contents( $this->_wp_config_path, $config_content, LOCK_EX );

			throw new Exception( 'Site became inaccessible after enabling debug mode. Changes have been reverted.' );
		}
	}

	/**
	 * Disables debug mode.
	 *
	 * @since 1.5.0
	 *
	 * @throws Exception If operation fails.
	 *
	 * @return void
	 */
	private function disable_debug_mode() {
		if ( ! $this->_wp_config_path ) {
			throw new Exception( 'wp-config.php path not found' );
		}

		$config_content = file_get_contents( $this->_wp_config_path );

		if ( false === $config_content ) {
			throw new Exception( 'Failed to read wp-config.php' );
		}

		// Store the log path before making changes so we can delete it regardless of outcome.
		$log_path = $this->get_debug_log_path();

		try {
			// Step 1: Remove our added constants.
			$config_content = $this->remove_added_constants( $config_content );

			// Step 2: Uncomment original constants.
			$config_content = $this->uncomment_original_constants( $config_content );

			// Write changes with file locking.
			if ( false === file_put_contents( $this->_wp_config_path, $config_content, LOCK_EX ) ) {
				throw new Exception( 'Failed to write to wp-config.php' );
			}

			// Verify site health after modification.
			if ( ! Core::is_site_accessible() ) {
				// Site is not accessible - revert the changes immediately.
				$config_content = file_get_contents( $this->_wp_config_path );

				if ( false !== $config_content ) {
					$config_content = $this->comment_out_existing_constants( $config_content );
					$config_content = $this->add_new_debug_constants( $config_content, $log_path );
				}

				file_put_contents( $this->_wp_config_path, $config_content, LOCK_EX );

				throw new Exception( 'Site became inaccessible after disabling debug mode. Changes have been reverted.' );
			}
		} finally {
			// Always attempt to delete the log file, even if an exception occurred.
			// This ensures the log is cleaned up regardless of whether the wp-config changes succeeded.
			if ( file_exists( $log_path ) ) {
				wp_delete_file( $log_path );
			}
		}
	}

	/**
	 * Comments out existing debug constants.
	 *
	 * @since 1.5.0
	 *
	 * @param string $content wp-config.php content.
	 *
	 * @return string Modified content.
	 */
	private function comment_out_existing_constants( $content ) {
		if ( ! $content ) {
			return '';
		}

		$constants = [ 'WP_DEBUG', 'WP_DEBUG_LOG' ];

		foreach ( $constants as $constant ) {
			/**
			 * Pattern breakdown:
			 *
			 * - ^(\s*)                  → Capture any leading whitespace at the start of the line.
			 * - (define\s*\(\s*['"]     → Match the word "define", optional spaces, an opening parenthesis,
			 *                             then an opening quote (single or double).
			 * - CONSTANT                → The constant name, inserted via preg_quote().
			 * - ['"]                    → Closing quote for the constant name.
			 * - .*?\)\s*;               → Lazily match everything up to closing parenthesis and semicolon.
			 * - .*?                     → Match anything else on the same line (e.g. inline comments).
			 * - $                       → Anchor at the end of the line.
			 *
			 * Flags:
			 * - m (multiline)           → ^ and $ match the start and end of each line.
			 *
			 * Capturing groups:
			 * - Group 1 → Leading whitespace (indentation).
			 * - Group 2 → The entire `define()` statement plus any trailing inline comment.
			 *
			 * @since 1.5.0
			 *
			 * @param string $constant The constant name to match.
			 *
			 * @return string The regex pattern.
			 */
			$pattern = "/^(\s*)(define\s*\(\s*['\"]" . preg_quote( $constant, '/' ) . "['\"].*?\)\s*;.*?)$/m";

			// Replace with commented version + marker.
			$replacement = '$1// $2 // ' . self::MARKER_MODIFIED;

			$result = preg_replace( $pattern, $replacement, $content );

			if ( null !== $result ) {
				$content = $result;
			}
		}

		return $content;
	}

	/**
	 * Adds new debug constants with our values.
	 *
	 * @since 1.5.0
	 *
	 * @param string $content  wp-config.php content.
	 * @param string $log_path Path for debug log file.
	 *
	 * @return string Modified content.
	 */
	private function add_new_debug_constants( $content, $log_path ) {
		// Build the constants block.
		$constants_block  = "\n";
		$constants_block .= self::BLOCK_BEGIN . "\n";
		$constants_block .= "define( 'WP_DEBUG', true ); // " . self::MARKER_ADDED . "\n";
		$constants_block .= "define( 'WP_DEBUG_LOG', '" . addslashes( $log_path ) . "' ); // " . self::MARKER_ADDED . "\n";
		$constants_block .= self::BLOCK_END . "\n\n";

		// Find insertion point (before require_once or "That's all" comment).
		$inserted = false;

		// Try to insert before "That's all" comment.
		$patterns = [
			'/\/\*\s*That\'s all.*?stop editing.*?\*\//i',
			'/require_once\s+.*?wp-settings\.php.*;/i',
		];

		foreach ( $patterns as $pattern ) {
			if ( preg_match( $pattern, $content, $matches, PREG_OFFSET_CAPTURE ) ) {
				$position = $matches[0][1];
				$content  = substr( $content, 0, $position ) . $constants_block . substr( $content, $position );
				$inserted = true;

				break;
			}
		}

		if ( ! $inserted ) {
			// If we couldn't find an insertion point, add at the end.
			$content .= $constants_block;
		}

		return $content;
	}

	/**
	 * Removes constants we added.
	 *
	 * @since 1.5.0
	 *
	 * @param string $content wp-config.php content.
	 *
	 * @return string Modified content.
	 */
	private function remove_added_constants( $content ) {
		if ( ! $content ) {
			return '';
		}
		// Remove the entire BEGIN/END block.
		$pattern = '/\n?' . preg_quote( self::BLOCK_BEGIN, '/' ) . '.*?' . preg_quote( self::BLOCK_END, '/' ) . '\n*/s';
		$content = preg_replace( $pattern, '', $content );
		if ( null === $content ) {
			return '';
		}

		// Also remove any individual lines marked as added (in case block markers are missing).
		$pattern = '/.*\/\/ ' . preg_quote( self::MARKER_ADDED, '/' ) . '\s*\n/';
		$result  = preg_replace( $pattern, '', $content );

		return null === $result ? '' : $result;
	}

	/**
	 * Uncomments original constants that we commented out.
	 *
	 * @since 1.5.0
	 *
	 * @param string $content wp-config.php content.
	 *
	 * @return string Modified content.
	 */
	private function uncomment_original_constants( $content ) {
		if ( ! $content ) {
			return '';
		}
		// Pattern to match commented lines with our marker.
		// Matches: // define( 'CONSTANT', value ) ; // Foo // gk-foundation-modified.
		$pattern = '/^(\s*)\/\/\s*(define\s*\([^)]+\)\s*;.*?)\s*\/\/\s*' . preg_quote( self::MARKER_MODIFIED, '/' ) . '$/m';

		// Replace with uncommented version (preserving original formatting).
		$replacement = '$1$2';
		$result      = preg_replace( $pattern, $replacement, $content );

		return null === $result ? $content : $result;
	}

	/**
	 * Returns the current debug mode status.
	 *
	 * @since 1.5.0
	 *
	 * @return array{is_enabled: bool, log_enabled: bool, enabled_by_foundation: bool, log_path: string|null} Status information.
	 */
	private function get_debug_status() {
		// Check if our markers exist in wp-config.php.
		$enabled_by_foundation = false;

		if ( $this->_wp_config_path && file_exists( $this->_wp_config_path ) ) {
			$content = file_get_contents( $this->_wp_config_path );

			if ( false !== $content &&
			     ( false !== strpos( $content, '// ' . self::MARKER_ADDED ) ||
			       false !== strpos( $content, self::BLOCK_BEGIN ) ) ) {
				$enabled_by_foundation = true;
			}
		}

		return [
			'is_enabled'            => defined( 'WP_DEBUG' ) && WP_DEBUG,
			'log_enabled'           => defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG,
			'enabled_by_foundation' => $enabled_by_foundation,
			'log_path'              => $this->get_current_log_path(),
		];
	}

	/**
	 * Returns the debug log path.
	 *
	 * @since 1.5.0
	 *
	 * @return string Log file path.
	 */
	private function get_debug_log_path() {
		$log_path = apply_filters( 'gk/foundation/logger/log-path', 'logs' );
		$log_dir  = WP_CONTENT_DIR . '/' . ltrim( $log_path, '/' );

		// Create directory if it doesn't exist.
		if ( ! file_exists( $log_dir ) ) {
			wp_mkdir_p( $log_dir );
		}

		return $log_dir . '/' . self::LOG_FILENAME;
	}

	/**
	 * Gets the current debug log path if debugging is enabled.
	 *
	 * @since 1.5.0
	 *
	 * @return string|null Current log path or null if not set.
	 */
	private function get_current_log_path() {
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			// WP_DEBUG_LOG can be either true or a string path.
			// @phpstan-ignore-next-line - WP_DEBUG_LOG can be bool or string.
			if ( is_string( WP_DEBUG_LOG ) ) {
				return WP_DEBUG_LOG;
			}

			// If it's true (not a string path), use default location.
			return WP_CONTENT_DIR . '/debug.log';
		}

		// Check if Foundation enabled debugging by looking for our markers.
		if ( $this->_wp_config_path && file_exists( $this->_wp_config_path ) ) {
			$content = file_get_contents( $this->_wp_config_path );

			if ( false !== $content && false !== strpos( $content, '// ' . self::MARKER_ADDED ) ) {
				// We enabled it, so return our standard log path.
				return $this->get_debug_log_path();
			}
		}

		return null;
	}

	/**
	 * Locates wp-config.php file.
	 *
	 * @since 1.5.0
	 *
	 * @return string|null Path to wp-config.php or null if not found.
	 */
	private function locate_wp_config() {
		// ABSPATH is always a string in WordPress.
		$wp_root = (string) ABSPATH;

		// Check in WP root.
		if ( file_exists( $wp_root . 'wp-config.php' ) ) {
			return $wp_root . 'wp-config.php';
		}

		// Check one level up from WP root.
		if ( file_exists( dirname( $wp_root ) . '/wp-config.php' ) ) {
			return dirname( $wp_root ) . '/wp-config.php';
		}

		return null;
	}

	/**
	 * Checks if wp-config.php is accessible.
	 *
	 * @since 1.5.0
	 *
	 * @return bool True if accessible.
	 */
	private function is_wp_config_accessible() {
		return $this->_wp_config_path && file_exists( $this->_wp_config_path ) && is_readable( $this->_wp_config_path );
	}

	/**
	 * Checks if wp-config.php is writable.
	 *
	 * @since 1.5.0
	 *
	 * @return bool True if writable.
	 */
	private function is_wp_config_writable() {
		return $this->_wp_config_path && is_writable( $this->_wp_config_path );
	}

	/**
	 * Builds log file info message.
	 *
	 * @since 1.5.0
	 *
	 * @param string $log_path          Path to log file.
	 * @param bool   $nest_under_toggle Whether to nest under the toggle (default true).
	 *
	 * @return array<string, mixed> Settings field array.
	 */
	private function build_log_file_info( $log_path, $nest_under_toggle = true ) {
		if ( ! file_exists( $log_path ) ) {
			$message = esc_html__( 'The log file is empty.', 'gk-gravityedit' );
			$color   = 'blue';
		} elseif ( ! is_readable( $log_path ) ) {
			// File exists but is not readable.
			$message = esc_html__( 'Debug log file exists but is not accessible.', 'gk-gravityedit' );
			$color   = 'yellow';
		} else {
			$download_link = $this->get_secure_download_url( $log_path );

			if ( $download_link ) {
				$file_size     = size_format( filesize( $log_path ) ?: 0, 2 );
				$file_modified = date_i18n( 'Y-m-d @ H:i:s', filemtime( $log_path ) );

				$message = strtr(
					// Translators: Do not translate the placeholders inside [].
					esc_html__( 'Download [link]debug log[/link] ([size] / [date_modified]).', 'gk-gravityedit' ),
					[
						'[link]'          => '<a href="' . esc_url( $download_link ) . '" class="font-medium underline text-blue-700 hover:text-blue-600">',
						'[/link]'         => '</a>',
						'[size]'          => $file_size,
						'[date_modified]' => $file_modified,
					]
				);
				$color = 'blue';
			} else {
				// Secure download link could not be generated.
				$message = esc_html__( 'Debug log file exists but download link could not be generated.', 'gk-gravityedit' );
				$color   = 'yellow';
			}
		}

		$field = [
			'id'   => 'wp_debug_log_info',
			'html' => $this->format_notice( $message, $color ),
		];

		// Only add nestUnder if we're showing the toggle.
		if ( $nest_under_toggle ) {
			$field['nestUnder'] = 'wp_debug_enabled';
		}

		return $field;
	}

	/**
	 * Generates a secure download URL for the debug log file.
	 *
	 * @since 1.5.0
	 *
	 * @param string $file_path Path to the log file.
	 *
	 * @return string The secure download URL or an empty string.
	 */
	private function get_secure_download_url( $file_path ) {
		try {
			$result = SecureDownload::get_instance()->generate_download_url(
				$file_path,
				[
					'capabilities'   => [ 'manage_options' ],
					'filename'       => basename( $file_path ),
					'cache_duration' => 0,
				]
			);

			return $result['url'];
		} catch ( Exception $e ) {
			// Return empty string if secure download fails.
			return '';
		}
	}

	/**
	 * Helper method that adds log info fields to the settings array.
	 *
	 * @since 1.5.0
	 *
	 * @param array<int, array<string, mixed>> $fields       Array to add fields to (passed by reference).
	 * @param array<string, mixed>             $debug_status Current debug status.
	 * @param bool                             $nest_under_toggle Whether to nest under toggle.
	 *
	 * @return void
	 */
	private function add_log_info_fields( &$fields, $debug_status, $nest_under_toggle = true ) {
		// If no log path, use default path.
		$log_path = is_string( $debug_status['log_path'] ) ? $debug_status['log_path'] : WP_CONTENT_DIR . '/debug.log';

		if ( file_exists( $log_path ) || null === $debug_status['log_path'] ) {
			// Either file exists or we're showing info for where it will be created.
			$fields[] = $this->build_log_file_info( $log_path, $nest_under_toggle );
		} else {
			// Debug is enabled but no log file exists yet at the custom path.
			$message = esc_html__( 'WordPress debugging is enabled by the host, but the log file is empty.', 'gk-gravityedit' );

			$fields[] = [
				'id'   => 'wp_debug_no_log_notice',
				'html' => $this->format_notice( $message, 'gray' ),
			];
		}
	}

	/**
	 * Builds notice when wp-config.php is not writable.
	 *
	 * @since 1.5.0
	 *
	 * @return string HTML for read-only notice.
	 */
	private function build_readonly_notice() {
		$message = strtr(
			esc_html__( 'The [code]wp-config.php[/code] file is not writable. Debug settings cannot be modified.', 'gk-gravityedit' ),
			[
				'[code]'  => '<code>',
				'[/code]' => '</code>',
			]
		);

		return $this->format_notice( $message, 'yellow' );
	}

	/**
	 * Formats a notice message with appropriate styling.
	 *
	 * @todo  Update the Settings UI to include these styles.
	 *
	 * @since 1.5.0
	 *
	 * @param string $message Notice message.
	 * @param string $color   Color theme (blue, green, yellow, red, gray).
	 *
	 * @return string Formatted HTML.
	 */
	private function format_notice( $message, $color = 'blue' ) {
		$icon = $this->get_notice_icon( $color );

		// Define color styles inline since we can't add CSS classes.
		$colors = [
			'gray'   => [
				'bg'   => 'rgba(249, 250, 251, 1)',
				'text' => 'rgba(55, 65, 81, 1)',
			],
			'blue'   => [
				'bg'   => 'rgba(239, 246, 255, 1)',
				'text' => 'rgba(29, 78, 216, 1)',
			],
			'green'  => [
				'bg'   => 'rgba(236, 253, 245, 1)',
				'text' => 'rgba(21, 128, 61, 1)',
			],
			'yellow' => [
				'bg'   => 'rgba(254, 252, 232, 1)',
				'text' => 'rgba(161, 98, 7, 1)',
			],
			'red'    => [
				'bg'   => 'rgba(254, 242, 242, 1)',
				'text' => 'rgba(185, 28, 28, 1)',
			],
		];

		$bg_color   = isset( $colors[ $color ] ) ? $colors[ $color ]['bg'] : $colors['blue']['bg'];
		$text_color = isset( $colors[ $color ] ) ? $colors[ $color ]['text'] : $colors['blue']['text'];

		return <<<HTML
<div style="background-color: {$bg_color}; padding: 1rem; border-radius: 0.375rem;">
	<div style="display: flex;">
		<div style="flex-shrink: 0;">
			{$icon}
		</div>
		<div style="margin-left: 0.75rem;">
			<p style="font-size: 0.875rem; line-height: 1.25rem; color: {$text_color}; margin: 0;">
				{$message}
			</p>
		</div>
	</div>
</div>
HTML;
	}

	/**
	 * Returns appropriate icon for notice type.
	 *
	 * @since 1.5.0
	 *
	 * @param string $color Color theme.
	 *
	 * @return string SVG icon HTML.
	 */
	private function get_notice_icon( $color ) {
		switch ( $color ) {
			case 'green':
				// Checkmark.
				return '<svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
					<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
				</svg>';
			case 'yellow':
			case 'red':
				// Warning.
				return '<svg class="h-5 w-5 text-' . $color . '-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
					<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
				</svg>';
			default:
				// Info.
				return '<svg class="h-5 w-5 text-' . $color . '-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
					<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
				</svg>';
		}
	}
}
