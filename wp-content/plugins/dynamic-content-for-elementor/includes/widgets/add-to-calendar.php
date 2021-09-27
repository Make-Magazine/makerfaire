<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use DynamicContentForElementor\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class DCE_Widget_Calendar extends DCE_Widget_Prototype {

	protected function _register_controls() {

		$this->start_controls_section(
				'section_button',
				[
					'label' => __( 'Button', 'dynamic-content-for-elementor' ),
				]
		);

		$this->add_control(
				'button_type',
				[
					'label' => __( 'Type', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => '',
					'options' => [
						'' => __( 'Default', 'dynamic-content-for-elementor' ),
						'info' => __( 'Info', 'dynamic-content-for-elementor' ),
						'success' => __( 'Success', 'dynamic-content-for-elementor' ),
						'warning' => __( 'Warning', 'dynamic-content-for-elementor' ),
						'danger' => __( 'Danger', 'dynamic-content-for-elementor' ),
					],
					'prefix_class' => 'elementor-button-',
				]
		);

		$this->add_control(
				'text',
				[
					'label' => __( 'Text', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
					'default' => __( 'Add to Calendar', 'dynamic-content-for-elementor' ),
					'placeholder' => __( 'Add to Calendar', 'dynamic-content-for-elementor' ),
				]
		);

		$this->add_control(
				'link',
				[
					'label' => __( 'Link', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				]
		);
		$this->add_control(
				'is_external',
				[
					'label' => __( 'Open in a new window', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SWITCHER,
				]
		);
		$this->add_control(
				'nofollow',
				[
					'label' => __( 'Add nofollow', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SWITCHER,
				]
		);
		$this->add_control(
				'download',
				[
					'label' => __( 'Force Download', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'separator' => 'after',
				]
		);

		$this->add_responsive_control(
				'align',
				[
					'label' => __( 'Alignment', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => __( 'Left', 'dynamic-content-for-elementor' ),
							'icon' => 'eicon-text-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'dynamic-content-for-elementor' ),
							'icon' => 'eicon-text-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'dynamic-content-for-elementor' ),
							'icon' => 'eicon-text-align-right',
						],
						'justify' => [
							'title' => __( 'Justified', 'dynamic-content-for-elementor' ),
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
					'label' => __( 'Size', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'sm',
					'options' => Helper::get_button_sizes(),
					'style_transfer' => true,
				]
		);

		$this->add_control(
				'selected_icon',
				[
					'label' => __( 'Icon', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::ICONS,
					'label_block' => true,
					'fa4compatibility' => 'icon',
				]
		);

		$this->add_control(
				'icon_align',
				[
					'label' => __( 'Icon Position', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'left',
					'options' => [
						'left' => __( 'Before', 'dynamic-content-for-elementor' ),
						'right' => __( 'After', 'dynamic-content-for-elementor' ),
					],
					'condition' => [
						'selected_icon[value]!' => '',
					],
				]
		);

		$this->add_control(
				'icon_indent',
				[
					'label' => __( 'Icon Spacing', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'max' => 50,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
				]
		);
		$this->add_control(
				'icon_size',
				[
					'label' => __( 'Icon Size', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 10,
							'max' => 60,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .elementor-button .elementor-button-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
		);

		$this->add_control(
				'view',
				[
					'label' => __( 'View', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::HIDDEN,
					'default' => 'traditional',
				]
		);

		$this->add_control(
				'button_css_id',
				[
					'label' => __( 'Button ID', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
					'default' => '',
					'title' => __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'dynamic-content-for-elementor' ),
					'label_block' => false,
					'description' => __( 'Please make sure the ID is unique and not used elsewhere on the page where this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'dynamic-content-for-elementor' ),
					'separator' => 'before',
				]
		);

		$this->end_controls_section();

		$this->start_controls_section(
				'section_style',
				[
					'label' => __( 'Button', 'dynamic-content-for-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
				]
		);

		$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'typography',
					'selector' => '{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button',
				]
		);

		$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				[
					'name' => 'text_shadow',
					'selector' => '{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button',
				]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
				'tab_button_normal',
				[
					'label' => __( 'Normal', 'dynamic-content-for-elementor' ),
				]
		);

		$this->add_control(
				'button_text_color',
				[
					'label' => __( 'Text Color', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
					],
				]
		);

		$this->add_control(
				'background_color',
				[
					'label' => __( 'Background Color', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};',
					],
				]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
				'tab_button_hover',
				[
					'label' => __( 'Hover', 'dynamic-content-for-elementor' ),
				]
		);

		$this->add_control(
				'hover_color',
				[
					'label' => __( 'Text Color', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}};',
						'{{WRAPPER}} a.elementor-button:hover svg, {{WRAPPER}} .elementor-button:hover svg, {{WRAPPER}} a.elementor-button:focus svg, {{WRAPPER}} .elementor-button:focus svg' => 'fill: {{VALUE}};',
					],
				]
		);

		$this->add_control(
				'button_background_hover_color',
				[
					'label' => __( 'Background Color', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};',
					],
				]
		);

		$this->add_control(
				'button_hover_border_color',
				[
					'label' => __( 'Border Color', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'condition' => [
						'border_border!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};',
					],
				]
		);

		$this->add_control(
				'hover_animation',
				[
					'label' => __( 'Hover Animation', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::HOVER_ANIMATION,
				]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'border',
					'selector' => '{{WRAPPER}} .elementor-button',
					'separator' => 'before',
				]
		);

		$this->add_control(
				'border_radius',
				[
					'label' => __( 'Border Radius', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
		);

		$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'button_box_shadow',
					'selector' => '{{WRAPPER}} .elementor-button',
				]
		);

		$this->add_responsive_control(
				'text_padding',
				[
					'label' => __( 'Padding', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' => 'before',
				]
		);

		$this->end_controls_section();

		$this->start_controls_section(
				'section_calendar',
				[
					'label' => __( 'Calendar', 'dynamic-content-for-elementor' ),
				]
		);

		$format = array(
			'gcalendar' => 'Google Calendar',
			'ics' => 'ICS (iCal, Outlook, etc)',
		);
		$this->add_control(
				'dce_calendar_format',
				[
					'label' => __( 'Type', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'gcalendar' => [
							'title' => __( 'Google Calendar', 'dynamic-content-for-elementor' ),
							'icon' => 'eicon-globe',
						],
						'ics' => [
							'title' => __( 'ICS (iCal, Outlook, etc)', 'dynamic-content-for-elementor' ),
							'icon' => 'fa fa-calendar',
						],
					],
					'label_block' => true,
					'default' => 'gcalendar',
					'toggle' => false,
				]
		);

		$this->add_control(
				'dce_calendar_title', [
					'label' => __( 'Title', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
				]
		);

		$this->add_control(
				'dce_calendar_datetime_format',
				[
					'label' => __( 'Datetime Field', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'picker' => [
							'title' => __( 'Static Datetime Picker', 'dynamic-content-for-elementor' ),
							'icon' => 'eicon-date',
						],
						'string' => [
							'title' => __( 'Dynamic String', 'dynamic-content-for-elementor' ),
							'icon' => 'fa fa-i-cursor',
						],
					],
					'label_block' => true,
					'default' => 'string',
					'toggle' => false,
				]
		);
		$this->add_control(
				'dce_calendar_datetime_start', [
					'label' => __( 'DateTime Start', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::DATE_TIME,
					'label_block' => true,
					'condition' => [
						'dce_calendar_datetime_format' => 'picker',
					],
				]
		);
		$this->add_control(
				'dce_calendar_datetime_end', [
					'label' => __( 'DateTime End', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::DATE_TIME,
					'label_block' => true,
					'condition' => [
						'dce_calendar_datetime_format' => 'picker',
					],
				]
		);
		$this->add_control(
				'dce_calendar_datetime_string_format', [
					'label' => __( 'DateTime Format', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => 'Y-m-d H:i',
					'placeholder' => 'Y-m-d H:i',
					'label_block' => true,
					'condition' => [
						'dce_calendar_datetime_format' => 'string',
					],
				]
		);
		$this->add_control(
				'dce_calendar_datetime_start_string', [
					'label' => __( 'DateTime Start', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
					'condition' => [
						'dce_calendar_datetime_format' => 'string',
					],
				]
		);
		$this->add_control(
				'dce_calendar_datetime_end_string', [
					'label' => __( 'DateTime End', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
					'condition' => [
						'dce_calendar_datetime_format' => 'string',
					],
				]
		);

		$this->add_control(
				'dce_calendar_description', [
					'label' => __( 'Description', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::WYSIWYG,
				]
		);
		$this->add_control(
				'dce_calendar_location', [
					'label' => __( 'Location', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
				]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( $settings['dce_calendar_format'] == 'ics' ) {
			$cal_url = DCE_URL . 'assets/ics.php';
			$cal_url .= '?post_id=' . Helper::get_current_post_id();
			$cal_url .= '&element_id=' . $this->get_id();
			if ( is_singular() ) {
				$cal_url .= '&queried_id=' . get_the_ID();
			}
		} else {
			$cal_url = 'https://www.google.com/calendar/render?action=TEMPLATE';

			if ( $settings['dce_calendar_title'] ) {
				$cal_url .= '&text=' . urlencode( $settings['dce_calendar_title'] );
			}
			if ( $settings['dce_calendar_description'] ) {
				$cal_url .= '&details=' . urlencode( $settings['dce_calendar_description'] );
			}
			if ( $settings['dce_calendar_location'] ) {
				$cal_url .= '&location=' . urlencode( $settings['dce_calendar_location'] );
			}

			// FORMAT
			$date_format = $settings['dce_calendar_datetime_string_format'];
			if ( empty( $date_format ) ) {
				$date_format = 'Y-m-d H:i';
			}
			// START
			$start = ( $settings['dce_calendar_datetime_format'] != 'string' ) ? $settings['dce_calendar_datetime_start'] : $settings['dce_calendar_datetime_start_string'];
			$init_start = $start;
			if ( empty( $start ) ) {
				$start = new \DateTime();
			} else {
				$start = \DateTime::createFromFormat( $date_format, $start );
			}
			if ( $start ) {
				$cal_url .= '&dates=' . urlencode( get_gmt_from_date( $start->format( 'Y-m-d H:i' ), 'Ymd\\THi00\\Z' ) );
			}
			// END
			$end = ( $settings['dce_calendar_datetime_format'] != 'string' ) ? $settings['dce_calendar_datetime_end'] : $settings['dce_calendar_datetime_end_string'];
			$init_end = $end;
			if ( empty( $end ) && $start ) {
				$end = new \DateTime( $start->format( 'Y-m-d H:i' ) );
				$end = $end->modify( '+ 1 day' );
			} elseif ( empty( $end ) && ! $start ) {
				$end = new \DateTime();
			} else {
				$end = \DateTime::createFromFormat( $date_format, $end );
			}
			if ( $end ) {
				$cal_url .= '%2F' . urlencode( get_gmt_from_date( $end->format( 'Y-m-d H:i' ), 'Ymd\\THi00\\Z' ) );
			}

			if ( ( ! $start || ! $end ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				?>
				<div class="elementor-alert elementor-alert-danger">
					<h5 class="elementor-alert-title">Warning</h5>
					DateTime Format: <b><?php echo $date_format; ?></b><br>
					<?php if ( ! $start ) {
						?>Start date is wrong: <b><?php echo $init_start; ?></b><br><?php } ?>
				<?php if ( ! $end ) {
					?>End date is wrong: <b><?php echo $init_end; ?></b><?php } ?>
				</div>
				<?php
			}
			if ( get_option( 'timezone_string' ) ) {
				$cal_url .= '&ctz=' . get_option( 'timezone_string' );
			}
		}

		$this->add_render_attribute( 'wrapper', 'class', 'elementor-button-wrapper' );

		$this->add_render_attribute( 'button', 'href', $cal_url );
		$this->add_render_attribute( 'button', 'class', 'elementor-button-link' );
		$this->add_render_attribute( 'button', 'target', '_blank' );
		$this->add_render_attribute( 'button', 'rel', 'nofollow' );

		$this->add_render_attribute( 'button', 'class', 'elementor-button' );
		$this->add_render_attribute( 'button', 'role', 'button' );

		if ( ! empty( $settings['button_css_id'] ) ) {
			$this->add_render_attribute( 'button', 'id', sanitize_text_field( $settings['button_css_id'] ) );
		}

		if ( ! empty( $settings['size'] ) ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-size-' . sanitize_text_field( $settings['size'] ) );
		}

		if ( $settings['hover_animation'] ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-animation-' . sanitize_text_field( $settings['hover_animation'] ) );
		}
		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<a <?php echo $this->get_render_attribute_string( 'button' ); ?>>
		<?php $this->render_text(); ?>
			</a>
		</div>
		<?php
	}

	protected function render_text() {
		$settings = $this->get_settings_for_display();

		$migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
		$is_new = empty( $settings['icon'] ) && Icons_Manager::is_migration_allowed();

		if ( ! $is_new && empty( $settings['icon_align'] ) ) {
			// @todo: remove when deprecated
			// added as bc in 2.6
			//old default
			$settings['icon_align'] = $this->get_settings( 'icon_align' );
		}

		$this->add_render_attribute([
			'content-wrapper' => [
				'class' => [ 'elementor-button-content-wrapper', 'dce-flexbox' ],
			],
			'icon-align' => [
				'class' => [
					'elementor-button-icon',
					'elementor-align-icon-' . $settings['icon_align'],
				],
			],
			'text' => [
				'class' => 'elementor-button-text',
			],
		]);

		$this->add_inline_editing_attributes( 'text', 'none' );
		?>
		<span <?php echo $this->get_render_attribute_string( 'content-wrapper' ); ?>>
				<?php if ( ! empty( $settings['icon'] ) || ! empty( $settings['selected_icon']['value'] ) ) : ?>
				<span <?php echo $this->get_render_attribute_string( 'icon-align' ); ?>>
					<?php
					if ( $is_new || $migrated ) :
						Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );
					else :
						?>
						<i class="<?php echo esc_attr( $settings['icon'] ); ?>" aria-hidden="true"></i>
				<?php endif; ?>
				</span>
		<?php endif; ?>
			<span <?php echo $this->get_render_attribute_string( 'text' ); ?>><?php echo wp_kses_post( $settings['text'] ); ?></span>
		</span>
		<?php
	}

	public function on_import( $element ) {
		return Icons_Manager::on_import_migration( $element, 'icon', 'selected_icon' );
	}

}
