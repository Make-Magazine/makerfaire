<?php
/**
 * Class SBI_Moderation_Mode_Feed
 *
 * @since 6.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
use InstagramFeed\SB_Instagram_Data_Encryption;

class SBI_Moderation_Mode_Feed extends SB_Instagram_Feed_Pro {
	/**
	 * @var array
	 */
	protected $cache;

	/**
	 * @var object|SBI_Moderation_Mode_Cache
	 */
	protected $previous_page_cache;

	/**
	 * @var int
	 */
	protected $feed_page;

	/**
	 * The cache we use for moderation mode pagination is based on each
	 * API response so it's handled differently
	 *
	 * @param int $cache_seconds
	 * @param string $feed_id
	 * @param int $feed_page
	 *
	 * @since 6.0.5
	 */
	public function set_mod_cache( $cache_seconds, $feed_id, $feed_page ) {
		$this->encryption = new SB_Instagram_Data_Encryption();
		$this->cache      = new SBI_Moderation_Mode_Cache( $feed_id, $feed_page, $cache_seconds );
		$this->feed_page  = $feed_page;
		$this->cache->retrieve_and_set();
	}

	/**
	 * We use the previous page's cache record to determine the URL needed for an API call
	 *
	 * @param int $cache_seconds
	 * @param string $feed_id
	 * @param int $feed_page
	 *
	 * @since 6.0.5
	 */
	public function set_previous_page_mod_cache( $cache_seconds, $feed_id, $feed_page ) {
		$this->encryption          = new SB_Instagram_Data_Encryption();
		$this->previous_page_cache = new SBI_Moderation_Mode_Cache( $feed_id, $feed_page, $cache_seconds );
		$this->previous_page_cache->retrieve_and_set();
	}

	/**
	 * Whether or not we have a previous page cache that hasn't expired
	 *
	 * @return bool
	 *
	 * @since 6.0.5
	 */
	public function previous_page_cache_exists() {
		return ! $this->previous_page_cache->is_expired( 'posts' );
	}

	/**
	 * We don't want the post data but the data related to the next page of the API
	 *
	 * @param array $atts
	 *
	 * @since 6.0.5
	 */
	public function set_next_page_data_from_previous_page_cache( $atts = array() ) {
		$posts_json = $this->previous_page_cache->get( 'posts' );

		$posts_data = json_decode( $posts_json, true );

		if ( $posts_data ) {
			$this->next_pages    = isset( $posts_data['pagination'] ) ? $posts_data['pagination'] : array();
			$this->pages_created = $this->feed_page + 99;
			$this->post_data     = array();
		}
	}
}
