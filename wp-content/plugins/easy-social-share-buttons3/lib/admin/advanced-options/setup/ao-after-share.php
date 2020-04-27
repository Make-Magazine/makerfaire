<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');
essb5_draw_heading( esc_html__('Pop up message width', 'essb'), '5');
essb5_draw_input_option('afterclose_popup_width', esc_html__('Pop up message width', 'essb'), esc_html__('Provide custom width in pixels for pop up window (number value with px in it. Example: 400). Default pop up width is 400.', 'essb'));
essb5_draw_heading( esc_html__('Single time message appearance', 'essb'), '5');
essb5_draw_switch_option('afterclose_singledisplay', esc_html__('Display pop up message once for selected time', 'essb'), esc_html__('Activate this option to prevent pop up window display on every page load. This option will make it display once for selected period of days.', 'essb'));
essb5_draw_input_option('afterclose_singledisplay_days', esc_html__('Days between pop up message display', 'essb'), esc_html__('Provide the value of days when pop up message will appear again. Leave blank for default value of 7 days.', 'essb'));

essb5_draw_heading( esc_html__('Component static resources load', 'essb'), '5');
essb5_draw_switch_option('afterclose_deactive_mobile', esc_html__('Do not display after social share action for mobile devices', 'essb'), esc_html__('Avoid display after share actions on mobile devices', 'essb'));
essb5_draw_switch_option('afterclose_activate_all', esc_html__('Include after share actions code on all pages', 'essb'), esc_html__('Activate this option if you plan to use after share actions on post types or pages where buttons are not assigned to appear automatically. This option usually is required when you use shortcodes to display buttons on specific parts of site (for example embed into theme and avoid automatic display)', 'essb'), '', esc_html__('Yes', 'essb'), esc_html__('No', 'essb'));
essb5_draw_switch_option('afterclose_deactive_sharedisable', esc_html__('Do not include after share actions code on pages where buttons are deactivated', 'essb'), esc_html__('Activate this option if you do not wish code for after share module to be added on pages where buttons are set to be off into settings (via on post/page options or from Display Settings).', 'essb'));
essb5_draw_switch_option('afterclose_activate_sharedisable', esc_html__('Always load after share code', 'essb'), esc_html__('Always load code of after share on each page.', 'essb'), esc_html__('Really usefull option when you plan to use after on pages where buttons for sharing are deactivated.', 'essb'));

essb_advancedopts_section_close();