<?php
/**
 * Divi Builder Integration
 *
 * Provides Divi Builder-specific field definitions and transformations.
 *
 * @package GravityKit\GravityView\PageBuilder
 * @since 3.0.0
 */

namespace GravityKit\GravityView\PageBuilder\Divi;

use GravityKit\GravityView\PageBuilder\PageBuilder;

/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

/**
 * Divi Builder-specific integration class.
 *
 * Extends the base PageBuilder with Divi Builder-specific
 * type mappings and transformations.
 *
 * @since 3.0.0
 */
class Divi extends PageBuilder {

	/**
	 * Singleton instance shared across all Divi modules.
	 *
	 * @since 3.0.0
	 *
	 * @var self|null
	 */
	private static $instance;

	/**
	 * Get the shared singleton instance.
	 *
	 * @since 3.0.0
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Builder identifier.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	protected $builder = 'divi';

	/**
	 * Map generic field type to Divi Builder-specific type.
	 *
	 * @since 3.0.0
	 *
	 * @param string $generic_type Generic type: 'text', 'number', 'select', etc.
	 *
	 * @return string Divi Builder-specific type.
	 */
	protected function map_field_type( $generic_type ) {
		$type_mapping = [
			'text'   => 'text',
			'number' => 'range',  // Divi uses 'range' for numbers.
			'select' => 'select',
		];

		return $type_mapping[ $generic_type ] ?? $generic_type;
	}

	/**
	 * Get Views list with Divi Builder-specific default label.
	 *
	 * @since 3.0.0
	 *
	 * @param string $default_label Default option label.
	 *
	 * @return array Views list.
	 */
	public function get_views_list( $default_label = '' ) {
		if ( empty( $default_label ) ) {
			$default_label = esc_html__( '-- Select a View --', 'gk-gravityview' );
		}

		return parent::get_views_list( $default_label );
	}
}
