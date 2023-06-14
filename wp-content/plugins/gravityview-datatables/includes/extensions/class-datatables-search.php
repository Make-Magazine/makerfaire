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

		$view = gravityview()->views->get( $view_id );

		if ( ! $view ) {
			return $dt_config;
		}

		/**
		 * Disable built-in DT search if a search widget is present.
		 */
		foreach ( $view->widgets->all() as $widget ) {
			if ( $widget instanceof GravityView_Widget_Search ) {
				// Remove built-in search ("f") from DOM setting.
				// @see https://datatables.net/reference/option/dom
				$dt_config['dom'] = empty( $dt_config['dom'] ) ? 'lrtip' : str_replace( 'f', '', $dt_config['dom'] );
				break;
			}
		}

		return $dt_config;
	}
}

new GV_Extension_DataTables_Search;
