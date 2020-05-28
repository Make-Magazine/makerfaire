<?php

/**
 * ESSBLightModeHelper
 * 
 * Provides predefined variables and designs for Easy Mode
 * 
 * @package EasySocialShareButtons
 * @author appscreo
 * @since 3.4.2
 *
 */
class ESSB_LightMode_Helper {
	
	public static function apply_global_options($options = array()) {
		
		$options['twitter_message_tags_to_hashtags'] = 'false';
		$options['twitter_message_optimize'] = 'false';
		$options['facebookadvanced'] = 'false';
		$options['pinterest_sniff_disable'] = 'false';
		$options['mail_function'] = 'link';
		$options['activate_ga_tracking'] = 'false';
		$options['activate_ga_campaign_tracking'] = '';
		$options['sso_apply_the_content'] = 'false';
		$options['native_active'] = 'false';
		$options['esml_active'] = 'false';
		
		return $options;
	}
	
	public static function position_with_predefined_options($position = '') {
	    $adaptive_positions = array('postfloat', 'sidebar', 'popup', 'flyin', 'topbar', 'bottombar', 'heroshare', 'cornerbar');
	    
	    return in_array($position, $adaptive_positions);	    
	}
	
	public static function apply_position_predefined_settings($position = '', $style = array()) {		
		if ($position == 'popup' || $position == 'flyin' || $position == 'heroshare') {
		    $style = self::popup_style($style, $position);
		}
		
		if ($position == 'sidebar' || $position == 'postfloat') {
		    $style = self::sidebar_styles($style, $position);
		}
		
		if ($position == 'topbar' || $position == 'bottombar') {
		    $style = self::topbar_style($style, $position);
		}
		
		if ($position == 'cornerbar') {
		    $style = self::cornerbar_style($style);
		}
		
		return $style;
	}
	
	private static function cornerbar_style($style = array()) {
	    $style['button_style'] = 'icon';
	    if ($style['show_counter']) {
	        if ($style['counter_pos'] != 'hidden') {
	            $style['counter_pos'] = 'hidden';
	        }
	        
	        if ($style['total_counter_pos'] != 'hidden') {
	            $style['total_counter_pos'] = 'leftbig';
	        }
	    }
	    
	    return $style;
	}
	
	private static function topbar_style($style = array(), $position = '') {
	    $style['button_align'] = 'stretched';
	    
	    return $style;
	}
	
	private static function popup_style($style = array(), $position = '') {
        $style['button_style'] = 'button';
        $style['button_width'] = 'column';
	        
	    if ($position == 'popup') {
	       $style['button_width_columns'] = '3';
        }
	    
	    if ($position == 'flyin') {
            $style['button_width_columns'] = '2';
        }
	        
	    if ($style['show_counter']) {
	        if ($style['counter_pos'] != 'hidden') {
                $style['counter_pos'] = 'insidenamem';
	        }
	        
            if ($style['total_counter_pos'] != 'hidden') {
                $style['total_counter_pos'] = 'leftbig';
            }
	    }
	    
	    return $style;
	}
	
	private static function sidebar_styles($style = array(), $position = '') {
	    
	    $style['button_width'] = '';
	    $style['button_align'] = 'center';
	    $style['button_style'] = 'icon';
	    
	    if ($position == 'sidebar') {
	       $style['nospace'] = 'true';
	    }	    
	    
	    if ($style['show_counter']) {
	        if ($style['total_counter_pos'] != 'hidden') {
	            $style['total_counter_pos'] = 'leftbig';
	        }
	        if ($style['counter_pos'] != 'hidden') {
	            $style['counter_pos'] = 'insidem';
	            $style['button_style'] = 'button';
	            
	            $style['button_align'] = 'left';
	            $style['button_width'] = 'fixed';
	            $style['button_width_fixed_value'] = '90';
	            $style['button_width_fixed_align'] = 'left';
	        }
	    }
	    
	    return $style;
	}
}