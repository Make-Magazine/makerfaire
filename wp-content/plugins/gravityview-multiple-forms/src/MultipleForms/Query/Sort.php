<?php

namespace GravityKit\MultipleForms\Query;

use GravityKit\MultipleForms\AbstractSingleton;
use GF_Query_Column;

/**
 * Class Sort
 *
 * @since 0.3
 *
 * @package GravityKit\MultipleForms\Query
 */
class Sort extends AbstractSingleton {
	/**
	 * Registering happens after the singleton instance has been set up, which is after the extension was confirmed to have
	 * its requirements met and after `plugins_loaded@P20`
	 *
	 * @since 0.3
	 *
	 * @return void
	 */
	protected function register(): void {

	}

	/**
	 * Configures a GF_Query_Column for sorting based on the field passed in.
	 * Will attempt to be compatible with Joins.
	 *
	 * @see static::get_sort_data_from_field
	 *
	 * @since 0.3
	 *
	 * @param int|string $sort_field
	 * @param int|string $base_form_id
	 *
	 * @return GF_Query_Column
	 */
	public function configure_order_column( $sort_field, $base_form_id ) {
		$order_data = $this->get_sort_data_from_field( $sort_field );

		// At this point we are assuming it's a normal field sorting.
		if ( null === $order_data ) {
			return new GF_Query_Column( $sort_field, (int) $base_form_id );
		}

		[ $form_id, $field_id ] = $order_data;

		$column = new GF_Query_Column( $field_id, (int) $form_id );

		return $column;
	}

	/**
	 * Formats a Form ID and Field Key for allows sorting later.
	 *
	 * @example form:23|2.3
	 * @example form:4|date_updated
	 * @example form:23|1
	 *
	 * @since 0.3
	 *
	 * @param string $field_key
	 * @param string $form_id
	 *
	 * @return string
	 */
	public function format_join_field( string $field_key, string $form_id ): string {
		return "form:{$form_id}|{$field_key}";
	}

	/**
	 * Given a formatted string will fetch the Form ID and Field ID for sorting.
	 *
	 * @example form:23|2.3
	 * @example form:4|date_updated
	 * @example form:23|1
	 *
	 * @since 0.3
	 *
	 * @param string $join_key
	 *
	 * @return array|null
	 */
	public function get_sort_data_from_field( string $join_key ) {
		$parts = explode( '|', $join_key );
		if ( count( $parts ) !== 2 ) {
			return null;
		}

		$form_id = (int) str_replace( 'form:', '', $parts[0] );
		$field_id = $parts[1];
		return [ $form_id, $field_id ];
	}
}
