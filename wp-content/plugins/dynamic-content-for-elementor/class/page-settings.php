<?php

namespace DynamicContentForElementor;

if (!\defined('ABSPATH')) {
    exit;
}
class PageSettings
{
    public $page_settings = [];
    public static $registered_page_settings = [];
    public static $namespace = '\\DynamicContentForElementor\\PageSettings\\';
    public function __construct()
    {
        $this->page_settings = self::get_page_settings_info();
    }
    public static function get_page_settings_info()
    {
        $page_settings['DCE_Document_Scrolling'] = ['name' => 'dce_document_scrolling', 'title' => __('Page Scroll', 'dynamic-content-for-elementor'), 'description' => __('Turn sections into scrolling steps and minimize pagination using either scroll, snap, or inertia effects', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-page-scroll', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/page-scroll-document/'];
        return $page_settings;
    }
    /**
     * On extensions Registered
     *
     * @since 0.0.1
     *
     * @access public
     */
    public function on_page_settings_registered()
    {
        $this->register_page_settings();
    }
    /**
     * On Controls Registered
     *
     * @since 1.0.4
     *
     * @access public
     */
    public function register_page_settings()
    {
        if (empty(self::$registered_page_settings)) {
            $excluded_page_settings = self::get_excluded_page_settings();
            foreach ($this->page_settings as $page_setting_class => $page_setting_info) {
                if (!isset($excluded_page_settings[$page_setting_class])) {
                    $class = self::$namespace . $page_setting_class;
                    $page_setting_object = new $class();
                    self::$registered_page_settings[$page_setting_class] = $page_setting_info;
                    \DynamicContentForElementor\Assets::add_depends($page_setting_object);
                }
            }
        }
    }
    public static function get_excluded_page_settings()
    {
        return \json_decode(get_option('dce_excluded_page_settings', '[]'), \true);
    }
}
