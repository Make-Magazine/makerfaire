<?php
/**
 * Base Page Builder Integration - Shared field definitions and utilities.
 *
 * Provides consistent field metadata, attribute mapping, and rendering logic
 * for ALL page builder integrations: Gutenberg, Beaver Builder, Divi, Elementor.
 *
 * @package GravityKit\GravityView\PageBuilder
 * @since 3.0.0
 */

namespace GravityKit\GravityView\PageBuilder;

use GV\View_Settings;
use GravityKit\GravityView\Shortcode\ShortcodeRenderer;
use GVCommon;

/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

/**
 * Base integration class for all page builders.
 *
 * Provides shared field definitions and utility methods for consistent
 * behavior across Gutenberg, Beaver Builder, Divi, and Elementor integrations.
 *
 * Each page builder should extend this class to provide builder-specific
 * overrides and transformations.
 *
 * @since 3.0.0
 */
class PageBuilder {

	/**
	 * Builder identifier.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	protected $builder = 'generic';

	/**
	 * Get field label.
	 *
	 * Tries View_Settings first (source of truth), falls back to page-builder metadata.
	 * Can be overridden by child classes for builder-specific transformations.
	 *
	 * @since 3.0.0
	 *
	 * @param string $setting_key The setting key.
	 *
	 * @return string The field label.
	 */
	public function get_field_label( $setting_key ) {
		$view_settings = View_Settings::defaults( true );

		if ( ! empty( $view_settings[ $setting_key ]['label'] ) ) {
			return $view_settings[ $setting_key ]['label'];
		}

		$metadata = $this->get_field_metadata();
		return $metadata[ $setting_key ]['label'] ?? '';
	}

	/**
	 * Get field description.
	 *
	 * Tries View_Settings first (source of truth), falls back to page-builder metadata.
	 *
	 * @since 3.0.0
	 *
	 * @param string $setting_key The setting key.
	 *
	 * @return string The field description.
	 */
	public function get_field_description( $setting_key ) {
		$view_settings = View_Settings::defaults( true );

		// View_Settings uses 'desc' or 'tooltip'.
		if ( ! empty( $view_settings[ $setting_key ]['desc'] ) ) {
			return $view_settings[ $setting_key ]['desc'];
		}

		if ( ! empty( $view_settings[ $setting_key ]['tooltip'] ) ) {
			return $view_settings[ $setting_key ]['tooltip'];
		}

		$metadata = $this->get_field_metadata();
		return $metadata[ $setting_key ]['description'] ?? '';
	}

	/**
	 * Get field options (for select/radio fields).
	 *
	 * Tries View_Settings first (source of truth), falls back to page-builder metadata.
	 *
	 * @since 3.0.0
	 *
	 * @param string $setting_key The setting key.
	 *
	 * @return array Options array (value => label).
	 */
	public function get_field_options( $setting_key ) {
		$view_settings = View_Settings::defaults( true );

		if ( ! empty( $view_settings[ $setting_key ]['options'] ) ) {
			return $view_settings[ $setting_key ]['options'];
		}

		$metadata = $this->get_field_metadata();
		return $metadata[ $setting_key ]['options'] ?? [];
	}

	/**
	 * Get field placeholder text.
	 *
	 * @since 3.0.0
	 *
	 * @param string $setting_key The setting key.
	 *
	 * @return string The placeholder text.
	 */
	public function get_field_placeholder( $setting_key ) {
		$metadata = $this->get_field_metadata();
		return $metadata[ $setting_key ]['placeholder'] ?? '';
	}

	/**
	 * Get field type for the page builder.
	 *
	 * Returns the appropriate field type based on the builder's type system.
	 * Falls back to a sensible default if the field is not defined.
	 * Can be overridden by child classes to provide builder-specific type mapping.
	 *
	 * @since 3.0.0
	 *
	 * @param string $setting_key The setting key.
	 *
	 * @return string The field type for this builder.
	 */
	public function get_field_type( $setting_key ) {
		$metadata = $this->get_field_metadata();

		// Get generic type from metadata.
		$generic_type = $metadata[ $setting_key ]['type'] ?? 'text';

		// Map to builder-specific type (can be overridden by child classes).
		return $this->map_field_type( $generic_type );
	}

	/**
	 * Get field default value.
	 *
	 * @since 3.0.0
	 *
	 * @param string $setting_key The setting key.
	 *
	 * @return mixed The default value (string, int, array, etc.).
	 */
	public function get_field_default( $setting_key ) {
		$metadata = $this->get_field_metadata();
		return $metadata[ $setting_key ]['default'] ?? '';
	}

	/**
	 * Map generic field type to builder-specific type.
	 *
	 * Handles cases where builders use different type names for the same concept.
	 * Can be overridden by child classes to provide builder-specific mappings.
	 *
	 * @since 3.0.0
	 *
	 * @param string $generic_type Generic type: 'text', 'number', 'select', etc.
	 *
	 * @return string Builder-specific type.
	 */
	protected function map_field_type( $generic_type ) {
		// Base implementation returns generic type unchanged.
		// Child classes should override this method with builder-specific mappings.
		return $generic_type;
	}

	/**
	 * Get sort direction options (ASC/DESC/RAND).
	 *
	 * @since 3.0.0
	 *
	 * @return array Sort direction options.
	 */
	public function get_sort_direction_options() {
		$options = $this->get_field_options( 'sort_direction' );

		if ( empty( $options ) ) {
			return [
				'ASC'  => esc_html__( 'Ascending', 'gk-gravityview' ),
				'DESC' => esc_html__( 'Descending', 'gk-gravityview' ),
				'RAND' => esc_html__( 'Random', 'gk-gravityview' ),
			];
		}

		return $options;
	}

	/**
	 * Get sort field options.
	 *
	 * Builds a list of sortable fields from all published Views' forms,
	 * including standard Gravity Forms meta fields that are always available.
	 *
	 * @since 3.0.0
	 *
	 * @return array Sort field options (value => label).
	 */
	public function get_sort_field_options() {
		static $cached;

		if ( null !== $cached ) {
			return $cached;
		}

		$options = [ '' => esc_html__( 'Default', 'gk-gravityview' ) ];

		$views    = GVCommon::get_all_views();
		$form_ids = [];
		foreach ( $views as $v ) {
			$fid = get_post_meta( $v->ID, '_gravityview_form_id', true );
			if ( $fid ) {
				$form_ids[] = (int) $fid;
			}
		}
		$form_ids       = array_unique( $form_ids );
		$multiple_forms = count( $form_ids ) > 1;

		foreach ( $form_ids as $form_id ) {
			$fields = GVCommon::get_sortable_fields_array( $form_id );

			$form_label = '';
			if ( $multiple_forms ) {
				$form       = \GFAPI::get_form( $form_id );
				$form_label = $form ? sprintf( ' (%s)', $form['title'] ) : '';
			}

			foreach ( $fields as $field_id => $field ) {
				$key = (string) $field_id;

				if ( isset( $options[ $key ] ) ) {
					continue;
				}

				$options[ $key ] = esc_html( $field['label'] . $form_label );
			}
		}

		$cached = $options;

		return $options;
	}

	/**
	 * Get sort field options for a specific View.
	 *
	 * Returns sortable fields from the View's connected form only,
	 * as an array of {value, label} objects suitable for JS consumption.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View post ID.
	 *
	 * @return array<array{value: string, label: string}> Sort field options.
	 */
	public function get_sort_field_options_for_view( $view_id ) {
		$form_id = (int) get_post_meta( $view_id, '_gravityview_form_id', true );

		if ( empty( $form_id ) ) {
			return [];
		}

		$fields  = GVCommon::get_sortable_fields_array( $form_id );
		$options = [];

		foreach ( $fields as $field_id => $field ) {
			$options[] = [
				'value' => (string) $field_id,
				'label' => $field['label'],
			];
		}

		return $options;
	}

	/**
	 * Get search operator options.
	 *
	 * @since 3.0.0
	 *
	 * @return array Search operator options.
	 */
	public function get_search_operator_options() {
		return $this->get_field_options( 'search_operator' );
	}

	/**
	 * Get page-builder-specific field metadata.
	 *
	 * This array provides ONLY:
	 * 1. Placeholders (not available in View_Settings)
	 * 2. Fields not in View_Settings (like post_id)
	 * 3. Page-builder-specific overrides when needed
	 *
	 * For labels, descriptions, and options, we pull from View_Settings first
	 * to maintain a single source of truth and avoid string duplication.
	 *
	 * Can be overridden by child classes for builder-specific metadata.
	 *
	 * @since 3.0.0
	 *
	 * @return array Field metadata keyed by setting name.
	 */
	protected function get_field_metadata() {
		return [
			// Text fields with placeholders.
			'sort_field'      => [
				'type'        => 'text',
				'default'     => '',
				'placeholder' => esc_html__( 'Field ID to sort by', 'gk-gravityview' ),
			],
			'search_field'    => [
				'type'        => 'text',
				'default'     => '',
				'placeholder' => esc_html__( 'Field ID to search in', 'gk-gravityview' ),
			],
			'search_value'    => [
				'type'        => 'text',
				'default'     => '',
				'placeholder' => esc_html__( 'Value to search for', 'gk-gravityview' ),
			],
			'search_operator' => [
				'type'    => 'select',
				'default' => 'contains',
				// View_Settings uses type 'operator' without options array.
				// Provide the options here for page builders.
				'options' => [
					'is'          => esc_html__( 'Is', 'gk-gravityview' ),
					'isnot'       => esc_html__( 'Is Not', 'gk-gravityview' ),
					'<>'          => esc_html__( 'Not Equal', 'gk-gravityview' ),
					'not in'      => esc_html__( 'Not In', 'gk-gravityview' ),
					'in'          => esc_html__( 'In', 'gk-gravityview' ),
					'>'           => esc_html__( 'Greater', 'gk-gravityview' ),
					'<'           => esc_html__( 'Lesser', 'gk-gravityview' ),
					'contains'    => esc_html__( 'Contains', 'gk-gravityview' ),
					'starts_with' => esc_html__( 'Starts With', 'gk-gravityview' ),
					'ends_with'   => esc_html__( 'Ends With', 'gk-gravityview' ),
					'like'        => esc_html__( 'Like', 'gk-gravityview' ),
					'>='          => esc_html__( 'Greater Or Equal', 'gk-gravityview' ),
					'<='          => esc_html__( 'Lesser Or Equal', 'gk-gravityview' ),
				],
			],
			'start_date'      => [
				'type'        => 'text',
				'default'     => '',
				'placeholder' => esc_html__( 'YYYY-MM-DD or relative date', 'gk-gravityview' ),
			],
			'end_date'        => [
				'type'        => 'text',
				'default'     => '',
				'placeholder' => esc_html__( 'YYYY-MM-DD or relative date', 'gk-gravityview' ),
			],
			'page_size'       => [
				'type'        => 'number',
				'default'     => '',
				'placeholder' => esc_html__( 'Leave empty to use View settings', 'gk-gravityview' ),
			],
			'offset'          => [
				'type'        => 'number',
				'default'     => 0,
				'placeholder' => esc_html__( '0', 'gk-gravityview' ),
			],
			'class'           => [
				'type'        => 'text',
				'default'     => '',
				'placeholder' => esc_html__( 'Custom CSS class', 'gk-gravityview' ),
			],
			'single_title'    => [
				'type'        => 'text',
				'default'     => '',
				'placeholder' => esc_html__( 'Leave empty to use View settings', 'gk-gravityview' ),
			],
			'back_link_label' => [
				'type'        => 'text',
				'default'     => '',
				'placeholder' => esc_html__( 'Leave empty to use View settings', 'gk-gravityview' ),
			],
			'sort_direction'  => [
				'type'    => 'select',
				'default' => '',
			],
			// Fields not in View_Settings.
			'post_id'         => [
				'type'        => 'number',
				'default'     => '',
				'label'       => esc_html__( 'Post ID', 'gk-gravityview' ),
				'description' => esc_html__( 'Override the post/page ID used for View context.', 'gk-gravityview' ),
				'placeholder' => esc_html__( 'Leave empty to use current post', 'gk-gravityview' ),
			],
		];
	}

	/**
	 * Get list of published Views for select controls.
	 *
	 * Returns default select format (associative array with ID => label).
	 * Can be overridden by child classes for builder-specific formats.
	 *
	 * @since 3.0.0
	 *
	 * @param string $default_label Default option label.
	 *
	 * @return array Views list in builder-specific format.
	 */
	public function get_views_list( $default_label = '' ) {
		if ( ! class_exists( 'GVCommon' ) ) {
			return [ '' => esc_html__( '-- No Views Found --', 'gk-gravityview' ) ];
		}

		$views = GVCommon::get_all_views();

		if ( empty( $views ) ) {
			return [ '' => esc_html__( '-- No Views Found --', 'gk-gravityview' ) ];
		}

		if ( empty( $default_label ) ) {
			$default_label = esc_html__( '-- Select a View --', 'gk-gravityview' );
		} else {
			$default_label = esc_html( $default_label );
		}

		$views_list = [];
		foreach ( $views as $view_post ) {
			$views_list[ (string) $view_post->ID ] = esc_html(
				// translators: %1$s is the View title, %2$d is the View ID.
				sprintf( __( '%1$s (View #%2$d)', 'gk-gravityview' ), $view_post->post_title, $view_post->ID )
			);
		}

		return [ '' => $default_label ] + $views_list;
	}

	/**
	 * Map builder-specific settings to camelCase block attributes.
	 *
	 * @since 3.0.0
	 *
	 * @param array|object $settings Builder settings.
	 * @param array        $mapping  Mapping (builder_key => camelCase_key).
	 *
	 * @return array camelCase block attributes.
	 */
	public function map_to_block_atts( $settings, $mapping ) {
		$block_atts = [];

		foreach ( $mapping as $builder_key => $block_key ) {
			// Handle both arrays and objects (Beaver Builder uses objects).
			$value = is_array( $settings )
				? ( $settings[ $builder_key ] ?? '' )
				: ( $settings->$builder_key ?? '' );

			if ( '' !== $value && null !== $value ) {
				$block_atts[ $block_key ] = $value;
			}
		}

		return $block_atts;
	}

	/**
	 * Build shortcode from settings and view.
	 *
	 * Uses the shared ShortcodeRenderer for consistency across
	 * all page builder integrations.
	 *
	 * @since 3.0.0
	 *
	 * @param array|object $settings Builder settings.
	 * @param \GV\View     $view     View object.
	 * @param array        $mapping  Mapping (builder_key => camelCase_key).
	 *
	 * @return string Formatted shortcode.
	 */
	public function build_shortcode( $settings, $view, $mapping ) {
		if ( ! $view instanceof \GV\View ) {
			return '';
		}

		$block_atts = $this->map_to_block_atts( $settings, $mapping );

		return ShortcodeRenderer::build_from_block_atts(
			$block_atts,
			$view->get_validation_secret()
		);
	}

	/**
	 * Get edit View URL.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View ID.
	 *
	 * @return string Edit URL.
	 */
	public function get_edit_view_url( $view_id ) {
		return admin_url( 'post.php?post=' . absint( $view_id ) . '&action=edit' );
	}

	/**
	 * Get render options for builder preview mode.
	 *
	 * @since 3.0.0
	 *
	 * @return array Render options with asset filtering.
	 */
	public function get_preview_render_options() {
		return [
			'allowed_style_patterns'  => ShortcodeRenderer::ALLOWLIST_HANDLE_PATTERNS,
			'allowed_script_patterns' => ShortcodeRenderer::ALLOWLIST_HANDLE_PATTERNS,
			'ignored_script_handles'  => ShortcodeRenderer::BUILDER_IGNORED_SCRIPT_HANDLES,
		];
	}

	/**
	 * Get standardized field groups for organizing page builder controls.
	 *
	 * Each group contains related settings for better UX in page builders.
	 *
	 * @since 3.0.0
	 *
	 * @return array Field groups with keys and labels.
	 */
	public function get_field_groups() {
		return [
			'view_selection'    => [
				'label'  => esc_html__( 'View Selection', 'gk-gravityview' ),
				'fields' => [ 'viewId' ],
			],
			'display_settings'  => [
				'label'  => esc_html__( 'Display Settings', 'gk-gravityview' ),
				'fields' => [ 'pageSize', 'offset', 'classValue' ],
			],
			'sorting'           => [
				'label'  => esc_html__( 'Sorting', 'gk-gravityview' ),
				'fields' => [ 'sortField', 'sortDirection' ],
			],
			'filtering'         => [
				'label'  => __( 'Search & Filtering', 'gk-gravityview' ),
				'fields' => [ 'searchField', 'searchValue', 'searchOperator' ],
			],
			'date_filtering'    => [
				'label'  => esc_html__( 'Date Filtering', 'gk-gravityview' ),
				'fields' => [ 'startDate', 'endDate' ],
			],
			'single_entry'      => [
				'label'  => esc_html__( 'Single Entry Settings', 'gk-gravityview' ),
				'fields' => [ 'singleTitle', 'backLinkLabel' ],
			],
			'advanced'          => [
				'label'  => esc_html__( 'Advanced', 'gk-gravityview' ),
				'fields' => [ 'postId' ],
			],
		];
	}

	/**
	 * Get metadata for all block types.
	 *
	 * Defines shortcode mappings, field groups, and configurations for each block type.
	 *
	 * @since 3.0.0
	 *
	 * @return array Block type metadata.
	 */
	public function get_block_types_metadata() {
		return [
			'view'         => [
				'shortcode'      => 'gravityview',
				'title'          => esc_html__( 'GravityView', 'gk-gravityview' ),
				'description'    => esc_html__( 'Display a GravityView View.', 'gk-gravityview' ),
				'icon'           => 'list-view',
				'attribute_map'  => [
					'viewId'         => 'id',
					'pageSize'       => 'page_size',
					'sortField'      => 'sort_field',
					'sortDirection'  => 'sort_direction',
					'searchField'    => 'search_field',
					'searchValue'    => 'search_value',
					'searchOperator' => 'search_operator',
					'startDate'      => 'start_date',
					'endDate'        => 'end_date',
					'offset'         => 'offset',
					'classValue'     => 'class',
					'singleTitle'    => 'single_title',
					'backLinkLabel'  => 'back_link_label',
					'postId'         => 'post_id',
					'secret'         => 'secret',
				],
				'required_fields' => [ 'viewId' ],
			],
			'entry'        => [
				'shortcode'       => 'gventry',
				'title'           => esc_html__( 'GravityView Entry', 'gk-gravityview' ),
				'description'     => esc_html__( 'Display a single GravityView entry.', 'gk-gravityview' ),
				'icon'            => 'admin-page',
				'attribute_map'   => [
					'viewId'  => 'view_id',
					'entryId' => 'id',
					'secret'  => 'secret',
				],
				'required_fields' => [ 'viewId', 'entryId' ],
			],
			'entry-field'  => [
				'shortcode'       => 'gvfield',
				'title'           => esc_html__( 'GravityView Entry Field', 'gk-gravityview' ),
				'description'     => esc_html__( 'Display a specific field from a GravityView entry.', 'gk-gravityview' ),
				'icon'            => 'text',
				'attribute_map'   => [
					'viewId'                => 'view_id',
					'entryId'               => 'entry_id',
					'fieldId'               => 'field_id',
					'fieldSettingOverrides' => 'field_setting_overrides',
					'secret'                => 'secret',
				],
				'required_fields' => [ 'viewId', 'entryId', 'fieldId' ],
			],
			'entry-link'   => [
				'shortcode'       => 'gv_entry_link',
				'title'           => esc_html__( 'GravityView Entry Link', 'gk-gravityview' ),
				'description'     => esc_html__( 'Display a link to a GravityView entry.', 'gk-gravityview' ),
				'icon'            => 'admin-links',
				'attribute_map'   => [
					'viewId'       => 'view_id',
					'entryId'      => 'entry_id',
					'action'       => 'action',
					'postId'       => 'post_id',
					'returnFormat' => 'return',
					'linkAtts'     => 'link_atts',
					'fieldValues'  => 'field_values',
					'content'      => 'content',
					'secret'       => 'secret',
				],
				'required_fields' => [ 'viewId', 'entryId' ],
				'supports_content' => true,
			],
			'view-details' => [
				'shortcode'       => 'gravityview',
				'title'           => esc_html__( 'GravityView View Details', 'gk-gravityview' ),
				'description'     => esc_html__( 'Display View metadata (total entries, page info, etc.).', 'gk-gravityview' ),
				'icon'            => 'info',
				'attribute_map'   => [
					'viewId' => 'id',
					'detail' => 'detail',
					'secret' => 'secret',
				],
				'required_fields' => [ 'viewId', 'detail' ],
			],
		];
	}

	/**
	 * Get the file path to a block type's SVG icon.
	 *
	 * Returns the path to the shared SVG icon file for the given block type.
	 * Icons are stored in the shared assets/icons/ directory.
	 *
	 * @since 3.0.0
	 *
	 * @param string $block_type Block type ('view', 'entry', 'entry-field', 'entry-link', 'view-details').
	 *
	 * @return string Absolute file path to the SVG icon, or empty string if not found.
	 */
	public function get_block_type_icon_path( $block_type ) {
		if ( ! isset( $this->get_block_types_metadata()[ $block_type ] ) ) {
			return '';
		}

		$icon_path = dirname( __FILE__ ) . '/assets/icons/' . $block_type . '.svg';

		if ( file_exists( $icon_path ) ) {
			return $icon_path;
		}

		return '';
	}

	/**
	 * Get the SVG content for a block type's icon.
	 *
	 * Reads the shared SVG icon file and optionally replaces `currentColor` with a specific fill color.
	 *
	 * @since 3.0.0
	 *
	 * @param string $block_type Block type ('view', 'entry', 'entry-field', 'entry-link', 'view-details').
	 * @param string $fill_color Optional fill color to replace `currentColor` (e.g., '#2b87da'). Default empty (keeps currentColor).
	 *
	 * @return string SVG content, or empty string if not found.
	 */
	public function get_block_type_icon_svg( $block_type, $fill_color = '' ) {
		$icon_path = $this->get_block_type_icon_path( $block_type );

		if ( empty( $icon_path ) ) {
			return '';
		}

		$svg = file_get_contents( $icon_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local file read.

		if ( false === $svg ) {
			return '';
		}

		if ( ! empty( $fill_color ) ) {
			$svg = str_replace( 'currentColor', esc_attr( $fill_color ), $svg );
		}

		return $svg;
	}

	/**
	 * Build shortcode for a specific block type.
	 *
	 * @since 3.0.0
	 *
	 * @param string       $block_type Block type ('view', 'entry', 'entry-field', 'entry-link', 'view-details').
	 * @param array|object $settings   Builder settings.
	 * @param \GV\View     $view       View object (for secret).
	 * @param array        $mapping    Mapping (builder_key => camelCase_key).
	 *
	 * @return string Formatted shortcode.
	 */
	public function build_block_type_shortcode( $block_type, $settings, $view, $mapping ) {
		$block_types = $this->get_block_types_metadata();

		if ( ! isset( $block_types[ $block_type ] ) ) {
			return '';
		}

		$metadata   = $block_types[ $block_type ];
		$block_atts = $this->map_to_block_atts( $settings, $mapping );

		// Add secret if View is provided.
		if ( $view instanceof \GV\View ) {
			$block_atts['secret'] = $view->get_validation_secret();
		}

		return ShortcodeRenderer::build_shortcode_from_atts(
			$metadata['shortcode'],
			$block_atts,
			$metadata['attribute_map'],
			isset( $metadata['supports_content'] ) && $metadata['supports_content']
		);
	}

	/**
	 * Get field metadata for a specific block type.
	 *
	 * @since 3.0.0
	 *
	 * @param string $block_type Block type.
	 *
	 * @return array Field metadata for the block type.
	 */
	public function get_block_type_field_metadata( $block_type ) {
		$block_types = $this->get_block_types_metadata();

		if ( ! isset( $block_types[ $block_type ] ) ) {
			return [];
		}

		$fields = [];

		// Add common fields.
		$fields['viewId'] = [
			'type'        => 'select',
			'label'       => esc_html__( 'Select View', 'gk-gravityview' ),
			'description' => esc_html__( 'Choose an existing View.', 'gk-gravityview' ),
			'required'    => true,
		];

		// Add block-type-specific fields.
		switch ( $block_type ) {
			case 'entry':
			case 'entry-field':
			case 'entry-link':
				$fields['entryId'] = [
					'type'        => 'text',
					'label'       => esc_html__( 'Entry ID', 'gk-gravityview' ),
					'description' => esc_html__( 'ID of the entry to display.', 'gk-gravityview' ),
					'placeholder' => esc_html__( 'Entry ID', 'gk-gravityview' ),
					'required'    => true,
				];
				break;
		}

		switch ( $block_type ) {
			case 'entry-field':
				$fields['fieldId'] = [
					'type'        => 'text',
					'label'       => esc_html__( 'Field ID', 'gk-gravityview' ),
					'description' => esc_html__( 'ID of the field to display.', 'gk-gravityview' ),
					'placeholder' => esc_html__( 'Field ID', 'gk-gravityview' ),
					'required'    => true,
				];
				$fields['fieldSettingOverrides'] = [
					'type'        => 'text',
					'label'       => esc_html__( 'Field Setting Overrides', 'gk-gravityview' ),
					'description' => esc_html__( 'Override field settings (advanced).', 'gk-gravityview' ),
					'placeholder' => esc_html__( 'JSON or query string format', 'gk-gravityview' ),
				];
				break;

			case 'entry-link':
				$fields['action'] = [
					'type'        => 'select',
					'label'       => esc_html__( 'Link Action', 'gk-gravityview' ),
					'description' => esc_html__( 'What type of link to generate.', 'gk-gravityview' ),
					'options'     => [
						'read'   => esc_html__( 'View Entry', 'gk-gravityview' ),
						'edit'   => esc_html__( 'Edit Entry', 'gk-gravityview' ),
						'delete' => esc_html__( 'Delete Entry', 'gk-gravityview' ),
					],
					'default'     => 'read',
				];
				$fields['returnFormat'] = [
					'type'        => 'select',
					'label'       => esc_html__( 'Return Format', 'gk-gravityview' ),
					'description' => esc_html__( 'Return HTML link or just the URL.', 'gk-gravityview' ),
					'options'     => [
						'html' => esc_html__( 'HTML Link', 'gk-gravityview' ),
						'url'  => esc_html__( 'URL Only', 'gk-gravityview' ),
					],
					'default'     => 'html',
				];
				$fields['linkAtts'] = [
					'type'        => 'text',
					'label'       => esc_html__( 'Link Attributes', 'gk-gravityview' ),
					'description' => __( 'Additional attributes for the link (e.g., target="_blank").', 'gk-gravityview' ),
					'placeholder' => __( 'class="custom" target="_blank"', 'gk-gravityview' ),
				];
				$fields['fieldValues'] = [
					'type'        => 'text',
					'label'       => esc_html__( 'Field Values', 'gk-gravityview' ),
					'description' => esc_html__( 'Pre-fill field values (for edit links).', 'gk-gravityview' ),
					'placeholder' => __( 'field_id=value&another_field=value', 'gk-gravityview' ),
				];
				$fields['content'] = [
					'type'        => 'text',
					'label'       => esc_html__( 'Link Text', 'gk-gravityview' ),
					'description' => esc_html__( 'Text to display for the link.', 'gk-gravityview' ),
					'placeholder' => esc_html__( 'View Entry', 'gk-gravityview' ),
				];
				$fields['postId'] = [
					'type'        => 'text',
					'label'       => esc_html__( 'Post ID', 'gk-gravityview' ),
					'description' => esc_html__( 'ID of the post/page containing the View.', 'gk-gravityview' ),
					'placeholder' => esc_html__( 'Leave empty to use current post', 'gk-gravityview' ),
				];
				break;

			case 'view-details':
				$fields['detail'] = [
					'type'        => 'select',
					'label'       => esc_html__( 'Detail Type', 'gk-gravityview' ),
					'description' => esc_html__( 'What View information to display.', 'gk-gravityview' ),
					'options'     => [
						'total_entries'   => esc_html__( 'Total Entries', 'gk-gravityview' ),
						'first_entry'     => esc_html__( 'First Entry Number', 'gk-gravityview' ),
						'last_entry'      => esc_html__( 'Last Entry Number', 'gk-gravityview' ),
						'page_size'       => esc_html__( 'Page Size', 'gk-gravityview' ),
					],
					'default'     => 'total_entries',
					'required'    => true,
				];
				break;
		}

		return $fields;
	}
}
