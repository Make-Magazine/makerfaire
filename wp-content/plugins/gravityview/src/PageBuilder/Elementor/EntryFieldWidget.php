<?php
/**
 * GravityView Entry Field Widget for Elementor
 *
 * @package GravityKit\GravityView\PageBuilder\Elementor
 * @since 3.0.0
 */

namespace GravityKit\GravityView\PageBuilder\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

/**
 * GravityView Entry Field Widget for Elementor.
 *
 * Displays a specific field from a GravityView entry.
 *
 * @since 3.0.0
 */
class EntryFieldWidget extends Widget_Base {

	use ElementorWidgetTrait;

	/**
	 * Widget type identifier.
	 *
	 * @since 3.0.0
	 */
	const WIDGET_TYPE = 'gk_elementor_gravityview_entry_field';

	/** @inheritDoc */
	protected static function get_block_type() {
		return 'entry-field';
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
		$metadata = self::get_builder_integration()->get_block_types_metadata()['entry-field'] ?? [];
		return $metadata['title'] ?? '';
	}

	/**
	 * Get widget icon.
	 *
	 * @since 3.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'gk-gravityview-icon-entry-field';
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
			'field',
			'entry',
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
			return;
		}

		$views_list  = self::get_builder_integration()->get_views_list();
		$fields_meta = self::get_builder_integration()->get_block_type_field_metadata( 'entry-field' );

		$this->start_controls_section(
			'entry_field_section',
			[
				'label' => esc_html__( 'Entry Field Settings', 'gk-gravityview' ),
			]
		);

		$this->add_control(
			'view_id',
			[
				'label'       => $fields_meta['viewId']['label'] ?? '',
				'type'        => Controls_Manager::SELECT2,
				'options'     => $views_list,
				'default'     => '',
				'label_block' => true,
				'description' => $fields_meta['viewId']['description'] ?? '',
			]
		);

		$this->add_control(
			'entry_id',
			[
				'label'       => $fields_meta['entryId']['label'] ?? '',
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => $fields_meta['entryId']['placeholder'] ?? '',
				'description' => $fields_meta['entryId']['description'] ?? '',
			]
		);

		$this->add_control(
			'field_id',
			[
				'label'       => $fields_meta['fieldId']['label'] ?? '',
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => $fields_meta['fieldId']['placeholder'] ?? '',
				'description' => $fields_meta['fieldId']['description'] ?? '',
			]
		);

		$this->add_control(
			'field_setting_overrides',
			[
				'label'       => $fields_meta['fieldSettingOverrides']['label'] ?? '',
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => $fields_meta['fieldSettingOverrides']['placeholder'] ?? '',
				'description' => $fields_meta['fieldSettingOverrides']['description'] ?? '',
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
}
