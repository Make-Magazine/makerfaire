<?php
/**
 * Activating all features connected with custom templates builder
 */

add_action ( 'init', 'essb_mytemplatebuilder_init', 9 );
function essb_mytemplatebuilder_init() {
	if (essb_option_bool_value('mytemplate_activate')) {
		add_filter('essb4_templates', 'essb_mytemplatebuilder_initialze');
	}
}


function essb_mytemplatebuilder_initialze($templates = array()) {
	$templates['998'] = __('User Template', 'essb');
	
	return $templates;
}

add_action('admin_head', 'essb_mytemplatebuilder_custom');

function essb_mytemplatebuilder_custom() {
	echo '<style type="text/css">
	.essb-mytemplate-network { padding: 20px; }
}
</style>';
}

add_action('essb_after_admin_save_settings', 'essb_mytemplatebuilder_generate_custom_styles');

function essb_mytemplatebuilder_generate_custom_styles() {
	$upload_dir = wp_upload_dir ();
		
	$base_path = $upload_dir ['basedir'] . '/essb-cache/';
	$base_url = $upload_dir['baseurl'] . '/essb-cache/';

	if (! is_dir ( $base_path )) {
		if (! wp_mkdir_p ( $base_path, 0777 )) {

			return false;
		}

	}
	
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/template-builder/functions-template-helper.php');

	$css = essb_mytemplatebuilder_generate_css();

	$filename = $base_path . 'essb-template-builder.css';

	if (! file_put_contents ( $filename, $css )) {
		return false;
	}
}

add_filter('essb4_templates_class', 'essb_mytemplatebuilder_class', 10, 2);

function essb_mytemplatebuilder_class($class, $template_id) {
	if ($template_id == '998') {
		$class = 'usercustom';
	}

	return $class;
}

add_action('plugins_loaded', 'essb_mytemplatebuilder_initialize_styles', 999);
add_action ('admin_enqueue_scripts', 'essb_mytemplatebuilder_initialize_styles_admin', 999 );

function essb_mytemplatebuilder_initialize_styles() {

	if (essb_option_bool_value('mytemplate_activate')) {
		$upload_dir = wp_upload_dir ();
	
		$base_path = $upload_dir ['basedir'] . '/essb-cache/';
		$base_url = $upload_dir['baseurl'] . '/essb-cache/';
	
		if (function_exists('essb_resource_builder')) {
			essb_resource_builder()->add_static_resource($base_url . '/essb-template-builder.css', 'essb-mytemplate', 'css');
		}
	}

}
function essb_mytemplatebuilder_initialize_styles_admin() {
	if (essb_option_bool_value('mytemplate_activate')) {
		$upload_dir = wp_upload_dir ();
	
		$base_path = $upload_dir ['basedir'] . '/essb-cache/';
		$base_url = $upload_dir['baseurl'] . '/essb-cache/';
	
		wp_register_style ( 'essb-mytemplate-admin', $base_url . '/essb-template-builder.css');
		wp_enqueue_style ( 'essb-mytemplate-admin' );
	}
}