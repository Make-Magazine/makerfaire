<?php
/**
 * @package     WordPress
 * @subpackage  Gum Elementor Addon
 * @author      support@themegum.com
 * @since       1.0.0
*/

defined('ABSPATH') or die();

use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;

class Gum_Elementor_Site_Nav_Widget extends Widget_Base {

  /**
   * Get widget name.
   *
   *
   * @since 1.0.0
   * @access public
   *
   * @return string Widget name.
   */

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


  public function get_name() {
    return 'gum_site_nav';
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

    return esc_html__( 'Site Navigation', 'gum-elementor-addon' );
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
    return 'fas fa-xs fa-bars';
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
    return [ 'wordpress', 'widget', 'menu', 'navigation', 'temegum' ];
  }

  protected function _register_controls() {

  $menus = wp_get_nav_menus( array() );

  $menu_options = array( '' => esc_html__( 'All Pages', 'gum-elementor-addon' ) );

  if ( ! empty ( $menus ) ) {
      foreach ( $menus as $item ) {
          $menu_options[ $item->term_id ] = $item->name;
      }
  }


  $typo_weight_options = [
    '' => esc_html__( 'Default', 'gum-elementor-addon' ),
  ];

  foreach ( array_merge( [ 'normal', 'bold' ], range( 100, 900, 100 ) ) as $weight ) {
    $typo_weight_options[ $weight ] = ucfirst( $weight );
  }

  $this->start_controls_section(
      'section_menu',
      [
        'label' => esc_html__( 'Menu', 'gum-elementor-addon' ),
      ]
  );

  $this->add_control(
    'menu_source',
    [
      'label' => esc_html__( 'Select Menu', 'gum-elementor-addon' ),
      'type' => Controls_Manager::SELECT,
      'options' =>  $menu_options,
      'default' => ''
    ]
  );



    $this->add_control(
      'responsive_breakpoint',
      [
        'label' => esc_html__( 'Breakpoint', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
              'none' => esc_html__('None','gum-elementor-addon'),
              'mobile' => esc_html__('Mobile','gum-elementor-addon'),
              'tablet' => esc_html__('Tablet','gum-elementor-addon'),
         ],
         'prefix_class' => 'make-responsive-',
         'default'=> 'mobile',
      ]
    );


    $this->end_controls_section();

/*
 * style params
 */
    $this->start_controls_section(
      'section_style_box',
      [
        'label' => esc_html__( 'Menu Item', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    


    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_menu_item',
        'selector' => '{{WRAPPER}} .gum-menu > .page_item > a,{{WRAPPER}} .gum-menu > .menu-item > a',
      ]
    );

    $this->add_responsive_control(
      'menu_item_gap',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          '%' => [
            'min' => 0,
            'max' => 100,
            'step' => 1,
          ],
          'px' => [
            'min' => 0,
            'max' => 200,
          ],

        ],  
        'default'=>['size'=>'','unit'=>'px'],
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .gum-menu > li' => 'margin-right: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'menu_item_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em' ],
        'default'=>['size'=>1,'unit'=>'em'],
        'selectors' => [
          '{{WRAPPER}} .gum-menu > .page_item > a,{{WRAPPER}} .gum-menu > .menu-item > a' => 'padding-top: {{TOP}}{{UNIT}};padding-left: {{LEFT}}{{UNIT}};padding-bottom: {{BOTTOM}}{{UNIT}};padding-right: {{RIGHT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'menu_item_radius',
      [
        'label' => esc_html__( 'Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .gum-menu > .page_item > a,{{WRAPPER}} .gum-menu > .menu-item > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'menu_item_border',
        'selector' => '{{WRAPPER}} .gum-menu > .page_item > a,{{WRAPPER}} .gum-menu > .menu-item > a',
      ]
    );

    $this->start_controls_tabs( 'menu_item_styles' );

    $this->start_controls_tab(
      'menu_item_style',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'menu_item_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .gum-menu > .page_item > a,{{WRAPPER}} .gum-menu > .menu-item > a' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'menu_item_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .gum-menu > .page_item > a,{{WRAPPER}} .gum-menu > .menu-item > a' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_tab();
    $this->start_controls_tab(
      'menu_item_hover_style',
      [
        'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'menu_item_hovercolor',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .gum-menu > .page_item > a:hover,{{WRAPPER}} .gum-menu > .menu-item > a:hover' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'menu_item_hoverbackground',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .gum-menu > .page_item > a:hover,{{WRAPPER}} .gum-menu > .menu-item > a:hover' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'menu_item_bdhover',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .gum-menu > .page_item > a:hover,{{WRAPPER}} .gum-menu > .menu-item > a:hover' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'menu_item_border_border!' => ''
        ],
      ]
    );


    $this->end_controls_tab();
    $this->start_controls_tab(
      'menu_item_active_style',
      [
        'label' => esc_html__( 'Active', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'menu_item_activecolor',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .gum-menu > .page_item.current_page_parent > a,{{WRAPPER}} .gum-menu > .menu-item.current-menu-parent > a' => 'color: {{VALUE}};',
          '{{WRAPPER}} .gum-menu > .page_item.current_page_item > a,{{WRAPPER}} .gum-menu > .menu-item.current-menu-item > a' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'menu_item_activebackground',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .gum-menu > .page_item.current_page_parent > a,{{WRAPPER}} .gum-menu > .menu-item.current-menu-parent > a' => 'background-color: {{VALUE}};',
          '{{WRAPPER}} .gum-menu > .page_item.current_page_item > a,{{WRAPPER}} .gum-menu > .menu-item.current-menu-item > a' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'menu_item_bdactive',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .gum-menu > .page_item.current_page_parent > a,{{WRAPPER}} .gum-menu > .menu-item.current-menu-parent > a' => 'border-color: {{VALUE}};',
          '{{WRAPPER}} .gum-menu > .page_item.current_page_item > a,{{WRAPPER}} .gum-menu > .menu-item.current-menu-item > a' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'menu_item_border_border!' => ''
        ],
      ]
    );

    $this->add_responsive_control(
      'menu_itemactive_font_weight',
      [
      'label' => esc_html__( 'Font Weight', 'gum-elementor-addon' ),
      'type' => Controls_Manager::SELECT,
      'default' => '',
      'options' => $typo_weight_options,
        'selectors' => [
          '{{WRAPPER}} .gum-menu > .page_item.current_page_parent > a,{{WRAPPER}} .gum-menu > .menu-item.current-menu-parent > a' => 'font-weight: {{VALUE}};',
          '{{WRAPPER}} .gum-menu > .page_item.current_page_item > a,{{WRAPPER}} .gum-menu > .menu-item.current-menu-item > a' => 'font-weight: {{VALUE}};',
        ]
      ]
    );

    $this->end_controls_tab();
    $this->end_controls_tabs();

    $this->add_control(
      'carret_heading',
      [
        'label' => esc_html__( 'CARET', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'carret_size',
      [
        'label' => esc_html__( 'Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 20,
          ],

        ],  
        'default'=>['size'=>5,'unit'=>'px'],
        'size_units' => [ 'px' ],
        'selectors' => [
          '{{WRAPPER}} .arrow span' => 'border-top-width: {{SIZE}}{{UNIT}};border-bottom-width: {{SIZE}}{{UNIT}};border-left-width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'carret_gap',
      [
        'label' => esc_html__( 'H Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 200,
          ],

        ],  
        'default'=>['size'=>'10','unit'=>'px'],
        'size_units' => [ 'px' ],
        'selectors' => [
          '{{WRAPPER}} .arrow' => 'padding-left: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'carret_offset',
      [
        'label' => esc_html__( 'V Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -20,
            'max' => 20,
          ],

        ],  
        'default'=>['size'=>'0','unit'=>'px'],
        'size_units' => [ 'px' ],
        'selectors' => [
          '{{WRAPPER}} .arrow' => 'transform: translateY({{SIZE}}{{UNIT}});',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'dropdown_style_box',
      [
        'label' => esc_html__( 'Dropdown', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    

    $this->add_control(
      'dropdownmenu_width',
      [
        'label' => esc_html__( 'Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 200,
            'max' => 1000,
          ],

        ],  
        'default'=>['size'=>'200','unit'=>'px'],
        'size_units' => [ 'px'],
        'selectors' => [
          '{{WRAPPER}} .gum-menu .sub-menu-container' => 'min-width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );


    $this->add_responsive_control(
      'dropdownmenu_gap',
      [
        'label' => esc_html__( 'Top Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 1000,
          ],

        ],  
        'default'=>['size'=>'','unit'=>'px'],
        'size_units' => [ 'px'],
        'selectors' => [
          '{{WRAPPER}} .gum-menu > li > .sub-menu-container' => 'padding-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'dropdown_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em' ],
        'allowed_dimensions' => 'vertical',
        'selectors' => [
          '{{WRAPPER}} .gum-menu .sub-menu > li:first-child' => 'padding-top: {{TOP}}{{UNIT}};',
          '{{WRAPPER}} .gum-menu .sub-menu > li:last-child' => 'padding-bottom: {{BOTTOM}}{{UNIT}};',
        ],
      ]
    );


    $this->add_control(
      'dropdown_radius',
      [
        'label' => esc_html__( 'Box Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .gum-menu .sub-menu > li:first-child' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};',
          '{{WRAPPER}} .gum-menu .sub-menu > li:last-child' => 'border-bottom-left-radius: {{BOTTOM}}{{UNIT}};border-bottom-right-radius: {{LEFT}}{{UNIT}};',
          '{{WRAPPER}} .gum-menu .sub-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'dropdown_border',
        'selector' => '{{WRAPPER}} .gum-menu .sub-menu',
      ]
    );

    $this->add_control(
      'dropdown_heading',
      [
        'label' => esc_html__( 'MENU ITEM', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_dropdownmenu_item',
        'selector' => '{{WRAPPER}} .gum-menu .sub-menu .page_item > a,{{WRAPPER}} .gum-menu .sub-menu .menu-item > a',
      ]
    );

    $this->add_responsive_control(
      'dropdownmenu_item_gap',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          '%' => [
            'min' => 0,
            'max' => 100,
            'step' => 1,
          ],
          'px' => [
            'min' => 0,
            'max' => 200,
          ],

        ],  
        'default'=>['size'=>'','unit'=>'px'],
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .gum-menu .sub-menu > li' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'dropdownmenu_item_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em' ],
        'selectors' => [
          '{{WRAPPER}} .gum-menu .sub-menu .page_item > a,{{WRAPPER}} .gum-menu .sub-menu .menu-item > a' => 'padding-top: {{TOP}}{{UNIT}};padding-left: {{LEFT}}{{UNIT}};padding-bottom: {{BOTTOM}}{{UNIT}};padding-right: {{RIGHT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'dropdownmenu_item_radius',
      [
        'label' => esc_html__( 'Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .gum-menu .sub-menu li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'dropdownmenu_item_border',
        'selector' => '{{WRAPPER}} .gum-menu .sub-menu li',
      ]
    );

    $this->start_controls_tabs( 'dropdownmenu_item_styles' );

    $this->start_controls_tab(
      'dropdownmenu_item_style',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'dropdownmenu_item_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .gum-menu .sub-menu .page_item > a,{{WRAPPER}} .gum-menu .sub-menu .menu-item > a' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'dropdownmenu_item_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .gum-menu .sub-menu > li' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_tab();
    $this->start_controls_tab(
      'dropdownmenu_item_hover_style',
      [
        'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'dropdownmenu_item_hovercolor',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .gum-menu .sub-menu .page_item > a:hover,{{WRAPPER}} .gum-menu .sub-menu .menu-item > a:hover' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'dropdownmenu_item_hoverbackground',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .gum-menu .sub-menu > li:hover' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'dropdownmenu_item_bdhover',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .gum-menu .sub-menu > li:hover' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'dropdownmenu_item_border_border!' => ''
        ],
      ]
    );


    $this->end_controls_tab();
    $this->start_controls_tab(
      'dropdownmenu_item_active_style',
      [
        'label' => esc_html__( 'Active', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'dropdownmenu_item_activecolor',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .gum-menu .sub-menu .page_item.page_item_has_children.current_page_parent > a,{{WRAPPER}} .gum-menu .sub-menu .menu-item.menu-item-has-children.current-menu-parent > a' => 'color: {{VALUE}};',
          '{{WRAPPER}} .gum-menu .sub-menu .page_item.current_page_item > a,{{WRAPPER}} .gum-menu .sub-menu .menu-item.current-menu-item > a' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'dropdownmenu_item_activebackground',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .gum-menu .sub-menu li.page_item.page_item_has_children.current_page_parent,{{WRAPPER}} .gum-menu .sub-menu li.menu-item.menu-item-has-children.current-menu-parent' => 'background-color: {{VALUE}};',
          '{{WRAPPER}} .gum-menu .sub-menu li.page_item.current_page_item,{{WRAPPER}} .gum-menu .sub-menu li.menu-item.current-menu-item' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'dropdownmenu_item_bdactive',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .gum-menu .sub-menu li.page_item.page_item_has_children.current_page_parent,{{WRAPPER}} .gum-menu .sub-menu li.menu-item.menu-item-has-children.current-menu-parent' => 'border-color: {{VALUE}};',
          '{{WRAPPER}} .gum-menu .sub-menu li.page_item.current_page_item,{{WRAPPER}} .gum-menu .sub-menu li.menu-item.current-menu-item' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'dropdownmenu_item_border_border!' => ''
        ],
      ]
    );

    $this->add_responsive_control(
      'dropdownmenu_itemactive_font_weight',
      [
      'label' => esc_html__( 'Font Weight', 'gum-elementor-addon' ),
      'type' => Controls_Manager::SELECT,
      'default' => '',
      'options' => $typo_weight_options,
        'selectors' => [
          '{{WRAPPER}} .gum-menu .sub-menu .page_item.page_item_has_children.current_page_parent > a,{{WRAPPER}} .gum-menu .sub-menu .menu-item.menu-item-has-children.current-menu-parent > a' => 'font-weight: {{VALUE}};',
          '{{WRAPPER}} .gum-menu .sub-menu .page_item.current_page_item > a,{{WRAPPER}} .gum-menu .sub-menu .menu-item.current-menu-item > a' => 'font-weight: {{VALUE}};',
        ]
      ]
    );

    $this->end_controls_tab();
    $this->end_controls_tabs();


    $this->end_controls_section(['condition' => ['responsive_breakpoint[value]!' => 'none']]);

    $this->start_controls_section(
      'mobile_title',
      [
        'label' => esc_html__( 'Mobile', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'responsive_breakpoint[value]!' => 'none'
        ],
      ]
    );

    $this->add_control(
      'mobile_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu' => 'background-color: {{VALUE}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu' => 'background-color: {{VALUE}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu' => 'background-color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'mobile_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ,'em' ],
        'default'=>['size'=>'','unit'=>'px'],
        'selectors' => [
          '[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );


    $this->add_control(
      'mobile_offset',
      [
        'label' => esc_html__( 'Vertical Offset', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -200,
            'max' => 200,
          ],

        ],  
        'default'=>['size'=>'25','unit'=>'px'],
        'size_units' => [ 'px'],
        'selectors' => [
          '[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu' => 'margin-top: {{SIZE}}{{UNIT}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu' => 'margin-top: {{SIZE}}{{UNIT}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );


    $this->add_control(
      'mobile_menu_heading',
      [
        'label' => esc_html__( 'MENU ITEM', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );


    $this->add_control(
      'mobile_menu_radius',
      [
        'label' => esc_html__( 'Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu > .page_item > a,[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu > .menu-item > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu > .page_item > a,[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu > .menu-item > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu > .page_item > a,[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu > .menu-item > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );



    $this->add_control(
      'mobile_menu_border',
      [
        'label' => esc_html__( 'Border Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px' ],
        'selectors' => [
          '[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu > .page_item > a,[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu > .menu-item > a' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu > .page_item > a,[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu > .menu-item > a' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu > .page_item > a,[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu > .menu-item > a' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => [
          'menu_item_border_border!' => ''
        ],
      ]
    );


    $this->add_control(
      'mobile_submenu_heading',
      [
        'label' => esc_html__( 'SUB MENU ITEM', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );


    $this->add_control(
      'mobile_subbackground',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu' => 'background: {{VALUE}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu' => 'background: {{VALUE}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu .sub-menu' => 'background: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'dropdownmenu_mobileitem_radius',
      [
        'label' => esc_html__( 'Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu .sub-menu li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );




    $this->add_control(
      'dropdownmenu_mobileitem_border',
      [
        'label' => esc_html__( 'Border Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px' ],
        'selectors' => [
          '[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu li' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu li' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu .sub-menu li' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => [
          'dropdownmenu_item_border_border!' => ''
        ],
      ]
    );



    $this->start_controls_tabs( 'dropdownmenu_mobileitem_styles' );

    $this->start_controls_tab(
      'dropdownmenu_mobileitem_style',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'dropdownmenu_mobileitem_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu .page_item > a,[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu .menu-item > a' => 'color: {{VALUE}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu .page_item > a,[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu .menu-item > a' => 'color: {{VALUE}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu .sub-menu .page_item > a,[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu .sub-menu .menu-item > a' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'dropdownmenu_mobileitem_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu li' => 'background-color: {{VALUE}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu li' => 'background-color: {{VALUE}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu .sub-menu li' => 'background-color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'dropdownmenu_mobileitem_bcolor',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu li' => 'border-color: {{VALUE}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu li' => 'border-color: {{VALUE}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu .sub-menu li' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'dropdownmenu_item_border_border!' => ''
        ],
      ]
    );


    $this->end_controls_tab();

    $this->start_controls_tab(
      'dropdownmenu_mobileitem_active_style',
      [
        'label' => esc_html__( 'Active', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'dropdownmenu_mobileitem_activecolor',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu .page_item.current_page_item > a,[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu .menu-item.current-menu-item > a' => 'color: {{VALUE}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu .page_item.current_page_item > a,[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu .menu-item.current-menu-item > a' => 'color: {{VALUE}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu .sub-menu .page_item.current_page_item > a,[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu .sub-menu .menu-item.current-menu-item > a' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'dropdownmenu_mobileitem_activebackground',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu li.page_item.current_page_item,[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu li.menu-item.current-menu-item' => 'background-color: {{VALUE}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu li.page_item.current_page_item,[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu li.menu-item.current-menu-item' => 'background-color: {{VALUE}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu .sub-menu li.page_item.current_page_item,[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu .sub-menu li.menu-item.current-menu-item' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'dropdownmenu_mobileitem_bdactive',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu li.page_item.current_page_item,[data-elementor-device-mode=tablet] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu li.menu-item.current-menu-item' => 'border-color: {{VALUE}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu li.page_item.current_page_item,[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-tablet .gum-menu .sub-menu li.menu-item.current-menu-item' => 'border-color: {{VALUE}};',
          '[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu .sub-menu li.page_item.current_page_item,[data-elementor-device-mode=mobile] {{WRAPPER}}.make-responsive-mobile .gum-menu .sub-menu li.menu-item.current-menu-item' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'dropdownmenu_item_border_border!' => ''
        ],
      ]
    );

    $this->end_controls_tab();
    $this->end_controls_tabs();

    $this->end_controls_section(['condition' => ['responsive_breakpoint[value]!' => 'none']]);

    $this->start_controls_section(
      'burger_title',
      [
        'label' => esc_html__( 'Burger Button', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'responsive_breakpoint[value]!' => 'none'
        ],
      ]
    );

    $this->add_control(
      'burger_width',
      [
        'label' => esc_html__( 'Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px'],
        'default'=>['size'=>22,'unit'=>'px'],
        'range' => [
          'px' => [
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .toggle-gum-menu .menu-bar span' => 'width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'burger_size',
      [
        'label' => esc_html__( 'Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px'],
        'default'=>['size'=>2,'unit'=>'px'],
        'range' => [
          'px' => [
            'min' => 1,
            'max' => 10,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .toggle-gum-menu .menu-bar span' => 'height: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .toggle-gum-menu .menu-bar span:nth-child(3)' => 'margin-top: calc(-{{SIZE}}{{UNIT}} - 5px);'
        ],
      ]
    );


    $this->add_control(
      'burger_line_radius',
      [
        'label' => esc_html__( 'Line Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'default'=>['size'=>1,'unit'=>'px'],
        'selectors' => [
          '{{WRAPPER}} .toggle-gum-menu .menu-bar span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );


    $this->add_control(
      'burger_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em' ],
        'selectors' => [
          '{{WRAPPER}} .toggle-gum-menu' => 'padding-top: {{TOP}}{{UNIT}};padding-right: {{RIGHT}}{{UNIT}};padding-bottom: {{BOTTOM}}{{UNIT}};padding-left: {{LEFT}}{{UNIT}};',
        ],
      ]
    );


    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'burger_border',
        'selector' => '{{WRAPPER}} .toggle-gum-menu',
      ]
    );


    $this->add_control(
      'burger_radius',
      [
        'label' => esc_html__( 'Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'default'=>['size'=>'','unit'=>'px'],
        'selectors' => [
          '{{WRAPPER}} .toggle-gum-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );


    $this->start_controls_tabs( 'burger_style' );

    $this->start_controls_tab(
      'burger_open_style',
      [
        'label' => esc_html__( 'Open Button', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'burger_open_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .toggle-collapse + .toggle-gum-menu .menu-bar span' => 'background-color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'burger_open_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .toggle-collapse + .toggle-gum-menu' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_tab();
    $this->start_controls_tab(
      'burger_close_style',
      [
        'label' => esc_html__( 'Close Button', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'burger_close_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .toggle-gum-menu .menu-bar span' => 'background-color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'burger_close_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .toggle-gum-menu' => 'background-color: {{VALUE}};',
        ],
      ]
    );



    $this->end_controls_tab();
    $this->end_controls_tabs();

    $this->end_controls_section();

  }

  public function get_nav_menu( $menu_id ) {

    return Gum_Elementor_Helper::_get_nav_menu( $menu_id ); 

  }

  protected function render() {

    $settings = $this->get_settings_for_display();

    extract( $settings );

    $html = '';

    $this->add_render_attribute( 'nav-wrapper', 'class', 'nav-wrapper' );

    $menus = $this->get_nav_menu( $menu_source );

    $html .= "<div ".$this->get_render_attribute_string( 'nav-wrapper' ).">";
    $html .= $menus;
    $html .= '<button class="toggle-gum-menu" type="button" onclick="javascript:;">';
    $html .= '<span class="menu-bar"><span></span><span></span><span></span><span></span></span></button>';
    $html .= "</div>";

    print $html;

  }


    public function enqueue_script( ) {

      wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());
      wp_enqueue_script( 'gum-elementor-addon', GUM_ELEMENTOR_URL . 'js/allscripts.js', array('jquery'), '1.0', false );

  }
}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Gum_Elementor_Site_Nav_Widget() );

?>