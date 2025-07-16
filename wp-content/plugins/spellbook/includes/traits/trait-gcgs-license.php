<?php

/**
 * Trait for handling GC Google Sheets license eligibility through Gravity Perks.
 */
trait GWAPI_GCGS_License {
	/**
	 * Check if we have a valid GP license that's eligible for GCGS.
	 * Used by get_product_type_from_file() to determine if GCGS should use the Perk license.
	 */
	public function has_gcgs_gp_license() {
		$license_data = $this->get_license_data( self::PRODUCT_TYPE_PERK );
		return isset( $license_data['valid'] ) &&
			$license_data['valid'] &&
			$this->is_gp_license_gcgs_eligible( $license_data );
	}

	/**
	 * Checks if a Gravity Perks license is GCGS eligible.
	 *
	 * @param array $license_data The license data.
	 *
	 * @return bool
	 */
	public function is_gp_license_gcgs_eligible( $license_data ) {
		return ! ! rgar( $license_data, 'gcgs_eligible' );
	}
}
