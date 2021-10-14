<?php

namespace Elementor;

class Hero_Panel extends Widget_Base {

    public function get_name() {
        return 'hero_panel';
    }

    public function get_title() {
        return __('Make: Hero Panel', 'makerfaire');
    }

	public function get_icon() {
		return 'fas fa-vr-cardboard';
	}

	public function get_categories() {
		return [ 'make' ];
	}

    protected function register_controls() {
        $this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Hero Panel', 'makerfaire' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'hero_title',
			[
				'label' => __( 'Hero Title', 'makerfaire' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'This title is displayed over the content with a semi transparent white background. (Optional)', 'makerfaire' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'hero_image_random',
			[
				'label' => __( 'Hero Image', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'description' => __('Background Image for the entire slide', 'makerfaire'),
			]
		);

		$repeater->add_control(
            'image_cta',
            [
                'label' => __('Image Link', 'makerfaire'),
                'type' => Controls_Manager::URL,
        		'description' => __('Optional - If supplied, this will make the image a clickable link.', 'makerfaire'),
                'default' => [
                    'url' => '',
                ]
            ]
        );

        $this->add_control(
            'hero_image_repeater',
            [
                'label' => __('Hero Images', ''),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'image_title' => __('Image #1', 'makerfaire'),
                        'list_content' => __('Upload 1-10 images for use as the hero image on the page. The displayed image will be randomly selected from these.<br/>Optimal size is 1920 x 490', 'makerfaire'),
                    ],
                ],
            ]
        );


        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

		$hero_array = array();
	    if ($settings['hero_image_repeater']) {
	        // loop through the rows of data

	        foreach ($settings['hero_image_repeater'] as $hero) {
	            the_row();

	            $hero_image_url = (isset($hero['hero_image_random']['url'])?$hero['hero_image_random']['url']:'');

	            if (!empty($hero['image_cta']['url'])) {
	                $columnInfo = '<a href="' . $hero['image_cta']['url'] . '" id="heroPanel"><div class="hero-img" style="background:url(' . $hero_image_url . ');background-size:cover;"></div></a>';
	            } else {
	                $columnInfo = '<div class="hero-img" id="heroPanel" style="background:url(' . $hero_image_url . ');background-size:cover;"></div>';
	            }
	            $hero_array[] = $columnInfo;
	        }
	        $randKey = array_rand($hero_array, 1);
	        $hero_image = $hero_array[$randKey];
	    }

	    $hero_text = $settings['hero_title'];

	    //build output
	    $return = '';
	    $return .= '<section class="hero-panel">';    // create content-panel section

	    $return .= '   <div class="row">
	                    <div class="col-xs-12">';
	    if ($hero_text) {
	        $return .= '<div class="top_left"><img src="https://makerfaire.com/wp-content/themes/makerfaire/img/TopLeftCorner.png"></div>'
	                . '<div class="panel_title">'
	                . '   <div class="panel_text">' . $hero_text . '</div>'
	                . '   <div class="bottom_right"><img src="https://makerfaire.com//wp-content/themes/makerfaire/img/BottomRightCorner.png"></div>'
	                . '</div>';
	    }
	    $return .= '        ' . $hero_image .
	            '     </div>' .
	            '   </div>';


	    // Because of the aggressive caching on prod, it makes more sense to shuffle the array in javascript
	    $return .= '</section><script type="text/javascript">var heroArray = ' . json_encode($hero_array) . ';heroArray.sort(function(a, b){return 0.5 - Math.random()});jQuery(document).ready(function(){jQuery("#heroPanel").replaceWith(heroArray[0]);});</script>';

		echo $return;

    } //end render function

} //end class
