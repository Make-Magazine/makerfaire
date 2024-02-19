<?php

use Acf_Prettyroutes\Model\Prettyroutes_Meta_Field;

/**
 * Action hook that run after a post has been translated (or copied) and become a new post.
 * We use this to handle some PrettyRoutes variables that cannot be copied with wpml-config.xml
 * 
 * @param int $new_post_id New WPML generated post ID.
 * @param array $data_fields Fields that has been translated by WPML.
 * @param object $job WPML job for current translation, where lies the original post ID which is $job->original_doc_id.
 */
function prettyroutes_wpml_pro_translation_completed( $new_post_id, $data_fields, $job ) {
	$post_id   = $job->original_doc_id;
	$post_type = ltrim( $job->original_post_type, 'post_' ); // because in WPML a post type name is prefixed with 'post_'
	
	if ( 'route' === $post_type ) {
		pretyroutes_waypoints_wpml_map_compatibility( $post_id, $new_post_id );
	}
}
add_action( 'wpml_pro_translation_completed', 'prettyroutes_wpml_pro_translation_completed', 10, 3 );

/**
 * Action hook that runs after a translated post has been created or updated with the ordinary WordPress post editor.
 * We use this to handle some MapifyPro variables that cannot be copied with wpml-config.xml
 * 
 * @param int $new_post_id New WPML generated post ID.
 * @param int $post_id Original post ID.
 */
function prettyroutes_wpml_after_save_post( $new_post_id, $post_id ) {
	// If the new post_id is the same with the old one, then this post has not been translated
	if ( $new_post_id === $post_id ) return;

	$post_type = get_post_type( $post_id );

	if ( 'route' === $post_type ) {
		pretyroutes_waypoints_wpml_map_compatibility( $post_id, $new_post_id );
	}
}
add_action( 'wpml_after_save_post', 'prettyroutes_wpml_after_save_post', 10, 2 );

/**
 * Handle the 'waypoints' fields compatibility with WPML.
 *
 * @param int $post_id The original post's ID.
 * @param int $new_post_id New WPML generated post ID.
 */
function pretyroutes_waypoints_wpml_map_compatibility( $post_id, $new_post_id ) {
	$waypoints_class = new Acf_Prettyroutes_Routes_Waypoints();

	// get meta data from current post
	$pin_enabled     = Prettyroutes_Meta_Field::get_repeater_meta( $post_id, $waypoints_class->pin_enabled_prefix );
	$tooltip_enabled = Prettyroutes_Meta_Field::get_repeater_meta( $post_id, $waypoints_class->tooltip_enabled_prefix );
	$tooltip_close   = Prettyroutes_Meta_Field::get_repeater_meta( $post_id, $waypoints_class->tooltip_close_prefix );
	$tooltip_content = Prettyroutes_Meta_Field::get_repeater_meta( $post_id, $waypoints_class->tooltip_content_prefix );
	$pin             = Prettyroutes_Meta_Field::get_repeater_meta( $post_id, $waypoints_class->pin_prefix );

	// save meta data to the new post
	Prettyroutes_Meta_Field::set_repeater_meta( $new_post_id, $pin_enabled, $waypoints_class->pin_enabled_prefix );
	Prettyroutes_Meta_Field::set_repeater_meta( $new_post_id, $tooltip_enabled, $waypoints_class->tooltip_enabled_prefix );
	Prettyroutes_Meta_Field::set_repeater_meta( $new_post_id, $tooltip_close, $waypoints_class->tooltip_close_prefix );	
	Prettyroutes_Meta_Field::set_repeater_meta( $new_post_id, $pin, $waypoints_class->pin_prefix );

	/**
	 * Because of the WPML limitation, we cannot automatically-translate the repeater fields
	 * So we wont replace the already translated waypoints tooltip content
	 */
	$new_tooltip_content = Prettyroutes_Meta_Field::get_repeater_meta( $new_post_id, $waypoints_class->tooltip_content_prefix );
	
	if ( ! $new_tooltip_content ) {
		Prettyroutes_Meta_Field::set_repeater_meta( $new_post_id, $tooltip_content, $waypoints_class->tooltip_content_prefix );
	}
}