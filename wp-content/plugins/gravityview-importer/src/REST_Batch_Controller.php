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
				),
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
		 * @deprecated Use `gravityview/import/rest/cap`
		 */
		$required_cap = apply_filters( 'gravityview-import/import-cap', 'gravityforms_edit_entries' );

		/**
		 * @filter gravityview/import/rest/cap Modify the REST capability required to import entries. By default: `gravityforms_edit_entries`.
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
		 * @deprecated Use `gravityview/import/rest/cap`
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
		 * @deprecated Use `gravityview/import/rest/cap`
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
		 * @deprecated Use `gravityview/import/rest/cap`
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
		 * @deprecated Use `gravityview/import/rest/cap`
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
		 * @deprecated Use `gravityview/import/rest/cap`
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
		 * @deprecated Use `gravityview/import/rest/cap`
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
		return rest_ensure_response( Batch::get( $request->get_param( 'id' ) ) );
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
		$batch = Batch::get( $request->get_param( 'id' ) );

		$rows = Batch::get_row_errors( $batch['id'], true );

		if ( ! empty( $request->get_param( 'csv' ) ) ) {
			ob_start();

			$csv = fopen( 'php://output', 'w' );

			fputcsv( $csv, $batch['meta']['excerpt'][0] );

			foreach ( $rows as $row ) {
				if ( ! $row['data'] ) {
					$row['data'] = array(); // empty invalid data
				}
				fputcsv( $csv, $row['data'] );
			}

			fflush( $csv );

			$data = rtrim( ob_get_clean() );

			$response = new \WP_REST_Response( '', 200 );
			$response->header( 'Content-Type', 'text/csv' );

			add_filter( 'rest_pre_serve_request', function() use ( $data ) {
				echo $data;
				return true;
			} );

			if ( defined( 'DOING_TESTS' ) && DOING_TESTS ) {
				echo $data; // rest_pre_serve_request is not called in tests
			}

			return $response;
		}

		return rest_ensure_response( $rows );
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
		 * @filter `gk/gravityimport/rest/request-validation-skip-params`
		 *
		 * @since  TBD
		 *
		 * @param array $rest_params_to_ignore REST request parameters that are safe to ignore.
		 */
		$rest_params_to_ignore = apply_filters( 'gk/gravityimport/rest/request-validation-skip-params', $rest_params_to_ignore );

		foreach ( $rest_params_to_ignore as $param ) {
			unset( $params[ $param ] );
		}
	}
}
