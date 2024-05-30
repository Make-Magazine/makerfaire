<?php
/*
  Plugin Name: YouTube WordPress Plugin Pro by Embed Plus
  Plugin URI: https://www.embedplus.com/dashboard/pro-easy-video-analytics.aspx
  Description: YouTube Embed Plugin. Embed a YouTube channel gallery, playlist gallery, YouTube live stream. Lite embeds with defer JavaScript and facade options
  Version: 14.2.1.2
  Author: Embed Plus for YouTube Team
  Author URI: https://www.embedplus.com
  Requires at least: 4.5
 */

/*
  Embed Plus Plugin for YouTube Pro
  Copyright (C) 2023 EmbedPlus.com

 */

//define('WP_DEBUG', true);

class YouTubePrefsPro
{

    public static $folder_name = 'youtube-embed-plus-pro';
    public static $curltimeout = 30;
    public static $version = '14.2.1.2';
    public static $opt_version = 'version';
    public static $opt_free_migrated = 'free_migrated';
    public static $optembedwidth = null;
    public static $optembedheight = null;
    public static $defaultheight = null;
    public static $defaultwidth = null;
    public static $oembeddata = null;
    public static $opt_center = 'centervid';
    public static $opt_glance = 'glance';
    public static $opt_autoplay = 'autoplay';
    public static $opt_debugmode = 'debugmode';
    public static $opt_uninstall_data = 'uninstall_data';
    public static $opt_old_script_method = 'old_script_method';
    public static $opt_cc_load_policy = 'cc_load_policy';
    public static $opt_cc_lang_pref = 'cc_lang_pref';
    public static $opt_iv_load_policy = 'iv_load_policy';
    public static $opt_loop = 'loop';
    public static $opt_modestbranding = 'modestbranding';
    public static $opt_rel = 'rel';
    public static $opt_fs = 'fs';
    public static $opt_playsinline = 'playsinline';
    public static $opt_autohide = 'autohide';
    public static $opt_controls = 'controls';
    public static $opt_theme = 'theme';
    public static $opt_color = 'color';
    public static $opt_listType = 'listType';
    public static $opt_dohl = 'dohl';
    public static $opt_hl = 'hl';
    public static $opt_ogvideo = 'ogvideo';
    public static $opt_nocookie = 'nocookie';
    public static $opt_gb_compat = 'gb_compat';
    public static $opt_facade_mode = 'facade_mode';
    public static $opt_facade_autoplay = 'facade_autoplay';
    public static $opt_gdpr_consent = 'gdpr_consent';
    public static $opt_gdpr_consent_message = 'gdpr_consent_message';
    public static $opt_gdpr_consent_button = 'gdpr_consent_button';
    public static $gdpr_cookie_name = 'ytprefs_gdpr_consent';
    public static $opt_playlistorder = 'playlistorder';
    public static $opt_acctitle = 'acctitle';
    public static $opt_pro = 'pro';
    public static $opt_oldspacing = 'oldspacing';
    public static $opt_frontend_only = 'frontend_only';
    public static $opt_responsive = 'responsive';
    public static $opt_responsive_all = 'responsive_all';
    public static $opt_origin = 'origin';
    public static $opt_widgetfit = 'widgetfit';
    public static $opt_evselector_light = 'evselector_light';
    public static $opt_stop_mobile_buffer = 'stop_mobile_buffer';
    public static $opt_restrict_wizard = 'restrict_wizard';
    public static $opt_restrict_wizard_roles = 'restrict_wizard_roles';
    public static $opt_ajax_compat = 'ajax_compat';
    public static $opt_maxres_facade = 'maxres_facade';
    public static $opt_ytapi_load = 'ytapi_load';
    public static $opt_defaultdims = 'defaultdims';
    public static $opt_defaultwidth = 'width';
    public static $opt_defaultheight = 'height';
    public static $opt_pause_others = 'pause_others';
    public static $opt_defaultvol = 'defaultvol';
    public static $opt_vol = 'vol';
    public static $opt_apikey = 'apikey';
    public static $opt_schemaorg = 'schemaorg';
    public static $opt_ftpostimg = 'ftpostimg';
    public static $opt_spdc = 'spdc';
    public static $opt_spdcab = 'spdcab';
    public static $opt_spdcexp = 'spdcexp';
    public static $opt_dashpre = 'dashpre';
    public static $opt_migrate = 'migrate';
    public static $opt_migrate_youtube = 'migrate_youtube';
    public static $opt_migrate_embedplusvideo = 'migrate_embedplusvideo';
    public static $spdcprefix = 'ytpref';
    public static $spdcall = 'youtubeprefs_spdcall';
    public static $opt_dynload = 'dynload';
    public static $opt_dyntype = 'dyntype';
    public static $opt_gallery_pagesize = 'gallery_pagesize';
    public static $opt_gallery_limit_dsc = 'gallery_limit_dsc';
    public static $opt_gallery_length_dsc = 'gallery_length_dsc';
    public static $opt_gallery_columns = 'gallery_columns';
    public static $opt_gallery_collapse_grid = 'gallery_collapse_grid';
    public static $opt_gallery_collapse_grid_breaks = 'gallery_collapse_grid_breaks';
    public static $opt_gallery_style = 'gallery_style';
    public static $opt_gallery_scrolloffset = 'gallery_scrolloffset';
    public static $opt_gallery_hideprivate = 'gallery_hideprivate';
    public static $opt_gallery_showtitle = 'gallery_showtitle';
    public static $opt_gallery_showpaging = 'gallery_showpaging';
    public static $opt_gallery_thumbplay = 'gallery_thumbplay';
    public static $opt_gallery_autonext = 'gallery_autonext';
    public static $opt_gallery_hidethumbimg = 'gallery_hidethumbimg';
    public static $opt_gallery_channelsub = 'gallery_channelsub';
    public static $opt_gallery_channelsublink = 'gallery_channelsublink';
    public static $opt_gallery_channelsubtext = 'gallery_channelsubtext';
    public static $opt_gallery_customarrows = 'gallery_customarrows';
    public static $opt_gallery_customprev = 'gallery_customprev';
    public static $opt_gallery_customnext = 'gallery_customnext';
    public static $opt_gallery_showdsc = 'gallery_showdsc';
    public static $opt_gallery_thumbcrop = 'gallery_thumbcrop';
    public static $opt_gallery_disptype = 'gallery_disptype';
    public static $opt_not_live_content = 'not_live_content';
    public static $opt_not_live_on = 'not_live_on';
    public static $opt_not_live_showtime = 'not_live_showtime';
    public static $opt_not_live_on_channel = 'not_live_on_channel';
    public static $opt_live_chat = 'live_chat';
    public static $opt_admin_off_scripts = 'admin_off_scripts';
    public static $opt_defer_js = 'defer_js';
    public static $opt_defer_jquery = 'defer_jquery';
    public static $opt_ajax_save = 'ajax_save';
    public static $opt_onboarded = 'onboarded';
    public static $opt_show_pointer = 'show_pointer';
    public static $opt_alloptions = 'youtubeprefspro_alloptions';
    public static $alloptions = null;
    public static $yt_options = array();
    public static $dft_bpts = array(array('bp' => array('min' => 0, 'max' => 767), 'cols' => 1));
    public static $dft_roles = array('administrator', 'editor', 'author', 'contributor', 'subscriber');
    public static $epbase = 'https://www.embedplus.com';
    public static $double_plugin = false;
    public static $scriptsprinted = 0;
    public static $min = '.min';
    public static $badentities = array('&#215;', '×', '&#8211;', '–', '&amp;', '&#038;', '&#38;');
    public static $goodliterals = array('x', 'x', '--', '--', '&', '&', '&');
    public static $wizard_hook = '';
    public static $onboarding_hook = '';
    public static $admin_page_hooks = array();
    public static $the_content_filters = array(
        'wptexturize',
        'wpautop',
        'shortcode_unautop',
        'prepend_attachment',
        'wp_make_content_images_responsive',
        'wp_filter_content_tags',
        'do_shortcode',
        'convert_smilies'
    );
    public static $get_api_key_msg = '';
    public static $boilerplate_api_error_message = '';
    public static $dft_gdpr_consent_message = '';
    public static $no_streams = 'NO STREAMS';
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    public static $vi_default_date = ''; // date('Y-m-d H:i:s', strtotime('2000-01-01'));
    public static $vi_last_category_update_interval = '1 hour';
    public static $vi_script_tag_done = false;
    public static $vi_dft_js_settings = array(
        //"adUnitType" => "NATIVE_VIDEO_UNIT",
        "divId" => "ytvi_story_container",
        "language" => "en-us",
        "iabCategory" => "",
        "font" => "Arial",
        "fontSize" => 12,
        "keywords" => "",
        "textColor" => "#000000",
        "backgroundColor" => "#ffffff",
        "vioptional1" => "",
        "vioptional2" => "",
        "vioptional3" => "",
        "float" => true,
        //"logoUrl" => "",
        "dfpSupport" => true,
        "sponsoredText" => "",
        "poweredByText" => ""
    );
    public static $opt_vi_active = 'vi_active';
    public static $opt_vi_hide_monetize_tab = 'vi_hide_monetize_tab';
    public static $opt_vi_endpoints = 'vi_endpoints';
    public static $opt_vi_token = 'vi_token';
    public static $opt_vi_last_login = 'vi_last_login';
    public static $opt_vi_last_category_update = 'vi_last_category_update';
    public static $opt_vi_adstxt = 'vi_adstxt';
    public static $opt_vi_js_settings = 'vi_js_settings';
    public static $opt_vi_js_script = 'vi_js_script';
    public static $opt_vi_js_posttypes = 'vi_js_posttypes';
    public static $opt_vi_js_position = 'vi_js_position';
    public static $opt_vi_show_gdpr_authorization = 'vi_show_gdpr_authorization';
    public static $opt_vi_show_privacy_button = 'vi_show_privacy_button';
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////

    public static $oldytregex = '@^\s*https?://(?:www\.)?(?:(?:youtube.com/(?:(?:watch)|(?:embed)|(?:playlist))(?:/live_stream){0,1}/{0,1}\?)|(?:youtu.be/))([^\s"]+)\s*$@im';
    public static $ytregex = '@^[\r\t ]*https?://(?:www\.)?(?:(?:youtube.com/(?:(?:watch)|(?:embed)|(?:playlist))(?:/live_stream){0,1}/{0,1}\?)|(?:youtu.be/))([^\s"]+)[\r\t ]*$@im';
    public static $justurlregex = '@https?://(?:www\.)?(?:(?:youtube.com/(?:(?:watch)|(?:embed)|(?:playlist))(?:/live_stream){0,1}/{0,1}\?)|(?:youtu.be/))([^\[\s"]+)@i';

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////


    public function __construct()
    {
        if (defined('EMBEDPLUS_BASE_URL'))
        {
            self::$epbase = EMBEDPLUS_BASE_URL;
        }
        self::$get_api_key_msg = __('The ### feature now requires a (free) YouTube API key from Google. Please follow the easy steps <a href="https://www.youtube.com/watch?v=ZCfrNvu6nMc" target="_blank">in this video</a> to create and save your API key.', 'text_domain');
        self::$boilerplate_api_error_message = __(' Please make sure you performed the <a href="https://www.youtube.com/watch?v=ZCfrNvu6nMc" target="_blank">steps in this video</a> to create and save a proper server API key.', 'text_domain');
        self::$dft_gdpr_consent_message = __('<p><strong>Please accept YouTube cookies to play this video.</strong> By accepting you will be accessing content from YouTube, a service provided by an external third party.</p><p><a href="https://policies.google.com/privacy" target="_blank">YouTube privacy policy</a></p><p>If you accept this notice, your choice will be saved and the page will refresh.</p>', 'text_domain');

        self::$vi_default_date = date('Y-m-d H:i:s', strtotime('2000-01-01'));
        register_deactivation_hook(__FILE__, array(self::class, 'on_deactivation'));
        add_action('admin_init', array(self::class, 'check_double_plugin_warning'));
        add_action('admin_notices', array(self::class, 'check_free_version'));

        $active_plugins = get_option('active_plugins', array());
        if (!in_array('youtube-embed-plus/youtube.php', $active_plugins))
        {
            self::$alloptions = get_option(self::$opt_alloptions);

            if (self::$alloptions == false || version_compare(self::$alloptions[self::$opt_version], self::$version, '<'))
            {
                self::initoptions();
            }

            if ((defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) || self::$alloptions[self::$opt_debugmode] == 1)
            {
                self::$min = '';
            }

            if (self::$alloptions[self::$opt_oldspacing] == 1)
            {
                self::$ytregex = self::$oldytregex;
            }

            self::$optembedwidth = intval(get_option('embed_size_w'));
            self::$optembedheight = intval(get_option('embed_size_h'));

            self::$yt_options = array(
                self::$opt_autoplay,
                self::$opt_cc_load_policy,
                self::$opt_cc_lang_pref,
                self::$opt_iv_load_policy,
                self::$opt_loop,
                self::$opt_modestbranding,
                self::$opt_rel,
                self::$opt_fs,
                self::$opt_playsinline,
                self::$opt_autohide,
                self::$opt_controls,
                self::$opt_hl,
                self::$opt_theme,
                self::$opt_color,
                self::$opt_listType,
                'index',
                'list',
                'start',
                'end',
                'channel'
            );

            add_action('media_buttons', array(self::class, 'media_button_wizard'), 11);


            self::do_ytprefs();
            add_action('admin_menu', array(self::class, 'ytprefs_plugin_menu'));
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(self::class, 'my_plugin_action_links'));

            if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 8)
            {
                add_action('admin_bar_menu', array(self::class, 'ytprefs_admin_bar'), 100);
                add_action('wp_enqueue_scripts', array(self::class, 'ytprefs_admin_bar_scripts'));
                add_action('admin_enqueue_scripts', array(self::class, 'ytprefs_admin_bar_scripts'));
            }


            if (!is_admin())
            {
                if (self::$alloptions[self::$opt_old_script_method] == 1)
                {
                    add_action('wp_print_scripts', array(self::class, 'jsvars'));
                    add_action('wp_enqueue_scripts', array(self::class, 'jsvars'));
                }

                add_action('wp_enqueue_scripts', array(self::class, 'ytprefsscript'), 100);
                add_action('wp_enqueue_scripts', array(self::class, 'fitvids'), 101);

                if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0 && self::$alloptions[self::$opt_ogvideo] == 1)
                {
                    add_action('wp_head', array(self::class, 'do_ogvideo'));
                }
                add_filter('body_class', array(self::class, 'body_class'));
            }

            add_filter('ytprefs_filter_the_content_light', array(self::class, 'filter_the_content_light'));

            add_action("wp_ajax_my_embedplus_onboarding_save_ajax", array(self::class, 'onboarding_save_ajax'));
            add_action("wp_ajax_my_embedplus_settings_save_ajax", array(self::class, 'settings_save_ajax'));
            add_action("wp_ajax_my_embedplus_onboarding_save_apikey_ajax", array(self::class, 'onboarding_save_apikey_ajax'));
            add_action("wp_ajax_my_embedplus_pro_record", array(self::class, 'my_embedplus_pro_record'));
            add_action("wp_ajax_my_embedplus_dashpre", array(self::class, 'my_embedplus_dashpre'));
            add_action("wp_ajax_my_embedplus_clearspdc", array(self::class, 'my_embedplus_clearspdc'));
            add_action("wp_ajax_my_embedplus_glance_vids", array(self::class, 'my_embedplus_glance_vids'));
            add_action("wp_ajax_my_embedplus_glance_count", array(self::class, 'my_embedplus_glance_count'));
            add_action("wp_ajax_my_embedplus_dismiss_double_plugin_warning", array(self::class, 'my_embedplus_dismiss_double_plugin_warning'));
            add_action("wp_ajax_my_embedplus_gallery_page", array(self::class, 'my_embedplus_gallery_page'));
            add_action("wp_ajax_nopriv_my_embedplus_gallery_page", array(self::class, 'my_embedplus_gallery_page'));
            add_action('admin_enqueue_scripts', array(self::class, 'admin_enqueue_scripts'), 10, 1);

            if (isset(self::$alloptions[self::$opt_pro]) && strlen(trim(self::$alloptions[self::$opt_pro])) > 8)
            {
                add_action("wp_ajax_my_embedplus_realtimeLiveCheck", array(self::class, 'realtimeLiveCheck'));
                add_action("wp_ajax_nopriv_my_embedplus_realtimeLiveCheck", array(self::class, 'realtimeLiveCheck'));
                add_action("wp_ajax_my_embedplus_livestream_advice_close", array(self::class, 'livestream_advice_close'));
                
                if (isset(self::$alloptions[self::$opt_dashpre]) && self::$alloptions[self::$opt_dashpre] == '1')
                {
                    add_action("wp_ajax_my_embedplus_yt_dash", array(self::class, 'my_embedplus_yt_dash'));
                    add_action("wp_ajax_nopriv_my_embedplus_yt_dash", array(self::class, 'my_embedplus_yt_dash'));                    
                }
            }

            if (!empty(self::$alloptions[self::$opt_not_live_on_channel]))
            {
                add_action("wp_footer", array(self::class, 'live_fallback_template'));
            }
            
            add_action("wp_footer", array(self::class, 'livestream_advice_template'));
            
            /////////////////////////////////////
            include_once(EPYTVI_INCLUDES_PATH . 'vi_actions.php');
            include_once(EPYTGB_INCLUDES_PATH . 'gutenberg_hooks.php');
        }
    }
    
    public static function body_class($classes)
    {
        if (current_user_can('edit_posts'))
        {
            $classes[] = 'epyt-edit-posts';
        }        
        return $classes;
    }
    
    public static function livestream_advice_template()
    {
        if (current_user_can('edit_posts') && get_user_meta(get_current_user_id(), 'embedplus_livestream_advice_close', true) != 1)
        {
            echo '<script type="text/x-template" id="epyt-livestream-advice-template">' . base64_encode(self::livestream_advice(true)) . '</script>';
        }
    }
    
    public static function livestream_advice($is_embed)
    {
        ob_start(); ?>
        <div class="epyt-livestream-advice">
            <div class="epyt-livestream-advice-close">&times;</div>
            <p>
                <strong>Live stream not starting on time?</strong> Note the following:
            </p>
            <ul class="reglist">
                <li>Google has unfortunately disabled the ability to instantly show the stream as soon as it starts. This plugin uses caching as a workaround, and will show the stream at a delay of at most 10-15 minutes. So, we recommend starting your stream 15 minutes ahead of schedule to ensure your visitors will see this stream come up at the expected time (feel free to just show your logo or even a blank/muted screen for these initial few minutes while visitors gather to watch).</li>
                <li>If you have any additional caching plugins installed, you may also need to exclude <?php echo $is_embed ? 'this page' : 'the page the embed is on' ?> from their caching. Caching plugins sometimes prevent fresh livestream data from loading.</li>
            </ul>
            <?php if ($is_embed)
            {
                ?>
                <p>(Note: this message is only visible while you are logged in)</p>
                <?php
            }
        ?>            
        </div>
        <?php $content = ob_get_clean();
        return $content;
    }
    
    public static function livestream_advice_close()
    {
        $result = array();
        if (self::is_ajax())
        {
            $user_id = get_current_user_id();
            update_user_meta($user_id, 'embedplus_livestream_advice_close', 1);
            $result['type'] = 'success';
            echo json_encode($result);
        }
        else
        {
            $result['type'] = 'error';
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
        die();
    }

    public static function live_fallback_template()
    {
        echo '<script type="text/x-template" id="epyt-live-fallback">' . base64_encode(apply_filters('ytprefs_filter_the_content_light', wp_kses_post(self::$alloptions[self::$opt_not_live_content]))) . '</script>';
    }

    public static function defer_scripts($tag, $handle, $src)
    {
        $defer = array(
            '__dispload__',
            '__ytprefs__',
            '__dynload__',
            '__ytprefs__bar',
            '__ytprefsfitvids__',
            '__jquery_cookie__'
        );

        if (self::$alloptions[self::$opt_defer_jquery] == 1 && !is_admin())
        {
            array_push($defer, 'jquery', 'jquery-core', 'jquery-migrate');
        }

        if (in_array($handle, $defer) && stripos($tag, ' defer') === false)
        {
            $tag = str_replace(' src', ' defer src', $tag);
        }

        return $tag;
    }

    public static function check_free_version()
    {
        $active_plugins = get_option('active_plugins', array());
        if (in_array('youtube-embed-plus/youtube.php', $active_plugins))
        {
            $class = 'notice notice-error is-dismissible';
            $message = __('For YouTube Pro to work, please deactivate the free version.', 'text_domain');

            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), wp_kses_post($message));
        }
    }

    public static function my_plugin_action_links($links)
    {
        if (is_array($links))
        {
            $links[] = '<a href="' . esc_url(admin_url('admin.php?page=youtube-my-preferences')) . '">Settings</a>';
        }
        return $links;
    }

    public static function ytprefs_admin_bar_scripts()
    {
        if (current_user_can('edit_posts'))
        {
            wp_enqueue_script('__ytprefs__bar', plugins_url('scripts/ytprefs-bar' . self::$min . '.js', __FILE__), array('jquery'), self::$version);
            wp_localize_script('__ytprefs__bar', '_EPYTB_', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'security' => wp_create_nonce('embedplus-nonce'),
                'pluginurl' => plugins_url('/', __FILE__)
            ));
        }
    }

    public static function ytprefs_admin_bar($wp_admin_bar)
    {

        if (current_user_can('edit_posts') && self::$alloptions[self::$opt_spdc] == 1 && self::$alloptions[self::$opt_spdcab] == 1)
        {
            $args = array(
                'id' => 'ytprefs-bar-cache',
                'title' => __('Clear YouTube Cache', 'text_domain'),
                'href' => '#',
                'meta' => array(
                    'class' => ''
                )
            );
            $wp_admin_bar->add_node($args);
        }
    }

    public static function show_glance_list()
    {
        $glancehref = self::show_glance();
        $cnt = self::get_glance_count();

        //display via list
        return '<li class="page-count">
            <a href="' . $glancehref . '" class="thickbox ytprefs_glance_button" id="ytprefs_glance_button" title="YouTube Embeds At a Glance">' . number_format_i18n($cnt) . ' ' . __('With YouTube', 'text_domain') . '</a>
        </li>';
    }

    public static function show_glance_table()
    {
        $glancehref = self::show_glance();
        $cnt = self::get_glance_count();
        return
                '<tr>
            <td class="first b"><a title="' . __('YouTube Embeds At a Glance', 'text_domain') . '" href="' . $glancehref . '" class="thickbox ytprefs_glance_button">' . number_format_i18n($cnt) . '</a></td>
            <td class="t"><a title="' . __('YouTube Embeds At a Glance', 'text_domain') . '" href="' . $glancehref . '" id="ytprefs_glance_button" class="thickbox ytprefs_glance_button">' . __('With YouTube', 'text_domain') . '</a></td>
        </tr>';
    }

    public static function get_glance_count()
    {
        global $wpdb;
        $query_sql = "
                SELECT count(*) as mytotal
                FROM $wpdb->posts
                WHERE (post_content LIKE '%youtube.com/%' OR post_content LIKE '%youtu.be/%')
                AND post_status = 'publish'";

        $query_result = $wpdb->get_results($query_sql, OBJECT);

        return intval($query_result[0]->mytotal);
    }

    public static function show_glance()
    {
        $glancehref = admin_url('admin.php?page=youtube-ep-glance') . '&random=' . rand(1, 1000) . '&TB_iframe=true&width=780&height=800';
        return $glancehref;
    }

    public static function glance_page()
    {
        ?>
        <div class="wrap">
            <style type="text/css">
                #wphead {display:none;}
                #wpbody{margin-left: 0px;}
                .wrap {font-family: Arial; padding: 0px 10px 0px 10px; line-height: 180%;}
                .bold {font-weight: bold;}
                .orange {color: #f85d00;}
                sup.orange {text-transform: lowercase; font-weight: bold; color: #f85d00;}
                #adminmenuback {display: none;}
                #adminmenu, adminmenuwrap {display: none;}
                #wpcontent, .auto-fold #wpcontent {margin-left: 0px;}
                #wpadminbar {display:none;}
                html.wp-toolbar {padding: 0px;}
                #footer, #wpfooter, .auto-fold #wpfooter {display: none;}
                #wpfooter {clear: both}
                .acctitle {background-color: #dddddd; border-radius: 5px; padding: 7px 15px 7px 15px; cursor: pointer; margin: 10px; font-weight: bold; font-size: 12px;}
                .acctitle:hover {background-color: #cccccc;}
                .accbox {display: none; position: relative; margin:  5px 8px 30px 15px; clear: both; line-height: 180%;}
                .accclose {position: absolute; top: -38px; right: 5px; cursor: pointer; width: 24px; height: 24px;}
                .accloader {padding-right: 20px;}
                .accthumb {display: block; width: 300px; float: left; margin-right: 25px;}
                .accinfo {width: 300px; float: left;}
                .accvidtitle {font-weight: bold; font-size: 16px;}
                .accthumb img {width: 300px; height: auto; display: block;}
                .clearboth {clear: both;}
                .pad20 {padding: 20px;}
                .center {text-align: center;}
                #screen-meta-links {display: none;}
            </style>
            <script type="text/javascript">
                function accclose(ele)
                {
                    jQuery(ele).parent('.accbox').hide(400);
                }

                (function ($j)
                {
                    $j(document).ready(function ()
                    {


                        $j('.acctitle').on('click', function ()
                        {
                            var $acctitle = $j(this);
                            var $accbox = $j(this).parent().children('.accbox');
                            var pid = $accbox.attr("data-postid");
                            $acctitle.prepend('<img alt="loading" class="accloader" src="<?php echo plugins_url('images/ajax-loader.gif', __FILE__) ?>" />');
                            jQuery.ajax({
                                type: "post",
                                dataType: "json",
                                timeout: 30000,
                                url: window._EPYTA_ ? window._EPYTA_.wpajaxurl : ajaxurl,
                                data: {action: 'my_embedplus_glance_vids', postid: pid},
                                success: function (response)
                                {
                                    if (response.type === "success")
                                    {
                                        $accbox.html(response.data),
                                                $accbox.show(400);
                                    }
                                    else
                                    {
                                    }
                                },
                                error: function (xhr, ajaxOptions, thrownError)
                                {

                                },
                                complete: function ()
                                {
                                    $acctitle.children('.accloader').remove();
                                }

                            });
                        });
                    });
                })(jQuery);</script>
            <?php
            global $wpdb;
            $query_sql = "
                SELECT SQL_CALC_FOUND_ROWS *
                FROM $wpdb->posts
                WHERE (post_content LIKE '%youtube.com/%' OR post_content LIKE '%youtu.be/%')
                AND post_status = 'publish'
                order by post_date DESC LIMIT 0, 10";

            $query_result = $wpdb->get_results($query_sql, OBJECT);

            if ($query_result !== null)
            {
                $total = $wpdb->get_var("SELECT FOUND_ROWS();");
                global $post;
                echo '<h2>' . sprintf(__('10 Latest Posts/Pages with YouTube Videos (%1$s Total)', 'text_domain'), intval($total)) . '</h2>';
                _e('We recommend using this page as an easy way to check the results of the global default settings you make (e.g. hide annotations) on your recent embeds. Or, simply use it as an index to jump right to your posts that contain YouTube embeds.', 'text_domain');
                if ($total > 0)
                {
                    echo '<ul class="accord">';
                    foreach ($query_result as $post)
                    {
                        echo '<li>';
                        setup_postdata($post);
                        the_title('<div class="acctitle">', ' &raquo;</div>');
                        echo '<div class="accbox" data-postid="' . $post->ID . '"></div><div class="clearboth"></div></li>';
                    }
                    echo '</ul>';
                }
                else
                {
                    echo '<p class="center bold orange">' . __('You currently do not have any YouTube embeds yet.', 'text_domain') . '</p>';
                }
            }

            wp_reset_postdata();
            ?>
            <?php printf(__('To remove this feature from your dashboard, simply uncheck <i>Show "At a Glance" Embed Links</i> in the <a target="_blank" href="%1$s">plugin settings page &raquo;</a>.', 'text_domain'), admin_url('admin.php?page=youtube-my-preferences#jumpdefaults')); ?>
        </div>
        <?php
    }

    public static function is_ajax()
    {
        $requested_with = filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH');
        $is_ajax = (function_exists('wp_doing_ajax') && wp_doing_ajax() || (!empty($requested_with) && strtolower($requested_with) == 'xmlhttprequest') || (defined('DOING_AJAX') && DOING_AJAX));
        if ($is_ajax)
        {
            header('HTTP/1.1 200 OK');
        }
        return $is_ajax;
    }

    public static function my_embedplus_glance_vids()
    {
        $result = array();
        if (self::is_ajax())
        {
            $postid = intval($_REQUEST['postid']);
            $currpost = get_post($postid);

            $thehtml = '<img alt="close" class="accclose" onclick="accclose(this)" src="' . plugins_url('images/accclose.png', __FILE__) . '" />';

            $matches = array();
            $ismatch = preg_match_all(self::$justurlregex, $currpost->post_content, $matches);

            if ($ismatch)
            {
                foreach ($matches[0] as $match)
                {
                    $link = trim(preg_replace('/&amp;/i', '&', $match));
                    $link = preg_replace('/\s/', '', $link);
                    $link = trim(str_replace(self::$badentities, self::$goodliterals, $link));

                    $linkparamstemp = explode('?', $link);

                    $linkparams = array();
                    if (count($linkparamstemp) > 1)
                    {
                        $linkparams = self::keyvalue($linkparamstemp[1], true);
                    }
                    if (strpos($linkparamstemp[0], 'youtu.be') !== false && !isset($linkparams['v']))
                    {
                        $vtemp = explode('/', $linkparamstemp[0]);
                        $linkparams['v'] = array_pop($vtemp);
                    }

                    $vidid = $linkparams['v'];

                    if ($vidid != null)
                    {
                        try
                        {
                            $odata = self::get_oembed('https://youtube.com/watch?v=' . $vidid, 1920, 1280);
                            $postlink = get_permalink($postid);
                            if ($odata != null && !is_wp_error($odata))
                            {
                                $_name = esc_attr(sanitize_text_field($odata->title));
                                $_description = esc_attr(sanitize_text_field($odata->author_name));
                                $_thumbnailUrl = esc_url("https://i.ytimg.com/vi/" . $vidid . "/0.jpg");

                                $thehtml .= '<a target="_blank" href="' . $postlink . '" class="accthumb"><img alt="' . __('YouTube Video', 'text_domain') . '" src="' . $_thumbnailUrl . '" /></a>';
                                $thehtml .= '<div class="accinfo">';
                                $thehtml .= '<a target="_blank" href="' . $postlink . '" class="accvidtitle">' . $_name . '</a>';
                                $thehtml .= '<div class="accdesc">' . (strlen($_description) > 400 ? substr($_description, 0, 400) . "..." : $_description) . '</div>';
                                $thehtml .= '</div>';
                                $thehtml .= '<div class="clearboth pad20"></div>';
                            }
                            else
                            {
                                $thehtml .= sprintf(__('<p class="center bold orange">This <a target="_blank" href="%1$s">post/page</a> contains a video that has been removed from YouTube.</p>', 'text_domain'), $postlink);
                            }
                        }
                        catch (Exception $ex)
                        {
                            
                        }
                    }
                    else if (isset($linkparams['list']))
                    {
                        // if playlist
                        try
                        {
                            $odata = self::get_oembed('https://youtube.com/playlist?list=' . $linkparams['list'], 1920, 1280);
                            $postlink = get_permalink($postid);
                            if ($odata != null && !is_wp_error($odata))
                            {
                                $_name = esc_attr(sanitize_text_field($odata->title));
                                $_description = esc_attr(sanitize_text_field($odata->author_name));
                                $_thumbnailUrl = esc_url($odata->thumbnail_url);

                                $thehtml .= '<a target="_blank" href="' . $postlink . '" class="accthumb"><img alt="' . __('YouTube Video', 'text_domain') . '" src="' . $_thumbnailUrl . '" /></a>';
                                $thehtml .= '<div class="accinfo">';
                                $thehtml .= '<a target="_blank" href="' . $postlink . '" class="accvidtitle">' . $_name . '</a>';
                                $thehtml .= '<div class="accdesc">' . (strlen($_description) > 400 ? substr($_description, 0, 400) . "..." : $_description) . '</div>';
                                $thehtml .= '</div>';
                                $thehtml .= '<div class="clearboth pad20"></div>';
                            }
                            else
                            {
                                $thehtml .= '<p class="center bold orange">' . sprintf(__('This <a target="_blank" href="%1$s">post/page</a> contains a video that has been removed from YouTube.', 'text_domain'), $postlink) . '</p>';
                            }
                        }
                        catch (Exception $ex)
                        {
                            
                        }
                    }
                }
            }



            if ($currpost != null)
            {
                $result['type'] = 'success';
                $result['data'] = $thehtml;
            }
            else
            {
                $result['type'] = 'error';
            }
            echo json_encode($result);
        }
        else
        {
            $result['type'] = 'error';
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
        die();
    }

    public static function my_embedplus_glance_count()
    {
        $result = array();
        if (self::is_ajax())
        {
            $thehtml = '';

            try
            {
                if (version_compare(get_bloginfo('version'), '3.8', '>='))
                {
                    $result['container'] = '#dashboard_right_now ul';
                    $thehtml .= self::show_glance_list();
                }
                else
                {
                    $result['container'] = '#dashboard_right_now .table_content table tbody';
                    $thehtml .= self::show_glance_table();
                }
                $result['type'] = 'success';
                $result['data'] = $thehtml;
            }
            catch (Exception $e)
            {
                $result['type'] = 'error';
            }

            echo json_encode($result);
        }
        else
        {
            $result['type'] = 'error';
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
        die();
    }

    public static function try_get_ytid($url)
    {
        $theytid = null;
        if (strpos($url, 'v=') !== false)
        {
            $url_params = explode('?', $url);
            $kvp = self::keyvalue($url_params[1], true);
            $theytid = $kvp['v'];
        }
        else if (strpos($url, "youtu.be") !== false)
        {
            $shortpath = explode('/', parse_url($url, PHP_URL_PATH));
            $theytid = $shortpath[1];
        }
        return $theytid;
    }

    public static function wizard()
    {
        ?>
        <div class="wrap" id="epyt_wiz_wrap">
            <div class="smallnote center"> <?php _e('Please periodically check the YouTube plugin tab on your admin panel to review the latest options.', 'text_domain') ?></div>

            <?php
            $form_valid = true;
            $acc_expand = '';
            $get_pro_link = self::$epbase . '/dashboard/pro-easy-video-analytics.aspx';


            $step1_api_error_msg = __(' Please confirm that the link works in your browser, and that <em>the owner of the video allowed embed sharing permissions (otherwise, contact the owner of the video to allow embedding)</em>. Then copy that full link in your address bar to paste here. If you are sure your link is correct, then (1) your API key may be too restrictive (<a target="_blank" href="https://console.developers.google.com/apis/credentials">check here</a>) or (2) you have reached your Google quota (<a href="https://console.developers.google.com/apis/dashboard" target="_blank">check here</a>). You can apply to Google for a <a href="https://services.google.com/fb/forms/ytapiquotarequest/" target="_blank">quota increase here</a>.', 'text_domain');
            $step1_video_errors = '';
            $step1_video_error_invalid = __('Sorry, that does not seem to be a link to an existing video. Please confirm that the link works in your browser, and that <em>the owner of the video allowed embed sharing permissions (otherwise, contact the owner of the video to allow embedding)</em>. Then copy that full link in your address bar to paste here.', 'text_domain');
            $step1_playlist_errors = '';
            $step1_playlist_error_invalid = __('Sorry, that does not seem to be a link to an existing playlist. Please confirm that the link works in your browser, and that <em>the owner of the playlist allowed embed sharing permissions (otherwise, contact the owner of the video to allow embedding)</em>. Then copy that full link in your address bar to paste here.', 'text_domain');
            $step1_channel_errors = '';
            $step1_channel_error_invalid = __('Sorry, that does not seem to be a link to an existing video.', 'text_domain') . ' ' . $step1_api_error_msg;
            $step1_live_errors = '';
            $step1_live_error_invalid = __('Sorry, that does not seem to be a valid link to an existing live video.', 'text_domain') . ' ' . $step1_api_error_msg;
            $step1_livechannel_errors = '';
            $step1_livechannel_error_invalid = __('Sorry, that does not seem to be a link to an existing channel. Note: the custom channel name will not work. Please enter either a) the official channel format shown above, or b) any any single video belonging to the channel and the plugin can retrieve the official channel format for you.', 'text_domain');

            $if_live_preview = false;

            $theytid = null;
            $theplaylistid = null;
            $final_title = '';
            $final_title_prefix = '';
            $thechannelid = null;
            $doing_live = false;
            $submit_type = null;
            $aspect_ratio = 9.0 / 16.0;

            if (isset($_POST['wizform_submit']))
            {
                check_admin_referer('_epyt_wiz', '_epyt_nonce');

                $submit_type = sanitize_text_field($_POST['wizform_submit']);
                if ($submit_type === 'step1_video')
                {
                    // validate
                    $search = sanitize_text_field(trim($_POST['txtUrl']));

                    try
                    {
                        if (empty($search))
                        {
                            throw new Exception();
                        }
                        else
                        {
                            // cleanup
                            $search = str_replace('/shorts/', '/watch?v=', $search);
                        }

                        if (preg_match(self::$justurlregex, $search))
                        {
                            //$search = esc_url($search);

                            try
                            {
                                $theytid = self::try_get_ytid($search);

                                if ($theytid == null)
                                {
                                    $form_valid = false;
                                    $step1_video_errors = $step1_video_error_invalid;
                                    $acc_expand = 'h3_video';
                                }
                                else
                                {

                                    $odata = self::get_oembed('http://youtube.com/watch?v=' . $theytid, 1920, 1280);
                                    if (is_object($odata))
                                    {
                                        $aspect_ratio = 1.0 * $odata->height / $odata->width;
                                        ?>

                                        <div id="step2_video" class="center">

                                            <h2>
                                                <?php
                                                if (isset($odata->title))
                                                {
                                                    $final_title = sanitize_text_field($odata->title);
                                                    $final_title_prefix = __('Video', 'text_domain');
                                                    echo sanitize_text_field($odata->title);
                                                }
                                                ?>
                                            </h2>
                                            <p class="center">
                                                <a class="ui-button ui-widget ui-corner-all inserttopost" rel="[embedyt] https://www.youtube.com/watch?v=<?php echo esc_attr($theytid) ?>[/embedyt]"><span class="ui-icon ui-icon-arrowthickstop-1-s"></span> <?php _e('Insert Into Editor', 'text_domain') ?></a>
                                                &nbsp;
                                                <a class="ui-button ui-widget ui-corner-all btn-customize-step"><span class="ui-icon ui-icon-wrench"></span> <?php _e('Customize', 'text_domain') ?></a>
                                            </p>
                                            &nbsp; <?php _e('Or Copy Code', 'text_domain') ?>:
                                            <span class="copycode">[embedyt] https://www.youtube.com/watch?v=<?php echo esc_attr($theytid) ?>[/embedyt]</span>
                                            <div class="clearboth" style="height: 10px;">
                                            </div>
                                            <div class="ep-wizard-preview-video-wrapper">
                                                <iframe src="https://www.youtube.com/embed/<?php echo esc_attr($theytid) ?>?rel=0" allowfullscreen="" frameborder="0"></iframe>
                                            </div>

                                        </div>
                                        <?php
                                    }
                                    else
                                    {
                                        $form_valid = false;
                                        $step1_video_errors = $step1_video_error_invalid;
                                        $acc_expand = 'h3_video';
                                    }
                                }
                            }
                            catch (Exception $ex)
                            {
                                $form_valid = false;
                                $step1_video_errors = $step1_video_error_invalid;
                                $acc_expand = 'h3_video';
                            }
                        }
                        else
                        {
                            $search_options = new stdClass();
                            $search_options->q = $search;
                            $search_options->pageToken = null;
                            ?>
                            <div id="step2_video_search" class="center">
                                <h2><?php _e('You searched for', 'text_domain') ?>: <em class="orange"><?php echo sanitize_text_field($search); ?></em> </h2>

                                <?php
                                $search_page = self::get_search_page($search_options);
                                echo $search_page->html;
                                ?>
                            </div>
                            <?php
                        }

                        // // if valid, set and display next step
                        // if not,form_valid = false and  set accordion expander and error messages
                    }
                    catch (Exception $ex)
                    {
                        $form_valid = false;
                        $step1_video_errors = $step1_video_error_invalid;
                        $acc_expand = 'h3_video';
                    }
                }
                else if ($submit_type === 'step1_playlist')
                {
                    $search = sanitize_text_field(trim($_POST['txtUrlPlaylist']));
                    try
                    {
                        if (empty($search))
                        {
                            throw new Exception();
                        }
                        if (preg_match(self::$justurlregex, $search))
                        {
                            try
                            {
                                $theytid = null;
                                try
                                {
                                    $theytid = self::try_get_ytid($search);
                                }
                                catch (Exception $ex)
                                {
                                    
                                }

                                $urlparams = explode('?', $search);
                                $qvars = array();
                                parse_str($urlparams[1], $qvars);
                                $theplaylistid = $qvars["list"];

                                $odata = self::get_oembed('https://youtube.com/playlist?list=' . $theplaylistid, 1920, 1280);

                                if (is_object($odata))
                                {
                                    $aspect_ratio = 1.0 * $odata->height / $odata->width;

                                    $rel = 'https://www.youtube.com/embed?listType=playlist&list=' . (esc_attr($theplaylistid) . (empty($theytid) ? '' : '&v=' . esc_attr($theytid)));
                                    ?>

                                    <div id="step2_playlist" class="center">

                                        <h2>
                                            <?php
                                            if (isset($odata->title))
                                            {
                                                $final_title = sanitize_text_field($odata->title);
                                                $final_title_prefix = __('Playlist', 'text_domain');
                                                echo __('Playlist', 'text_domain') . ': ' . sanitize_text_field($odata->title);
                                            }
                                            ?>
                                        </h2>
                                        <p class="center">
                                            <a class="ui-button ui-widget ui-corner-all inserttopost" rel="[embedyt] <?php echo $rel; ?>[/embedyt]"><span class="ui-icon ui-icon-arrowthickstop-1-s"></span> <?php _e('Insert as Playlist', 'text_domain') ?></a>
                                            &nbsp; <a class="ui-button ui-widget ui-corner-all inserttopost" rel="[embedyt] <?php echo $rel . '&layout=gallery'; ?>[/embedyt]"><span class="ui-icon ui-icon-arrowthickstop-1-s"></span> <?php _e('Insert as Gallery', 'text_domain'); ?></a>
                                            &nbsp; <a class="ui-button ui-widget ui-corner-all btn-customize-step"><span class="ui-icon ui-icon-wrench"></span> <?php _e('Customize', 'text_domain'); ?></a>
                                        </p>
                                        <p>
                                            <?php _e('Or Copy Code', 'text_domain'); ?>:
                                        </p>
                                        <p>
                                            <?php _e('Playlist Layout', 'text_domain'); ?>: <span class="copycode">[embedyt] <?php echo $rel; ?>[/embedyt]</span>
                                        </p>
                                        <p>
                                            <?php _e('Gallery Layout', 'text_domain'); ?>: <span class="copycode">[embedyt] <?php echo $rel . '&layout=gallery'; ?>[/embedyt]</span>
                                        </p>
                                        <div class="clearboth" style="height: 10px;">
                                        </div>
                                        <div class="ep-wizard-preview-video-wrapper">
                                            <iframe src="<?php echo $rel; ?>" allowfullscreen="" frameborder="0"></iframe>
                                        </div>
                                    </div>
                                    <?php
                                }
                                else
                                {
                                    $form_valid = false;
                                    $step1_playlist_errors = $step1_playlist_error_invalid;
                                    $acc_expand = 'h3_playlist';
                                }
                            }
                            catch (Exception $ex)
                            {
                                $form_valid = false;
                                $step1_playlist_errors = $step1_playlist_error_invalid;
                                $acc_expand = 'h3_playlist';
                            }
                        }
                    }
                    catch (Exception $ex)
                    {
                        $form_valid = false;
                        $step1_playlist_errors = $step1_playlist_error_invalid;
                        $acc_expand = 'h3_playlist';
                    }
                }
                else if ($submit_type === 'step1_channel')
                {
                    $search = sanitize_text_field(trim($_POST['txtUrlChannel']));
                    try
                    {
                        if (empty($search))
                        {
                            throw new Exception();
                        }
                        if (preg_match(self::$justurlregex, $search) || preg_match('@/channel/(.+)@', $search))
                        {
                            try
                            {
                                $thechannel = null;
                                if (preg_match(self::$justurlregex, $search))
                                {
                                    // single id
                                    $theytid = null;
                                    try
                                    {
                                        $theytid = self::try_get_ytid($search);
                                    }
                                    catch (Exception $ex)
                                    {
                                        
                                    }
                                    $chanvid = null;
                                    if ($theytid)
                                    {
                                        $chanvid = self::get_video_snippet($theytid);
                                    }
                                    if ($chanvid)
                                    {
                                        $thechannel = self::get_channel_snippet($chanvid->snippet->channelId);
                                    }
                                }
                                else
                                {
                                    // channel id
                                    $chanmatch = array();
                                    preg_match('@/channel/(.+)@', $search, $chanmatch);
                                    if (!empty($chanmatch))
                                    {
                                        $thechannel = self::get_channel_snippet($chanmatch[1]);
                                    }
                                }
                                if ($thechannel)
                                {
                                    $theplaylistid = $thechannel->contentDetails->relatedPlaylists->uploads;
                                    $rel = 'https://www.youtube.com/embed?listType=playlist&list=' . (esc_attr($theplaylistid));

                                    $final_title = sanitize_text_field($thechannel->snippet->title);
                                    $final_title_prefix = __('Channel', 'text_domain');
                                    ?>

                                    <div id="step2_channel" class="center">

                                        <h2>
                                            <?php
                                            echo __('Channel', 'text_domain') . ': ' . sanitize_text_field($thechannel->snippet->title);
                                            ?>
                                        </h2>
                                        <p class="center">
                                            <a class="ui-button ui-widget ui-corner-all inserttopost" rel="[embedyt] <?php echo $rel; ?>[/embedyt]"><span class="ui-icon ui-icon-arrowthickstop-1-s"></span> <?php _e('Insert as Playlist', 'text_domain'); ?></a>
                                            &nbsp; <a class="ui-button ui-widget ui-corner-all inserttopost" rel="[embedyt] <?php echo $rel . '&layout=gallery'; ?>[/embedyt]"><span class="ui-icon ui-icon-arrowthickstop-1-s"></span> <?php _e('Insert as Gallery', 'text_domain'); ?></a>
                                            &nbsp; <a class="ui-button ui-widget ui-corner-all btn-customize-step"><span class="ui-icon ui-icon-wrench"></span> <?php _e('Customize', 'text_domain'); ?></a>
                                        </p>
                                        <p>
                                            <?php _e('Or Copy Code', 'text_domain'); ?>:
                                        </p>
                                        <p>
                                            <?php _e('Playlist Layout', 'text_domain'); ?>: <span class="copycode">[embedyt] <?php echo $rel; ?>[/embedyt]</span>
                                        </p>
                                        <p>
                                            <?php _e('Gallery Layout', 'text_domain'); ?>: <span class="copycode">[embedyt] <?php echo $rel . '&layout=gallery'; ?>[/embedyt]</span>
                                        </p>
                                        <div class="clearboth" style="height: 10px;">
                                        </div>
                                        <div class="ep-wizard-preview-video-wrapper">
                                            <iframe src="<?php echo $rel; ?>" allowfullscreen="" frameborder="0"></iframe>
                                        </div>
                                    </div>
                                    <?php
                                }
                                else
                                {
                                    $form_valid = false;
                                    $step1_channel_errors = $step1_channel_error_invalid;
                                    $acc_expand = 'h3_channel';
                                }
                            }
                            catch (Exception $ex)
                            {
                                $form_valid = false;
                                $step1_channel_errors = $step1_channel_error_invalid;
                                $acc_expand = 'h3_channel';
                            }
                        }
                    }
                    catch (Exception $ex)
                    {
                        $form_valid = false;
                        $step1_channel_errors = $step1_channel_error_invalid;
                        $acc_expand = 'h3_channel';
                    }
                }
                else if ($submit_type === 'step1_livechannel')
                {
                    $search = sanitize_text_field(trim($_POST['txtUrlLiveChannel']));
                    try
                    {
                        if (empty($search))
                        {
                            throw new Exception();
                        }
                        if (preg_match(self::$justurlregex, $search) || preg_match('@/channel/(.+)@', $search))
                        {
                            try
                            {
                                $thechannelid = null;
                                if (preg_match(self::$justurlregex, $search))
                                {
                                    // single id
                                    $theytid = null;
                                    try
                                    {
                                        $theytid = self::try_get_ytid($search);
                                    }
                                    catch (Exception $ex)
                                    {
                                        
                                    }
                                    $chanvid = null;
                                    if ($theytid)
                                    {
                                        $chanvid = self::get_video_snippet($theytid);
                                    }
                                    if ($chanvid)
                                    {
                                        $thechannelid = $chanvid->snippet->channelId;
                                    }
                                }
                                else
                                {
                                    // channel id
                                    $chanmatch = array();
                                    preg_match('@/channel/(.+)@', $search, $chanmatch);
                                    if (!empty($chanmatch))
                                    {
                                        $thechannelid = $chanmatch[1];
                                    }
                                }
                                if (!empty($thechannelid))
                                {
                                    $rel = 'https://www.youtube.com/embed/live_stream?channel=' . (esc_attr($thechannelid));

                                    $final_title = esc_url('https://www.youtube.com/channel/' . $thechannelid);
                                    $final_title_prefix = __('Live stream from channel', 'text_domain');
                                    $doing_live = true;
                                    ?>

                                    <div id="step2_livechannel" class="center">
                                        <h2>
                                            <?php
                                            echo __('Live stream from channel', 'text_domain') . ': ' . esc_url('https://www.youtube.com/channel/' . $thechannelid);
                                            ?>
                                        </h2>
                                        <p class="center">
                                            <a class="ui-button ui-widget ui-corner-all inserttopost" rel="[embedyt] https://www.youtube.com/embed/live_stream?channel=<?php echo esc_attr($thechannelid) ?>[/embedyt]"><span class="ui-icon ui-icon-arrowthickstop-1-s"></span> <?php _e('Insert Into Editor', 'text_domain'); ?></a>
                                            &nbsp;
                                            <a class="ui-button ui-widget ui-corner-all btn-customize-step"><span class="ui-icon ui-icon-wrench"></span> <?php _e('Customize', 'text_domain'); ?></a>
                                        </p>
                                        &nbsp; <?php _e('Or Copy Code', 'text_domain'); ?>:
                                        <span class="copycode">[embedyt] https://www.youtube.com/embed/live_stream?channel=<?php echo esc_attr($thechannelid) ?>[/embedyt]</span>
                                        <div class="clearboth" style="height: 10px;">
                                        </div>
                                    </div>
                                    <?php
                                }
                                else
                                {
                                    $form_valid = false;
                                    $step1_livechannel_errors = $step1_livechannel_error_invalid;
                                    $acc_expand = 'h3_live';
                                }
                            }
                            catch (Exception $ex)
                            {
                                $form_valid = false;
                                $step1_livechannel_errors = $step1_livechannel_error_invalid;
                                $acc_expand = 'h3_live';
                            }
                        }
                        else
                        {
                            $form_valid = false;
                            $step1_livechannel_errors = $step1_livechannel_error_invalid;
                            $acc_expand = 'h3_live';
                        }
                    }
                    catch (Exception $ex)
                    {
                        $form_valid = false;
                        $step1_livechannel_errors = $step1_livechannel_error_invalid;
                        $acc_expand = 'h3_live';
                    }
                }
                else if ($submit_type === 'step1_live')
                {
                    $search = sanitize_text_field(trim($_POST['txtUrlLive']));
                    try
                    {
                        if (empty($search))
                        {
                            throw new Exception();
                        }

                        try
                        {
                            $theytid = null;
                            try
                            {
                                $theytid = self::try_get_ytid($search);
                            }
                            catch (Exception $ex)
                            {
                                
                            }
                            $live_attempt = self::get_video_snippet($theytid);
                            if ($live_attempt)
                            {
                                $if_live_preview = $live_attempt->id;
                                $final_title = sanitize_text_field($live_attempt->snippet->title);
                                $final_title_prefix = __('Live Stream', 'text_domain');
                            }
                            $rel = 'https://www.youtube.com/watch?v=' . (esc_attr($theytid)) . '&live=1';
                            $doing_live = true;
                            ?>
                            <div id="step2_live" class="center">

                                <h2>
                                    <?php
                                    echo __('Live Stream (or Premiere)', 'text_domain') . ': ' . sanitize_text_field($live_attempt->snippet->title);
                                    ?>
                                </h2>
                                <p class="center">
                                    <a class="ui-button ui-widget ui-corner-all inserttopost" rel="[embedyt] <?php echo $rel; ?>[/embedyt]"><span class="ui-icon ui-icon-arrowthickstop-1-s"></span> <?php _e('Insert Into Editor', 'text_domain'); ?></a>
                                    &nbsp; <a class="ui-button ui-widget ui-corner-all btn-customize-step"><span class="ui-icon ui-icon-wrench"></span> <?php _e('Customize', 'text_domain'); ?></a>
                                </p>
                                <p>
                                    <?php _e('Or Copy Code', 'text_domain'); ?>:
                                </p>
                                <p>
                                    <span class="copycode">[embedyt] <?php echo $rel; ?>[/embedyt]</span>
                                </p>
                                <div class="clearboth" style="height: 10px;">
                                </div>
                                <?php
                                if ($if_live_preview)
                                {
                                    ?>
                                    <div class="ep-wizard-preview-video-wrapper">
                                        <iframe src="https://www.youtube.com/embed/<?php echo esc_attr($if_live_preview) ?>?rel=0" allowfullscreen="" frameborder="0"></iframe>
                                    </div>
                                    <?php
                                }
                                ?>
                                <p>
                                    <?php _e('<strong>Is your live stream not working?</strong> Read more here: <a href="https://support.google.com/youtube/answer/2474026?hl=en" target="_blank">https://support.google.com/youtube/answer/2474026?hl=en</a>', 'text_domain'); ?>
                                </p>
                            </div>
                            <?php
                        }
                        catch (Exception $ex)
                        {
                            $form_valid = false;
                            $step1_live_errors = $step1_live_error_invalid;
                            $acc_expand = 'h3_live';
                        }
                    }
                    catch (Exception $ex)
                    {
                        $form_valid = false;
                        $step1_live_errors = $step1_live_error_invalid;
                        $acc_expand = 'h3_live';
                    }
                }
                else
                {
                    $form_valid = false;
                    $acc_expand = 'h3none';
                }
            }

            if (!isset($_POST['wizform_submit']) || ($form_valid === false))
            {
                if ($form_valid === false)
                {
                    ?>
                    <script type="text/javascript">
                        var _EPYTWIZ_ = _EPYTWIZ_ || {};
                        _EPYTWIZ_.acc_expand = '<?php echo sanitize_key($acc_expand) ?>';</script>
                    <?php
                }
                ?>

                <div class="wiz-accordion">
                    <h3 class="header-go"><a href="<?php echo admin_url('admin.php?page=youtube-my-preferences#jumpdefaults'); ?>"> <?php _e('Check my general YouTube embedding instructions and settings.', 'text_domain'); ?> </a></h3>
                    <div class="header-go-content"></div>
                    <h3 id="h3_video"> <a href="#"><?php _e('Embed a single video, or YouTube short.', 'text_domain'); ?></a></h3>
                    <div>
                        <h4 class="center"><?php _e('Single video directions', 'text_domain'); ?></h4>
                        <p>
                            <?php _e('Paste the url of a single video below (examples: <em>https://www.youtube.com/watch?v=YVvn8dpSAt0</em> or <em>https://www.youtube.com/shorts/J38Yq85ZoyY</em>)', 'text_domain'); ?>
                        </p>
                        <form name="wizform_video" method="post" action="" class="wizform" id="wizform_video">
                            <?php wp_nonce_field('_epyt_wiz', '_epyt_nonce', true); ?>
                            <div class="center txt-button-align">
                                <input name="txtUrl" maxlength="200" id="txtUrl" class="txturlpastecustom ui-widget ui-widget-content ui-corner-all" placeholder="<?php _e('Paste URL here', 'text_domain'); ?>" type="text"> <button name="wizform_submit" class="ui-button ui-widget ui-corner-all" type="submit" value="step1_video"><?php _e('Submit', 'text_domain'); ?></button>
                            </div>
                            <p class="badpaste orange bold" style="display: none;">
                                <?php _e('Please do not paste full embedcode above, only simple links to the YouTube video.
                                <br />
                                We have attempted to correct it above, but please doublecheck!', 'text_domain'); ?>
                            </p>
                        </form>
                        <?php echo $step1_video_errors ? '<p class="orange bold">' . $step1_video_errors . '</p>' : ''; ?>
                        <p><em><?php _e('Note: You can also search YouTube videos by title in the text box above (example: <em>TED talks</em>). However, searching will use a significant amount of your YouTube API quota.', 'text_domain'); ?></em></p>
                    </div>
                    <h3 id="h3_playlist"> <a href="#"><?php _e('Embed a playlist.', 'text_domain'); ?> </a></h3>
                    <div>
                        <h4 class="center"><?php _e('Playlist directions', 'text_domain'); ?></h4>
                        <div class="playlist-tabs">
                            <ul>
                                <li><a href="#ptabs-1"><?php _e('Self-contained layout directions', 'text_domain'); ?></a></li>
                                <li><a href="#ptabs-2"><?php _e('Gallery layout directions', 'text_domain'); ?></a></li>
                            </ul>
                            <div id="ptabs-1">
                                <img src="<?php echo plugins_url('/images/icon-playlist-self.jpg', __FILE__) ?>" class="icon-playlist" />
                                <ol>
                                    <li><?php _e('Go to the page for the playlist that lists all of its videos (<a href="https://www.youtube.com/playlist?list=PL70DEC2B0568B5469" target="_blank">Example &raquo;</a>).', 'text_domain'); ?></li>
                                    <li><?php _e('You may then click on the video that you want the playlist to start with (this step only applies to self-contained playlists. You cannot pick a starter for gallery layout directions).', 'text_domain'); ?></li>
                                    <li><?php _e('Copy the URL in your browser and paste it in the textbox below. You\'ll notice that a playlist URL contains the playlist ID (e.g. "PL...")', 'text_domain'); ?></li>
                                    <li><?php _e('Click "Get Playlist" to continue.', 'text_domain'); ?></li>
                                </ol>
                                <div class="clearboth">
                                </div>
                            </div>
                            <div id="ptabs-2">
                                <img src="<?php echo plugins_url('/images/icon-playlist-gallery.jpg', __FILE__) ?>" class="icon-playlist" />
                                <ol>
                                    <li><?php _e('Go to the page for the playlist that lists all of its videos (<a href="https://www.youtube.com/playlist?list=PL70DEC2B0568B5469" target="_blank">Example &raquo;</a>).', 'text_domain'); ?> </li>
                                    <li><?php _e('Copy the URL in your browser and paste it in the textbox below. You\'ll notice that a playlist URL contains the playlist ID (e.g. "PL...")', 'text_domain'); ?></li>
                                    <li><?php _e('Click "Get Playlist" to continue.', 'text_domain'); ?></li>
                                </ol>
                                <div class="clearboth">
                                </div>
                            </div>
                        </div>

                        <form name="wizform_playlist" method="post" action="" class="wizform" id="wizform_playlist">
                            <?php wp_nonce_field('_epyt_wiz', '_epyt_nonce', true); ?>
                            <div class="center txt-button-align">
                                <input name="txtUrlPlaylist" maxlength="200" id="txtUrlPlaylist" class="txturlpastecustom ui-widget ui-widget-content ui-corner-all" placeholder="Paste the playlist link here" type="text">
                                <button name="wizform_submit" class="ui-button ui-widget ui-corner-all" type="submit" value="step1_playlist"><?php _e('Get Playlist', 'text_domain'); ?></button>
                            </div>
                        </form>
                        <?php echo $step1_playlist_errors ? '<p class="orange bold">' . $step1_playlist_errors . '</p>' : ''; ?>
                    </div>
                    <h3 id="h3_channel"> <a href="#"><?php _e('Embed a channel.', 'text_domain'); ?>  </a></h3>
                    <div>
                        <h4 class="center"><?php _e('Channel directions', 'text_domain'); ?></h4>
                        <?php
                        if (!self::has_api_key())
                        {
                            echo str_replace('###', __('"search for channel"', 'text_domain'), self::$get_api_key_msg);
                        }
                        else
                        {
                            ?>
                            <p>
                                <?php _e('If you already know the direct link to the channel ID, enter it below. <br>Example: https://www.youtube.com<strong>/channel/</strong>UCnM5iMGiKsZg-iOlIO2ZkdQ <p class="error-channel-format smallnote">Note: the following format will not work:  https://www.youtube.com<strong>/c/</strong>customchannelname  If you cannot locate the proper channel ID format above, then try the other method below.</p> ', 'text_domain'); ?>
                            </p>
                            <p>
                                <?php _e('Or, simply enter a link to any single video that belongs to the user\'s channel, and the plugin will find the channel for you.<br>Example: https://www.youtube.com/watch?v=YVvn8dpSAt0', 'text_domain'); ?>
                            </p>
                            <form name="wizform_channel" method="post" action="" class="wizform" id="wizform_channel">
                                <?php wp_nonce_field('_epyt_wiz', '_epyt_nonce', true); ?>
                                <div class="center txt-button-align">
                                    <input name="txtUrlChannel" maxlength="200" id="txtUrlChannel" class="txturlpastecustom ui-widget ui-widget-content ui-corner-all" placeholder="<?php _e('Paste YouTube link here', 'text_domain'); ?>" type="text"> <button name="wizform_submit" class="ui-button ui-widget ui-corner-all" type="submit" value="step1_channel"><?php _e('Get Channel', 'text_domain'); ?></button>
                                </div>
                                <p class="badpaste orange bold" style="display: none;">
                                    <?php _e('Please do not paste full embedcode above, only simple links to the YouTube video.
                                    <br />
                                    We have attempted to correct it above, but please doublecheck!', 'text_domain'); ?>
                                </p>
                            </form>
                            <?php echo $step1_channel_errors ? '<p class="orange bold">' . $step1_channel_errors . '</p>' : ''; ?>
                            <?php
                        }
                        ?>
                    </div>
                    <h3 id="h3_live"> <a href="#"><?php _e('Embed a live stream or premiere video.', 'text_domain'); ?> </a></h3>
                    <div>
                        <h4 class="center"><?php _e('Live stream or premiere directions', 'text_domain'); ?></h4>
                        <?php
                        if (!self::has_api_key())
                        {
                            echo str_replace('###', __('live stream', 'text_domain'), self::$get_api_key_msg);
                        }
                        else
                        {
                            ?>
                            <div class="livestream-tabs">
                                <ul>
                                    <li><a href="#livestream-tabs-2"><?php _e('Channel-based livestream (recommended)', 'text_domain'); ?><sup class="orange"><?php _e('new', 'text_domain'); ?></sup></a></li>
                                    <li><a href="#livestream-tabs-1"><?php _e('Direct link to livestream or premiere video', 'text_domain'); ?></a></li>
                                </ul>
                                <div id="livestream-tabs-1">
                                    <p>
                                        <?php _e('This will embed a specific live stream or premiere video.', 'text_domain'); ?>
                                    </p>
                                    <ol>
                                        <li>
                                            <?php _e('Paste in the direct URL of the live stream or premiere below and click Submit. <br> Example', 'text_domain'); ?>: https://www.youtube.com/watch?v=<strong>5qap5aO4i9A</strong>
                                        </li>
                                        <li>
                                            <?php _e(' On the next screen, customize or insert your video.', 'text_domain'); ?>
                                        </li>
                                    </ol>
                                    <form name="wizform_live" method="post" action="" class="wizform" id="wizform_live">
                                        <?php wp_nonce_field('_epyt_wiz', '_epyt_nonce', true); ?>
                                        <div class="center txt-button-align">
                                            <input name="txtUrlLive" maxlength="200" id="txtUrlLive" class="ui-widget ui-widget-content ui-corner-all" placeholder="<?php _e('Paste YouTube link here', 'text_domain'); ?>" type="text"> <button name="wizform_submit" class="ui-button ui-widget ui-corner-all" type="submit" value="step1_live"><?php _e('Submit', 'text_domain'); ?></button>
                                        </div>
                                    </form>
                                    <?php echo $step1_live_errors ? '<p class="orange bold">' . $step1_live_errors . '</p>' : ''; ?>
                                </div>
                                <div id="livestream-tabs-2">
                                    <p>
                                        <?php _e('This will embed a video that will automatically display the next upcoming live stream from a channel. You have 2 choices:', 'text_domain'); ?>
                                    </p>
                                    <ol>
                                        <li>
                                            <?php _e('Enter the link to the official channel page below (note the word "channel" should be in the link, with a long ID afterward).<br>Example', 'text_domain'); ?>: https://www.youtube.com/<strong>channel</strong>/UCL0iAkpqV5YaIVG7xkDtS4Q
                                        </li>
                                        <li>
                                            <?php _e('Or, if you do not know where to find the above format, you can enter any single video that is owned by the channel, and the plugin can retrieve the above format for you.<br>Example', 'text_domain'); ?>: https://www.youtube.com/watch?<strong>v=3tXY8YSIxHM</strong>
                                        </li>
                                    </ol>
                                    <form name="wizform_livechannel" method="post" action="" class="wizform" id="wizform_livechannel">
                                        <?php wp_nonce_field('_epyt_wiz', '_epyt_nonce', true); ?>
                                        <div class="center txt-button-align">
                                            <input name="txtUrlLiveChannel" maxlength="200" id="txtUrlLiveChannel" class="ui-widget ui-widget-content ui-corner-all" placeholder="<?php _e('Paste channel link here', 'text_domain'); ?>" type="text"> <button name="wizform_submit" class="ui-button ui-widget ui-corner-all" type="submit" value="step1_livechannel"><?php _e('Get Channel', 'text_domain'); ?></button>
                                        </div>                                        
                                    </form>
                                    <?php echo $step1_livechannel_errors ? '<p class="orange bold">' . $step1_livechannel_errors . '</p>' : ''; ?>
                                    <?php echo self::livestream_advice(true); ?>
                                </div>
                            </div>

                            <?php
                        }
                        ?>
                    </div>
                    <?php
                    if (false && current_user_can('manage_options') && !self::vi_logged_in() && !(bool) (self::$alloptions[self::$opt_vi_hide_monetize_tab]))
                    {
                        ?>
                        <h3 id="h3_vi_monetize"> <a href="#"> Earn money embedding videos. </a></h3>
                        <div class="h3_vi_monetize-content">
                            <div class="vi-registration-box">
                                <?php
                                include_once(EPYTVI_INCLUDES_PATH . 'vi_registration_form.php');
                                include_once(EPYTVI_INCLUDES_PATH . 'vi_login_success.php');
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <h3 class="header-go"> <a href="<?php echo admin_url('admin.php?page=youtube-ep-analytics-dashboard'); ?>"><?php _e('Check my performance, blocked countries, deleted videos, etc.', 'text_domain'); ?> </a></h3>
                    <div class="header-go-content"></div>

                </div>
                <?php
            }
            ?>
        </div>
        <?php
        $wiz_defaults = self::$alloptions;

        unset($wiz_defaults[self::$opt_pro]);
        unset($wiz_defaults[self::$opt_apikey]);
        unset($wiz_defaults[self::$opt_dashpre]);
        unset($wiz_defaults[self::$opt_restrict_wizard]);
        unset($wiz_defaults[self::$opt_restrict_wizard_roles]);
        unset($wiz_defaults[self::$opt_gallery_channelsublink]);
        unset($wiz_defaults[self::$opt_gallery_channelsubtext]);
        unset($wiz_defaults[self::$opt_not_live_content]);
        unset($wiz_defaults[self::$opt_not_live_on]);


        $wiz_defaults['theytid'] = $theytid;
        $wiz_defaults['theplaylistid'] = $theplaylistid;
        $wiz_defaults['width'] = intval(self::get_blogwidth());
        $wiz_defaults['selheight'] = 'best-fit';
        $wiz_defaults['aspect_ratio'] = $aspect_ratio;
        $wiz_defaults['final_title'] = $final_title;
        $wiz_defaults['final_title_prefix'] = $final_title_prefix;
        $wiz_defaults['thechannelid'] = $thechannelid;
        $wiz_defaults['doing_live'] = $doing_live;
        $wiz_defaults['submit_type'] = $submit_type;
        $wiz_defaults['if_live_preview'] = $if_live_preview;

        //$wiz_defaults['duration'] = (object) array("hours" => 0, "mins" => 6, "secs" => 1);
        ?>

        <div class="wrap" id="ep-wizard-customizer" ng-app="YouTubeWizardApp"> 
            <div ng-controller="YouTubeWizardController">
                <div class="container-step-customize">
                    <a class="ui-button ui-widget ui-corner-all btn-get-code-step btn-epwiz-step" ng-click="getCodeStep()"><span class="ui-icon ui-icon-check"></span> <?php _e('Get Code', 'text_domain'); ?></a>
                    <h1><?php _e('YouTube Customizer', 'text_domain'); ?></h1> <!--carat-2-e-w-->
                    <div class="clearboth"></div>
                    <div class="wizoptionbox">
                        <div id="wizoptionboxLeft">
                            <div id="ytpreviewbox">
                                <div class="ep-wizard-preview-video-wrapper">
                                    <iframe ng-src="{{iframePreviewUrl()}}" id="ifPreview" title="<?php _e('YouTube video player', 'text_domain'); ?>" frameborder="0" allowfullscreen=""></iframe>
                                </div>
                            </div>
                        </div>
                        <div id="wizoptionboxRight">

                            <h2><?php _e('Size Ratio', 'text_domain'); ?></h2>
                            <p class="smallnote"><?php _e('We\'ve guessed your site\'s default content width below.', 'text_domain'); ?></p>                        
                            <label class="label-inline"><?php _e('Width', 'text_domain'); ?>:
                                <input name="txtWidth" ng-change="selectHeight()" ng-model="model.width" type="number" min="200" id="txtWidth" class="txtbox" required />
                            </label>
                            <label class="label-inline"><?php _e('Height', 'text_domain'); ?>:
                                <select id="selheight" ng-change="selectHeight()" ng-model="model.selheight">
                                    <option value="manual"><?php _e('Manual', 'text_domain'); ?> →</option>
                                    <option value="best-fit"><?php _e('Calculate Best-Fit', 'text_domain'); ?></option>
                                    <option value="16-9"><?php _e('Force 16:9 Ratio', 'text_domain'); ?></option>
                                    <option value="4-3"><?php _e('Force 4:3 Ratio', 'text_domain'); ?></option>
                                </select>
                                <input name="txtHeight" ng-model="model.height" type="number" id="txtHeight" class="txtbox" min="200" required ng-disabled="model.selheight !== 'manual'" />
                            </label>
                            <p class="smallnote"><?php _e('If you need the above ratio to be fixed pixels instead of fluid, try turning off "Responsive video sizing" in the plugin settings page.', 'text_domain'); ?></p>

                            <div id="panPlaylistStart" ng-if="model.theplaylistid">
                                <h2><?php _e('Playlist &amp; Gallery Options', 'text_domain'); ?></h2>
                                <div id="panPlaylistLayout">
                                    <?php _e('First select your playlist layout (normal or gallery)', 'text_domain'); ?>:
                                    <select ng-model="model.gallery_style">
                                        <option value=""><?php _e('Normal (self-contained)', 'text_domain'); ?></option>
                                        <option value="grid"><?php _e('Gallery - Grid', 'text_domain'); ?></option>
                                        <option value="listview"><?php _e('Gallery - Vertical List', 'text_domain'); ?></option>
                                        <option value="carousel"><?php _e('Gallery - Horizontal Slider', 'text_domain'); ?></option>
                                    </select>
                                </div>
                                <div ng-if="model.gallery_style == ''">
                                    <p>
                                        <?php _e('Select which video your <strong>self-contained</strong> playlist should start with (this does not apply to galleries)', 'text_domain'); ?>:
                                    </p>
                                    <label><input type="radio" ng-model="model.rblPlaylistStart" value="0"><?php _e('Automatic (latest added)', 'text_domain'); ?></label>
                                    <label><input type="radio" ng-model="model.rblPlaylistStart" value="1"><?php _e('Currently selected video', 'text_domain'); ?></label>
                                </div>
                                <div ng-if="model.gallery_style != ''" id="panGallerySettings" class="panGallerySettings">
                                    <p>
                                        <?php _e('Player display mode', 'text_domain'); ?>:
                                        <label class="label-inline"><input type="radio" ng-model="model.gallery_disptype" value="default"><?php _e('Normal', 'text_domain'); ?></label>
                                        <label class="label-inline"><input type="radio" ng-model="model.gallery_disptype" value="lb"><?php _e('Popup', 'text_domain'); ?></label>
                                    </p>
                                    <p ng-if="model.gallery_hidethumbimg != 1">
                                        <?php _e('Thumbnail shape', 'text_domain'); ?>:
                                        <label class="label-inline"><input type="radio" ng-model="model.gallery_thumbcrop" value="box"><?php _e('Rectangle', 'text_domain'); ?></label>
                                        <label class="label-inline"><input type="radio" ng-model="model.gallery_thumbcrop" value="portal"><?php _e('Circular', 'text_domain'); ?></label>
                                    </p>

                                    <label ng-if="['grid', 'listview', 'carousel'].indexOf(model.gallery_style) >= 0"><input type="checkbox" ng-click="gallery_showtitleChange()" <?php checked($wiz_defaults[self::$opt_gallery_showtitle], 1); ?>><?php _e('Show gallery thumbnail titles', 'text_domain'); ?></label>
                                    <label ng-if="['grid', 'listview', 'carousel'].indexOf(model.gallery_style) >= 0"><input type="checkbox" ng-click="gallery_showpagingChange()" <?php checked($wiz_defaults[self::$opt_gallery_showpaging], 1); ?>><?php _e('Show pagination and Next/Previous buttons', 'text_domain'); ?></label>
                                    <label ng-if="['grid', 'listview', 'carousel'].indexOf(model.gallery_style) >= 0"><input type="checkbox" ng-click="gallery_autonextChange()" <?php checked($wiz_defaults[self::$opt_gallery_autonext], 1); ?>><?php _e('Automatically play next video', 'text_domain'); ?></label>
                                    <label ng-if="['listview'].indexOf(model.gallery_style) >= 0"><input type="checkbox" ng-click="gallery_showdscChange()" <?php checked($wiz_defaults[self::$opt_gallery_showdsc], 1); ?>><?php _e('Show gallery thumbnail descriptions', 'text_domain'); ?></label>
                                    <label ng-if="['grid', 'listview'].indexOf(model.gallery_style) >= 0"><input type="checkbox" ng-click="gallery_hidethumbimgChange()" <?php checked($wiz_defaults[self::$opt_gallery_hidethumbimg], 1); ?>><?php _e('Hide thumbnail images', 'text_domain'); ?></label>
                                    <label ng-if="!!model.gallery_style"><input type="checkbox" ng-click="gallery_channelsubChange()" <?php checked($wiz_defaults[self::$opt_gallery_channelsub], 1); ?>><?php _e('Show subscribe button', 'text_domain'); ?></label>
                                    <div ng-if="['grid', 'listview', 'carousel'].indexOf(model.gallery_style) >= 0" id="panGalleryPagesize" class="boxGalleryPagesize">
                                        <?php _e('Thumbnails per page', 'text_domain'); ?>:
                                        <select ng-model="model.gallery_pagesize" convert-to-number>
                                            <?php
                                            for ($i = 1; $i <= 50; $i++)
                                            {
                                                ?>
                                                <option value="<?php echo $i ?>"><?php echo $i == intval($wiz_defaults[self::$opt_gallery_pagesize]) ? $i . __(' (Default)', 'text_domain') : $i ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div ng-if="['grid'].indexOf(model.gallery_style) >= 0" id="panGalleryColumns" class="boxGalleryColumns">
                                        <?php _e('Number of columns', 'text_domain'); ?>:
                                        <select ng-model="model.gallery_columns" convert-to-number>
                                            <?php
                                            for ($i = 1; $i <= 30; $i++)
                                            {
                                                ?>
                                                <option value="<?php echo $i ?>"><?php echo $i == intval($wiz_defaults[self::$opt_gallery_columns]) ? $i . __(' (Default)', 'text_domain') : $i ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div ng-if="model.doing_live" id="panLiveOptions">
                                <h2><?php _e('Livestream/Premiere Options', 'text_domain') ?></h2>
                                <label><input ng-click="live_chatChange()" <?php checked($wiz_defaults[self::$opt_live_chat], 1); ?> id="chklive_chat" type="checkbox" name="chklive_chat"><?php _e('Add live chat', 'text_domain') ?></label>
                            </div>
                            <h2><?php _e('Other Options', 'text_domain'); ?></h2>
                            <p class="smallnote"><?php _e('Your site\'s current defaults have been pre-checked for this video.', 'text_domain'); ?></p>
                            <div class="optionlist">
                                <label><input ng-click="centervidChange()" <?php checked($wiz_defaults[self::$opt_center], 1); ?> id="chkCenter" type="checkbox" name="chkCenter"><?php _e('Center-align the video', 'text_domain'); ?></label>
                                <label class="opt-wizhastip">
                                    <input ng-click="autoplayChange()" <?php checked($wiz_defaults[self::$opt_autoplay], 1); ?> id="chkAutoplay" type="checkbox" name="chkAutoplay"><?php _e('Autoplay the video', 'text_domain'); ?>
                                    <span class="ui-icon ui-icon-info" ng-if="model.autoplay == 1"></span>
                                    <span class="tip" ng-if="model.autoplay == 1">
                                        <?php _e('<strong>Note about autoplay:</strong> Desktop browsers like <a href="https://developers.google.com/youtube/iframe_api_reference#Mobile_considerations" target="_blank">Chrome and Safari are moving towards preventing autoplay for any video</a>. But, your chances are improved if you set your videos to initially start muted.', 'text_domain'); ?>
                                    </span>
                                </label>
                                <label><input ng-click="cc_load_policyChange()" <?php checked($wiz_defaults[self::$opt_cc_load_policy], 1); ?> id="chkCC" type="checkbox" name="chkCC"><?php _e('Turn on closed-captioning', 'text_domain'); ?></label>
                                <label><input ng-click="iv_load_policyChange()" <?php checked($wiz_defaults[self::$opt_iv_load_policy], 1); ?> id="chkAnn" type="checkbox" name="chkAnn"><?php _e('Show Annotations', 'text_domain'); ?></label>
                                <label><input ng-click="loopChange()" <?php checked($wiz_defaults[self::$opt_loop], 1); ?> id="chkLoop" type="checkbox" name="chkLoop"><?php _e('Loop the video', 'text_domain'); ?></label>
                                <label><input ng-click="modestbrandingChange()" <?php checked($wiz_defaults[self::$opt_modestbranding], 1); ?> id="chkModest" type="checkbox" name="chkModest"><?php _e('Modest branding: Hide YouTube logo while playing', 'text_domain'); ?></label>
                                <p>
                                    <?php _e('Show related videos after playing', 'text_domain'); ?>:
                                    <label><input type="radio" ng-model="model.rel" value="1" convert-to-number><?php _e('Show related videos', 'text_domain'); ?></label>
                                    <label><input type="radio" ng-model="model.rel" value="0" convert-to-number><?php _e('Show related only from the video\'s channel', 'text_domain'); ?></label>
                                    <label><input type="radio" ng-model="model.rel" value="-1" convert-to-number><?php _e('Hide related videos after playback', 'text_domain'); ?></label>
                                </p>
                                <label><input ng-click="fsChange()" <?php checked($wiz_defaults[self::$opt_fs], 1); ?> id="chkFs" type="checkbox" name="chkFs"><?php _e('Show fullscreen button', 'text_domain'); ?></label>
                                <label><input ng-click="controlsChange()" <?php checked($wiz_defaults[self::$opt_controls], 1); ?> id="chkControls" type="checkbox" name="chkControls"><?php _e('Show controls (uncheck to remove controls)', 'text_domain'); ?></label>
                                <label><input ng-click="defaultvolChange()" <?php checked($wiz_defaults[self::$opt_defaultvol], 1); ?> id="chkVolume" type="checkbox" name="chkVolume"><?php _e('Set an initial volume level (uncheck to use visitor\'s default)', 'text_domain'); ?></label>
                                <div id="boxdefaultvol" ng-if="model.defaultvol == 1">
                                    <?php _e('Volume', 'text_domain'); ?>: <span class="vol-output" ng-if="canInputRange()">{{model.vol == 0 ? '<?php _e('Mute', 'text_domain'); ?>' : model.vol + '%'}}</span>
                                    <input name="vol" type="{{canInputRange() ? 'range' : 'text'}}" id="vol" min="0" max="100" step="1" ng-model="model.vol" class="vol-range">
                                </div>
                            </div>

                            <div ng-if="!model.theplaylistid && !model.doing_live" id="panStartStop">
                                <h2><?php _e('Start / Stop Time', 'text_domain'); ?></h2>
                                <p>
                                    <?php _e('Optionally select a time to <strong>START</strong> when play is first clicked', 'text_domain'); ?>:
                                </p>
                                <div class="start-stop-box">
                                    <input type="number" ng-model="model.startHours" placeholder="hours" class="timepicker-box" step="1" min="0" max="11">
                                    :
                                    <input type="number" ng-model="model.startMins" placeholder="mins" class="timepicker-box" step="1" min="0" max="59">
                                    :
                                    <input type="number" ng-model="model.startSecs" placeholder="secs" class="timepicker-box" step="1" min="0" max="59">
                                </div>
                                <p>
                                    <label for="chkStop"><input ng-model="model.alsoStop" id="chkStop" type="checkbox" name="chkStop"><?php _e('Also <strong>STOP</strong> the video at a specific time?', 'text_domain'); ?></label>
                                </p>
                                <div ng-class="model.alsoStop ? 'start-stop-box' : 'start-stop-box greyout'" id="stopbox">
                                    <input type="number" ng-model="model.stopHours" placeholder="hours" class="timepicker-box" step="1" min="0" max="11" ng-disabled="!model.alsoStop" >
                                    :
                                    <input type="number" ng-model="model.stopMins" placeholder="mins" class="timepicker-box" step="1" min="0" max="59" ng-disabled="!model.alsoStop" >
                                    :
                                    <input type="number" ng-model="model.stopSecs" placeholder="secs" class="timepicker-box" step="1" min="0" max="59" ng-disabled="!model.alsoStop" >
                                </div>
                                <p class="smallnote bold" ng-if="model.startHours || model.startMins || model.startSecs || model.stopHours || model.stopMins || model.stopSecs">
                                    <?php _e('Start', 'text_domain'); ?>: {{ timeColons(model.startHours, model.startMins, model.startSecs)}} 
                                    <span ng-if="model.alsoStop">- <?php _e('End', 'text_domain'); ?>: {{ timeColons(model.stopHours, model.stopMins, model.stopSecs)}}</span>
                                </p>
                            </div>
        <!--                                    <pre>{{ model | json }}</pre>-->
                            <script type="text/javascript">
                                        (function ($)
                                        {
                                            window._EPYTWIZ_ = window._EPYTWIZ_ || {};
                                            window._EPYTWIZ_.model = <?php echo json_encode($wiz_defaults); ?>;
                                            window._EPYTWIZ_.myytdefaults = <?php echo json_encode($wiz_defaults); ?>;
                                        })(jQuery);
                            </script>
                        </div>
                        <div class="clearboth">
                        </div>
                    </div>
                    <a class="ui-button ui-widget ui-corner-all btn-get-code-step btn-epwiz-step" ng-click="getCodeStep()"><span class="ui-icon ui-icon-check"></span> <?php _e('Get Code', 'text_domain'); ?></a>
                    <div class="clearboth"></div>

                </div>
                <div class="container-step-get-code">
                    <a class="ui-button ui-widget ui-corner-all btn-re-edit-step btn-epwiz-step" ng-click="editStep()"><span class="ui-icon ui-icon-arrowreturnthick-1-w"></span> <?php _e('Re-edit Options', 'text_domain'); ?></a>
                    <h1><?php _e('Insert WordPress Embed Code', 'text_domain'); ?></h1>
                    <div class="clearboth"></div>
                    <h2>{{finalTitle()}}</h2>
                    <div class="wizoptionbox">
                        <p>
                            <?php _e('Click on "Insert" to insert the code into your WordPress editor. Or, click to select the embed code. Then copy and paste it into your editor. If the below code is really long, it may appear as if it takes up 2 or 3 lines after you paste it, but it should still work:', 'text_domain'); ?>
                        </p>
                        <p>
                            <textarea class="getcodebox" ng-click="selectAllText($event)">{{embedCode()}}</textarea>
                        </p>
                        <p>
                            <a class="ui-button ui-widget ui-corner-all" ng-click="insertFinalCode()"><span class="ui-icon ui-icon-arrowthickstop-1-s"></span> <?php _e('Insert Into Editor', 'text_domain'); ?></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public static function has_api_key()
    {
        if (isset(self::$alloptions[self::$opt_apikey]) && strlen(trim(self::$alloptions[self::$opt_apikey])) > 0)
        {
            return true;
        }

        return false;
    }

    public static function get_live_snippet($channel)
    {
        $apiEndpoint = 'https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&maxResults=1&type=video&eventType=live&safeSearch=none&videoEmbeddable=true&key=' . self::$alloptions[self::$opt_apikey]
                . '&channelId=' . urlencode($channel);
        $apiResult = wp_remote_get($apiEndpoint, array('timeout' => self::$curltimeout, 'headers' => array('referer' => site_url())));

        if (is_wp_error($apiResult))
        {
            return false;
        }

        $jsonResult = json_decode($apiResult['body']);

        if (isset($jsonResult->error))
        {
            return false;
        }

        if (isset($jsonResult->items) && $jsonResult->items != null && is_array($jsonResult->items) && count($jsonResult->items))
        {
            return $jsonResult->items[0];
        }

        return false;
    }

    public static function get_video_snippet($vid)
    {
        $apiEndpoint = 'https://www.googleapis.com/youtube/v3/videos?part=snippet&maxResults=1&key=' . self::$alloptions[self::$opt_apikey]
                . '&id=' . urlencode($vid);
        $apiResult = wp_remote_get($apiEndpoint, array('timeout' => self::$curltimeout, 'headers' => array('referer' => site_url())));

        if (is_wp_error($apiResult))
        {
            return false;
        }

        $jsonResult = json_decode($apiResult['body']);

        if (isset($jsonResult->error))
        {
            return false;
        }

        if (isset($jsonResult->items) && $jsonResult->items != null && is_array($jsonResult->items) && count($jsonResult->items))
        {
            return $jsonResult->items[0];
        }

        return false;
    }

    public static function get_channel_snippet($channid)
    {
        $apiEndpoint = 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails,snippet&key=' . self::$alloptions[self::$opt_apikey]
                . '&id=' . urlencode($channid);
        $apiResult = wp_remote_get($apiEndpoint, array('timeout' => self::$curltimeout, 'headers' => array('referer' => site_url())));

        if (is_wp_error($apiResult))
        {
            return false;
        }

        $jsonResult = json_decode($apiResult['body']);

        if (isset($jsonResult->error))
        {
            return false;
        }

        if (isset($jsonResult->items) && $jsonResult->items != null && is_array($jsonResult->items) && count($jsonResult->items))
        {
            return $jsonResult->items[0];
        }

        return false;
    }

    public static function clean_api_error($raw_message)
    {
        return htmlspecialchars(strip_tags(preg_replace('@&key=[^& ]+@i', '&key=*******', $raw_message)), ENT_QUOTES, 'UTF-8');
    }

    public static function clean_api_error_html($raw_message, $add_boilerplate)
    {
        $clean_html = '<div>' . __('Sorry, there was a YouTube error.', 'text_domain') . '</div>';
        if (current_user_can('manage_options'))
        {
            $clean_html = '<div>' . __('Sorry, there was a YouTube API error:', 'text_domain') . ' <em>' . self::clean_api_error($raw_message) . '</em>' .
                    ( $add_boilerplate ? self::$boilerplate_api_error_message : '' ) .
                    '</div>';
        }
        return $clean_html;
    }

    public static function get_search_page($options)
    {
        $gallobj = new stdClass();
        $pageSize = 30;

        if (!self::has_api_key())
        {
            $gallobj->html = '<div>' . str_replace('###', __('search', 'text_domain'), self::$get_api_key_msg) . '</div>';
            return $gallobj;
        }

        $apiEndpoint = 'https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=' . $pageSize . '&type=video&safeSearch=none&videoEmbeddable=true&key=' . self::$alloptions[self::$opt_apikey]
                . '&q=' . urlencode($options->q);
        if (!empty($options->pageToken))
        {
            $apiEndpoint .= '&pageToken=' . $options->pageToken;
        }

        $code = '';
        $apiResult = wp_remote_get($apiEndpoint, array('timeout' => self::$curltimeout, 'headers' => array('referer' => site_url())));

        if (is_wp_error($apiResult))
        {
            $gallobj->html = self::clean_api_error_html($apiResult->get_error_message(), true);
            return $gallobj;
        }

        $jsonResult = json_decode($apiResult['body']);

        if (isset($jsonResult->error))
        {
            if (isset($jsonResult->error->message))
            {
                $gallobj->html = self::clean_api_error_html($jsonResult->error->message, true);
                return $gallobj;
            }
            $gallobj->html = '<div>' . __('Sorry, there may be an issue with your YouTube API key.', 'text_domain') . ' ' . self::$boilerplate_api_error_message . '</div>';
            return $gallobj;
        }

        $totalResults = $jsonResult->pageInfo->totalResults;

        $nextPageToken = '';
        $prevPageToken = '';
        if (isset($jsonResult->nextPageToken))
        {
            $nextPageToken = $jsonResult->nextPageToken;
        }

        if (isset($jsonResult->prevPageToken))
        {
            $prevPageToken = $jsonResult->prevPageToken;
        }

        $cnt = 0;

        $code .= '<div class="epyt-search-results">';

        if (isset($jsonResult->items) && $jsonResult->items != null && is_array($jsonResult->items))
        {
            foreach ($jsonResult->items as $item)
            {

                $thumb = new stdClass();

                $thumb->id = isset($item->snippet->resourceId->videoId) ? $item->snippet->resourceId->videoId : null;
                $thumb->id = $thumb->id ? $thumb->id : (isset($item->id->videoId) ? $item->id->videoId : null);

                if ($thumb->id)
                {
                    $thumb->title = $item->snippet->title;

                    if (isset($item->snippet->thumbnails->high->url))
                    {
                        $thumb->img = $item->snippet->thumbnails->high->url;
                        $thumb->quality = 'high';
                    }
                    elseif (isset($item->snippet->thumbnails->default->url))
                    {
                        $thumb->img = $item->snippet->thumbnails->default->url;
                        $thumb->quality = 'default';
                    }
                    elseif (isset($item->snippet->thumbnails->medium->url))
                    {
                        $thumb->img = $item->snippet->thumbnails->medium->url;
                        $thumb->quality = 'medium';
                    }
                    else
                    {
                        $thumb->img = plugins_url('/images/deleted-video-thumb.png', __FILE__);
                        $thumb->quality = 'medium';
                    }

                    $code .= self::get_search_result_html($thumb, $options);
                    $cnt++;
                    $code .= '<div class="clear-both"></div>';
                }
            }
        }

        $code .= '<div class="clear-both"></div></div>';

        $totalPages = ceil($totalResults / $pageSize);
        $pagination = '<div class="epyt-pagination">';

        $txtprev = self::$alloptions[self::$opt_gallery_customarrows] ? self::$alloptions[self::$opt_gallery_customprev] : __('Prev', 'text_domain');
        $pagination .= '<div tabindex="0" role="button" class="epyt-pagebutton epyt-prev ' . (empty($prevPageToken) ? ' hide ' : '') . '" data-q="' . esc_attr($options->q)
                . '" data-pagetoken="' . esc_attr($prevPageToken)
                . '"><div class="epyt-arrow">&laquo;</div> <div>' . $txtprev . '</div></div>';


        $pagination .= '<div class="epyt-pagenumbers ' . ($totalPages > 1 ? '' : 'hide') . '">';
        $pagination .= '<div class="epyt-current">1</div><div class="epyt-pageseparator"> / </div><div class="epyt-totalpages">' . $totalPages . '</div>';
        $pagination .= '</div>';

        $txtnext = self::$alloptions[self::$opt_gallery_customarrows] ? self::$alloptions[self::$opt_gallery_customnext] : __('Next', 'text_domain');
        $pagination .= '<div tabindex="0" role="button" class="epyt-pagebutton epyt-next' . (empty($nextPageToken) ? ' hide ' : '') . '" data-q="' . esc_attr($options->q)
                . '" data-pagetoken="' . esc_attr($nextPageToken)
                . '"><div>' . $txtnext . '</div> <div class="epyt-arrow">&raquo;</div></div>';

        $pagination .= '<div class="epyt-loader"><img alt="loading" width="16" height="11" src="' . plugins_url('images/gallery-page-loader.gif', __FILE__) . '"></div>';
        $pagination .= '</div>';

        $code = $pagination . $code . $pagination;
        $gallobj->html = $code;
        return $gallobj;
    }

    public static function get_search_result_html($thumb, $options)
    {
        $escId = esc_attr($thumb->id);
        $code = '';

        $code .= '<div class="resultdiv" data-vid="' . $escId . '">
            <div class="resultinfo">
                <a class="pointer thumb load-movie" style="background-image: url(' . esc_url($thumb->img) . ')"></a>
                <a class="resulttitle pointer load-movie"><span class="ui-icon ui-icon-circle-triangle-e"></span> ' . sanitize_text_field($thumb->title) . '</a>
                <br>
                <span style="display: block;" id="scrollwatch' . $escId . '"></span>
                <div class="resultsubinfo">
                    <form name="wizform_video" method="post" action="" class="wizform" id="wizform_video">
                    <p>' .
                wp_nonce_field('_epyt_wiz', '_epyt_nonce', true, false) .
                '<button name="wizform_submit" class="ui-button ui-widget ui-corner-all" type="submit" value="step1_video"><span class="ui-icon ui-icon-circle-check"></span> ' . __('Select Video', 'text_domain') . '</button>
                        <input name="txtUrl" id="txtUrl" type="hidden" value="https://www.youtube.com/watch?v=' . $escId . '">
                    </p>
                    </form>
                </div>
            </div>
            <div class="clearboth"></div>
        </div>
        <div id="moviecontainer' . $escId . '" class="center moviecontainer relative" style="display: none;">
            ' . __('Preview', 'text_domain') . ': <a id="closeme' . $escId . '" class="closeme" data-vid="' . $escId . '">
                &times;
            </a>
            <div id="watch' . $escId . '">
            </div>
        </div>';

        return $code;
    }

    public static function user_in_roles_any($user, $roles)
    {
        foreach ($user->roles as $idx => $r)
        {
            if (in_array($r, $roles))
            {
                return true;
            }
        }
        return false;
    }

    public static function is_restrict_wizard()
    {
        $curr_user = wp_get_current_user();
        if (
                $curr_user->ID // logged in
                && isset(self::$alloptions[self::$opt_restrict_wizard]) && self::$alloptions[self::$opt_restrict_wizard] == 1 // restricting
                && is_array(self::$alloptions[self::$opt_restrict_wizard_roles]) && !self::user_in_roles_any($curr_user, self::$alloptions[self::$opt_restrict_wizard_roles])
        )
        {
            return true;
        }
        return false;
    }

    public static function media_button_wizard()
    {
        if (self::is_restrict_wizard())
        {
            return;
        }

        add_thickbox();

        $wizhref = admin_url('admin.php?page=youtube-ep-wizard') .
                '&random=' . rand(1, 1000) .
                '&TB_iframe=true&width=950&height=800';
        ?>
        <a href="<?php echo esc_attr($wizhref); ?>" class="thickbox button ytprefs_media_link" id="ytprefs_wiz_button" title="<?php _e('Visual YouTube Search Tool and Wizard - For easier embedding', 'text_domain'); ?>"><span></span> <?php _e('YouTube', 'text_domain'); ?></a>
        <?php
        if (current_user_can('manage_options') && self::vi_logged_in())
        {
            ?>
            <a class="button ytprefs_vi_embed_shortcode" id="ytprefs_wiz_button_vi" title="Embed vi video ad"><span></span> Video Ad</a>
            <?php
        }
    }

    public static function check_double_plugin_warning()
    {
        if (is_plugin_active('embedplus-for-wordpress/embedplus.php'))
        {
            add_action('admin_notices', array(self::class, "double_plugin_warning"));
        }
    }

    public static function double_plugin_warning()
    {
        global $pagenow;
        $user_id = get_current_user_id();
        if ($pagenow != 'plugins.php' || get_user_meta($user_id, 'embedplus_double_plugin_warning', true) != 1)
        {
            //echo '<div class="error">' . $_SERVER['QUERY_STRING'] .'</div>';
            if ($pagenow == 'plugins.php' || strpos($_SERVER['QUERY_STRING'], 'youtube-my-preferences') !== false ||
                    strpos($_SERVER['QUERY_STRING'], 'embedplus-video-analytics-dashboard') !== false ||
                    strpos($_SERVER['QUERY_STRING'], 'youtube-ep-analytics-dashboard') !== false ||
                    strpos($_SERVER['QUERY_STRING'], 'embedplus-official-options') !== false)
            {
                ?>
                <style type="text/css">
                    .embedpluswarning img
                    {
                        vertical-align: text-bottom;
                    }
                    div.bgyellow {background-color: #FCFC94; position: relative;}
                    a.epxout, a.epxout:hover {font-weight: bold; color: #ffffff; background-color: #ff8888; text-decoration: none;
                                              border-radius: 20px; font-size: 15px; position: absolute; top: 3px; right: 3px;
                                              line-height: 20px; text-align: center; width: 20px; height: 20px; display: block; cursor: pointer;}
                    </style>
                    <div class="error bgyellow embedpluswarningbox">
                    <p class="embedpluswarning">
                        <?php
                        if ($pagenow == 'plugins.php')
                        {
                            echo '<a class="epxout">&times;</a>';
                        }
                        ?>
                        <?php _e('Seems like you have two different YouTube plugins by the EmbedPlus Team installed', 'text_domain'); ?>: <b><img alt="<?php _e('YouTube Icon', 'text_domain'); ?>" src="<?php echo plugins_url('images/youtubeicon16.png', __FILE__) ?>" /> <?php _e('YouTube', 'text_domain'); ?></b> <?php _e('and', 'text_domain'); ?> <b><img alt="<?php _e('YouTube Icon', 'text_domain'); ?>" src="<?php echo plugins_url('images/btn_embedpluswiz.png', __FILE__) ?>" /> <?php _e('Advanced YouTube Embed.', 'text_domain'); ?></b> <?php _e('We strongly suggest keeping only the one you prefer, so that they don\'t conflict with each other while trying to create your embeds.', 'text_domain'); ?>
                    </p>
                </div>

                <script type="text/javascript">
                    (function ($)
                    {
                        $(document).ready(function ()
                        {
                            $('.epxout').on('click', function ()
                            {
                                $.ajax({
                                    type: "post",
                                    dataType: "json",
                                    timeout: 30000,
                                    url: window._EPYTA_ ? window._EPYTA_.wpajaxurl : ajaxurl,
                                    data: {action: 'my_embedplus_dismiss_double_plugin_warning'},
                                    success: function (response)
                                    {
                                        if (response.type === "success")
                                        {
                                            $(".embedpluswarningbox").hide();
                                        }
                                    },
                                    error: function (xhr, ajaxOptions, thrownError)
                                    {
                                    },
                                    complete: function ()
                                    {
                                    }
                                });
                            });
                        });
                    })(jQuery);</script>
                <?php
            }
        }
    }

    public static function my_embedplus_dismiss_double_plugin_warning()
    {
        $result = array();
        if (self::is_ajax())
        {
            $user_id = get_current_user_id();
            update_user_meta($user_id, 'embedplus_double_plugin_warning', 1);
            $result['type'] = 'success';
            echo json_encode($result);
        }
        else
        {
            $result['type'] = 'error';
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
        die();
    }

    public static function jsvars()
    {
        $loggedin = current_user_can('edit_posts');
        if (!($loggedin && self::$alloptions[self::$opt_admin_off_scripts]))
        {
            ?>
            <script data-cfasync="false">
                window._EPYT_ = window._EPYT_ || {
                    ajaxurl: "<?php echo admin_url('admin-ajax.php'); ?>",
                    security: "<?php echo wp_create_nonce('embedplus-nonce'); ?>",
                    gallery_scrolloffset: <?php echo intval(self::$alloptions[self::$opt_gallery_scrolloffset]) ?>,
                    eppathtoscripts: "<?php echo plugins_url('scripts/', __FILE__); ?>",
                    eppath: "<?php echo plugins_url('/', __FILE__); ?>",
                    epresponsiveselector: <?php echo self::get_responsiveselector(); ?>,
                    version: "<?php echo esc_attr(self::$alloptions[self::$opt_version]) ?>",
                    epdovol: true,
                    evselector: '<?php echo self::get_evselector(); ?>',
                    ajax_compat: <?php echo self::$alloptions[self::$opt_ajax_compat] == '1' ? 'true' : 'false' ?>,
                    maxres_facade: '<?php echo esc_attr(self::$alloptions[self::$opt_maxres_facade]) ?>',
                    ytapi_load: '<?php echo esc_attr(self::$alloptions[self::$opt_ytapi_load]) ?>',
                    pause_others: <?php echo self::$alloptions[self::$opt_pause_others] == '1' ? 'true' : 'false' ?>,
                    facade_mode: <?php echo self::$alloptions[self::$opt_facade_mode] == '1' ? 'true' : 'false' ?>,
                    not_live_on_channel: <?php echo self::$alloptions[self::$opt_not_live_on_channel] == '1' ? 'true' : 'false' ?>,
                    not_live_showtime: <?php echo intval(self::$alloptions[self::$opt_not_live_showtime]) ?>,
            <?php
            if (isset(self::$alloptions[self::$opt_pro]) && strlen(trim(self::$alloptions[self::$opt_pro])) > 8 && isset(self::$alloptions[self::$opt_dashpre]) && self::$alloptions[self::$opt_dashpre] == '1')
            {
                ?> dshpre:true,<?php }
            ?>
                    stopMobileBuffer: <?php echo self::$alloptions[self::$opt_stop_mobile_buffer] == '1' ? 'true' : 'false' ?>
                };</script>
            <?php
        }
    }

    public static function fitvids()
    {
        $loggedin = current_user_can('edit_posts');
        if (!($loggedin && self::$alloptions[self::$opt_admin_off_scripts]) && (self::$alloptions[self::$opt_responsive] || self::$alloptions[self::$opt_widgetfit]))
        {
            wp_enqueue_script('__ytprefsfitvids__', plugins_url('scripts/fitvids' . self::$min . '.js', __FILE__), array('__ytprefs__'), self::$version, true);
        }
    }

    public static function initoptions()
    {
        global $wpdb;
        $arroptions = get_option(self::$opt_alloptions);
        if ($arroptions !== false)
        {
            $bak = str_replace('.', '_', $arroptions[self::$opt_version]);
            add_option(self::$opt_alloptions . '_backup_' . $bak, $arroptions, '', 'no');
            $sql = "update " . $wpdb->options . " set autoload = 'no' where option_name like '" . self::$opt_alloptions . "\_backup\_%'";
            $wpdb->query($sql);
        }

        //vanilla defaults
        $_center = 0;
        $_glance = 0;
        $_autoplay = 0;
        $_cc_load_policy = 0;
        $_cc_lang_pref = '';
        $_iv_load_policy = 1;
        $_loop = 0;
        $_modestbranding = 0;
        $_rel = 1;
        $_fs = 1;
        $_theme = 'dark';
        $_color = 'red';
        $_autohide = 2;
        $_pro = '';
        $_nocookie = 0;
        $_gb_compat = 1;
        $_facade_mode = 0;
        $_facade_autoplay = 1;
        $_gdpr_consent = 0;
        $_gdpr_consent_message = self::$dft_gdpr_consent_message;
        $_gdpr_consent_button = __('Accept YouTube Content', 'text_domain');
        $_playlistorder = 0;
        $_acctitle = 0;
        $_ogvideo = 0;
        $_migrate = 0;
        $_migrate_youtube = 0;
        $_migrate_embedplusvideo = 0;
        $_controls = 1;
        $_oldspacing = 1;
        $_frontend_only = 1;
        $_responsive = 0;
        $_responsive_all = 1;
        $_widgetfit = 1;
        $_evselector_light = 0;
        $_stop_mobile_buffer = 1;
        $_restrict_wizard = 0;
        $_restrict_wizard_roles = self::$dft_roles;
        $_ajax_compat = 0;
        $_maxres_facade = 'eager';
        $_ytapi_load = 'light';
        $_schemaorg = 0;
        $_ftpostimg = 0;
        $_spdc = 0;
        $_spdcexp = 24;
        $_dashpre = 0;
        $_spdcab = 1;
        $_dynload = 0;
        $_dyntype = '';
        $_defaultdims = 1;
        $_defaultwidth = isset($GLOBALS['content_width']) && is_numeric($GLOBALS['content_width']) ? intval($GLOBALS['content_width']) : 800;
        $_defaultheight = intval($_defaultwidth * 9.0 / 16.0);
        $_playsinline = 0;
        $_origin = 0;
        $_pause_others = 0;
        $_defaultvol = 0;
        $_vol = '';
        $_apikey = '';
        $_hl = '';
        $_dohl = 0;
        $_gallery_columns = 3;
        $_gallery_collapse_grid = 0;
        $_gallery_collapse_grid_breaks = self::$dft_bpts;
        $_gallery_scrolloffset = 20;
        $_gallery_hideprivate = 1;
        $_gallery_showtitle = 1;
        $_gallery_showpaging = 1;
        $_gallery_autonext = 0;
        $_gallery_hidethumbimg = 0;
        $_gallery_thumbplay = 1;
        $_gallery_channelsub = 0;
        $_gallery_channelsublink = '';
        $_gallery_channelsubtext = __('Subscribe to my channel', 'text_domain');
        $_gallery_customarrows = 0;
        $_gallery_customprev = __('Prev', 'text_domain');
        $_gallery_customnext = __('Next', 'text_domain');
        $_gallery_pagesize = 15;
        $_gallery_limit_dsc = 0;
        $_gallery_length_dsc = 100;
        $_gallery_style = 'grid';
        $_gallery_showdsc = 0;
        $_gallery_thumbcrop = 'box';
        $_gallery_disptype = 'default';
        $_not_live_content = '';
        $_not_live_on = 0;
        $_not_live_showtime = 180;
        $_not_live_on_channel = 0;
        $_live_chat = 0;
        $_debugmode = 0;
        $_uninstall_data = 0;
        $_admin_off_scripts = 0;
        $_defer_js = 0;
        $_defer_jquery = 0;
        $_ajax_save = 1;
        $_show_pointer = 1;
        $_onboarded = 0;
        $_old_script_method = 0;

        $_vi_active = 0;
        $_vi_hide_monetize_tab = 0;
        $_vi_endpoints = '';
        $_vi_token = '';
        $_vi_last_login = self::$vi_default_date;
        $_vi_last_category_update = self::$vi_default_date;
        $_vi_adstxt = '';
        $_vi_js_settings = self::$vi_dft_js_settings;
        $_vi_js_script = '';
        $_vi_js_posttypes = array();
        $_vi_js_position = 'top';
        $_vi_show_gdpr_authorization = 1;
        $_vi_show_privacy_button = 0;

        $_free_migrated = 0;

        if ($arroptions == false || (is_array($arroptions) && isset($arroptions[self::$opt_free_migrated]) && $arroptions[self::$opt_free_migrated] == 0))
        {
            $arr_free_migrate_options = get_option('youtubeprefs_alloptions_migrate');
            if ($arr_free_migrate_options == false)
            {
                $arr_free_migrate_options = get_option('youtubeprefs_alloptions');
            }

            if ($arr_free_migrate_options != false)
            {
                if ($arroptions == false)
                {
                    $arroptions = $arr_free_migrate_options;
                }
                else
                {
                    $arroptions = $arr_free_migrate_options + $arroptions;
                }
                $arroptions[self::$opt_free_migrated] = 1;
            }
        }

        //update vanilla to previous settings if exists
        if ($arroptions !== false)
        {
            $_center = self::tryget($arroptions, self::$opt_center, 0);
            $_glance = self::tryget($arroptions, self::$opt_glance, $_glance);
            $_autoplay = self::tryget($arroptions, self::$opt_autoplay, 0);
            $_debugmode = self::tryget($arroptions, self::$opt_debugmode, 0);
            $_uninstall_data = self::tryget($arroptions, self::$opt_uninstall_data, 0);
            $_old_script_method = self::tryget($arroptions, self::$opt_old_script_method, 0);
            $_cc_load_policy = self::tryget($arroptions, self::$opt_cc_load_policy, 0);
            $_cc_lang_pref = self::tryget($arroptions, self::$opt_cc_lang_pref, $_cc_lang_pref);
            $_iv_load_policy = self::tryget($arroptions, self::$opt_iv_load_policy, 1);
            $_loop = self::tryget($arroptions, self::$opt_loop, 0);
            $_modestbranding = self::tryget($arroptions, self::$opt_modestbranding, 0);
            $_rel = self::tryget($arroptions, self::$opt_rel, 1);
            $_fs = self::tryget($arroptions, self::$opt_fs, 1);
            $_playsinline = self::tryget($arroptions, self::$opt_playsinline, 0);
            $_origin = self::tryget($arroptions, self::$opt_origin, 1);
            $_hl = self::tryget($arroptions, self::$opt_hl, '');
            $_dohl = self::tryget($arroptions, self::$opt_dohl, 0);
            $_theme = self::tryget($arroptions, self::$opt_theme, 'dark');
            $_color = self::tryget($arroptions, self::$opt_color, 'red');
            $_autohide = self::tryget($arroptions, self::$opt_autohide, 2);
            $_pro = self::tryget($arroptions, self::$opt_pro, '');
            $_nocookie = self::tryget($arroptions, self::$opt_nocookie, 0);
            $_gb_compat = self::tryget($arroptions, self::$opt_gb_compat, $_gb_compat);
            $_facade_mode = self::tryget($arroptions, self::$opt_facade_mode, $_facade_mode);
            $_facade_autoplay = self::tryget($arroptions, self::$opt_facade_autoplay, $_facade_autoplay);
            $_gdpr_consent = self::tryget($arroptions, self::$opt_gdpr_consent, $_gdpr_consent);
            $_gdpr_consent_message = self::tryget($arroptions, self::$opt_gdpr_consent_message, $_gdpr_consent_message);
            $_gdpr_consent_button = self::tryget($arroptions, self::$opt_gdpr_consent_button, $_gdpr_consent_button);
            $_playlistorder = self::tryget($arroptions, self::$opt_playlistorder, 0);
            $_acctitle = self::tryget($arroptions, self::$opt_acctitle, 0);
            $_ogvideo = self::tryget($arroptions, self::$opt_ogvideo, 0);
            $_migrate = self::tryget($arroptions, self::$opt_migrate, 0);
            $_migrate_youtube = self::tryget($arroptions, self::$opt_migrate_youtube, 0);
            $_migrate_embedplusvideo = self::tryget($arroptions, self::$opt_migrate_embedplusvideo, 0);
            $_controls = self::tryget($arroptions, self::$opt_controls, 1);
            $_controls = $_controls == 2 ? 1 : $_controls;
            $_oldspacing = self::tryget($arroptions, self::$opt_oldspacing, 1);
            $_frontend_only = self::tryget($arroptions, self::$opt_frontend_only, $_frontend_only);
            $_responsive = self::tryget($arroptions, self::$opt_responsive, $_responsive);
            $_responsive_all = self::tryget($arroptions, self::$opt_responsive_all, $_responsive_all);
            $_widgetfit = self::tryget($arroptions, self::$opt_widgetfit, 1);
            $_evselector_light = self::tryget($arroptions, self::$opt_evselector_light, 0);
            $_stop_mobile_buffer = self::tryget($arroptions, self::$opt_stop_mobile_buffer, 1);
            $_restrict_wizard = self::tryget($arroptions, self::$opt_restrict_wizard, 0);
            $_restrict_wizard_roles = self::tryget($arroptions, self::$opt_restrict_wizard_roles, self::$dft_roles);
            $_ajax_compat = self::tryget($arroptions, self::$opt_ajax_compat, 0);
            $_maxres_facade = self::tryget($arroptions, self::$opt_maxres_facade, $_maxres_facade);
            $_ytapi_load = self::tryget($arroptions, self::$opt_ytapi_load, $_ytapi_load);
            $_schemaorg = self::tryget($arroptions, self::$opt_schemaorg, 0);
            if (defined('WPSEO_VIDEO_FILE'))
            {
                $_schemaorg = 0;
            }
            $_ftpostimg = self::tryget($arroptions, self::$opt_ftpostimg, 0);
            $_spdc = self::tryget($arroptions, self::$opt_spdc, 0);
            $_spdcexp = self::tryget($arroptions, self::$opt_spdcexp, 24);
            $_dashpre = self::tryget($arroptions, self::$opt_dashpre, 0);
            $_spdcab = self::tryget($arroptions, self::$opt_spdcab, 1);
            $_dynload = self::tryget($arroptions, self::$opt_dynload, 0);
            $_dyntype = self::tryget($arroptions, self::$opt_dyntype, '');
            $_defaultdims = self::tryget($arroptions, self::$opt_defaultdims, $_defaultdims);
            $_defaultwidth = self::tryget($arroptions, self::$opt_defaultwidth, $_defaultwidth);
            $_defaultheight = self::tryget($arroptions, self::$opt_defaultheight, $_defaultheight);
            $_pause_others = self::tryget($arroptions, self::$opt_pause_others, $_pause_others);
            $_defaultvol = self::tryget($arroptions, self::$opt_defaultvol, 0);
            $_vol = self::tryget($arroptions, self::$opt_vol, '');
            $_apikey = self::tryget($arroptions, self::$opt_apikey, '');
            $_gallery_pagesize = self::tryget($arroptions, self::$opt_gallery_pagesize, 15);
            $_gallery_limit_dsc = self::tryget($arroptions, self::$opt_gallery_limit_dsc, $_gallery_limit_dsc);
            $_gallery_length_dsc = self::tryget($arroptions, self::$opt_gallery_length_dsc, $_gallery_length_dsc);
            $_gallery_columns = self::tryget($arroptions, self::$opt_gallery_columns, 3);
            $_gallery_collapse_grid = self::tryget($arroptions, self::$opt_gallery_collapse_grid, 0);
            $_gallery_collapse_grid_breaks = self::tryget($arroptions, self::$opt_gallery_collapse_grid_breaks, self::$dft_bpts);
            $_gallery_scrolloffset = self::tryget($arroptions, self::$opt_gallery_scrolloffset, 20);
            $_gallery_hideprivate = self::tryget($arroptions, self::$opt_gallery_hideprivate, $_gallery_hideprivate);
            $_gallery_showtitle = self::tryget($arroptions, self::$opt_gallery_showtitle, 1);
            $_gallery_showpaging = self::tryget($arroptions, self::$opt_gallery_showpaging, 1);
            $_gallery_autonext = self::tryget($arroptions, self::$opt_gallery_autonext, 0);
            $_gallery_hidethumbimg = self::tryget($arroptions, self::$opt_gallery_hidethumbimg, 0);
            $_gallery_thumbplay = self::tryget($arroptions, self::$opt_gallery_thumbplay, 1);
            $_gallery_style = self::tryget($arroptions, self::$opt_gallery_style, 'grid');
            $_gallery_thumbcrop = self::tryget($arroptions, self::$opt_gallery_thumbcrop, 'box');
            $_gallery_disptype = self::tryget($arroptions, self::$opt_gallery_disptype, 'default');
            $_gallery_channelsub = self::tryget($arroptions, self::$opt_gallery_channelsub, $_gallery_channelsub);
            $_gallery_channelsublink = self::tryget($arroptions, self::$opt_gallery_channelsublink, $_gallery_channelsublink);
            $_gallery_channelsubtext = self::tryget($arroptions, self::$opt_gallery_channelsubtext, $_gallery_channelsubtext);
            $_gallery_customarrows = self::tryget($arroptions, self::$opt_gallery_customarrows, $_gallery_customarrows);
            $_gallery_customnext = self::tryget($arroptions, self::$opt_gallery_customnext, $_gallery_customnext);
            $_gallery_customprev = self::tryget($arroptions, self::$opt_gallery_customprev, $_gallery_customprev);
            $_gallery_showdsc = self::tryget($arroptions, self::$opt_gallery_showdsc, $_gallery_showdsc);
            $_not_live_content = self::tryget($arroptions, self::$opt_not_live_content, $_not_live_content);
            $_not_live_content = empty($_not_live_content) ? $_not_live_content : trim($_not_live_content);
            $_not_live_on = self::tryget($arroptions, self::$opt_not_live_on, empty($_not_live_content) ? 0 : $_not_live_on);
            $_not_live_showtime = self::tryget($arroptions, self::$opt_not_live_showtime, $_not_live_showtime);
            $_not_live_on_channel = self::tryget($arroptions, self::$opt_not_live_on_channel, $_not_live_on_channel);
            $_live_chat = self::tryget($arroptions, self::$opt_live_chat, $_live_chat);
            $_admin_off_scripts = self::tryget($arroptions, self::$opt_admin_off_scripts, $_admin_off_scripts);
            $_defer_js = self::tryget($arroptions, self::$opt_defer_js, $_defer_js);
            $_defer_jquery = self::tryget($arroptions, self::$opt_defer_jquery, $_defer_jquery);
            $_ajax_save = self::tryget($arroptions, self::$opt_ajax_save, $_ajax_save);
            $_show_pointer = self::tryget($arroptions, self::$opt_show_pointer, $_show_pointer);
            $_onboarded = 0; // self::tryget($arroptions, self::$opt_onboarded, $_onboarded);

            $_vi_active = self::tryget($arroptions, self::$opt_vi_active, $_vi_active);
            $_vi_hide_monetize_tab = self::tryget($arroptions, self::$opt_vi_hide_monetize_tab, $_vi_hide_monetize_tab);
            $_vi_endpoints = self::tryget($arroptions, self::$opt_vi_endpoints, $_vi_endpoints);
            $_vi_token = self::tryget($arroptions, self::$opt_vi_token, $_vi_token);
            $_vi_last_login = self::tryget($arroptions, self::$opt_vi_last_login, $_vi_last_login);
            $_vi_last_category_update = self::tryget($arroptions, self::$opt_vi_last_category_update, $_vi_last_category_update);
            $_vi_adstxt = self::tryget($arroptions, self::$opt_vi_adstxt, $_vi_adstxt);
            $_vi_js_settings = self::tryget($arroptions, self::$opt_vi_js_settings, self::$vi_dft_js_settings);
            $_vi_js_script = self::tryget($arroptions, self::$opt_vi_js_script, $_vi_js_script);
            $_vi_js_posttypes = self::tryget($arroptions, self::$opt_vi_js_posttypes, $_vi_js_posttypes);
            $_vi_js_position = self::tryget($arroptions, self::$opt_vi_js_position, $_vi_js_position);
            $_vi_show_gdpr_authorization = self::tryget($arroptions, self::$opt_vi_show_gdpr_authorization, $_vi_show_gdpr_authorization);
            $_vi_show_privacy_button = self::tryget($arroptions, self::$opt_vi_show_privacy_button, $_vi_show_privacy_button);

            $_free_migrated = self::tryget($arroptions, self::$opt_free_migrated, 0);
        }
        else
        {
            $_oldspacing = 0;
        }

        $all = array(
            self::$opt_version => self::$version,
            self::$opt_center => $_center,
            self::$opt_glance => $_glance,
            self::$opt_autoplay => $_autoplay,
            self::$opt_cc_load_policy => $_cc_load_policy,
            self::$opt_cc_lang_pref => $_cc_lang_pref,
            self::$opt_iv_load_policy => $_iv_load_policy,
            self::$opt_loop => $_loop,
            self::$opt_modestbranding => $_modestbranding,
            self::$opt_rel => $_rel,
            self::$opt_fs => $_fs,
            self::$opt_playsinline => $_playsinline,
            self::$opt_origin => $_origin,
            self::$opt_autohide => $_autohide,
            self::$opt_hl => $_hl,
            self::$opt_dohl => $_dohl,
            self::$opt_theme => $_theme,
            self::$opt_color => $_color,
            self::$opt_pro => $_pro,
            self::$opt_nocookie => $_nocookie,
            self::$opt_gb_compat => $_gb_compat,
            self::$opt_facade_mode => $_facade_mode,
            self::$opt_facade_autoplay => $_facade_autoplay,
            self::$opt_gdpr_consent => $_gdpr_consent,
            self::$opt_gdpr_consent_message => $_gdpr_consent_message,
            self::$opt_gdpr_consent_button => $_gdpr_consent_button,
            self::$opt_playlistorder => $_playlistorder,
            self::$opt_acctitle => $_acctitle,
            self::$opt_ogvideo => $_ogvideo,
            self::$opt_migrate => $_migrate,
            self::$opt_migrate_youtube => $_migrate_youtube,
            self::$opt_migrate_embedplusvideo => $_migrate_embedplusvideo,
            self::$opt_controls => $_controls,
            self::$opt_oldspacing => $_oldspacing,
            self::$opt_frontend_only => $_frontend_only,
            self::$opt_responsive => $_responsive,
            self::$opt_responsive_all => $_responsive_all,
            self::$opt_widgetfit => $_widgetfit,
            self::$opt_evselector_light => $_evselector_light,
            self::$opt_stop_mobile_buffer => $_stop_mobile_buffer,
            self::$opt_restrict_wizard => $_restrict_wizard,
            self::$opt_restrict_wizard_roles => $_restrict_wizard_roles,
            self::$opt_ajax_compat => $_ajax_compat,
            self::$opt_maxres_facade => $_maxres_facade,
            self::$opt_ytapi_load => $_ytapi_load,
            self::$opt_schemaorg => $_schemaorg,
            self::$opt_ftpostimg => $_ftpostimg,
            self::$opt_spdc => $_spdc,
            self::$opt_spdcexp => $_spdcexp,
            self::$opt_dashpre => $_dashpre,
            self::$opt_spdcab => $_spdcab,
            self::$opt_dynload => $_dynload,
            self::$opt_dyntype => $_dyntype,
            self::$opt_defaultdims => $_defaultdims,
            self::$opt_defaultwidth => $_defaultwidth,
            self::$opt_defaultheight => $_defaultheight,
            self::$opt_pause_others => $_pause_others,
            self::$opt_defaultvol => $_defaultvol,
            self::$opt_vol => $_vol,
            self::$opt_apikey => $_apikey,
            self::$opt_gallery_columns => $_gallery_columns,
            self::$opt_gallery_collapse_grid => $_gallery_collapse_grid,
            self::$opt_gallery_collapse_grid_breaks => $_gallery_collapse_grid_breaks,
            self::$opt_gallery_scrolloffset => $_gallery_scrolloffset,
            self::$opt_gallery_hideprivate => $_gallery_hideprivate,
            self::$opt_gallery_showtitle => $_gallery_showtitle,
            self::$opt_gallery_showpaging => $_gallery_showpaging,
            self::$opt_gallery_autonext => $_gallery_autonext,
            self::$opt_gallery_hidethumbimg => $_gallery_hidethumbimg,
            self::$opt_gallery_thumbplay => $_gallery_thumbplay,
            self::$opt_gallery_channelsub => $_gallery_channelsub,
            self::$opt_gallery_channelsublink => $_gallery_channelsublink,
            self::$opt_gallery_channelsubtext => $_gallery_channelsubtext,
            self::$opt_gallery_customarrows => $_gallery_customarrows,
            self::$opt_gallery_customnext => $_gallery_customnext,
            self::$opt_gallery_customprev => $_gallery_customprev,
            self::$opt_gallery_showdsc => $_gallery_showdsc,
            self::$opt_gallery_style => $_gallery_style,
            self::$opt_gallery_thumbcrop => $_gallery_thumbcrop,
            self::$opt_gallery_disptype => $_gallery_disptype,
            self::$opt_gallery_pagesize => $_gallery_pagesize,
            self::$opt_gallery_limit_dsc => $_gallery_limit_dsc,
            self::$opt_gallery_length_dsc => $_gallery_length_dsc,
            self::$opt_not_live_content => $_not_live_content,
            self::$opt_not_live_on => $_not_live_on,
            self::$opt_not_live_showtime => $_not_live_showtime,
            self::$opt_not_live_on_channel => $_not_live_on_channel,
            self::$opt_live_chat => $_live_chat,
            self::$opt_debugmode => $_debugmode,
            self::$opt_uninstall_data => $_uninstall_data,
            self::$opt_admin_off_scripts => $_admin_off_scripts,
            self::$opt_defer_js => $_defer_js,
            self::$opt_defer_jquery => $_defer_jquery,
            self::$opt_ajax_save => $_ajax_save,
            self::$opt_show_pointer => $_show_pointer,
            self::$opt_onboarded => $_onboarded,
            self::$opt_old_script_method => $_old_script_method,
            self::$opt_vi_active => $_vi_active,
            self::$opt_vi_hide_monetize_tab => $_vi_hide_monetize_tab,
            self::$opt_vi_endpoints => $_vi_endpoints,
            self::$opt_vi_token => $_vi_token,
            self::$opt_vi_last_login => $_vi_last_login,
            self::$opt_vi_last_category_update => $_vi_last_category_update,
            self::$opt_vi_adstxt => $_vi_adstxt,
            self::$opt_vi_js_settings => $_vi_js_settings,
            self::$opt_vi_js_script => $_vi_js_script,
            self::$opt_vi_js_posttypes => $_vi_js_posttypes,
            self::$opt_vi_js_position => $_vi_js_position,
            self::$opt_vi_show_gdpr_authorization => $_vi_show_gdpr_authorization,
            self::$opt_vi_show_privacy_button => $_vi_show_privacy_button,
            self::$opt_free_migrated => $_free_migrated
        );

        update_option(self::$opt_alloptions, $all);
        update_option('embed_autourls', 1);
        self::$alloptions = get_option(self::$opt_alloptions);

        try
        {
            if (self::$alloptions[self::$opt_spdc] == 1)
            {
                self::spdcpurge();
                wp_remote_get(site_url(), array('timeout' => self::$curltimeout));
            }
        }
        catch (Exception $ex)
        {
            
        }

        try
        {
            if ((bool) self::$alloptions[self::$opt_dashpre])
            {
                if (function_exists('get_home_path'))
                {
                    self::remove_stats_validation();
                }
                else
                {
                    add_action('admin_init', array(self::class, 'remove_stats_validation'), 10, 0);
                }
            }
        }
        catch (Exception $ex)
        {
            
        }
    }

    public static function tryget($array, $key, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    public static function wp_above_version($ver)
    {
        global $wp_version;
        if (version_compare($wp_version, $ver, '>='))
        {
            return true;
        }
        return false;
    }

    public static function do_ytprefs()
    {
        //add_filter('autoptimize_filter_js_exclude', array(self::class, 'ao_override_jsexclude'), 10, 1);
        if (
                !is_admin() || (self::$alloptions[self::$opt_frontend_only] != 1)
        //|| (function_exists('wp_doing_ajax') && wp_doing_ajax())
        )
        {
            add_filter('the_content', array(self::class, 'apply_prefs_content'), 1);
            add_filter('widget_text', array(self::class, 'apply_prefs_widget'), 1);
            //add_filter('bjll/skip_classes', array(self::class, 'bjll_skip_classes'), 10, 2);

            add_filter('sgo_lazy_load_exclude_classes', array(self::class, 'exclude_lazy_sgo'));

            add_shortcode('embedyt', array(self::class, 'apply_prefs_shortcode'));
            if (self::$alloptions[self::$opt_migrate] == 1)
            {
                if (self::$alloptions[self::$opt_migrate_youtube] == 1)
                {
                    add_shortcode('youtube', array(self::class, 'apply_prefs_shortcode_youtube'));
                    add_shortcode('youtube_video', array(self::class, 'apply_prefs_shortcode_youtube'));
                }
                if (self::$alloptions[self::$opt_migrate_embedplusvideo] == 1)
                {
                    add_shortcode('embedplusvideo', array(self::class, 'apply_prefs_shortcode_embedplusvideo'));
                }
            }
        }
        if (self::$alloptions[self::$opt_ftpostimg] == 1 && self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0)
        {
            //add_action('save_post', array(self::class, 'doftpostimg'), 110, 3);
            add_action('transition_post_status', array(self::class, 'doftpostimg'), 110, 3);
        }

        if (self::$alloptions[self::$opt_defer_js] == 1)
        {
            add_filter('script_loader_tag', array(self::class, 'defer_scripts'), 10, 3);
        }
    }

    public static function ao_override_jsexclude($exclude)
    {
        if (strpos($exclude, 'ytprefs' . self::$min . '.js') === false)
        {
            return $exclude . ',ytprefs' . self::$min . '.js,__ytprefs__';
        }
        return $exclude;
    }

    public static function exclude_lazy_sgo($classes)
    {
        $classes[] = '__youtube_prefs__';
        return $classes;
    }

    public static function apply_prefs_shortcode($atts, $content = null)
    {
        $content = trim($content);
        $currfilter = current_filter();
        if (preg_match(self::$justurlregex, $content))
        {
            return self::get_html(array($content), strpos($currfilter, 'widget_text') === 0 ? false : true, false);
        }
        return '';
    }

    public static function apply_prefs_shortcode_youtube($atts, $content = null)
    {
        $content = 'https://www.youtube.com/watch?v=' . trim($content);
        $currfilter = current_filter();
        if (preg_match(self::$justurlregex, $content))
        {
            return self::get_html(array($content), $currfilter == 'widget_text' ? false : true, false);
        }
        return '';
    }

    public static function apply_prefs_shortcode_embedplusvideo($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            "height" => self::$defaultheight,
            "width" => self::$defaultwidth,
            "vars" => "",
            "standard" => "",
            "id" => "ep" . rand(10000, 99999)
                ), $atts);

        $epvars = $atts['vars'];
        $epvars = preg_replace('/\s/', '', $epvars);
        $epvars = preg_replace('/¬/', '&not', $epvars);
        $epvars = str_replace('&amp;', '&', $epvars);

        $epparams = self::keyvalue($epvars, true);

        if (isset($epparams) && isset($epparams['ytid']))
        {
            $start = isset($epparams['start']) && is_numeric($epparams['start']) ? '&start=' . intval($epparams['start']) : '';
            $end = isset($epparams['end']) && is_numeric($epparams['end']) ? '&end=' . intval($epparams['end']) : '';
            $end = isset($epparams['stop']) && is_numeric($epparams['stop']) ? '&end=' . intval($epparams['stop']) : '';

            $url = 'https://www.youtube.com/watch?v=' . trim($epparams['ytid']) . $start . $end;

            $currfilter = current_filter();
            if (preg_match(self::$justurlregex, $url))
            {
                return self::get_html(array($url), $currfilter == 'widget_text' ? false : true, false);
            }
        }
        return '';
    }

    public static function apply_prefs_content($content)
    {
        $content = preg_replace_callback(self::$ytregex, array(self::class, "get_html_content"), $content);
        return $content;
    }

    public static function apply_prefs_widget($content)
    {
        $content = preg_replace_callback(self::$ytregex, array(self::class, "get_html_widget"), $content);
        return $content;
    }

    public static function get_html_content($m)
    {
        return self::get_html($m, true, true);
    }

    public static function get_html_widget($m)
    {
        return self::get_html($m, false, true);
    }

    public static function get_gallery_page($options)
    {
        $gallobj = new stdClass();

        $options->pageSize = min(intval($options->pageSize), 50);
        $options->pageSizeReal = $options->pageSize;
        $options->columns = intval($options->columns) == 0 ? 3 : intval($options->columns);
        $options->showTitle = intval($options->showTitle);
        $options->showPaging = intval($options->showPaging);
        $options->autonext = intval($options->autonext);
        $options->hidethumbimg = intval($options->hidethumbimg);
        $options->thumbplay = intval($options->thumbplay);
        $options->showDsc = intval(isset($options->showDsc) ? $options->showDsc : 1);
        $options->thumbcrop = sanitize_html_class(isset($options->thumbcrop) ? $options->thumbcrop : 'box');
        $options->style = sanitize_html_class($options->style);

        if (empty($options->apiKey))
        {
            $gallobj->html = '<div>' . __('Please enter your YouTube API key to embed galleries.', 'text_domain') . '</div>';
            return $gallobj;
        }

        $apiEndpoint = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,status&playlistId=' . $options->playlistId
                . '&maxResults=' . $options->pageSize
                . '&key=' . $options->apiKey;
        if ($options->pageToken != null)
        {
            $apiEndpoint .= '&pageToken=' . $options->pageToken;
        }
        $spdckey = '';
        $optionsstr = json_encode($options);
        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 9 && self::$alloptions[self::$opt_spdc] == 1 && !(bool) self::$alloptions[self::$opt_gdpr_consent])
        {
            try
            {
                $spdckey = self::$spdcprefix . '_' . md5($apiEndpoint . $optionsstr);
                $spdcval = get_transient($spdckey);
                if (!empty($spdcval))
                {
                    //self::debuglog((microtime(true) - $time_start) . "\t" . $spdckey . "\t" . $spdcval . "\r\n");
                    $gallobj->html = $spdcval;
                    $pattern_videoid = '/data[-]videoid="([^"]+)"/';
                    $init_id_matches = array();
                    if (preg_match($pattern_videoid, $spdcval, $init_id_matches))
                    {
                        $gallobj->init_id = sanitize_text_field($init_id_matches[1]);
                    }
                    return $gallobj;
                }
            }
            catch (Exception $ex)
            {
                
            }
        }

        $code = '';
        $init_id = null;

        $apiResult = wp_remote_get($apiEndpoint, array('timeout' => self::$curltimeout, 'headers' => array('referer' => site_url())));

        if (is_wp_error($apiResult))
        {
            $gallobj->html = self::clean_api_error_html($apiResult->get_error_message(), true);
            return $gallobj;
        }

        if (self::$alloptions[self::$opt_debugmode] == 1 && current_user_can('manage_options'))
        {
            $redactedEndpoint = preg_replace('@&key=[^&]+@i', '&key=PRIVATE', $apiEndpoint);
            $active_plugins = get_option('active_plugins');
            $gallobj->html = '<pre onclick="_EPADashboard_.selectText(this);" class="epyt-debug">' . __('CLICK this debug text to auto-select all. Then, COPY the selection.', 'text_domain') . "\n\n" .
                    __('THIS IS DEBUG MODE OUTPUT. UNCHECK THE OPTION IN THE SETTINGS PAGE ONCE YOU ARE DONE DEBUGGING TO PUT THINGS BACK TO NORMAL.', 'text_domain') . "\n\n" . $redactedEndpoint . "\n\n" . print_r($apiResult, true) . "\n\nActive Plugins\n\n" . print_r($active_plugins, true) . '</pre>';
            return $gallobj;
        }

        $jsonResult = json_decode($apiResult['body']);

        if (isset($jsonResult->error))
        {
            if (isset($jsonResult->error->message))
            {
                $gallobj->html = self::clean_api_error_html($jsonResult->error->message, true);
                return $gallobj;
            }
            $gallobj->html = '<div>' . __('Sorry, there may be an issue with your YouTube API key.', 'text_domain') . ' ' . self::$boilerplate_api_error_message . '</div>';
            return $gallobj;
        }



        $resultsPerPage = $options->pageSize; // $jsonResult->pageInfo->resultsPerPage;
        $totalResults = $jsonResult->pageInfo->totalResults;

        $nextPageToken = '';
        $prevPageToken = '';
        if (isset($jsonResult->nextPageToken))
        {
            $nextPageToken = $jsonResult->nextPageToken;
        }

        if (isset($jsonResult->prevPageToken))
        {
            $prevPageToken = $jsonResult->prevPageToken;
        }

        $cnt = 0;
        $colclass = '';
        if (in_array($options->style, array('grid', '')))
        {
            $colclass = ' epyt-cols-' . $options->columns . ' ';
        }

        $cropclass = '';
        if (!in_array($options->thumbcrop, array('box', '')))
        {
            $cropclass = ' epyt-thumb-' . $options->thumbcrop . ' ';
        }

        $code .= '<div class="epyt-gallery-allthumbs ' . $cropclass . $colclass . '">';




        if (isset($jsonResult->items) && $jsonResult->items != null && is_array($jsonResult->items))
        {
            if (strpos($options->playlistId, 'UU') === 0)
            {
                // sort only channels
                usort($jsonResult->items, array(self::class, 'compare_vid_date')); // sorts in place                
            }

            $options->pageSizeReal = count($jsonResult->items);
            foreach ($jsonResult->items as $item)
            {

                $thumb = new stdClass();

                $thumb->id = isset($item->snippet->resourceId->videoId) ? $item->snippet->resourceId->videoId : null;
                $thumb->id = $thumb->id ? $thumb->id : $item->id->videoId;
                $thumb->title = $options->showTitle ? $item->snippet->title : '';
                $thumb->privacyStatus = isset($item->status->privacyStatus) ? $item->status->privacyStatus : null;

                if ($thumb->privacyStatus == 'private' && self::$alloptions[self::$opt_gallery_hideprivate] == 1)
                {
                    continue;
                }

                if ($cnt == 0 && $options->pageToken == null)
                {
                    $init_id = $thumb->id;
                }

                if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0 && $options->style == 'listview')
                {
                    $thumb->dsc = $options->showDsc ? $item->snippet->description : '';
                    if (self::$alloptions[self::$opt_gallery_limit_dsc])
                    {
                        $thumb->dsc = mb_strimwidth($thumb->dsc, 0, intval(self::$alloptions[self::$opt_gallery_length_dsc]), '...');
                    }
                }

                if ($thumb->privacyStatus == 'private')
                {
                    $thumb->img = plugins_url('/images/private.png', __FILE__);
                    $thumb->quality = 'medium';
                }
                else
                {
                    if (isset($item->snippet->thumbnails->high->url))
                    {
                        $thumb->img = $item->snippet->thumbnails->high->url;
                        $thumb->quality = 'high';
                    }
                    elseif (isset($item->snippet->thumbnails->default->url))
                    {
                        $thumb->img = $item->snippet->thumbnails->default->url;
                        $thumb->quality = 'default';
                    }
                    elseif (isset($item->snippet->thumbnails->medium->url))
                    {
                        $thumb->img = $item->snippet->thumbnails->medium->url;
                        $thumb->quality = 'medium';
                    }
                    else
                    {
                        $thumb->img = plugins_url('/images/deleted-video-thumb.png', __FILE__);
                        $thumb->quality = 'medium';
                    }
                }

                $code .= self::get_thumbnail_html($thumb, $options);
                $cnt++;

                if ($cnt % $options->columns === 0 && $options->style !== 'carousel')
                {
                    $code .= '<div class="epyt-gallery-rowbreak"></div>';
                }
            }
        }

        $code .= '<div class="epyt-gallery-clear"></div></div>';

        if ($options->style === 'carousel' && $options->showTitle)
        {
            $code .= '<div class="epyt-gallery-rowtitle"></div>';
        }


        $totalPages = ceil($totalResults / $resultsPerPage);
        $pagination = '<div class="epyt-pagination ' . ($options->showPaging == 0 ? 'epyt-hide-pagination' : '') . '">';

        $txtprev = self::$alloptions[self::$opt_gallery_customarrows] ? self::$alloptions[self::$opt_gallery_customprev] : __('Prev', 'text_domain');
        $pagination .= '<div tabindex="0" role="button" class="epyt-pagebutton epyt-prev ' . (empty($prevPageToken) ? ' hide ' : '') . '" data-playlistid="' . esc_attr($options->playlistId)
                . '" data-pagesize="' . intval($options->pageSize)
                . '" data-pagetoken="' . esc_attr($prevPageToken)
                . '" data-style="' . esc_attr($options->style)
                . '" data-epcolumns="' . intval($options->columns)
                . '" data-showtitle="' . intval($options->showTitle)
                . '" data-showpaging="' . intval($options->showPaging)
                . '" data-autonext="' . intval($options->autonext)
                . '" data-hidethumbimg="' . intval($options->hidethumbimg)
                . '" data-thumbplay="' . intval($options->thumbplay)
                . ((self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 9 && $options->style == 'listview') ? '" data-showdsc="' . intval($options->showDsc) : '')
                . ((self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 9 && !in_array($options->thumbcrop, array('box', ''))) ? '" data-thumbcrop="' . $options->thumbcrop : '')
                . '"><div class="epyt-arrow">&laquo;</div> <div>' . $txtprev . '</div></div>';


        $pagination .= '<div class="epyt-pagenumbers ' . ($totalPages > 1 ? '' : 'hide') . '">';
        $pagination .= '<div class="epyt-current">1</div><div class="epyt-pageseparator"> / </div><div class="epyt-totalpages">' . $totalPages . '</div>';
        $pagination .= '</div>';

        $txtnext = self::$alloptions[self::$opt_gallery_customarrows] ? self::$alloptions[self::$opt_gallery_customnext] : __('Next', 'text_domain');
        $pagination .= '<div tabindex="0" role="button" class="epyt-pagebutton epyt-next' . (empty($nextPageToken) ? ' hide ' : '') . '" data-playlistid="' . esc_attr($options->playlistId)
                . '" data-pagesize="' . intval($options->pageSize)
                . '" data-pagetoken="' . esc_attr($nextPageToken)
                . '" data-style="' . esc_attr($options->style)
                . '" data-epcolumns="' . intval($options->columns)
                . '" data-showtitle="' . intval($options->showTitle)
                . '" data-showpaging="' . intval($options->showPaging)
                . '" data-autonext="' . intval($options->autonext)
                . '" data-hidethumbimg="' . intval($options->hidethumbimg)
                . '" data-thumbplay="' . intval($options->thumbplay)
                . ((self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0 && $options->style == 'listview') ? '" data-showdsc="' . intval($options->showDsc) : '')
                . ((self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 9 && !in_array($options->thumbcrop, array('box', ''))) ? '" data-thumbcrop="' . $options->thumbcrop : '')
                . '"><div>' . $txtnext . '</div> <div class="epyt-arrow">&raquo;</div></div>';

        $pagination .= '<div class="epyt-loader"><img alt="loading" width="16" height="11" src="' . plugins_url('images/gallery-page-loader.gif', __FILE__) . '"></div>';

        $pagination .= '</div>';

//        if ($options->showPaging == 0)
//        {
//            $pagination = '<div class="epyt-pagination"></div>';
//        }
        $code = $pagination . $code . $pagination;

        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0 && self::$alloptions[self::$opt_spdc] == 1 && !(bool) self::$alloptions[self::$opt_gdpr_consent]
                && !empty($jsonResult->items))
        {
            $daysecs = self::$alloptions[self::$opt_spdcexp] * 60 * 60;
            set_transient($spdckey, $code, $daysecs);
            $allk = get_option(self::$spdcall, array());
            $allk[] = $spdckey;
            update_option(self::$spdcall, $allk);

            //self::debuglog((microtime(true) - $time_start) . "\t" . $spdckey . "\t" . $code . "\r\n");
        }

        $gallobj->html = $code;
        $gallobj->init_id = $init_id;
        return $gallobj;
    }

    public static function compare_vid_date($a, $b)
    {
        if ($a->snippet->publishedAt == $b->snippet->publishedAt)
        {
            return 0;
        }
        return ($a->snippet->publishedAt > $b->snippet->publishedAt) ? -1 : 1;
    }

    public static function get_thumbnail_html($thumb, $options)
    {
        $escId = esc_attr($thumb->id);
        $code = '';
        $styleclass = '';
        $rawstyle = '';
        $dschtml = '';
        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0)
        {
            if ($options->style == 'listview')
            {
                $styleclass = 'epyt-listview';
                //fdasfsadfsad
                $dschtml = isset($thumb->dsc) && !empty($thumb->dsc) ? '<div class="epyt-gallery-dsc">' . esc_html($thumb->dsc) . '</div>' : '';
            }
            else if ($options->style == 'carousel')
            {
                $rawstyle = ' style="width: ' . (100.0 / floatval(min($options->pageSizeReal, $options->pageSize))) . '%;" ';
            }
        }

        $show_thumbimg = $options->style == 'carousel' || !$options->hidethumbimg;

        $code .= '<div tabindex="0" role="button" data-videoid="' . $escId . '" class="epyt-gallery-thumb ' . $styleclass . ($show_thumbimg ? '' : ' epyt-hidethumbimg ') . '" ' . $rawstyle . '>';

        if ($show_thumbimg)
        {
            $code .= (self::gdpr_mode() ? '<div class="epyt-gallery-img-box"><div class="epyt-gallery-img epyt-gallery-img-gdpr">' :
                            '<div class="epyt-gallery-img-box"><div class="epyt-gallery-img" style="background-image: url(' . esc_attr($thumb->img) . ')">') .
                    '<div class="epyt-gallery-playhover"><img alt="play" class="epyt-play-img" width="30" height="23" src="' . plugins_url('images/playhover.png', __FILE__) . '" data-no-lazy="1" data-skipgform_ajax_framebjll="" /><div class="epyt-gallery-playcrutch"></div></div>' .
                    '</div></div>';
        }

        if ($options->style != 'carousel' && !empty($thumb->title))
        {
            $code .= '<div class="epyt-gallery-title">' . esc_html($thumb->title) . '</div>';
        }
        else
        {
            $code .= '<div class="epyt-gallery-notitle"><span>' . esc_html($thumb->title) . '</span></div>';
        }
        $code .= $dschtml . '</div>';


        return $code;
    }

    public static function my_embedplus_gallery_page()
    {
        if (self::is_ajax())
        {
            //check_ajax_referer('embedplus-nonce', 'security');
            $options = (object) $_POST['options'];
            $options->apiKey = self::$alloptions[self::$opt_apikey];
            echo self::get_gallery_page($options)->html;
            die();
        }
    }
    
    public static function get_video_details($video_id_list, $api_parts, $use_cache = true)
    {
        $error = null;
        try
        {
            $video_id_list_concat = implode(',', $video_id_list);
            $api_endpoint = 'https://www.googleapis.com/youtube/v3/videos?maxResults=50&safeSearch=none&videoEmbeddable=true&part=' . $api_parts . '&id=' . $video_id_list_concat . '&key=' . self::$alloptions[self::$opt_apikey];

            $cache_key = self::$spdcprefix . '_' . md5($api_endpoint);

            if ($use_cache)
            {
                $cache_val = get_transient($cache_key);
                if (!empty($cache_val))
                {
                    return $cache_val;
                }
            }

            $api_response = wp_remote_get($api_endpoint, array('timeout' => self::$curltimeout, 'headers' => array('referer' => site_url())));
            if (!is_wp_error($api_response))
            {
                $raw = wp_remote_retrieve_body($api_response);
                if (!empty($raw))
                {
                    $json = json_decode($raw, true);
                    if (!isset($json['error']) && is_array($json))
                    {
                        $cache_lifetime = 2 * MINUTE_IN_SECONDS;
                        $items = $json['items'];
                        if (!empty($json['items']) && count($json['items']))
                        {
                            usort($items, array(self::class, 'compare_scheduled_date'));
                            
//                            for ($i = 0; $i < count($items); $i++)
//                            {
//                                $recentSoon = abs();
//                                if ($recentSoon < $cache_lifetime)
//                                {
//                                    $cache_lifetime = MINUTE_IN_SECONDS / 2.0;
//                                    break;
//                                }
//                            }
                        }
                        else
                        {
                            $items = self::$no_streams;
                        }
                        set_transient($cache_key, $items, $cache_lifetime);
                        self::save_cache_key($cache_key);
                        return $items;
                    }
                    else if (isset($json['error']))
                    {
                        $error = new WP_Error('ytapi', $json['error']['message'], $json);
                    }
                }
            }
            else
            {
                $error = $api_response;
            }
        }
        catch (Exception $ex)
        {
            $error = new WP_Error('ytapi', $ex->getMessage(), $ex);
        }
        return $error;
    }

    public static function compare_scheduled_date($a, $b)
    {
        if ($a['liveStreamingDetails']['scheduledStartTime'] == $b['liveStreamingDetails']['scheduledStartTime'])
        {
            return 0;
        }
        return ($a['liveStreamingDetails']['scheduledStartTime'] < $b['liveStreamingDetails']['scheduledStartTime']) ? -1 : 1;
    }

    public static function save_cache_key($cache_key)
    {
        $allk = get_option(self::$spdcall, array());
        $allk[] = $cache_key;
        update_option(self::$spdcall, $allk);
    }

    public static function get_channel_streams($channel_id, $event_type, $use_cache = true)
    {
        $error = null;
        try
        {
            $api_endpoint = 'https://www.googleapis.com/youtube/v3/search?order=date&maxResults=50&type=video&safeSearch=none&videoEmbeddable=true&part=snippet'
                    . '&channelId=' . $channel_id
                    . '&eventType=' . $event_type
                    . '&key=' . self::$alloptions[self::$opt_apikey];

            $cache_key = self::$spdcprefix . '_' . md5($api_endpoint);
            $items_search = null;
            $items_details = null;

            if ($use_cache)
            {
                $items_search = get_transient($cache_key);
            }
            if (empty($items_search))
            {
                $api_response = wp_remote_get($api_endpoint, array('timeout' => self::$curltimeout, 'headers' => array('referer' => site_url())));
                if (!is_wp_error($api_response))
                {
                    $raw = wp_remote_retrieve_body($api_response);
                    $json = json_decode($raw, true);
                    if (!isset($json['error']) && is_array($json))
                    {
                        $items_search = $json['items'];
                        if (empty($items_search))
                        {
                            $items_search = self::$no_streams;
                        }
                        set_transient($cache_key, $items_search, 3 * HOUR_IN_SECONDS);
                        self::save_cache_key($cache_key);
                    }
                    else if (isset($json['error']))
                    {
                        return new WP_Error('ytapi', $json['error']['message'], $json);
                    }
                }
                else
                {
                    $error = $api_response;
                    return $error;
                }                    
            }
            
            

            if (!empty($items_search) && $items_search != self::$no_streams && is_array($items_search))
            {
                $item_ids = array();
                for ($i = 0; $i < count($items_search); $i++)
                {
                    $item_ids[] = $items_search[$i]['id']['videoId'];
                }
                $items_details = self::get_video_details($item_ids, 'snippet,liveStreamingDetails');
            }

            return $items_details;
        }
        catch (Exception $ex)
        {
            $error = new WP_Error('ytapi', $ex->getMessage(), $ex);
        }
        return $error;
    }

    public static function get_html($m, $iscontent, $isoverride)
    {
        //$time_start = microtime(true);

        $link = trim(str_replace(self::$badentities, self::$goodliterals, $m[0]));

        $link = preg_replace('/\s/', '', $link);
        $linkparamstemp = explode('?', $link);

        $linkparams = array();
        if (count($linkparamstemp) > 1)
        {
            $linkparams = self::keyvalue($linkparamstemp[1], true);
        }
        if (strpos($linkparamstemp[0], 'youtu.be') !== false && !isset($linkparams['v']))
        {
            $vtemp = explode('/', $linkparamstemp[0]);
            $linkparams['v'] = array_pop($vtemp);
        }

        if (isset($linkparams['live']) && $linkparams['live'] == '1')
        {
            $live_error_msg = __(' To embed live videos, please make sure you performed the <a href="https://www.youtube.com/watch?v=ZCfrNvu6nMc" target="_blank">steps in this video</a> to create and save a proper server API key.', 'text_domain');
            if (isset(self::$alloptions[self::$opt_apikey]))
            {
                // old channel method
                if (isset($linkparams['channel']))
                {
                    $linkparams['live_stream'] = 1;
                    if (false) // takes up too much quota;
                    {
                        try
                        {
                            $ytapilink_live = 'https://www.googleapis.com/youtube/v3/search?order=date&maxResults=1&type=video&eventType=live&safeSearch=none&videoEmbeddable=true&channelId=' . $linkparams['channel'] . '&part=snippet&key=' . self::$alloptions[self::$opt_apikey];
                            $apidata_live = wp_remote_get($ytapilink_live, array('timeout' => self::$curltimeout, 'headers' => array('referer' => site_url())));
                            if (!is_wp_error($apidata_live))
                            {
                                $raw = wp_remote_retrieve_body($apidata_live);
                                if (!empty($raw))
                                {
                                    $json = json_decode($raw, true);
                                    if (!isset($json['error']) && is_array($json) && count($json['items']))
                                    {
                                        $linkparams['v'] = $json['items'][0]['id']['videoId'];
                                    }
                                    else if (isset($json['error']))
                                    {
                                        return $live_error_msg; // . ' <em>(Error code ' . $json['error']->code . ': ' . $json['error']->message . ')</em>';
                                    }
                                }
                            }
                        }
                        catch (Exception $ex)
                        {
                            return $live_error_msg;
                        }
                    }
                }
                else if (isset($linkparams['v']))
                {
                    ////////////////////// process single video live stream
                    try
                    {
                        if (self::$alloptions[self::$opt_not_live_on])
                        {
                            // if not_live_content isn't being used, just process as a normal single video. otherwise: if not currently live (nor upcoming?), unset $linkparams['v']
                            $not_live_content = trim(htmlspecialchars_decode(wp_strip_all_tags(self::$alloptions[self::$opt_not_live_content], true), ENT_QUOTES));
                            if (!empty($not_live_content))
                            {
                                $ytapilink_live = 'https://www.googleapis.com/youtube/v3/videos?id=' . $linkparams['v'] . '&part=snippet&key=' . self::$alloptions[self::$opt_apikey];
                                $apidata_live = wp_remote_get($ytapilink_live, array('timeout' => self::$curltimeout, 'headers' => array('referer' => site_url())));
                                if (!is_wp_error($apidata_live))
                                {
                                    $raw = wp_remote_retrieve_body($apidata_live);
                                    if (!empty($raw))
                                    {
                                        $json = json_decode($raw, true);
                                        if (!isset($json['error']) && is_array($json) && count($json['items']))
                                        {
                                            if (isset($json['items'][0]['snippet']['liveBroadcastContent']) && $json['items'][0]['snippet']['liveBroadcastContent'] != 'live')
                                            {
                                                unset($linkparams['v']);
                                            }
                                        }
                                        else if (isset($json['error']))
                                        {
                                            return $live_error_msg; // . ' <em>(Error code ' . $json['error']->code . ': ' . $json['error']->message . ')</em>';
                                        }
                                    }
                                }
                            }
                        }
                    }
                    catch (Exception $ex)
                    {
                        return $live_error_msg;
                    }
                }
            }
            else
            {
                return $live_error_msg;
            }

            if (!isset($linkparams['v']) && !isset($linkparams['live_stream']))
            {
                return apply_filters('ytprefs_filter_the_content_light', wp_kses_post(self::$alloptions[self::$opt_not_live_content]));
            }
        }

        if (stripos($linkparamstemp[0], 'live_stream') !== false)
        {
            $linkparams['live_stream'] = 1;

            $channel_streams_now = self::get_channel_streams($linkparams['channel'], 'live');
            if (is_wp_error($channel_streams_now))
            {
                return self::clean_api_error_html($channel_streams_now->get_error_message(), true);
            }            
            $channel_streams_upcoming = self::get_channel_streams($linkparams['channel'], 'upcoming');
            if (is_wp_error($channel_streams_upcoming))
            {
                return self::clean_api_error_html($channel_streams_upcoming->get_error_message(), true);
            }                       
            
            $channel_streams_data = array();
            
            if (!empty($channel_streams_now) && $channel_streams_now != self::$no_streams)
            {
                //$now_utc = str_replace('+00:00', 'Z', date('c'));
                for ($i = 0; $i < count($channel_streams_now); $i++)
                {
                    $this_stream = new stdClass();
                    $this_stream->id = $channel_streams_now[$i]["id"];
                    $this_stream->start = isset($channel_streams_now[$i]['liveStreamingDetails']['scheduledStartTime']) ? $channel_streams_now[$i]['liveStreamingDetails']['scheduledStartTime']: null;
                    $this_stream->end = isset($channel_streams_now[$i]['liveStreamingDetails']['scheduledEndTime']) ? $channel_streams_now[$i]['liveStreamingDetails']['scheduledEndTime']: null;
                    $this_stream->actualStart = isset($channel_streams_now[$i]['liveStreamingDetails']['actualStartTime']) ? $channel_streams_now[$i]['liveStreamingDetails']['actualStartTime'] : null;
                    $this_stream->actualEnd = isset($channel_streams_now[$i]['liveStreamingDetails']['actualEndTime']) ? $channel_streams_now[$i]['liveStreamingDetails']['actualEndTime'] : null;
                    $this_stream->liveBroadcastContent = isset($channel_streams_now[$i]['snippet']['liveBroadcastContent']) ? $channel_streams_now[$i]['snippet']['liveBroadcastContent'] : null;
                    $channel_streams_data[] = $this_stream;
                }
            }
            if (!empty($channel_streams_upcoming) && $channel_streams_upcoming != self::$no_streams)
            {
                //$now_utc = str_replace('+00:00', 'Z', date('c'));
                for ($i = 0; $i < count($channel_streams_upcoming); $i++)
                {
                    $this_stream = new stdClass();
                    $this_stream->id = $channel_streams_upcoming[$i]["id"];
                    $this_stream->start = isset($channel_streams_upcoming[$i]['liveStreamingDetails']['scheduledStartTime']) ? $channel_streams_upcoming[$i]['liveStreamingDetails']['scheduledStartTime']: null;
                    $this_stream->end = isset($channel_streams_upcoming[$i]['liveStreamingDetails']['scheduledEndTime']) ? $channel_streams_upcoming[$i]['liveStreamingDetails']['scheduledEndTime'] : null; 
                    $this_stream->actualStart = isset($channel_streams_upcoming[$i]['liveStreamingDetails']['actualStartTime']) ? $channel_streams_upcoming[$i]['liveStreamingDetails']['actualStartTime'] : null;
                    $this_stream->actualEnd = isset($channel_streams_upcoming[$i]['liveStreamingDetails']['actualEndTime']) ? $channel_streams_upcoming[$i]['liveStreamingDetails']['actualEndTime']: null;
                    $this_stream->liveBroadcastContent = isset($channel_streams_upcoming[$i]['snippet']['liveBroadcastContent']) ? $channel_streams_upcoming[$i]['snippet']['liveBroadcastContent']: null;
                    $channel_streams_data[] = $this_stream;
                }
            }
            
            if (empty($channel_streams_data))
            {
                return '<div class="epyt-video-wrapper epyt-do-live-fallback" data-channel="' . esc_attr($linkparams['channel']) . '"></div>';
            }
            else
            {
                $linkparams['live'] = '1';
                $linkparams['streams'] = base64_encode(json_encode($channel_streams_data, JSON_PRETTY_PRINT));
            }
        }

        $youtubebaseurl = 'youtube';
        $schemaorgoutput = '';
        $voloutput = '';
        $dynsrc = '';
        $dyntype = '';
        $acctitle = '';
        $disptype = '';
        $beginlb = '';
        $endlb = '';
        $disptypeif = '';
        $relstop = '';

        $finalparams = $linkparams + self::$alloptions;

        $spdckey = '';
        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0 && self::$alloptions[self::$opt_spdc] == 1 && !isset($finalparams['live']) && !(bool) self::$alloptions[self::$opt_gdpr_consent])
        {
            try
            {
                $kparams = $finalparams;
                $kparams['iscontent'] = $iscontent;
                ksort($kparams);
                $jparams = json_encode($kparams);
                $spdckey = self::$spdcprefix . '_' . md5($jparams);
                $spdcval = get_transient($spdckey);
                if (!empty($spdcval))
                {
                    //self::debuglog((microtime(true) - $time_start) . "\t" . $spdckey . "\t" . $spdcval . "\r\n");
                    return $spdcval;
                }
            }
            catch (Exception $ex)
            {
                
            }
        }

        self::init_dimensions($link, $linkparams, $finalparams);

        if (self::$alloptions[self::$opt_nocookie] == 1)
        {
            $youtubebaseurl = 'youtube-nocookie';
        }

        if (self::$alloptions[self::$opt_defaultvol] == 1)
        {
            $voloutput = ' data-vol="' . self::$alloptions[self::$opt_vol] . '" ';
        }

        if (is_numeric(self::$alloptions[self::$opt_rel]) && intval(self::$alloptions[self::$opt_rel]) === -1)
        {
            $relstop = ' data-relstop="1" ';
        }

        if (!empty($finalparams[self::$opt_loop]))
        {
            $relstop = '';
        }

        if (self::$alloptions[self::$opt_dohl] == 1)
        {
            $locale = get_locale();
            $finalparams[self::$opt_hl] = $locale;
        }
        else
        {
            unset($finalparams[self::$opt_hl]);
        }

        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 10)
        {

            if (self::$alloptions[self::$opt_schemaorg] == 1 && isset($finalparams['v']))
            {
                $schemaorgoutput = self::getschemaorgoutput($finalparams['v']);
            }

            if (self::$alloptions[self::$opt_dynload] == 1 && stripos($_SERVER['REQUEST_URI'], 'vcv-ajax=1') === false)
            {
                $dynsrc = 'data-ep-';
                $dyntype = ' data-ep-a="' . self::$alloptions[self::$opt_dyntype] . '" ';
            }

            if (isset($linkparams[self::$opt_vol]) && is_numeric(trim($linkparams[self::$opt_vol])))
            {
                $voloutput = ' data-vol="' . $linkparams[self::$opt_vol] . '" ';
            }

            if (isset($linkparams[self::$opt_rel]) && is_numeric($linkparams[self::$opt_rel]) && intval($linkparams[self::$opt_rel]) === -1)
            {
                $relstop = ' data-relstop="1" ';
            }

            if (!empty($finalparams[self::$opt_loop]))
            {
                $relstop = '';
            }

            if (isset($finalparams['layout']) && strtolower($finalparams['layout']) == 'gallery' && isset($finalparams['list']) && isset($finalparams[self::$opt_gallery_disptype]) && $finalparams[self::$opt_gallery_disptype] === 'lb')
            {
                $finalparams[self::$opt_autoplay] = 0;
                $disptype = ' epyt-lb';
                $beginlb = '<div class="lity-hide">';
                $endlb = '</div>';
                $disptypeif = ' epyt-lbif';
                $dynsrc = 'data-lb';
            }
        }
        else
        {
            if (isset($finalparams[self::$opt_vol]))
            {
                unset($finalparams[self::$opt_vol]);
            }
            if (isset($finalparams[self::$opt_gallery_disptype]))
            {
                unset($finalparams[self::$opt_gallery_disptype]);
            }
        }

        $centercode = '';
        if ($finalparams[self::$opt_center] == 1)
        {
            $centercode = ' style="display: block; margin: 0px auto;" ';
        }

        if (self::$alloptions[self::$opt_acctitle] == 1)
        {
            try
            {
                //attr escape
                if (self::$oembeddata)
                {
                    $acctitle = self::$oembeddata->title;
                }
                else
                {

                    if (isset($linkparams['list']))
                    {
                        $odata = self::get_oembed('https://youtube.com/playlist?list=' . $linkparams['list'], 1920, 1280);
                        if (is_object($odata) && isset($odata->title))
                        {
                            $acctitle = $odata->title;
                        }
                    }
                    else if (isset($linkparams['v']))
                    {
                        $odata = self::get_oembed('https://youtube.com/watch?v=' . $linkparams['v'], 1920, 1280);
                        if (is_object($odata) && isset($odata->title))
                        {
                            $acctitle = $odata->title;
                        }
                    }
                }

                if ($acctitle)
                {
                    $acctitle = ' title="' . esc_attr($acctitle) . '" ';
                }
                else
                {
                    $acctitle = ' title="' . __('YouTube player', 'text_domain') . '" ';
                }
            }
            catch (Exception $e)
            {
                
            }
        }
        else
        {
            $acctitle = ' title="' . __('YouTube player', 'text_domain') . '" ';
        }

        // playlist cleanup
        $videoidoutput = isset($linkparams['v']) ? $linkparams['v'] : '';

        if ((self::$alloptions[self::$opt_playlistorder] == 1 || isset($finalparams['plindex'])) && isset($finalparams['list']))
        {
            try
            {
                $videoidoutput = '';
                if (isset($finalparams['plindex']))
                {
                    $finalparams['index'] = intval($finalparams['plindex']);
                }
            }
            catch (Exception $ex)
            {
                
            }
        }

        $galleryWrapper1 = '';
        $galleryWrapper2 = '';
        $galleryCode = '';
        $galleryid_ifm_data = '';
        if (isset($finalparams['layout']) && strtolower($finalparams['layout']) == 'gallery' && isset($finalparams['list']))
        {
            $gallery_options = new stdClass();
            $gallery_options->playlistId = $finalparams['list'];
            $gallery_options->pageToken = null;
            $gallery_options->pageSize = $finalparams[self::$opt_gallery_pagesize];
            $gallery_options->columns = intval($finalparams[self::$opt_gallery_columns]);
            $gallery_options->showTitle = intval($finalparams[self::$opt_gallery_showtitle]);
            $gallery_options->showPaging = intval($finalparams[self::$opt_gallery_showpaging]);
            $gallery_options->autonext = intval($finalparams[self::$opt_gallery_autonext]);
            $gallery_options->hidethumbimg = intval($finalparams[self::$opt_gallery_hidethumbimg]);
            $gallery_options->thumbplay = intval($finalparams[self::$opt_gallery_thumbplay]);
            $gallery_options->showDsc = intval($finalparams[self::$opt_gallery_showdsc]);
            $gallery_options->style = $finalparams[self::$opt_gallery_style];
            $gallery_options->thumbcrop = $finalparams[self::$opt_gallery_thumbcrop];
            $gallery_options->apiKey = self::$alloptions[self::$opt_apikey];

            $galleryid = 'epyt_gallery_' . rand(10000, 99999);
            $galleryid_ifm_data = ' data-epytgalleryid="' . $galleryid . '" ';

            $subbutton = '';
            if (isset($finalparams[self::$opt_gallery_channelsub]) && $finalparams[self::$opt_gallery_channelsub] == 1)
            {
                $subbutton = '<div class="epyt-gallery-subscribe"><a target="_blank" class="epyt-gallery-subbutton" href="' .
                        esc_url(self::$alloptions[self::$opt_gallery_channelsublink]) . '?sub_confirmation=1"><img alt="subscribe" src="' . plugins_url('images/play-subscribe.png', __FILE__) . '" />' .
                        htmlspecialchars(self::$alloptions[self::$opt_gallery_channelsubtext], ENT_QUOTES, 'UTF-8') . '</a></div>';
            }


            $gallery_page_obj = self::get_gallery_page($gallery_options);

            $galleryWrapper1 = '<div class="epyt-gallery ' . $disptype . '" data-currpage="1" id="' . $galleryid . '">';
            $galleryWrapper2 = '</div>';
            $galleryCode = $subbutton . '<div class="epyt-gallery-list epyt-gallery-style-' . esc_attr($gallery_options->style) . '">' .
                    $gallery_page_obj->html .
                    '</div>';
            $videoidoutput = isset($gallery_page_obj->init_id) ? $gallery_page_obj->init_id : '';
        }

        if (!empty($voloutput) && isset($finalparams['autoplay']) && $finalparams['autoplay'] == 1)
        {
            $voloutput .= ' data-epautoplay="1" ';
            $finalparams['autoplay'] = 0;
        }

        if (!empty($relstop) && isset($finalparams['rel']) && intval($finalparams['rel']) === -1)
        {
            $finalparams['rel'] = 0;
        }

        if (!empty($finalparams['live_stream']))
        {
            $videoidoutput = 'live_stream';
        }

        $begin_live_chat = '';
        $begin_live_chat_video = '';
        $begin_live_chat_box = '';
        $end_live_chat_box = '';
        $end_live_chat_video = '';
        $end_live_chat = '';
        if (((!empty($finalparams['live']) && $finalparams['live'] == 1) || !empty($finalparams['live_stream'])) && !empty($finalparams[self::$opt_live_chat]) && $finalparams[self::$opt_live_chat] == 1)
        {
            $begin_live_chat = '<div class="epyt-live-chat-wrapper ' . ($iscontent && $finalparams[self::$opt_gb_compat] == 1 && current_theme_supports('responsive-embeds') ? ' wp-block-embed' : '') . '">';
            $begin_live_chat_video = '<div class="epyt-live-chat-video">';
            $begin_live_chat_box = '<div class="epyt-live-chat-box">' . (!empty($finalparams[self::$opt_facade_mode]) && $finalparams[self::$opt_facade_mode] == 1 ? '<p>' . __('Click on the video to activate live chat.', 'text_domain') . '</p>' : '' );
            $end_live_chat = $end_live_chat_box = $end_live_chat_video = '</div>';
        }

        $begin_responsive = '';
        $end_responsive = '';
        $dim_attrs = ' width="' . self::$defaultwidth . '" height="' . self::$defaultheight . '" ';

        if ($finalparams[self::$opt_responsive] == 1)
        {
            $begin_responsive = '<div class="epyt-video-wrapper">';
            $end_responsive = '</div>';
        }

        $begin_gb_wrapper = '';
        $end_gb_wrapper = '';
        if ($iscontent && !$isoverride && $finalparams[self::$opt_gb_compat] == 1 && empty($begin_live_chat) && empty($beginlb) && current_theme_supports('responsive-embeds'))// self::using_gutenberg())
        {
            // don't do the following if: overriding default YT, is widget, is livechat, is popup
            $begin_gb_wrapper = '<figure class="wp-block-embed wp-block-embed-youtube is-type-video is-provider-youtube epyt-figure"><div class="wp-block-embed__wrapper">';
            //wp-block-embed-youtube is-type-video is-provider-youtube wp-embed-aspect-16-9 wp-has-aspect-ratio
            $end_gb_wrapper = '</div></figure>';
        }
        $iframe_id = rand(10000, 99999);

        $code1 = $begin_gb_wrapper . $beginlb . $begin_live_chat . $begin_live_chat_video . $begin_responsive;
        $code_iframe1 = $code_iframe2 = '';
        if ($videoidoutput != 'live_stream' && stripos($disptype, 'epyt-lb') === false && $finalparams[self::$opt_facade_mode] == 1)
        {
            $facade_img_src = '';
            if (!empty($videoidoutput))
            {
                $facade_img_src = ' src="https://i.ytimg.com/vi/' . $videoidoutput . ($finalparams[self::$opt_maxres_facade] == 'eager' ? '/maxresdefault.jpg' : '/hqdefault.jpg') . '" ';
            }
            else if (isset($finalparams['list']))
            {
                $facade_img_src = ' data-facadeoembed="playlist?list=' . $finalparams['list'] . '" ';
            }
            $acctitle = str_replace('title="', 'alt="', $acctitle);
            $facade_autoplay = $finalparams[self::$opt_facade_autoplay] == 1 ? ' data-epautoplay="1" ' : '';
            $code_iframe1 = '<div ' . $dyntype . $centercode . ' id="_ytid_' . $iframe_id . '" ' . $dim_attrs . ' data-origwidth="' . self::$defaultwidth . '" data-origheight="' . self::$defaultheight . '" ' . $relstop .
                    'data-facadesrc="https://www.' . $youtubebaseurl . '.com/embed/' . $videoidoutput . '?';
            $code_iframe2 = '" class="__youtube_prefs__ epyt-facade ' . (!empty($finalparams['live_stream']) || !empty($finalparams['streams']) ? ' epyt-live-channel ' : '') . ($iscontent ? '' : ' __youtube_prefs_widget__ ') . ($isoverride ? ' epyt-is-override ' : '') . $disptypeif . ' no-lazyload"' .
                    $voloutput . $galleryid_ifm_data . $facade_autoplay . (!empty($finalparams['streams']) ? ' data-streams="' . $finalparams['streams'] . '" ' : '') . '><img data-spai-excluded="true" class="epyt-facade-poster skip-lazy" loading="lazy" ' . $acctitle . $facade_img_src . ' />' .
                    '<button class="epyt-facade-play" aria-label="Play"><svg data-no-lazy="1" height="100%" version="1.1" viewBox="0 0 68 48" width="100%"><path class="ytp-large-play-button-bg" d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z" fill="#f00"></path><path d="M 45,24 27,14 27,34" fill="#fff"></path></svg></button>' .
                    '</div>';
        }
        else
        {
            $code_iframe1 = '<iframe ' . $dyntype . $centercode . ' id="_ytid_' . $iframe_id . '" ' . $dim_attrs . ' data-origwidth="' . self::$defaultwidth . '" data-origheight="' . self::$defaultheight . '" ' . $relstop .
                    (!empty($finalparams['streams']) ? 'data-' : $dynsrc) . 'src="https://www.' . $youtubebaseurl . '.com/embed/' . $videoidoutput . '?';
            $code_iframe2 = '" class="__youtube_prefs__ ' . (!empty($finalparams['live_stream']) || !empty($finalparams['streams']) ? ' epyt-live-channel ' : '') . ($iscontent ? '' : ' __youtube_prefs_widget__ ') . ($isoverride ? ' epyt-is-override ' : '') . $disptypeif . ' no-lazyload"' .
                    $voloutput . $acctitle . $galleryid_ifm_data . (!empty($finalparams['streams']) ? ' data-streams="' . $finalparams['streams'] . '" ' : '') . ' allow="fullscreen; accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen data-no-lazy="1" data-skipgform_ajax_framebjll=""></iframe>';
        }

        $code2 = $end_responsive . $end_live_chat_video . $begin_live_chat_box . $end_live_chat_box . $end_live_chat . $endlb . $end_gb_wrapper . $schemaorgoutput;

        $code1 .= $code_iframe1;
        $code2 = $code_iframe2 . $code2;

        $origin = '';

        try
        {
            if (self::$alloptions[self::$opt_origin] == 1)
            {
                $url_parts = parse_url(site_url());
                $origin = 'origin=' . $url_parts['scheme'] . '://' . $url_parts['host'] . '&';
            }
        }
        catch (Exception $e)
        {
            $origin = '';
        }
        $finalsrc = (self::$alloptions[self::$opt_ytapi_load] == 'never' ? '' : 'enablejsapi=1&') . $origin;

        if (count($finalparams) > 1)
        {
            foreach ($finalparams as $key => $value)
            {
                if (in_array($key, self::$yt_options))
                {
                    if (!empty($galleryCode) && ($key == 'listType' || $key == 'list'))
                    {
                        
                    }
                    else
                    {
                        if (!((isset($finalparams['live']) || isset($finalparams['live_stream'])) && $key == 'loop')) // don't add loop for channel streaming
                        {
                            $finalsrc .= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '=' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '&';
                            if ($key == 'loop' && $value == 1 && !isset($finalparams['list']))
                            {
                                $finalsrc .= 'playlist=' . $finalparams['v'] . '&';
                            }
                        }
                    }
                }
            }
        }

        if (self::gdpr_mode())
        {
            $code1 = $beginlb . '<div ' . $dyntype . $centercode . ' id="_ytid_' . rand(10000, 99999) . '"'; // width="' . self::$defaultwidth . '" height="' . self::$defaultheight . '" ';
            $code2 = ' class="__youtube_prefs__  __youtube_prefs_gdpr__ ' . ($iscontent ? '' : ' __youtube_prefs_widget__') . ($isoverride ? ' epyt-is-override ' : '') . $disptypeif . '" allowfullscreen data-no-lazy="1" data-skipgform_ajax_framebjll="">' .
                    apply_filters('ytprefs_filter_the_content_light', wp_kses_post(self::$alloptions[self::$opt_gdpr_consent_message])) .
                    '<button type="button" class="__youtube_prefs_gdpr__">' . trim(sanitize_text_field(self::$alloptions[self::$opt_gdpr_consent_button])) .
                    '<img src="' . plugins_url('images/icon-check.png', __FILE__) . '" alt="accept" data-no-lazy="1" data-skipgform_ajax_framebjll="" /></button>' .
                    '</div>' . $endlb . $schemaorgoutput;
            $finalsrc = '';
        }


        $code = $galleryWrapper1 . $code1 . $finalsrc . $code2 . $galleryCode . $galleryWrapper2;
        //. '<!--' . $m[0] . '-->';
        self::$defaultheight = null;
        self::$defaultwidth = null;
        self::$oembeddata = null;


        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0 && self::$alloptions[self::$opt_spdc] == 1 && !isset($finalparams['live']) && !(bool) self::$alloptions[self::$opt_gdpr_consent])
        {
            $exp = self::$alloptions[self::$opt_spdcexp] * 60 * 60;
//            if (isset($finalparams['live']) && $finalparams['live'] == 1)
//            {
//                $exp = self::$alloptions[self::$opt_spdcexp_live];
//            }
            set_transient($spdckey, $code, $exp);
            $allk = get_option(self::$spdcall, array());
            $allk[] = $spdckey;
            update_option(self::$spdcall, $allk);

            //self::debuglog((microtime(true) - $time_start) . "\t" . $spdckey . "\t" . $code . "\r\n");
        }
        return $code;
    }

    public static function using_gutenberg()
    {
        global $wp_version;
        if ((version_compare($wp_version, '5.0', '>=') && !is_plugin_active('classic-editor/classic-editor.php')) || is_plugin_active('gutenberg/gutenberg.php'))
        {
            return true;
        }
        return false;
    }

    public static function gdpr_mode()
    {
        return (bool) self::$alloptions[self::$opt_gdpr_consent] && filter_input(INPUT_COOKIE, self::$gdpr_cookie_name, FILTER_SANITIZE_NUMBER_INT) != 1;
    }

    public static function filter_the_content_light($content)
    {
        //global $wp_filter;
        //$the_content_filters_current = $wp_filter['the_content']->callbacks;

        for ($i = 0; $i < count(self::$the_content_filters); $i++)
        {
            if (function_exists(self::$the_content_filters[$i]) && !(self::wp_above_version('5.5') && self::$the_content_filters[$i] === 'wp_make_content_images_responsive'))
            {
                $content = call_user_func(self::$the_content_filters[$i], $content);
            }
        }
        return $content;
    }

    public static function debuglog($str)
    {
        $handle = fopen(__DIR__ . "\\debug.txt", "a+");
        fwrite($handle, $str);
        fclose($handle);
    }

    public static function spdcpurge()
    {
        global $wpdb;

        $allk = get_option(self::$spdcall, array());
        if (is_array($allk))
        {
            foreach ($allk as $t)
            {
                $success = delete_transient($t);
            }
        }
        update_option(self::$spdcall, array());

        // hard delete for db transients
        $sql = "delete from " . $wpdb->options . " where option_name like '\_transient\_" . self::$spdcprefix . "%' or option_name like '\_transient\_timeout\_" . self::$spdcprefix . "%'";
        $wpdb->query($sql);
    }

    public static function keyvalue($qry, $includev)
    {
        $ytvars = explode('&', $qry);
        $ytkvp = array();
        foreach ($ytvars as $k => $v)
        {
            $kvp = explode('=', $v);
            if (count($kvp) == 2 && ($includev || strtolower($kvp[0]) != 'v'))
            {
                $ytkvp[$kvp[0]] = $kvp[1];
            }
        }

        return $ytkvp;
    }

    public static function getschemaorgoutput($vidid)
    {
        $schemaorgcode = '';
        try
        {
            $ytapilink = 'https://www.googleapis.com/youtube/v3/videos?id=' . $vidid . '&part=contentDetails,snippet&key=' . self::$alloptions[self::$opt_apikey];


            $apidata = wp_remote_get($ytapilink, array('timeout' => self::$curltimeout, 'headers' => array('referer' => site_url())));
            if (!is_wp_error($apidata))
            {
                $raw = wp_remote_retrieve_body($apidata);
                if (!empty($raw))
                {
                    $json = json_decode($raw, true);
                    if (is_array($json))
                    {
                        $_name = esc_attr(sanitize_text_field(str_replace("@", "&#64;", isset($json['items'][0]['snippet']['title']) ? $json['items'][0]['snippet']['title'] : '')));
                        $_description = esc_attr(sanitize_text_field(str_replace("@", "&#64;", isset($json['items'][0]['snippet']['description']) ? $json['items'][0]['snippet']['description'] : '')));
                        $_description = empty($_description) ? __('YouTube video', 'text_domain') : $_description;
                        $_thumbnailUrl = esc_url("https://i.ytimg.com/vi/" . $vidid . "/0.jpg");
                        $_duration = isset($json['items'][0]['contentDetails']['duration']) ? $json['items'][0]['contentDetails']['duration'] : ''; // "T0H9M35S" "PT9M35S"
                        $_uploadDate = sanitize_text_field(isset($json['items'][0]['snippet']['publishedAt']) ? $json['items'][0]['snippet']['publishedAt'] : ''); // "2014-10-03T15:30:12.000Z"

                        $schemaorgcode = '<span itemprop="video" itemscope itemtype="http://schema.org/VideoObject">';
                        $schemaorgcode .= '<meta itemprop="embedUrl" content="https://www.youtube.com/embed/' . $vidid . '">';
                        $schemaorgcode .= '<meta itemprop="name" content="' . $_name . '">';
                        $schemaorgcode .= '<meta itemprop="description" content="' . $_description . '">';
                        $schemaorgcode .= '<meta itemprop="thumbnailUrl" content="' . $_thumbnailUrl . '">';
                        $schemaorgcode .= '<meta itemprop="duration" content="' . $_duration . '">';
                        $schemaorgcode .= '<meta itemprop="uploadDate" content="' . $_uploadDate . '">';
                        $schemaorgcode .= '</span>';
                    }
                }
            }
        }
        catch (Exception $ex)
        {
            
        }
        return $schemaorgcode;
    }

    public static function secondsToDuration($seconds)
    {
        $remaining = $seconds;
        $parts = array();
        $multipliers = array(
            'hours' => 3600,
            'minutes' => 60,
            'seconds' => 1
        );

        foreach ($multipliers as $type => $m)
        {
            $parts[$type] = (int) ($remaining / $m);
            $remaining -= ($parts[$type] * $m);
        }

        return $parts;
    }

    public static function formatDuration($parts)
    {
        $default = array(
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0
        );

        extract(array_merge($default, $parts));

        return "T{$hours}H{$minutes}M{$seconds}S";
    }

    public static function init_dimensions($url, $urlkvp, $finalparams)
    {
        // get default dimensions; try embed size in settings, then try theme's content width, then just 480px
        if (self::$defaultwidth == null)
        {
            global $content_width;
            if (empty($content_width))
            {
                $content_width = $GLOBALS['content_width'];
            }

            if (isset($urlkvp['width']) && is_numeric($urlkvp['width']))
            {
                self::$defaultwidth = $urlkvp['width'];
            }
            else if (self::$alloptions[self::$opt_defaultdims] == 1 && (isset(self::$alloptions[self::$opt_defaultwidth]) && is_numeric(self::$alloptions[self::$opt_defaultwidth])))
            {
                self::$defaultwidth = self::$alloptions[self::$opt_defaultwidth];
            }
            else if (self::$optembedwidth)
            {
                self::$defaultwidth = self::$optembedwidth;
            }
            else if ($content_width)
            {
                self::$defaultwidth = $content_width;
            }
            else
            {
                self::$defaultwidth = 480;
            }



            if (isset($urlkvp['height']) && is_numeric($urlkvp['height']))
            {
                self::$defaultheight = $urlkvp['height'];
            }
            else if (self::$alloptions[self::$opt_defaultdims] == 1 && (isset(self::$alloptions[self::$opt_defaultheight]) && is_numeric(self::$alloptions[self::$opt_defaultheight])))
            {
                self::$defaultheight = self::$alloptions[self::$opt_defaultheight];
            }
            else
            {
                self::$defaultheight = self::get_aspect_height($url, self::$defaultwidth);
            }
        }
    }

    public static function get_oembed($url, $height, $width)
    {
        if (stripos($url, 'listType=playlist') !== false && stripos($url, '/embed') !== false)
        {
            $url = str_replace('/embed', '/playlist', $url);
        }
        if (file_exists(ABSPATH . WPINC . '/class-wp-oembed.php'))
        {
            require_once(ABSPATH . WPINC . '/class-wp-oembed.php');
        }
        else
        {
            require_once(ABSPATH . WPINC . '/class-oembed.php');
        }
        $oembed = _wp_oembed_get_object();
        $args = array();
        $args['width'] = $width;
        $args['height'] = $height;
        $args['discover'] = false;
        self::$oembeddata = $oembed->fetch('https://www.youtube.com/oembed', $url, $args);
        return self::$oembeddata;
    }

    public static function get_aspect_height($url, $widthbox)
    {
        // attempt to get aspect ratio correct height from oEmbed
        $aspectheight = round(($widthbox * 9) / 16, 0);

        if ($url)
        {
            $odata = self::get_oembed($url, $widthbox, $widthbox);

            if ($odata)
            {
                $aspectheight = $odata->height;
            }
        }

        return $aspectheight;
    }

    public static function doftpostimg($new_status, $old_status, $post)
    {
        if (current_user_can('edit_posts'))
        {
            if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !in_array($new_status, array('publish', 'pending', 'draft', 'future', 'private')))
            {
                return;
            }
            try
            {
                self::doftpostimgfor($post);
            }
            catch (Exception $ex)
            {
                // display error message
            }
        }
    }

//    public static function doftpostimg($postid, $post, $update)
//    {
//        if (current_user_can('edit_posts'))
//        {
//            if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !in_array($post->post_status, array('publish', 'pending', 'draft', 'future', 'private')))
//            {
//                return;
//            }
//            try
//            {
//                self::doftpostimgfor($post);
//            }
//            catch (Exception $ex)
//            {
//                // display error message
//            }
//        }
//    }

    public static function doftpostimgfor($post)
    {
        $search_content = isset($post->post_content) ? $post->post_content : '';
        $search_content = substr(wp_strip_all_tags($search_content), 0, 5000);

        $search_content = apply_filters('youtube_embedplus_video_content', $search_content);

        $vid_match = null;
        if ($search_content && $post->ID && !has_post_thumbnail($post->ID) && preg_match(self::$justurlregex, $search_content, $vid_match))
        {

            $first_vid_link = trim(str_replace(self::$badentities, self::$goodliterals, $vid_match[0]));

            $first_vid_link = preg_replace('/\s/', '', $first_vid_link);
            $linkparamstemp = explode('?', $first_vid_link);

            $linkparams = array();
            if (count($linkparamstemp) > 1)
            {
                $linkparams = self::keyvalue($linkparamstemp[1], true);
            }
            if (strpos($linkparamstemp[0], 'youtu.be') !== false && !isset($linkparams['v']))
            {
                $vtemp = explode('/', $linkparamstemp[0]);
                $linkparams['v'] = array_pop($vtemp);
            }

            $just_id = isset($linkparams['v']) ? $linkparams['v'] : null;

            if ($just_id == null && isset($linkparams['list']))
            {
                $apiEndpoint = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,status&playlistId=' . $linkparams['list']
                        . '&maxResults=10&key=' . self::$alloptions[self::$opt_apikey];
                try
                {
                    $apiResult = wp_remote_get($apiEndpoint, array('timeout' => self::$curltimeout, 'headers' => array('referer' => site_url())));
                    if (!is_wp_error($apiResult))
                    {
                        $jsonResult = json_decode($apiResult['body']);
                        if (!isset($jsonResult->error) && isset($jsonResult->items) && $jsonResult->items != null && is_array($jsonResult->items))
                        {

                            $item = $jsonResult->items[0];
                            $just_id = isset($item->snippet->resourceId->videoId) ? $item->snippet->resourceId->videoId : null;
                            $just_id = $just_id ? $just_id : $item->id->videoId;
                        }
                    }
                }
                catch (Exception $ex)
                {
                    
                }
            }

            if ($just_id != null)
            {
                $ftimgurl = "https://img.youtube.com/vi/" . $just_id . "/maxresdefault.jpg";
                $ftimgid = self::media_sideload($ftimgurl, $post->ID, sanitize_title(preg_replace("/[^a-zA-Z0-9\s]/", "-", $post->post_title)));

                if (!$ftimgid || is_wp_error($ftimgid))
                {
                    $ftimgurl = null;
                    $ftimgid = 0;
                    if ($just_id)
                    {
                        if (file_exists(ABSPATH . WPINC . '/class-wp-oembed.php'))
                        {
                            require_once(ABSPATH . WPINC . '/class-wp-oembed.php');
                        }
                        else
                        {
                            require_once(ABSPATH . WPINC . '/class-oembed.php');
                        }
                        $oembed = _wp_oembed_get_object();
                        $args = array();
                        $args['width'] = 1920;
                        $args['height'] = 1080;
                        $args['discover'] = false;
                        $odata = $oembed->fetch('https://www.youtube.com/oembed', 'http://youtube.com/watch?v=' . $just_id, $args);

                        if ($odata)
                        {
                            $ftimgurl = $odata->thumbnail_url;
                        }
                    }

                    $ftimgid = $ftimgurl && !is_wp_error($ftimgurl) ? self::media_sideload($ftimgurl, $post->ID, sanitize_title(preg_replace("/[^a-zA-Z0-9\s]/", "-", $post->title))) : 0;

                    if (!$ftimgid || is_wp_error($ftimgid))
                    {
                        return;
                    }
                }
                set_post_thumbnail($post->ID, $ftimgid);
            }
        }
    }

    public static function media_sideload($url, $post_id, $filename = null)
    {
        if (!$url || !$post_id)
        {
            return new WP_Error('missing', __('Please provide a valid URL and post ID', 'text_domain'));
        }

        $post_title = get_the_title($post_id);

        require_once(ABSPATH . 'wp-admin/includes/file.php');
        $tmp = download_url($url);

        if (is_wp_error($tmp))
        {
            @unlink($file_array['tmp_name']);
            $file_array['tmp_name'] = '';
            return $tmp;
        }

        preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $url, $matches);
        $url_filename = basename($matches[0]);
        $url_type = wp_check_filetype($url_filename);

        if (!empty($filename))
        {
            $filename = sanitize_file_name($filename);
            $tmppath = pathinfo($tmp);
            $new = $tmppath['dirname'] . '/' . $filename . '.' . $tmppath['extension'];
            rename($tmp, $new);
            $tmp = $new;
        }

        $file_array['tmp_name'] = $tmp;
        if (!empty($filename))
        {
            $file_array['name'] = $filename . '.' . $url_type['ext'];
        }
        else
        {
            $file_array['name'] = $url_filename;
        }

        $post_data = array(
            'post_title' => $post_title,
            'post_parent' => $post_id,
        );

        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $att_id = media_handle_sideload($file_array, $post_id, null, $post_data);

        if (is_wp_error($att_id))
        {
            @unlink($file_array['tmp_name']);
            return $att_id;
        }

        return $att_id;
    }

    public static function do_ogvideo()
    {
        global $wp_query;
        if (isset($wp_query) && isset($wp_query->post) && isset($wp_query->post->post_content))
        {
            $the_content = $wp_query->post->post_content;
            $matches = array();
            $ismatch = preg_match_all(self::$justurlregex, $the_content, $matches);

            if ($ismatch)
            {
                $match = $matches[0][0];

                $link = trim(preg_replace('/&amp;/i', '&', $match));
                $link = preg_replace('/\s/', '', $link);
                $link = trim(str_replace(self::$badentities, self::$goodliterals, $link));

                $linkparamstemp = explode('?', $link);

                $linkparams = array();
                if (count($linkparamstemp) > 1)
                {
                    $linkparams = self::keyvalue($linkparamstemp[1], true);
                }
                if (strpos($linkparamstemp[0], 'youtu.be') !== false && !isset($linkparams['v']))
                {
                    $vtemp = explode('/', $linkparamstemp[0]);
                    $linkparams['v'] = array_pop($vtemp);
                }

                if (isset($linkparams['v']))
                {
                    ?>
                    <!--                <meta property="og:type" content="video.other">-->
                    <meta property="og:video:url" content="https://www.youtube.com/v/<?php echo esc_attr($linkparams['v']); ?>?version=3">
                    <meta property="og:video:type" content="application/x-shockwave-flash">
                    <meta property="og:video:width" content="480">
                    <meta property="og:video:height" content="360">
                    <meta property="og:image" content="https://img.youtube.com/vi/<?php echo esc_attr($linkparams['v']); ?>/0.jpg">
                    <?php
                }
            }
        }
    }

    public static function ytprefs_plugin_menu()
    {
        if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0)
        {
            self::$admin_page_hooks[] = add_menu_page(__('YouTube Settings', 'text_domain'), __('YouTube PRO', 'text_domain'), 'manage_options', 'youtube-my-preferences', array(self::class, 'ytprefs_show_options'), 'dashicons-video-alt3', '10.000392854349');
            self::$admin_page_hooks[] = add_submenu_page('youtube-my-preferences', '', '', 'manage_options', 'youtube-my-preferences', array(self::class, 'ytprefs_show_options'));
            self::$admin_page_hooks[] = add_submenu_page('youtube-my-preferences', __('YouTube Analytics Dashboard', 'text_domain'), '<span class="wp-menu-image dashicons-before dashicons-chart-line"></span> ' . __('PRO Analytics', 'text_domain'), 'manage_options', 'youtube-ep-analytics-dashboard', array(self::class, 'epstats_show_options'));

            include_once(EPYTVI_INCLUDES_PATH . 'vi_admin_menu.php');
        }
        else
        {
            self::$admin_page_hooks[] = add_menu_page(__('YouTube Settings', 'text_domain'), __('YouTube', 'text_domain'), 'manage_options', 'youtube-my-preferences', array(self::class, 'ytprefs_show_options'), 'dashicons-video-alt3', '10.000392854349');
            self::$admin_page_hooks[] = add_submenu_page('youtube-my-preferences', '', '', 'manage_options', 'youtube-my-preferences', array(self::class, 'ytprefs_show_options'));
        }
        self::$admin_page_hooks[] = add_submenu_page('youtube-my-preferences_nomenu', __('YouTube Posts', 'text_domain'), __('YouTube Posts', 'text_domain'), 'manage_options', 'youtube-ep-glance', array(self::class, 'glance_page'));
        self::$admin_page_hooks[] = self::$wizard_hook = add_submenu_page('youtube-my-preferences_nomenu', __('YouTube Wizard', 'text_domain'), __('YouTube Wizard', 'text_domain'), 'edit_posts', 'youtube-ep-wizard', array(self::class, 'wizard'));
        self::$admin_page_hooks[] = self::$onboarding_hook = add_submenu_page('youtube-my-preferences_nomenu', __('YouTube Setup', 'text_domain'), __('YouTube Setup', 'text_domain'), 'manage_options', 'youtube-ep-onboarding', array(self::class, 'ytprefs_show_onboarding'));
    }

    public static function remove_stats_validation()
    {
        if (isset(self::$alloptions[self::$opt_pro]) && strlen(self::$alloptions[self::$opt_pro]) > 8)
        {
            $stats_validate_file = self::vi_get_home_path() . 'embedplus' . strtolower(self::$alloptions[self::$opt_pro]) . '.html';
            $stats_validate_file_upper = self::vi_get_home_path() . 'embedplus' . strtoupper(self::$alloptions[self::$opt_pro]) . '.html';
            if (file_exists($stats_validate_file) && is_writable($stats_validate_file))
            {
                @unlink($stats_validate_file);
            }
            if (file_exists($stats_validate_file_upper) && is_writable($stats_validate_file_upper))
            {
                @unlink($stats_validate_file_upper);
            }

            if (file_exists($stats_validate_file) || file_exists($stats_validate_file_upper))
            {
                return false;
            }
            return true;
        }
        else
        {
            return true;
        }
    }

    public static function epstats_show_options()
    {

        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.', 'text_domain'));
        }

        if (self::$double_plugin)
        {
            self::double_plugin_warning();
        }

        if (!empty($_POST) && check_admin_referer('_epyt_save', '_epyt_nonce'))
        {
            // process form data
            if (filter_input(INPUT_POST, 'dashboard_off', FILTER_SANITIZE_NUMBER_INT) == 1)
            {
                $new_options = array(
                    self::$opt_dashpre => 0
                );
                self::update_option_set($new_options);
            }
        }

        // Now display the settings editing screen
        ?>
        <style type="text/css">
            .epicon { width: 20px; height: 20px; vertical-align: middle; padding-right: 5px;}
            .epindent {padding-left: 25px;}
            h1 span {vertical-align: middle;}
            .bold {font-weight: bold;}
            .orange {color: #f85d00;} 

        </style>
        <div class="wrap">
            <?php
            $thishost = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "");
            $thiskey = self::$alloptions[self::$opt_pro];

            //$dashurl = self::$epbase . "/dashboard/pro-easy-video-analytics.aspx?ref=protab&domain=" . $thishost . "&prokey=" . $thiskey;
            $dashurl = self::$epbase . "/dashboard/dashboard.aspx?domain=";

            if (self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0)
            {
                //// header
                echo '<h1><span class="dashicons-before dashicons-chart-line"></span> ' . __('YouTube Analytics Dashboard', 'text_domain') . "</h1>";
                ?>
                <div class="ytindent chx">
                    <p>
                        <?php _e('This page allows you to administer your YouTube Analytics Dashboard feature.', 'text_domain'); ?>
                    </p>

                    <?php
                    if ((bool) self::$alloptions[self::$opt_dashpre])
                    {
                        $remove_validate = self::remove_stats_validation();
                        ?>
                        <p>
                            Confirm and click below to load your dashboard. Or, <a href="<?php echo esc_attr($dashurl) ?>" target="_blank">click here</a> to access the dashboard in a separate tab.
                            <?php
                            if ($remove_validate == false)
                            {
                                ?>
                            <p>
                                <strong>Note:</strong> You no longer need your validation file so you can now delete it (e.g. <?php echo esc_html(site_url('/embedplus' . self::$alloptions[self::$opt_pro] . '.html')) ?> ).
                            </p>
                            <?php
                        }
                        ?>
                        </p>
                        <p>
                            <label><em><input type="checkbox" class="epyt-load-dashboard-confirm"/> I understand that the dashboard loaded from EmbedPlus.com will uses cookies to log me into the account I used to sign up.</em></label>
                        </p>
                        <p>
                            <button disabled type="button" class="button-primary epyt-load-dashboard">Load dashboard</button>
                        </p>
                        <p>
                        <form  method="post" action="">
                            <?php wp_nonce_field('_epyt_save', '_epyt_nonce'); ?>
                            <input type="hidden" value="1" name="dashboard_off" />
                            If you would like to deactivate the analytics feature, <button type="submit" class="button button-small epyt-dashboard-off">click here</button>
                        </form>
                        </p>
                        <?php
                    }
                    else
                    {
                        ?>
                        <p>
                            If your Pro plan includes the YouTube Analytics Dashboard feature, you can click the below button to load your dashboard from EmbedPlus.com and begin the validation process.
                        </p>
                        <p>
                            If you are not sure if your Pro plan comes with analytics, check your emailed purchase receipt for something like "YouTube Plugin Pro <strong>+ Analytics Subscription</strong>."
                        </p>
                        <p>
                            <label><em><input type="checkbox" class="epyt-load-dashboard-confirm"/> I understand that dashboard loaded from EmbedPlus.com will uses cookies to log me into the account I used to sign up.</em></label>
                        </p>
                        <p>
                            <button disabled type="button" class="button-primary epyt-load-dashboard">Load dashboard to login and validate analytics</button>
                        </p>
                        <?php
                    }
                    ?>
                    <iframe class="epyt-dashboard-content" data-src="<?php echo esc_attr($dashurl) ?>" width="1060" height="3600" scrolling="auto"/>

                </div>                


                <?php
            }
            else
            {
                //// header
                echo "<h2>" . '<img alt="YouTube Plugin Wizard" style="vertical-align: text-bottom;" src="' . plugins_url('images/iconwizard.png', __FILE__) . '" /> ' . __('YouTube Plugin PRO') . "</h2><p class='bold orange'>This tab is here to provide direct access to analytics. Graphs and other data about your site will show below after you activate PRO.</p><br>";
            }
            ?>

        </div>
        <?php
    }

    public static function my_embedplus_pro_record()
    {
        $result = array();
        if (self::is_ajax())
        {
            $tmppro = preg_replace('/[^A-Za-z0-9-]/i', '', $_REQUEST[self::$opt_pro]);
            $new_options = array();
            $new_options[self::$opt_pro] = $tmppro;
            $all = get_option(self::$opt_alloptions);
            $all = $new_options + $all;
            update_option(self::$opt_alloptions, $all);

            if (strlen($tmppro) > 0)
            {
                $result['type'] = 'success';
            }
            else
            {
                $result['type'] = 'error';
            }
            echo json_encode($result);
        }
        else
        {
            $result['type'] = 'error';
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
        die();
    }

    public static function my_embedplus_dashpre()
    {
        $result = array();
        if (self::is_ajax())
        {
            try
            {
                $tmp = intval($_REQUEST[self::$opt_dashpre]);
                $new_options = array();
                $new_options[self::$opt_dashpre] = $tmp;
                $all = get_option(self::$opt_alloptions);
                $all = $new_options + $all;
                update_option(self::$opt_alloptions, $all);
            }
            catch (Exception $ex)
            {
                
            }
        }
        die();
    }

    public static function my_embedplus_yt_dash()
    {
        if (self::is_ajax())
        {
            $qstring = $_POST['qstring'];
            try
            {
                $url = (strpos(self::$epbase, 'http') === false ? 'https:' : '') . self::$epbase . '/yt-dash/?' . $qstring;
                $result = wp_remote_get($url, array('timeout' => 1000));
                $result = null;
            }
            catch (Exception $ex)
            {
                
            }
        }
        die();
    }

    public static function my_embedplus_clearspdc()
    {
        $result = array();
        if (self::is_ajax())
        {
            try
            {
                self::spdcpurge();
                $result['type'] = 'success';
            }
            catch (Exception $ex)
            {
                $result['type'] = 'error';
            }
            echo json_encode($result);
        }
        else
        {
            $result['type'] = 'error';
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
        die();
    }
    
    public static function rss_lifetime($lifetime, $filename)
    {
        if (preg_match("/.+[.]local$/i", site_url()))
        {
            return 1;
        }
        return 10;
    }

    public static function realtimeLiveCheck()
    {
        $result = array();
        if (self::is_ajax())
        {
            try
            {
                $channelId = $_POST["channelId"];
                add_filter( 'wp_feed_cache_transient_lifetime' , array(self::class, 'rss_lifetime'), 10, 2);
                $rss = fetch_feed("https://www.youtube.com/feeds/videos.xml?channel_id=" . $channelId . '&rand=' . time());
                remove_filter( 'wp_feed_cache_transient_lifetime' , array(self::class, 'rss_lifetime'));
                if ($rss && !is_wp_error($rss))
                {
                    $streams = $_POST['streams'];
                    $rss_items = $rss->get_items();
                    $recently_updated = array();
                    
                    foreach ($rss_items as $item)
                    {
                        $last_updated = $item->get_item_tags('http://www.w3.org/2005/Atom', 'updated')[0]["data"];
                        $videoId = $item->get_item_tags('http://www.youtube.com/xml/schemas/2015', 'videoId')[0]['data'];
                        $now = new DateTime("UTC");
                        $last_updated_date = new DateTime($last_updated, new DateTimeZone("UTC"));
                        $diff = $now->getTimestamp() - $last_updated_date->getTimestamp();
                        $threeHours = 3 * HOUR_IN_SECONDS;
                        if ($diff < $threeHours)
                        {
                            $recently_updated[] = $videoId;
                        }
                    }
                    if (!empty($recently_updated))
                    {
                        $possible_streams = self::get_video_details($recently_updated, 'snippet,liveStreamingDetails');
                        $fresh_live = array();
                        foreach ($possible_streams as $ps)
                        {
                            if (!empty($ps['snippet']['liveBroadcastContent']) && $ps['snippet']['liveBroadcastContent'] == 'live' && self::is_fresh_live($streams, $ps))
                            {
                                $fresh_live[] = $ps;
                            }
                        }
                        if (!empty($fresh_live))
                        {
                            self::spdcpurge();
                            $result['fresh_live'] = $fresh_live;
                        }
                    }
                }
                $result['type'] = 'success';
            }
            catch (Exception $ex)
            {
                $result['type'] = 'error';
            }
            echo json_encode($result);
        }
        else
        {
            $result['type'] = 'error';
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
        die();
    }
    
    public static function is_fresh_live($known_streams, $possible_live)
    {
        foreach ($known_streams as $known)
        {
            if ($possible_live['id'] == $known['id'])
            {
                return false;
            }
        }
        return true;
    }

    public static function custom_admin_pointers_check()
    {
        if (!self::$alloptions[self::$opt_show_pointer])
        {
            return false;
        }
        $admin_pointers = self::custom_admin_pointers();
        foreach ($admin_pointers as $pointer => $array)
        {
            if ($array['active'])
            {
                return true;
            }
        }
    }

    public static function glance_script()
    {
        add_thickbox();
        ?>
        <script type="text/javascript">
            function widen_ytprefs_glance()
            {
                setTimeout(function ()
                {
                    jQuery("#TB_window").animate({marginLeft: '-' + parseInt((780 / 2), 10) + 'px', width: '780px'}, 300);
                    jQuery("#TB_window iframe").animate({width: '780px'}, 300);
                }, 15);
            }

            (function ($j)
            {
                $j(document).ready(function ()
                {

                    $j.ajax({
                        type: "post",
                        dataType: "json",
                        timeout: 30000,
                        url: window._EPYTA_ ? window._EPYTA_.wpajaxurl : ajaxurl,
                        data: {action: 'my_embedplus_glance_count'},
                        success: function (response)
                        {
                            if (response.type === "success")
                            {
                                $j(response.container).append(response.data);
                                $j(".ytprefs_glance_button").on('click', widen_ytprefs_glance);
                                $j(window).on('resize', widen_ytprefs_glance);
                                if (typeof ep_do_pointers === 'function')
                                {
                                    //ep_do_pointers($j);
                                }
                            }
                            else
                            {
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError)
                        {

                        },
                        complete: function ()
                        {
                        }
                    });
                });
            })(jQuery);</script>
        <?php
    }

    public static function custom_admin_pointers_footer()
    {
        $admin_pointers = self::custom_admin_pointers();
        ?>
        <script type="text/javascript">
            /* <![CDATA[ */
            function ep_do_pointers($)
            {
        <?php
        foreach ($admin_pointers as $pointer => $array)
        {
            if ($array['active'])
            {
                ?>
                        $('<?php echo $array['anchor_id']; ?>').pointer({
                            pointerClass: 'wp-pointer ytprefs-pointer',
                            content: '<?php echo $array['content']; ?>',
                            position: {
                                edge: '<?php echo $array['edge']; ?>',
                                align: '<?php echo $array['align']; ?>'
                            },
                            close: function ()
                            {
                                $.post(window._EPYTA_ ? window._EPYTA_.wpajaxurl : ajaxurl, {
                                    pointer: '<?php echo $pointer; ?>',
                                    action: 'dismiss-wp-pointer'
                                });
                            }
                        }).pointer('open');
                <?php
            }
        }
        ?>
            }

            ep_do_pointers(jQuery); // switch off all pointers via js ooopointer
            /* ]]> */
        </script>
        <?php
    }

    public static function custom_admin_pointers()
    {
        $dismissed = explode(',', (string) get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));
        $version = str_replace('.', '_', self::$version); // replace all periods in version with an underscore
        $prefix = 'custom_admin_pointers' . $version . '_';

        $new_pointer_content = '<h3>' . __('New Update') . '</h3>'; // ooopointer

        $new_pointer_content .= '<p>'; // ooopointer
        $new_pointer_content .= "This update addresses issues with the channel-based automatic live stream detection/loading feature for the Pro version, and provides better compatibility with PHP 8.3+ for both Free and Pro versions.";
        if (self::vi_logged_in())
        {
            $new_pointer_content .= "<br><br><strong>Note:</strong> You are currently logged into the vi intelligence feature. vi support is being deprecated in the next version, so we recommend taking the vi ads down from your site. Please contact ext@embedplus.com for questions.";
        }
        $new_pointer_content .= '</p>';

        return array(
            $prefix . 'new_items' => array(
                'content' => $new_pointer_content,
                'anchor_id' => 'a.toplevel_page_youtube-my-preferences', //'#ytprefs_glance_button', 
                'edge' => 'top',
                'align' => 'left',
                'active' => (!in_array($prefix . 'new_items', $dismissed))
            ),
        );
    }

    public static function postchecked($idx)
    {
        return isset($_POST[$idx]) && $_POST[$idx] == (true || 'on');
    }

    public static function settings_nav()
    {
        ?>

        <h3 class="nav-tab-wrapper">
            <a class="nav-tab <?php echo isset(self::$alloptions[self::$opt_pro]) && strlen(trim(self::$alloptions[self::$opt_pro])) > 0 ? '' : 'nav-tab-active' ?>" href="#jumppro">PRO Key</a>
            <a class="nav-tab" href="#jumpapikey">API Key</a>
            <a class="nav-tab <?php echo isset(self::$alloptions[self::$opt_pro]) && strlen(trim(self::$alloptions[self::$opt_pro])) > 0 ? 'nav-tab-active' : '' ?>" href="#jumpdefaults">Defaults</a>
            <a class="nav-tab" href="#jumpwiz">Wizard</a>
            <a class="nav-tab" href="#jumpgallery">Galleries</a>
            <a class="nav-tab" href="#jumpprosettings">PRO Settings</a>
            <?php
            if (false)//(!(bool) (self::$alloptions[self::$opt_vi_hide_monetize_tab]) && self::vi_ever_logged_in())
            {
                if (self::vi_logged_in())
                {
                    ?>
                    <a class="nav-tab href-link nav-tab-invalid" href="<?php echo admin_url('admin.php?page=youtube-ep-vi') ?>">Monetize</a>
                    <?php
                }
                else
                {
                    ?>
                    <a class="nav-tab nav-tab-invalid" href="#jumpmonetize">Monetize</a>
                    <?php
                }
            }
            ?>
            <a class="nav-tab" href="#jumpperformance">Performance</a>
            <a class="nav-tab" href="#jumpcompat">Compatibility</a>
            <a class="nav-tab" href="#jumpprivacy">Security & Privacy</a>
            <a class="nav-tab" href="#jumphowto">Embed Manually</a>
            <a class="nav-tab" href="#jumpsupport">Support</a>
        </h3>

        <?php
    }

    public static function ytprefs_show_options()
    {

        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        if (self::$double_plugin)
        {
            self::double_plugin_warning();
        }


        $ytprefs_submitted = 'ytprefs_submitted';

        // Read in existing option values from database

        $all = get_option(self::$opt_alloptions);

        // See if the user has posted us some information
        // If they did, this hidden field will be set to 'Y'
        if (isset($_POST[$ytprefs_submitted]) && $_POST[$ytprefs_submitted] == 'Y' && check_admin_referer('_epyt_save', '_epyt_nonce'))
        {
            $result = self::settings_save($all);
            $all = get_option(self::$opt_alloptions);
            ?>
            <div class="updated"><p><strong><?php echo wp_kses_post($result['message']) ?></strong></p></div>
            <?php
        }
        ?>

        <style type="text/css">
            .wrap {font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",Arial,sans-serif; color: #000000;}
            .wrap-ytprefs {max-width: 1064px;}
            h1 span {vertical-align: middle;}
            #ytform p, #ytform .p { line-height: 20px; margin: 18px 0; }
            .ytindent {padding: 0px 0px 0px 20px; font-size: 13px; margin-bottom: 100px;}
            .ytindent ul, .ytindent p {font-size: 13px;}
            .shadow {-webkit-box-shadow: 0px 0px 20px 0px #000000; box-shadow: 0px 0px 20px 0px #000000;}
            .gopro {margin: 0px;}
            .gopro img {vertical-align: middle;
                        width: 19px;
                        height: 19px;
                        padding-bottom: 4px;}
            .gopro li {margin-bottom: 0px;}
            .orange {color: #f85d00;}
            .bold {font-weight: bold;}
            .grey{color: #888888;}
            .epyt-notice-info {border-radius: 15px; padding: 10px 15px 15px 15px; border: 3px solid #CCE5EC; position: relative;}
            .epyt-notice-info .epyt-livestream-advice-close { display: none;}
            .pronon {font-weight: bold; color: #f85d00;}
            ul.reglist li {margin-left: 30px; list-style: disc outside none;}
            .procol {width: 475px; float: left;}
            .ytindent .procol ul {font-size: 12px;}
            .smallnote, .ytindent .smallnote {font-style: italic; font-size: 11px;}
            .italic {font-style: italic;}
            .ytindent h3 {font-size: 16px; line-height: 22px; margin: 5px 0px 10px 0px;}
            #wizleftlink {float: left; display: block; width: 240px; font-style: italic; text-align: center; text-decoration: none;}
            .button-primary {white-space: nowrap;}
            p.submit {margin: 10px 0 0 0; padding: 10px 0 5px 0;}
            .wp-core-ui p.submit .button-primary {
                font-weight: bold;
                font-size: 21px; height: 50px; padding: 0 20px 1px;
                background: #2ea2cc; /* Old browsers */
                background: -moz-linear-gradient(top,  #2ea2cc 0%, #007396 98%); /* FF3.6+ */
                background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#2ea2cc), color-stop(98%,#007396)); /* Chrome,Safari4+ */
                background: -webkit-linear-gradient(top,  #2ea2cc 0%,#007396 98%); /* Chrome10+,Safari5.1+ */
                background: -o-linear-gradient(top,  #2ea2cc 0%,#007396 98%); /* Opera 11.10+ */
                background: -ms-linear-gradient(top,  #2ea2cc 0%,#007396 98%); /* IE10+ */
                background: linear-gradient(to bottom,  #2ea2cc 0%,#007396 98%); /* W3C */
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#2ea2cc', endColorstr='#007396',GradientType=0 ); /* IE6-9 */
            }
            .wp-core-ui p.submit .button-primary[disabled] {
                opacity: .4;
                transition: opacity ease-in-out .3s;
            }
            .wp-core-ui p.submit .button-primary[disabled]:after {
                content: url(<?php echo admin_url('images/wpspin_light.gif') ?>);
                padding-left: 3px;
            }
            p.submit em {display: inline-block; padding-left: 20px; vertical-align: middle; width: 240px; margin-top: -6px;}
            #opt_pro {box-shadow: 0px 0px 5px 0px #1870D5; width: 320px;vertical-align: top;}
            #goprobox h3 {font-size: 14px;}
            .chx {border-left: 5px solid rgba(100, 100, 100,.1); margin-bottom: 20px;}
            .chx p {margin: 0px 0px 5px 0px;}
            .cuz {background-image: linear-gradient(to bottom,#4983FF,#0C5597) !important; color: #ffffff;}
            .brightpro {background-image: linear-gradient(to bottom,#ff5500,#cc2200) !important; color: #ffffff;}
            #boxdefaultdims {font-weight: bold; padding: 0px 10px; <?php echo $all[self::$opt_defaultdims] ? '' : 'display: none;' ?>}
            #boxcustomarrows {font-weight: bold; padding: 0px 10px; <?php echo $all[self::$opt_gallery_customarrows] ? 'display: block;' : 'display: none;' ?>}
            #boxchannelsub {font-weight: bold; padding: 0px 10px; <?php echo $all[self::$opt_gallery_channelsub] ? 'display: block;' : 'display: none;' ?>}
            #box_collapse_grid {font-weight: bold; padding: 0px 10px; <?php echo isset($all[self::$opt_gallery_collapse_grid]) && $all[self::$opt_gallery_collapse_grid] ? 'display: block;' : 'display: none;' ?>}
            #box_restrict_wizard {padding: 0px 10px; <?php echo isset($all[self::$opt_restrict_wizard]) && $all[self::$opt_restrict_wizard] ? 'display: block;' : 'display: none;' ?>}
            #box_restrict_wizard label {display: block; margin: 5px 10px;}
            .textinput {border-width: 2px !important;}
            input[type=text]::placeholder {font-weight: normal;}
            h3.sect {border-radius: 10px; background-color: #D9E9F7; padding: 5px 5px 5px 10px; position: relative; font-weight: bold;}
            h3.sect a {text-decoration: none; color: #E20000;}
            h3.sect a.button-primary {color: #ffffff;} 
            h4.sect {border-radius: 10px; background-color: #D9E9F7; padding: 5px 5px 5px 10px; position: relative; font-weight: bold;}

            .nav-tab-wrapper sup {line-height: 0;}
            .ytnav {margin-bottom: 15px;}
            .ytnav a {font-weight: bold; display: inline-block; padding: 5px 10px; margin: 0px 15px 0px 0px; border: 1px solid #cccccc; border-radius: 6px;
                      text-decoration: none; background-color: #ffffff;}
            .ytnav a:last-child {margin-right: 0;}
            .jumper {height: 25px;}
            .ssschema {float: right; width: 350px; height: auto; margin-right: 10px;}
            .ssfb {float: right; height: auto; margin-right: 10px; margin-left: 15px; margin-bottom: 10px;}
            .totop {position: absolute; right: 20px; top: 5px; color: #444444; font-size: 11px;}
            input[type=checkbox] {border: 1px solid #000000;}
            .chktitle {display: inline-block; padding: 1px 5px 1px 5px; border-radius: 3px; background-color: #ffffff; border: 1px solid #dddddd;}
            b, strong {font-weight: bold;}
            input.checkbox[disabled], input[type=radio][disabled] {border: 1px dashed #444444;}
            .pad10 {padding: 10px;}
            #boxdohl {font-weight: bold; padding: 0px 10px;  <?php echo $all[self::$opt_dohl] ? '' : 'display: none;' ?>}
            #boxdyn {font-weight: bold; padding: 0px 10px;  <?php echo $all[self::$opt_dynload] ? 'display: block;' : 'display: none;' ?>}
            #boxspdc {padding: 0px 10px;  border-left: 5px solid #eee;  <?php echo $all[self::$opt_spdc] ? '' : 'display: none;' ?>}
            #boxdefaultvol {font-weight: bold; padding: 0px 10px;  <?php echo $all[self::$opt_defaultvol] ? '' : 'display: none;' ?>}
            .vol-output {display: none; width: 30px; color: #008800;}
            .vol-range {background-color: #dddddd; border-radius: 3px; cursor: pointer;}
            input#vol {vertical-align: middle;}
            .vol-seeslider {display: none;}
            input#spdcexp {width: 70px;}
            .indent-option {margin-left: 25px;}
            #boxschemaorg { padding: 7px 0;  <?php echo $all[self::$opt_schemaorg] ? 'display: block;' : 'display: none;' ?>}
            #boxmigratelist { <?php echo $all[self::$opt_migrate] ? '' : 'display: none;' ?>}
            #boxresponsive_all { <?php echo $all[self::$opt_responsive] ? '' : 'display: none;' ?> padding-left: 25px; border-left: 5px solid rgba(100, 100, 100,.1); margin-left: 5px;}
            .apikey-msg {display: inline-block; vertical-align: top;}
            .apikey-video{margin-left: 3%; display: inline-block; width: 50%; position: relative; padding-top: 29%}
            .apikey-video iframe{display: block; width: 100%; height: 100%; position: absolute; top: 0; left: 0;}
            #boxnocookie {display: inline-block; border-radius: 3px; padding: 2px 4px 2px 4px; color: red;  <?php echo $all[self::$opt_nocookie] ? '' : 'display: none;' ?>}
            #boxapinever {display: none; color: red;}
            input[type="radio"]:checked ~ #boxapinever {display: block;}
            #box_facade_mode { color: red; <?php echo (bool) $all[self::$opt_facade_mode] ? 'display: block;' : 'display: none;' ?>}
            #box_gdpr_consent { color: red; <?php echo (bool) $all[self::$opt_gdpr_consent] ? 'display: block;' : 'display: none;' ?>}
            .strike {text-decoration: line-through;}
            .upgchecks { padding: 20px; border: 1px dotted #777777; background-color: #fcfcfc; }
            .clearboth {clear: both;}
            div.hr {clear: both; border-bottom: 1px dotted #A8BDD8; margin: 20px 0 20px 0;}
            .wp-pointer-buttons a.close {margin-top: 0 !important;}
            .pad20{padding: 20px 0 20px 0;}
            .ssgallery {float: right; width: 130px; height: auto; margin-left: 15px; border: 3px solid #ffffff;}
            .sssubscribe{display: block; width: 400px; height: auto;}
            .ssaltgallery {float: right; height: auto; margin-right: 10px; margin-left: 15px; margin-bottom: 10px; width: 350px;}
            .sspopupplayer {float: right; height: auto; margin-right: 10px; margin-left: 15px; margin-bottom: 10px; width: 350px;}
            .sshidethumbimg {float: right; height: auto; margin-right: 10px; margin-left: 40px; margin-bottom: 10px; width: 315px;}
            .sswizardbutton {    max-width: 70%; height: auto;}
            .save-changes-follow {position: fixed; z-index: 10000; bottom: 0; right: 0; background-color: #ffffff; padding: 0 20px; border-top-left-radius: 20px; border: 2px solid #aaaaaa; border-right-width: 0; border-bottom-width: 0;
                                  -webkit-box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.75);
                                  -moz-box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.75);
                                  box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.75); }
            .alertify .ajs-body .ajs-content {line-height: 2.5em;}
            #jumpmonetize h2:first-child {margin-bottom: 0;}

            .wrap-ytprefs h2.nav-tab-wrapper {
                margin:22px 0 0 0;
            }

            .wrap-ytprefs h3 .nav-tab {
                padding: 5px 10px;
            }

            .wrap-ytprefs section {
                display:none;
                padding-top:15px;
            }
            .wrap-ytprefs section<?php echo isset($all[self::$opt_pro]) && strlen(trim($all[self::$opt_pro])) > 0 ? '#jumpdefaults' : '#jumppro' ?> {
                display:block;
            }
            .wrap-ytprefs .no-js section {
                display: block;
            }

            .gdpr-options-left {
                width: 65%;
                float: left;
                clear: left;
            }

            .gdpr-options-right {
                width: 33%;
                float: right;
                margin-top: 20px;
            }

            .gdpr-options-right .img-gdpr-message {
                width: 100%;
                height: auto;
            }

            iframe#gdpr_consent_message_ifr {
                min-height: 250px !important;
            }

            section#jumpwiz hr {
                margin: 20px 0 20px 0;
            }
            .wiztab-screenshots {
                float: right;
                max-width: 50%;
                height: auto;
                clear: right;
                margin-left: 30px;
                margin-bottom: 15px;
                display: block;
                border: 5px solid #dddddd;
            }

            .epyt-deprecated {
                color: #aaaaaa;
            }

            input[type="checkbox"] + label .check-note {
                display: none;
            }

            input[type="checkbox"]:checked + label .check-note {
                display: inline;
            }

            .not-live-content {
                opacity: .3;
            }

            #not_live_on:checked ~ .not-live-content, #not_live_on_channel:checked ~ .not-live-content {
                opacity: 1;
            }

            #facade_mode ~ .box_facade_mode {
                opacity: .3;
            }

            #facade_mode:checked ~ .box_facade_mode {
                opacity: 1;
            }

            #defer_js ~ .box_defer_jquery {
                opacity: .3;
            }

            #defer_js:checked ~ .box_defer_jquery {
                opacity: 1;
            }

            .epyt-fitvid {
                width: 100%;
                padding-top: 56.25%;
                position: relative;
            }

            .epyt-fitvid iframe {
                position: absolute;
                top: 0;
                left:0;
                right: 0;
                bottom: 0;
                width: 100%;
                height: 100%;
            }

            .wiztab-pagebuilder {
                width: 50%;
                float: left;
                padding: 20px;
                box-sizing: border-box;
            }

        </style>        

        <div class="wrap wrap-ytprefs">
            <h1><span class="dashicons-before dashicons-video-alt3"></span> <?php echo __('YouTube Settings') ?></h1>
            <?php
            self::settings_nav();
            ?>

            <div class="ytindent">
                <section class="pattern" id="jumppro">
                    <h2>Pro Key</h2>
                    <div id="goprobox" class="epyt-notice-info">

                        <?php
                        if (isset($all[self::$opt_pro]) && strlen(trim($all[self::$opt_pro])) > 0)
                        {
                            echo "<h3 class=sect>" . __('Thank you for going PRO.', 'youtube-embed-plus-pro');
                            echo ' &nbsp;<input type="submit" name="showkey" class="button-primary" style="vertical-align: 15%;" id="showprokey" value="View my PRO key" />';
                            echo "</h3>";
                            echo '<p>';
                            _e('If you wish to add more licenses to your current key and activate Pro on other sites, contact us contact us at bizdev@embedplus.com for discounted pricing.', 'youtube-embed-plus-pro');
                            echo '</p>';
                            ?>
                            <?php
                        }
                        else
                        {
                            ?>
                            <h3 class="sect">
                                PRO users help keep new features coming and our coffee cups filled. Go PRO and get these perks in return:
                            </h3>
                            <div class="procol">
                                <ul class="gopro">
                                    <li>
                                        <img src="<?php echo plugins_url('images/iconcache.png', __FILE__) ?>">
                                        Faster Page Loads (Caching)
                                    </li>
                                    <li>
                                        <img src="<?php echo plugins_url('images/iconwizard.png', __FILE__) ?>">
                                        Full Visual Embedding Wizard (Easily customize embeds without memorizing codes)
                                    </li>
                                    <li>
                                        <img src="<?php echo plugins_url('images/icongallery.png', __FILE__) ?>">
                                        Advanced Gallery Customization Options (popup/lightbox player, slider and list layouts, and more)
                                    </li>       
                                    <li>
                                        <img src="<?php echo plugins_url('images/iconfx.png', __FILE__) ?>">
                                        Add eye-catching special effects as your videos load
                                    </li>
                                    <li>
                                        <img src="<?php echo plugins_url('images/deletechecker.png', __FILE__) ?>">
                                        Deleted Video Checker (alerts you if YouTube deletes videos you embedded)
                                    </li>
                                    <li>
                                        <img src="<?php echo plugins_url('images/globe.png', __FILE__) ?>">
                                        Alerts when visitors from different countries are blocked from viewing your embeds
                                    </li>                 
                                    <li>
                                        <img src="<?php echo plugins_url('images/mobilecompat.png', __FILE__) ?>">
                                        Check if your embeds have restrictions that can block mobile viewing
                                    </li>       

                                </ul>
                            </div>
                            <div class="procol" style="max-width: 465px;">
                                <ul class="gopro">
                                    <li>
                                        <img src="<?php echo plugins_url('images/videothumbs.png', __FILE__) ?>">
                                        Featured thumbnail images (just click 'Update')  
                                    </li>       
                                    <li>
                                        <img src="<?php echo plugins_url('images/prioritysupport.png', __FILE__) ?>">
                                        Priority support (Puts your request in front)
                                    </li>
                                    <li>
                                        <img src="<?php echo plugins_url('images/bulletgraph45.png', __FILE__) ?>">
                                        User-friendly video analytics dashboard
                                    </li>

                                    <li id="fbstuff">
                                        <img src="<?php echo plugins_url('images/iconfb.png', __FILE__) ?>">
                                        Automatic Open Graph tagging for Facebook
                                    </li>
                                    <li>
                                        <img src="<?php echo plugins_url('images/vseo.png', __FILE__) ?>">
                                        Automatic tagging for video SEO (will even work for your old embeds)
                                    </li>
                                    <li>
                                        <img src="<?php echo plugins_url('images/iconvolume.png', __FILE__) ?>">
                                        Fine-Grained Volume Initialization – Individual video volume settings in the wizard
                                    </li>       

                                    <li>
                                        <img src="<?php echo plugins_url('images/infinity.png', __FILE__) ?>">
                                        Unlimited PRO upgrades and downloads
                                    </li>
                                    <!--                            <li>
                                                                    <img src="<?php echo plugins_url('images/questionsale.png', __FILE__) ?>">
                                                                    What else? You tell us!                                
                                                                </li>                           -->
                                </ul>
                            </div>
                            <div style="clear: both;"></div>
                            <br>
                            <h3 class="bold">Enter and save your PRO key (emailed to you):</h3>
                            <p>This one-time save will verify your purchase with our EmbedPlus.com servers so that you can receive future upgrades.</p>
                        <?php }
                        ?>
                        <form name="form2" method="post" action="" id="epform2" class="submitpro" <?php
                        if ($all[self::$opt_pro] && strlen(trim($all[self::$opt_pro])) > 0)
                        {
                            echo 'style="display: none;"';
                        }
                        ?>>

                            <input name="<?php echo self::$opt_pro; ?>" id="opt_pro" value="<?php echo esc_attr($all[self::$opt_pro]); ?>" type="text">
                            <input type="submit" name="Submit" class="button-primary" id="prokeysubmit" value="<?php _e('Save Key') ?>" />

                            <br>
                            <span style="display: none;" id="prokeyloading" class="orange bold">Verifying...</span>
                            <span  class="orange bold" style="display: none;" id="prokeysuccess">Success! Please refresh this page. If you wish to add more licenses to your current key and activate Pro on other sites, contact us at contact us at bizdev@embedplus.com for discounted pricing.</span>
                            <span class="orange bold" style="display: none;" id="prokeyfailed">Sorry, that seems to be an invalid key, or it has been used already. If you're behind a firewall, you may need to try activating on another network.</span>
                            <span class="orange bold" style="display: none;" id="prokeycancel">Your request is being processed. Response code: CR1.</span>

                        </form>
                    </div>
                </section>

                <form name="form1" method="post" action="" id="ytform">
                    <input type="hidden" name="<?php echo $ytprefs_submitted; ?>" value="Y">
                    <?php wp_nonce_field('_epyt_save', '_epyt_nonce', true); ?>
                    <section class="pattern" id="jumpapikey">
                        <h2>
                            YouTube API Key
                        </h2>
                        <p>
                            Some features (such as galleries, and some wizard features) now require you to create a free YouTube API key from Google.
                            The instructions for this are very specific, so we created a video for you that's hopefully easy to follow: <a href="https://www.embedplus.com/how-to-create-a-youtube-api-key.aspx" target="_blank">Click this link</a> and follow the steps on the page. Then save your API key below.
                        </p>
                        <p>
                            <b class="chktitle">YouTube API Key:</b> 
                            <input type="text" name="<?php echo self::$opt_apikey; ?>" id="<?php echo self::$opt_apikey; ?>" value="<?php echo esc_attr(trim($all[self::$opt_apikey])); ?>" class="textinput" style="width: 250px;">
                        </p>
                    </section>

                    <section class="pattern" id="jumpdefaults">
                        <h2>
                            <?php _e("Default YouTube Options") ?>
                        </h2>
                        <p>
                            <?php _e("One of the benefits of using this plugin is that you can set site-wide default options for all your videos (click \"Save Changes\" when finished). However, you can also override them (and more) on a per-video basis. Directions on how to do that are in the next tab.") ?>
                        </p>

                        <div class="ytindent chx">
                            <p>
                                <input name="<?php echo self::$opt_glance; ?>" id="<?php echo self::$opt_glance; ?>" <?php checked($all[self::$opt_glance], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_glance; ?>"><?php _e('<b class="chktitle">At a glance:</b> Show "At a Glance" Embed Links on the dashboard homepage.') ?></label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_center; ?>" id="<?php echo self::$opt_center; ?>" <?php checked($all[self::$opt_center], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_center; ?>"><?php _e('<b class="chktitle">Centering:</b> Automatically center all your videos (not necessary if all your videos span the whole width of your blog).') ?></label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_autoplay; ?>" id="<?php echo self::$opt_autoplay; ?>" <?php checked($all[self::$opt_autoplay], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_autoplay; ?>">
                                    <?php _e('<b class="chktitle">Autoplay:</b>  Automatically start playing your videos.') ?>
                                    <strong>Note:</strong> If you're embedding videos from your own monetized YouTube channel, we advise you to read YouTube's resource page on ads on embedded videos:
                                    <a href="https://support.google.com/youtube/answer/132596?hl=en" target="_blank">https://support.google.com/youtube/answer/132596?hl=en</a>
                                    You'll see that videos that you want to monetize "should be embedded using the standard click-to-play embed and NOT a scripted play."
                                    Unchecking this option guarantees standard click-to-play gallery embedding.
                                    (Another Note: Desktop browsers like <a href="https://developers.google.com/youtube/iframe_api_reference#Mobile_considerations" target="_blank">Chrome and Safari are moving towards preventing autoplay for any video</a>. But, your chances are improved if you set your videos to initially start muted.)
                                </label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_iv_load_policy; ?>" id="<?php echo self::$opt_iv_load_policy; ?>" <?php checked($all[self::$opt_iv_load_policy], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_iv_load_policy; ?>"><?php _e('<b class="chktitle">Annotations:</b> Show annotations by default.') ?></label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_loop; ?>" id="<?php echo self::$opt_loop; ?>" <?php checked($all[self::$opt_loop], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_loop; ?>"><?php _e('<b class="chktitle">Looping:</b> Loop all your videos. Note: this feature is incompatible with the "hide related videos" feature.') ?></label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_modestbranding; ?>" id="<?php echo self::$opt_modestbranding; ?>" <?php checked($all[self::$opt_modestbranding], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_modestbranding; ?>"><?php _e('<b class="chktitle">Modest Branding:</b> No YouTube logo will be shown on the control bar.  Instead, as required by YouTube, the logo will only show as a watermark when the video is paused/stopped.') ?></label>
                            </p>
                            <p>
                                <label>
                                    <b class="chktitle">Related Videos:</b>
                                    Show or hide related and recommended videos at the end of playback.
                                    <br>
                                </label>
                                <input type="radio" name="<?php echo self::$opt_rel; ?>" id="<?php echo self::$opt_rel; ?>-1" value="-1" <?php checked($all[self::$opt_rel], -1); ?>>
                                <label for="<?php echo self::$opt_rel; ?>-1">Hide related videos at the end of playback </label> &nbsp;&nbsp;
                                <input type="radio" name="<?php echo self::$opt_rel; ?>" id="<?php echo self::$opt_rel; ?>0" value="0" <?php checked($all[self::$opt_rel], 0); ?>>
                                <label for="<?php echo self::$opt_rel; ?>0">Show related videos only from the video's channel</label> &nbsp;&nbsp;
                                <input type="radio" name="<?php echo self::$opt_rel; ?>" id="<?php echo self::$opt_rel; ?>1" value="1" <?php checked($all[self::$opt_rel], 1); ?>>
                                <label for="<?php echo self::$opt_rel; ?>1">Show related videos</label> &nbsp;&nbsp;
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_fs; ?>" id="<?php echo self::$opt_fs; ?>" <?php checked($all[self::$opt_fs], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_fs; ?>"><?php _e('<b class="chktitle">Show Fullscreen Button:</b> Show the fullscreen button.') ?></label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_acctitle; ?>" id="<?php echo self::$opt_acctitle; ?>" <?php checked($all[self::$opt_acctitle], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_acctitle; ?>"><b class="chktitle">Accessible Title Attributes: </b> Improve accessibility by using title attributes for screen reader support. It should help your site pass functional accessibility evaluations (FAE). </label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_color; ?>" id="<?php echo self::$opt_color; ?>" <?php checked($all[self::$opt_color], 'red'); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_color; ?>"><?php _e('<b class="chktitle">Red Progress Bar:</b> Use the red progress bar (uncheck to use a white progress bar). Note: Using white will disable the modestbranding option.') ?></label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_defaultdims; ?>" id="<?php echo self::$opt_defaultdims; ?>" <?php checked($all[self::$opt_defaultdims], 1); ?> type="checkbox" class="checkbox">                        
                                <span id="boxdefaultdims">
                                    Width: <input type="number" min="200" name="<?php echo self::$opt_defaultwidth; ?>" id="<?php echo self::$opt_defaultwidth; ?>" value="<?php echo esc_attr(trim($all[self::$opt_defaultwidth])); ?>" class="textinput" style="width: 75px;"> &nbsp;
                                    Height: <input type="number" min="200" name="<?php echo self::$opt_defaultheight; ?>" id="<?php echo self::$opt_defaultheight; ?>" value="<?php echo esc_attr(trim($all[self::$opt_defaultheight])); ?>" class="textinput" style="width: 75px;">
                                </span>

                                <label for="<?php echo self::$opt_defaultdims; ?>"><?php _e('<b class="chktitle">Default Dimensions:</b> Make your videos have a default size, for better loading performance and consistency. Recommended: 800 x 450 (NOTE: If responsive sizing is also turned on, your videos will be responsive but also keep this aspect ratio.). Also, according to YouTube guidelines, the player must be a minimum of 200 x 200 (or recommended 480 x 270 for 16:9 ratio players) in order to display correctly.') ?></label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_responsive; ?>" id="<?php echo self::$opt_responsive; ?>" <?php checked($all[self::$opt_responsive], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_responsive; ?>"><?php _e('<b class="chktitle">Responsive Video Sizing:</b> Make your videos responsive so that they dynamically fit in all screen sizes (smart phone, PC and tablet). NOTE: While this is checked, any custom hardcoded widths and heights you may have set will dynamically change too. <b>Uncheck this if your theme already properly handles responsive video sizing.</b>') ?></label>
                            <div id="boxresponsive_all">
                                <input type="radio" name="<?php echo self::$opt_responsive_all; ?>" id="<?php echo self::$opt_responsive_all; ?>1" value="1" <?php checked($all[self::$opt_responsive_all], 1); ?> >
                                <label for="<?php echo self::$opt_responsive_all; ?>1">Responsive for all YouTube videos</label> &nbsp;&nbsp;
                                <input type="radio" name="<?php echo self::$opt_responsive_all; ?>" id="<?php echo self::$opt_responsive_all; ?>0" value="0" <?php checked($all[self::$opt_responsive_all], 0); ?> >
                                <label for="<?php echo self::$opt_responsive_all; ?>0">Responsive for only videos embedded via this plugin</label>
                            </div>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_widgetfit; ?>" id="<?php echo self::$opt_widgetfit; ?>" <?php checked($all[self::$opt_widgetfit], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_widgetfit; ?>"><?php _e('<b class="chktitle">Autofit Widget Videos:</b> Make each video that you embed in a widget area automatically fit the width of its container.') ?></label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_playsinline; ?>" id="<?php echo self::$opt_playsinline; ?>" <?php checked($all[self::$opt_playsinline], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_playsinline; ?>">
                                    <b class="chktitle">iOS Playback:</b> Check this to allow your embeds to play inline within your page when viewed on iOS (iPhone and iPad) browsers. Uncheck it to have iOS launch your embeds in fullscreen instead.
                                    <em>Disclaimer: YouTube/Google has issues with this iOS related parameter, but we are providing it here in the event that they support it consistently.</em>
                                </label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_controls; ?>" id="<?php echo self::$opt_controls; ?>" <?php checked($all[self::$opt_controls], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_controls; ?>"><b class="chktitle">Show Controls:</b> Show the player's control bar. Unchecking this option creates a cleaner look but limits what your viewers can control (play position, volume, etc.).</label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_defaultvol; ?>" id="<?php echo self::$opt_defaultvol; ?>" <?php checked($all[self::$opt_defaultvol], 1); ?> type="checkbox" class="checkbox">                        
                                <label for="<?php echo self::$opt_defaultvol; ?>">
                                    <b class="chktitle">Volume Initialization: </b>
                                    Set an initial volume level for all of your embedded videos.  Check this and you'll see a <span class="vol-seeslider">slider</span> <span class="vol-seetextbox">textbox</span> for setting the start volume to a value between 0 (mute) and 100 (max) percent.  Leaving it unchecked means you want the visitor's default behavior.  This feature is experimental and is less predictable on a page with more than one embed. Read more about why you might want to <a href="<?php echo self::$epbase ?>/mute-volume-youtube-wordpress.aspx" target="_blank">initialize YouTube embed volume here &raquo;</a>
                                </label>
                                <span id="boxdefaultvol">
                                    Volume: <span class="vol-output"></span> <input min="0" max="100" step="1" type="text" name="<?php echo self::$opt_vol; ?>" id="<?php echo self::$opt_vol; ?>" value="<?php echo esc_attr(trim($all[self::$opt_vol])); ?>" >
                                </span>
                            </p>

                            <p>
                                <input name="<?php echo self::$opt_pause_others; ?>" id="<?php echo self::$opt_pause_others; ?>" <?php checked($all[self::$opt_pause_others], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_pause_others; ?>">
                                    <b class="chktitle"><?php _e('Simultaneous Playback Control:', 'youtube-embed-plus-pro'); ?></b>
                                    <?php _e('You can enable/disable the ability for visitors to have separate videos running at the same time on the same page. Check this to automatically pause other players while the current player is playing. (Note: this feature is not guaranteed to work with videos embedded from other plugins).', 'youtube-embed-plus-pro'); ?>
                                </label>
                            </p>

                            <p>
                                <input name="<?php echo self::$opt_cc_load_policy; ?>" id="<?php echo self::$opt_cc_load_policy; ?>" <?php checked($all[self::$opt_cc_load_policy], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_cc_load_policy; ?>"><?php _e('<b class="chktitle">Closed Captions:</b> Turn on closed captions by default.') ?></label>
                            </p>
                            <?php
                            $lang_codes = [
                                ["Abkhazian", "аҧсуа бызшәа, аҧсшәа", "ab", "abk", "abk"],
                                ["Afar", "Afaraf", "aa", "aar", "aar"],
                                ["Afrikaans", "Afrikaans", "af", "afr", "afr"],
                                ["Akan", "Akan", "ak", "aka", "aka"],
                                ["Albanian", "Shqip", "sq", "sqi", "alb"],
                                ["Amharic", "አማርኛ", "am", "amh", "amh"],
                                ["Arabic", "العربية", "ar", "ara", "ara"],
                                ["Aragonese", "aragonés", "an", "arg", "arg"],
                                ["Armenian", "Հայերեն", "hy", "hye", "arm"],
                                ["Assamese", "অসমীয়া", "as", "asm", "asm"],
                                ["Avaric", "авар мацӀ, магӀарул мацӀ", "av", "ava", "ava"],
                                ["Avestan", "avesta", "ae", "ave", "ave"],
                                ["Aymara", "aymar aru", "ay", "aym", "aym"],
                                ["Azerbaijani", "azərbaycan dili", "az", "aze", "aze"],
                                ["Bambara", "bamanankan", "bm", "bam", "bam"],
                                ["Bashkir", "башҡорт теле", "ba", "bak", "bak"],
                                ["Basque", "euskara, euskera", "eu", "eus", "baq"],
                                ["Belarusian", "беларуская мова", "be", "bel", "bel"],
                                ["Bengali", "বাংলা", "bn", "ben", "ben"],
                                ["Bihari languages", "भोजपुरी", "bh", "bih", "bih"],
                                ["Bislama", "Bislama", "bi", "bis", "bis"],
                                ["Bosnian", "bosanski jezik", "bs", "bos", "bos"],
                                ["Breton", "brezhoneg", "br", "bre", "bre"],
                                ["Bulgarian", "български език", "bg", "bul", "bul"],
                                ["Burmese", "ဗမာစာ", "my", "mya", "bur"],
                                ["Catalan, Valencian", "català, valencià", "ca", "cat", "cat"],
                                ["Chamorro", "Chamoru", "ch", "cha", "cha"],
                                ["Chechen", "нохчийн мотт", "ce", "che", "che"],
                                ["Chichewa, Chewa, Nyanja", "chiCheŵa, chinyanja", "ny", "nya", "nya"],
                                ["Chinese", "中文 (Zhōngwén), 汉语, 漢語", "zh", "zho", "chi"],
                                ["Chuvash", "чӑваш чӗлхи", "cv", "chv", "chv"],
                                ["Cornish", "Kernewek", "kw", "cor", "cor"],
                                ["Corsican", "corsu, lingua corsa", "co", "cos", "cos"],
                                ["Cree", "ᓀᐦᐃᔭᐍᐏᐣ", "cr", "cre", "cre"],
                                ["Croatian", "hrvatski jezik", "hr", "hrv", "hrv"],
                                ["Czech", "čeština, český jazyk", "cs", "ces", "cze"],
                                ["Danish", "dansk", "da", "dan", "dan"],
                                ["Divehi, Dhivehi, Maldivian", "ދިވެހި", "dv", "div", "div"],
                                ["Dutch, Flemish", "Nederlands, Vlaams", "nl", "nld", "dut"],
                                ["Dzongkha", "རྫོང་ཁ", "dz", "dzo", "dzo"],
                                ["English", "English", "en", "eng", "eng"],
                                ["Esperanto", "Esperanto", "eo", "epo", "epo"],
                                ["Estonian", "eesti, eesti keel", "et", "est", "est"],
                                ["Ewe", "Eʋegbe", "ee", "ewe", "ewe"],
                                ["Faroese", "føroyskt", "fo", "fao", "fao"],
                                ["Fijian", "vosa Vakaviti", "fj", "fij", "fij"],
                                ["Finnish", "suomi, suomen kieli", "fi", "fin", "fin"],
                                ["French", "français, langue française", "fr", "fra", "fre"],
                                ["Fulah", "Fulfulde, Pulaar, Pular", "ff", "ful", "ful"],
                                ["Galician", "Galego", "gl", "glg", "glg"],
                                ["Georgian", "ქართული", "ka", "kat", "geo"],
                                ["German", "Deutsch", "de", "deu", "ger"],
                                ["Greek, Modern (1453–)", "ελληνικά", "el", "ell", "gre"],
                                ["Guarani", "Avañe'ẽ", "gn", "grn", "grn"],
                                ["Gujarati", "ગુજરાતી", "gu", "guj", "guj"],
                                ["Haitian, Haitian Creole", "Kreyòl ayisyen", "ht", "hat", "hat"],
                                ["Hausa", "(Hausa) هَوُسَ", "ha", "hau", "hau"],
                                ["Hebrew", "עברית", "he", "heb", "heb"],
                                ["Herero", "Otjiherero", "hz", "her", "her"],
                                ["Hindi", "हिन्दी, हिंदी", "hi", "hin", "hin"],
                                ["Hiri Motu", "Hiri Motu", "ho", "hmo", "hmo"],
                                ["Hungarian", "magyar", "hu", "hun", "hun"],
                                ["Interlingua (International Auxiliary Language Association)", "Interlingua", "ia", "ina", "ina"],
                                ["Indonesian", "Bahasa Indonesia", "id", "ind", "ind"],
                                ["Interlingue, Occidental", "(originally:) Occidental, (after WWII:) Interlingue", "ie", "ile", "ile"],
                                ["Irish", "Gaeilge", "ga", "gle", "gle"],
                                ["Igbo", "Asụsụ Igbo", "ig", "ibo", "ibo"],
                                ["Inupiaq", "Iñupiaq, Iñupiatun", "ik", "ipk", "ipk"],
                                ["Ido", "Ido", "io", "ido", "ido"],
                                ["Icelandic", "Íslenska", "is", "isl", "ice"],
                                ["Italian", "Italiano", "it", "ita", "ita"],
                                ["Inuktitut", "ᐃᓄᒃᑎᑐᑦ", "iu", "iku", "iku"],
                                ["Japanese", "日本語 (にほんご)", "ja", "jpn", "jpn"],
                                ["Javanese", "ꦧꦱꦗꦮ, Basa Jawa", "jv", "jav", "jav"],
                                ["Kalaallisut, Greenlandic", "kalaallisut, kalaallit oqaasii", "kl", "kal", "kal"],
                                ["Kannada", "ಕನ್ನಡ", "kn", "kan", "kan"],
                                ["Kanuri", "Kanuri", "kr", "kau", "kau"],
                                ["Kashmiri", "कश्मीरी, كشميري‎", "ks", "kas", "kas"],
                                ["Kazakh", "қазақ тілі", "kk", "kaz", "kaz"],
                                ["Central Khmer", "ខ្មែរ, ខេមរភាសា, ភាសាខ្មែរ", "km", "khm", "khm"],
                                ["Kikuyu, Gikuyu", "Gĩkũyũ", "ki", "kik", "kik"],
                                ["Kinyarwanda", "Ikinyarwanda", "rw", "kin", "kin"],
                                ["Kirghiz, Kyrgyz", "Кыргызча, Кыргыз тили", "ky", "kir", "kir"],
                                ["Komi", "коми кыв", "kv", "kom", "kom"],
                                ["Kongo", "Kikongo", "kg", "kon", "kon"],
                                ["Korean", "한국어", "ko", "kor", "kor"],
                                ["Kurdish", "Kurdî, کوردی‎", "ku", "kur", "kur"],
                                ["Kuanyama, Kwanyama", "Kuanyama", "kj", "kua", "kua"],
                                ["Latin", "latine, lingua latina", "la", "lat", "lat"],
                                ["Luxembourgish, Letzeburgesch", "Lëtzebuergesch", "lb", "ltz", "ltz"],
                                ["Ganda", "Luganda", "lg", "lug", "lug"],
                                ["Limburgan, Limburger, Limburgish", "Limburgs", "li", "lim", "lim"],
                                ["Lingala", "Lingála", "ln", "lin", "lin"],
                                ["Lao", "ພາສາລາວ", "lo", "lao", "lao"],
                                ["Lithuanian", "lietuvių kalba", "lt", "lit", "lit"],
                                ["Luba-Katanga", "Kiluba", "lu", "lub", "lub"],
                                ["Latvian", "latviešu valoda", "lv", "lav", "lav"],
                                ["Manx", "Gaelg, Gailck", "gv", "glv", "glv"],
                                ["Macedonian", "македонски јазик", "mk", "mkd", "mac"],
                                ["Malagasy", "fiteny malagasy", "mg", "mlg", "mlg"],
                                ["Malay", "Bahasa Melayu, بهاس ملايو‎", "ms", "msa", "may"],
                                ["Malayalam", "മലയാളം", "ml", "mal", "mal"],
                                ["Maltese", "Malti", "mt", "mlt", "mlt"],
                                ["Maori", "te reo Māori", "mi", "mri", "mao"],
                                ["Marathi", "मराठी", "mr", "mar", "mar"],
                                ["Marshallese", "Kajin M̧ajeļ", "mh", "mah", "mah"],
                                ["Mongolian", "Монгол хэл", "mn", "mon", "mon"],
                                ["Nauru", "Dorerin Naoero", "na", "nau", "nau"],
                                ["Navajo, Navaho", "Diné bizaad", "nv", "nav", "nav"],
                                ["North Ndebele", "isiNdebele", "nd", "nde", "nde"],
                                ["Nepali", "नेपाली", "ne", "nep", "nep"],
                                ["Ndonga", "Owambo", "ng", "ndo", "ndo"],
                                ["Norwegian Bokmål", "Norsk Bokmål", "nb", "nob", "nob"],
                                ["Norwegian Nynorsk", "Norsk Nynorsk", "nn", "nno", "nno"],
                                ["Norwegian", "Norsk", "no", "nor", "nor"],
                                ["Sichuan Yi, Nuosu", "ꆈꌠ꒿ Nuosuhxop", "ii", "iii", "iii"],
                                ["South Ndebele", "isiNdebele", "nr", "nbl", "nbl"],
                                ["Occitan", "occitan, lenga d'òc", "oc", "oci", "oci"],
                                ["Ojibwa", "ᐊᓂᔑᓈᐯᒧᐎᓐ", "oj", "oji", "oji"],
                                ["Church Slavic, Old Slavonic, Church Slavonic, Old Bulgarian, Old Church Slavonic", "ѩзыкъ словѣньскъ", "cu", "chu", "chu"],
                                ["Oromo", "Afaan Oromoo", "om", "orm", "orm"],
                                ["Oriya", "ଓଡ଼ିଆ", "or", "ori", "ori"],
                                ["Ossetian, Ossetic", "ирон æвзаг", "os", "oss", "oss"],
                                ["Punjabi, Panjabi", "ਪੰਜਾਬੀ, پنجابی‎", "pa", "pan", "pan"],
                                ["Pali", "पालि, पाळि", "pi", "pli", "pli"],
                                ["Persian", "فارسی", "fa", "fas", "per"],
                                ["Polish", "język polski, polszczyzna", "pl", "pol", "pol"],
                                ["Pashto, Pushto", "پښتو", "ps", "pus", "pus"],
                                ["Portuguese", "Português", "pt", "por", "por"],
                                ["Quechua", "Runa Simi, Kichwa", "qu", "que", "que"],
                                ["Romansh", "Rumantsch Grischun", "rm", "roh", "roh"],
                                ["Rundi", "Ikirundi", "rn", "run", "run"],
                                ["Romanian, Moldavian, Moldovan", "Română", "ro", "ron", "rum"],
                                ["Russian", "русский", "ru", "rus", "rus"],
                                ["Sanskrit", "संस्कृतम्", "sa", "san", "san"],
                                ["Sardinian", "sardu", "sc", "srd", "srd"],
                                ["Sindhi", "सिन्धी, سنڌي، سندھی‎", "sd", "snd", "snd"],
                                ["Northern Sami", "Davvisámegiella", "se", "sme", "sme"],
                                ["Samoan", "gagana fa'a Samoa", "sm", "smo", "smo"],
                                ["Sango", "yângâ tî sängö", "sg", "sag", "sag"],
                                ["Serbian", "српски језик", "sr", "srp", "srp"],
                                ["Gaelic, Scottish Gaelic", "Gàidhlig", "gd", "gla", "gla"],
                                ["Shona", "chiShona", "sn", "sna", "sna"],
                                ["Sinhala, Sinhalese", "සිංහල", "si", "sin", "sin"],
                                ["Slovak", "Slovenčina, Slovenský Jazyk", "sk", "slk", "slo"],
                                ["Slovenian", "Slovenski Jezik, Slovenščina", "sl", "slv", "slv"],
                                ["Somali", "Soomaaliga, af Soomaali", "so", "som", "som"],
                                ["Southern Sotho", "Sesotho", "st", "sot", "sot"],
                                ["Spanish, Castilian", "Español", "es", "spa", "spa"],
                                ["Sundanese", "Basa Sunda", "su", "sun", "sun"],
                                ["Swahili", "Kiswahili", "sw", "swa", "swa"],
                                ["Swati", "SiSwati", "ss", "ssw", "ssw"],
                                ["Swedish", "Svenska", "sv", "swe", "swe"],
                                ["Tamil", "தமிழ்", "ta", "tam", "tam"],
                                ["Telugu", "తెలుగు", "te", "tel", "tel"],
                                ["Tajik", "тоҷикӣ, toçikī, تاجیکی‎", "tg", "tgk", "tgk"],
                                ["Thai", "ไทย", "th", "tha", "tha"],
                                ["Tigrinya", "ትግርኛ", "ti", "tir", "tir"],
                                ["Tibetan", "བོད་ཡིག", "bo", "bod", "tib"],
                                ["Turkmen", "Türkmen, Түркмен", "tk", "tuk", "tuk"],
                                ["Tagalog", "Wikang Tagalog", "tl", "tgl", "tgl"],
                                ["Tswana", "Setswana", "tn", "tsn", "tsn"],
                                ["Tonga (Tonga Islands)", "Faka Tonga", "to", "ton", "ton"],
                                ["Turkish", "Türkçe", "tr", "tur", "tur"],
                                ["Tsonga", "Xitsonga", "ts", "tso", "tso"],
                                ["Tatar", "татар теле, tatar tele", "tt", "tat", "tat"],
                                ["Twi", "Twi", "tw", "twi", "twi"],
                                ["Tahitian", "Reo Tahiti", "ty", "tah", "tah"],
                                ["Uighur, Uyghur", "ئۇيغۇرچە‎, Uyghurche", "ug", "uig", "uig"],
                                ["Ukrainian", "Українська", "uk", "ukr", "ukr"],
                                ["Urdu", "اردو", "ur", "urd", "urd"],
                                ["Uzbek", "Oʻzbek, Ўзбек, أۇزبېك‎", "uz", "uzb", "uzb"],
                                ["Venda", "Tshivenḓa", "ve", "ven", "ven"],
                                ["Vietnamese", "Tiếng Việt", "vi", "vie", "vie"],
                                ["Volapük", "Volapük", "vo", "vol", "vol"],
                                ["Walloon", "Walon", "wa", "wln", "wln"],
                                ["Welsh", "Cymraeg", "cy", "cym", "wel"],
                                ["Wolof", "Wollof", "wo", "wol", "wol"],
                                ["Western Frisian", "Frysk", "fy", "fry", "fry"],
                                ["Xhosa", "isiXhosa", "xh", "xho", "xho"],
                                ["Yiddish", "ייִדיש", "yi", "yid", "yid"],
                                ["Yoruba", "Yorùbá", "yo", "yor", "yor"],
                                ["Zhuang, Chuang", "Saɯ cueŋƅ, Saw cuengh", "za", "zha", "zha"],
                                ["Zulu", "isiZulu", "zu", "zul", "zul"]
                            ];

                            $selected_val = trim($all[self::$opt_cc_lang_pref]);
                            ?>
                            <p>
                                <label for="<?php echo self::$opt_cc_lang_pref; ?>"><b class="chktitle">Closed Captions Language:</b></label>
                                <select name="<?php echo self::$opt_cc_lang_pref; ?>" id="<?php echo self::$opt_cc_lang_pref; ?>" style="width: 260px;">                                    
                                    <option <?php echo '' == $selected_val ? 'selected' : '' ?> value="">Default/Unspecified</option>
                                    <?php
                                    foreach ($lang_codes as $idx => $lang_row)
                                    {
                                        $iso_code = $lang_row[2];
                                        $iso_label = $lang_row[0] . ' - ' . $lang_row[1];
                                        ?>
                                        <option <?php echo $iso_code == $selected_val ? 'selected' : '' ?> value="<?php echo $iso_code ?>"><?php echo $iso_label ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                Select the preferred default language for closed captions (when available).
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_dohl; ?>" id="<?php echo self::$opt_dohl; ?>" <?php checked($all[self::$opt_dohl], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_dohl; ?>"><b class="chktitle">Player Localization / Internationalization: </b>
                                    Automatically detect your site's default language (using get_locale) and set your YouTube embeds interface language so that it matches. Specifically, this will set the player's tooltips and caption track if your language is natively supported by YouTube. We suggest checking this if English is not your site's default language.  <a href="<?php echo self::$epbase ?>/youtube-iso-639-1-language-codes.aspx" target="_blank">See here for more details &raquo;</a></label>
                            </p>                    
                            <p>
                                <input name="<?php echo self::$opt_playlistorder; ?>" id="<?php echo self::$opt_playlistorder; ?>" <?php checked($all[self::$opt_playlistorder], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_playlistorder; ?>">
                                    <b class="chktitle">Self-contained Playlist Ordering:</b> 
                                    Note: This option does <strong class="orange">NOT</strong> apply to galleries. It applies only to these kinds of <em>self-contained playlists</em> (Example: <a target="_blank" href="https://www.youtube.com/watch?v=J50PlRZHH9I&t=3m20s">https://www.youtube.com/watch?v=J50PlRZHH9I&t=3m20s</a>).
                                    If you're trying to control the order of a <em>gallery</em> instead, then you must be the owner of the playlist, go to YouTube.com, and reorder it there. This plugin can only order a <em>gallery</em> the way the owner ordered the source playlist.
                                    Check this option if you just want your <em>self-contained playlists</em> to begin with the latest added video by default. (Unchecking this will force playlists to always start with your selected specific video, even if you add videos to the playlist later).
                                </label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_onboarded; ?>" id="<?php echo self::$opt_onboarded; ?>" <?php checked($all[self::$opt_onboarded], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_onboarded; ?>">
                                    <b class="chktitle">Hide Quick Setup Guide:</b>
                                    Check this to hide the installation setup wizard when this page loads.
                                </label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_show_pointer; ?>" id="<?php echo self::$opt_show_pointer; ?>" <?php checked($all[self::$opt_show_pointer], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_show_pointer; ?>">
                                    <b class="chktitle">Show New Feature Notice:</b>
                                    Show admin notice of new plugin features after updates.
                                </label>
                            </p>
                            <p class="<?php echo self::vi_logged_in() || !empty($all[self::$opt_vi_active]) || !self::vi_ever_logged_in() ? 'hidden' : '' ?>">
                                <input name="<?php echo self::$opt_vi_hide_monetize_tab; ?>" id="<?php echo self::$opt_vi_hide_monetize_tab; ?>" <?php checked($all[self::$opt_vi_hide_monetize_tab], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_vi_hide_monetize_tab; ?>"><b class="chktitle">Hide "Monetize" Feature:</b> (deprecated) Hide the tab(s) for the deprecated video intelligence feature.</label>
                            </p>
                            <div id="not_live_content_scroll" class="p">
                                <br>
                                <h3>Live Stream Options <sup class="orange">new</sup></h3>
                                <div class="epyt-notice-info">
                                    <?php echo self::livestream_advice(false); ?>
                                    <div class="clear-live-cache">
                                        <p>
                                            <strong>Note:</strong> As mentioned above, live streaming uses caching to help preserve your API quota. If you are still having issues with your stream starting on time, <input type="button" class="button button-primary btn-clear-live-cache" value="click here to clear your live stream cache"/>.
                                        </p>
                                        <p>
                                            <span style="display: none;" class="orange bold clearspdcloading">Clearing...</span>
                                            <span  class="orange bold clearspdcsuccess" style="display: none;">Finished clearing live stream cache.</span>
                                            <span class="orange bold clearspdcfailed" style="display: none;">Sorry, there seemed to be a problem clearing the cache.</span>
                                        </p>
                                    </div>
                                </div>                                
                                <p>
                                    <b class="chktitle">Use "Not Live" Fallback Content For Live Streams:</b> (<a href="<?php echo self::$epbase ?>/how-to-embed-a-youtube-livestream-in-wordpress.aspx" target="_blank">More info here</a>)
                                    This feature lets you display alternate content if your live stream is not currently active. There are two flavors of this feature: one that affects <strong>direct link</strong> live streams, and 
                                    one that affects <strong>channel</strong> live streams. Each are explained below. They work a little differently, but both use the same "Not Live" Fallback Content that you can edit below.
                                    <strong>Note: This feature does not always work for premieres, but we are hoping the YouTube API will support it in the future.</strong> Also, in a future update, these 2 flavors will merge into one to keep things simple.
                                </p>
                                <div class="ytindent chx">
                                    <input name="<?php echo self::$opt_not_live_on; ?>" id="<?php echo self::$opt_not_live_on; ?>" <?php checked($all[self::$opt_not_live_on], 1); ?> type="checkbox" class="checkbox">
                                    <label for="<?php echo self::$opt_not_live_on; ?>"><span class="chktitle">Turn on for <b>direct link</b> live streams:</span>
                                        When your direct-link embed is not streaming live, the YouTube live player usually displays a countdown after the user clicks the play button.
                                        Instead of showing that player, you can display some "coming soon" content in that space for your visitors to see until your video begins to live stream. 
                                        The plugin will automatically switch to your video's live stream once it's active. In the <em>"Not Live" Fallback Content</em> box below, enter what you would like to appear until then.
                                        You can even insert shortcodes from our plugin into the box below (shortcodes from other plugins may or may not work correctly).
                                        If you just want to show the standard countdown player that YouTube provides, don't use this feature.
                                        <strong>NOTE: Turning this on for direct-link live streams uses a significant amount of your YouTube API quota. We suggest unchecking it if your site has high traffic. If you chose to use this feature, do <u>not</u> put another live stream embed below.</strong>
                                    </label>
                                    <br>
                                    <br>
                                    <input name="<?php echo self::$opt_not_live_on_channel; ?>" id="<?php echo self::$opt_not_live_on_channel; ?>" <?php checked($all[self::$opt_not_live_on_channel], 1); ?> type="checkbox" class="checkbox">
                                    <label for="<?php echo self::$opt_not_live_on_channel; ?>"><span class="chktitle">Turn on for <b>channel</b> live streams:</span> 
                                        If your live stream embed is channel-based, YouTube might show an error message if there is no upcoming or currently streaming video from your channel. 
                                        Instead of showing that player, you can display some "coming soon" content in that space for your visitors to see until your video begins to live stream. 
                                        The plugin will automatically switch to your video's live stream once it's active.
                                        In the <em>"Not Live" Fallback Content</em> box below, enter what you would like to appear when nothing is playing or scheduled to play yet on your channel.
                                        You can even insert shortcodes from our plugin into the box below (shortcodes from other plugins may or may not work correctly).
                                        <strong>NOTE: We recommend trying this instead of the direct-link option, to see if it works for your site. We hope to improve this feature over time. If you chose to use this feature, do <u>not</u> put another live stream embed below.</strong>
                                    </label>
                                    <div class="p not-live-content">
                                        <p>                                            
                                            <b>"Not Live" Fallback Content:</b>
                                        </p>
                                        <?php
                                        wp_editor(wp_kses_post($all[self::$opt_not_live_content]), self::$opt_not_live_content, array('textarea_rows' => 7));
                                        ?> 
                                    </div>
                                     <p class="not-live-content">
                                        <label for="<?php echo self::$opt_not_live_showtime; ?>"><b class="chktitle">Reveal Video Countdown Before Start Time: <sup class="orange">new</sup></b></label>
                                        <input name="<?php echo self::$opt_not_live_showtime; ?>" min="1" id="<?php echo self::$opt_not_live_showtime; ?>" type="number" class="textinput" style="width: 60px;" value="<?php echo esc_attr(trim($all[self::$opt_not_live_showtime])); ?>">
                                        Enter how many minutes before the scheduled livestream that you would like to hide the above content and reveal the video embed's countdown. This will give your visitors time to watch the countdown and view your livestream start naturally, instead of needing to refresh the page at precisely the right start time.
                                        NOTE: For now, this feature only works with channel-based live streams.
                                    </p>                                    
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="pattern" id="jumpprivacy">                            
                        <h2>Security Options</h2>
                        <p>
                            <input name="<?php echo self::$opt_restrict_wizard; ?>" id="<?php echo self::$opt_restrict_wizard; ?>" <?php checked($all[self::$opt_restrict_wizard], 1); ?> type="checkbox" class="checkbox">
                            <label for="<?php echo self::$opt_restrict_wizard; ?>">
                                <b class="chktitle">Restrict Wizard Button:</b> Select which roles can use the YouTube wizard button. For example, you may wish to hide the button from contributors submitting content on the front end.
                            </label>
                            <br>
                            <span id="box_restrict_wizard" class="chx">
                                <?php
                                foreach (self::$dft_roles as $idx => $role)
                                {
                                    ?>
                                    <label>
                                        <input type="checkbox" name="<?php echo self::$opt_restrict_wizard_roles . '[]' ?>" value="<?php echo esc_attr($role) ?>" <?php echo in_array($role, $all[self::$opt_restrict_wizard_roles]) ? 'checked' : '' ?>>
                                        <?php echo esc_html(ucfirst($role)); ?>s
                                    </label>
                                    <?php
                                }
                                ?>
                            </span>
                        </p>
                        <p>
                            <input name="<?php echo self::$opt_origin; ?>" id="<?php echo self::$opt_origin; ?>" <?php checked($all[self::$opt_origin], 1); ?> type="checkbox" class="checkbox">
                            <label for="<?php echo self::$opt_origin; ?>"><b class="chktitle">Extra Player Security: </b>
                                Add site origin information with each embed code as an extra security measure. In YouTube's/Google's own words, checking this option "protects against malicious third-party JavaScript being injected into your page and hijacking control of your YouTube player." We especially recommend checking it as it adds higher security than the built-in YouTube embedding method that comes with the current version of WordPress (i.e. oembed).
                            </label>
                        </p>                        

                        <h2>Privacy Options</h2>
                        <p>These options may help with privacy restrictions such as GDPR and the EU Cookie Law.</p>
                        <div class="ytindent chx">
                            <p>
                                <b class="chktitle">YouTube API Loading:</b> Choose when to load the YouTube API. The "Restricted" or "Never" options will help with GDPR compliance:
                            <ul class="indent-option">
                                <li><label><input type="radio" name="<?php echo self::$opt_ytapi_load ?>" value="light" <?php checked($all[self::$opt_ytapi_load], 'light'); ?> /> <em>Restricted</em> - (Recommended) Only load the API on pages that have a YouTube video.</label></li>
                                <li><label>
                                        <input type="radio" name="<?php echo self::$opt_ytapi_load ?>" value="never" <?php checked($all[self::$opt_ytapi_load], 'never'); ?> /> <em>Never</em> - Do not load the YouTube API. Note: The "Never" choice may break a few features such as Volume Initialization and Gallery Continuous/Auto Play.
                                        <div id="boxapinever">
                                            Note: Checking this option may break some features such as the ones listed below:
                                            <ul class="list-ul">
                                                <li>Galleries</li>
                                                <li>Hide related videos at the end of playback</li>
                                                <li>Volume initialization</li>
                                                <li>Pro Analytics</li>
                                                <li>Simultaneous playback control</li>
                                                <li>Playing video on mobile devices</li>
                                                <li>Live chat</li>
                                            </ul>
                                        </div>
                                    </label></li>
                                <li><label><input type="radio" name="<?php echo self::$opt_ytapi_load ?>" value="always" <?php checked($all[self::$opt_ytapi_load], 'always'); ?> /> <em>Always</em> - Load the API on all pages. In most cases, the "Always" choice is not necessary.</label></li>
                            </ul>
                            </p>


                            <p>
                                <input name="<?php echo self::$opt_gdpr_consent; ?>" id="<?php echo self::$opt_gdpr_consent; ?>" <?php checked($all[self::$opt_gdpr_consent], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_gdpr_consent; ?>">
                                    <b class="chktitle">Privacy/GDPR - Show Consent Message:</b> Ask for consent before loading YouTube content. A message will be displayed in place of the YouTube video, as shown in the screenshot below. Once the visitor approves consent, the YouTube content will load. You can customize the message text and the button text in the next 2 options.
                                    See this feature demonstrated in <a href="https://www.youtube.com/watch?v=lm_HIic6obw" target="_blank">this video</a>.
                                </label>
                                <span id="box_gdpr_consent">
                                    Note: If your visitors click a red accept button but your site doesn't reveal the video, you probably have a caching plugin. There should be a setting in your caching plugin to prevent caching the consent cookie. The name of the consent cookie is: <code>ytprefs_gdpr_consent</code>
                                </span>
                            </p>

                            <p>
                                <label for="<?php echo self::$opt_gdpr_consent_message; ?>">
                                    <b class="chktitle">Privacy/GDPR - Consent Message Text:</b>
                                    Below you can customize the message that will appear to visitors before they accept YouTube content:
                                </label>
                            <div class="clearboth"></div>
                            <div class="gdpr-options-left">
                                <?php
                                wp_editor(wp_kses_post($all[self::$opt_gdpr_consent_message]), self::$opt_gdpr_consent_message, array(
                                    'textarea_rows' => 22,
                                    'media_buttons' => false,
                                    'teeny' => true
                                ));
                                ?> 
                            </div>
                            <div class="gdpr-options-right">
                                <p><em>Example of message and button:</em></p>

                                <img src="<?php echo plugins_url('images/ss-gdpr-message.png', __FILE__) ?>" alt="GDPR Consent Message Example" class="img-gdpr-message" />
                            </div>

                            </p>
                            <div class="clearboth"></div>
                            <p>
                                <label for="<?php echo self::$opt_gdpr_consent_button; ?>">
                                    <b class="chktitle">Privacy/GDPR - Consent Button Text:</b>
                                    This is the text for the red "Accept" button that appears with the above privacy/GDPR message:
                                </label>
                                <br>
                                <input type="text" placeholder="Example: Accept YouTube Content" name="<?php echo self::$opt_gdpr_consent_button; ?>" id="<?php echo self::$opt_gdpr_consent_button; ?>" value="<?php echo esc_attr(trim($all[self::$opt_gdpr_consent_button])); ?>" class="textinput regular-text"/>
                            </p>

                            <p>
                                <input name="<?php echo self::$opt_nocookie; ?>" id="<?php echo self::$opt_nocookie; ?>" <?php checked($all[self::$opt_nocookie], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_nocookie; ?>">
                                    <b class="chktitle">No Cookies:</b> Prevent YouTube from leaving tracking cookies on your visitors browsers unless they actual play the videos. This is coded to apply this behavior on links in your past post as well.
                                    <div id="boxnocookie">
                                        Note: Checking this option may introduce issues to features that depend YouTube's API, such as the ones listed below. We suggest testing them out to make sure you are still pleased with the results:
                                        <ul class="list-ul">
                                            <li>Galleries</li>
                                            <li>Hide related videos at the end of playback</li>
                                            <li>Volume initialization</li>
                                            <li>Pro Analytics</li>
                                            <li>Simultaneous playback control</li>
                                            <li>Playing video on mobile devices</li>
                                        </ul>
                                    </div>
                                </label>
                            </p>

                        </div>
                    </section>

                    <section class="pattern" id="jumpwiz">
                        <h2>Visual YouTube Wizard Directions</h2>
                        <p>
                            While you're writing your post or page, you have the ability to search YouTube and insert videos, playlists, and even galleries right from your editor. Below are directions for each type of WordPress editor.
                            For <strong>pagebuilder instructions</strong>, scroll down to the "Pagebuilder Tips" section.
                        </p>
                        <h3>Classic Editor</h3>
                        <img class="wiztab-screenshots" src="<?php echo plugins_url('images/ss-wiz-classic.png', __FILE__) ?>">
                        <p>
                            Simply click the YouTube wizard button found above 
                            your post editor to start the wizard (see image to the right to locate this button).  There, you'll have several options for different types of embeds.
                            Each embed code will have an "Insert Into Editor" button that you can click to directly embed the desired video link to your post without having to copy and paste.
                        </p>
                        <hr class="clearboth">
                        <h3>Widgets</h3>
                        <img class="wiztab-screenshots" src="<?php echo plugins_url('images/ss-wiz-widget.png', __FILE__) ?>">
                        <p>
                            To insert a video in a widget area, use the Text widget that comes with WordPress. Simply click the YouTube wizard button found above 
                            the widget textbox to start the wizard (see image to the right to locate this button).  There, you'll have several options for different types of embeds.
                            Each embed code will have an "Insert Into Editor" button that you can click to directly embed the desired video link to your post without having to copy and paste.
                        </p>
                        <hr class="clearboth"/>
                        <h3>Gutenberg Block Editor</h3>
                        <img class="wiztab-screenshots" src="<?php echo plugins_url('images/ss-wiz-gbblock.png', __FILE__) ?>">
                        <p>
                            Click on the (+) sign for the block editor list. The YouTube Wizard block is located under the "Embeds" category (make sure you choose "YouTube <strong>Wizard</strong>" not "YouTube").
                            Then your page will show a placeholder where you can launch the wizard. In the wizard, you'll have several options for different types of embeds.
                            Each embed code will have an "Insert Into Editor" button that you can click to directly embed the desired video link to your post without having to copy and paste.
                            You'll also be able to preview and interact with your embed without having to view the page on the front end.
                        </p>
                        <hr class="clearboth"/>
                        <h3>Gutenberg Classic Block</h3>
                        <img class="wiztab-screenshots" src="<?php echo plugins_url('images/ss-wiz-gbclassicblock.png', __FILE__) ?>">
                        <p>
                            Are you using Gutenberg but not quite ready to start using all the blocks? Well, you can still access our wizard if you wish to continue using Gutenberg's Classic block.
                            The Classic block brings back the former editor you used before WordPress, and so we've added a button to it you can use to launch the wizard (see example on the right).
                            Simply click the  wizard button to start the wizard. There, you'll have several options for different types of embeds.
                            Each embed code will have an "Insert Into Editor" button that you can click to directly embed the desired video link to your post without having to copy and paste.
                        </p>
                        <hr class="clearboth"/>
                        <h3>Pro Customization</h3>
                        <img class="wiztab-screenshots" src="<?php echo plugins_url('images/ssprowizard.png', __FILE__) ?>">
                        <p>
                            <b>Even more options are available to PRO users, no matter which editor you choose!</b>
                            Simply click the "Customize" button in the wizard to further personalize each of your embeds without having to manually add special codes yourself. 
                            The customize button will allow you to easily override most of the above default options for that embed.
                        </p>
                        <hr class="clearboth"/>
                        <h2 id="jumppagebuilder">Pagebuilder Tips</h2>
                        <p>
                            Most page builders have a text widget in which our plugin's wizard can be launched.  Watch the videos below for  some popular ones, namely Elementor, Beaver Builder, and SiteOrigin.
                        </p>
                        <p>
                            <em>Note: Please do not check the "Also Defer jQuery" option if you use a pagebuilder. Some pagebuilders cannot work if jQuery is deferred.</em>
                        </p>
                        <div class="wiztab-pagebuilder">
                            <h3>Beaver Builder</h3>
                            <div class="epyt-fitvid">
                                <iframe src="https://www.youtube.com/embed/bPgz0jyt7TE?rel=0" allowfullscreen="" frameborder="0"></iframe>
                            </div>
                        </div>
                        <div class="wiztab-pagebuilder">
                            <h3>Elementor</h3>
                            <div class="epyt-fitvid">
                                <iframe src="https://www.youtube.com/embed/ldNfIGRTxDU?rel=0" allowfullscreen="" frameborder="0"></iframe>
                            </div>
                        </div>
                        <div class="wiztab-pagebuilder">
                            <h3>Site Origin</h3>
                            <div class="epyt-fitvid">
                                <iframe src="https://www.youtube.com/embed/7QNYw_g-7WM?rel=0" allowfullscreen="" frameborder="0"></iframe>
                            </div>
                        </div>
                        <div class="wiztab-pagebuilder">
                            <h3>Visual Composer</h3>
                            <div class="epyt-fitvid">
                                <iframe src="https://www.youtube.com/embed/FWBQc9XhAqM?rel=0" allowfullscreen="" frameborder="0"></iframe>
                            </div>
                        </div>
                        <div class="wiztab-pagebuilder">
                            <h3>WPBakery</h3>
                            <div class="epyt-fitvid">
                                <iframe src="https://www.youtube.com/embed/7T5wPoEujN8?rel=0" allowfullscreen="" frameborder="0"></iframe>
                            </div>
                        </div>
                        <div class="wiztab-pagebuilder">
                            <p>
                                If you don't see your page builder listed above, don't worry. For pretty much any page builder with a short code widget, you can also embed your video, gallery, live stream, or premiere by creating the short code using the plugin's wizard and then embedding the code in the short code widget or text widget of your page builder of choice.
                            </p>
                        </div>
                    </section>
                    <section class="pattern" id="jumpgallery">
                        <h2>Gallery Settings and Directions</h2>
                        <img class="ssgallery" src="<?php echo plugins_url('images/ssgallery.png', __FILE__) ?>">
                        <p>
                            <a target="_blank" href="<?php echo self::$epbase ?>/responsive-youtube-playlist-channel-gallery-for-wordpress.aspx">You can now make playlist embeds (and channel-playlist embeds) have a gallery layout &raquo;</a>. <strong>First, you must obtain your YouTube API key</strong>. 
                            Don't worry, it's an easy process. Just <a href="https://www.youtube.com/watch?v=ZCfrNvu6nMc" target="_blank">click this link &raquo;</a> and follow the video on that page to get your server API key. Since Google updates their API Key generation directions frequently, follow the general steps shown in the video.
                            Then paste your API key in the "API Key" tab, and click the "Save Changes" button.
                        </p>

                        <p>
                            Below are the global settings for galleries. If you want each of your galleries to have custom settings, use the wizard for more options:
                        </p>
                        <div class="ytindent chx">

                            <p>
                                <label for="<?php echo self::$opt_gallery_pagesize; ?>"><b class="chktitle">Gallery Page Size:</b></label>
                                <select name="<?php echo self::$opt_gallery_pagesize; ?>" id="<?php echo self::$opt_gallery_pagesize; ?>" style="width: 60px;">
                                    <?php
                                    $gps_val = intval(trim($all[self::$opt_gallery_pagesize]));
                                    $gps_val = min($gps_val, 50);
                                    for ($gps = 1; $gps <= 50; $gps++)
                                    {
                                        ?><option <?php echo $gps_val == $gps ? 'selected' : '' ?> value="<?php echo $gps ?>"><?php echo $gps ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                Enter how many thumbnails per page should be shown at once (YouTube allows a maximum of 50 per page).
                            </p>
                            <p>
                                <label for="<?php echo self::$opt_gallery_columns; ?>"><b class="chktitle">Number of Columns:</b></label>
                                <input name="<?php echo self::$opt_gallery_columns; ?>" min="1" id="<?php echo self::$opt_gallery_columns; ?>" type="number" class="textinput" style="width: 60px;" value="<?php echo esc_attr(trim($all[self::$opt_gallery_columns])); ?>">                        
                                Enter how many thumbnails can fit per row.
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_gallery_collapse_grid; ?>" id="<?php echo self::$opt_gallery_collapse_grid; ?>" <?php checked($all[self::$opt_gallery_collapse_grid], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_gallery_collapse_grid; ?>">
                                    <b class="chktitle">Stack Thumbnails for Mobile:</b> Check this option to responsively stack thumbnails on smaller screens, for the grid layout.
                                </label>
                                <span id="box_collapse_grid">
                                    <?php
                                    foreach ($all[self::$opt_gallery_collapse_grid_breaks] as $idx => $bpts)
                                    {
                                        ?>
                                        On screens up to
                                        <input type="number" name="<?php echo self::$opt_gallery_collapse_grid_breaks . '[' . $idx . '][bp][max]'; ?>"
                                               id="<?php echo self::$opt_gallery_collapse_grid_breaks . '[' . $idx . '][bp][max]'; ?>" 
                                               value="<?php echo intval(trim($bpts['bp']['max'])); ?>" class="textinput" style="width: 70px;">px wide, stack thumbnails to 1 column.
                                        <input type="hidden" name="<?php echo self::$opt_gallery_collapse_grid_breaks . '[' . $idx . '][cols]'; ?>"
                                               id="<?php echo self::$opt_gallery_collapse_grid_breaks . '[' . $idx . '][cols]'; ?>"
                                               value="<?php echo intval(trim($bpts['cols'])); ?>">
                                        <input type="hidden" name="<?php echo self::$opt_gallery_collapse_grid_breaks . '[' . $idx . '][bp][min]'; ?>"
                                               id="<?php echo self::$opt_gallery_collapse_grid_breaks . '[' . $idx . '][bp][min]'; ?>"
                                               value="<?php echo intval(trim($bpts['bp']['min'])); ?>">
                                               <?php
                                           }
                                           ?>
                                    <span class="smallnote grey pad20"><br>Note: a common mobile screen width is 767 pixels.</span>
                                </span>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_gallery_showpaging; ?>" id="<?php echo self::$opt_gallery_showpaging; ?>" <?php checked($all[self::$opt_gallery_showpaging], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_gallery_showpaging; ?>"><b class="chktitle">Show Pagination:</b> Show the Next/Previous buttons and page numbering.
                                    It might be useful to hide pagination if you want your gallery to display just a subset of videos from a playlist or channel.  That is, only the first page of videos (defined by your page size) will be visible to your visitors if these buttons are hidden.
                                </label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_gallery_customarrows; ?>" id="<?php echo self::$opt_gallery_customarrows; ?>" <?php checked($all[self::$opt_gallery_customarrows], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_gallery_customarrows; ?>">
                                    <b class="chktitle">Custom Next/Previous Text:</b> If you want your gallery viewers to see something besides "Next" and "Prev" when browsing through thumbnails, enter your replacement text here. This feature can be quite useful for non-English sites.  For example, a French site might replace Prev with Pr&eacute;c&eacute;dent  and Next with Suivant.
                                </label>
                                <span id="boxcustomarrows">
                                    Previous Page: <input type="text" name="<?php echo self::$opt_gallery_customprev; ?>" id="<?php echo self::$opt_gallery_customprev; ?>" value="<?php echo esc_attr(trim($all[self::$opt_gallery_customprev])); ?>" class="textinput" style="width: 100px;"> &nbsp;
                                    Next Page: <input type="text" name="<?php echo self::$opt_gallery_customnext; ?>" id="<?php echo self::$opt_gallery_customnext; ?>" value="<?php echo esc_attr(trim($all[self::$opt_gallery_customnext])); ?>" class="textinput" style="width: 100px;">
                                </span>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_gallery_channelsub; ?>" id="<?php echo self::$opt_gallery_channelsub; ?>" <?php checked($all[self::$opt_gallery_channelsub], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_gallery_channelsub; ?>">
                                    <b class="chktitle">Show Subscribe Button: </b> Are you the channel owner for all your galleries? Check this box to add a "Subscribe" button to all your galleries as shown below.  This might help you convert your site's visitors to YouTube subscribers of your channel. 
                                </label>
                                <span id="boxchannelsub">
                                    Paste Channel URL: <input type="text" placeholder="Example: https://www.youtube.com/user/YourChannel" name="<?php echo self::$opt_gallery_channelsublink; ?>" id="<?php echo self::$opt_gallery_channelsublink; ?>" value="<?php echo esc_url(trim($all[self::$opt_gallery_channelsublink])); ?>" class="textinput regular-text"> &nbsp;
                                    Button text: <input type="text" name="<?php echo self::$opt_gallery_channelsubtext; ?>" id="<?php echo self::$opt_gallery_channelsubtext; ?>" value="<?php echo esc_attr(trim($all[self::$opt_gallery_channelsubtext])); ?>" class="textinput" style="width: 200px;">
                                </span>
                            </p>
                            <p><img class="sssubscribe" src="<?php echo plugins_url('images/sssubscribe.png', __FILE__) ?>"></p>

                            <p>
                                <label for="<?php echo self::$opt_gallery_scrolloffset; ?>"><b class="chktitle">Scroll Offset:</b></label>
                                <input name="<?php echo self::$opt_gallery_scrolloffset; ?>" id="<?php echo self::$opt_gallery_scrolloffset; ?>" type="number" class="textinput" style="width: 60px;" value="<?php echo esc_attr(trim($all[self::$opt_gallery_scrolloffset])); ?>">
                                After you click on a thumbnail, the gallery will automatically smooth scroll up to the actual player. If you need it to scroll a few pixels further, increase this number.
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_gallery_showtitle; ?>" id="<?php echo self::$opt_gallery_showtitle; ?>" <?php checked($all[self::$opt_gallery_showtitle], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_gallery_showtitle; ?>"><b class="chktitle">Show Thumbnail Title:</b> Show titles with each thumbnail.</label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_gallery_hideprivate; ?>" id="<?php echo self::$opt_gallery_hideprivate; ?>" <?php checked($all[self::$opt_gallery_hideprivate], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_gallery_hideprivate; ?>"><b class="chktitle">Hide Private Thumbnails:</b> Hide thumbnails for videos in a playlist that cannot be embedded yet. Note: This may make some page sizes look uneven.</label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_gallery_autonext; ?>" id="<?php echo self::$opt_gallery_autonext; ?>" <?php checked($all[self::$opt_gallery_autonext], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_gallery_autonext; ?>"><b class="chktitle">Automatic Continuous Play:</b>  Automatically play the next video in the gallery as soon as the current video finished.
                                    <strong>Note:</strong> If you're embedding videos from your own monetized YouTube channel, we advise you to read YouTube's resource page on ads on embedded videos:
                                    <a href="https://support.google.com/youtube/answer/132596?hl=en" target="_blank">https://support.google.com/youtube/answer/132596?hl=en</a>
                                    You'll see that videos that you want to monetize "should be embedded using the standard click-to-play embed and NOT a scripted play."
                                    Unchecking this option guarantees standard click-to-play gallery embedding.
                                </label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_gallery_thumbplay; ?>" id="<?php echo self::$opt_gallery_thumbplay; ?>" <?php checked($all[self::$opt_gallery_thumbplay], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_gallery_thumbplay; ?>"><b class="chktitle">Thumbnail Click Plays Video:</b>
                                    Clicking on a gallery thumbnail autoplays the video. Uncheck this and visitors must also click the video's play button after clicking the thumbnail
                                    (uncheck this option for standard click-to-play gallery embedding).
                                </label>
                            </p>
                            <div class="pad20">
                                <p>
                                    Ready to get started with an actual gallery?  Just click the plugin wizard button and pick your desired gallery embedding choice.
                                </p>
                                <p><img class="sswizardbutton" src="<?php echo plugins_url('images/sswizardbutton.jpg', __FILE__) ?>"></p>
                            </div>
                        </div>


                    </section>
                    <section class="pattern" id="jumpprosettings">
                        <div class="upgchecks">
                            <h2>PRO Features</h2>
                            <?php
                            if ($all[self::$opt_pro] && strlen(trim($all[self::$opt_pro])) > 9)
                            {
                                ?>
                                <p class="smallnote orange">
                                    Below are PRO features for more gallery customization options, faster page loading, enhanced SEO, and more. 
                                </p>
                                <p>
                                    <input name="<?php echo self::$opt_dynload; ?>" id="<?php echo self::$opt_dynload; ?>" <?php checked($all[self::$opt_dynload], 1); ?> type="checkbox" class="checkbox">                        
                                    <label for="<?php echo self::$opt_dynload; ?>">
                                        <b>(PRO)</b>  <b class="chktitle">Lazy-Loading for Performance:</b>
                                        Lazy-loading can speed up your page loads by loading the player only until it is visible on the screen. 
                                        <a href="https://www.youtube.com/watch?v=omNdJvXDCLo" target="_blank">See an example here</a>. 
                                        An added bonus to this option is that you have the choice of several types of eye-catching lazy-loading effects that will make your YouTube embeds bounce, flip, pulse, or slide as they lazy load on the screen. Check this box to select your desired effect. <a target="_blank" href="<?php echo self::$epbase ?>/add-special-effects-to-youtube-embeds-in-wordpress.aspx">Read more here &raquo;</a>
                                    </label>
                                    <br>
                                    <span id="boxdyn">
                                        Animation:
                                        <?php $cleandyn = trim($all[self::$opt_dyntype]); ?>
                                        <select name="<?php echo self::$opt_dyntype; ?>" id="<?php echo self::$opt_dyntype; ?>" >
                                            <option value="">Select type</option>
                                            <option value="rotateIn" <?php echo 'rotateIn' === $cleandyn ? 'selected' : '' ?> >rotate in</option>
                                            <option value="slideInRight" <?php echo 'slideInRight' === $cleandyn ? 'selected' : '' ?> >slide from right</option>
                                            <option value="slideInLeft" <?php echo 'slideInLeft' === $cleandyn ? 'selected' : '' ?> >slide from left</option>
                                            <option value="bounceIn" <?php echo 'bounceIn' === $cleandyn ? 'selected' : '' ?> >bounce in</option>
                                            <option value="flipInX" <?php echo 'flipInX' === $cleandyn ? 'selected' : '' ?> >flip up/down</option>
                                            <option value="flipInY" <?php echo 'flipInY' === $cleandyn ? 'selected' : '' ?> >flip left/right</option>
                                            <option value="pulse" <?php echo 'pulse' === $cleandyn ? 'selected' : '' ?> >pulse</option>
                                            <option value="tada" <?php echo 'tada' === $cleandyn ? 'selected' : '' ?> >jiggle</option>
                                            <option value="fadeIn" <?php echo 'fadeIn' === $cleandyn ? 'selected' : '' ?> >fade in</option>
                                            <option value="fadeInDown" <?php echo 'fadeInDown' === $cleandyn ? 'selected' : '' ?> >fade in downward</option>
                                            <option value="fadeInUp" <?php echo 'fadeInUp' === $cleandyn ? 'selected' : '' ?> >fade in upward</option>
                                            <option value="zoomInDown" <?php echo 'zoomInDown' === $cleandyn ? 'selected' : '' ?> >zoom in downward</option>
                                            <option value="zoomInUp" <?php echo 'zoomInUp' === $cleandyn ? 'selected' : '' ?> >zoom in upward</option>
                                        </select>
                                    </span>
                                </p>
                                <div class="hr"></div>
                                <div class="p">
                                    <img class="ssaltgallery" src="<?php echo plugins_url('images/ss-live-chat.jpg', __FILE__) ?>" />
                                    <input name="<?php echo self::$opt_live_chat; ?>" id="<?php echo self::$opt_live_chat; ?>" <?php checked($all[self::$opt_live_chat], 1); ?> type="checkbox" class="checkbox">
                                    <label for="<?php echo self::$opt_live_chat; ?>">
                                        <b><?php _e('(PRO)') ?> </b> <b class="chktitle"><?php _e('Enable Live Chat:') ?></b> <?php _e('Add more interaction to your site by including the YouTube live chat box as part of each live stream embed. Note that live chat can also be an option for earning money from your audience by using the Super Chat feature. <a href="https://creatoracademy.youtube.com/page/lesson/superchat" target="_blank">Learn more here</a>.') ?>
                                        <strong class="check-note"><?php _e('<span class="orange">NOTE:</span> In wide containers, the chat box will appear to the right of the player. It will appear below the player when the container is less than 964px. Also, Google/YouTube disables live chat on mobile devices. So for mobile phones and tablets, the chat box will be hidden.') ?></strong>
                                    </label>
                                    <br>
                                    <br>                                
                                </div>
                                <div class="hr"></div>
                                <p>
                                    <img class="ssaltgallery" src="<?php echo plugins_url('images/ssaltgalleryall.jpg', __FILE__) ?>" />
                                    <label for="<?php echo self::$opt_gallery_style; ?>">
                                        <b>(PRO)</b>  <b class="chktitle">Alternate Gallery Styling:</b>
                                        <?php $cleanstyle = trim($all[self::$opt_gallery_style]); ?>
                                        <select name="<?php echo self::$opt_gallery_style; ?>" id="<?php echo self::$opt_gallery_style; ?>" >
                                            <option value="">Gallery Style</option>
                                            <option value="grid" <?php echo 'grid' === $cleanstyle ? 'selected' : '' ?> >Grid (default)</option>
                                            <option value="listview" <?php echo 'listview' === $cleanstyle ? 'selected' : '' ?> >Vertical List</option>
                                            <option value="carousel" <?php echo 'carousel' === $cleanstyle ? 'selected' : '' ?> >Horizontal Slider</option>
                                        </select>
                                        Switch from the grid style of the free version to another gallery style. Right now, we provide a vertical (single column) and horizontal (single row) list style as alternatives to the grid, with more designs coming. These current alternatives were inspired by the standard YouTube playlist player's "table of contents," except our gallery's video lists are always visible and shown under the playing video.
                                        <a target="_blank" href="<?php echo self::$epbase ?>/responsive-youtube-playlist-channel-gallery-for-wordpress.aspx">Read more here &raquo;</a>
                                    </label>
                                </p>

                                <div class="hr"></div>
                                <p>
                                    <img class="ssaltgallery" src="<?php echo plugins_url('images/ssverticallayout.png', __FILE__) ?>" />
                                    <input name="<?php echo self::$opt_gallery_showdsc; ?>" id="<?php echo self::$opt_gallery_showdsc; ?>" <?php checked($all[self::$opt_gallery_showdsc], 1); ?> type="checkbox" class="checkbox">
                                    <label for="<?php echo self::$opt_gallery_showdsc; ?>">
                                        <b>(PRO)</b> <b class="chktitle">Show Gallery Descriptions (for vertical list styling): </b> 
                                        For the vertical list layout, this option will show full video descriptions (taken directly from YouTube.com) with each thumbnail. Note: these descriptions only apply the vertical list layout; other layouts don't have enough room.
                                    </label>
                                </p>
                                <div class="p">
                                    <input name="<?php echo self::$opt_gallery_limit_dsc; ?>" id="<?php echo self::$opt_gallery_limit_dsc; ?>" <?php checked($all[self::$opt_gallery_limit_dsc], 1); ?> type="checkbox" class="checkbox">
                                    <label for="<?php echo self::$opt_gallery_limit_dsc ?>">
                                        <b>(PRO)</b> <b class="chktitle">Description Limit:</b>
                                        Limit the number of characters shown in the description, when using the vertical list styling.
                                        <strong class="check-note">
                                            Number of characters:
                                            <input min="1" name="<?php echo self::$opt_gallery_length_dsc; ?>" id="<?php echo self::$opt_gallery_length_dsc; ?>" type="number" class="textinput" style="width: 90px;" value="<?php echo esc_attr(trim($all[self::$opt_gallery_length_dsc])); ?>">
                                        </strong>
                                    </label>
                                </div>
                                <div class="hr"></div>
                                <p>
                                    <img class="ssaltgallery" src="<?php echo plugins_url('images/ssaltgallerycircles.jpg', __FILE__) ?>" />
                                    <?php $cleancrop = trim($all[self::$opt_gallery_thumbcrop]); ?>
                                    <label for="<?php echo self::$opt_gallery_thumbcrop; ?>">
                                        <b>(PRO)</b>  <b class="chktitle">Gallery Thumbnail Shape:</b>
                                        Differentiate your gallery by showing different thumbnail shapes.  We currently offer rectangle and circle shapes.
                                    </label>
                                    <br>
                                    <select name="<?php echo self::$opt_gallery_thumbcrop; ?>" id="<?php echo self::$opt_gallery_thumbcrop; ?>" >
                                        <option value="">Thumbnail Shape</option>
                                        <option value="box" <?php echo 'box' === $cleancrop ? 'selected' : '' ?> >Rectangle (default)</option>
                                        <option value="portal" <?php echo 'portal' === $cleancrop ? 'selected' : '' ?> >Circular</option>
                                    </select>
                                </p>
                                <div class="hr"></div>
                                <p>
                                    <img class="sspopupplayer" src="<?php echo plugins_url('images/sspopupplayer.jpg', __FILE__) ?>" />
                                    <?php $cleandisp = trim($all[self::$opt_gallery_disptype]); ?>
                                    <label for="<?php echo self::$opt_gallery_disptype; ?>">
                                        <b>(PRO)</b>  <b class="chktitle">Gallery Video Display Mode:</b>
                                        Display your gallery videos simply above the thumbnails (default), or as a popup lightbox.
                                        Choosing "popup lightbox" will make your videos lazy-load, which will provide some performance benefits since the YouTube player is not initially loaded with your page. It's loaded with a popup only when a user clicks a thumbnail.
                                    </label>
                                    <br>
                                    <select name="<?php echo self::$opt_gallery_disptype; ?>" id="<?php echo self::$opt_gallery_disptype; ?>" >
                                        <option value="">Display Type</option>
                                        <option value="default" <?php echo 'default' === $cleandisp ? 'selected' : '' ?> >Above Thumbnails (default)</option>
                                        <option value="lb" <?php echo 'lb' === $cleandisp ? 'selected' : '' ?> >Popup Lightbox</option>
                                    </select>
                                </p>
                                <div class="hr"></div>

                                <p>
                                    <img class="sshidethumbimg" src="<?php echo plugins_url('images/sshidethumbimg.jpg', __FILE__) ?>" />
                                    <input name="<?php echo self::$opt_gallery_hidethumbimg; ?>" id="<?php echo self::$opt_gallery_hidethumbimg; ?>" <?php checked($all[self::$opt_gallery_hidethumbimg], 1); ?> type="checkbox" class="checkbox">
                                    <label for="<?php echo self::$opt_gallery_hidethumbimg; ?>">
                                        <b>(PRO)</b> <b class="chktitle">Hide Thumbnail Images:</b>
                                        (For "Grid" and "Vertical List" gallery layouts only) Hide the image for each thumbnail, leaving just the text. This can improve performance when imagery is not important.
                                        <a href="<?php echo self::$epbase ?>/responsive-youtube-playlist-channel-gallery-for-wordpress.aspx" target="_blank">See an example here &raquo;</a>
                                    </label>
                                </p>


                                <div class="hr"></div>

                                <p>
                                    <input name="<?php echo self::$opt_spdc; ?>" id="<?php echo self::$opt_spdc; ?>" <?php checked($all[self::$opt_spdc], 1); ?> type="checkbox" class="checkbox">
                                    <label for="<?php echo self::$opt_spdc; ?>">
                                        <b>(PRO)</b> <b class="chktitle">Faster Page Loads (Caching): </b> 
                                        Use embed caching to speed up your page loads. By default, WordPress needs to request information from YouTube.com's servers for every video you embed, every time a page is loaded. These data requests can add time to your total page load time. Turn on this feature to cache that data (instead of having to request for the same information every time you load a page). This should then make your pages that have videos load faster.  It's been noted that even small speed ups in page load can help increase visitor engagement, retention, and conversions. Caching also makes galleries run faster.

                                    </label>
                                <div class="indent-option">
                                    <div id="boxspdc">
                                        <?php
                                        $all_plugins = get_plugins();
                                        $all_active_plugins = get_option('active_plugins');
                                        $all_caching_plugins = array(
                                            'litespeed-cache/litespeed-cache.php',
                                            'w3-total-cache/w3-total-cache.php',
                                            'wp-fastest-cache/wpFastestCache.php',
                                            'wp-super-cache/wp-cache.php',
                                            'wp-rocket/wp-rocket.php'
                                        );
                                        //echo sprintf('<pre>%s</pre>', print_r($all_active_plugins, true));
                                        $active_caching_plugins = array_intersect($all_active_plugins, $all_caching_plugins);
                                        if (!empty($active_caching_plugins))
                                        {
                                            ?>
                                            <strong class="orange">Please uncheck this YouTube caching feature if you are already using a separate caching plugin. It looks like you already have the below caching plugin(s) active:</strong>
                                            <ul class="reglist">
                                                <?php
                                                foreach ($active_caching_plugins as $idx => $cp)
                                                {
                                                    echo '<li>' . esc_html($all_plugins[$cp]['Name']) . '</li>';
                                                }
                                                ?>
                                            </ul>
                                            <br>
                                            <?php
                                        }
                                        if ((bool) $all[self::$opt_gdpr_consent])
                                        {
                                            ?>
                                            <strong class="orange">Note: caching will always be disabled if GDPR Consent Mode is on, in order to display the consent message to each visitor properly.</strong>
                                            <?php
                                        }
                                        ?>


                                        <div class="pad10">
                                            <input type="button" class="button button-primary" value="Click to clear YouTube cache"/>
                                            <span style="display: none;" class="orange bold clearspdcloading">Clearing...</span>
                                            <span  class="orange bold clearspdcsuccess" style="display: none;">Finished clearing YouTube cache.</span>
                                            <span class="orange bold clearspdcfailed" style="display: none;">Sorry, there seemed to be a problem clearing the cache.</span>
                                        </div>
                                        <label>
                                            <b class="chktitle">Cache Lifetime (hours): </b>
                                            <input name="<?php echo self::$opt_spdcexp; ?>" id="<?php echo self::$opt_spdcexp; ?>" value="<?php echo min(720, intval($all[self::$opt_spdcexp])); ?>" type="number" min="1" max="720"/>
                                            Tip: If your pages rarely change, you may wish to set this to a much higher value than 24 hours (the max allowed is 720 hours, which is 30 days).
                                        </label>
                                        <br>
                                        <br>
                                        <label>
                                            <input name="<?php echo self::$opt_spdcab; ?>" id="<?php echo self::$opt_spdcab; ?>" <?php checked($all[self::$opt_spdcab], 1); ?> type="checkbox" class="checkbox"> 
                                            <b class="chktitle">Show "Clear YouTube Cache" Admin Bar Button: </b> 
                                            This will display the "Clear YouTube Cache" button conveniently in the top admin bar. Uncheck this if you wish to hide the button.
                                        </label>

                                    </div>
                                </div>
                                </p>
                                <div class="hr"></div>

                                <p>
                                    <input name="<?php echo self::$opt_schemaorg; ?>" id="<?php echo self::$opt_schemaorg; ?>" <?php echo defined('WPSEO_VIDEO_FILE') ? ' disabled ' : checked($all[self::$opt_schemaorg], 1, false); ?> type="checkbox" class="checkbox">
                                    <label for="<?php echo self::$opt_schemaorg; ?>">
                                        <b>(PRO)</b> <b class="chktitle">Video SEO Tags:</b>
                                        <?php
                                        if (defined('WPSEO_VIDEO_FILE'))
                                        {
                                            ?>
                                            <strong><span class="orange">Note:</span> Looks like you already have Yoast's Video SEO plugin installed. This option is disabled to allow Yoast's plugin to take over.</strong>
                                            <br>
                                            <?php
                                        }
                                        ?>                                        
                                        Update your YouTube embeds with Google, Bing, and Yahoo friendly schema markup for videos.                                        
                                    </label>
                                    <span id="boxschemaorg">
                                        <span class="apikey-msg">
                                            The video SEO tags include data like the title, description, and thumbnail information of each video you embed. This plugin automatically extracts this data directly from YouTube using the version 3 API. This particular API version requires that you obtain a server API key so that YouTube can authenticate the requests. <a href="https://www.youtube.com/watch?v=ZCfrNvu6nMc" target="_blank">Watch this video to see how to create your own key</a>. Then, paste it in the "YouTube API Key" box at the top of this screen, and click the "Save Changes" button.
                                        </span>
                                    </span>
                                </p>
                                <div class="hr"></div>                                
                                <p>
                                    <input name="<?php echo self::$opt_ogvideo; ?>" id="<?php echo self::$opt_ogvideo; ?>" <?php checked($all[self::$opt_ogvideo], 1); ?> type="checkbox" class="checkbox">
                                    <label for="<?php echo self::$opt_ogvideo; ?>">
                                        <b>(PRO)</b> <b class="chktitle">Facebook Open Graph Markup:</b>  Include video-specific Facebook Open Graph markup with the videos you embed with this plugin.  We follow the guidelines for videos as described here: <a href="https://developers.facebook.com/docs/sharing/webmasters#media" target="_blank">https://developers.facebook.com/docs/sharing/webmasters#media</a>.
                                        Note: We recommend using this feature along with a general SEO plugin to determine your page's object type, etc.
                                    </label>
                                </p>
                                <div class="hr"></div>
                                <p>
                                    <img class="ssfb" src="<?php echo plugins_url('images/youtube_thumbnail_sample.jpg', __FILE__) ?>" />
                                    <input name="<?php echo self::$opt_ftpostimg; ?>" id="<?php echo self::$opt_ftpostimg; ?>" <?php checked($all[self::$opt_ftpostimg], 1); ?> type="checkbox" class="checkbox">
                                    <label for="<?php echo self::$opt_ftpostimg; ?>">
                                        <b>(PRO)</b> <b class="chktitle">Featured Thumbnail Images: </b> 
                                        Automatically grab the thumbnail image of the first video embedded in each post or page, and use it as the featured image.  If your theme can display featured images of posts on your blog home, you'll see the thumbnails there as shown in the picture on the right.  All you have to do is click Update on a post or page and the plugin does the rest!
                                        (Example shown on the right) <a target="_blank" href="<?php echo self::$epbase ?>/add-youtube-video-thumbnails-featured-image-wordpress.aspx">Watch example here &raquo;</a>
                                    </label>
                                </p>

                                <?php
                            }
                            else
                            {
                                ?>
                                <p class="smallnote orange">Below are PRO features for enhanced SEO and performance (works for even past embed links). </p>
                                <p>
                                    <img class="ssaltgallery" src="<?php echo plugins_url('images/ssaltgalleryall.jpg', __FILE__) ?>" />
                                    <select disabled>
                                        <option value="">Gallery Style</option>
                                    </select>
                                    <label>
                                        <b class="chktitle">Advanced Gallery Customization Options: </b> <span class="pronon">(PRO Users)</span> 
                                        Switch from the grid style of the FREE version to another gallery style. Right now, we provide a vertical (single column) and horizontal (single row) list style as alternatives to the grid, with more designs coming. These current alternatives were inspired by the standard YouTube playlist player's "table of contents," except our gallery's video lists are always visible and shown under the playing video.
                                        <a target="_blank" href="<?php echo self::$epbase ?>/responsive-youtube-playlist-channel-gallery-for-wordpress.aspx">Read more here &raquo;</a>
                                    </label>
                                </p>

                                <div class="hr"></div>
                                <p>
                                    <img class="ssaltgallery" src="<?php echo plugins_url('images/ssverticallayout.png', __FILE__) ?>" />
                                    <input disabled type="checkbox" class="checkbox">
                                    <label>
                                        <b class="chktitle">Show Gallery Descriptions (for vertical list styling): </b>  <span class="pronon">(PRO Users)</span> 
                                        For the vertical list layout, this option will show full video descriptions (taken directly from YouTube.com) with each thumbnail. Note: these descriptions only apply the vertical list layout; other layouts don't have enough room.
                                    </label>
                                </p>
                                <div class="hr"></div>
                                <p>
                                    <img class="ssaltgallery" src="<?php echo plugins_url('images/ssaltgallerycircles.jpg', __FILE__) ?>" />
                                    <select disabled>
                                        <option value="">Select Thumbnail Shape</option>
                                    </select>
                                    <label>
                                        <b class="chktitle">Gallery Thumbnail Shape: </b> <span class="pronon">(PRO Users)</span> 
                                        Differentiate your gallery by showing different thumbnail shapes.  We currently offer rectangle and circle shapes.
                                        <a target="_blank" href="<?php echo self::$epbase ?>/responsive-youtube-playlist-channel-gallery-for-wordpress.aspx">Read more here &raquo;</a>
                                    </label>
                                </p>

                                <div class="hr"></div>
                                <p>
                                    <img class="sspopupplayer" src="<?php echo plugins_url('images/sspopupplayer.jpg', __FILE__) ?>" />
                                    <label>
                                        <b class="chktitle">Gallery Video Display Mode: </b> <span class="pronon">(PRO Users)</span>
                                        Display your gallery videos simply above the thumbnails (default), or as a popup lightbox.
                                    </label>
                                    <br>
                                    <input type="radio" disabled> Default &nbsp; <input type="radio" disabled> Popup lightbox
                                </p>

                                <div class="hr"></div>
                                <p>
                                    <input disabled type="checkbox" class="checkbox">
                                    <label>
                                        <b class="chktitle">Faster Page Loads (Caching): </b>  <span class="pronon">(PRO Users)</span> 
                                        Use embed caching to speed up your page loads. By default, WordPress needs to request information from YouTube.com's servers for every video you embed, every time a page is loaded. These data requests can add time to your total page load time. Turn on this feature to cache that data (instead of having to request for the same information every time you load a page). This should then make your pages that have videos load faster.  It's been noted that even small speed ups in page load can help increase visitor engagement, retention, and conversions. Caching also makes galleries run faster.
                                    </label>
                                <div class="indent-option">
                                    <label>
                                        <b class="chktitle">Cache Lifetime (hours): </b> 
                                        <input disabled value="24" type="number">
                                        Tip: If your pages rarely change, you may wish to set this to a much higher value than 24 hours.
                                    </label>
                                </div>
                                </p>
                                <div class="hr"></div>


                                <p>
                                    <input disabled type="checkbox" class="checkbox">
                                    <label>
                                        <b class="chktitle">Video SEO Tags:</b>  <span class="pronon">(PRO Users)</span> Update your YouTube embeds with Google, Bing, and Yahoo friendly schema markup for videos.
                                    </label>
                                </p>
                                <div class="hr"></div>
                                <p>
                                    <input disabled type="checkbox" class="checkbox">
                                    <label>
                                        <b class="chktitle">Special Lazy-Loading Effects:</b>  <span class="pronon">(PRO Users)</span> 
                                        Add eye-catching special effects that will make your YouTube embeds bounce, flip, pulse, or slide as they lazy load on the screen.  Check this box to select your desired effect. <a target="_blank" href="<?php echo self::$epbase ?>/add-special-effects-to-youtube-embeds-in-wordpress.aspx">Read more here &raquo;</a>
                                    </label>
                                </p>
                                <div class="hr"></div>
                                <p>
                                    <input disabled type="checkbox" class="checkbox">
                                    <label>
                                        <b class="chktitle">Facebook Open Graph Markup:</b> <span class="pronon">(PRO Users)</span>   Include Facebook Open Graph markup with the videos you embed with this plugin.  We follow the guidelines for videos as described here: <a href="https://developers.facebook.com/docs/sharing/webmasters#media" target="_blank">https://developers.facebook.com/docs/sharing/webmasters#media</a>
                                    </label>
                                </p>
                                <div class="hr"></div>
                                <p>
                                    <img class="ssfb" src="<?php echo plugins_url('images/youtube_thumbnail_sample.jpg', __FILE__) ?>" />
                                    <input disabled type="checkbox" class="checkbox">
                                    <label>
                                        <b class="chktitle">Featured Thumbnail Images:</b>  <span class="pronon">(PRO Users)</span> 
                                        Automatically grab the thumbnail image of the first video embedded in each post or page, and use it as the featured image. 
                                        All you have to do is click Update on a post or page and the plugin does the rest! 
                                        (Example shown on the right) <a target="_blank" href="<?php echo self::$epbase ?>/add-youtube-video-thumbnails-featured-image-wordpress.aspx">Read more here &raquo;</a>
                                    </label>
                                </p>
                                <div class="hr"></div>
                                <p>
                                    <a href="<?php echo self::$epbase ?>/dashboard/pro-easy-video-analytics.aspx" target="_blank">Activate the above and several other features &raquo;</a>
                                </p>
                                <?php
                            }
                            ?>
                            <div class="clearboth"></div>
                        </div>

                    </section>
                    <?php
                    if (!(bool) (self::$alloptions[self::$opt_vi_hide_monetize_tab]))
                    {
                        ?>
                        <section class="pattern" id="jumpmonetize">

                            <?php
                            //self::vi_monetize_title();
                            if (self::vi_script_setup_done())
                            {
                                echo '<h2>';
                                self::vi_print_toggle_button();
                                echo '</h2>';
                            }
                            ?>

                            <?php
                            if (!self::vi_logged_in())
                            {
                                echo '<div class="vi-registration-box">';
                                include_once(EPYTVI_INCLUDES_PATH . 'vi_registration_form.php');
                                include_once(EPYTVI_INCLUDES_PATH . 'vi_login_success.php');
                                echo '</div>';
                            }
                            else
                            {
                                include_once(EPYTVI_INCLUDES_PATH . 'vi_login_complete.php');
                            }
                            ?>
                        </section>
                    <?php } ?>
                    <section class="pattern" id="jumpcompat">
                        <h2>Compatibility Settings</h2>
                        <p>
                            With tens of thousands of active users, our plugin may not work with every plugin out there. Below are some settings you may wish to try out. 
                        </p>
                        <div class="ytindent chx">
                            <p>
                                <input name="<?php echo self::$opt_ajax_save; ?>" id="<?php echo self::$opt_ajax_save; ?>" <?php checked($all[self::$opt_ajax_save], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_ajax_save; ?>">
                                    <b class="chktitle">Save Settings with AJAX: </b>
                                    Turn this option off if you are having trouble saving your settings.
                                </label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_gb_compat; ?>" id="<?php echo self::$opt_gb_compat; ?>" <?php checked($all[self::$opt_gb_compat], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_gb_compat; ?>">
                                    <b class="chktitle">Gutenberg Block Editor Theme Spacing: </b> 
                                    Check this option to fix possible issues with spacing below your videos. You may also want to try combining this option with Responsive Sizing.
                                </label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_old_script_method; ?>" id="<?php echo self::$opt_old_script_method; ?>" <?php checked($all[self::$opt_old_script_method], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_old_script_method; ?>">
                                    <b class="chktitle">Use Legacy Scripts: </b>
                                    This is a legacy option for users with theme issues that require backwards compatibility (v.10.5 or earlier). It may also help with caching plugin or CDN plugin issues.
                                </label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_admin_off_scripts; ?>" id="<?php echo self::$opt_admin_off_scripts; ?>" <?php checked($all[self::$opt_admin_off_scripts], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_admin_off_scripts; ?>">
                                    <b class="chktitle">Turn Off Scripts While Editing: </b>
                                    Front-end editors and visual pagebuilders often run Javascript while you're in edit mode. Check this to turn off this plugin's Javascript during edit mode, if you see conflicts.
                                    Don't worry, all other visitors to your site will still view your site normally.
                                </label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_migrate; ?>" id="<?php echo self::$opt_migrate; ?>" <?php checked($all[self::$opt_migrate], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_migrate; ?>">
                                    <b class="chktitle">Migrate Shortcodes: </b> Inherit shortcodes from other plugins. This is useful for when a plugin becomes deprecated, or you simply prefer this plugin's features.
                                </label>
                            <div id="boxmigratelist">
                                <ul>
                                    <li><input name="<?php echo self::$opt_migrate_embedplusvideo; ?>" id="<?php echo self::$opt_migrate_embedplusvideo; ?>" <?php checked($all[self::$opt_migrate_embedplusvideo], 1); ?> type="checkbox" class="checkbox"><label for="<?php echo self::$opt_migrate_embedplusvideo; ?>"><b>"YouTube Advanced Embed":</b>   <code>[embedplusvideo]</code> shortcode</label></li>
                                    <li><input name="<?php echo self::$opt_migrate_youtube; ?>" id="<?php echo self::$opt_migrate_youtube; ?>" <?php checked($all[self::$opt_migrate_youtube], 1); ?> type="checkbox" class="checkbox"><label for="<?php echo self::$opt_migrate_youtube; ?>"><b>"YouTube Embed":</b> <code>[youtube]</code> and <code>[youtube_video]</code> shortcodes</label></li>
                                    <li class="smallnote orange" style="list-style: none;">This feature is beta. More shortcodes coming.</li>
                                </ul>

                            </div>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_oldspacing; ?>" id="<?php echo self::$opt_oldspacing; ?>" <?php checked($all[self::$opt_oldspacing], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_oldspacing; ?>">
                                    <b class="chktitle">Legacy Spacing:</b> Continue the spacing style from version 4.0 and older. Those versions required you to manually add spacing above and below your video. Unchecking this will automatically add the spacing.
                                </label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_frontend_only; ?>" id="<?php echo self::$opt_frontend_only; ?>" <?php checked($all[self::$opt_frontend_only], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_frontend_only; ?>">
                                    <b class="chktitle">Don't Run Shortcode In Admin:</b>
                                    Checking this will only allow the shortcode to run on the front-end of your website, and not in the admin area.
                                </label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_evselector_light; ?>" id="<?php echo self::$opt_evselector_light; ?>" <?php checked($all[self::$opt_evselector_light], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_evselector_light; ?>">
                                    <b class="chktitle">Theme Video Problems: </b> 
                                    Check this option if you're having issues with autoplayed videos or background videos etc. that have been generated by your theme.
                                </label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_stop_mobile_buffer; ?>" id="<?php echo self::$opt_stop_mobile_buffer; ?>" <?php checked($all[self::$opt_stop_mobile_buffer], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_stop_mobile_buffer; ?>">
                                    <b class="chktitle">Mobile Autoplay Problems: </b> 
                                    Autoplay works for desktop, but mobile devices don't allow autoplay due to network carrier data charges. For mobile devices, this option may help the player to properly display the video for the visitor to click on.
                                    (<strong>Note:</strong> Desktop browsers like <a href="https://developers.google.com/youtube/iframe_api_reference#Mobile_considerations" target="_blank">Chrome and Safari are moving towards preventing autoplay for any video</a>. But, your chances are improved if you set your videos to initially start muted.)
                                </label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_ajax_compat; ?>" id="<?php echo self::$opt_ajax_compat; ?>" <?php checked($all[self::$opt_ajax_compat], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_ajax_compat; ?>">
                                    <b class="chktitle">Ajax Theme:</b>
                                    If you have a theme that loads pages with AJAX transitions, try checking this option.
                                </label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_debugmode; ?>" id="<?php echo self::$opt_debugmode; ?>" <?php checked($all[self::$opt_debugmode], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_debugmode; ?>">
                                    <b class="chktitle">Debug Mode: </b> If you ask for support, we may ask you to turn on debug mode here.
                                    It may print out some diagnostic info so that we can help you solve your issue. 
                                </label>
                            </p>
                            <p>
                                <input name="<?php echo self::$opt_uninstall_data; ?>" id="<?php echo self::$opt_uninstall_data; ?>" <?php checked($all[self::$opt_uninstall_data], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_uninstall_data; ?>">
                                    <b class="chktitle">Delete Options When Uninstalling:</b> Checking this box will permanently delete your options the next time you uninstall the plugin. Leave it unchecked
                                    to preserve your options between installations.
                                </label>
                            </p>

                        </div>



                    </section>
                    <section class="pattern" id="jumphowto">
                        <h2>Manual Embedding</h2>
                        <p>
                            <strong>We strongly recommend using the wizard in your editor to embed.</strong> However, if you choose to manually embed code, follow some legacy instructions below.
                        </p>

                        <h3>
                            Manually Embed a YouTube Video or Playlist &nbsp; <a class="smallnote" href="#jumpgallery">(For gallery directions, go here &raquo;)</a>
                        </h3>
                        <p>
                            <b>For videos:</b> <i>Method 1 - </i> Do you already have a URL to the video you want to embed in a post, page, or even a widget? All you have to do is paste it on its own line, as shown below (including the https:// part). Easy, eh?<br>
                            <i>Method 2 - </i> If you want to do some formatting (e.g. add HTML to center a video) or have two or more videos next to each other on the same line, wrap each link with the <code>[embedyt]...[/embedyt]</code> shortcode. <b>Tip for embedding videos on the same line:</b> As shown in the example image below, decrease the size of each video so that they fit together on the same line (See the "How To Override Defaults" section for height and width instructions).
                        </p>
                        <p>
                            <b>For galleries:</b> <a href="#jumpgallery">Click here</a> to scroll down to gallery settings and directions.
                        </p>
                        <p>
                            <b>For self-contained playlists:</b> Go to the page for the playlist that lists all of its videos (<a target="_blank" href="http://www.youtube.com/playlist?list=PL70DEC2B0568B5469">Example &raquo;</a>). Click on the video that you want the playlist to start with. Copy and paste that browser URL into your blog on its own line. If you want the first video to always be the latest video in your playlist, check the option "Playlist Ordering" in the settings down below (you will also see this option available if you use the Pro Wizard). If you want to have two or more playlists next to each other on the same line, wrap each link with the <code>[embedyt]...[/embedyt]</code> shortcode.
                        </p>                
                        <p>
                            <b>For self-contained channel playlists:</b> At your editor, click on the <img style="vertical-align: text-bottom;" src="<?php echo plugins_url('images/wizbuttonbig.png', __FILE__) ?>"> wizard button and choose the option <i>Search for a video or channel to insert in my editor.</i> Then, click on the <i>channel playlist</i> option there (instead of <i>single video</i>). Search for the channel username and follow the rest of the directions there.
                        </p>
                        <p>
                            <strong>For directly embedding in your theme with PHP:</strong>
                            If you need to use PHP directly, we still recommend using the wizard to create the shortcode--but instead of pressing the "Insert" button, just copy the shortcode the wizard gives you. Then use the <code>do_shortcode()</code> function in your theme, like this:
                            <br>
                            <code>&lt;?php echo do_shortcode('[embedyt]....[/embedyt]'); ?&gt;</code>
                        </p>
                        <p>
                            <b>Examples:</b><br><br>
                            <img style="width: 900px; height: auto;" class="shadow" src="<?php echo plugins_url('images/sshowto.png', __FILE__) ?>" />
                        </p>
                        <p>
                            Always follow these rules for any URL:
                        </p>
                        <ul class="reglist">
                            <li>Make sure the URL is really on its own line by itself. Or, if you need multiple videos on the same line, make sure each URL is wrapped properly with the shortcode (Example:  <code>[embedyt]http://www.youtube.com/watch?v=ABCDEFGHIJK&width=400&height=250[/embedyt]</code>)</li>
                            <li>Make sure the URL is <strong>not</strong> an active hyperlink (i.e., it should just be plain text). Otherwise, highlight the URL and click the "unlink" button in your editor: <img src="<?php echo plugins_url('images/unlink.png', __FILE__) ?>"/></li>
                            <li>Make sure you did <strong>not</strong> format or align the URL in any way. If your URL still appears in your actual post instead of a video, highlight it and click the "remove formatting" button (formatting can be invisible sometimes): <img src="<?php echo plugins_url('images/erase.png', __FILE__) ?>"/></li>
                            <li>If you really want to align the video, try wrapping the link with the shortcode first. For example: <code>[embedyt]http://www.youtube.com/watch?v=ABCDEFGHIJK[/embedyt]</code> Using the shortcode also allows you to have two or more videos next to each other on the same line.  Just put the shortcoded links together on the same line. For example:<br>
                                <code>[embedyt]http://www.youtube.com/watch?v=ABCDEF[/embedyt] [embedyt]http://www.youtube.com/watch?v=GHIJK[/embedyt]</code>
                            </li>
                        </ul>       

                        <h3>
                            <?php _e("How To Manually Override Defaults / Other Options") ?>
                        </h3>
                        <p>
                            Suppose you have a few videos that need to be different from the above defaults. You can add options to the end of a link as displayed below to override the above defaults. Each option should begin with '&'.
                            <br><span class="orange">PRO users: You can use the <a href="<?php echo self::$epbase . '/dashboard/pro-easy-video-analytics.aspx?ref=manual' ?>" target="_blank">customize</a> buttons that you will see inside the wizard, instead of memorizing the following codes.</span>
                        </p>
                        <?php
                        _e('<ul class="reglist">');
                        _e("<li><strong>width</strong> - Sets the width of your player. If omitted, the default width will be the width of your theme's content.<em> Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&width=500</strong>&height=350</em></li>");
                        _e("<li><strong>height</strong> - Sets the height of your player. <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA&width=500<strong>&height=350</strong></em> </li>");
                        _e("<li><strong>autoplay</strong> - Set this to 1 to autoplay the video (or 0 to play the video once). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&autoplay=1</strong></em> (Note: Desktop browsers like Chrome and Safari are moving towards preventing autoplay for any video. But, your chances are improved if you set your videos to initially start muted. ) </li>");
                        _e("<li><strong>cc_load_policy</strong> - Set this to 1 to turn on closed captioning (or 0 to leave them off). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&cc_load_policy=1</strong></em> </li>");
                        _e("<li><strong>iv_load_policy</strong> - Set this to 3 to turn off annotations (or 1 to show them). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&iv_load_policy=3</strong></em> </li>");
                        _e("<li><strong>loop</strong> - Set this to 1 to loop the video (or 0 to not loop). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&loop=1</strong></em> </li>");
                        _e("<li><strong>modestbranding</strong> - Set this to 1 to remove the YouTube logo while playing (or 0 to show the logo). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&modestbranding=1</strong></em> </li>");
                        _e("<li><strong>rel</strong> - Set this to 0 to not show related videos at the end of playing (or 1 to show them). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&rel=0</strong></em> </li>");
                        _e("<li><strong>fs</strong> - Set this to 0 to hide the fullscreen button (or 1 to show it). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&fs=0</strong></em> </li>");
                        _e("<li><strong>color</strong> - Set this to 'white' to make the player have a white progress bar (or 'red' for a red progress bar). Note: Using white will disable the modestbranding option. <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&color=white</strong></em> </li>");
                        _e("<li><strong>controls</strong> - Set this to 0 to completely hide the video controls (or 1 to show it). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&controls=0</strong></em> </li>");
                        _e("<li><strong>playsinline</strong> - Set this to 1 to allow videos play inline with the page on iOS browsers. (Set to 0 to have iOS launch videos in fullscreen instead). <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&playsinline=1</strong></em> </li>");
                        _e("<li><strong>origin</strong> - Set this to 1 to add the 'origin' parameter for extra JavaScript security. <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA<strong>&origin=1</strong></em> </li>");
                        _e('</ul>');
                        _e("<p>You can also start and end each individual video at particular times. Like the above, each option should begin with '&'</p>");
                        _e('<ul class="reglist">');
                        _e("<li><strong>start</strong> - Sets the time (in seconds) to start the video. <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350<strong>&start=20</strong></em> </li>");
                        _e("<li><strong>end</strong> - Sets the time (in seconds) to stop the video. <em>Example: http://www.youtube.com/watch?v=quwebVjAEJA&width=500&height=350<strong>&end=100</strong></em> </li>");
                        _e('</ul>');
                        ?>
                    </section>

                    <section class="pattern" id="jumpperformance">
                        <h2><?php _e('Performance Settings & Tips', 'youtube-embed-plus-pro'); ?></h2>
                        <p>
                            <?php _e('On this page, we describe performance options to help optimize page speed times of your pages containing YouTube embeds.', 'youtube-embed-plus-pro'); ?>
                        </p>
                        <div class="p">
                            <input name="<?php echo self::$opt_facade_mode; ?>" id="<?php echo self::$opt_facade_mode; ?>" <?php checked($all[self::$opt_facade_mode], 1); ?> type="checkbox" class="checkbox">
                            <label for="<?php echo self::$opt_facade_mode ?>">
                                <b class="chktitle"><?php _e('Facade Mode:', 'youtube-embed-plus-pro'); ?> <sup class="orange">new</sup></b> 
                                <?php _e('This improves performance by loading a lighter version of the player, until it is clicked. Then the real player loads (note: for live streams, the real player is always loaded).  We have tested this feature in multiple cases and found it to successfully improve your Lighthouse performance score by addressing  the following recommendation: "Some third-party resources can be lazy loaded with a facade."', 'youtube-embed-plus-pro'); ?>
                                <a href="https://www.youtube.com/watch?v=W7PKUjVBDNE" target="_blank"><?php _e('See an example of this feature at work.', 'youtube-embed-plus-pro'); ?></a>
                            </label>                       
                            <div class="p box_facade_mode">
                                <input name="<?php echo self::$opt_facade_autoplay; ?>" id="<?php echo self::$opt_facade_autoplay; ?>" type="checkbox" class="checkbox" <?php checked($all[self::$opt_facade_autoplay], 1); ?>>
                                <label for="<?php echo self::$opt_facade_autoplay ?>">
                                    <b class="chktitle"><?php _e('Autoplay On Facade Click:', 'youtube-embed-plus-pro'); ?></b>
                                    <span>
                                        <?php _e('After clicking once on the facade (aka light thumbnail), it is replaced with the real player. Check this option to have the real player play immediately, otherwise it will require an additional click. Note that checking this option will use YouTube\'s autoplay feature, which will not contribute toward play counts.  If you\'re embedding videos from someone else\'s channel, we recommend checking this.  If you\'re embedding videos that are from your channel, then you should self-evaluate the tradeoff involving play counts and additional clicking.', 'youtube-embed-plus-pro'); ?>
                                    </span>
                                </label>
                                <br>
                                <br>
                                <label>
                                    <b class="chktitle"><?php _e('Force Maximum Quality for Facade Images: <sup class="orange">new</sup>', 'text_domain') ?></b>
                                    <?php _e('Attempt to load the maximum resolution image for the facade image. This max quality image does not exist for all videos, so you have a few choices below.', 'text_domain'); ?>
                                </label>
                                <ul class="indent-option">
                                    <li>
                                        <input type="radio" name="<?php echo self::$opt_maxres_facade; ?>" id="<?php echo self::$opt_maxres_facade; ?>_eager" value="eager" <?php checked($all[self::$opt_maxres_facade], 'eager'); ?>>
                                        <label for="<?php echo self::$opt_maxres_facade; ?>_eager"> <?php _e("<em>Eager:</em> Try loading the max resolution image by default. If the max-res image does not exist for the video, the next highest available resolution will immediately load instead. This option is best for websites that embed videos having very high resolutions (i.e. the max-res images will likely exist).", 'text_domain') ?> </label> <br>
                                    </li>
                                    <li>
                                        <input type="radio" name="<?php echo self::$opt_maxres_facade; ?>" id="<?php echo self::$opt_maxres_facade; ?>_soft" value="soft" <?php checked($all[self::$opt_maxres_facade], 'soft'); ?>>
                                        <label for="<?php echo self::$opt_maxres_facade; ?>_soft"> <?php _e("<em>Soft:</em> Load a relatively high resolution image that is guaranteed to exist, but immediately try updating the image to maximum resolution if it exists. This option is best for sites with some videos that may have a missing max resolution image.", 'text_domain') ?> </label>  <br>
                                    </li>
                                    <li>
                                        <input type="radio" name="<?php echo self::$opt_maxres_facade; ?>" id="<?php echo self::$opt_maxres_facade; ?>_off" value="off" <?php checked($all[self::$opt_maxres_facade], 'off'); ?>>
                                        <label for="<?php echo self::$opt_maxres_facade; ?>_off"><?php _e("<em>Off:</em> Don't try to maximize facade image quality. Just load the relatively high resolution image that all videos have.", 'text_domain') ?></label>  <br>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="p">
                            <input name="<?php echo self::$opt_defer_js; ?>" id="<?php echo self::$opt_defer_js; ?>" <?php checked($all[self::$opt_defer_js], 1); ?> type="checkbox" class="checkbox">
                            <label for="<?php echo self::$opt_defer_js ?>">
                                <b class="chktitle"><?php _e('Defer Javascript:', 'youtube-embed-plus-pro'); ?></b> 
                                <?php _e('JavaScript (JS) deferral is a common website performance option that can offer significant improvements of page speed. You can reduce the initial load time of your page by allowing this plugin\'s scripts to begin execution only after a page is loaded. You may receive a better GTMetrix score with this option turned on. Note: This feature is compatible with most sites, but turn it off if you are having issues.', 'youtube-embed-plus-pro'); ?>                                
                            </label>                       
                            <div class="p box_defer_jquery">
                                <input name="<?php echo self::$opt_defer_jquery; ?>" id="<?php echo self::$opt_defer_jquery; ?>" type="checkbox" class="checkbox" <?php checked($all[self::$opt_defer_jquery], 1); ?>>
                                <label for="<?php echo self::$opt_defer_jquery ?>">
                                    <b class="chktitle"><?php _e('Also Defer jQuery:', 'youtube-embed-plus-pro'); ?></b>
                                    <span style="color: red;">
                                        <?php _e('Note: Do NOT check this option if you are using a pagebuilder. Furthermore, defering jQuery may improve your GTMetrix score even more, but might not be compatible with your theme or other plugins (especially if they are not defering their own scripts). Use this option with caution.', 'youtube-embed-plus-pro'); ?>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <p><?php _e('Note: Since the YouTube player is loaded in its own iframe from YouTube\'s servers, browser restrictions prevent this plugin from directly deferring the JS inside the iframe. However, you can use facade mode above, or the lazy loading feature <a href="#jumpprosettings">described here &raquo;</a> to further improve your page speeds.', 'youtube-embed-plus-pro'); ?></p>
                        <!--                        <div style="width: 50%">
                                                    <div class="epyt-fitvid">
                                                        <iframe allow="encrypted-media" allowfullscreen="" src="https://www.youtube-nocookie.com/embed/?autoplay=0&amp;cc_load_policy=0&amp;iv_load_policy=1&amp;loop=0&amp;modestbranding=0&amp;fs=1&amp;playsinline=0&amp;controls=1&amp;color=red&amp;rel=1&amp;autohide=2&amp;theme=dark&amp;"></iframe>
                                                    </div>
                                                </div>-->
                    </section>

                    <div class="save-changes-follow"> <?php self::save_changes_button(isset($_POST[$ytprefs_submitted]) && $_POST[$ytprefs_submitted] == 'Y'); ?> </div>
                </form>

                <section class="pattern" id="jumpsupport">
                    <h2>Plugin Support</h2>
                    <div id="nonprosupport">
                        <p>
                            Here is a short video explaining a few of the plugin's features:
                        </p>                        
                        <div class="epyt-fitvid">
                            <iframe src="https://www.youtube.com/embed/QDdvXBqfrzM?rel=0" allowfullscreen="" frameborder="0"></iframe>
                        </div>
                        <p>
                            We've also found that a common support request has been from users that are pasting video links on single lines, as required, but are not seeing the video embed show up. One of these suggestions is usually the fix:                            
                        </p>                        
                        <ul class="reglist">
                            <li>Make sure the URL is really on its own line by itself. Or, if you need multiple videos on the same line, make sure each URL is wrapped properly with the shortcode (Example:  <code>[embedyt]http://www.youtube.com/watch?v=ABCDEFGHIJK&width=400&height=250[/embedyt]</code>)</li>
                            <li>Make sure the URL is not an active hyperlink (i.e., it should just be plain text). Otherwise, highlight the URL and click the "unlink" button in your editor: <img src="<?php echo plugins_url('images/unlink.png', __FILE__) ?>"/>.</li>
                            <li>Make sure you did <strong>not</strong> format or align the URL in any way. If your URL still appears in your actual post instead of a video, highlight it and click the "remove formatting" button (formatting can be invisible sometimes): <img src="<?php echo plugins_url('images/erase.png', __FILE__) ?>"/></li>
                            <li>Try wrapping the URL with the <code>[embedyt]...[/embedyt]</code> shortcode. For example: <code>[embedyt]http://www.youtube.com/watch?v=ABCDEFGHIJK[/embedyt]</code> Using the shortcode also allows you to have two or more videos next to each other on the same line.  Just put the shortcoded links together on the same line. For example:<br>
                                <code>[embedyt]http://www.youtube.com/watch?v=ABCDEF&width=400&height=250[/embedyt] [embedyt]http://www.youtube.com/watch?v=GHIJK&width=400&height=250[/embedyt]</code>
                                <br> TIP: As shown above, decrease the size of each video so that they fit together on the same line (See the "How To Override Defaults" section for height and width instructions)
                            </li>
                            <li>If you upload a new video to a playlist or channel and that video is not yet showing up on a gallery you embedded, you should clear/reset any caching plugins you have. This will force your site to retrieve the freshest version of your playlist and/or channel video listing.  If you don't reset you cache, then you'll have to wait until cache lifetime expires.</li>
                            <li>Finally, there's a slight chance your custom theme is the issue, if you have one. To know for sure, we suggest temporarily switching to one of the default WordPress themes (e.g., "Twenty Fourteen") just to see if your video does appear. If it suddenly works, then your custom theme is the issue. You can switch back when done testing.</li>
                            <li>If your videos always appear full size, try turning off "Responsive video sizing."</li>
                            <li>If none of the above work, you can contact us here if you still have issues: ext@embedplus.com. We'll try to respond within a week. PRO users should use the priority form below for faster replies.</li>                        
                        </ul>
                        <p>
                            Deactivating the No Cookies option has also been proven to solve player errors.
                        </p>
                        <p>
                            We also have a YouTube channel. We use it to provide users with some helper videos and a way to keep updated on new features as they are introduced. <a href="https://www.youtube.com/subscription_center?add_user=EmbedPlus" target="_blank">Subscribe for tips and updates here &raquo;</a>
                        </p>
                    </div>
                    <br>
                    <h3>
                        Priority Support
                    </h3>
                    <p>
                        <strong>PRO users:</strong> Below, We've enabled the ability to have priority support with our team.*  Use this to get one-on-one help with any issues you might have or to send us suggestions for future features.  We typically respond during normal work hours. We're always happy to accept any testimonials you might have as well.
                    </p>
                    <div class="ytprefs-pro-support">
                        <label>
                            <em>
                                <input type="checkbox"/> I understand that the EmbedPlus.com form uses cookies, and the following will need to be sent with the form in order to get help with my issue:
                                <ul class="reglist">
                                    <li>Browser version</li>
                                    <li>Domain name</li>
                                    <li>WordPress version</li>
                                    <li>Plugin version</li>
                                </ul>
                            </em>
                        </label>
                        <p>
                            <button disabled type="button" class="button-primary">Click to load priority support form</button>
                        </p>
                        <p>
                            <em>*Has your priority support period ended? <a href="https://www.embedplus.com/dashboard/pro-easy-video-analytics.aspx" target="_blank">Start a new one here &raquo;</a></em>
                        </p>
                        <iframe data-src="<?php echo self::$epbase ?>/dashboard/prosupport.aspx?simple=1&prokey=<?php echo esc_attr($all[self::$opt_pro]); ?>&domain=<?php echo site_url(); ?>&wpversion=<? echo get_bloginfo('version'); ?>&pluginversion=<? echo self::$version ?>" width="500" height="<?php echo ($all[self::$opt_pro] && strlen(trim($all[self::$opt_pro])) > 0) ? "500" : "140"; ?>"></iframe>
                    </div>

                </section>
            </div>
        </div>
        <script type="text/javascript">
            (function ($)
            {
                window.savevalidate = function (e)
                {
                    var $formDefaults = $(e.target);
                    var valid = true;
                    var $tabFocus = '';

                    var alertmessage = '';
                    if (jQuery("#<?php echo self::$opt_defaultdims; ?>").is(":checked"))
                    {
                        if (!(jQuery.isNumeric(jQuery.trim(jQuery("#<?php echo self::$opt_defaultwidth; ?>").val())) &&
                                jQuery.isNumeric(jQuery.trim(jQuery("#<?php echo self::$opt_defaultheight; ?>").val()))))
                        {
                            alertmessage += "Please enter valid numbers for default height and width, or uncheck the option.";
                            jQuery("#boxdefaultdims input").css("background-color", "#ffcccc").css("border", "2px solid #000000");
                            valid = false;
                            $tabFocus = $("#<?php echo self::$opt_defaultdims; ?>").closest('section');
                        }
                    }

                    if (jQuery("#<?php echo self::$opt_gallery_customarrows; ?>").is(":checked"))
                    {
                        if (!jQuery.trim(jQuery("#<?php echo self::$opt_gallery_customprev; ?>").val()) ||
                                !jQuery.trim(jQuery("#<?php echo self::$opt_gallery_customnext; ?>").val()))
                        {
                            alertmessage += "Please enter valid text for both the custom gallery Prev and Next buttons, or uncheck the option.";
                            jQuery("#boxcustomarrows input").css("background-color", "#ffcccc").css("border", "2px solid #000000");
                            valid = false;
                            $tabFocus = $("#<?php echo self::$opt_gallery_customarrows; ?>").closest('section');
                        }
                    }


                    if (jQuery("#<?php echo self::$opt_gallery_channelsub; ?>").is(":checked"))
                    {
                        if (!jQuery.trim(jQuery("#<?php echo self::$opt_gallery_channelsublink; ?>").val()) ||
                                !jQuery.trim(jQuery("#<?php echo self::$opt_gallery_channelsubtext; ?>").val()))
                        {
                            alertmessage += "Please enter valid text for both the subscribe text and subscribe URL, or uncheck the option.";
                            jQuery("#boxchannelsub input").css("background-color", "#ffcccc").css("border", "2px solid #000000");
                            valid = false;
                            $tabFocus = $("#<?php echo self::$opt_gallery_channelsub; ?>").closest('section');
                        }
                    }


                    if (jQuery("#<?php echo self::$opt_gallery_collapse_grid; ?>").is(":checked"))
                    {
                        var emptyStacks = [];
                        jQuery("#box_collapse_grid input").each(function ()
                        {
                            var val = jQuery(this).val();
                            if (jQuery.trim(val) === '' || !jQuery.isNumeric(val))
                            {
                                emptyStacks.push(this);
                                jQuery(this).css("background-color", "#ffcccc").css("outline", "2px solid #000000");
                            }
                        });
                        if (emptyStacks.length)
                        {
                            alertmessage += "Please enter a valid number for the gallery stacking screen width.";
                            valid = false;
                            $tabFocus = $("#<?php echo self::$opt_gallery_collapse_grid; ?>").closest('section');
                        }
                    }



                    if (jQuery("#<?php echo self::$opt_defaultvol; ?>").is(":checked"))
                    {
                        if (!(jQuery.isNumeric(jQuery.trim(jQuery("#<?php echo self::$opt_vol; ?>").val()))))
                        {
                            alertmessage += "Please enter a number between 0 and 100 for the default volume, or uncheck the option.";
                            jQuery("#boxdefaultvol input").css("background-color", "#ffcccc").css("border", "2px solid #000000");
                            valid = false;
                            $tabFocus = $("#<?php echo self::$opt_defaultvol; ?>").closest('section');
                        }
                    }

                    if (jQuery("#<?php echo self::$opt_spdc; ?>").is(":checked"))
                    {
                        if (!(jQuery.isNumeric(jQuery.trim(jQuery("#<?php echo self::$opt_spdcexp; ?>").val()))))
                        {
                            alertmessage += "Please enter a valid number of hours (greater than 0) for the cache lifetime, or uncheck the option.";
                            jQuery("#boxspdc input[type=number], #boxspdc input[type=text]").css("background-color", "#ffcccc").css("border", "2px solid #000000");
                            valid = false;
                            $tabFocus = $("#<?php echo self::$opt_spdc; ?>").closest('section');
                        }
                    }



                    if (jQuery("#<?php echo self::$opt_schemaorg; ?>").is(":checked"))
                    {
                        if (!(jQuery.trim(jQuery("#<?php echo self::$opt_apikey; ?>").val()).length > 0))
                        {
                            alertmessage += "Please enter a valid YouTube API key at the top of this screen, or uncheck the 'Video SEO Tags' option.";
                            jQuery("#<?php echo self::$opt_apikey; ?>").css("background-color", "#ffcccc").css("border", "2px solid #000000");
                            valid = false;
                            $tabFocus = $("#<?php echo self::$opt_schemaorg; ?>").closest('section');
                        }
                    }



                    if (jQuery("#<?php echo self::$opt_dynload; ?>").is(":checked"))
                    {
                        if (!(/^[A-Za-z-]+$/.test(jQuery.trim(jQuery("#<?php echo self::$opt_dyntype; ?>").val()))))
                        {
                            alertmessage += "Please select an animation, or uncheck the option.";
                            jQuery("#boxdyn select").css("background-color", "#ffcccc").css("border", "2px solid #000000");
                            valid = false;
                            $tabFocus = $("#<?php echo self::$opt_dynload; ?>").closest('section');
                        }
                    }


                    if (!valid)
                    {
                        alertify.alert(alertmessage);
                        var tabSelector = '.wrap-ytprefs .nav-tab-wrapper .nav-tab[href=#' + $tabFocus.attr('id') + ']';
                        $(tabSelector).trigger('click');
                    }
                    if (!$formDefaults.find('#ajax_save').is(':checked'))
                    {
                        return valid;
                    }
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////// MAIN PREFS AJAX SAVE                    
                    if (valid)
                    {
                        e.preventDefault();
                        (window.tinyMCE || window.tinymce).triggerSave();
                        $formDefaults.find('.ytprefs-submit').prop('disabled', true);

                        var formData = $formDefaults.ytprefsFormJSON();
                        formData.action = 'my_embedplus_settings_save_ajax';

                        $.ajax({
                            type: "post",
                            dataType: "json",
                            timeout: 30000,
                            url: window._EPYTA_ ? window._EPYTA_.wpajaxurl : ajaxurl,
                            data: formData,
                            success: function (response)
                            {
                                alertify.alert(response ? response.message : 'Sorry, there was an error submitting your settings.', function ()
                                {
                                    if (response.type == 'success')
                                    {
                                        window.top.location.href = window._EPYTA_.admin_url_ytprefs;
                                    }
                                });
                            },
                            error: function (xhr, ajaxOptions, thrownError)
                            {
                                alertify.alert('Sorry, there was an error saving your settings. ' + (thrownError ? thrownError : ''));
                            },
                            complete: function ()
                            {
                                $formDefaults.find('.ytprefs-submit').prop('disabled', false);
                            }
                        });
                    }
                };

                var prokeyval;
                var mydomain = escape("http://" + window.location.host.toString());
                jQuery(document).ready(function ($)
                {
                    $(document).on('click', '.wrap-ytprefs .nav-tab-wrapper a, .epyt-jumptab', function ()
                    {
                        $a = $(this);
                        if (!$a.hasClass('href-link'))
                        {
                            $('.wrap-ytprefs .nav-tab-wrapper a').removeClass('nav-tab-active');
                            $a.addClass('nav-tab-active');
                            $('.wrap-ytprefs section').hide();
                            $('.wrap-ytprefs section').filter($a.attr('rel') ? $a.attr('rel') : $a.attr('href')).fadeIn(200);
                            return false;
                        }

                    });

                    if (window.location.hash && window.location.hash == '#jumpmonetize')
                    {
                        setTimeout(function ()
                        {
                            window.scrollTo(0, 0);
                        }, 1);
                        $('.wrap-ytprefs .nav-tab-wrapper a[href="' + window.location.hash + '"]').trigger('click');
                    }

                    $('#ytform').on('submit', function (e)
                    {
                        return window.savevalidate(e);
                    });

                    jQuery('#<?php echo self::$opt_defaultdims; ?>').on('change', function ()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#boxdefaultdims").show(500);
                        }
                        else
                        {
                            jQuery("#boxdefaultdims").hide(500);
                        }

                    });
                    jQuery('#<?php echo self::$opt_gallery_customarrows; ?>').on('change', function ()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#boxcustomarrows").show(500);
                        }
                        else
                        {
                            jQuery("#boxcustomarrows").hide(500);
                        }

                    });
                    jQuery('#<?php echo self::$opt_gallery_collapse_grid; ?>').on('change', function ()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#box_collapse_grid").show(500);
                        }
                        else
                        {
                            jQuery("#box_collapse_grid").hide(500);
                        }
                    });
                    jQuery('#<?php echo self::$opt_restrict_wizard; ?>').on('change', function ()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#box_restrict_wizard").show(500);
                        }
                        else
                        {
                            jQuery("#box_restrict_wizard").hide(500);
                        }
                    });
                    jQuery('#<?php echo self::$opt_gallery_channelsub; ?>').on('change', function ()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#boxchannelsub").show(500);
                        }
                        else
                        {
                            jQuery("#boxchannelsub").hide(500);
                        }

                    });
                    jQuery('#<?php echo self::$opt_dynload; ?>').on('change', function ()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#boxdyn").show(500);
                        }
                        else
                        {
                            jQuery("#boxdyn").hide(500);
                        }

                    });
                    jQuery('#<?php echo self::$opt_spdc; ?>').on('change', function ()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#boxspdc").show(500);
                        }
                        else
                        {
                            jQuery("#boxspdc").hide(500);
                        }
                    });
                    jQuery('#<?php echo self::$opt_responsive; ?>').on('change', function ()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#boxresponsive_all").show(500);
                        }
                        else
                        {
                            jQuery("#boxresponsive_all").hide(500);
                        }
                    });
                    jQuery('#<?php echo self::$opt_migrate; ?>').on('change', function ()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#boxmigratelist").show(500);
                        }
                        else
                        {
                            jQuery("#boxmigratelist").hide(500);
                        }
                    });
                    jQuery('#<?php echo self::$opt_nocookie; ?>').on('change', function ()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#boxnocookie").show(500);
                        }
                        else
                        {
                            jQuery("#boxnocookie").hide(500);
                        }

                    });
                    jQuery('#<?php echo self::$opt_gdpr_consent; ?>').on('change', function ()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#box_gdpr_consent").show(500);
                        }
                        else
                        {
                            jQuery("#box_gdpr_consent").hide(500);
                        }

                    });
                    jQuery('.vi-not-interested').on('click', function (e)
                    {
                        //e.preventDefault();
                        jQuery('a.nav-tab[href="#jumpdefaults"]').trigger('click');
                        setTimeout(function ()
                        {
                            var scrollNext = jQuery('#vi_hide_monetize_tab').offset().top - 20;
                            $('html, body').animate({
                                scrollTop: scrollNext
                            }, 500, function ()
                            {
                            });
                        }, 500);
                    });

                    jQuery('#<?php echo self::$opt_schemaorg; ?>').on('change', function ()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#boxschemaorg").show(500);
                        }
                        else
                        {
                            jQuery("#boxschemaorg").hide(500);
                        }
                    });
                    jQuery('#<?php echo self::$opt_defaultvol; ?>').on('change', function ()
                    {
                        if (jQuery(this).is(":checked"))
                        {
                            jQuery("#boxdefaultvol").show(500);
                        }
                        else
                        {
                            jQuery("#boxdefaultvol").hide(500);
                        }

                    });
                    var rangedetect = document.createElement("input");
                    rangedetect.setAttribute("type", "range");
                    var canrange = rangedetect.type !== "text";
                    //canrange = false;
                    if (canrange)
                    {
                        $("input#vol").prop("type", "range").addClass("vol-range").on("input change", function ()
                        {
                            $('.vol-output').text($(this).val() > 0 ? $(this).val() + '%' : 'Mute');
                        });
                        $('.vol-output').css("display", "inline-block").text($("input#vol").val() > 0 ? $("input#vol").val() + '%' : 'Mute');
                        $('.vol-seeslider').show();
                        $('.vol-seetextbox').hide();
                    }
                    else
                    {
                        $("input#vol").width(40);
                    }


                    jQuery('#boxspdc input.button, .clear-live-cache input.button').on('click', function ()
                    {
                        jQuery('.clearspdcloading').show();
                        jQuery('.clearspdcfailed').hide();
                        jQuery('.clearspdcsuccess').hide();
                        $clearbutton = jQuery(this);
                        $clearbutton.prop('disabled', true);
                        jQuery.ajax({
                            type: "post",
                            dataType: "json",
                            timeout: 30000,
                            url: window._EPYTA_ ? window._EPYTA_.wpajaxurl : ajaxurl,
                            data: {action: 'my_embedplus_clearspdc'},
                            success: function (response)
                            {
                                if (response.type == "success")
                                {
                                    jQuery(".clearspdcsuccess").show();
                                }
                                else
                                {
                                    jQuery(".clearspdcfailed").show();
                                }
                            },
                            error: function (xhr, ajaxOptions, thrownError)
                            {
                                jQuery(".clearspdcfailed").show();
                            },
                            complete: function ()
                            {
                                jQuery('.clearspdcloading').hide();
                                $clearbutton.prop('disabled', false);
                            }

                        });
                    });
                    jQuery("#showcase-validate").on('click', function ()
                    {
                        window.open("<?php echo self::$epbase . "/showcase-validate.aspx?prokey=" . esc_attr(self::$alloptions[self::$opt_pro]) ?>" + "&domain=" + mydomain);
                    });
                    jQuery('#showprokey').on('click', function ()
                    {
                        jQuery('.submitpro').show(500);
                        return false;
                    });
                    jQuery('#prokeysubmit').on('click', function ()
                    {
                        jQuery(this).prop('disabled', true);
                        jQuery('#prokeyfailed').hide();
                        jQuery('#prokeysuccess').hide();
                        jQuery('#prokeyloading').show();
                        prokeyval = jQuery('#opt_pro').val();
                        var tempscript = document.createElement("script");
                        tempscript.src = "<?php echo self::$epbase ?>/dashboard/wordpress-pro-validatejp.aspx?simple=1&prokey=" + prokeyval + "&domain=" + mydomain;
                        var n = document.getElementsByTagName("head")[0].appendChild(tempscript);
                        setTimeout(function ()
                        {
                            n.parentNode.removeChild(n);
                        }, 500);
                        return false;
                    });
                    window.embedplus_record_prokey = function (good)
                    {

                        jQuery.ajax({
                            type: "post",
                            dataType: "json",
                            timeout: 30000,
                            url: window._EPYTA_ ? window._EPYTA_.wpajaxurl : ajaxurl,
                            data: {action: 'my_embedplus_pro_record', <?php echo self::$opt_pro; ?>: (good ? prokeyval : "")},
                            success: function (response)
                            {
                                if (response.type == "success")
                                {
                                    jQuery("#prokeysuccess").show();
                                }
                                else
                                {
                                    jQuery("#prokeyfailed").show();
                                }
                            },
                            error: function (xhr, ajaxOptions, thrownError)
                            {
                                jQuery('#prokeyfailed').show();
                            },
                            complete: function ()
                            {
                                jQuery('#prokeyloading').hide();
                                jQuery('#prokeysubmit').prop('disabled', false);
                            }

                        });
                    };
                    window.embedplus_cancel_prokey = function ()
                    {

                        jQuery.ajax({
                            type: "post",
                            dataType: "json",
                            timeout: 30000,
                            url: window._EPYTA_ ? window._EPYTA_.wpajaxurl : ajaxurl,
                            data: {action: 'my_embedplus_pro_record', <?php echo self::$opt_pro; ?>: ""},
                            success: function (response)
                            {
                                jQuery("#prokeycancel").show();
                            },
                            error: function (xhr, ajaxOptions, thrownError)
                            {
                                jQuery('#prokeyfailed').show();
                            },
                            complete: function ()
                            {
                                jQuery('#prokeyloading').hide();
                                jQuery('#prokeysubmit').prop('disabled', false);
                            }

                        });
                    };

                    $('.ytprefs-pro-support input[type="checkbox"]').on('change', function ()
                    {
                        if ($(this).is(':checked'))
                        {
                            $('.ytprefs-pro-support button').prop('disabled', false);
                        }
                        else
                        {
                            $('.ytprefs-pro-support button').prop('disabled', true);
                        }
                    });
                    $('#defer_js').on('change', function ()
                    {
                        if (!$(this).is(':checked'))
                        {
                            $('#defer_jquery').prop('checked', false).prop('disabled', true);
                        }
                        else
                        {
                            $('#defer_jquery').prop('disabled', false);
                        }
                    });
                    $('.ytprefs-pro-support button').on('click', function ()
                    {
                        $ifSupport = $('.ytprefs-pro-support iframe');
                        $ifSupport.attr('src', $ifSupport.data('src'));
                    });

                });
            })(jQuery);
        </script>

        <a href="<?php echo esc_attr(admin_url('admin.php?page=youtube-ep-onboarding') . '&random=' . rand(1, 1000) . '&TB_iframe=true&width=950&height=800'); ?>" class="thickbox ytprefs-onboarding-launch" id="ytprefs-onboarding-launch" title="YouTube Setup Guide"></a>

        <?php
        if (function_exists('add_thickbox'))
        {
            add_thickbox();
        }
    }

    public static function settings_save($all)
    {

        $new_options = array();
        $new_options[self::$opt_center] = self::postchecked(self::$opt_center) ? 1 : 0;
        $new_options[self::$opt_glance] = self::postchecked(self::$opt_glance) ? 1 : 0;
        $new_options[self::$opt_autoplay] = self::postchecked(self::$opt_autoplay) ? 1 : 0;
        $new_options[self::$opt_debugmode] = self::postchecked(self::$opt_debugmode) ? 1 : 0;
        $new_options[self::$opt_uninstall_data] = self::postchecked(self::$opt_uninstall_data) ? 1 : 0;
        $new_options[self::$opt_admin_off_scripts] = self::postchecked(self::$opt_admin_off_scripts) ? 1 : 0;
        $new_options[self::$opt_defer_js] = self::postchecked(self::$opt_defer_js) ? 1 : 0;
        $new_options[self::$opt_defer_jquery] = self::postchecked(self::$opt_defer_jquery) ? 1 : 0;
        $new_options[self::$opt_ajax_save] = self::postchecked(self::$opt_ajax_save) ? 1 : 0;
        $new_options[self::$opt_show_pointer] = self::postchecked(self::$opt_show_pointer) ? 1 : 0;
        $new_options[self::$opt_old_script_method] = self::postchecked(self::$opt_old_script_method) ? 1 : 0;
        $new_options[self::$opt_cc_load_policy] = self::postchecked(self::$opt_cc_load_policy) ? 1 : 0;
        $new_options[self::$opt_iv_load_policy] = self::postchecked(self::$opt_iv_load_policy) ? 1 : 3;
        $new_options[self::$opt_loop] = self::postchecked(self::$opt_loop) ? 1 : 0;
        $new_options[self::$opt_modestbranding] = self::postchecked(self::$opt_modestbranding) ? 1 : 0;
        $new_options[self::$opt_fs] = self::postchecked(self::$opt_fs) ? 1 : 0;
        $new_options[self::$opt_playsinline] = self::postchecked(self::$opt_playsinline) ? 1 : 0;
        $new_options[self::$opt_origin] = self::postchecked(self::$opt_origin) ? 1 : 0;
        $new_options[self::$opt_controls] = self::postchecked(self::$opt_controls) ? 1 : 0;
        $new_options[self::$opt_color] = self::postchecked(self::$opt_color) ? 'red' : 'white';
        $new_options[self::$opt_nocookie] = self::postchecked(self::$opt_nocookie) ? 1 : 0;
        $new_options[self::$opt_gb_compat] = self::postchecked(self::$opt_gb_compat) ? 1 : 0;
        $new_options[self::$opt_facade_mode] = self::postchecked(self::$opt_facade_mode) ? 1 : 0;
        $new_options[self::$opt_facade_autoplay] = self::postchecked(self::$opt_facade_autoplay) ? 1 : 0;
        $new_options[self::$opt_gdpr_consent] = self::postchecked(self::$opt_gdpr_consent) ? 1 : 0;
        $new_options[self::$opt_playlistorder] = self::postchecked(self::$opt_playlistorder) ? 1 : 0;
        $new_options[self::$opt_acctitle] = self::postchecked(self::$opt_acctitle) ? 1 : 0;
        $new_options[self::$opt_ogvideo] = self::postchecked(self::$opt_ogvideo) ? 1 : 0;
        $new_options[self::$opt_migrate] = self::postchecked(self::$opt_migrate) ? 1 : 0;
        $new_options[self::$opt_migrate_youtube] = self::postchecked(self::$opt_migrate_youtube) ? 1 : 0;
        $new_options[self::$opt_migrate_embedplusvideo] = self::postchecked(self::$opt_migrate_embedplusvideo) ? 1 : 0;
        $new_options[self::$opt_oldspacing] = self::postchecked(self::$opt_oldspacing) ? 1 : 0;
        $new_options[self::$opt_frontend_only] = self::postchecked(self::$opt_frontend_only) ? 1 : 0;
        $new_options[self::$opt_responsive] = self::postchecked(self::$opt_responsive) ? 1 : 0;
        $new_options[self::$opt_widgetfit] = self::postchecked(self::$opt_widgetfit) ? 1 : 0;
        $new_options[self::$opt_evselector_light] = self::postchecked(self::$opt_evselector_light) ? 1 : 0;
        $new_options[self::$opt_stop_mobile_buffer] = self::postchecked(self::$opt_stop_mobile_buffer) ? 1 : 0;
        $new_options[self::$opt_restrict_wizard] = self::postchecked(self::$opt_restrict_wizard) ? 1 : 0;
        $new_options[self::$opt_ajax_compat] = self::postchecked(self::$opt_ajax_compat) ? 1 : 0;
        $new_options[self::$opt_schemaorg] = self::postchecked(self::$opt_schemaorg) ? 1 : 0;
        $new_options[self::$opt_ftpostimg] = self::postchecked(self::$opt_ftpostimg) ? 1 : 0;
        $new_options[self::$opt_spdc] = self::postchecked(self::$opt_spdc) ? 1 : 0;
        $new_options[self::$opt_spdcab] = self::postchecked(self::$opt_spdcab) ? 1 : 0;
        $new_options[self::$opt_dynload] = self::postchecked(self::$opt_dynload) ? 1 : 0;
        $new_options[self::$opt_defaultdims] = self::postchecked(self::$opt_defaultdims) ? 1 : 0;
        $new_options[self::$opt_pause_others] = self::postchecked(self::$opt_pause_others) ? 1 : 0;
        $new_options[self::$opt_defaultvol] = self::postchecked(self::$opt_defaultvol) ? 1 : 0;
        $new_options[self::$opt_dohl] = self::postchecked(self::$opt_dohl) ? 1 : 0;
        $new_options[self::$opt_onboarded] = self::postchecked(self::$opt_onboarded) ? 1 : 0;
        $new_options[self::$opt_not_live_on] = self::postchecked(self::$opt_not_live_on) ? 1 : 0;
        $new_options[self::$opt_not_live_on_channel] = self::postchecked(self::$opt_not_live_on_channel) ? 1 : 0;
        $new_options[self::$opt_live_chat] = self::postchecked(self::$opt_live_chat) ? 1 : 0;
        $new_options[self::$opt_gallery_hideprivate] = self::postchecked(self::$opt_gallery_hideprivate) ? 1 : 0;
        $new_options[self::$opt_gallery_showtitle] = self::postchecked(self::$opt_gallery_showtitle) ? 1 : 0;
        $new_options[self::$opt_gallery_showpaging] = self::postchecked(self::$opt_gallery_showpaging) ? 1 : 0;
        $new_options[self::$opt_gallery_autonext] = self::postchecked(self::$opt_gallery_autonext) ? 1 : 0;
        $new_options[self::$opt_gallery_hidethumbimg] = self::postchecked(self::$opt_gallery_hidethumbimg) ? 1 : 0;
        $new_options[self::$opt_gallery_thumbplay] = self::postchecked(self::$opt_gallery_thumbplay) ? 1 : 0;
        $new_options[self::$opt_gallery_channelsub] = self::postchecked(self::$opt_gallery_channelsub) ? 1 : 0;
        $new_options[self::$opt_gallery_customarrows] = self::postchecked(self::$opt_gallery_customarrows) ? 1 : 0;
        $new_options[self::$opt_gallery_showdsc] = self::postchecked(self::$opt_gallery_showdsc) ? 1 : 0;
        $new_options[self::$opt_gallery_limit_dsc] = self::postchecked(self::$opt_gallery_limit_dsc) ? 1 : 0;
        $new_options[self::$opt_gallery_collapse_grid] = self::postchecked(self::$opt_gallery_collapse_grid) ? 1 : 0;
        $new_options[self::$opt_vi_hide_monetize_tab] = self::postchecked(self::$opt_vi_hide_monetize_tab) ? 1 : 0;

        $new_options[self::$opt_cc_lang_pref] = sanitize_title($_POST[self::$opt_cc_lang_pref]);

        $_not_live_showtime = 180;
        try
        {
            $_not_live_showtime = is_numeric(trim($_POST[self::$opt_not_live_showtime])) ? intval(trim($_POST[self::$opt_not_live_showtime])) : $_not_live_showtime;
        }
        catch (Exception $ex)
        {            
        }
        $new_options[self::$opt_not_live_showtime] = $_not_live_showtime;

        $_rel = 0;
        try
        {
            $_rel = is_numeric(trim($_POST[self::$opt_rel])) ? intval(trim($_POST[self::$opt_rel])) : $_rel;
        }
        catch (Exception $ex)
        {
            
        }
        $new_options[self::$opt_rel] = $_rel;

        $_gdpr_consent_message = '';
        try
        {
            $_gdpr_consent_message = wp_kses_post(stripslashes($_POST[self::$opt_gdpr_consent_message]));
        }
        catch (Exception $ex)
        {
            $_gdpr_consent_message = '';
        }
        $new_options[self::$opt_gdpr_consent_message] = $_gdpr_consent_message;

        $_gdpr_consent_button = '';
        try
        {
            $_gdpr_consent_button = wp_kses_post(stripslashes($_POST[self::$opt_gdpr_consent_button]));
        }
        catch (Exception $ex)
        {
            $_gdpr_consent_button = '';
        }
        $new_options[self::$opt_gdpr_consent_button] = $_gdpr_consent_button;


        $_ytapi_load = 'light';
        try
        {
            $_ytapi_load_temp = $_POST[self::$opt_ytapi_load];
            if (in_array($_ytapi_load_temp, array('always', 'light', 'never')))
            {
                $_ytapi_load = $_ytapi_load_temp;
            }
        }
        catch (Exception $ex)
        {
            
        }
        $new_options[self::$opt_ytapi_load] = $_ytapi_load;

        $_maxres_facade = 'eager';
        try
        {
            $_maxres_facade_temp = filter_input(INPUT_POST, self::$opt_maxres_facade);
            if (in_array($_maxres_facade_temp, array('eager', 'soft', 'off')))
            {
                $_maxres_facade = $_maxres_facade_temp;
            }
        }
        catch (Exception $ex)
        {
            
        }
        $new_options[self::$opt_maxres_facade] = $_maxres_facade;

        $_restrict_wizard_roles = self::$dft_roles;
        try
        {
            $_restrict_wizard_roles = is_array($_POST[self::$opt_restrict_wizard_roles]) ? $_POST[self::$opt_restrict_wizard_roles] : $_restrict_wizard_roles;
        }
        catch (Exception $ex)
        {
            
        }
        $new_options[self::$opt_restrict_wizard_roles] = $_restrict_wizard_roles;



        $_defaultwidth = '';
        try
        {
            $_defaultwidth = is_numeric(trim($_POST[self::$opt_defaultwidth])) ? intval(trim($_POST[self::$opt_defaultwidth])) : $_defaultwidth;
        }
        catch (Exception $ex)
        {
            
        }
        $new_options[self::$opt_defaultwidth] = $_defaultwidth;

        $_defaultheight = '';
        try
        {
            $_defaultheight = is_numeric(trim($_POST[self::$opt_defaultheight])) ? intval(trim($_POST[self::$opt_defaultheight])) : $_defaultheight;
        }
        catch (Exception $ex)
        {
            
        }
        $new_options[self::$opt_defaultheight] = $_defaultheight;

        $_responsive_all = 1;
        try
        {
            $_responsive_all = is_numeric(trim($_POST[self::$opt_responsive_all])) ? intval(trim($_POST[self::$opt_responsive_all])) : $_responsive_all;
        }
        catch (Exception $ex)
        {
            
        }
        $new_options[self::$opt_responsive_all] = $_responsive_all;

        $_vol = '';
        try
        {
            $_vol = is_numeric(trim($_POST[self::$opt_vol])) ? intval(trim($_POST[self::$opt_vol])) : $_vol;
        }
        catch (Exception $ex)
        {
            
        }
        $new_options[self::$opt_vol] = $_vol;

        $_gallery_pagesize = 15;
        try
        {
            $_gallery_pagesize = is_numeric(trim($_POST[self::$opt_gallery_pagesize])) ? intval(trim($_POST[self::$opt_gallery_pagesize])) : $_gallery_pagesize;
        }
        catch (Exception $ex)
        {
            
        }
        $new_options[self::$opt_gallery_pagesize] = $_gallery_pagesize;


        $_gallery_columns = 3;
        try
        {
            $_gallery_columns = is_numeric(trim($_POST[self::$opt_gallery_columns])) ? intval(trim($_POST[self::$opt_gallery_columns])) : $_gallery_columns;
        }
        catch (Exception $ex)
        {
            
        }
        $new_options[self::$opt_gallery_columns] = $_gallery_columns;

        $_gallery_length_dsc = 100;
        try
        {
            $_gallery_length_dsc = is_numeric(trim($_POST[self::$opt_gallery_length_dsc])) ? intval(trim($_POST[self::$opt_gallery_length_dsc])) : $_gallery_length_dsc;
        }
        catch (Exception $ex)
        {
            
        }
        $new_options[self::$opt_gallery_length_dsc] = $_gallery_length_dsc;

        $_gallery_collapse_grid_breaks = self::$dft_bpts;
        try
        {
            $_gallery_collapse_grid_breaks = is_array($_POST[self::$opt_gallery_collapse_grid_breaks]) ? $_POST[self::$opt_gallery_collapse_grid_breaks] : $_gallery_collapse_grid_breaks;
        }
        catch (Exception $ex)
        {
            
        }
        $new_options[self::$opt_gallery_collapse_grid_breaks] = $_gallery_collapse_grid_breaks;



        $_gallery_scrolloffset = 20;
        try
        {
            $_gallery_scrolloffset = is_numeric(trim($_POST[self::$opt_gallery_scrolloffset])) ? intval(trim($_POST[self::$opt_gallery_scrolloffset])) : $_gallery_scrolloffset;
        }
        catch (Exception $ex)
        {
            
        }
        $new_options[self::$opt_gallery_scrolloffset] = $_gallery_scrolloffset;

        $_gallery_style = 'grid';
        try
        {
            if (isset($_POST[self::$opt_gallery_style]))
            {
                $_gallery_style = trim(str_replace(array(' ', "'", '"'), array('', '', ''), strip_tags($_POST[self::$opt_gallery_style])));
            }
        }
        catch (Exception $ex)
        {
            $_gallery_style = 'grid';
        }
        $new_options[self::$opt_gallery_style] = $_gallery_style;


        $_gallery_thumbcrop = 'box';
        try
        {
            if (isset($_POST[self::$opt_gallery_thumbcrop]))
            {
                $_gallery_thumbcrop = trim(str_replace(array(' ', "'", '"'), array('', '', ''), strip_tags($_POST[self::$opt_gallery_thumbcrop])));
            }
        }
        catch (Exception $ex)
        {
            $_gallery_thumbcrop = 'box';
        }
        $new_options[self::$opt_gallery_thumbcrop] = $_gallery_thumbcrop;


        $_gallery_disptype = 'default';
        try
        {
            if (isset($_POST[self::$opt_gallery_disptype]))
            {
                $_gallery_disptype = trim(str_replace(array(' ', "'", '"'), array('', '', ''), strip_tags($_POST[self::$opt_gallery_disptype])));
            }
        }
        catch (Exception $ex)
        {
            $_gallery_disptype = 'default';
        }
        $new_options[self::$opt_gallery_disptype] = $_gallery_disptype;


        $_gallery_channelsublink = '';
        try
        {
            $_gallery_channelsublink = trim(strip_tags($_POST[self::$opt_gallery_channelsublink]));
            $pieces = explode('?', $_gallery_channelsublink);
            $_gallery_channelsublink = trim($pieces[0]);
        }
        catch (Exception $ex)
        {
            $_gallery_channelsublink = '';
        }
        $new_options[self::$opt_gallery_channelsublink] = $_gallery_channelsublink;


        $_gallery_channelsubtext = '';
        try
        {
            $_gallery_channelsubtext = stripslashes(trim($_POST[self::$opt_gallery_channelsubtext]));
        }
        catch (Exception $ex)
        {
            $_gallery_channelsubtext = '';
        }
        $new_options[self::$opt_gallery_channelsubtext] = $_gallery_channelsubtext;


        $_gallery_custom_prev = 'Prev';
        try
        {
            $_gallery_custom_prev = trim(strip_tags($_POST[self::$opt_gallery_customprev]));
        }
        catch (Exception $ex)
        {
            $_gallery_custom_prev = 'Prev';
        }
        $new_options[self::$opt_gallery_customprev] = $_gallery_custom_prev;


        $_gallery_custom_next = 'Next';
        try
        {
            $_gallery_custom_next = trim(strip_tags($_POST[self::$opt_gallery_customnext]));
        }
        catch (Exception $ex)
        {
            $_gallery_custom_next = 'Next';
        }
        $new_options[self::$opt_gallery_customnext] = $_gallery_custom_next;

        $_not_live_content = '';
        try
        {
            $_not_live_content = wp_kses_post(stripslashes($_POST[self::$opt_not_live_content]));
        }
        catch (Exception $ex)
        {
            $_not_live_content = '';
        }
        $new_options[self::$opt_not_live_content] = $_not_live_content;



        $_apikey = '';
        try
        {
            $_apikey = trim(str_replace(array(' ', "'", '"'), array('', '', ''), strip_tags($_POST[self::$opt_apikey])));
        }
        catch (Exception $ex)
        {
            $_apikey = '';
        }
        $new_options[self::$opt_apikey] = $_apikey;


        $_dyntype = '';
        try
        {
            if (isset($_POST[self::$opt_dyntype]))
            {
                $tempdyntype = trim($_POST[self::$opt_dyntype]);
                $_dyntype = preg_match('/^[a-zA-Z-]+$/i', $tempdyntype) ? $tempdyntype : '';
            }
        }
        catch (Exception $ex)
        {
            
        }
        $new_options[self::$opt_dyntype] = $_dyntype;

        $_spdcexp = 24;
        try
        {
            $_spdcexp = isset($_POST[self::$opt_spdcexp]) && is_numeric(trim($_POST[self::$opt_spdcexp])) ? intval(trim($_POST[self::$opt_spdcexp])) : $_spdcexp;
        }
        catch (Exception $ex)
        {
            
        }
        $new_options[self::$opt_spdcexp] = $_spdcexp;


        $all = $new_options + $all;

        update_option(self::$opt_alloptions, $all);

        try
        {
            self::spdcpurge();
            if ($all[self::$opt_spdc] == 1)
            {
                wp_remote_get(site_url(), array('timeout' => self::$curltimeout));
            }
        }
        catch (Exception $ex)
        {
            
        }
        return array(
            'type' => 'success',
            'message' => 'Changes were saved. <em>If you are using a separate caching plugin and you do not see your changes after saving, <strong class="orange">you need to reset your cache.</strong></em>'
        );
    }

    public static function settings_save_ajax()
    {
        $result = array();
        if (check_ajax_referer('_epyt_save', '_epyt_nonce', false) && current_user_can('manage_options'))
        {
            $all = get_option(self::$opt_alloptions);
            $result = self::settings_save($all);
        }
        else
        {
            $result['type'] = 'error';
            $result['message'] = 'Sorry, there was a problem saving your settings.';
        }
        echo json_encode($result);
        die();
    }

    public static function onboarding_save_valid(&$input)
    {
        $messages = array();
        try
        {
            $input[self::$opt_modestbranding] = intval($input[self::$opt_modestbranding]);
            $input[self::$opt_responsive] = intval($input[self::$opt_responsive]);
            $input[self::$opt_responsive_all] = intval($input[self::$opt_responsive_all]);
            $input[self::$opt_defer_js] = intval($input[self::$opt_defer_js]);

            $input[self::$opt_gallery_pagesize] = intval($input[self::$opt_gallery_pagesize]);
            $input[self::$opt_gallery_columns] = intval($input[self::$opt_gallery_columns]);
            $input[self::$opt_not_live_content] = wp_kses_post(stripslashes($input[self::$opt_not_live_content]));
            $input[self::$opt_not_live_on] = intval($input[self::$opt_not_live_on]);
            $input[self::$opt_not_live_on_channel] = intval($input[self::$opt_not_live_on_channel]);
            $input[self::$opt_live_chat] = intval($input[self::$opt_live_chat]);

            if (!in_array($input[self::$opt_ytapi_load], array('always', 'light', 'never')))
            {
                $input[self::$opt_ytapi_load] = 'light';
            }
            $input[self::$opt_facade_mode] = intval($input[self::$opt_facade_mode]);
            $input[self::$opt_facade_autoplay] = intval($input[self::$opt_facade_autoplay]);
            $input[self::$opt_gdpr_consent] = intval($input[self::$opt_gdpr_consent]);
            $input[self::$opt_gdpr_consent_message] = wp_kses_post(stripslashes($input[self::$opt_gdpr_consent_message]));
            $input[self::$opt_gdpr_consent_button] = wp_kses_post(stripslashes($input[self::$opt_gdpr_consent_button]));
            $input[self::$opt_nocookie] = intval($input[self::$opt_nocookie]);
        }
        catch (Exception $ex)
        {
            $messages[] = 'Please enter valid data.';
        }

        if (empty($messages))
        {
            return true;
        }
        return $messages;
    }

    public static function onboarding_save()
    {
        $result = array();
        $default = array(
            self::$opt_rel => 1,
            self::$opt_modestbranding => 0,
            self::$opt_responsive => 0,
            self::$opt_responsive_all => 0,
            self::$opt_defer_js => 0,
            self::$opt_gallery_pagesize => 15,
            self::$opt_gallery_columns => 3,
            self::$opt_not_live_content => '',
            self::$opt_not_live_on => 0,
            self::$opt_not_live_on_channel => 0,
            self::$opt_ytapi_load => 'light',
            self::$opt_facade_mode => 0,
            self::$opt_facade_autoplay => 1,
            self::$opt_gdpr_consent => 0,
            self::$opt_gdpr_consent_message => self::$dft_gdpr_consent_message,
            self::$opt_gdpr_consent_button => 'Accept YouTube Content',
            self::$opt_nocookie => 0
        );


        $input = shortcode_atts($default, stripslashes_deep($_POST));
        $valid = self::onboarding_save_valid($input);
        if ($valid === true)
        {
            self::update_option_set($input);
            $result['type'] = 'success';
        }
        else
        {
            $result['type'] = 'error';
            $result['message'] = implode('<br/>', $valid);
        }

        return $result;
    }

    public static function onboarding_save_ajax()
    {
        $result = array();
        if (self::is_ajax() && self::ajax_referer() && current_user_can('manage_options'))
        {
            $result = self::onboarding_save();
        }
        else
        {
            $result['type'] = 'error';
            $result['message'] = 'Sorry, there was a problem saving the data.';
        }
        echo json_encode($result);
        die();
    }

    public static function onboarding_save_apikey_valid(&$input)
    {
        $messages = array();
        try
        {
            $input[self::$opt_apikey] = trim(str_replace(array(' ', "'", '"'), array('', '', ''), strip_tags($input[self::$opt_apikey])));
        }
        catch (Exception $ex)
        {
            $messages[] = 'Please enter a valid API key.';
        }

        if (empty($messages))
        {
            return true;
        }
        return $messages;
    }

    public static function onboarding_save_apikey()
    {
        $result = array();
        $default = array(
            self::$opt_apikey => '',
        );

        $input = shortcode_atts($default, stripslashes_deep($_POST));
        $valid = self::onboarding_save_apikey_valid($input);
        if ($valid === true)
        {
            self::update_option_set($input);
            $result['type'] = 'success';
        }
        else
        {
            $result['type'] = 'error';
            $result['message'] = implode('<br/>', $valid);
        }

        return $result;
    }

    public static function onboarding_save_apikey_ajax()
    {
        $result = array();
        if (self::is_ajax() && self::ajax_referer() && current_user_can('manage_options'))
        {
            $result = self::onboarding_save_apikey();
        }
        else
        {
            $result['type'] = 'error';
            $result['message'] = 'Sorry, there was a problem saving the data.';
        }
        echo json_encode($result);
        die();
    }

    public static function ytprefs_show_onboarding()
    {

        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        if (self::$double_plugin)
        {
            self::double_plugin_warning();
        }
        $all = get_option(self::$opt_alloptions);

        $do_once = array(
            self::$opt_onboarded => 1
        );
        self::update_option_set($do_once);

        $alreadypro = false;
        if ($all[self::$opt_pro] && strlen(trim($all[self::$opt_pro])) > 0)
        {
            $alreadypro = true;
        }
        ?>
        <div class="wrap wrap-ytprefs-onboarding">
            <div class="ytprefs-ob-title">
                YouTube Setup Guide
            </div>
            <div class="relative">
                <div class="ytprefs-ob-step ytprefs-ob-step1 active-step">
                    <div class="ytprefs-ob-content">
                        <div class="ytprefs-ob-block">
                            <p>
                                With so many options available in this plugin, we created this easy setup guide to help you  quickly learn about its <strong>most common settings and newest features</strong>. We hope it will get you embedding videos, galleries, and/or live streams sooner.
                            </p>
                            <p>
                                You'll have an opportunity to see and set many other options after completing this setup guide.
                            </p>
                        </div>
                    </div>
                    <?php
                    if (!$alreadypro)
                    {
                        ?>
                        <div class="ytprefs-ob-content">
                            <div class="ytprefs-ob-block center">
                                <p class="bold">
                                    To get started, first enter your PRO key below.
                                </p>
                                <p>
                                    <input type="text" class="regular-text ob-prokey" placeholder="Enter your PRO key" id="opt_pro" value=""/><button type="button" id="prokeysubmit" class="button-primary">Save Key</button>
                                </p>
                                <p class="smallnote">
                                    This one-time save will verify your purchase with our EmbedPlus.com servers so that you can receive future upgrades.
                                </p>
                                <p>
                                    <span style="display: none;" id="prokeyloading" class="orange bold">Verifying...</span>
                                    <span  class="orange bold" style="display: none;" id="prokeysuccess">Success! Refreshing...</span>
                                    <span class="orange bold" style="display: none;" id="prokeyfailed">Sorry, that seems to be an invalid key, or it has been used already. If you're behind a firewall, you may need to try activating on another network.</span>
                                    <span class="orange bold" style="display: none;" id="prokeycancel">Your request is being processed. Response code: CR1.</span>
                                </p>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="ytprefs-ob-content ytprefs-ob-content1" <?php echo $alreadypro ? '' : 'style="display: none;"'; ?> >
                        <h2>
                            Below, check all that apply:<br><em>I'm interested in embedding...</em>
                        </h2>
                        <div class="ytprefs-hover-icons">
                            <img class="yob-single-icon" src="<?php echo plugins_url('images/icon-player-single.png', __FILE__) ?>"/>
                            <img class="yob-gallery-icon" src="<?php echo plugins_url('images/icon-playlist-gallery.png', __FILE__) ?>"/>
                            <img class="yob-standalone-icon" src="<?php echo plugins_url('images/icon-playlist-self.png', __FILE__) ?>"/>
                            <img class="yob-live-icon" src="<?php echo plugins_url('images/icon-player-live.png', __FILE__) ?>"/>
                            <img class="yob-privacy-icon" src="<?php echo plugins_url('images/icon-player-privacy.png', __FILE__) ?>"/>
        <!--                            <img class="yob-monetize-icon" src="<?php echo plugins_url('images/icon-player-money.png', __FILE__) ?>"/>-->
                        </div>
                        <ul class="ytprefs-ob-filter">
                            <li><label><input type="checkbox" data-obfilter="yob-single" /> Single videos.</label></li>
                            <li><label><input type="checkbox" data-obfilter="yob-gallery" /> Galleries of playlists or channels (displays thumbnails and a player).</label></li>
                            <li><label><input type="checkbox" data-obfilter="yob-standalone" /> Self-contained playlists or channels (no thumbnails, just YouTube's standard playlist player).</label></li>
                            <li><label><input type="checkbox" data-obfilter="yob-live" /> Live streams or premieres.</label></li>
                            <li style="display: none;"><label><input type="checkbox" data-obfilter="yob-privacy" /> With GDPR / privacy features.</label></li>
        <!--                            <li><label><input type="checkbox" data-obfilter="yob-monetize" /> Relevant video ads that earn me up to 10x higher CPMs (revenue) than display advertising.</label></li>-->
                        </ul>
                        <div class="ytprefs-ob-nav">
                            <button type="button" class="button-secondary ytprefs-ob-nav-close">Cancel</button>
                            <button type="button" disabled class="button-primary ytprefs-ob-nav-next">Next &raquo;</button>
                        </div>
                        <h2>
                            Intro Video
                        </h2>
                        <p>
                            Want a quick visual overview? Here's a preview of some of the free features of the plugin. Your Pro version has more features available on the Pro Settings tab.
                        </p>
                        <div class="epyt-fitvid">
                            <iframe src="https://www.youtube.com/embed/QDdvXBqfrzM?rel=0" allowfullscreen="" frameborder="0"></iframe>
                        </div>
                        <p>

                        </p>
                    </div>
                </div>
                <div class="ytprefs-ob-step ytprefs-ob-step2">
                    <div class="ytprefs-ob-content">
                        <h2>
                            You're interested in:
                        </h2>

                        <form id="form-onboarding">
                            <input type="hidden" name="action" value="my_embedplus_onboarding_save_ajax"/>

                            <div class="ytprefs-ob-setting yob-single yob-gallery yob-standalone yob-live">
                                <label>
                                    <b class="chktitle">Related Videos:</b>
                                    Show or hide related and recommended videos at the end of playback.
                                    <br>
                                </label>
                                <input type="radio" name="<?php echo self::$opt_rel; ?>" id="<?php echo self::$opt_rel; ?>-1" value="-1" <?php checked($all[self::$opt_rel], -1); ?>>
                                <label for="<?php echo self::$opt_rel; ?>-1">Hide related videos at the end of playback </label> &nbsp;&nbsp;
                                <input type="radio" name="<?php echo self::$opt_rel; ?>" id="<?php echo self::$opt_rel; ?>0" value="0" <?php checked($all[self::$opt_rel], 0); ?>>
                                <label for="<?php echo self::$opt_rel; ?>0">Show related videos only from the video's channel</label> &nbsp;&nbsp;
                                <input type="radio" name="<?php echo self::$opt_rel; ?>" id="<?php echo self::$opt_rel; ?>1" value="1" <?php checked($all[self::$opt_rel], 1); ?>>
                                <label for="<?php echo self::$opt_rel; ?>1">Show related videos</label> &nbsp;&nbsp;
                            </div>
                            <div class="ytprefs-ob-setting yob-single yob-gallery yob-standalone">
                                <input value="1" name="<?php echo self::$opt_facade_mode; ?>" id="<?php echo self::$opt_facade_mode; ?>" <?php checked($all[self::$opt_facade_mode], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_facade_mode ?>">
                                    <b class="chktitle"><?php _e('Facade Mode:', 'youtube-embed-plus-pro'); ?> <sup class="orange">new</sup></b> 
                                    <?php _e('This improves performance by loading a lighter version of the player, until it is clicked. Then the real player loads (note: for live streams, the real player is always loaded).  We have tested this feature in multiple cases and found it to successfully improve your Lighthouse performance score by addressing  the following recommendation: "Some third-party resources can be lazy loaded with a facade."', 'youtube-embed-plus-pro'); ?>
                                    <a href="https://www.youtube.com/watch?v=W7PKUjVBDNE" target="_blank"><?php _e('See an example of this feature at work.', 'youtube-embed-plus-pro'); ?></a>
                                </label>                       
                                <div class="p box_facade_mode">
                                    <input value="1" name="<?php echo self::$opt_facade_autoplay; ?>" id="<?php echo self::$opt_facade_autoplay; ?>" type="checkbox" class="checkbox" <?php checked($all[self::$opt_facade_autoplay], 1); ?>>
                                    <label for="<?php echo self::$opt_facade_autoplay ?>">
                                        <b class="chktitle"><?php _e('Autoplay On Facade Click:', 'youtube-embed-plus-pro'); ?></b>
                                        <span>
                                            <?php _e('After clicking once on the facade (aka light thumbnail), it is replaced with the real player. Check this option to have the real player play immediately, otherwise it will require an additional click. Note that checking this option will use YouTube\'s autoplay feature, which will not contribute toward play counts.  If you\'re embedding videos from someone else\'s channel, we recommend checking this.  If you\'re embedding videos that are from your channel, then you should self-evaluate the tradeoff involving play counts and additional clicking.', 'youtube-embed-plus-pro'); ?>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="ytprefs-ob-setting yob-single yob-gallery yob-standalone yob-live">
                                <input value="1" name="<?php echo self::$opt_modestbranding; ?>" id="<?php echo self::$opt_modestbranding; ?>" <?php checked($all[self::$opt_modestbranding], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_modestbranding; ?>"><?php _e('<b class="chktitle">Modest Branding:</b> No YouTube logo will be shown on the control bar.  Instead, as required by YouTube, the logo will only show as a watermark when the video is paused/stopped.') ?></label>
                            </div>
                            <div class="ytprefs-ob-setting yob-single yob-gallery yob-standalone yob-live">
                                <input value="1" name="<?php echo self::$opt_responsive; ?>" id="<?php echo self::$opt_responsive; ?>" <?php checked($all[self::$opt_responsive], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_responsive; ?>"><?php _e('<b class="chktitle">Responsive Video Sizing:</b> Make your videos responsive so that they dynamically fit in all screen sizes (smart phone, PC and tablet). NOTE: While this is checked, any custom hardcoded widths and heights you may have set will dynamically change too. <b>Do not check this if your theme already handles responsive video sizing.</b>') ?></label>
                                <p id="boxresponsive_all" class="ytindent">
                                    <input type="radio" name="<?php echo self::$opt_responsive_all; ?>" id="<?php echo self::$opt_responsive_all; ?>1" value="1" <?php checked($all[self::$opt_responsive_all], 1); ?> >
                                    <label for="<?php echo self::$opt_responsive_all; ?>1">Responsive for all YouTube videos</label> &nbsp;&nbsp;
                                    <input type="radio" name="<?php echo self::$opt_responsive_all; ?>" id="<?php echo self::$opt_responsive_all; ?>0" value="0" <?php checked($all[self::$opt_responsive_all], 0); ?> >
                                    <label for="<?php echo self::$opt_responsive_all; ?>0">Responsive for only videos embedded via this plugin</label>
                                </p>
                            </div>
                            <div class="ytprefs-ob-setting yob-gallery">
                                <label for="<?php echo self::$opt_gallery_pagesize; ?>"><b class="chktitle">Default Gallery Page Size:</b></label>
                                <select name="<?php echo self::$opt_gallery_pagesize; ?>" id="<?php echo self::$opt_gallery_pagesize; ?>" style="width: 60px;">
                                    <?php
                                    $gps_val = intval(trim($all[self::$opt_gallery_pagesize]));
                                    $gps_val = min($gps_val, 50);
                                    for ($gps = 1; $gps <= 50; $gps++)
                                    {
                                        ?><option <?php echo $gps_val == $gps ? 'selected' : '' ?> value="<?php echo $gps ?>"><?php echo $gps ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                Enter how many thumbnails per page should be shown at once (YouTube allows a maximum of 50 per page). You can later use the embedding wizard to customize this for specific galleries.
                            </div>

                            <div class="ytprefs-ob-setting yob-gallery">
                                <label for="<?php echo self::$opt_gallery_columns; ?>"><b class="chktitle">Default Gallery Number of Columns:</b></label>
                                <input name="<?php echo self::$opt_gallery_columns; ?>" id="<?php echo self::$opt_gallery_columns; ?>" type="number" class="textinput" style="width: 60px;" value="<?php echo esc_attr(trim($all[self::$opt_gallery_columns])); ?>">                        
                                Enter how many thumbnails can fit per row.  You can later use the embedding wizard to customize this for specific galleries.
                            </div>
                            <div class="ytprefs-ob-setting yob-live">
                                <p>
                                    <b class="chktitle">Use "Not Live" Fallback Content For Live Streams:</b> (<a href="<?php echo self::$epbase ?>/how-to-embed-a-youtube-livestream-in-wordpress.aspx" target="_blank">More info here</a>)
                                    This feature lets you display alternate content if your live stream is not currently active. There are 2 flavors of this feature: one that affects <strong>direct link</strong> live streams, and 
                                    one that affects <strong>channel</strong> live streams. Each are explained below. They work a little differently, but both use the same "Not Live" Fallback Content that you can edit below.
                                    <strong>Note: This feature does not currently work for premieres, but we are hoping the YouTube API will support it in the future.</strong>
                                </p>
                                <div class="ytindent chx">
                                    <input value="1" name="<?php echo self::$opt_not_live_on; ?>" id="<?php echo self::$opt_not_live_on; ?>" <?php checked($all[self::$opt_not_live_on], 1); ?> type="checkbox" class="checkbox">
                                    <label for="<?php echo self::$opt_not_live_on; ?>"><span class="chktitle">Turn on for <b>direct link</b> live streams:</span>
                                        When your direct-link embed is not streaming live, the YouTube live player usually displays a countdown after the user clicks the play button.
                                        Instead of showing that player, you can display some "coming soon" content in that space for your visitors to see until your video begins to live stream. 
                                        The plugin will automatically switch to your video's live stream once it's active. In the <em>"Not Live" Fallback Content</em> box below, enter what you would like to appear until then.
                                        You can even insert shortcodes from our plugin into the box below (shortcodes from other plugins may or may not work correctly).
                                        If you just want to show the standard countdown player that YouTube provides, don't use this feature.
                                        <strong>NOTE: Turning this on for direct-link live streams uses a significant amount of your YouTube API quota. We suggest unchecking it if your site has high traffic. If you chose to use this feature, do not put another live stream embed below.</strong>
                                    </label>
                                    <br>
                                    <br>
                                    <input value="1" name="<?php echo self::$opt_not_live_on_channel; ?>" id="<?php echo self::$opt_not_live_on_channel; ?>" <?php checked($all[self::$opt_not_live_on_channel], 1); ?> type="checkbox" class="checkbox">
                                    <label for="<?php echo self::$opt_not_live_on_channel; ?>"><span class="chktitle">Turn on for <b>channel</b> live streams:</span> <sup class="orange">beta</sup>
                                        If your live stream embed is channel-based, YouTube might show an error message if there is no upcoming or currently streaming video from your channel. 
                                        Instead of showing an error, you can display some "coming soon" content in that space for your visitors to see until you've scheduled a live stream 
                                        (Once you've scheduled something, YouTube will display the usual countdown until the stream happens). 
                                        In the <em>"Not Live" Fallback Content</em> box below, enter what you would like to appear when nothing is playing or scheduled to play yet on your channel.
                                        You can even insert shortcodes from our plugin into the box below (shortcodes from other plugins may or may not work correctly).
                                        <strong>NOTE: This feature for channel live streams is experimental, but it will preserve your API quota. We recommend trying this instead of the direct-link option, to see if it works for your site. We hope to improve this feature over time.</strong>
                                    </label>
                                    <div class="p not-live-content">
                                        <p>                                            
                                            <b>"Not Live" Fallback Content:</b>
                                        </p>
                                        <?php
                                        wp_editor(wp_kses_post($all[self::$opt_not_live_content]), self::$opt_not_live_content, array('textarea_rows' => 7));
                                        ?> 
                                    </div>
                                </div>
                            </div>

                            <div class="ytprefs-ob-setting yob-privacy">
                                <b class="chktitle">YouTube API Loading:</b> Choose when to load the YouTube API. The "Restricted" or "Never" options will help with GDPR compliance:
                                <ul class="indent-option">
                                    <li><label><input type="radio" name="<?php echo self::$opt_ytapi_load ?>" value="light" <?php checked($all[self::$opt_ytapi_load], 'light'); ?> /> <em>Restricted</em> - (Recommended) Only load the API on pages that have a YouTube video.</label></li>
                                    <li><label><input type="radio" name="<?php echo self::$opt_ytapi_load ?>" value="never" <?php checked($all[self::$opt_ytapi_load], 'never'); ?> /> <em>Never</em> - Do not load the YouTube API. Note: The "Never" choice may break a few features such as Volume Initialization and Gallery Continuous/Auto Play.</label></li>
                                    <li><label><input type="radio" name="<?php echo self::$opt_ytapi_load ?>" value="always" <?php checked($all[self::$opt_ytapi_load], 'always'); ?> /> <em>Always</em> - Load the API on all pages. In most cases, the "Always" choice is not necessary.</label></li>
                                </ul>
                            </div>


                            <div class="ytprefs-ob-setting yob-privacy">
                                <input value="1" name="<?php echo self::$opt_gdpr_consent; ?>" id="<?php echo self::$opt_gdpr_consent; ?>" <?php checked($all[self::$opt_gdpr_consent], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_gdpr_consent; ?>">
                                    <b class="chktitle">Privacy/GDPR - Show Consent Message:</b> Ask for consent before loading YouTube content. A message will be displayed in place of the YouTube video, as shown in the screenshot below. Once the visitor approves consent, the YouTube content will load. You can customize the message text and the button text in the next 2 options.
                                </label>
                            </div>


                            <div class="ytprefs-ob-setting yob-privacy">                                
                                <label for="<?php echo self::$opt_gdpr_consent_message; ?>">
                                    <b class="chktitle">Privacy/GDPR - Consent Message Text:</b>
                                    Below you can customize the message that will appear to visitors before they accept YouTube content:
                                </label>
                                <div class="clearboth"></div>
                                <div class="gdpr-options-left">
                                    <?php
                                    wp_editor(wp_kses_post($all[self::$opt_gdpr_consent_message]), self::$opt_gdpr_consent_message, array(
                                        'textarea_rows' => 22,
                                        'media_buttons' => false,
                                        'teeny' => true
                                    ));
                                    ?> 
                                </div>
                                <div class="gdpr-options-right">
                                    <p><em>Example of message and button:</em></p>

                                    <img src="<?php echo plugins_url('images/ss-gdpr-message.png', __FILE__) ?>" alt="GDPR Consent Message Example" class="img-gdpr-message" />
                                </div>

                            </div>

                            <div class="clearboth"></div>
                            <div class="ytprefs-ob-setting yob-privacy">
                                <label for="<?php echo self::$opt_gdpr_consent_button; ?>">
                                    <b class="chktitle">Privacy/GDPR - Consent Button Text:</b>
                                    This is the text for the red "Accept" button that appears with the above privacy/GDPR message:
                                </label>
                                <br>
                                <input type="text" placeholder="Example: Accept YouTube Content" name="<?php echo self::$opt_gdpr_consent_button; ?>" id="<?php echo self::$opt_gdpr_consent_button; ?>" value="<?php echo esc_attr(trim($all[self::$opt_gdpr_consent_button])); ?>" class="textinput regular-text"/>
                            </div>

                            <div class="ytprefs-ob-setting yob-privacy">
                                <input value="1" name="<?php echo self::$opt_nocookie; ?>" id="<?php echo self::$opt_nocookie; ?>" <?php checked($all[self::$opt_nocookie], 1); ?> type="checkbox" class="checkbox">
                                <label for="<?php echo self::$opt_nocookie; ?>">
                                    <b class="chktitle">No Cookies:</b> Prevent YouTube from leaving tracking cookies on your visitors browsers unless they actual play the videos. This is coded to apply this behavior on links in your past post as well.
                                    <span id="boxnocookie">
                                        Checking this option may introduce issues to features that depend YouTube's API, such as galleries and playlists. Furthermore, videos on mobile devices may have problems if you leave this checked. We suggest testing this out to make sure you are pleased with the results.
                                    </span>
                                </label>
                            </div>

                            <div class="ytprefs-ob-nav">
                                <button type="button" class="button-secondary ytprefs-ob-nav-prev">&laquo; Previous</button>
                                <button type="submit" class="button-primary ytprefs-ob-nav-next">Save & Next &raquo;</button>
                            </div>                    
                        </form>
                    </div>
                </div>
                <div class="ytprefs-ob-step ytprefs-ob-step3">
                    <div class="ytprefs-ob-content">
                        <h2>
                            YouTube API Key
                        </h2>
                        <form id="form-onboarding-apikey">
                            <input type="hidden" name="action" value="my_embedplus_onboarding_save_apikey_ajax"/>
                            <p>
                                Some features (such as galleries, and some wizard features) now require you to create a free YouTube API key from Google.
                            </p>
                            <?php
                            if (!empty($all[self::$opt_apikey]) && strlen($all[self::$opt_apikey]) > 0)
                            {
                                ?>
                                <p class="ytprefs-ob-success">
                                    Great! You already have an API key.
                                </p>
                                <?php
                            }
                            else
                            {
                                ?>
                                <p>
                                    The instructions for this are very specific, so we created a video for you that's hopefully easy to follow: <a href="https://www.embedplus.com/how-to-create-a-youtube-api-key.aspx" target="_blank">Click this link</a> and follow the steps on the page. Then save your API key here.
                                </p>                            
                                <?php
                            }
                            ?>
                            <p>
                                <input type="text" placeholder="Paste your YouTube API key here" name="<?php echo self::$opt_apikey; ?>" id="<?php echo self::$opt_apikey; ?>" value="<?php echo esc_attr(trim($all[self::$opt_apikey])); ?>" class="regular-text" style='max-width: 40%;'>
                            </p>                                

                            <div class="ytprefs-ob-nav">
                                <div class="ytprefs-ob-nav-ultimate">
                                    <button type="button" class="button-secondary ytprefs-ob-nav-prev">&laquo; Previous</button>                                
                                    <button type="button" class="button-secondary ytprefs-ob-nav-close">I'll do this later.</button>
                                    <button type="submit" class="button-primary ytprefs-ob-nav-next">Save & Finish</button>                                    
                                </div>
                                <div class="ytprefs-ob-nav-penultimate ytprefs-ob-nav-hide">
                                    <button type="button" class="button-secondary ytprefs-ob-nav-prev">&laquo; Previous</button>                                
                                    <button type="button" class="button-secondary ytprefs-ob-nav-skip">I'll do this later &raquo;</button>
                                    <button type="submit" class="button-primary ytprefs-ob-nav-next">Save & Next &raquo;</button>                                    
                                </div>
                            </div>                    
                        </form>
                    </div>
                </div>
                <div class="ytprefs-ob-step ytprefs-ob-step4">
                    <div class="ytprefs-ob-content">
                        <?php
                        if (!self::vi_logged_in() && !self::vi_script_setup_done())
                        {
                            echo '<div class="vi-registration-box">';
                            include_once(EPYTVI_INCLUDES_PATH . 'vi_registration_form.php');
                            include_once(EPYTVI_INCLUDES_PATH . 'vi_login_success.php');
                            echo '</div>';
                        }
                        else
                        {
                            ?>
                            <h2>Monetization</h2>
                            <p class="ytprefs-ob-success">
                                Hooray! You have already signed up for the <a href="<?php echo admin_url('admin.php?page=youtube-ep-vi') ?>" target="_blank">video ad monetization feature</a>.
                            </p>
                            <?php
                        }
                        ?>

                        <div class="ytprefs-ob-nav">
                            <button type="button" class="button-secondary ytprefs-ob-nav-prev">&laquo; Previous</button>
                            <button type="button" class="button-primary ytprefs-ob-nav-close">Finish</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php
    }

    public static function save_changes_button($submitted)
    {
        $button_label = 'Save Changes';
        if ($submitted)
        {
            $button_label = 'Changes Saved';
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ()
                {
                    setTimeout(function ()
                    {
                        jQuery('.ytprefs-submit').text('Save Changes');
                    }, 3000);
                });</script>
            <?php
        }
        ?>
        <p class="submit">
            <button type="submit" name="Submit" class="button-primary ytprefs-submit"><?php _e($button_label) ?></button>
            <em>If you're using a separate caching plugin and you do not see your changes after saving, <strong class="orange">you need to reset your cache.</strong></em>
        </p>
        <?php
    }

    public static function ytprefsscript()
    {
        $loggedin = current_user_can('edit_posts');
        if (!($loggedin && self::$alloptions[self::$opt_admin_off_scripts]))
        {
            wp_enqueue_style(
                    '__EPYT__style', plugins_url('styles/ytprefs' . self::$min . '.css', __FILE__), array(), self::$version
            );
            $cols = floatval(self::$alloptions[self::$opt_gallery_columns]);
            $cols = $cols == 0 ? 3.0 : $cols;
            $colwidth = 100.0 / $cols;
            $custom_css = "
                .epyt-gallery-thumb {
                        width: " . round($colwidth, 3) . "%;
                }
                ";

            if (self::$alloptions[self::$opt_gallery_collapse_grid] == 1)
            {
                foreach (self::$alloptions[self::$opt_gallery_collapse_grid_breaks] as $idx => $bpts)
                {
                    $custom_css .= "
                         @media (min-width:" . $bpts['bp']['min'] . "px) and (max-width: " . $bpts['bp']['max'] . "px) {
                            .epyt-gallery-rowbreak {
                                display: none;
                            }
                            .epyt-gallery-allthumbs[class*=\"epyt-cols\"] .epyt-gallery-thumb {
                                width: " . round(100.0 / intval($bpts['cols']), 3) . "% !important;
                            }
                          }";
                }
            }

            wp_add_inline_style('__EPYT__style', $custom_css);


            if ((isset(self::$alloptions[self::$opt_pro]) && strlen(trim(self::$alloptions[self::$opt_pro])) > 8))
            {
                wp_enqueue_style('__disptype__', plugins_url('scripts/lity' . self::$min . '.css', __FILE__), array(), self::$version);
                wp_enqueue_script('__dispload__', plugins_url('scripts/lity' . self::$min . '.js', __FILE__), array('jquery'), self::$version);
            }

            wp_enqueue_script('__ytprefs__', plugins_url('scripts/ytprefs' . self::$min . '.js', __FILE__), array('jquery'), self::$version);

            if (self::$alloptions[self::$opt_old_script_method] != 1)
            {
                $my_script_vars = array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'security' => wp_create_nonce('embedplus-nonce'),
                    'gallery_scrolloffset' => intval(self::$alloptions[self::$opt_gallery_scrolloffset]),
                    'eppathtoscripts' => plugins_url('scripts/', __FILE__),
                    'eppath' => plugins_url('/', __FILE__),
                    'epresponsiveselector' => self::get_responsiveselector(),
                    'epdovol' => true,
                    'version' => self::$alloptions[self::$opt_version],
                    'evselector' => self::get_evselector(),
                    'ajax_compat' => self::$alloptions[self::$opt_ajax_compat] == '1' ? true : false,
                    'maxres_facade' => esc_attr(self::$alloptions[self::$opt_maxres_facade]),
                    'ytapi_load' => self::$alloptions[self::$opt_ytapi_load],
                    'pause_others' => self::$alloptions[self::$opt_pause_others] == '1' ? true : false,
                    'stopMobileBuffer' => self::$alloptions[self::$opt_stop_mobile_buffer] == '1' ? true : false,
                    'facade_mode' => self::$alloptions[self::$opt_facade_mode] == '1' ? true : false,
                    'not_live_on_channel' => self::$alloptions[self::$opt_not_live_on_channel] == '1' ? true : false,
                    'not_live_showtime' => intval(self::$alloptions[self::$opt_not_live_showtime]),
                    'vi_active' => false, //self::$alloptions[self::$opt_vi_active] == '1' ? true : false,
                    'vi_js_posttypes' => array() // self::$alloptions[self::$opt_vi_js_posttypes]
                );

                if (isset(self::$alloptions[self::$opt_pro]) && strlen(trim(self::$alloptions[self::$opt_pro])) > 8 && isset(self::$alloptions[self::$opt_dashpre]) && self::$alloptions[self::$opt_dashpre] == '1')
                {
                    $my_script_vars['dshpre'] = true;
                }
                wp_localize_script('__ytprefs__', '_EPYT_', $my_script_vars);
            }

            if ((bool) self::$alloptions[self::$opt_gdpr_consent])
            {
                wp_enqueue_script('__jquery_cookie__', plugins_url('scripts/jquery.cookie' . self::$min . '.js', __FILE__), array('jquery'), self::$version);
            }

            ////////////////////// cloudflare accomodation
            //add_filter('script_loader_tag', array(self::class, 'set_cfasync'), 10, 3);

            if ((self::$alloptions[self::$opt_pro] && strlen(trim(self::$alloptions[self::$opt_pro])) > 0) && self::$alloptions[self::$opt_dynload] == 1)
            {
                wp_enqueue_style('__dyntype__', plugins_url('scripts/embdyn' . self::$min . '.css', __FILE__), array(), self::$version);
                wp_enqueue_script('__dynload__', plugins_url('scripts/embdyn' . self::$min . '.js', __FILE__), array('__ytprefs__'), self::$version);
            }
        }
    }

    public static function set_cfasync($tag, $handle, $src)
    {
        if ('__ytprefs__' !== $handle)
        {
            return $tag;
        }
        return str_replace('<script', '<script data-cfasync="false" ', $tag);
    }

    public static function get_evselector()
    {
        $evselector = 'iframe.__youtube_prefs__[src], iframe[src*="youtube.com/embed/"], iframe[src*="youtube-nocookie.com/embed/"]';

        if (self::$alloptions[self::$opt_evselector_light] == 1)
        {
            $evselector = 'iframe.__youtube_prefs__[src]';
        }

        return $evselector;
    }

    public static function get_responsiveselector()
    {
        $responsiveselector = '[]';
        if (self::$alloptions[self::$opt_widgetfit] == 1)
        {
            $responsiveselector = '["iframe.__youtube_prefs_widget__"]';
        }
        if (self::$alloptions[self::$opt_responsive] == 1)
        {
            if (self::$alloptions[self::$opt_responsive_all] == 1)
            {
                $responsiveselector = '["iframe.__youtube_prefs__","iframe[src*=\'youtube.com\']","iframe[src*=\'youtube-nocookie.com\']","iframe[data-ep-src*=\'youtube.com\']","iframe[data-ep-src*=\'youtube-nocookie.com\']","iframe[data-ep-gallerysrc*=\'youtube.com\']"]';
            }
            else
            {
                $responsiveselector = '["iframe.__youtube_prefs__"]';
            }
        }
        return $responsiveselector;
    }

    public static function admin_enqueue_scripts($hook)
    {
        if (in_array($hook, self::$admin_page_hooks))
        {
            wp_enqueue_style('__ytprefs_admin__alertify_css', plugins_url('styles/alertify/alertify' . self::$min . '.css', __FILE__), array(), self::$version);
            wp_enqueue_style('__ytprefs_admin__alertify_theme_css', plugins_url('styles/alertify/themes/default' . self::$min . '.css', __FILE__), array(), self::$version);
            wp_enqueue_style('__ytprefs_admin__vi_css', plugins_url('styles/ytvi-admin' . self::$min . '.css', __FILE__), array(), self::$version);
            wp_enqueue_script('__ytprefs_admin__alertify_js', plugins_url('scripts/alertify/alertify' . self::$min . '.js', __FILE__), array(), self::$version);
            wp_enqueue_script('__ytprefs_admin__alertify_defaults_js', plugins_url('scripts/alertify/alertify-defaults' . self::$min . '.js', __FILE__), array(), self::$version);
//            wp_enqueue_script('__ytprefs_admin__moment_js', plugins_url('scripts/chartjs/moment' . self::$min . '.js', __FILE__), array(), self::$version);
//            wp_enqueue_script('__ytprefs_admin__chart_js', plugins_url('scripts/chartjs/chart' . self::$min . '.js', __FILE__), array('__ytprefs_admin__moment_js'), self::$version);
//            wp_enqueue_script('__ytprefs_admin__chart_deferred_js', plugins_url('scripts/chartjs/chartjs-plugin-deferred' . self::$min . '.js', __FILE__), array('__ytprefs_admin__chart_js'), self::$version);
        }

        wp_enqueue_style('embedplusyoutube', plugins_url() . '/youtube-embed-plus-pro/scripts/embedplus_mce' . self::$min . '.css', array(), self::$version);
        wp_enqueue_script('__ytprefs_admin__', plugins_url('scripts/ytprefs-admin' . self::$min . '.js', __FILE__), array('jquery', 'jquery-effects-fade'), self::$version, false);
        $admin_script_vars = array(
            'wpajaxurl' => admin_url('admin-ajax.php'),
            'wizhref' => admin_url('admin.php?page=youtube-ep-wizard') . '&random=' . rand(1, 1000) . '&TB_iframe=true&width=950&height=800',
            'manage_options' => current_user_can('manage_options'),
            'security' => wp_create_nonce('embedplus-nonce'),
            'onboarded' => self::$alloptions[self::$opt_onboarded],
            'vi_logged_in' => self::vi_logged_in(),
            'epbase' => self::$epbase,
            'admin_url' => admin_url(),
            'vi_js_settings' => self::$alloptions[self::$opt_vi_js_settings],
            'admin_url_ytprefs' => admin_url('admin.php?page=youtube-my-preferences'),
            'admin_url_vi' => admin_url('admin.php?page=youtube-ep-vi')
                //'epblogwidth' => self::get_blogwidth(),
                //'epprokey' => self::$alloptions[self::$opt_pro],
                //'epbasesite' => self::$epbase,
                //'epversion' => self::$version,
                //'myytdefaults' => http_build_query(self::$alloptions),
                //'eppluginadminurl' => admin_url('admin.php?page=youtube-my-preferences')
        );
        wp_localize_script('__ytprefs_admin__', '_EPYTA_', $admin_script_vars);

        if (function_exists('add_thickbox'))
        {
            add_thickbox();
        }


        if ((get_bloginfo('version') >= '3.3') && self::custom_admin_pointers_check())
        {
            add_action('admin_print_footer_scripts', array(self::class, 'custom_admin_pointers_footer'));

            wp_enqueue_script('wp-pointer');
            wp_enqueue_style('wp-pointer');
        }

        if (self::$alloptions['glance'] == 1)
        {
            add_action('admin_print_footer_scripts', array(self::class, 'glance_script'));
        }

        if ($hook == self::$wizard_hook)
        {
            wp_enqueue_style('__ytprefs_admin__wizard_animate', plugins_url() . '/youtube-embed-plus-pro/scripts/embdyn' . self::$min . '.css', array(), self::$version);
            wp_enqueue_style('__ytprefs_admin__wizard_ui', plugins_url() . '/youtube-embed-plus-pro/styles/jquery-ui' . self::$min . '.css', array(), self::$version);
            wp_enqueue_style('__ytprefs_admin__wizard', plugins_url() . '/youtube-embed-plus-pro/styles/ytprefs-wizard' . self::$min . '.css', array(), self::$version);
            wp_enqueue_script('__ytprefs_admin__wizard_angularjs', plugins_url('scripts/angular' . self::$min . '.js', __FILE__), array(), self::$version);
            wp_enqueue_script('__ytprefs_admin__wizard_script', plugins_url('scripts/ytprefs-wizard' . self::$min . '.js', __FILE__), array('jquery', 'jquery-ui-accordion', 'jquery-ui-tabs', '__ytprefs_admin__wizard_angularjs'), self::$version);
        }

        if ($hook == self::$onboarding_hook)
        {
            wp_enqueue_style('__ytprefs_admin__onboarding_animate', plugins_url() . '/youtube-embed-plus-pro/scripts/embdyn' . self::$min . '.css', array(), self::$version);
            wp_enqueue_style('__ytprefs_admin__onboarding_ui', plugins_url() . '/youtube-embed-plus-pro/styles/jquery-ui' . self::$min . '.css', array(), self::$version);
            wp_enqueue_style('__ytprefs_admin__onboarding', plugins_url() . '/youtube-embed-plus-pro/styles/ytprefs-onboarding' . self::$min . '.css', array(), self::$version);
        }
    }

    public static function get_blogwidth()
    {
        $blogwidth = null;
        try
        {
            $embed_size_w = intval(get_option('embed_size_w'));

            global $content_width;
            if (empty($content_width))
            {
                $content_width = $GLOBALS['content_width'];
            }

            $blogwidth = $embed_size_w ? $embed_size_w : ($content_width ? $content_width : 450);
        }
        catch (Exception $ex)
        {
            
        }

        $blogwidth = preg_replace('/\D/', '', $blogwidth); //may have px

        return $blogwidth;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    private static function ajax_referer()
    {
        return check_ajax_referer('embedplus-nonce', 'security', false);
    }

    public static function base_url()
    {
        $parsed = parse_url(site_url());
        return $parsed['scheme'] . '://' . $parsed['host'];
    }

    public static function on_deactivation()
    {
        self::vi_cron_stop();
    }

    private static function update_option_set($new_options)
    {
        $all = get_option(self::$opt_alloptions);
        $all = $new_options + $all;
        update_option(self::$opt_alloptions, $all);
        self::$alloptions = get_option(self::$opt_alloptions);
    }

    private static function vi_remote_get($endpoint, $options = array())
    {
        $params = $options + array(
            'headers' => array('Authorization' => self::$alloptions[self::$opt_vi_token]),
            'timeout' => self::$curltimeout
        );
        return wp_remote_get($endpoint, $params);
    }

    private static function vi_remote_post($endpoint, $options = array())
    {
        $params = $options + array(
            'headers' => array('Content-Type' => 'application/json', 'Authorization' => self::$alloptions[self::$opt_vi_token]),
            'timeout' => self::$curltimeout
        );
//        if (self::$alloptions[self::$opt_debugmode])
//        {
//            echo $endpoint . '<br>' . self::vi_debug_json($params);
//        }
        return wp_remote_post($endpoint, $params);
    }

    private static function vi_remote_post_anon($endpoint, $options = array())
    {
        $params = $options + array(
            'headers' => array('Content-Type' => 'application/json'),
            'timeout' => self::$curltimeout
        );
        return wp_remote_post($endpoint, $params);
    }

    private static function vi_cache_endpoints_valid(&$apiResult)
    {
        $messages = array();


        if (is_wp_error($apiResult))
        {
            $messages[] = $apiResult->get_error_message();
        }
        else
        {
            $jsonResult = json_decode($apiResult['body']);

            if (!empty($jsonResult->error))
            {
                $messages[] = $jsonResult->error;
            }

            if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL))
            {
                $messages[] = "Please enter a valid email address.";
            }

            if (isset($jsonResult->status) && strcasecmp($jsonResult->status, 'ok') == 0 && isset($jsonResult->data) && is_object($jsonResult->data))
            {
                $apiResult = $jsonResult;
            }
        }
        if (empty($messages))
        {
            return true;
        }
        return $messages;
    }

    public static function vi_cache_endpoints()
    {
        $result = array();
        $apiResult = wp_remote_get(EPYTVI_ENDPOINTS_URL, array('timeout' => self::$curltimeout));
        $valid = self::vi_cache_endpoints_valid($apiResult);
        if ($valid === true)
        {
            $new_options = array(
                self::$opt_vi_endpoints => $apiResult->data
            );

            self::update_option_set($new_options);

            $post_email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            if (!empty($post_email))
            {
                $result['type'] = 'success';
                $result['data'] = $apiResult->data;
                $result['signupURLParams'] = $apiResult->data->signupURL . '?aid=WP_embedplus&email=' . filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) . '&domain=' . site_url();
            }
        }
        else
        {
            $result['type'] = 'error';
            $result['message'] = implode('<br/>', $valid);
        }

        return $result;
    }

    public static function vi_cache_endpoints_ajax()
    {
        $result = array();
        if (self::is_ajax() && self::ajax_referer() && current_user_can('manage_options'))
        {
            $result = self::vi_cache_endpoints();
        }
        else
        {
            $result['type'] = 'error';
            $result['message'] = 'Sorry, there was a problem submitting the data.';
        }

        $result['message'] = wp_kses_post($result['message']);
        echo json_encode($result);
        die();
    }

    public static function vi_notice_login_reminder()
    {
        if (filter_has_var(INPUT_COOKIE, 'vi_signup_attempt'))
        {
            $screen = get_current_screen();
            $date_string = filter_input(INPUT_COOKIE, 'vi_signup_attempt', FILTER_SANITIZE_STRING);
            $date_attempt = strtotime($date_string);
            $date_wait = strtotime($date_string . ' + 7 days');

            if (time() > $date_wait &&
                    !self::vi_logged_in() &&
                    in_array($screen->id, array('toplevel_page_youtube-my-preferences'))
            )
            {
                ?>
                <div class="notice notice-warning is-dismissible vi_notice_login_reminder">
                    <p>
                        It looks like you may have signed up for the vi monetization feature, but haven't completed the settings to receive revenue. Click the "Monetize" tab below to login and continue.
                    </p>
                </div>
                <script>
                    (function ($)
                    {
                        $(document).ready(function ()
                        {
                            $('.vi_notice_login_reminder').on('click', '.notice-dismiss', function ()
                            {
                                document.cookie = 'vi_signup_attempt=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
                            });
                        });
                    })(jQuery);
                </script>
                <?php
            }
        }
    }

    private static function vi_login_valid(&$input)
    {
        $messages = array();
        if (empty($input['email']))
        {
            $messages[] = 'Please enter your email.';
        }
        if (empty($input['password']))
        {
            $messages[] = 'Please enter your vi password.';
        }

        if (empty($messages))
        {
            return true;
        }
        return $messages;
    }

    private static function vi_login_api_valid(&$apiResult)
    {
        $messages = array();
        if (is_wp_error($apiResult))
        {
            $messages[] = $apiResult->get_error_message();
        }
        else
        {
            $jsonResult = json_decode($apiResult['body']);

            if (!empty($jsonResult->error))
            {
                $messages[] = $jsonResult->error->message . ": " . (is_string($jsonResult->error->description) ? $jsonResult->error->description : json_encode($jsonResult->error->description));
            }

            if (isset($jsonResult->status) && strcasecmp($jsonResult->status, 'ok') == 0 && isset($jsonResult->data) && strlen($jsonResult->data) > 0)
            {
                $apiResult = $jsonResult;
            }
        }
        if (empty($messages))
        {
            return true;
        }

        return $messages;
    }

    private static function vi_adstxt_api_valid(&$apiResult)
    {
        $messages = array();
        if (is_wp_error($apiResult))
        {
            $messages[] = $apiResult->get_error_message();
        }
        else
        {
            $jsonResult = json_decode($apiResult['body']);

            if (!empty($jsonResult->error))
            {
                $messages[] = implode(': ', array($jsonResult->error->message, $jsonResult->error->description));
            }

            if (isset($jsonResult->status) && strcasecmp($jsonResult->status, 'ok') == 0 && isset($jsonResult->data) && strlen($jsonResult->data) > 0)
            {
                $apiResult = $jsonResult;
            }
        }
        if (empty($messages))
        {
            return true;
        }
        return $messages;
    }

    public static function vi_login()
    {
        $result = array();
        $default = array(
            'email' => '',
            'password' => ''
        );
        $input = shortcode_atts($default, stripslashes_deep($_POST));
        $valid = self::vi_login_valid($input);
        if ($valid === true)
        {
            self::vi_cache_endpoints();
            $loginAPI = self::$alloptions[self::$opt_vi_endpoints]->loginAPI . '?affiliateId=WP_embedplus';
            $inputAuth = array(
                'email' => $input['email'],
                'password' => $input['password']
            );
            $apiResult = self::vi_remote_post_anon($loginAPI, array(
                        'body' => json_encode($inputAuth)
            ));
            $valid = self::vi_login_api_valid($apiResult);

            if ($valid === true)
            {
                $result['type'] = 'success';


                $new_options = array(
                    self::$opt_vi_token => $apiResult->data,
                    self::$opt_vi_last_login => date('Y-m-d H:i:s')
                );

                self::update_option_set($new_options);
            }
            else
            {
                $result['type'] = 'error';
                $result['message'] = implode('<br/>', $valid);
            }
        }
        else
        {
            $result['type'] = 'error';
            $result['message'] = implode('<br/>', $valid);
        }

        if ($result['type'] === 'success')
        {
            self::vi_db_init_schema();
        }
        return $result;
    }

    public static function vi_login_ajax()
    {
        $result = array();
        if (self::is_ajax() && self::ajax_referer() && current_user_can('manage_options'))
        {
            $result = self::vi_login();
        }
        else
        {
            $result['type'] = 'error';
            $result['message'] = 'Sorry, there was a problem submitting the data.';
        }

        $result['message'] = wp_kses_post($result['message']);
        echo json_encode($result);
        die();
    }

    public static function vi_logout_ajax()
    {
        $result = array();
        if (self::is_ajax() && self::ajax_referer() && current_user_can('manage_options'))
        {
            self::vi_cron_stop();

            $new_options = array(
                self::$opt_vi_token => ''
            );

            self::update_option_set($new_options);
            $result['type'] = 'success';
            $result['url'] = admin_url('admin.php?page=youtube-my-preferences');
        }
        else
        {
            $result['type'] = 'error';
            $result['message'] = 'Sorry, there was a problem submitting the data.';
        }
        echo json_encode($result);
        die();
    }

    public static function vi_toggle_ajax()
    {
        $result = array();
        if (self::is_ajax() && self::ajax_referer() && current_user_can('manage_options'))
        {
            $new_options = array(
                self::$opt_vi_active => self::$alloptions[self::$opt_vi_active] ? 0 : 1
            );

            self::update_option_set($new_options);
            $result['type'] = 'success';
            $result['button_text'] = self::$alloptions[self::$opt_vi_active] ? 'On' : 'Off';
        }
        else
        {
            $result['type'] = 'error';
            $result['message'] = 'Sorry, there was a problem submitting the data.';
        }
        echo json_encode($result);
        die();
    }

    public static function vi_hide_feature_ajax()
    {
        $result = array();
        if (self::is_ajax() && self::ajax_referer() && current_user_can('manage_options'))
        {
            $new_options = array(
                self::$opt_vi_hide_monetize_tab => 1
            );

            self::update_option_set($new_options);
            $result['type'] = 'success';
            $result['url'] = admin_url('admin.php?page=youtube-my-preferences');
        }
        else
        {
            $result['type'] = 'error';
            $result['message'] = 'Sorry, there was a network error. Please try again, or turn off this feature using the "Hide Monetize Feature" checkbox on the "Defaults" tab of the YouTube settings. If the issue persists, please contact ext@embedplus.com';
        }
        echo json_encode($result);
        die();
    }

    public static function vi_cover_prompt_yes()
    {
        return filter_input(INPUT_COOKIE, 'vi_cover_prompt_yes', FILTER_SANITIZE_NUMBER_INT) == 1;
    }

    public static function vi_cron_stop()
    {
        $timestamp = wp_next_scheduled('ytvi_cron_cache_js_hook');
        if ($timestamp !== false)
        {
            wp_unschedule_event($timestamp, 'ytvi_cron_cache_js_hook');
        }
    }

    private static function vi_reports_valid(&$apiResult)
    {
        $messages = array();
        if (is_wp_error($apiResult))
        {
            $messages[] = $apiResult->get_error_message();
        }
        else
        {
            $jsonResult = json_decode($apiResult['body']);

            //$messages[] = $apiResult['body']; // COMMENT

            if (!empty($jsonResult->error))
            {
                $messages[] = $jsonResult->error->message . ": " . $jsonResult->error->description;
            }

            if (isset($jsonResult->status) && strcasecmp($jsonResult->status, 'ok') == 0 && isset($jsonResult->data))
            {
                $apiResult = $jsonResult;
            }
        }
        if (empty($messages))
        {
            return true;
        }

        return $messages;
    }

    public static function vi_reports_ajax()
    {
        $result = array();
        if (self::is_ajax() && self::ajax_referer() && current_user_can('manage_options'))
        {
            $revenueResult = self::vi_remote_get(self::$alloptions[self::$opt_vi_endpoints]->revenueAPI);
            $revenue_valid = self::vi_reports_valid($revenueResult);
            if ($revenue_valid === true)
            {
                $result['data'] = $revenueResult->data;
                $result['type'] = 'success';
            }
            else
            {
                $result['type'] = 'error';
                $result['message'] = wp_kses_post(implode('<br/>', $revenue_valid));
            }
        }
        else
        {
            $result['type'] = 'error';
            $result['message'] = 'Sorry, there was a problem retrieving the data.';
        }
        echo json_encode($result);
        die();
    }

    private static function vi_cache_user_adstxt()
    {
        $adsTxtAPI = self::$alloptions[self::$opt_vi_endpoints]->adsTxtAPI;
        $iabResult = self::vi_remote_get($adsTxtAPI);
        $iab_valid = self::vi_adstxt_api_valid($iabResult);
        if ($iab_valid === true)
        {
            $new_options = array(
                self::$opt_vi_adstxt => $iabResult->data
            );

            self::update_option_set($new_options);
            return $iabResult->data;
        }
        return false;
    }

    private static function vi_adstxt_status()
    {
        $user_adstxt = self::vi_cache_user_adstxt();
        $current_adstxt = false;
        if ($user_adstxt === false)
        {
            return array(
                'code' => -1,
                'message' => 'Sorry, your publisher ads.txt info could not be retrieved. Please wait a few minutes and try again. Your ads.txt verification file will enable you to make money through vi. <a href="https://www.vi.ai/publisherfaq/?aid=WP_embedplus&utm_source=Wordpress&utm_medium=WP_embedplus" target="_blank">FAQ &raquo;</a>'
            );
        }
        else
        {
            $user_adstxt = preg_replace('~\R~u', PHP_EOL, $user_adstxt);
        }

        $adstxt_file = self::vi_get_home_path() . 'ads.txt';
        $adstxt_url = self::base_url() . '/ads.txt';

        $adstxt_http = wp_remote_get($adstxt_url, array('timeout' => self::$curltimeout));
        if (!is_wp_error($adstxt_http) && in_array(wp_remote_retrieve_response_code($adstxt_http), array(200, 301, 302, 304)))
        {
            $current_adstxt = wp_remote_retrieve_body($adstxt_http);
        }
        if (empty($current_adstxt))
        {
            $current_adstxt = file_get_contents($adstxt_file);
        }

        if (!empty($current_adstxt))
        {
            $current_adstxt = preg_replace('~\R~u', PHP_EOL, $current_adstxt);
            // append
            if (is_writable($adstxt_file))
            {
                if (stripos($current_adstxt, '# 41b5eef6') === false)
                {
                    $to_write = PHP_EOL . $user_adstxt;
                    file_put_contents($adstxt_file, $to_write, FILE_APPEND);
                    return array(
                        'code' => 1,
                        'before_adstxt' => $current_adstxt,
                        'after_adstxt' => $current_adstxt . $to_write,
                        'message' => 'You successfully validated your account. Your <a target="_blank" href="' . site_url() . '/ads.txt">ads.txt</a> file has been updated, which enables you to make money through vi. <a href="' . esc_url(admin_url('admin.php?page=youtube-ep-vi#jumpfaq')) . '" target="_blank">FAQ &raquo;</a>'
                    );
                }
                else if ($current_adstxt !== $user_adstxt)
                {
                    $current_adstxt_lines = preg_split('/\r\n|\r|\n/', $current_adstxt);
                    $current_adstxt_lines = array_filter($current_adstxt_lines, array(self::class, 'vi_not_vi_adstxt_line'));
                    $former_adstxt = implode(PHP_EOL, $current_adstxt_lines);

                    $new_adstxt = $former_adstxt . (strlen($former_adstxt) > 0 ? PHP_EOL : '') . $user_adstxt;
                    if ($current_adstxt === $new_adstxt)
                    {
                        return array(
                            'code' => 2,
                            'message' => 'You successfully validated your account.'
                        );
                    }
                    else
                    {
                        file_put_contents($adstxt_file, $new_adstxt);

                        return array(
                            'code' => 1,
                            'before_adstxt' => $current_adstxt,
                            'after_adstxt' => $new_adstxt,
                            'message' => 'You successfully validated your account. Your <a target="_blank" href="' . site_url() . '/ads.txt">ads.txt</a> file has been updated, which enables you to make money through vi. <a href="' . esc_url(admin_url('admin.php?page=youtube-ep-vi#jumpfaq')) . '" target="_blank">FAQ &raquo;</a>'
                        );
                    }
                }
                else
                {
                    return array(
                        'code' => 2,
                        'message' => 'You successfully validated your account.'
                    );
                }
            }
            else
            {
                if (stripos($current_adstxt, $user_adstxt) === false) // $user_adstxt
                {
                    return array(
                        'code' => 0,
                        'message' => 'Sorry, your current ads.txt file could not be automatically be updated. Please first <a class="button-secondary" href="' .
                        admin_url('admin.php') . '?ytvi_adstxt_download=1&key=' . urlencode(self::$alloptions[self::$opt_vi_token]) . '">download this updated ads.txt</a> file and upload it to your site root, then try logging in again. Your ads.txt verification file will enable you to make money through vi. <a href="https://www.vi.ai/publisherfaq/?aid=WP_embedplus&utm_source=Wordpress&utm_medium=WP_embedplus" target="_blank">FAQ &raquo;</a>'
                    );
                }
                else
                {
                    return array(
                        'code' => 2,
                        'message' => 'You successfully validated your account.'
                    );
                }
            }
        }
        else
        {
            // create
            if ((!file_exists($adstxt_file) && is_writable(self::vi_get_home_path())) || (file_exists($adstxt_file) && is_writable($adstxt_file)))
            {
                file_put_contents($adstxt_file, self::$alloptions[self::$opt_vi_adstxt], FILE_APPEND);

                return array(
                    'code' => 1,
                    'before_adstxt' => $current_adstxt,
                    'after_adstxt' => self::$alloptions[self::$opt_vi_adstxt],
                    'message' => 'You successfully validated your account. Your <a target="_blank" href="' . site_url() . '/ads.txt">ads.txt</a> file has been created, which enables you to make money through vi. <a href="' . esc_url(admin_url('admin.php?page=youtube-ep-vi#jumpfaq')) . '" target="_blank">FAQ &raquo;</a>'
                );
            }
            else
            {
                return array(
                    'code' => 0,
                    'message' => 'Sorry, your ads.txt verification file could not automatically be created. Please first <a class="button-secondary" href="' .
                    admin_url('admin.php') . '?ytvi_adstxt_download=1&key=' . urlencode(self::$alloptions[self::$opt_vi_token]) . '">download this ads.txt</a> file and upload it to your site root, then try logging in again. Your ads.txt verification file will enable you to make money through vi. <a href="https://www.vi.ai/publisherfaq/?aid=WP_embedplus&utm_source=Wordpress&utm_medium=WP_embedplus" target="_blank">FAQ &raquo;</a>'
                );
            }
        }
    }

    public static function vi_adstxt_status_soft_ajax()
    {
        $result = array();
        if (self::is_ajax() && self::ajax_referer() && current_user_can('manage_options'))
        {
            $default = array(
                'current_adstxt' => ''
            );
            $input = shortcode_atts($default, stripslashes_deep($_POST));
            $result = self::vi_adstxt_status_soft($input['current_adstxt']);

            if (isset($result['code']) && intval($result['code']) < 0)
            {
                $result['token'] = self::$alloptions[self::$opt_vi_token];
            }
        }
        else
        {
            $result['type'] = 'error';
            $result['message'] = 'Sorry, there was a problem verifying your ads.txt file. Please try again.';
        }

        $result['message'] = wp_kses_post($result['message']);
        echo json_encode($result);
        die();
    }

    private static function vi_adstxt_status_soft($current_adstxt)
    {
        $adstxt_url = self::base_url() . '/ads.txt';
        $adstxt_note = ' <strong>Note:</strong> If you already have an ads.txt file at ' . $adstxt_url . ', you will just need to add in the additional lines found in the download.';
        $user_adstxt = self::vi_cache_user_adstxt();
        $current_adstxt = empty($current_adstxt) ? false : $current_adstxt;
        if ($user_adstxt === false)
        {
            return array(
                'code' => -1,
                'message' => 'For your security, a quick reauthentication is needed to begin setting up your ads.txt file. First, log out of this Ads Settings page with the "Logout" button right above and then log back in with your vi login and password. Then come back to this tab for next steps. Your ads.txt verification file will enable you to make money through vi. <a href="https://www.vi.ai/publisherfaq/?aid=WP_embedplus&utm_source=Wordpress&utm_medium=WP_embedplus" target="_blank">FAQ &raquo;</a>'
            );
        }
        else
        {
            $user_adstxt = preg_replace('~\R~u', PHP_EOL, $user_adstxt);
        }

        if (!empty($current_adstxt))
        {
            $current_adstxt = preg_replace('~\R~u', PHP_EOL, $current_adstxt);

            // append / update manually
            if (stripos($current_adstxt, $user_adstxt) === false)
            {
                if (stripos($current_adstxt, '# 41b5eef6') !== false) // update
                {
                    return array(
                        'code' => 0,
                        'message' => 'Looks like video intelligence has just updated its ad delivery partners. To get the most revenue out of your ads, open up your '
                        . ' <a href="' . self::base_url() . '/ads.txt" target="_blank">ads.txt</a> file and replace the vi lines (ending in # 41b5eef6) with the new lines you see below. Then, refresh this page. '
                        . ' Please do not reorder or double space the below lines. '
                        . ' <strong>If we helped you with your ads.txt in the past, feel free to contact us to help out again with this update.</strong> '
                        . '<code># video intelligence (vi.ai) ads.txt lines begin here:' . PHP_EOL . $user_adstxt . PHP_EOL . '# video intelligence (vi.ai) ads.txt lines end</code>'
                    );
                }
                else // add
                {
                    return array(
                        'code' => 0,
                        'message' => 'In your current <a href="' . self::base_url() . '/ads.txt" target="_blank">ads.txt</a> file, just add in the additional lines you see below. Then, refresh this page.'
                        . ' Please do not reorder or double space the below lines. '
                        . '<code># video intelligence (vi.ai) ads.txt lines begin here:' . PHP_EOL . $user_adstxt . PHP_EOL . '# video intelligence (vi.ai) ads.txt lines end</code>'
                    );
                }
            }
            else
            {
                return array(
                    'code' => 2,
                    'message' => '<p class="adstxt-verify-message-valid">You successfully validated your ads.txt file.</p>'
                );
            }
        }
        else
        {
            // create manually
            return array(
                'code' => 0,
                'message' => 'You can <a class="button button-small" href="' . admin_url('admin.php') . '?ytvi_adstxt_download=1&key=' . urlencode(self::$alloptions[self::$opt_vi_token]) . '">download this ads.txt</a> file and upload it to your site root (or copy the same text below). Then, refresh this page to verify.'
                . ' Please do not reorder or double space the below lines. '
                . '<code># video intelligence (vi.ai) ads.txt lines begin here:' . PHP_EOL . $user_adstxt . PHP_EOL . '# video intelligence (vi.ai) ads.txt lines end</code>'
            );
        }
    }

    private static function vi_not_vi_adstxt_line($line)
    {
        return stripos($line, '# 41b5eef6') === false;
    }

    public static function vi_get_home_path()
    {
        $abs_root = get_home_path();
        if (strlen($abs_root) <= 1)
        {
            $abs_root = trailingslashit(str_replace('\\', '/', ABSPATH));
            $url_path = parse_url(site_url());
            if (isset($url_path['path']))
            {
                $relpath = trailingslashit($url_path['path']);
                $relpath_length = strlen($relpath);
                $path_intersect = substr($abs_root, -$relpath_length);
                if ($path_intersect === $relpath)
                {
                    $abs_root = trailingslashit(substr($abs_root, 0, strlen($abs_root) - $relpath_length));
                }
            }
        }
        return $abs_root;
    }

    public static function vi_adstxt_lookup()
    {
        $request = esc_url_raw(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        if ('/ads.txt' === $request)
        {
            if (function_exists('tenup_display_ads_txt'))
            {
                $post_id = get_option('adstxt_post');
                if (!empty($post_id))
                {
                    $post = get_post($post_id);
                    header('Content-Type: text/plain');
                    echo esc_html($post->post_content);
                    die();
                }
            }
        }
    }

    public static function vi_adstxt_download()
    {
        $inp_key = filter_input(INPUT_GET, 'key');
        if (filter_input(INPUT_GET, 'ytvi_adstxt_download') == 1 && !empty($inp_key))
        {
            $key = urldecode(filter_input(INPUT_GET, 'key', FILTER_DEFAULT));
            self::$alloptions[self::$opt_vi_token] = $key;
            $user_adstxt = self::vi_cache_user_adstxt();
            $adstxt_file = self::vi_get_home_path() . 'ads.txt';
            $current_adstxt = file_exists($adstxt_file) ? file_get_contents($adstxt_file) : '';

            $current_adstxt_lines = preg_split('/\r\n|\r|\n/', $current_adstxt);
            $current_adstxt_lines = array_filter($current_adstxt_lines, array(self::class, 'vi_not_vi_adstxt_line'));
            $former_adstxt = implode(PHP_EOL, $current_adstxt_lines);

            $new_adstxt = $former_adstxt . (strlen($former_adstxt) > 0 ? PHP_EOL : '') . ($user_adstxt === false ? '' : $user_adstxt);

            $new_adstxt = '# video intelligence (vi.ai) ads.txt lines begin here:' . PHP_EOL . $new_adstxt . PHP_EOL . '# video intelligence (vi.ai) ads.txt lines end';

            header("Expires: 0");
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header('Cache-Control: pre-check=0, post-check=0, max-age=0', false);
            header("Pragma: no-cache");
            header("Content-Disposition:attachment; filename=ads.txt");
            header("Content-Type: application/force-download");

            echo $new_adstxt;

            exit();
        }
    }

    public static function vi_logged_in()
    {
        return !empty(self::$alloptions[self::$opt_vi_token]);
    }

    public static function vi_settings_nav()
    {
        ?>
        <h3 class="nav-tab-wrapper">
            <a class="nav-tab nav-tab-active" href="#jumphowitworks">How It Works</a>
            <a class="nav-tab" href="#jumpdescription">Video Categories</a>
            <a class="nav-tab" href="#jumpappearance">Appearance</a>
            <a class="nav-tab" href="#jumpplacement">Placement</a>
            <a class="nav-tab nav-tab-adstxt" href="#jumpadstxt">Ads.txt Verification &nbsp;</a>
            <a class="nav-tab" href="#jumpperformance">Revenue Reporting</a>
            <a class="nav-tab" href="#jumprevenue">Profile Settings</a>
            <a class="nav-tab" href="#jumpviprivacy">Privacy</a>
            <a class="nav-tab" href="#jumpfaq">FAQs</a>
            <a class="nav-tab" href="#jumpsupport">Support</a>
        </h3>
        <?php
    }

    private static function vi_cache_js_valid(&$apiResult)
    {
        $messages = array();
        if (is_wp_error($apiResult))
        {
            $messages[] = $apiResult->get_error_message();
        }
        else
        {
            $jsonResult = json_decode($apiResult['body']);

            if (!empty($jsonResult->error))
            {
                //$messages[] = implode(': ', array('vi API - ' . self::vi_debug_json($apiResult)));
                $messages[] = 'If the issue is not resolved, please contact support at ext@embedplus.com and we will get you going. (Error code ' . wp_remote_retrieve_response_code($apiResult) . ', v' . self::$version .
                        ' - <em>' . implode(': ', array($jsonResult->error->message, is_string($jsonResult->error->description) ? $jsonResult->error->description : json_encode($jsonResult->error->description))) . '</em>)';
            }

            if (isset($jsonResult->status) && strcasecmp($jsonResult->status, 'ok') == 0 && isset($jsonResult->data) && strlen($jsonResult->data) > 0)
            {
                $apiResult = $jsonResult;
            }
        }
        if (empty($messages))
        {
            return true;
        }
        return $messages;
    }

    private static function vi_cache_js($options)
    {
        $readonly = array(
            'domain' => parse_url(site_url(), PHP_URL_HOST),
            'adUnitType' => 'NATIVE_VIDEO_UNIT',
            'logoUrl' => 'https://example.com/logo.jpg'
        );
        $options = $readonly + $options;
        $jsTagAPI = self::$alloptions[self::$opt_vi_endpoints]->jsTagAPI;

        $iabCategoryList = explode(',', $options['iabCategory']);

        $matches = array();
        if (isset(self::$alloptions[self::$opt_vi_js_script]) && preg_match('/IAB_Category[ ]*:([^,]+),/i', self::$alloptions[self::$opt_vi_js_script], $matches))
        {
            $currCategory = array(trim($matches[1]));
            $iabCategoryList = array_diff($iabCategoryList, $currCategory);
        }

        $options['iabCategory'] = $iabCategoryList[array_rand($iabCategoryList)];

        $apiResult = self::vi_remote_post($jsTagAPI, array(
                    'body' => json_encode($options)
        ));
        //$js_valid = array(self::vi_debug_json($options));
        $js_valid = self::vi_cache_js_valid($apiResult);
        if ($js_valid === true)
        {
            $mod_data = $apiResult->data;

            $new_options = array(
                self::$opt_vi_js_script => $mod_data,
                self::$opt_vi_last_category_update => date('Y-m-d H:i:s')
            );

            self::update_option_set($new_options);
        }

        return $js_valid;
    }

    public static function vi_debug_json($json)
    {
        return '<pre>' . json_encode($json, JSON_PRETTY_PRINT) . '</pre>';
    }

    public static function vi_script_setup_done()
    {
        if (empty(self::$alloptions[self::$opt_vi_js_script]))
        {
            return false;
        }
        return true;
    }

    public static function vi_admin_dashboard_valid(&$item)
    {
        $messages = array();

        $all_post_types = get_post_types(array('public' => true), 'names');

        foreach ($item[self::$opt_vi_js_posttypes] as $pt)
        {
            if (!in_array($pt, $all_post_types))
            {
                $messages[] = 'Please choose only valid post types for your ad to appear in.';
            }
        }

        if (!in_array($item[self::$opt_vi_js_position], array('top', 'bottom')))
        {
            $messages[] = 'Please choose a valid placement position.';
        }

        $item[self::$opt_vi_js_settings]['keywords'] = substr(sanitize_text_field(str_replace(array('\'', '"'), '', $item[self::$opt_vi_js_settings]['keywords'])), 0, 200);

        $item[self::$opt_vi_js_settings]['iabCategory'] = sanitize_text_field($item[self::$opt_vi_js_settings]['iabCategory']);
        if (empty($item[self::$opt_vi_js_settings]['iabCategory']))
        {
            $messages[] = 'Please choose at least one IAB category under Video Categories.';
        }
        $item[self::$opt_vi_js_settings]['language'] = sanitize_text_field($item[self::$opt_vi_js_settings]['language']);
        if (empty($item[self::$opt_vi_js_settings]['language']))
        {
            $item[self::$opt_vi_js_settings]['language'] = 'en-us';
        }

        $item[self::$opt_vi_js_settings]['backgroundColor'] = sanitize_hex_color($item[self::$opt_vi_js_settings]['backgroundColor']);
        if (empty($item[self::$opt_vi_js_settings]['backgroundColor']))
        {
            $item[self::$opt_vi_js_settings]['backgroundColor'] = '#ffffff';
        }

        $item[self::$opt_vi_js_settings]['textColor'] = sanitize_hex_color($item[self::$opt_vi_js_settings]['textColor']);
        if (empty($item[self::$opt_vi_js_settings]['textColor']))
        {
            $item[self::$opt_vi_js_settings]['textColor'] = '#000000';
        }

        $item[self::$opt_vi_js_settings]['font'] = sanitize_text_field($item[self::$opt_vi_js_settings]['font']);
        if (empty($item[self::$opt_vi_js_settings]['font']))
        {
            $item[self::$opt_vi_js_settings]['font'] = 'Arial';
        }

        if (!is_numeric($item[self::$opt_vi_js_settings]['fontSize']) || intval($item[self::$opt_vi_js_settings]['fontSize']) < 0)
        {
            $item[self::$opt_vi_js_settings]['fontSize'] = 12;
        }

        if (empty($messages))
        {
            $js = self::vi_cache_js($item[self::$opt_vi_js_settings]);
            if ($js === true)
            {
                $item[self::$opt_vi_js_script] = self::$alloptions[self::$opt_vi_js_script];
            }
            else
            {
                $messages[] = 'For your security, a quick re-authentication is required to save your most recent customizations. Simply log out of this Ads Settings page with the "Logout" button right above and then log back in with your vi login and password. ';
                $messages = array_merge($messages, $js);
            }
        }

        if (empty($messages))
        {
            return true;
        }

        return $messages;
    }

    public static function vi_print_toggle_button()
    {
        ?>
        <button style="z-index: 10" <?php echo self::vi_script_setup_done() ? '' : ' disabled '; ?> class="button-primary ytvi-btn-toggle <?php echo self::$alloptions[self::$opt_vi_active] ? 'ytvi-btn-active' : 'ytvi-btn-inactive' ?>">
            vi ads are: <strong><?php echo self::$alloptions[self::$opt_vi_active] ? 'On' : 'Off' ?></strong>
            <?php
            if (!self::vi_script_setup_done())
            {
                ?>
                <div class="ytvi-notyet">
                    <h3>Before you can turn on your ads:</h3>
                    <ol class="list-ol">
                        <li>Complete the <em>Video Categories, Appearance, and Placement</em> tabs.</li>
                        <li>Then click on the <strong>Save Changes</strong> button in the bottom right of this screen.</li>
                        <li>Then click the top right button to turn vi ads on.</li>
                    </ol>
                    <p>
                        Once your ads are on, complete the <em>Ads.txt</em> tab to have your ads start earning revenue. Then the <em>Profile</em> tab shows you how to receive payments.
                    </p>
                </div>
                <?php
            }
            ?>
        </button>
        <?php
    }

    public static function vi_admin_dashboard()
    {
        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $message = '';
        $notice = '';

        $item = array(
            self::$opt_vi_js_settings => self::$alloptions[self::$opt_vi_js_settings],
            self::$opt_vi_js_script => self::$alloptions[self::$opt_vi_js_script],
            self::$opt_vi_js_posttypes => self::$alloptions[self::$opt_vi_js_posttypes],
            self::$opt_vi_js_position => self::$alloptions[self::$opt_vi_js_position],
            self::$opt_vi_show_gdpr_authorization => self::$alloptions[self::$opt_vi_show_gdpr_authorization],
            self::$opt_vi_show_privacy_button => self::$alloptions[self::$opt_vi_show_privacy_button]
        );

        if (wp_verify_nonce(filter_input(INPUT_POST, 'nonce'), basename(__FILE__)))
        {
            $post_vars = stripslashes_deep($_POST);
            if (!array_key_exists(self::$opt_vi_js_posttypes, $post_vars))
            {
                $post_vars[self::$opt_vi_js_posttypes] = array();
            }
            $post_vars = shortcode_atts($item, $post_vars);

            $item[self::$opt_vi_js_settings] = $post_vars[self::$opt_vi_js_settings] + $item[self::$opt_vi_js_settings];
            $item[self::$opt_vi_js_posttypes] = $post_vars[self::$opt_vi_js_posttypes];
            $item[self::$opt_vi_js_position] = $post_vars[self::$opt_vi_js_position];
            $item[self::$opt_vi_show_gdpr_authorization] = self::postchecked(self::$opt_vi_show_gdpr_authorization) ? 1 : 0;
            $item[self::$opt_vi_show_privacy_button] = self::postchecked(self::$opt_vi_show_privacy_button) ? 1 : 0;

            $item_valid = self::vi_admin_dashboard_valid($item);

            //$item_valid = array('<pre>_post: ' . print_r(stripslashes_deep($_POST), true) . '</pre>', '<pre>item: ' . print_r($item, true) . '</pre>');

            if ($item_valid === true)
            {
                self::update_option_set($item);

                $message = 'Settings were successfully saved. Now you can turn on vi ads above. Note: changes may take a few minutes to appear on your website. If you are using a separate caching plugin, <strong>you need to reset your cache</strong> to see any changes.';
            }
            else
            {
                $notice = wp_kses_post(implode('<br/>', $item_valid));
            }
        }
        ?>
        <div class="wrap wrap-vi wrap-vi-settings">
            <h1><img class="yt-admin-icon" src="<?php echo plugins_url(self::$folder_name . '/images/icon-monetize-dark.svg') ?>" />
                Video Ad Settings
                <a class="button-secondary ytvi-btn-logout">Logout of vi settings</a>
                <?php self::vi_print_toggle_button(); ?>
            </h1>
            <div class="update-nag notice">                
                <p>This feature is being deprecated in the next version. Please contact ext@embedplus.com for questions.</p>
            </div>
            <br>
            <div class="updated ytvi-msg-congrats">
                <p>
                    Congrats! Ads are now on. Here are some tips to maximize your fill rate and therefore revenue:   
                </p>
                <ul class="list-ul">
                    <li>Visibility - The higher the player is placed, the greater the demand and fill rate. Inserting it near the top or middle of your pages are best.</li>
                    <li>Ad Unit Size - The recommended minimum width for the player is 336px </li>
                    <li>Give vi.ai about 2-3 weeks to optimize their inventory for your site</li>
                    <li>Contact us for help if you have any questions: ext@embedplus.com</li>
                </ul>
            </div>
            <?php
            if (!empty($notice))
            {
                ?>
                <div id="notice" class="error"><p><?php echo wp_kses_post($notice) ?></p></div>
                <?php
            }
            if (!empty($message))
            {
                ?>
                <div id="message" class="updated"><p><?php echo wp_kses_post($message) ?></p></div>
                <?php
            }

            self::vi_settings_nav();

//            echo '<pre>';
//            print_r(_get_cron_array());
//            echo '</pre>';
            ?>

            <form id="form" method="POST">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__)) ?>"/>
                <section class="pattern" id="jumphowitworks">
                    <h2>How It Works</h2>
                    <p>Before you begin, please turn off any ad blocker extensions you may have, so that you will see how your ads look. Then follow the steps below:</p>
                    <br>
                    <div class="vi-how-works" data-jump="#jumpdescription">
                        <div class="vi-num">1</div>
                        <img src="<?php echo plugins_url(self::$folder_name . '/images/icon-hw-description.png') ?>"/>
                        <h3>Video Categories</h3>
                        <p>
                            Categorize your site to help match with the right ads.
                        </p>
                    </div>
                    <div class="vi-how-works" data-jump="#jumpappearance">
                        <div class="vi-num">2</div>
                        <img src="<?php echo plugins_url(self::$folder_name . '/images/icon-hw-appearance.png') ?>"/>
                        <h3>Appearance</h3>
                        <p>
                            Customize how the ad player should look.
                        </p>
                    </div>
                    <div class="vi-how-works" data-jump="#jumpplacement">
                        <div class="vi-num">3</div>
                        <img src="<?php echo plugins_url(self::$folder_name . '/images/icon-hw-placement.png') ?>"/>
                        <h3>Placement</h3>
                        <p>
                            Decide where the ad player should be placed.
                        </p>
                    </div>
                    <div class="vi-how-works" data-jump="#nojump">
                        <div class="vi-num">4</div>
                        <img src="<?php echo plugins_url(self::$folder_name . '/images/icon-hw-turnon.png') ?>"/>
                        <h3>Turn It On</h3>
                        <p>
                            Click the colored button at the top right of this page to make the ad player visible.
                        </p>
                    </div>
                    <div class="vi-how-works" data-jump="#jumpadstxt">
                        <div class="vi-num">5</div>
                        <img src="<?php echo plugins_url(self::$folder_name . '/images/icon-hw-adstxt.png') ?>"/>
                        <h3>Ads.txt Verification</h3>
                        <p>
                            Verify your ads.txt file to start earning revenue.
                        </p>
                    </div>
                    <div class="vi-how-works" data-jump="#jumpperformance">
                        <div class="vi-num">6</div>
                        <img src="<?php echo plugins_url(self::$folder_name . '/images/icon-hw-performance.png') ?>"/>
                        <h3>Revenue Reporting</h3>
                        <p>
                            View reports on your CPM, revenue, and more.
                        </p>
                    </div>
                    <div class="vi-how-works" data-jump="#jumprevenue">
                        <div class="vi-num">7</div>
                        <img src="<?php echo plugins_url(self::$folder_name . '/images/icon-hw-revenue.png') ?>"/>
                        <h3>Profile Settings</h3>
                        <p>
                            Collect your earnings in a few days via PayPal or bank transfer.
                        </p>
                    </div>
                </section>

                <section class="pattern" id="jumpdescription">
                    <div class="adstxt-help">
                        <img src="<?php echo plugins_url(self::$folder_name . '/images/adstxt-help.png') . '?ver=' . self::$version; ?>"/>
                        Trouble getting content that fits your site, even with the proper settings above/below? Contact support at <strong><a href="mailto:ext@embedplus.com">ext@embedplus.com</a></strong>
                    </div>
                    <h2><span class="vi-num">1</span> Video Categories (Multiple Allowed)</h2>
                    <p>
                        Your video ad will be optimized to relate to your site's content and the one or more categories you select below. Note that the quality of the matches improves over time. 
                    </p>
                    <p>
                        <strong>Tip:</strong> select more than one category to add variety to your video ads.
                        If you select more than one, you must stay logged in to this settings page for your categories to automatically add variety to your ads.
                    </p>
                    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
                        <tbody>
                            <tr class="form-field">
                                <th valign="top" scope="row">
                                    <label for="<?php echo self::$opt_vi_js_settings ?>[iabCategory]">IAB Categories</label>
                                    <small>Select the categories that most fit your website. You can select up to 4. </small>
                                </th>
                                <td>
                                    <strong>Filter by:</strong>
                                    <select class="iab-cat-parent">
                                        <option value="">Choose Filter</option>
                                        <option value="IAB1">Arts & Entertainment</option>
                                        <option value="IAB2">Automotive</option>
                                        <option value="IAB3">Business</option>
                                        <!--                                        <option value="IAB4">Careers</option>-->
                                        <!--                                        <option value="IAB5">Education</option>-->
                                        <!--                                        <option value="IAB6">Family & Parenting</option>-->
                                        <option value="IAB7">Health & Fitness</option>
                                        <option value="IAB8">Food & Drink</option>
                                        <option value="IAB9">Hobbies & Interests</option>
                                        <option value="IAB10">Home & Garden</option>
                                        <option value="IAB11">Law, Gov't & Politics</option>
                                        <option value="IAB12">News</option>
                                        <!--                                        <option value="IAB13">Personal Finance</option>-->
                                        <!--                                        <option value="IAB14">Society</option>-->
                                        <option value="IAB15">Science</option>
                                        <option value="IAB16">Pets</option>
                                        <option value="IAB17">Sports</option>
                                        <option value="IAB18">Style & Fashion</option>
                                        <option value="IAB19">Technology & Computing</option>
                                        <option value="IAB20">Travel</option>
                                        <!--                                        <option value="IAB21">Real Estate</option>-->
                                        <option value="IAB22">Shopping</option>
                                        <!--                                        <option value="IAB23">Religion & Spirituality</option>-->
                                        <option value="IAB24">Uncategorized</option>
                                        <option value="IAB25">Non-Standard Content</option>
                                    </select>
                                    <div class="iab-cat-child-box hidden">
                                        <strong>Then choose category:</strong>
                                        <select class="iab-cat-child" disabled>
                                            <option value="">Select Category:</option>
                                            <option value="IAB1">Arts & Entertainment (All)</option>
                                            <option value="IAB1-1">Books & Literature</option>
                                            <option value="IAB1-2">Celebrity Fan/Gossip</option>
                                            <option value="IAB1-3">Fine Art</option>
                                            <option value="IAB1-4">Humor</option>
                                            <option value="IAB1-5">Movies</option>
                                            <option value="IAB1-6">Music</option>
                                            <option value="IAB1-7">Television</option>
                                            <option value="IAB2">Automotive (All)</option>
                                            <option value="IAB2-1">Auto Parts</option>
                                            <option value="IAB2-2">Auto Repair</option>
                                            <option value="IAB2-3">Buying/Selling Cars</option>
                                            <option value="IAB2-4">Car Culture</option>
                                            <option value="IAB2-5">Certified Pre-Owned</option>
                                            <option value="IAB2-6">Convertible</option>
                                            <option value="IAB2-7">Coupe</option>
                                            <option value="IAB2-8">Crossover</option>
                                            <option value="IAB2-9">Diesel</option>
                                            <option value="IAB2-10">Electric Vehicle</option>
                                            <option value="IAB2-11">Hatchback</option>
                                            <option value="IAB2-12">Hybrid</option>
                                            <option value="IAB2-13">Luxury</option>
                                            <option value="IAB2-14">MiniVan</option>
                                            <option value="IAB2-15">Mororcycles</option>
                                            <option value="IAB2-16">Off-Road Vehicles</option>
                                            <option value="IAB2-17">Performance Vehicles</option>
                                            <option value="IAB2-18">Pickup</option>
                                            <option value="IAB2-19">Road-Side Assistance</option>
                                            <option value="IAB2-20">Sedan</option>
                                            <option value="IAB2-21">Trucks & Accessories</option>
                                            <option value="IAB2-22">Vintage Cars</option>
                                            <option value="IAB2-23">Wagon</option>
                                            <option value="IAB3">Business (All)</option>
                                            <option value="IAB3-1">Advertising</option>
                                            <option value="IAB3-2">Agriculture</option>
                                            <option value="IAB3-3">Biotech/Biomedical</option>
                                            <option value="IAB3-4">Business Software</option>
                                            <option value="IAB3-5">Construction</option>
                                            <option value="IAB3-6">Forestry</option>
                                            <option value="IAB3-7">Government</option>
                                            <option value="IAB3-8">Green Solutions</option>
                                            <option value="IAB3-9">Human Resources</option>
                                            <option value="IAB3-10">Logistics</option>
                                            <option value="IAB3-11">Marketing</option>
                                            <option value="IAB3-12">Metals</option>
                                            <option value="IAB4">Careers (All)</option>
                                            <option value="IAB4-1">Career Planning</option>
                                            <option value="IAB4-2">College</option>
                                            <option value="IAB4-3">Financial Aid</option>
                                            <option value="IAB4-4">Job Fairs</option>
                                            <option value="IAB4-5">Job Search</option>
                                            <option value="IAB4-6">Resume Writing/Advice</option>
                                            <option value="IAB4-7">Nursing</option>
                                            <option value="IAB4-8">Scholarships</option>
                                            <option value="IAB4-9">Telecommuting</option>
                                            <option value="IAB4-10">U.S. Military</option>
                                            <option value="IAB4-11">Career Advice</option>
                                            <option value="IAB5">Education (All)</option>
                                            <option value="IAB5-1">7-12 Education</option>
                                            <option value="IAB5-2">Adult Education</option>
                                            <option value="IAB5-3">Art History</option>
                                            <option value="IAB5-4">Colledge Administration</option>
                                            <option value="IAB5-5">College Life</option>
                                            <option value="IAB5-6">Distance Learning</option>
                                            <option value="IAB5-7">English as a 2nd Language</option>
                                            <option value="IAB5-8">Language Learning</option>
                                            <option value="IAB5-9">Graduate School</option>
                                            <option value="IAB5-10">Homeschooling</option>
                                            <option value="IAB5-11">Homework/Study Tips</option>
                                            <option value="IAB5-12">K-6 Educators</option>
                                            <option value="IAB5-13">Private School</option>
                                            <option value="IAB5-14">Special Education</option>
                                            <option value="IAB5-15">Studying Business</option>
                                            <option value="IAB6">Family & Parenting (All)</option>
                                            <option value="IAB6-1">Adoption</option>
                                            <option value="IAB6-2">Babies & Toddlers</option>
                                            <option value="IAB6-3">Daycare/Pre School</option>
                                            <option value="IAB6-4">Family Internet</option>
                                            <option value="IAB6-5">Parenting – K-6 Kids</option>
                                            <option value="IAB6-6">Parenting teens</option>
                                            <option value="IAB6-7">Pregnancy</option>
                                            <option value="IAB6-8">Special Needs Kids</option>
                                            <option value="IAB6-9">Eldercare</option>
                                            <option value="IAB7">Health & Fitness (All)</option>
                                            <option value="IAB7-1">Exercise</option>
                                            <option value="IAB7-2">A.D.D.</option>
                                            <option value="IAB7-3">AIDS/HIV</option>
                                            <option value="IAB7-4">Allergies</option>
                                            <option value="IAB7-5">Alternative Medicine</option>
                                            <option value="IAB7-6">Arthritis</option>
                                            <option value="IAB7-7">Asthma</option>
                                            <option value="IAB7-8">Autism/PDD</option>
                                            <option value="IAB7-9">Bipolar Disorder</option>
                                            <option value="IAB7-10">Brain Tumor</option>
                                            <option value="IAB7-11">Cancer</option>
                                            <option value="IAB7-12">Cholesterol</option>
                                            <option value="IAB7-13">Chronic Fatigue Syndrome</option>
                                            <option value="IAB7-14">Chronic Pain</option>
                                            <option value="IAB7-15">Cold & Flu</option>
                                            <option value="IAB7-16">Deafness</option>
                                            <option value="IAB7-17">Dental Care</option>
                                            <option value="IAB7-18">Depression</option>
                                            <option value="IAB7-19">Dermatology</option>
                                            <option value="IAB7-20">Diabetes</option>
                                            <option value="IAB7-21">Epilepsy</option>
                                            <option value="IAB7-22">GERD/Acid Reflux</option>
                                            <option value="IAB7-23">Headaches/Migraines</option>
                                            <option value="IAB7-24">Heart Disease</option>
                                            <option value="IAB7-25">Herbs for Health</option>
                                            <option value="IAB7-26">Holistic Healing</option>
                                            <option value="IAB7-27">IBS/Crohn's Disease</option>
                                            <option value="IAB7-28">Incest/Abuse Support</option>
                                            <option value="IAB7-29">Incontinence</option>
                                            <option value="IAB7-30">Infertility</option>
                                            <option value="IAB7-31">Men's Health</option>
                                            <option value="IAB7-32">Nutrition</option>
                                            <option value="IAB7-33">Orthopedics</option>
                                            <option value="IAB7-34">Panic/Anxiety Disorders</option>
                                            <option value="IAB7-35">Pediatrics</option>
                                            <option value="IAB7-36">Physical Therapy</option>
                                            <option value="IAB7-37">Psychology/Psychiatry</option>
                                            <option value="IAB7-38">Senor Health</option>
                                            <option value="IAB7-39">Sexuality</option>
                                            <option value="IAB7-40">Sleep Disorders</option>
                                            <option value="IAB7-41">Smoking Cessation</option>
                                            <option value="IAB7-42">Substance Abuse</option>
                                            <option value="IAB7-43">Thyroid Disease</option>
                                            <option value="IAB7-44">Weight Loss</option>
                                            <option value="IAB7-45">Women's Health</option>
                                            <option value="IAB8">Food & Drink (All)</option>
                                            <option value="IAB8-1">American Cuisine</option>
                                            <option value="IAB8-2">Barbecues & Grilling</option>
                                            <option value="IAB8-3">Cajun/Creole</option>
                                            <option value="IAB8-4">Chinese Cuisine</option>
                                            <option value="IAB8-5">Cocktails/Beer</option>
                                            <option value="IAB8-6">Coffee/Tea</option>
                                            <option value="IAB8-7">Cuisine-Specific</option>
                                            <option value="IAB8-8">Desserts & Baking</option>
                                            <option value="IAB8-9">Dining Out</option>
                                            <option value="IAB8-10">Food Allergies</option>
                                            <option value="IAB8-11">French Cuisine</option>
                                            <option value="IAB8-12">Health/Lowfat Cooking</option>
                                            <option value="IAB8-13">Italian Cuisine</option>
                                            <option value="IAB8-14">Japanese Cuisine</option>
                                            <option value="IAB8-15">Mexican Cuisine</option>
                                            <option value="IAB8-16">Vegan</option>
                                            <option value="IAB8-17">Vegetarian</option>
                                            <option value="IAB8-18">Wine</option>
                                            <option value="IAB9">Hobbies & Interests (All)</option>
                                            <option value="IAB9-1">Art/Technology</option>
                                            <option value="IAB9-2">Arts & Crafts</option>
                                            <option value="IAB9-3">Beadwork</option>
                                            <option value="IAB9-4">Birdwatching</option>
                                            <option value="IAB9-5">Board Games/Puzzles</option>
                                            <option value="IAB9-6">Candle & Soap Making</option>
                                            <option value="IAB9-7">Card Games</option>
                                            <option value="IAB9-8">Chess</option>
                                            <option value="IAB9-9">Cigars</option>
                                            <option value="IAB9-10">Collecting</option>
                                            <option value="IAB9-11">Comic Books</option>
                                            <option value="IAB9-12">Drawing/Sketching</option>
                                            <option value="IAB9-13">Freelance Writing</option>
                                            <option value="IAB9-14">Genealogy</option>
                                            <option value="IAB9-15">Getting Published</option>
                                            <option value="IAB9-16">Guitar</option>
                                            <option value="IAB9-17">Home Recording</option>
                                            <option value="IAB9-18">Investors & Patents</option>
                                            <option value="IAB9-19">Jewelry Making</option>
                                            <option value="IAB9-20">Magic & Illusion</option>
                                            <option value="IAB9-21">Needlework</option>
                                            <option value="IAB9-22">Painting</option>
                                            <option value="IAB9-23">Photography</option>
                                            <option value="IAB9-24">Radio</option>
                                            <option value="IAB9-25">Roleplaying Games</option>
                                            <option value="IAB9-26">Sci-Fi & Fantasy</option>
                                            <option value="IAB9-27">Scrapbooking</option>
                                            <option value="IAB9-28">Screenwriting</option>
                                            <option value="IAB9-29">Stamps & Coins</option>
                                            <option value="IAB9-30">Video & Computer Games</option>
                                            <option value="IAB9-31">Woodworking</option>
                                            <option value="IAB10">Home & Garden (All)</option>
                                            <option value="IAB10-1">Appliances</option>
                                            <option value="IAB10-2">Entertaining</option>
                                            <option value="IAB10-3">Environmental Safety</option>
                                            <option value="IAB10-4">Gardening</option>
                                            <option value="IAB10-5">Home Repair</option>
                                            <option value="IAB10-6">Home Theater</option>
                                            <option value="IAB10-7">Interior Decorating</option>
                                            <option value="IAB10-8">Landscaping</option>
                                            <option value="IAB10-9">Remodeling & Construction</option>
                                            <option value="IAB11">Law, Gov't & Politics (All)</option>
                                            <option value="IAB11-1">Immigration</option>
                                            <option value="IAB11-2">Legal Issues</option>
                                            <option value="IAB11-3">U.S. Government Resources</option>
                                            <option value="IAB11-4">Politics</option>
                                            <option value="IAB11-5">Commentary</option>
                                            <option value="IAB12">News (All)</option>
                                            <option value="IAB12-1">International News</option>
                                            <option value="IAB12-2">National News</option>
                                            <option value="IAB12-3">Local News</option>
                                            <option value="IAB13">Personal Finance (All)</option>
                                            <option value="IAB13-1">Beginning Investing</option>
                                            <option value="IAB13-2">Credit/Debt & Loans</option>
                                            <option value="IAB13-3">Financial News</option>
                                            <option value="IAB13-4">Financial Planning</option>
                                            <option value="IAB13-5">Hedge Fund</option>
                                            <option value="IAB13-6">Insurance</option>
                                            <option value="IAB13-7">Investing</option>
                                            <option value="IAB13-8">Mutual Funds</option>
                                            <option value="IAB13-9">Options</option>
                                            <option value="IAB13-10">Retirement Planning</option>
                                            <option value="IAB13-11">Stocks</option>
                                            <option value="IAB13-12">Tax Planning</option>
                                            <option value="IAB14">Society (All)</option>
                                            <option value="IAB14-1">Dating</option>
                                            <option value="IAB14-2">Divorce Support</option>
                                            <option value="IAB14-3">Gay Life</option>
                                            <option value="IAB14-4">Marriage</option>
                                            <option value="IAB14-5">Senior Living</option>
                                            <option value="IAB14-6">Teens</option>
                                            <option value="IAB14-7">Weddings</option>
                                            <option value="IAB14-8">Ethnic Specific</option>
                                            <option value="IAB15">Science (All)</option>
                                            <option value="IAB15-1">Astrology</option>
                                            <option value="IAB15-2">Biology</option>
                                            <option value="IAB15-3">Chemistry</option>
                                            <option value="IAB15-4">Geology</option>
                                            <option value="IAB15-5">Paranormal Phenomena</option>
                                            <option value="IAB15-6">Physics</option>
                                            <option value="IAB15-7">Space/Astronomy</option>
                                            <option value="IAB15-8">Geography</option>
                                            <option value="IAB15-9">Botany</option>
                                            <option value="IAB15-10">Weather</option>
                                            <option value="IAB16">Pets (All)</option>
                                            <option value="IAB16-1">Aquariums</option>
                                            <option value="IAB16-2">Birds</option>
                                            <option value="IAB16-3">Cats</option>
                                            <option value="IAB16-4">Dogs</option>
                                            <option value="IAB16-5">Large Animals</option>
                                            <option value="IAB16-6">Reptiles</option>
                                            <option value="IAB16-7">Veterinary Medicine</option>
                                            <option value="IAB17">Sports (All)</option>
                                            <option value="IAB17-1">Auto Racing</option>
                                            <option value="IAB17-2">Baseball</option>
                                            <option value="IAB17-3">Bicycling</option>
                                            <option value="IAB17-4">Bodybuilding</option>
                                            <option value="IAB17-5">Boxing</option>
                                            <option value="IAB17-6">Canoeing/Kayaking</option>
                                            <option value="IAB17-7">Cheerleading</option>
                                            <option value="IAB17-8">Climbing</option>
                                            <option value="IAB17-9">Cricket</option>
                                            <option value="IAB17-10">Figure Skating</option>
                                            <option value="IAB17-11">Fly Fishing</option>
                                            <option value="IAB17-12">Football</option>
                                            <option value="IAB17-13">Freshwater Fishing</option>
                                            <option value="IAB17-14">Game & Fish</option>
                                            <option value="IAB17-15">Golf</option>
                                            <option value="IAB17-16">Horse Racing</option>
                                            <option value="IAB17-17">Horses</option>
                                            <option value="IAB17-18">Hunting/Shooting</option>
                                            <option value="IAB17-19">Inline Skating</option>
                                            <option value="IAB17-20">Martial Arts</option>
                                            <option value="IAB17-21">Mountain Biking</option>
                                            <option value="IAB17-22">NASCAR Racing</option>
                                            <option value="IAB17-23">Olympics</option>
                                            <option value="IAB17-24">Paintball</option>
                                            <option value="IAB17-25">Power & Motorcycles</option>
                                            <option value="IAB17-26">Pro Basketball</option>
                                            <option value="IAB17-27">Pro Ice Hockey</option>
                                            <option value="IAB17-28">Rodeo</option>
                                            <option value="IAB17-29">Rugby</option>
                                            <option value="IAB17-30">Running/Jogging</option>
                                            <option value="IAB17-31">Sailing</option>
                                            <option value="IAB17-32">Saltwater Fishing</option>
                                            <option value="IAB17-33">Scuba Diving</option>
                                            <option value="IAB17-34">Skateboarding</option>
                                            <option value="IAB17-35">Skiing</option>
                                            <option value="IAB17-36">Snowboarding</option>
                                            <option value="IAB17-37">Surfing/Bodyboarding</option>
                                            <option value="IAB17-38">Swimming</option>
                                            <option value="IAB17-39">Table Tennis/Ping-Pong</option>
                                            <option value="IAB17-40">Tennis</option>
                                            <option value="IAB17-41">Volleyball</option>
                                            <option value="IAB17-42">Walking</option>
                                            <option value="IAB17-43">Waterski/Wakeboard</option>
                                            <option value="IAB17-44">World Soccer</option>
                                            <option value="IAB18">Style & Fashion (All)</option>
                                            <option value="IAB18-1">Beauty</option>
                                            <option value="IAB18-2">Body Art</option>
                                            <option value="IAB18-3">Fashion</option>
                                            <option value="IAB18-4">Jewelry</option>
                                            <option value="IAB18-5">Clothing</option>
                                            <option value="IAB18-6">Accessories</option>
                                            <option value="IAB19">Technology & Computing (All)</option>
                                            <option value="IAB19-1">3-D Graphics</option>
                                            <option value="IAB19-2">Animation</option>
                                            <option value="IAB19-3">Antivirus Software</option>
                                            <option value="IAB19-4">C/C++</option>
                                            <option value="IAB19-5">Cameras & Camcorders</option>
                                            <option value="IAB19-6">Cell Phones</option>
                                            <option value="IAB19-7">Computer Certification</option>
                                            <option value="IAB19-8">Computer Networking</option>
                                            <option value="IAB19-9">Computer Peripherals</option>
                                            <option value="IAB19-10">Computer Reviews</option>
                                            <option value="IAB19-11">Data Centers</option>
                                            <option value="IAB19-12">Databases</option>
                                            <option value="IAB19-13">Desktop Publishing</option>
                                            <option value="IAB19-14">Desktop Video</option>
                                            <option value="IAB19-15">Email</option>
                                            <option value="IAB19-16">Graphics Software</option>
                                            <option value="IAB19-17">Home Video/DVD</option>
                                            <option value="IAB19-18">Internet Technology</option>
                                            <option value="IAB19-19">Java</option>
                                            <option value="IAB19-20">JavaScript</option>
                                            <option value="IAB19-21">Mac Support</option>
                                            <option value="IAB19-22">MP3/MIDI</option>
                                            <option value="IAB19-23">Net Conferencing</option>
                                            <option value="IAB19-24">Net for Beginners</option>
                                            <option value="IAB19-25">Network Security</option>
                                            <option value="IAB19-26">Palmtops/PDAs</option>
                                            <option value="IAB19-27">PC Support</option>
                                            <option value="IAB19-28">Portable</option>
                                            <option value="IAB19-29">Entertainment</option>
                                            <option value="IAB19-30">Shareware/Freeware</option>
                                            <option value="IAB19-31">Unix</option>
                                            <option value="IAB19-32">Visual Basic</option>
                                            <option value="IAB19-33">Web Clip Art</option>
                                            <option value="IAB19-34">Web Design/HTML</option>
                                            <option value="IAB19-35">Web Search</option>
                                            <option value="IAB19-36">Windows</option>
                                            <option value="IAB20">Travel (All)</option>
                                            <option value="IAB20-1">Adventure Travel</option>
                                            <option value="IAB20-2">Africa</option>
                                            <option value="IAB20-3">Air Travel</option>
                                            <option value="IAB20-4">Australia & New Zealand</option>
                                            <option value="IAB20-5">Bed & Breakfasts</option>
                                            <option value="IAB20-6">Budget Travel</option>
                                            <option value="IAB20-7">Business Travel</option>
                                            <option value="IAB20-8">By US Locale</option>
                                            <option value="IAB20-9">Camping</option>
                                            <option value="IAB20-10">Canada</option>
                                            <option value="IAB20-11">Caribbean</option>
                                            <option value="IAB20-12">Cruises</option>
                                            <option value="IAB20-13">Eastern Europe</option>
                                            <option value="IAB20-14">Europe</option>
                                            <option value="IAB20-15">France</option>
                                            <option value="IAB20-16">Greece</option>
                                            <option value="IAB20-17">Honeymoons/Getaways</option>
                                            <option value="IAB20-18">Hotels</option>
                                            <option value="IAB20-19">Italy</option>
                                            <option value="IAB20-20">Japan</option>
                                            <option value="IAB20-21">Mexico & Central America</option>
                                            <option value="IAB20-22">National Parks</option>
                                            <option value="IAB20-23">South America</option>
                                            <option value="IAB20-24">Spas</option>
                                            <option value="IAB20-25">Theme Parks</option>
                                            <option value="IAB20-26">Traveling with Kids</option>
                                            <option value="IAB20-27">United Kingdom</option>
                                            <option value="IAB21">Real Estate (All)</option>
                                            <option value="IAB21-1">Apartments</option>
                                            <option value="IAB21-2">Architects</option>
                                            <option value="IAB21-3">Buying/Selling Homes</option>
                                            <option value="IAB22">Shopping (All)</option>
                                            <option value="IAB22-1">Contests & Freebies</option>
                                            <option value="IAB22-2">Couponing</option>
                                            <option value="IAB22-3">Comparison</option>
                                            <option value="IAB22-4">Engines</option>
                                            <option value="IAB23">Religion & Spirituality (All)</option>
                                            <option value="IAB23-1">Alternative Religions</option>
                                            <option value="IAB23-2">Atheism/Agnosticism</option>
                                            <option value="IAB23-3">Buddhism</option>
                                            <option value="IAB23-4">Catholicism</option>
                                            <option value="IAB23-5">Christianity</option>
                                            <option value="IAB23-6">Hinduism</option>
                                            <option value="IAB23-7">Islam</option>
                                            <option value="IAB23-8">Judaism</option>
                                            <option value="IAB23-9">Latter-Day Saints</option>
                                            <option value="IAB23-10">Pagan/Wiccan</option>
                                            <option value="IAB24">Uncategorized (All)</option>
                                            <option value="IAB25">Non-Standard Content (All)</option>
                                            <option value="IAB25-1">Unmoderated UGC</option>
                                            <option value="IAB25-2">Extreme Graphic/Explicit Violence</option>
                                            <option value="IAB25-3">Pornography</option>
                                            <option value="IAB25-4">Profane Content</option>
                                            <option value="IAB25-5">Hate Content</option>
                                            <option value="IAB25-6">Under Construction</option>
                                            <option value="IAB25-7">Incentivized</option>
                                        </select>
                                    </div>
                                    <input class="iab-cat-tags" type="hidden" name="<?php echo self::$opt_vi_js_settings ?>[iabCategory]" id="<?php echo self::$opt_vi_js_settings ?>[iabCategory]" value="<?php echo esc_attr($item[self::$opt_vi_js_settings]['iabCategory']) ?>" />
                                    <br>
                                    <br>
                                    <p><strong>Your Selected Categories:</strong></p>
                                    <div class="iab-cat-tags-display"></div>
                                </td>
                            </tr>
                            <tr class="form-field <?php echo empty($item[self::$opt_vi_js_settings]['keywords']) ? ' hidden ' : '' ?>">
                                <th valign="top" scope="row">
                                    <label for="<?php echo self::$opt_vi_js_settings ?>[keywords]">Keywords</label>
                                    <small>Enter a few keywords that describe topics your visitors are likely to be interested in. <strong>Separate by commas.</strong>
                                        Tip: Try to avoid terms that have multiple meanings; e.g., just the word "record" can refer to music records and even sports records.</small>
                                </th>
                                <td>
                                    <input id="<?php echo self::$opt_vi_js_settings ?>[keywords]" name="<?php echo self::$opt_vi_js_settings ?>[keywords]" value="<?php echo esc_attr($item[self::$opt_vi_js_settings]['keywords']) ?>"
                                           type="text" maxlength="200" placeholder="Example: cooking, baking, food, recipes, kitchen">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>


                <section class="pattern" id="jumpappearance">
                    <h2><span class="vi-num">2</span> Appearance</h2>

                    <p>Customize your ad unit's visual appearance below.</p>
                    <div class="vi-story-demo">

                        <h3>
                            Appearance Demo
                        </h3>
                        <div class="vi-story-demo--box">
                            <div class="vi-story-demo--screen">
                                <span>AD + CONTENT</span>
                            </div>
                            <div class="vi-story-demo--info">
                                <div class="vi-story-demo--title">
                                    Example vi Story Title Text
                                </div>
                                <div class="vi-story-demo--featured">
                                    <span>featured by</span> <img src="<?php echo plugins_url(self::$folder_name . '/images/vi_logo.svg') ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table cellspacing="2" cellpadding="5" class="form-table form-table--vi-appearance">
                        <tbody>


                            <tr class="form-field">
                                <th valign="top" scope="row">
                                    <label for="<?php echo self::$opt_vi_js_settings ?>[backgroundColor]">Background Color</label>
                                    <small>Select a background color that will enable the ad to blend in with your site's theme.</small>
                                </th>
                                <td>
                                    <input id="<?php echo self::$opt_vi_js_settings ?>[backgroundColor]" name="<?php echo self::$opt_vi_js_settings ?>[backgroundColor]" value="<?php echo esc_attr($item[self::$opt_vi_js_settings]['backgroundColor']) ?>"
                                           type="text" maxlength="7" class="ytvi-color-field">
                                </td>
                            </tr>
                            <tr class="form-field">
                                <th valign="top" scope="row">
                                    <label for="<?php echo self::$opt_vi_js_settings ?>[textColor]">Text Color</label>
                                    <small>Select a text color that will enable the ad to blend in with your site's theme.</small>
                                </th>
                                <td>
                                    <input id="<?php echo self::$opt_vi_js_settings ?>[textColor]" name="<?php echo self::$opt_vi_js_settings ?>[textColor]" value="<?php echo esc_attr($item[self::$opt_vi_js_settings]['textColor']) ?>"
                                           type="text" maxlength="7" class="ytvi-color-field">
                                </td>
                            </tr>
                            <tr class="form-field">
                                <th valign="top" scope="row">
                                    <label for="<?php echo self::$opt_vi_js_settings ?>[font]">Font Family</label>
                                    <small>Select the font that matches your site's theme the most.</small>
                                </th>
                                <td>
                                    <select name="<?php echo self::$opt_vi_js_settings ?>[font]" id="<?php echo self::$opt_vi_js_settings ?>[font]" required>
                                        <?php
                                        $all_fonts = array(
                                            'Arial',
                                            'Arial Black',
                                            'Comic Sans MS',
                                            'Courier New',
                                            'Georgia',
                                            'Impact',
                                            'Lucida Console',
                                            'Lucida Sans Unicode',
                                            'Palatino Linotype',
                                            'Tahoma',
                                            'Times New Roman',
                                            'Trebuchet MS',
                                            'Verdana'
                                        );
                                        foreach ($all_fonts as $font)
                                        {
                                            ?>
                                            <option <?php selected($item[self::$opt_vi_js_settings]['font'], $font) ?> value="<?php echo esc_attr($font) ?>"><?php echo esc_attr($font) ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr class="form-field">
                                <th valign="top" scope="row">
                                    <label for="<?php echo self::$opt_vi_js_settings ?>[fontSize]">Font Size</label>
                                    <small>Select the font size for your ad.</small>
                                </th>
                                <td>
                                    <select name="<?php echo self::$opt_vi_js_settings ?>[fontSize]" id="<?php echo self::$opt_vi_js_settings ?>[fontSize]" required>
                                        <?php
                                        $all_font_sizes = array(
                                            8, 9, 10, 11, 12, 14, 16, 18, 20, 22, 24, 26, 28, 36
                                        );
                                        foreach ($all_font_sizes as $fsize)
                                        {
                                            ?>
                                            <option <?php selected($item[self::$opt_vi_js_settings]['fontSize'], $fsize) ?> value="<?php echo esc_attr($fsize) ?>"><?php echo esc_attr($fsize . 'px') ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>

                            <?php
                            if (!empty(self::$alloptions[self::$opt_vi_endpoints]->languages))
                            {
                                ?>
                                <tr class="form-field">
                                    <th valign="top" scope="row">
                                        <label for="<?php echo self::$opt_vi_js_settings ?>[language]">Language</label>
                                        <small>Select from the available list of languages.</small>
                                    </th>
                                    <td>
                                        <select name="<?php echo self::$opt_vi_js_settings ?>[language]" id="<?php echo self::$opt_vi_js_settings ?>[language]" required>
                                            <?php
                                            $all_languages = array();
                                            foreach (self::$alloptions[self::$opt_vi_endpoints]->languages as $lang)
                                            {
                                                $l = get_object_vars($lang);
                                                $all_languages = $l + $all_languages;
                                            }
                                            foreach ($all_languages as $lang_key => $lang_val)
                                            {
                                                ?>
                                                <option <?php selected($item[self::$opt_vi_js_settings]['language'], $lang_key) ?> value="<?php echo esc_attr($lang_key) ?>"><?php echo esc_attr($lang_val) ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="clearboth"></div>
                    <h3>Sizing Tips</h3>
                    <p>The video ad's player will be as large as the container it’s in. If you’d like to change the default size to something smaller, you’ll just need to add some CSS to your website's theme as follows:</p>
                    <ol class="list-ol">
                        <li>
                            You'll be using your site's theme customizer. In the WordPress admin menu on the left, go to <em>Appearance > <a target="_blank" href="<?php echo admin_url('customize.php?return=') . urlencode(admin_url()) ?>">Customize</a></em>.
                        </li>
                        <li>
                            On the customizer page, scroll down in the left menu to "Additional CSS" and click on it.
                        </li>
                        <li>
                            You'll have a textbox to paste in the following CSS (change 480 to your desired max width in pixels):
                            <br><br>
                            <div class="code pre"><?php echo ".ytvi-story-container {
max-width: 480px;
margin: 0 auto;
}" ?></div>
                        </li>
                        <li>
                            When done, click on the "Publish" button at the top to save your change, and then the X to close the theme customizer.
                        </li>                        
                    </ol>
                </section>


                <section class="pattern" id="jumpplacement">
                    <h2><span class="vi-num">3</span> Placement</h2>
                    <p>
                        You can choose to place your ad <strong>automatically</strong>, or <strong>manually</strong> using a shortcode, or in a specific spot in your <strong>theme</strong> code. Each method is explained below.
                    </p>
                    <p>
                        After you finish choosing your placement preferences below, 1) Click on "Save Changes", and 2) <strong class="vi-red">turn on</strong> the ads using the button at the top of this screen.
                    </p>
                    <p>
                        <strong>Note: The ad player will auto-fit to its container when loaded.</strong>
                    </p>

                    <h3>Automatic: Top or Bottom</h3>
                    <p>
                        You can have your ad automatically placed at the top or bottom of your post content--right above your first paragraph (top), or right under your last paragraph (bottom).
                        For optimal revenue, we recommend using the "Top" option:
                    </p>
                    <ul>
                        <li><label><input type="radio" name="<?php echo self::$opt_vi_js_position ?>" value="top" <?php checked($item[self::$opt_vi_js_position] == 'top') ?> /> Top (recommended for highest fill rate)</label></li>
                        <li><label><input type="radio" name="<?php echo self::$opt_vi_js_position ?>" value="bottom" <?php checked($item[self::$opt_vi_js_position] == 'bottom') ?> /> Bottom</label></li>
                    </ul>
                    <p>
                        Next, just check which types of posts you desire to have the ad appear, and the plugin will take care of the rest. 
                        You'll start seeing the ads on your pages after pressing the "Save Changes" button on the bottom right, and turning "ON" vi ads with the top right button.
                    </p>

                    <ul>
                        <?php
                        $all_post_types = get_post_types(array('public' => true), 'objects');
                        foreach ($all_post_types as $pt)
                        {
                            ?>
                            <li><label><input type="checkbox" name="<?php echo self::$opt_vi_js_posttypes ?>[]" value="<?php echo esc_attr($pt->name); ?>" <?php checked(in_array($pt->name, $item[self::$opt_vi_js_posttypes])) ?> /> <?php echo esc_html($pt->label); ?></label></li>
                            <?php
                        }
                        ?>                    
                    </ul>
                    <p>
                        Note that only one ad can appear on a page, but if you'd like more control of exactly <em>where</em> it's placed, see the "Manual" or "Theme Code" directions in the next sections.
                    </p>
                    <h3>Manual: Shortcode or Gutenberg Block</h3>
                    <p>
                        If you didn't select any of the automatic options above, you can manually insert your ad in text widgets, and in specific posts or pages. 
                        See the below screenshot to find the button you can use to manually embed the ad code.
                        (or, use this shortcode directly: <code>[embed-vi-ad]</code>).
                        <strong>We're quite happy to help you if you aren't sure what to do</strong>, especially due the newness of the new Gutenberg editor.  <strong>Just email us at ext@embedplus.com</strong>
                    </p>
                    <p>
                        By the way, if you did make an automatic selection above, do not make any manual insertions. Skip the remaining options on this page, since only one ad code/script is allowed per page and the above has got you covered. 
                    </p>
                    <img class="ss-vi-wizbutton" src="<?php echo plugins_url(self::$folder_name . '/images/ss-vi-wizbutton.png') . '?ver=' . self::$version; ?>"/>                    

                    <h3>Theme Code (advanced)</h3>
                    <p>You can also position the ad directly in your theme code. Copy the PHP code below and paste it where you would like it to appear in your theme.</p>
                    <p><code>echo do_shortcode("[embed-vi-ad]");</code></p>
                </section>


                <section class="pattern" id="jumpadstxt">
                    <div class="adstxt-help">
                        <img src="<?php echo plugins_url(self::$folder_name . '/images/adstxt-help.png') . '?ver=' . self::$version; ?>"/>
                        <p>
                            Trouble with your ads.txt verification? Contact support at <strong><a href="mailto:ext@embedplus.com">ext@embedplus.com</a></strong>
                        </p>                        
                    </div>
                    <h2><span class="vi-num">5</span> Ads.txt Verification</h2>
                    <p>
                        In order for your ads to start generating revenue, verify your ads.txt file.
                    </p>
                    <div class="adstxt-verify-message">

                    </div>

                    <p>
                        Are you also running Google Adsense ads on your site?  If so, also add the following line which is <a href="https://support.google.com/adsense/answer/7532444?hl=en" target="_blank">recommended by Google</a> for ads.txt files (replace the <code>0000000000000000</code> with your actual publisher ID as provided by Google).
                    </p>
                    <p>
                        <code class="adstxt-block">google.com, pub-0000000000000000, DIRECT, f08c47fec0942fa0</code>
                    </p>                    
                </section>


                <section class="pattern" id="jumpperformance">
                    <h2><span class="vi-num">6</span> Revenue Reporting</h2>
                    <div class="vi-report">
                        <div class="vi-total-earnings">
                            <h3>Total Earnings</h3>
                            <div class="vi-total-earnings-num"></div>
                        </div>
                        <div class="vi-report-graph">
                            <div class="vi-report-canvas-box">
                                <canvas id="vi-report-canvas"></canvas>
                            </div>
                        </div>
                        <div class="clearboth"></div>
                        <p>
                            To view more detailed reports on your ad's performance and stats,
                            <a class="button-secondary align-middle" target="_blank" href="<?php echo esc_url(trailingslashit(self::$alloptions[self::$opt_vi_endpoints]->dashboardURL) . 'scar/' . self::$alloptions[self::$opt_vi_token]); ?>">click here</a> 
                            to automatically login to your vi account. Then click on the "Reports" tab as shown below.
                        </p>
                        <p>
                            <img class="ss-vi-img" src="<?php echo plugins_url(self::$folder_name . '/images/ss-vi-dashreports.png'); ?>"/>
                        </p>

                    </div>
                    <div class="vi-report-error hide">
                        <div class="vi-total-earnings-error">
                            <h3>Total Earnings</h3>
                            <div class="vi-total-earnings-num-error">No Data</div>
                        </div>
                        <div class="vi-report-graph-error">
                            <h3>Monthly Earnings Graph</h3>
                            <div class="vi-report-canvas-box-error">
                                <br>
                                <br>
                                No Data
                            </div>
                        </div>
                        <div class="clearboth"></div>
                        <p>
                            Trouble showing the reports? Please try again later, or contact support at <strong><a href="mailto:ext@embedplus.com">ext@embedplus.com</a></strong>
                        </p>
                    </div>

                </section>

                <section class="pattern" id="jumprevenue">
                    <h2><span class="vi-num">7</span> Profile Settings</h2>
                    <p>
                        To enter where you would like to receive your payments,
                        <a class="button-secondary align-middle" target="_blank" href="<?php echo esc_url(trailingslashit(self::$alloptions[self::$opt_vi_endpoints]->dashboardURL) . 'scar/' . self::$alloptions[self::$opt_vi_token]); ?>">click here</a> 
                        to automatically login to your dashboard on vi.ai. Your deposit options, which include bank transfer or PayPal, are found in the "Settings" tab:
                    </p>
                    <p>
                        <img class="ss-vi-img" src="<?php echo plugins_url(self::$folder_name . '/images/ss-vi-dashrevenue.png'); ?>"/>
                    </p>
                    <p>
                        Trouble automatically logging in? <a target="_blank" href="<?php echo esc_url(self::$alloptions[self::$opt_vi_endpoints]->dashboardURL); ?>">Manually login here</a> using the email you signed up with.
                    </p>
                </section>

                <section class="pattern" id="jumpviprivacy">
                    <h2>Privacy</h2>
                    <p>
                        <label>
                            <input type="checkbox" id="<?php echo self::$opt_vi_show_gdpr_authorization ?>" name="<?php echo self::$opt_vi_show_gdpr_authorization ?>" value="1" <?php checked($item[self::$opt_vi_show_gdpr_authorization] == 1) ?> />
                            <strong>Show Privacy/GDPR Popup</strong> - Use the <a href="https://advertisingconsent.eu/" target="_blank">IAB approved</a> method to gain consent from your EU visitors before video intelligence cookies or ad content is loaded.
                        </label>
                    </p>
                    <p class="opt_<?php echo self::$opt_vi_show_privacy_button ?>" style="<?php echo $item[self::$opt_vi_show_gdpr_authorization] == 1 ? '' : 'display: none;' ?>">
                        <label>
                            <input type="checkbox" name="<?php echo self::$opt_vi_show_privacy_button ?>" value="1" <?php checked($item[self::$opt_vi_show_privacy_button] == 1) ?> />
                            <strong>Show Privacy Settings Button</strong> - Checking this will also display a floating button ("vi Privacy Settings") on pages where vi ads are shown. Users can click on it to reevaluate consent without to having to manually manage cookies from their browser settings.
                        </label>
                    </p>
                </section>

                <section class="pattern" id="jumpfaq">
                    <h2>FAQs</h2>

                    <ul class="list-ul">
                        <li>
                            <h3>What kind of video ad unit am I embedding?</h3>
                            <p>It's a unique type of ad unit called a "vi story," which is essentially a video ad wrapped with engaging content related to your website. <a target="_blank" href="<?php echo esc_url(self::$alloptions[self::$opt_vi_endpoints]->demoPageURL); ?>">View a demo here</a> (be sure to turn off ad-blocker to preview the demo).</p>
                            <p>Your ad unit will display content from quality sources like:</p>
                            <p class="vi-ad-source-row">
                                <img class="vi-ad-source" src="<?php echo plugins_url(self::$folder_name . '/images/vi-source-billboard.png') ?>"/>
                                <img class="vi-ad-source" src="<?php echo plugins_url(self::$folder_name . '/images/vi-source-nowthis.png') ?>"/>
                                <img class="vi-ad-source" src="<?php echo plugins_url(self::$folder_name . '/images/vi-source-bonnier.png') ?>"/>
                                <img class="vi-ad-source" src="<?php echo plugins_url(self::$folder_name . '/images/vi-source-cbc.png') ?>"/>
                                <img class="vi-ad-source" src="<?php echo plugins_url(self::$folder_name . '/images/vi-source-thetelegraph.png') ?>"/>
                                <img class="vi-ad-source" src="<?php echo plugins_url(self::$folder_name . '/images/vi-source-itn.png') ?>"/>
                            </p>
                        </li>
                        <li>
                            <h3>Why embed an ad unit that also includes a story, rather than just an ad?</h3>
                            <p>Simply put, advertisers pay more for video advertising when it's matched with video content. With both, you'll increase your visitors' time-on-site and even command up to 10x higher CPM than regular display advertising.</p>
                        </li>
                        <li>
                            <h3>What is my ads.txt file for?</h3>
                            <p>
                                This is an industry standard (IAB-approved) text file that aims to prevent unauthorized inventory sales. 
                                Basically, it helps increase your revenue by verifying to ad buyers that you have a valid site that they are buying ad space for.
                            </p>
                        </li>
                        <li>
                            <h3>What is the best place to put my ad?</h3>
                            <p>
                                To optimize your revenue, we strongly recommend embedding the ad "above the fold" when possible (lower placements tend to yield much less revenue). In general, the higher the placement, the better engagement and revenue.
                                The automatic placement options place the ad at the top of your content area for you, but keep this tip in mind whenever you manually embed the ad. 
                            </p>
                        </li>
                        <li>
                            <h3>Why are there no ads, even though I added the code (manually or automatically)?</h3>
                            <p>
                                It's likely that you've added more than one ad script/code to your pages.
                                Perhaps you selected the automatic placement on a post/page but also inserted a separate piece of code/script manually.
                                At this time, only one ad is allowed per page. If you insert more, then no ads might be visible.
                            </p>
                        </li>
                        <li>
                            <h3>Is this video intelligence (vi) monetization feature compatible with Google Adsense?</h3>
                            <p>
                                Yes.  Many publishers are in fact running both Adsense and vi at the same time.  Like vi, Google Adsense recommends that you have an  <a href="https://support.google.com/adsense/answer/7532444?hl=en" target="_blank">ads.txt in your root folder</a>.
                                Therefore, you should also include the following line in your ads.txt -- either before or after the lines you inserted for vi (replace the <code>0000000000000000</code> with the actual publisher ID provided by Google):
                            </p>
                            <p>
                                <code>google.com, pub-0000000000000000, DIRECT, f08c47fec0942fa0</code>
                            </p>
                        </li>
                        <li>
                            <h3>How do I resize the ad?</h3>
                            <p>The video ad's player will be as large as the container it’s in. If you’d like to change the default size to something smaller, you’ll just need to add some CSS to your website's theme as follows:</p>
                            <ol class="list-ol">
                                <li>
                                    You'll be using your site's theme customizer. In the WordPress admin menu on the left, go to <em>Appearance > <a target="_blank" href="<?php echo admin_url('customize.php?return=') . urlencode(admin_url()) ?>">Customize</a></em>.
                                </li>
                                <li>
                                    On the customizer page, scroll down in the left menu to "Additional CSS" and click on it.
                                </li>
                                <li>
                                    You'll have a textbox to paste in the following CSS (change 480 to your desired max width in pixels):
                                    <br><br>
                                    <div class="code pre"><?php echo ".ytvi-story-container {
max-width: 480px;
margin: 0 auto;
}" ?></div>
                                </li>
                                <li>
                                    When done, click on the "Publish" button at the top to save your change, and then the X to close the theme customizer.
                                </li>                        
                            </ol>
                        </li>
                        <li>
                            <h3>I don't want my ad to follow me as I scroll.</h3>
                            <p>
                                This is a feature that greatly increases your rate of revenue. If you would like to turn if off, please <a href="#jumpsupport">contact support</a>.
                            </p>
                        </li>
                        <li>
                            <h3>When will I start seeing ads within the vi story?</h3>
                            <p>
                                It can vary depending on which countries the bulk of your traffic is coming from. For most countries it takes about 1 to 2 days.  If you have questions, email us at ext@embedplus.com
                            </p>
                        </li>
                        <li>
                            <h3>How do I change the number of ads that are shown for each vi story I embed?</h3>
                            <p>
                                vi manages the maximum number of ads and time between them based on each publisher. This is to optimize the fill rates and monetization.  If you would like some custom settings, please <a href="#jumpsupport">contact support</a>.
                            </p>
                        </li>
                        <li>
                            <h3>Why am I seeing ads that do not match my site's topics?</h3>
                            <ol>
                                <li>Wait for 24 hours to give the video intelligence service time to learn more about your site.</li>
                                <li>Check each category and its subcategories to see if there is a better fit for your site’s topics than your initial selections.</li>
                                <li>If you're still not seeing well-matched ads, it’s likely that your site’s topics are very specific or they are based on categories in which vi.ai is still building inventory.  In the meantime, try and find other categories that you think will be of interest to your audience.</li>
                                <li>If all else fails, please <a href="#jumpsupport">contact support</a>.</li>
                            </ol>
                        </li>
                    </ul>
                    <p><strong>To see a comprehensive list of FAQs, <a target="_blank" href="https://www.vi.ai/publisherfaq/?aid=WP_embedplus&utm_source=Wordpress&utm_medium=WP_embedplus">please visit vi FAQs</a>.</strong></p>
                </section>

                <section class="pattern" id="jumpsupport">

                    <h2>Earnings & Payment Support</h2>
                    <p>
                        For issues on the advertising program, your earnings, and <img class="vi-logo-text" alt="vi: video intelligence" src="<?php echo plugins_url(self::$folder_name . '/images/vi_logo.svg') ?>"/>: please contact <strong><a href="mailto:ext@embedplus.com">ext@embedplus.com</a></strong>.
                    </p>
                </section>


                <div class="save-changes-follow"> <?php self::vi_save_changes_button(!empty($message)); ?> </div>
            </form>
        </div>
        <?php
    }

    public static function vi_monetize_title()
    {
        ?>
        Join over 40,000 publishers making money embedding high quality video ads
        <?php
    }

    public static function vi_admin_dashboard_pre()
    {
        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        ?>

        <div class="wrap wrap-vi wrap-vi-settings-pre">
            <?php
            //self::vi_monetize_title();
            if (self::vi_script_setup_done())
            {
                echo '<h1>';
                self::vi_print_toggle_button();
                echo '</h1>';
            }
            ?>

            <div class="vi-registration-box">
                <?php
                include_once(EPYTVI_INCLUDES_PATH . 'vi_registration_form.php');
                include_once(EPYTVI_INCLUDES_PATH . 'vi_login_success.php');
                ?>
            </div>
        </div>
        <?php
    }

    public static function vi_save_changes_button($submitted)
    {
        $button_label = 'Save Changes';
        if ($submitted)
        {
            $button_label = 'Changes Saved';
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ()
                {
                    setTimeout(function ()
                    {
                        jQuery('input.ytvi-admin-submit').val('Save Changes');
                    }, 3000);
                });

            </script>
            <?php
        }
        ?>
        <p class="submit">
            <input type="submit" name="Submit" class="button-primary ytvi-admin-submit" value="<?php _e($button_label) ?>" />
            <em>If you're using a separate caching plugin and you do not see your changes after saving, <strong class="orange">you need to reset your cache.</strong></em>
        </p>
        <?php
    }

    public static function vi_script_tag()
    {
        if (!self::$vi_script_tag_done && self::$alloptions[self::$opt_vi_active] && self::vi_script_setup_done())
        {
            if (stripos(self::$alloptions[self::$opt_vi_js_settings]['iabCategory'], ',') > 0 && self::vi_logged_in())
            {
                $last_category_update = strtotime(self::$alloptions[self::$opt_vi_last_category_update]);
                $last_category_update_plus = strtotime(self::$alloptions[self::$opt_vi_last_category_update] . ' + ' . self::$vi_last_category_update_interval);
                if ($last_category_update_plus < time())
                {
                    $success = self::vi_cache_js(self::$alloptions[self::$opt_vi_js_settings]);
                    if ($success !== true)
                    {
                        self::vi_token_expire();
                    }
                }
            }

            self::$vi_script_tag_done = true;
            $scriptTag = '<div class="ytvi-story-container" id="ytvi_story_container"><script class="ytvi-story-script" type="text/javascript">' .
                    self::$alloptions[self::$opt_vi_js_script] .
                    '</script></div>';
            return $scriptTag;
        }
        return '';
    }

    public static function vi_js_placement($content)
    {
        //$mainquery = is_main_query();

        if (!self::$vi_script_tag_done && self::$alloptions[self::$opt_vi_active] && self::vi_script_setup_done())
        {
            if (!empty(self::$alloptions[self::$opt_vi_js_posttypes]))
            {
                $singular = is_singular(self::$alloptions[self::$opt_vi_js_posttypes]);
                if ($singular && in_the_loop())
                {
                    return self::$alloptions[self::$opt_vi_js_position] == 'top' ? self::vi_script_tag() . $content : $content . self::vi_script_tag();
                }
            }
        }

        return $content;
    }

    public static function vi_js_shortcode($atts, $content = null)
    {
        return self::vi_script_tag();
    }

    public static function wp_insert_vi_api_is_eu()
    {
        $userIp = $_SERVER["REMOTE_ADDR"];
        if (defined('VI_EU_TEST'))
        {
            $userIp = '185.216.33.82'; // force EU for testing
        }
        $isEU = get_transient('wp_insert_vi_api_is_eu_' . $userIp);
        if ($isEU === false)
        {
            try
            {
                $response = wp_remote_get(
                        'http://gdpr-check.net/gdpr/is-eu?ip=' . $userIp, array('timeout' => 15)
                );
                if (!is_wp_error($response))
                {
                    if (200 == wp_remote_retrieve_response_code($response))
                    {
                        $responseBody = json_decode($response['body']);
                        if ((json_last_error() == JSON_ERROR_NONE))
                        {
                            if ((isset($responseBody->is_eu)) && ($responseBody->is_eu == '1'))
                            {
                                delete_transient('wp_insert_vi_api_is_eu_' . $userIp);
                                set_transient('wp_insert_vi_api_is_eu_' . $userIp, '1', WEEK_IN_SECONDS);
                                return true;
                            }
                            else
                            {
                                delete_transient('wp_insert_vi_api_is_eu_' . $userIp);
                                set_transient('wp_insert_vi_api_is_eu_' . $userIp, '0', WEEK_IN_SECONDS);
                                return false;
                            }
                        }
                        else
                        {
                            return false;
                        }
                    }
                    else
                    {
                        return false;
                    }
                }
            }
            catch (Exception $ex)
            {
                return false;
            }
        }
        else
        {
            if ($isEU == '1')
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    public static function wp_insert_vi_gdpr_popup_init()
    {
        if ((bool) self::$alloptions[self::$opt_vi_show_gdpr_authorization] || defined('VI_EU_TEST'))
        {
            add_action('init', array(self::class, 'wp_insert_vi_gdpr_data_init'));
            add_action('wp_enqueue_scripts', array(self::class, 'wp_insert_vi_gdpr_popup_wp_enqueue'));
            add_action('wp_footer', array(self::class, 'wp_insert_vi_gdpr_popup_wp_footer'));
        }
    }

    public static function wp_insert_vi_gdpr_popup_wp_enqueue()
    {
        wp_enqueue_style('wp_insert_vi_gdpr_css', plugins_url('styles/ytvi-gdpr' . self::$min . '.css', __FILE__), array(), self::$version);
        wp_enqueue_script('wp_insert_vi_gdpr_js', plugins_url('scripts/ytvi-gdpr' . self::$min . '.js', __FILE__), array('jquery'), self::$version, true);
    }

    public static function wp_insert_vi_gdpr_popup_wp_footer()
    {
        $showViConsent = true;
        $isEU = self::wp_insert_vi_api_is_eu();

        if (isset($_COOKIE['Viconsent']))
        {
            $showViConsent = false;
        }

        $labels = array();

        $viConsentPopupContent = isset(self::$alloptions[self::$opt_vi_endpoints]->consentPopupContent) ? self::$alloptions[self::$opt_vi_endpoints]->consentPopupContent : false;
        if ($viConsentPopupContent != false)
        {
            $lang = isset(self::$alloptions[self::$opt_vi_js_settings]['language']) ? self::$alloptions[self::$opt_vi_js_settings]['language'] : 'en-us';
            switch ($lang)
            {
                case 'de-de':
                    $labels['popupContent'] = $viConsentPopupContent->es;
                    $labels['accept'] = 'acepto';
                    $labels['donotaccept'] = 'no acepto';
                    $labels['showPurposes'] = 'Mostrar propósitos';
                    $labels['showVendors'] = 'Mostrar vendedores';

                    break;
                case 'fr-fr':
                    $labels['popupContent'] = $viConsentPopupContent->fr;
                    $labels['accept'] = 'J’accepte';
                    $labels['donotaccept'] = 'Je n’accepte pas';
                    $labels['showPurposes'] = 'Plus de details';
                    $labels['showVendors'] = 'Montrez les vendeurs';
                    break;
                case 'en-us':
                default:
                    $labels['popupContent'] = $viConsentPopupContent->en;
                    $labels['accept'] = 'I accept';
                    $labels['donotaccept'] = 'I do not accept';
                    $labels['showPurposes'] = 'View purposes';
                    $labels['showVendors'] = 'View vendors';
                    break;
            }
        }
        ?>

        <div id="wp_insert_vi_consent_popup_wrapper" style="display: none;">
            <div id="wp_insert_vi_consent_popup_wrapper2">
                <div id="wp_insert_vi_consent_popup_message">
                    <?php echo wp_kses_post($labels['popupContent']); ?>
                </div>
                <div id="wp_insert_vi_consent_popup_actions_wrapper">
                    <input id="wp_insert_vi_consent_popup_disagree_btn" type="button" value="<?php echo $labels['donotaccept'] ?>" onclick="wp_insert_vi_consent_popup_disagree()" />
                    <input id="wp_insert_vi_consent_popup_agree_btn"  type="button" value="<?php echo $labels['accept'] ?>" onclick="wp_insert_vi_consent_popup_agree()" />
                </div>
                <!--            <div id="wp_insert_vi_consent_popup_links_wrapper">-->                
                <!--            </div>-->
                <input id="wp_insert_vi_consent_popup_is_eu" type="hidden" value="<?php echo $isEU ?>" />
                <input id="wp_insert_vi_consent_popup_url" type="hidden" value="<?php echo esc_attr(trailingslashit(get_bloginfo('url'))) ?>" />
                <input id="wp_insert_vi_consent_popup_auth" type="hidden" value="<?php echo wp_create_nonce('wp_insert_vi_consent') ?>" />
                <input id="wp_insert_vi_consent_popup_vendor_list_version" type="hidden" value="<?php echo esc_attr(self::$alloptions[self::$opt_vi_endpoints]->vendorListVersion) ?>" />
                <?php
                $purposesBinary = '000000000000000000000000';
                $purposes = self::$alloptions[self::$opt_vi_endpoints]->purposes;
                if (isset($purposes) && (count($purposes) > 0))
                {
                    foreach ($purposes as $purpose)
                    {
                        $purposesBinary = substr_replace($purposesBinary, '1', ((24 - (int) $purpose->id) + 1), 1);
                    }
                }
                ?>
                <input id="wp_insert_vi_consent_popup_vendor_list_purposes" type="hidden" value="<?php echo esc_attr($purposesBinary) ?>" />
            </div>
        </div>
        <!--        <div id="wp_insert_vi_consent_popup_overlay" style="display: none;"></div>-->
        <?php
        if ((bool) self::$alloptions[self::$opt_vi_show_privacy_button])
        {
            ?>
            <span id="wp_insert_vi_consent_popup_settings_button" onclick="wp_insert_vi_consent_popup_settings()" unselectable="on" style="display: none;">vi Privacy settings</span>
            <?php
        }
    }

    public static function wp_insert_vi_gdpr_data_init()
    {
        if (isset($_GET['wp_insert_vi_consent']) && ($_GET['wp_insert_vi_consent'] != ''))
        {
            check_ajax_referer('wp_insert_vi_consent', 'wp_insert_vi_consent');

            global $wpdb;
            $table_name = $wpdb->prefix . 'vi_consent_logs';
            $query = $wpdb->prepare("SHOW TABLES LIKE %s", $wpdb->esc_like($table_name));
            if ($wpdb->get_var($query) != $table_name)
            {
                self::vi_db_init_schema();
            }

            $viconsent = array(
                'id' => 0,
                'viconsent' => (isset($_COOKIE['Viconsent']) ? $_COOKIE['Viconsent'] : ''),
                'date_created' => date('Y-m-d H:i:s')
            );

            $result = $wpdb->insert($table_name, $viconsent);
            die();
        }
    }

    public static function vi_db_init_schema()
    {
        try
        {
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE " . $wpdb->prefix . 'vi_consent_logs' . " (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  viconsent varchar(1000) NOT NULL DEFAULT '',
  date_created datetime NOT NULL,
  PRIMARY KEY  (id)
) $charset_collate;";
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta($sql);
        }
        catch (Exception $ex)
        {
            
        }
    }

    public static function vi_cron_interval($schedules)
    {
        $schedules['ytvi_fifteen_days'] = array(
            'interval' => 1296000,
            'display' => esc_html__('Every 15 Days'),
        );

        $schedules['ytvi_two_minutes'] = array(
            'interval' => 120,
            'display' => esc_html__('Every 2 Minutes'),
        );

        return $schedules;
    }

    public static function vi_cron_cache_js()
    {
        
    }

    public static function vi_ever_logged_in()
    {
        return self::$alloptions[self::$opt_vi_last_login] != self::$vi_default_date;
    }

    public static function vi_last_login_valid()
    {
        $last_login = strtotime(self::$alloptions[self::$opt_vi_last_login]);
        $last_login_plus = strtotime(self::$alloptions[self::$opt_vi_last_login] . ' + 29 days');
        //$last_login_plus = strtotime(self::$alloptions[self::$opt_vi_last_login] . ' + 2 minutes');
        if ($last_login_plus < time())
        {
            return false;
        }
        return true;
    }

    public static function vi_token_expire()
    {
        try
        {
            self::vi_cron_stop();
            if (self::vi_logged_in() && !self::vi_last_login_valid())
            {
                self::update_option_set(array(
                    self::$opt_vi_token => ''
                ));
            }
            else if (self::vi_logged_in() && filter_input(INPUT_SERVER, 'REQUEST_METHOD') != 'POST' && ((is_admin() && filter_input(INPUT_GET, 'page') == 'youtube-ep-vi') || !is_admin())
            ) // (&& not $_POSTing anything, && on monetize page) || NOT admin page...e.g. category randomization
            {
                $adsTxtAPI = self::$alloptions[self::$opt_vi_endpoints]->adsTxtAPI;
                $tokenCheck = self::vi_remote_get($adsTxtAPI);
                $tokenCheck_valid = self::vi_adstxt_api_valid($tokenCheck);
                if ($tokenCheck_valid !== true) // do a token check. if invalid, then:
                {
                    self::update_option_set(array(
                        self::$opt_vi_token => false
                    ));

                    if (is_admin())
                    {
                        wp_safe_redirect(admin_url('admin.php?page=youtube-ep-vi'));
                        exit;
                    }
                }
            }
        }
        catch (Exception $ex)
        {
            
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////


    public static function gb_block_assets()
    {
        // frontend+backend styles.
        wp_enqueue_style('epytgb-style-css', plugins_url('dist/blocks.style.build.css', __FILE__), array('wp-blocks'), self::$version);
    }

    public static function gb_editor_assets()
    {
        // backend styels
        self::ytprefsscript();
        self::fitvids();
        if (!empty(self::$alloptions[self::$opt_not_live_on_channel]))
        {
            add_action("admin_print_footer_scripts", array(self::class, 'live_fallback_template'));
        }
        
        add_action("admin_print_footer_scripts", array(self::class, 'livestream_advice_template'));
        

        if (!self::is_restrict_wizard() && current_user_can('edit_posts'))
        {
            // Scripts.
            wp_enqueue_script(
                    'epytgb-block-js', // Handle.
                    plugins_url('/dist/blocks.build.js', __FILE__), // Block.build.js: We register the block here. Built with Webpack.
                    array('wp-blocks', 'wp-i18n', 'wp-element'), // Dependencies, defined above.
                    self::$version, true // Enqueue the script in the footer.
            );

            // Styles.
            wp_enqueue_style(
                    'epytgb-block-editor-css', // Handle.
                    plugins_url('dist/blocks.editor.build.css', __FILE__), // Block editor CSS.
                    array('wp-edit-blocks'), // Dependency to include the CSS after it.
                    self::$version
            );

            //wp_enqueue_style('__ytprefs_admin__vi_css', plugins_url('styles/ytvi-admin' . self::$min . '.css', __FILE__), array(), self::$version);
            // Tiny MCE
            wp_enqueue_style('__ytprefs_admin__tinymce_css', plugins_url('styles/epyt_mce_wizard_button' . self::$min . '.css', __FILE__), array(), self::$version);
        }
    }

    public static function gb_classic_block_setup()
    {
        if (!self::is_restrict_wizard() && current_user_can('edit_posts'))
        {
            add_thickbox();
            add_filter("mce_external_plugins", array(self::class, "gb_add_tinymce_plugin"));
            add_filter('mce_buttons_2', array(self::class, 'gb_register_tinymce_button'));
        }
    }

    public static function gb_add_tinymce_plugin($plugin_array)
    {
        $plugin_array['epyt_mce_wizard_button'] = plugins_url('scripts/epyt_mce_wizard_button' . self::$min . '.js', __FILE__) . '?ver=' . self::$version;
        return $plugin_array;
    }

    public static function gb_register_tinymce_button($buttons)
    {
        array_push($buttons, "epyt_mce_wizard_button");
        return $buttons;
    }

    public static function gb_svg_defs()
    {
        ?>
        <svg style="height: 0 !important; width: 0 !important; display: absolute !important; top: 0 !important; left: 0 !important;"><defs><style>.epytcls-1{fill:red;}.epytcls-2{fill-rule:evenodd;fill:url(#radial-gradient);}.epytcls-3{fill:#31aaff;}.epytcls-4{fill:#fff;}</style><radialGradient id="radial-gradient" cx="193" cy="85.85" r="77.53" gradientUnits="userSpaceOnUse"><stop offset="0.17" stop-color="#fff"/><stop offset="0.68" stop-color="#31aaff"/></radialGradient></defs></svg>
        <?php
    }

    public static function gb_register_block_types()
    {
        if (function_exists('register_block_type'))
        {
            register_block_type(
                    'epyt/youtube', array(
                'attributes' => array(
                    'shortcode' => array(
                        'type' => 'string'
                    )
                ),
                'render_callback' => array(self::class, 'gb_render_callback_youtube'),
                    )
            );
        }
    }

    public static function gb_render_callback_youtube($attributes, $content)
    {
        if ($attributes && $attributes['shortcode'] && strpos($attributes['shortcode'], '[') === 0)
        {
            $render = do_shortcode($attributes['shortcode']);
            if (empty($render) && stripos($attributes['shortcode'], 'live=1') !== false)
            {
                $render = '<em>This is a live embed that is not currently streaming. You can optionally fill out the <a href="' . admin_url('admin.php?page=youtube-my-preferences') . '#not_live_content_scroll" target="_blank">Not Live Content</a> field in the YouTube plugin\'s Default Settings.</em>';
            }
            return $render; //empty($render) ? $render : str_replace('data-ep-src', 'src', $render);
        }
        return isset($attributes['shortcode']) ? $attributes['shortcode'] : '';
    }

}

// constants
define('EPYT_BASE_URL', rtrim(plugins_url('', __FILE__), "\\/") . '/');
define('EPYTVI_INCLUDES_PATH', rtrim(dirname(__FILE__), "\\/") . '/includes/vi/');
define('EPYTGB_INCLUDES_PATH', rtrim(dirname(__FILE__), "\\/") . '/includes/gutenberg/');
if (!defined('EPYTVI_ENDPOINTS_URL'))
    define('EPYTVI_ENDPOINTS_URL', 'https://dashboard-api.vidint.net/v1/api/widget/settings');

$youtubeplgplus_pro = new YouTubePrefsPro();
require rtrim(dirname(__FILE__), "\\/") . '/plugin-update-checker/plugin-update-checker.php';
$myUpdateCheckerYouTubePro = Puc_v4_Factory::buildUpdateChecker(
                (strpos(YouTubePrefsPro::$epbase, 'http') === false ? 'https:' : '') . YouTubePrefsPro::$epbase . '/youtube-pro/update-checker/?prokey=' . YouTubePrefsPro::$alloptions[YouTubePrefsPro::$opt_pro], __FILE__
);
