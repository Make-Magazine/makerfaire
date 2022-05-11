<?php
/**
 * class SBI_Moderation_Mode_Cache
 *
 * @since 6.0.5
 */
use InstagramFeed\SB_Instagram_Data_Encryption;

class SBI_Moderation_Mode_Cache extends SB_Instagram_Cache {

	protected $suffix;

	/**
	 * SBI_Cache constructor. Set the feed id, cache key, legacy
	 *
	 * @param string $feed_id
	 * @param int $page
	 * @param int $cache_time
	 *
	 * @since 6.0.5
	 */
	public function __construct( $feed_id, $page = 1, $cache_time = 0 ) {
		$this->cache_time = (int) $cache_time;
		$this->is_legacy  = false;
		$this->page       = $page;
		$this->feed_id    = str_replace( '*', '', $feed_id );

		$additional_suffix  = '_CUSTOMIZER_MODMODE';
		$offset             = $this->page > 1 ? $this->page : '';
		$additional_suffix .= $offset;
		$this->feed_id     .= $additional_suffix;
		$this->encryption   = new SB_Instagram_Data_Encryption();
	}

	/**
	 * Set all caches based on available data.
	 */
	public function retrieve_and_set() {
		$existing_caches = $this->query_sbi_feed_caches();

		foreach ( $existing_caches as $cache ) {
			switch ( $cache['cache_key'] ) {
				case 'posts':
					$this->posts = $cache['cache_value'];
					break;
				case 'posts' . $this->suffix:
					$this->posts_page = $cache['cache_value'];
					break;
			}
		}
	}

	/**
	 * Whether or not the cache needs to be refreshed
	 *
	 * @param string $cache_type
	 *
	 * @return bool
	 *
	 * @since 6.0.5
	 */
	public function is_expired( $cache_type = 'posts' ) {
		return empty( $this->posts );
	}

	/**
	 * @return array|mixed
	 */
	public function get_customizer_cache() {
		if ( strpos( $this->feed_id, '_CUSTOMIZER' ) === false ) {
			$feed_id = $this->feed_id . '_CUSTOMIZER';
		} else {
			$feed_id = $this->feed_id;
		}
		global $wpdb;
		$cache_table_name = $wpdb->prefix . 'sbi_feed_caches';

		$sql     = $wpdb->prepare(
			"
			SELECT * FROM $cache_table_name
			WHERE feed_id = %s
			AND cache_key = 'posts'",
			$feed_id
		);
		$results = $wpdb->get_results( $sql, ARRAY_A );

		$return = array();
		if ( ! empty( $results[0] ) ) {
			$return = $this->maybe_decrypt( $results[0]['cache_value'] );
			$return = json_decode( $return, true );

			$return = isset( $return['data'] ) ? $return['data'] : array();
		}

		return $return;
	}

	/**
	 * Get all available caches from the sbi_cache table.
	 *
	 * @return array
	 *
	 * @since 6.0.5
	 */
	protected function query_sbi_feed_caches() {
		global $wpdb;
		$cache_table_name = $wpdb->prefix . 'sbi_feed_caches';

		if ( $this->page === 1 ) {
			$sql = $wpdb->prepare(
				"
			SELECT * FROM $cache_table_name
			WHERE feed_id = %s",
				$this->feed_id
			);
		} else {
			$sql = $wpdb->prepare(
				"
			SELECT * FROM $cache_table_name
			WHERE feed_id = %s
			AND cache_key IN ( 'posts', %s, %s, %s )",
				$this->feed_id,
				'posts_' . $this->page,
				'resized_images_' . $this->page,
				'meta_' . $this->page
			);
		}

		$feed_cache = $wpdb->get_results( $sql, ARRAY_A );

		return $feed_cache;
	}
}
