<?php

// Post Meta Class
if (!class_exists('ESSB_Post_Meta')) {
    include_once (ESSB3_PLUGIN_ROOT . 'lib/classes/class-post-meta.php');
}

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
    // option added in version 8.5
    if (!essb_option_bool_value('classic_editor_disable_plugins')) {
        include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/class-essb-tinymce-loader.php');
    }
}

/**
 * Loading subscribe conversions class
 */
if (essb_option_bool_value('conversions_subscribe_lite_run') && !essb_options_bool_value('deactivate_module_conversions')) {
    if (!class_exists('ESSB_Subscribe_Conversions_Pro')) {
        include_once (ESSB3_MODULES_PATH . 'conversions-pro/class-subscribe-conversions.php');
    }
}

if (essb_option_bool_value('conversions_lite_run') && !essb_options_bool_value('deactivate_module_conversions')) {
    if (!class_exists('ESSB_Share_Conversions_Pro')) {
        include_once (ESSB3_MODULES_PATH . 'conversions-pro/class-share-conversions.php');
    }
}

if (!essb_option_bool_value('deactivate_module_shorturl') && essb_option_bool_value('shorturl_activate')) {
    if (!class_exists('ESSB_Short_URL')) {
        include_once (ESSB3_CLASS_PATH . 'share-information/class-short-url.php');
    }
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

/**
 * Register the add-ons installer
 */

include_once (ESSB3_CLASS_PATH . 'utilities/class-essb-tgm-plugin-activation.php');
if (!class_exists ( 'ESSBAddonsHelper' )) {
    include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/addons/essb-addons-helper4.php');
}

add_action( 'essb_tgmpa_register', 'essb_map_addons_to_tmga' );


?>