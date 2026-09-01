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

		/**
		 * Pods Gravity Forms add-on compatibility.
		 *
		 * Registered with the processor and batch objects so we can read the
		 * batch flags ("Ignore Required Form Fields") at run time.
		 */
		add_action( 'gravityview/import/processor/init', array( $this, 'pods_gf_compat' ), 10, 2 );
	}

	/**
	 * Keeps the Pods Gravity Forms add-on from breaking an import.
	 *
	 * Pods validates and saves its mapped fields through Gravity Forms submission hooks on
	 * every submission, independently of GravityImport's "Process Feeds" option and of Gravity
	 * Forms' own `isRequired` (which "Ignore Required Form Fields" already disables). Three
	 * things go wrong when GravityImport drives many submissions through a single request:
	 *
	 * 1. A mapped, Pods-required empty field makes Pods core call pods_error(), which during a
	 *    REST or AJAX request runs in "json" mode and ends the request with
	 *    wp_send_json( ..., 500 ) (an exit) that the Processor's per-row handler cannot catch,
	 *    failing the whole import with no row-level error and no log. `pods_error_mode` =>
	 *    "exception" makes Pods throw a catchable exception instead, so the error becomes a
	 *    per-row error and, with "Continue Processing If Errors Occur", the import continues.
	 * 2. That required-field check fires even when "Ignore Required Form Fields" is on.
	 *    `pods_api_handle_field_validation_check_required` => false extends that option's
	 *    promise to Pods-mapped fields so those rows actually import.
	 * 3. Pods saves to a Pod only once per PHP request: it records each handler it has run in
	 *    the per-form Pods_GF::$actioned static and removes its submission hook after the first
	 *    row. Because GravityImport submits every row in one request, only the first row would
	 *    reach Pods. Clearing that guard before each row (gform_pre_process) lets Pods save
	 *    every row in its default "validation" priority mode. The rarer "submission" priority
	 *    mode also depends on a submission hook Pods removes after the first row, which this
	 *    does not re-arm.
	 *
	 * All are scoped to the import: a fresh Processor (and this action) fires per import
	 * request, so they do not affect normal front-end submissions.
	 *
	 * @since 2.11.3
	 *
	 * @param \GravityKit\GravityImport\Processor $processor The initialized processor instance.
	 * @param array                               $args      The processor arguments (includes batch_id).
	 */
	public function pods_gf_compat( $processor, $args ) {
		// No-op unless the Pods Gravity Forms add-on is active.
		if ( ! class_exists( 'Pods_GF' ) ) {
			return;
		}

		add_filter( 'pods_error_mode', array( $this, 'force_pods_exception_mode' ) );

		// Reset Pods' per-request "already ran" guard before each row so its save-to-Pod work
		// fires for every submission, not just the first one in the request.
		add_filter( 'gform_pre_process', array( $this, 'reset_pods_gf_actioned_per_row' ) );

		$batch = isset( $args['batch_id'] ) ? Batch::get( $args['batch_id'] ) : null;

		// The "require" flag is present only when "Ignore Required Form Fields" is OFF
		// (see Processor::tick()). When it is absent, honor that choice for Pods too.
		if ( $batch && ! in_array( 'require', (array) $batch['flags'], true ) ) {
			add_filter( 'pods_api_handle_field_validation_check_required', '__return_false' );
		}
	}

	/**
	 * Resets the Pods Gravity Forms add-on's per-request guard before each imported row.
	 *
	 * Pods_GF::$actioned records, per form, which of its submission handlers have already run
	 * in the current PHP request, so its default "validation" priority save to a Pod fires only
	 * for the first submission. GravityImport processes every row in one request, so that guard
	 * would skip every row after the first. gform_pre_process fires at the start of each
	 * process_form(), before validation, so clearing the form's entry here lets Pods save each
	 * row. Hooked only during an import (see pods_gf_compat()).
	 *
	 * @since 2.11.3
	 *
	 * @param array $form The Gravity Forms form being processed.
	 *
	 * @return array The unmodified form (gform_pre_process is a filter).
	 */
	public function reset_pods_gf_actioned_per_row( $form ) {
		if ( class_exists( 'Pods_GF' ) && isset( $form['id'] ) && is_array( \Pods_GF::$actioned ) ) {
			unset( \Pods_GF::$actioned[ $form['id'] ] );
		}

		return $form;
	}

	/**
	 * Forces Pods to raise a catchable exception instead of exiting the request.
	 *
	 * @since 2.11.3
	 *
	 * @param string $error_mode The current Pods error mode.
	 *
	 * @return string Always "exception".
	 */
	public function force_pods_exception_mode( $error_mode ) {
		return 'exception';
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