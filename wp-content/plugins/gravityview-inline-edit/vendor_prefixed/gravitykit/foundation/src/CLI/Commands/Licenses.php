<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 02-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\CLI\Commands;

use Exception;
use GravityKit\GravityEdit\Foundation\CLI\AbstractCommand;
use GravityKit\GravityEdit\Foundation\Licenses\LicenseManager;
use WP_CLI;
use WP_CLI_Command;
use function WP_CLI\Utils\format_items;

/**
 * Manage licenses.
 */
class Licenses extends AbstractCommand {
	const DEFAULT_OUTPUT_FORMAT = 'table';

	/**
	 * List activated licenses.
	 *
	 * @since      1.2.0
	 *
	 * @param array $args
	 * @param array $assoc_args
	 *
	 * @subcommand list
	 *
	 * ## OPTIONS
	 *
	 * [--skip-cache]
	 * : Fetches licenses from the server rather than from local cache. Default: false.
	 *
	 * [--format=<format>]
	 * : Format output. Accepted values: table, json. Default: table.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk licenses list
	 *
	 * @synopsis [--skip-cache] [--format=<format>]
	 *
	 * @return void
	 */
	public function list( array $args, array $assoc_args ) {
		$licenses = $this->get_licenses_or_exit( isset( $assoc_args['skip-cache'] ) );

		if ( empty( $licenses ) ) {
			WP_CLI::error( 'No licenses found.' );
		}

		$this->show_count_or_exit( $licenses );

		$this->show_license_info(
			$licenses,
			$assoc_args['format'] ?? self::DEFAULT_OUTPUT_FORMAT
		);
	}

	/**
	 * Check remote server for license(s).
	 *
	 * @since      1.2.0
	 *
	 * @param array $args
	 * @param array $assoc_args
	 *
	 * @subcommand check
	 *
	 * ## OPTIONS
	 *
	 * <license-key>,<license-key>
	 * : Comma-separated license keys.
	 *
	 * [--format=<format>]
	 * : Format output. Accepted values: table, json. Default: table.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk licenses check <license-key>,<license-key>
	 *
	 * @synopsis <license-key> [--format=<format>]
	 *
	 * @return void
	 */
	public function check( array $args, array $assoc_args ) {
		if ( ! isset( $args[0] ) ) {
			WP_CLI::error( 'Please provide a license key.' );
		}

		$keys = explode( ',', $args[0] );

		try {
			$result = LicenseManager::get_instance()->check_licenses( $keys );
		} catch ( Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}

		foreach ( $result as $key => $license ) {
			if ( ! $license['_raw']['success'] ) {
				WP_CLI::error( "License {$key} is invalid.", false );

				continue;
			}

			$this->show_license_info( [ $license ], $assoc_args['format'] ?? self::DEFAULT_OUTPUT_FORMAT );
		}
	}

	/**
	 * Activate license(s).
	 *
	 * @since      1.2.0
	 *
	 * @param array $args
	 * @param array $assoc_args
	 *
	 * @subcommand activate
	 *
	 * ## OPTIONS
	 *
	 * <license-key>,<license-key>
	 * : Comma-separated license keys.
	 *
	 * [--url=<site-url>]
	 * : Site url for which to activate license(s).
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk licenses activate <license-key>,<license-key> --url=<site-url>
	 *
	 * @synopsis <license-key> [--url=<site-url>]
	 *
	 * @return void
	 */
	public function activate( array $args) {
		if ( ! isset( $args[0] ) ) {
			WP_CLI::error( 'Please provide a license key.' );
		}

		$is_url_specified = ! empty( preg_grep( '/^--url=/', $_SERVER['argv'] ?? [] ) );

		if ( ! $is_url_specified ) {
			WP_CLI::error( 'Please provide the site url (via the --url=<site-url> argument) for which to deactivate license(s).' );
		}

		$keys = explode( ',', $args[0] );

		$licenses = $this->get_licenses_or_exit();

		$error = false;

		foreach ( $keys as $key ) {
			WP_CLI::line( "Activating {$key}…" );

			if ( isset( $licenses[ $key ] ) ) {
				WP_CLI::warning( "License is already activate.\n", false );

				continue;
			}

			try {
				$license = LicenseManager::get_instance()->activate_license( $key );

				$this->show_license_info( [ $license ], $assoc_args['format'] ?? self::DEFAULT_OUTPUT_FORMAT );

				WP_CLI::success( "License activated.\n" );
			} catch ( \Exception $e ) {
				WP_CLI::error( $e->getMessage() . "\n", false );

				$error = true;
			}
		}

		if ( $error ) {
			WP_CLI::error();
		}
	}

	/**
	 * Deactivate license(s).
	 *
	 * @since      1.2.0
	 *
	 * @param array $args
	 * @param array $assoc_args
	 *
	 * @subcommand deactivate
	 *
	 * ## OPTIONS
	 *
	 * <license-key>,<license-key>
	 * : Comma-separated license keys.
	 *
	 * [--url=<site-url>]
	 * : Site url for which to deactivate license(s).
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk licenses deactivate <license-key>,<license-key> --url=<site-url>
	 *
	 * @synopsis <license-key> [--url=<site-url>]
	 *
	 * @return void
	 */
	public function deactivate( array $args ) {
		if ( ! isset( $args[0] ) ) {
			WP_CLI::error( 'Please provide a license key.' );
		}

		$is_url_specified = ! empty( preg_grep( '/^--url=/', $_SERVER['argv'] ?? [] ) );

		if ( ! $is_url_specified ) {
			WP_CLI::error( 'Please provide the site url (via the --url=<site-url> argument) for which to deactivate license(s).' );
		}

		$keys = explode( ',', $args[0] );

		$licenses = $this->get_licenses_or_exit();

		$error = false;

		foreach ( $keys as $key ) {
			WP_CLI::line( "Deactivating {$key}…" );

			if ( ! isset( $licenses[ $key ] ) ) {
				WP_CLI::warning( "License is not active.\n", false );

				continue;
			}

			try {
				LicenseManager::get_instance()->deactivate_license( $key );

				WP_CLI::success( "License deactivated.\n" );
			} catch ( \Exception $e ) {
				WP_CLI::error( $e->getMessage() . "\n", false );
			}
		}

		if ( $error ) {
			WP_CLI::error();
		}
	}

	/**
	 * Returns a list of licences.
	 *
	 * @since 1.2.0
	 *
	 * @interal
	 *
	 * @param bool $skip_cache Skip remote cache. Default: false.
	 *
	 * @return array
	 */
	private function get_licenses_or_exit( $skip_cache = false ): array {
		$licenses = [];

		try {
			if ( $skip_cache ) {
				LicenseManager::get_instance()->recheck_all_licenses( $skip_cache );
			}

			$licenses = LicenseManager::get_instance()->get_licenses_data();
		} catch ( Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}

		return $licenses;
	}

	/**
	 * Outputs license count or a "no licenses found" message and exits.
	 *
	 * @since 1.2.0
	 *
	 * @interal
	 *
	 * @param array $licenses
	 *
	 * @return void
	 */
	private function show_count_or_exit( array $licenses ) {
		if ( empty( $licenses ) ) {
			WP_CLI::error( 'No licenses found.' );
		}

		WP_CLI::line( sprintf( "Found %s license%s:\n", count( $licenses ), count( $licenses ) > 1 ? 's' : '' ) );
	}


	/**
	 * Outputs licenses info.
	 *
	 * @since 1.2.0
	 *
	 * @interal
	 *
	 * @param array  $licenses
	 * @param string $format Output format: table, json.
	 *
	 * @return void
	 */
	private function show_license_info( array $licenses, $format = self::DEFAULT_OUTPUT_FORMAT ) {
		$licenses = array_values( $licenses );

		$columns = [
			'Name',
			'Email',
			'Type',
			'Key',
			'Expiry',
			'Limit',
			'Site Count',
			'Activations Left',
		];

		if ( $format === 'json' ) {
			$columns = array_map( function ( $value ) {
				return strtolower( str_replace( ' ', '_', $value ) );
			}, $columns );
		}

		foreach ( $licenses as &$license ) {
			$_license = array_combine(
				$columns,
				[
					$license['name'],
					$license['email'],
					$license['license_name'],
					$license['key'],
					$license['expiry'],
					$license['license_limit'],
					$license['site_count'],
					$license['activations_left'],
				] );

			$license = $_license;
		}

		format_items(
			$format,
			$licenses,
			$columns
		);
	}
}
