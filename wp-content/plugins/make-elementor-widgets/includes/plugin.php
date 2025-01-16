<?php
namespace Elementor_Make_Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin class.
 *
 * The main class that initiates and runs the addon.
 *
 * @since 1.0.0
 */
final class Plugin {

	const VERSION = '2.1.2';
	const MINIMUM_ELEMENTOR_VERSION = '3.16.0';
	const MINIMUM_PHP_VERSION = '7.4';

	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	/**
	 * Constructor
	 *
	 * Perform some compatibility checks to make sure basic requirements are meet.
	 * If all compatibility checks pass, initialize the functionality.
	 */
	public function __construct() {

		if ( $this->is_compatible() ) {
			add_action( 'elementor/init', [ $this, 'init' ] );
		}
        
	}

	/**
	 * Compatibility Checks
	 *
	 * Checks whether the site meets the addon requirement.
	 */
	public function is_compatible() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return false;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return false;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return false;
		}

		return true;

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 */
	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor_make_widgets' ),
			'<strong>' . esc_html__( 'Make: Elementor Widgets', 'elementor_make_widgets' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor_make_widgets' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 */
	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor_make_widgets' ),
			'<strong>' . esc_html__( 'Make: Elementor Widgets', 'elementor_make_widgets' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor_make_widgets' ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 */
	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor_make_widgets' ),
			'<strong>' . esc_html__( 'Make: Elementor Widgets', 'elementor_make_widgets' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'elementor_make_widgets' ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Initialize
	 *
	 * Load the addons functionality only after Elementor is initialized.
	 *
	 * Fired by `elementor/init` action hook.
	 */
	public function init() {
        //common function file
        include( __DIR__ . '/functions.php' );

        add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'frontend_styles' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'frontend_scripts' ] );

        add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		//add_action( 'elementor/controls/register', [ $this, 'register_controls' ] );        

	}

	/**
	 * Register Widgets
	 *
	 * Load widgets files and register new Elementor widgets.
	 *
	 * Fired by `elementor/widgets/register` action hook.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {
        require_once(__DIR__ . '/widgets/shed-purchases-widget.php'); // MakerShed Purchaces        
        //require_once(__DIR__ . '/widgets/my-makerspaces-widget.php'); // My Makerspaces Widget        
        require_once(__DIR__ . '/widgets/my-makerCamp-widget.php');   // My Makercamp Widget		
        require_once(__DIR__ . '/widgets/make-custom-rss-feed.php');  // Make: Custom RSS Feed Widget        
        require_once(__DIR__ . '/widgets/make-interests-rss-feed.php'); // Make: Interests RSS Feed Widget
        require_once(__DIR__ . '/widgets/upcoming-makerfaires-widget.php'); // Make: upcoming MakerFaire Widget        
        require_once(__DIR__ . '/widgets/make-initiatives.php'); //  Make: initiativies Widget
        require_once(__DIR__ . '/widgets/my-subscription-widget.php'); // Subscription information from Omeda Widget

        //register widgets
        $widgets_manager->register( new \Elementor_mShedPurch_Widget() );
        //$widgets_manager->register( new \Elementor_myMspaces_Widget() );
        
        $widgets_manager->register( new \Elementor_MyMakerCamp_Widget() );
        $widgets_manager->register( new \Elementor_makeCustomRss_Widget() );
        $widgets_manager->register( new \Elementor_makeInterestsRss_Widget() );
        $widgets_manager->register( new \Elementor_upcomingMakerFaires_Widget() );
        $widgets_manager->register( new \Elementor_makeInitatives_Widget() );
        $widgets_manager->register( new \Elementor_mySubscription_Widget() );
    
	}

	/**
	 * Register Controls
	 *
	 * Load controls files and register new Elementor controls.
	 *
	 * Fired by `elementor/controls/register` action hook.
	 *
	 * @param \Elementor\Controls_Manager $controls_manager Elementor controls manager.
	 */
    /*
	public function register_controls( $controls_manager ) {

		//require_once( __DIR__ . '/includes/controls/control-1.php' );
		//require_once( __DIR__ . '/includes/controls/control-2.php' );

		//$controls_manager->register( new Control_1() );
		//$controls_manager->register( new Control_2() );

	}*/

     /**
    * we will add stylesheet for our plugin in style.css
    */
    public function frontend_styles() {
		//widget styles		
		wp_register_style("make-elementor-style", plugins_url('/css/style.css', __FILE__), array(), self::VERSION );
		wp_enqueue_style('make-elementor-style');
		wp_register_style("jquery-ui-tabs","//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css", array(), self::VERSION );
		wp_enqueue_style('jquery-ui-tabs');
    }


   /**
    * we will register javascript files here.
    * for the form within 'my supscription' widget, we will use ajax.
    */
    public function frontend_scripts() {		
		//widget scripts
		wp_enqueue_script('make-elementor-script', plugins_url( '/js/scripts.js', __FILE__ ), array('jquery'), self::VERSION  );

		//ajax for form submission
		wp_enqueue_script('make-omeda-script', plugin_dir_url(__FILE__) . 'js/omeda.js', array('jquery','jquery-ui-tabs'), self::VERSION , true);
		wp_localize_script('make-omeda-script', 'make_ajax_object', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'ajaxnonce' => wp_create_nonce('omeda_ajax')
		));
    }

}