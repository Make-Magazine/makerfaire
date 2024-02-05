<?php
namespace Elementor;

/**
 * @package     WordPress
 * @subpackage  Gum Elementor Addon
 * @author      support@themegum.com
 * @since       1.0.3
*/

defined('ABSPATH') or die();


class Gum_Elementor_Section_Widget{


  public function __construct( ) {

        add_action( 'elementor/element/section/section_advanced/after_section_end', array( $this, 'register_section_controls') , 999 );
        add_action( 'elementor/element/column/_section_responsive/after_section_end', array( $this, 'register_advanced_column_controls') , 999 );
        add_action( 'elementor/element/column/section_background_overlay/after_section_end', array( $this, 'register_column_controls') , 999 );
        add_action( 'elementor/element/heading/section_title_style/after_section_end', array( $this, 'register_widget_heading_style_controls') , 999 );
        add_action( 'elementor/element/image/section_style_image/after_section_end', array( $this, 'register_widget_image_style_controls') , 999 );


        /* since 1.3.0 */
        add_action( 'elementor/element/container/section_background_overlay/after_section_end', array( $this, 'register_container_controls') , 999 );


  }

  public function register_container_controls( Controls_Stack $element ) {


    /**
    * - Adding image mask background
    * @since 1.2.11
    * 
    */


    $element->update_control(
      'background_color_stop',
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
          'background_background' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $element->update_control(
      'background_color_b_stop',
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
          'background_background' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );


    $element->update_control(
      'background_hover_color_stop',
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
          'background_hover_background' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $element->update_control(
      'background_hover_color_b_stop',
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
          'background_hover_background' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $element->update_control(
      'background_overlay_color_stop',
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
          'background_overlay_background' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );


    $element->update_control(
      'background_overlay_color_b_stop',
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
          'background_overlay_background' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );


    $element->update_control(
      'background_overlay_hover_color_stop',
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
          'background_overlay_hover_background' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $element->update_control(
      'background_overlay_hover_color_b_stop',
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
          'background_overlay_hover_background' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );
    $element->start_injection( [
      'of' => 'overlay_blend_mode',
    ] );


    $element->add_control(
      '_overlay_maskimage_description',
      [
        'raw' => '<strong>' . esc_html__( 'Please note!', 'elementor' ) . '</strong> ' . esc_html__( 'Image mask only actived when overlay color background not empty.', 'gum-elementor-addon' ),
        'type' => Controls_Manager::RAW_HTML,
        'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
        'render_type' => 'ui',
        'condition' => [
          'background_overlay_background' => [ 'classic', 'gradient' ],
        ],
      ]
    );

    $element->add_control(
      'background_overlay_maskimage',
      [
        'label' => esc_html__( 'Image Mask', 'gum-elementor-addon' ),
        'type' => Controls_Manager::MEDIA,
        'media_type' => 'image',
        'should_include_svg_inline_option' => true,
        'library_type' => 'image/svg+xml',
        'dynamic' => [
          'active' => true,
        ],
        'selectors' => [ '{{WRAPPER}}::before' => '-webkit-mask-image: url("{{URL}}");',
        ],
        'render_type' => 'template',
        'condition' => [
          'background_overlay_background' => [ 'classic', 'gradient' ],
        ],
      ]
    );


    $element->add_responsive_control(
      'overlay_mask_size',
      [
        'label' => esc_html__( 'Mask Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'contain' => esc_html__( 'Fit', 'elementor' ),
          'cover' => esc_html__( 'Fill', 'elementor' ),
          'custom' => esc_html__( 'Custom', 'elementor' ),
        ],
        'default' => 'contain',
        'selectors' => [ '{{WRAPPER}}::before' => '-webkit-mask-size: {{VALUE}};' ],
        'condition' => [
          'background_overlay_maskimage[url]!' => '',
        ],
      ]
    );

    $element->add_responsive_control(
      'overlay_mask_size_scale',
      [
        'label' => esc_html__( 'Mask Scale', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'em', '%', 'vw' ],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 500,
          ],
          'em' => [
            'min' => 0,
            'max' => 100,
          ],
          '%' => [
            'min' => 0,
            'max' => 200,
          ],
          'vw' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'default' => [
          'unit' => '%',
          'size' => 100,
        ],
        'selectors' => [ '{{WRAPPER}}::before' => '-webkit-mask-size: {{SIZE}}{{UNIT}};' ],
        'condition' => [
          'background_overlay_maskimage[url]!' => '',
          'overlay_mask_size' => 'custom',
        ],
      ]
    );


    $element->add_responsive_control(
      'overlay_mask_position',
      [
        'label' => esc_html__( 'Mask Position', 'gum-elementor-addon' ),
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
          'custom' => esc_html__( 'Custom', 'elementor' ),
        ],
        'default' => 'center center',
        'selectors' => [ '{{WRAPPER}}::before' =>  '-webkit-mask-position: {{VALUE}};' ],
        'condition' => [
          'background_overlay_maskimage[url]!' => '',
        ],
      ]
    );


    $element->add_responsive_control(
      'overlay_mask_position_x',
      [
        'label' => esc_html__( 'Mask X Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'em', '%', 'vw' ],
        'range' => [
          'px' => [
            'min' => -500,
            'max' => 500,
          ],
          'em' => [
            'min' => -100,
            'max' => 100,
          ],
          '%' => [
            'min' => -100,
            'max' => 100,
          ],
          'vw' => [
            'min' => -100,
            'max' => 100,
          ],
        ],
        'default' => [
          'unit' => '%',
          'size' => 0,
        ],
        'selectors' => [ '{{WRAPPER}}::before' =>  '-webkit-mask-position-x: {{SIZE}}{{UNIT}};' ],
        'condition' => [
          'background_overlay_maskimage[url]!' => '',
          'overlay_mask_position' => 'custom',
        ],
      ]
    );

    $element->add_responsive_control(
      'overlay_mask_position_y',
      [
        'label' => esc_html__( 'Mask Y Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'em', '%', 'vw' ],
        'range' => [
          'px' => [
            'min' => -500,
            'max' => 500,
          ],
          'em' => [
            'min' => -100,
            'max' => 100,
          ],
          '%' => [
            'min' => -100,
            'max' => 100,
          ],
          'vw' => [
            'min' => -100,
            'max' => 100,
          ],
        ],
        'default' => [
          'unit' => '%',
          'size' => 0,
        ],
        'selectors' => [ '{{WRAPPER}}::before' =>  '-webkit-mask-position-y: {{SIZE}}{{UNIT}};' ],
        'condition' => [
          'background_overlay_maskimage[url]!' => '',
          'overlay_mask_position' => 'custom',
        ],
      ]
    );


    $element->add_responsive_control(
      'overlay_mask_repeat',
      [
        'label' => esc_html__( 'Mask Repeat', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'no-repeat' => esc_html__( 'No-Repeat', 'elementor' ),
          'repeat' => esc_html__( 'Repeat', 'elementor' ),
          'repeat-x' => esc_html__( 'Repeat-X', 'elementor' ),
          'repeat-Y' => esc_html__( 'Repeat-Y', 'elementor' ),
          'round' => esc_html__( 'Round', 'elementor' ),
          'space' => esc_html__( 'Space', 'elementor' ),
        ],
        'default' => 'no-repeat',
        'selectors' => [ '{{WRAPPER}}::before' => '-webkit-mask-repeat: {{VALUE}};' ],
        'condition' => [
          'background_overlay_maskimage[url]!' => '',
          'overlay_mask_size!' => 'cover',
        ],
      ]
    );

    $element->end_injection();
    

  }

  public function register_column_controls( Controls_Stack $element ) {


    /**
    * - Adding image mask background
    * @since 1.2.11
    * 
    */


    $element->update_control(
      'background_color_stop',
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
          'background_background' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $element->update_control(
      'background_color_b_stop',
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
          'background_background' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );


    $element->update_control(
      'background_hover_color_stop',
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
          'background_hover_background' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $element->update_control(
      'background_hover_color_b_stop',
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
          'background_hover_background' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );
    
    $element->start_injection( [
      'of' => 'overlay_blend_mode',
    ] );


    $element->add_control(
      '_overlay_maskimage_description',
      [
        'raw' => '<strong>' . esc_html__( 'Please note!', 'elementor' ) . '</strong> ' . esc_html__( 'Image mask only actived when overlay color background not empty.', 'gum-elementor-addon' ),
        'type' => Controls_Manager::RAW_HTML,
        'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
        'render_type' => 'ui',
        'condition' => [
          'background_overlay_background' => [ 'classic', 'gradient' ],
        ],
      ]
    );

    $element->add_control(
      'background_overlay_maskimage',
      [
        'label' => esc_html__( 'Image Mask', 'gum-elementor-addon' ),
        'type' => Controls_Manager::MEDIA,
        'media_type' => 'image',
        'should_include_svg_inline_option' => true,
        'library_type' => 'image/svg+xml',
        'dynamic' => [
          'active' => true,
        ],
        'selectors' => [
          '{{WRAPPER}} > .elementor-element-populated > .elementor-background-overlay' => '-webkit-mask-image: url("{{URL}}");',
        ],
        'render_type' => 'template',
        'condition' => [
          'background_overlay_background' => [ 'classic', 'gradient' ],
        ],
      ]
    );


    $element->add_responsive_control(
      'overlay_mask_size',
      [
        'label' => esc_html__( 'Mask Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'contain' => esc_html__( 'Fit', 'elementor' ),
          'cover' => esc_html__( 'Fill', 'elementor' ),
          'custom' => esc_html__( 'Custom', 'elementor' ),
        ],
        'default' => 'contain',
        'selectors' => [ '{{WRAPPER}} > .elementor-element-populated > .elementor-background-overlay' => '-webkit-mask-size: {{VALUE}};' ],
        'condition' => [
          'background_overlay_maskimage[url]!' => '',
        ],
      ]
    );

    $element->add_responsive_control(
      'overlay_mask_size_scale',
      [
        'label' => esc_html__( 'Mask Scale', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'em', '%', 'vw' ],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 500,
          ],
          'em' => [
            'min' => 0,
            'max' => 100,
          ],
          '%' => [
            'min' => 0,
            'max' => 200,
          ],
          'vw' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'default' => [
          'unit' => '%',
          'size' => 100,
        ],
        'selectors' => [ '{{WRAPPER}} > .elementor-element-populated > .elementor-background-overlay' => '-webkit-mask-size: {{SIZE}}{{UNIT}};' ],
        'condition' => [
          'background_overlay_maskimage[url]!' => '',
          'overlay_mask_size' => 'custom',
        ],
      ]
    );


    $element->add_responsive_control(
      'overlay_mask_position',
      [
        'label' => esc_html__( 'Mask Position', 'gum-elementor-addon' ),
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
          'custom' => esc_html__( 'Custom', 'elementor' ),
        ],
        'default' => 'center center',
        'selectors' => [ '{{WRAPPER}} > .elementor-element-populated > .elementor-background-overlay' =>  '-webkit-mask-position: {{VALUE}};' ],
        'condition' => [
          'background_overlay_maskimage[url]!' => '',
        ],
      ]
    );


    $element->add_responsive_control(
      'overlay_mask_position_x',
      [
        'label' => esc_html__( 'Mask X Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'em', '%', 'vw' ],
        'range' => [
          'px' => [
            'min' => -500,
            'max' => 500,
          ],
          'em' => [
            'min' => -100,
            'max' => 100,
          ],
          '%' => [
            'min' => -100,
            'max' => 100,
          ],
          'vw' => [
            'min' => -100,
            'max' => 100,
          ],
        ],
        'default' => [
          'unit' => '%',
          'size' => 0,
        ],
        'selectors' => [ '{{WRAPPER}} > .elementor-element-populated > .elementor-background-overlay' =>  '-webkit-mask-position-x: {{SIZE}}{{UNIT}};' ],
        'condition' => [
          'background_overlay_maskimage[url]!' => '',
          'overlay_mask_position' => 'custom',
        ],
      ]
    );

    $element->add_responsive_control(
      'overlay_mask_position_y',
      [
        'label' => esc_html__( 'Mask Y Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'em', '%', 'vw' ],
        'range' => [
          'px' => [
            'min' => -500,
            'max' => 500,
          ],
          'em' => [
            'min' => -100,
            'max' => 100,
          ],
          '%' => [
            'min' => -100,
            'max' => 100,
          ],
          'vw' => [
            'min' => -100,
            'max' => 100,
          ],
        ],
        'default' => [
          'unit' => '%',
          'size' => 0,
        ],
        'selectors' => [ '{{WRAPPER}} > .elementor-element-populated > .elementor-background-overlay' =>  '-webkit-mask-position-y: {{SIZE}}{{UNIT}};' ],
        'condition' => [
          'background_overlay_maskimage[url]!' => '',
          'overlay_mask_position' => 'custom',
        ],
      ]
    );


    $element->add_responsive_control(
      'overlay_mask_repeat',
      [
        'label' => esc_html__( 'Mask Repeat', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'no-repeat' => esc_html__( 'No-Repeat', 'elementor' ),
          'repeat' => esc_html__( 'Repeat', 'elementor' ),
          'repeat-x' => esc_html__( 'Repeat-X', 'elementor' ),
          'repeat-Y' => esc_html__( 'Repeat-Y', 'elementor' ),
          'round' => esc_html__( 'Round', 'elementor' ),
          'space' => esc_html__( 'Space', 'elementor' ),
        ],
        'default' => 'no-repeat',
        'selectors' => [ '{{WRAPPER}} > .elementor-element-populated > .elementor-background-overlay' => '-webkit-mask-repeat: {{VALUE}};' ],
        'condition' => [
          'background_overlay_maskimage[url]!' => '',
          'overlay_mask_size!' => 'cover',
        ],
      ]
    );


    $element->end_injection();

  }

  public function register_section_controls( Controls_Stack $element ) {


    /**
    * - Add inner section width option like top section
    * Change range from px only to px, vh, and vw
    *
    * - position option like common widget
    */

    $element->update_control(
      'background_color_stop',
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
          'background_background' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $element->update_control(
      'background_color_b_stop',
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
          'background_background' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );


    $element->update_control(
      'background_hover_color_stop',
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
          'background_hover_background' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $element->update_control(
      'background_hover_color_b_stop',
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
          'background_hover_background' => [ 'gradient' ],
        ],
        'of_type' => 'gradient',
      ]
    );

    $element->update_responsive_control(
      'custom_height_inner',
      [
        'label' => esc_html__( 'Minimum Height', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 400,
        ],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 1440,
          ],
          'vh' => [
            'min' => 0,
            'max' => 100,
          ],
          'vw' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'size_units' => [ 'px', 'vh', 'vw' ],
        'selectors' => [
          '{{WRAPPER}} > .elementor-container' => 'min-height: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'height_inner' => [ 'min-height' ],
        ],
        'hide_in_top' => true,
      ]
    );

    $element->start_controls_section(
      '_section_position',
      [
        'label' => esc_html__( 'Positioning', 'elementor' ),
        'tab' => Controls_Manager::TAB_ADVANCED,
        'hide_in_top' => true,
      ]
    );

    $element->add_responsive_control(
      '_element_width',
      [
        'label' => esc_html__( 'Width', 'elementor' ),
        'type' => Controls_Manager::SELECT,
        'default' => '',
        'options' => [
          '' => esc_html__( 'Default', 'elementor' ),
          'inherit' => esc_html__( 'Full Width', 'elementor' ) . ' (100%)',
          'auto' => esc_html__( 'Inline', 'elementor' ) . ' (auto)',
          'initial' => esc_html__( 'Custom', 'elementor' ),
        ],
        'selectors_dictionary' => [
          'inherit' => '100%',
        ],
        'prefix_class' => 'elementor-widget%s__width-',
        'selectors' => [
          '{{WRAPPER}}' => 'width: {{VALUE}}; max-width: {{VALUE}}',
        ],
      ]
    );

    $element->add_responsive_control(
      '_element_custom_width',
      [
        'label' => esc_html__( 'Custom Width', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 2000,
            'step' => 1,
          ],
          '%' => [
            'max' => 100,
            'step' => 1,
          ],
        ],
        'condition' => [
          '_element_width' => 'initial',
        ],
        'size_units' => [ 'px', '%', 'vw' ],
        'selectors' => [
          '{{WRAPPER}}' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}}',
        ],
      ]
    );

    $element->add_responsive_control(
      '_element_vertical_align',
      [
        'label' => esc_html__( 'Vertical Align', 'elementor' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'flex-start' => [
            'title' => esc_html__( 'Start', 'elementor' ),
            'icon' => 'eicon-v-align-top',
          ],
          'center' => [
            'title' => esc_html__( 'Center', 'elementor' ),
            'icon' => 'eicon-v-align-middle',
          ],
          'flex-end' => [
            'title' => esc_html__( 'End', 'elementor' ),
            'icon' => 'eicon-v-align-bottom',
          ],
        ],
        'condition' => [
          '_element_width!' => '',
          '_position' => '',
        ],
        'selectors' => [
          '{{WRAPPER}}' => 'align-self: {{VALUE}}',
        ],
      ]
    );

    $element->add_control(
      '_position_description',
      [
        'raw' => '<strong>' . esc_html__( 'Please note!', 'elementor' ) . '</strong> ' . esc_html__( 'Custom positioning is not considered best practice for responsive web design and should not be used too frequently.', 'elementor' ),
        'type' => Controls_Manager::RAW_HTML,
        'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
        'render_type' => 'ui',
        'condition' => [
          '_position!' => '',
        ],
      ]
    );

    $element->add_control(
      '_position',
      [
        'label' => esc_html__( 'Position', 'elementor' ),
        'type' => Controls_Manager::SELECT,
        'default' => '',
        'options' => [
          '' => esc_html__( 'Default', 'elementor' ),
          'absolute' => esc_html__( 'Absolute', 'elementor' ),
          'fixed' => esc_html__( 'Fixed', 'elementor' ),
        ],
        'prefix_class' => 'elementor-',
        'frontend_available' => true,
      ]
    );

    $start = is_rtl() ? esc_html__( 'Right', 'elementor' ) : esc_html__( 'Left', 'elementor' );
    $end = ! is_rtl() ? esc_html__( 'Right', 'elementor' ) : esc_html__( 'Left', 'elementor' );

    $element->add_control(
      '_offset_orientation_h',
      [
        'label' => esc_html__( 'Horizontal Orientation', 'elementor' ),
        'type' => Controls_Manager::CHOOSE,
        'toggle' => false,
        'default' => 'start',
        'options' => [
          'start' => [
            'title' => $start,
            'icon' => 'eicon-h-align-left',
          ],
          'end' => [
            'title' => $end,
            'icon' => 'eicon-h-align-right',
          ],
        ],
        'classes' => 'elementor-control-start-end',
        'render_type' => 'ui',
        'condition' => [
          '_position!' => '',
        ],
      ]
    );

    $element->add_responsive_control(
      '_offset_x',
      [
        'label' => esc_html__( 'Offset', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -1000,
            'max' => 1000,
            'step' => 1,
          ],
          '%' => [
            'min' => -200,
            'max' => 200,
          ],
          'vw' => [
            'min' => -200,
            'max' => 200,
          ],
          'vh' => [
            'min' => -200,
            'max' => 200,
          ],
        ],
        'default' => [
          'size' => '0',
        ],
        'size_units' => [ 'px', '%', 'vw', 'vh' ],
        'selectors' => [
          'body:not(.rtl) {{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}}',
          'body.rtl {{WRAPPER}}' => 'right: {{SIZE}}{{UNIT}}',
        ],
        'condition' => [
          '_offset_orientation_h!' => 'end',
          '_position!' => '',
        ],
      ]
    );

    $element->add_responsive_control(
      '_offset_x_end',
      [
        'label' => esc_html__( 'Offset', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -1000,
            'max' => 1000,
            'step' => 0.1,
          ],
          '%' => [
            'min' => -200,
            'max' => 200,
          ],
          'vw' => [
            'min' => -200,
            'max' => 200,
          ],
          'vh' => [
            'min' => -200,
            'max' => 200,
          ],
        ],
        'default' => [
          'size' => '0',
        ],
        'size_units' => [ 'px', '%', 'vw', 'vh' ],
        'selectors' => [
          'body:not(.rtl) {{WRAPPER}}' => 'right: {{SIZE}}{{UNIT}}',
          'body.rtl {{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}}',
        ],
        'condition' => [
          '_offset_orientation_h' => 'end',
          '_position!' => '',
        ],
      ]
    );

    $element->add_control(
      '_offset_orientation_v',
      [
        'label' => esc_html__( 'Vertical Orientation', 'elementor' ),
        'type' => Controls_Manager::CHOOSE,
        'toggle' => false,
        'default' => 'start',
        'options' => [
          'start' => [
            'title' => esc_html__( 'Top', 'elementor' ),
            'icon' => 'eicon-v-align-top',
          ],
          'end' => [
            'title' => esc_html__( 'Bottom', 'elementor' ),
            'icon' => 'eicon-v-align-bottom',
          ],
        ],
        'render_type' => 'ui',
        'condition' => [
          '_position!' => '',
        ],
      ]
    );

    $element->add_responsive_control(
      '_offset_y',
      [
        'label' => esc_html__( 'Offset', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -1000,
            'max' => 1000,
            'step' => 1,
          ],
          '%' => [
            'min' => -200,
            'max' => 200,
          ],
          'vh' => [
            'min' => -200,
            'max' => 200,
          ],
          'vw' => [
            'min' => -200,
            'max' => 200,
          ],
        ],
        'size_units' => [ 'px', '%', 'vh', 'vw' ],
        'default' => [
          'size' => '0',
        ],
        'selectors' => [
          '{{WRAPPER}}' => 'top: {{SIZE}}{{UNIT}}',
        ],
        'condition' => [
          '_offset_orientation_v!' => 'end',
          '_position!' => '',
        ],
      ]
    );

    $element->add_responsive_control(
      '_offset_y_end',
      [
        'label' => esc_html__( 'Offset', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -1000,
            'max' => 1000,
            'step' => 1,
          ],
          '%' => [
            'min' => -200,
            'max' => 200,
          ],
          'vh' => [
            'min' => -200,
            'max' => 200,
          ],
          'vw' => [
            'min' => -200,
            'max' => 200,
          ],
        ],
        'size_units' => [ 'px', '%', 'vh', 'vw' ],
        'default' => [
          'size' => '0',
        ],
        'selectors' => [
          '{{WRAPPER}}' => 'bottom: {{SIZE}}{{UNIT}}',
        ],
        'condition' => [
          '_offset_orientation_v' => 'end',
          '_position!' => '',
        ],
      ]
    );

    $element->end_controls_section();

    $element->start_controls_section(
      '_section_transform',
      [
        'label' => esc_html__( 'Transform', 'elementor' ),
        'tab' => Controls_Manager::TAB_ADVANCED,
        'hide_in_top' => true,
      ]
    );


    $element->add_control(
      "_transform_rotate_popover",
      [
        'label' => esc_html__( 'Rotate', 'elementor' ),
        'type' => Controls_Manager::POPOVER_TOGGLE,
        'prefix_class' => 'e-',
        'return_value' => 'transform',
      ]
    );

    $element->start_popover();

    $element->add_responsive_control(
      "_transform_rotateZ_effect",
      [
        'label' => esc_html__( 'Rotate', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -360,
            'max' => 360,
          ],
        ],
        'selectors' => [
          "{{WRAPPER}}" => '--e-transform-rw-rotateZ: {{SIZE}}deg',
        ],
        'condition' => [
          "_transform_rotate_popover!" => '',
        ],
        'frontend_available' => true,
      ]
    );

    $element->add_control(
      "_transform_rotate_3d",
      [
        'label' => esc_html__( '3D Rotate', 'elementor' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'On', 'elementor' ),
        'label_off' => esc_html__( 'Off', 'elementor' ),
        'selectors' => [
          "{{WRAPPER}}" => '--e-transform-rw-rotateX: 1deg;  --e-transform-rw-perspective: 20px;',
        ],
        'condition' => [
          "_transform_rotate_popover!" => '',
        ],
      ]
    );

    $element->add_responsive_control(
      "_transform_rotateX_effect",
      [
        'label' => esc_html__( 'Rotate X', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -360,
            'max' => 360,
          ],
        ],
        'condition' => [
          "_transform_rotate_3d!" => '',
          "_transform_rotate_popover!" => '',
        ],
        'selectors' => [
          "{{WRAPPER}}" => '--e-transform-rw-rotateX: {{SIZE}}deg;',
        ],
        'frontend_available' => true,
      ]
    );

    $element->add_responsive_control(
      "_transform_rotateY_effect",
      [
        'label' => esc_html__( 'Rotate Y', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -360,
            'max' => 360,
          ],
        ],
        'condition' => [
          "_transform_rotate_3d!" => '',
          "_transform_rotate_popover!" => '',
        ],
        'selectors' => [
          "{{WRAPPER}}" => '--e-transform-rw-rotateY: {{SIZE}}deg;',
        ],
        'frontend_available' => true,
      ]
    );

    $element->add_responsive_control(
      "_transform_perspective_effect",
      [
        'label' => esc_html__( 'Perspective', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 1000,
          ],
        ],
        'condition' => [
          "_transform_rotate_popover!" => '',
          "_transform_rotate_3d!" => '',
        ],
        'selectors' => [
          "{{WRAPPER}}" => '--e-transform-rw-perspective: {{SIZE}}px',
        ],
        'frontend_available' => true,
      ]
    );
    $element->end_popover();


    $element->add_control(
      "_transform_translate_popover",
      [
        'label' => esc_html__( 'Offset', 'elementor' ),
        'type' => Controls_Manager::POPOVER_TOGGLE,
        'prefix_class' => 'e-',
        'return_value' => 'transform',
      ]
    );


    $element->start_popover();

    $element->add_responsive_control(
      "_transform_translateX_effect",
      [
        'label' => esc_html__( 'Offset X', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ '%', 'px' ],
        'range' => [
          '%' => [
            'min' => -100,
            'max' => 100,
          ],
          'px' => [
            'min' => -1000,
            'max' => 1000,
          ],
        ],
        'condition' => [
          "_transform_translate_popover!" => '',
        ],
        'selectors' => [
          "{{WRAPPER}}" => '--e-transform-rw-translateX: {{SIZE}}{{UNIT}};',
        ],
        'frontend_available' => true,
      ]
    );

    $element->add_responsive_control(
      "_transform_translateY_effect",
      [
        'label' => esc_html__( 'Offset Y', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ '%', 'px' ],
        'range' => [
          '%' => [
            'min' => -100,
            'max' => 100,
          ],
          'px' => [
            'min' => -1000,
            'max' => 1000,
          ],
        ],
        'condition' => [
          "_transform_translate_popover!" => '',
        ],
        'selectors' => [
          "{{WRAPPER}}" => '--e-transform-rw-translateY: {{SIZE}}{{UNIT}};',
        ],
        'frontend_available' => true,
      ]
    );

    $element->end_popover();

    $element->add_control(
      "_transform_scale_popover",
      [
        'label' => esc_html__( 'Scale', 'elementor' ),
        'type' => Controls_Manager::POPOVER_TOGGLE,
        'prefix_class' => 'e-',
        'return_value' => 'transform',
      ]
    );


    $element->start_popover();

    $element->add_control(
      "_transform_keep_proportions",
      [
        'label' => esc_html__( 'Keep Proportions', 'elementor' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => esc_html__( 'On', 'elementor' ),
        'label_off' => esc_html__( 'Off', 'elementor' ),
        'default' => 'yes',
      ]
    );

    $element->add_responsive_control(
      "_transform_scale_effect",
      [
        'label' => esc_html__( 'Scale', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 2,
            'step' => 0.1,
          ],
        ],
        'condition' => [
          "_transform_scale_popover!" => '',
          "_transform_keep_proportions!" => '',
        ],
        'selectors' => [
          "{{WRAPPER}}" => '--e-transform-rw-scale: {{SIZE}};',
        ],
        'frontend_available' => true,
      ]
    );

    $element->add_responsive_control(
      "_transform_scaleX_effect",
      [
        'label' => esc_html__( 'Scale X', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 2,
            'step' => 0.1,
          ],
        ],
        'condition' => [
          "_transform_scale_popover!" => '',
          "_transform_keep_proportions" => '',
        ],
        'selectors' => [
          "{{WRAPPER}}" => '--e-transform-rw-scaleX: {{SIZE}};',
        ],
        'frontend_available' => true,
      ]
    );

    $element->add_responsive_control(
      "_transform_scaleY_effect",
      [
        'label' => esc_html__( 'Scale Y', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 2,
            'step' => 0.1,
          ],
        ],
        'condition' => [
          "_transform_scale_popover!" => '',
          "_transform_keep_proportions" => '',
        ],
        'selectors' => [
          "{{WRAPPER}}" => '--e-transform-rw-scaleY: {{SIZE}};',
        ],
        'frontend_available' => true,
      ]
    );

    $element->end_popover();

    $element->end_controls_section();


    $element->start_injection( [ 'of' => 'overlay_blend_mode' ] );
    $element->add_control(
      '_overlay_maskimage_description',
      [
        'raw' => '<strong>' . esc_html__( 'Please note!', 'elementor' ) . '</strong> ' . esc_html__( 'Image mask only actived when overlay color background not empty.', 'gum-elementor-addon' ),
        'type' => Controls_Manager::RAW_HTML,
        'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
        'render_type' => 'ui',
        'condition' => [
          'background_overlay_background' => [ 'classic', 'gradient' ],
        ],
      ]
    );

    $element->add_control(
      'background_overlay_maskimage',
      [
        'label' => esc_html__( 'Image Mask', 'gum-elementor-addon' ),
        'type' => Controls_Manager::MEDIA,
        'media_type' => 'image',
        'should_include_svg_inline_option' => true,
        'library_type' => 'image/svg+xml',
        'dynamic' => [
          'active' => true,
        ],
        'selectors' => [
          '{{WRAPPER}} > .elementor-background-overlay' => '-webkit-mask-image: url("{{URL}}");',
        ],
        'render_type' => 'template',
        'condition' => [
          'background_overlay_background' => [ 'classic', 'gradient' ],
        ],
      ]
    );



    $element->add_responsive_control(
      'overlay_mask_size',
      [
        'label' => esc_html__( 'Mask Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'contain' => esc_html__( 'Fit', 'elementor' ),
          'cover' => esc_html__( 'Fill', 'elementor' ),
          'custom' => esc_html__( 'Custom', 'elementor' ),
        ],
        'default' => 'contain',
        'selectors' => [ '{{WRAPPER}} > .elementor-background-overlay' => '-webkit-mask-size: {{VALUE}};' ],
        'condition' => [
          'background_overlay_maskimage[url]!' => '',
        ],
      ]
    );

    $element->add_responsive_control(
      'overlay_mask_size_scale',
      [
        'label' => esc_html__( 'Mask Scale', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'em', '%', 'vw' ],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 500,
          ],
          'em' => [
            'min' => 0,
            'max' => 100,
          ],
          '%' => [
            'min' => 0,
            'max' => 200,
          ],
          'vw' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'default' => [
          'unit' => '%',
          'size' => 100,
        ],
        'selectors' => [ '{{WRAPPER}} > .elementor-background-overlay' => '-webkit-mask-size: {{SIZE}}{{UNIT}};' ],
        'condition' => [
          'background_overlay_maskimage[url]!' => '',
          'overlay_mask_size' => 'custom',
        ],
      ]
    );


    $element->add_responsive_control(
      'overlay_mask_position',
      [
        'label' => esc_html__( 'Mask Position', 'gum-elementor-addon' ),
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
          'custom' => esc_html__( 'Custom', 'elementor' ),
        ],
        'default' => 'center center',
        'selectors' => [ '{{WRAPPER}} > .elementor-background-overlay' =>  '-webkit-mask-position: {{VALUE}};' ],
        'condition' => [
          'background_overlay_maskimage[url]!' => '',
        ],
      ]
    );


    $element->add_responsive_control(
      'overlay_mask_position_x',
      [
        'label' => esc_html__( 'Mask X Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'em', '%', 'vw' ],
        'range' => [
          'px' => [
            'min' => -500,
            'max' => 500,
          ],
          'em' => [
            'min' => -100,
            'max' => 100,
          ],
          '%' => [
            'min' => -100,
            'max' => 100,
          ],
          'vw' => [
            'min' => -100,
            'max' => 100,
          ],
        ],
        'default' => [
          'unit' => '%',
          'size' => 0,
        ],
        'selectors' => [ '{{WRAPPER}} > .elementor-background-overlay' =>  '-webkit-mask-position-x: {{SIZE}}{{UNIT}};' ],
        'condition' => [
          'background_overlay_maskimage[url]!' => '',
          'overlay_mask_position' => 'custom',
        ],
      ]
    );

    $element->add_responsive_control(
      'overlay_mask_position_y',
      [
        'label' => esc_html__( 'Mask Y Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'em', '%', 'vw' ],
        'range' => [
          'px' => [
            'min' => -500,
            'max' => 500,
          ],
          'em' => [
            'min' => -100,
            'max' => 100,
          ],
          '%' => [
            'min' => -100,
            'max' => 100,
          ],
          'vw' => [
            'min' => -100,
            'max' => 100,
          ],
        ],
        'default' => [
          'unit' => '%',
          'size' => 0,
        ],
        'selectors' => [ '{{WRAPPER}} > .elementor-background-overlay' =>  '-webkit-mask-position-y: {{SIZE}}{{UNIT}};' ],
        'condition' => [
          'background_overlay_maskimage[url]!' => '',
          'overlay_mask_position' => 'custom',
        ],
      ]
    );


    $element->add_responsive_control(
      'overlay_mask_repeat',
      [
        'label' => esc_html__( 'Mask Repeat', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'no-repeat' => esc_html__( 'No-Repeat', 'elementor' ),
          'repeat' => esc_html__( 'Repeat', 'elementor' ),
          'repeat-x' => esc_html__( 'Repeat-X', 'elementor' ),
          'repeat-Y' => esc_html__( 'Repeat-Y', 'elementor' ),
          'round' => esc_html__( 'Round', 'elementor' ),
          'space' => esc_html__( 'Space', 'elementor' ),
        ],
        'default' => 'no-repeat',
        'selectors' => [ '{{WRAPPER}} > .elementor-background-overlay' => '-webkit-mask-repeat: {{VALUE}};' ],
        'condition' => [
          'background_overlay_maskimage[url]!' => '',
          'overlay_mask_size!' => 'cover',
        ],
      ]
    );

    $element->end_injection();

  }


  public function register_advanced_column_controls( Controls_Stack $element ) {


    /**
    * - Adding responsive option 
    *
    * 
    */


    $element->start_injection( [ 'of' => '_section_responsive' ] );

    $active_breakpoints = array_reverse( Plugin::$instance->breakpoints->get_active_breakpoints() );

    foreach ( $active_breakpoints as $breakpoint_key => $breakpoint ) {
          $element->add_control(
            'reverse_order_' . $breakpoint_key,
            [
              'label' => esc_html__( 'Reverse Elements', 'gum-elementor-addon' ) . ' (' . $breakpoint->get_label() . ')',
              'type' => Controls_Manager::SWITCHER,
              'default' => '',
              'prefix_class' => 'elementor-column-',
              'return_value' => 'reverse-' . $breakpoint_key,
            ]
          );
        }

    $element->end_injection();

  }

  public function register_widget_heading_style_controls( Controls_Stack $element ) {

   $element->start_injection( [ 'of' => 'title_color' ] );

    $element->add_control(
      'title_hovercolor',
      [
        'label' => esc_html__( 'Link Hover', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'condition' => [
          'link[url]!' => '',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-heading-title:hover a' => 'color: {{VALUE}};',
        ],
      ]
    );

    $element->end_injection();


  }

 public function register_widget_image_style_controls( Controls_Stack $element ) {

   $element->start_injection( [ 'of' => 'object-fit' ] );

    $element->add_control(
      'object-postion',
      [
        'label' => esc_html__( 'Object Position', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SELECT,
        'condition' => [
          'height[size]!' => '',
          'object-fit' => 'contain',
        ],
        'options' => [
          '' => esc_html__( 'Middle', 'elementor' ),
          'top' => esc_html__( 'Top', 'elementor' ),
          'bottom' => esc_html__( 'Bottom', 'elementor' ),
        ],
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} img' => 'object-position: {{VALUE}};',
        ],
      ]
    );

    $element->end_injection();
  }


}




new \Elementor\Gum_Elementor_Section_Widget();
?>
