<?php
/**
 * Social Followers Counter
 *
 * @package EasySocialShareButtons
 * @since 3.0
 */

if (class_exists('ESSBControlCenter')) {
	if (!essb_option_bool_value('deactivate_module_followers')) {
		if (essb_options_bool_value('fanscounter_active')) {
			ESSBControlCenter::register_sidebar_section_menu('follow', 'follow-1', esc_html__('Settings', 'essb'));
			ESSBControlCenter::register_sidebar_section_menu('follow', 'follow-2', esc_html__('Networks', 'essb'));
			ESSBControlCenter::register_sidebar_section_menu('follow', 'follow-3', esc_html__('Floating Sidebar', 'essb'));
			ESSBControlCenter::register_sidebar_section_menu('follow', 'follow-5', esc_html__('Content Bar', 'essb'));
			ESSBControlCenter::register_sidebar_section_menu('follow', 'follow-4', esc_html__('Custom Layout Builder', 'essb'));
		}
		else {
			ESSBControlCenter::register_sidebar_section_menu('follow', 'follow-1', esc_html__('Settings', 'essb'));
		}
	}
	
	if (!essb_option_bool_value('deactivate_module_profiles')) {
		if (essb_option_bool_value('profiles_widget')) {
			ESSBControlCenter::register_sidebar_section_menu('profiles', 'profiles-1', esc_html__('Settings', 'essb'));
			ESSBControlCenter::register_sidebar_section_menu('profiles', 'profiles-2', esc_html__('Networks', 'essb'));
			ESSBControlCenter::register_sidebar_section_menu('profiles', 'profiles-3', esc_html__('Floating Sidebar', 'essb'));
			ESSBControlCenter::register_sidebar_section_menu('profiles', 'profiles-4', esc_html__('Content Bar', 'essb'));
		}
		else {
			ESSBControlCenter::register_sidebar_section_menu('profiles', 'profiles-1', esc_html__('Settings', 'essb'));
		}
	}
	
	if (!essb_option_bool_value('deactivate_module_natives')) {
		if (essb_option_bool_value('native_active')) {
			ESSBControlCenter::register_sidebar_section_menu('natives', 'native-1', esc_html__('Networks', 'essb'));
			ESSBControlCenter::register_sidebar_section_menu('natives', 'native-2', esc_html__('Skinned Buttons', 'essb'));
			ESSBControlCenter::register_sidebar_section_menu('natives', 'native-3', esc_html__('Privacy Buttons', 'essb'));
		}
		else {
			ESSBControlCenter::register_sidebar_section_menu('natives', 'native-1', esc_html__('Settings', 'essb'));
		}
	}
	
	if (!essb_option_bool_value('deactivate_module_facebookchat')) {
		ESSBControlCenter::register_sidebar_section_menu('chat', 'facebookchat', esc_html__('Facebook Messenger Live Chat', 'essb'));
	}
	
	if (!essb_option_bool_value('deactivate_module_clicktochat')) {
		ESSBControlCenter::register_sidebar_section_menu('chat', 'clicktochat', esc_html__('Click to Chat (WhatsApp, Viber)', 'essb'));
	}
	
	if (!essb_option_bool_value('deactivate_module_skypechat')) {
		ESSBControlCenter::register_sidebar_section_menu('chat', 'skypechat', esc_html__('Skype Live Chat', 'essb'));
	}
}

if (!essb_option_bool_value('deactivate_module_followers')) {
	ESSBOptionsStructureHelper::menu_item('display', 'follow', esc_html__('Social Followers Counter', 'essb'), ' ti-heart', 'activate_first', 'follow-1');
	ESSBOptionsStructureHelper::submenu_item('follow', 'follow-1', esc_html__('Settings', 'essb'));
	ESSBOptionsStructureHelper::submenu_item('follow', 'follow-2', esc_html__('Social Networks', 'essb'));
	ESSBOptionsStructureHelper::submenu_item('follow', 'follow-3', esc_html__('Follow Me Sidebar', 'essb'));
	ESSBOptionsStructureHelper::submenu_item('follow', 'follow-5', esc_html__('Follow Me Content Bar', 'essb'));
	ESSBOptionsStructureHelper::submenu_item('follow', 'follow-4', esc_html__('Custom Layout Builder', 'essb'));
}

if (!essb_option_bool_value('deactivate_module_profiles')) {
	ESSBOptionsStructureHelper::menu_item('display', 'profiles', esc_html__('Social Profiles', 'essb'), ' ti-user', 'activate_first', 'profiles-1');
	ESSBOptionsStructureHelper::submenu_item('profiles', 'profiles-1', esc_html__('Settings', 'essb'));
	ESSBOptionsStructureHelper::submenu_item('profiles', 'profiles-2', esc_html__('Social Networks', 'essb'));
	ESSBOptionsStructureHelper::submenu_item('profiles', 'profiles-3', esc_html__('Profile Links as Sidebar', 'essb'));
	ESSBOptionsStructureHelper::submenu_item('profiles', 'profiles-4', esc_html__('Profile Links Below Content', 'essb'));
}

if (!essb_option_bool_value('deactivate_module_natives')) {
	ESSBOptionsStructureHelper::menu_item('display', 'native', esc_html__('Like, Follow & Subscribe', 'essb'), ' ti-thumb-up', 'activate_first', 'native-1');
	ESSBOptionsStructureHelper::submenu_item('natives', 'native-1', esc_html__('Social Networks', 'essb'));
	ESSBOptionsStructureHelper::submenu_item('natives', 'native-2', esc_html__('Skinned buttons', 'essb'));
	ESSBOptionsStructureHelper::submenu_item('natives', 'native-3', esc_html__('Social Privacy', 'essb'));
}

if (!essb_option_bool_value('deactivate_module_clicktochat')) {
	ESSBOptionsStructureHelper::menu_item('chat', 'clicktochat', esc_html__('Click To Chat: WhatsApp, Viber', 'essb'), 'ti-facebook');

		ESSBOptionsStructureHelper::field_switch('chat', 'clicktochat', 'click2chat_activate', esc_html__('Activate Click To Chat Usage', 'essb'), esc_html__('Set this option to Yes if you wish to use the Click to Chat module on your site', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_text', esc_html__('Chat Button Text', 'essb'), esc_html__('Enter your own custom text that will appear on chat start button', 'essb'));
		ESSBOptionsStructureHelper::field_color('chat', 'clicktochat', 'click2chat_bgcolor', esc_html__('Button Background Color', 'essb'), esc_html__('Customize the background color of chat button.', 'essb'));
		ESSBOptionsStructureHelper::field_color('chat', 'clicktochat', 'click2chat_color', esc_html__('Button Text Color', 'essb'), esc_html__('Customize the text color of chat button.', 'essb'));
		$select_values = array('whatsapp' => array('title' => 'WhatsApp Icon', 'content' => '<i class="essb_icon_whatsapp"></i>'),
				'comments' => array('title' => 'Chat Icon', 'content' => '<i class="essb_icon_comments"></i>'),
				'comment-o' => array('title' => 'Chat Icon', 'content' => '<i class="essb_icon_comment-o"></i>'),
				'viber' => array('title' => 'Viber Icon', 'content' => '<i class="essb_icon_viber"></i>'));
		ESSBOptionsStructureHelper::field_toggle('chat', 'clicktochat', 'click2chat_icon', esc_html__('Button Icon', 'essb'), esc_html__('Select custom icon that will be used on button', 'essb'), $select_values);
		$more_options = array ("right" => "Bottom Right", "left" => "Bottom Left" );
		ESSBOptionsStructureHelper::field_select('chat', 'clicktochat', 'click2chat_location', esc_html__('Chat Button Location', 'essb'), esc_html__('Choose where button will appear on screen.', 'essb'), $more_options);
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_welcome_text', esc_html__('Welcome Text', 'essb'), esc_html__('The welcome text will appear just above the section with operators. If not set default text will not appear.', 'essb'));
		
		
		ESSBOptionsStructureHelper::field_checkbox_list('chat', 'clicktochat', 'click2chat_posttypes', esc_html__('Appear only on selected post types', 'essb'), esc_html__('Limit function to work only on selected post types. Leave non option selected to make it work on all', 'essb'), essb_get_post_types(), '', array('source' => 'post_types'));
		ESSBOptionsStructureHelper::field_switch('chat', 'clicktochat', 'click2chat_deactivate_homepage', esc_html__('Deactivate display on homepage', 'essb'), esc_html__('Exclude display of function on home page of your site.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_exclude', esc_html__('Exclude display on', 'essb'), esc_html__('Exclude appearance on posts/pages with these IDs. Comma separated: "11, 15, 125".', 'essb'), '');
		
		ESSBOptionsStructureHelper::panel_start('chat', 'clicktochat', esc_html__('Operator #1 Details', 'essb'), esc_html__('Configure details and usage of operator #1', 'essb'), '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
		ESSBOptionsStructureHelper::field_switch('chat', 'clicktochat', 'click2chat_operator1_active', esc_html__('Active Operator', 'essb'), esc_html__('Set to Yes if you wish this operator to be visible and available for chat.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_operator1_name', esc_html__('Name', 'essb'), esc_html__('Enter Name that will appear on screen', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_operator1_title', esc_html__('Title', 'essb'), esc_html__('Required to load Facebook API. To create one visit Facebook Developer Center.', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_operator1_number', esc_html__('Contact Number', 'essb'), esc_html__('Enter the contact number which will be used to contact with you. This number should contain the selected chat application installed.', 'essb'), '');
		$more_options = array ("" => "WhatsApp", "viber" => "Viber", "email" => "Email", "phone" => "Phone" );
		ESSBOptionsStructureHelper::field_select('chat', 'clicktochat', 'click2chat_operator1_app', esc_html__('Application', 'essb'), esc_html__('Choose the used for chat application for this operator', 'essb'), $more_options);
		ESSBOptionsStructureHelper::field_image('chat', 'clicktochat', 'click2chat_operator1_image', esc_html__('Profile Image', 'essb'), esc_html__('Image that will be displayed as profile picture', 'essb'), '', 'vertical1');
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_operator1_text', esc_html__('Auto Text', 'essb'), esc_html__('This text will pre-poluate the chat field. You can use [title] and [url] to as variables to set the current page title or URL. (WhatsApp Only)', 'essb'), '');
		ESSBOptionsStructureHelper::panel_end('chat', 'clicktochat');

		ESSBOptionsStructureHelper::panel_start('chat', 'clicktochat', esc_html__('Operator #2 Details', 'essb'), esc_html__('Configure details and usage of operator #1', 'essb'), '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
		ESSBOptionsStructureHelper::field_switch('chat', 'clicktochat', 'click2chat_operator2_active', esc_html__('Active Operator', 'essb'), esc_html__('Set to Yes if you wish this operator to be visible and available for chat.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_operator2_name', esc_html__('Name', 'essb'), esc_html__('Enter Name that will appear on screen', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_operator2_title', esc_html__('Title', 'essb'), esc_html__('Required to load Facebook API. To create one visit Facebook Developer Center.', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_operator2_number', esc_html__('Contact Number', 'essb'), esc_html__('Enter the contact number which will be used to contact with you. This number should contain the selected chat application installed.', 'essb'), '');
		
		$more_options = array ("" => "WhatsApp", "viber" => "Viber", "email" => "Email", "phone" => "Phone" );
		ESSBOptionsStructureHelper::field_select('chat', 'clicktochat', 'click2chat_operator2_app', esc_html__('Application', 'essb'), esc_html__('Choose the used for chat application for this operator', 'essb'), $more_options);
		ESSBOptionsStructureHelper::field_image('chat', 'clicktochat', 'click2chat_operator2_image', esc_html__('Profile Image', 'essb'), esc_html__('Image that will be displayed as profile picture', 'essb'), '', 'vertical1');
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_operator2_text', esc_html__('Auto Text', 'essb'), esc_html__('This text will pre-poluate the chat field. You can use [title] and [url] to as variables to set the current page title or URL. (WhatsApp Only)', 'essb'), '');
		ESSBOptionsStructureHelper::panel_end('chat', 'clicktochat');

		ESSBOptionsStructureHelper::panel_start('chat', 'clicktochat', esc_html__('Operator #3 Details', 'essb'), esc_html__('Configure details and usage of operator #1', 'essb'), '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
		ESSBOptionsStructureHelper::field_switch('chat', 'clicktochat', 'click2chat_operator3_active', esc_html__('Active Operator', 'essb'), esc_html__('Set to Yes if you wish this operator to be visible and available for chat.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_operator3_name', esc_html__('Name', 'essb'), esc_html__('Enter Name that will appear on screen', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_operator3_title', esc_html__('Title', 'essb'), esc_html__('Required to load Facebook API. To create one visit Facebook Developer Center.', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_operator3_number', esc_html__('Contact Number', 'essb'), esc_html__('Enter the contact number which will be used to contact with you. This number should contain the selected chat application installed.', 'essb'), '');
		$more_options = array ("" => "WhatsApp", "viber" => "Viber", "email" => "Email", "phone" => "Phone" );
		
		ESSBOptionsStructureHelper::field_select('chat', 'clicktochat', 'click2chat_operator3_app', esc_html__('Application', 'essb'), esc_html__('Choose the used for chat application for this operator', 'essb'), $more_options);
		ESSBOptionsStructureHelper::field_image('chat', 'clicktochat', 'click2chat_operator3_image', esc_html__('Profile Image', 'essb'), esc_html__('Image that will be displayed as profile picture', 'essb'), '', 'vertical1');
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_operator3_text', esc_html__('Auto Text', 'essb'), esc_html__('This text will pre-poluate the chat field. You can use [title] and [url] to as variables to set the current page title or URL. (WhatsApp Only)', 'essb'), '');
		ESSBOptionsStructureHelper::panel_end('chat', 'clicktochat');

		ESSBOptionsStructureHelper::panel_start('chat', 'clicktochat', esc_html__('Operator #4 Details', 'essb'), esc_html__('Configure details and usage of operator #1', 'essb'), '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
		ESSBOptionsStructureHelper::field_switch('chat', 'clicktochat', 'click2chat_operator4_active', esc_html__('Active Operator', 'essb'), esc_html__('Set to Yes if you wish this operator to be visible and available for chat.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_operator4_name', esc_html__('Name', 'essb'), esc_html__('Enter Name that will appear on screen', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_operator4_title', esc_html__('Title', 'essb'), esc_html__('Required to load Facebook API. To create one visit Facebook Developer Center.', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_operator4_number', esc_html__('Contact Number', 'essb'), esc_html__('Enter the contact number which will be used to contact with you. This number should contain the selected chat application installed.', 'essb'), '');
		$more_options = array ("" => "WhatsApp", "viber" => "Viber", "email" => "Email", "phone" => "Phone" );
		
		ESSBOptionsStructureHelper::field_select('chat', 'clicktochat', 'click2chat_operator4_app', esc_html__('Application', 'essb'), esc_html__('Choose the used for chat application for this operator', 'essb'), $more_options);
		ESSBOptionsStructureHelper::field_image('chat', 'clicktochat', 'click2chat_operator4_image', esc_html__('Profile Image', 'essb'), esc_html__('Image that will be displayed as profile picture', 'essb'), '', 'vertical1');
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'clicktochat', 'click2chat_operator4_text', esc_html__('Auto Text', 'essb'), esc_html__('This text will pre-poluate the chat field. You can use [title] and [url] to as variables to set the current page title or URL. (WhatsApp Only)', 'essb'), '');
		ESSBOptionsStructureHelper::panel_end('chat', 'clicktochat');

}

if (!essb_option_bool_value('deactivate_module_facebookchat')) {
	ESSBOptionsStructureHelper::menu_item('chat', 'facebookchat', esc_html__('Facebook Messenger Live Chat', 'essb'), 'ti-facebook');

	if (!ESSBActivationManager::isActivated()) {
		if (!ESSBActivationManager::isThemeIntegrated()) {
			ESSBOptionsStructureHelper::hint('chat', 'facebookchat', esc_html__('Activate Plugin To Use This Feature', 'essb'), 'Hello! Please <a href="admin.php?page=essb_redirect_update&tab=update">activate your copy</a> of Easy Social Share Buttons for WordPress to unlock and use this feature.', 'fa24 fa fa-lock', 'glow');
		}
		else {
			ESSBOptionsStructureHelper::hint('chat', 'facebookchat', esc_html__('Direct Customer Benefit ', 'essb'), sprintf(esc_html__('Access to one click ready made styles install is benefit for direct plugin customers. <a href="%s" target="_blank"><b>See all direct customer benefits</b></a>', 'essb'), ESSBActivationManager::getBenefitURL()), 'fa24 fa fa-lock', 'glow');
		}
	
	}
	else {
		ESSBOptionsStructureHelper::panel_start('chat', 'facebookchat', esc_html__('Enbable display of Facebook Messenger Live chat', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'fbmessenger_active', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
	
		ESSBOptionsStructureHelper::field_checkbox_list('chat', 'facebookchat', 'fbmessenger_posttypes', esc_html__('Appear only on selected post types', 'essb'), esc_html__('Select only if you do not wish the chat to appear on the entire site.', 'essb'), essb_get_post_types());
		ESSBOptionsStructureHelper::field_switch('chat', 'facebookchat', 'fbmessenger_deactivate_homepage', esc_html__('Deactivate display on homepage', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'facebookchat', 'fbmessenger_pageid', esc_html__('Facebook Page ID', 'essb'), esc_html__('Enter your Facebook Page ID to connect live page with it (live chat cannot connect to personal profiles). To use live chat you also need to whitelist your domain in the Page settings. To do this visit your Facebook Page Settings and add/remove whitelisted domains (you need to add a valid domain - example: https://socialsharingplugin.com). Live chat works only if your site uses SSL protocol and can run only on a real domain (no IP address or localhost enviroment).', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'facebookchat', 'fbmessenger_appid', esc_html__('Facebook Application ID', 'essb'), esc_html__('Required to load Facebook API. To create one visit Facebook Developer Center.', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'facebookchat', 'fbmessenger_exclude', esc_html__('Exclude display on', 'essb'), esc_html__('Exclude appearance on posts/pages with these IDs. Comma separated: "11, 15, 125".', 'essb'), '');
		ESSBOptionsStructureHelper::field_switch('chat', 'facebookchat', 'fbmessenger_minimized', esc_html__('Appear minimized', 'essb'), esc_html__('Set this option if you wish the chat to appear minimized', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_switch('chat', 'facebookchat', 'fbmessenger_left', esc_html__('Appear on the left', 'essb'), esc_html__('Change default appearance position to Left', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));

		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'facebookchat', 'fbmessenger_logged_greeting', esc_html__('Logged in users greeting', 'essb'), esc_html__('Optional. The greeting text that will be displayed if the user is currently logged in to Facebook. Maximum 80 characters.', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'facebookchat', 'fbmessenger_loggedout_greeting', esc_html__('Logged out users greeting', 'essb'), esc_html__('Optional. The greeting text that will be displayed if the user is currently not logged in to Facebook. Maximum 80 characters.', 'essb'));
		ESSBOptionsStructureHelper::field_color('chat', 'facebookchat', 'fbmessenger_color', esc_html__('Theme color', 'essb'), esc_html__('Optional. Set custom theme color for the chat.', 'essb'));
		$list_of_methods = array ("" => __('Default language (English US)', 'easy-facebook-comments'), "af_ZA" => "Afrikaans","ak_GH" => "Akan","am_ET" => "Amharic","ar_AR" => "Arabic","as_IN" => "Assamese","ay_BO" => "Aymara","az_AZ" => "Azerbaijani","be_BY" => "Belarusian","bg_BG" => "Bulgarian","bn_IN" => "Bengali","br_FR" => "Breton","bs_BA" => "Bosnian","ca_ES" => "Catalan","cb_IQ" => "Sorani Kurdish","ck_US" => "Cherokee","co_FR" => "Corsican","cs_CZ" => "Czech","cx_PH" => "Cebuano","cy_GB" => "Welsh","da_DK" => "Danish","de_DE" => "German","el_GR" => "Greek","en_GB" => "English (UK)","en_IN" => "English (India)","en_PI" => "English (Pirate)","en_UD" => "English (Upside Down)","en_US" => "English (US)","eo_EO" => "Esperanto","es_CL" => "Spanish (Chile)","es_CO" => "Spanish (Colombia)","es_ES" => "Spanish (Spain)","es_LA" => "Spanish","es_MX" => "Spanish (Mexico)","es_VE" => "Spanish (Venezuela)","et_EE" => "Estonian","eu_ES" => "Basque","fa_IR" => "Persian","fb_LT" => "Leet Speak","ff_NG" => "Fulah","fi_FI" => "Finnish","fo_FO" => "Faroese","fr_CA" => "French (Canada)","fr_FR" => "French (France)","fy_NL" => "Frisian","ga_IE" => "Irish","gl_ES" => "Galician","gn_PY" => "Guarani","gu_IN" => "Gujarati","gx_GR" => "Classical Greek","ha_NG" => "Hausa","he_IL" => "Hebrew","hi_IN" => "Hindi","hr_HR" => "Croatian","ht_HT" => "Haitian Creole","hu_HU" => "Hungarian","hy_AM" => "Armenian","id_ID" => "Indonesian","ig_NG" => "Igbo","is_IS" => "Icelandic","it_IT" => "Italian","ja_JP" => "Japanese","ja_KS" => "Japanese (Kansai)","jv_ID" => "Javanese","ka_GE" => "Georgian","kk_KZ" => "Kazakh","km_KH" => "Khmer","kn_IN" => "Kannada","ko_KR" => "Korean","ku_TR" => "Kurdish (Kurmanji)","ky_KG" => "Kyrgyz","la_VA" => "Latin","lg_UG" => "Ganda","li_NL" => "Limburgish","ln_CD" => "Lingala","lo_LA" => "Lao","lt_LT" => "Lithuanian","lv_LV" => "Latvian","mg_MG" => "Malagasy","mi_NZ" => "Māori","mk_MK" => "Macedonian","ml_IN" => "Malayalam","mn_MN" => "Mongolian","mr_IN" => "Marathi","ms_MY" => "Malay","mt_MT" => "Maltese","my_MM" => "Burmese","nb_NO" => "Norwegian (bokmal)","nd_ZW" => "Ndebele","ne_NP" => "Nepali","nl_BE" => "Dutch (België)","nl_NL" => "Dutch","nn_NO" => "Norwegian (nynorsk)","ny_MW" => "Chewa","or_IN" => "Oriya","pa_IN" => "Punjabi","pl_PL" => "Polish","ps_AF" => "Pashto","pt_BR" => "Portuguese (Brazil)","pt_PT" => "Portuguese (Portugal)","qc_GT" => "Quiché","qu_PE" => "Quechua","rm_CH" => "Romansh","ro_RO" => "Romanian","ru_RU" => "Russian","rw_RW" => "Kinyarwanda","sa_IN" => "Sanskrit","sc_IT" => "Sardinian","se_NO" => "Northern Sámi","si_LK" => "Sinhala","sk_SK" => "Slovak","sl_SI" => "Slovenian","sn_ZW" => "Shona","so_SO" => "Somali","sq_AL" => "Albanian","sr_RS" => "Serbian","sv_SE" => "Swedish","sw_KE" => "Swahili","sy_SY" => "Syriac","sz_PL" => "Silesian","ta_IN" => "Tamil","te_IN" => "Telugu","tg_TJ" => "Tajik","th_TH" => "Thai","tk_TM" => "Turkmen","tl_PH" => "Filipino","tl_ST" => "Klingon","tr_TR" => "Turkish","tt_RU" => "Tatar","tz_MA" => "Tamazight","uk_UA" => "Ukrainian","ur_PK" => "Urdu","uz_UZ" => "Uzbek","vi_VN" => "Vietnamese","wo_SN" => "Wolof","xh_ZA" => "Xhosa","yi_DE" => "Yiddish","yo_NG" => "Yoruba","zh_CN" => "Simplified Chinese (China)","zh_HK" => "Traditional Chinese (Hong Kong)","zh_TW" => "Traditional Chinese (Taiwan)","zu_ZA" => "Zulu","zz_TR" => "Zazaki");
		ESSBOptionsStructureHelper::field_select('chat', 'facebookchat', 'fbmessenger_language', esc_html__('Change Default Language', 'essb'), esc_html__('You can change the language of Facebook Messenger Live chat for your site. That includes the translation over the feature. You will still need to set up a greeting message on your language.', 'essb'), $list_of_methods);
		
		
		ESSBOptionsStructureHelper::panel_end('chat', 'facebookchat');
	}
}

if (!essb_option_bool_value('deactivate_module_skypechat')) {
	ESSBOptionsStructureHelper::menu_item('chat', 'skypechat', esc_html__('Skype Live Chat', 'essb'), 'ti-facebook');

	if (!ESSBActivationManager::isActivated()) {
		if (!ESSBActivationManager::isThemeIntegrated()) {
			ESSBOptionsStructureHelper::hint('chat', 'skypechat', esc_html__('Activate Plugin To Use This Feature', 'essb'), 'Hello! Please <a href="admin.php?page=essb_redirect_update&tab=update">activate your copy</a> of Easy Social Share Buttons for WordPress to unlock and use this feature.', 'fa24 fa fa-lock', 'glow');
		}
		else {
			ESSBOptionsStructureHelper::hint('chat', 'skypechat', esc_html__('Direct Customer Benefit ', 'essb'), sprintf(esc_html__('Access to one click ready made styles install is benefit for direct plugin customers. <a href="%s" target="_blank"><b>See all direct customer benefits</b></a>', 'essb'), ESSBActivationManager::getBenefitURL()), 'fa24 fa fa-lock', 'glow');
		}

	}
	else {
		ESSBOptionsStructureHelper::panel_start('chat', 'skypechat', esc_html__('Enable display of Skype Live Chat', 'essb'), esc_html__('Connect with your customers like never before', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'skype_active', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));

		ESSBOptionsStructureHelper::field_checkbox_list('chat', 'skypechat', 'skype_posttypes', esc_html__('Appear only on selected post types', 'essb'), esc_html__('Limit function to work only on selected post types. Leave non option selected to make it work on all', 'essb'), essb_get_post_types(), '', array('source' => 'post_types'));
		ESSBOptionsStructureHelper::field_switch('chat', 'skypechat', 'skype_deactivate_homepage', esc_html__('Deactivate display on homepage', 'essb'), esc_html__('Exclude display of function on home page of your site.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'skypechat', 'skype_user', esc_html__('Your Skype UserID', 'essb'), esc_html__('Enter your user ID to start a chat with your visitors.', 'essb'));
		ESSBOptionsStructureHelper::field_select('chat', 'skypechat', 'skype_type', esc_html__('Chat Button Style', 'essb'), esc_html__('Choose the initial chat style.', 'essb'), array('bubble' => 'Chat Bubble', 'rounded' => 'Rounded Button With Text'));
		
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'skypechat', 'skype_text', esc_html__('Custom chat button text', 'essb'), esc_html__('The custom chat button text will appear only if you select a rounded button style.', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('chat', 'skypechat', 'skype_exclude', esc_html__('Exclude display on', 'essb'), esc_html__('Exclude appearance on posts/pages with these IDs. Comma separated: "11, 15, 125".', 'essb'), '');

		ESSBOptionsStructureHelper::panel_end('chat', 'skypechat');
	}
}


if (!essb_option_bool_value('deactivate_module_natives')) {
	// native buttons
	if (essb_option_bool_value('native_active')) {
		ESSBOptionsStructureHelper::panel_start('natives', 'native-1', esc_html__('Activate usage of native like, follow and subscribe buttons', 'essb'), esc_html__('Native social buttons are great way to encourage more like, shares and follows as they are easy recognizable by users. Usage of them may affect site loading speed because they add additional calls and code to page load once they are initialized. Use them with caution.', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'native_active', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
		ESSBOptionsStructureHelper::field_section_start_full_panels('natives', 'native-1');
		ESSBOptionsStructureHelper::field_switch_panel('natives', 'native-1', 'otherbuttons_sameline', esc_html__('Display on same line', 'essb'), esc_html__('Activate this option to display native buttons on same line with the share buttons.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_switch_panel('natives', 'native-1', 'allow_native_mobile', esc_html__('Allow display of native buttons on mobile devices', 'essb'), esc_html__('The native buttons are set off by default on mobile devices because they may affect speed of mobile site version. If you wish to use them on mobile devices set this option to <b>Yes</b>.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_switch_panel('natives', 'native-1', 'allnative_counters', esc_html__('Activate native buttons counter', 'essb'), esc_html__('Activate this option to display counters for native buttons.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_section_end_full_panels('natives', 'native-1');
		ESSBOptionsStructureHelper::field_simplesort('natives', 'native-1', 'native_order', esc_html__('Drag and Drop change position of display', 'essb'), esc_html__('Change order of native button display', 'essb'), essb_default_native_buttons());
		
		ESSBOptionsStructureHelper::panel_start('natives', 'native-1', esc_html__('Facebook button', 'essb'), esc_html__('Include Facebook native button in your site', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle", "state" => "closed"));
		ESSBOptionsStructureHelper::field_section_start_full_panels('natives', 'native-1');
		ESSBOptionsStructureHelper::field_switch_panel('natives', 'native-1', 'facebook_like_button', esc_html__('Include Facebook Like/Follow Button', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_switch_panel('natives', 'native-1', 'facebook_like_button_share', esc_html__('Include also Facebook Share Button', 'essb'), esc_html__('Since latest Facebook API changes like button makes only Like action. If you wish to allow users share we can recommend to activate this option too.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_switch_panel('natives', 'native-1', 'facebook_like_button_api', esc_html__('My site already uses Facebook Api', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_switch_panel('natives', 'native-1', 'facebook_like_button_api_async', esc_html__('Load Facebook API asynchronous', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_panel('natives', 'native-1', 'facebook_like_button_width', esc_html__('Set custom width of Facebook like button to fix problem with not rendering correct. Value must be number without px in it.', 'essb'), '');
		ESSBOptionsStructureHelper::field_textbox_panel('natives', 'native-1', 'facebook_like_button_height', esc_html__('Set custom height of Facebook like button to fix problem with not rendering correct. Value must be number without px in it.', 'essb'), '');
		ESSBOptionsStructureHelper::field_textbox_panel('natives', 'native-1', 'facebook_like_button_margin_top', esc_html__('Set custom margin-top (to move up use negative value) of Facebook like button to fix problem with not rendering correct. Value must be number without px in it.', 'essb'), '');
		ESSBOptionsStructureHelper::field_textbox_panel('natives', 'native-1', 'facebook_like_button_lang', esc_html__('Custom language code for you native Facebook button', 'essb'), esc_html__('If you wish to change your native Facebook button language code from English you need to enter here your own code like es_ES. Full list of code can be found here: <a href="https://www.facebook.com/translations/FacebookLocales.xml" target="_blank">https://www.facebook.com/translations/FacebookLocales.xml</a>', 'essb'));
		ESSBOptionsStructureHelper::field_section_end_full_panels('natives', 'native-1');
		$listOfOptions = array ("like" => "Like page", "follow" => "Profile follow" );
		
		ESSBOptionsStructureHelper::field_select('natives', 'native-1', 'facebook_like_type', esc_html__('Button type', 'essb'), esc_html__('Choose button type you wish to use.', 'essb'), $listOfOptions);
		ESSBOptionsStructureHelper::field_textbox_stretched('natives', 'native-1', 'facebook_follow_profile', esc_html__('Facebook Follow Profile Page URL', 'essb'), '');
		ESSBOptionsStructureHelper::field_textbox_stretched('natives', 'native-1', 'custom_url_like_address', esc_html__('Custom Facebook like button address', 'essb'), esc_html__('Provide custom address in case you wish likes to be added to that page - example fan page. Otherwise likes will be counted to page where button is displayed.', 'essb'));
		ESSBOptionsStructureHelper::panel_end('natives', 'native-1');
				
		ESSBOptionsStructureHelper::panel_start('natives', 'native-1', esc_html__('Twitter button', 'essb'), esc_html__('Include Twitter native button in your site', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle", "state" => "closed"));
		ESSBOptionsStructureHelper::field_switch('natives', 'native-1', 'twitterfollow', esc_html__('Twitter Tweet/Follow Button', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		$listOfOptions = array ("follow" => "Follow user", "tweet" => "Tweet" );
		ESSBOptionsStructureHelper::field_select('natives', 'native-1', 'twitter_tweet', esc_html__('Button type', 'essb'), esc_html__('Choose button type you wish to use.', 'essb'), $listOfOptions);
		ESSBOptionsStructureHelper::field_textbox_stretched('natives', 'native-1', 'twitterfollowuser', esc_html__('Twitter Follow User', 'essb'), '');
		ESSBOptionsStructureHelper::panel_end('natives', 'native-1');
		
		ESSBOptionsStructureHelper::panel_start('natives', 'native-1', esc_html__('YouTube button', 'essb'), esc_html__('Include YouTube native button in your site', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle", "state" => "closed"));
		ESSBOptionsStructureHelper::field_switch('natives', 'native-1', 'youtubesub', esc_html__('YouTube channel subscribe button', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('natives', 'native-1', 'youtubechannel', esc_html__('Channel ID', 'essb'), '');
		ESSBOptionsStructureHelper::panel_end('natives', 'native-1');
		
		ESSBOptionsStructureHelper::panel_start('natives', 'native-1', esc_html__('Pinterest button', 'essb'), esc_html__('Include Pinterest native button in your site', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle", "state" => "closed"));
		ESSBOptionsStructureHelper::field_switch('natives', 'native-1', 'pinterestfollow', esc_html__('Include Pinterest Pin/Follow Button', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		$listOfOptions = array ("follow" => "Profile follow", "pin" => "Pin button" );
		ESSBOptionsStructureHelper::field_select('natives', 'native-1', 'pinterest_native_type', esc_html__('Button type', 'essb'), esc_html__('Choose button type you wish to use.', 'essb'), $listOfOptions);
		ESSBOptionsStructureHelper::field_textbox_stretched('natives', 'native-1', 'pinterestfollow_disp', esc_html__('Text on button when follow type is selected', 'essb'), '');
		ESSBOptionsStructureHelper::field_textbox_stretched('natives', 'native-1', 'pinterestfollow_url', esc_html__('Profile url when follow type is selected', 'essb'), esc_html__('Provide your Pinterest URL as it is seen at the browser, for example https://www.pinterest.com/appscreo.', 'essb'));
		ESSBOptionsStructureHelper::panel_end('natives', 'native-1');
		
		ESSBOptionsStructureHelper::panel_start('natives', 'native-1', esc_html__('LinkedIn button', 'essb'), esc_html__('Include LinkedIn native button in your site', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle", "state" => "closed"));
		ESSBOptionsStructureHelper::field_switch('natives', 'native-1', 'linkedin_follow', esc_html__('Include LinkedIn button', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('natives', 'native-1', 'linkedin_follow_id', esc_html__('Company ID', 'essb'), '');
		ESSBOptionsStructureHelper::panel_end('natives', 'native-1');
		
		ESSBOptionsStructureHelper::panel_start('natives', 'native-1', esc_html__('ManagedWP button', 'essb'), esc_html__('Include ManagedWP native button in your site', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle", "state" => "closed"));
		ESSBOptionsStructureHelper::field_switch('natives', 'native-1', 'managedwp_button', esc_html__('Include ManagedWP.org Upvote Button', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::panel_end('natives', 'native-1');
		
		ESSBOptionsStructureHelper::panel_start('natives', 'native-1', esc_html__('VKontankte (vk.com) button', 'essb'), esc_html__('Include VKontankte (vk.com) native button in your site', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle", "state" => "closed"));
		ESSBOptionsStructureHelper::field_switch('natives', 'native-1', 'vklike', esc_html__('Include VK.com Like Button', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('natives', 'native-1', 'vklikeappid', esc_html__('VKontakte (vk.com) Application ID', 'essb'), esc_html__('If you don\'t have application id for your site you need to generate one on VKontakte (vk.com) Dev Site. To do this visit this page http://vk.com/dev.php?method=Like and follow instructions on page', 'essb'));
		ESSBOptionsStructureHelper::panel_end('natives', 'native-1');
		
		ESSBOptionsStructureHelper::panel_end('natives', 'native-1');
		
		ESSBOptionsStructureHelper::panel_start('natives', 'native-2', esc_html__('Activate usage of skinned native buttons', 'essb'), esc_html__('This option will hide
				native buttons inside nice flat style boxes and show them on
				hover.', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'skin_native', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
		
		$skin_list = array ("flat" => "Flat", "metro" => "Metro" );
		ESSBOptionsStructureHelper::field_select('natives', 'native-2', 'skin_native_skin', esc_html__('Native buttons skin', 'essb'), esc_html__('Choose skin for native buttons. It will be applied only when option above is activated.', 'essb'), $skin_list);
		
		foreach (essb_default_native_buttons() as $network) {
			ESSBOptionsStructureHelper::panel_start('natives', 'native-2', ESSBOptionsStructureHelper::capitalize($network), esc_html__('Skinned settings for that social network', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle", "state" => "closed"));
		
			ESSBOptionsStructureHelper::field_color('natives', 'native-2', 'skinned_'.$network.'_color', esc_html__('Skinned button color replace', 'essb'), '');
			ESSBOptionsStructureHelper::field_color('natives', 'native-2', 'skinned_'.$network.'_hovercolor', esc_html__('Skinned button hover color replace', 'essb'), '');
			ESSBOptionsStructureHelper::field_color('natives', 'native-2', 'skinned_'.$network.'_textcolor', esc_html__('Skinned button text color replace', 'essb'), '');
			ESSBOptionsStructureHelper::field_textbox_stretched('natives', 'native-2', 'skinned_'.$network.'_text', esc_html__('Skinned button text replace', 'essb'), '');
			ESSBOptionsStructureHelper::field_textbox('natives', 'native-2', 'skinned_'.$network.'_width', esc_html__('Skinned button width replace', 'essb'), '', '', 'input60', 'fa-arrows-h', 'right');
			ESSBOptionsStructureHelper::panel_end('natives', 'native-2');
		
		}
		ESSBOptionsStructureHelper::panel_end('natives', 'native-2');
		
		ESSBOptionsStructureHelper::panel_start('natives', 'native-3', esc_html__('Activate social privacy native buttons', 'essb'), esc_html__('When used in social privacy mode native buttons will not load until user click and request their activation. Usage in this mode is great way to avoid delay of load when you use natives and also this is the only way to use them in countries where native buttons should be used in two-click mode', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'native_privacy_active', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
		foreach (essb_default_native_buttons() as $network) {
			ESSBOptionsStructureHelper::panel_start('natives', 'native-3', ESSBOptionsStructureHelper::capitalize($network), esc_html__('Privacy settings for that social network', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle", "state" => "closed"));
			ESSBOptionsStructureHelper::field_textbox_stretched('natives', 'native-3', 'skinned_'.$network.'_privacy_text', esc_html__('Privacy button text replace', 'essb'), '');
			ESSBOptionsStructureHelper::field_textbox('natives', 'native-3', 'skinned_'.$network.'_privacy_width', esc_html__('Privacy button width replace', 'essb'), '', '', 'input60', 'fa-arrows-h', 'right');
			ESSBOptionsStructureHelper::panel_end('natives', 'native-3');
		}
		ESSBOptionsStructureHelper::panel_end('natives', 'native-3');
	}
	else {
		ESSBOptionsStructureHelper::field_component('natives', 'native-1', 'essb5_advanced_natives_activate_options', 'false');
	}
}

// Followers Counter
if (!essb_option_bool_value('deactivate_module_followers')) {
	ESSBOptionsStructureHelper::help('follow', 'follow-1', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/social-followers-counter-general-setup/', 'Adding Followers\' Counter on Your Site' => 'https://docs.socialsharingplugin.com/knowledgebase/adding-social-followers-counter-on-your-site/'));
	
	if (essb_option_bool_value('fanscounter_active')) {		
		ESSBOptionsStructureHelper::panel_start('follow', 'follow-1', esc_html__('Enable Social Followers Counter', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'fanscounter_active', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
		ESSBOptionsStructureHelper::field_switch('follow', 'follow-1', 'fanscounter_widget_deactivate', esc_html__('I will not use Followers Counter widget', 'essb'), esc_html__('Remove the plugin widget for followers counter from the code and list of widgets.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_select('follow', 'follow-1', 'essb3fans_update', esc_html__('Data update period', 'essb'), esc_html__('The default is 1 day if nothing is selected.', 'essb'), ESSBSocialFollowersCounterHelper::available_cache_periods());
		ESSBOptionsStructureHelper::field_select('follow', 'follow-1', 'essb3fans_format', esc_html__('Number format', 'essb'), '', ESSBSocialFollowersCounterHelper::available_number_formats());
		ESSBOptionsStructureHelper::field_switch('follow', 'follow-1', 'fanscounter_clear_on_save', esc_html__('Clear stored values on settings update', 'essb'), esc_html__('Remove all stored values for the number of followers and do a fresh update.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		
		ESSBOptionsStructureHelper::field_heading('follow', 'follow-1', 'heading5', esc_html__('Social Networks', 'essb'));
		ESSBOptionsStructureHelper::field_checkbox_list_sortable('follow', 'follow-1', 'essb3fans_networks', esc_html__('Social Networks', 'essb'), esc_html__('Order and activate networks you wish to use in widget and shortcodes'), ESSBSocialFollowersCounterHelper::available_social_networks(false));
		
		ESSBOptionsStructureHelper::panel_end('follow', 'follow-1');
		
		ESSBOptionsStructureHelper::help('follow', 'follow-2', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/social-followers-counter-general-setup/#Additional_Network_Settings'));		
		essb3_draw_fanscounter_settings('follow', 'follow-2');
		
		ESSBOptionsStructureHelper::help('follow', 'follow-3', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/adding-social-followers-counter-as-a-floating-sidebar/'));		
		ESSBOptionsStructureHelper::panel_start('follow', 'follow-3', esc_html__('Enable display of social follower counters as an automatic floating sidebar', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'fanscounter_sidebar', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
		$defaults = ESSBSocialFollowersCounterHelper::default_instance_settings();
		$followers_default_options = ESSBSocialFollowersCounterHelper::default_options_structure(true, $defaults);
		ESSBOptionsStructureHelper::field_select('follow', 'follow-3', 'essb3fans_sidebar_template', esc_html__('Template', 'essb'), esc_html__('Choose template that you will use on followers sidebar', 'essb'), $followers_default_options['template']['values']);
		ESSBOptionsStructureHelper::field_select('follow', 'follow-3', 'essb3fans_sidebar_animation', esc_html__('Apply animation', 'essb'), esc_html__('Animation is a great way to grab visitors attention', 'essb'), $followers_default_options['animation']['values']);
		ESSBOptionsStructureHelper::field_switch('follow', 'follow-3', 'essb3fans_sidebar_nospace', esc_html__('Without space between buttons', 'essb'), esc_html__('Activate this option to connect follower buttons and remove tiny space between them.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_select('follow', 'follow-3', 'essb3fans_sidebar_orientation', esc_html__('Choose button layout style', 'essb'), esc_html__('Buttons can be horizontal or vertical. Choose the style that fits best into selected networks and site', 'essb'), array("h" => esc_html__("Horizontal", "essb"), "v" => esc_html__("Vertical", "essb")));
		ESSBOptionsStructureHelper::field_select('follow', 'follow-3', 'essb3fans_sidebar_position', esc_html__('Choose position on screen', 'essb'), esc_html__('Choose position where you wish to appear sidebar display', 'essb'), array("left" => esc_html__("Left", "essb"), "right" => esc_html__("Right", "essb")));
		ESSBOptionsStructureHelper::field_textbox('follow', 'follow-3', 'essb3fans_sidebar_width', esc_html__('Customize width of button', 'essb'), esc_html__('We choose default optimal width that fits in almost all sites. In some cases based on text you may need to shrink or extend button. Use this field to enter custom width (example: 100)', 'essb'), '', 'input60', 'fa-arrows-h', 'right');

		ESSBOptionsStructureHelper::field_switch('follow', 'follow-3', 'essb3fans_sidebar_total', esc_html__('Display the total followers', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		
		ESSBOptionsStructureHelper::panel_end('follow', 'follow-3');
	
		// Custom Layout Builder
		ESSBOptionsStructureHelper::help('follow', 'follow-4', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/creating-a-custom-layout-for-social-followers-counter/'));		
		ESSBOptionsStructureHelper::panel_start('follow', 'follow-4', esc_html__('Create Custom Followers Counter Layout', 'essb'), esc_html__('Activate this option if you wish to build a custom layout of the followers counter. You will be able to choose a separate columns style for each network and include an eye catching cover box.', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'fanscounter_layout', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
	
		ESSBOptionsStructureHelper::panel_start('follow', 'follow-4', esc_html__('Header Cover Box', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
		ESSBOptionsStructureHelper::field_switch_panel('follow', 'follow-4', 'essb3fans_coverbox_show', esc_html__('Display cover box above networks', 'essb'), esc_html__('Set Yes to display the configured cover box.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_select_panel('follow', 'follow-4', 'essb3fans_coverbox_style', esc_html__('Main style', 'essb'), esc_html__('Main style choose the accent color that will be used to draw texts', 'essb'), array('' => 'Light', 'dark' => 'Dark'));
		ESSBOptionsStructureHelper::field_color_panel('follow', 'follow-4', 'essb3fans_coverbox_bg', esc_html__('Custom background color', 'essb'), esc_html__('Setup custom background color that will appear in the cover box.', 'essb'), '', 'true');
	
		ESSBOptionsStructureHelper::field_image('follow', 'follow-4', 'essb3fans_coverbox_profile', esc_html__('Profile image', 'essb'), esc_html__('Optional you can set a custom profile image that will appear', 'essb'), '', 'vertical1');
		ESSBOptionsStructureHelper::field_textbox_stretched('follow', 'follow-4', 'essb3fans_coverbox_title', esc_html__('Title', 'essb'), esc_html__('Set own personalized title (shortcodes supported). Use [easy-total-followers] if you wish to display total number of followers in text', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched('follow', 'follow-4', 'essb3fans_coverbox_desc', esc_html__('Description text', 'essb'), esc_html__('Appearing in smaller text below title (shortcodes supported). Use [easy-total-followers] if you wish to display total number of followers in text', 'essb'));
		
		ESSBOptionsStructureHelper::panel_end('follow', 'follow-4');
		
		//
		ESSBOptionsStructureHelper::field_select('follow', 'follow-4', 'essb3fans_layout_cols', esc_html__('Columns', 'essb'), esc_html__('Choose the number of columns that will be used for custom layout.', 'essb'), array('2' => '2 Columns', '3' => '3 Columns', '4' => '4 Columns', '5' => '5 Columns', '6' => '6 Columns'));
		ESSBOptionsStructureHelper::field_select('follow', 'follow-4', 'essb3fans_layout_total', esc_html__('Display total number of followers', 'essb'), esc_html__('Change option if you wish to make the total value appear.', 'essb'), array('' => 'Do not display total number block', 'top' => 'Block above networks', 'bottom' => 'Block below networks'));
		ESSBOptionsStructureHelper::hint('follow', 'follow-4', '', esc_html__('The custom layout will display social networks that you activate from global plugin settings along with their order. In the block size setup you will see netowrks appearing in the deafult order from settings.', 'essb'), 'fa21 ti-ruler-pencil', 'glow');
		
		$network_list = ESSBSocialFollowersCounterHelper::available_social_networks(false);
		foreach ($network_list as $key => $network) {
			ESSBOptionsStructureHelper::field_select_panel('follow', 'follow-4', 'essb3fans_layout_cols_'.$key, $network, esc_html__('Width of block for this social network inside layout or leave default for a single size.', 'essb'), array('' => 'Default column size from layout', '2' => '2 Columns', '3' => '3 Columns', '4' => '4 Columns', '5' => '5 Columns', '6' => '6 Columns'));
		}
		
		ESSBOptionsStructureHelper::panel_end('follow', 'follow-4');
		
		// Follow me bar
		ESSBOptionsStructureHelper::help('follow', 'follow-5', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/social-followers-showing-as-an-automatic-content-bar/'));		
		ESSBOptionsStructureHelper::panel_start('follow', 'follow-5', esc_html__('Enable display of social followers counters below content (Profile Links with Followers Counter)', 'essb'), esc_html__('The option will add automatically the profile bar you configure below the content of Posts only. You are able to add the bar manually anywhere inside code using the shortcode [followme-bar]. The shortcode will work no matter if the automatic showing bar is active or not (but you need at least to activate for the basic settings configured).', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'fanscounter_postbar', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
		ESSBOptionsStructureHelper::panel_start('follow', 'follow-5', esc_html__('Custom Cover Box Content Above Buttons', 'essb'), esc_html__('Use the fields if you need to fill custom content above your follow buttons. The fields you can use to make the buttons be like a profile button for example.', 'essb'), '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
		ESSBOptionsStructureHelper::field_switch_panel('follow', 'follow-5', 'essb3fans_profile_c_show', esc_html__('Display cover box above networks', 'essb'), esc_html__('Set Yes to display the configured cover box.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_select_panel('follow', 'follow-5', 'essb3fans_profile_c_style', esc_html__('Main style', 'essb'), esc_html__('Main style choose the accent color that will be used to draw texts', 'essb'), array('' => 'Light', 'dark' => 'Dark'));
		ESSBOptionsStructureHelper::field_select_panel('follow', 'follow-5', 'essb3fans_profile_c_align', esc_html__('Align', 'essb'), esc_html__('Choose text and image alignment inside the cover box', 'essb'), array('' => 'Center (default)', 'left' => 'Left', 'right' => 'Right'));
		ESSBOptionsStructureHelper::field_color_panel('follow', 'follow-5', 'essb3fans_profile_c_bg', esc_html__('Custom background color', 'essb'), esc_html__('Setup custom background color that will appear in the cover box.', 'essb'), '', 'true');
		ESSBOptionsStructureHelper::field_image('follow', 'follow-5', 'essb3fans_profile_c_profile', esc_html__('Profile image', 'essb'), esc_html__('Optional you can set a custom profile image that will appear', 'essb'), '', 'vertical1');
		ESSBOptionsStructureHelper::field_textbox_stretched('follow', 'follow-5', 'essb3fans_profile_c_title', esc_html__('Title', 'essb'), esc_html__('Set own personalized title (shortcodes supported). Use [easy-total-followers] if you wish to display total number of followers in text', 'essb'));
		ESSBOptionsStructureHelper::field_textarea('follow', 'follow-5', 'essb3fans_profile_c_desc', esc_html__('Description text', 'essb'), esc_html__('Appearing in smaller text below title (shortcodes supported). Use [easy-total-followers] if you wish to display total number of followers in text', 'essb'));
		ESSBOptionsStructureHelper::panel_end('follow', 'follow-5');
		
		ESSBOptionsStructureHelper::field_select_panel('follow', 'follow-5', 'essb3fans_profile_cols', esc_html__('Columns', 'essb'), esc_html__('Choose the number of columns that will be used for custom layout.', 'essb'), array('' => 'Automatic', '2' => '2 Columns', '3' => '3 Columns', '4' => '4 Columns', '5' => '5 Columns', '6' => '6 Columns'));
		ESSBOptionsStructureHelper::field_select_panel('follow', 'follow-5', 'essb3fans_profile_template', esc_html__('Template', 'essb'), esc_html__('Choose template that you will use on followers sidebar', 'essb'), $followers_default_options['template']['values']);
		ESSBOptionsStructureHelper::field_select_panel('follow', 'follow-5', 'essb3fans_profile_animation', esc_html__('Apply animation', 'essb'), esc_html__('Animation is a great way to grab visitors attention', 'essb'), $followers_default_options['animation']['values']);
		ESSBOptionsStructureHelper::field_switch_panel('follow', 'follow-5', 'essb3fans_profile_nospace', esc_html__('Without space between buttons', 'essb'), esc_html__('Activate this option to connect follower buttons and remove tiny space between them.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_switch_panel('follow', 'follow-5', 'essb3fans_profile_notext', esc_html__('Without Follow Text', 'essb'), esc_html__('Set to Yes in case you need to remove the followers text below numbers.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_switch_panel('follow', 'follow-5', 'essb3fans_profile_nonumber', esc_html__('Without Follow Values', 'essb'), esc_html__('Set to Yes if you need to remove the number of followers (for example use it just as profile buttons).', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		
		ESSBOptionsStructureHelper::panel_end('follow', 'follow-5');
	}
	else {
		ESSBOptionsStructureHelper::field_component('follow', 'follow-1', 'essb5_advanced_followers_activate_options', 'false');
	}
}

// Profiles
if (!essb_option_bool_value('deactivate_module_profiles')) {
	ESSBOptionsStructureHelper::help('profiles', 'profiles-1', '', '', array('Help With Setup of Social Profiles' => 'https://docs.socialsharingplugin.com/knowledgebase/social-profile-links-general-setup/', 'Adding Social Profile Links' => 'https://docs.socialsharingplugin.com/knowledgebase/how-to-add-social-profile-links-on-your-site/'));
	
	if (essb_option_bool_value('profiles_widget')) {
		ESSBOptionsStructureHelper::field_switch('profiles', 'profiles-1', 'profiles_widget', esc_html__('Enable social profiles widget and shortcode', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
			
		ESSBOptionsStructureHelper::field_checkbox_list_sortable('profiles', 'profiles-1', 'profile_networks', esc_html__('Social profile networks', 'essb'), esc_html__('Enable and order the global social profiles you will use on your site. Later you can automatically use them in shortcodes, widgets or automated displays. There are also shortcode & widgets that you can use to add other profiles (not listed in the settings).', 'essb'), ESSBSocialProfilesHelper::available_social_networks());
		
		ESSBOptionsStructureHelper::help('profiles', 'profiles-2', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/social-profile-links-general-setup/'));
		essb_prepare_social_profiles_fields('profiles', 'profiles-2');
		
		ESSBOptionsStructureHelper::help('profiles', 'profiles-3', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/how-to-add-social-profile-links-on-your-site/#Adding_Profile_Buttons_as_Floating_Sidebar'));
		
		ESSBOptionsStructureHelper::panel_start('profiles', 'profiles-3', esc_html__('Enable social profile links as floating sidebar', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'profiles_display', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
		$listOfOptions = array("left" => esc_html__("Left", "essb"), "right" => esc_html__("Right", "essb"), "topleft" => esc_html__("Top left", "essb"), "topright" => esc_html__("Top right", "essb"), "bottomleft" => esc_html__("Bottom left", "essb"), "bottomright" => esc_html__("Bottom right", "essb"));
		ESSBOptionsStructureHelper::field_select('profiles', 'profiles-3', 'profiles_display_position', esc_html__('Floating sidebar position', 'essb'), '', $listOfOptions);
		ESSBOptionsStructureHelper::field_switch('profiles', 'profiles-3', 'profiles_mobile_deactivate', esc_html__('Do not show on mobile devices', 'essb'), '', 'recommended', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_switch('profiles', 'profiles-3', 'profiles_nospace', esc_html__('Remove spacing between buttons', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_select('profiles', 'profiles-3', 'profiles_template', esc_html__('Buttons template', 'essb'), '', ESSBSocialProfilesHelper::available_templates());
		ESSBOptionsStructureHelper::field_select('profiles', 'profiles-3', 'profiles_size', esc_html__('Button size', 'essb'), '', ESSBSocialProfilesHelper::available_sizes());
		ESSBOptionsStructureHelper::field_select('profiles', 'profiles-3', 'profiles_animation', esc_html__('Animation', 'essb'), '', ESSBSocialProfilesHelper::available_animations());
		ESSBOptionsStructureHelper::panel_end('profiles', 'profiles-3');
		
		ESSBOptionsStructureHelper::help('profiles', 'profiles-4', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/how-to-add-social-profile-links-on-your-site/#Adding_Profile_Buttons_Automatically_Below_Content_of_Posts'));
		ESSBOptionsStructureHelper::panel_start('profiles', 'profiles-4', esc_html__('Enable display of social profile links below post content', 'essb'), esc_html__('The option will add automatically the profile bar you configure below the content of Posts only. You are able to add the bar manually anywhere inside code using the shortcode [profile-bar]. The shortcode will work no matter if the automatic showing bar is active or not (but you need at least to activate for the basic settings configured).', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'profiles_post_display', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
		$listOfOptions = array("left" => esc_html__("Left", "essb"), "right" => esc_html__("Right", "essb"), "center" => esc_html__("Center", "essb"));
		ESSBOptionsStructureHelper::field_select_panel('profiles', 'profiles-4', 'profiles_post_align', esc_html__('Align content and profile buttons', 'essb'), esc_html__('Choose how the profile buttons and custom content (if used) will be aligned.', 'essb'), $listOfOptions);
		$listOfOptions = array("above" => esc_html__("Above Profile Buttons", "essb"), "left" => esc_html__("Along With Profile Buttons (on the same line)", "essb"));
		ESSBOptionsStructureHelper::field_select_panel('profiles', 'profiles-4', 'profiles_post_content_pos', esc_html__('Custom content position', 'essb'), esc_html__('Choose where the custom content will appear.', 'essb'), $listOfOptions);
		ESSBOptionsStructureHelper::field_wpeditor('profiles', 'profiles-4', 'profiles_post_content', esc_html__('Custom content', 'essb'), esc_html__('Set custom content appearing above profile buttons. If you does not wish such content simply leave it blank', 'essb'), 'htmlmixed');
		
		$listOfOptions = array("" => esc_html__("Default", "essb"), "full" => esc_html__("Fluid Full Width", "essb"));
		ESSBOptionsStructureHelper::field_select_panel('profiles', 'profiles-4', 'profiles_post_width', esc_html__('Profile buttons width', 'essb'), esc_html__('The fluid full width is not recommended if you are using a large amount of share buttons. There may not be enough space to show all buttons at same time.', 'essb'), $listOfOptions);
		ESSBOptionsStructureHelper::field_switch_panel('profiles', 'profiles-4', 'profiles_post_show_text', esc_html__('Show CTA texts', 'essb'), esc_html__('Set to Yes to make the buttons has icon and text filled inside settings', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_switch_panel('profiles', 'profiles-4', 'profiles_post_nospace', esc_html__('Remove spacing between buttons', 'essb'), esc_html__('Activate this option to remove default space between share buttons.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_select_panel('profiles', 'profiles-4', 'profiles_post_template', esc_html__('Choose template that you will use for sidebar', 'essb'), esc_html__('Template assigned here will be used for sidebar and also for default template for widget and shortcodes if you use such. Each widget or shortcode includes options to personalize it.', 'essb'), ESSBSocialProfilesHelper::available_templates());
		ESSBOptionsStructureHelper::field_select_panel('profiles', 'profiles-4', 'profiles_post_animation', esc_html__('Choose animation that you will use for sidebar', 'essb'), esc_html__('Animation assigned here will be used for sidebar and also for default template for widget and shortcodes if you use such. Each widget or shortcode includes options to personalize it.', 'essb'), ESSBSocialProfilesHelper::available_animations());
		$listOfOptions = array("" => esc_html__("Default", "essb"), "small" => esc_html__("Small", "essb"), "medium" => esc_html__("Medium", "essb"), "large" => esc_html__("Large", "essb"), "xlarge" => esc_html__("Extra Large", "essb"));
		ESSBOptionsStructureHelper::field_select_panel('profiles', 'profiles-4', 'profiles_post_size', esc_html__('Profile buttons size', 'essb'), '', $listOfOptions);
		ESSBOptionsStructureHelper::panel_end('profiles', 'profiles-4');
	}
	else {		
		ESSBOptionsStructureHelper::field_component('profiles', 'profiles-1', 'essb5_advanced_profiles_activate_options', 'false');
	}
}

function essb3_draw_fanscounter_settings($tab_id, $menu_id) {
	$setting_fields = ESSBSocialFollowersCounterHelper::options_structure();
	$network_list = ESSBSocialFollowersCounterHelper::available_social_networks();
	
	/**
	 * Load the SVG icons if not present
	 */
	if (!class_exists('ESSB_SVG_Icons')) {
	    include_once (ESSB3_CLASS_PATH . 'assets/class-svg-icons.php');
	}
	
	$networks_same_authentication = array();

	foreach ($network_list as $network => $title) {
	    
	    if (ESSBSocialFollowersCounterHelper::is_deprecated_network($network)) {
	        continue;
	    }	    
	    
		ESSBOptionsStructureHelper::holder_start($tab_id, $menu_id, 'essb-followers-panel essb-followers-'.$network, 'essb-followers-'.$network);
		ESSBOptionsStructureHelper::panel_start($tab_id, $menu_id, $title, '', 'fa21 essbfc-icon essbfc-icon-'.$network, array("mode" => "toggle", "state" => "closed", 'svg_icon' => ESSB_SVG_Icons::get_icon($network == 'total' ? 'total_followers' : $network)));

		$default_options_key = $network;
		$is_extended_key = false;

		if (strpos($default_options_key, '_') !== false && $default_options_key != 'wp_posts' && $default_options_key != 'wp_comments' && $default_options_key != 'wp_users' && $default_options_key != 'subscribe_form') {
			$key_array = explode('_', $default_options_key);
			$default_options_key = $key_array[0];
			$is_extended_key = true;
		}

		$single_network_options = isset($setting_fields[$default_options_key]) ? $setting_fields[$default_options_key] : array();
		
		foreach ($single_network_options as $field => $options) {
			$field_id = "essb3fans_".$network."_".$field;

			$field_type = isset($options['type']) ? $options['type'] : 'textbox';
			$field_text = isset($options['text']) ? $options['text'] : '';
			$field_description = isset($options['description']) ? $options['description'] : '';
			$field_values = isset($options['values']) ? $options['values'] : array();

			$is_authfield = isset($options['authfield']) ? $options['authfield'] : false;

			if ($is_extended_key && $is_authfield) {
				if (isset($networks_same_authentication[$default_options_key])) {
					continue;
				}
			}

			if ($field_type == "textbox") {
				ESSBOptionsStructureHelper::field_textbox_stretched($tab_id, $menu_id, $field_id, $field_text, $field_description);
			}
			if ($field_type == "select") {
				ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $field_id, $field_text, $field_description, $field_values);
			}
		}

		ESSBOptionsStructureHelper::panel_end($tab_id, $menu_id);
		ESSBOptionsStructureHelper::holder_end($tab_id, $menu_id);
	}
}

function essb_prepare_social_profiles_fields($tab_id, $menu_id) {
    /**
     * Load the SVG icons if not present
     */
    if (!class_exists('ESSB_SVG_Icons')) {
        include_once (ESSB3_CLASS_PATH . 'assets/class-svg-icons.php');
    }
    
	foreach (essb_available_social_profiles() as $key => $text) {
	    
	    $display_key = $key;
	    
	    if (ESSBSocialFollowersCounterHelper::is_deprecated_network($key)) {
	        continue;
	    }
	    
	    if ($display_key == 'instgram') {
	        $display_key = 'instagram';
	    }
	    	    
	    
	    ESSBOptionsStructureHelper::holder_start($tab_id, $menu_id, 'essb-profiles-panel essb-profiles-'.$display_key, 'essb-profiles-'.$display_key);
	    ESSBOptionsStructureHelper::panel_start($tab_id, $menu_id, $text, '', 'fa21 essbfc-icon essbfc-icon-'.$display_key, array("mode" => "toggle", "state" => "closed", 'svg_icon' => ESSB_SVG_Icons::get_icon($display_key)));

		if ($key == 'subscribe_form') {
		    ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, 'profile_'.$key, esc_html__('Form design', 'essb'), '', essb_optin_designs());		    
		}
		else {
            ESSBOptionsStructureHelper::field_textbox_stretched($tab_id, $menu_id, 'profile_'.$key, esc_html__('Full address to profile', 'essb'), esc_html__('Enter address to your profile in social network', 'essb'));
		}
		ESSBOptionsStructureHelper::field_textbox_stretched($tab_id, $menu_id, 'profile_text_'.$key, esc_html__('Display text with icon', 'essb'), esc_html__('Enter custom text that will be displayed with link to your social profile. Example: Follow us on '.$text, 'essb'));
		ESSBOptionsStructureHelper::panel_end($tab_id, $menu_id);
		
		ESSBOptionsStructureHelper::holder_end($tab_id, $menu_id);
	}
}

function essb5_advanced_followers_activate_options() {
	echo essb5_generate_code_advanced_activate_panel(esc_html__('Enable Social Followers Counter', 'essb'),
			esc_html__('Automatically show the number of followers for 30+ social networks with shortcode, widget, floating sidebar, content bar, page builder elements, etc. The correct display of followers requires activation of required access token and keys to receive the values.', 'essb'),
			'fanscounter_active', '', esc_html__('Enable', 'essb'), 'fa fa-check', 'ti-heart ao-lightblue-icon');
}

function essb5_advanced_profiles_activate_options() {
	echo essb5_generate_code_advanced_activate_panel(esc_html__('Enable Social Profile Links', 'essb'),
			esc_html__('Add a link to your social profiles with shortcode, widget, floating bar, content bar, etc.', 'essb'),
			'profiles_widget', '', esc_html__('Enable', 'essb'), 'fa fa-check', 'ti-id-badge ao-lightblue-icon');
}

function essb5_advanced_natives_activate_options() {
	echo essb5_generate_code_advanced_activate_panel(esc_html__('Enable Native Social Buttons', 'essb'),
			esc_html__('Use selected native social buttons along with your share buttons', 'essb'),
			'native_active', '', esc_html__('Enable', 'essb'), 'fa fa-check', 'ti-thumb-up ao-lightblue-icon');
}