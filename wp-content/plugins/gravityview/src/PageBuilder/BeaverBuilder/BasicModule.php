<?php
/**
 * GravityView Basic Module for Beaver Builder
 *
 * @package GravityKit\GravityView\PageBuilder\BeaverBuilder
 * @since 3.0.0
 */

namespace GravityKit\GravityView\PageBuilder\BeaverBuilder;

use FLBuilderModule;
use FLBuilder;

/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

/**
 * GravityView Basic Module for Beaver Builder.
 *
 * Provides basic functionality for embedding GravityView Views in Beaver Builder.
 *
 * @since 3.0.0
 */
class BasicModule extends FLBuilderModule {

	use BeaverBuilderModuleTrait;

	/** @inheritDoc */
	protected static function get_block_type() {
		return 'view';
	}

	/**
	 * Module constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		parent::__construct(
			[
				'name'            => esc_html__( 'GravityView', 'gk-gravityview' ),
				'description'     => esc_html__( 'Display a GravityView View.', 'gk-gravityview' ),
				'category'        => esc_html__( 'Basic', 'gk-gravityview' ),
				'group'           => esc_html__( 'GravityKit', 'gk-gravityview' ),
				'dir'             => __DIR__,
				'url'             => plugins_url( '', __FILE__ ),
				'editor_export'   => true,
				'enabled'         => true,
				'partial_refresh' => true,
			]
		);
	}

	/**
	 * Get module icon SVG.
	 *
	 * @since 3.0.0
	 *
	 * @param string $icon Icon name (unused).
	 *
	 * @return string SVG icon markup.
	 */
	public function get_icon( $icon = '' ) {
		return self::get_builder_integration()->get_block_type_icon_svg( 'view' );
	}

	/** @inheritDoc */
	public function frontend() {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in render_module/render_placeholder.
		echo $this->render_module();
	}
}

/**
 * Enqueue the shared dynamic sort field loader when the BB builder is active.
 *
 * `FLBuilderModel::is_builder_active()` caches its result in a static the first
 * time it is called; if anything queries it before the main query has resolved
 * (e.g., a sidebar widget rendering during init), it can latch to false and
 * stay false for the rest of the request. The `?fl_builder` query var is the
 * canonical signal BB itself uses to enter edit mode, so we trust it directly.
 */
add_action( 'wp_enqueue_scripts', function () {
	$is_bb_edit = isset( $_GET['fl_builder'] )
		|| ( class_exists( '\FLBuilderModel' ) && \FLBuilderModel::is_builder_active() );

	if ( $is_bb_edit ) {
		BasicModule::get_builder_integration()->enqueue_sort_field_script();
	}
}, 20 );

/**
 * Register the module with Beaver Builder.
 */
$integration     = BasicModule::get_builder_integration();
$view_fields_meta = $integration->get_block_type_field_metadata( 'view' );

FLBuilder::register_module(
	__NAMESPACE__ . '\BasicModule',
	[
		'general' => [
			'title'    => esc_html__( 'General', 'gk-gravityview' ),
			'sections' => [
				'view_selection' => [
					'title'  => esc_html__( 'View Selection', 'gk-gravityview' ),
					'fields' => [
						'viewId' => [
							'type'    => 'select',
							'label'   => $view_fields_meta['viewId']['label'] ?? '',
							'default' => '',
							'options' => $integration->get_views_list(),
							'preview' => [ 'type' => 'refresh' ],
						],
					],
				],
				'display_settings' => [
					'title'  => esc_html__( 'Display Settings', 'gk-gravityview' ),
					'fields' => [
						'pageSize'   => [
							'type'        => 'text',
							'label'       => $integration->get_field_label( 'page_size' ),
							'placeholder' => $integration->get_field_placeholder( 'page_size' ),
							'default'     => '',
							'maxlength'   => '5',
							'size'        => '5',
							'description' => $integration->get_field_description( 'page_size' ),
							'preview'     => [ 'type' => 'refresh' ],
						],
						'offset'     => [
							'type'        => 'text',
							'label'       => $integration->get_field_label( 'offset' ),
							'placeholder' => $integration->get_field_placeholder( 'offset' ),
							'default'     => '',
							'maxlength'   => '5',
							'size'        => '5',
							'description' => $integration->get_field_description( 'offset' ),
							'preview'     => [ 'type' => 'refresh' ],
						],
						'classValue' => [
							'type'        => 'text',
							'label'       => $integration->get_field_label( 'class' ),
							'placeholder' => $integration->get_field_placeholder( 'class' ),
							'preview'     => [ 'type' => 'refresh' ],
						],
					],
				],
				'sorting' => [
					'title'  => esc_html__( 'Sorting', 'gk-gravityview' ),
					'fields' => [
						'sortField'     => [
							'type'        => 'select',
							'label'       => $integration->get_field_label( 'sort_field' ),
							'options'     => $integration->get_sort_field_options(),
							'default'     => '',
							'description' => $integration->get_field_description( 'sort_field' ),
							'preview'     => [ 'type' => 'refresh' ],
						],
						'sortDirection' => [
							'type'    => 'select',
							'label'   => $integration->get_field_label( 'sort_direction' ),
							'default' => '',
							'options' => array_merge(
								[ '' => esc_html__( 'Default', 'gk-gravityview' ) ],
								$integration->get_sort_direction_options()
							),
							'description' => $integration->get_field_description( 'sort_direction' ),
							'preview'     => [ 'type' => 'refresh' ],
						],
					],
				],
				'filtering' => [
					'title'  => esc_html__( 'Search & Filtering', 'gk-gravityview' ),
					'fields' => [
						'searchField'    => [
							'type'        => 'text',
							'label'       => $integration->get_field_label( 'search_field' ),
							'placeholder' => $integration->get_field_placeholder( 'search_field' ),
							'description' => $integration->get_field_description( 'search_field' ),
							'preview'     => [ 'type' => 'refresh' ],
						],
						'searchValue'    => [
							'type'        => 'text',
							'label'       => $integration->get_field_label( 'search_value' ),
							'placeholder' => $integration->get_field_placeholder( 'search_value' ),
							'description' => $integration->get_field_description( 'search_value' ),
							'preview'     => [ 'type' => 'refresh' ],
						],
						'searchOperator' => [
							'type'    => 'select',
							'label'   => $integration->get_field_label( 'search_operator' ),
							'default' => 'contains',
							'options' => $integration->get_search_operator_options(),
							'preview' => [ 'type' => 'refresh' ],
						],
					],
				],
				'date_filtering' => [
					'title'  => esc_html__( 'Date Filtering', 'gk-gravityview' ),
					'fields' => [
						'startDate' => [
							'type'        => 'text',
							'label'       => $integration->get_field_label( 'start_date' ),
							'placeholder' => $integration->get_field_placeholder( 'start_date' ),
							'description' => $integration->get_field_description( 'start_date' ),
							'preview'     => [ 'type' => 'refresh' ],
						],
						'endDate'   => [
							'type'        => 'text',
							'label'       => $integration->get_field_label( 'end_date' ),
							'placeholder' => $integration->get_field_placeholder( 'end_date' ),
							'description' => $integration->get_field_description( 'end_date' ),
							'preview'     => [ 'type' => 'refresh' ],
						],
					],
				],
				'single_entry' => [
					'title'  => esc_html__( 'Single Entry Settings', 'gk-gravityview' ),
					'fields' => [
						'singleTitle'   => [
							'type'        => 'text',
							'label'       => $integration->get_field_label( 'single_title' ),
							'placeholder' => $integration->get_field_placeholder( 'single_title' ),
							'description' => $integration->get_field_description( 'single_title' ),
							'preview'     => [ 'type' => 'refresh' ],
						],
						'backLinkLabel' => [
							'type'        => 'text',
							'label'       => $integration->get_field_label( 'back_link_label' ),
							'placeholder' => $integration->get_field_placeholder( 'back_link_label' ),
							'description' => $integration->get_field_description( 'back_link_label' ),
							'preview'     => [ 'type' => 'refresh' ],
						],
					],
				],
				'advanced' => [
					'title'  => esc_html__( 'Advanced', 'gk-gravityview' ),
					'fields' => [
						'postId' => [
							'type'        => 'text',
							'label'       => $integration->get_field_label( 'post_id' ),
							'placeholder' => $integration->get_field_placeholder( 'post_id' ),
							'description' => $integration->get_field_description( 'post_id' ),
							'preview'     => [ 'type' => 'refresh' ],
						],
					],
				],
			],
		],
	]
);
