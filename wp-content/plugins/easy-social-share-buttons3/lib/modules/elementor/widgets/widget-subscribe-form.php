<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ESSB_Elementor_Subscribe_Form_Widget extends Widget_Base {
	public function get_name() {
		return 'easy-subscribe';
	}
	
	public function get_title() {
		return __( 'Subscribe Form', 'essb' );
	}
	
	public function get_icon() {
		return 'fa fa-envelope-o';
	}
	
	public function get_categories() {
		return [ 'essb' ];
	}
	
	protected function _register_controls() {
		
		$this->start_controls_section(
			'section_my_custom',
			array(
				'label' => esc_html__( 'Subscribe Form Design', 'essb' ),
			)
		);
		
		$this->add_control(
				'template',
				[
				'label' => __( 'Template', 'essb' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => essb_optin_designs()
				]
		);		
		
		
		$this->end_controls_section();
	}
	
	protected function render( $instance = array() ) {
		$settings = $this->get_settings_for_display();
		
		$template = ! empty( $settings['template'] ) ? $settings['template'] : '';
		
		if (!class_exists('ESSBNetworks_Subscribe')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-subscribe.php');
		}
		echo \ESSBNetworks_Subscribe::draw_inline_subscribe_form('inline', $template, false, 'elementor_widget');
	}
	
	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}
}
