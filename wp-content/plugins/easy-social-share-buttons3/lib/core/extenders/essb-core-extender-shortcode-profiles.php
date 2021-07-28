<?php
/**
 * EasySocialShareButtons CoreExtender: Shortcode Profiles
 *
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2016 AppsCreo
 * @since 3.6
 *
 */

class ESSBCoreExtenderShortcodeProfiles {
	
	public static function parse_shortcode($atts, $options) {
		
		$sc_networks = isset($atts['networks']) ? $atts['networks'] : '';
		$sc_template = isset($atts['template']) ? $atts['template'] : 'flat';
		$sc_animation = isset($atts['animation']) ? $atts['animation'] : '';
		$sc_nospace = isset($atts['nospace']) ? $atts['nospace'] : 'false';
		$sc_class = isset($atts['class']) ? $atts['class'] : '';
		$sc_align = isset($atts['align']) ? $atts['align'] : '';
		$sc_size = isset($atts['size']) ? $atts['size'] : '';
		$sc_cta = isset($atts['cta']) ? $atts['cta'] : '';
		$sc_cta_vertical = isset($atts['cta_vertical']) ? $atts['cta_vertical'] : '';
		$sc_columns = isset($atts['columns']) ? $atts['columns'] : '';
		$sc_profiles_all_networks = isset($atts['profiles_all_networks']) ? $atts['profiles_all_networks'] : '';
		
		if ($sc_profiles_all_networks == '' && $sc_networks != '') {
		    $sc_profiles_all_networks = 'true';
		}
		
		if ($sc_profiles_all_networks != '' && !essb_unified_true($sc_profiles_all_networks)) {
		    $sc_networks = '';
		}
				
		$sc_nospace = essb_unified_true($sc_nospace);	
		$sc_cta = essb_unified_true($sc_cta);
		$sc_cta_vertical = essb_unified_true($sc_cta_vertical);
		
		$profile_networks = array();
		$profile_networks_text = array();
		if ($sc_networks != '') {
			$profile_networks = explode(',', $sc_networks);
		}
		else {
		    $profile_networks = ESSBSocialProfilesHelper::get_active_networks();
		    $profile_active_networks = $profile_networks;
		    
		    if (!is_array($profile_networks)) {
		        $profile_networks = array();
		    }
		    
		    $profiles_order = ESSBSocialProfilesHelper::get_active_networks_order();
		    
		    if (!is_array($profiles_order)) {
		        $profiles_order = array();
		    }
		}		
		
		// prepare network values
		$sc_network_address = array();
		foreach ($profile_networks as $network) {
			$value = isset($atts[$network]) ? $atts[$network] : '';
			$text = isset($atts['profile_text_'.$network]) ? $atts['profile_text_'.$network] : '';
				
			if (empty($value)) {
				$value = isset($atts['profile_'.$network]) ? $atts['profile_'.$network] : '';
			}
				
			if (empty($value)) {
				$value = essb_object_value($options, 'profile_'.$network);
			}
			
			if (empty($text)) {
				$text = essb_object_value($options, 'profile_text_'.$network);
			}
				
			if (!empty($value)) {
				$sc_network_address[$network] = $value;
			}
			
			if (!empty($text)) {
				$profile_networks_text[$network] = $text;
			}
		}
		
		
		if (!defined('ESSB3_SOCIALPROFILES_ACTIVE')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-profiles/essb-social-profiles.php');
			include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-profiles/essb-social-profiles-helper.php');
			define('ESSB3_SOCIALPROFILES_ACTIVE', 'true');
			$template_url = ESSBSocialProfilesHelper::get_stylesheet_url();
			essb_resource_builder()->add_static_footer_css($template_url, 'essb-social-followers-counter');
		}
		else {
			if (!essb_resource_builder()->is_activated('profiles_css')) {
			    $template_url = ESSBSocialProfilesHelper::get_stylesheet_url();
				essb_resource_builder()->add_static_footer_css($template_url, 'essb-social-followers-counter');				
			}
		}
		
		$options = array(
				'position' => '',
				'template' => $sc_template,
				'animation' => $sc_animation,
				'nospace' => $sc_nospace,
				'networks' => $sc_network_address,
				'networks_text' => $profile_networks_text,
				'class' => $sc_class,
				'align' => $sc_align,
				'size' => $sc_size,
				'cta' => $sc_cta,
				'cta_vertical' => $sc_cta_vertical,
				'columns' => $sc_columns
		);
		
		return ESSBSocialProfiles::draw_social_profiles($options);
	}
	
}