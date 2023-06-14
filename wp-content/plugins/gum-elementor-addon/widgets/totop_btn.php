<?php
namespace Elementor;
/**
 * @package     WordPress
 * @subpackage  Gum Elementor Addon
 * @author      support@themegum.com
 * @since       1.2.1
*/
defined('ABSPATH') or die();

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;

class Gum_Elementor_Totop_Btn_Widget extends Widget_Base {


  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );

    $is_type_instance = $this->is_type_instance();

    if ( ! $is_type_instance && null === $args ) {
      throw new \Exception( '`$args` argument is required when initializing a full widget instance.' );
    }

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
    return 'gum_totop';
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

    return esc_html__( 'To Top Button', 'gum-elementor-addon' );
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
    return 'far fa-xs fa-arrow-alt-circle-up';
  }

  public function get_keywords() {
    return [ 'wordpress', 'widget', 'button','top','scroll'];
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
      'button_title',
      [
        'label' => esc_html__( 'Button', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'button_title_heading',
      [
        'label' => esc_html__( 'Important: This widget only one per page. Insert inside the footer section is best.', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'description' => esc_html__( 'This widget only one per page. Insert inside the footer section is best.', 'gum-elementor-addon' ),
        'separator' =>'after'
      ]
    );

    $this->add_control(
      'button_align',
      [
        'label' => esc_html__( 'Position', 'gum-elementor-addon' ),
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
        'default' => 'right',
      ]
    );

    $this->add_control(
      'selected_icon',
      [
        'label' => esc_html__( 'Add Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
      ]
    );


    $this->end_controls_section();

/*
 * style params
 */

    $this->start_controls_section(
      'button_styles',
      [
        'label' => esc_html__( 'Button', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    


    $this->add_responsive_control(
      'button_height',
      [
        'label' => esc_html__( 'Height', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 200,
            'step' => 1,
            'min' => 20,
          ],
        ],  
        'default'=>['size'=>54,'unit'=>'px'],
        'selectors' => [
          '#totop_btn' => 'height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'button_width',
      [
        'label' => esc_html__( 'Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 200,
            'step' => 1,
            'min' => 20,
          ],

        ],  
        'default'=>['size'=>54,'unit'=>'px'],
        'selectors' => [
          '#totop_btn' => 'width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );


    $this->add_responsive_control(
      'button_hoffset',
      [
        'label' => esc_html__( 'H Offset', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 200,
            'step' => 1,
            'min' => 0,
          ],
          '%' => [
            'max' => 100,
            'step' => 1,
            'min' => 0,
          ],
        ],  
        'default'=>['size'=>30,'unit'=>'px'],
        'size_units' => [ 'px','%' ],
        'selectors' => [
          '#totop_btn.bottom-right' => 'right: {{SIZE}}{{UNIT}};',
          '#totop_btn.bottom-left' => 'left: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'button_align!' => 'center',
        ],
      ]
    );


    $this->add_responsive_control(
      'button_voffset',
      [
        'label' => esc_html__( 'V Offset', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 200,
            'step' => 1,
            'min' => 0,
          ],
          '%' => [
            'max' => 100,
            'step' => 1,
            'min' => 0,
          ],
        ],  
        'default'=>['size'=>30,'unit'=>'px'],
        'size_units' => [ 'px','%' ],
        'selectors' => [
          '#totop_btn' => 'bottom: {{SIZE}}{{UNIT}};',
        ],
        'separator' => 'after'
      ]
    );    

    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'btn_border',
        'selector' => '#totop_btn',
      ]
    );

    $this->add_control(
      'btn_border_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '#totop_btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ]
      ]
    );

    $this->add_responsive_control(
      'btn_text_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '#totop_btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ]
      ]
    );


    $this->start_controls_tabs( 'tabs_button_style' );

    $this->start_controls_tab(
      'tab_button_normal',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'button_text_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '#totop_btn .totop-wrapper span' => 'border-color: {{VALUE}};',
          '#totop_btn .totop-wrapper svg,#totop_btn .totop-wrapper i' => 'fill: {{VALUE}};color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'button_background_color',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '#totop_btn' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_tab();
    $this->start_controls_tab(
      'tab_button_hover',
      [
        'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'button_hover_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '#totop_btn:hover .totop-wrapper span, #totop_btn:focus .totop-wrapper span' => 'border-color: {{VALUE}}!important;',
          '#totop_btn:hover .totop-wrapper svg, #totop_btn:focus .totop-wrapper svg' => 'fill: {{VALUE}}!important;',
          '#totop_btn:hover .totop-wrapper i, #totop_btn:focus .totop-wrapper i' => 'color: {{VALUE}}!important;',
        ],
      ]
    );

    $this->add_control(
      'button_background_hover_color',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '#totop_btn:hover' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'button_hover_border_color',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'condition' => [
          'btn_border_border!' => '',
        ],
        'selectors' => [
          '#totop_btn:hover,#totop_btn:focus' => 'border-color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_tab();
    $this->end_controls_tabs();

    $this->add_group_control(
      Group_Control_Box_Shadow::get_type(),
      [
        'name' => 'button_box_shadow',
        'selector' => '#totop_btn',
      ]
    );

    $this->add_responsive_control(
      'icon_size',
      [
        'label' => esc_html__( 'Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 200,
          ],
        ],
        'default'=> ['size'=>'','unit'=> 'px'],
        'selectors' => [
          '#totop_btn .totop-wrapper i,#totop_btn .totop-wrapper svg' => 'font-size: {{SIZE}}{{UNIT}};',
          '#totop_btn .totop-wrapper span' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'icon_width',
      [
        'label' => esc_html__( 'Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 10,
          ],
        ],
        'default'=> ['size'=>'','unit'=> 'px'],
        'selectors' => [
          '#totop_btn .totop-wrapper span' => 'border-top-width: {{SIZE}}{{UNIT}};border-left-width:{{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'selected_icon[value]' => '',
        ],
      ]
    );

    $this->add_control(
      'icon_rotate',
      [
        'label' => esc_html__( 'Icon Rotate', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'deg' ],
        'default' => [
          'size' => 0,
          'unit' => 'deg',
        ],
        'selectors' => [
          '#totop_btn .totop-wrapper' => 'transform: rotate({{SIZE}}{{UNIT}})',
        ],
      ]
    );

    $this->end_controls_section();

  }

  protected function render() {


    global $gum_helper;

    if(!isset($gum_helper) || !isset( $gum_helper['totop_load'] )){
      $gum_helper['totop_load'] = false;
    }

    if($gum_helper['totop_load']) return;

    $settings = $this->get_settings_for_display();

    extract( $settings );

    $this->add_render_attribute( 'wrapper', ['class' => ['totop', 'bottom-'.$button_align],'id'=>'totop_btn'] );
    $top_html =  '<span></span>';


    if(!empty($selected_icon['value'])){

      ob_start();
      Icons_Manager::render_icon( $selected_icon, [ 'aria-hidden' => 'true' ] );
      $top_html = ob_get_clean();
    }
    ?><div <?php echo $this->get_render_attribute_string( 'wrapper' );?>><span class="totop-wrapper"><?php print $top_html; ?></span></div>
<?php
    
    $gum_helper['totop_load'] = true;

  }


  protected function content_template() {
    ?><#  

      view.addRenderAttribute( 'wrapper', 'class', ['totop', 'bottom-'+settings.button_align]);
      view.addRenderAttribute( 'wrapper', 'id', ['totop_btn']);

      var button_html = '<span></span>';

      if ( settings.selected_icon.value !='' ){
          iconHTML = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' );
          button_html = iconHTML.value;
      }

#><div {{{ view.getRenderAttributeString( 'wrapper' ) }}}><span class="totop-wrapper">{{{ button_html }}}</span></div>
    <?php
  }

  public function enqueue_script( ) {

    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());
    wp_enqueue_script( 'gum-elementor-addon', GUM_ELEMENTOR_URL . 'js/allscripts.js', array('jquery'), '1.0', false );
  }


}


// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Gum_Elementor_Totop_Btn_Widget() );

?>