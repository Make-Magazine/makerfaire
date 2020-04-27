<?php
/**
 * Admin activation helper class
 * 
 * @since 4.2
 * @package EasySocialShareButtons
 * @author appscreo
 */

class ESSBAdminActivate {
	/**
	 * Return the current activation state
	 * @return boolean
	 */
	public static function is_activated() {
		return ESSBActivationManager::isActivated();
	}
	
	/**
	 * Control will the notifce for plugin activation should appear or not
	 * 
	 * @return boolean
	 */
	public static function should_display_notice() {
		$notice_dismissed = get_transient('essb3-activate-notice');
		
		if ($notice_dismissed === false) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Generate plugin activate to unlock button
	 * @param unknown_type $user_message
	 * @param unknown_type $custom_style
	 */
	public static function activateToUnlock($user_message = '', $custom_style = '') {
		if ($user_message == '') {
			$user_message = esc_html__('Activate plugin to unlock', 'essb');
		}

		$activate_url = admin_url('admin.php?page=essb_redirect_update&tab=update');
		
		$code = '<a href="'.esc_url($activate_url).'" class="essb-activate-to-unlock" style="'.esc_attr($custom_style).'"><i class="fa fa-ban"></i> '.$user_message.'</a>';
		
		return $code;
	}
	
	public static function dismiss_notice() {
		set_transient('essb3-activate-notice', 'true', 336 * HOUR_IN_SECONDS);
	}
	
	public static function notice_activate() {
		
		if (ESSBActivationManager::isThemeIntegrated()) {
			return;
		}
		
		return;
	}
	
	public static function notice_manager() {
		$dismiss_translate = isset($_REQUEST['dismiss_translate']) ? $_REQUEST['dismiss_translate'] : '';
		if ($dismiss_translate == 'true') {
			self::dismiss_notice_translate();
		}	
		
		$dismiss_subscribe = isset($_REQUEST['dismiss_subscribe']) ? $_REQUEST['dismiss_subscribe'] : '';
		// notice display
		if (self::should_display_notice_translate()) {
			self::notice_translate();
		}
	}
	
	public static function should_display_notice_translate() {
		return false;
	}
	
	public static function dismiss_notice_translate() {
		update_option('essb3-translate-notice', 'true', 'no');
	}
	
	public static function notice_translate() {
		$dismiss_url = esc_url_raw(add_query_arg(array('dismiss_translate' => 'true'), admin_url ("admin.php?page=essb_options")));
	
		$dismiss_addons_button = '<a href="'.$dismiss_url.'"  text="' . esc_html__ ( 'Close this message', 'essb' ) . '" class="status_button float_right" style="margin-right: 5px;"><i class="fa fa-close"></i>&nbsp;' . esc_html__ ( 'Close this message', 'essb' ) . '</a>';
		echo '<div class="essb-header-status">';		
		ESSBOptionsFramework::draw_hint(esc_html__('Help us make Easy Social Share Buttons speak in your language', 'essb'), sprintf('Version 4 of Easy Social Share Buttons for WordPress has fully translatable admin panel. Help up us and our customers by translating plugin in your language. Please <a href="admin.php?page=essb_redirect_advanced&tab=advanced&section=translate&subsection">view translate instructions and see how easy is.</a> %1$s', $dismiss_addons_button), 'fa fa-language', 'status');
		echo '</div>';
	}
}

?>