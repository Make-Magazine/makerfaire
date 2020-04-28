<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ESSB_Elementor_Pinterest_Image_Widget extends Widget_Base {
	public function get_name() {
		return 'pinterest-image';
	}
	
	public function get_title() {
		return __( 'Pinable Image', 'essb' );
	}
	
	public function get_icon() {
		return 'fa fa-pinterest';
	}
	
	public function get_categories() {
		return [ 'essb' ];
	}
	
	protected function _register_controls() {
		
		$this->start_controls_section(
			'section_my_custom',
			array(
				'label' => esc_html__( 'Pinable Image Settings', 'essb' ),
			)
		);
		
		$this->add_control(
				'message',
				[
				'label' => __( 'Custom Pin Text', 'essb' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => '',
				]
		);
		
		$this->add_control(
				'type',
				[
				'label' => __( 'Pin Type', 'essb' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => array('' => 'Custom Selected Image', 'post' => 'Pin Post Custom Pinterest Data')
				]
		);	

		$this->add_control(
				'align',
				[
				'label' => __( 'Image Align', 'essb' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => array('' => 'Default', 'left' => 'Left', 'center' => 'Center', 'right' => 'Right')
				]
		);
		
		$this->add_control(
				'image',
				[
				'label' => __( 'Image', 'essb' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
					]
				]
		);
		
		$this->add_control(
				'custom_classes',
				[
				'label' => __( 'Custom Image CSS Classes', 'essb' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				]
		);
		
		$this->add_control(
				'custom_image_explanation',
				[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'The selected image you choose will appear on screen and also in Pinterest share. If you need to specify a different Pinterest image (for example optimized for screen and optimized for share) choose below the image inside Pin.', 'essb' ),
				'content_classes' => 'elementor-descriptor',
				]
		);
		
		$this->add_control(
				'custom_image',
				[
				'label' => __( 'Custom Pin Image', 'essb' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
				'url' => \Elementor\Utils::get_placeholder_image_src(),
				]
				]
		);
		
		
		$this->end_controls_section();
	}
	
	protected function render( $instance = array() ) {
		$settings = $this->get_settings_for_display();
		
		$message = ! empty( $settings['message'] ) ? $settings['message'] : '';
		$type = ! empty( $settings['type'] ) ? $settings['type'] : '';
		$align = ! empty( $settings['align'] ) ? $settings['align'] : '';
		$image = ! empty( $settings['image'] ) ? $settings['image'] : '';
		$custom_classes = ! empty( $settings['custom_classes'] ) ? $settings['custom_classes'] : '';
		$custom_image = ! empty( $settings['custom_image'] ) ? $settings['custom_image'] : '';
		
		essb_depend_load_function('essb5_generate_pinterest_image', 'lib/modules/pinterest-pro/pinterest-pro-shortcodes.php');
		
		if (function_exists('essb5_generate_pinterest_image')) {
			echo essb5_generate_pinterest_image(array(
				'type' => $type,
					'image' => $image,
					'message' => $message,
					'custom_image' => $custom_image,
					'align' => $align,
					'class' => $custom_classes,
					'elementor' => true		
			));	
		}
		else {
			echo __('Pinterest Pro feature of Easy Social Share Buttons for WordPress is not active right now on your site', 'essb');
		}
	}
	
	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}
}
