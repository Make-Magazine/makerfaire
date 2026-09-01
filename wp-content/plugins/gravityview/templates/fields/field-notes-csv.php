<?php
/**
 * The entry notes field output template for CSVs.
 *
 * @global \GV\Template_Context $gravityview
 * @since 3.3.0
 */

if ( ! isset( $gravityview ) || empty( $gravityview->template ) ) {
	gravityview()->log->error( '{file} template loaded without context', array( 'file' => __FILE__ ) );
	return;
}

if ( ! class_exists( 'GravityView_Entry_Notes' ) ) {
	return;
}

// Notes belong to a single entry, so a joined View has to use the entry from the field's own
// form rather than whichever one Multi_Entry happens to hold first.
$entry          = $gravityview->entry->from_field( $gravityview->field, $gravityview->entry )->as_entry();
$field_settings = $gravityview->field->as_configuration();

// Exports must not disclose notes the same user would not be shown on screen.
if ( ! GravityView_Field_Notes::can_view_notes( $field_settings ) ) {
	return;
}

$notes        = GravityView_Field_Notes::get_visible_notes( $entry['id'], $field_settings );
$notes_output = \GV\Utils::get( $field_settings, 'notes_output', 'notes' );

if ( in_array( $notes_output, GravityView_Field_Notes::get_summary_modes(), true ) ) {
	// Merge tags resolve against the form the entry belongs to, which in a unioned View is
	// not the View's primary form.
	$entry_form = GVCommon::get_form( \GV\Utils::get( $entry, 'form_id', 0 ) );

	if ( ! $entry_form ) {
		$entry_form = $gravityview->view->form ? $gravityview->view->form->form : [];
	}

	echo GravityView_Field_Notes::get_summary_output( $notes, $notes_output, $field_settings, $entry_form, $entry );

	return;
}

if ( ! $notes ) {
	return;
}

/** @see templates/fields/field-fileupload-csv.php */
$glue = apply_filters( 'gravityview/template/field/csv/glue', ';', $gravityview );

$output = [];

foreach ( $notes as $note ) {
	$output[] = strtr(
		/* translators: placeholders in [brackets] are replaced and must not be translated. */
		_x( '[author] ([date]): [note]', 'A single entry note in a CSV export.', 'gk-gravityview' ),
		[
			'[author]' => $note->user_name,
			'[date]'   => $note->date_created,
			'[note]'   => $note->value,
		]
	);
}

echo implode( $glue, $output );
