<?php

/**
 * Custom css to load on contributor's admin page
 * 
 * @return void
 */
function mpfy_contributor_admin_enqueue_scripts() {
	$current_user = wp_get_current_user();
	
	if ( is_admin() && in_array( 'mpfy_contributor', (array) $current_user->roles ) ) {
		wp_enqueue_style( 'mpfy-contributor-admin', 
			plugin_dir_url( CROWD_PLUGIN_FILE ) . 'assets/contributors-admin.css', 
			array(), CROWD_PLUGIN_VERSION, 'all' 
		);
	}
}
add_action( 'admin_enqueue_scripts', 'mpfy_contributor_admin_enqueue_scripts' );

/**
 * ACF filter acf/load_value.
 * 
 * @param    mixed    $field_value    The field value.
 * @param    int      $post_id        The post ID where the value is saved.
 * @param    array    $field          The field array containing all settings.
 * @return   mixed
 */
function mpfy_contributor_acf_load_value( $field_value, $post_id, $field ) {
	$user_id = get_post_field( 'post_author', $post_id );
	return absint( $user_id );
}
add_filter( 'acf/load_value/key=' . mpfy_contributor_acf_key(), 'mpfy_contributor_acf_load_value', 10, 3 );

/**
 * ACF action acf/save_post.
 * This hook's priority has been set to 5 (< 10) to run before ACF saves the value.
 * 
 * @since    1.0.0
 * @param    int      $post_id    The post ID where the value is saved.
 */
function mpfy_contributor_acf_update_value( $post_id ) {
	if ( ! isset( $_POST['acf'][ mpfy_contributor_acf_key() ] ) ) return;

	wp_update_post( array(
		'ID'          => $post_id,
		'post_author' => absint( $_POST['acf'][ mpfy_contributor_acf_key() ] ),
	) );
}
add_action( 'acf/save_post', 'mpfy_contributor_acf_update_value', 5 );

/**
 * ACF key for the mpfy_contributor field.
 * 
 * @return string
 */
function mpfy_contributor_acf_key() {
	return 'mapify_acf_field_61765eead6e8c';
}

/**
 * Replace the "Permalink" column with "Author".
 * The "Permalink" column is save to remove because we already have the "View" link.
 * 
 * @return array
 */
function mpfy_add_location_author_admin_column( $columns ) {
	$new_columns = array();

	foreach ( $columns as $key => $value ) {
		if ( 'permalink' === $key ) {
			$new_columns['author'] = __( 'Author' , 'mpfy' );
			continue;
		}

		$new_columns[ $key ] = $value;
	}
	
    return $new_columns;
}
add_filter( 'manage_edit-map-location_columns', 'mpfy_add_location_author_admin_column', 20 );

/**
 * Show author dropdown to filter the admin map locations list.
 * 
 * @return void
 */
function mpfy_author_filter_on_admin_map_locations() {
    global $typenow, $wp_query;

    if ( 'map-location' === $typenow ) {
        wp_dropdown_users( array(
            'show_option_all' => __( 'All Authors', 'mpfy' ),
            'name'            => 'author',
            'selected'        => isset( $wp_query->query['author'] ) ? absint( $wp_query->query['author'] ) : 0,
        ) );
    }
}
add_action( 'restrict_manage_posts','mpfy_author_filter_on_admin_map_locations' );

/**
 * Only show mpfy_contributor's belonging for attachments
 * 
 * @return array
 */
function mpfy_contributor_attachments_args( $query ) {
    $current_user = wp_get_current_user();

    if ( $current_user && in_array( 'mpfy_contributor', (array) $current_user->roles ) ) {
        $query['author'] = $current_user->ID;
    }

    return $query;
}
add_filter( 'ajax_query_attachments_args', 'mpfy_contributor_attachments_args' );

/**
 * Restrict mpfy_contributor to add new location via admin page
 * 
 * @return void
 */
function mpfy_contributor_restrict_add_new_location_on_admin() {
	$screen       = get_current_screen();
	$current_user = wp_get_current_user();
	$is_screen    = $screen && 'add' === $screen->action && 'map-location' === $screen->id;
	$is_role      = $current_user && in_array( 'mpfy_contributor', (array) $current_user->roles );

	if ( $is_screen && $is_role ) {
		wp_die( 
			__( 'Sorry, you are not allowed to add a new map location through this page.', 'mpfy' ),
			__( 'Error', 'mpfy' )
		);	
	}
}
add_action( 'current_screen', 'mpfy_contributor_restrict_add_new_location_on_admin' );