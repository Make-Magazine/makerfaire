<?php

namespace GravityKit\GravityImport;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class REST_Batch_Controller extends \WP_REST_Controller {
	/**
	 * @inheritDoc
	 */
	public function register_routes() {
		register_rest_route( Core::rest_namespace, "/batches/?", array(
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_batches' ),
				'permission_callback' => array( $this, 'can_get_batches' ),
				'args'                => array(
				),
			),
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_batch' ),
				'permission_callback' => array( $this, 'can_create_batch' ),
				'validate_callback'   => array( $this, 'validate_batch_args' ),
				'args'                => array(
				),
			),
			array(
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_batches' ),
				'permission_callback' => array( $this, 'can_delete_batches' ),
				'args'                => array(
				),
			),
		) );

		register_rest_route( Core::rest_namespace, "/batches/(?P<id>[\d]+)/?", array(
			'args'   => array(
				'id' => array(
					'description' => 'Unique identifier for a batch.',
					'type'        => 'integer',
				),
			),
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_batch' ),
				'permission_callback' => array( $this, 'can_get_batch' ),
				'args'                => array(
				),
			),
			array(
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_batch' ),
				'permission_callback' => array( $this, 'can_delete_batch' ),
				'args'                => array(
				),
			),
			array(
				'methods'             => array( 'POST', 'PUT', 'PATCH' ),
				'callback'            => array( $this, 'update_batch' ),
				'permission_callback' => array( $this, 'can_update_batch' ),
				'args'                => array(
				),
			),
		) );

		register_rest_route( Core::rest_namespace, "/batches/(?P<id>[\d]+)/process/?", array(
			'args'   => array(
				'id' => array(
					'description' => 'Unique identifier for a batch.',
					'type'        => 'integer',
				),
			),
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'process_batch' ),
				'permission_callback' => array( $this, 'can_process_batch' ),
				'args'                => array(
				),
			),
		) );

		register_rest_route( Core::rest_namespace, "/batches/(?P<id>[\d]+)/schedule/?", array(
			'args'   => array(
				'id' => array(
					'description' => 'Unique identifier for a batch.',
					'type'        => 'integer',
				),
			),
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'schedule_batch' ),
				'permission_callback' => array( $this, 'can_process_batch' ),
				'args'                => array(),
			),
		) );

		register_rest_route( Core::rest_namespace, "/batches/(?P<id>[\d]+)/cancel/?", array(
			'args'   => array(
				'id' => array(
					'description' => 'Unique identifier for a batch.',
					'type'        => 'integer',
				),
			),
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'cancel_batch' ),
				'permission_callback' => array( $this, 'can_process_batch' ),
				'args'                => array(),
			),
		) );

		register_rest_route( Core::rest_namespace, "/batches/(?P<id>[\d]+)/pause/?", array(
			'args'   => array(
				'id' => array(
					'description' => 'Unique identifier for a batch.',
					'type'        => 'integer',
				),
			),
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'pause_batch' ),
				'permission_callback' => array( $this, 'can_process_batch' ),
				'args'                => array(),
			),
		) );

		register_rest_route( Core::rest_namespace, "/batches/(?P<id>[\d]+)/resume/?", array(
			'args'   => array(
				'id' => array(
					'description' => 'Unique identifier for a batch.',
					'type'        => 'integer',
				),
			),
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'resume_batch' ),
				'permission_callback' => array( $this, 'can_process_batch' ),
				'args'                => array(),
			),
		) );

		register_rest_route( Core::rest_namespace, "/batches/(?P<id>[\d]+)/dismiss-notice/?", array(
			'args'   => array(
				'id' => array(
					'description' => 'Unique identifier for a batch.',
					'type'        => 'integer',
				),
			),
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'dismiss_batch_notice' ),
				'permission_callback' => array( $this, 'can_process_batch' ),
				'args'                => array(),
			),
		) );

		register_rest_route( Core::rest_namespace, "/batches/(?P<id>[\d]+)/errors(?P<csv>\.csv)?/?", array(
			'args'   => array(
				'id' => array(
					'description' => 'Unique identifier for a batch.',
					'type'        => 'integer',
				),
			),
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_batch_errors' ),
				'permission_callback' => array( $this, 'can_process_batch' ),
				'args'                => array(
					'page'     => array(
						'default' => 1,
						'type'    => 'integer',
						'minimum' => 1,
					),
					'per_page' => array(
						'default' => 100,
						'type'    => 'integer',
						'minimum' => 1,
						'maximum' => 1000,
					),
				),
			),
		) );

		register_rest_route( Core::rest_namespace, "/batches/(?P<id>[\d]+)/generate-failed-csv/?", array(
			'args'   => array(
				'id' => array(
					'description' => 'Unique identifier for a batch.',
					'type'        => 'integer',
				),
			),
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'generate_failed_rows_csv' ),
				'permission_callback' => array( $this, 'can_process_batch' ),
				'args'                => array(),
			),
		) );

		register_rest_route( Core::rest_namespace, "/test/?", array(
			array(
				'methods'             => \WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'test' ),
				'permission_callback' => function() {
					return defined( 'DOING_TESTS' ) && DOING_TESTS;
				},
				'args'                => array(
				),
			),
		) );
	}

	/**
	 * A simple REST API test endpoint for preflight checks in our UI.
	 * @since develop
	 */
	public function test( $request ) {
		return rest_ensure_response( $request->get_method() );
	}

	/**
	 * The batch schema definition.
	 *
	 * @return array Batch transformed schema data.
	 */
	public function get_item_schema() {
		require_once __DIR__ . '/schema.php';

		$schema = gv_import_entries_get_batch_json_schema();

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Create a new batch.
	 *
	 * @param WP_REST_Request   $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function create_batch( $request ) {
		$params = $request->get_params();

		$this->clean_params( $params );

		$batch = Batch::create( $params );
		return rest_ensure_response( $batch );
	}

	/**
	 * Permissions check.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error           True if the request has access to create batches, WP_Error object otherwise.
	 */
	public function can_create_batch( $request ) {
		/**
		 * @deprecated 2.4 Use `gravityview/import/rest/cap` instead.
		 */
		$required_cap = apply_filters( 'gravityview-import/import-cap', 'gravityforms_edit_entries' );

		/**
		 * Modify the REST capability required to import entries. By default: `gravityforms_edit_entries`.
		 *
		 * @since 2.4
		 *
		 * @param string  $cap        The required capability.
		 * @param         string  $permission The accessed permission. Set to the permission check callback method name.
		 * @param WP_REST_Request $request    The REST request.
		 */
		$required_cap = apply_filters( 'gravityview/import/rest/cap', $required_cap, __FUNCTION__, $request );

		// We are about to edit entries, so make sure the current user can do this.
		if ( ! \GFCommon::current_user_can_any( $required_cap ) ) {
			return new \WP_Error(
				'gravityview/import/errors/auth',
				__( "Sorry, you don't have adequate permissions to import entries.", 'gk-gravityimport' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		if ( ! $request->get_param( 'form_id' ) || $request->get_param( 'form_title' ) ) {
			// A new form is about to be created. Can you do this?
			if ( ! \GFCommon::current_user_can_any( 'gravityforms_create_form' ) ) {
				return new \WP_Error(
					'gravityview/import/errors/create_form_auth',
					__( "Sorry, you don't have adequate permissions to create a new form.", 'gk-gravityimport' ),
					array( 'status' => rest_authorization_required_code() )
				);
			}
		}

		return true;
	}

	public function can_delete_batch( $request ) {
		/**
		 * @deprecated 2.4 Use `gravityview/import/rest/cap` instead.
		 */
		$required_cap = apply_filters( 'gravityview-import/import-cap', 'gravityforms_edit_entries' );

		$required_cap = apply_filters( 'gravityview/import/rest/cap', $required_cap, __FUNCTION__, $request );

		// We are about to delete a batch, so make sure the current user can do this.
		if ( ! \GFCommon::current_user_can_any( $required_cap ) ) {
			return new \WP_Error(
				'gravityview/import/errors/auth',
				__( 'Sorry, you are not allowed to delete an import batch as this user.', 'gk-gravityimport' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$batch = Batch::get( $request->get_param( 'id' ) );
		if ( ! $batch  ) {
			return new \WP_Error(
				'gravityview/import/errors/not_found',
				__( 'Batch not found.', 'gk-gravityimport' ),
				array( 'status' => 404 )
			);
		}

		return true;
	}

	public function can_delete_batches( $request ) {
		/**
		 * @deprecated 2.4 Use `gravityview/import/rest/cap` instead.
		 */
		$required_cap = apply_filters( 'gravityview-import/import-cap', 'gravityforms_edit_entries' );

		$required_cap = apply_filters( 'gravityview/import/rest/cap', $required_cap, __FUNCTION__, $request );

		// We are about to delete all the batches, so make sure the current user can do this.
		if ( ! \GFCommon::current_user_can_any( $required_cap ) ) {
			return new \WP_Error(
				'gravityview/import/errors/auth',
				__( 'Sorry, you are not allowed to delete batches as this user.', 'gk-gravityimport' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	public function can_update_batch( $request ) {
		/**
		 * @deprecated 2.4 Use `gravityview/import/rest/cap` instead.
		 */
		$required_cap = apply_filters( 'gravityview-import/import-cap', 'gravityforms_edit_entries' );

		$required_cap = apply_filters( 'gravityview/import/rest/cap', $required_cap, __FUNCTION__, $request );

		// We are about to edit a batch, so make sure the current user can do this.
		if ( ! \GFCommon::current_user_can_any( $required_cap ) ) {
			return new \WP_Error(
				'gravityview/import/errors/auth',
				__( 'Sorry, you are not allowed to edit this batch as this user.', 'gk-gravityimport' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$batch = Batch::get( $request->get_param( 'id' ) );
		if ( ! $batch  ) {
			return new \WP_Error(
				'gravityview/import/errors/not_found',
				__( 'Batch not found.', 'gk-gravityimport' ),
				array( 'status' => 404 )
			);
		}

		return true;
	}

	public function can_get_batch( $request ) {
		/**
		 * @deprecated 2.4 Use `gravityview/import/rest/cap` instead.
		 */
		$required_cap = apply_filters( 'gravityview-import/import-cap', 'gravityforms_edit_entries' );

		$required_cap = apply_filters( 'gravityview/import/rest/cap', $required_cap, __FUNCTION__, $request );

		// We are about to get batch data, so make sure the current user can do this.
		if ( ! \GFCommon::current_user_can_any( $required_cap ) ) {
			return new \WP_Error(
				'gravityview/import/errors/auth',
				__( 'Sorry, you are not allowed to get this batch as this user.', 'gk-gravityimport' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$batch = Batch::get( $request->get_param( 'id' ) );
		if ( ! $batch  ) {
			return new \WP_Error(
				'gravityview/import/errors/not_found',
				__( 'Batch not found.', 'gk-gravityimport' ),
				array( 'status' => 404 )
			);
		}

		return true;
	}

	public function can_get_batches( $request ) {
		/**
		 * @deprecated 2.4 Use `gravityview/import/rest/cap` instead.
		 */
		$required_cap = apply_filters( 'gravityview-import/import-cap', 'gravityforms_edit_entries' );

		$required_cap = apply_filters( 'gravityview/import/rest/cap', $required_cap, __FUNCTION__, $request );

		// We are about to get batch data, so make sure the current user can do this.
		if ( ! \GFCommon::current_user_can_any( $required_cap ) ) {
			return new \WP_Error(
				'gravityview/import/errors/auth',
				__( 'Sorry, you are not allowed to get this batch as this user.', 'gk-gravityimport' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	public function can_process_batch( $request ) {
		/**
		 * @deprecated 2.4 Use `gravityview/import/rest/cap` instead.
		 */
		$required_cap = apply_filters( 'gravityview-import/import-cap', 'gravityforms_edit_entries' );

		$required_cap = apply_filters( 'gravityview/import/rest/cap', $required_cap, __FUNCTION__, $request );

		// We are about to process batch data, so make sure the current user can do this.
		if ( ! \GFCommon::current_user_can_any( $required_cap ) ) {
			return new \WP_Error(
				'gravityview/import/errors/auth',
				__( 'Sorry, you are not allowed to process this batch as this user.', 'gk-gravityimport' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$batch = Batch::get( $request->get_param( 'id' ) );
		if ( ! $batch  ) {
			return new \WP_Error(
				'gravityview/import/errors/not_found',
				__( 'Batch not found.', 'gk-gravityimport' ),
				array( 'status' => 404 )
			);
		}

		return true;
	}

	public function get_batch( $request ) {
		$batch = Batch::get( $request->get_param( 'id' ) );

		if ( $batch ) {
			$batch = BackgroundProcessor::maybe_mark_failed_job( $batch );
			$batch['source_exists'] = ! empty( $batch['source'] ) && file_exists( $batch['source'] );
		}

		return rest_ensure_response( $batch );
	}

	public function get_batches( $request ) {
		$batches = Batch::all(); // @todo Add filtering as needed (status, pagination)
		return rest_ensure_response( $batches );
	}

	public function delete_batch( $request ) {
		return rest_ensure_response( Batch::delete( $request->get_param( 'id' ) ) );
	}

	public function delete_batches( $request ) {
		$batch_ids = wp_list_pluck( Batch::all(), 'id' );
		$results = array_map( array( '\GravityKit\GravityImport\Batch', 'delete' ), $batch_ids );
		return rest_ensure_response( array_combine( $batch_ids, $results ) );
	}

	public function process_batch( $request ) {
		$processor = new Processor( array(
			'batch_id' => $request->get_param( 'id' )
		) );

		return rest_ensure_response( $processor->run() );
	}

	public function update_batch( $request ) {
		$batch = Batch::get( $request->get_param( 'id' ) );

		$params = $request->get_params();

		$this->clean_params( $params );

		// @todo PUT vs. PATCH
		$batch = array_merge( $batch, $params );

		return rest_ensure_response( Batch::update( $batch ) );
	}

	public function get_batch_errors( $request ) {
		wp_raise_memory_limit( 'admin' );

		$batch = Batch::get( $request->get_param( 'id' ) );

		if ( ! empty( $request->get_param( 'csv' ) ) ) {
			return $this->get_batch_errors_csv( $batch );
		}

		return $this->get_batch_errors_json( $request, $batch );
	}

	/**
	 * Streams batch error rows as a CSV download.
	 *
	 * Uses Batch::stream_row_errors() to iterate rows without loading all into memory.
	 * In test mode, collects rows into a buffer since rest_pre_serve_request is not called.
	 *
	 * @since 2.9.0
	 *
	 * @param array $batch The batch data array.
	 *
	 * @return \WP_REST_Response Response object (actual output handled by rest_pre_serve_request filter).
	 */
	private function get_batch_errors_csv( $batch ) {
		$header_row   = $batch['meta']['excerpt'][0] ?? array();
		$header_row[] = '[' . __( 'Failure Reason', 'gk-gravityimport' ) . ']';

		// In test mode, rest_pre_serve_request is not called, so buffer and echo directly.
		if ( defined( 'DOING_TESTS' ) && DOING_TESTS ) {
			ob_start();

			$csv = fopen( 'php://output', 'w' );

			fputcsv( $csv, $header_row );

			foreach ( Batch::stream_row_errors( $batch['id'] ) as $row ) {
				if ( ! $row['data'] ) {
					$row['data'] = array();
				}

				$row_data   = (array) $row['data'];
				$row_data[] = $row['error'] ?? '';

				fputcsv( $csv, $row_data );
			}

			fflush( $csv );
			fclose( $csv );

			$data = rtrim( ob_get_clean() );

			echo $data;

			$response = new \WP_REST_Response( '', 200 );
			$response->header( 'Content-Type', 'text/csv' );

			return $response;
		}

		// Stream CSV directly to php://output via rest_pre_serve_request.
		add_filter( 'rest_pre_serve_request', function () use ( $batch, $header_row ) {
			header( 'Content-Type: text/csv' );
			header( 'Content-Disposition: attachment; filename="errors.csv"' );

			$csv = fopen( 'php://output', 'w' );

			fputcsv( $csv, $header_row );

			foreach ( Batch::stream_row_errors( $batch['id'] ) as $row ) {
				if ( ! $row['data'] ) {
					$row['data'] = array();
				}

				$row_data   = (array) $row['data'];
				$row_data[] = $row['error'] ?? '';

				fputcsv( $csv, $row_data );
			}

			fflush( $csv );
			fclose( $csv );

			return true;
		} );

		return new \WP_REST_Response( '', 200 );
	}

	/**
	 * Returns paginated batch error rows as JSON.
	 *
	 * Uses Batch::get_row_errors_paginated() for memory-safe pagination.
	 * Falls back to batch-level error info when no individual row errors exist.
	 *
	 * @since 2.9.0
	 *
	 * @param \WP_REST_Request $request The REST request.
	 * @param array            $batch   The batch data array.
	 *
	 * @return \WP_REST_Response Response object with pagination headers.
	 */
	private function get_batch_errors_json( $request, $batch ) {
		$page     = (int) $request->get_param( 'page' );
		$per_page = (int) $request->get_param( 'per_page' );

		$result = Batch::get_row_errors_paginated( $batch['id'], $page, $per_page );

		// If no row errors but batch has an error, include batch-level error info.
		if ( empty( $result['rows'] ) && 1 === $page && ! empty( $batch['error'] ) ) {
			$rows = array(
				array(
					'id'          => null,
					'data'        => array(),
					'error'       => $batch['error'],
					'batch_error' => true,
				),
			);

			return rest_ensure_response( $rows );
		}

		$response = rest_ensure_response( $result['rows'] );

		$response->header( 'X-WP-Total', (int) $result['total'] );
		$response->header( 'X-WP-TotalPages', (int) $result['total_pages'] );

		return $response;
	}

	/**
	 * Generates a CSV file containing only the failed rows from a batch import.
	 *
	 * @since 2.9.0
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error on failure.
	 */
	public function generate_failed_rows_csv( $request ) {
		wp_raise_memory_limit( 'admin' );

		$batch = Batch::get( $request->get_param( 'id' ) );

		if ( ! $batch ) {
			return new \WP_Error(
				'gravityview/import/errors/not_found',
				__( 'Batch not found.', 'gk-gravityimport' ),
				array( 'status' => 404 )
			);
		}

		// Query error count directly to avoid loading all rows into memory.
		global $wpdb;

		$tables      = gv_import_entries_get_db_tables();
		$error_count = (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM {$tables['rows']} WHERE batch_id = %d AND status = 'error'",
			$batch['id']
		) );

		if ( 0 === $error_count ) {
			return new \WP_Error(
				'gravityview/import/errors/no_failed_rows',
				__( 'No failed rows found for this batch.', 'gk-gravityimport' ),
				array( 'status' => 404 )
			);
		}

		$upload_dir = wp_upload_dir();
		$filename   = md5( uniqid( time() . 'retry-' . $batch['id'] ) ) . '.csv';
		$filepath   = $upload_dir['path'] . '/' . $filename;
		$csv        = fopen( $filepath, 'w' );

		if ( ! $csv ) {
			return new \WP_Error(
				'gravityview/import/errors/file_write',
				__( 'Unable to create the CSV file.', 'gk-gravityimport' ),
				array( 'status' => 500 )
			);
		}

		// Write the original CSV headers.
		fputcsv( $csv, $batch['meta']['excerpt'][0] ?? array() );

		// Stream rows to disk without loading all into memory.
		foreach ( Batch::stream_row_errors( $batch['id'] ) as $row ) {
			if ( ! $row['data'] ) {
				$row['data'] = array();
			}

			fputcsv( $csv, (array) $row['data'] );
		}

		fclose( $csv );

		return rest_ensure_response( array(
			'source' => $filepath,
			'rows'   => $error_count,
		) );
	}

	/**
	 * Schedules a batch for background processing.
	 *
	 * @since 2.9.0
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error on failure.
	 */
	public function schedule_batch( $request ) {
		$batch_id = $request->get_param( 'id' );
		$filename = $request->get_param( 'filename' );

		if ( $filename ) {
			Batch::set_original_filename( $batch_id, sanitize_file_name( $filename ) );
		}

		$result = BackgroundProcessor::get_instance()->schedule(
			$batch_id,
			get_current_user_id()
		);

		return rest_ensure_response( $result );
	}

	/**
	 * Cancels a background import.
	 *
	 * @since 2.9.0
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response Response object.
	 */
	public function cancel_batch( $request ) {
		$result = BackgroundProcessor::get_instance()->cancel(
			$request->get_param( 'id' )
		);

		return rest_ensure_response( [ 'cancelled' => $result ] );
	}

	/**
	 * Pauses a background import.
	 *
	 * @since 2.9.0
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object.
	 */
	public function pause_batch( $request ) {
		$result = BackgroundProcessor::get_instance()->pause(
			$request->get_param( 'id' )
		);

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return rest_ensure_response( [ 'paused' => true ] );
	}

	/**
	 * Resumes a paused background import.
	 *
	 * @since 2.9.0
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response|\WP_Error Response object.
	 */
	public function resume_batch( $request ) {
		$result = BackgroundProcessor::get_instance()->resume(
			$request->get_param( 'id' )
		);

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return rest_ensure_response( [ 'resumed' => true ] );
	}

	/**
	 * Dismisses the import progress notice for a batch.
	 *
	 * @since 2.9.0
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return \WP_REST_Response Response object.
	 */
	public function dismiss_batch_notice( $request ) {
		$batch_id = $request->get_param( 'id' );

		// Prevent orphaning active scheduler jobs by checking batch status first.
		$batch = BackgroundProcessor::maybe_mark_failed_job( Batch::get( $batch_id ) );

		if ( $batch && in_array( $batch['status'], BackgroundProcessor::ACTIVE_STATUSES, true ) ) {
			$scheduler_status = $batch['scheduler_status'] ?? '';

			if ( in_array( $scheduler_status, [ 'paused', 'pending' ], true ) || empty( $scheduler_status ) ) {
				return new \WP_Error(
					'gravityview/import/errors/active_batch',
					__( 'Cannot dismiss notice while the import is still active.', 'gk-gravityimport' ),
					[ 'status' => 409 ]
				);
			}
		}

		Notices\ImportNotices::get_instance()->remove( $batch_id );

		// Clear the job ID so get_active_import() no longer returns this batch.
		delete_post_meta( $batch_id, '_scheduler_job_id' );

		return rest_ensure_response( [ 'dismissed' => true ] );
	}

	public function validate_batch_args( $request ) {
		$params = $request->get_params();

		$this->clean_params( $params );

		return Batch::validate( $params );
	}

	private function clean_params( &$params ) {
		$rest_params_to_ignore = [
			'rest_route', // No permalinks enabled.
			'wlmdebug', // WishList member debug mode.
			'q', // Query string added on some hosts.
			'health-check-disable-plugin-hash', // Health Check plugin.
		];

		/**
		 * Modifies the list of REST request parameters that are ignored when validating the request.
		 *
		 * @since  2.4
		 *
		 * @param array $rest_params_to_ignore REST request parameters that are safe to ignore.
		 */
		$rest_params_to_ignore = apply_filters( 'gk/gravityimport/rest/request-validation-skip-params', $rest_params_to_ignore );

		foreach ( $rest_params_to_ignore as $param ) {
			unset( $params[ $param ] );
		}
	}
}
