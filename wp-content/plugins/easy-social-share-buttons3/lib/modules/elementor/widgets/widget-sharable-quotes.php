<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ESSB_Elementor_Sharable_Qutotes_Widget extends Widget_Base {
	public function get_name() {
		return 'easy-tweet';
	}
	
	public function get_title() {
		return __( 'Sharable Quotes', 'essb' );
	}
	
	public function get_icon() {
		return 'fa fa-twitter';
	}
	
	public function get_categories() {
		return [ 'essb' ];
	}
	
	protected function _register_controls() {
		
		$this->start_controls_section(
			'section_my_custom',
			array(
				'label' => esc_html__( 'Tweet Setup', 'essb' ),
			)
		);
		

		$this->add_control(
			'quote',
			[
				'label' => __( 'Quote', 'essb' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => '',
			]
		);
		
		$this->add_control(
				'user',
				[
				'label' => __( 'Include the following user in quote', 'essb' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				]
		);
		
		$this->add_control(
				'tags',
				[
				'label' => __( 'Hashtags', 'essb' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				]
		);
		
		$this->add_control(
				'url',
				[
				'label' => __( 'URL', 'essb' ),
				'title' => __('Include URL that you wish to appear inside the quote', 'essb'),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				]
		);

		$this->add_control(
				'template',
				[
				'label' => __( 'Template', 'essb' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
						'' => 'Default',
						'light' => 'Light',
						'dark' => 'Dark',
						'qlite' => 'Quote'
						)
				]
		);		
		
		
		$this->end_controls_section();
	}
	
	protected function render( $instance = array() ) {
		$settings = $this->get_settings_for_display();
		
		// get our input from the widget settings.
		$quote = ! empty( $settings['quote'] ) ? $settings['quote'] : '';
		$user = ! empty( $settings['user'] ) ? $settings['user'] : '';
		$tags = ! empty( $settings['tags'] ) ? $settings['tags'] : '';
		$url = ! empty( $settings['url'] ) ? $settings['url'] : '';
		$template = ! empty( $settings['template'] ) ? $settings['template'] : '';
		
		
		// building quotes shortcode
		$code = '[easy-tweet';
		
		if ($quote != '') {
			$code .= ' tweet="'.$quote.'"';
		}
		
		if ($user != '') {
			$code .= ' user="'.$user.'" via="yes"';
		}
		else {
			$code .= ' via="no"';
		}
		
		if ($tags != '') {
			$code .= ' hashtags="'.$tags.'" usehashtags="yes"';
		}
		else {
			$code .= ' usehashtags="no"';
		}
		
		if ($url != '') {
			$code .= ' url="'.$url.'"';
		}
		
		if ($template != '') {
			$code .= ' template="'.$template.'"';
		}
		
		$code .= ']';
		
		echo do_shortcode($code);
	}
	
	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}
}
