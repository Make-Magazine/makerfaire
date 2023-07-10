<?php

define ( 'ESSB_BLOCKS_FILE_ROOT', dirname ( __FILE__ ) . '/' );

add_action('init', 'essb_register_custom_blocks');

/**
 * Register the available blocks
 */
function essb_register_custom_blocks() {

	if (!essb_option_bool_value('deactivate_ctt')) {
		include_once( ESSB_BLOCKS_FILE_ROOT . 'blocks/twitter/block.php' );
		essb_block_register_twitter();
	}

	if (!essb_option_bool_value('deactivate_module_subscribe')) {
		include_once( ESSB_BLOCKS_FILE_ROOT . 'blocks/subscribe-forms/block.php' );
		essb_block_register_subscribe();
	}

	if (!essb_option_bool_value('deactivate_module_profiles')) {
		include_once( ESSB_BLOCKS_FILE_ROOT . 'blocks/profile-links/block.php' );
		essb_block_register_social_profiles();
	}

	if (!essb_option_bool_value('deactivate_module_followers')) {
		include_once( ESSB_BLOCKS_FILE_ROOT . 'blocks/followers-counter/block.php' );
		essb_block_register_social_followers();
	}

	if (!essb_option_bool_value('deactivate_module_instagram')) {
		include_once( ESSB_BLOCKS_FILE_ROOT . 'blocks/instagram-feed/block.php' );
		essb_block_register_instagram_feed();
	}

	if (!essb_options_bool_value('deactivate_custompositions')) {
	   include_once( ESSB_BLOCKS_FILE_ROOT . 'blocks/share-display/block.php' );
	   essb_block_register_social_share_display();
	}
}

add_action ( 'enqueue_block_editor_assets', 'essb_register_custom_block_assets');

function essb_register_custom_block_assets() {
	if (!essb_option_bool_value('deactivate_module_subscribe')) {
		$template_file = ESSB3_PLUGIN_URL . '/assets/modules/subscribe-forms.css';

		wp_register_style ( 'essb-subscribe-forms-style', $template_file, array (), ESSB3_VERSION );
		wp_enqueue_style ( 'essb-subscribe-forms-style' );
	}

	if (!essb_option_bool_value('deactivate_module_profiles')) {
		wp_register_style ( 'essbfc-admin3-style', ESSB3_PLUGIN_URL . '/lib/modules/social-followers-counter/assets/social-profiles.min.css', array (), ESSB3_VERSION );
		wp_enqueue_style ( 'essbfc-admin3-style' );

		wp_register_style ( 'essb-admin3-style-animations', ESSB3_PLUGIN_URL . '/assets/css/essb-animations.css', array (), ESSB3_VERSION );
		wp_enqueue_style ( 'essb-admin3-style-animations' );
	}

	if (!essb_option_bool_value('deactivate_module_followers')) {
		wp_register_style ( 'essbfc-admin3-style', ESSB3_PLUGIN_URL . '/lib/modules/social-followers-counter/assets/social-profiles.min.css', array (), ESSB3_VERSION );
		wp_enqueue_style ( 'essbfc-admin3-style' );

		wp_register_style ( 'essb-admin3-style-animations', ESSB3_PLUGIN_URL . '/assets/css/essb-animations.css', array (), ESSB3_VERSION );
		wp_enqueue_style ( 'essb-admin3-style-animations' );
	}

	if (!essb_option_bool_value('deactivate_module_instagram')) {
		$template_file = ESSB3_PLUGIN_URL . '/lib/modules/instagram-feed/assets/essb-instagramfeed.css';
		wp_register_style ( 'essb-instagram-feed', $template_file, array (), ESSB3_VERSION );
		wp_enqueue_style ( 'essb-instagram-feed' );
	}
	
	/**
	 * @since 8.5 fixing Click to Tweet styles load
	 */
	if (!essb_option_bool_value('deactivate_ctt')) {
	    $template_file = ESSB3_PLUGIN_URL . '/assets/modules/click-to-tweet.css';
	    wp_register_style ( 'essb-click-to-tweet', $template_file, array (), ESSB3_VERSION );
	    wp_enqueue_style ( 'essb-click-to-tweet' );
	}
}