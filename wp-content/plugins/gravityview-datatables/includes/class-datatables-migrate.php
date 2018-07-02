<?php
/**
 * GravityView Migrate Class - where awesome features become even better, seamlessly!
 *
 * @package   GravityView
 * @author    Zack Katz <zack@katzwebservices.com>
 * @license   ToBeDefined
 * @link      http://www.katzwebservices.com
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @since 1.2
 */


class GV_Extension_DataTables_Migrate {

	function __construct() {
		add_action( 'admin_init', array( $this, 'update_settings' ), 1 );
	}

	public function update_settings() {

		$this->maybe_migrate_tabletools_settings();

	}

	/**
	 * @since 2.0
	 */
	private function maybe_migrate_tabletools_settings() {

		// check if tabletools migration is already performed
		$is_updated = get_option( 'gv_migrate_dt_tabletools' );

		if ( ! $is_updated ) {
			$this->update_tabletools_settings();
		}
	}

	function update_tabletools_settings() {

		// Loop through all the views
		$query_args = array(
			'post_type' => 'gravityview',
			'post_status' => 'any',
			'posts_per_page' => -1,
		);

		$views = get_posts( $query_args );

		foreach( $views as $view ) {

			$previous_settings = get_post_meta( $view->ID, '_gravityview_datatables_settings', true );

			if( false === $previous_settings ) {
				continue;
			}

			// Backup previous settings, just out of an abundance of caution
			add_post_meta( $view->ID, '_gravityview_datatables_settings_bak', $previous_settings, true );

			$new_settings = array(
				'buttons' => rgar( $previous_settings, 'tabletools' ),
				'scroller' => rgar( $previous_settings, 'scroller' ),
				'scrolly' => rgar( $previous_settings, 'scrolly' ),
				'fixedheader' => rgar( $previous_settings, 'fixedheader' ),
				'fixedcolumns' => rgar( $previous_settings, 'fixedcolumns' ),
				'responsive' => rgar( $previous_settings, 'responsive' ),
				'export_buttons' => array(
					'copy' => rgars( $previous_settings, 'tt_buttons/copy' ),
					'csv' => rgars( $previous_settings, 'tt_buttons/csv' ),
					'pdf' => rgars( $previous_settings, 'tt_buttons/pdf' ),
					'print' => rgars( $previous_settings, 'tt_buttons/print' ),
					'excel' => rgars( $previous_settings, 'tt_buttons/xls' ),
				),
			);

			unset( $new_settings['tabletools'], $new_settings['tt_buttons'], $new_settings['export_buttons']['xls'] );

			// update datatables settings on the view
			update_post_meta( $view->ID, '_gravityview_datatables_settings', $new_settings );

			gravityview()->log->debug(  __METHOD__ . ': updating view #' . $view->ID . ' settings:', array( 'data' => $new_settings ) );

		} // foreach Views

		unset( $new_settings, $previous_settings );

		// all done! enjoy the new DataTables!
		update_option( 'gv_migrate_dt_tabletools', GV_Extension_DataTables::version );

		gravityview()->log->debug( __METHOD__ . ': All done! enjoy DataTables!' );
	}

} // end class

new GV_Extension_DataTables_Migrate;
