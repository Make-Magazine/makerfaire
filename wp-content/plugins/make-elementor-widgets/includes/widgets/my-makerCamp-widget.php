<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Maker Camp Project listings Widget
 *
 * Elementor widget that lists the Maker Camp projects that this user can access
 *
 * @since 1.0.0
 */
class Elementor_MyMakerCamp_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'makercampprojects';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Maker Camp Projects', 'elementor-make-widget' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-custom';
	}

	/**
	 * Get widget categories.
	 */
	public function get_categories() {
		return [ 'make-category' ];
	}

	/**
	 * Get widget keywords.
	 */
	public function get_keywords() {
		return [ 'make', 'camp', 'project', 'makercamp'];
	}

  /**
	 * Register widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
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
				'label' => esc_html__( 'Icon Alignment', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'after' => esc_html__( 'After', 'elementor' ),
					'before' => esc_html__( 'Before', 'elementor' ),
				],
				'default' => 'after',
				'prefix_class' => 'expandobox-align-',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend.
	 */
	protected function render() {
    $user = wp_get_current_user();
    $user_id   = $user->ID;
    $group_id = BP_Groups_Group::group_exists("maker-camp-2021");

    if (groups_is_user_member($user_id, $group_id)) {
		$settings = $this->get_settings_for_display();
        ?>
        <div class="dashboard-box make-elementor-expando-box" style="width:100%">
            <h4 class="closed"><?php echo ($settings['title']!=''?$settings['title']:'<img src="https://makercamp.com/wp-content/themes/makercamp-theme/assets/img/makercamp-logo.png" />');?></h4>
            <ul class="closed">
                <li>
                    <?php
                    $prev_blog_id = get_current_blog_id();

                    //switch to makercamp blog
                    switch_to_blog(7);

                    echo do_shortcode('[favorite_content]');
                    echo do_shortcode('[ld_course_list mycourses="true" num="10"]');

                    //switch back to main blog
                    switch_to_blog($prev_blog_id);
                    ?>
                </li>
            </ul>
        </div>
        <?php
    }
  }
}
