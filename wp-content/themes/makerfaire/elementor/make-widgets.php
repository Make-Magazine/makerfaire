<?php

class Make_Elementor_Widgets {

	protected static $instance = null;

	public static function get_instance() {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	protected function __construct() {
		// Include all function files in the make-makercamp/functions directory:
		foreach (glob(get_stylesheet_directory() . '/elementor/widgets/*.php') as $file) {
		    require_once $file;
		}
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );

	}

	public function register_widgets() {
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor\Two_Column_Video() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor\Two_Column_Image() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor\RSS_Feed() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor\Featured_Makers_Dynamic() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor\Featured_Items() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor\Upcoming_Faires() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor\Upcoming_Call_For_Makers() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor\Sponsors() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor\Image_Slider() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor\Hero_Panel() );
	}

}

add_action( 'init', 'make_elementor_init' );

function make_elementor_init() {
	Make_Elementor_Widgets::get_instance();
}


add_action( 'elementor/elements/categories_registered', 'add_elementor_widget_categories' );
function add_elementor_widget_categories($elements_manager) {
	 $elements_manager->add_category(
		'make',
		array(
			'title' => __( 'Make: Widgets', 'plugin-name' ),
			'icon'  => 'fa fa-plug',
		),
		1
	);
}
