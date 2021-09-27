<?php

namespace DynamicContentForElementor;

use DynamicContentForElementor\PageSettings\PageSettings_Scrollify;
use DynamicContentForElementor\PageSettings\PageSettings_InertiaScroll;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Core\Upgrade\Manager as UpgradeManager;
/**
 * Main Plugin Class
 *
 * @since 0.0.1
 */
class Plugin
{
    private static $instance;
    /**
     * @var UpgradeManager
     */
    public $upgrade;
    /**
     * Constructor
     *
     * @since 0.0.1
     *
     * @access public
     */
    public function __construct()
    {
        $this->init();
    }
    public static function instance()
    {
        if (\is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function init()
    {
        // Instance classes
        $this->instances();
        add_action('admin_menu', [$this, 'add_dce_menu'], 200);
        // fire actions
        add_action('elementor/init', [$this, 'add_dce_to_elementor'], 0);
        add_filter('plugin_action_links_' . DCE_PLUGIN_BASE, [$this, 'plugin_action_links']);
        add_filter('plugin_row_meta', [$this, 'plugin_row_meta'], 10, 2);
        add_filter('pre_handle_404', [$this, 'dce_allow_posts_pagination'], 999, 2);
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'add_form_fields_enchanted_tab']);
    }
    public function instances()
    {
        $this->controls = new \DynamicContentForElementor\Controls();
        $this->extensions = new \DynamicContentForElementor\Extensions();
        $this->page_settings = new \DynamicContentForElementor\PageSettings();
        $this->settings = new \DynamicContentForElementor\Settings();
        // Dashboard
        $this->api = new \DynamicContentForElementor\Dashboard\Api();
        $this->templatesystem = new \DynamicContentForElementor\Dashboard\TemplateSystem();
        $this->license = new \DynamicContentForElementor\Dashboard\License();
        $this->widgets = new \DynamicContentForElementor\Widgets();
        $this->stripe = new \DynamicContentForElementor\Stripe();
        new \DynamicContentForElementor\Ajax();
        new \DynamicContentForElementor\Assets();
        new \DynamicContentForElementor\Dashboard\Dashboard();
        new \DynamicContentForElementor\LicenseSystem();
        new \DynamicContentForElementor\TemplateSystem();
        new \DynamicContentForElementor\Elements();
        // Init hook
        do_action('dynamic_content_for_elementor/init');
    }
    /**
     * Add Actions
     *
     * @since 0.0.1
     *
     * @access private
     */
    public function add_dce_to_elementor()
    {
        // Global Settings Panel
        \DynamicContentForElementor\GlobalSettings::init();
        $this->upgrade = UpgradeManager::instance();
        // Controls
        add_action('elementor/controls/controls_registered', [$this->controls, 'on_controls_registered']);
        // Controls Manager
        \Elementor\Plugin::$instance->controls_manager = new \DynamicContentForElementor\DCE_Controls_Manager(\Elementor\Plugin::$instance->controls_manager);
        // Extensions
        $this->extensions->on_extensions_registered();
        // Page Settings
        $this->page_settings->on_page_settings_registered();
        // Widgets
        add_action('elementor/widgets/widgets_registered', [$this->widgets, 'on_widgets_registered']);
    }
    // This form tab is used for many extensions. We put it here avoiding
    // repetition at the small price of having the empty tab if the extensions
    // are disabled.
    public function add_form_fields_enchanted_tab($widget)
    {
        $elementor = \ElementorPro\Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['form_fields_enchanted_tab' => ['type' => 'tab', 'tab' => 'enchanted', 'label' => '<i class="dynicon icon-dyn-logo-dce" aria-hidden="true"></i>', 'tabs_wrapper' => 'form_fields_tabs', 'name' => 'form_fields_enchanted_tab', 'condition' => ['field_type!' => 'step']]];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    public function add_dce_menu()
    {
        // Dynamic Content - Menu
        add_menu_page('Dynamic.ooo', 'Dynamic.ooo', 'manage_options', 'dce-features', [$this->settings, 'dce_setting_page'], 'data:image/svg+xml;base64,' . $this->dce_get_icon_svg(), '58.6');
        // Dynamic Content - Features
        add_submenu_page('dce-features', 'Dynamic.ooo - ' . __('Features', 'dynamic-content-for-elementor'), __('Features', 'dynamic-content-for-elementor'), 'manage_options', 'dce-features', [$this->settings, 'dce_setting_page']);
        // Dynamic Content - Template System
        add_submenu_page('dce-features', 'Dynamic.ooo - ' . __('Template System', 'dynamic-content-for-elementor'), __('Template System', 'dynamic-content-for-elementor'), 'manage_options', 'dce-templatesystem', [$this->templatesystem, 'display_form']);
        // Dynamic Content - APIs
        add_submenu_page('dce-features', 'Dynamic.ooo - ' . __('APIs', 'dynamic-content-for-elementor'), __('APIs', 'dynamic-content-for-elementor'), 'manage_options', 'dce-apis', [$this->api, 'display_form']);
        // Dynamic Content - License
        add_submenu_page('dce-features', 'Dynamic.ooo - ' . __('License', 'dynamic-content-for-elementor'), __('License', 'dynamic-content-for-elementor'), 'administrator', 'dce-license', [$this->license, 'show_license_form']);
    }
    public static function plugin_action_links($links)
    {
        $links['config'] = '<a title="Configuration" href="' . admin_url() . 'admin.php?page=dce-features">' . __('Configuration', 'dynamic-content-for-elementor') . '</a>';
        return $links;
    }
    public function plugin_row_meta($plugin_meta, $plugin_file)
    {
        if ('dynamic-content-for-elementor/dynamic-content-for-elementor.php' === $plugin_file) {
            $row_meta = ['docs' => '<a href="https://help.dynamic.ooo/" aria-label="' . esc_attr(__('View Documentation', 'dynamic-content-for-elementor')) . '" target="_blank">' . __('Docs', 'dynamic-content-for-elementor') . '</a>', 'community' => '<a href="http://facebook.com/groups/dynamic.ooo" aria-label="' . esc_attr(__('Facebook Community', 'dynamic-content-for-elementor')) . '" target="_blank">' . __('FB Community', 'dynamic-content-for-elementor') . '</a>'];
            $plugin_meta = \array_merge($plugin_meta, $row_meta);
        }
        return $plugin_meta;
    }
    public static function dce_get_icon_svg($base64 = \true)
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 88.74 71.31"><path d="M35.65,588.27h27.5c25.46,0,40.24,14.67,40.24,35.25v.2c0,20.58-15,35.86-40.65,35.86H35.65Zm27.81,53.78c11.81,0,19.65-6.51,19.65-18v-.2c0-11.42-7.84-18-19.65-18H55.41v36.26Z" transform="translate(-35.65 -588.27)" fill="#a8abad"/><path d="M121.69,609.94a33.84,33.84,0,0,0-7.56-11.19,36.51,36.51,0,0,0-11.53-7.56A37.53,37.53,0,0,0,88,588.4a43.24,43.24,0,0,0-5.4.34,36.53,36.53,0,0,1,20.76,10,33.84,33.84,0,0,1,7.56,11.19,35.25,35.25,0,0,1,2.7,13.79v.2a34.79,34.79,0,0,1-2.75,13.79,35.21,35.21,0,0,1-19.19,18.94,36.48,36.48,0,0,1-9.27,2.45,42.94,42.94,0,0,0,5.39.35,37.89,37.89,0,0,0,14.67-2.8,35.13,35.13,0,0,0,19.19-18.94,34.79,34.79,0,0,0,2.75-13.79v-.2A35.25,35.25,0,0,0,121.69,609.94Z" transform="translate(-35.65 -588.27)" fill="#a8abad" /></svg>';
        return \base64_encode($svg);
    }
    public function dce_allow_posts_pagination($preempt, $wp_query)
    {
        if ($preempt || empty($wp_query->query_vars['page']) || empty($wp_query->post) || !is_singular()) {
            return $preempt;
        }
        $allow_pagination = \false;
        $document = '';
        $current_post_id = $wp_query->post->ID;
        $dce_posts_widgets = ['dyncontel-acfposts', 'dce-dynamicposts-v2', 'dyncontel-dynamicusers'];
        // Check if current post/page is built with Elementor and check for DCE posts pagination
        if (\Elementor\Plugin::$instance->db->is_built_with_elementor($current_post_id) && !$allow_pagination) {
            $allow_pagination = $this->dce_check_posts_pagination($current_post_id, $dce_posts_widgets);
        }
        $dce_template = get_option('dce_template');
        // Check if single DCE template is active and check for DCE posts pagination in template
        if (isset($dce_template) && 'active' == $dce_template && !$allow_pagination) {
            $options = get_option('dyncontel_options');
            $post_type = get_post_type($current_post_id);
            if ($options['dyncontel_field_single' . $post_type]) {
                $allow_pagination = $this->dce_check_posts_pagination($options['dyncontel_field_single' . $post_type], $dce_posts_widgets);
            }
        }
        // Check if single Elementor Pro template is active and check for DCE posts pagination in template
        if (Helper::is_elementorpro_active() && !$allow_pagination) {
            $locations = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_locations_manager()->get_locations();
            if (isset($locations['single'])) {
                $location_docs = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_documents_for_location('single');
                if (!empty($location_docs)) {
                    foreach ($location_docs as $location_doc_id => $settings) {
                        if ($wp_query->post->ID !== $location_doc_id && !$allow_pagination) {
                            $allow_pagination = $this->dce_check_posts_pagination($location_doc_id, $dce_posts_widgets);
                            break;
                        }
                    }
                }
            }
        }
        if ($allow_pagination) {
            return $allow_pagination;
        } else {
            return $preempt;
        }
    }
    protected function dce_check_posts_pagination($post_id, $dce_posts_widgets, $current_page = null)
    {
        $pagination = \false;
        if ($post_id) {
            $document = \Elementor\Plugin::$instance->documents->get($post_id);
            $document_elements = $document->get_elements_data();
            // Check if DCE posts widgets are present and if pagination or infinite scroll is active
            \Elementor\Plugin::$instance->db->iterate_data($document_elements, function ($element) use(&$pagination, $dce_posts_widgets) {
                if (isset($element['widgetType']) && \in_array($element['widgetType'], $dce_posts_widgets, \true)) {
                    if (isset($element['settings']['pagination_enable'])) {
                        if ($element['settings']['pagination_enable']) {
                            $pagination = \true;
                        }
                    }
                    if (isset($element['settings']['infiniteScroll_enable'])) {
                        if ($element['settings']['infiniteScroll_enable']) {
                            $pagination = \true;
                        }
                    }
                }
            });
        }
        return $pagination;
    }
}
\DynamicContentForElementor\Plugin::instance();
