<?php
/**
 * GravityView Entry Widget for Elementor
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
 * GravityView Entry Widget for Elementor.
 *
 * Displays a single GravityView entry.
 *
 * @since 3.0.0
 */
class EntryWidget extends Widget_Base {

	use ElementorWidgetTrait;

	/**
	 * Widget type identifier.
	 *
	 * @since 3.0.0
	 */
	const WIDGET_TYPE = 'gk_elementor_gravityview_entry';

	/** @inheritDoc */
	protected static function get_block_type() {
		return 'entry';
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
		$metadata = self::get_builder_integration()->get_block_types_metadata()['entry'] ?? [];
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
		return 'gk-gravityview-icon-entry';
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
			$this->register_gravityview_not_active_notice();
			return;
		}

		$views_list = self::get_builder_integration()->get_views_list();

		if ( empty( $views_list ) || 1 === count( $views_list ) ) {
			$this->register_no_views_notice();
			return;
		}

		$fields_meta = self::get_builder_integration()->get_block_type_field_metadata( 'entry' );

		$this->start_controls_section(
			'entry_section',
			[
				'label' => esc_html__( 'Entry Settings', 'gk-gravityview' ),
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

		$this->end_controls_section();
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
