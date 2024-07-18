<?php
namespace Elementor;
/**
 * @package     WordPress
 * @subpackage  Gum Elementor Addon
 * @author      support@themegum.com
 * @since       1.0.4
*/
defined('ABSPATH') or die();

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;

class Popover_Regular_Btn_Widget extends Widget_Base {


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
    return 'gum_popover';
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

    return esc_html__( 'Popover Button', 'gum-elementor-addon' );
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
    return 'eicon-image-hotspot';
  }

  public function get_keywords() {
    return [ 'wordpress', 'widget', 'button','popup','modal','spot' ];
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


  public static function get_button_sizes() {
    return [
      'xs' => esc_html__( 'Extra Small', 'gum-elementor-addon' ),
      'sm' => esc_html__( 'Small', 'gum-elementor-addon' ),
      'md' => esc_html__( 'Medium', 'gum-elementor-addon' ),
      'lg' => esc_html__( 'Large', 'gum-elementor-addon' ),
      'xl' => esc_html__( 'Extra Large', 'gum-elementor-addon' ),
    ];
  }


  protected function _register_controls() {

    $this->start_controls_section(
      'button_title',
      [
        'label' => esc_html__( 'Button', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'button_text',
      [
        'label' => esc_html__( 'Text', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'dynamic' => [
          'active' => true,
        ],
        'ai' => [
          'active' => false,
        ],
        'default' => esc_html__( 'Click here', 'gum-elementor-addon' ),
      ]
    );

    $this->add_responsive_control(
      'button_align',
      [
        'label' => esc_html__( 'Alignment', 'gum-elementor-addon' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => esc_html__( 'Centered', 'gum-elementor-addon' ),
            'icon' => 'eicon-text-align-left',
          ],
          'center' => [
            'title' => esc_html__( 'Centered', 'gum-elementor-addon' ),
            'icon' => 'eicon-text-align-center',
          ],
          'right' => [
            'title' => esc_html__( 'Centered', 'gum-elementor-addon' ),
            'icon' => 'eicon-text-align-right',
          ],
          'justify' => [
            'title' => esc_html__( 'Full Width', 'gum-elementor-addon' ),
            'icon' => 'eicon-text-align-justify',
          ],
        ],
        'prefix_class' => 'elementor%s-align-',
        'default' => '',
      ]
    );

    $this->add_control(
      'size',
      [
        'label' => esc_html__( 'Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'md',
        'options' => self::get_button_sizes(),
        'style_transfer' => true,
      ]
    );

    $this->add_control(
      'selected_icon',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
      ]
    );

    $this->add_control(
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

    $this->add_control(
      'icon_indent',
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
        'condition' => ['selected_icon[value]!' => ''],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'pop_heading',
      [
        'label' => esc_html__( 'Popup Content', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'pop_align',
      [
        'label' => esc_html__( 'Position', 'gum-elementor-addon' ),
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
          'bottom' => [
            'title' => esc_html__( 'Bottom', 'gum-elementor-addon' ),
            'icon' => 'eicon-v-align-bottom',
          ],
        ],
        'default' => 'bottom',
      ]
    );

    $this->add_control(
      'pop_title',
      [
        'label' => esc_html__( 'Title', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXT,
        'dynamic' => [
          'active' => true,
        ],
        'ai' => [
          'active' => false,
        ],
        'default' => '',
        'placeholder' => esc_html__( 'Enter your title', 'gum-elementor-addon' ),
        'label_block' => true,
      ]
    );

    $this->add_control(
      'pop_text',
      [
        'label' => esc_html__( 'Text', 'gum-elementor-addon' ),
        'type' => Controls_Manager::TEXTAREA,
        'dynamic' => [
          'active' => true,
        ],
        'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'gum-elementor-addon' ),
        'placeholder' => esc_html__( 'Enter your description', 'gum-elementor-addon' ),
        'rows' => 10,
        'show_label' => false,
      ]
    );

    $this->end_controls_section();

/*
 * style params
 */

    $this->start_controls_section(
      'button_styles',
      [
        'label' => esc_html__( 'Button', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'typography_title',
        'selector' => '{{WRAPPER}} .elementor-button',
      ]
    );

    $this->start_controls_tabs( 'tabs_button_style' );

    $this->start_controls_tab(
      'tab_button_normal',
      [
        'label' => esc_html__( 'Normal', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'button_text_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'fill: {{VALUE}};color: {{VALUE}};',
        ]
      ]
    );

    $this->add_control(
      'button_background_color',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_tab();
    $this->start_controls_tab(
      'tab_button_hover',
      [
        'label' => esc_html__( 'Hover/Clicked', 'gum-elementor-addon' ),
      ]
    );

    $this->add_control(
      'button_hover_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}}!important;',
          '{{WRAPPER}} .elementor-button:hover svg, {{WRAPPER}} .elementor-button:focus svg' => 'fill: {{VALUE}}!important;',
          '{{WRAPPER}} .pop-it .elementor-button' => 'color: {{VALUE}}!important;',
        ],
      ]
    );

    $this->add_control(
      'button_background_hover_color',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};',
          '{{WRAPPER}} .pop-it  .elementor-button' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'button_hover_border_color',
      [
        'label' => esc_html__( 'Border Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'condition' => [
          'btn_border_border!' => '',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button:hover, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};',
          '{{WRAPPER}} .pop-it  .elementor-button' => 'border-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_responsive_control(
      'icon_rotate',
      [
        'label' => esc_html__( 'Icon Rotate', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'deg' ],
        'default' => [
          'size' => 0,
          'unit' => 'deg',
        ],
        'tablet_default' => [
          'unit' => 'deg',
        ],
        'mobile_default' => [
          'unit' => 'deg',
        ],
        'selectors' => [
          '{{WRAPPER}} .pop-it .elementor-button-icon i, {{WRAPPER}} .pop-it .elementor-button-icon svg' => 'transform: rotate({{SIZE}}{{UNIT}})',
        ],
      ]
    );

    $this->end_controls_tab();
    $this->end_controls_tabs();

    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'btn_border',
        'selector' => '{{WRAPPER}} .elementor-button',
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'btn_border_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ]
      ]
    );

    $this->add_responsive_control(
      'btn_text_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ]
      ]
    );

    $this->add_group_control(
      Group_Control_Box_Shadow::get_type(),
      [
        'name' => 'button_box_shadow',
        'selector' => '{{WRAPPER}} .elementor-button',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'popup_styles',
      [
        'label' => esc_html__( 'Popup Content', 'gum-elementor-addon' ),
        'tab'   => Controls_Manager::TAB_STYLE,
      ]
    );    


    $this->add_control(
      'pop_width',
      [
        'label' => esc_html__( 'Width', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 100,
            'max' => 1000,
            'step'=> 1
          ],
          'default' => ['value'=> -1,'unit'=>'px']
        ],
        'selectors' => [
          '[data-elementor-device-mode=tablet] {{WRAPPER}} .popover-box' => 'width: {{SIZE}}{{UNIT}};',
          '[data-elementor-device-mode=desktop] {{WRAPPER}} .popover-box' => 'width: {{SIZE}}{{UNIT}};'
        ]
      ]
    );


    $this->add_control(
      'pop_bgcolor',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .popover-box' => 'background-color: {{VALUE}};',
          '{{WRAPPER}} .popover-box.pop-bottom::after' => 'border-bottom-color: {{VALUE}};',
          '{{WRAPPER}} .popover-box.pop-top::after' => 'border-top-color: {{VALUE}};',
          '{{WRAPPER}} .popover-box.pop-left::after' => 'border-left-color: {{VALUE}};',
          '{{WRAPPER}} .popover-box.pop-right::after' => 'border-right-color: {{VALUE}};',
        ],
      ]
    );


    $this->add_responsive_control(
      'pop_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .popover-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'pop_border_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .popover-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ]
      ]
    );

    $this->add_control(
      'pop_border',
      [
        'label' => esc_html__( 'Border Type', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::SELECT,
        'default' => '',
        'options' => [
          '' => esc_html__( 'None', 'gum-elementor-addon' ),
          'solid' => esc_html__( 'Solid', 'gum-elementor-addon' ),
          'double' => esc_html__( 'Double', 'gum-elementor-addon' ),
          'dotted' => esc_html__( 'Dotted', 'gum-elementor-addon' ),
          'dashed' => esc_html__( 'Dashed', 'gum-elementor-addon' ),
          'groove' => esc_html__( 'Groove', 'gum-elementor-addon' ),
        ],
        'selectors' => [
          '{{WRAPPER}} .popover-box' => 'border-style: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'pop_border_width',
      [
        'label' => esc_html__( 'Border Width', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::DIMENSIONS,
        'condition' => [
          'pop_border!' => '',
        ],
        'selectors' => [
          '{{WRAPPER}} .popover-box' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          '{{WRAPPER}} .popover-box.pop-bottom:before' => 'border-width: calc( 10px + {{TOP}}{{UNIT}} );',
          '{{WRAPPER}} .popover-box.pop-top:before' => 'border-width: calc( 10px + {{BOTTOM}}{{UNIT}} );',
          '{{WRAPPER}} .popover-box.pop-left:before' => 'border-width: calc( 10px + {{RIGHT}}{{UNIT}} );',
          '{{WRAPPER}} .popover-box.pop-right:before' => 'border-width: calc( 10px + {{LEFT}}{{UNIT}} );',
        ],
      ]
    );

    $this->add_control(
      'pop_border_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .popover-box' => 'border-color: {{VALUE}};',
          '{{WRAPPER}} .popover-box.pop-bottom:before' => 'border-bottom-color: {{VALUE}};',
          '{{WRAPPER}} .popover-box.pop-top:before' => 'border-top-color: {{VALUE}};',
          '{{WRAPPER}} .popover-box.pop-left:before' => 'border-left-color: {{VALUE}};',
          '{{WRAPPER}} .popover-box.pop-right:before' => 'border-right-color: {{VALUE}};',
        ],
        'condition' => [
          'pop_border!' => '',
        ],
      ]
    );


    $this->add_group_control(
      Group_Control_Box_Shadow::get_type(),
      [
        'name' => 'pop_box_shadow',
        'selector' => '{{WRAPPER}} .popover-box',
      ]
    );


    $this->add_control(
      'pop_box_separator',
      [
        'type' =>  Controls_Manager::HIDDEN,
        'separator' => 'before',
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'label' => esc_html__( 'Title', 'gum-elementor-addon' ),
        'name' => 'typography_pop_title',
        'selector' => '{{WRAPPER}} .popover-box h4',
        'separator' => 'before',
      ]
    );


    $this->add_control(
      'pop_title_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .popover-box h4' => 'color: {{VALUE}};',
        ]
      ]
    );

    $this->add_responsive_control(
      'pop_title_margin',
      [
        'label' => esc_html__( 'Spacing', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -50,
            'max' => 500,
            'step'=> 1
          ],
          'default' => ['value'=> -1,'unit'=>'px']
        ],
        'selectors' => [
          '{{WRAPPER}} {{WRAPPER}} .popover-box h4' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
        'separator' => 'after',
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'label' => esc_html__( 'Text', 'gum-elementor-addon' ),
        'name' => 'typography_pop_text',
        'selector' => '{{WRAPPER}} .popover-box > div',
      ]
    );


    $this->add_control(
      'pop_text_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' =>  Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .popover-box > div' => 'color: {{VALUE}};',
        ]
      ]
    );


    $this->end_controls_section();

  }

  protected function render() {

    $settings = $this->get_settings_for_display();

    extract( $settings );

    $this->add_render_attribute( 'wrapper', 'class', 'popover-button-wrapper' );

    $this->add_render_attribute( 'button', 'class', [
      'elementor-button',
      'popover-button',
      'elementor-size-' . $size,
      'elementor-button-align-'.$button_align
    ] );



    $this->add_link_attributes( 'button', array('url' => '#') );
    $this->add_render_attribute( 'button_text', 'class', 'elementor-button-text');
    $this->add_inline_editing_attributes( 'button_text', 'none' );

    $button_html = $button_icon = '';


    if(!empty($selected_icon['value'])){


      ob_start();
      Icons_Manager::render_icon( $selected_icon, [ 'aria-hidden' => 'true' ] );
      $icon = ob_get_clean();

       $button_icon = '<span class="elementor-button-icon elementor-align-icon-'.esc_attr_e($icon_align).'">'.$icon.'</span>';
    }

    $button_html .= '<span '.$this->get_render_attribute_string( 'button_text' ).'>'.esc_html($button_text).'</span>';

    ?>
    <div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>><a <?php echo $this->get_render_attribute_string( 'button' ); ?> data-pop="<?php esc_attr_e($pop_title);?>" data-pop-text="<?php esc_attr_e($pop_text);?>" data-pop-align="<?php esc_attr_e($pop_align);?>"><span class="elementor-button-content-wrapper"><?php print $button_icon.$button_html; ?></span></a>
    </div>
<?php

  }

  public function enqueue_script( ) {

    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());
    wp_enqueue_script( 'gum-elementor-addon', GUM_ELEMENTOR_URL . 'js/allscripts.js', array('jquery'), '1.0', false );
  }


}


// Register widget
\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Popover_Regular_Btn_Widget() );

?>