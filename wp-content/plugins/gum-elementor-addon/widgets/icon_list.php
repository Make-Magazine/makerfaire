<?php
namespace Elementor;

/**
 * @package     WordPress
 * @subpackage  Gum Addon for Elementor
 * @author      support@themegum.com
 * @since       1.0.8
*/

defined('ABSPATH') or die();


class Gum_Elementor_Widget_Icon_List{


  public function __construct( $data = [], $args = null ) {

      add_action( 'elementor/element/icon-list/section_icon_style/after_section_end', array( $this, 'register_section_icon_style_controls') , 999 );
      add_action( 'elementor/element/icon-list/section_text_style/after_section_end', array( $this, 'register_section_text_style_controls') , 999 );
      
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
        'selector' => '{{WRAPPER}} .elementor-icon-list-icon'
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
        'condition' => ['icon_border_border!' => ''],
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
        'condition' => ['icon_border_border!' => ''],
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
        'condition' => ['icon_border_border!' => ''],
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
        'condition' => ['icon_border_border!' => ''],
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
        'condition' => ['icon_border_border!' => ''],
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

  public function enqueue_script( ) {

    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());

  }

}

new \Elementor\Gum_Elementor_Widget_Icon_List();
?>
