<?php
/**
 * @package GravityKit\GravityView\PageBuilder
 * @since 3.0.0
 */

namespace GravityKit\GravityView\PageBuilder;

use GravityKit\GravityView\Shortcode\ShortcodeRenderer;

if ( ! defined( 'GRAVITYVIEW_DIR' ) ) { die(); }

/**
 * Global registry of View IDs rendered by page builder modules.
 *
 * Trait static properties are per-class, so this standalone class provides
 * a shared registry across all builders. Used by the admin toolbar to show
 * Edit View links for Views embedded via page builders.
 *
 * @since 3.0.0
 */
class RenderedViewRegistry {

	/**
	 * @var int[]
	 */
	private static $view_ids = [];

	/**
	 * Register a View ID.
	 *
	 * @param int $view_id View ID.
	 */
	public static function register( $view_id ) {
		$view_id = absint( $view_id );
		if ( ! $view_id ) {
			return;
		}

		self::$view_ids[ (string) $view_id ] = $view_id;
	}

	/**
	 * Get all registered View IDs.
	 *
	 * @return int[]
	 */
	public static function get_all() {
		return array_values( self::$view_ids );
	}
}
