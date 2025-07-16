<?php

trait GWAPI_Shop_License {
    /**
     * Get the Shop license key.
     * Checks in order:
     * 1. SPELLBOOK_KEY_SHOP constant
     * 2. gs_license option
     * 3. gspc_license legacy option
     *
     * @return string|null The license key or null if not found
     */
    public function get_shop_license_key() {
        // Check for constant first
        if ( defined( 'SPELLBOOK_KEY_SHOP' ) ) {
            return trim( SPELLBOOK_KEY_SHOP );
        }

        // Try current option
        $license = get_site_option( 'gs_license' );
        if ( isset( $license['key'] ) ) {
            return trim( $license['key'] );
        }

        // Try legacy option
        $legacy_license = get_site_option( 'gspc_license' );
        if ( isset( $legacy_license['key'] ) ) {
            return trim( $legacy_license['key'] );
        }

        return null;
    }

    /**
     * Set the Shop license key.
     *
     * @param string $key The license key to set
     * @return bool Whether the key was successfully set
     */
    public function set_shop_license_key( $key ) {
        if ( defined( 'SPELLBOOK_KEY_SHOP' ) ) {
            return false; // Can't override constant
        }

        $license = array(
            'key' => trim( $key ),
            'id' => 0 // Will be updated by API response
        );

        // Remove legacy option
        delete_site_option( 'gspc_license' );

        return update_site_option( 'gs_license', $license );
    }

    /**
     * Remove the Shop license key.
     * Removes both current and legacy options.
     *
     * @return bool Whether the key was successfully removed
     */
    public function remove_shop_license_key() {
		// Flush the license info transient
		$this->flush_shop_license_info();

        $current = delete_site_option( 'gs_license' );
        $legacy = delete_site_option( 'gspc_license' );
        return $current && $legacy;
    }

	/**
	 * Flush the Shop license info transient(s) so it can be re-fetched.
	 *
	 * @return void
	 */
	public function flush_shop_license_info() {
		delete_site_transient( 'gwapi_license_data_shop_' . SPELLBOOK_VERSION );
		$this->flush_products();
	}
}
