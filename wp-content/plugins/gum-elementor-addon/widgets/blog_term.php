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

/**
 * Blog term widget
 * @since       1.0.11
*/

class Gum_Elementor_Widget_Blog_term extends Widget_Base {


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
    return 'gum_blog_term';
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

    return esc_html__( 'Blog Term', 'gum-elementor-addon' );
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
    return 'eicon-meta-data';
  }

  public function get_keywords() {
    return [ 'wordpress', 'widget', 'post','category','tag' ];
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
        'label' => esc_html__( 'Mode', 'gum-elementor-addon' ),
      ]
    );


    $this->add_control(
      'term_type',
      [
        'label' => esc_html__( 'Mode', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'category' => esc_html__( 'Categories', 'gum-elementor-addon' ),
          'tags' => esc_html__( 'Tag Cloud', 'gum-elementor-addon' ),
        ],
        'default' => 'category'
      ]
    );



    $this->add_control(
      'hide_empty',
      [
        'label' => esc_html__( 'Hide Empty', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'Yes', 'gum-elementor-addon' ),
        'label_off' => esc_html__( 'No', 'gum-elementor-addon' ),
        'default' => 'yes',
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


    $this->add_control(
      'list_style',
      [
        'label' => esc_html__( 'List Style', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'disc' => esc_html__( 'Disk', 'gum-elementor-addon' ),
          'circle' => esc_html__( 'Circle', 'gum-elementor-addon' ),
          'square' => esc_html__( 'Square', 'gum-elementor-addon' ),
          'none' => esc_html__( 'None', 'gum-elementor-addon' ),
        ],
        'default' => 'disc',
        'condition' => [
          'term_type' => 'category'
        ],
        'selectors' => [
          '{{WRAPPER}} .blog-term.mode-category' => 'list-style: {{VALUE}};',
        ],
      ]
    );


    $this->add_control(
      'list_position',
      [
        'label' => esc_html__( 'List Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'initial' => esc_html__( 'Default', 'gum-elementor-addon' ),
          'inside' => esc_html__( 'Inside', 'gum-elementor-addon' ),
          'outside' => esc_html__( 'Outside', 'gum-elementor-addon' ),
        ],
        'default' => 'initial',
        'condition' => [
          'term_type' => 'category',
          'list_style!' => 'none'
        ],
        'selectors' => [
          '{{WRAPPER}} .blog-term.mode-category' => 'list-style-position: {{VALUE}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'meta_list_space',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 100,
            'step' => 1
          ],
        ],  
        'default'=>['size'=>0,'unit'=>'px'],
        'selectors' => [
          '{{WRAPPER}} .blog-term.mode-tags .list-term' => 'margin-left: calc({{SIZE}}{{UNIT}}/2);margin-right: calc({{SIZE}}{{UNIT}}/2);margin-bottom: calc({{SIZE}}{{UNIT}}/2);',
          '{{WRAPPER}} .blog-term.mode-category .list-term' => 'margin-bottom: {{SIZE}}{{UNIT}};'
        ],
      ]
    );


    $this->add_control(
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
          '{{WRAPPER}} .blog-term' => 'text-align: {{VALUE}};',
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

    $taxonomy = $term_type =='tags' ? 'post_tag' :'category';

    $args = array(
          'taxonomy' => $taxonomy,
          'orderby' => 'name',
          'show_count' => 0,
          'pad_counts' => 0,
          'hierarchical' => 0,
          'hide_empty' =>  $hide_empty === 'yes',
    );


    $terms = get_terms( $args );

    if( ! $terms ) return '';

    $rows_html = array();
    $this->add_render_attribute( 'list_wrapper', 'class', array( 'blog-term', 'mode-'.sanitize_text_field($term_type )));

    foreach ($terms as $index => $term ) {

          $rows_html[] = '<li class="list-term">'. sprintf('<a href="%s"><span class="meta-text">%s</span></a>', get_term_link($term->term_id), $term->name).'</li>';
       
    }

    echo '<ul '.$this->get_render_attribute_string( 'list_wrapper' ).'>'.join('',$rows_html).'</ul>';

  }

  protected function content_template() {

  }

  public function enqueue_script( ) {

    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());
  }


}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Gum_Elementor_Widget_Blog_term() );

?>