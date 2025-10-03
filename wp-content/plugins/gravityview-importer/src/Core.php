<?php

namespace GravityKit\GravityImport;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Core {
	/**
	 * @var string The plugin version.
	 */
	const version = GV_IMPORT_ENTRIES_VERSION;

	/**
	 * @var int The database version.
	 */
	const db_version = '2.2';

	/**
	 * @var string The REST namespace.
	 */
	const rest_namespace = 'gravityview/import/v1';

	/**
	 * Early initialization, called on `plugins_loaded` automatically.
	 *
	 * Tries to upgrade the database if needed, runs migrations.
	 * Adds some hooks, loads models, etc.
	 *
	 * @codeCoverageIgnore Tested during load.
	 *
	 * @return void
	 */
	public static function bootstrap() {
		self::maybe_upgrade();

		GF_System_Status_Screen::register( '\GravityKit\GravityImport\GF_System_Status_Screen' );

		add_action( 'init', array( '\GravityKit\GravityImport\Batch', 'register_post_type' ) );
		add_action( 'rest_api_init', array( __CLASS__, 'rest_api_init' ) );

		// Enable compatability with third-party plugins
		new Compat();

		// Add options to import entries to GF Entries and WP Import screens
		new GF_Entries_Screen();
		new WP_Import_Screen();
		Addon::get_instance();

		// Initialize UI
		new UI( self::version );
	}

	/**
	 * Maybe upgrade the database.
	 *
	 * @codeCoverageIgnore Tested during load.
	 *
	 * @uses \dbDelta
	 *
	 * @return void
	 */
	public static function maybe_upgrade() {
		$db_version = get_option( 'gv_import_entries_dbv', 0 );

		if ( version_compare( $db_version, self::db_version, '>=' ) ) {
			return;
		}

		self::db_migrate( $db_version );

		$schema = gv_import_entries_get_db_schema();

		/**
		 * Prepare the schema, see https://core.trac.wordpress.org/ticket/44767
		 */
		$schema = preg_replace( '#`\s+#', '` ', $schema );
		$schema = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $schema );

		if ( ! function_exists( '\dbDelta' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		}

		\dbDelta( $schema );

		update_option( 'gv_import_entries_dbv', self::db_version, false );
	}

	/**
	 * Drop all our tables.
	 *
	 * @codeCoverageIgnore Tested during load.
	 *
	 * @return void
	 */
	public static function drop_tables() {
		global $wpdb;

		$tables = gv_import_entries_get_db_tables();

		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS $table" );
		}
	}

	/**
	 * Migrate the database data.
	 *
	 * Runs before the table structures are altered. Migrates
	 * the data to match the newer schema.
	 *
	 * @codeCoverageIgnore Tested during load.
	 *
	 * @param int $version The version to migrate from.
	 *
	 * @return void
	 */
	public static function db_migrate( $version ) {
	}

	/**
	 * Initialize the REST API, this is a REST request.
	 *
	 * @return void
	 */
	public static function rest_api_init() {
		$controller = new REST_Batch_Controller;
		$controller->register_routes();

		// The default authenticated batch processing call.
		register_rest_route( Core::rest_namespace, "/process", array(
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( '\GravityKit\GravityImport\Processor', 'rest_process' ),
				'permission_callback' => array( $controller, 'can_update_batch' ),
				'args'                => array(
					'batch_id'        => array(
						'description' => 'The Batch ID to process. If not given, all batches are processed.',
						'type'        => 'number'
					)
				),
			),
		) );
	}

	/**
	 * Whether a column is an entry column (vs. a meta)
	 *
	 * @param string $column The column.
	 *
	 * @return bool Is column or not.
	 */
	public static function is_entry_column( $column ) {
		if ( is_numeric( $column ) ) {
			return false;
		}

		static $entry_columns = array();

		if ( empty( $entry_columns ) ) {
			global $wpdb;
			$entry_columns = wp_list_pluck( $wpdb->get_results( 'SHOW COLUMNS FROM ' . \GFFormsModel::get_entry_table_name(), ARRAY_A ), 'Field' );
		}

		return in_array( $column, $entry_columns );
	}

	/**
	 * Retrieve a list of all known meta fields.
	 *
	 * This method will scan the database and the known
	 * active plugins to return a list of meta fields along
	 * with their nice names (if we know them).
	 *
	 * @return array A key-value array with meta name as key and
	 *               label as value.
	 */
	public static function get_meta_fields() {
		global $wpdb;

		$fields = array();

		/**
		 * Find existing database meta keys.
		 */
		if ( version_compare( \GFFormsModel::get_database_version(), '2.3-dev-1', '>=' ) ) {
			$meta_table  = \GFFormsModel::get_entry_meta_table_name();
		} else {
			$meta_table  = \GFFormsModel::get_lead_meta_table_name();
		}

		$blacklist = array(
		);

		foreach ( $wpdb->get_col( "SELECT DISTINCT meta_key FROM $meta_table" ) as $field ) {
			if ( is_numeric( $field ) ) {
				continue;
			}

			if ( in_array( $field, $blacklist ) ) {
				continue;
			}

			$fields[ $field ] = $field;
		}

		$known_objects = array(
			'\GravityView_Maps_Loader' => array(
				'lat'  => __( 'Latitude', 'gk-gravityimport' ),
				'long' => __( 'Longitude', 'gk-gravityimport' ),
			),
			'\GVCommon' => array(
				'is_approved' => __( 'Approval Status', 'gk-gravityimport' ),
			),
		);

		/**
		 * Find known objects.
		 */
		foreach ( $known_objects as $object => $_fields ) {
			$existing_key = ! empty( array_intersect( array_keys( $fields ), array_keys( $_fields ) ) );
			if ( $existing_key || class_exists( $object ) || function_exists( $object ) ) {
				$fields = array_merge( $fields, $_fields );
			}
		}

		/**
		 * @filter `gravityview/import/meta_fields` Filter the known meta fields
		 * @param array Key-value array of known meta fields
		 */
		return apply_filters( 'gravityview/import/meta_fields', $fields );
	}

	/**
	 * Lowercase a string.
	 *
	 * Tries to mb_strtolower before falling back to strtolower.
	 * UTF-8 only.
	 *
	 * @param string $data
	 *
	 * @return string
	 */
	public static function strtolower( $data ) {
		if ( function_exists( 'mb_strtolower' ) ) {
			return mb_strtolower( $data, 'UTF-8' );
		}

		return strtolower( $data );
	}
}
