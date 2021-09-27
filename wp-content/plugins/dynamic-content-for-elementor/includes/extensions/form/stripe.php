<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Stripe;
use ElementorPro\Modules\Forms\Fields;
use ElementorPro\Modules\Forms\Classes;
use ElementorPro\Modules\Forms\Widgets\Form;
use ElementorPro\Plugin;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DCE_Extension_Form_Stripe extends \ElementorPro\Modules\Forms\Fields\Field_Base
{
    private $is_common = \false;
    public $has_action = \false;
    public $depended_scripts = ['dce-stripe'];
    private static $validated_intents = [];
    public function __construct()
    {
        add_action('elementor/widget/print_template', function ($template, $widget) {
            if ('form' === $widget->get_name()) {
                $template = \false;
            }
            return $template;
        }, 10, 2);
        parent::__construct();
    }
    public function get_script_depends()
    {
        return $this->depended_scripts;
    }
    public function get_name()
    {
        return 'Stripe';
    }
    public function get_label()
    {
        return __('Stripe', 'dynamic-content-for-elementor');
    }
    public function get_type()
    {
        return 'dce_form_stripe';
    }
    public function get_style_depends()
    {
        return $this->depended_styles;
    }
    public function update_controls($widget)
    {
        $elementor = Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $currencies = ['USD', 'AUD', 'BRL', 'GBP', 'CAD', 'CHF', 'CNY', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'INR', 'JPY', 'MYR', 'MXN', 'TWD', 'NZD', 'NOK', 'PHP', 'PLN', 'RUB', 'SGD', 'SEK', 'CHF', 'THB'];
        $currencies_options = [];
        foreach ($currencies as $curr) {
            $currencies_options[$curr] = $curr;
        }
        if (\Elementor\Plugin::$instance->editor->is_edit_mode() && !current_user_can('administrator')) {
            $field_controls = ['admin_notice' => ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => '<div class="elementor-panel-alert elementor-panel-alert-warning">' . __('You will need administrator capabilities to edit this form field.', 'dynamic-content-for-elementor') . '</div>', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]]];
        } else {
            $field_controls = ['dce_form_stripe_currency' => ['name' => 'dce_form_stripe_currency', 'label' => __('Transaction Currency', 'dynamic-content-for-elementor'), 'description' => __('The currency of this transaction.', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SELECT, 'options' => $currencies_options, 'default' => 'USD', 'label_block' => 'true', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_form_stripe_value_from_field' => ['name' => 'dce_form_stripe_value_from_field', 'label' => __('Get Amount dynamically from another field in the form', 'dynamic-content-for-elementor'), 'label_block' => \true, 'default' => 'no', 'type' => \Elementor\Controls_Manager::SWITCHER, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'dce_form_stripe_item_value' => ['name' => 'dce_form_stripe_item_value', 'label' => __('Transaction Amount', 'dynamic-content-for-elementor'), 'description' => __('Amount intended to be collected by this transaction in the currency unit', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '10.99', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'dynamic' => ['active' => \true], 'condition' => ['field_type' => $this->get_type(), 'dce_form_stripe_value_from_field!' => 'yes']], 'dce_form_stripe_value_field_id' => ['name' => 'dce_form_stripe_value_field_id', 'label' => __('Amount Field ID', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_form_stripe_value_from_field' => 'yes']], 'dce_stripe_attach_customer' => ['name' => 'dce_stripe_attach_customer', 'label' => __('Attach Customer Information to the Payment', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::SWITCHER, 'tab' => 'content', 'separator' => 'before', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type()]], 'admin_notice' => ['name' => 'admin_notice', 'type' => Controls_Manager::RAW_HTML, 'raw' => '<div class="elementor-control-field-description">' . __('The customer information taken from other fields will be attached to the payment and available in the Stripe Panel. Please insert the field IDs associated with each information, leave blank if not available. Notice that he Customer will be duplicated if they make multiple payments.', 'dynamic-content-for-elementor') . '</div>', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_attach_customer' => 'yes']], 'dce_stripe_future_usage' => ['name' => 'dce_stripe_future_usage', 'label' => __('Save the Customer payment method for future usage', 'dynamic-content-for-elementor'), 'description' => __('Associate the paying method with the Customer. You can then create recurring payments and subscriptions in the Stripe Panel. Customer email is required.', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::SWITCHER, 'tab' => 'content', 'separator' => 'before', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_attach_customer' => 'yes']], 'dce_stripe_customer_email_field_id' => ['name' => 'dce_stripe_customer_email_field_id', 'label' => __('Customer Email Field', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_attach_customer' => 'yes']], 'dce_stripe_customer_name_field_id' => ['name' => 'dce_stripe_customer_name_field_id', 'label' => __('Customer Full Name Field', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_attach_customer' => 'yes']], 'dce_stripe_customer_phone_field_id' => ['name' => 'dce_stripe_customer_phone_field_id', 'separator' => 'after', 'label' => __('Customer Phone Number Field', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => \Elementor\Controls_Manager::TEXT, 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'condition' => ['field_type' => $this->get_type(), 'dce_stripe_attach_customer' => 'yes']], 'dce_form_stripe_item_description' => ['name' => 'dce_form_stripe_item_description', 'label' => __('Item Description', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'placeholder' => __('Item Description', 'dynamic-content-for-elementor'), 'description' => __('You can also use tokens like [form:fieldid] to refer to other fields.', 'dynamic-content-for-elementor'), 'label_block' => 'true', 'default' => '', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'dynamic' => ['active' => 1], 'condition' => ['field_type' => $this->get_type()]], 'dce_form_stripe_item_sku' => ['name' => 'dce_form_stripe_item_sku', 'label' => __('Item SKU', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'placeholder' => __('Item SKU', 'dynamic-content-for-elementor'), 'label_block' => 'true', 'default' => '', 'tab' => 'content', 'inner_tab' => 'form_fields_content_tab', 'tabs_wrapper' => 'form_fields_tabs', 'dynamic' => ['active' => 1], 'condition' => ['field_type' => $this->get_type()]]];
        }
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
    public function render($item, $item_index, $form)
    {
        $stripe = \DynamicContentForElementor\Plugin::instance()->stripe;
        $stripe_key = $stripe->get_publishable_key();
        wp_add_inline_script('dce-stripe', 'dceStripePublishableKey = "' . \addslashes($stripe_key) . '";', 'before');
        $form->add_render_attribute('input' . $item_index, 'type', 'hidden', \true);
        $form->add_render_attribute('dce_stripe' . $item_index, 'data-required', $item['required'] ? 'true' : 'false', \true);
        $form->add_render_attribute('dce_stripe' . $item_index, 'data-field-index', $item_index, \true);
        $intent_url = admin_url('admin-ajax.php?action=dce_stripe_get_payment_intent');
        $nonce = wp_create_nonce('dce-stripe-intent');
        $form->add_render_attribute('dce_stripe' . $item_index, 'data-intent-url', $intent_url, \true);
        $form->add_render_attribute('dce_stripe' . $item_index, 'data-nonce', $nonce, \true);
        $form->add_render_attribute('dce_stripe' . $item_index, 'style', 'padding-top: 10px;', \true);
        $form->add_render_attribute('dce_stripe' . $item_index, 'class', 'elementor-field elementor-field-textual dce-stripe-elements', \true);
        echo '<input ' . $form->get_render_attribute_string('input' . $item_index) . '>';
        echo '<span class="stripe-error elementor-message elementor-message-danger elementor-help-inline elementor-form-help-inline" role="alert" style="display: none;"></span>';
        echo '<div ' . $form->get_render_attribute_string('dce_stripe' . $item_index) . '></div>';
    }
    public function process_field($field, Classes\Form_Record $record, Classes\Ajax_Handler $ajax_handler)
    {
        $error_msg = __('There was an error while completing the payment, please try again later or contact the merchant directly.', 'dynamic-content-for-elementor');
        $id = $field['id'];
        $intent_id = $field['value'];
        if (empty($intent_id)) {
            // Value is not allowed to be empty when field is required. So
            // if empty then the field is not required and no validation is
            // needed.
            return;
        }
        if (isset(self::$validated_intents[$intent_id])) {
            return;
            // good, already validated.
        }
        try {
            $intent = \DynamicOOOS\Stripe\PaymentIntent::retrieve($intent_id);
            $dce_id_expected = $record->get_form_settings('id') . '-' . $id;
            // we make sure the payment intent was created by this stripe
            // field and not elsewhere:
            if ($intent->metadata['dce_id'] !== $dce_id_expected) {
                $ajax_handler->add_error($id, $error_msg);
                return;
            }
            $intent->capture();
            if ('succeeded' !== $intent->status) {
                $ajax_handler->add_error($id, $error_msg);
            } else {
                self::$validated_intents[$intent_id] = \true;
            }
        } catch (\DynamicOOOS\Stripe\Exception\InvalidRequestException $e) {
            $ajax_handler->add_error($id, $error_msg);
        }
    }
}
