<?php
namespace Elementor;
/**
 * @package     WordPress
 * @subpackage  Gum Elementor Addon
 * @author      support@themegum.com
 * @since       1.0.9
*/
defined('ABSPATH') or die();

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;



class Gum_Elementor_Widget_Heading extends Widget_Base {


  /**
   * Get widget name.
   *
   *
   * @since 1.0.0
   * @access public
   *
   * @return string Widget name.
   */
  public function get_name() {
    return 'gum_heading';
  }

  /**
   * Get widget title.
   *
   *
   * @since 1.0.0
   * @access public
   *
   * @return string Widget title.
   */
  public function get_title() {

    return esc_html__( 'Heading', 'gum-elementor-addon' );
  }

  /**
   * Get widget icon.
   *
   *
   * @since 1.0.0
   * @access public
   *
   * @return string Widget icon.
   */
  public function get_icon() {
    return 'eicon-heading';
  }

  public function get_keywords() {
    return [ 'wordpress', 'widget', 'heading','title','' ];
  }

  /**
   * Get widget categories.
   *
   *
   * @since 1.0.0
   * @access public
   *
   * @return array Widget categories.
   */
  public function get_categories() {
    return [ 'temegum' ];
  }

  protected function _register_controls() {



    $this->start_controls_section(
      'section_title',
      [
        'label' => esc_html__( 'Title', 'elementor' ),
      ]
    );


    $this->add_control(
      'heading_source',
      [
        'label' => esc_html__( 'Title Source', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'text' => esc_html__( 'Text', 'gum-elementor-addon' ),
          'the_title' => esc_html__( 'Page Title', 'gum-elementor-addon' ),
          'site_title' => esc_html__( 'Site Title', 'gum-elementor-addon' ),
          'site_tagline' => esc_html__( 'Site Tagline', 'gum-elementor-addon' ),
          'site_year' => esc_html__( 'Year', 'gum-elementor-addon' )
        ],
        'default' => 'text'
      ]
    );

    $this->add_control(
      'main_heading',
      [
        'label' => esc_html__( 'Main Title', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXTAREA,
        'dynamic' => [
          'active' => false,
        ],
        'ai' => [
          'active' => false,
        ],
        'placeholder' => esc_html__( 'Enter your title', 'gum-elementor-addon' ),
        'default' => esc_html__( 'Heading Text Here', 'gum-elementor-addon' ),
        'condition' => [
          'heading_source[value]' => 'text'
        ],
      ]
    );

    $this->add_control(
      'prefix_heading',
      [
        'label' => esc_html__( 'Prefix Title', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXTAREA,
        'dynamic' => [
          'active' => false,
        ],
        'ai' => [
          'active' => false,
        ],
        'default' => '',
        'condition' => [
          'heading_source[value]' => 'text'
        ],
      ]
    );

    $this->add_control(
      'sub_heading',
      [
        'label' => esc_html__( 'Suffix Title', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXTAREA,
        'dynamic' => [
          'active' => false,
        ],
        'ai' => [
          'active' => false,
        ],
        'default' => '',
        'condition' => [
          'heading_source[value]' => 'text'
        ],
      ]
    );

    $this->add_control(
      'link',
      [
        'label' => esc_html__( 'Link', 'elementor' ),
        'type' => Controls_Manager::URL,
        'dynamic' => [
          'active' => true,
        ],
        'default' => [
          'url' => '',
        ],
        'separator' => 'before',
        'condition' => [
          'heading_source[value]!' => 'the_title'
        ],
      ]
    );


    $this->add_control(
      "page_link",
      [
        'label' => esc_html__( 'Add Link', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Yes', 'elementor' ),
        'label_off' => esc_html__( 'No', 'elementor' ),
        'condition' => [
          'heading_source[value]' => 'the_title'
        ],
      ]
    );

    $this->add_control(
      'font_size',
      [
        'label' => esc_html__( 'Size', 'elementor' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'default',
        'options' => [
          'xlarge'  => esc_html__('Extra Large','gum-elementor-addon'),
          'large' => esc_html__('Large','gum-elementor-addon'),
          'medium' => esc_html__('Medium','gum-elementor-addon'),
          'default' => esc_html__('Default','gum-elementor-addon'),
          'small' => esc_html__('Small','gum-elementor-addon'),
          'exsmall' => esc_html__('Extra small','gum-elementor-addon'),
        ],
      ]
    );

    $this->add_control(
      'tag',
      [
        'label' => esc_html__( 'HTML Tag', 'elementor' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'h1' => 'H1',
          'h2' => 'H2',
          'h3' => 'H3',
          'h4' => 'H4',
          'h5' => 'H5',
          'h6' => 'H6',
          'div' => 'div',
          'span' => 'span',
          'p' => 'p',
        ],
        'default' => 'h2',
      ]
    );

    $this->add_responsive_control(
      'text_align',
      [
        'label' => esc_html__( 'Alignment', 'elementor' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => esc_html__( 'Left', 'elementor' ),
            'icon' => 'eicon-text-align-left',
          ],
          'center' => [
            'title' => esc_html__( 'Center', 'elementor' ),
            'icon' => 'eicon-text-align-center',
          ],
          'right' => [
            'title' => esc_html__( 'Right', 'elementor' ),
            'icon' => 'eicon-text-align-right',
          ],
          'justify' => [
            'title' => esc_html__( 'Justified', 'elementor' ),
            'icon' => 'eicon-text-align-justify',
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .section-main-title' => 'text-align: {{VALUE}};',
        ],
        'default' => '',
      ]
    );


    $this->end_controls_section();

    $this->start_controls_section(
      'decoration',
      [
        'label' => esc_html__( 'Decoration', 'gum-elementor-addon' ),
      ]
    );    

    $this->add_control(
      'layout',
      [
        'label' => esc_html__( 'Decoration', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          ''=> esc_html__('None','gum-elementor-addon'),
          'underline'=> esc_html__('Yes','gum-elementor-addon'),
        ],
        'default' => '',
        'prefix_class' => 'layout-',
        'separator' => 'before',
      ]
    );


    $this->add_control(
      'line_position',
      [
        'label' => esc_html__( 'Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'default' => '',
        'options' => [
          '' => esc_html__( 'Top', 'gum-elementor-addon' ),
          'after' => esc_html__( 'Bottom', 'gum-elementor-addon' ),
        ],
        'style_transfer' => true,
        'condition' => [
          'layout[value]' => 'underline'
        ],
      ]
    );


    $this->add_responsive_control(
      'line_align',
      [
        'label' => esc_html__( 'Alignment', 'elementor' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => esc_html__( 'Left', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-left',
          ],
          'center' => [
            'title' => esc_html__( 'Center', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-center',
          ],
          'right' => [
            'title' => esc_html__( 'Right', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-right',
          ],
        ],
        'default' => '',
        'style_transfer' => true,
        'condition' => [
          'layout[value]' => 'underline'
        ],
      ]
    );

    $this->end_controls_section();


/*
 * style params
 */

    $this->start_controls_section(
      'title_style',
      [
        'label' => esc_html__( 'Main Title', 'elementor' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    

    $this->add_control(
      'title_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .section-main-title,{{WRAPPER}} .section-main-title a' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'title_hovercolor',
      [
        'label' => esc_html__( 'Hover Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .section-main-title:hover a' => 'color: {{VALUE}};',
        ],
        'conditions' => [
          'relation' => 'and',
          'terms' =>[
            [ 'name' => 'heading_source',
              'operator' => '==',
              'value' =>'the_title'
            ],
            [
              'name' => 'page_link',
              'operator' => '==',
              'value' => 'yes',
            ]
          ]
        ],
        'conditions' => [
          'relation' => 'and',
          'terms' =>[
            [ 'name' => 'heading_source',
              'operator' => '!==',
              'value' =>'the_title'
            ],
            [
              'name' => 'link[url]',
              'operator' => '!==',
              'value' => '',
            ]
          ]
        ]
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_title',
        'selector' => '{{WRAPPER}} .section-main-title',
      ]
    );


    $this->add_group_control(
      Group_Control_Text_Stroke::get_type(),
      [
        'name' => 'title_text_stroke',
        'selector' => '{{WRAPPER}} .section-main-title,{{WRAPPER}} .section-main-title a',
      ]
    );

    $this->add_group_control(
      Group_Control_Text_Shadow::get_type(),
      [
        'name' => 'title_text_shadow',
        'selector' => '{{WRAPPER}} .section-main-title,{{WRAPPER}} .section-main-title a',
      ]
    );

    $this->add_control(
      'main_title_background',
      [
        'label' => esc_html__( 'Background', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .section-main-title span.maintitle' => 'background: {{VALUE}};',
        ],
        'conditions' => [
          'relation' => 'or',
          'terms' =>[
            [
              'name' => 'prefix_heading',
              'operator' => '!==',
              'value' => '',
            ],
            [
              'name' => 'sub_heading',
              'operator' => '!==',
              'value' => '',
            ],
          ]
        ],
        'condition' => [
          'heading_source[value]' => 'text'
        ],
      ]
    );


    $this->add_responsive_control(
      'main_title_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .section-main-title span.maintitle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; margin: 0 0.33em;',
        ],
        'conditions' => [
          'relation' => 'or',
          'terms' =>[
            [
              'name' => 'prefix_heading',
              'operator' => '!==',
              'value' => '',
            ],
            [
              'name' => 'sub_heading',
              'operator' => '!==',
              'value' => '',
            ],
          ]
        ],
      ]
    ); 

    $this->add_control(
      'main_title_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .section-main-title span.maintitle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'conditions' => [
          'relation' => 'or',
          'terms' =>[
            [
              'name' => 'prefix_heading',
              'operator' => '!==',
              'value' => '',
            ],
            [
              'name' => 'sub_heading',
              'operator' => '!==',
              'value' => '',
            ],
          ]
        ],
      ]
    );

    $this->end_controls_section();


    $this->start_controls_section(
      'prefix_title_style',
      [
        'label' => esc_html__( 'Prefix Title', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'prefix_heading[value]!' => '',
          'heading_source[value]' => 'text'
        ],
      ]
    );    

    $this->add_control(
      'prefix_title_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .section-main-title span.prefix' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'prefix_title_hovercolor',
      [
        'label' => esc_html__( 'Hover Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .section-main-title:hover a span.prefix' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'link[url]!' => '',
        ],
      ]
    );

    $this->add_control(
      'prefix_titlefont_weight',
      [
      'label' => _x( 'Weight', 'Typography Control', 'elementor' ),
      'type' => Controls_Manager::SELECT,
      'default' => '',
      'options' => [
        '100' => '100',
        '200' => '200',
        '300' => '300',
        '400' => '400',
        '500' => '500',
        '600' => '600',
        '700' => '700',
        '800' => '800',
        '900' => '900',
        '' => esc_html__( 'Default', 'elementor' ),
        'normal' => esc_html__( 'Normal', 'elementor' ),
        'bold' => esc_html__( 'Bold', 'elementor' ),
      ],
      'selectors' => [
          '{{WRAPPER}} .section-main-title span.prefix' => 'font-weight: {{VALUE}};',
      ]]);

    $this->add_group_control(
      Group_Control_Text_Stroke::get_type(),
      [
        'name' => 'prefix_title_stroke',
        'selector' => '{{WRAPPER}} .section-main-title span.prefix',
      ]
    );

    $this->add_group_control(
      Group_Control_Text_Shadow::get_type(),
      [
        'name' => 'prefix_title_shadow',
        'selector' => '{{WRAPPER}} .section-main-title span.prefix',
      ]
    );

    $this->end_controls_section();


    $this->start_controls_section(
      'sub_title_style',
      [
        'label' => esc_html__( 'Suffix Title', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'sub_heading[value]!' => '',
          'heading_source[value]' => 'text'
        ],
      ]
    );    


    $this->add_control(
      'subtitle_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .section-main-title span.subfix' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'subtitle_hovercolor',
      [
        'label' => esc_html__( 'Hover Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .section-main-title:hover a span.subfix' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'link[url]!' => '',
        ],
      ]
    );

    $this->add_control(
      'sub_titlefont_weight',
      [
      'label' => _x( 'Weight', 'Typography Control', 'elementor' ),
      'type' => Controls_Manager::SELECT,
      'default' => '',
      'options' => [
        '100' => '100',
        '200' => '200',
        '300' => '300',
        '400' => '400',
        '500' => '500',
        '600' => '600',
        '700' => '700',
        '800' => '800',
        '900' => '900',
        '' => esc_html__( 'Default', 'elementor' ),
        'normal' => esc_html__( 'Normal', 'elementor' ),
        'bold' => esc_html__( 'Bold', 'elementor' ),
      ],
      'selectors' => [
          '{{WRAPPER}} .section-main-title span.subfix' => 'font-weight: {{VALUE}};',
      ]]);

    $this->add_group_control(
      Group_Control_Text_Stroke::get_type(),
      [
        'name' => 'sub_title_stroke',
        'selector' => '{{WRAPPER}} .section-main-title span.subfix',
      ]
    );

    $this->add_group_control(
      Group_Control_Text_Shadow::get_type(),
      [
        'name' => 'sub_title_shadow',
        'selector' => '{{WRAPPER}} .section-main-title span.subfix',
      ]
    );

    $this->end_controls_section();


    $this->start_controls_section(
      'title_decoration',
      [
        'label' => esc_html__( 'Decoration', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'layout[value]!' => ''
        ],
      ]
    );    

    $this->add_control(
      'decoration_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}}.layout-underline .gum-widget-title:before' => 'background-color: {{VALUE}};',
          '{{WRAPPER}}.layout-underline .gum-widget-title.decor-after:after' => 'background-color: {{VALUE}};',
        ],
        'condition' => [
          'layout[value]' => 'underline'
        ],
      ]
    );

    $this->add_responsive_control(
      'decoration_size',
      [
        'label' => esc_html__( 'Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', '%'],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 1000,
          ],
          '%' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'default'=> ['size'=>2,'unit'=> 'px'],
        'selectors' => [
          '{{WRAPPER}}.layout-underline .gum-widget-title:before' => 'height: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}}.layout-underline .gum-widget-title.decor-after:after' => 'height: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'layout[value]' => 'underline'
        ],
      ]
    );

    $this->add_responsive_control(
      'decoration_width',
      [
        'label' => esc_html__( 'Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 1000,
          ],
          '%' => [
            'min' => 0,
            'max' => 200,
          ],
        ],
        'size_units'=>['px','%'],
        'default'=> ['size'=>50,'unit'=> 'px'],
        'selectors' => [
          '{{WRAPPER}}.layout-underline .gum-widget-title:before' => 'width: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}}.layout-underline .gum-widget-title.decor-after:after' => 'width: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'layout[value]' => 'underline'
        ],
      ]
    );


    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'line_border',
        'selector' => '{{WRAPPER}}.layout-underline .gum-widget-title:before,{{WRAPPER}}.layout-underline .gum-widget-title:after',
        'condition' => [
          'layout[value]' => 'underline'
        ],
      ]
    );


    $this->add_control(
      'line_border_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}}.layout-underline .gum-widget-title:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          '{{WRAPPER}}.layout-underline .gum-widget-title.decor-after:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => [
          'layout[value]' => 'underline',
        ],
      ]
    );


    $this->add_group_control(
      Group_Control_Box_Shadow::get_type(),
      [
        'name' => 'line_box_shadow',
        'selector' => '{{WRAPPER}}.layout-underline .gum-widget-title:before,{{WRAPPER}}.layout-underline .gum-widget-title.decor-after:after',
        'condition' => [
          'layout[value]' => 'underline',
        ],
      ]
    );


    $this->add_control(
      'decoration_maskimage',
      [
        'label' => esc_html__( 'Image Mask', 'gum-elementor-addon' ),
        'type' => Controls_Manager::MEDIA,
        'media_type' => 'image',
        'should_include_svg_inline_option' => true,
        'library_type' => 'image/svg+xml',
        'dynamic' => [
          'active' => true,
        ],
        'selectors' => [
          '{{WRAPPER}}.layout-underline .gum-widget-title:before,{{WRAPPER}}.layout-underline .decor-after.gum-widget-title:after' => '-webkit-mask-image: url("{{URL}}");',
          '{{WRAPPER}}.layout-underline .decor-after.gum-widget-title:before' => '-webkit-mask-image: none!important;',
        ],
        'render_type' => 'template',
        'condition' => [
          'layout[value]' => 'underline'
        ],
        'separator' => 'before',
      ]
    );


    $this->add_responsive_control(
      'decoration_mask_size',
      [
        'label' => esc_html__( 'Mask Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'contain' => esc_html__( 'Fit', 'elementor' ),
          'cover' => esc_html__( 'Fill', 'elementor' ),
          'custom' => esc_html__( 'Custom', 'elementor' ),
        ],
        'default' => 'contain',
        'selectors' => [ '{{WRAPPER}}.layout-underline .gum-widget-title:before,{{WRAPPER}}.layout-underline .gum-widget-title:after' => '-webkit-mask-size: {{VALUE}};' ],
        'condition' => [
          'decoration_maskimage[url]!' => '',
          'layout[value]' => 'underline',
        ],
      ]
    );

    $this->add_responsive_control(
      'decoration_mask_size_scale',
      [
        'label' => esc_html__( 'Mask Scale', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'em', '%', 'vw' ],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 500,
          ],
          'em' => [
            'min' => 0,
            'max' => 100,
          ],
          '%' => [
            'min' => 0,
            'max' => 200,
          ],
          'vw' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'default' => [
          'unit' => '%',
          'size' => 100,
        ],
        'selectors' => [ '{{WRAPPER}}.layout-underline .gum-widget-title:before,{{WRAPPER}}.layout-underline .gum-widget-title:after' => '-webkit-mask-size: {{SIZE}}{{UNIT}};' ],
        'condition' => [
          'decoration_maskimage[url]!' => '',
          'decoration_mask_size' => 'custom',
        ],
      ]
    );

    $this->add_responsive_control(
      'decoration_mask_position',
      [
        'label' => esc_html__( 'Mask Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'center center' => esc_html__( 'Center Center', 'elementor' ),
          'center left' => esc_html__( 'Center Left', 'elementor' ),
          'center right' => esc_html__( 'Center Right', 'elementor' ),
          'top center' => esc_html__( 'Top Center', 'elementor' ),
          'top left' => esc_html__( 'Top Left', 'elementor' ),
          'top right' => esc_html__( 'Top Right', 'elementor' ),
          'bottom center' => esc_html__( 'Bottom Center', 'elementor' ),
          'bottom left' => esc_html__( 'Bottom Left', 'elementor' ),
          'bottom right' => esc_html__( 'Bottom Right', 'elementor' ),
          'custom' => esc_html__( 'Custom', 'elementor' ),
        ],
        'default' => 'center center',
        'selectors' => [ '{{WRAPPER}}.layout-underline .gum-widget-title:before,{{WRAPPER}}.layout-underline .gum-widget-title:after' =>  '-webkit-mask-position: {{VALUE}};' ],
        'condition' => [
          'decoration_maskimage[url]!' => '',
          'layout[value]' => 'underline',
        ],
      ]
    );

    $this->add_responsive_control(
      'decoration_mask_position_x',
      [
        'label' => esc_html__( 'Mask X Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'em', '%', 'vw' ],
        'range' => [
          'px' => [
            'min' => -500,
            'max' => 500,
          ],
          'em' => [
            'min' => -100,
            'max' => 100,
          ],
          '%' => [
            'min' => -100,
            'max' => 100,
          ],
          'vw' => [
            'min' => -100,
            'max' => 100,
          ],
        ],
        'default' => [
          'unit' => '%',
          'size' => 0,
        ],
        'selectors' => [ '{{WRAPPER}}.layout-underline .gum-widget-title:before,{{WRAPPER}}.layout-underline .gum-widget-title:after' =>  '-webkit-mask-position-x: {{SIZE}}{{UNIT}};' ],
        'condition' => [
          'decoration_maskimage[url]!' => '',
          'decoration_mask_position' => 'custom',
        ],
      ]
    );

    $this->add_responsive_control(
      'decoration_mask_position_y',
      [
        'label' => esc_html__( 'Mask Y Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'em', '%', 'vw' ],
        'range' => [
          'px' => [
            'min' => -500,
            'max' => 500,
          ],
          'em' => [
            'min' => -100,
            'max' => 100,
          ],
          '%' => [
            'min' => -100,
            'max' => 100,
          ],
          'vw' => [
            'min' => -100,
            'max' => 100,
          ],
        ],
        'default' => [
          'unit' => '%',
          'size' => 0,
        ],
        'selectors' => [ '{{WRAPPER}}.layout-underline .gum-widget-title:before,{{WRAPPER}}.layout-underline .gum-widget-title:after' =>  '-webkit-mask-position-y: {{SIZE}}{{UNIT}};' ],
        'condition' => [
          'decoration_maskimage[url]!' => '',
          'decoration_mask_position' => 'custom',
        ],
      ]
    );

    $this->add_responsive_control(
      'decoration_mask_repeat',
      [
        'label' => esc_html__( 'Mask Repeat', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'no-repeat' => esc_html__( 'No-Repeat', 'elementor' ),
          'repeat' => esc_html__( 'Repeat', 'elementor' ),
          'repeat-x' => esc_html__( 'Repeat-X', 'elementor' ),
          'repeat-Y' => esc_html__( 'Repeat-Y', 'elementor' ),
          'round' => esc_html__( 'Round', 'elementor' ),
          'space' => esc_html__( 'Space', 'elementor' ),
        ],
        'default' => 'no-repeat',
        'selectors' => [ '{{WRAPPER}}.layout-underline .gum-widget-title:before,{{WRAPPER}}.layout-underline .gum-widget-title:after' => '-webkit-mask-repeat: {{VALUE}};' ],
        'condition' => [
          'decoration_maskimage[url]!' => '',
          'decoration_mask_size!' => 'cover',
        ],
      ]
    );


    $this->end_controls_section();

  }

  protected function render() {

    $settings = $this->get_settings_for_display();

    extract( $settings );

    if($main_heading === '') return;

    $this->add_render_attribute( 'heading_container', 'class', 'gum-widget-title' );
    $this->add_render_attribute( 'section_heading', 'class', 'section-main-title' );

    if ( ! empty( $font_size ) ) {
      $this->add_render_attribute( 'section_heading', 'class', 'size-' . $font_size );
    }

    if( ! empty( $line_position )){
      $this->add_render_attribute( 'heading_container', 'class', 'decor-' . $line_position );      
    }

    if( ! empty( $line_align )){
      $this->add_render_attribute( 'heading_container', 'class', 'decorpos-' . $line_align );      
    }



    switch ( $heading_source ) {
      case 'the_title':
          $main_heading = get_the_title();
        break;
      case 'site_title':
          $main_heading = get_bloginfo( 'name' );
        break;
      case 'site_tagline':
          $main_heading = get_bloginfo( 'description' );
        break;      
      case 'site_year':
          $main_heading = date('Y');
        break;      
      default:

      $this->add_render_attribute( 'prefix_heading','class','prefix');
      $this->add_inline_editing_attributes( 'prefix_heading' );

      $heading = '<span '.$this->get_render_attribute_string( 'prefix_heading' ).'>'.esc_html($prefix_heading).'</span>';


      $this->add_render_attribute( 'main_heading','class','maintitle');
      $this->add_inline_editing_attributes( 'main_heading' );

      $heading .= '<span '.$this->get_render_attribute_string( 'main_heading' ).'>'.esc_html($main_heading).'</span>';

      $this->add_render_attribute( 'sub_heading','class','subfix');
      $this->add_inline_editing_attributes( 'sub_heading' );

      $heading .= '<span '.$this->get_render_attribute_string( 'sub_heading' ).'>'.esc_html($sub_heading).'</span>';

      $main_heading = $heading;

      break;
    }


    if ( ! empty( $link['url'] ) && $heading_source !='the_title') {

        $this->add_link_attributes( 'url', $link );
        $main_heading = sprintf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string( 'url' ), $main_heading );
    }elseif($heading_source =='the_title' && $page_link =='yes'){

        $main_heading = sprintf( '<a href="%1$s">%2$s</a>', get_the_permalink(), $main_heading );
    }

    $title_html = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $tag ), $this->get_render_attribute_string( 'section_heading' ), $main_heading );

    echo '<div '.$this->get_render_attribute_string( 'heading_container' ).'>'. $title_html .'</div>';
  }

  protected function content_template() {
    ?>
    <# 
    var allowed_tags = [ 'h1','h2','h3','h4','h5','h6','div','p','span' ];
    var tag_title = ( allowed_tags[ settings.tag ] && allowed_tags[ settings.tag ] != '' ) ? settings.tag : 'h2';
    var the_title = '<?php print esc_js(get_the_title());?>';
    var the_sitename = '<?php print esc_js( get_bloginfo( 'name' ) );?>';
    var the_sitetag = '<?php print esc_js( get_bloginfo( 'description' ) );?>';
    var site_year = '<?php print esc_js( date('Y') );?>';

    view.addRenderAttribute( 'section_heading', 'class', 'section-main-title' );

    if(settings.heading_source == 'site_title'){
      the_title = the_sitename;
    }
    else if(settings.heading_source == 'site_tagline'){
      the_title = the_sitetag;
    }
    else if(settings.heading_source == 'site_year'){
      the_title = site_year;
    }

    view.addRenderAttribute( 'heading_container', 'class', 'gum-widget-title' );

    if( settings.font_size !='' ){
      view.addRenderAttribute( 'section_heading', 'class', 'size-'+ settings.font_size );
    }

    if( settings.line_position !='' ){
      view.addRenderAttribute( 'heading_container', 'class', 'decor-'+ settings.line_position );
    }

    if( settings.line_align !='' ){
      view.addRenderAttribute( 'heading_container', 'class', 'decorpos-'+ settings.line_align );
    }

    if(settings.heading_source == 'text'){

      view.addRenderAttribute( 'prefix_heading' , 'class','prefix');
      view.addInlineEditingAttributes( 'prefix_heading' );  

      view.addRenderAttribute( 'main_heading' , 'class','maintitle' );
      view.addInlineEditingAttributes( 'main_heading' );  

      view.addRenderAttribute( 'sub_heading' , 'class','subfix');
      view.addInlineEditingAttributes( 'sub_heading' );  


    #>
    <div {{{ view.getRenderAttributeString( 'heading_container' ) }}}><{{{ tag_title}}} {{{ view.getRenderAttributeString( 'section_heading' ) }}}><span {{{ view.getRenderAttributeString( 'prefix_heading' ) }}}>{{{ settings.prefix_heading }}}</span><span {{{ view.getRenderAttributeString( 'main_heading' ) }}}>{{{ settings.main_heading }}}</span><span {{{ view.getRenderAttributeString( 'sub_heading' ) }}}>{{{ settings.sub_heading }}}</span></{{{ tag_title }}}></div>
    <# }else{ 
    #>
    <div {{{ view.getRenderAttributeString( 'heading_container' ) }}}><{{{ tag_title}}} {{{ view.getRenderAttributeString( 'section_heading' ) }}}>{{{ the_title }}}</{{{ tag_title }}}></div>
    <# } #>
    <?php

  }
}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Gum_Elementor_Widget_Heading() );

?>