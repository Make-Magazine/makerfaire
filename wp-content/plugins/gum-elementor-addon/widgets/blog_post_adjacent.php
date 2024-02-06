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
use Elementor\Group_Control_Typography;

/**
 * Post adjacent widget
 * @since       1.0.11
*/

class Gum_Elementor_Widget_Post_adjacent extends Widget_Base {


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
    return 'gum_post_adjacent';
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

    return esc_html__( 'Post Adjacent', 'gum-elementor-addon' );
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
    return 'eicon-angle-right';
  }

  public function get_keywords() {
    return [ 'wordpress', 'widget', 'post','previous','next' ];
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
      'post_type',
      [
        'label' => esc_html__( 'Source', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'previous' => esc_html__( 'Previous Post', 'gum-elementor-addon' ),
          'next' => esc_html__( 'Next Post', 'gum-elementor-addon' ),
        ],
        'default' => 'next'
      ]
    );


    $this->add_control(
        'post_label',
        [
          'label' => esc_html__( 'Label', 'gum-elementor-addon' ),
          'type' => Controls_Manager::SELECT,
          'options' => [
            'title' =>  esc_html__( 'Post Title', 'gum-elementor-addon' ),
            'custom' => esc_html__( 'Custom', 'gum-elementor-addon' ),
          ],
          'default' => 'title',
        ]
    );


    $this->add_control(
      'post_label_custom',
      [
        'label' => esc_html__( 'Custom Label', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'dynamic' => [
          'active' => false,
        ],
        'ai' => [
          'active' => false,
        ],
        'default' => esc_html__( 'Next Post', 'gum-elementor-addon' ),
        'condition' => [
          'post_label[value]' => 'custom'
        ],
      ]
    );

    $this->end_controls_section();

/*
 * style params
 */

    $this->start_controls_section(
      'post_link',
      [
        'label' => esc_html__( 'Link', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    

  $this->add_responsive_control(
      'post_link_align',
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
          '{{WRAPPER}} .adjacent-post' => 'text-align: {{VALUE}};',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_post_link',
        'selector' => '{{WRAPPER}} .adjacent-post',
      ]
    );

    $this->add_control(
      'post_link_color',
      [
        'label' => esc_html__( 'Normal Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} a.adjacent-post' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'post_link_hcolor',
      [
        'label' => esc_html__( 'Hover Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} a.adjacent-post:hover,{{WRAPPER}} a.adjacent-post:focus' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->end_controls_section();

  }

  protected function render() {

    $settings = $this->get_settings_for_display();
    extract( $settings );

    $next = (bool)($post_type == 'next');
    $next_post = get_adjacent_post( true, "", $next , "category" );

    if( empty( $next_post ) || $next_post->post_type !='post') return '';

    $post_id = $next_post->ID;
    $link_label = $next_post->post_title;

    $this->add_render_attribute( 'link_wrapper', 'class', ['adjacent-post', $post_type] );
    $this->add_link_attributes( 'link_wrapper', array('url'=>get_permalink($post_id)) );

    if( $post_label == 'custom' && $post_label_custom !=''){
      $link_label = esc_html( $post_label_custom );
    }

    echo '<a '.$this->get_render_attribute_string( 'link_wrapper' ).'>'.$link_label.'</a>';

  }

  protected function content_template() {

  }

  public function enqueue_script( ) {

    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());
  }


}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Gum_Elementor_Widget_Post_adjacent() );

?>