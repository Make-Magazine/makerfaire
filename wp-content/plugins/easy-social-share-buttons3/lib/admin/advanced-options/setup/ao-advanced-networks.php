<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb5_draw_hint('', esc_html__('Using the manage networks function you can easily select the only networks that will appear on your site. All other networks will remain hidden and they will not appear anywhere. You can change the active networks or deactivate the feature and use all networks again at any time.', 'essb'), '', 'glow');
essb5_draw_switch_option('activate_networks_manage', esc_html__('Make plugin use only selected networks', 'essb'), esc_html__('Use this feature in case you does not need all networks of plugin. That makes easy to manage and easy to personalize. Also that can save resources on a share counter update because it will refer to active networks only', 'essb'));
essb5_draw_heading(esc_html__('Select Active Only Networks', 'essb'), '5');

$all_networks = essb_available_social_networks(true);
$current_setup = essb_option_value('functions_networks');
if (!is_array($current_setup)) {
	$current_setup = array();
}

echo '<ul class="essb-component-networkselect">';

foreach ($all_networks as $social => $data) {
	$name = isset($data['name']) ? $data['name'] : $social;
	$is_checked = in_array($social, $current_setup);
	
	echo '<li class="essb-admin-networkselect-single essb-network-color-'.$social.' ao-networkselect-single-small">';
	echo '<span class="essb-sns-activate"><input type="checkbox" id="essb-sns-'.$social.'" name="essb_options[functions_networks][]" value="'.$social.'" '.($is_checked ? 'checked="checked"' : '').'></span>';
	echo '<span class="essb_icon essb_icon_'.$social.'"></span>';
	echo '<span class="essb-sns-name">'.$name.'</span>';
	echo '</li>';
}

echo '</ul>';
?>


