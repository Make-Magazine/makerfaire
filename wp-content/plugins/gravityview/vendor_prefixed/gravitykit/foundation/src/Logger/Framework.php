<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by gravityview on 10-October-2025 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Logger;

use GravityKit\GravityView\Foundation\Core as FoundationCore;
use GravityKit\GravityView\Foundation\Helpers\Core as CoreHelpers;
use GravityKit\GravityView\Foundation\Helpers\WP;
use GravityKit\GravityView\Foundation\ThirdParty\Monolog\Handler\ChromePHPHandler;
use GravityKit\GravityView\Foundation\ThirdParty\Monolog\Handler\StreamHandler;
use GravityKit\GravityView\Foundation\ThirdParty\Monolog\Handler\RotatingFileHandler;
use GravityKit\GravityView\Foundation\ThirdParty\Monolog\Logger as MonologLogger;
use GravityKit\GravityView\Foundation\Settings\Framework as SettingsFramework;
use GravityKit\GravityView\Foundation\Encryption\Encryption;
use GravityKit\GravityView\Foundation\ThirdParty\Psr\Log\LoggerInterface;
use GravityKit\GravityView\Foundation\ThirdParty\Psr\Log\LoggerTrait;
use Exception;

/**
 * Logging framework for GravityKit.
 */
class Framework implements LoggerInterface {
	use LoggerTrait;

	const DEFAULT_LOGGER_ID = 'gravitykit';

	const DEFAULT_LOGGER_TITLE = 'GravityKit';

	/**
	 * Minimum file size (in bytes) required to trigger log rotation.
	 *
	 * @since 1.3.0
	 *
	 * @var int
	 */
	const ROTATION_FILE_SIZE_THRESHOLD = 10 * 1024 * 1024; // 10MB.

	/**
	 * Instances of the logger class instantiated by various plugins.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private static $_instances = [];

	/**
	 * Settings framework instance.
	 *
	 * @since 1.0.0
	 *
	 * @var SettingsFramework
	 */
	private $_settings;

	/**
	 * Monolog class instance.
	 *
	 * @since 1.0.0
	 *
	 * @var MonologLogger|null
	 */
	private $_logger = null;

	/**
	 * Unique logger ID.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $_logger_id;

	/**
	 * Logger title.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $_logger_title;

	/**
	 * Location where logs are stored relative to WP_CONTENT_DIR.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $_log_path = 'logs';

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $logger_id    Unique name that's prefixed to each log entry.
	 * @param string $logger_title Logger title (used in the admin UI).
	 *
	 * @return void
	 */
	private function __construct( $logger_id, $logger_title ) {
		$this->_settings = SettingsFramework::get_instance();

		$this->_logger_id    = $logger_id;
		$this->_logger_title = $logger_title;

		/**
		 * Changes path where logs are stored.
		 *
		 * @filter `gk/foundation/logger/log-path`
		 *
		 * @since  1.0.0
		 *
		 * @param string $log_path Location where logs are stored relative to WP_CONTENT_DIR. Default: WP_CONTENT_DIR . '/logs'.
		 */
		$this->_log_path = apply_filters( 'gk/foundation/logger/log-path', $this->_log_path );

		$logger_handler = $this->get_logger_handler();

		if ( $logger_handler ) {
			$this->_logger = new MonologLogger( $logger_id );

			$this->_logger->pushHandler( $logger_handler );
		}
	}

	/**
	 * Returns class instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $logger_id    (optional) Unique logger identifier that's prefixed to each log entry or used with some handlers. Default: gravitykit.
	 * @param string $logger_title (optional) Logger title (used in the admin UI). Default: GravityKit.
	 *
	 * @return Framework
	 */
	public static function get_instance( $logger_id = '', $logger_title = '' ) {
		$logger_id    = $logger_id ?: self::DEFAULT_LOGGER_ID;
		$logger_title = $logger_title ?: self::DEFAULT_LOGGER_TITLE;

		if ( empty( self::$_instances[ $logger_id ] ) ) {
			self::$_instances[ $logger_id ] = new self( $logger_id, $logger_title );
		}

		return self::$_instances[ $logger_id ];
	}

	/**
	 * Initializes the logger component.
	 * This method is called by {@see Core::init()}.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public function init() {
		new Settings( $this );
	}

	/**
	 * Returns handler that will process log messages.
	 *
	 * @since 1.0.0
	 *
	 * @return void|ChromePHPHandler|GravityFormsHandler|StreamHandler|RotatingFileHandler|WeeklyRotatingFileHandler|QueryMonitorHandler
	 */
	public function get_logger_handler() {
		$settings = $this->_settings->get_plugin_settings( FoundationCore::ID );

		if ( empty( $settings['logger'] ) ) {
			if ( class_exists( 'GFLogging' ) && get_option( 'gform_enable_logging' ) ) {
				return new GravityFormsHandler( $this->_logger_id, $this->_logger_title );
			}

			return;
		}

		// Get log level - default to DEBUG for backward compatibility with older settings.
		$log_level_name = isset( $settings['logger_level'] ) ? $settings['logger_level'] : 'debug';
		$log_level      = Settings::get_monolog_level( $log_level_name );

		switch ( $settings['logger_type'] ) {
			case 'file':
				try {
					// Check if we should migrate existing log file.
					$this->maybe_migrate_existing_log();

					// Get rotation settings.
					$max_files       = isset( $settings['logger_max_files'] ) ? absint( $settings['logger_max_files'] ) : 7;
					$rotation_period = isset( $settings['logger_rotation_period'] ) ? $settings['logger_rotation_period'] : RotatingFileHandler::FILE_PER_DAY;

					// If weekly rotation is selected but WeeklyRotatingFileHandler doesn't exist, fall back to daily.
					if ( 'Y-\WW' === $rotation_period && ! class_exists( __NAMESPACE__ . '\WeeklyRotatingFileHandler' ) ) {
						$rotation_period = RotatingFileHandler::FILE_PER_DAY;
					}

					// Use custom handler for weekly rotation.
					if ( 'Y-\WW' === $rotation_period ) {
						$handler = new WeeklyRotatingFileHandler(
							$this->get_log_file(),
							$max_files,
							$log_level
						);
					} else {
						// Use standard handler for daily/monthly/yearly rotation.
						$handler = new RotatingFileHandler(
							$this->get_log_file(),
							$max_files,
							$log_level
						);
					}

					$handler->setFilenameFormat( '{filename}-{date}', $rotation_period );

					return $handler;
				} catch ( Exception $e ) {
					error_log( 'Could not initialize file logging for GravityKit:' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log

					return;
				}
			case 'query_monitor':
				return new QueryMonitorHandler( $log_level );
			case 'chrome_logger':
				return new ChromePHPHandler( $log_level );
		}
	}

	/**
	 * Closes all logger handlers.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public function close_handlers() {
		if ( ! $this->_logger ) {
			return;
		}

		$handlers = $this->_logger->getHandlers();

		// Currently only supports single handler setup - closes the primary handler.
		if ( isset( $handlers[0] ) && method_exists( $handlers[0], 'close' ) ) {
			// @phpstan-ignore-next-line
			$handlers[0]->close();
		}
	}

	/**
	 * Returns log file name with path.
	 *
	 * @return string
	 */
	public function get_log_file() {
		$hash = substr( Encryption::get_instance()->hash( FoundationCore::ID ), 0, 10 );

		return sprintf( '%s/%s/gravitykit-%s.log', WP_CONTENT_DIR, $this->_log_path, $hash );
	}

	/**
	 * Returns the log path relative to WP_CONTENT_DIR.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public function get_log_path() {
		return $this->_log_path;
	}

	/**
	 * Migrates existing log file to rotated format if it's larger than 10MB.
	 *
	 * @since 1.3.0
	 *
	 * @return void
	 */
	private function maybe_migrate_existing_log() {
		$log_file = $this->get_log_file();

		// Check if old log file exists.
		if ( ! file_exists( $log_file ) ) {
			return;
		}

		// Check if it's already a rotated file (contains date pattern).
		if ( preg_match( '/-\d{4}-\d{2}-\d{2}\.log$/', $log_file ) ) {
			return;
		}

		// Get file size.
		$file_size = filesize( $log_file );

		// Only migrate if file is larger than 10MB.
		if ( $file_size < self::ROTATION_FILE_SIZE_THRESHOLD ) {
			return;
		}

		// Create backup with current date.
		$backup_file = str_replace( '.log', '-' . current_time( 'Y-m-d' ) . '-migrated.log', $log_file );

		// Rename existing file.
		if ( rename( $log_file, $backup_file ) ) {
			// Set a transient to show admin notice.
			WP::set_transient(
                'gk_foundation_log_migrated',
                [
					'old_size' => size_format( $file_size ),
					'new_file' => basename( $backup_file ),
				],
                DAY_IN_SECONDS
            );
		}
	}

	/**
	 * Magic method to access Monolog's logger class methods.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name      Package/class name.
	 * @param array  $arguments Optional and not used.
	 *
	 * @return mixed|void
	 */
	public function __call( $name, array $arguments = [] ) {
		if ( ! $this->_logger instanceof MonologLogger ) {
			return;
		}

		/**
		 * Allows logging of WP heartbeat requests.
		 *
		 * @filter `gk/foundation/logger/allow-heartbeat-requests`
		 *
		 * @since  1.0.0
		 *
		 * @param bool $log_heartbeat Default: false.
		 */
		$log_heartbeat = apply_filters( 'gk/foundation/logger/allow-heartbeat-requests', false );

		if ( isset( $_REQUEST['action'] ) && 'heartbeat' === $_REQUEST['action'] && ! $log_heartbeat ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		if ( CoreHelpers::is_callable_class_method( [ $this->_logger, $name ] ) ) {
			/** @phpstan-ignore-next-line */
			return call_user_func_array( [ $this->_logger, $name ], $arguments );
		}
	}

	/**
	 * Logs with an arbitrary level.
	 *
	 * @inheritDoc
	 *
	 * @since 1.0.0
	 *
	 * @param mixed   $level   Log level.
	 * @param string  $message Log message.
	 * @param mixed[] $context Log context.
	 */
	public function log( $level, $message, array $context = [] ) {
		$this->__call( $level, [ $message, $context ] );
	}
}
