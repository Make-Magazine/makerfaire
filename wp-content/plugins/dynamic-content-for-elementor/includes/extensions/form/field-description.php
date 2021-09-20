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
class DCE_Extension_Form_description extends \DynamicContentForElementor\Extensions\DCE_Extension_Prototype
{
    private $is_common = \false;
    public $has_action = \false;
    public function get_name()
    {
        return 'dce_form_description';
    }
    public function get_label()
    {
        return __('Description', 'dynamic-content-for-elementor');
    }
    protected function add_actions()
    {
        add_action('elementor/widget/render_content', array($this, '_render_form'), 10, 2);
        add_action('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
    }
    public function _render_form($content, $widget)
    {
        if ($widget->get_name() == 'form') {
            $settings = $widget->get_settings_for_display();
            $add_css = '<style>.elementor-element.elementor-element-' . $widget->get_id() . ' .elementor-field-group { align-self: flex-start; }</style>';
            $jkey = 'dce_' . $widget->get_type() . '_form_' . $widget->get_id() . '_description';
            \ob_start();
            ?>
			<script id="<?php 
            echo $jkey;
            ?>">
			(function ($) {
				<?php 
            if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                ?>
				var <?php 
                echo $jkey;
                ?> = function ($scope, $) {
				if ($scope.hasClass("elementor-element-<?php 
                echo $widget->get_id();
                ?>")) {
					<?php 
            }
            $has_description = \false;
            foreach ($settings['form_fields'] as $key => $afield) {
                if (!empty($afield['field_description']) && $afield['field_description_position'] != 'no-description') {
                    $has_description = \true;
                    $field_description = \str_replace("'", "\\'", $afield['field_description']);
                    $field_description = \preg_replace('/\\s+/', ' ', \trim($field_description));
                    if ($afield['field_description_position'] == 'elementor-field-label') {
                        if ($afield['field_description_tooltip']) {
                            ?>
					jQuery('.elementor-element-<?php 
                            echo $widget->get_id();
                            ?> .elementor-field-group-<?php 
                            echo $afield['custom_id'];
                            ?> .elementor-field-label').addClass('dce-tooltip').addClass('elementor-field-label-description');
					jQuery('.elementor-element-<?php 
                            echo $widget->get_id();
                            ?> .elementor-field-group-<?php 
                            echo $afield['custom_id'];
                            ?> .elementor-field-label').append('<span class="dce-tooltiptext dce-tooltip-<?php 
                            echo $afield['field_description_tooltip_position'];
                            ?>"><?php 
                            echo $field_description;
                            ?></span>');
						<?php 
                        } else {
                            ?>
					jQuery('.elementor-element-<?php 
                            echo $widget->get_id();
                            ?> .elementor-field-group-<?php 
                            echo $afield['custom_id'];
                            ?> .elementor-field-label').wrap('<abbr class=\"elementor-field-label-description elementor-field-label-description-<?php 
                            echo $afield['custom_id'];
                            ?>" title="<?php 
                            echo $field_description;
                            ?>"></abbr>');
							<?php 
                        }
                    }
                    if ($afield['field_description_position'] == 'elementor-field') {
                        ?>
				  	jQuery('.elementor-element-<?php 
                        echo $widget->get_id();
                        ?> .elementor-field-group-<?php 
                        echo $afield['custom_id'];
                        ?>').append('<div class="elementor-field-input-description elementor-field-input-description-<?php 
                        echo $afield['custom_id'];
                        ?>"><?php 
                        echo $field_description;
                        ?></div>');
						<?php 
                    }
                }
            }
            if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                ?>
						}
					};
					$(window).on("elementor/frontend/init", function () {
						elementorFrontend.hooks.addAction("frontend/element_ready/form.default", <?php 
                echo $jkey;
                ?>);
					});
			<?php 
            }
            ?>
			})(jQuery, window);
		</script>
			<?php 
            $add_js = \ob_get_clean();
            if ($has_description) {
                $add_js = \DynamicContentForElementor\Assets::dce_enqueue_script($jkey, $add_js);
                return $content . $add_css . $add_js;
            }
        }
        return $content;
    }
    public static function _add_to_form(Controls_Stack $element, $control_id, $control_data, $options = [])
    {
        if (!current_user_can('administrator') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            return $control_data;
        }
        if ($element->get_name() == 'form') {
            if ($control_id == 'form_fields') {
                $control_data['fields']['form_fields_enchanted_tab'] = array('type' => 'tab', 'tab' => 'enchanted', 'label' => '<i class="dynicon icon-dyn-logo-dce" aria-hidden="true"></i>', 'tabs_wrapper' => 'form_fields_tabs', 'name' => 'form_fields_enchanted_tab', 'condition' => ['field_type!' => 'step']);
                $control_data['fields']['field_description_position'] = array('name' => 'field_description_position', 'label' => __('Description', 'dynamic-content-for-elementor'), 'separator' => 'before', 'type' => Controls_Manager::CHOOSE, 'options' => ['no-description' => ['title' => __('No Description', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-times'], 'elementor-field-label' => ['title' => __('On Label', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-tag'], 'elementor-field' => ['title' => __('Below Input', 'dynamic-content-for-elementor'), 'icon' => 'eicon-download-button']], 'toggle' => \false, 'default' => 'no-description', 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted');
                $control_data['fields']['field_description_tooltip'] = array('name' => 'field_description_tooltip', 'label' => __('Display as Tooltip', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['field_description_position' => 'elementor-field-label'], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted');
                $control_data['fields']['field_description_tooltip_position'] = array('name' => 'field_description_tooltip_position', 'label' => __('Tooltip Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['top' => ['title' => __('Top', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-up'], 'left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-left'], 'bottom' => ['title' => __('Bottom', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-down'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-angle-right']], 'toggle' => \false, 'default' => 'top', 'condition' => ['field_description_position' => 'elementor-field-label', 'field_description_tooltip!' => ''], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted');
                $control_data['fields']['field_description'] = array('name' => 'field_description', 'label' => __('Description HTML', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'label_block' => \true, 'fa4compatibility' => 'icon', 'condition' => ['field_description_position!' => 'no-description'], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted');
            }
            if ($control_id == 'field_background_color') {
                $element->add_control('field_description_color', ['label' => __('Description Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-field-input-description' => 'color: {{VALUE}};'], 'separator' => 'before']);
                $element->add_group_control(Group_Control_Typography::get_type(), ['name' => 'field_description_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .elementor-field-input-description']);
                $element->add_control('label_description_color', ['label' => __('Label Description Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-field-label-description .elementor-field-label' => 'display: inline-block;', '{{WRAPPER}} .elementor-field-label-description:after' => "\n\t\t\t\t\t\t\t\t\t\tcontent: '?';\n\t\t\t\t\t\t\t\t\t\tdisplay: inline-block;\n\t\t\t\t\t\t\t\t\t\tborder-radius: 50%;\n\t\t\t\t\t\t\t\t\t\tpadding: 2px 0;\n\t\t\t\t\t\t\t\t\t\theight: 1.2em;\n\t\t\t\t\t\t\t\t\t\tline-height: 1;\n\t\t\t\t\t\t\t\t\t\tfont-size: 80%;\n\t\t\t\t\t\t\t\t\t\twidth: 1.2em;\n\t\t\t\t\t\t\t\t\t\ttext-align: center;\n\t\t\t\t\t\t\t\t\t\tmargin-left: 0.2em;\n\t\t\t\t\t\t\t\t\t\tcolor: {{VALUE}};"], 'separator' => 'before', 'default' => '#ffffff']);
                $element->add_control('label_description_bgcolor', ['label' => __('Label Description Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .elementor-field-label-description:after' => 'background-color: {{VALUE}};'], 'default' => '#777777']);
            }
            if ($control_id == 'label_spacing') {
                $control_data['selectors']['body.rtl {{WRAPPER}} .elementor-labels-inline .elementor-field-group > abbr'] = 'padding-left: {{SIZE}}{{UNIT}};';
                // for the label position = inline option
                $control_data['selectors']['body:not(.rtl) {{WRAPPER}} .elementor-labels-inline .elementor-field-group > abbr'] = 'padding-right: {{SIZE}}{{UNIT}};';
                // for the label position = inline option
                $control_data['selectors']['body {{WRAPPER}} .elementor-labels-above .elementor-field-group > abbr'] = 'padding-bottom: {{SIZE}}{{UNIT}};';
                // for the label position = above option
            }
        }
        return $control_data;
    }
}
