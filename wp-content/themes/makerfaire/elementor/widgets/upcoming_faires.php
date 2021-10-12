<?php

namespace Elementor;

class Upcoming_Faires extends Widget_Base {

    public function get_name() {
        return 'upcoming_faires';
    }

    public function get_title() {
        return __('Make: Upcoming Faire List', 'makerfaire');
    }

	public function get_icon() {
		return 'fas fa-calendar-alt';
	}

	public function get_categories() {
		return [ 'make' ];
	}

    protected function register_controls() {
        $this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Upcoming Featured Faires List', 'makerfaire' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'type',
			[
				'label' => __( 'Faire Type', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => [
					'Featured' => __( 'Featured', 'makerfaire' ),
					'Flagship' => __( 'Flagship', 'makerfaire' ),
					'Mini' => __( 'Community', 'makerfaire' ),
					'School' => __( 'School', 'makerfaire' ),
				],
				'default' => 'Community',
				'multiple' => true,
				'description' => __( "Type of Maker Faire you would like to display (you can select multiple options)", 'makerfaire' ),
			]
		);

		$this->add_control(
			'past_or_future',
			[
				'label' => __( 'Past or Future Faires?', 'makerfaire' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'0' => [
						'title' => __( 'All', 'plugin-domain' ),
						'icon' => 'fas fa-calendar-alt',
					],
					'>' => [
						'title' => __( 'Future', 'plugin-domain' ),
						'icon' => 'fas fa-clock',
					],
					'<' => [
						'title' => __( 'Past', 'plugin-domain' ),
						'icon' => 'fas fa-history',
					],
				],
				'default' => 'on',
				'toggle' => true,
				'description' => __( "Choose whether to show Faires before or after current date", 'makerfaire' ),
			]
		);

		$this->add_control(
			'number',
			[
				'label' => __( 'Number', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'description' => __( "Max number of Faires to show", 'makerfaire' ),
				'min' => 1,
				'step' => 1,
				'default' => 4,
			]
		);


        $this->end_controls_section();
    }

    protected function render() {
		global $wpdb;
        $settings = $this->get_settings_for_display();

		$date_start = date('Y-m-d H:i:s', time());

		$faire_type = "'" . implode("', '", $settings['type']) . "'";
		$past_or_future_value = $settings['past_or_future'];

		$past_or_future = "";
		if($past_or_future_value == '>') {
			$past_or_future = " AND event_start_dt > '" . $date_start . "'";
		} else if($past_or_future_value == '<') {
			$past_or_future = " AND event_start_dt < '" . $date_start . "'";
		}
		$limit = $settings['number'];

		$return = "<ul class='flex-list faire-list'>";

		$rows = $wpdb->get_results( "SELECT faire_name, faire_nicename, event_type, event_dt, event_start_dt, event_end_dt, faire_url, cfm_url, faire_image, cfm_image FROM ".$wpdb->prefix."mf_global_faire WHERE event_type in(".$faire_type.")".$past_or_future." ORDER BY event_start_dt", OBJECT );

		$i = 0;
		foreach($rows as $row){
			if($row->faire_image && $row->event_dt) {
				$name = isset($row->faire_nicename) ? $row->faire_nicename : $row->faire_name;
				$return .= "<li><a href='$row->faire_url'>";
				$return .=      "<img src='$row->faire_image'>";
				$return .=      "<p>$row->event_dt</p>";
				$return .=      "<h3>$name</h3>";
				$return .= "</a></li>";
				if (++$i == $limit) break;
			}
		}
		$return .= "</ul>";
		echo($return);

    } //end render function

} //end class
