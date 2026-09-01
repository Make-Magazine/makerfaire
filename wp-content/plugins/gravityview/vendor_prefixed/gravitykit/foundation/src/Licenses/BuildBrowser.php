<?php

namespace GravityKit\GravityView\Foundation\Licenses;

use Exception;
use GravityKit\GravityView\Foundation\Helpers\Core as CoreHelpers;
use GravityKit\GravityView\Foundation\Helpers\WP;
use GravityKit\GravityView\Foundation\Licenses\Integrity\PackageVerifier;
use GravityKit\GravityView\Foundation\Logger\Framework as LoggerFramework;

/**
 * Lists other builds of a product and installs the one the user picks.
 *
 * This is how a site owner reinstalls an earlier release without leaving WordPress: no account-page
 * download, no manual zip upload, no support ticket asking for a link. The store decides which
 * builds a license may see -- earlier tagged releases for everyone it is enabled for, plus untagged
 * branch builds for internal licenses -- so this class never has to know which audience it serves.
 *
 * @since 1.31.0
 */
final class BuildBrowser {
	/**
	 * Store API path that lists the builds a license may install.
	 *
	 * @since 1.31.0
	 */
	const BUILDS_PATH = '/products/%d/builds';

	/**
	 * How many builds one listing may carry. The store caps this too; asking for more is pointless.
	 *
	 * @since 1.31.0
	 */
	const MAX_LIMIT = 100;

	/**
	 * @since 1.31.0
	 *
	 * @var BuildBrowser|null
	 */
	private static $_instance = null;

	/**
	 * Returns class instance.
	 *
	 * @since 1.31.0
	 *
	 * @return BuildBrowser
	 */
	public static function get_instance(): BuildBrowser {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Initializes the class.
	 *
	 * @since 1.31.0
	 *
	 * @return void
	 */
	public function init() {
		static $initialized;

		if ( $initialized ) {
			return;
		}

		add_filter( 'gk/foundation/ajax/' . Framework::AJAX_ROUTER . '/routes', [ $this, 'configure_ajax_routes' ] );

		$initialized = true;
	}

	/**
	 * Configures Ajax routes handled by this class.
	 *
	 * @since 1.31.0
	 *
	 * @see   \GravityKit\GravityView\Foundation\Core::process_ajax_request()
	 *
	 * @param array $routes Ajax action to class method map.
	 *
	 * @return array
	 */
	public function configure_ajax_routes( array $routes ) {
		return array_merge(
			$routes,
			[
				'get_product_builds'    => [ $this, 'ajax_get_builds' ],
				'install_product_build' => [ $this, 'ajax_install_build' ],
			]
		);
	}

	/**
	 * Ajax request to list the builds available for a product.
	 *
	 * @since 1.31.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array{builds: array, total: int, can_browse_branches: bool}
	 */
	public function ajax_get_builds( array $payload ): array {
		$payload = wp_parse_args(
			$payload,
			[
				'text_domain' => '',
				'search'      => '',
			]
		);

		$product = $this->get_authorized_product( (string) $payload['text_domain'] );
		$builds  = $this->fetch_builds( $product, (string) $payload['search'] );

		// The download URL is a signed, short-lived token; it never needs to reach the browser.
		// The install request names a build by version and commit and is re-resolved server-side.
		$listing = array_map(
			static function ( array $build ): array {
				unset( $build['download'], $build['signature'], $build['signing_key_id'], $build['sha256'] );

				return $build;
			},
			$builds['builds']
		);

		return [
			'builds'              => $listing,
			'total'               => $builds['total'] ?: count( $listing ),
			'can_browse_branches' => ! empty( $builds['can_browse_branches'] ),
		];
	}

	/**
	 * Ajax request to install a specific build of a product.
	 *
	 * @since 1.31.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array{products: array, activation_error: null|string, ui_action: array}
	 */
	public function ajax_install_build( array $payload ): array {
		$payload = wp_parse_args(
			$payload,
			[
				'text_domain'                 => '',
				'version'                     => '',
				'commit'                      => '',
				'frontend_foundation_version' => 0,
			]
		);

		$product = $this->get_authorized_product( (string) $payload['text_domain'] );

		if ( ! $product['installed'] ) {
			throw new Exception( esc_html__( 'Install the product before choosing a different build of it.', 'gk-gravityview' ) );
		}

		// Re-resolve the build from the store rather than trusting the browser's copy of it. The
		// request names a build; the store decides whether this license may have it and what its
		// signature is.
		$build = $this->find_build( $product, (string) $payload['version'], (string) $payload['commit'] );

		if ( ! $build ) {
			throw new Exception( esc_html__( 'That build is no longer available for this license.', 'gk-gravityview' ) );
		}

		$product_manager = ProductManager::get_instance();

		$was_active = (bool) $product['active'];

		PackageVerifier::$is_selected_build_install = true;
		PackageVerifier::$selected_build            = [
			'signature'      => $build['signature'],
			'signing_key_id' => $build['signing_key_id'],
			'sha256'         => $build['sha256'],
			'filename'       => $build['filename'],
			'slug'           => $product['slug'] ?? '',
		];

		try {
			$product_manager->update_product(
				$product,
				[
					'download_url' => $build['download'],
					'version'      => $build['version'],
				]
			);
		} finally {
			PackageVerifier::$is_selected_build_install = false;
			PackageVerifier::$selected_build            = null;
		}

		// Installing a specific build takes the product off whatever channel it was following: the
		// build is now pinned by hand, and leaving the preference would offer channel updates over it.
		//
		// The installed version is passed only when there WAS a channel. That argument records which
		// build a site was on when it left a channel, and a product that never followed one has no
		// such provenance to record.
		ChannelManager::get_instance()->clear_channel(
			$product['text_domain'],
			$product['channel'] ? (string) $build['version'] : ''
		);

		WP::delete_transient( ProductManager::PRODUCTS_DATA_CACHE_ID );

		// A refetch needs the store, which may be unreachable moments after the install. The build is
		// already on disk either way, so fall back to what we knew going in rather than leaving the
		// product installed and deactivated.
		$product = $product_manager->get_first_product_by_payload(
			[ 'text_domain' => $product['text_domain'] ],
			[ 'skip_request_cache' => true ]
		) ?? $product;

		return $product_manager->finalize_product_change( $product, $was_active, $payload['frontend_foundation_version'] );
	}

	/**
	 * Returns the product when the current user may install builds of it, or throws.
	 *
	 * @since 1.31.0
	 *
	 * @param string $text_domain Product text domain.
	 *
	 * @throws Exception
	 *
	 * @return array
	 */
	private function get_authorized_product( string $text_domain ): array {
		if ( ! Framework::get_instance()->current_user_can( 'update_products' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-gravityview' ) );
		}

		$product = ProductManager::get_instance()->get_first_product_by_payload( [ 'text_domain' => $text_domain ] );

		if ( ! $product ) {
			throw new Exception(
				strtr(
					esc_html_x( "Product with '[text_domain]' text domain not found.", 'Placeholders inside [] are not to be translated.', 'gk-gravityview' ),
					[ '[text_domain]' => $text_domain ]
				)
			);
		}

		if ( empty( $product['builds']['available'] ) ) {
			throw new Exception( esc_html__( 'Installing other builds is not available for this product.', 'gk-gravityview' ) );
		}

		// The same licensing scope the ordinary update path enforces. On multisite the products data
		// carries licenses mirrored from other blogs, so availability alone does not mean this site
		// may install anything.
		if ( isset( $product['licenses'] ) && ! ProductManager::get_instance()->is_product_update_authorized( $product ) ) {
			throw new Exception( esc_html__( 'An active license is required to install builds of this product.', 'gk-gravityview' ) );
		}

		return $product;
	}

	/**
	 * Returns one build matching a version and commit, or null.
	 *
	 * Asks the store for that exact build rather than searching and picking from the result: a
	 * search can be truncated by the response cap, and an install must never depend on where a
	 * build happened to land in a list.
	 *
	 * @since 1.31.0
	 *
	 * @param array  $product Product data.
	 * @param string $version Build version.
	 * @param string $commit  Commit hash, if the build has one.
	 *
	 * @return array|null
	 */
	private function find_build( array $product, string $version, string $commit ): ?array {
		if ( '' === $version ) {
			return null;
		}

		$response = $this->fetch_builds( $product, '', $version, $commit );

		foreach ( $response['builds'] as $build ) {
			if ( $build['version'] !== $version || (string) $build['commit'] !== $commit ) {
				continue;
			}

			if ( empty( $build['download'] ) || empty( $build['signature'] ) || empty( $build['filename'] ) ) {
				return null;
			}

			// The signature covers the filename, not the version label beside it. Requiring the
			// filename to name the version being installed is what stops a relabelled row handing
			// back a different -- genuinely signed -- build than the one the user picked, which the
			// rollback waiver would then let through.
			if ( ! $this->filename_names_version( $build['filename'], $version ) ) {
				return null;
			}

			return $build;
		}

		return null;
	}

	/**
	 * Whether a build filename actually names the version claimed for it.
	 *
	 * @since 1.31.0
	 *
	 * @param string $filename Build filename, e.g. `gravityview-3.1.1.zip`.
	 * @param string $version  Version the row claims.
	 *
	 * @return bool
	 */
	private function filename_names_version( string $filename, string $version ): bool {
		return (bool) preg_match( '/-' . preg_quote( $version, '/' ) . '\.zip$/i', $filename );
	}

	/**
	 * Asks the store which builds this product's license may install.
	 *
	 * @since 1.31.0
	 *
	 * @param array  $product Product data.
	 * @param string $search  Free-text filter over version, branch and commit.
	 * @param string $version Exact version to look up. Narrows to one build with $commit.
	 * @param string $commit  Exact commit to look up.
	 *
	 * @throws Exception
	 *
	 * @return array{builds: array, total: int, can_browse_branches: bool}
	 */
	private function fetch_builds( array $product, string $search, string $version = '', string $commit = '' ): array {
		$license_key = $this->resolve_license_key( $product );

		if ( ! $license_key ) {
			throw new Exception( esc_html__( 'An active license is required to install other builds of this product.', 'gk-gravityview' ) );
		}

		$url = LicenseManager::store_url() . sprintf( self::BUILDS_PATH, (int) $product['id'] );

		try {
			$response = Helpers::query_api(
				$url,
				[
					'license'     => $license_key,
					'search'      => $search,
					'version'     => $version,
					'commit'      => $commit,
					'limit'       => self::MAX_LIMIT,
					'url'         => is_multisite() ? network_home_url() : home_url(),
					'environment' => CoreHelpers::get_environment_type(),
				]
			);
		} catch ( Exception $e ) {
			LoggerFramework::get_instance()->warning( 'Unable to list product builds: ' . $e->getMessage() );

			throw new Exception( esc_html__( 'Unable to retrieve the list of builds. Please try again.', 'gk-gravityview' ) );
		}

		return [
			'builds'              => $this->normalize_builds( $response['builds'] ?? null ),
			'total'               => (int) ( $response['total'] ?? 0 ),
			'can_browse_branches' => ! empty( $response['can_browse_branches'] ),
		];
	}

	/**
	 * Coerces the store's build rows into the shape the rest of this class assumes.
	 *
	 * Everything here came off the wire, so a row that is not an array, or one carrying an array
	 * where a string belongs, must be dropped rather than reach a typed callback.
	 *
	 * @since 1.31.0
	 *
	 * @param mixed $rows Raw `builds` value from the store response.
	 *
	 * @return array
	 */
	private function normalize_builds( $rows ): array {
		if ( ! is_array( $rows ) ) {
			return [];
		}

		$fields     = [ 'version', 'filename', 'commit', 'branch', 'tag', 'signature', 'signing_key_id', 'sha256' ];
		$normalized = [];

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$build = [ 'released' => (int) ( is_scalar( $row['released'] ?? null ) ? $row['released'] : 0 ) ];

			foreach ( $fields as $field ) {
				$value = $row[ $field ] ?? '';

				$build[ $field ] = is_scalar( $value ) ? (string) $value : '';
			}

			$build['download'] = is_scalar( $row['download'] ?? null ) ? (string) $row['download'] : '';

			if ( '' === $build['version'] || '' === $build['filename'] ) {
				continue;
			}

			$normalized[] = $build;
		}

		return $normalized;
	}

	/**
	 * Returns a license key that entitles this site to the product, or an empty string.
	 *
	 * @since 1.31.0
	 *
	 * Prefers a license active for this site or network, so the store answers for the right one.
	 *
	 * @param array $product Product data.
	 *
	 * @return string
	 */
	private function resolve_license_key( array $product ): string {
		$license_manager = LicenseManager::get_instance();
		$licenses_data   = $license_manager->get_all_licenses_data();

		$fallback = '';

		foreach ( (array) ( $product['licenses'] ?? [] ) as $license_key ) {
			$license = $licenses_data[ $license_key ] ?? null;

			if ( ! is_array( $license ) ) {
				continue;
			}

			if ( empty( $license['products'][ $product['id'] ]['builds']['available'] ) ) {
				continue;
			}

			// On multisite this list carries licenses mirrored from other blogs. One of those can be
			// entitled without being this site's license, and the store decides what a license may
			// browse -- so a mirrored key would answer for the wrong site.
			if ( $license_manager->is_license_active_for_site( $license )
				|| $license_manager->is_license_active_for_network( $license ) ) {
				return (string) $license_key;
			}

			if ( '' === $fallback ) {
				$fallback = (string) $license_key;
			}
		}

		return $fallback;
	}
}
