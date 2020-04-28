<?php

/**
 * Generate the plugin default settings that will be loaded. The function is used when
 * plugin is activated for the very first time or each time the reset settings buttons is
 * clicked
 *
 * @since 5.9.3
 * @package EasySocialShareButtons
 * @author appscreo
 */

if (!function_exists('essb_generate_default_settings')) {
	function essb_generate_default_settings() {
		$options = '{"afterclose_type":"follow","afterclose_like_cols":"onecol","aftershare_optin_design":"design1","networks":["facebook","twitter","google","pinterest","linkedin"],"more_button_func":"1","share_button_func":"1","share_button_counter":"hidden","twitter_message_optimize_method":"1","subscribe_function":"form","subscribe_optin_design":"design1","subscribe_optin_design_popup":"design1","mail_function":"form","mail_function_security":"level1","flattr_lang":"sq_AL","style":"59","button_style":"button","counter_pos":"hidden","total_counter_pos":"hidden","fullwidth_align":"left","fullwidth_share_buttons_columns":"1","counter_mode":"360","counter_recover_mode":"unchanged","counter_recover_protocol":"unchanged","counter_recover_prefixdomain":"unchanged","twitter_counters":"self","force_counters_admin_type":"wp","esml_history":"1","esml_access":"manage_options","ga_tracking_mode":"simple","pinterest_template":"32","pinterest_button_style":"button","pinterest_position":"top-left","pinsc_template":"32","pinsc_button_style":"button","pinsc_position":"top-left","opengraph_tags":"true","sso_imagesize":"true","twitter_card_type":"summary","twitter_shareshort":"true","shorturl_type":"wp","shorturl_bitlyapi_version":"previous","affwp_active_mode":"id","user_network_name_facebook":"Facebook","user_network_name_twitter":"Twitter","user_network_name_google":"Google+","user_network_name_pinterest":"Pinterest","user_network_name_linkedin":"LinkedIn","topbar_contentarea_pos":"left","bottombar_contentarea_pos":"left","flyin_position":"right","sis_network_order":["facebook","twitter","google","linkedin","pinterest","tumblr","reddit","digg","delicious","vkontakte","odnoklassniki"],"sis_position":"top-left","sis_style":"tiny","sis_orientation":"horizontal","heroshare_second_type":"top","postbar_button_style":"recommended","postbar_counter_pos":"hidden","point_position":"bottomright","point_open_auto":"no","point_style":"simple","point_shape":"round","point_button_style":"recommended","point_template":"6","point_counter_pos":"inside","mobile_sharebuttonsbar_count":"1","sharebar_counter_pos":"inside","sharebar_total_counter_pos":"before","sharepoint_counter_pos":"inside","sharepoint_total_counter_pos":"before","display_in_types":["post"],"display_excerpt_pos":"top","content_position":"content_both","subscribe_connector":"mailchimp","subscribe_css_always":"true","use_minified_css":"true","use_minified_js":"true","essb_cache_mode":"full","apply_clean_buttons_method":"default","essb_access":"manage_options","turnoff_essb_advanced_box":"true"}';

		$options = htmlspecialchars_decode ( $options );
		$options = stripslashes ( $options );

		return json_decode ( $options, true );
	}
}
