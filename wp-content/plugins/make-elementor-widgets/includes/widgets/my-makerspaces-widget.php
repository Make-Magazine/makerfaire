<?php

use FacebookAds\Object\CustomAudienceNormalizers\CityNormalizer;
use OpenCloud\Common\Constants\State;
use plainview\sdk_broadcast\form2\inputs\email;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor my Makerspaces Widget
 *
 * Elementor widget that lists the makerspaces that you have submitted and links back to edit them
 *
 * @since 1.0.0
 */
class Elementor_myMspaces_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve myMspaces_Widget widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'mymspaces';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve myMspaces_Widget widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'My MakerSpaces listing', 'elementor-make-widget' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve myMspaces_Widget widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-custom';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the myMspaces_Widget widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'make-category' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the myMspaces_Widget widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'make', 'makerspaces'];
	}

	/**
	 * Register myMspaces_Widget widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'elementor-make-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

    	$this->add_control(
    			'title',
    			[
    				'label' => esc_html__( 'Title', 'elementor-make-widget' ),
    				'type' => \Elementor\Controls_Manager::TEXT,
    				'placeholder' => esc_html__( 'Enter your title', 'elementor-make-widget' ),
    			]
    		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'elementor-make-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_alignment',
			[
				'label' => esc_html__( 'Icon Alignment', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'after' => esc_html__( 'After', 'elementor-make-widget' ),
					'before' => esc_html__( 'Before', 'elementor-make-widget' ),
				],
				'default' => 'after',
				'prefix_class' => 'expandobox-align-',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render myMspaces_Widget widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		global $wpdb;
		global $bp;

		$user = wp_get_current_user();
		$user_email = (string) $user->user_email;
		$user_slug = $user->user_nicename;

		//pull makerspace information from the makerspace site
		$sql = 'SELECT distinct(entry.id) as entry_id, '.
				'(select meta_value from wp_3_gf_entry_meta where meta_key=1 and entry_id=entry.id) as space_name, '.
				'(select meta_value from wp_3_gf_entry_meta where meta_key=2 and entry_id=entry.id) as space_website, '.
				'(select meta_value from wp_3_gf_entry_meta where meta_key=11 and entry_id=entry.id) as space_city, '.
				'(select meta_value from wp_3_gf_entry_meta where meta_key=12 and entry_id=entry.id) as space_state, '.
				'(select meta_value from wp_3_gf_entry_meta where meta_key=89 and entry_id=entry.id) as space_country, '.
				'(select meta_value from wp_3_gf_entry_meta where meta_key=13 and entry_id=entry.id) as space_zipcode '.
				'from wp_3_gf_entry entry '.
				'left outer join wp_3_gf_entry_meta contact_email '.
					'on contact_email.meta_key in("141","86") and contact_email.meta_value = "alicia.williams130@gmail.com" '.
					'and contact_email.entry_id = entry.id '.
				'where entry.form_id=5 and entry.status="Active" and contact_email.id <>""';	

		
		$ms_results = $wpdb->get_results($sql);
		if (!empty($ms_results)) {
			?>
			<div class="dashboard-box make-elementor-expando-box">
				<h4 class="closed"><?php echo ($settings['title']!=''?$settings['title']:'My Makerspace listings');?></h4>
				<ul class="closed">
			<?php
			foreach($ms_results as $ms){
				?>
				<li><p><b><?php echo $ms['space_name']; ?></b> - <?php echo $ms['space_website']; ?></p></li>
				<?php
			}
			?>
					<li><a href="https://makerspaces.make.co/edit-your-makerspace/" class="btn universal-btn">Manage your Makerspace Listing</a></li>
				</ul>
			</div>
			<?php
		}
                        
	}

}
