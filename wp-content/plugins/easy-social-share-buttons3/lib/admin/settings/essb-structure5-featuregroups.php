<?php

if (class_exists('ESSBControlCenter')) {
	ESSBControlCenter::register_feature('avoid-negative-proof', 'social_proof_enable', 'deactivate_ansp');
	ESSBControlCenter::register_feature('pinterest-pro', 'pinterest_images', 'deactivate_module_pinterestpro');
	ESSBControlCenter::register_feature('share-optimize', 'opengraph_tags', 'deactivate_module_shareoptimize');
	ESSBControlCenter::register_feature('after-share', 'afterclose_active', 'deactivate_module_aftershare');
	ESSBControlCenter::register_feature('short-url', 'shorturl_activate', 'deactivate_module_shorturl');
	ESSBControlCenter::register_feature('affiliate', '', 'deactivate_module_affiliate');
	ESSBControlCenter::register_feature('custom-share', 'customshare', 'deactivate_module_customshare');
	ESSBControlCenter::register_feature('share-recovery', 'counter_recover_active', 'deactivate_ssr');
	ESSBControlCenter::register_feature('share-message', '', 'deactivate_module_message');
	ESSBControlCenter::register_feature('share-analytics', 'stats_active', 'deactivate_module_analytics');
	ESSBControlCenter::register_feature('share-metrics-lite', 'esml_active', 'deactivate_module_metrics');
	ESSBControlCenter::register_feature('conversions-lite', 'conversions_lite_run', 'deactivate_module_conversions');
	ESSBControlCenter::register_feature('fake-counters', 'activate_fake_counters', 'deactivate_fakecounters');
	ESSBControlCenter::register_feature('sharable-quotes', '', 'deactivate_ctt');
	ESSBControlCenter::register_feature('style-library', '', 'deactivate_stylelibrary');
	
	ESSBControlCenter::register_feature_group('share', array(
			'avoid-negative-proof',
			'share-recovery',
			'fake-counters',
			'pinterest-pro', 
			'share-optimize', 
			'after-share', 
			'short-url', 
			'affiliate', 
			'custom-share',
			'share-message',
			'share-analytics',
			'share-metrics-lite',
			'conversions-lite',
			'sharable-quotes',
			'style-library'));
	
	
	
	ESSBControlCenter::register_feature('custom-display', '', 'deactivate_custompositions');
	ESSBControlCenter::register_feature('custom-integrations', '', 'deactivate_method_integrations');

	ESSBControlCenter::register_feature_group('display', array(
			'custom-display',
			'custom-integrations'));
	
	
	ESSBControlCenter::register_feature('social-followers', '', 'deactivate_module_followers');
	ESSBControlCenter::register_feature('social-profiles', '', 'deactivate_module_profiles');
	ESSBControlCenter::register_feature('subscribe', '', 'deactivate_module_subscribe');
	ESSBControlCenter::register_feature('native-social', '', 'deactivate_module_natives');
	ESSBControlCenter::register_feature('facebook-chat', '', 'deactivate_module_facebookchat');
	ESSBControlCenter::register_feature('skype-chat', '', 'deactivate_module_skypechat');
	ESSBControlCenter::register_feature('clickto-chat', '', 'deactivate_module_clicktochat');
	ESSBControlCenter::register_feature('instagram', '', 'deactivate_module_instagram');
	ESSBControlCenter::register_feature('proof-notifications', '', 'deactivate_module_proofnotifications');
	ESSBControlCenter::register_feature_group('other-social', array(
			'social-followers',
			'social-profiles',
			'subscribe',
			'native-social',
			'facebook-chat',
			'skype-chat',
			'clickto-chat',
			'instagram',
			'proof-notifications'));
		
	/**
	 * Register the managing plugin feature details
	 */
	ESSBControlCenter::register_feature_details('avoid-negative-proof', esc_html__('Avoid Negative Social Proof', 'essb'), esc_html__('Hide social share counters before they reach a specific number of shares. Options will appear inside the Share Counter Setup menu.', 'essb'), 'ti-face-smile');
	ESSBControlCenter::register_feature_details('fake-counters', esc_html__('Fake Share Counters', 'essb'), esc_html__('Increase the number of shares with a multiplier (fake values). As an addition, you can also change the values to internal counters for all networks. Options will appear inside the Share Counter Setup menu.', 'essb'), 'ti-loop');
	ESSBControlCenter::register_feature_details('pinterest-pro', esc_html__('Pinterest Pro', 'essb'), esc_html__('Automatically add Pin button over images in content, include Pinterest sharing images or galleries', 'essb'), 'ti-pinterest');
	ESSBControlCenter::register_feature_details('share-optimize', esc_html__('Share Optimizations', 'essb'), esc_html__('Add social share optimization tags for easy tune of the shared information', 'essb'), 'ti-receipt');
	ESSBControlCenter::register_feature_details('after-share', esc_html__('After Share Events', 'essb'), esc_html__('Show additional actions to user after sharing content', 'essb'), 'ti-share');
	ESSBControlCenter::register_feature_details('share-recovery', esc_html__('Social Shares Recovery', 'essb'), esc_html__('Recover number of shares from specific URL changes (example: migrate your site to https (SSL). Options will appear inside the Share Counter Setup menu.', 'essb'), 'ti-reload');
	ESSBControlCenter::register_feature_details('affiliate', esc_html__('Affiliate & Point Integration', 'essb'), esc_html__('Integrate plugin work with myCred, AffiliateWP', 'essb'), 'ti-money');
	ESSBControlCenter::register_feature_details('short-url', esc_html__('Short URL', 'essb'), esc_html__('Generate short URLs for sharing on social networks', 'essb'), 'ti-new-window');
	ESSBControlCenter::register_feature_details('custom-share', esc_html__('Custom Share', 'essb'), esc_html__('Custom share feature makes possible to change the share URL that plugin will use', 'essb'), 'ti-share-alt');
	ESSBControlCenter::register_feature_details('share-message', esc_html__('Message Before Buttons', 'essb'), esc_html__('Add a custom message before or above share buttons "ex: Share this"', 'essb'), 'fa fa-comment');
	ESSBControlCenter::register_feature_details('share-analytics', esc_html__('Plugin Analytics', 'essb'), esc_html__('Log share button clicks and generate report dashboard', 'essb'), 'ti-stats-up');
	ESSBControlCenter::register_feature_details('share-metrics-lite', esc_html__('Social Metrics Lite', 'essb'), esc_html__('Log the official share values into a dashboard to see the most popular posts', 'essb'), 'ti-dashboard');
	ESSBControlCenter::register_feature_details('conversions-lite', esc_html__('Conversions Lite', 'essb'), esc_html__('Conversions lite allows tracking of share or subscribe conversions', 'essb'), 'ti-dashboard');
	ESSBControlCenter::register_feature_details('sharable-quotes', esc_html__('Sharable Quotes (Click to Tweet)', 'essb'), esc_html__('Add click to Tweet quotes inside content with shortcode', 'essb'), 'ti-twitter');
	ESSBControlCenter::register_feature_details('style-library', esc_html__('Style Library', 'essb'), esc_html__('Save and reuse again already configured styles and network list. Saved in the library you can also move the style to a new site. Try also one of 40+ already configured styles if you wonder how to start.', 'essb'), 'ti-paint-roller');
	
	ESSBControlCenter::register_feature_details('custom-display', esc_html__('Custom Display/Positions', 'essb'), esc_html__('With the help of custom display/positions, you can add a new display position inside plugin settings. This display position will have all the options like integrated already in the plugin. But you can use that position anywhere inside content with shortcode, a custom function call, Elementor Widget, etc.', 'essb'), 'ti-layout-media-center-alt');
	ESSBControlCenter::register_feature_details('custom-integrations', esc_html__('Integrations With Plugins', 'essb'), esc_html__('Additional integrations available with WooCommerce, bbPress and etc.', 'essb'), 'fa fa-plug');
	
	ESSBControlCenter::register_feature_details('social-followers', esc_html__('Social Followers Counter', 'essb'), esc_html__('Show the number of followers for 30+ social networks', 'essb'), 'ti-heart');
	ESSBControlCenter::register_feature_details('social-profiles', esc_html__('Social Profile Links', 'essb'), esc_html__('Add plain buttons for your social profiles with shortcode, widget or sidebar', 'essb'), 'ti-id-badge');
	ESSBControlCenter::register_feature_details('subscribe', esc_html__('Subscribe Forms', 'essb'), esc_html__('Add easy to use subscribe to mail list forms', 'essb'), 'ti-email');
	ESSBControlCenter::register_feature_details('native-social', esc_html__('Native Social Buttons', 'essb'), esc_html__('Use selected native social buttons along with your share buttons', 'essb'), 'ti-thumb-up');
	ESSBControlCenter::register_feature_details('facebook-chat', esc_html__('Facebook Live Chat', 'essb'), esc_html__('Connect with your visitors using Facebook live chat', 'essb'), 'fa fa-facebook');
	ESSBControlCenter::register_feature_details('skype-chat', esc_html__('Skype Live Chat', 'essb'), esc_html__('Connect with your visitors using Skype live chat', 'essb'), 'fa fa-skype');
	ESSBControlCenter::register_feature_details('clickto-chat', esc_html__('Click 2 Chat', 'essb'), esc_html__('Add click to chat feature for WhatsApp and Viber', 'essb'), 'fa fa-comments');
	ESSBControlCenter::register_feature_details('instagram', esc_html__('Instagram Feed', 'essb'), esc_html__('Enable generation of Instagram feeds or embed images on site', 'essb'), 'ti-instagram');
	ESSBControlCenter::register_feature_details('proof-notifications', esc_html__('Social Proof Notifications Lite', 'essb'), esc_html__('Enable display of share counter social proof notification messages', 'essb'), 'ti-comment-alt');
}


if (!function_exists('essb7_available_content_positions')) {
	function essb7_available_content_positions($wizard_mode = false) {
		$essb_avaliable_content_positions = array ();
		$essb_avaliable_content_positions ['content_top'] = array ('image' => 'assets/images/content-positions/content-1-01.svg', 'label' => 'Content top', 'desc' => 'Display share buttons at the top of content', 'link' => 'display-4' );
		$essb_avaliable_content_positions ['content_bottom'] = array ('image' => 'assets/images/content-positions/content-1-02.svg', 'label' => 'Content bottom', 'desc' => 'Display share buttons at the bottom of content', 'link' => 'display-5' );
		$essb_avaliable_content_positions ['content_both'] = array ('image' => 'assets/images/content-positions/content-1-03.svg', 'label' => 'Content top and bottom', 'desc' => 'Display share buttons on top and at the bottom of content' );
		if (!essb_options_bool_value('deactivate_method_float')) {
			$essb_avaliable_content_positions ['content_float'] = array ('image' => 'assets/images/content-positions/content-1-04.svg', 'label' => 'Float from content top', 'desc' => 'Display share buttons initially on top of content and during scroll they will stick on the top of window', 'link' => 'display-6' );
			$essb_avaliable_content_positions ['content_floatboth'] = array ('image' => 'assets/images/content-positions/content-1-05.svg', 'label' => 'Float from content top and bottom' , 'desc' => 'Display share buttons initially on top of content and during scroll they will stick on the top of window in combination with static bottom share buttons' );
		}

		if (!essb_options_bool_value('deactivate_method_followme')) {
			$essb_avaliable_content_positions ['content_followme'] = array ('image' => 'assets/images/content-positions/content-1-06.svg', 'label' => 'Follow me bar' , 'desc' => 'Display share buttons inside content (top, bottom or both) in combination with fixed bar, appearing when you scroll down the post/page and in content buttons are not visible inside.', 'link' => 'essb-menu-display|essb-menu-display-18' );
		}

		if (!essb_option_bool_value('deactivate_method_native')) {
			$essb_avaliable_content_positions ['content_nativeshare'] = array ('image' => 'assets/images/content-positions/content-1-08.svg', 'label' => 'Native social buttons top, share buttons bottom', 'desc' => 'This method will show activated inside Social Follow native buttons on top along with share buttons at the bottom' );
			$essb_avaliable_content_positions ['content_sharenative'] = array ('image' => 'assets/images/content-positions/content-1-07.svg', 'label' => 'Share buttons top, native buttons bottom', 'desc' => 'This method will show activated inside Social Follow native buttons at the bottom of content along with share buttons on the top' );
		}
		$essb_avaliable_content_positions ['content_manual'] = array ('image' => 'assets/images/content-positions/content-1-09.svg', 'label' => 'Manual display with shortcode only', 'desc' => 'Use this content position if you do not wish to have share buttons inside content or if you wish to add them manually' );

		if (has_filter('essb4_content_positions')) {
			$essb_avaliable_content_positions = apply_filters('essb4_content_positions', $essb_avaliable_content_positions);
		}

		if ($wizard_mode) {
			foreach ($essb_avaliable_content_positions as $key => $data) {
				$essb_avaliable_content_positions[$key]['link'] = '';
			}
		}

		return $essb_avaliable_content_positions;
	}
}