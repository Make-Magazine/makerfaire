<?php
?>

<?php 

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_help('', esc_html__('The different modes are designed to switch on or off a list of features. You can manually adjust each of the modes from Manage Plugin Features at any time. The modes will save time for that as we group the popular options.', 'essb'));

$value = essb_option_value('functions_mode');

$select_values = array('' => array('title' => 'Customized setup of used modules', 'content' => '<i class="ti-panel"></i> <span class="title">Custom</span>', 'isText'=>true),
		'light' => array('title' => 'Light sharing with only most popular share functions', 'content' => '<i class="ti-star"></i><span class="title">Easy Sharing (Basic)</span>', 'isText'=>true),
		'light_image' => array('title' => 'Light sharing with only most popular share functions', 'content' => '<i class="ti-star"></i><span class="title">Light Sharing Features</span>', 'isText'=>true),
		'medium' => array('title' => 'Extended share functionality', 'content' => '<i class="ti-star"></i> <span class="title">Medium Sharing & Subscribe</span>', 'isText'=>true),
		'advanced' => array('title' => 'Power social sharing', 'content' => '<i class="ti-star"></i><span class="title">Advanced Sharing & Subscribe</span>', 'isText'=>true),
		'sharefollow' => array('title' => 'All the best to share your content and grow your followers', 'content' => '<i class="ti-star"></i><span class="title">Sharing, Subscribe & Following</span>', 'isText'=>true),
		'full' => array('title' => 'All plugin functions', 'content' => '<i class="ti-package"></i><span class="title">Everything</span>', 'isText'=>true));

essb_component_options_group_select('functions_mode', $select_values, '', $value);

essb_advancedopts_section_close();

?>