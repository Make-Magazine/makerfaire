<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class ConfirmDialog extends \DynamicContentForElementor\Extensions\DCE_Extension_Prototype
{
    private static $actions_added = \false;
    public static $depended_plugins = ['elementor-pro'];
    private $is_common = \false;
    public $has_action = \false;
    public $depended_scripts = ['dce-confirm-dialog'];
    public $depended_styles = ['dce-jquery-confirm'];
    public function get_label()
    {
        return __('Confirm Dialog', 'dynamic-content-for-elementor');
    }
    public function add_assets_depends($instance, $form)
    {
        if ('yes' === $instance['dce_confirm_dialog_enabled']) {
            foreach ($this->depended_scripts as $script) {
                $form->add_script_depends($script);
            }
            foreach ($this->depended_styles as $style) {
                $form->add_style_depends($style);
            }
        }
    }
    protected function add_actions()
    {
        if (self::$actions_added) {
            return;
        }
        self::$actions_added = \true;
        add_action('elementor-pro/forms/pre_render', [$this, 'add_assets_depends'], 10, 2);
        add_action('elementor/element/form/section_buttons/after_section_end', [$this, 'add_controls_to_form']);
    }
    public function add_controls_to_form($widget)
    {
        if (!current_user_can('administrator') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            return;
        }
        $widget->start_controls_section('section_dce_confirm_dialog', ['label' => '<span class="color-dce icon icon-dyn-logo-dce pull-right ml-1"></span> ' . __('Confirm Dialog before Submit', 'dynamic-content-for-elementor')]);
        $widget->add_control('dce_confirm_dialog_enabled', ['label' => __('Confirm Dialog', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'no', 'frontend_available' => \true]);
        $widget->add_control('dce_confirm_dialog_title', ['label' => __('Title', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Confirm', 'label_block' => 'true', 'description' => __('You can use the same syntax as in Dynamic.ooo Live HTML field.', 'dynamic-content-for-elementor'), 'condition' => ['dce_confirm_dialog_enabled' => 'yes'], 'frontend_available' => \true]);
        $widget->add_control('dce_confirm_dialog_content', ['label' => __('Content', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::CODE, 'default' => 'Hi {{ form.name }}, please confirm submission', 'language' => 'html', 'label_block' => 'true', 'description' => __('HTML code to be displayed in the modal. You can use the same syntax as in Dynamic.ooo Live HTML field.', 'dynamic-content-for-elementor'), 'condition' => ['dce_confirm_dialog_enabled' => 'yes'], 'frontend_available' => \true]);
        $widget->add_control('dce_confirm_dialog_theme', ['label' => __('Theme', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'light', 'options' => ['light' => __('Light', 'dynamic-content-for-elementor'), 'dark' => __('Dark', 'dynamic-content-for-elementor'), 'material' => 'Material', 'supervan' => 'Supervan', 'bootstrap' => 'Bootstrap'], 'separator' => 'after', 'label_block' => 'true', 'condition' => ['dce_confirm_dialog_enabled' => 'yes'], 'frontend_available' => \true]);
        $widget->end_controls_section();
    }
}
