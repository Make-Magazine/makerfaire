<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_switch_option('use_stylebuilder', esc_html__('Use only selected styles', 'essb'), '');

$current_selection = essb_option_value('stylebuilder_css');
if (!is_array($current_selection)) {
	$current_selection = array();
}

$styles = essb_stylebuilder_css_files();

foreach ($styles as $key => $data) {
	echo '<div class="row ao-style-row"><span class="essb_checkbox_list_item"><input type="checkbox" name="essb_options[stylebuilder_css][]" id="stylebuilder-'.$key.'" class="stylebuilder-key" value="'.$key.'" '.(in_array($key, $current_selection) ? 'checked="checked"' : '').'/> '.($data['default'] == 'true' ? '<b>' : '').$data['name'].($data['default'] == 'true' ? '</b>' : '').'</span></div>';
}

echo '<div><b>'.esc_html__('If you wish to load only your own styles than leave all checkboxes be off. Once you do this you can add inside plugin settings or in theme just the code you need.', 'essb').'</b></div>';

essb_advancedopts_section_close();