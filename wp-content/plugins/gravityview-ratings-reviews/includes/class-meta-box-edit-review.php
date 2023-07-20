<?php
/**
 * Component that has responsibility to render meta box for review fields when
 * editing a review.
 *
 * @package   GravityView_Ratings_Reviews
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @since 0.1.0
 */

defined( 'ABSPATH' ) || exit;

class GravityView_Ratings_Reviews_Meta_Box_Edit_Review extends GravityView_Ratings_Reviews_Component {

	/**
	 * Callback when this component is loaded by the loader.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function load() {
		// Adds the meta box.
		add_action( 'add_meta_boxes_comment', array( $this, 'add_meta_box' ) );

		$this->load_admin();

	}


	/**
	 * Register admin scripts.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function register_scripts() {
		// Allows support of 'dashicons' for WP < 3.8.
		if ( ! wp_style_is( 'dashicons', 'registered' ) ) {
			wp_register_style( 'dashicons', $this->loader->css_url . 'dashicons.min.css' );
		}

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '-dev.css' : '.css';
		if ( ! wp_style_is( 'gv-ratings-reviews-admin', 'registered' ) ) {
			wp_register_style( 'gv-ratings-reviews-admin', $this->loader->css_url . "admin{$suffix}", array( 'dashicons' ), $this->loader->_version );
		}

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.js' : '.min.js';
		if ( ! wp_script_is( 'gv-ratings-reviews-admin', 'registered' ) ) {
			wp_register_script( 'gv-ratings-reviews-admin', $this->loader->js_url . "admin{$suffix}", array(), $this->loader->_version, true );
		}
	}

	/**
	 * Enqueue the admin scripts if needed.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function enqueue_when_needed() {
		global $wp_scripts, $pagenow;

		if ( in_array( $pagenow, array('comment.php', 'edit-comments.php') ) ) {
			$post_id = null;
			$params = array(
				'screen_id' => 'comment',
			);

			// Edit Comment screen
			if ( isset( $_GET['action'] ) && 'editcomment' === $_GET['action'] && isset( $_GET['c'] ) ) {
				$comment_id = intval( $_GET['c'] );
				$comment = get_comment_to_edit( $comment_id );

				$post_id = $comment->comment_post_ID;

				$params['is_list_reviews'] = false;

			} else if ( ! empty( $_GET['p'] ) ) {
				// View all comments screen for GV Bridge post

				$post_id = intval( $_GET['p'] );

				$params['is_list_reviews'] = true;
			}

			// The Post ID isn't a GV bridge post ID
			if ( ! GravityView_Ratings_Reviews_Helper::is_bridge_post_type( $post_id ) ) {
				return;
			}

			wp_enqueue_style( 'gv-ratings-reviews-admin' );
			wp_enqueue_script( 'gv-ratings-reviews-admin' );

			// Encode parameters for use in the script
			$exports = 'var GV_RATINGS_REVIEWS_ADMIN = ' . json_encode( $params );
			$wp_scripts->add_data( 'gv-ratings-reviews-admin', 'data', $exports );

		}

	}

	/**
	 * Add review meta box when editing review, as long as it's not a comment on a review
	 *
	 * @action add_meta_boxes_comment
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function add_meta_box( $comment ) {

		if ( GravityView_Ratings_Reviews_Helper::is_bridge_post_type( $comment->comment_post_ID ) && empty( $comment->comment_parent ) ) {
			add_meta_box(
				// ID.
				'gravityview_ratings_reviews_edit_review',
				// Title.
				__( 'GravityView - Review Attributes', 'gravityview-ratings-reviews' ),
				// Callback.
				array( $this, 'render_meta_box' ),
				// Screen.
				'comment',
				// Context.
				'normal',
				// Priority.
				'high'
			);
		}
	}

	/**
	 * Display the meta box.
	 *
	 * @since 0.1.0
	 *
	 * @param object $comment Comment object
	 *
	 * @todo Enqueue admin.css and the script to set star rating
	 * @return void
	 */
	public function render_meta_box( $comment ) {

		if ( ! empty( $_GET['review_type'] ) && in_array( $_GET['review_type'], array('stars', 'vote') ) ) {
			$review_type = $_GET['review_type'];
		} else {
			$review_type = 'stars';
		}

		$review_title = get_comment_meta( $comment->comment_ID, 'gv_review_title', true );
		$review_rate  = intval( get_comment_meta( $comment->comment_ID, 'gv_review_rate', true ) );
		$review_comp  = $this->loader->component_instances['review'];

		$fields = array(
			'gv_review_title' => '<p class="comment-form-gv-review comment-form-gv-review-title"><label for="gv_review_title">' . __( 'Title', 'gravityview-ratings-reviews' ) . '</label> ' . '<input id="gv_review_title" name="gv_review_title" type="text" size="30" value="' . esc_attr( $review_title ) . '" /></p>',
		);

		if ( 'vote' === $review_type ) {
			$vote_rate = GravityView_Ratings_Reviews_Helper::get_vote_from_star( $review_rate );
			$fields['gv_review_rate'] = sprintf(
				'<p class="comment-form-gv-review comment-form-gv-review-rate"><label>%s</label>%s %s</p>',
				__( 'Rate', 'gravityview-ratings-reviews' ),
				GravityView_Ratings_Reviews_Helper::get_vote_rating(
					array(
						'rating'    => $vote_rate,
						'number'    => 0,
						'clickable' => true,
					)
				),
				'<input id="gv_review_rate" name="gv_review_rate" class="gv-star-rate-field" type="hidden" value="' . esc_attr( $vote_rate ) . '" />' .
				'<input id="' . esc_attr( $review_comp->field_review_type ) . '" name="' . esc_attr( $review_comp->field_review_type ) . '" type="hidden" value="' . esc_attr( $review_type ) . '" />'
			);
		} else {
			$fields['gv_review_rate'] = sprintf(
				'<p class="comment-form-gv-review comment-form-gv-review-rate"><label>%s</label>%s %s</p>',
				__( 'Rate', 'gravityview-ratings-reviews' ),
				GravityView_Ratings_Reviews_Helper::get_star_rating(
					array(
						'rating'    => $review_rate,
						'type'      => 'rating',
						'number'    => 0,
						'clickable' => true,
					)
				),
				'<input id="gv_review_rate" name="gv_review_rate" class="gv-star-rate-field" type="hidden" value="' . esc_attr( $review_rate ) . '" />' .
				'<input id="' . esc_attr( $review_comp->field_review_type ) . '" name="' . esc_attr( $review_comp->field_review_type ) . '" type="hidden" value="' . esc_attr( $review_type ) . '" />'
			);
		}

		include $this->loader->locate_template('meta-box-edit-review.php');
	}
}
