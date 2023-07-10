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

$button = '<br/><span class="ao-new-subscribe-design ao-import-svg-icon" data-for="essb_options_icon" data-picker="ao-svg-file"><span class="essb_icon fa fa-upload"></span><span>Upload</span></span>';
$button .= '<input type="file" name="ao-svg-file" id="ao-svg-file" class="ao-hidden" accept=".svg"/>';

essb5_draw_input_option('name', esc_html__('Name', 'essb'), esc_html__('The name of the social network you will see in the list (ex: Facebook)', 'essb'), true, true, essb_array_value('name', $network_setup));
essb5_draw_input_option('network_id', esc_html__('Network ID', 'essb'), esc_html__('Unique network ID. Only lower case Latin symbols (a-z), numbers (0-9), and underscore (_) - example: facebook, my_network', 'essb'), true, true, essb_array_value('network_id', $network_setup));
essb5_draw_editor_option('icon', esc_html__('SVG Icon', 'essb'), esc_html__('Use the upload button to select an SVG icon or manually put the SVG content in the text field. Use a single-color SVG file to ensure icons will render correctly with all templates.', 'essb') . $button, 'htmlmixed', true, essb_array_value('icon', $network_setup));


essb5_draw_color_option('accent_color', esc_html__('Color', 'essb'), '', false, true, essb_array_value('accent_color', $network_setup));