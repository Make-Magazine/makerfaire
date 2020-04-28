<?php

function essb_mytemplatebuilder_generate_css() {
	$current_options = get_option(ESSB3_OPTIONS_NAME);
	
	$template_class = 'essb_template_usercustom';
	
	$snippet = '';
	
	$customizer_totalbgcolor =  essb_mytemplatebuilder_value($current_options, 'mytemplate_totalbgcolor' );
	$customizer_totalcolor =  essb_mytemplatebuilder_value($current_options, 'mytemplate_totalcolor' );
	$customizer_totalnobgcolor =  essb_mytemplatebuilder_value($current_options, 'mytemplate_totalnobgcolor' );
	$customizer_totalfontsize =  essb_mytemplatebuilder_value($current_options, 'mytemplate_totalfontsize' );
	$customizer_totalfontsize_after =  essb_mytemplatebuilder_value($current_options, 'mytemplate_totalfontsize_after' );
	$customizer_totalfontsize_after_color =  essb_mytemplatebuilder_value($current_options, 'mytemplate_totalfontsize_after_color' );
	
	$customizer_totalfontsize_beforeafter =  essb_mytemplatebuilder_value($current_options, 'mytemplate_totalfontsize_beforeafter' );
	
	if ($customizer_totalbgcolor != '') {
		$snippet .= ('.essb_links.'.$template_class.' .essb_totalcount { background: ' . $customizer_totalbgcolor . ' !important;} ');
	}
	if ($customizer_totalnobgcolor == 'true') {
		$snippet .= ('.essb_links.'.$template_class.' .essb_totalcount { background: none !important;} ');
	}
	if ($customizer_totalcolor != '') {
		$snippet .= ('.essb_links.'.$template_class.' .essb_totalcount, .essb_totalcount .essb_t_nb_after { color: ' . $customizer_totalcolor . ' !important;} ');
	}
	if ($customizer_totalfontsize != '') {
		$snippet .= ('.essb_links.'.$template_class.' .essb_totalcount .essb_t_nb { font-size: ' . $customizer_totalfontsize . '!important; line-height:' . $customizer_totalfontsize . '!important;}');
	}
	if ($customizer_totalfontsize_after != '') {
		$snippet .= ('.essb_links.'.$template_class.' .essb_totalcount .essb_t_nb_after { font-size: ' . $customizer_totalfontsize_after . '!important; }');
	}
	if ($customizer_totalfontsize_after_color != '') {
		$snippet .= ('.essb_links.'.$template_class.' .essb_totalcount .essb_t_nb_after { color: ' . $customizer_totalfontsize_after_color . '!important; }');
	}
	
	if ($customizer_totalfontsize_beforeafter != '') {
		$snippet .= ('.essb_links.'.$template_class.' .essb_totalcount_item_before .essb_t_before, .essb_totalcount_item_after .essb_t_before { font-size: ' . $customizer_totalfontsize_beforeafter . '!important; }');
	}
	
	
	$mytemplate_iconsize = essb_mytemplatebuilder_value($current_options, 'mytemplate_iconsize');
	$mytemplate_iconspace = essb_mytemplatebuilder_value($current_options, 'mytemplate_iconspace');
	$mytemplate_namesize = essb_mytemplatebuilder_value($current_options, 'mytemplate_namesize');
	$mytemplate_padding = essb_mytemplatebuilder_value($current_options, 'mytemplate_padding');
	$mytemplate_shape = essb_mytemplatebuilder_value($current_options, 'mytemplate_shape');
	$mytemplate_nameweight = essb_mytemplatebuilder_value($current_options, 'mytemplate_nameweight');
	$mytemplate_texttrans = essb_mytemplatebuilder_value($current_options, 'mytemplate_texttrans');
	$mytemplate_effect = essb_mytemplatebuilder_value($current_options, 'mytemplate_effect');
	$mytemplate_effect_color = essb_mytemplatebuilder_value($current_options, 'mytemplate_effect_color');
	$mytemplate_effect_strength = essb_mytemplatebuilder_value($current_options, 'mytemplate_effect_strength');

	if ($mytemplate_iconsize == '') { $mytemplate_iconsize = 18; }
	if ($mytemplate_iconspace == '') { $mytemplate_iconspace = 9; }
	
	$iconsize_with_gutter = intval($mytemplate_iconsize) + (2 * intval($mytemplate_iconspace));
	
	$snippet .= '.essb_links.'.$template_class.' .essb_icon:before { font-size: '.$mytemplate_iconsize.'px!important; top: '.$mytemplate_iconspace.'px !important; left:'.$mytemplate_iconspace.'px!important;}';
	$snippet .= '.essb_links.'.$template_class.' .essb_icon { width:'.$iconsize_with_gutter.'px !important; height:'.$iconsize_with_gutter.'px!important;}';
	
	if ($mytemplate_namesize != '') {
		$snippet .= '.essb_links.'.$template_class.' .essb_network_name { font-size: '.$mytemplate_namesize.'!important;}';
	}
	
	if ($mytemplate_nameweight != '') {
		if ($mytemplate_nameweight == 'normal') {
			$snippet .= '.essb_links.'.$template_class.' .essb_network_name { font-weight: 400!important;}';
		}
		if ($mytemplate_nameweight == 'bold') {
			$snippet .= '.essb_links.'.$template_class.' .essb_network_name { font-weight: bold!important;}';
		}
		if ($mytemplate_nameweight == 'italic') {
			$snippet .= '.essb_links.'.$template_class.' .essb_network_name { font-style: italic!important;}';
		}
	}
	
	if ($mytemplate_texttrans != '') {
		if ($mytemplate_texttrans == 'uppercase') {
			$snippet .= '.essb_links.'.$template_class.' .essb_network_name { text-transform: uppercase!important;}';
		}
		if ($mytemplate_texttrans == 'capitalaize') {
			$snippet .= '.essb_links.'.$template_class.' .essb_network_name { text-transform: capitalize!important;}';
		}
	}
	
	if ($mytemplate_padding != '') {
		$snippet .= '.essb_links.'.$template_class.' li a { padding: '.$mytemplate_padding.'!important;}';
	}
	
	if ($mytemplate_effect != '') {
		$default_effect_color = $mytemplate_effect_color != '' ? $mytemplate_effect_color : 'rgba(0,0,0,0.15);';
		$default_effect_key = 'box-shadow';
		
		if ($mytemplate_effect == 'flat') {
			$size = 'inset 0 -2px 0';
			if ($mytemplate_effect_strength == 'small') {
				$size = 'inset 0 -1px 0';
			}
			if ($mytemplate_effect_strength == 'medium') {
				$size = 'inset 0 -2px 0';
			}
			if ($mytemplate_effect_strength == 'large') {
				$size = 'inset 0 -3px 0';
			}
			if ($mytemplate_effect_strength == 'xlarge') {
				$size = 'inset 0 -4px 0';
			}
			
			$snippet .= '.essb_links.'.$template_class.' li a { '.$default_effect_key.': '.$size.' '.$default_effect_color.'!important;}';
			$snippet .= '.essb_links.'.$template_class.' li a { -webkit-'.$default_effect_key.': '.$size.' '.$default_effect_color.'!important;}';
		}

		if ($mytemplate_effect == 'shadow') {
			$size = '1px 1px 3px 0';
			if ($mytemplate_effect_strength == 'small') {
				$size = '0px 1px 1px 0';
			}
			if ($mytemplate_effect_strength == 'medium') {
				$size = '2px 2px 5px 0';
			}
			if ($mytemplate_effect_strength == 'large') {
				$size = '3px 3px 10px -1px';
			}
			if ($mytemplate_effect_strength == 'xlarge') {
				$size = '3px 3px 15px -2px';
			}
				
			$snippet .= '.essb_links.'.$template_class.' li a { '.$default_effect_key.': '.$size.' '.$default_effect_color.'!important;}';
			$snippet .= '.essb_links.'.$template_class.' li a { -webkit-'.$default_effect_key.': '.$size.' '.$default_effect_color.'!important;}';
				
		}

		if ($mytemplate_effect == 'glow') {
			$size = '0px 0px 5px 0';
			if ($mytemplate_effect_strength == 'small') {
				$size = '0px 0px 3px 0';
			}
			if ($mytemplate_effect_strength == 'medium') {
				$size = '0px 0px 8px 0';
			}
			if ($mytemplate_effect_strength == 'large') {
				$size = '0px 0px 12px 0';
			}
			if ($mytemplate_effect_strength == 'xlarge') {
				$size = '0px 0px 18px 0';
			}
				
			$snippet .= '.essb_links.'.$template_class.' li a { '.$default_effect_key.': '.$size.' '.$default_effect_color.'!important;}';
			$snippet .= '.essb_links.'.$template_class.' li a { -webkit-'.$default_effect_key.': '.$size.' '.$default_effect_color.'!important;}';
		
		}
		
	}
	
	if ($mytemplate_shape != '') {
		if ($mytemplate_shape == 'rounded') {
			$snippet .= '.essb_links.'.$template_class.' li a { border-radius: 4px!important; -webkit-border-radius: 4px!important;}';
				
		}
		if ($mytemplate_shape == 'round') {
			$snippet .= '.essb_links.'.$template_class.' li a { border-radius: '.$iconsize_with_gutter.'px!important; -webkit-border-radius: '.$iconsize_with_gutter.'px!important;}';
				
		}
		if ($mytemplate_shape == 'leaf') {
			$snippet .= '.essb_links.'.$template_class.' li a { border-radius: 14px 0px!important;}';
				
		}
	}
	
	$mytemplate_hover_color_effect = essb_mytemplatebuilder_value($current_options, 'mytemplate_hover_color_effect');
	if ($mytemplate_hover_color_effect != '') {
		if ($mytemplate_hover_color_effect == 'shiny') {
			$snippet .= '.essb_links.'.$template_class.' li a { overflow: hidden; }';
			$snippet .= '.essb_links.'.$template_class.' li a:before {   content: \'\'; position: absolute; top: -40%;  right: 110%; width: 30px; height: 200%; background: rgba(255, 255, 255, 0.3); transform: rotate(20deg); }';
			$snippet .= '.essb_links.'.$template_class.' li a:hover:before {  right: -50%; transition: 1s ease all; }';
		}
	}
	
	// Generate Each Social Network Code
	$all_networks = essb_available_social_networks();
	$all_network_colors = essb_mytemplatebuilder_network_colors();
		
	$checkbox_list_networks = array();
	foreach ($all_networks as $key => $object) {
		
		// Defaults
		$mytemplate_default_color = essb_mytemplatebuilder_value($current_options, 'mytemplate_default_color');
		$mytemplate_default_color_custom = essb_mytemplatebuilder_value($current_options, 'mytemplate_default_color_custom');
		$mytemplate_default_textcolor = essb_mytemplatebuilder_value($current_options, 'mytemplate_default_textcolor');
		$mytemplate_default_textcolor_custom = essb_mytemplatebuilder_value($current_options, 'mytemplate_default_textcolor_custom');
		$mytemplate_default_outlinesize = essb_mytemplatebuilder_value($current_options, 'mytemplate_default_outlinesize');
		$mytemplate_default_outlinecolor = essb_mytemplatebuilder_value($current_options, 'mytemplate_default_outlinecolor');
		$mytemplate_default_outlinecolor_custom = essb_mytemplatebuilder_value($current_options, 'mytemplate_default_outlinecolor_custom');
		

		$network_color = isset($all_network_colors[$key]) ? $all_network_colors[$key] : '';
		$stored_network_color = $network_color;
		$default_network_color = $network_color;
		$network_text_color = '#fff';
		
		if ($mytemplate_default_color == '' && $mytemplate_default_color_custom != '') {
			$network_color = $mytemplate_default_color_custom;
		}
		
		// Network Specific Colors
		if (essb_mytemplatebuilder_value($current_options, 'mytemplate_network_is_active') == 'true') {
			$mytemplate_network_color = essb_mytemplatebuilder_value($current_options, 'mytemplate_'.$key.'_bgcolor');
			$mytemplate_network_textcolor = essb_mytemplatebuilder_value($current_options, 'mytemplate_'.$key.'_textcolor');
			
			if ($mytemplate_network_color != '') {
				$network_color = $mytemplate_network_color;
				$default_network_color = $network_color;
			}
			
			if ($mytemplate_network_textcolor != '') {
				$network_text_color = $mytemplate_network_textcolor;
			}
		}
		
		// generting network CSS for default state
		if ($mytemplate_default_color == 'white') { 
			$network_color = '#fff';
		}
		if ($mytemplate_default_color == 'dark') {
			$network_color = '#111';
		}
		if ($mytemplate_default_color == 'custom' && $mytemplate_default_color_custom != '') {
			$network_color = $mytemplate_default_color_custom;
			$stored_network_color = $mytemplate_default_color_custom;
		}

		if ($mytemplate_default_textcolor == 'network') {
			$network_text_color = $default_network_color;
		}
		if ($mytemplate_default_textcolor == 'dark') {
			$network_text_color = '#111';
		}
		if ($mytemplate_default_textcolor == 'custom' && $mytemplate_default_textcolor_custom != '') {
			$network_text_color = $mytemplate_default_textcolor_custom;
		}
		
		$snippet .= '.essb_links.'.$template_class.' li.essb_link_'.$key.' a { background-color: '.$network_color.'!important; color:'.$network_text_color.'!important;}';
		
		if ($mytemplate_default_outlinesize != '') {
			$network_outline_color = $stored_network_color;
			
			if ($mytemplate_default_outlinecolor == 'white') {
				$network_outline_color = '#fff';
			}
			if ($mytemplate_default_outlinecolor == 'dark') {
				$network_outline_color = '#111';
			}
			if ($mytemplate_default_outlinecolor == 'custom' && $mytemplate_default_outlinecolor_custom != '') {
				$network_outline_color = $mytemplate_default_outlinecolor_custom;
			}
			
			$snippet .= '.essb_links.'.$template_class.' li.essb_link_'.$key.' a { border: '.$mytemplate_default_outlinesize.'px solid '.$network_outline_color.'}';
				
		}
		
		// hover
		
		$mytemplate_default_color = essb_mytemplatebuilder_value($current_options, 'mytemplate_hover_color');
		$mytemplate_default_color_custom = essb_mytemplatebuilder_value($current_options, 'mytemplate_hover_color_custom');
		$mytemplate_default_textcolor = essb_mytemplatebuilder_value($current_options, 'mytemplate_hover_textcolor');
		$mytemplate_default_textcolor_custom = essb_mytemplatebuilder_value($current_options, 'mytemplate_hover_textcolor_custom');
		$mytemplate_default_outlinesize = essb_mytemplatebuilder_value($current_options, 'mytemplate_hover_outlinesize');
		$mytemplate_default_outlinecolor = essb_mytemplatebuilder_value($current_options, 'mytemplate_hover_outlinecolor');
		$mytemplate_default_outlinecolor_custom = essb_mytemplatebuilder_value($current_options, 'mytemplate_hover_outlinecolor_custom');
		
		
		$network_color = isset($all_network_colors[$key]) ? $all_network_colors[$key] : '';
		$default_network_color = $network_color;
		$stored_network_color = $network_color;
		$network_text_color = '#fff';
		
		if ($mytemplate_default_color == 'network' && $mytemplate_default_color_custom != '') {
			$network_color = $mytemplate_default_color_custom;
		}
		
		// Network Specific Colors
		if (essb_mytemplatebuilder_value($current_options, 'mytemplate_network_is_active') == 'true') {
			$mytemplate_network_color = essb_mytemplatebuilder_value($current_options, 'mytemplate_'.$key.'_hovercolor');
			$mytemplate_network_textcolor = essb_mytemplatebuilder_value($current_options, 'mytemplate_'.$key.'_hovertextcolor');
				
			if ($mytemplate_network_color != '') {
				$network_color = $mytemplate_network_color;
				$default_network_color = $network_color;
			}
				
			if ($mytemplate_network_textcolor != '') {
				$network_text_color = $mytemplate_network_textcolor;
			}
		}
		
		// generting network CSS for default state
		if ($mytemplate_default_color == '') {
			$network_color = essb_mytemplatebuilder_adjust_brightness($network_color, essb_mytemplatebuilder_light_or_dark($network_color));
		}
		if ($mytemplate_default_color == 'white') {
			$network_color = '#fff';
		}
		if ($mytemplate_default_color == 'dark') {
			$network_color = '#111';
		}
		if ($mytemplate_default_color == 'custom' && $mytemplate_default_color_custom != '') {
			$network_color = $mytemplate_default_color_custom;
			$stored_network_color = $mytemplate_default_color_custom;
		}
		
		if ($mytemplate_default_textcolor == 'network') {
			$network_text_color = $default_network_color;
		}
		if ($mytemplate_default_textcolor == 'dark') {
			$network_text_color = '#111';
		}
		if ($mytemplate_default_textcolor == 'custom' && $mytemplate_default_textcolor_custom != '') {
			$network_text_color = $mytemplate_default_textcolor_custom;
		}
		
		$snippet .= '.essb_links.'.$template_class.' li.essb_link_'.$key.' a:hover { background-color: '.$network_color.'!important; color:'.$network_text_color.'!important;}';
		
		if ($mytemplate_default_outlinesize != '') {
			$network_outline_color = $stored_network_color;
				
			if ($mytemplate_default_outlinecolor == 'white') {
				$network_outline_color = '#fff';
			}
			if ($mytemplate_default_outlinecolor == 'dark') {
				$network_outline_color = '#111';
			}
			if ($mytemplate_default_outlinecolor == 'custom' && $mytemplate_default_outlinecolor_custom != '') {
				$network_outline_color = $mytemplate_default_outlinecolor_custom;
			}
				
			$snippet .= '.essb_links.'.$template_class.' li.essb_link_'.$key.' a:hover { border: '.$mytemplate_default_outlinesize.'px solid '.$network_outline_color.'}';
		
		}
		
		$mytemplate_effect = essb_mytemplatebuilder_value($current_options, 'mytemplate_hover_effect');
		$mytemplate_effect_color = essb_mytemplatebuilder_value($current_options, 'mytemplate_hover_effect_color');
		$mytemplate_shape = essb_mytemplatebuilder_value($current_options, 'mytemplate_hover_shape');
		if (essb_mytemplatebuilder_value($current_options, 'mytemplate_hover_effect_strength') != '') {
			$mytemplate_effect_strength = essb_mytemplatebuilder_value($current_options, 'mytemplate_hover_effect_strength');
		}
		
		if ($mytemplate_shape != '') {
			if ($mytemplate_shape == 'square') {
				$snippet .= '.essb_links.'.$template_class.' li a:hover { border-radius: 0px!important; -webkit-border-radius: 0px!important;}';
		
			}
			if ($mytemplate_shape == 'rounded') {
				$snippet .= '.essb_links.'.$template_class.' li a:hover { border-radius: 4px!important; -webkit-border-radius: 4px!important;}';
		
			}
			if ($mytemplate_shape == 'round') {
				$snippet .= '.essb_links.'.$template_class.' li a:hover { border-radius: '.$iconsize_with_gutter.'px!important; -webkit-border-radius: '.$iconsize_with_gutter.'px!important;}';
		
			}
			if ($mytemplate_shape == 'leaf') {
				$snippet .= '.essb_links.'.$template_class.' li a:hover { border-radius: 14px 0px!important; -webkit-border-radius: 14px 0px!important;}';
		
			}
		}
		
		if ($mytemplate_effect != '') {
			$default_effect_color = $mytemplate_effect_color != '' ? $mytemplate_effect_color : 'rgba(0,0,0,0.15);';
			$default_effect_key = 'box-shadow';
		
			if ($mytemplate_effect == 'flat') {
				$size = 'inset 0 -2px 0';
				if ($mytemplate_effect_strength == 'small') {
					$size = 'inset 0 -1px 0';
				}
				if ($mytemplate_effect_strength == 'medium') {
					$size = 'inset 0 -2px 0';
				}
				if ($mytemplate_effect_strength == 'large') {
					$size = 'inset 0 -3px 0';
				}
				if ($mytemplate_effect_strength == 'xlarge') {
					$size = 'inset 0 -4px 0';
				}
				
				$snippet .= '.essb_links.'.$template_class.' li a:hover { '.$default_effect_key.': '.$size.' '.$default_effect_color.'!important;}';
				$snippet .= '.essb_links.'.$template_class.' li a:hover { -webkit-'.$default_effect_key.': '.$size.' '.$default_effect_color.'!important;}';
			}
		
			if ($mytemplate_effect == 'shadow') {
				$size = '1px 1px 3px 0';
				if ($mytemplate_effect_strength == 'small') {
					$size = '0px 1px 1px 0';
				}
				if ($mytemplate_effect_strength == 'medium') {
					$size = '2px 2px 5px 0';
				}
				if ($mytemplate_effect_strength == 'large') {
					$size = '3px 3px 10px -1px';
				}
				if ($mytemplate_effect_strength == 'xlarge') {
					$size = '3px 3px 15px -2px';
				}
				
		
				$snippet .= '.essb_links.'.$template_class.' li a:hover { '.$default_effect_key.': '.$size.' '.$default_effect_color.'!important;}';
				$snippet .= '.essb_links.'.$template_class.' li a:hover { -webkit-'.$default_effect_key.': '.$size.' '.$default_effect_color.'!important;}';
		
			}
		
			if ($mytemplate_effect == 'glow') {
				$size = '0px 0px 5px 0';
				if ($mytemplate_effect_strength == 'small') {
					$size = '0px 0px 3px 0';
				}
				if ($mytemplate_effect_strength == 'medium') {
					$size = '0px 0px 8px 0';
				}
				if ($mytemplate_effect_strength == 'large') {
					$size = '0px 0px 12px 0';
				}
				if ($mytemplate_effect_strength == 'xlarge') {
					$size = '0px 0px 18px 0';
				}
		
				$snippet .= '.essb_links.'.$template_class.' li a:hover { '.$default_effect_key.': '.$size.' '.$default_effect_color.'!important;}';
				$snippet .= '.essb_links.'.$template_class.' li a:hover { -webkit-'.$default_effect_key.': '.$size.' '.$default_effect_color.'!important;}';
		
			}
		
		}
		
	}
	
	return $snippet;
}

function essb_mytemplatebuilder_value($options, $param) {
	return isset($options[$param]) ? $options[$param] : '';
}

function essb_mytemplatebuilder_adjust_brightness($hex, $steps) {
	// Steps should be between -255 and 255. Negative = darker, positive =
	// lighter
	$steps = max ( - 255, min ( 255, $steps ) );

	// Normalize into a six character long hex string
	$hex = str_replace ( '#', '', $hex );
	if (strlen ( $hex ) == 3) {
		$hex = str_repeat ( substr ( $hex, 0, 1 ), 2 ) . str_repeat ( substr ( $hex, 1, 1 ), 2 ) . str_repeat ( substr ( $hex, 2, 1 ), 2 );
	}

	// Split into three parts: R, G and B
	$color_parts = str_split ( $hex, 2 );
	$return = '#';

	foreach ( $color_parts as $color ) {
		$color = hexdec ( $color ); // Convert to decimal
		$color = max ( 0, min ( 255, $color + $steps ) ); // Adjust color
		$return .= str_pad ( dechex ( $color ), 2, '0', STR_PAD_LEFT ); // Make two
		// char hex code
	}

	return $return;
}

function essb_mytemplatebuilder_light_or_dark($color, $steps_light = 30, $steps_dark = -30) {
	$hex = str_replace( '#', '', $color );

	$c_r = hexdec( substr( $hex, 0, 2 ) );
	$c_g = hexdec( substr( $hex, 2, 2 ) );
	$c_b = hexdec( substr( $hex, 4, 2 ) );

	$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

	return $brightness > 155 ? $steps_dark : $steps_light;
}



function essb_mytemplatebuilder_network_colors() {
	$networks = array();
	
	$networks['more'] = '#c5c5c5';
	$networks['meneame'] = '#FF7D12';
	$networks['whatsapp'] = '#1D9E11';
	$networks['mwp'] = '#056CAD';
	$networks['flattr'] = '#8CB55B';
	$networks['ok'] = '#F4731C';
	$networks['xing'] = '#135a5b';
	$networks['weibo'] = '#ED1C24';
	$networks['pocket'] = '#EE4055';
	$networks['tumblr'] = '#2c4762';
	$networks['print'] = '#666';
	$networks['del'] = '#3398fc';
	$networks['buffer'] = '#111111';
	$networks['love'] = '#ED1C24';
	$networks['twitter'] = '#00abf0';
	$networks['google'] = '#EA4335';
	$networks['facebook'] = '#3a579a';
	$networks['pinterest'] = '#cd1c1f';
	$networks['digg'] = '#1b5791';
	$networks['linkedin'] = '#127bb6';
	$networks['stumbleupon'] = '#eb4723';
	$networks['vk'] = '#4c75a3';
	$networks['mail'] = '#666';
	$networks['reddit'] = '#333';
	$networks['blogger'] = '#f59038';
	$networks['amazon'] = '#111';
	$networks['yahoomail'] = '#511295';
	$networks['gmail'] = '#dd4b39';
	$networks['newsvine'] = '#0d642e';
	$networks['hackernews'] = '#f08641';
	$networks['evernote'] = '#7cbf4b';
	$networks['aol'] = '#111111';
	$networks['myspace'] = '#3a5998';
	$networks['mailru'] = '#FAA519';
	$networks['viadeo'] = '#222222';
	$networks['line'] = '#2CBF13';
	$networks['flipboard'] = '#B31F17';
	$networks['comments'] = '#444';
	$networks['yummly'] = '#e26326';
	$networks['sms'] = '#4ea546';
	$networks['viber'] = '#7d539d';
	$networks['telegram'] = '#0088cc';
	$networks['subscribe'] = '#f47555';
	$networks['skype'] = '#00aff0';
	$networks['messenger'] = '#0d87ff';
	$networks['kakaotalk'] = '#FBE600';
	$networks['sharebtn'] = '#2B6A94';
	$networks['livejournal'] = '#0ca8ec';
	$networks['yammer'] = '#3469BA';
	$networks['meetedgar'] = '#6cbdc5';
	
	return $networks;
}