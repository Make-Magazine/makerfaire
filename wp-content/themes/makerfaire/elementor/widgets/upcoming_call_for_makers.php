<?php

namespace Elementor;

class Upcoming_Call_For_Makers extends Widget_Base {

    public function get_name() {
        return 'upcoming_call_for_makers';
    }

    public function get_title() {
        return __('Make: Upcoming Call For Makers List', 'makerfaire');
    }

	public function get_icon() {
		return 'fas fa-calendar-plus';
	}

	public function get_categories() {
		return [ 'make' ];
	}

    protected function register_controls() {
        $this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Upcoming Call for Makers', 'makerfaire' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'featured_faires_number',
			[
				'label' => __( 'How Many Featured Faires?', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'description' => __( "Appears at the top as images, must be divisible by 3", 'makerfaire' ),
				'min' => 3,
				'step' => 3,
				'default' => 3,
			]
		);

		$this->add_control(
			'community_faires_number',
			[
				'label' => __( 'How Many Community Faires?', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'description' => __( "Appears underneath the featured faires as a list of links", 'makerfaire' ),
				'step' => 1,
				'default' => 0,
			]
		);


        $this->end_controls_section();
    }

    protected function render() {
		global $wpdb;
        $settings = $this->get_settings_for_display();

		$featured_faire_limit = $settings['featured_faires_number'];
		$community_faire_limit = $settings['community_faires_number'];

		$output = "<div class='cfm-list'>";
		$output .=   "<ul class='flex-list featured-cfm-list'>";
		$featuredRows = $wpdb->get_results( "SELECT event_start_dt, cfm_start_dt, cfm_end_dt, event_type, cfm_url, faire_image, cfm_image FROM ".$wpdb->prefix."mf_global_faire WHERE event_type = 'Featured' AND cfm_start_dt < CURRENT_DATE() AND cfm_end_dt > CURRENT_DATE() ORDER BY event_start_dt", OBJECT );
		$i = 0;
		foreach($featuredRows as $row){
			if($row->cfm_image) {
				$output .= "<li><a href='$row->cfm_url'>";
				$output .=      "<img src='$row->cfm_image'>";
				$output .= "</a></li>";
				if (++$i == $featured_faire_limit) break;
			}
		}
		$output .=   "</ul>";
		$output .=   "<ul class='community-cfm-list'>";
		$communityRows = $wpdb->get_results( "SELECT faire_name, event_start_dt, cfm_start_dt, cfm_end_dt, event_type, event_dt, cfm_url FROM ".$wpdb->prefix."mf_global_faire WHERE event_type = 'Mini' AND cfm_start_dt < CURRENT_DATE() AND cfm_end_dt > CURRENT_DATE() ORDER BY event_start_dt", OBJECT );
		$j = 0;
		foreach($communityRows as $row){
			$output .= "<li><a href='$row->cfm_url'>";
			$output .=      $row->faire_name . "<br />(" . $row->event_dt . ")";
			$output .= "</a></li>";
			if (++$j == $community_faire_limit) break;
		}
		$output .=   "</ul>";
		echo($output);

    } //end render function

} //end class
