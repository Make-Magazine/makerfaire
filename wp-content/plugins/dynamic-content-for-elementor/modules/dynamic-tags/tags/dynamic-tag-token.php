<?php

namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Modules\DynamicTags\Module;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DCE_DynamicTag_Token extends Tag
{
    protected static $acf_names = [];
    public function get_name()
    {
        return 'dce-token';
    }
    public function get_title()
    {
        return __('Token', 'dynamic-content-for-elementor');
    }
    public function get_group()
    {
        return 'dce';
    }
    public function get_categories()
    {
        return \DynamicContentForElementor\Helper::get_dynamic_tags_categories();
    }
    public function get_docs()
    {
        return 'https://www.dynamic.ooo/widget/dynamic-tag-token/';
    }
    protected function _register_controls()
    {
        if (current_user_can('administrator') || !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $this->register_controls_settings();
        } elseif (!current_user_can('administrator') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $this->register_controls_non_admin_notice();
        }
    }
    protected function register_controls_non_admin_notice()
    {
        $this->add_control('html_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => __('You will need administrator capabilities to edit this widget.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
    }
    protected function register_controls_settings()
    {
        $objects = array('post', 'user', 'term');
        $this->add_control('dce_token_wizard', ['label' => __('Wizard mode', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER]);
        $this->add_control('dce_token', ['label' => __('Token', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => \true, 'placeholder' => '[post:title], [post:meta_key], [user:display_name], [term:name], [wp_query:posts]', 'condition' => ['dce_token_wizard' => '']]);
        $obj_opt = [
            'post' => ['title' => __('Post', 'dynamic-content-for-elementor'), 'icon' => 'eicon-post-content'],
            'user' => ['title' => __('User', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-users'],
            'term' => ['title' => __('Term', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-tags'],
            /*'comment' => [
            			'title' => __('Comment', 'dynamic-content-for-elementor'),
            			'icon' => 'fa fa-comments',
            		],*/
            'option' => ['title' => __('Option', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-list'],
            'wp_query' => ['title' => __('WP Query', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-search'],
            'date' => ['title' => __('Date', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-calendar'],
            'system' => ['title' => __('System', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-cogs'],
        ];
        if (\DynamicContentForElementor\Helper::is_acf_active()) {
            $obj_opt['acf'] = ['title' => __('ACF', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-plug'];
        }
        $this->add_control('dce_token_object', ['label' => __('Object', 'dynamic-content-for-elementor'), 'label_block' => \true, 'type' => Controls_Manager::CHOOSE, 'options' => $obj_opt, 'default' => 'post', 'toggle' => \false, 'condition' => ['dce_token_wizard!' => '']]);
        $this->add_control('dce_token_field_date', ['label' => __('Date Modificator', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'placeholder' => '+1 week, -2 months, yesterday, timestamp', 'description' => __('A time modificator compatible with strtotime or a timestamp', 'dynamic-content-for-elementor'), 'label_block' => \true, 'condition' => ['dce_token_wizard!' => '', 'dce_token_object' => 'date']]);
        $this->add_control('dce_token_field_date_format', ['label' => __('Date Format', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'placeholder' => 'Y-m-d H:i:s', 'label_block' => \true, 'condition' => ['dce_token_wizard!' => '', 'dce_token_object' => 'date']]);
        $this->add_control('dce_token_field_system', ['label' => __('Field', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => \true, 'placeholder' => __('_GET, _POST, _SERVER, MY_CONSTANT', 'dynamic-content-for-elementor'), 'condition' => ['dce_token_wizard!' => '', 'dce_token_object' => 'system']]);
        foreach ($objects as $aobj) {
            $this->add_control('dce_token_field_' . $aobj, ['label' => __('Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Meta key or Field Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'fields', 'object_type' => $aobj, 'condition' => ['dce_token_wizard!' => '', 'dce_token_object' => $aobj]]);
        }
        $this->add_control('dce_token_field_option', ['label' => __('Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Option key', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'options', 'condition' => ['dce_token_wizard!' => '', 'dce_token_object' => 'option']]);
        $this->add_control('dce_token_field_acf', ['label' => __('Field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('ACF Field name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'acf-field', 'condition' => ['dce_token_wizard!' => '', 'dce_token_object' => 'acf']]);
        $this->add_control('dce_token_acf_settings', ['label' => __('Get Field Settings', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'condition' => ['dce_token_wizard!' => '', 'dce_token_object' => 'acf']]);
        $this->add_control('dce_token_subfield', ['label' => __('SubField', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => \true, 'placeholder' => __('my_sub:label', 'dynamic-content-for-elementor'), 'condition' => ['dce_token_wizard!' => '', 'dce_token_object!' => 'date']]);
        foreach ($objects as $aobj) {
            $this->add_control('dce_token_source_' . $aobj, ['label' => __('Source', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Search', 'dynamic-content-for-elementor') . ' ' . \ucfirst($aobj), 'label_block' => \true, 'query_type' => $aobj . 's', 'condition' => ['dce_token_wizard!' => '', 'dce_token_object' => $aobj]]);
        }
        $this->add_control('dce_token_filter', ['label' => __('Filters', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'rows' => 2, 'placeholder' => 'trim', 'label_block' => \true, 'condition' => ['dce_token_wizard!' => '']]);
        $this->add_control('dce_token_code', ['label' => __('Show code', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'condition' => ['dce_token_wizard!' => '']]);
        $this->add_control('dce_token_data', ['label' => __('Return as Data', 'dynamic-content-for-elementor'), 'type' => \Elementor\Controls_Manager::SWITCHER, 'description' => __('Required for MEDIA Controls and other Controls which need a structured data', 'dynamic-content-for-elementor'), 'condition' => ['dce_token_code' => '']]);
        $this->add_control('dce_token_help', ['type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => '<div id="elementor-panel__editor__help" class="p-0"><a id="elementor-panel__editor__help__link" href="' . $this->get_docs() . '" target="_blank">' . __('Need Help', 'dynamic-content-for-elementor') . ' <i class="eicon-help-o"></i></a></div>', 'separator' => 'before']);
    }
    protected function render_non_admin_notice()
    {
        echo '<span>' . __('You will need administrator capabilities to edit this dynamic tag.', 'dynamic-content-for-elementor') . '</span>';
    }
    public function render()
    {
        $settings = $this->get_settings_for_display(null, \true);
        if (empty($settings)) {
            return;
        }
        $value = $this->get_token_value($settings);
        echo $value;
    }
    public function get_token_value($settings)
    {
        if (!empty($settings['dce_token_wizard'])) {
            $objects = array('post', 'user', 'term');
            $token = '[';
            $token .= $settings['dce_token_object'];
            foreach ($objects as $aobj) {
                if ($settings['dce_token_field_' . $aobj]) {
                    $token .= ':' . $settings['dce_token_field_' . $aobj];
                }
            }
            if ($settings['dce_token_object'] == 'acf') {
                if (isset(self::$acf_names[$settings['dce_token_field_acf']])) {
                    $acf_name = self::$acf_names[$settings['dce_token_field_acf']];
                } else {
                    $acf_name = \DynamicContentForElementor\Helper::get_excerpt_by_id($settings['dce_token_field_acf']);
                    self::$acf_names[$settings['dce_token_field_acf']] = $acf_name;
                }
                $token .= ':' . $acf_name;
                if ($settings['dce_token_acf_settings']) {
                    $token .= ':settings';
                }
            }
            if ($settings['dce_token_object'] == 'date') {
                if ($settings['dce_token_field_date']) {
                    $token .= ':' . $settings['dce_token_field_date'];
                }
                if ($settings['dce_token_field_date_format']) {
                    $token .= '|' . $settings['dce_token_field_date_format'];
                }
            }
            if ($settings['dce_token_field_system']) {
                $token .= ':' . $settings['dce_token_field_system'];
            }
            if ($settings['dce_token_field_option']) {
                $token .= ':' . $settings['dce_token_field_option'];
            }
            if ($settings['dce_token_subfield']) {
                $token .= ':' . $settings['dce_token_subfield'];
            }
            if ($settings['dce_token_filter']) {
                $filters = \explode(\PHP_EOL, $settings['dce_token_filter']);
                $token .= '|' . \implode('|', $filters);
            }
            foreach ($objects as $aobj) {
                if ($settings['dce_token_source_' . $aobj]) {
                    $token .= '|' . $settings['dce_token_source_' . $aobj];
                }
            }
            $token .= ']';
            if ($settings['dce_token_code']) {
                echo $token;
                return;
            }
        } else {
            $token = $settings['dce_token'];
        }
        $value = \DynamicContentForElementor\Helper::get_dynamic_value($token);
        return $value;
    }
    public function get_value(array $options = [])
    {
        $settings = $this->get_settings_for_display(null, \true);
        if (empty($settings)) {
            return;
        }
        \DynamicContentForElementor\Tokens::$data = \true;
        $value = $this->get_token_value($settings);
        \DynamicContentForElementor\Tokens::$data = \false;
        // for MEDIA Control
        if (\filter_var($value, \FILTER_VALIDATE_URL)) {
            $image_data = ['url' => $value];
            $thumbnail_id = \DynamicContentForElementor\Helper::get_image_id($value);
            if ($thumbnail_id) {
                $image_data['id'] = $thumbnail_id;
            }
            return $image_data;
        }
        return $value;
    }
    public function get_content(array $options = [])
    {
        $settings = $this->get_settings();
        $value = \false;
        if (isset($settings['dce_token_data']) && $settings['dce_token_data']) {
            $value = $this->get_value($options);
        } else {
            \ob_start();
            $this->render();
            $value = \ob_get_clean();
            if ($value) {
                // TODO: fix spaces in `before`/`after` if WRAPPED_TAG ( conflicted with .elementor-tag { display: inline-flex; } );
                if (!Utils::is_empty($settings, 'before')) {
                    $value = wp_kses_post($settings['before']) . $value;
                }
                if (!Utils::is_empty($settings, 'after')) {
                    $value .= wp_kses_post($settings['after']);
                }
            } elseif (!Utils::is_empty($settings, 'fallback')) {
                $value = $settings['fallback'];
            }
        }
        if (empty($value) && $this->get_settings('fallback')) {
            $value = $this->get_settings('fallback');
            $value = \DynamicContentForElementor\Helper::get_dynamic_value($value);
        }
        return $value;
    }
}
