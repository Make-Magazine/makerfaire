<?php
namespace Elementor;

/**
 * @package     WordPress
 * @subpackage  Gum Elementor Addon
 * @author      support@themegum.com
 * @since       1.2.7
*/

defined('ABSPATH') or die();

class Gum_Elementor_Widget_CounterAddon{


  public function __construct( ) {

        add_action( 'elementor/element/counter/section_number/after_section_end', array( $this, 'register_section_number_controls') , 999 );
        add_action( 'elementor/element/counter/section_title/after_section_end', array( $this, 'register_section_title_controls') , 999 );

  }


  public function register_section_title_controls( Controls_Stack $element ) {

   $element->start_injection( [
      'of' => 'title_color',
    ] );



    $element->add_responsive_control(
      'title_spacing',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
         'px' => [
            'min' => -200,
            'max' => 200,
          ],
        ],  
        'default'=>['size'=>'','unit'=>'px'],
        'size_units' => [ 'px' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-counter-title' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
       ]
    );

    $element->add_group_control(
      Group_Control_Text_Stroke::get_type(),
      [
        'name' => 'text_stroke_title',
        'selector' => '{{WRAPPER}} .elementor-counter-title',
      ]
    );


    $element->end_injection();


  }

  public function register_section_number_controls( Controls_Stack $element ) {


   $element->start_injection( [
      'of' => 'number_color',
    ] );

    $element->add_group_control(
      Group_Control_Text_Stroke::get_type(),
      [
        'name' => 'text_stroke_number',
        'selector' => '{{WRAPPER}} .elementor-counter-number-wrapper',
      ]
    );

    $element->end_injection();

  }

}

new \Elementor\Gum_Elementor_Widget_CounterAddon();
?>
