<?php
/**
 * The template for displaying review list footer.
 *
 * @package GravityView_Ratings_Reviews
 * @since 0.1.0
 */
defined( 'ABSPATH' ) || exit;
?>

<div class="gv-review-list-footer">
	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Comment navigation', 'gravityview-ratings-reviews' ); ?></h1>
		<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'gravityview-ratings-reviews' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'gravityview-ratings-reviews' ) ); ?></div>
	</nav><!-- #comment-nav-below -->
	<?php endif; // Check for comment navigation. ?>

	<?php GravityView_Ratings_Reviews_Review::review_form(); ?>
</div><!-- /.gv-review-list-footer -->
