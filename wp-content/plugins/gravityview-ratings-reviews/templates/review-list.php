<?php
/**
 * The template for displaying reviews.
 *
 * The area of the single-entry that contains reviews and the review form.
 *
 * @package GravityView_Ratings_Reviews
 * @since 0.1.0
 */
global $post;

defined( 'ABSPATH' ) || exit;

// Before replacing current post, backup it to global.
wp_cache_add( 'gv_post_container_id', $post->ID );

// Post ID that connects entry to comments.
$post_bridge_id = GravityView_Ratings_Reviews_Helper::get_post_bridge_id( null, true );

$post = get_post( $post_bridge_id );
if ( $post && ! is_wp_error( $post ) ):
	setup_postdata( $post );
?>

	<?php do_action( 'gv_ratings_reviews_list_before' ); ?>
	<div class="gv-reviews-area">

		<?php
		GravityView_Ratings_Reviews_Helper::the_review_list_header();
		GravityView_Ratings_Reviews_Helper::the_review_list_body();
		GravityView_Ratings_Reviews_Helper::the_review_list_footer();
		?>

	</div><!-- .gv-reviews-area -->
	<?php do_action( 'gv_ratings_reviews_list_after' ); ?>

<?php
endif; // if $post
wp_reset_postdata();
