<?php

if (essb_option_bool_value('deactivate_module_subscribe')) {
	return;
}

ESSBOptionsStructureHelper::menu_item('optin', 'optin-1', __('Mailing List Platforms', 'essb'), ' ti-email');
ESSBOptionsStructureHelper::menu_item('optin', 'optin-14', __('Subscribe forms below content', 'essb'), ' ti-layout-media-overlay');
ESSBOptionsStructureHelper::menu_item('optin', 'optin-11', __('Subscribers Booster', 'essb'), ' ti-rocket');
ESSBOptionsStructureHelper::menu_item('optin', 'optin-12', __('Subscribers Flyout', 'essb'), ' ti-layout-media-center-alt');

ESSBOptionsStructureHelper::menu_item('optin', 'optin', __('Customize Form Designs', 'essb'), ' ti-ruler-pencil');

$active_d1 = essb5_has_setting_values(array('subscribe_mc_title', 'subscribe_mc_text', 'subscribe_mc_name', 'subscribe_mc_email', 'subscribe_mc_button', 'subscribe_mc_footer', 'subscribe_mc_success', 'subscribe_mc_error', 'customizer_subscribe_bgcolor1', 'customizer_subscribe_textcolor1', 'customizer_subscribe_hovercolor1', 'customizer_subscribe_hovertextcolor1', 'customizer_subscribe_emailcolor1'));
$active_d2 = essb5_has_setting_values(array('subscribe_mc_title2', 'subscribe_mc_text2', 'subscribe_mc_name2', 'subscribe_mc_email2', 'subscribe_mc_button2', 'subscribe_mc_footer2', 'subscribe_mc_success2', 'subscribe_mc_error2', 'customizer_subscribe_bgcolor2', 'customizer_subscribe_textcolor2', 'customizer_subscribe_hovercolor2', 'customizer_subscribe_hovertextcolor2', 'customizer_subscribe_emailcolor2'));
$active_d3 = essb5_has_setting_values(array('subscribe_mc_title3', 'subscribe_mc_text3', 'subscribe_mc_name3', 'subscribe_mc_email3', 'subscribe_mc_button3', 'subscribe_mc_footer3', 'subscribe_mc_success3', 'subscribe_mc_error3', 'customizer_subscribe_bgcolor3', 'customizer_subscribe_textcolor3', 'customizer_subscribe_hovercolor3', 'customizer_subscribe_hovertextcolor3', 'customizer_subscribe_emailcolor3'));
$active_d4 = essb5_has_setting_values(array('subscribe_mc_title4', 'subscribe_mc_text4', 'subscribe_mc_name4', 'subscribe_mc_email4', 'subscribe_mc_button4', 'subscribe_mc_footer4', 'subscribe_mc_success4', 'subscribe_mc_error4', 'customizer_subscribe_bgcolor4', 'customizer_subscribe_textcolor4', 'customizer_subscribe_hovercolor4', 'customizer_subscribe_hovertextcolor4', 'customizer_subscribe_emailcolor4'));
$active_d5 = essb5_has_setting_values(array('subscribe_mc_title5', 'subscribe_mc_text5', 'subscribe_mc_name5', 'subscribe_mc_email5', 'subscribe_mc_button5', 'subscribe_mc_footer5', 'subscribe_mc_success5', 'subscribe_mc_error5', 'customizer_subscribe_bgcolor5', 'customizer_subscribe_textcolor5', 'customizer_subscribe_hovercolor5', 'customizer_subscribe_hovertextcolor5', 'customizer_subscribe_emailcolor5'));
$active_d6 = essb5_has_setting_values(array('subscribe_mc_title6', 'subscribe_mc_text6', 'subscribe_mc_name6', 'subscribe_mc_email6', 'subscribe_mc_button6', 'subscribe_mc_footer6', 'subscribe_mc_success6', 'subscribe_mc_error6', 'customizer_subscribe_bgcolor6', 'customizer_subscribe_textcolor6', 'customizer_subscribe_hovercolor6', 'customizer_subscribe_hovertextcolor6', 'customizer_subscribe_emailcolor6'));
$active_d7 = essb5_has_setting_values(array('subscribe_mc_title7', 'subscribe_mc_text7', 'subscribe_mc_name7', 'subscribe_mc_email7', 'subscribe_mc_button7', 'subscribe_mc_footer7', 'subscribe_mc_success7', 'subscribe_mc_error7', 'customizer_subscribe_bgcolor7', 'customizer_subscribe_textcolor7', 'customizer_subscribe_hovercolor7', 'customizer_subscribe_hovertextcolor7', 'customizer_subscribe_emailcolor7'));
$active_d8 = essb5_has_setting_values(array('subscribe_mc_title8', 'subscribe_mc_text8', 'subscribe_mc_name8', 'subscribe_mc_email8', 'subscribe_mc_button8', 'subscribe_mc_footer8', 'subscribe_mc_success8', 'subscribe_mc_error8', 'customizer_subscribe_bgcolor8', 'customizer_subscribe_textcolor8', 'customizer_subscribe_hovercolor8', 'customizer_subscribe_hovertextcolor8', 'customizer_subscribe_emailcolor8'));
$active_d9 = essb5_has_setting_values(array('subscribe_mc_title9', 'subscribe_mc_text9', 'subscribe_mc_name9', 'subscribe_mc_email9', 'subscribe_mc_button9', 'subscribe_mc_footer9', 'subscribe_mc_success9', 'subscribe_mc_error9', 'customizer_subscribe_bgcolor9', 'customizer_subscribe_textcolor9', 'customizer_subscribe_hovercolor9', 'customizer_subscribe_hovertextcolor9', 'customizer_subscribe_emailcolor9'));

ESSBOptionsStructureHelper::field_heading('optin', 'optin', 'heading5', __('Integrated Designs', 'essb'), __('Modify the integrated inside plugin designs from #1 to #9. The save of the options will reload the screen. Do not forget to press the Update Options button in case you have unsaved changes done.', 'essb'));

essb5_menu_advanced_options_small_tile('optin', 'optin', __('Customize Design #1', 'essb'), '', __('The customize function provide a set of options that you can use to change the form displayed texts. You can also change the default form colors too.', 'essb'), '', 'true', '', 'subscribe-design1', __('Start Form Customizer', 'essb'), __('Customize the texts and colors of Design #1', 'essb'), ($active_d1 ? 'Customized' : ''), essb5_create_design_preview_button('design1'));
essb5_menu_advanced_options_small_tile('optin', 'optin', __('Customize Design #2', 'essb'), '', __('The customize function provide a set of options that you can use to change the form displayed texts. You can also change the default form colors too.', 'essb'), '', 'true', '', 'subscribe-design2', __('Start Form Customizer', 'essb'), __('Customize the texts and colors of Design #2', 'essb'), ($active_d2 ? 'Customized' : ''), essb5_create_design_preview_button('design2'));
essb5_menu_advanced_options_small_tile('optin', 'optin', __('Customize Design #3', 'essb'), '', __('The customize function provide a set of options that you can use to change the form displayed texts. You can also change the default form colors too.', 'essb'), '', 'true', '', 'subscribe-design3', __('Start Form Customizer', 'essb'), __('Customize the texts and colors of Design #3', 'essb'), ($active_d3 ? 'Customized' : ''), essb5_create_design_preview_button('design3'));
essb5_menu_advanced_options_small_tile('optin', 'optin', __('Customize Design #4', 'essb'), '', __('The customize function provide a set of options that you can use to change the form displayed texts. You can also change the default form colors too.', 'essb'), '', 'true', '', 'subscribe-design4', __('Start Form Customizer', 'essb'), __('Customize the texts and colors of Design #4', 'essb'), ($active_d4 ? 'Customized' : ''), essb5_create_design_preview_button('design4'));
essb5_menu_advanced_options_small_tile('optin', 'optin', __('Customize Design #5', 'essb'), '', __('The customize function provide a set of options that you can use to change the form displayed texts. You can also change the default form colors too.', 'essb'), '', 'true', '', 'subscribe-design5', __('Start Form Customizer', 'essb'), __('Customize the texts and colors of Design #5', 'essb'), ($active_d5 ? 'Customized' : ''), essb5_create_design_preview_button('design5'));
essb5_menu_advanced_options_small_tile('optin', 'optin', __('Customize Design #6', 'essb'), '', __('The customize function provide a set of options that you can use to change the form displayed texts. You can also change the default form colors too.', 'essb'), '', 'true', '', 'subscribe-design6', __('Start Form Customizer', 'essb'), __('Customize the texts and colors of Design #6', 'essb'), ($active_d6 ? 'Customized' : ''), essb5_create_design_preview_button('design6'));
essb5_menu_advanced_options_small_tile('optin', 'optin', __('Customize Design #7', 'essb'), '', __('The customize function provide a set of options that you can use to change the form displayed texts. You can also change the default form colors too.', 'essb'), '', 'true', '', 'subscribe-design7', __('Start Form Customizer', 'essb'), __('Customize the texts and colors of Design #7', 'essb'), ($active_d7 ? 'Customized' : ''), essb5_create_design_preview_button('design7'));
essb5_menu_advanced_options_small_tile('optin', 'optin', __('Customize Design #8', 'essb'), '', __('The customize function provide a set of options that you can use to change the form displayed texts. You can also change the default form colors too.', 'essb'), '', 'true', '', 'subscribe-design8', __('Start Form Customizer', 'essb'), __('Customize the texts and colors of Design #8', 'essb'), ($active_d8 ? 'Customized' : ''), essb5_create_design_preview_button('design8'));
essb5_menu_advanced_options_small_tile('optin', 'optin', __('Customize Design #9', 'essb'), '', __('The customize function provide a set of options that you can use to change the form displayed texts. You can also change the default form colors too.', 'essb'), '', 'true', '', 'subscribe-design9', __('Start Form Customizer', 'essb'), __('Customize the texts and colors of Design #9', 'essb'), ($active_d9 ? 'Customized' : ''), essb5_create_design_preview_button('design9'));

ESSBOptionsStructureHelper::field_heading('optin', 'optin', 'heading5', __('Own Designs', 'essb'), __('Add, remove or change created by user form designs. Those form designs you can use anywhere inside plugin where subscribe forms are present', 'essb'));
ESSBOptionsStructureHelper::field_component('optin', 'optin', 'essb5_add_subscribe_design_button');
// Easy Optin
$optin_connectors = array("mailchimp" => "MailChimp",
		"getresponse" => "GetResponse",
		"mymail" => "Mailster",
		"mailpoet" => "MailPoet",
		"mailerlite" => "MailerLite",
		"activecampaign" => "ActiveCampaign",
		"campaignmonitor" => "CampaignMonitor",
		"sendinblue" => "SendinBlue",
		"madmimi" => "Mad Mimi",
		"conversio" => "Conversio");

if (has_filter('essb_external_subscribe_connectors')) {
	$optin_connectors = apply_filters('essb_external_subscribe_connectors', $optin_connectors);
}

ESSBOptionsStructureHelper::panel_start('optin', 'optin-14', __('Automatically add opt-in form below content', 'essb'), __('Activate of this option will allow to include forms on posts or pages to get more subscribers.', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'optin_content_activate', 'switch_on' => __('Yes', 'essb'), 'switch_off' => __('No', 'essb')));

ESSBOptionsStructureHelper::field_section_start_full_panels('optin', 'optin-14');
ESSBOptionsStructureHelper::field_switch_panel('optin', 'optin-14', 'essb3_of|of_posts', __('Display on posts', 'essb'), __('Automatically display subscribe form on posts below content.', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));
ESSBOptionsStructureHelper::field_switch_panel('optin', 'optin-14', 'essb3_of|of_pages', __('Display on pages', 'essb'), __('Automatically display subscribe form on pages below content.', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));
ESSBOptionsStructureHelper::field_select_panel('optin', 'optin-14', 'essb3_of|of_design', __('Use followin template', 'essb'), __('Choose the design of optin forms that you wish to use for automatically generated forms', 'essb'), essb_optin_designs());
ESSBOptionsStructureHelper::field_section_end_full_panels('optin', 'optin-14');
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-14', 'essb3_of|of_exclude', __('Exclude display on', 'essb'), __('Exclude buttons on posts/pages with these IDs. Comma separated: "11, 15, 125". This will deactivate automated display of buttons on selected posts/pages but you are able to use shortcode on them.', 'essb'), '');

ESSBOptionsStructureHelper::field_switch('optin', 'optin-14', 'essb3_of|of_creditlink', __('Include credit link', 'essb'), __('Include tiny credit link below your form to allow others know what you are using to generate subscribe forms. Activate this option to show your appreciation for our efforts and allow you earn money from Envato affiliate program.', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));
ESSBOptionsStructureHelper::field_textbox('optin', 'optin-14', 'essb3_of|of_creditlink_user', __('Your Envato username', 'essb'), __('Include credit link and earn 30% of users first purchase via Envato Affiliate Program. Learn more <a href="https://themeforest.net/affiliate_program" target="_blank">here</a> for Envato affiliate program.', 'essb'), '');

ESSBOptionsStructureHelper::panel_end('optin', 'optin-14');

ESSBOptionsStructureHelper::panel_start('optin', 'optin-11', __('Automatically add pop up opt-in form (boost subscribers)', 'essb'), __('Activate of this option will allow to include forms on posts or pages to get more subscribers.', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'optin_booster_activate', 'switch_on' => __('Yes', 'essb'), 'switch_off' => __('No', 'essb')));

ESSBOptionsStructureHelper::panel_start('optin', 'optin-11', __('Limit display on selected post types only', 'essb'), __('Use this area if you wish to make function work for selected post types only. Otherwise it will appear everywhere on your site.', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'optin_booster_activate_posttypes', 'switch_on' => __('Yes', 'essb'), 'switch_off' => __('No', 'essb')));
ESSBOptionsStructureHelper::field_checkbox_list('optin', 'optin-11', 'essb3_ofob|posttypes', __('Appear only on selected post types', 'essb'), __('Limit function to work only on selected post types. Leave non option selected to make it work on all', 'essb'), essb_get_post_types());
ESSBOptionsStructureHelper::field_switch('optin', 'optin-11', 'essb3_ofob|deactivate_homepage', __('Deactivate display on homepage', 'essb'), __('Exclude display of function on home page of your site.', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('optin', 'optin-11');

	ESSBOptionsStructureHelper::field_switch_panel('optin', 'optin-11', 'essb3_ofob|ofob_single', __('Appear once for user', 'easy-optin-booster'), __('Activate this option if you wish to make event appear only once for user in the next 14 days. ', 'easy-optin-booster'), '', __('Yes', 'easy-optin-booster'), __('No', 'easy-optin-booster'));
	ESSBOptionsStructureHelper::field_textbox_panel('optin', 'optin-11', 'essb3_ofob|ofob_single_time', __('Custom appear once period (days)', 'easy-optin-booster'), __('If the option to show once is set the default period is 14 days. Fill here a custom days value (numeric)', 'easy-optin-booster'), '');
	ESSBOptionsStructureHelper::field_textbox_panel('optin', 'optin-11', 'essb3_ofob|ofob_exclude', __('Exclude display on', 'essb'), __('Exclude buttons on posts/pages with these IDs. Comma separated: "11, 15, 125". This will deactivate automated display of buttons on selected posts/pages but you are able to use shortcode on them.', 'essb'), '');

	ESSBOptionsStructureHelper::panel_start('optin', 'optin-11', __('Display after amount of seconds', 'easy-optin-booster'), __('Automatically display selected opt-in form after amount of seconds. If you wish to trigger immediately after load then you can use 1 as value', 'easy-optin-booster'), 'fa21 fa fa-cogs', array("mode" => "toggle", 'switch_id' => '', 'switch_on' => __('Yes', 'easy-optin-booster'), 'switch_off' => __('No', 'easy-optin-booster')));
	ESSBOptionsStructureHelper::field_switch('optin', 'optin-11', 'essb3_ofob|ofob_time', __('Activate', 'easy-optin-booster'), __('Set this option to Yes to use this event', 'easy-optin-booster'), '', __('Yes', 'easy-optin-booster'), __('No', 'easy-optin-booster'));
	ESSBOptionsStructureHelper::field_section_start_panels('optin', 'optin-11', '', '');
	ESSBOptionsStructureHelper::field_textbox_panel('optin', 'optin-11', 'essb3_ofob|ofob_time_delay', __('Display after seconds', 'easy-optin-booster'), __('If you wish to display it immediately after load use 1 as value. Otherwise provide value of seconds you wish to use. Blank field will avoid display of opt-in form', 'easy-optin-booster'), '', 'input60', 'fa-clock-o', 'right');
	ESSBOptionsStructureHelper::field_select_panel('optin', 'optin-11', 'essb3_ofob|of_time_design', __('Choose design for that event', 'easy-facebook-comments'), __('Choose design which will be used for that event. You can have different design on each event', 'easy-facebook-comments'), essb_optin_designs());
	ESSBOptionsStructureHelper::field_color_panel('optin', 'optin-11', 'essb3_ofob|of_time_bgcolor', __('Overlay background color', 'easy-optin-booster'), __('Change overlay background color that will be used for that event. You may need to replace the color if you customize design of chosen template.', 'easy-optin-booster'), '', 'true');
	ESSBOptionsStructureHelper::field_section_end_panels('optin', 'optin-11', '', '');
	ESSBOptionsStructureHelper::field_section_start_panels('optin', 'optin-11', '', '');
	ESSBOptionsStructureHelper::field_select_panel('optin', 'optin-11', 'essb3_ofob|of_time_close', __('Choose close type', 'easy-facebook-comments'), __('Choose how you wish to close the pop up form - with close icon or text link.', 'essb-optin-booster'), array("icon" => "Close Icon", "text" => "Text close link"));
	ESSBOptionsStructureHelper::field_color_panel('optin', 'optin-11', 'essb3_ofob|of_time_closecolor', __('Close action color', 'easy-optin-booster'), __('Customize close action color in case you change overlay color. Otherwise you can leave the default', 'easy-optin-booster'));
	ESSBOptionsStructureHelper::field_textbox_panel('optin', 'optin-11', 'essb3_ofob|of_time_closetext', __('Custom close text', 'easy-optin-booster'), __('Enter custom close text when you choose text mode of close function.', 'easy-optin-booster'), '');
	ESSBOptionsStructureHelper::field_section_end_panels('optin', 'optin-11', '', '');
	ESSBOptionsStructureHelper::panel_end('optin', 'optin-11');

	ESSBOptionsStructureHelper::panel_start('optin', 'optin-11', __('Display on scroll', 'easy-optin-booster'), __('Automatically display selected opt-in form when percent of content is viewed', 'easy-optin-booster'), 'fa21 fa fa-cogs', array("mode" => "toggle", 'switch_id' => '', 'switch_on' => __('Yes', 'easy-optin-booster'), 'switch_off' => __('No', 'easy-optin-booster')));
	ESSBOptionsStructureHelper::field_switch('optin', 'optin-11', 'essb3_ofob|ofob_scroll', __('Activate', 'easy-optin-booster'), __('Set this option to Yes to use this event', 'easy-optin-booster'), '', __('Yes', 'easy-optin-booster'), __('No', 'easy-optin-booster'));
	ESSBOptionsStructureHelper::field_section_start_panels('optin', 'optin-11', '', '');
	ESSBOptionsStructureHelper::field_textbox_panel('optin', 'optin-11', 'essb3_ofob|ofob_scroll_percent', __('Percent of content', 'easy-optin-booster'), __('Use numeric value without symbols. Exmaple: 40', 'easy-optin-booster'), '', 'input60', 'fa-long-arrow-down', 'right');
	ESSBOptionsStructureHelper::field_select_panel('optin', 'optin-11', 'essb3_ofob|of_scroll_design', __('Choose design for that event', 'easy-facebook-comments'), __('Choose design which will be used for that event. You can have different design on each event', 'easy-facebook-comments'), essb_optin_designs());
	ESSBOptionsStructureHelper::field_color_panel('optin', 'optin-11', 'essb3_ofob|of_scroll_bgcolor', __('Overlay background color', 'easy-optin-booster'), __('Change overlay background color that will be used for that event. You may need to replace the color if you customize design of chosen template.', 'easy-optin-booster'), '', 'true');
	ESSBOptionsStructureHelper::field_section_end_panels('optin', 'optin-11', '', '');
	ESSBOptionsStructureHelper::field_section_start_panels('optin', 'optin-11', '', '');
	ESSBOptionsStructureHelper::field_select_panel('optin', 'optin-11', 'essb3_ofob|of_scroll_close', __('Choose close type', 'easy-facebook-comments'), __('Choose how you wish to close the pop up form - with close icon or text link.', 'essb-optin-booster'), array("icon" => "Close Icon", "text" => "Text close link"));
	ESSBOptionsStructureHelper::field_color_panel('optin', 'optin-11', 'essb3_ofob|of_scroll_closecolor', __('Close action color', 'easy-optin-booster'), __('Customize close action color in case you change overlay color. Otherwise you can leave the default', 'easy-optin-booster'));
	ESSBOptionsStructureHelper::field_textbox_panel('optin', 'optin-11', 'essb3_ofob|of_scroll_closetext', __('Custom close text', 'easy-optin-booster'), __('Enter custom close text when you choose text mode of close function.', 'easy-optin-booster'), '');
	ESSBOptionsStructureHelper::field_section_end_panels('optin', 'optin-11', '', '');
	ESSBOptionsStructureHelper::panel_end('optin', 'optin-11');

	ESSBOptionsStructureHelper::panel_start('optin', 'optin-11', __('Display on exit intent', 'easy-optin-booster'), __('Automatically display selected opt-in form when user try to leave window', 'easy-optin-booster'), 'fa21 fa fa-cogs', array("mode" => "toggle", 'switch_id' => '', 'switch_on' => __('Yes', 'easy-optin-booster'), 'switch_off' => __('No', 'easy-optin-booster')));
	ESSBOptionsStructureHelper::field_switch('optin', 'optin-11', 'essb3_ofob|ofob_exit', __('Activate', 'easy-optin-booster'), __('Set this option to Yes to use this event', 'easy-optin-booster'), '', __('Yes', 'easy-optin-booster'), __('No', 'easy-optin-booster'));
	ESSBOptionsStructureHelper::field_section_start_panels('optin', 'optin-11', '', '');
	ESSBOptionsStructureHelper::field_select_panel('optin', 'optin-11', 'essb3_ofob|of_exit_design', __('Choose design for that event', 'easy-facebook-comments'), __('Choose design which will be used for that event. You can have different design on each event', 'easy-facebook-comments'), essb_optin_designs());
	ESSBOptionsStructureHelper::field_color_panel('optin', 'optin-11', 'essb3_ofob|of_exit_bgcolor', __('Overlay background color', 'easy-optin-booster'), __('Change overlay background color that will be used for that event. You may need to replace the color if you customize design of chosen template.', 'easy-optin-booster'), '', 'true');
	ESSBOptionsStructureHelper::field_section_end_panels('optin', 'optin-11', '', '');
	ESSBOptionsStructureHelper::field_section_start_panels('optin', 'optin-11', '', '');
	ESSBOptionsStructureHelper::field_select_panel('optin', 'optin-11', 'essb3_ofob|of_exit_close', __('Choose close type', 'easy-facebook-comments'), __('Choose how you wish to close the pop up form - with close icon or text link.', 'essb-optin-booster'), array("icon" => "Close Icon", "text" => "Text close link"));
	ESSBOptionsStructureHelper::field_color_panel('optin', 'optin-11', 'essb3_ofob|of_exit_closecolor', __('Close action color', 'easy-optin-booster'), __('Customize close action color in case you change overlay color. Otherwise you can leave the default', 'easy-optin-booster'));
	ESSBOptionsStructureHelper::field_textbox_panel('optin', 'optin-11', 'essb3_ofob|of_exit_closetext', __('Custom close text', 'easy-optin-booster'), __('Enter custom close text when you choose text mode of close function.', 'easy-optin-booster'), '');
	ESSBOptionsStructureHelper::field_section_end_panels('optin', 'optin-11', '', '');
	ESSBOptionsStructureHelper::panel_end('optin', 'optin-11');

	ESSBOptionsStructureHelper::field_switch('optin', 'optin-11', 'essb3_ofob|ofob_creditlink', __('Include credit link', 'easy-optin-booster'), __('Include tiny credit link below your comments box to allow others know what you are using to generate Facebook Comments. Activate this option to show your appreciation for our efforts.', 'easy-optin-booster'), '', __('Yes', 'easy-optin-booster'), __('No', 'easy-optin-booster'));
	ESSBOptionsStructureHelper::field_textbox('optin', 'optin-11', 'essb3_ofob|ofob_creditlink_user', __('Your Envato username', 'easy-optin-booster'), __('Include credit link and earn 30% of users first purchase via Envato Affiliate Program. Learn more <a href="https://themeforest.net/affiliate_program" target="_blank">here</a> for Envato affiliate program.', 'easy-optin-booster'), '');



ESSBOptionsStructureHelper::panel_end('optin', 'optin-11');

ESSBOptionsStructureHelper::panel_start('optin', 'optin-12', __('Automatically add fly out opt-in form', 'essb'), __('Activate of this option will allow to include forms on posts or pages to get more subscribers.', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'optin_flyout_activate', 'switch_on' => __('Yes', 'essb'), 'switch_off' => __('No', 'essb')));

ESSBOptionsStructureHelper::panel_start('optin', 'optin-12', __('Limit display on selected post types only', 'essb'), __('Use this area if you wish to make function work for selected post types only. Otherwise it will appear everywhere on your site.', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'optin_flyout_activate_posttypes', 'switch_on' => __('Yes', 'essb'), 'switch_off' => __('No', 'essb')));
ESSBOptionsStructureHelper::field_checkbox_list('optin', 'optin-12', 'essb3_ofof|posttypes', __('Appear only on selected post types', 'essb'), __('Limit function to work only on selected post types. Leave non option selected to make it work on all', 'essb'), essb_get_post_types());
ESSBOptionsStructureHelper::field_switch('optin', 'optin-12', 'essb3_ofof|of_deactivate_homepage', __('Deactivate display on homepage', 'essb'), __('Exclude display of function on home page of your site.', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('optin', 'optin-12');


	ESSBOptionsStructureHelper::field_switch('optin', 'optin-12', 'essb3_ofof|ofof_single', __('Appear once for user', 'easy-optin-flyout'), __('Activate this option if you wish to make event appear only once for user in the next 14 days. ', 'easy-optin-flyout'), '', __('Yes', 'easy-optin-flyout'), __('No', 'easy-optin-flyout'));
	ESSBOptionsStructureHelper::field_select('optin', 'optin-12', 'essb3_ofof|ofof_position', __('Appear at', 'easy-optin-flyout'), __('Choose position where the fly out will appear', 'easy-optin-flyout'), array("bottom-right" => __('Bottom Right', 'easy-optin-flyout'), "bottom-left" => __('Bottom Left', 'easy-optin-flyout'), "top-right" => __('Top Right', 'easy-optin-flyout'), "top-left" => __('Top Left', 'easy-optin-flyout')));
	ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-12', 'essb3_ofof|ofof_exclude', __('Exclude display on', 'essb'), __('Exclude buttons on posts/pages with these IDs. Comma separated: "11, 15, 125". This will deactivate automated display of buttons on selected posts/pages but you are able to use shortcode on them.', 'essb'), '');

	ESSBOptionsStructureHelper::panel_start('optin', 'optin-12', __('Display after amount of seconds', 'easy-optin-flyout'), __('Automatically display selected opt-in form after amount of seconds. If you wish to trigger immediately after load then you can use 1 as value', 'easy-optin-flyout'), 'fa21 fa fa-cogs', array("mode" => "toggle", 'switch_id' => '', 'switch_on' => __('Yes', 'easy-optin-flyout'), 'switch_off' => __('No', 'easy-optin-flyout')));
	ESSBOptionsStructureHelper::field_switch('optin', 'optin-12', 'essb3_ofof|ofof_time', __('Activate', 'easy-optin-flyout'), __('Set this option to Yes to use this event', 'easy-optin-flyout'), '', __('Yes', 'easy-optin-flyout'), __('No', 'easy-optin-flyout'));
	ESSBOptionsStructureHelper::field_section_start_panels('optin', 'optin-12', '', '');
	ESSBOptionsStructureHelper::field_textbox_panel('optin', 'optin-12', 'essb3_ofof|ofof_time_delay', __('Display after seconds', 'easy-optin-flyout'), __('If you wish to display it immediately after load use 1 as value. Otherwise provide value of seconds you wish to use. Blank field will avoid display of opt-in form', 'easy-optin-flyout'), '', 'input60', 'fa-clock-o', 'right');
	ESSBOptionsStructureHelper::field_select_panel('optin', 'optin-12', 'essb3_ofof|of_time_design', __('Choose design for that event', 'easy-optin-flyout'), __('Choose design which will be used for that event. You can have different design on each event', 'easy-optin-flyout'), essb_optin_designs());
	ESSBOptionsStructureHelper::field_color_panel('optin', 'optin-12', 'essb3_ofof|of_time_bgcolor', __('Overlay background color', 'easy-optin-flyout'), __('Change overlay background color that will be used for that event. You may need to replace the color if you customize design of chosen template.', 'easy-optin-flyout'), '', 'true');
	ESSBOptionsStructureHelper::field_section_end_panels('optin', 'optin-12', '', '');
	ESSBOptionsStructureHelper::field_section_start_panels('optin', 'optin-12', '', '');
	ESSBOptionsStructureHelper::field_select_panel('optin', 'optin-12', 'essb3_ofof|of_time_close', __('Choose close type', 'easy-optin-flyout'), __('Choose how you wish to close the pop up form - with close icon or text link.', 'essb-optin-booster'), array("icon" => "Close Icon", "text" => "Text close link"));
	ESSBOptionsStructureHelper::field_color_panel('optin', 'optin-12', 'essb3_ofof|of_time_closecolor', __('Close action color', 'easy-optin-flyout'), __('Customize close action color in case you change overlay color. Otherwise you can leave the default', 'easy-optin-flyout'));
	ESSBOptionsStructureHelper::field_textbox_panel('optin', 'optin-12', 'essb3_ofof|of_time_closetext', __('Custom close text', 'easy-optin-flyout'), __('Enter custom close text when you choose text mode of close function.', 'easy-optin-flyout'), '');
	ESSBOptionsStructureHelper::field_section_end_panels('optin', 'optin-12', '', '');
	ESSBOptionsStructureHelper::panel_end('optin', 'optin-12');

	ESSBOptionsStructureHelper::panel_start('optin', 'optin-12', __('Display on scroll', 'easy-optin-flyout'), __('Automatically display selected opt-in form when percent of content is viewed', 'easy-optin-flyout'), 'fa21 fa fa-cogs', array("mode" => "toggle", 'switch_id' => '', 'switch_on' => __('Yes', 'easy-optin-flyout'), 'switch_off' => __('No', 'easy-optin-flyout')));
	ESSBOptionsStructureHelper::field_switch('optin', 'optin-12', 'essb3_ofof|ofof_scroll', __('Activate', 'easy-optin-flyout'), __('Set this option to Yes to use this event', 'easy-optin-flyout'), '', __('Yes', 'easy-optin-flyout'), __('No', 'easy-optin-flyout'));
	ESSBOptionsStructureHelper::field_section_start_panels('optin', 'optin-12', '', '');
	ESSBOptionsStructureHelper::field_textbox_panel('optin', 'optin-12', 'essb3_ofof|ofof_scroll_percent', __('Percent of content', 'easy-optin-flyout'), __('Use numeric value without symbols. Exmaple: 40', 'easy-optin-flyout'), '', 'input60', 'fa-long-arrow-down', 'right');
	ESSBOptionsStructureHelper::field_select_panel('optin', 'optin-12', 'essb3_ofof|of_scroll_design', __('Choose design for that event', 'easy-optin-flyout'), __('Choose design which will be used for that event. You can have different design on each event', 'easy-optin-flyout'), essb_optin_designs());
	ESSBOptionsStructureHelper::field_color_panel('optin', 'optin-12', 'essb3_ofof|of_scroll_bgcolor', __('Overlay background color', 'easy-optin-flyout'), __('Change overlay background color that will be used for that event. You may need to replace the color if you customize design of chosen template.', 'easy-optin-flyout'), '', 'true');
	ESSBOptionsStructureHelper::field_section_end_panels('optin', 'optin-12', '', '');
	ESSBOptionsStructureHelper::field_section_start_panels('optin', 'optin-12', '', '');
	ESSBOptionsStructureHelper::field_select_panel('optin', 'optin-12', 'essb3_ofof|of_scroll_close', __('Choose close type', 'easy-optin-flyout'), __('Choose how you wish to close the pop up form - with close icon or text link.', 'essb-optin-booster'), array("icon" => "Close Icon", "text" => "Text close link"));
	ESSBOptionsStructureHelper::field_color_panel('optin', 'optin-12', 'essb3_ofof|of_scroll_closecolor', __('Close action color', 'easy-optin-flyout'), __('Customize close action color in case you change overlay color. Otherwise you can leave the default', 'easy-optin-flyout'));
	ESSBOptionsStructureHelper::field_textbox_panel('optin', 'optin-12', 'essb3_ofof|of_scroll_closetext', __('Custom close text', 'easy-optin-flyout'), __('Enter custom close text when you choose text mode of close function.', 'easy-optin-flyout'), '');
	ESSBOptionsStructureHelper::field_section_end_panels('optin', 'optin-12', '', '');
	ESSBOptionsStructureHelper::panel_end('optin', 'optin-12');

	ESSBOptionsStructureHelper::panel_start('optin', 'optin-12', __('Display on exit intent', 'easy-optin-flyout'), __('Automatically display selected opt-in form when user try to leave window', 'easy-optin-flyout'), 'fa21 fa fa-cogs', array("mode" => "toggle", 'switch_id' => '', 'switch_on' => __('Yes', 'easy-optin-flyout'), 'switch_off' => __('No', 'easy-optin-flyout')));
	ESSBOptionsStructureHelper::field_switch('optin', 'optin-12', 'essb3_ofof|ofof_exit', __('Activate', 'easy-optin-flyout'), __('Set this option to Yes to use this event', 'easy-optin-flyout'), '', __('Yes', 'easy-optin-flyout'), __('No', 'easy-optin-flyout'));
	ESSBOptionsStructureHelper::field_section_start_panels('optin', 'optin-12', '', '');
	ESSBOptionsStructureHelper::field_select_panel('optin', 'optin-12', 'essb3_ofof|of_exit_design', __('Choose design for that event', 'easy-optin-flyout'), __('Choose design which will be used for that event. You can have different design on each event', 'easy-optin-flyout'), essb_optin_designs());
	ESSBOptionsStructureHelper::field_color_panel('optin', 'optin-12', 'essb3_ofof|of_exit_bgcolor', __('Overlay background color', 'easy-optin-flyout'), __('Change overlay background color that will be used for that event. You may need to replace the color if you customize design of chosen template.', 'easy-optin-flyout'), '', 'true');
	ESSBOptionsStructureHelper::field_section_end_panels('optin', 'optin-12', '', '');
	ESSBOptionsStructureHelper::field_section_start_panels('optin', 'optin-12', '', '');
	ESSBOptionsStructureHelper::field_select_panel('optin', 'optin-12', 'essb3_ofof|of_exit_close', __('Choose close type', 'easy-optin-flyout'), __('Choose how you wish to close the pop up form - with close icon or text link.', 'essb-optin-booster'), array("icon" => "Close Icon", "text" => "Text close link"));
	ESSBOptionsStructureHelper::field_color_panel('optin', 'optin-12', 'essb3_ofof|of_exit_closecolor', __('Close action color', 'easy-optin-flyout'), __('Customize close action color in case you change overlay color. Otherwise you can leave the default', 'easy-optin-flyout'));
	ESSBOptionsStructureHelper::field_textbox_panel('optin', 'optin-12', 'essb3_ofof|of_exit_closetext', __('Custom close text', 'easy-optin-flyout'), __('Enter custom close text when you choose text mode of close function.', 'easy-optin-flyout'), '');
	ESSBOptionsStructureHelper::field_section_end_panels('optin', 'optin-12', '', '');
	ESSBOptionsStructureHelper::panel_end('optin', 'optin-12');


	ESSBOptionsStructureHelper::field_switch('optin', 'optin-12', 'essb3_ofof|ofof_creditlink', __('Include credit link', 'easy-optin-flyout'), __('Include tiny credit link below your comments box to allow others know what you are using to generate Facebook Comments. Activate this option to show your appreciation for our efforts.', 'easy-optin-flyout'), '', __('Yes', 'easy-optin-flyout'), __('No', 'easy-optin-flyout'));
	ESSBOptionsStructureHelper::field_textbox('optin', 'optin-12', 'essb3_ofof|ofof_creditlink_user', __('Your Envato username', 'easy-optin-flyout'), __('Include credit link and earn 30% of users first purchase via Envato Affiliate Program. Learn more <a href="https://themeforest.net/affiliate_program" target="_blank">here</a> for Envato affiliate program.', 'easy-optin-flyout'), '');

	ESSBOptionsStructureHelper::panel_end('optin', 'optin-12');

ESSBOptionsStructureHelper::field_select_panel('optin', 'optin-1', 'subscribe_connector', __('Choose your service', 'essb'), __('Select service that you wish to integrate with Easy Optin forms. Please note that for correct work you need to fill all required authorizations details for it below', 'essb'), $optin_connectors);
ESSBOptionsStructureHelper::field_switch_panel('optin', 'optin-1', 'subscribe_widget', __('Activate subscribe widget & shortcode', 'essb'), __('Activation of this option will allow you to use subscribe widget and shortcode anywhere on your site not connected with subscribe button inside share buttons', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));
ESSBOptionsStructureHelper::field_switch_panel('optin', 'optin-1', 'subscribe_css_always', __('Always load subscribe form styles', 'essb'), __('Set this to Yes if for some reason styles for subscribe forms does not appear on your site.', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));

if (!essb_option_bool_value('deactivate_module_conversions')) {
	ESSBOptionsStructureHelper::field_switch_panel('optin', 'optin-1', 'conversions_subscribe_lite_run', __('Track Subscribe Forms Conversion', 'essb'), __('Subscribe forms conversion is an easy way to manage and optimize display of subscribe forms on your site. Once active plugin will collect data for each displayed position and subscribes. You have also access to past 7 days historical data. <a href="https://socialsharingplugin.com/subscribe-conversions-lite/" target="_blank">Learn more for subscribe conversions lite</a>.', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));
}


ESSBOptionsStructureHelper::panel_start('optin', 'optin-1', __('Redirect on successful subscribe', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_success', __('Redirect to page on successful subscribe', 'essb'), __('If you wish to redirect users to page (example: Thank you page) fill its URL here. If field is blank plugin will not redirect. The URL should be filled in full - example: https://socialsharingplugin.com/thank-you/', 'essb'));
ESSBOptionsStructureHelper::field_switch('optin', 'optin-1', 'subscribe_success_new', __('Open successful redirect in a new window', 'essb'), __('Set to Yes if you wish the successful URL to appear in a popup instead of redirect on same page.', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('optin', 'optin-1');

ESSBOptionsStructureHelper::panel_start('optin', 'optin-1', __('Additional form settings', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::structure_row_start('optin', 'optin-1');
ESSBOptionsStructureHelper::structure_section_start('optin', 'optin-1', 'c6');
ESSBOptionsStructureHelper::field_switch('optin', 'optin-1', 'subscribe_require_name', __('Make name field required', 'essb'), __('Set this option to Yes if you need to make the name field mandatory when it is used in a design.', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'), '', '9');
ESSBOptionsStructureHelper::structure_section_end('optin', 'optin-1');
ESSBOptionsStructureHelper::structure_section_start('optin', 'optin-1', 'c6');
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_require_name_error', __('Not filled in name error', 'essb'), __('Localize the text of error message that name field is blank or leave blank to show the default', 'essb'));
ESSBOptionsStructureHelper::structure_section_end('optin', 'optin-1');
ESSBOptionsStructureHelper::structure_row_end('optin', 'optin-1');
ESSBOptionsStructureHelper::panel_end('optin', 'optin-1');


ESSBOptionsStructureHelper::panel_start('optin', 'optin-1', __('Include agree to terms confirmation (GDPR Recommended)', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_switch('optin', 'optin-1', 'subscribe_terms', __('Include I agree to terms confirmation', 'essb'), __('Set this option to Yes to add in form a checkbox that will require users to confirm that they agree with terms before submitting. (Recommended for usage in EU).', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_terms_text', __('Custom terms confirmation text', 'essb'), __('Set a custom text that will appear in the confirmation check before submission', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_terms_error', __('Custom not checked confirmation error', 'essb'), __('Set your own error message when confirmation box is not set.', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_terms_field', __('Forward confirmation status to mailing list custom field', 'essb'), __('For selected services it is possible to automatically write in a custom field that user confirm the sign up with the check. If your is in the supported list you can enter here the custom list parameter ID and plugin will store Yes. Supported by: MailChimp, GetReponse, MailPoet, ActiveCampaign, CampaignMonitor, SendInBlue', 'essb'));
ESSBOptionsStructureHelper::panel_end('optin', 'optin-1');


ESSBOptionsStructureHelper::holder_start('optin', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-mailchimp');
ESSBOptionsStructureHelper::panel_start('optin', 'optin-1', __('MailChimp', 'essb'), __('Configure mailing list service access details', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle"));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_mc_api', __('Mailchimp API key', 'essb'), __('<a href="http://kb.mailchimp.com/accounts/management/about-api-keys#Finding-or-generating-your-API-key" target="_blank">Find your API key</a>', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_mc_list', __('Mailchimp List ID', 'essb'), __('<a href="http://kb.mailchimp.com/lists/managing-subscribers/find-your-list-id" target="_blank">Find your List ID</a>', 'essb'));
ESSBOptionsStructureHelper::field_section_start_full_panels('optin', 'optin-1');
ESSBOptionsStructureHelper::field_switch_panel('optin', 'optin-1', 'subscribe_mc_welcome', __('Send welcome message:', 'essb'), __('Allow Mailchimp send welcome mssage upon subscribe.', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));
ESSBOptionsStructureHelper::field_switch_panel('optin', 'optin-1', 'subscribe_mc_double', __('Use double opt in:', 'essb'), __('The MailChimp double opt-in process is a two-step process, where a subscriber fills out your signup form and receives an email with a link to confirm their subscription. MailChimp also includes some additional thank you and confirmation pages you can customize with your brand and messaging.', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));
//ESSBOptionsStructureHelper::field_switch_panel('optin', 'optin-1', 'subscribe_mc_namefield', __('Display name field:', 'essb'), __('Activate this option to allow customers enter their name.', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));
ESSBOptionsStructureHelper::field_section_end_full_panels('optin', 'optin-1');
ESSBOptionsStructureHelper::field_section_start_full_panels('optin', 'optin-1');
ESSBOptionsStructureHelper::field_textbox_panel('optin', 'optin-1', 'subscribe_mc_custompos', __('Position Custom Field', 'essb'), __('If you wish to have the position where user fill the subscribe form inside your list use this field. You need to create a text field assigned to your list in MailChimp at first and than enter its ID here. <a href="https://mailchimp.com/help/manage-list-and-signup-form-fields/" target="_blank">Learn more for custom fields.</a>', 'essb'));
ESSBOptionsStructureHelper::field_textbox_panel('optin', 'optin-1', 'subscribe_mc_customdes', __('Design Custom Field', 'essb'), __('If you wish to have the design used in the form user fill to subscribe inside your list use this field. You need to create a text field assigned to your list in MailChimp at first and than enter its ID here. <a href="https://mailchimp.com/help/manage-list-and-signup-form-fields/" target="_blank">Learn more for custom fields.</a>', 'essb'));
ESSBOptionsStructureHelper::field_textbox_panel('optin', 'optin-1', 'subscribe_mc_customtitle', __('Post/Page Title Custom Field', 'essb'), __('If you need the page/post where used fill the form use this field. You need to create a text field assigned to your list in MailChimp at first and than enter its ID here. <a href="https://mailchimp.com/help/manage-list-and-signup-form-fields/" target="_blank">Learn more for custom fields.</a>', 'essb'));
ESSBOptionsStructureHelper::field_section_end_full_panels('optin', 'optin-1');
ESSBOptionsStructureHelper::panel_end('optin', 'optin-1');
ESSBOptionsStructureHelper::holder_end('optin', 'optin-1');

ESSBOptionsStructureHelper::holder_start('optin', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-getresponse');
ESSBOptionsStructureHelper::panel_start('optin', 'optin-1', __('GetResponse', 'essb'), __('Configure mailing list service access details', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle"));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_gr_api', __('GetReponse API key', 'essb'), __('<a href="http://support.getresponse.com/faq/where-i-find-api-key" target="_blank">Find your API key</a>', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_gr_list', __('GetReponse Campaign Name', 'essb'), __('<a href="http://support.getresponse.com/faq/can-i-change-the-name-of-a-campaign" target="_blank">Find your campaign name</a>', 'essb'));
ESSBOptionsStructureHelper::panel_end('optin', 'optin-1');
ESSBOptionsStructureHelper::holder_end('optin', 'optin-1');

ESSBOptionsStructureHelper::holder_start('optin', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-mailerlite');
ESSBOptionsStructureHelper::panel_start('optin', 'optin-1', __('MailerLite', 'essb'), __('Configure mailing list service access details', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle"));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_ml_api', __('MailerLite API key', 'essb'), __('Entery your MailerLite API key. To get your key visit this page <a href="https://app.mailerlite.com/subscribe/api" target="_blank">https://app.mailerlite.com/subscribe/api</a> and look under API key.', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_ml_list', __('MailerLite List ID (Group ID)', 'essb'), __('Enter your list id (aka Group ID). To find your group id visit again the page for API key generation and you will see all list you have with their ids.', 'essb'));
ESSBOptionsStructureHelper::panel_end('optin', 'optin-1');
ESSBOptionsStructureHelper::holder_end('optin', 'optin-1');

ESSBOptionsStructureHelper::holder_start('optin', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-activecampaign');
ESSBOptionsStructureHelper::panel_start('optin', 'optin-1', __('ActiveCampaign', 'essb'), __('Configure mailing list service access details', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle"));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_ac_api_url', __('ActiveCampaign API URL', 'essb'), __('Enter your ActiveCampaign API URL. To get API URL please go to your ActiveCampaign Account >> My Settings >> Developer.', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_ac_api', __('ActiveCampaign API Key', 'essb'), __('Enter your ActiveCampaign API Key. To get API Key please go to your ActiveCampaign Account >> My Settings >> Developer.', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_ac_list', __('ActiveCapaign List ID', 'essb'), __('Entery your ActiveCampaign List ID. To get your list ID visit lists pages and copy ID that you see in browser when you open list ?listid=<yourid>.', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_ac_form', __('ActiveCapaign Form ID', 'essb'), __('	Optional subscription Form ID, to inherit those redirection settings. Example: 1001. This will allow you to mimic adding the contact through a subscription form, where you can take advantage of the redirection settings.', 'essb'));
ESSBOptionsStructureHelper::panel_end('optin', 'optin-1');
ESSBOptionsStructureHelper::holder_end('optin', 'optin-1');


ESSBOptionsStructureHelper::holder_start('optin', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-campaignmonitor');
ESSBOptionsStructureHelper::panel_start('optin', 'optin-1', __('CampaignMonitor', 'essb'), __('Configure mailing list service access details', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle"));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_cm_api', __('CampaignMonitor API Key', 'essb'), __('Enter your Campaign Monitor API Key. You can get your API Key from the Account Settings page when logged into your Campaign Monitor account.', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_cm_list', __('CampaignMonitor List ID', 'essb'), __('Enter your List ID. You can get List ID from the list editor page when logged into your Campaign Monitor account.', 'essb'));
ESSBOptionsStructureHelper::panel_end('optin', 'optin-1');
ESSBOptionsStructureHelper::holder_end('optin', 'optin-1');

ESSBOptionsStructureHelper::holder_start('optin', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-mymail');
ESSBOptionsStructureHelper::panel_start('optin', 'optin-1', __('Mailster', 'essb'), __('Configure mailing list service access details', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle"));
$listOfOptions = array();
if (function_exists('mailster')) {
	$lists = mailster('lists')->get();
	foreach ($lists as $list) {
		if (function_exists('mailster')) $id = $list->ID;
		else $id = $list->term_id;

		$listOfOptions[$id] = $list->name;
	}
}
ESSBOptionsStructureHelper::field_select('optin', 'optin-1', 'subscribe_mm_list', __('Mailster List', 'essb'), __('Select your list. Please ensure that Mailster plugin is installed.', 'essb'), $listOfOptions);
ESSBOptionsStructureHelper::field_switch_panel('optin', 'optin-1', 'subscribe_mm_double', __('Use pending state for new subscribers', 'essb'), __('Use this to setup Pending state of all your new subscribers and manually review at later.', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('optin', 'optin-1');
ESSBOptionsStructureHelper::holder_end('optin', 'optin-1');


ESSBOptionsStructureHelper::holder_start('optin', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-mailpoet');
ESSBOptionsStructureHelper::panel_start('optin', 'optin-1', __('MailPoet', 'essb'), __('Configure mailing list service access details', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle"));
$listOfOptions = array();
try {
	if (class_exists('WYSIJA')) {
		$model_list = WYSIJA::get('list', 'model');
		$mailpoet_lists = $model_list->get(array('name', 'list_id'), array('is_enabled'=>1));
		if (sizeof($mailpoet_lists) > 0) {
			foreach ($mailpoet_lists as $list) {
				$listOfOptions[$list['list_id']] = $list['name'];
			}
		}
	}
	if (class_exists('\MailPoet\API\API')) {
		$subscription_lists = \MailPoet\API\API::MP('v1')->getLists();
		if (is_array($subscription_lists)) {
			foreach ($subscription_lists as $list) {
				$listOfOptions[$list['id']] = $list['name'];
			}
		}
	}
}
catch (Exception $e) {

}

ESSBOptionsStructureHelper::field_select('optin', 'optin-1', 'subscribe_mp_list', __('MailPoet List', 'essb'), __('Select your list. Please ensure that MailPoet plugin is installed.', 'essb'), $listOfOptions);
ESSBOptionsStructureHelper::panel_end('optin', 'optin-1');
ESSBOptionsStructureHelper::holder_end('optin', 'optin-1');

ESSBOptionsStructureHelper::holder_start('optin', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-sendinblue');
ESSBOptionsStructureHelper::panel_start('optin', 'optin-1', __('SendinBlue', 'essb'), __('SendinBlue mailing list service access details', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle"));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_sib_api', __('SendinBlue API Key', 'essb'), __('Enter your SendinBlue API Key. You can get your API Key from <a href="https://my.sendinblue.com/advanced/apikey" target="_blank">here</a> (API key version 2.0).', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_sib_list', __('SendinBlue List ID', 'essb'), __('Enter your list ID.', 'essb'));
ESSBOptionsStructureHelper::panel_end('optin', 'optin-1');
ESSBOptionsStructureHelper::holder_end('optin', 'optin-1');


ESSBOptionsStructureHelper::holder_start('optin', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-madmimi');
ESSBOptionsStructureHelper::panel_start('optin', 'optin-1', __('Mad Mimi', 'essb'), __('Mad Mimi mailing list service access details', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle"));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_madmimi_login', __('Mad Mimi Username/Email', 'essb'), __('Enter your username or e-mail address using to access the system', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_madmimi_api', __('Mad Mimi API Key', 'essb'), __('Enter your Mad Mimi API Key. You can get your API Key <a href="https://madmimi.com/user/edit?account_info_tabs=account_info_personal" target="_blank">here</a>.', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_madmimi_list', __('Mad Mimi List ID', 'essb'), __('Enter your list ID.', 'essb'));
ESSBOptionsStructureHelper::panel_end('optin', 'optin-1');
ESSBOptionsStructureHelper::holder_end('optin', 'optin-1');

ESSBOptionsStructureHelper::holder_start('optin', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-conversio');
ESSBOptionsStructureHelper::panel_start('optin', 'optin-1', __('Conversio', 'essb'), __('Configure mailing list service access details', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle"));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_conv_api', __('Conversio API key', 'essb'), __('Enter your API access key', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_conv_list', __('Conversio List ID', 'essb'), __('Enter your list ID (not list name but the unique ID of the list)', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', 'subscribe_conv_text', __('Optional opt-in text', 'essb'), __('What opt-in text was shown to the subscriber. This is required for GDPR compliance.', 'essb'));
ESSBOptionsStructureHelper::field_component('optin', 'optin-1', 'essb5_conversio_lists_locate', 'true');
ESSBOptionsStructureHelper::panel_end('optin', 'optin-1');
ESSBOptionsStructureHelper::holder_end('optin', 'optin-1');

$custom_connectors = array();
$custom_connectors_options = array();

if (has_filter('essb_external_subscribe_connectors')) {
	$custom_connectors = apply_filters('essb_external_subscribe_connectors', $custom_connectors);
}
if (has_filter('essb_external_subscribe_connectors_options')) {
	$custom_connectors_options = apply_filters('essb_external_subscribe_connectors_options', $custom_connectors_options);
}

foreach ($custom_connectors as $connector => $service_name) {
	if (isset($custom_connectors_options[$connector])) {
		ESSBOptionsStructureHelper::holder_start('optin', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-'.$connector);
		ESSBOptionsStructureHelper::panel_start('optin', 'optin-1', $service_name, __('Configure mailing list service access details', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle"));

		foreach ($custom_connectors_options[$connector] as $field => $settings) {
			$type = isset($settings['type']) ? $settings['type'] : 'text';
			$title = isset($settings['title']) ? $settings['title'] : '';
			$desc = isset($settings['desc']) ? $settings['desc'] : '';
			$values = isset($settings['values']) ? $settings['values'] : array();

			if ($type == 'text') {
				ESSBOptionsStructureHelper::field_textbox_stretched('optin', 'optin-1', $field, $title, $desc);
			}
			if ($type == 'switch') {
				ESSBOptionsStructureHelper::field_switch('optin', 'optin-1', $field, $title, $desc, '', __('Yes', 'essb'), __('No', 'essb'));
			}

			if ($type == 'select') {
				ESSBOptionsStructureHelper::field_select('optin', 'optin-1', $field, $title, $desc, $values);
			}
		}

		ESSBOptionsStructureHelper::panel_end('optin', 'optin-1');
		ESSBOptionsStructureHelper::holder_end('optin', 'optin-1');

	}
}

function essb5_create_design_preview_button($design = '') {
	$preview_url = add_query_arg(array('subscribe-preview' => 'true', 'design' => $design ), trailingslashit(home_url()));
	$custom_buttons = '<a href="'.esc_url($preview_url).'" target="_blank" class="essb-btn tile-orange ao-form-preview"><i class="fa fa-eye"></i>'.__('Preview', 'essb').'</a>';

	return $custom_buttons;
}

function essb5_add_subscribe_design_button() {
	echo '<div class="row" style="padding: 10px;">';
	echo '<a href="#" class="ao-new-subscribe-design ao-form-userdesign" data-design="new" data-title="Create new form design"><span class="essb_icon fa fa-plus-square"></span><span>'.__('Add new subscribe form design', 'essb').'</span></a>';
	echo '</div>';

	$all_designs = essb5_get_form_designs();

	$count = 0;
	foreach ($all_designs as $design) {
		$name = isset($design['name']) ? $design['name'] : 'Untitled Form';

		$preview_url = add_query_arg(array('subscribe-preview' => 'true', 'design' => 'userdesign-'.$count ), trailingslashit(home_url()));

		$custom_buttons = '<a href="#" class="essb-btn tile-config ao-form-userdesign" data-design="design-'.$count.'" data-title="Manage Existing Design"><i class="fa fa-cog"></i>'.__('Edit', 'essb').'</a>';
		$custom_buttons .= '<a href="#" class="essb-btn tile-deactivate ao-form-removeuserdesign" data-design="design-'.$count.'" data-title="Manage Existing Design"><i class="fa fa-close"></i>'.__('Remove', 'essb').'</a>';
		$custom_buttons .= '<a href="'.esc_url($preview_url).'" target="_blank" class="essb-btn tile-orange ao-form-preview" data-design="userdesign-'.$count.'" data-title="Manage Existing Design"><i class="fa fa-eye"></i>'.__('Preview', 'essb').'</a>';

		$options_load = array();
		$options_load['title'] = $name;
		$options_load['description'] = 'The form unique class is <code><b>.essb-custom-userdesign-'.$count.'</b></code>. You can use this class to write additional custom form styles.';
		$options_load['button_center'] = 'true';
		$options_load['tag'] = 'user';
		$options_load['custom_buttons'] = $custom_buttons;

		essb5_advanced_options_small_settings_tile(array('element_options' => $options_load));

		$count++;
	}
}

function essb5_conversio_lists_locate() {
	echo '<a href="#" class="ao-options-btn get-conversio-lists"><span class="essb_icon fa fa-refresh"></span><span>'.__('Choose List', 'essb').'</span></a>';
	// Conversio API docs: http://api-docs.conversio.com/#get-customer-lists
	// test API Key: 3f138995c963057676278e1148ce94794263c389
	?>
<script type="text/javascript">
jQuery(document).ready(function($){
	$('.get-conversio-lists').click(function(e) {
		e.preventDefault();

		var apiKey = $('#essb_options_subscribe_conv_api').val(),
				callbackToken = $('#essb_advancedoptions_token').val();
		if (apiKey == '') {
			$.toast({
			    heading: 'API key is not provided',
			    text: '',
			    showHideTransition: 'fade',
			    icon: 'error',
			    position: 'bottom-right',
			    hideAfter: 5000
			});

			return;
		}

		$.ajax({
            url: essb_advancedopts_ajaxurl  + '?action=essb_advanced_options&essb_advancedoptions_token='+callbackToken+'&cmd=conversio_lists&api='+apiKey,
            type: 'GET',
            dataType: 'json',
            contentType: 'application/json; charset=utf-8',
            success: function (result) {
               if (result) {
								 result = JSON.parse(result);
								 var output = [];
								 output.push('<select id="essb_options_subscribe_conv_list" type="text" name="essb_options[subscribe_conv_list]" class="input-element stretched ">');

								 for (var i=0;i<result.length;i++) {
									 output.push('<option value="'+result[i].id+'">'+result[i].title+'</option>');
								 }

								 output.push('</select>');
								 $('#essb_options_subscribe_conv_list').parent().html(output.join(''));
							 }
            },
            error: function (error) {
							$.toast({
									heading: 'Cannot get Conversio lists. Please verify the filled in API access key',
									text: '',
									showHideTransition: 'fade',
									icon: 'error',
									position: 'bottom-right',
									hideAfter: 5000
							});
            }
        });
	});
});
</script>
	<?php
}
