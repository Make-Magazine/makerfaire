<?php

final class GravityView_Ratings_Reviews_Recalculate_Ratings extends GravityView_Ratings_Reviews_Component {
	/**
	 * The bulk action used to recalculate the ratings.
	 *
	 * @since 2.2.0
	 * @var string
	 */
	const ACTION_RECALCULATE = 'gvrr_recalculate';

	/**
	 * Number of comments to process at one time in refreshing ratings.
	 */
	const NUMBER_TO_PROCESS = 100;

	/**
	 * Callback when this component is loaded by the loader.
	 *
	 * @since 2.2.0
	 * @return void
	 */
	public function load() {
		add_filter( 'gform_entry_list_action_' . self::ACTION_RECALCULATE, array( $this, 'bulk_recalculate' ), 10, 3 );
		add_filter( 'bulk_actions-forms_page_gf_entries', array( $this, 'bulk_actions' ), 10, 2 );
		add_action( 'wp_set_comment_status', array( $this, 'comment_status_changed' ), 10, 2 );

		add_action( 'admin_init', array( $this, 'refresh_old_reviews_votes' ) );
		add_action( 'gv_ratings_reviews_refresh', array( $this, 'refresh_reviews_cron_job' ) );
	}

	/**
	 * Get comments that has reviews.
	 *
	 * @param integer $offset
	 * @return array
	 */
	private function get_comments_reviews( $offset = 0 ) {
		$args = array(
			'number'     => self::NUMBER_TO_PROCESS,
			'offset'     => $offset,
			'status'     => 'all',
			'meta_query' => array(
				array(
					'key'     => 'gv_review_rate',
					'compare' => 'EXISTS',
				),
			),
		);

		return get_comments( $args );
	}

	/**
	 * Refreshes reviews cron job that will run in batch of 100 comments each time.
	 *
	 * @since 2.3.0
	 * 
	 * @param integer $offset
	 */
	public function refresh_reviews_cron_job( $offset ) {
		$comments = $this->get_comments_reviews( $offset );
		if ( empty( $comments ) ) {
			update_option( 'gv_ratings_reviews_refresh_already_run', true );
			return;
		}

		foreach ( $comments as $comment ) {
			$this->refresh_ratings_by_comment( $comment );
		}

		$new_offset = $offset + self::NUMBER_TO_PROCESS;
		if ( ! wp_next_scheduled( 'gv_ratings_reviews_refresh', array( $new_offset ) ) ) {
			wp_schedule_single_event( time(), 'gv_ratings_reviews_refresh', array( $new_offset ) );
		}
	}

	/**
	 * Refreshes old reviews votes.
	 * 
	 * @since 2.3.0
	 */
	public function refresh_old_reviews_votes() {
		// Check if the function has already run
		if ( get_option( 'gv_ratings_reviews_refresh_already_run', false ) ) {
			return;
		}

		$comments = $this->get_comments_reviews();

		// If comments are smaller than NUMBER_TO_PROCESS then we don't need to run a cron job.
		if ( count( $comments ) < self::NUMBER_TO_PROCESS ) {
			foreach ( $comments as $comment ) {
				$this->refresh_ratings_by_comment( $comment );
			}
			update_option( 'gv_ratings_reviews_refresh_already_run', true );
			return;
		}

		if ( ! wp_next_scheduled( 'gv_ratings_reviews_refresh', array( 0 ) ) ) {
			wp_schedule_single_event( time(), 'gv_ratings_reviews_refresh', array( 0 ) );
		}

	}

	/**
	 * Refreshes reviews rating by comment
	 * 
	 * @since 2.3.0
	 *
	 * @param WP_Comment $comment
	 */
	private function refresh_ratings_by_comment( $comment ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'gf_entry_meta';
		$query      = $wpdb->prepare( "SELECT entry_id FROM {$table_name} WHERE meta_key = 'gf_entry_to_comments_post_id' AND meta_value = %d", $comment->comment_post_ID );
		$entry_id   = $wpdb->get_var( $query );
		if ( (int) $entry_id === 0 ) {
			return;
		}

		\GravityView_Ratings_Reviews_Helper::refresh_ratings( null, $entry_id );

	}


	/**
	 * Runs whenever a comment status has changed to refresh ratings.
	 *
	 * @since 2.3.0
	 * 
	 * @param integer $comment_id
	 * @param string  $comment_status
	 */
	public function comment_status_changed( $comment_id, $comment_status ) {
		$comment = get_comment( $comment_id );
		$this->refresh_ratings_by_comment( $comment );
	}

	/**
	 * Adds a bulk recalculate actions.
	 *
	 * @param array $actions
	 *
	 * @return array
	 */
	public function bulk_actions( array $actions ) {
		if ( ! class_exists( \GVCommon::class ) ) {
			return $actions;
		}

		$form_id = isset( $_GET['id'] ) ? $_GET['id'] : 0;

		if ( ! $this->has_connected_reviews_view( $form_id ) ) {
			return $actions;
		}

		$group = $this->loader->getTitle();
		if ( ! array_key_exists( $group, $actions ) ) {
			$actions[ $group ] = array();
		}

		$actions[ $group ][ self::ACTION_RECALCULATE ] = esc_attr__( 'Recalculate Ratings', 'gravityview-ratings-reviews' );

		return $actions;
	}

	/**
	 * Recalculates the ratings for the selected entries.
	 *
	 * @param string $action The current action.
	 * @param int[]  $entries The selected entry IDs
	 * @param int    $form_id The form ID.
	 *
	 * @return void
	 */
	public function bulk_recalculate( $action, $entries, $form_id ) {
		if ( $action !== self::ACTION_RECALCULATE || ! $entries ) {
			return;
		}

		foreach ( $entries as $entry ) {
			GravityView_Ratings_Reviews_Helper::refresh_ratings( null, $entry, false, (int) $form_id );
		}

		echo '<div id="message" class="alert success"><p>' . __( 'Ratings recalculated.', 'gravityview-ratings-reviews' ) . '</p></div>';
	}

	/**
	 * Returns whether the form has at least one connected view that has reviews enabled.
	 *
	 * @since 2.2.0
	 *
	 * @param int $form_id The form ID.
	 *
	 * @return bool
	 */
	private function has_connected_reviews_view( $form_id ) {
		if ( ! $form_id ) {
			return false;
		}

		$connected_views = gravityview_get_connected_views(
			$form_id,
			array(
				'post_status' => 'any',
				'fields'      => 'ids',
			)
		);

		// Check if there is at least one connected view that allows for entry reviews
		foreach ( $connected_views as $post_id ) {
			$settings            = gravityview_get_template_settings( $post_id );
			$allow_entry_reviews = isset( $settings['allow_entry_reviews'] ) && $settings['allow_entry_reviews'];
			if ( $allow_entry_reviews ) {
				return true;
			}
		}

		return false;
	}
}
