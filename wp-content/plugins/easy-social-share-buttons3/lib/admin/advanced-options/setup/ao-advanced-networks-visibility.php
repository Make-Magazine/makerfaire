<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb5_draw_hint('', esc_html__('With the device visibility, you can choose the networks that will be visible on the desktop and those that will be visible just on mobile. You can also deactivate the unused social networks using the "Manage Available & Install Networks" button.', 'essb'), '', 'glow');
essb5_draw_switch_option('activate_networks_responsive', esc_html__('Activate network device visibility control', 'essb'), '', '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));

$all_networks = essb_available_social_networks();

echo '<div class="essb-flex-grid-r">';
echo '<div class="essb-flex-grid-c c8 bold"><span class="title">Network</span></div>';
echo '<div class="essb-flex-grid-c c4 bold"><span class="title">Hidden On</span></div>';
echo '</div>';

foreach ($all_networks as $social => $data) {
	$name = isset($data['name']) ? $data['name'] : $social;
	$field_id= 'responsive_'.$social;
	$value_d = essb_option_value($field_id.'_desktop');
	$value_m = essb_option_value($field_id.'_mobile');
	
	echo '<div class="essb-flex-grid-r float-switch">';
	echo '<div class="essb-flex-grid-c c8 bold">';
	echo '<div class="essb-admin-networkselect-single essb-network-color-'.$social.' ao-networkselect-single">';
	echo '<span class="essb_icon essb_icon_'.$social.'"></span>';
	echo '<span class="essb-sns-name">'.$name.'</span>';
	echo '</div>';
	echo '</div>';
	echo '<div class="essb-flex-grid-c c4 bold">';
	echo '<div class="essb-flex-grid-c c5">';
	echo '<i class="ti-desktop"></i>';
	ESSBOptionsFramework::draw_switch_field($field_id.'_desktop', 'essb_options', $value_d);
	echo '</div>';
	
	echo '<div class="essb-flex-grid-c c5">';
	echo '<i class="ti-mobile"></i>';
	ESSBOptionsFramework::draw_switch_field($field_id.'_mobile', 'essb_options', $value_m);
	echo '</div>';
	echo '</div>';
	echo '</div>';
}

echo '</ul>';
?>


