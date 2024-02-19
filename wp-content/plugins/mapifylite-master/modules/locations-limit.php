<?php

function mpfy_loclim_last_deleted_ids( $ids = null ) {
    static $post_ids;

    if ( ! empty( $ids ) ) {
        $post_ids = $ids;
    }

    return $post_ids;
}

function mpfy_loclim_get_location_limit() {
    return 10;
}

function mpfy_loclim_get_marked_for_deletion_meta_key() {
    return '_mpfy_loclim_marked_for_deletion';
}

function mpfy_loclim_get_published_locations_count() {
    global $wpdb;

    $query = 'SELECT COUNT(*) FROM `' . $wpdb->posts . '` WHERE `post_status` = "publish" AND `post_type` = "map-location"';

    return intval( $wpdb->get_var( $query ) );
}

function mpfy_loclim_get_marked_for_deletion() {
    global $wpdb;

    $query = "
        SELECT `post_id` FROM `$wpdb->postmeta`
        WHERE `meta_key` = %s
        AND `meta_value` = %s
    ";

    $query = $wpdb->prepare( $query, mpfy_loclim_get_marked_for_deletion_meta_key(), 'yes' );
    $post_ids = $wpdb->get_col( $query );

    $post_ids = array_map( function( $post_id ) {
        return intval( $post_id );
    }, $post_ids );
    $post_ids = array_filter( $post_ids );

    return $post_ids;
}

// --------- Hooks ---------

function mpfy_loclim_on_location_publish( $post_id, $post ) {
    global $wpdb;

    $published_locations_count = mpfy_loclim_get_published_locations_count();
    $location_limit = mpfy_loclim_get_location_limit();

    if ( $published_locations_count <= $location_limit ) {
        return;
    }

    $wpdb->update(
        $wpdb->posts,
        [ 'post_status' => 'draft' ],
        [ 'ID' => $post_id ]
    );
    update_post_meta( $post_id, mpfy_loclim_get_marked_for_deletion_meta_key(), 'yes' );
}
add_action( 'publish_map-location', 'mpfy_loclim_on_location_publish', 10, 2 );

function mpfy_loclim_purge_marked_for_deletion() {
    global $wpdb;

    $post_ids = mpfy_loclim_get_marked_for_deletion();
    mpfy_loclim_last_deleted_ids( $post_ids );
    if ( empty( $post_ids ) ) {
        return;
    }

    $post_ids_stringified   = implode( ',', $post_ids );
    $delete_locations_query = $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE ID IN ( %s )", $post_ids_stringified );
    $delete_meta_query      = $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id IN ( %s )", $post_ids_stringified );
    $wpdb->query( $delete_locations_query );
    $wpdb->query( $delete_meta_query );

    mpfy_loclim_maybe_redirect_to_dashboard();
}
add_action( 'admin_init', 'mpfy_loclim_purge_marked_for_deletion' );

function mpfy_loclim_maybe_redirect_to_dashboard() {
    global $pagenow;

    if ( $pagenow !== 'post.php' ) {
        return;
    }

    $current_id = ! empty( $_GET['post'] ) ? intval( $_GET['post'] ) : 0;
    $post_ids = mpfy_loclim_last_deleted_ids();
    if ( empty( $post_ids ) || ! in_array( $current_id, $post_ids ) ) {
        return;
    }

    wp_safe_redirect( admin_url( '/edit.php?post_type=map-location' ) );
    exit;
}

function mpfy_loclim_notice_limit_reached() {
    $published_locations_count = mpfy_loclim_get_published_locations_count();
    $location_limit = mpfy_loclim_get_location_limit();

    $should_show = $published_locations_count >= $location_limit;
    if ( ! $should_show ) {
        return;
    }
    ?>
        <div class="notice notice-warning">
            <p>
                <?php
                printf(
                    __( "You've reached the maximum number of map locations(%d) for the %s plugin. If you try to publish any more, they'll be deleted.", 'mpfy' ),
                    mpfy_loclim_get_location_limit(),
                    MAPIFY_PLUGIN_NAME
                );
                ?></p>
        </div>
    <?php
}
add_action( 'admin_notices', 'mpfy_loclim_notice_limit_reached' );
