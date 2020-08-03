<?php
if (! class_exists('ESSB_Admin_Topbar_Menu')) {

    /**
     * Create helper topbar menu
     *
     * @author appscreo
     * @package EasySocialShareButtons
     */
    final class ESSB_Admin_Topbar_Menu {

        function __construct () {
            add_action('admin_bar_menu', array ( $this, 'attach_admin_barmenu' ), 89);
            add_action('wp_enqueue_scripts', array ( $this, 'enqueue_assets' ));
            add_action('admin_enqueue_scripts', array ( $this, 'enqueue_assets' ));
        }

        public function enqueue_assets () {
            if (! is_admin_bar_showing() || ! current_user_can('edit_posts')) {
                return;
            }
            
            wp_enqueue_style('essb-adminbar', ESSB3_ASSETS_URL . '/admin/adminbar.css', array (), ESSB3_VERSION);
        }

        public function attach_admin_barmenu () {
            global $post;
            
            $url = essb_get_site_current_url(isset($post) ? $post->ID : null);
            
            $not_activated_dot = "";
            if (ESSBActivationManager::existNewVersion()) {
                $not_activated_dot = '<span style="background-color:#e74c3c;width:10px;height:10px;border-radius:50%;margin-left:5px;display:inline-block;"></span>';
            }
            
            $this->add_root_menu("Easy Social Share Buttons" . $not_activated_dot, "essb", esc_url(get_admin_url() . 'admin.php?page=essb_options'));
            
            $this->add_sub_menu(esc_html__('Features', 'essb'), esc_url(get_admin_url() . 'admin.php?page=essb_options'), "essb", "essb_p1");
            $this->add_sub_menu(esc_html__('Share Buttons', 'essb'), esc_url(get_admin_url() . 'admin.php?page=essb_options'), "essb_p1", "essb_p11");
            $this->add_sub_menu(esc_html__('Where to Display', 'essb'), esc_url(get_admin_url() . 'admin.php?page=essb_redirect_where'), "essb_p1", "essb_p12");
            
            if (! essb_option_bool_value('deactivate_module_followers')) {
                $this->add_sub_menu(esc_html__('Followers Counter', 'essb'), esc_url(get_admin_url() . 'admin.php?page=essb_redirect_follow&tab=follow'), "essb_p1", "essb_p13");
            }
            
            if (! essb_option_bool_value('deactivate_module_profiles')) {
                $this->add_sub_menu(esc_html__('Profile Links', 'essb'), esc_url(get_admin_url() . 'admin.php?page=essb_redirect_profiles&tab=profiles'), "essb_p1", "essb_p14");
            }
            
            if (! essb_option_bool_value('deactivate_module_subscribe')) {
                $this->add_sub_menu(esc_html__('Subscribe Forms', 'essb'), esc_url(get_admin_url() . 'admin.php?page=essb_redirect_subscribe&tab=subscribe'), "essb_p1", "essb_p15");
            }
            
            if (! essb_option_bool_value('deactivate_module_instagram')) {
                $this->add_sub_menu(esc_html__('Instagram Feed', 'essb'), esc_url(get_admin_url() . 'admin.php?page=essb_redirect_instagram&tab=instagram'), "essb_p1", "essb_p16");
            }
            
            if (! essb_option_bool_value('deactivate_module_proofnotifications')) {
                $this->add_sub_menu(esc_html__('Social Proof Notifications', 'essb'), esc_url(get_admin_url() . 'admin.php?page=essb_redirect_proof-notifications&tab=proof-notifications'), "essb_p1", "essb_p17");
            }
            
            $this->add_sub_menu(esc_html__('Settings', 'essb'), esc_url(get_admin_url() . 'admin.php?page=essb_options'), "essb", "essb_p2");
            
            $this->add_sub_menu(esc_html__('Optimizations', 'essb'), esc_url(get_admin_url() . 'admin.php?page=essb_redirect_advanced&tab=advanced&section=optimization'), "essb_p2", "essb_p21");
            $this->add_sub_menu(esc_html__('Advanced', 'essb'), esc_url(get_admin_url() . 'admin.php?page=essb_redirect_advanced&tab=advanced&section=advanced'), "essb_p2", "essb_p22");
            $this->add_sub_menu(esc_html__('Integrations', 'essb'), esc_url(get_admin_url() . 'admin.php?page=essb_redirect_advanced&tab=advanced&section=integrate'), "essb_p2", "essb_p23");
            $this->add_sub_menu(esc_html__('Color Change', 'essb'), esc_url(get_admin_url() . 'admin.php?page=essb_redirect_style&tab=style'), "essb_p2", "essb_p25");
            $this->add_sub_menu(esc_html__('Shortcode List', 'essb'), esc_url(get_admin_url() . 'admin.php?page=essb_redirect_shortcode&tab=shortcode'), "essb_p2", "essb_p24");
            
            $this->add_sub_menu(esc_html__('Validate and test shared data', 'essb'), '', "essb", "essb_v");
            $this->add_sub_menu(esc_html__('How will my information will look in Facebook', 'essb'), 'https://developers.facebook.com/tools/debug/og/object?q=' . esc_url($url), "essb_v", "essb_v1");
            $this->add_sub_menu(esc_html__('Test my Twitter Cards and validate site', 'essb'), 'https://cards-dev.twitter.com/validator/?link=' . esc_url($url), "essb_v", "essb_v2");
            $this->add_sub_menu(esc_html__('Pinterest Rich Pin validator', 'essb'), 'https://developers.pinterest.com/tools/url-debugger/?link=' . esc_url($url), "essb_v", "essb_v3");
            $this->add_sub_menu(esc_html__('LinkedIn Post Inspector', 'essb'), 'https://www.linkedin.com/post-inspector/inspect/' . urlencode(esc_url($url)), "essb_v", "essb_v4");
            
            if (defined('ESSB3_CACHE_ACTIVE')) {
                $this->add_sub_menu('<b>' . esc_html__('Purge plugin cache', 'essb') . '</b>', esc_url(get_admin_url() . 'admin.php?page=essb_redirect_advanced&tab=advanced&purge-cache=true'), "essb", "essb_p7");
            }
            
            if (defined('ESSB3_CACHED_COUNTERS')) {
                $root_clear_url = '';
                if (is_single() || is_page()) {
                    $root_clear_url = $url . '?essb_counter_update=true';
                }
                $this->add_sub_menu('' . esc_html__('Update Counters', 'essb') . '', esc_url($root_clear_url), "essb", "essb_p8");
                
                $history_clear_url = '';
                if (is_single() || is_page()) {
                    $this->add_sub_menu('<b>' . esc_html__('Update counters for current page/post', 'essb') . '</b>', esc_url($url) . '?essb_counter_update=true', "essb_p8", "essb_p81");
                    $current_url = essb_get_current_page_url();
                    $history_clear_url = $current_url;
                    $current_url = add_query_arg('essb_clear_cached_counters', 'true', $current_url);
                    $history_clear_url = add_query_arg('essb_clear_counters_history', 'true', $history_clear_url);
                }
                else if (is_admin()) {
                    $current_url = admin_url('admin.php?page=essb_options');
                    $history_clear_url = $current_url;
                    $current_url = add_query_arg('essb_clear_cached_counters', 'true', $current_url);
                    $history_clear_url = add_query_arg('essb_clear_counters_history', 'true', $history_clear_url);
                }
                else {
                    $current_url = essb_get_current_page_url();
                    $history_clear_url = essb_get_current_page_url();
                }
                
                $this->add_sub_menu(esc_html__('Setup update of counters on entire site', 'essb'), esc_url($current_url), "essb_p8", "essb_p82");
                $this->add_sub_menu(esc_html__('Clear counter history & update counters for current post/page', 'essb'), esc_url($history_clear_url), "essb_p8", "essb_p83");
            }
            
            $this->add_sub_menu(esc_html__('Shortcode Generator', 'essb'), esc_url(get_admin_url() . 'admin.php?page=essb_redirect_shortcode&tab=shortcode'), "essb", "essb_top_shortcodegen");
            
            $this->add_sub_menu(esc_html__('Need help?', 'essb'), esc_url(get_admin_url() . 'admin.php?page=essb_redirect_about&tab=about&about_tab=help'), "essb", "essb_p6");
            $this->add_sub_menu(esc_html__('About', 'essb'), esc_url(get_admin_url() . 'admin.php?page=essb_redirect_about&tab=about'), "essb", "essb_p101");
            
            if (! ESSBActivationManager::isActivated() && ! ESSBActivationManager::isThemeIntegrated()) {
                $activate_url = admin_url('admin.php?page=essb_redirect_update&tab=update');
                $this->add_sub_menu(esc_html__('Activate Plugin', 'essb'), esc_url($activate_url), "essb", "essb_p9");
            }
            
            if (ESSB3_ADDONS_ACTIVE) {
                $this->add_sub_menu(esc_html__('Extensions', 'essb'), esc_url(get_admin_url() . 'admin.php?page=essb_redirect_extensions&tab=extensions'), "essb", "essb_p7");
            }
        }

        function add_root_menu ($name, $id, $href = FALSE) {
            global $wp_admin_bar;
            if (! is_super_admin() || ! is_admin_bar_showing())
                return;
            
            $wp_admin_bar->add_menu(array ( 'id' => $id, 'meta' => array (), 'title' => $name, 'href' => $href ));
        }

        function add_sub_menu ($name, $link, $root_menu, $id, $meta = FALSE) {
            global $wp_admin_bar;
            if (! is_super_admin() || ! is_admin_bar_showing())
                return;
            
            $wp_admin_bar->add_menu(array ( 'parent' => $root_menu, 'id' => $id, 'title' => $name, 'href' => $link, 'meta' => $meta ));
        }
    }
}