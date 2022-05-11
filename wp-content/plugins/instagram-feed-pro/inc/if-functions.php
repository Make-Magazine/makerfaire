<?php
/**
 * Contains functions used primarily on the frontend but some also used in the
 * admin area.
 *
 * - Function for the shortcode that displays the feed
 * - AJAX call for pagination
 * - All AJAX calls for image resizing triggering
 * - Clearing page caches for caching plugins
 * - Starting cron caching
 * - Getting settings from the database
 * - Displaying frontend errors
 * - Enqueueing CSS and JS files for the feed
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * The main function the creates the feed from a shortcode.
 * Can be safely added directly to templates using
 * 'echo do_shortcode( "[instagram-feed]" );'
 */
add_shortcode( 'instagram-feed', 'display_instagram' );
function display_instagram( $atts = array() ) {
    do_action( 'sbi_before_display_instagram' );

	$database_settings = sbi_get_database_settings();

	if ( $database_settings['sb_instagram_ajax_theme'] !== 'on' && $database_settings['sb_instagram_ajax_theme'] !== 'true' ) {
		wp_enqueue_script( 'sbi_scripts' );
	}

	if ( $database_settings['enqueue_css_in_shortcode'] === 'on' || $database_settings['enqueue_css_in_shortcode'] === 'true' ) {
		wp_enqueue_style( 'sbi_styles' );
    }

	$moderation_mode = (isset ( $_GET['sbi_moderation_mode'] ) && $_GET['sbi_moderation_mode'] === 'true' && current_user_can( 'edit_posts' ));
	if ( $moderation_mode ) {
	    if ( is_array( $atts ) ) {
		    $atts['doingModerationMode'] = true;
	    } else {
	        $atts = array(
                'doingModerationMode' => true
            );
        }
    }

	$instagram_feed_settings = new SB_Instagram_Settings_Pro( $atts, $database_settings );

	if ( empty( $database_settings['connected_accounts'] ) && empty( $atts['accesstoken'] ) ) {
		$style = current_user_can( 'manage_instagram_feed_options' ) ? ' style="display: block;"' : '';
		ob_start(); ?>
        <div id="sbi_mod_error" <?php echo $style; ?>>
            <span><?php _e('This error message is only visible to WordPress admins', 'instagram-feed' ); ?></span><br />
            <p><b><?php _e( 'Error: No connected account.', 'instagram-feed' ); ?></b>
            <p><?php _e( 'Please go to the Instagram Feed settings page to connect an account.', 'instagram-feed' ); ?></p>
        </div>
		<?php
		$html = ob_get_contents();
		ob_get_clean();
		return $html;
	}

	$instagram_feed_settings->set_feed_type_and_terms();
	$instagram_feed_settings->set_transient_name();
	$transient_name = $instagram_feed_settings->get_transient_name();
	$settings = $instagram_feed_settings->get_settings();

	if ( ! $moderation_mode && ($settings['mediavine'] === 'on' || $settings['mediavine'] === 'true' || $settings['mediavine'] === true) ) {
		wp_enqueue_script('sb_instagram_mediavine_scripts');
	}

	$feed_type_and_terms = $instagram_feed_settings->get_feed_type_and_terms();
	$instagram_feed = new SB_Instagram_Feed_Pro( $transient_name );

	if ( SB_Instagram_Feed_Locator::should_do_locating() ) {
		$instagram_feed->add_report( 'doing feed locating' );

		$feed_details = array(
			'feed_id' => $transient_name,
			'atts' => $atts,
			'location' => array(
				'post_id' => get_the_ID(),
				'html' => 'unknown'
			)
		);
		$locator = new SB_Instagram_Feed_Locator( $feed_details );
		$locator->add_or_update_entry();
	}

	if ( $settings['caching_type'] === 'permanent' && empty( $settings['doingModerationMode'] ) ) {
		$instagram_feed->add_report( 'trying to use permanent cache' );
		$post_cache_success = $instagram_feed->maybe_set_post_data_from_backup();

		if ( ! $post_cache_success ) {
			$num_needed = $settings['num'];
			if ( ! empty( $settings['whitelist'] ) ) {
				$num_needed = $settings['whitelist_num'];
			}
			$raised_num_settings = $settings;
			$raised_num_settings['num'] = 100;

			if ( $instagram_feed->need_posts( $num_needed ) && $instagram_feed->can_get_more_posts() ) {
				while ( $instagram_feed->need_posts( $num_needed ) && $instagram_feed->can_get_more_posts() ) {
					$instagram_feed->add_remote_posts( $raised_num_settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
				}
				$instagram_feed->cache_feed_data( $instagram_feed_settings->get_cache_time_in_seconds(), true );
			}
        }
	} elseif ( $settings['caching_type'] === 'background' ) {

		$instagram_feed->add_report( 'background caching used' );
		if ( $instagram_feed->regular_cache_exists() ) {
			$instagram_feed->add_report( 'setting posts from cache' );
			$instagram_feed->set_post_data_from_cache();
		}

		if ( $instagram_feed->need_to_start_cron_job() ) {
			$instagram_feed->add_report( 'setting up feed for cron cache' );
			$to_cache = array(
                'atts' => $atts,
                'last_requested' => time(),
		    );

			$instagram_feed = SB_Instagram_Cron_Updater_Pro::do_single_feed_cron_update( $instagram_feed_settings, $to_cache, $atts, false );

			$instagram_feed->set_post_data_from_cache();

		} elseif ( $instagram_feed->should_update_last_requested() ) {
			$instagram_feed->add_report( 'updating last requested' );
			$to_cache = array(
				'last_requested' => time(),
			);

			$instagram_feed->set_cron_cache( $to_cache, $instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );
		}

    } elseif ( $instagram_feed->regular_cache_exists() ) {
		$instagram_feed->add_report( 'page load caching used and regular cache exists' );
		$instagram_feed->set_post_data_from_cache();

        if ( $instagram_feed->need_posts( $settings['num'] ) && $instagram_feed->can_get_more_posts() ) {
	        while ( $instagram_feed->need_posts( $settings['num'] ) && $instagram_feed->can_get_more_posts() ) {
				$instagram_feed->add_remote_posts( $settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
			}

			$instagram_feed->cache_feed_data( $instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );
		}

	} else {
		$instagram_feed->add_report( 'no feed cache found' );

        while ( $instagram_feed->need_posts( $settings['num'] ) && $instagram_feed->can_get_more_posts() ) {
            $instagram_feed->add_remote_posts( $settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
        }

        if ( $instagram_feed->out_of_next_pages() || $instagram_feed->should_look_for_db_only_posts( $settings, $feed_type_and_terms ) ) {
	        $instagram_feed->add_report( 'Adding Db only posts' );
	        $instagram_feed->add_db_only_posts( $transient_name, $settings, $feed_type_and_terms );
        }

		if ( ! $instagram_feed->should_use_backup() ) {

			$instagram_feed->cache_feed_data( $instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );
		} elseif ( $instagram_feed->should_cache_error() ) {

			$cache_time = min( $instagram_feed_settings->get_cache_time_in_seconds(), 15 * 60 );
			$instagram_feed->add_report( 'caching an error' );
			$instagram_feed->cache_feed_data( $cache_time, false );
		}

	}

	if ( $instagram_feed->should_use_backup() ) {
		$instagram_feed->add_report( 'trying to use backup' );
		$instagram_feed->maybe_set_post_data_from_backup();
		if ( ! empty( $settings['hidephotos'] ) ) {
			$instagram_feed->add_report( 'filtering backup' );

			$post_data = $instagram_feed->get_post_data();
			$was_one_post = count( $post_data ) > 0;
			$post_data = $instagram_feed->process_hide_photos( $post_data, $settings );
			$is_one_post_after_filter = count( $post_data ) > 0;

			$instagram_feed->set_post_data( $post_data );
			if ( $was_one_post && ! $is_one_post_after_filter ) {
				global $sb_instagram_posts_manager;
				$error_message_return = array(
					'error_message' => __( 'Error: No posts found.', 'instagram-feed' ),
					'admin_only' => __( 'No posts found that weren\'t hidden by your moderation settings.', 'instagram-feed' ),
					'frontend_directions' => '',
					'backend_directions' => ''
				);
				$sb_instagram_posts_manager->maybe_set_display_error( 'configuration', $error_message_return );
				$cache_time = min( $instagram_feed_settings->get_cache_time_in_seconds(), 15 * 60 );
				$instagram_feed->add_report( 'caching an error' );
				$instagram_feed->cache_feed_data( $cache_time, false, true );
            }

        }


		$instagram_feed->maybe_set_header_data_from_backup();
		$header_data = $instagram_feed->get_header_data();
	}

	// if need a header
	if ( $instagram_feed->need_header( $settings, $feed_type_and_terms ) ) {
	    if ( empty( $header_data )  ) {
		    if ( ($instagram_feed->should_use_backup() || $settings['caching_type'] === 'permanent') && empty( $settings['doingModerationMode'] ) ) {
			    $instagram_feed->add_report( 'trying to set header from backup' );
			    $header_cache_success = $instagram_feed->maybe_set_header_data_from_backup();
			    if ( ! $header_cache_success ) {
				    $instagram_feed->set_remote_header_data( $settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
				    $header_data = $instagram_feed->get_header_data();
				    if ( $settings['stories'] && ! empty( $header_data ) ) {
					    $instagram_feed->set_remote_stories_data( $settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
				    }
				    $instagram_feed->cache_header_data( $instagram_feed_settings->get_cache_time_in_seconds(), true );
			    } else {
				    $instagram_feed->cache_header_data( $instagram_feed_settings->get_cache_time_in_seconds(), false );
			    }
		    } elseif ( $settings['caching_type'] === 'background' ) {
			    $instagram_feed->add_report( 'background header caching used' );
			    $instagram_feed->set_header_data_from_cache();
		    } elseif ( $instagram_feed->regular_header_cache_exists() ) {
			    $instagram_feed->add_report( 'page load caching used and regular header cache exists' );
			    $instagram_feed->set_header_data_from_cache();
		    } else {
			    $instagram_feed->add_report( 'no header cache exists' );
			    $instagram_feed->set_remote_header_data( $settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
			    $header_data = $instagram_feed->get_header_data();
			    if ( $settings['stories'] && ! empty( $header_data ) ) {
				    $instagram_feed->set_remote_stories_data( $settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
			    }
			    $instagram_feed->cache_header_data( $instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );
		    }
        }

	} else {
		$showheader = ($settings['showheader'] === 'on' || $settings['showheader'] === 'true' || $settings['showheader'] === true);

		if ( $showheader ) {
		    $settings['generic_header'] = true;
		    $instagram_feed->set_generic_header_data( $feed_type_and_terms );
		    $instagram_feed->add_report( 'using generic header' );
	    } else {
		    $instagram_feed->add_report( 'no header needed' );
	    }
	}

	$settings['feed_avatars'] = array();
	if ( $instagram_feed->need_avatars( $settings ) ) {
		$instagram_feed->set_up_feed_avatars( $instagram_feed_settings->get_connected_accounts_in_feed(), $feed_type_and_terms );
		$settings['feed_avatars'] = $instagram_feed->get_username_avatars();
	}

	if ( $settings['resizeprocess'] === 'page' ) {
		$instagram_feed->add_report( 'resizing images for post set' );
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

	if ( $settings['disable_js_image_loading'] || $settings['imageres'] !== 'auto' ) {
		global $sb_instagram_posts_manager;
		$post_data = $instagram_feed->get_post_data();

		if ( ! $sb_instagram_posts_manager->image_resizing_disabled( $feed_type_and_terms ) ) {
			$image_ids = array();
            foreach ( $post_data as $post ) {
	            $image_ids[] = SB_Instagram_Parse::get_post_id( $post );
            }
	        $resized_images = SB_Instagram_Feed::get_resized_images_source_set( $image_ids, 0, $transient_name );

            $instagram_feed->set_resized_images( $resized_images );
        }
    }

	$instagram_feed->maybe_offset_posts( $settings['offset'] );

	return $instagram_feed->get_the_feed_html( $settings, $atts, $instagram_feed_settings->get_feed_type_and_terms(), $instagram_feed_settings->get_connected_accounts_in_feed() );
}

add_filter( 'widget_text', 'do_shortcode' );

/**
 * For efficiency, local versions of image files available for the images actually displayed on the page
 * are added at the end of the feed.
 *
 * @param object $instagram_feed
 * @param string $feed_id
 */
function sbi_add_resized_image_data( $instagram_feed, $feed_id ) {
	global $sb_instagram_posts_manager;

	if ( ! $sb_instagram_posts_manager->image_resizing_disabled( $feed_id ) ) {
	    if ( $instagram_feed->should_update_last_requested() ) {
		    SB_Instagram_Feed::update_last_requested( $instagram_feed->get_image_ids_post_set() );
	    }
	}

	?>
    <span class="sbi_resized_image_data" data-feed-id="<?php echo esc_attr( $feed_id ); ?>" data-resized="<?php echo esc_attr( sbi_json_encode( SB_Instagram_Feed::get_resized_images_source_set( $instagram_feed->get_image_ids_post_set(), 0, $feed_id ) ) ); ?>">
	</span>
	<?php
}
add_action( 'sbi_before_feed_end', 'sbi_add_resized_image_data', 10, 2 );

/**
 * Called after the load more button is clicked using admin-ajax.php
 */
function sbi_get_next_post_set() {
	if ( ! isset( $_POST['feed_id'] ) || strpos( $_POST['feed_id'], 'sbi' ) === false ) {
		die( 'invalid feed ID');
	}

	$feed_id = sanitize_text_field( $_POST['feed_id'] );
	$atts_raw = isset( $_POST['atts'] ) ? json_decode( stripslashes( $_POST['atts'] ), true ) : array();
	if ( is_array( $atts_raw ) ) {
		array_map( 'sanitize_text_field', $atts_raw );
	} else {
		$atts_raw = array();
	}
	$atts = $atts_raw; // now sanitized

	$database_settings = sbi_get_database_settings();
	$instagram_feed_settings = new SB_Instagram_Settings_Pro( $atts, $database_settings );

	if ( empty( $database_settings['connected_accounts'] ) && empty( $atts['accesstoken'] ) ) {
		die( 'error no connected account' );
	}

	$instagram_feed_settings->set_feed_type_and_terms();
	$instagram_feed_settings->set_transient_name();
	$transient_name = $instagram_feed_settings->get_transient_name();

	if ( $transient_name !== $feed_id ) {
		die( 'id does not match' );
	}

	$location = isset( $_POST['location'] ) && in_array( $_POST['location'], array( 'header', 'footer', 'sidebar', 'content' ), true ) ? sanitize_text_field( $_POST['location'] ) : 'unknown';
	$post_id = isset( $_POST['post_id'] ) && $_POST['post_id'] !== 'unknown' ? (int)$_POST['post_id'] : 'unknown';
	$feed_details = array(
		'feed_id' => $transient_name,
		'atts' => $atts,
		'location' => array(
			'post_id' => $post_id,
			'html' => $location
		)
	);

	sbi_do_background_tasks( $feed_details );

	$settings = $instagram_feed_settings->get_settings();
	$offset = isset( $_POST['offset'] ) ? (int)$_POST['offset'] : 0;
	$page = isset( $_POST['page'] ) ? (int)$_POST['page'] : 1;
	//$offset = $settings['minnum'] * $page;

	$feed_type_and_terms = $instagram_feed_settings->get_feed_type_and_terms();

	$instagram_feed = new SB_Instagram_Feed_Pro( $transient_name );

	if ( $settings['caching_type'] === 'permanent' && empty( $settings['doingModerationMode'] ) ) {
		$instagram_feed->add_report( 'trying to use permanent cache' );
		$instagram_feed->maybe_set_post_data_from_backup();
	} elseif ( $settings['caching_type'] === 'background' ) {
		$instagram_feed->add_report( 'background caching used' );
		if ( $instagram_feed->regular_cache_exists() ) {
			$instagram_feed->add_report( 'setting posts from cache' );
			$instagram_feed->set_post_data_from_cache();
		}

        if ( $instagram_feed->need_posts( $settings['minnum'] + $settings['offset'], $offset, $page ) && $instagram_feed->can_get_more_posts() ) {
            while ( $instagram_feed->need_posts( $settings['minnum'] + $settings['offset'], $offset, $page ) && $instagram_feed->can_get_more_posts() ) {
                $instagram_feed->add_remote_posts( $settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
            }

            $normal_method = true;
	        if ( $instagram_feed->need_to_start_cron_job() ) {
		        $instagram_feed->add_report( 'needed to start cron job' );
		        $to_cache = array(
			        'atts' => $atts,
			        'last_requested' => time(),
		        );


		        $normal_method = false;

	        } else {
		        $instagram_feed->add_report( 'updating last requested and adding to cache' );
		        $to_cache = array(
			        'last_requested' => time(),
		        );
	        }

	        if ( $instagram_feed->out_of_next_pages() && $instagram_feed->should_look_for_db_only_posts( $settings, $feed_type_and_terms ) ) {
		        $instagram_feed->add_report( 'Adding Db only posts' );
		        $instagram_feed->add_db_only_posts( $transient_name, $settings, $feed_type_and_terms );
	        }

	        if ( $normal_method ) {
		        $instagram_feed->set_cron_cache( $to_cache, $instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );
	        } else {
		        $instagram_feed->set_cron_cache( $to_cache, $instagram_feed_settings->get_cache_time_in_seconds() );
	        }
        }

	} elseif ( $instagram_feed->regular_cache_exists() ) {
		$instagram_feed->add_report( 'regular cache exists' );
		$instagram_feed->set_post_data_from_cache();

		if ( $instagram_feed->need_posts( (int)$settings['minnum'] + (int)$settings['offset'], $offset, $page ) && $instagram_feed->can_get_more_posts() ) {
			while ( $instagram_feed->need_posts( $settings['minnum'] + $settings['offset'], $offset, $page ) && $instagram_feed->can_get_more_posts() ) {
				$instagram_feed->add_remote_posts( $settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
			}

			if ( $instagram_feed->out_of_next_pages() || $instagram_feed->should_look_for_db_only_posts( $settings, $feed_type_and_terms ) ) {
				$instagram_feed->add_report( 'Adding Db only posts' );
				$instagram_feed->add_db_only_posts( $transient_name, $settings, $feed_type_and_terms );
			}

			$instagram_feed->add_report( 'adding to cache' );
			$instagram_feed->cache_feed_data( $instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );
		}


	} else {
		$instagram_feed->add_report( 'no feed cache found' );

        while ( $instagram_feed->need_posts( $settings['minnum'] + $settings['offset'], $offset, $page ) && $instagram_feed->can_get_more_posts() ) {
            $instagram_feed->add_remote_posts( $settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
        }

        if ( $instagram_feed->should_use_backup() ) {
            $instagram_feed->add_report( 'trying to use a backup cache' );
            $instagram_feed->maybe_set_post_data_from_backup();
        } else {
            $instagram_feed->add_report( 'transient gone, adding to cache' );
            $instagram_feed->cache_feed_data( $instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );
        }
	}

	$settings['feed_avatars'] = array();
	if ( $instagram_feed->need_avatars( $settings ) ) {
		$instagram_feed->set_up_feed_avatars( $instagram_feed_settings->get_connected_accounts_in_feed(), $feed_type_and_terms );
		$settings['feed_avatars'] = $instagram_feed->get_username_avatars();
	}

	$should_paginate_offset = (int)$offset + (int)$settings['offset'];
	$feed_status = array( 'shouldPaginate' => $instagram_feed->should_use_pagination( $settings, $should_paginate_offset ) );

	if ( $settings['disable_js_image_loading'] || $settings['imageres'] !== 'auto' ) {
		global $sb_instagram_posts_manager;
		$post_data = array_slice( $instagram_feed->get_post_data(), $offset, $settings['minnum'] );

		if ( ! $sb_instagram_posts_manager->image_resizing_disabled( $feed_type_and_terms ) ) {
			$image_ids = array();
			foreach ( $post_data as $post ) {
				$image_ids[] = SB_Instagram_Parse::get_post_id( $post );
			}
			$resized_images = SB_Instagram_Feed::get_resized_images_source_set( $image_ids, 0, $feed_id );

			$instagram_feed->set_resized_images( $resized_images );
		}
	}

	$instagram_feed->maybe_offset_posts( $settings['offset'] );

	$return = array(
		'html' => $instagram_feed->get_the_items_html( $settings, $offset, $instagram_feed_settings->get_feed_type_and_terms(), $instagram_feed_settings->get_connected_accounts_in_feed() ),
		'feedStatus' => $feed_status,
		'report' => $instagram_feed->get_report(),
        'resizedImages' => SB_Instagram_Feed::get_resized_images_source_set( $instagram_feed->get_image_ids_post_set(), 1, $feed_id )
	);

	SB_Instagram_Feed::update_last_requested( $instagram_feed->get_image_ids_post_set() );

	echo sbi_json_encode( $return );

	die();
}
add_action( 'wp_ajax_sbi_load_more_clicked', 'sbi_get_next_post_set' );
add_action( 'wp_ajax_nopriv_sbi_load_more_clicked', 'sbi_get_next_post_set' );

/**
 * Posts that need resized images are processed after being sent to the server
 * using AJAX
 *
 * @return string
 */
function sbi_process_submitted_resize_ids() {
	if ( ! isset( $_POST['feed_id'] ) || strpos( $_POST['feed_id'], 'sbi' ) === false ) {
		die( 'invalid feed ID');
	}

	$feed_id = sanitize_text_field( $_POST['feed_id'] );
	$images_need_resizing_raw = isset( $_POST['needs_resizing'] ) ? $_POST['needs_resizing'] : array();
	if ( is_array( $images_need_resizing_raw ) ) {
		array_map( 'sanitize_text_field', $images_need_resizing_raw );
	} else {
		$images_need_resizing_raw = array();
	}
	$images_need_resizing = $images_need_resizing_raw;

	$atts_raw = isset( $_POST['atts'] ) ? json_decode( stripslashes( $_POST['atts'] ), true ) : array();
	if ( is_array( $atts_raw ) ) {
		array_map( 'sanitize_text_field', $atts_raw );
	} else {
		$atts_raw = array();
	}
	$atts = $atts_raw; // now sanitized

	$offset = isset( $_POST['offset'] ) ? (int)$_POST['offset'] : 0;
	$cache_all = isset( $_POST['cache_all'] ) ? $_POST['cache_all'] === 'true' : false;

	$database_settings = sbi_get_database_settings();
	$instagram_feed_settings = new SB_Instagram_Settings_Pro( $atts, $database_settings );

	if ( empty( $database_settings['connected_accounts'] ) && empty( $atts['accesstoken'] ) ) {
		return '<div class="sb_instagram_error"><p>' . __( 'Please connect an account on the Instagram Feed plugin Settings page.', 'instagram-feed' ) . '</p></div>';
	}

	$instagram_feed_settings->set_feed_type_and_terms();
	$instagram_feed_settings->set_transient_name();
	$transient_name = $instagram_feed_settings->get_transient_name();
	$settings = $instagram_feed_settings->get_settings();

	$location = isset( $_POST['location'] ) && in_array( $_POST['location'], array( 'header', 'footer', 'sidebar', 'content' ), true ) ? sanitize_text_field( $_POST['location'] ) : 'unknown';
	$post_id = isset( $_POST['post_id'] ) && $_POST['post_id'] !== 'unknown' ? (int)$_POST['post_id'] : 'unknown';
	$feed_details = array(
		'feed_id' => $transient_name,
		'atts' => $atts,
		'location' => array(
			'post_id' => $post_id,
			'html' => $location
		)
	);

	sbi_do_background_tasks( $feed_details );

	if ( $cache_all ) {
	    $settings['cache_all'] = true;
    }

	if ( $transient_name !== $feed_id ) {
		die( 'id does not match' );
	}

	sbi_resize_posts_by_id( $images_need_resizing, $transient_name, $settings );
	sbi_delete_image_cache( $transient_name );

	global $sb_instagram_posts_manager;

	if ( ! $sb_instagram_posts_manager->image_resizing_disabled( $transient_name ) ) {
	    $num = $settings['minnum'] * 2 + 5;
		echo sbi_json_encode( SB_Instagram_Feed::get_resized_images_source_set( $num, $offset - $settings['minnum'], $feed_id, false ) );
		die();
	}

	die( 'resizing success' );
}
add_action( 'wp_ajax_sbi_resized_images_submit', 'sbi_process_submitted_resize_ids' );
add_action( 'wp_ajax_nopriv_sbi_resized_images_submit', 'sbi_process_submitted_resize_ids' );

function sbi_do_locator() {
	if ( ! isset( $_POST['feed_id'] ) || strpos( $_POST['feed_id'], 'sbi' ) === false ) {
		die( 'invalid feed ID');
	}

	$feed_id = sanitize_text_field( $_POST['feed_id'] );


	$atts_raw = isset( $_POST['atts'] ) ? json_decode( stripslashes( $_POST['atts'] ), true ) : array();
	if ( is_array( $atts_raw ) ) {
		array_map( 'sanitize_text_field', $atts_raw );
	} else {
		$atts_raw = array();
	}
	$atts = $atts_raw; // now sanitized

	$location = isset( $_POST['location'] ) && in_array( $_POST['location'], array( 'header', 'footer', 'sidebar', 'content' ), true ) ? sanitize_text_field( $_POST['location'] ) : 'unknown';
	$post_id = isset( $_POST['post_id'] ) && $_POST['post_id'] !== 'unknown' ? (int)$_POST['post_id'] : 'unknown';
	$feed_details = array(
		'feed_id' => $feed_id,
		'atts' => $atts,
		'location' => array(
			'post_id' => $post_id,
			'html' => $location
		)
	);

	sbi_do_background_tasks( $feed_details );

	wp_die( 'locating success' );
}
add_action( 'wp_ajax_sbi_do_locator', 'sbi_do_locator' );
add_action( 'wp_ajax_nopriv_sbi_do_locator', 'sbi_do_locator' );

function sbi_do_background_tasks( $feed_details ) {
	$locator = new SB_Instagram_Feed_Locator( $feed_details );
	$locator->add_or_update_entry();
	if ( $locator->should_clear_old_locations() ) {
		$locator->delete_old_locations();
	}
}

/**
 * Outputs an organized error report for the front end.
 * This hooks into the end of the feed before the closing div
 *
 * @param object $instagram_feed
 * @param string $feed_id
 */
function sbi_error_report( $instagram_feed, $feed_id ) {
    global $sb_instagram_posts_manager;

    $style = sbi_current_user_can( 'manage_instagram_feed_options' ) ? ' style="display: block;"' : '';

	$error_messages = $sb_instagram_posts_manager->get_frontend_errors( $instagram_feed );

    if ( ! empty( $error_messages ) ) {?>
        <div id="sbi_mod_error"<?php echo $style; ?>>
            <span><?php _e('This error message is only visible to WordPress admins', 'instagram-feed' ); ?></span><br />
        <?php foreach ( $error_messages as $error_message ) {

            echo '<div><strong>' . esc_html( $error_message['error_message'] )  . '</strong>';
            if ( sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
	            echo '<br>' . $error_message['admin_only'];
	            echo '<br>' . $error_message['frontend_directions'];
            }
            echo '</div>';
        } ?>
        </div>
        <?php
    }

	$sb_instagram_posts_manager->reset_frontend_errors();
}
add_action( 'sbi_before_feed_end', 'sbi_error_report', 10, 2 );

function sbi_delete_image_cache( $transient_name ) {
	$images_transient_name = str_replace( 'sbi_', 'sbi_i_', $transient_name );
	delete_transient( $images_transient_name );
}

function sbi_current_user_can( $cap ) {
    if ( $cap === 'manage_instagram_feed_options' ) {
	    $cap = current_user_can( 'manage_instagram_feed_options' ) ? 'manage_instagram_feed_options' : 'manage_options';
    }
	$cap = apply_filters( 'sbi_settings_pages_capability', $cap );

    return current_user_can( $cap );
}

function sbi_doing_openssl() {
    return extension_loaded( 'openssl' );
}
/**
 * Debug report added at the end of the feed when sbi_debug query arg is added to a page
 * that has the feed on it.
 *
 * @param object $instagram_feed
 * @param string $feed_id
 */
function sbi_debug_report( $instagram_feed, $feed_id ) {

    if ( ! isset( $_GET['sbi_debug'] ) ) {
        return;
    }
	global $sb_instagram_posts_manager;

    ?>
    <p>Status</p>
    <ul>
        <li>Time: <?php echo esc_html( date( "Y-m-d H:i:s", time() ) ); ?></li>
    <?php foreach ( $instagram_feed->get_report() as $item ) : ?>
        <li><?php echo esc_html( $item ); ?></li>
    <?php endforeach; ?>

	</ul>

    <?php
	$database_settings = sbi_get_database_settings();

	$public_settings_keys = SB_Instagram_Settings_Pro::get_public_db_settings_keys();
    ?>
    <p>Settings</p>
    <ul>
        <?php foreach ( $public_settings_keys as $key ) : if ( isset( $database_settings[ $key ] ) ) : ?>
        <li>
            <small><?php echo esc_html( $key ); ?>:</small>
        <?php if ( ! is_array( $database_settings[ $key ] ) ) :
                echo esc_html( $database_settings[ $key ] );
        else : ?>
<pre>
<?php var_export( $database_settings[ $key ] ); ?>
</pre>
        <?php endif; ?>
        </li>

        <?php endif; endforeach; ?>
    </ul>
    <p>GDPR</p>
    <ul>
		<?php
		$statuses = SB_Instagram_GDPR_Integrations::statuses();
		foreach ( $statuses as $status_key => $value) : ?>
            <li>
                <small><?php echo esc_html( $status_key ); ?>:</small>
				<?php if ( $value == 1 ) { echo 'success'; } else {  echo 'failed'; } ?>
            </li>

		<?php endforeach; ?>
        <li>
            <small>Enabled:</small>
		    <?php echo SB_Instagram_GDPR_Integrations::doing_gdpr( $database_settings ); ?>
        </li>
    </ul>
    <?php
}
add_action( 'sbi_before_feed_end', 'sbi_debug_report', 11, 2 );

/**
 * Uses post IDs to process images that may need resizing
 *
 * @param array $ids
 * @param string $transient_name
 * @param array $settings
 * @param int $offset
 */
function sbi_resize_posts_by_id( $ids, $transient_name, $settings, $offset = 0 ) {
	$instagram_feed = new SB_Instagram_Feed( $transient_name );

	if ( $instagram_feed->regular_cache_exists() ) {
		// set_post_data_from_cache
		$instagram_feed->set_post_data_from_cache();

		$cached_post_data = $instagram_feed->get_post_data();

		if ( ! isset( $settings['cache_all'] ) || ! $settings['cache_all'] ) {
			$num_ids = count( $ids );
			$found_posts = array();
			$i = 0;
			while ( count( $found_posts) < $num_ids && isset( $cached_post_data[ $i ] ) ) {
				if ( ! empty( $cached_post_data[ $i ]['id'] ) && in_array( $cached_post_data[ $i ]['id'], $ids, true ) ) {
					$found_posts[] = $cached_post_data[ $i ];
				}
				$i++;
			}
        } else {
			$found_posts = array_slice( $cached_post_data, 0, 50 );
        }


		$fill_in_timestamp = date( 'Y-m-d H:i:s', time() + 120 );

		if ( $offset !== 0 ) {
			$fill_in_timestamp = date( 'Y-m-d H:i:s', strtotime( $instagram_feed->get_earliest_time_stamp( $transient_name ) ) - 120 );
		}

        $image_sizes = array(
            'personal' => array( 'full' => 640, 'low' => 320, 'thumb' => 150 ),
            'business' => array( 'full' => 640, 'low' => 320, 'thumb' => 150 )
        );

		$post_set = new SB_Instagram_Post_Set( $found_posts, $transient_name, $fill_in_timestamp, $image_sizes );

		$post_set->maybe_save_update_and_resize_images_for_posts();
	}
}

function sbi_store_local_avatar( $connected_account ) {
	$sbi_settings = get_option( 'sb_instagram_settings', array() );
	$connected_accounts = $sbi_settings['connected_accounts'];
	if ( sbi_create_local_avatar( $connected_account['username'], $connected_account['profile_picture'] ) ) {
        $connected_accounts[ $connected_account['user_id'] ]['local_avatar'] = true;
    } else {
		$connected_accounts[ $connected_account['user_id'] ]['local_avatar'] = false;
	}


	$sbi_settings['connected_accounts'] = $connected_accounts;

	update_option( 'sb_instagram_settings', $sbi_settings );

	return $connected_accounts[ $connected_account['user_id'] ]['local_avatar'];
}

function sbi_create_local_avatar( $username, $file_name ) {
	$image_editor = wp_get_image_editor( $file_name );

	if ( ! is_wp_error( $image_editor ) ) {
		$upload = wp_upload_dir();

		$full_file_name = trailingslashit( $upload['basedir'] ) . trailingslashit( SBI_UPLOADS_NAME ) . $username  . '.jpg';

		$saved_image = $image_editor->save( $full_file_name );

		if ( ! $saved_image ) {
			global $sb_instagram_posts_manager;

			$sb_instagram_posts_manager->add_error( 'image_editor', __( 'Error saving edited image.', 'instagram-feed' ) . ' ' . $full_file_name );
		} else {
			return true;
		}
	} else {
		global $sb_instagram_posts_manager;

		$message = __( 'Error editing image.', 'instagram-feed' );
		if ( isset( $image_editor ) && isset( $image_editor->errors ) ) {
			foreach ( $image_editor->errors as $key => $item ) {
				$message .= ' ' . $key . '- ' . $item[0] . ' |';
			}
		}
		$sb_instagram_posts_manager->add_error( 'image_editor', $message . ' ' . $file_name );
	}
	return false;
}

/**
 * Get the settings in the database with defaults
 *
 * @return array
 */
function sbi_get_database_settings() {
	$defaults = array(
		'sb_instagram_at'                   => '',
		'sb_instagram_user_id'              => '',
		'sb_instagram_preserve_settings'    => '',
		'sb_instagram_ajax_theme'           => false,
		'sb_instagram_disable_resize'       => false,
		'sb_instagram_cache_time'           => 1,
		'sb_instagram_cache_time_unit'      => 'hours',
		'sbi_caching_type'                  => 'page',
		'sbi_cache_cron_interval'           => '12hours',
		'sbi_cache_cron_time'               => '1',
		'sbi_cache_cron_am_pm'              => 'am',
		'sb_instagram_width'                => '100',
		'sb_instagram_width_unit'           => '%',
		'sb_instagram_feed_width_resp'      => false,
		'sb_instagram_height'               => '',
		'sb_instagram_num'                  => '20',
		'sb_instagram_height_unit'          => '',
		'sb_instagram_cols'                 => '4',
		'sb_instagram_disable_mobile'       => false,
		'sb_instagram_image_padding'        => '5',
		'sb_instagram_image_padding_unit'   => 'px',
		'sb_instagram_sort'                 => 'none',
		'sb_instagram_background'           => '',
		'sb_instagram_show_btn'             => true,
		'sb_instagram_btn_background'       => '',
		'sb_instagram_btn_text_color'       => '',
		'sb_instagram_btn_text'             => __( 'Load More...', 'instagram-feed' ),
		'sb_instagram_image_res'            => 'auto',
		'sb_instagram_lightbox_comments'    => true,
		'sb_instagram_num_comments'         => 20,
        'sb_instagram_show_bio' => true,
		'sb_instagram_show_followers' => true,
		//Header
		'sb_instagram_show_header'          => true,
		'sb_instagram_header_size'  => 'small',
		'sb_instagram_header_color'         => '',
		'sb_instagram_stories' => true,
		'sb_instagram_stories_time' => 5000,
		//Follow button
		'sb_instagram_show_follow_btn'      => true,
		'sb_instagram_folow_btn_background' => '',
		'sb_instagram_follow_btn_text_color' => '',
		'sb_instagram_follow_btn_text'      => __( 'Follow on Instagram', 'instagram-feed' ),
		//Misc
		'sb_instagram_custom_css'           => '',
		'sb_instagram_custom_js'            => '',
		'sb_instagram_cron'                 => 'no',
		'sb_instagram_backup' => true,
		'sb_ajax_initial'    => false,
		'enqueue_css_in_shortcode' => false,
		'sb_instagram_disable_mob_swipe' => false,
		'sb_instagram_disable_awesome'      => false,
        'sb_instagram_disable_font'      => false,
		'gdpr'      => false,
		'altresize'      => true

	);
	$sbi_settings = get_option( 'sb_instagram_settings', array() );

	$return_settings = array_merge( $defaults, $sbi_settings );

	$return_settings = apply_filters( 'sbi_database_settings', $return_settings );

	return $return_settings;
}

/**
 * May include support for templates in theme folders in the future
 *
 * @return string full path to template
 *
 * @since 5.2 custom templates supported
 */
function sbi_get_feed_template_part( $part, $settings = array() ) {
	$file = '';

	/**
	 * Whether or not to search for custom templates in theme folder
	 *
	 * @param boolean  Setting from DB or shortcode to use custom templates
	 *
	 * @since 5.2
	 */
	$using_custom_templates_in_theme = apply_filters( 'sbi_use_theme_templates', $settings['customtemplates'] );
	$generic_path = trailingslashit( SBI_PLUGIN_DIR ) . 'templates/';

	if ( $using_custom_templates_in_theme ) {
		$custom_header_template = locate_template( 'sbi/header.php', false, false );
		$custom_header_boxed_template = locate_template( 'sbi/header-boxed.php', false, false );
		$custom_header_generic_template = locate_template( 'sbi/header-generic.php', false, false );
		$custom_item_template = locate_template( 'sbi/item.php', false, false );
		$custom_footer_template = locate_template( 'sbi/footer.php', false, false );
		$custom_feed_template = locate_template( 'sbi/feed.php', false, false );
	} else {
		$custom_header_template = false;
		$custom_header_boxed_template = false;
		$custom_header_generic_template = false;
		$custom_item_template = false;
		$custom_footer_template = false;
		$custom_feed_template = false;
    }

	if ( $part === 'header' ) {
	    if ( isset( $settings['generic_header'] ) ) {
	        if ( $custom_header_generic_template ) {
	            $file = $custom_header_generic_template;
            } else {
		        $file = $generic_path . 'header-generic.php';
	        }
	    } else {
		    if ( $settings['headerstyle'] !== 'boxed' ) {
			    if ( $custom_header_template ) {
				    $file = $custom_header_template;
			    } else {
				    $file = $generic_path . 'header.php';
			    }
		    } else {
			    if ( $custom_header_boxed_template ) {
				    $file = $custom_header_boxed_template;
			    } else {
				    $file = $generic_path . 'header-boxed.php';
			    }
		    }
        }
	} elseif ( $part === 'item' ) {
		if ( $custom_item_template ) {
			$file = $custom_item_template;
		} else {
			$file = $generic_path . 'item.php';
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

/**
 * Triggered by a cron event to update feeds
 */
function sbi_cron_updater() {
    $sbi_settings = sbi_get_database_settings();

    if ( $sbi_settings['sbi_caching_type'] === 'background' ) {
        $cron_updater = new SB_Instagram_Cron_Updater_Pro();

        $cron_updater->do_feed_updates();
    }

	sbi_do_background_tasks( array() );
}
add_action( 'sbi_feed_update', 'sbi_cron_updater' );

/**
 * @param string $maybe_dirty
 * @param string $helper
 *
 * @return string
 */
function sbi_maybe_clean( $maybe_dirty ) {
    $encryption = new SB_Instagram_Data_Encryption();

    $decrypted = $encryption->decrypt( $maybe_dirty );
    if ( $decrypted ) {
	    $maybe_dirty = $decrypted;
    }
	if ( substr_count ( $maybe_dirty , '.' ) < 3 ) {
		return str_replace( '634hgdf83hjdj2', '', $maybe_dirty );
	}

	$parts = explode( '.', trim( $maybe_dirty ) );
	$last_part = $parts[2] . $parts[3];
	$cleaned = $parts[0] . '.' . base64_decode( $parts[1] ) . '.' . base64_decode( $last_part );

	return $cleaned;
}

/**
 * @param $whole
 *
 * @return string
 */
function sbi_get_parts( $whole ) {
	if ( substr_count ( $whole , '.' ) !== 2 ) {
		return $whole;
	}

	$parts = explode( '.', trim( $whole ) );
	$return = $parts[0] . '.' . base64_encode( $parts[1] ). '.' . base64_encode( $parts[2] );

	return substr( $return, 0, 40 ) . '.' . substr( $return, 40, 100 );
}

/**
 * Deletes any cache or setting that may contain Instagram platform data
 */
function sbi_delete_all_platform_data() {
	global $sb_instagram_posts_manager;
	$manager = new SB_Instagram_Data_Manager();
	$sb_instagram_posts_manager->add_action_log( 'Deleted all platform data.' );
	$sb_instagram_posts_manager->reset_api_errors();
	$manager->delete_caches();
	$manager->delete_comments_data();
	$manager->delete_hashtag_data();
	SB_Instagram_Connected_Account::update_connected_accounts( array() );
}


function sbi_get_current_time() {
	$current_time = time();

	// where to do tests
	//$current_time = strtotime( 'November 25, 2020' ) + 1;

	return $current_time;
}

/**
 * Used to shorten screen reader and alt text but still
 * have the text end on a full word.
 *
 * @param $text
 * @param $max_characters
 *
 * @return string
 */
function sbi_shorten_paragraph( $text, $max_characters ) {

	if ( strlen( $text ) <= $max_characters ) {
		return $text;
	}

	$parts = preg_split( '/([\s\n\r]+)/', $text, null, PREG_SPLIT_DELIM_CAPTURE );
	$parts_count = count( $parts );

	$length = 0;
	$last_part = 0;
	for ( ; $last_part < $parts_count; ++$last_part ) {
		$length += strlen( $parts[ $last_part ] );
		if ( $length > $max_characters ) { break; }
	}

	$i = 0;
	$last_part = $last_part !== 0 ? $last_part - 1 : 0;
	$final_parts = array();
	if ( $last_part > 0 ) {
		while ( $i <= $last_part && isset( $parts[ $i ] ) ) {
			$final_parts[] = $parts[ $i ];
		    $i++;
        }

    } else {
	    return $text;
    }

	$final_parts[ $last_part ] = $final_parts[ $last_part ] . '...';

	$return = implode( ' ', $final_parts );

	return $return;
}

if ( ! function_exists( 'shorten_paragraph' ) ) {
    function shorten_paragraph( $text, $max_characters ) {
        return sbi_shorten_paragraph( $text, $max_characters );
    }
}

function sbi_code_check( $code ) {
    if ( strpos( $code, '634hgdf83hjdj2') !== false ) {
        return true;
    }
    return false;
}

function sbi_fixer( $code ) {
	if ( strpos( $code, '634hgdf83hjdj2') !== false ) {
	    return $code;
	} else {
		return substr_replace( $code , '634hgdf83hjdj2', 15, 0 );
    }
}

/**
 * @param $a
 * @param $b
 *
 * @return false|int
 */
function sbi_date_sort( $a, $b ) {
	$time_stamp_a = SB_Instagram_Parse::get_timestamp( $a );
	$time_stamp_b = SB_Instagram_Parse::get_timestamp( $b );

	if ( isset( $time_stamp_a ) ) {
		return $time_stamp_b - $time_stamp_a;
	} else {
		return rand ( -1, 1 );
	}
}

function sbi_likes_sort( $a, $b ) {
	$likes_a = SB_Instagram_Parse_Pro::get_likes_count( $a );
	$likes_b = SB_Instagram_Parse_Pro::get_likes_count( $b );

	if ( isset( $likes_a ) ) {
		return (int)$likes_b - (int)$likes_a;
	} else {
		return rand ( -1, 1 );
	}
}

/**
 * @param $a
 * @param $b
 *
 * @return false|int
 */
function sbi_rand_sort( $a, $b ) {
    return rand ( -1, 1 );
}

/**
 * Converts a hex code to RGB so opacity can be
 * applied more easily
 *
 * @param $hex
 *
 * @return string
 */
function sbi_hextorgb( $hex ) {
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

/**
 * Used to encode comments before returning in AJAX call
 *
 * @param $uri
 *
 * @return string
 */
function sbi_encode_uri( $uri )
{
	$unescaped = array(
		'%2D'=>'-','%5F'=>'_','%2E'=>'.','%21'=>'!', '%7E'=>'~',
		'%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')'
	);
	$reserved = array(
		'%3B'=>';','%2C'=>',','%2F'=>'/','%3F'=>'?','%3A'=>':',
		'%40'=>'@','%26'=>'&','%3D'=>'=','%2B'=>'+','%24'=>'$'
	);
	$score = array(
		'%23'=>'#'
	);

	return strtr( rawurlencode( $uri ), array_merge( $reserved,$unescaped,$score ) );
}

/**
 * Added to workaround MySQL tables that don't use utf8mb4 character sets
 *
 * @since 2.2.1/5.3.1
 */
function sbi_sanitize_emoji( $string ) {
    $encoded = array(
        'jsonencoded' => $string
    );
    return sbi_json_encode( $encoded );
}

/**
 * Added to workaround MySQL tables that don't use utf8mb4 character sets
 *
 * @since 2.2.1/5.3.1
 */
function sbi_decode_emoji( $string ) {
	if ( strpos( $string, '{"' ) !== false ) {
	    $decoded = json_decode( $string, true );
		return $decoded['jsonencoded'];
	}
	return $string;
}

/**
 * Used for caching posts in the background
 *
 * @return int
 */
function sbi_get_utc_offset() {
	return get_option( 'gmt_offset', 0 ) * HOUR_IN_SECONDS;
}

/**
 * Used for manipulating the current timestamp during tests
 *
 * @return int
 */
function sbi_get_current_timestamp() {
	$current_time = time();

	//$current_time = strtotime( 'November 25, 2022' ) + 1;

	return $current_time;
}

/**
 * Various warnings and workarounds are triggered
 * or changed by whether or not this function returns
 * true
 *
 * @return bool
 */
function sbi_is_after_deprecation_deadline() {
	$current_time = sbi_get_current_timestamp();

	return $current_time > strtotime( 'June 29, 2020' );
}

function sbi_json_encode( $thing ) {
	if ( function_exists( 'wp_json_encode' ) ) {
		return wp_json_encode( $thing );
	} else {
		return json_encode( $thing );
	}
}

function sbi_get_default_type_and_terms() {
	$if_atts = array();
	$if_database_settings = sbi_get_database_settings();

	$instagram_feed_settings = new SB_Instagram_Settings_Pro( $if_atts, $if_database_settings );

	$instagram_feed_settings->set_feed_type_and_terms();
	$if_feed_type_and_terms = $instagram_feed_settings->get_feed_type_and_terms();
	$connected_accounts = $instagram_feed_settings->get_connected_accounts_in_feed();

	$return = array(
		'type' => '',
		'term_label' => '',
		'terms' => ''
	);
	$terms_array = array();
	foreach ( $if_feed_type_and_terms as $key => $values ) {
		if ( empty( $type ) ) {
			if ( $key === 'users' ) {
				$return['type'] = 'user';
				$return['term_label'] = 'user(s)';
				foreach ( $values as $value ) {
					if ( isset( $connected_accounts[ $value['term'] ] ) ) {
						$terms_array[] = $connected_accounts[ $value['term'] ]['username'];
					} else {
						$terms_array[] = $value['term'];
					}
				}
			}
		}

	}

	$return['terms'] = implode( ', ', $terms_array );

	return $return;
}

function sbi_get_account_and_feed_info() {

    $return = array();
	$if_atts = array();
	$if_database_settings = sbi_get_database_settings();

	$instagram_feed_settings = new SB_Instagram_Settings_Pro( $if_atts, $if_database_settings );

	$instagram_feed_settings->set_feed_type_and_terms();
	$if_feed_type_and_terms = $instagram_feed_settings->get_feed_type_and_terms();
	$connected_accounts = $instagram_feed_settings->get_connected_accounts_in_feed();

	$type_and_terms = array(
		'type' => '',
		'term_label' => '',
		'terms' => ''
	);
	$terms_array = array();
	foreach ( $if_feed_type_and_terms as $key => $values ) {
		if ( empty( $type ) ) {
			if ( $key === 'users' ) {
				$type_and_terms['type'] = 'user';
				$type_and_terms['term_label'] = 'user(s)';
				foreach ( $values as $value ) {
					if ( isset( $connected_accounts[ $value['term'] ] ) ) {
						$terms_array[] = $connected_accounts[ $value['term'] ]['username'];
					} else {
						$terms_array[] = $value['term'];
					}
				}
			}
		}

	}

	$type_and_terms['terms'] =  $terms_array;

	$return['type_and_terms'] = $type_and_terms;
	$return['connected_accounts'] = $instagram_feed_settings->get_connected_accounts();
	$return['available_types'] = array(
        'user' => array(
            'label' => 'User',
            'shortcode' => 'user',
            'term_shortcode' => 'user',
            'key' => 'username',
            'input' => 'connected'
        ),
		'hashtag' => array(
			'label' => 'Hashtag',
			'shortcode' => 'hashtag',
			'term_shortcode' => 'hashtag',
			'key' => false,
			'input' => 'text',
            'instructions' => __( 'Any hashtags (comma separated)', 'instagram-feed' )
		),
		'tagged' => array(
			'label' => 'Tagged',
			'shortcode' => 'tagged',
			'term_shortcode' => 'tagged',
			'key' => 'username',
			'input' => 'connected'
		),
    );
	$return['settings'] = array(
        'type' => 'type'
    );


	return $return;
}

function sbi_private_account_near_expiration( $connected_account ) {
	$expires_in = max( 0, floor( ($connected_account['expires_timestamp'] - time()) / DAY_IN_SECONDS ) );
	return $expires_in < 10;
}

function sbi_update_connected_account( $account_id, $to_update ) {
	$if_database_settings = sbi_get_database_settings();

	$connected_accounts = $if_database_settings['connected_accounts'];

	if ( isset( $connected_accounts[ $account_id ] ) ) {

	    foreach ( $to_update as $key => $value ) {
		    $connected_accounts[ $account_id ][ $key ] = $value;
        }

		$if_database_settings['connected_accounts'] = $connected_accounts;

		update_option( 'sb_instagram_settings', $if_database_settings );
    }
}

/**
 * @return string
 *
 * @since 2.1.1
 */
function sbi_get_resized_uploads_url() {
	$upload = wp_upload_dir();

	$base_url = $upload['baseurl'];
	$home_url = home_url();

	if ( strpos( $home_url, 'https:' ) !== false ) {
		$base_url = str_replace( 'http:', 'https:', $base_url );
	}

	$resize_url = apply_filters( 'sbi_resize_url', trailingslashit( $base_url ) . trailingslashit( SBI_UPLOADS_NAME ) );

	return $resize_url;
}

/**
 * Used to clear caches when transients aren't working
 * properly
 */
function sb_instagram_cron_clear_cache() {
	//Delete all transients
	global $wpdb;
	$table_name = $wpdb->prefix . "options";
	$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_sbi\_%')
        " );
	$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_timeout\_sbi\_%')
        " );
	$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_&sbi\_%')
        " );
	$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_timeout\_&sbi\_%')
        " );
	$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_\$sbi\_%')
        " );
	$wpdb->query( "
        DELETE
        FROM $table_name
        WHERE `option_name` LIKE ('%\_transient\_timeout\_\$sbi\_%')
        " );

	sb_instagram_clear_page_caches();
}

/**
 * When certain events occur, page caches need to
 * clear or errors occur or changes will not be seen
 */
function sb_instagram_clear_page_caches() {
	$clear_page_caches = apply_filters( 'sbi_clear_page_caches', true );
	if ( ! $clear_page_caches ) {
		return;
	}

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

	// Litespeed Cache
	if ( method_exists( 'LiteSpeed_Cache_API', 'purge' ) ) {
		LiteSpeed_Cache_API::purge( 'esi.instagram-feed' );
	}
}

/**
 * Meant to be updated in an AJAX request from moderation mode
 * on the front end
 */
function sbi_update_mod_mode_settings() {
	if ( current_user_can( 'edit_posts' ) ) {
		$sb_instagram_settings = get_option( 'sb_instagram_settings' );
		$remove_ids = array();

		if ( ! empty( $_POST['ids'] ) ) {
			// append new id to remove id list if unique
			foreach ( $_POST['ids'] as $id ) {
				$remove_ids[] = sanitize_text_field( $id );
			}
        }

		// save the new setting as string
		$sb_instagram_settings['sb_instagram_hide_photos'] = implode( ', ', $remove_ids );

		update_option( 'sb_instagram_settings', $sb_instagram_settings );

		sb_instagram_cron_clear_cache();
	}
	die();
}
add_action('wp_ajax_sbi_update_mod_mode_settings', 'sbi_update_mod_mode_settings');

/**
 * Meant to be updated in an AJAX request from moderation mode
 * on the front end
 */
function sbi_update_mod_mode_white_list() {
	if ( current_user_can( 'edit_posts' ) ) {
		$white_index = isset( $_POST['db_index'] ) ? sanitize_text_field( $_POST['db_index'] ) : false;
		$permanent = isset( $_POST['permanent'] ) && $_POST['permanent'] == 'true' ? true : false;
		$current_white_names = get_option( 'sb_instagram_white_list_names', array() );

		if ( $white_index == '' ) {
			$new_index = count( $current_white_names ) + 1;

			while ( in_array( $new_index, $current_white_names ) ) {
				$new_index++;
			}
			$white_index = (string)$new_index;

			// user doesn't know the new name so echo it out here and add a message
			echo esc_html( $white_index );
		}

		$white_list_name = 'sb_instagram_white_lists_'.$white_index;
		$white_ids = array();

		// append new id to remove id list if unique
		if ( isset( $_POST['ids'] ) && is_array( $_POST['ids'] ) ) {

			foreach ( $_POST['ids'] as $id ) {
				$white_ids[] = sanitize_text_field( $id );
			}

			update_option( $white_list_name, $white_ids, false  );
		}

		// update white list names
		if ( ! in_array( $white_index, $current_white_names ) ) {
			$current_white_names[] = $white_index;
			update_option( 'sb_instagram_white_list_names', $current_white_names, false  );
		}

		$permanent_white_lists = get_option( 'sb_permanent_white_lists', array() );

		if ( $permanent ) {
			if ( ! in_array( $white_index, $permanent_white_lists, true ) ) {
				$permanent_white_lists[] = $white_index;
			}
			update_option( 'sb_permanent_white_lists', $permanent_white_lists, false  );
		} else {
			if ( in_array( $white_index, $permanent_white_lists, true ) ) {
				$update_wl = array();
				foreach ( $permanent_white_lists as $wl ) {
					if ( $wl !== $white_index ) {
						$update_wl[] = $wl;
					}
				}
				update_option( 'sb_permanent_white_lists', $update_wl, false  );
			}
		}

		sb_instagram_cron_clear_cache();

		set_transient( 'sb_wlupdated_'.$white_index, 'true', 3600 );
	}

	die();

}
add_action('wp_ajax_sbi_update_mod_mode_white_list', 'sbi_update_mod_mode_white_list');

/**
 * Makes the JavaScript file available and enqueues the stylesheet
 * for the plugin
 */
function sb_instagram_scripts_enqueue( $enqueue = false ) {
	//Register the script to make it available

	//Options to pass to JS file
	$sb_instagram_settings = sbi_get_database_settings();

	$js_file = 'js/sbi-scripts.min.js';
	if ( isset( $_GET['sbi_debug'] ) ) {
		$js_file = 'js/sbi-scripts.js';
	}

	if ( isset( $sb_instagram_settings['enqueue_js_in_head'] ) && $sb_instagram_settings['enqueue_js_in_head'] ) {
		wp_enqueue_script( 'sbi_scripts', trailingslashit( SBI_PLUGIN_URL ) . $js_file, array('jquery'), SBIVER, false );
	} else {
		wp_register_script( 'sbi_scripts', trailingslashit( SBI_PLUGIN_URL ) . $js_file, array('jquery'), SBIVER, true );
	}

	if ( isset( $sb_instagram_settings['enqueue_css_in_shortcode'] ) && $sb_instagram_settings['enqueue_css_in_shortcode'] ) {
		wp_register_style( 'sbi_styles', trailingslashit( SBI_PLUGIN_URL ) . 'css/sbi-styles.min.css', array(), SBIVER );
	} else {
		wp_enqueue_style( 'sbi_styles', trailingslashit( SBI_PLUGIN_URL ) . 'css/sbi-styles.min.css', array(), SBIVER );
	}

	$br_adjust = isset( $sb_instagram_settings['sbi_br_adjust'] ) && ($sb_instagram_settings['sbi_br_adjust'] == 'false' || $sb_instagram_settings['sbi_br_adjust'] == '0' || $sb_instagram_settings['sbi_br_adjust'] == false) ? false : true;

	$data = array(
		'font_method' => 'svg',
		'resized_url' => sbi_get_resized_uploads_url(),
		'placeholder' => trailingslashit( SBI_PLUGIN_URL ) . 'img/placeholder.png',
        'br_adjust' => $br_adjust,
    );
	if ( isset( $sb_instagram_settings['sb_instagram_disable_mob_swipe'] ) && $sb_instagram_settings['sb_instagram_disable_mob_swipe'] ) {
		$data['no_mob_swipe'] = true;
    }

	$translations = array(
        'share' => __( 'Share', 'instagram-feed' )
    );
	//Pass option to JS file
	wp_localize_script('sbi_scripts', 'sb_instagram_js_options', $data );
	wp_localize_script('sbi_scripts', 'sbiTranslations', $translations );
	if ( $enqueue ) {
		wp_enqueue_style( 'sbi_styles' );
		wp_enqueue_script( 'sbi_scripts', trailingslashit( SBI_PLUGIN_URL ) . $js_file, array('jquery'), SBIVER, true );
	}
}
add_action( 'wp_enqueue_scripts', 'sb_instagram_scripts_enqueue', 2 );

function sb_instagram_media_vine_js_register() {
	//Register the script to make it available
	wp_register_script( 'sb_instagram_mediavine_scripts', trailingslashit( SBI_PLUGIN_URL ) . 'js/sb-instagram-mediavine.js', array( 'jquery', 'sb_instagram_scripts' ), SBIVER, true );
}
add_action( 'wp_enqueue_scripts', 'sb_instagram_media_vine_js_register' );

/**
 * Adds the ajax url and custom JavaScript to the page
 */
function sb_instagram_custom_js() {
	$options = get_option('sb_instagram_settings');
	isset($options[ 'sb_instagram_custom_js' ]) ? $sb_instagram_custom_js = trim($options['sb_instagram_custom_js']) : $sb_instagram_custom_js = '';

	echo '<!-- Custom Feeds for Instagram JS -->';
	echo "\r\n";
	echo '<script type="text/javascript">';
	echo "\r\n";
	echo 'var sbiajaxurl = "' . esc_url( admin_url('admin-ajax.php') ) . '";';
    echo "\r\n";

	if ( ! empty( $sb_instagram_custom_js ) ) { ?>
window.sbi_custom_js = function(){
$ = jQuery;
<?php echo stripslashes( $sb_instagram_custom_js ); ?>
}
    <?php }

	echo "\r\n";
	echo '</script>';
	echo "\r\n";
}
add_action( 'wp_footer', 'sb_instagram_custom_js' );

//Custom CSS
add_action( 'wp_head', 'sb_instagram_custom_css' );
function sb_instagram_custom_css() {
	$options = get_option('sb_instagram_settings');

	isset($options[ 'sb_instagram_custom_css' ]) ? $sb_instagram_custom_css = trim($options['sb_instagram_custom_css']) : $sb_instagram_custom_css = '';

	//Show CSS if an admin (so can see Hide Photos link), if including Custom CSS or if hiding some photos
	( current_user_can( 'edit_posts' ) || !empty($sb_instagram_custom_css) || !empty($sb_instagram_hide_photos) ) ? $sbi_show_css = true : $sbi_show_css = false;

	if( $sbi_show_css ) echo '<!-- Custom Feeds for Instagram CSS -->';
	if( $sbi_show_css ) echo "\r\n";
	if( $sbi_show_css ) echo '<style type="text/css">';

	if( !empty($sb_instagram_custom_css) ){
		echo "\r\n";
		echo stripslashes($sb_instagram_custom_css);
	}

	if( current_user_can( 'edit_posts' ) ){
		echo "\r\n";
		echo "#sbi_mod_link, #sbi_mod_error{ display: block !important; }";
	}

	if( $sbi_show_css ) echo "\r\n";
	if( $sbi_show_css ) echo '</style>';
	if( $sbi_show_css ) echo "\r\n";
}

/**
 * Used to change the number of posts in the api request. Useful for filtered posts
 * or special caching situations.
 *
 * @param int $num
 * @param array $settings
 *
 * @return int
 */
function sbi_raise_num_in_request( $num, $settings ) {
    if ( $settings['sortby'] === 'random'
         || ! empty( $settings['includewords'] )
         || ! empty( $settings['excludewords'] )
         || $settings['media'] !== 'all'
         || ! empty ($settings['whitelist_ids'] ) ) {
        if ( $num > 6 ) {
	        return min( $num * 4, 100 );
        } else {
            return 30;
        }
    }
    return $num;
}
add_filter( 'sbi_num_in_request', 'sbi_raise_num_in_request', 5, 2 );

/**
 * Load the critical notice for logged in users.
 */
function sbi_critical_error_notice() {
	// Don't do anything for guests.
	if ( ! is_user_logged_in() ) {
		return;
	}

	// Only show this to users who are not tracked.
	if ( ! current_user_can( 'manage_instagram_feed_options' ) ) {
		return;
	}

	global $sb_instagram_posts_manager;
	if ( ! $sb_instagram_posts_manager->are_critical_errors() ) {
		return;
	}


	// Don't show if already dismissed.
	if ( get_option( 'sbi_dismiss_critical_notice', false ) ) {
		return;
	}

	$db_settings = sbi_get_database_settings();
	if ( isset( $db_settings['disable_admin_notice'] ) && $db_settings['disable_admin_notice'] === 'on' ) {
		return;
	}

	?>
    <div class="sbi-critical-notice sbi-critical-notice-hide">
        <div class="sbi-critical-notice-icon">
            <img src="<?php echo esc_url( SBI_PLUGIN_URL . 'img/insta-logo.png' ); ?>" width="45" alt="Instagram Feed icon" />
        </div>
        <div class="sbi-critical-notice-text">
            <h3><?php esc_html_e( 'Instagram Feed Critical Issue', 'instagram-feed' ); ?></h3>
            <p>
				<?php
				$doc_url = admin_url() . '?page=sb-instagram-feed&amp;tab=configure';
				// Translators: %s is the link to the article where more details about critical are listed.
				printf( esc_html__( 'An issue is preventing your Instagram Feeds from updating. %1$sResolve this issue%2$s.', 'instagram-feed' ), '<a href="' . esc_url( $doc_url ) . '" target="_blank">', '</a>' );
				?>
            </p>
        </div>
        <div class="sbi-critical-notice-close">&times;</div>
    </div>
    <style type="text/css">
        .sbi-critical-notice {
            position: fixed;
            bottom: 20px;
            right: 15px;
            font-family: Arial, Helvetica, "Trebuchet MS", sans-serif;
            background: #fff;
            box-shadow: 0 0 10px 0 #dedede;
            padding: 10px 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 325px;
            max-width: calc( 100% - 30px );
            border-radius: 6px;
            transition: bottom 700ms ease;
            z-index: 10000;
        }

        .sbi-critical-notice h3 {
            font-size: 13px;
            color: #222;
            font-weight: 700;
            margin: 0 0 4px;
            padding: 0;
            line-height: 1;
            border: none;
        }

        .sbi-critical-notice p {
            font-size: 12px;
            color: #7f7f7f;
            font-weight: 400;
            margin: 0;
            padding: 0;
            line-height: 1.2;
            border: none;
        }

        .sbi-critical-notice p a {
            color: #7f7f7f;
            font-size: 12px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
            text-decoration: underline;
            font-weight: 400;
        }

        .sbi-critical-notice p a:hover {
            color: #666;
        }

        .sbi-critical-notice-icon img {
            height: auto;
            display: block;
            margin: 0;
        }

        .sbi-critical-notice-icon {
            padding: 0;
            border-radius: 4px;
            flex-grow: 0;
            flex-shrink: 0;
            margin-right: 12px;
            overflow: hidden;
        }

        .sbi-critical-notice-close {
            padding: 10px;
            margin: -12px -9px 0 0;
            border: none;
            box-shadow: none;
            border-radius: 0;
            color: #7f7f7f;
            background: transparent;
            line-height: 1;
            align-self: flex-start;
            cursor: pointer;
            font-weight: 400;
        }
        .sbi-critical-notice-close:hover,
        .sbi-critical-notice-close:focus{
            color: #111;
        }

        .sbi-critical-notice.sbi-critical-notice-hide {
            bottom: -200px;
        }
    </style>
	<?php

	if ( ! wp_script_is( 'jquery', 'queue' ) ) {
		wp_enqueue_script( 'jquery' );
	}
	?>
    <script>
        if ( 'undefined' !== typeof jQuery ) {
            jQuery( document ).ready( function ( $ ) {
                /* Don't show the notice if we don't have a way to hide it (no js, no jQuery). */
                $( document.querySelector( '.sbi-critical-notice' ) ).removeClass( 'sbi-critical-notice-hide' );
                $( document.querySelector( '.sbi-critical-notice-close' ) ).on( 'click', function ( e ) {
                    e.preventDefault();
                    $( this ).closest( '.sbi-critical-notice' ).addClass( 'sbi-critical-notice-hide' );
                    $.ajax( {
                        url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
                        method: 'POST',
                        data: {
                            action: 'sbi_dismiss_critical_notice',
                            nonce: '<?php echo esc_js( wp_create_nonce( 'sbi-critical-notice' ) ); ?>',
                        }
                    } );
                } );
            } );
        }
    </script>
	<?php
}

add_action( 'wp_footer', 'sbi_critical_error_notice', 300 );

/**
 * Ajax handler to hide the critical notice.
 */
function sbi_dismiss_critical_notice() {

	check_ajax_referer( 'sbi-critical-notice', 'nonce' );

	update_option( 'sbi_dismiss_critical_notice', 1, false );

	wp_die();

}

add_action( 'wp_ajax_sbi_dismiss_critical_notice', 'sbi_dismiss_critical_notice' );

function sbi_schedule_report_email() {
	$options = get_option( 'sb_instagram_settings', array() );

	$input = isset( $options[ 'email_notification' ] ) ? $options[ 'email_notification' ] : 'monday';
	$timestamp = strtotime( 'next ' . $input );
	$timestamp = $timestamp + (3600 * 24 * 7);

	$six_am_local = $timestamp + sbi_get_utc_offset() + (6*60*60);

	wp_schedule_event( $six_am_local, 'sbiweekly', 'sb_instagram_feed_issue_email' );
}

function sbi_send_report_email() {
	$options = get_option('sb_instagram_settings' );

	$to_string = ! empty( $options['email_notification_addresses'] ) ? str_replace( ' ', '', $options['email_notification_addresses'] ) : get_option( 'admin_email', '' );

	$to_array_raw = explode( ',', $to_string );
	$to_array = array();

	foreach ( $to_array_raw as $email ) {
		if ( is_email( $email ) ) {
			$to_array[] = $email;
		}
	}

	if ( empty( $to_array ) ) {
		return false;
	}
    $from_name = esc_html( wp_specialchars_decode( get_bloginfo( 'name' ) ) );
    $email_from = $from_name . ' <' . get_option( 'admin_email', $to_array[0] ) . '>';
    $header_from  = "From: " . $email_from;

    $headers = array( 'Content-Type: text/html; charset=utf-8', $header_from );

	$header_image = SBI_PLUGIN_URL . 'img/balloon-120.png';

	$link = admin_url( '?page=sb-instagram-feed');
	//&tab=customize-advanced
	$footer_link = admin_url('admin.php?page=sb-instagram-feed&tab=customize-advanced&flag=emails');

	$is_expiration_notice = false;

	if ( isset( $options['connected_accounts'] ) ) {
	    foreach ( $options['connected_accounts'] as $account ) {
	        if ( $account['type'] === 'basic'
	             && isset( $account['private'] )
                 && sbi_private_account_near_expiration( $account ) ) {
		        $is_expiration_notice = true;
            }
        }
    }

	if ( ! $is_expiration_notice ) {
		$title = sprintf( __( 'Instagram Feed Report for %s', 'instagram-feed' ), str_replace( array( 'http://', 'https://' ), '', home_url() ) );
		$bold = __( 'There\'s an Issue with an Instagram Feed on Your Website', 'instagram-feed' );
		$details = '<p>' . __( 'An Instagram feed on your website is currently unable to connect to Instagram to retrieve new posts. Don\'t worry, your feed is still being displayed using a cached version, but is no longer able to display new posts.', 'instagram-feed' ) . '</p>';
		$details .= '<p>' . sprintf( __( 'This is caused by an issue with your Instagram account connecting to the Instagram API. For information on the exact issue and directions on how to resolve it, please visit the %sInstagram Feed settings page%s on your website.', 'instagram-feed' ), '<a href="' . esc_url( $link ) . '">', '</a>' ). '</p>';
	} else {
		$title = __( 'Your Private Instagram Feed Account Needs to be Reauthenticated', 'instagram-feed' );
		$bold = __( 'Access Token Refresh Needed', 'instagram-feed' );
		$details = '<p>' . __( 'As your Instagram account is set to be "Private", Instagram requires that you reauthenticate your account every 60 days. This a courtesy email to let you know that you need to take action to allow the Instagram feed on your website to continue updating. If you don\'t refresh your account, then a backup cache will be displayed instead.', 'instagram-feed' ) . '</p>';
		$details .= '<p>' . sprintf( __( 'To prevent your account expiring every 60 days %sswitch your account to be public%s. For more information and to refresh your account, click here to visit the %sInstagram Feed settings page%s on your website.', 'instagram-feed' ), '<a href="https://help.instagram.com/116024195217477/In">', '</a>', '<a href="' . esc_url( $link ) . '">', '</a>' ). '</p>';
	}
    $message_content = '<h6 style="padding:0;word-wrap:normal;font-family:\'Helvetica Neue\',Helvetica,Arial,sans-serif;font-weight:bold;line-height:130%;font-size: 16px;color:#444444;text-align:inherit;margin:0 0 20px 0;Margin:0 0 20px 0;">' . $bold . '</h6>' . $details;
	include_once SBI_PLUGIN_DIR . 'inc/class-sb-instagram-education.php';
	$educator = new SB_Instagram_Education();
	$dyk_message = $educator->dyk_display();
	ob_start();
	include SBI_PLUGIN_DIR . 'inc/email.php';
	$email_body = ob_get_contents();
	ob_get_clean();

	$sent = wp_mail( $to_array, $title, $email_body, $headers );

	return $sent;
}

function sbi_maybe_send_feed_issue_email() {
	global $sb_instagram_posts_manager;
	if ( ! $sb_instagram_posts_manager->are_critical_errors() ) {
		return;
	}
	$options = get_option('sb_instagram_settings' );

	if ( isset( $options['enable_email_report'] ) && empty( $options['enable_email_report'] ) ) {
		return;
	}

	sbi_send_report_email();
}
add_action( 'sb_instagram_feed_issue_email', 'sbi_maybe_send_feed_issue_email' );

function sbi_update_option( $option_name, $option_value, $autoload = true ) {
    return update_option( $option_name, $option_value, $autoload = true );
}

function sbi_get_option( $option_name, $default ) {
	return get_option( $option_name, $default );
}

function sbi_is_pro_version() {
	return defined( 'SBI_STORE_URL' );
}