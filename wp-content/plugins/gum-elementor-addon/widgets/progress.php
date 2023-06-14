<?php
namespace Elementor;

/**
 * @package     WordPress
 * @subpackage  Gum Elementor Addon
 * @author      support@themegum.com
 * @since       1.2.12
*/

defined('ABSPATH') or die();

class Gum_Elementor_Widget_ProgressAddon{


  public function __construct( ) {

        add_action( 'elementor/element/progress/section_progress_style/after_section_end', array( $this, 'register_section_progress_style_controls') , 999 );
  }

  public function register_section_progress_style_controls( Controls_Stack $element ) {

   $element->start_injection( [
      'of' => 'bar_border_radius',
    ] );


    $element->add_control(
      'bartip_border_radius',
      [
        'label' => esc_html__( 'Tip Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-progress-wrapper .elementor-progress-bar' => 'border-radius: 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0;',
        ],
      ]
    );

    $element->end_injection();


   $element->start_injection( [
      'of' => 'inner_text_heading',
    ] );


    $element->add_control(
      'inner_text_align',
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
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-progress-wrapper .elementor-progress-text' => 'text-align: {{VALUE}};',
        ],
        'default' => '',
      ]
    );

    $element->add_responsive_control(
      'inner_text_padding',
      [
        'label' => esc_html__( 'Padding', 'elementor' ),
        'type' => Controls_Manager::DIMENSIONS,
        'allowed_dimensions' => 'horizontal',
        'size_units' => [ 'px', 'em', '%'  ],
        'selectors' => [
          '{{WRAPPER}} .elementor-progress-wrapper .elementor-progress-text' => 'padding: 0 {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}};',
        ]
      ]
    );

    $element->end_injection();


  }

}

new \Elementor\Gum_Elementor_Widget_ProgressAddon();
?>
