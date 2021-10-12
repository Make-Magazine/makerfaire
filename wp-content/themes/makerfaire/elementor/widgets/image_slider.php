<?php

namespace Elementor;

class Image_Slider extends Widget_Base {

    public function get_name() {
        return 'image_slider';
    }

    public function get_title() {
        return __('Make: Image Slider', 'makerfaire');
    }

	public function get_icon() {
		return 'fas fa-columns';
	}

	public function get_categories() {
		return [ 'make' ];
	}

    protected function register_controls() {
        $this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Image Slider', 'makerfaire' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'slideshow_title',
			[
				'label' => __( 'Slideshow Title', 'makerfaire' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Optional header to appear above Slideshow', 'makerfaire' ),
			]
		);

		$this->add_control(
			'slideshow_name',
			[
				'label' => __( 'Slideshow Name', 'makerfaire' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Unique Identifier for the slideshow, in case you end using multiples of this block on single page (don\'t use any spaces)', 'makerfaire' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Image', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'description' => __('Background Image for the entire slide', 'makerfaire'),
			]
		);

		$repeater->add_control(
            'slide_title',
            [
                'label' => __('Slide Title', 'makerfaire'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

		$repeater->add_control(
            'slide_link',
            [
                'label' => __('Slide Link', 'makerfaire'),
                'type' => Controls_Manager::URL,
        		'description' => __('Link can be applied to body of slide or just to button when button text is provided.', 'makerfaire'),
                'default' => [
                    'url' => '',
                ]
            ]
        );

		$repeater->add_control(
            'slide_button_text',
            [
                'label' => __('Slide Button Text', 'makerfaire'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

		$this->add_control(
			'slide_button_color',
			[
				'label' => __( 'Slide Button Color', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'blue-bg' => __( 'Blue', 'makerfaire' ),
					'darkblue-bg' => __( 'Dark Blue', 'makerfaire' ),
					'grey-bg' => __( 'Grey', 'makerfaire' ),
					'darkgrey-bg' => __( 'Dark Grey', 'makerfaire' ),
				],
				'default' => 'blue-bg',
			]
		);
		$repeater->add_control(
            'slide_text',
            [
		        'label' => __('Slide Text', 'makerfaire'),
		        'type' => Controls_Manager::TEXT,
		        'description' => __('Optional slide text to show up for one column slides', 'makerfaire'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'slide',
            [
                'label' => __('Slide', ''),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'image_title' => __('Slide #1', 'makerfaire'),
                        'list_content' => __('Add as many slides as you want.', 'makerfaire'),
                    ],
                ],
                'title_field' => '{{{ slide_title }}}',
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

		$this->add_control(
			'text_position',
			[
				'label' => __( 'Text Position', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'top' => __( 'Top', 'makerfaire' ),
					'center' => __( 'Center', 'makerfaire' ),
					'bottom' => __( 'Bottom', 'makerfaire' ),
				],
				'default' => 'top',
				'description' => __('Choose where the name of the slide appears (top, bottom or center)' , 'makerfaire'),
			]
		);

		$this->add_control(
			'column_number',
			[
				'label' => __( 'Column Number', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 4,
				'step' => 1,
				'default' => 1,
				'description' => __('Choose a number of columns to show up in this slide show (max 4)', 'makerfaire'),
			]
		);

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

		$background_color = $settings['background_color'];
		$text_position = $settings['text_position'];
		$slideshow_title = $settings['slideshow_title'];
		$slideshow_name = $settings['slideshow_name'];
		$column_number = $settings['column_number'];
		$slides = $settings['slide'];

		$return = '';
		$return .= '<section class="slider-panel container-fluid ' . $background_color . ' position-' . $text_position . '">';
		if (!empty($slideshow_title)) {
			$return .= '<div class="slideshow-title"><h2>' . $slideshow_title . '</h2></div>';
		}
		$return .= '   <div class="' . $slideshow_name . '-carousel owl-carousel columns-' . $column_number . '">';
		//get requested data for each column
		foreach ($slides as $slide) {
			$imageObj = $slide['image'];
			if (empty($slide['slide_button_text']) && !empty($slide['slide_link']['url'])) {
				$return .= '<a href="' . $slide['slide_link']['url'] . '">';
			}
			$return .= '     <div class="item slide">
									   <div class="slide-image-section" style="background:url(' . $imageObj['url'] . ');background-repeat:no-repeat;background-size:cover;">';
			if (!empty($slide['slide_title']) && get_sub_field("column_number") > 1) {
				$return .= '     <p class="slide-title">' . $slide['slide_title'] . '</p>';
			}
			if (!empty($slide['slide_button_text']) && get_sub_field("column_number") > 1) {
				if (!empty($slide['slide_link']['url'])) {
					$return .= '      <a href="' . $slide['slide_link']['url'] . '">';
				}
				$return .= '          <button class="btn slide-btn ' . $slide['slide_button_color'] . '">' . $slide['slide_button_text'] . '</button>';
				if (!empty($slide['slide_link']['url'])) {
					$return .= '      </a>';
				}
			}
			// This section is only for one column slideshows that have description text
			if ($column_number == 1) {
				$return .= '    </div>
								<div class="slide-info-section">';
				if (!empty($slide['slide_title'])) {
					$return .= '     <p class="slide-title">' . $slide['slide_title'] . '</p>';
				}
				if (!empty($slide['slide_text'])) {
					$return .= '     <p class="slide-text">' . $slide['slide_text'] . '</p>';
				}
				if (!empty($slide['slide_button_text'])) {
					if (!empty($slide['slide_link']['url'])) {
						$return .= '   <a href="' . $slide['slide_link']['url'] . '">';
					}
					$return .= '         <button class="btn slide-btn ' . $slide['slide_button_color'] . '">' . $slide['slide_button_text'] . '</button>';
					if (!empty($slide['slide_link']['url'])) {
						$return .= '   </a>';
					}
				}
			}
			$return .= '       </div>
							 </div>';
			if (!empty($slide['slide_link']['url']) && empty($slide['slide_button_text'])) {
				$return .= '</a>';
			}
		}
		$tabletSlides = 1;
		if ($column_number > 1) {
			$tabletSlides = 2;
		}
		$return .= '   </div>
					</section>

						<script type="text/javascript">
						   jQuery(document).ready(function() {
							// slideshow carousel
								jQuery(".' . $slideshow_name . '-carousel.owl-carousel").owlCarousel({
								  loop: true,
								  margin: 15,
								  nav: true,
								  navText: [
									 "<i class=\'fas fa-caret-left\'></i>",
									 "<i class=\'fas fa-caret-right\'></i>"
								  ],
								  autoplay: true,
								  autoplayHoverPause: true,
								  responsive: {
									 0: {
										items: 1
									 },
									 600: {
									   items: ' . $tabletSlides . '
									 },
									 1000: {
										items: ' . $column_number . '
									 }
								  }
								})
							});
						</script>
						';
		echo $return;

    } //end render function

} //end class
