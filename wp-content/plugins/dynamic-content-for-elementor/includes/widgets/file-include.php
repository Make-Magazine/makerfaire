<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class DCE_Widget_IncludeFile extends \DynamicContentForElementor\Widgets\DCE_Widget_Prototype
{
    public function show_in_panel()
    {
        if (!current_user_can('administrator')) {
            return \false;
        }
        return \true;
    }
    protected function _register_controls()
    {
        if (current_user_can('administrator') || !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $this->register_controls_content();
        } elseif (!current_user_can('administrator') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $this->register_controls_non_admin_notice();
        }
    }
    protected function register_controls_content()
    {
        $this->start_controls_section('section_includefile', ['label' => __('File Include', 'dynamic-content-for-elementor')]);
        $this->add_control('file', ['label' => __('File path', 'dynamic-content-for-elementor'), 'description' => __('The path of the file to include (e.g.:folder/file.html) ', 'dynamic-content-for-elementor'), 'placeholder' => 'wp-content/themes/my-theme/my-custom-file.php', 'type' => Controls_Manager::TEXT, 'label_block' => \true, 'frontend_available' => \true, 'default' => '']);
        $this->end_controls_section();
    }
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $file = ABSPATH . $settings['file'];
        if ('' !== $settings['file'] && \file_exists($file)) {
            include $file;
        } elseif (current_user_can('administrator') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            if ('' === $settings['file']) {
                _e('Select the file', 'dynamic-content-for-elementor');
            } elseif (!\file_exists($file)) {
                _e('The file doesn\'t exists', 'dynamic-content-for-elementor');
            }
        }
    }
}
