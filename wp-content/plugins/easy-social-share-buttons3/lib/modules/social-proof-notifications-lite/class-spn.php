<?php
/**
 * Core class for generation of Social Proof Notifiactions Lite
 * 
 * @author appscreo
 * @package EasySocialShareButtons
 * @version 1.0
 * @since 7.0
 */
class ESSBSocialProofNotificationsLite {
	
	private static $_instance;
	
	private $resources = false;
	private $optimized = false;
	
	/**
	 * Get static instance of class
	 */
	public static function getInstance() {
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
	
	public function __construct() {
		if (essb_option_bool_value('proofnotifications_show')) {
			include_once ESSB3_PLUGIN_ROOT . 'lib/modules/social-proof-notifications-lite/helper-spn-functions.php';
			
			$this->optimized = true;
			if (function_exists ( 'essb_resource_builder' )) {
				essb_resource_builder ()->add_static_resource ( ESSB3_PLUGIN_URL . '/lib/modules/social-proof-notifications-lite/assets/essb-spn-lite'.($this->optimized ? '.min': '').'.css', 'essb-spn-lite', 'css' );
				essb_resource_builder ()->add_static_resource ( ESSB3_PLUGIN_URL . '/lib/modules/social-proof-notifications-lite/assets/essb-spn-lite'.($this->optimized ? '.min': '').'.js', 'essb-spn-lite', 'js' );
				$this->resources = true;
			}
			add_action ( 'wp_footer', array($this, 'draw_notifications'), 101);
		}
	}
	
	public function can_add_automatic_popup_widget() {
		if (is_admin () || is_search() || is_feed() || get_the_ID() == '') {
			return false;
		}
		
		return true;
	}
		

	public function draw_notifications() {
		if (!$this->can_add_automatic_popup_widget()) {
			return;
		}
		
		$message_pool = array();
		$notification_count = essb_sanitize_option_value('proofnotifications_counter');
		$output = '';
		
		if (intval($notification_count) == 0 || $notification_count == '') {
			$notification_count = 1;
		}
		
		$message_pool = array_merge($message_pool, essbspnlite_get_share_notifications_pool(get_the_ID()));
		
		// Compiling the required to display code
		$message_pool = essbspnlite_compile_message_pool($message_pool);
		
		shuffle($message_pool);
		$max = count($message_pool) > $notification_count ? $notification_count : count($message_pool);
		
		for ($i = 0; $i < $max; $i++) {
			if (isset($message_pool[$i])) {
				$output .= essbspnlite_notification_draw_code($message_pool[$i], ($i + 1));
			}
		}
		
		
		if ($output != '') {
			$output = '<div class="essbspn-holder" data-count="'.esc_attr($max).'" '.essbspnlite_notification_holder_options().'>'.$output.'</div>';
			echo $output;
		}
	}	
}