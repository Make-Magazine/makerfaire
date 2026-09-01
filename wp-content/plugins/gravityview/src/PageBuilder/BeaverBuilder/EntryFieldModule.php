<?php
/**
 * GravityView Entry Field Module for Beaver Builder
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
 * GravityView Entry Field Module for Beaver Builder.
 *
 * Displays a specific field from a GravityView entry.
 *
 * @since 3.0.0
 */
class EntryFieldModule extends FLBuilderModule {

	use BeaverBuilderModuleTrait;

	/** @inheritDoc */
	protected static function get_block_type() {
		return 'entry-field';
	}

	/**
	 * Module constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		$metadata = self::get_builder_integration()->get_block_types_metadata()['entry-field'] ?? [];

		parent::__construct(
			[
				// translators: "GV" is short for "GravityView". Keep it short to avoid truncation in Beaver Builder.
				'name'            => esc_html__( 'GV Entry Field', 'gk-gravityview' ),
				'description'     => $metadata['description'] ?? '',
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
		return self::get_builder_integration()->get_block_type_icon_svg( 'entry-field' );
	}

	/** @inheritDoc */
	public function frontend() {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in render_module/render_placeholder.
		echo $this->render_module();
	}
}

/**
 * Register the module with Beaver Builder.
 */
$integration = EntryFieldModule::get_builder_integration();
$fields_meta = $integration->get_block_type_field_metadata( 'entry-field' );

FLBuilder::register_module(
	__NAMESPACE__ . '\EntryFieldModule',
	[
		'general' => [
			'title'    => esc_html__( 'General', 'gk-gravityview' ),
			'sections' => [
				'view_selection' => [
					'title'  => esc_html__( 'View Selection', 'gk-gravityview' ),
					'fields' => [
						'viewId'  => [
							'type'        => 'select',
							'label'       => $fields_meta['viewId']['label'] ?? '',
							'description' => $fields_meta['viewId']['description'] ?? '',
							'default'     => '',
							'options'     => $integration->get_views_list(),
							'preview'     => [ 'type' => 'refresh' ],
						],
						'entryId' => [
							'type'        => 'text',
							'label'       => $fields_meta['entryId']['label'] ?? '',
							'description' => $fields_meta['entryId']['description'] ?? '',
							'placeholder' => $fields_meta['entryId']['placeholder'] ?? '',
							'preview'     => [ 'type' => 'refresh' ],
						],
						'fieldId' => [
							'type'        => 'text',
							'label'       => $fields_meta['fieldId']['label'] ?? '',
							'description' => $fields_meta['fieldId']['description'] ?? '',
							'placeholder' => $fields_meta['fieldId']['placeholder'] ?? '',
							'preview'     => [ 'type' => 'refresh' ],
						],
						'fieldSettingOverrides' => [
							'type'        => 'text',
							'label'       => $fields_meta['fieldSettingOverrides']['label'] ?? '',
							'description' => $fields_meta['fieldSettingOverrides']['description'] ?? '',
							'placeholder' => $fields_meta['fieldSettingOverrides']['placeholder'] ?? '',
							'preview'     => [ 'type' => 'refresh' ],
						],
					],
				],
			],
		],
	]
);
