<?php
/**
 * AMP share buttons generation. The buttons will appear only if the official WordPress AMP plugin is installed

 * @author appscreo
 * @since 5.0
 * @package EasySocialShareButtons
 */

if (! defined ( 'WPINC' ))
	die ();

define ( 'ESSB5_AMP_PLUGIN_ROOT', dirname ( __FILE__ ) . '/' );

class ESSBAmpSupport {
	private static $_instance;
	private $version = "2.0";
	private $options;
	private $position = "";
	
	function __construct() {
		add_action ( 'amp_init', array (&$this, 'activate_amp_support' ) );
		
		/**
		 * Additional check to validate AMP transitional running
		 */
		add_action ( 'wp_enqueue_scripts', array ($this, 'validate_amp_callback' ), 10 );
		add_action ( 'wp_head', array($this, 'push_amp_styles'));
	}
	
	public static function instance() {
		if (! (self::$_instance instanceof self)) {
			self::$_instance = new self ();
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
	
	function push_amp_styles() {
	    if (function_exists('amp_is_request') && amp_is_request()) {
	        echo '<style amp-custom>';
	        include_once (ESSB5_AMP_PLUGIN_ROOT . 'essb-amp-styles.php');
	        
	        //if (class_exists('ESSBSocialFollowersCounter') || class_exists('ESSBSocialProfiles')) {
	        //    include_once (ESSB5_AMP_PLUGIN_ROOT . 'essb-social-profiles-amp.php');
	        //}
            echo '</style>';
	    }
	}
	
	public function validate_amp_callback() {
	    if (function_exists('amp_is_request') && amp_is_request()) {
	        
	        /**
	         * Reconfigure the plugin settings using the AMP support
	         */
	        
	        ESSB_Plugin_Options::set('content_position', 'content_manual');
	        ESSB_Plugin_Options::set('button_position', array());
	        ESSB_Plugin_Options::set('live_customizer_disabled', 'true');
	        
	        // Deactivate plugin content filters aready added for sharing
	        essb_core()->temporary_deactivate_content_filters();
	        
	        // Deactivate all plugin styles
	        essb_resource_builder()->deactivate_actions();
	        ESSB_Runtime_Cache::set('amp_running', true);
	        
	        wp_enqueue_style('essb-amp-styles', ESSB3_PLUGIN_URL. '/lib/modules/amp-sharing/essb-amp-styles.php', false, ESSB3_VERSION, 'all');
	        
	        if (class_exists('ESSBSocialFollowersCounter') || class_exists('ESSBSocialProfiles')) {
	            wp_enqueue_style('essb-amp-styles-profiles', ESSB3_PLUGIN_URL. '/lib/modules/amp-sharing/essb-social-profiles-amp.php', false, ESSB3_VERSION, 'all');	            
	        }
	    }
	}
	
	public function activate_amp_support() {
		$is_mobile = true;
		
		if ($is_mobile) {
			$this->options = get_option ( 'easy-social-share-buttons3' );
			
			$content_position = essb_option_value('content_position_amp');
			
			if ($content_position != 'content_top' && $content_position != 'content_bottom' && $content_position != 'content_both') {
				$this->position = 'content_bottom';
			} else {
				$this->position = $content_position;
			}
			
			add_filter ( 'the_content', array (&$this, 'amp_display_share' ) );
			add_action ( 'amp_post_template_css', array (&$this, 'amp_load_css' ), 10 );
		}
	}
	
	public function amp_display_share($content) {
		$links_before = '';
		$links_after = '';
		
		if (essb_is_amp_page()) {
			$post_types = $this->option_value ( 'display_in_types' );
			if ($this->position == 'content_top' || $this->position == 'content_both') {
				if (essb_core ()->check_applicability ( $post_types, 'top' )) {
					$links_before = essb_core ()->generate_share_buttons ( 'amp', 'share', array ('only_share' => true, 'amp' => true ) );
				}
			}
			if ($this->position == 'content_bottom' || $this->position == 'content_both') {
				if (essb_core ()->check_applicability ( $post_types, 'bottom' )) {
					$links_after = essb_core ()->generate_share_buttons ( 'amp', 'share', array ('only_share' => true, 'amp' => true ) );
				}
			}
		}
		
		return $links_before . $content . $links_after;
	}
	
	public function is_active_mobile_support() {
		$key = 'mobile_positions';
		
		$value = isset ( $this->options [$key] ) ? $this->options [$key] : 'false';
		
		return ($value == 'true') ? true : false;
	}
	
	public function option_value($key) {
		return isset ( $this->options [$key] ) ? $this->options [$key] : '';
	}
	
	public function amp_load_css() {
		include_once (ESSB5_AMP_PLUGIN_ROOT . 'essb-amp-styles.php');
		
		if (class_exists('ESSBSocialFollowersCounter') || class_exists('ESSBSocialProfiles')) {
		    include_once (ESSB5_AMP_PLUGIN_ROOT . 'essb-social-profiles-amp.php');
		}
		
	}
}

// Loading the class for AMP display
ESSB_Factory_Loader::activate_instance('amp-support', 'ESSBAmpSupport');

