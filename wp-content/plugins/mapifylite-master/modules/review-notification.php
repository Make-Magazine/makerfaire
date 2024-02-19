<?php
/**
 * Handle the admin notification about suggestion to review the plugin on wordpress.org
 *
 * @package MapifyLite
 */

/**
 * Admin notices about suggestion to review
 */
function mpfy_suggestion_to_review_notification() {
	if ( ! mpfy_is_user_should_review() ) {
		return;
	}
	?>

	<div class="notice notice-info-mapifylite is-dismissible mpfy-hidden" id="mpfy-response-to-the-review">
		<p><?php esc_html_e( 'Thank you for your attention!, and please, enjoy the plugin :)', 'mpfy' ); ?></p>
	</div>

	<div class="notice notice-info-mapifylite" id="mpfy-suggestion-to-review">
		<?php wp_nonce_field( 'mpfy_suggestion_to_review', 'mpfy_review_nonce' ); ?>

		<p>
			<?php
			printf(
				// translators: %1$s: Minimum post count.
				// translators: %2$s: Html tag.
				// translators: %3$s: Html tag.
				esc_html__( 'Hey, I noticed you have created %1$s or more Map Locations on %2$sMapifyLite%3$s - that\'s awesome! May I ask you to give it a %2$s5-star%3$s rating on WordPress? Just to help us spread the word and boost our motivation.', 'mpfy' ),
				esc_html( MAPIFY_MINIMUM_ITEMS_TO_REVIEW ),
				'<strong>',
				'</strong>'
			);
			?>

			<ul>
				<li>
					<span class="dashicons dashicons-thumbs-up"></span> 
					<a href="https://wordpress.org/support/plugin/mapifylite/reviews/#new-post" id="mpfy-do-the-review" target="_blank">
						<?php esc_html_e( 'Ok, you deserve it', 'mpfy' ); ?>
					</a>
				</li>
				<li>
					<span class="dashicons dashicons-smiley"></span> 
					<a href="" id="mpfy-did-the-review">
						<?php esc_html_e( 'I already did', 'mpfy' ); ?>
					</a>
				</li>
				<li>
					<span class="dashicons dashicons-no"></span> 
					<a href="" id="mpfy-wont-review">
						<?php esc_html_e( 'No, not good enough for now', 'mpfy' ); ?>
					</a>
				</li>
			</ul>
		</p>
	</div>

	<?php
}
add_action( 'admin_notices', 'mpfy_suggestion_to_review_notification' );

/**
 * Admin enqueue script
 *
 * @param string $hook The current admin page.
 */
function mpfy_suggestion_to_review_script( $hook ) {
	if ( mpfy_is_user_should_review() ) {
		wp_enqueue_script( 'mpfy-suggestion-to-review', plugin_dir_url( MAPIFY_PLUGIN_FILE ) . 'assets/js/suggestion-to-review.js', array( 'jquery' ), MAPIFY_PLUGIN_VERSION, true );
	}
}
add_action( 'admin_enqueue_scripts', 'mpfy_suggestion_to_review_script' );

/**
 * Check if we we should notice user to review the plugin
 */
function mpfy_is_user_should_review() {
	$is_user_should_review = get_option( 'mpfy_is_user_should_review', false );
	$selected_action       = get_option( 'mpfy_review_selected_user_action', false );

	if ( $is_user_should_review ) {
		return true;
	} elseif ( ! $is_user_should_review && ! $selected_action ) {
		$count_per_statuses = wp_count_posts( 'map-location' );
		$published_count    = isset( $count_per_statuses->publish ) ? absint( $count_per_statuses->publish ) : 0;

		if ( $published_count >= MAPIFY_MINIMUM_ITEMS_TO_REVIEW ) {
			update_option( 'mpfy_is_user_should_review', 1 );
			return true;
		}
	}

	return false;
}

/**
 * Ajax hook to store the user selected item on the plugin review notification.
 */
function mpfy_review_save_selected_user_action() {
	check_ajax_referer( 'mpfy_suggestion_to_review', 'nonce' );

	$post_data       = isset( $_POST ) ? wp_unslash( $_POST ) : array();
	$selected_action = sanitize_text_field( $post_data['selected_action'] );

	// update selected_user_action.
	update_option( 'mpfy_review_selected_user_action', $selected_action );

	// update is_user_should_review.
	update_option( 'mpfy_is_user_should_review', 0 );

	wp_die();
}
add_action( 'wp_ajax_mpfy_review_save_selected_user_action', 'mpfy_review_save_selected_user_action' );
