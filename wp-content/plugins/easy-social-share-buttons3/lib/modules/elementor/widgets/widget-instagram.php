<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ESSB_Elementor_Instagram_Feed_Widget extends Widget_Base {
	public function get_name() {
		return 'instagram-feed';
	}
	
	public function get_title() {
		return esc_html__( 'Instagram Feed', 'essb' );
	}
	
	public function get_icon() {
		return 'fa fa-instagram';
	}
	
	public function get_categories() {
		return [ 'essb' ];
	}
	
	protected function _register_controls() {
		
		$this->start_controls_section(
			'section_my_custom',
			array(
				'label' => esc_html__( 'Instagram Setup', 'essb' ),
			)
		);
		
		
		if (class_exists('ESSBControlCenterShortcodes')) {
			$shortcode_settings = \ESSBControlCenterShortcodes::get_shortcode_options('instagram-feed');
			
			foreach ($shortcode_settings as $param => $setup) {
				$type = $setup['type'];
				$title = isset($setup['title']) ? $setup['title'] : '';
				$description = isset($setup['description']) ? $setup['description'] : '';
				$options = isset($setup['options']) ? $setup['options'] : array();
				$value = isset($setup['default_value']) ? $setup['default_value'] : '';
				
				if ($type == 'text') {
					$this->add_control(
							$param,
							array(
							'label' => $title,
							'type' => Controls_Manager::TEXT,
							'default' => $value,
							)
					);
					
					if ($description != '') {
						$this->add_control(
								'custom_image_explanation',
								array(
								'type' => Controls_Manager::RAW_HTML,
								'raw' => $description,
								'content_classes' => 'elementor-descriptor',
								)
						);
					}
				}
				
				if ($type == 'select') {
					$this->add_control(
							$param,
							array(
							'label' => $title,
							'type' => Controls_Manager::SELECT,
							'default' => $value,
							'options' =>  $options
							)
					);
					
					if ($description != '') {
						$this->add_control(
								'custom_image_explanation',
								array(
										'type' => Controls_Manager::RAW_HTML,
										'raw' => $description,
										'content_classes' => 'elementor-descriptor',
								)
						);
					}
				}
			}
		}
				
		$this->end_controls_section();
	}
	
	protected function render( $instance = array() ) {
		$settings = $this->get_settings_for_display();
		

		if (function_exists('essb_instagram_feed')) {
			echo essb_instagram_feed()->generate_shortcode($settings);
		}
	}
	
	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}
}
