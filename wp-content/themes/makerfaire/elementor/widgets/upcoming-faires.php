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

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'plugin-name' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __( 'Faire Title Typography', 'makerfaire' ),
				'selector' => '{{WRAPPER}} .uf-title',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'date_typography',
				'label' => __( 'Faire Date Typography', 'makerfaire' ),
				'selector' => '{{WRAPPER}} .uf-date',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'country_typography',
				'label' => __( 'Faire Country Typography', 'makerfaire' ),
				'selector' => '{{WRAPPER}} .uf-date',
				'condition' => [
					'show_country' => 'true',
				],
			]
		);

		$this->add_control(
			'show_images',
			[
				'label' => __( 'Show Images', 'makerfaire' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'false' => [
						'title' => __( 'Hide', 'plugin-domain' ),
						'icon' => 'fas fa-eye-slash ',
					],
					'true' => [
						'title' => __( 'Show', 'plugin-domain' ),
						'icon' => 'fas fa-eye',
					],
				],
				'default' => 'true',
				'toggle' => true,
				'description' => __( "Choose whether or not to show the Faire Images", 'makerfaire' ),
			]
		);

		$this->add_control(
			'show_country',
			[
				'label' => __( 'Show Country', 'makerfaire' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'false' => [
						'title' => __( 'Hide', 'plugin-domain' ),
						'icon' => 'fas fa-eye-slash ',
					],
					'true' => [
						'title' => __( 'Show', 'plugin-domain' ),
						'icon' => 'fas fa-eye',
					],
				],
				'default' => 'false',
				'toggle' => true,
				'description' => __( "Choose whether or not to show the Faire Country", 'makerfaire' ),
			]
		);

		$this->add_control(
			'show_type_flag',
			[
				'label' => __( 'Show Type Flag', 'makerfaire' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'false' => [
						'title' => __( 'Hide', 'plugin-domain' ),
						'icon' => 'fas fa-eye-slash ',
					],
					'true' => [
						'title' => __( 'Show', 'plugin-domain' ),
						'icon' => 'fas fa-eye',
					],
				],
				'default' => 'false',
				'toggle' => true,
				'description' => __( "Choose whether or not to show a flag in the top right representing the Faire Type", 'makerfaire' ),
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

		$rows = $wpdb->get_results( "SELECT faire_name, faire_nicename, event_type, venue_address_country, event_dt, event_start_dt, event_end_dt, faire_url, cfm_url, faire_image, cfm_image FROM ".$wpdb->prefix."mf_global_faire WHERE event_type in(".$faire_type.") ".$past_or_future." ORDER BY event_start_dt", OBJECT );

		$i = 0;
		foreach($rows as $row){
			if($row->faire_image && $row->event_dt) {
				$name = isset($row->faire_nicename) ? $row->faire_nicename : $row->faire_name;
				$event_type = ($row->event_type == "Mini") ? $event_type = "Community" : $event_type = $row->event_type;
				$return .= "<li><a href='$row->faire_url'>";
				if($settings['show_images'] == 'true') {
					$return .=  "<img src='$row->faire_image'>";
				}
				$return .= 		"<div class='uf-date-row'>";
				$return .=      	"<p class='uf-date'>$row->event_dt</p>";
				if($settings['show_type_flag'] == 'true') {
					$return .=  	"<div class='uf-flag type-$row->event_type'>$event_type</div>";
				}
				$return .= 		"</div>";
				$return .=      "<h3 class='uf-title'>$name</h3>";
				if($settings['show_country'] == 'true') {
					$return .=  "<h4 class='uf-country'>$row->venue_address_country</h4>";
				}
				$return .= "</a></li>";
				if (++$i == $limit) break;
			}
		}
		$return .= "</ul>";
		echo($return);

    } //end render function

} //end class
