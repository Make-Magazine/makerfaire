<?php
/**
 * The custom positions render file. This file contains all read/write functions along
 * with code render and shortcodes support
 * 
 * @since 5.9.2
 * @package EasySocialShareButtons
 * @author appscreo
 */

define('ESSB_CUSTOM_POSITIONS', 'essb_custom_positions');

add_shortcode('social-share-display', 'essb_shortcode_show_custom_position');


/**
 * @author appscreo
 */
class ESSBCustomPositionsManager {
	
	public $hooks = array();

	/**
	 * Create and load required actions and hooks
	 * 
	 */
	public function __construct() {
		
		$existing_hooks = essb5_get_custom_positions();
		
		if (!is_array($existing_hooks)) {
			$existing_hooks = array();
		}
		
		$this->hooks = $existing_hooks;
		
		
		// interface registration of menu options
		$this->essb_interface_register();
	}

	
	/**
	 * Handle registration of custom display methods and hooks created by user inside plugin menu
	 */
	public function essb_interface_register() {
		add_filter('essb4_custom_method_list', array($this, 'essb_interface_custom_positions'));
		add_filter('essb4_custom_positions', array($this, 'essb_display_register_mycustom_position'));
		add_filter('essb4_button_positions', array($this, 'essb_display_mycustom_position'));
		//@since 7.0 - register also on mobile devices
		add_filter('essb4_button_positions_mobile', array($this, 'essb_display_mycustom_position'));
		add_action('init', array($this, 'essb_custom_methods_register'), 99);
	}
	
	public function essb_interface_custom_positions($methods) {
		$count = 40;
		
		foreach ($this->hooks as $key => $name) {
			
			
			if ($name != '') {
				$count++;
				$methods['display-'.$key] = $name;
			}
		}
		
		
		return $methods;
	}
	
	public function essb_display_register_mycustom_position($positions) {
		foreach ($this->hooks as $key => $name) {
			
			if ($name != '') {
				$positions[$key] = $name;
			}
		}
		
		return $positions;
	}
	
	public function essb_display_mycustom_position($positions) {
		
		foreach ($this->hooks as $key => $name) {
			if ($name != '') {
				$positions[$key] = array ("image" => "assets/images/display-positions-09.png", "label" => $name );
			}
		}
		
		
		return $positions;
	}
	
	public function essb_custom_methods_register() {
	
		if (is_admin()) {
			if (class_exists('ESSBOptionsStructureHelper')) {
				
				$count = 40;
				
				foreach ($this->hooks as $key => $name) {						
					if ($name != '') {
						$count++;
						if (class_exists('ESSBControlCenter')) {
							essb_prepare_location_advanced_customization('where', 'positions|display-'.$key, $key);
						}
						else {
							essb_prepare_location_advanced_customization('where', 'display-'.$key, $key);
						}
					}
				}
			}
	
		}
	}
	
	public function action_parser() {
		$running_action = current_action();

		if (isset($this->hook_actions_map[$running_action])) {
			$key = $this->hook_actions_map[$running_action];
			
			essb_hook_integration_draw($key);
		}
	}
	
	public function filter_parser($buffer) {
		$running_action = current_filter();
		
		if (isset($this->hook_actions_map[$running_action])) {
			$key = $this->hook_actions_map[$running_action];
				
			$buffer .= essb_hook_integration_generate($key);
		}
		
		return $buffer;
	}	
}

/**
 * Read the list of custom positions and returns it as array
 * 
 * @returns {array}
 */
function essb5_get_custom_positions() {
	$r = get_option(ESSB_CUSTOM_POSITIONS);
	
	if (!$r || !is_array($r)) {
		$r = array();
	}
	
	return $r;
}

/**
 * Update designs and store them inside the database
 * 
 * @param unknown_type $designs
 */
function essb5_save_custom_positions($designs = array()) {
	update_option(ESSB_CUSTOM_POSITIONS, $designs);
}


function essb5_remove_custom_position($design = '') {
	$designs = essb5_get_custom_positions();
	
	if (isset($designs[$design])) {
		unset ($designs[$design]);
	}
	
	essb5_save_custom_positions($designs);
}

if (!function_exists('essb_custom_position_draw')) {

	/**
	 * Generate and draw custom position share buttons inside plugin
	 *
	 * @param string $position
	 */

	function essb_custom_position_draw($position = '', $force = false, $archive = false, $custom_share = array(), $loop = false) {
	    
	    if ($loop) {
	        $post_id = get_the_ID();
	        $post_data = ESSB_Runtime_Cache::get_post_sharing_data($post_id);
	        $share_object = $post_data->compile_share_object();
	        
	        $custom = true;
	        $custom_share['custom'] = true;
	        $custom_share['url'] = $share_object['url'];
	        $custom_share['message'] = $share_object['title'];
	        $custom_share['title'] = $share_object['title'];
	        $custom_share['image'] = $share_object['image'];
	        $custom_share['description'] = $share_object['description'];
	        $custom_share['twitter_user'] = $share_object['twitter_user'];
	        $custom_share['twitter_hashtags'] = $share_object['twitter_hashtags'];
	        $custom_share['twitter_tweet'] = $share_object['twitter_tweet'];
	        $custom_share['force_set_post_id'] = $post_id;
	    }
	    
	    echo essb_custom_position_generate($position, $force, $archive, $custom_share);
	}
}

if (!function_exists('essb_custom_position_generate')) {

	/**
	 * Generate the custom share buttons based on the provided custom key for position
	 *
	 * @param string $position
	 * @return string
	 */
    function essb_custom_position_generate($position = '', $force = false, $archive = false, $custom_share = array()) {
		$r = '';
		if (function_exists('essb_core')) {
			$general_options = essb_core()->get_general_options();
			
			$custom = false;
			$custom_options = array();
			
			if (isset($custom_share) && isset($custom_share['custom']) && $custom_share['custom']) {
			    $custom = true;
			    $custom_options['url'] = isset($custom_share['url']) ? $custom_share['url'] : '';
			    $custom_options['title'] = isset($custom_share['message']) ? $custom_share['message'] : '';
			    $custom_options['title_plain'] = isset($custom_share['message']) ? $custom_share['message'] : '';
			    $custom_options['description'] = isset($custom_share['message']) ? $custom_share['message'] : '';
			    $custom_options['image'] = isset($custom_share['image']) ? $custom_share['image'] : '';
			    
                /**
                 * @since 8.1.5 Passing tweet as option too
                 */			    			    
			    if (!isset($custom_share['twitter_tweet']) || empty($custom_share['twitter_tweet'])) {
			        $custom_share['twitter_tweet'] = $custom_share['message'];
			    }
			    
			    $custom_options['twitter_tweet'] = isset($custom_share['twitter_tweet']) ? $custom_share['twitter_tweet'] : '';
			}
						
			// Forcing archive mode (mainly used in Elementor)
			if ($archive) {
			    ESSB_Runtime_Cache::set('force-archive-'.$position, true);
			}
			
			if ($force) {
			    if ($custom) {
			        $r = essb_core()->generate_share_buttons($position, 'share', $custom_options);
			    }
			    else {
				    $r = essb_core()->generate_share_buttons($position);
			    }
			}
			else {
				if (is_array($general_options)) {
					if (in_array($position, $general_options['button_position'])) {
					    if ($custom) {
					        $r = essb_core()->generate_share_buttons($position, 'share', $custom_options);
					    }
					    else {
						  $r = essb_core()->generate_share_buttons($position);
					    }
					}
				}
			}
		}

		return $r;
	}
}

function essb_shortcode_show_custom_position($atts = array()) {
	$display = isset($atts['display']) ? $atts['display'] : '';
	$force = isset($atts['force']) ? $atts['force'] : '';
	$archive = isset($atts['archive']) ? $atts['archive'] : '';
	
	$custom = isset($atts['custom']) ? $atts['custom'] : '';
	$url = isset($atts['url']) ? $atts['url'] : '';
	$message = isset($atts['message']) ? $atts['message'] : '';
	$image = isset($atts['image']) ? $atts['image'] : '';
	
	$post_id = isset($atts['post_id']) ? $atts['post_id'] : '';
	
	
	/**
	 * @since 8.2 Integration with JetEngine Listing templates
	 * @var Ambiguous $jetengine
	 */
	$jetengine = isset($atts['jetengine']) ? $atts['jetengine'] : '';
	
	/**
	 * @since 9.3 Integration with Elementor Loop Grid in arhive template
	 */
	$loop_archive = isset($atts['looparchive']) ? $atts['looparchive'] : '';
	
	$custom_options = array();
		
	if ($custom == 'true') {
	    if (essb_option_bool_value('affwp_active_shortcode')) {
	        essb_helper_maybe_load_feature('integration-affiliatewp');
	        $url = essb_generate_affiliatewp_referral_link($url);
	    }
	    
	    $custom_options['custom'] = true;
	    $custom_options['url'] = $url;
	    $custom_options['message'] = $message;
	    $custom_options['title'] = $message;
	    $custom_options['image'] = $image;
	    
	    $custom_options['twitter_tweet'] = isset($atts['tweet']) ? $atts['tweet'] : '';
	    
	    /**
	     * @since 9.1.4
	     */
	    if (!empty($post_id)) {
	        $custom_options['force_set_post_id'] = $post_id;
	    }
	}
	
	if ($loop_archive == 'true') {
	    $post_id = get_the_ID();
	    $post_data = ESSB_Runtime_Cache::get_post_sharing_data($post_id);
	    $share_object = $post_data->compile_share_object();
	    
	    $custom_options['custom'] = true;
	    $custom_options['url'] = $share_object['url'];
	    $custom_options['message'] = $share_object['title'];
	    $custom_options['title'] = $share_object['title'];
	    $custom_options['image'] = $share_object['image'];
	    $custom_options['description'] = $share_object['description'];
	    $custom_options['twitter_user'] = $share_object['twitter_user'];
	    $custom_options['twitter_hashtags'] = $share_object['twitter_hashtags'];
	    $custom_options['twitter_tweet'] = $share_object['twitter_tweet'];
	    $custom_options['force_set_post_id'] = $post_id;
	}
	
	if ($jetengine == 'true' && function_exists('jet_engine')) {
	    $post_id = jet_engine()->listings->data->get_current_object_id();
	    
	    /**
	     * @since 8.1.6 for customizations
	     */
	    if (has_filter('essb_custom_position_display_jetengine_postid')) {
	        $post_id = apply_filters('essb_custom_position_display_jetengine_postid', $post_id);
	    }
	    
	    $post_data = ESSB_Runtime_Cache::get_post_sharing_data($post_id);
	    $share_object = $post_data->compile_share_object();
	    
	    $custom_options['custom'] = true;
	    $custom_options['url'] = $share_object['url'];
	    $custom_options['message'] = $share_object['title'];
	    $custom_options['title'] = $share_object['title'];
	    $custom_options['image'] = $share_object['image'];
	    $custom_options['description'] = $share_object['description'];
	    $custom_options['twitter_user'] = $share_object['twitter_user'];
	    $custom_options['twitter_hashtags'] = $share_object['twitter_hashtags'];
	    $custom_options['twitter_tweet'] = $share_object['twitter_tweet'];
	    $custom_options['force_set_post_id'] = $post_id;
	}
 	
	return essb_custom_position_generate($display, $force == 'true' ? true : false, $archive == 'true' ? true : false, $custom_options);
}

// Enable the instance of manager class
ESSB_Factory_Loader::activate('positions-manager', 'ESSBCustomPositionsManager');