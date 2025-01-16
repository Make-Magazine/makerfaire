<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Make: Upcoming Maker Faires Widget
 *
 * Elementor widget that pulls x of the next upcoming Maker Faires
 *
 * @since 1.0.0
 */
class Elementor_upcomingMakerFaires_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'upcomingmakerfaires';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Make: Upcoming Maker Faires', 'elementor-make-widget' );
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
		return [ 'make', 'custom', 'maker faire'];
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
				'label' => esc_html__( 'Items to display', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'1'  => esc_html__( '1', 'elementor-make-widget' ),
					'2' => esc_html__( '2', 'elementor-make-widget' ),
					'3' => esc_html__( '3', 'elementor-make-widget' ),
					'4' => esc_html__( '4', 'elementor-make-widget' ),
					'5' => esc_html__( '5', 'elementor-make-widget' ),
					'6' => esc_html__( '6', 'elementor-make-widget' ),
					'7' => esc_html__( '7', 'elementor-make-widget' ),
					'8' => esc_html__( '8', 'elementor-make-widget' ),
					'9' => esc_html__( '9', 'elementor-make-widget' ),
					'10' => esc_html__( '10', 'elementor-make-widget' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'plugin-name' ),
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
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} h4 a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'background_color',
			[
				'label' => esc_html__( 'Background Color', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .upcoming-makerfaires-feed ul' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'faire_name_color',
			[
				'label' => esc_html__( 'Faire Name Color', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#3fafed',
				'selectors' => [
					'{{WRAPPER}} ul h5' => 'color: {{VALUE}} !important',
				],
			]
		);
		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} ul a' => 'color: {{VALUE}}',
				],
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
		$number = $settings['num_display'];
		$title = $settings['title'];
		$link = $settings['link'];

		$api_url = 'https://makerfaire.com/query/?type=map&upcoming=true&number=' . $number . '&categories=mini,featured,flagship';
        $faire_content = @file_get_contents($api_url);
        // Decode the JSON in the file
        $faires = json_decode($faire_content, true);

		$title = $title==''?'<img src="https://make.co/wp-content/themes/make-experiences/images/makerfaire-logo.png">':'<h4>'.$settings['title'].'</h4>';
		if ($link) {
			$title = '<a target="_blank" class="upcomingFairesLink" href="' . esc_url($link) . '">' . $title . '</a>';
		}


        $return = '<div class="upcoming-makerfaires-feed">'.$title.'<ul>';

        // Loop through products in the collection
		if(isset($faires['Locations'])) {
			foreach ($faires['Locations'] as $faire) {
				$return .= "<li>
								<a target='_blank' href='" . $faire['faire_url'] . "'>
									<h5>" . $faire['name'] . "</h5>
									<div class='faire-feed-date'>" . $faire['event_dt'] . "</div>
									<div class='faire-feed-location'>" . $faire['venue_address_street'] . " " . $faire['venue_address_city'] . " " . $faire['venue_address_state'] . " " . $faire['venue_address_country'] . "</div>
								</a>
							</li>";
			}
		} else {
			$return .= "<li>Having trouble getting Maker Faire data right now. Find upcoming Maker Faires <a href='https://makerfaire.com/map' target='_blank'>here!</a>";
		}
        $return .= "</ul></div>";
        echo ($return);

	} //end render public function
}
