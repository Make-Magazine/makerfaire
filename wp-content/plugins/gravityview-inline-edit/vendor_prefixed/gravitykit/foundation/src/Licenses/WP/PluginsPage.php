<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 02-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Licenses\WP;

use GravityKit\GravityEdit\Foundation\Core;
use GravityKit\GravityEdit\Foundation\Helpers\Arr;
use GravityKit\GravityEdit\Foundation\Licenses\Framework;
use GravityKit\GravityEdit\Foundation\Settings\Framework as SettingsFramework;
use GravityKit\GravityEdit\Foundation\Licenses\ProductManager;
use GravityKit\GravityEdit\Foundation\Licenses\LicenseManager;

/**
 * Manages the display of GK products on the Plugins page.
 *
 * @since 1.2.0
 */
class PluginsPage {
	/**
	 * @since 1.2.0
	 *
	 * @var PluginsPage Class instance.
	 */
	private static $_instance;

	/**
	 * Returns class instance.
	 *
	 * @since 1.2.0
	 *
	 * @return PluginsPage
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

		add_filter( 'init', [ $this, 'configure_hooks' ] );

		$initialized = true;
	}

	/**
	 * Adds various hooks on 'init'.
	 *
	 * @since 1.2.0
	 *
	 * @return void|bool
	 */
	public function configure_hooks() {
		if ( ! $this->is_plugins_page() ) {
			return;
		}

		add_filter( 'all_plugins', [ $this, 'group_products' ], 10, 2 );

		add_action( 'after_plugin_row', [ $this, 'enqueue_update_notices' ], 10, 2 );

		add_action( 'after_plugin_row', [ $this, 'enqueue_unlicensed_notices' ], 10, 2 );

		add_action( 'after_plugin_row', [ $this, 'display_notices' ], 11, 2 );

		add_filter( 'plugin_action_links', [ $this, 'modify_product_action_links' ], 10, 3 );

		// Disable/enable the "Group GravityKit products" setting.
		if ( isset( $_REQUEST['gk_disable_grouping'] ) || isset( $_REQUEST['gk_enable_grouping'] ) ) {
			SettingsFramework::get_instance()->save_plugin_setting( Core::ID, 'group_gk_products', isset( $_REQUEST['gk_enable_grouping'] ) );

			return wp_safe_redirect( remove_query_arg( isset( $_REQUEST['gk_enable_grouping'] ) ? 'gk_enable_grouping' : 'gk_disable_grouping' ) );
		}

		// Add action to links that require confirmation.
		add_filter( 'gk/foundation/inline-scripts', function ( $scripts ) {
			$scripts[]['script'] = <<<JS
document.addEventListener( 'DOMContentLoaded', function () {
	document.querySelectorAll( 'a[data-gk-product-confirmation]').forEach( link => {
		link.addEventListener( 'click', ( e ) => !confirm( link.dataset.gkProductConfirmation ) && e.preventDefault() );
	} );
} );
JS;

			return $scripts;
		} );

		// Prevent WordPress from displaying an update notice for each unlicensed product or that with unmet dependencies.
		// Instead, we display our own notice (@see PluginsPage::enqueue_update_notices()).
		// 1. Save the current update data count and return it when 'wp_get_update_data' fires, which happens after 'site_transient_update_plugins' filter that we use in the second step to remove plugins.
		if ( function_exists( 'wp_get_update_data' ) ) {
			$update_data_backup = wp_get_update_data();

			add_filter( 'wp_get_update_data', function () use ( $update_data_backup ) {
				return $update_data_backup;
			}, 10 );
		}

		// 2. Remove plugins from the list of those that have updates available.
		add_filter( 'site_transient_update_plugins', function ( $data ) {
			if ( ! isset( $data->response ) ) {
				return $data;
			}

			$products = ProductManager::get_instance()->get_products_data();

			foreach ( $data->response as $plugin_path => $plugin ) {
				if ( ! isset( $plugin->gk_product_text_domain ) || ! isset( $products[ $plugin->gk_product_text_domain ] ) ) {
					continue;
				}

				$product = $products[ $plugin->gk_product_text_domain ];

				if ( ! $product['update_available'] ) {
					continue;
				}

				if ( ! $product['checked_dependencies'][ $product['server_version'] ]['status'] ?? false ) {
					unset( $data->response[ $plugin_path ] );
				}

				if ( ! $product['free'] && empty( $product['licenses'] ) ) {
					unset( $data->response[ $plugin_path ] );
				}
			}

			return $data;
		} );
	}

	/**
	 * Modifies action links (e.g., Settings, Support, etc.) for each product or grouped products on the Plugins page.
	 *
	 * @since 1.2.0
	 *
	 * @param array  $links       Links associated with the product.
	 * @param string $plugin_path Plugin path.
	 * @param array  $plugin_data Plugin data.
	 *
	 * @return array
	 */
	public function modify_product_action_links( $links, $plugin_path, $plugin_data ) {
		static $products;

		if ( ! $products ) {
			$products = ProductManager::get_instance()->get_products_data();

			$products = array_filter( $products, function ( $product ) {
				return ! $product['third_party'];
			} );
		}

		if ( empty( $products ) ) {
			return $links;
		}

		// If this is a grouped entry for GravityKit products, display custom links and return early.
		if ( isset( $plugin_data['GravityKitGroup'] ) ) {
			return [
				'manage'           => sprintf(
					'<a href="%s">%s</a>',
					esc_url_raw( add_query_arg( [ 'page' => Framework::ID ], admin_url( 'admin.php' ) ) ),
					esc_html__( 'Manage Your Kit', 'gk-gravityedit' )
				),
				'settings'         => sprintf(
					'<a href="%s">%s</a>',
					esc_url_raw( add_query_arg( [ 'page' => SettingsFramework::ID ], admin_url( 'admin.php' ) ) ),
					esc_html__( 'Settings', 'gk-gravityedit' )
				),
				'disable_grouping' => sprintf(
					'<a href="%s" title="%s">%s</a>',
					esc_url_raw( add_query_arg( [ 'gk_disable_grouping' => 1 ], admin_url( 'plugins.php' ) ) ),
					esc_attr__( 'Disable the grouping of GravityKit products', 'gk-gravityedit' ),
					esc_html__( 'Ungroup', 'gk-gravityedit' )
				),
			];
		}

		$product = $products[ $plugin_data['TextDomain'] ] ?? null;

		$gk_links = [];

		if ( $product ) {
			if ( ! $product['active'] ) {
				// Modify Activate link for products that have unmet dependencies.
				if ( ! $product['checked_dependencies'][ $product['installed_version'] ]['status'] ?? false ) {
					$links['activate'] = sprintf(
						'<a href="%s" title="%s">%s</a>',
						esc_url_raw( add_query_arg( [ 'action' => 'activate' ], Framework::get_instance()->get_link_to_product_search( $product['id'] ) ) ),
						esc_html__( 'This product has unmet dependencies. Click this link to see see what they are.', 'gk-gravityedit' ),
						esc_html__( 'Activate…', 'gk-gravityedit' )
					);
				}

				// Modify Delete link for products that are installed from a Git repository.
				if ( $product['has_git_folder'] && isset( $links['delete'] ) ) {
					$deletion_link = preg_match( '/href="([^"]*)"/', $links['delete'], $matches ) ? $matches[1] : '';

					if ( $deletion_link ) {
						$links['delete'] = sprintf(
							'<a href="%s" title="%s" data-gk-product-confirmation="%s">%s</a>',
							$deletion_link,
							strtr(
								esc_html_x( '[product] is installed from a Git repository. Click this link to confirm deletion.', 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
								[ '[product]' => $product['name'], ]
							),
							strtr(
								esc_html_x( '[product] is installed from a Git repository. Are you sure you want to delete it?', 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
								[ '[product]' => $product['name'], ]
							),
							esc_html__( 'Delete…', 'gk-gravityedit' )
						);
					}
				}
			}

			// Modify Deactivate link for products that are required by other products to be active.
			if ( $product['active'] && ! empty( $product['required_by'] ) && isset( $links['deactivate'] ) ) {
				$deactivation_link = ( preg_match( '/href="([^"]*)"/', $links['deactivate'], $matches ) ? $matches[1] : '' );

				if ( $deactivation_link ) {
					$required_by = implode( ', ', array_map( function ( $required_by ) {
						return $required_by['name'];
					}, $product['required_by'] ) );

					$links['deactivate'] = sprintf(
						'<a href="%s" title="%s" data-gk-product-confirmation="%s">%s</a>',
						$deactivation_link,
						strtr(
							esc_html_x( '[product] is required by other products to be active. Click this link to see which ones and to confirm deactivation.', 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
							[ '[product]' => $product['name'], ]
						),
						strtr(
							esc_html_x( '[product] is required by [products] to be active. Are you sure you want to deactivate it?', 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
							[
								'[product]'  => $product['name'],
								'[products]' => $required_by,
							]
						),
						esc_html__( 'Deactivate…', 'gk-gravityedit' )
					);
				}
			}

			if ( $product['settings'] ) {
				$gk_links = [
					'settings' => sprintf(
						'<a href="%s">%s</a>',
						$product['settings'],
						esc_html__( 'Settings', 'gk-gravityedit' )
					)
				];
			}

			$gk_links['support'] = sprintf(
				'<a href="%s">%s</a>',
				'https://docs.gravitykit.com',
				esc_html__( 'Support', 'gk-gravityedit' )
			);
		}

		$foundation_info = Core::get_instance()->get_foundation_information();

		if ( ( $product && count( $products ) > 1 ) || ( count( $products ) && $plugin_data['TextDomain'] === $foundation_info['source_plugin']['TextDomain'] ) ) {
			$gk_links['enable_grouping'] = sprintf(
				'<a href="%s" title="%s">%s</a>',
				esc_url_raw( add_query_arg( [ 'gk_enable_grouping' => 1 ], admin_url( 'plugins.php' ) ) ),
				esc_html__( 'Aggregate all GravityKit products into a single entry on the Plugins page for a cleaner view and easier management.', 'gk-gravityedit' ),
				esc_html__( 'Group', 'gk-gravityedit' )
			);
		}

		$merged_links = array_merge( $links, $gk_links );

		if ( ! $product ) {
			return $merged_links;
		}

		/**
		 * @filter `gk/foundation/products/{$product_slug}/action-links` Sets product action links in the Plugins page.
		 *
		 * @since  1.0.3
		 *
		 * @param array $merged_links Combined GravityKit and original action links.
		 * @param array $gk_links     GravityKit-added action links.
		 * @param array $link         Original action links.
		 */
		return apply_filters( "gk/foundation/products/{$product['slug']}/action-links", $merged_links, $gk_links, $links );
	}

	/**
	 * Groups all GravityKit products under a single entry on the Plugins page if the "Group GravityKit products" setting is enabled.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function group_products( $wp_plugins ) {
		if ( ! $this->should_group_products() ) {
			return $wp_plugins;
		}

		static $products;

		if ( ! $products ) {
			$products = ProductManager::get_instance()->get_products_data( [ 'key_by' => 'path' ] );

			$products = array_filter( $products, function ( $product ) {
				return $product['installed'] && ! $product['third_party'];
			} );
		}

		if ( empty( $products ) ) {
			return $wp_plugins;
		}

		$foundation_info = Core::get_instance()->get_foundation_information();

		if ( count( $products ) ) {
			foreach ( $wp_plugins as $path => &$wp_plugin ) {
				// If more than one GravityKit product is installed, group them under a single entry using the product that loaded Foundation.
				// Foundation can be loaded by products that are not necessarily on the list of products returned by EDD, such as the standalone Foundation plugin.
				if ( $wp_plugin['TextDomain'] === $foundation_info['source_plugin']['TextDomain'] ) {
					uasort( $products, function ( $first, $second ) {
						return $first['name'] <=> $second['name'];
					} );

					$grouped_products = array_map( function ( $product ) {
						return sprintf(
							'<a href="%s">%s</a>',
							Framework::get_instance()->get_link_to_product_search( $product['id'] ),
							$product['name']
						);
					}, $products );

					$wp_plugin = array_merge( $wp_plugin, [
						'Name'            => __( 'GravityKit', 'gk-gravityedit' ),
						'Version'         => $foundation_info['version'],
						'TextDomain'      => $foundation_info['source_plugin']['TextDomain'],
						'Description'     => strtr(
							esc_html(
								_nx(
									'1 installed GravityKit product: [products].',
									'A suite of [number] installed GravityKit products: [products].',
									count( $grouped_products ),
									'Placeholders inside [] are not to be translated.',
									'gk-gravityedit'
								)
							),
							[
								'[number]'   => count( $grouped_products ),
								'[products]' => implode( ', ', $grouped_products )
							]
						),
						'GravityKitGroup' => true,
					] );

					continue;
				}

				if ( ! isset( $products[ $path ] ) ) {
					continue;
				}

				// Remove the product from the list of plugins.
				unset( $wp_plugins[ $path ] );
			}
		}

		add_filter( 'plugin_row_meta', function ( $wp_plugin_meta, $wp_plugin_file, $wp_plugin_data ) {
			if ( ! isset( $wp_plugin_data['GravityKitGroup'] ) ) {
				return $wp_plugin_meta;
			}

			return [
				'<a href="https://www.gravitykit.com">' . esc_html__( 'Visit GravityKit.com', 'gk-gravityedit' ) . '</a>'
			];
		}, 10, 4 );

		return $wp_plugins;
	}

	/**
	 * Enqueues notices for display on the Plugins page if any of the installed products have newer versions available.
	 * These notices are only displayed if the "Group GravityKit products" setting is enabled, if there are unmet dependencies, or if products are unlicensed.
	 * In all other cases, WordPress automatically displays an update notice for each product.
	 *
	 * @since 1.2.0
	 *
	 * @see   PluginsPage::configure_hooks() for the logic that's used to remove default WP notices.
	 *
	 * @param string $plugin_path
	 * @param array  $plugin_data
	 *
	 * @return void
	 */
	public function enqueue_update_notices( $plugin_path, $plugin_data ) {
		static $products;

		if ( ! $products ) {
			$products = ProductManager::get_instance()->get_products_data();

			$products = array_filter( $products, function ( $product ) {
				return $product['installed'] && ! $product['third_party'];
			} );
		}

		if ( empty( $products ) ) {
			return;
		}

		$notice = null;

		if ( $this->should_group_products() ) {
			$foundation_info = Core::get_instance()->get_foundation_information();

			if ( $plugin_data['TextDomain'] !== $foundation_info['source_plugin']['TextDomain'] ) {
				return;
			}

			$has_updates = array_filter( $products, function ( $product ) {
				return $product['update_available'];
			} );

			if ( empty( $has_updates ) ) {
				return;
			}

			$notice = strtr( esc_html(
				_nx(
					'[products_with_updates] product has a newer version available. Please visit the [link]Manage Your Kit[/link] page to update it.',
					'[products_with_updates] products have newer versions available. Please visit the [link]Manage Your Kit[/link] page to update them.',
					count( $has_updates ),
					'Placeholders inside [] are not to be translated.',
					'gk-gravityedit'
				) ),
				[
					'[products_with_updates]' => count( $has_updates ),
					'[link]'                  => '<a href="' . esc_url_raw( add_query_arg( [ 'page' => Framework::ID, 'filter' => 'update-available' ], admin_url( 'admin.php' ) ) ) . '">',
					'[/link]'                 => '</a>'
				]
			);
		} else {
			$product = Arr::first( $products, function ( $product ) use ( $plugin_data ) {
				return $product['text_domain'] === $plugin_data['TextDomain'];
			} );

			if ( ! $product || ! $product['update_available'] || $product['free'] ) {
				return;
			}

			if ( empty( $product['licenses'] ) ) {
				$notice = strtr(
					esc_html__( 'There is a new version [version] of [product] available.', 'gk-gravityedit' ),
					[
						'[product]' => $product['name'],
						'[version]' => $product['server_version'],
					]
				);
			} else if ( ! $product['checked_dependencies'][ $product['installed_version'] ]['status'] ?? false ) {
				$notice = strtr(
					esc_html_x( 'There is a new version [version] of [product] available. [link]Update now…[/link].', 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
					[
						'[product]' => $product['name'],
						'[version]' => $product['server_version'],
						'[link]'    => sprintf(
							'<a href="%s" title="%s">',
							esc_url_raw( add_query_arg( [ 'action' => 'update' ], Framework::get_instance()->get_link_to_product_search( $product['id'] ) ) ),
							esc_attr__( 'This product has unmet dependencies. Click this link to see see what they are.', 'gk-gravityedit' )
						),
						'[/link]'   => '</a>'
					]
				);
			}
		}

		if ( ! $notice ) {
			return;
		}

		add_filter( 'gk/foundation/products/plugins-page-notices', function ( $notices ) use ( $plugin_path, $notice ) {
			if ( ! isset( $notices[ $plugin_path ] ) ) {
				$notices[ $plugin_path ] = [];
			}

			$notices[ $plugin_path ][] = [
				'type'   => 'warning',
				'notice' => $notice,
			];

			return $notices;
		} );
	}

	/**
	 * Enqueues a notice for display on the Plugins page if the product (or grouped products) is unlicensed.
	 *
	 * @since 1.2.0
	 *
	 * @param string $plugin_path
	 * @param array  $plugin_data
	 *
	 * @return void
	 */
	public function enqueue_unlicensed_notices( $plugin_path, $plugin_data ) {
		static $products;

		if ( ! $products ) {
			$products = ProductManager::get_instance()->get_products_data();

			$products = array_filter( $products, function ( $product ) {
				return $product['installed'] && ! $product['third_party'] && ! $product['free'];
			} );
		}

		if ( empty( $products ) ) {
			return;
		}

		$licenses_data = LicenseManager::get_instance()->get_licenses_data();

		$unlicensed_products = array_filter( $products, function ( $product ) use ( $licenses_data ) {
			return empty( array_intersect( array_keys( $licenses_data ), $product['licenses'] ) );
		} );

		if ( empty( $unlicensed_products ) ) {
			return;
		}

		$notice = null;

		if ( isset( $plugin_data['GravityKitGroup'] ) ) {
			$notice = strtr( esc_html(
				_nx(
					'[unlicensed] product is unlicensed. Please [link]visit the licensing page[/link] to enter a valid license or to purchase a new one.',
					'[unlicensed] products are unlicensed. Please [link]visit the licensing page[/link] to enter a valid license or to purchase a new one.',
					count( $unlicensed_products ),
					'Placeholders inside [] are not to be translated.',
					'gk-gravityedit'
				) ),
				[
					'[unlicensed]' => count( $unlicensed_products ),
					'[link]'       => '<a href="' . esc_url_raw( add_query_arg( [ 'page' => Framework::ID, 'filter' => 'unlicensed' ], admin_url( 'admin.php' ) ) ) . '">',
					'[/link]'      => '</a>'
				]
			);
		} else if ( isset( $unlicensed_products[ $plugin_data['TextDomain'] ] ) ) {
			$notice = strtr(
				esc_html_x( 'This is an unlicensed product. Please [link]visit the licensing page[/link] to enter a valid license or to purchase a new one.', 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
				[
					'[link]'  => '<a href="' . Framework::get_instance()->get_link_to_product_search( $unlicensed_products[ $plugin_data['TextDomain'] ]['id'] ) . '">',
					'[/link]' => '</a>'
				]
			);
		}

		if ( ! $notice ) {
			return;
		}

		add_filter( 'gk/foundation/products/plugins-page-notices', function ( $notices ) use ( $plugin_path, $notice ) {
			if ( ! isset( $notices[ $plugin_path ] ) ) {
				$notices[ $plugin_path ] = [];
			}

			$notices[ $plugin_path ][] = [
				'type'   => 'error',
				'notice' => $notice,
			];

			return $notices;
		} );
	}

	/**
	 * Displays notices for each product on the Plugins page.
	 *
	 * @used-by PluginsPage::enqueue_update_notices()
	 * @used-by PluginsPage::enqueue_unlicensed_notices()
	 *
	 * @param string $plugin_path
	 *
	 * @return void
	 */
	public function display_notices( $plugin_path ) {
		$notices = apply_filters( 'gk/foundation/products/plugins-page-notices', [] );

		if ( ! isset( $notices[ $plugin_path ] ) ) {
			return;
		}

		$screen  = get_current_screen();
		$columns = get_column_headers( $screen );
		$colspan = ! is_countable( $columns ) ? 3 : count( $columns );

		$active = ProductManager::get_instance()->is_product_active_in_current_context( $plugin_path ) ? 'active' : '';

		$notices = array_map( function ( $data ) {
			return [
				'notice' => <<<HTML
<div class="update-message notice inline notice-{$data['type']} notice-alt">
	<p>{$data['notice']}</p>
</div>
HTML
			];
		}, $notices[ $plugin_path ] );

		$notices = join( '', Arr::pluck( $notices, 'notice' ) );

		$notices = <<<HTML
<tr class="plugin-update-tr {$active} gk-custom-plugin-update-message" data-plugin="{$plugin_path}">
	<td colspan="{$colspan}" class="plugin-update colspanchange">
		{$notices}
	</td>
</tr>
<style>tr[data-plugin="{$plugin_path}"]:not(.gk-custom-plugin-update-message) td, tr[data-plugin="{$plugin_path}"]:not(.gk-custom-plugin-update-message) th { box-shadow: none !important; }</style>
HTML;

		// Display notices after WP's default notice (typically, the update notice).
		// This prevents a visible separation between notices and makes them appear as part of the same plugin row.
		add_action( "after_plugin_row_{$plugin_path}", function () use ( $notices ) {
			echo $notices;
		}, 11 );
	}

	/**
	 * Determines whether products are grouped on the Plugins page.
	 *
	 * @since 1.2.0
	 *
	 * @return bool
	 */
	public function should_group_products() {
		static $should_group = null;

		if ( is_null( $should_group ) ) {
			$should_group = SettingsFramework::get_instance()->get_plugin_setting( Core::ID, 'group_gk_products' );
		};

		return $should_group;
	}

	/**
	 * Determines whether the current page is a Plugins page.
	 *
	 * @since 1.2.0
	 *
	 * @return bool
	 */
	public function is_plugins_page() {
		global $pagenow;

		return is_admin() && 'plugins.php' === $pagenow;
	}
}
