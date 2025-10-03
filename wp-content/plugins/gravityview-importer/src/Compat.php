<?php

namespace GravityKit\GravityImport;

class Compat {
	public function __construct() {
		/**
		 * Do only when processing via the Processor...
		 */
		add_action( 'gravityview/import/processor/init', function () {
			/**
			 * Evade the zero-spam plugin.
			 * https://github.com/gravityview/Import-Entries/issues/329
			 */
			if ( function_exists( 'zerospam_get_key' ) ) {
				add_action( 'gform_pre_submission', function () {
					$_POST['zerospam_key'] = zerospam_get_key(); // Inject the correct key ;)
				}, 1 );
			}

			// Imported files shall never be considered spam!
			add_filter( 'gform_entry_is_spam', '__return_false', 1000 );

			/**
			 * Akismet should not be spammed.
			 * https://github.com/gravityview/Import-Entries/issues/331
			 */
			add_filter( 'gform_akismet_enabled', '__return_false' );

			/**
			 * Invisible reCaptcha should be ignored.
			 * https://github.com/gravityview/Import-Entries/issues/336
			 */
			add_filter( 'google_invre_is_gf_excluded', '__return_true' );

			/**
			 * Convert text approval status values to numeric values.
			 * https://github.com/GravityKit/GravityImport/issues/482
			 */
			add_filter( 'gravityview/import/column/data', array( $this, 'convert_approval_status_to_numeric' ), 10, 3 );
		} );
	}

	/**
	 * Convert text approval status values to numeric values during import.
	 *
	 * When importing into the Approval Status meta field, GravityView expects numeric values:
	 * - 1 = Approved
	 * - 2 = Disapproved
	 * - 3 = Unapproved
	 *
	 * This method converts text values (e.g., "Approved") to their numeric equivalents,
	 * respecting WordPress translations.
	 *
	 * @since 2.7.0
	 *
	 * @param mixed $data   The cell data.
	 * @param array $column The column schema definition.
	 * @param array $batch  The batch.
	 *
	 * @return mixed The potentially converted data.
	 */
	public function convert_approval_status_to_numeric( $data, $column, $batch ) {
		// Only process the is_approved field.
		if ( 'is_approved' !== $column['field'] ) {
			return $data;
		}

		// Skip if already numeric.
		if ( is_numeric( $data ) ) {
			return $data;
		}

		// Trim and convert to lowercase for comparison.
		$normalized_value = Core::strtolower( trim( $data ) );

		// Get WordPress-translated approval status values.
		$approved_text    = Core::strtolower( __( 'Approved', 'gk-gravityimport' ) );
		$disapproved_text = Core::strtolower( __( 'Disapproved', 'gk-gravityimport' ) );
		$unapproved_text  = Core::strtolower( __( 'Unapproved', 'gk-gravityimport' ) );

		// Convert text values to numeric.
		if ( $normalized_value === $approved_text ) {
			return '1';
		} elseif ( $normalized_value === $disapproved_text ) {
			return '2';
		} elseif ( $normalized_value === $unapproved_text ) {
			return '3';
		}

		// Return original value if no match.
		return $data;
	}
}