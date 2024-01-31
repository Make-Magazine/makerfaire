<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 02-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\CLI;

use WP_CLI;

/**
 * This class adds custom commands to WP CLI.
 */
class CLI {
	const COMMAND_PREFIX = 'gk';

	const COMMANDS_FOLDER = __DIR__ . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR;

	const COMMAND_NAMESPACE = __NAMESPACE__ . '\Commands\\';

	/**
	 * Class instance.
	 *
	 * @since 1.2.0
	 *
	 * @var CLI
	 */
	private static $_instance;

	/**
	 * Class constructor.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	private function __construct() {
		add_action( 'wp_loaded', [ $this, 'register_commands' ] );
	}

	/**
	 * Returns class instance.
	 *
	 * @since 1.2.0
	 *
	 * @return CLI
	 */
	public static function get_instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Registers WP CLI commands.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public static function register_commands() {
		$commands = [];

		$command_classes = glob( self::COMMANDS_FOLDER . '*.php' );

		foreach ( $command_classes as $class ) {
			$class           = pathinfo( $class, PATHINFO_FILENAME );
			$full_class_name = self::COMMAND_NAMESPACE . $class;

			if ( ! class_exists( $full_class_name ) ) {
				continue;
			}

			$command = defined( "{$full_class_name}::COMMAND" ) ? $full_class_name::COMMAND : strtolower( $class );

			$commands[ $command ] = $full_class_name;
		}

		/**
		 * Registers custom GK commands. They will be prefixed with "gk" (e.g., "gk <custom command>").
		 *
		 * @filter gk/foundation/cli/commands
		 *
		 * @since  1.2.0
		 *
		 * @param array $commands
		 */
		$commands = apply_filters( 'gk/foundation/cli/commands', $commands );

		foreach ( $commands as $command => $class ) {
			WP_CLI::add_command(
				$command === 'root' ? self::COMMAND_PREFIX : self::COMMAND_PREFIX . ' ' . $command,
				$class
			);
		}
	}
}
