<?php
/**
 * @package GravityKit\GravityView\PageBuilder
 * @since 3.0.0
 */

namespace GravityKit\GravityView\PageBuilder;

use GravityKit\GravityView\Shortcode\ShortcodeRenderer;

if ( ! defined( 'GRAVITYVIEW_DIR' ) ) { die(); }


/**
 * Instance-based render lifecycle for page builder modules.
 *
 * Adds render_module() on top of the core utilities.
 * Used by Divi, Beaver Builder, and Elementor (not Gutenberg).
 *
 * @since 3.0.0
 */
trait PageBuilderModuleTrait {

	use PageBuilderModuleCore;

	/**
	 * Get module properties as a flat associative array.
	 *
	 * @since 3.0.0
	 *
	 * @return array Module properties.
	 */
	abstract protected function get_module_props();

	/**
	 * Check if we're in the page builder's editor/preview context.
	 *
	 * @since 3.0.0
	 *
	 * @return bool Whether the builder editor is active.
	 */
	abstract protected function is_builder_editing();

	/**
	 * Render the module output using the standard flow.
	 *
	 * Handles: extract props → validate → resolve View → build shortcode → render.
	 * Returns HTML string. Callers echo or return as needed per builder convention.
	 *
	 * @since 3.0.0
	 *
	 * @return string Module HTML output, or empty string.
	 */
	protected function render_module() {
		$block_atts = static::extract_block_atts( $this->get_module_props() );

		$error = static::validate_required_atts( $block_atts );
		if ( $error ) {
			return $this->is_builder_editing() ? ShortcodeRenderer::render_placeholder( $error ) : '';
		}

		$view_id = (int) ( $block_atts['viewId'] ?? 0 );
		$view    = \GV\View::by_id( $view_id );
		if ( ! $view ) {
			return $this->is_builder_editing()
				? ShortcodeRenderer::render_placeholder( __( 'View not found.', 'gk-gravityview' ) )
				: '';
		}

		RenderedViewRegistry::register( $view_id );

		$entry_error = static::validate_entry_exists( $block_atts, $view );
		if ( $entry_error ) {
			return $this->is_builder_editing() ? ShortcodeRenderer::render_placeholder( $entry_error ) : '';
		}

		static::validate_enum_atts( $block_atts );

		$shortcode      = static::build_shortcode( static::get_block_type(), $block_atts, $view );
		$render_options = [];

		if ( $this->is_builder_editing() ) {
			$render_options = [
				'allowed_style_patterns'  => ShortcodeRenderer::ALLOWLIST_HANDLE_PATTERNS,
				'allowed_script_patterns' => ShortcodeRenderer::ALLOWLIST_HANDLE_PATTERNS,
				'ignored_script_handles'  => ShortcodeRenderer::BUILDER_IGNORED_SCRIPT_HANDLES,
			];
		}

		$rendered = ShortcodeRenderer::render( $shortcode, $render_options );

		if ( '' === ( $rendered['content'] ?? '' ) ) {
			return '';
		}

		if ( $this->is_builder_editing() ) {
			$rendered = ShortcodeRenderer::apply_preview_fallbacks( $rendered );
		}

		return $rendered['content'] . ShortcodeRenderer::render_asset_loader( $rendered );
	}
}
