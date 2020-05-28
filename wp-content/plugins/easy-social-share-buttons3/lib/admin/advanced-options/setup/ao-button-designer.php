<?php
/**
 * Create and manage new share buttons 
 */

$loadingOptions = isset($_REQUEST['loadingOptions']) ? $_REQUEST['loadingOptions'] : array();
$network = isset($loadingOptions['network']) ? $loadingOptions['network'] : '';
$network_setup = array();

if ($network != '') {
	$network_setup = essb_get_custom_button_settings($network);
	
	if (isset($network_setup['icon'])) {
		$network_setup['icon'] = base64_decode($network_setup['icon']);
	}
}

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options_custom_networks');
}

echo '<input type="hidden" name="network_button_id" id="network_button_id" value="'.esc_attr($network).'"/>';

/**
 * Button parameters
 */

essb5_draw_input_option('network_id', esc_html__('Network ID', 'essb'), esc_html__('Fill a custom unique ID for the network button. Use only lowercase Latin symbols (a-z) and numbers (0-9). No spaces are allowed - use underscore.', 'essb'), true, true, essb_array_value('network_id', $network_setup));
essb5_draw_input_option('name', esc_html__('Name', 'essb'), esc_html__('The name of the button. This will be the text you will see in the list of networks. It will also be the text that will appear when you add a button initially. Try to keep it short.', 'essb'), true, true, essb_array_value('name', $network_setup));
essb5_draw_input_option('url', esc_html__('Button URL', 'essb'), esc_html__('The URL can be a share command but it also can be a plain URL where you wish to direct your visitors. If you are filling share command than you should fill at the proper places the variables representing sharable content: %%title%%, %%image%%, %%permalink%%, %%description%%', 'essb'), true, true, essb_array_value('url', $network_setup));
essb5_draw_switch_option('counter', esc_html__('Enable internal share counter', 'essb'), esc_html__('The option will enable support for the internal share counter. To show the counter on your site you need to have the internal counter option active (in Share Counter Setup) and you are using a design with share values.', 'essb'), true, essb_array_value('counter', $network_setup));

essb5_draw_editor_option('icon', esc_html__('SVG Icon', 'essb'), esc_html__('Place the content of an SVG icon that will be used to show the buttons. To do this prepare a flat color SVG icon (or download such from a collection). Open that file with a text editor and copy content inside the field.', 'essb'), 'htmlmixed', true, essb_array_value('icon', $network_setup));
essb5_draw_input_option('padding_top', esc_html__('Icon top padding', 'essb'), esc_html__('Correct the default value of padding for the icon. Numeric values only. The current default value is 10 (when nothing filled).', 'essb'), true, true, essb_array_value('padding_top', $network_setup));
essb5_draw_input_option('padding_left', esc_html__('Icon left padding', 'essb'), esc_html__('Correct the default value of padding for the icon. Numeric values only. The current default value is 8 (when nothing filled).', 'essb'), true, true, essb_array_value('padding_left', $network_setup));

essb5_draw_heading(esc_html__('Colors', 'essb'), '5');
essb5_draw_color_option('bgcolor', esc_html__('Background color', 'essb'), '', false, true, essb_array_value('bgcolor', $network_setup));
essb5_draw_color_option('iconcolor', esc_html__('Icon color', 'essb'), '', false, true, essb_array_value('iconcolor', $network_setup));
essb5_draw_color_option('textcolor', esc_html__('Text color', 'essb'), '', false, true, essb_array_value('textcolor', $network_setup));

essb5_draw_heading(esc_html__('Colors on Hover', 'essb'), '5');
essb5_draw_color_option('bgcolor_hover', esc_html__('Background color', 'essb'), '', false, true, essb_array_value('bgcolor_hover', $network_setup));
essb5_draw_color_option('iconcolor_hover', esc_html__('Icon color', 'essb'), '', false, true, essb_array_value('iconcolor_hover', $network_setup));
essb5_draw_color_option('textcolor_hover', esc_html__('Text color', 'essb'), '', false, true, essb_array_value('textcolor_hover', $network_setup));
