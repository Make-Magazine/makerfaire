<?php
/**
 * Shortcode to handle showing/hiding content in merge tags. Works great with GravityView Custom Content fields
 *
 * @package   GravityKit\GravityView\Shortcode
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      http://www.gravitykit.com
 *
 * @since 3.0.0 Migrated to PSR-4 namespace.
 *
 * @deprecated 3.0.0
 * @see \GV\Shortcodes\gvlogic
 */

namespace GravityKit\GravityView\Shortcode;

/**
 * Legacy [gvlogic] shortcode handler.
 *
 * This is the legacy `GVLogic_Shortcode` class, distinct from the
 * `\GV\Shortcodes\gvlogic` class (already migrated).
 *
 * @since 3.0.0
 * @since 3.0.0 Migrated to PSR-4 namespace.
 *
 * @deprecated 3.0.0
 * @see \GV\Shortcodes\gvlogic
 */
class LegacyGVLogicShortcode {

	/**
	 * @var self|null
	 */
	public static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			return self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Handle the [gvlogic] shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @param string $tag     Shortcode tag.
	 *
	 * @return string
	 */
	public function shortcode( $atts, $content = '', $tag = '' ) {
		$shortcode = new \GV\Shortcodes\gvlogic();
		return $shortcode->callback( $atts, $content, $tag );
	}
}
