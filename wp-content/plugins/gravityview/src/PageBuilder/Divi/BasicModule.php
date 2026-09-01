<?php
/**
 * GravityView Basic Module for Divi Builder
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
 * GravityView Basic Module for Divi Builder.
 *
 * Provides basic functionality for embedding GravityView Views in Divi Builder.
 *
 * @since 3.0.0
 */
class BasicModule extends \ET_Builder_Module {

	use DiviModuleTrait;

	/**
	 * Module slug.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	public $slug = 'gk_gravityview';

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

	/**
	 * Initialize the module.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function init() {
		$this->name             = esc_html__( 'GravityView', 'gk-gravityview' );
		$this->icon_path        = self::get_builder_integration()->get_block_type_icon_path( 'view' );
		$this->main_css_element = '%%order_class%%';

		// Enqueue Visual Builder scripts only when VB is active.
		if ( $this->is_builder_editing() ) {
			// Enqueue the shared asset loader (required by the React component).
			$asset_loader_path = GRAVITYVIEW_DIR . 'src/PageBuilder/assets/gv-builder-asset-loader.js';
			wp_enqueue_script(
				'gk-gravityview-builder-assets',
				plugins_url( 'src/PageBuilder/assets/gv-builder-asset-loader.js', GRAVITYVIEW_FILE ),
				[],
				is_readable( $asset_loader_path ) ? filemtime( $asset_loader_path ) : GV_PLUGIN_VERSION,
				true
			);

			// Enqueue the Divi Visual Builder React component.
			$bundle_path = __DIR__ . '/build/bundle.min.js';
			wp_enqueue_script(
				'gk-gravityview-divi-vb',
				plugins_url( 'build/bundle.min.js', __FILE__ ),
				[ 'react', 'react-dom', 'wp-i18n', 'gk-gravityview-builder-assets' ],
				is_readable( $bundle_path ) ? filemtime( $bundle_path ) : GV_PLUGIN_VERSION,
				true
			);

			wp_set_script_translations( 'gk-gravityview-divi-vb', 'gk-gravityview' );
		}

		$this->settings_modal_toggles = [
			'general' => [
				'toggles' => [
					'main_content'     => esc_html__( 'View Selection', 'gk-gravityview' ),
					'display_settings' => esc_html__( 'Display Settings', 'gk-gravityview' ),
					'sorting'          => esc_html__( 'Sorting', 'gk-gravityview' ),
					'filtering'        => esc_html__( 'Search & Filtering', 'gk-gravityview' ),
					'date_filtering'   => esc_html__( 'Date Filtering', 'gk-gravityview' ),
					'single_entry'     => esc_html__( 'Single Entry Settings', 'gk-gravityview' ),
					'advanced'         => esc_html__( 'Advanced', 'gk-gravityview' ),
				],
			],
		];

		$this->advanced_fields = [
			'background'     => [
				'css' => [
					'main'      => '%%order_class%%',
					'important' => true,
				],
			],
			'borders'        => [
				'default' => [
					'css' => [
						'main' => [
							'border_radii'       => '%%order_class%%',
							'border_radii_hover' => '%%order_class%%:hover',
							'border_styles'      => '%%order_class%%',
						],
						'important' => true,
					],
				],
			],
			'box_shadow'     => [
				'default' => [
					'css' => [
						'main'      => '%%order_class%%',
						'important' => true,
					],
				],
			],
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
		$views_list       = self::get_builder_integration()->get_views_list();
		$view_fields_meta = self::get_builder_integration()->get_block_type_field_metadata( 'view' );

		$fields = [
			// View Selection.
			'view_id'         => [
				'label'            => $view_fields_meta['viewId']['label'] ?? '',
				'type'             => 'select',
				'option_category'  => 'basic_option',
				'options'          => $views_list,
				'default'          => '',
				'searchable'       => true,
				'description'      => $view_fields_meta['viewId']['description'] ?? '',
				'toggle_slug'      => 'main_content',
				'computed_affects' => [ '__view_content' ],
			],

			// Display Settings.
			'page_size'       => [
				'label'            => self::get_builder_integration()->get_field_label( 'page_size' ),
				'type'             => 'range',
				'option_category'  => 'basic_option',
				'default'          => '',
				'default_unit'     => '',
				'range_settings'   => [
					'min'  => '-1',
					'max'  => '',
					'step' => '1',
				],
				'unitless'         => true,
				'description'      => self::get_builder_integration()->get_field_description( 'page_size' ),
				'toggle_slug'      => 'display_settings',
				'computed_affects' => [ '__view_content' ],
			],
			'offset'          => [
				'label'            => self::get_builder_integration()->get_field_label( 'offset' ),
				'type'             => 'range',
				'option_category'  => 'basic_option',
				'default'          => '0',
				'range_settings'   => [
					'min'  => '0',
					'max'  => '',
					'step' => '1',
				],
				'unitless'         => true,
				'description'      => self::get_builder_integration()->get_field_description( 'offset' ),
				'toggle_slug'      => 'display_settings',
				'computed_affects' => [ '__view_content' ],
			],
			'class'           => [
				'label'            => self::get_builder_integration()->get_field_label( 'class' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'default'          => '',
				'description'      => self::get_builder_integration()->get_field_description( 'class' ),
				'toggle_slug'      => 'display_settings',
				'computed_affects' => [ '__view_content' ],
			],

			// Sorting.
			// Provides the full cross-form options list — Divi's React-based
			// select doesn't expose a JS API to filter options after the
			// view changes, so per-View filtering isn't feasible here. Form
			// names are appended to each label when multiple forms exist
			// (handled inside get_sort_field_options) so users can disambiguate.
			'sort_field'      => [
				'label'            => self::get_builder_integration()->get_field_label( 'sort_field' ),
				'type'             => 'select',
				'option_category'  => 'basic_option',
				'options'          => self::get_builder_integration()->get_sort_field_options(),
				'default'          => '',
				'description'      => self::get_builder_integration()->get_field_description( 'sort_field' ),
				'toggle_slug'      => 'sorting',
				'computed_affects' => [ '__view_content' ],
			],
			'sort_direction'  => [
				'label'            => self::get_builder_integration()->get_field_label( 'sort_direction' ),
				'type'             => 'select',
				'option_category'  => 'basic_option',
				'options'          => array_merge(
					[ '' => esc_html__( 'Default', 'gk-gravityview' ) ],
					self::get_builder_integration()->get_sort_direction_options()
				),
				'default'          => '',
				'description'      => self::get_builder_integration()->get_field_description( 'sort_direction' ),
				'toggle_slug'      => 'sorting',
				'computed_affects' => [ '__view_content' ],
			],

			// Search & Filtering.
			'search_field'    => [
				'label'            => self::get_builder_integration()->get_field_label( 'search_field' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'default'          => '',
				'description'      => self::get_builder_integration()->get_field_description( 'search_field' ),
				'toggle_slug'      => 'filtering',
				'computed_affects' => [ '__view_content' ],
			],
			'search_value'    => [
				'label'            => self::get_builder_integration()->get_field_label( 'search_value' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'default'          => '',
				'description'      => self::get_builder_integration()->get_field_description( 'search_value' ),
				'toggle_slug'      => 'filtering',
				'computed_affects' => [ '__view_content' ],
			],
			'search_operator' => [
				'label'            => self::get_builder_integration()->get_field_label( 'search_operator' ),
				'type'             => 'select',
				'option_category'  => 'basic_option',
				'default'          => 'contains',
				'options'          => self::get_builder_integration()->get_search_operator_options(),
				'toggle_slug'      => 'filtering',
				'computed_affects' => [ '__view_content' ],
			],

			// Date Filtering.
			'start_date'      => [
				'label'            => self::get_builder_integration()->get_field_label( 'start_date' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'default'          => '',
				'description'      => self::get_builder_integration()->get_field_description( 'start_date' ),
				'toggle_slug'      => 'date_filtering',
				'computed_affects' => [ '__view_content' ],
			],
			'end_date'        => [
				'label'            => self::get_builder_integration()->get_field_label( 'end_date' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'default'          => '',
				'description'      => self::get_builder_integration()->get_field_description( 'end_date' ),
				'toggle_slug'      => 'date_filtering',
				'computed_affects' => [ '__view_content' ],
			],

			// Single Entry Settings.
			'single_title'    => [
				'label'            => self::get_builder_integration()->get_field_label( 'single_title' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'default'          => '',
				'description'      => self::get_builder_integration()->get_field_description( 'single_title' ),
				'toggle_slug'      => 'single_entry',
				'computed_affects' => [ '__view_content' ],
			],
			'back_link_label' => [
				'label'            => self::get_builder_integration()->get_field_label( 'back_link_label' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'default'          => '',
				'description'      => self::get_builder_integration()->get_field_description( 'back_link_label' ),
				'toggle_slug'      => 'single_entry',
				'computed_affects' => [ '__view_content' ],
			],

			// Advanced.
			'post_id'         => [
				'label'            => self::get_builder_integration()->get_field_label( 'post_id' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'default'          => '',
				'description'      => self::get_builder_integration()->get_field_description( 'post_id' ),
				'toggle_slug'      => 'advanced',
				'computed_affects' => [ '__view_content' ],
			],

			// Computed field.
			'__view_content'  => [
				'type'                => 'computed',
				'computed_callback'   => [ self::class, 'render_view_content' ],
				'computed_depends_on' => [
					'view_id',
					'page_size',
					'sort_field',
					'sort_direction',
					'search_field',
					'search_value',
					'search_operator',
					'start_date',
					'end_date',
					'offset',
					'class',
					'single_title',
					'back_link_label',
					'post_id',
				],
			],
		];

		return $fields;
	}

	/** @inheritDoc */
	protected static function get_block_type() {
		return 'view';
	}

	/**
	 * Render the module output.
	 *
	 * Wraps the View output in a container div for styling.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $attrs       Module attributes.
	 * @param string $content     Module content.
	 * @param string $render_slug Module render slug.
	 *
	 * @return string Module HTML output.
	 */
	public function render( $attrs, $content, $render_slug ) {
		$output = $this->render_module();

		if ( empty( $output ) ) {
			return $output;
		}

		return sprintf( '<div class="gk-gravityview-divi-module">%s</div>', $output );
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
