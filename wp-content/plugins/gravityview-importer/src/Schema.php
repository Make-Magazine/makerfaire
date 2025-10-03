<?php

namespace GravityKit\GravityImport;

if ( ! defined( 'ABSPATH' ) ) return; // Exit if accessed directly

/**
 * Returns the database schema as an array.
 *
 * Fed into `dbDelta` eventually.
 *
 * @codeCoverageIgnore Tested during installation
 *
 * @return string SQL create code for tables, etc.
 */
function gv_import_entries_get_db_schema() {
	global $wpdb;

	$tables          = gv_import_entries_get_db_tables();
	$charset_collate = $wpdb->get_charset_collate();

	$schema = array(

	/**
	 * The rows table.
	 *
	 * A database representation of a batch source.
	 *
	 * @param id       A unique row ID
	 * @param batch_id The unique batch ID this row belongs to
	 * @param entry_id The entry ID this ended up being inserted as
	 * @param number   The row number as it appears in the CSV
	 * @param status   One of:
	 *                     new        An unprocessed row
	 *                     processing Processing this row
	 *                     processed  A processed row
	 *                     skipped    A row that has been skipped
	 *                     error      Errored
	 * @param error    An error message when status is error
	 * @param data     A JSON array representation of data
	 */
"CREATE TABLE `{$tables['rows']}` (
	`id`         BIGINT UNSIGNED AUTO_INCREMENT,
	`batch_id`   BIGINT UNSIGNED NOT NULL,
	`entry_id`   BIGINT UNSIGNED,
	`number`     INT UNSIGNED NOT NULL,
	`status`     ENUM('new','processing', 'processed', 'skipped', 'error'),
	`error`      LONGTEXT,
	`data`       LONGTEXT,

	PRIMARY KEY  (`id`),
	KEY batch_id (`batch_id`),
	KEY batch_status (`batch_id`, `status`)
) $charset_collate",

	/**
	 * A log of all operations done.
	 *
	 * Used for rollback functionality. Can be suppressed by using
	 * the nolog flag for a specific batch as it can get really large
	 * for big or frequent imports. Deleted when a batch is deleted.
	 *
	 * @param id        A unique log entry ID
	 * @param batch_id  The batch this entry is associated with (used for flushes)
	 * @param row_id    The row this entry is associated with
	 * @param timestamp A microtimestamp of the operation
	 * @param op        One of:
	 *                     delete This data was deleted
	 *                     insert This data was inserted
	 * @param data      A JSON array representation of one database row:
	 *                      { "table": { "column": "value" },
	 *                        "table": [ { "column": "value" }, { "column": "value" } ] }
	 */
"CREATE TABLE `{$tables['log']}` (
	`id`          BIGINT UNSIGNED AUTO_INCREMENT,
	`batch_id`    BIGINT UNSIGNED NOT NULL,
	`row_id`      BIGINT UNSIGNED,
	`timestamp`   DOUBLE(14,4) UNSIGNED NOT NULL,
	`op`          ENUM('delete','insert'),
	`data`        LONGTEXT,

	PRIMARY KEY     (`id`),
	KEY    batch_id (`batch_id`),
	KEY    row_id   (`row_id`),
	KEY timestamp   (`timestamp`)
) $charset_collate",
	);

	return $schema;
}

/**
 * Returns the table names used by the import plugin.
 *
 * Returns an array with the following keys:
 *    rows The rows table
 *    log  The log table
 *
 * @return array The tables.
 */
function gv_import_entries_get_db_tables() {
	global $wpdb;

	$prefix = $wpdb->prefix;

	return array(
		'rows' => "{$prefix}gv_importentry_rows",
		'log'  => "{$prefix}gv_importentry_log",
	);
}

/**
 * Returns the JSON schema for the Batch type.
 *
 * JSON Schema Draft 04 (sort of)
 *
 * @return array The JSON schema that can be encoded.
 */
function gv_import_entries_get_batch_json_schema() {
	$schema = array(
		'$schema'        => 'http://json-schema.org/draft-04/schema#',
		'title'          => 'batch',
		'type'           => 'object',

		'properties'     => array(
			'id'         => array(
				'description' => 'Unique import batch ID.',
				'type'        => 'integer',
				'context'     =>  array( 'view', 'edit' ),
				'readonly'    =>  true,
				'extra'       =>  array(
					'column'  => 'posts.ID', // Store in wp_posts, not meta
				),
			),
			'status'     => array(
				'description' => 'Import batch status.',
				'type'        => 'string',
				'enum'        =>  array(
					 'new',        // This is fresh batch, not configured yet
					 'parsing',    // The source CSV is being parsed (rows table hydration)
					 'parsed',     // The source CSV has been parsed, waiting for launch
					 'process',    // This batch can be launched as soon as possible
					 'processing', // This batch is being processed
					 'halt',       // Stop processing, resuming is possible
					 'halted',     // Processing is stopped, but resuming is possible
					 'done',       // All done
					 'rollback',   // A rollback is in progress
					 'rolledback', // A rollback is done
					 'error',      // An error occurred, read 'error' column
				),
				'context'     =>  array( 'view', 'edit' ),
				'readonly'    =>  false,
				'extra'       =>  array(
					// API settable states
					'user'    =>  array( 'new', 'process', 'halt', 'rollback' ),
					// Stop states
					'stop'    =>  array( 'halted', 'done', 'rolledback', 'error' ),
				),
			),
			'error'      => array(
				'description' => 'An error string in case of batch failure.',
				'type'        => 'string',
				'context'     =>  array( 'view' ),
				'readonly'    =>  true,
			),
			'progress'   => array(
				'description' => 'The total number of rows processed grouped by their statuses.',
				'items'       =>  array( '$ref' => '#/definitions/progress' ),
				'context'     =>  array( 'view' ),
				'readonly'    =>  true,
			),
			'created'    => array(
				'description' => 'The UNIX timestamp when this import batch was created.',
				'type'        => 'integer',
				'context'     =>  array( 'view', 'edit' ),
				'readonly'    =>  true,
			),
			'updated'    => array(
				'description' => 'The UNIX timestamp when this import batch was last updated.',
				'type'        => 'integer',
				'context'     =>  array( 'view', 'edit' ),
				'readonly'    =>  true,
			),
			'schema'     => array(
				'description' => 'An array of column to entry field mapping rules.',
				'type'        => 'array',
				'items'       =>  array( '$ref' => '#/definitions/rule' ),
				'context'     =>  array( 'view', 'edit' ),
				'readonly'    =>  false,
			),
			'form_id'    =>  array(
				'description' => 'The Gravity Forms form ID to map into.',
				'type'        => 'number',
				'context'     =>  array( 'view', 'edit' ),
				'readonly'    =>  false,
			),
			'form_title'    =>  array(
				'description' => 'The Gravity Forms form title used to create a new form.',
				'type'        => 'string',
				'context'     =>  array( 'view', 'edit' ),
				'readonly'    =>  false,
			),
			'feeds'      => array(
				'description' => 'An array of feed IDs to process.',
				'type'        => 'array',
				'items'       =>  array( 'integer', 'string' ),
				'context'     =>  array( 'view', 'edit' ),
				'readonly'    =>  false,
			),
			'conditions' => array(
				'description' => 'Filter conditions, which rows to process.',
				'type'        =>  array( '$ref' => '#/definitions/condition' ),
				'context'     =>  array( 'view', 'edit' ),
				'readonly'    =>  false,
			),
			'source'     => array(
				'description' => 'A URI to the CSV data source.',
				'type'        => 'string',
				'context'     =>  array( 'view', 'edit' ),
				'readonly'    =>  false,
			),
			'flags'      => array(
				'description' => 'Special processing flags.',
				'type'        => 'array',
				'anyOf'       =>  array(
					"flush",                       // Truncate all existing entries
					"patch",                       // Patch existing entries, keeping undefined values untouched, needs entry ID map rule
					"auto",                        // Autoprocess this batch, all configuration has been supplied upfront
					"hookless",                    // Suppress all before, after hooks, feeds, insert as is
					"nolog",                       // Do not generate binary log, makes rollbacks impossible, faster, saves database space
					"lock",                        // Lock tables for read, write during processing
					"soft",                        // Allow errors in rows through, process to the end
					"require",                     // Do not ignore required fields, consider errored if a field is missing
					"valid",                       // All data is unconditionally valid (unless overridden by filters), but sanitized
					"notify",                      // Run notifications for all imported entries
					"ignorefieldconditionallogic", // Ignore conditional logic for fields
					"keepsource",                  // Do not remove the local source after import when deleting this batch
					"remove"                       // Remove batch once it has finished with no errors and skips
				),
				'context'     =>  array( 'view', 'edit' ),
				'readonly'    =>  false,
			),
			'meta'       => array(
				'description' => 'Batch metadata, like parsed CSV headers, etc. A source of preliminary information about the batch.',
				'type'        => '#/definitions/meta',
				'context'     =>  array( 'view', 'edit' ),
				'readonly'    =>  true,
			)
		),

		'definitions'    => array(
			'rule'       => array(
				'type'       => 'object',
				'properties' =>  array(
					'column' =>  array(
						'description' => 'The column number in the import row.',
						'type'        => 'number',
						'context'     =>  array( 'view', 'edit' ),
						'readonly'    =>  false,
					),
					'name'   =>  array(
						'description' => 'The column name.',
						'type'        => 'string',
						'context'     =>  array( 'view', 'edit' ),
						'readonly'    =>  false,
					),
					'field'  =>  array(
						'description' => 'The Gravity Forms field ID to map into, or type and input (for multiinput) if new form is created.',
						'type'        =>  array( 'number', 'string' ),
						'context'     =>  array( 'view', 'edit' ),
						'readonly'    =>  false,
					),
					'flags'  =>  array(
						'description' => 'Special processing rules and exceptions, overrides.',
						'type'        => 'array',
						'anyOf'       =>  array(
							'valid',     // All values are valid for this column, unquestionable import
							'default',   // For empty column use default field value (if set)
							'keeplinks', // Keep file upload URLs as is for this field
						),
						'context'     =>  array( 'view', 'edit' ),
						'readonly'    =>  false,
					),
					'meta'   =>  array(
						'description' => 'Miscellaneous settings that are not shared across field types.',
						'type'        => 'object',
						'properties'  =>  array(
							'parent_name'     =>  array(
								'description' => 'The column name for the parent field. Used for multi-input (address, name, credit card, etc.) fields.',
								'type'        => 'string',
								'context'     =>  array( 'view', 'edit' ),
								'readonly'    =>  false,
							),
							'is_meta'         =>  array(
								'description' => 'This is a meta key mapping for meta fields.',
								'type'        => 'text',
								'context'     =>  array( 'view', 'edit' ),
								'readonly'    =>  false,
							),
							'datetime_format' =>  array(
								'description' => 'The incoming date format for this date or time field (or datetime if it ever arrives).',
								'type'        => 'string',
								'context'     =>  array( 'view', 'edit' ),
								'readonly'    =>  false,
							),
							'date_timezone'   =>  array(
								'description' => 'Date timezone.',
								'type'        => 'string',
								'context'     =>  array( 'view', 'edit' ),
								'readonly'    =>  false,
							),
							'list_rows'       =>  array(
								'description' => 'For lists columns can be mapped to row items. This is used to designate row columns and their order.',
								'context'     =>  array( 'view', 'edit' ),
								'readonly'    =>  false,
							),
							'list_cols'       =>  array(
								'description' => 'This is is used to designate row labels for lists.',
								'context'     =>  array( 'view', 'edit' ),
								'readonly'    =>  false,
							),
							'type'            =>  array(
								'description' => 'The input subtype for such fields as poll, etc.',
								'context'     =>  array( 'view', 'edit' ),
								'readonly'    =>  false,
							),
							'value'           =>  array(
								'description' => 'The value for this field, if supported.',
								'context'     =>  array( 'view', 'edit' ),
								'readonly'    =>  false,
							),
							'choices'         =>  array(
								'description' => 'The choices for this field, includes quiz definitions.',
								'context'     =>  array( 'view', 'edit' ),
								'readonly'    =>  false,
							),
							'correct'         =>  array(
								'description' => 'This choice is correct. Used for quiz checkboxes.',
								'context'     =>  array( 'view', 'edit' ),
								'readonly'    =>  false,
							),
							'weight'          =>  array(
								'description' => 'This choice quiz answer weight. Used for quiz checkboxes.',
								'context'     =>  array( 'view', 'edit' ),
								'readonly'    =>  false,
							),
							'multiple_files_upload' =>  array(
								'description' => 'Used for the "file upload" field to indicate that it can accept multiple files.',
								'context'     =>  array( 'view', 'edit' ),
								'readonly'    =>  false,
							),
						),
						'context'     =>  array( 'view', 'edit' ),
						'readonly'    =>  false,
					),
				),
			),

			/**
			 * Examples:
			 *
			 * A condition where column 1 is not equal to 'hello':
			 *     {"column": 1, "op": "like", "value": "hello%"}
			 *
			 * Nested conditions:
			 *     {"op": "and", "value": [
			 *         {"column": 1, "op": "neq", "value": "hello"},
			 *         {"column": 1, "op": "neq", "value": "world"}
			 *     ]}
			 */
			'condition'  => array(
				'type'       => 'object',
				'properties' =>  array(
					'column' =>  array(
						'description' => 'The column number. Not needed if op is a boolean operation.',
						'type'        => 'integer',
						'context'     =>  array( 'view', 'edit' ),
						'readonly'    =>  false,
					),
					'op'     =>  array(
						'description' => 'The comparison operation.',
						'type'        => 'string',
						'enum'        =>  array( 'eq', 'neq', 'like', 'nlike', 'lt', 'gt', 'and', 'or' ),
						'context'     =>  array( 'view', 'edit' ),
						'extra'       =>  array(
							'boolean' =>  array( 'and', 'or' ),
						),
						'readonly'    =>  false,
					),
					'value'  =>  array(
						'description' => 'The value. An array if op is a boolean operation.',
						'type'        =>  array( 'string', 'array' ),
						'context'     =>  array( 'view', 'edit' ),
						'readonly'    =>  false,
					),
				),
			),

			'meta'       => array(
				'type'        => 'object',
				'properties'  =>  array(
					'rows'    =>  array(
						'description' => 'The number of rows in the source. Available as we are parsing.',
						'type'        => 'integer',
						'context'     =>  array( 'view', 'edit' ),
						'readonly'    =>  true,
					),
					'columns' =>  array(
						'description' => 'An array of column objects describing what we were able to parse.',
						'type'        => 'array',
						'items'       =>  array( '$ref' => '#/definitions/columnmeta' ),
						'context'     =>  array( 'view', 'edit' ),
						'readonly'    =>  true,
					),
					'excerpt' => array(
						'description' => 'An excerpt of rows from the CSV.',
						'type'        => 'array',
						'items'       =>  array( '$ref' => '#/definitions/excerpt' ),
						'context'     =>  array( 'view', 'edit' ),
						'readonly'    =>  true,
					),
				),
			),

			'columnmeta' => array(
				'type'        => 'object',
				'properties'  =>  array(
					'title'   =>  array(
						'description' => 'The column title.',
						'type'        => 'text',
						'context'     =>  array( 'view', 'edit' ),
						'readonly'    =>  true,
					),
					'field'   =>  array(
						'description' => 'The guessed field ID if form given. Or guessed type if not given.',
						'type'        =>  array( 'integer', 'string' ),
						'context'     =>  array( 'view', 'edit' ),
						'readonly'    =>  true,
					),
				),
			),

			'excerpt'   => array(
				'type'        => 'array',
				'items'       =>  array( 'string' ),
			),

			'progress'  => array(
				'type'        => 'object',
				'properties'  =>  array(
					'total'             =>  array(
						'description' => 'Total amount of rows in the CSV.',
						'type'        => 'integer',
						'context'     =>  array( 'view' ),
						'readonly'    =>  true,
					),
					'new'             =>  array(
						'description' => 'Rows that have not been processed yet.',
						'type'        => 'integer',
						'context'     =>  array( 'view' ),
						'readonly'    =>  true,
					),
					'processing'      =>  array(
						'description' => 'Rows that are being processed now.',
						'type'        => 'integer',
						'context'     =>  array( 'view' ),
						'readonly'    =>  true,
					),
					'processed'       =>  array(
						'description' => 'Rows that have been processed.',
						'type'        => 'integer',
						'context'     =>  array( 'view' ),
						'readonly'    =>  true,
					),
					'skipped'         =>  array(
						'description' => 'Rows that have been skipped.',
						'type'        => 'integer',
						'context'     =>  array( 'view' ),
						'readonly'    =>  true,
					),
					'error'           =>  array(
						'description' => 'Rows that have been errored.',
						'type'        => 'integer',
						'context'     =>  array( 'view' ),
						'readonly'    =>  true,
					),
				),
			),
		),
	);

	return $schema;
}
