<?php
/**
 * Elementor Integration
 *
 * Provides Elementor-specific field definitions and transformations.
 *
 * @package GravityKit\GravityView\PageBuilder
 * @since 3.0.0
 */

namespace GravityKit\GravityView\PageBuilder\Elementor;

use GravityKit\GravityView\PageBuilder\PageBuilder;
use Elementor\Controls_Manager;

/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

/**
 * Elementor-specific integration class.
 *
 * Extends the base PageBuilder with Elementor-specific
 * type mappings and transformations.
 *
 * @since 3.0.0
 */
class Elementor extends PageBuilder {

	/**
	 * Builder identifier.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	protected $builder = 'elementor';

	/**
	 * Attribute mapping from Elementor settings to camelCase block attributes.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	const ATTRIBUTE_MAPPING = [
		// BasicWidget (main GravityView widget).
		'embedded_view'          => 'viewId',
		'page_size'              => 'pageSize',
		'sort_field'             => 'sortField',
		'sort_direction'         => 'sortDirection',
		'search_field'           => 'searchField',
		'search_value'           => 'searchValue',
		'search_operator'        => 'searchOperator',
		'start_date'             => 'startDate',
		'end_date'               => 'endDate',
		'offset'                 => 'offset',
		'class_value'            => 'classValue',
		'single_title'           => 'singleTitle',
		'back_link_label'        => 'backLinkLabel',
		'post_id'                => 'postId',
		// Secondary widgets (Entry, EntryField, EntryLink, ViewDetails)
		// use snake_case control IDs that need explicit mapping.
		'view_id'                => 'viewId',
		'entry_id'               => 'entryId',
		'field_id'               => 'fieldId',
		'return_format'          => 'returnFormat',
		'link_atts'              => 'linkAtts',
		'field_values'           => 'fieldValues',
		'field_setting_overrides' => 'fieldSettingOverrides',
	];

	/**
	 * Map generic field type to Elementor-specific type.
	 *
	 * @since 3.0.0
	 *
	 * @param string $generic_type Generic type: 'text', 'number', 'select', etc.
	 *
	 * @return string Elementor-specific type.
	 */
	protected function map_field_type( $generic_type ) {
		$type_mapping = [
			'text'   => Controls_Manager::TEXT,
			'number' => Controls_Manager::NUMBER,
			'select' => Controls_Manager::SELECT,
		];

		return $type_mapping[ $generic_type ] ?? $generic_type;
	}

	/**
	 * Register inline CSS for custom widget icons.
	 *
	 * Uses SVG mask-image so icons inherit Elementor's panel icon color.
	 * Call this on the `elementor/editor/before_enqueue_styles` action.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function register_widget_icon_styles() {
		$block_types = [ 'view', 'entry', 'entry-field', 'entry-link', 'view-details' ];
		$css         = '';

		foreach ( $block_types as $block_type ) {
			$svg_path = $this->get_block_type_icon_path( $block_type );

			if ( empty( $svg_path ) || ! is_readable( $svg_path ) ) {
				continue;
			}

			$svg_content = file_get_contents( $svg_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local file read.

			if ( empty( $svg_content ) ) {
				continue;
			}

			// Encode SVG for use as a CSS data URI.
			$encoded_svg = 'data:image/svg+xml,' . rawurlencode( $svg_content );
			$class_name  = 'gk-gravityview-icon-' . $block_type;

			$css .= ".{$class_name}{display:inline-block;width:1em;height:1em;background-color:currentColor;-webkit-mask-image:url(\"{$encoded_svg}\");mask-image:url(\"{$encoded_svg}\");-webkit-mask-size:contain;mask-size:contain;-webkit-mask-repeat:no-repeat;mask-repeat:no-repeat;-webkit-mask-position:center;mask-position:center;}";
		}

		if ( ! empty( $css ) ) {
			wp_register_style( 'gk-gravityview-elementor-icons', false ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Inline style, no version needed.
			wp_enqueue_style( 'gk-gravityview-elementor-icons' );
			wp_add_inline_style( 'gk-gravityview-elementor-icons', $css );
		}
	}

	/**
	 * Get the Elementor icon CSS class for a block type.
	 *
	 * @since 3.0.0
	 *
	 * @param string $block_type Block type identifier.
	 *
	 * @return string CSS class name.
	 */
	public function get_widget_icon_class( $block_type ) {
		return 'gk-gravityview-icon-' . $block_type;
	}

	/**
	 * Get Views list with Elementor-specific default label.
	 *
	 * @since 3.0.0
	 *
	 * @param string $default_label Default option label.
	 *
	 * @return array Views list.
	 */
	public function get_views_list( $default_label = '' ) {
		if ( empty( $default_label ) ) {
			$default_label = esc_html__( '— Select a View —', 'gk-gravityview' );
		}

		return parent::get_views_list( $default_label );
	}
}
