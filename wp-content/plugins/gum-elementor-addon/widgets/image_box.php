<?php
namespace Elementor;

/**
 * @package     WordPress
 * @subpackage  Gum Elementor Addon
 * @author      support@themegum.com
 * @since       1.3.9
*/

defined('ABSPATH') or die();


class Gum_Elementor_Widget_Image_Box{


  public function __construct( $data = [], $args = null ) {


      add_action( 'elementor/element/image-box/section_style_box/after_section_end', array( $this, 'register_section_image_style_controls') , 999 );

      add_action( 'elementor/element/icon-box/section_style_icon/after_section_end', array( $this, 'register_section_iconbox_style_controls') , 999 );
      add_action( 'elementor/element/icon-box/section_style_content/after_section_end', array( $this, 'register_section_style_content_controls') , 999 );
      add_action( 'elementor/element/image-box/section_style_content/after_section_end', array( $this, 'register_section_style_content_controls') , 999 );

      add_action( 'elementor/element/icon-box/section_icon/after_section_end', array( $this, 'register_section_strech_box_controls') , 999 );
      add_action( 'elementor/element/image-box/section_image/after_section_end', array( $this, 'register_section_strech_box_controls') , 999 );

      add_action( 'elementor/element/before_section_start', [ $this, 'enqueue_script' ] );

  }

  public function register_section_image_style_controls( Controls_Stack $element ) {


    $element->start_injection( [
      'of' => 'position',
    ] );


    $element->add_control(
      'keep_strech',
      [
        'label' => esc_html__( 'Keep Horizontal on Mobile', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'default' => '',
        'prefix_class' => 'elementor-keep-position-',
        'condition' => [
          'position!' => 'top',
        ],
      ]
    );

    $element->end_injection();


    $element->update_responsive_control(
      'image_space',
      [
        'label' => esc_html__( 'Image Spacing', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
        'default' => [
          'size' => 15,
        ],
        'range' => [
          'px' => [
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}}.elementor-position-right .elementor-image-box-img' => 'margin-left: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}}.elementor-position-left .elementor-image-box-img' => 'margin-right: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}}.elementor-position-top .elementor-image-box-img' => 'margin-bottom: {{SIZE}}{{UNIT}};',
          '(mobile){{WRAPPER}}.elementor-position-right.elementor-keep-position-yes .elementor-image-box-img' => 'margin-left: {{SIZE}}{{UNIT}}!important;margin-right:0!important;',
          '(mobile){{WRAPPER}}.elementor-position-left.elementor-keep-position-yes .elementor-image-box-img' => 'margin-right: {{SIZE}}{{UNIT}}!important;margin-left:0!important;',
          '(mobile){{WRAPPER}}:not(.elementor-keep-position-yes) .elementor-image-box-img' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'image[url]!' => '',
        ],
      ]
    );

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

  function register_section_style_content_controls( Controls_Stack $element ){


    $element->update_responsive_control(
      'title_bottom_space',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
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


  public function enqueue_script( ) {

    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());

  }

}

new \Elementor\Gum_Elementor_Widget_Image_Box();
?>
