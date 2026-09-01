<?php
/**
 * GravityView Entry Module for Divi Builder
 *
 * @package GravityKit\GravityView\PageBuilder\Divi
 * @since 3.0.0
 */

namespace GravityKit\GravityView\PageBuilder\Divi;

/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

/**
 * GravityView Entry Module for Divi Builder.
 *
 * Displays a single GravityView entry.
 *
 * @since 3.0.0
 */
class EntryModule extends \ET_Builder_Module {

	use DiviModuleTrait;

	/**
	 * Module slug.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	public $slug = 'gk_gravityview_entry';

	/**
	 * Visual Builder support.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	public $vb_support = 'on';

	/**
	 * Module credits.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	protected $module_credits = [
		'module_uri' => 'https://www.gravitykit.com/products/gravityview/',
		'author'     => 'GravityKit',
		'author_uri' => 'https://www.gravitykit.com',
	];

	/**
	 * Module icon path (SVG file).
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	public $icon_path;

	/** @inheritDoc */
	protected static function get_block_type() {
		return 'entry';
	}

	/**
	 * Initialize the module.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function init() {
		$metadata        = self::get_builder_integration()->get_block_types_metadata()['entry'] ?? [];
		$this->name      = $metadata['title'] ?? '';
		$this->icon_path = self::get_builder_integration()->get_block_type_icon_path( 'entry' );

		$this->settings_modal_toggles = [
			'general' => [
				'toggles' => [
					'main_content' => esc_html__( 'Entry Settings', 'gk-gravityview' ),
				],
			],
		];

		$this->advanced_fields = [
			'background'     => false,
			'borders'        => false,
			'box_shadow'     => false,
			'margin_padding' => [
				'css' => [
					'important' => 'all',
				],
			],
			'fonts'          => false,
			'text'           => false,
			'link_options'   => false,
		];
	}

	/**
	 * Get module fields.
	 *
	 * @since 3.0.0
	 *
	 * @return array Module fields configuration.
	 */
	public function get_fields() {
		$views_list  = self::get_builder_integration()->get_views_list();
		$fields_meta = self::get_builder_integration()->get_block_type_field_metadata( 'entry' );

		return [
			'view_id'        => [
				'label'            => $fields_meta['viewId']['label'] ?? '',
				'type'             => 'select',
				'option_category'  => 'basic_option',
				'options'          => $views_list,
				'default'          => '',
				'searchable'       => true,
				'description'      => $fields_meta['viewId']['description'] ?? '',
				'toggle_slug'      => 'main_content',
				'computed_affects' => [ '__view_content' ],
			],
			'entry_id'       => [
				'label'            => $fields_meta['entryId']['label'] ?? '',
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'default'          => '',
				'description'      => $fields_meta['entryId']['description'] ?? '',
				'toggle_slug'      => 'main_content',
				'computed_affects' => [ '__view_content' ],
			],
			'__view_content' => [
				'type'                => 'computed',
				'computed_callback'   => [ self::class, 'render_view_content' ],
				'computed_depends_on' => [ 'view_id', 'entry_id' ],
			],
		];
	}

	/** @inheritDoc */
	public function render( $attrs, $content, $render_slug ) {
		return $this->render_module();
	}

	/**
	 * Computed callback for Visual Builder.
	 *
	 * @since 3.0.0
	 *
	 * @param array $props Module properties.
	 *
	 * @return array{content: string, styles: array, scripts: array}
	 */
	public static function render_view_content( $props ) {
		return self::render_computed( $props );
	}
}
