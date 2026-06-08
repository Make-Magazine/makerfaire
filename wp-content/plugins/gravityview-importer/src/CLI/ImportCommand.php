<?php

namespace GravityKit\GravityImport\CLI;

use GFAPI;
use GravityKit\GravityImport\Batch;
use GravityKit\GravityImport\ImportProfile;
use GravityKit\GravityImport\Processor;
use WP_CLI;
use WP_CLI\Utils;
use WP_CLI_Command;

/**
 * GravityImport CLI commands for headless CSV imports.
 *
 * @since 2.11.0
 */
class ImportCommand extends WP_CLI_Command {
	/**
	 * Imports CSV data using a saved import profile.
	 *
	 * ## OPTIONS
	 *
	 * --file=<path>
	 * : Path to the CSV file to import.
	 *
	 * --profile=<path>
	 * : Path to the import profile JSON file.
	 *
	 * [--form-id=<id>]
	 * : Override the form ID from the profile.
	 *
	 * [--force]
	 * : Skip form hash mismatch warning.
	 *
	 * [--dry-run]
	 * : Validate and summarize the import without creating a batch.
	 *
	 * [--format=<format>]
	 * : Output format. One of: table, json, csv, yaml. Default: table.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk import run --file=data.csv --profile=profile.json
	 *     wp gk import run --file=data.csv --profile=profile.json --form-id=7
	 *     wp gk import run --file=data.csv --profile=profile.json --dry-run
	 *
	 * @since 2.11.0
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Named arguments.
	 */
	public function run( $args, $assoc_args ) {
		$format       = $this->get_format( $assoc_args );
		$file         = $this->get_required_csv_file( $assoc_args, $format );
		$profile     = $this->load_profile_from_args( $assoc_args, $format );
		$form_id     = $this->get_form_id( $profile, $assoc_args );
		$form        = $this->get_form( $form_id, $format );
		$force       = isset( $assoc_args['force'] );
		$dry_run     = isset( $assoc_args['dry-run'] );
		$profile     = $this->validate_profile_against_form( $profile, $form, $format );
		$hash_status = $this->check_form_hash( $profile, $form, $force, null, $format );
		$csv_headers = $this->read_csv_headers( $file, $format );
		$match       = ImportProfile::match_columns( $profile['columns'] ?? [], $csv_headers );

		if ( 'table' === $format && ! empty( $match['unmatched'] ) ) {
			$count = count( $match['unmatched'] );

			WP_CLI::warning( sprintf( __( '%d profile column(s) could not be matched to the CSV.', 'gk-gravityimport' ), $count ) );
		}

		$schema = ImportProfile::remap_schema( $profile['schema'], $match['matched'] );

		if ( empty( $schema ) ) {
			$this->emit_error( $format, 'schema_unmappable', __( 'No schema rules could be mapped to the CSV columns.', 'gk-gravityimport' ) );
		}

		$batch_args = $this->build_batch_args( $file, $form_id, $schema, $profile );

		if ( $dry_run ) {
			return $this->display_dry_run_summary( $format, $form, $profile, $csv_headers, $match, $schema, $batch_args, $hash_status );
		}

		if ( 'table' === $format ) {
			WP_CLI::log( sprintf( __( 'Creating import batch for Form #%d...', 'gk-gravityimport' ), (int) $form_id ) );
		}

		$batch = Batch::create( $batch_args );

		if ( is_wp_error( $batch ) ) {
			$this->emit_error( $format, 'batch_create_failed', $batch->get_error_message() );
		}

		$batch_id = $batch['id'];

		if ( 'table' === $format ) {
			WP_CLI::log( sprintf( __( 'Batch #%d created. Processing...', 'gk-gravityimport' ), (int) $batch_id ) );
		}

		$processor = new Processor( [ 'batch_id' => $batch_id ] );
		$last_status = '';

		do {
			$processor->run();
			$current = Batch::get( $batch_id );

			if ( is_wp_error( $current ) ) {
				$this->emit_error( $format, 'batch_runtime_error', $current->get_error_message() );
			}

			$status = $current['status'];

			if ( 'table' === $format && $status !== $last_status ) {
				WP_CLI::log( sprintf( __( 'Status: %s', 'gk-gravityimport' ), $status ) );
				$last_status = $status;
			}

			if ( 'table' === $format && 'processing' === $status ) {
				$processed = (int) ( $current['progress']['processed'] ?? 0 );
				$total     = (int) ( $current['progress']['total'] ?? 0 );

				if ( $total > 0 ) {
					WP_CLI::log( sprintf( __( 'Processed %1$d of %2$d rows...', 'gk-gravityimport' ), $processed, $total ) );
				}

				usleep( 100000 );
			}
		} while ( ! in_array( $status, [ 'done', 'error', 'rolledback' ], true ) );

		$final = Batch::get( $batch_id );

		if ( is_wp_error( $final ) ) {
			$this->emit_error( $format, 'batch_runtime_error', $final->get_error_message() );
		}

		$total    = (int) ( $final['progress']['total'] ?? 0 );
		$imported = (int) ( $final['progress']['processed'] ?? 0 );
		$errors   = (int) ( $final['progress']['error'] ?? 0 );
		$skipped  = (int) ( $final['progress']['skipped'] ?? 0 );

		if ( 'table' !== $format && 'done' === $final['status'] ) {
			return $this->display_run_summary( $format, $batch_id, $final['status'], $total, $imported, $skipped, $errors, $final['error'] ?? '' );
		}

		if ( 'done' === $final['status'] ) {
			if ( $errors > 0 ) {
				WP_CLI::warning( sprintf( __( 'Import finished with errors: %1$d imported, %2$d skipped, %3$d rows had errors out of %4$d rows.', 'gk-gravityimport' ), $imported, $skipped, $errors, $total ) );
			} elseif ( $skipped > 0 ) {
				WP_CLI::success( sprintf( __( 'Import complete: %1$d entries imported, %2$d skipped from %3$d rows.', 'gk-gravityimport' ), $imported, $skipped, $total ) );
			} else {
				WP_CLI::success( sprintf( __( 'Import complete: %1$d entries imported from %2$d rows.', 'gk-gravityimport' ), $imported, $total ) );
			}
		} elseif ( ! empty( $final['error'] ) ) {
			$this->emit_error( $format, 'batch_runtime_error', sprintf( __( 'Import ended with status: %1$s. %2$d rows had errors. Last error: %3$s', 'gk-gravityimport' ), $final['status'], $errors, $final['error'] ) );
		} else {
			$this->emit_error( $format, 'batch_runtime_error', sprintf( __( 'Import ended with status: %1$s. %2$d rows had errors.', 'gk-gravityimport' ), $final['status'], $errors ) );
		}
	}

	/**
	 * Validates a profile against a CSV file without importing.
	 *
	 * ## OPTIONS
	 *
	 * --file=<path>
	 * : Path to the CSV file.
	 *
	 * --profile=<path>
	 * : Path to the import profile JSON file.
	 *
	 * [--form-id=<id>]
	 * : Override the form ID from the profile.
	 *
	 * [--format=<format>]
	 * : Output format. One of: table, json, csv, yaml. Default: table.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk import validate --file=data.csv --profile=profile.json
	 *
	 * @since 2.11.0
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Named arguments.
	 */
	public function validate( $args, $assoc_args ) {
		$format       = $this->get_format( $assoc_args );
		$file         = $this->get_required_csv_file( $assoc_args, $format );
		$profile     = $this->load_profile_from_args( $assoc_args, $format );
		$form_id     = $this->get_form_id( $profile, $assoc_args );
		$form        = $this->get_form( $form_id, $format );
		$warnings    = [];
		$profile     = $this->validate_profile_against_form( $profile, $form, $format, $warnings );
		$hash_status = $this->check_form_hash( $profile, $form, true, __( 'Form structure has changed since profile was saved.', 'gk-gravityimport' ), $format );
		$csv_headers = $this->read_csv_headers( $file, $format );
		$match       = ImportProfile::match_columns( $profile['columns'] ?? [], $csv_headers );
		$schema      = ImportProfile::remap_schema( $profile['schema'], $match['matched'] );
		$summary     = [
			'profile_valid'     => true,
			'form_id'           => (int) $form_id,
			'form_title'        => $form['title'],
			'form_hash'         => ImportProfile::compute_form_hash( $form ),
			'hash_status'       => $hash_status,
			'columns_matched'   => count( $match['matched'] ),
			'columns_unmatched' => count( $match['unmatched'] ),
			'schema_rules'      => count( $schema ),
			'warnings'          => array_map( function( $warning ) {
				return $warning['message'];
			}, $warnings ),
		];

		if ( 'table' !== $format ) {
			$this->format_items( $format, [ $summary ], array_keys( $summary ) );

			return;
		}

		WP_CLI::log( __( 'Profile structure is valid.', 'gk-gravityimport' ) );
		WP_CLI::log( sprintf( __( 'Form #%1$d exists: %2$s', 'gk-gravityimport' ), (int) $form_id, $form['title'] ) );

		if ( 'match' === $hash_status ) {
			WP_CLI::log( __( 'Form structure matches profile.', 'gk-gravityimport' ) );
		}

		WP_CLI::log(
			sprintf(
				__( 'Column matching: %1$d matched, %2$d unmatched.', 'gk-gravityimport' ),
				count( $match['matched'] ),
				count( $match['unmatched'] )
			)
		);
		WP_CLI::log( sprintf( __( '%d schema rules mapped successfully.', 'gk-gravityimport' ), count( $schema ) ) );
		WP_CLI::success( __( 'Profile validation complete.', 'gk-gravityimport' ) );
	}

	/**
	 * Resolves the requested output format.
	 *
	 * @since 2.11.0
	 *
	 * @param array $assoc_args Named command arguments.
	 *
	 * @return string Output format.
	 */
	private function get_format( $assoc_args ) {
		$format = $assoc_args['format'] ?? 'table';
		$allowed = [ 'table', 'json', 'csv', 'yaml' ];

		if ( ! in_array( $format, $allowed, true ) ) {
			$this->emit_error( $format, 'invalid_format', sprintf( __( 'Invalid format: %s', 'gk-gravityimport' ), $format ) );
		}

		return $format;
	}

	/**
	 * Emits a command error in the requested output format.
	 *
	 * @since 2.11.0
	 *
	 * @param string $format  Output format.
	 * @param string $code    Stable error code.
	 * @param string $message Error message.
	 */
	private function emit_error( $format, $code, $message ) {
		if ( in_array( $format, [ 'json', 'csv', 'yaml' ], true ) ) {
			$this->format_items( $format, [
				[
					'status'  => 'error',
					'code'    => $code,
					'message' => $message,
				],
			], [ 'status', 'code', 'message' ] );

			WP_CLI::halt( 1 );
		}

		WP_CLI::error( $message );
	}

	/**
	 * Formats items and adds the missing trailing newline for structured formats.
	 *
	 * @since 2.11.0
	 *
	 * @param string $format Output format.
	 * @param array  $items  Items to format.
	 * @param array  $fields Fields to include.
	 */
	private function format_items( $format, $items, $fields ) {
		Utils\format_items( $format, $items, $fields );

		if ( 'table' !== $format ) {
			WP_CLI::line( '' );
		}
	}

	/**
	 * Gets the required CSV file argument.
	 *
	 * @since 2.11.0
	 *
	 * @param array $assoc_args Named command arguments.
	 * @param string $format     Output format.
	 *
	 * @return string Resolved CSV file path.
	 */
	private function get_required_csv_file( $assoc_args, $format ) {
		if ( empty( $assoc_args['file'] ) ) {
			$this->emit_error( $format, 'missing_file', __( 'Missing required --file argument.', 'gk-gravityimport' ) );
		}

		$file = realpath( $assoc_args['file'] );

		if ( ! $file || ! is_file( $file ) ) {
			$this->emit_error( $format, 'csv_not_found', sprintf( __( 'CSV file not found or not readable: %s', 'gk-gravityimport' ), $assoc_args['file'] ) );
		}

		if ( ! is_readable( $file ) ) {
			$this->emit_error( $format, 'csv_unreadable', sprintf( __( 'CSV file not readable: %s', 'gk-gravityimport' ), $assoc_args['file'] ) );
		}

		return $file;
	}

	/**
	 * Loads the profile file from command arguments.
	 *
	 * @since 2.11.0
	 *
	 * @param array $assoc_args Named command arguments.
	 * @param string $format     Output format.
	 *
	 * @return array Profile data.
	 */
	private function load_profile_from_args( $assoc_args, $format ) {
		if ( empty( $assoc_args['profile'] ) ) {
			$this->emit_error( $format, 'missing_profile', __( 'Missing required --profile argument.', 'gk-gravityimport' ) );
		}

		$profile = ImportProfile::load( $assoc_args['profile'] );

		if ( is_wp_error( $profile ) ) {
			$this->emit_error( $format, $profile->get_error_code() ?: 'profile_invalid', $profile->get_error_message() );
		}

		return $profile;
	}

	/**
	 * Resolves the form ID for the command.
	 *
	 * @since 2.11.0
	 *
	 * @param array $profile    Profile data.
	 * @param array $assoc_args Named command arguments.
	 *
	 * @return int Form ID.
	 */
	private function get_form_id( $profile, $assoc_args ) {
		return isset( $assoc_args['form-id'] ) ? (int) $assoc_args['form-id'] : (int) $profile['formId'];
	}

	/**
	 * Gets a Gravity Forms form or stops with a CLI error.
	 *
	 * @since 2.11.0
	 *
	 * @param int    $form_id Form ID.
	 * @param string $format  Output format.
	 *
	 * @return array Form data.
	 */
	private function get_form( $form_id, $format ) {
		$form = GFAPI::get_form( $form_id );

		if ( ! $form ) {
			$this->emit_error( $format, 'form_not_found', sprintf( __( 'Form #%d not found.', 'gk-gravityimport' ), (int) $form_id ) );
		}

		return $form;
	}

	/**
	 * Validates a profile against a form and emits warnings.
	 *
	 * @since 2.11.0
	 *
	 * @param array  $profile  Profile data.
	 * @param array  $form     Form data.
	 * @param string $format   Output format.
	 * @param array  $warnings Warning data.
	 *
	 * @return array Filtered profile data.
	 */
	private function validate_profile_against_form( $profile, $form, $format = 'table', &$warnings = [] ) {
		$result = ImportProfile::validate_against_form( $profile, $form );

		if ( is_wp_error( $result ) ) {
			$this->emit_error( $format, $result->get_error_code() ?: 'profile_invalid', $result->get_error_message() );
		}

		$warnings = $result['warnings'];

		if ( 'table' === $format ) {
			foreach ( $warnings as $warning ) {
				WP_CLI::warning( $warning['message'] );
			}
		}

		return $result['data'];
	}

	/**
	 * Checks the profile hash against the current form.
	 *
	 * @since 2.11.0
	 *
	 * @param array       $profile Profile data.
	 * @param array       $form    Form data.
	 * @param bool        $force   Whether to continue on hash mismatch.
	 * @param string|null $warning Warning message for allowed mismatches.
	 * @param string      $format  Output format.
	 *
	 * @return string Hash status.
	 */
	private function check_form_hash( $profile, $form, $force, $warning = null, $format = 'table' ) {
		if ( empty( $profile['formHash'] ) ) {
			return 'missing';
		}

		$current_hash = ImportProfile::compute_form_hash( $form );

		if ( $current_hash === $profile['formHash'] ) {
			return 'match';
		}

		if ( ! $force ) {
			$this->emit_error( $format, 'form_hash_mismatch', __( 'Form structure has changed since profile was saved. Use --force to proceed anyway.', 'gk-gravityimport' ) );
		}

		if ( 'table' === $format ) {
			WP_CLI::warning( $warning ?: __( 'Form structure has changed since profile was saved. Proceeding because --force was supplied.', 'gk-gravityimport' ) );
		}

		return 'mismatch';
	}

	/**
	 * Reads CSV headers from a local CSV file.
	 *
	 * @since 2.11.0
	 *
	 * @param string $file   CSV file path.
	 * @param string $format Output format.
	 *
	 * @return array CSV headers.
	 */
	private function read_csv_headers( $file, $format ) {
		$csv_handle = fopen( $file, 'r' );

		if ( ! $csv_handle ) {
			$this->emit_error( $format, 'csv_unreadable', sprintf( __( 'Could not open CSV file: %s', 'gk-gravityimport' ), $file ) );
		}

		$csv_headers = fgetcsv( $csv_handle );

		fclose( $csv_handle );

		if ( ! $csv_headers ) {
			$this->emit_error( $format, 'csv_empty_headers', __( 'Could not read CSV headers.', 'gk-gravityimport' ) );
		}

		return $csv_headers;
	}

	/**
	 * Builds Batch::create arguments from profile data.
	 *
	 * @since 2.11.0
	 *
	 * @param string $file    CSV file path.
	 * @param int    $form_id Form ID.
	 * @param array  $schema  Mapped schema.
	 * @param array  $profile Profile data.
	 *
	 * @return array Batch arguments.
	 */
	private function build_batch_args( $file, $form_id, $schema, $profile ) {
		$flags      = [ 'auto' ];
		$feeds      = [];
		$conditions = [];

		if ( ! empty( $profile['options'] ) ) {
			$opts = $profile['options'];

			if ( ! empty( $opts['ignoreErrors']['checked'] ) ) {
				$flags[] = 'soft';
			}

			// Batch "require" enforces required fields; checked ignoreRequired means the flag must stay off.
			if ( empty( $opts['ignoreRequired']['checked'] ) ) {
				$flags[] = 'require';
			}

			if ( ! empty( $opts['emailNotifications']['checked'] ) ) {
				$flags[] = 'notify';
			}

			if ( ! empty( $opts['skipValidation']['checked'] ) ) {
				$flags[] = 'valid';
			}

			if ( ! empty( $opts['ignoreFieldConditionalLogic']['checked'] ) ) {
				$flags[] = 'ignorefieldconditionallogic';
			}

			if ( ! empty( $opts['processFeeds']['checked'] ) && ! empty( $opts['processFeeds']['feeds'] ) ) {
				$feeds = $opts['processFeeds']['feeds'];
			}

			if ( ! empty( $opts['conditionalImport']['checked'] ) && ! empty( $opts['conditionalImport']['conditions'] ) ) {
				$conditions = $opts['conditionalImport']['conditions'];
			}

			if ( ! empty( $opts['useDefaultFieldValues']['checked'] ) ) {
				foreach ( $schema as &$rule ) {
					if ( ! isset( $rule['flags'] ) ) {
						$rule['flags'] = [];
					}

					$rule['flags'][] = 'default';
				}

				unset( $rule );
			}
		}

		return [
			'source'     => $file,
			'form_id'    => $form_id,
			'schema'     => $schema,
			'flags'      => $flags,
			'feeds'      => $feeds,
			'conditions' => $conditions,
		];
	}

	/**
	 * Displays a dry-run summary without creating a batch.
	 *
	 * @since 2.11.0
	 *
	 * @param string $format      Output format.
	 * @param array  $form        Form data.
	 * @param array  $profile     Profile data.
	 * @param array  $csv_headers CSV headers.
	 * @param array  $match       Column match result.
	 * @param array  $schema      Mapped schema.
	 * @param array  $batch_args  Batch arguments.
	 * @param string $hash_status Hash status.
	 */
	private function display_dry_run_summary( $format, $form, $profile, $csv_headers, $match, $schema, $batch_args, $hash_status ) {
		if ( 'table' === $format ) {
			WP_CLI::log( __( 'Dry run: no import batch was created.', 'gk-gravityimport' ) );
		}

		$this->format_items( $format, [
			[
				'item'  => __( 'Form', 'gk-gravityimport' ),
				'value' => sprintf( __( '#%1$d %2$s', 'gk-gravityimport' ), (int) $form['id'], $form['title'] ),
			],
			[
				'item'  => __( 'Profile form', 'gk-gravityimport' ),
				'value' => sprintf( __( '#%d', 'gk-gravityimport' ), (int) $profile['formId'] ),
			],
			[
				'item'  => __( 'Form hash', 'gk-gravityimport' ),
				'value' => $hash_status,
			],
			[
				'item'  => __( 'CSV columns', 'gk-gravityimport' ),
				'value' => count( $csv_headers ),
			],
			[
				'item'  => __( 'Matched columns', 'gk-gravityimport' ),
				'value' => count( $match['matched'] ),
			],
			[
				'item'  => __( 'Unmatched columns', 'gk-gravityimport' ),
				'value' => count( $match['unmatched'] ),
			],
			[
				'item'  => __( 'Schema rules', 'gk-gravityimport' ),
				'value' => count( $schema ),
			],
			[
				'item'  => __( 'Flags', 'gk-gravityimport' ),
				'value' => implode( ',', $batch_args['flags'] ),
			],
		], [ 'item', 'value' ] );

		if ( 'table' === $format ) {
			WP_CLI::success( __( 'Dry run complete.', 'gk-gravityimport' ) );
		}
	}

	/**
	 * Displays a structured run summary.
	 *
	 * @since 2.11.0
	 *
	 * @param string $format     Output format.
	 * @param int    $batch_id   Batch ID.
	 * @param string $status     Batch status.
	 * @param int    $total      Total rows.
	 * @param int    $imported   Imported rows.
	 * @param int    $skipped    Skipped rows.
	 * @param int    $errors     Error rows.
	 * @param string $last_error Last error message.
	 */
	private function display_run_summary( $format, $batch_id, $status, $total, $imported, $skipped, $errors, $last_error ) {
		$row = [
			'batch_id'   => (int) $batch_id,
			'status'     => $status,
			'total'      => $total,
			'imported'   => $imported,
			'skipped'    => $skipped,
			'errors'     => $errors,
			'last_error' => $last_error,
		];

		$this->format_items( $format, [ $row ], array_keys( $row ) );
	}
}
