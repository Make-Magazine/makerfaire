<?php
if (!class_exists('ESSBSocialFollowersCounterHelper')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-helper.php');
}

if (!class_exists('ESSBSocialProfilesHelper')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-profiles/essb-social-profiles-helper.php');
}

include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-admin-options-helper5.php');


$essb_navigation_tabs = array();
$essb_sidebar_sections = array();
$essb_sidebar_sections = array();
$essb_sidebar_description = array();
global $essb_sidebar_description;

$essb_options = essb_options();

ESSBOptionsStructureHelper::init();
ESSBOptionsStructureHelper::tab('social', __('Social Sharing', 'essb'), __('Social Sharing', 'essb'), 'ti-sharethis');
ESSBOptionsStructureHelper::tab('where', __('Where to Display', 'essb'), __('Where to Display', 'essb'), 'ti-layout');

$essb_sidebar_description['social'] = __('Setup share buttons on site', 'essb');
$essb_sidebar_description['where'] = __('Positions, mobile, integrations', 'essb');

if (!essb_option_bool_value('deactivate_module_natives') ||
		!essb_option_bool_value('deactivate_module_profiles') ||
		!essb_option_bool_value('deactivate_module_followers') ||
		!essb_option_bool_value('deactivate_module_facebookchat') ||
		!essb_option_bool_value('deactivate_module_skypechat')) {
	ESSBOptionsStructureHelper::tab('display', __('Social Follow & Chat', 'essb'), __('Social Follow & Chat', 'essb'), 'ti-heart');
	$essb_sidebar_description['display'] = __('Increase social followers', 'essb');

}

if (!essb_option_bool_value('deactivate_module_subscribe')) {
	ESSBOptionsStructureHelper::tab('optin', __('Subscribe Forms', 'essb'), __('Subscribe Forms', 'essb'), 'ti-email');
	$essb_sidebar_description['optin'] = __('Add subscribe to mailing list forms', 'essb');
}
ESSBOptionsStructureHelper::tab('advanced', __('Advanced Settings', 'essb'), __('Advanced Settings', 'essb'), 'ti-settings');
$essb_sidebar_description['advanced'] = __('Optimization and advanced settings', 'essb');
ESSBOptionsStructureHelper::tab('style', __('Style Settings', 'essb'), __('Style Settings', 'essb'), 'ti-palette');
$essb_sidebar_description['style'] = __('Customizer colors, custom CSS', 'essb');
ESSBOptionsStructureHelper::tab('shortcode', __('Shortcode Generator', 'essb'), __('Shortcode Generator', 'essb'), 'ti-shortcode', '', true);
$essb_sidebar_description['shortcode'] = __('Generate custom shortcodes', 'essb');
if (essb_option_bool_value('stats_active')) {
	ESSBOptionsStructureHelper::tab('analytics', __('Analytics', 'essb'), __('Analytics', 'essb'), 'ti-stats-up', '', true);
	$essb_sidebar_description['analytics'] = __('View stored analytics data', 'essb');
}

if (!essb_option_bool_value('deactivate_module_conversions')) {
	if (essb_option_bool_value('conversions_lite_run') || essb_options_bool_value('conversions_subscribe_lite_run')) {
		ESSBOptionsStructureHelper::tab('conversions', __('Conversions Lite', 'essb'), __('Conversions Lite', 'essb'), 'ti-dashboard', '');
		$essb_sidebar_description['conversions'] = __('View and activate conversions', 'essb');
	}

}

if (essb_option_bool_value('activate_hooks') || essb_option_bool_value('activate_fake') || essb_option_bool_value('activate_minimal')) {
	ESSBOptionsStructureHelper::tab('developer', __('Developer Tools', 'essb'), __('Developer Tools', 'essb'), 'ti-server');
	$essb_sidebar_description['developer'] = __('Custom integrations, fake counters', 'essb');

}


ESSBOptionsStructureHelper::tab('import', __('Import / Export', 'essb'), __('Import / Export Plugin Configuration', 'essb'), 'ti-reload', 'right', true);
$essb_sidebar_description['import'] = __('Import, export or rollback settings', 'essb');


ESSBOptionsStructureHelper::tab('update', __('Activate', 'essb'), __('Activate Easy Social Share Buttons for WordPress', 'essb'), 'ti-lock', 'right', true, false, false, true);
$essb_sidebar_description['update'] = __('Activate premium benefits', 'essb');


ESSBOptionsStructureHelper::tab('quick', __('Quick Setup', 'essb'), __('Quick Setup Wizard', 'essb'), 'fa fa-cog', '', false, true, false, true);
$essb_sidebar_description['quick'] = __('Fast and easy setup common options', 'essb');

if (essb_option_value('functions_mode') != 'light') {
	ESSBOptionsStructureHelper::tab('readymade', __('Styles Library', 'essb'), __('Apply Preconfigured Styles', 'essb'), 'ti-brush', '', false, false, false, true);
	$essb_sidebar_description['readymade'] = __('Apply design to selected position', 'essb');
}

ESSBOptionsStructureHelper::tab('status', __('System Status', 'essb'), __('System Status', 'essb'), 'ti-receipt', '', true, true, true, true);
$essb_sidebar_description['status'] = __('System configuration, tests', 'essb');

if (essb_option_value('functions_mode') != 'light') {
	ESSBOptionsStructureHelper::tab('extensions', __('Extensions', 'essb'), __('Extensions', 'essb'), 'ti-package', '', true, false, true);
	$essb_sidebar_description['extensions'] = __('Download & install extensions', 'essb');
}

if (essb_installed_wpml() || essb_installed_polylang()) {
	ESSBOptionsStructureHelper::tab('translate', __('Multilingual Translate', 'essb'), __('Multilingual Translate', 'essb'), 'fa fa-globe', '', !ESSBActivationManager::isActivated(), false, false, false);
	$essb_sidebar_description['translate'] = __('Setup multilnagual values for selected options', 'essb');
}

ESSBOptionsStructureHelper::tab('about', __('About', 'essb'), __('About', 'essb'), 'ti-info-alt', '', true, false, true);
$essb_sidebar_description['about'] = __('Get help, version info', 'essb');

ESSBOptionsStructureHelper::tab('modes', __('Switch Plugin Modes', 'essb'), __('Switch Plugin Modes', 'essb'), 'ti-info-alt', '', false, true, false, true);
ESSBOptionsStructureHelper::tab('functions', __('Manage Plugin Functions', 'essb'), __('Manage Plugin Functions', 'essb'), 'ti-info-alt', '', false, true, false, true);

//-- menu
$user_active_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';

$active_settings_page = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : '';
if (strpos ( $active_settings_page, 'essb_redirect_' ) !== false) {
	$options_page = str_replace ( 'essb_redirect_', '', $active_settings_page );
	if ($options_page != '') {
		$user_active_tab = $options_page;
	}
}

if ($user_active_tab == "quick") {
	include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-wizard.php');
}
if ($user_active_tab == "readymade") {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/admin-options/essb-options-structure-readymade.php');
}


// version 5 options structure
include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-functions.php');
include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-sharing.php');
include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-where.php');
include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-follow.php');
include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-subscribe.php');
include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-advanced.php');
include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-style.php');
include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-import.php');
if (essb_option_bool_value('activate_hooks') || essb_option_bool_value('activate_fake') || essb_option_bool_value('activate_minimal')) {
	include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-structure5-developer.php');
}

if ($user_active_tab == "translate") {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-admin-options-wpml.php');
}
