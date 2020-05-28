<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_heading(esc_html__('Deactivated display methods', 'essb'), '5', esc_html__('Display methods with Yes are deactivated and you will not be able to select them from the list. This makes easy to operate just with the positions you need. If you wish to add a position that is deactivated just change to No.', 'essb'));
essb5_draw_switch_option('deactivate_method_float', esc_html__('Float from Above The Content', 'essb'), '');
essb5_draw_switch_option('deactivate_method_postfloat', esc_html__('Post Vertical Float', 'essb'), '');
essb5_draw_switch_option('deactivate_method_sidebar', esc_html__('Sidebar', 'essb'), '');
essb5_draw_switch_option('deactivate_method_topbar', esc_html__('Top Bar', 'essb'), '');
essb5_draw_switch_option('deactivate_method_bottombar', esc_html__('Bottom Bar', 'essb'), '');
essb5_draw_switch_option('deactivate_method_popup', esc_html__('Pop Up', 'essb'), '');
essb5_draw_switch_option('deactivate_method_flyin', esc_html__('Fly In', 'essb'), '');
essb5_draw_switch_option('deactivate_method_heroshare', esc_html__('Full Screen Hero Share', 'essb'), '');
essb5_draw_switch_option('deactivate_method_postbar', esc_html__('Post Bar', 'essb'), '');
essb5_draw_switch_option('deactivate_method_point', esc_html__('Point', 'essb'), '');
essb5_draw_switch_option('deactivate_method_image', esc_html__('On Media', 'essb'), '');
essb5_draw_switch_option('deactivate_method_native', esc_html__('Native Social Button Display Methods', 'essb'), '');
essb5_draw_switch_option('deactivate_method_followme', esc_html__('Follow Me Bar', 'essb'), '');
essb5_draw_switch_option('deactivate_method_corner', esc_html__('Corner Bar', 'essb'), '');
essb5_draw_switch_option('deactivate_method_booster', esc_html__('Share Booster', 'essb'), '');
essb5_draw_switch_option('deactivate_method_sharebutton', esc_html__('Share Button', 'essb'), '');
essb5_draw_switch_option('deactivate_method_integrations', esc_html__('Integrations with Other Plugins', 'essb'), '');

essb_advancedopts_section_close();