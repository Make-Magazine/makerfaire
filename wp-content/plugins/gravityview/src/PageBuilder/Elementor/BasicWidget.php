<?php
/**
 * GravityView Basic Widget for Elementor
 *
 * @package GravityKit\GravityView\PageBuilder\Elementor
 * @since 3.0.0
 */

namespace GravityKit\GravityView\PageBuilder\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use GVCommon;

/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

/**
 * GravityView Basic Widget for Elementor.
 *
 * Provides basic functionality for embedding GravityView Views in Elementor.
 * Can be extended by the Advanced Elementor Widget plugin for more features.
 *
 * @since 3.0.0
 */
class BasicWidget extends Widget_Base {

	use ElementorWidgetTrait;

	/**
	 * Widget type identifier.
	 *
	 * CRITICAL: Must match Advanced Widget's identifier for compatibility.
	 *
	 * @since 3.0.0
	 */
	const WIDGET_TYPE = 'gk_elementor_gravityview';

	/** @inheritDoc */
	protected static function get_block_type() {
		return 'view';
	}

	/**
	 * Get widget name.
	 *
	 * @since 3.0.0
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return self::WIDGET_TYPE;
	}

	/**
	 * Get widget title.
	 *
	 * @since 3.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'GravityView', 'gk-gravityview' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 3.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'gk-gravityview-icon-view';
	}

	/**
	 * Get widget categories.
	 *
	 * @since 3.0.0
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'basic' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * @since 3.0.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [
			'gravity forms',
			'gravityforms',
			'gravityview',
			'view',
			'gravity view',
		];
	}

	/**
	 * Check if widget has dynamic content.
	 *
	 * @since 3.0.0
	 *
	 * @return bool Whether widget has dynamic content.
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Register widget controls.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	protected function register_controls() {

		if ( ! class_exists( '\GV\View' ) ) {
			$this->register_gravityview_not_active_notice();
			return;
		}

		$views_list = self::get_builder_integration()->get_views_list();

		if ( empty( $views_list ) || count( $views_list ) <= 1 ) {
			$this->register_no_views_notice();
			return;
		}

		$this->register_view_selection_section( $views_list );
		$this->register_display_settings_section();
		$this->register_sorting_section();
		$this->register_filtering_section();
		$this->register_date_filtering_section();
		$this->register_single_entry_section();
		$this->register_advanced_section();
	}

	/**
	 * Register a notice when GravityView is not active.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	private function register_gravityview_not_active_notice() {
		$this->start_controls_section(
			'view_notice_section',
			[
				'label' => esc_html__( 'GravityView Not Activated', 'gk-gravityview' ),
			]
		);

		$message = sprintf(
			'%s<br><br><a href="%s">%s</a>',
			esc_html__( 'GravityView is required to use this widget.', 'gk-gravityview' ),
			esc_url( admin_url( 'plugins.php' ) ),
			esc_html__( 'Activate GravityView', 'gk-gravityview' )
		);

		$this->add_control(
			'view_notice',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => $message,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register a notice when no Views are found.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	private function register_no_views_notice() {
		$this->start_controls_section(
			'view_section',
			[
				'label' => esc_html__( 'No Views Found', 'gk-gravityview' ),
			]
		);

		$message = sprintf(
			'<p>%s <a href="%s">%s</a></p>',
			esc_html__( 'No published Views found.', 'gk-gravityview' ),
			esc_url( admin_url( 'post-new.php?post_type=gravityview' ) ),
			esc_html__( 'Create a new View', 'gk-gravityview' )
		);

		$this->add_control(
			'view_notice',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => $message,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register View selection section.
	 *
	 * @since 3.0.0
	 *
	 * @param array $views_list List of Views with ID as key and title as value.
	 *
	 * @return void
	 */
	private function register_view_selection_section( $views_list ) {
		$view_fields_meta = self::get_builder_integration()->get_block_type_field_metadata( 'view' );

		$this->start_controls_section(
			'view_section',
			[
				'label' => $view_fields_meta['viewId']['label'] ?? '',
			]
		);

		$this->add_control(
			'embedded_view',
			[
				'label'       => $view_fields_meta['viewId']['label'] ?? '',
				'type'        => Controls_Manager::SELECT2,
				'options'     => $views_list,
				'default'     => '',
				'label_block' => true,
				'description' => $view_fields_meta['viewId']['description'] ?? '',
			]
		);

		// Edit View link (shown when a View is selected).
		$this->add_control(
			'edit_view_link',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<a href="#" target="_blank" class="elementor-button elementor-button-default gk-edit-view-link" style="display:none;">' . esc_html__( 'Edit this View', 'gk-gravityview' ) . '</a>',
				'content_classes' => 'gk-edit-view-link-wrapper',
			]
		);

		// Hidden controls for layout data (required for Advanced Widget compatibility).
		$this->add_control(
			'views_layouts',
			[
				'type'    => Controls_Manager::HIDDEN,
				'default' => wp_json_encode( $this->get_views_layouts_data() ),
			]
		);

		$this->add_control(
			'layout_single',
			[
				'type'    => Controls_Manager::HIDDEN,
				'default' => '',
			]
		);

		$this->add_control(
			'layout_multiple',
			[
				'type'    => Controls_Manager::HIDDEN,
				'default' => '',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register display settings section.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	private function register_display_settings_section() {
		$this->start_controls_section(
			'display_settings_section',
			[
				'label' => esc_html__( 'Display Settings', 'gk-gravityview' ),
			]
		);

		$this->add_control(
			'page_size',
			[
				'label'       => self::get_builder_integration()->get_field_label( 'page_size' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => -1,
				'placeholder' => self::get_builder_integration()->get_field_placeholder( 'page_size' ),
				'description' => self::get_builder_integration()->get_field_description( 'page_size' ),
			]
		);

		$this->add_control(
			'offset',
			[
				'label'       => self::get_builder_integration()->get_field_label( 'offset' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'placeholder' => self::get_builder_integration()->get_field_placeholder( 'offset' ),
				'description' => self::get_builder_integration()->get_field_description( 'offset' ),
			]
		);

		$this->add_control(
			'class_value',
			[
				'label'       => self::get_builder_integration()->get_field_label( 'class' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => self::get_builder_integration()->get_field_placeholder( 'class' ),
				'description' => self::get_builder_integration()->get_field_description( 'class' ),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register sorting section.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	private function register_sorting_section() {
		$this->start_controls_section(
			'sorting_section',
			[
				'label' => esc_html__( 'Sorting', 'gk-gravityview' ),
			]
		);

		$this->add_control(
			'sort_field',
			[
				'label'       => self::get_builder_integration()->get_field_label( 'sort_field' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => self::get_builder_integration()->get_field_placeholder( 'sort_field' ),
				'description' => self::get_builder_integration()->get_field_description( 'sort_field' ),
			]
		);

		$this->add_control(
			'sort_direction',
			[
				'label'       => self::get_builder_integration()->get_field_label( 'sort_direction' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array_merge(
					[ '' => esc_html__( 'Default', 'gk-gravityview' ) ],
					self::get_builder_integration()->get_sort_direction_options()
				),
				'description' => self::get_builder_integration()->get_field_description( 'sort_direction' ),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register filtering section.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	private function register_filtering_section() {
		$this->start_controls_section(
			'filtering_section',
			[
				'label' => esc_html__( 'Search & Filtering', 'gk-gravityview' ),
			]
		);

		$this->add_control(
			'search_field',
			[
				'label'       => self::get_builder_integration()->get_field_label( 'search_field' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => self::get_builder_integration()->get_field_placeholder( 'search_field' ),
				'description' => self::get_builder_integration()->get_field_description( 'search_field' ),
			]
		);

		$this->add_control(
			'search_value',
			[
				'label'       => self::get_builder_integration()->get_field_label( 'search_value' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => self::get_builder_integration()->get_field_placeholder( 'search_value' ),
				'description' => self::get_builder_integration()->get_field_description( 'search_value' ),
			]
		);

		$this->add_control(
			'search_operator',
			[
				'label'   => self::get_builder_integration()->get_field_label( 'search_operator' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'contains',
				'options' => self::get_builder_integration()->get_search_operator_options(),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register date filtering section.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	private function register_date_filtering_section() {
		$this->start_controls_section(
			'date_filtering_section',
			[
				'label' => esc_html__( 'Date Filtering', 'gk-gravityview' ),
			]
		);

		$this->add_control(
			'start_date',
			[
				'label'       => self::get_builder_integration()->get_field_label( 'start_date' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => self::get_builder_integration()->get_field_placeholder( 'start_date' ),
				'description' => self::get_builder_integration()->get_field_description( 'start_date' ),
			]
		);

		$this->add_control(
			'end_date',
			[
				'label'       => self::get_builder_integration()->get_field_label( 'end_date' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => self::get_builder_integration()->get_field_placeholder( 'end_date' ),
				'description' => self::get_builder_integration()->get_field_description( 'end_date' ),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register single entry settings section.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	private function register_single_entry_section() {
		$this->start_controls_section(
			'single_entry_section',
			[
				'label' => esc_html__( 'Single Entry Settings', 'gk-gravityview' ),
			]
		);

		$this->add_control(
			'single_title',
			[
				'label'       => self::get_builder_integration()->get_field_label( 'single_title' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => self::get_builder_integration()->get_field_placeholder( 'single_title' ),
				'description' => self::get_builder_integration()->get_field_description( 'single_title' ),
			]
		);

		$this->add_control(
			'back_link_label',
			[
				'label'       => self::get_builder_integration()->get_field_label( 'back_link_label' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => self::get_builder_integration()->get_field_placeholder( 'back_link_label' ),
				'description' => self::get_builder_integration()->get_field_description( 'back_link_label' ),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register advanced settings section.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	private function register_advanced_section() {
		$this->start_controls_section(
			'advanced_section',
			[
				'label' => esc_html__( 'Advanced', 'gk-gravityview' ),
			]
		);

		$this->add_control(
			'post_id',
			[
				'label'       => self::get_builder_integration()->get_field_label( 'post_id' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 0,
				'placeholder' => self::get_builder_integration()->get_field_placeholder( 'post_id' ),
				'description' => self::get_builder_integration()->get_field_description( 'post_id' ),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	protected function render() {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in render_module/ShortcodeRenderer.
		echo $this->render_module();
	}

	/**
	 * Get Views layouts data for Advanced Widget compatibility.
	 *
	 * @since 3.0.0
	 *
	 * @return array Array of View layouts indexed by View ID.
	 */
	private function get_views_layouts_data() {
		$views = GVCommon::get_all_views();

		if ( empty( $views ) ) {
			return [];
		}

		$layouts = [];

		foreach ( $views as $view_post ) {
			$layouts[ $view_post->ID ] = [
				'single'   => get_post_meta( $view_post->ID, '_gravityview_directory_template_single', true ) ?: '',
				'multiple' => get_post_meta( $view_post->ID, '_gravityview_directory_template', true ) ?: '',
			];
		}

		return $layouts;
	}
}
