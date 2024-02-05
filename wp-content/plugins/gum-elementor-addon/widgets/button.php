<?php
namespace Elementor;

/**
 * @package     WordPress
 * @subpackage  Gum Elementor Addon
 * @author      support@themegum.com
 * @since       1.0.8
*/

defined('ABSPATH') or die();


class Gum_Elementor_Widget_Icon_Button{


  public function __construct( $data = [], $args = null ) {

      add_action( 'elementor/element/button/section_style/after_section_end', array( $this, 'register_section_icon_style_controls') , 999 );
      add_action( 'elementor/element/before_section_start', [ $this, 'enqueue_script' ] );
      add_action( 'elementor/element/video/section_image_overlay_style/after_section_end', array( $this, 'register_section_play_icon_controls') , 999 );

  }

  public function register_section_icon_style_controls( Controls_Stack $element ) {


    /**
    * - Add icon position left/right
    *
    */

    $element->start_controls_section(
      'button_icon_style',
      [
        'label' => esc_html__( 'Icon', 'elementor' ),
        'tab' => Controls_Manager::TAB_STYLE,
        'condition' => [ 'selected_icon[value]!' => '' ],
      ]
    );


    $element->add_control(
      'button_icon_size',
      [
        'label' => esc_html__( 'Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 100,
          ],
        ],
        'default' =>['value'=>'', 'unit'=>'px'],
        'selectors' => [
          '{{WRAPPER}} .elementor-button-icon' => 'font-size: {{SIZE}}{{UNIT}};',
        ],
      ]
    );


    $element->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'button_icon_border',
        'selector' => '{{WRAPPER}} .elementor-button-icon',
      ]
    );


    $element->add_responsive_control(
      'button_icon_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => ['button_icon_border_border!' => ''],
      ]
    );


    $element->add_control(
      'button_icon_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => ['button_icon_border_border!' => ''],
      ]
    );

    $element->start_controls_tabs( '_tabs_button_icon_style' );

    $element->start_controls_tab(
      '_tab_button_icon_normal',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );

    $element->add_control(
      'button_icon_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-button .elementor-button-icon i' => 'color: {{VALUE}};',
          '{{WRAPPER}} .elementor-button .elementor-button-icon svg *' => 'fill: {{VALUE}};',
        ],
      ]
    );

    $element->add_control(
      'button_icon_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-button .elementor-button-icon' => 'background: {{VALUE}};',
        ],
        'condition' => ['button_icon_border_border!' => ''],
      ]
    );

    $element->add_control(
      'rotate',
      [
        'label' => esc_html__( 'Rotate', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'deg' ],
        'default' => [
          'size' => 0,
          'unit' => 'deg',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button .elementor-button-icon i, {{WRAPPER}} .elementor-button .elementor-button-icon svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
        ],
      ]
    );

    $element->end_controls_tab();
    $element->start_controls_tab(
      '_tab_button_icon_hover',
      [
        'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
      ]
    );

    $element->add_control(
      'button_hover_icon_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover .elementor-button-icon i' => 'color: {{VALUE}};',
          '{{WRAPPER}} .elementor-button:hover .elementor-button-icon svg *' => 'fill: {{VALUE}};',
        ],
      ]
    );

    $element->add_control(
      'button_hover_icon_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover .elementor-button-icon' => 'background: {{VALUE}};',
        ],
        'condition' => ['button_icon_border_border!' => ''],
      ]
    );

    $element->add_control(
      'button_hover_icon_border',
      [
        'label' => esc_html__( 'Border', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover .elementor-button-icon' => 'border-color: {{VALUE}};',
        ],
        'condition' => ['button_icon_border_border!' => ''],
      ]
    );

    $element->add_control(
      'hover_rotate',
      [
        'label' => esc_html__( 'Rotate', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'deg' ],
        'default' => [
          'size' => '',
          'unit' => 'deg',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover .elementor-button-icon i, {{WRAPPER}} .elementor-button:hover .elementor-button-icon svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
        ],
      ]
    );

    $element->add_control(
      'content_box_transform_transition_hover',
      [
        'label' => esc_html__( 'Transition Duration (ms)', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 100,
            'max' => 10000,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button-content-wrapper' => '--e-button-transition-duration: {{SIZE}}ms',
        ],
      ]
    );


    $element->end_controls_tab();
    $element->end_controls_tabs();

    $element->end_controls_section();

  }

  public function register_section_play_icon_controls( Controls_Stack $element ){

    $element->remove_control( 'play_icon_color' );


    $element->start_injection( [
      'of' => 'play_icon_size',
    ] );


    $element->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'play_icon_border',
        'selector' => '{{WRAPPER}} .elementor-custom-embed-play',
        'condition' => [
          'show_image_overlay' => 'yes',
          'show_play_icon' => 'yes',
        ],
      ]
    );

 
    $element->add_control(
      'play_icon_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-custom-embed-play' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => [
          'show_image_overlay' => 'yes',
          'show_play_icon' => 'yes',
          'play_icon_border_border!' =>''
        ],
      ]
    );

    $element->add_responsive_control(
      'play_icon_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-custom-embed-play' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => [
          'show_image_overlay' => 'yes',
          'show_play_icon' => 'yes',
          'play_icon_border_border!' =>''
        ],
      ]
    );

    $element->start_controls_tabs( '_tabs_play_icon_style' );

    $element->start_controls_tab(
      '_tab_play_icon_normal',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );

    $element->add_control(
      "play_icon_opacity",
      [
        'label' => esc_html__( 'Opacity', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 1,
            'step' => 0.1,
          ],
        ],
        'condition' => [
          'show_image_overlay' => 'yes',
          'show_play_icon' => 'yes',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-custom-embed-play' => 'opacity: {{SIZE}};',
          '{{WRAPPER}} .elementor-custom-embed-play i' => 'opacity: 1;',
          '{{WRAPPER}} .elementor-custom-embed-play svg' => 'opacity: 1;',
        ],
      ]
    );

    $element->add_control(
      'play_icon_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-custom-embed-play i' => 'color: {{VALUE}}',
          '{{WRAPPER}} .elementor-custom-embed-play svg' => 'fill: {{VALUE}}',
        ],
        'condition' => [
          'show_image_overlay' => 'yes',
          'show_play_icon' => 'yes',
        ],
      ]
    );

    $element->add_control(
      'play_icon_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-custom-embed-play' => 'background: {{VALUE}};',
        ],
        'condition' => [
          'show_image_overlay' => 'yes',
          'show_play_icon' => 'yes',
          'play_icon_border_border!' =>''
        ],
      ]
    );        

    $element->end_controls_tab();
    $element->start_controls_tab(
      '_tab_play_icon_hover',
      [
        'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
      ]
    );

    $element->add_control(
      "play_icon_hover_opacity",
      [
        'label' => esc_html__( 'Opacity', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 1,
            'step' => 0.1,
          ],
        ],
        'condition' => [
          'show_image_overlay' => 'yes',
          'show_play_icon' => 'yes',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-custom-embed-image-overlay:hover .elementor-custom-embed-play' => 'opacity: {{SIZE}};',
        ],
      ]
    );

    $element->add_control(
      'play_icon_hovercolor',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-custom-embed-play:hover i' => 'color: {{VALUE}}',
          '{{WRAPPER}} .elementor-custom-embed-play:hover svg' => 'fill: {{VALUE}}',
        ],
        'condition' => [
          'show_image_overlay' => 'yes',
          'show_play_icon' => 'yes',
        ],
      ]
    );

    $element->add_control(
      'play_icon_hover_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-custom-embed-play:hover' => 'background: {{VALUE}};',
        ],
        'condition' => [
          'show_image_overlay' => 'yes',
          'show_play_icon' => 'yes',
          'play_icon_border_border!' =>''
        ],
      ]
    );

    $element->add_control(
      'play_icon_border_hover_color',
      [
        'label' => esc_html__( 'Border', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-custom-embed-play:hover' => 'border-color: {{VALUE}}!important;',
        ],
        'condition' => [
          'show_image_overlay' => 'yes',
          'show_play_icon' => 'yes',
          'play_icon_border_border!' =>''
        ],
      ]
    );

    $element->end_controls_tab();
    $element->end_controls_tabs();

    $element->end_injection();

  }


  public function enqueue_script( ) {

    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());

  }

}

new \Elementor\Gum_Elementor_Widget_Icon_Button();
?>
