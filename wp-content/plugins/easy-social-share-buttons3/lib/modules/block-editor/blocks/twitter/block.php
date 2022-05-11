<?php
/**
 * Generate Twitter block
 */

/**
 * Register the Twitter block
 */
function essb_block_register_twitter() {
	$block_path = 'block.js';
	wp_register_script(
		'essb-twitter-block',
		plugins_url( $block_path, __FILE__ ),
		[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' ],
		'1.0'
	);

	register_block_type( 'essb/essb-twitter', array(

		/** Define the attributes used in your block */

		'attributes'      => array(
			'theme'         => array(
				'type' => 'string'
			),
			'username'      => array(
				'type' => 'string'
			),
			'tweet'         => array(
				'type' => 'string'
			),
			'hashtags'      => array(
				'type' => 'string'
			),
			'tweet'         => array(
				'type' => 'string'
			),
			'url'           => array(
				'type' => 'string'
			),
			'hide_username' => array(
				'type' => 'boolean'
			),
			'hide_hashtags' => array(
				'type' => 'boolean'
			)
		),

		/** Define the category for your block */
		'category'        => 'widgets',

		/** The script name we gave in the wp_register_script() call */
		'editor_script'   => 'essb-twitter-block',
		/*'style'           => 'essb-twitter-block-css',*/

		/** The callback called by the javascript file to render the block */
		'render_callback' => 'essb_block_render_twitter',
	) );
}

/**
 * Render Twitter block
 * @param $attributes
 * @return string
 *
 * @author appscreo
 * @package EasySocialShareButtons
 * @since 8.0
 */
function essb_block_render_twitter( $attributes ) {
	/** @var  $is_in_edit_mode  Check if we are in the editor */
	$is_in_edit_mode = strrpos( $_SERVER['REQUEST_URI'], "context=edit" );


	$atts = array(
		'tweet'        => '',
		'via'          => 'yes',
		'url'          => '',
		'nofollow'     => 'no',
		'user'         => '',
		'hashtags'     => '',
		'usehashtags'  => 'yes',
		'template'     => '',
		'image'        => '',
		'preview_mode' => $is_in_edit_mode ? 'true' : 'false'
	);

	if ( isset( $attributes['tweet'] ) ) {
		$atts['tweet'] = $attributes['tweet'];
	}

	if ( isset( $attributes['theme'] ) ) {
		$atts['template'] = $attributes['theme'];
	}

	if ( isset( $attributes['username'] ) ) {
		$atts['user'] = $attributes['username'];
	}

	if ( isset( $attributes['hashtags'] ) ) {
		$atts['hashtags'] = $attributes['hashtags'];
	}

	if ( isset( $attributes['url'] ) ) {
		$atts['url'] = $attributes['url'];
	}

	if ( isset( $attributes['hide_username'] ) ) {
		$atts['via'] = $attributes['hide_username'] ? 'no' : 'yes';
	}

	if ( isset( $attributes['hide_hashtags'] ) ) {
		$atts['usehashtags'] = $attributes['hide_hashtags'] ? 'no' : 'yes';
	}

	if ( function_exists( 'essb_ctt_shortcode' ) ) {
		return essb_ctt_shortcode( $atts );
	} else {
		return 'Click to Tweet not running';
	}
}