<?php
/**
 * Gutenberg Integration
 *
 * Provides Gutenberg-specific field definitions and transformations.
 *
 * @package GravityKit\GravityView\Gutenberg
 * @since 3.0.0
 */

namespace GravityKit\GravityView\PageBuilder\Gutenberg;

use GravityKit\GravityView\PageBuilder\PageBuilder;
use GVCommon;

/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

/**
 * Gutenberg-specific integration class.
 *
 * Extends the base PageBuilder with Gutenberg-specific
 * type mappings and view list format.
 *
 * @since 3.0.0
 */
class Gutenberg extends PageBuilder {

	/**
	 * Builder identifier.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	protected $builder = 'gutenberg';

	/**
	 * Map generic field type to Gutenberg-specific type.
	 *
	 * @since 3.0.0
	 *
	 * @param string $generic_type Generic type: 'text', 'number', 'select', etc.
	 *
	 * @return string Gutenberg-specific type.
	 */
	protected function map_field_type( $generic_type ) {
		$type_mapping = [
			'text'   => 'string',
			'number' => 'number',
			'select' => 'string', // Gutenberg uses string with enum for selects.
		];

		return $type_mapping[ $generic_type ] ?? $generic_type;
	}

	/**
	 * Get Views list in Gutenberg format.
	 *
	 * Returns array of objects with value/label/secret for Gutenberg blocks.
	 *
	 * @since 3.0.0
	 *
	 * @param string $default_label Unused for Gutenberg format.
	 *
	 * @return array Views list in Gutenberg format.
	 */
	public function get_views_list( $default_label = '' ) {
		if ( ! class_exists( 'GVCommon' ) ) {
			return [];
		}

		$views = GVCommon::get_all_views();

		if ( empty( $views ) ) {
			return [];
		}

		// No View hydration here: this runs on every block-editor load, and
		// View::by_id() costs ~130µs per View — seconds on installs with
		// thousands. The cap check must keep the object ID: per-View
		// grants/denials via map_meta_cap/user_has_cap are a documented
		// extension point (see RolesCapabilities::has_cap).
		$formatted_views = [];

		foreach ( $views as $view_post ) {
			if ( ! GVCommon::has_cap( 'edit_gravityviews', $view_post->ID ) ) {
				continue;
			}

			$secret = \GV\View::get_validation_secret_by_id( (int) $view_post->ID );

			$formatted_views[] = [
				'value'  => (string) $view_post->ID,
				// translators: %1$s is the View title, %2$d is the View ID.
				'label'  => esc_html( sprintf( __( '%1$s (#%2$d)', 'gk-gravityview' ), $view_post->post_title, $view_post->ID ) ),
				'secret' => null !== $secret ? sanitize_text_field( $secret ) : null,
			];
		}

		return $formatted_views;
	}
}
