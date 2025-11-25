<?php

trait GWAPI_Wiz_Bundle_License {
    /**
     * Get the Wiz Bundle license key.
     * Checks in order:
     * 1. SPELLBOOK_KEY_WIZ_BUNDLE constant
     * 2. gwp_wiz_bundle_license option
     *
     * @return string|null The license key or null if not found
     */
    public function get_wiz_bundle_license_key() {
        // Check for constant first
        if ( defined( 'SPELLBOOK_KEY_WIZ_BUNDLE' ) ) {
            return trim( SPELLBOOK_KEY_WIZ_BUNDLE );
        }

        $license = get_site_option( 'gwp_wiz_bundle_license' );
        if ( isset( $license['key'] ) ) {
            return trim( $license['key'] );
        }
        return null;
    }

    /**
     * Set the Wiz Bundle license key.
     *
     * @param string $key The license key to set
     * @return bool Whether the key was successfully set
     */
    public function set_wiz_bundle_license_key( $key ) {
        if ( defined( 'SPELLBOOK_KEY_WIZ_BUNDLE' ) ) {
            return false; // Can't override constant
        }

        $license = array(
            'key' => trim( $key ),
            'id' => 0 // Will be updated by API response
        );
        return update_site_option( 'gwp_wiz_bundle_license', $license );
    }

    /**
     * Remove the Wiz Bundle license key.
     *
     * @return bool Whether the key was successfully removed
     */
    public function remove_wiz_bundle_license_key() {
        delete_site_option( 'gwp_wiz_bundle_license' );
        $this->flush_wiz_bundle_license_info();
        return true;
    }

    /**
     * Flush the Wiz Bundle license info transient(s) so it can be re-fetched.
     *
     * @return void
     */
    public function flush_wiz_bundle_license_info() {
        delete_site_transient( 'gwapi_license_data_wiz-bundle_' . SPELLBOOK_VERSION );
        $this->flush_products();
    }
}
