<?php

namespace DynamicContentForElementor\Core\Upgrade;

use DynamicContentForElementor\Extensions;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
class Upgrades
{
    public static $extension_translation = ['DCE_Extension_Animations' => 'ext_animations', 'DCE_Extension_CopyPaste' => 'ext_copypaste', 'DCE_Extension_Editor' => 'ext_editor', 'DCE_Extension_Masking' => 'ext_masking', 'DCE_Extension_Rellax' => 'ext_rellax', 'DCE_Extension_Reveal' => 'ext_reveal', 'DCE_Extension_Template' => 'ext_template', 'DCE_Extension_Token' => 'ext_token', 'DCE_Extension_Php' => 'dynamic_tag_php', 'DCE_Extension_Transforms' => 'ext_transforms', 'DCE_Extension_Unwrap' => 'ext_unwrap', 'DCE_Extension_Video' => 'ext_video', 'DCE_Extension_Visibility' => 'ext_visibility', 'DCE_Extension_Form_Address_Autocomplete' => 'ext_form_address_autocomplete', 'DCE_Extension_Form_Amount' => 'ext_form_amount', 'DCE_Extension_Form_description' => 'ext_form_description', 'DCE_Extension_Form_Email' => 'ext_form_email', 'DCE_Extension_Form_Export' => 'ext_form_export', 'DCE_Extension_Form_Icons' => 'ext_form_icons', 'DCE_Extension_Form_Inline_Align' => 'ext_form_inline_align', 'DCE_Extension_Form_Length' => 'ext_form_length', 'DCE_Extension_Form_Message' => 'ext_form_message', 'DCE_Extension_Form_Method' => 'ext_form_method', 'DCE_Extension_Form_Password_Visibility' => 'ext_form_password_visibility', 'DCE_Extension_Form_PDF' => 'ext_form_pdf', 'DCE_Extension_Form_Redirect' => 'ext_form_redirect', 'DCE_Extension_Form_Regex' => 'ext_form_regex', 'DCE_Extension_Form_Reset' => 'ext_form_reset', 'DCE_Extension_Form_Save' => 'ext_form_save', 'DCE_Extension_Form_Select2' => 'ext_form_select2', 'DCE_Extension_Form_Signature' => 'ext_form_signature', 'DCE_Extension_Form_Step' => 'ext_form_step', 'DCE_Extension_Form_Submit_On_Change' => 'ext_form_submit_on_change', 'DCE_Extension_Form_Submit' => 'ext_form_submit', 'DCE_Extension_Form_Telegram' => 'ext_form_telegram', 'DCE_Extension_Form_Visibility' => 'ext_form_visibility', 'ConditionalFieldsV2' => 'ext_conditional_fields_v2', 'DCE_Extension_Form_WYSIWYG' => 'ext_form_wysiwyg', 'DCE_Extension_Form_PayPal' => 'ext_form_paypal', 'DCE_Extension_Form_Stripe' => 'ext_form_stripe', 'CustomValidation' => 'ext_custom_validation', 'JsField' => 'ext_js_field', 'LiveHtml' => 'ext_live_html', 'HiddenLabel' => 'ext_hidden_label', 'DynamicSelect' => 'ext_dynamic_select', 'Tooltip' => 'ext_tooltip', 'DynamicCountdown' => 'ext_dynamic_countdown', 'ConfirmDialog' => 'ext_confirm_dialog'];
    public static $widget_translation = ['DCE_Widget_Acf' => 'wdg_acf', 'DCE_Widget_Gallery' => 'wdg_gallery', 'DCE_Widget_Relationship' => 'wdg_relationship', 'DCE_Widget_Repeater' => 'wdg_repeater', 'Acf_Repeater_V2' => 'wdg_acf_repeater_v2', 'Acf_Flexible_Content' => 'wdg_acf_flexible_content', 'DCE_Widget_Slider' => 'wdg_slider', 'DCE_Widget_BarCode' => 'wdg_barcode', 'DCE_Widget_Calendar' => 'wdg_calendar', 'DCE_Widget_Clipboard' => 'wdg_clipboard', 'DCE_Widget_DynamicCookie' => 'wdg_dyncookie', 'DCE_Widget_Favorites' => 'wdg_favorites', 'DCE_Widget_ModalWindow' => 'wdg_modal_window', 'DCE_Widget_Pdf' => 'wdg_pdf', 'DCE_Widget_PopUp' => 'wdg_popup', 'DCE_Widget_Template' => 'wdg_template', 'DCE_Widget_Tokens' => 'wdg_tokens', 'DCE_Widget_AnimateText' => 'wdg_animate_text', 'DCE_Widget_Parallax' => 'wdg_parallax', 'DCE_Widget_ThreesixtySlider' => 'wdg_threesixty_slider', 'DCE_Widget_Tilt' => 'wdg_tilt', 'DCE_Widget_TwentyTwenty' => 'wdg_twentytwenty', 'DCE_Widget_DoShortcode' => 'wdg_doshortcode', 'DCE_Widget_IncludeFile' => 'wdg_includefile', 'DCE_Widget_RawPhp' => 'wdg_rawphp', 'DCE_Widget_RemoteContent' => 'wdg_remotecontent', 'Iframe' => 'wdg_iframe', 'GoogleDocumentPreview' => 'wdg_google_doc_preview', 'DCE_Widget_DynamicPosts' => 'wdg_dynamicposts', 'DCE_Widget_DynamicPosts_v2' => 'wdg_dynamicposts_v2', 'DynamicWooProducts' => 'wdg_dynamic_woo_products', 'WooProductsCart' => 'wdg_woo_products_cart', 'WooProductUpsells' => 'wdg_woo_product_upsells', 'WooProductCrosssells' => 'wdg_woo_product_crosssells', 'ShowFavorites' => 'wdg_show_favorites', 'StickyPosts' => 'wdg_stick_yposts', 'SearchResults' => 'wdg_search_results', 'MyPosts' => 'wdg_my_posts', 'DCE_Widget_GoogleMaps' => 'wdg_google_maps', 'DCE_Widget_DynamicUsers' => 'wdg_dynamic_users', 'DCE_Widget_AnimatedOffcanvasMenu' => 'wdg_animated_offcanvas_menu', 'DCE_Widget_CursorTracker' => 'wdg_cursor_tracker', 'DCE_Widget_FileBrowser' => 'wdg_file_browser', 'DCE_Widget_ParentChildMenu' => 'wdg_parent_child_menu', 'DCE_Widget_SearchFilter' => 'wdg_search_filter', 'DCE_Widget_SinglePostsMenu' => 'wdg_single_posts_menu', 'DCE_Widget_TaxonomyTermsMenu' => 'wdg_taxonomy_terms_menu', 'DCE_Widget_Views' => 'wdg_views', 'DCE_Widget_Pods' => 'wdg_pods', 'DCE_Widget_PodsGallery' => 'wdg_pods_gallery', 'DCE_Widget_PodsRelationship' => 'wdg_pods_relationship', 'DCE_Widget_Breadcrumbs' => 'wdg_breadcrumbs', 'DCE_Widget_Content' => 'wdg_content', 'DCE_Widget_Date' => 'wdg_date', 'DCE_Widget_Excerpt' => 'wdg_excerpt', 'DCE_Widget_FeaturedImage' => 'wdg_featured_image', 'DCE_Widget_IconFormat' => 'wdg_icon_format', 'DCE_Widget_Meta' => 'wdg_meta', 'DCE_Widget_NextPrev' => 'wdg_next_prev', 'DCE_Widget_ReadMore' => 'wdg_read_more', 'DCE_Widget_Terms' => 'wdg_terms', 'DCE_Widget_Title' => 'wdg_title', 'DCE_Widget_TitleType' => 'wdg_title_type', 'DCE_Widget_User' => 'wdg_user', 'DCE_Widget_TitleTaxonomy' => 'wdg_title_taxonomy', 'DCE_Widget_SvgBlob' => 'wdg_svg_blob', 'DCE_Widget_SvgDistortion' => 'wdg_svg_distortion', 'DCE_Widget_SvgFilterEffects' => 'wdg_svg_filter_effects', 'DCE_Widget_SvgImagemask' => 'wdg_svg_imagemask', 'DCE_Widget_SvgMorphing' => 'wdg_svg_morphing', 'DCE_Widget_Svg_PathText' => 'wdg_svg_path_text', 'DCE_Widget_Toolset' => 'wdg_toolset', 'DCE_Widget_ToolsetRelationship' => 'wdg_toolset_relationship', 'DCE_Widget_BgCanvas' => 'wdg_bg_canvas', 'DCE_Widget_DistortionImage' => 'wdg_distortion_image', 'DCE_Widget_Panorama' => 'wdg_panorama', 'OsmMap' => 'wdg_osm_map', 'PdfViewer' => 'wdg_pdf_viewer'];
    public static $setting_translation = ['DCE_Document_Scrolling' => 'pst_scrolling', 'DCE_Settings_SmoothTransition' => 'gst_smooth_transition', 'DCE_Settings_TrackerHeader' => 'gst_tracker_header'];
    public static function _v_2_3_0_signature_change_default($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_signature_change_default'], 'control_ids' => []]];
        return self::_update_widget_settings('form', $updater, $changes);
    }
    /** Remove controls from repeater in add to favorites */
    public static function _v_2_2_0_flatten_add_to_favorites_list($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_flatten_add_to_favorites_list'], 'control_ids' => []]];
        return self::_update_widget_settings('dce-add-to-favorites', $updater, $changes);
    }
    /**
     * Smooth Transition should be inactive by default on 2.2.0. So we need to
     * save the database.
     */
    public static function _v_2_2_0_smooth_transition_default_inactive()
    {
        $option = \json_decode(get_option('dce_features_status_option', '[]'), \true);
        if (!isset($option['gst_smooth_transition'])) {
            $option['gst_smooth_transition'] = 'active';
            update_option('dce_features_status_option', wp_json_encode($option));
        }
    }
    public static function _flatten_add_to_favorites_list($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        $controls = ['dce_favorite_key', 'dce_favorite_title_add', 'dce_favorite_icon_add', 'dce_favorite_title_remove', 'dce_favorite_icon_remove'];
        if (isset($element['settings']['dce_favorite_list']) && !empty($element['settings']['dce_favorite_list'])) {
            $old = $element['settings']['dce_favorite_list'][0] ?? [];
            foreach ($controls as $control) {
                if (isset($old[$control])) {
                    $element['settings'][$control] = $old[$control];
                    $args['do_update'] = \true;
                }
            }
        }
        return $element;
    }
    public static function get_new_extensions_status()
    {
        $extensions_old = \json_decode(get_option('dce_excluded_extensions', '[]'), \true);
        $extensions_old += \json_decode(get_option('dce_excluded_dynamic_tags', '[]'), \true);
        $extensions_new = [];
        foreach ($extensions_old as $old_name => $is_excluded) {
            if (!isset(self::$extension_translation[$old_name])) {
                continue;
            }
            $new_name = self::$extension_translation[$old_name];
            $extensions_new[$new_name] = $is_excluded ? 'inactive' : 'active';
        }
        return $extensions_new;
    }
    public static function get_new_widgets_status()
    {
        $widgets_old = \json_decode(get_option('dce_excluded_widgets', '[]'), \true);
        $widgets_new = [];
        foreach ($widgets_old as $old_name => $is_excluded) {
            if (!isset(self::$widget_translation[$old_name])) {
                continue;
            }
            $new_name = self::$widget_translation[$old_name];
            $widgets_new[$new_name] = $is_excluded ? 'inactive' : 'active';
        }
        // all widgets that are not set in the option were active by default (including legacy):
        $all_widgets = \DynamicContentForElementor\Plugin::instance()->features->filter(['type' => 'widget']);
        $all_widgets = \array_map(function ($w) {
            return 'active';
        }, $all_widgets);
        return $widgets_new + $all_widgets;
    }
    public static function get_new_settings_status()
    {
        $settings_old = \json_decode(get_option('dce_excluded_page_settings', '[]'), \true);
        $settings_old += \json_decode(get_option('dce_excluded_global_settings', '[]'), \true);
        $settings_new = [];
        foreach ($settings_old as $old_name => $_) {
            if (!isset(self::$setting_translation[$old_name])) {
                continue;
            }
            $new_name = self::$setting_translation[$old_name];
            $settings_new[$new_name] = 'inactive';
        }
        return $settings_new;
    }
    /**
     * Merge all feature status options into one.
     */
    public static function _v_2_1_0_merge_feature_status_options()
    {
        $ext = self::get_new_extensions_status();
        $wdg = self::get_new_widgets_status();
        $set = self::get_new_settings_status();
        $features_manager = \DynamicContentForElementor\Plugin::instance()->features;
        $features_manager->db_update_features_status($ext + $wdg + $set);
    }
    /**
     * Move Dynamic Tags option from extensions option
     */
    public static function _v_2_0_0_move_dynamic_tags_option()
    {
        $excluded_extensions = \json_decode(get_option('dce_excluded_extensions'), \true);
        $excluded_dynamic_tags = [];
        if (isset($excluded_extensions['DCE_Extension_Template']) && \true === $excluded_extensions['DCE_Extension_Template']) {
            $excluded_dynamic_tags['DCE_Extension_Template'] = \true;
        }
        if (isset($excluded_extensions['DCE_Extension_Token']) && \true === $excluded_extensions['DCE_Extension_Token']) {
            $excluded_dynamic_tags['DCE_Extension_Token'] = \true;
        }
        if (!empty($excluded_dynamic_tags)) {
            update_option('dce_excluded_dynamic_tags', wp_json_encode($excluded_dynamic_tags));
        }
    }
    /**
     * Remove old options not used from v 1.14.0
     */
    public static function _v_2_0_0_remove_old_options()
    {
        delete_option('dce_template_disable');
        delete_option('WP-DCE-1_excluded_extensions');
        delete_option('WP-DCE-1_excluded_widgets');
        delete_option('WP-DCE-1_excluded_documents');
        delete_option('WP-DCE-1_excluded_globals');
        delete_option('WP-DCE-1_active_widgets');
        delete_option('WP-DCE-1_active_extensions');
        delete_option('WP-DCE-1_active_documents');
        delete_option('WP-DCE-1_active_globals');
        delete_option('WP-DCE-1_license_activated');
        delete_option('WP-DCE-1_license_domain');
        delete_option('WP-DCE-1_license_key');
        delete_option('WP-DCE-1_license_expiration');
        return \false;
    }
    /**
     * Move Custom Meta Fields tab on Dynamic Posts - Custom Meta Items
     */
    public static function _v_2_0_0_dynamic_posts_move_custom_meta_fields($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_dynamic_posts_move_custom_meta_fields'], 'control_ids' => []]];
        return self::_update_widget_settings('dce-dynamicposts-v2', $updater, $changes);
    }
    public static function _dynamic_posts_move_custom_meta_fields($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        if (isset($element['settings']['list_items']) && isset($element['settings']['custommeta_items'])) {
            $custommeta_items = $element['settings']['custommeta_items'];
            $custommeta_items_align = $element['settings']['custommeta_items_align'] ?? '';
            $count_custommeta = \count($custommeta_items);
            $old_items = $element['settings']['list_items'];
            $new_items = [];
            $metaitems_found = \false;
            foreach ($old_items as $item) {
                if ('item_custommeta' === ($item['item_id'] ?? '')) {
                    if ($metaitems_found) {
                        continue;
                    }
                    $metaitems_found = \true;
                    foreach ($custommeta_items as $custommeta_item) {
                        $new_item = $custommeta_item;
                        if (empty($custommeta_item['item_align'])) {
                            $new_item['item_align'] = $custommeta_items_align;
                        } else {
                            $new_item['item_align'] = $custommeta_item['item_align'];
                        }
                        $new_item['item_id'] = 'item_custommeta';
                        $new_items[] = $new_item;
                    }
                } else {
                    $new_items[] = $item;
                }
            }
            $element['settings']['list_items'] = $new_items;
            $args['do_update'] = \true;
        }
        return $element;
    }
    /**
     * Change Open Link in New Window on Dynamic Posts Items
     */
    public static function _v_2_0_0_dynamic_posts_link_new_window($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_dynamic_posts_link_new_window'], 'control_ids' => []]];
        return self::_update_widget_settings('dce-dynamicposts-v2', $updater, $changes);
    }
    public static function _dynamic_posts_link_new_window($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        if (isset($element['settings']['list_items'])) {
            $old_items = $element['settings']['list_items'];
            $new_items = [];
            foreach ($old_items as $item) {
                if (empty($item['open_target_blank'])) {
                    $item['open_target_blank'] = 'yes';
                }
                $new_items[] = $item;
            }
            $element['settings']['list_items'] = $new_items;
            $args['do_update'] = \true;
        }
        return $element;
    }
    public static function _rename_tooltip_control($element, $args)
    {
        if (!($element['settings']['enable_tooltip'] ?? '') === 'yes') {
            return $element;
        }
        $args['do_update'] = \true;
        $changes = ['enable_tooltip' => 'dce_enable_tooltip', 'tooltip_content' => 'dce_tooltip_content', 'tooltip_arrow' => 'dce_tooltip_arrow', 'tooltip_follow_cursor' => 'dce_tooltip_follow_cursor', 'tooltip_max_width' => 'dce_tooltip_max_width', 'tooltip_touch' => 'dce_tooltip_touch', 'tooltip_background_color' => 'dce_tooltip_background_color', 'tooltip_color' => 'dce_tooltip_color'];
        foreach ($changes as $old => $new) {
            if (isset($element['settings'][$old])) {
                $element['settings'][$new] = $element['settings'][$old];
                unset($element['settings'][$old]);
            }
        }
        return $element;
    }
    /** Rename because of conflict with other plugins */
    public static function _v_1_15_3_rename_tooltip_control($updater)
    {
        global $wpdb;
        $post_ids = $updater->query_col('SELECT `post_id`
					FROM `' . $wpdb->postmeta . '`
					WHERE `meta_key` = "_elementor_data"
					AND `meta_value` LIKE \'%"enable_tooltip":"yes"%\';');
        if (empty($post_ids)) {
            return \false;
        }
        foreach ($post_ids as $post_id) {
            $do_update = \false;
            $document = \Elementor\Plugin::instance()->documents->get($post_id);
            if (!$document) {
                continue;
            }
            $data = $document->get_elements_data();
            if (empty($data)) {
                continue;
            }
            $args = ['do_update' => &$do_update];
            $callback = ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_rename_tooltip_control'];
            $data = \Elementor\Plugin::instance()->db->iterate_data($data, $callback, $args);
            if (!$do_update) {
                continue;
            }
            // We need the `wp_slash` in order to avoid the unslashing during the `update_metadata`
            $json_value = wp_slash(wp_json_encode($data));
            update_metadata('post', $post_id, '_elementor_data', $json_value);
        }
        return $updater->should_run_again($post_ids);
    }
    /**
     * Change _id to item_id on Dynamic Posts v2 Items and remove hidden items
     */
    public static function _v_1_15_0_dynamic_posts_v2_item_id($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_dynamic_posts_v2_items'], 'control_ids' => []]];
        return self::_update_widget_settings('dce-dynamicposts-v2', $updater, $changes);
    }
    /**
     * Set a new option called 'dce_template'
     */
    public static function _v_1_14_4_template_system_old_default()
    {
        $template_disable = get_option('dce_template_disable');
        if (!$template_disable) {
            update_option('dce_template', 'active');
        } else {
            // We used 0 before, change it to 'inactive'.
            update_option('dce_template', 'inactive');
        }
        return \false;
    }
    /**
     * split ACF Repeater widget in old and new version
     */
    public static function _v_1_14_0_split_acf_repeater($updater)
    {
        if (get_option('dce_acfrepeater_newversion', 'yes') !== 'yes') {
            return \false;
        }
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_change_widget_name'], 'data' => 'dce-acf-repeater-v2']];
        return self::_update_widget_settings('dyncontel-acf-repeater', $updater, $changes);
    }
    /**
     * We want to change setting "Results per page/Number of Posts" default values for Dynamic Posts v1 and v2
     */
    public static function _v_1_14_0_dynamic_posts_results_per_page_default($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_widget_settings_save_old_default'], 'control_ids' => ['num_posts' => '-1']]];
        return self::_update_widget_settings('dyncontel-acfposts', $updater, $changes);
    }
    public static function _v_1_14_0_dynamic_posts_v2_results_per_page_default($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_widget_settings_save_old_default'], 'control_ids' => ['num_posts' => '-1']]];
        return self::_update_widget_settings('dce-dynamicposts-v2', $updater, $changes);
    }
    public static function _v_1_14_0_update_excluded_extensions_option($updater)
    {
        // Update on v2.1.0: this is the upgrade from
        // WP-DCE-1_excluded_extensions to dce_excluded_extension. It did the
        // right thing before, but the old functions have been removed, and so
        // now we do nothing. Thus resetting all extensions to active.
    }
    /**
     * Remove WP-DCE-1 from all options and set new options with "dce_" prefix
     */
    public static function _v_1_14_0_update_options($updater)
    {
        $excluded_widgets = \json_decode(get_option('WP-DCE-1_excluded_widgets'), \true);
        if ($excluded_widgets) {
            update_option('dce_excluded_widgets', wp_json_encode($excluded_widgets));
        }
        $excluded_documents = \json_decode(get_option('WP-DCE-1_excluded_documents'), \true);
        if ($excluded_documents) {
            update_option('dce_excluded_page_settings', wp_json_encode($excluded_documents));
        }
        $excluded_globals = \json_decode(get_option('WP-DCE-1_excluded_globals'), \true);
        // Set Option for Frontend Navigator
        if (isset($excluded_globals['DCE_Frontend_Navigator_Enable_Visitor'])) {
            update_option('dce_frontend_navigator', 'active-visitors');
        } elseif (!isset($excluded_globals['DCE_Frontend_Navigator'])) {
            update_option('dce_frontend_navigator', 'active');
        } else {
            update_option('dce_frontend_navigator', 'inactive');
        }
        if ($excluded_globals) {
            update_option('dce_excluded_global_settings', wp_json_encode($excluded_globals));
        }
        $license_activated = get_option('WP-DCE-1_license_activated');
        if ($license_activated) {
            update_option('dce_license_activated', $license_activated);
        }
        $license_domain = get_option('WP-DCE-1_license_domain');
        if ($license_domain) {
            update_option('dce_license_domain', $license_domain);
        }
        $license_key = get_option('WP-DCE-1_license_key');
        if ($license_key) {
            update_option('dce_license_key', $license_key);
        }
        $license_expiration = get_option('WP-DCE-1_license_expiration');
        if ($license_expiration) {
            update_option('dce_license_expiration', $license_expiration);
        }
    }
    public static function _v_1_12_4_remove_option_api_array($updater)
    {
        $dce_apis = get_option('WP-DCE-1_apis', []);
        if (isset($dce_apis['dce_api_gmaps'])) {
            update_option('dce_google_maps_api', $dce_apis['dce_api_gmaps']);
        }
        if (!empty($dce_apis['dce_api_gmaps_acf'])) {
            update_option('dce_google_maps_api_acf', 'yes');
        }
    }
    public static function _v_1_12_4_dce_tokens_html_tag_default($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_widget_settings_save_old_default'], 'control_ids' => ['dce_html_tag' => 'span']]];
        return self::_update_widget_settings('dce-tokens', $updater, $changes);
    }
    /**
     * We want to change setting default values for the converters of the form
     * pdf action and pdf widget.
     */
    public static function _v_1_10_0_pdf_button_default($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_widget_settings_save_old_default'], 'control_ids' => ['dce_pdf_button_converter' => 'dompdf']]];
        return self::_update_widget_settings('dce_pdf_button', $updater, $changes);
    }
    public static function _v_1_10_0_form_pdf_default($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_save_old_default_conv_form_pdf'], 'control_ids' => []]];
        return self::_update_widget_settings('form', $updater, $changes);
    }
    /** Form pdf SVG has now a repeater for multiple pages. */
    public static function _v_1_10_0_form_pdf_svg_repeater($updater)
    {
        $changes = [['callback' => ['DynamicContentForElementor\\Core\\Upgrade\\Upgrades', '_pdf_form_new_svg_repeater'], 'control_ids' => []]];
        return self::_update_widget_settings('form', $updater, $changes);
    }
    /**
     * This is so we can distinguish old installations from new ones, old
     * installations use the old acfrepeater version.
     */
    public static function _v_1_11_0_acfrepeater_version_olddefault()
    {
        $version = get_option('dce_acfrepeater_newversion');
        if (!$version) {
            update_option('dce_acfrepeater_newversion', 'no');
        } else {
            // We used 1 before, change it to 'yes'.
            update_option('dce_acfrepeater_newversion', 'yes');
        }
        return \false;
    }
    public static function _signature_change_default($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        if (isset($element['settings']['form_fields']) && \is_array($element['settings']['form_fields'])) {
            $fields = $element['settings']['form_fields'];
            foreach ($fields as $i => $field) {
                if (($field['field_type'] ?? '') === 'dce_form_signature') {
                    $element['settings']['form_fields'][$i]['signature_save_to_file'] = 'no';
                    $args['do_update'] = \true;
                }
            }
        }
        return $element;
    }
    public static function _change_widget_name($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        $element['widgetType'] = $args['data'];
        $args['do_update'] = \true;
        return $element;
    }
    public static function _dynamic_posts_v2_items($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        if (isset($element['settings']['list_items'])) {
            $old_items = $element['settings']['list_items'];
            $new_items = [];
            foreach ($old_items as $item) {
                if (!isset($item['show_item']) || $item['show_item'] === 'check') {
                    $item['item_id'] = $item['_id'];
                    unset($item['_id']);
                    $new_items[] = $item;
                }
            }
            $element['settings']['list_items'] = $new_items;
            $args['do_update'] = \true;
        }
        return $element;
    }
    public static function _pdf_form_new_svg_repeater($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        if (isset($element['settings']['dce_form_pdf_svg_code'])) {
            $code = $element['settings']['dce_form_pdf_svg_code'];
            unset($element['settings']['dce_form_pdf_svg_code']);
            $repeater = [['_id' => wp_unique_id(), 'text' => $code]];
            $element['settings']['dce_form_pdf_svg_code_repeater'] = $repeater;
            $args['do_update'] = \true;
        }
        return $element;
    }
    public static function _save_old_default_conv_form_pdf($element, $args)
    {
        $widget_id = $args['widget_id'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        // if the pdf action was registered in the form:
        if (isset($element['settings']['submit_actions']) && \in_array('dce_form_pdf', $element['settings']['submit_actions'])) {
            if (empty($element['settings']['dce_form_pdf_converter'])) {
                $element['settings']['dce_form_pdf_converter'] = 'dompdf';
                $args['do_update'] = \true;
            }
        }
        return $element;
    }
    /**
     * $changes is an array of arrays in the following format:
     * [
     *   'control_ids' => array of control ids
     *   'callback' => user callback to manipulate the control_ids
     * ]
     *
     * @param       $widget_id
     * @param       $updater
     * @param array $changes
     *
     * @return bool
     */
    public static function _update_widget_settings($widget_id, $updater, $changes)
    {
        global $wpdb;
        $post_ids = $updater->query_col('SELECT `post_id`
					FROM `' . $wpdb->postmeta . '`
					WHERE `meta_key` = "_elementor_data"
					AND `meta_value` LIKE \'%"widgetType":"' . $widget_id . '"%\';');
        if (empty($post_ids)) {
            return \false;
        }
        foreach ($post_ids as $post_id) {
            $do_update = \false;
            $document = \Elementor\Plugin::instance()->documents->get($post_id);
            if (!$document) {
                continue;
            }
            $data = $document->get_elements_data();
            if (empty($data)) {
                continue;
            }
            // loop thru callbacks & array
            foreach ($changes as $change) {
                $args = ['do_update' => &$do_update, 'widget_id' => $widget_id];
                if (isset($change['control_ids'])) {
                    $args['control_ids'] = $change['control_ids'];
                }
                if (isset($change['data'])) {
                    $args['data'] = $change['data'];
                }
                if (isset($change['prefix'])) {
                    $args['prefix'] = $change['prefix'];
                    $args['new_id'] = $change['new_id'];
                }
                $data = \Elementor\Plugin::instance()->db->iterate_data($data, $change['callback'], $args);
                if (!$do_update) {
                    continue;
                }
                // We need the `wp_slash` in order to avoid the unslashing during the `update_metadata`
                $json_value = wp_slash(wp_json_encode($data));
                update_metadata('post', $post_id, '_elementor_data', $json_value);
            }
        }
        // End foreach().
        return $updater->should_run_again($post_ids);
    }
    /**
     * @param $element
     * @param $args
     *
     * @return mixed
     */
    public static function _rename_widget_settings($element, $args)
    {
        $widget_id = $args['widget_id'];
        $changes = $args['control_ids'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        foreach ($changes as $old => $new) {
            if (!empty($element['settings'][$old]) && !isset($element['settings'][$new])) {
                $element['settings'][$new] = $element['settings'][$old];
                $args['do_update'] = \true;
            }
        }
        return $element;
    }
    /**
     * Useful when we want to change a setting default value: Finds all the
     * instances where the setting value is unset and set it to the old
     * default value.
     */
    public static function _widget_settings_save_old_default($element, $args)
    {
        $widget_id = $args['widget_id'];
        $changes = $args['control_ids'];
        if (empty($element['widgetType']) || $widget_id !== $element['widgetType']) {
            return $element;
        }
        foreach ($changes as $setting_name => $old_default) {
            if (empty($element['settings'][$setting_name])) {
                $element['settings'][$setting_name] = $old_default;
                $args['do_update'] = \true;
            }
        }
        return $element;
    }
}
