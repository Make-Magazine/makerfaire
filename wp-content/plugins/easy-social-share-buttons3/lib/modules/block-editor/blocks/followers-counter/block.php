<?php
/**
 * Generate Social Followers block
 */

/**
 * Register the Subscribe block
 */
function essb_block_register_social_followers() {
	$block_path = 'block.js';
	wp_register_script(
		'essb-social-followers-block',
		plugins_url( $block_path, __FILE__ ),
		[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' ],
		'1.0'
	);

	$block_options = array();

	if (!class_exists('ESSBSocialFollowersCounterHelper')) {
		include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-helper.php');
	}

	$default_shortcode_setup = ESSBSocialFollowersCounterHelper::default_instance_settings();
	$shortcode_settings = ESSBSocialFollowersCounterHelper::default_options_structure(true, $default_shortcode_setup);

	$general_options = array('total_type', 'columns', 'template', 'animation');

	foreach ($shortcode_settings as $field => $options) {
		if (!in_array($field, $general_options)) {
			continue;
		}

		$block_options[$field] = $options;
	}

	wp_localize_script('essb-social-followers-block', 'essb_block_followers', $block_options);


	register_block_type( 'essb/essb-socialfollowers', array(

		/** Define the attributes used in your block */

		'attributes'      => array(
			'template'         => array(
				'type' => 'string'
			),
			'animation' => array(
				'type' => 'string'
			),
			'columns' => array(
				'type' => 'string'
			),
			'bgcolor' => array(
				'type' => 'string'
			),
			'total_type' => array(
				'type' => 'string'
			),
			'nospace' => array(
				'type' => 'boolean'
			),
			'hide_value' => array(
				'type' => 'boolean'
			),
			'hide_text' => array(
				'type' => 'boolean'
			),
			'show_total' => array(
				'type' => 'boolean'
			)
		),

		/** Define the category for your block */
		'category'        => 'widgets',

		/** The script name we gave in the wp_register_script() call */
		'editor_script'   => 'essb-social-followers-block',

		/** The callback called by the javascript file to render the block */
		'render_callback' => 'essb_block_render_social_followers',
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
function essb_block_render_social_followers( $attributes ) {
	/** @var  $is_in_edit_mode  Check if we are in the editor */
	$is_in_edit_mode = strrpos( $_SERVER['REQUEST_URI'], "context=edit" );

	$attributes['preview_mode'] = $is_in_edit_mode;
	$attributes['hide_title'] = 1;
	$attributes['new_window'] = 1;

	if (!class_exists('\ESSBSocialFollowersCounterHelper')) {
		include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-helper.php');
	}

	if (!class_exists('\ESSBSocialFollowersCounterDraw')) {
		include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-draw.php');
	}

	if (!class_exists('\ESSBSocialFollowersCounter')) {
		include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter.php');
	}

	$default_options = ESSBSocialFollowersCounterHelper::default_instance_settings();
	$attrs = shortcode_atts( $default_options , $attributes );

	$attrs['preview_mode'] = $is_in_edit_mode;

	ob_start();
	ESSBSocialFollowersCounterDraw::draw_followers($attrs, true);
	$html = ob_get_contents();
	ob_end_clean();

	return $html;
}