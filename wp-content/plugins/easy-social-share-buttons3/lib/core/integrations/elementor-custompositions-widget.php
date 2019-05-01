<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ESSB_Elementor_Custom_Positions_Widget extends Widget_Base {
	public function get_name() {
		return 'social-share-display';
	}
	
	public function get_title() {
		return __( 'Social Share Buttons Display', 'essb' );
	}
	
	public function get_icon() {
		return 'eicon-share';
	}
	
	public function get_categories() {
		return [ 'basic', 'essb' ];
	}
	
	protected function _register_controls() {
		
		$this->start_controls_section(
			'section_my_custom',
			array(
				'label' => esc_html__( 'Display Setup', 'essb' ),
			)
		);
		

		$this->add_control(
			'display',
			[
				'label' => __( 'Custom Design', 'essb' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => essb5_get_custom_positions()
			]
		);
		
		$this->add_control(
				'always_show_desc',
				[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'The custon display will appear based on position settings inside Easy Social Share Buttons for WordPress. If the selected design is not activated inside Positions menu, plugin will not generate share buttons. If you wish to bypass this check and always show the share buttons no matter of position selection set the below option to Yes.', 'essb' ),
				'content_classes' => 'elementor-descriptor',
				]
		);
		
		$this->add_control(
				'force',
				[
				'label' => __( 'Always Show', 'essb' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'No', 'essb' ),
				'label_on' => __( 'Yes', 'essb' ),
				'default' => 'no',
				]
		);
		
		$this->add_control(
				'colors_warning',
				[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'Note: If you need to add additional displays you can do this from Where to Display -> Custom Position/Displays', 'essb' ),
				'content_classes' => 'elementor-descriptor',
				]
		);
		

		
		$this->end_controls_section();
	}
	
	protected function render( $instance = array() ) {
		$settings = $this->get_settings_for_display();
		// get our input from the widget settings.
		$force = ! empty( $settings['force'] ) ? $settings['force'] : '';
		$display = ! empty( $settings['display'] ) ? $settings['display'] : '';
		
		$force = ($force == 'yes') ? true : false;
		
		essb_custom_position_draw($display, $force);
	}
	
	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}
}
