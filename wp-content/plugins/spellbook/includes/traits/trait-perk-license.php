<?php

trait GWAPI_Perk_License {
    /**
     * Get the Perk license key.
     * Checks in order:
	 * 1. SPELLBOOK_KEY_PERKS constant
     * 2. GPERKS_LICENSE_KEY legacy constant
     * 3. gwp_settings option
     *
     * @return string|null The license key or null if not found
     */
    public function get_perk_license_key() {
		// Check for constant first
		if ( defined( 'SPELLBOOK_KEY_PERKS' ) ) {
			return trim( SPELLBOOK_KEY_PERKS );
		}

        // Check for legacy constant second
        if ( defined( 'GPERKS_LICENSE_KEY' ) && GPERKS_LICENSE_KEY ) {
            return trim( GPERKS_LICENSE_KEY );
        }

        // Check main Gravity Perks settings
        $settings = get_site_option( 'gwp_settings' );
        if ( isset( $settings['license_key'] ) ) {
            return trim( $settings['license_key'] );
        }

        return null;
    }

    /**
     * Set the Perk license key.
     *
     * @param string $key The license key to set
     * @return bool Whether the key was successfully set
     */
    public function set_perk_license_key( $key ) {
        if ( defined( 'GPERKS_LICENSE_KEY' ) ) {
            return false; // Can't override legacy constant
        }

		if ( defined( 'SPELLBOOK_KEY_PERKS' ) ) {
			return false; // Can't override constant
		}

        $settings = get_site_option( 'gwp_settings', array() );
        $settings['license_key'] = trim( $key );

        return update_site_option( 'gwp_settings', $settings );
    }

    /**
     * Remove the Perk license key.
     *
     * @return bool Whether the key was successfully removed
     */
    public function remove_perk_license_key() {
		$this->flush_perk_license_info();

        if ( defined( 'GPERKS_LICENSE_KEY' ) ) {
            return false; // Can't remove legacy constant
        }

		if ( defined( 'SPELLBOOK_KEY_PERKS' ) ) {
			return false; // Can't remove constant
		}

        $settings = get_site_option( 'gwp_settings', array() );
        unset( $settings['license_key'] );

        return update_site_option( 'gwp_settings', $settings );
    }

	/**
	 * Flush the Perk license info transient(s) so it can be re-fetched.
	 *
	 * @return void
	 */
	public function flush_perk_license_info() {
		delete_site_transient( 'gwapi_license_data_perk_' . SPELLBOOK_VERSION );
		$this->flush_products();
	}
}
