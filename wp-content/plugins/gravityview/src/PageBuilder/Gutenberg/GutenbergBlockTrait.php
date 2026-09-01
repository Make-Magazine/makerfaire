<?php
/**
 * Gutenberg-specific trait for GravityView blocks.
 *
 * Uses PageBuilderModuleCore (static utilities only) since Gutenberg blocks
 * render via static callbacks, not instance methods.
 *
 * @package GravityKit\GravityView\PageBuilder\Gutenberg
 * @since 3.0.0
 */

namespace GravityKit\GravityView\PageBuilder\Gutenberg;

use GravityKit\GravityView\PageBuilder\PageBuilderModuleCore;
use GravityKit\GravityView\Shortcode\ShortcodeRenderer;
use GravityKit\GravityView\Foundation\Helpers\Arr;
use GVCommon;

/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

/**
 * Gutenberg block trait.
 *
 * Provides a static `render_block()` method that uses the shared core utilities
 * (extract, validate, build shortcode) plus Gutenberg-specific REST/preview handling.
 *
 * @since 3.0.0
 */
trait GutenbergBlockTrait {

	use PageBuilderModuleCore;

	/** @inheritDoc */
	public static function get_builder_integration() {
		static $instance;

		if ( null === $instance ) {
			$instance = new Gutenberg();
		}

		return $instance;
	}

	/**
	 * Render a Gutenberg block using the shared infrastructure.
	 *
	 * Handles: build shortcode from metadata → render → return.
	 * Supports Gutenberg-specific previewAsShortcode mode and REST JSON responses.
	 *
	 * @since 3.0.0
	 *
	 * @param array $block_attributes Block attributes from block.json (camelCase).
	 *
	 * @return string Rendered HTML, JSON for REST, or shortcode string for preview.
	 */
	protected static function render_block( $block_attributes = [] ) {
		$block_type           = static::get_block_type();
		$preview_as_shortcode = Arr::get( $block_attributes, 'previewAsShortcode' );
		$is_rest_request      = GVCommon::is_rest_request();
		$metadata             = static::get_builder_integration()->get_block_types_metadata()[ $block_type ] ?? [];
		$attribute_map        = $metadata['attribute_map'] ?? [];

		// Register View ID for admin toolbar detection.
		$view_id = (int) ( $block_attributes['viewId'] ?? 0 );
		if ( $view_id ) {
			\GravityKit\GravityView\PageBuilder\RenderedViewRegistry::register( $view_id );
		}

		// Mask the secret in preview-as-shortcode mode.
		if ( $preview_as_shortcode && $is_rest_request && isset( $block_attributes['secret'] ) ) {
			$block_attributes['secret'] = '*********';
		}

		// Build shortcode using the shared infrastructure.
		$shortcode = ShortcodeRenderer::build_shortcode_from_atts(
			$metadata['shortcode'] ?? '',
			$block_attributes,
			$attribute_map,
			! empty( $metadata['supports_content'] )
		);

		if ( $preview_as_shortcode && $is_rest_request ) {
			if ( 'view' === $block_type ) {
				return wp_json_encode(
					[
						'content' => $shortcode,
						'scripts' => [],
						'styles'  => [],
					]
				);
			}

			return $shortcode;
		}

		$rendered = ShortcodeRenderer::render( $shortcode );

		if ( ! $is_rest_request ) {
			return $rendered['content'] ?? '';
		}

		// View block returns full JSON for REST (content + assets).
		if ( 'view' === $block_type ) {
			return wp_json_encode( $rendered );
		}

		return $rendered['content'] ?? '';
	}
}
