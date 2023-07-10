<?php
/**
 * Generate Social Profiles block
 */

/**
 * Register the Subscribe block
 */
function essb_block_register_social_profiles() {
	$block_path = 'block.js';
	wp_register_script(
		'essb-social-profiles-block',
		plugins_url( $block_path, __FILE__ ),
		[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' ],
		'1.0'
	);

	$block_options = array();

	if (!function_exists('essb_get_shortcode_options_easy_profiles')) {
		include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/settings/shortcode-options/easy-profiles.php');
	}

	$general_options = array('template', 'animation', 'align', 'size', 'nospace', 'columns', 'cta', 'cta_vertical');
	$shortcode_settings = essb_get_shortcode_options_easy_profiles();

	foreach ($shortcode_settings as $field => $options) {
		if (!in_array($field, $general_options)) {
			continue;
		}

		$block_options[$field] = $options;
	}

	wp_localize_script('essb-social-profiles-block', 'essb_block_profiles', $block_options);


	register_block_type( 'essb/essb-socialprofiles', array(

		/** Define the attributes used in your block */

		'attributes'      => array(
			'template'         => array(
				'type' => 'string'
			),
			'animation' => array(
				'type' => 'string'
			),
			'align' => array(
				'type' => 'string'
			),
			'size' => array(
				'type' => 'string'
			),
			'columns' => array(
				'type' => 'string'
			),
			'nospace' => array(
				'type' => 'boolean'
			),
			'cta' => array(
				'type' => 'boolean'
			),
			'cta_vertical' => array(
				'type' => 'boolean'
			),
		    'cta_number' => array( 'type' => 'boolean')
		),

		/** Define the category for your block */
		'category'        => 'widgets',

		/** The script name we gave in the wp_register_script() call */
		'editor_script'   => 'essb-social-profiles-block',

		/** The callback called by the javascript file to render the block */
		'render_callback' => 'essb_block_render_social_profiles',
	) );
}

/**
 * Render social profiles block
 * @param $attributes
 * @return string
 *
 * @author appscreo
 * @package EasySocialShareButtons
 * @since 8.0
 */
function essb_block_render_social_profiles( $attributes ) {
	/** @var  $is_in_edit_mode  Check if we are in the editor */
	$is_in_edit_mode = strrpos( $_SERVER['REQUEST_URI'], "context=edit" );

	$attributes['preview_mode'] = $is_in_edit_mode;

	essb_depend_load_class('\ESSBCoreExtenderShortcodeProfiles', 'lib/core/extenders/essb-core-extender-shortcode-profiles.php');
	return ESSBCoreExtenderShortcodeProfiles::parse_shortcode($attributes, ESSB_Plugin_Options::read_all());
}