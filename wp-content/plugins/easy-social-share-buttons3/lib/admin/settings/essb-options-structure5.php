<?php
if (!class_exists('ESSBSocialFollowersCounterHelper')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-helper.php');
}

if (!class_exists('ESSBSocialProfilesHelper')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-profiles/essb-social-profiles-helper.php');
}

include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-admin-options-helper5.php');
include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-featuregroups.php');


$essb_navigation_tabs = array();
$essb_sidebar_sections = array();
$essb_sidebar_sections = array();
$essb_sidebar_description = array();
global $essb_sidebar_description;

$essb_options = essb_options();

if (class_exists('ESSBControlCenter')) {
    
    /**
     * @since 8.0 - New control center
     * ESSBControlCenter::set_new_version();
     */
    
    ESSBControlCenter::register_sidebar_block('sharing', 'ti-sharethis', 'Sharing', array('social_heading', 'social', 'where', 'othersharing'));
    ESSBControlCenter::register_sidebar_block('following', 'ti-heart', 'Follow', array('follow_heading', 'follow', 'profiles', 'natives', 'instagram', 'proof-notifications', 'othersocial'));
    ESSBControlCenter::register_sidebar_block('subscribe', 'ti-email', 'Subscribe', array('subscribe'));
    ESSBControlCenter::register_sidebar_block('chat', 'ti-comments', 'Chat & Contact', array('chat'));
    ESSBControlCenter::register_sidebar_block('advanced', 'ti-settings', 'Advanced', array('additional_heading', 'advanced', 'translate', 'shortcode'));
    ESSBControlCenter::register_sidebar_block('styles', 'ti-palette', 'Styles', array('style', 'readymade'));
    ESSBControlCenter::register_sidebar_block('import', 'ti-reload', 'Import, Export, Reset', array('import'));
    ESSBControlCenter::register_sidebar_block('analytics', 'ti-stats-up', 'Analytics', array('analytics_heading', 'analytics', 'conversions'));
    ESSBControlCenter::register_sidebar_block('developer', 'ti-server', 'Analytics', array('developer'));
    ESSBControlCenter::register_sidebar_block('about', 'ti-info-alt', 'About, Activate, Extensions', array('about', 'update', 'extensions'));
    ESSBControlCenter::register_sidebar_block('extensions', 'ti-package', 'Extensions', array());
    ESSBControlCenter::set_global_block('extensions');
    
    
	ESSBControlCenter::register_sidebar_heading('social_heading', esc_html__('Social Sharing', 'essb'));
	ESSBControlCenter::register_sidebar_section('social', esc_html__('Share Buttons', 'essb'), '', 'ti-sharethis');
	ESSBControlCenter::register_sidebar_section('where', esc_html__('Where to Display', 'essb'), '', 'ti-layout');
	
	if (ESSBControlCenter::feature_group_has_deactivated('share') || ESSBControlCenter::feature_group_has_deactivated('display')) {
		ESSBControlCenter::register_sidebar_section('othersharing', esc_html__('Additional Features', 'essb'), '', 'ti-plug', false, false, false, false, true);		
	}
	
	if (!essb_option_bool_value('deactivate_module_natives') ||
			!essb_option_bool_value('deactivate_module_profiles') ||
			!essb_option_bool_value('deactivate_module_followers') ||
			!essb_option_bool_value('deactivate_module_facebookchat') ||
			!essb_option_bool_value('deactivate_module_skypechat') ||
			!essb_option_bool_value('deactivate_module_subscribe') ||
			!essb_option_bool_value('deactivate_module_instagram')) {
		
		ESSBControlCenter::register_sidebar_heading('follow_heading', esc_html__('Follow, Chat & Subscribe', 'essb'));
		
		if (!essb_option_bool_value('deactivate_module_followers')) {
			ESSBControlCenter::register_sidebar_section('follow', esc_html__('Followers Counter', 'essb'), '', 'ti-heart');				
		}
		
		if (!essb_option_bool_value('deactivate_module_profiles')) {
			ESSBControlCenter::register_sidebar_section('profiles', esc_html__('Profile Links', 'essb'), '', 'ti-share');
		}
		
		if (!essb_option_bool_value('deactivate_module_natives')) {
			ESSBControlCenter::register_sidebar_section('natives', esc_html__('Native Social Buttons', 'essb'), '', 'ti-thumb-up');
		}
		
		if (!essb_option_bool_value('deactivate_module_facebookchat') || !essb_option_bool_value('deactivate_module_skypechat') || !essb_option_bool_value('deactivate_module_clicktochat')) {
			ESSBControlCenter::register_sidebar_section('chat', esc_html__('Social Chat', 'essb'), '', 'ti-comments');				
		}
		
		if (!essb_option_bool_value('deactivate_module_subscribe')) {
			ESSBControlCenter::register_sidebar_section('subscribe', esc_html__('Subscribe Forms', 'essb'), '', 'ti-email');
		}

		if (!essb_option_bool_value('deactivate_module_instagram')) {
			ESSBControlCenter::register_sidebar_section('instagram', esc_html__('Instagram Feed', 'essb'), '', 'ti-instagram');
		}
		
		if (!essb_option_bool_value('deactivate_module_proofnotifications')) {
			ESSBControlCenter::register_sidebar_section('proof-notifications', esc_html__('Social Proof Notifications Lite', 'essb'), '', 'ti-comment-alt');
		}
		
		if (ESSBControlCenter::feature_group_has_deactivated('other-social')) {
			ESSBControlCenter::register_sidebar_section('othersocial', esc_html__('Additional Features', 'essb'), '', 'ti-plug');
			ESSBControlCenter::register_sidebar_section_menu('othersocial', 'othersocial', esc_html__('Additional Features', 'essb'));
			ESSBOptionsStructureHelper::field_component('othersocial', 'othersocial', 'essb5_advanced_other_features_global_social_activate', 'false');
		}				
	}
	else {
		if (ESSBControlCenter::feature_group_has_deactivated('other-social')) {
			ESSBControlCenter::register_sidebar_heading('follow_heading', esc_html__('Follow, Chat & Subscribe', 'essb'));
			ESSBControlCenter::register_sidebar_section('othersocial', esc_html__('Additional Features', 'essb'), '', 'ti-plug');
			ESSBControlCenter::register_sidebar_section_menu('othersocial', 'othersocial', esc_html__('Additional Features', 'essb'));
			ESSBOptionsStructureHelper::field_component('othersocial', 'othersocial', 'essb5_advanced_other_features_global_social_activate', 'false');
		}		
	}
	
	ESSBControlCenter::register_sidebar_heading('additional_heading', esc_html__('Additional Settings', 'essb'));
	ESSBControlCenter::register_sidebar_section('advanced', esc_html__('Advanced', 'essb'), '', 'ti-settings');

	ESSBControlCenter::register_sidebar_section('style', esc_html__('Style Settings', 'essb'), '', 'ti-palette');
	
	ESSBControlCenter::register_sidebar_section('import', esc_html__('Import / Export / Reset', 'essb'), '', 'ti-reload', true);
	ESSBControlCenter::register_sidebar_section('shortcode', esc_html__('Shortcode Generator', 'essb'), '', 'ti-shortcode', true, false, false, false, true);
	
	if (essb_option_bool_value('activate_hooks') || essb_option_bool_value('activate_fake') || essb_option_bool_value('activate_minimal')) {
		ESSBControlCenter::register_sidebar_section('developer', esc_html__('Developer Tools', 'essb'), '', 'ti-server');
	}
	
	if (essb_option_bool_value('stats_active') || essb_option_bool_value('conversions_lite_run') || essb_options_bool_value('conversions_subscribe_lite_run')) {
		ESSBControlCenter::register_sidebar_heading('analytics_heading', esc_html__('Analytics', 'essb'));
		
		if (essb_option_bool_value('stats_active') && !essb_option_bool_value('deactivate_module_analytics')) {
			ESSBControlCenter::register_sidebar_section('analytics', esc_html__('Analytics Reports', 'essb'), '', 'ti-stats-up', true);
		}
		
		if (essb_option_bool_value('conversions_lite_run') || essb_options_bool_value('conversions_subscribe_lite_run')) {
			ESSBControlCenter::register_sidebar_section('conversions', esc_html__('Conversions Report', 'essb'), '', 'ti-dashboard', true);
		}
	}
	
	ESSBControlCenter::register_sidebar_heading('advanced_split', '');
	
	if (has_filter('essb_unset_activation_page')) {
	    $result = false;
	    $result = apply_filters('essb_unset_activation_page', $result);
	    
	    if (!$result) {
	        ESSBControlCenter::register_sidebar_section('update', esc_html__('Activate', 'essb'), '', 'ti-lock', false, false, false, false, true);
	    }
	}
	else {
	   ESSBControlCenter::register_sidebar_section('update', esc_html__('Activate', 'essb'), '', 'ti-lock', false, false, false, false, true);
	}
	
	if (essb_option_value('functions_mode') != 'light') {
		if (!essb_option_bool_value('deactivate_stylelibrary')) {
			ESSBControlCenter::register_sidebar_section('readymade', esc_html__('Style Library', 'essb'), '', 'ti-brush', true, false, false, false, true);
		}
		ESSBControlCenter::register_sidebar_section('extensions', esc_html__('Extensions', 'essb'), '', 'ti-package', true, false, false, true, false);
	}
	
	if (essb_installed_wpml() || essb_installed_polylang()) {
		ESSBControlCenter::register_sidebar_section('translate', esc_html__('Multilingual Translate', 'essb'), '', 'fa fa-globe');
	}
	
	ESSBControlCenter::register_sidebar_section('about', esc_html__('About', 'essb'), '', 'ti-info-alt', true);
	
	ESSBControlCenter::deprecate_blocks_in_new_version();
}


ESSBOptionsStructureHelper::init();
ESSBOptionsStructureHelper::tab('social', esc_html__('Social Sharing', 'essb'), esc_html__('Social Sharing', 'essb'), 'ti-sharethis');
ESSBOptionsStructureHelper::tab('where', esc_html__('Where to Display', 'essb'), esc_html__('Where to Display', 'essb'), 'ti-layout');

$essb_sidebar_description['social'] = esc_html__('Setup share buttons on site', 'essb');
$essb_sidebar_description['where'] = esc_html__('Positions, mobile, integrations', 'essb');

if (!essb_option_bool_value('deactivate_module_natives') ||
		!essb_option_bool_value('deactivate_module_profiles') ||
		!essb_option_bool_value('deactivate_module_followers') ||
		!essb_option_bool_value('deactivate_module_facebookchat') ||
		!essb_option_bool_value('deactivate_module_skypechat')) {
	ESSBOptionsStructureHelper::tab('display', esc_html__('Social Follow & Chat', 'essb'), esc_html__('Social Follow & Chat', 'essb'), 'ti-heart');
	$essb_sidebar_description['display'] = esc_html__('Increase social followers', 'essb');

}

if (!essb_option_bool_value('deactivate_module_subscribe')) {
	ESSBOptionsStructureHelper::tab('optin', esc_html__('Subscribe Forms', 'essb'), esc_html__('Subscribe Forms', 'essb'), 'ti-email');
	$essb_sidebar_description['optin'] = esc_html__('Add subscribe to mailing list forms', 'essb');
}
ESSBOptionsStructureHelper::tab('advanced', esc_html__('Advanced Settings', 'essb'), esc_html__('Advanced Settings', 'essb'), 'ti-settings');
$essb_sidebar_description['advanced'] = esc_html__('Optimization and advanced settings', 'essb');
ESSBOptionsStructureHelper::tab('style', esc_html__('Style Settings', 'essb'), esc_html__('Style Settings', 'essb'), 'ti-palette');
$essb_sidebar_description['style'] = esc_html__('Customizer colors, custom CSS', 'essb');
ESSBOptionsStructureHelper::tab('shortcode', esc_html__('Shortcode Generator', 'essb'), esc_html__('Shortcode Generator', 'essb'), 'ti-shortcode', '', true);
$essb_sidebar_description['shortcode'] = esc_html__('Generate custom shortcodes', 'essb');
if (essb_option_bool_value('stats_active')) {
	ESSBOptionsStructureHelper::tab('analytics', esc_html__('Analytics', 'essb'), esc_html__('Analytics', 'essb'), 'ti-stats-up', '', true);
	$essb_sidebar_description['analytics'] = esc_html__('View stored analytics data', 'essb');
}

if (!essb_option_bool_value('deactivate_module_conversions')) {
	if (essb_option_bool_value('conversions_lite_run') || essb_options_bool_value('conversions_subscribe_lite_run')) {
		ESSBOptionsStructureHelper::tab('conversions', esc_html__('Conversions Lite', 'essb'), esc_html__('Conversions Lite', 'essb'), 'ti-dashboard', '');
		$essb_sidebar_description['conversions'] = esc_html__('View and activate conversions', 'essb');
	}

}

if (essb_option_bool_value('activate_hooks') || essb_option_bool_value('activate_fake') || essb_option_bool_value('activate_minimal')) {
	ESSBOptionsStructureHelper::tab('developer', esc_html__('Developer Tools', 'essb'), esc_html__('Developer Tools', 'essb'), 'ti-server');
	$essb_sidebar_description['developer'] = esc_html__('Custom integrations, fake counters', 'essb');

}


ESSBOptionsStructureHelper::tab('import', esc_html__('Import / Export', 'essb'), esc_html__('Import / Export Plugin Configuration', 'essb'), 'ti-reload', 'right', true);
$essb_sidebar_description['import'] = esc_html__('Import, export or rollback settings', 'essb');


ESSBOptionsStructureHelper::tab('update', esc_html__('Activate', 'essb'), esc_html__('Activate Easy Social Share Buttons for WordPress', 'essb'), 'ti-lock', 'right', true, false, false, true);
$essb_sidebar_description['update'] = esc_html__('Activate premium benefits', 'essb');


ESSBOptionsStructureHelper::tab('quick', esc_html__('Quick Setup', 'essb'), esc_html__('Quick Setup Wizard', 'essb'), 'fa fa-cog', '', false, true, false, true);
$essb_sidebar_description['quick'] = esc_html__('Fast and easy setup common options', 'essb');

if (essb_option_value('functions_mode') != 'light') {
	ESSBOptionsStructureHelper::tab('readymade', esc_html__('Styles Library', 'essb'), esc_html__('Apply Preconfigured Styles', 'essb'), 'ti-brush', '', false, false, false, true);
	$essb_sidebar_description['readymade'] = esc_html__('Apply design to selected position', 'essb');
}

ESSBOptionsStructureHelper::tab('status', esc_html__('System Status', 'essb'), esc_html__('System Status', 'essb'), 'ti-receipt', '', true, true, true, true);
$essb_sidebar_description['status'] = esc_html__('System configuration, tests', 'essb');

if (essb_option_value('functions_mode') != 'light') {
	ESSBOptionsStructureHelper::tab('extensions', esc_html__('Extensions', 'essb'), esc_html__('Extensions', 'essb'), 'ti-package', '', true, false, true);
	$essb_sidebar_description['extensions'] = esc_html__('Download & install extensions', 'essb');
}

if (essb_installed_wpml() || essb_installed_polylang()) {
	ESSBOptionsStructureHelper::tab('translate', esc_html__('Multilingual Translate', 'essb'), esc_html__('Multilingual Translate', 'essb'), 'fa fa-globe', '', !ESSBActivationManager::isActivated(), false, false, false);
	$essb_sidebar_description['translate'] = esc_html__('Setup multilnagual values for selected options', 'essb');
}

ESSBOptionsStructureHelper::tab('about', esc_html__('About', 'essb'), esc_html__('About', 'essb'), 'ti-info-alt', '', true, false, true);
$essb_sidebar_description['about'] = esc_html__('Get help, version info', 'essb');

ESSBOptionsStructureHelper::tab('modes', esc_html__('Switch Plugin Modes', 'essb'), esc_html__('Switch Plugin Modes', 'essb'), 'ti-info-alt', '', false, true, false, true);
ESSBOptionsStructureHelper::tab('functions', esc_html__('Manage Plugin Functions', 'essb'), esc_html__('Manage Plugin Functions', 'essb'), 'ti-info-alt', '', false, true, false, true);

ESSBOptionsStructureHelper::tab('test', esc_html__('Test Playground', 'essb'), esc_html__('Test Playground', 'essb'), 'ti-receipt', '', true, true, true, true);
ESSBOptionsStructureHelper::tab('test2', esc_html__('Test Playground', 'essb'), esc_html__('Test Playground', 'essb'), 'ti-receipt', '', true, true, true, true);


//-- menu
$user_active_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';

$active_settings_page = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : '';
if (strpos ( $active_settings_page, 'essb_redirect_' ) !== false) {
	$options_page = str_replace ( 'essb_redirect_', '', $active_settings_page );
	if ($options_page != '') {
		$user_active_tab = $options_page;
	}
}

if ($user_active_tab == "readymade") {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/admin-options/essb-options-structure-readymade.php');
}


// version 5 options structure
include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-sharing.php');
include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-where.php');
include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-follow.php');
include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-subscribe.php');
include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-advanced.php');
include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-style.php');
include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-import.php');
include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-instagram.php');
include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-proof-notifications.php');

if (essb_option_bool_value('activate_hooks') || essb_option_bool_value('activate_fake') || essb_option_bool_value('activate_minimal')) {
	include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-developer.php');
}

if ($user_active_tab == "translate") {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-admin-options-wpml.php');
}

function essb5_advanced_other_features_global_social_activate() {
	$share_features = ESSBControlCenter::$features_group['other-social'];

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