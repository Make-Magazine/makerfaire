<?php

if (essb_option_bool_value('deactivate_module_subscribe')) {
	return;
}

if (class_exists('ESSBControlCenter')) {
	ESSBControlCenter::register_sidebar_section_menu('subscribe', 'optin-1', esc_html__('Setup', 'essb'));
	ESSBControlCenter::register_sidebar_section_menu('subscribe', 'optin-14', esc_html__('Forms Below Content', 'essb'));
	ESSBControlCenter::register_sidebar_section_menu('subscribe', 'optin-11', esc_html__('Booster Pop-up Forms', 'essb'));
	ESSBControlCenter::register_sidebar_section_menu('subscribe', 'optin-12', esc_html__('Fly-out Forms', 'essb'));
	ESSBControlCenter::register_sidebar_section_menu('subscribe', 'optin-15', esc_html__('Subscribe Locker', 'essb'));
	ESSBControlCenter::register_sidebar_section_menu('subscribe', 'optin', esc_html__('Customize Form Designs', 'essb'));
}



ESSBOptionsStructureHelper::menu_item('subscribe', 'optin-1', esc_html__('Mailing List Platforms', 'essb'), ' ti-email');
ESSBOptionsStructureHelper::menu_item('subscribe', 'optin-14', esc_html__('Subscribe forms below content', 'essb'), ' ti-layout-media-overlay');
ESSBOptionsStructureHelper::menu_item('subscribe', 'optin-11', esc_html__('Subscribers Booster', 'essb'), ' ti-rocket');
ESSBOptionsStructureHelper::menu_item('subscribe', 'optin-12', esc_html__('Subscribers Flyout', 'essb'), ' ti-layout-media-center-alt');

ESSBOptionsStructureHelper::menu_item('subscribe', 'optin', esc_html__('Customize Form Designs', 'essb'), ' ti-ruler-pencil');

$active_d1 = essb5_has_setting_values(array('subscribe_mc_title', 'subscribe_mc_text', 'subscribe_mc_name', 'subscribe_mc_email', 'subscribe_mc_button', 'subscribe_mc_footer', 'subscribe_mc_success', 'subscribe_mc_error', 'customizer_subscribe_bgcolor1', 'customizer_subscribe_textcolor1', 'customizer_subscribe_hovercolor1', 'customizer_subscribe_hovertextcolor1', 'customizer_subscribe_emailcolor1'));
$active_d2 = essb5_has_setting_values(array('subscribe_mc_title2', 'subscribe_mc_text2', 'subscribe_mc_name2', 'subscribe_mc_email2', 'subscribe_mc_button2', 'subscribe_mc_footer2', 'subscribe_mc_success2', 'subscribe_mc_error2', 'customizer_subscribe_bgcolor2', 'customizer_subscribe_textcolor2', 'customizer_subscribe_hovercolor2', 'customizer_subscribe_hovertextcolor2', 'customizer_subscribe_emailcolor2'));
$active_d3 = essb5_has_setting_values(array('subscribe_mc_title3', 'subscribe_mc_text3', 'subscribe_mc_name3', 'subscribe_mc_email3', 'subscribe_mc_button3', 'subscribe_mc_footer3', 'subscribe_mc_success3', 'subscribe_mc_error3', 'customizer_subscribe_bgcolor3', 'customizer_subscribe_textcolor3', 'customizer_subscribe_hovercolor3', 'customizer_subscribe_hovertextcolor3', 'customizer_subscribe_emailcolor3'));
$active_d4 = essb5_has_setting_values(array('subscribe_mc_title4', 'subscribe_mc_text4', 'subscribe_mc_name4', 'subscribe_mc_email4', 'subscribe_mc_button4', 'subscribe_mc_footer4', 'subscribe_mc_success4', 'subscribe_mc_error4', 'customizer_subscribe_bgcolor4', 'customizer_subscribe_textcolor4', 'customizer_subscribe_hovercolor4', 'customizer_subscribe_hovertextcolor4', 'customizer_subscribe_emailcolor4'));
$active_d5 = essb5_has_setting_values(array('subscribe_mc_title5', 'subscribe_mc_text5', 'subscribe_mc_name5', 'subscribe_mc_email5', 'subscribe_mc_button5', 'subscribe_mc_footer5', 'subscribe_mc_success5', 'subscribe_mc_error5', 'customizer_subscribe_bgcolor5', 'customizer_subscribe_textcolor5', 'customizer_subscribe_hovercolor5', 'customizer_subscribe_hovertextcolor5', 'customizer_subscribe_emailcolor5'));
$active_d6 = essb5_has_setting_values(array('subscribe_mc_title6', 'subscribe_mc_text6', 'subscribe_mc_name6', 'subscribe_mc_email6', 'subscribe_mc_button6', 'subscribe_mc_footer6', 'subscribe_mc_success6', 'subscribe_mc_error6', 'customizer_subscribe_bgcolor6', 'customizer_subscribe_textcolor6', 'customizer_subscribe_hovercolor6', 'customizer_subscribe_hovertextcolor6', 'customizer_subscribe_emailcolor6'));
$active_d7 = essb5_has_setting_values(array('subscribe_mc_title7', 'subscribe_mc_text7', 'subscribe_mc_name7', 'subscribe_mc_email7', 'subscribe_mc_button7', 'subscribe_mc_footer7', 'subscribe_mc_success7', 'subscribe_mc_error7', 'customizer_subscribe_bgcolor7', 'customizer_subscribe_textcolor7', 'customizer_subscribe_hovercolor7', 'customizer_subscribe_hovertextcolor7', 'customizer_subscribe_emailcolor7'));
$active_d8 = essb5_has_setting_values(array('subscribe_mc_title8', 'subscribe_mc_text8', 'subscribe_mc_name8', 'subscribe_mc_email8', 'subscribe_mc_button8', 'subscribe_mc_footer8', 'subscribe_mc_success8', 'subscribe_mc_error8', 'customizer_subscribe_bgcolor8', 'customizer_subscribe_textcolor8', 'customizer_subscribe_hovercolor8', 'customizer_subscribe_hovertextcolor8', 'customizer_subscribe_emailcolor8'));
$active_d9 = essb5_has_setting_values(array('subscribe_mc_title9', 'subscribe_mc_text9', 'subscribe_mc_name9', 'subscribe_mc_email9', 'subscribe_mc_button9', 'subscribe_mc_footer9', 'subscribe_mc_success9', 'subscribe_mc_error9', 'customizer_subscribe_bgcolor9', 'customizer_subscribe_textcolor9', 'customizer_subscribe_hovercolor9', 'customizer_subscribe_hovertextcolor9', 'customizer_subscribe_emailcolor9'));

ESSBOptionsStructureHelper::help('subscribe', 'optin', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/customize-existing-form-designs-or-build-your-own/'));

ESSBOptionsStructureHelper::field_heading('subscribe', 'optin', 'heading7', esc_html__('Customize integrated form designs', 'essb'), '', 'pb0', '<i class="fa Example of pencil-square-o fa-pencil-square-o"></i>');
ESSBOptionsStructureHelper::holder_start('subscribe', 'optin', 'essb-related-heading7', '');
ESSBOptionsStructureHelper::hint('subscribe', 'optin', '', esc_html__('Modify the integrated inside plugin designs from #1 to #9. The save of the options will reload the screen. Do not forget to press the Update Options button in case you have unsaved changes done.', 'essb'), '', 'glowhint');

essb5_menu_advanced_options_small_tile('subscribe', 'optin', esc_html__('Customize Design #1', 'essb'), '', esc_html__('The customize function provide a set of options that you can use to change the form displayed texts. You can also change the default form colors too.', 'essb'), '', 'true', '', 'subscribe-design1', esc_html__('Start Form Customizer', 'essb'), esc_html__('Customize the texts and colors of Design #1', 'essb'), ($active_d1 ? 'Customized' : ''), essb5_create_design_preview_button('design1'));
essb5_menu_advanced_options_small_tile('subscribe', 'optin', esc_html__('Customize Design #2', 'essb'), '', esc_html__('The customize function provide a set of options that you can use to change the form displayed texts. You can also change the default form colors too.', 'essb'), '', 'true', '', 'subscribe-design2', esc_html__('Start Form Customizer', 'essb'), esc_html__('Customize the texts and colors of Design #2', 'essb'), ($active_d2 ? 'Customized' : ''), essb5_create_design_preview_button('design2'));
essb5_menu_advanced_options_small_tile('subscribe', 'optin', esc_html__('Customize Design #3', 'essb'), '', esc_html__('The customize function provide a set of options that you can use to change the form displayed texts. You can also change the default form colors too.', 'essb'), '', 'true', '', 'subscribe-design3', esc_html__('Start Form Customizer', 'essb'), esc_html__('Customize the texts and colors of Design #3', 'essb'), ($active_d3 ? 'Customized' : ''), essb5_create_design_preview_button('design3'));
essb5_menu_advanced_options_small_tile('subscribe', 'optin', esc_html__('Customize Design #4', 'essb'), '', esc_html__('The customize function provide a set of options that you can use to change the form displayed texts. You can also change the default form colors too.', 'essb'), '', 'true', '', 'subscribe-design4', esc_html__('Start Form Customizer', 'essb'), esc_html__('Customize the texts and colors of Design #4', 'essb'), ($active_d4 ? 'Customized' : ''), essb5_create_design_preview_button('design4'));
essb5_menu_advanced_options_small_tile('subscribe', 'optin', esc_html__('Customize Design #5', 'essb'), '', esc_html__('The customize function provide a set of options that you can use to change the form displayed texts. You can also change the default form colors too.', 'essb'), '', 'true', '', 'subscribe-design5', esc_html__('Start Form Customizer', 'essb'), esc_html__('Customize the texts and colors of Design #5', 'essb'), ($active_d5 ? 'Customized' : ''), essb5_create_design_preview_button('design5'));
essb5_menu_advanced_options_small_tile('subscribe', 'optin', esc_html__('Customize Design #6', 'essb'), '', esc_html__('The customize function provide a set of options that you can use to change the form displayed texts. You can also change the default form colors too.', 'essb'), '', 'true', '', 'subscribe-design6', esc_html__('Start Form Customizer', 'essb'), esc_html__('Customize the texts and colors of Design #6', 'essb'), ($active_d6 ? 'Customized' : ''), essb5_create_design_preview_button('design6'));
essb5_menu_advanced_options_small_tile('subscribe', 'optin', esc_html__('Customize Design #7', 'essb'), '', esc_html__('The customize function provide a set of options that you can use to change the form displayed texts. You can also change the default form colors too.', 'essb'), '', 'true', '', 'subscribe-design7', esc_html__('Start Form Customizer', 'essb'), esc_html__('Customize the texts and colors of Design #7', 'essb'), ($active_d7 ? 'Customized' : ''), essb5_create_design_preview_button('design7'));
essb5_menu_advanced_options_small_tile('subscribe', 'optin', esc_html__('Customize Design #8', 'essb'), '', esc_html__('The customize function provide a set of options that you can use to change the form displayed texts. You can also change the default form colors too.', 'essb'), '', 'true', '', 'subscribe-design8', esc_html__('Start Form Customizer', 'essb'), esc_html__('Customize the texts and colors of Design #8', 'essb'), ($active_d8 ? 'Customized' : ''), essb5_create_design_preview_button('design8'));
essb5_menu_advanced_options_small_tile('subscribe', 'optin', esc_html__('Customize Design #9', 'essb'), '', esc_html__('The customize function provide a set of options that you can use to change the form displayed texts. You can also change the default form colors too.', 'essb'), '', 'true', '', 'subscribe-design9', esc_html__('Start Form Customizer', 'essb'), esc_html__('Customize the texts and colors of Design #9', 'essb'), ($active_d9 ? 'Customized' : ''), essb5_create_design_preview_button('design9'));
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin');

ESSBOptionsStructureHelper::field_heading('subscribe', 'optin', 'heading7', esc_html__('Own Designs', 'essb'), '', 'mt40 pb0', '<i class="fa fa-plus-square-o"></i>');
ESSBOptionsStructureHelper::holder_start('subscribe', 'optin', 'essb-related-heading7', '');
ESSBOptionsStructureHelper::hint('subscribe', 'optin', '', esc_html__('Add, remove or change created by user form designs. Those form designs you can use anywhere inside plugin where subscribe forms are present.', 'essb'), '', 'glowhint');
ESSBOptionsStructureHelper::field_component('subscribe', 'optin', 'essb5_add_subscribe_design_button');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin');

// --> Main settings for connectors
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
		"conversio" => "Conversio",
        'fluentcrm' => 'FluentCRM',
        'acelle' => 'Acelle - Email Marketing'
);

if (has_filter('essb_external_subscribe_connectors')) {
	$optin_connectors = apply_filters('essb_external_subscribe_connectors', $optin_connectors);
}
ESSBOptionsStructureHelper::help('subscribe', 'optin-14', '', '', array('General Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/automatically-add-subscribe-forms-below-content/'));

ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-14', esc_html__('Enable forms below content', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'optin_content_activate', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-14', 'essb3_of|of_posts', esc_html__('Display on posts', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-14', 'essb3_of|of_pages', esc_html__('Display on pages', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-14', 'essb3_of|of_design', esc_html__('Use followin template', 'essb'), '', essb_optin_designs());
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-14', 'essb3_of|of_deactivate_mobile', esc_html__('Don\'t show on mobile devices', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-14', 'essb3_of|of_exclude', esc_html__('Exclude display on', 'essb'), esc_html__('Exclude buttons on posts/pages with these IDs. Comma separated: "11, 15, 125". This will deactivate automated display of buttons on selected posts/pages but you are able to use shortcode on them.', 'essb'), '');
ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-14');

ESSBOptionsStructureHelper::help('subscribe', 'optin-11', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/automatically-add-pop-up-subscribe-forms-booster-forms/'));

ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-11', esc_html__('Enable booster pop-up forms', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'optin_booster_activate', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));

ESSBOptionsStructureHelper::field_heading('subscribe', 'optin-11', 'heading7', esc_html__('General', 'essb'), '', 'pb0', '<i class="ti-widget-alt"></i>');
ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-11', 'essb-related-heading7', '');
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-11', 'essb3_ofob|posttypes', esc_html__('Post types', 'essb'), esc_html__('You can choose the post types where pop-up forms will show. Leave blank for the entire website.', 'essb'), 'list_all_posttype', '', '', 'true');
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-11', 'essb3_ofob|deactivate_homepage', esc_html__('Deactivate display on the homepage', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-11', 'essb3_ofob|ofob_single', esc_html__('Appear once for user', 'essb'), esc_html__('Set a technical cookie to know that the user sees the form and does not show it again for a selected period of days.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-11', 'essb3_ofob|ofob_single_time', esc_html__('Do not show again period (days)', 'essb'), esc_html__('Overwrite the default value of 14 days for the do not show again cookie. Numeric value only.', 'essb'), '', 'input60', 'fa-calendar', 'right');
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-11', 'essb3_ofob|ofob_exclude', esc_html__('Do not show on', 'essb'), esc_html__('Exclude display of form on selected IDs of posts/pages or custom post types (comma separated). Example: 100, 200, 30', 'essb'), '');
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-11', 'essb3_ofob|ofob_deactivate_mobile', esc_html__('Don\'t show on mobile devices', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-11', 'essb3_ofob|ofob_deactivate_desktop', esc_html__('Don\'t show on desktop devices', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBControlCenter::set_extra_description('ofob_manual_mode', 'Shortcode: <code>[booster-subscribe-form]</code>');
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-11', 'essb3_ofob|ofob_manual_mode', esc_html__('Manual insertion mode', 'essb'), 'With manual mode, you can show the pop-up form using a shortcode. Just place that shortcode anywhere in the content and form will work.', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-11');
ESSBControlCenter::relation_enabled('optin-11', 'ofob_single', array('ofob_single_time'));

ESSBOptionsStructureHelper::field_heading('subscribe', 'optin-11', 'heading7', esc_html__('Display after amount of seconds', 'essb'), '', 'mt40 pb0', '<i class="fa fa-clock-o"></i>');
ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-11', 'essb-related-heading7', '');
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-11', 'essb3_ofob|ofob_time', esc_html__('Activate', 'essb'), esc_html__('Set this option to Yes to use this event', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-11', 'essb3_ofob|ofob_time_delay', esc_html__('Time delay', 'essb'), esc_html__('Set 1 in the field for immediate display or amount of seconds to delay. If the field is blank the form won\'t appear with this condition.', 'essb'), '', 'input60', 'fa-clock-o', 'right');
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-11', 'essb3_ofob|of_time_design', esc_html__('Choose design for that event', 'essb'), esc_html__('Choose design which will be used for that event. You can have different design on each event', 'essb'), essb_optin_designs());
ESSBOptionsStructureHelper::field_color('subscribe', 'optin-11', 'essb3_ofob|of_time_bgcolor', esc_html__('Overlay background color', 'essb'), esc_html__('Change overlay background color that will be used for that event. You may need to replace the color if you customize design of chosen template.', 'essb'), '', 'true');
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-11', 'essb3_ofob|of_time_close', esc_html__('Choose close type', 'essb'), esc_html__('Choose how you wish to close the pop up form - with close icon or text link.', 'essb'), array("icon" => "Close Icon", "text" => "Text close link"));
ESSBOptionsStructureHelper::field_color('subscribe', 'optin-11', 'essb3_ofob|of_time_closecolor', esc_html__('Close action color', 'essb'), esc_html__('Customize close action color in case you change overlay color. Otherwise you can leave the default', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-11', 'essb3_ofob|of_time_closetext', esc_html__('Custom close text', 'essb'), esc_html__('Enter custom close text when you choose text mode of close function.', 'essb'), '');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-11');
ESSBControlCenter::relation_enabled('optin-11', 'ofob_time', array('ofob_time_delay', 'of_time_design', 'of_time_bgcolor', 'of_time_close', 'of_time_closecolor', 'of_time_closetext'));

ESSBOptionsStructureHelper::field_heading('subscribe', 'optin-11', 'heading7', esc_html__('Display on scroll', 'essb'), '', 'mt40 pb0', '<i class="fa fa-arrow-down"></i>');
ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-11', 'essb-related-heading7', '');
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-11', 'essb3_ofob|ofob_scroll', esc_html__('Activate', 'essb'), esc_html__('Set this option to Yes to use this event', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-11', 'essb3_ofob|ofob_scroll_percent', esc_html__('Percent of content', 'essb'), esc_html__('Use numeric value without symbols. Exmaple: 40', 'essb'), '', 'input60', 'fa-long-arrow-down', 'right');
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-11', 'essb3_ofob|of_scroll_design', esc_html__('Choose design for that event', 'essb'), esc_html__('Choose design which will be used for that event. You can have different design on each event', 'essb'), essb_optin_designs());
ESSBOptionsStructureHelper::field_color('subscribe', 'optin-11', 'essb3_ofob|of_scroll_bgcolor', esc_html__('Overlay background color', 'essb'), esc_html__('Change overlay background color that will be used for that event. You may need to replace the color if you customize design of chosen template.', 'essb'), '', 'true');
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-11', 'essb3_ofob|of_scroll_close', esc_html__('Choose close type', 'essb'), esc_html__('Choose how you wish to close the pop up form - with close icon or text link.', 'essb'), array("icon" => "Close Icon", "text" => "Text close link"));
ESSBOptionsStructureHelper::field_color('subscribe', 'optin-11', 'essb3_ofob|of_scroll_closecolor', esc_html__('Close action color', 'essb'), esc_html__('Customize close action color in case you change overlay color. Otherwise you can leave the default', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-11', 'essb3_ofob|of_scroll_closetext', esc_html__('Custom close text', 'essb'), esc_html__('Enter custom close text when you choose text mode of close function.', 'essb'), '');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-11');
ESSBControlCenter::relation_enabled('optin-11', 'ofob_scroll', array('ofob_scroll_percent', 'of_scroll_design', 'of_scroll_bgcolor', 'of_scroll_close', 'of_scroll_closecolor', 'of_scroll_closetext'));

ESSBOptionsStructureHelper::field_heading('subscribe', 'optin-11', 'heading7', esc_html__('Display on exit intent', 'essb'), '', 'mt40 pb0', '<i class="fa fa-external-link-square"></i>');
ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-11', 'essb-related-heading7', '');
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-11', 'essb3_ofob|ofob_exit', esc_html__('Activate', 'essb'), esc_html__('Set this option to Yes to use this event', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-11', 'essb3_ofob|of_exit_design', esc_html__('Choose design for that event', 'essb'), esc_html__('Choose design which will be used for that event. You can have different design on each event', 'essb'), essb_optin_designs());
ESSBOptionsStructureHelper::field_color('subscribe', 'optin-11', 'essb3_ofob|of_exit_bgcolor', esc_html__('Overlay background color', 'essb'), esc_html__('Change overlay background color that will be used for that event. You may need to replace the color if you customize design of chosen template.', 'essb'), '', 'true');
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-11', 'essb3_ofob|of_exit_close', esc_html__('Choose close type', 'essb'), esc_html__('Choose how you wish to close the pop up form - with close icon or text link.', 'essb'), array("icon" => "Close Icon", "text" => "Text close link"));
ESSBOptionsStructureHelper::field_color('subscribe', 'optin-11', 'essb3_ofob|of_exit_closecolor', esc_html__('Close action color', 'essb'), esc_html__('Customize close action color in case you change overlay color. Otherwise you can leave the default', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-11', 'essb3_ofob|of_exit_closetext', esc_html__('Custom close text', 'essb'), esc_html__('Enter custom close text when you choose text mode of close function.', 'essb'), '');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-11');
ESSBControlCenter::relation_enabled('optin-11', 'ofob_exit', array('of_exit_design', 'of_exit_bgcolor', 'of_exit_close', 'of_exit_closecolor', 'of_exit_closetext'));

ESSBOptionsStructureHelper::field_heading('subscribe', 'optin-11', 'heading7', esc_html__('Manual display', 'essb'), '', 'mt40 pb0', '<i class="fa fa-code"></i>');
ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-11', 'essb-related-heading7', '');
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-11', 'essb3_ofob|ofob_manual', esc_html__('Activate', 'essb'), esc_html__('Set this option to Yes to use this event', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-11', 'essb3_ofob|of_manual_design', esc_html__('Choose design for that event', 'essb'), esc_html__('Choose design which will be used for that event. You can have different design on each event', 'essb'), essb_optin_designs());
ESSBOptionsStructureHelper::field_color('subscribe', 'optin-11', 'essb3_ofob|of_manual_bgcolor', esc_html__('Overlay background color', 'essb'), esc_html__('Change overlay background color that will be used for that event. You may need to replace the color if you customize design of chosen template.', 'essb'), '', 'true');
ESSBControlCenter::set_description_inline('of_manual_selector');
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-11', 'essb3_ofob|of_manual_selector', esc_html__('Open form selector (class, ID)', 'essb'), esc_html__('Enter custom class or ID which will start the form when clicked. You can also start the form with the following javascript function call: essb_manualform_show();', 'essb'), '');
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-11', 'essb3_ofob|of_manual_close', esc_html__('Choose close type', 'essb'), esc_html__('Choose how you wish to close the pop up form - with close icon or text link.', 'essb'), array("icon" => "Close Icon", "text" => "Text close link"));
ESSBOptionsStructureHelper::field_color('subscribe', 'optin-11', 'essb3_ofob|of_manual_closecolor', esc_html__('Close action color', 'essb'), esc_html__('Customize close action color in case you change overlay color. Otherwise you can leave the default', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-11', 'essb3_ofob|of_manual_closetext', esc_html__('Custom close text', 'essb'), esc_html__('Enter custom close text when you choose text mode of close function.', 'essb'), '');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-11');
ESSBControlCenter::relation_enabled('optin-11', 'ofob_manual', array('of_manual_design', 'of_manual_bgcolor', 'of_manual_selector', 'of_manual_close', 'of_manual_closecolor', 'of_manual_closetext'));


ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-11');

ESSBOptionsStructureHelper::help('subscribe', 'optin-12', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/automatically-display-fly-out-subscribe-forms/'));
ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-12', esc_html__('Enable Fly-out Subscribe Forms', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'optin_flyout_activate', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));


ESSBOptionsStructureHelper::field_heading('subscribe', 'optin-12', 'heading7', esc_html__('General', 'essb'), '', 'pb0', '<i class="ti-widget-alt"></i>');
ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-12', 'essb-related-heading7', '');
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-12', 'essb3_ofof|posttypes', esc_html__('Post types', 'essb'), esc_html__('You can choose the post types where pop-up forms will show. Leave blank for the entire website.', 'essb'), 'list_all_posttype', '', '', 'true');
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-12', 'essb3_ofof|of_deactivate_homepage', esc_html__('Deactivate display on homepage', 'essb'), esc_html__('Exclude display of function on home page of your site.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-12', 'essb3_ofof|ofof_single', esc_html__('Appear once for user', 'essb'), esc_html__('Activate this option if you wish to make event appear only once for user in the next 14 days. ', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-12', 'essb3_ofof|ofof_position', esc_html__('Appear at', 'essb'), esc_html__('Choose position where the fly out will appear', 'essb'), array("bottom-right" => esc_html__('Bottom Right', 'essb'), "bottom-left" => esc_html__('Bottom Left', 'essb'), "top-right" => esc_html__('Top Right', 'essb'), "top-left" => esc_html__('Top Left', 'essb')));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-12', 'essb3_ofof|ofof_exclude', esc_html__('Exclude display on', 'essb'), esc_html__('Exclude buttons on posts/pages with these IDs. Comma separated: "11, 15, 125". This will deactivate automated display of buttons on selected posts/pages but you are able to use shortcode on them.', 'essb'), '');
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-12', 'essb3_ofof|ofof_deactivate_mobile', esc_html__('Don\'t show on mobile devices', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-12');


ESSBOptionsStructureHelper::field_heading('subscribe', 'optin-12', 'heading7', esc_html__('Display after amount of seconds', 'essb'), '', 'mt40 pb0', '<i class="fa fa-clock-o"></i>');
ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-12', 'essb-related-heading7', '');	
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-12', 'essb3_ofof|ofof_time', esc_html__('Activate', 'essb'), esc_html__('Set this option to Yes to use this event', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-12', 'essb3_ofof|ofof_time_delay', esc_html__('Display after seconds', 'essb'), esc_html__('If you wish to display it immediately after load use 1 as value. Otherwise provide value of seconds you wish to use. Blank field will avoid display of opt-in form', 'essb'), '', 'input60', 'fa-clock-o', 'right');
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-12', 'essb3_ofof|of_time_design', esc_html__('Choose design for that event', 'essb'), esc_html__('Choose design which will be used for that event. You can have different design on each event', 'essb'), essb_optin_designs());
ESSBOptionsStructureHelper::field_color('subscribe', 'optin-12', 'essb3_ofof|of_time_bgcolor', esc_html__('Overlay background color', 'essb'), esc_html__('Change overlay background color that will be used for that event. You may need to replace the color if you customize design of chosen template.', 'essb'), '', 'true');
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-12', 'essb3_ofof|of_time_close', esc_html__('Choose close type', 'essb'), esc_html__('Choose how you wish to close the pop up form - with close icon or text link.', 'essb'), array("icon" => "Close Icon", "text" => "Text close link"));
ESSBOptionsStructureHelper::field_color('subscribe', 'optin-12', 'essb3_ofof|of_time_closecolor', esc_html__('Close action color', 'essb'), esc_html__('Customize close action color in case you change overlay color. Otherwise you can leave the default', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-12', 'essb3_ofof|of_time_closetext', esc_html__('Custom close text', 'essb'), esc_html__('Enter custom close text when you choose text mode of close function.', 'essb'), '');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-12');
ESSBControlCenter::relation_enabled('optin-12', 'ofof_time', array('ofof_time_delay', 'of_time_design', 'of_time_bgcolor', 'of_time_close', 'of_time_closecolor', 'of_time_closetext'));
	
ESSBOptionsStructureHelper::field_heading('subscribe', 'optin-12', 'heading7', esc_html__('Display on scroll', 'essb'), '', 'mt40 pb0', '<i class="fa fa-arrow-down"></i>');
ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-12', 'essb-related-heading7', '');
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-12', 'essb3_ofof|ofof_scroll', esc_html__('Activate', 'essb'), esc_html__('Set this option to Yes to use this event', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-12', 'essb3_ofof|ofof_scroll_percent', esc_html__('Percent of content', 'essb'), esc_html__('Use numeric value without symbols. Exmaple: 40', 'essb'), '', 'input60', 'fa-long-arrow-down', 'right');
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-12', 'essb3_ofof|of_scroll_design', esc_html__('Choose design for that event', 'essb'), esc_html__('Choose design which will be used for that event. You can have different design on each event', 'essb'), essb_optin_designs());
ESSBOptionsStructureHelper::field_color('subscribe', 'optin-12', 'essb3_ofof|of_scroll_bgcolor', esc_html__('Overlay background color', 'essb'), esc_html__('Change overlay background color that will be used for that event. You may need to replace the color if you customize design of chosen template.', 'essb'), '', 'true');
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-12', 'essb3_ofof|of_scroll_close', esc_html__('Choose close type', 'essb'), esc_html__('Choose how you wish to close the pop up form - with close icon or text link.', 'essb'), array("icon" => "Close Icon", "text" => "Text close link"));
ESSBOptionsStructureHelper::field_color('subscribe', 'optin-12', 'essb3_ofof|of_scroll_closecolor', esc_html__('Close action color', 'essb'), esc_html__('Customize close action color in case you change overlay color. Otherwise you can leave the default', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-12', 'essb3_ofof|of_scroll_closetext', esc_html__('Custom close text', 'essb'), esc_html__('Enter custom close text when you choose text mode of close function.', 'essb'), '');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-12');
ESSBControlCenter::relation_enabled('optin-12', 'ofof_scroll', array('ofof_scroll_percent', 'of_scroll_design', 'of_scroll_bgcolor', 'of_scroll_close', 'of_scroll_closecolor', 'of_scroll_closetext'));

ESSBOptionsStructureHelper::field_heading('subscribe', 'optin-12', 'heading7', esc_html__('Display on exit intent', 'essb'), '', 'mt40 pb0', '<i class="fa fa-external-link-square"></i>');
ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-12', 'essb-related-heading7', '');
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-12', 'essb3_ofof|ofof_exit', esc_html__('Activate', 'essb'), esc_html__('Set this option to Yes to use this event', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-12', 'essb3_ofof|of_exit_design', esc_html__('Choose design for that event', 'essb'), esc_html__('Choose design which will be used for that event. You can have different design on each event', 'essb'), essb_optin_designs());
ESSBOptionsStructureHelper::field_color('subscribe', 'optin-12', 'essb3_ofof|of_exit_bgcolor', esc_html__('Overlay background color', 'essb'), esc_html__('Change overlay background color that will be used for that event. You may need to replace the color if you customize design of chosen template.', 'essb'), '', 'true');
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-12', 'essb3_ofof|of_exit_close', esc_html__('Choose close type', 'essb'), esc_html__('Choose how you wish to close the pop up form - with close icon or text link.', 'essb'), array("icon" => "Close Icon", "text" => "Text close link"));
ESSBOptionsStructureHelper::field_color('subscribe', 'optin-12', 'essb3_ofof|of_exit_closecolor', esc_html__('Close action color', 'essb'), esc_html__('Customize close action color in case you change overlay color. Otherwise you can leave the default', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-12', 'essb3_ofof|of_exit_closetext', esc_html__('Custom close text', 'essb'), esc_html__('Enter custom close text when you choose text mode of close function.', 'essb'), '');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-12');
ESSBControlCenter::relation_enabled('optin-12', 'ofof_exit', array('of_exit_design', 'of_exit_bgcolor', 'of_exit_close', 'of_exit_closecolor', 'of_exit_closetext'));

ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-12');

/* Locker */
ESSBOptionsStructureHelper::help('subscribe', 'optin-15', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/working-with-subscribe-locker-and-setup/'));
ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-15', esc_html__('Enable subscribe locker', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'optin_locker_activate', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));

ESSBOptionsStructureHelper::field_heading('subscribe', 'optin-15', 'heading7', esc_html__('General', 'essb'), '', 'pb0', '<i class="ti-widget-alt"></i>');
ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-15', 'essb-related-heading7', '');
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-15', 'essb3_oflock|posttypes', esc_html__('Post types', 'essb'), esc_html__('You can choose the post types where pop-up forms will show. Leave blank for the entire website.', 'essb'), 'list_all_posttype', '', '', 'true');
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-15', 'essb3_oflock|deactivate_homepage', esc_html__('Deactivate display on the homepage', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-15', 'essb3_oflock|single_time', esc_html__('Unlock time (days)', 'essb'), esc_html__('Set custom unlock time. The default unlocks cookie expiration period is 90 days. Numeric value only.', 'essb'), '', 'input60', 'fa-calendar', 'right');
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-15', 'essb3_oflock|oflock_exclude', esc_html__('Do not show on', 'essb'), esc_html__('Exclude display of form on selected IDs of posts/pages or custom post types (comma separated). Example: 100, 200, 30', 'essb'), '');
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-15', 'essb3_oflock|oflock_deactivate_mobile', esc_html__('Don\'t show on mobile devices', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-15', 'essb3_oflock|oflock_allow_close', esc_html__('Allow continuing without subscribing', 'essb'), 'Enable if you give your visitors an option to close the form without doing a subscribe action.', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-15');

ESSBOptionsStructureHelper::field_heading('subscribe', 'optin-15', 'heading7', esc_html__('Display after amount of seconds', 'essb'), '', 'mt40 pb0', '<i class="fa fa-clock-o"></i>');
ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-15', 'essb-related-heading7', '');
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-15', 'essb3_oflock|oflock_time', esc_html__('Activate', 'essb'), esc_html__('Set this option to Yes to use this event', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-15', 'essb3_oflock|oflock_time_delay', esc_html__('Time delay', 'essb'), esc_html__('Set 1 in the field for immediate display or amount of seconds to delay. If the field is blank the form won\'t appear with this condition.', 'essb'), '', 'input60', 'fa-clock-o', 'right');
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-15', 'essb3_oflock|oflock_time_design', esc_html__('Choose design for that event', 'essb'), esc_html__('Choose design which will be used for that event. You can have different design on each event', 'essb'), essb_optin_designs());
ESSBOptionsStructureHelper::field_color('subscribe', 'optin-15', 'essb3_oflock|oflock_time_bgcolor', esc_html__('Overlay background color', 'essb'), esc_html__('Change overlay background color that will be used for that event. You may need to replace the color if you customize design of chosen template.', 'essb'), '', 'true');
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-15', 'essb3_oflock|oflock_time_close', esc_html__('Choose close type', 'essb'), esc_html__('Choose how you wish to close the pop up form - with close icon or text link. Only if "Allow continuing without subscribing" is enabled.', 'essb'), array("icon" => "Close Icon", "text" => "Text close link"));
ESSBOptionsStructureHelper::field_color('subscribe', 'optin-15', 'essb3_oflock|oflock_time_closecolor', esc_html__('Close action color', 'essb'), esc_html__('Customize close action color in case you change overlay color. Otherwise you can leave the default. Only if "Allow continuing without subscribing" is enabled.', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-15', 'essb3_oflock|oflock_time_closetext', esc_html__('Custom close text', 'essb'), esc_html__('Enter custom close text when you choose text mode of close function. Only if "Allow continuing without subscribing" is enabled.', 'essb'), '');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-15');
ESSBControlCenter::relation_enabled('optin-15', 'oflock_time', array('oflock_time_delay', 'oflock_time_design', 'oflock_time_bgcolor', 'oflock_time_close', 'oflock_time_closecolor', 'oflock_time_closetext'));

ESSBOptionsStructureHelper::field_heading('subscribe', 'optin-15', 'heading7', esc_html__('Display on scroll', 'essb'), '', 'mt40 pb0', '<i class="fa fa-arrow-down"></i>');
ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-15', 'essb-related-heading7', '');
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-15', 'essb3_oflock|oflock_scroll', esc_html__('Activate', 'essb'), esc_html__('Set this option to Yes to use this event', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-15', 'essb3_oflock|oflock_scroll_percent', esc_html__('Percent of content', 'essb'), esc_html__('Use numeric value without symbols. Exmaple: 40', 'essb'), '', 'input60', 'fa-long-arrow-down', 'right');
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-15', 'essb3_oflock|oflock_scroll_design', esc_html__('Choose design for that event', 'essb'), esc_html__('Choose design which will be used for that event. You can have different design on each event', 'essb'), essb_optin_designs());
ESSBOptionsStructureHelper::field_color('subscribe', 'optin-15', 'essb3_oflock|oflock_scroll_bgcolor', esc_html__('Overlay background color', 'essb'), esc_html__('Change overlay background color that will be used for that event. You may need to replace the color if you customize design of chosen template.', 'essb'), '', 'true');
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-15', 'essb3_oflock|oflock_scroll_close', esc_html__('Choose close type', 'essb'), esc_html__('Choose how you wish to close the pop up form - with close icon or text link. Only if "Allow continuing without subscribing" is enabled.', 'essb'), array("icon" => "Close Icon", "text" => "Text close link"));
ESSBOptionsStructureHelper::field_color('subscribe', 'optin-15', 'essb3_oflock|oflock_scroll_closecolor', esc_html__('Close action color', 'essb'), esc_html__('Customize close action color in case you change overlay color. Otherwise you can leave the default. Only if "Allow continuing without subscribing" is enabled.', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-15', 'essb3_oflock|oflock_scroll_closetext', esc_html__('Custom close text', 'essb'), esc_html__('Enter custom close text when you choose text mode of close function. Only if "Allow continuing without subscribing" is enabled.', 'essb'), '');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-15');
ESSBControlCenter::relation_enabled('optin-15', 'oflock_scroll', array('oflock_scroll_percent', 'oflock_scroll_design', 'oflock_scroll_bgcolor', 'oflock_scroll_close', 'oflock_scroll_closecolor', 'oflock_scroll_closetext'));


ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-15');



ESSBOptionsStructureHelper::help('subscribe', 'optin-1', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/subscribe-forms-general-setup/', 'Manual Form Adding in Content' => 'https://docs.socialsharingplugin.com/knowledgebase/manual-subscribe-form-adding-in-the-content/'));
	
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-1', 'subscribe_connector', esc_html__('Mailing list service', 'essb'), '', $optin_connectors);
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-1', 'subscribe_widget', esc_html__('Enable widget & shortcode', 'essb'), esc_html__('Enable usage of shortcode and widget anywhere on-site (outside automated displays).', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-1', 'subscribe_css_deactivate_mobile', esc_html__('Hide subscribe forms on mobile', 'essb'), esc_html('The option fully deactivates subscribe forms running on mobile. Extra components like fly-out or booster forms can be deactivated separately on mobile from their own settings screen.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));

if (!essb_option_bool_value('deactivate_module_conversions')) {
	ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-1', 'conversions_subscribe_lite_run', esc_html__('Track subscribe forms conversion', 'essb') . essb_generate_expert_badge(), esc_html__('Collect information about the subscription forms\' performance. You can see the number of views of each form or location and the conversions it generates (even those that are not successful). That will help optimize the usage of the subscribe forms on your website.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
}

// begin: connectors

ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-mailchimp');
ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-1', esc_html__('MailChimp', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "toggle"));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_mc_api', esc_html__('Mailchimp API key', 'essb'), '<a href="http://kb.mailchimp.com/accounts/management/about-api-keys#Finding-or-generating-your-API-key" target="_blank">Find your API key</a>');
ESSBControlCenter::set_description_inline('subscribe_mc_api');
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_mc_list', esc_html__('Mailchimp Audience ID', 'essb'), '<a href="https://mailchimp.com/help/find-audience-id/" target="_blank">Find Your Audience ID</a>');
ESSBControlCenter::set_description_inline('subscribe_mc_list');
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_mc_tags', esc_html__('Tags', 'essb'), 'Assign tags to the entered subscriber. Multiple tags can be set as a comma-separated list.');
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-1', 'subscribe_mc_welcome', esc_html__('Send welcome message on subscribe', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-1', 'subscribe_mc_double', esc_html__('Use double opt in', 'essb'), esc_html__('The MailChimp double opt-in process is a two-step process, where a subscriber fills out your signup form and receives an email with a link to confirm their subscription. MailChimp also includes some additional thank you and confirmation pages you can customize with your brand and messaging.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));

ESSBOptionsStructureHelper::field_heading('subscribe', 'optin-1', 'heading7', esc_html__('Send additional form fields to the mailing service', 'essb'), '<a href="https://mailchimp.com/help/manage-audience-signup-form-fields/" target="_blank">More about Manage Audience and Signup Form Fields on MailChimp</a>', 'pb0', '<i class="ti-widget-alt"></i>');
ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-1', 'essb-related-heading7', '');
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-1', 'subscribe_mc_custompos', esc_html__('Custom field for Position', 'essb') . essb_generate_expert_badge(), esc_html__('Enter field ID where the value of the position will be stored.', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-1', 'subscribe_mc_customdes', esc_html__('Custom field for Design', 'essb') . essb_generate_expert_badge(), esc_html__('Enter field ID where the value of the design will be stored.', 'essb'));
ESSBOptionsStructureHelper::field_textbox('subscribe', 'optin-1', 'subscribe_mc_customtitle', esc_html__('Custom field for Post/Page title', 'essb') . essb_generate_expert_badge(), esc_html__('Enter field ID where the post/page title will be stored.', 'essb'));
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-1');
ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-1');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-1');

ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-getresponse');
ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-1', esc_html__('GetResponse', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "toggle"));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_gr_api', esc_html__('GetReponse API key', 'essb'), '<a href="http://support.getresponse.com/faq/where-i-find-api-key" target="_blank">Find your API key</a>');
ESSBControlCenter::set_description_inline('subscribe_gr_api');
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_gr_list', esc_html__('GetReponse Campaign Name', 'essb'), '<a href="http://support.getresponse.com/faq/can-i-change-the-name-of-a-campaign" target="_blank">Find your campaign name</a>');
ESSBControlCenter::set_description_inline('subscribe_gr_list');
ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-1');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-1');

ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-mailerlite');
ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-1', esc_html__('MailerLite', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "toggle"));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_ml_api', esc_html__('MailerLite API key', 'essb'), sprintf(esc_html__('Entery your MailerLite API key. To get your key visit this page %s and look under API key.', 'essb'), '<a href="https://app.mailerlite.com/subscribe/api" target="_blank">https://app.mailerlite.com/subscribe/api</a>'));
ESSBControlCenter::set_description_inline('subscribe_ml_api');
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_ml_list', esc_html__('MailerLite List ID (Group ID)', 'essb'), esc_html__('Enter your list id (aka Group ID). To find your group id visit again the page for API key generation and you will see all list you have with their ids.', 'essb'));
ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-1');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-1');

ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-activecampaign');
ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-1', esc_html__('ActiveCampaign', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "toggle"));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_ac_api_url', esc_html__('ActiveCampaign API URL', 'essb'), esc_html__('Enter your ActiveCampaign API URL. To get API URL please go to your ActiveCampaign Account >> My Settings >> Developer.', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_ac_api', esc_html__('ActiveCampaign API Key', 'essb'), esc_html__('Enter your ActiveCampaign API Key. To get API Key please go to your ActiveCampaign Account >> My Settings >> Developer.', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_ac_list', esc_html__('ActiveCapaign List ID', 'essb'), esc_html__('Entery your ActiveCampaign List ID. To get your list ID visit lists pages and copy ID that you see in browser when you open list ?listid=<yourid>.', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_ac_form', esc_html__('ActiveCapaign Form ID', 'essb'), esc_html__('	Optional subscription Form ID, to inherit those redirection settings. Example: 1001. This will allow you to mimic adding the contact through a subscription form, where you can take advantage of the redirection settings.', 'essb'));
ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-1');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-1');

ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-campaignmonitor');
ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-1', esc_html__('CampaignMonitor', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "toggle"));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_cm_api', esc_html__('CampaignMonitor API Key', 'essb'), esc_html__('Enter your Campaign Monitor API Key. You can get your API Key from the Account Settings page when logged into your Campaign Monitor account.', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_cm_list', esc_html__('CampaignMonitor List ID', 'essb'), esc_html__('Enter your List ID. You can get List ID from the list editor page when logged into your Campaign Monitor account.', 'essb'));
ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-1');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-1');

ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-mymail');
ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-1', esc_html__('Mailster', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "toggle"));
$listOfOptions = array();
if (function_exists('mailster')) {
	$lists = mailster('lists')->get();
	foreach ($lists as $list) {
		if (function_exists('mailster')) $id = $list->ID;
		else $id = $list->term_id;

		$listOfOptions[$id] = $list->name;
	}
}
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-1', 'subscribe_mm_list', esc_html__('Mailster List', 'essb'), esc_html__('Select your list. Please ensure that Mailster plugin is installed.', 'essb'), $listOfOptions);
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-1', 'subscribe_mm_double', esc_html__('Use pending state for new subscribers', 'essb'), esc_html__('Use this to setup Pending state of all your new subscribers and manually review at later.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-1');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-1');


ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-mailpoet');
ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-1', esc_html__('MailPoet', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "toggle"));
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

ESSBOptionsStructureHelper::field_select('subscribe', 'optin-1', 'subscribe_mp_list', esc_html__('MailPoet List', 'essb'), esc_html__('Select your list. Please ensure that MailPoet plugin is installed.', 'essb'), $listOfOptions);
ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-1');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-1');

ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-sendinblue');
ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-1', esc_html__('SendinBlue', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "toggle"));
ESSBControlCenter::set_description_inline('subscribe_sib_api');
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_sib_api', esc_html__('SendinBlue API Key', 'essb'), sprintf(esc_html__('Open %s and get your version 3 API key.', 'essb'), '<a href="https://account.sendinblue.com/advanced/api" target="_blank">https://account.sendinblue.com/advanced/api</a>'));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_sib_list', esc_html__('SendinBlue List ID', 'essb'), esc_html__('Enter a numeric value of the list ID. For example: if your list ID is #2 put just 2.', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_sib_name_param', esc_html__('Custom parameter for the name field', 'essb'), esc_html__('The default binding of the name parameter in SendInBlue is in the LASTNAME. If you are using a localized version or another field type its name in here (example: NAME).', 'essb'));
ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-1');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-1');


ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-madmimi');
ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-1', esc_html__('Mad Mimi', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "toggle"));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_madmimi_login', esc_html__('Mad Mimi Username/Email', 'essb'), esc_html__('Enter your username or e-mail address using to access the system', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_madmimi_api', esc_html__('Mad Mimi API Key', 'essb'), sprintf(esc_html__('Enter your Mad Mimi API Key. You can get your API Key %s', 'essb'), '<a href="https://madmimi.com/user/edit?account_info_tabs=account_info_personal" target="_blank">https://madmimi.com/user/edit?account_info_tabs=account_info_personal</a>'));
ESSBControlCenter::set_description_inline('subscribe_madmimi_api');
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_madmimi_list', esc_html__('Mad Mimi List ID', 'essb'), esc_html__('Enter your list ID.', 'essb'));
ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-1');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-1');

ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-conversio');
ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-1', esc_html__('Conversio', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "toggle"));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_conv_api', esc_html__('Conversio API key', 'essb'), esc_html__('Enter your API access key', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_conv_list', esc_html__('Conversio List ID', 'essb'), esc_html__('Enter your list ID (not list name but the unique ID of the list)', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_conv_text', esc_html__('Optional opt-in text', 'essb'), esc_html__('What opt-in text was shown to the subscriber. This is required for GDPR compliance.', 'essb'));
ESSBOptionsStructureHelper::field_component('subscribe', 'optin-1', 'essb5_conversio_lists_locate', 'true');
ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-1');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-1');

ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-fluentcrm');
ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-1', esc_html__('FluentCRM', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "toggle"));
$listOfOptions = array();
if (function_exists('FluentCrmApi')) {
    $lists = FluentCrmApi('lists')->all();
    foreach ($lists as $list) {
        $id = $list->ID;                
        $listOfOptions[$id] = $list->title;
    }
}
ESSBOptionsStructureHelper::field_select('subscribe', 'optin-1', 'subscribe_fcrm_list', esc_html__('List', 'essb'), esc_html__('Select your list. Please ensure that FluentCRM plugin is installed.', 'essb'), $listOfOptions);
ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-1');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-1');

ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-acelle');
ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-1', esc_html__('Acelle - Email Marketing Web Application', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "toggle"));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_acelle_url', esc_html__('Acelle URL', 'essb'), esc_html__('Enter your main URL of the Acelle application. Example: https://acelle.wsl', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_acelle_api', esc_html__('API Token', 'essb'), '');
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_acelle_listid', esc_html__('List ID', 'essb'), '');
ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-1');
ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-1');


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
		ESSBOptionsStructureHelper::holder_start('subscribe', 'optin-1', 'essb-subscribe-connector', 'essb-subscribe-connector-'.$connector);
		ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-1', $service_name, esc_html__('Configure mailing list service access details', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle"));

		foreach ($custom_connectors_options[$connector] as $field => $settings) {
			$type = isset($settings['type']) ? $settings['type'] : 'text';
			$title = isset($settings['title']) ? $settings['title'] : '';
			$desc = isset($settings['desc']) ? $settings['desc'] : '';
			$values = isset($settings['values']) ? $settings['values'] : array();

			if ($type == 'text') {
				ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', $field, $title, $desc);
				ESSBControlCenter::set_description_inline($field);
			}
			if ($type == 'switch') {
				ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-1', $field, $title, $desc, '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
				ESSBControlCenter::set_description_inline($field);
			}

			if ($type == 'select') {
				ESSBOptionsStructureHelper::field_select('subscribe', 'optin-1', $field, $title, $desc, $values);
				ESSBControlCenter::set_description_inline($field);
			}
		}

		ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-1');
		ESSBOptionsStructureHelper::holder_end('subscribe', 'optin-1');

	}
}

// end: connectors

ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-1', esc_html__('Redirect on successful subscribe', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_success', esc_html__('Page URL', 'essb'), esc_html__('Enter a page where you wish to direct users after successful subscribe.', 'essb'));
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-1', 'subscribe_success_new', esc_html__('Open URL in a new window', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-1');

ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-1', esc_html__('Additional form settings', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-1', 'subscribe_require_name', esc_html__('Make the name field required', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_require_name_error', esc_html__('Error message when name field is blank', 'essb'), '');
ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-1');

ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-1', esc_html__('Add ReCaptcha protection to subscribe forms', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-1', 'subscribe_recaptcha', esc_html__('Enable Google\'s reCAPTCHA v2', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_recaptcha_site', esc_html__('reCAPTCHA Site Key', 'essb'), '');
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_recaptcha_secret', esc_html__('reCAPTCHA Secret Key', 'essb'), '');
ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-1');

ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-1', esc_html__('Add CloudFlare Turnstile protection to subscribe forms', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-1', 'subscribe_turnstile', esc_html__('Enable CloudFlare\'s Turnstile', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_turnstile_site', esc_html__('Turnstile Site Key', 'essb'), '');
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_turnstile_secret', esc_html__('Turnstile Secret Key', 'essb'), '');
ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-1');


ESSBOptionsStructureHelper::panel_start('subscribe', 'optin-1', esc_html__('Include agree to terms confirmation (GDPR Recommended)', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_switch('subscribe', 'optin-1', 'subscribe_terms', esc_html__('Include I agree to terms confirmation', 'essb'), esc_html__('Set this option to Yes to add in form a checkbox that will require users to confirm that they agree with terms before submitting. (Recommended for usage in EU).', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_terms_text', esc_html__('Text appearing next to the confirmation check', 'essb'), '');
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_terms_link', esc_html__('Link to a privacy page the confirmation text', 'essb'), esc_html__('Enter URL if you need to connect the confirmation text with a specific URL - for example, the Terms or Privacy page.', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_terms_link_text', esc_html__('Text of the link', 'essb'), esc_html__('When you enter a text for the link (example: Read our privacy) the plugin will append it at the end of confirmation box text. And only this will be clickable as a link. But if you don\'t put custom text inside this field, but URL is present the entire confirmation option text will be a link.', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_terms_error', esc_html__('Error message if the confirmation option is not set', 'essb'), '');
ESSBOptionsStructureHelper::field_textbox_stretched('subscribe', 'optin-1', 'subscribe_terms_field', esc_html__('Forward confirmation status to the mailing list', 'essb'), esc_html__('For selected services, it is possible to automatically write in a custom field that the user confirms the sign up with the check. Enter here custom list parameter ID and the plugin will store Yes on a field check. Supported by: MailChimp, GetReponse, MailPoet, ActiveCampaign, CampaignMonitor, SendInBlue', 'essb'));
ESSBOptionsStructureHelper::panel_end('subscribe', 'optin-1');


function essb5_create_design_preview_button($design = '') {
	$preview_url = add_query_arg(array('subscribe-preview' => 'true', 'design' => $design ), trailingslashit(home_url()));
	$custom_buttons = '<a href="'.esc_url($preview_url).'" target="_blank" class="essb-btn tile-orange ao-form-preview"><i class="fa fa-eye"></i>'.esc_html__('Preview', 'essb').'</a>';

	return $custom_buttons;
}

function essb5_add_subscribe_design_button() {
	echo '<div class="row essb-new-subscribe-design">';
	echo '<a href="#" class="ao-new-subscribe-design ao-form-userdesign" data-design="new" data-title="Create new form design"><span class="essb_icon fa fa-plus-square"></span><span>'.esc_html__('Add new subscribe form design', 'essb').'</span></a>';
	echo '</div>';

	$all_designs = essb5_get_form_designs();

	$count = 0;
	foreach ($all_designs as $design) {
		$name = isset($design['name']) ? $design['name'] : 'Untitled Form';

		$preview_url = add_query_arg(array('subscribe-preview' => 'true', 'design' => 'userdesign-'.$count ), trailingslashit(home_url()));

		$custom_buttons = '<a href="#" class="essb-btn tile-config ao-form-userdesign" data-design="design-'.$count.'" data-title="Manage Existing Design"><i class="fa fa-cog"></i>'.esc_html__('Edit', 'essb').'</a>';
		$custom_buttons .= '<a href="#" class="essb-btn tile-deactivate ao-form-removeuserdesign" data-design="design-'.$count.'" data-title="Manage Existing Design"><i class="fa fa-close"></i>'.esc_html__('Remove', 'essb').'</a>';
		$custom_buttons .= '<a href="'.esc_url($preview_url).'" target="_blank" class="essb-btn tile-orange ao-form-preview" data-design="userdesign-'.$count.'" data-title="Manage Existing Design"><i class="fa fa-eye"></i>'.esc_html__('Preview', 'essb').'</a>';

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
	echo '<a href="#" class="ao-options-btn get-conversio-lists"><span class="essb_icon fa fa-refresh"></span><span>'.esc_html__('Choose List', 'essb').'</span></a>';
	// Conversio API docs: http://api-docs.conversio.com/#get-customer-lists
	// test API Key: 3f138995c963057676278e1148ce94794263c389
	?>
<script type="text/javascript">
jQuery(document).ready(function($){
	"use strict";
	
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
