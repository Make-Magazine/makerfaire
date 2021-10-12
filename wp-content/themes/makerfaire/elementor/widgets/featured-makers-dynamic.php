<?php

namespace Elementor;

class Featured_Makers_Dynamic extends Widget_Base {

    public function get_name() {
        return 'featured_makers_dynamic';
    }

    public function get_title() {
        return __('Make: Featured Makers - Dynamic', 'makerfaire');
    }

	public function get_icon() {
		return 'fas fa-user-astronaut';
	}

	public function get_categories() {
		return [ 'make' ];
	}

    protected function register_controls() {
        $this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Featured Makers - Dynamic', 'makerfaire' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'makerfaire' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your title', 'makerfaire' ),
			]
		);

		$this->add_control(
			'form_id',
			[
				'label' => __( 'Form ID', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'description' => __( "Enter the form to pull featured individuals from. They must have the 'Featured Maker' flag set to be pulled in.", 'makerfaire' ),
				'min' => 1,
				'step' => 1,
			]
		);

		$this->add_control(
			'random',
			[
				'label' => __( 'Random Pull Accepted?', 'makerfaire' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'on' => [
						'title' => __( 'On', 'plugin-domain' ),
						'icon' => 'fas fa-toggle-on',
					],
					'off' => [
						'title' => __( 'Off', 'plugin-domain' ),
						'icon' => 'fas fa-toggle-off',
					],
				],
				'default' => 'on',
				'toggle' => true,
			]
		);

		$this->add_control(
			'makers_to_show',
			[
				'label' => __( 'Makers to Show', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 3,
				'max' => 6,
				'step' => 3,
				'default' => 3,
			]
		);

		$this->add_control(
			'more_makers_button',
			[
				'label' => __( '"More Makers" Button', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'makerfaire' ),
				'description' => __( 'Optional button to link to a page with more makers. Leave URL field blank to hide.', 'makerfaire' ),
				'default' => [
					'url' => '',
				]
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => __( 'Background Color', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'white-bg' => __( 'White', 'makerfaire' ),
					'grey-bg' => __( 'Grey', 'makerfaire' ),
					'darkgrey-bg' => __( 'Dark Grey', 'makerfaire' ),
					'blue-bg' => __( 'Blue', 'makerfaire' ),
					'darkblue-bg' => __( 'Dark Blue', 'makerfaire' ),
				],
				'default' => 'white-bg',
			]
		);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

		$return = '';
		$makers_to_show = $settings['makers_to_show'];
		$more_makers_button = $settings['more_makers_button'];
		$background_color = $settings['background_color'];
		$title = $settings['title'];

		//var_dump($background_color);
		// Check if the background color selected was white
		$return .= '<section class="featured-maker-panel ' . $background_color . '"> ';

		$return .= '  <div class="panel-title title-w-border-y ' . ($background_color === "white-bg" ? ' yellow-underline' : '') . '">
					   <h2>' . $title . '</h2>
					 </div>';

		//build makers array
		$makerArr = array();

		$formid = (int) ($settings['form_id']);

		$search_criteria['status'] = 'active';
		$search_criteria['field_filters'][] = array('key' => '303', 'value' => 'Accepted');
		$search_criteria['field_filters'][] = array('key' => '304', 'value' => 'Featured Maker');

		$entries = \GFAPI::get_entries($formid, $search_criteria, null, array('offset' => 0, 'page_size' => 999));

		//randomly order entries
		if( $settings['random'] == 'on' ) {
			shuffle($entries);
		}
		foreach ($entries as $entry) {
			$url = $entry['22'];

			$overrideImg = $entry['id'];
			if ($overrideImg != '')
				$url = $overrideImg;
				$makerArr[] = array('image' => $url,
				'name' => $entry['151'],
				'desc' => $entry['16'],
				'maker_url' => '/maker/entry/' . $entry['id']
			);
		}

		//limit the number returned to $makers_to_show
		$makerArr = array_slice($makerArr, 0, $makers_to_show);

		$return .= '<div id="performers" class="featured-image-grid">';

		//loop thru maker data and build the table
		foreach ($makerArr as $maker) {
			// var_dump($maker);
			// echo '<br />';
			$return .= '<div class="grid-item lazyload" data-bg="' . $maker['image'] . '">';

			if (!empty($maker['desc'])) {
				$markup = !empty($maker['maker_url']) ? 'a' : 'div';
				$href = !empty($maker['maker_url']) ? 'href="' . $maker['maker_url'] . '"' : '';
				$return .= '<' . $markup . ' ' . $href . ' class="grid-item-desc">
						 <div class="desc-body"><h4>' . $maker['name'] . '</h4>
						 <p class="desc">' . $maker['desc'] . '</p></div>';
				if (!empty($maker['maker_url'])) {
					$return .= '  <p class="btn btn-blue read-more-link">Learn More</p>'; //<a href="' . $maker['maker_url'] . '"></a>
				}
				$return .= ' </' . $markup . '>';
			}
			// the caption section
			$return .= '  <div class="grid-item-title-block hidden-sm hidden-xs">
							 <h3>' . $maker['name'] . '</h3>
						</div>';
			$return .= '</div>'; //close .grid-item
		}
		$return .= '</div>';  //close #makers

		//check if we should display a more maker button
		if ($settings['more_makers_button']) {
			$return .= '<div class="row">
				<div class="col-xs-12 text-center">
				  <a class="btn btn-outlined more-makers-link" href="' . $settings['more_makers_button']['url'] . '">More Makers</a>
				</div>
			  </div>';
		}
		$return .= '</section>';
		$return .= '<script type="text/javascript">
						jQuery(document).ready(function(){
								fitTextToBox();
							 });
							 jQuery(window).resize(function(){
								 fitTextToBox();
							 });
						</script>';
		echo $return;

    } //end render function

} //end class
