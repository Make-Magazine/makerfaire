<?php

trait GWAPI_Connect_License {
    /**
     * Get the Connect license key.
     * Checks in order:
     * 1. SPELLBOOK_KEY_CONNECT constant
     * 2. gc_license option
     *
     * @return string|null The license key or null if not found
     */
    public function get_connect_license_key() {
        // Check for constant first
        if ( defined( 'SPELLBOOK_KEY_CONNECT' ) ) {
            return trim( SPELLBOOK_KEY_CONNECT );
        }

        $license = get_site_option( 'gc_license' );
        if ( isset( $license['key'] ) ) {
            return trim( $license['key'] );
        }
        return null;
    }

    /**
     * Set the Connect license key.
     *
     * @param string $key The license key to set
     * @return bool Whether the key was successfully set
     */
    public function set_connect_license_key( $key ) {
        if ( defined( 'SPELLBOOK_KEY_CONNECT' ) ) {
            return false; // Can't override constant
        }

        $license = array(
            'key' => trim( $key ),
            'id' => 0 // Will be updated by API response
        );
        return update_site_option( 'gc_license', $license );
    }

    /**
     * Remove the Connect license key.
     *
     * @return bool Whether the key was successfully removed
     */
    public function remove_connect_license_key() {
        delete_site_option( 'gc_license' );
		$this->flush_connect_license_info();
		return true;
    }

	/**
	 * Flush the Connect license info transient(s) so it can be re-fetched.
	 *
	 * @return void
	 */
	public function flush_connect_license_info() {
		delete_site_transient( 'gwapi_license_data_connect_' . SPELLBOOK_VERSION );
		$this->flush_products();
	}
}
