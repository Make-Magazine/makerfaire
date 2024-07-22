<?php
namespace Elementor;
/**
 * @package     WordPress
 * @subpackage  Gum Elementor Addon
 * @author      support@themegum.com
 * @since       1.0.10
*/
defined('ABSPATH') or die();

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use WP_Query;

/**
 * Post related widget
 * @since       1.0.11
*/

class Gum_Elementor_Widget_Post_related extends Widget_Base {


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
    return 'gum_post_related';
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

    return esc_html__( 'Post Related', 'gum-elementor-addon' );
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
    return 'eicon-table-of-contents';
  }

  public function get_keywords() {
    return [ 'wordpress', 'widget', 'post','related','category' ];
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
    return [ 'temegum_blog' ];
  }

  protected function _register_controls() {


    $this->start_controls_section(
      'section_layout',
      [
        'label' => esc_html__( 'Layout', 'gum-elementor-addon' ),
      ]
    );


    $this->add_responsive_control(
      'post_list_wide',
      [
        'label' => esc_html__( 'Grid Wide', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          '%' => [
            'min' => 10,
            'max' => 100,
            'step' => 0.1
          ],
        ],  
        'default'=>['size'=>33.33,'unit'=>'%'],
        'size_units' => [ '%' ],
        'style_transfer' => true,
        'selectors' => [
          '{{WRAPPER}} .related-post-container' => 'width: {{SIZE}}%;',
        ],
      ]
    );

    $this->add_control(
      'show_image',
      [
        'label' => esc_html__( 'Show Image', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'default' => 'yes',
      ]
    );


    $this->add_group_control(
      Group_Control_Image_Size::get_type(),
      [
        'name' => 'thumbnail', 
        'default' => 'medium',
        'condition' => [
          'show_image[value]' => 'yes'
        ],
      ]
    );

    $this->add_control(
      'show_content',
      [
        'label' => esc_html__( 'Show Content', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'default' => 'yes',
      ]
    );

   $this->add_control(
      'post_content_word',
      [
        'label'     => esc_html__( 'Word Count', 'gum-elementor-addon' ),
        'type'      => Controls_Manager::NUMBER,
        'default'   => '',
        'condition' => [
          'show_content[value]' => 'yes'
        ],
      ]
    );

   $this->add_control(
      'post_content_sufix',
      [
        'label'     => esc_html__( 'Suffix', 'gum-elementor-addon' ),
        'type'      => Controls_Manager::TEXT,
        'default'   => '',
        'ai' => [
          'active' => false,
        ],
        'condition' => [
          'show_content[value]' => 'yes',
          'post_content_word!' => ''
        ],
      ]
    );   

    $this->add_control(
      'show_meta',
      [
        'label' => esc_html__( 'Display post date?', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => 'yes',
      ]
    );


    $this->add_control(
      'date_meta_position',
      [
        'label' => esc_html__( 'Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'top' => esc_html__( 'Before Title', 'gum-elementor-addon' ),
          'mid' => esc_html__( 'After Title', 'gum-elementor-addon' ),
          'bottom' => esc_html__( 'After Content', 'gum-elementor-addon' ),
        ],
        'condition' => [
          'show_meta[value]' => 'yes'
        ],
        'default' => 'bottom'
      ]
    );


    $this->add_control(
      'meta_icon',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
        'condition' => [
          'show_meta[value]' => 'yes'
        ],
      ]
    );


    $this->add_control(
      'show_readmore',
      [
        'label' => esc_html__( 'Show Readmore', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => 'no',
      ]
    );


    $this->add_control(
      'readmore_label',
      [
        'label' => esc_html__( 'Label', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'default' => esc_html__( 'Readmore', 'gum-elementor-addon' ),
        'label_block' => true,
        'ai' => [
          'active' => false,
        ],
        'condition' => [
          'show_readmore[value]' => 'yes',
        ],

      ]
    );

    $this->add_control(
      'readmore_icon',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
        'condition' => [
          'show_readmore[value]' => 'yes',
        ],

      ]
    );


    $this->add_control(
      'readmore_icon_align',
      [
        'label' => esc_html__( 'Icon Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'left',
        'options' => [
          'left' => esc_html__( 'Before', 'gum-elementor-addon' ),
          'right' => esc_html__( 'After', 'gum-elementor-addon' ),
        ],
        'condition' => [
          'show_readmore[value]' => 'yes',
          'readmore_icon[value]!' => '',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_title',
      [
        'label' => esc_html__( 'Query', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'post_type',
      [
        'label' => esc_html__( 'Related In', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'tag' => esc_html__( 'Same Tag', 'gum-elementor-addon' ),
          'cat' => esc_html__( 'Same Category', 'gum-elementor-addon' ),
          'all' => esc_html__( 'Same Tag or Category', 'gum-elementor-addon' ),
        ],
        'default' => 'all'
      ]
    );

    $this->add_control(
      'posts_per_page',
      [
        'label' => esc_html__( 'Post Count', 'gum-elementor-addon' ),
        'type' => Controls_Manager::NUMBER,
        'min' => 1,
        'max' => 100,
        'step' => 1,
        'default'=>3
      ]
    );

    $this->add_control(
      'is_featured_image',
      [
        'label' => esc_html__( 'Featured Image Only', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'default' => '',
      ]
    );

    $this->end_controls_section();


/*
 * style params
 */


    $this->start_controls_section(
      'post_list_style',
      [
        'label' => esc_html__( 'Box Styles', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    



    $this->add_responsive_control(
      'post_list_space',
      [
        'label' => esc_html__( 'Horizontal Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 1000,
          ],
        ],  
        'default'=>['size'=> 10,'unit'=>'px'],
        'size_units' => [ 'px' ],
        'selectors' => [
          '{{WRAPPER}} .related-post-wrap' => 'margin-right: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .related-posts' => 'margin-right: -{{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'post_list_vspace',
      [
        'label' => esc_html__( 'Vertical Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 1000,
          ],
        ],  
        'default'=>['size'=> 0,'unit'=>'px'],
        'size_units' => [ 'px' ],
        'selectors' => [
          '{{WRAPPER}} .related-post-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'post_list_padding',
      [
          'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px'],
          'selectors' => [
              '{{WRAPPER}} .related-post-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );


    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'post_list_border',
        'selector' => '{{WRAPPER}} .related-post-wrap',
      ]
    );


   $this->start_controls_tabs( 'post_list_tabs', [] );
   $this->start_controls_tab(
       'post_list_normal',
       [
           'label' =>esc_html__( 'Normal', 'elementor' ),
       ]
   );

    $this->add_control(
      'post_list_bgcolor',
      [
        'label' => esc_html__( 'Background', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .related-post-wrap' => 'background-color: {{VALUE}};',
        ]
      ]
    );

   $this->end_controls_tab();

   $this->start_controls_tab(
       'post_list_hover',
       [
           'label' =>esc_html__( 'Hover', 'elementor' ),
       ]
   );

    $this->add_control(
      'post_list_bghover',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .related-post-wrap:hover' => 'background-color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'post_list_bdhover',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .related-post-wrap:hover' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'post_list_border_border!' => ''
        ],
      ]
    );


   $this->end_controls_tab();
   $this->end_controls_tabs();

    $this->add_control(
      'post_list_radius',
      [
        'label' => esc_html__( 'Box Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .related-post-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'post_content_heading',
      [
        'label' => esc_html__( 'Content Box', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before'
      ]
    );


    $this->add_responsive_control(
      'post_content_space',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -200,
            'max' => 200,
          ],
        ],  
        'default'=>['size'=>0,'unit'=>'px'],
        'size_units' => [ 'px' ],
        'selectors' => [
          '{{WRAPPER}} .related-post-content' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'post_content_padding',
      [
          'label' => esc_html__( 'Padding', 'elementor' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px'],
          'selectors' => [
              '{{WRAPPER}} .related-post-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'post_content_border',
        'selector' => '{{WRAPPER}} .related-post-content',
      ]
    );


   $this->start_controls_tabs( 'post_content_tabs', [] );
   $this->start_controls_tab(
       'post_content_normal',
       [
           'label' =>esc_html__( 'Normal', 'elementor' ),
       ]
   );

    $this->add_control(
      'post_content_bgcolor',
      [
        'label' => esc_html__( 'Background', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .related-post-content' => 'background-color: {{VALUE}};',
        ]
      ]
    );

   $this->end_controls_tab();

   $this->start_controls_tab(
       'post_content_hover',
       [
           'label' =>esc_html__( 'Hover', 'elementor' ),
       ]
   );

    $this->add_control(
      'post_content_bghover',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .related-post-wrap:hover .related-post-content' => 'background-color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'post_content_bdhover',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .related-post-wrap:hover .related-post-content' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'post_content_border_border!' => ''
        ],
      ]
    );

    $this->add_control(
      'post_content_texthover',
      [
        'label' => esc_html__( 'Text Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .related-post-wrap:hover .related-post-content *' => 'color: {{VALUE}};',
        ],
      ]
    );

   $this->end_controls_tab();
   $this->end_controls_tabs();



    $this->add_control(
      'post_content_radius',
      [
        'label' => esc_html__( 'Box Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .related-post-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'post_image_style',
      [
        'label' => esc_html__( 'Featured Image', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'show_image[value]' => 'yes'
        ],
      ]
    );    

    $this->add_responsive_control(
      'post_image_space',
      [
        'label' => esc_html__( 'Height', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 1000,
          ],
          'vh' => [
            'max' => 100,
          ],
        ],  
        'default'=>['size'=>135,'unit'=>'px'],
        'size_units' => [ 'px','vh' ],
        'selectors' => [
          '{{WRAPPER}} .blog-image' => 'height: {{SIZE}}{{UNIT}};min-height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'post_image_padding',
      [
          'label' => esc_html__( 'Padding', 'elementor' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px'],
          'selectors' => [
              '{{WRAPPER}} .blog-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'post_image_border',
        'selector' => '{{WRAPPER}} .blog-image',
      ]
    );


    $this->add_control(
      'post_image_hcolor',
      [
        'label' => esc_html__( 'Hover Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .related-post-wrap:hover .blog-image,{{WRAPPER}} .related-post-wrap:focus .blog-image' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'post_image_border_border!' => ''
        ],
      ]
    );


    $this->add_control(
      'post_image_radius',
      [
        'label' => esc_html__( 'Box Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .blog-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();


    $this->start_controls_section(
      'post_title_style',
      [
        'label' => esc_html__( 'Post Title', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    

    $this->add_responsive_control(
      'post_title_align',
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
          'justify' => [
            'title' => esc_html__( 'Justified', 'elementor' ),
            'icon' => 'eicon-text-align-justify',
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .related-post-title' => 'text-align: {{VALUE}};',
        ],
        'default' => '',
      ]
    );

    $this->add_responsive_control(
      'post_title_space',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -200,
            'max' => 200,
          ],
        ],  
        'default'=>['size'=>10,'unit'=>'px'],
        'size_units' => [ 'px' ],
        'selectors' => [
          '{{WRAPPER}} .related-post-title' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_post_title',
        'selector' => '{{WRAPPER}} .related-post-title',
      ]
    );

    $this->add_control(
      'post_title_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .related-post-title' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'post_title_hcolor',
      [
        'label' => esc_html__( 'Hover Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .related-post-title:hover,{{WRAPPER}} .related-post-title:focus' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_responsive_control(
      'post_title_padding',
      [
          'label' => esc_html__( 'Padding', 'elementor' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px'],
          'selectors' => [
              '{{WRAPPER}} .related-post-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'post_title_border',
        'selector' => '{{WRAPPER}} .related-post-title',
      ]
    );

    $this->end_controls_section();


    $this->start_controls_section(
      'post_content_style',
      [
        'label' => esc_html__( 'Post Content', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'show_content[value]' => 'yes'
        ],
      ]
    );    

    $this->add_responsive_control(
      'post_content_align',
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
          'justify' => [
            'title' => esc_html__( 'Justified', 'elementor' ),
            'icon' => 'eicon-text-align-justify',
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .related-post-description' => 'text-align: {{VALUE}};',
        ],
        'default' => '',
      ]
    );


    $this->add_responsive_control(
      'post_text_space',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 200,
          ],
        ],  
        'default'=>['size'=>'','unit'=>'px'],
        'size_units' => [ 'px' ],
        'selectors' => [
          '{{WRAPPER}} .related-post-description' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_post_text',
        'selector' => '{{WRAPPER}} .related-post-description',
      ]
    );

    $this->add_control(
      'post_text_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .related-post-description' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'post_meta_style',
      [
        'label' => esc_html__( 'Post Date', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'show_meta[value]' => 'yes'
        ],
      ]
    );    

    $this->add_responsive_control(
      'post_meta_space',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 200,
          ],
        ],  
        'default'=>['size'=>10,'unit'=>'px'],
        'size_units' => [ 'px' ],
        'selectors' => [
          '{{WRAPPER}} .post-metainfo' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_post_meta',
        'selector' => '{{WRAPPER}} .post-metainfo',
      ]
    );

    $this->add_control(
      'post_meta_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .post-metainfo' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_responsive_control(
      'post_meta_padding',
      [
          'label' => esc_html__( 'Padding', 'elementor' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px'],
          'selectors' => [
              '{{WRAPPER}} .post-metainfo' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'post_meta_border',
        'selector' => '{{WRAPPER}} .post-metainfo',
      ]
    );

    $this->add_control(
      'post_meta_hcolor',
      [
        'label' => esc_html__( 'Hover Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .related-post-wrap:hover .post-metainfo,{{WRAPPER}} .related-post-wrap:focus .post-metainfo' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'post_meta_border_border!' => ''
        ],
      ]
    );

    $this->add_control(
      'icon_style_heading',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before'
      ]
    );

    $this->add_control(
      'icon_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .post-metainfo i, {{WRAPPER}} .post-metainfo path' => 'fill: {{VALUE}}; color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'icon_size',
      [
        'label' => esc_html__( 'Icon Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 100,
          ],
        ],
        'default' =>['value'=>'', 'unit'=>'px'],
        'selectors' => [
          '{{WRAPPER}} .post-metainfo i' => 'font-size: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .post-metainfo svg' => 'height: {{SIZE}}%;width: {{SIZE}}%;'
        ],
        'condition' => ['meta_icon[value]!' => ''],
      ]
    );

    $this->add_control(
      'icon_indent',
      [
        'label' => esc_html__( 'Text Indent', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 100,
          ],
        ],
        'default' =>['value'=>'', 'unit'=>'px'],
        'selectors' => [
          '{{WRAPPER}} .post-metainfo .date-meta' => 'padding-right: {{SIZE}}{{UNIT}};',
        ],
        'condition' => ['meta_icon[value]!' => ''],
      ]
    );

    $this->end_controls_section();


    $this->start_controls_section(
      'post_readmore_style',
      [
        'label' => esc_html__( 'Readmore', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'show_readmore[value]' => 'yes'
        ],
      ]
    );  

    $this->add_control(
      'readmore_button_align',
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
          'full' => [
            'title' => esc_html__( 'Full Width', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-stretch',
          ],
          'right' => [
            'title' => esc_html__( 'Right', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-right',
          ],
        ],
        'default' => '',
        'condition' => ['show_readmore[value]' => 'yes']
      ]
    );


    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_readmore_button',
        'selector' => '{{WRAPPER}} .elementor-button',
      ]
    );

    $this->add_responsive_control(
      'readmore_button_margin',
      [
          'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', '%', 'em' ],
          'selectors' => [
              '{{WRAPPER}} .elementor-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );


    $this->add_responsive_control(
      'readmore_button_padding',
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
      'readmore_button_radius',
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
        'name' => 'readmore_button_border',
        'selector' => '{{WRAPPER}} article .elementor-button',
        'separator' => 'before',
      ]
    );


    $this->start_controls_tabs( 'tabs_readmore_button_style' );

    $this->start_controls_tab(
      'tab_readmore_button_normal',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'readmore_button_color',
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
      'readmore_button_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_tab();

    $this->start_controls_tab(
      'tab_readmore_button_hover',
      [
        'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'readmore_button_hover_color',
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
      'readmore_button_background_hover_color',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'readmore_button_hover_border_color',
      [
        'label' => esc_html__( 'Border', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'condition' => [
          'readmore_button_border_border!' => '',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_tab();
    $this->end_controls_tabs();

    $this->add_control(
      'readmore_icon_heading',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
        'condition' => ['show_readmore[value]' => 'yes','readmore_icon[value]!' => ''],
      ]
    );

    $this->add_control(
      'readmore_icon_indent',
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
        'condition' => ['show_readmore[value]' => 'yes','readmore_label!' => '','readmore_icon[value]!' => ''],
      ]
    );


    $this->add_control(
      'readmore_icon_size',
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
        'condition' => ['show_readmore[value]' => 'yes','readmore_icon[value]!' => ''],
      ]
    );


    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'readmore_icon_border',
        'selector' => '{{WRAPPER}} .elementor-button .elementor-button-icon',
        'condition' => ['show_readmore[value]' => 'yes','readmore_icon[value]!' => ''],
      ]
    );

    $this->add_responsive_control(
        'readmore_icon_padding',
        [
            'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} .elementor-button .elementor-button-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => ['show_readmore[value]' => 'yes','readmore_icon[value]!' => '','readmore_icon_border_border!' => ''],
        ]
    );

    $this->add_control(
      'readmore_icon_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button .elementor-button-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => ['show_readmore[value]' => 'yes','readmore_icon[value]!' => '','readmore_icon_border_border!'=>''],
      ]
    );

    $this->start_controls_tabs( '_tabs_readmore_icon_style',['condition' => ['show_readmore[value]' => 'yes','readmore_icon[value]!' => '']] );

    $this->start_controls_tab(
      '_tab_readmore_icon_normal',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'readmore_icon_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button .elementor-button-icon i,{{WRAPPER}} .elementor-button .elementor-button-icon svg' => 'color: {{VALUE}}!important,fill: {{VALUE}}!important;',
        ],
        'condition' => ['show_readmore[value]' => 'yes','readmore_icon[value]!' => ''],
      ]
    );


    $this->add_control(
      'readmore_icon_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-button .elementor-button-icon' => 'background: {{VALUE}};',
        ],
        'condition' => ['readmore_icon_border_border!' => ''],
      ]
    );

    $this->add_control(
      'readmore_icon_rotate',
      [
        'label' => esc_html__( 'Rotate', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'deg' ],
        'default' => [
          'size' => 0,
          'unit' => 'deg',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button .elementor-button-icon i, {{WRAPPER}} .elementor-button .elementor-button-icon svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
        ],
        'condition' => ['show_readmore[value]' => 'yes','readmore_icon[value]!' => ''],

      ]
    );

    $this->end_controls_tab();
    $this->start_controls_tab(
      '_tab_readmore_icon_hover',
      [
        'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'readmore_icon_hover_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover .elementor-button-icon i,{{WRAPPER}} .elementor-button:hover .elementor-button-icon svg' => 'color: {{VALUE}}!important,fill: {{VALUE}}!important;',
        ],
        'condition' => ['show_readmore[value]' => 'yes','readmore_icon[value]!' => ''],
      ]
    );

    $this->add_control(
      'readmore_icon_hover_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover .elementor-button-icon' => 'background: {{VALUE}};',
        ],
        'condition' => ['readmore_icon_border_border!' => ''],
      ]
    );

    $this->add_control(
      'readmore_icon_border_hover_color',
      [
        'label' => esc_html__( 'Border', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover .elementor-button-icon' => 'border-color: {{VALUE}}!important;',
        ],
        'condition' => ['show_readmore!' => '','readmore_icon[value]!' => '','readmore_icon_border_border!'=>''],
      ]
    );


    $this->add_control(
      'readmore_icon_hover_rotate',
      [
        'label' => esc_html__( 'Rotate', 'gum-elementor-addon' ),
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
      'readmore_icon_transform_transition_hover',
      [
        'label' => esc_html__( 'Transition Duration (ms)', 'gum-elementor-addon' ),
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

    $posts_per_page = isset($posts_per_page) && $posts_per_page!='' ? $posts_per_page : 3;
    $post_id = get_the_ID();

    $query_params= array(
      'posts_per_page' => absint($posts_per_page),
      'no_found_rows' => true,
      'post_status' => 'publish',
      'post_type'=>'post',
      'ignore_sticky_posts' => true,
      'post__not_in' => [$post_id]
    );

    if($is_featured_image === 'yes'){
        $query_params['meta_query'] = array(
            'relation' => 'AND',
            array('key' => '_thumbnail_id')
        );
    }

    $meta_icon_html = '';

    if( $show_meta == 'yes'){

      if ( 'svg' === $meta_icon['library'] ) {
        $meta_icon_html = Icons_Manager::render_uploaded_svg_icon( $meta_icon['value'] );
      } else {
        $meta_icon_html = Icons_Manager::render_font_icon( $meta_icon, [ 'aria-hidden' => 'true' ], 'i' );
      }
    }

    if($post_type =='cat'){

      $categories = get_the_category();
      $cat_options=array();

      if($categories){
        foreach ($categories as $category) {
          $cat_options[]= $category->term_id;
        }

        $query_params['cat'] = $cat_options;
      }

    }elseif($post_type =='tag'){

      $tags = get_the_tags();
      $tag_options=array();

      if($tags){
        foreach ($tags as $tag) {
          $tag_options[]= $tag->term_id;
        }

        $query_params['tax_query'] = array(
          array(
            'taxonomy' => 'post_tag',
            'field' => 'id',
            'terms' => $tag_options,
            'operator' => 'IN'
          )
        );

      }

    }else{

      $categories = get_the_category();
      $cat_options = $tax_query = $tag_options = array();

      if($categories){
        foreach ($categories as $category) {
          $cat_options[]= $category->term_id;
        }

        $tax_query[] = array(
          'taxonomy' => 'category',
          'field' => 'id',
          'terms' => $cat_options
          );
  
      }

      $tags = get_the_tags();

      if($tags){
        foreach ($tags as $tag) {
          $tag_options[]= $tag->term_id;
        }

        $tax_query['relation'] = 'OR';
        $tax_query[] = array(
            'taxonomy' => 'post_tag',
            'field' => 'id',
            'terms' => $tag_options
        );

      }

      $query_params['tax_query'] = $tax_query;

    }

    $post_query = new WP_Query($query_params);

     if (is_wp_error($post_query) || !$post_query->have_posts()) {
      return '';
    }

    $rows_html  = array();

    while ( $post_query->have_posts() ) : 

      $post_query->the_post();
      $post_id = get_the_ID();

      $post_title = get_the_title();
      $post_format = get_post_format();

      $post_url = get_the_permalink();
      $image_url = null;
      $text_content = '';
      $settings['post_url'] = $post_url;

      if($show_image =='yes'){

        $thumb_id = get_post_thumbnail_id( $post_id );
        $image_url = Group_Control_Image_Size::get_attachment_image_src($thumb_id, 'thumbnail', $settings);
      }

      if($show_content == 'yes'){
        $text_content = get_the_excerpt();

        if($post_content_word > 0){
          $text_content = wp_trim_words($text_content ,  absint($post_content_word) , $post_content_sufix );
        }
      }

      ob_start();?>
      <div class="related-post-container">
        <div class="related-post-wrap">
        <?php if( $show_image == 'yes' && $image_url){?>
          <a class="related-post-link" href="<?php print esc_url($post_url);?>">
            <div class="blog-image" style="background-image:url(<?php print esc_url($image_url);?>">
            <div style="clear:both"></div>
            </div>
          </a>
        <?php } ?>
        <div class="related-post-content">       
        <?php if( $show_meta == 'yes' && $date_meta_position == 'top'){ ?>
        <div class="post-metainfo">
          <?php if( !empty($meta_icon['value'])){ ?><span class="date-meta"><?php print Utils::print_unescaped_internal_string( $meta_icon_html ); ?></span><?php } the_date();?>
        </div>
        <?php } ?>
        <a class="related-post-title" href="<?php print esc_url($post_url);?>"><?php print $post_title; ?></a>
         <?php if( $show_meta == 'yes' && $date_meta_position == 'mid'){ ?>
        <div class="post-metainfo">
          <?php if( !empty($meta_icon['value'])){ ?><span class="date-meta"><?php print Utils::print_unescaped_internal_string( $meta_icon_html ); ?></span><?php } the_date();?>
        </div>
        <?php } ?>
        <?php if($show_content == 'yes' && $text_content !=''){?>
        <div class="related-post-description"><?php print esc_html($text_content); ?></div>
        <?php }
        if( $show_meta == 'yes' && $date_meta_position == 'bottom'){ ?>
        <div class="post-metainfo">
          <?php if( !empty($meta_icon['value'])){ ?><span class="date-meta"><?php print Utils::print_unescaped_internal_string( $meta_icon_html ); ?></span><?php } the_date();?>
        </div>
        <?php } ?>
        <?php if( $show_readmore == 'yes'){ 
          $this->get_readmore_button( 'post-'.$post_id,  $settings ); 
        } ?>
        </div>
      </div>
    </div>
        <?php
      $rows_html[] = ob_get_clean();
    endwhile;

    wp_reset_postdata();

    $this->add_render_attribute( 'related_wrapper', 'class', 'related-posts');

    echo '<div '.$this->get_render_attribute_string( 'related_wrapper' ).'>'.join('',$rows_html).'</div><div class="not-empty">&nbsp;</div>';

  }


  protected function get_readmore_button( $index, $settings = array() ) {

     if(!isset($settings['show_readmore']) || $settings['show_readmore']!='yes' ) return '';

    $this->add_render_attribute( 'button-'.$index ,
      [
        'class' => ['elementor-button', 'readmore-button' ],
        'role' => 'button'
      ]
    );

    $this->add_link_attributes( 'button-'.$index, array('url' => $settings['post_url']) );
    $this->add_render_attribute( 'button-'.$index, 'class', 'elementor-button-link' );

    $this->add_render_attribute( [
      'readmore_icon_align' => [
        'class' => [
          'elementor-button-icon',
          'elementor-align-icon-' . $settings['readmore_icon_align'],
        ],
      ],
    ] );

    $this->add_render_attribute( $index , 'class', 'elementor-button-text' );
    $this->add_inline_editing_attributes( $index, 'none' );

    $readmore_button_align = isset( $settings['readmore_button_align'] ) ? $settings['readmore_button_align'] : '';

    ?><div class="elementor-button-wrap<?php print ' button-align-'.$readmore_button_align ;?>"><a <?php echo $this->get_render_attribute_string( 'button-'.$index ); ?>>
          <span class="elementor-button-content-wrapper">
      <?php if ( ! empty( $settings['readmore_icon']['value'] ) ) : ?>
      <span <?php echo $this->get_render_attribute_string( 'readmore_icon_align' ); ?>>
          <?php Icons_Manager::render_icon( $settings['readmore_icon'], [ 'aria-hidden' => 'true' ] ); ?>
      </span>
      <?php endif; ?>
      <span <?php echo $this->get_render_attribute_string( $index );?>><?php echo esc_html($settings['readmore_label']); ?></span>
    </span>
  </a></div><?php

  }
  protected function content_template() {

  }

  public function enqueue_script( ) {

    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());
  }


}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Gum_Elementor_Widget_Post_related() );

?>