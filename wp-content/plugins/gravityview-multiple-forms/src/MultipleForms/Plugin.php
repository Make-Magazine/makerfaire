<?php

namespace GravityKit\MultipleForms;

use GFForms;
use GravityKit\MultipleForms\Admin\FilterSortTab;
use GravityKit\MultipleForms\Admin\Metabox;

/**
 * Class Plugin
 *
 * @since 0.3
 *
 * @package GravityKit\MultipleForms
 */
class Plugin extends AbstractSingleton {
	/**
	 * Holds the current version of the plugin.
	 *
	 * @since 0.3
	 *
	 * @var string
	 */
	public const VERSION = GV_MF_VERSION;

	/**
	 * Main file used to load the plugin, will be used for loading certain files and assets.
	 *
	 * @since 0.3
	 *
	 * @var string
	 */
	public const FILE = GV_MF_FILE;

	/**
	 * Main slug for the plugin.
	 *
	 * @since 0.3
	 *
	 * @var string
	 */
	public const SLUG = 'gk-multipleforms';

	/**
	 * Register of the service provider for the main plugin class.
	 *
	 * Important to note that the registering happens as soon as the plugin is loaded, so dependencies on other plugins cannot happen this early.
	 * Here all we need to hook on the `gk/multipleforms/loaded` which happens after the `plugins_loaded` and the extension is confirmed to pass
	 * all the requirements.
	 *
	 * @since 0.3
	 */
	protected function register(): void {
		add_action( 'plugins_loaded', [ $this, 'load' ], 20 );

		add_action( 'gk/multiple-forms/loaded', [ Query\Handler::class, 'instance' ] );
		add_action( 'gk/multiple-forms/loaded', [ Query\Sort::class, 'instance' ] );
		add_action( 'gk/multiple-forms/loaded', [ Query\IsApprovedModifier::class, 'instance' ] );
		add_action( 'gk/multiple-forms/loaded', [ Template\ComplexFieldEntryModifier::class, 'instance' ] );
		add_action( 'gk/multiple-forms/loaded', [ AJAX::class, 'instance' ] );
		add_action( 'gk/multiple-forms/loaded', [ View::class, 'instance' ] );
		add_action( 'gk/multiple-forms/loaded', [ Assets::class, 'instance' ] );
		add_action( 'gk/multiple-forms/loaded', [ Metabox::class, 'instance' ] );
		add_action( 'gk/multiple-forms/loaded', [ FilterSortTab::class, 'instance' ] );
	}

	/**
	 * Determines if GravityView is loaded and if we should load the Multiple Forms extension.
	 *
	 * @since 0.3
	 *
	 * @return bool
	 */
	public static function should_load(): bool {
		$display_admin_notice = function ( $notice ) {
			add_action( 'admin_notices', function () use ( $notice ) {
				echo wp_kses_post( "<div class='error' style='padding: 1.25em 0 1.25em 1em;'>$notice</div>" );
			} );
		};

		if ( ! class_exists( '\GV\Plugin' ) || ! function_exists( 'gravityview' ) ) {
			$admin_notice = strtr(
				esc_html_x( 'GravityView - Multiple Forms requires [url]GravityView[/url] to be installed and activated.', 'Placeholders inside [] are not to be translated.', 'gravityview-multiple-forms' ),
				[
					'[url]'  => '<a href="https://www.gravitykit.com/extensions/gravityview/">',
					'[/url]' => '</a>',
				]
			);

			$display_admin_notice( $admin_notice );

			return false;
		}

		if ( ! class_exists( 'GFForms' ) || ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
			$admin_notice = strtr(
				esc_html_x( 'GravityView - Multiple Forms requires [url]Gravity Forms[/url] to be installed and activated.', 'Placeholders inside [] are not to be translated.', 'gravityview-multiple-forms' ),
				[
					'[url]'  => '<a href="https://www.gravitykit.com/gravityforms">',
					'[/url]' => '</a>',
				]
			);

			$display_admin_notice( $admin_notice );

			return false;
		}

		return true;
	}

	/**
	 * Load all pieces of the plugin properly.
	 *
	 * @since 0.3
	 */
	public function load(): void {
		if ( ! static::should_load() ) {
			return;
		}

		$this->register_to_foundation();

		$this->load_functions();

		$this->initialize_extension();

		/**
		 * Triggers an action when the plugin is fully loaded.
		 *
		 * @since 0.3
		 */
		do_action( 'gk/multiple-forms/loaded' );
	}

	/**
	 * The code that runs during plugin activation.
	 *
	 * @since 0.3
	 */
	public static function activate(): void {

	}

	/**
	 * The code that runs during plugin deactivation.
	 *
	 * @since 0.3
	 */
	public static function deactivate(): void {

	}

	/**
	 * Register this addon to GravityView's Foundation Core.
	 *
	 * @since 0.3
	 *
	 * @return void
	 */
	public function register_to_foundation(): void {
		if ( ! class_exists( 'GravityKit\GravityView\Foundation\Core' ) ) {
			return;
		}

		\GravityKit\GravityView\Foundation\Core::register( static::FILE );
	}

	/**
	 * Fetches the base url for this plugin.
	 *
	 * @since 0.3
	 *
	 * @return string
	 */
	public static function get_base_url(): string {
		static $base_url;

		if ( empty( $base_url ) ) {
			$base_url = trailingslashit( plugin_dir_url( static::FILE ) );
		}

		return $base_url;
	}

	/**
	 * Fetches the base path for this plugin.
	 *
	 * @since 0.3
	 *
	 * @return string
	 */
	public static function get_base_path(): string {
		static $base_path;

		if ( empty( $base_path ) ) {
			$base_path = trailingslashit( dirname( static::FILE ) );
		}

		return $base_path;
	}

	public function initialize_extension(): void {
		new Extension();
	}

	/**
	 * Loads all functions for this plugin.
	 *
	 * @since 0.3
	 */
	protected function load_functions(): void {

	}
}
