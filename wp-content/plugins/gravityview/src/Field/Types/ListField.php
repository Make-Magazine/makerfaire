<?php
/**
 * List field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_List class.
 * Named ListField instead of List because "list" is a reserved keyword in PHP.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

use GF_Field_List;
use GFAPI;
use RGFormsModel;

use function gravityview;
use function gravityview_get_input_id_from_id;

/**
 * Add custom options for list fields
 *
 * @since 1.14
 * @since 3.0.0 Migrated to PSR-4.
 */
class ListField extends \GravityView_Field {
	var $name = 'list';

	/**
	 * @var bool
	 * @since 1.15.3
	 */
	var $is_searchable = true;

	var $search_operators = ['contains'];

	/**
	 * @var bool
	 * @since 1.15.3
	 */
	var $is_sortable = false;

	/** @see GF_Field_List */
	var $_gf_field_class_name = 'GF_Field_List';

	var $group = 'advanced';

	var $icon = 'dashicons-list-view';

	function __construct() {

		$this->label = esc_html__( 'List', 'gk-gravityview' );

		parent::__construct();

		add_filter( 'gravityview/template/field/label', [ $this, 'filter_field_label' ], 10, 2 );

		add_filter( 'gravityview/common/get_form_fields', [ $this, 'add_form_fields' ], 10, 3 );

		add_filter( 'gravityview/search/searchable_fields', [ $this, 'remove_columns_from_searchable_fields' ], 10 );

		add_filter( 'gravityview/merge_tags/modifiers/value', [ $this, 'handle_list_column_modifier' ], 10, 6 );
	}

	/**
	 * Removes columns from searchable field dropdown
	 *
	 * Columns are able to be added to View layouts, but not separately searched!
	 *
	 * @param array $fields
	 * @param int   $form_id
	 *
	 * @return array
	 */
	public function remove_columns_from_searchable_fields( $fields ) {

		foreach ( $fields as $key => $field ) {
			if ( isset( $field['parent'] ) && $field['parent'] instanceof GF_Field_List ) {
				unset( $fields[ $key ] );
			}
		}

		return $fields;
	}

	/**
	 * If a form has list fields, add the columns to the field picker
	 *
	 * @since 1.17
	 *
	 * @param array $fields Associative array of fields, with keys as field type
	 * @param array $form GF Form array
	 * @param bool  $include_parent_field Whether to include the parent field when getting a field with inputs
	 *
	 * @return array $fields with list field columns added, if exist. Unmodified if form has no list fields.
	 */
	function add_form_fields( $fields = [], $form = [], $include_parent_field = true ) {

		$list_fields = GFAPI::get_fields_by_type( $form, 'list' );

		// Add the list columns
		foreach ( $list_fields as $list_field ) {

			if ( empty( $list_field->enableColumns ) ) {
				continue;
			}

			$list_columns = [];

			foreach ( (array) $list_field->choices as $key => $input ) {

				$input_id = sprintf( '%d.%d', $list_field->id, $key ); // {field_id}.{column_key}

				$list_columns[ $input_id ] = [
					'label'       => \GV\Utils::get( $input, 'text' ),
					'customLabel' => '',
					'parent'      => $list_field,
					'type'        => \GV\Utils::get( $list_field, 'type' ),
					'adminLabel'  => \GV\Utils::get( $list_field, 'adminLabel' ),
					'adminOnly'   => \GV\Utils::get( $list_field, 'adminOnly' ),
				];
			}

			// If there are columns, add them under the parent field
			if ( ! empty( $list_columns ) ) {

				$index = array_search( $list_field->id, array_keys( $fields ) ) + 1;

				/**
				 * Merge the $list_columns into the $fields array at $index
				 *
				 * @see https://stackoverflow.com/a/1783125
				 */
				$fields = array_slice( $fields, 0, $index, true ) + $list_columns + array_slice( $fields, $index, null, true );
			}

			unset( $list_columns, $index, $input_id );
		}

		return $fields;
	}

	/**
	 * Get the value of a Multiple Column List field for a specific column.
	 *
	 * @since 1.14
	 *
	 * @see GF_Field_List::get_value_entry_detail()
	 *
	 * @param GF_Field_List $field Gravity Forms field
	 * @param string|array  $field_value Serialized or unserialized array value for the field
	 * @param int|string    $column_id The numeric key of the column (0-index) or the label of the column
	 * @param string        $format If set to 'raw', return an array of values for the column. Otherwise, allow Gravity Forms to render using `html` or `text`
	 *
	 * @return array|string|null Returns null if the $field_value passed wasn't an array or serialized array
	 */
	public static function column_value( GF_Field_List $field, $field_value, $column_id = 0, $format = 'html' ) {

		$list_rows = maybe_unserialize( $field_value );

		if ( ! is_array( $list_rows ) ) {
			gravityview()->log->error( '$field_value did not unserialize', [ 'data' => $field_value ] );
			return null;
		}

		$column_values = [];

		// Each list row
		foreach ( $list_rows as $list_row ) {
			$current_column = 0;
			foreach ( (array) $list_row as $column_key => $column_value ) {

				// If the label of the column matches $column_id, or the numeric key value matches, add the value
				if ( (string) $column_key === (string) $column_id || ( is_numeric( $column_id ) && (int) $column_id === $current_column ) ) {
					$column_values[] = $column_value;
				}
				++$current_column;
			}
		}

		// Return the array of values
		if ( 'raw' === $format ) {
			return $column_values;
		}
		// Return the Gravity Forms Field output
		else {
			return $field->get_value_entry_detail( serialize( $column_values ), [], false, $format );
		}
	}

	/**
	 * When showing single column values, display the label of the column instead of the field.
	 *
	 * @since 3.1.0
	 *
	 * @param string               $label   Existing label string.
	 * @param \GV\Template_Context $context The template context.
	 *
	 * @return string The filtered label.
	 */
	public function filter_field_label( $label, $context ) {
		$form  = $context->view && $context->view->form ? $context->view->form->form : [];
		$entry = $context->entry ? $context->entry->as_entry() : [];

		return $this->_filter_field_label( $label, $context->field->as_configuration(), $form, $entry );
	}

	/**
	 * Uses the field column label for List field columns.
	 *
	 * @since 1.14
	 *
	 * @param string $label Existing label string.
	 * @param array  $field GV field settings array.
	 * @param array  $form  Gravity Forms form array.
	 * @param array  $entry Gravity Forms entry array.
	 *
	 * @return string The filtered label.
	 */
	public function _filter_field_label( $label, $field, $form, $entry ) {

		$field_object = RGFormsModel::get_field( $form, $field['id'] );

		// Not a list field
		if ( ! $field_object || 'list' !== $field_object->type ) {
			return $label;
		}

		// Custom label is defined, so use it
		if ( ! empty( $field['custom_label'] ) ) {
			return $label;
		}

		$column_id = gravityview_get_input_id_from_id( $field['id'] );

		// Parent field, not column field
		if ( false === $column_id ) {
			return $label;
		}

		return self::get_column_label( $field_object, $column_id, $label );
	}

	/**
	 * Get the column label for the list
	 *
	 * @since 1.14
	 *
	 * @param GF_Field_List $field Gravity Forms List field
	 * @param int           $column_id The key of the column (0-index)
	 * @param string        $backup_label Backup label to use. Optional.
	 *
	 * @return string
	 */
	public static function get_column_label( GF_Field_List $field, $column_id, $backup_label = '' ) {

		// Doesn't have columns enabled
		if ( ! isset( $field->choices ) || ! $field->enableColumns ) {
			return $backup_label;
		}

		// Get the list of columns, with numeric index keys
		$columns = wp_list_pluck( $field->choices, 'text' );

		return isset( $columns[ $column_id ] ) ? $columns[ $column_id ] : $backup_label;
	}

	/**
	 * Handles List field column modifiers (e.g., `{List:10:2}` where "2" is the column number).
	 *
	 * @since 2.46.0
	 *
	 * @param string    $return    The current merge tag value to be filtered.
	 * @param string    $raw_value The raw value submitted for this field. May be CSV or JSON-encoded.
	 * @param string    $value     The original merge tag value, passed from Gravity Forms
	 * @param string    $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field's ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param string    $modifier  The string containing any modifiers for this merge tag. For example, "maxwords:10" would be the modifiers for the following merge tag: `{Text:2:maxwords:10}`.
	 * @param \GF_Field $field     The current field.
	 *
	 * @return string
	 */
	public function handle_list_column_modifier( $return, $raw_value, $value, $merge_tag, $modifier, $field ) {
		// Only process for List fields with columns enabled.
		if ( ! $field instanceof GF_Field_List || ! $field->enableColumns ) {
			return $return;
		}

		[ $column, $format ] = array_pad( explode( ':', $modifier, 2 ), 2, null );

		if ( ! is_numeric( $column ) ) {
			return $return;
		}

		$column_id    = (int) $modifier;
		$column_value = self::column_value( $field, $raw_value, $column_id, $format ?: 'html' );

		if ( null !== $column_value ) {
			return $column_value;
		}

		return $return;
	}
}
