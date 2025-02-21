<?php
namespace Elementor;
/**
 * @package     WordPress
 * @subpackage  Gum Elementor Addon
 * @author      support@themegum.com
 * @since       1.0.3
*/
defined('ABSPATH') or die();

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;

class Month_Anual_Pricetable_TogglePeriod_Regular_Widget extends Widget_Base {


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
    return 'gum_toggle_priceperiod';
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

    return esc_html__( 'Toggle Price Table', 'gum-elementor-addon' );
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
    return 'eicon-product-price';
  }

  public function get_keywords() {
    return [ 'wordpress', 'widget', 'toggle','price','period' ];
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
        'label' => esc_html__( 'Period', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'main_period',
      [
        'label' => esc_html__( '1st Period Label', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'label_block' => true,
        'dynamic' => [
          'active' => false,
        ],
        'ai' => [
          'active' => false,
        ],
        'default' => esc_html__( 'Monthly', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'anual_period',
      [
        'label' => esc_html__( '2nd Period Label', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'label_block' => true,
        'dynamic' => [
          'active' => false,
        ],
        'ai' => [
          'active' => false,
        ],
        'default' => esc_html__( 'Yearly', 'gum-elementor-addon' ),
      ]
    );


    $this->add_responsive_control(
      'align',
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
        'default' => 'center',
        'selectors' => [
          '{{WRAPPER}} .price-period-switch' => 'text-align: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'target_id',
      [
        'label' => esc_html__( 'Section Price CSS ID (optional)', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'ai' => [
          'active' => false,
        ],
        'default' => '',
        'title' => esc_html__( 'CSS ID from price table section NOT this widget ID. You can founded on target section settings: Advanced > CSS ID', 'gum-elementor-addon' ),
        'description' => esc_html__( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'gum-elementor-addon' ),
      ]
    );

    $this->end_controls_section();

/*
 * style params
 */

    $this->start_controls_section(
      'title_style',
      [
        'label' => esc_html__( 'Period', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    


    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_title',
        'selector' => '{{WRAPPER}} .period',
      ]
    );


    $this->add_responsive_control(
      'all_padding',
      [
        'label' => esc_html__( 'Vertical Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'allowed_dimensions' => 'vertical',
        'placeholder' => [
          'top' => '',
          'right' => '0',
          'bottom' => '',
          'left' => '0',
        ],
        'selectors' => [
          '{{WRAPPER}} .price-period-switch .period' => 'padding: {{TOP}}{{UNIT}} 0 {{BOTTOM}}{{UNIT}} 0;',
        ],
      ]
    );


    $this->add_control(
      'period_border_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .price-period-switch .period.regular-period' => 'border-radius: {{TOP}}{{UNIT}} 0px 0px {{LEFT}}{{UNIT}};',
          '{{WRAPPER}} .price-period-switch .period.anual-period' => 'border-radius: 0px {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} 0px;',
        ]
      ]
    );


    $this->add_control(
      'mainperiod',
      [
        'label' => esc_html__( '1st Period', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before'
      ]
    );

    $this->add_responsive_control(
      'main_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'allowed_dimensions' => 'horizontal',
        'placeholder' => [
          'top' => '0',
          'right' => '',
          'bottom' => '0',
          'left' => '',
        ],
        'selectors' => [
          '{{WRAPPER}} .price-period-switch .regular-period span' => 'padding: 0 {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}};',
        ],
      ]
    );


    $this->add_control(
      'mainperiod_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'description' => esc_html__( 'Warning: This style will remove next version. Please use radius on each period style', 'gum-elementor-addon' ),
        'selectors' => [
          '{{WRAPPER}} .price-period-switch .period.regular-period' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ]
      ]
    );

    $this->add_control(
      'anualperiod',
      [
        'label' => esc_html__( '2nd Period', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before'
      ]
    );
    
    $this->add_responsive_control(
      'anual_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'allowed_dimensions' => 'horizontal',
        'placeholder' => [
          'top' => '0',
          'right' => '',
          'bottom' => '0',
          'left' => '',
        ],
        'selectors' => [
          '{{WRAPPER}} .price-period-switch .anual-period span' => 'padding: 0 {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}};',
        ],
      ]
    );


    $this->add_control(
      'anualperiod_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .price-period-switch .period.anual-period' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ]
      ]
    );



    $this->start_controls_tabs( 'period_styles',['separator' => 'before'] );

    $this->start_controls_tab(
      'period_style',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'title_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-period-switch .period' => 'color: {{VALUE}};',
        ]
      ]
    );



    $this->add_control(
      'title_bgcolor',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-period-switch .period' => 'background-color: {{VALUE}};',
        ],
      ]
    );




    $this->end_controls_tab();
    $this->start_controls_tab(
      'period_active_style',
      [
        'label' => esc_html__( 'Active Period', 'gum-elementor-addon' ),
      ]
    );




    $this->add_control(
      'period_active_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-period-switch .period.active' => 'color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'period_active_bgcolor',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-period-switch .period.active' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_tab();
    $this->end_controls_tabs();


    $this->end_controls_section();


  }

  protected function render() {

    $settings = $this->get_settings_for_display();

    extract( $settings );

    $this->add_inline_editing_attributes( 'main_period', 'none' );
    $this->add_inline_editing_attributes( 'anual_period', 'none' );

?>
    <div class="price-period-switch-wrap">
      <ul class="price-period-switch" data-target="<?php esc_attr_e($target_id);?>">
      <li class="period active regular-period"><span <?php echo $this->get_render_attribute_string( 'main_period' ); ?>><?php esc_html_e($main_period);?></span></li><li class="period anual-period"><span <?php echo $this->get_render_attribute_string( 'anual_period' ); ?>><?php esc_html_e($anual_period);?></span></li>
      </ul>
    </div>
<?php

  }

  public function enqueue_script( ) {

    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());
    wp_enqueue_script( 'gum-price-table', GUM_ELEMENTOR_URL . 'js/price-table.js', array('jquery'), '1.0', false );
  }


}


// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Month_Anual_Pricetable_TogglePeriod_Regular_Widget() );

?>