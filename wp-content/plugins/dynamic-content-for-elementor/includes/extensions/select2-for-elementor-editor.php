<?php

namespace DynamicContentForElementor\Extensions;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DCE_Extension_Editor extends \DynamicContentForElementor\Extensions\DCE_Extension_Prototype
{
    public $name = 'Select2 for Elementor Editor';
    private $is_common = \false;
    protected function add_actions()
    {
        add_action('elementor/editor/after_enqueue_scripts', function () {
            wp_register_script('dce-select2-for-elementor-editor', plugins_url('/assets/js/select2-for-elementor-editor.js', DCE__FILE__), [], DCE_VERSION);
            wp_enqueue_script('dce-select2-for-elementor-editor');
        });
    }
}
