<?php

if (!function_exists('essb_get_shortcode_options_pinterest_image')) {
	function essb_get_shortcode_options_pinterest_image() {
		$r = array();
		
		$r['message'] = array('type' => 'textarea', 'title' => esc_html__('Custom Pin share message', 'essb'));
		
		$r['type'] = array('type' => 'select', 'title' => esc_html__('Pin button share action', 'essb'),
				'options' => array(
						'' => esc_html__('Selected in the shortcode image', 'essb'),
						'post' => esc_html__('Custom image from the Pinterest section in post edit mode', 'essb')));
		$r['align'] = array('type' => 'select', 'title' => esc_html__('Alignment', 'essb'),
				'options' => array(
						'' => esc_html__('Default', 'essb'),
						'left' => esc_html__('Left', 'essb'),
						'center' => esc_html__('Center', 'essb'),
						'right' => esc_html__('Right', 'essb'),
				));
		$r['image'] = array('type' => 'text', 'title' => esc_html__('Image URL', 'essb'));
		$r['custom_image'] = array('type' => 'text', 'title' => esc_html__('Custom Pin image URL', 'essb'), 'description' => esc_html__('Provide a custom Pin image URL only when you need to have a different image on screen than those for Pin. If so in the Image URL field you will put the one appearing on screen (screen optimized) and here you will set the image for sharing (Pinterest optimized).', 'essb'));
		$r['custom_classes'] = array('type' => 'text', 'title' => esc_html__('Custom CSS Classes', 'essb'));
		
		return $r;
	}
}

if (!function_exists('essb_get_shortcode_options_pinterest_gallery')) {
	function essb_get_shortcode_options_pinterest_gallery() {
		$r = array();

		$r['message'] = array('type' => 'textarea', 'title' => esc_html__('Custom Pin share message', 'essb'));

		$r['columns'] = array('type' => 'select', 'title' => esc_html__('Columns', 'essb'),
				'options' => array(
						'1' => esc_html__('1 column', 'essb'),
						'2' => esc_html__('2 columns', 'essb'),
						'3' => esc_html__('3 columns', 'essb'),
						'4' => esc_html__('4 columns', 'essb'),
				));
		$r['images'] = array('type' => 'text', 'title' => esc_html__('Images', 'essb'), 'description' => esc_html__('Enter image IDs from the media library, separated with , (comma). Example: 100,200,300', 'essb'));
		$r['custom_classes'] = array('type' => 'text', 'title' => esc_html__('Custom CSS Classes', 'essb'));
		$r['spacing'] = array('type' => 'text', 'title' => esc_html__('Space between images', 'essb'), 'description' => esc_html__('Example: 5px, 1%, 2em', 'essb'));
		$r['adjust'] = array('type' => 'select', 'title' => esc_html__('Equal image height?', 'essb'),
				'options' => array(
						'' => esc_html__('No', 'essb'),
						'yes' => esc_html__('Yes', 'essb'),
				));		
		return $r;
	}
}