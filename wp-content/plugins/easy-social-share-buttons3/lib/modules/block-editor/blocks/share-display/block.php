<?php
/**
 * Generate Share Display
 */

/**
 * Register the Subscribe block
 */
function essb_block_register_social_share_display() {
	$block_path = 'block.js';
	wp_register_script(
		'essb-share-display-block',
		plugins_url( $block_path, __FILE__ ),
		[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' ],
		'1.0'
	);

	if (function_exists('essb5_get_custom_positions')) {
	   wp_localize_script('essb-share-display-block', 'essb_block_share_display', essb5_get_custom_positions());
	}
	else {
	    wp_localize_script('essb-share-display-block', 'essb_block_share_display', array());
	}

	register_block_type( 'essb/essb-share-display', array(

		/** Define the attributes used in your block */

		'attributes'      => array(
			'display'         => array(
				'type' => 'string'
			),
			'force' => array(
				'type' => 'boolean'
			),
			'custom_share' => array(
				'type' => 'boolean'
			),
			'custom_share_url' => array(
				'type' => 'string'
			),
			'custom_share_message' => array(
				'type' => 'string'
			),
			'custom_share_image' => array(
				'type' => 'string'
			),
		),

		/** Define the category for your block */
		'category'        => 'widgets',

		/** The script name we gave in the wp_register_script() call */
		'editor_script'   => 'essb-share-display-block',

		/** The callback called by the javascript file to render the block */
		'render_callback' => 'essb_block_render_share_display',
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
function essb_block_render_share_display( $attributes ) {
	/** @var  $is_in_edit_mode  Check if we are in the editor */

	$is_in_edit_mode = strrpos( $_SERVER['REQUEST_URI'], "context=edit" );

	$force =  isset($attributes['force']) ? $attributes['force'] : '';
	$archive = isset($attributes['archive']) ? $attributes['archive'] : '';
	$display = isset($attributes['display']) ? $attributes['display'] : '';
		

	$force = ($force == 'yes') ? true : false;
	$archive = ($archive == 'yes') ? true : false;

	if ($is_in_edit_mode) {
		$output = essb_custom_position_generate($display, $force, $archive);

		if (!empty($output)) {
			return $output;
		}
		else {
			return '<p>You need to select a design from the menu or the selected design is not enabled in the positions. To avoid the positions check you can enable the "Always show" option.</p>';
		}
	}
	else {
		$custom_share = isset($attributes['custom_share']) ? $attributes['custom_share'] : '';

		if (!empty($custom_share)) {
			$custom_share_obj = array('custom' => 'true');
			$custom_share_obj['url'] = isset($attributes['custom_share_url']) ? $attributes['custom_share_url'] : '';
			$custom_share_obj['message'] = isset($attributes['custom_share_message']) ? $attributes['custom_share_message'] : '';
			$custom_share_obj['image'] = isset($attributes['custom_share_image']) ? $attributes['custom_share_image'] : '';

			return essb_custom_position_generate( $display, $force, $archive, $custom_share_obj );
		}
		else {
			return essb_custom_position_generate( $display, $force, $archive );
		}
	}
}