<?php
/**
 * Searching.
 */
class GV_Extension_DataTables_Search extends GV_DataTables_Extension {
	
	public $settings_key = 'search';

	/**
     * Unused.
	 */
	public function settings_row( $ds ) {}

	/**
	 * Add specific DataTables configuration options to the JS call.
	 *
	 * @param array $dt_config The existing options.
	 * @param int $view_id The View.
	 * @param WP_Post $post The post.
	 *
	 * @return array The modified options.
	 */
	public function maybe_add_config( $dt_config, $view_id, $post  ) {

	    $dt_config['searching'] = true;

		/**
		 * Do not show if a search widget is present.
		 */
		if ( $view = gravityview()->views->get( $view_id ) ) {
			foreach ( $view['widgets'] as $position ) {
				foreach ( $position as $widget ) {
					if ( 'search_bar' === $widget['id'] ) {
						$dt_config['searching'] = false;
						break 2;
					}
				}
			}
		}

		return $dt_config;
	}
}

new GV_Extension_DataTables_Search;
