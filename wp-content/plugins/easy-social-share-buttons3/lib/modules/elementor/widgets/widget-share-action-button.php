<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ESSB_Elementor_Share_Action_Button_Widget extends Widget_Base {
	public function get_name() {
		return 'share-action-button';
	}
	
	public function get_title() {
		return __( 'Share Action Button', 'essb' );
	}
	
	public function get_icon() {
		return 'fa fa-share-square-o';
	}
	
	public function get_categories() {
		return [ 'essb' ];
	}
	
	protected function _register_controls() {
		
		$this->start_controls_section(
			'section_my_custom',
			array(
				'label' => esc_html__( 'Action Button Settings', 'essb' ),
			)
		);
		
		$this->add_control(
				'text',
				[
				'label' => __( 'Text', 'essb' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				]
		);
		
		$this->add_control(
				'background',
				[
				'label' => __( 'Button Background', 'essb' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				]
		);
		
		$this->add_control(
				'color',
				[
				'label' => __( 'Button Text', 'essb' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				]
		);		

		$this->add_control(
				'style',
				[
				'label' => __( 'Style', 'essb' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' =>  array('' => 'Button with background color', 'outline' => 'Outline button', 'modern' => 'Modern button')
				]
		);

		$this->add_control(
				'icon',
				[
				'label' => __( 'Icon', 'essb' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' =>  array(
				'' => 'Icon #1',
				'share-alt-square' => 'Icon #2',
				'share-alt' => 'Icon #3',
				'share-tiny' => 'Icon #4',
				'share-outline' => 'Icon #5'
				)
				]
		);
		
		$this->add_control(
				'stretched',
				[
				'label' => __( 'Full Width Button', 'essb' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'No', 'essb' ),
				'label_on' => __( 'Yes', 'essb' ),
				'default' => 'no',
				]
		);
		
		$this->add_control(
				'total',
				[
				'label' => __( 'Show Total Counter', 'essb' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'No', 'essb' ),
				'label_on' => __( 'Yes', 'essb' ),
				'default' => 'no',
				]
		);
		
		$this->end_controls_section();
	}
	
	protected function render( $instance = array() ) {
		$settings = $this->get_settings_for_display();
		
		// get our input from the widget settings.
		$text = ! empty( $settings['text'] ) ? $settings['text'] : '';
		$background = ! empty( $settings['background'] ) ? $settings['background'] : '';
		$color = ! empty( $settings['color'] ) ? $settings['color'] : '';
		$style = ! empty( $settings['style'] ) ? $settings['style'] : '';
		$icon = ! empty( $settings['icon'] ) ? $settings['icon'] : '';
		$stretched = ! empty( $settings['stretched'] ) ? $settings['stretched'] : '';
		$total = ! empty( $settings['total'] ) ? $settings['total'] : '';
		
		if (function_exists('essb_shortcode_share_cta')) {
			echo essb_shortcode_share_cta(array(
					'text' => $text, 
					'icon' => $icon, 
					'style' => $style, 
					'background' => $background, 
					'color' => $color, 
					'stretched' => ($stretched == 'yes' ? 'true' : ''), 
					'total' => ($total == 'yes' ? 'true' : '')));
		}
		else {
			echo __('Cannot render the action button', 'essb');
		}
	}
	
	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}
}
