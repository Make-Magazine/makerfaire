<?php
/**
 * Class CFF_Resizer
 *
 * Image resizing and local storage is done when there are no "medium"
 * sized images available from the API. This class handles this process
 * using the raw API data and a list of post IDs that need resizing.
 *
 * @since 3.14
 */

namespace CustomFacebookFeed;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class CFF_Resizer {
	/**
	 * @var array
	 */
	private $feed_id;

	private $post_ids_need_resizing;

	/**
	 * @var array
	 */
	private $resized_image_data;

	/**
	 * @var string|null
	 */
	private $upload_dir;

	/**
	 * @var string|null
	 */
	private $upload_url;

	private $resizing_tables_exist;

	private $limit;

	public function __construct( $post_ids_need_resizing, $feed_id, $posts, $feed_options ) {
		$this->post_ids_need_resizing = $post_ids_need_resizing;

		$this->feed_id = $feed_id;
		$this->feed_options = $feed_options;

		$this->posts = $posts;

		$this->image_sizes = CFF_Resizer::image_sizes( $feed_options );

		$upload = wp_upload_dir();
		$upload_dir = $upload['basedir'];
		$upload_dir = trailingslashit( $upload_dir ) . CFF_UPLOADS_NAME;

		$upload_url = trailingslashit( $upload['baseurl'] ) . CFF_UPLOADS_NAME;

		$this->upload_dir = $upload_dir;

		$this->upload_url = $upload_url;

		$this->limit = 200;
	}

	public function get_new_resized_image_data() {
		return $this->resized_image_data;
	}

	public function do_resizing() {
		$posts_iterated_through = 0;
		$number_resized = 0;
		$number_updated = 0;
		$connected_accounts = CFF_Utils::cff_get_connected_accounts();


		if ( CFF_Resizer::max_resizing_per_time_period_reached() ) {
			var_dump( 'skipping too frequent');
			return;
		}

		foreach ( $this->posts as $post ) {
			$facebook_api_id = CFF_Parse_Pro::get_post_id( $post );
			$connected_account = false;
			$in_resizing_array = in_array( $facebook_api_id, $this->post_ids_need_resizing, true );
			$imploded_resizing_string = implode( '_',$this->post_ids_need_resizing );
			$short_resizing_array = explode( '_', $imploded_resizing_string );
			if ( ! $in_resizing_array && strpos( $facebook_api_id, '_' ) === false ) {
				$in_resizing_array = in_array( $facebook_api_id, $short_resizing_array, true );

				/*
				 * The account ID is part of the ID sent for resizing. Adding the connected
				 * account here in case it's needed for getting the cover photo
				 */
				if ( CFF_Parse_Pro::is_album( $post ) ) {
					foreach ($this->post_ids_need_resizing as $post_need_resizing_ids) {
						$account_and_post_id = explode( '_', $post_need_resizing_ids );

						if ( isset( $account_and_post_id[1] ) && $account_and_post_id[1] === $facebook_api_id ) {
							if ( isset( $connected_accounts->{ $account_and_post_id[0] } ) ) {
								$connected_account = $connected_accounts->{ $account_and_post_id[0] };
							}
						}

					}

				}
			}
			if ( $in_resizing_array
				&& $posts_iterated_through < 600
				&& $number_resized < 300) {
				$single_post = new CFF_Post_Record( $post, $this->feed_id, $connected_account );

				if ( ! $single_post->exists_in_posts_table() ) {
					if ( CFF_Resizer::max_total_records_reached() ) {
						CFF_Resizer::delete_least_used_image();
					}
					$single_post->save_new_record();
					$single_post->resize_and_save_image( $this->image_sizes, $this->upload_dir );

					$number_resized++;
				} else {
					if ( ! $single_post->exists_in_feeds_posts_table() ) {
						$single_post->insert_cff_feeds_posts();
					}
					$number_updated++;
				}

			}

			$posts_iterated_through ++;
		}

	}


	public function do_resizing_group() {
		$posts_iterated_through = 0;
		$number_resized = 0;
		$number_updated = 0;
		$connected_accounts = CFF_Utils::cff_get_connected_accounts();


		if ( CFF_Resizer::max_resizing_per_time_period_reached() ) {
			var_dump( 'skipping too frequent');
			return;
		}

		foreach ( $this->posts as $post ) {
			$facebook_api_id = CFF_Parse_Pro::get_post_id( $post );
			$connected_account = false;
			$in_resizing_array = in_array( $facebook_api_id, $this->post_ids_need_resizing, true );
			$imploded_resizing_string = implode( '_',$this->post_ids_need_resizing );
			$short_resizing_array = explode( '_', $imploded_resizing_string );
			if ( ! $in_resizing_array && strpos( $facebook_api_id, '_' ) === false ) {
				$in_resizing_array = in_array( $facebook_api_id, $short_resizing_array, true );

				/*
				 * The account ID is part of the ID sent for resizing. Adding the connected
				 * account here in case it's needed for getting the cover photo
				 */
				if ( CFF_Parse_Pro::is_album( $post ) ) {
					foreach ($this->post_ids_need_resizing as $post_need_resizing_ids) {
						$account_and_post_id = explode( '_', $post_need_resizing_ids );

						if ( isset( $account_and_post_id[1] ) && $account_and_post_id[1] === $facebook_api_id ) {
							if ( isset( $connected_accounts->{ $account_and_post_id[0] } ) ) {
								$connected_account = $connected_accounts->{ $account_and_post_id[0] };
							}
						}

					}

				}
			}
			if ( $in_resizing_array
				&& $posts_iterated_through < 204
				&& $number_resized < 102) {
				$single_post = new CFF_Post_Record( $post, $this->feed_id, $connected_account );

				if ( ! $single_post->exists_in_posts_table() ) {
					if ( CFF_Resizer::max_total_records_reached() ) {
						CFF_Resizer::delete_least_used_image();
					}
					$single_post->save_new_record();
					$single_post->resize_and_save_image( $this->image_sizes, $this->upload_dir );

					$number_resized++;
				} else {
					if ( ! $single_post->exists_in_feeds_posts_table() ) {
						$single_post->insert_cff_feeds_posts();
					}
					$number_updated++;
				}

			}

			$posts_iterated_through ++;
		}

	}
	public static function image_sizes( $feed_options ) {
		$image_sizes = array( 400, 250 );

		if ( CFF_GDPR_Integrations::doing_gdpr( $feed_options ) ) {
			$image_sizes[] = 700;
		}

		$image_sizes = apply_filters( 'cff_resized_image_sizes', $image_sizes );

		return $image_sizes;
	}

	public static function get_resized_image_data_for_set( $ids_or_feed_id, $args = array() ) {
		global $wpdb;

		$posts_table_name = $wpdb->prefix . CFF_POSTS_TABLE;
		$feeds_posts_table_name = $wpdb->prefix . CFF_FEEDS_POSTS_TABLE;

		if ( is_array( $ids_or_feed_id ) ) {
			$ids = $ids_or_feed_id;

			$id_string = "'" . implode( "','", esc_sql( $ids ) ) . "'";
			$results = $wpdb->get_results( "
			SELECT p.media_id, p.facebook_id, p.sizes
			FROM $posts_table_name AS p
			WHERE p.facebook_id IN($id_string)
		  	AND p.images_done = 1", ARRAY_A );

			$return = $results;
		} else {
			$feed_id_array = explode( '#', $ids_or_feed_id );
			$feed_id = $feed_id_array[0];
			$limit = isset( $args['limit'] ) ? $args['limit'] : 100;
			$offset = isset( $args['offset'] ) ? $args['offset'] : 0;

			$results = $wpdb->get_results( $wpdb->prepare( "
			SELECT p.media_id, p.facebook_id, p.aspect_ratio, p.sizes
			FROM $posts_table_name AS p
			INNER JOIN $feeds_posts_table_name AS f ON p.id = f.id
			WHERE f.feed_id = %s
		  	AND p.images_done = 1
			ORDER BY p.time_stamp
			DESC LIMIT %d, %d", $feed_id, $offset, (int)$limit ), ARRAY_A );

			$return = $results;
		}

		return $return;
	}

	public static function delete_resizing_table_and_images() {
		$upload = wp_upload_dir();

		global $wpdb;

		$posts_table_name = $wpdb->prefix . CFF_POSTS_TABLE;
		$feeds_posts_table_name = $wpdb->prefix . CFF_FEEDS_POSTS_TABLE;

		$image_files = glob( trailingslashit( $upload['basedir'] ) . trailingslashit( CFF_UPLOADS_NAME ) . '*'  ); // get all file names
		foreach ( $image_files as $file ) { // iterate files
			if ( is_file( $file ) ) {
				unlink( $file );
			}
		}

		//Delete tables
		$wpdb->query( "DROP TABLE IF EXISTS $posts_table_name" );
		$wpdb->query( "DROP TABLE IF EXISTS $feeds_posts_table_name" );
	}

	public static function create_resizing_table_and_uploads_folder() {
		$upload = wp_upload_dir();

		$upload_dir = $upload['basedir'];
		$upload_dir = trailingslashit( $upload_dir ) . CFF_UPLOADS_NAME;
		if ( ! file_exists( $upload_dir ) ) {
			$created = wp_mkdir_p( $upload_dir );
			if ( $created ) {
				\cff_main_pro()->cff_error_reporter->remove_error( 'upload_dir' );
			} else {
				\cff_main_pro()->cff_error_reporter->add_error( 'upload_dir', array( __( 'There was an error creating the folder for storing resized images.', 'custom-facebook-feed' ), $upload_dir ) );

			}
		} else {
			\cff_main_pro()->cff_error_reporter->remove_error( 'upload_dir' );
		}
		return \cff_main_pro()->cff_create_database_table();
	}

	public static function delete_least_used_image() {
		global $wpdb;

		$posts_table_name = $wpdb->prefix . CFF_POSTS_TABLE;
		$feeds_posts_table_name = $wpdb->prefix . CFF_FEEDS_POSTS_TABLE;

		$image_sizes = CFF_Resizer::image_sizes( array() );

		$oldest_posts = $wpdb->get_results( "SELECT * FROM $posts_table_name ORDER BY last_requested ASC LIMIT 1", ARRAY_A );

		$upload = wp_upload_dir();

		foreach ( $oldest_posts as $post ) {
			$api_data = json_decode( $post['json_data'] );

			$api_post_id = CFF_Parse_Pro::get_post_id( $api_data );

			foreach ( $image_sizes as $image_size ) {
				$image_source_set    = CFF_Parse_Pro::get_media_src_set( $api_data );

				$new_file_name       = $api_post_id;
				$i = 0;
				foreach ( $image_source_set as $image_file_to_resize ) {
					if ($i < 4) {
						foreach ( $image_file_to_resize as $resolution => $image_url ) {

							$suffix = $image_size;

							$this_image_file_name = trailingslashit( $upload['basedir'] ) . trailingslashit( CFF_UPLOADS_NAME ) . $new_file_name . '-' . $i . '-' .  $suffix . '.jpg';

							if ( is_file( $this_image_file_name ) ) {
								unlink( $this_image_file_name );
							}

						}
					}

					$i++;
				}

			}

			$wpdb->query( $wpdb->prepare( "DELETE FROM $posts_table_name WHERE id = %d", $post['id'] ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM $feeds_posts_table_name WHERE record_id = %d", $post['id'] ) );

		}

	}

	/**
	 * Calculates how many records are in the database and whether or not it exceeds the limit
	 *
	 * @return bool
	 *
	 * @since 3.14
	 */
	public function max_total_records_reached() {
		global $wpdb;
		$table_name = $wpdb->prefix . CFF_POSTS_TABLE;

		$num_records = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );

		if ( !isset( $this->limit ) && (int)$num_records > CFF_MAX_RECORDS ) {
			$this->limit = (int)$num_records - CFF_MAX_RECORDS;
		}

		return ((int)$num_records > CFF_MAX_RECORDS);
	}

	/**
	 * The plugin caps how many new images are created in a 15 minute window to
	 * avoid overloading servers
	 *
	 * @return bool
	 *
	 * @since 3.14
	 */
	public static function max_resizing_per_time_period_reached() {
		global $wpdb;
		$table_name = $wpdb->prefix . CFF_POSTS_TABLE;

		$fifteen_minutes_ago = date( 'Y-m-d H:i:s', time() - 15 * 60 );

		$num_new_records = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE created_on > '$fifteen_minutes_ago'" );

		return ((int)$num_new_records > 100);
	}

	/**
	 * @return bool
	 *
	 * @since 3.14
	 */
	public function image_resizing_disabled() {
		$disable_resizing = isset( $this->feed_options['disableresize'] ) ? $this->feed_options['disableresize'] === 'on' || $this->feed_options['disableresize'] === true : false;
		return $disable_resizing;
	}

	/**
	 * Used to skip image resizing if the tables were never successfully
	 * created
	 *
	 * @return bool
	 *
	 * @since 3.14
	 */
	public function does_resizing_tables_exist() {
		global $wpdb;

        $table_name = esc_sql( $wpdb->prefix . CFF_FEEDS_POSTS_TABLE );
        $resizing_key = "cff_resizing_exists";

        $cff_resizing_cache = wp_cache_get( $resizing_key );

		if ( false === $cff_resizing_cache ) {

			if ( $wpdb->get_var( "show tables like '$table_name'" ) == $table_name ) {
				wp_cache_set( $resizing_key, true );
            } else {
                wp_cache_set( $resizing_key, false );
            }
		}

		return $cff_resizing_cache;
	}

}
