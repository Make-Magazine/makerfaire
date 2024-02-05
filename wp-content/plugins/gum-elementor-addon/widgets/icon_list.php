<?php
namespace Elementor;

/**
 * @package     WordPress
 * @subpackage  Gum Elementor Addon
 * @author      support@themegum.com
 * @since       1.0.8
*/

defined('ABSPATH') or die();


class Gum_Elementor_Widget_Icon_List{


  public function __construct( $data = [], $args = null ) {

      add_action( 'elementor/element/icon-list/section_icon_style/after_section_end', array( $this, 'register_section_icon_style_controls') , 999 );
      add_action( 'elementor/element/icon-list/section_text_style/after_section_end', array( $this, 'register_section_text_style_controls') , 999 );

      add_action( 'elementor/element/icon-box/section_style_icon/after_section_end', array( $this, 'register_section_iconbox_style_controls') , 999 );
      add_action( 'elementor/element/icon-box/section_style_content/after_section_end', array( $this, 'register_section_style_content_controls') , 999 );
      add_action( 'elementor/element/image-box/section_style_content/after_section_end', array( $this, 'register_section_style_content_controls') , 999 );

      add_action( 'elementor/element/icon-box/section_icon/after_section_end', array( $this, 'register_section_strech_box_controls') , 999 );
      add_action( 'elementor/element/image-box/section_image/after_section_end', array( $this, 'register_section_strech_box_controls') , 999 );

      add_action( 'elementor/element/before_section_start', [ $this, 'enqueue_script' ] );

  }

  public function register_section_icon_style_controls( Controls_Stack $element ) {


    /**
    * - Add icon position left/right
    *
    */

    $element->start_injection( [
      'of' => 'icon_self_align',
    ] );

    $element->add_responsive_control(
      'icon_position',
      [
        'label' => esc_html__( 'Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          '0' => [
            'title' => esc_html__( 'Left', 'elementor' ),
            'icon' => 'eicon-h-align-left',
          ],
          '10' => [
            'title' => esc_html__( 'Right', 'elementor' ),
            'icon' => 'eicon-h-align-right',
          ],
        ],
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-list-icon' => 'order: {{VALUE}};'
        ],
        'prefix_class' => 'elementor-icon-list-ico-position-',
      ]
    );


    $element->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'icon_border',
        'selector' => '{{WRAPPER}} .elementor-icon-list-icon',
        'condition' => ['icon_self_align' => 'center'],
      ]
    );


    $element->add_control(
      'icon_border_hover',
      [
        'label' => esc_html__( 'Hover', 'elementor' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-list-item:hover .elementor-icon-list-icon' => 'border-color: {{VALUE}};',
        ],
        'condition' => ['icon_self_align' => 'center','icon_border_border!' => ''],
      ]
    );

    $element->add_control(
      'icon_background',
      [
        'label' => esc_html__( 'Background', 'elementor' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-list-icon' => 'background-color: {{VALUE}};',
        ],
        'condition' => ['icon_self_align' => 'center','icon_border_border!' => ''],
      ]
    );

    $element->add_control(
      'icon_hover_background',
      [
        'label' => esc_html__( 'Hover', 'elementor' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-list-item:hover .elementor-icon-list-icon' => 'background-color: {{VALUE}};',
        ],
        'condition' => ['icon_self_align' => 'center','icon_border_border!' => ''],
      ]
    );


    $element->add_control(
      'icon_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-list-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => ['icon_self_align' => 'center','icon_border_border!' => ''],
      ]
    );


    $element->add_responsive_control(
      'icon_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-list-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => ['icon_self_align' => 'center','icon_border_border!' => ''],
      ]
    );


    $element->end_injection();

  }

  function register_section_text_style_controls( Controls_Stack $element ) {


    $element->update_responsive_control(
      'icon_align',
      [
        'label' => esc_html__( 'Alignment', 'elementor' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => esc_html__( 'Left', 'elementor' ),
            'icon' => 'eicon-h-align-left',
          ],
          'center' => [
            'title' => esc_html__( 'Center', 'elementor' ),
            'icon' => 'eicon-h-align-center',
          ],
          'justify' => [
            'title' => esc_html__( 'Justify', 'elementor' ),
            'icon' => 'eicon-h-align-stretch',
          ],
          'right' => [
            'title' => esc_html__( 'Right', 'elementor' ),
            'icon' => 'eicon-h-align-right',
          ],
        ],
        'prefix_class' => 'elementor%s-align-',
      ]
    );

    $element->update_control(
      'text_indent',
      [
        'label' => esc_html__( 'Text Indent', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 50,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}}:NOT(.elementor-icon-list-ico-position-10) .elementor-icon-list-text' => is_rtl() ? 'padding-right: {{SIZE}}{{UNIT}};' : 'padding-left: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}}.elementor-icon-list-ico-position-10 .elementor-icon-list-text' => is_rtl() ? 'padding-left: {{SIZE}}{{UNIT}};' : 'padding-right: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $element->start_injection( [
      'of' => 'text_indent',
    ] );


    $element->add_responsive_control(
      'text_display',
      [
        'label' => esc_html__( 'Text Hidden', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'default' => '',
        'devices' => ['tablet','mobile'],
        'prefix_class' => 'elementor-icon-list-text-%s-hidden-',
      ]
    );

    $element->end_injection();


  }

  function register_section_style_content_controls( Controls_Stack $element ){


    $element->update_responsive_control(
      'title_bottom_space',
      [
        'label' => esc_html__( 'Spacing', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-box-title' => 'margin-bottom: {{SIZE}}{{UNIT}};margin-top: 0;',
          '{{WRAPPER}} .elementor-image-box-title' => 'margin-bottom: {{SIZE}}{{UNIT}};margin-top: 0;',
        ],
        'condition' => [
          'title_inline[value]!' => 'inline',
        ],
      ]
    );

    $element->start_injection( [
      'of' => 'title_bottom_space',
    ] );

    $element->add_responsive_control(
      'title_right_space',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
          'em' => [
            'min' => 0,
            'max' => 10,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-box-title' => 'margin-right: {{SIZE}}{{UNIT}};margin-top: 0;',
          '{{WRAPPER}} .elementor-image-box-title' => 'margin-right: {{SIZE}}{{UNIT}};margin-top: 0;',
        ],
        'size_units' => [ 'px' ,'em' ],
        'default'=>['size'=>'0.5','unit'=>'em'],
        'condition' => [
           'title_inline[value]' => 'inline',
        ],
      ]
    );

    $element->end_injection();


    $element->start_injection( [
      'of' => 'title_color',
    ] );

    $element->add_control(
      'title_hover_color',
      [
        'label' => esc_html__( 'Hover Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-box-title:hover,{{WRAPPER}} .elementor-icon-box-title:hover a' => 'color: {{VALUE}}!important;',
          '{{WRAPPER}} .elementor-image-box-title:hover,{{WRAPPER}} .elementor-image-box-title:hover a' => 'color: {{VALUE}}!important;',
        ],
        'condition' => [
          'link[url]!' => '',
        ],
      ]
    );

    $element->add_control(
      'boxhover_title_color',
      [
        'label' => esc_html__( 'On Box Hover', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}}:hover .elementor-icon-box-title' => 'color: {{VALUE}};',
          '{{WRAPPER}}:hover .elementor-image-box-title' => 'color: {{VALUE}};',
        ],
      ]
    );

    $element->end_injection();

    $element->start_injection( [
      'of' => 'description_color',
    ] );


    $element->add_control(
      'boxhover_description_color',
      [
        'label' => esc_html__( 'On Box Hover', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}}:hover .elementor-icon-box-description' => 'color: {{VALUE}};',
          '{{WRAPPER}}:hover .elementor-image-box-description' => 'color: {{VALUE}};',
        ],
      ]
    );

    $element->end_injection();


  }


  function register_section_iconbox_style_controls( Controls_Stack $element ) {


    $element->update_control(
      'hover_primary_color',
      [
        'label' => esc_html__( 'Primary Color', 'elementor' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}}.elementor-view-stacked:hover .elementor-icon:hover' => 'background-color: {{VALUE}};',
          '{{WRAPPER}}.elementor-view-framed:hover .elementor-icon:hover, {{WRAPPER}}.elementor-view-default:hover .elementor-icon:hover' => 'fill: {{VALUE}}; color: {{VALUE}}!important; border-color: {{VALUE}};',
        ],
      ]
    );

    $element->update_control(
      'hover_secondary_color',
      [
        'label' => esc_html__( 'Secondary Color', 'elementor' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'condition' => [
          'view!' => 'default',
        ],
        'selectors' => [
          '{{WRAPPER}}.elementor-view-framed:hover .elementor-icon:hover' => 'background-color: {{VALUE}}',
          '{{WRAPPER}}.elementor-view-stacked:hover .elementor-icon:hover' => 'fill: {{VALUE}}; color: {{VALUE}}',
        ],
      ]
    );


    $element->start_injection( [
      'of' => 'icon_space',
    ] );


    /**
    * - Add icon vertical position
    *
    */

    $element->add_responsive_control(
      'icon_top_margin',
      [
        'label' => esc_html__( 'Top Offset', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => '',
        ],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-box-icon' => 'margin-top: {{SIZE}}{{UNIT}}',
        ],
      ]
    );

    $element->end_injection();


    $element->start_injection( [
      'of' => 'border_radius',
    ] );

    $element->add_control(
      'boxheading_title',
      [
        'label' => esc_html__( 'On Box Hover', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $element->add_control(
      'boxhover_icon_color',
      [
        'label' => esc_html__( 'Primary Color', 'elementor' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}}.elementor-view-stacked:hover .elementor-icon' => 'background-color: {{VALUE}};',
          '{{WRAPPER}}.elementor-view-framed:hover .elementor-icon, {{WRAPPER}}.elementor-view-default:hover .elementor-icon' => 'fill: {{VALUE}}; color: {{VALUE}}; border-color: {{VALUE}};',
        ],
      ]
    );

    $element->add_control(
      'boxhover_icon_secondcolor',
      [
        'label' => esc_html__( 'Secondary Color', 'elementor' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'condition' => [
          'view!' => 'default',
        ],
        'selectors' => [
          '{{WRAPPER}}.elementor-view-framed:hover .elementor-icon' => 'background-color: {{VALUE}};',
          '{{WRAPPER}}.elementor-view-stacked:hover .elementor-icon' => 'fill: {{VALUE}}; color: {{VALUE}};',
        ],
      ]
    );

    $element->end_injection();

  }

  function register_section_strech_box_controls( Controls_Stack $element ){

    $element->start_injection( [
      'of' => 'title_size',
    ] );

    $element->add_control(
      'title_inline',
      [
        'label' => esc_html__( 'Title Display', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          '' => esc_html__( 'Default', 'gum-elementor-addon' ),
          'inline' => esc_html__( 'Inline', 'gum-elementor-addon' ),
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-box-title,{{WRAPPER}} .elementor-image-box-title' => 'display: {{VALUE}}',
          '{{WRAPPER}} .elementor-icon-box-description,{{WRAPPER}} .elementor-image-box-description' => 'display: {{VALUE}}',
        ],
      ]
    );

    $element->add_control(
      'box_strech',
      [
        'label' => esc_html__( 'Stretch Box', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'default' => '',
        'prefix_class' => 'elementor-boxstretch-',
      ]
    );

    $element->end_injection();


  }

  public function enqueue_script( ) {

    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());

  }

}

new \Elementor\Gum_Elementor_Widget_Icon_List();
?>
