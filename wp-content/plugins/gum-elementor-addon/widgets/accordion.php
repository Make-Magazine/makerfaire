<?php
namespace Elementor;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
/**
 * @package     WordPress
 * @subpackage  Gum Elementor Addon
 * @author      support@themegum.com
 * @since       1.0.9
*/

defined('ABSPATH') or die();

class Gum_Elementor_Widget_Accordion{


  public function __construct( ) {

        add_action( 'elementor/element/accordion/section_title/after_section_end', array( $this, 'register_tabtitle_icon_style_controls') , 999 );
        add_action( 'elementor/element/before_section_start', array( $this, 'enqueue_script' ) );
        add_action( 'elementor/element/accordion/section_toggle_style_icon/after_section_end', array( $this, 'register_toggle_style_icon_controls') , 999 );
        add_action( 'elementor/element/accordion/section_toggle_style_title/after_section_end', array( $this, 'register_toggle_style_title_controls') , 999 );
        add_action( 'elementor/element/accordion/section_toggle_style_content/after_section_end', array( $this, 'register_toggle_style_content_controls') , 999 );

        add_action( 'elementor/element/accordion/section_title_style/after_section_end', array( $this, 'register_section_title_style_controls') , 999 );
        add_filter( 'elementor/widget/render_content', array( $this, 'registering_render_content') , 999, 2 );
        add_filter( 'elementor/widget/print_template', array( $this, 'print_content_template') , 999, 2 );
  }

  public function register_tabtitle_icon_style_controls( Controls_Stack $element ) {

    $repeater = new Repeater();

    $repeater->add_control(
      'tab_icon',
      [
        'label' => esc_html__( 'Icon', 'gum-elementor-addon' ),
        'type' => Controls_Manager::ICONS,
        'fa4compatibility' => 'icon',
      ]
    );

    $repeater->add_control(
      'tab_title',
      [
        'label' => esc_html__( 'Title & Description', 'elementor' ),
        'type' => Controls_Manager::TEXT,
        'default' => esc_html__( 'Accordion Title', 'elementor' ),
        'dynamic' => [
          'active' => true,
        ],
        'ai' => [
          'active' => false,
        ],
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'tab_content',
      [
        'label' => esc_html__( 'Content', 'elementor' ),
        'type' => Controls_Manager::WYSIWYG,
        'default' => esc_html__( 'Accordion Content', 'elementor' ),
        'show_label' => false,
      ]
    );


    $element->update_control(
      'tabs',
      [
        'label' => esc_html__( 'Accordion Items', 'elementor' ),
        'type' => Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [
          [
            'tab_icon' => '',
            'tab_title' => esc_html__( 'Accordion #1', 'elementor' ),
            'tab_content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor' ),
          ],
          [
            'tab_icon' => '',
            'tab_title' => esc_html__( 'Accordion #2', 'elementor' ),
            'tab_content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor' ),
          ],
        ],
        'title_field' => '{{{ elementor.helpers.renderIcon( this, tab_icon, {}, "i", "panel" ) || \'<i class="{{ icon }}" aria-hidden="true"></i> \' }}}{{{ tab_title }}}',
      ]
    );

  }

  public function register_section_title_style_controls( Controls_Stack $element ) {


   $element->start_injection( [
      'of' => 'border_color',
    ] );

    $element->add_control(
      'accordion_spacing',
      [
        'label' => esc_html__( 'Spacing', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-accordion-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .elementor-accordion-item:last-child' => 'margin-bottom: 0px;',
        ],
      ]
    );

    $element->end_injection();

  }

  public function register_toggle_style_content_controls( Controls_Stack $element ) {


   $element->start_injection( [
      'of' => 'content_padding',
    ] );


    $element->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'content_padding_border',
        'selector' => '{{WRAPPER}} .elementor-accordion-item .elementor-tab-content,{{WRAPPER}} .elementor-accordion-item .elementor-tab-content.elementor-active',
      ]
    );


   $element->end_injection();

   }



  public function register_toggle_style_title_controls( Controls_Stack $element ) {


   $element->start_injection( [
      'of' => 'section_toggle_style_title',
    ] );

    $element->remove_control('title_background');
    $element->remove_control('title_color');
    $element->remove_control('tab_active_color');

    $element->start_controls_tabs( '_tabs_toggle_style_title' );

    $element->start_controls_tab(
      '_tab_toggle_style_title_normal',
      [
        'label' => esc_html__( 'Normal', 'elementor' ),
      ]
    );


    $element->add_control(
      'title_color',
      [
        'label' => esc_html__( 'Color', 'elementor' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-accordion-icon, {{WRAPPER}} .elementor-accordion-title' => 'color: {{VALUE}};',
          '{{WRAPPER}} .elementor-accordion-icon svg' => 'fill: {{VALUE}};',
        ],
        'global' => [
          'default' => Global_Colors::COLOR_PRIMARY,
        ],
      ]
    );

    $element->add_control(
      'title_background',
      [
        'label' => esc_html__( 'Background', 'elementor' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-tab-title' => 'background-color: {{VALUE}};',
        ],
      ]
    );


    $element->add_control(
      'tab_active_color',
      [
        'label' => esc_html__( 'Active Color', 'elementor' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-active .elementor-accordion-icon, {{WRAPPER}} .elementor-active .elementor-accordion-title' => 'color: {{VALUE}};',
          '{{WRAPPER}} .elementor-active .elementor-accordion-icon svg' => 'fill: {{VALUE}};',
        ],
        'global' => [
          'default' => Global_Colors::COLOR_ACCENT,
        ],
      ]
    );

    $element->add_control(
      'title_active_background',
      [
        'label' => esc_html__( 'Active Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-tab-title.elementor-active' => 'background-color: {{VALUE}};',
        ],
        'separator' => 'after',
      ]
    );


    $element->end_controls_tab();

    $element->start_controls_tab(
      '_tab_toggle_style_title_hover',
      [
        'label' => esc_html__( 'Hover', 'elementor' ),
      ]
    );

    $element->add_control(
      'title_hover_color',
      [
        'label' => esc_html__( 'Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-tab-title:hover .elementor-accordion-title' => 'color: {{VALUE}}!important;',
        ],
      ]
    );

    $element->add_control(
      'title_hover_background',
      [
        'label' => esc_html__( 'Background', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-tab-title:hover' => 'background-color: {{VALUE}}!important;',
        ],
        'separator' => 'after',
      ]
    );

    $element->end_controls_tab();
    $element->end_controls_tabs();

    $element->end_injection();

    $element->start_injection( [
      'of' => 'title_padding',
    ] );


    $element->add_control(
      'toggle_title_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-accordion-item .elementor-tab-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $element->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'toggle_title_border',
        'selector' => '{{WRAPPER}} .elementor-accordion-item .elementor-tab-title,{{WRAPPER}} .elementor-accordion-item .elementor-tab-title.elementor-active',
      ]
    );

    $element->end_injection();


  }

  public function register_toggle_style_icon_controls( Controls_Stack $element ) {


    $element->start_injection( [
      'of' => 'icon_active_color',
    ] );


    $element->add_control(
      'icon_hover_color',
      [
        'label' => esc_html__( 'Hover Color', 'gum-elementor-addon' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-tab-title:hover .elementor-accordion-icon i:before,{{WRAPPER}} .elementor-tab-title:hover .elementor-accordion-icon svg' => 'color: {{VALUE}}!important;fill: {{VALUE}}!important;',
        ],
      ]
    );


    $element->add_responsive_control(
      'icon_size',
      [
        'label' => esc_html__( 'Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 14,
        ],
        'range' => [
          'px' => [
            'min' => 6,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-accordion-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .elementor-accordion-icon svg' => 'width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $element->add_responsive_control(
      'icon_offset',
      [
        'label' => esc_html__( 'Vertical Offset', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => -100,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-accordion-icon span' => 'margin-top: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $element->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'toggle_icon_border',
        'selector' => '{{WRAPPER}} .elementor-accordion-icon span i',
      ]
    );


    $element->add_responsive_control(
      'toggle_icon_padding',
      [
        'label' => esc_html__( 'Padding', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-accordion-icon span i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => ['toggle_icon_border_border!' => ''],
      ]
    );


    $element->add_control(
      'toggle_icon_radius',
      [
        'label' => esc_html__( 'Border Radius', 'gum-elementor-addon' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-accordion-icon span i' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => ['toggle_icon_border_border!' => ''],
      ]
    );


    $element->end_injection();


    $element->start_injection( [
      'of' => 'icon_space',
    ] );


    $element->add_control(
      'toggle_style_icon_heading',
      [
        'label' => esc_html__( 'TAB ICON', 'gum-elementor-addon' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $element->add_responsive_control(
      'tab_icon_size',
      [
        'label' => esc_html__( 'Size', 'gum-elementor-addon' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 14,
        ],
        'range' => [
          'px' => [
            'min' => 6,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-accordion-title i' => 'font-size: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .elementor-accordion-title svg' => 'width: {{SIZE}}{{UNIT}};',
        ],
      ]
    );


    $element->add_responsive_control(
      'tab_icon_space',
      [
        'label' => esc_html__( 'Spacing', 'elementor' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-accordion-title i, {{WRAPPER}} .elementor-accordion-title svg' => 'margin-right: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $element->end_injection();

  }

  function registering_render_content( $widget_content , $element ) {

    if('accordion' !== $element->get_name()) return $widget_content;

    ob_start();

    $settings = $element->get_settings_for_display();
    $migrated = isset( $settings['__fa4_migrated']['selected_icon'] );

    if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
      // @todo: remove when deprecated
      // added as bc in 2.6
      // add old default
      $settings['icon'] = 'fa fa-plus';
      $settings['icon_active'] = 'fa fa-minus';
      $settings['icon_align'] = $element->get_settings( 'icon_align' );
    }

    $is_new = empty( $settings['icon'] ) && Icons_Manager::is_migration_allowed();
    $has_icon = ( ! $is_new || ! empty( $settings['selected_icon']['value'] ) );
    $id_int = substr( $element->get_id_int(), 0, 3 );

    ?>
    <div class="elementor-accordion" role="tablist">
      <?php
      foreach ( $settings['tabs'] as $index => $item ) :
        $tab_count = $index + 1;

        $tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );
        $tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', $index );

//        $this->add_inline_editing_attributes( $tab_content_setting_key, 'advanced', $element );     
        ?>
        <div class="elementor-accordion-item">
          <<?php echo Utils::validate_html_tag( $settings['title_html_tag'] ); ?> <?php echo $element->get_render_attribute_string( $tab_title_setting_key ); ?>>
            <?php if ( $has_icon ) : ?>
              <span class="elementor-accordion-icon elementor-accordion-icon-<?php echo esc_attr( $settings['icon_align'] ); ?>" aria-hidden="true">
              <?php
              if ( $is_new || $migrated ) { ?>
                <span class="elementor-accordion-icon-closed"><?php Icons_Manager::render_icon( $settings['selected_icon'] ); ?></span>
                <span class="elementor-accordion-icon-opened"><?php Icons_Manager::render_icon( $settings['selected_active_icon'] ); ?></span>
              <?php } else { ?>
                <i class="elementor-accordion-icon-closed <?php echo esc_attr( $settings['icon'] ); ?>"></i>
                <i class="elementor-accordion-icon-opened <?php echo esc_attr( $settings['icon_active'] ); ?>"></i>
              <?php } ?>
              </span>
            <?php endif; ?>
            <a class="elementor-accordion-title" href=""><?php echo esc_html($item['tab_title']); 

            $iconHTML = '';

            if(!empty($item['tab_icon']['value'])){
                ob_start();
                Icons_Manager::render_icon( $item['tab_icon'], [ 'aria-hidden' => 'true' ] );
                echo ob_get_clean();
            }


          ?></a>
          </<?php echo Utils::validate_html_tag( $settings['title_html_tag'] ); ?>>
          <div <?php echo $element->get_render_attribute_string( $tab_content_setting_key ); ?>><?php echo $this->parse_text_editor( $item['tab_content'] , $element ); ?></div>
        </div>

      <?php endforeach; ?>
      <?php
      if ( isset( $settings['faq_schema'] ) && 'yes' === $settings['faq_schema'] ) {
        $json = [
          '@context' => 'https://schema.org',
          '@type' => 'FAQPage',
          'mainEntity' => [],
        ];

        foreach ( $settings['tabs'] as $index => $item ) {
          $json['mainEntity'][] = [
            '@type' => 'Question',
            'name' => wp_strip_all_tags( $item['tab_title'] ),
            'acceptedAnswer' => [
              '@type' => 'Answer',
              'text' => $this->parse_text_editor( $item['tab_content'] , $element ),
            ],
          ];
        }
        ?>
        <script type="application/ld+json"><?php echo wp_json_encode( $json ); ?></script>
      <?php } ?>
    </div>
    <?php

    $widget_content = ob_get_clean();

    return $widget_content;

  }

  function print_content_template($template_content , $element){

    if('accordion' !== $element->get_name()) return $template_content;

    ob_start();

    ?>
    <div class="elementor-accordion" role="tablist">
      <#
      if ( settings.tabs ) {
        var tabindex = view.getIDInt().toString().substr( 0, 3 ),
          iconHTML = elementor.helpers.renderIcon( view, settings.selected_icon, {}, 'i' , 'object' ),
          iconActiveHTML = elementor.helpers.renderIcon( view, settings.selected_active_icon, {}, 'i' , 'object' ),
          migrated = elementor.helpers.isIconMigrated( settings, 'selected_icon' );


        _.each( settings.tabs, function( item, index ) {
          var tabCount = index + 1,
            tabTitleKey = view.getRepeaterSettingKey( 'tab_title', 'tabs', index ),
            tabContentKey = view.getRepeaterSettingKey( 'tab_content', 'tabs', index ),
            tabiconHTML = elementor.helpers.renderIcon( view, item.tab_icon, {}, 'i' , 'object' );


          view.addRenderAttribute( tabTitleKey, {
            'id': 'elementor-tab-title-' + tabindex + tabCount,
            'class': [ 'elementor-tab-title' ],
            'tabindex': tabindex + tabCount,
            'data-tab': tabCount,
            'role': 'tab',
            'aria-controls': 'elementor-tab-content-' + tabindex + tabCount,
            'aria-expanded': 'false',
          } );

          view.addRenderAttribute( tabContentKey, {
            'id': 'elementor-tab-content-' + tabindex + tabCount,
            'class': [ 'elementor-tab-content', 'elementor-clearfix' ],
            'data-tab': tabCount,
            'role': 'tabpanel',
            'aria-labelledby': 'elementor-tab-title-' + tabindex + tabCount
          } );


          var titleHTMLTag = elementor.helpers.validateHTMLTag( settings.title_html_tag );
          #>
          <div class="elementor-accordion-item">
            <{{{ titleHTMLTag }}} {{{ view.getRenderAttributeString( tabTitleKey ) }}}>
              <# if ( settings.icon || settings.selected_icon ) { #>
              <span class="elementor-accordion-icon elementor-accordion-icon-{{ settings.icon_align }}" aria-hidden="true">
                <# if ( iconHTML && iconHTML.rendered && ( ! settings.icon || migrated ) ) { #>
                  <span class="elementor-accordion-icon-closed">{{{ iconHTML.value }}}</span>
                  <span class="elementor-accordion-icon-opened">{{{ iconActiveHTML.value }}}</span>
                <# } else { #>
                  <i class="elementor-accordion-icon-closed {{ settings.icon }}"></i>
                  <i class="elementor-accordion-icon-opened {{ settings.icon_active }}"></i>
                <# } #>
              </span>
              <# } #>
              <a class="elementor-accordion-title" href="">{{{ item.tab_title }}}<# 
                if ( item.tab_icon ) { if ( tabiconHTML && tabiconHTML.rendered ){ #>{{{ tabiconHTML.value }}}<# } else { #><i class="{{ item.tab_icon }}"></i><# }} #></a>
            </{{{ titleHTMLTag }}}>
            <div {{{ view.getRenderAttributeString( tabContentKey ) }}}>{{{ item.tab_content }}}</div>
          </div>
          <#
        } );
      } #>
    </div>
<?php         
  
    $template_content = ob_get_clean();

    return $template_content;

  }

  protected function parse_text_editor( $content , $element) {

    /** This filter is documented in wp-includes/widgets/class-wp-widget-text.php */
    $content = apply_filters( 'widget_text', $content, $element->get_settings() );

    $content = shortcode_unautop( $content );
    $content = do_shortcode( $content );
    $content = wptexturize( $content );

    if ( $GLOBALS['wp_embed'] instanceof \WP_Embed ) {
      $content = $GLOBALS['wp_embed']->autoembed( $content );
    }

    return $content;
  }


  protected function get_repeater_setting_key( $setting_key, $repeater_key, $repeater_item_index ) {
    return implode( '.', [ $repeater_key, $repeater_item_index, $setting_key ] );
  }

  protected function add_inline_editing_attributes( $key, $toolbar , $element) {
    if ( ! Plugin::$instance->editor->is_edit_mode() ) {
      return;
    }

    $element->add_render_attribute( $key, [
      'class' => 'elementor-inline-editing',
      'data-elementor-setting-key' => $key,
    ] );

    if ( 'basic' !== $toolbar ) {
      $element->add_render_attribute( $key, [
        'data-elementor-inline-editing-toolbar' => $toolbar,
      ] );
    }
  }

  public function enqueue_script( ) {
    wp_enqueue_style( 'gum-elementor-addon',GUM_ELEMENTOR_URL."css/style.css",array());
  }

}

new \Elementor\Gum_Elementor_Widget_Accordion();
?>
