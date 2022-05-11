<?php
/**
 * Create and manage new share buttons 
 */

$loadingOptions = isset($_REQUEST['loadingOptions']) ? $_REQUEST['loadingOptions'] : array();
$network = isset($loadingOptions['network']) ? $loadingOptions['network'] : '';
$network_setup = array();

if ($network != '') {
	$network_setup = essb_get_custom_profile_button_settings($network);
	
	if (isset($network_setup['icon'])) {
		$network_setup['icon'] = base64_decode($network_setup['icon']);
	}
}

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options_customprofile_networks');
}

echo '<input type="hidden" name="network_button_id" id="network_button_id" value="'.esc_attr($network).'"/>';

/**
 * Button parameters
 */

essb5_draw_input_option('network_id', esc_html__('Network ID', 'essb'), esc_html__('Fill a custom unique ID for the network button. Use only lowercase Latin symbols (a-z) and numbers (0-9). No spaces are allowed - use underscore.', 'essb'), true, true, essb_array_value('network_id', $network_setup));
essb5_draw_input_option('name', esc_html__('Name', 'essb'), esc_html__('The name of the button. This is the name you will see in the list of social networks for selection.', 'essb'), true, true, essb_array_value('name', $network_setup));
essb5_draw_editor_option('icon', esc_html__('SVG Icon', 'essb'), esc_html__('Place the content of the SVG icon you wish to use with this button. Use single color flat SVG files.', 'essb'), 'htmlmixed', true, essb_array_value('icon', $network_setup));
essb5_draw_color_option('accent_color', esc_html__('Color', 'essb'), '', false, true, essb_array_value('accent_color', $network_setup));