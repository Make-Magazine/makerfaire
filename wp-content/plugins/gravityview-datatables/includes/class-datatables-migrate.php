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

			// get_post_meta() with $single=true returns '' for a View with no meta, never
			// `false`, so this must check for "nothing to migrate" directly rather than
			// comparing against a value the function can never return.
			if ( ! is_array( $previous_settings ) || empty( $previous_settings ) ) {
				continue;
			}

			// A View with no TableTools keys has nothing for this migration to convert. This
			// also protects a View that already carries modern settings (field filters,
			// processing mode, RowGroup, etc.) from being clobbered if the migration re-runs.
			if ( ! isset( $previous_settings['tabletools'] ) && ! isset( $previous_settings['tt_buttons'] ) ) {
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
			);

			// Converts only the legacy keys that are actually present, onto whatever modern
			// `export_buttons` map the View already carries. `rgars()` answers a missing key
			// with '' rather than null, so reading all five unconditionally yields a full
			// array of '' that the array_filter() below cannot drop (it is an array, not
			// null) and array_merge() then writes over the View's real export_buttons.
			$legacy_buttons = isset( $previous_settings['tt_buttons'] ) && is_array( $previous_settings['tt_buttons'] )
				? $previous_settings['tt_buttons']
				: array();

			if ( $legacy_buttons ) {
				$converted = array();

				foreach ( array( 'copy' => 'copy', 'csv' => 'csv', 'pdf' => 'pdf', 'print' => 'print', 'excel' => 'xls' ) as $modern_key => $legacy_key ) {
					if ( array_key_exists( $legacy_key, $legacy_buttons ) ) {
						$converted[ $modern_key ] = $legacy_buttons[ $legacy_key ];
					}
				}

				if ( $converted ) {
					$existing_buttons = isset( $previous_settings['export_buttons'] ) && is_array( $previous_settings['export_buttons'] )
						? $previous_settings['export_buttons']
						: array();

					$new_settings['export_buttons'] = array_merge( $existing_buttons, $converted );
				}
			}

			unset( $new_settings['tabletools'], $new_settings['tt_buttons'] );

			// Merges onto the previous settings instead of replacing them outright, so any
			// modern keys the migration doesn't know about survive, then drops the legacy keys
			// the merge just converted.
			$merged_settings = array_merge( $previous_settings, array_filter( $new_settings, static function ( $value ) {
				return null !== $value;
			} ) );

			unset( $merged_settings['tabletools'], $merged_settings['tt_buttons'] );

			// update datatables settings on the view
			update_post_meta( $view->ID, '_gravityview_datatables_settings', $merged_settings );

			gravityview()->log->debug(  __METHOD__ . ': updating view #' . $view->ID . ' settings:', array( 'data' => $merged_settings ) );

		} // foreach Views

		unset( $new_settings, $previous_settings );

		// all done! enjoy the new DataTables!
		update_option( 'gv_migrate_dt_tabletools', GV_Extension_DataTables::version );

		gravityview()->log->debug( __METHOD__ . ': All done! enjoy DataTables!' );
	}

} // end class

new GV_Extension_DataTables_Migrate;
