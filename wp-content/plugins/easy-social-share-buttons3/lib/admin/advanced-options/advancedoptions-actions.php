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
		
		if (! function_exists ( 'essb_get_custom_buttons' )) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/helpers/custombuttons-helper.php');
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
		
		if ($cmd == 'remove_instagram_account') {
		    $this->remove_instagram_account();
		}
		
		if ($cmd == 'update_instagram_token') {
		    $this->update_instagram_token();
		}
		
		if ($cmd == 'update_instagram_images') {
		    $this->update_instagram_images();
		}
		
		if ($cmd == 'remove_instagram_accounts') {
		    $this->remove_instagram_accounts();
		}
		
		if ($cmd == 'remove_custom_button') {
			$this->remove_custom_button();
		}

		if ($cmd == 'reset_command') {
			$this->reset_plugin_data();
		}

		if ($cmd == 'conversio_lists') {
			echo json_encode($this->get_conversio_lists());
		}
		
		/**
		 * Shortcode creation and store events
		 */
		
		if ($cmd == 'shortcode_save') {
			echo json_encode($this->shortcode_save());
		}
		
		if ($cmd == 'shortcode_get') {
			echo json_encode($this->shortcode_get());
		}
		
		if ($cmd == 'shortcode_remove') {
			echo $this->shortcode_remove();
		}
		
		if ($cmd == 'shortcode_list') {
			echo $this->shortcode_list();
		}
		
		if ($cmd == 'enable_option') {
			$this->activate_boolean_option();
		}
		
		if ($cmd == 'enable_automation') {
		    $this->enable_automation();
		}

		// exit command execution
		wp_die();
	}
	
	/**
	 * Enable automation for options setup
	 */
	public function enable_automation() {
	    $group = 'essb_options';
	    $key = isset($_REQUEST['automation']) ? sanitize_text_field($_REQUEST['automation']) : '';
	    
	    if ($key != '') {
	        $current_settings = $this->get_plugin_options($group);

	        if (! function_exists ( 'essb_admin_automation_enable' )) {
	            include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/helpers/automation-helper.php');
	        }
	        
	        $current_settings = essb_admin_automation_enable($current_settings, $key);
	        
	        $this->save_plugin_options($group, $current_settings);
	    }
	}
	
	public function activate_boolean_option() {
		$group = 'essb_options';
		$key = isset($_REQUEST['key']) ? sanitize_text_field($_REQUEST['key']) : '';
		
		if ($key != '') {
			$current_settings = $this->get_plugin_options($group);
			$current_settings[$key] = 'true';
			$this->save_plugin_options($group, $current_settings);
		}
	}
	
	/**
	 * Store a generated shortcode from plugin
	 */
	public function shortcode_save() {
		$r = '';
		
		if (class_exists('ESSBControlCenterShortcodes')) {
			$key = isset($_REQUEST['key']) ? $_REQUEST['key'] : '';
			$shortcode = isset($_REQUEST['shortcode']) ? $_REQUEST['shortcode'] : '';
			$options = isset($_REQUEST['options']) ? $_REQUEST['options'] : '';
			$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
			
			$r = ESSBControlCenterShortcodes::save_shortcode($shortcode, $options, $name, $key);
		}
		
		return array('key' => $r);
	}
	
	/**
	 * Get settings for 
	 */
	public function shortcode_get() {
		$r = array();
		$key = isset($_REQUEST['key']) ? $_REQUEST['key'] : '';
		
		if (class_exists('ESSBControlCenterShortcodes')) {
			$codes = ESSBControlCenterShortcodes::get_saved_shortcodes();
			if (isset($codes[$key])) $r = $codes[$key];
		}
		
		return $r;
	}
	
	/**
	 * List of all existing inside plugin shortcodes
	 */
	public function shortcode_list() {
		$r = '';
		
		if (class_exists('ESSBControlCenterShortcodes')) {
			$r = ESSBControlCenterShortcodes::generate_stored_shortcodes();
		}
		
		return $r;
	}
	
	/**
	 * Remove a stored shortcode
	 */
	public function shortcode_remove() {
		$key = isset($_REQUEST['shortcode_key']) ? $_REQUEST['shortcode_key'] : '';
		$r = '';
		
		if ($key != '' && class_exists('ESSBControlCenterShortcodes')) {
			ESSBControlCenterShortcodes::remove_shortcode($key);
			$r = ESSBControlCenterShortcodes::generate_stored_shortcodes();
		}
		
		return $r;
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
		
		if ($current_tab == 'manage_instagram_accounts') {
		    $this->load_settings('instagram-account');
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
		
		if ($current_tab == 'advanced-networks') {
			$this->load_settings('advanced-networks');
		}
		
		if ($current_tab == 'advanced-networks-visibility') {
			$this->load_settings('advanced-networks-visibility');
		}
		
		if ($current_tab == 'avoid-negative-proof') {
			$this->load_settings('avoid-negative-proof');
		}
		
		if ($current_tab == 'single-counter') {
			$this->load_settings('single-counter');
		}
		
		if ($current_tab == 'total-counter') {
			$this->load_settings('total-counter');
		}
		
		if ($current_tab == 'update-counter') {
			$this->load_settings('update-counter');
		}
		
		if ($current_tab == 'internal-counter') {
		    $this->load_settings('internal-counter');
		}
		
		if ($current_tab == 'adaptive-styles') {
			$this->load_settings('adaptive-styles');
		}
		
		if ($current_tab == 'facebook-ogtags') {
			$this->load_settings('facebook-ogtags');
		}
		
		if ($current_tab == 'integration-mycred') {
			$this->load_settings('integration-mycred');
		}
		
		if ($current_tab == 'integration-affiliatewp') {
			$this->load_settings('integration-affiliatewp');
		}
		
		if ($current_tab == 'integration-affiliates') {
			$this->load_settings('integration-affiliates');
		}
		
		if ($current_tab == 'analytics') {
			$this->load_settings('analytics');
		}
		
		if ($current_tab == 'share-conversions') {
			$this->load_settings('share-conversions');
		}
		
		if ($current_tab == 'metrics-lite') {
			$this->load_settings('metrics-lite');
		}
		
		if ($current_tab == 'share-google-analytics') {
			$this->load_settings('share-google-analytics');
		}
		
		if ($current_tab == 'excerpts') {
			$this->load_settings('excerpts');
		}
		
		if ($current_tab == 'after-share') {
			$this->load_settings('after-share');
		}
		
		if ($current_tab == 'style-builder') {
			$this->load_settings('style-builder');
		}
		
		if ($current_tab == 'instagramfeed-shortcode') {
			$this->load_settings('instagramfeed-shortcode');
		}
		
		if ($current_tab == 'instagramimage-shortcode') {
			$this->load_settings('instagramimage-shortcode');
		}
		
		if ($current_tab == 'other-counter') {
			$this->load_settings('other-counter');
		}
		
		if ($current_tab == 'manage-buttons') {
			$this->load_settings('button-designer');
		}
		
		if ($current_tab == 'boarding') {
			$this->load_settings('boarding');
		}
		
		/**
		 * External tabs
		 * 
		 * @since 7.4.1
		 */
		$extension_tabs = array();
		
		if (has_filter('essb_extension_advanced_tabs')) {
		    $extension_tabs = apply_filters('essb_extension_advanced_tabs', $extension_tabs);
		}
		
		if (in_array($current_tab, $extension_tabs)) {
		    $this->load_extension_settings($current_tab);
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
	
	public function load_extension_settings($settings_key = '') {
	    $file = apply_filters("essb_extension_advanced_tabs_file_{$settings_key}", $file);
	    
	    if ($file != '') {
	        include_once $file;
	    }
	}
	
	public function remove_custom_button() {
		$network_id = isset($_REQUEST['network_id']) ? $_REQUEST['network_id'] : '';
		
		if ($network_id != '') {
			essb_remove_custom_button($network_id);
		}
	}

	public function remove_form_design() {
		$design = isset($_REQUEST['design']) ? $_REQUEST['design'] : '';

		if ($design != '') {
			essb5_form_remove_design($design);
		}
	}
	
	public function remove_instagram_account() {
	    $account = isset($_REQUEST['account']) ? $_REQUEST['account'] : '';
	    
	    if ($account != '') {
	        if (!class_exists('ESSBInstagramFeed')) {
	            include_once (ESSB3_MODULES_PATH . 'instagram-feed/class-instagram-feed.php');
	        }
	        
	        ESSBInstagramFeed::remove_account($account);
	    }
	}
	
	public function update_instagram_token() {
	    $account = isset($_REQUEST['account']) ? $_REQUEST['account'] : '';
	    $token = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';
	    
	    if ($account != '' && $token != '') {
	        if (!class_exists('ESSBInstagramFeed')) {
	            include_once (ESSB3_MODULES_PATH . 'instagram-feed/class-instagram-feed.php');
	        }
	        
	        ESSBInstagramFeed::update_token($account, $token);
	    }
	}
	
	public function update_instagram_images() {
	    $account = isset($_REQUEST['account']) ? $_REQUEST['account'] : '';
	    $username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
	    
	    if ($account != '' && $username != '') {
	        if (!class_exists('ESSBInstagramFeed')) {
	            include_once (ESSB3_MODULES_PATH . 'instagram-feed/class-instagram-feed.php');
	        }
	        
	        ESSBInstagramFeed::update_images($username);
	    }
	}
	
	public function remove_instagram_accounts() {
	    if (!class_exists('ESSBInstagramFeed')) {
	        include_once (ESSB3_MODULES_PATH . 'instagram-feed/class-instagram-feed.php');
	    }
	    
	    ESSBInstagramFeed::remove_all_accounts();
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
		else if ($group == 'essb_options_ig_accounts') {
		    // @since 7.9 Instagram connected accounts
		    $this->save_instagram_account($options);
		}
		else if ($group == 'essb_options_custom_networks') {
			$this->save_custom_button($options);
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
	
	public function save_custom_button($options = array()) {
		$network_id = isset($options['network_id']) ? $options['network_id'] : '';

		$existing = essb_get_custom_buttons();
		
		
		if (isset($existing[$network_id])) {
			$existing[$network_id] = array();
		}
		
		foreach ($options as $key => $value) {
			if ($key != 'network_button_id' && $key != 'essb_advanced_token' && $key != '_wp_http_referer') {
				
				// encoding icon to prevent issues with the display
				if ($key == 'icon' && $value != '') {
					$value = base64_encode($value);
				}
				
				$existing[$network_id][$key] = $value;
			}
		}
		
		essb_save_custom_buttons($existing);
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
			if ($key != 'form_design_id' && $key != 'essb_advanced_token' && $key != '_wp_http_referer') {
			    $value = wp_kses($value, essb_subscribe_fields_safe_html());
				$existing[$design][$key] = $value;
			}
		}

		essb5_save_form_designs($existing);
	}
	
	public function save_instagram_account($options = array()) {
	    if (!class_exists('ESSBInstagramFeed')) {
	        include_once (ESSB3_MODULES_PATH . 'instagram-feed/class-instagram-feed.php');
	    }
	    
	    $account_id = isset($options['ig_account_id']) ? $options['ig_account_id'] : '';
	    $account_type = isset($options['ig_account_type']) ? $options['ig_account_type'] : '';
	    
	    if ($account_id == 'new') {
	        $account_id = ESSBInstagramFeed::get_new_account_id();
	        
	        if (empty($account_id)) {
	            $account_id = 1;
	        }
	    }
	    
	    $account_options = array();
	    
	    foreach ($options as $key => $value) {
	        if ($key != 'ig_account_id' && $key != 'essb_advanced_token' && $key != '_wp_http_referer' && $key != 'ig_account_type') {
	            $value = wp_kses($value, essb_subscribe_fields_safe_html());
	            $account_options[$key] = $value;
	        }
	    }
	    
	    $account_options['account_type'] = $account_type;
	    
	    ESSBInstagramFeed::save_account($account_id, $account_options);
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
		
		// Install the analytics table
		if ($param == "stats_active" && $value == 'true') {
		    if (!class_exists('ESSBSocialShareAnalyticsBackEnd')) {
		        include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-share-analytics/essb-social-share-analytics-backend.php');
		    }
            ESSBSocialShareAnalyticsBackEnd::install();
		}
		
		// Sanitize the subscribe form values!
		if (strpos($param, 'subscribe_mc') !== false) {
		    $options[$param] = wp_kses($value, essb_subscribe_fields_safe_html());
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
		$current_options['deactivate_module_shorturl'] = 'false';
		
		//
		$current_options['deactivate_ctt'] = 'false'; // Click to Tweet
		$current_options['deactivate_module_pinterestpro'] = 'false'; // After Share Events
		$current_options['deactivate_module_conversions'] = 'false'; // Conversion Tracking
		$current_options['deactivate_custompositions'] = 'false'; // Creating custom positions
		$current_options['deactivate_settings_post_type'] = 'false'; // Additional settings by post types
		$current_options['deactivate_module_clicktochat'] = 'false'; // Click to Chat
		$current_options['deactivate_module_instagram'] = 'false'; // Instagram feed
		$current_options['deactivate_module_proofnotifications'] = 'false'; // Social Proof Notifications

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
		
		$current_options['deactivate_custombuttons'] = 'false';
		$current_options['deactivate_module_shorturl'] = 'false';
		$current_options['deactivate_fakecounters'] = 'false';
		$current_options['activate_automatic_mobile'] = 'false';

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
			
			$current_options['deactivate_module_pinterestpro'] = 'true'; // After Share Events
			$current_options['deactivate_module_conversions'] = 'true'; // Conversion Tracking
			$current_options['deactivate_custompositions'] = 'true'; // Creating custom positions
			$current_options['deactivate_settings_post_type'] = 'true'; // Additional settings by post types
			$current_options['deactivate_module_clicktochat'] = 'true'; // Click to Chat
			$current_options['deactivate_module_instagram'] = 'true'; // Instagram feed
			$current_options['deactivate_module_proofnotifications'] = 'true'; // Social Proof Notifications

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
			
			$current_options['deactivate_custombuttons'] = 'true';
			
			$current_options['deactivate_module_shorturl'] = 'true';
			$current_options['deactivate_fakecounters'] = 'true';
			
			$current_options['deactivate_ctt'] = 'true';
		}
		
		if ($functions_mode == 'light_image') {
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
				
			$current_options['deactivate_module_conversions'] = 'true'; // Conversion Tracking
			$current_options['deactivate_settings_post_type'] = 'true'; // Additional settings by post types
			$current_options['deactivate_module_clicktochat'] = 'true'; // Click to Chat
			$current_options['deactivate_module_instagram'] = 'true'; // Instagram feed
			$current_options['deactivate_module_proofnotifications'] = 'true'; // Social Proof Notifications
		
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
			
			$current_options['deactivate_custombuttons'] = 'true';
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
			$current_options['deactivate_module_clicktochat'] = 'true'; // Click to Chat
			$current_options['deactivate_module_instagram'] = 'true'; // Instagram feed
			$current_options['deactivate_module_proofnotifications'] = 'true'; // Social Proof Notifications

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
			
			$current_options['deactivate_custombuttons'] = 'true';
		}

		if ($functions_mode == 'advanced') {
			$current_options['deactivate_module_customshare'] = 'true';

			$current_options['deactivate_module_followers'] = 'true';
			$current_options['deactivate_module_profiles'] = 'true';
			$current_options['deactivate_module_natives'] = 'true';
			$current_options['deactivate_module_facebookchat'] = 'true';
			$current_options['deactivate_module_skypechat'] = 'true';
			$current_options['deactivate_module_clicktochat'] = 'true'; // Click to Chat
			
			$current_options['deactivate_module_instagram'] = 'true'; // Instagram feed
			$current_options['deactivate_module_proofnotifications'] = 'true'; // Social Proof Notifications

			$current_options['deactivate_method_native'] = 'true';
			$current_options['deactivate_method_heroshare'] = 'true';

			$current_options['activate_fake'] = 'false';
			$current_options['activate_hooks'] = 'false';
			
			$current_options['deactivate_custombuttons'] = 'true';
		}

		if ($functions_mode == 'sharefollow') {
			$current_options['deactivate_module_customshare'] = 'true';

			$current_options['deactivate_module_natives'] = 'true';

			$current_options['deactivate_method_native'] = 'true';
			$current_options['deactivate_method_heroshare'] = 'true';
			$current_options['deactivate_custombuttons'] = 'true';

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
		 * 5.1. Counters, including internal, official and update period
		 */
		if ($function == 'resetcounterall') {
			delete_post_meta_by_key('essb_cache_expire');
			$networks = essb_available_social_networks();
			
			foreach ($networks as $key => $data) {
				delete_post_meta_by_key('essb_pc_'.$key);
				delete_post_meta_by_key('essb_c_'.$key);
			}
			delete_post_meta_by_key('essb_c_total');
			delete_post_meta_by_key('_essb_love');
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
		
		/**
		 * 7. All stored information
		 */
		if ($function == 'all') {
			// short URLs
			delete_post_meta_by_key('essb_shorturl_googl');
			delete_post_meta_by_key('essb_shorturl_post');
			delete_post_meta_by_key('essb_shorturl_bitly');
			delete_post_meta_by_key('essb_shorturl_ssu');
			delete_post_meta_by_key('essb_shorturl_rebrand');
			
			// share counters
			delete_post_meta_by_key('essb_cache_expire');
			$networks = essb_available_social_networks();
			
			foreach ($networks as $key => $data) {
				delete_post_meta_by_key('essb_c_'.$key);
				delete_post_meta_by_key('essb_pc_'.$key);
			}
			
			delete_post_meta_by_key('essb_c_total');
			
			delete_post_meta_by_key('_essb_love');
			delete_post_meta_by_key('essb_metrics_data');
			
			delete_post_meta_by_key('essb_cached_image');
			
			// post setup data
			delete_post_meta_by_key('essb_off');
			delete_post_meta_by_key('essb_post_button_style');
			delete_post_meta_by_key('essb_post_template');
			delete_post_meta_by_key('essb_post_counters');
			delete_post_meta_by_key('essb_post_counter_pos');
			delete_post_meta_by_key('essb_post_total_counter_pos');
			delete_post_meta_by_key('essb_post_customizer');
			delete_post_meta_by_key('essb_post_animations');
			delete_post_meta_by_key('essb_post_optionsbp');
			delete_post_meta_by_key('essb_post_content_position');
			foreach ( essb_available_button_positions() as $position => $name ) {
				delete_post_meta_by_key("essb_post_button_position_{$position}");
			}
			
			delete_post_meta_by_key('essb_post_native');
			delete_post_meta_by_key('essb_post_native_skin');
			delete_post_meta_by_key('essb_post_share_message');
			delete_post_meta_by_key('essb_post_share_url');
			delete_post_meta_by_key('essb_post_share_image');
			delete_post_meta_by_key('essb_post_share_text');
			delete_post_meta_by_key('essb_post_pin_image');
			delete_post_meta_by_key('essb_post_fb_url');
			delete_post_meta_by_key('essb_post_plusone_url');
			delete_post_meta_by_key('essb_post_twitter_hashtags');
			delete_post_meta_by_key('essb_post_twitter_username');
			delete_post_meta_by_key('essb_post_twitter_tweet');
			delete_post_meta_by_key('essb_activate_ga_campaign_tracking');
			delete_post_meta_by_key('essb_post_og_desc');
			delete_post_meta_by_key('essb_post_og_author');
			delete_post_meta_by_key('essb_post_og_title');
			delete_post_meta_by_key('essb_post_og_image');
			delete_post_meta_by_key('essb_post_og_video');
			delete_post_meta_by_key('essb_post_og_video_w');
			delete_post_meta_by_key('essb_post_og_video_h');
			delete_post_meta_by_key('essb_post_og_url');
			delete_post_meta_by_key('essb_post_twitter_desc');
			delete_post_meta_by_key('essb_post_twitter_title');
			delete_post_meta_by_key('essb_post_twitter_image');
			delete_post_meta_by_key('essb_post_google_desc');
			delete_post_meta_by_key('essb_post_google_title');
			delete_post_meta_by_key('essb_post_google_image');
			delete_post_meta_by_key('essb_activate_sharerecovery');
			delete_post_meta_by_key('essb_post_og_image1');
			delete_post_meta_by_key('essb_post_og_image2');
			delete_post_meta_by_key('essb_post_og_image3');
			delete_post_meta_by_key('essb_post_og_image4');
			
			// Adding remove command for legacy social metrics lite data from versions 3.x, 2.x
			delete_post_meta_by_key('esml_socialcount_LAST_UPDATED');
			delete_post_meta_by_key('esml_socialcount_TOTAL');
			delete_post_meta_by_key('esml_socialcount_facebook');
			delete_post_meta_by_key('esml_socialcount_twitter');
			delete_post_meta_by_key('esml_socialcount_googleplus');
			delete_post_meta_by_key('esml_socialcount_linkedin');
			delete_post_meta_by_key('esml_socialcount_pinterest');
			delete_post_meta_by_key('esml_socialcount_diggs');
			delete_post_meta_by_key('esml_socialcount_delicious');
			delete_post_meta_by_key('esml_socialcount_facebook_comments');
			delete_post_meta_by_key('esml_socialcount_stumbleupon');
			
			// removing plugin saved possible options
			delete_option('essb3_addons');
			delete_option('essb3_addons_announce');
			delete_option(ESSB3_OPTIONS_NAME);
			delete_option('essb_dismissed_notices');
			
			delete_option(ESSB3_OPTIONS_NAME_FANSCOUNTER);
			delete_option(ESSB3_FIRST_TIME_NAME);
			delete_option('essb-shortcodes');
			delete_option('essb-hook');
			delete_option('essb3-translate-notice');
			delete_option('essb3-subscribe-notice');
			delete_option(ESSB3_EASYMODE_NAME);
			delete_option(ESSB5_SETTINGS_ROLLBACK);
			delete_option('essb-admin-settings-token');
			delete_option('essb_cache_static_cache_ver');
			delete_option('essb4-activation');
			delete_option('essb4-latest-version');
			delete_option('essb-conversions-lite');
			delete_option('essb-subscribe-conversions-lite');
			delete_option('essbfcounter_cached');
			delete_option('essbfcounter_expire');
			delete_option(ESSB3_MAIL_SALT);
			
			global $wpdb;
			$table  = $wpdb->prefix . ESSB3_TRACKER_TABLE;
			$wpdb->query( "DROP TABLE IF EXISTS ".$table );
		}
		
		/**
		 * 8. Custom form designs
		 */
		if ($function == 'removeforms') {		
			delete_option('essb_options_forms');
		}
		
		/**
		 * 9. Love This
		 */
		if ($function == 'removelove') {
			delete_post_meta_by_key('essb_c_love');
			delete_post_meta_by_key('essb_pc_love');
			delete_post_meta_by_key('_essb_love');
		}
		
		/**
		 * 10. Instagram Transients
		 */
		if ($function == 'instagramtransients') {
		    global $wpdb;
		    
		    $ig_data = $wpdb->get_col( "SELECT option_name FROM $wpdb->options where (option_name LIKE '_transient_timeout_essb-u-%') OR (option_name LIKE '_transient_essb-u-%') OR (option_name LIKE '_transient_timeout_essb-h-%') OR (option_name LIKE '_transient_essb-h-%')" );
		    
		    if (!empty($ig_data)) {
		        foreach( $ig_data as $transient ) {
		            
		            $name = str_replace( '_transient_timeout_', '', $transient );
		            $name = str_replace( '_transient_', '', $transient );
		            delete_transient( $name );		            
		        }	
		    }
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

if (!function_exists('essb_advancedopts_section_open')) {
	function essb_advancedopts_section_open($section = '') {
		printf('<div class="advancedopt-section %s">', esc_attr($section));
	}
	
	function essb_advancedopts_section_close() {
		echo '</div>';
	}
	
}
