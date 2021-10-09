<?php

namespace Elementor;

class RSS_Feed extends Widget_Base {

    public function get_name() {
        return 'rss_feed';
    }

    public function get_title() {
        return __('Make: RSS Feed', 'makerfaire');
    }

	public function get_icon() {
		return 'fa fa-rss';
	}

	public function get_categories() {
		return [ 'make' ];
	}

    protected function register_controls() {
        $this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Makezine RSS Feed', 'makerfaire' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
				'rss_title',
				[
					'label' => __( 'Title', 'makerfaire' ),
					'label_block' => true,
					'type' => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( 'Enter your title', 'makerfaire' ),
				]
			);

			$this->add_control(
				'feed_tag',
				[
					'label' => __( 'Feed Tag', 'makerfaire' ),
					'label_block' => true,
					'type' => \Elementor\Controls_Manager::TEXT,
	                'placeholder' => __( 'Enter Tag here', 'makerfaire' ),
	                'description' => __( 'The "tag" from the makezine.com site, for example "bay-area-maker-faire".', 'makerfaire' ),
				]
			);

			$this->add_control(
				'more_link',
				[
					'label' => __( 'More Link', 'makerfaire' ),
					'type' => \Elementor\Controls_Manager::URL,
					'placeholder' => __( 'https://your-link.com', 'makerfaire' ),
					'description' => __( 'Add the "See All" link here, e.g. News from Make: See All', 'makerfaire' ),
					'default' => [
						'url' => '',
					]
				]
			);

			$this->add_control(
				'limit',
				[
					'label' => __( 'Number', 'makerfaire' ),
					'type' => \Elementor\Controls_Manager::NUMBER,
					'min' => 3,
					'max' => 12,
					'step' => 3,
					'default' => 6,
				]
			);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

		$rss_shortcode = '[make_rss title="'.$settings['rss_title'].'" feed="'.$settings['feed_tag'].'" moreLink="'.$settings['more_link']['url'].'" number='.$settings['limit'].']';
		echo do_shortcode($rss_shortcode);

    } //end render function

} //end class
