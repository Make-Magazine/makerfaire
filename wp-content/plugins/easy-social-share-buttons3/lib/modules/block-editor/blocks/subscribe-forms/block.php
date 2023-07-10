<?php
/**
 * Generate Subscribe form block
 */

/**
 * Register the Subscribe block
 */
function essb_block_register_subscribe() {
	$block_path = 'block.js';
	wp_register_script(
		'essb-subscribe-block',
		plugins_url( $block_path, __FILE__ ),
		[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' ],
		'1.0'
	);

	wp_localize_script('essb-subscribe-block', 'essb_block_subscribe_designs', essb_optin_designs());

	register_block_type( 'essb/essb-subscribe', array(

		/** Define the attributes used in your block */

		'attributes'      => array(
			'template'         => array(
				'type' => 'string'
			)
		),

		/** Define the category for your block */
		'category'        => 'widgets',

		/** The script name we gave in the wp_register_script() call */
		'editor_script'   => 'essb-subscribe-block',

		/** The callback called by the javascript file to render the block */
		'render_callback' => 'essb_block_render_subscribe',
	) );
}

/**
 * Render Subscribe block
 * @param $attributes
 * @return string
 *
 * @author appscreo
 * @package EasySocialShareButtons
 * @since 8.0
 */
function essb_block_render_subscribe( $attributes ) {
	/** @var  $is_in_edit_mode  Check if we are in the editor */
	$is_in_edit_mode = strrpos( $_SERVER['REQUEST_URI'], "context=edit" );
	
	if (strrpos( $_SERVER['REQUEST_URI'], "action=edit" )) $is_in_edit_mode = true;

	$template = isset($attributes['template']) ? $attributes['template'] : '';

	if (!class_exists('ESSBNetworks_Subscribe')) {
		include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-subscribe.php');
	}
	return ESSBNetworks_Subscribe::draw_inline_subscribe_form('inline', $template, false, 'block_widget');
}