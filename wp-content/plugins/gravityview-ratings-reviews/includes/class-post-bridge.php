<?php
/**
 * Component that has responsibility to provide a bridge between GF entries and
 * reviews/comments. It's accomplished by registering CPT in which its post will be
 * created for each GF entry creation.
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

class GravityView_Ratings_Reviews_Post_Bridge extends GravityView_Ratings_Reviews_Component {

	/**
	 * Post type name. Post of this CPT will be created for each submitted entry.
	 * The ID of the post will be stored in entry meta, keyed by `$this->$entry_meta_key`.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $name = 'gf_entry_to_comments';

	/**
	 * Entry-meta's name that stores post ID that bridges entry to comments.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $post_id_meta_key = 'gf_entry_to_comments_post_id';

	/**
	 * Entry ID stored in post bridge meta.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $entry_id_meta_key = 'gf_entry_id';

	/**
	 * Callback when this component is loaded by the loader.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function load() {
		// Register custom post type.
		add_action( 'init', array( $this, 'register_post_type' ) );

		// Creates post bridge, if doesn't exists, when entry has been created.
		add_action( 'gform_post_submission', array( $this, 'create_bridge' ) );

		// Edit post link should points to edit GF entry.
		add_filter( 'get_edit_post_link', array( $this, 'edit_entry_link' ), 10, 2 );

		// View entry in admin should point to view GF entry.
		add_filter( 'post_type_link', array( $this, 'post_type_link' ), 10, 2 );

		// Permalink to comment bridge should be replaced by a link to the actual comments in the notification.
		add_filter( 'comment_notification_text', array( $this, 'replace_notification_comment_link', ), 10, 2 );
	}

	/**
	 * Register custom post type that acts as a bridge between Gravity Forms entries
	 * and comments.
	 *
	 * @action init
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_post_type() {
		$args = array(
			'description'         => '',
			'public'              => false,
			'publicly_queryable'  => false,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'exclude_from_search' => true,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'can_export'          => false,
			'delete_with_user'    => true,
			'hierarchical'        => false,
			'has_archive'         => false,
			'query_var'           => false,
			'rewrite'             => false,

			// What features the post type supports.
			'supports' => array(
				'comments' => true,
			),

			'labels' => array(
				'name'          => __( 'Entry',      'gravityview-ratings-reviews' ),
				'singular_name' => __( 'Entry',      'gravityview-ratings-reviews' ),
				'view_item'     => __( 'View Entry', 'gravityview-ratings-reviews' ),
			),
		);

		register_post_type( $this->name, $args );
	}

	/**
	 * Creates post bridge for each GF entry, if the bridge is not created yet.
	 *
	 * @action gform_post_submission
	 *
	 * @since 0.1.0
	 *
	 * @param array|int $entry The entry that was just created.
	 *
	 * @return WP_Error|int
	 */
	public function create_bridge( $entry ) {

		if( empty( $entry ) ) {
			$error = 'Empty $entry array; bridge post not created';
			do_action( 'gravityview_log_error', __METHOD__ . ' ' . $error );
			return new WP_Error( 'gv_ratings_reviews_post_bridge', $error );
		}

		if ( ! is_array( $entry ) && ! is_numeric( $entry ) ) {
			$error = '$entry is not numeric; bridge post not created';
			do_action( 'gravityview_log_error', __METHOD__ . ' ' . $error );

			return new WP_Error( 'gv_ratings_reviews_post_bridge_not_numeric', $error );
		}

		if ( is_numeric( $entry ) ) {

			// Convert entry ID to array
			$entry = GFAPI::get_entry( $entry );

			if ( ! $entry || is_wp_error( $entry ) ) {
				$error = '$entry was an ID for entry that doesn\'t exist; bridge post not created';
				do_action( 'gravityview_log_error', __METHOD__ . ' ' . $error );

				return new WP_Error( 'gv_ratings_reviews_post_bridge_missing_entry', $error );
			}
		}

		// First, checks whether this entry has a post bridge.
		$post_id = absint( gform_get_meta( $entry['id'], $this->post_id_meta_key ) );

		if ( ! $post_id ) {

			$post_bridge_title = sprintf( __( 'GF entry ID %d, GF form ID %d', 'gravityview-ratings-reviews' ), $entry['id'], $entry['form_id'] );

			/**
			 * @filter `gv_ratings_reviews_post_bridge_title` Modify the title of the Post Bridge using entry data
			 * The Post Bridge is a 1 <=> 1 custom post type that has a 1 <=> 1 relationship with a Gravity Forms entry that has a review.
			 * Comments are left on the Post Bridge post, then pulled in on each entry by getting comments stored on the Post Bridge CPT.
			 *
			 * @param string $post_bridge_title Existing title format: "GF entry ID %d, GF form ID %d"
			 * @param array $entry Gravity Forms entry to create the post bridge for.
			 */
			$post_bridge_title = apply_filters( 'gv_ratings_reviews_post_bridge_title', $post_bridge_title, $entry );

			$post_id = wp_insert_post( array(
				'post_type'      => $this->name,
				'post_title'     => $post_bridge_title,
				'post_status'    => 'publish',
				'comment_status' => 'open',
			) );
		}

		$post_bridge = get_post( $post_id );

		if ( is_wp_error( $post_bridge ) || $this->name !== get_post_type( $post_bridge ) ) {
			$error = __( 'Unable to create post bridge', 'gravityview-ratings-reviews' );
			do_action( 'gravityview_log_error', __METHOD__ . ' ' . $error .' - Post bridge post/WP_Error: ', $post_bridge );
			return new WP_Error( 'gv_ratings_reviews_post_bridge', $error );
		}

		// Saves post ID into entry meta so that we can query the comments later.
		gform_update_meta( $entry['id'], $this->post_id_meta_key, $post_bridge->ID, $entry['form_id'] );

		// Saves entry ID in post bridge.
		update_post_meta( $post_bridge->ID, $this->entry_id_meta_key, $entry['id'] );

		return $post_bridge->ID;
	}

	/**
	 * Filter the post edit link.
	 *
	 * @filter get_edit_post_link
	 *
	 * @since 0.1.0
	 *
	 * @param string $link    The edit link.
	 * @param int    $post_id Post ID.
	 *
	 * @return string The edit link.
	 */
	public function edit_entry_link( $link, $post_id ) {
		if ( $this->name === get_post_type( $post_id ) ) {
			$entry_id = get_post_meta( $post_id, $this->entry_id_meta_key, true );
			$link     = GravityView_Ratings_Reviews_Helper::get_entry_admin_url( $entry_id, 'edit' );
		}

		return $link;
	}

	/**
	 * Filter the permalink for a post bridge when in admin.
	 *
	 * @filter post_type_link
	 *
	 * @since 0.1.0
	 *
	 * @param string  $post_link The post's permalink.
	 * @param WP_Post $post      The post in question.
	 *
	 * @return string
	 */
	public function post_type_link( $link, $post ) {
		if ( $post->post_type === $this->name && is_admin() ) {
			$entry_id = get_post_meta( $post->ID, $this->entry_id_meta_key, true );
			$link     = GravityView_Ratings_Reviews_Helper::get_entry_admin_url( $entry_id );
		}

		return $link;
	}

	/**
	 * Filters the notification message and replaces the permalink for a bridge to the actual comments page.
	 *
	 * @filter comment_notification_text
	 *
	 * @param string $message The notification message.
	 * @param int $comment_id The comment ID.
	 *
	 * @return string
	 */
	public function replace_notification_comment_link( string $message, $comment_id ) {
		$comment = get_comment( $comment_id );
		$bridge  = get_post( $comment->comment_post_ID );
		if ( $bridge->post_type !== $this->name ) {
			return $message;
		}

		$old_permalink = get_permalink( $comment->comment_post_ID ) . '#comments';

		return str_replace( $old_permalink, get_comment_link($comment_id), $message );
	}
}
