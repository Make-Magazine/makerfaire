<?php

namespace DynamicContentForElementor;

use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Core\Upgrade\Manager as UpgradeManager;
/**
 * Main Plugin Class
 *
 * @since 0.0.1
 */
class Plugin
{
    public static $backup_path = \WP_CONTENT_DIR . '/backup';
    static $instance;
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
        self::$instance = $this;
        $this->init();
    }
    public static function instance()
    {
        if (\is_null(self::$instance)) {
            new self();
        }
        return self::$instance;
    }
    public function init()
    {
        $this->init_managers();
        // fire actions
        add_action('elementor/init', [$this, 'add_dce_to_elementor'], 0);
        add_filter('plugin_action_links_' . DCE_PLUGIN_BASE, [$this, 'plugin_action_links']);
        add_filter('plugin_row_meta', [$this, 'plugin_row_meta'], 10, 2);
        add_filter('pre_handle_404', [$this, 'dce_allow_posts_pagination'], 999, 2);
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'add_form_fields_enchanted_tab']);
    }
    public function init_managers()
    {
        $this->features = new \DynamicContentForElementor\Features();
        $this->controls = new \DynamicContentForElementor\Controls();
        $this->page_settings = new \DynamicContentForElementor\PageSettings();
        $this->admin_pages_manager = new \DynamicContentForElementor\AdminPages\Manager();
        $this->widgets = new \DynamicContentForElementor\Widgets();
        $this->stripe = new \DynamicContentForElementor\Stripe();
        $this->pdf_html_templates = new \DynamicContentForElementor\PdfHtmlTemplates();
        $this->cryptocurrency = new \DynamicContentForElementor\Cryptocurrency();
        new \DynamicContentForElementor\Ajax();
        new \DynamicContentForElementor\Assets();
        new \DynamicContentForElementor\Dashboard();
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
        $this->extensions = new \DynamicContentForElementor\Extensions();
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
    public function dce_allow_posts_pagination($preempt, $wp_query)
    {
        if ($preempt || empty($wp_query->query_vars['page']) || empty($wp_query->post) || !is_singular()) {
            return $preempt;
        }
        $allow_pagination = \false;
        $document = '';
        $current_post_id = $wp_query->post->ID;
        $dce_posts_widgets = ['dyncontel-acfposts', 'dce-dynamicposts-v2', 'dyncontel-dynamicusers', 'dce-sticky-posts', 'dce-my-posts', 'dce-dynamic-woo-products', 'dce-search-results', 'dce-dynamic-show-favorites', 'dce-woo-products-cart', 'dce-woo-product-upsells', 'dce-woo-product-crosssells'];
        // Check if current post/page is built with Elementor and check for DCE posts pagination
        if (\Elementor\Plugin::$instance->db->is_built_with_elementor($current_post_id) && !$allow_pagination) {
            $allow_pagination = $this->dce_check_posts_pagination($current_post_id, $dce_posts_widgets);
        }
        $dce_template = get_option('dce_template');
        // Check if single DCE template is active and check for DCE posts pagination in template
        if (isset($dce_template) && 'active' == $dce_template && !$allow_pagination) {
            $options = get_option('dyncontel_options');
            $post_type = get_post_type($current_post_id);
            if (\is_array($options) && $options['dyncontel_field_single' . $post_type]) {
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
