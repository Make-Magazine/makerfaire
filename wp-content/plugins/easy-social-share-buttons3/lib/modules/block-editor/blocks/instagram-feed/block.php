<?php
/**
 * Generate Instagram Feed block
 */

/**
 * Register the Subscribe block
 */
function essb_block_register_instagram_feed() {
	$block_path = 'block.js';
	wp_register_script(
		'essb-instagram-feed-block',
		plugins_url( $block_path, __FILE__ ),
		[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' ],
		'1.0'
	);

	$block_options = array();
	$shortcode_settings = array();

	if (function_exists('essb_instagram_feed')) {
		$shortcode_settings = essb_instagram_feed()->get_settings();
	}

	foreach ($shortcode_settings as $field => $options) {
		if ($options['type'] != 'select') {
			continue;
		}

		$block_options[$field] = $options['options'];
	}

	wp_localize_script('essb-instagram-feed-block', 'essb_block_instagram', $block_options);


	register_block_type( 'essb/essb-instagram', array(

		/** Define the attributes used in your block */

		'attributes' => array(
			'username' => array(
				'type' => 'string',
				'default' => ''
			),
			'type' => array(
				'type' => 'string'
			),
			'show' => array(
				'type' => 'string'
			),
			'profile' => array(
				'type' => 'string'
			),
			'followbtn' => array(
				'type' => 'string'
			),
			'profile_size' => array(
				'type' => 'string'
			),
			'space' => array(
				'type' => 'string'
			),
			'masonry' => array(
				'type' => 'string'
			),
		),

		/** Define the category for your block */
		'category'        => 'widgets',

		/** The script name we gave in the wp_register_script() call */
		'editor_script'   => 'essb-instagram-feed-block',

		/** The callback called by the javascript file to render the block */
		'render_callback' => 'essb_block_render_instagram_feed',
	) );
}

/**
 * Render the Instagram Feed
 * @param $attributes
 * @return string
 *
 * @author appscreo
 * @package EasySocialShareButtons
 * @since 8.0
 */
function essb_block_render_instagram_feed( $attributes ) {
	/** @var  $is_in_edit_mode  Check if we are in the editor */
	$is_in_edit_mode = strrpos( $_SERVER['REQUEST_URI'], "context=edit" );

	$attributes['preview_mode'] = $is_in_edit_mode ? 'true' : '';

	if (function_exists('essb_instagram_feed')) {
		return essb_instagram_feed()->generate_shortcode($attributes);
	}
	else {
		return 'The Instagram module is deactivated';
	}
}