<?php
/**
 * Divi-specific trait for GravityView modules.
 *
 * Extends the generic PageBuilderModuleTrait with Divi Visual Builder
 * computed callback support and builder context detection.
 *
 * @package GravityKit\GravityView\PageBuilder\Divi
 * @since 3.0.0
 */

namespace GravityKit\GravityView\PageBuilder\Divi;

use GravityKit\GravityView\PageBuilder\PageBuilderModuleTrait;
use GravityKit\GravityView\Shortcode\ShortcodeRenderer;

/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

/**
 * Divi Builder module trait.
 *
 * Provides Divi-specific implementations of the abstract methods from
 * PageBuilderModuleTrait, plus Visual Builder computed callback support.
 *
 * @since 3.0.0
 */
trait DiviModuleTrait {

	use PageBuilderModuleTrait;

	/** @inheritDoc */
	protected function get_module_props() {
		return $this->props;
	}

	/** @inheritDoc */
	protected function is_builder_editing() {
		if ( function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled() ) {
			return true;
		}

		if ( isset( $_GET['et_fb'] ) || isset( $_GET['et_pb_preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return true;
		}

		if ( wp_doing_ajax() && isset( $_POST['action'] ) && is_string( $_POST['action'] ) && 0 === strpos( $_POST['action'], 'et_' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return true;
		}

		return false;
	}

	/** @inheritDoc */
	public static function get_builder_integration() {
		return Divi::get_instance();
	}

	/**
	 * Render module content for the Divi Visual Builder computed callback.
	 *
	 * Static method required by Divi's computed field mechanism.
	 * Returns structured array with content, styles, and scripts.
	 *
	 * @since 3.0.0
	 *
	 * @param array $props Module properties from computed callback.
	 *
	 * @return array{content: string, styles: array, scripts: array}
	 */
	protected static function render_computed( $props ) {
		if ( ! class_exists( '\GV\View' ) ) {
			return self::empty_vb_result();
		}

		$block_atts = static::extract_block_atts( $props );

		$error = static::validate_required_atts( $block_atts );
		if ( $error ) {
			return self::placeholder_vb_result( $error );
		}

		$view = \GV\View::by_id( (int) ( $block_atts['viewId'] ?? 0 ) );
		if ( ! $view ) {
			return self::placeholder_vb_result( __( 'View not found.', 'gk-gravityview' ) );
		}

		$entry_error = static::validate_entry_exists( $block_atts, $view );
		if ( $entry_error ) {
			return self::placeholder_vb_result( $entry_error );
		}

		static::validate_enum_atts( $block_atts );

		$mapping   = array_combine( array_keys( $block_atts ), array_keys( $block_atts ) );
		$shortcode = static::get_builder_integration()->build_block_type_shortcode(
			static::get_block_type(),
			$block_atts,
			$view,
			$mapping
		);

		$rendered = ShortcodeRenderer::render( $shortcode, [
			'allowed_style_patterns'  => ShortcodeRenderer::ALLOWLIST_HANDLE_PATTERNS,
			'allowed_script_patterns' => ShortcodeRenderer::ALLOWLIST_HANDLE_PATTERNS,
			'ignored_script_handles'  => ShortcodeRenderer::BUILDER_IGNORED_SCRIPT_HANDLES,
		] );

		$content = $rendered['content'] ?? '';

		if ( '' === $content ) {
			return self::empty_vb_result();
		}

		return [
			'content' => $content,
			'styles'  => array_values( $rendered['styles'] ?? [] ),
			'scripts' => array_values( $rendered['scripts'] ?? [] ),
		];
	}

	/**
	 * Return an empty Visual Builder result.
	 *
	 * @since 3.0.0
	 *
	 * @return array{content: string, styles: array, scripts: array}
	 */
	private static function empty_vb_result() {
		return [ 'content' => '', 'styles' => [], 'scripts' => [] ];
	}

	/**
	 * Return a Visual Builder result with a placeholder message.
	 *
	 * @since 3.0.0
	 *
	 * @param string $message Placeholder message.
	 *
	 * @return array{content: string, styles: array, scripts: array}
	 */
	private static function placeholder_vb_result( $message ) {
		return [
			'content' => ShortcodeRenderer::render_placeholder( $message ),
			'styles'  => [],
			'scripts' => [],
		];
	}
}
