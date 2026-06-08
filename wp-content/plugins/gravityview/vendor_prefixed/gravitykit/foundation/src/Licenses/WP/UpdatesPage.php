<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Licenses\WP;

use GravityKit\GravityView\Foundation\Licenses\Framework;
use GravityKit\GravityView\Foundation\Licenses\ProductManager;
use GravityKit\GravityView\Foundation\WP\AdminMenu;
use WP_Error;

/**
 * Manages the display of GK products on the Updates page (Dashboard > Updates) & optionally prevents updating them.
 *
 * @since 1.2.0
 */
class UpdatesPage {
	/**
	 * Class instance.
	 *
	 * @since 1.2.0
	 *
	 * @var UpdatesPage|null;
	 */
	private static $_instance = null;

	/**
	 * Returns class instance.
	 *
	 * @since 1.2.0
	 *
	 * @return UpdatesPage
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Initializes the class.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function init() {
		static $initialized = false;

		if ( $initialized ) {
			return;
		}

		add_action( 'init', [ $this, 'modify_display_of_updates_table' ] );
		add_filter( 'upgrader_pre_install', [ $this, 'prevent_update' ], 10, 2 );

		$initialized = true;
	}

	/**
	 * Modifies the display of the Updates table.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function modify_display_of_updates_table() {
		if ( ! $this->is_updates_page() ) {
			return;
		}

		if ( ! AdminMenu::should_initialize() ) {
			return;
		}

		$update_transient = get_site_transient( 'update_plugins' );

		$products = array_filter(
			ProductManager::get_instance()->get_products_data( [ 'key_by' => 'path' ] ),
			function ( $product ) use ( $update_transient ) {
				if ( $product['third_party'] || empty( $product['path'] ) ) {
					return false;
				}

				// Use the version from the WP update transient (set by EDD.php) to check dependencies.
				$offered_version = $update_transient->response[ $product['path'] ]->new_version ?? null;

				return $offered_version && ! ( $product['checked_dependencies'][ $offered_version ]['status'] ?? true );
			}
		);

		$product_html_markup = array_map(
			function ( $product ) use ( $update_transient ) {
				$offered_version = $update_transient->response[ $product['path'] ]->new_version ?? $product['server_version'];

				$product_logo_text = strtr(
					esc_html__( '[product] logo', 'gk-gravityview' ),
					[
						'[product]' => $product['name'],
					]
				);

				$update_description_text = strtr(
					esc_html_x( 'You have version [current_version] installed. Before updating to [new_version], please [link]review the requirements[/link].', 'Placeholders inside [] are not to be translated.', 'gk-gravityview' ),
					[
						'[current_version]' => $product['installed_version'],
						'[new_version]'     => $offered_version,
						'[link]'            => '<a href="' . esc_url_raw( add_query_arg( [ 'action' => 'update' ], Framework::get_instance()->get_link_to_product_search( $product['id'] ) ) ) . '">',
						'[/link]'           => '</a>',
					]
				);

				return <<<HTML
<tr>
	<td class="check-column"></td>
	<td class="plugin-title">
		<p>
			<img src="{$product['icon']}" alt="{$product_logo_text}">
			<strong>{$product['name']}</strong>
			{$update_description_text}
		</p>
	</td>
</tr>
HTML;
			},
			$products
		);

		$product_html_markup = wp_json_encode( array_combine( array_keys( $products ), $product_html_markup ) );

		// Update table row of each product with unmet dependencies.
		add_filter(
			'gk/foundation/inline-scripts',
			function ( $scripts ) use ( $product_html_markup ) {
				$scripts[]['script'] = <<<JS
document.addEventListener( 'DOMContentLoaded', function () {
	const product_rows = {$product_html_markup};

	document.querySelectorAll( '#update-plugins-table td.check-column input' ).forEach( input => {
		if ( product_rows[ input.value ] === undefined ) {
			return;
		}

		input.closest( 'tr' ).outerHTML = product_rows[ input.value ];
	} );
} );
JS;

				return $scripts;
			}
		);
	}

	/**
	 * Optionally prevents updating a product if it has unmet dependencies.
	 * While we prevent that from the Plugins and Updates pages, let's take a step further and prevent it in the backend when WP is about to install the product.
	 *
	 * @since 1.2.0
	 *
	 * @param bool|WP_Error $response Update response.
	 * @param array         $args     Extra arguments passed to hooked filters.
	 *
	 * @return bool|WP_Error
	 */
	public function prevent_update( $response, $args ) {
		if ( is_wp_error( $response ) || ! isset( $args['plugin'] ) ) {
			return $response;
		}

		if ( ! AdminMenu::should_initialize() ) {
			return $response;
		}

		$products = ProductManager::get_instance()->get_products_data( [ 'key_by' => 'path' ] );

		$product = $products[ $args['plugin'] ] ?? null;

		if ( ! $product || $product['third_party'] ) {
			return $response;
		}

		// Check if the version being installed has unmet dependencies.
		// Use the WP update transient (set by EDD.php) to determine the offered version.
		$update_transient = get_site_transient( 'update_plugins' );
		$offered_version  = $update_transient->response[ $product['path'] ]->new_version ?? null;

		// If the offered version has met deps (or no transient entry), allow the install.
		// Also allow if stable deps are met — the install may be a "switch to stable" action.
		$offered_deps_ok = ! $offered_version || ( $product['checked_dependencies'][ $offered_version ]['status'] ?? true );
		$stable_deps_ok  = $product['checked_dependencies'][ $product['server_version'] ]['status'] ?? true;

		if ( $offered_deps_ok || $stable_deps_ok ) {
			return $response;
		}

		return new WP_Error(
			'gk_product_unmet_dependency',
			strtr(
				esc_html_x( 'This product has unmet dependencies. [link]Review the requirements[/link] and try updating again.', 'Placeholders inside [] are not to be translated.', 'gk-gravityview' ),
				[
					'[link]'  => '<a href="' . esc_url_raw( add_query_arg( [ 'action' => 'update' ], Framework::get_instance()->get_link_to_product_search( $product['id'] ) ) ) . '" target="_parent">',
					'[/link]' => '</a>',
				]
			)
		);
	}

	/**
	 * Determines whether the current page is an Updates page.
	 *
	 * @since 1.2.0
	 *
	 * @return bool
	 */
	public function is_updates_page() {
		global $pagenow;

		return is_admin() && 'update-core.php' === $pagenow;
	}
}
