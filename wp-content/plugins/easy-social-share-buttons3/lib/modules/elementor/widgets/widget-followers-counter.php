<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ESSB_Elementor_Followers_Counter_Widget extends Widget_Base {
	public function get_name() {
		return 'followers-counter';
	}
	
	public function get_title() {
		return __( 'Followers Counter', 'essb' );
	}
	
	public function get_icon() {
		return 'fa fa-heart-o';
	}
	
	public function get_categories() {
		return [ 'essb' ];
	}
	
	protected function _register_controls() {
		
		$this->start_controls_section(
			'section_my_custom',
			array(
				'label' => esc_html__( 'Followers Setup', 'essb' ),
			)
		);
		
		// loading shortcode default structure
		if (!class_exists('ESSBSocialFollowersCounterHelper')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-helper.php');
		}
		
		$default_shortcode_setup = \ESSBSocialFollowersCounterHelper::default_instance_settings();
		$shortcode_settings = \ESSBSocialFollowersCounterHelper::default_options_structure(true, $default_shortcode_setup);
		foreach ($shortcode_settings as $field => $options) {
						
			$description = isset($options['description']) ? $options['description'] : '';
			$title = isset($options['title']) ? $options['title'] : '';
			$type = isset($options['type']) ? $options['type'] : '';
			$default = isset($options['default']) ? $options['default'] : '';

			if ($type == 'textbox' && $field == 'bgcolor') {
				$type = 'color';
			}
			
			if ($type == 'textbox') {
				$this->add_control(
						$field,
						[
						'label' => $title,
						'type' => Controls_Manager::TEXT,
						'default' => '',
						]
				);
			}
			
			if ($type == 'color') {
				$this->add_control(
						$field,
						[
						'label' => $title,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
						]
				);
			}
			
			if ($type == 'checkbox') {
				$this->add_control(
						$field,
						[
						'label' => $title,
						'type' => Controls_Manager::SWITCHER,
						'label_off' => __( 'No', 'essb' ),
						'label_on' => __( 'Yes', 'essb' ),
						'default' => 'no',
						]
				);
			}
			
			if ($type == 'select') {
				$values = isset($options['values']) ? $options['values'] : array();
				
				if ($field == 'columns') {
					$values['layout'] = __('User Layout Builder', 'essb');
				}
				
				$this->add_control(
						$field,
						[
						'label' => $title,
						'type' => Controls_Manager::SELECT,
						'default' => $default,
						'options' =>  $values
						]
				);
				
			}
			
		}
		

		$this->end_controls_section();
	}
	
	protected function render( $instance = array() ) {
		$settings = $this->get_settings_for_display();
		
		if (!class_exists('\ESSBSocialFollowersCounterHelper')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-helper.php');
		}
		
		if (!class_exists('\ESSBSocialFollowersCounterDraw')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-draw.php');
		}
		
		$default_options = \ESSBSocialFollowersCounterHelper::default_instance_settings();		
		$attrs = shortcode_atts( $default_options , $settings );
		
		$columns = ! empty( $settings['columns'] ) ? $settings['columns'] : '';
		if ($columns == 'layout') {
			\ESSBSocialFollowersCounterDraw::draw_followers($attrs, false, true);
		}
		else {
			\ESSBSocialFollowersCounterDraw::draw_followers($attrs, true);
		}
	}
	
	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}
}
