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
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;

class Gum_Elementor_Widget_blog_featured_image extends Widget_Base {


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
    return 'gum_post_image';
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

    return esc_html__( 'Featured Image', 'gum-elementor-addon' );
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
    return [ 'wordpress', 'widget', 'post','single','feature','image' ];
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
        'label' => esc_html__( 'Image', 'elementor' ),
      ]
    );


    $this->add_group_control(
      Group_Control_Image_Size::get_type(),
      [
        'name' => 'thumbnail', 
        'default' => 'medium',
      ]
    );


    $this->add_responsive_control(
      'image_align',
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
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}};',
        ],
        'default' => '',
      ]
    );


    $this->end_controls_section();

/*
 * style params
 */

    $this->start_controls_section(
      'title_style',
      [
        'label' => esc_html__( 'Image', 'elementor' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    


    $this->add_responsive_control(
      'image_width',
      [
        'label' => esc_html__( 'Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 2000,
          ],
          '%' => [
            'min' => 0,
            'max' => 100,
          ],
          'vw' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'size_units'=>['%','vw','px'],
        'default'=> ['size'=>'','unit'=> '%'],
        'selectors' => [
          '{{WRAPPER}} .blog-featureimage' => 'width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'image_maxwidth',
      [
        'label' => esc_html__( 'Max Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 2000,
          ],
          '%' => [
            'min' => 0,
            'max' => 100,
          ],
          'vw' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'size_units'=>['%','vw','px'],
        'default'=> ['size'=>'','unit'=> '%'],
        'selectors' => [
          '{{WRAPPER}} .blog-featureimage' => 'max-width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );


    $this->add_responsive_control(
      'image_height',
      [
        'label' => esc_html__( 'Height', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 2000,
          ],
          'vh' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'size_units'=>['px','vh'],
        'default'=> ['size'=>'','unit'=> 'px'],
        'selectors' => [
          '{{WRAPPER}} .blog-featureimage,{{WRAPPER}} .blog-featureimage img' => 'height: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'image_position',
      [
        'label' => esc_html__( 'Image Fit', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          '' => esc_html__( 'Default', 'gum-elementor-addon' ),
          'cover' => esc_html__( 'Cover', 'gum-elementor-addon' ),
          'contain' => esc_html__( 'Contain', 'gum-elementor-addon' ),
        ],
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .blog-featureimage' => 'background-size: {{VALUE}};',
        ],
        'condition' => [ 'image_height!' => ''],

      ]
    );

    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'image_border',
        'selector' => '{{WRAPPER}} .blog-featureimage',
      ]
    );


    $this->add_control(
      'image_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .blog-featureimage' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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


    $thumb_id = get_post_thumbnail_id( $post->ID );
    $image = ['id' => $thumb_id ];
    $settings['thumbnail'] = $image;

    $image_url = Group_Control_Image_Size::get_attachment_image_src( $thumb_id , 'thumbnail', $settings);

    if ( empty( $image_url ) )return; 

    $this->add_render_attribute( 'wrapper', 'class', 'blog-featureimage' );
    $this->add_render_attribute( 'wrapper', 'style', 'background-image: url('.esc_attr( $image_url ).')' );

    $image_html = sprintf( '<img src="%s" title="%s" alt="%s" />', esc_attr( $image_url ), Control_Media::get_image_title( $thumb_id ), Control_Media::get_image_alt( $thumb_id ) );

    ?><div <?php $this->print_render_attribute_string( 'wrapper' ); ?>><?php print $image_html;?></div><?php

  }
}
// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Gum_Elementor_Widget_blog_featured_image() );

?>