<?php
/**
 * The template for displaying review form.
 *
 * @see GravityView_Ratings_Reviews_Review::review_form()
 * @package GravityView_Ratings_Reviews
 * @since 0.1.0
 */
defined( 'ABSPATH' ) || exit;
?>

<?php if ( comments_open( $post_id ) ) : ?>
<?php
/**
 * Fires before the comment form.
 *
 * @since 0.1.0
 */
do_action( 'comment_form_before' );
?>
<div id="respond" class="comment-respond">
	<h3 id="reply-title" class="comment-reply-title"><?php comment_form_title( $args['title_reply'], $args['title_reply_to'] ); ?> <small><?php cancel_comment_reply_link( $args['cancel_reply_link'] ); ?></small></h3>
	<?php if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) : ?>
		<?php echo $args['must_log_in']; ?>
		<?php
		/**
		 * Fires after the HTML-formatted 'must log in after' message in the comment form.
		 *
		 * @since 0.1.0
		 */
		do_action( 'comment_form_must_log_in_after' );
		?>
	<?php else : ?>
		<?php
		$allowed_to_review = true;
		$user              = wp_get_current_user();
		if ( $user->exists() ) {
			if ( empty( $user->display_name ) ) {
				$user->display_name = $user->user_login;
			}

			$allowed_to_review = GravityView_Ratings_Reviews_Helper::is_user_allowed_to_leave_review( $post_id, $user->display_name, $user->user_email );
		}
		?>
		<?php

        if( is_wp_error( $allowed_to_review ) ) {

            echo esc_html( $allowed_to_review->get_error_message() );

        } elseif ( ! $allowed_to_review ) { ?>

			<?php echo $args['limited_to_one_review']; ?>
			<?php
			/**
			 * Fires after the HTML-formatted 'limited to one review' message.
			 *
			 * @since 0.1.0
			 */
			do_action( 'limited_to_one_review_after' );
			?>

		<?php } else { ?>

			<form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post" id="<?php echo esc_attr( $args['id_form'] ); ?>" class="comment-form"<?php echo $html5 ? ' novalidate' : ''; ?>>
				<?php
				/**
				 * Fires at the top of the comment form, inside the <form> tag.
				 *
				 * @since 0.1.0
				 */
				do_action( 'comment_form_top' );
				?>
				<?php if ( is_user_logged_in() ) : ?>
					<?php
					/**
					 * Filter the 'logged in' message for the comment form for display.
					 *
					 * @since 0.1.0
					 *
					 * @param string $args_logged_in The logged-in-as HTML-formatted message.
					 * @param array  $commenter      An array containing the comment author's
					 *                               username, email, and URL.
					 * @param string $user_identity  If the commenter is a registered user,
					 *                               the display name, blank otherwise.
					 */
					echo apply_filters( 'comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity );
					?>
					<?php
					/**
					 * Fires after the is_user_logged_in() check in the comment form.
					 *
					 * @since 0.1.0
					 *
					 * @param array  $commenter     An array containing the comment author's
					 *                              username, email, and URL.
					 * @param string $user_identity If the commenter is a registered user,
					 *                              the display name, blank otherwise.
					 */
					do_action( 'comment_form_logged_in_after', $commenter, $user_identity );
					?>
				<?php else : ?>
					<?php echo $args['comment_notes_before']; ?>
					<?php
					/**
					 * Fires before the comment fields in the comment form.
					 *
					 * @since 0.1.0
					 */
					do_action( 'comment_form_before_fields' );
					foreach ( (array) $args['fields'] as $name => $field ) {
						/**
						 * Filter a comment form field for display.
						 *
						 * The dynamic portion of the filter hook, $name, refers to the name
						 * of the comment form field. Such as 'author', 'email', or 'url'.
						 *
						 * @since 0.1.0
						 *
						 * @param string $field The HTML-formatted output of the comment form field.
						 */
						echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";
					}
					/**
					 * Fires after the comment fields in the comment form.
					 *
					 * @since 0.1.0
					 */
					do_action( 'comment_form_after_fields' );
					?>
				<?php endif; ?>
				<?php
				/**
				 * Filter the content of the comment textarea field for display.
				 *
				 * @since 0.1.0
				 *
				 * @param string $args_comment_field The content of the comment textarea field.
				 */
				echo apply_filters( 'comment_form_field_comment', $args['comment_field'] );
				?>
				<?php echo $args['comment_notes_after']; ?>
				<p class="form-submit">
					<input name="<?php echo esc_attr( $args['name_submit'] ); ?>" type="submit" id="<?php echo esc_attr( $args['id_submit'] ); ?>" value="<?php echo esc_attr( $args['label_submit'] ); ?>" />
					<?php comment_id_fields( $post_id ); ?>
				</p>
				<?php
				/**
				 * Fires at the bottom of the comment form, inside the closing </form> tag.
				 *
				 * @since 0.1.0
				 *
				 * @param int $post_id The post ID.
				 */
				do_action( 'comment_form', $post_id );
				?>
			</form>

		<?php } ?>

	<?php endif; ?>
</div><!-- #respond -->
<?php
/**
 * Fires after the comment form.
 *
 * @since 0.1.0
 */
do_action( 'comment_form_after' );
else :
/**
 * Fires after the comment form if comments are closed.
 *
 * @since 0.1.0
 */
do_action( 'comment_form_comments_closed' );
endif;
