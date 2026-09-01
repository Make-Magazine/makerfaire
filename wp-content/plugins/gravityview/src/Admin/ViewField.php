<?php
/**
 * A field in GravityView view configuration.
 *
 * PSR-4 migration of the legacy GravityView_Admin_View_Field class.
 *
 * @package GravityKit\GravityView\Admin
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Admin;

/**
 * A field in GravityView view configuration
 */
class ViewField extends \GravityView_Admin_View_Item {

	protected $label_type = 'field';

	protected function get_title( string $label ): string {
		// translators: %s: the field label.
		return sprintf( __( 'Field: %s', 'gk-gravityview' ), $label );
	}

	protected function additional_info() {

		$field_info_items = [];

		if ( ! empty( $this->item['adminLabel'] ) ) {
			$field_info_items[] = [
				// translators: %s: the field admin label.
				'value' => sprintf( __( 'Admin Label: %s', 'gk-gravityview' ), $this->item['adminLabel'] ),
				'class' => 'gv-sublabel',
			];
		}

		// Fields with IDs, not like Source URL or Entry ID
		if ( is_numeric( $this->id ) || preg_match( '/^\d+(\.\d+)*$/', $this->id ) ) {
			$field_type_title = \GFCommon::get_field_type_title( $this->item['input_type'] );

			if ( ! empty( $this->item['parent'] ) ) {
				$field_info_items[] = [
					// translators: %s: the parent field label.
					'value' => sprintf( esc_html__( 'Parent: %s', 'gk-gravityview' ), esc_attr( $this->item['parent']['label'] ) ),
				];
			}

			$field_info_items[] = [
				// translators: %s: the field type title.
				'value'          => sprintf( __( 'Type: %s', 'gk-gravityview' ), $field_type_title ),
				'hide_in_picker' => ! empty( $this->item['parent'] ),
			];

			$field_info_items[] = [
				// translators: %s: the field ID number.
				'value' => sprintf( __( 'Field ID: %s', 'gk-gravityview' ), $this->id ),
			];
		}

		if ( ! empty( $this->item['desc'] ) ) {
			$field_info_items[] = [
				'value' => $this->item['desc'],
			];
		}

		$field_info_items[] = [
			// translators: %s: the form ID number.
			'value'          => sprintf( __( 'Form ID: %s', 'gk-gravityview' ), $this->form_id ),
			'hide_in_picker' => true,
		];

		return $field_info_items;
	}

	/**
	 * @inheritDoc
	 *
	 * @since 2.51.0
	 */
	protected function is_parent(): bool {
		if ( ! is_numeric( $this->id ) || strpos( $this->id, '.' ) !== false ) {
			return parent::is_parent();
		}

		$field = \GFFormsModel::get_field( $this->form_id, $this->id );

		return $field && ( $field->get_entry_inputs() || ( $field->fields ?? null ) );
	}

	/**
	 * @inheritDoc
	 *
	 * @since 2.51.0
	 */
	protected function get_nesting_level(): int {
		if ( ! is_numeric( $this->id ) ) {
			return parent::get_nesting_level();
		}

		$field = \GFFormsModel::get_field( $this->form_id, $this->id );
		if ( ! $field ) {
			return parent::get_nesting_level();
		}

		$parents = \GravityView_Field_Repeater::get_repeater_field_ids( $this->form_id );
		$level   = count( $parents[ $this->id ] ?? [] );

		return $level ? $level : parent::get_nesting_level();
	}
}
