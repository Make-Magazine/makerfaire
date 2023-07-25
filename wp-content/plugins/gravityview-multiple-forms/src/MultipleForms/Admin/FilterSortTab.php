<?php

namespace GravityKit\MultipleForms\Admin;

use GravityKit\MultipleForms\AbstractSingleton;
use GravityKit\MultipleForms\Query\Sort;
use GravityKit\MultipleForms\View;
use GFAPI;
use GVCommon;

/**
 * Class FilterSortTab
 *
 * @since 0.3
 *
 */
class FilterSortTab extends AbstractSingleton {

	/**
	 * Registering happens after the singleton instance has been set up, which is after the extension was confirmed to have
	 * its requirements met and after `plugins_loaded@P20`
	 *
	 * @since 0.3
	 *
	 * @return void
	 */
	protected function register(): void {
		$this->hook_sortable_fields();
	}

	/**
	 * Hooks the sortable fields modifications.
	 *
	 * @since 0.3
	 *
	 * @return void
	 */
	public function hook_sortable_fields(): void {
		add_filter( 'gravityview/common/sortable_fields', [ $this, 'filter_sortable_fields' ], 20, 2 );
	}

	/**
	 * Unhooks the sortable fields modifications.
	 *
	 * @since 0.3
	 *
	 * @return void
	 */
	public function unhook_sortable_fields(): void {
		remove_filter( 'gravityview/common/sortable_fields', [ $this, 'filter_sortable_fields' ], 20 );
	}

	/**
	 * Modifies the sortable fields
	 *
	 * @since 0.3
	 *
	 * @param array      $fields
	 * @param int|string $form_id
	 *
	 * @return array
	 */
	public function filter_sortable_fields( array $fields, $form_id ): array {
		$this->unhook_sortable_fields();

		global $post;
		$joins_form_ids = View::get_join_form_ids( $post );

		foreach ( $joins_form_ids as $join_form_id ) {
			if ( (int) $form_id === (int) $join_form_id ) {
				continue;
			}

			$form = GFAPI::get_form( $join_form_id );

			if ( ! $form ) {
				do_action( 'gravityview_log_error', "[Multiple Forms][Form Join] {$join_form_id} joined {$form_id} but it is an invalid Form ID, so we cannot include it's sortable fields.", __METHOD__ );
				continue;
			}

			$sortable_fields = GVCommon::get_sortable_fields_array( $join_form_id );

			foreach ( $sortable_fields as $field_key => $field ) {
				$field['label'] = sprintf( __( "%s (ID: %d) - %s", 'gravityview-multiple-forms' ), $form['title'], $join_form_id, $field['label'] );
				$new_key = Sort::instance()->format_join_field( $field_key, $join_form_id );

				// Include the field into the sorting fields on this page.
				$fields[ $new_key ] = $field;
			}
		}

		$this->hook_sortable_fields();

		return $fields;
	}
}
