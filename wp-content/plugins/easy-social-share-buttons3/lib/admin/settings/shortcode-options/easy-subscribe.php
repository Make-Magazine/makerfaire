<?php
if (!function_exists('essb_get_shortcode_options_easy_subscribe')) {
	function essb_get_shortcode_options_easy_subscribe() {
		$r = array();
		
		$listOfTypes = array("" => esc_html__('Default', 'essb'));
		$all_designs = essb_optin_designs();
		foreach ($all_designs as $key => $value) {
			$listOfTypes[$key] = $value;
		}
		
		$r['design'] = array('type' => 'select', 'title' => esc_html__('Template', 'essb'),
				'options' => $listOfTypes);
		$r['twostep'] = array('type' => 'select', 'title' => esc_html__('Two step form', 'essb'),
				'options' => array(
						'' => esc_html__('No', 'essb'),
						'true' => esc_html__('Yes', 'essb')));
		
		$r['twostep_text'] = array('type' => 'text', 'title' => esc_html__('Link text for the two step mode', 'essb'));
		$r['twostep_inline'] = array('type' => 'select', 'title' => esc_html__('Show two step form inline', 'essb'),
				'options' => array(
						'' => esc_html__('No', 'essb'),
						'true' => esc_html__('Yes', 'essb')));
		
		return $r;
	}
}