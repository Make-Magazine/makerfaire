<?php

namespace Elementor;

class Two_Column_Image extends Widget_Base {

    public function get_name() {
        return 'two_column_image';
    }

    public function get_title() {
        return __('Make: 2 Column Image', 'makerfaire');
    }

	public function get_icon() {
		return 'fa fa-image';
	}

	public function get_categories() {
		return [ 'make' ];
	}

    protected function _register_controls() {
        $this->start_controls_section(
            'content_section2',
            [
                'label' => __('Content', 'makerfaire'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'image_title',
            [
                'label' => __('Image Title', 'makerfaire'),
                'type' => Controls_Manager::TEXT,
                'description' => __('Enter the Image title', 'makerfaire'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'image_text',
            [
                'label' => __('Image Text', 'makerfaire'),
                'type'  => Controls_Manager::TEXTAREA,
                'description' => __('Enter the text that appears under the title', 'makerfaire'),
                'label_block' => true,
            ]
        );

		$repeater->add_control(
            'image_link_text',
            [
		        'label' => __('Image Link Text', 'makerfaire'),
		        'type' => Controls_Manager::TEXT,
		        'description' => __('Enter the text for the link', 'makerfaire'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'image_link_url',
            [
                'label' => __('Image Link URL', 'makerfaire'),
                'type' => Controls_Manager::URL,
        		'description' => __('Enter the url for the link', 'makerfaire'),
                'default' => [
                    'url' => '',
                ]
            ]
        );

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Choose Image', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				]
			]
		);

        $repeater->add_control(
            'image_overlay_text',
            [
		        'label' => __('Image Overlay Text', 'makerfaire'),
		        'type' => Controls_Manager::TEXT,
		        'description' => __('Enter text for the optional image overlay', 'makerfaire'),
                'label_block' => true,
            ]
        );

		$repeater->add_control(
            'image_overlay_link',
            [
                'label' => __('Image Overlay Link', 'makerfaire'),
                'type' => Controls_Manager::URL,
        		'description' => __('Enter the url for the optional image overlay', 'makerfaire'),
                'default' => [
                    'url' => '',
                ]
            ]
        );

		$repeater->add_control(
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

        $this->add_control(
            'image_list',
            [
                'label' => __('Image Panel Rows', ''),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'image_title' => __('Image Row #1', 'makerfaire'),
                        'list_content' => __('Item content. Click the edit button to change this text.', 'makerfaire'),
                    ],
                ],
                'title_field' => '{{{ image_title }}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
		$return = '<section class="image-panel container-fluid">';    // create content-panel section

		if ($settings['image_list']) {
			$imageRowNum = 0;
			foreach ($settings['image_list'] as $image) {
				$imageRowNum += 1;
				$imageObj = $image['image'];

				if ($imageRowNum % 2 != 0) {
					$return .= '<div class="row ' . $image['background_color'] . '">';
					$return .= '  <div class="col-sm-4 col-xs-12">
									<h4>' . $image['image_title'] . '</h4>
									<p>' . $image['image_text'] . '</p>';
					if ( isset($image['image_link_url']) && isset($image['image_link_text']) ) {
						$return .= '  	<a href="' . $image['image_link_url']['url'] . '">' . $image['image_link_text'] . '</a>';
					}
					$return .= '  </div>';
					$return .= '  <div class="col-sm-8 col-xs-12">
									 <div class="image-display">';
					if (isset($image['image_overlay_link'])) {
						$return .= ' 		  <a href="' . $image['image_overlay_link']['url'] . '">';
					}
					$return .= '			 <img class="img-responsive lazyload" src="' . $imageObj['url'] . '" alt="' . $imageObj['alt'] . '" />';
					if (isset($image['image_overlay_text'])) {
						$return .= '  <div class="image-overlay-text">' . $image['image_overlay_text'] . '</div>';
					}
					if (isset($image['image_overlay_link'])) {
						$return .= '        </a>';
					}
					$return .= '		</div>
								  </div>';
					$return .= '</div>';
				} else {
					$return .= '<div class="row ' . $image['background_color'] . '">';
					$return .= '  <div class="col-sm-8 col-xs-12">
									 <div class="image-display">';
					if (isset($image['image_overlay_link'])) {
						$return .= ' 		  <a href="' . $image['image_overlay_link']['url'] . '">';
					}
					$return .= '			 <img class="img-responsive lazyload" src="' . $imageObj['url'] . '" alt="' . $imageObj['alt'] . '" />';
					if ( isset($image['image_overlay_text']) ) {
						$return .= '  <div class="image-overlay-text">' . $image['image_overlay_text'] . '</div>';
						;
					}
					if (isset($image['image_overlay_link'])) {
						$return .= '        </a>';
					}
					$return .= '  </div>';
					$return .= '</div>';
					$return .= '<div class="col-sm-4 col-xs-12">
										<h4>' . $image['image_title'] . '</h4>
										<p>' . $image['image_text'] . '</p>';
					if ( isset($image['image_link_url']) && isset($image['image_link_text']) ) {
						$return .= '  	<a href="' . $image['image_link_url']['url'] . '">' . $image['image_link_text'] . '</a>';
					}
					$return .= '  </div>';
					$return .= '</div>';
				}
			}
		} // End if image_list
		$return .= '</section>'; // end section/container
        echo $return;
    } //end render function

} //end class
