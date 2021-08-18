<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class ESSBElementorWidgetsLoader {

	private static $instance = null;

	public static function get_instance() {
		if ( ! self::$instance )
			self::$instance = new self;
		return self::$instance;
	}

	public function init(){
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'widgets_registered' ) );
		add_action( 'elementor/elements/categories_registered', array($this, 'add_elementor_widget_categories') );

	}

	public function add_elementor_widget_categories($elements_manager ) {
		$elements_manager->add_category(
				'essb',
				[
				'title' => esc_html__( 'Easy Social Share Buttons', 'essb' ),
				'icon' => 'eicon-share',
				]
		);
	}

	public function widgets_registered() {

		// We check if the Elementor plugin has been installed / activated.
		if(defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base')){
			// We look for any theme overrides for this custom Elementor element.
			// If no theme overrides are found we use the default one in this plugin.

			$template_file = plugin_dir_path(__FILE__).'widgets/widget-sharable-quotes.php';

			if ( $template_file && is_readable( $template_file ) ) {
				require_once $template_file;
			}

			$template_file = plugin_dir_path(__FILE__).'widgets/widget-subscribe-form.php';

			if ( $template_file && is_readable( $template_file ) ) {
				require_once $template_file;
			}

			$template_file = plugin_dir_path(__FILE__).'widgets/widget-pinterest-image.php';

			if ( $template_file && is_readable( $template_file ) ) {
				require_once $template_file;
			}

			$template_file = plugin_dir_path(__FILE__).'widgets/widget-share-action-button.php';

			if ( $template_file && is_readable( $template_file ) ) {
				require_once $template_file;
			}

			$template_file = plugin_dir_path(__FILE__).'widgets/widget-followers-counter.php';

			if ( $template_file && is_readable( $template_file ) ) {
				require_once $template_file;
			}
			
			$template_file = plugin_dir_path(__FILE__).'widgets/widget-instagram.php';
			
			if ( $template_file && is_readable( $template_file ) ) {
				require_once $template_file;
			}
			
			$template_file = plugin_dir_path(__FILE__).'widgets/widget-social-profiles.php';
			
			if ( $template_file && is_readable( $template_file ) ) {
			    require_once $template_file;
			}


			$widget_manager = \Elementor\Plugin::instance()->widgets_manager;
			$widget_manager->register_widget_type( new Elementor\ESSB_Elementor_Sharable_Qutotes_Widget() );
			$widget_manager->register_widget_type( new Elementor\ESSB_Elementor_Subscribe_Form_Widget() );
			$widget_manager->register_widget_type( new Elementor\ESSB_Elementor_Pinterest_Image_Widget() );
			$widget_manager->register_widget_type( new Elementor\ESSB_Elementor_Share_Action_Button_Widget() );
			$widget_manager->register_widget_type( new Elementor\ESSB_Elementor_Followers_Counter_Widget() );
			$widget_manager->register_widget_type( new Elementor\ESSB_Elementor_Instagram_Feed_Widget() );
			$widget_manager->register_widget_type( new Elementor\ESSB_Elementor_Social_Profiles_Widget() );
		}
	}
}

ESSBElementorWidgetsLoader::get_instance()->init();
