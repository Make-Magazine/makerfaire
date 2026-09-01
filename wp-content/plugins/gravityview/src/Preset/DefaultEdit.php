<?php
/**
 * @package GravityKit\GravityView\Preset
 */

namespace GravityKit\GravityView\Preset;

use GravityKit\GravityView\Utils\Assets;

/**
 * GravityView Default Edit Template.
 * Defines Edit Table (default) template (Edit Entry) - this is not visible; it's an internal template only.
 *
 * @since 3.0.0
 */
class DefaultEdit extends \GravityView_Template {

	function __construct( $id = 'default_table_edit', $settings = [], $field_options = [], $areas = [] ) {

		$edit_settings = [
			'slug'        => 'edit',
			'type'        => 'internal',
			'label'       => __( 'Edit Table', 'gk-gravityview' ),
			'description' => __( 'Display items in a table view.', 'gk-gravityview' ),
			'logo'        => Assets::url( 'presets/default-table/logo-default-table.png' ),
			'css_source'  => \gravityview_css_url( 'table-view.css', GRAVITYVIEW_DIR . 'templates/css/' ),
		];

		$settings = wp_parse_args( $settings, $edit_settings );

		/**
		 * @see  GravityView_Admin_Views::get_default_field_options() for Generic Field Options
		 * @var array
		 */
		$field_options = [];

		$areas = [
			[
				'1-1' => [
					[
						'areaid' => 'edit-fields',
						'title'  => __( 'Visible Edit Fields', 'gk-gravityview' ),
					],
				],
			],
		];

		parent::__construct( $id, $settings, $field_options, $areas );
	}
}
