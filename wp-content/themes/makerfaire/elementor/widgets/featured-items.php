<?php

namespace Elementor;

class Featured_Items extends Widget_Base {

    public function get_name() {
        return 'featured_items';
    }

    public function get_title() {
        return __('Make: Featured Items', 'makerfaire');
    }

	public function get_icon() {
		return 'fas fa-boxes';
	}

	public function get_categories() {
		return [ 'make' ];
	}

    protected function register_controls() {
        $this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Featured Items', 'makerfaire' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'panel_title',
			[
				'label' => __( 'Title', 'makerfaire' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'i.e. "Featured Items"', 'makerfaire' ),
			]
		);

		$repeater = new Repeater();

        $repeater->add_control(
            'item_name',
            [
                'label' => __('Item Name', 'makerfaire'),
                'type' => Controls_Manager::TEXT,
                'description' => __('Optional field. 80 characters or less ideal. Use sup tag for smaller text.', 'makerfaire'),
                'label_block' => true,
            ]
        );

		$repeater->add_control(
			'item_image',
			[
				'label' => __( 'Choose Image', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'description' => __('Images are best with square sizes around 500px x 500px.', 'makerfaire'),
			]
		);

        $repeater->add_control(
            'item_short_description',
            [
		        'label' => __('Short Description', 'makerfaire'),
		        'type' => Controls_Manager::TEXTAREA,
		        'description' => __('Optional field. 300 characters or less ideal.', 'makerfaire'),
                'label_block' => true,
            ]
        );

		$repeater->add_control(
            'more_info_url',
            [
                'label' => __('More Info URL', 'makerfaire'),
                'type' => Controls_Manager::URL,
        		'description' => __('Optional link to more information. Leave URL field blank to hide.', 'makerfaire'),
                'default' => [
                    'url' => '',
                ]
            ]
        );

		$repeater->add_control(
			'new_tab',
			[
				'label' => __( 'New Tab?', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					false => __( 'Same Tab', 'makerfaire' ),
					true => __( 'New Tab', 'makerfaire' ),
				],
				'default' => '0',
				'description' => __( 'Should the link open in a new tab or not', 'makefaire' ),
			]
		);

        $this->add_control(
            'featured_items',
            [
                'label' => __('Featured Items', ''),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'image_title' => __('Featured Item #1', 'makerfaire'),
                        'list_content' => __('Adds a panel for 1-4 rows of featured items. Each item features an image, name, and short description that will roll over on a hover. Start by clicking the "Add New Item" button for each featured item to show in this panel.', 'makerfaire'),
                    ],
                ],
                'title_field' => '{{{ item_name }}}',
            ]
        );

		$this->add_control(
			'items_to_show',
			[
				'label' => __( 'Amount of Items to show', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'Show 3, 6, 9, 12 featured items', 'makerfaire' ),
				'min' => 3,
				'max' => 12,
				'step' => 3,
				'default' => 3,
			]
		);

		$this->add_control(
			'cta_text',
			[
				'label' => __( 'CTA Text', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'i.e. "See More"', 'makerfaire' ),
				'description' => __( 'Type the CTA link text here.', 'makerfaire' ),
			]
		);

		$this->add_control(
			'cta_link',
			[
				'label' => __( 'CTA Link', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'makerfaire' ),
				'description' => __( 'Optional button to link to a page with more items. Leave URL field blank to hide.', 'makerfaire' ),
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

	    $items_to_show = $settings['items_to_show'];
	    $background_color = $settings['background_color'];
	    $title = $settings['panel_title'];
	    $cta_url = $settings['cta_link']['url'];
	    $cta_text = $settings['cta_text'];

	    // Check if the background color selected was white
	    $return = '<section class="featured-item-panel full-width-div ' . $background_color . '"> ';

	    if ($title) {
	        $return .= '  <div class="panel-title title-w-border-y ' . ($background_color === "white-bg" ? ' navy-underline' : '') . '">
								 <h2>' . $title . '</h2>
							  </div>';
	    }

	    //build makers array
	    $itemArr = array();
	    // check if the nested repeater field has rows of data
	    if ($settings['featured_items']) {
	        // loop through the rows of data
	        foreach ($settings['featured_items'] as $item) {
	            the_row();
	            $itemArr[] = array(
	                'name' => $item['item_name'],
	                'imageObj' => $item['item_image'],
	                'desc' => $item['item_short_description'],
	                'item_url' => $item['more_info_url'],
	                'new_tab' => $item['new_tab'],
	            );
	        }
	    }

	    //limit the number returned to $makers_to_show
	    $itemArr = array_slice($itemArr, 0, $items_to_show);

	    $return .= '<div class="featured-image-grid">';

	    //loop thru item data and build the table
	    foreach ($itemArr as $item) {
			$markup = !empty($item['item_url']) ? 'a' : 'div';
			$newTab = $item['new_tab'];
			$newTab = ($newTab == true ? "target='_blank'" : "target='_self'");

			$href = !empty($item['item_url']['url']) ? 'href="' . $item['item_url']['url'] . '" ' . $newTab : '';

	        $return .= '<' . $markup . ' ' . $href . ' class="grid-item lazyload" style="background:url(' . $item['imageObj']['url'] . ');background-size:cover;">';

	        if (!empty($item['desc'])) {
	            $return .= '<div class="grid-item-desc">
	                     <div class="desc-body"><h4>' . $item['name'] . '</h4>
	                     <p class="desc">' . $item['desc'] . '</p></div>';
	            if (!empty($item['item_url']['url'])) {
	                $return .= '  <p class="btn btn-blue read-more-link">Learn More</p>'; //<a href="' . $maker['item_url'] . '"></a>
	            }
	            $return .= ' </div>';
	        }
	        // the caption section
	        $return .= '  <div class="grid-item-title-block">
			                 <h3 class="text-'. get_field('item_title_orientation') . '">' . $item['name'] . '</h3>
	                    </div>';
	        $return .= '</' . $markup . '>'; //close .grid-item
	    }
	    $return .= '</div>';  //close
	    //check if we should display a more maker button

	    if ($cta_url) {
	        if (empty($cta_text)) {
	            $cta_text = 'More Items';
	        }
	        $return .= '<div class="row">
	            <div class="col-xs-12 text-center">
	              <a class="btn universal-btn-navy more-makers-link" href="' . $cta_url . '">' . $cta_text . '</a>
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
