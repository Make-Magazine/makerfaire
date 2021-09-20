<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class DCE_Widget_RawPhp extends \DynamicContentForElementor\Widgets\DCE_Widget_Prototype
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
        $this->start_controls_section('section_rawphp', ['label' => __('PHP Raw', 'dynamic-content-for-elementor')]);
        $this->add_control('custom_php', ['label' => __('Custom PHP', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'language' => 'php']);
        $this->end_controls_section();
    }
    protected function render()
    {
        $settings = $this->get_settings_for_display(null, \true);
        if (current_user_can('administrator') && \Elementor\Plugin::$instance->editor->is_edit_mode() && empty($settings['custom_php'])) {
            echo __('Add your Custom PHP Code to begin.', 'dynamic-content-for-elementor');
        } elseif (!current_user_can('administrator') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $this->render_non_admin_notice();
        } elseif (current_user_can('administrator') && \Elementor\Plugin::$instance->editor->is_edit_mode() && !empty($settings['custom_php']) || !is_admin() && !empty($settings['custom_php'])) {
            $evalError = \false;
            // The following is needed because if the code echoes only a
            // '0', Elementor will not render the widget at all.
            echo '<!-- Dynamic PHP Raw -->';
            try {
                @eval($settings['custom_php']);
            } catch (\ParseError $e) {
                $evalError = \true;
            } catch (\Throwable $e) {
                $evalError = \true;
            }
            if ($evalError && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<strong>';
                echo __('Please check your PHP code', 'dynamic-content-for-elementor');
                echo '</strong><br />';
                echo 'ERROR: ', $e->getMessage(), "\n";
            }
        }
    }
}
