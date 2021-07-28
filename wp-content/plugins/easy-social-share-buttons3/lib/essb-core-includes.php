<?php
/**
 * Loading the default plugin components for running 
 */

global $essb_options, $essb_networks, $essb_translate_options;
$essb_options = get_option(ESSB3_OPTIONS_NAME);
$essb_networks = essb_available_social_networks();

ESSB_Plugin_Options::trigger_options_filter('essb4_options_extender');


include_once (ESSB3_CLASS_PATH . 'class-activation-manager.php');

include_once (ESSB3_HELPERS_PATH . 'helpers-tools.php');
include_once (ESSB3_HELPERS_PATH . 'helpers-url.php');

// include options helper functions
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/essb-global-settings.php');


// @since 4.0 - activation of widget and shortcodes require to activate widget display method
if (essb_is_active_feature('sharingwidget')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/widgets/essb-share-widget.php');
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/widgets/essb-popular-posts-widget-shortcode.php');
}

if (essb_option_bool_value('subscribe_widget')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/widgets/essb-share-subscribe-widget.php');
}

if (essb_option_bool_value('instagram_widget') && !essb_option_bool_value('deactivate_instagram_feed')) {
    include_once (ESSB3_MODULES_PATH . 'instagram-feed/class-instagram-feed-widget.php');
}

// initialize global plugin settings from version 3.4.1
ESSBGlobalSettings::load($essb_options);

/**
 * Register top bar helper navigation only if enabled
 */
if (!essb_option_bool_value('disable_adminbar_menu')) {
    add_action( 'init', function() {
        if (is_admin_bar_showing()) {
            include_once ESSB3_CLASS_PATH . 'class-admin-topbar-menu.php';
            ESSB_Factory_Loader::activate('admin-topbar', 'ESSB_Admin_Topbar_Menu');
        }
    });
}


if (essb_option_bool_value('essb_cache')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/cache/essb-dynamic-cache.php');
	$cache_mode = essb_sanitize_option_value('essb_cache_mode');
	ESSBDynamicCache::activate($cache_mode);
}

if (essb_options_bool_value('precompiled_resources')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/cache/essb-precompiled.php');
	ESSBPrecompiledResources::activate();
}


if (essb_options_bool_value('essb_cache_static') || essb_options_bool_value('essb_cache_static_js')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/cache/essb-static-cache.php');
}


// dynamic resource builder
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/essb-resource-builder.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/essb-resource-builder-core.php');

if (!defined('ESSB3_LIGHTMODE')) {
	if (essb_options_bool_value('native_active') && !essb_option_bool_value('deactivate_module_natives')) {
		include_once (ESSB3_PLUGIN_ROOT . 'lib/core/native-buttons/essb-skinned-native-button.php');
		include_once (ESSB3_PLUGIN_ROOT . 'lib/core/native-buttons/essb-social-privacy.php');
		include_once (ESSB3_PLUGIN_ROOT . 'lib/core/native-buttons/essb-native-buttons-helper.php');
		define('ESSB3_NATIVE_ACTIVE', true);
	}
}

/**
 * Running the social share optimization tags
 */
if (! essb_options_bool_value('deactivate_module_shareoptimize')) {
    if (essb_options_bool_value('opengraph_tags') || essb_options_bool_value('twitter_card')) {
        include_once (ESSB3_MODULES_PATH . 'social-share-optimization/class-metadetails.php');
        include_once (ESSB3_MODULES_PATH . 'social-share-optimization/class-taxonomy.php');
        
        if (essb_options_bool_value('opengraph_tags')) {
            include_once (ESSB3_MODULES_PATH . 'social-share-optimization/class-opengraph.php');
            
            if (class_exists('WooCommerce', false) && ! essb_option_bool_value('sso_deactivate_woocommerce')) {
                include_once (ESSB3_MODULES_PATH . 'social-share-optimization/class-woocommerce.php');
            }
        }
        if (essb_options_bool_value('twitter_card')) {
            include_once (ESSB3_MODULES_PATH . 'social-share-optimization/class-twittercards.php');
        }
        
        if (! essb_options_bool_value('deactivate_module_shareoptimize')) {
            ESSB_Runtime_Cache::set('sso-running', true);
        }
    }
}

if (essb_options_bool_value('stats_active') && !essb_options_bool_value('deactivate_module_analytics')) {
    include_once (ESSB3_MODULES_PATH . 'social-share-analytics/essb-social-share-analytics.php');
    ESSB_Runtime_Cache::set('stats-running', true);
}

if (essb_options_bool_value('mycred_activate') && !essb_options_bool_value('deactivate_module_affiliate')) {
    include_once (ESSB3_MODULES_PATH . 'mycred/essb-mycred-integration.php');
	define('ESSB3_MYCRED_ACTIVE', true);
	ESSBMyCredIntegration::get_instance();
}

if (essb_options_bool_value('mycred_activate_custom') && !essb_options_bool_value('deactivate_module_affiliate')) {
    include_once (ESSB3_MODULES_PATH . 'mycred/essb-mycred-custom-hook.php');
	define('ESSB3_MYCRED_CUSTOM_ACTIVE', true);
}

if (essb_options_bool_value('afterclose_active') && !essb_options_bool_value('deactivate_module_aftershare')) {
    include_once (ESSB3_MODULES_PATH . 'after-share-close/essb-after-share-close.php');
	ESSB_Runtime_Cache::set('after-share-running', true);
}
else{
	if (ESSB3_DEMO_MODE) {
		$is_active_option = isset($_REQUEST['aftershare']) ? $_REQUEST['aftershare'] : '';
		if ($is_active_option != '') {
		    include_once (ESSB3_MODULES_PATH . 'after-share-close/essb-after-share-close.php');
			ESSB_Runtime_Cache::set('after-share-running', true);

		}
	}
}

if (essb_is_active_feature('imageshare')) {
    include_once (ESSB3_MODULES_PATH . 'social-image-share/essb-social-image-share.php');
    ESSB_Runtime_Cache::set('onmedia-running', true);
}

if (!defined('ESSB3_LIGHTMODE') && !essb_options_bool_value('deactivate_module_profiles')) {
	if (essb_options_bool_value('profiles_display') || essb_options_bool_value('profiles_post_display')) {
	    include_once (ESSB3_MODULES_PATH . 'social-profiles/essb-social-profiles.php');
	    include_once (ESSB3_MODULES_PATH . 'social-profiles/essb-social-profiles-helper.php');
		define('ESSB3_SOCIALPROFILES_ACTIVE', 'true');
	}
	// Social Profiles Widget is always available
	if (essb_option_bool_value('profiles_widget')) {
	    include_once (ESSB3_MODULES_PATH . 'social-profiles/essb-social-profiles-widget.php');
	}
}

if (essb_options_bool_value('fanscounter_active') && !essb_options_bool_value('deactivate_module_followers')) {
	define('ESSB3_SOCIALFANS_ACTIVE', 'true');

	global $essb_socialfans_options;
	$essb_socialfans_options = get_option(ESSB3_OPTIONS_NAME_FANSCOUNTER);

	if (has_filter('essb4_followeroptions_extender')) {
		$essb_socialfans_options = apply_filters('essb4_followeroptions_extender', $essb_socialfans_options);
	}


	include_once (ESSB3_MODULES_PATH . 'social-followers-counter/essb-social-followers-counter-helper.php');

	// if options does not exist we intialize the default settings
	if (!is_array($essb_socialfans_options)) {
		$essb_socialfans_options = array();
		$essb_socialfans_options['expire'] = 1400;
		$essb_socialfans_options['format'] = 'short';

		// apply default values from structure helper
		$essb_socialfans_options = ESSBSocialFollowersCounterHelper::create_default_options_from_structure($essb_socialfans_options);
	}

	if (!essb_option_bool_value('fanscounter_widget_deactivate')) {
	    include_once (ESSB3_MODULES_PATH . 'social-followers-counter/essb-social-followers-counter-widget.php');
	}
	
	include_once (ESSB3_MODULES_PATH . 'social-followers-counter/essb-social-followers-counter.php');
}

if (!defined('ESSB3_LIGHTMODE')) {
	if (essb_options_bool_value('esml_active') && !essb_option_bool_value('deactivate_module_metrics')) {
		if (!defined('ESSB3_ESML_ACTIVE')) {
			define('ESSB3_ESML_ACTIVE', 'true');
		}

		if (is_admin()) {
		    include_once(ESSB3_MODULES_PATH . 'social-metrics/class-socialmetrics.php');
		}
		include_once(ESSB3_MODULES_PATH . 'social-metrics/socialmetrics-functions.php');
	}

}

if (essb_is_active_feature('cachedcounters')) {
	define('ESSB3_CACHED_COUNTERS', true);
	include_once(ESSB3_PLUGIN_ROOT . 'lib/core/share-counters/essb-cached-counters.php');

	if (essb_options_bool_value('counter_recover_active')) {
		define('ESSB3_SHARED_COUNTER_RECOVERY', true);
		include_once(ESSB3_PLUGIN_ROOT . 'lib/core/share-counters/essb-sharecounter-recovery.php');
	}
	
	if (essb_option_bool_value('homepage_total_allposts') || essb_option_bool_value('site_total_allposts')) {
		include_once(ESSB3_PLUGIN_ROOT . 'lib/core/share-counters/essb-homepage-counters.php');
	}
	
	if (essb_option_bool_value('hide_counter_homepage') || essb_option_bool_value('hide_counter_archive')) {
		include_once(ESSB3_PLUGIN_ROOT . 'lib/core/share-counters/essb-hidden-counters.php');
	}
}

// click to tweet module
if (!essb_options_bool_value('deactivate_ctt')) {
    include_once (ESSB3_MODULES_PATH . 'click-to-tweet/essb-click-to-tweet.php');
}

if (essb_option_bool_value('optin_content_activate') && !essb_option_bool_value('deactivate_module_subscribe')) {
    include_once (ESSB3_MODULES_PATH . 'optin-below-content/optin-below-content.php');
}

if (essb_option_bool_value('optin_flyout_activate') && !essb_option_bool_value('deactivate_module_subscribe')) {
    include_once (ESSB3_MODULES_PATH . 'optin-flyout/class-optin-flyout.php');
}

if (essb_option_bool_value('optin_booster_activate') && !essb_options_bool_value('deactivate_module_subscribe')) {
    include_once (ESSB3_MODULES_PATH . 'optin-booster/class-optin-booster.php');
}

if (essb_option_bool_value('fbmessenger_active') && !essb_options_bool_value('deactivate_module_facebookchat')) {
    include_once (ESSB3_MODULES_PATH . 'social-chat/essb-messenger-live-chat.php');
}

if (essb_option_bool_value('skype_active') && !essb_options_bool_value('deactivate_module_skypechat')) {
    include_once (ESSB3_MODULES_PATH . 'social-chat/essb-skype-live-chat.php');
}

if (essb_option_bool_value('click2chat_activate') && !essb_options_bool_value('deactivate_module_clicktochat')) {
    include_once (ESSB3_MODULES_PATH . 'social-chat/essb-click2chat.php');
}


// visual composer element bridge
if (function_exists('vc_map')) {
    include_once (ESSB3_MODULES_PATH . 'visual-composer/essb-visual-composer-map.php');
}

// WPML Bridge
if (essb_installed_wpml() || essb_installed_polylang()) {
	$essb_translate_options = get_option(ESSB3_WPML_OPTIONS_NAME);
	include_once(ESSB3_PLUGIN_ROOT . 'lib/core/essb-wpml-bridge.php');
	if (!is_admin()) {
		if (has_filter('essb4_options_multilanguage')) {
			$essb_options = apply_filters('essb4_options_multilanguage', $essb_options);
			
			/**
			 * @since 7.7 Integrate filter to the new options
			 */
			ESSB_Plugin_Options::trigger_options_filter('essb4_options_multilanguage');
		}
		if (defined('ESSB3_SOCIALFANS_ACTIVE')) {
			if (has_filter('essb4_followeroptions_multilanguage')) {
				$essb_socialfans_options = apply_filters('essb4_followeroptions_multilanguage', $essb_socialfans_options);
			}
		}
	}
}

if (essb_option_bool_value('amp_positions')) {
    include_once (ESSB3_MODULES_PATH . 'amp-sharing/essb-amp-sharebuttons.php');
}

if (essb_option_value('functions_mode_mobile') == 'auto') {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/helpers/helpers-automatic-mobile.php');
}

if (essb_option_bool_value('activate_networks_manage')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/helpers/helpers-manage-networks.php');
}

if (essb_sanitize_option_value('css_mode') == 'slim') {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/helpers/helpers-slimstyles.php');
}

if (essb_option_bool_value('activate_fake')) {
    include_once (ESSB3_MODULES_PATH . 'fake-share-counters/fakesharecounters-front.php');
}

if (essb_option_bool_value('activate_fake_counters')) {
    include_once (ESSB3_MODULES_PATH . 'fake-share-counters/fakesharecounters-adaptive.php');
}

if (essb_option_bool_value('activate_minimal')) {
    include_once (ESSB3_MODULES_PATH . 'fake-share-counters/minimalsharecounters-front.php');
}

if (essb_option_bool_value('activate_hooks')) {
    include_once (ESSB3_MODULES_PATH . 'custom-hooks-integration/hookintegrations-helper.php');
    include_once (ESSB3_MODULES_PATH . 'custom-hooks-integration/hookintegrations-class.php');
}

if (essb_option_bool_value('conversions_lite_run') && !essb_options_bool_value('deactivate_module_conversions')) {
    include_once (ESSB3_MODULES_PATH . 'conversions-lite/class-conversions-lite.php');
}

if (essb_option_bool_value('conversions_subscribe_lite_run') && !essb_options_bool_value('deactivate_module_conversions')) {
    include_once (ESSB3_MODULES_PATH . 'conversions-lite/class-subscribe-conversions-lite.php');
}

/**
 * Running integrations
 */
if (class_exists('REALLY_SIMPLE_SSL')) {
    include_once (ESSB3_INTEGRATIONS_PATH . 'reallysimplessl.php');
}

if (essb_option_bool_value('activate_sw_bridge')) {
    include_once (ESSB3_INTEGRATIONS_PATH . 'warfare.php');
}

if (essb_option_bool_value('activate_ss_bridge')) {
    include_once (ESSB3_INTEGRATIONS_PATH . 'socialsnap.php');
}

if (essb_option_bool_value('activate_ms_bridge')) {
    include_once (ESSB3_INTEGRATIONS_PATH . 'mashshare.php');
}

if (essb_option_bool_value('rankmath_og_deactivate')) {
    include_once (ESSB3_INTEGRATIONS_PATH . 'rankmath.php');
}

if (!essb_option_bool_value('deactivate_custompositions')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/options/custompositions-helper.php');
	include_once (ESSB3_PLUGIN_ROOT . 'lib/core/integrations/wpbackery-custompositions.php');

	if (!essb_option_bool_value('remove_elementor_widgets') && defined('ELEMENTOR_PATH')) {
		include_once (ESSB3_PLUGIN_ROOT . 'lib/core/integrations/elementor-custompositions.php');
	}
}

// inclduing elementor bridget
if (defined('ELEMENTOR_PATH') && !essb_option_bool_value('remove_elementor_widgets')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/elementor/class-elementor-loader.php');
}

if (essb_option_bool_value('mytemplate_activate')) {
    include_once (ESSB3_MODULES_PATH . 'template-builder/essb-templatebuilder-bridge.php');
}

if (!essb_option_bool_value('deactivate_module_instagram')) {
    include_once (ESSB3_MODULES_PATH . 'instagram-feed/class-instagram-feed.php');
}

if (!essb_option_bool_value('deactivate_module_proofnotifications')) {
    include_once (ESSB3_MODULES_PATH . 'social-proof-notifications-lite/class-spn.php');
}

if (!essb_option_bool_value('deactivate_custombuttons') && essb_option_bool_value('custombuttons_enable')) {
    include_once (ESSB3_CORE_PATH . 'classes/class-register-custom-networks.php');
}

ESSB_Plugin_Options::trigger_options_filter('essb4_options_extender_after_load');

if (essb_option_bool_value('activate_automatic_position')) {
	ESSB_Runtime_Cache::set('adaptive-styles', true);
}

include_once (ESSB3_HELPERS_PATH . 'helpers-share-data.php');

include_once (ESSB3_CORE_PATH . 'helpers/helpers-core-sharing.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/essb-button-helper.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/essb-shortcode-mapper.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/core/essb-actions-mapper.php');
include_once (ESSB3_PLUGIN_ROOT . 'lib/essb-core.php');

?>
