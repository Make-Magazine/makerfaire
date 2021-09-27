<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class DCE_Widget_DoShortcode extends \DynamicContentForElementor\Widgets\DCE_Widget_Prototype
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
            $this->_register_controls_content();
        } elseif (!current_user_can('administrator') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $this->register_controls_non_admin_notice();
        }
    }
    protected function _register_controls_content()
    {
        $this->start_controls_section('section_doshortcode', ['label' => __('DoShortcode', 'dynamic-content-for-elementor')]);
        $this->add_control('doshortcode_string', ['label' => __('Shortcode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'description' => __('Example:', 'dynamic-content-for-elementor') . ' [gallery ids="66,67,28"]']);
        $this->end_controls_section();
    }
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $doshortcode_string = $settings['doshortcode_string'];
        if ($doshortcode_string != '') {
            echo do_shortcode($doshortcode_string);
        }
    }
}
