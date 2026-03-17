<?php

namespace GravityKit\GravityImport;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Batch {
	/**
	 * @var string The custom post type this maps to.
	 *
	 * Has to be less than 20 characters.
	 */
	const POST_TYPE = 'gv_importentry_batch';

	/**
	 * Registers the batch post type.
	 *
	 * Called on `init` from Core.
	 * Keeps track of new and past import jobs.
	 *
	 * @codeCoverageIgnore Tested during load.
	 *
	 * @see `gv_import_entries_get_batch_json_schema`
	 *
	 * The following parameters are kept in the posts table:
	 *     @param id         A unique batch ID.
	 *
	 * The following parameters are kept in the meta table:
	 *     @param created    When this batch was created
	 *     @param updated    When this batch was updated (progress, statuses)
	 *     @param status     One of:
	 *                           new         this is fresh batch, not configured yet
	 *                           parsing     the source CSV is being parsed (rows table hydration)
	 *                           parsed      the source CSV has been parsed, waiting for launch
	 *                           process     this batch can be launched as soon as possible 
	 *                           processing  this batch is being processed
	 *                           halt        stop processing, resuming is possible
	 *                           halted      stopped but resuming is possible
	 *                           done        all done
	 *                           rollback    a rollback is in progress
	 *                           rolledback  a rollback is done
	 *                           error       an error occurred, read 'error' column
	 *     @param error      Some error message if status is error
	 *     @param schema     A JSON array of objects defining how row columns are mapped into entries:
	 *                           {
	 *                               column  the numeric column ID in the row
	 *                               name    the column name (parsed from CSV headers)
	 *                               field   the Gravity Forms field ID to map into (type if creating new form)
	 *                               flags   special processing rules and exceptions, overrides
	 *                           }
	 *     @param form_id    The form this batch is imported into. Leave empty to create a new form.
	 *     @param feeds      A JSON array of feed IDs to run.
	 *     @param conditions Conditional processing filters inserts in nested JSON:
	 *                           {
	 *                               column  the column number in the row
	 *                               op      one of eq, neq, like, lt, gt
	 *                               value   a literal value or array
	 *                           }
	 *                       Each condition can be grouped and nested into a logical boolean array:
	 *                           { "and": [ condition, condition, { "or": condition, condition } }
	 *     @param source     The source for the CSV data. Can be remote, local file, etc.
	 *     @param flags      A JSON array of special batch settings
	 *     @param meta       A JSON object of parsed source metadata with the following structure:
	 *                           {
	 *                               rows      the number of rows
	 *                               columns   an array with objects of the following structure:
	 *                                   title the title of the column
	 *                                   field the proposed mapping to a field ID if form supplied
	 *                                         or a guessed field type if not
	 *                               excerpt   an array of arrays containing cells, about 20 of them
	 *                                         used as a preview of what's in the CSV
	 *                           }
	 *
	 * @return void
	 */
	public static function register_post_type() {
		register_post_type( Batch::POST_TYPE, array(
			'supports'           => array(),
			'capabilities'       => array( 'administrator' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_in_ui'         => false,
			'show_in_menu'       => false,
			'query_var'          => false,
			'rewrite'            => false,
			'can_export'         => false,
			'show_in_rest'       => false,
		) );
	}

	/**
	 * Create a new batch.
	 *
	 * @see `gv_import_entries_get_batch_json_schema`
	 *
	 * @param array $args The batch arguments.
	 *
	 * @return array|\WP_Error A batch or an error.
	 */
	public static function create( $args ) {
		global $wpdb;

		$defaults = array(
			'schema'      =>  array(),
			'form_id'     =>  null,
			'form_title'  =>  '',
			'feeds'       =>  array(),
			'conditions'  =>  array(),
			'source'      =>  null,
			'flags'       =>  array(),
			'meta'        =>  array(
				'rows'    =>  0,
				'columns' =>  array(),
				'excerpt' =>  array(),
			),
		);

		if ( $not_allowed = array_diff( array_keys( $args ), array_keys( $defaults ) ) ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_args', sprintf( __( 'Arguments not allowed here: %s', 'gk-gravityimport' ), implode( ' ', $not_allowed ) ) );
		}

		$args = shortcode_atts( $defaults, $args );

		$args['status']  = 'new';

		if ( is_wp_error( $error = self::validate( $args ) ) ) {
			return $error;
		}

		$id = wp_insert_post( array(
			'post_type'    => self::POST_TYPE,
		) );

		$args['created'] = $args['updated'] = time();

		if ( is_wp_error( $id ) ) {
			return $id;
		}

		if ( ! $id ) {
			return new \WP_Error( 'gravityview/import/errors/fatal', __( 'Could not create import batch in database.', 'gk-gravityimport' ) );
		}

		$args['id'] = $id;

		/**
		 * A batch is being created.
		 *
		 * @since 2.0
		 *
		 * @param array $batch The batch.
		 */
		$args = apply_filters( 'gravityview/import/batch/create', $args );

		$args['id'] = $id;

		$batch = self::update( $args );

		/**
		 * A batch has been created.
		 *
		 * @since 2.0
		 *
		 * @param array $batch The batch.
		 */
		do_action( 'gravityview/import/batch/created', $batch );

		return $batch;
	}

	/**
	 * Update a batch.
	 *
	 * @param array $batch Any valid batch arguments to update. Requires at least a batch ID.
	 *
	 * @return array|\WP_Error The batch or the error.
	 */
	public static function update( $batch ) {
		$schema = gv_import_entries_get_batch_json_schema();

		if ( ! get_post( $batch['id'] ) ) {
			return new \WP_Error( 'gravityview/import/errors/fatal', __( 'Could not save batch in database.', 'gk-gravityimport' ) );
		}

		foreach ( $schema['properties'] as $key => $property ) {
			if ( isset( $property['extra'] ) && ! empty( $property['extra']['column'] ) ) {
				continue;
			}

			$batch['updated'] = time();

			if ( isset( $batch[ $key ] ) ) {
				update_post_meta( $batch['id'], $key, $batch[ $key ] );
			}
		}

		return Batch::get( $batch['id'] );
	}

	/**
	 * General validation.
	 *
	 * Does not take into account state, etc. Just straight up
	 * preliminary examination and checking against the JSON
	 * schema. Only contains writable values.
	 *
	 * @param array $args Batch arguments.
	 * @see `gv_import_entries_get_batch_json_schema`
	 *
	 * @return true|\WP_Error An error or true.
	 */
	public static function validate( $args ) {
		$schema = gv_import_entries_get_batch_json_schema();

		$defaults = array(
			'id'                => null,
			'status'            => null,
			'schema'            => array(),
			'form_id'           => null,
			'form_title'        => '',
			'feeds'             => array(),
			'conditions'        => array(),
			'source'            => null,
			'original_filename' => '',
			'flags'             => array(),
			'meta'              => array(
				'rows'    => 0,
				'columns' => array(),
				'excerpt' => array(),
			),
		);

		if ( $not_allowed = array_diff( array_keys( $args ), array_keys( $defaults ) ) ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_args', sprintf( __( 'Arguments not allowed here: %s', 'gk-gravityimport' ), implode( ' ', $not_allowed ) ) );
		}

		# Batch with only 2 arguments (form_title|form_id and source) is a new batch and requires no further arguments
		if ( 2 === count( $args ) && ( ! empty( $args['form_title'] ) || ! empty( $args['form_id'] ) ) && ! empty( $args['source'] ) ) {
			return true;
		}

		$args = shortcode_atts( $defaults, $args );

		if ( $args['id'] && is_null( Batch::get( $args['id'] ) ) ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_id', __( 'Invalid batch ID supplied.', 'gk-gravityimport' ) );
		}

		if ( ! in_array( $args['status'], $user = $schema['properties']['status']['extra']['user'] ) ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_status', sprintf( __( 'Invalid status argument. Allowed: %s', 'gk-gravityimport' ), implode( ' ', $user ) ) );
		}

		if ( $args['form_id'] ) {
			if ( ! \GFAPI::form_id_exists( $args['form_id'] ) ) {
				return new \WP_Error( 'gravityview/import/errors/invalid_form', __( 'Invalid form ID supplied.', 'gk-gravityimport' ) );
			}
			$form = \GFAPI::get_form( $args['form_id'] );
		} else {
			$form = null;
		}

		$seen_fields = array();
		if ( $args['schema'] ) {
			// Validate the schema.
			foreach ( $args['schema'] as $id => $rule ) {
				if ( is_wp_error( $error = self::validate_rule( $id, $rule, $form ) ) ) {
					return $error;
				}

				if ( $form ) {
					if ( isset( $seen_fields[ $rule['field'] ] ) ) {
						// Duplicate field sinks are not allowed.
						return self::get_duplicate_field_error( $rule, $seen_fields[ $rule['field'] ], $form );
					}
					$seen_fields[ $rule['field'] ] = $rule;
				}
			}
		}

		if ( $form && $args['feeds'] ) {
			// All given feeds have to be active.
			$feeds    = \GFAPI::get_feeds( array(), $form['id'], null, true );
			$feed_ids = is_wp_error( $feeds ) ? array() : wp_list_pluck( $feeds, 'id' );

			foreach ( $args['feeds'] as $feed_id ) {
				if ( ! in_array( $feed_id, $feed_ids ) ) {
					return new \WP_Error( 'gravityview/import/errors/invalid_feed', sprintf( __( 'Invalid feed ID %s provided for form ID %s.', 'gk-gravityimport' ), $feed_id, $form['id'] ), array( 'feed_id' => $feed_id, 'form_id' => $form['id'] ) );
				}
			}
		} else if ( $args['feeds'] ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_feed', __( '"feeds" parameter must be empty for new forms.', 'gk-gravityimport' ) );
		}

		if ( $args['conditions'] ) {
			if ( is_wp_error( $error = self::validate_condition( $args['conditions'] ) ) ) {
				return $error;
			}
		}

		if ( empty( $args['source'] ) ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_source', __( '"source" parameter is required', 'gk-gravityimport' ) );
		}

		if ( $args['flags'] ) {
			if ( $not_allowed = array_diff( $args['flags'], $schema['properties']['flags']['anyOf'] ) ) {
				return new \WP_Error( 'gravityview/import/errors/invalid_flags', sprintf( __( 'Unknown flags: %s', 'gk-gravityimport' ), implode( ' ', $not_allowed ) ) );
			}
		}

		if ( array_keys( $args['meta'] ) != array( 'rows', 'columns', 'excerpt' ) ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_meta', __( 'Invalid meta structure', 'gk-gravityimport' ), array( 'meta' => $args['meta'] ) );
		}

		if ( ! is_numeric( $args['meta']['rows'] ) || $args['meta']['rows'] < 0 ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_meta', __( 'Invalid meta structure', 'gk-gravityimport' ), array( 'meta' => $args['meta'] ) );
		}

		if ( ! is_array( $args['meta']['columns'] ) ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_meta', __( 'Invalid meta structure', 'gk-gravityimport' ), array( 'meta' => $args['meta'] ) );
		}

		foreach ( $args['meta']['columns'] as $id => $column ) {
			if ( is_wp_error( $error = self::validate_metacolumn( $id, $column, $form ) ) ) {
				return $error;
			}
		}

		return true;
	}

	/**
	 * Validate a rule.
	 *
	 * @param int          $id   The rule number. Used in the error.
	 * @param array        $rule Rule arguments.
	 * @param array|null   $form The form or null if new form.
	 *
	 * @see `gv_import_entries_get_batch_json_schema`
	 *
	 * @return true|\WP_Error An error or true.
	 */
	public static function validate_rule( $id, $rule, $form ) {
		$defaults = array(
			'column' => -1,
			'name'   => '',
			'field'  => null,
			'flags'  => array(),
			'meta'   => array(),
		);

		if ( $not_allowed = array_diff( array_keys( $rule ), array_keys( $defaults ) ) ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_args', sprintf( __( 'Rule %d arguments not allowed here: %d', 'gk-gravityimport' ), $id, implode( ' ', $not_allowed ) ) );
		}

		$rule = shortcode_atts( $defaults, $rule );

		$is_meta = ! empty( $rule['meta']['is_meta'] );

		if ( ! is_numeric( $rule['column'] ) || $rule['column'] < 0 ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_rule', sprintf( __( 'Rule %d invalid column number.', 'gk-gravityimport' ), $id ), array( 'status' => 400 ) );
		}

		if ( ! $rule['field'] ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_rule', sprintf( __( 'Rule %d field cannot be empty.', 'gk-gravityimport' ), $id ), array( 'status' => 400 ) );
		}

		if ( ! $is_meta && $form ) {
			if ( is_numeric( $rule['field'] ) ) {
				if ( ! $field = \GFFormsModel::get_field( $form, $rule['field'] ) ) {
					return new \WP_Error( 'gravityview/import/errors/invalid_rule', sprintf( __( 'Rule %d invalid field ID for form.', 'gk-gravityimport' ), $id ), array( 'status' => 400 ) );
				}

				if ( intval( $rule['field'] ) != $rule['field'] ) {
					if ( ! $field['inputs'] || ! in_array( $rule['field'], wp_list_pluck( $field['inputs'], 'id' ) ) ) {
						return new \WP_Error( 'gravityview/import/errors/invalid_rule', sprintf( __( 'Rule %d invalid field ID for form.', 'gk-gravityimport' ), $id ), array( 'status' => 400 ) );
					}
				}
			} else if ( 'notes' === $rule['field'] ) {
				// All good, we know thy secret type.
			} else if ( ! in_array( $rule['field'], \GFFormsModel::get_lead_db_columns() ) ) {
				return new \WP_Error( 'gravityview/import/errors/invalid_rule', sprintf( __( 'Rule %d invalid field ID for form.', 'gk-gravityimport' ), $id ), array( 'status' => 400 ) );
			}
		} else if ( ! $is_meta ) {
			/**
			 * Parse out just the type.
			 */
			$type = current( explode( '.', $rule['field'] ) );
			$type = current( explode( '[', $type ) );

			if ( ! Core::is_entry_column( $rule['field'] ) && ! \GF_Fields::exists( $type ) ) {
				return new \WP_Error( 'gravityview/import/errors/invalid_rule', sprintf( __( 'Rule %d unknown field type.', 'gk-gravityimport' ), $id ), array( 'status' => 400 ) );
			}
		}

		$schema = gv_import_entries_get_batch_json_schema();

		foreach ( (array)$rule['flags'] as $flag ) {
			if ( ! in_array( $flag, $allowed = $schema['definitions']['rule']['properties']['flags']['anyOf'] ) ) {
				return new \WP_Error( 'gravityview/import/errors/invalid_rule', sprintf( __( 'Rule %d unknown flag. Allowed: %s', 'gk-gravityimport' ), $id, implode( ' ', $allowed ) ), array( 'status' => 400 ) );
			}
		}

		foreach ( (array)$rule['meta'] as $meta => $_ ) {
			if ( ! in_array( $meta, $allowed = array_keys( $schema['definitions']['rule']['properties']['meta']['properties'] ) ) ) {
				return new \WP_Error( 'gravityview/import/errors/invalid_rule', sprintf( __( 'Rule %d unknown meta. Allowed: %s', 'gk-gravityimport' ), $id, implode( ' ', $allowed ) ), array( 'status' => 400 ) );
			}
		}

		return true;
	}

	/**
	 * Validate a condition.
	 *
	 * Recursive.
	 *
	 * @param array $condition A condition.
	 * @see `gv_import_entries_get_batch_json_schema`
	 *
	 * @return true|\WP_Error An error or true.
	 */
	public static function validate_condition( $condition ) {
		$defaults = array(
			'column' => null,
			'op'     => null,
			'value'  => array(),
		);

		if ( $not_allowed = array_diff( array_keys( $condition ), array_keys( $defaults ) ) ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_args', sprintf( __( 'Condition arguments not allowed here: %s', 'gk-gravityimport' ),  implode( ' ', $not_allowed ) ) );
		}

		$condition = shortcode_atts( $defaults, $condition );

		$schema = gv_import_entries_get_batch_json_schema();
		
		$boolean_logic = $schema['definitions']['condition']['properties']['op']['extra']['boolean'];
		$comparators   = array_diff( $schema['definitions']['condition']['properties']['op']['enum'], $boolean_logic );

		if ( is_numeric( $condition['column'] ) ) {
			if ( $condition['column'] < 0 ) {
				return new \WP_Error( 'gravityview/import/errors/invalid_condition', __( 'Condition invalid column number.', 'gk-gravityimport' ) );
			}

			if ( ! in_array( $condition['op'], $comparators ) ) {
				return new \WP_Error( 'gravityview/import/errors/invalid_condition', __( 'Condition invalid operation.', 'gk-gravityimport' ) );
			}

			if ( ! is_string( $condition['value'] ) && ! is_numeric( $condition['value'] ) ) {
				return new \WP_Error( 'gravityview/import/errors/invalid_condition', __( 'Condition invalid literal value.', 'gk-gravityimport' ) );
			}

			return true;
		}

		// Nested condition.
		if ( empty( $condition['column'] ) && is_array( $condition['value'] ) ) {
			if ( ! in_array( $condition['op'], $boolean_logic ) ) {
				return new \WP_Error( 'gravityview/import/errors/invalid_condition', __( 'Invalid boolean operator for nested conditions.', 'gk-gravityimport' ) );
			}

			foreach ( $condition['value'] as $condition ) {
				if ( is_wp_error( $error = self::validate_condition( $condition ) ) ) {
					return $error;
				}
			}

			return true;
		}

		return new \WP_Error( 'gravityview/import/errors/invalid_condition', __( 'Malformed condition. Missing column or condition chain.', 'gk-gravityimport' ) );
	}

	/**
	 * Validate a metacolumn.
	 *
	 * @param int          $id     The metacolumn number. Used in the error.
	 * @param array        $column Column arguments.
	 * @param array|null   $form   The form or null if new form.
	 *
	 * @see `gv_import_entries_get_batch_json_schema`
	 *
	 * @return true|\WP_Error An error or true.
	 */
	public static function validate_metacolumn( $id, $column, $form ) {
		$defaults = array(
			'title' => '',
			'field' => null,
		);

		if ( $not_allowed = array_diff( array_keys( $column ), array_keys( $defaults ) ) ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_args', sprintf( __( 'Column meta %d arguments not allowed here: %d', 'gk-gravityimport' ), $id, implode( ' ', $not_allowed ) ) );
		}

		$rule = shortcode_atts( $defaults, $column );

		if ( ! is_string( $column['title'] ) ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_columnmeta', sprintf( __( 'Column meta %d invalid column title.', 'gk-gravityimport' ), $id ) );
		}

		if ( $form ) {
			if ( is_numeric( $rule['field'] ) ) {
				if ( ! $field = \GFFormsModel::get_field( $form, $rule['field'] ) ) {
					return new \WP_Error( 'gravityview/import/errors/invalid_columnmeta', sprintf( __( 'Column meta %d invalid field ID for form.', 'gk-gravityimport' ), $id ) );
				}

				if ( intval( $rule['field'] ) != $rule['field'] ) {
					if ( ! $field['inputs'] || ! in_array( $rule['field'], wp_list_pluck( $field['inputs'], 'id' ) ) ) {
						return new \WP_Error( 'gravityview/import/errors/invalid_columnmeta', sprintf( __( 'Column meta %d invalid field ID for form.', 'gk-gravityimport' ), $id ) );
					}
				}
			} else if ( ! in_array( $rule['field'], \GFFormsModel::get_lead_db_columns() ) ) {
				return new \WP_Error( 'gravityview/import/errors/invalid_columnmeta', sprintf( __( 'Column meta %d invalid field ID for form.', 'gk-gravityimport' ), $id ) );
			}
		} else {
			if ( ! \GF_Fields::exists( $rule['field'] ) ) {
				return new \WP_Error( 'gravityview/import/errors/invalid_columnmeta', sprintf( __( 'Column meta %d unknown field type.', 'gk-gravityimport' ), $id ) );
			}
		}

		return true;
	}


	/**
	 * Stores the Scheduler job ID for a batch.
	 *
	 * @since 2.9.0
	 *
	 * @param int $batch_id The batch ID.
	 * @param int $job_id   The Scheduler job ID.
	 */
	public static function set_job_id( $batch_id, $job_id ) {
		update_post_meta( $batch_id, '_scheduler_job_id', (int) $job_id );
	}

	/**
	 * Retrieves the Scheduler job ID for a batch.
	 *
	 * @since 2.9.0
	 *
	 * @param int $batch_id The batch ID.
	 *
	 * @return int The Scheduler job ID, or 0 if not set.
	 */
	public static function get_job_id( $batch_id ) {
		return (int) get_post_meta( $batch_id, '_scheduler_job_id', true );
	}

	/**
	 * Stores the original uploaded filename for a batch.
	 *
	 * @since 2.9.0
	 *
	 * @param int    $batch_id The batch ID.
	 * @param string $filename The original filename.
	 */
	public static function set_original_filename( $batch_id, $filename ) {
		update_post_meta( $batch_id, '_original_filename', $filename );
	}

	/**
	 * Retrieves the original uploaded filename for a batch.
	 *
	 * @since 2.9.0
	 *
	 * @param int $batch_id The batch ID.
	 *
	 * @return string The original filename, or empty string if not set.
	 */
	public static function get_original_filename( $batch_id ) {
		return (string) get_post_meta( $batch_id, '_original_filename', true );
	}

	/**
	 * Get a batch by ID.
	 *
	 * @param int $batch_id The batch ID.
	 *
	 * @return null|array The batch or null.
	 */
	public static function get( $batch_id ) {
		if ( ! $post = get_post( $batch_id ) ) {
			return null;
		}

		if ( $post->post_type != Batch::POST_TYPE ) {
			return null;
		}

		return self::post_to_array( $post );
	}

	/**
	 * Get all batches.
	 *
	 * @param array $args Filters.
	 *                    $limit  int             the number of baches to return,
	 *                    $order  string          order by a batch field,
	 *                    $sort   string          direction ASC, DESC,
	 *                    $status string|string[] filter by status(es)
	 *
	 * @return array Of batches.
	 */
	public static function all( $args = array() ) {
		global $wpdb;

		$args = wp_parse_args( $args, array(
			'limit'  => 0,
			'order'  => null,
			'sort'   => 'DESC',
			'status' => array(),
		) );

		/**
		 * Protect against column name injection. Do not remove.
		 */
		if ( ! in_array( $args['order'], array( 'ID', 'created', 'updated' ), true ) ) {
			$args['order'] = '';
		}

		/**
		 * Protect against injection of sort direction.
		 */
		if ( ! in_array( $args['sort'], array( 'ASC', 'DESC' ), true ) ) {
			$args['sort'] = 'ASC';
		}

		if ( ! is_array( $args['status'] ) ) {
			$args['status'] = array( $args['status'] );
		}

		$tables = gv_import_entries_get_db_tables();

		$sql = array(
			"SELECT ID FROM $wpdb->posts",
			"WHERE",
			$wpdb->prepare( "post_type = %s", Batch::POST_TYPE ),

			/**
			 * Status
			 */
			count( $args['status'] ) ?
				"AND post_status IN (" . implode( ', ', array_map( function( $status ) use ( $wpdb ) {
					return $wpdb->prepare( '%s', $status );
				}, $args['status'] ) ) . ")" : '',

			/**
			 * ORDER BY
			 */
			$args['order'] ? "ORDER BY `{$args['order']}`" : '',
			( $args['order'] && $args['sort'] ) ? $args['sort'] : '',

			/**
			 * LIMIT
			 */
			( $args['limit'] > 0 ) ? $wpdb->prepare( 'LIMIT %d', $args['limit'] ) : '',
		);

		/**
		 * We perform a direct query to avoid meta overhead.
		 */
		$batches = array_map( array( __CLASS__, 'get' ), $wpdb->get_col( implode( ' ', array_filter( $sql ) ) ) );

		return array_filter( $batches, function( $batch ) {
			return ! is_null( $batch );
		} );
	}

	/**
	 * Delete a batch.
	 *
	 * @param int $batch_id The batch ID.
	 *
	 * @return true|\WP_Error
	 */
	public static function delete( $batch_id ) {
		global $wpdb;

		$tables = gv_import_entries_get_db_tables();

		if ( ! $batch = Batch::get( $batch_id ) ) {
			return new \WP_Error( 'gravityview/import/errors/not_found', __( 'Unknown batch ID', 'gk-gravityimport' ) );
		}

		if ( in_array( $batch['status'], array( 'parsing', 'processing', 'halt', 'rollback' ) ) ) {
			return new \WP_Error( 'gravityview/import/errors/invalid_status', sprintf( __( 'Batch is not in stop state: %s', 'gk-gravityimport' ), $batch['status'] ) );
		}

		wp_delete_post( $batch_id );
		$wpdb->delete( $tables['rows'], array( 'batch_id' => $batch_id ) );
		$wpdb->delete( $tables['log'], array( 'batch_id' => $batch_id ) );

		// If source is a filepath and doesn't need to be kept delete it.
		if ( ! in_array( 'keepsource', $batch['flags'] ) && file_exists( $batch['source'] ) && is_writable( $batch['source'] ) ) {
			wp_delete_file( $batch['source'] );
		}
		
		return true;
	}

	/**
	 * Transform a post object into a JSONifiable array.
	 *
	 * @see `gv_import_entries_get_batch_json_schema`
	 *
	 * @param \WP_Post The post.
	 *
	 * @return array The batch in array form.
	 */
	public static function post_to_array( $post ) {
		global $wpdb;

		$tables = gv_import_entries_get_db_tables();

		$progress = $wpdb->get_results( $wpdb->prepare( "SELECT status, COUNT(status) c FROM {$tables['rows']} WHERE batch_id = %d GROUP BY status", $post->ID ), ARRAY_A );

		$total = ( empty( $post->progress ) || empty( $post->progress['total'] ) ) ? 0 : $post->progress['total'];

		return array(
			'id'          => $post->ID,
			'status'      => $post->status,
			'error'       => $post->error ? : '',
			'progress'    => wp_parse_args(
				array_merge(
					array( 'total' => $total ),
					array_combine(
						wp_list_pluck( $progress, 'status' ), array_map( 'intval', wp_list_pluck( $progress, 'c' ) )
					)
				), array_combine( array( 'total', 'new', 'processing', 'processed', 'skipped', 'error' ), array_fill( 0, 6, 0 ) )
			),
			'created'     => $post->created,
			'updated'     => $post->updated,
			'schema'      => $post->schema ? : array(),
			'form_id'     => $post->form_id ? : null,
			'form_title'  => self::resolve_form_title( $post ),
			'feeds'       => $post->feeds ? : array(),
			'conditions'  => $post->conditions? : array(),
			'source'            => $post->source,
			'original_filename' => self::get_original_filename( $post->ID ),
			'flags'             => $post->flags ? : array(),
			'meta'        => $post->meta ? : array(
				'total'   => 0,
				'rows'    => 0,
				'columns' => array(),
				'excerpt' => array(),
			)
		);
	}

	/**
	 * Resolves the form title for a batch post.
	 *
	 * Falls back to the Gravity Forms API if the batch doesn't have a stored title.
	 *
	 * @since 2.9.0
	 *
	 * @param \WP_Post $post The batch post object.
	 *
	 * @return string The form title, or empty string if unavailable.
	 */
	private static function resolve_form_title( $post ) {
		if ( ! empty( $post->form_title ) ) {
			return $post->form_title;
		}

		$form_id = $post->form_id ? : null;

		if ( ! $form_id || ! class_exists( '\GFAPI' ) ) {
			return '';
		}

		$form = \GFAPI::get_form( $form_id );

		if ( ! $form || is_wp_error( $form ) ) {
			return '';
		}

		return $form['title'] ?? '';
	}

	/**
	 * Return row errors
	 *
	 * @param int $batch_id The batch ID.
	 * @param boolean $include_data Whether to include row data in response.
	 *
	 * @return array Row errors.
	 */
	public static function get_row_errors( $batch_id, $include_data = false ) {
		global $wpdb;

		$batch = Batch::get( $batch_id );

		$tables = gv_import_entries_get_db_tables();

		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT number, " . ( $include_data ? "data, " : " " ) . " error FROM {$tables['rows']} WHERE batch_id = %d AND status = 'error'", $batch['id'] ), ARRAY_A );

		return array_map( function ( $r ) use ( $include_data ) {

			$r['number'] = intval( $r['number'] );
			if ( $include_data ) {
				$r['data'] = json_decode( $r['data'] );
			}

			return $r;
		}, $rows );
	}

	/**
	 * Yields row errors in memory-safe chunks using a generator.
	 *
	 * Unlike get_row_errors(), this method never loads all failed rows into memory at once.
	 * Each chunk is fetched, yielded row-by-row, then freed before the next chunk is fetched.
	 *
	 * @since 2.9.0
	 *
	 * @param int $batch_id   The batch ID.
	 * @param int $chunk_size Number of rows to fetch per database query. Default: 500.
	 *
	 * @return \Generator Yields associative arrays with 'number', 'data', and 'error' keys.
	 */
	public static function stream_row_errors( $batch_id, $chunk_size = 500 ) {
		global $wpdb;

		$batch = Batch::get( $batch_id );

		if ( ! $batch ) {
			return;
		}

		$tables = gv_import_entries_get_db_tables();

		$total = (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM {$tables['rows']} WHERE batch_id = %d AND status = 'error'",
			$batch['id']
		) );

		if ( 0 === $total ) {
			return;
		}

		$offset = 0;

		while ( $offset < $total ) {
			$rows = $wpdb->get_results( $wpdb->prepare(
				"SELECT number, data, error FROM {$tables['rows']} WHERE batch_id = %d AND status = 'error' ORDER BY number ASC LIMIT %d OFFSET %d",
				$batch['id'],
				$chunk_size,
				$offset
			), ARRAY_A );

			if ( empty( $rows ) ) {
				break;
			}

			foreach ( $rows as $row ) {
				$row['number'] = (int) $row['number'];
				$row['data'] = json_decode( $row['data'] );

				yield $row;
			}

			// Free memory from the last query result.
			$wpdb->last_result = array();

			$offset += $chunk_size;
		}
	}

	/**
	 * Returns a single page of row errors with pagination metadata.
	 *
	 * @since 2.9.0
	 *
	 * @param int $batch_id The batch ID.
	 * @param int $page     The page number (1-based). Default: 1.
	 * @param int $per_page Number of rows per page. Default: 100.
	 *
	 * @return array {
	 *     @type array $rows        Array of row error arrays with 'number', 'data', and 'error' keys.
	 *     @type int   $total       Total number of error rows.
	 *     @type int   $total_pages Total number of pages.
	 * }
	 */
	public static function get_row_errors_paginated( $batch_id, $page = 1, $per_page = 100 ) {
		global $wpdb;

		$batch = Batch::get( $batch_id );

		if ( ! $batch ) {
			return array(
				'rows'        => array(),
				'total'       => 0,
				'total_pages' => 0,
			);
		}

		$tables = gv_import_entries_get_db_tables();

		$total = (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM {$tables['rows']} WHERE batch_id = %d AND status = 'error'",
			$batch['id']
		) );

		$offset = ( $page - 1 ) * $per_page;

		$rows = $wpdb->get_results( $wpdb->prepare(
			"SELECT number, data, error FROM {$tables['rows']} WHERE batch_id = %d AND status = 'error' ORDER BY number ASC LIMIT %d OFFSET %d",
			$batch['id'],
			$per_page,
			$offset
		), ARRAY_A );

		$rows = array_map( function ( $row ) {
			$row['number'] = (int) $row['number'];
			$row['data'] = json_decode( $row['data'] );

			return $row;
		}, $rows );

		return array(
			'rows'        => $rows,
			'total'       => $total,
			'total_pages' => (int) ceil( $total / $per_page ),
		);
	}

	/**
	 * Recursively test a condition.
	 *
	 * @param array $condition The batch condition.
	 * @param array $data      The data (row) to validate.
	 * @see `gv_import_entries_get_batch_json_schema`
	 *
	 * @return boolean|\WP_Error Whether it passes or not, error if invalid condition.
	 */
	public static function test_condition( $condition, $data ) {
		if ( empty( $condition ) ) {
			return true;
		}

		if ( is_wp_error( $error = self::validate_condition( $condition ) ) ) {
			return $error;
		}

		$defaults = array(
			'column' => null,
			'op'     => null,
			'value'  => array(),
		);

		$condition = shortcode_atts( $defaults, $condition );

		$schema = gv_import_entries_get_batch_json_schema();
		
		$boolean_logic = $schema['definitions']['condition']['properties']['op']['extra']['boolean'];

		// Nested condition.
		if ( empty( $condition['column'] ) && is_array( $condition['value'] ) ) {
			if ( in_array( $condition['op'], $boolean_logic ) ) {
				foreach ( $condition['value'] as $sub_condition ) {
					if ( is_wp_error( $error = $result = self::test_condition( $sub_condition, $data ) ) ) {
						return $error;
					}

					if ( 'or' === $condition['op'] ) {
						if ( true === $result ) {
							// Return true if any of the conditions are true
							return $result;
						}
					} else {
						if ( false === $result ) {
							// Return false if any of the conditions are false
							return $result;
						}
					}
				}

				if ( 'or' === $condition['op'] ) {
					// None of the OR conditions matched
					return false;
				} else {
					// None of the AND conditions failed
					return true;
				}
			}
		}

		$value = ( ! empty( $data[ $condition['column'] ] ) ) ? $data[ $condition['column'] ] : '';

		switch ( $op = $condition['op'] ):
			case 'eq':
				return $condition['value'] === $value;
			case 'neq':
				return $condition['value'] !== $value;
			case 'like':
				return strpos( $value, $condition['value'] ) !== false;
			case 'nlike':
				return strpos( $value, $condition['value'] ) === false;
			case 'gt':
			case 'lt':
				if ( is_numeric( $value ) && is_numeric( $condition['value'] ) ) {
					settype( $value, 'float' );
					settype( $condition['value'], 'float' );
				}
				return ( 'gt' === $op ) ? $value > $condition['value'] : $value < $condition['value'];
		endswitch;

		return false;
	}

	/**
	 * Generate a descriptive error message for duplicate field mapping.
	 *
	 * @param array $rule The rule attempting to map to the field.
	 * @param array $existing_rule The rule already mapped to the field.
	 * @param array $form The Gravity Forms form array.
	 *
	 * @return \WP_Error
	 */
	private static function get_duplicate_field_error( $rule, $existing_rule, $form ) {
		$field_label = $rule['field'];
		
		// Try to get a more descriptive field label
		if ( is_numeric( $rule['field'] ) ) {
			$field = \GFFormsModel::get_field( $form, $rule['field'] );
			if ( $field && ! empty( $field->label ) ) {
				$field_label = sprintf( '"%s" (ID: %s)', $field->label, $rule['field'] );
			}
		} elseif ( ! empty( $rule['meta'] ) && ! empty( $rule['meta']['is_meta'] ) ) {
			$field_label = sprintf( 'meta field "%s"', $rule['field'] );
		}
		
		$error_message = sprintf( 
			__( 'Field %s cannot be mapped to multiple columns. It is already mapped to column %d and cannot also be mapped to column %d.', 'gk-gravityimport' ), 
			$field_label,
			$existing_rule['column'] + 1, // Add 1 for user-friendly column numbering
			$rule['column'] + 1 // Add 1 for user-friendly column numbering
		);
		
		return new \WP_Error( 'gravityview/import/errors/invalid_rule', $error_message, array( 
			'rule' => $rule, 
			'existing_rule' => $existing_rule,
			'status' => 400 
		) );
	}
}
