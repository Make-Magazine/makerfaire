<?php

// make order of projects library random
add_filter( 'posts_orderby', 'randomise_with_pagination', 9999999 );
function randomise_with_pagination( $orderby ) {
	$post = is_singular() ? get_queried_object() : false;
	if ( ! empty($post) && is_a($post, 'WP_Post') && (is_page(661625) || is_page(661623) || is_page(693019) || is_page(693751))) {
        // Reset seed on load of initial archive page
        if(!isset($_GET["sf_paged"]) || get_query_var( 'paged' ) == 1 ) {
            if( isset( $_SESSION['seed'] ) ) {
                unset( $_SESSION['seed'] );
            }
        }
        // Get seed from session variable if it exists
        $seed = false;
        if( isset( $_SESSION['seed'] ) ) {
            $seed = $_SESSION['seed'];
        }
        // Set new seed if none exists
        if ( ! $seed ) {
            $seed = rand();
            $_SESSION['seed'] = $seed;
        }
        // Update ORDER BY clause to use seed
        return 'RAND(' . $seed . ')';
    } else {
        return "wp_posts.post_date DESC";
    }
}