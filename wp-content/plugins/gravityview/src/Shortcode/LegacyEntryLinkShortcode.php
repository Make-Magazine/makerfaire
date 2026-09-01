<?php

namespace GravityKit\GravityView\Shortcode;

use \GV\Shortcodes\gv_entry_link;

/**
 * Shortcode to handle showing/hiding content in merge tags. Works great with GravityView Custom Content fields
 *
 * @since      3.0.0
 * @deprecated 2.45
 * @see        gv_entry_link
 */
class LegacyEntryLinkShortcode {

	/**
	 * @deprecated 2.45
	 * @see        gv_entry_link
	 */
	public function read_shortcode( $atts, $content = '', $tag = 'gv_entry_link' ) {
		\_deprecated_function( __FUNCTION__, '2.45', '\GV\Shortcodes\gv_entry_link' );

		$shortcode = new gv_entry_link();

		return $shortcode->callback( $atts, $content, $tag );
	}

	/**
	 * @deprecated 2.45
	 * @see        gv_entry_link
	 */
	public function edit_shortcode( $atts, $content = '', $tag = 'gv_edit_entry_link' ) {
		\_deprecated_function( __FUNCTION__, '2.45', '\GV\Shortcodes\gv_entry_link' );

		$shortcode = new gv_entry_link();

		return $shortcode->callback( $atts, $content, $tag );
	}

	/**
	 * @deprecated 2.45
	 * @see        gv_entry_link
	 */
	public function delete_shortcode( $atts, $content = '', $tag = 'gv_delete_entry_link' ) {
		\_deprecated_function( __FUNCTION__, '2.45', '\GV\Shortcodes\gv_entry_link' );

		$shortcode = new gv_entry_link();

		return $shortcode->callback( $atts, $content );
	}
}
