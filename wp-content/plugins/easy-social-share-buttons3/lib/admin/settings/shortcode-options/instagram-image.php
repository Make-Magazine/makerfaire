<?php

if (!function_exists('essb_get_shortcode_options_instagram_image')) {
	function essb_get_shortcode_options_instagram_image() {
		$r = array();
		
		$r['id'] = array('type' => 'text', 'title' => esc_html__('Image ID', 'essb'));
		$r['profile'] = array('type' => 'select', 'title' => esc_html__('Show profile information', 'essb'),
				'options' => array(
						'false' => esc_html__('No', 'essb'),
						'true' => esc_html__('Yes', 'essb')));
		$r['info'] = array('type' => 'select', 'title' => esc_html__('Show image information', 'essb'),
				'options' => array(
						'false' => esc_html__('No', 'essb'),
						'true' => esc_html__('Yes', 'essb')));
		
		return $r;
	}
}