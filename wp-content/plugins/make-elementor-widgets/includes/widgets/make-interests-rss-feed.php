<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Make: Community Interests RSS feed
 *
 * Elementor widget should only work on Make: Community, as it pulls your interests from there
 *
 * @since 1.0.0
 */
class Elementor_makeInterestsRss_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'makeinterestsrss';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Make: Interests RSS feed', 'elementor-make-widget' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-custom';
	}

	/**
	 * Get widget categories.
	 */
	public function get_categories() {
		return [ 'make-category' ];
	}

	/**
	 * Get widget keywords.
	 */
	public function get_keywords() {
		return [ 'make', 'custom', 'community', 'rss'];
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'elementor-make-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

    	$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title (optional)', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your title', 'elementor-make-widget' ),
			]
		);
		$this->add_control(
			'link',
			[
				'label' => esc_html__( 'Link (defaults to site)', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Provide a link', 'elementor-make-widget' ),
			]
		);
		$this->add_control(
			'num_display',
			[
				'label' => esc_html__( 'Items to display', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 6,
				'min' => 1,
				'step' => 1,
			]
		);
		$this->add_control(
			'disp_order',
			[
				'label' => esc_html__( 'Display Order', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'random' => [
						'title' => esc_html__( 'Random', 'elementor-make-widget' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Date', 'elementor-make-widget' ),
						'icon' => 'eicon-date',
					],
					'right' => [
						'title' => esc_html__( 'Author', 'elementor-make-widget' ),
						'icon' => 'eicon-person',
					],
				],
				'default' => 'center',
				'toggle' => true,
			]
		);
		$this->add_control(
			'show_author',
			[
				'label' => esc_html__( 'Show Author', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementor-make-widget' ),
				'label_off' => esc_html__( 'Hide', 'elementor-make-widget' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'show_date',
			[
				'label' => esc_html__( 'Show Date', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementor-make-widget' ),
				'label_off' => esc_html__( 'Hide', 'elementor-make-widget' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'show_summary',
			[
				'label' => esc_html__( 'Show Summary', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementor-make-widget' ),
				'label_off' => esc_html__( 'Hide', 'elementor-make-widget' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'show_categories',
			[
				'label' => esc_html__( 'Show Categories', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementor-make-widget' ),
				'label_off' => esc_html__( 'Hide', 'elementor-make-widget' ),
				'return_value' => 'yes',
				'default' => 'no',
			],
		);
		$this->add_control(
			'horizontal_display',
			[
				'label' => esc_html__( 'Horizontal Display', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementor-make-widget' ),
				'label_off' => esc_html__( 'No', 'elementor-make-widget' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'carousel',
			[
				'label' => esc_html__( 'Carousel', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementor-make-widget' ),
				'label_off' => esc_html__( 'No', 'elementor-make-widget' ),
				'return_value' => 'yes',
				'condition' => [
				    'horizontal_display' => 'yes',
			  	],
				'default' => 'no',
			],
		);
		$this->add_control(
			'read_more',
			[
				'label' => esc_html__( 'Read More Text', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Read More', 'elementor-make-widget' ),
				'condition' => [
					'horizontal_display' => 'yes',
				    'carousel' => 'yes',
			  	],
				'default' => 'Read More',
			],
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'elementor-make-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'header_bg_color',
			[
				'label' => esc_html__( 'Header Background Color', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#005e9a',
				'selectors' => [
					'{{WRAPPER}} h4' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'header_text_color',
			[
				'label' => esc_html__( 'Header Text Color', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffff',
				'selectors' => [
					'{{WRAPPER}} h4 a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'title_position',
			[
				'label' => esc_html__( 'Title Position', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'top' => [
						'title' => esc_html__( 'Top', 'elementor-make-widget' ),
						'icon' => 'eicon-arrow-up',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'elementor-make-widget' ),
						'icon' => 'eicon-arrow-down',
					],
				],
				'default' => 'top',
				'toggle' => true,
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
    	$settings = $this->get_settings_for_display();
		$num_display = $settings['num_display'];
		$disp_order = $settings['disp_order'];
		$title = !empty($settings['title']) ? $settings['title'] : "Your Personalized Makezine Feed";
		$link = $settings['link'];
		$show_categories = $settings['show_categories'];

		$url = 'https://makezine.com/feed';

		$args = array(
		    'field'   => 'Interests',
		    'user_id' => bp_loggedin_user_id()
		);
		
		$interests = $interest_slugs = bp_get_profile_field_data($args);
		
		array_walk($interest_slugs, function (&$value) {
			$term = get_term_by('name', $value, "category");
			$value = isset($term->slug) ? $term->slug : "";
		});

		// if no interests are set, we are just using the default makezine feed, otherwise, build a feed based on interests
		$url = !empty($interests) ? 'https://makezine.com/category/' . implode(",", $interest_slugs) . '/feed' : "https://makezine.com/feed";

		$rss = fetch_feed($url);
		$desc = '';

		if ($link) {
			$title = '<a target="_blank" class="rsswidget" href="' . esc_url($link) . '">' . $title . '</a>';
		}
		echo '<h4>'.$title.'</h4>';

		if (!is_wp_error($rss)) {
			$desc = esc_attr(strip_tags(@html_entity_decode($rss->get_description(), ENT_QUOTES, get_option('blog_charset'))));
			if (empty($title)) {
				$title = strip_tags($rss->get_title());
			}
			if (empty($link)) {
				$link = strip_tags($rss->get_permalink());
				while (stristr($link, 'http') != $link) {
					$link = substr($link, 1);
				}
			}
			makewidget_rss_output($rss, $settings);
		} else {
			makewidget_rss_output('https://makezine.com/feed/', $settings);
		}
		if ($show_categories == "yes") {
			echo("<h5>Read more on Makezine.com related to your interests!</h5><div class='cat-links'>");
			for($i = 0; $i <= count($interests); $i++) {
				echo("<a href='https://makezine.com/category/".$interest_slugs[$i]."' target='_blank'>".$interests[$i]."</a>");
			}
			echo("</div>");
		}
		echo("<div class='edit-interests'><a href='/members/me/profile/edit/group/4/'>Edit your interests</a> to personalize your feed.</div>");

		if (!is_wp_error($rss)) {
			$rss->__destruct();
		}
		unset($rss);

	} //end render public function
}
