<?php

if (!function_exists('essb_get_shortcode_options_easy_profiles')) {
	function essb_get_shortcode_options_easy_profiles() {
		$r = array();
		
		if (!class_exists('ESSBSocialProfilesHelper')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-profiles/essb-social-profiles-helper.php');
		}
		
		$r['template'] = array('type' => 'select', 'title' => esc_html__('Template', 'essb'),
				'options' => ESSBSocialProfilesHelper::available_templates());
		$r['animation'] = array('type' => 'select', 'title' => esc_html__('Animation', 'essb'),
				'options' => ESSBSocialProfilesHelper::available_animations());
		$r['align'] = array('type' => 'select', 'title' => esc_html__('Alignment', 'essb'),
				'options' => ESSBSocialProfilesHelper::available_alignments());
		$r['size'] = array('type' => 'select', 'title' => esc_html__('Size', 'essb'),
				'options' => ESSBSocialProfilesHelper::available_sizes());
		
		$r['nospace'] = array('type' => 'checkbox', 'title' => esc_html__('Without space between buttons', 'essb'));
		
		$r['columns'] = array('type' => 'select', 'title' => esc_html__('Columns', 'essb'),
				'options' => array(
						'' => esc_html__('Don\'t show in columns (automatic width)', 'essb'),
						'1' => esc_html__('1 Column', 'essb'),
						'2' => esc_html__('2 Columns', 'essb'),
						'3' => esc_html__('3 Columns', 'essb'),
						'4' => esc_html__('4 Columns', 'essb'),
						'5' => esc_html__('5 Columns', 'essb'),
						'6' => esc_html__('6 Columns', 'essb'),
				));
		
		$r['cta'] = array('type' => 'checkbox', 'title' => esc_html__('Show texts with the buttons', 'essb'));
		$r['cta_vertical'] = array('type' => 'checkbox', 'title' => esc_html__('Vertical text layout', 'essb'));
		$r['cta_number'] = array('type' => 'checkbox', 'title' => esc_html__('Show numbers with the buttons', 'essb'));
		
		$r['profiles_all_networks'] = array('type' => 'checkbox', 'title' => esc_html__('Custom list of networks', 'essb'));
		
		$r['networks_list_start'] = array('type' => 'section-open', 'title' => 'shortcode-all-profile-networks');
		
		$r['spacer1'] = array('type' => 'separator', 'title' => esc_html__('Profile links', 'essb'), 'description' => 'Only when custom list of networks is "Yes"');
		
		$r['networks'] = array('type' => 'text', 'title' => esc_html__('Networks', 'essb'), 'description' => esc_html__('Setup custom network list appearance (order and used networks). Enter the network IDs (example: facebook), separated with comma (",")', 'essb'),
				'options' => essb_available_social_profiles());
		
		foreach (essb_available_social_profiles() as $key => $value) {
			$r['spacer1_'.$key] = array('type' => 'separator-small', 'title' => $value . ' (id: ' . $key . ')');
			$r['profile_'.$key] = array('type' => 'text', 'title' => $value . esc_html__(' URL', 'essb'));
			$r['profile_text_'.$key] = array('type' => 'text', 'title' => $value . esc_html__(' custom follow text', 'essb'));
			$r['profile_count_'.$key] = array('type' => 'text', 'title' => $value . esc_html__(' custom number value', 'essb'));
		}

		$r['networks_list_close'] = array('type' => 'section-close');
		
		return $r;
	}
}

if (!function_exists('essb_get_shortcode_options_profile_bar')) {
	function essb_get_shortcode_options_profile_bar() {
		$r = array();
		
		return $r;
	}
}