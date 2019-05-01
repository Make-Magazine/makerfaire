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
						essb_prepare_location_advanced_customization('where', 'display-'.$key, $key);
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

	function essb_custom_position_draw($position = '', $force = false) {
		echo essb_custom_position_generate($position, $force);
	}
}

if (!function_exists('essb_custom_position_generate')) {

	/**
	 * Generate the custom share buttons based on the provided custom key for position
	 *
	 * @param string $position
	 * @return string
	 */
	function essb_custom_position_generate($position = '', $force = false) {
		$r = '';
		if (function_exists('essb_core')) {
			$general_options = essb_core()->get_general_options();
			if ($force) {
				$r = essb_core()->generate_share_buttons($position);
			}
			else {
				if (is_array($general_options)) {
					if (in_array($position, $general_options['button_position'])) {
						$r = essb_core()->generate_share_buttons($position);
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
	
	return essb_custom_position_generate($display, $force == 'true' ? true : false);
}


global $essb_positions_manager;
if (!$essb_positions_manager) {
	$essb_positions_manager = new ESSBCustomPositionsManager();
}