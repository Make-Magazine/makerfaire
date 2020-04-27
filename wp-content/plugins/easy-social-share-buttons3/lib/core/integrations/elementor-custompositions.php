<?php

if ( ! defined( 'ABSPATH' ) ) exit;


class ESSBElementorCustomDisplayElement {
	private static $instance = null;

	public static function get_instance() {
		if ( ! self::$instance )
			self::$instance = new self;
		return self::$instance;
	}

	public function init(){
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'widgets_registered' ) );

	}
	public function widgets_registered() {
		// We check if the Elementor plugin has been installed / activated.
		
		if(defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base')){
			// We look for any theme overrides for this custom Elementor element.
			// If no theme overrides are found we use the default one in this plugin.
			//
			$template_file = plugin_dir_path(__FILE__).'elementor-custompositions-widget.php';

			if ( $template_file && is_readable( $template_file ) ) {
				require_once $template_file;
			}

			$widget_manager = \Elementor\Plugin::instance()->widgets_manager;
			$widget_manager->register_widget_type( new Elementor\ESSB_Elementor_Custom_Positions_Widget() );
		}
	}
}

ESSBElementorCustomDisplayElement::get_instance()->init();
