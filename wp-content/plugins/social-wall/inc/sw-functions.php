<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


add_shortcode( 'social-wall', 'sbsw_feed_init' );
function sbsw_feed_init( $atts, $content = null ) {
	wp_enqueue_script( 'sbsw_scripts' );
	$plugins_with_atts = sbsw_parse_shortcodes( $content );
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
	$plugins_with_atts = sbsw_filter_plugin_with_atts_for_compatibility( $plugins_with_atts );

	$plugins_types_and_terms = array();
	$plugin_settings = array();
	if ( isset( $plugins_with_atts['instagram-feed'] ) ) {
		$if_atts = $plugins_with_atts['instagram-feed'];
		$if_database_settings = sbi_get_database_settings();

		$instagram_feed_settings = new SB_Instagram_Settings_Pro( $if_atts, $if_database_settings );

		$instagram_feed_settings->set_feed_type_and_terms();
		$if_settings = $instagram_feed_settings->get_settings();
		$if_feed_type_and_terms = $instagram_feed_settings->get_feed_type_and_terms();

		$plugins_types_and_terms['instagram'] = $if_feed_type_and_terms;
		$plugin_settings['instagram'] = $if_settings;
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
		$plugin_settings['facebook'] = $fb_settings;
	}
	if ( isset( $plugins_with_atts['youtube-feed'] ) ) {
		$yt_atts              = $plugins_with_atts['youtube-feed'];
		$yt_database_settings = sby_get_database_settings();

		$youtube_feed_settings = new SBY_Settings_Pro( $yt_atts, $yt_database_settings );
		$youtube_feed_settings->set_feed_type_and_terms();
		$yt_settings                           = $youtube_feed_settings->get_settings();
		$yt_feed_type_and_terms                = $youtube_feed_settings->get_feed_type_and_terms();

		$plugins_types_and_terms['youtube'] = $yt_feed_type_and_terms;
		$plugin_settings['youtube'] = $yt_settings;
	}
	if ( isset( $plugins_with_atts['custom-twitter-feed'] ) || isset( $plugins_with_atts['custom-twitter-feeds'] ) ) {
		$tw_atts              = isset( $plugins_with_atts['custom-twitter-feed'] ) ? $plugins_with_atts['custom-twitter-feed'] : $plugins_with_atts['custom-twitter-feeds'];

		$twitter_feed_settings = new CTF_Settings_Pro( $tw_atts );
		$twitter_feed_settings->set_feed_type_and_terms();
		$tw_settings                           = $twitter_feed_settings->get_settings();
		$tw_feed_type_and_terms                = $twitter_feed_settings->get_feed_type_and_terms();

		$plugins_types_and_terms['twitter'] = $tw_feed_type_and_terms;
		$plugin_settings['twitter'] = $tw_settings;
	}

	$social_wall_settings = new SW_Settings( $atts, $database_settings, $plugins_types_and_terms, $plugin_settings );
	$social_wall_settings->set_transient_name();
	$transient_name = $social_wall_settings->get_transient_name();
	$settings = $social_wall_settings->get_settings();

	$social_wall_feed = new SW_Feed( $transient_name );
	$social_wall_feed->set_plugins_with_atts( $plugins_with_atts );
	$social_wall_feed->set_atts( $atts );
	if ( ! $social_wall_feed->regular_cache_exists() ) {
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

		$wall_posts = array();
		$wall_next_pages = array();
		$wall_account_data = array();
		$wall_misc_data = array();
		$plugins_index = array();
		$one_post_retrieved = false;
		$transient_name = 'sw_CACHE';
		$social_wall_feed->set_last_requested( time() );
		$social_wall_feed->set_last_retrieve( time() );
		$num_needed = $settings['num'] * $highest_num_api_connections;

		if ( isset( $plugins_with_atts['instagram-feed'] ) ) {
			$instagram_feed = new SB_Instagram_Feed_Pro( $transient_name );

			$if_settings['num'] = $settings['num'];
			$if_settings['minnum'] = $settings['num'];
			$if_settings['apinum'] = $settings['num'];
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
			$tw_settings['apinum'] = $settings['num'];
            $tw_settings['apinum'] = $settings['num'] * $highest_num_api_connections;
			$tw_settings['showheader'] = true;

            $twitter_feed->set_remote_header_data( $tw_settings, $tw_feed_type_and_terms );
            $header_data = $twitter_feed->get_header_data();
            if ( isset( $header_data[0] ) ) {
                $first_user = CTF_Parse_Pro::get_user_name( $header_data[0] );
                $wall_account_data['twitter'] = array(
                    $first_user => $header_data[0]
                );
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
		$social_wall_feed->cache_feed_data( SBSW_CRON_UPDATE_CACHE_TIME, $wall_next_pages );
	} else {
		$social_wall_feed->set_post_data_from_cache();
		if ( $social_wall_feed->should_update_last_requested() ) {
			$social_wall_feed->add_report( 'updating last requested' );
			$to_update = array(
				'last_requested' => time(),
			);
			$social_wall_feed->update_cache( $to_update );
		}
		if ( $social_wall_feed->feed_is_not_updating() ) {
			$social_wall_feed->add_report( 'not updating, forcing refresh' );
			$social_wall_feed->delete_transients();
		}
	}

	if ( ! $social_wall_feed->regular_header_cache_exists() ) {
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
		$wall_account_data = isset( $wall_account_data ) ? $wall_account_data : array();
		$social_wall_feed->set_header_data( $wall_account_data );
		$social_wall_feed->cache_header_data( SBSW_CRON_UPDATE_CACHE_TIME, $wall_account_data );

	} else {
		$social_wall_feed->set_header_data_from_cache();
	}

	$html = '';
	if ( ! empty( $_GET['sbsw_debug'] ) ) {
		$html .= sbsw_debug_report( $social_wall_feed, $social_wall_settings );
	}

	$html .= $social_wall_feed->get_the_feed_html( $social_wall_feed->get_post_data(), $social_wall_feed->get_header_data(), $settings, $plugins_with_atts );
	return $html;
}

function sbsw_get_next_post_set() {
	if ( ! isset( $_POST['feed_id'] ) || strpos( $_POST['feed_id'], 'sbsw' ) === false ) {
		die( 'invalid feed ID');
	}

	$feed_id = sanitize_text_field( $_POST['feed_id'] );
	$atts_raw = isset( $_POST['atts'] ) ? json_decode( stripslashes( $_POST['atts'] ), true ) : array();
	$sanitized_atts = array();
	if ( is_array( $atts_raw ) ) {
		foreach ( $atts_raw as $plugin => $plugin_atts_raw ) {
			if ( is_array( $plugin_atts_raw ) ) {
				array_map( 'sanitize_text_field', $plugin_atts_raw );
				$sanitized_atts[ $plugin ] = $plugin_atts_raw;
			} else {
				$sanitized_atts[ $plugin ] = array();
			}
		}
	} else {
		$sanitized_atts = array();
	}
	$atts = $sanitized_atts; // now sanitized

	$page = isset( $_POST['page'] ) ? (int)$_POST['page'] : 1;
	$filter_type = isset( $_POST['filter_type'] ) ? sanitize_text_field( $_POST['filter_type'] ) : false;

	$plugins_with_atts = $atts;
	$plugins_types_and_terms = array();
	$plugin_settings = array();
	if ( isset( $plugins_with_atts['instagram-feed'] ) ) {
		$if_atts = $plugins_with_atts['instagram-feed'];
		$if_database_settings = sbi_get_database_settings();
		$instagram_feed_settings = new SB_Instagram_Settings_Pro( $if_atts, $if_database_settings );
		$instagram_feed_settings->set_feed_type_and_terms();
		$instagram_feed_settings->set_transient_name();
		$if_transient_name = $instagram_feed_settings->get_transient_name();
		$if_settings = $instagram_feed_settings->get_settings();
		$if_feed_type_and_terms = $instagram_feed_settings->get_feed_type_and_terms();

		$plugins_types_and_terms['instagram'] = $if_feed_type_and_terms;
		$plugin_settings['instagram'] = $if_settings;
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
		$plugin_settings['facebook'] = $fb_settings;
	}

	if ( isset( $plugins_with_atts['youtube-feed'] ) ) {
		$yt_atts              = $plugins_with_atts['youtube-feed'];
		$yt_database_settings = sby_get_database_settings();

		$youtube_feed_settings = new SBY_Settings_Pro( $yt_atts, $yt_database_settings );
		$youtube_feed_settings->set_feed_type_and_terms();
		$youtube_feed_settings->set_transient_name();
		$yt_transient_name                     = $youtube_feed_settings->get_transient_name();
		$yt_settings                           = $youtube_feed_settings->get_settings();
		$yt_feed_type_and_terms                = $youtube_feed_settings->get_feed_type_and_terms();

		$plugins_types_and_terms['youtube'] = $yt_feed_type_and_terms;
		$plugin_settings['youtube'] = $yt_settings;
	}

	if ( isset( $plugins_with_atts['custom-twitter-feed'] ) || isset( $plugins_with_atts['custom-twitter-feeds'] ) ) {
		$tw_atts              = isset( $plugins_with_atts['custom-twitter-feed'] ) ? $plugins_with_atts['custom-twitter-feed'] : $plugins_with_atts['custom-twitter-feeds'];

		$twitter_feed_settings = new CTF_Settings_Pro( $tw_atts );
		$twitter_feed_settings->set_feed_type_and_terms();
		$tw_settings                           = $twitter_feed_settings->get_settings();

		$tw_feed_type_and_terms                = $twitter_feed_settings->get_feed_type_and_terms();

		$plugins_types_and_terms['twitter'] = $tw_feed_type_and_terms;
		$plugin_settings['twitter'] = $tw_settings;
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

	$social_wall_settings = new SW_Settings( $atts, $database_settings, $plugins_types_and_terms, $plugin_settings );
	$social_wall_settings->set_transient_name();
	$transient_name = $social_wall_settings->get_transient_name();
	$settings = $social_wall_settings->get_settings();
	$offset = $settings['minnum'] * $page;

	$social_wall_feed = new SW_Feed( $transient_name );

	$next_pages = array();
	if ( $social_wall_feed->regular_cache_exists() ) {
		$social_wall_feed->set_post_data_from_cache();
		$next_pages = $social_wall_feed->get_next_pages();
	} else {
		$settings['num'] = $settings['num'] * $highest_num_api_connections * ($page + 1);
	}

	if ( $social_wall_feed->need_posts( $settings['num'], $offset, $page ) && $social_wall_feed->can_get_more_posts() ) {
		$wall_posts        = array();
		$wall_next_pages   = array();
		$wall_account_data = array();
		$wall_misc_data = array();
		$plugins_index     = array();
		$transient_name    = 'sw_CACHE';

		$num_needed = $settings['num'];
		$social_wall_feed->add_report( 'Num needed ' . $num_needed);

		$next_page_index = 0;

		if ( isset( $plugins_with_atts['instagram-feed'] ) ) {
			$instagram_feed = new SB_Instagram_Feed_Pro( $transient_name );

			if ( isset( $next_pages[ $next_page_index ] ) ) {
				$instagram_feed->set_next_pages( $next_pages[ $next_page_index ] );
			}
			$next_page_index++;
			$if_settings['num']        = $settings['num'];
            $if_settings['apinum'] = $settings['num'];

			$if_settings['showheader'] = true;

			if ( $instagram_feed->need_header( $if_settings, $if_feed_type_and_terms ) ) {
				$instagram_feed->set_remote_header_data( $if_settings, $if_feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
				$first_user_header_data = $instagram_feed->get_header_data();
				$first_user             = SB_Instagram_Parse_Pro::get_username( $first_user_header_data );
				$wall_account_data[]    = array(
					$first_user => $first_user_header_data
				);
			} else {
				$wall_account_data[] = array();
			}

			if ( $instagram_feed->need_posts( $num_needed, 0 ) && $instagram_feed->can_get_more_posts() ) {
				while ( $instagram_feed->need_posts( $num_needed, 0 ) && $instagram_feed->can_get_more_posts() ) {
					$instagram_feed->add_remote_posts( $if_settings, $if_feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
				}
			}

			$wall_posts[]      = $instagram_feed->get_post_data();
			$wall_next_pages[] = $instagram_feed->get_next_pages();
			$plugins_index[]   = 'instagram-feed';
		}

		if ( isset( $plugins_with_atts['custom-facebook-feed'] ) ) {
			$facebook_feed = new CustomFacebookFeed\CFF_Feed_Pro( $transient_name );

			if ( isset( $next_pages[ $next_page_index ] ) ) {
				$facebook_feed->set_next_pages( $next_pages[ $next_page_index ] );
			}
			$next_page_index++;

			$fb_settings['num'] = $settings['num'];
			$fb_settings['minnum'] = $settings['num'];
			$fb_settings['apinum'] = $settings['num'];

			$fb_settings['showheader'] = true;

			$wall_account_data[] = array();

			if ( $facebook_feed->need_posts( $num_needed ) && $facebook_feed->can_get_more_posts() ) {
				while ( $facebook_feed->need_posts( $num_needed ) && $facebook_feed->can_get_more_posts() ) {
					$facebook_feed->add_remote_posts( $fb_settings );
				}
			}
			$wall_posts[] = $facebook_feed->get_post_data();
			$wall_next_pages[] = $facebook_feed->get_next_pages();

			$plugins_index[] = 'custom-facebook-feed';
		}

		if ( isset( $plugins_with_atts['youtube-feed'] ) ) {
			$youtube_feed = new SBY_Feed_Pro( $transient_name );
			if ( isset( $next_pages[ $next_page_index ] ) ) {
				$youtube_feed->set_next_pages( $next_pages[ $next_page_index ] );
			}
			$next_page_index++;

			$yt_settings['num']        = $settings['num'];
            $yt_settings['apinum'] = $settings['num'];

			$yt_settings['showheader'] = true;

			if ( $youtube_feed->need_header( $yt_settings, $yt_feed_type_and_terms ) ) {
				$youtube_feed->set_remote_header_data( $yt_settings, $yt_feed_type_and_terms, $youtube_feed_settings->get_connected_accounts_in_feed() );
				$first_user_header_data = $youtube_feed->get_header_data();
				$first_user             = SBY_Parse_Pro::get_channel_id( $first_user_header_data );
				$wall_account_data[]    = array(
					$first_user => $first_user_header_data
				);
			} else {
				$wall_account_data[] = array();
			}

			if ( $youtube_feed->need_posts( $num_needed, 0 ) && $youtube_feed->can_get_more_posts() ) {
				while ( $youtube_feed->need_posts( $num_needed, 0 ) && $youtube_feed->can_get_more_posts() ) {
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
			$wall_posts[]      = $post_data;

			if ( ! empty( $post_data ) ) {
				$vid_ids = array();
				foreach ( $post_data as $post ) {
					$vid_ids[] = SBY_Parse::get_video_id( $post );
				}
				sby_process_post_set_caching( $vid_ids, $youtube_feed_settings->get_transient_name() );
			}
			$wall_next_pages[] = $youtube_feed->get_next_pages();
			$wall_misc_data['youtube'] = $youtube_feed->get_misc_data( '', $post_data );
			$plugins_index[]   = 'youtube-feed';
		}

		if ( isset( $plugins_with_atts['custom-twitter-feed'] ) || isset( $plugins_with_atts['custom-twitter-feeds'] ) ) {
			$twitter_feed = new CTF_Feed_Pro( $transient_name );
			if ( isset( $next_pages[ $next_page_index ] ) ) {
				$twitter_feed->set_next_pages( $next_pages[ $next_page_index ] );
			}
			$next_page_index++;

			$tw_settings['num']        = $settings['num'];
            $tw_settings['apinum'] = $settings['num'];

			$tw_settings['showheader'] = true;

			if ( $twitter_feed->need_header( $tw_settings, $tw_feed_type_and_terms ) ) {
				$twitter_feed->set_remote_header_data( $tw_settings, $tw_feed_type_and_terms );
				$first_user_header_data = $twitter_feed->get_header_data();
				$first_user             = CTF_Parse_Pro::get_channel_id( $first_user_header_data );
				$wall_account_data[]    = array(
					$first_user => $first_user_header_data
				);
			} else {
				$wall_account_data[] = array();
			}

			if ( $twitter_feed->need_posts( $num_needed, 0 ) && $twitter_feed->can_get_more_posts() ) {
				while ( $twitter_feed->need_posts( $num_needed, 0 ) && $twitter_feed->can_get_more_posts() ) {
					$twitter_feed->add_remote_posts( $tw_settings, $tw_feed_type_and_terms );
				}
			}
			$wall_posts[]      = $twitter_feed->get_post_data();
			$wall_next_pages[] = $twitter_feed->get_next_pages();
			$plugins_index[]   = 'twitter-feed';
		}

		$social_wall_feed->append_misc_data( $wall_misc_data );
		$social_wall_feed->process_raw_posts( $wall_posts, $settings );
		$social_wall_feed->cache_feed_data( SBSW_CRON_UPDATE_CACHE_TIME, $wall_next_pages );
	}

	if ( ! $social_wall_feed->regular_header_cache_exists() ) {
		if ( isset( $plugins_with_atts['instagram-feed'] ) ) {
			if ( ! isset( $instagram_feed ) ) {
				$instagram_feed = new SB_Instagram_Feed_Pro( $transient_name );
			}
			$if_settings['showheader'] = true;
			if ( $instagram_feed->need_header( $if_settings, $if_feed_type_and_terms ) ) {
				$instagram_feed->set_remote_header_data( $if_settings, $if_feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
				$first_user_header_data = $instagram_feed->get_header_data();
				$first_user = SB_Instagram_Parse_Pro::get_username( $first_user_header_data );
				$wall_account_data['instagram'] = array(
					$first_user => $first_user_header_data
				);
			} else {
				$wall_account_data['instagram'] = array();
			}
		}

		if ( isset( $plugins_with_atts['custom-facebook-feed'] ) ) {
			$wall_account_data['facebook'] = array();
		}

		if ( isset( $plugins_with_atts['youtube-feed'] ) ) {
			if ( ! isset( $youtube_feed ) ) {
				$youtube_feed = new SBY_Feed_Pro( $transient_name );
			}
			$tw_settings['showheader'] = true;

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

			if ( $twitter_feed->need_header( $tw_settings, $tw_feed_type_and_terms ) ) {
				$twitter_feed->set_remote_header_data( $tw_settings, $tw_feed_type_and_terms );
				$first_user_header_data = $twitter_feed->get_header_data();
				$first_user = CTF_Parse_Pro::get_channel_id( $first_user_header_data );
				$wall_account_data['twitter'] = array(
					$first_user => $first_user_header_data
				);
			} else {
				$wall_account_data['twitter'] = array();
			}
		}

		$social_wall_feed->set_header_data( $wall_account_data );
		$social_wall_feed->cache_header_data( 180, $wall_account_data );

	} else {
		$social_wall_feed->set_header_data_from_cache();
	}

	$should_paginate = $social_wall_feed->should_use_pagination( $settings, $offset );

	$feed_status = array( 'shouldPaginate' => $should_paginate );
	$social_wall_feed->add_report( 'Num in cache ' . count( $social_wall_feed->get_post_data()));


	$post_data = $social_wall_feed->get_post_data();
	if ( $filter_type ) {
	    $post_data = SW_Feed::filter_for_plugin( $post_data, $filter_type );
	}

	$return = array(
		'html' => $social_wall_feed->get_the_items_html( $post_data, $social_wall_feed->get_header_data(), $settings, $offset ),
		'feedStatus' => $feed_status,
		'wallReport' => $social_wall_feed->get_report()
	);

    if ( $filter_type ) {
        $return['feedStatus']['filterOutOfPages'] = array();
	    $misc_data = $social_wall_feed->get_misc_data();
	    $next_pages = $social_wall_feed->get_next_pages();
	    $i = 0;
	    $more_posts_in_cache = count( $post_data ) > ($offset + $settings['num']);
	    foreach ( $misc_data as $plugin => $data ) {
	        if ( isset( $next_pages[ $i ] )
	            && $next_pages[ $i ] === false
	            && ! $more_posts_in_cache ) {
	            $return['feedStatus']['filterOutOfPages'][] = $plugin;
	        }
            $i ++;
	    }
	}

	if ( isset( $instagram_feed ) ) {
		$return['instagramReport'] = $instagram_feed->get_report();
	}
	if ( isset( $youtube_feed ) ) {
		$return['youtubeReport'] = $youtube_feed->get_report();
	}
	if ( isset( $twitter_feed ) ) {
		$return['twitterReport'] = $twitter_feed->get_report();
	}
	echo wp_json_encode( $return );


	die();
}
add_action( 'wp_ajax_sbsw_load_more_clicked', 'sbsw_get_next_post_set' );
add_action( 'wp_ajax_nopriv_sbsw_load_more_clicked', 'sbsw_get_next_post_set' );

//sbsw_background_processing
function sbsw_background_processing() {

	if ( ! isset( $_POST['feed_id'] ) ) {
		return;
	}
	$feed_id = sanitize_text_field( $_POST['feed_id'] );
	$atts_raw = isset( $_POST['atts'] ) ? json_decode( stripslashes( $_POST['atts'] ), true ) : array();
	$sanitized_atts = array();
	if ( is_array( $atts_raw ) ) {
		foreach ( $atts_raw as $plugin => $plugin_atts_raw ) {
			if ( is_array( $plugin_atts_raw ) ) {
				array_map( 'sanitize_text_field', $plugin_atts_raw );
				$sanitized_atts[ $plugin ] = $plugin_atts_raw;
			} else {
				$sanitized_atts[ $plugin ] = array();
			}

		}

	} else {
		$sanitized_atts = array();
	}
	$atts = $sanitized_atts; // now sanitized

	$return = array();
	if ( isset( $_POST['posts']['youtube'] ) && function_exists( 'sby_process_post_set_caching' ) ) {
	    $social_wall_feed = new SW_Feed( $feed_id );
        $social_wall_feed->set_post_data_from_cache();
	    $posts = $social_wall_feed->get_post_data();
	    $youtube_posts = SW_Feed::filter_for_plugin( $posts, 'youtube' );

		$info = sby_process_post_set_caching( $youtube_posts, $feed_id );
		$return['youtube'] = $info;
		$wall_misc_data = array();

		$final_info = array();

		foreach ( $info as $key => $info ) {

		    $stats = $info;

		    $stats['sby_view_count'] = $info['raw']['views'];
		    		    $stats['sby_like_count'] = $info['raw']['likes'];
		    $stats['sby_comment_count'] = $info['raw']['comments'];

		    $final_info[ $key ] = $stats;
		}

        $wall_misc_data['youtube']['stats'] = $final_info;
        $wall_next_pages = $social_wall_feed->get_next_pages();
        $social_wall_feed->append_misc_data( $wall_misc_data );
        $social_wall_feed->cache_feed_data( SBSW_CRON_UPDATE_CACHE_TIME, $wall_next_pages );
	}

	if ( isset( $_POST['posts']['twitter'] ) && function_exists( 'ctf_twitter_cards' ) ) {
		$url_item_batch = array();
		if ( isset( $_POST['posts']['twitter']['cards'] ) ) {
            foreach ( $_POST['posts']['twitter']['cards'] as $tc_item ) {
                $url_item_batch[] = array(
                    'id' => sanitize_text_field( $tc_item['id'] ),
                    'url' => esc_url_raw( $tc_item['url'] )
                );
            }
		}

		$twitter_card_batch = CTF_Twitter_Card_Manager::process_url_batch( $url_item_batch );

		$twitter_return = array();
		foreach ( $twitter_card_batch as $twitter_card_array ) {
			$url = $twitter_card_array['url'];

			$twitter_card = $twitter_card_array['twitter_card'];

			$image = CTF_Display_Elements_Pro::get_twitter_card_media_html( $twitter_card );
			$title = CTF_Parse_Pro::get_twitter_card_title( $twitter_card );
			$description = CTF_Parse_Pro::get_twitter_card_description( $twitter_card );
			$link_html = CTF_Display_Elements_Pro::get_icon( 'link' ) . CTF_Display_Elements_Pro::get_twitter_card_link_text( $url );

			$content = '';
			if ( ! empty( $title )
				|| ! empty( $description ) ) {
				$parts = array(
					'url' => $url,
					'image' => $image,
					'title' => $title,
					'description' => sbsw_maybe_shorten_text( $description, 150 ),
					'link_html' => $link_html
				);
				$content = SW_Display_Elements::get_boxed_content_html( $parts );
			}

			$twitter_return[ $twitter_card_array['id'] ]['cards'] = array(
				'html' => $content,
				'url' => $url,
				'is_new' => $twitter_card_array['is_new']
			);
		}

		$sanitized_ids = array();
		if ( class_exists( 'CTF_Resizer' ) && ! empty( $_POST['posts']['twitter']['resize'] ) ) {
            foreach ( $_POST['posts']['twitter']['resize'] as $id ) {
                $sanitized_ids[] = sanitize_text_field( $id );
            }
            $social_wall_feed = new SW_Feed( $feed_id );

            if ( $social_wall_feed->regular_cache_exists() ) {
                $social_wall_feed->set_post_data_from_cache();

                $posts = SW_Feed::filter_for_plugin( $social_wall_feed->get_post_data(), 'twitter' );

                $resizer = new CTF_Resizer( $sanitized_ids, $feed_id, $posts );
                if ( ! $resizer->image_resizing_disabled() ) {
                    $resizer->do_resizing();
                }
            }
		}


		$return['twitter'] = $twitter_return;
	}

	if ( isset( $_POST['posts']['instagram'] ) && function_exists( 'sbi_resize_posts_by_id' ) ) {
		$sanitized_ids = array();
		foreach ( $_POST['posts']['instagram'] as $id ) {
			$sanitized_ids[] = sanitize_text_field( $id );
		}
		//$if_atts = $atts['instagram'];
		$database_settings = sbi_get_database_settings();
		$instagram_feed_settings = new SB_Instagram_Settings_Pro( array(), $database_settings );

		$instagram_feed_settings->set_feed_type_and_terms();
		$instagram_feed_settings->set_transient_name();
		$transient_name = $instagram_feed_settings->get_transient_name();
		$settings = $instagram_feed_settings->get_settings();

		sbi_resize_posts_by_id( $sanitized_ids, $feed_id, $settings );

		$return['instagram'] = '1';
	}

	if ( isset( $_POST['posts']['facebook'] ) && function_exists( 'cff_process_submitted_resize_ids' ) ) {
		$sanitized_ids = array();
		foreach ( $_POST['posts']['facebook'] as $id ) {
			$sanitized_ids[] = sanitize_text_field( $id );
		}
		$social_wall_feed = new SW_Feed( $feed_id );

		if ( $social_wall_feed->regular_cache_exists() ) {
			$social_wall_feed->set_post_data_from_cache();

			$resizer = new CustomFacebookFeed\CFF_Resizer( $sanitized_ids, $feed_id, $social_wall_feed->get_post_data(), array( 'disableresize' => false ) );
			$resizer->do_resizing();
		}
		$return['facebook'] = '1';
	}

	echo wp_json_encode( $return );

	die();
}
add_action( 'wp_ajax_sbsw_background_processing', 'sbsw_background_processing' );
add_action( 'wp_ajax_nopriv_sbsw_background_processing', 'sbsw_background_processing' );

function sbsw_parse_shortcodes( $content ) {
	if ( false === strpos( $content, '[' ) ) {
		return $content;
	}

	$shortcode_tags = array(
		'instagram-feed' => 'instagram',
		'custom-twitter-feed' => 'twitter',
		'custom-twitter-feeds' => 'twitter',
		'youtube-feed' => 'youtube',
		'custom-facebook-feed' => 'facebook'
	);

	// Find all registered tag names in $content.
	preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $content, $matches );
	$tagnames = array_intersect( array_keys( $shortcode_tags ), $matches[1] );

	$pattern = get_shortcode_regex( $tagnames );
	$content = preg_match_all( "/$pattern/", $content, $atts_string );

	$plugins_with_atts = array();

	$i = 0;
	foreach ( $tagnames as $plugin ) {
	    $parsed_atts = shortcode_parse_atts( $atts_string[3][ $i ] );
	    if ( ! empty( $parsed_atts ) ) {
		    $plugins_with_atts[ $atts_string[2][ $i ] ] = $parsed_atts;
	    } else {
		    $plugins_with_atts[ $atts_string[2][ $i ] ] = array();
	    }
		$i++;
	}

	return $plugins_with_atts;
}

function sbsw_social_wall_is_minimum_version_for_instagram_feed() {
	if ( ! defined( 'SBI_MINIMUM_WALL_VERSION' ) ) {
		return false;
	}
	if ( version_compare( SWVER, SBI_MINIMUM_WALL_VERSION  ) < 0 ) {
		return false;
	}
	return true;
}

function sbsw_instagram_feed_is_minimum_version() {
	if ( ! defined( 'SBIVER' ) ) {
		return false;
	}
	if ( version_compare( SBIVER, SBSW_MIN_IF_VERSION  ) < 0 ) {
		return false;
	}
	return true;
}

function sbsw_social_wall_is_minimum_version_for_facebook_feed() {
	if ( ! defined( 'CFF_MINIMUM_WALL_VERSION' ) ) {
		return false;
	}
	if ( version_compare( SWVER, CFF_MINIMUM_WALL_VERSION  ) < 0 ) {
		return false;
	}
	return true;
}

function sbsw_facebook_feed_is_minimum_version() {
	if ( ! defined( 'CFFVER' ) ) {
		return false;
	}
	if ( version_compare( CFFVER, SBSW_MIN_FB_VERSION  ) < 0 ) {
		return false;
	}
	return true;
}

function sbsw_social_wall_is_minimum_version_for_twitter_feed() {
	if ( ! defined( 'CTF_MINIMUM_WALL_VERSION' ) ) {
		return false;
	}
	if ( version_compare( SWVER, CTF_MINIMUM_WALL_VERSION  ) < 0 ) {
		return false;
	}
	return true;
}

function sbsw_twitter_feed_is_minimum_version() {
	if ( ! defined( 'CTF_VERSION' ) ) {
		return false;
	}
	if ( version_compare( CTF_VERSION, SBSW_MIN_TW_VERSION  ) < 0 ) {
		return false;
	}
	return true;
}

function sbsw_social_wall_is_minimum_version_for_youtube_feed() {
	if ( ! defined( 'SBY_MINIMUM_WALL_VERSION' ) ) {
		return false;
	}
	if ( version_compare( SWVER, SBY_MINIMUM_WALL_VERSION  ) < 0 ) {
		return false;
	}
	return true;
}

function sbsw_youtube_feed_is_minimum_version() {
	if ( ! defined( 'SBYVER' ) ) {
		return false;
	}
	if ( version_compare( SBYVER, SBSW_MIN_YT_VERSION  ) < 0 ) {
		return false;
	}
	return true;
}

function sbsw_filter_plugin_with_atts_for_compatibility( $plugins_with_atts ) {

	$not_available = sbsw_get_not_available_in_shortcode( $plugins_with_atts );
	$return = array();
	foreach ( $plugins_with_atts as $plugin => $value ) {
		if ( ! in_array( $plugin, $not_available, true ) ) {
			$return[ $plugin ] = $value;
		}
	}

	return $return;
}

function sbsw_merge_atts( $plugins_with_atts, $atts ) {
    $return = $plugins_with_atts;

	foreach ( $plugins_with_atts as $plugin => $plugin_atts ) {
		if ( isset( $atts['includewords'] ) ) {
			$return[ $plugin ]['includewords'] = $atts['includewords'];
			$return[ $plugin ]['filter']       = $atts['includewords'];
		} elseif ( ! isset( $return[ $plugin ]['includewords'] ) ) {
			$return[ $plugin ]['includewords'] = '';
        } elseif ( ! isset( $return[ $plugin ]['filter'] ) ) {
			$return[ $plugin ]['filter'] = '';
		}

		if ( isset( $atts['excludewords'] ) ) {
			$return[ $plugin ]['excludewords'] = $atts['excludewords'];
			$return[ $plugin ]['filterex'] = $atts['excludewords'];
		} elseif ( ! isset( $return[ $plugin ]['excludewords'] ) ) {
			$return[ $plugin ]['excludewords'] = '';
		} elseif ( ! isset( $return[ $plugin ]['filterex'] ) ) {
			$return[ $plugin ]['filterex'] = '';
		}
		if ( isset( $atts['hideposts'] ) ) {
			$return[ $plugin ]['hideposts'] = $atts['hideposts'];
		}
    }

	return $return;
}

function sbsw_get_not_available_in_shortcode( $plugins_with_atts ) {
	$not_available = array();

	if ( isset( $plugins_with_atts['instagram-feed'] ) ) {
		if ( ! sbsw_instagram_feed_is_minimum_version()
			|| ! sbsw_social_wall_is_minimum_version_for_instagram_feed() ) {
			$not_available[] = 'instagram-feed';
		}
	}

	if ( isset( $plugins_with_atts['custom-facebook-feed'] ) ) {
		if ( ! sbsw_facebook_feed_is_minimum_version()
		     || ! sbsw_social_wall_is_minimum_version_for_facebook_feed() ) {
			$not_available[] = 'custom-facebook-feed';
		}
	}

	if ( isset( $plugins_with_atts['youtube-feed'] ) ) {
		if ( ! sbsw_youtube_feed_is_minimum_version()
		     || ! sbsw_social_wall_is_minimum_version_for_youtube_feed() ) {
			$not_available[] = 'youtube-feed';
		}
	}

	if ( isset( $plugins_with_atts['custom-twitter-feed'] ) || isset( $plugins_with_atts['custom-twitter-feeds'] ) ) {
		if ( ! sbsw_twitter_feed_is_minimum_version()
		     || ! sbsw_social_wall_is_minimum_version_for_twitter_feed() ) {
			$not_available[] = 'custom-twitter-feed';
			$not_available[] = 'custom-twitter-feeds';
		}
	}

	return $not_available;
}

function sbsw_get_not_available_warnings( $not_available ) {

	$style = ! current_user_can( 'manage_options' ) ? ' style="display:none;"' : '';
	ob_start(); ?>
	<div id="sbsw-mod-error" <?php echo $style; ?>>
		<span><?php _e('This error message is only visible to WordPress admins', 'social-wall' ); ?></span><br />
		<p><b><?php _e( 'The following plugins need to be updated or activated to be included in your social wall feed:', 'instagram-feed' ); ?></b>
		<p id="sbsw-mod-error-list">
		<?php if ( in_array( 'instagram-feed', $not_available, true ) ) : ?>
			<span>Instagram Feed Pro</span>
		<?php endif; ?>
		<?php if ( in_array( 'custom-facebook-feed', $not_available, true ) ) : ?>
			<span>Custom Facebook Feed Pro</span>
		<?php endif; ?>
		<?php if ( in_array( 'custom-twitter-feed', $not_available, true ) ) : ?>
			<span>Custom Twitter Feeds Pro</span>
		<?php endif; ?>
		<?php if ( in_array( 'youtube-feed', $not_available, true ) ) : ?>
			<span>Feeds for YouTube Pro</span>
		<?php endif; ?>
		<?php if ( current_user_can( 'manage_options' ) ) : ?>
			<span><?php echo sprintf( __( 'Address this on the %splugins%s page'), '<a href="'.admin_url( 'plugins.php').'" target="blank" rel="noopener noreferrer">', '</a>' ); ?></span>
		<?php endif; ?>
		</p>
	</div>
	<?php
	$html = ob_get_contents();
	ob_get_clean();
	return $html;
}

function sbsw_maybe_get_feed_notice( $sw_wall ) {
    $posts = $sw_wall->get_post_data();
    if ( ! empty( $posts ) ) {
        return;
    }
	$style = ! current_user_can( 'manage_options' ) ? ' style="display:none;"' : '';
	ob_start(); ?>
    <div id="sbsw-mod-error" <?php echo $style; ?>>
        <span><?php _e('This error message is only visible to WordPress admins', 'social-wall' ); ?></span><br />
        <p><b><?php _e( 'No posts are available for this feed.', 'social-wall' ); ?></b>
        <p id="sbsw-mod-error-list">
		    <?php _e( 'Please make sure that your feed is properly configured for the social network feeds you are trying to include.', 'social-wall' ); ?>
        </p>
    </div>
	<?php
	$html = ob_get_contents();
	ob_get_clean();
	return $html;
}

function sbsw_debug_report( $social_wall_feed, $social_wall_settings ) {

	if ( ! isset( $_GET['sbsw_debug'] ) ) {
		return '';
	}
	ob_start();
	?>
    <p>Status</p>
    <ul>
        <li>Time: <?php echo esc_html( date( "Y-m-d H:i:s", time() ) ); ?></li>
		<?php foreach ( $social_wall_feed->get_report() as $item ) : ?>
            <li><?php echo esc_html( $item ); ?></li>
		<?php endforeach; ?>

    </ul>

	<?php
	$plugin_type_and_terms = array();

	?>
    <p>Type and Terms</p>
    <?php foreach ( $plugin_type_and_terms as $plugin =>  $plugin_type_and_term) : ?>
    <strong><?php echo esc_html( $plugin ); ?></strong>
    <ul>
		<?php foreach ( $plugin_type_and_term as $type => $array_of_terms ) : ?>
            <li><?php echo esc_html( $type ); ?>
            <ul>
		<?php foreach ( $array_of_terms as $terms_and_params ) : ?>
            <?php foreach ( $terms_and_params as $key => $value ) : ?>

                <?php
                echo '<li>' . esc_html( $key ); ?>: <br>
                <?php if ( is_array( $value ) ) {
                    var_export( $value );
                } else {
                    echo esc_html( $value );
                }
                endforeach;
		    endforeach; ?>
            </ul>
            </li>

	    <?php
		endforeach;
    echo '</ul>';
	endforeach;
	$html = ob_get_contents();
	ob_get_clean();
	return $html;
}
add_action( 'sbsw_before_feed_end', 'sbsw_debug_report', 11, 2 );

function sbsw_get_resize_data_for_post_set( $post_ids, $feed_id = '' ) {
	$return = array();
	if ( isset( $post_ids['facebook'] ) ) {
		$return['facebook'] = CustomFacebookFeed\CFF_Resizer::get_resized_image_data_for_set( $post_ids['facebook'], $feed_id );
	}
	if ( isset( $post_ids['instagram'] ) ) {
		$return['instagram'] = SB_Instagram_Feed::get_resized_images_source_set( $post_ids['instagram'], 0, $feed_id );
	}
    if ( isset( $post_ids['twitter'] ) && class_exists( 'CTF_Resizer' ) ) {
		$return['twitter'] = CTF_Resizer::get_resized_image_data_for_set( $post_ids['twitter'], 0 );
	}
	return $return;
}

/**
 * Triggered by a cron event to update feeds
 */
function sbsw_cron_updater() {
    $cron_updater = new SW_Cron_Updater();

    $cron_updater->do_feed_updates();
}
add_action( 'sbsw_feed_update', 'sbsw_cron_updater' );

function sbsw_json_encode( $thing ) {
	if ( function_exists( 'wp_json_encode' ) ) {
		return wp_json_encode( $thing );
	} else {
		return json_encode( $thing );
	}
}

function sbsw_settings_defaults() {
	$defaults = array(
		'num' => 9,
		'nummobile' => 9,
		'minnum' => 9,
		'cols' => 3,
		'colsmobile' => 'auto',
		'masonrycols' => 3,
		'masonrycolsmobile' => 1,
        'masonryshowfilter' => false,
		'widthresp' => true,
        'showfilter' => true,
		'class' => '',
		'height' => '',
		'heightunit' => '%',
		'disablemobile' => false,
		'itemspacing' => 9,
		'itemspacingunit' => 'px',
		'background' => '',
		'layout' => 'masonry',
		'theme' => 'light',
		'carouselcols' => 3,
		'carouselcolsmobile' => 2,
		'carouselarrows' => true,
		'carouselpag' => true,
		'carouselautoplay' => false,
		'sortby' => 'none',
		'dateformat' => 'relative',
		'minutetext' => 'm',
		'hourtext' => 'h',
		'daytext' => 'd',
		'weektext' => 'w',
		'monthtext' => 'mo',
		'yeartext' => 'y',
		'imageres' => 'auto',
		'showbutton' => true,
		'buttontext' => __( 'Load More', 'social-wall' ),
		'textlength' => 300,
		'contenttextsize' => 'inherit',
		'cache_cron_interval' => '1hour',
		'cache_time_unit' => 'hours',
		'backup_cache_enabled' => true,
		'ajax_post_load' => false,
		'ajaxtheme' => false,
		'enqueue_css_in_shortcode' => false,
		'customtemplates' => false,
		'doingModerationMode' => false,
		'addModerationModeLink' => false,
		'disable_js_image_loading' => false,
	);

	return $defaults;
}

function sbsw_get_active_plugins() {
	$active = array();

	if ( class_exists( 'CustomFacebookFeed\CFF_Parse_Pro' ) ) {
		$active[] = 'facebook';
	}

	if ( class_exists( 'CTF_Parse_Pro' ) ) {
		$active[] = 'twitter';
	}

	if ( class_exists( 'SB_Instagram_Parse_Pro' ) ) {
		$active[] = 'instagram';
	}

	if ( class_exists( 'SBY_Parse_Pro' ) ) {
		$active[] = 'youtube';
	}

	return $active;
}

function sbsw_get_database_settings() {
	$settings = get_option( 'sbsw_settings', array() );

	$defaults = sbsw_settings_defaults();

	return array_merge( $defaults, $settings );
}

function sbsw_clear_cache() {
	//Delete all transients
	global $wpdb;
	$table_name = $wpdb->prefix . "options";
	$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_sbsw\_%')
        " );
	$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_timeout\_sbsw\_%')
        " );
	$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_&sbsw\_%')
        " );
	$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_timeout\_&sbsw\_%')
        " );
	$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_\$sbsw\_%')
        " );
	$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_timeout\_\$sbsw\_%')
        " );

	sbsw_clear_page_caches();
}
add_action( 'sbsw_settings_after_customize_save', 'sbsw_clear_cache' );

function sbsw_clear_page_caches() {
	if ( isset( $GLOBALS['wp_fastest_cache'] ) && method_exists( $GLOBALS['wp_fastest_cache'], 'deleteCache' ) ){
		/* Clear WP fastest cache*/
		$GLOBALS['wp_fastest_cache']->deleteCache();
	}

	if ( function_exists( 'wp_cache_clear_cache' ) ) {
		wp_cache_clear_cache();
	}

	if ( class_exists('W3_Plugin_TotalCacheAdmin') ) {
		$plugin_totalcacheadmin = & w3_instance('W3_Plugin_TotalCacheAdmin');

		$plugin_totalcacheadmin->flush_all();
	}

	if ( function_exists( 'rocket_clean_domain' ) ) {
		rocket_clean_domain();
	}

	if ( class_exists( 'autoptimizeCache' ) ) {
		/* Clear autoptimize */
		autoptimizeCache::clearall();
	}
}

/**
 * @param $a
 * @param $b
 *
 * @return false|int
 */
function sbsw_date_sort( $a, $b ) {
	$time_stamp_a = SW_Parse::get_timestamp( $a );
	$time_stamp_b = SW_Parse::get_timestamp( $b );

	if ( isset( $time_stamp_a ) ) {
		return $time_stamp_b - $time_stamp_a;
	} else {
		return rand ( -1, 1 );
	}
}

/**
 * Converts a hex code to RGB so opacity can be
 * applied more easily
 *
 * @param $hex
 *
 * @return string
 */
function sbsw_hextorgb( $hex ) {
	// allows someone to use rgb in shortcode
	if ( strpos( $hex, ',' ) !== false ) {
		return $hex;
	}

	$hex = str_replace( '#', '', $hex );

	if ( strlen( $hex ) === 3 ) {
		$r = hexdec( substr( $hex,0,1 ).substr( $hex,0,1 ) );
		$g = hexdec( substr( $hex,1,1 ).substr( $hex,1,1 ) );
		$b = hexdec( substr( $hex,2,1 ).substr( $hex,2,1 ) );
	} else {
		$r = hexdec( substr( $hex,0,2 ) );
		$g = hexdec( substr( $hex,2,2 ) );
		$b = hexdec( substr( $hex,4,2 ) );
	}
	$rgb = array( $r, $g, $b );

	return implode( ',', $rgb ); // returns the rgb values separated by commas
}

function sbsw_get_utc_offset() {
	return get_option( 'gmt_offset', 0 ) * HOUR_IN_SECONDS;
}

/**
 * @param $a
 * @param $b
 *
 * @return false|int
 */
function sbsw_rand_sort( $a, $b ) {
	return rand ( -1, 1 );
}

function sbsw_esc_html_with_br( $text ) {
	return str_replace( array( '&lt;br /&gt;', '&lt;br&gt;' ), '<br>', esc_html( nl2br( $text ) ) );
}

function sbsw_replace_double_quotes( $text ) {
	return str_replace( array( '"', '&quot;' ), '&rdquo;', $text);
}

function sbsw_esc_attr_with_br( $text ) {
	return str_replace( array( '&lt;br /&gt;', '&lt;br&gt;' ), '&lt;br /&gt;', esc_attr( nl2br( $text ) ) );
}

function sbsw_get_date_format() {
	$date_format = get_option( 'date_format' );
	if ( ! $date_format ) {
		$date_format = 'F j, Y';
	}
	$time_format = get_option( 'time_format' );
	if ( ! $time_format ) {
		$date_format .= ' g:i a';
	} else {
		$date_format .= ' ' . $time_format;
	}

	return $date_format;
}

function sbsw_maybe_shorten_text( $string, $max_characters, $with_show_more = false ) {

	if ( strlen( $string ) <= $max_characters ) {
		return $string;
	}
	$parts = preg_split( '/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE );
	$parts_count = count( $parts );

	if ( ! $with_show_more ) {
		$length = 0;
		$last_part = 0;
		for ( ; $last_part < $parts_count; ++$last_part ) {
			$length += strlen( $parts[ $last_part ] );
			if ( $length > $max_characters ) { break; }
		}

		$last_part = $last_part !== 0 ? $last_part - 1 : 0;
		$parts = array_slice( $parts, 0, $last_part );

		$return = implode( ' ', $parts ) . '...';

	} else {
		$length = 0;
		$last_part = 0;
		$first_parts = array();
		$end_parts = array();
		for ( ; $last_part < $parts_count; $last_part++ ) {
			$length += strlen( $parts[ $last_part ] );
			if ( $length < $max_characters ) {
				$first_parts[] = $parts[ $last_part ];
			} else {
				$end_parts[] = $parts[ $last_part ];
			}
		}
		$return = implode( ' ', $first_parts ) . '<a href="#" class="sbsw-more">...</a><span class="sbsw-remaining">';

		$return .= implode( ' ', $end_parts ).'</span>';
	}


	return $return;
}

function sbsw_shorten_and_see_more_format_text( $string, $max_characters ) {

	if ( strlen( $string ) <= $max_characters ) {
		return $string;
	}

	$parts = preg_split( '/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE );
	$parts_count = count( $parts );

	$length = 0;
	$last_part = 0;
	for ( ; $last_part < $parts_count; ++$last_part ) {
		$length += strlen( $parts[ $last_part ] );
		if ( $length > $max_characters ) { break; }
	}

	$last_part = $last_part !== 0 ? $last_part - 1 : 0;
	$parts = array_slice( $parts, 0, $last_part );

	$return = implode( ' ', $parts ) . '...';

	return $return;
}

function sbsw_maybe_shorten_link_text( $url ) {
	$url = explode("/", preg_replace("(^https?://)", "", $url));

	if ( isset( $url[0] ) ) {
		return $url[0];
	}

	return $url;
}

function sbsw_format_count( $count ) {
	$count = (int)$count;

	if ( $count < 1000 ) {
		$text = number_format_i18n( $count, 0 );
	} elseif ( $count < 1000000 ) {
		$thousands = $count/1000;
		$num_text = round( $thousands, 1 );

		/* translators: 'K' is short for English "Thousand" i.e. 10K = 10,000 */
		$thousands_text = apply_filters( 'sbsw_thousands_text', 'K' );
		$text = $num_text . $thousands_text;
	} else {
		$millions = $count/1000000;
		$num_text = round( $millions, 1 );

		/* translators: 'M' is short for English "Million" i.e. 10M = 10,000,000 */
		$millions_text = apply_filters( 'sbsw_millions_text', 'M' );
		$text = $num_text . $millions_text;
	}

	return '<span class="sbsw-count">' . esc_html( $text ) . '</span>';
}

function sbsw_format_duration( $time_in_seconds ) {
	if ( $time_in_seconds < 60 ) {
		$leading_zero = $time_in_seconds < 10 ? 0 : '';
		return '0:' .$leading_zero . $time_in_seconds;
	} elseif ( $time_in_seconds < 3600 ) {

		$minutes = floor( $time_in_seconds / 60 );

		$seconds = $time_in_seconds - ($minutes * 60);

		$leading_zero = $seconds < 10 ? 0 : '';

		return $minutes . ':' .$leading_zero . $seconds;

	} else {
		return gmdate( 'H:i:s', $time_in_seconds );
	}
}

function sbsw_get_feed_template_part( $part, $settings = array() ) {
	$file = '';

	/**
	 * Whether or not to search for custom templates in theme folder
	 *
	 * @param boolean  Setting from DB or shortcode to use custom templates
	 *
	 * @since 1.0
	 */
	$using_custom_templates_in_theme = apply_filters( 'sbsw_use_theme_templates', $settings['customtemplates'] );
	$generic_path = trailingslashit( SBSW_PLUGIN_DIR ) . 'templates/';

	if ( $using_custom_templates_in_theme ) {
        $custom_header_template = locate_template( 'sbsw/header.php', false, false );
		$custom_item_template = locate_template( 'sbsw/item.php', false, false );
		$custom_footer_template = locate_template( 'sbsw/footer.php', false, false );
		$custom_feed_template = locate_template( 'sbsw/feed.php', false, false );
	} else {
	    $custom_header_template = false;
		$custom_item_template = false;
		$custom_footer_template = false;
		$custom_feed_template = false;
	}

	if ( $part === 'item' ) {
		if ( $custom_item_template ) {
			$file = $custom_item_template;
		} else {
			$file = $generic_path . 'item.php';
		}
	} elseif ( $part === 'header' ) {
		if ( $custom_header_template ) {
			$file = $custom_header_template;
		} else {
			$file = $generic_path . 'header.php';
		}
	} elseif ( $part === 'footer' ) {
		if ( $custom_footer_template ) {
			$file = $custom_footer_template;
		} else {
			$file = $generic_path . 'footer.php';
		}
	} elseif ( $part === 'feed' ) {
		if ( $custom_feed_template ) {
			$file = $custom_feed_template;
		} else {
			$file = $generic_path . 'feed.php';
		}
	}

	return $file;
}

add_action( 'wp_head', 'sbsw_custom_css' );
function sbsw_custom_css() {
	$sbsw_settings = sbsw_get_database_settings();

	$css = isset( $sbsw_settings['custom_css'] ) ? trim( $sbsw_settings['custom_css'] ) : '';

	//Show CSS if an admin (so can see Hide Photos link), if including Custom CSS or if hiding some photos
	if ( ! empty( $css ) ) {

		echo '<!-- Social Wall CSS -->';
		echo "\r\n";
		echo '<style type="text/css">';

		if ( ! empty( $css ) ){
			echo "\r\n";
			echo stripslashes($css);
		}

		echo "\r\n";
		echo '</style>';
		echo "\r\n";
    }

}

function sbsw_custom_js() {
	$sbsw_settings = sbsw_get_database_settings();

	$js = isset( $sbsw_settings['custom_js'] ) ? trim( $sbsw_settings['custom_js'] ) : '';

	echo '<!-- Social Wall JS -->';
	echo "\r\n";
	echo '<script type="text/javascript">';
	echo "\r\n";

	if ( ! empty( $js ) ) {
		echo "\r\n";
		echo "jQuery( document ).ready(function($) {";
		echo "\r\n";
		echo "window.sbswCustomJS = function(){";
		echo "\r\n";
		echo stripslashes($js);
		echo "\r\n";
		echo "}";
		echo "\r\n";
		echo "});";
	}

	echo "\r\n";
	echo '</script>';
	echo "\r\n";
}
add_action( 'wp_footer', 'sbsw_custom_js' );

function sbsw_scripts_enqueue( $enqueue = false ) {
	//Register the script to make it available

	//Options to pass to JS file
	$sbsw_settings = sbsw_get_database_settings();

	$js_file = 'js/social-wall.min.js';
	if ( isset( $_GET['sw_debug'] ) ) {
		$js_file = 'js/social-wall.js';
	}

	if ( isset( $sbsw_settings['enqueue_js_in_head'] ) && $sbsw_settings['enqueue_js_in_head'] ) {
		wp_enqueue_script( 'sbsw_scripts', trailingslashit( SBSW_PLUGIN_URL ) . $js_file, array('jquery'), SWVER, false );
	} else {
		wp_register_script( 'sbsw_scripts', trailingslashit( SBSW_PLUGIN_URL ) . $js_file, array('jquery'), SWVER, true );
	}

	if ( isset( $sbsw_settings['enqueue_css_in_shortcode'] ) && $sbsw_settings['enqueue_css_in_shortcode'] ) {
		wp_register_style( 'sbsw_styles', trailingslashit( SBSW_PLUGIN_URL ) . 'css/social-wall.min.css', array(), SWVER );
	} else {
		wp_enqueue_style( 'sbsw_styles', trailingslashit( SBSW_PLUGIN_URL ) . 'css/social-wall.min.css', array(), SWVER );
	}

	$data = array(
		'adminAjaxUrl' => admin_url( 'admin-ajax.php' ),
		'lightboxPlaceholder' => trailingslashit( SBSW_PLUGIN_URL ) . 'img/lightbox-placeholder.png',
		'placeholder' => trailingslashit( SBSW_PLUGIN_URL ) . 'img/placeholder.png',
	);

	if ( method_exists( 'CustomFacebookFeed\CFF_Utils','cff_get_resized_uploads_url' ) ) {
		$data['cffResizeUrl'] = CustomFacebookFeed\CFF_Utils::cff_get_resized_uploads_url();
	}
	if ( function_exists( 'sbi_get_resized_uploads_url' ) ) {
		$data['sbiResizeUrl'] = sbi_get_resized_uploads_url();
	}
    if ( function_exists( 'ctf_get_resized_uploads_url' ) ) {
		$data['ctfResizeUrl'] = ctf_get_resized_uploads_url();
	}
	//Pass option to JS file
	wp_localize_script('sbsw_scripts', 'sbswOptions', $data );

	if ( $enqueue ) {
		wp_enqueue_style( 'sbsw_styles' );
		wp_enqueue_script( 'sbsw_scripts' );
	}
}
add_action( 'wp_enqueue_scripts', 'sbsw_scripts_enqueue', 3 );