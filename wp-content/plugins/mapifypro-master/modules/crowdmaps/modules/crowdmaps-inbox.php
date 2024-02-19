<?php

/**
 * Register page CrowdMaps Inbox
 */
function crowd_register_page_inbox() {
	$count_posts     = wp_count_posts( 'map-location' );
	$pending_counter = $count_posts->pending > 0 ? sprintf( '<span class="awaiting-mod">%s</span>', esc_html( $count_posts->pending ) ) : '';

	add_submenu_page(
		'mapify.php',
		__( 'CrowdMaps Inbox', 'crowd' ),
		mpfy_get_icon( 'crowdmaps' ) . __( 'CrowdMaps', 'crowd' ) . ' ' . $pending_counter,
		'manage_options',
		'crowd-inbox',
		'crowd_page_inbox',
		3
	); 
}
add_action( 'admin_menu', 'crowd_register_page_inbox' );

/**
 * Page CrowdMaps Inbox
 */
function crowd_page_inbox() {
	$paged  = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1;
	$paged  = $paged < 1 ? 1 : $paged;
	$search = '';

	// args
	$args = array(
		'post_type'      => 'map-location',
		'posts_per_page' => 20,
		'post_status'    => 'pending', 
		'paged'          => $paged, // pagination, based on posts_per_page option
		'order'          => 'ASC',
	);

	// search
	if ( isset( $_GET['s'] ) && '' !== $_GET['s'] ) {
		$args['s'] = sanitize_text_field( $_GET['s'] );
		$search    = $args['s'];
	}

	// filter by month
	if ( isset( $_GET['m'] ) && 0 !== intval( $_GET['m'] ) ) {
		$args['m'] = intval( $_GET['m'] );
	}
	
	// filter by author
	if ( isset( $_GET['user'] ) && 0 !== intval( $_GET['user'] ) ) {
		$args['author'] = absint( $_GET['user'] );
	}

	// filter by map
	if ( isset( $_GET['map_id'] ) && 0 !== intval( $_GET['map_id'] ) ) {
		$args['meta_query'] = array(
			array(
				'key'     => '_map_location_map',
				'value'   => '(^|,)' . absint( $_GET['map_id'] ) . '(,|$)', // select between comma(s)
				'compare' => 'REGEXP',
			),
		);
	}

	include_once( ABSPATH . '/wp-admin/includes/class-wp-posts-list-table.php' );

	$the_query          = new WP_Query( $args );
	$total_number       = $the_query->found_posts;   // total without limit
	$displayed_number   = $the_query->post_count;    // total with limit
	$max_page           = $the_query->max_num_pages; // max page number
	$wp_post_list_table = new WP_Posts_List_Table();
	$count_posts        = wp_count_posts( 'map-location' );
	$updated            = isset( $_GET['updated'] ) ? boolval( $_GET['updated'] ) : false;

	require CROWD_PLUGIN_DIR_PATH . '/html/html-admin-inbox-page.php';
	wp_reset_postdata();
}

/**
 * Pagination URL generator
 */
function crowd_page_inbox_paginate_url( $paged = 1, $max_page = 1 ) {
	$paginate_url = admin_url( 'admin.php' );
	$uri          = parse_url( $_SERVER['REQUEST_URI'] );

	parse_str( $uri['query'], $query );
	
	// prevent minus or over
	$paged = $paged < 1 ? 1 : $paged;
	$paged = $paged > $max_page ? $max_page : $paged;

	if ( is_array( $query ) && count( $query ) > 0 ) {
		$query['paged'] = $paged;

		foreach ( $query as $key => $value ) {
			$paginate_url = add_query_arg( $key, $value, $paginate_url );
		}
	}

	echo esc_url_raw( $paginate_url );
}

/**
 * Admin action to publish map location
 */
function crowd_publish_map_location() {
	check_admin_referer( 'Lijtw52BURKDNmb3' );

	// publish post
	wp_update_post( array(
		'ID'          => absint( $_GET['id'] ),
		'post_status' => 'publish',
	) );
	
	// redirect
	wp_redirect( add_query_arg( 'updated', '1', $_SERVER['HTTP_REFERER'] ) );
    exit();
}
add_action( 'admin_action_crowd_publish_location', 'crowd_publish_map_location' );

/**
 * Dropdown maps
 */
function crowd_dropdown_maps() {
	$args = array(
        'depth'                 => 0,
        'child_of'              => 0,
        'selected'              => isset( $_GET['map_id'] ) ? absint( $_GET['map_id'] ) : 0,
        'name'                  => 'map_id',
        'id'                    => '',
        'class'                 => '',
        'show_option_none'      => __( 'All Maps', 'crowd' ),
        'show_option_no_change' => '',
        'option_none_value'     => '0',
        'value_field'           => 'ID',
    );
 
	$output = '';
    $posts  = get_posts( array(
        'post_type'   => 'map',
        'post_status' => 'any',
        'numberposts' => '-1',
    ) );

	// Back-compat with old system where both id and name were based on $name argument.
    if ( empty( $args['id'] ) ) {
        $args['id'] = $args['name'];
    }
 
    if ( ! empty( $posts ) ) {
        $class = '';
        if ( ! empty( $args['class'] ) ) {
            $class = " class='" . esc_attr( $args['class'] ) . "'";
        }
 
        $output = "<select name='" . esc_attr( $args['name'] ) . "'" . $class . " id='" . esc_attr( $args['id'] ) . "'>\n";
        if ( $args['show_option_no_change'] ) {
            $output .= "\t<option value=\"-1\">" . $args['show_option_no_change'] . "</option>\n";
        }
        if ( $args['show_option_none'] ) {
            $output .= "\t<option value=\"" . esc_attr( $args['option_none_value'] ) . '">' . $args['show_option_none'] . "</option>\n";
        }
        $output .= walk_page_dropdown_tree( $posts, $args['depth'], $args );
        $output .= "</select>\n";
    }

	return $output;
}