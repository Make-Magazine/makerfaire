<?php

class ESSBSocialFollowersCounterHelper {
	
	/**
	 * default_instance_settings
	 * 
	 * Create default instance options used in shortcodes and widgets
	 * 
	 * @return multitype:string number
	 * @since 3.4
	 */
	public static function default_instance_settings() {
		$defaults = array();
		$defaults['title'] = "Social Followers";
		$defaults['new_window'] = 1;
		$defaults['nofollow'] = 1;
		$defaults['hide_title'] = 0;
		$defaults['show_total'] = 0;
		$defaults['total_type'] = 'text_before';
		$defaults['columns'] = 3;
		$defaults['template'] = 'flat';
		$defaults['animation'] = '';
		$defaults['bgcolor'] = '';
		$defaults['nospace'] = 0;
		$defaults['hide_value'] = 0;
		$defaults['hide_text'] = 0;
		
		return $defaults;
	}
	
	/**
	 * default_options_structure
	 * 
	 * Default widget or shortcode settings fields.
	 * Updated 4.2 to set $custom_defaults array value
	 * 
	 * @param boolean $apply_defaults
	 * @return multitype:multitype:string  multitype:string multitype:string
	 * @since 3.4	 
	 */
	public static function default_options_structure($apply_defaults = false, $custom_defaults = array()) {
		$structure = array();
		$structure['title'] = array('type' => 'textbox', 'title' => 'Title', 'description' => 'Display title over the widget');
		$structure['hide_title'] = array('type' => 'checkbox', 'title' => 'Hide widget title', 'description' => 'Activate this option if you wish to hide widget title');
		$structure['new_window'] = array('type' => 'checkbox', 'title' => 'Open links in new window', 'description' => '(recommended) Activate this option to open links to social profiles in new window');
		$structure['nofollow'] = array('type' => 'checkbox', 'title' => 'Add nofollow to links', 'description' => '(recommended) Activate this option to add nofollow state of outgoing links');
		$structure['separator1'] = array('type' => 'separator', 'title' => 'Total followers setup', 'hide_advanced' => 'true');
		$structure['show_total'] = array('type' => 'checkbox', 'title' => 'Display total followers', 'description' => 'Activate this option if you wish to display total number of followers', 'hide_advanced' => 'true');
		$structure['total_type'] = array('type' => 'select', 'title' => 'Total followers type', 'description' => 'Choose total followers display type for this widget', 'values' => array('text_before' => 'Display as text before buttons', 'text_after' => 'Display as text after buttons', 'button_single' => 'Button with width of single button'), 'hide_advanced' => 'true');
		$structure['separator2'] = array('type' => 'separator', 'title' => 'Visual setup');
		$structure['columns'] = array('type' => 'select', 'title' => 'Columns', 'description' => 'Choose number of columns', 'values' => array('1' => '1 Column', '2' => '2 Columns', '3' => '3 Columns', '4' => '4 Columns', '5' => '5 Columns', '6' => '6 Columns', 'row' => 'Without automatic column split'), 'hide_advanced' => 'true', 'default' => '1');
		$structure['template'] = array('type' => 'select', 'title' => 'Template', 'description' => 'Choose template for this widget', 'values' => array(
		    'color' => 'Color icons', 
		    'roundcolor' => 'Round Color Icons', 
		    'outlinecolor' => 'Outline Color Icons', 
		    'grey' => 'Grey icons', 
		    'roundgrey' => 'Round Grey Icons', 
		    'outlinegrey' => 'Outline Grey Icons', 
		    'light' => 'Light Icons', 
		    'roundlight' => 'Round Light Icons', 
		    'outlinelight' => 'Outline Light Icons', 
		    'metro' => 'Metro', 
		    'flat' => 'Flat', 
		    'dark' => 'Dark', 
		    'tinycolor' => 'Tiny Color', 
		    'tinygrey' => 'Tiny Grey', 
		    'tinylight' => 'Tiny Light',
		    'tinymodern' => 'Tiny Modern', 
		    'modern' => "Modern", 
		    'modernlight' => "Modern Light",
		    'modernoutline' => "Modern Outline", 
		    "metro essbfc-template-fancy" => "Metro Fancy", 
		    "metro essbfc-template-bold" => "Metro Bold",
		    'metrooutline' => 'Framed',
		    'gradient' => 'Gradient',
		    'minimal' => 'Minimal'
		), 'default' => 'metro');
		$structure['animation'] = array('type' => 'select', 'title' => 'Animation', 'description' => 'Animate buttons on hover', 'values' => array('' => 'Without animation', 'pulse' => "Pulse", "down" => "Down", "up" => "Up", "pulse-grow" => "Pulse Grow", "pop" => "Pop", "wobble-horizontal" => "Wobble Horizontal", "wobble-vertical" => "Wobble Vertical", "buzz-out" => "Buzz Out"));
		$structure['nospace'] = array('type' => 'checkbox', 'title' => 'Without space between buttons', 'description' => 'Activate this option if you wish to remove space between single buttons');
		$structure['bgcolor'] = array('type' => 'textbox', 'title' => 'Custom background color', 'description' => 'Provide custom background color for followers counter area');
		$structure['hide_value'] = array('type' => 'checkbox', 'title' => 'Hide number of followers', 'description' => 'Activate this option if you wish to hide number of followers value');
		$structure['hide_text'] = array('type' => 'checkbox', 'title' => 'Hide text followers text', 'description' => 'Activate this option if you wish to hide text for followers below value');
		
		if ($apply_defaults) {
			if (isset($custom_defaults)) {
				$default_options = $custom_defaults;
			}
			else {
				$default_options = self::default_instance_settings();
			}
			foreach ($default_options as $key => $value) {
				$structure[$key]['default_value'] = $value;
			}
		}
		
		return $structure;
	}
	
	/**
	 * 
	 * @param unknown_type $option
	 * @param unknown_type $default
	 * @return Ambigous <string, unknown>
	 */
	public static function get_option($option, $default = '') {
		return essb_followers_option($option, $default);
	}
	
	public static function get_active_networks() {
		$network_list = self::get_option('networks');
	
		return self::clear_deprecated_networks($network_list);
	}
	
	public static function get_active_networks_order() {
		$network_order = self::get_option('networks_order');
	
		$network_order = self::simplify_order_list($network_order);
	
		return self::clear_deprecated_networks($network_order);
	}
	
	/**
	 * Clear networks that are not available anymore
	 * 
	 * @param array $networks
	 * @return unknown[]
	 */
	public static function clear_deprecated_networks($networks = array()) {
	    $r = array();
	    
	    foreach ($networks as $network) {
	        if (!self::is_deprecated_network($network)) {
	            $r[] = $network;
	        }
	    }
	    
	    return $r;
	}
	
	public static function simplify_order_list($order) {
		$result = array();
		
		if (!is_array($order)) {
			$order = array();
		}
		
		foreach ($order as $network) {
			$network_details = explode('|', $network);
			$result[] = $network_details[0];
		}
	
		return $result;
	}
	
	/**
	 * Check if the network is not deprecated. If so don't draw it.
	 * 
	 * @param string $network
	 * @return boolean
	 */
	public static function is_deprecated_network($network = '') {
	    $socials = array();
	    $socials[] = 'google';
	    $socials[] = 'forrst';
	    $socials[] = 'audioboo';
	    $socials[] = 'vine';
	    
	    return in_array($network, $socials);
	}
	
	/**
	 * Generate list of all available networks
	 * @param unknown_type $display_total
	 * 
	 * @since 6.3
	 * Google+ is removed from the list to ensure that it will not appear inside the settings or dispaly
	 * even if it is configured (due to potential issues in display)
	 * 
	 * @since 7.6
	 * Clear: Forrst
	 */
	
	public static function available_social_networks ($display_total = true) {
	
		$socials = array ();
		$socials['facebook'] = 'Facebook';
		$socials['twitter'] = 'Twitter';
		$socials['pinterest'] = 'Pinterest';
		$socials['linkedin'] = 'LinkedIn';
		$socials['github'] = 'GitHub';
		$socials['vimeo'] = 'Vimeo';
		$socials['dribbble'] = 'Dribbble';
		$socials['envato'] = 'Envato';
		$socials['soundcloud'] = 'SoundCloud';
		$socials['behance'] = 'Behance';
		$socials['foursquare'] = 'Foursquare';
		// @deprecated 7.6 $socials['forrst'] = 'Forrst';
		
		$socials['mailchimp'] = 'MailChimp';
		$socials['delicious'] = 'Delicious';
		$socials['instgram'] = 'Instagram';
		$socials['youtube'] = 'YouTube';
		$socials['vk'] = 'VK';
		$socials['rss'] = 'RSS';
		$socials['vine'] = 'Vine';
		$socials['tumblr'] = 'Tumblr';
		$socials['slideshare'] = 'SlideShare';
		$socials['500px'] = '500px';
		$socials['flickr'] = 'Flickr';
		$socials['wp_posts'] = 'WordPress Posts';
		$socials['wp_comments'] = 'WordPress Comments';
		$socials['wp_users'] = 'WordPress Users';
		$socials['audioboo'] = 'Audioboo';
		$socials['steamcommunity'] = 'Steam';
		$socials['weheartit'] = 'WeHeartit';
		$socials['feedly'] = 'Feedly';
		$socials['love'] = 'Love Counter';
		$socials['mailpoet'] = 'MailPoet';
		$socials['mymail'] = 'myMail / Mailster';
		$socials['spotify'] = 'Spotify';
		$socials['twitch'] = 'Twitch';
		$socials['mailerlite'] = 'MailerLite';
		
		// networks added in version 5
		$socials['itunes'] = 'iTunes';
		$socials['deviantart'] = 'Deviantart';
		$socials['paypal'] = 'PayPal';
		$socials['whatsapp'] = 'WhatsApp';
		$socials['tripadvisor'] = 'Tripadvisor';
		$socials['snapchat'] = 'Snapchat';
		$socials['telegram'] = 'Telegram';
		
		$socials['subscribe'] = 'Generic Subscribe Button';
		
		$socials['xing'] = 'Xing';
		$socials['medium'] = 'Medium';
		$socials['patreon'] = 'Patreon';
		$socials['mixer'] = 'Mixer';
		$socials['tiktok'] = 'TikTok';
		$socials['ok'] = 'Odnoklassniki';
		
		$socials['subscribe_form'] = 'Subscribe Form'; // since 7.1
		
		$socials['periscope'] = 'Periscope';
		
		if (has_filter('essb4_follower_networks')) {
			$socials = apply_filters('essb4_follower_networks', $socials);
		}
			
		if ($display_total) {
			$socials['total'] = 'Total Followers Counter';
		}
	
		return $socials;
	}
	
	public static function available_cache_periods () {
	
		$periods = array ();
		$periods[0] = 'Use Default';
		$periods[60] = '1 Hour';
		$periods[120] = '3 Hours';
		$periods[600] = '6 Hours';
		$periods[540] = '9 Hours';
		$periods[720] = '12 Hours';
		$periods[1440] = '1 Day';
		$periods[4320] = '3 Days';
		$periods[7200] = '5 Days';
		$periods[10800] = '7 Days';
		$periods[20160] = '14 Days';
		$periods[43200] = '1 Month';
	
		return $periods;
	}
	
	public static function available_number_formats () {
	
		$format = array ();
		$format['full'] = '1,000, 10,000'; 
		$format['fulldot'] = '1.000, 10.000';
		$format['fullspace'] = '1 000, 10 000'; 
		$format['short'] = '1k, 10k, 100k, 1m'; 
	
		return $format;
	}
	
	/**
	 * Generate options for all social networks inside the Socail Followers Counter
	 * 
	 * @since 6.3
	 * Google+ removed from the settings
	 */
	public static function options_structure() {
	    $settings = array ();
	    
	    /**
	     * @since 7.6 Define the manual value for easy update
	     */
	    $manual_followers_text = 'Number of followers';
	    $manual_followers_custom_text = 'Number of followers or custom text';
	    
	    $settings['facebook']['id'] = array('type' => 'textbox', 'text' => 'Page ID/Name or profile');
	    $settings['facebook']['account_type'] = array('type' => 'select', 'text' => 'Account type', 'values' => array('page' => 'Page', 'followers' => 'Followers'), 'default' => 'page');
	    $settings['facebook']['access_token'] = array('type' => 'textbox', 'text' => 'Access token', 'description' => 'Access token is optional parameter. Generate and fill this parameter only if you are not able to see followers counter without it (usually this is required to be filled when Facebook page has limitation set - for age, country or other). To generate access token please visit this link and follow instructions: <a href="http://tools.creoworx.com/facebook/" target="_blank">http://tools.creoworx.com/facebook/</a>', 'authfield' => true);
	    $settings['facebook']['update_method'] = array('type' => 'select', 'text' => 'Updated Method', 'values' => array('method1' => 'Method #1', 'method2' => 'Method #2'), 'default' => 'method1');
	    $settings['facebook']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Fans');
	    $settings['facebook']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['twitter']['id'] = array('type' => 'textbox', 'text' => 'Username');
	    $settings['twitter']['consumer_key'] = array('type' => 'textbox', 'text' => 'Consumer key', 'authfield' => true);
	    $settings['twitter']['consumer_secret'] = array('type' => 'textbox', 'text' => 'Consumer secret', 'authfield' => true);
	    $settings['twitter']['access_token'] = array('type' => 'textbox', 'text' => 'Access token', 'authfield' => true);
	    $settings['twitter']['access_token_secret'] = array('type' => 'textbox', 'text' => 'Access token secret', 'authfield' => true);
	    $settings['twitter']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['twitter']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['pinterest']['id'] = array('type' => 'textbox', 'text' => 'Username');
	    $settings['pinterest']['text'] = array('type' => 'textbox', 'text' => 'Fans text', 'default' => 'Followers');
	    $settings['pinterest']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    /**
	     * @since 7.6 Clear LinkedIn outdated access values
	     * @since 7.7 Adding personal user profile too
	     */
	    $settings['linkedin']['id'] = array('type' => 'textbox', 'text' => 'LinkedIn Company ID or Profile URL', "description" => "Example: company ID: envato or profile url: https://www.linkedin.com/in/applications-creo-bb06a29a");
	    $settings['linkedin']['type'] = array('type' => 'select', 'text' => 'Type', 'values' => array('company' => 'Company', 'profile' => 'Profile'));
	    $settings['linkedin']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['linkedin']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['github']['id'] = array('type' => 'textbox', 'text' => 'Username');
	    $settings['github']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['github']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['vimeo']['id'] = array('type' => 'textbox', 'text' => 'Channel name/Username');
	    $settings['vimeo']['account_type'] = array('type' => 'select', 'text' => 'Profile type', 'values' => array('channel' => 'Channel', 'user' => 'User'));
	    $settings['vimeo']['access_token'] = array('type' => 'textbox', 'text' => 'Access token', 'description' => 'Access token key is required only if you display information for user. To generate this key you need to go to Vimeo Developer Center and create application <a href="https://developer.vimeo.com/" target="_blank">https://developer.vimeo.com/</a>', 'authfield' => true);
	    $settings['vimeo']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Subscribers');
	    $settings['vimeo']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['dribbble']['id'] = array('type' => 'textbox', 'text' => 'Username');
	    $settings['dribbble']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['dribbble']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['envato']['id'] = array('type' => 'textbox', 'text' => 'Username');
	    $settings['envato']['site'] = array('type' => 'select', 'text' => 'Envato site', 'values' => array('themeforest' => 'Themeforest', 'codecanyon' => 'Codecanyon', '3docean' => '3docean', 'activeden' => 'Activeden', 'audiojungle' => 'Audiojungle', 'graphicriver' => 'Graphicriver', 'photodune' => 'Photodune', 'videohive' => 'Videohive'));
	    $settings['envato']['ref'] = array('type' => 'textbox', 'text' => 'Referral username', 'description' => 'Provide different username that will appear in the ref link to site');
	    $settings['envato']['text'] =array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['envato']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['soundcloud']['id'] = array('type' => 'textbox', 'text' => 'Username');
	    $settings['soundcloud']['api_key'] = array('type' => 'textbox', 'text' => 'API Key', 'authfield' => true);
	    $settings['soundcloud']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Followers');
	    $settings['soundcloud']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['behance']['id'] = array('type' => 'textbox', 'text' => 'Username');
	    $settings['behance']['api_key'] = array('type' => 'textbox', 'text' => 'API Key', 'authfield' => true);
	    $settings['behance']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['behance']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['foursquare']['api_key'] = array('type' => 'textbox', 'text' => 'API Key');
	    $settings['foursquare']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Followers');
	    $settings['foursquare']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['forrst']['id'] = array('type' => 'textbox', 'text' => 'Username');
	    $settings['forrst']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['forrst']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['mailchimp']['list_id'] = array('type' => 'textbox', 'text' => 'List ID');
	    $settings['mailchimp']['api_key'] = array('type' => 'textbox', 'text' => 'API Key');
	    $settings['mailchimp']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Subscribers');
	    $settings['mailchimp']['list_url'] = array('type' => 'textbox', 'text' => 'List URL address', 'description' => 'Provide subscribe form address where users will be redirected when click on button');
	    $settings['mailchimp']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['delicious']['id'] = array('type' => 'textbox', 'text' => 'Username');
	    $settings['delicious']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['delicious']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['instgram']['username'] = array('type' => 'textbox', 'text' => 'Username');
	    $settings['instgram']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['instgram']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['youtube']['id'] = array('type' => 'textbox', 'text' => 'Channel/User');
	    $settings['youtube']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Subscribers');
	    $settings['youtube']['account_type'] = array('type' => 'select', 'text' => 'Account Type', 'values' => array('channel' => 'Channel', 'user' => 'User'));
	    $settings['youtube']['icon_type'] = array('type' => 'select', 'text' => 'Icon Type', 'values' => array('' => 'YouTube Logo', 'play' => 'YouTube Play Icon'));
	    $settings['youtube']['url_type'] = array('type' => 'select', 'text' => 'Channel URL Type', 'values' => array('channel' => 'Full channel url (/channel/)', 'c' => 'Short channel url (/c/)'), "description" => "Choose channel url type according to how you see your address in browser. Default is long format channel which works in more than 90%.");
	    $settings['youtube']['api_key'] = array('type' => 'textbox', 'text' => 'API Key', 'description' => 'If you have set a Google+ API key you can use it same here - all you need is to enable access to YouTube API in Google Console.', 'authfield' => true);
	    $settings['youtube']['url'] = array('type' => 'textbox', 'text' => 'Custom Channel URL', 'description' => 'The visit channel URL is automatically generated based on settings above. If you wish to fill a custom URL for button click (example branded channel URL) you can do this here. The supported URL can also be fully custom (example: landing page)');
	    $settings['youtube']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['vk']['id'] = array('type' => 'textbox', 'text' => 'Your VK.com ID number or Community ID/Name');
	    $settings['vk']['account_type'] = array('type' => 'select', 'text' => 'Profile type', 'values' => array('profile' => 'Profile', 'community' => 'Community ID/Name'));
	    $settings['vk']['access_token'] = array('type' => 'textbox', 'text' => 'Access Token Key', 'description' => 'Reading data from that network require access data key. You may not see number of followers appearing if that key is not filled or properly generarated. You can refer to the network support for key generation if you are not sure how this happens.', 'authfield' => true);
	    $settings['vk']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['vk']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['rss']['link'] = array('type' => 'textbox', 'text' => 'URL address of your feed');
	    $settings['rss']['count'] = array('type' => 'textbox', 'text' => 'Value of subsribers');
	    $settings['rss']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Subscribers');
	    $settings['rss']['feedblitz'] = array('type' => 'textbox', 'text' => 'feedblitz.com counter address', 'description' => 'Optional. If you have feedblitz account and wish to display automatically value of subscribers fill here the counter address.');
	    
	    $settings['vine']['email'] = array('type' => 'textbox', 'text' => 'Email');
	    $settings['vine']['password'] = array('type' => 'textbox', 'text' => 'Password');
	    $settings['vine']['username'] = array('type' => 'textbox', 'text' => 'Username');
	    $settings['vine']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['vine']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['tumblr']['basename'] = array('type' => 'textbox', 'text' => 'Blog basename', 'description' => 'Your blog base name looks like appscreo.tumblr.com');
	    $settings['tumblr']['api_key'] = array('type' => 'textbox', 'text' => 'Consumer Key', 'authfield' => true);
	    $settings['tumblr']['api_secret'] = array('type' => 'textbox', 'text' => 'Consumer Secret', 'authfield' => true);
	    $settings['tumblr']['access_token'] = array('type' => 'textbox', 'text' => 'Access Token', 'authfield' => true);
	    $settings['tumblr']['access_token_secret'] = array('type' => 'textbox', 'text' => 'Access Token Secret', 'authfield' => true);
	    $settings['tumblr']['text'] = array('type' => 'textbox','text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['tumblr']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['slideshare']['username'] = array('type' => 'textbox', 'text' => 'Username');
	    $settings['slideshare']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Followers');
	    $settings['slideshare']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['500px']['username'] = array('type' => 'textbox', 'text' => 'Username');
	    $settings['500px']['api_key'] = array('type' => 'textbox', 'text' => 'API Key', 'authfield' => true);
	    $settings['500px']['api_secret'] = array('type' => 'textbox', 'text' => 'API Secret', 'authfield' => true);
	    $settings['500px']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['500px']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['flickr']['id'] = array('type' => 'textbox', 'text' => 'Group slug');
	    $settings['flickr']['api_key'] = array('type' => 'textbox', 'text' => 'API Key', 'authfield' => true);
	    $settings['flickr']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Followers');
	    $settings['flickr']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['wp_posts']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Posts');
	    $settings['wp_posts']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    $settings['wp_posts']['url'] = array('type' => 'textbox', 'text' => 'URL address when user click on total button');
	    
	    $settings['wp_comments']['text'] = array('type' => 'textbox','text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Comments');
	    $settings['wp_comments']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    $settings['wp_comments']['url'] = array('type' => 'textbox', 'text' => 'URL address when user click on total button');
	    
	    $settings['wp_users']['text'] = array('type' => 'textbox','text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Users');
	    $settings['wp_users']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    $settings['wp_users']['url'] = array('type' => 'textbox', 'text' => 'URL address when user click on total button');
	    
	    $settings['audioboo']['id'] = array('type' => 'textbox', 'text' => 'Username');
	    $settings['audioboo']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['audioboo']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['steamcommunity']['id'] = array('type' => 'textbox', 'text' => 'Social network profile ID');
	    $settings['steamcommunity']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['steamcommunity']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['weheartit']['id'] = array('type' => 'textbox', 'text' => 'Username');
	    $settings['weheartit']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['weheartit']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['feedly']['url'] = array('type' => 'textbox', 'text' => 'Feedly URL address');
	    $settings['feedly']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Subscribers');
	    $settings['feedly']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['total']['url'] = array('type' => 'textbox', 'text' => 'URL address when user click on total button');
	    $settings['total']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Total fans');
	    
	    $settings['love']['url'] = array('type' => 'textbox', 'text' => 'URL address when user click on love button');
	    $settings['love']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Loves');
	    $settings['love']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['spotify']['id'] = array('type' => 'textbox', 'text' => 'Spotify URI');
	    $settings['spotify']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['spotify']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['twitch']['id'] = array('type' => 'textbox', 'text' => 'Channel Name');
	    $settings['twitch']['api'] = array('type' => 'textbox', 'text' => 'Access Token');
	    $settings['twitch']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['twitch']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['mymail']['id'] = array('type' => 'select', 'text' => 'Choose List', 'values' => self::mymail_get_lists());
	    $settings['mymail']['url'] = array('type' => 'textbox', 'text' => 'List URL');
	    $settings['mymail']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Subscribers');
	    $settings['mymail']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $mailpoet_lists = self::mailpoet_get_lists();
	    $mailpoet_lists = array_merge( array( array( 'list_id' => 'all', 'name' => esc_html__(' Total Subscribers from All Lists', ESSB3_TEXT_DOMAIN ))), $mailpoet_lists);
	    $mailpoet_lists = array_merge( array( array( 'list_id' => '', 'name' => esc_html__(' ', 'essb' ))), $mailpoet_lists);
	    
	    $parsed_lists = array();
	    foreach ($mailpoet_lists as $list) {
	        $list_id = isset($list['list_id']) ? $list['list_id'] : '';
	        if ($list_id == '') {
	            $list_id = isset($list['id']) ? $list['id'] : '';
	        }
	        $list_name = isset($list['name']) ? $list['name'] : '';
	        $parsed_lists[$list_id] = $list_name;
	    }
	    $settings['mailpoet']['id'] = array('type' => 'select', 'text' => 'Choose List', 'values' => $parsed_lists);
	    $settings['mailpoet']['url'] = array('type' => 'textbox', 'text' => 'List URL');
	    $settings['mailpoet']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)', 'default' => 'Followers');
	    $settings['mailpoet']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    $settings['mailerlite']['id'] = array('type' => 'textbox', 'text' => 'URL when button is clicked');
	    $settings['mailerlite']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Followers');
	    $settings['mailerlite']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_text);
	    
	    /** Settings for networks added in version 5 - no counter avaialble */
	    $settings['itunes']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Posts');
	    $settings['itunes']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_custom_text);
	    $settings['itunes']['url'] = array('type' => 'textbox', 'text' => 'URL address to network profile (address where users will go once button is clicked)');
	    
	    $settings['deviantart']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Posts');
	    $settings['deviantart']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_custom_text);
	    $settings['deviantart']['url'] = array('type' => 'textbox', 'text' => 'URL address to network profile (address where users will go once button is clicked)');
	    
	    $settings['paypal']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Posts');
	    $settings['paypal']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_custom_text);
	    $settings['paypal']['url'] = array('type' => 'textbox', 'text' => 'URL address to network profile (address where users will go once button is clicked)');
	    
	    $settings['whatsapp']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Posts');
	    $settings['whatsapp']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_custom_text);
	    $settings['whatsapp']['url'] = array('type' => 'textbox', 'text' => 'URL address to network profile (address where users will go once button is clicked)');
	    
	    $settings['tripadvisor']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Posts');
	    $settings['tripadvisor']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_custom_text);
	    $settings['tripadvisor']['url'] = array('type' => 'textbox', 'text' => 'URL address to network profile (address where users will go once button is clicked)');
	    
	    $settings['snapchat']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Posts');
	    $settings['snapchat']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_custom_text);
	    $settings['snapchat']['url'] = array('type' => 'textbox', 'text' => 'URL address to network profile (address where users will go once button is clicked)');
	    
	    $settings['telegram']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Posts');
	    $settings['telegram']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_custom_text);
	    $settings['telegram']['url'] = array('type' => 'textbox', 'text' => 'URL address to network profile (address where users will go once button is clicked)');
	    
	    $settings['subscribe']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Subscribers');
	    $settings['subscribe']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_custom_text);
	    $settings['subscribe']['url'] = array('type' => 'textbox', 'text' => 'URL address to network profile (address where users will go once button is clicked)');
	    
	    $settings['xing']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Followers');
	    $settings['xing']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_custom_text);
	    $settings['xing']['url'] = array('type' => 'textbox', 'text' => 'URL address to network profile (address where users will go once button is clicked)');
	    
	    $settings['medium']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Followers');
	    $settings['medium']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_custom_text);
	    $settings['medium']['url'] = array('type' => 'textbox', 'text' => 'URL address to network profile (address where users will go once button is clicked)');
	    
	    $settings['patreon']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Followers');
	    $settings['patreon']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_custom_text);
	    $settings['patreon']['url'] = array('type' => 'textbox', 'text' => 'URL address to network profile (address where users will go once button is clicked)');
	    
	    $settings['mixer']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Followers');
	    $settings['mixer']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_custom_text);
	    $settings['mixer']['url'] = array('type' => 'textbox', 'text' => 'URL address to network profile (address where users will go once button is clicked)');
	    
	    $settings['tiktok']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Followers');
	    $settings['tiktok']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_custom_text);
	    $settings['tiktok']['url'] = array('type' => 'textbox', 'text' => 'URL address to network profile (address where users will go once button is clicked)');
	    
	    $settings['ok']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Followers');
	    $settings['ok']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_custom_text);
	    $settings['ok']['url'] = array('type' => 'textbox', 'text' => 'URL address to network profile (address where users will go once button is clicked)');
	    
	    $settings['subscribe_form']['design'] = array('type' => 'select', 'text' => 'Design', 'values' => essb_optin_designs());
	    $settings['subscribe_form']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Followers');
	    $settings['subscribe_form']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_custom_text);
	    
		$settings['periscope']['text'] = array('type' => 'textbox', 'text' => 'Text below number', 'description' => 'Text that will appear below number of followers (fans, likes, subscribers, followers and etc.)','default' => 'Followers');
		$settings['periscope']['uservalue'] = array('type' => 'textbox', 'text' => $manual_followers_custom_text);
		$settings['periscope']['url'] = array('type' => 'textbox', 'text' => 'URL address to network profile (address where users will go once button is clicked)');
		
		
		if (has_filter('essb4_follower_networks_settings')) {
			$settings = apply_filters('essb4_follower_networks_settings', $settings);
		}
		
		return $settings;
	}
	

	public static function create_default_options_from_structure($options) {
		$structure = self::options_structure();
		
		foreach ($structure as $network => $data) {
			$base_network_option_id = "essb3fans_".$network."_";
			foreach ($data as $key => $setup) {
				$default_text = isset($setup['default']) ? $setup['default'] : '';
				
				if (!empty($default_text)) {
					$options[$base_network_option_id.$key] = $default_text;
				}
			}
		}
		
		$options['essb3fans_update'] = 1440;
		$options['essb3fans_format'] = 'short';
		
		return $options;
	}
		
	public static function mailpoet_total_subscribers(){
		if( class_exists( 'WYSIJA' ) ){
			$config = WYSIJA::get('config','model');
			$result = $config->getValue('total_subscribers');
			return $result;
		}
	}
	
	//Get Mail Lists
	public static function mailpoet_get_lists(){
		
		if (class_exists('MailPoet\API\API')) {
			$subscription_lists = \MailPoet\API\API::MP('v1')->getLists();
			return $subscription_lists;
		}
		
		else if( class_exists( 'WYSIJA' ) ){
			$helper_form_engine = WYSIJA::get('form_engine', 'helper');
			$lists = $helper_form_engine->get_lists();
			return $lists ;
		}
		else {
			return array();
		}
	}
	
	//Get Subscribers of Specific List
	public static function mailpoet_get_list_users( $list ){
		if( class_exists( 'WYSIJA' ) ){
			$model_user_list = WYSIJA::get('user_list', 'model');$query = 'SELECT COUNT(*) as count
			FROM ' . '[wysija]' . $model_user_list->table_name . '
			WHERE list_id = ' . $list ;
	
			$result = $model_user_list->query('get_res', $query);
			return $result[0][ 'count' ];
		}
	}
	
	public static function mymail_get_lists() {
		
		if (function_exists('mailster')) {
			$lists = mailster('lists')->get();
			foreach ($lists as $list) {
				$result[$list->ID] = $list->name;
			}
					
			return $result;
		}
	}
	
}

if (!function_exists('essb_update_available_follower_networks_in_settings')) {
	add_filter('essb4_followers_networks_update_list', 'essb_update_available_follower_networks_in_settings');
	
	function essb_update_available_follower_networks_in_settings($list_of_networks) {
		$current_networks = ESSBSocialFollowersCounterHelper::available_social_networks(false);
		$all_networks = array();
		foreach ($current_networks as $network => $network_name) {
			$key = $network.'|'.$network_name;
				
			if (!in_array($key, $list_of_networks)) {
				$list_of_networks[] = $key;
			}
				
			$all_networks[] = $key;
		}
		
		return $list_of_networks;
		
	}
}

if (!function_exists('essb_followers_option')) {
	function essb_followers_option($option, $default = '') {
		global $essb_socialfans_options;
	
		$option = 'essb3fans_'.$option;
	
		$value = isset($essb_socialfans_options[$option]) ? $essb_socialfans_options[$option] : '';
		if ($value == "-") {
			$value = "";
		}
	
		if (empty($value) && !empty($default)) {
			$value = $default;
		}
	
		return $value;
	}
}

