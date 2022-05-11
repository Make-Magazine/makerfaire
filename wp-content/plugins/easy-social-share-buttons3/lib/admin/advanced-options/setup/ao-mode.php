<?php 

$share_levels = array();

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

?>

<div class="essb-options-hint essb-options-hint-glowhint">
	<div class="essb-options-hint-desc">
	The modes automatically enable or disable features to achieve the level you choose. The entry-level called "Simple" contains just the basic options (those offered by any other social media plugins). The mode doesn't stop from manually enable/disable different modes from the features button. In case you are not aware of your need recommended level is "Simple".
		<div>
			<br/>
			<a href="#" target="_blank" class="ao-external-link">Learn more<i class="fa fa-external-link"></i></a>
		</div> 
	</div>
</div>

<div class="ao-advanced-modes mode-selection">
	<div class="essb-flex-grid-c c12 essb-heading sub7 svg-icon">
		<span class="icon"><svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M352 320c-25.6 0-48.9 10-66.1 26.4l-98.3-61.5c5.9-18.8 5.9-39.1 0-57.8l98.3-61.5C303.1 182 326.4 192 352 192c53 0 96-43 96-96S405 0 352 0s-96 43-96 96c0 9.8 1.5 19.6 4.4 28.9l-98.3 61.5C144.9 170 121.6 160 96 160c-53 0-96 43-96 96s43 96 96 96c25.6 0 48.9-10 66.1-26.4l98.3 61.5c-2.9 9.4-4.4 19.1-4.4 28.9 0 53 43 96 96 96s96-43 96-96-43-96-96-96zm0-272c26.5 0 48 21.5 48 48s-21.5 48-48 48-48-21.5-48-48 21.5-48 48-48zM96 304c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm256 160c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48z" class=""></path></svg></span>
		<div><em>Social Share Features</em></div>
	</div>

	<div class="essb-section-holder essb-related-heading7">
	
	<?php 
	$share_value = essb_sanitize_option_value('functions_mode_sharing');
	
	$select_values = array(
	    '' => array('title' => 'Custom', 'content' => '<i class="ti-check-box"></i><span class="title">Custom</span>', 'isText' => true),
	    'simple' => array('title' => 'Simple', 'content' => '<i class="ti-check-box"></i><span class="title">Simple (Light & Fast)</span>', 'isText' => true),
	    'medium' => array('title' => 'Medium', 'content' => '<i class="ti-check-box"></i><span class="title">Medium</span>', 'isText' => true),
	    'advanced' => array('title' => 'Advanced', 'content' => '<i class="ti-check-box"></i><span class="title">Advanced</span>', 'isText' => true),
	    'full' => array('title' => 'Full', 'content' => '<i class="ti-check-box"></i><span class="title">Full</span>', 'isText' => true),
	);
	
	essb_component_options_group_select('functions_mode_sharing', $select_values, '', $share_value);
	?>
	
	</div>
	
	<div class="essb-flex-grid-c c12 essb-heading sub7 svg-icon">
		<span class="icon"><svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512"><path fill="currentColor" d="M248 8C111 8 0 119 0 256s111 248 248 248 248-111 248-248S385 8 248 8zm0 464c-119.1 0-216-96.9-216-216S128.9 40 248 40s216 96.9 216 216-96.9 216-216 216zm116-318.4c-41.9-36.3-89.5-8.4-104.9 7.7L248 172.9l-11.1-11.6c-26.6-27.9-72.5-35.9-104.9-7.7-35.3 30.6-37.2 85.6-5.6 118.7l108.9 114.1c7 7.4 18.4 7.4 25.5 0l108.9-114.1c31.5-33.2 29.7-88.1-5.7-118.7zm-17 96.5l-99 103.8-99-103.8c-16.7-17.5-20.4-51.6 3.4-72.1 22.2-19.3 50-6.8 61.9 5.7L248 219l33.7-35.3c8.7-9.2 37.5-26.8 61.9-5.7 23.8 20.5 20.1 54.5 3.4 72.1z" class=""></path></svg></span>
		<div><em>Other Social Features</em></div>
	</div>

	<div class="essb-section-holder essb-related-heading7">
	
	<?php 
	$other_value = essb_sanitize_option_value('functions_mode_other');
	
	$select_values = array(
	    '' => array('title' => 'Custom', 'content' => '<i class="ti-check-box"></i><span class="title">Custom</span>', 'isText' => true),
	    'no' => array('title' => 'Without other social media features', 'content' => '<i class="ti-check-box"></i><span class="title">Without other social media features</span>', 'isText' => true),
	    'simple' => array('title' => 'Simple', 'content' => '<i class="ti-check-box"></i><span class="title">Simple</span>', 'isText' => true),
	    'medium' => array('title' => 'Medium', 'content' => '<i class="ti-check-box"></i><span class="title">Medium</span>', 'isText' => true),
	    'advanced' => array('title' => 'Advanced', 'content' => '<i class="ti-check-box"></i><span class="title">Advanced</span>', 'isText' => true),
	    'full' => array('title' => 'Full', 'content' => '<i class="ti-check-box"></i><span class="title">Full</span>', 'isText' => true),
	);
	
	essb_component_options_group_select('functions_mode_other', $select_values, '', $other_value);
	?>
	
	</div>
	
	<div class="essb-flex-grid-c c12 essb-heading sub7 svg-icon">
		<span class="icon"><svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M296 160H180.6l42.6-129.8C227.2 15 215.7 0 200 0H56C44 0 33.8 8.9 32.2 20.8l-32 240C-1.7 275.2 9.5 288 24 288h118.7L96.6 482.5c-3.6 15.2 8 29.5 23.3 29.5 8.3 0 16.4-4.4 20.8-12l176-304c9.3-15.9-2.2-36-20.7-36zM140.3 436.9l33.5-141.6 9.3-39.4h-150L63 32h125.9l-38.7 118-13.8 42h145.7L140.3 436.9z" class=""></path></svg></span>
		<div><em>Optimization</em></div>
	</div>

	<div class="essb-section-holder essb-related-heading7">
	
	<?php 
	$other_value = essb_sanitize_option_value('functions_mode_optimize');
	
	$select_values = array(
	    '' => array('title' => 'Custom', 'content' => '<i class="ti-check-box"></i><span class="title">Custom</span>', 'isText' => true),
	    'level1' => array('title' => 'Cache/optimization plugin is running on the website', 'content' => '<i class="ti-check-box"></i><span class="title">Cache/optimization plugin is running on the website</span>', 'isText' => true),
	    'level2' => array('title' => 'No cache or optimization plugin is installed', 'content' => '<i class="ti-check-box"></i><span class="title">No cache or optimization plugin is installed</span>', 'isText' => true),
	);
	
	essb_component_options_group_select('functions_mode_optimize', $select_values, '', $other_value);
	?>
	
	</div>	
</div>


<?php 

essb_advancedopts_section_close();

?>