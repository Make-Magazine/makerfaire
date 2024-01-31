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
use GravityKit\GravityEdit\Foundation\Licenses\ProductDependencyChecker;
use GravityKit\GravityEdit\Foundation\Licenses\ProductManager;
use WP_CLI;
use WP_CLI_Command;
use function WP_CLI\Utils\format_items;

/**
 * Manage products.
 */
class Products extends AbstractCommand {
	const DEFAULT_OUTPUT_FORMAT = 'table';

	/**
	 * List products. By default, only the licensed products are displayed.
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
	 * [--format=<format>]
	 * : Format output. Accepted values: table, json. Default: table.
	 *
	 * [--only-installed]
	 * : Display only installed products. Default: false.
	 *
	 * [--include-hidden]
	 * : Include "hidden" products (i.e., typically non-GK products that are tracked for internal purposes). Default: false.
	 *
	 * [--exclude-unlicensed]
	 * : Exclude unlicensed products. Default: false.
	 *
	 * [--skip-cache]
	 * : Fetches product list from the server rather than from local cache. Default: false.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk products list
	 *     wp gk products list --include-unlicensed --include-hidden
	 *
	 * @synopsis [--only-installed] [--include-hidden] [--exclude-unlicensed] [--skip-cache] [--format=<format>]
	 *
	 * @return void
	 */
	public function list( array $args, array $assoc_args ) {
		$products = $this->get_products_or_exit( isset( $assoc_args['skip-cache'] ) );

		$products = array_filter( $products, function ( $product ) use ( $assoc_args ) {
			if ( isset( $assoc_args['only-installed'] ) && ! $product['installed'] ) {
				return false;
			}

			if ( $product['hidden'] && ! isset( $assoc_args['include-hidden'] ) ) {
				return false;
			}

			if ( $product['free'] ) {
				return true;
			}

			if ( isset( $assoc_args['exclude-unlicensed'] ) ) {
				return ! empty( $product['licenses'] );
			}

			return true;
		} );

		$this->show_count_or_exit( $products );

		$this->show_products(
			$products,
			$assoc_args['format'] ?? self::DEFAULT_OUTPUT_FORMAT
		);
	}

	/**
	 * Install product(s).
	 *
	 * @since      1.2.0
	 * @since      1.2.4 Added --all option.
	 *
	 * @param array $args
	 * @param array $assoc_args
	 *
	 * @subcommand install
	 *
	 * ## OPTIONS
	 *
	 * [<product-text-domain>,...]
	 *
	 * [--all]
	 * : Install all available products.
	 *
	 * [--skip-dependency-check]
	 * : Do not verify if product has unmet dependencies (system or plugin). Default: false.
	 *
	 * [--activate]
	 * : Activate after installation. Default: false.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk products install gk-gravityview
	 *     wp gk products install gk-gravityview,gk-gravitymigrate --activate --skip-dependency-check
	 *     wp gk products install --all
	 *
	 * @synopsis [<product-text-domain>] [--all] [--activate] [--skip-dependency-check]
	 *
	 * @return void
	 */
	public function install( array $args, array $assoc_args ) {
		if ( ! isset( $assoc_args['all'] ) && ! isset( $args[0] ) ) {
			WP_CLI::error( 'Please provide a product text domain.' );
		}

		$text_domains = explode( ',', $args[0] );

		$failed_products = [];

		$products = $this->get_products_or_exit();

		foreach ( $text_domains as $text_domain ) {
			$found = false;

			foreach ( $products as $product ) {
				if ( $product['text_domain'] !== $text_domain && ! isset( $assoc_args['all'] ) ) {
					continue;
				}

				WP_CLI::line( "Installing {$product['name']}..." );

				$found = true;

				if ( $product['installed'] ) {
					WP_CLI::warning( "{$product['name']} is already installed.\n", false );

					continue;
				}

				if ( ! $product['free'] && empty( $product['licenses'] ) ) {
					WP_CLI::warning( "{$product['name']} is not licensed.\n", false );

					continue;
				}

				if ( ! isset( $assoc_args['skip-dependency-check'] ) && ( ! empty( $product['checked_dependencies']['unmet']['system'] ) || ! empty( $product['checked_dependencies']['unmet']['plugin'] ) ) ) {
					WP_CLI::error( "{$product['name']} has unmet dependencies. Please resolve them first.\n", false );

					$this->show_unmet_dependencies( $product['checked_dependencies']['unmet'] );

					$failed_products[] = $product['text_domain'];

					continue;
				}

				try {
					ProductManager::get_instance()->install_product( $product );

					WP_CLI::success( "{$product['name']} installed.\n" );

					if ( $assoc_args['activate'] ?? false ) {
						ProductManager::get_instance()->activate_product( $product );

						WP_CLI::success( "{$product['name']} activated.\n" );
					}
				} catch ( Exception $e ) {
					WP_CLI::error( $e->getMessage() . "\n", false );

					$failed_products[] = $product['text_domain'];

					continue;
				}
			}

			if ( ! $found && ! empty( $failed_products ) ) {
				WP_CLI::error( "{$text_domain} not found.\n", false );

				$failed_products[] = $text_domain;
			}
		}

		if ( ! empty( $failed_products ) ) {
			WP_CLI::error( sprintf( 'Failed to install %s.', implode( ' ', $failed_products ) ) );
		}
	}

	/**
	 * Updates product(s).
	 *
	 * @since      1.2.0
	 * @since      1.2.4 Added --all option.
	 *
	 * @param array $args
	 * @param array $assoc_args
	 *
	 * @subcommand update
	 *
	 * ## OPTIONS
	 *
	 * [<product-text-domain>,...]
	 *
	 * [--all]
	 * : Install all available updates.
	 *
	 * [--skip-dependency-check]
	 * : Do not verify if product has unmet dependencies (system or plugin). Default: false.
	 *
	 * [--skip-git-check]
	 * : Do not check if product is installed from a Git repository and overwrite the installation folder. Default: false.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk products install gk-gravityview
	 *     wp gk products install gk-gravityview,gk-gravitymigrate --skip-dependency-check
	 *     wp gk products install --all
	 *
	 * @synopsis [<product-text-domain>] [--all] [--skip-dependency-check] [--skip-git-check]
	 *
	 * @return void
	 */
	public function update( array $args, array $assoc_args ) {
		if ( ! isset( $assoc_args['all'] ) && ! isset( $args[0] ) ) {
			WP_CLI::error( 'Please provide a product text domain.' );
		}

		$text_domains = explode( ',', $args[0] );

		$failed_products = [];

		$products = $this->get_products_or_exit();

		foreach ( $text_domains as $text_domain ) {
			$found = false;

			foreach ( $products as $product ) {
				if ( ! $product['update_available'] ) {
					continue;
				}

				if ( $product['text_domain'] !== $text_domain && ! isset( $assoc_args['all'] ) ) {
					continue;
				}

				WP_CLI::line( "Updating {$product['name']}..." );

				$found = true;

				if ( ! $product['installed'] ) {
					WP_CLI::warning( "{$product['name']} is not installed.\n", false );

					continue;
				}

				if ( ! $product['free'] && empty( $product['licenses'] ) ) {
					WP_CLI::warning( "{$product['name']} is not licensed.\n", false );

					continue;
				}

				if ( $product['has_git_folder'] && ! $assoc_args['skip-git-check'] ) {
					WP_CLI::warning( "{$product['name']} is installed from a Git repository.\n", false );

					continue;
				}

				if ( ! $product['update_available'] ) {
					WP_CLI::warning( "{$product['name']} is up to date.\n", false );

					continue;
				}

				if ( ! isset( $assoc_args['skip-dependency-check'] ) && ( ! $product['checked_dependencies'][ $product['server_version'] ]['status'] ?? false ) ) {
					WP_CLI::error( "{$product['name']} has unmet dependencies. Please resolve them first.\n", false );

					$this->show_unmet_dependencies( $product['checked_dependencies'][ $product['server_version'] ]['unmet'] );

					$failed_products[] = $product['text_domain'];

					continue;
				}

				try {
					ProductManager::get_instance()->update_product( $product );

					WP_CLI::success( "{$product['name']} updated.\n" );
				} catch ( Exception $e ) {
					WP_CLI::error( $e->getMessage() . "\n", false );

					$failed_products[] = $product['text_domain'];

					continue;
				}
			}

			if ( ! $found && ! empty( $failed_products ) ) {
				WP_CLI::error( "{$text_domain} not found.\n", false );

				$failed_products[] = $text_domain;
			}
		}

		if ( ! empty( $failed_products ) ) {
			WP_CLI::error( sprintf( 'Failed to update %s.', implode( ' ', $failed_products ) ) );
		}
	}

	/**
	 * Activate product(s).
	 *
	 * @since      1.2.0
	 * @since      1.2.4 Added --all option.
	 *
	 * @param array $args
	 * @param array $assoc_args
	 *
	 * @subcommand activate
	 *
	 * ## OPTIONS
	 *
	 * [<product-text-domain>...]
	 *
	 * [--all]
	 * : Activate all inactive products.
	 *
	 * [--skip-dependency-check]
	 * : Do not verify if product has unmet dependencies (system or plugin). Default: false.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk products activate gk-gravityview
	 *     wp gk products activate gk-gravityview,gk-gravitymigrate --skip-dependency-check
	 *     wp gk products activate --all
	 *
	 * @synopsis [<product-text-domain>] [--all] [--skip-dependency-check]
	 *
	 * @return void
	 */
	public function activate( array $args, array $assoc_args ) {
		if ( ! isset( $assoc_args['all'] ) && ! isset( $args[0] ) ) {
			WP_CLI::error( 'Please provide a product text domain.' );
		}

		$text_domains = explode( ',', $args[0] );

		$failed_products = [];

		$products = $this->get_products_or_exit();

		foreach ( $text_domains as $text_domain ) {
			$found = false;

			foreach ( $products as $product ) {
				if ( $product['text_domain'] !== $text_domain && ! isset( $assoc_args['all'] ) ) {
					continue;
				}

				WP_CLI::line( "Activating {$product['name']}..." );

				$found = true;

				if ( $product['active'] ) {
					WP_CLI::warning( "{$product['name']} is already active.\n", false );

					continue;
				}

				if ( ! isset( $assoc_args['skip-dependency-check'] ) && ( ! $product['checked_dependencies'][ $product['server_version'] ]['status'] ?? false ) ) {
					WP_CLI::error( "{$product['name']} has unmet dependencies. Please resolve them first.\n", false );

					$this->show_unmet_dependencies( $product['checked_dependencies'][ $product['server_version'] ]['unmet'] );

					$failed_products[] = $product['text_domain'];

					continue;
				}

				try {
					ProductManager::get_instance()->activate_product( $product );

					WP_CLI::success( "{$product['name']} activated.\n" );
				} catch ( Exception $e ) {
					WP_CLI::error( $e->getMessage() . "\n", false );

					$failed_products[] = $product['text_domain'];

					continue;
				}
			}

			if ( ! $found && ! empty( $failed_products ) ) {
				WP_CLI::error( "{$text_domain} not found.\n", false );

				$failed_products[] = $text_domain;
			}
		}

		if ( ! empty( $failed_products ) ) {
			WP_CLI::error( sprintf( 'Failed to activate %s.', implode( ' ', $failed_products ) ) );
		}
	}

	/**
	 * Deactivate product(s).
	 *
	 * @since      1.2.0
	 * @since      1.2.4 Added --all option.
	 *
	 * @param array $args
	 * @param array $assoc_args
	 *
	 * @subcommand deactivate
	 *
	 * ## OPTIONS
	 *
	 * [<product-text-domain>,...]
	 *
	 * [--all]
	 * : Deactivate all active products.
	 *
	 * [--skip-dependency-check]
	 * : Do not verify if this product is required by other products to be active. Default: false.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk products deactivate gk-gravityview
	 *     wp gk products deactivate gk-gravityview,gk-gravitymigrate --skip-dependency-check
	 *     wp gk products deactivate --all
	 *
	 * @synopsis [<product-text-domain>] [--all] [--skip-dependency-check]
	 *
	 * @return void
	 */
	public function deactivate( array $args, array $assoc_args ) {
		if ( ! isset( $assoc_args['all'] ) && ! isset( $args[0] ) ) {
			WP_CLI::error( 'Please provide a product text domain.' );
		}

		$text_domains = explode( ',', $args[0] );

		$failed_products = [];

		$products = $this->get_products_or_exit();

		foreach ( $text_domains as $text_domain ) {
			$found = false;

			foreach ( $products as $product ) {
				if ( $product['text_domain'] !== $text_domain && ! isset( $assoc_args['all'] ) ) {
					continue;
				}

				WP_CLI::line( "Deactivating {$product['name']}..." );

				$found = true;

				if ( ! $product['active'] ) {
					WP_CLI::warning( "{$product['name']} is not active.\n", false );

					continue;
				}

				if ( ! isset( $assoc_args['skip-dependency-check'] ) && ! empty( $product['required_by'] ) ) {
					WP_CLI::error( "{$product['name']} is required by other products. Please deactivate them first.\n", false );

					foreach ( $product['required_by'] as &$required_by ) {
						$required_by = [
							'Name'        => $required_by['name'],
							'Text Domain' => $required_by['text_domain'],
						];
					}

					format_items( self::DEFAULT_OUTPUT_FORMAT, $product['required_by'], [ 'Name', 'Text Domain' ] );

					$failed_products[] = $product['text_domain'];

					continue;
				}

				try {
					ProductManager::get_instance()->deactivate_product( $product );

					WP_CLI::success( "{$product['name']} deactivated.\n" );
				} catch ( Exception $e ) {
					WP_CLI::error( $e->getMessage() . "\n", false );

					$failed_products[] = $product['text_domain'];

					continue;
				}
			}

			if ( ! $found && ! empty( $failed_products ) ) {
				WP_CLI::error( "{$text_domain} not found.\n", false );

				$failed_products[] = $text_domain;
			}
		}

		if ( ! empty( $failed_products ) ) {
			WP_CLI::error( sprintf( 'Failed to deactivate %s.', implode( ' ', $failed_products ) ) );
		}
	}

	/**
	 * Delete product(s).
	 *
	 * @since      1.2.0
	 * @since      1.2.4 Added --all option.
	 *
	 * @param array $args
	 * @param array $assoc_args
	 *
	 * @subcommand delete
	 *
	 * ## OPTIONS
	 *
	 * [<product-text-domain>,...]
	 *
	 * [--all]
	 * : Delete all installed products.
	 *
	 * [--deactivate-before-deletion]
	 * : Deactivate product before deleting it. Default: false.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk products delete gk-gravityview
	 *     wp gk products delete gk-gravityview,gk-gravitymigrate --deactivate-before-deletion
	 *     wp gk products delete --all
	 *
	 * @synopsis [<product-text-domain>] [--all] [--deactivate-before-deletion]
	 *
	 * @return void
	 */
	public function delete( array $args, array $assoc_args ) {
		if ( ! isset( $assoc_args['all'] ) && ! isset( $args[0] ) ) {
			WP_CLI::error( 'Please provide a product text domain.' );
		}

		$text_domains = explode( ',', $args[0] );

		$failed_products = [];

		$products = $this->get_products_or_exit();

		foreach ( $text_domains as $text_domain ) {
			$found = false;

			foreach ( $products as $product ) {
				if ( $product['text_domain'] !== $text_domain && ! isset( $assoc_args['all'] ) ) {
					continue;
				}

				WP_CLI::line( "Deleting {$product['name']}..." );

				$found = true;

				if ( ! $product['installed'] ) {
					WP_CLI::warning( "{$product['name']} is not installed.\n", false );

					continue;
				}

				if ( $product['active'] && ! isset( $assoc_args['deactivate-before-deletion'] ) ) {
					WP_CLI::warning( "{$product['name']} is active and needs to be deactivated first.\n", false );

					continue;
				} else if ( $product['active'] ) {
					try {
						ProductManager::get_instance()->deactivate_product( $product );
					} catch ( Exception $e ) {
						WP_CLI::error( $e->getMessage() . "\n", false );

						$failed_products[] = $product['text_domain'];

						continue;
					}
				}

				try {
					ProductManager::get_instance()->delete_product( $product );

					WP_CLI::success( "{$product['name']} deleted.\n" );
				} catch ( Exception $e ) {
					WP_CLI::error( $e->getMessage() . "\n", false );

					$failed_products[] = $product['text_domain'];

					continue;
				}
			}

			if ( ! $found && ! empty( $failed_products ) ) {
				WP_CLI::error( "{$text_domain} not found.\n", false );

				$failed_products[] = $text_domain;
			}
		}

		if ( ! empty( $failed_products ) ) {
			WP_CLI::error( sprintf( 'Failed to delete %s.', implode( ' ', $failed_products ) ) );
		}
	}

	/**
	 * Search products.
	 *
	 * @since      1.2.0
	 *
	 * @param array $args
	 * @param array $assoc_args
	 *
	 * @subcommand search
	 *
	 * ## OPTIONS
	 *
	 * <search-term>
	 * : Search term.
	 *
	 * [--include-hidden]
	 * : Include "hidden" products (i.e., typically non-GK products that are tracked for internal purposes). Default: false.
	 *
	 * [--skip-cache]
	 * : Fetches product list from the server rather than from local cache. Default: false.
	 *
	 * [--format=<format>]
	 * : Format output. Accepted values: table, json. Default: table.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk products search import
	 *
	 * @synopsis <search-term> [--include-hidden] [--skip-cache] [--format=<format>]
	 *
	 * @return void
	 */
	public function search( array $args, array $assoc_args ) {
		if ( ! isset( $args[0] ) ) {
			WP_CLI::error( 'Please provide a search term.' );
		}

		$products = $this->get_products_or_exit( isset( $assoc_args['skip-cache'] ) );

		$search_term = strtolower( $args[0] );

		$search_properties = [ 'name', 'text_domain', 'excerpt' ];

		$products = array_filter( $products, function ( $product ) use ( $search_term, $search_properties, $assoc_args ) {
			if ( $product['hidden'] && ! isset( $assoc_args['include-hidden'] ) ) {
				return false;
			}

			foreach ( $search_properties as $key ) {
				if ( ! isset( $product[ $key ] ) ) {
					continue;
				}

				if ( strpos( strtolower( $product[ $key ] ), $search_term ) !== false ) {
					return true;
				}
			}
		} );

		$this->show_count_or_exit( $products );

		$this->show_products( $products, $assoc_args['format'] ?? self::DEFAULT_OUTPUT_FORMAT );
	}

	/**
	 * Returns a list of products.
	 *
	 * @since 1.2.0
	 *
	 * @interal
	 *
	 * @param bool $skip_cache Skip remote cache. Default: false.
	 *
	 * @return array
	 */
	private function get_products_or_exit( $skip_cache = false ): array {
		$products = array_values( ProductManager::get_instance()->get_products_data( [ 'skip_remote_cache' => $skip_cache ] ) );

		foreach ( $products as $key => $product ) {
			if ( $product['third_party'] ) {
				unset( $products[ $key ] );
			}
		}

		return $products;
	}

	/**
	 * Outputs product count or a "no products found" message and exits.
	 *
	 * @since 1.2.0
	 *
	 * @interal
	 *
	 * @param array $products
	 *
	 * @return void
	 */
	private function show_count_or_exit( array $products ) {
		if ( empty( $products ) ) {
			WP_CLI::error( 'No products found.' );
		}

		WP_CLI::line( sprintf( "Found %s product%s:\n", count( $products ), count( $products ) > 1 ? 's' : '' ) );

	}

	/**
	 * Outputs products info.
	 *
	 * @since 1.2.0
	 *
	 * @interal
	 *
	 * @param array  $products
	 * @param string $format Output format: table, json.
	 *
	 * @return void
	 */
	private function show_products( array $products, $format = self::DEFAULT_OUTPUT_FORMAT ) {
		$products = array_values( $products );

		$columns = [
			'Name',
			'Description',
			'Text Domain',
			'Version',
			'Update Available',
			'Licensed',
			'Installed',
			'Active',
			'Network Active',
		];

		if ( $format === 'json' ) {
			$columns = array_map( function ( $value ) {
				return strtolower( str_replace( ' ', '_', $value ) );
			}, $columns );
		}

		foreach ( $products as &$product ) {
			$product = array_combine(
				$columns,
				[
					$product['name'],
					$product['excerpt'],
					$product['text_domain'],
					$product['installed'] ? $product['installed_version'] : $product['server_version'],
					$product['update_available'] ? $product['server_version'] : '',
					! empty( $product['licenses'] ) ? '✓' : ( $product['free'] ? 'Free' : '' ),
					$product['installed'] ? '✓ ' : '',
					$product['active'] ? '✓' : '',
					$product['network_activated'] ? '✓' : ''
				] );
		}

		format_items(
			$format,
			$products,
			$columns
		);
	}

	/**
	 * Outputs failed product dependencies' information.
	 *
	 * @since 1.2.0
	 *
	 * @interal
	 *
	 * @param $unmet_dependencies
	 *
	 * @return void
	 */
	private function show_unmet_dependencies( $unmet_dependencies ) {
		if ( ! empty( $unmet_dependencies['system'] ) ) {
			WP_CLI::line( "System dependencies:\n" );

			// Translate failure reasons.
			foreach ( $unmet_dependencies['system'] as &$dependency ) {
				switch ( $dependency['reason'] ) {
					case ProductDependencyChecker::FAILURE_OLDER_VERSION:
						$dependency['reason'] = "Older version ({$dependency['current_version']}) is installed";

						break;
				}

				// Add some human-readable column names for display purposes.
				$dependency = [
					'Name'             => $dependency['name'],
					'Required Version' => $dependency['required_version'],
					'Status'           => $dependency['reason'],
				];
			}


			format_items( self::DEFAULT_OUTPUT_FORMAT, $unmet_dependencies['system'], [ 'Name', 'Required Version', 'Status' ] );
		}

		if ( ! empty( $unmet_dependencies['plugin'] ) ) {
			WP_CLI::line( "Plugin dependencies:\n" );

			// Translate failure reasons.
			foreach ( $unmet_dependencies['plugin'] as &$dependency ) {
				switch ( $dependency['reason'] ) {
					case ProductDependencyChecker::FAILURE_OLDER_VERSION:
						$dependency['reason'] = "Older version ({$dependency['current_version']}) is installed";

						break;
					case ProductDependencyChecker::FAILURE_INACTIVE:
						$dependency['reason'] = 'Not activated';

						break;
					case ProductDependencyChecker::FAILURE_NOT_INSTALLED:
						$dependency['reason'] = 'Not installed';

						break;
					case ProductDependencyChecker::FAILURE_UNLICENSED:
						$dependency['reason'] = 'Unlicensed';

						break;
					case ProductDependencyChecker::FAILURE_NO_DOWNLOAD_LINK:
						$dependency['reason'] = 'Download link is missing';

						break;
				}

				// Add some human-readable column names for display purposes.
				$dependency = [
					'Dependency'       => $dependency['name'],
					'Required Version' => $dependency['required_version'],
					'Status'           => $dependency['reason'],
				];
			}

			format_items( self::DEFAULT_OUTPUT_FORMAT, $unmet_dependencies['plugin'], [ 'Dependency', 'Required Version', 'Status' ] );

			WP_CLI::line();
		}
	}
}
