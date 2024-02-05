<?php
/**
 * Social Share Buttons Settings Screen
 *
 * @package EasySocialShareButtons
 * @since 1.0
 */

$share_networks_tag = 'share-1|networks';
$share_networks_advanced = 'share-1|advanced';

if (class_exists('ESSBControlCenter')) {
    
    if (!ESSBControlCenter::is_new_version()) {
    	ESSBControlCenter::register_sidebar_section_menu('social', 'share-1', esc_html__('Networks', 'essb'));
    	
    	ESSBControlCenter::register_sidebar_section_menu_sub('social', 'share-1', 'networks_title', array('type' => 'title', 'value' => esc_html__('Networks', 'essb')));
    	ESSBControlCenter::register_sidebar_section_menu_sub('social', 'share-1', 'networks_menu_split', array('type' => 'splitter', 'value' => esc_html__('Navigate to', 'essb'), 'class' => 'no-pt'));
    	ESSBControlCenter::register_sidebar_section_menu_sub('social', 'share-1', 'networks_menu', array('type' => 'menu', 'value' => array('networks' => esc_html__('Social Networks', 'essb'), 'advanced' => esc_html__('Additional Network Options', 'essb'))));
    	ESSBControlCenter::register_sidebar_section_menu_sub('social', 'share-1', 'networks_menu_split1', array('type' => 'splitter', 'value' => ''));
    	ESSBControlCenter::register_sidebar_section_menu_sub('social', 'share-1', 'networks_desc2', array('type' => 'description', 'value' => esc_html__('A few of the social networks have additional options. Those additional options can help tune up the work of each button or to provide additional functionality. All available options for networks you can find inside the Additional Network Options tab.', 'essb')));
    	ESSBControlCenter::register_sidebar_section_menu_sub('social', 'share-1', 'networks_help_btn1', array('type' => 'button', 'class' => 'inner-help', 'value' => array( 'text' => esc_html__('Help with network setup', 'essb'), 'url' => 'https://docs.socialsharingplugin.com/knowledgebase/social-sharing-setup-social-networks/', 'target' => '_blank')));
    	ESSBControlCenter::register_sidebar_section_menu_sub('social', 'share-1', 'networks_help_btn2', array('type' => 'button', 'class' => 'inner-help', 'value' => array( 'text' => esc_html__('Working with More button', 'essb'), 'url' => 'https://docs.socialsharingplugin.com/knowledgebase/social-sharing-how-to-set-up-more-button/', 'target' => '_blank')));
    	ESSBControlCenter::register_sidebar_section_menu_sub('social', 'share-1', 'networks_help_btn3', array('type' => 'button', 'class' => 'inner-help', 'value' => array( 'text' => esc_html__('Help with Pinterest share modes', 'essb'), 'url' => 'https://docs.socialsharingplugin.com/knowledgebase/pinterest-share-button-modes-what-is-the-difference-and-which-of-them-to-use-on-site/', 'target' => '_blank')));
    }
    else {
        ESSBControlCenter::register_sidebar_section_menu('social', 'share-networks', esc_html__('Global Networks', 'essb'));
        ESSBControlCenter::register_sidebar_section_menu('social', 'share-advanced', esc_html__('Global Network Options', 'essb'));
        $share_networks_tag = 'share-networks';
        $share_networks_advanced = 'share-advanced';
    }
    
	ESSBControlCenter::register_sidebar_section_menu('social', 'share-2', ESSBControlCenter::is_new_version() ? esc_html__('Global Template & Style', 'essb') : 'Template & Style');
	ESSBControlCenter::register_sidebar_section_menu('social', 'sharecnt', esc_html__('Share Counter Setup', 'essb'));
	
	if (!essb_option_bool_value('deactivate_module_pinterestpro')) {
		ESSBControlCenter::register_sidebar_section_menu('social', 'pinpro', esc_html__('Pinterest Pro', 'essb'));
	}
	
	if (!essb_option_bool_value('deactivate_module_shareoptimize')) {
		ESSBControlCenter::register_sidebar_section_menu('social', 'optimize', esc_html__('Sharing Optimization', 'essb'));
	}
	
	if (!essb_option_bool_value('deactivate_module_aftershare')) {
		ESSBControlCenter::register_sidebar_section_menu('social', 'after-share', esc_html__('After Share Events', 'essb'));
	}
	
	if (!essb_option_bool_value('deactivate_module_shorturl')) {
		ESSBControlCenter::register_sidebar_section_menu('social', 'shorturl', esc_html__('Short URL', 'essb'));
	}
	
	if (!essb_option_bool_value('deactivate_module_analytics') || !essb_option_bool_value('deactivate_module_metrics') || !essb_option_bool_value('deactivate_module_conversions') || !essb_option_bool_value('deactivate_module_google_analytics')) {
		ESSBControlCenter::register_sidebar_section_menu('social', 'analytics', esc_html__('Analytics', 'essb'));
	}
	
	if (!essb_option_bool_value('deactivate_module_affiliate')) {
		ESSBControlCenter::register_sidebar_section_menu('social', 'affiliate', esc_html__('Affiliate Integration', 'essb'));
	}
	
	if (!essb_option_bool_value('deactivate_module_customshare')) {
		ESSBControlCenter::register_sidebar_section_menu('social', 'customshare', esc_html__('Custom Share', 'essb'));
	}
	
	if (!essb_option_bool_value('deactivate_module_message')) {
		ESSBControlCenter::register_sidebar_section_menu('social', 'message', esc_html__('Message Before/Above Buttons', 'essb'));
	}
	
	if (!essb_option_bool_value('deactivate_ctt')) {
		ESSBControlCenter::register_sidebar_section_menu('social', 'clicktotweet', esc_html__('Click to Tweet', 'essb'));
	}
	
	if (!essb_option_bool_value('deactivate_custombuttons')) {
		ESSBControlCenter::register_sidebar_section_menu('social', 'custombuttons', esc_html__('Custom Share Buttons', 'essb'));
	}
	
	if (!essb_option_bool_value('deactivate_module_conversions')) {
		if (essb_option_bool_value('conversions_lite_run')) {
			ESSBControlCenter::register_sidebar_section_menu('conversions', 'share', esc_html__('General Share Conversions', 'essb'));
			ESSBControlCenter::register_sidebar_section_menu('conversions', 'shareposts', esc_html__('Post Share Conversions', 'essb'));
		}
	
		if (essb_option_bool_value('conversions_subscribe_lite_run')) {
			ESSBControlCenter::register_sidebar_section_menu('conversions', 'subscribe', esc_html__('Subscribe Forms', 'essb'));
		}
	}
	
	if (ESSBControlCenter::feature_group_has_deactivated('share') || ESSBControlCenter::feature_group_has_deactivated('display')) {
		ESSBControlCenter::register_sidebar_section_menu('othersharing', 'other', esc_html__('Additional Features', 'essb'));
		ESSBOptionsStructureHelper::field_component('othersharing', 'other', 'essb5_advanced_other_features_activate', 'false');
		ESSBOptionsStructureHelper::field_component('othersharing', 'other', 'essb5_advanced_other_features_where_activate', 'false');
	}
}

ESSBOptionsStructureHelper::menu_item('social', 'share-1', esc_html__('Networks', 'essb'), ' ti-sharethis');
ESSBOptionsStructureHelper::menu_item('social', 'share-2', esc_html__('Template & Style', 'essb'), ' ti-sharethis');

ESSBOptionsStructureHelper::menu_item('social', 'sharecnt', esc_html__('Share Counters Setup', 'essb'), ' ti-stats-up');
if (!essb_option_bool_value('deactivate_module_pinterestpro')) {
	ESSBOptionsStructureHelper::menu_item('social', 'pinpro', esc_html__('Pinterest Pro', 'essb'), ' ti-new-window');
}

if (!essb_option_bool_value('deactivate_module_shareoptimize')) {
	ESSBOptionsStructureHelper::menu_item('social', 'optimize', esc_html__('Sharing Optimization', 'essb'), ' ti-new-window');
}
if (!essb_option_bool_value('deactivate_module_aftershare')) {
	ESSBOptionsStructureHelper::menu_item('social', 'after-share', esc_html__('After Share Events', 'essb'), ' ti-layout-cta-left');
}

if (!essb_option_bool_value('deactivate_module_shorturl')) {
	ESSBOptionsStructureHelper::menu_item('social', 'shorturl', esc_html__('Short URL', 'essb'), ' ti-new-window');
}

if (!essb_option_bool_value('deactivate_module_analytics') || !essb_option_bool_value('deactivate_module_metrics') || !essb_option_bool_value('deactivate_module_conversions') || !essb_option_bool_value('deactivate_module_google_analytics')) {
	ESSBOptionsStructureHelper::menu_item('social', 'analytics', esc_html__('Analytics', 'essb'), ' ti-stats-up');
}

if (!essb_option_bool_value('deactivate_module_affiliate')) {
	ESSBOptionsStructureHelper::menu_item('social', 'affiliate', esc_html__('Affiliate Integration', 'essb'), ' ti-new-window');
}

if (!essb_option_bool_value('deactivate_module_customshare')) {
	ESSBOptionsStructureHelper::menu_item('social', 'customshare', esc_html__('Custom Share', 'essb'), ' ti-new-window');
}

if (!essb_option_bool_value('deactivate_module_message')) {
	ESSBOptionsStructureHelper::menu_item('social', 'message', esc_html__('Message Before/Above Buttons', 'essb'), ' ti-new-window');
}

if (!essb_option_bool_value('deactivate_module_conversions')) {
	if (essb_option_bool_value('conversions_lite_run')) {
		ESSBOptionsStructureHelper::menu_item('conversions', 'share', esc_html__('Share Buttons', 'essb'), ' ti-new-window');
	}
	
	if (essb_option_bool_value('conversions_subscribe_lite_run')) {
		ESSBOptionsStructureHelper::menu_item('conversions', 'subscribe', esc_html__('Subscribe Forms', 'essb'), ' ti-new-window');
	}
}

ESSBOptionsStructureHelper::help('social', $share_networks_tag, esc_html__('How to select social share buttons for your website – global share buttons, per position share buttons, etc.', 'essb'), '', array('Help with Style Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/social-sharing-setup-social-networks/'));

// share-1 stucture
if (!essb_option_bool_value('user_fixed_networks') && essb_postions_with_custom_networks5(true) != '') {
    ESSBOptionsStructureHelper::field_func('social', $share_networks_tag, 'essb5_custom_position_networks', '', '');
}

ESSBOptionsStructureHelper::field_func('social', $share_networks_tag, 'essb5_main_network_selection', '', '');

ESSBOptionsStructureHelper::advanced_settings_panel_open('social', $share_networks_tag);
$network_sort_order = array("" => esc_html__("User provided order", "essb"), "shares" => esc_html__("Sort dynamically by number of shares", "essb"));
ESSBOptionsStructureHelper::field_select('social', $share_networks_tag, 'user_sort', esc_html__('Order share buttons\' appearance', 'essb'), esc_html__('Dynamic sort requires share counters to be enabled. Networks without share value may not appear.', 'essb'), $network_sort_order);
ESSBOptionsStructureHelper::field_switch('social', $share_networks_tag, 'user_fixed_networks', esc_html__('Use global networks for all display positions', 'essb'), esc_html__('Set the list to be used from the plugin for all display positions. Enable if you do not need to choose an individual list on specific positions of your website.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
ESSBOptionsStructureHelper::advanced_settings_panel_close('social', $share_networks_tag);

ESSBOptionsStructureHelper::field_component('social', $share_networks_tag, 'essb5_advanced_deactivate_networks_button', 'false');


ESSBOptionsStructureHelper::help('social', $share_networks_advanced, esc_html__('How to select social share buttons for your website – global share buttons, per position share buttons, etc.', 'essb'), '', array('Help with Style Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/additional-network-options/'));
ESSBOptionsStructureHelper::hint('social', $share_networks_advanced, '', esc_html__('A few of the networks that are available on your site contain additional settings. With the help of those settings, you can change the work of the listed networks (example: configure additional X (formerly Twitter) settings).', 'essb'));

if (essb_is_active_social_network('pinterest')) {
    ESSBControlCenter::register_help_link('pinterest_sniff_disable', 'https://docs.socialsharingplugin.com/knowledgebase/pinterest-share-button-modes-what-is-the-difference-and-which-of-them-to-use-on-site/');
	ESSBOptionsStructureHelper::panel_start('social', $share_networks_advanced, esc_html__('Pinterest', 'essb'), '', 'fa21 essb_icon_pinterest', array("mode" => "toggle", 'state' => 'closed'));
	ESSBOptionsStructureHelper::field_switch('social', $share_networks_advanced, 'pinterest_sniff_disable', esc_html__('Disable Pin of any image from the page', 'essb'), 
			esc_html__('The default Pinterest button mode will show a picker of all images on-page. If you need to provide a custom image for Pin with the description you can deactivate the Pin of any image and make settings on each post (default is the featured image).', 'essb'), 
			'', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '');
	
	ESSBOptionsStructureHelper::field_switch('social', $share_networks_advanced, 'pinterest_using_api', esc_html__('My website already uses Pinterest API', 'essb'), esc_html__('Enable the option if your Pinterest share button appears broken.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '');
	ESSBOptionsStructureHelper::field_switch('social', $share_networks_advanced, 'pinterest_save_anyimage', esc_html__('Include Pinterest "Save Image" button', 'essb'), esc_html__('This function adds the default Pinterest save button. To get better control and design options you can use the Pinterest Pro button instead.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '');
	ESSBOptionsStructureHelper::panel_end('social', $share_networks_advanced);
}

if (essb_is_active_social_network('twitter')) {
	ESSBOptionsStructureHelper::panel_start('social', $share_networks_advanced, esc_html__('X (formerly Twitter)', 'essb'), '', 'fa21 essb_icon_twitter', array("mode" => "toggle", 'state' => 'closed'));
	ESSBOptionsStructureHelper::field_select('social', $share_networks_advanced, 'share_twitter_icon_type', esc_html__('X (formerly Twitter) icon and color scheme', 'essb'), '', array('' => 'Twitter', 'x' => 'X'));
	ESSBOptionsStructureHelper::field_textbox_stretched('social', $share_networks_advanced, 'twitteruser', esc_html__('Username', 'essb'), esc_html__('Enter your X (formerly Twitter) username. This is used for X (formerly Twitter) share functionality (via @username).', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_stretched('social', $share_networks_advanced, 'twitterhashtags', esc_html__('Hashtags', 'essb'), esc_html__('Enter default hashtags for each Tweet. You can set one or multiple separated with comma (example: tag1,tag2,tag3).', 'essb'));
	ESSBOptionsStructureHelper::field_switch('social', $share_networks_advanced, 'twitter_message_tags_to_hashtags', esc_html__('Generate hashtags from post tags', 'essb'), esc_html__('Automatically transform post tags into hashtags in the Tweet. Use this with caution due to the character limitation in the Tweet.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));

	ESSBOptionsStructureHelper::field_heading('social', $share_networks_advanced, 'heading6', esc_html__('Automated Tweet Message Optimization', 'essb'));
	ESSBOptionsStructureHelper::field_switch('social', $share_networks_advanced, 'twitter_message_optimize', esc_html__('Enable Optimization', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	$listOfOptions = array("1" => "Remove hashtags, remove via username, truncate message", "2" => "Remove via username, remove hashtags, truncate message", "3" => "Remove via username, truncate message", "4" => "Remove hashtags, truncate message", "5" => "Truncate only message");
	ESSBOptionsStructureHelper::field_select('social', $share_networks_advanced, 'twitter_message_optimize_method', esc_html__('Method of optimization', 'essb'), '', $listOfOptions);
	ESSBOptionsStructureHelper::field_switch('social', $share_networks_advanced, 'twitter_message_optimize_dots', esc_html__('Add read more dots when truncate message', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::panel_end('social', $share_networks_advanced);
}

if (essb_is_active_social_network('facebook')) {
	ESSBOptionsStructureHelper::panel_start('social', $share_networks_advanced, esc_html__('Facebook', 'essb'), '', 'fa21 essb_icon_facebook', array("mode" => "toggle", 'state' => 'closed'));
	ESSBOptionsStructureHelper::field_switch('social', $share_networks_advanced, 'facebookadvanced', esc_html__('Use Facebook advanced sharing', 'essb') . essb_generate_expert_badge(), esc_html__('Advanced sharing requires having a valid app for a website created in the Facebook Developer Center. Facebook will limit posting in the share screen to personal accounts only if you enable the Advanced Sharing feature.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_stretched('social', $share_networks_advanced, 'facebookadvancedappid', esc_html__('Facebook application ID for advanced sharing', 'essb'), '');
	ESSBOptionsStructureHelper::panel_end('social', $share_networks_advanced);
}

if (essb_is_active_social_network('messenger')) {
	ESSBOptionsStructureHelper::panel_start('social', $share_networks_advanced, esc_html__('Facebook Messenger', 'essb'), '', 'fa21 essb_icon_messenger', array("mode" => "toggle", 'state' => 'closed'));
	ESSBOptionsStructureHelper::field_textbox_stretched('social', $share_networks_advanced, 'fbmessengerapp', esc_html__('Facebook Application ID:', 'essb'), esc_html__('Facebook Application ID connected with your site is required to make messenger sharing work. If you use Facebook Advanced Sharing feature then it is not needed to fill this parameter as application is already applied into Facebook Advanced Sharing settings', 'essb'));
	ESSBOptionsStructureHelper::panel_end('social', $share_networks_advanced);
}

if (essb_is_active_social_network('whatsapp')) {
	ESSBOptionsStructureHelper::panel_start('social', $share_networks_advanced, esc_html__('WhatsApp', 'essb'), '', 'fa21 essb_icon_whatsapp', array("mode" => "toggle", 'state' => 'closed'));
	ESSBOptionsStructureHelper::field_switch('social', $share_networks_advanced, 'whatsapp_api', esc_html__('Use WhatsApp web (for desktop and mobile)', 'essb'), esc_html__('Enable share command via the WhatsApp site. The option will allow for desktop users to be redirected on the WhatsApp for Web version (WhatsApp currently does not support WhatsApp Web for Firefox browser).', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::panel_end('social', $share_networks_advanced);
}

if (essb_is_active_social_network('more')) {
	ESSBOptionsStructureHelper::panel_start('social', $share_networks_advanced, esc_html__('More Button (Open Additional/All Networks)', 'essb'), '', 'fa21 essb_icon_more', array("mode" => "toggle", 'state' => 'closed'));
	$more_options = array ("1" => "Display all active networks after more button", "2" => "Display all social networks as pop up", "3" => "Display only active social networks as pop up", "4" => "Display all active networks after more button in popup" );
	ESSBOptionsStructureHelper::field_select('social', $share_networks_advanced, 'more_button_func', esc_html__('More button', 'essb'), esc_html__('Select networks that you wish to appear in your list. With drag and drop you can rearrange them.', 'essb'), essb_available_more_button_commands());
	$more_options = array ("plus" => "Plus icon", "dots" => "Dots icon" );
	
	$select_values = array('plus' => array('title' => 'Plus Icon', 'content' => '<i class="essb_icon_more"></i>'),
			'dots' => array('title' => 'Dots Icon', 'content' => '<i class="essb_icon_more_dots"></i>'));
	ESSBOptionsStructureHelper::field_toggle('social', $share_networks_advanced, 'more_button_icon', esc_html__('More button icon', 'essb'), esc_html__('Select more button icon style. You can choose from default + symbol or dots symbol', 'essb'), $select_values);
	
	$more_options = array ("" => "Classic Style", "modern" => "Modern Style" );
	
	if (essb_option_bool_value('disable_more_button_popstyle')) {
	    $more_options = array ("" => "Classic Style" );
	}
	
	ESSBOptionsStructureHelper::field_select('social', $share_networks_advanced, 'more_button_popstyle', esc_html__('More button pop up style', 'essb'), esc_html__('Choose the style of your pop up with social networks', 'essb'), $more_options);
	ESSBOptionsStructureHelper::field_select('social', $share_networks_advanced, 'more_button_poptemplate', esc_html__('Template of social networks in more pop up', 'essb'), esc_html__('Choose different tempate of buttons in pop up with share buttons or leave usage of default template', 'essb'), essb_available_tempaltes4(true));
	ESSBOptionsStructureHelper::field_textbox_stretched('social', $share_networks_advanced, 'more_button_title', esc_html__('Customize "Share via" text', 'essb'), '');
	ESSBOptionsStructureHelper::panel_end('social', $share_networks_advanced);
}

if (essb_is_active_social_network('share')) {
	ESSBOptionsStructureHelper::panel_start('social', $share_networks_advanced, esc_html__('Share Button (Open Additional/All Networks)', 'essb'), '', 'fa21 essb_icon_share', array("mode" => "toggle", 'state' => 'closed'));
	$more_options = array ("1" => "Display all active networks after more button", "2" => "Display all social networks as pop up", "3" => "Display only active social networks as pop up", "4" => "Display all active networks after more button in popup" );
	ESSBOptionsStructureHelper::field_select('social', $share_networks_advanced, 'share_button_func', esc_html__('Share button function', 'essb'), esc_html__('Select networks that you wish to appear in your list. With drag and drop you can rearrange them.', 'essb'), essb_available_more_button_commands());
	$more_options = array ("" => "Classic Style", "modern" => "Modern Style" );
	ESSBOptionsStructureHelper::field_select('social', $share_networks_advanced, 'share_button_popstyle', esc_html__('More button pop up style', 'essb'), esc_html__('Choose the style of your pop up with social networks', 'essb'), $more_options);
	ESSBOptionsStructureHelper::field_select('social', $share_networks_advanced, 'share_button_poptemplate', esc_html__('Template of social networks in more pop up', 'essb'), esc_html__('Choose different tempate of buttons in pop up with share buttons or leave usage of default template', 'essb'), essb_available_tempaltes4(true));
	ESSBOptionsStructureHelper::field_textbox_stretched('social', $share_networks_advanced, 'share_button_title', esc_html__('Customize "Share via" text', 'essb'), '');
	
	
	$select_values = array('plus' => array('title' => '', 'content' => '<i class="essb_icon_more"></i>'),
			'dots' => array('title' => '', 'content' => '<i class="essb_icon_more_dots"></i>'),
					'share' => array('title' => '', 'content' => '<i class="essb_icon_share"></i>'),
					'share-alt-square' => array('title' => '', 'content' => '<i class="essb_icon_share-alt-square"></i>'),
			'share-alt' => array('title' => '', 'content' => '<i class="essb_icon_share-alt"></i>'),
			'share-tiny' => array('title' => '', 'content' => '<i class="essb_icon_share-tiny"></i>'),
			'share-outline' => array('title' => '', 'content' => '<i class="essb_icon_share-outline"></i>')
			);
	ESSBOptionsStructureHelper::field_toggle('social', $share_networks_advanced, 'share_button_icon', esc_html__('Share button icon', 'essb'), esc_html__('Choose the share button icon you will use (default is share if nothing is selected)', 'essb'), $select_values);
	
	
	$more_options = array ("" => "Default from settings (like other share buttons)", "icon" => "Icon only", "button" => "Button", "text" => "Text only" );
	ESSBOptionsStructureHelper::field_select('social', $share_networks_advanced, 'share_button_style', esc_html__('Share button style', 'essb'), esc_html__('Select more button icon style. You can choose from default + symbol or dots symbol', 'essb'), $more_options);
	
	$share_counter_pos = array("hidden" => "No counter", "inside" => "Inside button without text", "insidename" => "Inside button after text", "insidebeforename" => "Inside button before text", "topn" => "Top", "bottom" => "Bottom");
	ESSBOptionsStructureHelper::field_select('social', $share_networks_advanced, 'share_button_counter', esc_html__('Display total counter with the following position', 'essb'), esc_html__('Choose where you wish to display total counter of shares assigned with this button. To view total counter you need to have share counters active and they should not be running in real time mode. Also you need to have your share button set with style button. When you use share button with counter we highly recommend to hide total counter by setting position to be hidden - this will avoid having two set of total value on screen.', 'essb'), $share_counter_pos);
	
	ESSBOptionsStructureHelper::panel_end('social', $share_networks_advanced);
}

if (essb_is_active_social_network('subscribe')) {
	ESSBOptionsStructureHelper::panel_start('social', $share_networks_advanced, esc_html__('Subscribe Button', 'essb'), '', 'fa21 essb_icon_subscribe', array("mode" => "toggle", 'state' => 'closed'));
	
	$listOfValues = array ("form" => esc_html__('Custom content', 'essb'), "link" => esc_html__('Link to custom URL', 'essb'), "mailchimp" => esc_html__('Plugin generated subscribe form with integration', 'essb') );	
	ESSBOptionsStructureHelper::field_select('social', $share_networks_advanced, 'subscribe_function', esc_html__('Specify subscribe button function', 'essb'), '', $listOfValues);
	ESSBOptionsStructureHelper::holder_start('social', $share_networks_advanced, 'essb-subscribe-function-link', 'essb-subscribe-function-link');
	ESSBOptionsStructureHelper::field_textbox_stretched('social', $share_networks_advanced, 'subscribe_link', esc_html__('Custom URL', 'essb'), esc_html__('Enter the URL where users will go when the subscribe button is pressed.', 'essb'));
	ESSBOptionsStructureHelper::holder_end('social', $share_networks_advanced);
	ESSBOptionsStructureHelper::holder_start('social', $share_networks_advanced, 'essb-subscribe-function-form', 'essb-subscribe-function-form');
	ESSBOptionsStructureHelper::field_editor('social', $share_networks_advanced, 'subscribe_content', esc_html__('Custom content', 'essb'), esc_html__('Enter the custom code that will appear when the subscribe button is pressed. This can be shortcode from another plugin, custom HTML code, custom service code, etc.', 'essb'), 'htmlmixed');
	ESSBOptionsStructureHelper::holder_end('social', $share_networks_advanced);
	$listOfValues = essb_optin_designs();
	ESSBOptionsStructureHelper::holder_start('social', $share_networks_advanced, 'essb-subscribe-function-mailchimp', 'essb-subscribe-function-mailchimp');
	ESSBOptionsStructureHelper::field_select('social', $share_networks_advanced, 'subscribe_optin_design', esc_html__('Choose design for inline appearance', 'essb'), '', $listOfValues);
	ESSBOptionsStructureHelper::field_select('social', $share_networks_advanced, 'subscribe_optin_design_popup', esc_html__('Choose design form pop-up appearance', 'essb'), '', $listOfValues);
	
	ESSBOptionsStructureHelper::holder_end('social', $share_networks_advanced);
	ESSBOptionsStructureHelper::panel_end('social', $share_networks_advanced);
}

if (essb_is_active_social_network('mail')) {
	ESSBOptionsStructureHelper::panel_start('social', $share_networks_advanced, esc_html__('Email', 'essb'), '', 'fa21 essb_icon_mail', array("mode" => "toggle", 'state' => 'closed'));
		
	$listOfValues = array ("form" => esc_html__('Pop-up form with server side email sending', 'essb'), "link" => esc_html__('Visitor mail client (recommended)', 'essb') );
	
	// Disable change of mode in mini mode
	if (essb_option_bool_value('mail_function_disable_change')) {
	    $listOfValues = array ("link" => esc_html__('Visitor mail client (recommended)', 'essb') );
	}
	
	
	ESSBControlCenter::set_description_inline('mail_subject');
	ESSBControlCenter::set_description_inline('mail_body');
	
	ESSBOptionsStructureHelper::field_select('social', $share_networks_advanced, 'mail_function', esc_html__('Send to mail button function', 'essb'), '', $listOfValues);
	ESSBOptionsStructureHelper::field_textbox_stretched('social', $share_networks_advanced, 'mail_subject', esc_html__('Subject', 'essb'), esc_html__('Variables: %%title%%, %%siteurl%%, %%permalink%%, %%shorturl%%, %%image%%, %%from_email%%, %%from_name%%, %%to_email%%', 'essb'));
	ESSBOptionsStructureHelper::field_textarea('social', $share_networks_advanced, 'mail_body', esc_html__('Message', 'essb'), esc_html__('Variables: %%title%%, %%siteurl%%, %%permalink%%, %%shorturl%%, %%image%%, %%from_email%%, %%from_name%%, %%to_email%%', 'essb'));
	
	ESSBOptionsStructureHelper::holder_start('social', $share_networks_advanced, 'essb-setup-mail-function', 'essb-setup-mail-function');
	ESSBOptionsStructureHelper::field_switch('social', $share_networks_advanced, 'mail_popup_preview', esc_html__('Display preview of mail message', 'essb'), esc_html__('Include non editable preview of mail message in the popup form.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	
	
	ESSBOptionsStructureHelper::field_textbox_stretched('social', $share_networks_advanced, 'mail_copyaddress', esc_html__('Send copy of all messages to', 'essb'), esc_html__('Provide email address if you wish to get copy of each message that is sent via form', 'essb'));
	
	$listOfValues = array ("level1" => "Advanced security check", "level2" => "Basic security check" );
	ESSBOptionsStructureHelper::field_select('social', $share_networks_advanced, 'mail_function_security', esc_html__('Use the following security check when form is used', 'essb'), esc_html__('Security check is made to prevent unauthorized access to send mail function of plugin. The default option is to use advanced security check but if you get message invalid security key during send process switch to lower level check - Basic security check.', 'essb'), $listOfValues);
	ESSBOptionsStructureHelper::field_switch('social', $share_networks_advanced, 'mail_popup_mobile', esc_html__('Allow usage of pop up mail form on mobile devices', 'essb'), esc_html__('Activate this option to allow usage of pop up form when site is browsed with mobile device. Default setting is to use build in mobile device mail application.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	
	ESSBOptionsStructureHelper::field_textbox_stretched('social', $share_networks_advanced, 'mail_captcha', esc_html__('Captcha Message', 'essb'), esc_html__('Enter captcha question you wish to ask users to validate that they are human.', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_stretched('social', $share_networks_advanced, 'mail_captcha_answer', esc_html__('Captcha Answer', 'essb'), esc_html__('Enter answer you wish users to put to verify them.', 'essb'));

	ESSBOptionsStructureHelper::field_switch('social', $share_networks_advanced, 'mail_recaptcha', esc_html__('Enable Google\'s reCAPTCHA v2', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_stretched('social', $share_networks_advanced, 'mail_recaptcha_site', esc_html__('reCAPTCHA Site Key', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('social', $share_networks_advanced, 'mail_recaptcha_secret', esc_html__('reCAPTCHA Secret Key', 'essb'), '');
	
	
	ESSBOptionsStructureHelper::holder_end('social', $share_networks_advanced);
	ESSBOptionsStructureHelper::panel_end('social', $share_networks_advanced);
}

if (essb_is_active_social_network('print')) {
	ESSBOptionsStructureHelper::panel_start('social', $share_networks_advanced, esc_html__('Print', 'essb'), '', 'fa21 essb_icon_print', array("mode" => "toggle", 'state' => 'closed'));
	ESSBOptionsStructureHelper::field_switch('social', $share_networks_advanced, 'print_use_printfriendly', esc_html__('Use for printing printfriendly.com', 'essb'), esc_html__('Activate that option to use printfriendly.com as printing service instead of default print function of browser', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::panel_end('social', $share_networks_advanced);
}

if (essb_is_active_social_network('buffer')) {
	ESSBOptionsStructureHelper::panel_start('social', $share_networks_advanced, esc_html__('Buffer', 'essb'), '', 'fa21 essb_icon_buffer', array("mode" => "toggle", 'state' => 'closed'));
	ESSBOptionsStructureHelper::field_switch('social', $share_networks_advanced, 'buffer_twitter_user', esc_html__('Add X (formerly Twitter) username to buffer shares', 'essb'), esc_html__('Append also X (formerly Twitter) username into Buffer shares', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::panel_end('social', $share_networks_advanced);
}

if (essb_is_active_social_network('telegram')) {
	ESSBOptionsStructureHelper::panel_start('social', $share_networks_advanced, esc_html__('Telegram', 'essb'), '', 'fa21 essb_icon_telegram', array("mode" => "toggle", 'state' => 'closed'));
	ESSBOptionsStructureHelper::field_switch('social', $share_networks_advanced, 'telegram_alternative', esc_html__('Use alternative Telegram share', 'essb'), esc_html__('Alternative Telegram share method uses Telegram website to share data instead of direct call to mobile application. This method currently supports share to web application too.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::panel_end('social', $share_networks_advanced);
}

if (essb_is_active_social_network('comments')) {
	ESSBOptionsStructureHelper::panel_start('social', $share_networks_advanced, esc_html__('Comment Button', 'essb'), '', 'fa21 essb_icon_comments', array("mode" => "toggle", 'state' => 'closed'));
	ESSBOptionsStructureHelper::field_textbox('social', $share_networks_advanced, 'comments_address', esc_html__('Comments button address', 'essb'), esc_html__('If you use external comment system like Disqus you may need to personalize address to comments element (default is #comments).', 'essb'));
	ESSBOptionsStructureHelper::panel_end('social', $share_networks_advanced);
}

if (essb_is_active_social_network('love')) {
	ESSBOptionsStructureHelper::panel_start('social', $share_networks_advanced, esc_html__('Love This Button', 'essb'), '', 'fa21 essb_icon_love', array("mode" => "toggle", 'state' => 'closed'));
	ESSBOptionsStructureHelper::field_switch('social', $share_networks_advanced, 'lovethis_disable_thankyou', esc_html__('Don\'t show thank you message after the button is clicked', 'essb'), '');
	ESSBOptionsStructureHelper::field_switch('social', $share_networks_advanced, 'lovethis_disable_loved', esc_html__('Don\'t show you already loved this message', 'essb'), '');
	ESSBOptionsStructureHelper::field_switch('social', $share_networks_advanced, 'show_total_loves_column', esc_html__('Show total loves column in the post list', 'essb'), '');
	ESSBOptionsStructureHelper::panel_end('social', $share_networks_advanced);
}

if (essb_is_active_social_network('copy')) {
    ESSBOptionsStructureHelper::panel_start('social', $share_networks_advanced, esc_html__('Copy Link', 'essb'), '', 'fa21 essb_icon_copy', array("mode" => "toggle", 'state' => 'closed'));
    ESSBOptionsStructureHelper::field_switch('social', $share_networks_advanced, 'copylink_direct', esc_html__('Direct link copy (no pop-up)', 'essb'), esc_html__('Trying to directly copy the link without showing the pop-up window. This may not work for all browsers due to security setups or restrictions.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
    ESSBOptionsStructureHelper::panel_end('social', $share_networks_advanced);
}

// share-2 button styles
ESSBOptionsStructureHelper::help('social', 'share-2', esc_html__('Default share buttons template and style', 'essb'), esc_html__('This is the place where you set up the default share buttons template and style for the entire site. Those settings will be always used when no personal configuration is made for location, mobile device, plugin integration, post type, etc.', 'essb'), array('Help with Style Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/social-sharing-share-buttons-style/'));

if (!essb_option_bool_value('activate_automatic_position') && essb_postions_with_custom_options5(true) != '') {
	ESSBOptionsStructureHelper::field_func('social', 'share-2', 'essb5_custom_position_settings', '', '');
}

ESSBOptionsStructureHelper::tabs_start('social', 'share-2', 'global-styles', array('<i class="ti-ruler-pencil"></i>'.esc_html__('Template', 'essb'),
    '<i class="ti-reload"></i>'.esc_html__('Counters', 'essb'),
    '<i class="ti-layout-width-default"></i>'.esc_html__('Width', 'essb'),
    '<i class="ti-widget"></i>'.esc_html__('Library & Automation', 'essb')
), 'true', 'true');

ESSBOptionsStructureHelper::tab_start('social', 'share-2', 'global-styles-0', 'true');
ESSBOptionsStructureHelper::title('social', 'share-2', esc_html__('Template', 'essb'), '', 'inner-row');
ESSBOptionsStructureHelper::field_func('social', 'share-2', 'essb5_main_template_selection', '', '');

ESSBOptionsStructureHelper::title('social', 'share-2', esc_html__('Buttons style', 'essb'), '', 'inner-row');
ESSBOptionsStructureHelper::field_func('social', 'share-2', 'essb5_main_buttonstyle_selection', '', '');
ESSBOptionsStructureHelper::title('social', 'share-2', esc_html__('Buttons align', 'essb'), '', 'inner-row');
ESSBOptionsStructureHelper::field_func('social', 'share-2', 'essb5_main_alignment_choose', '', '');

$select_values = array('' => array('title' => 'Default', 'content' => 'Default'),
    'xs' => array('title' => 'Extra Small', 'content' => 'XS'),
    's' => array('title' => 'Small', 'content' => 'S'),
    'm' => array('title' => 'Medium', 'content' => 'M'),
    'l' => array('title' => 'Large', 'content' => 'L'),
    'xl' => array('title' => 'Extra Large', 'content' => 'XL'),
    'xxl' => array('title' => 'Extra Extra Large', 'content' => 'XXL')
);

ESSBOptionsStructureHelper::title('social', 'share-2', esc_html__('Buttons size', 'essb'), '', 'inner-row');
ESSBOptionsStructureHelper::field_toggle('social', 'share-2', 'button_size', '', '', $select_values, '', '', 'button_size');

ESSBOptionsStructureHelper::field_switch('social', 'share-2', 'nospace', esc_html__('Without space between buttons', 'essb'), esc_html__('Activate this option if you wish to connect share buttons without any space between them.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
ESSBOptionsStructureHelper::title('social', 'share-2', esc_html__('Animate share buttons', 'essb'), '', 'inner-row');
ESSBOptionsStructureHelper::field_func('social', 'share-2', 'essb5_main_animation_selection', '', '');

ESSBOptionsStructureHelper::tab_end('social', 'share-2');

ESSBOptionsStructureHelper::tab_start('social', 'share-2', 'global-styles-1');
ESSBOptionsStructureHelper::field_switch('social', 'share-2', 'show_counter', esc_html__('Display counter of sharing', 'essb'), esc_html__('Activate display of share counters.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
ESSBOptionsStructureHelper::title('social', 'share-2', esc_html__('Single button share counter position', 'essb'), '', 'inner-row');
ESSBOptionsStructureHelper::field_func('social', 'share-2', 'essb5_main_singlecounter_selection', '', '');

ESSBOptionsStructureHelper::title('social', 'share-2', esc_html__('Total share counter position', 'essb'), '', 'inner-row');
ESSBOptionsStructureHelper::field_func('social', 'share-2', 'essb5_main_totalcoutner_selection', '', '');

ESSBOptionsStructureHelper::hint('social', 'share-2', '', esc_html__('Additional counter options are available inside Share Counters Setup menu (total counter icon, single network update settings, share recovery, avoid negative proof and etc.)', 'essb'));
ESSBOptionsStructureHelper::tab_end('social', 'share-2');

ESSBOptionsStructureHelper::tab_start('social', 'share-2', 'global-styles-2');
ESSBOptionsStructureHelper::title('social', 'share-2', esc_html__('Button width', 'essb'), '', 'inner-row');
ESSBOptionsStructureHelper::field_func('social', 'share-2', 'essb5_main_button_width_choose', '', '');

ESSBOptionsStructureHelper::holder_start('social', 'share-2', 'essb-fixed-width', 'essb-fixed-width');
ESSBOptionsStructureHelper::title('social', 'share-2', esc_html__('Customize fixed width display', 'essb'), esc_html__('In fixed width mode buttons will have exactly same width defined by you no matter of device or screen resolution (not responsive).', 'essb'), 'inner-row');
ESSBOptionsStructureHelper::field_section_start_panels('social', 'share-2', '', esc_html__('Customize the fixed width options', 'essb'));
ESSBOptionsStructureHelper::field_textbox_panel('social', 'share-2', 'fixed_width_value', esc_html__('Custom buttons width', 'essb'), esc_html__('Provide custom width of button in pixels without the px symbol.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_select_panel('social', 'share-2', 'fixed_width_align', esc_html__('Choose alignment of network name', 'essb'), esc_html__('Provide different alignment of network name, when fixed button width is activated. When counter position is Inside or Inside name, that alignment will be applied for the counter. Default value is center.', 'essb'), array("" => "Center", "left" => "Left", "right" => "Right"));
ESSBOptionsStructureHelper::field_section_end_panels('social', 'share-2');
ESSBOptionsStructureHelper::holder_end('social', 'share-2');

ESSBOptionsStructureHelper::holder_start('social', 'share-2', 'essb-full-width', 'essb-full-width');
ESSBOptionsStructureHelper::title('social', 'share-2', esc_html__('Customize full width display', 'essb'), esc_html__('In full width mode buttons will distribute over the entire screen width on each device (responsive).', 'essb'), 'inner-row');
ESSBOptionsStructureHelper::field_select_panel('social', 'share-2', 'fullwidth_align', esc_html__('Choose alignment of network name', 'essb'), esc_html__('Provide different alignment of network name (counter when position inside or inside name). Default value is left.', 'essb'), array("left" => "Left", "center" => "Center", "right" => "Right"));
ESSBOptionsStructureHelper::field_section_start_panels('social', 'share-2', esc_html__('Customize width of first two buttons', 'essb'), esc_html__('Provide different width for the first two buttons in the row. The width should be entered as number in percents (without the % mark). You can fill only one of the values or both values.', 'essb'), '', 'true');
ESSBOptionsStructureHelper::field_textbox_panel('social', 'share-2', 'fullwidth_first_button', esc_html__('Width of first button', 'essb'), esc_html__('Provide custom width of first button when full width is active. This value is number in percents without the % symbol.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('social', 'share-2', 'fullwidth_second_button', esc_html__('Width of second button', 'essb'), esc_html__('Provide custom width of second button when full width is active. This value is number in percents without the % symbol.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_section_end_panels('social', 'share-2');

ESSBOptionsStructureHelper::panel_start('social', 'share-2', esc_html__('Fix button apperance', 'essb'), esc_html__('Full width share buttons uses formula to calculate the best width of buttons. In some cases based on other site styles you may need to personalize some of the values in here', 'essb'), '', array("mode" => "toggle", 'state' => 'closed'));
ESSBOptionsStructureHelper::field_section_start_panels('social', 'share-2', '', esc_html__('Full width option will make buttons to take the width of your post content area.', 'essb'));
ESSBOptionsStructureHelper::field_textbox_panel('social', 'share-2', 'fullwidth_share_buttons_correction', esc_html__('Max width of button on desktop', 'essb'), esc_html__('Provide custom width of single button when full width is active. This value is number in percents without the % symbol.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('social', 'share-2', 'fullwidth_share_buttons_correction_mobile', esc_html__('Max width of button on mobile', 'essb'), esc_html__('Provide custom width of single button when full width is active. This value is number in percents without the % symbol.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('social', 'share-2', 'fullwidth_share_buttons_container', esc_html__('Max width of buttons container element', 'essb'), esc_html__('If you wish to display total counter along with full width share buttons please provide custom max width of buttons container in percent without % (example: 90). Leave this field blank for default value of 100 (100%).', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_section_end_panels('social', 'share-2');
ESSBOptionsStructureHelper::panel_end('social', 'share-2');
ESSBOptionsStructureHelper::holder_end('social', 'share-2');

ESSBOptionsStructureHelper::holder_start('social', 'share-2', 'essb-column-width', 'essb-column-width');
ESSBOptionsStructureHelper::title('social', 'share-2', esc_html__('Customize column display', 'essb'), esc_html__('In column mode buttons will distribute over the entire screen width on each device in the number of columns you setup (responsive).', 'essb'), 'inner-row');
ESSBOptionsStructureHelper::field_section_start_panels('social', 'share-2', '', '');
$listOfOptions = array("1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "6" => "6", "7" => "7", "8" => "8", "9" => "9", "10" => "10");
ESSBOptionsStructureHelper::field_select_panel('social', 'share-2', 'fullwidth_share_buttons_columns', esc_html__('Number of columns', 'essb'), esc_html__('Choose the number of columns that buttons will be displayed.', 'essb'), $listOfOptions);
ESSBOptionsStructureHelper::field_select_panel('social', 'share-2', 'fullwidth_share_buttons_columns_align', esc_html__('Choose alignment of network name', 'essb'), esc_html__('Provide different alignment of network name (counter when position inside or inside name). Default value is left.', 'essb'), array("" => "Left", "center" => "Center", "right" => "Right"));
ESSBOptionsStructureHelper::field_section_end_panels('social', 'share-2');
ESSBOptionsStructureHelper::holder_end('social', 'share-2');

ESSBOptionsStructureHelper::holder_start('social', 'share-2', 'essb-flex-width', 'essb-flex-width');
ESSBOptionsStructureHelper::title('social', 'share-2', esc_html__('Customize Flex Buttons', 'essb'), esc_html__('In flexible width mode buttons will always take the full width of content area. You can customize the alignment or preserve space for the total area.', 'essb'), 'inner-row');
ESSBOptionsStructureHelper::field_section_start_panels('social', 'share-2', '', esc_html__('Customize the flex width options', 'essb'));
ESSBOptionsStructureHelper::field_textbox_panel('social', 'share-2', 'flex_width_value', esc_html__('Preserve Space For Total Counter', 'essb'), esc_html__('Use this field to setup custom width for the total counter area (numeric value only as a percent)', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('social', 'share-2', 'flex_button_value', esc_html__('Assign a Specific Button Width (%)', 'essb'), esc_html__('Use this field to setup custom width for the network button (numeric value only as a percent)', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_select_panel('social', 'share-2', 'flex_width_align', esc_html__('Choose alignment of network name', 'essb'), esc_html__('Provide different alignment of network name, when this button width is activated. When counter position is Inside or Inside name, that alignment will be applied for the counter. Default value is center.', 'essb'), array("" => "Left", "center" => "Center", "right" => "Right"));
ESSBOptionsStructureHelper::field_section_end_panels('social', 'share-2');
ESSBOptionsStructureHelper::holder_end('social', 'share-2');

ESSBOptionsStructureHelper::tab_end('social', 'share-2');

ESSBOptionsStructureHelper::tab_start('social', 'share-2', 'global-styles-3');
essb5_stylemanager_include_menu('social', 'share-2', 'site', 'true');
//ESSBOptionsStructureHelper::field_component('social', 'share-2', 'essb5_advanced_adaptive_styles', 'false');
ESSBOptionsStructureHelper::field_switch('social', 'share-2', 'activate_automatic_position', esc_html__('Adaptive position styles', 'essb'), esc_html__('Automatically apply the most commonly used styles for the different sharing positions. This eliminates the advanced design settings.', 'essb'));
ESSBOptionsStructureHelper::field_switch('social', 'share-2', 'activate_automatic_mobile_content', esc_html__('Activate automatic responsive design of content share buttons', 'essb'), esc_html__('Automatic responsive design will change the button style of the content share buttons generated by the automatic display methods. It will hide share counters, stretch width, hide network texts and so to ensure that buttons will fit inside content.', 'essb'));


ESSBOptionsStructureHelper::tab_end('social', 'share-2');

ESSBOptionsStructureHelper::tabs_end('social', 'share-2');

ESSBOptionsStructureHelper::title('social', 'share-2', esc_html__('Style Preview', 'essb'), esc_html__('This style preview is illustrative showing how your buttons will look. All displayed share counters are random generated for preview purposes - real share values will appear on each post. Once you save settings you will be able to test the exact preview on-site with networks you choose.', 'essb'));				
ESSBOptionsStructureHelper::field_func('social', 'share-2', 'essb5_live_preview', '', '');

/** Share Counters **/
ESSBOptionsStructureHelper::help('social', 'sharecnt', '', esc_html__('This section holds all options that are outside design settings for share counter update and display. Changes that you make here will be used on entire site.', 'essb'), 
		array('Help with Counter Setup' => 'https://docs.socialsharingplugin.com/knowledgebase/share-counters-setup/', 
				'How to recover shares after SSL migration' => 'https://docs.socialsharingplugin.com/knowledgebase/how-to-recover-shares-when-you-switch-to-https-activate-ssl/', 
				'My Facebook Share Counter is Not Updating' => 'https://docs.socialsharingplugin.com/knowledgebase/my-facebook-share-counter-is-not-updating-not-showing-values/'));

ESSBOptionsStructureHelper::tabs_start('social', 'sharecnt', 'counter-tabs', array('<i class="ti-settings" style="margin-right: 5px;"></i>'.esc_html__('Counter Update', 'essb'), 
		'<i class="ti-settings" style="margin-right: 5px;"></i>'.esc_html__('Advanced Update Settings', 'essb')), 'false', 'true');

ESSBOptionsStructureHelper::tab_start('social', 'sharecnt', 'counter-tabs-0', 'true');

essb_heading_with_related_section_open('social', 'sharecnt', esc_html__('Counter Update', 'essb'), '<i class="fa fa-refresh"></i>');

ESSBOptionsStructureHelper::field_select('social', 'sharecnt', 'counter_mode', esc_html__('Share counts refresh', 'essb'), esc_html__('Adjust the period between share counters update on your website. The option will work only if you have share counters enabled to display on at least one position of the share buttons.', 'essb'), essb_cached_counters_update(), '', '');
ESSBOptionsStructureHelper::field_switch('social', 'sharecnt', 'cache_counter_increase', esc_html__('Increase update period for older posts', 'essb'), esc_html__('Use this option to increase progressive update counter interval for older posts of your site. This will make less calls to social APIs and make counters update faster.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');

if (!essb_option_bool_value('deactivate_expertcounters')) {
    ESSBOptionsStructureHelper::panel_start('social', 'sharecnt', esc_html__('Advanced counter update options', 'essb') . essb_generate_expert_badge(), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
    
    ESSBOptionsStructureHelper::field_switch('social', 'sharecnt', 'cache_counter_refresh_cache', esc_html__('Force counter update on the background when a cache plugin/server is used', 'essb'), esc_html__('The option will add code to update share counters on the backend even when the site is cached. Your visitors will see the change when the cache expires. Warning! This option may generate a high server load - use with caution.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
    ESSBOptionsStructureHelper::field_switch('social', 'sharecnt', 'cache_counter_refresh_async', esc_html__('Speed up process of counters update', 'essb'), esc_html__('Enable via an external library asynchronous counter update mode which is up to 5 times faster than the regular update. Requires PHP 5.4 or newer and does not work with PHP 8.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
    ESSBOptionsStructureHelper::field_switch('social', 'sharecnt', 'cache_counter_force', esc_html__('Always save the share counter value without using the internal cache', 'essb'), esc_html__('The option will eliminate the internal cache and save the value from the social network even it is smaller from the past successful update. Warning! Use with caution - without the cache of counters, your site may show zero shares when there are such.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
    ESSBOptionsStructureHelper::field_switch('social', 'sharecnt', 'cache_counter_narrow', esc_html__('Narrow down the number of share counter updates for a post/page', 'essb'), esc_html__('This option will add an additional check to updated posts to ensure that popular posts will not update so frequently. This will allow to update less popular posts by reserving additional ticks for them.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
    ESSBOptionsStructureHelper::field_switch('social', 'sharecnt', 'cache_counter_logging', esc_html__('Log counter update requests', 'essb'), esc_html__('Save information for the last 99 requests for a counter update. That helps to detect potential configuration or connection problems. The logging is not compatible with the Speed up process of the counters update option.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
    
    ESSBOptionsStructureHelper::panel_end('social', 'sharecnt');
}

ESSBOptionsStructureHelper::field_component('social', 'sharecnt', 'essb5_additional_counter_options_only', 'false');

essb_heading_with_related_section_close('social', 'sharecnt');

ESSBOptionsStructureHelper::field_component('social', 'sharecnt', 'essb5_additional_counter_options', 'false');


ESSBOptionsStructureHelper::tab_end('social', 'sharecnt');

ESSBOptionsStructureHelper::tab_start('social', 'sharecnt', 'counter-tabs-1');
ESSBOptionsStructureHelper::panel_start('social', 'sharecnt', esc_html__('Advanced counter update options', 'essb'), esc_html__('Configure different advanced counter update functions of plugin when you use real time or cached counters', 'essb'), 'fa21 fa fa-refresh', array("mode" => "toggle", 'state' => 'opened'));
ESSBOptionsStructureHelper::field_heading('social', 'sharecnt', 'heading5', esc_html__('Real time counters', 'essb'));
ESSBOptionsStructureHelper::field_section_start('social', 'sharecnt', esc_html__('Counter update options for all networks that does not provide direct access to counter API or does not have share counter and uses internal counters', 'essb'), '', '');
ESSBOptionsStructureHelper::field_switch('social', 'sharecnt', 'force_counters_admin', esc_html__('Load counters for social networks without direct access to counter API with build-in WordPress AJAX functions (using AJAX settings)', 'essb'), esc_html__('This method is more secure and required by some hosting companies but may slow down page load.', 'essb'), 'yes', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
$listOfOptions = array("wp" => "Build in WordPress ajax handler", "light" => "Light Easy Social Share Buttons handler");
ESSBOptionsStructureHelper::field_select('social', 'sharecnt', 'force_counters_admin_type', esc_html__('AJAX load method', 'essb'), esc_html__('Choose the default ajax method from build in WordPress or light handler', 'essb'), $listOfOptions);
ESSBOptionsStructureHelper::field_switch('social', 'sharecnt', 'force_counters_admin_single', esc_html__('Use single request of counter load for all social networks that uses the ajax handler', 'essb'), esc_html__('This method will make single call to AJAX handler to get all counters instead of signle call for each network. The pros of this option is that you will make less calls to selected AJAX handler. We suggest to use this option in combination with counters cache.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_section_end('social', 'sharecnt');
ESSBOptionsStructureHelper::field_section_start('social', 'sharecnt', esc_html__('Counter cache for AJAX load method', 'essb'), esc_html__('This will reduce load because counters will be updated when cache expires', 'essb'), '');
ESSBOptionsStructureHelper::field_switch('social', 'sharecnt', 'admin_ajax_cache', esc_html__('Activate', 'essb'), '', 'yes', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_textbox('social', 'sharecnt', 'admin_ajax_cache_time', esc_html__('Cache expiration time', 'essb'), esc_html__('Amount of seconds for cache (default is 600 if nothing is provided)', 'essb'));
ESSBOptionsStructureHelper::field_section_end('social', 'sharecnt');
ESSBOptionsStructureHelper::panel_end('social', 'sharecnt');

ESSBOptionsStructureHelper::tab_end('social', 'sharecnt');

ESSBOptionsStructureHelper::tabs_end('social', 'sharecnt');

ESSBOptionsStructureHelper::help('social', 'analytics', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/working-with-different-analytics-features-of-wordpress-social-share-buttons/'));
ESSBOptionsStructureHelper::field_component('social', 'analytics', 'essb5_additional_analytics_options', 'false');


/** Pinterest Pro **/
if (!essb_option_bool_value('deactivate_module_pinterestpro')) {
	
	ESSBOptionsStructureHelper::help('social', 'pinpro', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/sharing-images-with-pinterest-pro-setup-tools/'));
		
	ESSBOptionsStructureHelper::panel_start('social', 'pinpro', esc_html__('Tools for Images', 'essb') . essb_generate_expert_badge(), '', 'fa21 fa fa-pinterest-p', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_switch('social', 'pinpro', 'pinterest_force_description', esc_html__('Fill custom Pinterest message on all images', 'essb'), esc_html__('Attach the custom Pin message configured in the plugin post settings to all images inside the content', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
	ESSBOptionsStructureHelper::field_switch('social', 'pinpro', 'pinterest_force_responsive', esc_html__('Force sharing of responsive thumbnail images', 'essb'), esc_html__('Enable the option if the plugin can\'t detect the biggest image from the responsive list.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
	ESSBOptionsStructureHelper::field_switch('social', 'pinpro', 'pinterest_set_datamedia', esc_html__('Make all images Pin the custom image set on post', 'essb'), esc_html__('The option will work only if you set a custom image for the Pin. When enabled it will add automatically to all content images the customization you do via the data-pin-media attribute.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
	ESSBOptionsStructureHelper::field_switch('social', 'pinpro', 'pinterest_set_pinid_all', esc_html__('Fill Pin ID from the post customizations on all images', 'essb'), esc_html__('The option will set the Pin ID from the settings to all images. This will change the Pin the current image to a re-pin of the Pin ID you provide.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');		
	ESSBOptionsStructureHelper::panel_end('social', 'pinpro');
	
	ESSBOptionsStructureHelper::panel_start('social', 'pinpro', esc_html__('Pinterest image hover pins', 'essb'), '', 'fa32 fa fa-pinterest-p', array("mode" => "switch", 'switch_id' => 'pinterest_images', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
	$location = 'pinterest';
	
	ESSBOptionsStructureHelper::tabs_start('social', 'pinpro', 'pinpro-styles', array('<i class="ti-settings"></i>'.esc_html__('Appearance', 'essb'),
	    '<i class="ti-settings"></i>'.esc_html__('Design', 'essb')), 'true', 'true');
	ESSBOptionsStructureHelper::tab_start('social', 'pinpro', 'pinpro-styles-0', 'true');
	ESSBOptionsStructureHelper::field_textbox('social', 'pinpro', 'pinterest_text', esc_html__('Button action text', 'essb'), esc_html__('Replace or localize the default action text used by the plugin (Pin).', 'essb'), '', '' ,'', '', '');
	
	$list_of_positions =  array(
	    'top-left'      => esc_html__( 'Top left', 'essb' ),
	    'top-middle'    => esc_html__( 'Top middle', 'essb' ),
	    'top-right'     => esc_html__( 'Top right', 'essb' ),
	    'middle-left'   => esc_html__( 'Middle left', 'essb' ),
	    'middle-middle' => esc_html__( 'Middle', 'essb' ),
	    'middle-right'  => esc_html__( 'Middle right', 'essb' ),
	    'bottom-left'   => esc_html__( 'Bottom left', 'essb' ),
	    'bottom-middle' => esc_html__( 'Bottom middle', 'essb' ),
	    'bottom-right'  => esc_html__( 'Bottom right', 'essb' ));
	ESSBOptionsStructureHelper::field_select('social', 'pinpro', 'pinterest_position', esc_html__('Position over image', 'essb'), '', $list_of_positions, '', '');
	
	$list_of_visibility = array(
	    '' => esc_html__('On Hover', 'essb'),
	    'always' => esc_html__('Always Visible', 'essb'),
	);
	
	ESSBOptionsStructureHelper::field_select('social', 'pinpro', 'pinterest_visibility', esc_html__('Button visibility (Desktop)', 'essb'), '', $list_of_visibility, '', '');
	
	$list_of_mobile_positions = array(
	    '' => esc_html__('Same as desktop', 'essb'),
	    'below' => esc_html__('Below image', 'essb'),
	    'hidden' => esc_html__('Don\'t show', 'essb'),
	);
	ESSBOptionsStructureHelper::field_select('social', 'pinpro', 'pinterest_mobile_position', esc_html__('Mobile position over image', 'essb'), '', $list_of_mobile_positions, '', '');
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'pinpro', 'pinterest_hideon', esc_html__('Don\'t show on', 'essb'), esc_html__('In case you need to hide button on specific images you can write a relative path here.', 'essb'), '', '' ,'', '', '');
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'pinpro', 'pinterest_selector', esc_html__('Custom image selector', 'essb'), esc_html__('Use only if you need to change the place where images are located. In all other situations leave it blank to work with default setup.', 'essb'), '', '' ,'', '', '');

	
	ESSBOptionsStructureHelper::field_textbox('social', 'pinpro', 'pinterest_minwidth', esc_html__('Min image size', 'essb'), esc_html__('Control the min image size of Pinable images. If nothing is set the default value of 300 will be used.', 'essb'), '', '' ,'', '', '', array('pinterest_minwidth' => esc_html__('Width', 'essb'), 'pinterest_minheight' => esc_html__('Height', 'essb')));
	ESSBOptionsStructureHelper::field_textbox('social', 'pinpro', 'pinterest_minwidth_mobile', esc_html__('Min image size (Mobile)', 'essb'), esc_html__('Specify additional default image size for Pin images on mobile. If blank the desktop size (or standard values) will be used.', 'essb'), '', '' ,'', '', '', array('pinterest_minwidth_mobile' => esc_html__('Width', 'essb'), 'pinterest_minheight_mobile' => esc_html__('Height', 'essb')));
	ESSBOptionsStructureHelper::field_switch('social', 'pinpro', 'pinterest_nolinks', esc_html__('Don\'t show on images with links', 'essb'), esc_html__('Set to Yes if you wish the images that are wrapped inside links to avoid showing the Pin button', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
	ESSBOptionsStructureHelper::field_switch('social', 'pinpro', 'pinterest_lazyload', esc_html__('My images have lazy loading', 'essb'), esc_html__('Enable if you are using lazyloading optimization or infinite content loading. Otherwise, the Pin button may not register on all images.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
	ESSBOptionsStructureHelper::field_switch('social', 'pinpro', 'pinterest_alwayscustom', esc_html__('Use the post custom Pinterest message for all images', 'essb'), esc_html__('Enable the option to let the plugin always use the custom Pinterest message you have set. If there is no custom message the plugin will use the default settings (post/page title).', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
	ESSBOptionsStructureHelper::field_switch('social', 'pinpro', 'pinterest_reposition', esc_html__('Correct button position', 'essb'), esc_html__('Set to Yes if your Pin button appear outside the image (on images with smaller width than the content area).', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
	ESSBOptionsStructureHelper::field_switch('social', 'pinpro', 'pinterest_optimized_load', esc_html__('Optimize the initial image detection scan', 'essb'), esc_html__('Set Yes to delay the initial images scan only after the user does an interaction with the page - touch, move, scroll, type.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
	
	ESSBOptionsStructureHelper::tab_end('social', 'pinpro');
	
	ESSBOptionsStructureHelper::tab_start('social', 'pinpro', 'pinpro-styles-1');
	ESSBOptionsStructureHelper::title('social', 'pinpro', esc_html__('Template', 'essb'), '', 'inner-row');
	ESSBOptionsStructureHelper::field_template_select('social', 'pinpro', $location.'_template', $location, 'pinterest');
	
	ESSBOptionsStructureHelper::title('social', 'pinpro', esc_html__('Buttons style', 'essb'), '', 'inner-row');
	ESSBOptionsStructureHelper::field_buttonstyle_select('social', 'pinpro', $location.'_button_style', $location, true);
	
	$select_values = array('' => array('title' => 'Default', 'content' => 'Default'),
	    'xs' => array('title' => 'Extra Small', 'content' => 'XS'),
	    's' => array('title' => 'Small', 'content' => 'S'),
	    'm' => array('title' => 'Medium', 'content' => 'M'),
	    'l' => array('title' => 'Large', 'content' => 'L'),
	    'xl' => array('title' => 'Extra Large', 'content' => 'XL'),
	    'xxl' => array('title' => 'Extra Extra Large', 'content' => 'XXL')
	);
	ESSBOptionsStructureHelper::field_toggle_panel('social', 'pinpro', $location.'_button_size', esc_html__('Button Size', 'essb'), '', $select_values, '', '', 'button_size');
	
	
	//essb5_main_animation_selection
	ESSBOptionsStructureHelper::title('social', 'pinpro', esc_html__('Animate share buttons', 'essb'), '', 'inner-row');
	ESSBOptionsStructureHelper::field_animation_select('social', 'pinpro', $location.'_css_animations', $location);
	
	ESSBOptionsStructureHelper::tab_end('social', 'pinpro');
	ESSBOptionsStructureHelper::tabs_end('social', 'pinpro');
	

    ESSBOptionsStructureHelper::panel_end('social', 'pinpro');
		
		//-- shortcode styles
		$location = 'pinsc';
		ESSBOptionsStructureHelper::panel_start('social', 'pinpro', esc_html__('Pinterest Pro: Action button style used in shortcodes', 'essb'), '', 'fa21 fa fa-pinterest-p', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open1"));
		ESSBOptionsStructureHelper::title('social', 'pinpro', esc_html__('Template', 'essb'), '', 'inner-row');
		ESSBOptionsStructureHelper::field_template_select('social', 'pinpro', $location.'_template', $location, 'pinterest');
		
		ESSBOptionsStructureHelper::title('social', 'pinpro', esc_html__('Buttons style', 'essb'), '', 'inner-row');
		ESSBOptionsStructureHelper::field_buttonstyle_select('social', 'pinpro', $location.'_button_style', $location, true);
		
		$select_values = array('' => array('title' => 'Default', 'content' => 'Default'),
				'xs' => array('title' => 'Extra Small', 'content' => 'XS'),
				's' => array('title' => 'Small', 'content' => 'S'),
				'm' => array('title' => 'Medium', 'content' => 'M'),
				'l' => array('title' => 'Large', 'content' => 'L'),
				'xl' => array('title' => 'Extra Large', 'content' => 'XL'),
				'xxl' => array('title' => 'Extra Extra Large', 'content' => 'XXL')
		);
		ESSBOptionsStructureHelper::field_toggle_panel('social', 'pinpro', $location.'_button_size', esc_html__('Button Size', 'essb'), '', $select_values, '', '', 'button_size');
		
		
		//essb5_main_animation_selection
		ESSBOptionsStructureHelper::title('social', 'pinpro', esc_html__('Animate share buttons', 'essb'), '', 'inner-row');
		ESSBOptionsStructureHelper::field_animation_select('social', 'pinpro', $location.'_css_animations', $location);
			
		$list_of_positions =  array(
				'top-left'      => esc_html__( 'Top left', 'essb' ),
				'top-middle'    => esc_html__( 'Top middle', 'essb' ),
				'top-right'     => esc_html__( 'Top right', 'essb' ),
				'middle-left'   => esc_html__( 'Middle left', 'essb' ),
				'middle-middle' => esc_html__( 'Middle', 'essb' ),
				'middle-right'  => esc_html__( 'Middle right', 'essb' ),
				'bottom-left'   => esc_html__( 'Bottom left', 'essb' ),
				'bottom-middle' => esc_html__( 'Bottom middle', 'essb' ),
				'bottom-right'  => esc_html__( 'Bottom right', 'essb' ));
		
		ESSBOptionsStructureHelper::field_select('social', 'pinpro', $location.'_position', esc_html__('Position Over Image', 'essb'), esc_html__('Choose the location of the button over the selected image', 'essb'), $list_of_positions, '', '');
		ESSBOptionsStructureHelper::field_textbox('social', 'pinpro', $location.'_text', esc_html__('Pin Action Text', 'essb'), esc_html__('Set a custom call to action text for Pinterest images button. The text will be visible when style of button is button, text or icon with text on hover. If nothing is provided than the default text "Pin" will be used.', 'essb'), '', '' ,'', '', '');
		ESSBOptionsStructureHelper::field_switch('social', 'pinpro', $location.'_alwayscustom', esc_html__('Always Use Custom Pinterest Description', 'essb'), esc_html__('Set to Yes if you need to avoid automated message generation based on image data (title or alternative text).', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
		ESSBOptionsStructureHelper::field_switch('social', 'pinpro', $location.'_lazyloading', esc_html__('My Images Have Lazy Loading', 'essb'), esc_html__('Set to Yes if you use lazy loading on your site and images in the gallery do not show properly (have a too-small height).', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '');
		
		
		ESSBOptionsStructureHelper::panel_end('social', 'pinpro');
		
		essb_create_exclude_display_on('social', 'pinpro', 'pinpro', true, true, true, true);		
		
		ESSBOptionsStructureHelper::field_component('social', 'pinpro', 'essb5_advanced_pinpro_deactivate_options', 'false');
		
}


/** Optimize **/
if (!essb_option_bool_value('deactivate_module_shareoptimize')) {
	ESSBOptionsStructureHelper::help('social', 'optimize', esc_html__('What are social media optimization tags and why I need them?', 'essb'), '', array('How to customize shared information' => 'https://docs.socialsharingplugin.com/knowledgebase/how-to-customize-personalize-shared-information-on-social-networks/', 'I see wrong share information' => 'https://docs.socialsharingplugin.com/knowledgebase/facebook-is-showing-the-wrong-image-title-or-description/', 'Test & Fix Facebook Showing Wrong Information' => 'https://docs.socialsharingplugin.com/knowledgebase/how-to-test-and-fix-facebook-sharing-wrong-information-using-facebook-open-graph-debugger/'));
	
	ESSBOptionsStructureHelper::panel_start('social', 'optimize', esc_html__('Homepage share message', 'essb'), '', 'fa21 fa fa-home', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'optimize', 'sso_frontpage_title', esc_html__('Title', 'essb'), esc_html__('Title that will be displayed on frontpage.', 'essb'));
	ESSBOptionsStructureHelper::field_textarea('social', 'optimize', 'sso_frontpage_description', esc_html__('Description', 'essb'), esc_html__('Description that will be displayed on frontpage', 'essb'));
	ESSBOptionsStructureHelper::field_image('social', 'optimize', 'sso_frontpage_image', esc_html__('Image', 'essb'), esc_html__('Image that will be displayed on frontpage', 'essb'), '', 'vertical1');
	ESSBOptionsStructureHelper::panel_end('social', 'optimize');
	
	ESSBOptionsStructureHelper::panel_start('social', 'optimize', esc_html__('Enable Open Graph', 'essb'), '', 'fa21 fa fa-facebook', array("mode" => "switch", 'switch_id' => 'opengraph_tags', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
	
	ESSBOptionsStructureHelper::field_component('social', 'optimize', 'essb5_advanced_sso_options', 'false');	
	
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'optimize', 'opengraph_tags_fbpage', esc_html__('Facebook Page URL', 'essb'), esc_html__('Provide your Facebook page address.', 'essb'));
	ESSBOptionsStructureHelper::field_textbox('social', 'optimize', 'opengraph_tags_fbadmins', esc_html__('Facebook Admins', 'essb'), esc_html__('Enter IDs of Facebook Users that are admins of current page.', 'essb'));
	ESSBOptionsStructureHelper::field_textbox('social', 'optimize', 'opengraph_tags_fbapp', esc_html__('Facebook Application ID', 'essb'), esc_html__('Enter ID of Facebook Application to be able to use Facebook Insights', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'optimize', 'opengraph_tags_fbauthor', esc_html__('Facebook Author Profile', 'essb'), esc_html__('Add link to Facebook profile page of article author if you wish it to appear in shared information.', 'essb'));
	ESSBOptionsStructureHelper::field_image('social', 'optimize', 'sso_default_image', esc_html__('Default share image', 'essb'), esc_html__('The default share image is the one that will be used on entire site when there is no post or page featured image added (or personal social media optimization image)', 'essb'), '', 'vertical1');
	ESSBOptionsStructureHelper::panel_end('social', 'optimize');
	
	ESSBOptionsStructureHelper::panel_start('social', 'optimize', esc_html__('Enable X (formerly Twitter) Cards', 'essb'), '', 'fa21 fa fa-twitter', array("mode" => "switch", 'switch_id' => 'twitter_card', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
	ESSBOptionsStructureHelper::field_textbox('social', 'optimize', 'twitter_card_user', esc_html__('X (formerly Twitter) Site Username', 'essb'), esc_html__('Enter your X (formerly Twitter) site username.', 'essb'));
	$listOfOptions = array ("summary" => "Summary", "summaryimage" => "Summary with image" );
	ESSBOptionsStructureHelper::field_select('social', 'optimize', 'twitter_card_type', esc_html__('X (formerly Twitter) Card Type', 'essb'), esc_html__('Choose the default card type that should be generated.', 'essb'), $listOfOptions);
	ESSBOptionsStructureHelper::panel_end('social', 'optimize');

	ESSBOptionsStructureHelper::field_component('social', 'optimize', 'essb5_advanced_sso_deactivate_options', 'false');	
}

/** Short URL **/
if (!essb_option_bool_value('deactivate_module_shorturl')) {
	ESSBOptionsStructureHelper::help('social', 'shorturl', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/setup-short-urls-or-sharing/'));
	
	ESSBOptionsStructureHelper::panel_start('social', 'shorturl', esc_html__('Enable link shortening', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'shorturl_activate', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
	
	ESSBOptionsStructureHelper::field_select('social', 'shorturl', 'twitter_shareshort', esc_html__('Generate short URLs for', 'essb'), '', array( "true" => "Recommended social networks only (X (formerly Twitter), Mobile Messengers)", "false" => "All social networks"));
	
	$listOfOptions = array("wp" => "Build in WordPress function wp_get_shortlink()", "goo.gl" => "goo.gl", "bit.ly" => "bit.ly", 'rebrand.ly' => 'Rebrandly', 'po.st' => 'po.st');
	if (defined('ESSB3_SSU_VERSION')) {
		$listOfOptions['ssu'] = esc_html__('Social Media Short URLs add-on for Easy Social Share Buttons for WordPress', 'essb');
	}
	
	if (isset($listOfOptions['goo.gl'])) {
	    unset ($listOfOptions['goo.gl']);
	}
	
	/**
	 * @since 7.7 Added support for Premium URL Shortener
	 */
	$listOfOptions['pus'] = 'Premium URL Shortener';
	
	ESSBOptionsStructureHelper::field_select('social', 'shorturl', 'shorturl_type', esc_html__('Short URL type', 'essb'), esc_html__('Usage of external service for short URL generation requires to set up all fields (tokens, access, etc.). Without doing this setup, URLs won\'t be generated.'), $listOfOptions);
	ESSBOptionsStructureHelper::field_switch('social', 'shorturl', 'deactivate_shorturl_cache', esc_html__('Deactivate short URLs cache', 'essb') . essb_generate_expert_badge() , esc_html__('Set to Yes to temporary stop the short URL cache. This will make plugin update the visited posts short URL. You can also clear the short URL cache for entire site using the option inside Import Options.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch('social', 'shorturl', 'deactivate_shorturl_preview', esc_html__('Avoid generation of short URLs on preview pages', 'essb'), esc_html__('Apply additional check to prevent generation of short URLs on preview pages.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	
	ESSBOptionsStructureHelper::holder_start('social', 'shorturl', 'essb-short-bitly', 'essb-short-bitly');
	
	essb_heading_with_related_section_open('social', 'shorturl', 'bit.ly', '<i class="fa fa-cog"></i>', '');
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'shorturl', 'shorturl_bitlyapi', esc_html__('bit.ly Access token key', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'shorturl', 'shorturl_bitlydomain', esc_html__('bit.ly branded short URL domain (example: shrt.to)', 'essb'), '');
	essb_heading_with_related_section_close('social', 'shorturl');
	
	ESSBOptionsStructureHelper::holder_end('social', 'shorturl');
	
	ESSBOptionsStructureHelper::holder_start('social', 'shorturl', 'essb-short-googl', 'essb-short-googl');
	ESSBOptionsStructureHelper::field_heading('social', 'shorturl', 'heading5', esc_html__('goo.gl Access Configuration', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'shorturl', 'shorturl_googlapi', esc_html__('goo.gl API key', 'essb'), esc_html__('Goo.gl short URL service is official closed. Please swich to a different provider.', 'essb'));
	ESSBOptionsStructureHelper::holder_end('social', 'shorturl');
	
	ESSBOptionsStructureHelper::holder_start('social', 'shorturl', 'essb-short-rebrandly', 'essb-short-rebrandly');
	essb_heading_with_related_section_open('social', 'shorturl', 'Rebrandly', '<i class="fa fa-cog"></i>', '');	
	ESSBControlCenter::set_extra_description('shorturl_rebrandpi', '<a href="https://app.rebrandly.com/account/api-keys" target="_blank">Rebrandly API Settings page</a>');
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'shorturl', 'shorturl_rebrandpi', esc_html__('Rebrandly API key', 'essb'), esc_html__('Rebrandly service require API key to generate your short URLs', 'essb'));
	ESSBControlCenter::set_extra_description('shorturl_rebrandpi_domain', '<a href="https://app.rebrandly.com/domains/" target="_blank">Rebrandly Domain list page</a>');
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'shorturl', 'shorturl_rebrandpi_domain', esc_html__('Rebrandly Domain ID', 'essb'), sprintf(esc_html__('If you have your own branded domain name fill in here its ID.', 'essb') ));
	ESSBOptionsStructureHelper::field_switch('social', 'shorturl', 'shorturl_rebrandpi_https', esc_html__('Generate HTTPS version of Short URL', 'essb'), esc_html__('Set to Yes if you wish the generated URLs to be with https protocol instead of http', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	essb_heading_with_related_section_close('social', 'shorturl');
	ESSBOptionsStructureHelper::holder_end('social', 'shorturl');
	
	ESSBOptionsStructureHelper::holder_start('social', 'shorturl', 'essb-short-post', 'essb-short-post');
	essb_heading_with_related_section_open('social', 'shorturl', 'po.st', '<i class="fa fa-cog"></i>', '');		
	ESSBControlCenter::set_extra_description('shorturl_postapi', '<a href="http://re.po.st/register" target="_blank">http://re.po.st/register</a>');
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'shorturl', 'shorturl_postapi', esc_html__('po.st API Access Token', 'essb'), esc_html__('po.st service require API access token to generate your short URLs.', 'essb'));
	essb_heading_with_related_section_close('social', 'shorturl');
	ESSBOptionsStructureHelper::holder_end('social', 'shorturl');
	
	ESSBOptionsStructureHelper::holder_start('social', 'shorturl', 'essb-short-pus', 'essb-short-pus');
	essb_heading_with_related_section_open('social', 'shorturl', 'Premium URL Shortener', '<i class="fa fa-cog"></i>', '');
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'shorturl', 'shorturl_pus_url', esc_html__('URL of the shortener', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'shorturl', 'shorturl_pus_api', esc_html__('API key', 'essb'), '');
	essb_heading_with_related_section_close('social', 'shorturl');
	ESSBOptionsStructureHelper::holder_end('social', 'shorturl');	
	
	ESSBOptionsStructureHelper::panel_end('social', 'shorturl');
	
	ESSBOptionsStructureHelper::field_component('social', 'shorturl', 'essb5_advanced_shorturl_options', 'false');
}

/** After Share Events **/
// after share actions
if (!essb_options_bool_value('deactivate_module_aftershare')) {
	ESSBOptionsStructureHelper::help('social', 'after-share', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/after-share-events/'));
	
	
	if (essb_option_bool_value('afterclose_active')) {
		ESSBOptionsStructureHelper::panel_start('social', 'after-share', esc_html__('Enable After Share Events', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'afterclose_active', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
		ESSBOptionsStructureHelper::field_component('social', 'after-share', 'essb5_advanced_aftershare_options', 'false');
		ESSBOptionsStructureHelper::field_select('social', 'after-share', 'aftershare_networks', esc_html__('Enable after share for selected networks only', 'essb'), '', essb_get_all_networks_source(true), '', '', 'true');
		
		$action_types = array (
				"follow" => esc_html__("Social Like/Follow Buttons (Native)", 'essb'), 
				"follow_profile" => esc_html__('Social Profile Buttons', 'essb'),
				"message" => esc_html__("Custom HTML message or shortcode", 'essb'), 
				"code" => esc_html__("Custom Javascript code", 'essb'), 
				"optin" => esc_html__("Plugin integrated subscribe form", 'essb'), 
				"popular" => esc_html__("Popular social posts", 'essb') );
		ESSBOptionsStructureHelper::field_select('social', 'after-share', 'afterclose_type', esc_html__('Action type', 'essb'), '', $action_types);
		
		ESSBOptionsStructureHelper::holder_start('social', 'after-share', 'essb-aftershare-follow essb-aftershare-type', 'essb-aftershare-follow');	
		ESSBOptionsStructureHelper::field_textbox_stretched('social', 'after-share', 'afterclose_follow_title', esc_html__('Title of window', 'essb'), esc_html__('Provide a custom shot title for the after share window. The title will appear at the top on the same line with the close button. It is optional - if you did not provide a title it will leave it blank.', 'essb'));
		ESSBOptionsStructureHelper::field_editor('social', 'after-share', 'afterclose_like_text', esc_html__('Custom content', 'essb'), esc_html__('Appear before the buttons in the window (HTML and shortcodes supported).', 'essb'), 'htmlmixed');
		$col_values = array("onecol" => "1 Column", "twocols" => "2 Columns", "threecols" => "3 Columns");
		ESSBOptionsStructureHelper::field_select('social', 'after-share', 'afterclose_like_cols', esc_html__('Display social profile in the following number of columns', 'essb'), esc_html__('Choose the number of columns that social profiles will appear. Please note that using greater value may require increase the pop up window width.', 'essb'), $col_values);
		ESSBOptionsStructureHelper::field_textbox_stretched('social', 'after-share', 'afterclose_like_fb_like_url', esc_html__('Include Facebook Like Button for the following url', 'essb'), esc_html__('Provide url address users to like. This can be you Facebook fan page, additional page or any other page you wish users to like.', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('social', 'after-share', 'afterclose_like_fb_follow_url', esc_html__('Include Facebook Follow Profile button', 'essb'), esc_html__('Provide url address of profile users to follow.', 'essb'));
		ESSBOptionsStructureHelper::field_textbox('social', 'after-share', 'afterclose_like_twitter_profile', esc_html__('Include X (formerly Twitter) Follow Button', 'essb'), esc_html__('Provide X (formerly Twitter) username people to follow (without @)', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('social', 'after-share', 'afterclose_like_pin_follow_url', esc_html__('Include Pinterest Follow Profile button', 'essb'), esc_html__('Provide url address to a Pinterest profile.', 'essb'));
		ESSBOptionsStructureHelper::field_textbox('social', 'after-share', 'afterclose_like_youtube_channel', esc_html__('Include Youtube Subscribe Channel button', 'essb'), esc_html__('Provide your Youtube Channel ID.', 'essb'));
		ESSBOptionsStructureHelper::field_textbox('social', 'after-share', 'afterclose_like_youtube_user', esc_html__('Include Youtube Subscribe User button', 'essb'), esc_html__('Provide your Youtube Channel Username.', 'essb'));
		ESSBOptionsStructureHelper::field_textbox('social', 'after-share', 'afterclose_like_linkedin_company', esc_html__('Include LinkedIn Company follow button', 'essb'), esc_html__('Provide your LinkedIn company ID.', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('social', 'after-share', 'afterclose_like_vk', esc_html__('Include VKontakte ID of Page or Group', 'essb'), esc_html__('To get this ID login to your vk profile and visit this page: https://vk.com/dev/Subscribe (copy numbers after http://vk.com/id in the field).', 'essb'));
		ESSBOptionsStructureHelper::holder_end('social', 'after-share');	
		
		ESSBOptionsStructureHelper::holder_start('social', 'after-share', 'essb-aftershare-message essb-aftershare-type', 'essb-aftershare-message');
		ESSBOptionsStructureHelper::field_textbox_stretched('social', 'after-share', 'afterclose_message_title', esc_html__('Title of window', 'essb'), esc_html__('Provide a custom shot title for the after share window. The title will appear at the top on the same line with the close button. It is optional - if you did not provide a title it will leave it blank.', 'essb'));
		ESSBOptionsStructureHelper::field_editor('social', 'after-share', 'afterclose_message_text', esc_html__('Custom html message', 'essb'), esc_html__('Put code of your custom message here. This can be subscribe form or anything you wish to display (html supported, shortcodes supported).', 'essb'), 'htmlmixed');
		ESSBOptionsStructureHelper::holder_end('social', 'after-share');
		
		
		ESSBOptionsStructureHelper::holder_start('social', 'after-share', 'essb-aftershare-code essb-aftershare-type', 'essb-aftershare-code');
		ESSBOptionsStructureHelper::field_switch('social', 'after-share', 'afterclose_code_always_use', esc_html__('Always include custom code', 'essb'), esc_html__('Activate this option to make code always be executed even if a different message type is activated', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_editor('social', 'after-share', 'afterclose_code_text', esc_html__('Custom javascript code', 'essb'), esc_html__('Provide your custom javascript code that will be executed (available parameters: oService - social network clicked by user and oPostID for the post where button is clicked).', 'essb'), 'htmlmixed');
		ESSBOptionsStructureHelper::holder_end('social', 'after-share');
		
		ESSBOptionsStructureHelper::holder_start('social', 'after-share', 'essb-aftershare-optin essb-aftershare-type', 'essb-aftershare-optin');
		$listOfValues = essb_optin_designs();
		ESSBOptionsStructureHelper::field_select('social', 'after-share', 'aftershare_optin_design', esc_html__('Subscribe form design', 'essb'), '', $listOfValues);
		ESSBOptionsStructureHelper::holder_end('social', 'after-share');
		
		ESSBOptionsStructureHelper::holder_start('social', 'after-share', 'essb-aftershare-follow_profile essb-aftershare-type', 'essb-aftershare-follow_profile');
		ESSBOptionsStructureHelper::field_textbox_stretched('social', 'after-share', 'afterclose_profile_title', esc_html__('Title of window', 'essb'), esc_html__('Provide a custom shot title for the after share window. The title will appear at the top on the same line with the close button. It is optional - if you did not provide a title it will leave it blank.', 'essb'));
		ESSBOptionsStructureHelper::field_editor('social', 'after-share', 'aftershare_profiles_message', esc_html__('Message', 'essb'), esc_html__('Provides custom HTML message appearing above the buttons.', 'essb'), 'htmlmixed');
		
		$source = array(
						'' => esc_html__('Don\'t show in columns (automatic width)', 'essb'),
						'1' => esc_html__('1 Column', 'essb'),
						'2' => esc_html__('2 Columns', 'essb'),
						'3' => esc_html__('3 Columns', 'essb'),
						'4' => esc_html__('4 Columns', 'essb'),
						'5' => esc_html__('5 Columns', 'essb'),
						'6' => esc_html__('6 Columns', 'essb'),
				);
		
		if (!class_exists('ESSBSocialProfilesHelper')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-profiles/essb-social-profiles-helper.php');
		}
		
		ESSBOptionsStructureHelper::field_select('social', 'after-share', 'aftershare_profiles_columns', esc_html__('Columns', 'essb'), '', $source);
		ESSBOptionsStructureHelper::field_select('social', 'after-share', 'aftershare_profiles_template', esc_html__('Template', 'essb'), '', ESSBSocialProfilesHelper::available_templates());
		ESSBOptionsStructureHelper::field_select('social', 'after-share', 'aftershare_profiles_animation', esc_html__('Animation', 'essb'), '', ESSBSocialProfilesHelper::available_animations());
		ESSBOptionsStructureHelper::field_select('social', 'after-share', 'aftershare_profiles_align', esc_html__('Alignment', 'essb'), '', ESSBSocialProfilesHelper::available_alignments());
		ESSBOptionsStructureHelper::field_select('social', 'after-share', 'aftershare_profiles_size', esc_html__('Size', 'essb'), '', ESSBSocialProfilesHelper::available_sizes());
		ESSBOptionsStructureHelper::field_switch('social', 'after-share', 'aftershare_profiles_nospace', esc_html__('Without space between buttons', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_switch('social', 'after-share', 'aftershare_profiles_cta', esc_html__('Show texts with the buttons', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_switch('social', 'after-share', 'aftershare_profiles_cta_vertical', esc_html__('Vertical text layout', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		
		ESSBOptionsStructureHelper::holder_end('social', 'after-share');
		
		
		ESSBOptionsStructureHelper::panel_end('social', 'after-share');
		
		ESSBOptionsStructureHelper::field_component('social', 'after-share', 'essb5_advanced_aftershare_deactivate_options', 'false');
	}
	else {
		ESSBOptionsStructureHelper::field_component('social', 'after-share', 'essb5_advanced_aftershare_activate_options', 'false');		
	}
}

/** Affiliate & Point **/
if (!essb_option_bool_value('deactivate_module_affiliate')) {
	ESSBOptionsStructureHelper::help('social', 'affiliate', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/affiliate-point-plugins-integration-in-easy-social-share-buttons-for-wordpress/'));	
	ESSBOptionsStructureHelper::field_component('social', 'affiliate', 'essb5_advanced_affiliate_options', 'false');	
}

/** Custom Share **/
if (!essb_option_bool_value('deactivate_module_customshare')) {	
	ESSBOptionsStructureHelper::panel_start('social', 'customshare', esc_html__('Enable global custom share', 'essb'), '', 'fa21 fa fa-share', array("mode" => "switch", 'switch_id' => 'customshare', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
	ESSBOptionsStructureHelper::hint('social', 'customshare', '', 'Overwrite the share parameters for the entire website. Note that most of the social networks accept only the URL as a shared parameter. To ensure your custom message will work properly fill the same information in the social media optimization tags for the shared URL too.', '', 'glowhelp');
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'customshare', 'customshare_url', esc_html__('Custom share URL', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'customshare', 'customshare_text', esc_html__('Custom share message', 'essb'), '');
	ESSBOptionsStructureHelper::field_file('social', 'customshare', 'customshare_image', esc_html__('Custom share image', 'essb'), '');
	ESSBOptionsStructureHelper::field_textarea('social', 'customshare', 'customshare_description', esc_html__('Custom share description', 'essb'), '');
	ESSBOptionsStructureHelper::panel_end('social', 'customshare');
	
	ESSBOptionsStructureHelper::field_component('social', 'customshare', 'essb5_advanced_customshare_options', 'false');
}

/** Message Before Share **/
if (!essb_option_bool_value('deactivate_module_message')) {
	ESSBOptionsStructureHelper::help('social', 'message', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/custom-message-before-or-above-share-buttons/'));	
	ESSBOptionsStructureHelper::panel_start('social', 'message', esc_html__('User message before share buttons', 'essb'), '', 'fa21 ti-arrow-right', array("mode" => "toggle", 'state' => 'closed'));
		
	ESSBOptionsStructureHelper::field_editor('social', 'message', 'message_share_before_buttons', esc_html__('Message before share buttons', 'essb'), esc_html__('You can use following variables to create personalized message: %%title%% - displays current post title, %%permalink%% - displays current post address.', 'essb'), 'htmlmixed');
	
	$select_values = array('desktop' => array('title' => 'Desktop', 'content' => '<i class="fa fa-desktop"></i>'),
			'mobile' => array('title' => 'Mobile', 'content' => '<i class="fa fa-mobile"></i>', 'padding' => '12px 16px'),	
			'tablet' => array('title' => 'Tablet', 'content' => '<i class="fa fa-tablet"></i>', 'padding' => '12px 13px'));
	ESSBOptionsStructureHelper::field_group_select('social', 'message', 'message_share_before_buttons_on', esc_html__('Message will appear on', 'essb'), esc_html__('Choose device types where you wish message to appear. Leave blank for all type of devices', 'essb'), $select_values, '', '', '');
	ESSBOptionsStructureHelper::panel_end('social', 'message');
	
	ESSBOptionsStructureHelper::panel_start('social', 'message', esc_html__('User message above share buttons', 'essb'), '', 'fa21 ti-arrow-up', array("mode" => "toggle", 'state' => 'closed'));
	ESSBOptionsStructureHelper::field_editor('social', 'message', 'message_above_share_buttons', esc_html__('Message above share buttons', 'essb'), esc_html__('You can use following variables to create personalized message: %%title%% - displays current post title, %%permalink%% - displays current post address.', 'essb'), 'htmlmixed');
	ESSBOptionsStructureHelper::field_group_select('social', 'message', 'message_above_share_buttons_on', esc_html__('Message will appear on', 'essb'), esc_html__('Choose device types where you wish message to appear. Leave blank for all type of devices', 'essb'), $select_values, '', '', '');
	ESSBOptionsStructureHelper::panel_end('social', 'message');
	
	if (essb_option_bool_value('native_active')) {
		ESSBOptionsStructureHelper::panel_start('social', 'message', esc_html__('User message above native buttons', 'essb'), esc_html__('Enter custom message that will appear above your native buttons (html code supported)', 'essb'), 'fa21 fa fa-comment-o', array("mode" => "toggle", 'state' => 'closed'));
		ESSBOptionsStructureHelper::field_editor('social', 'message', 'message_like_buttons', esc_html__('Message above like buttons', 'essb'), esc_html__('You can use following variables to create personalized message: %%title%% - displays current post title, %%permalink%% - displays current post address.', 'essb'), 'htmlmixed');
		ESSBOptionsStructureHelper::field_group_select('social', 'message', 'message_like_buttons_on', esc_html__('Message will appear on', 'essb'), esc_html__('Choose device types where you wish message to appear. Leave blank for all type of devices', 'essb'), $select_values, '', '', '');
		ESSBOptionsStructureHelper::panel_end('social', 'message');
	}
	
	ESSBOptionsStructureHelper::field_component('social', 'message', 'essb5_advanced_custommessage_options', 'false');
}

/** Click to Tweet */
if (!essb_option_bool_value('deactivate_ctt')) {
	ESSBOptionsStructureHelper::help('social', 'clicktotweet', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/configure-and-add-sharable-quotes-on-your-site-a-k-a-click-to-tweet/'));
	
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'clicktotweet', 'ctt_user', esc_html__('Default @username', 'essb'), esc_html__('Example: appscreo (without @ symbol)', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'clicktotweet', 'ctt_hashtags', esc_html__('Default #hashtags', 'essb'), esc_html__('Example: tag1,tag2', 'essb'));
	ESSBOptionsStructureHelper::field_switch('social', 'clicktotweet', 'cct_hide_mobile', esc_html__('Hide on mobile', 'essb'), 'The hide on mobile option is available for the sharable quote boxes (click to Tweet boxes). The inline Tweet option won\'t disappear.');
	ESSBOptionsStructureHelper::field_switch('social', 'clicktotweet', 'cct_url', esc_html__('Automatically include the page link', 'essb'), 'Automatically include the current page or post URL as a link in the Tweet. Recommended unless you need to set up a custom share URL each time you add a box. The inline Tweet automatically adds the current post or page URL as part of Tweet (when there is no custom URL provided).');
	ESSBOptionsStructureHelper::field_select('social', 'clicktotweet', 'cct_template', esc_html__('Default template', 'essb'), '', array('' => 'Default', 'light' => 'Light', 'dark' => 'Dark', 'qlite' => 'Quote', 'modern' => 'Modern', 'user' => 'User'));

	ESSBOptionsStructureHelper::field_select('social', 'clicktotweet', 'cct_template_inline', esc_html__('Default inline template', 'essb'), '', array('' => 'Default', 'light' => 'Light', 'dark' => 'Dark', 'qlite' => 'Quote', 'modern' => 'Modern', 'user' => 'User', 'same' => 'Same as the click to Tweet box'));
	
	ESSBOptionsStructureHelper::field_component('social', 'clicktotweet', 'essb5_advanced_clicktotweet_options', 'false');
}

/** Custom Buttons */
if (!essb_option_bool_value('deactivate_custombuttons')) {
    
    ESSBOptionsStructureHelper::help('social', 'custombuttons', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/how-to-add-a-custom-button-in-the-share-network-list-adding-any-social-network-for-sharing/'));
    
    
    essb_heading_with_related_section_open('social', 'custombuttons', esc_html__('Custom Share Button', 'essb'), '<i class="ti-share"></i>');
    
	ESSBOptionsStructureHelper::field_switch('social', 'custombuttons', 'custombuttons_enable', esc_html__('Enable custom share buttons', 'essb'), esc_html__('You need to set this to Yes to see your custom network button in the list of social networks.', 'essb'), '', '', '', '', '', 'true');
	
	if (essb_option_bool_value('custombuttons_enable')) {
	   ESSBOptionsStructureHelper::field_component('social', 'custombuttons', 'essb_create_custombuttons', 'true');
	}
	
	essb_heading_with_related_section_close('social', 'custombuttons');
}

if (!essb_option_bool_value('deactivate_module_conversions')) {

	if (essb_option_bool_value('conversions_lite_run')) {
	
	    if (!class_exists('ESSB_Share_Conversions_Pro')) {
	        include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/conversions-pro/class-share-conversions.php');
	    }
	    
	    ESSBOptionsStructureHelper::field_func('conversions', 'share', 'essb_share_conversions_dashboard_report', '', '');
	    ESSBOptionsStructureHelper::field_func('conversions', 'shareposts', 'essb_share_conversions_dashboard_report_posts', '', '');
	}

	if (essb_option_bool_value('conversions_subscribe_lite_run')) {
		
	    if (!class_exists('ESSB_Subscribe_Conversions_Pro')) {
	        include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/conversions-pro/class-subscribe-conversions.php');
	    }	    
				
		ESSBOptionsStructureHelper::field_func('conversions', 'subscribe', 'essb_subscribe_conversions_dashboard_report', '', '');
	}
	
}

//*** Help Functions of that settings screen
function essb_postions_with_custom_networks5($as_text = false) {
	$result = array();

	foreach ( essb_avaliable_content_positions () as $key => $data ) {
		$key = str_replace("content_", "", $key);
		$position_networks = essb_option_value ( $key.'_networks' );

		if (is_array($position_networks) && essb_option_bool_value($key.'_activate')) {
			$result[] = array('key' => $key, 'title' => $data ['label']);
		}
	}

	foreach ( essb_available_button_positions () as $key => $data ) {
		$position_networks = essb_option_value ( $key.'_networks' );

		if (is_array($position_networks)) {
			$result[] = array('key' => $key, 'title' => $data ['label']);
		}
	}

	$key = 'mobile';
	$position_networks = essb_option_value ( $key.'_networks' );
	if (is_array($position_networks)) {
		$result[] = array('key' => $key, 'title' => 'Mobile Devices');
	}

	$key = 'sharebar';
	$position_networks = essb_option_value ( $key.'_networks' );
	if (is_array($position_networks)) {
		$result[] = array('key' => $key, 'title' => 'Mobile Share Bar');
	}

	$key = 'sharepoint';
	$position_networks = essb_option_value ( $key.'_networks' );
	if (is_array($position_networks)) {
		$result[] = array('key' => $key, 'title' => 'Mobile Share Point');
	}

	$key = 'sharebottom';
	$position_networks = essb_option_value ( $key.'_networks' );
	if (is_array($position_networks)) {
		$result[] = array('key' => $key, 'title' => 'Mobile Share Buttons Bar');
	}

	if (!$as_text) {
		return $result;
	}
	else {
		$output = '';
		foreach ($result as $data) {
			$output .= ($output != '') ? ', '.$data['title'] : $data['title'];
		}

		return $output;
	}
}

function essb_postions_with_custom_options5($as_text = false) {
	$result = array();

	foreach ( essb_avaliable_content_positions () as $key => $data ) {
		$key = str_replace("content_", "", $key);
		$position_networks = essb_option_value ( $key.'_activate' );

		if ($position_networks == 'true') {
			$result[] = array('key' => $key, 'title' => $data ['label']);
		}
	}

	foreach ( essb_available_button_positions () as $key => $data ) {
		$position_networks = essb_option_value ( $key.'_activate' );

		if ($position_networks == 'true') {
			$result[] = array('key' => $key, 'title' => $data ['label']);
		}
	}

	$key = 'mobile';
	$position_networks = essb_option_value ( $key.'_activate' );
	if ($position_networks == 'true') {
		$result[] = array('key' => $key, 'title' => 'Mobile Devices');
	}

	$key = 'sharebar';
	$position_networks = essb_option_value ( $key.'_activate' );
	if ($position_networks == 'true') {
		$result[] = array('key' => $key, 'title' => 'Mobile Share Bar');
	}

	$key = 'sharepoint';
	$position_networks = essb_option_value ( $key.'_activate' );
	if ($position_networks == 'true') {
		$result[] = array('key' => $key, 'title' => 'Mobile Share Point');
	}

	$key = 'sharebottom';
	$position_networks = essb_option_value ( $key.'_activate' );
	if ($position_networks == 'true') {
		$result[] = array('key' => $key, 'title' => 'Mobile Share Buttons Bar');
	}

	if (!$as_text) {
		return $result;
	}
	else {
		$output = '';
		foreach ($result as $data) {
			$output .= ($output != '') ? ', '.$data['title'] : $data['title'];
		}

		return $output;
	}
}

function essb5_main_network_selection() {
	essb_component_network_selection();
}

function essb5_main_template_selection() {
	essb_component_template_select();
}

function essb5_main_buttonstyle_selection() {
	essb_component_buttonstyle_select();
}

function essb5_main_animation_selection() {
	essb_component_animation_select();
}

function essb5_main_singlecounter_selection() {
	essb_component_counterpos_select();
}

function essb5_main_totalcoutner_selection() {
	essb_component_totalcounterpos_select();
}

function essb5_main_alignment_choose() {
	$select_values = array('' => array('title' => 'Left', 'content' => '<i class="ti-align-left"></i>'),
			'center' => array('title' => 'Center', 'content' => '<i class="ti-align-center"></i>'),
			'right' => array('title' => 'Right', 'content' => '<i class="ti-align-right"></i>'),
			'stretched' => array('title' => 'Stetched', 'content' => '<i class="ti-layout-slider"></i>'));
	
	$value = essb_option_value('button_pos');
	
	essb_component_options_group_select('button_pos', $select_values, '', $value, 'essb_options', 'essb-align-selector control_essb-width-section');
	
}

function essb5_main_button_width_choose() {
	$value = essb_option_value('button_width');
	
	$select_values = array('' => array('title' => 'Automatic Width', 'content' => 'AUTO', 'isText'=>true),
			'fixed' => array('title' => 'Fixed Width', 'content' => 'Fixed', 'isText'=>true),
			'full' => array('title' => 'Full Width', 'content' => 'Full', 'isText'=>true),
			'flex' => array('title' => 'Fluid', 'content' => 'Fluid', 'isText'=>true),
			'column' => array('title' => 'Columns', 'content' => 'Columns', 'isText'=>true),);
	
	essb_component_options_group_select('button_width', $select_values, '', $value);
}



function essb5_live_preview() {
	
	// preparing actual list of social networks from general plugin section screen
	$all_networks = essb_available_social_networks(true);
	$active_networks = essb_option_value('networks');
	$r = array();
	if (!is_array($active_networks)) {
		$r[] = array('key' => 'facebook', 'name' => 'Facebook');
		$r[] = array('key' => 'twitter', 'name' => 'X (formerly Twitter)');
		$r[] = array('key' => 'pinterest', 'name' => 'Pinterest');
		$r[] = array('key' => 'linkedin', 'name' => 'LinkedIn');
	}
	else {
		foreach ($active_networks as $key) {
			$r[] = array('key' => $key, 'name' => isset($all_networks[$key]) ? $all_networks[$key]['name'] : $key);
		}
	}
	
	$code = '<div class="essb-component-buttons-livepreview" data-settings="essb_global_preview">';
	$code .= '</div>';
	
	$code .= "<script type=\"text/javascript\">

	var essb_global_preview = {
			'networks': ".json_encode($r).",
			'template': 'essb_field_style',
			'button_style': 'essb_field_button_style',
			'button_size': 'essb_options_button_size',
			'align': 'essb_options_button_pos',
			'nospace': 'essb_field_nospace',
			'counter': 'essb_field_show_counter',
			'counter_pos': 'essb_field_counter_pos',
			'total_counter_pos': 'essb_field_total_counter_pos',
			'width': 'essb_options_button_width',
			'animation': 'essb_field_css_animations',
			'fixed_width': 'essb_options_fixed_width_value',
			'fixed_align': 'essb_options_fixed_width_align',
			'columns_count': 'essb_options_fullwidth_share_buttons_columns',
			'columns_align': 'essb_options_fullwidth_share_buttons_columns_align',
			'full_button': 'essb_options_fullwidth_share_buttons_correction',
			'full_align': 'essb_options_fullwidth_align',
			'full_first': 'essb_options_fullwidth_first_button',
			'full_second': 'essb_options_fullwidth_second_button',
			'flex_align': 'essb_options_flex_width_align',
			'flex_width': 'essb_options_flex_width_value',
			'flex_button': 'essb_options_flex_button_value'				
	};
	
	</script>";
	 
	echo $code;	
}

function essb5_custom_position_networks() {
	$positions_with_networks = essb_postions_with_custom_networks5(true);
	if ($positions_with_networks != '') {
		
		echo '<div class="essb-usefull-hint">';
		echo '<div class="title">';
		esc_html_e('The changes inside global options won\'t reflect on the following positions that overwrite the network list:', 'essb');
		echo '</div>';
		echo '<div class="positions tag-list">';
		
		$positions_with_networks_list = essb_postions_with_custom_networks5();
		foreach ($positions_with_networks_list as $data) {
			$key = isset($data['key']) ? $data['key'] : '';
			$title = isset($data['title']) ? $data['title'] : '';
				
			echo '<span class="tag">'.$title.'</span>';
		}
		
		echo '</div>';
		echo '</div>';
	}
	
}

function essb5_custom_position_settings() {
	$positions_with_networks = essb_postions_with_custom_options5(true);
	if ($positions_with_networks != '') {
	
		echo '<div class="essb-usefull-hint">';
		echo '<div class="title">';
		esc_html_e('The changes inside global options won\'t reflect on the following positions that overwrite the template and style:', 'essb');
		echo '</div>';
		echo '<div class="positions tag-list">';
		
		$positions_with_networks_list = essb_postions_with_custom_options5();
		foreach ($positions_with_networks_list as $data) {
			$key = isset($data['key']) ? $data['key'] : '';
			$title = isset($data['title']) ? $data['title'] : '';
		
			echo '<span class="tag">'.$title.'</span>';
		}
		
		echo '</div>';
		
		echo '</div>';		
	}
}

global $essb5_options_translate;
$essb5_options_translate = array();
$essb5_options_translate['essb5_main_template_selection'] = 'style';
$essb5_options_translate['essb5_main_buttonstyle_selection'] = 'button_style';
$essb5_options_translate['essb5_main_animation_selection'] = 'css_animations';
$essb5_options_translate['essb5_main_singlecounter_selection'] = 'counter_pos';
$essb5_options_translate['essb5_main_totalcoutner_selection'] = 'total_counter_pos';

function essb5_additional_counter_options_only() {
    
    echo essb5_generate_code_advanced_settings_panel(
        esc_html__('Share Counter Update Setup', 'essb'),
        esc_html__('Configure global options related to the update of the share counters on your website (Facebook, X (formerly Twitter), LinkedIn, AddThis).', 'essb'),
        'update-counter', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500');
        
    echo essb5_generate_code_advanced_settings_panel(
        esc_html__('Internal Share Counter', 'essb'),
        'Configure internal share counters based on button clicks for all networks that do not have a share counter.',
        'internal-counter', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500', '', '', '', false, '',
        'automation-deactivate-internal', esc_html__('Fully Deactivate Internal Counters', 'essb'));
    
    // logger
    if (essb_option_bool_value('cache_counter_logging')) {
        echo essb5_generate_code_advanced_settings_panel(
            esc_html__('Update Log', 'essb') . essb_generate_expert_badge(),
            'Trace the share counter update. The log shows the requests for each URL to the social network APIs and the raw response result. The log updates each time you update your share counter. You have access to the latest 99 requests.',
            'counter-update-log', '', esc_html__('View', 'essb'), 'ti-server', 'no', '1000', '', '', esc_html__('Update Log', 'essb'), false, '',
            'automation-clear-counter-update-log', esc_html__('Clear log', 'essb'));
    }
}


function essb5_additional_counter_options() {	
    
	ESSBOptionsFramework::draw_heading(esc_html__('Individual Network & Total Counter Display', 'essb'), '7', '', '', 'essb-internal-heading6 pb0', '<i class="ti-pencil-alt"></i>');
	ESSBOptionsFramework::draw_holder_start(array('class' => 'essb-related-heading7'));
	echo essb5_generate_code_advanced_settings_panel(
			esc_html__('Individual Network Share Counter', 'essb'),
			'',
			'single-counter', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500');

	echo essb5_generate_code_advanced_settings_panel(
			esc_html__('Total Share Counter', 'essb'),
			'',
			'total-counter', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500');

    ESSBOptionsFramework::draw_holder_end();
			    
			
    ESSBOptionsFramework::draw_heading(esc_html__('Additional Share Counter Options & Features', 'essb'), '7', '', '', 'essb-internal-heading6 pb0', '<i class="ti-pencil-alt"></i>');
    ESSBOptionsFramework::draw_holder_start(array('class' => 'essb-related-heading7'));

    if (!essb_option_bool_value('deactivate_ansp')) {
        echo essb5_generate_code_advanced_settings_panel(
            esc_html__('Avoid Social Negative Proof', 'essb'),
            esc_html__('Avoid social negative proof allows you to hide button counters or total counter till a defined value of shares is reached', 'essb'),
            'avoid-negative-proof', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500', '', '', '', false, '', 
            'automation-avoid-negative', esc_html__('Automatically Configure The Avoid Negative Proof', 'essb'));
            
    }
    
    if (!essb_option_bool_value('deactivate_ssr')) {
        echo essb5_generate_code_advanced_settings_panel(
            esc_html__('Recover My Shares', 'essb'),
            esc_html__('Share counter recovery allows you restore back shares once you make a permalink change (including installing a SSL certificate). Share recovery will show back shares only if they are present for both versions of URL (before and after change).', 'essb'),
            'share-recovery', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500', '', '', '', false, '',
            'automation-https-recover', esc_html__('Automatically Configure Recovery When Moved to HTTPS', 'essb'));
    }   
	
    if (!essb_option_bool_value('deactivate_expertcounters')) {
    	echo essb5_generate_code_advanced_settings_panel(
    			esc_html__('Additional Counter Display Rules', 'essb') . essb_generate_expert_badge(),
    			esc_html__('Configure additional options for share counter display, update, etc.', 'essb'),
    			'other-counter', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500', '', '', esc_html__('Additional Counter Display Rules', 'essb'));
    }
	
	if (!essb_option_bool_value('deactivate_fakecounters')) {
		echo essb5_generate_code_advanced_settings_panel(
				esc_html__('Fake Share Counters', 'essb'),
				esc_html__('Increase the number of shares with a multiplier (fake values). As an addition, you can also change the values to internal counters for all networks.', 'essb'),
				'share-fake', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500');
	}
	ESSBOptionsFramework::draw_holder_end();
}

function essb5_advanced_deactivate_networks_button() {
	echo essb5_generate_code_advanced_settings_panel(
			esc_html__('Manage Available Social Networks', 'essb'),
			esc_html__('Deactivate the networks you won\'t use on your website. It will simplify the network customization process.', 'essb'),
			'advanced-networks', '', esc_html__('Manage', 'essb'), 'ti-layout-list-thumb', 'yes', '', '', 
			'ti-layout-list-thumb', '', false, 
			'https://docs.socialsharingplugin.com/knowledgebase/manage-available-installed-share-networks/',
	        'automatic-network-setup', esc_html__('Automated Setup Using My Networks', 'essb'));

	echo essb5_generate_code_advanced_settings_panel(
			esc_html__('Manage Network Device Visibility (Mobile, Desktop)', 'essb'),
			esc_html__('Enable responsive network visibility. Easily limit the display of particular networks on desktop or mobile.', 'essb'),
			'advanced-networks-visibility', '', esc_html__('Manage', 'essb'), 'ti-eye', 'no', '', '', 'ti-eye',
			'', false,
			'https://docs.socialsharingplugin.com/knowledgebase/manage-network-device-visibility-mobile-tablet-desktop/',
	        'automatic-responsive-networks', esc_html__('Automatic Responsive Networks\' Setup', 'essb'));	
}

function essb5_advanced_adaptive_styles() {
	echo essb5_generate_code_advanced_settings_panel(
			esc_html__('Automatic Adaptive Position Styles', 'essb'),
			esc_html__('Let plugin configure for additional positions automatic styles based on those you set here. If you are not looking for an advanced setup recommend trying the feature.', 'essb'),
			'adaptive-styles', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '600', '', 'ti-widget', '', false, '',
	        'automatic-positions-setup', esc_html__('Enable Automatic Adaptive Styles', 'essb'));

}

function essb5_advanced_sso_options() {
	echo essb5_generate_code_advanced_settings_panel(
	    esc_html__('Advanced Generation Options', 'essb') . essb_generate_expert_badge(),
			esc_html__('Manage additional features connected with the Open Graph tags generation.', 'essb'),
			'facebook-ogtags', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500', '', '', esc_html('Advanced generation options', 'essb'));

}

function essb5_advanced_affiliate_options() {
	echo essb5_generate_code_advanced_settings_panel(
			esc_html__('myCred Integration', 'essb'),
			esc_html__('Configure integration with the myCred plugin to award users for sharing content.', 'essb'),
			'integration-mycred', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500');

	echo essb5_generate_code_advanced_settings_panel(
			esc_html__('AffiliateWP Integration', 'essb'),
			esc_html__('Configure integration with the AffiliateWP plugin to share links that contain an affiliate (referral parameter).', 'essb'),
			'integration-affiliatewp', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500');

    echo essb5_generate_code_advanced_settings_panel(
			    esc_html__('SliceWP Integration', 'essb'),
			    esc_html__('Configure integration with the SliceWP plugin to share links that contain an affiliate (referral parameter).', 'essb'),
			    'integration-slicewp', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500');
			    
	
	echo essb5_generate_code_advanced_settings_panel(
			esc_html__('WP Affiliates Integration', 'essb'),
			esc_html__('Configure integration with the WP Affiliates plugin to share links that contain an affiliate (referral parameter).', 'essb'),
			'integration-affiliates', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500');
	
	echo essb5_generate_code_advanced_deactivate_panel(esc_html__('Deactivate Affiliate Plugins Integration Functions', 'essb'),
			esc_html__('The deactivation of a component will remove it from plugin settings and stop its code from running. At any time you can activate back again all deactivated features from the "Manage Plugin Features" menu.', 'essb'),
			'deactivate_module_affiliate', '', esc_html__('Deactivate', 'essb'), 'fa fa-close', 'fa fa-close ao-red-icon');
}

function essb5_advanced_sso_deactivate_options() {
	echo essb5_generate_code_advanced_deactivate_panel(esc_html__('Deactivate Social Share Optimization Functions', 'essb'),
			esc_html__('The deactivation of a component will remove it from plugin settings and stop its code from running. At any time you can activate back again all deactivated features from the "Manage Plugin Features" menu.', 'essb'),
			'deactivate_module_shareoptimize', '', esc_html__('Deactivate', 'essb'), 'fa fa-close', 'fa fa-close ao-red-icon');
}

function essb5_advanced_pinpro_deactivate_options() {
	echo essb5_generate_code_advanced_deactivate_panel(esc_html__('Deactivate Pinterest Pro Features', 'essb'),
			esc_html__('The deactivation of a component will remove it from plugin settings and stop its code from running. At any time you can activate back again all deactivated features from the "Manage Plugin Features" menu.', 'essb'),
			'deactivate_module_pinterestpro', '', esc_html__('Deactivate', 'essb'), 'fa fa-close', 'fa fa-close ao-red-icon');
}

function essb5_advanced_customshare_options() {
	echo essb5_generate_code_advanced_deactivate_panel(esc_html__('Deactivate Custom Share Message Features', 'essb'),
			esc_html__('The deactivation of a component will remove it from plugin settings and stop its code from running. At any time you can activate back again all deactivated features from the "Manage Plugin Features" menu.', 'essb'),
			'deactivate_module_customshare', '', esc_html__('Deactivate', 'essb'), 'fa fa-close', 'fa fa-close ao-red-icon');	
}

function essb5_advanced_custommessage_options() {
	echo essb5_generate_code_advanced_deactivate_panel(esc_html__('Deactivate Custom Message Before/Above Buttons', 'essb'),
			esc_html__('The deactivation of a component will remove it from plugin settings and stop its code from running. At any time you can activate back again all deactivated features from the "Manage Plugin Features" menu.', 'essb'),
			'deactivate_module_message', '', esc_html__('Deactivate', 'essb'), 'fa fa-close', 'fa fa-close ao-red-icon');	
}

function essb5_advanced_clicktotweet_options() {
	echo '<div class="essb-clicktotweet-preview">';
	echo '</div>';
	
	echo '<div class="essb-clicktotweet-inline-preview">';
	echo '</div>';
	
	echo essb5_generate_code_advanced_settings_panel(
	    esc_html__('Generate Click to Tweet Shortcode', 'essb'),
	    esc_html__('Generate a Click to Tweet shortcode that you can add anywhere inside the content. If you are using WordPress block editor or Elementor page builder there are ready-to-use blocks for adding Click to Tweet boxes.', 'essb'),
	    'shortcode-ctt', 'ao-shortcode', esc_html__('Generate', 'essb'), 'fa fa-code', 'no', '500', '', 'ti-twitter', esc_html__('Shortcode Generator', 'essb'), true);
	    
	
	echo essb5_generate_code_advanced_deactivate_panel(esc_html__('Deactivate Click to Tweet', 'essb'),
			esc_html__('The deactivation of a component will remove it from plugin settings and stop its code from running. At any time you can activate back again all deactivated features from the "Manage Plugin Features" menu.', 'essb'),
			'deactivate_ctt', '', esc_html__('Deactivate', 'essb'), 'fa fa-close', 'fa fa-close ao-red-icon');
}

function essb5_advanced_shorturl_options() {
	echo essb5_generate_code_advanced_deactivate_panel(esc_html__('Deactivate Short URL Generation Features', 'essb'),
			esc_html__('The deactivation of a component will remove it from plugin settings and stop its code from running. At any time you can activate back again all deactivated features from the "Manage Plugin Features" menu.', 'essb'),
			'deactivate_module_shorturl', '', esc_html__('Deactivate', 'essb'), 'fa fa-close', 'fa fa-close ao-red-icon');
}

function essb5_additional_analytics_options() {
	if (!essb_option_bool_value('deactivate_module_analytics')) {
		echo essb5_generate_code_advanced_settings_panel(
				esc_html__('Share Button Analytics', 'essb'),
				esc_html__('Build-in analytics is exteremly powerful tool which will let you to track how your visitors interact with share buttons. Get reports by positions, device type, social networks, for periods or for content.', 'essb'),
				'analytics', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500');
		
	}
	
	if (!essb_option_bool_value('deactivate_module_conversions')) {
		echo essb5_generate_code_advanced_settings_panel(
				esc_html__('Conversion Tracking', 'essb') . essb_generate_expert_badge(),
				esc_html__('Collect analytics data for share buttons\' conversions. You can see the conversion rate of each network, position, or even the network in the position. That can help optimize the usage of positions and networks on your website.', 'essb'),
		    'share-conversions', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500', '', '', esc_html__('Conversion Tracking', 'essb'));
		
	}
	
	if (!essb_option_bool_value('deactivate_module_metrics')) {
		echo essb5_generate_code_advanced_settings_panel(
				esc_html__('Share Metrics Lite', 'essb'),
				esc_html__('Social Metrics data collection require to have share counters active on your site. All data will be updated and stored inside metrics dashboard on each share counter update. Metrics data cannot be collected if you use real time share counters.', 'essb'),
				'metrics-lite', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500');
	
	}
	
	if (!essb_option_bool_value('deactivate_module_google_analytics')) {
		echo essb5_generate_code_advanced_settings_panel(
				esc_html__('Google Analytics Tracking', 'essb'),
				esc_html__('Attach UTM parameters to the shared URLs or enable Events tracking. UTM parameters allow you to track the source of the traffic for your social shares in Google Analytics.', 'essb'),
				'share-google-analytics', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500');
		
	}
}

function essb5_advanced_aftershare_options() {
	echo essb5_generate_code_advanced_settings_panel(
	    esc_html__('Additional Appearance Options', 'essb') . essb_generate_expert_badge(),
			esc_html__('Inside additional appearance options, you can configure the message width, mobile appearance, one-time per user appearance, etc.', 'essb'),
			'after-share', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500', '', '', esc_html__('After Share Appearance Options', 'essb'));
}

function essb5_advanced_aftershare_deactivate_options() {
	echo essb5_generate_code_advanced_deactivate_panel(esc_html__('Deactivate After Share Features', 'essb'),
			esc_html__('The deactivation of a component will remove it from plugin settings and stop its code from running. At any time you can activate back again all deactivated features from the "Manage Plugin Features" menu.', 'essb'),
			'deactivate_module_aftershare', '', esc_html__('Deactivate', 'essb'), 'fa fa-close', 'fa fa-close ao-red-icon');
}

function essb5_advanced_other_features_activate() {
	$share_features = ESSBControlCenter::$features_group['share'];

	foreach ($share_features as $feature) {
		if (ESSBControlCenter::feature_is_deactivated($feature)) {
			echo essb5_generate_code_advanced_activate_panel(ESSBControlCenter::get_feature_title($feature),
					ESSBControlCenter::get_feature_long_description($feature),
					ESSBControlCenter::get_feature_deactivate_option($feature), 
					'', esc_html__('Activate', 'essb'), 'fa fa-check', ESSBControlCenter::get_feature_icon($feature).' ao-darkblue-icon', 
					'ao-additional-features-activate', 'false');
				
		}
	}
}

function essb5_advanced_aftershare_activate_options() {
	echo essb5_generate_code_advanced_activate_panel(esc_html__('Enable After Share Events', 'essb'),
			esc_html__('Show an additional pop-up window with various types of messages for those that share content on your site. A unique option to connect with most engaged visitors - example: show a follow button (native or static) after content is shared or invite to join your mailing list.', 'essb'),
			'afterclose_active', '', esc_html__('Enable', 'essb'), 'fa fa-check', 'ti-share ao-lightblue-icon');
}

/**
 * Creating custom buttons code
 */

function essb_create_custombuttons($options = array()) {
	
	echo '<div class="essb-flex-grid-r">';
	echo '<a href="#" class="ao-new-subscribe-design ao-new-sharecustom-button" data-title="'.esc_html__('New Custom Button', 'essb').'"><span class="essb_icon fa fa-plus-square"></span><span>'.esc_html__('Create new custom button', 'essb').'</span></a>';
	if (ESSBActivationManager::isActivated()) {
	    echo '<a href="#" class="ao-new-subscribe-design ao-import-sharecustom-button" data-title="'.esc_html__('Import Custom Button', 'essb').'"><span class="essb_icon fa fa-cloud-upload"></span><span>'.esc_html__('Import', 'essb').'</span></a>';
	}
	echo '<a href="#" class="ao-new-subscribe-design ao-deleteall-sharecustom-button" data-title="'.esc_html__('Delete All', 'essb').'"><span class="essb_icon fa fa-close"></span><span>'.esc_html__('Remove All', 'essb').'</span></a>';
	
	echo '<a href="https://socialsharingplugin.com/library/" target="_blank" class="ao-new-subscribe-design ao-hub-followcustom-button" data-title="'.esc_html__('Go to HUB', 'essb').'"><span class="essb_icon fa fa-database"></span><span>'.esc_html__('Get more networks', 'essb').'</span></a>';
	
	echo '</div>';
	
	if (! function_exists ( 'essb_get_custom_buttons' )) {
		include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/helpers/custombuttons-helper.php');
	}
	
	$user_buttons = essb_get_custom_buttons();
	echo '<div class="essb-custom-button-list">';
	foreach ($user_buttons as $id => $data) {
		$name = isset($data['name']) ? $data['name'] : 'Untitled Button';
		$icon = isset($data['icon']) ? $data['icon'] : '';
		$bgcolor = isset($data['bgcolor']) ? $data['bgcolor'] : '';
		$iconcolor = isset($data['iconcolor']) ? $data['iconcolor'] : '';
		$network_color = isset($data['network_color']) ? $data['network_color'] : '';
		
		if ($icon != '') {
			$icon = base64_decode($icon);
		}
		
		if (empty($iconcolor)) {
		    $iconcolor = '#ffffff';
		}
		
		$description = '';
		
		if ($icon != '') {
			$description = '<div class="icon custom-network-'.$id.'">'.stripslashes($icon).'</div>';
			
			if ($bgcolor != '' || $iconcolor != '' || $network_color != '') {
				$description .= '<style>';
				if ($network_color != '') {
				    $description .= '.custom-network-'.$id.' {background-color: '.esc_attr($network_color).';}';
				}
				else if ($bgcolor != '') {
					$description .= '.custom-network-'.$id.' {background-color: '.esc_attr($bgcolor).';}';
				}
				
				if ($iconcolor != '') {
					$description .= '.custom-network-'.$id.' svg path {fill: '.esc_attr($iconcolor).'!important;}';
				}
				$description .= '</style>';
			}
		}

		$custom_buttons = '<a href="#" class="essb-btn tile-config ao-new-sharecustom-button" data-network="'.$id.'" data-title="Manage Existing Button"><i class="fa fa-cog"></i>'.esc_html__('Edit', 'essb').'</a>';
		$custom_buttons .= '<a href="#" class="essb-btn tile-deactivate ao-remove-sharecustom-button" data-network="'.$id.'" data-title="Remove Existing Button"><i class="fa fa-close"></i>'.esc_html__('Remove', 'essb').'</a>';
		if (ESSBActivationManager::isActivated()) {
		    $custom_buttons .= '<a href="#" class="essb-btn tile-general ao-export-sharecustom-button" data-network="'.$id.'" data-title="Export Existing Button"><i class="fa fa-cloud-download"></i>'.esc_html__('Export', 'essb').'</a>';
		}
		$options_load = array();
		$options_load['title'] = $name;
		$options_load['description'] = $description;
		$options_load['button_center'] = 'true';
		$options_load['tag'] = $id;
		$options_load['custom_buttons'] = $custom_buttons;

		essb5_advanced_options_small_settings_tile(array('element_options' => $options_load));
	}
	echo '</div>';
	
}