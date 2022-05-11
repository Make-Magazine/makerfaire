<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class SW_Cron_Updater
{
	public static function do_feed_updates() {
		$feed_caches = SW_Cron_Updater::get_feed_cache_option_names();
		shuffle(  $feed_caches );

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

				$plugins_with_atts = isset( $feed_data['plugins_with_atts'] ) ? $feed_data['plugins_with_atts'] : false;

				$plugins_with_atts = sbsw_filter_plugin_with_atts_for_compatibility( $plugins_with_atts );

				$last_retrieve = isset( $feed_data['last_retrieve'] ) ? (int)$feed_data['last_retrieve'] : 0;
				$last_requested = isset( $feed_data['last_requested'] ) ? (int)$feed_data['last_requested'] : false;
				$report[ $feed_id ]['last_retrieve'] = date( 'Y-m-d H:i:s', $last_retrieve );
				if ( $plugins_with_atts !== false ) {

					if ( ! $last_requested || $last_requested > (time() - 60*60*24*60) ) {
						$atts = isset( $feed_data['atts'] ) ? $feed_data['atts'] : false;

						SW_Cron_Updater::do_single_feed_cron_update( $plugins_with_atts, $atts );

						$report[ $feed_id ]['did_update'] = 'yes';
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

		update_option( 'sbsw_cron_report', $report, false );
	}

	public static function do_single_feed_cron_update( $plugins_with_atts, $atts, $include_resize = true ) {
		$database_settings = sbsw_get_database_settings();

		$atts = is_array( $atts ) ? $atts : array();
		if ( empty( $atts['includewords'] ) && ! empty( $database_settings['includewords'] ) ) {
			$atts['includewords'] = $database_settings['includewords'];
		}
		if ( empty( $atts['excludewords'] ) && ! empty( $database_settings['excludewords'] ) ) {
			$atts['excludewords'] = $database_settings['excludewords'];
		}
		if ( empty( $atts['hidephotos'] ) && ! empty( $database_settings['hidephotos'] ) ) {
			$atts['hidephotos'] = $database_settings['hidephotos'];
		}

		$plugins_with_atts = sbsw_merge_atts( $plugins_with_atts, $atts );

		$plugins_types_and_terms = array();
		if ( isset( $plugins_with_atts['instagram-feed'] ) ) {
			$if_atts = $plugins_with_atts['instagram-feed'];
			$if_database_settings = sbi_get_database_settings();

			$instagram_feed_settings = new SB_Instagram_Settings_Pro( $if_atts, $if_database_settings );

			$instagram_feed_settings->set_feed_type_and_terms();
			$if_settings = $instagram_feed_settings->get_settings();
			$if_feed_type_and_terms = $instagram_feed_settings->get_feed_type_and_terms();

			$plugins_types_and_terms['instagram'] = $if_feed_type_and_terms;
		}

		if ( isset( $plugins_with_atts['custom-facebook-feed'] ) ) {
			$fb_atts = $plugins_with_atts['custom-facebook-feed'];
			if ( isset( $fb_atts['accesstoken'] ) ) {
				$fb_atts['ownaccesstoken'] = 'on';
			}
			$fb_atts = cff_get_processed_options( $fb_atts );
			$facebook_feed_settings = new CustomFacebookFeed\CFF_Settings_Pro( $fb_atts );

			$facebook_feed_settings->set_feed_type_and_terms();
			$fb_settings = $facebook_feed_settings->get_settings();
			$fb_feed_type_and_terms = $facebook_feed_settings->get_feed_type_and_terms();

			$plugins_types_and_terms['facebook'] = $fb_feed_type_and_terms;

		}

		if ( isset( $plugins_with_atts['youtube-feed'] ) ) {
			$yt_atts              = $plugins_with_atts['youtube-feed'];
			$yt_database_settings = sby_get_database_settings();

			$youtube_feed_settings = new SBY_Settings_Pro( $yt_atts, $yt_database_settings );
			$youtube_feed_settings->set_feed_type_and_terms();
			$yt_settings                           = $youtube_feed_settings->get_settings();
			$yt_feed_type_and_terms                = $youtube_feed_settings->get_feed_type_and_terms();

			$plugins_types_and_terms['youtube'] = $yt_feed_type_and_terms;
		}

		if ( isset( $plugins_with_atts['custom-twitter-feed'] ) || isset( $plugins_with_atts['custom-twitter-feeds'] ) ) {
			$tw_atts              = isset( $plugins_with_atts['custom-twitter-feed'] ) ? $plugins_with_atts['custom-twitter-feed'] : $plugins_with_atts['custom-twitter-feeds'];

			$twitter_feed_settings = new CTF_Settings_Pro( $tw_atts );
			$twitter_feed_settings->set_feed_type_and_terms();
			$tw_settings                           = $twitter_feed_settings->get_settings();
			$tw_feed_type_and_terms                = $twitter_feed_settings->get_feed_type_and_terms();

			$plugins_types_and_terms['twitter'] = $tw_feed_type_and_terms;
		}

		$highest_num_api_connections = 0;
		$plugin_num_api_connections = array();
		foreach ( $plugins_types_and_terms as $plugin => $plugins_types_and_term ) {
			$num_things = 0;
			foreach ( $plugins_types_and_term as $api_connections ) {
				$count = count( $api_connections );
				$num_things = $num_things + $count;
			}
			$plugin_num_api_connections[ $plugin ] = $num_things;
			if ( $num_things > $highest_num_api_connections ) {
				$highest_num_api_connections = $num_things;
			}
		}

		$database_settings = sbsw_get_database_settings();

		$social_wall_settings = new SW_Settings( $atts, $database_settings, $plugins_types_and_terms );
		$social_wall_settings->set_transient_name();
		$transient_name = $social_wall_settings->get_transient_name();
		$settings = $social_wall_settings->get_settings();

		$social_wall_feed = new SW_Feed( $transient_name );
		$social_wall_feed->set_non_post_related_data_from_cache();
		$social_wall_feed->set_last_retrieve( time() );

		$social_wall_feed->set_plugins_with_atts( $plugins_with_atts );

		$wall_posts = array();
		$wall_next_pages = array();
		$wall_account_data = array();
		$wall_misc_data = array();
		$plugins_index = array();
		$one_post_retrieved = false;
		$transient_name = 'sw_CACHE';

		$num_needed = $settings['num'];
		if ( isset( $plugins_with_atts['instagram-feed'] ) ) {
			$instagram_feed = new SB_Instagram_Feed_Pro( $transient_name );
			$if_settings['num'] = $settings['num'];
			$if_settings['minnum'] = $settings['num'];
			$if_settings['apinum'] = $settings['num'] * $highest_num_api_connections;

			$if_settings['showheader'] = true;

			if ( $instagram_feed->need_posts( $num_needed ) && $instagram_feed->can_get_more_posts() ) {
				while ( $instagram_feed->need_posts( $num_needed ) && $instagram_feed->can_get_more_posts() ) {
					$instagram_feed->add_remote_posts( $if_settings, $if_feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
				}
			}
			$post_data = $instagram_feed->get_post_data();
			if ( ! empty( $post_data ) ) {
				$one_post_retrieved = true;
			}

			$wall_posts[] = $post_data;
			$wall_next_pages[] = $instagram_feed->get_next_pages();
			$wall_misc_data['instagram'] = array();
			$plugins_index[] = 'instagram-feed';
		}

		if ( isset( $plugins_with_atts['custom-facebook-feed'] ) ) {
			$facebook_feed = new CustomFacebookFeed\CFF_Feed_Pro( $transient_name );

			$fb_settings['num'] = $settings['num'];
			$fb_settings['minnum'] = $settings['num'];
			$fb_settings['apinum'] = $settings['num'] * $highest_num_api_connections;

			$fb_settings['showheader'] = true;

			$wall_account_data[] = array();

			if ( $facebook_feed->need_posts( $num_needed ) && $facebook_feed->can_get_more_posts() ) {
				while ( $facebook_feed->need_posts( $num_needed ) && $facebook_feed->can_get_more_posts() ) {
					$facebook_feed->add_remote_posts( $fb_settings );
				}
			}

			$post_data = $facebook_feed->get_post_data();
			if ( ! empty( $post_data ) ) {
				$one_post_retrieved = true;
			}
			$wall_posts[] = $post_data;
			$wall_next_pages[] = $facebook_feed->get_next_pages();
			$wall_misc_data['facebook'] = array();

			$plugins_index[] = 'custom-facebook-feed';
		}

		if ( isset( $plugins_with_atts['youtube-feed'] ) ) {
			$youtube_feed = new SBY_Feed_Pro( $transient_name );
			$yt_settings['num'] = $settings['num'];
			$yt_settings['minnum'] = $settings['num'];
			$yt_settings['apinum'] = $settings['num'] * $highest_num_api_connections;

			$yt_settings['showheader'] = true;

			if ( $youtube_feed->need_header( $yt_settings, $yt_feed_type_and_terms ) ) {
				$youtube_feed->set_remote_header_data( $yt_settings, $yt_feed_type_and_terms, $youtube_feed_settings->get_connected_accounts_in_feed() );
				$first_user_header_data = $youtube_feed->get_header_data();
				$first_user = SBY_Parse_Pro::get_channel_id( $first_user_header_data );
				$wall_account_data[] = array(
					$first_user => $first_user_header_data
				);
			} else {
				$wall_account_data[] = array();
			}

			if ( $youtube_feed->need_posts( $yt_settings['num'] ) && $youtube_feed->can_get_more_posts() ) {
				while ( $youtube_feed->need_posts( $yt_settings['num'] ) && $youtube_feed->can_get_more_posts() ) {
					$youtube_feed->add_remote_posts( $yt_settings, $yt_feed_type_and_terms, $youtube_feed_settings->get_connected_accounts_in_feed() );
					$posts = $youtube_feed->get_post_data();
					$ids_in_feed = array();
					$non_duplicate_posts = array();
					$removed = array();

					foreach ( $posts as $post ) {
						$post_id = SBY_Parse::get_video_id( $post );
						if ( ! in_array( $post_id, $ids_in_feed, true ) ) {
							$ids_in_feed[] = $post_id;
							$non_duplicate_posts[] = $post;
						} else {
							$removed[] = $post_id;
						}
					}

					$youtube_feed->set_post_data( $non_duplicate_posts );
				}
			}
			$post_data = $youtube_feed->get_post_data();
			if ( ! empty( $post_data ) ) {
				$one_post_retrieved = true;

				$vid_ids = array();
				foreach ( $post_data as $post ) {
					$vid_ids[] = SBY_Parse::get_video_id( $post );
				}
				sby_process_post_set_caching( $vid_ids, $youtube_feed_settings->get_transient_name() );
			}
			$wall_posts[] = $post_data;
			$wall_next_pages[] = $youtube_feed->get_next_pages();
			$wall_misc_data['youtube'] = $youtube_feed->get_misc_data( '', $post_data );

			$plugins_index[] = 'youtube-feed';
		}

		if ( isset( $plugins_with_atts['custom-twitter-feed'] ) || isset( $plugins_with_atts['custom-twitter-feeds'] ) ) {
			$twitter_feed = new CTF_Feed_Pro( $transient_name );
			$tw_settings['num'] = $settings['num'];
			$tw_settings['minnum'] = $settings['num'];
			$tw_settings['apinum'] = $settings['num'] * $highest_num_api_connections;
			$tw_settings['showheader'] = true;

			if ( true ) {
				$twitter_feed->set_remote_header_data( $tw_settings, $tw_feed_type_and_terms );
				$header_data = $twitter_feed->get_header_data();
				if ( isset( $header_data[0] ) ) {
					$first_user = CTF_Parse_Pro::get_user_name( $header_data[0] );
					$wall_account_data['twitter'] = array(
						$first_user => $header_data[0]
					);
				}

			} else {
				$wall_account_data['twitter'] = array();
			}

			if ( $twitter_feed->need_posts( $tw_settings['num'] ) && $twitter_feed->can_get_more_posts() ) {
				while ( $twitter_feed->need_posts( $tw_settings['num'] ) && $twitter_feed->can_get_more_posts() ) {
					$twitter_feed->add_remote_posts( $tw_settings, $tw_feed_type_and_terms );
				}
			}
			$post_data = $twitter_feed->get_post_data();
			if ( ! empty( $post_data ) ) {
				$one_post_retrieved = true;
			}
			$wall_posts[] = $post_data;
			$wall_next_pages[] = $twitter_feed->get_next_pages();
			$wall_misc_data['twitter'] = array();

			$plugins_index[] = 'twitter-feed';
		}

		if ( $one_post_retrieved ) {
			$social_wall_feed->set_background_processes_flag( true );
		}
		$social_wall_feed->set_misc_data( $wall_misc_data );
		$social_wall_feed->process_raw_posts( $wall_posts, $settings );

		$social_wall_feed->cache_feed_data( 60*60*24*60, $wall_next_pages );

		if ( isset( $plugins_with_atts['instagram-feed'] ) ) {
			if ( ! isset( $instagram_feed ) ) {
				$instagram_feed = new SB_Instagram_Feed_Pro( $transient_name );
			}
			$if_settings['showheader'] = true;
			$user_index = 0;

			foreach ( $if_feed_type_and_terms as $feed_type => $terms ) {

				foreach ( $terms as $term ) {
					if ( $feed_type === 'users' ) {
						$this_type_and_terms = array(
							'users' => array(
								$if_feed_type_and_terms['users'][ $user_index ]
							)
						);
						$user_index++;
						$instagram_feed->set_remote_header_data( $if_settings, $this_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
						$first_user_header_data = $instagram_feed->get_header_data();
						$first_user = SB_Instagram_Parse_Pro::get_username( $first_user_header_data );
						$wall_account_data['instagram'][ $first_user ] = $first_user_header_data;

					} elseif ( $feed_type === 'tagged' ) {
						$this_type_and_terms = array(
							'tagged' => array(
								$if_feed_type_and_terms['tagged'][ $user_index ]
							)
						);
						$user_index++;
						$instagram_feed->set_remote_header_data( $if_settings, $this_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
						$first_user_header_data = $instagram_feed->get_header_data();
						$first_user = SB_Instagram_Parse_Pro::get_username( $first_user_header_data );
						$wall_account_data['instagram'][ $first_user ] = $first_user_header_data;

					} else {
						$wall_account_data['instagram'][ $feed_type ] = SB_Instagram_Feed_Pro::get_generic_header_data( $feed_type, $term );
					}
				}

			}
		}


		if ( isset( $plugins_with_atts['custom-facebook-feed'] ) ) {
			$wall_account_data['facebook'] = array();
		}

		if ( isset( $plugins_with_atts['youtube-feed'] ) ) {
			if ( ! isset( $youtube_feed ) ) {
				$youtube_feed = new SBY_Feed_Pro( $transient_name );
			}
			$yt_settings['showheader'] = true;

			if ( $youtube_feed->need_header( $yt_settings, $yt_feed_type_and_terms ) ) {
				$youtube_feed->set_remote_header_data( $yt_settings, $yt_feed_type_and_terms, $youtube_feed_settings->get_connected_accounts_in_feed() );
				$first_user_header_data = $youtube_feed->get_header_data();
				$first_user = SBY_Parse_Pro::get_channel_id( $first_user_header_data );
				$wall_account_data['youtube'] = array(
					$first_user => $first_user_header_data
				);
			} else {
				$wall_account_data['youtube'] = array();
			}
		}

		if ( isset( $plugins_with_atts['custom-twitter-feed'] ) || isset( $plugins_with_atts['custom-twitter-feeds'] ) ) {
			if ( ! isset( $twitter_feed ) ) {
				$twitter_feed = new CTF_Feed_Pro( $transient_name );
			}
			$tw_settings['showheader'] = true;

			if ( true ) {
				$twitter_feed->set_remote_header_data( $tw_settings, $tw_feed_type_and_terms );
				$header_data = $twitter_feed->get_header_data();
				if ( isset( $header_data[0] ) ) {
					$first_user = CTF_Parse_Pro::get_user_name( $header_data[0] );
					$wall_account_data['twitter'] = array(
						$first_user => $header_data[0]
					);
				}
			} else {
				$wall_account_data['twitter'] = array();
			}
		}
		$social_wall_feed->set_header_data( $wall_account_data );
		$social_wall_feed->cache_header_data( 60*60*24*60, $wall_account_data );

		do_action( 'sbsw_after_single_feed_cron_update', $transient_name );
	}

	public static function get_feed_cache_option_names() {
		global $wpdb;
		$feed_caches = apply_filters( 'sbsw_feed_cache_option_names', array() );

		if ( ! empty( $feed_caches ) ) {
			return $feed_caches;
		}

		$results = $wpdb->get_results( "
		SELECT option_name
        FROM $wpdb->options
        WHERE `option_name` LIKE ('%\_transient\_sbsw\_%')
        AND `option_name` NOT LIKE ('%\_transient\_sbsw\_header%')
        AND `option_name` NOT LIKE ('%\_transient\_sbsw\_misc%');", ARRAY_A );

		if ( isset( $results[0] ) ) {
			$feed_caches = $results;
		}

		return $feed_caches;
	}

	public static function start_cron_job( $sbi_cache_cron_interval, $sbi_cache_cron_time, $sbi_cache_cron_am_pm ) {
		wp_clear_scheduled_hook( 'sbsw_feed_update' );

		if ( $sbi_cache_cron_interval === '12hours' || $sbi_cache_cron_interval === '24hours' ) {
			$relative_time_now = time() + sbsw_get_utc_offset();
			$base_day = strtotime( date( 'Y-m-d', $relative_time_now ) );
			$add_time = $sbi_cache_cron_am_pm === 'pm' ? (int)$sbi_cache_cron_time + 12 : (int)$sbi_cache_cron_time;
			$utc_start_time = $base_day + (($add_time * 60 * 60) - sbsw_get_utc_offset());

			if ( $utc_start_time < time() ) {
				if ( $sbi_cache_cron_interval === '12hours' ) {
					$utc_start_time += 60*60*12;
				} else {
					$utc_start_time += 60*60*24;
				}
			}

			if ( $sbi_cache_cron_interval === '12hours' ) {
				wp_schedule_event( $utc_start_time, 'twicedaily', 'sbsw_feed_update' );
			} else {
				wp_schedule_event( $utc_start_time, 'daily', 'sbsw_feed_update' );
			}
		} else {

			if ( $sbi_cache_cron_interval === '30mins' ) {
				wp_schedule_event( time(), 'sw30mins', 'sbsw_feed_update' );
			} else {
				wp_schedule_event( time(), 'hourly', 'sbsw_feed_update' );
			}
		}

	}
}