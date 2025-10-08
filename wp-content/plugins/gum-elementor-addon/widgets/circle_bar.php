<?php
namespace Elementor;
/**
 * @package     WordPress
 * @subpackage  Gum Addon for Elementor
 * @author      support@themegum.com
 * @since       1.3.12
*/
defined('ABSPATH') or die();

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

class Gum_Elementor_Circlebar_Widget extends Widget_Base {


  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );

    $is_type_instance = $this->is_type_instance();

    if ( ! $is_type_instance && null === $args ) {
      throw new \Exception( '`$args` argument is required when initializing a full widget instance.' );
    }

    wp_register_script( 'easyPieChart', GUM_ELEMENTOR_URL . 'js/chart.js', [ 'elementor-frontend' ,'jquery'], '1.0.0', true );

    add_action( 'elementor/element/before_section_start', [ $this, 'enqueue_script' ] );

    if ( $is_type_instance ) {

      if(method_exists( $this, 'register_skins')){
         $this->register_skins();
       }else{
         $this->_register_skins();
       }
       
      $widget_name = $this->get_name();

      /**
       * Widget skin init.
       *
       * Fires when Elementor widget is being initialized.
       *
       * The dynamic portion of the hook name, `$widget_name`, refers to the widget name.
       *
       * @since 1.0.0
       *
       * @param Widget_Base $this The current widget.
       */
      do_action( "elementor/widget/{$widget_name}/skins_init", $this );
    }
  }

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
    return 'gum_circle_bar';
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

    return esc_html__( 'Circle Bar', 'gum-elementor-addon' );
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
    return 'fas fa-circle-notch eicon-counter-circle';
  }

  public function get_keywords() {
    return [ 'wordpress', 'widget', 'chart'];
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
      'piechart_title',
      [
        'label' => esc_html__( 'Circle Pie Chart', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'percent',
      [
        'label' => esc_html__( 'Percentage', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 50,
          'unit' => '%',
        ],
        'dynamic' => [
          'active' => false,
        ],
      ]
    );


    $this->add_control(
      'count_number',
      [
        'label' => esc_html__( 'Counter number', 'gum-elementor-addon' ),
        'type' => Controls_Manager::NUMBER,
        'dynamic' => [
          'active' => false,
        ],
        'ai' => [
          'active' => false,
        ],
        'default' => '',
      ]
    );


    $this->add_control(
      'count_unit',
      [
        'label' => esc_html__( 'Number prefix', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'dynamic' => [
          'active' => false,
        ],
        'ai' => [
          'active' => false,
        ],
        'default' => '',
      ]
    );


    $this->end_controls_section();



    $this->start_controls_section(
      'section_piechart_style',
      [
        'label' => esc_html__( 'Circle Pie Chart', 'gum-elementor-addon' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );


    $this->add_control(
      'line_color',
      [
        'label' => esc_html__( 'Active Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'global' => [
          'default' => Global_Colors::COLOR_PRIMARY,
        ],
        'selectors' => [
          '{{WRAPPER}} .bar-color' => 'color: {{VALUE}}',
        ],
        'style_transfer' => true,
      ]
    );



    $this->add_control(
      'track_color',
      [
        'label' => esc_html__( 'Track Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .bar-background' => 'color: {{VALUE}}',
        ],
        'style_transfer' => true,
      ]
    );


    $this->add_control(
      'linecap',
      [
        'label' => esc_html__( 'Line Cap', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'default' => '',
        'options' => array(
          ''=>esc_html__('Default','gum-elementor-addon'),
          'round'=>esc_html__('Round','gum-elementor-addon'),
          'square'=>esc_html__('Square','gum-elementor-addon'),
        ),
        'style_transfer' => true,
      ]
    );



    $this->add_control(
      'canvas_wide',
      [
        'label' => esc_html__( 'Canvas size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ '%'],
        'default' => [ 'size'=> 100],
        'description' => esc_html__( 'Bar wide equal with container width will activate pei chart mode.', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'canvas_align',
      [
        'label' => esc_html__( 'Alignment', 'gum-elementor-addon' ),
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
              ]
        ],
        'default' => 'center',
        'toggle' => false,
        'prefix_class' => 'canvas-position-',
      ]
    );

    $this->add_control(
      'line_wide',
      [
        'label' => esc_html__( 'Bar Wide', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px'],
        'default' => [ 'size'=> 15],
        'range' => [
          'px' => [
            'min' => 1,
            'max' => 2000,
          ],
        ],
      'description' => esc_html__( 'Bar wide equal with container width will activate pei chart mode.', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'track_wide',
      [
        'label' => esc_html__( 'Track Wide', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px'],
        'default' => [ 'size'=> 15],
        'range' => [
          'px' => [
            'min' => 1,
            'max' => 2000,
          ],
        ],
      ]
    );

    $this->end_controls_section();

 
    $this->start_controls_section(
      'number_styles',
      [
        'label' => esc_html__( 'Number', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'conditions' => [
          'relation' => 'or',
          'terms' => [
            ['name' =>'count_number','operator' => '!==', 'value' => ''],
            ['name' =>'count_unit','operator' => '!==', 'value' => ''],
          ]
        ],
      ]
    );    


    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'label' => esc_html__( 'Counter number', 'gum-elementor-addon' ),
        'name' => 'typography_number_title',
        'selector' => '{{WRAPPER}} .circle-bar-value',
        'separator' => 'before',
        'condition' => [
          'count_number[value]!' => '',
        ],
      ]
    );


    $this->add_control(
      'number_title_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .circle-bar-value' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'count_number[value]!' => '',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'label' => esc_html__( 'Number prefix', 'gum-elementor-addon' ),
        'name' => 'typography_number_prefix',
        'selector' => '{{WRAPPER}} .circle-bar-unit',
        'separator' => 'before',
        'condition' => [
          'count_unit[value]!' => '',
        ],
      ]
    );


    $this->add_control(
      'number_prefix_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .circle-bar-unit' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'count_unit[value]!' => '',
        ],
      ]
    );


    $this->add_responsive_control(
      'number_prefix_margin',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -50,
            'max' => 500,
            'step'=> 1
          ],
          'default' => ['value'=> -1,'unit'=>'px']
        ],
        'selectors' => [
          '{{WRAPPER}} .circle-bar-unit' => 'margin-left: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'count_unit[value]!' => '',
        ],

      ]
    );


    $this->end_controls_section();

  }

  protected function render() {

    $settings = $this->get_settings_for_display();

    extract( $settings );

    $percent = is_numeric( $percent['size'] ) ? $percent['size'] : '0';
    if ( 100 < $percent ) { $percent = 100;}

    $line = filter_var($line_wide['size'],  FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $track = filter_var($track_wide['size'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); 
    $cap = isset($linecap) &&  in_array( $linecap, array('round','square')) ? $linecap : '';
    $widgetID = "mod_". substr( $this->get_id_int(), 0, 4 );
    ?>
<div class="circle-bar-outer" id="<?php print esc_js(esc_attr($widgetID));?>" style="width:<?php print esc_attr($canvas_wide['size']);?>%">
<div class="circle-bar-label"><?php 
if( isset($count_number) && $count_number!=''){ printf('<span class="circle-bar-value">%s</span>', esc_html($count_number) ); }
if( isset($count_unit) && $count_unit!=''){ printf('<span class="circle-bar-unit">%s</span>', esc_html($count_unit) ); }
?>
</div>
<div  class="circle-bar" data-percent="<?php print(esc_attr($percent));?>"><span class="bar-color"></span><span class="bar-background"></span></div></div>
<script type="text/javascript">
jQuery(document).ready(function($){
    'use strict';

    var <?php print esc_js($widgetID).'clrtimer';?>,<?php print esc_js($widgetID).'_size';?>,<?php print esc_js($widgetID).'_line';?>=15,<?php print esc_js($widgetID).'_track';?> = 15,<?php print esc_js($widgetID).'_barColor';?>= $('#<?php print esc_js($widgetID);?>').find('.bar-color').css('color'),<?php print esc_js($widgetID).'_barBackground';?>= $('#<?php print esc_js($widgetID);?>').find('.bar-background').css('color');

    $(window).on('resize',function(e) { 
      <?php print esc_js($widgetID).'_size';?> = $('#<?php print esc_js($widgetID);?>').outerWidth();
      <?php print esc_js($widgetID).'_line';?> = Math.min(<?php print esc_js($widgetID).'_size';?>/2, <?php print(esc_attr($line));?>);
      <?php print esc_js($widgetID).'_track';?> = Math.min(<?php print esc_js($widgetID).'_size';?>/2, <?php print(esc_attr($track));?>);

      clearTimeout(<?php print esc_js($widgetID).'clrtimer';?>);
      <?php print esc_js($widgetID).'clrtimer';?> = setTimeout(function(){
        $('#<?php print esc_js($widgetID);?> .circle-bar').removeData('easyPieChart').find('canvas').remove();
        $('#<?php print esc_js($widgetID);?> .circle-bar').easyPieChart({
          barColor: <?php print esc_js($widgetID).'_barColor';?>,
          trackColor: <?php print esc_js($widgetID).'_barBackground';?>,
          scaleColor: false,
          lineCap: '<?php print(esc_attr($cap));?>',
          lineWidth: <?php print esc_js($widgetID).'_line';?>,
          trackWidth: <?php print esc_js($widgetID).'_track';?>,
          animate: 1200,
          size: <?php print esc_js($widgetID).'_size';?>
        });

      }, 100);

    });

    $(window).resize();


});

</script>
<?php

  }

  public function enqueue_script( ) {
    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());
  }

  public function get_script_depends() {
       return [ 'easyPieChart' ];
   }


}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Gum_Elementor_Circlebar_Widget() );

?>