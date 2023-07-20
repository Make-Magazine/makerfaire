<?php
/**
 * Reviews component.
 *
 * Review is a comment.
 *
 * @package   GravityView_Ratings_Reviews
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @since     0.1.0
 */

defined( 'ABSPATH' ) || exit;

class GravityView_Ratings_Reviews_Review extends GravityView_Ratings_Reviews_Component {

	/**
	 * Field's name that hold View ID value.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $field_view_id = 'gv_view_id';

	/**
	 * Field's name that hold Entry ID value.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $field_entry_id = 'gv_entry_id';

	/**
	 * Field's name that hold post ID containing the View. This might be View ID
	 * or Post ID that has gravityview shortcode being rendered.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $field_post_container_id = 'gv_post_container_id';

	/**
	 * Field's name that hold review's type (e.g., 'vote', 'stars', etc). See
	 * class-meta-box.php for defined review types.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $field_review_type = 'gv_review_type';

	/**
	 * The comment meta key used to store the originating View or Post ID where the comment was submitted
	 *
	 * @since 1.3
	 *
	 * @type string
	 */
	public $review_meta_id = 'gv_post_id';

	/**
	 * Callback when this component is loaded by the loader.
	 *
	 * @return void
	 * @since 0.1.0
	 *
	 */
	public function load() {

		// By default `wp_star_rating` is enabled for admin only.
		require_once ABSPATH . 'wp-admin/includes/template.php';

		$this->gravityview_hooks();
		$this->wp_hooks();
	}

	/**
	 * GravityView hooks.
	 *
	 * @return void
	 * @since 0.1.0
	 *
	 */
	protected function gravityview_hooks() {

		add_action( 'gravityview_before', array( $this, 'start_microdata_wrapper' ) );

		// Render reviews below single entry.
		add_action( 'gravityview_after', array( $this, 'entry_reviews' ), 15 );

		add_action( 'gravityview_after', array( $this, 'end_microdata_wrapper' ), 16 );
	}

	/**
	 * Wrap entry in rich snippets to allow review microdata
	 */
	function start_microdata_wrapper() {

		echo '<div itemscope itemtype="https://schema.org/Thing" id="gv-item-reviewed" itemprop="itemReviewed">';
	}

	/**
	 * Wrap entry in rich snippets to allow review microdata
	 */
	function end_microdata_wrapper() {

		echo '</div>';
	}

	/**
	 * WordPress hooks.
	 *
	 * @return void
	 * @since 0.1.0
	 *
	 */
	protected function wp_hooks() {

		// Comments.
		add_filter( 'preprocess_comment', array( $this, 'preprocess_comment' ) );
		add_filter( 'pre_comment_approved', array( $this, 'pre_comment_approved' ), 10, 2 );
		add_action( 'init', array( $this, 'add_empty_comment_placeholder' ) );
		add_action( 'wp_insert_comment', array( $this, 'remove_empty_comment_placeholder' ), 30, 2 );
		add_action( 'comment_duplicate_trigger', array( $this, 'review_duplicate_message' ) );
		add_filter( 'comment_form_logged_in_after', array( $this, 'review_fields' ) );
		add_filter( 'comment_form_after_fields', array( $this, 'review_fields' ) );
		add_filter( 'comment_post_redirect', array( $this, 'redirect_to_entry' ), 10, 2 );
		add_action( 'comment_form', array( $this, 'inject_fields' ) );
		add_filter( 'comments_open', array( $this, 'comments_open' ), 10, 2 );
		add_filter( 'comment_class', array( $this, 'comment_class' ), 10, 4 );
		add_filter( 'get_comment_link', array( $this, 'get_comment_link' ), 10, 3 );

		add_action( 'comment_trashed_gravityview', array( $this, 'update_counter' ), 15, 2 );
		add_action( 'comment_approved_gravityview', array( $this, 'update_counter' ), 15, 2 );
		add_action( 'wp_insert_comment', array( $this, 'update_counter' ), 15, 2 );
		add_action( 'wp_insert_comment', array( $this, 'update_comment_post_id' ), 17, 2 );
		add_action( 'wp_insert_comment', array( $this, 'update_comment_type' ), 20, 2 );

		add_filter( 'admin_comment_types_dropdown', array( $this, 'admin_add_comment_type' ) );

		add_action( 'gform_delete_lead', array( $this, 'delete_entry_comments' ), 15 );
		add_action( 'gform_update_status', array( $this, 'update_status_entry_comments' ), 15, 3 );

		// Styles and scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Adds the GravityView Ratings and Reviews as a type of Comment to be filtered in the comments listing page
	 *
	 * @param array $options The options to filter
	 *
	 * @filter admin_comment_types_dropdown
	 *
	 * @return array          Options with the GravityView item
	 * @since  0.1.4
	 */
	public function admin_add_comment_type( $options = array() ) {

		$options['gravityview'] = esc_attr__( 'GravityView Reviews', 'gravityview-ratings-reviews' );

		return $options;
	}

	/**
	 * When deleting a Lead/Entry, remove the Bridge post, which will delete the Comments
	 *
	 * @filter gform_delete_lead
	 *
	 * @param int $entry_id Contains id of the entry.
	 *
	 * @return void
	 * @since  0.1.1
	 *
	 * @uses   gravityview_get_entry() Fetch the entry attached to the comment
	 *
	 */
	public function delete_entry_comments( $entry_id = 0 ) {

		$bridge = GravityView_Ratings_Reviews_Helper::get_post_bridge_id( $entry_id );
		wp_delete_post( $bridge, true );
	}

	/**
	 * Change the status of the Comments and Bridge Post based on the Lead/Entry
	 *
	 * @filter preprocess_comment
	 *
	 * @param int    $entry_id       Contains id of the entry.
	 * @param string $property_value Contains the current status of the lead
	 * @param string $previous_value Contains the previous status of the lead
	 *
	 * @return void
	 * @since  0.1.1
	 *
	 * @uses   gravityview_get_entry() Fetch the entry attached to the comment
	 *
	 */
	public function update_status_entry_comments( $entry_id, $property_value, $previous_value = '' ) {

		$bridge = GravityView_Ratings_Reviews_Helper::get_post_bridge_id( $entry_id );

		if ( ! $bridge ) {
			do_action( 'gravityview_log_error', __METHOD__ . ': Bridge not found when updating entry status for entry ID ' . intval( $entry_id ) );

			return;
		}

		if ( 'trash' === $property_value ) {

			wp_trash_post( $bridge );

		} elseif ( 'active' === $property_value ) {

			$untrash = false;

			// The entry was in the trash, so the bridge should be too
			if ( 'trash' === $previous_value ) {
				// Returns false if post wasn't in the trash
				$untrash = wp_untrash_post( $bridge );
			}

			// But if it wasn't in the trash, continue with the publish method
			if ( ! $untrash ) {
				wp_publish_post( $bridge );
			}
		}
	}

	/**
	 * Should empty comments be allowed?
	 *
	 * @param int  $comment_post_ID The bridge post ID
	 * @param null $comment
	 *
	 * @return mixed|void
	 * @since 1.3
	 */
	public function allow_empty_comment_content( $view_id = 0, $comment_post_ID = 0 ) {

		$allow_empty_comments = function_exists( 'gravityview_get_template_setting' ) ? gravityview_get_template_setting( $view_id, 'allow_empty_reviews' ) : false;

		/**
		 * @filter `gv_ratings_reviews_allow_empty_comments` Should empty review comments be allowed?
		 *
		 * @param boolean $allowed         True or false
		 * @param int     $view_id         The ID of the current View
		 * @param int     $comment_post_ID The ID of the submitted comment
		 *
		 * @since  1.3
		 */
		return apply_filters( 'gv_ratings_reviews_allow_empty_comments', ! empty( $allow_empty_comments ), $view_id, $comment_post_ID );
	}

	/**
	 * Remove placeholder comment text
	 *
	 * The placeholder text is added to pass WP validation. Now we remove it after the comment's been otherwise validated.
	 *
	 * @param int    $comment_post_ID Bridge Post ID
	 * @param object $comment         WP Comment object
	 *
	 * @return void
	 * @since 1.3
	 * @see   add_empty_comment_placeholder
	 */
	public function remove_empty_comment_placeholder( $id, $comment ) {

		// Only do this on GravityView comments.
		if ( 'gravityview' !== $comment->comment_type ) {
			return;
		}

		if ( preg_match( '/^<!-- GV\\d+ -->$/us', $comment->comment_content ) ) {

			$comment_array = array(
				'comment_ID'      => $id,
				'comment_post_ID' => $comment->comment_post_ID,
				'comment_content' => '',
			);

			// Comment was updated if value is 1, or was not updated if value is 0.
			$updated = wp_update_comment( $comment_array );

			if ( $updated ) {
				do_action( 'gravityview_log_debug', __METHOD__ . ' Removed empty comment placeholder' );
			} else {
				do_action( 'gravityview_log_error', __METHOD__ . ' Failed to remove empty comment placeholder', $comment_array );
			}
		}
	}

	/**
	 * If empty comments are allowed, add a placeholder comment to trick WP into thinking not empty
	 * This placeholder is then removed in remove_empty_comment_placeholder()
	 *
	 * @param int $comment_post_ID Bridge Post ID
	 *
	 * @return void
	 * @see   remove_empty_comment_placeholder()
	 * @since 1.3
	 */
	public function add_empty_comment_placeholder( $comment_post_ID = 0 ) {

		global $pagenow;

		/** Make sure this is a comment, and that it's our comment */
		if ( 'wp-comments-post.php' !== $pagenow || empty( $_POST[ $this->field_view_id ] ) || empty( $_POST[ $this->field_entry_id ] ) ) {
			return;
		}

		$submitted_comment = isset( $_POST['comment'] ) ? trim( $_POST['comment'] ) : false;

		// Comments on reviews should not be empty
		$has_parent_comment = ! empty( $_POST['comment_parent'] );

		if ( empty( $submitted_comment ) && ! $has_parent_comment && $this->allow_empty_comment_content( absint( $_POST[ $this->field_view_id ] ), $comment_post_ID ) ) {

			/** @internal microtime() is to make it unique so that duplicate comments don't get booted */
			$_POST['comment'] = sprintf( '<!-- GV%s -->', microtime( true ) * 10000 );

			do_action( 'gravityview_log_debug', __METHOD__ . ' Empty comment text; adding placeholder' );
		}
	}

	/**
	 * Filter a comment's data before it is sanitized and inserted into the database.
	 *
	 * @filter preprocess_comment
	 *
	 * @param array $commentdata Contains information on the comment.
	 *
	 * @return array Filtered $commentdata
	 * @since  0.1.0
	 *
	 * @uses   gravityview_get_entry() Fetch the entry attached to the comment
	 */
	public function preprocess_comment( $commentdata ) {

		/** Not a GravityView comment */
		if ( empty( $_POST[ $this->field_view_id ] ) || empty( $_POST[ $this->field_entry_id ] ) ) {
			return $commentdata;
		}

		/**
		 * Existing entry might not have post bridge. Or Post ID being passed is the post ID of the post containing gravityview shortcode.
		 *
		 * @var GravityView_Ratings_Reviews_Post_Bridge $post_bridge
		 */
		$post_bridge = $this->loader->component_instances['post-bridge'];

		if ( $post_bridge->name !== get_post_type( $commentdata['comment_post_ID'] ) ) {

			$entry = gravityview_get_entry( esc_attr( $_POST[ $this->field_entry_id ] ) );

			$bridge_id = $post_bridge->create_bridge( $entry );

			if ( $bridge_id && ! is_wp_error( $bridge_id ) ) {
				$commentdata['comment_post_ID'] = $bridge_id;
			}

		}

		return $commentdata;
	}

	/**
	 * Filter a comment's approval status before it is set.
	 *
	 * @param bool|string $approved    The approval status. Accepts 1, 0, or 'spam'.
	 * @param array       $commentdata Comment data.
	 *
	 * @since 0.1.0
	 *
	 */
	public function pre_comment_approved( $approved, $commentdata ) {

		$post_bridge_comp = $this->loader->component_instances['post-bridge'];

		if ( get_post_type( $commentdata['comment_post_ID'] ) === $post_bridge_comp->name ) {

			$allowed = GravityView_Ratings_Reviews_Helper::is_user_allowed_to_leave_review(
				$commentdata['comment_post_ID'],
				$commentdata['comment_author'],
				$commentdata['comment_author_email'],
				$commentdata
			);

			if ( is_wp_error( $allowed ) ) {
				wp_die( $allowed->get_error_message() );
			}

			if ( ! $allowed ) {
				wp_die( __( 'You have already reviewed this entry.', 'gravityview-ratings-reviews' ) );
			}
		}

		return $approved;
	}

	/**
	 * Updates an existing comment in the database.
	 *
	 * Filters the comment and makes sure certain fields are valid before updating.
	 *
	 * @param array $comment Contains information on the comment.
	 *
	 * @return int Comment was updated if value is 1, or was not updated if value is 0.
	 * @since 2.0.0
	 *
	 * @global wpdb $wpdb    WordPress database abstraction object.
	 *
	 */
	public function update_comment_type( $id, $comment = array() ) {

		// Only process bridge post comments
		if ( ! GravityView_Ratings_Reviews_Helper::is_bridge_post_type( $comment->comment_post_ID ) ) {
			return;
		}

		if ( empty( $comment ) ) {
			$comment = get_comment( $id );
		}

		// Only process bridge post comments
		if ( ! GravityView_Ratings_Reviews_Helper::is_bridge_post_type( $comment->comment_post_ID ) ) {
			return;
		}

		// Update the comment type to GravityView
		$comment->comment_type = 'gravityview';

		// wp_update_comment expects an array
		$updated = wp_update_comment( (array) $comment );

		if ( empty( $updated ) ) {
			do_action( 'gravityview_log_error', __METHOD__ . ' - Comment did not update.', $comment );
		}

	}

	/**
	 * Set the post ID for where the comment was submitted from
	 *
	 * This allows us to properly link back to the entry View or Post/Page where the View is embedded.
	 *
	 * @param int      $id      ID of the comment
	 * @param stdClass $comment Object with comment data (`comment_ID`, `comment_post_ID`, etc)
	 *
	 * @return void
	 * @since  1.3
	 *
	 */
	public function update_comment_post_id( $id = 0, $comment = null ) {

		// Only process bridge post comments without a parent comment
		if ( ! GravityView_Ratings_Reviews_Helper::is_bridge_post_type( $comment->comment_post_ID ) || ! empty( $comment->comment_parent ) ) {
			return;
		}

		// Save the review title
		if ( ! empty( $_POST[ $this->field_post_container_id ] ) ) {
			$comment_post_id = intval( $_POST[ $this->field_post_container_id ] );

			update_comment_meta( $id, $this->review_meta_id, $comment_post_id );
		}
	}

	/**
	 * Set ratings data for each comment
	 *
	 * @param int      $id      ID of the comment
	 * @param stdClass $comment Object with comment data (`comment_ID`, `comment_post_ID`, etc)
	 *
	 * @return void
	 * @since  0.1.1
	 *
	 */
	public function update_counter( $id = 0, $comment = null ) {

		// Only process bridge post comments without a parent comment
		if ( ! GravityView_Ratings_Reviews_Helper::is_bridge_post_type( $comment->comment_post_ID ) || ! empty( $comment->comment_parent ) ) {
			return;
		}

		// Save the review title
		if ( ! empty( $_POST['gv_review_title'] ) ) {
			$comment_title = $_POST['gv_review_title'];
			if ( current_user_can( 'unfiltered_html' ) ) {
				$comment_title = wp_filter_post_kses( $comment_title );
			} else {
				$comment_title = wp_filter_kses( $comment_title );
			}

			update_comment_meta( $id, 'gv_review_title', $comment_title );
		}

		if ( ! empty( $_POST[ $this->field_review_type ] ) && ! empty( $_POST['gv_review_rate'] ) ) {

			switch ( $_POST[ $this->field_review_type ] ) {

				case 'stars':
					$rate = absint( $_POST['gv_review_rate'] );
					break;
				//
				// If review's type is NOT 'stars', we need to convert the value to star
				// based.
				//
				case 'vote':
				default:
					$rate = GravityView_Ratings_Reviews_Helper::get_star_from_vote( $_POST['gv_review_rate'] );
					break;
			}

			// Save the review rating
			update_comment_meta( $id, 'gv_review_rate', $rate );

			// Update the entry meta
			$this->update_entry_meta_counter( $id, $comment );
		}

	}

	/**
	 * Update the entry ratings for a comment.
	 *
	 * @param int      $id      ID of the comment
	 * @param stdClass $comment Object with comment data (`comment_ID`, `comment_post_ID`, etc)
	 *
	 * @return void
	 * @since  0.1.1
	 *
	 */
	function update_entry_meta_counter( $id, $comment ) {

		if ( ! is_object( $comment ) || empty( $comment ) ) {
			$comment = get_comment( $id );
		}
		$entry_id = get_post_meta( $comment->comment_post_ID, 'gf_entry_id', true );

		if ( is_numeric( $entry_id ) ) {
			GravityView_Ratings_Reviews_Helper::refresh_ratings( $comment->comment_post_ID, $entry_id, true );
		}
	}

	/**
	 * Change the message when a duplicate review is detected.
	 *
	 * @action comment_duplicate_trigger
	 *
	 * @param array $commentdata Contains information on the comment.
	 *
	 * @return void
	 * @since  0.1.0
	 *
	 */
	public function review_duplicate_message( $commentdata ) {

		$post        = get_post( $commentdata['comment_post_ID'] );
		$post_bridge = $this->loader->component_instances['post-bridge'];

		if ( $post_bridge->name === get_post_type( $post ) ) {
			wp_die( __( 'Duplicate review detected; it looks as though you&#8217;ve already said that!', 'gravityview-ratings-reviews' ) );
		}
	}

	/**
	 * Filter the default comment form fields to add 'vote' field.
	 *
	 * @return void
	 * @todo   This can be removed/moved since we've implemented our own helper
	 *         `GravityView_Ratings_Reviews::review_form`.
	 *
	 * @since  0.1.0
	 *
	 * @action comment_form_logged_in_after
	 * @action comment_form_after_fields
	 *
	 */
	public function review_fields() {

		global $gravityview_view;

		if ( ! $this->is_single_context() || ! $gravityview_view->getCurrentEntry() ) {
			return;
		}

		$expected_post_types = array(
			'gravityview',
			$this->loader->component_instances['post-bridge']->name,
		);

		if ( ! in_array( get_post_type(), $expected_post_types ) ) {
			return;
		}

		if ( 'vote' === $gravityview_view->getAtts( 'entry_review_type' ) ) {
			$rating_field = GravityView_Ratings_Reviews_Helper::get_vote_rating(
				array(
					'rating'    => 0,
					'number'    => 0,
					'clickable' => true,
				)
			);
		} else {
			$rating_field = GravityView_Ratings_Reviews_Helper::get_star_rating(
				array(
					'rating'    => 0,
					'type'      => 'rating',
					'number'    => 0,
					'clickable' => true,
				)
			);
		}

		$fields                    = array();
		$fields['gv_review_title'] = '<p class="comment-form-gv-review comment-form-gv-review-title"><label for="gv_review_title">' . __( 'Title', 'gravityview-ratings-reviews' ) . '</label> ' . '<input id="gv_review_title" name="gv_review_title" type="text" size="30" /></p>';

		if ( true === GravityView_Ratings_Reviews_Helper::is_ratings_allowed( null, null, $gravityview_view ) ) {
			$fields['gv_review_rate'] = sprintf(
				'<p class="comment-form-gv-review comment-form-gv-review-rate"><label>%s</label>%s %s</p>',
				__( 'Rate', 'gravityview-ratings-reviews' ),
				$rating_field,
				'<input id="gv_review_rate" class="gv-star-rate-field" name="gv_review_rate" type="hidden" />'
			);
		}

		foreach ( $fields as $name => $field ) {
			echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";
		}
	}

	/**
	 * Filter the location URI to send the reviewer back to entry after posting.
	 *
	 * @filter comment_post_redirect
	 *
	 * @param string $location The 'redirect_to' URI sent via $_POST.
	 * @param object $comment  Comment object.
	 *
	 * @return string Location to entry
	 * @since  0.1.0
	 *
	 */
	public function redirect_to_entry( $location, $comment ) {

		return GravityView_Ratings_Reviews_Helper::get_review_permalink( $location, $comment, 'redirect' );
	}

	/**
	 * Inject fields required by ratings-reviews extension at the bottom of the
	 * comment form, inside the closing </form> tag.
	 *
	 * @action comment_form
	 *
	 * @return void
	 * @uses   GravityView_API::get_entry_slug() Get the slug for the entry
	 *
	 * @since  0.1.0
	 *
	 */
	public function inject_fields() {

		global $gravityview_view;

		if ( $this->is_single_context() && $entry = $gravityview_view->getCurrentEntry() ) {

			$entry_slug = GravityView_API::get_entry_slug( $entry['id'], $entry );

			printf( '<input type="hidden" name="%s" value="%s">', 'redirect_to', GravityView_API::entry_link( $entry, wp_cache_get( 'gv_post_container_id' ), true ) );
			printf( '<input type="hidden" name="%s" value="%s">', $this->field_entry_id, $entry_slug );
			printf( '<input type="hidden" name="%s" value="%d">', $this->field_view_id, $gravityview_view->getViewId() );
			printf( '<input type="hidden" name="%s" value="%s">', $this->field_review_type, $gravityview_view->getAtts( 'entry_review_type' ) );
			printf( '<input type="hidden" name="%s" value="%d">', $this->field_post_container_id, wp_cache_get( 'gv_post_container_id' ) );
		}

	}

	/**
	 * Render entry reviews.
	 *
	 * @param int $view_id
	 *
	 * @return void
	 * @todo   Add ability to view existing ratings while not adding new ratings
	 *
	 * @filter gv_ratings_reviews_display_reviews Whether to display the reviews
	 * @action gravityview_after
	 *
	 * @since  0.1.0
	 *
	 */
	public function entry_reviews( $view_id ) {

		$comments_open = $this->comments_open( false, $view_id );

		/**
		 * Whether to display reviews for the View
		 *
		 * @param boolean $display_reviews Whether the commments are open and it's not Edit Entry screen
		 * @param int     $view_id         ID of the View being displayed
		 *
		 * @since 1.0.3-beta
		 */
		$display_reviews = apply_filters( 'gv_ratings_reviews_display_reviews', $comments_open, $view_id );

		if ( $display_reviews ) {

			remove_all_filters( 'comments_open' );

			if ( class_exists( 'Jetpack_Comments' ) ) {
				$jp = new Jetpack_Comments;
				remove_action( 'comment_form_before', array( $jp, 'comment_form_before' ) );
				remove_action( 'comment_form_after', array( $jp, 'comment_form_after' ) );
			}

			add_filter( 'comments_open', '__return_true' );

			// Loads review walker.
			require_once $this->loader->includes_dir . 'class-review-walker.php';

			include $this->loader->locate_template( 'review-list.php' );

			remove_filter( 'comments_open', '__return_true' );
		}

		unset( $comments_open, $display_reviews, $template_path );
	}

	/**
	 * When a View has allow_entry_reviews enabled then allows reviews/comments
	 * to be rendered.
	 *
	 * @filter comments_open
	 *
	 * @param bool $is_open Default status
	 * @param int  $post_id
	 *
	 * @return bool
	 * @since  0.1.0
	 *
	 */
	public function comments_open( $is_open, $post_id ) {
  
		if ( $this->is_single_context() ) {
			$is_open = $this->is_allowable_to_receive_review( $post_id );
		} elseif ( ! empty( $_POST[ $this->field_view_id ] ) ) {
			$is_open = $this->is_allowable_to_receive_review( $_POST[ $this->field_view_id ] );
		}

		return $is_open;
	}

	/**
	 * Adds 'gv-review-item' to review item's class.
	 *
	 * @param array       $classes    An array of comment classes.
	 * @param string      $class      A comma-separated list of additional classes added to the list.
	 * @param int         $comment_id The comment id.
	 * @param int|WP_Post $post_id    The post ID or WP_Post object.
	 *
	 * @return array An array of classes.
	 * @since 0.1.0
	 *
	 */
	public function comment_class( $classes, $class, $comment_id, $post_id ) {

		if ( GravityView_Ratings_Reviews_Helper::get_post_bridge_type() === get_post_type( $post_id ) ) {
			$classes[] = 'gv-review-item';
		}

		return $classes;
	}

	/**
	 * Filter the returned single comment permalink.
	 *
	 * @filter get_comment_link
	 *
	 * @param string $link    The comment permalink with '#comment-$id' appended.
	 * @param object $comment The current comment object.
	 * @param array  $args    An array of arguments to override the defaults.
	 *
	 * @see    get_page_of_comment()
	 *
	 * @since  0.1.0
	 *
	 */
	public function get_comment_link( $link, $comment, $args ) {

		return GravityView_Ratings_Reviews_Helper::get_review_permalink( $link, $comment, 'permalink' );
	}

	/**
	 * Whether a Post is allowable to receive review. This is used by comments_open.
	 *
	 * @param int|WP_Post $post The post ID or WP_Post object.
	 *
	 * @return bool
	 * @since 0.1.0
	 *
	 */
	public function is_allowable_to_receive_review( $post ) {

		$is_allowable = false;

		$post = get_post( $post );
		if ( empty( $post ) || is_wp_error( $post ) ) {
			return $is_allowable;
		}

		if ( $this->is_single_context() || 'gravityview' === get_post_type( $post->ID ) ) {
			$settings     = gravityview_get_template_settings( $post->ID );
			$is_allowable = ! empty( $settings['allow_entry_reviews'] );
		}

		return $is_allowable;
	}

	/**
	 * Is current context is 'single'? Useful when global $gravityview_view object
	 * is instantiated already.
	 *
	 * @return bool
	 * @global GravityView_View $gravityview_view
	 *
	 * @since 0.1.0
	 *
	 */
	public function is_single_context() {

		global $gravityview_view;

		return ( ! empty( $gravityview_view ) && 'single' === $gravityview_view->getContext() );
	}

	/**
	 * Enqueue statics (JS and CSS).
	 *
	 * @action wp_enqueue_scripts
	 *
	 * @return void
	 * @since  0.1.0
	 *
	 */
	public function enqueue_scripts() {

		/**
		 * Fires before required scripts and styles are enqueued.
		 *
		 * @since 0.1.0
		 */
		do_action( 'gravityview_ratings_reviews_before_enqueue' );

		$this->register_scripts();
		$this->enqueue_when_needed();

		/**
		 * Fires after required scripts and styles are enqueued.
		 *
		 * @since 0.1.0
		 */
		do_action( 'gravityview_ratings_reviews_after_enqueue' );
	}

	/**
	 * Register public scripts.
	 *
	 * @return void
	 * @since 0.1.0
	 *
	 */
	protected function register_scripts() {

		// Allows support of 'dashicons' for WP < 3.8.
		if ( ! wp_style_is( 'dashicons', 'registered' ) ) {
			wp_register_style( 'dashicons', $this->loader->css_url . 'dashicons.min.css' );
		}

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '-dev.css' : '.css';
		if ( ! wp_style_is( 'gv-ratings-reviews-public', 'registered' ) ) {
			wp_register_style( 'gv-ratings-reviews-public', $this->loader->css_url . "public{$suffix}", array( 'dashicons' ), $this->loader->_version );
		}

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.js' : '.min.js';
		if ( ! wp_script_is( 'gv-ratings-reviews-public', 'registered' ) ) {
			wp_register_script( 'gv-ratings-reviews-public', $this->loader->js_url . "public{$suffix}", array( 'jquery', 'underscore' ), $this->loader->_version, true );
		}
	}

	/**
	 * Enqueue the public scripts if needed.
	 *
	 * @return void
	 * @since 0.1.0
	 *
	 */
	protected function enqueue_when_needed() {

		global $gravityview_view, $post, $wp_scripts;

		$needed = ( 'gravityview' === get_post_type( $post ) || ! empty( $gravityview_view ) || ( ! empty( $post->post_content ) && has_shortcode( $post->post_content, 'gravityview' ) ) );

		// Checking for the Admin pages
		if ( is_admin() && function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();

			if ( 'comment' === $screen->id && ! empty( $_GET['action'] ) && ! empty( $_GET['c'] ) ) {
				$comment = absint( $_GET['c'] );
				$comment = get_comment( $comment );
				$needed  = ( 'gravityview' === $comment->comment_type );
			}
		}

		if ( ! $needed ) {
			return;
		}

		wp_enqueue_style( 'gv-ratings-reviews-public' );
		wp_enqueue_script( 'gv-ratings-reviews-public' );

		$exports = sprintf( 'var GV_RATINGS_REVIEWS = %s', json_encode( $this->get_exported_vars() ) );
		$wp_scripts->add_data( 'gv-ratings-reviews-public', 'data', $exports );
	}

	/**
	 * Output a complete review form for use within a template.
	 *
	 * This is heavily borrowed from core's comment-template.php
	 *
	 * Most strings and form fields may be controlled through the $args array passed
	 * into the function, while you may also choose to use the comment_form_default_fields
	 * filter to modify the array of default fields if you'd just like to add a new
	 * one or remove a single field. All fields are also individually passed through
	 * a filter of the form comment_form_field_$name where $name is the key used
	 * in the array of fields.
	 *
	 * @param array       $args                 {
	 *                                          Optional. Default arguments and form fields to override.
	 *
	 * @type array        $fields               {
	 *         Default comment fields, filterable by default via the 'comment_form_default_fields' hook.
	 *
	 * @type string       $author               Comment author field HTML.
	 * @type string       $email                Comment author email field HTML.
	 * @type string       $url                  Comment author URL field HTML.
	 *     }
	 * @type string       $comment_field        The comment textarea field HTML.
	 * @type string       $must_log_in          HTML element for a 'must be logged in to comment' message.
	 * @type string       $logged_in_as         HTML element for a 'logged in as <user>' message.
	 * @type string       $comment_notes_before HTML element for a message displayed before the comment form.
	 *                                        Default 'Your email address will not be published.'.
	 * @type string       $comment_notes_after  HTML element for a message displayed after the comment form.
	 *                                        Default 'You may use these HTML tags and attributes ...'.
	 * @type string       $id_form              The comment form element id attribute. Default 'commentform'.
	 * @type string       $id_submit            The comment submit element id attribute. Default 'submit'.
	 * @type string       $name_submit          The comment submit element name attribute. Default 'submit'.
	 * @type string       $title_reply          The translatable 'reply' button label. Default 'Leave a Reply'.
	 * @type string       $title_reply_to       The translatable 'reply-to' button label. Default 'Leave a Reply to %s',
	 *                                        where %s is the author of the comment being replied to.
	 * @type string       $cancel_reply_link    The translatable 'cancel reply' button label. Default 'Cancel reply'.
	 * @type string       $label_submit         The translatable 'submit' button label. Default 'Post a comment'.
	 * @type string       $format               The comment form format. Default 'xhtml'. Accepts 'xhtml', 'html5'.
	 * }
	 *
	 * @param int|WP_Post $post_id              Post ID or WP_Post object to generate the form for. Default current post.
	 *
	 * @since 0.1.0
	 *
	 */
	public static function review_form( $args = array(), $post_id = null ) {

		global $gravityview_view,
			/** @var GravityView_Ratings_Reviews_Loader $gv_ratings_reviews */
			   $gv_ratings_reviews;

		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		$commenter     = wp_get_current_commenter();
		$user          = wp_get_current_user();
		$user_identity = $user->exists() ? $user->display_name : '';

		$args = wp_parse_args( $args );
		if ( ! isset( $args['format'] ) ) {
			$args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
		}

		$req      = get_option( 'require_name_email' );
		$aria_req = ( $req ? " aria-required='true'" : '' );
		$html5    = 'html5' === $args['format'];
		$fields   = array(
			'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'gravityview-ratings-reviews' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
						'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
			'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'gravityview-ratings-reviews' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
						'<input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
			'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website', 'gravityview-ratings-reviews' ) . '</label> ' .
						'<input id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
		);

		$required_text = sprintf( ' ' . __( 'Required fields are marked %s', 'gravityview-ratings-reviews' ), '<span class="required">*</span>' );

		if ( ! empty( $gravityview_view->entries[0] ) ) {
			// gv_entry_link is checking global post, so we need to reset the
			// global post to the view container.
			global $post;

			$current_post = $post;
			$post         = get_post( wp_cache_get( 'gv_post_container_id' ) );
			setup_postdata( $post );

			$permalink = gv_entry_link( $gravityview_view->entries[0] );
			$post      = $current_post;
			setup_postdata( $post );
		} else {
			$permalink = apply_filters( 'the_permalink', get_permalink( $post_id ) );
		}

		/**
		 * Filter the default comment form fields.
		 *
		 * @param array $fields The default comment fields.
		 *
		 * @since 0.1.0
		 *
		 */
		$fields   = apply_filters( 'gv_ratings_reviews_review_form_fields', $fields );
		$defaults = array(
			'fields'                => $fields,
			'comment_field'         => '<p class="comment-form-comment"><label for="comment">' . _x( 'Review', 'noun', 'gravityview-ratings-reviews' ) . '</label> <textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',
			/** This filter is documented in wp-includes/link-template.php */
			'must_log_in'           => '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.', 'gravityview-ratings-reviews' ), wp_login_url( $permalink ) ) . '</p>',
			/** This filter is documented in wp-includes/link-template.php */
			'logged_in_as'          => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'gravityview-ratings-reviews' ), get_edit_user_link(), $user_identity, wp_logout_url( $permalink ) ) . '</p>',
			'comment_notes_before'  => '<p class="comment-notes">' . __( 'Your email address will not be published.', 'gravityview-ratings-reviews' ) . ( $req ? $required_text : '' ) . '</p>',
			'comment_notes_after'   => '<p class="form-allowed-tags">' . sprintf( __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s', 'gravityview-ratings-reviews' ), ' <code>' . allowed_tags() . '</code>' ) . '</p>',
			'id_form'               => 'commentform',
			'id_submit'             => 'submit',
			'name_submit'           => 'submit',
			'title_reply'           => __( 'Review this entry', 'gravityview-ratings-reviews' ),
			'title_reply_to'        => __( 'Reply to %s', 'gravityview-ratings-reviews' ),
			'cancel_reply_link'     => __( 'Cancel reply', 'gravityview-ratings-reviews' ),
			'label_submit'          => __( 'Post Review', 'gravityview-ratings-reviews' ),
			'format'                => 'xhtml',

			/** The message shown to users who try to add two reviews to the same entry. */
			'limited_to_one_review' => '<p class="limited-to-one-review">' . sprintf( __( 'You have already reviewed this entry.', 'gravityview-ratings-reviews' ) ) . '</p>',
		);

		/**
		 * Filter the comment form default arguments.
		 *
		 * Use 'gv_ratings_reviews_review_form_fields' to filter the comment fields.
		 *
		 * @param array $defaults The default comment form arguments.
		 *
		 * @since 1.3
		 *
		 */
		$args = wp_parse_args( $args, apply_filters( 'gv_ratings_reviews_review_form_settings', $defaults ) );

		$template = $gv_ratings_reviews->locate_template( 'review-form.php' );

		include_once $template;
	}

	/**
	 * Get exported vars for JS.
	 *
	 * @return array
	 * @since 0.1.0
	 *
	 */
	protected function get_exported_vars() {

		$vars = array(
			'comment_label_when_reply'      => __( 'Comment', 'gravityview-ratings-reviews' ),
			'comment_submit_when_reply'     => __( 'Post Comment', 'gravityview-ratings-reviews' ),
			'comment_to_review_text'        => __( 'Leave comment on this review', 'gravityview-ratings-reviews' ),
			'cancel_comment_to_review_text' => __( 'Cancel comment', 'gravityview-ratings-reviews' ),
			'vote_text_format'              => __( '<%= number %> rating', 'gravityview-ratings-reviews' ),
			'vote_up'                       => GravityView_Ratings_Reviews_Helper::get_vote_rating_text( 1 ),
			'vote_down'                     => GravityView_Ratings_Reviews_Helper::get_vote_rating_text( - 1 ),
			'vote_zero'                     => GravityView_Ratings_Reviews_Helper::get_vote_rating_text( 0 ),
		);

		/**
		 * Filter the array of data to be localized for javascript
		 *
		 * @param array $vars
		 *
		 * @since 1.3
		 *
		 */
		return apply_filters( 'gv_ratings_reviews_js_vars', $vars );
	}
}
