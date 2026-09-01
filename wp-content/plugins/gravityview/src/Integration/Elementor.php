<?php
/**
 * Add Elementor compatibility to GravityView.
 *
 * @package GravityKit\GravityView\Integration
 *
 * @since 1.17.2
 */

namespace GravityKit\GravityView\Integration;

use GravityKit\GravityView\PageBuilder\Elementor\BasicWidget;
use GravityKit\GravityView\PageBuilder\Elementor\Elementor as ElementorIntegration;
use GravityKit\GravityView\PageBuilder\Elementor\EntryFieldWidget;
use GravityKit\GravityView\PageBuilder\Elementor\EntryLinkWidget;
use GravityKit\GravityView\PageBuilder\Elementor\EntryWidget;
use GravityKit\GravityView\PageBuilder\Elementor\ViewDetailsWidget;

/**
 * @inheritDoc
 * @since 1.17.2
 */
class Elementor extends AbstractPluginHooks {

	/**
	 * @inheritDoc
	 * @since 1.17.2
	 */
	protected $constant_name = 'ELEMENTOR_VERSION';

	protected $content_meta_keys = [ '_elementor_data' ];

	/**
	 * @inheritDoc
	 */
	public function add_hooks() {
		parent::add_hooks();

		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'maybe_enqueue_editor_assets' ] );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'maybe_enqueue_editor_assets' ] );
		add_action( 'elementor/preview/enqueue_styles', [ $this, 'maybe_enqueue_editor_assets' ] );
		add_action( 'elementor/preview/enqueue_scripts', [ $this, 'maybe_enqueue_editor_assets' ] );
		add_action( 'elementor/editor/before_enqueue_styles', [ $this, 'register_widget_icons' ] );
	}

	/**
	 * Register GravityView widgets with Elementor.
	 *
	 * @since 3.0.0
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 *
	 * @return void
	 */
	public function register_widgets( $widgets_manager ) {
		$widgets_manager->register( new BasicWidget() );
		$widgets_manager->register( new EntryWidget() );
		$widgets_manager->register( new EntryFieldWidget() );
		$widgets_manager->register( new EntryLinkWidget() );
		$widgets_manager->register( new ViewDetailsWidget() );
	}

	/**
	 * Enqueue the shared builder asset loader inside Elementor editor/preview contexts.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function maybe_enqueue_editor_assets() {
		if ( ! $this->is_elementor_context() ) {
			return;
		}

		$asset_loader_path = GRAVITYVIEW_DIR . 'src/PageBuilder/assets/gv-builder-asset-loader.js';

		wp_enqueue_script(
			'gk-gravityview-builder-assets',
			plugins_url( 'src/PageBuilder/assets/gv-builder-asset-loader.js', GRAVITYVIEW_FILE ),
			[],
			is_readable( $asset_loader_path ) ? filemtime( $asset_loader_path ) : GV_PLUGIN_VERSION,
			true
		);
	}

	/**
	 * Inject SVG icon CSS into the Elementor editor styles so widget icons render correctly.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function register_widget_icons() {
		( new ElementorIntegration() )->register_widget_icon_styles();
	}

	/**
	 * Whether the current request is inside an Elementor editor or preview context.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	private function is_elementor_context() {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return false;
		}

		$elementor = \Elementor\Plugin::$instance;

		if ( ! $elementor ) {
			return false;
		}

		if ( ! empty( $elementor->editor ) && $elementor->editor->is_edit_mode() ) {
			return true;
		}

		if ( ! empty( $elementor->preview ) && $elementor->preview->is_preview_mode() ) {
			return true;
		}

		return false;
	}
}
