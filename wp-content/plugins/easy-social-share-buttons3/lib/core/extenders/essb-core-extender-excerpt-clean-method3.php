<?php
/**
 * EasySocialShareButtons CoreExtender: Excerpt Clean Method 3
 *
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2016 AppsCreo
 * @since 3.6
 *
 */

if (!function_exists('essb_excerpt_clean_method3')) {
	function essb_excerpt_clean_method3($text, $options, $networks, $default_names) {
		global $essb_networks;
		
		foreach ($essb_networks as $k => $data) {
			$network_name = $data['name'];
		
			if ($network_name != '-') {
				$text = str_replace($network_name, '', $text);
			}
				
			if (!in_array($k, $networks)) {
				continue;
			}
				
			$position_top_name = essb_object_value($options, 'top_'.$k.'_name');
			$position_float_name = essb_object_value($options, 'float_'.$k.'_name');
			$position_postfloat_name = essb_object_value($options, 'postfloat_'.$k.'_name');
		
			$default_name = $default_names[$k];
		
			if (!empty($position_top_name)) {
				if ($position_top_name != '-') {
					$text = str_replace($position_top_name, '', $text);
				}
			}
			if (!empty($position_float_name)) {
				if ($position_float_name != '-') {
					$text = str_replace($position_float_name, '', $text);
				}
			}
			if (!empty($position_postfloat_name)) {
				if ($position_postfloat_name != '-') {
					$text = str_replace($position_postfloat_name, '', $text);
				}
			}
			if (!empty($default_name)) {
				if ($default_name != '-') {
					$text = str_replace($default_name, '', $text);
				}
			}
		}
		
		$message_before_buttons = essb_object_value($options, 'message_share_before_buttons');
		$message_before_buttons = trim($message_before_buttons);
		
		if (!empty($message_before_buttons)) {
			$text = str_replace($message_before_buttons, '', $text);
		}
		
		if (defined('ESSB3_NATIVE_ACTIVE')) {
			$skin_native = essb_object_bool_value($options, 'skin_native');
			if (ESSB3_NATIVE_ACTIVE && $skin_native) {
				$native_buttons = ESSBNativeButtonsHelper::active_native_buttons();
		
				foreach ($native_buttons as $network) {
					$skinned_text = essb_object_value($options, $network.'_text');
					if (!empty($skinned_text)) {
						$text = str_replace($skinned_text, '', $text);
					}
				}
			}
		}
		
		return $text;
	}
}