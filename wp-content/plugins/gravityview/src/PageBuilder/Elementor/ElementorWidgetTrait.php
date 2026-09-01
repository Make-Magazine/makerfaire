<?php
/**
 * Elementor-specific trait for GravityView widgets.
 *
 * Extends the generic PageBuilderModuleTrait with Elementor
 * context detection and prop access.
 *
 * @package GravityKit\GravityView\PageBuilder\Elementor
 * @since 3.0.0
 */

namespace GravityKit\GravityView\PageBuilder\Elementor;

use GravityKit\GravityView\PageBuilder\PageBuilderModuleTrait;

/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

/**
 * Elementor widget trait.
 *
 * @since 3.0.0
 */
trait ElementorWidgetTrait {

	use PageBuilderModuleTrait;

	/**
	 * Get module properties, normalizing Elementor-specific control names.
	 *
	 * Elementor uses custom control names (e.g., 'embedded_view' instead of 'view_id')
	 * that differ from the standard naming conventions. This method pre-maps them using
	 * Elementor::ATTRIBUTE_MAPPING so the base trait's extract_block_atts()
	 * can find them via the standard camelCase key lookup.
	 *
	 * @inheritDoc
	 */
	protected function get_module_props() {
		$settings = $this->get_settings_for_display();

		// Add camelCase aliases for Elementor-specific control names.
		foreach ( Elementor::ATTRIBUTE_MAPPING as $elementor_key => $camel_key ) {
			if ( isset( $settings[ $elementor_key ] ) && ! isset( $settings[ $camel_key ] ) ) {
				$settings[ $camel_key ] = $settings[ $elementor_key ];
			}
		}

		return $settings;
	}

	/** @inheritDoc */
	protected function is_builder_editing() {
		$plugin = \Elementor\Plugin::$instance ?? null;

		if ( ! $plugin || empty( $plugin->editor ) || empty( $plugin->preview ) ) {
			return false;
		}

		return $plugin->editor->is_edit_mode() || $plugin->preview->is_preview_mode();
	}

	/** @inheritDoc */
	public static function get_builder_integration() {
		static $instance;

		if ( null === $instance ) {
			$instance = new Elementor();
		}

		return $instance;
	}
}
