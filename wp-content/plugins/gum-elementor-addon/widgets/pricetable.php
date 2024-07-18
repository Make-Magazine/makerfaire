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
use Elementor\Scheme_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;

class Month_Anual_Pricetable_Regular_Widget extends Widget_Base {


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
    return 'gum_pricetable';
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

    return esc_html__( 'Price Table', 'gum-elementor-addon' );
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
    return 'fas fa-xs fa-dollar-sign';
  }

  public function get_keywords() {
    return [ 'wordpress', 'widget', 'price','pricing' ];
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


  public static function get_button_sizes() {
    return [
      'xs' => esc_html__( 'Extra Small', 'gum-elementor-addon' ),
      'sm' => esc_html__( 'Small', 'gum-elementor-addon' ),
      'md' => esc_html__( 'Medium', 'gum-elementor-addon' ),
      'lg' => esc_html__( 'Large', 'gum-elementor-addon' ),
      'xl' => esc_html__( 'Extra Large', 'gum-elementor-addon' ),
    ];
  }

  protected function _register_controls() {



    $this->start_controls_section(
      'header_title',
      [
        'label' => esc_html__( 'Header', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'block_name',
      [
        'label' => esc_html__( 'Title', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'ai' => [
          'active' => false,
        ],
        'placeholder' => esc_html__( 'Enter package name', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'block_subtitle',
      [
        'label' => esc_html__( 'Package Description', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXTAREA,
        'placeholder' => esc_html__( 'Enter package description', 'gum-elementor-addon' ),
        'rows' => 2,
        'label_block' => true,
      ]
    );

    $this->add_control(
      'tag',
      [
        'label' => esc_html__( 'Title HTML Tag', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'h1' => 'H1',
          'h2' => 'H2',
          'h3' => 'H3',
          'h4' => 'H4',
          'h5' => 'H5',
          'h6' => 'H6',
          'div' => 'div',
        ],
        'default' => 'h3',
      ]
    );

    $this->add_control(
      'layout',
      [
        'label' => esc_html__( 'Title Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
              '1' => esc_html__( 'Inside', 'gum-elementor-addon' ),
              '2' => esc_html__( 'Outside', 'gum-elementor-addon' ),
        ],
        'default' => '1',
        'style_transfer' => true,
      ]
    );


    $this->add_control(
      'badge',
      [
        'label' => esc_html__( 'Badge', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'ai' => [
          'active' => false,
        ],
        'description' => esc_html__( 'Add badge on table. Leave blank if not use it.', 'gum-elementor-addon' ),
      ]
    );

    $this->add_responsive_control(
      'badge_align',
      [
        'label' => esc_html__( 'Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => esc_html__( 'Left', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-left',
          ],
          'center' => [
            'title' => esc_html__( 'Left', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-center',
          ],
          'right' => [
            'title' => esc_html__( 'Right', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-right',
          ],
        ],
        'default' => 'right',
        'condition' => [
          'badge[value]!' => '',
        ],
      ]
    );


    $this->end_controls_section();


    $this->start_controls_section(
      'pricing_title',
      [
        'label' => esc_html__( 'Pricing', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'block_price',
      [
        'label' => esc_html__( 'Price', 'gum-elementor-addon' ),
        'type' => Controls_Manager::NUMBER,
        'default' => 100,
      ]
    );

    $this->add_control(
      'anual_price',
      [
        'label' => esc_html__( 'Annual Price', 'gum-elementor-addon' ),
        'type' => Controls_Manager::NUMBER,
        'default' => '',
        'description' => esc_html__( 'Pricing for 2nd period', 'gum-elementor-addon' ),
        'condition' => [
          'period' => 'yes',
          'double_period' => 'yes'
        ],
      ]
    );

    $this->add_control(
      'block_symbol',
      [
        'label' => esc_html__( 'Currency Symbol', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'default' => '$',
        'ai' => [
          'active' => false,
        ],
      ]
    );

    $this->add_control(
      'period',
      [
        'label' => esc_html__( 'Show Period', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_off' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => '',
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'main_period',
      [
        'label' => esc_html__( 'Period', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'default' => esc_html__( 'Monthly', 'gum-elementor-addon' ),
        'condition' => [
          'period' => 'yes',
        ],
        'ai' => [
          'active' => false,
        ],
      ]
    );

  $this->add_control(
      'double_period',
      [
        'label' => esc_html__( '2nd Period', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_off' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => '',
        'condition' => [
          'period' => 'yes',
        ],
      ]
    );


    $this->add_control(
      'anual_period',
      [
        'label' => esc_html__( 'Period', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'default' => esc_html__( 'Yearly', 'gum-elementor-addon' ),
        'ai' => [
          'active' => false,
        ],
        'condition' => [
          'period' => 'yes',
          'double_period' => 'yes'
        ],
      ]
    );


    $this->add_control(
      'price_position',
      [
        'label' => esc_html__( 'Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
              'title' => esc_html__( 'Before Title', 'gum-elementor-addon' ),
              'before' => esc_html__( 'Before Description', 'gum-elementor-addon' ),
              'after' => esc_html__( 'After Description', 'gum-elementor-addon' ),
        ],
        'default' => 'before',
        'separator' => 'before',
        'style_transfer' => true,
      ]
    );

    $this->end_controls_section();

    $repeater = new Repeater();

    $repeater->add_control(
      'list_content',
      [
        'label' => esc_html__( 'Price Item', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXTAREA,
        'default' => esc_html__( 'Lorem ipsum dolor sit amet', 'gum-elementor-addon' ),
        'rows' => 3,
        'ai' => [
          'active' => false,
        ],
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'list_icon',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
      ]
    );


    $this->start_controls_section(
      'section_price_items',
      [
        'label' => esc_html__( 'Price Items', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'show_list',
      [
        'label' => esc_html__( 'Price Items', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'Hide', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Show', 'gum-elementor-addon' ),
        'default' => 'yes',
        'style_transfer' => true,
      ]
    );

    $this->add_control(
      'lists',
      [
        'label' => esc_html__( 'Items', 'gum-elementor-addon' ),
        'type' => Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [
          [
            'list_content' => esc_html__( 'Price item #1', 'gum-elementor-addon' ),
          ],
          [
            'list_content' => esc_html__( 'Price item #2', 'gum-elementor-addon' ),
          ],
          [
            'list_content' => esc_html__( 'Price item #3', 'gum-elementor-addon' ),
          ],
        ],
        'title_field' => '{{{ list_content }}}',
        'condition' => ['show_list[value]' => 'yes'],
      ]
    );


    $this->end_controls_section();


    $this->start_controls_section(
      'section_button',
      [
        'label' => esc_html__( 'Footer & Button', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'show_footer',
      [
        'label' => esc_html__( 'Footer', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'Hide', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Show', 'gum-elementor-addon' ),
        'default' => 'yes',
        'style_transfer' => true,
      ]
    );

    $this->add_control(
      'show_button',
      [
        'label' => esc_html__( 'Action Button', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'Hide', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Show', 'gum-elementor-addon' ),
        'default' => 'yes',
        'condition' => ['show_footer[value]' => 'yes'],
        'style_transfer' => true,
      ]
    );


    $this->add_control(
      'button_text',
      [
        'label' => esc_html__( 'Text', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'dynamic' => [
          'active' => true,
        ],
        'ai' => [
          'active' => false,
        ],
        'default' => esc_html__( 'Click here', 'gum-elementor-addon' ),
        'condition' => ['show_button[value]' => 'yes']
      ]
    );

    $this->add_control(
      'link',
      [
        'label' => esc_html__( 'Link', 'gum-elementor-addon' ),
        'type' => Controls_Manager::URL,
        'dynamic' => [
          'active' => true,
        ],
        'placeholder' => esc_html__( 'https://your-link.com', 'gum-elementor-addon' ),
        'default' => [
          'url' => '#',
        ],
        'description' => esc_html__( 'The link will active when 2nd price period activated.', 'gum-elementor-addon' ),
        'condition' => ['show_button[value]' => 'yes']
      ]
    );


    $this->add_control(
      'anual_link',
      [
        'label' => esc_html__( 'Annual Link', 'gum-elementor-addon' ),
        'type' => Controls_Manager::URL,
        'dynamic' => [
          'active' => true,
        ],
        'placeholder' => esc_html__( 'https://your-link.com', 'gum-elementor-addon' ),
        'default' => [
          'url' => '#',
        ],
        'condition' => [
          'show_button[value]' => 'yes',
          'period' => 'yes',
          'double_period' => 'yes'
        ],
      ]
    );

    $this->add_control(
      'selected_icon',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
        'condition' => ['show_button[value]' => 'yes']
      ]
    );

    $this->add_control(
      'icon_align',
      [
        'label' => esc_html__( 'Icon Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'left',
        'options' => [
          'left' => esc_html__( 'Before', 'gum-elementor-addon' ),
          'right' => esc_html__( 'After', 'gum-elementor-addon' ),
        ],
        'condition' => [
          'selected_icon[value]!' => '',
          'show_button[value]' => 'yes'
        ],
      ]
    );

    $this->end_controls_section();


/*
 * style params
 */


    $this->start_controls_section(
      'section_table_style',
      [
        'label' => esc_html__( 'Table', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    


    $this->add_responsive_control(
      'table_scale',
      [
        'label' => esc_html__( 'Scaling', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0.5,
            'max' => 2,
            'step'=> 0.1
          ],
        ],
        'default' => [
          'size' => '1',
          'unit' => 'px'
        ],
        'selectors' => [
          '{{WRAPPER}}' => 'transform: scale({{SIZE}});',
        ],
        'description' => esc_html__( 'Scale the table bigger or smaller. Normal no scalling', 'gum-elementor-addon' ),
      ]
    );

    
    $this->end_controls_section();

    $this->start_controls_section(
      'section_head_style',
      [
        'label' => esc_html__( 'Header', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    

    $this->add_responsive_control(
      'table_head_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );


    $this->add_responsive_control(
      'table_head_align',
      [
        'label' => esc_html__( 'Content Align', 'gum-elementor-addon' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => esc_html__( 'Left', 'gum-elementor-addon' ),
            'icon' => 'eicon-text-align-left',
          ],
          'center' => [
            'title' => esc_html__( 'Center', 'gum-elementor-addon' ),
            'icon' => 'eicon-text-align-center',
          ],
          'right' => [
            'title' => esc_html__( 'Right', 'gum-elementor-addon' ),
            'icon' => 'eicon-text-align-right',
          ],
          'justify' => [
            'title' => esc_html__( 'Justify', 'gum-elementor-addon' ),
            'icon' => 'eicon-text-align-justify',
          ]
        ],
        'default' => 'center',
        'selectors' => [
            '{{WRAPPER}} .price-block-inner .price-heading' => 'text-align: {{VALUE}};',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Background::get_type(),
      [
        'name' => 'head_background',
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'types' => [ 'classic'],
        'fields_options' => [
          'background' => [
            'default' => 'classic',
          ],
          'color' => [
            'default' => '#666666',
          ],
        ],
        'selector' => '{{WRAPPER}} .price-block-inner .price-heading',
      ]
    );


    $this->add_control(
      'head__hover_bgcolor',
      [
        'label' => esc_html__( 'Hover Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner:hover .price-heading' => 'background-color: {{VALUE}};'
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'head_border',
        'selector' => '{{WRAPPER}} .price-block-inner .price-heading',
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'head_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-heading' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ]
      ]
    );


    $this->add_control(
      'table_head_price_name',
      [
        'label' => esc_html__( 'Title', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );


    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_price_name',
        'selector' => '{{WRAPPER}} .price-block-inner .price-name'
      ]
    );

    $this->add_control(
      'price_name_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '#ffffff',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-name' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'price_name_hover_bolor',
      [
        'label' => esc_html__( 'Hover Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner:hover .price-name' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_responsive_control(
      'price_name_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-name' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => [
          'layout' => '2',
        ]
      ]
    );

    $this->add_responsive_control(
      'price_name_space',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 200,
            'step'=> 1
          ],
        ],
        'default' => [
          'size' => '',
          'unit' => 'px'
        ],
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'layout' => '1',
        ]
      ]
    );


    $this->add_control(
      'price_name_bgcolor',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-name' => 'background-color: {{VALUE}};',
        ],
        'condition' => [
          'layout' => '2',
        ],
      ]
    );


    $this->add_control(
      'price_name_hoover_bgcolor',
      [
        'label' => esc_html__( 'Hover Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner:hover .price-name,{{WRAPPER}} .price-block-inner:focus .price-name' => 'background-color: {{VALUE}};',
        ],
        'condition' => [
          'layout' => '2',
        ]
      ]
    );

    $this->add_control(
      'table_head_price_desc',
      [
        'label' => esc_html__( 'Package Description', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );


    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_price_desc',
        'selector' => '{{WRAPPER}} .price-block-inner .price-description',
      ]
    );


    $this->add_control(
      'price_desc_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '#ffffff',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-description' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'price_desc_hover_bolor',
      [
        'label' => esc_html__( 'Hover Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner:hover .price-description' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_responsive_control(
      'price_desc_space',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 200,
            'step'=> 1
          ],
        ],
        'default' => [
          'size' => '',
          'unit' => 'px'
        ],
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-description' => 'margin-bottom: {{SIZE}}{{UNIT}};'
        ],
      ]
    );


    $this->add_control(
      'table_head_badge_style',
      [
        'label' => esc_html__( 'Badge', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
        'condition' => [
          'badge[value]!' => '',
        ],
      ]
    );


    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_badge',
        'selector' => '{{WRAPPER}} .price-badge',
        'condition' => [
          'badge[value]!' => '',
        ],
      ]
    );

    $this->add_control(
      'badge_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-badge' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'badge[value]!' => '',
        ],

      ]
    );

    $this->add_control(
      'badge_bgcolor',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-badge' => 'background-color: {{VALUE}};',
        ],
        'condition' => [
          'badge[value]!' => '',
        ],
      ]
    );


    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'badge_border',
        'selector' => '{{WRAPPER}} .price-block-inner .price-badge',
        'condition' => [
          'badge[value]!' => '',
        ],
      ]
    );

    $this->add_control(
      'badge_border_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => [
          'badge[value]!' => '',
        ],
      ]
    );


    $this->add_responsive_control(
      'badge_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => [
          'badge[value]!' => '',
        ],
      ]
    );

    $this->add_responsive_control(
      'badge_space',
      [
        'label' => esc_html__( 'Vertical Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 200,
            'step'=> 1
          ],
        ],
        'default' => [
          'size' => '15',
          'unit' => 'px'
        ],
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-badge' => 'top: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'badge[value]!' => '',
        ],
      ]
    );


    $this->add_responsive_control(
      'badge_offset',
      [
        'label' => esc_html__( 'Horizontal Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 200,
            'step'=> 1
          ],
        ],
        'default' => [
          'size' => '15',
          'unit' => 'px'
        ],
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-badge.left' => 'left: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .price-block-inner .price-badge:NOT(.left):NOT(.center)' => 'right: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'badge[value]!' => '',
          'badge_align[value]!' => 'center',
        ],
      ]
    );
    $this->end_controls_section();

    $this->start_controls_section(
      'section_pricing_style',
      [
        'label' => esc_html__( 'Pricing', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    


    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_price_value',
        'selector' => '{{WRAPPER}} .price-heading .price-value',
      ]
    );


    $this->add_control(
      'price_value_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'price_value_hover_bolor',
      [
        'label' => esc_html__( 'Hover Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner:hover .price' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_responsive_control(
      'price_value_space',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 200,
            'step'=> 1
          ],
        ],
        'default' => [
          'size' => '',
          'unit' => 'px'
        ],
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price' => 'margin-bottom: {{SIZE}}{{UNIT}};'
        ],
      ]
    );

    $this->add_control(
      'table_currency_style',
      [
        'label' => esc_html__( 'Currency Symbol', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );


    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_currency',
        'selector' => '{{WRAPPER}} .price-heading .price-symbol',
      ]
    );


    $this->add_responsive_control(
      'price_currency_margin',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -50,
            'max' => 50,
            'step'=> 1
          ],
          'default' => ['value'=> -1,'unit'=>'px']
        ],
        'selectors' => [
          '{{WRAPPER}} .price-heading .price-symbol' => 'margin-right: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'price_currency_space',
      [
        'label' => esc_html__( 'Offset', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -50,
            'max' => 50,
            'step'=> 1
          ],
          'default' => ['value'=> -1,'unit'=>'px']
        ],
        'selectors' => [
          '{{WRAPPER}} .price-heading .price-symbol' => 'top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );


    $this->add_control(
      'table_period_style',
      [
        'label' => esc_html__( 'Period', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );


    $this->add_control(
      'period_position',
      [
        'label' => esc_html__( 'Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'below' => esc_html__( 'Below', 'gum-elementor-addon' ),
          'beside' => esc_html__( 'Beside', 'gum-elementor-addon' ),
        ],
        'default' => 'beside'
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_period',
        'selector' => '{{WRAPPER}} .price-heading .price-period',
      ]
    );


    $this->add_responsive_control(
      'price_period_margin',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -50,
            'max' => 50,
            'step'=> 1
          ],
          'default' => ['value'=> -1,'unit'=>'px']
        ],
        'selectors' => [
          '{{WRAPPER}} .price-heading .price-period.position-inline' => 'margin-left: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .price-heading .price-period.position-block' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();


    $this->start_controls_section(
      'section_list_style',
      [
        'label' => esc_html__( 'Price Items', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => ['show_list[value]' => 'yes'],
      ]
    );    


    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_list_title',
        'selector' => '{{WRAPPER}} .price-features li',
      ]
    );


    $this->add_control(
      'table_list_position',
      [
        'label' => esc_html__( 'Content Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'flex-start' => [
            'title' => esc_html__( 'Left', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-left',
          ],
          'center' => [
            'title' => esc_html__( 'Center', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-center',
          ],
          'flex-end' => [
            'title' => esc_html__( 'Right', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-right',
          ],
        ],
        'default' => 'center',
        'selectors' => [
            '{{WRAPPER}} .price-block-inner .price-features > li' => 'justify-content: {{VALUE}};',
        ],
      ]
    );
    
    $this->add_responsive_control(
      'table_list_align',
      [
        'label' => esc_html__( 'Content Align', 'gum-elementor-addon' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
              'left' => [
                'title' => esc_html__( 'Left', 'gum-elementor-addon' ),
                'icon' => 'eicon-text-align-left',
              ],
              'center' => [
                'title' => esc_html__( 'Center', 'gum-elementor-addon' ),
                'icon' => 'eicon-text-align-center',
              ],
              'right' => [
                'title' => esc_html__( 'Right', 'gum-elementor-addon' ),
                'icon' => 'eicon-text-align-right',
              ],
              'justify' => [
                'title' => esc_html__( 'Justify', 'gum-elementor-addon' ),
                'icon' => 'eicon-text-align-justify',
              ]
        ],
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-features > li' => 'text-align: {{VALUE}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'list_space',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 200,
          ],
        ],
        'default' => [
          'size' => 10,
          'unit' => 'px'
        ],
        'selectors' => [
          '{{WRAPPER}} ul li' => 'padding-bottom: {{SIZE}}{{UNIT}};padding-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );


    $this->add_responsive_control(
      'table_list_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-features' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'table_list_border',
        'selector' => '{{WRAPPER}} .price-block-inner .features',
      ]
    );

    $this->add_control(
      'table_list_radius',
      [
        'label' => esc_html__( 'Border Radius', 'month-annual-pricetable-elementor' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .features' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ]
      ]
    );


    $this->start_controls_tabs( 'table_list_styles' );

    $this->start_controls_tab(
      'table_list_style',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'list_title_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-features li span' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'list_bgcolor',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-features' => 'background-color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'list_odd_bgcolor',
      [
        'label' => esc_html__( 'Odd Row Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-features li:nth-child(2n)' => 'background-color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'list_even_bgcolor',
      [
        'label' => esc_html__( 'Even Row Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-features li:nth-child(2n+1)' => 'background-color: {{VALUE}};',
        ]
      ]
    );

    $this->end_controls_tab();
    $this->start_controls_tab(
      'table_list_hover_style',
      [
        'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'list_hover_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner:hover .price-features li span' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'list_hover_icon_color',
      [
        'label' => esc_html__( 'Icon Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner:hover .price-features li i' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'list_hover_bgcolor',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner:hover .price-features' => 'background-color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'list_odd_hover_bgcolor',
      [
        'label' => esc_html__( 'Odd Row Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner:hover .price-features li:nth-child(2n)' => 'background-color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'list_even_hover_bgcolor',
      [
        'label' => esc_html__( 'Even Row Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner:hover .price-features li:nth-child(2n+1)' => 'background-color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'list_hover_divider_color',
      [
        'label' => esc_html__( 'Divider Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner:hover .price-features > li:NOT(last-child)' => 'border-bottom-color: {{VALUE}};',
        ]
      ]
    );

    $this->end_controls_tab();
    $this->end_controls_tabs();


    $this->add_control(
      'section_list_icon_styles',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );


    $this->add_control(
      'section_list_icon_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-features li i' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'list_iconsize',
      [
        'label' => esc_html__( 'Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'em' => [
            'max' => 5,
          ],
        ],
        'default' =>['value'=>1, 'unit'=>'em'],
        'selectors' => [
          '{{WRAPPER}} .price-features li i' => 'font-size: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'list_icon_indent',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 100,
          ],
        ],
        'default' =>['value'=>10, 'unit'=>'px'],
        'selectors' => [
          '{{WRAPPER}} .price-features li i' => 'margin-right: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'list_icon_space',
      [
        'label' => esc_html__( 'Offset', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -50,
            'max' => 50,
            'step'=> 1
          ],
          'default' => ['value'=> '','unit'=>'px']
        ],
        'selectors' => [
          '{{WRAPPER}} .price-features li i' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'section_divider_styles',
      [
        'label' => esc_html__( 'Divider', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'list_divider_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-features > li:NOT(last-child)' => 'border-bottom-color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'list_divider_size',
      [
        'label' => esc_html__( 'Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
          'step' => 1,
        ],
        'default' => [
          'size' => 1,
          'unit' =>'px'
        ],
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-features > li' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'list_divider_space',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 200,
          ],
        ],
        'default' => [
          'size' => 15,
          'unit' => 'px'
        ],
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-features > li' => 'margin-left: {{SIZE}}{{UNIT}};margin-right: {{SIZE}}{{UNIT}};',
        ],
      ]
    );



    $this->end_controls_section();


    $this->start_controls_section(
      'footer_styles',
      [
        'label' => esc_html__( 'Footer', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => ['show_footer[value]' => 'yes']
      ]
    );    


    $this->add_responsive_control(
      'footer_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'footer_bgcolor',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '#666666',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-footer' => 'background-color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'footer_hover_bgcolor',
      [
        'label' => esc_html__( 'Hover Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .price-block-inner:hover .price-footer' => 'background-color: {{VALUE}};',
        ]
      ]
    );

    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'footer_border',
        'selector' => '{{WRAPPER}} .price-block-inner .price-footer',
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'footer_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .price-block-inner .price-footer' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ]
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_button_style',
      [
        'label' => esc_html__( 'Button', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => ['show_button[value]' => 'yes']
      ]
    );    

    $this->add_responsive_control(
      'button_align',
      [
        'label' => esc_html__( 'Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'center' => [
            'title' => esc_html__( 'Centered', 'gum-elementor-addon' ),
            'icon' => 'eicon-text-align-center',
          ],
          'justify' => [
            'title' => esc_html__( 'Full Width', 'gum-elementor-addon' ),
            'icon' => 'eicon-text-align-justify',
          ],
        ],
        'default' => '',
        'condition' => ['show_button[value]' => 'yes']
      ]
    );

    $this->add_control(
      'size',
      [
        'label' => esc_html__( 'Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'md',
        'options' => self::get_button_sizes(),
        'condition' => ['show_button[value]' => 'yes'],
        'style_transfer' => true,
      ]
    );

    $this->add_control(
      'icon_indent',
      [
        'label' => esc_html__( 'Icon Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 100,
          ],
        ],
        'default' =>['value'=>5, 'unit'=>'px'],
        'selectors' => [
          '{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
        ],
        'condition' => ['selected_icon[value]!' => ''],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'btn_typography',
        'selector' => '{{WRAPPER}} .elementor-button',
        'condition' => ['show_button[value]' => 'yes']
      ]
    );

    $this->add_group_control(
      Group_Control_Text_Shadow::get_type(),
      [
        'name' => 'btn_text_shadow',
        'selector' => '{{WRAPPER}} .elementor-button',
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
        'label' => esc_html__( 'Text Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'button_background_color',
      [
        'label' => esc_html__( 'Background Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};',
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
        'label' => esc_html__( 'Text Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}}!important;',
          '{{WRAPPER}} .elementor-button:hover svg, {{WRAPPER}} .elementor-button:focus svg' => 'fill: {{VALUE}}!important;',
        ],
      ]
    );

    $this->add_control(
      'button_background_hover_color',
      [
        'label' => esc_html__( 'Background Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};',
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
          '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'btn_hover_animation',
      [
        'label' => esc_html__( 'Hover Animation', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HOVER_ANIMATION,
      ]
    );

    $this->end_controls_tab();
    $this->end_controls_tabs();

    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'btn_border',
        'selector' => '{{WRAPPER}} .elementor-button',
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'btn_border_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ]
      ]
    );

    $this->add_group_control(
      Group_Control_Box_Shadow::get_type(),
      [
        'name' => 'button_box_shadow',
        'selector' => '{{WRAPPER}} .elementor-button',
      ]
    );

    $this->add_responsive_control(
      'btn_text_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'separator' => 'before'
      ]
    );

    $this->end_controls_section();


  }

  protected function render() {

    $settings = $this->get_settings_for_display();

    extract( $settings );

      $allowed_tags = array('h1','h2','h3','h4','h5','h6','div');
      $tag_title = (in_array( $tag, $allowed_tags )) ? trim( $tag ): 'h3';

      $this->add_render_attribute( 'block_price', 'class', 'regular-price');
      $this->add_inline_editing_attributes( 'block_price', 'none' );

    if($period=='yes'){


      $price_html = '<div class="price"><span class="price-symbol">'.esc_html($block_symbol).'</span>';
      $price_html .='<span class="price-value"><span '.$this->get_render_attribute_string( 'block_price' ).'>'.esc_html($block_price).'</span>';
      
      if($double_period == 'yes'){
        $price_html .='<span class="anual-price" style="display:none">'.esc_html($anual_price).'</span>';
      }
      
      $price_html .='</span>';

      $this->add_render_attribute( 'main_period', 'class', 'regular-period');
      $this->add_inline_editing_attributes( 'main_period', 'none' );
      

      $price_html .='<span class="price-period '.($period_position=='below' ? 'position-block':'position-inline').'"><span '.$this->get_render_attribute_string( 'main_period' ).'>'.esc_html($main_period).'</span>';
      if($double_period == 'yes'){
        $price_html .='<span class="anual-period" style="display:none">'.esc_html($anual_period).'</span>';
      }

      $price_html .='</span></div>';

    }
    else{
      $price_html = '<div class="price"><span class="price-symbol">'.esc_html($block_symbol).'</span><span class="price-value"><span '.$this->get_render_attribute_string( 'block_price' ).'>'.esc_html($block_price).'</span></span></div>';

    }

    $compile  = '<div class="temegum-price-table'.($double_period == 'yes' ? ' double-period':'' ).'"><div class="price-block-inner">';


    if($badge!=''){
      $this->add_render_attribute( 'badge', 'class', 'price-badge');
      $this->add_inline_editing_attributes( 'badge', 'none' );

      if($badge_align!=''){
          $this->add_render_attribute( 'badge', 'class', $badge_align);
      }

      $compile .= '<span '.$this->get_render_attribute_string( 'badge' ).'>'.esc_html($badge).'</span>';
    }

    if($layout!='2'){
      $compile .= '<div class="price-heading">';
    }

    if($price_position == 'title'){
      $compile .=  $price_html;
    }

    if($block_name!=''){
      $this->add_render_attribute( 'block_name', 'class', 'price-name');
      $this->add_inline_editing_attributes( 'block_name', 'none' );
      $compile .= '<'.$tag_title.' '.$this->get_render_attribute_string( 'block_name' ).'>'.esc_html($block_name).'</'.$tag_title.'>';
    }

    
    if($layout=='2'){
      $compile .= '<div class="price-heading">';
    }

    if($price_position == 'before'){
      $compile .=  $price_html;
    }


    if($block_subtitle!=''){

      $this->add_render_attribute( 'block_subtitle', 'class', 'price-description');
      $this->add_inline_editing_attributes( 'block_subtitle', 'none' );

      $compile .= '<h4 '.$this->get_render_attribute_string( 'block_subtitle' ).'>'.esc_html($block_subtitle).'</h4>';

    }

    if($price_position == 'after'){
      $compile .=  $price_html;
    }


    $compile .= '</div>';

    if( isset($lists) && count($lists) && $show_list =='yes'){

    $compile .= '<ul class="price-features">';

    foreach ($lists as $index => $list) {


            $repeater_setting_key = $this->get_repeater_setting_key( 'list_content', 'lists', $index );
            $this->add_inline_editing_attributes( $repeater_setting_key );

            $list_iconHTML = '';

            if(!empty($list['list_icon']['value'])){
                ob_start();
                Icons_Manager::render_icon( $list['list_icon'], [ 'aria-hidden' => 'true' ] );
                $list_iconHTML = ob_get_clean();
            }

           $compile.='<li class="elementor-repeater-item-'.esc_attr($list['_id']).'">'.$list_iconHTML.'<span '.$this->get_render_attribute_string( $repeater_setting_key ).'>'.strip_tags($list['list_content']).'</span></li>';
    }

    $compile .= '</ul>';

    }

    if($show_footer=='yes'){

        $button_html = '';

        if($show_button=='yes'){

              $button_icon = '';

              $this->add_render_attribute( 'button', 'class', [
                'elementor-button',
                'elementor-size-' . $size,
                'elementor-button-align-'.$button_align
              ] );


              $this->add_render_attribute( 'button_text', 'class', 'elementor-button-text');
              $this->add_inline_editing_attributes( 'button_text', 'none' );

              if ( ! empty( $link['url'] ) ) {
                $this->add_link_attributes( 'button', $link );
              }


              if($double_period == 'yes'){
                  $this->add_render_attribute( 'button', 'class', 'double-period');
              } 

              if ( $btn_hover_animation ) {
                $this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $btn_hover_animation );
              }


              if(!empty($selected_icon['value'])){


                ob_start();
                Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );
                $icon = ob_get_clean();


                 $button_icon = '<span class="elementor-button-icon elementor-align-icon-'.esc_attr($icon_align).'">'.$icon.'</span>';

              }

              $button_html = '<a '.$this->get_render_attribute_string( 'button' );


              if($double_period == 'yes'){
                $url1 = ! empty( $link['url'] ) ? $link['url']: '';
                $url2 = ! empty( $anual_link['url'] ) ? $anual_link['url']: '';

                $button_html .= ' data-regular="'.esc_url($url1).'" data-anual="'.esc_url($url2).'"';
              }

              $button_html .= '><span class="elementor-button-content-wrapper">';
              $button_html .= $button_icon;
              $button_html .= '<span '.$this->get_render_attribute_string( 'button_text' ).'>'.esc_html($button_text).'</span>';
              $button_html .= '</span></a>';

        }

        $compile .= '<div class="price-footer"><div class="price-btn">'.$button_html."</div></div>";
    
    }

    $compile.="</div></div>";

    print $compile;

  }

  protected function content_template() {
    ?>
<#  
    var price_html = '', list_iconsHTML = {};
    var allowed_tags = [ 'h1','h2','h3','h4','h5','h6','div' ];
    var tag_title = ( allowed_tags[ settings.tag ] && allowed_tags[ settings.tag ] != '' ) ? settings.tag : 'h3';

      view.addRenderAttribute( 'block_price', 'class', 'regular-price');
      view.addInlineEditingAttributes( 'block_price', 'none' );

    if ( settings.period=='yes' ){


      price_html  = '<div class="price"><span class="price-symbol">'+ settings.block_symbol + '</span>';
      price_html += '<span class="price-value"><span ' + view.getRenderAttributeString( 'block_price' ) + '>' + settings.block_price + '</span>';
      
      if ( settings.double_period == 'yes' ){
        price_html +='<span class="anual-price" style="display:none">' + settings.anual_price + '</span>';
      }
      
      price_html += '</span>';

      view.addRenderAttribute( 'main_period', 'class', 'regular-period');
      view.addInlineEditingAttributes( 'main_period', 'none' );
      
      price_html +='<span class="price-period '+ ( settings.period_position == 'below' ? 'position-block':'position-inline') + '"><span '+ view.getRenderAttributeString( 'main_period' ) + '>' + settings.main_period + '</span>';
      
      if ( settings.double_period == 'yes' ){
        price_html +='<span class="anual-period" style="display:none">'+ settings.anual_period + '</span>';
      }

      price_html +='</span></div>';

    }
    else{
      price_html = '<div class="price"><span class="price-symbol">' + settings.block_symbol + '</span><span class="price-value"><span ' + view.getRenderAttributeString( 'block_price' ) + '>' + settings.block_price + '</span></span></div>';

    }
#>    
<div class="temegum-price-table<# if ( settings.double_period=='yes' ) { #> double-period<# }#>">
  <div class="price-block-inner">
    <# if ( settings.badge !='' ){ 

      view.addRenderAttribute( 'badge', 'class', 'price-badge');
      view.addInlineEditingAttributes( 'badge', 'none' );

      if( settings.badge_align !='' ){
      view.addRenderAttribute( 'badge', 'class', settings.badge_align );
      } #>
      <span {{{ view.getRenderAttributeString( 'badge' ) }}}>{{{ settings.badge }}}</span>
    <# } #>
    <# if ( settings.layout != '2' ){ #>
      <div class="price-heading">
    <# } #>
    <# if ( settings.price_position == 'title' ){ #>
        {{{ price_html }}}
    <# } #>
    <# if ( settings.block_name !='' ){

      view.addRenderAttribute( 'block_name', 'class', 'price-name' );
      view.addInlineEditingAttributes( 'block_name', 'none' );

    #>
      <{{{ tag_title}}} {{{ view.getRenderAttributeString( 'block_name' ) }}}>{{{ settings.block_name }}}</{{{ tag_title }}}>
    <# } #>
    <# if ( settings.layout == '2' ){ #>
      <div class="price-heading">
    <# } #>
    <# if ( settings.price_position == 'before' ){ #>
        {{{ price_html }}}
    <# } #>
    <# if ( settings.block_subtitle !='' ){

      view.addRenderAttribute( 'block_subtitle', 'class', 'price-description');
      view.addInlineEditingAttributes( 'block_subtitle', 'none' ); 

      #>
      <h4 {{{ view.getRenderAttributeString( 'block_subtitle' ) }}}>{{{ settings.block_subtitle }}}</h4>
    <# } #>
    <# if ( settings.price_position == 'after' ){ #>
      {{{ price_html }}}
    <# } #>
      </div>
    <# 
    if ( settings.lists && settings.show_list == 'yes') { #>
      <ul class="price-features">
    <# 

    _.each( settings.lists, function( list, index) {

          var repeater_setting_key = view.getRepeaterSettingKey( 'list_content', 'lists', index );
          view.addInlineEditingAttributes( repeater_setting_key );#>
           <li class="elementor-repeater-item-{{ list._id }}">
            <# if ( list.list_icon  ) {

                  list_iconsHTML[ index ] = elementor.helpers.renderIcon( view, list.list_icon, { 'aria-hidden': 'true' }, 'i', 'object' );
                  if ( list_iconsHTML[ index ].rendered ) { #>
                    {{{ list_iconsHTML[ index ].value }}}
                   <#}
          } #>
          <span {{{ view.getRenderAttributeString( repeater_setting_key ) }}}>{{{ list.list_content }}}</span></li>
    <# }); #>
      </ul>
    <# }
    #>
    <# if ( settings.show_footer =='yes' ){

        var button_html = '';

        if ( settings.show_button=='yes' ){

              var button_icon = '';

              view.addRenderAttribute( 'button', 'class', [
                'elementor-button',
                'elementor-size-' + settings.size,
                'elementor-button-align-' + settings.button_align
              ] );


              view.addRenderAttribute( 'button_text', 'class', 'elementor-button-text');
              view.addInlineEditingAttributes( 'button_text', 'none' );

              if ( settings.double_period == 'yes' ){
                  view.addRenderAttribute( 'button', 'class', 'double-period');
              } 

              if ( settings.btn_hover_animation ) {
                view.addRenderAttribute( 'button', 'class', 'elementor-animation-' + settings.btn_hover_animation );
              }


              if ( settings.selected_icon.value !='' ){

                  var iconHTML = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),


                 button_icon = '<span class="elementor-button-icon elementor-align-icon-' + settings.icon_align + '">' + iconHTML.value +'</span>';

              }

              button_html = '<a ' + view.getRenderAttributeString( 'button' ) + ' href="' + settings.link.url + '" ';
              button_html += '><span class="elementor-button-content-wrapper">';
              button_html += button_icon;
              button_html += '<span ' + view.getRenderAttributeString( 'button_text' ) + '>' +  settings.button_text + '</span>';
              button_html += '</span></a>';

        } #>
        <div class="price-footer"><div class="price-btn">{{{ button_html }}}</div></div>
    <# } #>
  </div>
</div>
    <?php
  }

  public function enqueue_script( ) {
    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());
  }


}


// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Month_Anual_Pricetable_Regular_Widget() );

?>