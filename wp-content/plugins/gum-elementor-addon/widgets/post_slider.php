<?php
namespace Elementor;
/**
 * @package     WordPress
 * @subpackage  Gum Elementor Addon
 * @author      support@themegum.com
 * @since       1.2.0
*/
defined('ABSPATH') or die();

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use WP_Query;
use Gum_Elementor_Helper;

class Gum_Elementor_Widget_post_slider extends Widget_Base {

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
    return 'gum_posts_slider';
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

    return esc_html__( 'Post Slider', 'gum-elementor-addon' );
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
    return 'fas fa-xs fa-newspaper';
  }

  public function get_keywords() {
    return [ 'wordpress', 'widget', 'post', 'recent', 'blog','slider' ];
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
        'label' => esc_html__( 'Slide', 'elementor' ),
      ]
    );

    $this->add_control(
      'tag',
      [
        'label' => esc_html__( 'Title HTML Tag', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'h2' => 'H2',
          'h3' => 'H3',
          'h4' => 'H4',
          'h5' => 'H5',
          'h6' => 'H6',
          'div' => 'div',
        ],
        'default' => 'h4',
      ]
    );


    $this->add_control(
      'image_position',
      [
        'label' => esc_html__( 'Image Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => esc_html__( 'Left', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-left',
          ],
          'top' => [
            'title' => esc_html__( 'Top', 'gum-elementor-addon' ),
            'icon' => 'eicon-v-align-top',
          ],
          'right' => [
            'title' => esc_html__( 'Right', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-right',
          ],
        ],
        'default' => 'top',
      ]
    );


    $this->add_control(
      'image_totop',
      [
        'label' => esc_html__( 'Ignore on Mobile', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => 'yes',
        'description' => esc_html__( 'Image changed to top on mobile', 'gum-elementor-addon' ),
        'condition' => [
          'image_position!' => 'top',
        ],
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
      'show_excerpt',
      [
        'label' => esc_html__( 'Show Post Excerpt', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => 'yes',
        'separator' => 'before'
      ]
    );

   $this->add_control(
      'post_content_word',
      [
        'label'     => esc_html__( 'Word Count', 'gum-elementor-addon' ),
        'type'      => Controls_Manager::NUMBER,
        'default'   => '',
        'condition' => [
          'show_excerpt' => 'yes',
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
          'show_excerpt' => 'yes',
          'post_content_word!' => ''
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
        'default' => 'yes',
        'separator' => 'before'
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
          'show_readmore' => 'yes',
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
          'show_readmore' => 'yes',
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
          'show_readmore' => 'yes',
          'readmore_icon[value]!' => '',
        ],
      ]
    );

    $this->add_control(
      'show_meta',
      [
        'label' => esc_html__( 'Show Meta', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => 'yes',
        'separator' => 'before'
      ]
    );

    $this->add_control(
      'date_meta',
      [
        'label' => esc_html__( 'Date', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          '' => esc_html__( 'None', 'gum-elementor-addon' ),
          'top' => esc_html__( 'Before Title', 'gum-elementor-addon' ),
          'mid' => esc_html__( 'After Title', 'gum-elementor-addon' ),
          'bottom' => esc_html__( 'Bottom', 'gum-elementor-addon' ),
        ],
        'condition' => [
          'show_meta!' => ''
        ],
        'default' => 'mid'
      ]
    );

    $this->add_control(
      'author_meta',
      [
        'label' => esc_html__( 'Author', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          '' => esc_html__( 'None', 'gum-elementor-addon' ),
          'top' => esc_html__( 'Before Title', 'gum-elementor-addon' ),
          'mid' => esc_html__( 'After Title', 'gum-elementor-addon' ),
          'bottom' => esc_html__( 'Bottom', 'gum-elementor-addon' ),
        ],
        'condition' => [
          'show_meta!' => ''
        ],
        'default' => 'mid'
      ]
    );

    $this->add_control(
      'category_meta',
      [
        'label' => esc_html__( 'Category', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          '' => esc_html__( 'None', 'gum-elementor-addon' ),
          'top' => esc_html__( 'Before Title', 'gum-elementor-addon' ),
          'mid' => esc_html__( 'After Title', 'gum-elementor-addon' ),
          'bottom' => esc_html__( 'Bottom', 'gum-elementor-addon' ),
        ],
        'condition' => [
          'show_meta!' => ''
        ],
        'default' => 'top'
      ]
    );

    $this->add_control(
      'meta_divider',
      [
        'label' => esc_html__( 'Divider', 'gum-elementor-addon' ),
        'type' => Controls_Manager::CHOOSE,
        'default' => '',
        'options' => [
          '' => [
            'title' => esc_html__( 'None', 'gum-elementor-addon' ),
            'icon' => 'eicon-ban',
          ],
          'text' => [
            'title' => esc_html__( 'Text', 'gum-elementor-addon' ),
            'icon' => 'eicon-t-letter-bold',
          ],
          'icon' => [
            'title' => esc_html__( 'Icon', 'gum-elementor-addon' ),
            'icon' => 'eicon-star',
          ],
        ],
        'prefix_class' => 'elementor-post-meta-divider-',
        'toggle' => false,
        'condition' => [
          'show_meta!' => ''
        ],
      ]
    );

    $this->add_control(
      'divider_text',
      [
        'label' => esc_html__( 'Text', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'condition' => [
          'meta_divider' => 'text',
          'show_meta!' => ''
        ],
        'ai' => [
          'active' => false,
        ],
        'default' => '-',
        'dynamic' => [
          'active' => false,
        ],
      ]
    );


    $this->add_control(
      'divider_icon',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'default' => [
          'value' => 'fas fa-caret-right',
          'library' => 'fa-solid',
        ],
        'condition' => [
          'meta_divider' => 'icon',
          'show_meta!' => ''
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_data',
      [
        'label' => esc_html__( 'Query', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'filter_by',
      [
        'label' => esc_html__( 'Selection', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          '' => esc_html__( 'Select All', 'gum-elementor-addon' ),
          'category' => esc_html__( 'By Category', 'gum-elementor-addon' ),
          'tag' => esc_html__( 'By Tag', 'gum-elementor-addon' ),
          'ids' => esc_html__( 'By IDs', 'gum-elementor-addon' ),
          'sticky' => esc_html__( 'Sticky Only', 'gum-elementor-addon' )
        ],
        'default' => ''
      ]
    );



    $categories_options = array(
       ''=> esc_html__( 'Select Category', 'gum-elementor-addon' ),
     );

   $categories_args = array(
            'orderby' => 'name',
            'show_count' => 0,
            'pad_counts' => 0,
            'hierarchical' => 0,
    );


    $categories=get_categories($categories_args);

    if(count($categories)){

        foreach ( $categories as $category ) {
          $categories_options[$category->term_id] = $category->name;
        }

     }

    $this->add_control(
      'cat_ids',
      [
        'label' => esc_html__( 'By Category', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => $categories_options,
        'default' => '',
        'condition' => [
          'filter_by' => 'category'
        ],
      ]
    );


    $tags_options = array(
       ''=> esc_html__( 'Select Tag', 'gum-elementor-addon' ),
     );

   $tags_args = array(
            'orderby' => 'name',
            'show_count' => 0,
            'pad_counts' => 0,
            'hierarchical' => 0,
    );


    $tags=get_tags($tags_args);

    if(count($tags)){

        foreach ( $tags as $term ) {
          $tags_options[$term->term_id] = $term->name;
        }

     }

    $this->add_control(
      'tags_ids',
      [
        'label' => esc_html__( 'By Tags', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => $tags_options,
        'default' => '',
        'condition' => [
          'filter_by' => 'tag'
        ],
      ]
    );

    $this->add_control(
      'posts_ids',
      [
        'label' => esc_html__( 'Post ID', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'default' => '',
        'ai' => [
          'active' => false,
        ],
        'description' => esc_html__( 'Type post IDs. Seperated by comma', 'gum-elementor-addon' ),
        'label_block' => true,
        'condition' => [
          'filter_by' => 'ids'
        ],
      ]
    );

    $this->add_control(
      'posts_per_page',
      [
        'label' => esc_html__( 'Post to Show', 'gum-elementor-addon' ),
        'type' => Controls_Manager::NUMBER,
        'min' => 1,
        'max' => 100,
        'step' => 1,
        'default'=>get_option('posts_per_page')
      ]
    );

    $this->add_control(
      'posts_offset',
      [
        'label' => esc_html__( 'Post Offset', 'gum-elementor-addon' ),
        'type' => Controls_Manager::NUMBER,
        'min' => 1,
        'max' => 1000,
        'step' => 1,
        'default'=> 0
      ]
    );


    $this->add_control(
      'ignore_sticky',
      [
        'label' => esc_html__( 'Ignore Sticky', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => 'yes',
        'description' => esc_html__( 'Sticky post ignored from ordering', 'gum-elementor-addon' ),
        'condition' => [
          'filter_by!' => 'sticky'
        ],
      ]
    );

    $this->add_control(
      'skip_previous',
      [
        'label' => esc_html__( 'Exclude Previous Post', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => '',
        'description' => esc_html__( 'Exclude post from previous module', 'gum-elementor-addon' ),
      ]
    );


    $this->end_controls_section();

    $this->start_controls_section(
      'section_setting',
      [
        'label' => esc_html__( 'Settings', 'elementor' ),
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
        'default' => '1'
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
        'label' => esc_html__( 'Slide', 'gum-elementor-addon' ),
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
        'default'=>['size'=>75,'unit'=>'vh'],
        'size_units' => [ 'px' ,'vh' ],
        'selectors' => [
          '{{WRAPPER}} article' => 'min-height: {{SIZE}}{{UNIT}};',
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
          '{{WRAPPER}} .grid-posts .grid-post' => 'padding-left: calc({{SIZE}}{{UNIT}}/2);padding-right: calc({{SIZE}}{{UNIT}}/2);',
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
              '{{WRAPPER}} .grid-posts .grid-post article' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
          '{{WRAPPER}} .grid-posts .grid-post article' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );
    
    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'post_grid_border',
        'selector' => '{{WRAPPER}} .grid-posts .grid-post article',
      ]
    );

    $this->add_control(
      'post_grid_bdhover',
      [
        'label' => esc_html__( 'Hover Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .grid-posts .grid-post article:hover' => 'border-color: {{VALUE}};',
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
          '{{WRAPPER}} .grid-posts .grid-post article' => 'background-color: {{VALUE}};',
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
          '{{WRAPPER}} .grid-posts .grid-post article:hover' => 'background-color: {{VALUE}};',
        ]
      ]
    );

    $this->end_controls_section();


    $this->start_controls_section(
      'post_list_image',
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
          '{{WRAPPER}} article .blog-image' => 'height: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} article .blog-image img' => 'height: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'image_position' => 'top'
        ],
      ]
    );

    $this->add_responsive_control(
      'post_image_mobileheight',
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
        'devices' => ['mobile'],
        'size_units' => [ 'px' ,'vh' ],
        'selectors' => [
          '{{WRAPPER}} article .blog-image' => 'height: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} article .blog-image img' => 'height: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'image_totop' => 'yes',
          'image_position!' => 'top',
        ],
      ]
    );

    $this->add_responsive_control(
      'post_image_width',
      [
        'label' => esc_html__( 'Width (%)', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          '%' => [
            'max' => 80,
            'step' => 5,
            'min' => 20,
          ],

        ],  
        'default'=>['size'=>50,'unit'=>'%'],
        'size_units' => [ '%' ],
        'selectors' => [
          '{{WRAPPER}} article .post-top' => 'width: {{SIZE}}%;',
          '{{WRAPPER}} article .post-top + .post-content' => 'width: calc( 100% - {{SIZE}}% );',
        ],
        'condition' => [
          'image_position!' => 'top'
        ],
      ]
    );

    $this->add_responsive_control(
      'post_image_margin',
      [
          'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', '%', 'em' ],
          'selectors' => [
              '{{WRAPPER}} article .post-top' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );

    $this->add_responsive_control(
      'post_image_padding',
      [
          'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', '%', 'em' ],
          'selectors' => [
              '{{WRAPPER}} article .post-top' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
          '{{WRAPPER}} article .post-top' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'post_image_border',
        'selector' => '{{WRAPPER}} article .post-top',
      ]
    );


    $this->add_control(
      'post_image_bdhover',
      [
        'label' => esc_html__( 'Hover Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} article:hover .post-top' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'post_image_border_border!' => ''
        ],
      ]
    );


    $this->end_controls_section();

    $this->start_controls_section(
      'post_title_style',
      [
        'label' => esc_html__( 'Content Box', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    


    $this->add_responsive_control(
      'grid_content_align',
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
          '{{WRAPPER}} article .post-content' => 'text-align: {{VALUE}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'grid_content_padding',
      [
          'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', '%', 'em' ],
          'selectors' => [
              '{{WRAPPER}} article .post-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );

    $this->add_control(
      'grid_content_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} article .post-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );


    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'grid_content_border',
        'selector' => '{{WRAPPER}} article .post-content',
      ]
    );



    $this->add_control(
      'grid_content_bdhover',
      [
        'label' => esc_html__( 'Hover Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} article:hover .post-content' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'grid_content_border_border!' => ''
        ],
      ]
    );

    $this->add_control(
      'grid_content_bgcolor',
      [
        'label' => esc_html__( 'Background', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} article .post-content' => 'background-color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'grid_content_bghover',
      [
        'label' => esc_html__( 'Hover Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} article:hover .post-content' => 'background-color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'post_title_heading',
      [
        'label' => esc_html__( 'Post Title', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_post_title',
        'selector' => '{{WRAPPER}} .post-title a',
      ]
    );

    $this->add_control(
      'post_title_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .post-title a' => 'color: {{VALUE}};',
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
          '{{WRAPPER}} .post-title a:hover,{{WRAPPER}} .post-title a:focus' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_responsive_control(
      'post_title_margin',
      [
          'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', '%', 'em' ],
          'selectors' => [
              '{{WRAPPER}} article .post-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );


    $this->add_control(
      'post_date_heading',
      [
        'label' => esc_html__( 'Post Excerpt', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
        'condition' => [
          'show_excerpt' => 'yes',
        ],
      ]
    );


    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_post_content',
        'selector' => '{{WRAPPER}} article .content-excerpt',
        'condition' => [
          'show_excerpt' => 'yes',
        ],
      ]
    );

    $this->add_control(
      'post_content_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} article .content-excerpt' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'show_excerpt' => 'yes',
        ],
      ]
    );


    $this->add_control(
      'post_content_hcolor',
      [
        'label' => esc_html__( 'Hover Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} article:hover .content-excerpt,{{WRAPPER}} article:focus .content-excerpt' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'show_excerpt' => 'yes',
        ],
      ]
    );

    $this->add_responsive_control(
      'post_content_margin',
      [
          'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', '%', 'em' ],
          'selectors' => [
              '{{WRAPPER}} article .content-excerpt' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
          'condition' => [
            'show_excerpt' => 'yes',
          ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'post_meta_style',
      [
        'label' => esc_html__( 'Post Meta', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'show_meta!' => ''
        ],
      ]
    );  


    $this->add_responsive_control(
      'meta_list_space',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'em' => [
            'max' => 10,
            'step'=> 1,
          ],
          'px' => [
            'max' => 2000,
            'step'=> 1,
          ],
        ],  
        'default'=>['size'=>1,'unit'=>'em'],
        'size_units' => [ 'px', 'em' ],
        'selectors' => [
          '{{WRAPPER}} .meta-divider' => 'padding-left: calc({{SIZE}}{{UNIT}}/2);padding-right: calc({{SIZE}}{{UNIT}}/2);',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_meta_title',
        'selector' => '{{WRAPPER}} .list-meta a,{{WRAPPER}} .list-meta .meta-text',
      ]
    );


   $this->start_controls_tabs( 'meta_title_tabs', [] );

   $this->start_controls_tab(
       'meta_title_normal',
       [
           'label' =>esc_html__( 'Normal', 'elementor' ),
       ]
   );


    $this->add_control(
      'meta_title_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta a,{{WRAPPER}} .list-meta .meta-text' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'meta_list_bgcolor',
      [
        'label' => esc_html__( 'Background', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta' => 'background-color: {{VALUE}};',
        ]
      ]
    );

   $this->end_controls_tab();

   $this->start_controls_tab(
       'meta_title_hover',
       [
           'label' =>esc_html__( 'Hover', 'elementor' ),
       ]
   );


    $this->add_control(
      'meta_title_hcolor',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta:hover a,{{WRAPPER}} .list-meta:hover .meta-text' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'meta_list_bghover',
      [
        'label' => esc_html__( 'Background', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta:hover' => 'background-color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'meta_list_bdhover',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta:hover' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'meta_list_border_border!' => ''
        ],
      ]
    );


   $this->end_controls_tab();
   $this->end_controls_tabs();


    $this->add_responsive_control(
        'meta_list_padding',
        [
            'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} .list-meta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_control(
      'meta_list_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .list-meta' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'meta_list_border',
        'selector' => '{{WRAPPER}} .list-meta',
      ]
    );


    $this->add_control(
      'divider_heading',
      [
        'label' => esc_html__( 'Divider', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
        'condition' => [
          'meta_divider!' => ''
        ],
      ]
    );

    $this->add_responsive_control(
      'divider_size',
      [
        'label' => esc_html__( 'Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'em' => [
            'max' => 10,
            'step'=>1
          ],
          'px' => [
            'max' => 200,
            'step'=>1
          ],
        ],  
        'default'=>['size'=>1,'unit'=>'em'],
        'size_units' => [ 'px', 'em' ],
        'selectors' => [
          '{{WRAPPER}} .meta-divider span' => 'font-size: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'meta_divider!' => ''
        ],
      ]
    );

    $this->add_control(
      'divider_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .meta-divider span' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'meta_divider!' => ''
        ],
      ]
    );

    $this->add_responsive_control(
        'divider_padding',
        [
            'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default' => [],
            'selectors' => [
                '{{WRAPPER}} .meta-divider' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
              'meta_divider!' => ''
            ],
        ]
    );


    $this->end_controls_section();

    $this->start_controls_section(
      'post_readmore_style',
      [
        'label' => esc_html__( 'Readmore', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'show_readmore!' => ''
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
          'full' => [
            'title' => esc_html__( 'Full Width', 'gum-elementor-addon' ),
            'icon' => 'eicon-text-align-justify',
          ]
        ],
        'default' => '',
        'condition' => ['show_readmore[value]' => 'yes']
      ]
    );

    $this->add_control(
      'readmore_icon_indent',
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
        'condition' => ['readmore_icon[value]!' => ''],
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

    $posts_per_page = isset($posts_per_page) && $posts_per_page!='' ? $posts_per_page : 10;
    $paged = get_query_var('paged');

    $widget_id=  substr( $this->get_id_int(), 0, 3 );

    if($show_meta == 'yes'){

      $divider = '';

      if($meta_divider == 'text'){
        $divider = '<span>'.esc_html($divider_text).'</span>';

      }elseif($meta_divider == 'icon'){
        ob_start();
        
        Icons_Manager::render_icon( $divider_icon, ['aria-hidden' => 'true'],'span' );

        $divider = ob_get_clean();
      }

      $settings['divider'] = $divider;

    }

    if(! $paged ){
      global $wp_query;
      $paged = $wp_query->get('page');
    }

    $ignore_sticky_posts = ( $filter_by!='sticky' && $ignore_sticky === 'yes') ? true : false;

    $query_params= array(
      'posts_per_page' => absint($posts_per_page),
      'no_found_rows' => false,
      'post_status' => 'publish',
      'post_type'=>'post',
      'paged' => $paged,
      'ignore_sticky_posts' => $ignore_sticky_posts
    );

    if( $posts_offset && $posts_offset > 0 ){
      $query_params['offset'] = absint( $posts_offset );
    }

    if( $skip_previous == 'yes'){
      $excludes =  $this->get_previous_blog();
      $query_params['post__not_in'] = $excludes;
    }

    switch ($filter_by) {
      case 'category':
            if($cat_ids !=''){
              $query_params['cat']= trim($cat_ids);
            }
        break;      
      case 'tag':
            if($tags_ids !=''){

              $query_params['tax_query'] = array(
                array(
                  'taxonomy' => 'post_tag',
                  'field' => 'id',
                  'terms' => array( $tags_ids ),
                  'operator' => 'IN'
                )
              );
            }
        break;
      case 'ids':
            if($posts_ids !=''){
              $query_params['post__in']=explode(",",$posts_ids);
            }
        break;
      case 'sticky':
              $query_params['ignore_sticky_posts']= false;
              $sticky_posts = get_option('sticky_posts');

              if( isset($query_params['post__not_in']) && !empty( $query_params['post__not_in'] )){
                $excludes_post = $query_params['post__not_in'];

                foreach ($sticky_posts as $k => $value) {
                  if(in_array($value, $excludes_post)) unset($sticky_posts[$k]);
                }
              }

              $query_params['post__in']= count( $sticky_posts ) ? $sticky_posts : array(0);

        break;
      default:
        break;
    }

    $post_query = new WP_Query($query_params);

    if (is_wp_error($post_query) || !$post_query->have_posts()) {
      return '';
    }

    $rows_html  = array();

    while ( $post_query->have_posts() ) : 

     $post_query->the_post();
     $rows_html[] = $this->get_post($settings);

    endwhile;

    wp_reset_postdata();

    $make_carousel = ($posts_per_page > $grid_layout) && (count($rows_html) > $grid_layout ) ? true : false;

    $col_class = $make_carousel ? 'slide-item grid-post grid-col-1 image-position-'.sanitize_html_class($image_position) : 'slide-item grid-post grid-col-'.absint($grid_layout).' image-position-'.sanitize_html_class($image_position);

    if($image_totop === 'yes'){
      $col_class.=' mobile-force-ontop';
    }

    echo '<div id="mod_'.esc_attr($widget_id).'" class="owl-carousel-container">';
    echo '<div class="grid-posts'.($make_carousel ? ' owl-carousel':'').'"><div class="'.esc_attr($col_class).'">'.join('</div><div class="'.esc_attr($col_class).'">',$rows_html).'</div></div>';

   if($make_carousel && $slide_navigation === 'arrow'){
     print $this->get_carousel_navigation($settings);
   }

   echo '</div>';

   if( $make_carousel){
     $this->render_carousel_script($widget_id,$settings);
   }

  }


  protected function get_post($settings = array()) {

    if(!isset( $settings ) || empty( $settings )){
      $settings = $this->get_settings_for_display();
    }

    extract( $settings );      

    $post_id = get_the_ID();
    $post_title = get_the_title();

    $this->add_toprevious_blog( $post_id );

    $hide_content = true; $post_content = '';

    if(!isset($show_excerpt) || $show_excerpt =='yes'){

      $hide_content = false;
      $post_content = strip_shortcodes( get_the_excerpt() );
      if($post_content_word !='' && $post_content_word > 0){
        $post_content = wp_trim_words($post_content ,  absint($post_content_word) , $post_content_sufix );
      }

    }


    $post_url = get_the_permalink();

    $thumb_id = get_post_thumbnail_id( $post_id );
    $image = ['id' => $thumb_id ];
    $settings['thumbnail'] = $image;

    $divider = isset( $settings['divider'] ) ? $settings['divider'] : '';
    $settings['post_url'] = $post_url;

    $image_url = Group_Control_Image_Size::get_attachment_image_src( $thumb_id , 'thumbnail', $settings);

    if ( ! empty( $image_url ) ) {
      $image_html = sprintf( '<img src="%s" title="%s" alt="%s" />', esc_attr( $image_url ), Control_Media::get_image_title( $thumb_id ), Control_Media::get_image_alt( $thumb_id ) );
    }

    $allowed_tags = array('h1','h2','h3','h4','h5','h6','div');
    $tag_title = (in_array( $tag, $allowed_tags )) ? trim( $tag ): 'h4';

    ob_start();
?>
<article id="post-<?php esc_attr_e($post_id); ?>" <?php post_class(); ?>>
    <?php if($image_url!=''):?>
  <div class="post-top">
    <div class="blog-image" style="background-image: url('<?php esc_attr_e($image_url); ?>');"><?php print $image_html;?></div>
  </div>
    <?php endif;?>
    <div class="post-content">
      <?php $this->get_post_meta( $settings , 'top', $divider) ?>
      <?php printf( '<%s class="post-title"><a href="%s">%s</a></%s>',$tag_title, get_the_permalink( $post_id ),esc_html( $post_title ), $tag_title);?>
      <?php $this->get_post_meta( $settings , 'mid', $divider); ?>      
      <?php if(!$hide_content){ printf( '<div class="content-excerpt clearfix">%s</div>', $post_content); } ?>
      <?php $this->get_post_meta( $settings , 'bottom', $divider); ?>
      <?php $this->get_readmore_button( 'post-'.$post_id,  $settings ); ?>
  </div>
</article>
<?php 

    return ob_get_clean();
  }

  protected function get_post_meta($settings = array(), $position='', $spacer='') {

    if(!isset($settings['show_meta']) || $settings['show_meta']!='yes' || empty($position) ) return '';

    $post_id = get_the_ID();

    $metas = array( 'date_meta','author_meta','category_meta' );
    $rows_html  = array();

    foreach ($metas as $meta) {
      if( isset( $settings[$meta] ) && $position == $settings[$meta] ){

        $meta_url = '';
        $meta_type = '';


        switch ($meta) {
          case 'date_meta':
            $meta_type = get_the_date();
            break;
          case 'author_meta':

            $author_id = get_post_field( 'post_author', $post_id );
            $meta_type = get_the_author_meta('nickname', $author_id);
            $meta_url = get_the_author_meta('url',$author_id);

            break;
          case 'category_meta':
            $categories = get_the_category($post_id);

            if($categories){
              $category = $categories[0];

              $meta_type = $category->name;
              $meta_url = get_category_link( $category->term_id );
            }
            break;
          default:
            $meta_type = '';
            break;
        }

        if($meta_type!=''){
            $rows_html[] = '<li class="list-meta">'. ( $meta_url !='' ? sprintf('<a href="%s"><span class="meta-text">%s</span></a>', $meta_url, $meta_type) : sprintf('<span class="meta-text">%s</span>',$meta_type) ).'</li>';
        }

      }


    }

    echo '<ul class="meta-position-'.esc_attr($position).' posts-meta">'.join('<li class="meta-divider">'.$spacer.'</li>',$rows_html).'</ul>';

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

    ?><div class="elementor-button-wrap<?php print ' button-align-'.esc_attr($settings['readmore_button_align']);?>"><a <?php echo $this->get_render_attribute_string( 'button-'.$index ); ?>>
          <span class="elementor-button-content-wrapper">
      <?php if ( ! empty( $settings['readmore_icon']['value'] ) ) : ?>
      <span <?php echo $this->get_render_attribute_string( 'readmore_icon_align' ); ?>>
          <?php Icons_Manager::render_icon( $settings['readmore_icon'], [ 'aria-hidden' => 'true' ] ); ?>
      </span>
      <?php endif; ?>
      <span <?php echo $this->get_render_attribute_string( $index );?>><?php esc_html_e($settings['readmore_label']); ?></span>
    </span>
  </a></div><?php

  }

  public function get_previous_blog(  ) {

    global $gum_helper;

    if(!isset($gum_helper) || !isset( $gum_helper['blog_ids'] )){
      $gum_helper['blog_ids'] = array();
    }

    return $gum_helper['blog_ids'];

  } 


  public function add_toprevious_blog( $ids ) {

    global $gum_helper;

    $previous_blog = $this->get_previous_blog();

    if( is_array($ids) ){
      $previous_blog = array_merge( $previous_blog, $ids );
    }
    else{
      array_push($previous_blog, $ids );
    }

    $gum_helper['blog_ids'] = array_unique( $previous_blog );

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
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Gum_Elementor_Widget_post_slider() );

?>