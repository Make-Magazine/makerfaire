<?php

namespace GravityKit\GravityImport;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;

class Processor {

	/**
	 * @var array Processor arguments.
	 */
	private $args;

	/**
	 * @var array Default Excel false values
	 */
	private $default_false_values = array( '0', 'false', 'null', 'no', '' );

	/**
	 * @var array Default true values
	 */
	private $default_true_values = array( '1', 'true', 'yes' );

	/**
	 * @var array Array of accepted delimiters
	 */
	private $valid_delimiters = array( "\t", ',', '|', ';' );

	/**
	 * @param array $args     Various arguments for this processor:
	 *                        batch_id   string Only process tasks for this batch.
	 *                        timeout    int    The maximum number of seconds to process.
	 *                        memory     int    The maximum number of bytes for memory limit.
	 *                        count      int    The maximum number of rows to process per run.
	 *                        breakpoint string A state to break on during run. Broken once.
	 *
	 * Any first limit to be reached will trigger processing to be suspended.
	 */
	public function __construct( $args ) {
		$args = wp_parse_args( $args, array(
			'batch_id'   => null,
			'time'       => time(),
			'timeout'    => Server::get_timeout(),
			'memory'     => Server::get_memory_limit(),
			'count'      => 0,
			'countout'   => 0,
			'breakpoint' => null,
		) );

		/**
		 * Filters the processor arguments before the import processor is initialized.
		 *
		 * Use this filter to modify timeout, memory limits, batch ID, or other processor settings
		 * before the import process begins.
		 *
		 * @since 2.0
		 *
		 * @param array $args {
		 *     Processor arguments.
		 *
		 *     @type string|null $batch_id   The batch ID to process. Default null.
		 *     @type int         $time       Start time as Unix timestamp. Default current time.
		 *     @type int         $timeout    Maximum seconds to process before suspending. Default from Server::get_timeout().
		 *     @type int         $memory     Maximum memory in bytes. Default from Server::get_memory_limit().
		 *     @type int         $count      Maximum rows to process per run. Default 0 (unlimited).
		 *     @type int         $countout   Internal counter for processed rows. Default 0.
		 *     @type string|null $breakpoint Status to break on during run. Default null.
		 * }
		 */
		$this->args = apply_filters( 'gravityview/import/processor/args', $args );

		/**
		 * Fires when the import processor has been initialized and is ready to run.
		 *
		 * Use this action to perform setup tasks before the import process begins,
		 * such as logging, adding additional filters, or preparing external resources.
		 *
		 * @since 2.0
		 *
		 * @param \GravityKit\GravityImport\Processor $processor The initialized processor instance.
		 * @param array                               $args      The processor arguments before filtering.
		 */
		do_action( 'gravityview/import/processor/init', $this, $args );
	}

	/**
	 * Reset the processor state.
	 */
	private function reset() {
		$this->args['countout'] = 0;
	}

	/**
	 * Sets the current breakpoint.
	 *
	 * @param string $breakpoint A batch status to break on during the run.
	 */
	public function set_breakpoint( $breakpoint ) {
		$this->args['breakpoint'] = $breakpoint;
	}

	/**
	 * Run tasks for as long as we can.
	 *
	 * @return \WP_Error|array A batch or an error.
	 */
	public function run() {
		global $wpdb;

		$this->reset();

		if ( ! $this->args['time'] ) {
			$this->args['time'] = time();
		}

		$schema = gv_import_entries_get_batch_json_schema();

		if ( ! $batch = Batch::get( $this->args['batch_id'] ) ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_id', __( 'Batch does not exist', 'gk-gravityimport' ) );
		}

		/**
		 * Filters the batch data before processing begins.
		 *
		 * Use this filter to modify batch settings, add custom flags, or alter
		 * the import configuration before the processor starts running.
		 *
		 * @since 2.0
		 *
		 * @param array                               $batch     The batch data array containing import configuration.
		 * @param \GravityKit\GravityImport\Processor $processor The processor instance.
		 */
		$batch = apply_filters( 'gravityview/import/run/batch', $batch, $this );

		/**
		 * Invalid statuses.
		 */
		if ( in_array( $batch['status'], $schema['properties']['status']['extra']['stop'] ) ) {
			$batch['error'] = $error = __( 'Batch is in stop state. Running no longer possible.', 'gk-gravityimport' );
			Batch::update( $batch );
			return new \WP_Error( 'gravityview/import/errors/invalid_state', $error, array( 'status' => 409 ) );
		}

		if ( ! $this->has_resources() ) {
			return $batch;
		}

		if ( in_array( $batch['status'], array( 'new' ) ) ) {
			if ( is_wp_error( $batch = $this->tick( $batch ) ) ) {
				return $batch;
			}
		}

		/**
		 * Automatic parse launch.
		 *
		 * We don't care about timeouts or memory here, because
		 * these statuses are typically turned over very quickly.
		 */
		if ( is_array( $batch['flags'] ) && in_array( 'auto', $batch['flags'] ) ) {
			switch ( $batch['status'] ):
				case 'parsing':
				case 'process':
					/**
					 * Switch to ready and run.
					 */
					$batch = $this->tick( $batch );
					return $this->run();
				case 'parsed':
					/**
					 * Switch to ready and run.
					 */
					$batch['status'] = 'process';

					do_action( "gravityview/import/process/{$batch['status']}", $batch = Batch::update( $batch ) );

					$batch = $this->tick( $batch );
					return $this->run();
			endswitch;
		}

		if ( in_array( $batch['status'], array( 'rollback' ) ) ) {
			return $this->tick( $batch );
		}

		while ( in_array( $batch['status'], array( 'parsing', 'process', 'processing', 'halt' ) ) ) {

			if ( ! $this->has_resources() ) {
				break;
			}

			if ( $this->is_count_exceeded() ) {
				break;
			}

			$this->args['countout']++;

			if ( is_wp_error( $result = $this->tick( $batch ) ) ) {

				if ( $result->get_error_code() === 'gravityview/import/breakpoint' ) {
					return $result;
				}

				$batch = Batch::get( $batch['id'] );
				if ( $batch['status'] === 'error' ) {
					return $result;
				}

				break;
			}

			$batch = $result;
		}

		return $batch;
	}

	/**
	 * Run a bit.
	 *
	 * @param array $batch The batch we're running over.
	 */
	private function tick( $batch ) {
		if ( $batch['status'] === $this->args['breakpoint'] ) {
			$this->args['breakpoint'] = null;
			return new \WP_Error( 'gravityview/import/breakpoint', sprintf( __( 'Breakpoint on status %s', 'gk-gravityimport' ), $batch['status'] ), $batch );
		}

		/**
		 * @var \WP_Error|array $result The result of the handler.
		 */
		switch ( $batch['status'] ) {
			case 'new':
				$result = $this->handle_new( $batch );
				break;
			case 'parsing':
				$result = $this->handle_parsing( $batch );
				break;
			case 'process':
				$result = $this->handle_process( $batch );
				break;
			case 'processing':
				$result = $this->handle_processing( $batch );
				break;
			case 'halt':
			case 'halted':
				$result = $this->handle_halt( $batch );
				break;
			case 'rollback':
				$result = $this->handle_rollback( $batch );
				break;
			case 'rolledback':
				$result = $this->handle_rolledback( $batch );
				break;
			case 'done':
				$result = $this->handle_done( $batch );
				break;
			default:
				$result = new \WP_Error( 'gravityview/import/errors/tick/invalid-status', sprintf( __( 'Unknown batch status: %s', 'gk-gravityimport' ), $batch['status'] ) );
				break;
		}

		if ( is_wp_error( $result ) ) {
			$batch['status'] = 'error';
			$batch['error']  = $result->get_error_message();

			do_action( "gravityview/import/process/{$batch['status']}", $batch = Batch::update( $batch ) );

			return $result;
		}

		return $result;
	}

	/**
	 * Have we got fuel for more processing?
	 *
	 * @return bool
	 */
	public function has_resources() {
		/**
		 * Filters whether the processor has sufficient resources to continue processing.
		 *
		 * Use this filter to override the default memory and timeout checks,
		 * or to implement custom resource checking logic.
		 *
		 * @since 2.0
		 *
		 * @param bool|null                           $has_resources True to continue, false to stop, null to use default logic.
		 * @param \GravityKit\GravityImport\Processor $processor     The processor instance.
		 */
		if ( ! is_null( $result = apply_filters( 'gravityview/import/has_resources', null, $this ) ) ) {
			return $result;
		}

		if ( $this->args['memory'] ) {
			if ( Server::is_memory_exceeded( $this->args['memory'] ) ) {
				return false;
			}
		}

		if ( $this->args['timeout'] ) {
			/**
			 * Calculate current time passed.
			 * A 3 second reserve is provided.
			 */
			if ( ( time() + Server::TIMEOUT_RESERVE ) > ( $this->args['time'] + $this->args['timeout'] ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check if the count is exceeded (more rows than the total).
	 *
	 * @internal
	 *
	 * @since 2.6.0
	 *
	 * @return bool
	 */
	public function is_count_exceeded() {
		if ( ! isset( $this->args['count'] ) || ! isset( $this->args['countout'] ) || empty( $this->args['count'] ) ) {
			return false;
		}

		if ( $this->args['countout'] >= $this->args['count'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Prepare the source.
	 *
	 * @internal
	 *
	 * @param array $batch The batch.
	 *
	 * @return \WP_Error|array A batch or an error.
	 */
	public function handle_new( $batch ) {
		if ( is_wp_error( $error = $this->_handle_check_status( __FUNCTION__, $batch ) ) ) {
			return $error;
		}

		if ( is_wp_error( $source = $this->to_local( $batch['source'], true ) ) ) {
			Batch::update( array(
				'id'     => $batch['id'],
				'status' => 'error',
				'error'  => $source->get_error_message(),
			) );
			return $source;
		}

		return Batch::update( array(
			'id'     => $batch['id'],
			'status' => 'parsing'
		) );
	}

	/**
	 * Parse the source.
	 *
	 * Detect headers, fields, rowcount, etc.
	 * Sets status to 'parsed' on success.
	 *
	 * @internal
	 *
	 * @todo prevent race conditions on cuncurrency here
	 *
	 * @throws \Exception
	 *
	 * @param array $batch The batch.
	 *
	 * @return \WP_Error|array A batch or an error.
	 */
	public function handle_parsing( $batch ) {
		// Enable auto-detection of line endings to handle Mac-style CR-only line endings
		// Suppress deprecation warning in PHP 8.1+ (where line endings are auto-detected)
		@ini_set( 'auto_detect_line_endings', true );

		if ( is_wp_error( $error = $this->_handle_check_status( __FUNCTION__, $batch ) ) ) {
			return $error;
		}

		if ( is_wp_error( $error = $this->_handle_check_source( $batch ) ) ) {
			return $error;
		}

		if ( $batch['form_id'] ) {
			if ( ! $form = \GFAPI::get_form( $batch['form_id'] ) ) {
				return new \WP_Error( 'gravityview/import/errors/invalid_form', __( 'Invalid form ID.', 'gk-gravityimport' ) );
			}
		} else {
			$form = null;
		}

		$interpreter = $this->get_interpreter_for( $batch );

		$lexer = $this->get_lexer_for( $batch );

		/**
		 * Field detection functions.
		 */
		$all_funcs = array(
			'numeric'   => 'is_numeric',
			'new_lines' => function ( $d ) {
				return strpos( $d, "\n" );
			},
			'email'     => 'is_email',
			'html'      => function ( $d ) {
				return strip_tags( $d ) != $d;
			},
			'date'      => function ( $d ) {
				return strtotime( $d );
			},
			'time'      => function ( $d ) {
				return preg_match( '#\d{1,2}:\d{2}(:\d{2})?#', $d );
			},
		);

		/**
		 * Filters the number of rows to analyze during the parsing phase.
		 *
		 * The excerpt size determines how many rows are scanned to detect
		 * column types and build field mapping hints during the parsing phase.
		 *
		 * @since 2.0
		 * @since 2.6.1 Increased default from 20 to 51 to show 50 data rows in preview (plus 1 header row).
		 *
		 * @param int   $excerpt_size The number of rows to analyze. Includes headers. Default 51.
		 * @param array $batch        The batch data.
		 */
		$excerpt_size = apply_filters( 'gravityview/import/parse/excerpt', 51, $batch );

		/**
		 * Set the number of lines in the source.
		 */
		if ( empty( $batch['progress']['total'] ) ) {
			$csv = fopen( $batch['source'], 'r' );

			if ( ! $csv ) {
				return new \WP_Error( 'gravityview/import/errors/cannot_open_csv', __( 'Failed to open CSV file for processing.', 'gk-gravityimport' ) );
			}

			while ( fgetcsv( $csv ) !== false ) {
				$batch['progress']['total']++;
			}
			$batch['progress']['total'] = max( 0, $batch['progress']['total'] - 1 /** ignore headers */ );
			$batch                      = Batch::update( $batch );
			fclose( $csv );
		}

		/**
		 * Analyze and build the hints.
		 */
		$headers   = empty( $batch['meta']['_headers'] ) ? array() : $batch['meta']['_headers'];
		$columns   = empty( $batch['meta']['_columns'] ) ? array() : $batch['meta']['_columns'];
		$number    = 0;
		$processor = &$this;
		$interpreter->addObserver( function ( $row ) use ( &$batch, &$headers, &$columns, &$all_funcs, &$number, $form, $excerpt_size, $processor ) {
			$number++;

			/**
			 * Fires when a CSV row is being processed during the parsing phase.
			 *
			 * @since      1.0
			 * @deprecated 2.0 Use `gravityview/import/process/row` instead.
			 *
			 * @param array $row    The row data as an array of column values.
			 * @param int   $number The row number (starts from 1 with headers).
			 */
			do_action( 'gravityview-importer/process-row', $row, $number );

			/**
			 * Fires when a CSV row is being processed during the parsing phase.
			 *
			 * Use this action to perform custom operations on each row during parsing,
			 * such as logging, validation, or custom field mapping.
			 *
			 * @since 2.0
			 *
			 * @param array $row    The row data as an array of column values.
			 * @param int   $number The row number (starts from 1 with headers).
			 * @param array $batch  The batch data.
			 */
			do_action( 'gravityview/import/process/row', $row, $number, $batch );

			if ( isset( $batch['meta']['_resume'] ) ) {
				// Skip some rows...
				if ( $number <= $batch['meta']['_resume'] ) {
					return; // ...as this is a resumable run
				}
			}

			if ( empty( $headers ) ) {
				/**
				 * Fix BOM added by Gravity Forms export.php
				 *
				 * @see http://stackoverflow.com/questions/5601904/encoding-a-string-as-utf-8-with-bom-in-php
				 */
				$row[0] = str_replace( chr( 239 ) . chr( 187 ) . chr( 191 ), '', $row[0] );
				$row[0] = trim( $row[0], '"' );
			}

			/**
			 * Record the excerpt.
			 */
			if ( count( $batch['meta']['excerpt'] ) < $excerpt_size ) {
				if ( json_encode( $row ) ) {
					$batch['meta']['excerpt'][] = $row;
				} // Otherwise this is broken, unencodeable UTF8 ;(
			}

			/**
			 * Parse first line as headers.
			 */
			if ( empty( $headers ) ) {
				$headers = $row;

				if ( empty( $batch['scheme'] ) && $form ) {
					/**
					 * Find matches by field label, name, field ID or field type.
					 */
					foreach ( $headers as $i => $column ) {
						$column_name = Core::strtolower( trim( $column ) );

						/**
						 * Field names and IDs as exported.
						 */
						foreach ( \GFCommon::get_field_filter_settings( $form ) as $field ) {
							if ( $field['key'] == 'entry_id' ) {
								$field['key'] = 'id'; // There's no such column as entry_id in the database...
							}

							if ( in_array( $column_name, array( Core::strtolower( $field['key'] ), Core::strtolower( $field['text'] ) ) ) ) {
								$columns[ $i ] = array(
									'title' => $column,
									'field' => $field['key'],
								);
								continue 2;
							}
						}

						/**
						 * The note field.
						 * Only match exact "notes" or "entry notes" - partial matches like "Analyst Notes" should not map to Entry Notes
						 */
						$notes_exact_matches = array(
							Core::strtolower( __( 'Notes', 'gk-gravityimport' ) ),
							Core::strtolower( __( 'Entry Notes', 'gk-gravityimport' ) ),
						);
						if ( in_array( $column_name, $notes_exact_matches, true ) ) {
							$columns[ $i ] = array(
								'title' => $column,
								'field' => 'notes',
							);
							continue;
						}

						/**
						 * User ID.
						 */
						$user_id_parts = explode( '(', __( 'Created By (User Id)', 'gk-gravityimport' ) );
						if (
							strpos( $column_name, Core::strtolower( trim( $user_id_parts[0] ) ) ) !== false
							|| strpos( $column_name, Core::strtolower( trim( $user_id_parts[1], ' )' ) ) ) !== false ) {
							continue; // Do not autosuggest the User ID column
						}

						/**
						 * Numeric IDs.
						 */
						if ( is_numeric( $column_name ) ) {
							foreach ( $form['fields'] as $field ) {
								if ( ( $field['id'] == $column_name ) || ( is_array( $field->inputs ) && in_array( $column_name, wp_list_pluck( $field->inputs, 'id' ) ) ) ) {
									$columns[ $i ] = array(
										'title' => $column,
										'field' => $column_name,
									);
									continue 2;
								}
							}
						}

						/**
						 * Auxiliary columns.
						 */
						if ( in_array( $column_name, \GFFormsModel::get_lead_db_columns() ) ) {
							$columns[ $i ] = array(
								'title' => $column,
								'field' => $column_name,
							);
							continue;
						}

						/**
						 * Check for other potential entry columns.
						 * Currency is not included in GFCommon::get_entry_info_filter_settings, for example.
						 */
						if ( in_array( str_replace( ' ', '_', $column_name ), \GFFormsModel::get_lead_db_columns() ) ) {
							$columns[ $i ] = array(
								'title' => $column,
								'field' => str_replace( ' ', '_', $column_name ),
							);
							continue;
						}

						/**
						 * Extra labels.
						 */
						foreach ( $form['fields'] as $field ) {
							foreach ( array( 'adminLabel', 'label' ) as $key ) {
								if ( Core::strtolower( trim( $field[ $key ] ) ) === Core::strtolower( $column_name ) ) {
									$columns[ $i ] = array(
										'title' => $column,
										'field' => $field['id'],
									);
									continue 3;
								}
							}
						}

						/**
						 * Field inputs.
						 */
						foreach ( $form['fields'] as $field ) {
							if ( $field->inputs ) {
								foreach ( $field->inputs as $input ) {
									if ( in_array( $column_name, array( Core::strtolower( trim( rgar( $input, 'label' ) ) ), Core::strtolower( trim( rgar( $input, 'name' ) ) ) ) ) ) {
										$columns[ $i ] = array(
											'title' => $column,
											'field' => $input['id'],
										);
										continue 3;
									}

									$hybrid_input = sprintf( "%s (%s)", $field->label ? $field->label : $field->adminLabel, rgar( $input, 'label', rgar( $input, 'name' ) ) );

									if ( $column_name == Core::strtolower( $hybrid_input ) ) {
										$columns[ $i ] = array(
											'title' => $column,
											'field' => $input['id'],
										);
										continue 3;
									}
								}
							}
						}
					}
				}

				if ( empty( $batch['scheme'] ) && ! $form ) {
					/**
					 * Try to figure out what sort of row this is.
					 * We're mainly looking at dates, texts, numbers.
					 */
					foreach ( $headers as $i => $column ) {

						$column_name = trim( Core::strtolower( $column ) );

						/**
						 * Field names and IDs as exported.
						 */
						foreach ( \GFCommon::get_entry_info_filter_settings() as $field ) {
							if ( in_array( $column_name, array( Core::strtolower( $field['key'] ), Core::strtolower( $field['text'] ) ) ) ) {

								if ( $field['key'] == 'entry_id' ) {
									$field['key'] = 'id'; // There's no such column as entry_id in the database...
								}

								$columns[ $i ] = array(
									'title' => $column,
									'field' => $field['key'],
								);
								continue 2;
							}
						}

						/**
						 * The note field.
						 * Only match exact "notes" or "entry notes" - partial matches like "Analyst Notes" should not map to Entry Notes
						 */
						$notes_exact_matches = array(
							Core::strtolower( __( 'Notes', 'gk-gravityimport' ) ),
							Core::strtolower( __( 'Entry Notes', 'gk-gravityimport' ) ),
						);
						if ( in_array( $column_name, $notes_exact_matches, true ) ) {
							$columns[ $i ] = array(
								'title' => $column,
								'field' => 'notes',
							);
							continue;
						}

						/**
						 * Check for other potential entry columns.
						 * Currency is not included in GFCommon::get_entry_info_filter_settings, for example.
						 */
						if ( in_array( str_replace( ' ', '_', $column_name ), \GFFormsModel::get_lead_db_columns() ) ) {
							$columns[ $i ] = array(
								'title' => $column,
								'field' => str_replace( ' ', '_', $column_name ),
							);
							continue;
						}

						$typemap = $this->get_field_typemap( $column, $batch );

						foreach ( $typemap as $keyword => $type ) {

							// Match exact matches and complete words (not just a string match like "app" => "apple").
							// Normalize column name to handle underscores, parentheses, and other separators.
							$normalized = preg_replace( '/[^a-z0-9]+/i', ' ', $column_name );
							$escaped    = preg_quote( $keyword, '/' );
							$pattern    = '/(^|[^a-z0-9])' . $escaped . '(?=[^a-z0-9]|$)/i';

							if ( preg_match( $pattern, $normalized ) ) {
								$columns[ $i ] = array(
									'title' => $column,
									'field' => $type,
								);
								continue 2;
							}
						}
					}
				}

				return;
			}

			/**
			 * Gather data about the rows when trying to detect field types.
			 */
			if ( empty( $batch['scheme'] ) ) {
				foreach ( $row as $i => $data ) {
					if ( ! empty( $columns[ $i ]['field'] ) ) {
						continue;
					}

					if ( empty( $columns[ $i ] ) ) {
						$columns[ $i ] = array(
							'title' => rgar( $headers, $i, sprintf( __( 'Column %d', 'gk-gravityimport' ), $i ) ),
							'field' => '',
						);
					}

					/**
					 * Empty data is special. Probably nullable field.
					 */
					if ( ( $result = empty( \GFCommon::safe_strlen( trim( $data ) ) ) ) && empty( $columns[ $i ][ $key = '_is_all_empty' ] ) ) {
						$columns[ $i ][ $key ] = true;
					} elseif ( ! empty( $columns[ $i ][ $key = '_is_all_empty' ] ) && ! $result ) {
						$columns[ $i ][ $key ] = false;
					}

					if ( $result ) {
						continue;
					}

					/**
					 * All the rows have to be of a specific format.
					 */
					foreach ( $all_funcs as $key => $callback ) {
						$result = $callback( $data );
						$key    = "_is_all_$key";
						if ( $result && empty( $columns[ $i ][ $key ] ) ) {
							$columns[ $i ][ $key ] = true;
						} elseif ( ! empty( $columns[ $i ][ $key ] ) && ! $result ) {
							$columns[ $i ][ $key ] = false;
						}
					}

					if ( empty( $columns[ $i ]['_length'] ) ) {
						$columns[ $i ]['_length'] = \GFCommon::safe_strlen( $data );
					} else {
						$columns[ $i ]['_length'] += \GFCommon::safe_strlen( $data );
					}

					// @TODO determine if this is necessary
					//$columns[ $i ]['_options'][ md5( $data ) ] = true;
				}
			}

			/**
			 * Record the data into the rows table.
			 */
			$tables = gv_import_entries_get_db_tables();

			global $wpdb;

			$data = array(
				'number'   => $number,
				'batch_id' => $batch['id'],
				'status'   => 'new',
			);

			if ( ! $encoded_row = json_encode( $row ) ) {
				$data['error']  = __( 'Malformed, empty or invalid row data', 'gk-gravityimport' );
				$data['status'] = 'error';
			} else {
				$data['data'] = $encoded_row;
			}

			$wpdb->insert( $tables['rows'], $data );

			/**
			 * Bump the rowcount.
			 */
			$batch['meta']['rows']++;

			if ( ! $processor->has_resources() ) {
				$batch['meta']['_headers'] = $headers;
				$batch['meta']['_columns'] = $columns;
				$batch['meta']['_resume']  = $number;

				throw new \Exception( 'breakpoint' );
			}
		} );

		try {
			$lexer->parse( $batch['source'], $interpreter );
		} catch ( \Exception $e ) {
			if ( $e->getMessage() === 'breakpoint' ) {
				return Batch::update( $batch );
			}

			return new \WP_Error( 'gravityview/import/errors/csv_error', $e->getMessage() );
		}

		unset( $batch['meta']['_columns'], $batch['meta']['_headers'], $batch['meta']['_resume'] );

		foreach ( $headers as $i => $header ) {

			if ( empty( $batch['scheme'] ) && empty( $columns[ $i ]['field'] ) ) {
				if ( ! $form ) {
					/**
					 * Analyze gathered row data and suggest a field type.
					 */
					if ( ! empty( $columns[ $i ]['_is_all_numeric'] ) ) {
						$columns[ $i ]['field'] = 'number';
					} else {
						if ( ! empty( $columns[ $i ]['_is_all_email'] ) ) {
							$columns[ $i ]['field'] = 'email';
						} elseif ( ! empty( $columns[ $i ]['_is_all_html'] ) ) {
							$columns[ $i ]['field'] = 'html';
						} elseif ( ! empty( $columns[ $i ]['_is_all_empty'] ) ) {
							$columns[ $i ]['field'] = '';
						} elseif ( ! empty( $columns[ $i ]['_is_all_new_lines'] ) ) {
							$columns[ $i ]['field'] = 'textarea';
						} elseif ( ! empty( $columns[ $i ]['_length'] ) && ( $columns[ $i ]['_length'] / $batch['meta']['rows'] > 128 ) ) {
							$columns[ $i ]['field'] = 'textarea';
						} elseif ( ! empty( $columns[ $i ]['_is_all_time'] ) ) {
							$columns[ $i ]['field'] = 'time';
						} elseif ( ! empty( $columns[ $i ]['_is_all_date'] ) ) {
							$columns[ $i ]['field'] = 'date';
						} /* @TODO determine if this is necessary
						 * elseif ( ! empty( $columns[ $i ]['_options'] )
						 * && ( $options = count( $columns[ $i ]['_options'] ) ) > 1
						 * && ( $options / $batch['meta']['rows'] <= 0.2 ) ) {
						 * $columns[ $i ]['field'] = 'radio';
						 * } */ else {
							$columns[ $i ]['field'] = 'text';
						}
					}
				} else {
					/**
					 * Further heuristics by existing field type in the form.
					 */
					if ( ! empty( $columns[ $i ]['_is_all_email'] ) ) {
						if ( count( $field = \GFAPI::get_fields_by_type( $form, 'email' ) ) === 1 ) {
							$columns[ $i ]['field'] = $field[0]['id'];
						}
					} elseif ( ! empty( $columns[ $i ]['_is_all_date'] ) ) {
						if ( count( $field = \GFAPI::get_fields_by_type( $form, 'date' ) ) === 1 ) {
							$columns[ $i ]['field'] = $field[0]['id'];
						}
					} elseif ( ! empty( $columns[ $i ]['_is_all_time'] ) ) {
						if ( count( $field = \GFAPI::get_fields_by_type( $form, 'time' ) ) === 1 ) {
							$columns[ $i ]['field'] = $field[0]['id'];
						}
					}
				}
			}

			/**
			 * Cleanup temporary fields.
			 */
			foreach ( $columns[ $i ] as $key => $_ ) {
				if ( strpos( $key, '_' ) === 0 ) {
					unset( $columns[ $i ][ $key ] );
				}
			}
		}

		$batch['meta']['columns'] = array_filter( $columns, function ( $column ) {
			return ! empty( $column['field'] );
		} );

		$batch['status'] = 'parsed';

		do_action( "gravityview/import/process/{$batch['status']}", $batch = Batch::update( $batch ) );

		/**
		 * @deprecated 2.0 Use `gravityview/import/process/parsed` instead.
		 */
		do_action( 'gravityview-importer/end-of-file' );

		return $batch;
	}

	/**
	 * Halt processing on this batch.
	 *
	 * Processing will not halt immediately if other processors
	 * are working on the batch. See timeouts and wait periods.
	 *
	 * @param array $batch The batch.
	 *
	 * @return \WP_Error|array The batch or an error on timeout.
	 */
	public function handle_halt( $batch ) {
		global $wpdb;

		$tables = gv_import_entries_get_db_tables();

		if ( $processing = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$tables['rows']} WHERE batch_id = %d AND status = 'processing'", $batch['id'] ) ) ) {

			/**
			 * The amount of patience before the batch
			 *                                           enters an error an error state on frozen
			 *                                           row processing.
			 *
			 * @since 2.0
			 *
			 * @param int   $timeout The amount of time to wait before erroring.
			 * @param array $batch   The batch.
			 */
			$timeout = apply_filters( 'gravityview/import/halt/timeout', 10, $batch );

			if ( $timeout && ( ( $batch['updated'] + $timeout ) < time() ) ) {
				$batch['status'] = 'error';
				$batch['error']  = $error = sprintf( __( 'Wait timeout on processing rows. Zombies: %d.', 'gk-gravityimport' ), $processing );

				do_action( "gravityview/import/process/{$batch['status']}", $batch = Batch::update( $batch ) );

				return new \WP_Error( 'gravityview/import/errors/timeout', $error );
			}

			/**
			 * The time to sleep before trying to halt again.
			 *
			 * @since 2.0
			 *
			 * @param int   $sleep The amount of time to wait before trying again.
			 *                     Defaults to 1 if there are enough resources to continue.
			 *                     0 if there are none.
			 * @param array $batch The batch.
			 */
			sleep( $sleep = apply_filters( 'gravityview/import/halt/sleep', $this->has_resources() ? 1 : 0, $batch ) );

			return $batch;
		}


		$batch['status'] = 'halted';

		do_action( "gravityview/import/process/{$batch['status']}", $batch = Batch::update( $batch ) );

		return $batch;
	}

	/**
	 * Return a configured Goodby\CSV\Import\Standard\Interpreter for a batch.
	 *
	 * @internal
	 *
	 * @param array $batch The batch.
	 *
	 * @return \Goodby\CSV\Import\Standard\Interpreter
	 */
	public function get_interpreter_for( $batch ) {
		$interpreter = new Interpreter();


		/**
		 * @deprecated 2.0 Use `gravityview/import/unstrict` instead.
		 */
		$allow_mismatched_rows = apply_filters( 'gravityview-importer/unstrict', true );

		/**
		 * Whether to allow importing rows that aren't the same size as expected
		 *
		 * @since 2.0 Changed default to true
		 *
		 * @param bool  $allow_mismatched_rows Default: true
		 * @param array $batch                 The batch
		 */
		$allow_mismatched_rows = apply_filters( 'gravityview/import/unstrict', $allow_mismatched_rows, $batch );

		if ( $allow_mismatched_rows ) {
			$interpreter->unstrict();
		}

		return $interpreter;
	}

	/**
	 * Return a configured Goodby\CSV\Import\Standard\Lexer for a batch.
	 *
	 * @internal
	 *
	 * @param array  $batch The batch.
	 * @param string $class Override the Lexer class. Used in tests.
	 *
	 * @return \Goodby\CSV\Import\Standard\Lexer
	 */
	public function get_lexer_for( $batch, $class = null ) {
		// Enable auto-detection of line endings to handle Mac-style CR-only line endings
		// Suppress deprecation warning in PHP 8.1+ (where line endings are auto-detected)
		@ini_set( 'auto_detect_line_endings', true );

		$config = new LexerConfig();

		$headers = fopen( $batch['source'], 'rb' );

		if ( ! $headers ) {
			throw new \Exception( __( 'Failed to open CSV file for delimiter detection.', 'gk-gravityimport' ) );
		}

		while ( ! $header = fgets( $headers ) ) {
		}
		/** Rid us of empty new lines. */

		if ( preg_match( '#[\w ]+(\W)([\w+ ]\\1)?#', $header, $matches ) ) {
			if ( in_array( $matches[1], $this->valid_delimiters, true ) ) {
				$config->setDelimiter( $matches[1] );
			}
		}

		/**
		 * @deprecated 2.0 Use `gravityview/import/config` instead.
		 */
		do_action( 'gravityview-importer/config', $config );

		/**
		 * Configure the import format.
		 * Used to set exotic formats, escapes, etc.
		 *
		 * @param Goodby\CSV\Import\Standard\Lexer\Config $config The config object.
		 * @param array                                   $batch  The batch.
		 */
		do_action_ref_array( 'gravityview/import/config', array( &$config, $batch ) );

		if ( $class ) {
			$lexer = new $class( $config );
		} else {
			$lexer = new Lexer( $config );
		}

		return $lexer;
	}

	/**
	 * Prepare and proxy to handle_processing.
	 *
	 * Flushes the table if needed.
	 * Maybe locks the database.
	 *
	 * Assumes everything's ready for processing.
	 * Or resuming. Changes state to processing and
	 * delegates functionality to handle_processing,
	 * our main workhorse.
	 *
	 * @internal
	 *
	 * @param array $batch The batch.
	 *
	 * @return \WP_Error|array A batch or an error.
	 */
	public function handle_process( $batch ) {
		if ( is_wp_error( $error = $this->_handle_check_status( __FUNCTION__, $batch ) ) ) {
			return $error;
		}

		$batch['status'] = 'processing';

		if ( in_array( 'flush', $batch['flags'] ) ) {
			foreach ( \GFAPI::get_entries( $batch['form_id'] ) as $entry ) {
				if ( ! in_array( 'nolog', $batch['flags'] ) ) {
					Log::save( $batch['id'], 0, 'delete', $entry['id'] );
				}
				\GFAPI::delete_entry( $entry['id'] );
			}
		}

		do_action( "gravityview/import/process/{$batch['status']}", $batch = Batch::update( $batch ) );

		return $batch;
	}

	/**
	 * Processes the batch.
	 *
	 * @internal
	 *
	 * @param array $batch The batch.
	 *
	 * @return \WP_Error|array A batch or an error.
	 */
	public function handle_processing( $batch ) {
		global $wpdb;

		$tables = gv_import_entries_get_db_tables();

		if ( is_wp_error( $error = $this->_handle_check_status( __FUNCTION__, $batch ) ) ) {
			return $error;
		}

		/**
		 * Strip read-only batch values before validation.
		 */
		$schema           = gv_import_entries_get_batch_json_schema();
		$_batch           = $batch;
		$_batch['status'] = 'process';

		foreach ( $schema['properties'] as $name => $property ) {
			if ( ! empty( $property['readonly'] ) ) {
				unset( $_batch[ $name ] );
			}
		}

		if ( is_wp_error( $error = Batch::validate( $_batch ) ) ) {
			return $error;
		}

		if ( empty( $batch['schema'] ) ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_schema', __( 'No schema provided. Nothing to do.', 'gk-gravityimport' ) );
		}

		/**
		 * Create a new form if one is not provided.
		 */
		if ( is_null( $batch['form_id'] ) ) {
			$form_id = \GFAPI::add_form( array(
				'title'          => ( ! empty( $batch['form_title'] ) ) ? $batch['form_title'] : 'Import Batch #' . $batch['id'],
				'labelPlacement' => null,
				'fields'         => array(),
				'button'         => array(
					'type'     => 'text',
					'text'     => _x( 'Submit', 'Submit button text; the submit button is added to all new forms in GF 2.6+', 'gk-gravityimport' ),
					'imageUrl' => ''
				)
			) );

			if ( is_wp_error( $error = $form_id ) ) {
				return $error;
			}

			$form = \GFAPI::get_form( $form_id );

			/**
			 * Gravity Forms does not expose information on whether a field has
			 * multiple inputs or not. This is decided during form setting rendering and
			 * cannot be detected via an API, as far as we know.
			 *
			 * So we'll maintain a list and a filter.
			 */
			$multiinput_fields = array(
				'address',
				'checkbox',
				'name',
				'survey',
				'time',
				'option',
				'creditcard',
				'password',
				'consent',
				'poll',
				'quiz',
			);

			/**
			 * A list of multi-input fields. Here for forward patching purposes.
			 *
			 * @since 2.0
			 *
			 * @param string[] $multiinput_fields A list of field types that are multi-input.
			 * @param array    $batch             The batch.
			 */
			$multiinput_fields = apply_filters( 'gravityview/import/fields/multi-input', $multiinput_fields, $batch );

			$fields   = array();
			$field_id = 0;

			foreach ( $batch['schema'] as &$column ) {
				if ( Core::is_entry_column( $column['field'] ) ) {
					continue;
				}

				if ( ! empty( $column['meta']['is_meta'] ) ) {
					continue;
				}

				$index = null;
				$input = null;
				$type  = $column['field'];

				$field_key = $field_id;

				/**
				 * Support syntax for subfields.
				 *
				 * TYPE([INDEX])(.INPUT)
				 * () denotes optional syntax
				 * TYPE - is the field type
				 * INDEX - arbitrary numeric index, does not need to be sequential, if missing "" will be assumed
				 * INPUT - the input number, if missing "1" will be assumed
				 */
				preg_match_all( '#[^\.\[\]]+#i', $type, $matches );

				if ( $matches ) {
					$matches = reset( $matches );

					switch ( count( $matches ) ) {
						case 2:
							list( $type, $input ) = $matches;
							break;
						case 3:
							list( $type, $index, $input ) = $matches;
							break;
					}

					if ( $input ) {
						$field_key = json_encode( array( $type, $index ) );
					}
				}

				/**
				 * Group subfields into fields as needed.
				 * We do this by looking at the field index and type.
				 */
				if ( ! isset( $fields[ $field_key ] ) ) {
					$fields[ $field_key ] = \GF_Fields::create( array(
						'id'     => ++$field_id, // Autoincrement the field ID, assign it
						'label'  => ( ! empty( $column['name'] ) ) ? $column['name'] : $batch['meta']['columns'][ $column['column'] ]['title'],
						'type'   => $type,
						'formId' => $form_id,

						'inputType' => isset( $column['meta']['type'] ) ? $column['meta']['type'] : '',
					) );

					/**
					 * Add defaul inputs in some special cases.
					 */
					if ( 'time' === $fields[ $field_key ]->type ) {
						$fields[ $field_key ]->inputs = array(
							array( 'id' => "$field_id.1", 'label' => 'HH', 'name' => '' ),
							array( 'id' => "$field_id.2", 'label' => 'MM', 'name' => '' ),
							array( 'id' => "$field_id.3", 'label' => 'AM/PM', 'name' => '' ),
						);
					}

					if ( 'list' === $type ) {
						if ( ! empty( $column['meta']['list_cols'] ) ) {
							/**
							 * Add the columns.
							 */
							$fields[ $field_key ]->choices = array();
							foreach ( $column['meta']['list_cols'] as $label ) {
								$fields[ $field_key ]->choices[] = array(
									"text"  => $label,
									"value" => $label
								);
							}
							$fields[ $field_key ]->enableColumns = true;
						} else {
							/**
							 * Maybe JSON or PHP serialized?
							 */
							$data = $this->get_column_choices( $batch['id'], $column['column'], $tables );

							foreach ( $data as $d ) {
								if (
									is_array( $d = maybe_unserialize( $d ) )
									|| is_array( $d = json_decode( $d, true ) )
								) {
									/**
									 * Grab the first row's columns and hydrate the list columns.
									 */
									$fields[ $field_key ]->choices = array();
									if ( ! empty( $d ) && is_array( $d[0] ) ) {
										foreach ( $d[0] as $label => $_ ) {
											$fields[ $field_key ]->choices[] = array(
												"text"  => $label,
												"value" => $label
											);
										}

										$fields[ $field_key ]->enableColumns = true;
									}
								}
							}

							/*
							*/
						}
					}

					if ( in_array( $type, array( 'radio', 'select' ) ) ) {
						$choices = $this->get_column_choices( $batch['id'], $column['column'], $tables );

						$fields[ $field_key ]->choices = array();

						foreach ( $choices as $choice ) {
							$fields[ $field_key ]->choices[] = array(
								'text'  => $choice,
								'value' => $choice,
							);
						}
					}

					if ( 'multiselect' === $type ) {
						$choices = $this->get_column_choices( $batch['id'], $column['column'], $tables );

						/**
						 * The delimiter for multiselect fields.
						 *
						 * @since 2.0
						 *
						 * @param string    $delimiter The delimiter. Default: comma.
						 * @param \GF_Field $field     The multiselect field.
						 * @param array     $column    The column that is being processed.
						 * @param array     $batch     The batch.
						 */
						$delimiter = apply_filters( 'gravityview/import/column/multiselect/delimiter', ',', $fields[ $field_key ], $column, $batch );

						$all_the_choices = array();

						foreach ( $choices as $choice ) {
							$multichoices    = array_map( 'trim', explode( $delimiter, $choice ) );
							$all_the_choices = array_unique( array_merge( $all_the_choices, $multichoices ) );
						}

						$fields[ $field_key ]->choices = array();

						foreach ( $all_the_choices as $choice ) {
							$fields[ $field_key ]->choices[] = array(
								'text'  => $choice,
								'value' => $choice,
							);
						}

						$fields[ $field_key ]->storageType = 'json'; // This is only necessary for multiselect it seems
					}

					if ( 'poll' === $type ) {
						$fields[ $field_key ]['poll_field_type'] = isset( $column['meta']['type'] ) ? $column['meta']['type'] : 'radio';

						$choices = $this->get_column_choices( $batch['id'], $column['column'], $tables );

						$fields[ $field_key ]->choices = array();

						/**
						 * The unchecked values for checkbox fields.
						 *
						 * @since 2.0
						 *
						 * @param string[]  $unchecked The unchecked values.
						 * @param \GF_Field $field     The checkbox field.
						 * @param array     $column    The column that is being processed.
						 * @param array     $batch     The batch.
						 */
						$unchecked = apply_filters( 'gravityview/import/column/checkbox/unchecked', $this->default_false_values, $fields[ $field_key ], $column, $batch );

						foreach ( $choices as $choice ) {
							if ( ! in_array( Core::strtolower( $choice ), $unchecked, true ) ) {
								$fields[ $field_key ]->choices[] = array(
									'text'  => $choice,
									'value' => 'gpoll' . substr( md5( uniqid( '', true ) ), 0, 8 ),
								);
							}
						}
					}
				}

				if ( 'checkbox' === $type ) {
					if ( empty( $fields[ $field_key ]->inputs ) ) {
						$fields[ $field_key ]->inputs = array();
					}

					$id                             = sprintf( "%d.%d", $fields[ $field_key ]->id, count( $fields[ $field_key ]->inputs ) + 1 );
					$fields[ $field_key ]->inputs[] = array(
						'id'    => $id,
						'label' => $column['name'],
					);

					if ( empty( $fields[ $field_key ]->choices ) ) {
						$fields[ $field_key ]->choices = array();
					}

					$fields[ $field_key ]->choices[] = array(
						'text'  => $column['name'],
						'value' => ! empty ( $column['meta']['value'] ) ? $column['meta']['value'] : $column['name'],
					);
				}

				/**
				 * If an input is not specified and the field has multiple inputs,
				 * then fallback to 1.
				 */
				if ( is_null( $input ) && in_array( $type, $multiinput_fields, true ) ) {
					$input = 1;
				}

				/**
				 * Poll radios have no inputs. Unlike checkboxes.
				 */
				if ( in_array( $type, array( 'poll', 'quiz' ) ) && ( ! isset( $column['meta']['type'] ) || $column['meta']['type'] !== 'checkbox' ) ) {
					$input = null;
				}

				$input_id = implode( '.', array_filter( array( $fields[ $field_key ]->id, $input ), 'strlen' ) );

				if ( 'quiz' === $type ) {
					$fields[ $field_key ]->gquizFieldType = empty( $column['meta']['type'] ) ? 'radio' : $column['meta']['type'];

					if ( empty( $column['meta']['type'] ) || 'checkbox' !== $column['meta']['type'] ) {
						$fields[ $field_key ]->choices = array();

						/**
						 * Hydrate the radios and the dropdowns.
						 */
						if ( ! empty( $column['meta']['choices'] ) ) {
							foreach ( $column['meta']['choices'] as $choice ) {
								$fields[ $field_key ]->choices[] = array(
									'text'           => $choice['name'],
									'value'          => 'gquiz' . substr( md5( uniqid( '', true ) ), 0, 8 ),
									'gquizWeight'    => empty( $choice['weight'] ) ? '0' : $choice['weight'],
									'gquizIsCorrect' => ! empty( $choice['correct'] ),
								);

								if ( ! empty( $choice['weight'] ) ) {
									$fields[ $field_key ]->gquizWeightedScoreEnabled = true;
								}
							}
						}
					}
				}

				if ( 'fileupload' === $type && ! empty( $column['meta']['multiple_files_upload'] ) ) {
					$fields[ $field_key ]->multipleFiles = true;
					$fields[ $field_key ]->maxFiles      = '';
				}

				if ( $input ) {
					if ( ! $fields[ $field_key ]->inputs ) {
						$fields[ $field_key ]->inputs = array();
					}

					if ( 'quiz' === $type ) {
						/**
						 * Inputs and choices for quiz checkboxes.
						 */
						$id                             = sprintf( "%d.%d", $fields[ $field_key ]->id, count( $fields[ $field_key ]->inputs ) + 1 );
						$fields[ $field_key ]->inputs[] = array(
							'id'    => $id,
							'label' => $column['name'],
						);

						if ( ! $fields[ $field_key ]->choices ) {
							$fields[ $field_key ]->choices = array();
						}

						$fields[ $field_key ]->choices[] = array(
							'text'           => $column['name'],
							'value'          => 'gquiz' . substr( md5( uniqid( '', true ) ), 0, 8 ),
							'gquizWeight'    => empty( $column['meta']['weight'] ) ? '0' : $column['meta']['weight'],
							'gquizIsCorrect' => ! empty( $column['meta']['correct'] ),
						);

						if ( ! empty( $column['meta']['weight'] ) ) {
							$fields[ $field_key ]->gquizWeightedScoreEnabled = true;
						}
					} elseif ( $type === 'address' ) {
						if ( ! in_array( $input_id, wp_list_pluck( $fields[ $field_key ]->inputs, 'id' ), true ) ) {
							list( $_, $subinput ) = explode( '.', $input_id );

							$address_inputs = array(
								'1' => esc_html__( 'Street Address', 'gk-gravityimport' ),
								'2' => esc_html__( 'Address Line 2', 'gk-gravityimport' ),
								'3' => esc_html__( 'City', 'gk-gravityimport' ),
								'4' => esc_html__( 'State / Province / Region', 'gk-gravityimport' ),
								'5' => esc_html__( 'ZIP / Postal Code', 'gk-gravityimport' ),
								'6' => esc_html__( 'Country', 'gk-gravityimport' )
							);

							$fields[ $field_key ]->inputs[] = array(
								'id'          => $input_id,
								'label'       => $address_inputs[ $subinput ],
								'customLabel' => ( ! empty( $column['name'] ) ) ? $column['name'] : '',
							);
						}
					} elseif ( $type !== 'checkbox' ) {
						if ( ! in_array( $input_id, wp_list_pluck( $fields[ $field_key ]->inputs, 'id' ), true ) ) {
							$fields[ $field_key ]->inputs[] = array(
								'id'    => $input_id,
								'label' => ( ! empty( $column['name'] ) ) ? $column['name'] : '',
							);
						}
					}

					/**
					 * Set the parent field label.
					 */
					if ( ! empty( $column['meta']['parent_name'] ) ) {
						$fields[ $field_key ]->label = $column['meta']['parent_name'];
					}
				}

				$column['field'] = $input_id;
			}

			foreach ( $fields as &$field ) {
				// Backfill the address inputs
				if ( $field->type == 'address' ) {
					foreach ( $address_inputs as $subinput => $label ) {
						if ( ! in_array( $id = implode( '.', array( $field->id, $subinput ) ), wp_list_pluck( $field->inputs, 'id' ), true ) ) {
							$field->inputs[] = array(
								'id'    => $id,
								'label' => $label,
							);
						}
					}
					$field->inputs = wp_list_sort( $field->inputs, 'id' );
				}
			}

			$form['fields'] = array_values( $fields );
			\GFAPI::update_form( $form );

			$batch['form_id'] = $form_id;
			$batch            = Batch::update( $batch );
		} else {
			$form = \GFAPI::get_form( $batch['form_id'] );
		}

		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tables['rows']} WHERE batch_id = %d AND status = 'processing'", $batch['id'] ) );

		if ( ! $row ) {

			if ( ! $wpdb->query( $wpdb->prepare( "UPDATE {$tables['rows']} SET status = 'processing' WHERE batch_id = %d AND status = 'new' AND LAST_INSERT_ID(id) LIMIT 1", $batch['id'] ) ) ) {

				$batch['status'] = 'done';

				/**
				 * Callback on batch status updates.
				 *
				 * @param array $batch The batch.
				 */
				do_action( "gravityview/import/process/{$batch['status']}", $batch = Batch::update( $batch ) );

				if ( in_array( 'remove', $batch['flags'] ) && ! $batch['progress']['error'] && ! $batch['progress']['skipped'] ) {
					Batch::delete( $batch['id'] ); // Delete the batch if there are no errors or skips
				}

				return $batch;
			}

			$row = $wpdb->get_row( "SELECT * FROM {$tables['rows']} WHERE id = LAST_INSERT_ID();" );
		}

		$row->data = json_decode( $row->data );

		/**
		 * Conditional skips.
		 */
		if ( ! empty( $batch['conditions'] ) ) {
			if ( ! Batch::test_condition( $batch['conditions'], $row->data ) ) {
				$wpdb->update( $tables['rows'], array( 'status' => 'skipped', 'entry_id' => 0 ), array( 'id' => $row->id ) );

				/**
				 * This row is skipped due to conditional logic.
				 *
				 * @param object $row   The row.
				 * @param array  $batch The batch.
				 */
				do_action( 'gravityview/import/process/row/skipped', $row, $batch );

				$batch['progress']['skipped']++;
				return Batch::update( $batch );
			}
		}

		/**
		 * Skip empty rows.
		 */
		if ( empty( array_filter( $row->data, 'strlen' ) ) ) {
			$wpdb->update( $tables['rows'], array( 'status' => 'skipped', 'entry_id' => 0 ), array( 'id' => $row->id ) );

			do_action( 'gravityview/import/process/row/skipped', $row, $batch );

			$batch['progress']['skipped']++;
			return Batch::update( $batch );
		}


		/**
		 * Do some actual work.
		 */
		if ( ! class_exists( 'GFFormDisplay' ) ) {
			require_once \GFCommon::get_base_path() . '/form_display.php';
		}

		$single_fileupload_fields = array();

		$notifications_callback = null;
		$disable_async_notifications = null;

		$validate_callbacks = array();

		/**
		 * Override the form trash and active state, we work with all
		 * forms, and can import into inactive ones no problemo.
		 *
		 * Remove some fields that are not really relevant or impede safe import.
		 *
		 * Mock fileupload fields to accept preuploaded files.
		 *
		 * Disable notifications if needed.
		 *
		 * Remove pages.
		 *
		 * etc.
		 */
		add_filter( 'gform_pre_process', $pre_process_validate_callback = function ( $form ) use ( $batch, &$single_fileupload_fields, &$notifications_callback, &$disable_async_notifications, &$validate_callbacks ) {
			$form['is_active']    = true;
			$form['is_trash']     = false;
			$form['requireLogin'] = false;
			$form['scheduleForm'] = false;
			$form['limitEntries'] = false;

			// Evidently disables submission notifications.
			if ( ! in_array( 'notify', $batch['flags'] ) ) {
				add_filter( 'gform_disable_notification', $notifications_callback = '__return_true' );
			} else {
				// Force synchronous delivery during import. The import context (batch
				// state, temporary data) may not be available when a background queue
				// processes the notifications.
				$disable_async_notifications = static function () {
					return false;
				};

				add_filter( 'gform_is_asynchronous_notifications_enabled', $disable_async_notifications );
			}

			foreach ( $form['fields'] as $field_id => &$field ) {
				// We are not human, we will not pass the test
				if ( 'captcha' === $field->type ) {
					unset( $form['fields'][ $field_id ] );
					continue;
				}

				// Pages, ugh.
				if ( 'page' === $field->type ) {
					unset( $form['fields'][ $field_id ] );
					continue;
				}

				// Disable form state validation
				$field->allowsPrepopulate = true;

				/**
				 * Allow setting the isRequired property of this field to false.
				 *
				 * @param bool      $unrequire Allow unrequire? Default: true.
				 * @param \GF_Field $field The field.
				 * @param array     $batch The batch.
				 */
				$unrequire = apply_filters( 'gravityview/import/field/unrequire', true, $field, $batch );

				// Ignore required field settings
				if ( ( ! in_array( 'require', $batch['flags'] ) ) && $field->isRequired && $unrequire ) {
					$field->isRequired = false;
				}

				// Total should store the value without question if it's set in the schema
				if ( 'total' === $field->type ) {
					foreach ( $batch['schema'] as $rule ) {
						if ( $rule['field'] == $field->id ) {
							$number         = \GF_Fields::get_instance( 'number' );
							$number->id     = $field->id;
							$number->formId = $field->formId;
							$field          = $number;
							break;
						}
					}
				}

				if ( 'email' === $field->type ) {
					$field->emailConfirmEnabled = false;
				}

				// Strict radio matches in hard mode
				if ( 'radio' === $field->type ) {
					if ( ! $field->enableOtherChoice && ! in_array( 'soft', $batch['flags'] ) ) {
						add_filter( 'gform_field_validation', $validate_callbacks[] = function ( $result, $value, $form, $the_field ) use ( &$field, &$batch ) {
							if ( $field->id != $the_field->id ) {
								return $result;
							}

							if ( ! $value ) {
								return $result;
							}

							$values = array_map( function ( $choice ) use ( $field ) {
								return ! empty( $choice['value'] ) || $field->enableChoiceValue ? $choice['value'] : $choice['text'];
							}, $field->choices );

							/**
							 * @deprecated 2.0 Use `gravityview/import/column/radio/strict` instead.
							 */
							$strict_radio_choices = apply_filters( 'gravityview-importer/strict-mode', apply_filters( 'gravityview-importer/strict-mode/radio-choices', true ) );

							/**
							 * Suppress strict radio value validation.
							 *
							 * @since 2.0
							 *
							 * @param bool      $validate Validate. Default: true.
							 * @param \GF_Field $field    The radio field.
							 * @param array     $batch    The batch.
							 */
							if ( ! in_array( $value, $values ) && apply_filters( 'gravityview/import/column/radio/strict', $strict_radio_choices, $field, $batch ) ) {
								$result['is_valid'] = false;
								$result['message']  = sprintf( __( 'Radio choice is not one of: %s', 'gk-gravityimport' ), esc_html( implode( ', ', $values ) ) );
							}
							return $result;
						}, 10, 4 );
					}
				}

				// Strict select matches in hard mode
				if ( 'select' === $field->type && ! in_array( 'soft', $batch['flags'] ) ) {
					add_filter( 'gform_field_validation', $validate_callbacks[] = function ( $result, $value, $form, $the_field ) use ( &$field, &$batch ) {
						if ( $field->id != $the_field->id ) {
							return $result;
						}

						if ( ! $value ) {
							return $result;
						}

						$values = array_map( function ( $choice ) use ( $field ) {
							return ! empty( $choice['value'] ) || $field->enableChoiceValue ? $choice['value'] : $choice['text'];
						}, $field->choices );

						/**
						 * Suppress strict select value validation.
						 *
						 * @since 2.0
						 *
						 * @param bool      $validate Validate. Default: true.
						 * @param \GF_Field $field    The select field.
						 * @param array     $batch    The batch.
						 */
						if ( ! in_array( $value, $values ) && apply_filters( 'gravityview/import/column/select/strict', true, $field, $batch ) ) {
							$result['is_valid'] = false;
							$result['message']  = sprintf( __( 'Select choice is not one of: %s', 'gk-gravityimport' ), esc_html( implode( ', ', $values ) ) );
						}
						return $result;
					}, 10, 4 );
				}

				// Strict matches for polls and quizzes
				if ( in_array( $field->type, array( 'poll', 'quiz' ) ) ) {
					add_filter( 'gform_field_validation', $validate_callbacks[] = function ( $result, $value, $form, $the_field ) use ( &$field, &$batch ) {
						if ( $field->id != $the_field->id ) {
							return $result;
						}

						if ( ! $value ) {
							return $result;
						}

						if ( is_array( $value ) ) {
							if ( array_diff( array_filter( $value ), $values = wp_list_pluck( $field->choices, 'value' ) ) ) {
								$result['is_valid'] = false;
								$result['message']  = sprintf( __( 'Poll/Quiz choice is not one of: %s', 'gk-gravityimport' ), esc_html( implode( ', ', $values ) ) );
							}
						} else {
							if ( ! in_array( $value, $values = wp_list_pluck( $field->choices, 'value' ) ) ) {
								$result['is_valid'] = false;
								$result['message']  = sprintf( __( 'Poll/Quiz choice is not one of: %s', 'gk-gravityimport' ), esc_html( implode( ', ', $values ) ) );
							}
						}

						return $result;
					}, 10, 4 );
				}

				// Strict matches for survey
				if ( 'survey' === $field->type && ! in_array( $field->inputType, array( 'text', 'textarea', 'rank' ) ) ) {
					add_filter( 'gform_field_validation', $validate_callbacks[] = function ( $result, $value, $form, $the_field ) use ( &$field, &$batch ) {
						if ( $field->id != $the_field->id ) {
							return $result;
						}

						if ( ! $value ) {
							return $result;
						}

						if ( $field->inputType == 'likert' && $field->gsurveyLikertEnableMultipleRows ) {
							// Populate all the choice column/row combinations
							$choices = array();
							foreach ( $field->gsurveyLikertRows as $row ) {
								foreach ( $field->choices as $choice ) {
									$choices[] = array( 'value' => "{$row['value']}:{$choice['value']}" );
								}
							}
							$field->choices = $choices;
						}

						if ( is_array( $value ) ) {
							if ( array_diff( array_filter( $value ), $values = wp_list_pluck( $field->choices, 'value' ) ) ) {
								$result['is_valid'] = false;
								$result['message']  = sprintf( __( 'Survey choice is not one of: %s', 'gk-gravityimport' ), esc_html( implode( ', ', $values ) ) );
							}
						} else {
							if ( ! in_array( $value, $values = wp_list_pluck( $field->choices, 'value' ) ) ) {
								$result['is_valid'] = false;
								$result['message']  = sprintf( __( 'Survey choice is not one of: %s', 'gk-gravityimport' ), esc_html( implode( ', ', $values ) ) );
							}
						}

						return $result;
					}, 10, 4 );
				}

				// Strict multiselect matches in hard mode
				if ( 'multiselect' === $field->type ) {
					if ( ! in_array( 'soft', $batch['flags'] ) ) {
						add_filter( 'gform_field_validation', $validate_callbacks[] = function ( $result, $value, $form, $the_field ) use ( &$field, &$batch ) {
							if ( $field->id != $the_field->id ) {
								return $result;
							}

							if ( ! $value ) {
								return $result;
							}

							$values = array_map( function ( $choice ) use ( $field ) {
								return ! empty( $choice['value'] ) ? $choice['value'] : '';
							}, $field->choices );

							$values = array_merge( $values, array_map( function ( $choice ) use ( $field ) {
								return ! empty( $choice['text'] ) ? $choice['text'] : '';
							}, $field->choices ) );

							$values = array_unique( array_filter( $values ) );

							/**
							 * Suppress strict multiselect value validation.
							 *
							 * @since 2.0
							 *
							 * @param bool      $validate Validate. Default: true.
							 * @param \GF_Field $field    The multiselect field.
							 * @param array     $batch    The batch.
							 */
							if ( array_diff( array_filter( $value ), $values ) && apply_filters( 'gravityview/import/column/multiselect/strict', true, $field, $batch ) ) {
								$result['is_valid'] = false;
								$result['message']  = sprintf( __( 'Multiselect choice is not one of: %s', 'gk-gravityimport' ), esc_html( implode( ', ', $values ) ) );
							}
							return $result;
						}, 10, 4 );
					}
				}

				if ( 'fileupload' === $field->type && ! $field->multipleFiles ) {
					$single_fileupload_fields[] = $field->id;
					$field->multipleFiles       = true;
					$field->maxFiles            = 1;
				}

				/**
				 * Map the signature type into a simple text type.
				 * This helps store it without much ado (curve data, ugh).
				 */
				if ( 'signature' === $field->type ) {
					$textfield = new \GF_Field_Text( array(
						'id'     => $field->id,
						'formId' => $field->formId,
					) );

					$field = $textfield;
				}

				// Disable field conditional logic when the flag is set in the UI.
				if ( in_array( 'ignorefieldconditionallogic', $batch['flags'] ) && ! empty( $field->conditionalLogic ) ) {
					$field->conditionalLogic = false;
				}
			}

			return $form;
		} );

		$post  = $_POST;
		$files = $_FILES;

		/**
		 * Build the request data.
		 */
		$_POST = array(
			'is_submit_' . $batch['form_id'] => true,
			'gform_uploaded_files'           => array(),
			'gform_submit'                   => $batch['form_id'],
		);

		$update_entry_properties = array();
		$update_entry_meta       = array();

		$form = \GFAPI::get_form( $batch['form_id'] );

		$has_fields     = false;
		$has_user_agent = false;

		$datetime_partials    = array();
		$fileupload_keeplinks = array();
		$consent_partials     = array();
		$product_partials     = array();
		$applied_coupons      = array();
		$notes                = array();

		foreach ( $batch['schema'] as $column ) {
			if ( ! isset( $row->data[ $column['column'] ] ) || ( ! is_numeric( $row->data[ $column['column'] ] ) && empty( $row->data[ $column['column'] ] ) ) ) {
				$row->data[ $column['column'] ] = '';
			}

			/**
			 * Save the original data for later reuse.
			 */
			$row_data = $row->data[ $column['column'] ];

			if ( Core::is_entry_column( $column['field'] ) ) {
				if ( 'id' === $column['field'] ) {
					if ( $update_id = $row->data[ $column['column'] ] ) {
						if ( is_wp_error( $entry = \GFAPI::get_entry( $update_id ) ) || $entry['form_id'] != $form['id'] ) {
							$error = __( 'ID does not exist for form', 'gk-gravityimport' );

							return $this->handle_row_processing_error( $error, $row, $batch );
						}
					}
				} else {
					if ( in_array( $column['field'], array( 'date_created', 'date_updated', 'payment_date' ) ) ) {
						if ( isset( $datetime_partials[ $column['column'] ] ) ) {
							$partial = $datetime_partials[ $column['column'] ];
						} else {
							$partial = null;
						}

						$row->data[ $column['column'] ] = self::transform_datetime( 'date', $row->data[ $column['column'] ], isset( $column['meta']['datetime_format'] ) ? $column['meta']['datetime_format'] : null, 'Y-m-d H:i:s', $partial );

						// Convert entry date properties to UTC timezone since we directly update them in the database and not via the \GF_Query call
						if ( ! isset( $column['meta']['date_timezone'] ) || 'UTC' !== $column['meta']['date_timezone'] ) {
							$row->data[ $column['column'] ] = get_gmt_from_date( $row->data[ $column['column'] ] );
						}

						$datetime_partials[ $column['column'] ] = $row->data[ $column['column'] ];
					}

					/**
					 * Allow the transformation of data for a column.
					 *
					 * @since 2.0
					 *
					 * @param string $data   The cell data.
					 * @param array  $column The column schema definition.
					 * @param array  $batch  The batch.
					 */
					$row->data[ $column['column'] ] = apply_filters( 'gravityview/import/column/data', $row->data[ $column['column'] ], $column, $batch );

					$update_entry_properties[ $column['field'] ] = $row->data[ $column['column'] ];

					if ( 'user_agent' === $column['field'] ) {
						$has_user_agent = true; // The User Agent has been supplied.
					}
				}
			} else if ( 'notes' === $column['field'] ) {

				$note_string = wp_specialchars_decode( $row->data[ $column['column'] ] );

				/** @see filter_gform_leads_before_export_add_notes */
				$note_string = str_replace( '\,', ',', $note_string );

				$notes = @json_decode( $note_string );

				if ( ! is_array( $notes ) ) {
					if ( ! in_array( 'soft', $batch['flags'] ) ) {
						$batch['status'] = 'error';
						$batch['error']  = sprintf( __( 'Entry Notes are not properly formatted.', 'gk-gravityimport' ), $row->id );
						$notes           = array();

						do_action( "gravityview/import/process/{$batch['status']}", $batch = Batch::update( $batch ) );
					}
				}
			} else {
				$field = \GFFormsModel::get_field( $form, $column['field'] );

				if ( $field ) {
					if ( in_array( $field->type, array( 'date' ) ) ) {
						$row->data[ $column['column'] ] = self::transform_datetime( 'date', $row->data[ $column['column'] ], isset( $column['meta']['datetime_format'] ) ? $column['meta']['datetime_format'] : null, 'Y-m-d', null );
					}

					if ( in_array( $field->type, array( 'time' ) ) ) {
						$format                         = $field->timeFormat === '24' ? 'H i' : 'h i a';
						$row->data[ $column['column'] ] = self::transform_datetime( 'time', $row->data[ $column['column'] ], isset( $column['meta']['datetime_format'] ) ? $column['meta']['datetime_format'] : null, $format, null );
						$row->data[ $column['column'] ] = explode( ' ', $row->data[ $column['column'] ] );
					}
				}

				/**
				 * Allow the transformation of data for a column.
				 *
				 * @since 2.0
				 *
				 * @param string $data   The cell data.
				 * @param array  $column The column schema definition.
				 * @param array  $batch  The batch.
				 */
				$row->data[ $column['column'] ] = apply_filters( 'gravityview/import/column/data', $row->data[ $column['column'] ], $column, $batch );

				if ( ! empty( $column['meta']['is_meta'] ) ) {
					$update_entry_meta[] = array( $column['field'], $row->data[ $column['column'] ] );
				} else if ( $field ) {
					// @todo: move to a file/flow of its own soon
					if ( 'fileupload' === $field->get_input_type() ) {
						if ( ! isset( $column['flags'] ) || ! in_array( 'keeplinks', $column['flags'] ) ) {
							if ( $field->multipleFiles && ! in_array( $field->id, $single_fileupload_fields ) ) {
								$urls = @json_decode( $row->data[ $column['column'] ] );

								if ( null === $urls && JSON_ERROR_NONE !== json_last_error() ) {
									$urls = explode( ',', $row->data[ $column['column'] ] );
								}

								$urls = is_array( $urls ) ? $urls : array( $urls );
							} else {
								$urls = array( $row->data[ $column['column'] ] );
							}

							$urls = array_filter( array_map( 'trim', $urls ) );

							foreach ( $urls as $url ) {
								/**
								 * Path to local file that can be moved to uploads.
								 *
								 * @since 2.0
								 *
								 * @param string    $source The local file path, that can be moved.
								 * @param string    $url    The current cell value.
								 * @param \GF_Field $field  The upload field.
								 * @param array     $column The column that is being processed.
								 * @param array     $batch  The batch.
								 */
								if ( ! $source = apply_filters( 'gravityview/import/column/file/source', '', $url, $field, $column, $batch ) ) {
									wp_mkdir_p( $source_dir = \GFFormsModel::get_upload_path( $batch['form_id'] ) . '/tmp/' );
									$response = wp_remote_get( $url, array(
										'stream'   => true,
										'filename' => $source = $source_dir . wp_generate_password( 32, false ),
									) );

									if ( is_wp_error( $response ) || ( wp_remote_retrieve_response_code( $response ) != 200 ) ) {
										if ( is_wp_error( $response ) ) {
											$wpdb->update( $tables['rows'], array( 'status' => 'error', 'error' => $error = $response->get_error_message() ), array( 'id' => $row->id ) );
										} else {
											$wpdb->update( $tables['rows'], array( 'status' => 'error', 'error' => $error = sprintf( 'HTTP %d', wp_remote_retrieve_response_code( $response ) ) ), array( 'id' => $row->id ) );
										}

										do_action( 'gravityview/import/process/row/error', $row, $error, $batch );

										if ( ! in_array( 'soft', $batch['flags'] ) ) {
											$batch['status'] = 'error';
											$batch['error']  = sprintf( __( 'Failed to process row ID %d', 'gk-gravityimport' ), $row->id );

											do_action( "gravityview/import/process/{$batch['status']}", $batch = Batch::update( $batch ) );
										}

										$_POST  = $post;
										$_FILES = $files;

										return Batch::update( $batch );
									}
								}

								/**
								 * The name of the file.
								 *
								 * @since 2.0
								 *
								 * @param string    $name   The name of the file.
								 * @param string    $url    The current cell value.
								 * @param \GF_Field $field  The upload field.
								 * @param array     $column The column that is being processed.
								 * @param array     $batch  The batch.
								 */
								if ( ! $filename = apply_filters( 'gravityview/import/column/file/name', '', $url, $field, $column, $batch ) ) {
									$url_parts = parse_url( $url );

									if ( ! empty( $url_parts['query'] ) ) {
										parse_str( $url_parts['query'], $query );
										if ( ! empty( $query['gf-download'] ) ) {
											$filename = basename( $query['gf-download'] );
										}
									}

									if ( ! $filename ) {
										$filename = basename( $url_parts['path'] );
									}
								}

								if ( empty( $_POST['gform_uploaded_files']["input_{$field->id}"] ) ) {
									$_POST['gform_uploaded_files']["input_{$field->id}"] = array();
								}

								$_POST['gform_uploaded_files']["input_{$field->id}"][] = array(
									'temp_filename'     => basename( $source ),
									'uploaded_filename' => $filename,
								);

								if ( 1 === (int) $field->maxFiles ) {
									$_FILES["input_{$field->id}"] = array(
										'tmp_name' => $source,
										'name'     => $filename,
										'error'    => UPLOAD_ERR_NO_FILE,
									);
								}

								$has_fields = true;
							}
						} else {
							// Links are kept as is.
							$fileupload_keeplinks[ $field->id ] = $row->data[ $column['column'] ];

							if ( empty( $_POST['gform_uploaded_files']["input_{$field->id}"] ) ) {
								$_POST['gform_uploaded_files']["input_{$field->id}"] = array();
							}

							$_POST['gform_uploaded_files']["input_{$field->id}"][] = array(
								'temp_filename'     => wp_generate_password( 16, false ),
								'uploaded_filename' => wp_generate_password( 16, false ),
							);

							$has_fields = true;
						}
					} elseif ( 'consent' === $field->type ) {
						list( $field_id, $input_id ) = explode( '.', $column['field'] );

						if ( ! isset( $consent_partials[ $field_id ] ) ) {
							$consent_partials[ $field_id ] = array(
								'1' => false,
								'2' => $field->checkboxLabel,
								'3' => \GFFormsModel::get_latest_form_revisions_id( $form['id'] ),
								'_' => $field->description, // The original description
							);
						}

						switch ( $input_id ):
							case '1':
								/**
								 * The text for which the field is considered checked.
								 *
								 * @since 2.0
								 *
								 * @param string    $checked The checked text. Default: Checked in the current locale.
								 * @param \GF_Field $field   The consent field.
								 * @param array     $column  The column that is being processed.
								 * @param array     $batch   The batch.
								 */
								$checked = apply_filters( 'gravityview/import/column/consent/checked', __( 'Checked', 'gk-gravityimport' ), $field, $column, $batch );
								if ( $is_checked = ( $row->data[ $column['column'] ] === $checked ) ) {
									$_POST[ 'input_' . $field_id . "_$input_id" ] = '1';
									$consent_partials[ $field_id ][ $input_id ]   = true;

									$has_fields = true;
								}
								break;
							case '2':
								$_POST[ 'input_' . $field_id . "_$input_id" ] = $row->data[ $column['column'] ];
								$has_fields                                   = true;
								break;
							case '3':
								if ( $description = $row->data[ $column['column'] ] ) {
									if ( $field->description !== $description ) {
										// Find revision number for the description
										$revisions_table_name = \GFFormsModel::get_form_revisions_table_name();
										$revision_id          = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $revisions_table_name WHERE display_meta LIKE %s AND form_id = %d",
											'%' . json_encode( $wpdb->esc_like( $description ) ) . '%',
											$form['id']
										) );

										if ( ! $revision_id ) {
											// Create a revision and restore the form
											foreach ( $form['fields'] as &$_field ) {
												if ( $field->id == $_field->id ) {
													$_field->description = $description;
													\GFAPI::update_form( $form );
													$revision_id         = \GFFormsModel::get_latest_form_revisions_id( $form['id'] );
													$_field->description = $consent_partials[ $field_id ]['_'];
													\GFAPI::update_form( $form );
													$consent_partials[ $field_id ]['3'] = \GFFormsModel::get_latest_form_revisions_id( $form['id'] );
													break;
												}
											}
										}

										$_POST[ 'input_' . $field_id . "_$input_id" ] = $revision_id;
										$has_fields                                   = true;
									}
								}
								break;
						endswitch;
					} elseif ( 'list' === $field->type ) {
						$_POST[ 'input_' . $column['field'] ] = array();

						if (
							is_array( $data = maybe_unserialize( $row->data[ $column['column'] ] ) )
							|| is_array( $data = json_decode( $row->data[ $column['column'] ], true ) )
						) {
							foreach ( $data as $column_or_columns ) {
								if ( is_array( $column_or_columns ) ) {
									foreach ( $column_or_columns as $c ) {
										$_POST[ 'input_' . $column['field'] ][] = $c;
									}
								} else {
									$_POST[ 'input_' . $column['field'] ][] = $column_or_columns;
								}
							}
						} else {
							if ( ! $field->enableColumns ) {
								if ( $data = explode( '|', $row->data[ $column['column'] ] ) ) {
									$_POST[ 'input_' . $column['field'] ] = $data;
								}
							} elseif ( ! empty( $column['meta']['list_rows'] ) ) {
								foreach ( $column['meta']['list_rows'] as $row_column ) {
									if ( isset( $row->data[ $row_column ] ) ) {
										if ( $data = explode( '|', $row->data[ $row_column ] ) ) {
											$_POST[ 'input_' . $column['field'] ] = array_merge( $_POST[ 'input_' . $column['field'] ], $data );
										} else {
											$_POST[ 'input_' . $column['field'] ][] = '';
										}
									}
								}
							}
						}

						$has_fields = $has_fields || ! empty( $_POST[ 'input_' . $column['field'] ] );
					} elseif ( 'multiselect' === $field->type ) {
						/**
						 * Documented elsewhere.
						 */
						$delimiter = apply_filters( 'gravityview/import/column/multiselect/delimiter', ',', $field, $column, $batch );
						if ( $data = array_filter( explode( $delimiter, $row->data[ $column['column'] ] ) ) ) {
							$_POST[ 'input_' . $column['field'] ] = $data;
							$has_fields                           = true;
						}
					} elseif ( 'signature' === $field->type ) {
						if ( ! class_exists( '\GFSignature' ) ) {
							$error = __( 'Gravity Forms Signature Addon is not active.', 'gk-gravityimport' );

							$_POST  = $post;
							$_FILES = $files;

							return $this->handle_row_processing_error( $error, $row, $batch );
						}

						$url = $row->data[ $column['column'] ];

						if ( ! $url ) {
							continue;
						}

						wp_mkdir_p( $signatures_dir = \GFSignature::get_signatures_folder() );

						/**
						 * The name of the signature file.
						 *
						 * @since 2.0
						 *
						 * @param string    $name   The name of the file.
						 * @param string    $url    The current cell value.
						 * @param \GF_Field $field  The signature field.
						 * @param array     $column The column that is being processed.
						 * @param array     $batch  The batch.
						 */
						if ( ! $filename = apply_filters( 'gravityview/import/column/signature/name', '', $url, $field, $column, $batch ) ) {

							$url_parts = parse_url( $url );

							if ( ! empty( $url_parts['query'] ) ) {
								parse_str( $url_parts['query'], $query );
								if ( ! empty( $query['signature'] ) ) {
									$filename = $query['signature'] . '.png';
								}
							}

							if ( ! $filename || file_exists( $signatures_dir . $filename ) ) {

								// Check to see if filename exists. If so, generate a different unique filename.
								while ( file_exists( $signatures_dir . ( $filename = ( uniqid( '', true ) . '.png' ) ) ) ) {
								}
							}

							$filename = sanitize_file_name( $filename );
						}

						/**
						 * Path to local file that can be moved to signatures.
						 *
						 * @since 2.0
						 *
						 * @param string    $source The local file path, that can be moved.
						 * @param string    $url    The current cell value.
						 * @param \GF_Field $field  The signature field.
						 * @param array     $column The column that is being processed.
						 * @param array     $batch  The batch.
						 */
						if ( ! $source = apply_filters( 'gravityview/import/column/signature/source', '', $url, $field, $column, $batch ) ) {

							$response = wp_remote_get( $url, array(
								'stream'   => true,
								'filename' => $signatures_dir . $filename,
							) );

							if ( is_wp_error( $response ) || ( wp_remote_retrieve_response_code( $response ) != 200 ) ) {
								if ( is_wp_error( $response ) ) {
									$error = $response->get_error_message();
								} else {
									$error = sprintf( 'HTTP %d', wp_remote_retrieve_response_code( $response ) );
								}

								$_POST  = $post;
								$_FILES = $files;

								return $this->handle_row_processing_error( $error, $row, $batch );
							}
						} else {
							rename( $source, $signatures_dir . $filename );
						}

						$_POST[ 'input_' . $column['field'] ] = $filename;
						$has_fields                           = true;

					} elseif ( 'checkbox' === $field->type ) {
						/**
						 * Documented elsewhere.
						 */
						$unchecked = apply_filters( 'gravityview/import/column/checkbox/unchecked', $this->default_false_values, $field, $column, $batch );

						/**
						 * Search for exact matches first.
						 */
						$choices = array();

						$is_single_cell_input = false === strpos( $column['field'], '.' );

						foreach ( $field->inputs as $input ) {
							if ( $input['id'] == $column['field'] || $is_single_cell_input ) {
								$choices = array_merge( wp_list_pluck( $field->choices, 'text' ), wp_list_pluck( $field->choices, 'value' ) );
								$choices = array_unique( array_map( '\GravityKit\GravityImport\Core::strtolower', $choices ) );
							}
						}

						/**
						 * Blank out as unchecked unless exact falsey match.
						 */
						if ( ! in_array( Core::strtolower( $row->data[ $column['column'] ] ), $choices ) && in_array( Core::strtolower( $row->data[ $column['column'] ] ), $unchecked ) ) {
							$row->data[ $column['column'] ] = '';
						} else {
							foreach ( $field->inputs as $input ) {
								if ( $input['id'] == $column['field'] || $is_single_cell_input ) {
									foreach ( $field->choices as $choice ) {
										if ( $choice['text'] == $input['label'] ) {
											if ( Core::strtolower( $row->data[ $column['column'] ] ) != Core::strtolower( $choice['value'] ) &&
											     ! in_array( Core::strtolower( $row->data[ $column['column'] ] ), $this->default_true_values ) ) {
												continue;
											}

											$row->data[ $column['column'] ] = $choice['value'];

											$_POST[ 'input_' . str_replace( '.', '_', $input['id'] ) ] = $row->data[ $column['column'] ];
											$has_fields                                                = true;
										}
									}
								}
							}
						}
					} elseif ( 'product' === $field->type ) {
						if ( $field->inputs ) {
							list( $field_id, $input_id ) = explode( '.', $column['field'] );
						} else {
							$field_id = $column['field'];
						}

						if ( ! isset( $product_partials[ $field_id ] ) ) {
							$product_partials[ $field_id ] = true;

							$_POST[ 'input_' . $field_id . '_1' ] = $field->label;
							$_POST[ 'input_' . $field_id . '_2' ] = $field->basePrice;

							if ( ! $field->disableQuantity ) {
								$_POST[ 'input_' . $field_id . '_3' ] = '';
							}
						}

						$_POST[ 'input_' . str_replace( '.', '_', $column['field'] ) ] = $row->data[ $column['column'] ];
						$has_fields                                                    = true;
					} elseif ( 'coupon' == $field->type ) {
						if ( ! isset( $applied_coupons[ $field->id ] ) ) {
							$applied_coupons[ $field->id ] = array();
						}

						if ( ! class_exists( '\GFCoupons' ) ) {
							$error = __( 'Gravity Forms Coupons Addon is not active.', 'gk-gravityimport' );

							$_POST  = $post;
							$_FILES = $files;

							return $this->handle_row_processing_error( $error, $row, $batch );
						}

						$coupons = \GFCoupons::get_instance();

						$error = false;

						if ( ! empty( $row->data[ $column['column'] ] ) ) {
							if ( preg_match_all( '#\((.+?):#', $row->data[ $column['column'] ], $matches ) ) {
								foreach ( $matches[1] as $coupon ) {
									if ( $coupon = $coupons->get_config( $form, $coupon ) ) {
										// @todo Do we need to sort these flat first then percentage?
										$applied_coupons[ $field->id ][ $coupon['meta']['couponCode'] ] = $coupon['meta'];
									} else {
										$error = true;
									}
								}
							} else {
								$error = true;
							}

							if ( $error ) {
								$error = __( 'Invalid coupon code', 'gk-gravityimport' );

								$_POST  = $post;
								$_FILES = $files;

								return $this->handle_row_processing_error( $error, $row, $batch );
							}
						}
					} elseif ( in_array( $field->type, array( 'poll', 'quiz' ) ) ) {
						/**
						 * Documented elsewhere.
						 */
						$unchecked = apply_filters( 'gravityview/import/column/checkbox/unchecked', $this->default_false_values, $field, $column, $batch );

						if ( in_array( Core::strtolower( $row->data[ $column['column'] ] ), $unchecked ) ) {
							$row->data[ $column['column'] ] = '';
						} else {
							// Convert from text to value if possible
							$names = wp_list_pluck( $field->choices, 'text' );
							if ( ( $i = array_search( $row->data[ $column['column'] ], $names, true ) ) === false ) {
								$_POST[ 'input_' . str_replace( '.', '_', $column['field'] ) ] = '#'; // No exact matches, validate into an error after submission
							} else {
								$_POST[ 'input_' . str_replace( '.', '_', $column['field'] ) ] = $field->choices[ $i ]['value'];
							}

							$has_fields = true;
						}
					} elseif ( 'survey' === $field->type ) {
						/**
						 * Documented elsewhere.
						 */
						$unchecked = apply_filters( 'gravityview/import/column/checkbox/unchecked', $this->default_false_values, $field, $column, $batch );

						$input_type = $field->inputType;

						if ( $input_type == 'likert' && $field->gsurveyLikertEnableMultipleRows ) {
							$input_type .= '-multi'; // Multilikerts :)
						}

						switch ( $input_type ):
							case 'textarea':
							case 'text':
								$_POST[ 'input_' . str_replace( '.', '_', $column['field'] ) ] = $row->data[ $column['column'] ];
								$has_fields                                                    = $has_fields || ! empty( $row->data[ $column['column'] ] );
								break;
							case 'likert':
							case 'radio':
							case 'select':
							case 'rating':
								// Convert from text to value if possible
								$names = wp_list_pluck( $field->choices, 'text' );

								if ( ( $i = array_search( $row->data[ $column['column'] ], $names, true ) ) === false ) {
									$_POST[ 'input_' . str_replace( '.', '_', $column['field'] ) ] = '#'; // No exact matches, validate into an error after submission
								} else {
									$_POST[ 'input_' . str_replace( '.', '_', $column['field'] ) ] = $field->choices[ $i ]['value'];
									$has_fields                                                    = true;
								}

								break;
							case 'checkbox':
								$names = wp_list_pluck( $field->choices, 'text' );

								if ( in_array( Core::strtolower( $row->data[ $column['column'] ] ), $unchecked ) ) {
									$_POST[ 'input_' . str_replace( '.', '_', $column['field'] ) ] = '';
								} elseif ( ( $i = array_search( $row->data[ $column['column'] ], $names, true ) ) === false ) {
									$_POST[ 'input_' . str_replace( '.', '_', $column['field'] ) ] = '#'; // No exact matches, validate into an error after submission
								} else {
									$_POST[ 'input_' . str_replace( '.', '_', $column['field'] ) ] = $field->choices[ $i ]['value'];
									$has_fields                                                    = true;
								}

								break;
							case 'likert-multi':
								$likert_row = '';
								foreach ( $field->inputs as $input ) {
									if ( $input['id'] == $column['field'] ) {
										$likert_row = $input['name'];
										break;
									}
								}

								$names = wp_list_pluck( $field->choices, 'text' );

								if ( in_array( Core::strtolower( $row->data[ $column['column'] ] ), $unchecked ) ) {
									$_POST[ 'input_' . str_replace( '.', '_', $column['field'] ) ] = '';
								} elseif ( ( $i = array_search( $row->data[ $column['column'] ], $names, true ) ) === false ) {
									$_POST[ 'input_' . str_replace( '.', '_', $column['field'] ) ] = '#'; // No exact matches, validate into an error after submission
								} else {
									$_POST[ 'input_' . str_replace( '.', '_', $column['field'] ) ] = "$likert_row:{$field->choices[ $i ]['value']}";
									$has_fields                                                    = true;
								}

								break;
							case 'rank':
								$re_choice = implode( '|', array_map( 'preg_quote', wp_list_pluck( $field->choices, 'text' ) ) );
								$values    = array();
								if ( preg_match_all( "#\d\. ($re_choice),?#", $row->data[ $column['column'] ], $matches ) ) {
									foreach ( $matches[1] as $match ) {
										foreach ( $field->choices as $choice ) {
											if ( $match == $choice['text'] ) {
												$values[] = $choice['value'];
												break;
											}
										}
									}
								}
								$_POST[ 'input_' . str_replace( '.', '_', $column['field'] ) ] = $has_rank = implode( ',', $values );
								$has_fields                                                    = $has_fields || $has_rank;
								break;
						endswitch;
					} elseif ( '' === $row->data[ $column['column'] ] ) {
						$use_default = isset( $column['flags'] ) && in_array( 'default', $column['flags'] );

						/**
						 * @deprecated 2.0 Use `gravityview/import/column/default` instead.
						 */
						$use_default = apply_filters( 'gravityview-importer/use-default-value', $use_default );

						/**
						 * Whether to use the default field value on an empty cell or not.
						 *
						 * @since 2.0
						 *
						 * @param bool  $use_default Use or not. Default: the value of the `default` column flat (usually false).
						 * @param array $column      The column.
						 * @param array $batch       The batch.
						 */
						$use_default = apply_filters( 'gravityview/import/column/default', $use_default, $column, $batch );

						if ( $use_default ) {
							$_POST[ 'input_' . str_replace( '.', '_', $column['field'] ) ] = $field->defaultValue;
							$has_fields                                                    = true;
						} else {
							$_POST[ 'input_' . str_replace( '.', '_', $column['field'] ) ] = ''; // Empty
						}
					} else {
						$_POST[ 'input_' . str_replace( '.', '_', $column['field'] ) ] = $row->data[ $column['column'] ];
						$has_fields                                                    = $has_fields || ! empty( $row->data[ $column['column'] ] ) || ! empty( trim( $row->data[ $column['column'] ] ) );
					}
				} else {
					$error = sprintf( _x( 'Form field with ID %s does not exist', '%s is replaced with field ID', 'gk-gravityimport' ), $column['field'] );

					return $this->handle_row_processing_error( $error, $row, $batch );
				}

				/**
				 * Hydrate the partial consents.
				 */
				foreach ( $consent_partials as $field_id => $inputs ) {
					if ( $inputs['1'] ) {
						if ( empty( $_POST[ 'input_' . $field_id . "_2" ] ) ) {
							$_POST[ 'input_' . $field_id . "_2" ] = $inputs['2'];
							$has_fields                           = true;
						}

						if ( empty( $_POST[ 'input_' . $field_id . "_3" ] ) ) {
							$_POST[ 'input_' . $field_id . "_3" ] = $inputs['3'];
							$has_fields                           = true;
						}
					} else {
						unset( $_POST[ 'input_' . $field_id . "_2" ] );
						unset( $_POST[ 'input_' . $field_id . "_3" ] );
					}
				}

				/**
				 * Collect the coupons.
				 */
				foreach ( $applied_coupons as $field_id => $coupons ) {
					if ( $coupons ) {
						$_POST["gf_coupons_$field_id"] = json_encode( $coupons );
						$_POST["input_$field_id"]      = implode( ',', wp_list_pluck( $coupons, 'couponCode' ) );

						$has_fields = true;
					}
				}
			}

			/**
			 * Restore original data.
			 */
			$row->data[ $column['column'] ] = $row_data;
		}

		add_filter( 'gform_suppress_confirmation_redirect', '__return_true' );

		add_filter( 'gravityview/approve_entries/after_submission', '__return_false' );

		add_filter( 'gform_save_field_value', $save_field_value_upload_single_callback = function ( $value, $entry, $field, $form, $input_id ) use ( &$single_fileupload_fields ) {
			foreach ( $single_fileupload_fields as $field_id ) {
				/**
				 * Single fileupload fields are turned into multiple ones in order to leverage
				 * the preupload mechanism in GravityForms (without having to deal with $_FILES directly.
				 *
				 * Such fields are marked and their values are unserialized before storage to conform to
				 * the single upload storage format.
				 */
				if ( $field_id == $field->id && $value ) {
					$value = json_decode( $value );
					return array_pop( $value );
				}
			}

			return $value;
		}, 10, 5 );

		add_filter( 'gform_validation', $file_upload_keeplinks_validate = function ( $validation_result ) use ( $fileupload_keeplinks ) {
			// GF 2.7+ validates file uploads (https://github.com/gravityforms/gravityforms/commit/9ca22da193c1252c707e22a7a3cf3152e53f6aa2)
			// by checking for file existence and there is no filter to override this behavior. As a workaround, we should remove failed validation
			// from file upload fields for which "keeplinks" flag is set.

			if ( $validation_result['is_valid'] || ( ! $validation_result['is_valid'] && empty( $fileupload_keeplinks ) ) ) {
				return $validation_result;
			}

			$invalid_field_count = 0;

			foreach ( $validation_result['form']['fields'] as $field ) {
				if ( ! $field->failed_validation ) {
					continue;
				}

				if ( ! in_array( $field->id, array_keys( $fileupload_keeplinks ) ) ) {
					$invalid_field_count++;

					continue;
				}

				$field->failed_validation  = false;
				$field->validation_message = '';
			}

			if ( ! $invalid_field_count ) {
				$validation_result['is_valid'] = true;
			}

			return $validation_result;
		} );

		add_filter( 'gform_save_field_value', $save_field_value_fileupload_keeplinks = function ( $value, $entry, $field, $form, $input_id ) use ( &$fileupload_keeplinks ) {
			if ( in_array( $field->id, array_keys( $fileupload_keeplinks ) ) ) {
				return $fileupload_keeplinks[ $field->id ];
			}

			return $value;
		}, 10, 5 );

		/**
		 * Only process feeds that were specifically set.
		 */
		add_filter( 'gform_addon_pre_process_feeds', $pre_process_feeds = function ( $feeds, $entry, $form ) use ( $batch ) {
			$_feeds = array();

			if ( ! empty( $batch['feeds'] ) ) {
				foreach ( $batch['feeds'] as $feed_id ) {
					foreach ( $feeds as $feed ) {
						if ( $feed['id'] == $feed_id ) {
							$_feeds[] = $feed;
						}
					}
				}
			}

			return $_feeds;
		}, 10, 3 );

		try {
			if ( empty( $has_fields ) ) {
				/**
				 * Some fields have not been set. The form will return with an error
				 * claiming that "At least one field must be filled out".
				 * See: \GFFormDisplay::is_form_empty
				 */
				add_filter( 'gform_validation', $validation_empty_fields_callback = function ( $validation_result ) {
					$validation_result['is_valid'] = true;
					return $validation_result;
				} );
			}

			/**
			 * Global validation overrides.
			 */
			add_filter( 'gform_validation', $global_validation_callback = function ( $validation_result ) use ( $batch, $row ) {
				/**
				 * Suppress global validation.
				 *
				 * @since 2.0
				 *
				 * @param bool   $validate          Whether to validate or not. Default: true; validate.
				 * @param array  $validation_result The current validation result.
				 * @param object $row               The row.
				 * @param array  $batch             The batch.
				 */
				if ( in_array( 'valid', $batch['flags'] ) || ! apply_filters( 'gravityview/import/entry/validate', true, $validation_result, $row, $batch ) ) {
					$validation_result['is_valid'] = true;
				}

				return $validation_result;
			} );

			$global_field_validation_callbacks = array();
			foreach ( $form['fields'] as &$field ) {
				add_filter( 'gform_field_validation', $global_field_validation_callbacks[] = function ( $validation_result, $value, $form, $the_field ) use ( $batch, $row ) {
					/**
					 * Suppress global validation.
					 *
					 * @param boolean $validate          Whether to validate or not. Default: true; validate.
					 * @param array  $validation_result The current validation result.
					 * @param object $row               The row.
					 * @param array  $batch             The batch.
					 */
					if ( ! apply_filters( 'gravityview/import/field/validate', true, $validation_result, $the_field, $row, $batch ) ) {
						$validation_result['is_valid'] = true;
					}

					return $validation_result;
				}, 10, 4 );
			}

			\GFCache::flush();
			\GFFormsModel::flush_current_forms();
			\GFFormsModel::flush_current_lead();
			\GFFormDisplay::$submission = null;

			$_POST['gform_uploaded_files'] = json_encode( $_POST['gform_uploaded_files'] );

			// Encode List field arrays to JSON strings (GF expects JSON from browser submissions).
			// Only encode if GF will decode: is_administrative() && allowsPrepopulate.
			foreach ( $form['fields'] as $field ) {
				if ( 'list' === $field->type && $field->is_administrative() && $field->allowsPrepopulate ) {
					$input_name = 'input_' . $field->id;
					if ( isset( $_POST[ $input_name ] ) && is_array( $_POST[ $input_name ] ) ) {
						$_POST[ $input_name ] = json_encode( $_POST[ $input_name ] );
					}
				}
			}

			/**
			 * @todo make sure anything that's not been submitted in patch-mode stays there
			 */

			$remove_submit_button_logic = function ( $form ) {

				unset( $form['button']['conditionalLogic'] );

				return $form;
			};

			add_filter( "gform_form_post_get_meta_{$batch['form_id']}", $remove_submit_button_logic );

			\GFFormDisplay::process_form( $batch['form_id'] ); // @todo try submit_form()
		} catch ( \Exception $e ) {
			$error = $e->getMessage();

			remove_filter( 'gform_suppress_confirmation_redirect', '__return_true' );
			remove_filter( 'gform_pre_process', $pre_process_validate_callback );
			remove_filter( 'gform_validation', $file_upload_keeplinks_validate );
			remove_filter( 'gform_save_field_value', $save_field_value_upload_single_callback );
			remove_filter( 'gform_save_field_value', $save_field_value_fileupload_keeplinks );
			remove_filter( 'gform_validation', $global_validation_callback );
			remove_filter( 'gform_addon_pre_process_feeds', $pre_process_feeds );

			foreach ( $global_field_validation_callbacks as $cb ) {
				remove_filter( 'gform_field_validation', $cb );
			}

			if ( empty( $has_fields ) ) {
				remove_filter( 'gform_validation', $validation_empty_fields_callback );
			}

			if ( ! in_array( 'notify', $batch['flags'] ) ) {
				remove_filter( 'gform_disable_notification', $notifications_callback );
			} elseif ( $disable_async_notifications ) {
				remove_filter( 'gform_is_asynchronous_notifications_enabled', $disable_async_notifications );
			}

			foreach ( $validate_callbacks as $callback ) {
				remove_filter( 'gform_field_validation', $callback );
			}

			$_POST                         = $post;
			$_FILES                        = $files;
			\GFFormsModel::$uploaded_files = array();
			$GLOBALS['_gf_uploaded_files'] = array();

			return $this->handle_row_processing_error( $error, $row, $batch );
		}

		$_POST                         = $post;
		$_FILES                        = $files;
		\GFFormsModel::$uploaded_files = array();
		$GLOBALS['_gf_uploaded_files'] = array();

		remove_filter( 'gform_suppress_confirmation_redirect', '__return_true' );
		remove_filter( 'gform_pre_process', $pre_process_validate_callback );
		remove_filter( 'gform_save_field_value', $save_field_value_upload_single_callback );
		remove_filter( 'gform_save_field_value', $save_field_value_fileupload_keeplinks );
		remove_filter( 'gform_validation', $global_validation_callback );
		remove_filter( 'gform_addon_pre_process_feeds', $pre_process_feeds );

		foreach ( $global_field_validation_callbacks as $cb ) {
			remove_filter( 'gform_field_validation', $cb );
		}

		if ( empty( $has_fields ) ) {
			remove_filter( 'gform_validation', $validation_empty_fields_callback );
		}

		if ( ! in_array( 'notify', $batch['flags'] ) ) {
			remove_filter( 'gform_disable_notification', $notifications_callback );
		} elseif ( $disable_async_notifications ) {
			remove_filter( 'gform_is_asynchronous_notifications_enabled', $disable_async_notifications );
		}

		foreach ( $validate_callbacks as $callback ) {
			remove_filter( 'gform_field_validation', $callback );
		}

		$submission = ! empty( \GFFormDisplay::$submission[ $batch['form_id'] ] ) ? \GFFormDisplay::$submission[ $batch['form_id'] ] : null;

		if ( is_null( $submission ) || ! $submission['is_valid'] ) {
			if ( ! is_null( $submission ) ) {
				$validation_error = __( 'Unknown', 'gk-gravityimport' );

				foreach ( $submission['form']['fields'] as $field ) {
					if ( $field->validation_message ) {
						$validation_error = sprintf( '%s (%s / #%s)', htmlspecialchars_decode( $field->validation_message, ENT_QUOTES ), $field->label ?: ( $field->adminLabel ?: $validation_error ), $field->id );
						break;
					}
				}

				$error = sprintf( esc_html__( 'Failed validation: %s', 'gk-gravityimport' ), esc_html( $validation_error ) );
			} else {
				$error = __( "Failed to pass one of Gravity Forms' form processing conditions", 'gk-gravityimport' );
			}

			$wpdb->update( $tables['rows'], array( 'status' => 'error', 'error' => $error ), array( 'id' => $row->id ) );

			do_action( 'gravityview/import/process/row/error', $row, $error, $batch );

			if ( ! in_array( 'soft', $batch['flags'] ) ) {
				$batch['status'] = 'error';
				$batch['error']  = sprintf( __( 'Failed to process row ID %d', 'gk-gravityimport' ), $row->id );

				do_action( "gravityview/import/process/{$batch['status']}", $batch = Batch::update( $batch ) );

				return $batch;
			}

			return Batch::update( $batch );
		}

		$entry = $submission['lead'];

		if ( ! empty( $update_id ) ) {
			$old_entry = \GFAPI::get_entry( $update_id );

			$wpdb->delete( \GFFormsModel::get_entry_meta_table_name(), array( 'entry_id' => $update_id ) );
			$wpdb->update( \GFFormsModel::get_entry_meta_table_name(), array( 'entry_id' => $update_id ), array( 'entry_id' => $entry['id'] ) );

			$wpdb->delete( \GFFormsModel::get_entry_notes_table_name(), array( 'entry_id' => $update_id ) );
			$wpdb->update( \GFFormsModel::get_entry_notes_table_name(), array( 'entry_id' => $update_id ), array( 'entry_id' => $entry['id'] ) );

			if ( $update_entry_properties ) {
				$wpdb->update( \GFFormsModel::get_entry_table_name(), $update_entry_properties, array( 'id' => $update_id ) );
			}
			$wpdb->delete( \GFFormsModel::get_entry_table_name(), array( 'id' => $entry['id'] ) );

			$entry = \GFAPI::get_entry( $update_id );

			// @todo Pull update hooks, suppress create hooks.
		} else {

			if ( ! $has_user_agent ) {
				/**
				 * @deprecated 2.0 Use `gravityview/import/user-agent` instead.
				 */
				$update_entry_properties['user_agent'] = apply_filters( 'gravityview-import/user-agent', __( 'GravityView Import', 'gk-gravityimport' ) );

				/**
				 * Set a missing User-Agent string for an entry.
				 *
				 * @since 2.0
				 *
				 * @param string $user_agent The User-Agent. Default: "GravityView Import"
				 * @param array  $entry      The entry.
				 * @param array  $batch      The batch.
				 * @param array  $data       The row data.
				 */
				$update_entry_properties['user_agent'] = apply_filters( 'gravityview/import/user-agent', $update_entry_properties['user_agent'], $entry, $batch, $row->data );
			}

			if ( $update_entry_properties ) {
				$wpdb->update( \GFFormsModel::get_entry_table_name(), $update_entry_properties, array( 'id' => $entry['id'] ) );
			}
		}

		foreach ( $update_entry_meta as $meta ) {
			gform_add_meta( $entry['id'], $meta[0], $meta[1], $batch['form_id'] );
		}

		// Force update_entry_meta_callback to run, for automatic survey score calculation
		\GFFormsModel::set_entry_meta( $entry, $form );

		foreach ( $notes as $note ) {
			$user = false;

			if ( ! empty( $note->user_email ) ) {
				$user = get_user_by( 'email', $note->user_email );
			}

			if ( ! $user && ! empty( $note->user_name ) ) {
				$user = get_user_by( 'login', $note->user_name );
			}

			if ( ! $user ) {
				$user = wp_get_current_user();
			}

			/**
			 * Override the note user.
			 *
			 * @since 2.0
			 *
			 * @param \WP_User $user  The default user. Default: current user.
			 * @param object   $note  The note.
			 * @param array    $entry The entry.
			 * @param array    $batch The batch.
			 */
			$user = apply_filters( 'gravityview/import/column/notes/user', $user, $note, $entry, $batch );

			add_action( 'gform_post_note_added', $note_update_date_callback = function ( $note_insert_id ) use ( $note ) {
				if ( empty( $note->date_created ) ) {
					return;
				}

				$GLOBALS['wpdb']->update(
					\GFFormsModel::get_entry_notes_table_name(), array(
					'date_created' => $note->date_created
				), array( 'id' => $note_insert_id )
				);
			} );

			\GFFormsModel::add_note( $entry['id'], $user->ID, $user->user_login, $note->value, isset( $note->note_type ) ? $note->note_type : 'note' );

			remove_action( 'gform_post_note_added', $note_update_date_callback );
		}

		if ( ! in_array( 'nolog', $batch['flags'] ) ) {
			Log::save( $batch['id'], $row->id, 'insert', $entry['id'] );
		}

		$wpdb->update( $tables['rows'], array( 'status' => 'processed', 'entry_id' => $entry['id'] ), array( 'id' => $row->id ) );

		if ( ! empty( $update_id ) ) {
			/**
			 * @deprecated Use `gravityview/import/entry/updated`
			 */
			do_action_deprecated( 'gravityview-importer/after-update', array( $entry ), '2.0.0', 'gravityview/import/entry/updated' );

			/**
			 * An entry has been updated.
			 *
			 * @param array $entry     The entry.
			 * @param array $old_entry The entry.
			 * @param array $batch     The batch.
			 */
			do_action( 'gravityview/import/entry/updated', $entry, $old_entry, $batch );
		} else {
			/**
			 * @deprecated 2.0 Use `gravityview/import/entry/created` instead.
			 */
			do_action( 'gravityview-importer/after-add', $entry );

			/**
			 * A new entry has been created.
			 *
			 * @param array $entry The entry.
			 * @param array $batch The batch.
			 */
			do_action( 'gravityview/import/entry/created', $entry, $batch );
		}

		return Batch::update( $batch );
	}

	/**
	 * It's done.
	 *
	 * @internal
	 *
	 * @param array $batch The batch.
	 *
	 * @return \WP_Error|array A batch or an error.
	 */
	public function handle_done( $batch ) {
		if ( is_wp_error( $error = $this->_handle_check_status( __FUNCTION__, $batch ) ) ) {
			return $error;
		}

		return new \WP_Error( 'gravityview/import/errors/done', __( 'No more rows to process.', 'gk-gravityimport' ) );
	}

	/**
	 * It's done.
	 *
	 * @internal
	 *
	 * @param array $batch The batch.
	 *
	 * @return \WP_Error|array A batch or an error.
	 */
	public function handle_rolledback( $batch ) {
		if ( is_wp_error( $error = $this->_handle_check_status( __FUNCTION__, $batch ) ) ) {
			return $error;
		}

		return new \WP_Error( 'gravityview/import/errors/done', __( 'No more rows to process.', 'gk-gravityimport' ) );
	}

	/**
	 * Rollback the whole batch.
	 *
	 * @internal
	 *
	 * @param array $batch The batch.
	 *
	 * @return \WP_Error|array A batch or an error.
	 */
	public function handle_rollback( $batch ) {
		if ( is_wp_error( $error = $this->_handle_check_status( __FUNCTION__, $batch ) ) ) {
			return $error;
		}

		if ( in_array( 'nolog', $batch['flags'] ) ) {
			return new \WP_Error( 'gravityview/import/errors/rollback', __( 'Rollback not possible on a non-logged batch.', 'gk-gravityimport' ) );
		}

		// @todo test concurrency here
		if ( ! Log::rollback_batch( $batch['id'] ) ) {
			return new \WP_Error( 'gravityview/import/errors/rollback', __( 'Rollback failed.', 'gk-gravityimport' ) );
		}

		$batch['status'] = 'rolledback';

		do_action( "gravityview/import/process/{$batch['status']}", $batch = Batch::update( $batch ) );

		return $batch;
	}

	/**
	 * Check status before allowing handler through.
	 *
	 * @internal
	 *
	 * @param string $function The function this is being called from.
	 * @param array  $batch    The batch array.
	 *
	 * @return \WP_Error|void
	 */
	public function _handle_check_status( $function, $batch ) {
		$status = str_replace( 'handle_', '', $function );
		if ( ! is_array( $batch ) || empty( $batch['status'] ) || $batch['status'] != $status ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_state', sprintf( __( 'Batch is not in "%s" state, wrong handler called.', 'gk-gravityimport' ), $status ), array( 'status' => 409 ) );
		}
	}

	/**
	 * Check the source for readability.
	 *
	 * @internal
	 *
	 * @param array $batch The batch array.
	 *
	 * @return \WP_Error|void
	 */
	public function _handle_check_source( $batch ) {
		if ( ! is_array( $batch ) || empty( $batch['source'] ) ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_source', __( 'Source is no longer readable.', 'gk-gravityimport' ) );
		}

		// is_readable() is unreliable on stream wrappers whose url_stat() is incomplete; for
		// allowed wrappers, fall back to the same fopen() probe to_local() uses.
		$is_readable = $this->is_allowed_stream_wrapper_source( $batch['source'] )
			? $this->is_source_readable( $batch['source'] )
			: is_readable( $batch['source'] );

		if ( ! $is_readable ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_source', __( 'Source is no longer readable.', 'gk-gravityimport' ) );
		}
	}

	/**
	 * A default process setup.
	 *
	 * Called via REST. Can be used as an example
	 * to build more complicated setups, with parallel processing,
	 * load-based throttling, etc.
	 *
	 * @internal
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_Error|\WP_REST_Response|\WP_HTTP_Response
	 */
	public static function rest_process( $request ) {
		$processor = new Processor( array(
			'batch_id' => $request->get_param( 'batch_id' ),
		) );
		$processor->run();

		return rest_ensure_response( true );
	}

	/**
	 * Transform source path to a local path.
	 *
	 * If the source is a remote URI, it will be downloaded to the
	 * temporary directory. Supports HTTP/HTTPS.
	 *
	 * @internal
	 *
	 * @todo Add SFTP, FTP, FTPS support
	 *
	 * @param string $source The source as given in the batch.
	 * @param bool   $force  Force a download if remote and local path exists.
	 *
	 * @return string|\WP_Error A local path or an error.
	 */
	public function to_local( $source, $force = false ) {
		/**
		 * Is this a remote?
		 */
		// PHP stream wrapper protocol names may contain digits and `+`, `-`, `.` (see the PHP manual
		// for stream_wrapper_register()), so the captured scheme allows them too — otherwise
		// schemes like s3:// would fall through to the local-file branch and fail.
		if ( preg_match( '#^(?!file)([a-z][a-z0-9+\-.]*):\/\/#i', $source, $matches ) ) {
			$scheme = strtolower( $matches[1] );
			if ( ! in_array( $scheme, array( 'http', 'https' ), true ) ) {
				// Accept allowlisted registered stream wrappers (e.g. vip:// on WP VIP). The
				// allowlist intentionally excludes wrappers that would let an authenticated user
				// read arbitrary content (phar://, data://, php://, glob://, compress.zlib://, ...).
				if ( $this->is_allowed_stream_wrapper_source( $source ) && $this->is_source_readable( $source ) ) {
					return $source;
				}

				return new \WP_Error( 'gravityview/import/errors/invalid_source', __( 'Source protocol not supported.', 'gk-gravityimport' ) );
			}

			if ( ! function_exists( 'download_url' ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
			}

			if ( is_wp_error( $result = download_url( $source ) ) ) {
				return new \WP_Error( 'gravityview/import/errors/invalid_source', __( 'Source URL could not be downloaded.', 'gk-gravityimport' ) );
			}
			return $result;
		}

		/**
		 * This is local.
		 */
		$source = preg_replace( '#^file:\/\/#i', '', $source );

		if ( ! file_exists( $source ) ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_source', __( 'Source does not exist.', 'gk-gravityimport' ) );
		}

		if ( ! is_readable( $source ) || '/tmp/unreadable' === $source ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_source', __( 'Source is not readable.', 'gk-gravityimport' ) );
		}

		return $source;
	}

	/**
	 * Checks whether a source URI uses an allowlisted, registered PHP stream wrapper.
	 *
	 * Used by {@see to_local()} and {@see _handle_check_source()} as the single source of truth for
	 * "is this a wrapper we trust as an import source." The allowlist exists to keep authenticated
	 * users from pointing the importer at wrappers that can read arbitrary content (phar://, data://,
	 * php://, glob://, compress.zlib://, …) even when those wrappers are registered by PHP itself.
	 *
	 * @since 2.11.1
	 *
	 * @param string $source The source URI.
	 *
	 * @return bool
	 */
	private function is_allowed_stream_wrapper_source( $source ) {
		if ( ! preg_match( '#^(?!file)([a-z][a-z0-9+\-.]*):\/\/#i', (string) $source, $matches ) ) {
			return false;
		}

		$scheme = strtolower( $matches[1] );

		/**
		 * Filters the stream-wrapper schemes accepted as import sources.
		 *
		 * Default list covers the object-storage wrappers we deliberately support
		 * (vip:// on WP VIP, s3://, gs://, azure://). Add other wrappers here only if
		 * the source they expose is trusted — wrappers like phar://, data://, php://,
		 * glob://, and compress.zlib:// must NOT be enabled.
		 *
		 * @since 2.11.1
		 *
		 * @param string[] $allowed Lower-cased stream-wrapper schemes.
		 * @param string   $source  The full source URI being checked.
		 */
		$allowed = (array) apply_filters(
			'gk/gravityimport/source/allowed-wrappers',
			array( 'vip', 's3', 's3a', 's3n', 'gs', 'azure' ),
			$source
		);

		if ( ! in_array( $scheme, $allowed, true ) ) {
			return false;
		}

		return in_array( $scheme, array_map( 'strtolower', stream_get_wrappers() ), true );
	}

	/**
	 * Checks whether a stream-wrapper source can be opened for reading.
	 *
	 * Uses a brief fopen() probe rather than is_readable(), because the latter relies on the
	 * wrapper implementing url_stat() — which is unreliable for non-local schemes such as vip://.
	 *
	 * @since 2.11.1
	 *
	 * @param string $source The source URI.
	 *
	 * @return bool
	 */
	private function is_source_readable( $source ) {
		$handle = @fopen( $source, 'r' );

		if ( ! $handle ) {
			return false;
		}

		fclose( $handle );

		return true;
	}

	/**
	 * Return the calculated arguments for this processor.
	 *
	 * @return array The arguments ($this->args)
	 */
	public function get_args() {
		return $this->args;
	}

	/**
	 * Transform an input datetime format to an output one.
	 *
	 * Uses https://www.php.net/manual/en/datetime.createfromformat.php
	 *
	 * @param string $type               The input type.
	 * @param string $input_date         The input date.
	 * @param string $input_date_format  The input date format.
	 * @param string $output_date_format The output date format.
	 * @param int    $partial            The partial timestamp that needs to be completed.
	 *
	 * @return string The final date output. Remains untouched if format is incorrect/unsupported.
	 */
	public static function transform_datetime( $type, $input_date, $input_date_format, $output_date_format = 'Y-m-d H:i:s', $partial = 0 ) {

		if ( ! $input_date_format ) {
			return $input_date;
		}

		$custom_input = null;

		if ( preg_match( '/regex:\((.*?)\)$/', $input_date_format, $regex ) && preg_match( '/' . str_replace( '/', '\/', base64_decode( $regex[1] ) ) . '/i', $input_date, $parsed_date ) ) {
			if ( 'time' === $type && ! empty( $parsed_date['hour'] ) && ! empty( $parsed_date['minute'] ) ) {
				$custom_input = sprintf( '%02d:%02d %s',
					(int) $parsed_date['hour'],
					(int) $parsed_date['minute'],
					! empty( $parsed_date['period'] ) ? $parsed_date['period'] : ''
				);

				$input_date_format = sprintf( '%s:%s %s',
					(int) $parsed_date['hour'] > 12 ? 'g' : 'G',
					'i',
					! empty( $parsed_date['period'] ) ? 'a' : ''
				);
			}


			if ( 'date' === $type && ! empty( $parsed_date['year'] ) && ! empty( $parsed_date['month'] ) && ! empty( $parsed_date['day'] ) ) {
				if ( ! (int) $parsed_date['month'] ) {
					$month                = array_search( $parsed_date['month'], array( 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec' ) );
					$parsed_date['month'] = ! is_null( $month ) ? $month + 1 : $parsed_date['month'];
				}

				$custom_input = sprintf( '%d/%d/%d',
					(int) $parsed_date['day'],
					(int) $parsed_date['month'],
					(int) $parsed_date['year']
				);

				$input_date_format = 'd/m/' . ( strlen( (int) $parsed_date['year'] ) === 2 ? 'y' : 'Y' );

				if ( 'Y-m-d H:i:s' == $output_date_format ) {
					$custom_input .= ' ' . $parsed_date['time'];

					$input_date_format = sprintf(
						'%s %s:%s',
						$input_date_format,
						(int) $parsed_date['hour'] > 12 ? 'g' : 'G',
						'i'
					);
				}
			}
		}

		// Add missing seconds
		if ( 'Y-m-d G:i:s' === $input_date_format && 1 === substr_count( $input_date, ':' ) ) {
			$input_date .= ':00';
		}

		if ( ! $date = date_create_from_format( $input_date_format, $custom_input ? $custom_input : $input_date ) ) {
			return $input_date;
		}

		return $date->format( $output_date_format );
	}

	/**
	 * Extracts unique values for a specific column from batch row data.
	 *
	 * Caches the row data per batch to avoid repeated large queries.
	 * Limited to 5000 rows to prevent memory exhaustion on large imports.
	 *
	 * @since 2.9.0
	 *
	 * @param int   $batch_id     The batch ID.
	 * @param int   $column_index The column index to extract values from.
	 * @param array $tables       Database table names.
	 *
	 * @return array Unique values found in the specified column.
	 */
	private function get_column_choices( $batch_id, $column_index, $tables ) {
		global $wpdb;

		static $cache = array();

		$cache_key = (string) $batch_id;

		if ( ! isset( $cache[ $cache_key ] ) ) {
			$result = $wpdb->get_col( $wpdb->prepare(
				"SELECT data FROM {$tables['rows']} WHERE batch_id = %d LIMIT 5000",
				$batch_id
			) );

			$cache[ $cache_key ] = array_map( 'json_decode', $result );

			// Free the raw result set from memory.
			$wpdb->last_result = array();
		}

		return array_filter( array_unique( wp_list_pluck( $cache[ $cache_key ], $column_index ) ), 'strlen' );
	}

	/**
	 * Handle errors when a row can't be processed.
	 *
	 * @since 2.2.3
	 *
	 * @param string     $error Error message
	 * @param array|null $row   Row data
	 * @param array      $batch Batch data
	 *
	 * @return array|\WP_Error|null
	 */
	public function handle_row_processing_error( $error, $row, $batch ) {
		global $wpdb;

		$tables = gv_import_entries_get_db_tables();

		$wpdb->update( $tables['rows'], array( 'status' => 'error', 'error' => $error ), array( 'id' => $row->id ) );

		/**
		 * This row has errored for some reason.
		 *
		 * @param object $row   The row object in the database.
		 * @param string $error The error.
		 * @param array  $batch The batch.
		 */
		do_action( 'gravityview/import/process/row/error', $row, $error, $batch );

		if ( ! in_array( 'soft', $batch['flags'] ) ) {
			$batch['status'] = 'error';
			$batch['error']  = sprintf( __( 'Failed to process row ID %d', 'gk-gravityimport' ), $row->id );

			do_action( "gravityview/import/process/{$batch['status']}", $batch = Batch::update( $batch ) );

			return $batch;
		}

		return Batch::update( $batch );
	}

	/**
	 * Get the field type mapping for auto-detecting field types based on column names.
	 *
	 * Returns an array of keywords and their corresponding field types.
	 * Organized by use case for better maintainability.
	 * More specific keywords are prioritized at the top, with catchall patterns at the bottom.
	 *
	 * @param array $column The column header.
	 * @param array $batch The batch.
	 *
	 * @return array An associative array of keyword => field type mappings, ordered by priority.
	 */
	private function get_field_typemap( $column, $batch ) {
		// Static cache for the base typemap to avoid repeated translation lookups.
		static $base_typemap_cache = null;

		if ( $base_typemap_cache ) {
			/** This filter is documented below. */
			return apply_filters( 'gravityview/import/parse/typemap', $base_typemap_cache, $column, $batch );
		}

		$base_typemap_cache = [
			// === ENTRY PROPERTIES ===
			_x( 'entry id', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )        => 'id',
			_x( 'entry date', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )      => 'date_created',
			_x( 'ip address', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )      => 'ip',
			_x( 'user ip', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )         => 'ip',
			_x( 'approval status', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' ) => 'is_approved',
			_x( 'user agent', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )      => 'user_agent',
			_x( 'post id', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )         => 'post_id',
			_x( 'payment status', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )  => 'payment_status',
			_x( 'payment date', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )    => 'payment_date',
			_x( 'payment amount', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )  => 'payment_amount',
			_x( 'transaction id', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )  => 'transaction_id',
			_x( 'source url', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )      => 'source_url',
			_x( 'date created', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )    => 'date_created',
			_x( 'date updated', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )    => 'date_updated',

			// Additional entry properties from database schema
			_x( 'is starred', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )       => 'is_starred',
			_x( 'starred', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )          => 'is_starred',
			_x( 'is read', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )          => 'is_read',
			_x( 'read', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )             => 'is_read',
			_x( 'currency', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )         => 'currency',
			_x( 'payment method', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )   => 'payment_method',
			_x( 'is fulfilled', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )     => 'is_fulfilled',
			_x( 'fulfilled', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )        => 'is_fulfilled',
			_x( 'created by', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )       => 'created_by',
			_x( 'user id', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )          => 'created_by',
			_x( 'transaction type', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' ) => 'transaction_type',
			_x( 'entry status', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )     => 'status',
			_x( 'source id', 'Typemap keyword. Part of a word that matches the name of an entry property. Has to be lowercase.', 'gk-gravityimport' )        => 'source_id',

			// === GRAVITYVIEW ENTRY META ===
			_x( 'longitude', 'Typemap keyword. Part of a word that matches an entry property. Has to be lowercase.', 'gk-gravityimport' )                              => 'long',
			_x( 'latitude', 'Typemap keyword. Part of a word that matches an entry property. Has to be lowercase.', 'gk-gravityimport' )                               => 'lat',
			_x( 'gravityview_ratings_star', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' )                  => 'number',
			_x( 'gravityview_ratings_stars', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' )                 => 'number',
			_x( 'gravityview_ratings_total', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' )                 => 'number',
			_x( 'gravityview_ratings_vote', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' )                  => 'number',
			_x( 'gravityview_ratings_votes', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' )                 => 'number',
			_x( 'is_approved', 'Typemap keyword. Part of a word that matches an entry property. Has to be lowercase.', 'gk-gravityimport' )                            => 'number',
			_x( 'workflow_current_status_timestamp', 'Typemap keyword. Part of a word that matches an entry property. Has to be lowercase.', 'gk-gravityimport' )     => 'number',
			_x( 'submission speed', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' )                          => 'number',

			// === CONTACT INFORMATION ===
			_x( 'email', 'Typemap keyword. Part of a word that matches an email field. Has to be lowercase. Set to ### if does not apply.', 'gk-gravityimport' ) => 'email',

			// Professional Contact
			_x( 'company', 'Typemap keyword. Part of a word that matches a text field. Has to be lowercase.', 'gk-gravityimport' ) => 'text',
			_x( 'organization', 'Typemap keyword. Part of a word that matches a text field. Has to be lowercase.', 'gk-gravityimport' ) => 'text',
			_x( 'business', 'Typemap keyword. Part of a word that matches a text field. Has to be lowercase.', 'gk-gravityimport' ) => 'text',
			_x( 'employer', 'Typemap keyword. Part of a word that matches a text field. Has to be lowercase.', 'gk-gravityimport' ) => 'text',
			_x( 'job', 'Typemap keyword. Part of a word that matches a text field. Has to be lowercase.', 'gk-gravityimport' ) => 'text',
			_x( 'position', 'Typemap keyword. Part of a word that matches a text field. Has to be lowercase.', 'gk-gravityimport' ) => 'text',
			_x( 'department', 'Typemap keyword. Part of a word that matches a text field. Has to be lowercase.', 'gk-gravityimport' ) => 'text',

			// Social Media
			_x( 'twitter', 'Typemap keyword. Part of a word that matches a text field. Has to be lowercase.', 'gk-gravityimport' ) => 'text',
			_x( 'linkedin', 'Typemap keyword. Part of a word that matches a text field. Has to be lowercase.', 'gk-gravityimport' ) => 'text',
			_x( 'facebook', 'Typemap keyword. Part of a word that matches a text field. Has to be lowercase.', 'gk-gravityimport' ) => 'text',
			_x( 'instagram', 'Typemap keyword. Part of a word that matches a text field. Has to be lowercase.', 'gk-gravityimport' ) => 'text',
			_x( 'username', 'Typemap keyword. Part of a word that matches a text field. Has to be lowercase.', 'gk-gravityimport' ) => 'text',

			// === PHONE FIELDS ===
			_x( 'fax', 'Typemap keyword. Part of a word that matches a phone field. Has to be lowercase. Set to ### if does not apply.', 'gk-gravityimport' )       => 'phone',
			_x( 'mobile', 'Typemap keyword. Part of a word that matches a phone field. Has to be lowercase. Set to ### if does not apply.', 'gk-gravityimport' )    => 'phone',
			_x( 'phone', 'Typemap keyword. Part of a word that matches a phone field. Has to be lowercase. Set to ### if does not apply.', 'gk-gravityimport' )     => 'phone',
			_x( 'telephone', 'Typemap keyword. Part of a word that matches a phone field. Has to be lowercase. Set to ### if does not apply.', 'gk-gravityimport' ) => 'phone',
			_x( 'cell', 'Typemap keyword. Part of a word that matches a phone field. Has to be lowercase. Set to ### if does not apply.', 'gk-gravityimport' )      => 'phone',
			_x( 'whatsapp', 'Typemap keyword. Part of a word that matches a phone field. Has to be lowercase.', 'gk-gravityimport' ) => 'phone',

			// === FINANCIAL & E-COMMERCE ===
			_x( 'price', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'cost', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'amount', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'payment', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'fee', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'budget', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'salary', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'revenue', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'discount', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'quantity', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'qty', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'total', 'Typemap keyword. Part of a word that matches a total field. Has to be lowercase.', 'gk-gravityimport' ) => 'total',

			// Product/Order Information
			_x( 'sku', 'Typemap keyword. Part of a word that matches a text field. Has to be lowercase.', 'gk-gravityimport' ) => 'text',
			_x( 'product', 'Typemap keyword. Part of a word that matches a text field. Has to be lowercase.', 'gk-gravityimport' ) => 'text',
			_x( 'item', 'Typemap keyword. Part of a word that matches a text field. Has to be lowercase.', 'gk-gravityimport' ) => 'text',
			_x( 'order', 'Typemap keyword. Part of a word that matches a text field. Has to be lowercase.', 'gk-gravityimport' ) => 'text',
			_x( 'invoice', 'Typemap keyword. Part of a word that matches a text field. Has to be lowercase.', 'gk-gravityimport' ) => 'text',
			_x( 'coupon', 'Typemap keyword. Part of a word that matches a text field. Has to be lowercase.', 'gk-gravityimport' ) => 'text',

			// === NUMBER FIELDS ===
			_x( 'number', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'year', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' )   => 'number',
			_x( 'count', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' )  => 'number',
			_x( 'age', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'weight', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'height', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'score', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'rating', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'rank', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'gpa', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',
			_x( 'pin', 'Typemap keyword. Part of a word that matches a number field. Has to be lowercase.', 'gk-gravityimport' ) => 'number',

			// === FILE UPLOAD & MEDIA ===
			_x( 'file', 'Typemap keyword. Part of a word that matches a file field. Has to be lowercase.', 'gk-gravityimport' ) => 'fileupload',
			_x( 'upload', 'Typemap keyword. Part of a word that matches a file field. Has to be lowercase.', 'gk-gravityimport' ) => 'fileupload',
			_x( 'attachment', 'Typemap keyword. Part of a word that matches a file field. Has to be lowercase.', 'gk-gravityimport' ) => 'fileupload',
			_x( 'document', 'Typemap keyword. Part of a word that matches a file field. Has to be lowercase.', 'gk-gravityimport' ) => 'fileupload',
			_x( 'pdf', 'Typemap keyword. Part of a word that matches a file field. Has to be lowercase.', 'gk-gravityimport' ) => 'fileupload',
			_x( 'image', 'Typemap keyword. Part of a word that matches a file field. Has to be lowercase.', 'gk-gravityimport' ) => 'fileupload',
			_x( 'photo', 'Typemap keyword. Part of a word that matches a file field. Has to be lowercase.', 'gk-gravityimport' ) => 'fileupload',
			_x( 'picture', 'Typemap keyword. Part of a word that matches a file field. Has to be lowercase.', 'gk-gravityimport' ) => 'fileupload',
			_x( 'resume', 'Typemap keyword. Part of a word that matches a file field. Has to be lowercase.', 'gk-gravityimport' ) => 'fileupload',
			_x( 'cv', 'Typemap keyword. Part of a word that matches a file field. Has to be lowercase.', 'gk-gravityimport' ) => 'fileupload',
			_x( 'portfolio', 'Typemap keyword. Part of a word that matches a file field. Has to be lowercase.', 'gk-gravityimport' ) => 'fileupload',
			_x( 'signature', 'Typemap keyword. Part of a word that matches a signature field. Has to be lowercase.', 'gk-gravityimport' ) => 'signature',

			// === URL/WEBSITE FIELDS ===
			_x( 'website', 'Typemap keyword. Part of a word that matches a URL field. Has to be lowercase.', 'gk-gravityimport' )                            => 'website',
			_x( 'site', 'Typemap keyword. Part of a word that matches a URL field. Has to be lowercase. Set to ### if does not apply.', 'gk-gravityimport' ) => 'website',
			_x( 'url', 'Typemap keyword. Part of a word that matches a URL field. Has to be lowercase. Set to ### if does not apply.', 'gk-gravityimport' )  => 'website',
			_x( 'link', 'Typemap keyword. Part of a word that matches a URL field. Has to be lowercase.', 'gk-gravityimport' ) => 'website',

			// === TEXTAREA FIELDS ===
			_x( 'bio', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase. Set to ### if does not apply.', 'gk-gravityimport' ) => 'textarea',
			_x( 'description', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase.', 'gk-gravityimport' ) => 'textarea',
			_x( 'comment', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase.', 'gk-gravityimport' ) => 'textarea',
			_x( 'comments', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase.', 'gk-gravityimport' ) => 'textarea',
			_x( 'message', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase.', 'gk-gravityimport' ) => 'textarea',
			_x( 'note', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase.', 'gk-gravityimport' ) => 'textarea',
			_x( 'notes', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase.', 'gk-gravityimport' ) => 'textarea',
			_x( 'feedback', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase.', 'gk-gravityimport' ) => 'textarea',
			_x( 'review', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase.', 'gk-gravityimport' ) => 'textarea',
			_x( 'testimonial', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase.', 'gk-gravityimport' ) => 'textarea',
			_x( 'question', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase.', 'gk-gravityimport' ) => 'textarea',
			_x( 'answer', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase.', 'gk-gravityimport' ) => 'textarea',
			_x( 'summary', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase.', 'gk-gravityimport' ) => 'textarea',
			_x( 'details', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase.', 'gk-gravityimport' ) => 'textarea',
			_x( 'inquiry', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase.', 'gk-gravityimport' ) => 'textarea',
			_x( 'request', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase.', 'gk-gravityimport' ) => 'textarea',
			_x( 'reason', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase.', 'gk-gravityimport' ) => 'textarea',
			_x( 'suggestion', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase.', 'gk-gravityimport' ) => 'textarea',
			_x( 'experience', 'Typemap keyword. Part of a word that matches a textarea field. Has to be lowercase.', 'gk-gravityimport' ) => 'textarea',

			// === DATE & TIME FIELDS ===
			_x( 'date', 'Typemap keyword. Part of a word that matches a date field. Has to be lowercase.', 'gk-gravityimport' )        => 'date',
			_x( 'birthday', 'Typemap keyword. Part of a word that matches a date field. Has to be lowercase.', 'gk-gravityimport' )    => 'date',
			_x( 'anniversary', 'Typemap keyword. Part of a word that matches a date field. Has to be lowercase.', 'gk-gravityimport' ) => 'date',
			_x( 'dob', 'Typemap keyword. Part of a word that matches a date field. Has to be lowercase.', 'gk-gravityimport' ) => 'date',
			_x( 'birth', 'Typemap keyword. Part of a word that matches a date field. Has to be lowercase.', 'gk-gravityimport' ) => 'date',
			_x( 'deadline', 'Typemap keyword. Part of a word that matches a date field. Has to be lowercase.', 'gk-gravityimport' ) => 'date',
			_x( 'due', 'Typemap keyword. Part of a word that matches a date field. Has to be lowercase.', 'gk-gravityimport' ) => 'date',
			_x( 'expiry', 'Typemap keyword. Part of a word that matches a date field. Has to be lowercase.', 'gk-gravityimport' ) => 'date',
			_x( 'expire', 'Typemap keyword. Part of a word that matches a date field. Has to be lowercase.', 'gk-gravityimport' ) => 'date',
			_x( 'start', 'Typemap keyword. Part of a word that matches a date field. Has to be lowercase.', 'gk-gravityimport' ) => 'date',
			_x( 'end', 'Typemap keyword. Part of a word that matches a date field. Has to be lowercase.', 'gk-gravityimport' ) => 'date',
			_x( 'graduation', 'Typemap keyword. Part of a word that matches a date field. Has to be lowercase.', 'gk-gravityimport' ) => 'date',

			_x( 'timestamp', 'Typemap keyword. Part of a word that matches a date field. Has to be lowercase.', 'gk-gravityimport' ) => 'date',
			_x( 'time', 'Typemap keyword. Part of a word that matches a time field. Has to be lowercase.', 'gk-gravityimport' )  => 'time',
			_x( 'hour', 'Typemap keyword. Part of a word that matches a time field. Has to be lowercase.', 'gk-gravityimport' )  => 'time',
			_x( 'hours', 'Typemap keyword. Part of a word that matches a time field. Has to be lowercase.', 'gk-gravityimport' ) => 'time',
			_x( 'duration', 'Typemap keyword. Part of a word that matches a time field. Has to be lowercase.', 'gk-gravityimport' ) => 'time',

			// === SELECTION FIELDS ===
			_x( 'gender', 'Typemap keyword. Part of a word that matches a radio field. Has to be lowercase.', 'gk-gravityimport' ) => 'radio',
			_x( 'sex', 'Typemap keyword. Part of a word that matches a radio field. Has to be lowercase.', 'gk-gravityimport' ) => 'radio',
			_x( 'choice', 'Typemap keyword. Part of a word that matches a radio field. Has to be lowercase.', 'gk-gravityimport' ) => 'radio',
			_x( 'option', 'Typemap keyword. Part of a word that matches a radio field. Has to be lowercase.', 'gk-gravityimport' ) => 'radio',
			_x( 'satisfaction', 'Typemap keyword. Part of a word that matches a radio field. Has to be lowercase.', 'gk-gravityimport' ) => 'radio',

			_x( 'select', 'Typemap keyword. Part of a word that matches a select field. Has to be lowercase.', 'gk-gravityimport' ) => 'select',
			_x( 'choose', 'Typemap keyword. Part of a word that matches a select field. Has to be lowercase.', 'gk-gravityimport' ) => 'select',
			_x( 'category', 'Typemap keyword. Part of a word that matches a select field. Has to be lowercase.', 'gk-gravityimport' ) => 'select',
			_x( 'type', 'Typemap keyword. Part of a word that matches a select field. Has to be lowercase.', 'gk-gravityimport' ) => 'select',
			_x( 'status', 'Typemap keyword. Part of a word that matches a select field. Has to be lowercase.', 'gk-gravityimport' ) => 'select',
			_x( 'priority', 'Typemap keyword. Part of a word that matches a select field. Has to be lowercase.', 'gk-gravityimport' ) => 'select',
			_x( 'level', 'Typemap keyword. Part of a word that matches a select field. Has to be lowercase.', 'gk-gravityimport' ) => 'select',
			_x( 'grade', 'Typemap keyword. Part of a word that matches a select field. Has to be lowercase.', 'gk-gravityimport' ) => 'select',
			_x( 'degree', 'Typemap keyword. Part of a word that matches a select field. Has to be lowercase.', 'gk-gravityimport' ) => 'select',
			_x( 'nationality', 'Typemap keyword. Part of a word that matches a select field. Has to be lowercase.', 'gk-gravityimport' ) => 'select',
			_x( 'language', 'Typemap keyword. Part of a word that matches a select field. Has to be lowercase.', 'gk-gravityimport' ) => 'select',
			_x( 'timezone', 'Typemap keyword. Part of a word that matches a select field. Has to be lowercase.', 'gk-gravityimport' ) => 'select',

			_x( 'tag', 'Typemap keyword. Part of a word that matches a multiselect field. Has to be lowercase.', 'gk-gravityimport' ) => 'multiselect',
			_x( 'tags', 'Typemap keyword. Part of a word that matches a multiselect field. Has to be lowercase.', 'gk-gravityimport' ) => 'multiselect',

			// === CHECKBOX FIELDS ===
			_x( 'check', 'Typemap keyword. Part of a word that matches a checkbox field. Has to be lowercase.', 'gk-gravityimport' ) => 'checkbox',
			_x( 'confirm', 'Typemap keyword. Part of a word that matches a checkbox field. Has to be lowercase.', 'gk-gravityimport' ) => 'checkbox',
			_x( 'acknowledge', 'Typemap keyword. Part of a word that matches a checkbox field. Has to be lowercase.', 'gk-gravityimport' ) => 'checkbox',
			_x( 'verify', 'Typemap keyword. Part of a word that matches a checkbox field. Has to be lowercase.', 'gk-gravityimport' ) => 'checkbox',

			// === CONSENT & LEGAL FIELDS ===
			_x( 'consent', 'Typemap keyword. Part of a word that matches a consent field. Has to be lowercase.', 'gk-gravityimport' ) => 'consent',
			_x( 'agree', 'Typemap keyword. Part of a word that matches a consent field. Has to be lowercase.', 'gk-gravityimport' ) => 'consent',
			_x( 'accept', 'Typemap keyword. Part of a word that matches a consent field. Has to be lowercase.', 'gk-gravityimport' ) => 'consent',
			_x( 'subscribe', 'Typemap keyword. Part of a word that matches a consent field. Has to be lowercase.', 'gk-gravityimport' ) => 'consent',
			_x( 'terms', 'Typemap keyword. Part of a word that matches a consent field. Has to be lowercase.', 'gk-gravityimport' ) => 'consent',
			_x( 'privacy', 'Typemap keyword. Part of a word that matches a consent field. Has to be lowercase.', 'gk-gravityimport' ) => 'consent',
			_x( 'gdpr', 'Typemap keyword. Part of a word that matches a consent field. Has to be lowercase.', 'gk-gravityimport' ) => 'consent',
			_x( 'newsletter', 'Typemap keyword. Part of a word that matches a consent field. Has to be lowercase.', 'gk-gravityimport' ) => 'consent',
			_x( 'marketing', 'Typemap keyword. Part of a word that matches a consent field. Has to be lowercase.', 'gk-gravityimport' ) => 'consent',
			_x( 'opt', 'Typemap keyword. Part of a word that matches a consent field. Has to be lowercase.', 'gk-gravityimport' ) => 'consent',

			// === HIDDEN & SYSTEM FIELDS ===
			_x( 'hidden', 'Typemap keyword. Part of a word that matches a hidden field. Has to be lowercase.', 'gk-gravityimport' ) => 'hidden',
			_x( 'session', 'Typemap keyword. Part of a word that matches a hidden field. Has to be lowercase.', 'gk-gravityimport' ) => 'hidden',
			_x( 'tracking', 'Typemap keyword. Part of a word that matches a hidden field. Has to be lowercase.', 'gk-gravityimport' ) => 'hidden',
			_x( 'utm', 'Typemap keyword. Part of a word that matches a hidden field. Has to be lowercase.', 'gk-gravityimport' ) => 'hidden',
			_x( 'referrer', 'Typemap keyword. Part of a word that matches a hidden field. Has to be lowercase.', 'gk-gravityimport' ) => 'hidden',
			_x( 'source', 'Typemap keyword. Part of a word that matches a hidden field. Has to be lowercase.', 'gk-gravityimport' ) => 'hidden',
			_x( 'campaign', 'Typemap keyword. Part of a word that matches a hidden field. Has to be lowercase.', 'gk-gravityimport' ) => 'hidden',

			// === NAME FIELDS ===
			// Put more specific patterns first to ensure they match before generic ones
			_x( 'prefix', 'Typemap keyword. Part of a word that matches a name field. Has to be lowercase.', 'gk-gravityimport' )                                        => 'name.2',
			_x( 'title', 'Typemap keyword. Part of a word that matches a name field. Has to be lowercase.', 'gk-gravityimport' )                                         => 'name.2',
			_x( 'first', 'Typemap keyword. Part of a word that matches a name field. Has to be lowercase.', 'gk-gravityimport' )                                         => 'name.3',
			_x( 'middle', 'Typemap keyword. Part of a word that matches a name field. Has to be lowercase.', 'gk-gravityimport' )                                        => 'name.4',
			_x( 'last', 'Typemap keyword. Part of a word that matches a name field. Has to be lowercase.', 'gk-gravityimport' )                                          => 'name.6',
			_x( 'suffix', 'Typemap keyword. Part of a word that matches a name field. Has to be lowercase.', 'gk-gravityimport' )                                        => 'name.8',
			_x( 'spouse', 'Typemap keyword. Part of a word that matches a name field. Has to be lowercase.', 'gk-gravityimport' )                                        => 'name.3',
			_x( 'name', 'Typemap keyword. Part of a word that matches a name field. Has to be lowercase.', 'gk-gravityimport' )                                          => 'name.3',

			// === ADDRESS FIELDS ===
			// Put more specific patterns first to ensure they match before generic ones
			_x( 'street address', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase.', 'gk-gravityimport' )                            => 'address.1',
			_x( 'mailing address', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase.', 'gk-gravityimport' )                           => 'address.1',
			_x( 'address 2', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase.', 'gk-gravityimport' )                                 => 'address.2',
			_x( 'line 2', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase.', 'gk-gravityimport' )                                    => 'address.2',
			_x( 'suite', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase.', 'gk-gravityimport' )                                     => 'address.2',
			_x( 'unit', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase.', 'gk-gravityimport' )                                      => 'address.2',
			_x( 'apartment', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase.', 'gk-gravityimport' )                                 => 'address.2',
			_x( 'street', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase.', 'gk-gravityimport' )                                    => 'address.1',
			_x( 'mailing', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase.', 'gk-gravityimport' )                                   => 'address.1',
			_x( 'address', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase.', 'gk-gravityimport' )                                   => 'address.1',
			_x( 'city', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase.', 'gk-gravityimport' )                                      => 'address.3',
			_x( 'state', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase. Set to ### if does not apply.', 'gk-gravityimport' )       => 'address.4',
			_x( 'province', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase. Set to ### if does not apply.', 'gk-gravityimport' )    => 'address.4',
			_x( 'region', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase. Set to ### if does not apply.', 'gk-gravityimport' )      => 'address.4',
			_x( 'county', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase.', 'gk-gravityimport' )                                    => 'address.4',
			_x( 'district', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase.', 'gk-gravityimport' )                                 => 'address.4',
			_x( 'postal code', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase. Set to ### if does not apply.', 'gk-gravityimport' ) => 'address.5',
			_x( 'zip code', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase. Set to ### if does not apply.', 'gk-gravityimport' )    => 'address.5',
			_x( 'postcode', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase.', 'gk-gravityimport' )                                  => 'address.5',
			_x( 'zip', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase. Set to ### if does not apply.', 'gk-gravityimport' )         => 'address.5',
			_x( 'country', 'Typemap keyword. Part of a word that matches an address field. Has to be lowercase.', 'gk-gravityimport' )                                   => 'address.6',
		];

		/**
		 * The typemap for new form fields.
		 *
		 * @since 2.0
		 *
		 * @param array  $typemap An associative array of search string => type.
		 * @param string $column  The column header.
		 * @param array  $batch   The batch.
		 */
		return apply_filters( 'gravityview/import/parse/typemap', $base_typemap_cache, $column, $batch );
	}
}
