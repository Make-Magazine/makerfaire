<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 02-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Licenses;

use Exception;
use GravityKit\GravityEdit\Foundation\Helpers\Arr;
use GravityKit\GravityEdit\Foundation\Helpers\Core as CoreHelpers;

/**
 * Class for managing product dependencies.
 */
class ProductDependencyChecker {
	const FAILURE_NOT_FOUND               = 'not_found';
	const FAILURE_NOT_INSTALLED           = 'not_installed';
	const FAILURE_INACTIVE                = 'inactive';
	const FAILURE_OLDER_VERSION           = 'older_version';
	const FAILURE_UNLICENSED              = 'unlicensed';
	const FAILURE_NO_DOWNLOAD_LINK        = 'no_download_link';
	const FAILURE_CIRCULAR_REFERENCE      = 'circular_reference';
	const FAILURE_UNMET_DEPENDENCY        = 'unmet_dependency';
	const FAILURE_UNKNOWN_PRODUCT_ACTION  = 'unknown_product_action';
	const FAILURE_MISSING_DEPENDENCIES    = 'missing_dependencies';
	const FAILURE_MISSING_PRODUCT_VERSION = 'missing_product_version';
	const ACTION_UPDATE                   = 'update';
	const ACTION_ACTIVATE                 = 'activate';

	/**
	 * Array of products data.
	 *
	 * @since 1.2.0
	 *
	 * @see   ProductManager::get_products_data()
	 *
	 * @var array
	 */
	private $products;

	/**
	 * Class constructor.
	 *
	 * @since 1.2.0
	 *
	 * @param array $products Products data. {@see ProductManager::get_product_schema()}.
	 */
	public function __construct( array $products = [] ) {
		$this->products = $products;
	}

	/**
	 * Checks if a product is a dependency of a single product.
	 *
	 * @since 1.2.0
	 *
	 * @param string $dependency_text_domain The text domain of the dependency.
	 * @param string $product_text_domain    The text domain of the product.
	 * @param bool   $active_product_only    (optional) Whether to check only active product. Default is false.
	 *
	 * @return array|false
	 */
	public function is_a_dependency_of_product( string $dependency_text_domain, string $product_text_domain, bool $active_product_only = false ) {
		if ( $dependency_text_domain === $product_text_domain ) {
			return false;
		}

		$product = $this->get_product( $product_text_domain );

		if ( ! $product ) {
			return false;
		}

		if ( $active_product_only && ! $product['active'] ) {
			return false;
		}

		$plugin_dependencies = $product['dependencies']['0.0.1']['plugin'] ?? [];

		foreach ( $plugin_dependencies as $dependency ) {
			if ( $dependency['text_domain'] === $dependency_text_domain ) {
				return [
					'name'        => $product['name'],
					'text_domain' => $product_text_domain,
				];
			}
		}

		return false;
	}

	/**
	 * Checks if a product is a dependency of any product(s).
	 *
	 * @since 1.2.0
	 *
	 * @param string $dependency_text_domain The text domain of the dependency.
	 * @param bool   $active_products_only   (optional) Whether to check only active products. Default is false.
	 *
	 * @return array|false
	 */
	public function is_a_dependency_of_any_product( string $dependency_text_domain, bool $active_products_only = false ) {
		$products = [];

		foreach ( $this->products as $product ) {
			$required_by = $this->is_a_dependency_of_product( $dependency_text_domain, $product['text_domain'], $active_products_only );

			if ( $required_by ) {
				$products[] = $required_by;
			}
		}

		return ! empty( $products ) ? $products : false;
	}

	/**
	 * Returns dependencies for a product in an order that they should be installed/activated/updated.
	 *
	 * @since 1.2.0
	 *
	 * @param string      $product_text_domain The product text domain to check.
	 * @param string|null $product_version     (Optional) The product version to check. This is used to get the right set of dependencies as they are versioned. Default: null (i.e., the installed version will be used followed by the [remote] server version).
	 *
	 * @throws Exception Exception if a circular reference is detected.
	 *
	 * @return array
	 */
	public function get_product_dependency_resolution_sequence( $product_text_domain = '', $product_version = null ): array {
		$get_product_dependency_resolution_sequence = function ( $product_text_domain, $product_version = null, $checked_products = [], $ordered_dependencies = [] ) use ( &$get_product_dependency_resolution_sequence ) {
			$product = $this->get_product( $product_text_domain );

			if ( in_array( $product_text_domain, $checked_products, true ) ) {
				throw new Exception( self::FAILURE_CIRCULAR_REFERENCE );
			}

			$checked_products[] = $product_text_domain;

			// First check if all product dependencies are met.
			$dependencies_check = $this->check_dependencies( $product_text_domain, $product_version );

			if ( ! empty( Arr::get( $dependencies_check, 'unmet.system' ) ) ) {
				throw new Exception( self::FAILURE_UNMET_DEPENDENCY );
			}

			foreach ( Arr::get( $dependencies_check, 'unmet.plugin', [] ) as $unmet_dependency ) {
				if ( ! $unmet_dependency['resolvable'] ) {
					throw new Exception( self::FAILURE_UNMET_DEPENDENCY );
				}
			}

			// Then get the resolution sequence of all dependencies.
			foreach ( Arr::get( $dependencies_check, 'unmet.plugin', [] ) as $dependency ) {
				$ordered_dependencies += $get_product_dependency_resolution_sequence(
					$dependency['text_domain'],
					$dependency['required_version'],
					$checked_products,
					$ordered_dependencies
				);
			}

			switch ( true ) {
				case $product['active'] && $product['update_available']:
				case ! $product['active'] && $product['installed'] && $product['update_available']:
				case ! $product['active'] && ! $product['installed'] && $product['update_available']:
					$action  = 'update';
					$version = $product['server_version'];

					break;
				case ! $product['active'] && ! $product['installed']:
					$action  = 'install';
					$version = $product['server_version'];

					break;
				case ! $product['active'] && $product['installed']:
					$action  = 'activate';
					$version = $product['installed_version'];

					break;
				default:
					throw new Exception( self::FAILURE_UNKNOWN_PRODUCT_ACTION );
			}

			if ( ! in_array( $product_text_domain, $ordered_dependencies, true ) ) {
				$ordered_dependencies[ $product_text_domain ] = [
					'name'        => $product['name'],
					'text_domain' => $product['text_domain'],
					'action'      => $action,
					'version'     => $version,
				];
			}

			return $ordered_dependencies;
		};

		$result = $get_product_dependency_resolution_sequence( $product_text_domain, $product_version );

		array_pop( $result );

		return array_values( $result );
	}

	/**
	 * Checks if a product meets all dependencies.
	 *
	 * @since 1.2.0
	 *
	 * @param string      $product_text_domain The product text domain to check.
	 * @param string|null $product_version     (Optional) The product version to check. This is used to get the right set of dependencies as they are versioned. Default: null (i.e., the installed version will be used followed by the [remote] server version).
	 *
	 * @return array
	 */
	public function check_dependencies( $product_text_domain = '', $product_version = null ): array {
		$check_dependencies = function ( $product_text_domain, $product_version = null, $checked_dependencies = [], $unmet_dependencies = [] ) use ( &$check_dependencies ) {
			if ( empty( $unmet_dependencies ) ) {
				$unmet_dependencies = [
					'system' => [],
					'plugin' => [],
				];
			}

			if ( isset( $checked_dependencies[ $product_text_domain ] ) ) {
				return [
					'status' => true,
					'unmet'  => $unmet_dependencies,
				];
			}

			$checked_dependencies[ $product_text_domain ] = true;

			$missing_name_placeholder = strtr(
				_x( "Product with '[text_domain]' text domain", 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
				[ '[text_domain]' => $product_text_domain ]
			);

			$product = $this->get_product( $product_text_domain );

			if ( ! $product ) {
				$unmet_dependencies['plugin'][ $product_text_domain ] = [
					'name'             => $missing_name_placeholder,
					'required_version' => '',
					'text_domain'      => $product_text_domain,
					'icon'             => '',
					'reason'           => self::FAILURE_NOT_FOUND,
					'resolvable'       => false,
				];

				return [
					'status' => false,
					'unmet'  => $unmet_dependencies,
				];
			}

			if ( ! $product_version ) {
				if ( ! empty( $product['installed_version'] ) ) {
					$product_version = $product['installed_version'];
				} else if ( ! empty( $product['server_version'] ) ) {
					$product_version = $product['server_version'];
				}

				if ( ! $product_version ) {
					$unmet_dependencies['plugin'][ $product_text_domain ] = [
						'name'             => $product['name'],
						'required_version' => '',
						'text_domain'      => $product_text_domain,
						'icon'             => $product['icon'] ?? '',
						'reason'           => self::FAILURE_MISSING_PRODUCT_VERSION,
						'resolvable'       => false,
					];

					return [
						'status' => false,
						'unmet'  => $unmet_dependencies,
					];
				}
			}

			$dependencies = $this->get_dependencies_for_product_version( $product_version, $product['dependencies'] );

			if ( is_null( $dependencies ) ) {
				$unmet_dependencies['plugin'][ $product_text_domain ] = [
					'name'             => $product['name'],
					'required_version' => $product_version,
					'text_domain'      => $product_text_domain,
					'icon'             => $product['icon'] ?? '',
					'reason'           => self::FAILURE_MISSING_DEPENDENCIES,
					'resolvable'       => false,
				];

				return [
					'status' => false,
					'unmet'  => $unmet_dependencies,
				];
			}

			foreach ( ( $dependencies['plugin'] ?? [] ) as $dependency_data ) {
				$dependency_text_domain = $dependency_data['text_domain'];

				$dependency_product = $this->get_product( ( implode( '|', [ $dependency_text_domain, $dependency_data['text_domain_legacy'] ?? '' ] ) ) );

				if ( ! $dependency_product ) {
					$checked_dependencies[ $dependency_text_domain ] = true;

					$unmet_dependencies['plugin'][ $dependency_text_domain ] = [
						'name'             => $dependency_data['name'] ?? $missing_name_placeholder,
						'required_version' => $dependency_data['version'],
						'text_domain'      => $dependency_text_domain,
						'icon'             => '',
						'reason'           => self::FAILURE_NOT_FOUND,
						'resolvable'       => false,
					];

					continue;
				}

				$dependencies_of_dependency = $this->get_dependencies_for_product_version( $dependency_data['version'], $dependency_product['dependencies'] );

				if ( ! empty( $dependency_product['dependencies'] && is_null( $dependencies_of_dependency ) ) ) {
					return [
						'status'  => true,
						'reason'  => self::FAILURE_MISSING_DEPENDENCIES,
						'product' => $dependency_product,
						'unmet'   => $unmet_dependencies,
					];
				}

				// Check for dependencies of the dependent product. Love me some recursion :D!
				if ( ! empty( $dependencies_of_dependency['plugin'] ) || ! empty( $dependencies_of_dependency['system'] ) ) {
					$dependencies_of_dependency = $check_dependencies( $dependency_text_domain, $dependency_data['version'], $checked_dependencies, $unmet_dependencies );

					if ( ! $dependencies_of_dependency['status'] ) {
						$unmet_dependencies = array_merge( $unmet_dependencies, $dependencies_of_dependency['unmet'] );
					}
				}

				$unmet_dependencies['plugin'] = $this->process_plugin_dependency( $product_text_domain, $dependency_data, $dependency_product, $unmet_dependencies['plugin'] );
			}

			foreach ( ( $dependencies['system'] ?? [] ) as $dependency_data ) {
				$unmet_dependencies['system'] = $this->process_system_dependency( $product_text_domain, $dependency_data, $unmet_dependencies['system'] );
			}

			return [
				'status' => empty( $unmet_dependencies['system'] ) && empty( $unmet_dependencies['plugin'] ),
				'unmet'  => $unmet_dependencies,
			];
		};

		$result = $check_dependencies( $product_text_domain, $product_version );

		foreach ( $result['unmet'] as &$type ) {
			$type = array_values( $type );
		}

		return $result;
	}

	/**
	 * Checks if a plugin dependency is met.
	 *
	 * @since   1.2.0
	 *
	 * @used-by ProductDependencyChecker::check_all_dependencies()
	 *
	 * @param string $required_by_product_text_domain The text domain of the product that requires the dependency.
	 * @param array  $dependency_data                 The required dependency data.
	 * @param array  $dependency_product              The dependency product data.
	 * @param array  $unmet_dependencies              (optional) Object with unmet dependencies that will be updated. Default: empty array.
	 *
	 * @return array
	 */
	private function process_plugin_dependency( $required_by_product_text_domain, $dependency_data, $dependency_product, $unmet_dependencies = [] ): array {
		$dependency_text_domain = $dependency_data['text_domain'];

		// If there are multiple versions of the same dependency required by more than one product, use the highest version as the required version.
		$highest_required_version = Arr::get( $unmet_dependencies, "{$dependency_text_domain}.required_version" );
		$highest_required_version = version_compare( $highest_required_version ?? 0, $dependency_data['version'], '<' ) ? $dependency_data['version'] : $highest_required_version;

		$unmet_dependency = [
			'name'              => $dependency_product['name'],
			'text_domain'       => $dependency_text_domain,
			'icon'              => ( $dependency_data['icon'] ?? $dependency_product['icon'] ) ?? '',
			'active'            => $dependency_product['active'],
			'installed'         => $dependency_product['installed'],
			'installed_version' => $dependency_product['installed_version'],
			'server_version'    => $dependency_product['server_version'],
			'required_version'  => $highest_required_version,
			'required_by'       => array_merge(
				Arr::get( $unmet_dependencies, "{$dependency_text_domain}.required_by", [] ),
				[ $required_by_product_text_domain => $dependency_data['version'] ]
			),
		];

		if ( ! $dependency_product['installed'] ) {
			switch ( true ) {
				// Free but without download link.
				case $dependency_product['free'] && ! $dependency_product['download_link']:
					$unmet_dependency['reason']     = self::FAILURE_NO_DOWNLOAD_LINK;
					$unmet_dependency['resolvable'] = false;

					break;
				// Unlicensed product.
				case empty( $dependency_product['licenses'] ) && ! $dependency_product['free']:
					$unmet_dependency['reason']     = self::FAILURE_UNLICENSED;
					$unmet_dependency['resolvable'] = false;

					break;
				// Low version.
				case version_compare( $dependency_product['server_version'] ?? 0, $highest_required_version, '<' ):
					$unmet_dependency['reason']     = self::FAILURE_OLDER_VERSION;
					$unmet_dependency['resolvable'] = false;

					break;
				// Product is not and can't be installed because it's not licensed and doesn't have a download link (i.e., a free product).
				case empty( $dependency_product['licenses'] ) && ! $dependency_product['download_link']:
					$unmet_dependency['reason']     = self::FAILURE_NOT_INSTALLED;
					$unmet_dependency['resolvable'] = false;

					break;
				// Product is not installed but can be.
				default:
					$unmet_dependency['reason']     = self::FAILURE_NOT_INSTALLED;
					$unmet_dependency['resolvable'] = true;

					break;
			}
		}

		if ( $dependency_product['installed'] ) {
			switch ( true ) {
				// Unlicensed product.
				case empty( $dependency_product['licenses'] ) && ! $dependency_product['free']:
					$unmet_dependency['reason']     = self::FAILURE_UNLICENSED;
					$unmet_dependency['resolvable'] = false;

					break;
				// Low version and no update available OR Update available but low version.
				case ! $dependency_product['update_available'] && version_compare( $dependency_product['installed_version'] ?? 0, $highest_required_version, '<' ):
				case $dependency_product['update_available'] && version_compare( $dependency_product['server_version'] ?? 0, $highest_required_version, '<' ):
					$unmet_dependency['reason']     = self::FAILURE_OLDER_VERSION;
					$unmet_dependency['resolvable'] = false;

					break;
				// Update available and the version is >= than the one required.
				case $dependency_product['update_available'] && ( version_compare( $dependency_product['installed_version'] ?? 0, $highest_required_version, '<' ) && version_compare( $dependency_product['server_version'] ?? 0, $highest_required_version, '>=' ) ):
					$unmet_dependency['reason']     = self::FAILURE_OLDER_VERSION;
					$unmet_dependency['resolvable'] = true;

					break;
				// Product is not active.
				case ! $dependency_product['active']:
					$unmet_dependency['reason']     = self::FAILURE_INACTIVE;
					$unmet_dependency['resolvable'] = true;
					break;
			}
		}

		if ( isset( $unmet_dependency['reason'] ) ) {
			$unmet_dependencies[ $dependency_text_domain ] = $unmet_dependency;
		}

		return $unmet_dependencies;
	}

	/**
	 * Checks if a system dependency is met.
	 *
	 * @since   1.2.0
	 *
	 * @used-by ProductDependencyChecker::check_all_dependencies()
	 *
	 * @param string $required_by_product_text_domain The text domain of the product that requires the dependency.
	 * @param array  $dependency_data                 The required dependency data.
	 * @param array  $unmet_dependencies              (optional) Object with unmet dependencies that will be updated. Default: empty array.
	 *
	 * @return array
	 */
	private function process_system_dependency( $required_by_product_text_domain, $dependency_data, $unmet_dependencies = [] ): array {
		global $wp_version;

		$dependency_name = $dependency_data['name'];

		// If there are multiple versions of the same dependency required by more than one product, use the highest version as the required version.
		$highest_required_version = Arr::get( $unmet_dependencies, "{$dependency_name}.required_version" );
		$highest_required_version = version_compare( $highest_required_version ?? 0, $dependency_data['version'], '<' ) ? $dependency_data['version'] : $highest_required_version;

		$unmet_dependency = [
			'name'              => $dependency_name,
			'icon'              => $dependency_data['icon'] ?? '',
			'available_version' => null,
			'required_version'  => $highest_required_version,
			'required_by'       => array_merge(
				Arr::get( $unmet_dependencies, "{$dependency_name}.required_by", [] ),
				[ $required_by_product_text_domain => $dependency_data['version'] ]
			),
		];

		switch ( $dependency_name ) {
			case 'PHP':
				if ( ! is_php_version_compatible( $highest_required_version ) ) {
					$unmet_dependency['available_version'] = PHP_VERSION;
					$unmet_dependency['reason']            = self::FAILURE_OLDER_VERSION;
					$unmet_dependency['resolvable']        = false;
				}

				break;
			case 'WordPress':
				if ( ! is_wp_version_compatible( $highest_required_version ) ) {
					$unmet_dependency['available_version'] = $wp_version;
					$unmet_dependency['reason']            = self::FAILURE_OLDER_VERSION;
					$unmet_dependency['resolvable']        = false;
				}

				break;
			default:
				unset( $unmet_dependency['available_version'] );
				$unmet_dependency['reason']     = self::FAILURE_NOT_FOUND;
				$unmet_dependency['resolvable'] = false;

				break;
		}

		if ( isset( $unmet_dependency['reason'] ) ) {
			$unmet_dependencies[ $dependency_name ] = $unmet_dependency;
		}

		return $unmet_dependencies;
	}

	/**
	 * Returns product data.
	 * If the product is not GK (as returned by our EDD API) product, it will be searched in the installed plugins.
	 *
	 * @since 1.2.0
	 *
	 * @param string|null $text_domain_str Text domain(s). Optionally pipe-separated (e.g. 'gravityview|gk-gravtiyview').
	 *
	 * @return array|null
	 */
	private function get_product( $text_domain_str = '' ): ?array {
		$text_domains_arr = explode( '|', $text_domain_str );

		foreach ( $text_domains_arr as $text_domain ) {
			$gk_product = Arr::first(
				$this->products,
				function ( $product ) use ( $text_domain ) {
					return $product['text_domain'] === $text_domain;
				}
			);

			if ( $gk_product ) {
				return ProductManager::get_instance()->normalize_product_data( $gk_product );
			}
		}

		$non_gk_product = CoreHelpers::get_installed_plugin_by_text_domain( $text_domain_str );

		if ( $non_gk_product ) {
			$non_gk_product = ProductManager::get_instance()->normalize_product_data( $non_gk_product );

		}

		return $non_gk_product;
	}

	/**
	 * Returns dependencies based on the version.
	 *
	 * @since 1.2.0
	 *
	 * @param string|null $product_version
	 * @param array        $dependencies
	 *
	 * @return mixed|null
	 */
	private function get_dependencies_for_product_version( $product_version, $dependencies ) {
		$dependencies_versions = array_keys( $dependencies );

		if ( empty( $dependencies_versions ) ) {
			return null;
		}

		$compatible_version = array_filter( $dependencies_versions, function ( $dependency_version ) use ( $product_version ) {
			return version_compare( $dependency_version, $product_version, '<=' );
		} );


		if ( empty( $compatible_version ) ) {
			return null;
		}

		$compatible_version = max( $compatible_version );

		return $dependencies[ $compatible_version ] ?? null;
	}
}
