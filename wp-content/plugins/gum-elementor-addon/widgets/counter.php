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

        add_action( 'elementor/element/counter/section_counter/after_section_end', array( $this, 'register_section_counter_controls') , 999 );
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

  public function register_section_counter_controls( Controls_Stack $element ){

    $element->start_injection( [
      'of' => 'section_counter',
    ] );


    $element->add_responsive_control(
      'section_counter_align',
      [
        'label' => esc_html__( 'Align', 'gum-elementor-addon' ),
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
        'prefix_class' => 'counter%s-align_',
        'selectors' => [
          '{{WRAPPER}} .elementor-counter-title' => 'text-align: {{VALUE}};',
        ],
      ]
    );

    $element->end_injection();
  }

}

new \Elementor\Gum_Elementor_Widget_CounterAddon();
?>
