<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Licenses\WP;

use Exception;
use GravityKit\GravityEdit\Foundation\Core;
use GravityKit\GravityEdit\Foundation\Helpers\Arr;
use GravityKit\GravityEdit\Foundation\Licenses\Framework;
use GravityKit\GravityEdit\Foundation\Settings\Framework as SettingsFramework;
use GravityKit\GravityEdit\Foundation\Licenses\ProductManager;
use GravityKit\GravityEdit\Foundation\Licenses\LicenseManager;
use GravityKit\GravityEdit\Foundation\Logger\Framework as LoggerFramework;
use GravityKit\GravityEdit\Foundation\WP\AdminMenu;

/**
 * Manages the display of GK products on the Plugins page.
 *
 * @since 1.2.0
 */
class PluginsPage {
	/**
	 * Class instance.
	 *
	 * @since 1.2.0
	 *
	 * @var PluginsPage|null
	 */
	private static $_instance = null;

	/**
	 * Number of GravityKit-managed products updated by the legacy bulk upgrader.
	 *
	 * @since 1.21.0
	 *
	 * @var int
	 */
	private $bulk_upgraded_gk_product_count = 0;

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

		add_action( 'init', [ $this, 'configure_hooks' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_menu_badge_sync_script' ] );

		add_action( 'upgrader_process_complete', [ $this, 'track_bulk_upgraded_gk_products' ], 10, 2 );

		add_action( 'admin_footer', [ $this, 'sync_admin_menu_badge_after_bulk_upgrade' ] );

		$initialized = true;
	}

	/**
	 * Adds various hooks on 'init'.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function configure_hooks() {
		if ( ! $this->is_plugins_page() ) {
			return;
		}

		add_filter( 'all_plugins', [ $this, 'group_products' ] );

		add_action( 'after_plugin_row', [ $this, 'enqueue_update_notices' ], 10, 2 );

		add_action( 'after_plugin_row', [ $this, 'enqueue_unlicensed_notices' ], 10, 2 );

		add_action( 'after_plugin_row', [ $this, 'display_notices' ], 11 );

		add_filter( 'plugin_action_links', [ $this, 'modify_product_action_links' ], 10, 3 );

		// Disable/enable the "Group GravityKit products" setting.
		if ( isset( $_REQUEST['gk_disable_grouping'] ) || isset( $_REQUEST['gk_enable_grouping'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			SettingsFramework::get_instance()->save_plugin_setting( Core::ID, 'group_gk_products', isset( $_REQUEST['gk_enable_grouping'] ) ); // phpcs:ignore WordPress.Security.NonceVerification

			wp_safe_redirect( remove_query_arg( isset( $_REQUEST['gk_enable_grouping'] ) ? 'gk_enable_grouping' : 'gk_disable_grouping' ) ); // phpcs:ignore WordPress.Security.NonceVerification

			exit();
		}

		// Add action to links that require confirmation.
		add_filter(
			'gk/foundation/inline-scripts',
			function ( $scripts ) {
				$scripts[]['script'] = <<<JS
document.addEventListener( 'DOMContentLoaded', function () {
	document.querySelectorAll( 'a[data-gk-product-confirmation]').forEach( link => {
		link.addEventListener( 'click', ( e ) => !confirm( link.dataset.gkProductConfirmation ) && e.preventDefault() );
	} );
} );
JS;

				return $scripts;
			}
		);

		// Prevent WordPress from displaying an update notice for each unlicensed product or that with unmet dependencies.
		// Instead, we display our own notice (@see PluginsPage::enqueue_update_notices()).
		// 1. Save the current update data count and return it when 'wp_get_update_data' fires, which happens after 'site_transient_update_plugins' filter that we use in the second step to remove plugins.
		if ( function_exists( 'wp_get_update_data' ) ) {
			$update_data_backup = wp_get_update_data();

			add_filter(
				'wp_get_update_data',
				function () use ( $update_data_backup ) {
					return $update_data_backup;
				},
				10
			);
		}

		// 2. Remove plugins from the list of those that have updates available.
		add_filter(
			'site_transient_update_plugins',
			function ( $data ) {
				if ( ! isset( $data->response ) ) {
					return $data;
				}

				$products = ProductManager::get_instance()->get_products_data();

				foreach ( $data->response as $plugin_path => $plugin ) {
					if ( ! isset( $plugin->gk_product_text_domain ) || ! isset( $products[ $plugin->gk_product_text_domain ] ) ) {
						continue;
					}

					$product = $products[ $plugin->gk_product_text_domain ];

					// Resolve the version being offered (from the WP update transient, which EDD.php populated).
					$offered_version = $plugin->new_version ?? $product['server_version'];

					if ( empty( $product['checked_dependencies'][ $offered_version ]['status'] ) ) {
						unset( $data->response[ $plugin_path ] );
					}

					if ( ! $product['free'] && empty( $product['licenses'] ) ) {
						unset( $data->response[ $plugin_path ] );
					}
				}

				return $data;
			}
		);
	}

	/**
	 * Tracks GravityKit-managed products updated by WordPress' legacy bulk plugin upgrader.
	 *
	 * @since 1.21.0
	 *
	 * @param mixed $upgrader   Upgrader instance.
	 * @param array $hook_extra Bulk upgrader context.
	 *
	 * @return void
	 */
	public function track_bulk_upgraded_gk_products( $upgrader, $hook_extra ): void {
		unset( $upgrader );

		if ( ! is_array( $hook_extra ) ) {
			return;
		}

		if ( 'plugin' !== Arr::get( $hook_extra, 'type' ) || 'update' !== Arr::get( $hook_extra, 'action' ) ) {
			return;
		}

		$updated_paths = $hook_extra['plugins'] ?? array_filter( [ $hook_extra['plugin'] ?? null ] );
		$updated_paths = array_values( array_unique( array_filter( (array) $updated_paths ) ) );

		if ( empty( $updated_paths ) ) {
			return;
		}

		try {
			$managed_paths = ProductManager::get_instance()->get_managed_product_paths();
		} catch ( Exception $e ) {
			LoggerFramework::get_instance()->warning( 'Unable to get products when tracking bulk-upgraded products.' );

			return;
		}

		$this->bulk_upgraded_gk_product_count += count( array_intersect( $updated_paths, $managed_paths ) );
	}

	/**
	 * Syncs the admin menu badge after WordPress' legacy bulk plugin upgrader finishes.
	 *
	 * @since 1.21.0
	 *
	 * @return void
	 */
	public function sync_admin_menu_badge_after_bulk_upgrade(): void {
		global $pagenow;

		$action = Arr::get( wp_unslash( $_GET ), 'action' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$action = is_string( $action ) ? sanitize_key( $action ) : '';

		// WordPress' bulk plugin upgrade runs inside an iframe at update.php?action=update-selected; the
		// GravityKit badge lives in the parent (update-core.php) document, which the emitted script updates
		// via window.parent. The admin menu is not rendered in the iframe, so should_initialize() is not checked.
		if ( 'update.php' !== $pagenow || 'update-selected' !== $action ) {
			return;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		if ( ! Framework::get_instance()->current_user_can( 'install_products' ) ) {
			return;
		}

		if ( $this->bulk_upgraded_gk_product_count < 1 ) {
			return;
		}

		wp_print_inline_script_tag( $this->get_admin_menu_badge_bulk_upgrade_script( $this->bulk_upgraded_gk_product_count ) );
	}

	/**
	 * Enqueues the admin menu badge sync script on plugin update screens.
	 *
	 * @since 1.21.0
	 *
	 * @param string $page Current page.
	 *
	 * @return void
	 */
	public function enqueue_admin_menu_badge_sync_script( $page ): void {
		if ( ! in_array( $page, [ 'plugins.php', 'update-core.php' ], true ) ) {
			return;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		if ( ! AdminMenu::should_initialize() ) {
			return;
		}

		if ( ! Framework::get_instance()->current_user_can( 'install_products' ) ) {
			return;
		}

		try {
			$product_paths = ProductManager::get_instance()->get_product_paths_with_available_update();
		} catch ( Exception $e ) {
			LoggerFramework::get_instance()->warning( 'Unable to get products when enqueueing the admin menu badge sync script.' );

			return;
		}

		if ( empty( $product_paths ) ) {
			return;
		}

		$script_data = [
			'paths'    => array_values( $product_paths ),
			'badgeIds' => $this->get_admin_menu_badge_ids(),
		];

		/**
		 * Filters the admin menu badge sync script data.
		 *
		 * @filter gk/foundation/admin-menu-badge-sync/data
		 *
		 * @since 1.21.0
		 *
		 * @param array $script_data Script data.
		 */
		$script_data = apply_filters( 'gk/foundation/admin-menu-badge-sync/data', $script_data );

		if ( empty( $script_data['paths'] ) || empty( $script_data['badgeIds'] ) ) {
			return;
		}

		$handle = 'gk-foundation-admin-menu-badge-sync';

		wp_register_script( $handle, false, [ 'jquery', 'updates' ], Core::VERSION, true );
		wp_localize_script( $handle, 'gkFoundationAdminMenuBadgeSync', [ 'data' => $script_data ] );
		wp_add_inline_script( $handle, $this->get_admin_menu_badge_sync_script() );
		wp_enqueue_script( $handle );
	}

	/**
	 * Returns GravityKit admin menu badge IDs.
	 *
	 * @since 1.21.0
	 *
	 * @return array
	 */
	private function get_admin_menu_badge_ids(): array {
		return [
			AdminMenu::WP_ADMIN_MENU_SLUG . '-badge',
			Framework::ID . '-badge',
		];
	}

	/**
	 * Returns the admin menu badge sync script.
	 *
	 * @since 1.21.0
	 *
	 * @return string
	 */
	private function get_admin_menu_badge_sync_script(): string {
		$event_handler_script = <<<'JS'
( function ( $, settings ) {
	const data = settings.data || {};

	if ( ! Array.isArray( data.paths ) || ! Array.isArray( data.badgeIds ) ) {
		return;
	}

	const paths = new Set( data.paths );
	const badgeIds = data.badgeIds;

	$( document ).on( 'wp-plugin-update-success wp-plugin-delete-success', function ( event, response ) {
		if ( ! response || ! response.plugin || ! paths.has( response.plugin ) ) {
			return;
		}

		settings.decrementBadges( badgeIds, 1 );
		paths.delete( response.plugin );
	} );
} )( jQuery, window.gkFoundationAdminMenuBadgeSync || {} );
JS;

		return $this->get_admin_menu_badge_sync_helper_script() . "\n" . $event_handler_script;
	}

	/**
	 * Returns the shared admin menu badge decrement helper script.
	 *
	 * @since 1.21.0
	 *
	 * @return string
	 */
	private function get_admin_menu_badge_sync_helper_script(): string {
		return <<<'JS'
( function ( $, window ) {
	const settings = window.gkFoundationAdminMenuBadgeSync || {};

	window.gkFoundationAdminMenuBadgeSync = settings;

	settings.decrementBadges = function ( badgeIds, decrementBy ) {
		const decrementAmount = parseInt( decrementBy, 10 );

		if ( ! Array.isArray( badgeIds ) || isNaN( decrementAmount ) || decrementAmount < 1 ) {
			return;
		}

		badgeIds.forEach( function ( badgeId ) {
			// WordPress duplicates the badge ID in .wp-menu-name and aria-hidden .wp-submenu-head.
			$( '[id="' + badgeId + '"]' ).each( function () {
				const badge = $( this );
				const countElement = badge.find( '.plugin-count' );
				const currentCount = parseInt( countElement.text().replace( /[^0-9]/g, '' ), 10 );

				if ( isNaN( currentCount ) ) {
					return;
				}

				const newCount = Math.max( currentCount - decrementAmount, 0 );

				if ( newCount <= 0 ) {
					badge.remove();

					return;
				}

				const className = badge.attr( 'class' ) || '';

				countElement.text( String( newCount ) );
				badge.attr( 'class', className.replace( /count-[0-9]+/g, 'count-' + newCount ) );
			} );
		} );
	};
} )( jQuery, window );
JS;
	}

	/**
	 * Returns the legacy bulk upgrade footer sync script.
	 *
	 * @since 1.21.0
	 *
	 * @param int $decrement_count Decrement count.
	 *
	 * @return string
	 */
	private function get_admin_menu_badge_bulk_upgrade_script( int $decrement_count ): string {
		$badge_ids_json = wp_json_encode( $this->get_admin_menu_badge_ids() ) ?: '[]';

		// Runs inside the upgrade iframe; the badge lives in the parent document, so update window.parent.
		return <<<JS
( function () {
	const targetWindow = window.parent && window.parent !== window ? window.parent : null;

	if ( ! targetWindow || ! targetWindow.document ) {
		return;
	}

	const doc = targetWindow.document;
	const badgeIds = {$badge_ids_json};
	const decrementBy = {$decrement_count};

	badgeIds.forEach( function ( badgeId ) {
		// WordPress duplicates the badge ID in .wp-menu-name and aria-hidden .wp-submenu-head.
		doc.querySelectorAll( '[id="' + badgeId + '"]' ).forEach( function ( badge ) {
			const countElement = badge.querySelector( '.plugin-count' );

			if ( ! countElement ) {
				return;
			}

			const currentCount = parseInt( ( countElement.textContent || '' ).replace( /[^0-9]/g, '' ), 10 );

			if ( isNaN( currentCount ) ) {
				return;
			}

			const newCount = Math.max( currentCount - decrementBy, 0 );

			if ( newCount <= 0 ) {
				if ( badge.parentNode ) {
					badge.parentNode.removeChild( badge );
				}

				return;
			}

			countElement.textContent = String( newCount );
			badge.className = badge.className.replace( /count-[0-9]+/g, 'count-' + newCount );
		} );
	} );
} )();
JS;
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

		$modify_links_with_admin_menu_functionality = AdminMenu::should_initialize();

		if ( ! $products ) {
			$products = ProductManager::get_instance()->get_products_data();

			$products = array_filter(
				$products,
				function ( $product ) {
					return ! $product['third_party'];
				}
			);
		}

		if ( empty( $products ) ) {
			return $links;
		}

		// If this is a grouped entry for GravityKit products, display custom links and return early.
		if ( $modify_links_with_admin_menu_functionality && isset( $plugin_data['GravityKitGroup'] ) ) {
			return [
				'manage'           => sprintf(
					'<a href="%s">%s</a>',
					esc_url_raw( add_query_arg( [ 'page' => Framework::ID ], admin_url( 'admin.php' ) ) ),
					esc_html__( 'Manage Your Kit', 'gk-foundation' )
				),
				'settings'         => sprintf(
					'<a href="%s">%s</a>',
					esc_url_raw( add_query_arg( [ 'page' => SettingsFramework::ID ], admin_url( 'admin.php' ) ) ),
					esc_html__( 'Settings', 'gk-foundation' )
				),
				'disable_grouping' => sprintf(
					'<a href="%s" title="%s">%s</a>',
					esc_url_raw( add_query_arg( [ 'gk_disable_grouping' => 1 ], admin_url( 'plugins.php' ) ) ),
					esc_attr__( 'Disable the grouping of GravityKit products', 'gk-foundation' ),
					esc_html__( 'Ungroup', 'gk-foundation' )
				),
			];
		}

		$product = $plugin_data ? ( $products[ $plugin_data['TextDomain'] ] ?? null ) : null;

		$gk_links = [];

		if ( $product ) {
			if ( ! $product['active'] ) {
				/* phpcs:ignore Squiz.PHP.CommentedOutCode.Found
				    // In the future, we might want to check if the product is licensed and if the license is valid. This was removed in 5a5c8cc.
					if ( ! $product['free'] && empty( $product['licenses'] ) ) {
						$links['activate'] = sprintf(
							'<a href="%s" title="%s">%s</a>',
							esc_url_raw( Framework::get_instance()->get_link_to_product_search( $product['id'] ) ),
							esc_html__( 'This product requires a license key to be activated. Click this link to enter your license key.', 'gk-foundation' ),
							esc_html__( 'Activate…', 'gk-foundation' )
						);
					}
				*/
				// Modify Activate link for products that have unmet dependencies.
				if ( $modify_links_with_admin_menu_functionality && ! $product['checked_dependencies'][ $product['installed_version'] ]['status'] ) {
					$links['activate'] = sprintf(
						'<a href="%s" title="%s">%s</a>',
						esc_url_raw( add_query_arg( [ 'action' => 'activate' ], Framework::get_instance()->get_link_to_product_search( $product['id'] ) ) ),
						esc_html__( 'This product has unmet dependencies. Click this link to see see what they are.', 'gk-foundation' ),
						esc_html__( 'Activate…', 'gk-foundation' )
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
								esc_html_x( '[product] is installed from a Git repository. Click this link to confirm deletion.', 'Placeholders inside [] are not to be translated.', 'gk-foundation' ),
								[ '[product]' => $product['name'] ]
							),
							strtr(
								esc_html_x( '[product] is installed from a Git repository. Are you sure you want to delete it?', 'Placeholders inside [] are not to be translated.', 'gk-foundation' ),
								[ '[product]' => $product['name'] ]
							),
							esc_html__( 'Delete…', 'gk-foundation' )
						);
					}
				}
			}

			// Modify Deactivate link for products that are required by other products to be active.
			if ( $modify_links_with_admin_menu_functionality && $product['active'] && ! empty( $product['required_by'] ) && isset( $links['deactivate'] ) ) {
				$deactivation_link = ( preg_match( '/href="([^"]*)"/', $links['deactivate'], $matches ) ? $matches[1] : '' );

				if ( $deactivation_link ) {
					$required_by = implode(
						', ',
						array_map(
							function ( $required_by ) {
								return $required_by['name'];
							},
							$product['required_by']
						)
					);

					$links['deactivate'] = sprintf(
						'<a href="%s" title="%s" data-gk-product-confirmation="%s">%s</a>',
						$deactivation_link,
						strtr(
							esc_html_x( '[product] is required by other products to be active. Click this link to see which ones and to confirm deactivation.', 'Placeholders inside [] are not to be translated.', 'gk-foundation' ),
							[ '[product]' => $product['name'] ]
						),
						strtr(
							esc_html_x( '[product] is required by [products] to be active. Are you sure you want to deactivate it?', 'Placeholders inside [] are not to be translated.', 'gk-foundation' ),
							[
								'[product]'  => $product['name'],
								'[products]' => $required_by,
							]
						),
						esc_html__( 'Deactivate…', 'gk-foundation' )
					);
				}
			}

			if ( $product['settings'] ) {
				$gk_links = [
					'settings' => sprintf(
						'<a href="%s">%s</a>',
						$product['settings'],
						esc_html__( 'Settings', 'gk-foundation' )
					),
				];
			}

			$gk_links['support'] = sprintf(
				'<a href="%s">%s</a>',
				'https://docs.gravitykit.com',
				esc_html__( 'Support', 'gk-foundation' )
			);
		}

		if ( $modify_links_with_admin_menu_functionality ) {
			$foundation_info = Core::get_instance()->get_foundation_information();

			if ( ( $product && count( $products ) > 1 ) || ( count( $products ) && $plugin_data && $plugin_data['TextDomain'] === $foundation_info['source_plugin']['TextDomain'] ) ) {
				$gk_links['enable_grouping'] = sprintf(
					'<a href="%s" title="%s">%s</a>',
					esc_url_raw( add_query_arg( [ 'gk_enable_grouping' => 1 ], admin_url( 'plugins.php' ) ) ),
					esc_html__( 'Aggregate all GravityKit products into a single entry on the Plugins page for a cleaner view and easier management.', 'gk-foundation' ),
					esc_html__( 'Group', 'gk-foundation' )
				);
			}
		}

		$merged_links = array_merge( $links, $gk_links );

		if ( ! $product ) {
			return $merged_links;
		}

		/**
		 * Sets product action links in the Plugins page.
		 *
		 * @filter `gk/foundation/products/{$product_slug}/action-links`
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
	 * @param array $wp_plugins List of plugins.
	 *
	 * @return array
	 */
	public function group_products( $wp_plugins ) {
		if ( ! $this->should_group_products() ) {
			return $wp_plugins;
		}

		static $products;

		if ( ! $products ) {
			$products = ProductManager::get_instance()->get_products_data( [ 'key_by' => 'path' ] );

			$products = array_filter(
				$products,
				function ( $product ) {
					return $product['installed'] && ! $product['third_party'];
				}
			);
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
					uasort(
						$products,
						function ( $first, $second ) {
							return $first['name'] <=> $second['name'];
						}
					);

					$grouped_products = array_map(
						function ( $product ) {
							return sprintf(
								'<a href="%s">%s</a>',
								Framework::get_instance()->get_link_to_product_search( $product['id'] ),
								$product['name']
							);
						},
						$products
					);

					$wp_plugin = array_merge(
						$wp_plugin,
						[
							'Name'            => __( 'GravityKit', 'gk-foundation' ),
							'Version'         => $foundation_info['version'],
							'TextDomain'      => $foundation_info['source_plugin']['TextDomain'],
							'Description'     => strtr(
								esc_html(
									_nx(
										'1 installed GravityKit product: [products].',
										'A suite of [number] installed GravityKit products: [products].',
										count( $grouped_products ),
										'Placeholders inside [] are not to be translated.',
										'gk-foundation'
									)
								),
								[
									'[number]'   => count( $grouped_products ),
									'[products]' => implode( ', ', $grouped_products ),
								]
							),
							'GravityKitGroup' => true,
						]
					);

					continue;
				}

				if ( ! isset( $products[ $path ] ) ) {
					continue;
				}

				// Remove the product from the list of plugins.
				unset( $wp_plugins[ $path ] );
			}
		}

		add_filter(
			'plugin_row_meta',
			function ( $wp_plugin_meta, $wp_plugin_file, $wp_plugin_data ) {
				if ( ! isset( $wp_plugin_data['GravityKitGroup'] ) ) {
					return $wp_plugin_meta;
				}

				return [
					'<a href="https://www.gravitykit.com">' . esc_html__( 'Visit GravityKit.com', 'gk-foundation' ) . '</a>',
				];
			},
			10,
			3
		);

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
	 * @param string $plugin_path Plugin path.
	 * @param array  $plugin_data Plugin data.
	 *
	 * @return void
	 */
	public function enqueue_update_notices( $plugin_path, $plugin_data ) {
		static $products;

		if ( ! $products ) {
			$products = ProductManager::get_instance()->get_products_data();

			$products = array_filter(
				$products,
				function ( $product ) {
					return $product['installed'] && ! $product['third_party'];
				}
			);
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

			$update_transient = get_site_transient( 'update_plugins' );

			$has_updates = array_filter(
				$products,
				function ( $product ) use ( $update_transient ) {
					return ! empty( $product['path'] ) && isset( $update_transient->response[ $product['path'] ] );
				}
			);

			if ( empty( $has_updates ) ) {
				return;
			}

			$notice = strtr(
				esc_html(
					_nx(
						'[products_with_updates] product has a newer version available. Please visit the [link]Manage Your Kit[/link] page to update it.',
						'[products_with_updates] products have newer versions available. Please visit the [link]Manage Your Kit[/link] page to update them.',
						count( $has_updates ),
						'Placeholders inside [] are not to be translated.',
						'gk-foundation'
					)
				),
				[
					'[products_with_updates]' => count( $has_updates ),
					'[link]'                  => '<a href="' . esc_url_raw(
							add_query_arg(
								[
									'page'   => Framework::ID,
									'filter' => 'update-available',
								],
								admin_url( 'admin.php' )
							)
						) . '">',
					'[/link]'                 => '</a>',
				]
			);
		} else {
			$product = Arr::first(
				$products,
				function ( $product ) use ( $plugin_data ) {
					return in_array( $plugin_data['TextDomain'], $product['text_domains'], true );
				}
			);

			$update_transient = get_site_transient( 'update_plugins' );
			$has_wp_update    = $product && ! empty( $product['path'] ) && isset( $update_transient->response[ $product['path'] ] );

			if ( ! $product || ! $has_wp_update || $product['free'] ) {
				return;
			}

			$offered_version = $update_transient->response[ $product['path'] ]->new_version ?? $product['server_version'];

			if ( ! $product['checked_dependencies'][ $product['installed_version'] ]['status'] ) {
				$notice = strtr(
					esc_html_x( 'There is a new version [version] of [product] available. [link]Update now…[/link].', 'Placeholders inside [] are not to be translated.', 'gk-foundation' ),
					[
						'[product]' => $product['name'],
						'[version]' => $offered_version,
						'[link]'    => sprintf(
							'<a href="%s" title="%s">',
							esc_url_raw( add_query_arg( [ 'action' => 'update' ], Framework::get_instance()->get_link_to_product_search( $product['id'] ) ) ),
							esc_attr__( 'This product has unmet dependencies. Click this link to see see what they are.', 'gk-foundation' )
						),
						'[/link]'   => '</a>',
					]
				);
			} elseif ( is_multisite() && ! is_network_admin() ) {
				// WP core's wp_plugin_update_row() gates on is_network_admin() || !is_multisite(),
				// so it never renders on subsites. Show the standard update notice ourselves.
				$details_url = self_admin_url(
					'plugin-install.php?tab=plugin-information&plugin='
					. ( $update_transient->response[ $product['path'] ]->slug ?? '' )
					. '&section=changelog&TB_iframe=true&width=600&height=800'
				);

				$update_url = wp_nonce_url(
					self_admin_url( 'update.php?action=upgrade-plugin&plugin=' . rawurlencode( $product['path'] ) ),
					'upgrade-plugin_' . $product['path']
				);

				$notice = strtr(
					esc_html_x(
						'There is a new version of [product] available. [details_link]View version [version] details[/details_link] or [update_link]update now[/update_link].',
						'Placeholders inside [] are not to be translated.',
						'gk-foundation'
					),
					[
						'[product]'       => $product['name'],
						'[version]'       => $offered_version,
						'[details_link]'  => sprintf(
							'<a href="%s" class="thickbox open-plugin-details-modal" aria-label="%s">',
							esc_url( $details_url ),
							esc_attr( sprintf( __( 'View %1$s version %2$s details' ), $product['name'], $offered_version ) )
						),
						'[/details_link]' => '</a>',
						'[update_link]'   => sprintf(
							'<a href="%s" class="update-link" aria-label="%s">',
							esc_url( $update_url ),
							esc_attr( sprintf( __( 'Update %s now' ), $product['name'] ) )
						),
						'[/update_link]'  => '</a>',
					]
				);
			}
		}

		if ( ! $notice ) {
			return;
		}

		add_filter(
			'gk/foundation/products/plugins-page-notices',
			function ( $notices ) use ( $plugin_path, $notice ) {
				if ( ! isset( $notices[ $plugin_path ] ) ) {
					$notices[ $plugin_path ] = [];
				}

				$notices[ $plugin_path ][] = [
					'type'   => 'warning',
					'notice' => $notice,
				];

				return $notices;
			}
		);
	}

	/**
	 * Enqueues a notice for display on the Plugins page if the product (or grouped products) is unlicensed.
	 *
	 * @since 1.2.0
	 *
	 * @param string $plugin_path Plugin path.
	 * @param array  $plugin_data Plugin data.
	 *
	 * @return void
	 */
	public function enqueue_unlicensed_notices( $plugin_path, $plugin_data ) {
		static $products;

		if ( ! $products ) {
			$products = ProductManager::get_instance()->get_products_data();

			$products = array_filter(
				$products,
				function ( $product ) {
					return $product['installed'] && ! $product['third_party'] && ! $product['free'];
				}
			);
		}

		if ( empty( $products ) ) {
			return;
		}

		$licenses_data = LicenseManager::get_instance()->get_licenses_data();

		$unlicensed_products = array_filter(
			$products,
			function ( $product ) use ( $licenses_data ) {
				return empty( array_intersect( array_keys( $licenses_data ), $product['licenses'] ) );
			}
		);

		if ( empty( $unlicensed_products ) ) {
			return;
		}

		$notice = null;

		if ( isset( $plugin_data['GravityKitGroup'] ) ) {
			$notice = strtr(
				esc_html(
					_nx(
						'[unlicensed] product is unlicensed. Please [link]visit the licensing page[/link] to enter a valid license or to purchase a new one.',
						'[unlicensed] products are unlicensed. Please [link]visit the licensing page[/link] to enter a valid license or to purchase a new one.',
						count( $unlicensed_products ),
						'Placeholders inside [] are not to be translated.',
						'gk-foundation'
					)
				),
				[
					'[unlicensed]' => count( $unlicensed_products ),
					'[link]'       => '<a href="' . esc_url_raw(
							add_query_arg(
								[
									'page'   => Framework::ID,
									'filter' => 'unlicensed',
								],
								admin_url( 'admin.php' )
							)
						) . '">',
					'[/link]'      => '</a>',
				]
			);
		} elseif ( isset( $unlicensed_products[ $plugin_data['TextDomain'] ] ) ) {
			$notice = strtr(
				esc_html_x( 'This is an unlicensed product. Please [link]visit the licensing page[/link] to enter a valid license or to purchase a new one.', 'Placeholders inside [] are not to be translated.', 'gk-foundation' ),
				[
					'[link]'  => '<a href="' . Framework::get_instance()->get_link_to_product_search( $unlicensed_products[ $plugin_data['TextDomain'] ]['id'] ) . '">',
					'[/link]' => '</a>',
				]
			);
		}

		if ( ! $notice ) {
			return;
		}

		add_filter(
			'gk/foundation/products/plugins-page-notices',
			function ( $notices ) use ( $plugin_path, $notice ) {
				if ( ! isset( $notices[ $plugin_path ] ) ) {
					$notices[ $plugin_path ] = [];
				}

				$notices[ $plugin_path ][] = [
					'type'   => 'error',
					'notice' => $notice,
				];

				return $notices;
			}
		);
	}

	/**
	 * Displays notices for each product on the Plugins page.
	 *
	 * @used-by PluginsPage::enqueue_update_notices()
	 * @used-by PluginsPage::enqueue_unlicensed_notices()
	 *
	 * @param string $plugin_path Plugin path.
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

		// phpcs:disable WordPress.Arrays.CommaAfterArrayItem.NoComma
		$notices = array_map(
			function ( $data ) {
				return [
					'notice' => <<<HTML
<div class="update-message notice inline notice-{$data['type']} notice-alt">
	<p>{$data['notice']}</p>
</div>
HTML
				];
			},
			$notices[ $plugin_path ]
		);          // phpcs:enable WordPress.Arrays.CommaAfterArrayItem.NoComma

		$notices = join( '', Arr::pluck( $notices, 'notice' ) );

		// Omit `data-plugin`: these are notice rows, not update offers. WP core's plugin-update click handler targets
		// `[data-plugin] .update-link` and runs wp.updates.updatePlugin() against the matched row, so carrying the
		// attribute lets a plain notice link (e.g. "visit the licensing page") be mistaken for a plugin update.
		$notices = <<<HTML
<tr class="plugin-update-tr {$active} gk-custom-plugin-update-message">
	<td colspan="{$colspan}" class="plugin-update colspanchange">
		{$notices}
	</td>
</tr>
<style>tr[data-plugin="{$plugin_path}"]:not(.gk-custom-plugin-update-message) td, tr[data-plugin="{$plugin_path}"]:not(.gk-custom-plugin-update-message) th { box-shadow: none !important; }</style>
HTML;

		// Display notices after WP's default notice (typically, the update notice).
		// This prevents a visible separation between notices and makes them appear as part of the same plugin row.
		add_action(
			"after_plugin_row_{$plugin_path}",
			function () use ( $notices ) {
				echo $notices; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			},
			11
		);
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

		return $should_group && AdminMenu::should_initialize();
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
