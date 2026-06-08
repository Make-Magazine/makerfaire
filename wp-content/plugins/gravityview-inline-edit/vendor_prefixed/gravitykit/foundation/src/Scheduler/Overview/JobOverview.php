<?php
/**
 * GravityKit Background Jobs overview page controller.
 *
 * @license GPL-2.0-or-later
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Scheduler\Overview;

use GravityKit\GravityEdit\Foundation\Core;
use GravityKit\GravityEdit\Foundation\Helpers\Arr;
use GravityKit\GravityEdit\Foundation\Helpers\Core as CoreHelpers;
use GravityKit\GravityEdit\Foundation\Scheduler\Models\HealthCheck;
use GravityKit\GravityEdit\Foundation\Scheduler\Store\DbStore;
use GravityKit\GravityEdit\Foundation\Translations\Framework as TranslationsFramework;
use GravityKit\GravityEdit\Foundation\WP\AjaxRouter;

/**
 * Job overview class.
 */
class JobOverview {
	const PAGE_ID = 'gk_background_jobs';

	/**
	 * Handle for the compiled Svelte bundle.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	private const ASSETS_HANDLE = 'gk-background-jobs';

	/**
	 * JS file name.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	private const JS_FILE = 'background-jobs.js';

	/**
	 * CSS file name.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	private const CSS_FILE = 'background-jobs.css';

	/**
	 * User option name for the per-page screen option.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	const PER_PAGE_OPTION = 'gk_background_jobs_per_page';

	/**
	 * Default items per page.
	 *
	 * @since 1.12.0
	 *
	 * @var int
	 */
	const PER_PAGE_DEFAULT = 20;

	/**
	 * Class instance.
	 *
	 * @since 1.12.0
	 *
	 * @var JobOverview|null
	 */
	private static $instance;

	/**
	 * Constructor.
	 *
	 * @since 1.12.0
	 */
	public function __construct() {
		if ( ! class_exists( 'ActionScheduler' ) ) {
			return;
		}

		add_filter( 'gk/foundation/admin-menu/submenus', [ $this, 'add_gk_submenu' ], 20 );
		add_action( 'admin_init', [ $this, 'disable_notices' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_svelte_assets' ] );
		add_action( 'current_screen', [ $this, 'register_screen_options' ] );

		// Must register before current_screen — WP processes Screen Options POST on admin_init.
		add_filter( 'set_screen_option_' . self::PER_PAGE_OPTION, [ $this, 'save_per_page_option' ], 10, 3 );

		Diagnostics::register_site_health();

		new JobAjaxController( new JobSerializer() );
	}

	/**
	 * Returns class instance.
	 *
	 * @since 1.12.0
	 *
	 * @return JobOverview
	 */
	public static function get_instance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Adds Background Jobs submenu to the GravityKit menu above "Grant Support Access".
	 *
	 * @since 1.12.0
	 *
	 * @param array $submenus List of submenus.
	 *
	 * @return array
	 */
	public function add_gk_submenu( array $submenus ): array {
		$page_id          = self::PAGE_ID;
		$submenu_position = 'top';

		if ( ! did_action( 'gk/foundation/initialized' ) || Arr::get( $submenus, "{$submenu_position}.{$page_id}" ) ) {
			return $submenus;
		}

		// Only show if the setting is enabled (off by default).
		if ( CoreHelpers::is_network_admin() ) {
			/**
			 * Controls whether the Background Jobs page appears in the network admin menu.
			 *
			 * @since 1.12.0
			 *
			 * @param bool $show Whether to show the menu item. Default: false.
			 */
			$show = apply_filters( 'gk/foundation/scheduler/ui/show-in-network-admin', false );
		} else {
			$show = Core::get_instance()->settings()->get_plugin_setting( Core::ID, 'show_background_jobs', 0 );
		}

		if ( empty( $show ) ) {
			return $submenus;
		}

		$submenus[ $submenu_position ][ $page_id ] = [
			'page_title'         => esc_html__( 'Background Jobs', 'gk-foundation' ),
			'menu_title'         => esc_html__( 'Background Jobs', 'gk-foundation' ),
			'capability'         => CoreHelpers::is_network_admin() ? 'manage_network' : 'manage_options',
			'id'                 => self::PAGE_ID,
			'order'              => 3,
			'callback'           => [ $this, 'render_job_overview' ],
			'hide_admin_notices' => true,
		];

		// Guard against duplicate registration since this filter callback runs on every get_submenus() call.
		static $counter_registered = false;

		if ( ! $counter_registered ) {
			$counter_registered = true;

			add_filter(
				'gk/foundation/admin-menu/submenu/' . self::PAGE_ID . '/counter',
				function ( $count ) {
					$health = HealthCheck::run();

					return $health->has_failure() ? $count + 1 : $count;
				}
			);
		}

		return $submenus;
	}

	/**
	 * Renders the Background Jobs page shell.
	 *
	 * The Svelte app mounts to #gk-background-jobs.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	public function render_job_overview(): void {
		echo '<div class="wrap">';
		echo '<h1 class="wp-heading-inline">' . esc_html__( 'Background Jobs', 'gk-foundation' ) . '</h1>';
		echo '<hr class="wp-header-end">';
		echo '<div id="gk-background-jobs"></div>';
		echo '</div>';
	}

	/**
	 * Enqueues the Svelte bundle and localizes initial data.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	public function enqueue_svelte_assets(): void {
		if ( ! $this->is_job_overview_page() ) {
			return;
		}

		$js_path  = CoreHelpers::get_assets_path( self::JS_FILE );
		$css_path = CoreHelpers::get_assets_path( self::CSS_FILE );

		if ( ! file_exists( $js_path ) ) {
			return;
		}

		wp_enqueue_script(
			self::ASSETS_HANDLE,
			CoreHelpers::get_assets_url( self::JS_FILE ),
			[ 'wp-i18n' ],
			(string) filemtime( $js_path ),
			[ 'strategy' => 'defer' ]
		);

		if ( file_exists( $css_path ) ) {
			wp_enqueue_style(
				self::ASSETS_HANDLE,
				CoreHelpers::get_assets_url( self::CSS_FILE ),
				[],
				(string) filemtime( $css_path )
			);
		}

		wp_localize_script(
			self::ASSETS_HANDLE,
			'gkBackgroundJobs',
			[ 'data' => $this->get_initial_data() ]
		);

		// Load UI translations.
		$foundation_information = Core::get_instance()->get_foundation_information();

		TranslationsFramework::get_instance()->load_frontend_translations(
			$foundation_information['source_plugin']['TextDomain'],
			'',
			'gk-foundation'
		);
	}

	/**
	 * Builds the initial data payload for wp_localize_script.
	 *
	 * Includes the first page of jobs so the UI renders instantly without
	 * waiting for an AJAX round-trip.
	 *
	 * @since 1.12.0
	 *
	 * @return array
	 */
	public function get_initial_data(): array {
		$serializer = new JobSerializer();

		/**
		 * Filters the polling interval for the Background Jobs UI, in seconds.
		 *
		 * @since 1.12.0
		 *
		 * @param int $interval Polling interval in seconds. Default: 5.
		 */
		$poll_interval = (int) apply_filters( 'gk/foundation/scheduler/ui/poll-interval', 5 );

		$params = array_merge(
			AjaxRouter::get_ajax_params( JobAjaxController::AJAX_ROUTER ),
			[
				'languageDirection' => is_rtl() ? 'rtl' : 'ltr',
				'pageId'            => self::PAGE_ID,
				'timezone'          => wp_timezone_string(),
				'pollInterval'      => max( 1, $poll_interval ),
			]
		);

		if ( CoreHelpers::is_network_admin() ) {
			return $this->get_network_initial_data( $params, $serializer );
		}

		return $this->get_single_site_initial_data( $params, $serializer );
	}

	/**
	 * Builds initial data for the single-site Background Jobs page.
	 *
	 * @since 1.12.0
	 *
	 * @param array         $params     Shared params from get_initial_data().
	 * @param JobSerializer $serializer Serializer instance.
	 *
	 * @return array
	 */
	private function get_single_site_initial_data( array $params, JobSerializer $serializer ): array {
		$service = new JobQueryService( DbStore::get_instance(), $serializer );

		$status  = sanitize_text_field( wp_unslash( $_GET['status'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ?? 'activity' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$order   = sanitize_text_field( wp_unslash( $_GET['order'] ?? 'desc' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$search  = sanitize_text_field( wp_unslash( $_GET['s'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$product = sanitize_text_field( wp_unslash( $_GET['product'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$result = $service->list(
			[
				'status'           => $status,
				'orderby'          => $orderby,
				'order'            => $order,
				'search'           => $search,
				'product'          => $product,
				'include_products' => true,
				'per_page'         => self::get_per_page(),
			]
		);

		return [
			'params'  => $params,
			'initial' => $result,
		];
	}

	/**
	 * Builds initial data for the network admin Background Jobs page.
	 *
	 * Uses NetworkJobQueryService to aggregate jobs across all network sites.
	 *
	 * @since 1.12.0
	 *
	 * @param array         $params     Shared params from get_initial_data().
	 * @param JobSerializer $serializer Serializer instance.
	 *
	 * @return array
	 */
	private function get_network_initial_data( array $params, JobSerializer $serializer ): array {
		$network_service = new NetworkJobQueryService( $serializer );

		$params['isNetwork'] = true;
		$params['sites']     = $network_service->get_all_sites();

		$site_id = (int) ( $_GET['site_id'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$product = sanitize_text_field( wp_unslash( $_GET['product'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$result = $network_service->list(
			[
				'status'           => sanitize_text_field( wp_unslash( $_GET['status'] ?? '' ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				'orderby'          => 'activity',
				'order'            => 'desc',
				's'                => sanitize_text_field( wp_unslash( $_GET['s'] ?? '' ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				'product'          => $product,
				'include_products' => true,
				'site_id'          => $site_id,
				'per_page'         => self::get_per_page(),
			]
		);

		return [
			'params'  => $params,
			'initial' => $result,
		];
	}

	/**
	 * Disables all notices.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	public function disable_notices() {
		if ( ! $this->is_job_overview_page() ) {
			return;
		}

		add_action(
			'admin_enqueue_scripts',
			function () {
				remove_all_actions( 'admin_notices' );
				remove_all_actions( 'all_admin_notices' );
			}
		);

		add_action(
			'admin_enqueue_scripts',
			function () {
				remove_all_filters( 'update_footer' );
			}
		);

		add_filter(
			'admin_footer_text',
			function () {
				return '';
			}
		);
	}

	/**
	 * Checks if the current page is the Background Jobs page.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public function is_job_overview_page(): bool {
		return filter_input( INPUT_GET, 'page' ) === self::PAGE_ID;
	}

	/**
	 * Registers the "per page" screen option for the Background Jobs page.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	public function register_screen_options(): void {
		if ( ! $this->is_job_overview_page() ) {
			return;
		}

		add_screen_option(
			'per_page',
			[
				'default' => self::PER_PAGE_DEFAULT,
				'option'  => self::PER_PAGE_OPTION,
			]
		);
	}

	/**
	 * Saves the per-page screen option value.
	 *
	 * @since 1.12.0
	 *
	 * @param mixed  $status Default false (to skip saving).
	 * @param string $option Screen option name.
	 * @param int    $value  Screen option value.
	 *
	 * @return int
	 */
	public function save_per_page_option( $status, $option, $value ) {
		return (int) $value;
	}

	/**
	 * Returns the user's saved per-page preference.
	 *
	 * @since 1.12.0
	 *
	 * @return int
	 */
	public static function get_per_page(): int {
		$per_page = (int) get_user_option( self::PER_PAGE_OPTION );

		return $per_page > 0 ? $per_page : self::PER_PAGE_DEFAULT;
	}
}
