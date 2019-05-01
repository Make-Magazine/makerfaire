<?php
if (!function_exists('essb_active_oninstall')) {
	/**
	 * The core plugin activate function. It is designed to activate default plugin options
	 * and if needed to activate the first time widget wizard.
	 * 
	 * @package EasySocialShareButtons
	 * @since 6.0
	 * @author appscreo
	 */
	function essb_active_oninstall() {
		$mail_salt_check = get_option(ESSB3_MAIL_SALT);
		if (!$mail_salt_check || empty($mail_salt_check)) {
			$new_salt = mt_rand();
			update_option(ESSB3_MAIL_SALT, $new_salt);
		}
		
		$exist_settings = get_option(ESSB3_OPTIONS_NAME);
		if (!$exist_settings) {
			if (!function_exists('essb_generate_default_settings')) {
				include_once (ESSB3_PLUGIN_ROOT . 'lib/core/options/default-options.php');
			}
			$options_base = essb_generate_default_settings();
			if ($options_base) {
				update_option(ESSB3_OPTIONS_NAME, $options_base);
			}
			update_option(ESSB3_FIRST_TIME_NAME, 'true');
		}
		// clear stored add-ons on activation of plugin
		delete_option('essb3_addons');
	}
}