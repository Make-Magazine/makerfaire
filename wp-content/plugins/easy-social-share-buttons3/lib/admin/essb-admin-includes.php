<?php

include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/essb-admin-helpers.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-control-center-base.php');

include_once (ESSB3_PLUGIN_ROOT . 'lib/core/cache/essb-cache-detector.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/options/essb-options-structure-shared.php');
if (defined('ESSB3_SETTING5')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/options/essb-options-framework5.php');
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/options/essb-options-interface5.php');	
	include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-settings-components5.php');
}
else {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/options/essb-options-framework.php');
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/options/essb-options-interface.php');
}

// metabox builder
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/options/essb-matebox-options-framework.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/options/essb-metabox-interface5.php');


if (!essb_option_bool_value('deactivate_module_pinterestpro')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/pinterest-pro/pinterest-pro-admin.php');
}

if (!essb_option_bool_value('deactivate_module_pinterestpro') || !essb_option_bool_value('essb_deactivate_ctt')) {
    include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/class-essb-tinymce-loader.php');
}


include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-share-analytics/essb-social-share-analytics-backend.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-options-structure5.php');

include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/essb-metabox.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/essb-admin-activate.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/essb-admin.php');

include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/essb-global-wordpress-notifications.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/essb-trigger-notifications.php');

if (!class_exists('ESSBControlCenterShortcodes')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/settings/essb-control-center-shortcodes.php');
	ESSBControlCenterShortcodes::add_plugin_shortcodes();
	
	include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/essb-media-buttons.php');
}


?>