<?php
namespace Elementor;
/**
 * @package     WordPress
 * @subpackage  Gum Elementor Addon
 * @author      support@themegum.com
 * @since       1.0.12
*/
defined('ABSPATH') or die();

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

/**
 * Post share widget
 * @since       1.1.0
*/

class Gum_Elementor_Widget_Post_share extends Widget_Base {


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
    return 'gum_post_share';
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

    return esc_html__( 'Post Share', 'gum-elementor-addon' );
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
    return 'fa fa-xs fa-share';
  }

  public function get_keywords() {
    return [ 'wordpress', 'widget', 'post','social','share' ];
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

  protected function _get_share_link($type='fb', $link ='', $settings = array()) {

    $regexp_pattern = array(
      'fb' => 'https://www.facebook.com/sharer/sharer.php?u=%s',
      'tw' => 'https://twitter.com/share?url=%s',
      'plus' => 'https://plus.google.com/share?url=%s',
      'pin' => 'https://www.pinterest.com/pin/create/button/?url=%s',
    );

    $regexp = apply_filters( 'gum_post_share_share_link_format', $regexp_pattern, $settings );

    if(in_array($type, array_keys($regexp) )){

      $link = sprintf( $regexp[$type] ,$link);

    }
    else if($type == 'cs' && isset( $settings['custom_link']) ) {

      $custom_link = $settings['custom_link'];
      $regexp = trim( $custom_link['url'] );

      $link = sprintf( $regexp  ,$link);

    }
    return $link;

  } 

  protected function _register_controls() {



    $this->start_controls_section(
      'section_title',
      [
        'label' => esc_html__( 'Share', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'link_icon',
      [
        'label' => esc_html__( 'Icon', 'elementor' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'social',
        'default' => [
          'value' => 'fab fa-facebook-f',
          'library' => 'fa-brands',
        ],
        'recommended' => [
          'fa-brands' => [
            'facebook-f',
            'instagram',
            'linkedin',
            'pinterest',
            'twitter',
            'google',
          ],
        ],
      ]
    );

    $this->add_control(
      'link_text',
      [
        'label' => esc_html__( 'Label', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'dynamic' => [
          'active' => true,
        ],
        'ai' => [
          'active' => false,
        ],
        'default' => '',
      ]
    );


    $this->add_control(
      'share_type',
      [
        'label' => esc_html__( 'Share For', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'fb' => esc_html__( 'Facebook', 'gum-elementor-addon' ),
          'tw' => esc_html__( 'Twitter', 'gum-elementor-addon' ),
          'plus' => esc_html__( 'Google Plus', 'gum-elementor-addon' ),
          'pin' => esc_html__( 'Pinterest', 'gum-elementor-addon' ),
          'cs' => esc_html__( 'Custom', 'gum-elementor-addon' ),
        ],
        'default' => 'fb'
      ]
    );

    $this->add_control(
      'custom_link',
      [
        'label' => esc_html__( 'Link', 'gum-elementor-addon' ),
        'type' => Controls_Manager::URL,
        'dynamic' => [
          'active' => true,
        ],
        'placeholder' => esc_html__( 'https://your-link.com/shared=%s', 'gum-elementor-addon' ),
        'default' => [
          'url' => '',
        ],
        'description' => esc_html__( 'The link will add post permalink. Place %s as permalink placehorlder.', 'gum-elementor-addon' ),
        'condition' => ['share_type[value]' => 'cs']
      ]
    );

    $this->end_controls_section();

/*
 * style params
 */


    $this->start_controls_section(
      'share_style',
      [
        'label' => esc_html__( 'Share', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );


   $this->start_controls_tabs( 'meta_title_tabs', [] );

   $this->start_controls_tab(
       'link_share_normal',
       [
           'label' =>esc_html__( 'Normal', 'elementor' ),
       ]
   );


    $this->add_control(
      'link_share_bgcolor',
      [
        'label' => esc_html__( 'Background', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .share-link' => 'background-color: {{VALUE}};',
        ]
      ]
    );

   $this->end_controls_tab();

   $this->start_controls_tab(
       'link_share_hover',
       [
           'label' =>esc_html__( 'Hover', 'elementor' ),
       ]
   );


    $this->add_control(
      'link_share_bghover',
      [
        'label' => esc_html__( 'Background', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .share-link:hover' => 'background-color: {{VALUE}};',
        ]
      ]
    );


    $this->add_control(
      'link_share_bdhover',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .share-link:hover' => 'border-color: {{VALUE}};',
        ],
        'condition' => [
          'link_share_border_border!' => ''
        ],
      ]
    );


    $this->end_controls_tab();
    $this->end_controls_tabs();


    $this->add_responsive_control(
        'link_share_padding',
        [
            'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} .share-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );


    $this->add_group_control(
     Group_Control_Border::get_type(),
      [
        'name' => 'link_share_border',
        'selector' => '{{WRAPPER}} .share-link',
      ]
    );

    $this->add_control(
      'link_share_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .share-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );


    $this->end_controls_section();

    $this->start_controls_section(
      'link_text_style',
      [
        'label' => esc_html__( 'Label', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
        'condition' => [
          'link_text!' => ''
        ],
      ]
    );    

    $this->add_responsive_control(
      'link_text_position',
      [
        'label' => esc_html__( 'Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => esc_html__( 'Left', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-left',
          ],
          'right' => [
            'title' => esc_html__( 'Right', 'gum-elementor-addon' ),
            'icon' => 'eicon-h-align-right',
          ],
        ],
        'default' => 'right',
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_meta_title',
        'selector' => '{{WRAPPER}} .share-text',
      ]
    );

    $this->add_control(
      'link_text_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .share-text' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'link_text_hcolor',
      [
        'label' => esc_html__( 'Hover Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} a:hover .share-text' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_responsive_control(
      'link_text_space',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 100,
            'step'=> 1,
          ],
        ],  
        'default'=>['size'=>'','unit'=>'px'],
        'size_units' => [ 'px'],
        'selectors' => [
          '{{WRAPPER}} .share-link.label-right .share-link-icon + .share-text' => 'margin-left: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .share-link.label-left .share-link-icon + .share-text' => 'margin-right: {{SIZE}}{{UNIT}};'
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'link_icon_style',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    

    $this->add_responsive_control(
      'link_icon_size',
      [
        'label' => esc_html__( 'Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 200,
            'step'=> 1,
          ],
        ],  
        'default'=>['size'=>'','unit'=>'px'],
        'size_units' => [ 'px' ],
        'selectors' => [
          '{{WRAPPER}} .share-link-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .share-link-icon svg' => 'width: {{SIZE}}{{UNIT}};height: auto;'
        ],
      ]
    );

    $this->add_control(
      'link_icon_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .share-link-icon,{{WRAPPER}} .share-link-icon svg *' => 'color: {{VALUE}};fill: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'link_icon_hcolor',
      [
        'label' => esc_html__( 'Hover Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} a:hover .share-link-icon,{{WRAPPER}} a:hover .share-link-icon svg *' => 'color: {{VALUE}};fill: {{VALUE}};',
        ]
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

    $url = $this->_get_share_link($share_type, get_permalink( $post_id ), $settings);


    $this->add_render_attribute( 'link_text' , 'class', 'share-text' );
    $this->add_inline_editing_attributes( 'link_text', 'none' );

    $this->add_link_attributes( 'share-link' , array('url' => $url ) );
    $this->add_render_attribute( 'share-link', ['class' => 'share-link'] );

    if($link_text_position!=''){

      $this->add_render_attribute( 'share-link', ['class' => 'label-'.$link_text_position ]);
    }
   ?>
   <a <?php echo $this->get_render_attribute_string( 'share-link'); ?>>
<?php if(!empty($link_icon)){?>
<span class="share-link-icon">
    <?php Icons_Manager::render_icon( $link_icon , [ 'aria-hidden' => 'true' ] );?>
</span>
<?php }

    if($link_text!=''){ ?>
    <span <?php echo $this->get_render_attribute_string( 'link_text' );?>><?php esc_html_e($link_text); ?></span><?php } ?></a>    
    <?php 
  }

  protected function content_template() {

  }

  public function enqueue_script( ) {

    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());
  }


}

// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Gum_Elementor_Widget_Post_share() );
?>