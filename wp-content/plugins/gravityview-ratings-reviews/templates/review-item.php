<?php
/**
 * The template for displaying review item.
 *
 * @package GravityView_Ratings_Reviews
 * @since 0.1.0
 *
 * @global string $review_rating_type `vote` or `rating`
 * @global object $comment Comment to display.
 * @global int    $depth   Depth of comment.
 * @global array  $args    An array of arguments.
 * @global GravityView_Ratings_Reviews_Review_Walker $this
 * @global string $tag The comment container HTML tag ('div' or 'li')
 * @global string $add_below The container to add the Reply form to. 'div-comment' or 'comment'
 */

defined( 'ABSPATH' ) || exit;
$comment_classes[] = $this->has_children ? 'parent' : '';
// Styling similar to the WordPress Default Comments
$comment_classes[] = 'comment';

$review_rate      = ( $depth === 1 ) ? get_comment_meta( $comment->comment_ID, 'gv_review_rate', true ) : false;
$review_title     = get_comment_meta( $comment->comment_ID, 'gv_review_title', true );
$gravityview_view = $args['walker']->get_gv_obj();

?>
<<?php echo $tag; ?> id="review-<?php comment_ID(); ?>" <?php comment_class( implode( ' ', $comment_classes ) ); ?> itemprop="review" itemscope itemtype="http://schema.org/Review">
	<article id="div-review-<?php comment_ID(); ?>" class="comment-body" data-review_id="<?php comment_ID(); ?>">
		<footer class="comment-meta">
			<div class="comment-author vcard">
				<?php
				if ( 0 !== $args['avatar_size'] && 1 === (int) $gravityview_view->getAtts( 'show_gravatar_for_review' ) ) {
					if ( 1 === $depth ) {
						$args['avatar_size'] = 80;
					}

					$avatar = get_avatar( $comment->user_id, $args['avatar_size'] );

					if ( $avatar ) {
						printf(
							'<div class="gravatar">%1$s</div>',
							$avatar
						);
					}
				}
				?>
				<?php
				printf(
					// Translators: %1$s contains the translation for `By`, %2$s contains the authors name and profile link.
					__( '<span class="by">%1$s</span> <cite class="gv-review-author-link fn" itemprop="author">%2$s</cite>', 'gravityview-ratings-reviews' ),
					__( 'By', 'gravityview-ratings-reviews' ),
					get_comment_author_link()
				);
				?>
			</div><!-- .comment-author -->

			<div class="comment-metadata">
				<meta itemprop="itemReviewed" content="<?php
				// Translators: %s contains the post bridge ID.
				printf( _x( 'Entry %s', 'Item being reviewed', 'gravityview-ratings-reviews' ), GravityView_Ratings_Reviews_Helper::get_post_bridge_id() ); ?>" />
				<meta itemprop="datePublished" content="<?php echo esc_attr( get_comment_date( 'c', $comment->comment_ID ) ); ?>" />

				<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID, $args ) ); ?>">
					<time datetime="<?php comment_time( 'c' ); ?>">
						<?php
						// Translators: %1$s contains the date, %2$s contains the time.
						printf( _x( '%1$s at %2$s', '1: date, 2: time', 'gravityview-ratings-reviews' ), get_comment_date(), get_comment_time() ); ?>
					</time>
				</a>
				<?php edit_comment_link( __( 'Edit', 'gravityview-ratings-reviews' ), '<span class="edit-link">', '</span>' ); ?>

			</div><!-- .comment-metadata -->

			<?php

			if ( '0' == $comment->comment_approved ) {
				if ( 1 === $depth ) {
					$awaiting_moderation = __( 'Your review is awaiting moderation.', 'gravityview-ratings-reviews' );
				} else {
					$awaiting_moderation = __( 'Your comment is awaiting moderation.', 'gravityview-ratings-reviews' );
				}
				?>
				<p class="gv-review-awaiting-moderation comment-awaiting-moderation"><?php echo esc_html( $awaiting_moderation ); ?></p>
			<?php } ?>

			<?php if ( $review_rate ) : ?>
			<div class="gv-review-rate" itemprop="reviewRating">
				<?php
				if ( 'vote' === $review_rating_type ) {
					GravityView_Ratings_Reviews_Helper::the_vote_rating(
						array(
							'rating' => $review_rate ? GravityView_Ratings_Reviews_Helper::get_vote_from_star( $review_rate ) : 0,
						)
					);
				} else {
					GravityView_Ratings_Reviews_Helper::the_star_rating(
						array(
							'rating' => $review_rate ? $review_rate : 0,
							'type'   => 'rating',
						)
					);
				}
				?>
			</div><!-- .gv-review-rate -->
			<?php endif; ?>

		</footer><!-- .comment-meta -->

		<div class="comment-content gv-review-content-text" itemprop="reviewBody">
			<?php if ( ! empty( $review_title ) ) : ?>
			<p class="gv-review-title" itemprop="name">
				<?php echo esc_html( $review_title ); ?>
			</p>
			<?php endif; ?>

			<?php
			comment_text(
				get_comment_id(),
				array_merge(
					$args,
					array(
						'add_below' => $add_below,
						'depth'     => $depth,
						'max_depth' => $args['max_depth'],
					)
				)
			);
			?>
		</div><!-- .comment-content -->

		<?php
		if ( GravityView_Ratings_Reviews_Review_Edit::is_user_allowed_to_edit_review( $comment, get_the_ID(), $gravityview_view->view_id ) ) {
			echo GravityView_Ratings_Reviews_Review_Edit::get_review_edit_buttons( $comment );
			echo GravityView_Ratings_Reviews_Review_Edit::get_review_edit_form( $comment, $review_title, $review_rate, $review_rating_type );
		}
		?>

		<?php
		comment_reply_link(
			array_merge(
				$args,
				array(
					'reply_text' => __( 'Comment on this review', 'gravityview-ratings-reviews' ),
					'login_text' => __( 'Log in to comment', 'gravityview-ratings-reviews' ),
					'add_below'  => 'gv-div-review',
					'depth'      => $depth,
					'max_depth'  => $args['max_depth'],
					'before'     => '<div class="reply">',
					'after'      => '</div>',
				)
			)
		);
		?>
	</article><!-- .comment-body -->
