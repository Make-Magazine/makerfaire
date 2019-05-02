<?php
/**
 * Advanced Actions Library
 * Advanced remote options that will appear to manage
 *
 * @package EasySocialShareButtons
 * @since 5.9
 */
class ESSBAdvancedOptions {

	private static $instance = null;

	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	} // end get_instance;

	public function __construct() {
		add_action ( 'wp_ajax_essb_advanced_options', array($this, 'request_parser') );
	}

	/**
	 * The request_parser function runs everytime when the style manager action is called.
	 * It will dispatch the event to the internal class function and return the required
	 * for front-end data
	 *
	 * @since 5.9
	 */
	public function request_parser() {
		$cmd = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : '';

		/**
		 * Security verify of the sender
		 */

		if (! isset( $_REQUEST['essb_advancedoptions_token'] ) || !wp_verify_nonce( $_REQUEST['essb_advancedoptions_token'], 'essb_advancedoptions_setup' )) {
			print 'Sorry, your nonce did not verify.';
			wp_die();
		}

		/**
		 * Loading the form designer functios that are required to work and deal
		 * with load save and update. But load only if we have not done than in the past.
		 */
		if (! function_exists ( 'essb5_get_form_designs' )) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/helpers/formdesigner-helper.php');
		}


		if ($cmd == 'get') {
			$this->get_options();
		}

		if ($cmd == 'save') {
			echo json_encode($this->save_options());
		}

		if ($cmd == 'create_position') {
			echo json_encode($this->create_new_position());
		}

		if ($cmd == 'remove_position') {
			echo json_encode($this->remove_position());
		}

		if ($cmd == 'remove_form_design') {
			$this->remove_form_design();
		}

		if ($cmd == 'reset_command') {
			$this->reset_plugin_data();
		}

		if ($cmd == 'conversio_lists') {
			echo json_encode($this->get_conversio_lists());
		}

		// exit command execution
		wp_die();
	}

	public function get_conversio_lists() {
		$apiKey = isset($_REQUEST['api']) ? $_REQUEST['api'] : '';

		$server_response = array();

		try {
			$curl = curl_init('https://app.conversio.com/api/v1/customer-lists');
			curl_setopt($curl, CURLOPT_TIMEOUT, 10);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
			//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-ApiKey: '.$apiKey, 'Accept: application/json'));

			$server_response = curl_exec($curl);
			curl_close($curl);

		}
		catch (Exception $e) {
		}
		return $server_response;
	}

	public function remove_position() {
		$key = isset($_REQUEST['position']) ? $_REQUEST['position'] : '';

		$positions = essb5_get_custom_positions();

		if (!is_array($positions)) {
			$positions = array();
		}

		if (isset($positions[$key])) {
			unset($positions[$key]);
		}

		essb5_save_custom_positions($positions);

		return $positions;
	}

	public function create_new_position() {
		$position_name = isset($_REQUEST['position_name']) ? $_REQUEST['position_name'] : '';

		$positions = essb5_get_custom_positions();

		if (!is_array($positions)) { $positions = array(); }

		$key = time();

		$positions[$key] = $position_name;

		essb5_save_custom_positions($positions);

		return $positions;
	}

	/**
	 * Loading options from file. The function will include a PHP file where the settings
	 * will be described like inside the plugin menu
	 *
	 * @since 5.9
	 */
	public function get_options() {
		$current_tab = isset($_REQUEST['settings']) ? $_REQUEST['settings'] : '';

		// returning empty result because there is no settup tab
		if ($current_tab == '') { return; }

		if ($current_tab == 'mode') {
			$this->load_settings('mode');
		}

		// subscribe designs
		if ($current_tab == 'subscribe-design1' || $current_tab == 'subscribe-design2' ||
				$current_tab == 'subscribe-design3' || $current_tab == 'subscribe-design4' ||
				$current_tab == 'subscribe-design5' || $current_tab == 'subscribe-design6' ||
				$current_tab == 'subscribe-design7' || $current_tab == 'subscribe-design8' ||
				$current_tab == 'subscribe-design9') {
			$this->load_settings($current_tab);
		}

		if ($current_tab == 'manage_subscribe_forms') {
			$this->load_settings('form-designer');
		}

		if ($current_tab == 'manage-positions') {
			$this->load_settings('manage-positions');
		}

		if ($current_tab == 'share-recovery') {
			$this->load_settings('share-recovery');
		}

		if ($current_tab == 'avoid-negative') {
			$this->load_settings('avoid-negative');
		}

		if ($current_tab == 'share-fake') {
			$this->load_settings('share-fake');
		}

		if ($current_tab == 'features') {
			$this->load_settings('features');
		}

		if ($current_tab == 'advanced-deactivate') {
			$this->load_settings('advanced-deactivate');
		}
	}

	public function get_subcategories() {
		$current_tab = isset($_REQUEST['settings']) ? $_REQUEST['settings'] : '';
	}


	/**
	 * Including a PHP file with the existing settings (template)
	 *
	 * @param {string} $settings_file
	 */
	public function load_settings($settings_file = '') {
		if ($settings_file == '') {
			return;
		}

		include_once ESSB3_PLUGIN_ROOT . 'lib/admin/advanced-options/setup/ao-'.$settings_file.'.php';
	}

	public function remove_form_design() {
		$design = isset($_REQUEST['design']) ? $_REQUEST['design'] : '';

		if ($design != '') {
			essb5_form_remove_design($design);
		}
	}

	/**
	 * Hold down the save options actions.
	 *
	 * @since 5.9
	 */
	public function save_options() {
		$group = isset($_REQUEST['group']) ? $_REQUEST['group'] : '';
		$options = isset($_REQUEST['advanced_options']) ? $_REQUEST['advanced_options'] : '';
		$r = array();

		if (empty($group)) { $group = 'essb_options'; }


		if ($group == 'essb_options_forms') {
			$this->save_subscribe_form($options);
		}
		else {
			// Loading existing saved settings for the options group
			$current_settings = $this->get_plugin_options($group);

			if (!empty($options)) {
				foreach ($options as $key => $value) {
					$current_settings = $this->apply_settings_value($current_settings, $key, $value);
					$r[$key] = $value;
				}
			}

			// update the plugin settings
			$this->save_plugin_options($group, $current_settings);
		}
		return array('group' => $group);
	}

	/**
	 * Read the saved settings for a selected options group
	 * @param {string} $group
	 */
	public function get_plugin_options($group = '') {
		$options = array();

		if ($group == '' || $group == 'essb_options') {
			$options = get_option(ESSB3_OPTIONS_NAME);
		}
		else {
			// This will add the possibility in feature to integrate any
			// additional setup option files to the plugin library
			if (has_filter('essb_advanced_settings_get_options')) {
				$options = apply_filters('essb_advanced_settings_get_options', $group, $options);
			}
		}

		return $options;
	}

	/**
	 * Save modified settings for selected options group
	 *
	 * @param {string} $group
	 * @param {array} $options
	 */
	public function save_plugin_options($group = '', $options = array()) {
		$options = $this->clean_blank_values($options);

		if ($group == '' || $group == 'essb_options') {
			update_option(ESSB3_OPTIONS_NAME, $options);
		}

		$options = apply_filters('essb_advanced_settings_save_options', $group, $options);

	}

	public function save_subscribe_form($options = array()) {
		$design = isset($options['form_design_id']) ? $options['form_design_id'] : '';

		$existing = essb5_get_form_designs();

		if ($design == 'new') {
			$design = essb5_create_form_design();
		}

		if (isset($existing[$design])) {
			$existing[$design] = array();
		}

		foreach ($options as $key => $value) {
			if ($key != 'form_design_id') {
				$existing[$design][$key] = $value;
			}
		}

		essb5_save_form_designs($existing);
	}

	/**
	 * Add existing parameter to options. The function will make additional checks if needed
	 * and change other values too for setup paramaeters like plugin modes
	 *
	 * @param unknown_type $options
	 * @param unknown_type $param
	 * @param unknown_type $value
	 */
	public function apply_settings_value($options = array(), $param = '', $value = '') {

		$options[$param] = $value;

		if ($param == 'functions_mode') {
			$options = $this->apply_functions_mode($options, $value);
		}

		if ($param == 'activate_mobile_auto') {
			$options['functions_mode_mobile'] = ($value == 'true') ? 'auto' : '';
		}

		return $options;
	}

	/**
	 * Remove parameters without values from the settings object before saving data
	 *
	 * @param unknown_type $object
	 * @return unknown
	 */
	public function clean_blank_values($object) {
		foreach ($object as $key => $value) {
			if (!is_array($value)) {
				$value = trim($value);

				if (empty($value)) {
					unset($object[$key]);
				}
			}
			else {
				if (count($value) == 0) {
					unset($object[$key]);
				}
			}
		}

		return $object;
	}

	/**
	 * The default plugin options will be changed based on the selected plugin working
	 * mode. The change will deactivate/activate additional plugin modules and/or
	 * display methods.
	 *
	 * @param unknown_type $current_options
	 * @param unknown_type $functions_mode
	 */
	private function apply_functions_mode($current_options, $functions_mode = '') {
		$current_options['deactivate_module_aftershare'] = 'false';
		$current_options['deactivate_module_analytics'] = 'false';
		$current_options['deactivate_module_affiliate'] = 'false';
		$current_options['deactivate_module_customshare'] = 'false';
		$current_options['deactivate_module_message'] = 'false';
		$current_options['deactivate_module_metrics'] = 'false';
		$current_options['deactivate_module_translate'] = 'false';
		$current_options['deactivate_module_followers'] = 'false';
		$current_options['deactivate_module_profiles'] = 'false';
		$current_options['deactivate_module_natives'] = 'false';
		$current_options['deactivate_module_subscribe'] = 'false';
		$current_options['deactivate_module_facebookchat'] = 'false';
		$current_options['deactivate_module_skypechat'] = 'false';

		$current_options['deactivate_method_float'] = 'false';
		$current_options['deactivate_method_postfloat'] = 'false';
		$current_options['deactivate_method_sidebar'] = 'false';
		$current_options['deactivate_method_topbar'] = 'false';
		$current_options['deactivate_method_bottombar'] = 'false';
		$current_options['deactivate_method_popup'] = 'false';
		$current_options['deactivate_method_flyin'] = 'false';
		$current_options['deactivate_method_postbar'] = 'false';
		$current_options['deactivate_method_point'] = 'false';
		$current_options['deactivate_method_image'] = 'false';
		$current_options['deactivate_method_native'] = 'false';
		$current_options['deactivate_method_heroshare'] = 'false';
		$current_options['deactivate_method_integrations'] = 'false';

		if ($functions_mode == 'light') {
			$current_options['deactivate_module_aftershare'] = 'true';
			$current_options['deactivate_module_analytics'] = 'true';
			$current_options['deactivate_module_affiliate'] = 'true';
			$current_options['deactivate_module_customshare'] = 'true';
			$current_options['deactivate_module_message'] = 'true';
			$current_options['deactivate_module_metrics'] = 'true';
			$current_options['deactivate_module_translate'] = 'true';
			$current_options['deactivate_module_followers'] = 'true';
			$current_options['deactivate_module_profiles'] = 'true';
			$current_options['deactivate_module_natives'] = 'true';
			$current_options['deactivate_module_subscribe'] = 'true';
			$current_options['deactivate_module_facebookchat'] = 'true';
			$current_options['deactivate_module_skypechat'] = 'true';

			$current_options['deactivate_method_float'] = 'true';
			$current_options['deactivate_method_postfloat'] = 'true';
			$current_options['deactivate_method_topbar'] = 'true';
			$current_options['deactivate_method_bottombar'] = 'true';
			$current_options['deactivate_method_popup'] = 'true';
			$current_options['deactivate_method_flyin'] = 'true';
			$current_options['deactivate_method_postbar'] = 'true';
			$current_options['deactivate_method_point'] = 'true';
			$current_options['deactivate_method_native'] = 'true';
			$current_options['deactivate_method_heroshare'] = 'true';
			$current_options['deactivate_method_integrations'] = 'true';

			$current_options['activate_fake'] = 'false';
			$current_options['activate_hooks'] = 'false';
		}

		if ($functions_mode == 'medium') {
			$current_options['deactivate_module_affiliate'] = 'true';
			$current_options['deactivate_module_customshare'] = 'true';
			$current_options['deactivate_module_message'] = 'true';
			$current_options['deactivate_module_metrics'] = 'true';
			$current_options['deactivate_module_translate'] = 'true';

			$current_options['deactivate_module_followers'] = 'true';
			$current_options['deactivate_module_profiles'] = 'true';
			$current_options['deactivate_module_natives'] = 'true';
			$current_options['deactivate_module_facebookchat'] = 'true';
			$current_options['deactivate_module_skypechat'] = 'true';

			$current_options['deactivate_method_postfloat'] = 'true';
			$current_options['deactivate_method_topbar'] = 'true';
			$current_options['deactivate_method_bottombar'] = 'true';
			$current_options['deactivate_method_popup'] = 'true';
			$current_options['deactivate_method_flyin'] = 'true';
			$current_options['deactivate_method_point'] = 'true';
			$current_options['deactivate_method_native'] = 'true';
			$current_options['deactivate_method_heroshare'] = 'true';
			$current_options['deactivate_method_integrations'] = 'true';

			$current_options['activate_fake'] = 'false';
			$current_options['activate_hooks'] = 'false';
		}

		if ($functions_mode == 'advanced') {
			$current_options['deactivate_module_customshare'] = 'true';

			$current_options['deactivate_module_followers'] = 'true';
			$current_options['deactivate_module_profiles'] = 'true';
			$current_options['deactivate_module_natives'] = 'true';
			$current_options['deactivate_module_facebookchat'] = 'true';
			$current_options['deactivate_module_skypechat'] = 'true';

			$current_options['deactivate_method_native'] = 'true';
			$current_options['deactivate_method_heroshare'] = 'true';

			$current_options['activate_fake'] = 'false';
			$current_options['activate_hooks'] = 'false';
		}

		if ($functions_mode == 'sharefollow') {
			$current_options['deactivate_module_customshare'] = 'true';

			$current_options['deactivate_module_natives'] = 'true';

			$current_options['deactivate_method_native'] = 'true';
			$current_options['deactivate_method_heroshare'] = 'true';

			$current_options['activate_fake'] = 'false';
			$current_options['activate_hooks'] = 'false';
		}

		return $current_options;
	}

	/**
	 * Reset of plugin data
	 */

	public function reset_plugin_data() {
		$function = isset($_REQUEST['function']) ? $_REQUEST['function'] : '';

		/**
		 * Apply different forms of data reset based on selected by user action
		 */

		/**
		 * 1. Reset plugin settings to default
		 */
		if ($function == 'resetsettings') {
			$essb_admin_options = array ();
			$essb_options = array ();

			if (!function_exists('essb_generate_default_settings')) {
				include_once (ESSB3_PLUGIN_ROOT . 'lib/core/options/default-options.php');
			}
			$options_base = essb_generate_default_settings();

			if ($options_base) {
				$essb_options = $options_base;
				$essb_admin_options = $options_base;
			}
			update_option ( ESSB3_OPTIONS_NAME, $essb_admin_options );
		}

		/**
		 * 2. Reset followers counter options
		 */
		if ($function == 'resetfollowerssettings') {
			delete_option(ESSB3_OPTIONS_NAME_FANSCOUNTER);
		}

		/**
		 * 3. Internal Analaytics Data if used
		 */
		if ($function == 'resetanalytics') {
			delete_post_meta_by_key('essb_metrics_data');

			global $wpdb;
			$table  = $wpdb->prefix . 'essb3_click_stats';			
			$delete = $wpdb->query(("TRUNCATE TABLE $table"));
		}

		/**
		 * 4. Internal share counters
		 */
		if ($function == 'resetinternal') {
			$networks = essb_available_social_networks();

			foreach ($networks as $key => $data) {
				delete_post_meta_by_key('essb_pc_'.$key);
			}

			delete_post_meta_by_key('_essb_love');
		}

		/**
		 * 5. Counter update period
		 */
		if ($function == 'resetcounter') {
			delete_post_meta_by_key('essb_cache_expire');
		}

		/**
		 * 6. Short URL & Image Cache
		 */
		if ($function == 'resetimage') {

			// short URLs
			delete_post_meta_by_key('essb_shorturl_googl');
			delete_post_meta_by_key('essb_shorturl_post');
			delete_post_meta_by_key('essb_shorturl_bitly');
			delete_post_meta_by_key('essb_shorturl_ssu');
			delete_post_meta_by_key('essb_shorturl_rebrand');

			// image cache
			delete_post_meta_by_key('essb_cached_image');
		}
	}
}

if (!function_exists('essb_advancedopts_settings_group')) {
	/**
	 * Generate a group tag that will be used to find the exact options place where to save the settings
	 *
	 * @param unknown_type $group
	 */
	function essb_advancedopts_settings_group($group = '') {
		echo '<input type="hidden" id="essb-advanced-group" name="essb-advanced-group" value="'.esc_attr($group).'"/>';

		wp_nonce_field( 'essb_advanced_setup', 'essb_advanced_token' );
	}

}
