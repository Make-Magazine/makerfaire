<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use ElementorPro\Modules\Forms\Fields;
use Elementor\Widget_Base;
use ElementorPro\Modules\Forms\Classes;
use ElementorPro\Modules\Forms\Widgets\Form;
use ElementorPro\Plugin;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DCE_Extension_Form_Regex extends \DynamicContentForElementor\Extensions\DCE_Extension_Prototype
{
    public static $depended_plugins = ['elementor-pro'];
    public static $docs = 'https://www.dynamic.ooo/';
    private $is_common = \false;
    public $has_action = \false;
    public function get_name()
    {
        return 'dce_form_regex';
    }
    public function get_label()
    {
        return __('Regex Field for Elementor Pro Form', 'dynamic-content-for-elementor');
    }
    protected function add_actions()
    {
        add_action('elementor/widget/render_content', array($this, '_render_form'), 10, 2);
    }
    public function _render_form($content, $widget)
    {
        if ($widget->get_name() == 'form') {
            $settings = $widget->get_settings_for_display();
            foreach ($settings['form_fields'] as $key => $afield) {
                if ($afield['field_type'] == 'text' && !empty($afield['field_regex'])) {
                    $content = \str_replace('id="form-field-' . $afield['custom_id'] . '"', 'id="form-field-' . $afield['custom_id'] . '" data-regex="true" pattern="' . $afield['field_regex'] . '"', $content);
                }
            }
        }
        return $content;
    }
    public static function _add_to_form(Controls_Stack $element, $control_id, $control_data, $options = [])
    {
        if ($element->get_name() == 'form' && $control_id == 'form_fields') {
            $control_data['fields']['form_fields_enchanted_tab'] = array('type' => 'tab', 'tab' => 'enchanted', 'label' => '<i class="dynicon icon-dyn-logo-dce" aria-hidden="true"></i>', 'tabs_wrapper' => 'form_fields_tabs', 'name' => 'form_fields_enchanted_tab', 'condition' => ['field_type!' => 'step']);
            $control_data['fields']['field_regex'] = array('name' => 'field_regex', 'label' => __('Regex', 'dynamic-content-for-elementor'), 'description' => __('A regular expression is a sequence of characters that define a pattern. Use it to restrict the characters permitted on this field.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'separator' => 'before', 'return_value' => 'true', 'conditions' => ['terms' => [['name' => 'field_type', 'operator' => 'in', 'value' => ['text', 'textarea', 'email', 'url', 'password']]]], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted');
        }
        return $control_data;
    }
}
