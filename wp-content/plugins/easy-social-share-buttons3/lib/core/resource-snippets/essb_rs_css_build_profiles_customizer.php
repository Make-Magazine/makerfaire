<?php
if (!function_exists('essb_rs_css_build_profiles_customizer')) {
	add_filter('essb_css_buffer_head', 'essb_rs_css_build_profiles_customizer');
	function essb_rs_css_build_profiles_customizer($buffer) {
		
		$is_active = essb_option_bool_value('activate_profiles_customizer');
		if (!$is_active) {
			return $buffer;
		}
		
		$snippet = '';
		$network_list = ESSBSocialProfilesHelper::available_social_networks();
		
		
		foreach ($network_list as $network => $title) {
			$color_isset = essb_sanitize_option_value('profilecustomize_'.$network);
			if ($color_isset == '' && $network == 'instagram') {
				$color_isset = essb_sanitize_option_value('profilecustomize_instgram');
			}
						
			if ($color_isset != '') {
				$snippet .= ('body .essbfc-container-profiles.essbfc-template-color .essbfc-icon-'.$network.', .essbfc-template-grey .essbfc-icon-'.$network.' { color: '.$color_isset.' !important; }');
				$snippet .= ('body .essbfc-container-profiles.essbfc-template-roundcolor .essbfc-icon-'.$network.', .essbfc-template-roundgrey .essbfc-icon-'.$network.' { background-color: '.$color_isset.' !important; } ');
				$snippet .= ('body .essbfc-container-profiles.essbfc-template-outlinecolor .essbfc-icon-'.$network.', .essbfc-template-outlinegrey .essbfc-icon-'.$network.'  { color: '.$color_isset.' !important; border-color: '.$color_isset.' !important; }');
				$snippet .= ('body .essbfc-container-profiles.essbfc-template-outlinecolor li:hover .essbfc-icon-'.$network.', .essbfc-template-outlinegrey li:hover .essbfc-icon-'.$network.' { background-color: '.$color_isset.' !important; color: #fff !important; }');
				$snippet .= ('body .essbfc-container-profiles.essbfc-template-metro .essbfc-'.$network.' .essbfc-network { background-color: '.$color_isset.' !important; }');
				$snippet .= ('body .essbfc-container-profiles.essbfc-template-flat .essbfc-'.$network.' .essbfc-network { background-color: '.$color_isset.' !important; }');
				$snippet .= ('body .essbfc-container-profiles.essbfc-template-dark .essbfc-'.$network.' .essbfc-network { background-color: '.$color_isset.' !important; }');
				$snippet .= ('body .essbfc-container-profiles.essbfc-template-modern .essbfc-'.$network.' .essbfc-network i { color: '.$color_isset.' !important; }');
				$snippet .= ('body .essbfc-container-profiles.essbfc-template-modern .essbfc-'.$network.' .essbfc-network { border-bottom: 3px solid '.$color_isset.' !important }');
				$snippet .= ('body .essbfc-container-profiles.essbfc-template-modern .essbfc-'.$network.':hover .essbfc-network { background-color: '.$color_isset.' !important; }');
				$snippet .= ('body .essbfc-container-profiles.essbfc-template-modern .essbfc-'.$network.':hover .essbfc-network i { color: #fff !important; }');
				$snippet .= ('body .essbfc-container-profiles.essbfc-template-tinycolor .essbfc-'.$network.' .essbfc-network { background-color: '.$color_isset.' !important; }');
				$snippet .= ('body .essbfc-container-profiles.essbfc-template-modernlight .essbfc-'.$network.' .essbfc-network i { color: '.$color_isset.' !important; }');
				$snippet .= ('body .essbfc-container-profiles.essbfc-template-modernlight .essbfc-'.$network.' .essbfc-network { color: '.$color_isset.' !important }');
				$snippet .= ('body .essbfc-container-profiles.essbfc-template-modernlight .essbfc-'.$network.':hover .essbfc-network i { color: #fff !important; }');
				$snippet .= ('body .essbfc-container-profiles.essbfc-template-modernlight .essbfc-'.$network.':hover .essbfc-network { background-color: '.$color_isset.' !important; color: #fff !important; }');
				
				if ($network == 'instgram') {
					$network1 = 'instagram';
					$snippet .= ('body .essbfc-container-profiles.essbfc-template-color .essbfc-icon-'.$network1.', .essbfc-template-grey .essbfc-icon-'.$network1.' { color: '.$color_isset.' !important; }');
					$snippet .= ('body .essbfc-container-profiles.essbfc-template-roundcolor .essbfc-icon-'.$network1.', .essbfc-template-roundgrey .essbfc-icon-'.$network1.' { background-color: '.$color_isset.' !important; } ');
					$snippet .= ('body .essbfc-container-profiles.essbfc-template-outlinecolor .essbfc-icon-'.$network1.', .essbfc-template-outlinegrey .essbfc-icon-'.$network1.'  { color: '.$color_isset.' !important; border-color: '.$color_isset.' !important; }');
					$snippet .= ('body .essbfc-container-profiles.essbfc-template-outlinecolor li:hover .essbfc-icon-'.$network1.', .essbfc-template-outlinegrey li:hover .essbfc-icon-'.$network1.' { background-color: '.$color_isset.' !important; color: #fff !important; }');
					$snippet .= ('body .essbfc-container-profiles.essbfc-template-metro .essbfc-'.$network1.' .essbfc-network { background-color: '.$color_isset.' !important; }');
					$snippet .= ('body .essbfc-container-profiles.essbfc-template-flat .essbfc-'.$network1.' .essbfc-network { background-color: '.$color_isset.' !important; }');
					$snippet .= ('body .essbfc-container-profiles.essbfc-template-dark .essbfc-'.$network1.' .essbfc-network { background-color: '.$color_isset.' !important; }');
					$snippet .= ('body .essbfc-container-profiles.essbfc-template-modern .essbfc-'.$network1.' .essbfc-network i { color: '.$color_isset.' !important; }');
					$snippet .= ('body .essbfc-container-profiles.essbfc-template-modern .essbfc-'.$network1.' .essbfc-network { border-bottom: 3px solid '.$color_isset.' !important }');
					$snippet .= ('body .essbfc-container-profiles.essbfc-template-modern .essbfc-'.$network1.':hover .essbfc-network { background-color: '.$color_isset.' !important; }');
					$snippet .= ('body .essbfc-container-profiles.essbfc-template-modern .essbfc-'.$network1.':hover .essbfc-network i { color: #fff !important; }');
					$snippet .= ('body .essbfc-container-profiles.essbfc-template-tinycolor .essbfc-'.$network1.' .essbfc-network { background-color: '.$color_isset.' !important; }');
					$snippet .= ('body .essbfc-container-profiles.essbfc-template-modernlight .essbfc-'.$network1.' .essbfc-network i { color: '.$color_isset.' !important; }');
					$snippet .= ('body .essbfc-container-profiles.essbfc-template-modernlight .essbfc-'.$network1.' .essbfc-network { color: '.$color_isset.' !important }');
					$snippet .= ('body .essbfc-container-profiles.essbfc-template-modernlight .essbfc-'.$network1.':hover .essbfc-network i { color: #fff !important; }');
					$snippet .= ('body .essbfc-container-profiles.essbfc-template-modernlight .essbfc-'.$network1.':hover .essbfc-network { background-color: '.$color_isset.' !important; color: #fff !important; }');
				}
				
			}
		}
		return $buffer.$snippet;
	}
}