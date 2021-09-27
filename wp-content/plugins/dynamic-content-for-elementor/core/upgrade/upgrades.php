<?php

namespace DynamicContentForElementor\Core\Upgrade;

use DynamicContentForElementor\Extensions;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
class Upgrades
{
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
        $excluded_extensions = \json_decode(get_option('WP-DCE-1_excluded_extensions'), \true);
        $excluded_extensions = $excluded_extensions ? $excluded_extensions : [];
        // Old option had names as array values, it's not useful, just use true:
        \array_walk($excluded_extensions, function (&$k, $_) {
            $k = \true;
        });
        $all_extensions = Extensions::get_extensions_info();
        // excplicitly set all extensions not specified as false (not excluded):
        \array_walk($all_extensions, function (&$k, $_) {
            $k = \false;
        });
        $excluded_extensions += $all_extensions;
        update_option('dce_excluded_extensions', wp_json_encode($excluded_extensions));
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
