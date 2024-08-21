<?php

namespace Elementor;

class Sponsors extends Widget_Base {

    public function get_name() {
        return 'sponsors';
    }

    public function get_title() {
        return __('Make: Sponsors', 'makerfaire');
    }

	public function get_icon() {
		return 'fas fa-hands-helping';
	}

	public function get_categories() {
		return [ 'make' ];
	}

    protected function register_controls() {
        $this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Sponsors', 'makerfaire' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title_sponsor_panel',
			[
				'label' => __( 'Title', 'makerfaire' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Thank you to our sponsors', 'makerfaire' ),
			]
		);


		$this->add_control(
			'sponsors_page_year',
			[
				'label' => __( 'Sponsor Page Year', 'makerfaire' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 2018,
				'description' => __( 'Enter the 4 digit year to be displayed.', 'makerfaire' ),
			]
		);

		$this->add_control(
			'sponsors_page_url',
			[
				'label' => __( 'Sponsor Page URL', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com/sponsors', 'makerfaire' ),
				'description' => __( 'Provide the url of the page you\'d like to draw sponsor data from', 'makerfaire' ),
				'default' => [
					'url' => '',
				]
			]
		);
		//show or hide the types for each sponsor level
		$this->add_control(
			'show_sponsor_type',
			[
				'label' => esc_html__( 'Show Sponsor Type', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'makerfaire' ),
				'label_off' => esc_html__( 'Hide', 'makerfaire' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		//show or hide the link to the sponsor page
		$this->add_control(
			'show_sponsor_link',
			[
				'label' => esc_html__( 'Show Link to Sponsor Page', 'makerfaire' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'makerfaire' ),
				'label_off' => esc_html__( 'Hide', 'makerfaire' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);		

        $this->end_controls_section();
    }

    protected function render() {
		$sponsorArray = array(
			array('presenting_sponsors', 'PRESENTING'),
			array('platinum_sponsors', 'PLATINUM'),
			array('goldsmith_sponsors', 'GOLDSMITH'),
			array('silversmith_sponsors', 'SILVERSMITH'),
			array('coppersmith_sponsors', 'COPPERSMITH'),
			array('media_sponsors', 'MEDIA AND COMMUNITY'),											
		);
        $settings = $this->get_settings_for_display();

		$show_type = (isset($settings['show_sponsor_type'])?$settings['show_sponsor_type']:'yes');
		$show_link = (isset($settings['show_sponsor_link'])?$settings['show_sponsor_link']:'yes');
		$url = $settings['sponsors_page_url']['url'];
	    $year = $settings['sponsors_page_year'];
	    $id = url_to_postid($url);

	    $title = $settings['title_sponsor_panel'];
	    if($title=='')  $title = 'Thank you to our sponsors';

	    // IF CUSTOM FIELD FOR SPONSOR SLIDER HAS A URL THEN SHOW THAT URL'S SPONSORS
	    if ($url!='') {
	    	$return = '
		<div class="sponsor-slide">
	      <div class="container">
	         <div class="row">
	            <div class="col-xs-12 text-center padbottom">
	               <h2 class="sponsor-slide-title">' . $title . '</h2>
	            </div>
	         </div>
	         <div class="row">
	            <div class="col-sm-12">
	               <h4 class="sponsor-slide-title">' . ($year ? $year . ' ' : '') . 'Maker Faire Sponsors: <br /> <span class="sponsor-slide-cat"></span></h4>
	            </div>
	         </div>
	         <div class="row">
	            <div class="col-xs-12">
	               <div id="carousel-sponsors-slider" class="carousel slide" data-ride="carousel">
	                  <!-- Wrapper for slides -->
	                  <div class="carousel-inner" role="listbox">';
		
	        foreach ($sponsorArray as $sponsor) {
	            if (have_rows($sponsor[0], $id)) {					
	                $sponsorCount = get_post_meta($id, $sponsor[0], true);

	                $return .= '
	                     <div class="item">
	                        <div class="row sponsors-row sponsors-' . $sponsorCount . '">
	                           <div class="col-xs-12">';
							   if($show_type=='yes'){
									'$return .= <h3 class="sponsors-type text-center">' . $sponsor[1] . '</h3>';
							   }		   
	                              
	                $return .= '<div class="faire-sponsors-box">';

	                while (have_rows($sponsor[0], $id)) {
	                    the_row();
	                    $sub_field_1 = get_sub_field('image'); //Photo
	                    $sub_field_2 = get_sub_field('url'); //URL

						if($sponsor[1]=='PRESENTING'){
							$return .= '      <div class="sponsors-box-lg">';
						}else{
							$return .= '      <div class="sponsors-box-md">';
						}
	                    
	                    if (get_sub_field('url')) {
	                        $return .= '      <a href="' . $sub_field_2 . '" target="_blank">';
	                    }
	                    $return .= '            <img src="' . $sub_field_1 . '" alt="Maker Faire sponsor logo" />';
	                    if (get_sub_field('url')) {
	                        $return .= '      </a>';
	                    }
	                    $return .= '      </div><!-- close .sponsors-box-md -->';
	                }
	                $return .= '
	                              </div> <!-- close .faire-sponsors-box -->
	                           </div> <!-- close .col-xs-12 -->
	                        </div> <!-- close .row sponsors-row -->
	                     </div> <!-- close .item -->';
	            }
	        }

	        $return .= '
	                  </div> <!-- close .carousel-inner-->
	               </div> <!-- close #carousel-sponsors-slider -->
	            </div> <!-- close .col-xs-12 -->
	         </div> <!-- close .row -->';
			 
			 if($show_link == 'yes'){
				$return .= '<div class="row">
	            <div class="col-xs-12 text-center">
	               <a class="btn btn-white more-makers-link" href="' . $url . '">Meet The Sponsors</a>
	            </div>
	         </div>';
			 }

	        $return .= ' 

	      </div> <!-- close .container -->
	   </div> <!-- close .sponsor-slide -->';

	        $return .= '<script>
	                     // Update the sponsor slide title each time the slide changes
	                     jQuery("#carousel-sponsors-slider .carousel-inner .item:first-child").addClass("active");
	                     jQuery(function() {
	                       var title = jQuery(".item.active .sponsors-type").html();
	                       jQuery(".sponsor-slide-cat").text(title);
	                       jQuery("#carousel-sponsors-slider").on("slid.bs.carousel", function () {
	                         var title = jQuery(".item.active .sponsors-type").html();
	                         jQuery(".sponsor-slide-cat").text(title);
	                       });
	                       if (jQuery(window).width() < 767) {
	                         jQuery( ".maker-slider-btn" ).html("Learn More");
	                       }
	                     });
	                     </script>';
	    }

	    echo $return;

    } //end render function

} //end class
