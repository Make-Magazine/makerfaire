<?php
?>

<?php 

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

/**
 * Mobile Setup
 */

ESSBOptionsFramework::draw_hint(__('Mobile Device Detection & Setup', 'essb'), __('Easy Social Share Buttons for WordPress has advanced mobile display options to get a fully personalized mobile appearance. If you do not wish to do this by yourself you can set automatic configuration and plugin will do the job for you.', 'essb'), 'fa21 ti-mobile', 'glow');

$value = essb_option_value('functions_mode_mobile');

$select_values = array('' => array('title' => 'Manual Setup', 'content' => '<i class="ti-panel" style="margin-right: 5px;"></i> <span class="title">Manual Setup</span><span class="desc">Allows full control over share buttons on mobile device</span>', 'isText'=>true),
		'auto' => array('title' => 'Plugin will automatically setup share buttons', 'content' => '<i class="ti-star" style="margin-right: 5px;"></i><span class="title">Automatic Setup</span><span class="desc">Plugin will automatically setup share buttons for mobile</span>', 'isText'=>true),
		'deactivate' => array('title' => 'Deactivate mobile settings and do not show buttons on mobile devices', 'content' => '<i class="ti-close" style="margin-right: 5px;"></i><span class="title">Deactivate on Mobile</span><span class="desc">Plugin will not show buttons on mobile devices</span>', 'isText'=>true));

essb_component_options_group_select('functions_mode_mobile', $select_values, '', $value);

/**
 * Function Modes
 */

echo '<div style="margin-top: 15px;">&nbsp;</div>';

ESSBOptionsFramework::draw_hint(__('Plugin Mode', 'essb'), __('Easy Social Share Buttons for WordPress is all-in-one social media plugin for WordPress packed with lot of options. In case you do not plan to use all of them it is easy to deactivate (or activate back) functions you do not need. To do this use one of preset operation modes or set on custom and remove functions you do not use.', 'essb'), 'fa21 ti-layout-slider-alt', 'glow');

$value = essb_option_value('functions_mode');

$select_values = array('' => array('title' => 'Customized setup of used modules', 'content' => '<i class="ti-panel" style="margin-right: 5px;"></i> <span class="title">Custom</span>', 'isText'=>true),
		'light' => array('title' => 'Light sharing with only most popular share functions', 'content' => '<i class="ti-star" style="margin-right: 5px;"></i><span class="title">Lite Share Buttons</span>', 'isText'=>true),
		'medium' => array('title' => 'Extended share functionality', 'content' => '<i class="ti-sharethis-alt" style="margin-right: 5px;"></i> <span class="title">Medium Sharing & Subscribe</span>', 'isText'=>true),
		'advanced' => array('title' => 'Power social sharing', 'content' => '<i class="ti-new-window" style="margin-right: 5px;"></i><span class="title">Advanced Sharing & Subscribe</span>', 'isText'=>true),
		'sharefollow' => array('title' => 'All the best to share your content and grow your followers', 'content' => '<i class="ti-heart" style="margin-right: 5px;"></i><span class="title">Sharing, Subscribe & Following</span>', 'isText'=>true),
		'full' => array('title' => 'All plugin functions', 'content' => '<i class="ti-package" style="margin-right: 5px;"></i><span class="title">Everything</span>', 'isText'=>true));

essb_component_options_group_select('functions_mode', $select_values, '', $value);

include_once(ESSB3_PLUGIN_ROOT.'lib/admin/helpers/mode-information.php');

echo '<script type="text/javascript">
jQuery(document).ready(function($){
	$(".essb-button-openmodes").hide();
	$(".essb-feature-mode").removeClass("closed");
});
</script>
';	
?>