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
class DCE_Extension_Form_Submit extends \DynamicContentForElementor\Extensions\DCE_Extension_Prototype
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
        return 'dce_form_submit';
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
        return __('submit', 'dynamic-content-for-elementor');
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
        add_action('elementor_pro/forms/render_field/submit', array($this, '_render_submit'));
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
            $settings['button_text'];
            // submit button text
            $submits = \explode('elementor-field-type-submit', $content);
            if (\count($submits) > 2) {
                list($more, $original) = \explode('>', \end($submits), 2);
                list($original, $more) = \explode('</div>', $original, 2);
                foreach ($submits as $skey => $asubmit) {
                    if ($skey && $skey < \count($submits)) {
                        // remove label
                        $pieces = \explode('<label', $asubmit, 2);
                        if (\count($pieces) == 2) {
                            $more = \explode('</label>', \end($pieces), 2);
                            $content .= 'elementor-field-type-submit' . \reset($pieces) . \end($more);
                        } else {
                            $content .= 'elementor-field-type-submit' . $asubmit;
                        }
                    } else {
                        if ($skey) {
                            $content = 'elementor-field-type-submit' . $asubmit;
                        } else {
                            $content = $asubmit;
                        }
                    }
                }
            }
        }
        return $content;
    }
    public function _render_submit($instance, $item_index = 0, $form = null)
    {
        $btn_class = '';
        if (!empty($instance['button_size'])) {
            $btn_class .= ' elementor-size-' . $instance['button_size'];
        }
        if (!empty($instance['button_type'])) {
            $btn_class .= ' elementor-button-' . $instance['button_type'];
        }
        if (!empty($instance['button_hover_animation'])) {
            $btn_class .= ' elementor-animation-' . $instance['button_hover_animation'];
        }
        ?>
		<button type="submit" class="elementor-button<?php 
        echo $btn_class;
        ?>">
				<span>
						<?php 
        if (!empty($instance['field_icon'])) {
            ?>
								<span class="elementor-align-icon-left elementor-button-icon">
										<?php 
            Icons_Manager::render_icon($instance['field_icon'], ['aria-hidden' => 'true']);
            ?>
										<?php 
            if (empty($instance['field_label'])) {
                ?>
												<span class="elementor-screen-only"><?php 
                _e('Submit', 'dynamic-content-for-elementor');
                ?></span>
										<?php 
            }
            ?>
								</span>
						<?php 
        }
        ?>
						<?php 
        if (!empty($instance['field_label'])) {
            ?>
								<span class="elementor-button-text"><?php 
            echo $instance['field_label'];
            ?></span>
						<?php 
        }
        ?>
				</span>
		</button>
		<?php 
        return \true;
    }
    public static function _add_to_form(Controls_Stack $element, $control_id, $control_data, $options = [])
    {
        if ($element->get_name() == 'form' && $control_id == 'form_fields') {
            $control_data['fields']['form_fields_enchanted_tab'] = array('type' => 'tab', 'tab' => 'enchanted', 'label' => '<i class="dynicon icon-dyn-logo-dce" aria-hidden="true"></i>', 'tabs_wrapper' => 'form_fields_tabs', 'name' => 'form_fields_enchanted_tab', 'condition' => ['field_type!' => 'step']);
            $control_data['fields']['field_type']['options']['submit'] = __('Submit', 'dynamic-content-for-elementor');
            $control_data['fields']['button_size'] = array('name' => 'button_size', 'label' => __('Size', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'sm', 'options' => Helper::get_button_sizes(), 'condition' => ['field_type' => ['submit', 'reset']], 'tabs_wrapper' => 'form_fields_tabs', 'inner_tab' => 'form_fields_enchanted_tab', 'tab' => 'enchanted');
        }
        return $control_data;
    }
}
