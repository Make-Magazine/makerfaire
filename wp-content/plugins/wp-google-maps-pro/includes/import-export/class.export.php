<?php
namespace WPGMZA;

/**
 * Export abstracted class
 *
 * @since 9.0.0
 */
#[\AllowDynamicProperties]
abstract class Export {
	/**
	 * Constructor
	*/
	public function __construct(){

	}

	/**
	 * Download the export in the preferred format
	*/
	abstract function download();

	/**
	 * Sanitize map ids. (SHOULD BE ABSTRACTED)
	 *
	 * @param array $maps Integer array of map ids.
	 * @return array Integer array of map ids.
	*/
	protected function sanitize_map_ids( $maps ) {
		$map_count = count( $maps );
		for ( $i = 0; $i < $map_count; $i++ ) {
			if ( ! is_numeric( $maps[ $i ] ) ) {
				unset( $maps[ $i ] );
				continue;
			}
			$maps[ $i ] = absint( $maps[ $i ] );
			if ( $maps[ $i ] < 1 ) {
				unset( $maps[ $i ] );
			}
		}
		return $maps;
	}
}