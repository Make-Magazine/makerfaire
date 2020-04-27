<?php
if (!function_exists('essb_get_shortcode_options_sharable_quote')) {
	function essb_get_shortcode_options_sharable_quote() {
		$r = array();

		$r['tweet'] = array('type' => 'textarea', 'title' => esc_html__('Tweet message', 'essb'));
		$r['user'] = array('type' => 'text', 'title' => esc_html__('Username to mention in Tweet', 'essb'), 'description' => esc_html__('Twitter username without the @ symbol', 'essb'));
		$r['hashtags'] = array('type' => 'text', 'title' => esc_html__('Hashtags', 'essb'), 'description' => esc_html__('In this field the hashtags list is added without the # symbol and separated with comma. Example: hashtag1,hashtag2', 'essb'));
		$r['url'] = array('type' => 'text', 'title' => esc_html__('Share URL', 'essb'), 'description' => esc_html__('Optional value if you did not provide URL in the Tweet', 'essb'));
		$r['template'] = array('type' => 'select', 'title' => esc_html__('Template', 'essb'),
				'options' => array(
						'' => esc_html__('Default', 'essb'),
						'light' => esc_html__('Light', 'essb'),
						'dark' => esc_html__('Dark', 'essb'),
						'qlite' => esc_html__('Quote', 'essb'),
						'modern' => esc_html__('Modern', 'essb'),
						'user' => esc_html__('User', 'essb')
				));		
		
		
		$r['via'] = array('type' => 'select', 'title' => esc_html__('Don\'t include username in the Tweet', 'essb'),
				'options' => array('' => 'Default', 'no' => esc_html__('No', 'essb'), 'yes' => esc_html__('Yes', 'essb')));
		$r['usehashtags'] = array('type' => 'select', 'title' => esc_html__('Don\'t include hashtags in the Tweet', 'essb'),
				'options' => array('' => 'Default', 'no' => esc_html__('No', 'essb'), 'yes' => esc_html__('Yes', 'essb')));
		
		return $r;
	}
}