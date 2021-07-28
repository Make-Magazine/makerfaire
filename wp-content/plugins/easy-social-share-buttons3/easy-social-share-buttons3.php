<?php

/**
* Plugin Name: Easy Social Share Buttons for WordPress
* Description: The first true all in one social media plugin for WordPress, including social share buttons, social followers counter, social profile links, click to tweet, Pinnable images, after share events, subscribe forms, Instagram feed, social proof notifications and much more.
* Plugin URI: https://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=appscreo
* Version: 7.9
* Author: CreoApps
* Author URI: https://codecanyon.net/user/appscreo/portfolio?ref=appscreo
*/


if (! defined ( 'WPINC' ))
	die ();

if (defined('ESSB3_VERSION')) {
    /**
	* Exit if the Easy Social Share Buttons for WordPress is running - prevent multiple extensions
	*/
    return;
}

define ( 'ESSB3_VERSION', '7.9' );
define ( 'ESSB3_PLUGIN_ROOT', dirname ( __FILE__ ) . '/' );
define ( 'ESSB3_PLUGIN_URL', plugins_url () . '/' . basename ( dirname ( __FILE__ ) ) );
define ( 'ESSB3_PLUGIN_BASE_NAME', plugin_basename ( __FILE__ ) );
define ( 'ESSB3_OPTIONS_NAME', 'easy-social-share-buttons3');
define ( 'ESSB3_WPML_OPTIONS_NAME', 'easy-social-share-buttons3-wpml');
define ( 'ESSB3_OPTIONS_NAME_FANSCOUNTER', 'easy-social-share-buttons3-fanscounter');
define ( 'ESSB3_EASYMODE_NAME', 'essb3-easymode');
define ( 'ESSB3_FIRST_TIME_NAME', 'essb3-firsttime');
define ( 'ESSB3_TEXT_DOMAIN', 'essb');
define ( 'ESSB3_TRACKER_TABLE', 'essb3_click_stats');
define ( 'ESSB3_MAIL_SALT', 'easy-social-share-buttons-mailsecurity');

define ( 'ESSB3_DEMO_MODE', true);
define ( 'ESSB3_ADDONS_ACTIVE', true);
define ( 'ESSB3_ACTIVATION', true);
define ( 'ESSB3_SETTING5', true);

define ( 'ESSB3_LIB_PATH', ESSB3_PLUGIN_ROOT . 'lib/');
define ( 'ESSB3_HELPERS_PATH', ESSB3_LIB_PATH . 'helpers/');


/**
 * Easy Social Share Buttons manager class to access all plugin features
 *
 * @package EasySocialShareButtons
 * @author  appscreo
 * @since   3.4
 *
 */
class ESSB_Manager {

	/**
	 * Initialized as theme
	 * @since 3.4
	 */
	private $is_in_theme = false;

	/**
	 * Disable automatic plugin updates
	 * @since 3.4
	 */
	private $disable_updater = false;

	/**
	 * Component factory
	 * @since 3.4
	 */
	private $factory = array();

	/**
	 * Plugin settings for faster access
	 * @since 3.4
	 */
	public $settings;

	/**
	 * Is mobile device
	 * @var bool
	 * @since 3.4.2
	 */
	private $is_mobile = false;

	/**
	 * Is tablet device
	 * @var bool
	 * @since 3.4.2
	 */
	private $is_tablet = false;

	/**
	 * Handle state of checked for mobile device
	 * @var bool
	 * @since 3.4.2
	 */
	private $mobile_checked = false;

	private static $_instance;

	private function __construct() {
	    // Early loading functions of plugin
	    include_once (ESSB3_HELPERS_PATH . 'helpers-priority-load.php');
	    
	    // include the helper factory to get access to main plugin component
	    include_once (ESSB3_HELPERS_PATH . 'helpers-core.php');

		// default plugin options
		include_once (ESSB3_PLUGIN_ROOT . 'lib/core/options/essb-options-defaults.php');
		include_once (ESSB3_PLUGIN_ROOT . 'lib/core/options/essb-admin-options-defaults.php');

		// activation/deactivation hooks
		register_activation_hook ( __FILE__, array ('ESSB_Manager', 'activate' ) );
		register_deactivation_hook ( __FILE__, array ('ESSB_Manager', 'deactivate' ) );

		// initialize plugin
		add_action( 'plugins_loaded', array( &$this, 'load_widgets' ), 9);		
		add_action( 'init', array( &$this, 'init' ), 9);

		if (is_admin()) {
			if (!defined('ESSB3_AVOID_WELCOME') && !$this->isInTheme()) {
				function essb_page_welcome_redirect() {
					$redirect = get_transient( '_essb_page_welcome_redirect' );
					delete_transient( '_essb_page_welcome_redirect' );
					$redirect && wp_redirect( esc_url(admin_url( 'admin.php?page=essb_redirect_about' )) );
				}
				add_action( 'init', 'essb_page_welcome_redirect' );
			}
		}
	}

	/**
	 * Get static instance of class
	 *
	 * @return ESSB_Manager
	 */
	public static function instance() {
		if ( ! ( self::$_instance instanceof self ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}


	/**
	 * Cloning disabled
	 */
	public function __clone() {
	}

	/**
	 * Serialization disabled
	 */
	public function __sleep() {
	}

	/**
	 * De-serialization disabled
	 */
	public function __wakeup() {
	}

	/**
	 * Initialize plugin load
	 */
	public function init() {

		// @since 4.2 option to disable translations
		if (!essb_option_bool_value('disable_translation')) {
			load_plugin_textdomain('essb', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
		}

		$this->resourceBuilder();
		$this->essb();

		// Share Optimization Tags
		if (ESSB_Runtime_Cache::running('sso-running')) {
		    if (class_exists('ESSB_OpenGraph')) {
		        ESSB_Factory_Loader::activate('sso-opengraph', 'ESSB_OpenGraph');
		    }
		    
		    if (class_exists('ESSB_WooCommerceOpenGraph')) {
		        ESSB_Factory_Loader::activate('sso-opengraph-woo', 'ESSB_WooCommerceOpenGraph');
		    }
		    if (class_exists('ESSB_TwitterCards')) {
		        ESSB_Factory_Loader::activate('sso-twitter-cards', 'ESSB_TwitterCards');
		    }
		    
		    if (class_exists('ESSB_TaxonomyOptimizations')) {
		        ESSB_Factory_Loader::activate('sso-taxonomy', 'ESSB_TaxonomyOptimizations');
		    }
		}		

		// Share Analytics
		if (ESSB_Runtime_Cache::running('stats-running')) {
		    ESSB_Factory_Loader::activate_instance('internal-stats', 'ESSBSocialShareAnalytics');
		    $this->resourceBuilder()->add_js(ESSB_Factory_Loader::get('internal-stats')->generate_tracker_code(), true, 'essb-stats-tracker');
		}

		// After Share Actions
		if (ESSB_Runtime_Cache::running('after-share-running')) {
		    ESSB_Factory_Loader::activate('after-share', 'ESSBAfterCloseShare3');
		    
		    /**
		     * Pinterest javascript API check - if it is used automatically 
		     * switching to the legacy share method. Required to prevent visual 
		     * glitches in the Pin button.
		     */
		    if (ESSB_Factory_Loader::get('after-share')->pinterest_api_loaded()) {
		        ESSB_Plugin_Options::set('pinterest_using_api', 'true');
		    }
		}

		// On Media Sharing
		if (ESSB_Runtime_Cache::running('onmedia-running')) {
			ESSB_Factory_Loader::activate('onmedia', 'ESSBSocialImageShare');
			essb_depend_load_function('essb_rs_css_build_imageshare_customizer', 'lib/core/resource-snippets/essb_rs_css_build_imageshare_customizer.php');

		}

		// Social Profiles
		if (!defined('ESSB3_LIGHTMODE')) {
			if (defined('ESSB3_SOCIALPROFILES_ACTIVE')) {
				ESSB_Factory_Loader::activate('essbsp', 'ESSBSocialProfiles');
			}
		}

		// Followers Counter
		if (defined('ESSB3_SOCIALFANS_ACTIVE')) {
			ESSB_Factory_Loader::activate('essbfc', 'ESSBSocialFollowersCounter');
		}
		
		if (!essb_option_bool_value('deactivate_module_instagram') && class_exists('ESSBInstagramFeed')) {
		    ESSB_Factory_Loader::activate_instance('instagram', 'ESSBInstagramFeed');
		}
		
		if (!essb_option_bool_value('deactivate_module_proofnotifications') && class_exists('ESSBSocialProofNotificationsLite')) {
		    ESSB_Factory_Loader::activate('spn-lite', 'ESSBSocialProofNotificationsLite');
		}

		if (!defined('ESSB3_LIGHTMODE')) {
			if (defined('ESSB3_NATIVE_ACTIVE')) {
				// Social Privacy Buttons when active include resources
				$essb_spb = ESSBSocialPrivacyNativeButtons::get_instance();
				ESSBNativeButtonsHelper::$essb_spb = $essb_spb;
				foreach ($this->privacyNativeButtons()->resource_files as $key => $object) {
					$this->resourceBuilder()->add_static_resource($object['file'], $object['key'], $object['type']);
				}
				foreach (ESSBSkinnedNativeButtons::get_assets() as $key => $object) {
					$this->resourceBuilder()->add_static_resource($object['file'], $object['key'], $object['type']);
				}
				$this->resourceBuilder()->add_css(ESSBSkinnedNativeButtons::generate_skinned_custom_css(), 'essb-skinned-native-buttons');

				// asign instance of native buttons privacy class to helper

				// register active social network apis
				foreach (ESSBNativeButtonsHelper::get_list_of_social_apis() as $key => $code) {
					$this->resourceBuilder()->add_social_api($key);
				}
			}
		}

		// @since 4.2 Live Customizer Initialization
		if (essb_live_customizer_can_run()) {
            include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/live-customizer/essb-live-customizer.php');
            ESSB_Factory_Loader::activate('essb_live_customizer', 'ESSBLiveCustomizer');
        }

		if (is_admin()) {
			$this->asAdmin();
		}

	}



	/**
	 * Load plugin active widgets based on user settings
	 */
	public function load_widgets() {
		// include the main plugin required files
		include_once (ESSB3_PLUGIN_ROOT . 'lib/essb-core-includes.php');

		ESSBActivationManager::init();

		if (is_admin()) {

			$exist_user_purchase_code = essb_sanitize_option_value('purchase_code');
			$deactivate_updates = essb_option_bool_value('deactivate_updates');

			if (ESSBActivationManager::isActivated() && !$this->isInTheme() && !$deactivate_updates) {

				include (ESSB3_PLUGIN_ROOT . 'lib/external/autoupdate/plugin-update-checker.php');
				
				$update_url = 'https://update.creoworx.com/easy-social-share-buttons3/?action=get_metadata&slug=easy-social-share-buttons3';
				
				// @since 1.3.3
				// autoupdate
				// activating autoupdate option
				$essb_autoupdate = Puc_v4_Factory::buildUpdateChecker ( $update_url, __FILE__, 'easy-social-share-buttons3' );
				// @since 1.3.7.2 - update to avoid issues with other plugins that uses same
				// method
				function addSecretKeyESSB3($query) {
					global $exist_user_purchase_code;
					$query ['license'] = ESSBActivationManager::getActivationCode();
					$query ['purchase_code'] = ESSBActivationManager::getPurchaseCode();
					$query ['domain'] = ESSBActivationManager::domain();
					return $query;
				}
				$essb_autoupdate->addQueryArgFilter ( 'addSecretKeyESSB3' );
			}

		}
	}

	/**
	 * setIsInTheme
	 *
	 * Tell plugin that is loaded in theme - disable automatic updates and disable redirect after install
	 * @param bool $value
	 */
	public function setIsInTheme ( $value = true) {
		$this->is_in_theme = (boolean) $value;
	}

	public function isInTheme () {
		return (boolean) $this->is_in_theme;
	}

	public function disableUpdates() {
		$this->disable_updater = true;
	}

	/**
	 * @return NULL|mixed
	 */
	public function resourceBuilder() {		
	    if (!ESSB_Factory_Loader::running('resource-builder')) {
	        ESSB_Factory_Loader::activate('resource-builder', 'ESSB_Plugin_Assets');
	        
	        /**
	         * Deprecating the old resource builder
	         * ESSB_Factory_Loader::activate('resource-builder', 'ESSBResourceBuilder');
	         */	        
	    }
	    
	    return ESSB_Factory_Loader::get('resource-builder');
	}

	public function essb() {	    
	    if (!ESSB_Factory_Loader::running('essb-core')) {
	        ESSB_Factory_Loader::activate('essb-core', 'ESSBCore');
	    }
	    
	    return ESSB_Factory_Loader::get('essb-core');
	}

	public function privacyNativeButtons() {
		if (!isset($this->factory['nativeprivacy'])) {
			$this->factory['nativeprivacy'] = new ESSBSocialPrivacyNativeButtons;
		}

		return $this->factory['nativeprivacy'];
	}

	public function socialFollowersCounter() {
	    if (!ESSB_Factory_Loader::running('essbfc')) {
	        ESSB_Factory_Loader::activate('essbfc', 'ESSBSocialFollowersCounter');
	    }
	    
	    return ESSB_Factory_Loader::get('essbfc');	    
	}	

	public function deactiveExecution() {
		$this->essb()->temporary_deactivate_content_filters();
	}

	public function reactivateExecution() {
		$this->essb()->reactivate_content_filters_after_temporary_deactivate();
	}

	public function essbOptions() {
		if (!isset($this->settings)) {
			$this->settings = get_option(ESSB3_OPTIONS_NAME);
		}

		return $this->settings;
	}


	/**
	 * isMobile
	 *
	 * Checks and return state of mobile device detected
	 *
	 * @return boolean
	 * @since 3.4.2
	 */
	public function isMobile() {
		if (!$this->mobile_checked) {
			
			if (!class_exists('ESSB_Mobile_Detect')) {
				include_once (ESSB3_PLUGIN_ROOT . 'lib/external/mobile-detect/mobile-detect.php');
			}
			
			$this->mobile_checked = true;
			$mobile_detect = new ESSB_Mobile_Detect();

			$this->is_mobile = $mobile_detect->isMobile();
			$this->is_tablet = $mobile_detect->isTablet();

			if (essb_option_bool_value('mobile_exclude_tablet') && $this->is_tablet) {
				$this->is_mobile = false;
			}
			unset($mobile_detect);

			return $this->is_mobile;
		}
		else {
			return $this->is_mobile;
		}
	}

	public function isTablet() {
		if (!$this->mobile_checked) {
			if (!class_exists('ESSB_Mobile_Detect')) {
				include_once (ESSB3_PLUGIN_ROOT . 'lib/external/mobile-detect/mobile-detect.php');
			}
			$this->mobile_checked = true;
			$mobile_detect = new ESSB_Mobile_Detect();

			$this->is_mobile = $mobile_detect->isMobile();
			$this->is_tablet = $mobile_detect->isTablet();

			unset($mobile_detect);

			return $this->is_tablet;
		}
		else {
			return $this->is_tablet;
		}
	}


	/**
	 * Run admin part of code, when user with admin capabilites is detected
	 *
	 * @since 3.4
	 */
	protected function asAdmin() {
		include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/essb-admin-includes.php');
		ESSB_Factory_Loader::activate('essb-admin', 'ESSBAdminControler');
	}

	/**
	 * factoryActivate
	 *
	 * Load plugin component into main class
	 *
	 * @param string $module
	 * @param object $class_name
	 * @since 3.4
	 */
	public function factoryActivate($module = '', $class_name) {
		if (!empty($module) && !isset($this->factory[$module])) {
			$this->factory[$module] = new $class_name;
		}
	}

	/*
	 * Static activation/deactivation hooks
	 */

	public static function activate() {
		include_once(ESSB3_PLUGIN_ROOT . 'activate.php');
		essb_active_oninstall();

		// activate redirection hook
		if ( ! is_network_admin() ) {
			set_transient( '_essb_page_welcome_redirect', 1, 30 );
		}
	}

	/**
	 * @param unknown $options
	 * @return mixed|NULL
	 */
	public static function convert_ready_made_option($options) {
		$options = base64_decode ( $options );

		$options = htmlspecialchars_decode ( $options );
		$options = stripslashes ( $options );

		if ($options != '') {
			$imported_options = json_decode ( $options, true );

			return $imported_options;
		}
		else {
			return null;
		}
	}

	public static function deactivate() {
		delete_option(ESSB3_MAIL_SALT);
	}
}

/**
 * Initialize plugin with main global instace of ESSB_Manager
 *
 * @since 3.4
 */
ESSB_Manager::instance();
