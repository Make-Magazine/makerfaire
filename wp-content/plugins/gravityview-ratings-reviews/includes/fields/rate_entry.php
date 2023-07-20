<?php

defined( 'ABSPATH' ) || exit;

/**
 * Add custom options for HTML field
 */
class GravityView_Field_Rate_Entry extends GravityView_Field {

	/**
	 * @type string File name
	 */
	var $name = 'rate_entry';

	var $icon = 'dashicons-star-half';

	/**
	 * @type string The description of the field in the field picker
	 */
	var $description;

	/**
	 * @type string The label of the field in the field picker
	 */
	var $label;

	/**
	 * @type string AJAX action to add or update entry rating
	 */
	const AJAX_ACTION_ADD_UPDATE_RATING = 'rate_entry_action';

	function __construct() {

		parent::__construct();

		$this->label       = __( 'Rate Entry', 'gravityview-ratings-reviews' );
		$this->description = __( 'Rate entry in multiple entries view', 'gravityview-ratings-reviews' );

		add_filter( 'gravityview_entry_default_fields', array( $this, 'add_field_to_field_picker' ), 10, 3 );
		add_filter( 'gv_ratings_reviews_js_vars', array( $this, 'get_frontend_parameters' ) );

		add_action( 'wp_ajax_nopriv_' . self::AJAX_ACTION_ADD_UPDATE_RATING, array( $this, 'add_update_rating' ) );
		add_action( 'wp_ajax_' . self::AJAX_ACTION_ADD_UPDATE_RATING, array( $this, 'add_update_rating' ) );
	}

	/**
	 * Pass additional parameters to the UI if this is a multiple entries view
	 *
	 * @param array  $field_options
	 * @param string $template_id
	 * @param string $field_id
	 * @param string $context
	 * @param string $input_type
	 *
	 * @return array
	 */
	function get_frontend_parameters( $vars ) {

		$GV_instance = \GravityView_frontend::getInstance();

		$vars = array_merge( $vars, array(
			'action'  => self::AJAX_ACTION_ADD_UPDATE_RATING,
			'nonce'   => wp_create_nonce( $this->name ),
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		) );

		if ( $GV_instance->isGravityviewPostType() && ! $GV_instance->is_single_entry() ) {
			$vars = array_merge( $vars, array(
				'multiple_entries' => true,
				'post_id'          => $GV_instance->getPostId(),
			) );
		}

		return $vars;
	}

	/**
	 * Configure field options
	 *
	 * @return void
	 */
	function add_update_rating() {

		global $gv_ratings_reviews;

		// Validate AJAX request
		$is_valid_nonce  = wp_verify_nonce( rgpost( 'nonce' ), $this->name );
		$is_valid_action = self::AJAX_ACTION_ADD_UPDATE_RATING === rgpost( 'action' );
		$type            = rgpost( 'type' );
		$rating          = (int) rgpost( 'rating' );
		$comment_id      = (int) rgpost( 'comment_id' );
		$comment         = ( $comment_id ) ? get_comment( $comment_id ) : null;
		$user            = wp_get_current_user();
		$view_id         = (int) rgpost( 'view_id' );
		$entry_id        = (int) rgpost( 'entry_id' );
		$entry           = GFAPI::get_entry( $entry_id );
		$update_rating   = (boolean) rgpost( 'update_rating' );

		if ( ! $is_valid_action || ! $is_valid_action || ! $entry_id ) {
			// Return 'forbidden' response if nonce is invalid, otherwise it's a 'bad request'
			wp_die( false, false, array( 'response' => ( ! $is_valid_nonce ) ? 403 : 400 ) );
		}

		$success = true;

		if ( $type === 'vote' ) {
			switch ( true ) {
				case $rating < 0:
					$rating = 1;
					break;
				case $rating === 0:
					$rating = 3;
					break;
				case $rating === 1:
					$rating = 5;
					break;
			}
		}

		if ( $update_rating && $comment instanceof \WP_Comment && $user instanceof \WP_User && (int) $comment->user_id === (int) $user->ID ) {
			if ( ! update_comment_meta( $comment_id, 'gv_review_rate', $rating ) ) {
				$error_message = esc_html__( 'Entry rating could not be updated. Please try again or contact support.', 'gravityview-ratings-reviews' );
				$success       = false;
			}
		} else {
			$post_bridge_comp = $gv_ratings_reviews->component_instances['post-bridge'];
			$post_id          = $post_bridge_comp->create_bridge( $entry );

			$error_message = esc_html__( 'Entry could not be rated. Please try again or contact support.', 'gravityview-ratings-reviews' );

			if ( ! is_wp_error( $post_id ) ) {
				$comment = array(
					'comment_post_ID'      => $post_id,
					'user_id'              => $user->ID,
					'comment_author'       => $user->user_login,
					'comment_author_email' => $user->user_email,
					'comment_approved'     => 1,
				);

				$view_settings = gravityview_get_template_settings( $view_id );

				if ( GravityView_Ratings_Reviews_Helper::is_user_allowed_to_leave_review( $post_id, $user->user_login, $user->user_email, $comment, $view_settings ) ) {
					$comment_id = wp_insert_comment( $comment );

					if ( is_wp_error( $comment_id ) || ! add_comment_meta( $comment_id, 'gv_review_rate', $rating ) ) {
						$success = false;
					}
				} else {
					$error_message = esc_html__( 'You can  could not be rated. Please try again or contact support.', 'gravityview-ratings-reviews' );

					$success = false;
				}
			} else {
				$success = false;
			}
		}

		if ( $success ) {
			wp_send_json_success(
				array(
					'rating'     => $rating,
					'comment_id' => $comment_id,
				)
			);
		} else {
			wp_send_json_error(
				array(
					'error' => $error_message,
				)
			);
		}
	}

	/**
	 * Configure field options
	 *
	 * @param array  $field_options
	 * @param string $template_id
	 * @param string $field_id
	 * @param string $context
	 * @param string $input_type
	 *
	 * @return array
	 */
	function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id ) {

		unset ( $field_options['show_as_link'] );

		return $field_options;
	}

	/**
	 * Add the field to the field picker
	 *
	 * @param array  $fields Array of fields to display in field picker
	 * @param array  $form   Connected Gravity Forms form
	 * @param string $zone   Current tab in field configuration
	 *
	 * @return array
	 */
	function add_field_to_field_picker( $fields = array(), $form = array(), $zone = '' ) {

		if ( in_array( $zone, array( 'edit', 'single' ) ) ) {
			return $fields;
		}

		$fields['rate_entry'] = array(
			'label' => $this->label,
			'type'  => $this->name,
			'desc'  => $this->description,
		);

		return $fields;
	}
}

new GravityView_Field_Rate_Entry;
