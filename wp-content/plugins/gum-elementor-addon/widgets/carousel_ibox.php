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
use Elementor\Repeater;
use Gum_Elementor_Helper;

class Gum_Elementor_Widget_imagebox_carousel extends Widget_Base {

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
    return 'gum_ibox_carousel';
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

    return esc_html__( 'imBox Carousel', 'gum-elementor-addon' );
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
    return 'far fa-xs fa-image';
  }

  public function get_keywords() {
    return [ 'wordpress', 'widget', 'image', 'box','slider' ];
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
        'label' => esc_html__( 'Carousel Item', 'elementor' ),
      ]
    );

    $repeater = new Repeater();


    $repeater->add_control(
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
      ]
    );


    $repeater->add_control(
      'image',
      [
        'label' => esc_html__( 'Image', 'gum-elementor-addon' ),
        'type' => Controls_Manager::MEDIA,
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'button_label',
      [
        'label' => esc_html__( 'Button Text', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'default' => esc_html__( 'Button', 'gum-elementor-addon' ),
        'label_block' => true,
        'ai' => [
          'active' => false,
        ],
      ]
    );

    $repeater->add_control(
      'selected_icon',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
      ]
    );

    $repeater->add_control(
      'button_icon_align',
      [
        'label' => esc_html__( 'Icon Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'left',
        'options' => [
          'left' => esc_html__( 'Before', 'gum-elementor-addon' ),
          'right' => esc_html__( 'After', 'gum-elementor-addon' ),
        ],
      ]
    );


    $repeater->add_control(
      'content_title',
      [
        'label' => esc_html__( 'Title', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'dynamic' => [
          'active' => true,
        ],
        'ai' => [
          'active' => false,
        ],
        'default' => '',
        'placeholder' => esc_html__( 'Enter your title', 'gum-elementor-addon' ),
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'content_text',
      [
        'label' => esc_html__( 'Text', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXTAREA,
        'dynamic' => [
          'active' => true,
        ],
        'default' => '',
        'placeholder' => '',
        'rows' => 10,
        'show_label' => false,
      ]
    );

    $this->add_control(
      'slides',
      [
        'label' => esc_html__( 'Slide Items', 'gum-elementor-addon' ),
        'type' => Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [
          [
            'image'=>'',
            'button_label' => esc_html__( 'Button #1', 'gum-elementor-addon' ),
            'content_title' => esc_html__( 'Slide #1', 'gum-elementor-addon' ),
            'content_text' =>'',
            'selected_icon' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gum-elementor-addon' ),
          ],
          [
            'image'=>'',
            'button_label' => esc_html__( 'Button #2', 'gum-elementor-addon' ),
            'content_title' => esc_html__( 'Slide #2', 'gum-elementor-addon' ),
            'content_text' =>'',
            'selected_icon' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gum-elementor-addon' ),
          ],
          [
            'image'=>'',
            'button_label' => esc_html__( 'Button #3', 'gum-elementor-addon' ),
            'content_title' => esc_html__( 'Slide #3', 'gum-elementor-addon' ),
            'content_text' =>'',
            'selected_icon' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gum-elementor-addon' ),
          ],
        ],
        'title_field' => '{{{ content_title }}}'
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_setting',
      [
        'label' => esc_html__( 'Settings', 'elementor' ),
      ]
    );

    $this->add_group_control(
      Group_Control_Image_Size::get_type(),
      [
        'name' => 'thumbnail', 
        'default' => 'medium',
      ]
    );

    $this->add_control(
      'show_button',
      [
        'label' => esc_html__( 'Show Button', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          '' => esc_html__( 'None', 'gum-elementor-addon' ),
          'yes' => esc_html__( 'Over Box', 'gum-elementor-addon' ),
          'overimage' => esc_html__( 'Over Image', 'gum-elementor-addon' ),
          'overcontent' => esc_html__( 'Over Content Box', 'gum-elementor-addon' ),
          'bottom' => esc_html__( 'On Content Box', 'gum-elementor-addon' ),
        ],
        'default' => 'yes',
        'style_transfer' => true,
      ]
    );

    $this->add_control(
      'show_content',
      [
        'label' => esc_html__( 'Show Content', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          '' => esc_html__( 'None', 'gum-elementor-addon' ),
          'overimage' => esc_html__( 'Over Image', 'gum-elementor-addon' ),
          'yes' => esc_html__( 'Normal', 'gum-elementor-addon' ),
        ],
        'default' => 'yes',
        'style_transfer' => true,
      ]
    );


    $this->add_control(
      'title_tag',
      [
        'label' => esc_html__( 'Title Tag', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'h1' => 'H1',
          'h2' => 'H2',
          'h3' => 'H3',
          'h4' => 'H4',
          'h5' => 'H5',
          'h6' => 'H6',
          'div' => 'div',
          'span' => 'span',
          'p' => 'p',
        ],
        'default' => 'div',
        'condition' => [
          'show_content!' => '',
        ],
      ]
    );

    $this->add_control(
      'grid_layout',
      [
        'label' => esc_html__( 'Layout', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          '1' => esc_html__( 'One Column', 'gum-elementor-addon' ),
          '2' => esc_html__( 'Two Column', 'gum-elementor-addon' ),
          '3' => esc_html__( 'Three Column', 'gum-elementor-addon' ),
          '4' => esc_html__( 'Four Column', 'gum-elementor-addon' ),
          '5' => esc_html__( 'Five Column', 'gum-elementor-addon' ),
        ],
        'default' => '3',
        'separator' => 'before'
      ]
    );

    $this->add_control(
      'grid_table_layout',
      [
        'label' => esc_html__( 'Tablet Layout', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          '1' => esc_html__( 'One Column', 'gum-elementor-addon' ),
          '2' => esc_html__( 'Two Column', 'gum-elementor-addon' ),
          '3' => esc_html__( 'Three Column', 'gum-elementor-addon' ),
          '4' => esc_html__( 'Four Column', 'gum-elementor-addon' ),
          '5' => esc_html__( 'Five Column', 'gum-elementor-addon' ),
        ],
        'default' => '1'
      ]
    );

    $this->add_control(
      'grid_mobile_layout',
      [
        'label' => esc_html__( 'Mobile Layout', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          '1' => esc_html__( 'One Column', 'gum-elementor-addon' ),
          '2' => esc_html__( 'Two Column', 'gum-elementor-addon' ),
          '3' => esc_html__( 'Three Column', 'gum-elementor-addon' ),
          '4' => esc_html__( 'Four Column', 'gum-elementor-addon' ),
          '5' => esc_html__( 'Five Column', 'gum-elementor-addon' ),
        ],
        'default' => '1'
      ]
    );

    $this->add_control(
      'slide_autoplay',
      [
        'label' => esc_html__( 'Autoplay', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => 'yes',
      ]
    );

    $this->add_control(
      'slide_loop',
      [
        'label' => esc_html__( 'Infinity Loop', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => 'yes',
      ]
    );

    $this->add_control(
      'slide_interval',
      [
        'label' => esc_html__( 'Slide Interval', 'gum-elementor-addon' ),
        'type' => Controls_Manager::NUMBER,
        'default' => 5000,
      ]
    );

    $this->add_control(
      'slide_speed',
      [
        'label' => esc_html__( 'Slide Speed', 'gum-elementor-addon' ),
        'type' => Controls_Manager::NUMBER,
        'default' => 800,
      ]
    );

    $this->end_controls_section();


    $this->start_controls_section(
      'section_navigation',
      [
        'label' => esc_html__( 'Navigation', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'slide_navigation',
      [
        'label' => esc_html__( 'Type', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'arrow' => esc_html__( 'Arrow', 'gum-elementor-addon' ),
          'dot' => esc_html__( 'Dots', 'gum-elementor-addon' ),
          '' => esc_html__( 'None', 'gum-elementor-addon' ),
        ],
        'default' => 'dot'
      ]
    );

    $this->add_control(
      'pagination_align',
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
          'stretch' => [
            'title' => esc_html__( 'Full Width', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-stretch',
          ]
        ],
        'default' => '',
        'prefix_class' => 'navigation-',
        'selectors' => [
            '{{WRAPPER}} .owl-carousel .owl-dots,{{WRAPPER}} .owl-custom-pagination' => 'text-align: {{VALUE}};',
        ],
        'condition' => ['slide_navigation!' => '']
      ]
    );


    $this->add_control(
      'pagination_position',
      [
        'label' => esc_html__( 'Vertical', 'gum-elementor-addon' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'top' => [
            'title' => esc_html__( 'Top', 'gum-elementor-addon' ),
            'icon' => 'eicon-v-align-top',
          ],
          'middle' => [
            'title' => esc_html__( 'Middle', 'gum-elementor-addon' ),
            'icon' => 'eicon-v-align-middle',
          ],
          'bottom' => [
            'title' => esc_html__( 'Bottom', 'gum-elementor-addon' ),
            'icon' => 'eicon-v-align-bottom',
          ],
        ],
        'default' => 'bottom',
        'prefix_class' => 'position-',
        'condition' => ['slide_navigation' => 'arrow','pagination_align' => 'stretch']
      ]
    );


    $this->add_control(
      'left_icon',
      [
        'label' => esc_html__( 'Left Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
        'condition' => [
          'slide_navigation' => 'arrow',
        ],
      ]
    );

    $this->add_control(
      'right_icon',
      [
        'label' => esc_html__( 'Right Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
        'condition' => [
          'slide_navigation' => 'arrow',
        ],
      ]
    );


    $this->end_controls_section();

/*
 * style params
 */

    $this->start_controls_section(
      'post_grid_style',
      [
        'label' => esc_html__( 'Carousel Item', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    

    $this->add_responsive_control(
      'post_grid_height',
      [
        'label' => esc_html__( 'Min Height', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 1000,
            'step' => 5,
            'min' => 50,
          ],
          'vh' => [
            'max' => 100,
            'step' => 1,
            'min' => 10,
          ],

        ],  
        'default'=>['size'=>'','unit'=>'vh'],
        'size_units' => [ 'px' ,'vh' ],
        'selectors' => [
          '{{WRAPPER}} .grid-box .imb-box' => 'min-height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'post_grid_gutter',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
         'px' => [
            'max' => 1000,
          ],
        ],  
        'default'=>['size'=>'30','unit'=>'px'],
        'size_units' => [ 'px' ],
        'selectors' => [
          '{{WRAPPER}} .grid-imboxs .grid-box' => 'padding-left: calc({{SIZE}}{{UNIT}}/2);padding-right: calc({{SIZE}}{{UNIT}}/2);',
        ],
       ]
    );

    $this->add_responsive_control(
      'post_grid_padding',
      [
          'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', '%', 'em' ],
          'selectors' => [
              '{{WRAPPER}} .imb-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );

    $this->add_control(
      'post_grid_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .imb-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );
    
    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'post_grid_border',
        'selector' => '{{WRAPPER}} .grid-box .imb-box',
      ]
    );

    $this->add_control(
      'post_grid_bdhover',
      [
        'label' => esc_html__( 'Hover Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .grid-box .imb-box:hover' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'post_grid_border_border!' => ''
        ],
      ]
    );

    $this->add_control(
      'post_grid_bgcolor',
      [
        'label' => esc_html__( 'Background', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .grid-box .imb-box' => 'background-color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'post_grid_bghover',
      [
        'label' => esc_html__( 'Hover Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .grid-box .imb-box:hover' => 'background-color: {{VALUE}};',
        ]
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'post_grid_image',
      [
        'label' => esc_html__( 'Image Box', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    

    $this->add_responsive_control(
      'post_image_height',
      [
        'label' => esc_html__( 'Height', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 1000,
            'step' => 5,
            'min' => 50,
          ],
          'vh' => [
            'max' => 100,
            'step' => 1,
            'min' => 10,
          ],

        ],  
        'default'=>['size'=>'','unit'=>'px'],
        'size_units' => [ 'px' ,'vh' ],
        'selectors' => [
          '{{WRAPPER}} .grid-box .blog-featureimage' => 'height: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .grid-box .blog-featureimage img' => 'height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'post_image_minheight',
      [
        'label' => esc_html__( 'Min Height', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 1000,
            'step' => 5,
            'min' => 50,
          ],
          'vh' => [
            'max' => 100,
            'step' => 1,
            'min' => 10,
          ],

        ],  
        'default'=>['size'=>'','unit'=>'px'],
        'size_units' => [ 'px' ,'vh' ],
        'selectors' => [
          '{{WRAPPER}} .imb-box .blog-featureimage' => 'min-height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'post_image_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .grid-box .blog-featureimage' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'post_image_border',
        'selector' => '{{WRAPPER}} .grid-box .blog-featureimage',
      ]
    );

    $this->add_control(
      'post_image_bdhover',
      [
        'label' => esc_html__( 'Hover Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .imb-box:hover .blog-featureimage' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'post_image_border_border!' => ''
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'content_box_style',
      [
        'label' => esc_html__( 'Content Box', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'show_content!' => ''
        ],
      ]
    );    

    $this->add_control(
      'content_position',
      [
        'label' => esc_html__( 'Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'flex-start' => [
            'title' => esc_html__( 'Top', 'gum-elementor-addon' ),
            'icon' => 'eicon-v-align-top',
          ],
          'center' => [
            'title' => esc_html__( 'Middle', 'gum-elementor-addon' ),
            'icon' => 'eicon-v-align-middle',
          ],
          'flex-end' => [
            'title' => esc_html__( 'Bottom', 'gum-elementor-addon' ),
            'icon' => 'eicon-v-align-bottom',
          ],
        ],
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .imb-box-content' => 'justify-content: {{VALUE}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'content_align',
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
          '{{WRAPPER}} .imb-box-content' => 'text-align: {{VALUE}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'content_height',
      [
        'label' => esc_html__( 'Height', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 1000,
            'step' => 5,
            'min' => 50,
          ],
          'vh' => [
            'max' => 100,
            'step' => 1,
            'min' => 10,
          ],

        ],  
        'default'=>['size'=>'','unit'=>'px'],
        'size_units' => [ 'px' ,'vh' ],
        'selectors' => [
          '{{WRAPPER}} .imb-box .imb-box-content' => 'height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'content_minheight',
      [
        'label' => esc_html__( 'Min Height', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 1000,
            'step' => 5,
            'min' => 50,
          ],
          'vh' => [
            'max' => 100,
            'step' => 1,
            'min' => 10,
          ],

        ],  
        'default'=>['size'=>'','unit'=>'px'],
        'size_units' => [ 'px' ,'vh' ],
        'selectors' => [
          '{{WRAPPER}} .imb-box .imb-box-content' => 'min-height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );


    $this->add_responsive_control(
      'content_box_margin',
      [
          'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', '%', 'em' ],
          'selectors' => [
              '{{WRAPPER}} .imb-box-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );

    $this->add_responsive_control(
      'content_padding',
      [
          'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', '%', 'em' ],
          'selectors' => [
              '{{WRAPPER}} .imb-box-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );

    $this->add_control(
      'content_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .imb-box-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );


    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'content_border',
        'selector' => '{{WRAPPER}} .imb-box-content',
      ]
    );


    $this->start_controls_tabs( 'tabs_content_box_style' );

    $this->start_controls_tab(
      'tab_content_box_normal',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'content_bgcolor',
      [
        'label' => esc_html__( 'Background', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .imb-box-content' => 'background-color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'content_box_opacity',
      [
        'label' => esc_html__( 'Opacity', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          '%' => [
            'min' => 0,
            'max' => 1,
            'step' => 0.01,
          ],
        ],
        'default' =>['value'=>1, 'unit'=>'%'],
        'size_units' => [  '%' ],
        'selectors' => [
          '{{WRAPPER}} .imb-box-content' => 'opacity: {{SIZE}};',
        ],
      ]
    );

    $this->end_controls_tab();

    $this->start_controls_tab(
      'tab_content_box_hover',
      [
        'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'content_box_background_hover_color',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .imb-box:hover .imb-box-content, {{WRAPPER}} .imb-box:focus .imb-box-content' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'content_box_hover_border_color',
      [
        'label' => esc_html__( 'Border', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'condition' => [
          'content_border_border!' => '',
        ],
        'selectors' => [
          '{{WRAPPER}} .imb-box:hover .imb-box-content, {{WRAPPER}} .imb-box:focus .imb-box-content' => 'border-color: {{VALUE}};',
        ],
      ]
    );



    $this->add_control(
      'box_title_hcolor',
      [
        'label' => esc_html__( 'Title Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .imb-box:hover .imb-box-heading,{{WRAPPER}} .imb-box:hover .imb-box-heading' => 'color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'box_content_hcolor',
      [
        'label' => esc_html__( 'Content Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .imb-box:hover .imb-box-decription,{{WRAPPER}} .imb-box:focus .imb-box-decription' => 'color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'content_box_hoveropacity',
      [
        'label' => esc_html__( 'Opacity', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          '%' => [
            'min' => 0,
            'max' => 1,
            'step' => 0.01,
          ],
        ],
        'default' =>['value'=>1, 'unit'=>'%'],
        'size_units' => [  '%' ],
        'selectors' => [
          '{{WRAPPER}} .imb-box:hover .imb-box-content, {{WRAPPER}} .imb-box:focus .imb-box-content' => 'opacity: {{SIZE}};',
        ],
      ]
    );


    $this->add_control(
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
          '{{WRAPPER}} .imb-box-content' => '--e-box-transition-duration: {{SIZE}}ms',
        ],
      ]
    );

    $this->end_controls_tab();
    $this->end_controls_tabs();

    $this->add_control(
      'content_title_heading',
      [
        'label' => esc_html__( 'Title', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_content_title',
        'selector' => '{{WRAPPER}} .imb-box-heading',
      ]
    );

    $this->add_control(
      'content_title_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .imb-box-heading' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_responsive_control(
      'box_title_margin',
      [
          'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', '%', 'em' ],
          'selectors' => [
              '{{WRAPPER}} .imb-box-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );


    $this->add_control(
      'box_content_heading',
      [
        'label' => esc_html__( 'Content', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
        'condition' => [
          'show_content!' => '',
        ],
      ]
    );


    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_box_content',
        'selector' => '{{WRAPPER}} .imb-box-decription',
        'condition' => [
          'show_content!' => '',
        ],
      ]
    );

    $this->add_control(
      'box_content_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .imb-box-decription' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'show_content!' => '',
        ],
      ]
    );


    $this->add_responsive_control(
      'box_content_margin',
      [
        'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%', 'em' ],
        'selectors' => [
            '{{WRAPPER}} .imb-box-decription' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => [
          'show_content!' => '',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'box_button_style',
      [
        'label' => esc_html__( 'Button', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'show_button!' => ''
        ],
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
        'default' => '',
        'selectors' => [
            '{{WRAPPER}} .elementor-button-wrap' => 'text-align: {{VALUE}};',
        ],
        'condition' => ['show_button' => 'bottom']
      ]
    );

    $this->add_control(
      'button_width',
      [
        'label' => esc_html__( 'Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 1000,
          ],
          '%' => [
            'max' => 100,
          ],
        ],
        'default' =>['value'=>'', 'unit'=>'px'],
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'min-width: {{SIZE}}{{UNIT}};',
        ],
        'condition' => ['show_button!' => ''],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_box_button',
        'selector' => '{{WRAPPER}} .elementor-button',
      ]
    );

    $this->add_responsive_control(
      'box_button_margin',
      [
          'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', '%', 'em' ],
          'allowed_dimensions' => 'vertical',
          'selectors' => [
              '{{WRAPPER}} .elementor-button' => 'margin-top: {{TOP}}{{UNIT}};margin-bottom: {{BOTTOM}}{{UNIT}};',
          ],
      ]
    );

    $this->add_responsive_control(
      'box_button_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ]
      ]
    );
    
    $this->add_control(
      'box_button_radius',
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
      Group_Control_Border::get_type(),
      [
        'name' => 'box_button_border',
        'selector' => '{{WRAPPER}} .elementor-button',
      ]
    );

    $this->start_controls_tabs( 'tabs_box_button_style' );

    $this->start_controls_tab(
      'tab_box_button_normal',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'box_button_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'box_button_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'button_opacity',
      [
        'label' => esc_html__( 'Opacity', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          '%' => [
            'min' => 0,
            'max' => 1,
            'step' => 0.01,
          ],
        ],
        'default' =>['value'=>1, 'unit'=>'%'],
        'size_units' => [  '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'opacity: {{SIZE}};',
        ],
        'condition' => ['show_button!' => ''],
      ]
    );

    $this->end_controls_tab();

    $this->start_controls_tab(
      'tab_box_button_hover',
      [
        'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'box_button_hover_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}}!important;',
          '{{WRAPPER}} .elementor-button:hover svg, {{WRAPPER}} .elementor-button:focus svg' => 'fill: {{VALUE}}!important;',
        ],
      ]
    );

    $this->add_control(
      'box_button_background_hover_color',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'box_button_hover_border_color',
      [
        'label' => esc_html__( 'Border', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'condition' => [
          'box_button_border_border!' => '',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'button_hoveropacity',
      [
        'label' => esc_html__( 'Opacity', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          '%' => [
            'min' => 0,
            'max' => 1,
            'step' => 0.01,
          ],
        ],
        'default' =>['value'=>1, 'unit'=>'%'],
        'size_units' => [  '%' ],
        'selectors' => [
          '{{WRAPPER}} .imb-box:hover .elementor-button, {{WRAPPER}} .imb-box:focus .elementor-button' => 'opacity: {{SIZE}};',
        ],
        'condition' => ['show_button!' => ''],
      ]
    );
    $this->end_controls_tab();
    $this->end_controls_tabs();


    $this->add_control(
      'button_icon_heading',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
        'condition' => ['show_button!' => ''],
      ]
    );

    $this->add_control(
      'button_icon_indent',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
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
        'condition' => ['show_button!' => ''],
      ]
    );

    $this->add_control(
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
          '{{WRAPPER}} .elementor-button .elementor-button-icon' => 'font-size: {{SIZE}}{{UNIT}};',
        ],
        'condition' => ['show_button!' => ''],
      ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'button_icon_border',
        'selector' => '{{WRAPPER}} .elementor-button .elementor-button-icon',
        'condition' => ['show_button!' => ''],
      ]
    );

    $this->add_responsive_control(
        'button_icon_padding',
        [
            'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} .elementor-button .elementor-button-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => ['show_button!' => '','button_icon_border_border!' => ''],
        ]
    );

    $this->add_control(
      'button_icon_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button .elementor-button-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => ['show_button!' => '','button_icon_border_border!'=>''],
      ]
    );


    $this->start_controls_tabs( '_tabs_button_icon_style' );

    $this->start_controls_tab(
      '_tab_button_icon_normal',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'button_icon_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button .elementor-button-icon i,{{WRAPPER}} .elementor-button .elementor-button-icon svg' => 'color: {{VALUE}}!important,fill: {{VALUE}}!important;',
        ],
        'condition' => ['show_button!' => ''],
      ]
    );


    $this->add_control(
      'button_icon_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-button .elementor-button-icon' => 'background: {{VALUE}};',
        ],
        'condition' => ['show_button!' => '','button_icon_border_border!' => ''],
      ]
    );


    $this->add_control(
      'button_icon_rotate',
      [
        'label' => esc_html__( 'Rotate', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'deg' ],
        'default' => [
          'size' => 0,
          'unit' => 'deg',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button .elementor-button-icon i,{{WRAPPER}} .elementor-button .elementor-button-icon svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
        ],
        'condition' => ['show_button!' => ''],

      ]
    );


    $this->end_controls_tab();
    $this->start_controls_tab(
      '_tab_button_icon_hover',
      [
        'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
      ]
    );        

    $this->add_control(
      'button_icon_hover_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover .elementor-button-icon i,{{WRAPPER}} .elementor-button:hover .elementor-button-icon svg' => 'color: {{VALUE}}!important,fill: {{VALUE}}!important;',
        ],
        'condition' => ['show_button!' => ''],
      ]
    );

    $this->add_control(
      'button_hover_icon_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover .elementor-button-icon' => 'background: {{VALUE}};',
        ],
        'condition' => ['show_button!' => '','button_icon_border_border!' => ''],
      ]
    );

    $this->add_control(
      'button_icon_border_hover_color',
      [
        'label' => esc_html__( 'Border', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover .elementor-button-icon' => 'border-color: {{VALUE}}!important;',
        ],
        'condition' => ['show_button!' => '','button_icon_border_border!'=>''],
      ]
    );


    $this->add_control(
      'button_icon_hover_rotate',
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

    $this->add_control(
      'button_icon_transform_transition_hover',
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


    $this->end_controls_tab();
    $this->end_controls_tabs();

    $this->end_controls_section();

    $this->start_controls_section(
      'pagination_style',
      [
        'label' => esc_html__( 'Navigation', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'slide_navigation!' => ''
        ],
      ]
    );  

    $this->add_responsive_control(
      'pagination_item_width',
      [
        'label' => esc_html__( 'Size Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 200,
            'step'=> 1
          ],
        ],
        'default' => [
          'size' => '12',
          'unit' => 'px'
        ],
        'selectors' => [
          '{{WRAPPER}} .owl-dots .owl-dot span' => 'width: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'pagination_align!' => 'stretch',
        ],
      ]
    );

    $this->add_responsive_control(
      'pagination_item_height',
      [
        'label' => esc_html__( 'Size Height', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 200,
            'step'=> 1
          ],
        ],
        'default' => [
          'size' => '12',
          'unit' => 'px'
        ],
        'selectors' => [
          '{{WRAPPER}} .owl-dots .owl-dot span' => 'height: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'pagination_align!' => 'stretch',
        ]
      ]
    );

    $this->add_responsive_control(
      'pagination_item_spacing',
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
          'size' => 7,
          'unit' => 'px'
        ],
        'selectors' => [
          '{{WRAPPER}} .owl-dots .owl-dot span' => 'margin-left: calc({{SIZE}}{{UNIT}}/2);margin-right: calc({{SIZE}}{{UNIT}}/2);',
          '{{WRAPPER}} .owl-custom-pagination .btn-owl.prev' => 'margin-right: calc({{SIZE}}{{UNIT}}/2);',
          '{{WRAPPER}} .owl-custom-pagination .btn-owl.next' => 'margin-left: calc({{SIZE}}{{UNIT}}/2);',
        ],
        'condition' => [
          'pagination_align!' => 'stretch',
        ]
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_pagination',
        'selector' => '{{WRAPPER}} .owl-custom-pagination .btn-owl',
        'condition' => ['slide_navigation' => 'arrow','left_icon[value]' => '']
      ]
    );

    $this->add_responsive_control(
      'pagination_item_size',
      [
        'label' => esc_html__( 'Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 1000,
            'step'=> 1
          ],
          'em' => [
            'min' => 0,
            'max' => 100,
            'step'=> 1
          ],
        ],
        'default' => [ 'size' => '1.2','unit' => 'em'],
        'size_units' => [ 'px' ,'em' ],
        'selectors' => [
          '{{WRAPPER}} .owl-custom-pagination .btn-owl' => 'font-size: {{SIZE}}{{UNIT}};',
        ],
        'condition' => ['slide_navigation' => 'arrow','left_icon[value]!' => '','right_icon[value]!' => '']
      ]
    );

    $this->add_responsive_control(
      'pagination_margin',
      [
          'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', 'em' ],
          'selectors' => [
              '{{WRAPPER}} .owl-custom-pagination,{{WRAPPER}} .owl-carousel .owl-dots' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
          'separator' =>'before',
      ]
    );

    $this->add_responsive_control(
      'pagination_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .owl-custom-pagination .btn-owl' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => ['slide_navigation' => 'arrow']
      ]
    );

    $this->add_control(
      'pagination_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .owl-dots .owl-dot span,{{WRAPPER}} .owl-custom-pagination .btn-owl' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ]
      ]
    );

    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'pagination_border',
        'selector' => '{{WRAPPER}} .owl-dots .owl-dot span,{{WRAPPER}} .owl-custom-pagination .btn-owl',
      ]
    );


    $this->start_controls_tabs( 'tabs_pagination_style' );

    $this->start_controls_tab(
      'tab_pagination_normal',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'pagination_item_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .owl-custom-pagination .btn-owl' => 'color: {{VALUE}};',
          '{{WRAPPER}} .owl-dots .owl-dot span' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'pagination_item_bgcolor',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .owl-custom-pagination .btn-owl' => 'background-color: {{VALUE}};',
        ],
        'condition' => ['slide_navigation' => 'arrow']
      ]
    );

    $this->end_controls_tab();
    $this->start_controls_tab(
      'tab_pagination_current',
      [
        'label' => esc_html__( 'Current', 'gum-elementor-addon' ),
        'condition' => ['slide_navigation' => 'dot']
      ]
    );

    $this->add_control(
      'pagination_curitem_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .owl-dots .owl-dot.active span' => 'background-color: {{VALUE}};',
        ],
      ]
    );    

    $this->add_control(
      'pagination_curitem_bdcolor',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .owl-dots .owl-dot.active span' => 'border-color: {{VALUE}};',
        ],
        'condition' => ['pagination_border_border!' => '']
      ]
    );   

    $this->end_controls_tab();

    $this->start_controls_tab(
      'tab_pagination_hover',
      [
        'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'pagination_item_hovercolor',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .owl-custom-pagination .btn-owl:hover' => 'color: {{VALUE}};',
          '{{WRAPPER}} .owl-dots .owl-dot:hover span' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'pagination_item_hoverbgcolor',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .owl-custom-pagination .btn-owl:hover' => 'background-color: {{VALUE}};',
        ],
        'condition' => ['slide_navigation' => 'arrow']
      ]
    );

    $this->add_control(
      'pagination_item_hover_bdcolor',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .owl-custom-pagination .btn-owl:hover,{{WRAPPER}} .owl-dots .owl-dot:hover span' => 'border-color: {{VALUE}};',
        ],
        'condition' => ['pagination_border_border!' => '']
      ]
    );   


    $this->end_controls_tab();
    $this->end_controls_tabs();


    $this->end_controls_section();

  }

  protected function render() {

    $settings = $this->get_settings_for_display();

    extract( $settings );

    if(!count( $slides )) return;

    $widget_id=  substr( $this->get_id_int(), 0, 3 );
    $rows_html  = array();
    $tag = Utils::validate_html_tag( $title_tag );

    foreach ($slides as $index => $slide) {

        ob_start();

        $media = $slide['image'];

        $thumb_id = $media['id'];
        $image = ['id' => $thumb_id ];
        $settings['thumbnail'] = $image;
        $content_box = '';

        $image_url = Group_Control_Image_Size::get_attachment_image_src( $thumb_id, 'thumbnail', $settings);

        echo '<div class="imb-box button-style'.sanitize_html_class($show_button).'">';

        if($show_content!=''){

            $content_box = '<div class="imb-box-content">';
            $content_box .=  $slide['content_title']!=''? '<'.$tag.' class="imb-box-heading">'.( $show_button == '' && ! empty( $slide['link']['url'] )? sprintf( '<a href="%s">'.esc_html($slide['content_title']).'</a>', esc_url( $slide['link']['url'] ) ) : esc_html($slide['content_title']) ).'</'.$tag.'>' : '';
            $content_box .=  $slide['content_text'] !=''? '<div class="imb-box-decription">'.esc_html($slide['content_text']).'</div>' : '';
            
            if( $show_button === 'bottom' || $show_button === 'overcontent'){
              $content_box .= $this->get_button( $index, $slide, $settings, false );
            } 

            $content_box .= '</div>';

        }


        if ( ! empty( $image_url ) ) {

            $image_html = sprintf( '<img src="%s" title="%s" alt="%s" />', esc_attr( $image_url ), Control_Media::get_image_title( $thumb_id ), Control_Media::get_image_alt( $thumb_id ) );

          if ( $show_button == '' && ! empty( $slide['link']['url'] ) ) {
            $image_html = sprintf( '<a href="%s">'.$image_html.'</a>', esc_url( $slide['link']['url'] ) );
          }?><div class="blog-featureimage" style="background-image: url('<?php print esc_attr($image_url); ?>');"><?php 
            
            if( $show_button === 'overimage'){
              $this->get_button( $index, $slide, $settings, true );
            } 

            if( $show_content === 'overimage'){
              print  $content_box.$image_html."</div>";
            }else{
              print  $image_html."</div>".$content_box;
            }

        }else{
           print  $content_box;
        }


        if( $show_button === 'yes'){
          $this->get_button( $index, $slide, $settings, true );
        } 

        echo '</div>';

        $rows_html[] = ob_get_clean();
    }

    $make_carousel = (count($rows_html) > $grid_layout ) ? true : false;

    $col_class = $make_carousel ? 'slide-item grid-box grid-col-1' : 'grid-box grid-col-'.absint($grid_layout);

    echo '<div id="mod_'.$widget_id.'" class="owl-carousel-container">';
    echo '<div class="grid-imboxs'.($make_carousel ? ' owl-carousel':'').'"><div class="'.$col_class.'">'.join('</div><div class="'.$col_class.'">',$rows_html).'</div></div>';

   if($make_carousel && $slide_navigation === 'arrow'){
     print $this->get_carousel_navigation($settings);
   }

    echo '</div>';

   if( $make_carousel){
     $this->render_carousel_script($widget_id,$settings);
   }

  }

  protected function get_button( $index, $slide=array(), $settings = array(), $echo = true  ) {

    if(!isset($settings['show_button']) || $settings['show_button'] =='' ) return '';

    $this->add_render_attribute( 'button-'.$index ,
      [
        'class' => ['elementor-button', 'imbox-button' ],
        'role' => 'button'
      ]
    );

    if ( ! empty( $slide['link']['url'] ) ) {
      $this->add_link_attributes( 'button-'.$index, $slide['link'] );
    }
  
    $this->add_render_attribute( 'button-'.$index, 'class', 'elementor-button-link' );

    $this->add_render_attribute( [
      'button_icon_align' => [
        'class' => [
          'elementor-button-icon',
          'elementor-align-icon-' . $slide['button_icon_align'],
        ],
      ],
    ] );

    $this->add_render_attribute( $index , 'class', 'elementor-button-text' );

    ob_start();

    ?><div class="elementor-button-wrap"><a <?php echo $this->get_render_attribute_string( 'button-'.$index ); ?>>
          <span class="elementor-button-content-wrapper">
      <?php if ( ! empty( $slide['selected_icon']['value'] ) ) : ?>
      <span <?php echo $this->get_render_attribute_string( 'button_icon_align' ); ?>>
          <?php Icons_Manager::render_icon( $slide['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
      </span>
      <?php endif; ?>
      <span <?php echo $this->get_render_attribute_string( $index );?>><?php esc_html_e($slide['button_label']); ?></span>
    </span>
  </a></div><?php

    $output = ob_get_clean();

    if( !$echo) return $output;

    echo $output;


  }

  protected function render_carousel_script($widget_id,$settings=array()) {

    if(!isset( $settings ) || empty( $settings )){
      $settings = $this->get_settings_for_display();
    }

    extract( $settings );

    $compile ="<script type=\"text/javascript\">".'jQuery(document).ready(function($) {'.
            '\'use strict\';'.'
            var carousel'.$widget_id.' = $("#mod_'.$widget_id.' .owl-carousel");';

    $compile .= 'try{ carousel'.$widget_id.'.owlCarousel({
                responsiveClass:true,
                responsive : {
                    0 : {items : 1},
                    360 : {items : '.$grid_mobile_layout.'},
                    768 : {items : '.$grid_table_layout.'},
                    1024 : {items : '.$grid_layout.'}
                },
                loop: '.($slide_loop ? 'true':'false').',
                dots  : '.(($slide_navigation=='dot')?"true":"false").',
                nav  : false,
                smartSpeed  : '.absint($slide_speed).',
                rewindSpeed  : '.absint($slide_speed).',
                autoplayTimeout : '.absint($slide_interval).',';

    if($slide_autoplay === 'yes' ) { $compile.= 'autoplay:true,'; }
    if($slide_navigation === 'arrow' ) { 

         $compile.="});\n";
         $compile.='
            var mod'.$widget_id.' = $(\'#mod_'.$widget_id.'\');
            $(\'.owl-custom-pagination .next\',mod'.$widget_id.').click(function(){ 
              carousel'.$widget_id.'.trigger(\'next.owl.carousel\');
            });

            $(\'.owl-custom-pagination .prev\',mod'.$widget_id.').click(function(){
              carousel'.$widget_id.'.trigger(\'prev.owl.carousel\');
            });';

    }else{
      $compile.='});';
    }

    $compile.='}catch(err){}';            
    $compile.='});</script>';

    print "{$compile}";

  }

  protected function get_carousel_navigation( $settings=array() ){

    if(!isset( $settings ) || empty( $settings )){
      $settings = $this->get_settings_for_display();
    }

    extract( $settings );

    $left = esc_html__('Prev','gum-elementor-addon');
    $right = esc_html__('Next','gum-elementor-addon');

    if(!empty($left_icon['value'])){
        ob_start();
        Icons_Manager::render_icon( $left_icon, [ 'aria-hidden' => 'true' ] );
        $left = ob_get_clean();
    }

    if(!empty($right_icon['value'])){
        ob_start();
        Icons_Manager::render_icon( $right_icon, [ 'aria-hidden' => 'true' ] );
        $right = ob_get_clean();
    }


    $output = sprintf('<div class="owl-custom-pagination "><span class="btn-owl prev">%s</span><span class="btn-owl next">%s</span></div>',$left,$right );          

    return $output;
  } 

  protected function content_template() {

  }

  public function enqueue_script( ) {

    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());
    wp_enqueue_style('owl.carousel', GUM_ELEMENTOR_URL . '/css/owl.carousel.css', array());
    wp_enqueue_script('owl.carousel' , GUM_ELEMENTOR_URL . '/js/owl.carousel.min.js' ,array('jquery'), '2.2.1', false );
    wp_enqueue_script( 'gum-elementor-addon', GUM_ELEMENTOR_URL . 'js/allscripts.js', array('jquery'), '1.0', false );
  }


}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Gum_Elementor_Widget_imagebox_carousel() );


class Gum_Elementor_Widget_imagebox extends Widget_Base {

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
    return 'gum_imbox';
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

    return esc_html__( 'imBox', 'gum-elementor-addon' );
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
    return 'far fa-xs fa-image';
  }

  public function get_keywords() {
    return [ 'wordpress', 'widget', 'image', 'box' ];
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
        'label' => esc_html__( 'Content', 'elementor' ),
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
      ]
    );

    $this->add_control(
      'image',
      [
        'label' => esc_html__( 'Image', 'gum-elementor-addon' ),
        'type' => Controls_Manager::MEDIA,
        'label_block' => true,
      ]
    );


    $this->add_group_control(
      Group_Control_Image_Size::get_type(),
      [
        'name' => 'thumbnail', 
        'default' => 'medium',
      ]
    );



    $this->add_control(
      'show_content',
      [
        'label' => esc_html__( 'Show Content', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          '' => esc_html__( 'None', 'gum-elementor-addon' ),
          'overimage' => esc_html__( 'Over Image', 'gum-elementor-addon' ),
          'yes' => esc_html__( 'Normal', 'gum-elementor-addon' ),
        ],
        'default' => 'yes',
        'style_transfer' => true,
        'separator' => 'before'
      ]
    );

    $this->add_control(
      'content_title',
      [
        'label' => esc_html__( 'Title', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'dynamic' => [
          'active' => true,
        ],
        'default' => '',
        'placeholder' => esc_html__( 'Enter your title', 'gum-elementor-addon' ),
        'label_block' => true,
        'condition' => [
          'show_content!' => '',
        ],

      ]
    );


    $this->add_control(
      'title_tag',
      [
        'label' => esc_html__( 'Title Tag', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'h1' => 'H1',
          'h2' => 'H2',
          'h3' => 'H3',
          'h4' => 'H4',
          'h5' => 'H5',
          'h6' => 'H6',
          'div' => 'div',
          'span' => 'span',
          'p' => 'p',
        ],
        'default' => 'h4',
        'condition' => [
          'show_content!' => '',
        ],
      ]
    );

    $this->add_control(
      'content_text',
      [
        'label' => esc_html__( 'Text', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXTAREA,
        'dynamic' => [
          'active' => true,
        ],
        'default' => '',
        'placeholder' => '',
        'rows' => 10,
        'show_label' => false,
        'condition' => [
          'show_content!' => '',
        ],

      ]
    );


    $this->add_control(
      'show_button',
      [
        'label' => esc_html__( 'Show Button', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          '' => esc_html__( 'None', 'gum-elementor-addon' ),
          'yes' => esc_html__( 'Over Box', 'gum-elementor-addon' ),
          'overimage' => esc_html__( 'Over Image', 'gum-elementor-addon' ),
          'overcontent' => esc_html__( 'Over Content Box', 'gum-elementor-addon' ),
          'bottom' => esc_html__( 'On Content Box', 'gum-elementor-addon' ),
        ],
        'default' => 'yes',
        'style_transfer' => true,
        'separator' => 'before'
      ]
    );

    $this->add_control(
      'button_label',
      [
        'label' => esc_html__( 'Button Text', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'default' => esc_html__( 'Button', 'gum-elementor-addon' ),
        'label_block' => true,
        'condition' => [
          'show_button!' => '',
        ],
        'style_transfer' => true,
      ]
    );

    $this->add_control(
      'selected_icon',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
        'condition' => [
          'show_button!' => '',
        ],
      ]
    );

    $this->add_control(
      'button_icon_align',
      [
        'label' => esc_html__( 'Icon Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'left',
        'options' => [
          'left' => esc_html__( 'Before', 'gum-elementor-addon' ),
          'right' => esc_html__( 'After', 'gum-elementor-addon' ),
        ],
        'style_transfer' => true,
        'condition' => [
          'show_button!' => '',
        ],
      ]
    );

    $this->end_controls_section();

/*
 * style params
 */

    $this->start_controls_section(
      'post_grid_image',
      [
        'label' => esc_html__( 'Image Box', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    

    $this->add_responsive_control(
      'post_image_height',
      [
        'label' => esc_html__( 'Height', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 1000,
            'step' => 5,
            'min' => 50,
          ],
          'vh' => [
            'max' => 100,
            'step' => 1,
            'min' => 10,
          ],

        ],  
        'default'=>['size'=>'','unit'=>'px'],
        'size_units' => [ 'px' ,'vh' ],
        'selectors' => [
          '{{WRAPPER}} .imb-box .blog-featureimage' => 'height: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .imb-box .blog-featureimage img' => 'height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'post_image_minheight',
      [
        'label' => esc_html__( 'Min Height', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 1000,
            'step' => 5,
            'min' => 50,
          ],
          'vh' => [
            'max' => 100,
            'step' => 1,
            'min' => 10,
          ],

        ],  
        'default'=>['size'=>'','unit'=>'px'],
        'size_units' => [ 'px' ,'vh' ],
        'selectors' => [
          '{{WRAPPER}} .imb-box .blog-featureimage' => 'min-height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'post_image_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .imb-box .blog-featureimage' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'post_image_border',
        'selector' => '{{WRAPPER}} .imb-box .blog-featureimage',
      ]
    );


    $this->add_control(
      'post_image_bdhover',
      [
        'label' => esc_html__( 'Hover Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .imb-box:hover .blog-featureimage' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'post_image_border_border!' => ''
        ],
      ]
    );


    $this->end_controls_section();

    $this->start_controls_section(
      'content_box_style',
      [
        'label' => esc_html__( 'Content Box', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'show_content!' => ''
        ],
      ]
    );    


    $this->add_control(
      'content_position',
      [
        'label' => esc_html__( 'Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'flex-start' => [
            'title' => esc_html__( 'Top', 'gum-elementor-addon' ),
            'icon' => 'eicon-v-align-top',
          ],
          'center' => [
            'title' => esc_html__( 'Middle', 'gum-elementor-addon' ),
            'icon' => 'eicon-v-align-middle',
          ],
          'flex-end' => [
            'title' => esc_html__( 'Bottom', 'gum-elementor-addon' ),
            'icon' => 'eicon-v-align-bottom',
          ],
        ],
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .imb-box-content' => 'justify-content: {{VALUE}};',
        ],
      ]
    );



    $this->add_responsive_control(
      'content_align',
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
          '{{WRAPPER}} .imb-box-content' => 'text-align: {{VALUE}};',
        ],
      ]
    );


    $this->add_responsive_control(
      'content_height',
      [
        'label' => esc_html__( 'Height', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 1000,
            'step' => 5,
            'min' => 50,
          ],
          'vh' => [
            'max' => 100,
            'step' => 1,
            'min' => 10,
          ],

        ],  
        'default'=>['size'=>'','unit'=>'px'],
        'size_units' => [ 'px' ,'vh' ],
        'selectors' => [
          '{{WRAPPER}} .imb-box .imb-box-content' => 'height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'content_minheight',
      [
        'label' => esc_html__( 'Min Height', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 1000,
            'step' => 5,
            'min' => 50,
          ],
          'vh' => [
            'max' => 100,
            'step' => 1,
            'min' => 10,
          ],

        ],  
        'default'=>['size'=>'','unit'=>'px'],
        'size_units' => [ 'px' ,'vh' ],
        'selectors' => [
          '{{WRAPPER}} .imb-box .imb-box-content' => 'min-height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );


    $this->add_responsive_control(
      'content_box_margin',
      [
          'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', '%', 'em' ],
          'selectors' => [
              '{{WRAPPER}} .imb-box-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );

    $this->add_responsive_control(
      'content_padding',
      [
          'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', '%', 'em' ],
          'selectors' => [
              '{{WRAPPER}} .imb-box-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );

    $this->add_control(
      'content_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .imb-box-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );


    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'content_border',
        'selector' => '{{WRAPPER}} .imb-box-content',
      ]
    );

    $this->start_controls_tabs( 'tabs_content_box_style' );

    $this->start_controls_tab(
      'tab_content_box_normal',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'content_bgcolor',
      [
        'label' => esc_html__( 'Background', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .imb-box-content' => 'background-color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'content_box_opacity',
      [
        'label' => esc_html__( 'Opacity', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          '%' => [
            'min' => 0,
            'max' => 1,
            'step' => 0.01,
          ],
        ],
        'default' =>['value'=>1, 'unit'=>'%'],
        'size_units' => [  '%' ],
        'selectors' => [
          '{{WRAPPER}} .imb-box-content' => 'opacity: {{SIZE}};',
        ],
      ]
    );

    $this->end_controls_tab();

    $this->start_controls_tab(
      'tab_content_box_hover',
      [
        'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'content_box_background_hover_color',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .imb-box:hover .imb-box-content, {{WRAPPER}} .imb-box:focus .imb-box-content' => 'background-color: {{VALUE}};',
        ],
      ]
    );



    $this->add_control(
      'content_box_hover_border_color',
      [
        'label' => esc_html__( 'Border', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'condition' => [
          'content_border_border!' => '',
        ],
        'selectors' => [
          '{{WRAPPER}} .imb-box:hover .imb-box-content, {{WRAPPER}} .imb-box:focus .imb-box-content' => 'border-color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'box_title_hcolor',
      [
        'label' => esc_html__( 'Title Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .imb-box:hover .imb-box-heading,{{WRAPPER}} .imb-box:hover .imb-box-heading' => 'color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'box_content_hcolor',
      [
        'label' => esc_html__( 'Content Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .imb-box:hover .imb-box-decription,{{WRAPPER}} .imb-box:focus .imb-box-decription' => 'color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'content_box_hoveropacity',
      [
        'label' => esc_html__( 'Opacity', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          '%' => [
            'min' => 0,
            'max' => 1,
            'step' => 0.01,
          ],
        ],
        'default' =>['value'=>1, 'unit'=>'%'],
        'size_units' => [  '%' ],
        'selectors' => [
          '{{WRAPPER}} .imb-box:hover .imb-box-content, {{WRAPPER}} .imb-box:focus .imb-box-content' => 'opacity: {{SIZE}};',
        ],
      ]
    );


    $this->add_control(
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
          '{{WRAPPER}} .imb-box-content' => '--e-box-transition-duration: {{SIZE}}ms',
        ],
      ]
    );

    $this->end_controls_tab();
    $this->end_controls_tabs();

    $this->add_control(
      'content_title_heading',
      [
        'label' => esc_html__( 'Title', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_content_title',
        'selector' => '{{WRAPPER}} .imb-box-heading',
      ]
    );

    $this->add_control(
      'content_title_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .imb-box-heading' => 'color: {{VALUE}};',
        ]
      ]
    );



    $this->add_responsive_control(
      'box_title_margin',
      [
          'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', '%', 'em' ],
          'selectors' => [
              '{{WRAPPER}} .imb-box-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );


    $this->add_control(
      'box_content_heading',
      [
        'label' => esc_html__( 'Content', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );


    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_box_content',
        'selector' => '{{WRAPPER}} .imb-box-decription',
      ]
    );

    $this->add_control(
      'box_content_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .imb-box-decription' => 'color: {{VALUE}};',
        ],
      ]
    );


    $this->add_responsive_control(
      'box_content_margin',
      [
        'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%', 'em' ],
        'selectors' => [
            '{{WRAPPER}} .imb-box-decription' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();


    $this->start_controls_section(
      'box_button_style',
      [
        'label' => esc_html__( 'Button', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'show_button!' => ''
        ],
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
        'default' => '',
        'selectors' => [
            '{{WRAPPER}} .elementor-button-wrap' => 'text-align: {{VALUE}};',
        ],
        'condition' => ['show_button' => 'bottom']
      ]
    );

    $this->add_control(
      'button_width',
      [
        'label' => esc_html__( 'Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 1000,
          ],
          '%' => [
            'max' => 100,
          ],
        ],
        'default' =>['value'=>'', 'unit'=>'px'],
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'min-width: {{SIZE}}{{UNIT}};',
        ],
        'condition' => ['show_button!' => ''],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_box_button',
        'selector' => '{{WRAPPER}} .elementor-button',
      ]
    );

    $this->add_responsive_control(
      'box_button_margin',
      [
          'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', '%', 'em' ],
          'allowed_dimensions' => 'vertical',
          'selectors' => [
              '{{WRAPPER}} .elementor-button' => 'margin-top: {{TOP}}{{UNIT}};margin-bottom: {{BOTTOM}}{{UNIT}};',
          ],
      ]
    );


    $this->add_responsive_control(
      'box_button_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ]
      ]
    );
    
    $this->add_control(
      'box_button_radius',
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
      Group_Control_Border::get_type(),
      [
        'name' => 'box_button_border',
        'selector' => '{{WRAPPER}} .elementor-button',
      ]
    );


    $this->start_controls_tabs( 'tabs_box_button_style' );

    $this->start_controls_tab(
      'tab_box_button_normal',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'box_button_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'box_button_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'button_opacity',
      [
        'label' => esc_html__( 'Opacity', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          '%' => [
            'min' => 0,
            'max' => 1,
            'step' => 0.01,
          ],
        ],
        'default' =>['value'=>1, 'unit'=>'%'],
        'size_units' => [  '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'opacity: {{SIZE}};',
        ],
        'condition' => ['show_button!' => ''],
      ]
    );

    $this->end_controls_tab();

    $this->start_controls_tab(
      'tab_box_button_hover',
      [
        'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'box_button_hover_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}}!important;',
          '{{WRAPPER}} .elementor-button:hover svg, {{WRAPPER}} .elementor-button:focus svg' => 'fill: {{VALUE}}!important;',
        ],
      ]
    );

    $this->add_control(
      'box_button_background_hover_color',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'box_button_hover_border_color',
      [
        'label' => esc_html__( 'Border', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'condition' => [
          'box_button_border_border!' => '',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'button_hoveropacity',
      [
        'label' => esc_html__( 'Opacity', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          '%' => [
            'min' => 0,
            'max' => 1,
            'step' => 0.01,
          ],
        ],
        'default' =>['value'=>1, 'unit'=>'%'],
        'size_units' => [  '%' ],
        'selectors' => [
          '{{WRAPPER}}:hover .elementor-button, {{WRAPPER}}:focus .elementor-button' => 'opacity: {{SIZE}};',
        ],
        'condition' => ['show_button!' => ''],
      ]
    );
    $this->end_controls_tab();
    $this->end_controls_tabs();


    $this->add_control(
      'button_icon_heading',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
        'condition' => ['show_button!' => '','selected_icon[value]!'=>''],
      ]
    );


    $this->add_control(
      'button_icon_indent',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
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
        'condition' => ['show_button!' => '','button_label!' => '','selected_icon[value]!'=>''],
      ]
    );

    $this->add_control(
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
          '{{WRAPPER}} .elementor-button .elementor-button-icon' => 'font-size: {{SIZE}}{{UNIT}};',
        ],
        'condition' => ['show_button!' => '','selected_icon[value]!' => ''],
      ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'button_icon_border',
        'selector' => '{{WRAPPER}} .elementor-button .elementor-button-icon',
        'condition' => ['show_button!' => '','selected_icon[value]!' => ''],
      ]
    );

    $this->add_responsive_control(
        'button_icon_padding',
        [
            'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} .elementor-button .elementor-button-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => ['show_button!' => '','selected_icon[value]!' => '','button_icon_border_border!'=>''],
        ]
    );

    $this->add_control(
      'button_icon_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button .elementor-button-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => ['show_button!' => '','selected_icon[value]!' => '','button_icon_border_border!'=>''],
      ]
    );

    $this->start_controls_tabs( '_tabs_button_icon_style' );

    $this->start_controls_tab(
      '_tab_button_icon_normal',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );
        

    $this->add_control(
      'button_icon_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button .elementor-button-icon i,{{WRAPPER}} .elementor-button .elementor-button-icon svg' => 'color: {{VALUE}}!important,fill: {{VALUE}}!important;',
        ],
        'condition' => ['show_button!' => '','selected_icon[value]!' => ''],
      ]
    );

    $this->add_control(
      'button_icon_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-button .elementor-button-icon' => 'background: {{VALUE}};',
        ],
        'condition' => ['show_button!' => '','button_icon_border_border!' => ''],
      ]
    );

    $this->add_control(
      'button_icon_rotate',
      [
        'label' => esc_html__( 'Rotate', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'deg' ],
        'default' => [
          'size' => 0,
          'unit' => 'deg',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button .elementor-button-icon i,{{WRAPPER}} .elementor-button .elementor-button-icon svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
        ],
        'condition' => ['show_button!' => '','selected_icon[value]!' => ''],

      ]
    );

    $this->end_controls_tab();
    $this->start_controls_tab(
      '_tab_button_icon_hover',
      [
        'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'button_icon_hover_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover .elementor-button-icon i,{{WRAPPER}} .elementor-button:hover .elementor-button-icon svg' => 'color: {{VALUE}}!important,fill: {{VALUE}}!important;',
        ],
        'condition' => ['show_button!' => '','selected_icon[value]!' => ''],
      ]
    );

    $this->add_control(
      'button_hover_icon_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover .elementor-button-icon' => 'background: {{VALUE}};',
        ],
        'condition' => ['show_button!' => '','button_icon_border_border!' => ''],
      ]
    );

    $this->add_control(
      'button_icon_border_hover_color',
      [
        'label' => esc_html__( 'Border', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover .elementor-button-icon' => 'border-color: {{VALUE}}!important;',
        ],
        'condition' => ['show_button!' => '','selected_icon[value]!' => '','button_icon_border_border!'=>''],
      ]
    );


    $this->add_control(
      'button_icon_hover_rotate',
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

    $this->add_control(
      'button_icon_transform_transition_hover',
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

    $this->end_controls_tab();
    $this->end_controls_tabs();


    $this->end_controls_section();

  }

  protected function render() {

    $settings = $this->get_settings_for_display();

    extract( $settings );

      $content_box = '';

      echo '<div class="imb-box button-style'.sanitize_html_class($show_button).'">';

          $image_url = Group_Control_Image_Size::get_attachment_image_src( $image['id'], 'thumbnail', $settings);

          if($show_content!=''){

              $tag = Utils::validate_html_tag( $title_tag );

              $content_box = '<div class="imb-box-content">';
              $content_box .=  $content_title!=''? '<'.$tag.' class="imb-box-heading">'.( $show_button == '' && ! empty( $link['url'] )? sprintf( '<a href="%s">'.esc_html($content_title).'</a>', esc_url( $link['url'] ) ) : esc_html($content_title) ).'</'.$tag.'>' : '';
              $content_box .=  $content_text !=''? '<div class="imb-box-decription">'.esc_html($content_text).'</div>' : '';
              
              if( $show_button === 'bottom' || $show_button === 'overcontent'){
                $content_box .= $this->get_button( $settings, false );
              } 

              $content_box .= '</div>';

          }


        if ( ! empty( $image_url ) ) {

            $image_html = sprintf( '<img src="%s" title="%s" alt="%s" />', esc_attr( $image_url ), Control_Media::get_image_title( $image['id'] ), Control_Media::get_image_alt( $image['id'] ) );

          if ( $show_button == '' && ! empty( $link['url'] ) ) {
            $image_html = sprintf( '<a href="%s">'.$image_html.'</a>', esc_url( $link['url'] ) );
          }?><div class="blog-featureimage" style="background-image: url('<?php print esc_attr( $image_url ); ?>');"><?php 
            
            if( $show_button === 'overimage'){
              $this->get_button( $settings, true );
            } 

            if( $show_content === 'overimage'){
              print  $content_box.$image_html."</div>";
            }else{
              print  $image_html."</div>".$content_box;
            }

        }else{
           print  $content_box;
        }


        if( $show_button === 'yes'){
          $this->get_button( $settings, true );
        } 

    echo '</div>';

  }

  protected function get_button( $settings = array(), $echo = true ) {

    if(!isset($settings['show_button']) || $settings['show_button'] =='' ) return '';

    $this->add_render_attribute( 'box-button' ,
      [
        'class' => ['elementor-button', 'imbox-button' ],
        'role' => 'button'
      ]
    );

    if ( ! empty( $settings['link']['url'] ) ) {
      $this->add_link_attributes( 'box-button', $settings['link'] );
    }
  
    $this->add_render_attribute( 'box-button', 'class', 'elementor-button-link' );

    $this->add_render_attribute( [
      'button_icon_align' => [
        'class' => [
          'elementor-button-icon',
          'elementor-align-icon-' . $settings['button_icon_align'],
        ],
      ],
    ] );

    $this->add_render_attribute( 'button_label' , 'class', 'elementor-button-text' );

    ob_start();

    ?><div class="elementor-button-wrap"><a <?php echo $this->get_render_attribute_string( 'box-button' ); ?>>
          <span class="elementor-button-content-wrapper">
      <?php if ( ! empty( $settings['selected_icon']['value'] ) ) : ?>
      <span <?php echo $this->get_render_attribute_string( 'button_icon_align' ); ?>>
          <?php Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
      </span>
      <?php endif; ?>
      <span <?php echo $this->get_render_attribute_string( 'button_label' );?>><?php esc_html_e( $settings['button_label']); ?></span>
    </span>
  </a></div><?php

    $output = ob_get_clean();

    if( !$echo) return $output;

    echo $output;

  }


  protected function content_template() {

  }

  public function enqueue_script( ) {

    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());
  }


}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Gum_Elementor_Widget_imagebox() );

?>