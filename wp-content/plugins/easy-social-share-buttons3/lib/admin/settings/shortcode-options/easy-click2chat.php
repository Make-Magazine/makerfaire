<?php
if (!function_exists('essb_get_shortcode_options_easy_click2chat')) {
	function essb_get_shortcode_options_easy_click2chat() {
		$r = array();

		$r['text'] = array('type' => 'text', 'title' => esc_html__('Call to action text for the button', 'essb'));
		$r['background'] = array('type' => 'text', 'title' => esc_html__('Background color', 'essb'), 'options' => array('size' => 'small'));
		$r['color'] = array('type' => 'text', 'title' => esc_html__('Text color', 'essb'), 'options' => array('size' => 'small'));
		$listOfTypes = array("whatsapp" => "Icon #1", "comments" => "Icon #2", "comment-o" => "Icon #3", "viber" => "Icon #4");
		
		$r['icon'] = array('type' => 'select', 'title' => esc_html__('Icon', 'essb'),
				'options' => $listOfTypes);
		
		return $r;
	}
}