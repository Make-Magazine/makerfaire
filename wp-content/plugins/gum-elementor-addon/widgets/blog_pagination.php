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

class Gum_Elementor_Widget_blog_Pagination extends Widget_Base {

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
    return 'gum_posts_paging';
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

    return esc_html__( 'Blog Pagination', 'gum-elementor-addon' );
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
    return 'fas fa-xs fa-ellipsis-h';
  }

  public function get_keywords() {
    return [ 'wordpress', 'widget', 'post','recent' ];
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
        'label' => esc_html__( 'Content', 'elementor' ),
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


    $this->add_control(
      'image_position',
      [
        'label' => esc_html__( 'Image Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          '2' => [
            'title' => esc_html__( 'Right', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-right',
          ],
          '' => [
            'title' => esc_html__( 'Left', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-left',
          ],
        ],
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .post-thumbnail' => 'order: {{VALUE}};'
        ],
        'prefix_class' => 'post-thumbnail-position-',
        'condition' => [
          'show_image[value]' => 'yes'
        ],
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
      'show_date',
      [
        'label' => esc_html__( 'Display post date?', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'default' => 'yes',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_data',
      [
        'label' => esc_html__( 'Data', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'source_orderby',
      [
        'label' => esc_html__( 'Order By', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'latest' => esc_html__( 'Latest Post', 'gum-elementor-addon' ),
          'view' => esc_html__( 'Most Read', 'gum-elementor-addon' ),
          'comment' => esc_html__( 'Most Comment', 'gum-elementor-addon' )
        ],
        'default' => 'latest'
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


    $categories_options = array(
       'all'=> esc_html__( 'All Category', 'gum-elementor-addon' ),
       'only'=> esc_html__( 'Same This Post', 'gum-elementor-addon' )
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
        'default' => 'all',
      ]
    );



    $this->end_controls_section();


/*
 * style params
 */

    $this->start_controls_section(
      'post_list_style',
      [
        'label' => esc_html__( 'List Style', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    

    $this->add_responsive_control(
      'post_list_space',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'em' => [
            'max' => 10,
          ],
          'px' => [
            'max' => 2000,
          ],
        ],  
        'default'=>['size'=>1,'unit'=>'em'],
        'size_units' => [ 'px', 'em' ],
        'selectors' => [
          '{{WRAPPER}} .post-divider' => 'padding-top: calc({{SIZE}}{{UNIT}}/2);padding-bottom: calc({{SIZE}}{{UNIT}}/2);',
        ],
      ]
    );

    $this->add_responsive_control(
            'post_list_padding',
            [
                'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .post-item .item-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
    );

    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'post_list_border',
        'selector' => '{{WRAPPER}} .post-item',
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
          '{{WRAPPER}} .post-item' => 'background-color: {{VALUE}};',
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
          '{{WRAPPER}} .post-item:hover' => 'background-color: {{VALUE}};',
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
          '{{WRAPPER}} .post-item:hover' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'post_list_border_border!' => ''
        ],
      ]
    );

   $this->end_controls_tab();
   $this->end_controls_tabs();


    $this->add_control(
      'show_divider',
      [
        'label' => esc_html__( 'Show Divider', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'default' => '',
        'prefix_class' => 'post-divider-',
        'separator' => 'before'
      ]
    );

    $this->add_responsive_control(
      'divider_align',
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
          ],
        ],
        'default' => '',
        'prefix_class' => 'divider%s-position-',
        'condition' => [
          'show_divider[value]' => 'yes'
        ],
        'separator' => 'before'
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
          ],
          'px' => [
            'max' => 2000,
          ],
        ],  
        'default'=>['size'=>1,'unit'=>'px'],
        'size_units' => [ 'px', 'em' ],
        'selectors' => [
          '{{WRAPPER}}.post-divider-yes .post-divider:after' => 'height: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'show_divider[value]' => 'yes'
        ],
      ]
    );

    $this->add_responsive_control(
      'divider_width',
      [
        'label' => esc_html__( 'Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          '%' => [
            'min' => 0.1,
            'max' => 100,
          ],
          'px' => [
            'min' => 1,
            'max' => 2000,
          ],
        ],  
        'default'=>['size'=>50,'unit'=>'%'],
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}}.post-divider-yes .post-divider:after' => 'width: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'show_divider[value]' => 'yes'
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
          '{{WRAPPER}}.post-divider-yes .post-divider:after' => 'background-color: {{VALUE}};',
        ],
        'condition' => [
          'show_divider[value]' => 'yes'
        ],
      ]
    );

    $this->add_control(
      'divider_radius',
      [
        'label' => esc_html__( 'Line Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}}.post-divider-yes .post-divider:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => [
          'show_divider[value]' => 'yes'
        ],
      ]
    );
    
    $this->end_controls_section();


    $this->start_controls_section(
      'post_list_image',
      [
        'label' => esc_html__( 'Image', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    

    $this->add_responsive_control(
      'post_image_width',
      [
        'label' => esc_html__( 'Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          '%' => [
            'max' => 100,
            'step' => 1,
          ],
          'px' => [
            'max' => 2000,
          ],

        ],  
        'default'=>['size'=>150,'unit'=>'px'],
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .post-item .post-thumbnail' => 'width: {{SIZE}}{{UNIT}};',
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
          '{{WRAPPER}} .post-item .post-thumbnail img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'post_title_space',
      [
        'label' => esc_html__( 'Gap with Content', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'em' => [
            'max' => 10,
          ],
          'px' => [
            'max' => 2000,
          ],
        ],  
        'default'=>['size'=>1,'unit'=>'em'],
        'size_units' => [ 'px', 'em' ],
        'selectors' => [
          '{{WRAPPER}}:not(.post-thumbnail-position-2) .post-item .post-thumbnail' => 'padding-right: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}}.post-thumbnail-position-2 .post-item .post-thumbnail' => 'padding-left: {{SIZE}}{{UNIT}};',
        ],
      ]
    );


    $this->end_controls_section();

    $this->start_controls_section(
      'post_title_style',
      [
        'label' => esc_html__( 'Content', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    


    $this->add_control(
      'post_title_heading',
      [
        'label' => esc_html__( 'Post Title', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_post_title',
        'selector' => '{{WRAPPER}} a.post-title',
      ]
    );

    $this->add_control(
      'post_title_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} a.post-title' => 'color: {{VALUE}};',
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
          '{{WRAPPER}} li:hover a.post-title,{{WRAPPER}} li:focus a.post-title' => 'color: {{VALUE}};',
        ]
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
      'post_date_heading',
      [
        'label' => esc_html__( 'Post Date', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
        'condition' => [
          'show_date[value]' => 'yes'
        ],
      ]
    );


    $this->add_responsive_control(
      'post_date_space',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'em' => [
            'max' => 10,
          ],
          'px' => [
            'max' => 2000,
          ],
        ],  
        'default'=>['size'=>1,'unit'=>'em'],
        'size_units' => [ 'px', 'em' ],
        'selectors' => [
          '{{WRAPPER}} .post-item .post-date' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'show_date[value]' => 'yes'
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_post_date',
        'selector' => '{{WRAPPER}} .post-date',
        'condition' => [
          'show_date[value]' => 'yes'
        ],
      ]
    );

    $this->add_control(
      'post_date_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .post-date' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'show_date[value]' => 'yes'
        ],
      ]
    );

    $this->end_controls_section();

  }

  protected function render() {

    $settings = $this->get_settings_for_display();

    extract( $settings );

    $posts_per_page = isset($posts_per_page) && $posts_per_page!='' ? $posts_per_page : 3;


    $query_params= array(
      'posts_per_page' => absint($posts_per_page),
      'no_found_rows' => true,
      'post_status' => 'publish',
      'post_type'=>'post',
      'ignore_sticky_posts' => true
    );

    $meta_query= $post_not_ids = array();

    if($is_featured_image === 'yes'){
      $meta_query['relation'] = 'AND';
      $meta_query[] = array(
        'key' => '_thumbnail_id'
        );
    }

  if($single_exclude && $single_exclude === 'yes' && is_single()){
    $post_not_ids[] = $post_id = get_the_ID();
  }

  if($cat_ids && $cat_ids!='all'){

    if($cat_ids == 'only'){
      $post_id =  isset($post_id) ? $post_id : get_the_ID(); 

      $cat_options= [];

      if( $categories= get_the_category($post_id)){


        foreach ($categories as $category) {
          $cat_options[]= $category->term_id;
        }

      }

      $query_params['cat'] = $cat_options;

    }
    else{
      $query_params['cat']= trim($cat_ids);
    }    
  }
 
  if($source_orderby=='view'){
      if(!isset($meta_query['relation'])) $meta_query['relation'] = 'AND';
      $meta_query[]=array(
        'key' => '_post_views_count',
        'orderby' => 'meta_value',
        'order' => 'DESC'
      );

      $query_params['orderby'] = 'meta_value_num';
      $query_params['order'] = 'DESC';
    }
    elseif($source_orderby=='comment'){

      $query_params['orderby'] = 'comment_count';
      $query_params['order'] = 'DESC';
    }

    if(count($meta_query)){
      $query_params['meta_query'] = $meta_query;
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
      $post_id = get_the_ID();

      $post_title = get_the_title();

      if($post_title_word !='' && $post_title_word > 0){
        $post_title = wp_trim_words($post_title ,  absint($post_title_word) );
      }

      $post_url = get_the_permalink();
      $image_html = null;

      if($show_image==='yes'){

        $thumb_id = get_post_thumbnail_id( $post_id );
        $image = ['id' => $thumb_id ];
        $settings['image'] = $image;
        $image_html = Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'image' );

      }
      ob_start();?>
      <li class="post-item"><div class="item-wrap"><?php if( $show_image==='yes' && $image_html){ echo '<a class="post-thumbnail" href="'.esc_url($post_url).'">'.$image_html.'</a>'; } ?><div class="post-item-content"><a class="post-title" href="<?php print esc_url($post_url);?>"><?php esc_html_e($post_title);?></a><?php if($show_date==='yes'){ printf('<span class="post-date">%s</span>', get_the_date("d M Y", $post_id)); }?></div></div></li>
      <?php
      $rows_html[] = ob_get_clean();
    endwhile;

    wp_reset_postdata();

    $this->add_render_attribute( 'list_wrapper', 'class', 'posts-list');

    echo '<ul '.$this->get_render_attribute_string( 'list_wrapper' ).'>'.join('<li class="post-divider"></li>',$rows_html).'</ul>';

  }

  protected function content_template() {

  }

  public function enqueue_script( ) {

    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());
  }


}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Gum_Elementor_Widget_blog_Pagination() );

?>