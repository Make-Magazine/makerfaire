<?php
/**
 * Facebook Messenger Live Chat Module for Easy Social Share Buttons for WordPress
 *
 * @package EasySocialShareButtons
 * @author appscreo
 * @version 1.0
 * @since 5.3
 */

if (!function_exists('essb_register_messenger')) {
	function essb_register_messenger() {
		
		if (is_admin() || is_feed()) { return; }
		
		$is_deactivated = false;
		$exclude_from = essb_option_value('fbmessenger_exclude');
		if (! empty ( $exclude_from )) {
			$excule_from = explode ( ',', $exclude_from );
		
			$excule_from = array_map ( 'trim', $excule_from );
			if (in_array ( get_the_ID (), $excule_from, false )) {
				$is_deactivated = true;
			}
		}
		
		if (essb_option_bool_value('fbmessender_deactivate_homepage')) {
			if (is_home() || is_front_page()) {
				$is_deactivated = true;
			}
		}
		
		if (essb_option_bool_value('fbmessenger_posttypes')) {
			$posttypes = $this->option_value('posttypes');
			if (!is_array($posttypes)) {
				$posttypes = array();
			}
				
			if (!is_singular($posttypes)) {
				$is_deactivated = true;
			}
		}
		
		// deactivate display of the functions
		if ($is_deactivated) { return; }
		
		$fbmessenger_logged_greeting = essb_option_value('fbmessenger_logged_greeting');
		$fbmessenger_loggedout_greeting = essb_option_value('fbmessenger_loggedout_greeting');
		$fbmessenger_color = essb_option_value('fbmessenger_color');
		
		$extra_options = '';
		
		if ($fbmessenger_color != '') {
			$extra_options .= ' theme_color="'.esc_attr($fbmessenger_color).'"';
		}
		if ($fbmessenger_logged_greeting != '') {
			$extra_options .= ' logged_in_greeting="'.esc_attr($fbmessenger_logged_greeting).'"';
		}
		if ($fbmessenger_loggedout_greeting != '') {
			$extra_options .= ' logged_out_greeting="'.esc_attr($fbmessenger_loggedout_greeting).'"';
		}
		
		$minimized_state = essb_option_bool_value('fbmessenger_minimized');				
		echo '<div class="fb-customerchat" page_id="'.esc_attr(essb_option_value('fbmessenger_pageid')).'" '.($minimized_state ? 'minimized="true"' : '').$extra_options.'></div>';

		if (essb_option_bool_value('fbmessenger_left')) {
			echo '<style type="text/css">.fb_dialog, .fb-customerchat:not(.fb_iframe_widget_fluid) iframe { left: 18pt !important; right: auto !important; }</style>';
		}
		
		// loading Facebook API required for the function to run
		//essb_resource_builder()->add_social_api('facebook');
		echo '
		<script type="text/javascript">
		(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js#xfbml=1&version=v2.12&autoLogAppEvents=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>';
	}
	
	add_action('wp_footer', 'essb_register_messenger');
}