<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DCE_Extension_Form_Reset extends \DynamicContentForElementor\Extensions\DCE_Extension_Prototype
{
    private $is_common = \false;
    public $has_action = \false;
    /**
     * Get Name
     *
     * Return the action name
     *
     * @access public
     * @return string
     */
    public function get_name()
    {
        return 'dce_form_reset';
    }
    /**
     * Get Label
     *
     * Returns the action label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return __('Reset', 'dynamic-content-for-elementor');
    }
    /**
     * Add Actions
     *
     * @since 0.5.5
     *
     * @access private
     */
    protected function add_actions()
    {
        add_action('elementor/widget/render_content', array($this, '_render_form'), 10, 2);
        add_action('elementor_pro/forms/render_field/reset', array($this, '_render_reset'));
        add_action('elementor/element/form/section_button_style/after_section_end', array($this, '_add_form_reset_style'));
        add_action('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
    }
    public function _render_reset($item, $item_index = 0, $form = null)
    {
        ?>
		<input type="reset" class="elementor-button-reset elementor-button elementor-size-<?php 
        echo $item['button_size'];
        ?>" value="<?php 
        echo $item['field_label'];
        ?>">
		<?php 
        return \true;
    }
    public function _render_form($content, $widget)
    {
        if ($widget->get_name() == 'form') {
            $settings = $widget->get_settings_for_display();
            $resets = \explode('elementor-field-type-reset', $content);
            if (\count($resets) > 1) {
                foreach ($resets as $rkey => $areset) {
                    if ($rkey) {
                        // remove label
                        $pieces = \explode('<label', $areset, 2);
                        if (\count($pieces) == 2) {
                            $more = \explode('</label>', \end($pieces), 2);
                            $content .= 'elementor-field-type-reset' . \reset($pieces) . \end($more);
                        } else {
                            $content .= 'elementor-field-type-reset' . $areset;
                        }
                    } else {
                        $content = $areset;
                    }
                }
            }
        }
        return $content;
    }
    public function _add_form_reset_style($element, $args = array())
    {
        if ($element->get_name() == 'form') {
            $element->start_controls_section('section_reset_button_style', ['label' => __('Reset Button', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
            $element->start_controls_tabs('tabs_reset_button_style');
            $element->start_controls_tab('tab_reset_button_normal', ['label' => __('Normal', 'dynamic-content-for-elementor')]);
            $element->add_control('reset_button_background_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-reset' => 'background-color: {{VALUE}};']]);
            $element->add_control('reset_button_text_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-reset' => 'color: {{VALUE}};', '{{WRAPPER}} .elementor-button.elementor-button-reset svg' => 'fill: {{VALUE}};']]);
            $element->add_group_control(Group_Control_Typography::get_type(), ['name' => 'reset_button_typography', 'selector' => '{{WRAPPER}} .elementor-button.elementor-button-reset']);
            $element->add_group_control(Group_Control_Border::get_type(), ['name' => 'reset_button_border', 'selector' => '{{WRAPPER}} .elementor-button.elementor-button-reset']);
            $element->add_control('reset_button_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-reset' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
            $element->add_control('reset_button_text_padding', ['label' => __('Text Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em', '%'], 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-reset' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
            $element->end_controls_tab();
            $element->start_controls_tab('tab_reset_button_hover', ['label' => __('Hover', 'dynamic-content-for-elementor')]);
            $element->add_control('reset_button_background_hover_color', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-reset:hover' => 'background-color: {{VALUE}};']]);
            $element->add_control('reset_button_hover_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-reset:hover' => 'color: {{VALUE}};']]);
            $element->add_control('reset_button_hover_border_color', ['label' => __('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-button.elementor-button-reset:hover' => 'border-color: {{VALUE}};'], 'condition' => ['reset_button_border_border!' => '']]);
            $element->end_controls_tab();
            $element->end_controls_tabs();
            $element->end_controls_section();
        }
    }
    public static function _add_to_form(Controls_Stack $element, $control_id, $control_data, $options = [])
    {
        if ($element->get_name() == 'form' && $control_id == 'form_fields') {
            $control_data['fields']['form_fields_enchanted_tab'] = array('type' => 'tab', 'tab' => 'enchanted', 'label' => '<i class="dynicon icon-dyn-logo-dce" aria-hidden="true"></i>', 'tabs_wrapper' => 'form_fields_tabs', 'name' => 'form_fields_enchanted_tab', 'condition' => ['field_type!' => 'step']);
            $control_data['fields']['field_type']['options']['reset'] = __('Reset', 'dynamic-content-for-elementor');
        }
        return $control_data;
    }
}
