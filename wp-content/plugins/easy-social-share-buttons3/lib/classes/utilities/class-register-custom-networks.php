<?php
/**
 * Register user-created custom network buttons
 * 
 * @since 7.0
 * @author appscreo
 * @package EasySocialShareButtons
 */

class ESSB_Register_Custom_Networks {

	public static $_instance;
	
	private $buttons = array();
	
	
	/**
	 * Get static instance of class
	 *
	 * @return ESSB_Manager
	 */
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
	
	/**
	 * Register the custom network buttons
	 */
	public function __construct() {
		global $essb_networks;
		
		if (! function_exists ( 'essb_get_custom_buttons' )) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/helpers/custombuttons-helper.php');
		}
		
		$this->buttons = essb_get_custom_buttons();
		
		add_filter ( 'essb4_social_networks', array($this, 'register_buttons') );
		
		foreach ($this->buttons as $id => $data) {
			$counter = isset($data['counter']) ? $data['counter'] : '';
			$name = isset($data['name']) ? $data['name'] : '';
			
			$essb_networks[$id] = array('name' => $name, 'type' => 'buildin', 'supports' => 'desktop,mobile,retina templates only');
			
			// Button without internal share counter
			if ($counter != 'true') {
				add_filter ( "essb4_no_counter_for_{$id}", '__return_true');
			}
			
			add_filter ( "essb4_shareapi_url_{$id}", array($this, 'prepare_share_url') );
			add_filter ( "essb_network_svg_icon_{$id}", array($this, 'prepare_svg_icon') );
		}
		
		$this->prepare_button_styles();
	}
	
	/**
	 * Generate a custom SVG icon inline
	 * 
	 * @param string $svg
	 */
	public function prepare_svg_icon($svg = '') {
		global $wp_current_filter;
		
		if( ! empty( $wp_current_filter ) && is_array( $wp_current_filter ) ) {
			foreach( $wp_current_filter as $filter ) {
				if (strpos($filter, 'essb_network_svg_icon_') !== false) {
					$id = str_replace('essb_network_svg_icon_', '', $filter);
						
					$data = isset($this->buttons[$id]) ? $this->buttons[$id] : array();
					$icon = isset($data['icon']) ? $data['icon'] : '';
					
					if ($icon != '') {
						$svg = stripslashes(base64_decode($icon));
					}
				}
			}
		}
		
		return $svg;
	}
	
	/**
	 * Generate the share URL for the custom button
	 * 
	 * @param {array} $share
	 */
	public function prepare_share_url($share = array()) {
		global $wp_current_filter;
		
		$url = '';
		
		if( ! empty( $wp_current_filter ) && is_array( $wp_current_filter ) ) {
			foreach( $wp_current_filter as $filter ) {
				if (strpos($filter, 'essb4_shareapi_url_') !== false) {
					$id = str_replace('essb4_shareapi_url_', '', $filter);
					
					$data = isset($this->buttons[$id]) ? $this->buttons[$id] : array();
					$url = isset($data['url']) ? $data['url'] : '';
					
					$url = str_replace('%%permalink%%', $share['url'], $url);
					$url = str_replace('%%image%%', $share['image'], $url);
					$url = str_replace('%%title%%', $share['title'], $url);
					$url = str_replace('%%description%%', $share['description'], $url);
				}
			}
		}
		
		return $url;
	}
	
	/**
	 * Register custom network buttons
	 * 
	 * @param {array} $networks
	 */
	public function register_buttons($networks = array()) {
		foreach ($this->buttons as $id => $data) {
			$name = isset($data['name']) ? $data['name'] : $id;
		
			$networks[$id] = array('name' => $name, 'type' => 'buildin', 'supports' => 'desktop,mobile,retina templates only');
		}
		
		return $networks;
	}
	
	/**
	 * Generate the custom styles for the buttons that user register
	 */
	public function prepare_button_styles() {
		$css_code = '';
		
		foreach ($this->buttons as $id => $data) {
			$bgcolor = isset($data['bgcolor']) ? $data['bgcolor'] : '';
			$iconcolor = isset($data['iconcolor']) ? $data['iconcolor'] : '';
			$textcolor = isset($data['textcolor']) ? $data['textcolor'] : '';

			$bgcolor_hover = isset($data['bgcolor_hover']) ? $data['bgcolor_hover'] : '';
			$iconcolor_hover = isset($data['iconcolor_hover']) ? $data['iconcolor_hover'] : '';
			$textcolor_hover = isset($data['textcolor_hover']) ? $data['textcolor_hover'] : '';
			
			$padding_left = isset($data['padding_left']) ? $data['padding_left'] : '';
			$padding_top = isset($data['padding_top']) ? $data['padding_top'] : '';
			
			if (intval($padding_left) == 0) { $padding_left = '8'; }
			if (intval($padding_top) == 0) { $padding_top = '10'; }
				
			$icon = isset($data['icon']) ? $data['icon'] : '';
			
			if ($bgcolor != '' || $textcolor != '') {
				$css_code .= '.essb_links .essb_link_'.esc_attr($id).' a { ';
				if ($bgcolor != '') {
					$css_code .= 'background-color:'.esc_attr($bgcolor).'!important;';
				}
				if ($textcolor != '') {
					$css_code .= 'color:'.esc_attr($textcolor).'!important;';
				}
				$css_code .= '}';
			}
			
			if ($bgcolor_hover != '' || $textcolor_hover != '') {
				$css_code .= '.essb_links .essb_link_'.esc_attr($id).' a:hover { ';
				if ($bgcolor != '') {
					$css_code .= 'background-color:'.esc_attr($bgcolor_hover).'!important;';
				}
				if ($textcolor != '') {
					$css_code .= 'color:'.esc_attr($textcolor_hover).'!important;';
				}
				$css_code .= '}';
			}
			
			if ($icon != '') {
				$css_code .= '.essb_links .essb_link_'.esc_attr($id).' a svg { height: calc(100% - '.($padding_top * 2).'px); width: auto; padding-top: '.esc_attr($padding_top).'px; padding-left: '.esc_attr($padding_left).'px; } ';
				
				if ($iconcolor != '') {
					$css_code .= '.essb_links .essb_link_'.esc_attr($id).' a svg, .essb_links .essb_link_'.esc_attr($id).' a svg path { fill: '.esc_attr($iconcolor).'!important; } ';
				}
				
				if ($iconcolor_hover != '') {
					$css_code .= '.essb_links .essb_link_'.esc_attr($id).' a:hover svg, .essb_links .essb_link_'.esc_attr($id).' a:hover svg path { fill: '.esc_attr($iconcolor_hover).'!important; } ';
				}
			}
		}
		
		essb_resource_builder()->add_css($css_code, 'essb-custom-userbuttons');
	}
}

ESSB_Factory_Loader::activate_instance('register-custom-networks', 'ESSB_Register_Custom_Networks');