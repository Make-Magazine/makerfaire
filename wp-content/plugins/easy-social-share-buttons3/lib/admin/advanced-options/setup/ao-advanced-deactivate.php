<?php 

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb5_draw_panel_start(__('Deactivate Plugin Functions on Specific Places', 'essb'), __('Deactivate functions of plugin on selected posts/pages only', 'essb'), 'fa21 fa fa-cogs', array("mode" => "toggle", 'state' => 'closed1', "css_class" => "essb-auto-open"));
essb5_draw_input_option('deactivate_on_share', __('Social Share Buttons', 'essb'), __('Deactivate function on posts/pages with these IDs? Comma separated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts related to this function', 'essb'), true);
essb5_draw_input_option('deactivate_on_share_cats', __('Social Share Buttons on Posts from Categories', 'essb'), __('Deactivate function on selected categories. Comma separated category IDs: 11, 15, 125', 'essb'), true);
essb5_draw_input_option('deactivate_on_native', __('Native Buttons', 'essb'), __('Deactivate function on posts/pages with these IDs? Comma separated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts related to this function', 'essb'), true);
essb5_draw_input_option('deactivate_on_fanscounter', __('Social Following (Followers Counter)', 'essb'), __('Deactivate function on posts/pages with these IDs? Comma separated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts related to this function', 'essb'), true);
essb5_draw_input_option('deactivate_on_ctt', __('Sharable Quotes (Click To Tweet)', 'essb'), __('Deactivate function on posts/pages with these IDs? Comma separated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts related to this function', 'essb'), true);
essb5_draw_input_option('deactivate_on_sis', __('On Media Sharing (Social Image Share)', 'essb'), __('Deactivate function on posts/pages with these IDs? Comma separated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts related to this function', 'essb'), true);
essb5_draw_input_option('deactivate_on_profiles', __('Social Profiles', 'essb'), __('Deactivate function on posts/pages with these IDs? Comma separated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts related to this function', 'essb'), true);
essb5_draw_input_option('deactivate_on_sso', __('Social Share Optimization Meta Tags', 'essb'), __('Deactivate function on posts/pages with these IDs? Comma separated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts related to this function', 'essb'), true);
essb5_draw_input_option('deactivate_on_aftershare', __('After Share Actions', 'essb'), __('Deactivate function on posts/pages with these IDs? Comma separated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts related to this function', 'essb'), true);
essb5_draw_input_option('deactivate_on_mobile', __('Mobile Display of Share Buttons', 'essb'), __('Deactivate function on posts/pages with these IDs? Comma separated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts related to this function', 'essb'), true);
essb5_draw_panel_end();
