<?php
/**
 * Generates default field configurations for View templates based on the connected form.
 *
 * @package GravityKit\GravityView\Admin
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Admin;

use GFFormsModel;
use GravityKit\GravityView\Preset\LayoutBuilder;

/**
 * Generates the default field configuration for a new View.
 *
 * When a user selects a template on a new View with a form already chosen,
 * this class generates sensible default fields matching GF's entry list
 * column selection and maps them into the template's zone structure.
 *
 * Generation is allowed to guess semantics (e.g. "the second form field is a
 * subtitle") because it only ever runs against a form, where there is no user
 * arrangement to violate. Migration of an existing configuration between
 * templates must NOT guess — that is why {@see TemplateFieldTransfer} maps
 * conservatively while this class maps semantically. Keep that asymmetry.
 *
 * @since 3.0.0
 */
final class PresetFieldGenerator {

	/**
	 * Entry-meta field IDs that should not be treated as form content.
	 *
	 * @since 3.0.0
	 */
	private const ENTRY_META_IDS = [
		'date_created',
		'id',
		'ip',
		'source_url',
		'payment_status',
		'transaction_id',
		'payment_amount',
		'payment_date',
		'payment_method',
		'is_fulfilled',
		'created_by',
	];

	/**
	 * The form ID.
	 *
	 * @since 3.0.0
	 *
	 * @var int
	 */
	private int $form_id;

	/**
	 * The template ID (e.g. `default_table`).
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private string $template_id;

	/**
	 * GF form meta.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	private array $form;

	/**
	 * Initializes the generator for one form + template combination.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $form_id     The form ID.
	 * @param string $template_id The template ID (e.g. `default_table`).
	 */
	public function __construct( int $form_id, string $template_id ) {
		$this->form_id     = $form_id;
		$this->template_id = $template_id;
		$this->form        = \GFAPI::get_form( $this->form_id ) ?: [];
	}

	/**
	 * Generates the complete default field configuration for a new View.
	 *
	 * @since 3.0.0
	 *
	 * @return array Field configuration keyed by `{context}_{zone}`, or empty
	 *               when the form is missing or has no fields.
	 */
	public function generate(): array {
		if ( empty( $this->form_id ) || empty( $this->form['fields'] ) ) {
			return [];
		}

		/**
		 * Modify whether to initialize the Multiple Entries layout with all form fields or only the fields displayed in the Gravity Forms Entries table when creating a new View.
		 *
		 * @since 2.27
		 *
		 * @param bool $show_all_fields Whether to include all form fields (true) or only the fields displayed in the Gravity Forms Entries table (false). Default: `false`.
		 * @param int  $form_id         The current form ID.
		 */
		$show_all_fields = apply_filters(
			'gk/gravityview/view/configuration/multiple-entries/initialize-with-all-form-fields',
			false,
			$this->form_id
		);

		$directory_fields = $show_all_fields
			? $this->build_all_form_fields_with_link()
			: $this->build_directory_fields();

		$entry_fields = $this->build_single_fields();

		return array_merge(
			$this->map_directory_fields( $directory_fields ),
			$this->map_single_fields( $entry_fields )
		);
	}

	/**
	 * Builds directory fields from GF's default entry list columns.
	 *
	 * The first field links to the Single Entry view.
	 *
	 * @since 3.0.0
	 *
	 * @return array Flat array of field configurations keyed by unique ID.
	 */
	private function build_directory_fields(): array {
		$columns = GFFormsModel::get_grid_columns( $this->form_id );
		$fields  = [];

		foreach ( $columns as $column_id => $column ) {
			$gv_field = \GravityView_Fields::get_instance( $column['type'] );

			if ( ! $gv_field ) {
				continue;
			}

			$fields[ uniqid( '', true ) ] = [
				'label'        => \GV\Utils::get( $column, 'label' ),
				'type'         => $gv_field->name,
				'id'           => $column_id,
				'form_id'      => $this->form_id,
				'show_as_link' => empty( $fields ),
			];
		}

		return $fields;
	}

	/**
	 * Builds all form fields with the first valid one linked to Single Entry.
	 *
	 * Used when the `initialize-with-all-form-fields` filter returns true.
	 *
	 * @since 3.0.0
	 *
	 * @return array Flat array of field configurations keyed by unique ID.
	 */
	private function build_all_form_fields_with_link(): array {
		$fields = $this->build_all_form_fields();

		foreach ( $fields as &$field ) {
			$gf_field = \GF_Fields::get( $field['type'] );

			if ( ! $gf_field ) {
				continue;
			}

			$field['show_as_link'] = true;
			break;
		}

		return $fields;
	}

	/**
	 * Builds Single Entry fields: all form fields plus an Edit Entry link.
	 *
	 * @since 3.0.0
	 *
	 * @return array Flat array of field configurations keyed by unique ID.
	 */
	private function build_single_fields(): array {
		$fields = $this->build_all_form_fields();

		$fields[ uniqid( '', true ) ] = [
			'label'       => esc_html__( 'Edit Entry', 'gk-gravityview' ),
			'admin_label' => esc_html__( 'Link to Edit Entry', 'gk-gravityview' ),
			'type'        => 'edit_link',
			'id'          => 'edit_link',
			'form_id'     => $this->form_id,
		];

		return $fields;
	}

	/**
	 * Builds field configurations for every form field.
	 *
	 * @since 3.0.0
	 *
	 * @return array Flat array of field configurations keyed by unique ID.
	 */
	private function build_all_form_fields(): array {
		$fields = [];

		foreach ( $this->form['fields'] as $gf_field ) {
			$fields[ uniqid( '', true ) ] = [
				'label'   => $gf_field->label,
				'type'    => $gf_field->type,
				'id'      => $gf_field->id,
				'form_id' => $this->form_id,
			];
		}

		return $fields;
	}

	/**
	 * Maps directory field configurations into the template's zone structure.
	 *
	 * Unrecognized templates fall back to the Table zone, matching the
	 * pre-existing behavior when no template is posted.
	 *
	 * @since 3.0.0
	 *
	 * @param array $field_configs Flat array of field configurations.
	 *
	 * @return array Keyed by `directory_{zone}`.
	 */
	private function map_directory_fields( array $field_configs ): array {
		if ( LayoutBuilder::ID === $this->template_id ) {
			return [ 'directory_' . LayoutBuilder::get_default_area_id() => $field_configs ];
		}

		if ( 'default_list' === $this->template_id ) {
			return $this->map_list_directory_fields( $field_configs );
		}

		return [ 'directory_table-columns' => $field_configs ];
	}

	/**
	 * Maps Single Entry field configurations into the template's zone structure.
	 *
	 * @since 3.0.0
	 *
	 * @param array $field_configs Flat array of field configurations.
	 *
	 * @return array Keyed by `single_{zone}`.
	 */
	private function map_single_fields( array $field_configs ): array {
		if ( LayoutBuilder::ID === $this->template_id ) {
			return [ 'single_' . LayoutBuilder::get_default_area_id() => $field_configs ];
		}

		if ( 'default_list' === $this->template_id ) {
			return [ 'single_list-description' => $field_configs ];
		}

		return [ 'single_table-columns' => $field_configs ];
	}

	/**
	 * Maps fields into the List template's semantic directory zones.
	 *
	 * The first field becomes the Listing Title (linked to Single Entry), the
	 * second the Subheading, remaining form fields the description, and the
	 * Entry Date lands in the footer.
	 *
	 * @since 3.0.0
	 *
	 * @param array $field_configs Flat array of field configurations.
	 *
	 * @return array Keyed by `directory_{zone}`.
	 */
	private function map_list_directory_fields( array $field_configs ): array {
		$fields = array_values( $field_configs );
		$keys   = array_keys( $field_configs );
		$result = [];

		if ( isset( $fields[0] ) ) {
			$result['directory_list-title'] = [ $keys[0] => $fields[0] ];
		}

		if ( isset( $fields[1] ) ) {
			$result['directory_list-subtitle'] = [ $keys[1] => $fields[1] ];
		}

		$description = [];

		for ( $i = 2, $count = count( $fields ); $i < $count; $i++ ) {
			$is_entry_meta = in_array( (string) $fields[ $i ]['id'], self::ENTRY_META_IDS, true );

			if ( $is_entry_meta ) {
				continue;
			}

			$description[ $keys[ $i ] ] = $fields[ $i ];
		}

		if ( $description ) {
			$result['directory_list-description'] = $description;
		}

		foreach ( $fields as $idx => $field ) {
			if ( 'date_created' === (string) $field['id'] ) {
				$result['directory_list-footer-left'] = [ $keys[ $idx ] => $field ];
				break;
			}
		}

		return $result;
	}
}
