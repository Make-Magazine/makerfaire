<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use DynamicContentForElementor\Helper;
// Exit if accessed directly
if (!\defined('ABSPATH')) {
    exit;
}
class Title extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_style_depends()
    {
        return ['dce-title'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $post_type_object = get_post_type_object(get_post_type());
        $this->start_controls_section('section_title', ['label' => __('Title', 'dynamic-content-for-elementor')]);
        $this->add_control('html_tag', ['label' => __('HTML Tag', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['h1' => __('H1', 'dynamic-content-for-elementor'), 'h2' => __('H2', 'dynamic-content-for-elementor'), 'h3' => __('H3', 'dynamic-content-for-elementor'), 'h4' => __('H4', 'dynamic-content-for-elementor'), 'h5' => __('H5', 'dynamic-content-for-elementor'), 'h6' => __('H6', 'dynamic-content-for-elementor'), 'p' => __('p', 'dynamic-content-for-elementor'), 'div' => __('div', 'dynamic-content-for-elementor'), 'span' => __('span', 'dynamic-content-for-elementor')], 'default' => 'h3', 'separator' => 'before']);
        $this->add_responsive_control('align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'selectors' => ['{{WRAPPER}}' => 'text-align: {{VALUE}};'], 'condition' => ['enable_divider' => '']]);
        $this->add_control('link_to', ['label' => __('Link to', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'none', 'options' => ['none' => __('None', 'dynamic-content-for-elementor'), 'home' => __('Home URL', 'dynamic-content-for-elementor'), 'post' => 'Post URL', 'parent' => 'Parent Page', 'custom' => __('Custom URL', 'dynamic-content-for-elementor')]]);
        $this->add_control('link', ['label' => __('Link', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'placeholder' => __('https://your-link.com', 'dynamic-content-for-elementor'), 'condition' => ['link_to' => 'custom'], 'default' => ['url' => ''], 'show_label' => \false]);
        $this->add_control('enable_divider', ['label' => __('Dividers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'label_on' => __('Show', 'dynamic-content-for-elementor'), 'label_off' => __('Hide', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'render_type' => 'template', 'prefix_class' => 'dce-title-divider-']);
        $this->add_control('enable_masking', ['label' => __('Masking', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => '', 'label_on' => __('Show', 'dynamic-content-for-elementor'), 'label_off' => __('Hide', 'dynamic-content-for-elementor'), 'return_value' => 'yes', 'render_type' => 'template', 'prefix_class' => 'dce-title-mask-']);
        $this->end_controls_section();
        $this->start_controls_section('section_title_dividers', ['label' => __('Dividers', 'dynamic-content-for-elementor'), 'condition' => ['enable_divider' => 'yes']]);
        $this->add_control('style_dividers', ['label' => __('Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['solid' => __('Solid', 'dynamic-content-for-elementor'), 'double' => __('Double', 'dynamic-content-for-elementor'), 'dotted' => __('Dotted', 'dynamic-content-for-elementor'), 'dashed' => __('Dashed', 'dynamic-content-for-elementor')], 'default' => 'solid', 'selectors' => ['{{WRAPPER}} .dce-divider:after, {{WRAPPER}} .dce-divider:before' => 'border-top-style: {{VALUE}};']]);
        $this->add_control('weight_dividers', ['label' => __('Weight', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => 1, 'max' => 50]], 'selectors' => ['{{WRAPPER}} .dce-divider:after, {{WRAPPER}} .dce-divider:before' => 'border-top-width: {{SIZE}}{{UNIT}};']]);
        $this->add_control('color_dividers', ['label' => __('Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'default' => '', 'selectors' => ['{{WRAPPER}} .dce-divider:after, {{WRAPPER}} .dce-divider:before' => 'border-top-color: {{VALUE}};']]);
        $this->add_responsive_control('width_dividers', ['label' => __('Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'size_units' => ['%', 'px'], 'range' => ['px' => ['max' => 600, 'min' => 10, 'step' => 1]], 'default' => ['size' => 25, 'unit' => '%'], 'tablet_default' => ['unit' => '%'], 'mobile_default' => ['unit' => '%'], 'selectors' => ['{{WRAPPER}} .dce-divider:after, {{WRAPPER}} .dce-divider:before' => 'width: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('gap_dividers', ['label' => __('Gap', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 15], 'range' => ['px' => ['min' => 2, 'max' => 80]], 'selectors' => ['{{WRAPPER}} .dce-title-divider' => 'padding-right: {{SIZE}}{{UNIT}}; padding-left: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('space_dividers', ['label' => __('Space', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 15], 'range' => ['px' => ['min' => -100, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .dce-title-divider' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};']]);
        $this->add_responsive_control('divider_position', ['label' => __('Position', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'default' => 'center', 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right'], 'top' => ['title' => __('Top', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-top'], 'bottom' => ['title' => __('Bottom', 'dynamic-content-for-elementor'), 'icon' => 'eicon-v-align-bottom']], 'prefix_class' => 'dce-divider-position%s-']);
        $this->add_responsive_control('divider_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => '', 'prefix_class' => 'dce-title-align%s-', 'selectors' => ['{{WRAPPER}} .dce-title-divider' => 'text-align: {{VALUE}};'], 'condition' => ['divider_position' => ['top', 'bottom']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_title_masking', ['label' => __('Masking', 'dynamic-content-for-elementor'), 'condition' => ['enable_masking' => 'yes']]);
        $this->add_group_control(Group_Control_Background::get_type(), ['name' => 'background_masking', 'types' => ['classic', 'gradient'], 'fields_options' => ['background' => ['frontend_available' => \true], 'video_link' => ['frontend_available' => \true]], 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-title span']);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => __('Source', 'dynamic-content-for-elementor')]);
        $this->add_control('data_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'description' => __('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => __('Same', 'dynamic-content-for-elementor'), 'label_off' => __('other', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_control('other_post_source', ['label' => __('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['data_source' => '']]);
        $this->add_control('other_post_parent', ['label' => __('From post parent', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['data_source' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Title', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-title' => 'color: {{VALUE}};', '{{WRAPPER}} .dynamic-content-for-elementor-title a' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-title']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'text_shadow', 'selector' => '{{WRAPPER}} .dynamic-content-for-elementor-title']);
        $this->add_control('blend_mode', ['label' => __('Blend Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Normal', 'dynamic-content-for-elementor'), 'multiply' => __('Multiply', 'dynamic-content-for-elementor'), 'screen' => __('Screen', 'dynamic-content-for-elementor'), 'overlay' => __('Overlay', 'dynamic-content-for-elementor'), 'darken' => __('Darken', 'dynamic-content-for-elementor'), 'lighten' => __('Lighten', 'dynamic-content-for-elementor'), 'color-dodge' => __('Color Dodge', 'dynamic-content-for-elementor'), 'saturation' => __('Saturation', 'dynamic-content-for-elementor'), 'color' => __('Color', 'dynamic-content-for-elementor'), 'difference' => __('Difference', 'dynamic-content-for-elementor'), 'exclusion' => __('Exclusion', 'dynamic-content-for-elementor'), 'hue' => __('Hue', 'dynamic-content-for-elementor'), 'luminosity' => __('Luminosity', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-title .dce-title' => 'mix-blend-mode: {{VALUE}}'], 'separator' => 'before']);
        $this->end_controls_section();
        $this->start_controls_section('section_rollhover', ['label' => __('Rollover', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => ['link_to!' => 'none']]);
        $this->add_control('hover_color', ['label' => __('Hover Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dynamic-content-for-elementor-title a:hover' => 'color: {{VALUE}};'], 'condition' => ['link_to!' => 'none']]);
        $this->add_control('hover_animation', ['label' => __('Hover Animation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HOVER_ANIMATION, 'condition' => ['link_to!' => 'none']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $id_page = Helper::get_the_id($settings['other_post_source'], $settings['other_post_parent']);
        $title = __('Post Title', 'dynamic-content-for-elementor');
        $title = wp_kses_post(get_the_title($id_page));
        // Archives
        if (!Helper::in_the_loop()) {
            // All other Taxonomies
            if (is_archive() && !Helper::in_the_loop()) {
                $title = wp_kses_post(get_the_title($id_page));
                if (is_tax() || is_category() || is_tag()) {
                    $title = single_term_title('', \false);
                } else {
                    $title = post_type_archive_title('', \false);
                }
            }
            if (is_home()) {
                $title = '';
                $object_t = get_post_type_object(get_post_type())->labels;
                $label_t = $object_t->name;
                $title = $label_t;
            }
            if (is_search()) {
                $title = wp_kses_post(get_the_title());
            }
        }
        if ($settings['other_post_source'] || $settings['other_post_parent']) {
            $title = wp_kses_post(get_the_title($id_page));
        }
        if ($settings['enable_divider'] == 'yes') {
            $title = '<div class="dce-divider"><span class="dce-title dce-title-divider">' . $title . '</span></div>';
        } else {
            $title = '<span class="dce-title">' . $title . '</span>';
        }
        if (empty($title)) {
            return;
        }
        switch ($settings['link_to']) {
            case 'custom':
                if (!empty($settings['link']['url'])) {
                    $link = esc_url($settings['link']['url']);
                } else {
                    $link = \false;
                }
                break;
            case 'post':
                $link = esc_url(get_the_permalink($id_page));
                break;
            case 'parent':
                $id_page_parent = wp_get_post_parent_id($id_page);
                $link = esc_url(get_the_permalink($id_page_parent));
                break;
            case 'home':
                $link = esc_url(get_home_url());
                break;
            case 'none':
            default:
                $link = \false;
                break;
        }
        $target = !empty($settings['link']) && $settings['link']['is_external'] ? ' target="_blank"' : '';
        $nofollow = !empty($settings['link']['nofollow']) ? ' rel="nofollow"' : '';
        $animation_class = !empty($settings['hover_animation']) ? 'elementor-animation-' . $settings['hover_animation'] : '';
        $html = \sprintf('<%1$s class="dynamic-content-for-elementor-title %2$s">', \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']), $animation_class);
        if ($link) {
            $html .= \sprintf('<a href="%1$s"%2$s%4$s>%3$s</a>', $link, $target, $title, $nofollow);
        } else {
            $html .= $title;
        }
        $html .= \sprintf('</%s>', \DynamicContentForElementor\Helper::validate_html_tag($settings['html_tag']));
        echo $html;
    }
}
