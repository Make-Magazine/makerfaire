<?php
namespace Elementor;
/**
 * @package     WordPress
 * @subpackage  Gum Elementor Addon
 * @author      support@themegum.com
 * @since       1.2.0h
*/
defined('ABSPATH') or die();

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

use WP_Query;
use Gum_Elementor_Helper;

class Gum_Elementor_Widget_blog_grid extends Widget_Base {

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
    return 'gum_posts_grid';
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

    return esc_html__( 'Blog Grid', 'gum-elementor-addon' );
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
    return 'fas fa-xs fa-grip-vertical';
  }

  public function get_keywords() {
    return [ 'wordpress', 'widget', 'post', 'recent', 'blog' ];
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
      'section_title',
      [
        'label' => esc_html__( 'Layout', 'gum-elementor-addon' ),
      ]
    );

    $this->add_responsive_control(
      'grid_layout',
      [
        'label' => esc_html__( 'Layout', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          '1' => esc_html__( 'One Column', 'gum-elementor-addon' ),
          '2' => esc_html__( 'Two Column', 'gum-elementor-addon' ),
          '3' => esc_html__( 'Three Column', 'gum-elementor-addon' ),
          '4' => esc_html__( 'Four Column', 'gum-elementor-addon' ),
        ],
        'default' => '3',
        'mobile_default' => '1',
        'prefix_class' => 'post-grid-col%s-',
      ]
    );


  $this->add_control(
    'show_image',
    [
      'label' => esc_html__( 'Show Featured Image', 'gum-elementor-addon' ),
      'type' => Controls_Manager::SWITCHER,
      'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
      'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
      'default' => 'yes',
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
        'condition' => [
          'show_image' => 'yes',
        ],
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
        'show_image' => 'yes',
      ],
    ]
  );

    $this->add_group_control(
      Group_Control_Image_Size::get_type(),
      [
        'name' => 'thumbnail', 
        'default' => 'medium',
        'condition' => [
          'show_image' => 'yes',
        ],
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
        'separator' => 'before',
      ]
    );

  $this->add_control(
    'title_position',
    [
      'label' => esc_html__( 'Title Position', 'gum-elementor-addon' ),
      'type' => Controls_Manager::SELECT,
      'options' => [
        'content' => esc_html__( 'Content Box', 'gum-elementor-addon' ),
        'before' => esc_html__( 'Before Image', 'gum-elementor-addon' ),
        'after' => esc_html__( 'After Image', 'gum-elementor-addon' ),
      ],
      'default' => 'content',
      'condition' => [
        'show_image' => 'yes',
      ],
    ]
  );


 $this->add_control(
    'post_title_word',
    [
      'label'     => esc_html__( 'Word Count', 'gum-elementor-addon' ),
      'type'      => Controls_Manager::NUMBER,
      'default'   => '',
    ]
  );

 $this->add_control(
    'post_title_sufix',
    [
      'label'     => esc_html__( 'Suffix', 'gum-elementor-addon' ),
      'type'      => Controls_Manager::TEXT,
      'default'   => '',
      'ai' => [
        'active' => false,
      ],
      'condition' => [
        'post_title_word!' => ''
      ],
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

   $this->end_controls_section();

    $this->start_controls_section(
      'meta_layout',
      [
        'label' => esc_html__( 'Meta', 'gum-elementor-addon' ),
        'condition' => [
          'show_meta!' => ''
        ],
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
        'default' => 'mid'
      ]
    );

    $this->add_control(
      'date_meta_icon',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
        'condition' => [
          'date_meta!' => ''
        ],

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
        'default' => 'mid'
      ]
    );


    $this->add_control(
      'author_meta_icon',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
        'condition' => [
          'author_meta!' => ''
        ],
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
        'default' => 'top'
      ]
    );


    $this->add_control(
      'category_meta_icon',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
        'condition' => [
          'category_meta!' => ''
        ],
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
        'separator' => 'before'
      ]
    );

    $this->add_control(
      'divider_text',
      [
        'label' => esc_html__( 'Text', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'condition' => [
          'meta_divider' => 'text',
        ],
        'default' => '-',
        'ai' => [
          'active' => false,
        ],
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
        'description' => esc_html__( 'Type post IDs. Seperated by comma', 'gum-elementor-addon' ),
        'label_block' => true,
        'ai' => [
          'active' => false,
        ],
        'condition' => [
          'filter_by' => 'ids'
        ],
      ]
    );


    $this->add_control(
      'posts_per_page',
      [
        'label' => esc_html__( 'Post Per Page', 'gum-elementor-addon' ),
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
      'source_filter_heading',
      [
        'label' => esc_html__( 'Filter', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before'
      ]
    );

    $this->add_control(
      'is_featured_image',
      [
        'label' => esc_html__( 'Featured Image Only', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => '',
      ]
    );


    $this->add_control(
      'single_exclude',
      [
        'label' => esc_html__( 'Exclude Current Post?', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => 'yes',
        'description' => esc_html__( 'Select yes if current active post not showing in the listing.', 'gum-elementor-addon' ),
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
      'section_pagination',
      [
        'label' => esc_html__( 'Pagination', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'show_pagination',
      [
        'label' => esc_html__( 'Enable', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => 'yes',
      ]
    );

    $this->add_control(
      'pagination_align',
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
          'justify' => [
            'title' => esc_html__( 'Full Width', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-stretch',
          ],
          'right' => [
            'title' => esc_html__( 'Right', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-right',
          ],
        ],
        'default' => 'center',
        'prefix_class' => 'pagination-',
        'condition' => ['show_pagination!' => '']
      ]
    );

    $this->add_control(
      'pagination_type',
      [
        'label' => esc_html__( 'Layout', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'paging' => esc_html__( 'Paging', 'gum-elementor-addon' ),
          'arrow' => esc_html__( 'Previous / Next', 'gum-elementor-addon' ),
        ],
        'default' => 'paging',
        'condition' => [
          'show_pagination!' => ''
        ],
      ]
    );


    $this->add_control(
      'pagination_baseurl',
      [
        'label' => esc_html__( 'Base URL', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          '' => esc_html__( 'This Page ( default )', 'gum-elementor-addon' ),
          'blog' => esc_html__( 'Blog Page', 'gum-elementor-addon' ),
          'custom' => esc_html__( 'Custom', 'gum-elementor-addon' ),
        ],
        'default' => '',
        'condition' => [
          'show_pagination!' => ''
        ],
      ]
    );


    $this->add_control(
      'custom_baseurl',
      [
        'label' => esc_html__( 'Link', 'gum-elementor-addon' ),
        'type' => Controls_Manager::URL,
        'dynamic' => [
          'active' => true,
        ],
        'default' => [
          'url' => '',
        ],
        'description' => esc_html__( 'Select the page url for next page.', 'gum-elementor-addon' ),
        'condition' => ['show_pagination!' => '', 'pagination_baseurl' => 'custom']
      ]
    );



    $this->end_controls_section();


/*
 * style params
 */

    $this->start_controls_section(
      'post_grid_style',
      [
        'label' => esc_html__( 'Grid Style', 'gum-elementor-addon' ),
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
        'default'=>['size'=>'','unit'=>'px'],
        'size_units' => [ 'px' ,'vh' ],
        'selectors' => [
          '{{WRAPPER}} article' => 'min-height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'post_grid_gutter',
      [
        'label' => esc_html__( 'Horizontal Gutter', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
         'px' => [
            'max' => 1000,
          ],
        ],  
        'default'=>['size'=>'30','unit'=>'px'],
        'size_units' => [ 'px' ],
        'selectors' => [
          '{{WRAPPER}} .grid-posts .grid-post:not(.grid-col-1)' => 'padding-left: calc({{SIZE}}{{UNIT}}/2);padding-right: calc({{SIZE}}{{UNIT}}/2);',
          '{{WRAPPER}} .grid-posts .grid-post' => 'padding-bottom: {{SIZE}}{{UNIT}};',
        ]
       ]
    );


    $this->add_responsive_control(
      'post_grid_vgutter',
      [
        'label' => esc_html__( 'Vertical Gutter', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
         'px' => [
            'max' => 1000,
          ],
        ],  
        'default'=>  array(),
        'size_units' => [ 'px' ],
        'selectors' => [
          '{{WRAPPER}} .grid-posts .grid-post' => 'padding-bottom: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .grid-posts' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
        ]
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

    
    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'post_grid_border',
        'selector' => '{{WRAPPER}} .grid-posts .grid-post article',
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




   $this->start_controls_tabs( 'post_grid_tabs', [] );

   $this->start_controls_tab(
       'post_grid_tab_normal',
       [
           'label' =>esc_html__( 'Normal', 'gum-elementor-addon' ),
       ]
   );


    $background_options =  $this->grid_background_types();

    $this->add_control(
      'post_grid_background_type',
      [
        'label' => esc_html__( 'Background Type', 'elementor' ),
        'type' => Controls_Manager::CHOOSE,
        'render_type' => 'ui',
        'options'=> $background_options,
        'default' => 'classic',
      ]
    );

    $this->add_control(
      'post_grid_background',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .grid-posts .grid-post article' => 'background-color: {{VALUE}};',
        ],
        'condition' => [
          'post_grid_background_type!' => '',
        ],
      ]
    );

    $this->add_control(
      'post_grid_color_stop',
      [
        'label' => esc_html__( 'Location', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ '%', 'custom' ],
        'default' => [
          'unit' => '%',
          'size' => 0,
        ],
        'range' => [
          '%' => [
            'min' => -100,
            'max' => 200,
          ],
        ],
        'render_type' => 'ui',
        'condition' => [
          'post_grid_background_type' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'post_grid_color_b',
      [
        'label' => esc_html__( 'Second Color', 'elementor' ),
        'type' => Controls_Manager::COLOR,
        'default' => '#f2295b',
        'render_type' => 'ui',
        'condition' => [
          'post_grid_background_type' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'post_grid_color_b_stop',
      [
        'label' => esc_html__( 'Location', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ '%', 'custom' ],
        'default' => [
          'unit' => '%',
          'size' => 100,
        ],
        'range' => [
          '%' => [
            'min' => -100,
            'max' => 200,
          ],
        ],
        'render_type' => 'ui',
        'condition' => [
          'post_grid_background_type' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'post_grid_gradient_type',
      [
        'label' => esc_html__( 'Type', 'elementor' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'linear' => esc_html__( 'Linear', 'elementor' ),
          'radial' => esc_html__( 'Radial', 'elementor' ),
        ],
        'default' => 'linear',
        'render_type' => 'ui',
        'condition' => [
          'post_grid_background_type' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'post_grid_gradient_angle',
      [
        'label' => esc_html__( 'Angle', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'deg', 'grad', 'rad', 'turn', 'custom' ],
        'default' => [
          'unit' => 'deg',
          'size' => 180,
        ],
        'selectors' => [
          '{{WRAPPER}} .grid-posts .grid-post article' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{post_grid_background.VALUE}} {{post_grid_color_stop.SIZE}}{{post_grid_color_stop.UNIT}}, {{post_grid_color_b.VALUE}} {{post_grid_color_b_stop.SIZE}}{{post_grid_color_b_stop.UNIT}})',
        ],
        'condition' => [
          'post_grid_background_type' => [ 'gradient' ],
          'post_grid_gradient_type' => 'linear',
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'post_grid_gradient_position',
      [
        'label' => esc_html__( 'Position', 'elementor' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'center center' => esc_html__( 'Center Center', 'elementor' ),
          'center left' => esc_html__( 'Center Left', 'elementor' ),
          'center right' => esc_html__( 'Center Right', 'elementor' ),
          'top center' => esc_html__( 'Top Center', 'elementor' ),
          'top left' => esc_html__( 'Top Left', 'elementor' ),
          'top right' => esc_html__( 'Top Right', 'elementor' ),
          'bottom center' => esc_html__( 'Bottom Center', 'elementor' ),
          'bottom left' => esc_html__( 'Bottom Left', 'elementor' ),
          'bottom right' => esc_html__( 'Bottom Right', 'elementor' ),
        ],
        'default' => 'center center',
        'selectors' => [
          '{{WRAPPER}} .grid-posts .grid-post article' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{post_grid_background.VALUE}} {{post_grid_color_stop.SIZE}}{{post_grid_color_stop.UNIT}}, {{post_grid_color_b.VALUE}} {{post_grid_color_b_stop.SIZE}}{{post_grid_color_b_stop.UNIT}})',
        ],
        'condition' => [
          'post_grid_background_type' => [ 'gradient' ],
          'post_grid_gradient_type' => 'radial',
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_group_control(
      Group_Control_Box_Shadow::get_type(),
      [
        'name' => 'post_grid_shadow',
        'selector' => '{{WRAPPER}} .grid-posts .grid-post article',
      ]
    );


   $this->end_controls_tab();

   $this->start_controls_tab(
       'post_grid_tab_hover',
       [
           'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
       ]
   );

    $this->add_control(
      'post_grid_bdhover',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
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
      'post_grid_bghover_type',
      [
        'label' => esc_html__( 'Background Type', 'elementor' ),
        'type' => Controls_Manager::CHOOSE,
        'render_type' => 'ui',
        'options'=> $background_options,
        'default' => 'classic',
      ]
    );

    $this->add_control(
      'post_grid_bghover',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .grid-posts .grid-post article:hover' => 'background-color: {{VALUE}};background-image: none;',
        ],
        'condition' => [
          'post_grid_bghover_type!' => '',
        ],
      ]
    );

   $this->add_control(
      'post_grid_bghover_stop',
      [
        'label' => esc_html__( 'Location', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ '%', 'custom' ],
        'default' => [
          'unit' => '%',
          'size' => 0,
        ],
        'range' => [
          '%' => [
            'min' => -100,
            'max' => 200,
          ],
        ],
        'render_type' => 'ui',
        'condition' => [
          'post_grid_bghover_type' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'post_grid_bghover_color_b',
      [
        'label' => esc_html__( 'Second Color', 'elementor' ),
        'type' => Controls_Manager::COLOR,
        'default' => '#f2295b',
        'render_type' => 'ui',
        'condition' => [
          'post_grid_bghover_type' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'post_grid_bghover_b_stop',
      [
        'label' => esc_html__( 'Location', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ '%', 'custom' ],
        'default' => [
          'unit' => '%',
          'size' => 100,
        ],
        'range' => [
          '%' => [
            'min' => -100,
            'max' => 200,
          ],
        ],
        'render_type' => 'ui',
        'condition' => [
          'post_grid_bghover_type' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'post_grid_bghover_gradient_type',
      [
        'label' => esc_html__( 'Type', 'elementor' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'linear' => esc_html__( 'Linear', 'elementor' ),
          'radial' => esc_html__( 'Radial', 'elementor' ),
        ],
        'default' => 'linear',
        'render_type' => 'ui',
        'condition' => [
          'post_grid_bghover_type' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'post_grid_bghover_gradient_angle',
      [
        'label' => esc_html__( 'Angle', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'deg', 'grad', 'rad', 'turn', 'custom' ],
        'default' => [
          'unit' => 'deg',
          'size' => 180,
        ],
        'selectors' => [
          '{{WRAPPER}} .grid-posts .grid-post article:hover' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{post_grid_bghover.VALUE}} {{post_grid_bghover_stop.SIZE}}{{post_grid_bghover_stop.UNIT}}, {{post_grid_bghover_color_b.VALUE}} {{post_grid_bghover_b_stop.SIZE}}{{post_grid_bghover_b_stop.UNIT}});',
        ],
        'condition' => [
          'post_grid_bghover_type' => [ 'gradient' ],
          'post_grid_bghover_gradient_type' => 'linear',
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'post_grid_bghover_gradient_position',
      [
        'label' => esc_html__( 'Position', 'elementor' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'center center' => esc_html__( 'Center Center', 'elementor' ),
          'center left' => esc_html__( 'Center Left', 'elementor' ),
          'center right' => esc_html__( 'Center Right', 'elementor' ),
          'top center' => esc_html__( 'Top Center', 'elementor' ),
          'top left' => esc_html__( 'Top Left', 'elementor' ),
          'top right' => esc_html__( 'Top Right', 'elementor' ),
          'bottom center' => esc_html__( 'Bottom Center', 'elementor' ),
          'bottom left' => esc_html__( 'Bottom Left', 'elementor' ),
          'bottom right' => esc_html__( 'Bottom Right', 'elementor' ),
        ],
        'default' => 'center center',
        'selectors' => [
          '{{WRAPPER}} .grid-posts .grid-post article:hover' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{post_grid_bghover.VALUE}} {{post_grid_bghover_stop.SIZE}}{{post_grid_bghover_stop.UNIT}}, {{post_grid_bghover_color_b.VALUE}} {{post_grid_bghover_b_stop.SIZE}}{{post_grid_bghover_b_stop.UNIT}})',
        ],
        'condition' => [
          'post_grid_bghover_type' => [ 'gradient' ],
          'post_grid_bghover_gradient_type' => 'radial',
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_group_control(
      Group_Control_Box_Shadow::get_type(),
      [
        'name' => 'post_grid_hover_shadow',
        'selector' => '{{WRAPPER}}:hover .grid-posts .grid-post article',
      ]
    );


    $this->add_control(
      'post_grid_hover_transition',
      [
        'label' => esc_html__( 'Transition Duration', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 0.3,
        ],
        'range' => [
          'px' => [
            'max' => 3,
            'step' => 0.1,
          ],
        ],
        'render_type' => 'ui',
        'separator' => 'before',
        'selectors' => [
          '{{WRAPPER}} .grid-posts .grid-post article' => 'transition: background {{SIZE}}s'
        ],
      ]
    );

   $this->end_controls_tab();
   $this->end_controls_tabs();


    $this->end_controls_section();

    $this->start_controls_section(
      'post_list_image',
      [
        'label' => esc_html__( 'Image Box', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'show_image' => 'yes',
        ],
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

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'post_image_border',
        'selector' => '{{WRAPPER}} article .blog-image',
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


    $this->add_control(
      'post_image_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} article .blog-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );


    $this->add_group_control(
      Group_Control_Box_Shadow::get_type(),
      [
        'name' => 'post_image_shadow',
        'selector' => '{{WRAPPER}} article .blog-image',
      ]
    );
    


    $this->add_responsive_control(
      'post_image_padding',
      [
          'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', '%', 'em' ],
          'selectors' => [
              '{{WRAPPER}} article .blog-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
          'separator' => 'before'
      ]
    );

    $this->add_responsive_control(
      'post_image_margin',
      [
          'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', '%', 'em' ],
          'selectors' => [
              '{{WRAPPER}} article .blog-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'grid_content_heading',
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

   $this->start_controls_tabs( 'grid_content_tabs', [] );

   $this->start_controls_tab(
       'grid_content_normal',
       [
           'label' =>esc_html__( 'Normal', 'gum-elementor-addon' ),
       ]
   );

    $this->add_control(
      'grid_content_bgcolor_type',
      [
        'label' => esc_html_x( 'Background Type', 'Background Control', 'elementor' ),
        'type' => Controls_Manager::CHOOSE,
        'render_type' => 'ui',
        'options'=> $background_options,
        'default' => 'classic',
      ]
    );

    $this->add_control(
      'grid_content_bgcolor',
      [
        'label' => esc_html__( 'Background Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} article .post-content' => 'background-color: {{VALUE}};',
        ],
        'condition' => [
          'grid_content_bgcolor_type!' => '',
        ],
      ]
    );

    $this->add_control(
      'grid_content_bgcolor_stop',
      [
        'label' => esc_html__( 'Location', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ '%', 'custom' ],
        'default' => [
          'unit' => '%',
          'size' => 0,
        ],
        'range' => [
          '%' => [
            'min' => -100,
            'max' => 200,
          ],
        ],
        'render_type' => 'ui',
        'condition' => [
          'grid_content_bgcolor_type' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'grid_content_bgcolor_b',
      [
        'label' => esc_html__( 'Second Color', 'elementor' ),
        'type' => Controls_Manager::COLOR,
        'default' => '#f2295b',
        'render_type' => 'ui',
        'condition' => [
          'grid_content_bgcolor_type' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'grid_content_bgcolor_b_stop',
      [
        'label' => esc_html__( 'Location', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ '%', 'custom' ],
        'default' => [
          'unit' => '%',
          'size' => 100,
        ],
        'range' => [
          '%' => [
            'min' => -100,
            'max' => 200,
          ],
        ],
        'render_type' => 'ui',
        'condition' => [
          'grid_content_bgcolor_type' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'grid_content_bgcolor_gradient_type',
      [
        'label' => esc_html__( 'Type', 'elementor' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'linear' => esc_html__( 'Linear', 'elementor' ),
          'radial' => esc_html__( 'Radial', 'elementor' ),
        ],
        'default' => 'linear',
        'render_type' => 'ui',
        'condition' => [
          'grid_content_bgcolor_type' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'grid_content_bgcolor_gradient_angle',
      [
        'label' => esc_html__( 'Angle', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'deg', 'grad', 'rad', 'turn', 'custom' ],
        'default' => [
          'unit' => 'deg',
          'size' => 180,
        ],
        'selectors' => [
          '{{WRAPPER}} article .post-content' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{grid_content_bgcolor.VALUE}} {{grid_content_bgcolor_stop.SIZE}}{{grid_content_bgcolor_stop.UNIT}}, {{grid_content_bgcolor_b.VALUE}} {{grid_content_bgcolor_b_stop.SIZE}}{{grid_content_bgcolor_b_stop.UNIT}})',
        ],
        'condition' => [
          'grid_content_bgcolor_type' => [ 'gradient' ],
          'grid_content_bgcolor_gradient_type' => 'linear',
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'grid_content_bgcolor_gradient_position',
      [
        'label' => esc_html__( 'Position', 'elementor' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'center center' => esc_html__( 'Center Center', 'elementor' ),
          'center left' => esc_html__( 'Center Left', 'elementor' ),
          'center right' => esc_html__( 'Center Right', 'elementor' ),
          'top center' => esc_html__( 'Top Center', 'elementor' ),
          'top left' => esc_html__( 'Top Left', 'elementor' ),
          'top right' => esc_html__( 'Top Right', 'elementor' ),
          'bottom center' => esc_html__( 'Bottom Center', 'elementor' ),
          'bottom left' => esc_html__( 'Bottom Left', 'elementor' ),
          'bottom right' => esc_html__( 'Bottom Right', 'elementor' ),
        ],
        'default' => 'center center',
        'selectors' => [
          '{{WRAPPER}} article .post-content' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{grid_content_bgcolor.VALUE}} {{grid_content_bgcolor_stop.SIZE}}{{grid_content_bgcolor_stop.UNIT}}, {{grid_content_bgcolor_b.VALUE}} {{grid_content_bgcolor_b_stop.SIZE}}{{grid_content_bgcolor_b_stop.UNIT}})',
        ],
        'condition' => [
          'grid_content_bgcolor_type' => [ 'gradient' ],
          'grid_content_bgcolor_gradient_type' => 'radial',
        ],
        'of_type' => 'gradient',
      ]
    );


   $this->end_controls_tab();

   $this->start_controls_tab(
       'grid_content_hover',
       [
           'label' => esc_html__( 'Hover', 'gum-elementor-addon' ),
       ]
   );

    $this->add_control(
      'grid_content_bdhover',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
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
      'grid_content_bghover_type',
      [
        'label' => esc_html_x( 'Background Type', 'Background Control', 'elementor' ),
        'type' => Controls_Manager::CHOOSE,
        'render_type' => 'ui',
        'options'=> $background_options,
        'default' => 'classic',
      ]
    );


    $this->add_control(
      'grid_content_bghover',
      [
        'label' => esc_html__( 'Background Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} article:hover .post-content' => 'background-color: {{VALUE}};background-image: none;',
        ],
        'condition' => [
          'grid_content_bghover_type!' => '',
        ],
      ]
    );


   $this->add_control(
      'grid_content_bghover_stop',
      [
        'label' => esc_html__( 'Location', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ '%', 'custom' ],
        'default' => [
          'unit' => '%',
          'size' => 0,
        ],
        'range' => [
          '%' => [
            'min' => -100,
            'max' => 200,
          ],
        ],
        'render_type' => 'ui',
        'condition' => [
          'grid_content_bghover_type' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'grid_content_bghover_b',
      [
        'label' => esc_html__( 'Second Color', 'elementor' ),
        'type' => Controls_Manager::COLOR,
        'default' => '#f2295b',
        'render_type' => 'ui',
        'condition' => [
          'grid_content_bghover_type' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'grid_content_bghover_b_stop',
      [
        'label' => esc_html__( 'Location', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ '%', 'custom' ],
        'default' => [
          'unit' => '%',
          'size' => 100,
        ],
        'range' => [
          '%' => [
            'min' => -100,
            'max' => 200,
          ],
        ],
        'render_type' => 'ui',
        'condition' => [
          'grid_content_bghover_type' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'grid_content_bghover_gradient_type',
      [
        'label' => esc_html__( 'Type', 'elementor' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'linear' => esc_html__( 'Linear', 'elementor' ),
          'radial' => esc_html__( 'Radial', 'elementor' ),
        ],
        'default' => 'linear',
        'render_type' => 'ui',
        'condition' => [
          'grid_content_bghover_type' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'grid_content_bghover_gradient_angle',
      [
        'label' => esc_html__( 'Angle', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'deg', 'grad', 'rad', 'turn', 'custom' ],
        'default' => [
          'unit' => 'deg',
          'size' => 180,
        ],
        'selectors' => [
          '{{WRAPPER}} article:hover .post-content' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{grid_content_bghover.VALUE}} {{grid_content_bghover_stop.SIZE}}{{grid_content_bghover_stop.UNIT}}, {{grid_content_bghover_b.VALUE}} {{grid_content_bghover_b_stop.SIZE}}{{grid_content_bghover_b_stop.UNIT}})',
        ],
        'condition' => [
          'grid_content_bghover_type' => [ 'gradient' ],
          'grid_content_bghover_gradient_type' => 'linear',
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'grid_content_bghover_gradient_position',
      [
        'label' => esc_html__( 'Position', 'elementor' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'center center' => esc_html__( 'Center Center', 'elementor' ),
          'center left' => esc_html__( 'Center Left', 'elementor' ),
          'center right' => esc_html__( 'Center Right', 'elementor' ),
          'top center' => esc_html__( 'Top Center', 'elementor' ),
          'top left' => esc_html__( 'Top Left', 'elementor' ),
          'top right' => esc_html__( 'Top Right', 'elementor' ),
          'bottom center' => esc_html__( 'Bottom Center', 'elementor' ),
          'bottom left' => esc_html__( 'Bottom Left', 'elementor' ),
          'bottom right' => esc_html__( 'Bottom Right', 'elementor' ),
        ],
        'default' => 'center center',
        'selectors' => [
          '{{WRAPPER}} article:hover .post-content' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{grid_content_bghover.VALUE}} {{grid_content_bghover_stop.SIZE}}{{grid_content_bghover_stop.UNIT}}, {{grid_content_bghover_b.VALUE}} {{grid_content_bghover_b_stop.SIZE}}{{grid_content_bghover_b_stop.UNIT}})',
        ],
        'condition' => [
          'grid_content_bghover_type' => [ 'gradient' ],
          'grid_content_bghover_gradient_type' => 'radial',
        ],
        'of_type' => 'gradient',
      ]
    );

    $this->add_control(
      'grid_content_hover_transition',
      [
        'label' => esc_html__( 'Transition Duration', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 0.3,
        ],
        'range' => [
          'px' => [
            'max' => 3,
            'step' => 0.1,
          ],
        ],
        'render_type' => 'ui',
        'separator' => 'before',
        'selectors' => [
          '{{WRAPPER}} article .post-content' => 'transition: background {{SIZE}}s'
        ],
      ]
    );

   $this->end_controls_tab();
   $this->end_controls_tabs();


    $this->end_controls_section();

    $this->start_controls_section(
      'post_title_heading',
      [
        'label' => esc_html__( 'Post Title', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
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
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
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


    $this->end_controls_section();

    $this->start_controls_section(
      'post_content_heading',
      [
        'label' => esc_html__( 'Post Excerpt', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
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
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
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


    $this->add_control(
      'date_meta_style',
      [
        'label' => esc_html__( 'Date Styles', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => '',
        'condition' => [
          'show_meta!' => '',
          'date_meta!' => ''
        ],
      ]
    );

    $this->add_control(
      'author_meta_style',
      [
        'label' => esc_html__( 'Author Styles', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => '',
        'condition' => [
          'show_meta!' => '',
          'author_meta!' => ''
        ],
      ]
    );


    $this->add_control(
      'category_meta_style',
      [
        'label' => esc_html__( 'Category Styles', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => '',
        'condition' => [
          'show_meta!' => '',
          'category_meta!' => ''
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
        'separator' => 'before',
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


    $this->add_control(
      'general_icon_size',
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
          '{{WRAPPER}} .list-meta i' => 'font-size: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .list-meta svg' => 'height: {{SIZE}}%;width: {{SIZE}}%;'
        ],
      ]
    );

    $this->add_control(
      'general_icon_indent',
      [
        'label' => esc_html__( 'Text Indent', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 100,
          ],
        ],
        'default' =>['value'=>'10', 'unit'=>'px'],
        'selectors' => [
          '{{WRAPPER}} .list-meta i + .meta-text,{{WRAPPER}} .list-meta i + a .meta-text' => 'padding-left: {{SIZE}}{{UNIT}};',
        ],
      ]
    );


   $this->start_controls_tabs( 'meta_title_tabs', [] );

   $this->start_controls_tab(
       'meta_title_normal',
       [
           'label' =>esc_html__( 'Normal', 'gum-elementor-addon' ),
       ]
   );


    $this->add_control(
      'meta_title_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta a,{{WRAPPER}} .list-meta .meta-text' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'general_icon_color',
      [
        'label' => esc_html__( 'Icon Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta i, {{WRAPPER}} .list-meta path' => 'fill: {{VALUE}}; color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'meta_list_bgcolor',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
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
           'label' =>esc_html__( 'Hover', 'gum-elementor-addon' ),
       ]
   );


    $this->add_control(
      'meta_title_hcolor',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta:hover a,{{WRAPPER}} .list-meta:hover .meta-text' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'general_icon_hcolor',
      [
        'label' => esc_html__( 'Icon Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta:hover i, {{WRAPPER}} .list-meta:hover path' => 'fill: {{VALUE}}; color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'meta_list_bghover',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
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
        'meta_list_margin',
        [
            'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'placeholder' => [
              'top' => '',
              'right' => '',
              'bottom' => '',
              'left' => '',
            ],
            'selectors' => [
                '{{WRAPPER}} .posts-meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

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
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
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
      'date_meta_styles',
      [
        'label' => esc_html__( 'Date Styles', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'date_meta_style' => 'yes',
          'date_meta!' => '',
          'show_meta!' => ''
        ],
      ]
    );  

    $this->add_control(
      'date_icon_size',
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
          '{{WRAPPER}} .list-meta.date_meta i' => 'font-size: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .list-meta.date_meta svg' => 'height: {{SIZE}}%;width: {{SIZE}}%;'
        ],
        'condition' => ['date_meta_icon[value]!' => ''],
      ]
    );

    $this->add_control(
      'date_icon_indent',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 100,
          ],
        ],
        'default' =>['value'=>'10', 'unit'=>'px'],
        'selectors' => [
          '{{WRAPPER}} .list-meta.date_meta .meta-text' => 'padding-left: {{SIZE}}{{UNIT}};',
        ],
        'condition' => ['date_meta_icon[value]!' => ''],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_datemeta',
        'label' =>'Typography',
        'selector' => '{{WRAPPER}} .list-meta.date_meta a,{{WRAPPER}} .list-meta.date_meta .meta-text',
      ]
    );

   $this->start_controls_tabs( 'datemeta_tabs', [] );

   $this->start_controls_tab(
       'datemeta_normal',
       [
           'label' =>esc_html__( 'Normal', 'gum-elementor-addon' ),
       ]
   );

    $this->add_control(
      'date_meta_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.date_meta a,{{WRAPPER}} .list-meta.date_meta .meta-text' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'date_icon_color',
      [
        'label' => esc_html__( 'Icon Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.date_meta i, {{WRAPPER}} .list-meta.date_meta path' => 'fill: {{VALUE}}; color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'datemeta_bgcolor',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.date_meta' => 'background-color: {{VALUE}};',
        ]
      ]
    );


   $this->end_controls_tab();
   $this->start_controls_tab(
       'datemeta_hover',
       [
           'label' =>esc_html__( 'Hover', 'gum-elementor-addon' ),
       ]
   );


    $this->add_control(
      'datemeta_hcolor',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.date_meta:hover a,{{WRAPPER}} .list-meta.date_meta:hover .meta-text' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'date_icon_hcolor',
      [
        'label' => esc_html__( 'Icon Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.date_meta:hover i, {{WRAPPER}} .list-meta.date_meta:hover path' => 'fill: {{VALUE}}; color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'datemeta_bghover',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.date_meta:hover' => 'background-color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'datemeta_bdhover',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.date_meta:hover' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'datemeta_border_border!' => ''
        ],
      ]
    );

   $this->end_controls_tab();
   $this->end_controls_tabs();

    $this->add_responsive_control(
        'datemeta_margin',
        [
            'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'allowed_dimensions' => 'horizontal',
            'placeholder' => [
              'top' => 0,
              'right' => '',
              'bottom' => 0,
              'left' => '',
            ],
            'selectors' => [
                '{{WRAPPER}} .list-meta.date_meta' => 'margin-left: {{LEFT}}{{UNIT}};margin-right:{{RIGHT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_responsive_control(
        'datemeta_padding',
        [
            'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} .list-meta.date_meta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_control(
      'datemeta_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .list-meta.date_meta' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'datemeta_border',
        'selector' => '{{WRAPPER}} .list-meta.date_meta',
      ]
    );



    $this->end_controls_section();

    $this->start_controls_section(
      'author_meta_styles',
      [
        'label' => esc_html__( 'Author Styles', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'author_meta_style' => 'yes',
          'show_meta!' => '',
          'author_meta!' => ''
        ],
      ]
    );  

    $this->add_control(
      'author_icon_size',
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
          '{{WRAPPER}} .list-meta.author_meta i' => 'font-size: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .list-meta.author_meta svg' => 'height: {{SIZE}}%;width: {{SIZE}}%;'
        ],
        'condition' => ['author_meta_icon[value]!' => ''],
      ]
    );

    $this->add_control(
      'author_icon_indent',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 100,
          ],
        ],
        'default' =>['value'=>'10', 'unit'=>'px'],
        'selectors' => [
          '{{WRAPPER}} .list-meta.author_meta .meta-text' => 'padding-left: {{SIZE}}{{UNIT}};',
        ],
        'condition' => ['author_meta_icon[value]!' => ''],
      ]
    );


    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_authormeta',
        'selector' => '{{WRAPPER}} .list-meta.author_meta a,{{WRAPPER}} .list-meta.author_meta .meta-text',
      ]
    );

   $this->start_controls_tabs( 'authormeta_tabs', [] );
   $this->start_controls_tab(
       'authormeta_normal',
       [
           'label' =>esc_html__( 'Normal', 'gum-elementor-addon' ),
       ]
   );

    $this->add_control(
      'authormeta_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.author_meta a,{{WRAPPER}} .list-meta.author_meta .meta-text' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'author_icon_color',
      [
        'label' => esc_html__( 'Icon Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.author_meta i, {{WRAPPER}} .list-meta.author_meta path' => 'fill: {{VALUE}}; color: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'authormeta_bgcolor',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.author_meta' => 'background-color: {{VALUE}};',
        ]
      ]
    );


   $this->end_controls_tab();
   $this->start_controls_tab(
       'authormeta_hover',
       [
           'label' =>esc_html__( 'Hover', 'gum-elementor-addon' ),
       ]
   );

    $this->add_control(
      'authormeta_hcolor',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.author_meta:hover a,{{WRAPPER}} .list-meta.author_meta:hover .meta-text' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'author_icon_hcolor',
      [
        'label' => esc_html__( 'Icon Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.author_meta:hover i, {{WRAPPER}} .list-meta.author_meta:hover path' => 'fill: {{VALUE}}; color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'authormeta_bghover',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.author_meta:hover' => 'background-color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'authormeta_bdhover',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.author_meta:hover' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'authormeta_border_border!' => ''
        ],
      ]
    );

   $this->end_controls_tab();
   $this->end_controls_tabs();

    $this->add_responsive_control(
        'authormeta_margin',
        [
            'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'allowed_dimensions' => 'horizontal',
            'placeholder' => [
              'top' => 0,
              'right' => '',
              'bottom' => 0,
              'left' => '',
            ],
            'selectors' => [
                '{{WRAPPER}} .list-meta.author_meta' => 'margin-left: {{LEFT}}{{UNIT}};margin-right:{{RIGHT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_responsive_control(
        'authormeta_padding',
        [
            'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} .list-meta.author_meta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_control(
      'authormeta_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .list-meta.author_meta' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'authormeta_border',
        'selector' => '{{WRAPPER}} .list-meta.author_meta',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'category_meta_styles',
      [
        'label' => esc_html__( 'Category Styles', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'category_meta_style' => 'yes',
          'show_meta!' => '',
          'category_meta!' => ''
        ],
      ]
    );  

    $this->add_control(
      'category_icon_size',
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
          '{{WRAPPER}} .list-meta.category_meta i' => 'font-size: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .list-meta.category_meta svg' => 'height: {{SIZE}}%;width: {{SIZE}}%;'
        ],
        'condition' => ['category_meta_icon[value]!' => ''],
      ]
    );

    $this->add_control(
      'category_icon_indent',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 100,
          ],
        ],
        'default' =>['value'=>'10', 'unit'=>'px'],
        'selectors' => [
          '{{WRAPPER}} .list-meta.category_meta .meta-text' => 'padding-left: {{SIZE}}{{UNIT}};',
        ],
        'condition' => ['category_meta_icon[value]!' => ''],
      ]
    );


    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_categorymeta',
        'selector' => '{{WRAPPER}} .list-meta.category_meta a,{{WRAPPER}} .list-meta.category_meta .meta-text',
      ]
    );


   $this->start_controls_tabs( 'categorymeta_tabs', [] );
   $this->start_controls_tab(
       'categorymeta_normal',
       [
           'label' =>esc_html__( 'Normal', 'gum-elementor-addon' ),
       ]
   );

    $this->add_control(
      'categorymeta_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.category_meta a,{{WRAPPER}} .list-meta.category_meta .meta-text' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'category_icon_color',
      [
        'label' => esc_html__( 'Icon Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.category_meta i, {{WRAPPER}} .list-meta.category_meta path' => 'fill: {{VALUE}}; color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'categorymeta_bgcolor',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.category_meta' => 'background-color: {{VALUE}};',
        ]
      ]
    );


   $this->end_controls_tab();
   $this->start_controls_tab(
       'categorymeta_hover',
       [
           'label' =>esc_html__( 'Hover', 'gum-elementor-addon' ),
       ]
   );


    $this->add_control(
      'categorymeta_hcolor',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.category_meta:hover a,{{WRAPPER}} .list-meta.category_meta:hover .meta-text' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'category_icon_hcolor',
      [
        'label' => esc_html__( 'Icon Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.category_meta:hover i, {{WRAPPER}} .list-meta.category_meta:hover path' => 'fill: {{VALUE}}; color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'categorymeta_bghover',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.category_meta:hover' => 'background-color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'categorymeta_bdhover',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta.category_meta:hover' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'categorymeta_border_border!' => ''
        ],
      ]
    );

   $this->end_controls_tab();
   $this->end_controls_tabs();

    $this->add_responsive_control(
        'categorymeta_margin',
        [
            'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'allowed_dimensions' => 'horizontal',
            'placeholder' => [
              'top' => 0,
              'right' => '',
              'bottom' => 0,
              'left' => '',
            ],
            'selectors' => [
                '{{WRAPPER}} .list-meta.category_meta' => 'margin-left: {{LEFT}}{{UNIT}};margin-right:{{RIGHT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_responsive_control(
        'categorymeta_padding',
        [
            'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} .list-meta.category_meta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_control(
      'categorymeta_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .list-meta.category_meta' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'categorymeta_border',
        'selector' => '{{WRAPPER}} .list-meta.category_meta',
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
        'condition' => ['show_readmore!' => '']
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
        'condition' => ['show_readmore!' => '','readmore_icon[value]!' => ''],
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
        'condition' => ['show_readmore!' => '','readmore_label!' => '','readmore_icon[value]!' => ''],
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
        'condition' => ['show_readmore!' => '','readmore_icon[value]!' => ''],
      ]
    );


    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'readmore_icon_border',
        'selector' => '{{WRAPPER}} .elementor-button .elementor-button-icon',
        'condition' => ['show_readmore!' => '','readmore_icon[value]!' => ''],
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
            'condition' => ['show_readmore!' => '','readmore_icon[value]!' => '','readmore_icon_border_border!' => ''],
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
        'condition' => ['show_readmore!' => '','readmore_icon[value]!' => '','readmore_icon_border_border!'=>''],
      ]
    );

    $this->start_controls_tabs( '_tabs_readmore_icon_style',['condition' => ['show_readmore!' => '','readmore_icon[value]!' => '']] );

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
        'condition' => ['show_readmore!' => '','readmore_icon[value]!' => ''],
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
        'condition' => ['show_readmore!' => '','readmore_icon[value]!' => ''],

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
        'condition' => ['show_readmore!' => '','readmore_icon[value]!' => ''],
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

    $this->start_controls_section(
      'pagination_style',
      [
        'label' => esc_html__( 'Pagination', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'show_pagination!' => ''
        ],
      ]
    );  



    $this->add_responsive_control(
      'pagination_margin',
      [
          'label' => esc_html__( 'Margin', 'gum-elementor-addon' ),
          'type' => Controls_Manager::DIMENSIONS,
          'size_units' => [ 'px', '%', 'em' ],
          'selectors' => [
              '{{WRAPPER}} .grid-posts-pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          ],
      ]
    );



    $this->add_control(
      'pagination_item_width',
      [
        'label' => esc_html__( 'Min Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::NUMBER,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .grid-posts-pagination .page-numbers,{{WRAPPER}} .grid-posts-pagination .nav-button' => 'min-width: {{VALUE}}px;',
        ],
        'separator' =>'before'
      ]
    );

    $this->add_responsive_control(
      'pagination_item_spacing',
      [
        'label' => esc_html__( 'Item Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 200,
            'step'=> 1
          ],
        ],
        'default' => [
          'size' => '10',
          'unit' => 'px'
        ],
        'selectors' => [
          '{{WRAPPER}} .grid-posts-pagination li:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
        ],
        'separator' =>'after',
        'condition' => [
          'pagination_align!' => 'justify'
        ]
      ]
    );


    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_pagination',
        'selector' => '{{WRAPPER}} .grid-posts-pagination a,{{WRAPPER}} .grid-posts-pagination .page-numbers',
      ]
    );

    $this->add_responsive_control(
      'pagination_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .grid-posts-pagination .page-numbers,{{WRAPPER}} .grid-posts-pagination .nav-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ]
      ]
    );
    
    $this->add_control(
      'pagination_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .grid-posts-pagination .page-numbers,{{WRAPPER}} .grid-posts-pagination .nav-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ]
      ]
    );

    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'pagination_border',
        'selector' => '{{WRAPPER}} .grid-posts-pagination .page-numbers,{{WRAPPER}} .grid-posts-pagination .nav-button',
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
          '{{WRAPPER}} .grid-posts-pagination .page-numbers,{{WRAPPER}} .grid-posts-pagination .nav-button' => 'color: {{VALUE}};',
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
          '{{WRAPPER}} .grid-posts-pagination .page-numbers,{{WRAPPER}} .grid-posts-pagination .nav-button' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_tab();
    $this->start_controls_tab(
      'tab_pagination_current',
      [
        'label' => esc_html__( 'Current', 'gum-elementor-addon' ),
        'condition' => ['pagination_type' => 'paging']
      ]
    );


    $this->add_control(
      'pagination_curitem_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .grid-posts-pagination .page-numbers.current' => 'color: {{VALUE}};',
        ],
      ]
    );    

    $this->add_control(
      'pagination_curitem_bgcolor',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .grid-posts-pagination .page-numbers.current' => 'background-color: {{VALUE}};',
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
          '{{WRAPPER}} .grid-posts-pagination .page-numbers.current' => 'border-color: {{VALUE}};',
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
          '{{WRAPPER}} .grid-posts-pagination .page-numbers:hover,{{WRAPPER}} .grid-posts-pagination .nav-button:hover' => 'color: {{VALUE}};',
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
          '{{WRAPPER}} .grid-posts-pagination .page-numbers:hover,{{WRAPPER}} .grid-posts-pagination .nav-button:hover' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'pagination_item_hover_bdcolor',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .grid-posts-pagination .page-numbers:hover,{{WRAPPER}} .grid-posts-pagination .nav-button:hover' => 'border-color: {{VALUE}};',
        ],
        'condition' => ['pagination_border_border!' => '']
      ]
    );   

    $this->end_controls_tab();
    $this->end_controls_tabs();


    $this->end_controls_section();

  }

  private static function grid_background_types() {
    return [
      'classic' => [
        'title' => esc_html_x( 'Classic', 'Background Control', 'elementor' ),
        'icon' => 'eicon-paint-brush',
      ],
      'gradient' => [
        'title' => esc_html_x( 'Gradient', 'Background Control', 'elementor' ),
        'icon' => 'eicon-barcode',
      ],
    ];
  }

  protected function render() {

    $settings = $this->get_settings_for_display();

    extract( $settings );


    $posts_per_page = isset($posts_per_page) && $posts_per_page!='' ? $posts_per_page : 10;
    $paged = get_query_var('paged');

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

    $query_params= array(
      'posts_per_page' => $posts_per_page,
      'no_found_rows' => false,
      'post_status' => 'publish',
      'post_type'=>'post',
      'paged' => $paged,
      'ignore_sticky_posts' => true
    );

    $post_not_ids = array();


    if($is_featured_image === 'yes'){

      $query_params['meta_query'] = array('relation'=> 'AND', array( 'key' => '_thumbnail_id' ) );
    }

    if( $posts_offset && $posts_offset > 0 ){
      $query_params['offset'] = absint( $posts_offset );
    }


    if($single_exclude && $single_exclude === 'yes' && is_single()){
      $post_not_ids[] = $post_id = get_the_ID();
    }

    if( $skip_previous == 'yes'){
      $excludes =  $this->get_previous_blog();
      $post_not_ids = array_merge($post_not_ids, $excludes );
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


    if(count($post_not_ids)){
      $query_params['post__not_in'] = $post_not_ids;
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

  
    $col_class = 'grid-post grid-col-'.$grid_layout.' image-position-'.$image_position;

    if($image_totop === 'yes'){
      $col_class.=' mobile-force-ontop';
    }

    echo '<div class="grid-posts"><div class="'.$col_class.'">'.join('</div><div class="'.$col_class.'">',$rows_html).'</div></div><div class="not-empty">&nbsp;</div>';

    if( $show_pagination == 'yes'){

      $base_url="";

      switch ($pagination_baseurl) {
        case 'blog':

            $nex_page_id = get_option('page_for_posts');
            $base_url= get_permalink($nex_page_id); 

          break;                
        case 'custom':
            if(isset( $custom_baseurl['url'] )){
                $base_url = $custom_baseurl['url'];
              }
          break;
        default:
          break;
      }

      $pagination = $this->get_pagination( $post_query, $posts_per_page, $pagination_type, $base_url );

      print $pagination;

    }

  }

  protected function get_pagination( $query, $posts_per_page, $type="", $base_url="" ) {

    if( !is_object( $query ) || !method_exists( $query , 'have_posts')) return;

    $total = $query->found_posts;
    $max_num_pages = ceil( $total / $posts_per_page );

    $pagination_args=array(
          "max_num_pages"=> $max_num_pages,
          "total"=> $total,
          "base_url" => esc_url( $base_url ),
          "before"=>"<li>",
          "after"=>"</li>",
          "navigation_type" => $type,
          "wrapper"=>"<div class=\"grid-posts-pagination %s\" dir=\"ltr\"><ul>%s</ul></div>"
    );

    $pagination =  Gum_Elementor_Helper::blog_pagination( $pagination_args );

    return $pagination;
  }

  protected function get_post($settings = array()) {

    if(!isset( $settings ) || empty( $settings )){
      $settings = $this->get_settings_for_display();
    }

    extract( $settings );      

    $post_id = get_the_ID();
    $post_title = get_the_title();

    $hide_content = true; $post_content = '';

    if(!isset($show_excerpt) || $show_excerpt =='yes'){

      $hide_content = false;
      $post_content = strip_shortcodes( get_the_excerpt() );
      if($post_content_word !='' && $post_content_word > 0){
        $post_content = wp_trim_words($post_content ,  absint($post_content_word) , $post_content_sufix );
      }

    }

    if($post_title_word !='' && $post_title_word > 0 ){
        $post_title = wp_trim_words($post_title ,  absint($post_title_word) , $post_title_sufix );
    }

    $this->add_toprevious_blog( $post_id );
    $post_url = get_the_permalink();

    $thumb_id = get_post_thumbnail_id( $post_id );
    $image = ['id' => $thumb_id ];
    $settings['thumbnail'] = $image;

    $divider = isset( $settings['divider'] ) ? $settings['divider'] : '';
    $settings['post_url'] = $post_url;

    $is_image = isset($show_image) && $show_image =='yes' || !isset($show_image);

    $image_url = $is_image ? Group_Control_Image_Size::get_attachment_image_src( $thumb_id , 'thumbnail', $settings) : '';

    if ( ! empty( $image_url ) ) {
      $image_html = sprintf( '<img src="%s" title="%s" alt="%s" />', esc_attr( $image_url ), Control_Media::get_image_title( $thumb_id ), Control_Media::get_image_alt( $thumb_id ) );
    }

    $allowed_tags = array('h1','h2','h3','h4','h5','h6','div');
    $tag_title = (in_array( $tag, $allowed_tags )) ? trim( $tag ): 'h4';

    $post_title = sprintf( '<%s class="post-title"><a href="%s">%s</a></%s>',$tag_title, get_the_permalink( $post_id ),esc_html( $post_title ), $tag_title);
    $top_meta = $this->get_post_meta( $settings , 'top', $divider);
    $mid_meta = $this->get_post_meta( $settings , 'mid', $divider);

    $top_content = $title_position == 'content' || $image_url=='' ? $top_meta.$post_title.$mid_meta :  "" ;

    ob_start();
?>
<article id="post-<?php print esc_attr($post_id); ?>" <?php post_class(); ?>>
    <?php if($image_url!=''):?>
  <div class="post-top">
    <?php if($title_position == 'before'){ print $top_meta.$post_title.$mid_meta; } ?>
    <div class="blog-image" style="background-image: url('<?php print $image_url; ?>');"><?php print $image_html;?></div>
    <?php if($title_position == 'after'){ print $top_meta.$post_title.$mid_meta; } ?>
  </div>
    <?php endif;?>
    <div class="post-content">
      <?php 

      print $top_content;

      ?>
      <?php if(!$hide_content){ printf( '<div class="content-excerpt clearfix">%s</div>', $post_content); } ?>
      <?php print $this->get_post_meta( $settings , 'bottom', $divider); ?>
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
        $meta_icon_html = $this->get_meta_icon( $settings[ $meta.'_icon']);


        switch ($meta) {
          case 'date_meta':
            $meta_type = get_the_date();
            break;
          case 'author_meta':

            $author_id = get_post_field( 'post_author', $post_id );
            $meta_type = get_the_author_meta('nickname', $author_id);
            $meta_url = esc_url(get_the_author_meta('url',$author_id));

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
            $rows_html[] = '<li class="list-meta '.$meta.'">'.$meta_icon_html. ( $meta_url !='' ? sprintf('<a href="%s"><span class="meta-text">%s</span></a>', $meta_url, $meta_type) : sprintf('<span class="meta-text">%s</span>',$meta_type) ).'</li>';
        }

      }


    }

    return '<ul class="meta-position-'.esc_attr($position).' posts-meta">'.join('<li class="meta-divider">'.$spacer.'</li>',$rows_html).'</ul>';

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

  protected function get_meta_icon( $meta_icon ) {

    if( empty($meta_icon['value'] )) return '';

     if ( 'svg' === $meta_icon['library'] ) {
        return Icons_Manager::render_uploaded_svg_icon( $meta_icon['value'] );
      } else {
        return Icons_Manager::render_font_icon( $meta_icon, [ 'aria-hidden' => 'true' ], 'i' );
      }

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

  protected function content_template() {

  }

  public function enqueue_script( ) {

    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());
  }

}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Gum_Elementor_Widget_blog_grid() );

?>