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
use Elementor\Repeater;

/**
 * Post term widget
 * @since       1.0.11
*/

class Gum_Elementor_Widget_Post_term extends Widget_Base {


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
    return 'gum_post_term';
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

    return esc_html__( 'Post Term', 'gum-elementor-addon' );
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
    return 'eicon-archive-title';
  }

  public function get_keywords() {
    return [ 'wordpress', 'widget', 'post','tag','category' ];
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
        'label' => esc_html__( 'Term', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'term_type',
      [
        'label' => esc_html__( 'Source', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'category' => esc_html__( 'Category', 'gum-elementor-addon' ),
          'tags' => esc_html__( 'Tags', 'gum-elementor-addon' ),
        ],
        'default' => 'category'
      ]
    );


    $this->add_control(
      'term_linked',
      [
        'label' => esc_html__( 'Linked', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'default' => 'yes',
      ]
    );

    $this->add_control(
        'list_layout',
        [
          'label' => esc_html__( 'Layout', 'gum-elementor-addon' ),
          'type' => Controls_Manager::CHOOSE,
          'options' => [
            'horizontal' => [
              'title' => esc_html__( 'Horizontal', 'gum-elementor-addon' ),
              'icon' => 'eicon-ellipsis-h',
            ],
            'vertical' => [
              'title' => esc_html__( 'Vertical', 'gum-elementor-addon' ),
              'icon' => 'eicon-editor-list-ul',
            ]
          ],
          'default' => 'horizontal',
          'prefix_class' => 'term_list_layout-',
          'style_transfer' => true,
        ]
    );

    $this->add_control(
      'separator',
      [
        'label' => esc_html__( 'Separator', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'default' => '',
        'dynamic' => [
          'active' => false,
        ],
        'description' => esc_html__( 'Only horizontal mode.', 'gum-elementor-addon' ),
      ]
    );


    $this->end_controls_section();

/*
 * style params
 */

    $this->start_controls_section(
      'meta_list_style',
      [
        'label' => esc_html__( 'List', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
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
            'max' => 100,
            'step'=> 1,
          ],
        ],  
        'default'=>['size'=>0,'unit'=>'em'],
        'size_units' => [ 'px', 'em' ],
        'selectors' => [
          '{{WRAPPER}} .term-divider' => 'margin-left: calc({{SIZE}}{{UNIT}}/2);margin-right: calc({{SIZE}}{{UNIT}}/2);',
          '{{WRAPPER}} .list-term' => 'margin-bottom: {{SIZE}}{{UNIT}};'
        ],
      ]
    );


  $this->add_responsive_control(
      'lists_align',
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
        'selectors' => [
          '{{WRAPPER}} .posts-term' => 'text-align: {{VALUE}};',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_meta_title',
        'selector' => '{{WRAPPER}} .list-term .meta-text',
      ]
    );


    $this->add_responsive_control(
        'meta_list_padding',
        [
            'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} .list-term' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );


    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'meta_list_border',
        'selector' => '{{WRAPPER}} .list-term',
      ]
    );

    $this->add_control(
      'meta_list_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .list-term' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
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
          '{{WRAPPER}} .list-term a,{{WRAPPER}} .list-term .meta-text' => 'color: {{VALUE}};',
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
          '{{WRAPPER}} .list-term' => 'background-color: {{VALUE}};',
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
          '{{WRAPPER}} .list-term:hover a,{{WRAPPER}} .list-term:hover .meta-text' => 'color: {{VALUE}};',
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
          '{{WRAPPER}} .list-term:hover' => 'background-color: {{VALUE}};',
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
          '{{WRAPPER}} .list-term:hover' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'meta_list_border_border!' => ''
        ],
      ]
    );


    $this->end_controls_tab();
    $this->end_controls_tabs();

    $this->end_controls_section();


/*
 * style params
 */

    $this->start_controls_section(
      'divider_style',
      [
        'label' => esc_html__( 'Divider', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'divider!' => ''
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
          'divider!' => ''
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
          'divider!' => ''
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
        ]
    );

    
    $this->end_controls_section();


  }

  protected function render() {

    $settings = $this->get_settings_for_display();
    extract( $settings );

    $post = get_post();

    if( empty( $post ) || $post->post_type !='post') return '';

    $post_id = $post->ID;

    $taxonomy = $term_type =='tags' ? 'post_tag' :'category';
    $terms = get_the_terms( $post_id, $taxonomy );

    if( ! $terms ) return '';

    $rows_html = array();
    $this->add_render_attribute( 'list_wrapper', 'class', 'posts-term');

    foreach ($terms as $index => $term ) {

          $rows_html[] = '<li class="list-term">'. ( $term_linked=='yes' ? sprintf('<a href="%s"><span class="meta-text">%s</span></a>', get_term_link($term->term_id), $term->name) : sprintf('<span class="meta-text">%s</span>',$term->name) ).'</li>';
       
      }

    echo '<ul '.$this->get_render_attribute_string( 'list_wrapper' ).'>'.join('<li class="term-divider"><span>'.esc_html($separator).'</span></li>',$rows_html).'</ul>';


  }

  protected function content_template() {

  }

  public function enqueue_script( ) {

    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());
  }


}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Gum_Elementor_Widget_Post_term() );

/**
 * Post meta widget
 * @since       1.0.11
*/

class Gum_Elementor_Widget_Post_meta extends Widget_Base {


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
    return 'gum_post_meta';
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

    return esc_html__( 'Post Meta', 'gum-elementor-addon' );
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
    return 'eicon-post-info';
  }

  public function get_keywords() {
    return [ 'wordpress', 'widget', 'post','meta' ];
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
        'label' => esc_html__( 'Data', 'elementor' ),
      ]
    );

    $repeater = new Repeater();


    $repeater->add_control(
      'meta_type',
      [
        'label' => esc_html__( 'Type', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'text' => esc_html__( 'Text', 'gum-elementor-addon' ),
          'author' => esc_html__( 'Post Author', 'gum-elementor-addon' ),
          'date' => esc_html__( 'Post Date', 'gum-elementor-addon' ),
          'comment' => esc_html__( 'Post Comments', 'gum-elementor-addon' ),
          'category' => esc_html__( 'Post Category', 'gum-elementor-addon' )
        ],
        'default' => 'text',
      ]
    );

    $repeater->add_control(
      'meta_text',
      [
        'label' => esc_html__( 'Text', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'label_block' => true,
        'dynamic' => [
          'active' => true,
        ],
        'default' => esc_html__( 'Text Here', 'gum-elementor-addon' ),
        'condition' => [
          'meta_type[value]' => 'text'
        ],
      ]
    );

    $repeater->add_control(
      'meta_prefix',
      [
        'label' => esc_html__( 'Prefix', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'dynamic' => [
          'active' => false,
        ],
        'ai' => [
          'active' => false,
        ],
        'default' => '',
      ]
    );


    $repeater->add_control(
      'meta_icon',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
      ]
    );



    $repeater->add_control(
      'meta_linked',
      [
        'label' => esc_html__( 'Linked', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'default' => '',
        'separator' => 'before',
        'condition' => [
          'meta_type[value]' => array('category','date','author','text')
        ],
      ]
    );


    $repeater->add_control(
      'meta_url',
      [
        'label' => esc_html__( 'Your Link', 'gum-elementor-addon' ),
        'type' => Controls_Manager::URL,
        'label_block' => true,
        'dynamic' => [
          'active' => true,
        ],
        'placeholder' => esc_html__( 'https://your-link.com', 'gum-elementor-addon' ),
        'default' => [
          'url' => '#',
        ],
        'conditions' => [
          'relation' => 'and',
          'terms' => [
            [
              'name' => 'meta_linked',
              'operator' => '==',
              'value' => 'yes',
            ],
            [
              'name' => 'meta_type',
              'operator' => '==',
              'value' => 'text',
            ],
          ],
        ],
      ]
    );

    $this->add_control(
      'meta_lists',
      [
        'label' => esc_html__( 'Selected Info', 'gum-elementor-addon' ),
        'type' => Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'title_field' => "<# var title = (meta_type == 'text' ? meta_text : meta_type); #>{{{ title }}}",
        'default' => [
          [
            'meta_type' => 'author',
            'meta_url' => '',
            'meta_text' => '',
            'meta_prefix' => '',
            'meta_icon' => '',
            'meta_linked' => 'yes'
          ],
          [
            'meta_type' => 'date',
            'meta_url' => '',
            'meta_text' => '',
            'meta_prefix' => '',
            'meta_icon' => '',
            'meta_linked' => 'yes'
          ],
          [
            'meta_type' => 'category',
            'meta_url' => '',
            'meta_text' => '',
            'meta_prefix' => '',
            'meta_icon' => '',
            'meta_linked' => 'yes'
          ],
        ]
      ]      
    );

    $this->add_responsive_control(
      'lists_align',
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
        'selectors' => [
          '{{WRAPPER}} .posts-meta' => 'text-align: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'divider',
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
        'separator' => 'before',
        'prefix_class' => 'elementor-post-meta-divider-',
        'toggle' => false,
      ]
    );

    $this->add_control(
      'divider_text',
      [
        'label' => esc_html__( 'Text', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'condition' => [
          'divider' => 'text',
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
          'divider' => 'icon',
        ],
      ]
    );

    $this->end_controls_section();


/*
 * style params
 */

    $this->start_controls_section(
      'meta_list_style',
      [
        'label' => esc_html__( 'Meta', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    

    $this->add_responsive_control(
      'meta_list_space',
      [
        'label' => esc_html__( 'Horizontal Spacing', 'gum-elementor-addon' ),
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


    $this->add_responsive_control(
      'meta_list_vspace',
      [
        'label' => esc_html__( 'Vertical Spacing', 'gum-elementor-addon' ),
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
        'default'=>[],
        'size_units' => [ 'px', 'em' ],
        'selectors' => [
          '{{WRAPPER}} .list-meta' => 'margin-bottom: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .posts-meta' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
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
      'icon_hover_color',
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


    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'meta_list_border',
        'selector' => '{{WRAPPER}} .list-meta',
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


    $this->add_control(
      'icon_style_heading',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'after'
      ]
    );

    $this->add_control(
      'icon_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .list-meta i, {{WRAPPER}} .list-meta path' => 'fill: {{VALUE}}; color: {{VALUE}};',
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
          '{{WRAPPER}} .list-meta i' => 'font-size: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .list-meta svg' => 'height: {{SIZE}}%;width: {{SIZE}}%;'
        ],
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
          '{{WRAPPER}} .list-meta .meta-text' => 'padding-left: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();


/*
 * style params
 */

    $this->start_controls_section(
      'divider_style',
      [
        'label' => esc_html__( 'Divider', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'divider!' => ''
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
          'divider!' => ''
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
          'divider!' => ''
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
        ]
    );

    
    $this->end_controls_section();


  }

  protected function render() {

    $settings = $this->get_settings_for_display();

    extract( $settings );

    if(!count( $meta_lists )) return '';

    $rows_html  = array();

    $post_id = get_the_ID();
    $author_id = get_post_field( 'post_author', $post_id );

    foreach ($meta_lists as $index => $list ) {

      $meta_linked = $list['meta_linked'];
      $meta_url = isset($list['meta_url']['url']) ? $list['meta_url']['url'] : '';
      $meta_type = $meta_icon_html = '';

      switch ($list['meta_type']) {
        case 'comment':
          $meta_type = get_comments_number_text( __('0 comment'), __('1 comment'),__('% comments'));
          break;
        case 'date':
          $meta_type = get_the_date();
          $meta_url = $meta_linked=='yes' ? get_day_link(get_post_time('Y'), get_post_time('m'), get_post_time('j')) : '' ;
          break;
        case 'author':
          $meta_type = get_the_author_meta('nickname', $author_id);
          $meta_url = $meta_linked=='yes' ? get_the_author_meta('url',$author_id) : '';

          break;
        case 'category':
          $categories = get_the_category($post_id);

          if($categories){
            $category = $categories[0];
            $meta_type = $category->name;
            $meta_url = get_category_link( $category->term_id );
          }
          break;
        case 'categories':
        default:
          $meta_type = $list['meta_text'];
          break;
      }

      if( isset($list['meta_icon'] ) ){

        if ( 'svg' === $list['meta_icon']['library'] ) {
          $meta_icon_html = Icons_Manager::render_uploaded_svg_icon( $list['meta_icon']['value'] );
        } else {
          $meta_icon_html = Icons_Manager::render_font_icon( $list['meta_icon'], [ 'aria-hidden' => 'true' ], 'i' );
        }
      }

      if( $list['meta_prefix']!=''){
        $meta_type = $list['meta_prefix'].' '.$meta_type;
      } 

      if($meta_type!=''){
          $rows_html[] = '<li class="list-meta">'. ( $meta_linked=='yes' ? sprintf('<a href="%s">'.$meta_icon_html.'<span class="meta-text">%s</span></a>', esc_url($meta_url), esc_html($meta_type)) : sprintf( $meta_icon_html.'<span class="meta-text">%s</span>',esc_html($meta_type)) ).'</li>';
      }
     
    }

    if($divider == 'text'){
      $divider = '<span>'.esc_html($divider_text).'</span>';

    }elseif($divider == 'icon'){
      ob_start();
      
      Icons_Manager::render_icon( $divider_icon, ['aria-hidden' => 'true'],'span' );

      $divider = ob_get_clean();
    }


    $this->add_render_attribute( 'list_wrapper', 'class', 'posts-meta');

    if(count($rows_html)){
      echo '<ul '.$this->get_render_attribute_string( 'list_wrapper' ).'>'.join('<li class="meta-divider">'.$divider.'</li>',$rows_html).'</ul>';
    }

  }

  protected function content_template() {

  }

  public function enqueue_script( ) {

    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());
  }


}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Gum_Elementor_Widget_Post_meta() );

?>