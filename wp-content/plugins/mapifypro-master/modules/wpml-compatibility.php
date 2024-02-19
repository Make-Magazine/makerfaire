<?php

use \Acf_Mapifypro\Model\Mapify_Meta_Field;
use \Acf_Mapifypro\Model\Mapify_Map_Location;
use \Acf_Mapifypro\Model\Maptiles_Uploader;

/**
 * Action hook that runs after a translated post has been created or updated with the WPML's automatic translation page.
 * We use this to handle some MapifyPro variables that cannot be copied with wpml-config.xml
 * 
 * @param int $new_post_id New WPML generated post ID.
 * @param array $data_fields Fields that has been translated by WPML.
 * @param object $job WPML job for current translation, where lies the original post ID which is $job->original_doc_id.
 */
function mpfy_wpml_pro_translation_completed( $new_post_id, $data_fields, $job ) {
	$post_id   = $job->original_doc_id;
	$post_type = ltrim( $job->original_post_type, 'post_' ); // because in WPML a post type name is prefixed with 'post_'

	if ( 'map' === $post_type ) { 
		mpfy_wpml_map_compatibility( $post_id, $new_post_id );
	}

	if ( 'map-location' === $post_type ) {
		mpfy_wpml_map_location_compatibility( $post_id, $new_post_id );
	}
}
add_action( 'wpml_pro_translation_completed', 'mpfy_wpml_pro_translation_completed', 10, 3 );

/**
 * Action hook that runs after a translated post has been created or updated with the ordinary WordPress post editor.
 * We use this to handle some MapifyPro variables that cannot be copied with wpml-config.xml
 * 
 * @param int $new_post_id New WPML generated post ID.
 * @param int $post_id Original post ID.
 */
function mpfy_wpml_after_save_post( $new_post_id, $post_id ) {
	// If the new post_id is the same with the old one, then this post has not been translated
	if ( absint( $new_post_id ) === absint( $post_id ) ) return;

	$post_type = get_post_type( $post_id );

	if ( 'map' === $post_type ) { 
		mpfy_wpml_map_compatibility( $post_id, $new_post_id );
	}

	if ( 'map-location' === $post_type ) {
		mpfy_wpml_map_location_compatibility( $post_id, $new_post_id );
	}
}
add_action( 'wpml_after_save_post', 'mpfy_wpml_after_save_post', 10, 2 );

/**
 * Handle the 'map' post type compatibility with WPML.
 * 
 * @param int $post_id Original post ID.
 * @param int $new_post_id New WPML generated post ID.
 */
function mpfy_wpml_map_compatibility( $post_id, $new_post_id ) {
	// Front-End Search Radius Options
	$search_radius_class = new Acf_Mapifypro_Search_Radius_Options();
	$search_radius_value = Mapify_Meta_Field::get_repeater_meta( $post_id, $search_radius_class->mapify_data_prefix );
	Mapify_Meta_Field::set_repeater_meta( $new_post_id, $search_radius_value, $search_radius_class->mapify_data_prefix );

	// Maptiles (Image Mode)
	$wp_upload_dir       = wp_upload_dir();	
	$original_maptiles   = new Maptiles_Uploader( $post_id );
	$original_upload_dir = $wp_upload_dir['basedir'] . '/' .  $original_maptiles->download_folder_name . '/' . $post_id;

	if ( 'tiles_download_completed' === $original_maptiles->status && file_exists( $original_upload_dir ) ) {
		$new_maptiles   = new Maptiles_Uploader( $new_post_id );
		$new_upload_dir = $wp_upload_dir['basedir'] . '/' .  $new_maptiles->download_folder_name . '/' . $new_post_id;		
		$original_files = glob( $original_upload_dir . '/*.*' );

		// create the local directory if not exist
		if ( ! file_exists( $new_upload_dir ) ) {
			wp_mkdir_p( $new_upload_dir );
		} else {
			$existed_files = scandir( $new_upload_dir );

			// flush the directory before fill it with the new batch
			if ( is_array( $existed_files ) ) {
				foreach ( $existed_files as $file ) {
					/**
					 * Remove unused file
					 * Suppress the warning message if the file didn't exist
					 */
					@unlink( $new_upload_dir . '/' . $file );
				}
			}
		}

		// copy tiles from the original post
		foreach( $original_files as $file ){
			$file_to_go = str_replace( $original_upload_dir, $new_upload_dir, $file );
		  	copy( $file, $file_to_go );
		}
	}
}

/**
 * Handle the 'map-location' post type compatibility with WPML.
 * 
 * @param int $post_id Original post ID.
 * @param int $new_post_id New WPML generated post ID.
 */
function mpfy_wpml_map_location_compatibility( $post_id, $new_post_id ) {
	// Address Details: Links
	$links_class  = new Acf_Mapifypro_Map_Location_Links();
	$links_url    = Mapify_Meta_Field::get_repeater_meta( $post_id, $links_class->link_url_prefix );
	$links_text   = Mapify_Meta_Field::get_repeater_meta( $post_id, $links_class->link_text_prefix );
	$links_target = Mapify_Meta_Field::get_repeater_meta( $post_id, $links_class->link_target_prefix );
	Mapify_Meta_Field::set_repeater_meta( $new_post_id, $links_url, $links_class->link_url_prefix );	
	Mapify_Meta_Field::set_repeater_meta( $new_post_id, $links_text, $links_class->link_text_prefix );	
	Mapify_Meta_Field::set_repeater_meta( $new_post_id, $links_target, $links_class->link_target_prefix );

	// Gallery Images
	$original_map_location = new Mapify_Map_Location( $post_id );
	$gallery_images        = $original_map_location->get_gallery_images();
	$new_map_location      = new Mapify_Map_Location( $new_post_id );
	$new_map_location->set_gallery_images( $gallery_images );
}

/**
 * Show a notification for user on each translated MapifyPro post-types, that the original MapifyPro post settings 
 * for the translated version, will be replaced with the original one, once user save the post.
 * 
 * @return void
 */
function mpfy_wpml_translated_post_notification(){	
	global $sitepress;

	$screen     = get_current_screen();
	$post_types = array( 'map', 'map-location', 'map-drawer', 'route' );
	$post_id    = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
	
	if ( $post_id && 'post' === $screen->base && in_array( $screen->post_type, $post_types ) && class_exists( 'SitePress' ) && $sitepress instanceof SitePress ) {
		$original_post_id   = apply_filters( 'wpml_object_id', $post_id, $screen->post_type, true, $sitepress->get_default_language() );
		$original_edit_link = get_edit_post_link( $original_post_id );

		if ( $original_post_id !== $post_id && $original_edit_link ) {
			$title      = __( 'MapifyPro', 'mpfy' );
			$message    = __( 'Since this is the translated version of the original post, the original MapifyPro settings for this post will be applied when you save this post.', 'mpfy' );
			$link_label = __( 'View the original post.', 'mpfy' );

			// print notice
			printf( '<div class="notice notice-info mapifypro-notice"><p><strong>%s:</strong> %s <a href="%s">%s</a></p></div>', $title, $message, $original_edit_link, $link_label );
		}
	}
}
add_action( 'admin_notices', 'mpfy_wpml_translated_post_notification' );