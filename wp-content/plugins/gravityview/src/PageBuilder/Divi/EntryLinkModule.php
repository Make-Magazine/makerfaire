<?php
/**
 * GravityView Entry Link Module for Divi Builder
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
 * GravityView Entry Link Module for Divi Builder.
 *
 * Displays a link to a GravityView entry.
 *
 * @since 3.0.0
 */
class EntryLinkModule extends \ET_Builder_Module {

	use DiviModuleTrait;

	/**
	 * Module slug.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	public $slug = 'gk_gravityview_entry_link';

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
		return 'entry-link';
	}

	/**
	 * Initialize the module.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function init() {
		$metadata        = self::get_builder_integration()->get_block_types_metadata()['entry-link'] ?? [];
		$this->name      = $metadata['title'] ?? '';
		$this->icon_path = self::get_builder_integration()->get_block_type_icon_path( 'entry-link' );

		$this->settings_modal_toggles = [
			'general' => [
				'toggles' => [
					'view_selection' => esc_html__( 'Entry Selection', 'gk-gravityview' ),
					'link_settings'  => esc_html__( 'Link Settings', 'gk-gravityview' ),
					'advanced'       => esc_html__( 'Advanced', 'gk-gravityview' ),
				],
			],
		];

		$this->main_css_element = '%%order_class%%';

		// render() wraps the output in an `et_pb_module %%slug%%_N`
		// container so Divi's cached CSS (generated from fonts → link)
		// can actually target our anchor. Without the wrapper, Divi still
		// emits the rule but nothing in the DOM matches it.
		$this->advanced_fields = [
			'background'     => false,
			'borders'        => false,
			'box_shadow'     => false,
			'margin_padding' => [
				'css' => [
					'important' => 'all',
				],
			],
			'fonts'          => [
				'link' => [
					'label'           => esc_html__( 'Link', 'gk-gravityview' ),
					'css'             => [
						'main'      => '%%order_class%% a',
						// Without 'important', Divi only marks text-color
						// as `!important` (hardcoded). font-weight, font-style,
						// text-transform, and font-size all lose to the theme's
						// default `a` styling. Force `!important` on all so the
						// user's link settings actually take effect.
						'important' => 'all',
					],
					'hide_text_align' => true,
				],
			],
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
		$fields_meta = self::get_builder_integration()->get_block_type_field_metadata( 'entry-link' );

		return [
			// Entry Selection.
			'view_id'        => [
				'label'            => $fields_meta['viewId']['label'] ?? '',
				'type'             => 'select',
				'option_category'  => 'basic_option',
				'options'          => $views_list,
				'default'          => '',
				'searchable'       => true,
				'description'      => $fields_meta['viewId']['description'] ?? '',
				'toggle_slug'      => 'view_selection',
				'computed_affects' => [ '__view_content' ],
			],
			'entry_id'       => [
				'label'            => $fields_meta['entryId']['label'] ?? '',
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'default'          => '',
				'description'      => $fields_meta['entryId']['description'] ?? '',
				'toggle_slug'      => 'view_selection',
				'computed_affects' => [ '__view_content' ],
			],

			// Link Settings.
			'action'         => [
				'label'            => $fields_meta['action']['label'] ?? '',
				'type'             => 'select',
				'option_category'  => 'basic_option',
				'options'          => $fields_meta['action']['options'] ?? [],
				'default'          => $fields_meta['action']['default'] ?? '',
				'description'      => $fields_meta['action']['description'] ?? '',
				'toggle_slug'      => 'link_settings',
				'computed_affects' => [ '__view_content' ],
			],
			'return_format'  => [
				'label'            => $fields_meta['returnFormat']['label'] ?? '',
				'type'             => 'select',
				'option_category'  => 'basic_option',
				'options'          => $fields_meta['returnFormat']['options'] ?? [],
				'default'          => $fields_meta['returnFormat']['default'] ?? '',
				'description'      => $fields_meta['returnFormat']['description'] ?? '',
				'toggle_slug'      => 'link_settings',
				'computed_affects' => [ '__view_content' ],
			],
			'content'        => [
				'label'            => $fields_meta['content']['label'] ?? '',
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'default'          => '',
				'description'      => $fields_meta['content']['description'] ?? '',
				'toggle_slug'      => 'link_settings',
				'computed_affects' => [ '__view_content' ],
			],
			'link_atts'      => [
				'label'            => $fields_meta['linkAtts']['label'] ?? '',
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'default'          => '',
				'description'      => $fields_meta['linkAtts']['description'] ?? '',
				'toggle_slug'      => 'link_settings',
				'computed_affects' => [ '__view_content' ],
			],

			// Advanced.
			'post_id'        => [
				'label'            => $fields_meta['postId']['label'] ?? '',
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'default'          => '',
				'description'      => $fields_meta['postId']['description'] ?? '',
				'toggle_slug'      => 'advanced',
				'computed_affects' => [ '__view_content' ],
			],
			'field_values'   => [
				'label'            => $fields_meta['fieldValues']['label'] ?? '',
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'default'          => '',
				'description'      => $fields_meta['fieldValues']['description'] ?? '',
				'toggle_slug'      => 'advanced',
				'computed_affects' => [ '__view_content' ],
			],

			// Computed field.
			'__view_content' => [
				'type'                => 'computed',
				'computed_callback'   => [ self::class, 'render_view_content' ],
				'computed_depends_on' => [
					'view_id',
					'entry_id',
					'action',
					'return_format',
					'content',
					'link_atts',
					'post_id',
					'field_values',
				],
			],
		];
	}

	/**
	 * Wrap render output in Divi's module classes so `fonts → link` CSS applies.
	 *
	 * Divi does not auto-wrap modules declared with `vb_support = 'partial'`
	 * in an `et_pb_module %%order_class%%` container on the frontend, so the
	 * `%%order_class%% a` selector Divi emits into its cached CSS matches
	 * nothing and link style controls (color, size, font, etc.) silently
	 * no-op. Adding the wrapper ourselves — with the bare slug-based order
	 * class Divi expects (no `et_pb_` prefix on the order class itself) —
	 * hands the rendered anchor over to Divi's existing style pipeline
	 * instead of reinventing it.
	 *
	 * @inheritDoc
	 */
	public function render( $attrs, $content, $render_slug ) {
		$output = $this->render_module();

		if ( '' === $output ) {
			return $output;
		}

		$order_class = $this->slug . '_' . $this->render_count();

		return sprintf(
			'<div class="et_pb_module %s %s">%s</div>',
			esc_attr( $this->slug ),
			esc_attr( $order_class ),
			$output
		);
	}

	/**
	 * Computed callback for Visual Builder.
	 *
	 * Wraps the rendered anchor in the same `et_pb_module {slug}_{N}`
	 * container that frontend render() emits, so Divi's link CSS can
	 * actually target the anchor in the VB preview too. Without the
	 * wrapper, the cached `.gk_gravityview_entry_link_0 a` rule has
	 * no element to apply to and the user sees zero style updates
	 * until they save+exit the editor.
	 *
	 * @since 3.0.0
	 *
	 * @param array $props Module properties.
	 *
	 * @return array{content: string, styles: array, scripts: array}
	 */
	public static function render_view_content( $props ) {
		$result = self::render_computed( $props );

		if ( empty( $result['content'] ) ) {
			return $result;
		}

		// Use a fixed instance index for VB previews. Divi only renders
		// one preview at a time per module instance, and the parent VB
		// already keys CSS rules to the module's order class via its
		// own ID tracking, so 0 is safe and matches what the cached
		// CSS rule targets.
		$slug              = 'gk_gravityview_entry_link';
		$result['content'] = sprintf(
			'<div class="et_pb_module %s %s_0">%s</div>',
			esc_attr( $slug ),
			esc_attr( $slug ),
			$result['content']
		);

		return $result;
	}
}
