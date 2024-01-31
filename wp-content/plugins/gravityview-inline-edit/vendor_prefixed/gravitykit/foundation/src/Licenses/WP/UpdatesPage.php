<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 02-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Licenses\WP;

use GravityKit\GravityEdit\Foundation\Licenses\Framework;
use GravityKit\GravityEdit\Foundation\Licenses\ProductManager;
use WP_Error;

/**
 * Manages the display of GK products on the Updates page (Dashboard > Updates) & optionally prevents updating them.
 *
 * @since 1.2.0
 */
class UpdatesPage {
	/**
	 * @since 1.2.0
	 *
	 * @var UpdatesPage Class instance.
	 */
	private static $_instance;

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

		add_filter( 'init', [ $this, 'modify_display_of_updates_table' ] );
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

		$products = array_filter( ProductManager::get_instance()->get_products_data( [ 'key_by' => 'path' ] ), function ( $product ) {
			return ! $product['third_party'] && $product['update_available'] && ( ! $product['checked_dependencies'][ $product['server_version'] ]['status'] ?? false );
		} );

		$product_html_markup = array_map( function ( $product ) {
			$product_logo_text = strtr(
				esc_html__( '[product] logo', 'gk-gravityedit' ),
				[
					'[product]' => $product['name'],
				]
			);

			$update_description_text = strtr(
				esc_html_x( 'You have version [current_version] installed. Before updating to [new_version], please [link]review the requirements[/link].', 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
				[
					'[current_version]' => $product['installed_version'],
					'[new_version]'     => $product['server_version'],
					'[link]'            => '<a href="' . esc_url_raw( add_query_arg( [ 'action' => 'update' ], Framework::get_instance()->get_link_to_product_search( $product['id'] ) ) ) . '">',
					'[/link]'           => '</a>',
				]
			);

			return <<<HTML
<tr>
	<td class="check-column"></td>
	<td class="plugin-title">
		<p>
			<img src="{$product['icon']}" alt="{$$product_logo_text}">
			<strong>{$product['name']}</strong>
			{$update_description_text}
		</p>
	</td>
</tr>
HTML;
		}, $products );

		$product_html_markup = json_encode( array_combine( array_keys( $products ), $product_html_markup ) );

		// Update table row of each product with unmet dependencies.
		add_filter( 'gk/foundation/inline-scripts', function ( $scripts ) use ( $product_html_markup ) {
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
		} );
	}

	/**
	 * Optionally prevents updating a product if it has unmet dependencies.
	 * While we prevent that from the Plugins and Updates pages, let's take a step further and prevent it in the backend when WP is about to install the product.
	 *
	 * @param bool|WP_Error $response
	 * @param array         $args
	 *
	 * @return bool|WP_Error
	 */
	public function prevent_update( $response, $args ) {
		if ( is_wp_error( $response ) || ! isset( $args['plugin'] ) || ! isset( $products[ $args['plugin'] ] ) ) {
			return $response;
		}

		$product = $products[ $args['plugin'] ];

		if ( $product['checked_dependencies'][ $product['server_version'] ]['status'] ?? false ) {
			return $response;
		}

		return new WP_Error( 'gk_product_unmet_dependency', strtr(
			esc_html_x( 'This product has unmet dependencies. [link]Review the requirements[/link] and try updating again.', 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
			[
				'[link]'  => '<a href="' . esc_url_raw( add_query_arg( [ 'action' => 'update' ], Framework::get_instance()->get_link_to_product_search( $product['id'] ) ) ) . '" target="_parent">',
				'[/link]' => '</a>',
			]
		) );
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
