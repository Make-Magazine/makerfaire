<?php
/**
 * Beaver Builder-specific trait for GravityView modules.
 *
 * Extends the generic PageBuilderModuleTrait with Beaver Builder
 * context detection and prop access.
 *
 * @package GravityKit\GravityView\PageBuilder\BeaverBuilder
 * @since 3.0.0
 */

namespace GravityKit\GravityView\PageBuilder\BeaverBuilder;

use GravityKit\GravityView\PageBuilder\PageBuilderModuleTrait;

/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

/**
 * Beaver Builder module trait.
 *
 * @since 3.0.0
 */
trait BeaverBuilderModuleTrait {

	use PageBuilderModuleTrait;

	/** @inheritDoc */
	protected function get_module_props() {
		return (array) $this->settings;
	}

	/**
	 * @inheritDoc
	 *
	 * `FLBuilderModel::is_builder_active()` caches in a static the first time
	 * it's called — anything querying it before the main query resolves (e.g.
	 * a sidebar widget rendering during init) latches it to false for the rest
	 * of the request. Trust the raw request signals first so module render
	 * inside an AJAX refresh still recognizes edit mode.
	 */
	protected function is_builder_editing() {
		if ( isset( $_GET['fl_builder'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return true;
		}

		if ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && is_string( $_REQUEST['action'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			&& 0 === strpos( $_REQUEST['action'], 'fl_builder_' ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		) {
			return true;
		}

		return class_exists( '\FLBuilderModel' )
			&& method_exists( '\FLBuilderModel', 'is_builder_active' )
			&& \FLBuilderModel::is_builder_active();
	}

	/** @inheritDoc */
	public static function get_builder_integration() {
		static $instance;

		if ( null === $instance ) {
			$instance = new BeaverBuilder();
		}

		return $instance;
	}
}
