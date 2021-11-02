<?php
/**
 * Class SB_Instagram_Comments
 *
 * Collection of static functions meant to retrieve comments from
 * the Instagram API for a single post and store them in a cache.
 *
 * See the "sbiComments" object in the sb-instagram.js class to see how
 * loading of comments into the lightbox is handled.
 *
 * @since 5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class SB_Instagram_Comments
{
	/**
	 * AJAX listeners related to all of the frontend comment features
	 *
	 * @since 5.0
	 */
	public static function init_listeners() {
		add_action( 'wp_ajax_sbi_get_comment_cache', array( 'SB_Instagram_Comments', 'the_ajax_comment_cache' ) );
		add_action( 'wp_ajax_nopriv_sbi_get_comment_cache', array( 'SB_Instagram_Comments', 'the_ajax_comment_cache' ) );
		add_action( 'wp_ajax_sbi_remote_comments_needed', array( 'SB_Instagram_Comments', 'process_remote_comment_request' ) );
		add_action( 'wp_ajax_nopriv_sbi_remote_comments_needed', array( 'SB_Instagram_Comments', 'process_remote_comment_request' ) );
	}

	/**
	 * When the first image is opened in the lightbox, the comment
	 * cache is retrieved using AJAX
	 *
	 * @since 5.0
	 */
	public static function the_ajax_comment_cache() {

		$comment_cache = SB_Instagram_Comments::get_comment_cache();

		echo $comment_cache;

		die();
	}

	/**
	 * If no comments are available in the comment cache for a post, or
	 * the number of comments for the post are greater than the
	 * number in the cache, new remote comments are retreived.
	 *
	 * @since 5.0
	 * @since 5.1.2 remote comments only retrieved if API requests are not delayed
	 */
	public static function process_remote_comment_request() {
		$post_id = isset(  $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : false;
		$user = isset( $_POST['user'] ) ? sanitize_text_field( $_POST['user'] ) : false;
		$type = isset( $_POST['type'] ) && sanitize_text_field( $_POST['type'] ) === 'personal' ? 'personal' : 'business';

		if ( $post_id && $user ) {
			$sb_instagram_settings = get_option( 'sb_instagram_settings', array() );

			$account = array();
			foreach ( $sb_instagram_settings['connected_accounts'] as $connected_account ) {
				$ca_type = isset( $connected_account['type'] ) ? $connected_account['type'] : 'personal';
				if ( $connected_account['username'] === $user && $type === $ca_type ) {
					$account = $connected_account;
				}
			}

			if ( ! empty( $account['access_token'] ) ) {
				global $sb_instagram_posts_manager;

				$api_requests_delayed = $sb_instagram_posts_manager->are_current_api_request_delays( $account );

				if ( ! $api_requests_delayed ) {
					$comments = SB_Instagram_Comments::get_remote_comments( $account, $post_id );

					if ( $comments ) {
						SB_Instagram_Comments::update_comment_cache( $post_id, $comments, count( $comments ) );

						echo sbi_json_encode( $comments );
					} else {
						echo '{}';
					}

				}

			}

		}

		die();
	}

	/**
	 * Comments are stored in a cache with the Instagram Post ID
	 * as the key
	 *
	 * @return mixed|string
	 *
	 * @since 5.0
	 */
	public static function get_comment_cache() {
		$comment_cache = get_transient( 'sbinst_comment_cache' );

		if ( $comment_cache ) {
			$encryption = new SB_Instagram_Data_Encryption();

			$maybe_decrypted = $encryption->decrypt( $comment_cache );
			if ( ! empty( $maybe_decrypted ) ) {
				$comment_cache = $maybe_decrypted;
			}
			$comment_cache_data = ! empty( $comment_cache ) ? $comment_cache : '{}';
		} else {
			$comment_cache_data = '{}';
		}

		return $comment_cache_data;
	}

	/**
	 * Adds new comments to the comment cache. Only the latest
	 * 200 posts have comments cached.
	 *
	 * @param string $post_id
	 * @param array $comments
	 * @param int $total_comments
	 *
	 * @since 5.0
	 */
	public static function update_comment_cache( $post_id, $comments, $total_comments ) {

		$comment_cache_transient = get_transient( 'sbinst_comment_cache' );
		$encryption = new SB_Instagram_Data_Encryption();

		$maybe_decrypted = $encryption->decrypt( $comment_cache_transient );
		if ( ! empty( $maybe_decrypted ) ) {
			$comment_cache_transient = $maybe_decrypted;
		}

		$comment_cache = $comment_cache_transient ? json_decode( $comment_cache_transient, $assoc = true ) : array();

		if ( ! isset( $comment_cache[ $post_id ] ) && count( $comment_cache ) >= 200 ) {
			array_shift( $comment_cache );
		}

		$comment_cache[ $post_id ] = array( $comments, time() + (15 * 60), $total_comments );

		set_transient( 'sbinst_comment_cache', $encryption->encrypt( sbi_json_encode( $comment_cache ) ), 0 );
	}

	/**
	 * Retrieve comments for a specific post. Comments for a post can only be
	 * retrieved if the account is connected.
	 *
	 * @param array $account connected account for the post
	 * @param string $post_id
	 *
	 * @return array|bool
	 *
	 * @since 5.0
	 */
	public static function get_remote_comments( $account, $post_id ) {

		$comments_return = array();

		// basic display does not support comments as of January 2020
		if ( $account['type'] === 'basic' ) {
			return array();
		}

		$connection = new SB_Instagram_API_Connect_Pro( $account, 'comments', array( 'post_id' => $post_id ) );

		$connection->connect();

		if ( ! $connection->is_wp_error() && ! $connection->is_instagram_error() ) {
			$comments = $connection->get_data();

			if ( ! empty( $comments ) && isset( $comments[0]['text'] ) ) {
				foreach ( $comments as $comment ) {

					$username = isset( $comment['from'] ) ? SB_Instagram_Comments::clean_comment( $comment['from']['username'] ) : SB_Instagram_Comments::clean_comment( $comment['username'] );
					$comments_return[] = array(
						'text' => SB_Instagram_Comments::clean_comment( $comment['text'] ),
						'id' => esc_html( $comment['id'] ),
						'username' => esc_html( $username )
					);

				}
			}

			return $comments_return;

		} else {
			if ( $connection->is_wp_error() ) {
				SB_Instagram_API_Connect::handle_wp_remote_get_error( $connection->get_wp_error() );
			} else {
				SB_Instagram_API_Connect::handle_instagram_error( $connection->get_data(), $account, 'comments' );
			}

			return false;
		}
	}

	/**
	 * The only html allowed should be <br> tags. All parts of the comment are
	 * escaped and new lines converted to <br> to be easily and securely converted
	 * in the JS.
	 *
	 * @param $comment_part
	 *
	 * @return string
	 *
	 * @since 5.0
	 */
	public static function clean_comment( $comment_part ) {
		return esc_html( nl2br( $comment_part ) );
	}
}