<?php
/**
 * Class SB_Instagram_Cron_Updater_Pro
 *
 * The use of recent hashtag feeds that require some posts
 * to be loaded strictly from the sb_instagram_posts custom
 * tables in the database means that updating in the background
 * requires some additional logic.
 *
 * @since 5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class SB_Instagram_Cron_Updater_Pro extends  SB_Instagram_Cron_Updater
{
	/**
	 * Loop through all feed cache transients and update the post and
	 * header caches.
	 *
	 * Pro - Need to use the Pro version of the single cron update
	 *
	 * @since 5.0
	 * @since 5.1.2 feed cache array is shuffled to accommodate large numbers of feeds
	 */
	public static function do_feed_updates() {
		$feed_caches = SB_Instagram_Cron_Updater::get_feed_cache_option_names();
		shuffle(  $feed_caches );
		$settings = sbi_get_database_settings();

		$report = array(
			'notes' => array(
				'time_ran' => date( 'Y-m-d H:i:s' ),
				'num_found_transients' => count( $feed_caches )
			)
		);

		foreach ( $feed_caches as $feed_cache ) {

			$feed_id  = str_replace( '_transient_', '', $feed_cache['option_name'] );
			$report[ $feed_id ] = array();

			$transient = get_transient( $feed_id );

			if ( $transient ) {
				$feed_data                  = json_decode( $transient, true );

				$atts = isset( $feed_data['atts'] ) ? $feed_data['atts'] : false;
				$last_retrieve = isset( $feed_data['last_retrieve'] ) ? (int)$feed_data['last_retrieve'] : 0;
				$last_requested = isset( $feed_data['last_requested'] ) ? (int)$feed_data['last_requested'] : false;
				$report[ $feed_id ]['last_retrieve'] = date( 'Y-m-d H:i:s', $last_retrieve );
				if ( $atts !== false ) {

					if ( ! $last_requested || $last_requested > (time() - 60*60*24*30) ) {
						$instagram_feed_settings = new SB_Instagram_Settings_Pro( $atts, $settings );

						if ( empty( $settings['connected_accounts'] ) && empty( $atts['accesstoken'] ) ) {
							$report[ $feed_id ]['did_update'] = 'no - no connected account';
						} else {
							SB_Instagram_Cron_Updater_Pro::do_single_feed_cron_update( $instagram_feed_settings, $feed_data, $atts );

							$report[ $feed_id ]['did_update'] = 'yes';
						}
					} else {
						$report[ $feed_id ]['did_update'] = 'no - not recently requested';
					}


				} else {
					$report[ $feed_id ]['did_update'] = 'no - missing atts';
				}

			} else {
				$report[ $feed_id ]['did_update'] = 'no - no transient found';
			}

		}

		update_option( 'sbi_cron_report', $report, false );
	}

	/**
	 * Update a single feed cache based on settings
	 *
	 * Pro - Logic added for recent hashtag feeds that require retrieving
	 *       old posts from the custom database tables
	 *
	 * @param $instagram_feed_settings
	 * @param $feed_data
	 * @param $atts
	 * @param bool $include_resize
	 *
	 * @return object
	 *
	 * @since 5.0
	 */
	public static function do_single_feed_cron_update( $instagram_feed_settings, $feed_data, $atts, $include_resize = true ) {
		$instagram_feed_settings->set_feed_type_and_terms();
		$instagram_feed_settings->set_transient_name();
		$transient_name = $instagram_feed_settings->get_transient_name();
		$settings = $instagram_feed_settings->get_settings();
		$feed_type_and_terms = $instagram_feed_settings->get_feed_type_and_terms();

		$instagram_feed = new SB_Instagram_Feed_Pro( $transient_name );

		while ( $instagram_feed->need_posts( $settings['num'] ) && $instagram_feed->can_get_more_posts() ) {
			$instagram_feed->add_remote_posts( $settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
		}

		if ( $instagram_feed->out_of_next_pages() && $instagram_feed->should_look_for_db_only_posts( $settings, $feed_type_and_terms ) ) {
			$instagram_feed->add_report( 'Adding Db only posts' );

			$instagram_feed->add_db_only_posts( $transient_name, $settings, $feed_type_and_terms );
		}

		$to_cache = array(
			'atts' => $atts,
			'last_requested' => $feed_data['last_requested'],
			'last_retrieve' => time()
		);

		$instagram_feed->set_cron_cache( $to_cache, $instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );

		if ( $instagram_feed->need_header( $settings, $feed_type_and_terms ) ) {
			$instagram_feed->set_remote_header_data( $settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
			$header_data = $instagram_feed->get_header_data();
			if ( $settings['stories'] && ! empty( $header_data ) ) {
				$instagram_feed->set_remote_stories_data( $settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
			}
			$instagram_feed->cache_header_data( $instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );
		}

		if ( $include_resize ) {
			$post_data = $instagram_feed->get_post_data();
			$post_data = array_slice( $post_data, 0, $settings['num'] );
			$fill_in_timestamp = date( 'Y-m-d H:i:s', time() + 150 );

			$image_sizes = array(
				'personal' => array( 'full' => 640, 'low' => 320, 'thumb' => 150 ),
				'business' => array( 'full' => 640, 'low' => 320, 'thumb' => 150 )
			);

			$post_set = new SB_Instagram_Post_Set( $post_data, $transient_name, $fill_in_timestamp, $image_sizes );

			$post_set->maybe_save_update_and_resize_images_for_posts();
		}

		do_action( 'sbi_after_single_feed_cron_update', $transient_name );

		return $instagram_feed;
	}

}