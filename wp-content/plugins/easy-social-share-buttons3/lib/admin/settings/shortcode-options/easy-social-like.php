<?php
if (!function_exists('essb_get_shortcode_options_easy_social_like')) {
	function essb_get_shortcode_options_easy_social_like() {
		$r = array();
		
		$r['align'] = array('type' => 'select', 'title' => esc_html__('Alignment', 'essb'),
				'options' => array(
						'left' => esc_html__('Left', 'essb'),
						'center' => esc_html__('Center', 'essb'),
						'right' => esc_html__('Right', 'essb'),
				));
		
		$r['counters'] = array('type' => 'checkbox-true', 'title' => esc_html__('Show counters (where supported)', 'essb'));
		$r['skinned'] = array('type' => 'checkbox-true', 'title' => esc_html__('Hide buttons behind skin', 'essb'));
		$r['skin'] = array('type' => 'select', 'title' => esc_html__('Skin', 'essb'),
				'options' => array(
						'metro' => esc_html__('Metro', 'essb'),
						'flat' => esc_html__('Flat', 'essb')
				));
		
		$r['spacer1'] = array('type' => 'separator', 'title' => esc_html__('Facebook Like Button', 'essb'));
		$r['facebook'] = array('type' => 'checkbox', 'title' => esc_html__('Enable Facebook Like button', 'essb'));
		$r['facebook_url'] = array('type' => 'text', 'title' => esc_html__('Custom like URL', 'essb'));
		$r['facebook_width'] = array('type' => 'text', 'title' => esc_html__('Custom button width', 'essb'), 'options' => array('size' => 'small'));
		$r['facebook_skinned_text'] = array('type' => 'text', 'title' => esc_html__('Skinned text', 'essb'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));
		$r['facebook_skinned_width'] = array('type' => 'text', 'title' => esc_html__('Skinned width', 'essb'), 'options' => array('size' => 'small'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));
		
		$r['spacer2'] = array('type' => 'separator', 'title' => esc_html__('Facebook Follow Button', 'essb'));
		$r['facebook_follow'] = array('type' => 'checkbox', 'title' => esc_html__('Enable Facebook Follow button', 'essb'));
		$r['facebook_follow_url'] = array('type' => 'text', 'title' => esc_html__('Custom follow URL', 'essb'));
		$r['facebook_follow_width'] = array('type' => 'text', 'title' => esc_html__('Custom button width', 'essb'), 'options' => array('size' => 'small'));
		$r['facebook_follow_skinned_text'] = array('type' => 'text', 'title' => esc_html__('Skinned text', 'essb'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));
		$r['facebook_follow_skinned_width'] = array('type' => 'text', 'title' => esc_html__('Skinned width', 'essb'), 'options' => array('size' => 'small'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));

		$r['spacer3'] = array('type' => 'separator', 'title' => esc_html__('Twitter Follow Button', 'essb'));
		$r['twitter_follow'] = array('type' => 'checkbox', 'title' => esc_html__('Enable Twitter Follow button', 'essb'));
		$r['twitter_follow_user'] = array('type' => 'text', 'title' => esc_html__('Twitter username', 'essb'));
		$r['twitter_follow_skinned_text'] = array('type' => 'text', 'title' => esc_html__('Skinned text', 'essb'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));
		$r['twitter_follow_skinned_width'] = array('type' => 'text', 'title' => esc_html__('Skinned width', 'essb'), 'options' => array('size' => 'small'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));
		
		$r['spacer4'] = array('type' => 'separator', 'title' => esc_html__('Twitter Tweet Button', 'essb'));
		$r['twitter_tweet'] = array('type' => 'checkbox', 'title' => esc_html__('Enable Twitter Tweet button', 'essb'));
		$r['twitter_tweet_message'] = array('type' => 'text', 'title' => esc_html__('Twitter message', 'essb'));
		$r['twitter_tweet_skinned_text'] = array('type' => 'text', 'title' => esc_html__('Skinned text', 'essb'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));
		$r['twitter_tweet_skinned_width'] = array('type' => 'text', 'title' => esc_html__('Skinned width', 'essb'), 'options' => array('size' => 'small'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));

		$r['spacer5'] = array('type' => 'separator', 'title' => esc_html__('YouTube Channel Subscribe', 'essb'));
		$r['youtube'] = array('type' => 'checkbox', 'title' => esc_html__('Enable YouTube channel subscribe button', 'essb'));
		$r['youtube_channel'] = array('type' => 'text', 'title' => esc_html__('Channel', 'essb'));
		$r['youtube_skinned_text'] = array('type' => 'text', 'title' => esc_html__('Skinned text', 'essb'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));
		$r['youtube_skinned_width'] = array('type' => 'text', 'title' => esc_html__('Skinned width', 'essb'), 'options' => array('size' => 'small'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));

		$r['spacer6'] = array('type' => 'separator', 'title' => esc_html__('YouTube Channel Subscribe', 'essb'));
		$r['youtube'] = array('type' => 'checkbox', 'title' => esc_html__('Enable YouTube channel subscribe button', 'essb'));
		$r['youtube_channel'] = array('type' => 'text', 'title' => esc_html__('Channel ID', 'essb'));
		$r['youtube_skinned_text'] = array('type' => 'text', 'title' => esc_html__('Skinned text', 'essb'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));
		$r['youtube_skinned_width'] = array('type' => 'text', 'title' => esc_html__('Skinned width', 'essb'), 'options' => array('size' => 'small'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));

		$r['spacer7'] = array('type' => 'separator', 'title' => esc_html__('Pinterest Pin', 'essb'));
		$r['pinterest_pin'] = array('type' => 'checkbox', 'title' => esc_html__('Enable Pinterest Pin button', 'essb'));
		$r['pinterest_pin_skinned_text'] = array('type' => 'text', 'title' => esc_html__('Skinned text', 'essb'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));
		$r['pinterest_pin_skinned_width'] = array('type' => 'text', 'title' => esc_html__('Skinned width', 'essb'), 'options' => array('size' => 'small'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));

		$r['spacer8'] = array('type' => 'separator', 'title' => esc_html__('Pinterest Follow', 'essb'));
		$r['pinterest_follow'] = array('type' => 'checkbox', 'title' => esc_html__('Enable Pinterest follow button', 'essb'));
		$r['pinterest_follow_display'] = array('type' => 'text', 'title' => esc_html__('Show text inside button', 'essb'));
		$r['pinterest_follow_url'] = array('type' => 'text', 'title' => esc_html__('Profile URL', 'essb'));
		$r['pinterest_follow_skinned_text'] = array('type' => 'text', 'title' => esc_html__('Skinned text', 'essb'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));
		$r['pinterest_follow_skinned_width'] = array('type' => 'text', 'title' => esc_html__('Skinned width', 'essb'), 'options' => array('size' => 'small'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));

		$r['spacer9'] = array('type' => 'separator', 'title' => esc_html__('LinkedIn Company Follow', 'essb'));
		$r['linkedin'] = array('type' => 'checkbox', 'title' => esc_html__('Enable LinkedIn company follow button', 'essb'));
		$r['linkedin_company'] = array('type' => 'text', 'title' => esc_html__('Company ID', 'essb'));
		$r['linkedin_skinned_text'] = array('type' => 'text', 'title' => esc_html__('Skinned text', 'essb'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));
		$r['linkedin_skinned_width'] = array('type' => 'text', 'title' => esc_html__('Skinned width', 'essb'), 'options' => array('size' => 'small'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));
		
		$r['spacer10'] = array('type' => 'separator', 'title' => esc_html__('VKontakte', 'essb'));
		$r['vk_follow'] = array('type' => 'checkbox', 'title' => esc_html__('Enable vk.com button', 'essb'));
		$r['vk_skinned_text'] = array('type' => 'text', 'title' => esc_html__('Skinned text', 'essb'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));
		$r['vk_skinned_width'] = array('type' => 'text', 'title' => esc_html__('Skinned width', 'essb'), 'options' => array('size' => 'small'), 'description' => esc_html__('Only when skinned mode is enabled', 'essb'));
		
		
		return $r;
	}
}