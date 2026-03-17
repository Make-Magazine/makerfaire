<?php
namespace GV;

/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

/**
 * The View DataTables Template class .
 *
 * Renders a \GV\View and a \GV\Entry_Collection via a \GV\View_Renderer.
 */
class View_DataTable_Template extends View_Template {
	/**
	 * @var string The template slug to be loaded (like "table", "list")
	 */
	public static $slug = 'datatable';

	/**
	 * @var string The template configuration slug
	 * Some templates share the same configuration layouts.
	 */
	public static $_configuration_slug = 'table';

	public function __construct( View $view, Entry_Collection $entries, Request $request ) {
		parent::__construct( $view, $entries, $request );
		$this->plugin_directory = GV_DT_DIR;
	}

	/**
	 * Output the table column names.
	 *
	 * @return void
	 */
	public function the_columns() {
		$fields = $this->view->fields->by_position( 'directory_table-columns' );
		$form   = $this->view->form;

		$visible_field_ids = array();

		/** @todo Add class filters from the old code. */
		foreach ( $fields->by_visible()->all() as $field ) {

			if ( 'custom' == $field->type ) {
				$visible_field_ids[] = 'custom_' . $field->UID;
			} else {
				$visible_field_ids[] = $field->ID;
			}

			$column_label = apply_filters( 'gravityview/template/field_label', $field->get_label( $this->view, $form ), $field->as_configuration(), $form->form ? $form->form : null, null );

			echo strtr( '<th id="gv-field-{form_id}-{field_id}" class="gv-field-{form_id}-{field_id} {css_class}" {width} scope="col"><span class="gv-field-label">{label}</span></th>', array(
				'{form_id}'   => esc_attr( $form->ID ),
				'{field_id}'  => esc_attr( $field->ID ),
				'{css_class}' => gravityview_sanitize_html_class( $field->custom_class ),
				'{width}'     => $field->width ? sprintf( ' style="width: %d%%"', $field->width ) : '',
				'{label}'     => $column_label,
			) );
		}

		// Add hidden column headers for sort fields that aren't in the visible columns.
		$sort_field_setting = (array) $this->view->settings->get( 'sort_field', array() );

		foreach ( $sort_field_setting as $sort_field ) {
			if ( empty( $sort_field ) || in_array( $sort_field, $visible_field_ids ) ) {
				continue;
			}

			// Create a field object for the hidden sort field.
			$hidden_field = is_numeric( $sort_field )
				? \GV\GF_Field::by_id( $this->view->form, $sort_field )
				: \GV\Internal_Field::by_id( $sort_field );

			if ( ! $hidden_field ) {
				continue;
			}

			$column_label = apply_filters( 'gravityview/template/field_label', $hidden_field->get_label( $this->view, $form ), $hidden_field->as_configuration(), $form->form ? $form->form : null, null );

			// Output hidden column header.
			echo strtr( '<th id="gv-field-{form_id}-{field_id}" class="gv-field-{form_id}-{field_id} gv-hidden-sort-column" scope="col"><span class="gv-field-label">{label}</span></th>', array(
				'{form_id}'   => esc_attr( $form->ID ),
				'{field_id}'  => esc_attr( $sort_field ),
				'{label}'     => $column_label,
			) );
		}
	}

	/**
	 * Output the entry row.
	 *
	 * @param \GV\Entry $entry The entry to be rendered.
	 * @param array $attributes The attributes for the <tr> tag
	 *
	 * @return void
	 */
	public function the_entry( \GV\Entry $entry, $attributes ) {
		/**
		 * Filter the row attributes for the row in table view.
		 *
		 * @since 2.0
		 *
		 * @param array             $attributes The HTML attributes.
		 * @param \GV\Entry         $entry      The entry this is being called for.
		 * @param \GV\View_Template $this       This template.
		 */
		$attributes = apply_filters( 'gravityview/entry/row/attributes', $attributes, $entry, $this );

		/** Glue the attributes together. */
		foreach ( $attributes as $attribute => $value ) {
			$attributes[$attribute] = sprintf( "$attribute=\"%s\"", esc_attr( $value) );
		}
		$attributes = implode( ' ', $attributes );

		$fields = $this->view->fields->by_position( 'directory_table-columns' )->by_visible();

		?>
			<tr<?php echo $attributes ? " $attributes" : ''; ?>>
				<?php foreach ( $fields->all() as $field ) {
					$this->the_field( $field, $entry );
				} ?>
			</tr>
		<?php
	}

	/**
	 * Output a field cell.
	 *
	 * @param \GV\Field $field The field to be ouput.
	 * @param \GV\Field $entry The entry this field is for.
	 *
	 * @return void
	 */
	public function the_field( \GV\Field $field, \GV\Entry $entry ) {
		$attributes = array(
			'id' => sprintf( 'gv-field-%d-%s', $this->view->form ? $this->view->form->ID : 0, $field->ID ),
			'class' => sprintf( 'gv-field-%d-%s', $this->view->form ? $this->view->form->ID : 0, $field->ID ),
		);

		/**
		 * Filter the row attributes for the row in table view.
		 *
		 * @since 2.0
		 *
		 * @param array             $attributes The HTML attributes.
		 * @param \GV\Field         $field      The field these attributes are for.
		 * @param \GV\Entry         $entry      The entry this is being called for.
		 * @param \GV\View_Template $this       This template.
		 */
		$attributes = apply_filters( 'gravityview/entry/cell/attributes', $attributes, $field, $entry, $this );

		/** Glue the attributes together. */
		foreach ( $attributes as $attribute => $value ) {
			$attributes[$attribute] = sprintf( "$attribute=\"%s\"", esc_attr( $value) );
		}
		$attributes = implode( ' ', $attributes );
		if ( $attributes ) {
			$attributes = " $attributes";
		}

		$form = $this->view->form;
		$single_entry = $entry;

		if ( is_callable( array( $entry, 'is_multi' ) ) && $entry->is_multi() ) {

		    if ( ! $single_entry = $entry->from_field( $field ) ) {
				echo '<td></td>';
				return;
			}

			$form = GF_Form::by_id( $field->form_id );
		}

		$renderer = new Field_Renderer();
		$source   = is_numeric( $field->ID ) ? $form : new Internal_Source();

		/** Output. */
		printf( '<td%s>%s</td>', $attributes, $renderer->render( $field, $this->view, $source, $single_entry, $this->request ) );
	}
}
