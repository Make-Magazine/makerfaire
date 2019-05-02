<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb5_draw_heading(__('Mobile Appearance', 'essb'), '5', __('Use those options to deactivate plugin on mobile', 'essb'));
essb5_draw_switch_option('deactivate_mobile', __('Deactivate plugin on mobile', 'essb'), __('Use this option to completely deactivate plugin usage on mobile devices inlcuding all of its modules.', 'essb'));
essb5_draw_switch_option('deactivate_mobile_share', __('Deactivate social share buttons on mobile', 'essb'), __('This option will deactivate share function on mobile devices but it will keep up showing all other modules.', 'essb'));

essb5_draw_heading(__('Turn Off Display Methods', 'essb'), '5', __('Set to Yes option for the positions you does not wish to use on your site. This will deactivate the marked positions from the settings screen and code.', 'essb'));
essb5_draw_switch_option('deactivate_method_float', __('Turn off Float from Top', 'essb'), __('', 'essb'));
essb5_draw_switch_option('deactivate_method_postfloat', __('Turn off Post Vertical Float', 'essb'), __('', 'essb'));
essb5_draw_switch_option('deactivate_method_sidebar', __('Turn off Sidebar', 'essb'), __('', 'essb'));
essb5_draw_switch_option('deactivate_method_topbar', __('Turn off Top Bar', 'essb'), __('', 'essb'));
essb5_draw_switch_option('deactivate_method_bottombar', __('Turn off Bottom Bar', 'essb'), __('', 'essb'));
essb5_draw_switch_option('deactivate_method_popup', __('Turn off Pop Up', 'essb'), __('', 'essb'));
essb5_draw_switch_option('deactivate_method_flyin', __('Turn off Fly In', 'essb'), __('', 'essb'));
essb5_draw_switch_option('deactivate_method_heroshare', __('Turn off Full Screen Hero Share', 'essb'), __('', 'essb'));
essb5_draw_switch_option('deactivate_method_postbar', __('Turn off Post Bar', 'essb'), __('', 'essb'));
essb5_draw_switch_option('deactivate_method_point', __('Turn off Point', 'essb'), __('', 'essb'));
essb5_draw_switch_option('deactivate_method_image', __('Turn off On Media', 'essb'), __('', 'essb'));
essb5_draw_switch_option('deactivate_method_native', __('Turn off Methods with native buttons', 'essb'), __('', 'essb'));
essb5_draw_switch_option('deactivate_method_followme', __('Turn off Follow me bar', 'essb'), __('', 'essb'));
essb5_draw_switch_option('deactivate_method_corner', __('Turn off Corner Bar', 'essb'), __('', 'essb'));
essb5_draw_switch_option('deactivate_method_booster', __('Turn off Share Booster', 'essb'), __('', 'essb'));
essb5_draw_switch_option('deactivate_method_sharebutton', __('Turn off Share Button', 'essb'), __('', 'essb'));
essb5_draw_switch_option('deactivate_method_integrations', __('Turn off Plugin Integrations', 'essb'), __('', 'essb'));
