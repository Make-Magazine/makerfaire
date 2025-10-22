<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 16-October-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Logger;

use Exception;
use GravityKit\GravityEdit\Foundation\Core as FoundationCore;
use GravityKit\GravityEdit\Foundation\Helpers\Arr;
use GravityKit\GravityEdit\Foundation\Helpers\WP;
use GravityKit\GravityEdit\Foundation\Settings\Framework as SettingsFramework;
use GravityKit\GravityEdit\Foundation\Components\SecureDownload;

/**
 * Settings management for the logging framework.
 *
 * @since 1.3.0
 */
class Settings {
	/**
	 * Settings framework instance.
	 *
	 * @since 1.3.0
	 *
	 * @var SettingsFramework
	 */
	private $_settings_framework;

	/**
	 * Logger framework instance.
	 *
	 * @since 1.3.0
	 *
	 * @var Framework
	 */
	private $_logger_framework;

	/**
	 * Default logger settings.
	 *
	 * @since 1.3.0
	 *
	 * @var array
	 */
	const DEFAULT_SETTINGS = [
		'logger'                 => 0,
		'logger_type'            => 'file',
		'logger_level'           => 'warning',
		'logger_max_files'       => 7,
		'logger_rotation_period' => 'Y-m-d',
	];

	/**
	 * Maximum number of log files to display in the UI.
	 *
	 * @since 1.3.0
	 *
	 * @var int
	 */
	const MAX_DISPLAY_FILES = 10;

	/**
	 * Logger-related setting IDs.
	 *
	 * @since 1.3.0
	 *
	 * @var array
	 */
	const LOGGER_SETTING_IDS = [ 'logger', 'logger_type', 'logger_level', 'logger_level_critical_notice', 'logger_level_warning_notice', 'logger_level_notice_notice', 'logger_level_info_notice', 'logger_level_debug_notice', 'chrome_logger_tip', 'query_monitor_notice', 'logger_rotation_period', 'logger_max_files', 'log_file', 'log_migration_notice', 'gravity_forms_logger_tip' ];

	/**
	 * Class constructor.
	 *
	 * @since 1.3.0
	 *
	 * @param Framework $logger_framework Logger framework instance.
	 *
	 * @return void
	 */
	public function __construct( Framework $logger_framework ) {
		$this->_logger_framework   = $logger_framework;
		$this->_settings_framework = SettingsFramework::get_instance();

		// Register hooks for settings integration.
		add_filter( 'gk/foundation/settings/' . FoundationCore::ID . '/save/before', [ $this, 'save_settings' ] );
		add_filter( 'gk/foundation/settings', [ $this, 'get_settings' ] );
	}

	/**
	 * Returns logger settings.
	 *
	 * @since 1.3.0
	 *
	 * @param array $gk_settings GravityKit general settings object.
	 *
	 * @return array
	 */
	public function get_settings( $gk_settings ) {
		$existing_settings = Arr::get( $gk_settings, 'gk_foundation.sections.2.settings', [] );

		/**
		 * Remove any existing logger settings before adding our own.
		 *
		 * This handles a specific issue where plugins may instantiate their local
		 * Foundation Logger class directly instead of using the global GravityKitFoundation::logger()
		 * instance due to it not yet being available during the request lifecycle.
		 *
		 * 1. A plugin uses LoggerFramework::get_instance() from its bundled Foundation
		 * 2. This registers the 'gk/foundation/settings' filter with basic logger settings
		 * 3. Later, the winning Foundation instance (latest version) also registers its filter
		 * 4. Both filters run, resulting in duplicate logger settings
		 *
		 * The root cause is that the guard against duplicate filter registration only works within
		 * a single Foundation instance. When plugins use their local Foundation classes before the
		 * global instance is available, they bypass this protection.
		 *
		 * This was fixed by moving the filter registration to the Settings class constructor, but
		 * we need this for backwards compatibility with older Foundation versions.
		 */
		$filtered_settings = array_filter(
            $existing_settings,
            function( $setting ) {
				return ! isset( $setting['id'] ) || ! in_array( $setting['id'], self::LOGGER_SETTING_IDS, true );
			}
        );

		Arr::set( $gk_settings, 'gk_foundation.sections.2.settings', array_values( $filtered_settings ) );

		$logger_setting_values = $this->_settings_framework->get_plugin_settings( FoundationCore::ID );

		// If multisite and not the main site, get default settings from the main site.
		if ( ! is_main_site() && empty( $logger_setting_values ) ) {
			$logger_setting_values = $this->_settings_framework->get_plugin_settings( FoundationCore::ID, get_main_site_id() );
		}

		$logger_setting_values = wp_parse_args( $logger_setting_values, self::DEFAULT_SETTINGS );

		$this->add_inline_styles();

		$logger_settings = $this->build_base_settings( $logger_setting_values );

		$log_file_settings = $this->build_log_file_settings( $this->_logger_framework->get_log_file() );

		if ( $log_file_settings ) {
			$logger_settings[] = $log_file_settings;
		}

		$this->update_gk_settings( $gk_settings, $logger_settings );

		return $gk_settings;
	}

	/**
	 * Builds the base logger settings.
	 *
	 * @since 1.3.0
	 *
	 * @param array $saved_settings Saved logger settings values.
	 *
	 * @return array
	 */
	private function build_base_settings( $saved_settings ) {
		$logger_enabled = Arr::get( $saved_settings, 'logger', self::DEFAULT_SETTINGS['logger'] );
		$logger_type    = Arr::get( $saved_settings, 'logger_type', self::DEFAULT_SETTINGS['logger_type'] );

		$base_settings = [];

		// Add Gravity Forms notice if applicable.
		if ( ! $logger_enabled && class_exists( 'GFLogging' ) && get_option( 'gform_enable_logging' ) ) {
			$base_settings[] = $this->get_gravity_forms_notice();
		}

		// Basic logger settings.
		$base_settings[] = [
			'id'    => 'logger',
			'type'  => 'checkbox',
			'title' => esc_html__( 'Enable Logging', 'gk-gravityedit' ),
			'value' => $logger_enabled,
		];

		$base_settings[] = [
			'id'          => 'logger_level',
			'type'        => 'select',
			'title'       => esc_html__( 'Log Level', 'gk-gravityedit' ),
			'description' => esc_html__( 'What severity of events to log.', 'gk-gravityedit' ),
			'value'       => Arr::get( $saved_settings, 'logger_level', self::DEFAULT_SETTINGS['logger_level'] ),
			'choices'     => $this->get_log_level_choices(),
			'requires'    => [
				'id'       => 'logger',
				'operator' => '=',
				'value'    => '1',
			],
		];

		$base_settings[] = [
			'id'          => 'logger_type',
			'type'        => 'select',
			'title'       => esc_html__( 'Log Type', 'gk-gravityedit' ),
			'description' => esc_html__( 'Where to store log output.', 'gk-gravityedit' ),
			'value'       => $logger_type,
			'choices'     => [
				[
					'title' => esc_html__( 'File', 'gk-gravityedit' ),
					'value' => 'file',
				],
				[
					'title' => esc_html__( 'Query Monitor', 'gk-gravityedit' ),
					'value' => 'query_monitor',
				],
				[
					'title' => esc_html__( 'Chrome Logger', 'gk-gravityedit' ),
					'value' => 'chrome_logger',
				],
			],
			'requires'    => [
				'id'       => 'logger',
				'operator' => '=',
				'value'    => '1',
			],
		];

		// Add log level notices explaining the various settings.
		$base_settings = array_merge( $base_settings, $this->get_log_level_notices() );

		// Add rotation settings for file logger.
		$base_settings = array_merge( $base_settings, $this->get_rotation_settings( $saved_settings ) );

		// Add handler-specific notices.
		$base_settings = array_merge( $base_settings, $this->get_handler_notices() );

		return $base_settings;
	}

	/**
	 * Returns log level choices for the settings UI.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	private function get_log_level_choices() {
		return [
			[
				'title' => esc_html__( 'Minimal (Critical Issues Only)', 'gk-gravityedit' ),
				'value' => 'critical',
			],
			[
				'title' => esc_html__( 'Standard (Problems & Errors)', 'gk-gravityedit' ),
				'value' => 'warning',
			],
			[
				'title' => esc_html__( 'Detailed (Including Notices)', 'gk-gravityedit' ),
				'value' => 'notice',
			],
			[
				'title' => esc_html__( 'Verbose (All Activity)', 'gk-gravityedit' ),
				'value' => 'info',
			],
			[
				'title' => esc_html__( 'Debug (Everything + Technical Details)', 'gk-gravityedit' ),
				'value' => 'debug',
			],
		];
	}

	/**
	 * Converts log level name to Monolog constant.
	 *
	 * @since 1.3.0
	 *
	 * @param string $level Log level name.
	 *
	 * @return int Monolog log level constant.
	 */
	public static function get_monolog_level( $level ) {
		// Import MonologLogger here to avoid early loading issues.
		$monolog_logger_class = 'GravityKit\\GravityEdit\\Foundation\\ThirdParty\\Monolog\\Logger';

		if ( ! class_exists( $monolog_logger_class ) ) {
			// Fallback to a reasonable default if Monolog isn't available.
			return 300; // WARNING level.
		}

		$level_mapping = [
			'debug'     => $monolog_logger_class::DEBUG,     // 100
			'info'      => $monolog_logger_class::INFO,      // 200
			'notice'    => $monolog_logger_class::NOTICE,    // 250
			'warning'   => $monolog_logger_class::WARNING,   // 300
			'error'     => $monolog_logger_class::ERROR,     // 400
			'critical'  => $monolog_logger_class::CRITICAL,  // 500
			'alert'     => $monolog_logger_class::ALERT,     // 550
			'emergency' => $monolog_logger_class::EMERGENCY, // 600
		];

		return $level_mapping[ $level ] ?? $monolog_logger_class::WARNING;
	}

	/**
	 * Returns rotation settings for file logger.
	 *
	 * @since 1.3.0
	 *
	 * @param array $saved_settings Saved settings values.
	 *
	 * @return array
	 */
	private function get_rotation_settings( $saved_settings ) {
		$rotation_choices = [
			[
				'title' => esc_html__( 'Daily', 'gk-gravityedit' ),
				'value' => 'Y-m-d',
			],
		];

		// Only show weekly option if supported.
		if ( class_exists( __NAMESPACE__ . '\WeeklyRotatingFileHandler' ) ) {
			$rotation_choices[] = [
				'title' => esc_html__( 'Weekly', 'gk-gravityedit' ),
				'value' => 'Y-\WW',
			];
		}

		$rotation_choices[] = [
			'title' => esc_html__( 'Monthly', 'gk-gravityedit' ),
			'value' => 'Y-m',
		];

		$rotation_choices[] = [
			'title' => esc_html__( 'Yearly', 'gk-gravityedit' ),
			'value' => 'Y',
		];

		$saved_rotation_period = Arr::get( $saved_settings, 'logger_rotation_period', self::DEFAULT_SETTINGS['logger_rotation_period'] );

		// If weekly is selected but not supported, fallback to daily.
		if ( 'Y-\WW' === $saved_rotation_period && ! class_exists( __NAMESPACE__ . '\WeeklyRotatingFileHandler' ) ) {
			$saved_rotation_period = self::DEFAULT_SETTINGS['logger_rotation_period'];
		}

		return [
			[
				'id'          => 'logger_rotation_period',
				'type'        => 'select',
				'title'       => esc_html__( 'Log cleanup schedule', 'gk-gravityedit' ),
				'description' => esc_html__( 'How often to start a new log file. Previous log files will be kept according to the retention setting below.', 'gk-gravityedit' ),
				'value'       => $saved_rotation_period,
				'choices'     => $rotation_choices,
				'requires'    => [
					[
						'id'       => 'logger',
						'operator' => '=',
						'value'    => '1',
					],
					[
						'id'       => 'logger_type',
						'operator' => '=',
						'value'    => 'file',
					],
				],
			],
			[
				'id'          => 'logger_max_files',
				'type'        => 'select',
				'title'       => esc_html__( 'Number of log files to keep', 'gk-gravityedit' ),
				'description' => esc_html__( 'Maximum number of log files to keep. Older files will be automatically deleted to save disk space.', 'gk-gravityedit' ),
				'value'       => (string) Arr::get( $saved_settings, 'logger_max_files', self::DEFAULT_SETTINGS['logger_max_files'] ),
				'choices'     => [
					[
						'title' => esc_html__( '3 files', 'gk-gravityedit' ),
						'value' => '3',
					],
					[
						'title' => esc_html__( '7 files', 'gk-gravityedit' ),
						'value' => '7',
					],
					[
						'title' => esc_html__( '14 files', 'gk-gravityedit' ),
						'value' => '14',
					],
					[
						'title' => esc_html__( '30 files', 'gk-gravityedit' ),
						'value' => '30',
					],
					[
						'title' => esc_html__( 'Keep all files', 'gk-gravityedit' ),
						'value' => '0',
					],
				],
				'requires'    => [
					[
						'id'       => 'logger',
						'operator' => '=',
						'value'    => '1',
					],
					[
						'id'       => 'logger_type',
						'operator' => '=',
						'value'    => 'file',
					],
				],
			],
		];
	}

	/**
	 * Builds log file download settings.
	 *
	 * @since 1.3.0
	 *
	 * @param string $log_file Path to the log file.
	 *
	 * @return array|null
	 */
	private function build_log_file_settings( $log_file ) {
		$log_dir      = dirname( $log_file );
		$log_basename = basename( $log_file, '.log' );

		// Check for migration notice.
		$migration_notice = $this->get_migration_notice();

		if ( $migration_notice ) {
			return $migration_notice;
		}

		// Get the current rotation period setting.
		$logger_settings = $this->_settings_framework->get_plugin_settings( FoundationCore::ID );
		$rotation_period = isset( $logger_settings['logger_rotation_period'] ) ? $logger_settings['logger_rotation_period'] : 'Y-m-d';

		// Find the current log file based on rotation period.
		$current_log_file = $log_dir . '/' . $log_basename . '-' . current_time( $rotation_period ) . '.log';

		// Use the current dated log file if it exists, otherwise check for base file.
		if ( file_exists( $current_log_file ) ) {
			$log_file = $current_log_file;
		} elseif ( ! file_exists( $log_file ) ) {
			// Check if any rotated files exist.
			$pattern       = $log_dir . '/' . $log_basename . '-*.log';
			$rotated_files = glob( $pattern );

			if ( ! is_array( $rotated_files ) || count( $rotated_files ) === 0 ) {
				return null;
			}

			// Use the first/newest rotated file for display purposes.
			$log_file = $rotated_files[0];
		}

		// Get download notice HTML.
		$download_notice = $this->get_download_notice_html( $log_file );

		return [
			'id'        => 'log_file',
			'html'      => strtr(
				$this->get_notice_template(),
				[
					'%color%'  => 'blue',
					'%icon%'   => $this->get_checkmark_icon(),
					'%notice%' => $download_notice,
				]
			),
			'requires'  => [
				[
					'id'       => 'logger',
					'operator' => '=',
					'value'    => '1',
				],
				[
					'id'       => 'logger_type',
					'operator' => '=',
					'value'    => 'file',
				],
			],
			'nestUnder' => 'logger_max_files',
		];
	}

	/**
	 * Generates the download notice HTML for log files.
	 *
	 * @since 1.3.0
	 *
	 * @param string $log_file Current log file path.
	 *
	 * @return string
	 */
	private function get_download_notice_html( $log_file ) {
		$log_dir      = dirname( $log_file );
		$log_basename = basename( $log_file, '.log' );

		// Extract the true base name without any date suffix (daily, weekly, monthly, or yearly format).
		$true_basename = preg_replace( '/-(\d{4}-\d{2}-\d{2}|\d{4}-W\d{2}|\d{4}-\d{2}|\d{4})(-migrated)?$/', '', $log_basename );

		// Get all rotated files.
		$pattern       = $log_dir . '/' . $true_basename . '-*.log';
		$rotated_files = glob( $pattern );

		if ( ! is_array( $rotated_files ) || empty( $rotated_files ) ) {
			return $this->format_single_file_notice( $log_file );
		}

		// If there's only one file, use single file format.
		if ( 1 === count( $rotated_files ) ) {
			return $this->format_single_file_notice( $rotated_files[0] );
		}

		return $this->format_multiple_files_notice( $rotated_files );
	}

	/**
	 * Formats notice for a single log file.
	 *
	 * @since 1.3.0
	 *
	 * @param string $log_file Log file path.
	 *
	 * @return string
	 */
	private function format_single_file_notice( $log_file ) {
		$download_link = $this->get_secure_download_url( $log_file );

		return strtr(
			// Translators: Do not translate the placeholders inside [].
			esc_html__( 'Download [link]log file[/link] ([size] / [date_modified]).', 'gk-gravityedit' ),
			[
				'[link]'          => '<a href="' . esc_url( $download_link ) . '" class="font-medium underline text-blue-700 hover:text-blue-600">',
				'[/link]'         => '</a>',
				'[size]'          => size_format( filesize( $log_file ) ?: 0, 2 ),
				'[date_modified]' => date_i18n( 'Y-m-d @ H:i:s', filemtime( $log_file ) ),
			]
		);
	}

	/**
	 * Formats notice for multiple log files.
	 *
	 * @since 1.3.0
	 *
	 * @param array $rotated_files Array of rotated log files.
	 *
	 * @return string
	 */
	private function format_multiple_files_notice( $rotated_files ) {
		// Sort files by date (newest first), with migrated files last.
		usort(
			$rotated_files,
			function( $a, $b ) {
				// Check if either file is migrated.
				$a_is_migrated = (bool) preg_match( '/-migrated\.log$/', $a );
				$b_is_migrated = (bool) preg_match( '/-migrated\.log$/', $b );

				// If one is migrated and the other isn't, migrated goes last.
				if ( $a_is_migrated && ! $b_is_migrated ) {
					return 1;
				}
				if ( ! $a_is_migrated && $b_is_migrated ) {
					return -1;
				}

				// Extract dates from filenames for comparison.
				$a_date = $this->extract_date_from_filename( $a );
				$b_date = $this->extract_date_from_filename( $b );

				// If we can extract dates, compare them.
				if ( $a_date && $b_date ) {
					// Convert week format to comparable format.
					$a_comparable = str_replace( 'W', '', $a_date );
					$b_comparable = str_replace( 'W', '', $b_date );

					return strcmp( $b_comparable, $a_comparable );
				}

				// Fallback to filename comparison.
				return strcmp( $b, $a );
			}
		);

		$download_links = [];
		$total_size     = 0;
		$total_files    = count( $rotated_files );

		foreach ( $rotated_files as $file ) {
			$total_size += filesize( $file ) ?: 0;
		}

		// Create links for the most recent files only.
		foreach ( array_slice( $rotated_files, 0, self::MAX_DISPLAY_FILES ) as $index => $file ) {
			$file_info        = $this->get_file_info( $file, $index );
			$download_links[] = strtr(
				'<li><a href="[url]" class="font-medium underline text-blue-700 hover:text-blue-600">[text]</a> ([size])</li>',
				[
					'[url]'  => esc_url( $file_info['url'] ),
					'[text]' => $file_info['text'],
					'[size]' => size_format( $file_info['size'] ),
				]
			);
		}

		$list_html = '<ul style="margin-top: 1em;">' . implode( '', $download_links ) . '</ul>';

		return $this->format_files_list_with_summary( $list_html, $total_files, $total_size );
	}

	/**
	 * Gets file information for display.
	 *
	 * @since 1.3.0
	 *
	 * @param string $file  File path.
	 * @param int    $index File index.
	 *
	 * @return array
	 */
	private function get_file_info( $file, $index ) {
		$size = filesize( $file ) ?: 0;

		// Step 1: Determine file type and extract raw date.
		$is_migrated = (bool) preg_match( '/-migrated\.log$/', $file );
		$raw_date    = $this->extract_date_from_filename( $file );

		// Step 2: Format the display text.
		$display_text = $this->format_file_display_text( $raw_date, $is_migrated, $index );

		// Step 3: Generate download URL.
		$download_url = $this->get_secure_download_url( $file );

		return [
			'url'  => $download_url,
			'text' => $display_text,
			'size' => $size,
		];
	}

	/**
	 * Generates a secure download URL for a log file.
	 *
	 * @since 1.3.0
	 *
	 * @param string $file_path Path to the log file.
	 *
	 * @return string The secure download URL or fallback direct URL.
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
			return $this->get_direct_download_url( $file_path );
		}
	}

	/**
	 * Generates a direct download URL (fallback method).
	 *
	 * @since 1.3.0
	 *
	 * @param string $file_path Path to the log file.
	 *
	 * @return string The direct download URL.
	 */
	private function get_direct_download_url( $file_path ) {
		return sprintf(
			'%s/%s/%s',
			content_url(),
			$this->_logger_framework->get_log_path(),
			basename( $file_path )
		);
	}

	/**
	 * Extracts date from log filename.
	 *
	 * @since 1.3.0
	 *
	 * @param string $file File path.
	 *
	 * @return string Raw date string or empty if no date found.
	 */
	private function extract_date_from_filename( $file ) {
		// Try different date patterns.
		$patterns = [
			'/-(\d{4}-\d{2}-\d{2})-migrated\.log$/', // 2025-07-09-migrated.log
			'/-(\d{4}-\d{2}-\d{2})\.log$/',          // 2025-07-09.log
			'/-(\d{4}-W\d{2})-migrated\.log$/',      // 2025-W28-migrated.log
			'/-(\d{4}-W\d{2})\.log$/',               // 2025-W28.log
			'/-(\d{4}-\d{2})-migrated\.log$/',       // 2025-07-migrated.log
			'/-(\d{4}-\d{2})\.log$/',                // 2025-07.log
			'/-(\d{4})-migrated\.log$/',             // 2025-migrated.log
			'/-(\d{4})\.log$/',                      // 2025.log
		];

		foreach ( $patterns as $pattern ) {
			if ( preg_match( $pattern, $file, $matches ) ) {
				return $matches[1];
			}
		}

		return '';
	}

	/**
	 * Formats the display text for a log file.
	 *
	 * @since 1.3.0
	 *
	 * @param string $raw_date    Raw date extracted from filename.
	 * @param bool   $is_migrated Whether this is a migrated file.
	 * @param int    $index       File index (0 = newest).
	 *
	 * @return string Formatted display text.
	 */
	private function format_file_display_text( $raw_date, $is_migrated, $index ) {
		// Handle migrated files.
		if ( $is_migrated ) {
			return strtr(
				'[date] [status]',
				[
					'[date]'   => $raw_date,
					'[status]' => esc_html_x( '(migrated)', 'Indicates the log file that existed before log rotation was enabled.', 'gk-gravityedit' ),
				]
			);
		}

		// Handle current file (newest non-migrated).
		if ( 0 === $index ) {
			return strtr(
				'[date] [status]',
				[
					'[date]'   => $raw_date,
					'[status]' => esc_html_x( '(current)', 'Indicates the log file that is currently being used.', 'gk-gravityedit' ),
				]
			);
		}

		return $raw_date;
	}

	/**
	 * Formats files list with summary.
	 *
	 * @since 1.3.0
	 *
	 * @param string $list_html  HTML list of files.
	 * @param int    $total_files Total number of files.
	 * @param int    $total_size  Total size of all files.
	 *
	 * @return string
	 */
	private function format_files_list_with_summary( $list_html, $total_files, $total_size ) {
		if ( $total_files > self::MAX_DISPLAY_FILES ) {
			$remaining_files = $total_files - self::MAX_DISPLAY_FILES;
			$summary_text    = strtr(
				// Translators: Do not translate the placeholders inside [].
				esc_html__( '[remaining] older files not shown. Total: [count] files, [total_size]', 'gk-gravityedit' ),
				[
					'[remaining]'  => $remaining_files,
					'[count]'      => $total_files,
					'[total_size]' => size_format( $total_size, 2 ),
				]
			);
		} else {
			$summary_text = strtr(
				// Translators: Do not translate the placeholders inside [].
				esc_html__( 'Total size: [total_size]', 'gk-gravityedit' ),
				[
					'[total_size]' => size_format( $total_size, 2 ),
				]
			);
		}

		return strtr(
			'Log files:[list][summary]',
			[
				'[list]'    => $list_html,
				'[summary]' => '<p style="margin-top: 1em;">' . $summary_text . '</p>',
			]
		);
	}

	/**
	 * Gets migration notice if applicable.
	 *
	 * @since 1.3.0
	 *
	 * @return array|null
	 */
	private function get_migration_notice() {
		$migration_data = WP::get_transient( 'gk_foundation_log_migrated' );

		if ( ! $migration_data ) {
			return null;
		}

		$migration_notice = strtr(
			// Translators: Do not translate the placeholders inside [].
			esc_html__( 'Your existing log file ([old_size]) has been archived as [new_file]. Log rotation is now active.', 'gk-gravityedit' ),
			[
				'[old_size]' => $migration_data['old_size'],
				'[new_file]' => $migration_data['new_file'],
			]
		);

		WP::delete_transient( 'gk_foundation_log_migrated' );

		return [
			'id'       => 'log_migration_notice',
			'html'     => strtr(
				$this->get_notice_template(),
				[
					'%color%'  => 'blue',
					'%icon%'   => $this->get_checkmark_icon(),
					'%notice%' => $migration_notice,
				]
			),
			'requires' => [
				'id'       => 'logger_type',
				'operator' => '=',
				'value'    => 'file',
			],
		];
	}

	/**
	 * Returns log level educational notices.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	private function get_log_level_notices() {
		$notices = [];

		// Critical level notice.
		$notices[] = [
			'id'              => 'logger_level_critical_notice',
			'html'            => strtr(
				$this->get_notice_template(),
				[
					'%color%'  => 'red',
					'%icon%'   => $this->get_info_icon(),
					'%notice%' => wp_kses( __( '<code>CRITICAL</code> level only - logs system failures, alerts, and emergencies. You may miss important warnings and errors that could help troubleshoot issues.', 'gk-gravityedit' ), [ 'code' => [] ] ),
				]
			),
			'requires'        => [
				[
					'id'       => 'logger',
					'operator' => '=',
					'value'    => '1',
				],
				[
					'id'       => 'logger_level',
					'operator' => '=',
					'value'    => 'critical',
				],
			],
			'excludeFromSave' => true,
			'nestUnder'       => 'logger_level',
		];

		// Warning level notice (Standard).
		$notices[] = [
			'id'              => 'logger_level_warning_notice',
			'html'            => strtr(
				$this->get_notice_template(),
				[
					'%color%'  => 'blue',
					'%icon%'   => $this->get_checkmark_icon(),
					'%notice%' => wp_kses( __( '<code>WARNING</code> level and above (<code>WARNING</code>, <code>CRITICAL</code>) - logs warnings, errors, and critical issues. This is the recommended level for most production websites as it captures problems without excessive noise.', 'gk-gravityedit' ), [ 'code' => [] ] ),
				]
			),
			'requires'        => [
				[
					'id'       => 'logger',
					'operator' => '=',
					'value'    => '1',
				],
				[
					'id'       => 'logger_level',
					'operator' => '=',
					'value'    => 'warning',
				],
			],
			'excludeFromSave' => true,
			'nestUnder'       => 'logger_level',
		];

		// Notice level notice.
		$notices[] = [
			'id'              => 'logger_level_notice_notice',
			'html'            => strtr(
				$this->get_notice_template(),
				[
					'%color%'  => 'blue',
					'%icon%'   => $this->get_info_icon(),
					'%notice%' => wp_kses( __( '<code>NOTICE</code> level and above (<code>NOTICE</code>, <code>WARNING</code>, <code>CRITICAL</code>) - includes notices along with all warnings and errors. Good for monitoring site health and catching potential issues before they become problems.', 'gk-gravityedit' ), [ 'code' => [] ] ),
				]
			),
			'requires'        => [
				[
					'id'       => 'logger',
					'operator' => '=',
					'value'    => '1',
				],
				[
					'id'       => 'logger_level',
					'operator' => '=',
					'value'    => 'notice',
				],
			],
			'excludeFromSave' => true,
			'nestUnder'       => 'logger_level',
		];

		// Info level notice.
		$notices[] = [
			'id'              => 'logger_level_info_notice',
			'html'            => strtr(
				$this->get_notice_template(),
				[
					'%color%'  => 'yellow',
					'%icon%'   => $this->get_info_icon(),
					'%notice%' => wp_kses( __( '<code>INFO</code> level and above (<code>INFO</code>, <code>NOTICE</code>, <code>WARNING</code>, <code>CRITICAL</code>) - logs general information and all higher priority events. Useful for detailed site monitoring but may create more log entries than needed for typical use.', 'gk-gravityedit' ), [ 'code' => [] ] ),
				]
			),
			'requires'        => [
				[
					'id'       => 'logger',
					'operator' => '=',
					'value'    => '1',
				],
				[
					'id'       => 'logger_level',
					'operator' => '=',
					'value'    => 'info',
				],
			],
			'excludeFromSave' => true,
			'nestUnder'       => 'logger_level',
		];

		// Debug level notice.
		$notices[] = [
			'id'              => 'logger_level_debug_notice',
			'html'            => strtr(
				$this->get_notice_template(),
				[
					'%color%'  => 'yellow',
					'%icon%'   => $this->get_info_icon(),
					'%notice%' => wp_kses( __( '<code>DEBUG</code> level and above (<code>DEBUG</code>, <code>INFO</code>, <code>NOTICE</code>, <code>WARNING</code>, <code>CRITICAL</code>) - logs everything including detailed technical information. Only use temporarily for troubleshooting as it creates high volume logs and may impact performance.', 'gk-gravityedit' ), [ 'code' => [] ] ),
				]
			),
			'requires'        => [
				[
					'id'       => 'logger',
					'operator' => '=',
					'value'    => '1',
				],
				[
					'id'       => 'logger_level',
					'operator' => '=',
					'value'    => 'debug',
				],
			],
			'excludeFromSave' => true,
			'nestUnder'       => 'logger_level',
		];

		return $notices;
	}

	/**
	 * Returns handler-specific notices.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	private function get_handler_notices() {
		$notices = [];

		// Chrome Logger notice.
		$notices[] = [
			'id'              => 'chrome_logger_tip',
			'html'            => strtr(
				$this->get_notice_template(),
				[
					'%color%'  => 'yellow',
					'%icon%'   => $this->get_info_icon(),
					'%notice%' => $this->get_chrome_logger_tip(),
				]
			),
			'requires'        => [
				[
					'id'       => 'logger',
					'operator' => '=',
					'value'    => '1',
				],
				[
					'id'       => 'logger_type',
					'operator' => '=',
					'value'    => 'chrome_logger',
				],
			],
			'excludeFromSave' => true,
			'nestUnder'       => 'logger_type',
		];

		// Query Monitor notice.
		if ( ! class_exists( 'QueryMonitor' ) ) {
			$notices[] = [
				'id'              => 'query_monitor_notice',
				'html'            => strtr(
					$this->get_notice_template(),
					[
						'%color%'  => 'yellow',
						'%icon%'   => $this->get_info_icon(),
						'%notice%' => $this->get_query_monitor_notice(),
					]
				),
				'requires'        => [
					[
						'id'       => 'logger',
						'operator' => '=',
						'value'    => '1',
					],
					[
						'id'       => 'logger_type',
						'operator' => '=',
						'value'    => 'query_monitor',
					],
				],
				'excludeFromSave' => true,
				'nestUnder'       => 'logger_type',
			];
		}

		return $notices;
	}

	/**
	 * Returns Gravity Forms logger notice.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	private function get_gravity_forms_notice() {
		$gravity_forms_logger_tip = strtr(
			// Translators: Do not translate the placeholders inside [].
			esc_html__( 'Logging is currently handled by [link]Gravity Forms[/link].', 'gk-gravityedit' ),
			[
				'[link]'  => '<a href="' . admin_url( 'admin.php?page=gf_settings&subview=gravityformslogging' ) . '" class="font-medium underline text-yellow-700 hover:text-yellow-600">',
				'[/link]' => '</a>',
			]
		);

		return [
			'id'       => 'gravity_forms_logger_tip',
			'html'     => strtr(
				$this->get_notice_template(),
				[
					'%color%'  => 'yellow',
					'%icon%'   => $this->get_info_icon(),
					'%notice%' => $gravity_forms_logger_tip,
				]
			),
			'requires' => [
				'id'       => 'logger',
				'operator' => '!=',
				'value'    => '1',
			],
		];
	}

	/**
	 * Returns Chrome Logger tip text.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	private function get_chrome_logger_tip() {
		return strtr(
			// Translators: Do not translate the placeholders inside [].
			esc_html__( 'You must install [link]Chrome Logger[/link] browser extension to use this option.', 'gk-gravityedit' ),
			[
				'[link]'  => '<a href="https://craig.is/writing/chrome-logger" class="font-medium underline text-yellow-700 hover:text-yellow-600">',
				'[/link]' => '</a>',
			]
		);
	}

	/**
	 * Returns Query Monitor notice text.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	private function get_query_monitor_notice() {
		return strtr(
			// Translators: Do not translate the placeholders inside [].
			esc_html__( 'You must install [link]Query Monitor[/link] WordPress plugin to use this option.', 'gk-gravityedit' ),
			[
				'[link]'  => '<a href="https://wordpress.org/plugins/query-monitor/" class="font-medium underline text-yellow-700 hover:text-yellow-600">',
				'[/link]' => '</a>',
			]
		);
	}

	/**
	 * Updates GravityKit settings with logger settings in a dedicated Logging section.
	 *
	 * @since 1.3.0
	 *
	 * @param array $gk_settings     GravityKit settings array (passed by reference).
	 * @param array $logger_settings Logger settings to add.
	 *
	 * @return void
	 */
	private function update_gk_settings( &$gk_settings, $logger_settings ) {
		// Create a new Logging section in GravityKit settings.
		$logging_section = [
			'title'    => esc_html__( 'Logging', 'gk-gravityedit' ),
			'settings' => $logger_settings,
		];

		// Insert the Logging section before Technical (index 2).
		$existing_sections = Arr::get( $gk_settings, 'gk_foundation.sections', [] );
		array_splice( $existing_sections, 2, 0, [ $logging_section ] );

		Arr::set( $gk_settings, 'gk_foundation.sections', $existing_sections );

		// Update defaults.
		Arr::set(
			$gk_settings,
			'gk_foundation.defaults',
			array_merge(
				Arr::get( $gk_settings, 'gk_foundation.defaults', [] ),
				self::DEFAULT_SETTINGS
			)
		);
	}

	/**
	 * Returns notice template HTML.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	private function get_notice_template() {
		return <<<HTML
<div class="bg-%color%-50 p-4">
	<div class="flex">
		<div class="flex-shrink-0">
			%icon%
		</div>
	    <div class="ml-3">
			<p class="text-sm text-%color%-700">
			%notice%
			</p>
		</div>
	</div>
</div>
HTML;
	}

	/**
	 * Returns info icon SVG.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	private function get_info_icon() {
		return <<<HTML
<svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
	<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
</svg>
HTML;
	}

	/**
	 * Returns checkmark icon SVG.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	private function get_checkmark_icon() {
		return <<<HTML
<svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
	<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
</svg>
HTML;
	}

	/**
	 * Adds inline styles for logger UI.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	private function add_inline_styles() {
		add_filter(
			'gk/foundation/inline-styles',
			function ( $styles ) {
				$css      = <<<CSS
.bg-yellow-50 {
    --tw-bg-opacity: 1;
    background-color: rgba(255, 251, 235, var(--tw-bg-opacity))
}

.bg-blue-50 {
    --tw-bg-opacity: 1;
    background-color: rgba(239, 246, 255, var(--tw-bg-opacity))
}

.bg-red-50 {
    --tw-bg-opacity: 1;
    background-color: rgba(254, 242, 242, var(--tw-bg-opacity))
}

.text-yellow-400 {
    --tw-text-opacity: 1;
    color: rgba(251, 191, 36, var(--tw-text-opacity))
}

.text-yellow-700 {
    --tw-text-opacity: 1;
    color: rgba(180, 83, 9, var(--tw-text-opacity))
}

.text-blue-400 {
    --tw-text-opacity: 1;
    color: rgba(96, 165, 250, var(--tw-text-opacity))
}

.text-blue-700 {
    --tw-text-opacity: 1;
    color: rgba(29, 78, 216, var(--tw-text-opacity))
}

.hover\:text-yellow-600:hover {
    --tw-text-opacity: 1;
    color: rgba(217, 119, 6, var(--tw-text-opacity))
}

.hover\:text-blue-600:hover {
    --tw-text-opacity: 1;
    color: rgba(37, 99, 235, var(--tw-text-opacity))
}
CSS;
				$styles[] = [
					'style' => $css,
				];

				return $styles;
			}
		);
	}

	/**
	 * Handles settings save and cleans up log files when logger type changes.
	 *
	 * @since 1.3.0
	 *
	 * @param array $new_settings Settings to save.
	 *
	 * @return array
	 */
	public function save_settings( $new_settings ) {
		$current_settings = $this->_settings_framework->get_plugin_settings( FoundationCore::ID );

		// Validate rotation period - ensure it's a supported format.
		if ( ! empty( $new_settings['logger_rotation_period'] ) ) {
			$valid_formats = [ 'Y-m-d', 'Y-m', 'Y' ];

			// Only allow weekly format if WeeklyRotatingFileHandler exists.
			if ( class_exists( __NAMESPACE__ . '\WeeklyRotatingFileHandler' ) ) {
				$valid_formats[] = 'Y-\WW';
			}

			if ( ! in_array( $new_settings['logger_rotation_period'], $valid_formats, true ) ) {
				$new_settings['logger_rotation_period'] = self::DEFAULT_SETTINGS['logger_rotation_period'];
			}
		}

		// Validate log level - ensure it's a supported level.
		if ( ! empty( $new_settings['logger_level'] ) ) {
			$valid_levels = [ 'debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency' ];

			if ( ! in_array( $new_settings['logger_level'], $valid_levels, true ) ) {
				$new_settings['logger_level'] = self::DEFAULT_SETTINGS['logger_level'];
			}
		}

		// Only proceed if logger was previously enabled with file type.
		if ( empty( $current_settings['logger'] ) || 'file' !== $current_settings['logger_type'] ) {
			return $new_settings;
		}

		// Check if we need to clean up log files.
		$should_cleanup = false;

		// The logger setting is always present (checkbox value: '0' or '1').
		$logger_disabled = ! isset( $new_settings['logger'] ) || '0' === $new_settings['logger'] || ! $new_settings['logger'];

		if ( $logger_disabled ) {
			// Logger is being disabled.
			$should_cleanup = true;
		} elseif ( isset( $new_settings['logger'] ) && $new_settings['logger'] && 'file' !== $new_settings['logger_type'] ) {
			// Logger remains enabled but type changed from 'file'.
			$should_cleanup = true;
		}

		if ( ! $should_cleanup ) {
			return $new_settings;
		}

		// Close handlers before deleting files.
		$this->_logger_framework->close_handlers();

		// Delete all log files.
		$log_file     = $this->_logger_framework->get_log_file();
		$log_dir      = dirname( $log_file );
		$log_basename = basename( $log_file, '.log' );

		$pattern       = $log_dir . '/' . $log_basename . '-*.log';
		$rotated_files = glob( $pattern );

		if ( is_array( $rotated_files ) ) {
			foreach ( $rotated_files as $file ) {
				wp_delete_file( $file );
			}
		}

		wp_delete_file( $log_file );

		return $new_settings;
	}
}
