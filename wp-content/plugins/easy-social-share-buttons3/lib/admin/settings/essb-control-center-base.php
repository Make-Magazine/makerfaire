<?php
if (!class_exists('ESSBControlCenter')) {
	class ESSBControlCenter {
	    
	    /**
	     * New version is running
	     * @var string
	     * @since 8.0
	     */
	    private static $new_version = false;
	    
	    /**
	     * Defines a global block to hold all sections that are not distributed
	     * @since 8.0
	     */
	    private static $global_block = '';
	    
	    /**
	     * Icon based blocks for accessing the navigation
	     * @since 8.0
	     */
	    public static $sidebar_blocks = array();
	    
		/**
		 * Menu sidebar sections available
		 */
		public static $sidebar_sections = array();

		/**
		 * All menu items inside a sidebar
		 */
		public static $menu_items = array();
		
		/**
		 * All internal page sub-sections (sub-menus)
		 */
		public static $sub_sections = array();
		
		/**
		 * Hold the existing active section inside the navigation
		 */
		public static $active_section = '';
		
		/**
		 * Hold a list of all plugin features and check if one is running or not
		 */
		public static $feature_list = array();
		
		/**
		 * Hold the list of existing feature groups (for direct access to all)
		 */
		public static $features_group = array();
		
		/**
		 * Activate the new version of the settings screen
		 */
		public static function set_new_version() {
		    self::$new_version = true;
		}
		
		/**
		 * Check if the new version is enabled
		 * 
		 * @return boolean
		 */
		public static function is_new_version() {
		    return self::$new_version;
		}
		
	    public static function deprecate_blocks_in_new_version() {
	        if (self::$new_version) {
	            $blocks = array('othersharing', 'othersocial');
	            
	            foreach ($blocks as $block_id) {
	                if (isset(self::$sidebar_sections[$block_id])) {
	                    unset (self::$sidebar_sections[$block_id]);
	                }
	            }
	        }
	    }
		
		/**
		 * ---------------------------------------------------------
		 * Register the features list
		 * ---------------------------------------------------------
		 */
		
		/**
		 * Register a group of features
		 * 
		 * @param string $id
		 * @param array $features IDs of already registered features in the list 
		 */
		public static function register_feature_group($id, $features = array()) {
			self::$features_group[$id] = $features;
		}
		
		/**
		 * Check if all
		 * @param unknown_type $id
		 * @return boolean
		 */
		public static function feature_group_is_running($id) {
			return self::all_features_are_running(self::$features_group[$id]);
		}
		
		public static function feature_group_is_deactivated($id) {
			return self::all_features_are_deactivated(self::$features_group[$id]);
		}
		
		public static function feature_group_has_running($id) {
			return self::one_feature_is_running(self::$features_group[$id]);
		}
		
		public static function feature_group_is_active($id) {
			$r = false;
			
			foreach (self::$features_group[$id] as $single_id) {
				if (self::feature_is_deactivated($single_id)) {
					$r = true;
				}
			}
			
			return $r;
		}
		
		public static function feature_group_has_deactivated($id) {
			$r = false;
				
			foreach (self::$features_group[$id] as $single_id) {
				if (self::feature_is_deactivated($single_id)) {
					$r = true;
				}
			}
				
			return $r;
		}
		
		/**
		 * Register a single feature inside the list
		 * 
		 * @param string $id
		 * @param string $running_option
		 * @param string $deactivate_option
		 * @param string $activate_option
		 */
		public static function register_feature($id, $running_option = '', $deactivate_option = '', $activate_option = '') {
			self::$feature_list[$id] = array('running' => $running_option, 'deactivate' => $deactivate_option, 'activate' => $activate_option);
		}
		
		public static function register_feature_details($id, $title, $description, $icon, $long_description = '') {
			if (isset(self::$feature_list[$id])) {
				self::$feature_list[$id]['title'] = $title;
				self::$feature_list[$id]['description'] = $description;
				self::$feature_list[$id]['icon'] = $icon;
				self::$feature_list[$id]['long_desc'] = $long_description;
			}
		}
		
		public static function get_feature_title($id) {
			return isset(self::$feature_list[$id]) && isset(self::$feature_list[$id]['title']) ? self::$feature_list[$id]['title'] : '';
		}
		
		public static function get_feature_deactivate_option($id) {
			return isset(self::$feature_list[$id]) && isset(self::$feature_list[$id]['deactivate']) ? self::$feature_list[$id]['deactivate'] : '';
		}

		public static function get_feature_description($id) {
			return isset(self::$feature_list[$id]) && isset(self::$feature_list[$id]['description']) ? self::$feature_list[$id]['description'] : '';
		}
		
		public static function get_feature_icon($id) {
			return isset(self::$feature_list[$id]) && isset(self::$feature_list[$id]['icon']) ? self::$feature_list[$id]['icon'] : '';
		}
		
		public static function get_feature_long_description($id) {	
			$r = '';
			if (isset(self::$feature_list[$id])) {
				if (isset(self::$feature_list[$id]['long_desc'])) {
					$r = self::$feature_list[$id]['long_desc'];
				}
				
				if ($r == '' && isset(self::$feature_list[$id]['description'])) {
					$r = self::$feature_list[$id]['description'];
				}
			}
			
			return $r;
		}
		
		
		/**
		 * Check if a feature is currently running
		 * 
		 * @param string $id
		 * @return boolean
		 */
		public static function feature_is_running($id) {
			$r = false;
			$active = true;
			
			if (self::$feature_list[$id]['deactivate'] != '') {
				$active = !essb_option_bool_value(self::$feature_list[$id]['deactivate']);
			}
			
			if ($active && self::$feature_list[$id]['running'] != '') {
				$r = essb_option_bool_value(self::$feature_list[$id]['running']);
			}
			else if ($active) {
				$r = true;
			}
			
			return $r;
		}
		
		/**
		 * Check if a feature is deactivated
		 * 
		 * @param string $id
		 * @return boolean
		 */
		public static function feature_is_deactivated($id) {
			$r = false;
			
			if (self::$feature_list[$id]['deactivate'] != '') {				
				$r = essb_option_bool_value(self::$feature_list[$id]['deactivate']);
			}
			
			return $r;
		}
		
		public static function all_features_are_deactivated($ids = array()) {
			$r = true;
				
			foreach ($ids as $id) {				
				if (!self::feature_is_deactivated($id)) {
					$r = false;
				}
			}
				
			return $r;
		}
		
		/**
		 * Check if all features from a list are running
		 * 
		 * @param array $ids
		 * @return boolean
		 */
		public static function all_features_are_running($ids = array()) {
			$r = true;
			
			foreach ($ids as $id) {
				if (!self::feature_is_running($id)) {
					$r = false;
				}
			}
			
			return $r;
		} 
		
		/**
		 * Check if a single feature from a list is running
		 * 
		 * @param array $ids
		 * @return boolean
		 */
		public static function one_feature_is_running($ids = array()) {
			$r = false;
			
			foreach ($ids as $id) {
				if (self::feature_is_running($id)) {
					$r = true;
				}
			}
				
			return $r;
		}
		
		/**
		 * Check if there is a sidebar section registered for the plugin
		 *
		 * @param array $ids
		 * @return boolean
		 */
		public static function one_section_is_registered ($ids = array()) {
		    $r = false;
		    
		    foreach ($ids as $id) {
		        if (isset(self::$sidebar_sections[$id])) {
		            $r = true;
		        }
		    }
		    
		    return $r;
		}
		
		/**
		 * ---------------------------------------------------------
		 * Register navigation and options section inside plugin
		 * ---------------------------------------------------------
		 */
		
		/**
		 * Register existing active section to use it inside plugin
		 * @param string $section_id
		 */
		public static function set_active_section($section_id = '') {
			self::$active_section = $section_id;
		}
		
		
		public static function register_sidebar_block($block_id, $icon = '', $title = '', $sections = array()) {
		    self::$sidebar_blocks[$block_id] = array(
		        'icon' => $icon,
		        'title' => $title,
		        'sections' => $sections
		    );
		}
		
		/**
		 * Append a new section to an existing sidebar block
		 *
		 * @param unknown $block_id
		 * @param unknown $section
		 */
		public static function append_to_sidebar_block ($block_id, $section) {
		    if (isset(self::$sidebar_blocks[$block_id]) && !empty($section)) {
		        self::$sidebar_blocks[$block_id]['sections'][] = $section;
		    }
		}
		
		/**
		 * 
		 * @param string $block_id
		 * @since 8.0
		 */
		public static function set_global_block($block_id = '') {
		    if (isset(self::$sidebar_blocks[$block_id])) {
		        self::$global_block = $block_id;
		    }
		}
		
		/**
		 * 
		 * @param string $section_id
		 * @return boolean
		 */
		public static function exist_in_block($section_id = '') {
		    $r = false;
		    
		    foreach (self::$sidebar_blocks as $block_id => $options) {
		        if (!isset($options['sections'])) {
		            continue;
		        }
		        
		        if (in_array($section_id, $options['sections'])) {
		            $r = true;
		        }
		    }
		    
		    return $r;
		}
		
		/**
		 * Distribute the not assigned sections into a global block (usually extensions)
		 */
		public static function distribute_unassigned_sections() {
		    if (self::$global_block != '') {
		        foreach (self::$sidebar_sections as $section_id => $options) {
		            if (!self::exist_in_block($section_id)) {
		                self::append_to_sidebar_block(self::$global_block, $section_id);
		            }
		        }
		    }
		}

		/**
		 * Register a new sidebar navigation item (menu item)
		 *
		 * @param string $section_id
		 * @param string $section_name
		 * @param string $section_title
		 * @param string $icon
		 * @param boolean $hide_update Don't display the update settings button
		 * @param boolean $hide_menu Don't display inside the plugin settings menu
		 * @param boolean $hide_sidebar Don't display navigation sidebar
		 */
		public static function register_sidebar_section($section_id, $section_name, $section_title, $icon = '', $hide_update = false, 
				$hide_menu = false, $hide_sidebar = false, $hide_topbar = false, $hide_settings_menu_only = false) {
			
			if ($section_title || empty($section_title)) {
				$section_title = $section_name;
			}
			
			self::$sidebar_sections[$section_id] = array(
					'name' => $section_name,
					'title' => $section_title,
					'icon' => $icon,
					'hide_update' => $hide_update,
					'hide_menu' => $hide_menu,
					'hide_sidebar' => $hide_sidebar,
					'hide_topbar' => $hide_topbar,
					'hide_settings_menu' => $hide_settings_menu_only,
					'type' => 'menu'
			);
		}

		/**
		 * Register a sidebar heading for grouping menu items
		 *
		 * @param string $section_heading_id
		 * @param string $section_heading_name
		 * @param string $icon
		 */
		public static function register_sidebar_heading($section_heading_id, $section_heading_name, $icon = '') {
			self::$sidebar_sections[$section_heading_id] = array(
					'name' => $section_heading_name,
					'title' => $section_heading_name,
					'icon' => $icon,
					'hide_update' => true,
					'hide_menu' => true,
					'hide_sidebar' => true,
					'type' => 'split'
			);
		}

		/**
		 * Register a new submenu to a section (for sections that has multiple submenus inside it)
		 *
		 * @param string $section_id
		 * @param string $menu_id
		 * @param string $title
		 */
		public static function register_sidebar_section_menu($section_id, $menu_id, $title) {
			if (!isset(self::$sidebar_sections[$section_id])){
				return;
			}
			
			if (!isset(self::$menu_items[$section_id])) {
				self::$menu_items[$section_id] = array();
			}
				
			self::$menu_items[$section_id][$menu_id] = array(
					'title' => $title,
					'childs' => false,
					'child_list' => array()
			);
		}
		
		/**
		 * 
		 * @param string $section_id
		 * @param string $menu_id
		 * @param string $sub_id
		 * @param array $options
		 */
		public static function register_sidebar_section_menu_sub($section_id, $menu_id, $sub_id, $options = array()) {
			self::$menu_items[$section_id][$menu_id]['childs'] = true;
			self::$menu_items[$section_id][$menu_id]['child_list'][] = $sub_id;
			self::$sub_sections[$sub_id] = $options;
		}

		/**
		 * Check if single sidebar section has a child menu
		 *
		 * @param string $section_id
		 */
		public static function section_has_menu($section_id) {
			return isset(self::$menu_items[$section_id]) ? true : false;
		}
		
		public static function section_menu_items_count($section_id) {
			$r = 0;
			
			foreach (self::$menu_items[$section_id] as $menu_id => $menu_options) {
				$r++;
			}
			
			return $r;
		}
		
		/**
		 * Check if current menu item has sub sections
		 * @param string $section_id
		 * @param string $menu_id
		 */
		public static function section_menu_has_sub($section_id, $menu_id) {
			return isset(self::$menu_items[$section_id][$menu_id]) ? self::$menu_items[$section_id][$menu_id]['childs'] : false;
		}
		
		/**
		 * Get the active subsection title
		 * 
		 * @param string $section_id
		 * @param string $menu_id
		 */
		public static function section_menu_sub_title($section_id, $menu_id) {
			return isset(self::$menu_items[$section_id][$menu_id]) ? self::$menu_items[$section_id][$menu_id]['title'] : '';
		}
		
		/**
		 * Check if section has sidebar to display
		 * 
		 * @param string $section_id
		 * @return boolean
		 */
		public static function section_without_sidebar($section_id) {
			return self::$sidebar_sections[$section_id]['hide_sidebar'] || false;
		}
		
		/**
		 * Check if the section should have save settings button added
		 * 
		 * @param string $section_id
		 * @return boolean
		 */
		public static function section_without_save($section_id) {
			return self::$sidebar_sections[$section_id]['hide_update'] || false;
		}
		
		/**
		 * Check if current running section needs to have a top bar displayed
		 * 
		 * @param string $section_id
		 */
		public static function section_without_topbar($section_id) {
			return self::$sidebar_sections[$section_id]['hide_topbar'] || false;
		}

		/**
		 * Create a menu item linl
		 * 
		 * @param string $section_id
		 * @param string $menu_id
		 * @return string
		 */
		public static function section_build_url($section_id = '', $menu_id = '') {
			$options_handler = $section_id == 'social' ? 'essb_options' : 'essb_redirect_'.$section_id;
			
			$url_base = 'admin.php?page=' . $options_handler . '&tab=' . $section_id;
			
			if ($menu_id != '') {
				$url_base .= '&section='.$menu_id;
			}
			
			return admin_url($url_base);
		}
		
		/**
		 * Generate help button URL
		 * 
		 * @return string
		 */
		public static function help_url() {
			return admin_url('admin.php?page=essb_redirect_about&tab=about&about_tab=help');
		}
		
		public static function getting_started_url() {
			return admin_url('admin.php?page=essb_options');
		}

		/**
		 * Count existing deactivated features from the list (and generate a total count)
		 */
		public static function features_count() {
			$deactivate_keys = array('deactivate_ansp', 'deactivate_ssr', 'deactivate_ctt', 'deactivate_module_aftershare', 'deactivate_module_shareoptimize',
					'deactivate_module_analytics', 'deactivate_module_pinterestpro', 'deactivate_module_shorturl',
					'deactivate_module_affiliate', 'deactivate_module_customshare', 'deactivate_module_message', 'deactivate_module_metrics',
					'deactivate_module_translate', 'deactivate_module_conversions', 'deactivate_custompositions',
					'deactivate_method_integrations', 'deactivate_settings_post_type', 'deactivate_module_followers',
					'deactivate_module_profiles', 'deactivate_module_subscribe', 'deactivate_module_facebookchat',
					'deactivate_module_skypechat', 'deactivate_module_clicktochat', 'deactivate_fakecounters', 
					'deactivate_module_instagram', 'deactivate_module_proofnotifications',
					'deactivate_stylelibrary', 'deactivate_custombuttons');
			
			$cnt_active = 0;
			foreach ($deactivate_keys as $feature) {
				if (!essb_option_bool_value($feature)) {
					$cnt_active++;
				}
			}
			
			return sprintf('%s/%s', $cnt_active, count($deactivate_keys));
		}
		
		/**
		 * Get list of internal page navigation sections (if such are already registered)
		 */
		public static function get_navigation_sections() {
			$r = array();
			
			foreach (self::$sidebar_sections as $section_id => $options) {
				$name = isset($options['name']) ? $options['name'] : '';
				$hide_menu = isset($options['hide_menu']) ? $options['hide_menu'] : false;
				$hide_settings_menu = isset($options['hide_settings_menu']) ? $options['hide_settings_menu'] : false;
				$type = isset($options['type']) ? $options['type'] : 'menu';
				
				if (!$hide_menu && $hide_settings_menu) { $hide_menu = true; }
				
				if ($type == 'menu' && $name != '') {
					$r[$section_id] = array('name' => $name, 'hide' => $hide_menu);
				}
			}
			
			return $r;
		}
		
		/**
		 * Get list of options for an internal page seciont
		 * 
		 * @param string $section_id
		 */
		public static function get_section_options($section_id = '') {
			global $essb_section_options;
			
			$options = isset($essb_section_options[$section_id]) ? $essb_section_options[$section_id] : array();
			
			return $options;
		}
		
		/**
		 * This function will convert the past legacy structure of settings to support sub section (internal)
		 * in a page of options. This will not affect the options that are in a signle menu
		 */
		public static function convert_legacy_options_structure() {
			global $essb_section_options;
			
			$r = array();
			
			foreach ($essb_section_options as $section_id => $section_data) {
				$r[$section_id] = array();
				
				foreach ($section_data as $menu_id => $controls) {
					$submenu_id = '';
					
					if (strpos($menu_id, '|') !== false) {
						$menu_obj = explode('|', $menu_id);
						$menu_id = $menu_obj[0];
						$submenu_id = $menu_obj[1];
					}
					
					if (!isset($r[$section_id][$menu_id])) {
						$r[$section_id][$menu_id] = array();
					}
					
					foreach ($controls as $control) {
						if ($submenu_id != '') {
							$control['submenu_id'] = $submenu_id;
						}
						
						$r[$section_id][$menu_id][] = $control;
					}
				}
			}
			
			$essb_section_options = $r;
		}
		
		public static function get_section_title($section_id = '') {
			return array(
				'title' => self::$sidebar_sections[$section_id]['title'],
				'icon' => self::$sidebar_sections[$section_id]['icon'],
			);
		}
		
		public static function translations() {
			$r = array();
			$r['setup_save'] = esc_html__('Your Settings Are Saved!', 'essb');
			$r['setup_save_desc'] = esc_html__('Your new setup is ready to use. If you use cache plugin (example: W3 Total Cache, WP Super Cache, WP Rocket) or optimization plugin (example: Autoptimize, BWP Minify) it is highly recommended to clear cache or you may not see the changes.', 'essb');
			
			$r['deactivate_action_save'] = essb_option_bool_value('deactivate_ajaxsubmit');
			$r['load_section'] = isset($_REQUEST['section']) ? esc_attr($_REQUEST['section']) : '';
			$r['load_subsection'] = isset($_REQUEST['subsection']) ? esc_attr($_REQUEST['subsection']) : '';
			
			return $r;
		}
		
		/**
		 * ---------------------------------------------------------
		 * Control center output
		 * ---------------------------------------------------------
		 */

		/**
		 * Output setup panel header
		 */
		public static function draw_header($custom = false, $group = '', $force_hide_menu = false) {
			global $_REQUEST;
			
			$active_section = isset($_REQUEST['section']) ? $_REQUEST['section'] : '';
			$active_subsection = isset($_REQUEST['subsection']) ? $_REQUEST['subsection'] : '';
			
			$admin_template = '';
			$hide_menu = self::section_without_sidebar(self::$active_section);
			if (!$hide_menu && $force_hide_menu) {
				$hide_menu = true;
			}
			
			echo '<form id="essb_options_form" enctype="multipart/form-data" method="post" action="">';
			if ($custom && !empty($group)) {
				echo '<input type="hidden" name="action" value="update"/>';
				echo '<input type="hidden" name="option_page" value="'.$group.'"/>';
				wp_nonce_field( 'essb_setup', 'essb_token' );
				echo '<input type="hidden" name="essb_salt" value="'.sanitize_text_field(essb_admin_setting_token()).'"/>';
			}
			else {
				echo '<input type="hidden" name="action" value="update"/>';
				echo '<input type="hidden" name="option_page" value="essb_settings_group"/>';
				wp_nonce_field( 'essb_setup', 'essb_token' );
				echo '<input type="hidden" name="essb_salt" value="'.sanitize_text_field(essb_admin_setting_token()).'"/>';
			}
			echo '<input id="section" name="section" type="hidden" value="'.sanitize_text_field($active_section).'"/>';
			echo '<input id="subsection" name="subsection" type="hidden" value="'.sanitize_text_field($active_subsection).'"/>';
			echo '<input id="tab" name="tab" type="hidden" value="'.sanitize_text_field(self::$active_section).'"/>';
			
			echo '<div class="essb-control'.($hide_menu ? ' essb-control-nosidebar': '').'">';
		}

		/**
		 * Output setup panel footer
		 */
		public static function draw_footer() {
			echo '</div>';
			echo '</form>';
			
			echo '<div id="essb-cc-preloader"><div id="essb-cc-loader"></div></div>';
		}

		/**
		 * Output setup panel navigation
		 */
		public static function draw_sidebar() {
		    
		    /**
		     * @since 8.0
		     */
		    if (self::$new_version) {
		        self::draw_sidebar_v8();
		        return '';
		    }
		    
			$plugin_title = 'ESSB';
			$plugin_title = apply_filters('essb_control_center_navigation_title', $plugin_title);
				
			echo '<!-- Setup Navigation -->';
			echo '<div class="essb-control-navigation">';
				
			// essb-header-logo
			echo '<div class="essb-header-logo">';
				
			echo '<div class="essb-header-logo-image"><div class="essb-logo essb-logo32"></div></div>';
			echo '<div class="essb-header-logo-title">'.esc_html($plugin_title).'<span class="version">'.ESSB3_VERSION.'</span></div>';
				
			echo '</div>'; // essb-header-logo
				
			// essb-primary-navigation
			echo '<ul class="essb-primary-navigation">';
				
			// Generating navigation based on registered sidebar sections
			foreach (self::$sidebar_sections as $section_id => $section_options) {
				$type = isset($section_options['type']) ? $section_options['type'] : '';
				$name = isset($section_options['name']) ? $section_options['name'] : '';
				
				if ($type == 'split') {
					echo '<li class="essb-navigation-split essb-cc-'.esc_attr($section_id).'">'.esc_html($name).'</li>';
				}
				else if ($type == 'menu') {
					$icon = isset($section_options['icon']) ? $section_options['icon'] : '';
					$hide_menu = isset($section_options['hide_menu']) ? $section_options['hide_menu'] : false;
					$is_active = $section_id == self::$active_section;
					
					/**
					 * Option is internal - it should be hidden inside the navigation
					 */
					if ($hide_menu) { 
						continue;
					}
						
					echo '<li class="essb-menu-item essb-cc-'.esc_attr($section_id).($is_active ? ' active' : '').'" data-menu="'.esc_attr($section_id).'">';
					echo '<a href="'.esc_url(self::section_build_url($section_id)).'">';
					if ($icon != '') {
						echo '<i class="'.esc_attr($icon).'"></i>';
					}
					echo '<span>'.esc_attr($name).'</span>';
					echo '</a>';
						
					// Generate the sidebar output if needed
					if (self::section_has_menu($section_id)) {

						$menu_items = self::section_menu_items_count($section_id);
						
						echo '<ul class="essb-submenu essb-cc-sub-'.esc_attr($section_id).($is_active ? ' active-submenu': '').($menu_items < 2 ? ' essb-cc-singleitem' : '').'">';
						foreach (self::$menu_items[$section_id] as $menu_id => $menu_options) {
							echo '<li class="essb-submenu-item essb-cc-'.esc_attr($section_id).'-'.esc_attr($menu_id).'" data-menu="'.esc_attr($section_id).'" data-submenu="'.esc_attr($menu_id).'">';
							echo '<a href="'.esc_url(self::section_build_url($section_id, $menu_id)).'"><span>' . esc_html($menu_options['title']) . '</span></a>';
							echo '</li>';
						}
						echo '</ul>';
					}
						
					echo '</li>';
				}
			}
				
			echo '</ul>';
				
			echo '</div>'; //essb-control-navigation
		}
		
		/**
		 * Output setup panel navigation
		 * @since 8.0
		 */
		public static function draw_sidebar_v8() {
		    
		    self::distribute_unassigned_sections();
		    
		    $plugin_title = 'ESSB';
		    $plugin_title = apply_filters('essb_control_center_navigation_title', $plugin_title);
		    
		    $active_block_id = '';
		    
		    echo '<!-- parent blocks -->';
		    echo '<div class="essb-vertical-blocks-nav">';
		    
		    echo '<div class="essb-header-logo">';
		    
		    echo '<div class="essb-header-logo-image"><div class="essb-logo essb-logo32"></div></div>';
		    echo '<div class="essb-header-logo-title"><span class="version">'.ESSB3_VERSION.'</span></div>';
		    
		    echo '</div>'; // essb-header-logo
		    
		    foreach (self::$sidebar_blocks as $block_id => $options) {
		        $related_sections = isset($options['sections']) ? $options['sections'] : array();
		        $is_active = in_array(self::$active_section, $related_sections);
		        
		        if (!self::one_section_is_registered($related_sections)) {
		            continue;
		        }
		        
		        if ($is_active) {
		            $active_block_id = $block_id;
		        }
		        
		        echo '<div class="nav-block '.($is_active ? ' active' : '').'" title="'.esc_attr($options['title']).'" data-block="'.esc_attr($block_id).'">';
		        echo '<i class="'.esc_attr($options['icon']).'"></i>';
		        echo '</div>';
		    }
		    
		    echo '</div>';
		    
		    echo '<!-- end: parent blocks -->';
		    
		    echo '<!-- Setup Navigation -->';
		    echo '<div class="essb-inner-navigation">';
		    
		    
		    // block rendering
		    foreach (self::$sidebar_blocks as $block_id => $options) {
		        $related_sections = isset($options['sections']) ? $options['sections'] : array();
		        
		        // essb-primary-navigation
		        echo '<ul class="essb-primary-navigation'.($active_block_id == $block_id ? ' active': '').'" id="block-'.esc_attr($block_id).'">';
		        
		        // Generating navigation based on registered sidebar sections
		        foreach (self::$sidebar_sections as $section_id => $section_options) {
		            $type = isset($section_options['type']) ? $section_options['type'] : '';
		            $name = isset($section_options['name']) ? $section_options['name'] : '';
		            
		            if (!in_array($section_id, $related_sections)) {
		                continue;
		            }
		            
		            if ($type == 'split') {
		                echo '<li class="essb-navigation-split essb-cc-'.esc_attr($section_id).'">'.esc_html($name).'</li>';
		            }
		            else if ($type == 'menu') {
		                $icon = isset($section_options['icon']) ? $section_options['icon'] : '';
		                $hide_menu = isset($section_options['hide_menu']) ? $section_options['hide_menu'] : false;
		                $is_active = $section_id == self::$active_section;
		                
		                /**
		                 * Option is internal - it should be hidden inside the navigation
		                 */
		                if ($hide_menu) {
		                    continue;
		                }
		                
		                echo '<li class="essb-menu-item essb-cc-'.esc_attr($section_id).($is_active ? ' active' : '').'" data-menu="'.esc_attr($section_id).'" data-title="'.esc_attr($name).'">';
		                echo '<a href="'.esc_url(self::section_build_url($section_id)).'">';
		                if ($icon != '') {
		                    echo '<i class="'.esc_attr($icon).'"></i>';
		                }
		                echo '<span>'.esc_attr($name).'</span>';
		                echo '</a>';
		                
		                // Generate the sidebar output if needed
		                if (self::section_has_menu($section_id)) {
		                    
		                    $menu_items = self::section_menu_items_count($section_id);
		                    
		                    echo '<ul class="essb-submenu essb-cc-sub-'.esc_attr($section_id).($is_active ? ' active-submenu': '').($menu_items < 2 ? ' essb-cc-singleitem' : '').'">';
		                    foreach (self::$menu_items[$section_id] as $menu_id => $menu_options) {
		                        echo '<li class="essb-submenu-item essb-cc-'.esc_attr($section_id).'-'.esc_attr($menu_id).'" data-menu="'.esc_attr($section_id).'" data-submenu="'.esc_attr($menu_id).'" data-title="'.esc_html($menu_options['title']).'">';
		                        echo '<a href="'.esc_url(self::section_build_url($section_id, $menu_id)).'"><span>' . esc_html($menu_options['title']) . '</span></a>';
		                        
		                        
		                        $has_submenu = self::section_menu_has_sub($section_id, $menu_id);
		                        if ($has_submenu) {
		                            
		                            echo '<div class="essb-inner-menu">';
		                            self::draw_sidebar_submenu($section_id, $menu_id);
		                            echo '</div>';
		                        }
		                        
		                        echo '</li>';
		                    }
		                    echo '</ul>';
		                }
		                
		                echo '</li>';
		            }
		        }
		        
		        echo '</ul>';
		        
		    }
		    
		    echo '</div>'; //essb-control-navigation
		}
		
		public static function draw_blank_content_start() {
			echo '<!-- Options Content -->';
			echo '<div class="essb-control-content">';
		}
		
		public static function draw_blank_content_end() {
			print '</div>';
		}

		/**
		 * Output content area
		 */
		public static function draw_content() {
		    
		    /**
		     * @since 8.0
		     */
		    if (self::$new_version) {
		        self::draw_content_v8();
		        return '';
		    }
		    
			echo '<!-- Options Content -->';
			echo '<div class="essb-control-content">';
				
			// Generation of control center header 
			if (!self::section_without_topbar(self::$active_section)) {
				echo '<div class="essb-control-top">';
				
				echo '<a href="#" class="essb-control-btn essb-head-modesbtn" id="essb-head-modesbtn"><i class="fa fa-magic"></i><span>'.esc_html__('Switch Mode', 'essb').'</span></a>';
				
				// Manage plugin features button
				echo '<a href="#" class="essb-control-btn essb-head-featuresbtn" id="essb-head-featuresbtn"><i class="fa fa-cog"></i><span>'.esc_html__('Activate/Deactivate Features', 'essb').'</span><span class="small-tag">'.esc_html(self::features_count()).'</span></a>';
				
				// Help button
				echo '<a href="'.esc_url(self::help_url()).'" class="essb-control-btn essb-control-btn-help"><i class="fa fa-life-ring"></i><span>'.esc_html__('Get Support', 'essb').'</span></a>';

				// Onboarding button
				echo '<a href="'.esc_url(self::getting_started_url()).'" class="essb-control-btn essb-control-btn-onboarding"><i class="fa fa-info-circle"></i><span>'.esc_html__('Getting Started', 'essb').'</span></a>';
				
				
				if (!ESSBActivationManager::isActivated()) {
					echo '<a href="'.esc_url(admin_url('admin.php?page=essb_redirect_update&tab=update')).'" class="essb-control-btn essb-control-btn-activate"><i class="fa fa-ban"></i> '.esc_html__('Not activated', 'essb').'</a>';
				}
				
				// Save settings button
				if (!self::section_without_save(self::$active_section)) {
					echo '<a href="#" class="essb-control-btn essb-control-btn-save"><i class="ti-save"></i><span>'.esc_html__('Save Settings', 'essb').'</span></a>';
					echo '<input type="Submit" name="Submit" value="Update Settings" class="essb-btn essb-btn-red essb-hidden" id="essb-btn-update">';
				}
				
				echo '</div>';
			}	
			
			// Actual setup screen content
			echo '<div class="essb-control-inner">';
			
			$section_options = self::get_section_options(self::$active_section);
			
			echo '<div class="essb-control-inner-content essb-options-container essb-options">';
			
			do_action('essb_control_center_before_content');
			
			// Draw the options title section
			$section_title = self::get_section_title(self::$active_section);
			
			echo '<div class="essb-options-title">'.($section_title['icon'] != '' ? '<i class="title-icon '.esc_attr($section_title['icon']).'"></i>' : '').esc_attr($section_title['title']).'<span class="essb-options-subtitle"></span></div>';
			
			foreach($section_options as $section => $fields) {
				printf('<div id="essb-container-%1$s" class="essb-data-container">', esc_attr($section));

				$has_submenu = self::section_menu_has_sub(self::$active_section, $section);
				$sub_title = self::section_menu_sub_title(self::$active_section, $section);
				$child_list = array();
				
				echo '<div class="essb-inner-flex">';
				// Generating internal section submenu and get the list of child menu items
				if ($has_submenu) {

					echo '<div class="essb-inner-menu">';
					$child_list = self::draw_submenu(self::$active_section, $section);
					echo '</div>';
				}
				
				echo '<div class="essb-inner-content'.($has_submenu ? ' essb-hasmenu' : '').'">';
				
				// Breadcrumb navigation
				echo '<div class="essb-inner-breadcrumb">'.($section_title['icon'] != '' ? '<i class="title-icon '.esc_attr($section_title['icon']).'"></i>' : '').esc_attr($section_title['title']).'<span class="essb-options-subtitle">'.esc_html($sub_title).'</span></div>';
				
				
				if (!$has_submenu) {				
					echo '<div class="essb-flex-grid essb-parent-options">';
					$section_options = $fields;
					ESSBOptionsFramework::reset_row_status();
					foreach ($section_options as $option) {
						ESSBOptionsFramework::draw_options_field($option, false, array());
					}
					echo '</div>'; // .essb-flex-grid
				}
				else {
					foreach ($child_list as $submenu_id) {
						echo '<div class="essb-child-section essb-parent-options essb-child-section-'.esc_attr($submenu_id).'">';
						
						echo '<div class="essb-flex-grid">';
						$section_options = $fields;
						ESSBOptionsFramework::reset_row_status();
						foreach ($section_options as $option) {
							$option_submenu_id = isset($option['submenu_id']) ? $option['submenu_id'] : '';
							if ($option_submenu_id != $submenu_id) { continue; }
							
							ESSBOptionsFramework::draw_options_field($option, false, array());
						}
						echo '</div>'; // .essb-flex-grid
						
						echo '</div>'; // .essb-child-section
					}
				}
				
				echo '</div>'; // .essb-inner-content

				echo '</div>'; // .essb-inner-flex
				
				echo '</div>'; //.essb-data-container
			}
			echo '</div>'; // .essb-control-inner-content
			
			echo '</div>'; // .essb-control-inner
			
			echo '</div>'; //essb-control-content
		}
		
		/**
		 * Output content area
		 */
		public static function draw_content_v8() {
		    echo '<!-- Options Content -->';
		    echo '<div class="essb-control-content">';
		    
		    // Generation of control center header
		    if (!self::section_without_topbar(self::$active_section)) {
		        echo '<div class="essb-control-top">';
		        
		        echo '<a href="#" class="essb-control-btn essb-head-modesbtn" id="essb-head-modesbtn"><i class="fa fa-magic"></i><span>'.esc_html__('Switch Mode', 'essb').'</span></a>';
		        
		        // Manage plugin features button
		        echo '<a href="#" class="essb-control-btn essb-head-featuresbtn" id="essb-head-featuresbtn"><i class="fa fa-cog"></i><span>'.esc_html__('Activate/Deactivate Features', 'essb').'</span><span class="small-tag">'.esc_html(self::features_count()).'</span></a>';
		        
		        // Help button
		        echo '<a href="'.esc_url(self::help_url()).'" class="essb-control-btn essb-control-btn-help"><i class="fa fa-life-ring"></i><span>'.esc_html__('Get Support', 'essb').'</span></a>';
		        
		        // Onboarding button
		        echo '<a href="'.esc_url(self::getting_started_url()).'" class="essb-control-btn essb-control-btn-onboarding"><i class="fa fa-info-circle"></i><span>'.esc_html__('Getting Started', 'essb').'</span></a>';
		        
		        
		        if (!ESSBActivationManager::isActivated()) {
		            echo '<a href="'.esc_url(admin_url('admin.php?page=essb_redirect_update&tab=update')).'" class="essb-control-btn essb-control-btn-activate"><i class="fa fa-ban"></i> '.esc_html__('Not activated', 'essb').'</a>';
		        }
		        
		        // Save settings button
		        if (!self::section_without_save(self::$active_section)) {
		            echo '<a href="#" class="essb-control-btn essb-control-btn-save"><i class="ti-save"></i><span>'.esc_html__('Save Settings', 'essb').'</span></a>';
		            echo '<input type="Submit" name="Submit" value="Update Settings" class="essb-btn essb-btn-red essb-hidden" id="essb-btn-update">';
		        }
		        
		        echo '</div>';
		    }
		    
		    // Actual setup screen content
		    echo '<div class="essb-control-inner">';
		    
		    $section_options = self::get_section_options(self::$active_section);
		    
		    echo '<div class="essb-control-inner-content essb-options-container essb-options">';
		    
		    do_action('essb_control_center_before_content');
		    
		    // Draw the options title section
		    $section_title = self::get_section_title(self::$active_section);
		    
		    echo '<div class="essb-options-title">'.($section_title['icon'] != '' ? '<i class="title-icon '.esc_attr($section_title['icon']).'"></i>' : '').esc_attr($section_title['title']).'<span class="essb-options-subtitle"></span></div>';
		    
		    foreach($section_options as $section => $fields) {
		        printf('<div id="essb-container-%1$s" class="essb-data-container">', esc_attr($section));
		        
		        $has_submenu = self::section_menu_has_sub(self::$active_section, $section);
		        $sub_title = self::section_menu_sub_title(self::$active_section, $section);
		        $child_list = array();
		        
		        $show_submenu = false;
		        
		        echo '<div class="essb-inner-flex">';
		        // Generating internal section submenu and get the list of child menu items
		        
		        if ($has_submenu && $show_submenu) {
		            
		            echo '<div class="essb-inner-menu">';
		            $child_list = self::draw_submenu(self::$active_section, $section);
		            echo '</div>';
		        }
		        else if ($has_submenu && !$show_submenu) {
		            $child_list = self::generate_submenu(self::$active_section, $section);
		        }
		        
		        echo '<div class="essb-inner-content'.($has_submenu && $show_submenu ? ' essb-hasmenu' : '').'">';
		        
		        // Breadcrumb navigation
		        echo '<div class="essb-inner-breadcrumb">'.($section_title['icon'] != '' ? '<i class="title-icon '.esc_attr($section_title['icon']).'"></i>' : '').esc_attr($section_title['title']).'<span class="essb-options-subtitle">'.esc_html($sub_title).'</span></div>';
		        
		        
		        if (!$has_submenu) {
		            echo '<div class="essb-flex-grid essb-parent-options">';
		            $section_options = $fields;
		            ESSBOptionsFramework::reset_row_status();
		            foreach ($section_options as $option) {
		                ESSBOptionsFramework::draw_options_field($option, false, array());
		            }
		            echo '</div>'; // .essb-flex-grid
		        }
		        else {
		            foreach ($child_list as $submenu_id) {
		                echo '<div class="essb-child-section essb-parent-options essb-child-section-'.esc_attr($submenu_id).'">';
		                
		                echo '<div class="essb-flex-grid">';
		                $section_options = $fields;
		                ESSBOptionsFramework::reset_row_status();
		                foreach ($section_options as $option) {
		                    $option_submenu_id = isset($option['submenu_id']) ? $option['submenu_id'] : '';
		                    if ($option_submenu_id != $submenu_id) { continue; }
		                    
		                    ESSBOptionsFramework::draw_options_field($option, false, array());
		                }
		                echo '</div>'; // .essb-flex-grid
		                
		                echo '</div>'; // .essb-child-section
		            }
		        }
		        
		        echo '</div>'; // .essb-inner-content
		        
		        echo '</div>'; // .essb-inner-flex
		        
		        echo '</div>'; //.essb-data-container
		    }
		    echo '</div>'; // .essb-control-inner-content
		    
		    echo '</div>'; // .essb-control-inner
		    
		    echo '</div>'; //essb-control-content
		}
		
		public static function generate_submenu($section_id, $menu_id) {
		    $child_list = self::$menu_items[$section_id][$menu_id]['child_list'];
		    $tab_list = array();
		    
		    foreach ($child_list as $child_id) {
		        $options = self::$sub_sections[$child_id];
		        $type = isset($options['type']) ? $options['type'] : '';
		        $value = isset($options['value']) ? $options['value'] : '';
		        $id = isset($options['id']) ? $options['id'] : '';
		        $class_name = isset($options['class']) ? $options['class'] : '';
		        
		        if ($type == 'menu') {
		            foreach ($value as $key => $text) {
		                $tab_list[] = $key;
		            }
		        }
		    }
		    
		    return $tab_list;
		}
		
		
		/**
		 * Output child menu navigation and generate a list of the child ids
		 * 
		 * @param string $section_id
		 * @param string $menu_id
		 * @return array
		 */
		public static function draw_submenu($section_id, $menu_id) {
			$child_list = self::$menu_items[$section_id][$menu_id]['child_list'];
			$tab_list = array();
			
			foreach ($child_list as $child_id) {
				$options = self::$sub_sections[$child_id];
				$type = isset($options['type']) ? $options['type'] : '';
				$value = isset($options['value']) ? $options['value'] : '';
				$id = isset($options['id']) ? $options['id'] : '';
				$class_name = isset($options['class']) ? $options['class'] : '';
				
				if ($type == 'title') {
					echo '<h3 class="inner-section-title'.($class_name != '' ? ' '.esc_attr($class_name) : '').'"'.($id != '' ? 'id="'.esc_attr($id).'"': '').'>'.esc_html($value).'</h3>';
				}
				
				if ($type == 'description') {
					echo '<div class="description'.($class_name != '' ? ' '.esc_attr($class_name) : '').'"'.($id != '' ? 'id="'.esc_attr($id).'"': '').'>'.esc_html($value).'</div>';
				}
				
				if ($type == 'splitter') {
					echo '<div class="splitter'.($class_name != '' ? ' '.esc_attr($class_name) : '').'"'.($id != '' ? 'id="'.esc_attr($id).'"': '').'>'.esc_html($value).'</div>';
				}
				
				if ($type == 'button') {
					$text = $value['text'];
					$url = $value['url'];
					$target = isset($value['target']) ? $value['target'] : '';

					echo '<a href="'.esc_url($url).'" '.($target != '' ? 'target="'.esc_attr($target).'"' : '').' class="inner-btn'.($class_name != '' ? ' '.esc_attr($class_name) : '').'"'.($id != '' ? 'id="'.esc_attr($id).'"': '').'>'.esc_html($text).'</a>';
				}
				
				if ($type == 'code') {
					echo $value;
				}
				
				if ($type == 'menu') {
					echo '<ul>';
					
					foreach ($value as $key => $text) {
						echo '<li data-tab="'.$key.'" class="essb-inner-menu-item-'.esc_attr($key).'"><a href="#">'.esc_html($text).'</a></li>';
						$tab_list[] = $key;
					}
					
					echo '</ul>';	
				}
			}
			
			return $tab_list;
		}
		
		/**
		 * Output child menu navigation and generate a list of the child ids
		 *
		 * @param string $section_id
		 * @param string $menu_id
		 * @return array
		 */
		public static function draw_sidebar_submenu($section_id, $menu_id) {
		    $child_list = self::$menu_items[$section_id][$menu_id]['child_list'];
		    $tab_list = array();
		    
		    foreach ($child_list as $child_id) {
		        $options = self::$sub_sections[$child_id];
		        $type = isset($options['type']) ? $options['type'] : '';
		        $value = isset($options['value']) ? $options['value'] : '';
		        $id = isset($options['id']) ? $options['id'] : '';
		        $class_name = isset($options['class']) ? $options['class'] : '';
		        
		        if ($type == 'menu') {
		            echo '<ul>';
		            
		            foreach ($value as $key => $text) {
		                echo '<li data-tab="'.$key.'" class="essb-inner-menu-item-'.esc_attr($key).'"><a href="#">'.esc_html($text).'</a></li>';
		                $tab_list[] = $key;
		            }
		            
		            echo '</ul>';
		        }
		    }
		}
	}
}
