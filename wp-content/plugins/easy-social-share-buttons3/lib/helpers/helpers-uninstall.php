<?php

if (!function_exists('essb_clear_on_uninstall')) {
    function essb_clear_on_uninstall() {
        delete_post_meta_by_key('essb_shorturl_googl');
        delete_post_meta_by_key('essb_shorturl_post');
        delete_post_meta_by_key('essb_shorturl_bitly');
        delete_post_meta_by_key('essb_shorturl_ssu');
        delete_post_meta_by_key('essb_shorturl_rebrand');
        delete_post_meta_by_key('essb_shorturl_pus');
        
        // share counters
        delete_post_meta_by_key('essb_cache_expire');
        
        $networks = essb_available_social_networks();
        foreach ($networks as $key => $data) {
            delete_post_meta_by_key('essb_c_'.$key);
            delete_post_meta_by_key('essb_pc_'.$key);
        }
        
        delete_post_meta_by_key('essb_c_total');
        
        delete_post_meta_by_key('_essb_love');
        delete_post_meta_by_key('essb_metrics_data');
        
        delete_post_meta_by_key('essb_cached_image');
        
        // post setup data
        delete_post_meta_by_key('essb_off');
        delete_post_meta_by_key('essb_post_button_style');
        delete_post_meta_by_key('essb_post_template');
        delete_post_meta_by_key('essb_post_counters');
        delete_post_meta_by_key('essb_post_counter_pos');
        delete_post_meta_by_key('essb_post_total_counter_pos');
        delete_post_meta_by_key('essb_post_customizer');
        delete_post_meta_by_key('essb_post_animations');
        delete_post_meta_by_key('essb_post_optionsbp');
        delete_post_meta_by_key('essb_post_content_position');
        
        foreach ( essb_available_button_positions() as $position => $name ) {
            delete_post_meta_by_key("essb_post_button_position_{$position}");
        }
        
        delete_post_meta_by_key('essb_post_native');
        delete_post_meta_by_key('essb_post_native_skin');
        delete_post_meta_by_key('essb_post_share_message');
        delete_post_meta_by_key('essb_post_share_url');
        delete_post_meta_by_key('essb_post_share_image');
        delete_post_meta_by_key('essb_post_share_text');
        delete_post_meta_by_key('essb_post_pin_image');
        delete_post_meta_by_key('essb_post_fb_url');
        delete_post_meta_by_key('essb_post_plusone_url');
        delete_post_meta_by_key('essb_post_twitter_hashtags');
        delete_post_meta_by_key('essb_post_twitter_username');
        delete_post_meta_by_key('essb_post_twitter_tweet');
        delete_post_meta_by_key('essb_activate_ga_campaign_tracking');
        delete_post_meta_by_key('essb_post_og_desc');
        delete_post_meta_by_key('essb_post_og_author');
        delete_post_meta_by_key('essb_post_og_title');
        delete_post_meta_by_key('essb_post_og_image');
        delete_post_meta_by_key('essb_post_og_video');
        delete_post_meta_by_key('essb_post_og_video_w');
        delete_post_meta_by_key('essb_post_og_video_h');
        delete_post_meta_by_key('essb_post_og_url');
        delete_post_meta_by_key('essb_post_twitter_desc');
        delete_post_meta_by_key('essb_post_twitter_title');
        delete_post_meta_by_key('essb_post_twitter_image');
        delete_post_meta_by_key('essb_post_google_desc');
        delete_post_meta_by_key('essb_post_google_title');
        delete_post_meta_by_key('essb_post_google_image');
        delete_post_meta_by_key('essb_activate_sharerecovery');
        delete_post_meta_by_key('essb_post_og_image1');
        delete_post_meta_by_key('essb_post_og_image2');
        delete_post_meta_by_key('essb_post_og_image3');
        delete_post_meta_by_key('essb_post_og_image4');
        
        // Adding remove command for legacy social metrics lite data from versions 3.x, 2.x
        delete_post_meta_by_key('esml_socialcount_LAST_UPDATED');
        delete_post_meta_by_key('esml_socialcount_TOTAL');
        delete_post_meta_by_key('esml_socialcount_facebook');
        delete_post_meta_by_key('esml_socialcount_twitter');
        delete_post_meta_by_key('esml_socialcount_googleplus');
        delete_post_meta_by_key('esml_socialcount_linkedin');
        delete_post_meta_by_key('esml_socialcount_pinterest');
        delete_post_meta_by_key('esml_socialcount_diggs');
        delete_post_meta_by_key('esml_socialcount_delicious');
        delete_post_meta_by_key('esml_socialcount_facebook_comments');
        delete_post_meta_by_key('esml_socialcount_stumbleupon');
        
        // removing plugin saved possible options
        delete_option('essb3_addons');
        delete_option('essb3_addons_announce');
        delete_option(ESSB3_OPTIONS_NAME);
        delete_option('essb_dismissed_notices');
        
        delete_option(ESSB3_OPTIONS_NAME_FANSCOUNTER);
        delete_option(ESSB3_FIRST_TIME_NAME);
        delete_option('essb-shortcodes');
        delete_option('essb-hook');
        delete_option('essb3-translate-notice');
        delete_option('essb3-subscribe-notice');
        delete_option(ESSB3_EASYMODE_NAME);
        delete_option(ESSB5_SETTINGS_ROLLBACK);
        delete_option('essb-admin-settings-token');
        delete_option('essb_cache_static_cache_ver');
        delete_option('essb4-activation');
        delete_option('essb4-latest-version');
        delete_option('essb-conversions-lite');
        delete_option('essb-subscribe-conversions-lite');
        delete_option('essbfcounter_cached');
        delete_option('essbfcounter_expire');
        delete_option(ESSB3_MAIL_SALT);
        delete_option('essb_custom_buttons');
        delete_option('essb_custom_profile_buttons');
        delete_option('essb_options_forms');
        delete_option('essb_stylemaneger_user');
        delete_option('essb_custom_positions');
        delete_option('essb_instagram_accounts');
        
        delete_option('essb3-of');
        delete_option('essb3-ofob');
        delete_option('essb3-ofof');
        delete_option('essb-fake');
        delete_option('essb-hook');
        delete_option('essb3-oflock');
        
        global $wpdb;
        $table  = $wpdb->prefix . ESSB3_TRACKER_TABLE;
        $wpdb->query( "DROP TABLE IF EXISTS ".$table );
        
        if (!class_exists('ESSB_Subscribe_Conversions_Pro')) {
            include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/conversions-pro/class-subscribe-conversions.php');
        }
        
        ESSB_Subscribe_Conversions_Pro::uninstall();
        
        if (!class_exists('ESSB_Share_Conversions_Pro')) {
            include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/conversions-pro/class-share-conversions.php');
        }
        
        ESSB_Share_Conversions_Pro::uninstall();
        
        if (!class_exists('ESSB_Logger_ShareCounter_Update')) {
            include_once (ESSB3_CLASS_PATH . 'loggers/class-sharecounter-update.php');
        }
        
        ESSB_Logger_ShareCounter_Update::clear();

        
        if (!class_exists('ESSB_Logger_Followers_Update')) {
            include_once (ESSB3_CLASS_PATH . 'loggers/class-followers-update.php');
        }
        
        ESSB_Logger_Followers_Update::clear();
        
        // Post Meta Class
        if (!class_exists('ESSB_Post_Meta')) {
            include_once (ESSB3_PLUGIN_ROOT . 'lib/classes/class-post-meta.php');
        }        
        
        ESSB_Post_Meta::uninstall();
    }
}