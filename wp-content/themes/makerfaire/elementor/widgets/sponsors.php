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
		return ['make'];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => __('Sponsors', 'makerfaire'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'sponsors_page_id',
			[
				'label' => __('Sponsor Page ID', 'makerfaire'),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
				'description' => __('Enter the ID of the page  page you\'d like to pull sponsor data from.', 'makerfaire'),
			]
		);

		$this->add_control(
			'sponsors_page_url',
			[
				'label' => __('Sponsor Page URL', 'makerfaire'),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => __('https://your-link.com/sponsors', 'makerfaire'),
				'description' => __('Provide the url of the sponsor page to link to.', 'makerfaire'),
				'default' => [
					'url' => '',
				]
			]
		);
		//show or hide the link to the sponsor page
		$this->add_control(
			'show_sponsor_link',
			[
				'label' => esc_html__('Show Link to Sponsor Page', 'makerfaire'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Show', 'makerfaire'),
				'label_off' => esc_html__('Hide', 'makerfaire'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		//Slider or Stacked layout
		$this->add_control(
			'show_slide_block',
			[
				'label' => esc_html__('Layout', 'makerfaire'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide' => [
						'title' => esc_html__( 'Slider', 'makerfaire' )
					],
					'stacked' => [
						'title' => esc_html__( 'Stacked', 'makerfaire' )
					]
				],				
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$return = '';
		//pull settings for this widget
		$settings = $this->get_settings_for_display();

		$show_link   = (isset($settings['show_sponsor_link']) 	? $settings['show_sponsor_link'] : 'yes');
		$slide_block = (isset($settings['show_slide_block'])  	? $settings['show_slide_block']  : 'slide');

		//if the post ID isn't set, try to get it from the passed URL
		$url 		 = $settings['sponsors_page_url']['url'];
		$id 		 = (isset($settings['sponsors_page_id'])  ? $settings['sponsors_page_id'] : url_to_postid($url));

		// if we have a page to pull sponsors from
		if ($id != 0 && have_rows('sponsors', $id)) {
			ob_start(); ?>
			<div class="sponsor-slide">
				<div class="container">
					<div id="carousel-sponsors-slider" class="carousel slide" data-ride="carousel">
						<!-- Wrapper for slides -->
						<div class="carousel-inner" role="listbox">
							<?php
							//each sponsor
							while (have_rows('sponsors', $id)) {
								the_row();
								$sponsor_label = get_sub_field('sponsor_level_label');
								$logo_size     = get_sub_field('logo_size');
								$sponsorCount  = get_post_meta($id, 'sponsors', true);
							?>
								<div class="<?php echo ($slide_block == 'slide' ? 'item' : ''); ?>">
									<div class="row sponsors-row sponsors-<?php echo $sponsorCount; ?>">
										<div class="col-xs-12">
											<h3 class="sponsors-type text-center"><?php echo $sponsor_label; ?></h3>
											<div class="faire-sponsors-box">
												<?php
												if (have_rows('sponsor_list')) {
													while (have_rows('sponsor_list')) {
														the_row();
														$sponsor_logo = get_sub_field('sponsor_logo'); //Photo
														$sponsor_link = get_sub_field('sponsor_link'); //URL
												?>
														<div class="sponsors-box-<?php echo $logo_size; ?>">
															<?php
															if ($sponsor_link != '') ?>
															<a href="<?php echo $sponsor_link; ?>" target="_blank">
																<img src="<?php echo $sponsor_logo['url']; ?>" alt="Maker Faire sponsor logo" class="img-responsive" />
																<?php
																if ($sponsor_link != '') ?>
															</a>
														</div>
												<?php
													}
												}
												?>
											</div>
										</div>
									</div>
								</div>
							<?php
							}
							if ($show_link == 'yes') {
							?>
								<div class="row">
									<div class="col-xs-12 text-center">
										<a class="btn btn-white more-makers-link" href="<?php echo $url; ?>">Meet The Sponsors</a>
									</div>
								</div>
							<?php
							}
							?>
						</div>
					</div>
					<?php
					if ($slide_block == 'slide') {
					?>
						<script>
							// Update the sponsor slide title each time the slide changes
							jQuery("#carousel-sponsors-slider .carousel-inner .item:first-child").addClass("active");
							jQuery(function() {
								var title = jQuery(".item.active .sponsors-type").html();
								jQuery(".sponsor-slide-cat").text(title);
								jQuery("#carousel-sponsors-slider").on("slid.bs.carousel", function() {
									var title = jQuery(".item.active .sponsors-type").html();
									jQuery(".sponsor-slide-cat").text(title);
								});
								if (jQuery(window).width() < 767) {
									jQuery(".maker-slider-btn").html("Learn More");
								}
							});
						</script>
					<?php
					}
					?>
				</div>
			</div>
<?php
			$return = ob_get_clean();
		} //end if
		echo $return;
	} //end render function

} //end class
