<?php

namespace GravityKit\GravityImport;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * The rollback log.
 *
 * A stack-based log. See schema for data layout.
 */
class Log {
	/**
	 * Log an operation on an entry.
	 *
	 * @param int    $batch_id  The import batch ID.
	 * @param int    $row_id    The import row ID, if associated.
	 * @param string $operatoin The operation 'insert' or 'delete'.
	 * @param int    $entry_id  The Gravity Forms entry ID.
	 *
	 * @return bool Saving has been completed.
	 */
	public static function save( $batch_id, $row_id, $operation, $entry_id ) {
		global $wpdb;

		$tables = gv_import_entries_get_db_tables();

		$data = $wpdb->get_row( $wpdb->prepare( sprintf( "SELECT * FROM %s WHERE id = %%d", $entry_table = \GFFormsModel::get_entry_table_name() ), $entry_id ), ARRAY_A );
		$meta = $wpdb->get_results( $wpdb->prepare( sprintf( "SELECT * FROM %s WHERE entry_id = %%d", $entry_meta_table = \GFFormsModel::get_entry_meta_table_name() ), $entry_id ), ARRAY_A );

		$data = array_merge( array(
			$entry_table => $data ? : array(),
		), $meta ? array(
			$entry_meta_table => $meta,
		) : array() );

		if ( ! $wpdb->insert( $tables['log'], array(
			'batch_id'  => $batch_id,
			'row_id'    => $row_id,
			'op'        => $operation,
			'timestamp' => microtime( true ),
			'data'      => json_encode( $data ),
		) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Rollback an operation on an entry.
	 *
	 * No hooks are called. This is a raw SQL insert.
	 *
	 * It is important to save the initial state of the entry before rolling back.
	 * If there are no more log entries for an associated import row, a rollback will
	 * delete the entry altogether. Careful.
	 *
	 * @param int  $row_id       The row ID to restore.
	 *
	 * @return bool Rollback has been completed.
	 */
	public static function rollback( $row_id ) {
		global $wpdb;

		$tables = gv_import_entries_get_db_tables();

		if ( ! $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tables['log']} WHERE row_id = %d ORDER BY timestamp DESC", $row_id ) ) ) {
			return false;
		}

		$wpdb->query( 'START TRANSACTION;' );

		if ( self::_rollback_row( $row ) ) {
			$wpdb->query( 'COMMIT;' );
			return true;
		}

		$wpdb->query( 'ROLLBACK;' );
		return false;
	}

	/**
	 * Rollback the whole batch.
	 *
	 * @param int $batch_id The batch ID.
	 *
	 * @return bool Whether it succeeded or not.
	 */
	public static function rollback_batch( $batch_id ) {
		global $wpdb;

		$tables = gv_import_entries_get_db_tables();

		if ( ! $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$tables['log']} WHERE batch_id = %d ORDER BY timestamp DESC", $batch_id ) ) ) {
			return false;
		}

		$wpdb->query( 'START TRANSACTION;' );

		foreach ( $rows as $row ) {
			if ( ! self::_rollback_row( $row ) ) {
				$wpdb->query( 'ROLLBACK' );
				return false;
			}
		}

		$wpdb->query( 'COMMIT;' );

		wp_cache_flush();

		return true;
	}

	/**
	 * Rollback a row entry.
	 *
	 * Is not transactioned.
	 *
	 * @internal
	 *
	 * @param object $row The row as returned from the database.
	 *
	 * @return
	 */
	private static function _rollback_row( $row ) {
		global $wpdb;

		$tables = gv_import_entries_get_db_tables();

		$data = json_decode( $row->data, true );

		$entry_table = \GFFormsModel::get_entry_table_name();
		$entry_meta_table = \GFFormsModel::get_entry_meta_table_name();

		if ( $row->op == 'delete' ) {
			if ( ! $wpdb->insert( $entry_table, $data[ $entry_table ] ) ) {
				return false;
			}

			foreach ( $data[ $entry_meta_table ] as $entry_meta ) {
				if ( ! $wpdb->insert( $entry_meta_table, $entry_meta ) ) {
					return false;
				}
			}

			if ( ! $wpdb->delete( $tables['log'], array( 'id' => $row->id ) ) ) {
				return false;
			}
		} else if ( $row->op == 'insert' ) {
			if ( $wpdb->delete( $entry_meta_table, array( 'entry_id' => $data[ $entry_table ]['id'] ) ) === false ) {
				return false;
			}

			if ( ! $wpdb->delete( $entry_table, array( 'id' => $data[ $entry_table ]['id'] ) ) ) {
				return false;
			}

			if ( ! $wpdb->delete( $tables['log'], array( 'id' => $row->id ) ) ) {
				return false;
			}

			if ( $row->row_id && $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tables['log']} WHERE row_id = %d ORDER BY timestamp DESC", $row->row_id ) ) ) {
				$data = json_decode( $row->data, true );

				if ( ! $wpdb->insert( $entry_table, $data[ $entry_table ] ) ) {
					return false;
				}

				foreach ( $data[ $entry_meta_table ] as $entry_meta ) {
					if ( ! $wpdb->insert( $entry_meta_table, $entry_meta ) ) {
						return false;
					}
				}
			}
		}

		return true;
	}
}
