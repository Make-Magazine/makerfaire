<?php

namespace DynamicContentForElementor;

use Elementor\Controls_Manager;
if (!\defined('ABSPATH')) {
    exit;
}
class Extensions
{
    public static $extensions = [];
    public static $registered_extensions = [];
    public static $groups;
    public static $namespace = '\\DynamicContentForElementor\\Extensions\\';
    public function __construct()
    {
        self::$extensions = self::get_extensions_info();
        add_action('admin_notices', [__CLASS__, 'warning_old_conditional']);
    }
    public static function warning_old_conditional()
    {
        if (isset($_POST['save-dce-feature'])) {
            return;
            // settings are being saved, we can't be sure of the extension status.
        }
        $excluded = self::get_excluded_extensions();
        if (!($excluded['DCE_Extension_Form_Visibility'] ?? \false)) {
            $msg = __('It appears that the extension Conditional Fields (old version) for Elementor Pro Form is enabled. Notice that this is a legacy extension that is known to cause problems with form validation. We recommend disabling it if you don’t need it. You can do it from the ', 'dynamic-content-for-elementor');
            $url = admin_url('admin.php?page=dce-features&tab=legacy');
            $msg .= "<a href='{$url}'>" . __('Features Dashboard', 'dynamic-content-for-elementor') . '</a>.';
            $msg .= ' ' . __('You can use the new version instead: Conditional Fields v2 for Elementor Pro Form.', 'dynamic-content-for-elementor');
            \DynamicContentForElementor\Notice::dce_admin_notice__danger($msg);
        }
    }
    public static function init_group()
    {
        self::$groups = ['FORM' => __('Elementor Pro Form', 'dynamic-content-for-elementor'), 'COMMON' => __('Widgets', 'dynamic-content-for-elementor')];
    }
    public static function get_extensions_info()
    {
        return ['DCE_Extension_Animations' => ['name' => 'dce_extension_animations', 'category' => ['COMMON'], 'title' => __('Dynamic Animations', 'dynamic-content-for-elementor'), 'description' => __('Predefined CSS-Animations with keyframe', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-animations', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/loop-animations/'], 'DCE_Extension_CopyPaste' => ['name' => 'dce_extension_copypaste', 'category' => ['COMMON'], 'title' => __('Copy&Paste Cross Sites', 'dynamic-content-for-elementor'), 'description' => __('Copy and Paste any element from one site to another', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-copy-paste', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/copy-paste-cross-site/'], 'DCE_Extension_Editor' => ['name' => 'dce_extension_editor', 'category' => ['COMMON'], 'title' => __('Select2 for Elementor Editor', 'dynamic-content-for-elementor'), 'description' => __('Select2 gives you a select box with support for searching in the Elementor Backend Editor', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-select2-editor', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/select2-elementor-editor/'], 'DCE_Extension_Masking' => ['name' => 'dce_extension_masking', 'category' => ['COMMON'], 'title' => __('Advanced Masking Controls', 'dynamic-content-for-elementor'), 'description' => __('Advanced Masking features for Image, Image-box and Video Widgets', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-advanced-masking', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/advanced-masking/'], 'DCE_Extension_Rellax' => ['name' => 'dce_extension_rellax', 'category' => ['COMMON'], 'title' => __('Rellax', 'dynamic-content-for-elementor'), 'description' => __('Rellax Parallax rules for widgets and rows', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-rellax', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/rellax-parallax/'], 'DCE_Extension_Reveal' => ['name' => 'dce_extension_reveal', 'category' => ['COMMON'], 'title' => __('Reveal', 'dynamic-content-for-elementor'), 'description' => __('Reveal animation on-scroll for Widgets', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-reveal', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/scroll-reveals/'], 'DCE_Extension_Template' => ['name' => 'dce_extension_template', 'category' => ['COMMON'], 'title' => __('Dynamic Tag - Template', 'dynamic-content-for-elementor'), 'description' => __('Add support for Template in Dynamic Tag for Text, HTML and Textarea settings', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-template', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/dynamic-tag-template'], 'DCE_Extension_Token' => ['name' => 'dce_extension_token', 'category' => ['COMMON'], 'title' => __('Dynamic Tag - Tokens', 'dynamic-content-for-elementor'), 'description' => __('Add support for Tokens in Dynamic Tag for Text, Number and Textarea settings', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-tokens', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/dynamic-tag-token/'], 'DCE_Extension_Transforms' => ['name' => 'dce_extension_transforms', 'category' => ['COMMON'], 'title' => __('Transforms', 'dynamic-content-for-elementor'), 'description' => __('Apply CSS Transforms to Element', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-transforms', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/transforms/'], 'DCE_Extension_Unwrap' => ['name' => 'dce_extension_unwrap', 'category' => ['COMMON'], 'title' => __('Unwrap', 'dynamic-content-for-elementor'), 'description' => __('Remove extra Elementor wrapper divs', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-unwrap', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/unwrap/'], 'DCE_Extension_Video' => ['name' => 'dce_extension_video', 'category' => ['COMMON'], 'title' => __('Advanced Video Controls', 'dynamic-content-for-elementor'), 'description' => __('Advanced Video features for Elementor Video Widget', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-advanced-video-controls', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/advanced-video-controls/'], 'DCE_Extension_Visibility' => ['name' => 'dce_extension_visibility', 'category' => ['COMMON'], 'title' => __('Dynamic Visibility', 'dynamic-content-for-elementor'), 'description' => __('Visibility rules for widgets, rows, columns, and sections', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-visibility', 'plugin_depends' => '', 'doc_url' => 'https://www.dynamic.ooo/widget/dynamic-visibility-for-elementor/'], 'DCE_Extension_Form_Address_Autocomplete' => ['name' => 'dce_extension_form_address_autocomplete', 'category' => ['FORM'], 'title' => __('Address Autocomplete for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Use autocomplete to give your form fields the type-ahead-search behaviour of the Google Maps search field. The autocomplete service can match on full words and substrings, resolving place names and addresses', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-autocomplete-address', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/address-autocomplete-for-elementor-pro-form/'], 'DCE_Extension_Form_Amount' => ['name' => 'dce_extension_form_amount', 'category' => ['FORM'], 'title' => __('Amount Field for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Add Amount Field to Elementor Pro Form', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-amount', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/amount-elementor-pro-form/'], 'DCE_Extension_Form_description' => ['name' => 'dce_extension_form_description', 'category' => ['FORM'], 'title' => __('Field Description for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Describe your form field to help users better understand each field. You can add a tooltip or insert text', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-description', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/field-description-elementor-pro-form/'], 'DCE_Extension_Form_Email' => ['name' => 'dce_extension_form_email', 'category' => ['FORM'], 'title' => __('Dynamic Email for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Add Dynamic Email action to Elementor Pro Form', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-email', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/dynamic-email-for-elementor-pro-form/'], 'DCE_Extension_Form_Export' => ['name' => 'dce_extension_form_export', 'category' => ['FORM'], 'title' => __('Export for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Add Export action to Elementor Pro Form', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-export', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/export-for-elementor-pro-form/'], 'DCE_Extension_Form_Icons' => ['name' => 'dce_extension_form_icons', 'category' => ['FORM'], 'title' => __('Icons for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Add icons on the label or inside the input of form fields', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-icons', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/icons-for-elementor-pro-form/'], 'DCE_Extension_Form_Inline_Align' => ['name' => 'dce_extension_form_inline_align', 'category' => ['FORM'], 'title' => __('Inline Align for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Choose the inline align type for checkbox and radio fields', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-inline-align', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/inline-align-for-elementor-pro-form/'], 'DCE_Extension_Form_Length' => ['name' => 'dce_extension_form_length', 'category' => ['FORM'], 'title' => __('Field Length for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Choose a minimum and maximum characters length for your text and textarea fields', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-field-length', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/field-length-for-elementor-pro-form/'], 'DCE_Extension_Form_Message' => ['name' => 'dce_extension_form_message', 'category' => ['FORM'], 'title' => __('Message Generator for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Options to customize the Elementor Pro Form success message', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-message-generator', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/message-generator-for-elementor-pro-form/'], 'DCE_Extension_Form_Method' => ['name' => 'dce_extension_form_method', 'category' => ['FORM'], 'title' => __('Method for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Add a different method attribute on your forms that specifies how to send form-data', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-method', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/method-for-elementor-pro-form/'], 'DCE_Extension_Form_Password_Visibility' => ['name' => 'dce_extension_form_password_visibility', 'category' => ['FORM'], 'title' => __('Password Visibility for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Allow your users to show or hide their password on Elementor Pro Form', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-password-visibility', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/password_visibility-for-elementor-pro-form/'], 'DCE_Extension_Form_PDF' => ['name' => 'dce_extension_form_pdf', 'category' => ['FORM'], 'title' => __('PDF Generator for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Add PDF creation action to Elementor Pro Form', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-pdf-generator', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/pdf-generator-for-elementor-pro-form/'], 'DCE_Extension_Form_Redirect' => ['name' => 'dce_extension_form_redirect', 'category' => ['FORM'], 'title' => __('Dynamic Redirect for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Redirect your users to different URLs based on their choice on submitted form fields', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-redirect', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/dynamic-redirect-for-elementor-pro-form/'], 'DCE_Extension_Form_Regex' => ['name' => 'dce_extension_form_regex', 'category' => ['FORM'], 'title' => __('Regex Field for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Validate form fields using a regular expression', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-regex', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/regex-field-for-elementor-pro-form/'], 'DCE_Extension_Form_Reset' => ['name' => 'dce_extension_form_reset', 'category' => ['FORM'], 'title' => __('Reset Button for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Add a reset button which resets all form fields to their initial values', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-reset', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/reset-button-for-elementor-pro-form/'], 'DCE_Extension_Form_Save' => ['name' => 'dce_extension_form_save', 'category' => ['FORM'], 'title' => __('Save for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Save the form data submitted by your client as posts, users or terms', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-save', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/save-elementor-pro-form/'], 'DCE_Extension_Form_Select2' => ['name' => 'dce_extension_form_select2', 'category' => ['FORM'], 'title' => __('Select2 for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Add Select2 to your select fields to gives a customizable select box with support for searching', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-select2', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/select2-for-elementor-pro-form/'], 'DCE_Extension_Form_Signature' => ['name' => 'dce_extension_form_signature', 'category' => ['FORM'], 'title' => __('Signature for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Add a signature field to Elementor Pro Form and it will be included in your PDF', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-signature', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/signature-field-for-elementor-pro-form'], 'DCE_Extension_Form_Step' => ['name' => 'dce_extension_form_step', 'category' => ['FORM'], 'title' => __('Enhanced Multi-Step for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Add features to Elementor Pro Multi-Step: label as a legend, show all steps, scroll to top on step change and step summary', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-multistep', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/enhanced-multi-step-elementor-pro-form/'], 'DCE_Extension_Form_Submit_On_Change' => ['name' => 'dce_extension_form_submit_on_change', 'category' => ['FORM'], 'title' => __('Submit On Change for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Submit the form automatically when the user chooses a radio button or a select field', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-submit-on-change', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/submit-on-change-for-elementor-pro-form/'], 'DCE_Extension_Form_Submit' => ['name' => 'dce_extension_form_submit', 'category' => ['FORM'], 'title' => __('Submit Button for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Add another submit button on your forms', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-submit', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/submit-button-for-elementor-pro-form/'], 'DCE_Extension_Form_Telegram' => ['name' => 'dce_extension_form_telegram', 'category' => ['FORM'], 'title' => __('Telegram for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Send the content of your Elementor Pro Form to Telegram', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-telegram', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/telegram-for-elementor-pro-form/'], 'DCE_Extension_Form_Visibility' => ['name' => 'dce_extension_form_visibility', 'legacy' => \true, 'category' => ['FORM'], 'title' => __('Conditional Fields (old version) for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Conditionally display fields based on form field values', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-conditional-fields', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/conditional-fields-for-elementor-pro-form/'], 'ConditionalFieldsV2' => ['name' => 'dce_conditional_fields_v2', 'category' => ['FORM'], 'title' => __('Conditionals V2 for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Contains the Extensions: Conditional Fields V2 and Conditional Validation', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-conditional-fields', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/conditional-fields-v2-for-elementor-pro-form/', 'minimum_php' => '7.2'], 'DCE_Extension_Form_WYSIWYG' => ['name' => 'dce_extension_form_wysiwyg', 'category' => ['FORM'], 'title' => __('WYSIWYG Editor for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Add a WYSIWYG editor to your textarea fields', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-wysiwyg', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/WYSIWYG-editor-for-elementor-pro-form/'], 'DCE_Extension_Form_PayPal' => ['name' => 'dce_extension_form_paypal', 'category' => ['FORM'], 'title' => __('PayPal Field for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Add a PayPal field for simple payments to Elementor Pro Form', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-paypal', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/paypal-for-elementor-pro-form/'], 'DCE_Extension_Form_Stripe' => ['name' => 'dce_extension_form_stripe', 'category' => ['FORM'], 'title' => __('Stripe Field for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Add a Stripe field for simple payments to Elementor Pro Form', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-stripe', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widgets/stripe-elementor-pro-form'], 'CustomValidation' => ['name' => 'custom_validation', 'category' => ['FORM'], 'title' => __('PHP Validation for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Add PHP code to validate a whole form', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-custom-validation', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/php-validation-for-elementor-pro-form/'], 'JsField' => ['name' => 'js_field', 'category' => ['FORM'], 'title' => __('JS Field for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Take advantage of JS full power to set the value of a field on Elementor Pro Form', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-js-amount', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/js-field-for-elementor-pro-form/'], 'LiveHtml' => ['name' => 'dce_live_html', 'category' => ['FORM'], 'title' => __('Live HTML Field for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Insert the values of your fields in an HTML Field and those changes will be updated live as they happen', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-live-html', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/live-html-field-elementor-pro-form/'], 'HiddenLabel' => ['name' => 'hidden_label', 'category' => ['FORM'], 'title' => __('Hidden Label for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Get the label corresponding to the value of another Radio, Select or Checkbox field', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-hidden-label', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/hidden-label-elementor-pro-form/'], 'DynamicSelect' => ['name' => 'dynamic_select', 'category' => ['FORM'], 'title' => __('Dynamic Select for Elementor Pro Form', 'dynamic-content-for-elementor'), 'description' => __('Insert a select field where the list of options changes dynamically according to the value of another field', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-dynamic-select', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/dynamic-select-field-elementor-pro-form/'], 'Tooltip' => ['name' => 'dce_tooltip', 'category' => ['FORM'], 'title' => __('Tooltip', 'dynamic-content-for-elementor'), 'description' => __('Add a tooltip to any widget', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-tooltip', 'plugin_depends' => [], 'doc_url' => 'https://www.dynamic.ooo/widget/tooltip-elementor'], 'DynamicCountdown' => ['name' => 'dce_dynamic_countdown', 'category' => ['FORM'], 'title' => __('Dynamic Due Date for Elementor Countdown', 'dynamic-content-for-elementor'), 'description' => __('Add a Dynamic Due Date field on Elementor Countdown widget to permit dynamic values', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-dynamic-countdown', 'plugin_depends' => ['elementor-pro'], 'doc_url' => 'https://www.dynamic.ooo/widget/dynamic-due-date-field-elementor-countdown']];
    }
    public static function get_form_extensions_info()
    {
        $form_extensions = [];
        foreach (self::get_extensions_info() as $extension_class => $extension_info) {
            if (\in_array('FORM', $extension_info['category'], \true)) {
                $form_extensions[$extension_class] = $extension_info;
            }
        }
        return $form_extensions;
    }
    public static function get_extensions_not_form_info()
    {
        $extensions_not_form = [];
        foreach (self::get_extensions_info() as $extension_class => $extension_info) {
            if ('FORM' !== $extension_info) {
                $extensions_not_form[$extension_class] = $extension_info;
            }
        }
        return $extensions_not_form;
    }
    public static function get_active_form_extensions()
    {
        $extensions = self::get_form_extensions_info();
        $option_excluded_extensions = self::get_excluded_extensions();
        $active_extensions = array();
        foreach ($extensions as $extension_class => $extension_info) {
            if (!isset($option_excluded_extensions[$extension_class])) {
                $active_extensions[$extension_class] = $extension_info;
            }
        }
        return $active_extensions;
    }
    /**
     * On extensions Registered
     *
     * @since 0.0.1
     *
     * @access public
     */
    public function on_extensions_registered()
    {
        $this->register_extensions();
        add_action('elementor_pro/init', function () {
            do_action('dce/register_form_actions');
        });
    }
    public function add_form_action($extension)
    {
        $priority = 10;
        if (isset($extension->action_priority)) {
            $priority = $extension->action_priority;
        }
        add_action('dce/register_form_actions', function () use($extension) {
            \ElementorPro\Plugin::instance()->modules_manager->get_modules('forms')->add_form_action($extension->get_label(), $extension);
        }, $priority);
    }
    /**
     * On Controls Registered
     *
     * @since 1.0.4
     *
     * @access public
     */
    public function register_extensions()
    {
        if (empty(self::$registered_extensions)) {
            $excluded_extensions = self::get_excluded_extensions();
            $extensions = self::$extensions;
            foreach ($extensions as $extension_class => $extension_info) {
                if (!isset($excluded_extensions[$extension_class])) {
                    $class = self::$namespace . $extension_class;
                    if (\DynamicContentForElementor\Helper::check_plugin_dependencies(\false, $extension_info['plugin_depends']) && (!isset($extension_info['minimum_php']) || \version_compare(\phpversion(), $extension_info['minimum_php'], '>='))) {
                        $extension = new $class($extension_info);
                        if (isset($extension->has_action) && $extension->has_action) {
                            $this->add_form_action($extension);
                        }
                        self::$registered_extensions[$extension_class] = $extension;
                        \DynamicContentForElementor\Assets::add_depends($extension);
                    }
                }
            }
        }
    }
    public static function get_legacy_extensions()
    {
        $legacy = \array_filter(self::get_extensions_info(), function ($info) {
            return isset($info['legacy']) && $info['legacy'];
        });
        return $legacy;
    }
    public static function get_excluded_extensions()
    {
        $excluded = \json_decode(get_option('dce_excluded_extensions', '[]'), \true);
        $legacy = \array_filter(self::get_extensions_info(), function ($info) {
            return isset($info['legacy']) && $info['legacy'];
        });
        \array_walk($legacy, function (&$k, $_) {
            $k = \true;
        });
        // overrides only if extensions not already present in options excluded.
        $excluded += $legacy;
        // return only the one that are set to true:
        return \array_filter($excluded, function ($e) {
            return $e;
        });
    }
}
