<?php
/**
 * Display the reviews link field type.
 *
 * @since   0.1.0
 * @package GravityView_Ratings_Reviews
 */

if ( ! isset( $gravityview ) || empty( $gravityview->template ) ) {
	gravityview()->log->error( '{file} template loaded without context', array( 'file' => __FILE__ ) );

	return;
}

global $gv_ratings_reviews;

$post_bridge_comp  = $gv_ratings_reviews->component_instances['post-bridge'];
$post_id           = $post_bridge_comp->create_bridge( $gravityview->entry->as_entry() );
$view_settings     = $gravityview->view->settings->all();
$user              = wp_get_current_user();
$allowed_to_review = GravityView_Ratings_Reviews_Helper::is_user_allowed_to_leave_review( $post_id, $user->user_login, $user->user_email, array(), $view_settings );

if ( ! is_user_logged_in() ) {
	echo esc_html__( 'Please log in to rate an entry from this View.', 'gravityview-ratings-reviews' );
} elseif ( ! absint( $view_settings['allow_entry_reviews'] ) ) {
	echo esc_html__( 'To rate an entry, please enable this option in the View settings.', 'gravityview-ratings-reviews' );
} elseif ( ! absint( $view_settings['allow_empty_reviews'] ) ) {
	echo esc_html__( 'To rate an entry from this View, please allow empty review text in the View settings.', 'gravityview-ratings-reviews' );
} elseif ( ! $allowed_to_review ) {
	echo esc_html__( 'You have already reviewed this entry.', 'gravityview-ratings-reviews' );
} else {
	$data = array(
		'clickable' => true,
		'entry_id'  => $gravityview->entry['id'],
	);

	if ( 'vote' === $view_settings['entry_review_type'] ) {
		echo GravityView_Ratings_Reviews_Helper::get_vote_rating( $data );
	} else {
		echo GravityView_Ratings_Reviews_Helper::get_star_rating( $data );
	}
}
