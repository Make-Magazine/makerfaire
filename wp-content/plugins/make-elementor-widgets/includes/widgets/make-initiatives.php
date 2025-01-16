<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Make: Initiatives Widget
 *
 * Elementor widget that adds a repeater line that contains the following fields:
 *    image
 *		title
 *		description
 *		link URL
 *	this needs to to have the ability to randomly show the repeater items
 *
 * @since 1.0.0
 */
class Elementor_makeInitatives_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'makeinitatives';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Make: Initiatives', 'elementor-make-widget' );
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
		return [ 'make', 'initiatives', 'repeater' ];
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
    				'label' => esc_html__( 'Title (optional)', 'elementor-make-widget' ),
    				'type' => \Elementor\Controls_Manager::TEXT,
    				'placeholder' => esc_html__( 'Enter your title', 'elementor-make-widget' ),
    			]
    		);

	  $repeater = new \Elementor\Repeater();

		//title
		$repeater->add_control(
			'list_title', [
				'label' => esc_html__( 'Title', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Title' , 'elementor-make-widget' ),
				'label_block' => true,
			]
		);

		//image
		$repeater->add_control(
			'image',
			[
				'label' => esc_html__( 'Choose Image', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		//description
		$repeater->add_control(
			'item_description',
			[
				'label' => esc_html__( 'Description', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'rows' => 10,
				'placeholder' => esc_html__( 'Type your description here', 'elementor-make-widget' ),
			]
		);

		//url
		$repeater->add_control(
			'content_link',
			[
				'label' => __( 'Content Link', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( '#' , 'elementor-make-widget' ),
				'show_label' => true,
			]
		);

		//content title
		$repeater->add_control(
			'content_title', [
				'label' => esc_html__( 'Content Title', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Read More' , 'elementor-make-widget' ),
				'label_block' => true,
			]
		);


		$this->add_control(
			'list',
			[
				'label' => esc_html__( 'Repeater List', 'elementor-make-widget' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'list_title' => esc_html__( 'Title #1', 'elementor-make-widget' ),
						'list_content' => esc_html__( 'Item content. Click the edit button to change this text.', 'elementor-make-widget' ),
					],
					[
						'list_title' => esc_html__( 'Title #2', 'elementor-make-widget' ),
						'list_content' => esc_html__( 'Item content. Click the edit button to change this text.', 'elementor-make-widget' ),
					],
				],
				'title_field' => '{{{ list_title }}}',
			]
		);

		//randomize the list options
		$this->add_control(
			'randomize_list',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => esc_html__( 'Randomize List', 'elementor-make-widget' ),
				'options' => [
					'yes' => esc_html__( 'Yes', 'elementor-make-widget' ),
					'no' => esc_html__( 'No', 'elementor-make-widget' ),
				],
				'default' => 'yes',
			]
		);

		//number to show
		$this->add_control(
			'num_show',
			[
				'type' => \Elementor\Controls_Manager::NUMBER,
				'label' => esc_html__( 'Number to Display', 'elementor-make-widget' ),
				'placeholder' => '0',
				'min' => 0,
				'max' => 10,
				'step' => 1,
				'default' => 5,
			]
		);
		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$title = $settings['title'];

		//current user
		$current_user = wp_get_current_user();
		$user_email = $current_user->user_email;

		if ( $settings['list'] ) {
			//random sort the list items
			if($settings['randomize_list']=='yes')
				shuffle($settings['list']);

			echo '<div class="make-initiatives-widget">';
			foreach (array_slice($settings['list'], 0, $settings['num_show'])   as $item ) {
				echo '<div class="make-list-item">';
					echo '<div class="make-image-area">';
					echo 	'<a target="_blank" href="'. $item['content_link'] .'">';
					echo 		'<div class="make-image">'. wp_get_attachment_image( $item['image']['id'], 'thumbnail' ).'</div>';
					echo 	'</a>';
					echo '</div>';

					echo '<div class="make-content-area">';
					echo 		'<h3 class="make-post-title">';
					echo 			'<a target="_blank" href="'. $item['content_link'] .'">'.$item['list_title'].'</a>';
					echo 		'</h3>';
					echo 		'<div class="make-description">'.$item['item_description'].'</div>';
					echo 		'<div class="make-link">';
					echo 			'<a target="_blank" href="'. $item['content_link'] .'">'.$item['content_title'].'</a>';
					echo 		'</div>';
					echo '</div>';
				echo '</div>';
			}
			echo '</div>';
		}
	}

}
