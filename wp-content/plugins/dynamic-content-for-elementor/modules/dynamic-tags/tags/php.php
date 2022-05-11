<?php

namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Modules\DynamicTags\Module;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Php extends Tag
{
    protected static $acf_names = [];
    public function get_name()
    {
        return 'dce-dynamic-tag-php';
    }
    public function get_title()
    {
        return __('PHP', 'dynamic-content-for-elementor');
    }
    public function get_group()
    {
        return 'dce';
    }
    public function get_categories()
    {
        return \DynamicContentForElementor\Helper::get_dynamic_tags_categories();
    }
    public function get_docs()
    {
        return 'https://www.dynamic.ooo/widget/dynamic-tag-php/';
    }
    protected function register_controls()
    {
        if (\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            $this->register_controls_settings();
        } else {
            $this->register_controls_non_admin_notice();
        }
    }
    protected function register_controls_non_admin_notice()
    {
        $this->add_control('html_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => __('You will need administrator capabilities to edit this widget.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
    }
    protected function register_controls_settings()
    {
        $this->add_control('custom_php', ['label' => __('Custom PHP', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'language' => 'php', 'default' => 'echo "Hello World!";']);
    }
    public function render()
    {
        $settings = $this->get_settings_for_display();
        if (!\DynamicContentForElementor\Helper::can_register_unsafe_controls()) {
            return;
        }
        if (empty($settings)) {
            return;
        }
        $error = \false;
        try {
            @eval($settings['custom_php']);
        } catch (\ParseError $e) {
            $error = $e->getMessage();
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }
        if ($error && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            echo '<strong>';
            echo __('Please check your PHP code', 'dynamic-content-for-elementor');
            echo '</strong><br />';
            echo __('ERROR', 'dynamic-content-for-elementor') . ': ' . $error, "\n";
        }
    }
}
