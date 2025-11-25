<?php

trait GWAPI_Wiz_Bundle_Upgrade {
	/**
	 * Check if a license response indicates an upgrade to Wiz Bundle.
	 * If detected, performs the upgrade and returns bundle license data.
	 *
	 * @param array $license_data The license data response
	 * @param string $from_type The product type being checked (perk or connect)
	 * @return array|false Bundle license data if upgrade detected, false otherwise
	 */
	public function check_and_handle_wiz_bundle_upgrade( $license_data, $from_type ) {
		// Only handle mismatch errors for perk/connect types
		if ( $license_data['license'] !== 'item_name_mismatch' ||
			! in_array( $from_type, [ 'perk', 'connect' ] ) ) {
			return false;
		}

		$returned_item_name = $license_data['item_name'] ?? '';

		// Check if this is an upgrade to Wiz Bundle (decode URL encoding)
		if ( urldecode( $returned_item_name ) !== 'Wiz Bundle' ) {
			return false;
		}

		// Get the license key being checked
		$license_key = $this->get_license_key( $from_type );
		if ( ! $license_key ) {
			return false;
		}

		// Perform the upgrade
		$this->handle_wiz_bundle_upgrade( $license_key, $from_type );

		// Return the fresh bundle license data
		return $this->get_license_data( 'wiz-bundle', true );
	}

	/**
	 * Handle upgrading from GP or GC to Wiz Bundle.
	 * Deactivates both GP and GC licenses and sets up the bundle license.
	 *
	 * @param string $license_key The license key being upgraded
	 * @param string $from_type The original product type (perk or connect)
	 * @return bool Whether upgrade was successful
	 */
	public function handle_wiz_bundle_upgrade( $license_key, $from_type ) {
		// Deactivate GP license if it exists
		$gp_key = $this->get_perk_license_key();
		if ( $gp_key ) {
			$this->deactivate_license( self::PRODUCT_TYPE_PERK );
		}

		// Deactivate GC license if it exists
		$gc_key = $this->get_connect_license_key();
		if ( $gc_key ) {
			$this->deactivate_license( self::PRODUCT_TYPE_CONNECT );
		}

		// Set the Wiz Bundle license
		$this->set_wiz_bundle_license_key( $license_key );

		// Flush all license caches
		$this->flush_perk_license_info();
		$this->flush_connect_license_info();
		$this->flush_wiz_bundle_license_info();

		return true;
	}
}
