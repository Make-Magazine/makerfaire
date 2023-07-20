<?php

final class GravityView_Ratings_Reviews_Recalculate_Ratings extends GravityView_Ratings_Reviews_Component {
	/**
	 * The bulk action used to recalculate the ratings.
	 * @since $ver$
	 * @var string
	 */
	const ACTION_RECALCULATE = 'gvrr_recalculate';

	/**
	 * Callback when this component is loaded by the loader.
	 *
	 * @since $ver$
	 * @return void
	 */
	public function load() {
		add_filter( 'gform_entry_list_action_' . self::ACTION_RECALCULATE, [ $this, 'bulk_recalculate' ], 10, 3 );
		add_filter( 'bulk_actions-forms_page_gf_entries', [ $this, 'bulk_actions' ], 10, 2 );
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
			$actions[ $group ] = [];
		}

		$actions[ $group ][ self::ACTION_RECALCULATE ] = esc_attr__( 'Recalculate Ratings', 'gravityview-ratings-reviews' );

		return $actions;
	}

	/**
	 * Recalculates the ratings for the selected entries.
	 *
	 * @param string $action The current action.
	 * @param int[] $entries The selected entry IDs
	 * @param int $form_id The form ID.
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
	 * @since $ver$
	 *
	 * @param int $form_id The form ID.
	 *
	 * @return bool
	 */
	private function has_connected_reviews_view( $form_id ) {
		if ( ! $form_id ) {
			return false;
		}

		$connected_views = gravityview_get_connected_views( $form_id, [ 'post_status' => 'any', 'fields' => 'ids' ] );

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
