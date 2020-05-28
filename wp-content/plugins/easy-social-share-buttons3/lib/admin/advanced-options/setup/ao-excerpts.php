<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');
essb5_draw_switch_option('display_excerpt', esc_html__('Activate Excerpt Display', 'essb'), '');
$listOfOptions = array("top" => "Before excerpt", "bottom" => "After excerpt");
essb5_draw_select_option('display_excerpt_pos', esc_html__('Buttons position in excerpt', 'essb'), '', $listOfOptions);

essb_advancedopts_section_close();
