<?php

namespace DynamicContentForElementor\Extensions;

use DynamicOOOS\Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
use ElementorPro\Plugin;
use ElementorPro\Modules\Forms\Fields\Field_Base;
use Elementor\Repeater;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class ConditionalFieldsV2 extends \DynamicContentForElementor\Extensions\DCE_Extension_Prototype
{
    private $is_common = \false;
    public $has_action = \false;
    public $depended_scripts = ['dce-conditional-fields'];
    public $depended_styles = ['dce-conditional-fields'];
    public function get_name()
    {
        return 'dce_conditional_fields_v2';
    }
    public function get_label()
    {
        return __('Conditional Fields v2', 'dynamic-content-for-elementor');
    }
    /**
     * Rewrite the expression so that each line are logically connected
     *  with an `and`.
     */
    private static function and_join_lines($expr)
    {
        $lines = \preg_split('/\\r\\n|\\r|\\n/', $expr);
        $lines = \array_filter($lines, function ($l) {
            return !\preg_match('/^\\s*$/', $l);
            // filter empty lines
        });
        return '(' . \implode(')&&(', $lines) . ')';
    }
    private static function are_conditions_enabled($field)
    {
        $enabled = $field['dce_field_conditions_mode'] === 'show' || $field['dce_field_conditions_mode'] === 'hide';
        return $enabled && !\preg_match('/^\\s*$/', $field['dce_conditions_expression']);
    }
    private function get_fields_conditions($instance)
    {
        $conditions = [];
        foreach ($instance['form_fields'] as $field) {
            if (self::are_conditions_enabled($field)) {
                $conditions[] = ['id' => $field['custom_id'], 'condition' => self::and_join_lines($field['dce_conditions_expression']), 'mode' => $field['dce_field_conditions_mode'], 'disableOnly' => $field['dce_conditions_disable_only'] === 'yes'];
            }
        }
        return $conditions;
    }
    private function get_submit_conditions($instance)
    {
        $conditions = [];
        foreach ($instance['dce_conditional_validations'] as $validation) {
            if ($validation['disabled'] !== 'yes' && $validation['hide_submit'] === 'yes') {
                $conditions[] = $validation['expression'];
            }
        }
        return $conditions;
    }
    public function add_assets_depends($instance, $form)
    {
        // fetch all the settings data we need to pass to the JavaScript code:
        $field_ids = [];
        foreach ($instance['form_fields'] as $field) {
            $field_ids[] = $field['custom_id'];
        }
        $field_conditions = $this->get_fields_conditions($instance);
        $submit_conditions = $this->get_submit_conditions($instance);
        $enabled = \false;
        if (!empty($field_conditions)) {
            $form->add_render_attribute('wrapper', 'data-field-conditions', wp_json_encode($field_conditions));
            $enabled = \true;
        }
        if (!empty($submit_conditions)) {
            $form->add_render_attribute('wrapper', 'data-submit-conditions', wp_json_encode($submit_conditions));
            $enabled = \true;
        }
        if ($enabled) {
            $form->add_render_attribute('wrapper', 'data-field-ids', wp_json_encode($field_ids));
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
        add_action('elementor/element/form/section_form_fields/before_section_end', [$this, 'update_fields_controls']);
        add_action('elementor/element/form/section_buttons/after_section_end', [$this, 'update_validation_controls']);
        add_action('elementor-pro/forms/pre_render', [$this, 'add_assets_depends'], 10, 2);
        // very low priority because it needs to fix validation of other validation hooks.
        add_action('elementor_pro/forms/validation', [$this, 'validation'], 1000, 2);
    }
    public function update_validation_controls($widget)
    {
        $widget->start_controls_section('section_conditional_validation', ['label' => '<span class="color-dce icon icon-dyn-logo-dce pull-right ml-1"></span> ' . __('Conditional Validation', 'dynamic-content-for-elementor')]);
        $repeater = new \Elementor\Repeater();
        $repeater->add_control('disabled', ['label' => __('Disable', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER]);
        $repeater->add_control('expression', ['label' => __('Expression', 'dynamic-content-for-elementor'), 'description' => __('One condition per line. All conditions are and-connected. Conditions are expressions that can also use the or operator and much more! You can use our online tool <a href="https://www.dynamic.ooo/conditions-generator/">Conditions Generator</a> to generate your conditions more easily.', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'label_block' => \true]);
        $repeater->add_control('error_message', ['label' => __('Error Message', 'dynamic-content-for-elementor'), 'default' => __('Form Validation Error', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => \true]);
        $repeater->add_control('error_field_id', ['label' => __('Field ID to attach the error to (optional)', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => \true]);
        $repeater->add_control('hide_submit', ['label' => __('Also hide the Submit button if the condition is not satisfied', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'label_block' => \true]);
        $widget->add_control('dce_conditional_validations', ['label' => __('Conditional Validations', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'default' => [['disabled' => 'yes', 'error_message' => 'Your name should be Joe', 'expression' => 'name == "Joe"', 'error_field_id' => 'name']], 'title_field' => 'Condition {{ error_field_id }}', 'hide_submit' => 'no']);
        $widget->end_controls_section();
    }
    public function update_fields_controls($widget)
    {
        $elementor = Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = ['form_fields_conditions_tab' => ['type' => 'tab', 'tab' => 'content', 'label' => __('Conditions', 'dynamic-content-for-elementor'), 'conditions' => ['terms' => [['name' => 'field_type', 'operator' => '!in', 'value' => ['hidden', 'step']]]], 'tabs_wrapper' => 'form_fields_tabs', 'name' => 'form_fields_conditions_tab'], 'dce_field_conditions_mode' => ['name' => 'dce_field_conditions_mode', 'label' => __('Condition', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['visible' => ['title' => __('Always Visible', 'dynamic-content-for-elementor'), 'icon' => 'eicon-check'], 'show' => ['title' => __('Show if', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-eye'], 'hide' => ['title' => __('Hide if', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-eye-slash']], 'toggle' => \false, 'default' => 'visible', 'tab' => 'content', 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_conditions_tab'], 'dce_conditions_expression' => ['name' => 'dce_conditions_expression', 'type' => \Elementor\Controls_Manager::TEXTAREA, 'label' => __('Conditions Expressions', 'dynamic-content-for-elementor'), 'description' => __('One condition per line. All conditions are and-connected. Conditions are expressions that can also use the or operator and much more! You can use our online tool <a href="https://www.dynamic.ooo/conditions-generator/">Conditions Generator</a> to generate your conditions more easily.', 'dynamic-content-for-elementor'), 'placeholder' => "name == 'Joe'", 'condition' => ['dce_field_conditions_mode!' => 'visible'], 'tab' => 'content', 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_conditions_tab'], 'dce_conditions_disable_only' => ['name' => 'dce_conditions_disable_only', 'label' => __('Disable only', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'tab' => 'content', 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_conditions_tab', 'condition' => ['dce_field_conditions_mode!' => 'visible']]];
        $control_data['fields'] = \array_merge($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    /**
     * Determine all field visibilities based on the conditions.
     */
    public function determine_visibilities($conditions, $values)
    {
        $lang = new ExpressionLanguage();
        $visibility = [];
        // Assume they are all visible at the beginning:
        foreach ($conditions as $id => $_) {
            $visibility[$id] = \true;
        }
        foreach ($conditions as $id => $condition) {
            $res = $lang->evaluate($condition['condition'], $values);
            $res = $condition['mode'] === 'show' ? $res : !$res;
            if (!$res) {
                // we don't want an inactive field value to influence
                // further conditions:
                $values[$id] = '';
            }
            $visibility[$id] = $res;
        }
        return $visibility;
    }
    /** Return an array with key field id and value its raw_value */
    private function get_field_values($record)
    {
        $raw_fields = $record->get_field([]);
        $values = [];
        foreach ($raw_fields as $field) {
            $values[$field['id']] = $field['raw_value'];
        }
        return $values;
    }
    // Returns true if there are errors on the form ajax
    // handler. Unfortunately this doesn't work if set_success is used
    // directly, however this does not occur anywhere neither in Elementor pro
    // nor DCE.
    private static function ajax_handler_has_errors($ajax_handler)
    {
        $has_error = \false;
        $has_error |= !empty($ajax_handler->errors);
        $has_error |= !empty($ajax_handler->messages['error']);
        $has_error |= !empty($ajax_handler->messages['admin_error']);
        return $has_error;
    }
    /**
     * Remove validation errors related to fields that are required but
     * that have been hidden by a condition.
     */
    public function fix_validation($record, $ajax_handler)
    {
        $conditions = [];
        $values = $this->get_field_values($record);
        foreach ($record->get_form_settings('form_fields') as $field) {
            if (self::are_conditions_enabled($field)) {
                $conditions[$field['custom_id']] = ['condition' => self::and_join_lines($field['dce_conditions_expression']), 'mode' => $field['dce_field_conditions_mode']];
            }
        }
        $visibilities = $this->determine_visibilities($conditions, $values);
        foreach ($visibilities as $id => $visible) {
            if (!$visible) {
                if (!empty($values[$id])) {
                    $ajax_handler->add_admin_error_message(__("Conditional Fields Error: When you have a field where you can pick multiple items, like checkbox or the select field with multiple select active, you must use the operator in. If instead you have a field where you pick only one value (like Select) you most likely want to use == and not in. Check the docs if unsure.", 'dynamic-content-for-elementor'));
                } else {
                    // Remove potential validation error related to the field:
                    unset($ajax_handler->errors[$id]);
                }
            }
        }
        // if there are no errors then the form is actually good.
        if (!$this->ajax_handler_has_errors($ajax_handler)) {
            $ajax_handler->set_success(\true);
        }
    }
    public function conditional_validation($record, $ajax_handler)
    {
        $lang = new ExpressionLanguage();
        $values = $this->get_field_values($record);
        $validations = $record->get_form_settings('dce_conditional_validations');
        foreach ($validations as $validation) {
            if ($validation['disabled'] !== 'yes') {
                try {
                    $res = $lang->evaluate($validation['expression'], $values);
                } catch (\DynamicOOOS\Symfony\Component\ExpressionLanguage\SyntaxError $e) {
                    $ajax_handler->add_admin_error_message(__('Conditional validation error: ', 'dynamic-content-for-elementor') . $e->getMessage());
                    return;
                }
                if (!$res) {
                    if ($validation['error_field_id']) {
                        $ajax_handler->add_error($validation['error_field_id'], $validation['error_message']);
                    } else {
                        $ajax_handler->add_error_message($validation['error_message']);
                    }
                }
            }
        }
    }
    public function validation($record, $ajax_handler)
    {
        $this->conditional_validation($record, $ajax_handler);
        $this->fix_validation($record, $ajax_handler);
    }
}
