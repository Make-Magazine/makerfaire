<?php
/**
 * Component helper that provides helper methods.
 *
 * @since     0.1.0
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @package   GravityView_Ratings_Reviews
 */

defined( 'ABSPATH' ) || exit;

class GravityView_Ratings_Reviews_Helper {

	/**
	 * Get post type name of the post bridge.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public static function get_post_bridge_type() {

		/** @global GravityView_Ratings_Reviews_Loader $gv_ratings_reviews */
		global $gv_ratings_reviews;

		return $gv_ratings_reviews->component_instances['post-bridge']->name;
	}

	/**
	 * Check whether a Post is a bridge post type or not
	 *
	 * @param int|WP_Post $post_or_post_id $post object or post ID to check
	 *
	 * @return boolean          True: yep, GV bridge. False: nope.
	 */
	public static function is_bridge_post_type( $post_or_post_id ) {

		$comment_post_type = get_post_type( $post_or_post_id );

		$expected_type = self::get_post_bridge_type();

		return $comment_post_type === $expected_type;
	}

	/**
	 * Retrieve reviews. Post being passed should be the post bridge.
	 *
	 * @since 0.1.0
	 *
	 * @param string      $status  Status for reviews. Default: approve
	 *
	 * @param int|WP_Post $post_id Optional. Post ID or WP_Post object. Default is global $post.
	 * @return array Reviews arrays
	 */
	public static function get_reviews( $post = 0, $status = 'approve' ) {

		/** @global GravityView_Ratings_Reviews_Loader $gv_ratings_reviews */
		global $gv_ratings_reviews;

		$reviews = array();
		$post    = get_post( $post );

		if ( ! $post ) {
			return $reviews;
		}

		if ( $gv_ratings_reviews->component_instances['post-bridge']->name === get_post_type( $post ) ) {

			$defaults = array(
				'post_id' => $post->ID,
				# 'type__in' => array( 'gravityview' ), // TODO: Consider converting to only fetch GravityView comments
				'status'  => $status, // Only get approved comments
			);

			/**
			 * @filter `gv_ratings_reviews_get_reviews_atts` Modify the settings passed to get_comments() when fetching reviews
			 *
			 * @since  2.0.1
			 * @see    get_comments()
			 * @param string      $status Status for reviews. Default: approve
			 *
			 * @param array       $atts   Settings for get_comments()
			 * @param int|WP_Post $post   WP_Post Post Bridge object.
			 */
			$atts = apply_filters( 'gv_ratings_reviews_get_reviews_atts', $defaults );

			// Sanity check
			if ( empty( $atts ) ) {
				$atts = $defaults;
			}

			$reviews = get_comments( $atts );
		}

		return $reviews;
	}

	/**
	 * Get the comment data connected to a post bridge
	 *
	 * @param int     $bridge_post_id ID of the Post Bridge post
	 * @param string  $status         Status for reviews. Default: approve
	 * @param boolean $add_metadata   Whether to include comment meta. Default: true
	 *
	 * @return array Array of comments with metadata as well
	 */
	public static function get_reviews_data( $bridge_post_id, $status = 'approve', $add_metadata = true ) {

		$comments = self::get_reviews( $bridge_post_id, $status );

		$comments_dump = array();

		foreach ( $comments as $comment ) {
			$data = array();

			foreach ( $comment as $key => $value ) {
				$data[ str_replace( 'comment_', '', $key ) ] = $value;
			}

			if ( $add_metadata ) {
				$data['meta'] = get_comment_meta( $comment->comment_ID );
			}

			$comments_dump[] = $data;
		}

		return $comments_dump;
	}

	/**
	 * Retrieve the amount of reviews an entry has. Post being passed should be
	 * the post bridge.
	 *
	 * @since 0.1.0
	 *
	 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
	 *
	 * @return int
	 */
	public static function get_reviews_number( $post ) {

		$reviews     = self::get_reviews( $post );
		$parent_only = array_filter( $reviews, array( __CLASS__, 'filter_parent' ) );

		return count( $parent_only );
	}

	/**
	 * Callback for array_filter to filter comment_parent equal to zero, as non-zero
	 * comment_parent is a review's comment.
	 *
	 * @since 0.1.0
	 *
	 * @param object $comment
	 *
	 * @return bool
	 */
	public static function filter_parent( $comment ) {

		return ! $comment->comment_parent;
	}

	/**
	 * Display the language string for the number of reviews the current entry has.
	 *
	 * @since 0.1.0
	 *
	 * @param string      $zero Optional. Text for no comments. Default false.
	 * @param string      $one  Optional. Text for one comment. Default false.
	 * @param string      $more Optional. Text for more than one comment. Default false.
	 *
	 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
	 * @return string
	 */
	public static function get_reviews_number_text( $post, $zero = false, $one = false, $more = false ) {

		$number = self::get_reviews_number( $post );

		if ( $number > 1 ) {
			$output = str_replace( '%', number_format_i18n( $number ), ( false === $more ) ? __( '% Reviews', 'gravityview-ratings-reviews' ) : $more );
		} elseif ( 0 === intval( $number ) ) {
			$output = ( false === $zero ) ? __( 'No Reviews', 'gravityview-ratings-reviews' ) : $zero;
		} else { // must be one
			$output = ( false === $one ) ? __( '1 Review', 'gravityview-ratings-reviews' ) : $one;
		}

		/**
		 * Filter the comments count for display.
		 *
		 * @since 0.1.0
		 *
		 * @param int    $number The number of entry reviews.
		 *
		 * @param string $output A translatable string formatted based on whether the count
		 *                       is equal to 0, 1, or 1+.
		 */
		return apply_filters( 'gv_reviews_number', $output, $number );
	}

	/**
	 * Display the language string for the number of comments the current post has.
	 *
	 * @since 0.1.0
	 *
	 * @param string      $zero    Optional. Text for no comments. Default false.
	 * @param string      $one     Optional. Text for one comment. Default false.
	 * @param string      $more    Optional. Text for more than one comment. Default false.
	 *
	 * @param int|WP_Post $post_id Optional. Post ID or WP_Post object. Default is global $post.
	 * @return void
	 */
	public static function the_reviews_number_text( $post, $zero = false, $one = false, $more = false ) {

		echo self::get_reviews_number_text( $post, $zero, $one, $more );
	}

	/**
	 * Output link to entry's reviews.
	 *
	 * @since 0.1.0
	 *
	 * @see   get_reviews_link
	 * @return void
	 */
	public static function the_reviews_link() {

		echo self::get_the_reviews_link();
	}

	/**
	 * Get the average rating array for an entry
	 *
	 * @since 1.3
	 * @param array $entry Gravity Forms entry. If not set, uses GravityView_View::getCurrentEntry()
	 *
	 * @return array Array with 'detail_stars' ( the number of votes at each star level ), 'detail_vote' (number of votes
	 * for each voting option), 'average_stars' (the aggregate star rating), 'average_vote' (the aggregate vote count), and 'total_voters'
	 * @uses  get_review_average_rating()
	 *
	 * @uses  get_post_bridge_id()
	 * @uses  GravityView_View::getCurrentEntry()
	 */
	public static function get_entry_average_rating( $entry = array() ) {

		if ( empty( $entry ) ) {
			do_action( 'gravityview_log_debug', __METHOD__ . ': $entry not passed; fetching current entry' );
			$entry = GravityView_View::getInstance()->getCurrentEntry();
		}

		if ( empty( $entry ) ) {
			do_action( 'gravityview_log_error', __METHOD__ . ': $entry is empty' );

			return array();
		}

		// Post ID that links entry with comments.
		$post_bridge_id = self::get_post_bridge_id( $entry['id'], true );

		// Replaces current post with bridge post.
		$post = get_post( $post_bridge_id );

		$average = array();
		if ( $post && ! is_wp_error( $post ) ) {
			setup_postdata( $post );
			$average = self::get_review_average_rating( $post );
		} elseif ( is_wp_error( $post ) ) {
			do_action( 'gravityview_log_error', __METHOD__ . ': $post is error', $post );
		}

		wp_reset_postdata();

		return $average;
	}

	/**
	 * Generate a link to an entry's reviews.
	 *
	 * @since 1.0.4
	 *
	 * @return string
	 */
	public static function get_the_reviews_link() {

		$gravityview_view = GravityView_View::getInstance();

		$fs = wp_parse_args(
			$gravityview_view->getCurrentField( 'field_settings' ),
			array(
				'no_comment_text'     => __( 'Leave a Review', 'gravityview-ratings-reviews' ),
				'one_comment_text'    => __( '1 Review', 'gravityview-ratings-reviews' ),
				'more_comments_text'  => __( '% Reviews', 'gravityview-ratings-reviews' ),
				'show_average_rating' => true,
			)
		);

		$entry = $gravityview_view->getCurrentEntry();

		// This is added to GravityView 1.16 for DataTables support. Can be removed in the future.
		if ( empty( $entry ) && ! empty( $gravityview_view->_current_field['entry'] ) ) {
			$entry = $gravityview_view->_current_field['entry'];
		}

		$field = $gravityview_view->getCurrentField();
		$type  = $gravityview_view->getAtts( 'entry_review_type' );

		// Link to entry detail.
		$href = GravityView_API::entry_link( $entry, $field );

		// Post ID that links entry with comments.
		$post_bridge_id = self::get_post_bridge_id( $entry['id'] );

		// Replaces current post with bridge post.
		$post = get_post( $post_bridge_id );

		$return_link = '';

		if ( $post && ! is_wp_error( $post ) ) {
			setup_postdata( $post );

			$average = self::get_review_average_rating( $post );
			if ( $fs['show_average_rating'] && ! empty( $average['total_voters'] ) ) {

				if ( 'vote' === $type ) {
					self::the_vote_rating(
						array(
							'rating' => $average['average_vote'],
							'number' => $average['total_voters'],
						)
					);
				} else {
					self::the_star_rating(
						array(
							'rating' => $average['average_stars'],
							'type'   => 'rating',
							'number' => $average['total_voters'],
						)
					);
				}
			}

			if ( true === GravityView_Ratings_Reviews_Helper::is_reviews_allowed( null, null, $gravityview_view ) ) {

				$form = (array) GFAPI::get_form( $entry['form_id'] );

				$no_comment_text = GFCommon::replace_variables( $fs['no_comment_text'], $form, $entry );
				$one_comment_text = GFCommon::replace_variables( $fs['one_comment_text'], $form, $entry );
				$more_comments_text = GFCommon::replace_variables( $fs['more_comments_text'], $form, $entry );

				$anchor_text = self::get_reviews_number_text( $post, $no_comment_text, $one_comment_text, $more_comments_text );
			} else {
				$anchor_text = self::get_reviews_number_text( $post, false, false, false );
			}

			$return_link = gravityview_get_link(
				$href . '#gv-entry-reviews',
				$anchor_text,
				array(
					'title' => __( 'Reviews of this entry', 'gravityview-ratings-reviews' ),
				)
			);

		}

		return $return_link;
	}

	/**
	 * Get review permalink. If the comment is not a review, then original
	 * location will be returned.
	 *
	 * This is used by 'review' component to get proper review permalink
	 * and after-saved redirect.
	 *
	 * When `is_admin()` the permalink will be set to view entry page.
	 *
	 * @since 0.1.0
	 *
	 * @see   GravityView_Ratings_Reviews_Review::redirect_to_entry
	 * @see   GravityView_Ratings_Reviews_Review::get_comment_link
	 *
	 * @param object $review   Comment object
	 *
	 * @param string $location Original location
	 * @return string
	 */
	public static function get_review_permalink( $location, $review, $action = 'permalink' ) {

		global $gv_ratings_reviews, $pagenow;

		$bridge_post_id = $review->comment_post_ID;

		/** @var GravityView_Ratings_Reviews_Post_Bridge $post_bridge */
		$post_bridge = $gv_ratings_reviews->component_instances['post-bridge'];

		/** @var GravityView_Ratings_Reviews_Review $review_comp */
		$review_comp = $gv_ratings_reviews->component_instances['review'];

		/** We're dealing with a Post Bridge comment. */
		if ( $post_bridge->name === get_post_type( $bridge_post_id ) ) {

			$entry_id = absint( get_post_meta( $bridge_post_id, $post_bridge->entry_id_meta_key, true ) );

			if ( is_admin() ) {
				$location = self::get_entry_admin_url( $entry_id );
			} else {

				/**
				 * If redirect_to is set, use it.
				 *
				 * @since 1.3
				 */
				if ( ! empty( $_POST['redirect_to'] ) ) {

					/**
					 * @internal No need to sanitize; it'll be handled by wp_safe_redirect()
					 * @file     wp-comments-post.php
					 */
					$location = $_POST['redirect_to'];

					// Don't keep processing after redirect is calculated
					if ( 'redirect' === $action ) {
						unset( $_POST['redirect_to'] );
					}

				} else {

					// Recent comments widget
					if ( in_array( current_filter(), array( 'get_comment_link', 'comment_notification_text' ) ) ) {
						$post_id = get_comment_meta( $review->comment_ID, $review_comp->review_meta_id, true );
					} else {
						$post_id = wp_cache_get( 'gv_post_container_id' );

						if ( ! $post_id ) {
							$post_id = get_queried_object_id();
						}
					}

					$entry    = gravityview_get_entry( $entry_id, true, false );
					$location = gv_entry_link( $entry, $post_id );
				}

				$location .= '#review-' . $review->comment_ID;

			}
		}

		return $location;
	}

	/**
	 * Retrieve edit review link.
	 *
	 * @since 0.1.0
	 *
	 * @param array $args       Optional. Args to be passed as query string
	 *
	 * @param int   $comment_id Optional. Comment ID.
	 * @return string
	 */
	public static function get_edit_review_link( $comment_id = 0, $args = array() ) {

		$comment = get_comment( $comment_id );

		if ( ! current_user_can( 'edit_comment', $comment->comment_ID ) ) {
			return;
		}

		$location = admin_url( 'comment.php?action=editcomment&amp;c=' ) . $comment->comment_ID;

		if ( ! empty( $args ) ) {
			$location = add_query_arg( $args, $location );
		}

		/**
		 * Filter the comment edit link.
		 *
		 * @param string $location The edit link.
		 */
		return apply_filters( 'get_edit_comment_link', $location );
	}

	/**
	 * Display edit review link with formatting.
	 *
	 * @since 0.1.0
	 *
	 * @param string $before Optional. Display before edit link.
	 * @param string $after  Optional. Display after edit link.
	 * @param array  $args   Optional. Args to be passed as query string
	 *
	 * @param string $text   Optional. Anchor text.
	 * @return void
	 */
	public static function edit_review_link( $text = null, $before = '', $after = '', $args = array() ) {

		global $comment;

		if ( ! current_user_can( 'edit_comment', $comment->comment_ID ) ) {
			return;
		}

		if ( null === $text ) {
			$text = __( 'Edit This', 'gravityview-ratings-reviews' );
		}

		$link = '<a class="comment-edit-link" href="' . self::get_edit_review_link( $comment->comment_ID, $args ) . '">' . $text . '</a>';

		/**
		 * Filter the comment edit link anchor tag.
		 *
		 * @since 0.1.0
		 *
		 * @param int    $comment_id Comment ID.
		 * @param string $text       Anchor text.
		 *
		 * @param string $link       Anchor tag for the edit link.
		 */
		echo $before . apply_filters( 'edit_comment_link', $link, $comment->comment_ID, $text ) . $after;
	}

	/**
	 * Get GF view entry admin URL.
	 *
	 * @since 0.1.0
	 *
	 * @param string $mode     Screen mode. Can be 'edit'.
	 *
	 * @param int    $entry_id GF entry ID
	 * @return string URL to view entry in admin. If entry or form not found, it's an inline javascript alert URL.
	 */
	public static function get_entry_admin_url( $entry_id, $mode = '' ) {

		$js_error = '';
		$form     = false;

		if ( class_exists( 'GFAPI' ) ) {

			$entry = GFAPI::get_entry( $entry_id );

			if ( ! $entry || is_wp_error( $entry ) ) {
				$js_error = __( 'This entry no longer exists.', 'gravityview-ratings-reviews' );
			} else {

				$form = GFAPI::get_form( $entry['form_id'] );

				if ( ! $form || is_wp_error( $form ) ) {
					$js_error = __( 'The form connected to this entry no longer exists.', 'gravityview-ratings-reviews' );
				}
			}

		} else {
			$js_error = __( 'Gravity Forms must be active to view this entry.', 'gravityview-ratings-reviews' );
		}

		// If there's an error, return early
		if ( ! empty( $js_error ) ) {
			return 'javascript:alert("' . esc_html( $js_error ) . '");';
		}

		if ( ! in_array( $mode, array( 'edit' ) ) ) {
			$mode = '';
		}

		return admin_url(
			sprintf(
				'admin.php?page=%s&view=%s&id=%d&lid=%d&screen_mode=%s',
				'gf_entries',
				'entry',
				absint( $form['id'] ),
				absint( $entry_id ),
				$mode
			)
		);
	}

	/**
	 * Retrieve reviews. Post being passed should be the post bridge.
	 *
	 * @since 0.1.0
	 *
	 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
	 *
	 * @return array
	 */
	public static function get_review_average_rating( $post = 0 ) {

		global $wpdb, $gv_ratings_reviews;

		//
		// The structure of the result.
		//
		// The suffix should matches with 'Review type'
		// found in 'Ratings & Reviews' meta box.
		//
		$result = array(
			'detail_stars'  => array_fill( 1, 5, 0 ),
			'detail_vote'   => array( 'down' => 0, 'up' => 0 ),
			'average_stars' => 0,
			'average_vote'  => 0,
			'total_voters'  => 0, // Doesn't count someone that doesn't leave a rate.
		);

		$post_bridge_comp = $gv_ratings_reviews->component_instances['post-bridge'];
		$review_comp      = $gv_ratings_reviews->component_instances['review'];

		$post = get_post( $post );
		if ( $post_bridge_comp->name !== get_post_type( $post ) ) {
			return $result;
		}

		// Checks if results are available from cache.
		$last_changed = wp_cache_get( 'last_changed', 'comment' );
		if ( ! $last_changed ) {
			$last_changed = microtime();
			wp_cache_set( 'last_changed', $last_changed, 'comment' );
		}
		$cache_key = "gv_ratings_reviews:get_review_average_rating:$post->ID:$last_changed";

		if ( $cache = wp_cache_get( $cache_key ) ) {
			return $cache;
		}

		// TODO: Convert away from SQL
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT
					cm.meta_value as star,
					COUNT(cm.meta_value) as count FROM $wpdb->comments c
				LEFT JOIN $wpdb->posts p ON c.comment_post_ID = p.ID
				LEFT JOIN $wpdb->commentmeta cm ON cm.comment_id = c.comment_ID
				WHERE
					c.comment_approved = '1'
					AND
					p.post_type = %s
					AND
					p.ID = %d
					AND
					p.post_status = 'publish'
					AND
					c.comment_parent = 0
					AND
					cm.meta_key = 'gv_review_rate'
				GROUP BY cm.meta_value
				",
				$post_bridge_comp->name,
				$post->ID
			),
			ARRAY_A
		);

		$total_stars = 0;
		$total_votes = 0;
		$total_count = 0;
		foreach ( $rows as $row ) {
			$star  = intval( $row['star'] );
			$count = intval( $row['count'] );
			$vote  = self::get_vote_from_star( $star );

			if ( 0 !== $star ) {
				$result['detail_stars'][ $star ] += $count;
			}

			if ( 1 === $vote ) {
				$result['detail_vote']['up'] += $count;
			} elseif ( - 1 === $vote ) {
				$result['detail_vote']['down'] += $count;
			}

			$total_count += $count;
			$total_stars += $star * $count;
			$total_votes += $vote * $count;
		}
		$result['average_stars'] = $total_count > 0 ? $total_stars / $total_count : 0;
		$result['average_vote']  = $total_votes;
		$result['total_voters']  = $total_count;

		wp_cache_add( $cache_key, $result );

		return $result;
	}

	/**
	 * Returns post ID that links entry with comments.
	 *
	 * @since 1.3 Added $create_if_not_exists
	 *
	 * @since 0.1.0
	 * @param mixed   $entry_id             GF Entry ID
	 * @param boolean $create_if_not_exists If the post bridge doesn't exist, create one?
	 *
	 * @return mixed
	 */
	public static function get_post_bridge_id( $entry_id = null, $create_if_not_exists = false ) {

		/** @global GravityView_Ratings_Reviews_Loader $gv_ratings_reviews */
		global $gv_ratings_reviews;

		/** When deleting bulk entries, GV may not have initiated yet. */
		if ( ! class_exists( 'GravityView_View' ) && defined( 'GRAVITYVIEW_DIR' ) ) {
			include_once( GRAVITYVIEW_DIR . 'includes/class-template.php' );
		}

		$gravityview_view = GravityView_View::getInstance();

		if ( null === $entry_id
			 && 'single' === $gravityview_view->getContext()
			 && ! empty( $gravityview_view->entries[0] )
		) {

			$entry_id = $gravityview_view->entries[0]['id'];
		}

		$post_bridge = gform_get_meta( $entry_id, $gv_ratings_reviews->component_instances['post-bridge']->post_id_meta_key );

		if ( ! $post_bridge && $create_if_not_exists ) {
			$post_bridge = $gv_ratings_reviews->component_instances['post-bridge']->create_bridge( $entry_id );
		}

		return $post_bridge;
	}

	/**
	 * Get the titles for the star ratings
	 *
	 * Users may want to modify the rating structure
	 *
	 * @param int|null $number If defined, the numeric value of the star to get the rating for (1-5)
	 *
	 * @return string|array         If $number is defined, the title for a star rating with the value of $number. Otherwise, array of all ratings.
	 */
	private static function get_star_rating_title( $number = null ) {

		$original_star_titles = array(
			1 => _x( '1 star', 'Rating description shown when hovering over a star', 'gravityview-ratings-reviews' ),
			2 => _x( '2 stars', 'Rating description shown when hovering over a star', 'gravityview-ratings-reviews' ),
			3 => _x( '3 stars', 'Rating description shown when hovering over a star', 'gravityview-ratings-reviews' ),
			4 => _x( '4 stars', 'Rating description shown when hovering over a star', 'gravityview-ratings-reviews' ),
			5 => _x( '5 stars', 'Rating description shown when hovering over a star', 'gravityview-ratings-reviews' ),
		);

		/**
		 * @filter `gv_ratings_reviews_star_rating_titles` Filter the star rating hover titles.
		 *
		 * Make sure to keep the array key numbers intact; they map with the star rating.
		 *
		 * You can set an empty string to disable the title attribute from appearing in GravityView_Ratings_Reviews_Helper::get_star_rating()
		 *
		 * @link   https://gist.github.com/zackkatz/0f160dd235049b59f775 Example filter use
		 *
		 * @since  0.1.1
		 *
		 * @param array $stars The array of star ratings
		 */
		$star_titles = apply_filters( 'gv_ratings_reviews_star_rating_titles', $original_star_titles );

		// If number is defined, return the title for that number
		if ( $number ) {

			if ( isset( $star_titles[ $number ] ) ) {
				return $star_titles[ $number ];
			} elseif ( isset( $original_stars[ $number ] ) ) {
				return $original_stars[ $number ];
			} else {
				return null;
			}
		}

		// Otherwise, return the whole array
		return $star_titles;
	}

	/**
	 * Get a HTML element with a star rating for a given rating. Copied from
	 * wp_star_rating with a bit of sugar addition.
	 *
	 * @since 0.1.0
	 *
	 * @param array $args         {
	 *                            Optional. Array of star ratings arguments.
	 *
	 * @type int    $rating       The rating to display, expressed in either a 0.5 rating increment,
	 *                                or percentage. Default 0.
	 * @type string $type         Format that the $rating is in. Valid values are 'rating' (default),
	 *                                or, 'percent'. Default 'rating'.
	 * @type int    $number       The number of ratings that makes up this rating. Default 0.
	 * @type bool   $clickable    Whether the star is clickable or not. Default false.
	 * @type bool   $display_text Whether to show the text. Default false.
	 * }
	 *
	 * @return string
	 */
	public static function get_star_rating( $args = array() ) {

		global $gravityview_view;

		$defaults = array(
			'rating'       => 0,
			'type'         => 'rating',
			'number'       => 0,
			'clickable'    => false,
			'display_text' => false,
		);
		$r        = wp_parse_args( $args, $defaults );

		$output = '';

		// Non-english decimal places when the $rating is coming from a string.
		$rating = str_replace( ',', '.', $r['rating'] );

		// Convert Percentage to star rating, 0..5 in .5 increments
		if ( 'percent' === $r['type'] ) {
			$rating = round( $rating / 10, 0 ) / 2;
		}

		if ( $r['number'] ) {
			/* translators: 1: The rating, 2: The number of ratings */
			$format = _n( '%1$s rating based on %2$s rating', '%1$s rating based on %2$s ratings', $r['number'], 'gravityview-ratings-reviews' );
			$title  = sprintf( $format, number_format_i18n( $rating, 1 ), number_format_i18n( $r['number'] ) );
		} else {
			/* translators: 1: The rating */
			$title = sprintf( __( '%s rating', 'gravityview-ratings-reviews' ), number_format_i18n( $rating, 1 ) );
		}

		if ( $r['clickable'] ) {
			$entry_id = ( ! empty( $r['entry_id'] ) ) ? sprintf( 'data-entry-id="%d"', $r['entry_id'] ) : '';
			$view_id  = sprintf( 'data-view-id="%d"', $gravityview_view->view_id );

			$output .= sprintf( '<div class="gv-star-rating gv-star-rate-holder" %s %s>', $entry_id, $view_id );

			$format = '<span class="gv-star-rate"%s>&#61780;</span>';

			$star_count = 1;
			while ( $star_count <= 5 ) {

				$title = self::get_star_rating_title( $star_count );

				// You can set an empty string to disable the title attribute.
				if ( ! empty( $title ) ) {
					$title = ' title="' . esc_attr( $title ) . '"';
				}

				$output .= sprintf( $format, $title );

				$star_count ++;
			}

			$output .= '</div>';
		} else {

			// If the number is passed, this is displaying the aggregate rating
			if ( $r['number'] ) {
				$output .= '<div class="gv-star-rating" title="' . esc_attr( $title ) . '" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';
				$output .= '<meta itemprop="reviewCount" content="' . $r['number'] . '">';
			} // Otherwise, just a single rating
			else {
				$output .= '<div class="gv-star-rating" title="' . esc_attr( $title ) . '" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">';
			}

			// Calculate the number of each type of star needed
			$full_stars  = floor( $rating );
			$half_stars  = ceil( $rating - $full_stars );
			$empty_stars = 5 - $full_stars - $half_stars;

			$output .= '<span class="screen-reader-text">' . $title . '</span>';
			$output .= '<meta itemprop="ratingValue" content="' . round( $rating, 3 ) . '">';
			$output .= str_repeat( '<span class="gv-star gv-star-full"></span>', $full_stars );
			$output .= str_repeat( '<span class="gv-star gv-star-half"></span>', $half_stars );
			$output .= str_repeat( '<span class="gv-star gv-star-empty"></span>', $empty_stars );
			$output .= '</div>';
		}

		if ( $r['display_text'] ) {
			$output .= '<div class="gv-star-rating-text">' . esc_html( $title ) . '</div>';
		}

		/**
		 * Filter for HTML element with a star rating.
		 *
		 * @since 0.1.0
		 * @param array  $r      Optional array of star ratings arguments.
		 *
		 * @param string $output HTML element with a star rating
		 *
		 */
		return apply_filters( 'gv_ratings_reviews_star_rating', $output, $r );
	}

	/**
	 * Outputs a HTML element with the star rating exposed on a 0..5 scale in
	 * half star increments (ie. 1, 1.5, 2 stars). Optionally, if specified, the
	 * number of ratings may also be displayed by passing the $number parameter.
	 *
	 * @since 0.1.0
	 *
	 * @param array $args         {
	 *                            Optional. Array of star ratings arguments.
	 *
	 * @type int    $rating       The rating to display, expressed in either a 0.5 rating increment,
	 *                                or percentage. Default 0.
	 * @type string $type         Format that the $rating is in. Valid values are 'rating' (default),
	 *                                or, 'percent'. Default 'rating'.
	 * @type int    $number       The number of ratings that makes up this rating. Default 0.
	 * @type bool   $clickable    Whether the star is clickable or not. Default false.
	 * @type bool   $display_text Whether to show the text. Default false.
	 * }
	 *
	 * @return void
	 */
	public static function the_star_rating( $args = array() ) {

		echo self::get_star_rating( $args );
	}

	/**
	 * Outputs a HTML element with the star rating exposed on a 0..5 scale in
	 * half star increments (ie. 1, 1.5, 2 stars). Optionally, if specified, the
	 * number of ratings may also be displayed by passing the $number parameter.
	 *
	 * @since 1.3
	 *
	 * @param array $entry Gravity Forms Entry array
	 *
	 * @return string Star rating from get_star_rating()
	 * @uses  get_entry_average_rating
	 * @uses  get_star_rating
	 */
	public static function get_star_rating_for_entry( $entry = array() ) {

		$average = GravityView_Ratings_Reviews_Helper::get_entry_average_rating( $entry );

		// There was a problem.
		if ( ! isset( $average['average_stars'] ) ) {
			do_action( 'gravityview_log_error', 'Error fetching average rating for entry' );

			return '';
		}

		do_action( 'gravityview_log_debug', 'Average rating array for entry', array( '$entry' => $entry, '$average' => $average ) );

		$args = array(
			'rating' => $average['average_stars'],
			'type'   => 'rating',
			'number' => $average['total_voters'],
		);

		return self::get_star_rating( $args );
	}

	/**
	 * Get the text used to display positive and negative ratings
	 *
	 * @param boolean $type Use "up" for positive or "down" for negative. Up default.
	 *
	 * @return string "+1" and "-1" are defaults
	 */
	public static function get_vote_rating_text( $rating_value = 1, $positive = true ) {

		$rating_abs = absint( $rating_value );

		$original_rating_text = array(
			'up'   => sprintf( __( '+%d', 'gravityview-ratings-reviews' ), number_format_i18n( $rating_abs ) ),
			'down' => sprintf( __( '-%d', 'gravityview-ratings-reviews' ), number_format_i18n( $rating_abs ) ),
			'zero' => __( 'No Rating', 'gravityview-ratings-reviews' ),
		);

		/**
		 * @filer `gv_ratings_reviews_vote_rating_text` Filter the vote rating text.
		 *
		 * Make sure to keep the array key numbers intact; they map with the vote ratings.
		 *
		 * @param array $original_rating_text {
		 *
		 * @type string $up                   The text to show when the rating is positive. Default: "+1"
		 * @type string $down                 The text to show when the rating is negative. Default: "-1"
		 * @type string $down                 The text to show when there is no rating. Default: "No Rating"
		 * }
		 */
		$rating_text = apply_filters( 'gv_ratings_reviews_vote_rating_text', $original_rating_text );

		if ( $rating_value > 0 ) {
			return $rating_text['up'];
		} elseif ( $rating_value < 0 ) {
			return $rating_text['down'];
		} else {
			return $rating_text['zero'];
		}
	}

	/**
	 * Get a vote HTML element.
	 *
	 * @since 0.1.0
	 *
	 * @param array $args         {
	 *                            Optional. Array of star ratings arguments.
	 *
	 * @type int    $rating       The rating to display. Default 0.
	 * @type int    $number       The number of ratings that makes up this rating. Default 0.
	 * @type bool   $clickable    Whether the vote is clickable or not. Default false.
	 * @type bool   $display_text Whether to show the text. Default false.
	 * }
	 *
	 * @return string
	 */
	public static function get_vote_rating( $args = array() ) {

		global $gravityview_view;

		$defaults = array(
			'rating'       => 0,
			'number'       => 0,
			'clickable'    => false,
			'display_text' => false,
		);

		$r                  = wp_parse_args( $args, $defaults );
		$is_positive_rating = ( $r['rating'] > 0 );
		$is_negative_rating = ( $r['rating'] < 0 );

		$r['rating'] = intval( $r['rating'] );
		$r['number'] = intval( $r['number'] );

		if ( $r['number'] ) {
			$rating_string = self::get_vote_rating_text( $r['rating'] );

			/* translators: 1: The rating, 2: The number of ratings */
			$format = _n( '%1$s rating based on %2$s vote', '%1$s rating based on %2$s votes', $r['number'], 'gravityview-ratings-reviews' );
			$title  = sprintf( $format, $rating_string, number_format_i18n( $r['number'] ) );
		} else {
			/* translators: 1: The rating */
			$title = sprintf( __( '%s rating', 'gravityview-ratings-reviews' ), number_format_i18n( $r['rating'] ) );
		}

		$output = '';
		if ( $r['clickable'] ) {
			$entry_id = ( ! empty( $r['entry_id'] ) ) ? sprintf( 'data-entry-id="%d"', $r['entry_id'] ) : '';
			$view_id  = sprintf( 'data-view-id="%d"', $gravityview_view->view_id );

			$output_wrapper = sprintf( '<span class="gv-vote-rating gv-vote-rate-holder" %s %s>', $entry_id, $view_id );
			$vote_tag       = 'a';
		} else {
			$output_wrapper = '<span class="gv-vote-rating" title="' . esc_attr( $title ) . '">';
			$vote_tag       = 'span';
		}

		$output .= $output_wrapper;
		$output .= sprintf(
			'<%s class="gv-vote-up%s" title="%s"></%1$s>',
			$vote_tag,
			$is_positive_rating ? ' gv-rate-mutated' : '',
			$is_positive_rating ? esc_attr__( 'Up vote shows the entry is useful for the author', 'gravityview-ratings-reviews' ) : ''
		);
		$output .= sprintf(
			'<%s class="gv-vote-down%s" title="%s"></%1$s>',
			$vote_tag,
			$is_negative_rating ? ' gv-rate-mutated' : '',
			$is_negative_rating ? esc_attr__( 'Down vote shows the entry is not useful for the author', 'gravityview-ratings-reviews' ) : ''
		);

		if ( ! $r['clickable'] ) {
			$output .= sprintf(
				'<span class="gv-vote-rating-text" title="%s">%s</span>',
				esc_attr( $title ),
				esc_html( self::get_vote_rating_text( $r['rating'] ) )
			);
		}

		$output .= '</span>';

		// If there is more than one rating, display the rating average
		if ( $r['number'] > 1 && $r['display_text'] ) {
			$output .= sprintf( '<span class="gv-vote-average-rating">%s</span>', $title );
		}

		/**
		 * Filter for HTML element with a vote rating.
		 *
		 * @since 0.1.0
		 * @param array  $r      Array of args used to generate the output
		 *
		 * @param string $output HTML element with a vote rating
		 */
		return apply_filters( 'gv_ratings_reviews_vote_rating', $output, $r );
	}

	/**
	 * Outputs an HTML element with the vote in integers
	 *
	 * @since 1.3
	 *
	 * @param array $entry Gravity Forms Entry array
	 *
	 * @return string Star rating from get_vote_rating()
	 * @uses  get_entry_average_rating
	 * @uses  get_vote_rating
	 */
	public static function get_vote_rating_for_entry( $entry = array() ) {

		$average = GravityView_Ratings_Reviews_Helper::get_entry_average_rating( $entry );

		// There was a problem.
		if ( ! isset( $average['average_vote'] ) ) {
			do_action( 'gravityview_log_error', 'Error fetching average vote for entry' );

			return '';
		}

		do_action( 'gravityview_log_debug', 'Average vote array for entry', array( '$entry' => $entry, '$average' => $average ) );

		$args = array(
			'rating' => $average['average_vote'],
			'type'   => 'vote',
			'number' => $average['total_voters'],
		);

		return self::get_vote_rating( $args );
	}

	/**
	 * Outputs an HTML element for vote rating.
	 *
	 * @since 0.1.0
	 *
	 * @param array $args         {
	 *                            Optional. Array of star ratings arguments.
	 *
	 * @type int    $rating       The rating to display. Default 0.
	 * @type int    $number       The number of ratings that makes up this rating. Default 0.
	 * @type bool   $clickable    Whether the star is clickable or not. Default false.
	 * @type bool   $display_text Whether to show the text. Default false.
	 * }
	 *
	 * @return void
	 */
	public static function the_vote_rating( $args = array() ) {

		echo self::get_vote_rating( $args );
	}

	/**
	 * Get vote value from a given star rating.
	 *
	 * @since 0.1.0
	 *
	 * @param int $rating Star rating, from 0 - 5
	 *
	 * @return int Valid value: -1, 0, +1
	 */
	public static function get_vote_from_star( $rating ) {

		$rating = absint( $rating );

		if ( 3 > $rating && 0 < $rating ) {
			return - 1;
		}

		if ( 3 < $rating ) {
			return 1;
		}

		return 0;
	}

	/**
	 * Get star value from a given vote rating (-1, 0, 1).
	 *
	 * Down vote = 1 star
	 * Up vote = 5 star
	 * No vote = 0 (empty)
	 *
	 * @since 0.1.0
	 *
	 * @param int $vote Vote rating
	 *
	 * @return int
	 */
	public static function get_star_from_vote( $vote ) {

		$vote = intval( $vote );

		if ( 1 === $vote ) {
			return 5;
		}

		if ( - 1 === $vote ) {
			return 1;
		}

		return 0;
	}

	/**
	 * Get the star ratings for an entry.
	 *
	 * @since 0.1.0
	 *
	 * @param null|int $post_id The bridge post the comments and ratings are attached to.
	 * @param null|int $entry_id The entry to retrieve the ratings for.
	 *
	 * @return int[] Array of review ratings
	 */
	public static function get_ratings( $post_id = null, $entry_id = null ) {

		if ( is_null( $post_id ) ) {
			$post_id = gform_get_meta( $entry_id, 'gf_entry_to_comments_post_id' );
		}

		if ( ! is_numeric( $post_id ) ) {
			return array();
		}

		$query = new WP_Comment_Query( array( 'post_id' => $post_id ) );
		$rates = array();
		// Todo: This can probably be retrieved in a single query instead of N+1
		foreach ( $query->comments as $comment ) {
			$rates[] = get_comment_meta( $comment->comment_ID, 'gv_review_rate', true );
		}

		return $rates;
	}

	/**
	 * Adds PHP 5.2 Array replace functionality
	 *
	 * @see http://php.net/manual/en/function.array-replace.php for usage
	 *
	 * @param array  &$array the Original Array
	 *
	 * @return null|array         If any of the Arguments are not array it will return otherwise if several arrays are passed for replacement, they will be processed in order, the later arrays overwriting the previous values.
	 */
	protected static function array_replace( array $array ) {

		$args  = func_get_args();
		$count = func_num_args();
		if ( 1 === $count ) {
			return $array;
		}

		if ( ! function_exists( 'array_replace' ) ) {
			for ( $i = 0; $i < $count; ++ $i ) {
				if ( is_array( $args[ $i ] ) ) {
					foreach ( $args[ $i ] as $key => $val ) {
						$array[ $key ] = $val;
					}
				} else {
					trigger_error( sprintf( __( '%s(): Argument #%d is not an array', 'gravityview-ratings-reviews' ), __FUNCTION__, ( $i + 1 ) ), E_USER_NOTICE );

					return null;
				}
			}
		} else {
			$array = call_user_func_array( 'array_replace', $args );
		}

		return $array;
	}

	/**
	 * Calculates and returns the ratings and votes for an entry.
	 *
	 * @since 0.1.0
	 *
	 * @param null|int $post_id The bridge post the comments and ratings are attached to.
	 * @param null|int $entry_id The entry to retrieve the ratings for.
	 *
	 * @return array Array of key => value pairs for entry meta (star ratings, votes, etc)
	 */
	public static function get_ratings_detailed( $post_id = null, $entry_id = null ) {

		$stars       = self::get_ratings( $post_id, $entry_id );
		$total_count = count( $stars );

		$count_stars = self::array_replace( array_fill( 1, 5, 0 ), array_count_values( $stars ) );
		$total_stars = array_sum( $stars );

		$votes = array();
		foreach ( $stars as $star ) {
			$votes[] = self::get_vote_from_star( $star );
		}
		$count_votes = self::array_replace( array( '-1' => 0, '0' => 0, '1' => 0, ), array_count_values( $votes ) );
		$total_votes = array_sum( $votes );

		$metas = array(
			'star_1' => $count_stars[1],
			'star_2' => $count_stars[2],
			'star_3' => $count_stars[3],
			'star_4' => $count_stars[4],
			'star_5' => $count_stars[5],
			'stars'  => $total_count > 0 ? $total_stars / $total_count : 0,

			'vote_down'    => $count_votes[ - 1 ],
			'vote_neutral' => $count_votes[0],
			'vote_up'      => $count_votes[1],
			'votes'        => $total_count > 0 ? $total_votes : 0,

			'total' => $total_count,
		);

		return $metas;
	}

	/**
	 * Refreshes the ratings fields for a specific entry.
	 *
	 * @since $ver$
	 *
	 * @param null|int $post_id The bridge post the comments and ratings are attached to.
	 * @param null|int $entry_id The entry to retrieve the ratings for.
	 * @param bool $clear_cache Whether to clear the cache.
	 * @param null|int $form_id The form ID.
	 *
	 * @return void
	 */
	public static function refresh_ratings( $post_id, $entry_id, $clear_cache = false, $form_id = null ) {
		$metas = static::get_ratings_detailed( $post_id, $entry_id );

		if ( ! $form_id ) {
			// Retrieve form ID once to avoid multiple lookups during the update.
			$entry = \GFAPI::get_entry( $entry_id );

			if ( $entry instanceof \WP_Error ) {
				return;
			}

			$form_id = (int) $entry['form_id'];
		}

		foreach ( $metas as $meta_key => $meta_value ) {
			gform_update_meta( $entry_id, 'gravityview_ratings_' . $meta_key, $meta_value, $form_id );
		}

		/**
		 * Add entry to the GravityView_Cache blocklist by triggering an update.
		 *
		 * @since 1.2.2
		 */
		if ( $clear_cache ) {
			do_action( 'gravityview_clear_entry_cache', $entry_id );
		}
	}

	/**
	 * If view setting limits to one review per person then the person that
	 * has left review before is not allowed to leave review again.
	 *
	 * @since 0.1.0
	 *
	 * @param int|WP_Post $post_bridge         The post ID or WP_Post object of post bridge
	 * @param string      $review_author       Reviewer's name
	 * @param string      $review_author_email Reviewer's email
	 * @param array       $reviewdata          Other comment data {@since 1.2.2}
	 * @param array       $view_settings       View settings {@since 2.1}
	 *
	 * @return bool|WP_Error If returning a WP_Error, the message will be displayed to the user. Otherwise, default "You have already reviewed this entry." message will be shown.
	 */
	public static function is_user_allowed_to_leave_review( $post_bridge, $review_author = '', $review_author_email = '', $reviewdata = array(), $view_settings = array() ) {

		global $gravityview_view, $gv_ratings_reviews;

		$post_bridge_comp = $gv_ratings_reviews->component_instances['post-bridge'];
		$review_comp      = $gv_ratings_reviews->component_instances['review'];

		$is_allowed  = true;
		$post_bridge = get_post( $post_bridge );

		// Admins and users who can moderate comments can leave unlimited comments.
		if ( GFCommon::current_user_can_any( array( 'manage_options', 'moderate_comments' ) ) ) {
			$is_allowed = true;
		} elseif ( $post_bridge_comp->name === get_post_type( $post_bridge ) ) {
			// When POSTing data to wp-comments-post.php or working with an AJAX request, the global `$gravityview_view` is not instantiated.
			if ( empty( $gravityview_view ) && empty( $view_settings ) ) {
				if ( ! empty( $_POST[ $review_comp->field_view_id ] ) ) {
					$view_settings = gravityview_get_template_settings( $_POST[ $review_comp->field_view_id ] );
				} else {
					// This could be when admin replies from admin dashboard.
					// We don't have View object at this point so that we ignore
					// settings.
					$view_settings['limit_one_review_per_person'] = false;
				}
			} elseif ( empty( $view_settings ) ) {
				$view_data     = gravityview_get_current_view_data();
				$view_settings = $view_data['atts'];
			}

			if ( ! $view_settings['limit_one_review_per_person'] ) {
				$is_allowed = true;
			} else {
				$reviewdata['comment_post_ID']      = $post_bridge->ID;
				$reviewdata['comment_author']       = $review_author;
				$reviewdata['comment_author_email'] = $review_author_email;

				if ( ! empty( $reviewdata['comment_author'] ) && self::is_user_has_left_review_before( $reviewdata ) && empty( $reviewdata['comment_parent'] ) ) {
					$is_allowed = false;
				}
			}
		}

		/**
		 * @filter `gv_ratings_reviews_is_user_allowed_to_review`
		 *
		 * @since  2.0.1
		 * @param array         $reviewdata  Comment data array, including author, email,
		 * @param int|WP_Post   $post_bridge The post ID or WP_Post object of post bridge
		 *
		 * @param bool|WP_Error $is_allowed  Whether the user is allowed to review the entry.
		 */
		$is_allowed = apply_filters( 'gv_ratings_reviews_is_user_allowed_to_review', $is_allowed, $reviewdata, $post_bridge );

		return $is_allowed;
	}

	/**
	 * Checks whether user has left a review to entry before.
	 *
	 * @since 0.1.0
	 *
	 * @param array $commentdata Contains information on the comment
	 *
	 * @return bool
	 */
	public static function is_user_has_left_review_before( $commentdata ) {

		global $wpdb;

		// Simple has_review check.
		//
		// Expected_slashed ($comment_post_ID, $comment_author, $comment_author_email).
		// TODO: Convert from SQL to WP_Comment_Query
		$has_review = $wpdb->prepare(
			"
			SELECT comment_ID FROM $wpdb->comments
			WHERE
				comment_post_ID = %d
				AND
				comment_parent = 0
				AND
				comment_approved != 'trash'
				AND ( comment_author = %s ",
			wp_unslash( $commentdata['comment_post_ID'] ),
			wp_unslash( $commentdata['comment_author'] )
		);
		if ( $commentdata['comment_author_email'] ) {
			$has_review .= $wpdb->prepare(
				'OR comment_author_email = %s ',
				wp_unslash( $commentdata['comment_author_email'] )
			);
		}
		$has_review .= ') LIMIT 1';

		if ( $wpdb->get_var( $has_review ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get the review list header.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public static function get_the_review_list_header() {

		return self::get_list_template_content( 'header' );
	}

	/**
	 * Output the review list header.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function the_review_list_header() {

		echo self::get_the_review_list_header();
	}

	/**
	 * Get the review list body.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public static function get_review_list_body() {

		return self::get_list_template_content( 'body' );
	}

	/**
	 * Output the review list body.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function the_review_list_body() {

		echo self::get_review_list_body();
	}

	/**
	 * Get the review list footer.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public static function get_the_review_list_footer() {

		return self::get_list_template_content( 'footer' );
	}

	/**
	 * Output the review list footer.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function the_review_list_footer() {

		echo self::get_the_review_list_footer();
	}

	/**
	 * Get template content of the review list section.
	 *
	 * @since 0.1.0
	 *
	 * @param string $section Valid sections are: 'header', 'body', and 'footer'.
	 *
	 * @return string
	 */
	protected static function get_list_template_content( $section ) {

		/** @global GravityView_Ratings_Reviews_Loader $gv_ratings_reviews */
		global $gv_ratings_reviews;

		if ( ! in_array( $section, array( 'header', 'body', 'footer' ) ) ) {
			return '';
		}

		ob_start();
		$gv_ratings_reviews->locate_template( "review-list-{$section}.php", true );

		return ob_get_clean();
	}

	/**
	 * Allows users to filter if an Entry, Form or View can be rated by the users
	 *
	 * @param object|array|int $form  GravityForms Form Object or ID
	 * @param object|array|int $entry GravityForms Entry Object or ID
	 * @param object|array|int $view  GravityView Object or ID
	 *
	 * @filter gv_ratings_reviews_ratings_allowed
	 *
	 * @return boolean        Returns the "filtered" result on a True or False
	 */
	public static function is_ratings_allowed( $form = null, $entry = null, $view = null ) {

		$allow_ratings = true;

		if ( is_object( $view ) && is_a( $view, 'GravityView_View' ) ) {
			$allow_ratings = ! (bool) $view->getAtts( 'hide_ratings' );
		}

		/**
		 * @filter `gv_ratings_reviews_ratings_allowed` Display ratings fields for this View?
		 *
		 * @param boolean          $allow_ratings True: Yes, allow ratings; false: no, don't allow ratings
		 * @param object|array|int $form          GravityForms Form Object or ID
		 * @param object|array|int $entry         GravityForms Entry Object or ID
		 * @param object|array|int $view          GravityView Object or ID
		 */
		return apply_filters( 'gv_ratings_reviews_ratings_allowed', $allow_ratings, $form, $entry, $view );
	}

	/**
	 * Allows users to filter if an Entry, Form or View can be reviewed by the users
	 *
	 * @param object|array|int $form  GravityForms Form Object or ID
	 * @param object|array|int $entry GravityForms Entry Object or ID
	 * @param object|array|int $view  GravityView Object or ID
	 *
	 * @filter gv_ratings_reviews_reviews_allowed
	 *
	 * @return boolean        Returns the "filtered" result on a True or False
	 */
	public static function is_reviews_allowed( $form = null, $entry = null, $view = null ) {

		$reviews_allowed = true;

		if ( is_object( $view ) && is_a( $view, 'GravityView_View' ) ) {
			$reviews_allowed = (bool) $view->getAtts( 'allow_entry_reviews' );
		}

		/**
		 * @filter `gv_ratings_reviews_ratings_allowed` Allow reviewing an entry for this View?
		 *
		 * @param boolean          $allow_ratings True: Yes, allow reviews; false: no, don't allow reviews
		 * @param object|array|int $form          GravityForms Form Object or ID
		 * @param object|array|int $entry         GravityForms Entry Object or ID
		 * @param object|array|int $view          GravityView Object or ID
		 */
		return apply_filters( 'gv_ratings_reviews_reviews_allowed', $reviews_allowed, $form, $entry, $view );
	}

}
