<?php
/**
 * Handle Rating Reviews Edit.
 *
 * @package   GravityView_Ratings_Reviews
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://www.gravitykit.com
 * @copyright Copyright 2014, Katz Web Services, Inc.
 */

defined( 'ABSPATH' ) || exit;

class GravityView_Ratings_Reviews_Review_Edit extends GravityView_Ratings_Reviews_Component {

	public function load() {
		add_action( 'init', array( $this, 'edit_review' ), 50 );
	}

	/**
	 * Saves the edit review form.
	 * 
	 * @since 2.3.0
	 * 
	 * @return void
	 */
	public function edit_review() {
		if ( ! isset( $_POST['_gravityview_ratings_view_id'] ) || (int) $_POST['_gravityview_ratings_view_id'] === 0 ) {
			return;
		}

		check_admin_referer( 'update-comment_' . $_POST['comment_ID'] );

		$view_id = (int) $_POST['_gravityview_ratings_view_id'];

		$comment = get_comment( $_POST['comment_ID'] );
		if ( ! $comment ) {
			wp_die( __( 'Comment ID is not valid.', 'gravityview-ratings-reviews' ) );
		}

		if ( ! $this->is_user_allowed_to_edit_review( $comment, (int) $_POST['post_id'], $view_id ) ) {
			wp_die( __( 'Sorry, you are not allowed to edit this comment.', 'gravityview-ratings-reviews' ) );
		}

		$allow_empty_comments = GravityView_Ratings_Reviews_Review::allow_empty_comment_content( $view_id, $comment->comment_post_ID );
		if ( empty( $_POST['comment_content'] ) && ! $allow_empty_comments ) {
			wp_die( __( 'Sorry, you are not allowed to have empty comment texts.', 'gravityview-ratings-reviews' ) );
		}

		$updated = wp_update_comment( $_POST, true );

		if ( is_wp_error( $updated ) ) {
			wp_die( $updated->get_error_message() );
		}

		do_action( 'gravityview_ratings_review_edited', $comment, $updated );

		$redirect = GravityView_Ratings_Reviews_Helper::get_review_permalink( '', $comment, 'redirect' );
		wp_safe_redirect( $redirect );
		exit;

	}

	/**
	 * Returns how much time is left from comment edit duration.
	 *
	 * @since 2.3.0
	 * 
	 * @param object  $comment
	 * @param integer $edit_duration
	 * 
	 * @return integer
	 */
	private static function get_edit_review_time_left( $comment, $edit_duration ) {
		$commentDate          = strtotime( $comment->comment_date );
		$now                  = current_time( 'timestamp' );
		$timeDifference       = floor( $now - $commentDate );
		$review_edit_duration = intval( $edit_duration ) - $timeDifference;
		return $review_edit_duration;
	}

	/**
	 * Returns review edit buttons.
	 *
	 * @since 2.3.0
	 * 
	 * @param object $comment
	 * 
	 * @return mixed
	 */
	public static function get_review_edit_buttons( $comment ) {
		global $gravityview_view;
		$edit_duration  = $gravityview_view->getAtts( 'allow_reviews_edit_duration' );
		$edit_time_left = self::get_edit_review_time_left( $comment, $edit_duration );

		$output  = sprintf( '<a rel="nofollow" data-commentid="%d" data-postid="%d" class="gv-review-edit-button" href="#">%s</a>', $comment->comment_ID, $comment->comment_post_ID, esc_html__( 'Click to Edit', 'gravityview-ratings-reviews' ) );
		$output .= sprintf( '<time title="%s" class="review-edit-duration" data-edit-time-left="%s"></time>', esc_html__( 'Time left to edit the review', 'gravityview-ratings-reviews' ), $edit_time_left );

		return $output;

	}

	/**
	 * Returns review form.
	 *
	 * @since 2.3.0
	 * 
	 * @param object $comment
	 * @param string $review_title
	 * @param string $review_rate
	 * @param string $review_type
	 * 
	 * @return mixed
	 */
	public static function get_review_edit_form( $comment, $review_title, $review_rate, $review_type ) {
		global $gv_ratings_reviews, $gravityview_view;
		$review_comp = $gv_ratings_reviews->component_instances['review'];

		if ( $review_type === 'vote' ) {
			$review_rate = GravityView_Ratings_Reviews_Helper::get_vote_from_star( $review_rate );
		}

		// Form start
		$output = '<form action="" method="POST" class="gv-review-edit-area" id="gv-review-' . (int) $comment->comment_ID . '" style="display:none;">';

		$output .= wp_nonce_field( 'update-comment_' . $comment->comment_ID );

		// WP Form Fields
		$output .= '
        <input type="hidden" name="redirect_url" value="' . esc_url( get_the_permalink() ) . '" />
		<input type="hidden" name="_gravityview_ratings_view_id" value="' . ( $gravityview_view ? esc_attr( $gravityview_view->view_id ) : '' ) . '" />
		<input type="hidden" name="post_id" value="' . esc_attr( get_the_ID() ) . '" />
		<input type="hidden" name="c" value="' . esc_attr( $comment->comment_ID ) . '" />
		<input type="hidden" name="p" value="' . esc_attr( $comment->comment_post_ID ) . '" />
		<input type="hidden" name="comment_ID" value="' . esc_attr( $comment->comment_ID ) . '" />
		<input type="hidden" name="action" value="editedcomment" />
		<input type="hidden" name="comment_post_ID" value="' . esc_attr( $comment->comment_post_ID ) . '" />
		';

		// Show title and rating edits for main reviews only (not replies)
		if ( (int) $comment->comment_parent === 0 ) {
			// Title section
			$output .= '<div class="gv-review-edit-title">
				<label for="gv_review_title">' . esc_html__( 'Edit Title', 'gravityview-ratings-reviews' ) . '</label>
				<input id="gv_review_title" name="gv_review_title" type="text" size="30" value="' . esc_attr( $review_title ) . '">
			</div>';

		}

		// Rating section
		if ( (int) $comment->comment_parent === 0 && true === GravityView_Ratings_Reviews_Helper::is_ratings_allowed( null, null, $gravityview_view ) ) {

			if ( 'vote' === $review_type ) {
				$rating_field = GravityView_Ratings_Reviews_Helper::get_vote_rating(
					array(
						'rating'    => (int) $review_rate,
						'number'    => 0,
						'clickable' => true,
					)
				);
			} else {
				$rating_field = GravityView_Ratings_Reviews_Helper::get_star_rating(
					array(
						'rating'    => (int) $review_rate,
						'type'      => 'rating',
						'number'    => 0,
						'clickable' => true,
					)
				);
			}

			$output .= sprintf(
				'<div class="gv-review-edit-rate">
					<label for="gv_review_rate">%s</label>
					%s
					<input id="gv_review_rate" name="gv_review_rate" class="gv-star-rate-field" type="hidden" value="%s" />
					<input id="%s" name="%s" type="hidden" value="%s" />
				</div>',
				esc_html__( 'Edit Rate', 'gravityview-ratings-reviews' ),
				$rating_field,
				esc_attr( $review_rate ),
				esc_attr( $review_comp->field_review_type ),
				esc_attr( $review_comp->field_review_type ),
				$review_type
			);
		}

		// Review/Reply section
		$output .= '<div class="gv-review-edit-text">
			<label for="gv-review-edit-text">' . esc_html__( 'Edit ' . ( (int) $comment->comment_parent === 0 ? 'Review' : 'Reply' ) . '', 'gravityview-ratings-reviews' ) . '</label>
			<textarea id="gv-review-edit-text" name="comment_content" class="gv-review-edit-text" cols="45" rows="8">' . esc_html( $comment->comment_content ) . '</textarea>
		</div>';

		// Buttons
		$output .= '<div class="gv-review-edit-buttons">
			<button type="submit" name="save" class="gv-review-edit-save">' . esc_html__( 'Save', 'gravityview-ratings-reviews' ) . '</button>
			<button class="gv-review-edit-cancel">' . esc_html__( 'Cancel', 'gravityview-ratings-reviews' ) . '</button>
		</div>';

		// Form end
		$output .= '</form>';

		return $output;
	}

	/**
	 * Checks whether the current user is allowed to edit a review.
	 *
	 * @since 2.3.0
	 *
	 * @param WP_Comment $review The review (comment) object.
	 * @param int $entry_id The entry that has the reviews.
	 * @param int $view_id The GravityView View ID.
	 *
	 * @return boolean
	 */
	public static function is_user_allowed_to_edit_review( $review, $entry_id, $view_id ) {

		if ( ! is_user_logged_in() ) {
			return apply_filters( 'gv_review_can_edit', false, $review, $entry_id, $view_id );
		}

		if ( current_user_can( 'edit_comment', $review->comment_ID ) ) {
			return apply_filters( 'gv_review_can_edit', true, $review, $entry_id, $view_id );
		}

		$view_settings = GVCommon::get_template_settings( $view_id );

		if ( 1 !== (int) ( $view_settings['allow_reviews_edit'] ?? 0 ) ) {
			return apply_filters( 'gv_review_can_edit', false, $review, $entry_id, $view_id );
		}

		if ( (int) $review->comment_post_ID !== (int) $entry_id ) {
			return apply_filters( 'gv_review_can_edit', false, $review, $entry_id, $view_id );
		}

		$user_id = (int) get_current_user_id();
		if ( (int) $review->user_id !== $user_id ) {
			return apply_filters( 'gv_review_can_edit', false, $review, $entry_id, $view_id );
		}

		$time_diff             = current_time( 'timestamp' ) - strtotime( $review->comment_date );
		$allowed_edit_duration = GravityView_Ratings_Reviews_Meta_Box::DEFAULT_REVIEW_EDIT_DURATION;

		if ( array_key_exists( 'allow_reviews_edit_duration', $view_settings ) ) {
			$allowed_edit_duration = (int) $view_settings['allow_reviews_edit_duration'];
		}

		$review_edit_duration = intval( $allowed_edit_duration ) - $time_diff;

		if ( 0 !== $allowed_edit_duration && $review_edit_duration <= 0 ) {
			return apply_filters( 'gv_review_can_edit', false, $review, $entry_id, $view_id );
		}

		return apply_filters( 'gv_review_can_edit', true, $review, $entry_id, $view_id );
	}
}
