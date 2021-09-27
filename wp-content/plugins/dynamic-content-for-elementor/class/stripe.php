<?php

namespace DynamicContentForElementor;

use ElementorPro\Modules\Forms\Module as Forms_Module;
if (!\defined('ABSPATH')) {
    exit;
}
class Stripe
{
    public function get_publishable_key()
    {
        if (get_option('dce_stripe_api_mode') === 'live') {
            return get_option('dce_stripe_api_publishable_key_live');
        } else {
            return get_option('dce_stripe_api_publishable_key_test');
        }
    }
    public function set_key()
    {
        if (get_option('dce_stripe_api_mode') === 'live') {
            \DynamicOOOS\Stripe\Stripe::setApiKey(get_option('dce_stripe_api_secret_key_live'));
        } else {
            \DynamicOOOS\Stripe\Stripe::setApiKey(get_option('dce_stripe_api_secret_key_test'));
        }
    }
    public function __construct()
    {
        $this->set_key();
        add_action('wp_ajax_dce_stripe_get_payment_intent', [$this, 'get_payment_intent_ajax']);
        add_action('wp_ajax_nopriv_dce_stripe_get_payment_intent', [$this, 'get_payment_intent_ajax']);
    }
    /**
     * Get form element form post_id, queried_id and form_id.
     * Code taken from Elementor Pro Ajax Handler.
     */
    public function get_form_element()
    {
        // $post_id that holds the form settings.
        $post_id = $_POST['post_id'];
        // $queried_id the post for dynamic values data.
        if (isset($_POST['queried_id'])) {
            $queried_id = $_POST['queried_id'];
        } else {
            $queried_id = $post_id;
        }
        $elementor = \Elementor\Plugin::$instance;
        // Make the post as global post for dynamic values.
        $elementor->db->switch_to_post($queried_id);
        $form_id = $_POST['form_id'];
        $document = $elementor->documents->get($post_id);
        $form = null;
        $template_id = null;
        if ($document) {
            $form = Forms_Module::find_element_recursive($document->get_elements_data(), $form_id);
        }
        if (!empty($form['templateID'])) {
            $template = $elementor->documents->get($form['templateID']);
            if (!$template) {
                return \false;
            }
            $template_id = $template->get_id();
            $form = $template->get_elements_data()[0];
        }
        $widget = $elementor->elements_manager->create_element_instance($form);
        $form['settings'] = $widget->get_settings_for_display();
        $form['settings']['id'] = $form_id;
        $form['settings']['form_post_id'] = $template_id ? $template_id : $post_id;
        // TODO: Should be removed if there is an ability to edit "global widgets"
        $form['settings']['edit_post_id'] = $post_id;
        return $form;
    }
    public function get_payment_intent_ajax()
    {
        if (!wp_verify_nonce($_POST['intent_nonce'] ?? '', 'dce-stripe-intent')) {
            wp_send_json_error(['message' => 'Nonce Error']);
        }
        $form = $this->get_form_element();
        if (empty($form)) {
            wp_send_json_error(['message' => 'Invalid Form']);
        }
        $field_settings = $form['settings']['form_fields'][$_POST['field_index']] ?? \false;
        if ($field_settings === \false) {
            wp_send_json_error(['message' => 'Invalid Form']);
        }
        $client_secret = $this->make_payment_intent($form, $field_settings);
        if ($client_secret === \false) {
            wp_send_json_error(['message' => 'Stripe Authentication Error']);
        }
        wp_send_json_success(['client_secret' => $client_secret]);
    }
    /** Example: 10, USD will return 1000. 10, YEN will return 10. */
    public function get_amount_in_currency_smallest_unit(float $amount, string $currency_code)
    {
        $iso4217 = new \DynamicOOOS\Payum\ISO4217\ISO4217();
        $currency = $iso4217->findByAlpha3($currency_code);
        $exponent = $currency->getExp();
        return \intval($amount * \pow(10, $exponent));
    }
    public function make_stripe_customer($item)
    {
        $customer = ['name' => $item['dce_stripe_customer_name_field_id'] ?? '', 'email' => $item['dce_stripe_customer_email_field_id'] ?? '', 'phone' => $item['dce_stripe_customer_phone_field_id'] ?? ''];
        $customer = \array_filter($customer, function ($id) {
            return !empty($id);
        });
        $customer = \array_map(function ($id) {
            return $_POST['form_fields'][$id];
        }, $customer);
        return \DynamicOOOS\Stripe\Customer::create($customer);
    }
    private function expand_description_form_tokens($description)
    {
        return \preg_replace_callback('/\\[form:([^\\]]+)\\]/', function ($matches) {
            return $_POST['form_fields'][$matches[1] ?? ''] ?? '';
        }, $description);
    }
    public function make_payment_intent($form, $item)
    {
        // We might get the amount from another field, or statically.
        if (($item['dce_form_stripe_value_from_field'] ?? '') === 'yes') {
            $field_id = $item['dce_form_stripe_value_field_id'] ?? '';
            $amount = $_POST['form_fields'][$field_id] ?? \false;
            if ($amount === \false) {
                wp_send_json_error(['message' => __('Could not find the Amount field.', 'dynamic-content-for-elementor')]);
            }
        } else {
            $amount = $item['dce_form_stripe_item_value'];
        }
        $amount = (float) $amount;
        if ($amount <= 0) {
            wp_send_json_error(['message' => 'Invalid amount given']);
        }
        $amount = $this->get_amount_in_currency_smallest_unit($amount, $item['dce_form_stripe_currency']);
        $intent_data = ['amount' => $amount, 'currency' => $item['dce_form_stripe_currency'], 'confirmation_method' => 'automatic', 'confirm' => \false, 'capture_method' => 'manual', 'description' => $this->expand_description_form_tokens($item['dce_form_stripe_item_description'] ?? ''), 'metadata' => ['dce_id' => $form['settings']['id'] . '-' . $item['custom_id'], 'sku' => $item['dce_form_stripe_item_sku'] ?? '']];
        if (($item['dce_stripe_future_usage'] ?? '') === 'yes') {
            $intent_data['setup_future_usage'] = 'off_session';
        }
        if (($item['dce_stripe_attach_customer'] ?? '') === 'yes') {
            $customer = $this->make_stripe_customer($item);
            $intent_data['customer'] = $customer->id;
        }
        try {
            $intent = \DynamicOOOS\Stripe\PaymentIntent::create($intent_data);
            return $intent->client_secret;
        } catch (\DynamicOOOS\Stripe\Exception\AuthenticationException $e) {
            return \false;
        }
    }
}
