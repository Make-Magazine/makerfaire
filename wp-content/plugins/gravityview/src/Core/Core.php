<?php
/**
 * The core GravityView API.
 *
 * Returned by the wrapper gravityview() global function, exposes
 * all the required public functionality and classes, sets up global
 * state depending on current request context, etc.
 *
 * @package GravityKit\GravityView\Core
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Core;

use GravityKit\GravityView\Utils\Path;

/**
 * The core GravityView API.
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Core namespace.
 */
final class Core {
	/**
	 * @var \GravityKit\GravityView\Core\Core The static instance.
	 */
	private static $__instance = null;

	/**
	 * @var \GV\Plugin The WordPress plugin context.
	 *
	 * @api
	 * @since 2.0
	 */
	public $plugin;

	/**
	 * @var \GV\Admin_Request|\GV\Frontend_Request|\GV\Request The global request.
	 *
	 * @api
	 * @since 2.0
	 */
	public $request;

	/**
	 * @var \GV\Logger
	 *
	 * @api
	 * @since 2.0
	 */
	public $log;

	/**
	 * Get the global instance of Core.
	 *
	 * @return \GravityKit\GravityView\Core\Core The global instance of GravityView Core.
	 */
	public static function get() {
		if ( ! self::$__instance instanceof self ) {
			self::$__instance = new self();
		}
		return self::$__instance;
	}

	/**
	 * Very early initialization.
	 *
	 * Activation handlers, rewrites, post type registration.
	 */
	public static function bootstrap() {
		\GV\Plugin::get()->register_activation_hooks();
	}

	/**
	 * Bootstrap.
	 *
	 * @return void
	 */
	private function __construct() {
		self::$__instance = $this;
		$this->init();
	}

	/**
	 * Early initialization.
	 *
	 * Sets up the object, adds hooks, and instantiates classes that rely on
	 * side-effect construction. All class files are resolved via Composer
	 * autoloading (PSR-4 for `src/`).
	 *
	 * @since 2.0
	 * @since 3.0.0 Removed direct file loading in favor of Composer autoloading.
	 * @deprecated 3.0.0 Direct file loading removed; classes now resolve via Composer autoloading.
	 *
	 * @return void
	 */
	private function init() {
		$this->plugin = \GV\Plugin::get();

		/**
		 * Filter the logger instance being used for logging.
		 *
		 * @since 2.0
		 *
		 * @param \GV\Logger $logger The logger instance.
		 */
		$this->log = apply_filters( 'gravityview/logger', new \GV\WP_Action_Logger() );

		/** Request. */
		if ( \GV\Request::is_admin() ) {
			$this->request = new \GV\Admin_Request();
		} else {
			$this->request = new \GV\Frontend_Request();
		}

		/** Load function files that are guarded by GRAVITYVIEW_DIR and need explicit inclusion. */
		include Path::file( 'Utils/import-functions.php' );
		include Path::file( 'Utils/helper-functions.php' );
		include Path::file( 'Utils/connector-functions.php' );

		/** Instantiate classes that relied on side-effect construction in their original files. */
		\GravityView_Compatibility::getInstance();
		add_action( 'init', [ 'GravityView_Roles_Capabilities', 'get_instance' ], 1 );
		new \GravityView_Admin();
		new \GravityView_Cache();

		/** @deprecated 3.0.0 Legacy core loading — will be removed in a future major version. */
		$this->plugin->include_legacy_core();

		/** Register the gravityview post type upon WordPress core init. */
		add_action( 'init', [ '\GV\View', 'register_post_type' ] );
		add_action( 'init', [ '\GV\View', 'add_rewrite_endpoint' ] );

		/**
		 * Wire revision support for the GravityView post type. Snapshots
		 * the field/widget/template-settings post meta onto every revision
		 * row so the WP revisions UI shows real diffs and "Restore"
		 * brings the entire View shape back — not just post_title.
		 */
		add_action( 'init', [ '\GravityKit\GravityView\View\Revisions', 'register' ] );
		add_filter( 'map_meta_cap', [ '\GV\View', 'restrict' ], 11, 4 );
		add_action( 'template_redirect', [ '\GV\View', 'template_redirect' ] );
		add_action( 'the_content', [ '\GV\View', 'content' ] );

		/**
		 * Stop all further functionality from loading if the WordPress
		 * plugin is incompatible with the current environment.
		 *
		 * Saves some time and memory.
		 */
		if ( ! $this->plugin->is_compatible() ) {
			$this->log->error( 'GravityView 2.0 is not compatible with this environment. Stopped loading.' );

			return;
		}

		/** Add rewrite endpoint for single-entry URLs. */
		add_action( 'init', [ '\GV\Entry', 'add_rewrite_endpoint' ] );

		/** REST API */
		add_action( 'rest_api_init', [ '\GV\REST\Core', 'init' ] );

		/**
		 * WordPress Abilities API, routed through Foundation.
		 *
		 * Bootstrap registers the gk-gravityview-* categories and loads every
		 * ability file under src/Abilities/{Category}/, all via Foundation's
		 * abilities facade. We defer to `gk/foundation/initialized` because
		 * Bootstrap calls `\GravityKitFoundation::abilities()` synchronously
		 * at init time, and that alias isn't created until Foundation's own
		 * plugins_loaded callback (priority 100) runs. Hooking the action
		 * lets Foundation finish bootstrapping first; the hook never fires
		 * (and abilities stay unregistered) if Foundation isn't present.
		 *
		 * @since 3.0.0
		 */
		add_action( 'gk/foundation/initialized', [ '\GravityKit\GravityView\Abilities\Bootstrap', 'init' ] );

		/** Generate custom slugs on entry save. @todo Deprecate. */
		add_action( 'gform_entry_created', [ '\GravityView_API', 'entry_create_custom_slug' ], 10, 2 );

		/** Shortcodes */
		add_action( 'init', [ '\GV\Shortcodes\gravityview', 'add' ] );
		add_action( 'init', [ '\GV\Shortcodes\gventry', 'add' ] );
		add_action( 'init', [ '\GV\Shortcodes\gvfield', 'add' ] );
		add_action( 'init', [ '\GV\Shortcodes\gvlogic', 'add' ] );
		add_action( 'init', [ '\GV\Shortcodes\gv_entry_link', 'add' ] );

		/** oEmbed */
		add_action( 'init', [ '\GV\oEmbed', 'init' ], 11 );

		/** Gutenberg Blocks. */
		\GravityKit\GravityView\PageBuilder\Gutenberg\Blocks::get_instance();

		/** Powered By. */
		new \GravityView_Powered_By();

		/** Cache busting. */
		add_action( 'clean_post_cache', '\GV\View::_flush_cache' );

		/**
		 * The core has been loaded.
		 *
		 * Note: this is a very early load hook, not all of WordPress core has been loaded here.
		 * `init` hasn't been called yet.
		 *
		 * @since 2.0
		 */
		do_action( 'gravityview/loaded' );
	}

	public function __clone() { }

	public function __wakeup() { }

	/**
	 * Wrapper magic.
	 *
	 * Making developers happy, since 2017.
	 */
	public function __get( $key ) {
		static $views;

		switch ( $key ) {
			case 'views':
				if ( is_null( $views ) ) {
					$views = new \GV\Wrappers\views();
				}
				return $views;
		}
	}

	public function __set( $key, $value ) {
	}
}
