<?php
ESSBOptionsStructureHelper::menu_item('style', 'buttons', __('Share Buttons', 'essb'), 'default');

if (!essb_option_bool_value('deactivate_module_followers')) {
	ESSBOptionsStructureHelper::menu_item('style', 'fans', __('Followers Counter', 'essb'), 'default');
}
ESSBOptionsStructureHelper::menu_item('style', 'image', __('On Media Share', 'essb'), 'default');
ESSBOptionsStructureHelper::menu_item('style', 'css', __('Additional CSS', 'essb'), 'default');
ESSBOptionsStructureHelper::menu_item('style', 'css2', __('Additional Footer CSS', 'essb'), 'default');

ESSBOptionsStructureHelper::menu_item('style', 'my-template', __('Custom Share Buttons Template', 'essb'), 'default');

ESSBOptionsStructureHelper::panel_start('style', 'buttons', __('Activate customization of template', 'essb'), __('Customization allows you to change colors of each button or total counter. Your are also able to activate customizer for specific pages only via on post styles.', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'customizer_is_active', 'switch_on' => __('Yes', 'essb'), 'switch_off' => __('No', 'essb'), 'switch_submit' => 'true'));


$customizer_is_active = ESSBOptionValuesHelper::options_bool_value($essb_options, 'customizer_is_active');
if ($customizer_is_active) {
	ESSBOptionsStructureHelper::panel_start('style', 'buttons', __('Total counter style', 'essb'), __('Customize total counter colors and font size', 'essb'), 'fa21 ti-ruler-pencil', array("mode" => "toggle"));
	ESSBOptionsStructureHelper::field_section_start_full_panels('style', 'buttons');
	ESSBOptionsStructureHelper::field_color_panel('style', 'buttons', 'customizer_totalbgcolor', __('Background color', 'essb'), __('Replace total counter background color', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel('style', 'buttons', 'customizer_totalnobgcolor', __('Remove background color', 'essb'), __('Activate this option to remove the background color', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));
	ESSBOptionsStructureHelper::field_color_panel('style', 'buttons', 'customizer_totalcolor', __('Text color', 'essb'), __('Replace total counter text color', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels('style', 'buttons');
	ESSBOptionsStructureHelper::field_section_start_full_panels('style', 'buttons');
	ESSBOptionsStructureHelper::field_textbox_panel('style', 'buttons', 'customizer_totalfontsize', __('Total counter big style font-size', 'essb'), __('Enter value in px (ex: 21px) to change the total counter font-size', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_panel('style', 'buttons', 'customizer_totalfontsize_after', __('Total counter big style shares text font-size', 'essb'), __('Enter value in px (ex: 10px) to change the total counter shares text font-size', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_panel('style', 'buttons', 'customizer_totalfontsize_beforeafter', __('Total counter before/after share buttons text font-size', 'essb'), __('Enter value in px (ex: 14px) to change the total counter text font-size', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels('style', 'buttons');
	ESSBOptionsStructureHelper::panel_end('style', 'buttons');

	ESSBOptionsStructureHelper::panel_start('style', 'buttons', __('Customization for all social networks', 'essb'), __('Provide settings that will be applied over all social networks at once. Below you can also customize settings for single network as well.', 'essb'), 'fa21 ti-ruler-pencil', array("mode" => "toggle"));
	ESSBOptionsStructureHelper::field_section_start_full_panels('style', 'buttons');
	ESSBOptionsStructureHelper::field_color_panel('style', 'buttons', 'customizer_bgcolor', __('Background color', 'essb'), __('Replace all buttons background color', 'essb'));
	ESSBOptionsStructureHelper::field_color_panel('style', 'buttons', 'customizer_textcolor', __('Text color', 'essb'), __('Replace all buttons text color', 'essb'));
	ESSBOptionsStructureHelper::field_color_panel('style', 'buttons', 'customizer_hovercolor', __('Hover background color', 'essb'), __('Replace all buttons hover background color', 'essb'));
	ESSBOptionsStructureHelper::field_color_panel('style', 'buttons', 'customizer_hovertextcolor', __('Hover text color', 'essb'), __('Replace all buttons hover text color', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel('style', 'buttons', 'customizer_remove_bg_hover_effects', __('Remove effects applied from theme on hover', 'essb'), __('Activate this option to remove the default theme hover effects (like darken or lighten color).', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels('style', 'buttons');
	ESSBOptionsStructureHelper::field_section_start_full_panels('style', 'buttons');
	ESSBOptionsStructureHelper::field_textbox_panel('style', 'buttons', 'customizer_iconsize', __('Icon size', 'essb'), __('Provide custom icon size value. Default value for almost all templates is 18. Please enter value without any symbols before/after it - example: 22', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_panel('style', 'buttons', 'customizer_namesize', __('Network name font size', 'essb'), __('Enter value in px (ex: 10px) to change the network name text font-size', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel('style', 'buttons', 'customizer_namebold', __('Make network name bold', 'essb'), __('Activate this option to apply bold style over network name', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel('style', 'buttons', 'customizer_nameupper', __('Make network name upper case', 'essb'), __('Activate this option to apply automatic transform to upper case over network name', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels('style', 'buttons');
	ESSBOptionsStructureHelper::panel_end('style', 'buttons');

	ESSBOptionsStructureHelper::panel_start('style', 'buttons', __('Activate customization by network', 'essb'), __('Use this option to activate customization of each social network individually.', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'customizer_network_is_active', 'switch_on' => __('Yes', 'essb'), 'switch_off' => __('No', 'essb'), 'switch_submit' => 'true'));
	
	if (essb_option_bool_value('customizer_network_is_active')) {
		ESSBOptionsStructureHelper::field_heading('style', 'buttons', 'heading5', __('Color customization for single social networks', 'essb'));
		essb3_prepare_color_customization_by_network('style', 'buttons');
	}
	ESSBOptionsStructureHelper::panel_end('style', 'buttons');
}
ESSBOptionsStructureHelper::panel_end('style', 'buttons');
ESSBOptionsStructureHelper::field_editor('style', 'css', 'customizer_css', __('Additional custom CSS', 'essb'), __('Provide your own custom CSS code that will be used only when plugin is active', 'essb'));

ESSBOptionsStructureHelper::field_editor('style', 'css2', 'customizer_css_footer', __('Additional custom CSS that will be added to footer', 'essb'), __('Add custom CSS code here if you wish that code to be included into footer of site', 'essb'));

if (!essb_option_value('deactivate_module_followers')) {
	ESSBOptionsStructureHelper::panel_start('style', 'fans', __('Activate customization of followers counter colors', 'essb'), __('Activate this option to get range of options to change color for each network into followers counter. The change will be applied on selected template and on any instance of follower buttons', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'activate_fanscounter_customizer', 'switch_on' => __('Yes', 'essb'), 'switch_off' => __('No', 'essb')));
	essb3_draw_fanscounter_customization('style', 'fans');
	ESSBOptionsStructureHelper::panel_end('style', 'fans');
}

ESSBOptionsStructureHelper::panel_start('style', 'image', __('Activate customization of on media sharing button colors', 'essb'), __('Activate this option to get range of options to change color for each network into on media sharing.', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'activate_imageshare_customizer', 'switch_on' => __('Yes', 'essb'), 'switch_off' => __('No', 'essb')));
essb3_draw_imageshare_customization('style', 'image');
ESSBOptionsStructureHelper::panel_end('style', 'image');


function essb3_prepare_color_customization_by_network($tab_id, $menu_id) {
	global $essb_networks;

	$checkbox_list_networks = array();
	foreach ($essb_networks as $key => $object) {
		$checkbox_list_networks[$key] = $object['name'];
	}

	foreach ($checkbox_list_networks as $key => $text) {

		ESSBOptionsStructureHelper::panel_start($tab_id, $menu_id, $text, __('Configure additional options for this network', 'essb'), 'fa21 essb_icon_'.$key, array("mode" => "toggle", 'state' => 'closed'));
		ESSBOptionsStructureHelper::field_section_start_full_panels($tab_id, $menu_id);
		//ESSBOptionsStructureHelper::field_section_start_full_panels($tab_id, $menu_id, $text, '');
		ESSBOptionsStructureHelper::field_color_panel($tab_id, $menu_id, 'customizer_'.$key.'_bgcolor', __('Background color', 'essb'), __('Replace all buttons background color', 'essb'));
		ESSBOptionsStructureHelper::field_color_panel($tab_id, $menu_id, 'customizer_'.$key.'_textcolor', __('Text color', 'essb'), __('Replace all buttons text color', 'essb'));
		ESSBOptionsStructureHelper::field_color_panel($tab_id, $menu_id, 'customizer_'.$key.'_hovercolor', __('Hover background color', 'essb'), __('Replace all buttons hover background color', 'essb'));
		ESSBOptionsStructureHelper::field_color_panel($tab_id, $menu_id, 'customizer_'.$key.'_hovertextcolor', __('Hover text color', 'essb'), __('Replace all buttons hover text color', 'essb'));
		ESSBOptionsStructureHelper::field_section_end_full_panels($tab_id, $menu_id);
		ESSBOptionsStructureHelper::field_file($tab_id, $menu_id, 'customizer_'.$key.'_icon', __('Icon', 'essb'), __('Replace social icon', 'essb'));
		ESSBOptionsStructureHelper::field_textbox($tab_id, $menu_id, 'customizer_'.$key.'_iconbgsize', __('Background size for regular icon', 'essb'), __('Provide custom background size if needed (for retina templates default used is 21px 21px)', 'essb'));
		ESSBOptionsStructureHelper::field_file($tab_id, $menu_id, 'customizer_'.$key.'_hovericon', __('Hover icon', 'essb'), __('Replace social icon', 'essb'));
		ESSBOptionsStructureHelper::field_textbox($tab_id, $menu_id, 'customizer_'.$key.'_hovericonbgsize', __('Hover background size for regular icon', 'essb'), __('Provide custom background size if needed (for retina templates default used is 21px 21px)', 'essb'));
		ESSBOptionsStructureHelper::panel_end($tab_id, $menu_id);
	}
}


function essb3_draw_imageshare_customization($tab_id, $menu_id) {
	$listOfNetworksAdvanced = array( "facebook" => "Facebook", "twitter" => "Twitter", "google" => "Google", "linkedin" => "LinkedIn", "pinterest" => "Pinterest", "tumblr" => "Tumblr", "reddit" => "Reddit", "digg" => "Digg", "delicious" => "Delicious", "vkontakte" => "VKontakte", "odnoklassniki" => "Odnoklassniki");

	foreach ($listOfNetworksAdvanced as $network => $title) {
		ESSBOptionsStructureHelper::field_color($tab_id, $menu_id, 'imagecustomizer_'.$network, $title, '');
	}
}

function essb3_draw_fanscounter_customization($tab_id, $menu_id) {
	$network_list = ESSBSocialFollowersCounterHelper::available_social_networks();

	if (defined('ESSB3_SFCE_VERSION')) {
		$network_list = ESSBSocialFollowersCounterHelper::list_of_all_available_networks_extended();
	}

	foreach ($network_list as $network => $title) {
		ESSBOptionsStructureHelper::field_color($tab_id, $menu_id, 'fanscustomizer_'.$network, $title, '');
	}
}

/** Custom Share Buttons Template Code */

ESSBOptionsStructureHelper::field_switch('style', 'my-template', 'mytemplate_activate', __('Use my template', 'essb'), __('Set this option to Yes if you wish to use the custom template you made', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));

ESSBOptionsStructureHelper::panel_start('style', 'my-template', __('Total counter style', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_section_start_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_totalbgcolor', __('Background color', 'essb'), __('Replace total counter background color', 'essb'));
ESSBOptionsStructureHelper::field_switch_panel('style', 'my-template', 'mytemplate_totalnobgcolor', __('Remove background color', 'essb'), __('Activate this option to remove the background color', 'essb'), '', __('Yes', 'essb'), __('No', 'essb'));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_totalcolor', __('Text color', 'essb'), __('Replace total counter text color', 'essb'));
ESSBOptionsStructureHelper::field_section_end_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::field_section_start_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::field_textbox_panel('style', 'my-template', 'mytemplate_totalfontsize', __('Total counter big style font-size', 'essb'), __('Enter value in px (ex: 21px) to change the total counter font-size', 'essb'));
ESSBOptionsStructureHelper::field_textbox_panel('style', 'my-template', 'mytemplate_totalfontsize_after', __('Total counter big style shares text font-size', 'essb'), __('Enter value in px (ex: 10px) to change the total counter shares text font-size', 'essb'));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_totalfontsize_after_color', __('Total counter big style shares text color', 'essb'), '');
ESSBOptionsStructureHelper::field_textbox_panel('style', 'my-template', 'mytemplate_totalfontsize_beforeafter', __('Total counter before/after share buttons text font-size', 'essb'), __('Enter value in px (ex: 14px) to change the total counter text font-size', 'essb'));
ESSBOptionsStructureHelper::field_section_end_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::panel_end('style', 'my-template');


ESSBOptionsStructureHelper::panel_start('style', 'my-template', __('Button Style', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::title('style', 'my-template',  __('Icon Size, Font Size & Button Size', 'essb'), '', 'inner-row');
ESSBOptionsStructureHelper::field_section_start_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::field_textbox_panel('style', 'my-template', 'mytemplate_iconsize', __('Icon Size', 'essb'), __('Provide custom icon size value. Numeric value (example: 24) - default value is: 18', 'essb'));
ESSBOptionsStructureHelper::field_textbox_panel('style', 'my-template', 'mytemplate_iconspace', __('Icon Gutter', 'essb'), __('Default value used here is 9 (9px from each side) and you can change this according to effect you wish to have. This value will also reflect over the size of button.', 'essb'));
ESSBOptionsStructureHelper::field_textbox_panel('style', 'my-template', 'mytemplate_namesize', __('Network name font size', 'essb'), __('Enter custom network name font size (example: 16px, 2em, 5rem)', 'essb'));
ESSBOptionsStructureHelper::field_textbox_panel('style', 'my-template', 'mytemplate_padding', __('Button Padding', 'essb'), __('Set custom padding of button if you wish to get a bigger button. Supporting the CSS standard padding values (10px, 10px 10px).', 'essb'));
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_nameweight', __('Network name text style', 'essb'), __('', 'essb'), array("" => "Default", "normal" => "Normal", "bold" => "Bold", "italic" => "Italic"));
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_texttrans', __('Network name text transform', 'essb'), __('', 'essb'), array("" => "Default", "uppercase" => "Uppercase", "capitalaize" => "Capitalize"));
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_shape', __('Button Shape', 'essb'), __('Customize the shape of button', 'essb'), array("" => "Default", "rounded" => "Round Edges", "round" => "Round", "leaf" => "Leaf"));
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_effect', __('Button Effect', 'essb'), __('Add custom button effect', 'essb'), array("" => "No effect", "flat" => "Flat style", "shadow" => "Drop shadow", "glow" => "Glow effect"));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_effect_color', __('Button Effect Color', 'essb'), __('Replace default effect color', 'essb'), '', 'true');
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_effect_strength', __('Button Effect Strength', 'essb'), __('Correct strength of button effect (when activated)', 'essb'), array("" => "Default", "small" => "Small", "medium" => "Medium", "large" => "Large", 'xlarge' => "Extra Large"));

ESSBOptionsStructureHelper::field_section_end_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::panel_end('style', 'my-template');

ESSBOptionsStructureHelper::panel_start('style', 'my-template', __('Default Color Set', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::title('style', 'my-template',  __('Default Color Set', 'essb'), '', 'inner-row');
ESSBOptionsStructureHelper::field_section_start_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_default_color', __('Background Color', 'essb'), __('Choose the default background color of button', 'essb'), array("" => "Network default/custom color", "white" => "White", "dark" => "Dark", "custom" => "Custom Color"));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_default_color_custom', __('Custom Background Color', 'essb'), __('Custom background color will be used when you select from menu to use custom color. It will be set for all social networks that does not have own custom color', 'essb'));
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_default_textcolor', __('Text/Icon Color', 'essb'), __('Choose the default text/icon color of button', 'essb'), array("" => "White", "network" => "Network default/custom color", "dark" => "Dark", "custom" => "Custom Color"));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_default_textcolor_custom', __('Custom Text/Icon Color', 'essb'), __('Custom text/icon color will be used when you select from menu to use custom color. It will be set for all social networks that does not have own custom color', 'essb'));
ESSBOptionsStructureHelper::field_textbox_panel('style', 'my-template', 'mytemplate_default_outlinesize', __('Outline Size', 'essb'), __('Setup outline over the share button. To do this and color setup work enter numeric value for size (1, 2, 3 and etc.)', 'essb'));
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_default_outlinecolor', __('Outline Color', 'essb'), __('Choose the default outline color of button', 'essb'), array("" => "Network default/custom color", "white" => "White", "dark" => "Dark", "custom" => "Custom Color"));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_default_outlinecolor_custom', __('Custom Outline Color', 'essb'), __('Custom outline color will be used when you select from menu to use custom color. It will be set for all social networks that does not have own custom color', 'essb'));
ESSBOptionsStructureHelper::field_section_end_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::panel_end('style', 'my-template');

ESSBOptionsStructureHelper::panel_start('style', 'my-template', __('On Hover Color Set', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::title('style', 'my-template',  __('On Hover Color Set', 'essb'), '', 'inner-row');
ESSBOptionsStructureHelper::field_section_start_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_hover_color', __('Background Color', 'essb'), __('Choose the default background color of button', 'essb'), array("" => "Generate automatically", "network" => "Network default/custom color", "white" => "White", "dark" => "Dark", "custom" => "Custom Color"));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_hover_color_custom', __('Custom Background Color', 'essb'), __('Custom background color will be used when you select from menu to use custom color. It will be set for all social networks that does not have own custom color', 'essb'));
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_hover_textcolor', __('Text/Icon Color', 'essb'), __('Choose the default text/icon color of button', 'essb'), array("" => "White", "network" => "Network default/custom color", "dark" => "Dark", "custom" => "Custom Color"));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_hover_textcolor_custom', __('Custom Text/Icon Color', 'essb'), __('Custom text/icon color will be used when you select from menu to use custom color. It will be set for all social networks that does not have own custom color', 'essb'));
ESSBOptionsStructureHelper::field_textbox_panel('style', 'my-template', 'mytemplate_hover_outlinesize', __('Outline Size', 'essb'), __('Setup outline over the share button. To do this and color setup work enter numeric value for size (1, 2, 3 and etc.)', 'essb'));
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_hover_outlinecolor', __('Outline Color', 'essb'), __('Choose the default outline color of button', 'essb'), array("" => "Network default/custom color", "white" => "White", "dark" => "Dark", "custom" => "Custom Color"));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_hover_outlinecolor_custom', __('Custom Outline Color', 'essb'), __('Custom outline color will be used when you select from menu to use custom color. It will be set for all social networks that does not have own custom color', 'essb'));

ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_hover_color_effect', __('Button Hover Effect', 'essb'), __('Set eye catching hover animation', 'essb'), array("" => "No hover animation", "shiny" => "Shiny"));


ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_hover_shape', __('Button Shape', 'essb'), __('Customize the shape of button', 'essb'), array("" => "Default", "square" => "Rectangle", "rounded" => "Round Edges", "round" => "Round", "leaf" => "Leaf"));
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_hover_effect', __('Button Effect', 'essb'), __('Add custom button effect', 'essb'), array("" => "Default", "no" => "No effect", "flat" => "Flat style", "shadow" => "Drop shadow", "glow" => "Glow effect"));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_hover_effect_color', __('Button Effect Color', 'essb'), __('Replace default effect color', 'essb'), '', 'true');
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_hover_effect_strength', __('Button Effect Strength', 'essb'), __('Correct strength of button effect (when activated)', 'essb'), array("" => "Default", "small" => "Small", "medium" => "Medium", "large" => "Large", 'xlarge' => "Extra Large"));

ESSBOptionsStructureHelper::field_section_end_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::panel_end('style', 'my-template');

//ESSBOptionsStructureHelper::panel_start('style', 'my-template', __('Colors by Social Networks', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::panel_start('style', 'my-template', __('Colors by Social Networks', 'essb'), __('Use this option if you wish to personalize social network individual colors.', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'mytemplate_network_is_active', 'switch_on' => __('Yes', 'essb'), 'switch_off' => __('No', 'essb'), 'switch_submit' => 'true'));


if (essb_option_bool_value('mytemplate_network_is_active')) {
	$all_networks = essb_available_social_networks();
		
	$checkbox_list_networks = array();
	foreach ($all_networks as $key => $object) {
		$checkbox_list_networks[$key] = $object['name'];
	}
		
	$tab_id = 'style';
	$menu_id = 'my-template';
		
	foreach ($checkbox_list_networks as $key => $text) {
		ESSBOptionsStructureHelper::holder_start($tab_id, $menu_id, 'essb-mytemplate-network essb-options-hint-glow', 'essb-mytemplate-network');

		ESSBOptionsStructureHelper::title('style', 'my-template',  $text, '', 'inner-row');
		ESSBOptionsStructureHelper::field_section_start_full_panels($tab_id, $menu_id);
		ESSBOptionsStructureHelper::field_color_panel($tab_id, $menu_id, 'mytemplate_'.$key.'_bgcolor', __('Background color', 'essb'), __('Replace all buttons background color', 'essb'));
		ESSBOptionsStructureHelper::field_color_panel($tab_id, $menu_id, 'mytemplate_'.$key.'_textcolor', __('Text color', 'essb'), __('Replace all buttons text color', 'essb'));
		ESSBOptionsStructureHelper::field_color_panel($tab_id, $menu_id, 'mytemplate_'.$key.'_hovercolor', __('Hover background color', 'essb'), __('Replace all buttons hover background color', 'essb'));
		ESSBOptionsStructureHelper::field_color_panel($tab_id, $menu_id, 'mytemplate_'.$key.'_hovertextcolor', __('Hover text color', 'essb'), __('Replace all buttons hover text color', 'essb'));
		ESSBOptionsStructureHelper::field_section_end_full_panels($tab_id, $menu_id);
		ESSBOptionsStructureHelper::holder_end($tab_id, $menu_id);
	}
}

ESSBOptionsStructureHelper::panel_end('style', 'my-template');
