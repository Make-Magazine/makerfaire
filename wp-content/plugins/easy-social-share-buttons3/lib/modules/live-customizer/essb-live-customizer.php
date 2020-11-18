<?php

/**
 * Provide front end options customization
 * 
 * @author appscreo
 * @package EasySocialShareButtons
 * @since 4.2
 *
 */

class ESSBLiveCustomizer {
	
	public function __construct() {
		
		add_action ( 'wp_enqueue_scripts', array (&$this, 'enqueue_scripts_and_styles' ) );
		add_action ( 'wp_footer', array (&$this, 'load_customizer' ) );
		
		add_action ( 'wp_ajax_essb_livecustomizer_options', array ($this, 'customizer_options' ) );
		add_action ( 'wp_ajax_essb_livecustomizer_save', array ($this, 'customizer_options_save' ) );
		add_action ( 'wp_ajax_essb_livecustomizer_get', array ($this, 'customizer_get' ) );
		add_action ( 'wp_ajax_essb_livecustomizer_get_meta', array ($this, 'customizer_get_meta' ) );
		
	}
	
	public function is_amp() {
	    if (function_exists('amp_is_request') && amp_is_request()) {
	        return true;
	    }
	    else {
	        return false;
	    }
	}
	
	public function enqueue_scripts_and_styles() {		
		if (essb_is_mobile()) {
			return;
		}
		
		if ($this->is_amp()) {
		    return;
		}
		
		wp_enqueue_style ( 'essb-fontawsome', ESSB3_PLUGIN_URL . '/assets/admin/font-awesome.min.css', array (), ESSB3_VERSION );

		wp_enqueue_style ( 'essb-live-customizer-alerts', ESSB3_PLUGIN_URL.'/assets/admin/sweetalert.css', array (), ESSB3_VERSION );
		wp_enqueue_script ( 'essb-live-customizer-alerts', ESSB3_PLUGIN_URL . '/assets/admin/sweetalert.min.js', array ('jquery'), false, true);
		
		wp_enqueue_style ( 'essb-themifyicons', ESSB3_PLUGIN_URL . '/assets/admin/themify-icons.css', array (), ESSB3_VERSION );	
		wp_enqueue_style ( 'essb-live-customizer', ESSB3_PLUGIN_URL . '/lib/modules/live-customizer/assets/essb-live-customizer.css', array (), ESSB3_VERSION );	
		wp_enqueue_script ( 'essb-live-customizer', ESSB3_PLUGIN_URL . '/lib/modules/live-customizer/assets/essb-live-customizer.js', array ('jquery'), false, true);	
		wp_enqueue_style ( 'essb-live-customizer-animations', ESSB3_PLUGIN_URL.'/assets/css/essb-animations.min.css', array (), ESSB3_VERSION );
		wp_enqueue_style ( 'essb-live-customizer-display-methods', ESSB3_PLUGIN_URL.'/assets/css/essb-display-methods.min.css', array (), ESSB3_VERSION );
		
		wp_localize_script ('essb-live-customizer', 'essb_live_customizer_positions', $this->generate_positions_map());
		wp_add_inline_script ('essb-live-customizer', $this->prepare_position_setup_link());
		
		wp_enqueue_media ();	
	}
	
	public function can_run() {
		$post_types = essb_option_value('display_in_types');
		if (!is_array($post_types)) {
			$post_types = array();
		}
		
		unset($post_types['all_lists']);
		unset($post_types['homepage']);
		
		return is_singular($post_types);
	}
	
	public function load_customizer() {
		if ($this->can_run() && !essb_is_mobile() && !$this->is_amp()) {		
			include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/live-customizer/controls/controls.php');				
			include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/live-customizer/essb-live-customizer-toggle.php');
			include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/live-customizer/controls/panel.php');			
		}
	}	
		
	//-- Internal Options Read & Save
	public function customizer_get() {
		$params = isset($_REQUEST['params']) ? $_REQUEST['params'] : '';
		$response = array();
		
		$params_list = explode(',', $params);
		foreach ($params_list as $param) {
			$response[$param] = essb_option_value($param);
		}
		
		send_nosniff_header();
		header('content-type: application/json');
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		
		echo json_encode($response);
		die();
	}
	
	public function customizer_get_meta() {
		$params = isset($_REQUEST['params']) ? $_REQUEST['params'] : '';
		$postid = isset($_REQUEST['postid']) ? $_REQUEST['postid'] : '';
		$response = array();
	
		if ($postid != '') {
			$params_list = explode(',', $params);
			foreach ($params_list as $param) {
				$response[$param] = get_post_meta($postid, $param, true);
			}
		}
	
		send_nosniff_header();
		header('content-type: application/json');
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
	
		echo json_encode($response);
		die();
	}
	
	public function customizer_options() {
		global $post_id;
		$post_id = isset($_REQUEST['postid']) ? $_REQUEST['postid'] : '';
		$section = isset($_REQUEST['section']) ? $_REQUEST['section'] : '';
		
		
		send_nosniff_header();
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		
		if ($post_id != '' && $section != '') {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/live-customizer/controls/section-'.$section.'.php');
		}
		
		die();
	}
	
	
	public function customizer_options_save() {
		global $essb_options;
		
		$list = isset($_REQUEST['list']) ? $_REQUEST['list'] : '';
		$postid = isset($_REQUEST['postid']) ? $_REQUEST['postid'] : '';
		
		$exist_metabox = false;
		$meta_update = array();
		$exist_options = false;
		
		$debug_output = array();
		$debug_output['post_id'] = $postid;
		$debug_output['list_of_options'] = $list;
		
		if ($list != '') {
			$params = explode('|', $list);
			
			foreach ($params as $param) {
				$value = isset($_REQUEST[$param]) ? $_REQUEST[$param] : '';
				
				if (is_array($value)) {
					$debug_output[$param] = 'meta|' . $value['value'];
					$update_at = $value['update'];
					if ($update_at == 'meta') {
						$meta_update[$param] = $value['value'];
						$exist_metabox = true;
					}
					
					if ($update_at == 'options') {
						$debug_output[$param] = 'options|' . $value['value'];
						$essb_options[$param] = $value['value'];
						
						if ($param == 'mobile_css_activate') {
							if ($value == 'true') {
								$essb_options['mobile_css_readblock'] = 'true';
								$essb_options['mobile_css_all'] = 'true';
								$essb_options['mobile_css_optimized'] = 'true';
							}
							else {
								$essb_options['mobile_css_readblock'] = 'false';
								$essb_options['mobile_css_all'] = 'false';
								$essb_options['mobile_css_optimized'] = 'false';
							}
						}
						
						$exist_options = true;
					}
				}
			}
		}
		
		if ($exist_metabox) {
			$this->update_post_meta($postid, $meta_update);
		}
		
		if ($exist_options) {
			update_option(ESSB3_OPTIONS_NAME, $essb_options);
		}
		
		echo json_encode($debug_output);
		die();
	}
	
	private function update_post_meta($post_id, $meta_details) {
		foreach ($meta_details as $param => $value) {
			$this->save_metabox_value($post_id, $param, $value);
		}
	}
	
	private function save_metabox_value($post_id, $option, $value) {
		if (!empty($value)) {
			update_post_meta ( $post_id, $option, $value );
		}
		else {
			delete_post_meta ( $post_id, $option );
		}
	}
	
	private function generate_positions_map() {
		$r = array();
		
		$r['top'] = 'display-4';
		$r['bottom'] = 'display-5';
		$r['float'] = 'display-6';
		$r['followme'] = 'display-18';
		$r['sidebar'] = 'display-8';
		$r['popup'] = 'display-11';
		$r['flyin'] = 'display-12';
		$r['postfloat'] = 'display-7';
		$r['topbar'] = 'display-9';
		$r['bottombar'] = 'display-10';
		$r['onmedia'] = 'display-13';
		$r['heroshare'] = 'display-14';
		$r['postbar'] = 'display-15';
		$r['point'] = 'display-16';
		$r['cornerbar'] = 'display-19';
		$r['booster'] = 'display-20';
		$r['sharebutton'] = 'display-21';

		return $r;
	}
	
	private function prepare_position_setup_link() {
		$r = 'var essb_live_settings = "'. esc_url(admin_url('admin.php?page=essb_redirect_where')). '"';
		
		return $r;
	}
}