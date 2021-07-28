<?php
if (class_exists('ESSBControlCenter')) {
	ESSBControlCenter::register_sidebar_section_menu('style', 'buttons', esc_html__('Share Buttons', 'essb'));
	if (!essb_option_bool_value('deactivate_module_followers')) {
		ESSBControlCenter::register_sidebar_section_menu('style', 'fans', esc_html__('Followers Counter', 'essb'));
	}
	
	if (!essb_option_bool_value('deactivate_module_profiles')) {
		ESSBControlCenter::register_sidebar_section_menu('style', 'profiles', esc_html__('Profile Links', 'essb'));
	}
	
	if (essb_is_active_feature('imageshare')) {
		ESSBControlCenter::register_sidebar_section_menu('style', 'image', esc_html__('On Media', 'essb'));
	}
	
	if (!essb_option_bool_value('deactivate_ctt')) {
		ESSBControlCenter::register_sidebar_section_menu('style', 'cct', esc_html__('Click to Tweet', 'essb'));
	}
	
	ESSBControlCenter::register_sidebar_section_menu('style', 'css', esc_html__('Additional CSS', 'essb'));
	ESSBControlCenter::register_sidebar_section_menu('style', 'css2', esc_html__('Additional Footer CSS', 'essb'));
	ESSBControlCenter::register_sidebar_section_menu('style', 'my-template', esc_html__('Share Buttons Template Builder', 'essb'));
}


ESSBOptionsStructureHelper::menu_item('style', 'buttons', esc_html__('Share Buttons', 'essb'), 'default');

if (!essb_option_bool_value('deactivate_module_followers')) {
	ESSBOptionsStructureHelper::menu_item('style', 'fans', esc_html__('Followers Counter', 'essb'), 'default');
}
ESSBOptionsStructureHelper::menu_item('style', 'image', esc_html__('On Media Share', 'essb'), 'default');
ESSBOptionsStructureHelper::menu_item('style', 'css', esc_html__('Additional CSS', 'essb'), 'default');
ESSBOptionsStructureHelper::menu_item('style', 'css2', esc_html__('Additional Footer CSS', 'essb'), 'default');

ESSBOptionsStructureHelper::menu_item('style', 'my-template', esc_html__('Custom Share Buttons Template', 'essb'), 'default');

ESSBOptionsStructureHelper::panel_start('style', 'buttons', esc_html__('Enable color customization of the used theme for Share Buttons', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'customizer_is_active', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb'), 'switch_submit' => 'true'));

$customizer_is_active = essb_options_bool_value('customizer_is_active');
if ($customizer_is_active) {
	ESSBOptionsStructureHelper::panel_start('style', 'buttons', esc_html__('Total Counter', 'essb'), '', 'fa21 ti-ruler-pencil', array("mode" => "toggle"));
	ESSBOptionsStructureHelper::field_section_start_full_panels('style', 'buttons');
	ESSBOptionsStructureHelper::field_color_panel('style', 'buttons', 'customizer_totalbgcolor', esc_html__('Background color', 'essb'), '');
	ESSBOptionsStructureHelper::field_switch_panel('style', 'buttons', 'customizer_totalnobgcolor', esc_html__('Remove background color', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_color_panel('style', 'buttons', 'customizer_totalcolor', esc_html__('Text color', 'essb'), '');
	ESSBOptionsStructureHelper::field_section_end_full_panels('style', 'buttons');
	ESSBOptionsStructureHelper::field_section_start_full_panels('style', 'buttons');
	ESSBOptionsStructureHelper::field_textbox_panel('style', 'buttons', 'customizer_totalfontsize', esc_html__('Total counter big style font-size', 'essb'), esc_html__('Enter value in px (ex: 21px) to change the total counter font-size', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_panel('style', 'buttons', 'customizer_totalfontsize_after', esc_html__('Total counter big style shares text font-size', 'essb'), esc_html__('Enter value in px (ex: 10px) to change the total counter shares text font-size', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_panel('style', 'buttons', 'customizer_totalfontsize_beforeafter', esc_html__('Total counter before/after share buttons text font-size', 'essb'), esc_html__('Enter value in px (ex: 14px) to change the total counter text font-size', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels('style', 'buttons');
	ESSBOptionsStructureHelper::panel_end('style', 'buttons');

	ESSBOptionsStructureHelper::panel_start('style', 'buttons', esc_html__('Background Colors, Icon Size, Network Name Size for All Networks', 'essb'), '', 'fa21 ti-ruler-pencil', array("mode" => "toggle"));
	ESSBOptionsStructureHelper::field_section_start_full_panels('style', 'buttons');
	ESSBOptionsStructureHelper::field_color_panel('style', 'buttons', 'customizer_bgcolor', esc_html__('Background color', 'essb'), '');
	ESSBOptionsStructureHelper::field_color_panel('style', 'buttons', 'customizer_textcolor', esc_html__('Text color', 'essb'), '');
	ESSBOptionsStructureHelper::field_color_panel('style', 'buttons', 'customizer_hovercolor', esc_html__('Hover background color', 'essb'), '');
	ESSBOptionsStructureHelper::field_color_panel('style', 'buttons', 'customizer_hovertextcolor', esc_html__('Hover text color', 'essb'), '');
	ESSBOptionsStructureHelper::field_switch_panel('style', 'buttons', 'customizer_remove_bg_hover_effects', esc_html__('Remove effects applied from theme on hover', 'essb'), esc_html__('Activate this option to remove the default theme hover effects (like darken or lighten color).', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels('style', 'buttons');
	ESSBOptionsStructureHelper::field_section_start_full_panels('style', 'buttons');
	ESSBOptionsStructureHelper::field_textbox_panel('style', 'buttons', 'customizer_iconsize', esc_html__('Icon size', 'essb'), esc_html__('Provide custom icon size value. Default value for almost all templates is 18. Please enter value without any symbols before/after it - example: 22', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_panel('style', 'buttons', 'customizer_namesize', esc_html__('Network name font size', 'essb'), esc_html__('Enter value in px (ex: 10px) to change the network name text font-size', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel('style', 'buttons', 'customizer_namebold', esc_html__('Make network name bold', 'essb'), esc_html__('Activate this option to apply bold style over network name', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel('style', 'buttons', 'customizer_nameupper', esc_html__('Make network name upper case', 'essb'), esc_html__('Activate this option to apply automatic transform to upper case over network name', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels('style', 'buttons');
	ESSBOptionsStructureHelper::panel_end('style', 'buttons');

	ESSBOptionsStructureHelper::panel_start('style', 'buttons', esc_html__('Colors and Icon change for every network', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'customizer_network_is_active', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb'), 'switch_submit' => 'true'));
	
	if (essb_option_bool_value('customizer_network_is_active')) {
		essb3_prepare_color_customization_by_network('style', 'buttons');
	}
	ESSBOptionsStructureHelper::panel_end('style', 'buttons');
}
ESSBOptionsStructureHelper::panel_end('style', 'buttons');
ESSBOptionsStructureHelper::title('style', 'css', esc_html__('Header CSS Code', 'essb'), esc_html__('Enter additional custom CSS code that will appear inside content when the plugin is running. ', 'essb'));
ESSBOptionsStructureHelper::field_editor('style', 'css', 'customizer_css', '', '', 'css');
ESSBOptionsStructureHelper::title('style', 'css2', esc_html__('Footer CSS Code', 'essb'), esc_html__('Enter additional custom CSS code that will appear inside content when the plugin is running. ', 'essb'));
ESSBOptionsStructureHelper::field_editor('style', 'css2', 'customizer_css_footer', '', '', 'css');

if (!essb_option_bool_value('deactivate_module_followers')) {
	ESSBOptionsStructureHelper::panel_start('style', 'fans', esc_html__('Enable color customization of social followers counter', 'essb'), '', 'fa21 fa fa-paint-brush', array("mode" => "switch", 'switch_id' => 'activate_fanscounter_customizer', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
	essb3_draw_fanscounter_customization('style', 'fans');
	ESSBOptionsStructureHelper::panel_end('style', 'fans');
}

if (!essb_option_bool_value('deactivate_module_profiles')) {
	ESSBOptionsStructureHelper::panel_start('style', 'profiles', esc_html__('Enable color customization of social profile links', 'essb'), '', 'fa21 fa fa-paint-brush', array("mode" => "switch", 'switch_id' => 'activate_profiles_customizer', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
	essb3_draw_profiles_customization('style', 'profiles');
	ESSBOptionsStructureHelper::panel_end('style', 'profiles');
}

if (essb_is_active_feature('imageshare')) {
	ESSBOptionsStructureHelper::panel_start('style', 'image', esc_html__('Activate customization of on media sharing button colors', 'essb'), esc_html__('Activate this option to get range of options to change color for each network into on media sharing.', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'activate_imageshare_customizer', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
	essb3_draw_imageshare_customization('style', 'image');
	ESSBOptionsStructureHelper::panel_end('style', 'image');
}

if (!essb_option_bool_value('deactivate_ctt')) {
	ESSBOptionsStructureHelper::panel_start('style', 'cct', esc_html__('Enable build of a custom template for Click-to-Tweet', 'essb'), '', 'fa21 fa fa-paint-brush', array("mode" => "switch", 'switch_id' => 'activate_cct_customizer', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));

	ESSBOptionsStructureHelper::field_color('style', 'cct', 'customize_cct_bg', esc_html__('Background color', 'essb'), '');
	ESSBOptionsStructureHelper::field_color('style', 'cct', 'customize_cct_bg_hover', esc_html__('Background color no hover', 'essb'), '');
	
	ESSBOptionsStructureHelper::field_color('style', 'cct', 'customize_cct_color', esc_html__('Text color', 'essb'), '');
	ESSBOptionsStructureHelper::field_color('style', 'cct', 'customize_cct_color_hover', esc_html__('Text color no hover', 'essb'), '');
	
	ESSBOptionsStructureHelper::field_textbox('style', 'cct', 'customizer_cct_border', esc_html__('Border', 'essb'), esc_html__('border-width border-style border-color|initial|inherit', 'essb'));
	ESSBOptionsStructureHelper::field_textbox('style', 'cct', 'customizer_cct_border_hover', esc_html__('Border on hover', 'essb'), esc_html__('border-width border-style border-color|initial|inherit', 'essb'));
	
	ESSBOptionsStructureHelper::field_textbox('style', 'cct', 'customizer_cct_border_radius', esc_html__('Border radius', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox('style', 'cct', 'customizer_cct_padding', esc_html__('Padding', 'essb'), '');
	ESSBOptionsStructureHelper::field_select('style', 'cct', 'customizer_cct_align', esc_html__('Alignment', 'essb'), '', 
		array('' => esc_html__('Default', 'essb'), 'left' => esc_html__('Left', 'essb'), 'center' => esc_html__('Center', 'essb'), 'right' => esc_html__('Right', 'essb'))		
	);
	ESSBOptionsStructureHelper::field_textbox('style', 'cct', 'customizer_cct_fontsize', esc_html__('Font size', 'essb'), '');
	
	ESSBOptionsStructureHelper::panel_end('style', 'cct');
	
}


function essb3_prepare_color_customization_by_network($tab_id, $menu_id) {
	global $essb_networks;

	$checkbox_list_networks = array();
	foreach ($essb_networks as $key => $object) {
		$checkbox_list_networks[$key] = $object['name'];
	}

	foreach ($checkbox_list_networks as $key => $text) {
		ESSBOptionsStructureHelper::panel_start($tab_id, $menu_id, $text, esc_html__('Configure additional options for this network', 'essb'), 'fa21 essb_icon_'.$key, array("mode" => "toggle", 'state' => 'closed'));
		ESSBOptionsStructureHelper::field_section_start_full_panels($tab_id, $menu_id);
		ESSBOptionsStructureHelper::field_color_panel($tab_id, $menu_id, 'customizer_'.$key.'_bgcolor', esc_html__('Background color', 'essb'), '');
		ESSBOptionsStructureHelper::field_color_panel($tab_id, $menu_id, 'customizer_'.$key.'_textcolor', esc_html__('Text color', 'essb'), '');
		ESSBOptionsStructureHelper::field_color_panel($tab_id, $menu_id, 'customizer_'.$key.'_hovercolor', esc_html__('Hover background color', 'essb'), '');
		ESSBOptionsStructureHelper::field_color_panel($tab_id, $menu_id, 'customizer_'.$key.'_hovertextcolor', esc_html__('Hover text color', 'essb'), '');
		ESSBOptionsStructureHelper::field_section_end_full_panels($tab_id, $menu_id);
		ESSBOptionsStructureHelper::field_file($tab_id, $menu_id, 'customizer_'.$key.'_icon', esc_html__('Icon', 'essb'), '');
		ESSBOptionsStructureHelper::field_textbox($tab_id, $menu_id, 'customizer_'.$key.'_iconbgsize', esc_html__('Background size for regular icon', 'essb'), esc_html__('Provide custom background size if needed (for retina templates default used is 21px 21px)', 'essb'));
		ESSBOptionsStructureHelper::field_file($tab_id, $menu_id, 'customizer_'.$key.'_hovericon', esc_html__('Hover icon', 'essb'), '');
		ESSBOptionsStructureHelper::field_textbox($tab_id, $menu_id, 'customizer_'.$key.'_hovericonbgsize', esc_html__('Hover background size for regular icon', 'essb'), esc_html__('Provide custom background size if needed (for retina templates default used is 21px 21px)', 'essb'));
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
	
	/**
	 * Load the SVG icons if not present
	 */
	if (!class_exists('ESSB_SVG_Icons')) {
	    include_once (ESSB3_CLASS_PATH . 'assets/class-svg-icons.php');
	}
	
	ESSBOptionsStructureHelper::field_heading($tab_id, $menu_id, 'heading5', 'All networks');
	ESSBOptionsStructureHelper::field_section_start_full_panels($tab_id, $menu_id);
	ESSBOptionsStructureHelper::field_color($tab_id, $menu_id, 'fanscustomizer_all', esc_html__('Regular', 'essb'), '');
	ESSBOptionsStructureHelper::field_color($tab_id, $menu_id, 'fanscustomizer_hover_all', esc_html__('Hover', 'essb'), '');
	ESSBOptionsStructureHelper::field_section_end_full_panels($tab_id, $menu_id);
	
	foreach ($network_list as $network => $title) {
	    
	    if (ESSBSocialFollowersCounterHelper::is_deprecated_network($network)) {
	        continue;
	    }
	    
	    $title = '<span class="essb-svg-size18">'.ESSB_SVG_Icons::get_icon($network) . '</span> ' . $title;
	    
	    ESSBOptionsStructureHelper::field_heading($tab_id, $menu_id, 'heading5', $title);
	    ESSBOptionsStructureHelper::field_section_start_full_panels($tab_id, $menu_id);
	    ESSBOptionsStructureHelper::field_color($tab_id, $menu_id, 'fanscustomizer_'.$network, esc_html__('Regular', 'essb'), '');
	    ESSBOptionsStructureHelper::field_color($tab_id, $menu_id, 'fanscustomizer_hover_'.$network, esc_html__('Hover', 'essb'), '');
	    ESSBOptionsStructureHelper::field_section_end_full_panels($tab_id, $menu_id);
	}
}

function essb3_draw_profiles_customization($tab_id, $menu_id) {
	$network_list = ESSBSocialProfilesHelper::available_social_networks();

	ESSBOptionsStructureHelper::field_heading($tab_id, $menu_id, 'heading5', 'All networks');
	ESSBOptionsStructureHelper::field_section_start_full_panels($tab_id, $menu_id);
	ESSBOptionsStructureHelper::field_color($tab_id, $menu_id, 'profilecustomize_all', esc_html__('Regular', 'essb'), '');
	ESSBOptionsStructureHelper::field_color($tab_id, $menu_id, 'profilecustomize_hover_all', esc_html__('Hover', 'essb'), '');
	ESSBOptionsStructureHelper::field_section_end_full_panels($tab_id, $menu_id);
	
	/**
	 * Load the SVG icons if not present
	 */
	if (!class_exists('ESSB_SVG_Icons')) {
	    include_once (ESSB3_CLASS_PATH . 'assets/class-svg-icons.php');
	}
	
	foreach ($network_list as $network => $title) {
	    
	    if (ESSBSocialFollowersCounterHelper::is_deprecated_network($network)) {
	        continue;
	    }	    
	    
	    $title = '<span class="essb-svg-size18">'.ESSB_SVG_Icons::get_icon($network) . '</span> ' . $title;
	    
	    ESSBOptionsStructureHelper::field_heading($tab_id, $menu_id, 'heading5', $title);	    
	    ESSBOptionsStructureHelper::field_section_start_full_panels($tab_id, $menu_id);
	    ESSBOptionsStructureHelper::field_color($tab_id, $menu_id, 'profilecustomize_'.$network, esc_html__('Regular', 'essb'), '');
	    ESSBOptionsStructureHelper::field_color($tab_id, $menu_id, 'profilecustomize_hover_'.$network, esc_html__('Hover', 'essb'), '');
	    ESSBOptionsStructureHelper::field_section_end_full_panels($tab_id, $menu_id);
	}
}

/** Custom Share Buttons Template Code */

ESSBOptionsStructureHelper::field_switch('style', 'my-template', 'mytemplate_activate', esc_html__('Use my template', 'essb'), esc_html__('Set this option to Yes if you wish to use the custom template you made', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));

ESSBOptionsStructureHelper::panel_start('style', 'my-template', esc_html__('Total counter style', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::field_section_start_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_totalbgcolor', esc_html__('Background color', 'essb'), esc_html__('Replace total counter background color', 'essb'));
ESSBOptionsStructureHelper::field_switch_panel('style', 'my-template', 'mytemplate_totalnobgcolor', esc_html__('Remove background color', 'essb'), esc_html__('Activate this option to remove the background color', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_totalcolor', esc_html__('Text color', 'essb'), esc_html__('Replace total counter text color', 'essb'));
ESSBOptionsStructureHelper::field_section_end_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::field_section_start_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::field_textbox_panel('style', 'my-template', 'mytemplate_totalfontsize', esc_html__('Total counter big style font-size', 'essb'), esc_html__('Enter value in px (ex: 21px) to change the total counter font-size', 'essb'));
ESSBOptionsStructureHelper::field_textbox_panel('style', 'my-template', 'mytemplate_totalfontsize_after', esc_html__('Total counter big style shares text font-size', 'essb'), esc_html__('Enter value in px (ex: 10px) to change the total counter shares text font-size', 'essb'));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_totalfontsize_after_color', esc_html__('Total counter big style shares text color', 'essb'), '');
ESSBOptionsStructureHelper::field_textbox_panel('style', 'my-template', 'mytemplate_totalfontsize_beforeafter', esc_html__('Total counter before/after share buttons text font-size', 'essb'), esc_html__('Enter value in px (ex: 14px) to change the total counter text font-size', 'essb'));
ESSBOptionsStructureHelper::field_section_end_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::panel_end('style', 'my-template');


ESSBOptionsStructureHelper::panel_start('style', 'my-template', esc_html__('Button Style', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::title('style', 'my-template',  esc_html__('Icon Size, Font Size & Button Size', 'essb'), '', 'inner-row');
ESSBOptionsStructureHelper::field_section_start_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::field_textbox_panel('style', 'my-template', 'mytemplate_iconsize', esc_html__('Icon Size', 'essb'), esc_html__('Provide custom icon size value. Numeric value (example: 24) - default value is: 18', 'essb'));
ESSBOptionsStructureHelper::field_textbox_panel('style', 'my-template', 'mytemplate_iconspace', esc_html__('Icon Gutter', 'essb'), esc_html__('Default value used here is 9 (9px from each side) and you can change this according to effect you wish to have. This value will also reflect over the size of button.', 'essb'));
ESSBOptionsStructureHelper::field_textbox_panel('style', 'my-template', 'mytemplate_namesize', esc_html__('Network name font size', 'essb'), esc_html__('Enter custom network name font size (example: 16px, 2em, 5rem)', 'essb'));
ESSBOptionsStructureHelper::field_textbox_panel('style', 'my-template', 'mytemplate_padding', esc_html__('Button Padding', 'essb'), esc_html__('Set custom padding of button if you wish to get a bigger button. Supporting the CSS standard padding values (10px, 10px 10px).', 'essb'));
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_nameweight', esc_html__('Network name text style', 'essb'), '', array("" => "Default", "normal" => "Normal", "bold" => "Bold", "italic" => "Italic"));
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_texttrans', esc_html__('Network name text transform', 'essb'), '', array("" => "Default", "uppercase" => "Uppercase", "capitalaize" => "Capitalize"));
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_shape', esc_html__('Button Shape', 'essb'), esc_html__('Customize the shape of button', 'essb'), array("" => "Default", "rounded" => "Round Edges", "round" => "Round", "leaf" => "Leaf"));
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_effect', esc_html__('Button Effect', 'essb'), esc_html__('Add custom button effect', 'essb'), array("" => "No effect", "flat" => "Flat style", "shadow" => "Drop shadow", "glow" => "Glow effect"));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_effect_color', esc_html__('Button Effect Color', 'essb'), esc_html__('Replace default effect color', 'essb'), '', 'true');
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_effect_strength', esc_html__('Button Effect Strength', 'essb'), esc_html__('Correct strength of button effect (when activated)', 'essb'), array("" => "Default", "small" => "Small", "medium" => "Medium", "large" => "Large", 'xlarge' => "Extra Large"));

ESSBOptionsStructureHelper::field_section_end_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::panel_end('style', 'my-template');

ESSBOptionsStructureHelper::panel_start('style', 'my-template', esc_html__('Default Color Set', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::title('style', 'my-template',  esc_html__('Default Color Set', 'essb'), '', 'inner-row');
ESSBOptionsStructureHelper::field_section_start_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_default_color', esc_html__('Background Color', 'essb'), esc_html__('Choose the default background color of button', 'essb'), array("" => "Network default/custom color", "white" => "White", "dark" => "Dark", "custom" => "Custom Color"));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_default_color_custom', esc_html__('Custom Background Color', 'essb'), esc_html__('Custom background color will be used when you select from menu to use custom color. It will be set for all social networks that does not have own custom color', 'essb'));
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_default_textcolor', esc_html__('Text/Icon Color', 'essb'), esc_html__('Choose the default text/icon color of button', 'essb'), array("" => "White", "network" => "Network default/custom color", "dark" => "Dark", "custom" => "Custom Color"));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_default_textcolor_custom', esc_html__('Custom Text/Icon Color', 'essb'), esc_html__('Custom text/icon color will be used when you select from menu to use custom color. It will be set for all social networks that does not have own custom color', 'essb'));
ESSBOptionsStructureHelper::field_textbox_panel('style', 'my-template', 'mytemplate_default_outlinesize', esc_html__('Outline Size', 'essb'), esc_html__('Setup outline over the share button. To do this and color setup work enter numeric value for size (1, 2, 3 and etc.)', 'essb'));
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_default_outlinecolor', esc_html__('Outline Color', 'essb'), esc_html__('Choose the default outline color of button', 'essb'), array("" => "Network default/custom color", "white" => "White", "dark" => "Dark", "custom" => "Custom Color"));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_default_outlinecolor_custom', esc_html__('Custom Outline Color', 'essb'), esc_html__('Custom outline color will be used when you select from menu to use custom color. It will be set for all social networks that does not have own custom color', 'essb'));
ESSBOptionsStructureHelper::field_section_end_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::panel_end('style', 'my-template');

ESSBOptionsStructureHelper::panel_start('style', 'my-template', esc_html__('On Hover Color Set', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
ESSBOptionsStructureHelper::title('style', 'my-template',  esc_html__('On Hover Color Set', 'essb'), '', 'inner-row');
ESSBOptionsStructureHelper::field_section_start_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_hover_color', esc_html__('Background Color', 'essb'), esc_html__('Choose the default background color of button', 'essb'), array("" => "Generate automatically", "network" => "Network default/custom color", "white" => "White", "dark" => "Dark", "custom" => "Custom Color"));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_hover_color_custom', esc_html__('Custom Background Color', 'essb'), esc_html__('Custom background color will be used when you select from menu to use custom color. It will be set for all social networks that does not have own custom color', 'essb'));
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_hover_textcolor', esc_html__('Text/Icon Color', 'essb'), esc_html__('Choose the default text/icon color of button', 'essb'), array("" => "White", "network" => "Network default/custom color", "dark" => "Dark", "custom" => "Custom Color"));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_hover_textcolor_custom', esc_html__('Custom Text/Icon Color', 'essb'), esc_html__('Custom text/icon color will be used when you select from menu to use custom color. It will be set for all social networks that does not have own custom color', 'essb'));
ESSBOptionsStructureHelper::field_textbox_panel('style', 'my-template', 'mytemplate_hover_outlinesize', esc_html__('Outline Size', 'essb'), esc_html__('Setup outline over the share button. To do this and color setup work enter numeric value for size (1, 2, 3 and etc.)', 'essb'));
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_hover_outlinecolor', esc_html__('Outline Color', 'essb'), esc_html__('Choose the default outline color of button', 'essb'), array("" => "Network default/custom color", "white" => "White", "dark" => "Dark", "custom" => "Custom Color"));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_hover_outlinecolor_custom', esc_html__('Custom Outline Color', 'essb'), esc_html__('Custom outline color will be used when you select from menu to use custom color. It will be set for all social networks that does not have own custom color', 'essb'));

ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_hover_color_effect', esc_html__('Button Hover Effect', 'essb'), esc_html__('Set eye catching hover animation', 'essb'), array("" => "No hover animation", "shiny" => "Shiny"));


ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_hover_shape', esc_html__('Button Shape', 'essb'), esc_html__('Customize the shape of button', 'essb'), array("" => "Default", "square" => "Rectangle", "rounded" => "Round Edges", "round" => "Round", "leaf" => "Leaf"));
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_hover_effect', esc_html__('Button Effect', 'essb'), esc_html__('Add custom button effect', 'essb'), array("" => "Default", "no" => "No effect", "flat" => "Flat style", "shadow" => "Drop shadow", "glow" => "Glow effect"));
ESSBOptionsStructureHelper::field_color_panel('style', 'my-template', 'mytemplate_hover_effect_color', esc_html__('Button Effect Color', 'essb'), esc_html__('Replace default effect color', 'essb'), '', 'true');
ESSBOptionsStructureHelper::field_select_panel('style', 'my-template', 'mytemplate_hover_effect_strength', esc_html__('Button Effect Strength', 'essb'), esc_html__('Correct strength of button effect (when activated)', 'essb'), array("" => "Default", "small" => "Small", "medium" => "Medium", "large" => "Large", 'xlarge' => "Extra Large"));

ESSBOptionsStructureHelper::field_section_end_full_panels('style', 'my-template');
ESSBOptionsStructureHelper::panel_end('style', 'my-template');

ESSBOptionsStructureHelper::panel_start('style', 'my-template', esc_html__('Colors by Social Networks', 'essb'), esc_html__('Use this option if you wish to personalize social network individual colors.', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'mytemplate_network_is_active', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb'), 'switch_submit' => 'true'));

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
		ESSBOptionsStructureHelper::field_color_panel($tab_id, $menu_id, 'mytemplate_'.$key.'_bgcolor', esc_html__('Background color', 'essb'), esc_html__('Replace all buttons background color', 'essb'));
		ESSBOptionsStructureHelper::field_color_panel($tab_id, $menu_id, 'mytemplate_'.$key.'_textcolor', esc_html__('Text color', 'essb'), esc_html__('Replace all buttons text color', 'essb'));
		ESSBOptionsStructureHelper::field_color_panel($tab_id, $menu_id, 'mytemplate_'.$key.'_hovercolor', esc_html__('Hover background color', 'essb'), esc_html__('Replace all buttons hover background color', 'essb'));
		ESSBOptionsStructureHelper::field_color_panel($tab_id, $menu_id, 'mytemplate_'.$key.'_hovertextcolor', esc_html__('Hover text color', 'essb'), esc_html__('Replace all buttons hover text color', 'essb'));
		ESSBOptionsStructureHelper::field_section_end_full_panels($tab_id, $menu_id);
		ESSBOptionsStructureHelper::holder_end($tab_id, $menu_id);
	}
}

ESSBOptionsStructureHelper::panel_end('style', 'my-template');
