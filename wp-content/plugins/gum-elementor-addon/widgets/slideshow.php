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
use Elementor\Icons_Manager;
use Elementor\Repeater;

class Elementor_Petro_Slides_Widget extends Widget_Base {

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
    return 'gum_slide';
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

    return esc_html__( 'Slideshow', 'gum-elementor-addon' );
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
    return 'eicon-slideshow fa-xs';
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
      'section_content',
      [
        'label' => esc_html__( 'Content', 'gum-elementor-addon' ),
      ]
    );

    $repeater = new Repeater();

    $repeater->add_control(
      'image',
      [
        'label' => esc_html__( 'Background Image', 'gum-elementor-addon' ),
        'type' => Controls_Manager::MEDIA,
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'slide_title',
      [
        'label' => esc_html__( 'Heading', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'default' => esc_html__( 'Slide Title Text', 'gum-elementor-addon' ),
        'description' => esc_html__( 'Title text can contain allowed with tag: strong, b.', 'gum-elementor-addon' ),
        'label_block' => true,
        'ai' => [
          'active' => false,
        ],
      ]
    );

    $repeater->add_control(
      'slide_subtitle',
      [
        'label' => esc_html__( 'Sub Heading', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
        'ai' => [
          'active' => false,
        ],
      ]
    );

    $repeater->add_control(
      'slide_content',
      [
        'label' => esc_html__( 'Content', 'gum-elementor-addon' ),
        'type' => Controls_Manager::WYSIWYG,
        'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gum-elementor-addon' ),
        'label_block' => true,
      ]
    );


    $repeater->add_control(
      'button_title',
      [
        'label' => esc_html__( 'Button', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );


    $repeater->start_controls_tabs( 'slide_button' );

    $repeater->start_controls_tab(
      'button_left',
      [
        'label' => esc_html__( 'Left', 'gum-elementor-addon' ),
      ]
    );

    $repeater->add_control(
      'button_label',
      [
        'label' => esc_html__( 'Button Text', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'default' => esc_html__( 'Left Button', 'gum-elementor-addon' ),
        'label_block' => true,
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
        ],
      ]
    );

    $repeater->add_control(
      'button_link',
      [
        'label' => esc_html__( 'Link', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'ai' => [
          'active' => false,
        ],
        'label_block' => true,
      ]
    );

    $repeater->end_controls_tab();
    $repeater->start_controls_tab(
      'button_right',
      [
        'label' => esc_html__( 'Right', 'gum-elementor-addon' ),
      ]
    );


    $repeater->add_control(
      'button_r_label',
      [
        'label' => esc_html__( 'Button Text', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'default' => esc_html__( 'Right Button', 'gum-elementor-addon' ),
        'label_block' => true,
        'ai' => [
          'active' => false,
        ],
      ]
    );


    $repeater->add_control(
      'selected_r_icon',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
      ]
    );

    $repeater->add_control(
      'icon_r_align',
      [
        'label' => esc_html__( 'Icon Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'left',
        'options' => [
          'left' => esc_html__( 'Before', 'gum-elementor-addon' ),
          'right' => esc_html__( 'After', 'gum-elementor-addon' ),
        ],
        'condition' => [
          'selected_r_icon[value]!' => '',
        ],
      ]
    );

    $repeater->add_control(
      'button_r_link',
      [
        'label' => esc_html__( 'Link', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'label_block' => true,
        'ai' => [
          'active' => false,
        ],
      ]
    );



    $repeater->end_controls_tab();
    $repeater->end_controls_tabs();

    $repeater->add_control(
      'slide_align',
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
        'default' => '',
      ]
    );


    $repeater->add_responsive_control(
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
              ]
        ],
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} {{CURRENT_ITEM}} .wrap-caption' => 'text-align: {{VALUE}};',
        ],
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
            'slide_title' => esc_html__( 'Slide #1', 'gum-elementor-addon' ),
            'button_label' => esc_html__( 'Left Button', 'gum-elementor-addon' ),
            'button_link' => '#',
            'button_r_label' => esc_html__( 'Right Button', 'gum-elementor-addon' ),
            'button_r_link' => '',
            'slide_align' => '',
            'slide_content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gum-elementor-addon' ),
          ],
          [
            'slide_title' => esc_html__( 'Slide #2', 'gum-elementor-addon' ),
            'button_label' => esc_html__( 'Left Button', 'gum-elementor-addon' ),
            'button_link' => '#',
            'button_r_label' => esc_html__( 'Right Button', 'gum-elementor-addon' ),
            'button_r_link' => '',
            'slide_align' => '',
            'slide_content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gum-elementor-addon' ),
          ],
          [
            'slide_title' => esc_html__( 'Slide #3', 'gum-elementor-addon' ),
            'button_label' => esc_html__( 'Left Button', 'gum-elementor-addon' ),
            'button_link' => '',
            'button_r_label' => esc_html__( 'Right Button', 'gum-elementor-addon' ),
            'button_r_link' => '#',
            'slide_align' => '',
            'slide_content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gum-elementor-addon' ),
          ],
        ],
        'title_field' => '{{{ slide_title }}}'
      ]
    );


    $this->add_control(
      'slide_layout',
      [
        'label' => esc_html__( 'Content Layout', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
              '1' => esc_html__( 'Heading - Text - Sub Heading - Button', 'gum-elementor-addon' ),
              '2' => esc_html__( 'Heading - Sub Heading - Text - Button', 'gum-elementor-addon' ),
              '3' => esc_html__( 'Heading - Sub Heading - Button - Text', 'gum-elementor-addon' )
        ],
        'default' => '1'
      ]
    );


  $this->end_controls_section();


  $this->start_controls_section(
      'section_slideshow',
      [
        'label' => esc_html__( 'Settings', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'animation_title',
      [
        'label' => esc_html__( 'Animation', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
      ]
    );

    $this->add_control(
      'image_animation',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
              'slide' => esc_html__('Sliding','gum-elementor-addon'),
              'fade' => esc_html__('Fade','gum-elementor-addon'),
         ],
         'default'=> 'slide',
      ]
    );

    $this->add_control(
      'slide_animation',
      [
        'label' => esc_html__( 'Content', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
              'none' => esc_html__('None','gum-elementor-addon'),
              'fromTop' => esc_html__('From Top','gum-elementor-addon'),
              'fromBottom' => esc_html__('From Bottom','gum-elementor-addon'),
              'scale' => esc_html__('Scale','gum-elementor-addon'),
              'fade' => esc_html__('Fade','gum-elementor-addon'),
              'fadeScale' => esc_html__('Scale and Fade','gum-elementor-addon'),
         ],
         'default'=> 'none',
      ]
    );

    $this->add_control(
      'easing',
      [
        'label' => esc_html__( 'Easing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
              'linear' => esc_html__('Linear','gum-elementor-addon'),
              'swing' => esc_html__('Swing','gum-elementor-addon'),
         ],
         'default'=>'linear'
      ]
    );


    $this->add_control(
      'autoplay',
      [
        'label' => esc_html__( 'Autoplay', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'default' => 'yes',
      ]
    );

    $this->add_control(
      'interval',
      [
        'label' => esc_html__( 'Slide Interval', 'gum-elementor-addon' ),
        'type' => Controls_Manager::NUMBER,
        'min' => 100,
        'max' => 10000,
        'step' => 100,
        'default'=> 5000,
        'condition' => [
          'autoplay[value]' => 'yes',
        ],
      ]
    );


    $this->add_control(
      'slide_speed',
      [
        'label' => esc_html__( 'Slide Speed', 'gum-elementor-addon' ),
        'type' => Controls_Manager::NUMBER,
        'min' => 100,
        'max' => 10000,
        'step' => 100,
        'default'=> 800,
      ]
    );


    $this->add_control(
      'slider_size_title',
      [
        'label' => esc_html__( 'Dimension', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'slider_width',
      [
        'label' => esc_html__( 'Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
              'window' => esc_html__('Fullscreen','gum-elementor-addon'),
              'custom' => esc_html__('Custom','gum-elementor-addon'),
         ],
        'default' => 'custom',
      ]
    );

    $this->add_responsive_control(
      'slider_width_custom',
      [
        'label' => esc_html__( 'Custom Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          '%' => [
            'min' => 1,
            'max' => 100,
            'step' => 1,
          ],
          'vw' => [
            'min' => 1,
            'max' => 100,
          ]
        ],  
        'default'=>['size'=>100,'unit'=>'%'],
        'size_units' => [ '%', 'vw' ],
        'selectors' => [
          '{{WRAPPER}} .gum-superslide-helper,{{WRAPPER}} .gum-superslide' => 'width: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'slider_width[value]' => 'custom',
        ],
      ]
    );


    $this->add_control(
      'slider_height',
      [
        'label' => esc_html__( 'Height', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
              'window' => esc_html__('Fullscreen','gum-elementor-addon'),
              'custom' => esc_html__('Custom','gum-elementor-addon'),
         ],
        'default' => 'window',
      ]
    );

    $this->add_responsive_control(
      'slider_height_custom',
      [
        'label' => esc_html__( 'Custom Height', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 1,
            'max' => 10000,
            'step' => 1,
          ],
          'vh' => [
            'min' => 1,
            'max' => 100,
          ]
        ],  
        'default'=>['size'=>100,'unit'=>'vh'],
        'size_units' => [ 'px', 'vh' ],
        'selectors' => [
          '{{WRAPPER}} .gum-superslide-helper,{{WRAPPER}} .gum-superslide' => 'height: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'slider_height[value]' => 'custom',
        ],
      ]
    );


    $this->add_control(
      'navigation',
      [
        'label' => esc_html__( 'Navigation', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => 'no',
        'separator' => 'before'
      ]
    );

    $this->add_control(
      'navigation_left_icon',
      [
        'label' => esc_html__( 'Left Arrow Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
        'condition' => [
          'navigation[value]' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'navigation_right_icon',
      [
        'label' => esc_html__( 'Right Arrow Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
        'condition' => [
          'navigation[value]' => 'yes',
        ],
      ]
    );



    $this->add_control(
      'pagination',
      [
        'label' => esc_html__( 'Pagination', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => 'no',
        'separator' => 'before'
      ]
    );


    $this->end_controls_section();

/*
 * style params
 */
    $this->start_controls_section(
      'section_style_box',
      [
        'label' => esc_html__( 'Slide Item', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    


    $this->add_control(
      'content_layout',
      [
        'label' => esc_html__( 'Content Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
              'boxed' => esc_html__('Boxed','gum-elementor-addon'),
              'full_width' => esc_html__('Fullwidth','gum-elementor-addon'),
         ],
        'default' => 'boxed',
        'prefix_class' => 'slide-layout-',
      ]
    );


    $this->add_control(
      'background_ovl',
      [
        'label' => esc_html__( 'Background Overlay', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .gum-superslide .overlay-bg' => 'background-color: {{VALUE}};',
        ],
      ]
    );


    $this->add_responsive_control(
      'slide_container',
      [
        'label' => esc_html__( 'Content width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          '%' => [
            'min' => 40,
            'max' => 100,
            'step' => 1,
          ],
          'vw' => [
            'min' => 40,
            'max' => 100,
          ],
          'px' => [
            'min' => 200,
            'max' => 2000,
          ],

        ],  
        'default'=>['size'=>50,'unit'=>'%'],
        'size_units' => [ 'px', '%', 'vw' ],
        'selectors' => [
          '{{WRAPPER}} .gum-superslide .wrap-caption' => 'max-width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );


    $this->add_responsive_control(
      'slide_container_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .gum-superslide .wrap-caption' => 'padding-top: {{TOP}}{{UNIT}};padding-left: {{LEFT}}{{UNIT}};padding-bottom: {{BOTTOM}}{{UNIT}};padding-right: {{RIGHT}}{{UNIT}};',
        ],
      ]
    );


    $this->add_control(
      'heading_description',
      [
        'label' => esc_html__( 'Heading', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_heading',
        'selector' => '{{WRAPPER}} .gum-superslide .caption-heading',
      ]
    );

    $this->add_control(
      'heading_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .gum-superslide .caption-heading' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'heading_tag_color',
      [
        'label' => esc_html__( 'Tag Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'description' => esc_html__( 'Color will applied to i, b, strong tag inside the title text.', 'gum-elementor-addon' ),
        'selectors' => [
          '{{WRAPPER}} .gum-superslide .caption-heading strong, {{WRAPPER}} .gum-superslide .caption-heading b, {{WRAPPER}} .gum-superslide .caption-heading i' => 'color: {{VALUE}};',
        ]
      ]
    );


    $typo_weight_options = [
      '' => esc_html__( 'Default', 'gum-elementor-addon' ),
    ];

    foreach ( array_merge( [ 'normal', 'bold' ], range( 100, 900, 100 ) ) as $weight ) {
      $typo_weight_options[ $weight ] = ucfirst( $weight );
    }

    $this->add_responsive_control(
      'heading_tag_font_weight',
      [
      'label' => esc_html__( 'Tag Font Weight', 'gum-elementor-addon' ),
      'type' => Controls_Manager::SELECT,
      'default' => '',
      'options' => $typo_weight_options,
        'selectors' => [
          '{{WRAPPER}} .gum-superslide .caption-heading strong, {{WRAPPER}} .gum-superslide .caption-heading b, {{WRAPPER}} .gum-superslide .caption-heading i' => 'font-weight: {{VALUE}};',
        ]
      ]
    );


    $this->add_responsive_control(
      'heading_spacing',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -200,
            'max' => 200,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .gum-superslide .caption-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
      ]
    );


    $this->add_control(
      'subheading_description',
      [
        'label' => esc_html__( 'Sub Heading', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_subheading',
        'selector' => '{{WRAPPER}} .gum-superslide .caption-subheading',
      ]
    );

    $this->add_control(
      'subheading_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .gum-superslide .caption-subheading' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_responsive_control(
      'subheading_spacing',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -200,
            'max' => 200,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .gum-superslide .caption-subheading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
      ]
    );


    $this->add_control(
      'title_content',
      [
        'label' => esc_html__( 'Content', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );


    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_content',
        'selector' => '{{WRAPPER}} .gum-superslide .excerpt',
      ]
    );

    $this->add_control(
      'content_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .gum-superslide .excerpt' => 'color: {{VALUE}};',
        ],
      ]
    );


    $this->add_responsive_control(
      'content_spacing',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -200,
            'max' => 200,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .gum-superslide .excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
      ]
    );


    $this->add_control(
      'title_button',
      [
        'label' => esc_html__( 'Button', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );


    $this->add_responsive_control(
      'button_margin',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -200,
            'max' => 200,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .gum-superslide .elementor-button + .excerpt' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );


    $this->end_controls_section();

    $this->start_controls_section(
      'button_title',
      [
        'label' => esc_html__( 'Button', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_button',
        'selector' => '{{WRAPPER}} .elementor-button',
      ]
    );

    $this->add_responsive_control(
      'button_size',
      [
        'label' => esc_html__( 'Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', '%' ],
        'range' => [
          'px' => [
            'max' => 1000,
          ],
          '%' => [
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'button_spacing',
      [
        'label' => esc_html__( 'Horizontal Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px' ],
        'range' => [
          'px' => [
            'max' => 1000,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button + .elementor-button' => 'margin-left: {{SIZE}}{{UNIT}};',
        ],
        'description' => esc_html__( 'Spacing between button.', 'gum-elementor-addon' ),
      ]
    );


    $this->add_responsive_control(
      'button_vertical_spacing',
      [
        'label' => esc_html__( 'Vertical Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px' ],
        'range' => [
          'px' => [
            'max' => 200,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
        'conditions' => [
          'relation' => 'and',
          'terms' => [
            [
              'name' => 'button_size[unit]',
              'operator' => '==',
              'value' => '%',
            ],
            [
              'name' => 'button_size[size]',
              'operator' => '>',
              'value' => '50',
            ],
          ],
        ],


        'description' => esc_html__( 'Spacing between button.', 'gum-elementor-addon' ),
      ]
    );

    $this->add_responsive_control(
      'button_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'allowed_dimensions' => 'vertical',
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'padding-top: {{TOP}}{{UNIT}};padding-bottom: {{BOTTOM}}{{UNIT}};',
        ],
      ]
    );


    $this->add_control(
      'button_border_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'separator' => 'after',
      ]
    );




    $this->start_controls_tabs( 'left_button_style' );

    $this->start_controls_tab(
      'button_left_style',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'left_button_title',
      [
        'label' => esc_html__( 'Primary', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'after',
      ]
    );



    $this->add_control(
      'button_left_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button.primary-button' => 'color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'button_left_bgcolor',
      [
        'label' => esc_html__( 'Backgound', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button.primary-button' => 'background-color: {{VALUE}};',
        ],
      ]
    );


    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'primary_button_border',
        'selector' => '{{WRAPPER}} .elementor-button.primary-button',
      ]
    );


    $this->add_responsive_control(
      'primary_icon_spacing',
      [
        'label' => esc_html__( 'Icon Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px' ],
        'range' => [
          'px' => [
            'max' => 1000,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button.primary-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .elementor-button.primary-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .elementor-button.primary-button .elementor-button-text' => '-webkit-box-flex: 0;flex-grow: 0;',
        ],
      ]
    );

    $this->add_control(
      'right_button_title',
      [
        'label' => esc_html__( 'Secondary', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );



    $this->add_control(
      'button_right_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button.secondary-button' => 'color: {{VALUE}};',
        ],
        'separator' => 'before',
      ]
    );


    $this->add_control(
      'button_right_bgcolor',
      [
        'label' => esc_html__( 'Backgound', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button.secondary-button' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'secondary_button_border',
        'selector' => '{{WRAPPER}} .elementor-button.secondary-button',
      ]
    );



    $this->add_responsive_control(
      'secondary_icon_spacing',
      [
        'label' => esc_html__( 'Icon Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px' ],
        'range' => [
          'px' => [
            'max' => 1000,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button.secondary-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .elementor-button.secondary-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .elementor-button.secondary-button .elementor-button-text' => '-webkit-box-flex: 0;flex-grow: 0;',
        ],
      ]
    );




    $this->end_controls_tab();
    $this->start_controls_tab(
      'button_left_hover_style',
      [
        'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'left_button_hvr_title',
      [
        'label' => esc_html__( 'Primary', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'after',
      ]
    );

    $this->add_control(
      'button_left_hover_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button.primary-button:hover' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'button_left_hover_bgcolor',
      [
        'label' => esc_html__( 'Backgound', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button.primary-button:hover' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'button_primary_hover_border_color',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'condition' => [
          'primary_button_border_border!' => '',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button.primary-button:hover, {{WRAPPER}} .elementor-button.secondary-button:focus' => 'border-color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'right_button_hrv_title',
      [
        'label' => esc_html__( 'Secondary', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );


    $this->add_control(
      'button_right_hover_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button.secondary-button:hover' => 'color: {{VALUE}};',
        ],
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'button_right_hover_bgcolor',
      [
        'label' => esc_html__( 'Backgound', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button.secondary-button:hover' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'button_secondary_hover_border_color',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'condition' => [
          'secondary_button_border_border!' => '',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button.secondary-button:hover, {{WRAPPER}} .elementor-button.secondary-button:focus' => 'border-color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_tab();
    $this->end_controls_tabs();


    $this->end_controls_section(['condition' => ['navigation[value]' => 'yes']]);
    $this->start_controls_section(
      'section_navigation_styles',
      [
        'label' => esc_html__( 'Navigation', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'navigation[value]' => 'yes'
        ],
      ]
    );

    $this->add_control(
      'container_width',
      [
        'label' => esc_html__( 'Boxed', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => 'no',
        'condition' => [
          'navigation[value]' => 'yes',
        ],
      ]
    );

    $this->add_responsive_control(
      'navigation_offset',
      [
        'label' => esc_html__( 'Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px' ],
        'range' => [
          'px' => [
            'max' => 1000,
          ],
        ],
        'default' => [
          'size' => 60,
        ],
        'selectors' => [
          '{{WRAPPER}} .slides-navigation .container a.prev' => 'left: -{{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .slides-navigation .container a.next' => 'right: -{{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'navigation_size',
      [
        'label' => esc_html__( 'Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px' ],
        'range' => [
          'px' => [
            'max' => 1000,
          ],
        ],
        'default' => [
          'size' => 16,
        ],
        'selectors' => [
          '{{WRAPPER}} .slides-navigation a' => 'font-size: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'navigation[value]' => 'yes'
        ],
      ]
    );


    $this->add_responsive_control(
      'navigation_width',
      [
        'label' => esc_html__( 'Box Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px' ],
        'range' => [
          'px' => [
            'max' => 1000,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .slides-navigation a' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
        ],
        'default' => [
          'size' => 40,
        ],
        'condition' => [
          'navigation_size!' => ''
        ],
      ]
    );

    $this->add_responsive_control(
      'navigation_radius',
      [
        'label' => esc_html__( 'Left Arrow Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .slides-navigation a.prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => [
          'navigation[value]' => 'yes',
        ],
      ]
    );

    $this->add_responsive_control(
      'navigation_right_radius',
      [
        'label' => esc_html__( 'Right Arrow Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .slides-navigation a.next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => [
          'navigation[value]' => 'yes',
        ],
      ]
    );


    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'navigation_border',
        'selector' => '{{WRAPPER}} .slides-navigation a',
        'condition' => [
          'navigation[value]' => 'yes'
        ],
      ]
    );

    $this->start_controls_tabs( 
      'navigation_colors',
      [
        'condition' => [
          'navigation[value]' => 'yes',
        ],
      ]
    );

    $this->start_controls_tab(
      'navigation_colors_normal',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
        'condition' => [
          'navigation[value]' => 'yes',
        ],
      ]
    );


    $this->add_control(
      'navigation_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .slides-navigation a' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'navigation[value]' => 'yes',
        ],

      ]
    );


    $this->add_control(
      'navigation_bgcolor',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .slides-navigation a' => 'background-color: {{VALUE}};',
        ],
        'condition' => [
          'navigation[value]' => 'yes',
        ],

      ]
    );

    $this->end_controls_tab();


    $this->start_controls_tab(
      'navigation_colors_hover',
      [
        'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
        'condition' => [
          'navigation[value]' => 'yes',
        ],
      ]
    );


    $this->add_control(
      'navigation_color_hover',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .slides-navigation a:hover,{{WRAPPER}} .slides-navigation a:focus' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'navigation[value]' => 'yes',
        ],

      ]
    );


    $this->add_control(
      'navigation_bgcolor_hover',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .slides-navigation a:hover,{{WRAPPER}} .slides-navigation a:focus' => 'background-color: {{VALUE}};',
        ],
        'condition' => [
          'navigation[value]' => 'yes',
        ],

      ]
    );

    $this->add_control(
      'navigation_bordercolor_hover',
      [
        'label' => esc_html__( 'Border', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .slides-navigation a:hover,{{WRAPPER}} .slides-navigation a:focus' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'navigation[value]' => 'yes',
          'navigation_border_border!' => '',
        ],

      ]
    );

    $this->end_controls_tab();
    $this->end_controls_tabs();


    $this->end_controls_section(['condition' => ['pagination[value]' => 'yes']]);
    $this->start_controls_section(
      'section_pagination_styles',
      [
        'label' => esc_html__( 'Pagination', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'pagination[value]' => 'yes'
        ],
      ]
    );


    $this->add_responsive_control(
      'pagination_align',
      [
        'label' => esc_html__( 'X Position', 'gum-elementor-addon' ),
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
        'default' => '',
        'condition' => [
          'pagination[value]' => 'yes'
        ],
        'selectors' => [
          '{{WRAPPER}} .slides-pagination' => 'text-align: {{VALUE}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'pagination_offset',
      [
        'label' => esc_html__( 'Y Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px' , '%'],
        'range' => [
          'px' => [
            'max' => 1000,
          ],
          '%' => [
            'max' => 100,
          ],
        ],
        'default' => [
          'px' => [ 'size' => 25 ]
        ],
        'selectors' => [
          '{{WRAPPER}} .slides-pagination' => 'bottom: {{SIZE}}{{UNIT}};',
        ],
      ]
    );


    $this->add_responsive_control(
      'pagination_height',
      [
        'label' => esc_html__( 'Height', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px' ],
        'range' => [
          'px' => [
            'max' => 1000,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .slides-pagination a' => 'height: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'pagination[value]' => 'yes'
        ],
      ]
    );

    $this->add_responsive_control(
      'pagination_width',
      [
        'label' => esc_html__( 'Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px','%' ],
        'range' => [
          'px' => [
            'max' => 1000,
          ],
          '%' => [
            'min' => 1,
            'max' => 100,
            'step' => 1,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .slides-pagination a' => 'width: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'pagination[value]' => 'yes'
        ],
      ]
    );

    $this->add_responsive_control(
      'pagination_spacing',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px' ],
        'range' => [
          'px' => [
            'max' => 1000,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .slides-pagination a' => 'margin-left: calc({{SIZE}}{{UNIT}}/2);margin-right: calc({{SIZE}}{{UNIT}}/2);',
        ],
        'description' => esc_html__( 'Spacing between thumbnail.', 'gum-elementor-addon' ),
        'condition' => [
          'pagination[value]' => 'yes'
        ],
      ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'pagination_border',
        'selector' => '{{WRAPPER}} .slides-pagination a',
        'condition' => [
          'pagination[value]' => 'yes'
        ],
      ]
    );

    $this->add_responsive_control(
      'pagination_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .slides-pagination a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => [
          'pagination[value]' => 'yes',
        ],
      ]
    );

    $this->start_controls_tabs( 
      'pagination_colors',
      [
        'condition' => [
          'pagination[value]' => 'yes',
        ],
      ]
    );

    $this->start_controls_tab(
      'pagination_colors_normal',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
        'condition' => [
          'pagination[value]' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'pagination_bgcolor',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .slides-pagination a' => 'background-color: {{VALUE}};',
        ],
        'condition' => [
          'pagination[value]' => 'yes',
        ],

      ]
    );

    $this->end_controls_tab();


    $this->start_controls_tab(
      'pagination_colors_hover',
      [
        'label' => esc_html__( 'Hover & Current', 'gum-elementor-addon' ),
        'condition' => [
          'pagination[value]' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'pagination_bgcolor_hover',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .slides-pagination a:hover,{{WRAPPER}} .slides-pagination a:focus, {{WRAPPER}} .slides-pagination a.current' => 'background-color: {{VALUE}};',
        ],
        'condition' => [
          'pagination[value]' => 'yes',
        ],

      ]
    );

    $this->add_control(
      'pagination_bordercolor_hover',
      [
        'label' => esc_html__( 'Border', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .slides-pagination a:hover,{{WRAPPER}} .slides-pagination a:focus, {{WRAPPER}} .slides-pagination a.current' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'pagination[value]' => 'yes',
          'pagination_border_border!' => '',
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

    $play = ($autoplay=='yes') ? absint($interval) : 'false';
    $slide_speed = ($slide_speed) ? $slide_speed : 800;

    $widgetID = "mod_". substr( $this->get_id_int(), 0, 4 );

    $slide_layout = ($slide_layout) ? absint($slide_layout): 1;

    $layouter = $this->get_slide_layout($slide_layout);

  ?>
<div id="<?php esc_attr_e($widgetID);?>-helper" class="gum-superslide-helper">
<div id="<?php esc_attr_e($widgetID);?>" class="gum-superslide" dir="ltr">
    <ul class="slides-container">
    <?php 

  foreach ($slides as $index => $slide) {

  $media = $slide['image'];
  $title = $slide['slide_title'];
  $content = $slide['slide_content'];
  $slide_subtitle = $slide['slide_subtitle'];

  $align = $slide['slide_align'];
  $image_id = $media['id'];

  $bg_image=Gum_Elementor_Addon::get_image_size($image_id,'full');

?>
<li class="elementor-repeater-item-<?php esc_attr_e($slide['_id']); ?>">
<?php 

    if($bg_image){

     $alt_image = get_post_meta($image_id, '_wp_attachment_image_alt', true);
      print '<img src="'.esc_url($bg_image[0]).'" alt="'.esc_attr($alt_image).'" />';

    }
?>
<div class="overlay-bg"></div>
<div class="container">
    <div class="wrap-caption <?php print sanitize_html_class($align);?> <?php if($slide_animation !='') { print 'animated-'.sanitize_html_class($slide_animation);}?>">
      <?php 

      $slide_html = $layouter;


      if($title!='') { 

        $slide_title_key = $this->get_repeater_setting_key( 'slide_title', 'slides', $index );
        $this->add_inline_editing_attributes( $slide_title_key );
        $this->add_render_attribute( $slide_title_key , 'class', 'caption-heading' );

        $title = sprintf('<h2 %1$s>%2$s</h2>', $this->get_render_attribute_string( $slide_title_key ), esc_html($title));

      }


      if($content!='') { 

        $slide_content_key = $this->get_repeater_setting_key( 'slide_content', 'slides', $index );
        $this->add_inline_editing_attributes( $slide_content_key );
        $this->add_render_attribute( $slide_content_key , 'class', 'excerpt' );

        $content = '<p '.$this->get_render_attribute_string( $slide_content_key ).'>'.strip_tags(trim($content)).'</p>';

      }

      if($slide_subtitle!='') { 

        $slide_subtitle_key = $this->get_repeater_setting_key( 'slide_subtitle', 'slides', $index );
        $this->add_inline_editing_attributes( $slide_subtitle_key );
        $this->add_render_attribute( $slide_subtitle_key , 'class', 'caption-subheading' );

       $slide_subtitle = sprintf('<h4 %1$s>%2$s</h4>', $this->get_render_attribute_string( $slide_subtitle_key ), esc_html($slide_subtitle));

      }

      ob_start();


      if($slide['button_label'] !='') { 

        $button_label_key = $this->get_repeater_setting_key( 'button_label', 'slides', $index );
        $this->render_primary_button($button_label_key, $slide );
      }
      if($slide['button_r_label'] !='') { 

        $buttonr_label_key = $this->get_repeater_setting_key( 'button_r_label', 'slides', $index );
        $this->render_secondary_button($buttonr_label_key, $slide );
      }

      $button_html= ob_get_clean();

      $this->parse_slide_layout( $slide_html , 'heading', $title );
      $this->parse_slide_layout( $slide_html , 'subheading', $slide_subtitle );
      $this->parse_slide_layout( $slide_html , 'description', $content );
      $this->parse_slide_layout( $slide_html , 'button', $button_html );

      print $slide_html;

      ?>    
    </div>
</div>
</li>
<?php

  }

?>
</ul>
<?php if($navigation): ?>
    <nav class="slides-navigation">
<?php if($container_width == 'yes'){?>
      <div class="container">
<?php }?>
        <a href="#" class="prev"><?php if(!empty($navigation_left_icon['value'])) { Icons_Manager::render_icon( $navigation_left_icon, [ 'aria-hidden' => 'true' ] );} else{ ?><span></span><?php } ?></a>
        <a href="#" class="next"><?php if(!empty($navigation_right_icon['value'])) { Icons_Manager::render_icon( $navigation_right_icon, [ 'aria-hidden' => 'true' ] );} else{ ?><span></span><?php } ?></a>
<?php if($container_width == 'yes'){?>
          </div>
<?php }?>
      </nav>    
<?php endif;?>
  </div></div>
<script type="text/javascript">
  jQuery(document).ready(function($){
    'use strict';
    try{
    $('#<?php print esc_js($widgetID);?>').superslides({
      play: <?php print esc_js($play);?>,
      animation_speed: <?php print absint($slide_speed);?>,
      inherit_height_from: <?php print $slider_height == 'custom' && $slider_height_custom!='' ?  "'#".esc_js($widgetID)."-helper'" : 'window';?>,
      inherit_width_from: <?php print $slider_width == 'custom' && $slider_width_custom!='' ?  "'#".esc_js($widgetID)."-helper'" : 'window';?>,
      pagination: <?php if ($pagination) {?>true<?php }else{?>false<?php }?>,
      hashchange: false,
      scrollable: true,
<?php if($easing !='' ) { print 'animation_easing:\''. sanitize_html_class($easing).'\',';}?>
      animation: '<?php if ($image_animation =='fade'){?>fade<?php }else{?>slide<?php }?>'
    });
    }catch(err){}
  });
</script>
<?php

  }

  protected function parse_slide_layout( & $layouter , $key, $value ) {

    if(!$layouter) $layouter = '';

    $layouter = preg_replace('/{'.$key.'}/', $value, $layouter);
  }


  protected function get_slide_layout( $index = 1 ) {

    $layout = array(
      1=> '{heading}{description}{subheading}{button}',
      2=> '{heading}{subheading}{description}{button}',
      3=> '{heading}{subheading}{button}{description}'
    );

    if(array_key_exists($index, $layout) )
       return $layout[$index];

     return $layout[1];

  }

  protected function render_primary_button( $index, $slide = array() ) {

    $this->add_render_attribute( 'button-'.$index ,
      [
        'class' => ['elementor-button', 'primary-button' ],
        'role' => 'button'
      ]
    );

    if ( ! empty( $slide['button_link'] ) ) {
      $this->add_link_attributes( 'button-'.$index, array('url' => $slide['button_link']) );
      $this->add_render_attribute( 'button-'.$index, 'class', 'elementor-button-link' );
    }

    $migrated = isset( $slide['__fa4_migrated']['selected_icon'] );
    $is_new = empty( $slide['icon'] ) && Icons_Manager::is_migration_allowed();

    if ( ! $is_new && empty( $slide['icon_align'] ) ) {
      // @todo: remove when deprecated
      // added as bc in 2.6
      //old default
      $slide['icon_align'] = $this->get_settings( 'icon_align' );
    }

    $this->add_render_attribute( [
      'icon-align' => [
        'class' => [
          'elementor-button-icon',
          'elementor-align-icon-' . $slide['icon_align'],
        ],
      ],
    ] );

    $this->add_render_attribute( $index , 'class', 'elementor-button-text' );
    $this->add_inline_editing_attributes( $index, 'none' );

    ?><a <?php echo $this->get_render_attribute_string( 'button-'.$index ); ?>>
          <span class="elementor-button-content-wrapper">
      <?php if ( ! empty( $slide['icon'] ) || ! empty( $slide['selected_icon']['value'] ) ) : ?>
      <span <?php echo $this->get_render_attribute_string( 'icon-align' ); ?>>
        <?php if ( $is_new || $migrated ) :
          Icons_Manager::render_icon( $slide['selected_icon'], [ 'aria-hidden' => 'true' ] );
        else : ?>
          <i class="<?php echo esc_attr( $slide['icon'] ); ?>" aria-hidden="true"></i>
        <?php endif; ?>
      </span>
      <?php endif; ?>
      <span <?php echo $this->get_render_attribute_string( $index );?>><?php esc_html_e($slide['button_label']); ?></span>
    </span>
  </a><?php

  } 


  protected function render_secondary_button( $index, $slide = array() ) {

    $this->add_render_attribute( 'button-'.$index ,
      [
        'class' => ['elementor-button', 'secondary-button' ],
        'role' => 'button'
      ]
    );

    if ( ! empty( $slide['button_r_link'] ) ) {
      $this->add_link_attributes( 'button-'.$index, array('url' => $slide['button_r_link']) );
      $this->add_render_attribute( 'button-'.$index, 'class', 'elementor-button-link' );
    }

    $migrated = isset( $slide['__fa4_migrated']['selected_r_icon'] );
    $is_new = empty( $slide['r_icon'] ) && Icons_Manager::is_migration_allowed();

    if ( ! $is_new && empty( $slide['icon_r_align'] ) ) {
      // @todo: remove when deprecated
      // added as bc in 2.6
      //old default
      $slide['icon_r_align'] = $this->get_settings( 'icon_r_align' );
    }

    $this->add_render_attribute( [
      'icon-r-align' => [
        'class' => [
          'elementor-button-icon',
          'elementor-align-icon-' . $slide['icon_r_align'],
        ],
      ],
    ] );

    $this->add_render_attribute( $index , 'class', 'elementor-button-text' );
    $this->add_inline_editing_attributes( $index, 'none' );

    ?><a <?php echo $this->get_render_attribute_string( 'button-'.$index ); ?>>
          <span class="elementor-button-content-wrapper">
      <?php if ( ! empty( $slide['r_icon'] ) || ! empty( $slide['selected_r_icon']['value'] ) ) : ?>
      <span <?php echo $this->get_render_attribute_string( 'icon-r-align' ); ?>>
        <?php if ( $is_new || $migrated ) :
          Icons_Manager::render_icon( $slide['selected_r_icon'], [ 'aria-hidden' => 'true' ] );
        else : ?>
          <i class="<?php echo esc_attr( $slide['r_icon'] ); ?>" aria-hidden="true"></i>
        <?php endif; ?>
      </span>
      <?php endif; ?>
      <span <?php echo $this->get_render_attribute_string( $index );?>><?php esc_html_e( $slide['button_r_label'] ); ?></span>
    </span>
  </a><?php

  } 
    public function enqueue_script( ) {

      wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());

      wp_enqueue_script( 'easing' , GUM_ELEMENTOR_URL . 'js/jquery.easing.1.3.js', array(), '1.0', true );
      wp_enqueue_script( 'superslides' , GUM_ELEMENTOR_URL . 'js/jquery.superslides.js', array('jquery','easing'), '1.0', true );
  }
}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Elementor_Petro_Slides_Widget() );


?>
