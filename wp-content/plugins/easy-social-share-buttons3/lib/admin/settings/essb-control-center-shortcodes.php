<?php
if (!class_exists('ESSBControlCenterShortcodes')) {

	class ESSBControlCenterShortcodes {
		/**
		 * Prevent multiple shortcode initialization
		 */
		private static $initialized = false;
		
		/**
		 * List of all plugin supported shortcodes
		 */
		public static $shortcodes = array();
		
		/**
		 * List of all options for supported shortcodes
		 */
		public static $shortcode_options = array();
		
		/**
		 * Register shortcodes that can store settings
		 */
		public static $shortcode_save_supported = array();
		
		/**
	     * The option where plugin will store saved shortcodes (only when admin generator from
	     * plugin settings is used)
		 */
		private static $shortcode_cache_option = 'essb-shortcodes';
		
		/**
		 * All stored for now shortcodes
		 */
		public static $saved_codes = array();
		
		/**
		 * Loading saved shortcodes
		 */
		public static function load_saved_shortcodes() {
			self::$saved_codes = get_option(self::$shortcode_cache_option);
			if (!is_array(self::$saved_codes)) {
				self::$saved_codes = array();
			}
		}
		
		public static function get_saved_shortcodes() {
			self::load_saved_shortcodes();
			
			return self::$saved_codes;
		}
		
		/**
		 * Remove a stored shortcode in the options table
		 * 
		 * @param unknown_type $key
		 */
		public static function remove_shortcode($key = '') {
			/**
			 * Loading again the saved codes to ensure that everything is up to date
			 */
			self::load_saved_shortcodes();
			
			if (isset(self::$saved_codes[$key])) {
				unset (self::$saved_codes[$key]);
			}
			
			delete_option(self::$shortcode_cache_option);
			update_option(self::$shortcode_cache_option, self::$saved_codes, 'no', 'no');
		}
		
		/**
		 * Save a generated shortcode
		 * 
		 * @param unknown_type $shortcode
		 * @param unknown_type $options
		 * @param unknown_type $name
		 * @param unknown_type $existing_key
		 */
		public static function save_shortcode($shortcode, $options = array(), $name = '', $existing_key = '') {
			/**
			 * Loading again the saved codes to ensure that everything is up to date
			 */
			self::load_saved_shortcodes();
			
			$key = $existing_key != '' ? $existing_key : time();
			
			self::$saved_codes[$key] = array(
					'name' => $name,
					'settings' => $options,
					'shortcode' => $shortcode
			);
			
			delete_option(self::$shortcode_cache_option);
			update_option(self::$shortcode_cache_option, self::$saved_codes, 'no', 'no');
			
			return $key;
		}
		
		/**
		 * Register single shortcode in the list
		 * 
		 * @param unknown_type $shortcode
		 * @param unknown_type $title
		 * @param unknown_type $description
		 * @param unknown_type $can_save
		 */
		public static function register_shortcode($shortcode, $icon = '', $title = '', $description = '', $can_save = false) {
			self::$shortcodes[$shortcode] = array(
					'title' => $title,
					'description' => $description,
					'icon' => $icon
			);
			
			self::$shortcode_options[$shortcode] = array();
			self::$shortcode_save_supported[$shortcode] = $can_save;
		}
		
		/**
		 * Register single shortcode option
		 * 
		 * @param unknown_type $shortcode
		 * @param unknown_type $param
		 * @param unknown_type $type
		 * @param unknown_type $title
		 * @param unknown_type $description
		 * @param unknown_type $options
		 * @param unknown_type $default
		 */
		public static function register_shortcode_option($shortcode, $param, $type, $title = '', $description = '', $options = array(), $default = '') {
			self::$shortcode_options[$shortcode][$param] = array(
					'title' => $title,
					'description' => $description,
					'type' => $type,
					'options' => $options,
					'default_value' => $default
			);
		}
		
		/**
		 * Register shortcode options from an object
		 * 
		 * @param unknown_type $shortcode
		 * @param unknown_type $options
		 */
		public static function register_shortcode_options_feed($shortcode, $options = array()) {
			foreach ($options as $key => $settings) {
				if (!isset($settings['title'])) {
					$settings['title'] = '';
				}
				if (!isset($settings['description'])) {
					$settings['description'] = '';
				}
				if (!isset($settings['options'])) {
					$settings['options'] = array();
				}
				if (!isset($settings['default_value'])) {
					$settings['default_value'] = '';
				}
				
				self::register_shortcode_option($shortcode, $key, $settings['type'], $settings['title'], $settings['description'], $settings['options'], $settings['default_value']);
			}
		}
		
		/**
		 * Return registered shortcode settings
		 * @param unknown_type $shortcode
		 */
		public static function get_shortcode_options($shortcode = '') {
			$r = array();

			if (isset(self::$shortcode_options[$shortcode])) {
				$r = self::$shortcode_options[$shortcode];
			}
			
			return $r;
		}
		
		/**
		 * Register all supported by plugin shortcodes in the list with options
		 */
		public static function add_plugin_shortcodes() {
			
			/***
			 * Add shortcodes function is already called. Do not call it again to
			 * prevent multiple shortcodes assigned at same time
			 */
			if (self::$initialized) {
				return;
			}
			
			/**
			 * Sharing shortcodes
			 */
			essb_depend_load_function('essb_get_shortcode_options_share_codes_loader', 'lib/admin/settings/shortcode-options/share-shortcodes.php');

			self::register_shortcode('social-share', 'ti-sharethis', esc_html__('Simple social share buttons', 'essb'), '', true);
			self::register_shortcode_options_feed('social-share', essb_get_shortcode_options_social_share());				

			self::register_shortcode('easy-social-share', 'ti-sharethis', esc_html__('Advanced social share buttons', 'essb'), '', true);
			self::register_shortcode_options_feed('easy-social-share', essb_get_shortcode_options_easy_social_share());				
			
			self::register_shortcode('easy-total-shares', 'ti-sharethis', esc_html__('Show total number of shares', 'essb'), '', true);
			self::register_shortcode_options_feed('easy-total-shares', essb_get_shortcode_options_easy_total_shares());				
			
			self::register_shortcode('share-action-button', 'ti-sharethis', esc_html__('Inline share start CTA button', 'essb'), '', false);
			self::register_shortcode_options_feed('share-action-button', essb_get_shortcode_options_share_action_button());				
			
			/**
			 * Shortcode for the custom positions display (when the module is running inside settings)
			 */
			if (!essb_option_bool_value('deactivate_custompositions')) {
				self::register_shortcode('social-share-display', 'ti-layout', esc_html__('Show custom postition/display', 'essb'), '', false);
				self::register_shortcode_options_feed('social-share-display', essb_get_shortcode_options_social_share_display());
			}
			
			self::register_shortcode('easy-social-share-popup', 'ti-sharethis', esc_html__('Show share buttons as pop-up', 'essb'), '', true);
			self::register_shortcode_options_feed('easy-social-share-popup', essb_get_shortcode_options_easy_social_share_popup());
				
			self::register_shortcode('easy-popular-posts', 'ti-view-list', esc_html__('Show top posts by social shares', 'essb'), '', true);
			self::register_shortcode_options_feed('easy-popular-posts', essb_get_shortcode_options_easy_popular_posts());
				
			/** 
			 * Social Followers Counter
			 */
			if (!essb_option_bool_value('deactivate_module_followers')) {
				essb_depend_load_function('essb_get_shortcode_options_easy_followers', 'lib/admin/settings/shortcode-options/easy-followers.php');
				self::register_shortcode('easy-followers', 'ti-heart', esc_html__('Show social followers counter block', 'essb'), '', true);
				self::register_shortcode_options_feed('easy-followers', essb_get_shortcode_options_easy_followers());

				self::register_shortcode('easy-followers-layout', 'ti-heart', esc_html__('Show social followers custom layout', 'essb'), '', true);
				self::register_shortcode_options_feed('easy-followers-layout', essb_get_shortcode_options_easy_followers(true));				
				
				self::register_shortcode('easy-total-followers', 'ti-heart', esc_html__('Show the total number of followers', 'essb'), '', false);
			}
			
			/**
			 * Subscribe Forms
			 */
			if (!essb_option_bool_value('deactivate_module_subscribe')) {
				essb_depend_load_function('essb_get_shortcode_options_easy_subscribe', 'lib/admin/settings/shortcode-options/easy-subscribe.php');
				self::register_shortcode('easy-subscribe', 'ti-email', esc_html__('Add subscribe to mailing list form', 'essb'), '', true);
				self::register_shortcode_options_feed('easy-subscribe', essb_get_shortcode_options_easy_subscribe());
					
			}
			
			/**
			 * Profile links
			 */
			if (!essb_option_bool_value('deactivate_module_profiles')) {
				essb_depend_load_function('essb_get_shortcode_options_easy_profiles', 'lib/admin/settings/shortcode-options/easy-profiles.php');
				self::register_shortcode('easy-profiles', 'ti-share', esc_html__('Add social profile links', 'essb'), '', true);
				self::register_shortcode_options_feed('easy-profiles', essb_get_shortcode_options_easy_profiles());
					
				self::register_shortcode('profile-bar', 'ti-share', esc_html__('Add social profiles bar (require pre-setup from the profiles menu)', 'essb'), '');
				self::register_shortcode_options_feed('profile-bar', essb_get_shortcode_options_profile_bar());
				
			}
			
			/**
			 * Click 2 Chat
			 */
			if (!essb_option_bool_value('deactivate_module_clicktochat')) {
				essb_depend_load_function('essb_get_shortcode_options_easy_click2chat', 'lib/admin/settings/shortcode-options/easy-click2chat.php');
				self::register_shortcode('easy-click2chat', 'ti-comments', esc_html__('Add inline button starting a click to chat window (example chat with us)', 'essb'), '', false);
				self::register_shortcode_options_feed('easy-click2chat', essb_get_shortcode_options_easy_click2chat());
				
			}

			/** 
			 * Sharable Quotes (Click 2 Tweet)
			 */
			if (!essb_option_bool_value('deactivate_ctt')) {
				essb_depend_load_function('essb_get_shortcode_options_sharable_quote', 'lib/admin/settings/shortcode-options/sharable-quote.php');
				self::register_shortcode('sharable-quote', 'ti-twitter', esc_html__('Add a click to tweet quote', 'essb'), '');
				self::register_shortcode_options_feed('sharable-quote', essb_get_shortcode_options_sharable_quote());
				
			}
						
			/**
			 * Pinterest Pro
			 */
			if (!essb_option_bool_value('deactivate_module_pinterestpro')) {
				essb_depend_load_function('essb_get_shortcode_options_pinterest_image', 'lib/admin/settings/shortcode-options/pinterest-image.php');
				self::register_shortcode('pinterest-image', 'ti-pinterest', esc_html__('Add image with Pin button and custom share quote', 'essb'), '');
				self::register_shortcode_options_feed('pinterest-image', essb_get_shortcode_options_pinterest_image());
				self::register_shortcode('pinterest-gallery', 'ti-pinterest', esc_html__('Add image gallery with Pin button', 'essb'), '');
				self::register_shortcode_options_feed('pinterest-gallery', essb_get_shortcode_options_pinterest_gallery());
			}
			
			/**
			 * Instagram Images & Feed
			 */
			if (!essb_option_bool_value('deactivate_module_instagram')) {
				essb_depend_load_function('essb_get_shortcode_options_instagram_feed', 'lib/admin/settings/shortcode-options/instagram-feed.php');
				self::register_shortcode('instagram-feed', 'ti-instagram', esc_html__('Add Instagram feed for user or hashtag', 'essb'), '');
				self::register_shortcode_options_feed('instagram-feed', essb_get_shortcode_options_instagram_feed());
			}
			
			/**
			 * Native buttons shortocde
			 */
			if (!essb_option_bool_value('deactivate_module_natives')) {
				essb_depend_load_function('essb_get_shortcode_options_easy_social_like', 'lib/admin/settings/shortcode-options/easy-social-like.php');
				self::register_shortcode('easy-social-like', 'ti-thumb-up', esc_html__('Add native buttons', 'essb'), '', true);
				self::register_shortcode_options_feed('easy-social-like', essb_get_shortcode_options_easy_social_like());
			}
			
			self::$initialized = true;
		}
		
		/***
		 * --------------------------------------------------------------------
		 * Shortcode screen draw
		 * --------------------------------------------------------------------
		 */
		
		public static function generate_screen($settings_screen = true) {
			$code = '';
			
			$code .= '<div class="essb-shortcode-overlay">';
			
			$code .= '<div class="essb-shortcode-screen">';
			
			$code .= '<div class="heading">';
			$code .= '<div class="essb-logo essb-logo32"></div>';
			$code .= '<span class="title">'.esc_html__('Generate Shortcode', 'essb').'</span>';
			$code .= '<span class="generate"><i class="fa fa-code"></i><label>'.esc_html__('Generate', 'essb').'</label></span>';
			$code .= '<span class="generated-list"><i class="fa fa-list"></i><label>'.esc_html__('Saved', 'essb').'</label></span>';
			$code .= '<span class="generate-options" data-url="'.esc_url(admin_url('admin.php?page=essb_redirect_shortcode&tab=shortcode')).'"><i class="fa fa-th-list"></i><label>'.esc_html__('Options', 'essb').'</label></span>';
			$code .= '<span class="close"><i class="fa fa-times"></i></span>';
			$code .= '</div>'; // heading
			
			
			$code .= '<div class="window-content">';
			$code .= '<div class="navigation">';
			
			foreach (self::$shortcodes as $shortcode => $details) {
				$code .= '<a href="#" data-code="'.esc_attr($shortcode).'" class="essb-sc-menu-'.esc_attr($shortcode).'">';
				$code .= '<div class="icon">';
				$code .= '<i class="'.esc_attr($details['icon']).'"></i>';
				$code .= '</div>';
				$code .= '<div class="desc">';
				$code .= '<span class="sc">';
				$code .= $shortcode;
				$code .= '</span>';
				$code .= '<span class="title">';
				$code .= $details['title'];
				$code .= '</span>';
				$code .= '</div>';
				$code .= '</a>';
			}
			
			$code .= '</div>'; // navigation
			
			$code .= '<div class="content">';
			
			$code .= '<div class="shortcode-generated">';
			$code .= '<h5>'.esc_html__('Your shortcode', 'essb').'</h5>';
			$code .= '<div class="shortcode-result"></div>';
			
			if ($settings_screen) {
				$code .= '<h5>'.esc_html__('How to include the shortcode inside theme files', 'essb').'</h5>';
				$code .= '<div class="shortcode-embed-result"></div>';
			}
			
			$code .= '</div>';
			
			$code .= '<div class="shortcode-options shortcode-list active" data-code="list">';
			$code .= self::generate_stored_shortcodes($settings_screen);
			$code .= '</div>';
			
			// Generating setting contents for each shortcode 
			foreach (self::$shortcodes as $shortcode => $details) {
				$code .= self::generate_shortcode_options($shortcode, $settings_screen);
			}
			
			$code .= '</div>';
			
			$code .= '</div>'; // window-content
			
			$code .= '</div>'; //shortcode-screen
			
			$code .= '</div>'; // shortcode overlay screen
			
			return $code;
		}
		
		public static function generate_stored_shortcodes($settings_screen = true) {
			self::load_saved_shortcodes();
			
			$code = '';
			$has_code = false;

			foreach (self::$saved_codes as $key => $data) {
				$shortcode = $data['shortcode'];
				$details = isset(self::$shortcodes[$shortcode]) ? self::$shortcodes[$shortcode] : array();
				
				$code .= '<div class="essb-stored-sc">';
				$code .= '<div class="header">';
				if (isset($details['icon'])) {
					$code .= '<div class="icon">';
					$code .= '<i class="'.esc_attr($details['icon']).'"></i>';
					$code .= '</div>';
				}
				$code .= '<div class="title">'.esc_html($data['name']).'</div>';
				$code .= '<div class="actions">';
				if ($settings_screen) {
					$code .= '<span class="edit" data-ukey="'.esc_attr($key).'" onclick="essbShortcodeGeneratorEdit(this); return false;"><i class="fa fa-pencil-square-o"></i>'.esc_html__('Edit', 'essb').'</span>';
					$code .= '<span class="remove" data-ukey="'.esc_attr($key).'" onclick="essbShortcodeGeneratorRemove(this); return false;"><i class="fa fa-times"></i>'.esc_html__('Remove', 'essb').'</span>';
				}
				$code .= '</div>';
				$code .= '</div>';
				$code .= '<div class="shortcode"><code contenteditable="true" onfocus="essbShortcodeFocusSelect(this);">[' .esc_html($data['shortcode']). ' ukey="'.esc_attr($key).'"]</code></div>';
				$code .= '</div>';
				
				$has_code = true;
			}
			
			if (!$has_code) {
				$code .= '<div class="message">';
				$code .= '<h3>'.esc_html__('You does not have any stored shortcodes', 'essb').'</h3>';
				$code .= '</div>';
			}
			
			return $code;
		}
		
		public static function generate_shortcode_options($shortcode = '', $settings_screen = true) {
			$code = '';
			
			$code .= '<div class="shortcode-options shortcode-'.esc_attr($shortcode).'" data-code="'.esc_attr($shortcode).'">';
			
			$details = self::$shortcodes[$shortcode];
			
			$code .= '<div class="heading-row">';
			$code .= '<h2>[' . $shortcode.']</h2>';
			$code .= '<h3>'.$details['title'].'</h3>';
			$code .= '</div>';
			
			/**
			 * The shortcode support save for re-use option
			 */
			if (self::$shortcode_save_supported[$shortcode] && $settings_screen) {
				$code .= '<div class="option-saverow">';
				$code .= '<div class="param-name">';
				$code .= '<div><strong><input type="checkbox" id="shortcode-store_'.esc_attr($shortcode).'" class="shortcode-store"/>'.esc_html__('Store my generated shortcode', 'essb').'</strong></div>';
				$code .= '<em>'.esc_html__('Optionally store your shortcode and use it multiple times on-site with the same setup. For all stored shortcodes you can make a change in the settings and it will reflect anywhere it is used. To store a shortcode you need to tick the box and enter a custom name (used for a reference).', 'essb').'</em>';
				$code .= '</div>';
				$code .= '<div class="value-field">';
				$code .= '<input type="text" id="shortcode-name_'.esc_attr($shortcode).'" class="widefat shortcode-name" />';
				$code .= '</div>';
				$code .= '</div>';
			}
			
			$default_options = self::$shortcode_options[$shortcode];
			$has_options = false;
			
			$salt = mt_rand();
			
			foreach ($default_options as $key => $setup) {
				$value = '';
				$type = $setup['type'];
				$title = isset($setup['title']) ? $setup['title'] : '';
				$description = isset($setup['description']) ? $setup['description'] : '';
				$options = isset($setup['options']) ? $setup['options'] : array();
				$value = isset($setup['default_value']) ? $setup['default_value'] : '';
				
				$has_options = true;
				
				if ($type == 'section-open') {
					$code .= '<div class="sc-inner-section '.esc_attr($title).'">';
				}
				
				if ($type == 'section-close') {
					$code .= '</div>';
				}
				
				if ($type == 'separator') {
					$code .= '<div class="option-row seperator-row">';
					$code .= '<strong>'.$title.'</strong>';
					$code .= '</div>';
				}
				
				if ($type == 'separator-small') {
					$code .= '<div class="option-row seperator-small-row">';
					$code .= '<strong>'.$title.'</strong>';
					$code .= '</div>';
				}
				
				if ($type == 'checkbox') {
					$type = 'select';
					$options = array('no' => esc_html__('No', 'essb'), 'yes' => esc_html__('Yes', 'essb'));
				}
				
				if ($type == 'checkbox-true') {
					$type = 'select';
					$options = array('false' => esc_html__('No', 'essb'), 'true' => esc_html__('Yes', 'essb'));
				}
				
				if ($type == 'text') {
					
					if (!is_array($options)) {
						$options = array();
					}
					
					$size = isset($options['size']) ? $options['size'] : '';
					
					$code .= '<div class="option-row">';
					
					$code .= '<div class="param">';
					$code .= '<label for="generated_shortcode_'.esc_attr($shortcode.$key).'"><strong>'.$title.'</strong></label>';
					if ($description != '') {
						$code .= '<em>'.$description.'</em>';
					}
					$code .= '</div>';
					
					$code .= '<div class="value">';
					$code .= '<input class="widefat'.($size != '' ? ' size-' . $size : '').'" id="generated_shortcode_'.esc_attr($shortcode.$key).'" name="generated_shortcode_'.esc_attr($shortcode.$key).'" type="text" value="'.esc_attr($value).'" data-param="'.esc_attr($key).'" />';
					$code .= '</div>';
			
					$code .= '</div>';
				}
				
				if ($type == 'textarea') {
					$code .= '<div class="option-row">';
						
					$code .= '<div class="param">';
					$code .= '<label for="generated_shortcode_'.esc_attr($shortcode.$key).'"><strong>'.$title.'</strong></label>';
					if ($description != '') {
						$code .= '<em>'.$description.'</em>';
					}
					$code .= '</div>';
						
					$code .= '<div class="value">';
					$code .= '<textarea class="widefat" id="generated_shortcode_'.esc_attr($shortcode.$key).'" name="generated_shortcode_'.esc_attr($shortcode.$key).'" data-param="'.esc_attr($key).'" rows="4">'.$value.'</textarea>';
					$code .= '</div>';
						
					$code .= '</div>';
				}
			
				if ($type == 'select') {
					$code .= '<div class="option-row">';
					
					$code .= '<div class="param">';
					$code .= '<label for="generated_shortcode_'.esc_attr($shortcode.$key).'"><strong>'.$title.'</strong></label>';
					if ($description != '') {
						$code .= '<em>'.$description.'</em>';
					}
					$code .= '</div>';
					
					$code .= '<div class="value">';
					$code .= '<select class="widefat" id="generated_shortcode_'.esc_attr($shortcode.$key).'" name="generated_shortcode_'.esc_attr($shortcode.$key).'" value="'.esc_attr($value).'" data-param="'.esc_attr($key).'" >';
					foreach ($options as $opt_key => $opt_value) {
						$code .= '<option value="'.$opt_key.'" '.($opt_key == $value ? 'selected': '').'>'.$opt_value.'</option>';
					}
					$code .= '</select>';
					$code .= '</div>';
			
					$code .= '</div>';
				}
				
				if ($type == 'networks') {
					$code .= '<div class="option-row">';
						
					$code .= '<div class="param">';
					$code .= '<label for="generated_shortcode_'.esc_attr($shortcode.$key).'"><strong>'.$title.'</strong></label>';
					if ($description != '') {
						$code .= '<em>'.$description.'</em>';
					}
					$code .= '</div>';
						
					$code .= '<div class="value">';
					$code .= '<ul id="generated_shortcode_'.esc_attr($shortcode.$key).'" class="essb-sc-networks essb-sc-sortable" data-param="'.$key.'">';
					
					foreach ($options as $opt_key => $opt_value) {
						$code .= '<li>';
						$code .= '<input type="checkbox" name="'.$key.'_'.$opt_key.'_'.$salt.'" id="'.$key.'_'.$opt_key.'_'.$salt.'" value="'.$opt_key.'" class="sc-network-select"/>';
						$code .= '<label for="'.$key.'_'.$opt_key.'_'.$salt.'">'.$opt_value.'</label>';
						$code .= '</li>';
					}
					
					$code .= '</ul>';
					$code .= '</div>';
						
					$code .= '</div>';
				}
			}
			
			if (!$has_options) {
				$code .= '<div class="option-saverow">';
				$code .= '<div class="param-name">';
				$code .= esc_html__('This shortcode does not have any options. You can press generate directly or copy it to code.', 'essb');
				$code .= '</div>';
				$code .= '</div>';
			}
			
			$code .= '</div>';
			
			return $code;
		}
		
		/**
		 * Generate the shortcode options screen
		 * @param unknown_type $settings_page
		 */
		public static function draw_screen($settings_page = true) {
			echo self::generate_screen($settings_page);
		}
	}
	
}