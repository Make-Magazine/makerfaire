<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class SW_Feed {
	/**
	 * @var string
	 */
	private $regular_feed_transient_name;

	/**
	 * @var string
	 */
	private $header_transient_name;

	private $misc_transient_name;

	/**
	 * @var string
	 */
	private $backup_feed_transient_name;

	/**
	 * @var string
	 */
	private $backup_header_transient_name;

	/**
	 * @var array
	 */
	private $post_data;

	/**
	 * @var
	 */
	private $header_data;

	public $misc_data;

	private $plugins_with_atts;

	private $last_retrieve;

	private $last_requested;

	/**
	 * @var bool
	 */
	private $should_paginate;


	/**
	 * @var bool
	 */
	private $should_use_backup;

	/**
	 * @var array
	 */
	private $report;

	private $next_pages;

	private $pages_created;

	private $background_processes_flag;

	/**
	 * SW_Feed constructor.
	 *
	 * @param string $transient_name ID of this feed
	 *  generated in the SB_Instagram_Settings class
	 */
	public function __construct( $transient_name ) {
		$this->regular_feed_transient_name = $transient_name;
		$this->backup_feed_transient_name  = SBSW_BACKUP_PREFIX . $transient_name;

		$sbsw_header_transient_name          = str_replace( 'sbsw_', 'sbsw_header_', $transient_name );
		$sbsw_header_transient_name          = substr( $sbsw_header_transient_name, 0, 44 );
		$this->header_transient_name        = $sbsw_header_transient_name;

		$sbsw_misc_transient_name          = str_replace( 'sbsw_', 'sbsw_misc_', $transient_name );
		$sbsw_misc_transient_name          = substr( $sbsw_misc_transient_name, 0, 44 );
		$this->misc_transient_name        = $sbsw_misc_transient_name;
		$this->backup_header_transient_name = SBSW_BACKUP_PREFIX . $sbsw_header_transient_name;

		$this->post_data       = array();
		$this->misc_data       = array();
		$this->should_paginate = true;
		$this->pages_created = 0;

		$this->plugins_with_atts = array();
		$this->atts = array();
		$this->should_use_backup = false;
		$this->background_processes_flag = false;

		// used for errors and the sbsw_debug report
		$this->report = array();
	}

	/**
	 * @return array
	 *
	 * @since 1.0
	 */
	public function get_post_data() {
		return $this->post_data;
	}

	public function get_next_pages() {
		return $this->next_pages;
	}

	public function get_plugins_with_atts() {
		return $this->plugins_with_atts;
	}

	public function set_atts( $atts ) {
		$this->atts = $atts;
	}

	public function set_plugins_with_atts( $plugins_with_atts ) {
		$this->plugins_with_atts = $plugins_with_atts;
	}
	public function set_last_retrieve( $last_retrieve ) {
		$this->last_retrieve = $last_retrieve;
	}

	public function set_last_requested( $last_requested ) {
		$this->last_requested = $last_requested;
	}
	/**
	 * @return array
	 *
	 * @since 1.0
	 */
	public function set_post_data( $post_data ) {
		$this->post_data = $post_data;
	}

	public function set_next_pages( $next_pages ) {
		$this->next_pages = $next_pages;
	}

	public function set_pages_created( $num ) {
	    $this->pages_created = $num;
    }

    public function set_misc_data( $misc_data ) {
	    $this->misc_data = $misc_data;
    }

	public function get_misc_data() {
		return $this->misc_data;
	}

    public function append_misc_data( $append_misc_data ) {
	    if ( isset( $this->misc_data['youtube'] ) ) {
		    $this->misc_data['youtube']['stats'] = array_merge( $this->misc_data['youtube']['stats'], $append_misc_data['youtube']['stats'] );
	    }
    }

	/**
	 * Checks the database option related the transient expiration
	 * to ensure it will be available when the page loads
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function regular_cache_exists() {
		//Check whether the cache transient exists in the database and is available for more than one more minute
		$transient_exists = get_transient( $this->regular_feed_transient_name );

		return $transient_exists;
	}

	/**
	 * Checks the database option related the header transient
	 * expiration to ensure it will be available when the page loads
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function regular_header_cache_exists() {
		$header_transient = get_transient( $this->header_transient_name );

		return $header_transient;
	}

	public function cache_header_data( $cache_time, $save_backup = false ) {
		if ( $this->header_data ) {
			set_transient( $this->header_transient_name, wp_json_encode( $this->header_data ), $cache_time );
		}
	}

	/**
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function should_use_backup() {
		return $this->should_use_backup || empty( $this->post_data );
	}

	public function get_report() {
		return $this->report;
	}

	/**
	 * The header is only displayed when the setting is enabled and
	 * an account has been connected
	 *
	 * Overwritten in the Pro version
	 *
	 * @param array $settings settings specific to this feed
	 * @param array $feed_types_and_terms organized settings related to feed data
	 *  (ex. 'user' => array( 'smashballoon', 'customyoutubefeed' )
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function need_header( $settings, $feed_types_and_terms ) {
		$showheader = ($settings['showheader'] === 'on' || $settings['showheader'] === 'true' || $settings['showheader'] === true);
		return ($showheader && isset( $feed_types_and_terms['channels'] ));
	}

	/**
	 * Use the transient name to retrieve cached data for header
	 *
	 * @since 1.0
	 */
	public function set_header_data_from_cache() {
		$header_cache = get_transient( $this->header_transient_name );

		$header_cache = json_decode( $header_cache, true );

		if ( ! empty( $header_cache ) ) {
			$this->header_data = $header_cache;
		}
	}

	public function set_header_data( $header_data ) {
		$this->header_data = $header_data;
	}

	/**
	 * @since 1.0
	 */
	public function get_header_data() {
		return $this->header_data;
	}

	public function cache_feed_data( $cache_time, $next_pages, $save_backup = false ) {
		if ( ! empty( $this->post_data ) ) {
			$this->trim_posts_to_max();

            $this->set_next_pages( $next_pages );
			$to_cache = array(
				'data' => $this->post_data,
				'pagination' => $next_pages,
                'pages_created' => $this->pages_created,
                'plugins_with_atts' => $this->plugins_with_atts,
				'last_retrieve' => $this->last_retrieve,
				'last_requested' => $this->last_requested,
				'atts' => $this->atts
			);

			set_transient( $this->regular_feed_transient_name, wp_json_encode( $to_cache ), $cache_time );
			set_transient( $this->misc_transient_name, wp_json_encode( $this->misc_data ), $cache_time );
		} else {
			$this->add_report( 'no data not caching' );
		}
	}

	/**
	 * Sets the post data, pagination data, shortcode atts used (cron cache),
	 * and timestamp of last retrieval from transient (cron cache)
	 *
	 * @param array $atts available for cron caching
	 *
	 * @since 1.0
	 */
	public function set_post_data_from_cache( $atts = array() ) {
		$transient_data = get_transient( $this->regular_feed_transient_name );

		$transient_data = json_decode( $transient_data, true );

		if ( $transient_data ) {
			$post_data = isset( $transient_data['data'] ) ? $transient_data['data'] : array();
			$this->post_data = $post_data;
			$this->next_pages = isset( $transient_data['pagination'] ) ? $transient_data['pagination'] : array();
			$this->pages_created = isset( $transient_data['pages_created'] ) ? $transient_data['pages_created'] : 0;
			$this->plugins_with_atts = isset( $transient_data['plugins_with_atts'] ) ? $transient_data['plugins_with_atts'] : array();
			$this->atts = isset( $transient_data['atts'] ) ? $transient_data['atts'] : array();          
            $this->last_retrieve = isset( $transient_data['last_retrieve'] ) ? $transient_data['last_retrieve'] : null;
            $this->last_requested = isset( $transient_data['last_requested'] ) ? $transient_data['last_requested'] : null;


			$misc_transient_data = get_transient( $this->misc_transient_name );

			$misc_transient_data = json_decode( $misc_transient_data, true );

			$this->misc_data = $misc_transient_data;

			$this->add_report( 'getting feed from cache' );
			$this->add_report( 'last retrieve: ' . date( 'Y-m-d H:i:s', $this->last_retrieve ) .', last requested: ' . date( 'Y-m-d H:i:s', $this->last_requested ) );
			$this->add_report( 'pages created: ' . $this->pages_created .', next pages exist: ' . ! empty( $this->next_pages ) );
		}
	}

	public function set_non_post_related_data_from_cache() {
		$transient_data = get_transient( $this->regular_feed_transient_name );

		$transient_data = json_decode( $transient_data, true );

		if ( $transient_data ) {
	        $this->plugins_with_atts = isset( $transient_data['plugins_with_atts'] ) ? $transient_data['plugins_with_atts'] : array();
			$this->atts = isset( $transient_data['atts'] ) ? $transient_data['atts'] : array();
			$this->last_retrieve = $transient_data['last_retrieve'];
			$this->last_requested = $transient_data['last_requested'];
		}
	}

	public function feed_is_not_updating() {
	    $settings = sbsw_get_database_settings();
		$sbsw_cache_cron_interval = isset( $settings['cache_cron_interval'] ) ? $settings['cache_cron_interval'] : '';

		$compare_seconds = HOUR_IN_SECONDS;
        if ( $sbsw_cache_cron_interval === '12hours' ) {
            $compare_seconds = HOUR_IN_SECONDS * 12;
        } elseif ( $sbsw_cache_cron_interval === '24hours' ) {
	        $compare_seconds = HOUR_IN_SECONDS * 24;
        } elseif ( $sbsw_cache_cron_interval === '30mins' ) {
	        $compare_seconds = HOUR_IN_SECONDS / 2;
        }

		$force_refresh_time = $compare_seconds + HOUR_IN_SECONDS;
	    return ($this->last_retrieve < time() - $force_refresh_time);
    }

	public function delete_transients() {
        delete_transient( $this->regular_feed_transient_name );
        delete_transient( $this->header_transient_name );
        delete_transient( $this->misc_transient_name );
	}

	/**
	 * Sets post data from a permanent database backup of feed
	 * if it was created
	 *
	 * @since 1.0
	 */
	public function maybe_set_post_data_from_backup() {
		$backup_data = get_option( $this->backup_feed_transient_name, false );

		if ( $backup_data ) {
			$backup_data = json_decode( $backup_data, true );

			$post_data = isset( $backup_data['data'] ) ? $backup_data['data'] : array();
			$this->post_data = $post_data;
			$this->next_pages = isset( $backup_data['pagination'] ) ? $backup_data['pagination'] : array();

			if ( isset( $backup_data['atts'] ) ) {
				$this->last_retrieve = $backup_data['last_retrieve'];
			}

			$this->maybe_set_header_data_from_backup();

			return true;
		} else {
			$this->add_report( 'no backup post data found' );

			return false;
		}
	}

	/**
	 * Sets header data from a permanent database backup of feed
	 * if it was created
	 *
	 * @since 1.0
	 */
	public function maybe_set_header_data_from_backup() {
		$backup_header_data = get_option( $this->backup_header_transient_name, false );

		if ( ! empty( $backup_header_data ) ) {
			$backup_header_data = json_decode( $backup_header_data, true );
			$this->header_data = $backup_header_data;

			return true;
		} else {
			$this->add_report( 'no backup header data found' );

			return false;
		}
	}

	public function process_raw_posts( $raw_posts, $settings ) {
		$posts = $this->merge_posts( $raw_posts, $settings );

		$existing_posts = $this->get_post_data();

		if ( ! empty( $existing_posts ) ) {
			$merged_posts = array_merge( $existing_posts, $posts );
			$merged_posts = $this->sort_posts( $merged_posts, $settings );
		} else {
			$posts = $this->sort_posts( $posts, $settings );
			$merged_posts = $posts;
		}

		$this->set_post_data( $merged_posts );

		$this->remove_duplicate_posts();
	}

	public function need_posts( $num, $offset = 0, $page = 0 ) {
		$num_existing_posts = is_array( $this->post_data ) ? count( $this->post_data ) : 0;
		$num_needed_for_page = (int)$num + (int)$offset;
		if ( $this->pages_created < $page ) {
			$this->add_report( 'need another page' );
			$this->pages_created ++;
			return true;
        }

		($num_existing_posts < $num_needed_for_page) ? $this->add_report( 'need more posts' ) : $this->add_report( 'have enough posts' );

		return ($num_existing_posts < $num_needed_for_page);
	}

	/**
	 * Checks to see if there are additional pages available for any of the
	 * accounts in the feed and that the max conccurrent api request limit
	 * has not been reached
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function can_get_more_posts() {
		$one_type_and_term_has_more_ages = $this->next_pages !== false;
		$one_type_and_term_has_more_ages ? $this->add_report( 'more pages available' ) : $this->add_report( 'no next page' );

		return ($one_type_and_term_has_more_ages);
	}

	public function should_use_pagination( $settings, $offset = 0 ) {
		if ( $settings['minnum'] < 1 ) {
			$this->add_report( 'minnum too small' );

			return false;
		}

		if ( $settings['layout'] === 'carousel' ) {
			return false;
		}
		$posts_available = count( $this->post_data ) - ($offset + $settings['minnum']);
		$show_loadmore_button_by_settings = ($settings['showbutton'] == 'on' || $settings['showbutton'] == 'true' || $settings['showbutton'] == true ) && $settings['showbutton'] !== 'false';

		if ( $show_loadmore_button_by_settings ) {
			if ( $posts_available > 0 ) {
				$this->add_report( 'do pagination, posts available' );
				return true;
			}
			$pages = $this->next_pages;

			if ( $pages && ! $this->should_use_backup() ) {
				foreach ( $pages as $page ) {
					if ( ! empty( $page ) ) {
						return true;
					}
				}
			}

		}

		$this->add_report( 'no pagination, no posts available' );

		return false;
	}

	public function set_background_processes_flag( $status ) {
	    $this->background_processes_flag = $status;
    }

	public function update_cache( $to_update ) {
		if ( ! empty( $this->post_data )
		     || ! empty( $this->next_pages )
		     || ! empty( $to_cache['data'] ) ) {
			$to_cache = array(
				'data' => $this->post_data,
				'pagination' => $this->next_pages,
				'pages_created' => $this->pages_created,
				'plugins_with_atts' => $this->plugins_with_atts,
				'last_retrieve' => $this->last_retrieve,
				'last_requested' => $this->last_requested,
				'atts' => $this->atts
			);

			foreach ( $to_update as $key => $value ) {
				$to_cache[ $key ] = $value;
            }

			set_transient( $this->regular_feed_transient_name, sbsw_json_encode( $to_cache ), SBSW_CRON_UPDATE_CACHE_TIME );
		} else {
			$this->add_report( 'no data not caching' );
		}

	}

	public function should_update_last_requested() {
		return (rand( 1, 20 ) === 20);
	}

	public static function filter_for_plugin( $posts, $plugin ) {
	    $filtered = array();

	    foreach ( $posts as $post ) {
	        if ( SW_Parse::get_plugin( $post ) === $plugin ) {
		        $filtered[] = $post;
            }
        }

	    return $filtered;

    }

	private function merge_posts( $plugin_post_sets, $settings ) {
		$merged_posts = array();
		if ( $settings['sortby'] === 'alternate' ) {
			$min_cycles = max( 1, (int)$settings['num'] );
			for ( $i = 0; $i <= $min_cycles; $i ++ ) {
				foreach ( $plugin_post_sets as $post_set ) {
					if ( isset( $post_set[ $i ] ) ) {
						$merged_posts[] = $post_set[ $i ];
					}
				}
			}

		} else {
			if ( isset( $plugin_post_sets[0] ) ) {
				foreach ( $plugin_post_sets as $post_set ) {
					$merged_posts = array_merge( $merged_posts, $post_set );
				}
			}
		}

		return $merged_posts;
	}

	protected function remove_duplicate_posts() {
		$posts = $this->post_data;
		$ids_in_feed = array(
            'instagram' => array(),
            'facebook' => array(),
            'twitter' => array(),
            'youtube' => array(),
		);
		$non_duplicate_posts = array();
		$removed = array();

		foreach ( $posts as $post ) {
		    $plugin = SW_Parse::get_plugin( $post );
			$post_id = SW_Parse::get_post_id( $post, $plugin );
			if ( ! in_array( $post_id, $ids_in_feed[ $plugin ], true ) ) {
				$ids_in_feed[ $plugin ][] = $post_id;
				$non_duplicate_posts[] = $post;
			} else {
				$removed[] = $post_id;
			}
		}

		$this->add_report( 'removed duplicates: ' . implode(', ', $removed ) );
		$this->set_post_data( $non_duplicate_posts );
	}

	/**
	 * Sorts a post set based on sorting settings. Sorting by "alternate"
	 * is done when merging posts for efficiency's sake so the post set is
	 * just returned as it is.
	 *
	 * @param array $post_set
	 * @param array $settings
	 *
	 * @return mixed|array
	 *
	 * @since 1.0
	 */
	protected function sort_posts( $post_set, $settings ) {
		if ( empty( $post_set ) ) {
			return $post_set;
		}

		// sorting done with "merge_posts" to be more efficient
		if ( $settings['sortby'] === 'alternate' || $settings['sortby'] === 'api' ) {
			$return_post_set = $post_set;
		} elseif ( $settings['sortby'] === 'random' ) {
			/*
             * randomly selects posts in a random order. Cache saves posts
             * in this random order so paginating does not cause some posts to show up
             * twice or not at all
             */
			usort($post_set, 'sbsw_rand_sort' );
			$return_post_set = $post_set;

		} else {
			// compares posted on dates of posts
			usort($post_set, 'sbsw_date_sort' );
			$return_post_set = $post_set;
		}

		/**
		 * Apply a custom sorting of posts
		 *
		 * @param array $return_post_set    Ordered set of filtered posts
		 * @param array $settings           Settings for this feed
		 *
		 * @since 1.0
		 */

		return apply_filters( 'sbsw_sorted_posts', $return_post_set, $settings );
	}

	protected function add_other_atts( $other_atts, $settings ) {
		$options_att_arr = array();

		$cols = (int)( $settings['cols'] ) > 0 ? (int)$settings['cols'] : '3';
		$options_att_arr['cols'] = $cols;

		$colsmobile = (int)( $settings['colsmobile'] ) > 0 ? (int)$settings['colsmobile'] : 'auto';
		$options_att_arr['colsmobile'] = $colsmobile;

		$layout = $settings['layout'];
		if ( ! in_array( $layout, array( 'masonry', 'carousel' ) ) ) {
			$layout = 'list';
		}

		if ( $layout === 'masonry' ) {
			$options_att_arr['masonry'] = true;
		} elseif ( $layout === 'carousel' ) {
			$options_att_arr['carousel'] = true;
            $arrows = $settings['carouselarrows'] == 'true' || $settings['carouselarrows'] == 'on' || $settings['carouselarrows'] == 1 || $settings['carouselarrows'] == '1';
            $pag = $settings['carouselpag'] == 'true' || $settings['carouselpag'] == 'on' || $settings['carouselpag'] == 1 || $settings['carouselpag'] == '1';
            $autoplay = $settings['carouselautoplay'] == 'true' || $settings['carouselautoplay'] == 'on' || $settings['carouselautoplay'] == 1 || $settings['carouselautoplay'] == '1';
            $time = $autoplay ? (int)$settings['carouseltime'] : false;
            $loop = ! empty( $settings['carouselloop'] ) && ($settings['carouselloop'] !== 'rewind') ? false : true;
            $rows = ! empty( $settings['carouselrows'] ) ? min( (int)$settings['carouselrows'], 2 ) : 1;
            $options_att_arr['carousel'] = array( $arrows, $pag, $autoplay, $time, $loop, $rows );
			$options_att_arr['itemspacing'] = (int)$settings['itemspacing'].$settings['itemspacingunit'];
		} else {
			$options_att_arr['list'] = true;
			$options_att_arr['cols'] = 1;
			$options_att_arr['colsmobile'] = 1;
		}

		if ( ! empty( $settings['cache_all'] ) ) {
			$options_att_arr['cache_all'] = true;
		}

		$moderation_mode = isset( $settings['doingModerationMode'] );
		if ( $moderation_mode ) {
			$mod_index = isset( $_GET['sbsw_moderation_index'] ) ? sanitize_text_field( substr( $_GET['sbsw_moderation_index'], 0, 10 ) ) : '0';
			$options_att_arr['modindex'] = $mod_index;
			if ( ! empty( $settings['whitelist'] ) ) {
				$white_list_name = $settings['whitelist'];
				$white_list_ids = ! empty( $settings['whitelist'] ) ? get_option( 'sb_instagram_white_lists_'.$settings['whitelist'], array() ) : false;
				$options_att_arr['whiteListName'] = $white_list_name;
				$options_att_arr['whiteListIDs'] = $white_list_ids;
			}
			$hide_photos = ! empty( $settings['hidephotos'] ) ? explode( ',', str_replace( ' ', '', $settings['hidephotos'] ) ) : array();
			if ( ! empty( $hide_photos ) ) {
				$options_att_arr['hidePhotos'] = $hide_photos;
			}
		}

		if ( $settings['addModerationModeLink'] ) {
			$options_att_arr['moderationLink'] = true;
		}
		$other_atts .= ' data-options="'.esc_attr( wp_json_encode( $options_att_arr ) ).'"';

		return $other_atts;
	}

	public function get_the_feed_html( $posts, $account_data, $settings, $atts ) {
		$posts = array_slice( $this->post_data, 0, $settings['num'] );

		$use_pagination = $this->should_use_pagination( $settings, 0 );

		$feed_id = $this->regular_feed_transient_name;
		$shortcode_atts = ! empty( $atts ) ? wp_json_encode( $atts ) : '{}';

		$feed_atts = '';
		$feed_atts = $this->add_other_atts( $feed_atts, $settings );

		$flags = array();
		if ( $this->background_processes_flag ) {
			$flags[] = 'background';
		}
		if ( sbsw_instagram_feed_is_minimum_version() && sbsw_social_wall_is_minimum_version_for_instagram_feed() ) {
			global $sb_instagram_posts_manager;

			if ( $sb_instagram_posts_manager->image_resizing_disabled() ) {
				$flags[] = 'ifResizeDisable';
			}
		}
		if ( sbsw_facebook_feed_is_minimum_version() && sbsw_social_wall_is_minimum_version_for_facebook_feed() ) {
			$options = get_option('cff_style_settings');
			$disable_resize = isset($options[ 'cff_disable_resize' ]) ? $options[ 'cff_disable_resize' ] : false;
			$resizer = new CustomFacebookFeed\CFF_Resizer( array(), '', array(), array() );
			if ( $disable_resize || $resizer->image_resizing_disabled() ) {
				$flags[] = 'fbResizeDisable';
			}
		}
		if ( sbsw_twitter_feed_is_minimum_version() && sbsw_social_wall_is_minimum_version_for_twitter_feed()) {
		    if ( ! class_exists( 'CTF_Resizer' ) ) {
			    $flags[] = 'twResizeDisable';
		    } else {
				$options = get_option('ctf_options');
				$disable_resize = isset($options[ 'resize' ]) ? $options[ 'resize' ] === 'disable' : false;
				$resizer = new CTF_Resizer( array(), '', array(), array() );
				if ( $disable_resize || $resizer->image_resizing_disabled() ) {
					$flags[] = 'twResizeDisable';
				}
            }

		}
		if ( ! empty( $flags ) ) {
			$feed_atts .= ' data-sbsw-flags="' . implode(',', $flags ) . '"';
		}
		$settings['showfilter'] = $settings['masonryshowfilter'] && $settings['layout'] === 'masonry';
		if ( $settings['showfilter'] ) {
			$plugins_in_feed = array();

			foreach ( $this->plugins_with_atts as $plugin => $atts ) {
				if ( strpos( $plugin, 'instagram' ) !== false ) {
					$plugins_in_feed[] = 'instagram';
                } elseif ( strpos( $plugin, 'facebook' ) !== false ) {
					$plugins_in_feed[] = 'facebook';
				} elseif ( strpos( $plugin, 'twitter' ) !== false ) {
					$plugins_in_feed[] = 'twitter';
				} elseif ( strpos( $plugin, 'youtube' ) !== false ) {
					$plugins_in_feed[] = 'youtube';
				}
			}
        }

		if ( $settings['showfilter'] && count( $plugins_in_feed ) < 2 ) {
			$settings['showfilter'] = false;
        }

		if ( $settings['layout'] !== 'list' ) {
			$cols_setting = $settings['cols'];
			$colsmobile_setting = $settings['colsmobile'];
		} else {
			$cols_setting = 1;
			$colsmobile_setting = 1;
		}
		$maybe_feed_notice = sbsw_maybe_get_feed_notice( $this );

		ob_start();
		include sbsw_get_feed_template_part( 'feed', $settings );
		$html = ob_get_contents();
		ob_get_clean();

		return $html;
	}

	public function get_the_items_html( $posts, $account_data, $settings, $offset ) {
		if ( empty( $posts ) ) {
			ob_start();
			$html = ob_get_contents();
			ob_get_clean();		?>
			<p><?php _e( 'No posts found.', 'instagram-feed' ); ?></p>
			<?php
			$html = ob_get_contents();
			ob_get_clean();
			return $html;
		}

		$posts = array_slice( $posts, $offset, $settings['num'] );

		ob_start();
		$this->posts_loop( $posts, $account_data, $settings, $offset );

		$html = ob_get_contents();
		ob_get_clean();

		return $html;
	}

	private function posts_loop( $posts, $account_data, $settings, $offset = 0 ) {

		$image_ids = array();
		$post_index = $offset;
		$misc_data = $this->misc_data;

		foreach ( $posts as $post ) {
		    $plugin = SW_Parse::get_plugin( $post );
			$image_ids[ $plugin ][] = SW_Parse::get_post_id( $post, $plugin );
			include sbsw_get_feed_template_part( 'item', $settings );
			$post_index++;
		}

		$this->image_ids_post_set = $image_ids;
	}

	protected function trim_posts_to_max() {
		if ( ! is_array( $this->post_data ) ) {
			return;
		}

		$max = apply_filters( 'sbsw_max_cache_size', 700 );
		$this->set_post_data( array_slice( $this->post_data , 0, $max ) );
	}

	public function add_report( $to_add ) {
		$this->report[] = $to_add;
	}
}