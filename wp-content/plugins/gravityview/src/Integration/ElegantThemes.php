<?php
/**
 * Add Elegant Themes compatibility to GravityView (Divi theme).
 *
 * @since 3.0.0
 *
 * @package GravityKit\GravityView\Integration
 */

namespace GravityKit\GravityView\Integration;

use GravityKit\GravityView\PageBuilder\Divi\BasicModule;
use GravityKit\GravityView\PageBuilder\Divi\EntryFieldModule;
use GravityKit\GravityView\PageBuilder\Divi\EntryLinkModule;
use GravityKit\GravityView\PageBuilder\Divi\EntryModule;
use GravityKit\GravityView\PageBuilder\Divi\ViewDetailsModule;
use function gravityview;

/**
 * @inheritDoc
 * @since 1.17.2
 */
class ElegantThemes extends AbstractPluginHooks {

	/**
	 * @inheritDoc
	 * @since 1.17.2
	 */
	protected $function_name = 'et_setup_theme';

	/**
	 * Also activate when the Divi Builder plugin is installed without the Divi theme.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	protected $constant_name = 'ET_BUILDER_PLUGIN_DIR';

	function add_hooks() {
		parent::add_hooks();

		add_action( 'admin_init', [ $this, 'add_hooks_admin_init' ], 1 );
		add_filter( 'et_builder_enable_jquery_body', [ $this, 'edit_entry_jquery_compat_fix' ] );
		add_action( 'et_builder_ready', [ $this, 'load_divi_modules' ] );
	}

	/**
	 * Instantiate GravityView Divi Builder modules once Divi's module base class is available.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function load_divi_modules() {
		if ( ! class_exists( 'ET_Builder_Module' ) ) {
			return;
		}

		new BasicModule();
		new EntryModule();
		new EntryFieldModule();
		new EntryLinkModule();
		new ViewDetailsModule();
	}

	/**
	 * Prevent Divi from adding their stuff to GV pages
	 */
	public function add_hooks_admin_init() {
		if ( gravityview()->request->is_admin( '', null ) ) {
			// Prevent Divi from adding import/export modal dialog
			remove_action( 'admin_init', 'et_pb_register_builder_portabilities' );

			// Divi theme adds their quicktag shortcode buttons on a View CPT. This causes JS errors.
			remove_action( 'admin_head', 'et_add_simple_buttons' );
		}

		// Prevent Divi from rendering the sidebar with one of our Widgets in Page Builder.
		// See: https://github.com/gravityview/GravityView/issues/914
		add_action( 'et_pb_admin_excluded_shortcodes', [ $this, 'maybe_admin_excluded_shortcodes' ] );
	}

	/**
	 * Maybe prevent Divi (and others) from rendering our Widgets in the Page Builders Sidebar widget.
	 *
	 * Divi (among others) tries to render all the widgets in the sidebar.
	 * Our Widgets are not designed to be rendered in the administration panel.
	 *
	 * Try to find the sidebar it wants to render, see if it contains our Widgets
	 *  and prevent it from being rendered if it does. Allow everything else through.
	 *
	 * @see https://github.com/gravityview/GravityView/issues/914
	 *
	 * @param array $shortcodes The shortcodes that should not be rendered in the Page Builder.
	 *
	 * @return array The shortcodes that should not be rendered in the Page Builder.
	 */
	public function maybe_admin_excluded_shortcodes( $shortcodes ) {
		global $post;

		if ( ! $post || ! $post->post_content ) {
			return $shortcodes;
		}

		/**
		 * Find the et_pb_sidebar shortcode and the area it's assigned to.
		 */
		preg_match( '#\[et_pb_sidebar .*area="(.*?)"#', $post->post_content, $matches );

		if ( 2 != count( $matches ) ) {
			return $shortcodes;
		}

		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( empty( $sidebars_widgets[ $matches[1] ] ) ) {
			return $shortcodes;
		}

		foreach ( $sidebars_widgets[ $matches[1] ] as $widgets ) {
			if (
				/**
				 * Blocklisted widgets.
				 */
				0 === strpos( $widgets, 'gravityview_search' ) ||
				0 === strpos( $widgets, 'gv_recent_entries' )
			) {

					$shortcodes [] = 'et_pb_sidebar';
					break;
			}
		}

		return $shortcodes;
	}

	/**
	 * Prevents Divi from "faking" jQuery on the Edit Entry screen.
	 * This breaks some functionality, such as the Gravity Forms Signature Add-On.
	 *
	 * @used-by `et_builder_enable_jquery_body` filter
	 *
	 * @since 2.31.0
	 *
	 * @param bool $enable_jquery_compat
	 *
	 * @return bool
	 */
	public function edit_entry_jquery_compat_fix( $enable_jquery_compat ) {
		return gravityview()->request->is_edit_entry() ? false : $enable_jquery_compat;
	}
}
