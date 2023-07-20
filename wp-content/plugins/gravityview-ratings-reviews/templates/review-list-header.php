<?php
/**
 * The template for displaying review list header.
 *
 * @package GravityView_Ratings_Reviews
 * @since 0.1.0
 */
global $post, $gravityview_view;

defined( 'ABSPATH' ) || exit;

$reviews_number       = GravityView_Ratings_Reviews_Helper::get_reviews_number( $post );
$review_rating_type   = $gravityview_view->atts['entry_review_type'];
$entry_average_rating = GravityView_Ratings_Reviews_Helper::get_review_average_rating( $post );
?>

<div class="gv-review-list-header" id="gv-entry-reviews">
	<h2 class="gv-review-list-title">
	<?php if ( $reviews_number ) : ?>
		<span class="gv-review-num-of-reviews">
			<?php echo esc_html( sprintf( _n( 'One review of this entry', '%1$s reviews of this entry', $reviews_number, 'gravityview-ratings-reviews' ), number_format_i18n( $reviews_number ) ) ); ?>
		</span>

		<span class="gv-review-rating-aggregate">
			<?php
			if ( 'vote' === $review_rating_type ) {
				GravityView_Ratings_Reviews_Helper::the_vote_rating( array(
					'rating'       => $entry_average_rating['average_vote'],
					'number'       => $entry_average_rating['total_voters'],
					'display_text' => true,
				) );
			} else {
				GravityView_Ratings_Reviews_Helper::the_star_rating( array(
					'rating'       => $entry_average_rating['average_stars'],
					'type'         => 'rating',
					'number'       => $entry_average_rating['total_voters'],
					'display_text' => true,
				) );
			}
			?>
		<span>
	<?php else : ?>
		<span class="gv-review-num-of-reviews">
			<?php esc_html_e( 'This entry has no reviews.', 'gravityview-ratings-reviews' ); ?>
		</span>
	<?php endif; ?>
	</h2>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'gravityview-ratings-reviews' ); ?></h1>
		<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'gravityview-ratings-reviews' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'gravityview-ratings-reviews' ) ); ?></div>
	</nav><!-- #comment-nav-above -->
	<?php endif; // Check for comment navigation. ?>
</div>
