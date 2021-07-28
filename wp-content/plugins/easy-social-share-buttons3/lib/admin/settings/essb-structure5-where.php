<?php
// -- Where to display buttons
$where_to_display = 'where';

if (class_exists('ESSBControlCenter')) {
	ESSBControlCenter::register_sidebar_section_menu($where_to_display, 'posts', esc_html__('Post Types', 'essb'));
	ESSBControlCenter::register_sidebar_section_menu($where_to_display, 'positions', esc_html__('Positions', 'essb'));
	ESSBControlCenter::register_sidebar_section_menu_sub($where_to_display, 'positions', 'positions_title', array('type' => 'title', 'value' => esc_html__('Share Button Positions', 'essb')));
	ESSBControlCenter::register_sidebar_section_menu_sub($where_to_display, 'positions', 'positions_menu', array('type' => 'menu', 'value' => array('positions' => esc_html__('Positions', 'essb'), 'positions_posttype' => esc_html__('Different Positions by Post Type', 'essb'))));
	ESSBControlCenter::register_sidebar_section_menu_sub($where_to_display, 'positions', 'positions_menu_split1', array('type' => 'splitter', 'value' => 'Position Settings', 'class' => ''));		
	ESSBControlCenter::register_sidebar_section_menu_sub($where_to_display, 'positions', 'positions_desc1', array('type' => 'description', 'value' => esc_html('Configure additional options for the display positions you will use on your site.', 'essb')));
	
	
	$position_setup_list = array();
	$position_setup_list['display-4'] = esc_html__('Content Top', 'essb');
	$position_setup_list['display-5'] = esc_html__('Content Bottom', 'essb');
	
	if (!essb_options_bool_value('deactivate_method_float')) {
		$position_setup_list['display-6'] = esc_html__('Float From Top', 'essb');
	}
	
	if (!essb_options_bool_value('deactivate_method_followme')) {
		$position_setup_list['display-18'] = esc_html__('Follow me Share Bar', 'essb');
	}
	
	if (!essb_options_bool_value('deactivate_method_corner')) {
		$position_setup_list['display-19'] = esc_html__('Corner Bar', 'essb');
	}
	
	if (!essb_options_bool_value('deactivate_method_booster')) {
		$position_setup_list['display-20'] = esc_html__('Share Booster', 'essb');
	}
	
	if (!essb_options_bool_value('deactivate_method_sharebutton')) {
		$position_setup_list['display-21'] = esc_html__('Share Button', 'essb');
	}
	
	if (!essb_options_bool_value('deactivate_method_postfloat')) {
		$position_setup_list['display-7'] = esc_html__('Post Vertical Float', 'essb');
	}
	
	if (!essb_options_bool_value('deactivate_method_sidebar')) {
		$position_setup_list['display-8'] = esc_html__('Sidebar', 'essb');
	}
	
	if (!essb_options_bool_value('deactivate_method_topbar')) {
		$position_setup_list['display-9'] = esc_html__('Top Bar', 'essb');
	}
	
	if (!essb_options_bool_value('deactivate_method_bottombar')) {
		$position_setup_list['display-10'] = esc_html__('Bottom Bar', 'essb');
	}
	
	if (!essb_options_bool_value('deactivate_method_popup')) {
		$position_setup_list['display-11'] = esc_html__('Pop up', 'essb');
	}
	
	if (!essb_options_bool_value('deactivate_method_flyin')) {
		$position_setup_list['display-12'] = esc_html__('Fly in', 'essb');
	}
	
	if (!essb_option_bool_value('deactivate_method_image')) {
		$position_setup_list['display-13'] = esc_html__('On media', 'essb');
	}
	
	if (!essb_options_bool_value('deactivate_method_heroshare')) {
		$position_setup_list['display-14'] = esc_html__('Full Screen Hero Share', 'essb');
	}
	
	if (!essb_options_bool_value('deactivate_method_postbar')) {
		$position_setup_list['display-15'] = esc_html__('Post Share Bar', 'essb');
	}
	
	if (!essb_options_bool_value('deactivate_method_point')) {
		$position_setup_list['display-16'] = esc_html__('Point', 'essb');
	}
	
	$position_setup_list['display-17'] = esc_html__('Excerpt', 'essb');
	
	// @since 4.1 allow usage of external display methods
	$essb_custom_methods = array();
	$essb_custom_methods = apply_filters('essb4_custom_method_list', $essb_custom_methods);
	foreach ($essb_custom_methods as $key => $title) {
		$position_setup_list[$key] = $title;
	}
	
	ESSBControlCenter::register_sidebar_section_menu_sub($where_to_display, 'positions', 'positions_setup_menu', array('type' => 'menu', 'value' => $position_setup_list));
	
	if (!essb_option_bool_value('activate_automatic_mobile')) {
		ESSBControlCenter::register_sidebar_section_menu($where_to_display, 'mobile', esc_html__('Mobile', 'essb'));
		
		if (essb_option_bool_value('mobile_positions') && essb_option_value('functions_mode_mobile') != 'auto' && essb_option_value('functions_mode_mobile') != 'deactivate') {
			
			$mobile_positions = array('bar' => esc_html__('Mobile Share Bar', 'essb'), 'point' => esc_html__('Mobile Share Point', 'essb'), 'buttons' => esc_html__('Mobile Share Buttons Bar', 'essb'), 'other' => esc_html('Customize Other Positions When Shown on Mobile', 'essb'));
			$essb_custom_methods = array();
			$essb_custom_methods = apply_filters('essb4_custom_method_list_mobile', $essb_custom_methods);
			foreach ($essb_custom_methods as $key => $title) {
				$mobile_positions[$key] = $title;
			}
			
			ESSBControlCenter::register_sidebar_section_menu_sub($where_to_display, 'mobile', 'mobile_title', array('type' => 'title', 'value' => esc_html__('Mobile Display', 'essb')));
			ESSBControlCenter::register_sidebar_section_menu_sub($where_to_display, 'mobile', 'mobile_desc1', array('type' => 'description', 'value' => esc_html('Configure the appearance of the share buttons when a mobile display mode is active. If you are not sure about your setup than activate one of the mobile-optimized displays in combination with responsive mode.', 'essb')));
			ESSBControlCenter::register_sidebar_section_menu_sub($where_to_display, 'mobile', 'mobile_menu', array('type' => 'menu', 'value' => array('setup' => esc_html__('Setup', 'essb'), 'responsive' => esc_html__('Responsive Mode', 'essb'))));
			ESSBControlCenter::register_sidebar_section_menu_sub($where_to_display, 'mobile', 'mobile_menu_split1', array('type' => 'splitter', 'value' => '', 'class' => 'no-pt'));
			ESSBControlCenter::register_sidebar_section_menu_sub($where_to_display, 'mobile', 'mobile_desc2', array('type' => 'description', 'value' => esc_html('Configure additional visual options and network setups related to the dedicated mobile display.', 'essb')));
			ESSBControlCenter::register_sidebar_section_menu_sub($where_to_display, 'mobile', 'mobile_menu2', array('type' => 'menu', 'value' => $mobile_positions));
			ESSBControlCenter::register_sidebar_section_menu_sub($where_to_display, 'mobile', 'mobile_menu_split2', array('type' => 'splitter', 'value' => '', 'class' => 'no-pt'));
			ESSBControlCenter::register_sidebar_section_menu_sub($where_to_display, 'mobile', 'mobile_help_btn1', array('type' => 'button', 'value' => array( 'text' => esc_html__('How to setup responsive buttons', 'essb'), 'url' => 'https://docs.socialsharingplugin.com/knowledgebase/how-to-active-mobile-responsive-share-buttons-mode/', 'target' => '_blank')));
				
		}
	}
	
	if ((function_exists('is_amp_endpoint') || function_exists('ampforwp_is_amp_endpoint')) && essb_option_value('functions_mode') != 'light') {
		ESSBControlCenter::register_sidebar_section_menu($where_to_display, 'amp', esc_html__('AMP', 'essb'));
	}
	
	if (!essb_option_bool_value('deactivate_method_integrations')) {
		ESSBControlCenter::register_sidebar_section_menu($where_to_display, 'plugins', esc_html__('Plugins Integration', 'essb'));
	}
	
	if (!essb_option_bool_value('deactivate_custompositions')) {
		ESSBControlCenter::register_sidebar_section_menu($where_to_display, 'custom', esc_html__('Custom Positions / Displays', 'essb'));
	}
	
	ESSBControlCenter::register_sidebar_section_menu($where_to_display, 'deactivate', esc_html__('Deactivate Display', 'essb'));	
}

ESSBOptionsStructureHelper::menu_item($where_to_display, 'posts', esc_html__('Post Types', 'essb'), ' ti-widget-alt');
ESSBOptionsStructureHelper::menu_item($where_to_display, 'positions', esc_html__('Positions', 'essb'), ' ti-widget-alt');
ESSBOptionsStructureHelper::menu_item($where_to_display, 'display', esc_html__('Position Settings', 'essb'), ' ti-layout-grid2-alt', 'activate_first', 'positions|display-4');
ESSBOptionsStructureHelper::submenu_item($where_to_display, 'positions|display-4', esc_html__('Content Top', 'essb'),  'default', 'menu');
ESSBOptionsStructureHelper::submenu_item($where_to_display, 'positions|display-5', esc_html__('Content Bottom', 'essb'),  'default', 'menu', '');
if (!essb_options_bool_value('deactivate_method_float')) {
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'display-6', esc_html__('Float from top', 'essb'),  'default', 'menu', '');
}

if (!essb_options_bool_value('deactivate_method_followme')) {
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'display-18', esc_html__('Follow me share bar', 'essb'),  'default', 'menu', '');
}

if (!essb_options_bool_value('deactivate_method_corner')) {
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'display-19', esc_html__('Corner Bar', 'essb'),  'default', 'menu', '');
}

if (!essb_options_bool_value('deactivate_method_booster')) {
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'display-20', esc_html__('Share Booster', 'essb'),  'default', 'menu', '');
}

if (!essb_options_bool_value('deactivate_method_sharebutton')) {
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'display-21', esc_html__('Share Button', 'essb'),  'default', 'menu', '');
}


if (!essb_options_bool_value('deactivate_method_postfloat')) {
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'display-7', esc_html__('Post vertical float', 'essb'),  'default', 'menu', '');
}
if (!essb_options_bool_value('deactivate_method_sidebar')) {
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'display-8', esc_html__('Sidebar', 'essb'),  'default', 'menu', '');
}
if (!essb_options_bool_value('deactivate_method_topbar')) {
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'display-9', esc_html__('Top bar', 'essb'),  'default', 'menu', '');
}
if (!essb_options_bool_value('deactivate_method_bottombar')) {
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'display-10', esc_html__('Bottom bar', 'essb'),  'default', 'menu', '');
}

if (!essb_options_bool_value('deactivate_method_popup')) {
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'display-11', esc_html__('Pop up', 'essb'),  'default', 'menu', '');
}

if (!essb_options_bool_value('deactivate_method_flyin')) {
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'display-12', esc_html__('Fly in', 'essb'),  'default', 'menu', '');
}

if (!essb_option_bool_value('deactivate_method_image')) {
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'display-13', esc_html__('On media', 'essb'),  'default', 'menu', '');
}

if (!essb_options_bool_value('deactivate_method_heroshare')) {
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'display-14', esc_html__('Full screen hero share', 'essb'),  'default', 'menu', '');
}

if (!essb_options_bool_value('deactivate_method_postbar')) {
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'display-15', esc_html__('Post share bar', 'essb'),  'default', 'menu', '');
}
if (!essb_options_bool_value('deactivate_method_point')) {
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'display-16', esc_html__('Point', 'essb'),  'default', 'menu', '');
}
ESSBOptionsStructureHelper::submenu_item($where_to_display, 'display-17', esc_html__('Excerpt', 'essb'),  'default', 'menu', '');

// @since 4.1 allow usage of external display methods
$essb_custom_methods = array();
$essb_custom_methods = apply_filters('essb4_custom_method_list', $essb_custom_methods);
foreach ($essb_custom_methods as $key => $title) {
	ESSBOptionsStructureHelper::submenu_item($where_to_display, $key, $title,  'default', 'menu', '', 'display');
}

if (!essb_option_bool_value('activate_automatic_mobile')) {
	ESSBOptionsStructureHelper::menu_item($where_to_display, 'mobile', esc_html__('Mobile', 'essb'), ' ti-mobile', 'activate_first', 'mobile-1');
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'mobile-1', esc_html__('Display Options', 'essb'));
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'mobile-2', esc_html__('Customize buttons when viewed from mobile device', 'essb'));
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'mobile-3', esc_html__('Share bar', 'essb'));
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'mobile-4', esc_html__('Share point', 'essb'));
	ESSBOptionsStructureHelper::submenu_item($where_to_display, 'mobile-5', esc_html__('Share buttons bar', 'essb'));

	$essb_custom_methods = array();
	$essb_custom_methods = apply_filters('essb4_custom_method_list_mobile', $essb_custom_methods);
	foreach ($essb_custom_methods as $key => $title) {
		ESSBOptionsStructureHelper::submenu_item($where_to_display, $key, $title);
	}
}

if ((function_exists('is_amp_endpoint') || function_exists('ampforwp_is_amp_endpoint')) && essb_option_value('functions_mode') != 'light') {
	ESSBOptionsStructureHelper::menu_item($where_to_display, 'amp', esc_html__('AMP Sharing', 'essb'), ' ti-widget-alt');

}

if (!essb_option_bool_value('deactivate_method_integrations')) {
	ESSBOptionsStructureHelper::menu_item($where_to_display, 'plugins', esc_html__('Plugin Integrations', 'essb'), ' ti-widget-alt');
}

// show or not custom positions
if (!essb_option_bool_value('deactivate_custompositions')) {
	ESSBOptionsStructureHelper::help($where_to_display, 'custom', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/creating-custom-displays-positions-for-share-buttons-and-how-to-use-them/'));
	
	ESSBOptionsStructureHelper::menu_item($where_to_display, 'custom', esc_html__('Custom Positions/Displays', 'essb'), ' ti-widget-alt');
	ESSBOptionsStructureHelper::field_heading($where_to_display, 'custom', 'heading5', esc_html__('Custom Positions/Custom Share Buttons Display', 'essb'), esc_html__('Add and register custom share button positions. Those positions you can easy use anywhere inside content with shortcode or easy hook inside any code with a function call. The main advantage of locations/displays is that you easy change settings like any of existing positions. And changing the design will change it anywhere you have used it before. If you temporary need to stop a display just deactivate it from the positions menu.', 'essb'));
	ESSBOptionsStructureHelper::field_component($where_to_display, 'custom', 'essb5_custom_positions', 'true');
}

/*** Where to Display **/
ESSBOptionsStructureHelper::help($where_to_display, 'posts', '', '', array('Help with Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/automatic-display-of-share-buttons-on-different-post-types-plugins-and-automatic-deactivate-of-display/#Selecting_Post_Types_for_Automatic_Display'));

ESSBOptionsStructureHelper::field_func($where_to_display, 'posts', 'essb3_post_type_select', esc_html__('Display on', 'essb'), esc_html__('Choose post types where you wish buttons to appear. If you are running a WooCommerce store you can choose between post type Products which will display share buttons into product description or option to display buttons below the price.', 'essb'));
ESSBOptionsStructureHelper::field_component($where_to_display, 'posts', 'essb5_additional_excerpt_options', 'false');

ESSBOptionsStructureHelper::help($where_to_display, 'deactivate', '', '', array('Help with Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/automatic-display-of-share-buttons-on-different-post-types-plugins-and-automatic-deactivate-of-display/#Automated_Deactivate_of_Display'));
ESSBOptionsStructureHelper::field_textbox_stretched($where_to_display, 'deactivate', 'display_exclude_from', esc_html__('Exclude automatic display on', 'essb'), esc_html__('Exclude buttons on posts/pages with these IDs. Comma separated: "11, 15, 125". This will deactivate automated display of buttons on selected posts/pages but you are able to use shortcode on them.', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched($where_to_display, 'deactivate', 'display_deactivate_on', esc_html__('Deactivate plugin on', 'essb'), esc_html__('Deactivate buttons on posts/pages with these IDs. Comma separated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts. Plugin also allows to deactivate only specific functions on selected page/post ids.', 'essb'));
ESSBOptionsStructureHelper::field_switch($where_to_display, 'deactivate', 'deactivate_homepage', esc_html__('Deactivate buttons display on homepage', 'essb'), esc_html__('Exclude display of buttons on home page.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch($where_to_display, 'deactivate', 'deactivate_mobile', esc_html__('Deactivate plugin on mobile', 'essb'), esc_html__('Deactivate completely plugin work on mobile. The option requires server-side mobile detection to work.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch($where_to_display, 'deactivate', 'deactivate_mobile_share', esc_html__('Deactivate share buttons on mobile', 'essb'), esc_html__('Deactivate display of share buttons on mobile. The option requires server-side mobile detection to work.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch($where_to_display, 'deactivate', 'hide_content_archive', esc_html__('Hide content share buttons from archive pages', 'essb'), esc_html__('Fully deactivate only the content share buttons on archive pages (category, tag, author, etc.). All other display methods will continue to work.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
ESSBOptionsStructureHelper::field_switch($where_to_display, 'deactivate', 'hide_content_home', esc_html__('Hide content share buttons on the homepage', 'essb'), esc_html__('Fully deactivate only the content share buttons on the homepage. All other display methods will continue to work.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));

ESSBOptionsStructureHelper::field_textarea($where_to_display, 'deactivate', 'url_deactivate_share', esc_html__('Specify URLs of pages or posts where share buttons will not appear', 'essb'), esc_html__('The option will deactivate only the share buttons. All other plugin components will continue to work. One per line without the domain name. Use (.*) wildcards to address multiple URLs under a given path. Example: /profile/(.*)', 'essb'));
ESSBOptionsStructureHelper::field_textarea($where_to_display, 'deactivate', 'url_deactivate_full', esc_html__('Specify URLs of pages or posts where the plugin will not appear ', 'essb'), esc_html__('The option will deactivate all plugin functions. One per line without the domain name. Use (.*) wildcards to address multiple URLs under a given path. Example: /profile/(.*)', 'essb'));


ESSBOptionsStructureHelper::field_component($where_to_display, 'deactivate', 'essb5_additional_deactivate_options', 'false');

ESSBOptionsStructureHelper::field_heading($where_to_display, 'posts', 'heading5', esc_html__('Automatic display on', 'essb'));
ESSBOptionsStructureHelper::field_textbox_stretched($where_to_display, 'posts', 'display_include_on', esc_html__('Automatic display buttons on', 'essb'), esc_html__('Provide list of post/page ids where buttons will display no matter that post type is active or not. Comma seperated values: "11, 15, 125". This will eactivate automated display of buttons on selected posts/pages even if post type that they use is not marked as active.', 'essb'));

if (!essb_options_bool_value('deactivate_method_integrations')) {
	ESSBOptionsStructureHelper::help($where_to_display, 'plugins', '', '', array('Help with Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/working-with-automatic-display-positions-for-social-share-buttons-and-plugin-integrations/#Plugin_Integrations'));
	
	ESSBOptionsStructureHelper::panel_start($where_to_display, 'plugins', esc_html__('WooCommerce', 'essb'), esc_html__('Activate specific locations related to this plugin', 'essb'), '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'plugins', 'woocommece_share', esc_html__('Default WooCommerce hook for share buttons (after product brief description)', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'plugins', 'woocommerce_after_add_to_cart_form', esc_html__('After add to cart button', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'plugins', 'woocommece_beforeprod', esc_html__('Display buttons on top of product (before product)', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'plugins', 'woocommece_afterprod', esc_html__('Display buttons at the bottom of product (after product)', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'plugins');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'plugins', esc_html__('WP e-Commerce', 'essb'), esc_html__('Activate specific locations related to this plugin', 'essb'), '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'plugins', 'wpec_before_desc', esc_html__('Display before product description', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'plugins', 'wpec_after_desc', esc_html__('Display after product description', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'plugins', 'wpec_theme_footer', esc_html__('Display at the bottom of page', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'plugins');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'plugins', esc_html__('JigoShop', 'essb'), esc_html__('Activate specific locations related to this plugin', 'essb'), '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'plugins', 'jigoshop_top', esc_html__('JigoShop Before Product', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'plugins', 'jigoshop_bottom', esc_html__('JigoShop After Product', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'plugins');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'plugins', esc_html__('iThemes Exchange', 'essb'), esc_html__('Activate specific locations related to this plugin', 'essb'), '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'plugins', 'ithemes_after_title', esc_html__('Display after product title', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'plugins', 'ithemes_before_desc', esc_html__('Display before product description', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'plugins', 'ithemes_after_desc', esc_html__('Display after product description', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'plugins', 'ithemes_after_product', esc_html__('Display after product advanced content (after product information)', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'plugins', 'ithemes_after_purchase', esc_html__('Display share buttons for each product after successful purchase (when shopping cart is used)', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'plugins');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'plugins', esc_html__('bbPress', 'essb'), esc_html__('Activate specific locations related to this plugin', 'essb'), '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'plugins', 'bbpress_forum', esc_html__('Display in forums', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'plugins', 'bbpress_topic', esc_html__('Display in topics', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'plugins');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'plugins', esc_html__('BuddyPress', 'essb'), esc_html__('Activate specific locations related to this plugin', 'essb'), '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'plugins', 'buddypress_activity', esc_html__('Display in activity', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'plugins', 'buddypress_group', esc_html__('Display on group page', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'plugins');
}

// positions
ESSBOptionsStructureHelper::help($where_to_display, 'positions|positions', '', '', array('Help with Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/working-with-automatic-display-positions-for-social-share-buttons-and-plugin-integrations/'));
ESSBOptionsStructureHelper::field_component($where_to_display, 'positions|positions', 'essb5_advanced_additional_sharebutton_positions', 'false');

ESSBOptionsStructureHelper::field_heading($where_to_display, 'positions|positions', 'heading5', esc_html__('Inline Content Share Buttons', 'essb'), '');
ESSBOptionsStructureHelper::field_single_position_select($where_to_display, 'positions|positions', 'content_position', essb5_available_content_positions());

ESSBOptionsStructureHelper::field_heading($where_to_display, 'positions|positions', 'heading5', esc_html__('Additional Share Button Displays', 'essb'), '');
ESSBOptionsStructureHelper::field_multi_position_select($where_to_display, 'positions|positions', 'button_position', essb5_available_button_positions());


add_action('admin_init', 'essb3_register_positions_by_posttypes');

essb_prepare_location_advanced_customization($where_to_display, 'positions|display-4', 'top');
essb_prepare_location_advanced_customization($where_to_display, 'positions|display-5', 'bottom');

if (!essb_options_bool_value('deactivate_method_float')) {
	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-6', esc_html__('Floating Bar Fixed Position & Auto Hide', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-6');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-6', 'float_top', esc_html__('Top position for non logged in users', 'essb'), esc_html__('Customize default fixed position of floating bar. You may need to enter value here if your site has a fixed menu or other top fixed element (numeric value)', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-6', 'float_top_loggedin', esc_html__('Top position for logged in users', 'essb'), esc_html__('If you display WordPress admin bar for logged in users you can correct float from top position for logged in users to avoid bar to be rendered below WordPress admin bar.', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-6', 'float_top_disappear', esc_html__('Autohide after percent of content is viewed', 'essb'), esc_html__('Provide value in percent if you wish to hide float bar - for example 80 will make bar to disappear when 80% of page content is viewed from user.', 'essb'), '', 'input60', 'fa-sort-numeric-asc', 'right');
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-6');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-6');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-6', esc_html__('Background color of floating bar', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_panels($where_to_display, 'positions|display-6', esc_html__('Background color', 'essb'), esc_html__('Change default background color of float bar (default is white #FFFFFF).', 'essb'));
	ESSBOptionsStructureHelper::field_color_panel($where_to_display, 'positions|display-6', 'float_bg', esc_html__('Choose background color', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-6', 'float_bg_opacity', esc_html__('Change opacity of background color', 'essb'), esc_html__('Change default opacity of background color if you wish to have a semi-transparent effect (default is 1 full color). You can enter value between 0 and 1 (example: 0.7)', 'essb'), '', 'input60');
	ESSBOptionsStructureHelper::field_section_end_panels($where_to_display, 'positions|display-6');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-6');


	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-6', esc_html__('Content width inside floating bar & margins', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-6');
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-6', 'float_full', esc_html__('Set full width of float bar', 'essb'), esc_html__('This option will make float bar to take full width of browser window.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-6', 'float_full_maxwidth', esc_html__('Max width of buttons area', 'essb'), esc_html__('Provide custom max width of buttons area when full width float bar is active. Provide number value in pixels without the px (example 960)', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-6', 'float_remove_margin', esc_html__('Remove top space', 'essb'), esc_html__('This option will clear the blank space that may appear according to theme settings between top of window and float bar.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-6');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-6');

	essb_prepare_location_advanced_customization($where_to_display, 'positions|display-6', 'float');
}

if (!essb_options_bool_value('deactivate_method_postfloat')) {
	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-7', esc_html__('Correct Initial & Fixed Top Position', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-7');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-7', 'postfloat_initialtop', esc_html__('Custom top position of post float bar when loaded', 'essb'), esc_html__('Customize the initial top position of post float bar if you wish to be different from content start.', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-7', 'postfloat_top', esc_html__('Top position of post float buttons when they are fixed', 'essb'), esc_html__('Filled value to change the top position if you have another fixed element (example: fixed menu).', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-7');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-7');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-7', esc_html__('Change Horizontal & Vertical Offset from Content', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-7');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-7', 'postfloat_marginleft', esc_html__('Horizontal offset from content', 'essb'), esc_html__('You can provide custom left offset from content. Leave blank to use default value.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-7', 'postfloat_margintop', esc_html__('Vertical offset from content start', 'essb'), esc_html__('You can provide custom vertical offset from content start. Leave blank to use default value. (Negative values moves up, positve moves down).', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-7');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-7');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-7', esc_html__('Automatica Appear or Disappear On', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-7');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-7', 'postfloat_percent', esc_html__('Display after page scroll', 'essb'), esc_html__('Show the floating bar when visitors scroll on the page. You can set value in percents of content (example: 40) or equal to pixels from the top of the page (example: 500px). When this setting is used the floating bar will remain hidden till the condition is completed successfully.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-7', 'postfloat_bottom_offset', esc_html__('End of content offset', 'essb'), esc_html__('This will make buttons disappear before reaching the end of content with added the offset value. Works only if you do not activate the option to "Do not hide post vertical float at the end of content".', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-7', 'postfloat_always_visible', esc_html__('Do not hide post vertical float at the end of content', 'essb'), esc_html__('Activate this option to make post vertical float stay on screen when end of post content is reached.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-7', 'postfloat_fix_bottom', esc_html__('Fix the floating bar at the end of the content', 'essb'), esc_html__('Only when the do not hide option is enabled and not in combination with selectors hide. This will fix the bar at the bottom and stay there.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_stretched_panel($where_to_display, 'positions|display-7', 'postfloat_selectors', esc_html__('Hide when specific selectors are in the viewport', 'essb'), esc_html__('Fill one or more (separated with comma) selectors (optional). If one of those selectors is in the viewport the share buttons in the vertical float will disappear. The option will only work if you enable "Do not hide post vertical float at the end of content". Example: .class1, #id2', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-7');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-7');

	essb_prepare_location_advanced_customization($where_to_display, 'positions|display-7', 'postfloat');
}

if (!essb_options_bool_value('deactivate_method_sidebar')) {
	$listOfOptions = array("" => "Left", "right" => "Right");
	$sidebar_loading_animations = array("" => esc_html__("No animation", "essb"), "slide" => esc_html__("Slide", "essb"), "fade" => esc_html__("Fade", "essb"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-8');
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-8', 'sidebar_pos', esc_html__('Sidebar appearance', 'essb'), esc_html__('You choose different position for sidebar. Available options are Left (default), Right', 'essb'), $listOfOptions);
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-8', 'sidebar_entry_ani', esc_html__('Display animation', 'essb'), esc_html__('Assign sidebar initial appearance animation - a nice way to catch visitors attention.', 'essb'), $sidebar_loading_animations);
	$listOfOptions = array('' => 'Middle', 'top' => 'Top', 'bottom' => 'Bottom', 'custom' => 'Custom');
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-8', 'sidebar_location', esc_html__('Sidebar location on screen', 'essb'), esc_html__('Define where the sidebar will appear on the screen. You can choose a default middle location, top of the screen, bottom of screen or custom position.', 'essb'), $listOfOptions);
	$listOfOptions = array('' => 'Default', 'medium' => 'Medium', 'large' => 'Large', 'xlarge' => 'Extra Large');
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-8', 'sidebar_icon_space', esc_html__('Sidebar internal button spacing', 'essb'), esc_html__('Easily increase the space around icons in the share buttons. This option may be overwritten if you choose a different size of a button from the Advanced Position settings.', 'essb'), $listOfOptions);
	
	if (!essb_option_bool_value('activate_automatic_position')) {
		ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-8', 'sidebar_adaptive_style', esc_html__('Automatic sidebar styles', 'essb'), esc_html__('Add automatic styles for your sidebar. This is a basic styling over the button shape and design using the most popular configurations. You can still overwrite this with the advanced position settings.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));		
	}
	
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-8');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-8', esc_html__('Customize Top & Left/Right Position of Sidebar', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-8');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-8', 'sidebar_fixedleft', esc_html__('Customize left/right position of sidebar', 'essb'), esc_html__('Use this field to change initial position of sidebar. You can use numeric value for example 10.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-8', 'sidebar_fixedtop', esc_html__('Customize the position of sidebar on screen', 'essb'), esc_html__('The custom top position of the sidebar can be set in pixels (example: 100px from top) or in percents (10% from top). Setting a custom position will overwrite the location option you choose above.', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-8');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-8');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-8', esc_html__('Sidebar Appear/Disappear on Scroll and Close Sidebar Button', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-8');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-8', 'sidebar_leftright_percent', esc_html__('Display after amount of content is viewed', 'essb'), esc_html__('If you wish to make sidebar appear after percent of content is viewed enter value here (leave blank to appear immediately after load).', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-8', 'sidebar_appear_unit', esc_html__('Measuring unit of appear value', 'essb'), esc_html__('Default appearance value is % but you can change it here to px', 'essb'), array('' => '%', 'px' => 'px'));
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-8', 'sidebar_leftright_percent_hide', esc_html__('Hide after percent of content is viewed', 'essb'), esc_html__('If you wish to make sidebar disappear after percent of content is viewed enter value here (leave blank to make it always be visible).', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-8', 'sidebar_leftright_close', esc_html__('Add close sidebar button', 'essb'), esc_html__('Activate that option to add a close sidebar button.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-8', 'sidebar_content_hide', esc_html__('Hide when static content buttons are visible', 'essb'), esc_html__('Enable the option to make your sidebar disappear from the screen when static content buttons are visible (above content or below content sharing). The option won\'t detect interactive content displays.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-8');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-8');

	essb_prepare_location_advanced_customization($where_to_display, 'positions|display-8', 'sidebar');
}

if (!essb_options_bool_value('deactivate_method_topbar')) {
	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-9', esc_html__('Top Bar Appearance & Position', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-9');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-9', 'topbar_top', esc_html__('Top position for non logged in users', 'essb'), esc_html__('The bar has a default initial position but if your site has a fixed top element you can use this field to change the initial top position and avoid bar appear before/above that fixed element', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-9', 'topbar_top_loggedin', esc_html__('Top position for logged in users', 'essb'), esc_html__('If you display WordPress admin bar for logged in users you can correct float from top position for logged in users to avoid bar to be rendered below WordPress admin bar.', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-9', 'topbar_top_onscroll', esc_html__('Appear after percent of content is viewed', 'essb'), esc_html__('If you wish top bar to appear when user starts scrolling fill here percent of conent after is viewed it will be visible.', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-9', 'topbar_hide', esc_html__('Hide after percent of content is viewed', 'essb'), esc_html__('Provide value in percent if you wish to hide float bar - for example 80 will make bar to disappear when 80% of page content is viewed from user.', 'essb'), '', 'input60', 'fa-sort-numeric-asc', 'right');
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-9');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-9');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-9', esc_html__('Background Color', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-9');
	ESSBOptionsStructureHelper::field_color_panel($where_to_display, 'positions|display-9', 'topbar_bg', esc_html__('Choose background color', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-9', 'topbar_bg_opacity', esc_html__('Change opacity of background color', 'essb'), esc_html__('Change default opacity of background color if you wish to have a semi-transparent effect (default is 1 full color). You can enter value between 0 and 1 (example: 0.7)', 'essb'), '', 'input60');
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-9');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-9');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-9', esc_html__('Width, Height & Button Placement', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-9');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-9', 'topbar_height', esc_html__('Height of top bar content area', 'essb'), esc_html__('Provide custom height of content area. Provide number value in pixels without the px (example 40). Leave blank for default height.', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-9', 'topbar_maxwidth', esc_html__('Max width of content area', 'essb'), esc_html__('Provide custom max width of content area. Provide number value in pixels without the px (example 960). Leave blank for full width.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
	$listOfOptions = array("" => "Left", "center" => "Center", "right" => "Right");
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-9', 'topbar_buttons_align', esc_html__('Align buttons', 'essb'), esc_html__('Choose your button alignment', 'essb'), $listOfOptions);
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-9');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-9');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-9', esc_html__('INCLUDE CUSTOM CONTENT', 'essb'), esc_html__('Inside bar you can add custom content along with your share buttons.'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'topbar_contentarea', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-9');
	$listOfOptions = array("left" => "Left", "right" => "Right");
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-9', 'topbar_contentarea_width', esc_html__('Custom content area % width', 'essb'), esc_html__('Provide custom width of content area (default value if nothing is filled is 30 which means 30%). Fill number value without % mark - example 40.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-9', 'topbar_contentarea_pos', esc_html__('Custom content area position', 'essb'), esc_html__('Choose your content area alignment', 'essb'), $listOfOptions);
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-9');
	ESSBOptionsStructureHelper::field_wpeditor($where_to_display, 'positions|display-9', 'topbar_usercontent', esc_html__('Custom content', 'essb'), '', 'htmlmixed');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-9');
	essb_prepare_location_advanced_customization($where_to_display, 'positions|display-9', 'topbar');
}

if (!essb_options_bool_value('deactivate_method_bottombar')) {

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-10', esc_html__('Bottom Bar Appear/Disappear on Scroll', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-10');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-10', 'bottombar_top_onscroll', esc_html__('Appear after percent of content is viewed', 'essb'), esc_html__('If you wish bottom bar to appear when user starts scrolling fill here percent of conent after is viewed it will be visible.', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-10', 'bottombar_hide', esc_html__('Hide after percent of content is viewed', 'essb'), esc_html__('Provide value in percent if you wish to hide float bar - for example 80 will make bar to disappear when 80% of page content is viewed from user.', 'essb'), '', 'input60', 'fa-sort-numeric-asc', 'right');
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-10');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-10');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-10', esc_html__('Background Color of Bar', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-10');
	ESSBOptionsStructureHelper::field_color_panel($where_to_display, 'positions|display-10', 'bottombar_bg', esc_html__('Choose background color', 'essb'), esc_html__('Overwrite default bar background color #ffffff (white)', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-10', 'bottombar_bg_opacity', esc_html__('Change opacity of background color', 'essb'), esc_html__('Change default opacity of background color if you wish to have a semi-transparent effect (default is 1 full color). You can enter value between 0 and 1 (example: 0.7)', 'essb'), '', 'input60');
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-10');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-10');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-10', esc_html__('Width, Height & Buttons Placement', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-10');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-10', 'bottombar_height', esc_html__('Height of top bar content area', 'essb'), esc_html__('Provide custom height of content area. Provide number value in pixels without the px (example 40). Leave blank for default value.', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-10', 'bottombar_maxwidth', esc_html__('Max width of content area', 'essb'), esc_html__('Provide custom max width of content area. Provide number value in pixels without the px (example 960). Leave blank for full width.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
	$listOfOptions = array("" => "Left", "center" => "Center", "right" => "Right");
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-10', 'bottombar_buttons_align', esc_html__('Align buttons', 'essb'), esc_html__('Choose your content area alignment', 'essb'), $listOfOptions);
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-10');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-10');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-10', esc_html__('BOTTOM BAR CUSTOM CONTENT SETTINGS', 'essb'), esc_html__('Include custom content into bottom bar along with your share buttons'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'bottombar_contentarea', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-10');
	$listOfOptions = array("left" => "Left", "right" => "Right");
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-10', 'bottombar_contentarea_width', esc_html__('Custom content area % width', 'essb'), esc_html__('Provide custom width of content area (default value if nothing is filled is 30 which means 30%). Fill number value without % mark - example 40.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-10', 'bottombar_contentarea_pos', esc_html__('Custom content area position', 'essb'), esc_html__('Choose your button alignment', 'essb'), $listOfOptions);
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-10');
	ESSBOptionsStructureHelper::field_wpeditor($where_to_display, 'positions|display-10', 'bottombar_usercontent', esc_html__('Custom content', 'essb'), '', 'htmlmixed');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-10');

	essb_prepare_location_advanced_customization($where_to_display, 'positions|display-10', 'bottombar');
}

if (!essb_options_bool_value('deactivate_method_popup')) {
	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-11', esc_html__('Custom Pop Up Content Settings', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_textbox_stretched($where_to_display, 'positions|display-11', 'popup_window_title', esc_html__('Pop up window title', 'essb'), esc_html__('Set your custom pop up window title.', 'essb'));
	ESSBOptionsStructureHelper::field_editor($where_to_display, 'positions|display-11', 'popup_user_message', esc_html__('Pop up window message', 'essb'), esc_html__('Set your custom message that will appear above buttons', 'essb'), "htmlmixed");
	ESSBOptionsStructureHelper::field_textbox($where_to_display, 'positions|display-11', 'popup_user_width', esc_html__('Pop up window width', 'essb'), esc_html__('Set your custom window width (default is 800 or window width - 60). Value if provided should be numeric without px symbols.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-11');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-11', esc_html__('Display On The Following Events', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-11');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-11', 'popup_window_popafter', esc_html__('Display pop up window after (sec)', 'essb'), esc_html__('If you wish pop up window to appear after amount of seconds you can provide theme here. Leave blank for immediate pop up after page load.', 'essb'), '', 'input60', 'fa-clock-o', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-11', 'popup_user_percent', esc_html__('Display pop up window after percent of content is viewed', 'essb'), esc_html__('Set amount of page content after which the pop up will appear.', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-11', 'popup_display_end', esc_html__('Display pop up at the end of content', 'essb'), esc_html__('Automatically display pop up when the content end is reached', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-11', 'popup_display_exit', esc_html__('Display pop up on exit intent', 'essb'), esc_html__('Automatically display pop up when exit intent is detected', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-11', 'popup_display_comment', esc_html__('Display pop up on user comment', 'essb'), esc_html__('Automatically display pop up when user leave a comment.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-11', 'popup_display_purchase', esc_html__('Display pop up after WooCommerce purchase', 'essb'), esc_html__('Display on Thank You page of WooCommerce after purchase', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-11', 'popup_user_manual_show', esc_html__('Manual window display mode', 'essb'), esc_html__('Activating manual display mode will allow you to show window when you decide with calling following javascript function essb_popup_show();', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-11', 'popup_avoid_logged_users', esc_html__('Do not show pop up for logged in users', 'essb'), esc_html__('Activate this option to avoid display of pop up when user is logged in into site.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-11');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-11');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-11', esc_html__('Automatic close & do not show again', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-11');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-11', 'popup_window_close_after', esc_html__('Automatically close pop up after (sec)', 'essb'), esc_html__('You can provide seconds and after they expire window will close automatically. User can close this window manually by pressing close button.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-11', 'popup_user_autoclose', esc_html__('Close up message customize', 'essb'), esc_html__('Set custom text announcement for closing the pop up. After your text there will be timer counting the seconds leaving.', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-11', 'popup_user_notshow_onclose', esc_html__('After user close window do not show it again on this page/post for him', 'essb'), esc_html__('Activating this option will set cookie that will not show again pop up message for next 7 days for user on this post/page', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-11', 'popup_user_notshow_onclose_all', esc_html__('After user close window do not show it again on all page/post for him', 'essb'), esc_html__('Activating this option will set cookie that will not show again pop up message for next 7 days for user on all posts/pages', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-11');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-11');

	essb_prepare_location_advanced_customization($where_to_display, 'positions|display-11', 'popup');
}

if (!essb_options_bool_value('deactivate_method_flyin')) {

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-12', esc_html__('Fly in Custom content and Position', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_textbox_stretched($where_to_display, 'positions|display-12', 'flyin_window_title', esc_html__('Fly in window title', 'essb'), esc_html__('Set your custom fly in window title.', 'essb'));
	ESSBOptionsStructureHelper::field_editor($where_to_display, 'positions|display-12', 'flyin_user_message', esc_html__('Fly in window message', 'essb'), esc_html__('Set your custom message that will appear above buttons', 'essb'), "htmlmixed");
	ESSBOptionsStructureHelper::field_textbox($where_to_display, 'positions|display-12', 'flyin_user_width', esc_html__('Fly in window width', 'essb'), esc_html__('Set your custom window width (default is 400 or window width - 60). If value is provided should be numeric without px symbols.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
	$listOfOptions = array("right" => "Right", "left" => "Left");
	ESSBOptionsStructureHelper::field_select($where_to_display, 'positions|display-12', 'flyin_position', esc_html__('Choose fly in display position', 'essb'), '', $listOfOptions);
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'positions|display-12', 'flyin_noshare', esc_html__('Do not show share buttons in fly in', 'essb'), esc_html__('Activating this you will get a fly in display without share buttons in it - only the custom content you have set.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-12');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-12', esc_html__('Display on the following events', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-12');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-12', 'flyin_window_popafter', esc_html__('Display fly in window after (sec)', 'essb'), esc_html__('If you wish fly in window to appear after amount of seconds you can provide them here. Leave blank for immediate pop up after page load.', 'essb'), '', 'input60', 'fa-clock-o', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-12', 'flyin_user_percent', esc_html__('Display fly in window after percent of content is viewed', 'essb'), esc_html__('Set amount of page content after which the pop up will appear.', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-12', 'flyin_display_end', esc_html__('Display fly in at the end of content', 'essb'), esc_html__('Automatically display fly in when the content end is reached.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-12', 'flyin_display_comment', esc_html__('Display fly in on user comment', 'essb'), esc_html__('Automatically display fly in when user leaves a comment.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-12', 'flyin_user_manual_show', esc_html__('Manual fly in display mode', 'essb'), esc_html__('Activating manual display mode will allow you to show window when you decide with calling following javascript function essb_flyin_show(); or essb_flyin_manual_show();', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-12');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-12');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-12', esc_html__('Automatic Close & do not show again', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-12');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-12', 'flyin_window_close_after', esc_html__('Automatically close fly in after (sec)', 'essb'), esc_html__('You can provide seconds and after they expire window will close automatically. User can close this window manually by pressing close button.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-12', 'flyin_user_autoclose', esc_html__('Close up message customize', 'essb'), esc_html__('Set custom text announcement for closing the fly in. After your text there will be timer counting the seconds leaving.', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-12', 'flyin_user_notshow_onclose', esc_html__('After user closes window do not show it again on this page/post for him', 'essb'), esc_html__('Activating this option will set cookie that will not show again pop up message for next 7 days for user on this post/page', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-12', 'flyin_user_notshow_onclose_all', esc_html__('After user close window do not show it again on all page/post for him', 'essb'), esc_html__('Activating this option will set cookie that will not show again pop up message for next 7 days for user on all posts/pages', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-12');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-12');

	essb_prepare_location_advanced_customization($where_to_display, 'positions|display-12', 'flyin');
}

if (!essb_option_bool_value('deactivate_method_image')) {
	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-13', esc_html__('On media buttons appearance', 'essb'), esc_html__('Choose where you wish buttons to appear', 'essb'), 'fa21 ti-layout-grid2-alt', array("mode" => "toggle"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-13');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-13', 'sis_selector', esc_html__('Default image share selector', 'essb'), esc_html__('Provide your own custom image selector that will allow to pickup share images. Leave blank for use the default or use <b>.essbis_site img</b> to allow share of any image on site.', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-13', 'sis_dontshow', esc_html__('Do not show on', 'essb'), esc_html__('Set image classes and IDs for which on media display buttons won\'t show. Separate several selectors with commas. (example: .notshow or .notshow,#notshow', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-13');

	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-13');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-13', 'sis_minWidth', esc_html__('Minimal width', 'essb'), esc_html__('Minimum width of image for sharing. Use value without px.', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-13', 'sis_minHeight', esc_html__('Minimal height', 'essb'), esc_html__('Minimum height of image for sharing. Use value without px.', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-13', 'sis_minWidthMobile', esc_html__('Minimal width on Mobile', 'essb'), esc_html__('Minimum width of image for sharing. Use value without px.', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-13', 'sis_minHeightMobile', esc_html__('Minimal height on Mobile', 'essb'), esc_html__('Minimum height of image for sharing. Use value without px.', 'essb'));

	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-13', 'sis_always_visible', esc_html__('Always visible share icons', 'essb'), esc_html__('Use this option to make on media share buttons always visible on desktop. The function may not work if you have lazy loading images.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-13', 'sis_facebookapp', esc_html__('Facebook Application ID', 'essb'), esc_html__('If you wish to make plugin selected image on Facebook you need to create application and make it public to use advanced sharing. Advanced sharing will allow to post any image on Facebook but it will allow share on personal timeline only.', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-13');

	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-13');
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-13', 'sis_on_mobile', esc_html__('Enable on mobile', 'essb'), esc_html__('Enable image sharing on mobile devices', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-13', 'sis_on_mobile_click', esc_html__('Appear on mobile with click', 'essb'), esc_html__('The on media buttons are always visible when you open site with mobile device. Use this option if you wish to make the appear with click over image.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-13');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-13');


	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-13', esc_html__('Use the following social buttons', 'essb'), esc_html__('Choose social buttons that you will use on media images', 'essb'), 'fa21 ti-layout-grid2-alt', array("mode" => "toggle"));
	$listOfNetworks = array( "facebook", "twitter", "google", "linkedin", "pinterest", "tumblr", "reddit", "digg", "delicious", "vkontakte", "odnoklassniki");
	$listOfNetworksAdvanced = array( "facebook" => "Facebook", "twitter" => "Twitter", "google" => "Google", "linkedin" => "LinkedIn", "pinterest" => "Pinterest", "tumblr" => "Tumblr", "reddit" => "Reddit", "digg" => "Digg", "delicious" => "Delicious", "vkontakte" => "VKontakte", "odnoklassniki" => "Odnoklassniki");
	ESSBOptionsStructureHelper::field_checkbox_list($where_to_display, 'positions|display-13', 'sis_networks', esc_html__('Activate networks', 'essb'), esc_html__('Choose active social networks', 'essb'), $listOfNetworksAdvanced);
	ESSBOptionsStructureHelper::field_simplesort($where_to_display, 'positions|display-13', 'sis_network_order', esc_html__('Display order', 'essb'), esc_html__('Arrange network appearance using drag and drop', 'essb'), $listOfNetworks);
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-13');
	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-13', esc_html__('Visual display setup', 'essb'), esc_html__('Customize look and feel of your social share buttons that appear on images', 'essb'), 'fa21 ti-layout-grid2-alt', array("mode" => "toggle"));

	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-13');

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

	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-13', 'sis_position', esc_html__('Choose position of buttons on image', 'essb'), esc_html__('Select default position of buttons over image. Depends on active buttons and template select the best to fit them into images', 'essb'), $list_of_positions);

	$listOfTemplates = array("tiny" => "Tiny", "flat-small" => "Small", "flat" => "Regular", "round" => "Round");
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-13', 'sis_style', esc_html__('Template', 'essb'), esc_html__('Choose buttons template. You can use only build into module templates to avoid misconfiguration', 'essb'), $listOfTemplates);
	$listOfTemplates = array("" => "Default", "tiny" => "Tiny", "flat-small" => "Small", "flat" => "Regular", "round" => "Round");
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-13', 'sis_mobile_style', esc_html__('Template on Mobile', 'essb'), esc_html__('Choose buttons template that will be used on a mobile device. You can use only build into module templates to avoid misconfiguration', 'essb'), $listOfTemplates);
	$listOfOptions = array("horizontal" => esc_html__("Horizontal", 'essb'), "vertical" => esc_html__("Vertical", 'essb'));
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-13', 'sis_orientation', esc_html__('Orientation', 'essb'), esc_html__('Display buttons aligned horizontal or vertical', 'essb'), $listOfOptions);
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-13');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-13');
}
if (!essb_options_bool_value('deactivate_method_heroshare')) {
	ESSBOptionsStructureHelper::field_textbox($where_to_display, 'positions|display-14', 'heroshare_user_width', esc_html__('Custom window width', 'essb'), esc_html__('Set your custom window width (default is 960 or window width - 60). Value if provided should be numeric without px symbols.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
	ESSBOptionsStructureHelper::field_section_start($where_to_display, 'positions|display-14', esc_html__('Primary content area', 'essb'), esc_html__('Primary content area is located above post information and share details. You can use it to add custom title or message that will appear on top. Leave it blank if you do not wish to have such', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_stretched($where_to_display, 'positions|display-14', 'heroshare_window_title', esc_html__('Window title', 'essb'), esc_html__('Set your custom pop up window title.', 'essb'));
	ESSBOptionsStructureHelper::field_editor($where_to_display, 'positions|display-14', 'heroshare_user_message', esc_html__('Window message', 'essb'), esc_html__('Set your custom message that will appear above buttons', 'essb'), "htmlmixed");
	ESSBOptionsStructureHelper::field_section_end($where_to_display, 'positions|display-14');

	ESSBOptionsStructureHelper::field_section_start($where_to_display, 'positions|display-14', esc_html__('Additional content area', 'essb'), esc_html__('Additional content area is located below share buttons and provide various message types. If you do not wish to display it choose data type to html message and leave field for message blank', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_stretched($where_to_display, 'positions|display-14', 'heroshare_second_title', esc_html__('Title', 'essb'), esc_html__('Set your custom pop up window title for additional content area.', 'essb'));
	$listOfOptions = array("top" => "Top social posts (require build in analytics to be active)", "fans" => "Followers counter (require followers counter to be activated)", "html" => "Custom HTML message");
	ESSBOptionsStructureHelper::field_select($where_to_display, 'positions|display-14', 'heroshare_second_type', esc_html__('Type of displayed data', 'essb'), esc_html__('Choose what you wish to be displayed into second widget area below share buttons. If you wish to leave it blank choose Custom HTML message and do not fill anything inside field for custom message.', 'essb'), $listOfOptions);
	ESSBOptionsStructureHelper::field_textbox_stretched($where_to_display, 'positions|display-14', 'heroshare_second_fans', esc_html__('Followers counter shortcode', 'essb'), esc_html__('Fill in this field you followers counter shortcode that will be used if you select in second widget area to have followers counter. Shortcode can be generated using shortcode generator.', 'essb'));
	ESSBOptionsStructureHelper::field_editor($where_to_display, 'positions|display-14', 'heroshare_second_message', esc_html__('Custom HTML message', 'essb'), esc_html__('Set your custom message (for example html code for opt-in form). This field supports shortcodes.', 'essb'), "htmlmixed");
	ESSBOptionsStructureHelper::field_section_end($where_to_display, 'positions|display-14');

	ESSBOptionsStructureHelper::field_section_start_panels($where_to_display, 'positions|display-14', esc_html__('Hero share window display', 'essb'), '');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-14', 'heroshare_window_popafter', esc_html__('Display pop up window after (sec)', 'essb'), esc_html__('If you wish pop up window to appear after amount of seconds you can provide theme here. Leave blank for immediate pop up after page load.', 'essb'), '', 'input60', 'fa-clock-o', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-14', 'heroshare_user_percent', esc_html__('Display pop up window after percent of content is viewed', 'essb'), esc_html__('Set amount of page content after which the pop up will appear.', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-14', 'heroshare_display_end', esc_html__('Display pop up at the end of content', 'essb'), esc_html__('Automatically display pop up when the content end is reached', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-14', 'heroshare_display_exit', esc_html__('Display pop up on exit intent', 'essb'), esc_html__('Automatically display pop up when exit intent is detected', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-14', 'heroshare_user_manual_show', esc_html__('Manual window display mode', 'essb'), esc_html__('Activating manual display mode will allow you to show window when you decide with calling following javascript function essb_heroshare_show();', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-14', 'heroshare_avoid_logged_users', esc_html__('Do not show pop up for logged in users', 'essb'), esc_html__('Activate this option to avoid display of pop up when user is logged in into site.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_panels($where_to_display, 'positions|display-14');

	ESSBOptionsStructureHelper::field_section_start_panels($where_to_display, 'positions|display-14', esc_html__('Window close', 'essb'), '');
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-14', 'heroshare_user_notshow_onclose', esc_html__('After user close window do not show it again on this page/post for him', 'essb'), esc_html__('Activating this option will set cookie that will not show again pop up message for next 7 days for user on this post/page', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-14', 'heroshare_user_notshow_onclose_all', esc_html__('After user close window do not show it again on all page/post for him', 'essb'), esc_html__('Activating this option will set cookie that will not show again pop up message for next 7 days for user on all posts/pages', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_panels($where_to_display, 'positions|display-14');
	essb_prepare_location_advanced_customization($where_to_display, 'positions|display-14', 'heroshare');
}

if (!essb_options_bool_value('deactivate_method_postbar')) {
	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-15', esc_html__('Deactivate Post Bar Default Components', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-15');
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-15', 'postbar_deactivate_prevnext', esc_html__('Deactivate previous/next articles', 'essb'), esc_html__('Activate this option if you wish to deactivate display of previous/next article buttons', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-15', 'postbar_deactivate_progress', esc_html__('Deactivate read progress bar', 'essb'), esc_html__('Activate this option if you wish to deactivate display of read progress', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-15', 'postbar_deactivate_title', esc_html__('Deactivate post title', 'essb'), esc_html__('Activate this option if you wish to deactivate display of post title', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-15', 'postbar_deactivate_share', esc_html__('Deactivate share buttons', 'essb'), esc_html__('Show post-bar without share buttons', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-15');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-15');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-15', esc_html__('Activate Additional Components', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-15');
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-15', 'postbar_activate_category', esc_html__('Activate display of category', 'essb'), esc_html__('Activate this option if you wish to activate display of category', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-15', 'postbar_activate_author', esc_html__('Activate display of post author', 'essb'), esc_html__('Activate this option if you wish to activate display of post author', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-15', 'postbar_activate_total', esc_html__('Activate display of total shares counter', 'essb'), esc_html__('Activate this option if you wish to activate display of total shares counter', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-15', 'postbar_activate_comments', esc_html__('Activate display of comments counter', 'essb'), esc_html__('Activate this option if you wish to activate display of comments counter', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-15', 'postbar_activate_time', esc_html__('Activate display of time to read', 'essb'), esc_html__('Activate this option if you wish to activate display of time to read', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-15', 'postbar_activate_time_words', esc_html__('Words per minuted for time to read', 'essb'), esc_html__('Customize the words per minute for time to read display', 'essb'), '', 'input60', 'fa-clock-o', 'right');
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-15');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-15');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-15', esc_html__('Personalize Colors', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-15');
	ESSBOptionsStructureHelper::field_color_panel($where_to_display, 'positions|display-15', 'postbar_bgcolor', esc_html__('Change default background color', 'essb'), esc_html__('Customize the default post bar background color (#FFFFFF)', 'essb'));
	ESSBOptionsStructureHelper::field_color_panel($where_to_display, 'positions|display-15', 'postbar_color', esc_html__('Change default text color', 'essb'), esc_html__('Customize the default post bar text color (#111111)', 'essb'));
	ESSBOptionsStructureHelper::field_color_panel($where_to_display, 'positions|display-15', 'postbar_accentcolor', esc_html__('Change default accent color', 'essb'), esc_html__('Customize the default post bar accent color (#3D8EB9)', 'essb'));
	ESSBOptionsStructureHelper::field_color_panel($where_to_display, 'positions|display-15', 'postbar_altcolor', esc_html__('Change default alt text color', 'essb'), esc_html__('Customize the default post bar alt text color (#FFFFFF) which is applied to elements with accent background color', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-15');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-15');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-15', esc_html__('Customize Buttons Style', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	$tab_id = $where_to_display;
	$menu_id = 'positions|display-15';
	$location = 'postbar';


	ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_button_style', esc_html__('Buttons Style', 'essb'), esc_html__('Select your button display style.', 'essb'), essb_avaiable_button_style_with_recommend());
	ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_template', esc_html__('Template', 'essb'), esc_html__('Select your template for that display location.', 'essb'), essb_available_tempaltes4(true));
	ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_nospace', esc_html__('Remove spacing between buttons', 'essb'), esc_html__('Activate this option to remove default space between share buttons.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_show_counter', esc_html__('Display counter of sharing', 'essb'), esc_html__('Activate display of share counters.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_counter_pos', esc_html__('Position of counters', 'essb'), esc_html__('Choose your default button counter position', 'essb'), essb_avaliable_counter_positions());

	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-15');

	essb_prepare_location_advanced_customization($where_to_display, 'positions|display-15', 'postbar');
}

if (!essb_options_bool_value('deactivate_method_point')) {

	// Point
	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-16', esc_html__('Point Appearance & Style', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-16');
	$point_positions = array("bottomright" => esc_html__('Bottom Right', 'essb'), 'bottomleft' => esc_html__('Bottom Left', 'essb'), 'topright' => esc_html__('Top Right', 'essb'), 'topleft' => esc_html__('Top Left', 'essb'));
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-16', 'point_position', esc_html__('Point will appear on', 'essb'), esc_html__('Choose where you wish sharing point to appear', 'essb'), $point_positions);
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-16', 'point_total', esc_html__('Display total counter', 'essb'), esc_html__('Activate this option if you wish to activate display of total counter on point', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	$point_open_triggers = array("no" => esc_html__("No", "essb"), "end" => esc_html__("At the end of content", "essb"), "middle" => esc_html__("After the middle of content", "essb"));
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-16', 'point_open_auto', esc_html__('Automatic share point open', 'essb'), esc_html__('Select your button display style.', 'essb'), $point_open_triggers);

	$point_display_style = array("simple" => esc_html__('Simple icons', 'essb'), 'advanced' => esc_html__('Advanced Panel', 'essb'));
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-16', 'point_style', esc_html__('Share buttons action type', 'essb'), esc_html__('Choose your share buttons action type. Simple buttons will just open share buttons when you click the point. Advanced panel allows you also to include custom texts before/after buttons into nice flyout panel', 'essb'), $point_display_style);
	$point_display_style = array("round" => esc_html__('Round', 'essb'), 'square' => esc_html__('Square', 'essb'), 'rounded' => esc_html__('Rounded edges square', 'essb'));
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-16', 'point_shape', esc_html__('Point button shape', 'essb'), esc_html__('Choose the shape of share point - default is round', 'essb'), $point_display_style);

	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-16', 'point_allowall', esc_html__('Display share point anywhere on site', 'essb'), esc_html__('Default point setup is made to appear on posts, custom post types and pages but it will not appear on lists of posts, dynamic pages activate this option.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-16', 'point_close', esc_html__('Automatic close after seconds', 'essb'), esc_html__('Enter a value if you wish to setup automated close of the point display method once it is is opened on screen (numeric value, example: 5 (5 seconds))', 'essb'), '', 'input60', 'fa-arrows-v', 'right');

	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-16');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-16');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-16', esc_html__('Personalize Colors', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-16');
	ESSBOptionsStructureHelper::field_color_panel($where_to_display, 'positions|display-16', 'point_bgcolor', esc_html__('Change default background color', 'essb'), esc_html__('Customize the default point background color', 'essb'));
	ESSBOptionsStructureHelper::field_color_panel($where_to_display, 'positions|display-16', 'point_color', esc_html__('Change default text color', 'essb'), esc_html__('Customize the default point text color', 'essb'));
	ESSBOptionsStructureHelper::field_color_panel($where_to_display, 'positions|display-16', 'point_accentcolor', esc_html__('Change default total background color', 'essb'), esc_html__('Customize the default total background color', 'essb'));
	ESSBOptionsStructureHelper::field_color_panel($where_to_display, 'positions|display-16', 'point_altcolor', esc_html__('Change default total text color', 'essb'), esc_html__('Customize the default total text color', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'positions|display-16');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-16');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-16', esc_html__('Customize Buttons Style', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	$tab_id = $where_to_display;
	$menu_id = 'positions|display-16';
	$location = 'point';
	
	/**
	 * Update to the new components
	 * @since 7.3.1
	 */
	ESSBOptionsStructureHelper::title($tab_id, $menu_id, esc_html__('Template', 'essb'), '', 'inner-row');
	ESSBOptionsStructureHelper::field_template_select($tab_id, $menu_id, $location.'_template', $location);	
	
	ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_button_style', esc_html__('Buttons Style', 'essb'), esc_html__('Select your button display style.', 'essb'), essb_avaiable_button_style_with_recommend());
	ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_nospace', esc_html__('Remove spacing between buttons', 'essb'), esc_html__('Activate this option to remove default space between share buttons.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_show_counter', esc_html__('Display counter of sharing', 'essb'), esc_html__('Activate display of share counters.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_counter_pos', esc_html__('Position of counters', 'essb'), esc_html__('Choose your default button counter position. Please note that if you use Simple icons mode all Inside positions will act like Inside - network names will not appear because of visual limitations', 'essb'), essb_avaliable_counter_positions_point());
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-16');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-16', esc_html__('Custom Content for Advanced Point Display', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_wpeditor($where_to_display, 'positions|display-16', 'point_top_content', esc_html__('Custom content above share buttons', 'essb'), esc_html__('Optional: Provide custom content that will appear above share buttons. You can use the variables to display post related content: %%title%%, %%url%%, %%image%%, %%permalink%%', 'essb'), 'htmlmixed');
	ESSBOptionsStructureHelper::field_wpeditor($where_to_display, 'positions|display-16', 'point_bottom_content', esc_html__('Custom content below share buttons', 'essb'), esc_html__('Optional: Provide custom content that will appear below share buttons. You can use the variables to display post related content: %%title%%, %%url%%, %%image%%, %%permalink%%', 'essb'), 'htmlmixed');
	ESSBOptionsStructureHelper::field_switch($where_to_display, 'positions|display-16', 'point_articles', esc_html__('Display prev/next article', 'essb'), esc_html__('Activate this option to display prev/next article from same category', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));

	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-16');

	essb_prepare_location_advanced_customization($where_to_display, 'positions|display-16', 'point');
}

essb_prepare_location_advanced_customization($where_to_display, 'positions|display-17', 'excerpt');

if (!essb_options_bool_value('deactivate_method_followme')) {
	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-18', esc_html__('Follow me bar appearance', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-18');
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-18', 'followme_pos', esc_html__('Follow me bar appearance', 'essb'), esc_html__('Set where the bar will appear when user scroll down on content. Default appearance is bottom but you can change to top too.', 'essb'), array('' => 'Bottom', 'top' => 'Top', 'left' => 'Left'));
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-18', 'followme_content', esc_html__('Static content share buttons position', 'essb'), esc_html__('Choose where you wish to display the static content share buttons in content. The default is above and below content but you can set above or bottom only too.', 'essb'), array('' => 'Above & Below Content', 'above' => 'Above Content Only', 'below' => 'Below Content Only'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-18', 'followme_full', esc_html__('Display buttons on full screen width', 'essb'), esc_html__('The default width of buttons area inside follow me bar will be the width of buttons inside content. Set this option if you wish to make it take the full width of screen.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-18', 'followme_top', esc_html__('Custom top position', 'essb'), esc_html__('Only when top appearance is selected. Use this field if you have a fixed top element and you wish to avoid appearance of bar over it. (numeric value, example: 50)', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-18', 'followme_top_offset', esc_html__('Offset from the top on user scroll when the bar will appear', 'essb'), esc_html__('Optionally set a numeric value (in pixels). When this value is provided the bar will appear not only when the content buttons are out the visible area, but the user should also pass that depth of scroll in pixels.', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-18', 'followme_nomargin', esc_html__('Remove top/bottom space', 'essb'), esc_html__('The bar has little top and bottom space added to get a better look. On some designs you may wish to remove it - if so just activate the option (or you can add your own custom code).', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-18', 'followme_noleftmargin', esc_html__('My bar does not appear centered on screen', 'essb'), esc_html__('If you experience that type of problem just activate this option to fix it.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-18', 'followme_hide', esc_html__('Hide before end of page', 'essb'), esc_html__('Set this option to yes if you wish the bar to disappear before end of page.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));

	ESSBOptionsStructureHelper::field_section_end_panels($where_to_display, 'positions|display-18');

	ESSBOptionsStructureHelper::field_color_panel($where_to_display, 'positions|display-18', 'followme_bg', esc_html__('Customize bar background', 'essb'), esc_html__('The follow me bar has solid white color as background. If you wish to change that color use this field and set your own (alpha colors supported)', 'essb'), '', 'true');

	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-18');

	essb_prepare_location_advanced_customization($where_to_display, 'positions|display-18', 'followme');
}

if (!essb_options_bool_value('deactivate_method_corner')) {
	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-19', esc_html__('Corner Bar Apperance', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-19');
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-19', 'cornerbar_pos', esc_html__('Position on screen', 'essb'), esc_html__('Choose the edge of screen where buttons will appear. Default is bottom right corner', 'essb'), array('' => 'Bottom Right', 'bottom-left' => 'Bottom Left', 'top-right' => 'Top Right', 'top-left' => 'Top Left'));
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-19', 'cornerbar_style', esc_html__('Bar style', 'essb'), esc_html__('Choose a style of bar that contains share buttons', 'essb'), array('' => 'Transparent', 'light' => 'Light', 'dark' => 'Dark', 'glow' => 'Glow'));
	ESSBOptionsStructureHelper::field_color_panel($where_to_display, 'positions|display-19', 'cornerbar_bg', esc_html__('Customize bar background', 'essb'), esc_html__('Set custom background color when you are using different style than transparent', 'essb'), '', 'true');

	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-19', 'cornerbar_width', esc_html__('Set custom max-width of bar', 'essb'), esc_html__('Bar has default max-width that can be use of 640px. Use this field to change that by entering your custom value: example: 400px, 50%', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
	ESSBOptionsStructureHelper::field_section_end_panels($where_to_display, 'positions|display-19');

	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-19');
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-19', 'cornerbar_hide', esc_html__('Hide before end of page', 'essb'), esc_html__('Set this option to yes if you wish the bar to disappear before end of page.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-19', 'cornerbar_show', esc_html__('Visibility on screen', 'essb'), esc_html__('Choose when bar will be visible based on various conditions', 'essb'), array('' => 'Immediately after page load', 'onscroll' => 'Appear on scroll', 'onscroll50' => 'Appear on scroll when at least 50% of content is viewed', 'content' => 'Appear when content buttons are not visible (static positions for above/below content)'));
	ESSBOptionsStructureHelper::field_section_end_panels($where_to_display, 'positions|display-19');

	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-19');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-19', 'cornerbar_text', esc_html__('Custom text before share buttons', 'essb'), esc_html__('Enter custom text to engage shares. Example: Share', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-19', 'cornerbar_arrow', esc_html__('Add arrow after text', 'essb'), esc_html__('Include nice styled arrow pointing share buttons.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-19', 'cornerbar_small', esc_html__('Small size text', 'essb'), esc_html__('Text will appear by default using the theme font size. Set this option to make it looks time (12px).', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_panels($where_to_display, 'positions|display-19');

	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-19');

	essb_prepare_location_advanced_customization($where_to_display, 'positions|display-19', 'cornerbar');
}

if (!essb_options_bool_value('deactivate_method_booster')) {
	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-20', esc_html__('Booster Appearance', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-20');
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-20', 'booster_trigger', esc_html__('Appear', 'essb'), esc_html__('Choose when share booster will overtake window', 'essb'), array('' => 'Immediately', 'time' => 'Time Delayed', 'scroll' => 'On Scroll'));
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-20', 'booster_time', esc_html__('Delay in seconds', 'essb'), esc_html__('Set amount of seconds to wait before booster appear', 'essb'), '', 'input60', 'fa-clock-o', 'right');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-20', 'booster_scroll', esc_html__('Percent of content read', 'essb'), esc_html__('Set percent of content to be read before booster appear', 'essb'), '', 'input60', 'fa-arrows-v', 'right');
	ESSBOptionsStructureHelper::field_color_panel($where_to_display, 'positions|display-20', 'booster_bg', esc_html__('Overlay background color', 'essb'), esc_html__('Setup custom overlay color that will appear over content.', 'essb'), '', 'true');
	ESSBOptionsStructureHelper::field_section_end_panels($where_to_display, 'positions|display-20');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-20');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-20', esc_html__('Booster Disappear', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-20');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-20', 'booster_donotshow', esc_html__('Do not show again for', 'essb'), esc_html__('Set custom number of days when booster will remain inactive once appear for user', 'essb'), '', 'input60', 'fa-clock-o', 'right');
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-20', 'booster_donotshow_on', esc_html__('Do not show on', 'essb'), esc_html__('Customize hide of booster for current post/page or all posts/pages', 'essb'), array('' => 'Current post/page only', 'all' => 'All posts/pages on site'));
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-20', 'booster_autoclose', esc_html__('Automatically close if no action', 'essb'), esc_html__('Make booster overlay close automatically after amount of seconds. Leave blank to remain constantly on screen waiting for action.', 'essb'), '', 'input60', 'fa-clock-o', 'right');
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-20', 'booster_manualclose', esc_html__('Allow user manually close the screen', 'essb'), esc_html__('Set this option to Yes if you wish to allow user manually close the screen. This will not raise do not show action and this visitor will see again the booster screen when post is loaded till an action of sharing is made.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-20', 'booster_manualclose_text', esc_html__('Close link text', 'essb'), esc_html__('Set a custom close text that will change the default (only if the manual close link is active).', 'essb'));
	ESSBOptionsStructureHelper::field_section_end_panels($where_to_display, 'positions|display-20');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-20');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-20', esc_html__('Booster Window Content', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-20');
	ESSBOptionsStructureHelper::field_color_panel($where_to_display, 'positions|display-20', 'booster_window_bg', esc_html__('Content background', 'essb'), esc_html__('Custom content background color.', 'essb'), '', 'true');
	ESSBOptionsStructureHelper::field_color_panel($where_to_display, 'positions|display-20', 'booster_window_color', esc_html__('Content text color', 'essb'), esc_html__('Custom content text color.', 'essb'), '', 'true');
	ESSBOptionsStructureHelper::field_section_end_panels($where_to_display, 'positions|display-20');
	ESSBOptionsStructureHelper::field_textbox_stretched($where_to_display, 'positions|display-20', 'booster_title', esc_html__('Title', 'essb'), esc_html__('Set own personalized title.', 'essb'));
	ESSBOptionsStructureHelper::field_editor($where_to_display, 'positions|display-20', 'booster_message', esc_html__('Custom message', 'essb'), esc_html__('Set your custom message that will appear above buttons (shortcodes supported)', 'essb'), "htmlmixed");
	ESSBOptionsStructureHelper::field_image($where_to_display, 'positions|display-20', 'booster_bg_image', esc_html__('Background image', 'essb'), esc_html__('Set custom background image that will appear on the content screen', 'essb'), '', 'vertical1');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-20');

	essb_prepare_location_advanced_customization($where_to_display, 'positions|display-20', 'booster');
}

if (!essb_options_bool_value('deactivate_method_sharebutton')) {
	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-21', esc_html__('Button Appearance', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-21');
	ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'positions|display-21', 'sharebutton_text', esc_html__('Share Button Text', 'essb'), esc_html__('Set custom text inside button (example: Share with friends)', 'essb'), '', '');
	ESSBOptionsStructureHelper::field_color_panel($where_to_display, 'positions|display-21', 'sharebutton_bg', esc_html__('Button Background Color', 'essb'), esc_html__('Set custom background of the default share button.', 'essb'), '', 'true');
	ESSBOptionsStructureHelper::field_color_panel($where_to_display, 'positions|display-21', 'sharebutton_color', esc_html__('Button Text Color', 'essb'), esc_html__('Set custom button text color.', 'essb'), '', 'true');
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-21', 'sharebutton_style', esc_html__('Button Style', 'essb'), esc_html__('Control the button style', 'essb'), array('' => 'Button with background color', 'outline' => 'Outline button', 'modern' => 'Modern button'));
	$point_positions = array("" => esc_html__('Bottom Right', 'essb'), 'bottomleft' => esc_html__('Bottom Left', 'essb'), 'topright' => esc_html__('Top Right', 'essb'), 'topleft' => esc_html__('Top Left', 'essb'), 'manual' => esc_html__('Manual window open with function call', 'essb'));
	ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'positions|display-21', 'sharebutton_position', esc_html__('Button Appearance', 'essb'), esc_html__('Choose where you wish sharing button to appear', 'essb'), $point_positions);
	ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'positions|display-21', 'sharebutton_total', esc_html__('Display Total Counter', 'essb'), esc_html__('Activate this option to display total share counter.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	$select_values = array(
			'' => array('title' => '', 'content' => '<i class="essb_icon_share"></i>'),
			'share-alt-square' => array('title' => '', 'content' => '<i class="essb_icon_share-alt-square"></i>'),
			'share-alt' => array('title' => '', 'content' => '<i class="essb_icon_share-alt"></i>'),
			'share-tiny' => array('title' => '', 'content' => '<i class="essb_icon_share-tiny"></i>'),
			'share-outline' => array('title' => '', 'content' => '<i class="essb_icon_share-outline"></i>')
	);
	ESSBOptionsStructureHelper::field_toggle($where_to_display, 'positions|display-21', 'sharebutton_icon', esc_html__('Share button icon', 'essb'), esc_html__('Choose the share button icon you will use (default is share if nothing is selected)', 'essb'), $select_values);

	ESSBOptionsStructureHelper::field_section_end_panels($where_to_display, 'positions|display-21');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-21');

	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|display-21', esc_html__('Personalize Pop-up Share Window Content', 'essb'), '', '', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'positions|display-21');
	ESSBOptionsStructureHelper::field_textbox_stretched($where_to_display, 'positions|display-21', 'sharebutton_window_title', esc_html__('Pop up window title', 'essb'), esc_html__('Set your custom pop up window title.', 'essb'));
	ESSBOptionsStructureHelper::field_editor($where_to_display, 'positions|display-21', 'sharebutton_user_message', esc_html__('Pop up window message', 'essb'), esc_html__('Set your custom message that will appear above buttons', 'essb'), "htmlmixed");
	ESSBOptionsStructureHelper::field_section_end_panels($where_to_display, 'positions|display-21');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|display-21');

	essb_prepare_location_advanced_customization($where_to_display, 'positions|display-21', 'sharebutton');
}

if (!essb_option_bool_value('activate_automatic_mobile')) {
	if (essb_option_bool_value('mobile_positions') && essb_option_value('functions_mode_mobile') != 'auto' && essb_option_value('functions_mode_mobile') != 'deactivate') {
		ESSBOptionsStructureHelper::help($where_to_display, 'mobile|setup', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/configure-share-buttons-for-mobile/', 'Step By Step Guide For Setting Up Responsive Buttons Without Dealing With Mobile Options' => 'https://docs.socialsharingplugin.com/knowledgebase/step-by-step-guide-for-setting-up-responsive-buttons-without-dealing-with-mobile-options/'));
		ESSBOptionsStructureHelper::panel_start($where_to_display, 'mobile|setup', esc_html__('Custom Mobile Display & Positions', 'essb'), esc_html__('The custom mobile-optimized displays and styles can help you drive the most from your mobile share buttons. In case you wish to rely on automatic mobile setup or in case you do not need such deep mobile setup, revert back to No and save settings.', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'mobile_positions', 'switch_submit' => 'true', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
	
		ESSBOptionsStructureHelper::title($where_to_display, 'mobile|setup',  esc_html__('Inline Content Share Buttons', 'essb'), '');
		ESSBOptionsStructureHelper::field_single_position_select($where_to_display, 'mobile|setup', 'content_position_mobile', essb5_available_content_positions_mobile());
	
		ESSBOptionsStructureHelper::title($where_to_display, 'mobile|setup',  esc_html__('Additional button display positions', 'essb'), esc_html__('Choose additional display methods that can be used to display buttons.', 'essb'));
		ESSBOptionsStructureHelper::field_multi_position_select($where_to_display, 'mobile|setup', 'button_position_mobile', essb5_available_button_positions_mobile());
	
		ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'mobile|setup');
		ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'mobile|setup', 'mobile_exclude_tablet', esc_html__('Do not apply mobile settings for tablets', 'essb'), esc_html__('You can avoid mobile rules for settings for tablet devices.', 'essb'), 'recommeded', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'mobile|setup', 'mobile_avoid_newwindow', esc_html__('Open sharing window in same tab', 'essb'), esc_html__('Activate this option if you wish to make sharing on mobile open in same tab. Warning! Option may lead to loose visitor as once share dialog is opened with this option user will leave your site. Use with caution..', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'mobile|setup');
	
		ESSBOptionsStructureHelper::field_heading($where_to_display, 'mobile|setup', 'heading4', esc_html__('Customize active social networks', 'essb'));
		ESSBOptionsStructureHelper::title($where_to_display, 'mobile|setup', '', esc_html__('Choose social networks you wish to see appearing when your site is opened from a mobile device. This list is optional and you can use it in case you need to set a personalized mobile network list. Otherwise leave it blank for faster update of settings.', 'essb'), 'inner-row');
	
		ESSBOptionsStructureHelper::field_network_select($where_to_display, 'mobile|setup', 'mobile_networks', 'mobile');
	
		ESSBOptionsStructureHelper::holder_start($where_to_display, 'mobile|setup', 'essb-panel-sharebar', 'essb-panel-sharebar');
		ESSBOptionsStructureHelper::field_heading($where_to_display, 'mobile|setup', 'heading4', esc_html__('Share bar customizations', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_stretched($where_to_display, 'mobile|setup', 'mobile_sharebar_text', esc_html__('Text on share bar', 'essb'), esc_html__('Customize the default share bar text (default is Share).', 'essb'));
		ESSBOptionsStructureHelper::holder_end($where_to_display, 'mobile|setup');
	
		ESSBOptionsStructureHelper::holder_start($where_to_display, 'mobile|setup', 'essb-panel-sharebuttons', 'essb-panel-sharebuttons');
		ESSBOptionsStructureHelper::field_heading($where_to_display, 'mobile|setup', 'heading4', esc_html__('Share buttons bar customizations', 'essb'));
		ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'mobile|setup');
		$listOfOptions = array("1" => "1 Button", "2" => "2 Buttons", "3" => "3 Buttons", "4" => "4 Buttons", "5" => "5 Buttons", "6" => "6 Buttons", "7" => "7 Buttons", "8" => "8 Buttons");
		ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'mobile|setup', 'mobile_sharebuttonsbar_count', esc_html__('Number of buttons in share buttons bar', 'essb'), esc_html__('Provide number of buttons you wish to see in buttons bar. If the number of activated buttons is greater than selected here the last button will be more button which will open pop up with all active buttons.', 'essb'), $listOfOptions);
		ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'mobile|setup', 'mobile_sharebuttonsbar_names', esc_html__('Display network names', 'essb'), esc_html__('Activate this option to display network names (default display is icons only).', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'mobile|setup', 'mobile_sharebuttonsbar_total', esc_html__('Display total counter', 'essb'), esc_html__('Activate this option to display total share counter as first button. If you activate it please keep in mind that you need to set number of columns to be with one more than buttons you except to see (total counter will act as single button)', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'mobile|setup', 'mobile_sharebuttonsbar_counter', esc_html__('Display button counter', 'essb'), esc_html__('Set to Yes if you wish to show individual share counter for each button in the mobile share buttons bar.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'mobile|setup', 'mobile_sharebuttonsbar_total_source', esc_html__('Total value source', 'essb'), esc_html__('Choose the source of total values that you will see - shares or views/reads (require views/reads extension)', 'essb'), array('' => 'Shares', 'views' => 'Views/Reads'));
	
		ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'mobile|setup');
	
		ESSBOptionsStructureHelper::title($where_to_display, 'mobile|setup', esc_html__('Bar appearance', 'essb'), esc_html__('By default share bar will be visible after page is fully loaded. If you wish using fields below you can set to appear/disappear on scroll.', 'essb'));
		ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'mobile|setup');
		ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'mobile|setup', 'mobile_sharebuttonsbar_showscroll', esc_html__('Show after % of content', 'essb'), esc_html__('Fill a numeric value of percent (example 10) if you wish the bar to appear on scroll.', 'essb'));
		ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'mobile|setup', 'mobile_sharebuttonsbar_hideend', esc_html__('Hide buttons bar before end of page', 'essb'), esc_html__('This option is made to hide buttons bar once you reach 90% of page content to allow the entire footer to be visible.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'mobile|setup', 'mobile_sharebuttonsbar_hideend_percent', esc_html__('% of content is viewed to hide buttons bar before end of page', 'essb'), esc_html__('Customize the default percent 90 when buttons bar will hide. Enter value in percents without the % mark.', 'essb'));
		$listOfOptions = array("" => "Bottom", "top" => "Top");
		ESSBOptionsStructureHelper::field_select_panel($where_to_display, 'mobile|setup', 'mobile_sharebuttonsbar_pos', esc_html__('Bar position on screen', 'essb'), esc_html__('Default position of bar on screen is bottom but you can swap it to top using this field.', 'essb'), $listOfOptions);
		ESSBOptionsStructureHelper::field_section_end_full_panels($where_to_display, 'mobile|setup');
	
		ESSBOptionsStructureHelper::panel_start($where_to_display, 'mobile|setup', esc_html__('Include Bottom Ad Code', 'essb'), esc_html__('Activate this option if you wish to make your mobile bar include also custom ad code below share buttons when display bottom or at the bottom when share buttons are displayed on top. The custom ad code will be always visible no matter of bar appearance rules when they are displayed separately.'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'sharebottom_adarea', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
		ESSBOptionsStructureHelper::field_color($where_to_display, 'mobile|setup', 'sharebottom_usercontent_bg', esc_html__('Setup custom background color', 'essb'), esc_html__('Enter custom background color which will apply over the ad bar. That field is required only if you wish to set such. By default bar is transparent when you see it separate from share buttons or it will have background related to bar with buttons', 'essb'));
		ESSBOptionsStructureHelper::field_switch($where_to_display, 'mobile|setup', 'sharebottom_usercontent_control', esc_html__('Apply bar appearance rules', 'essb'), esc_html__('Activate this option to make ad bar appear/disappear based on share bar rules. If option is not set bar will be always visible on screen', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_editor($where_to_display, 'mobile|setup', 'sharebottom_usercontent', esc_html__('Ad code/custom code', 'essb'), esc_html__('Shortcodes supported', 'essb'), 'htmlmixed');
		ESSBOptionsStructureHelper::panel_end($where_to_display, 'mobile|setup');
	
		ESSBOptionsStructureHelper::holder_end($where_to_display, 'mobile|setup');	
		ESSBOptionsStructureHelper::panel_end($where_to_display, 'mobile|setup');
	
		ESSBOptionsStructureHelper::panel_start($where_to_display, 'mobile|responsive', esc_html__('Responsive Mobile Share Buttons', 'essb'), esc_html__('Use responsive mode for your share buttons along with the mobile-specific display methods. The responsive mode will add the mobile-only displays and they will become visible when below specific resolution. The responsive mode will help to have a beautiful mobile share buttons display when you are using a cache plugin or server that does not support separate mobile caching (does not store a separate cache that allows server-side work).', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'mobile_css_activate', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
		ESSBOptionsStructureHelper::field_section_start_full_panels($where_to_display, 'mobile|responsive');
		ESSBOptionsStructureHelper::field_textbox_panel($where_to_display, 'mobile|responsive', 'mobile_css_screensize', esc_html__('Width of screen', 'essb'), esc_html__('Leave blank to use the default width of 750. In case you wish to customize it fill value in numbers (without px) and all devices that have screen width below will be marked as mobile.', 'essb'));
		ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'mobile|responsive', 'mobile_css_readblock', esc_html__('Hide read blocking methods', 'essb'), esc_html__('Activate this option to remove all read blocking methods on mobile devices. Read blocking display methods are Sidebar and Post Vertical Float', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'mobile|responsive', 'mobile_css_all', esc_html__('Hide all share buttons on mobile', 'essb'), esc_html__('Activate this option to hide all share buttons on mobile devices including those made with shortcodes.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_switch_panel($where_to_display, 'mobile|responsive', 'mobile_css_optimized', esc_html__('Control mobile optimized display methods', 'essb'), esc_html__('Activate this option to display mobile optimized display methods when resolution meets the mobile size that is defined. Methods that are controlled with this option include: Share Buttons Bar, Share Bar and Share Point. At least one of those methods should be selected in the settings above for additional display methods.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_section_end_panels($where_to_display, 'mobile|responsive');
		ESSBOptionsStructureHelper::panel_end($where_to_display, 'mobile|responsive');
		
		
		essb_prepare_location_advanced_customization($where_to_display, 'mobile|other', 'mobile');
		essb_prepare_location_advanced_customization_mobile($where_to_display, 'mobile|bar', 'sharebar');
		essb_prepare_location_advanced_customization_mobile($where_to_display, 'mobile|point', 'sharepoint');
		essb_prepare_location_advanced_customization_mobile($where_to_display, 'mobile|buttons', 'sharebottom');
	}
	else {
		ESSBOptionsStructureHelper::help($where_to_display, 'mobile', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/configure-share-buttons-for-mobile/', 'Step By Step Guide For Setting Up Responsive Buttons Without Dealing With Mobile Options' => 'https://docs.socialsharingplugin.com/knowledgebase/step-by-step-guide-for-setting-up-responsive-buttons-without-dealing-with-mobile-options/'));
		ESSBOptionsStructureHelper::field_component($where_to_display, 'mobile', 'essb5_advanced_mobile_activate_options', 'false');		
	}
}

if ((function_exists('is_amp_endpoint') || function_exists('ampforwp_is_amp_endpoint')) && essb_option_value('functions_mode') != 'light') {
	ESSBOptionsStructureHelper::panel_start($where_to_display, 'amp', esc_html__('Enable static share buttons on AMP posts or pages', 'essb'), '', 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'amp_positions', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));

	ESSBOptionsStructureHelper::title($where_to_display, 'amp',  esc_html__('Primary content display position', 'essb'), esc_html__('Choose default method that will be used to render buttons inside content', 'essb'));
	ESSBOptionsStructureHelper::field_single_position_select($where_to_display, 'amp', 'content_position_amp', essb_avaliable_content_positions_amp());

	ESSBOptionsStructureHelper::field_heading($where_to_display, 'amp', 'heading4', esc_html__('Customize active social networks', 'essb'));
	ESSBOptionsStructureHelper::title($where_to_display, 'amp', '', esc_html__('Choose social networks you wish to see appearing when your site is opened from a mobile device. This list is optional and you can use it in case you need to set a personalized mobile network list. Otherwise leave it blank for faster update of settings.', 'essb'), 'inner-row');

	ESSBOptionsStructureHelper::field_network_select($where_to_display, 'amp', 'amp_networks', 'amp');
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'amp');
}


function essb_prepare_location_advanced_customization($tab_id, $menu_id, $location = '', $post_type = false, $heading = '') {
	global $essb_networks, $essb_options;
	
	if ($heading != '') {
		ESSBOptionsStructureHelper::field_heading($tab_id, $menu_id, 'heading5', $heading);
	}

	$essb_networks = essb_available_social_networks();

	$checkbox_list_networks = array();
	foreach ($essb_networks as $key => $object) {
		$checkbox_list_networks[$key] = $object['name'];
	}

	if ($location != 'mobile') {
		if (!$post_type) {
			ESSBOptionsStructureHelper::help($tab_id, $menu_id, '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/additional-display-options-for-the-automatic-share-button-positions/'));
				
			ESSBOptionsStructureHelper::panel_start($tab_id, $menu_id, esc_html__('Responsive Appearance', 'essb'), '', 'ti-mobile essb-icon21', array("mode" => "toggle", "state" => "closed", "css_class" => "essb-auto-open"));
	
			ESSBOptionsStructureHelper::field_section_start_full_panels($tab_id, $menu_id);
	
			ESSBOptionsStructureHelper::field_switch_panel($tab_id, $menu_id, $location.'_mobile_deactivate', esc_html__('Do not show on mobile', 'essb'), esc_html__('Activate this option if you wish that method to be hidden when site is browsed with mobile device.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
			ESSBOptionsStructureHelper::field_switch_panel($tab_id, $menu_id, $location.'_tablet_deactivate', esc_html__('Do not show on tablet', 'essb'), esc_html__('Activate this option if you wish that method to be hidden when site is browsed with tablet device.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
			ESSBOptionsStructureHelper::field_switch_panel($tab_id, $menu_id, $location.'_desktop_deactivate', esc_html__('Do not show on desktop', 'essb'), esc_html__('Activate this option if you wish that method to be hidden when site is browsed with desktop.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	
			if ($location != 'postbar' && $location != 'point') {
				if (essb_option_bool_value('native_active')) {
					ESSBOptionsStructureHelper::field_switch_panel($tab_id, $menu_id, $location.'_native_deactivate', esc_html__('Deactivate native buttons', 'essb'), esc_html__('Activate this option if you wish to deactivate native buttons for that display method.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
				}
	
				if (!$post_type) {
					ESSBOptionsStructureHelper::field_switch_panel($tab_id, $menu_id, $location.'_text_deactivate', esc_html__('Hide message above, before', 'essb'), esc_html__('Activate this option if you wish to hide message above, before or below for that display.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
				}
			}
			ESSBOptionsStructureHelper::field_section_end_full_panels($tab_id, $menu_id);
			ESSBOptionsStructureHelper::panel_end($tab_id, $menu_id);
		}
	}
	
	/**
	 * Adaptive styles are running. Switching off the location settings if so
	 */
	if (essb_option_bool_value('activate_automatic_position')) {
		return;
	}

	$panel_title = "";
	if (!$post_type) {
		$panel_title = esc_html__('Personalize buttons on that display position', 'essb');
	}
	else {
		$panel_title = esc_html__('Personalize buttons for that post type', 'essb');
	}

	if ($location == 'mobile') {
		$panel_title = esc_html__('Personalize buttons that are displayed on mobile device', 'essb');
	}

	/**
	 * Including the style manager inside settings menu. This will allow to save the style
	 * or install a saved style from the library with just a single click
	 */
	essb5_stylemanager_include_menu($tab_id, $menu_id, $location, essb_option_bool_value($location.'_activate') ? 'true' : '');

	if (!$post_type) {
		ESSBOptionsStructureHelper::panel_start($tab_id, $menu_id, esc_html__('Custom Code for The Display Method'), esc_html__('Using these options you can add a custom code that will appear before and/or after the share buttons display. With the proper code, you can fully customize and change the display look. The code fields support HTML, shortcodes, CSS and etc.', 'essb'), 'essb-icon21 fa fa-terminal', array("mode" => "switch", 'switch_id' => $location.'_code', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb'), 'switch_submit' => 'true'));
		if (essb_option_bool_value($location.'_code')) {
			ESSBOptionsStructureHelper::hint($tab_id, $menu_id, '', esc_html__('The custom code fields supports HTML, shortcode, custom CSS (wrapped in styles tag). If you wish to write a specific custom CSS code working for this display only, you can use the {instance_class} variable - example: ', 'essb'). '<code>&lt;style type="text/css"&gt;{instance_class} li a { color: red !important; }</style></code>');				
			ESSBOptionsStructureHelper::field_editor($tab_id, $menu_id, $location.'_code_before', esc_html__('Before Display', 'essb'), '', 'htmlmixed');
			ESSBOptionsStructureHelper::field_editor($tab_id, $menu_id, $location.'_code_after', esc_html__('After Display', 'essb'), '', 'htmlmixed');
		}
		ESSBOptionsStructureHelper::panel_end($tab_id, $menu_id);
	}

	$panel_title = esc_html__('Configure Custom Share Button Style & Network List', 'essb');
	ESSBOptionsStructureHelper::panel_start($tab_id, $menu_id, $panel_title, esc_html__('Enable this option to configure a personal design and network list, working for this display only. The settings you do will overwrite the global styles and networks.', 'essb'), 'essb-icon21 ti-layout-accordion-list', array("mode" => "switch", 'switch_id' => $location.'_activate', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb'), 'switch_submit' => 'true'));

	if (essb_option_bool_value($location.'_activate')) {

		if ($location != 'postbar' && $location != 'point') {
			// Position Style Settings version 5
			ESSBOptionsStructureHelper::holder_start($tab_id, $menu_id, 'essb-advanced-location-setup');

			ESSBOptionsStructureHelper::help($tab_id, $menu_id, esc_html__('Setting a Custom Share Buttons Style', 'essb'), esc_html__('Right now you activate the personalized location settings. When personalized location settings are active plugin will always take the settings here and it will not refer to the global changes you made.', 'essb'), array('Help with Style Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/social-sharing-share-buttons-style/'));


			ESSBOptionsStructureHelper::structure_row_start($tab_id, $menu_id);
			ESSBOptionsStructureHelper::structure_section_start($tab_id, $menu_id, 'c4');

			ESSBOptionsStructureHelper::title($tab_id, $menu_id, esc_html__('Template', 'essb'), '', 'inner-row');
			ESSBOptionsStructureHelper::field_template_select($tab_id, $menu_id, $location.'_template', $location);

			ESSBOptionsStructureHelper::title($tab_id, $menu_id, esc_html__('Buttons style', 'essb'), '', 'inner-row');
			ESSBOptionsStructureHelper::field_buttonstyle_select($tab_id, $menu_id, $location.'_button_style', $location);

			//essb5_main_alignment_choose
			$select_values = array('' => array('title' => 'Left', 'content' => '<i class="ti-align-left"></i>'),
					'center' => array('title' => 'Center', 'content' => '<i class="ti-align-center"></i>'),
					'right' => array('title' => 'Right', 'content' => '<i class="ti-align-right"></i>'),
					'stretched' => array('title' => 'Stetched', 'content' => '<i class="ti-layout-slider"></i>'));
			ESSBOptionsStructureHelper::title($tab_id, $menu_id, esc_html__('Buttons align', 'essb'), '', 'inner-row');
			ESSBOptionsStructureHelper::field_toggle($tab_id, $menu_id, $location.'_button_pos', '', '', $select_values, '', '', '', 'essb-align-selector control_essb-width-section-'.$location);

			$select_values = array('' => array('title' => 'Default', 'content' => 'Default'),
					'xs' => array('title' => 'Extra Small', 'content' => 'XS'),
					's' => array('title' => 'Small', 'content' => 'S'),
					'm' => array('title' => 'Medium', 'content' => 'M'),
					'l' => array('title' => 'Large', 'content' => 'L'),
					'xl' => array('title' => 'Extra Large', 'content' => 'XL'),
					'xxl' => array('title' => 'Extra Extra Large', 'content' => 'XXL')
			);

			ESSBOptionsStructureHelper::title($tab_id, $menu_id, esc_html__('Buttons size', 'essb'), '', 'inner-row');
			ESSBOptionsStructureHelper::field_toggle($tab_id, $menu_id, $location.'_button_size', '', '', $select_values, '', '', 'button_size');


			ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_nospace', esc_html__('Without space between buttons', 'essb'), esc_html__('Activate this option if you wish to connect share buttons without any space between them.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '8');
			//essb5_main_animation_selection
			ESSBOptionsStructureHelper::title($tab_id, $menu_id, esc_html__('Animate share buttons', 'essb'), '', 'inner-row');
			ESSBOptionsStructureHelper::field_animation_select($tab_id, $menu_id, $location.'_css_animations', $location);


			ESSBOptionsStructureHelper::structure_section_end($tab_id, $menu_id);
			ESSBOptionsStructureHelper::structure_section_start($tab_id, $menu_id, 'c4');

			ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_show_counter', esc_html__('Display counter of sharing', 'essb'), esc_html__('Activate display of share counters.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'), '', '8');
			//essb5_main_singlecounter_selection
			ESSBOptionsStructureHelper::title($tab_id, $menu_id, esc_html__('Single button share counter position', 'essb'), '', 'inner-row');
			ESSBOptionsStructureHelper::field_counterposition_select($tab_id, $menu_id, $location.'_counter_pos', $location);

			ESSBOptionsStructureHelper::title($tab_id, $menu_id, esc_html__('Total share counter position', 'essb'), '', 'inner-row');
			ESSBOptionsStructureHelper::field_totalcounterposition_select($tab_id, $menu_id, $location.'_total_counter_pos', $location);

			//essb5_main_totalcoutner_selection

			ESSBOptionsStructureHelper::structure_section_end($tab_id, $menu_id);
			ESSBOptionsStructureHelper::structure_section_start($tab_id, $menu_id, 'c4', '', '', 'top', '', 'essb-width-section-'.$location);
			ESSBOptionsStructureHelper::title($tab_id, $menu_id, esc_html__('Button width', 'essb'), '', 'inner-row');

			$select_values = array('' => array('title' => 'Automatic Width', 'content' => 'AUTO', 'isText'=>true),
					'fixed' => array('title' => 'Fixed Width', 'content' => 'Fixed', 'isText'=>true),
					'full' => array('title' => 'Full Width', 'content' => 'Full', 'isText'=>true),
					'flex' => array('title' => 'Fluid', 'content' => 'Fluid', 'isText'=>true),
					'column' => array('title' => 'Columns', 'content' => 'Columns', 'isText'=>true),);
			ESSBOptionsStructureHelper::field_toggle($tab_id, $menu_id, $location.'_button_width', '', '', $select_values);


			ESSBOptionsStructureHelper::holder_start($tab_id, $menu_id, $location.'-essb-fixed-width essb-hidden-open', $location.'-essb-fixed-width');
			ESSBOptionsStructureHelper::title($tab_id, $menu_id, esc_html__('Customize fixed width display', 'essb'), esc_html__('In fixed width mode buttons will have exactly same width defined by you no matter of device or screen resolution (not responsive).', 'essb'), 'inner-row');
			ESSBOptionsStructureHelper::field_section_start_panels($tab_id, $menu_id, '', esc_html__('Customize the fixed width options', 'essb'));
			ESSBOptionsStructureHelper::field_textbox_panel($tab_id, $menu_id, $location.'_fixed_width_value', esc_html__('Custom buttons width', 'essb'), esc_html__('Provide custom width of button in pixels without the px symbol.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
			ESSBOptionsStructureHelper::field_select_panel($tab_id, $menu_id, $location.'_fixed_width_align', esc_html__('Choose alignment of network name', 'essb'), esc_html__('Provide different alignment of network name, when fixed button width is activated. When counter position is Inside or Inside name, that alignment will be applied for the counter. Default value is center.', 'essb'), array("" => "Center", "left" => "Left", "right" => "Right"));
			ESSBOptionsStructureHelper::field_section_end_panels($tab_id, $menu_id);
			ESSBOptionsStructureHelper::holder_end($tab_id, $menu_id);

			ESSBOptionsStructureHelper::holder_start($tab_id, $menu_id, $location.'-essb-full-width essb-hidden-open', $location.'-essb-full-width');
			ESSBOptionsStructureHelper::title($tab_id, $menu_id, esc_html__('Customize full width display', 'essb'), esc_html__('In full width mode buttons will distribute over the entire screen width on each device (responsive).', 'essb'), 'inner-row');
			ESSBOptionsStructureHelper::field_select_panel($tab_id, $menu_id, $location.'_fullwidth_align', esc_html__('Choose alignment of network name', 'essb'), esc_html__('Provide different alignment of network name (counter when position inside or inside name). Default value is left.', 'essb'), array("left" => "Left", "center" => "Center", "right" => "Right"));
			ESSBOptionsStructureHelper::field_section_start_panels($tab_id, $menu_id, esc_html__('Customize width of first two buttons', 'essb'), esc_html__('Provide different width for the first two buttons in the row. The width should be entered as number in percents (without the % mark). You can fill only one of the values or both values.', 'essb'), '', 'true');
			ESSBOptionsStructureHelper::field_textbox_panel($tab_id, $menu_id, $location.'_fullwidth_first_button', esc_html__('Width of first button', 'essb'), esc_html__('Provide custom width of first button when full width is active. This value is number in percents without the % symbol.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
			ESSBOptionsStructureHelper::field_textbox_panel($tab_id, $menu_id, $location.'_fullwidth_second_button', esc_html__('Width of second button', 'essb'), esc_html__('Provide custom width of second button when full width is active. This value is number in percents without the % symbol.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
			ESSBOptionsStructureHelper::field_section_end_panels($tab_id, $menu_id);

			ESSBOptionsStructureHelper::panel_start($tab_id, $menu_id, esc_html__('Fix button apperance', 'essb'), esc_html__('Full width share buttons uses formula to calculate the best width of buttons. In some cases based on other site styles you may need to personalize some of the values in here', 'essb'), '', array("mode" => "toggle", 'state' => 'closed'));
			ESSBOptionsStructureHelper::field_section_start_panels($tab_id, $menu_id, '', esc_html__('Full width option will make buttons to take the width of your post content area.', 'essb'));
			ESSBOptionsStructureHelper::field_textbox_panel($tab_id, $menu_id, $location.'_fullwidth_share_buttons_correction', esc_html__('Max width of button on desktop', 'essb'), esc_html__('Provide custom width of single button when full width is active. This value is number in percents without the % symbol.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
			ESSBOptionsStructureHelper::field_textbox_panel($tab_id, $menu_id, $location.'_fullwidth_share_buttons_correction_mobile', esc_html__('Max width of button on mobile', 'essb'), esc_html__('Provide custom width of single button when full width is active. This value is number in percents without the % symbol.', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
			ESSBOptionsStructureHelper::field_textbox_panel($tab_id, $menu_id, $location.'_fullwidth_share_buttons_container', esc_html__('Max width of buttons container element', 'essb'), esc_html__('If you wish to display total counter along with full width share buttons please provide custom max width of buttons container in percent without % (example: 90). Leave this field blank for default value of 100 (100%).', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
			ESSBOptionsStructureHelper::field_section_end_panels($tab_id, $menu_id);
			ESSBOptionsStructureHelper::panel_end($tab_id, $menu_id);
			ESSBOptionsStructureHelper::holder_end($tab_id, $menu_id);

			ESSBOptionsStructureHelper::holder_start($tab_id, $menu_id, $location.'-essb-column-width essb-hidden-open', $location.'-essb-column-width');
			ESSBOptionsStructureHelper::title($tab_id, $menu_id, esc_html__('Customize column display', 'essb'), esc_html__('In column mode buttons will distribute over the entire screen width on each device in the number of columns you setup (responsive).', 'essb'), 'inner-row');
			ESSBOptionsStructureHelper::field_section_start_panels($tab_id, $menu_id, '', '');
			$listOfOptions = array("1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "6" => "6", "7" => "7", "8" => "8", "9" => "9", "10" => "10");
			ESSBOptionsStructureHelper::field_select_panel($tab_id, $menu_id, $location.'_fullwidth_share_buttons_columns', esc_html__('Number of columns', 'essb'), esc_html__('Choose the number of columns that buttons will be displayed.', 'essb'), $listOfOptions);
			ESSBOptionsStructureHelper::field_select_panel($tab_id, $menu_id, $location.'_fullwidth_share_buttons_columns_align', esc_html__('Choose alignment of network name', 'essb'), esc_html__('Provide different alignment of network name (counter when position inside or inside name). Default value is left.', 'essb'), array("" => "Left", "center" => "Center", "right" => "Right"));
			ESSBOptionsStructureHelper::field_section_end_panels($tab_id, $menu_id);
			ESSBOptionsStructureHelper::holder_end($tab_id, $menu_id);

			ESSBOptionsStructureHelper::holder_start($tab_id, $menu_id, $location.'-essb-flex-width essb-hidden-open', $location.'-essb-flex-width');
			ESSBOptionsStructureHelper::title($tab_id, $menu_id, esc_html__('Customize Flex Buttons', 'essb'), esc_html__('In flexible width mode buttons will always take the full width of content area. You can customize the alignment or preserve space for the total area.', 'essb'), 'inner-row');
			ESSBOptionsStructureHelper::field_section_start_panels($tab_id, $menu_id, '', esc_html__('Customize the flex width options', 'essb'));
			ESSBOptionsStructureHelper::field_textbox_panel($tab_id, $menu_id, $location.'_flex_width_value', esc_html__('Preserve Space For Total Counter (%)', 'essb'), esc_html__('Use this field to setup custom width for the total counter area (numeric value only as a percent)', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
			ESSBOptionsStructureHelper::field_textbox_panel($tab_id, $menu_id, $location.'_flex_button_value', esc_html__('Assign a Specific Button Width (%)', 'essb'), esc_html__('Use this field to setup custom width for single button (numeric value only as a percent)', 'essb'), '', 'input60', 'fa-arrows-h', 'right');
			ESSBOptionsStructureHelper::field_select_panel($tab_id, $menu_id, $location.'_flex_width_align', esc_html__('Choose alignment of network name', 'essb'), esc_html__('Provide different alignment of network name, when this button width is activated. When counter position is Inside or Inside name, that alignment will be applied for the counter. Default value is center.', 'essb'), array("" => "Left", "center" => "Center", "right" => "Right"));
			ESSBOptionsStructureHelper::field_section_end_panels($tab_id, $menu_id);
			ESSBOptionsStructureHelper::holder_end($tab_id, $menu_id);

			ESSBOptionsStructureHelper::structure_section_end($tab_id, $menu_id);
			ESSBOptionsStructureHelper::structure_row_end($tab_id, $menu_id);

			ESSBOptionsStructureHelper::title($tab_id, $menu_id, esc_html__('Live Style Preview', 'essb'), esc_html__('This style preview is illustrative showing how your buttons will look. All displayed share counters are random generated for preview purpose - real share values will appear on each post. Once you save settings you will be able to test the exact preview on site with networks you choose', 'essb'));
			ESSBOptionsStructureHelper::field_func($tab_id, $menu_id, 'essb5_live_preview_single_position', '', '', '', array('position' => $location));

			ESSBOptionsStructureHelper::holder_end($tab_id, $menu_id);

		}

		if ($location != 'mobile' && !essb_option_bool_value('user_fixed_networks')) {
			ESSBOptionsStructureHelper::field_heading($tab_id, $menu_id, 'heading5', esc_html__('Personalize social networks', 'essb'));
			ESSBOptionsStructureHelper::tabs_start($tab_id, $menu_id, $location.'buttons-tabs', array('<i class="ti-settings" style="margin-right: 5px;"></i>'.esc_html__('Social Networks', 'essb'), '<i class="ti-settings" style="margin-right: 5px;"></i>'.esc_html__('Additional Network Options', 'essb')), 'false', 'true');
			ESSBOptionsStructureHelper::tab_start($tab_id, $menu_id, $location.'buttons-tabs-0', 'true');

			ESSBOptionsStructureHelper::help($tab_id, $menu_id, esc_html__('Setting a Custom Social Network List', 'essb'), esc_html__('When no networks are selected inside the networks field below, plugin will always use those that you set inside Social Sharing settings. In case you need to change that list, please do that here. Also remember that once you setup a personalized list of networks any other global change will not reflect here (until at least one network is selected)', 'essb'), array('Help with Networks Setup' => 'https://docs.socialsharingplugin.com/knowledgebase/social-sharing-setup-social-networks/', 'How to work with more button' => 'https://docs.socialsharingplugin.com/knowledgebase/social-sharing-how-to-set-up-more-button/'));

			ESSBOptionsStructureHelper::title($tab_id, $menu_id, esc_html__('Personalize Displayed Social Networks', 'essb'), esc_html__('Choose a personalized list of social networks that will run for this display only (different than global set). Leave blank to use the global set list of social networks', 'essb'));
			ESSBOptionsStructureHelper::field_network_select($tab_id, $menu_id, $location.'_networks', $location);

			ESSBOptionsStructureHelper::tab_end($tab_id, $menu_id);

			ESSBOptionsStructureHelper::tab_start($tab_id, $menu_id, $location.'buttons-tabs-1');

			if (essb_is_active_social_network('more')) {
				ESSBOptionsStructureHelper::panel_start($tab_id, $menu_id, esc_html__('More Button', 'essb'), esc_html__('Configure additional options for this network', 'essb'), 'fa21 essb_icon_more', array("mode" => "toggle", 'state' => 'opened'));
				$more_options = array ("1" => "Display all active networks after more button", "2" => "Display all social networks as pop up", "3" => "Display only active social networks as pop up", "4" => "Display all active networks after more button in popup" );
				ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_more_button_func', esc_html__('More button', 'essb'), esc_html__('Select networks that you wish to appear in your list. With drag and drop you can rearrange them.', 'essb'), essb_available_more_button_commands());
				$more_options = array ("plus" => "Plus icon", "dots" => "Dots icon" );

				$select_values = array('plus' => array('title' => 'Plus Icon', 'content' => '<i class="essb_icon_more"></i>'),
						'dots' => array('title' => 'Dots Icon', 'content' => '<i class="essb_icon_more_dots"></i>'));
				ESSBOptionsStructureHelper::field_toggle($tab_id, $menu_id, $location.'_more_button_icon', esc_html__('More button icon', 'essb'), esc_html__('Select more button icon style. You can choose from default + symbol or dots symbol', 'essb'), $select_values);
				ESSBOptionsStructureHelper::panel_end($tab_id, $menu_id);
			}

			if (essb_is_active_social_network('share')) {
				ESSBOptionsStructureHelper::panel_start($tab_id, $menu_id, esc_html__('Share Button', 'essb'), esc_html__('Configure additional options for this network', 'essb'), 'fa21 essb_icon_share', array("mode" => "toggle", 'state' => 'opened'));
				$more_options = array ("1" => "Display all active networks after more button", "2" => "Display all social networks as pop up", "3" => "Display only active social networks as pop up", "4" => "Display all active networks after more button in popup" );
				ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_share_button_func', esc_html__('Share button function', 'essb'), esc_html__('Select networks that you wish to appear in your list. With drag and drop you can rearrange them.', 'essb'), essb_available_more_button_commands());


				$select_values = array('plus' => array('title' => '', 'content' => '<i class="essb_icon_more"></i>'),
						'dots' => array('title' => '', 'content' => '<i class="essb_icon_more_dots"></i>'),
						'share' => array('title' => '', 'content' => '<i class="essb_icon_share"></i>'),
						'share-alt-square' => array('title' => '', 'content' => '<i class="essb_icon_share-alt-square"></i>'),
						'share-alt' => array('title' => '', 'content' => '<i class="essb_icon_share-alt"></i>'),
						'share-tiny' => array('title' => '', 'content' => '<i class="essb_icon_share-tiny"></i>'),
						'share-outline' => array('title' => '', 'content' => '<i class="essb_icon_share-outline"></i>')
				);
				ESSBOptionsStructureHelper::field_toggle($tab_id, $menu_id, $location.'_share_button_icon', esc_html__('Share button icon', 'essb'), esc_html__('Choose the share button icon you will use (default is share if nothing is selected)', 'essb'), $select_values);


				$more_options = array ("" => "Default from settings (like other share buttons)", "icon" => "Icon only", "button" => "Button", "text" => "Text only" );
				ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_share_button_style', esc_html__('Share button style', 'essb'), esc_html__('Select more button icon style. You can choose from default + symbol or dots symbol', 'essb'), $more_options);

				$share_counter_pos = array("hidden" => "No counter", "inside" => "Inside button without text", "insidename" => "Inside button after text", "insidebeforename" => "Inside button before text", "topn" => "Top", "bottom" => "Bottom");
				ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_share_button_counter', esc_html__('Display total counter with the following position', 'essb'), esc_html__('Choose where you wish to display total counter of shares assigned with this button. <br/> To view total counter you need to have share counters active and they should not be running in real time mode. Also you need to have your share button set with style button. When you use share button with counter we highly recommend to hide total counter by setting position to be hidden - this will avoid having two set of total value on screen.', 'essb'), $share_counter_pos);

				ESSBOptionsStructureHelper::panel_end($tab_id, $menu_id);
			}

			ESSBOptionsStructureHelper::tab_end($tab_id, $menu_id);
			ESSBOptionsStructureHelper::tabs_end($tab_id, $menu_id);


		}
	}
	ESSBOptionsStructureHelper::panel_end($tab_id, $menu_id); // customization

}


function essb_prepare_location_advanced_customization_mobile($tab_id, $menu_id, $location = '') {
	global $essb_networks;

	$checkbox_list_networks = array();
	foreach ($essb_networks as $key => $object) {
		$checkbox_list_networks[$key] = $object['name'];
	}

	ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_activate', esc_html__('I wish to personalize global button settings for that location', 'essb'), esc_html__('Activate this option to apply personalized settings for that display location that will overwrite the global.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	ESSBOptionsStructureHelper::field_heading($tab_id, $menu_id, 'heading5', esc_html__('Visual Changes', 'essb'));

	ESSBOptionsStructureHelper::field_section_start($tab_id, $menu_id, esc_html__('Set button style', 'essb'), '');

	ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_template', esc_html__('Template', 'essb'), esc_html__('Select your template for that display location.', 'essb'), essb_available_tempaltes4(true));

	if ($location != 'sharebottom') {
		ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_nospace', esc_html__('Remove spacing between buttons', 'essb'), esc_html__('Activate this option to remove default space between share buttons.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
	}

	ESSBOptionsStructureHelper::field_section_end($tab_id, $menu_id);

	if ($location != 'sharebottom') {
		ESSBOptionsStructureHelper::field_section_start($tab_id, $menu_id, esc_html__('Counter settings', 'essb'), '');
		ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_show_counter', esc_html__('Display counter of sharing', 'essb'), esc_html__('Activate display of share counters.', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
		ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_counter_pos', esc_html__('Position of counters', 'essb'), esc_html__('Choose your default button counter position', 'essb'), essb_avaliable_counter_positions_mobile());
		ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_total_counter_pos', esc_html__('Position of total counter', 'essb'), esc_html__('For vertical display methods left means before buttons (top) and right means after buttons (bottom).', 'essb'), essb_avaiable_total_counter_position_mobile());
		ESSBOptionsStructureHelper::field_section_end($tab_id, $menu_id);
	}
	
	if (!essb_option_bool_value('user_fixed_networks')) {
		ESSBOptionsStructureHelper::field_section_start($tab_id, $menu_id, esc_html__('Personalize social networks', 'essb'), '');
	
		ESSBOptionsStructureHelper::title($tab_id, $menu_id, esc_html__('Personalize Displayed Social Networks', 'essb'), esc_html__('Choose a personalized list of social networks that will run for this display only (different than global set). Leave blank to use the global set list of social networks', 'essb'));
		ESSBOptionsStructureHelper::field_network_select($tab_id, $menu_id, $location.'_networks', $location);
		ESSBOptionsStructureHelper::field_section_end($tab_id, $menu_id);
	}
}


function essb3_register_positions_by_posttypes() {
	global $wp_post_types, $where_to_display;
	$where_to_display = 'where';
	ESSBOptionsStructureHelper::help($where_to_display, 'positions|positions_posttype', '', '', array('Help with Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/working-with-automatic-display-positions-for-social-share-buttons-and-plugin-integrations/#Different_Positions_by_Post_Types'));	
	ESSBOptionsStructureHelper::panel_start($where_to_display, 'positions|positions_posttype', esc_html__('Enable different positions by post type', 'essb'), '', 'fa21 ti-layout', array("mode" => "switch", 'switch_id' => 'positions_by_pt', 'switch_on' => esc_html__('Yes', 'essb'), 'switch_off' => esc_html__('No', 'essb')));
	$pts = get_post_types ( array ('show_ui' => true, '_builtin' => true ) );
	$cpts = get_post_types ( array ('show_ui' => true, '_builtin' => false ) );
	$first_post_type = "";
	$key = 1;
	foreach ( $pts as $pt ) {
		ESSBOptionsStructureHelper::holder_start($where_to_display, 'positions|positions_posttype', 'essb-position-posttype', 'essb-position-posttype-'.esc_attr($pt));
		
		ESSBOptionsStructureHelper::field_heading($where_to_display, 'positions|positions_posttype', 'heading6', $wp_post_types [$pt]->label);
		ESSBOptionsStructureHelper::structure_row_start($where_to_display, 'positions|positions_posttype');
		ESSBOptionsStructureHelper::structure_section_start($where_to_display, 'positions|positions_posttype', 'c6', esc_html__('Primary content display position', 'essb'), esc_html__('Choose default in content position that will be used for that post type', 'essb'));
		ESSBOptionsStructureHelper::field_select($where_to_display, 'positions|positions_posttype', 'content_position_'.$pt, '', '', essb_simplified_radio_check_list(essb_avaliable_content_positions(), true));
		ESSBOptionsStructureHelper::structure_section_end($where_to_display, 'positions|positions_posttype');

		ESSBOptionsStructureHelper::structure_section_start($where_to_display, 'positions|positions_posttype', 'c6', esc_html__('Additional button display positions', 'essb'), esc_html__('Choose additional site display position that will be used for that post type', 'essb'));
		ESSBOptionsStructureHelper::field_checkbox_list($where_to_display, 'positions|positions_posttype', 'button_position_'.$pt, '', '', essb_simplified_radio_check_list(essb_available_button_positions()));
		ESSBOptionsStructureHelper::structure_section_end($where_to_display, 'positions|positions_posttype');
		ESSBOptionsStructureHelper::structure_row_end($where_to_display, 'positions|positions_posttype');
		ESSBOptionsStructureHelper::holder_end($where_to_display, 'positions|positions_posttype');
	}

	foreach ( $cpts as $cpt ) {
		ESSBOptionsStructureHelper::holder_start($where_to_display, 'positions|positions_posttype', 'essb-position-posttype', 'essb-position-posttype-'.esc_attr($cpt));
		
		ESSBOptionsStructureHelper::field_heading($where_to_display, 'positions|positions_posttype', 'heading6', $wp_post_types [$cpt]->label);
		ESSBOptionsStructureHelper::structure_row_start($where_to_display, 'positions|positions_posttype');
		ESSBOptionsStructureHelper::structure_section_start($where_to_display, 'positions|positions_posttype', 'c6', esc_html__('Primary content display position', 'essb'), esc_html__('Choose default in content position that will be used for that post type', 'essb'));
		ESSBOptionsStructureHelper::field_select($where_to_display, 'positions|positions_posttype', 'content_position_'.$cpt, '', '', essb_simplified_radio_check_list(essb_avaliable_content_positions(), true));
		ESSBOptionsStructureHelper::structure_section_end($where_to_display, 'positions|positions_posttype');

		ESSBOptionsStructureHelper::structure_section_start($where_to_display, 'positions|positions_posttype', 'c6', esc_html__('Additional button display positions', 'essb'), esc_html__('Choose additional site display position that will be used for that post type', 'essb'));
		ESSBOptionsStructureHelper::field_checkbox_list($where_to_display, 'positions|positions_posttype', 'button_position_'.$cpt, '', '', essb_simplified_radio_check_list(essb_available_button_positions()));
		ESSBOptionsStructureHelper::structure_section_end($where_to_display, 'positions|positions_posttype');
		ESSBOptionsStructureHelper::structure_row_end($where_to_display, 'positions|positions_posttype');
		ESSBOptionsStructureHelper::holder_end($where_to_display, 'positions|positions_posttype');

	}

	/**
	 * Separate settings for homepage
	 */
	$custom_pt = 'homepage';
	ESSBOptionsStructureHelper::holder_start($where_to_display, 'positions|positions_posttype', 'essb-position-posttype', 'essb-position-posttype-'.esc_attr($custom_pt));
	
	ESSBOptionsStructureHelper::field_heading($where_to_display, 'positions|positions_posttype', 'heading6', 'Homepage');
	ESSBOptionsStructureHelper::structure_row_start($where_to_display, 'positions|positions_posttype');
	ESSBOptionsStructureHelper::structure_section_start($where_to_display, 'positions|positions_posttype', 'c6', esc_html__('Primary content display position', 'essb'), esc_html__('Choose default in content position that will be used for that post type', 'essb'));
	ESSBOptionsStructureHelper::field_select($where_to_display, 'positions|positions_posttype', 'content_position_'.$custom_pt, '', '', essb_simplified_radio_check_list(essb_avaliable_content_positions(), true));
	ESSBOptionsStructureHelper::structure_section_end($where_to_display, 'positions|positions_posttype');
	
	ESSBOptionsStructureHelper::structure_section_start($where_to_display, 'positions|positions_posttype', 'c6', esc_html__('Additional button display positions', 'essb'), esc_html__('Choose additional site display position that will be used for that post type', 'essb'));
	ESSBOptionsStructureHelper::field_checkbox_list($where_to_display, 'positions|positions_posttype', 'button_position_'.$custom_pt, '', '', essb_simplified_radio_check_list(essb_available_button_positions()));
	ESSBOptionsStructureHelper::structure_section_end($where_to_display, 'positions|positions_posttype');
	ESSBOptionsStructureHelper::structure_row_end($where_to_display, 'positions|positions_posttype');
	ESSBOptionsStructureHelper::holder_end($where_to_display, 'positions|positions_posttype');
	
	ESSBOptionsStructureHelper::panel_end($where_to_display, 'positions|positions_posttype');
}

function essb3_post_type_select() {
	global $essb_admin_options, $wp_post_types;

	$pts = get_post_types ( array ('show_ui' => true, '_builtin' => true ) );
	$cpts = get_post_types ( array ('show_ui' => true, '_builtin' => false ) );

	$current_posttypes = array();
	if (is_array($essb_admin_options)) {
		$current_posttypes = essb_options_value('display_in_types', array());
	}

	if (!is_array($current_posttypes)) {
		$current_posttypes = array();
	}
	echo '<ul class="essb-posttype-selection">';

	foreach ($pts as $pt) {
		$selected = in_array ( $pt, $current_posttypes ) ? 'checked="checked"' : '';
		printf('<li><input type="checkbox" name="essb_options[display_in_types][]" id="%1$s" value="%1$s" %2$s> <label for="%1$s">%3$s</label></li>', $pt, $selected, $wp_post_types [$pt]->label);
	}

	foreach ($cpts as $pt) {
		$selected = in_array ( $pt, $current_posttypes  ) ? 'checked="checked"' : '';
		printf('<li><input type="checkbox" name="essb_options[display_in_types][]" id="%1$s" value="%1$s" %2$s> <label for="%1$s">%3$s</label></li>', $pt, $selected, $wp_post_types [$pt]->label);
	}

	$selected = in_array ( 'all_lists', $current_posttypes  ) ? 'checked="checked"' : '';
	printf('<li><input type="checkbox" name="essb_options[display_in_types][]" id="%1$s" value="%1$s" %2$s> <label for="%1$s">%3$s</label></li>', 'all_lists', $selected, 'Lists of articles (blog, archives, search results, etc.)');

	$selected = in_array ( 'homepage', $current_posttypes  ) ? 'checked="checked"' : '';
	printf('<li><input type="checkbox" name="essb_options[display_in_types][]" id="%1$s" value="%1$s" %2$s> <label for="%1$s">%3$s</label></li>', 'homepage', $selected, 'Homepage');
	
	echo '</ul>';
}

function essb5_live_preview_single_position($options) {
	$element_options = isset($options['element_options']) ? $options['element_options'] : array();
	$position = isset($element_options['position']) ? $element_options['position'] : '';

	// if there is no position we cannot display live preview
	if ($position == '') { return; }
	
	// preparing actual list of social networks from general plugin section screen
	$all_networks = essb_available_social_networks(true);
	$active_networks = essb_option_value('networks');
	$r = array();
	if (!is_array($active_networks)) {
		$r[] = array('key' => 'facebook', 'name' => 'Facebook');
		$r[] = array('key' => 'twitter', 'name' => 'Twitter');
		$r[] = array('key' => 'pinterest', 'name' => 'Pinterest');
		$r[] = array('key' => 'linkedin', 'name' => 'LinkedIn');
	}
	else {
		foreach ($active_networks as $key) {
			$r[] = array('key' => $key, 'name' => isset($all_networks[$key]) ? $all_networks[$key]['name'] : $key);
		}
	}

	$code = '<div class="essb-component-buttons-livepreview" data-settings="essb_'.$position.'_global_preview">';
	$code .= '</div>';

	$code .= "<script type=\"text/javascript\">

	var essb_".$position."_global_preview = {
	'networks': ".json_encode($r).",
	'template': 'essb_field_".$position."_template',
	'button_style': 'essb_field_".$position."_button_style',
	'button_size': 'essb_options_".$position."_button_size',
	'align': 'essb_options_".$position."_button_pos',
	'nospace': 'essb_field_".$position."_nospace',
	'counter': 'essb_field_".$position."_show_counter',
	'counter_pos': 'essb_field_".$position."_counter_pos',
	'total_counter_pos': 'essb_field_".$position."_total_counter_pos',
	'width': 'essb_options_".$position."_button_width',
	'animation': 'essb_field_".$position."_css_animations',
	'fixed_width': 'essb_options_".$position."_fixed_width_value',
	'fixed_align': 'essb_options_".$position."_fixed_width_align',
	'columns_count': 'essb_options_".$position."_fullwidth_share_buttons_columns',
	'columns_align': 'essb_options_".$position."_fullwidth_share_buttons_columns_align',
	'full_button': 'essb_options_".$position."_fullwidth_share_buttons_correction',
	'full_align': 'essb_options_".$position."_fullwidth_align',
	'full_first': 'essb_options_".$position."_fullwidth_first_button',
	'full_second': 'essb_options_".$position."_fullwidth_second_button',
	'flex_align': 'essb_options_".$position."_flex_width_align',
	'flex_width': 'essb_options_".$position."_flex_width_value',
	'flex_button': 'essb_options_".$position."_flex_button_value'
	};

	</script>";

	echo $code;
}

function essb5_custom_positions($opts = array()) {
	?>
	<a href="#" class="ao-new-subscribe-design ao-new-display-position"><span class="essb_icon fa fa-plus-square"></span><span><?php esc_html_e('Add New Custom Display/Position', 'essb'); ?></span></a>
	<div id="ao-custom-positions"></div>
	<script type="text/javascript">
	var ao_user_positions = <?php echo json_encode(essb5_get_custom_positions()); ?>;
	jQuery(document).ready(function($){
		var ao_user_positions_draw = window.ao_user_positions_draw = function() {
			var r = [];

			if (ao_user_positions) {
				for (var key in ao_user_positions) {
					r.push('<div class="advancedoptions-tile" style="margin-bottom: 20px;">');
					r.push('<div class="advancedoptions-tile-head">');
					r.push('<div class="advancedoptions-tile-head-title">');
					r.push('	<h3>'+ao_user_positions[key]+'</h3>');
					r.push('	<span class="status tag" style="margin-left: 10px;">'+key+'</span>');
					r.push('</div>');
					r.push('<div class="advancedoptions-tile-head-tools">');
					r.push('<a href="#" class="essb-btn tile-deactivate ao-remove-display-position" data-position="'+key+'"><i class="fa fa-close"></i>Remove</a>');
					r.push('</div>');
					r.push('</div>'); // head
					r.push('<div class="advancedoptions-tile-body" style="overflow: hidden;">');
					r.push('<div class="sub5" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;"><div>Usage inside content</div></div>');
					r.push('<p><b>Show the custom display inside content based on selection in Positions menu</b></p>');
					r.push('<div class="code"><code>[social-share-display display="'+key+'"]</code></div>');
					r.push('<p><b>Always show the display no matter on selection in Positions menu</b></p>');
					r.push('<div class="code"><code>[social-share-display display="'+key+'" force="true"]</code></div>');
					r.push('<p><b>Used on an archive template</b></p>');
					r.push('<div class="code"><code>[social-share-display display="'+key+'" archive="true" force="true|false"]</code></div>');
					r.push('<div class="sub5" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;"><div>Usage inside code</div></div>');
					r.push('<p><b>Show the custom display based on selection in Positions menu</b></p>');
					r.push('<div class="code"><code>&lt;?php if (function_exists("essb_custom_position_draw")) { essb_custom_position_draw("'+key+'"); }  ?&gt;</code></div>');
					r.push('<p><b>Always show the display no matter on selection in Positions menu</b></p>');
					r.push('<div class="code"><code>&lt;?php if (function_exists("essb_custom_position_draw")) { essb_custom_position_draw("'+key+'", true); }  ?&gt;</code></div>');
					r.push('<p><b>Used on an archive template</b></p>');
					r.push('<div class="code"><code>&lt;?php if (function_exists("essb_custom_position_draw")) { essb_custom_position_draw("'+key+'", true|false, true); }  ?&gt;</code></div>');
					r.push('</div>');
					r.push('</div>');
				}
			}

			document.querySelector('#ao-custom-positions').innerHTML = r.join('');

			$('.ao-remove-display-position').click(function(e) {
				e.preventDefault();

				var position = $(this).data('position') || '';
				if (!position || position == '') return;

				aoRemoveCustomPosition(position);
			});
		};

		ao_user_positions_draw();
	});

	</script>

	<?php
}

function essb5_additional_excerpt_options() {
	echo essb5_generate_code_advanced_settings_panel(
			esc_html__('Display on Post Excerpts', 'essb'),
			esc_html__('The options will work only if the theme uses excerpt display for the listing pages (blog, archive, category). If the theme does not use the excerpt display you can activate appearance on the listing pages from the post type menu.', 'essb'),
			'excerpts', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500');
	
}

function essb5_additional_deactivate_options() {
	echo essb5_generate_code_advanced_settings_panel(
			esc_html__('Advanced Plugin Deactivate', 'essb'),
			esc_html__('With advanced plugin deactivate of features you can stop particular components from running on selected pages. ', 'essb'),
			'advanced-deactivate', '', esc_html__('Configure', 'essb'), 'ti-settings', 'no', '500');
}

function essb5_advanced_mobile_activate_options() {	
	$value = essb_sanitize_option_value('functions_mode_mobile');
	
	// Advanced value is used as a trigger to the mobile settings only
	if ($value == 'advanced') {
		$value = '';
		global $essb_options;
		$essb_options['functions_mode_mobile'] = '';
	}
	
	$select_values = array(
			'' => array('title' => 'No specific mobile settings - using global and position rules', 'content' => '<i class="ti-panel"></i> <div class="content"><span class="title">No specific settings</span><span class="desc">Use the global or position responsive settings you have made</span></div>', 'isText'=>true),
			'auto' => array('title' => 'Plugin will automatically setup share buttons', 'content' => '<i class="ti-star"></i><div class="content"><span class="title">Automatic Setup</span><span class="desc">The plugin will automatically setup responsive share buttons for mobile</span></div>', 'isText'=>true),
			'deactivate' => array('title' => 'Deactivate mobile settings and do not show buttons on mobile devices', 'content' => '<i class="ti-close"></i><div class="content"><span class="title">Deactivate on Mobile</span><span class="desc">The plugin will not show buttons on mobile devices</span></div>', 'isText'=>true),
			'advanced' => array('title' => 'Advanced Custom Mobile Display & Positions ', 'content' => '<i class="ti-mobile"></i> <div class="content"><span class="title">Advanced Custom Mobile Display & Positions</span><span class="desc">Allows full control over share buttons on a mobile device (some of the features will require server-side mobile detection)</span></div>', 'isText'=>true),
		);
	
	essb_component_options_group_select('functions_mode_mobile', $select_values, '', $value);
	
	ESSBOptionsFramework::draw_holder_start(array('class' => 'functions_mode_mobile_auto', 'user_id' => 'functions_mode_mobile_auto'));
	ESSBOptionsFramework::draw_heading(esc_html__('Choose mobile position', 'essb'), '4');
	ESSBOptionsFramework::draw_single_position_select('functions_mode_mobile_auto', 'essb_options', array('values' => essb5_available_positions_mobile_only()));
	
	// Mobile breakpoint
	echo '<div class="essb-automobile-breakpoint">';
    ESSBOptionsFramework::draw_options_row_start(esc_html('Mobile breakpoint', 'essb'), esc_html('Sets the breakpoint for mobile devices. Below this breakpoint mobile layout will appear (Default: 768px). To make the mobile methods work for tablets enter 1024.', 'essb'));
	ESSBOptionsFramework::draw_input_field('functions_mode_mobile_auto_breakpoint', false, 'essb_options', essb_sanitize_option_value('functions_mode_mobile_auto_breakpoint'));
	ESSBOptionsFramework::draw_options_row_end();
	echo '</div>';
	
	ESSBOptionsFramework::draw_holder_end();
}

function essb5_advanced_other_features_where_activate() {
	$share_features = ESSBControlCenter::$features_group['display'];

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


function essb5_advanced_additional_sharebutton_positions() {
	echo essb5_generate_code_advanced_settings_panel(
			esc_html__('Manage Available Share Button Displays', 'essb'),
			esc_html__('Control the available for usage automatic display positions for share buttons. Currently, you see below only those that are active. You can add more from the existing or deactivate those you do not need.', 'essb'),
			'manage-positions', '', esc_html__('Manage', 'essb'), 'ti-layout', 'yes', '500', '', 'ti-layout', '', false,
			'https://docs.socialsharingplugin.com/knowledgebase/working-with-automatic-display-positions-for-social-share-buttons-and-plugin-integrations/#Manage_Available_Share_Button_Displays');
}