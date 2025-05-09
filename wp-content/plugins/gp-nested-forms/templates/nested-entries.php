<?php
/**
 * @var array  $nested_fields      An array of GF_Field objects.
 * @var array  $nested_form        The form object of the nested form.
 * @var array  $nested_field_ids   An array of nested field IDs.
 * @var array  $entries            An array of child entries submitted from the current Nested Form field.
 * @var array  $labels             An array of labels used in this template.
 * @var array  $aria_labels        An array of labels used for screen readers.
 * @var array  $actions            An array of HTML strings used to display field actions.
 * @var bool   $enable_duplication Can child entries be duplicated?
 * @var int    $column_count       The number of columns.
 * @var string $add_button         The markup for the "Add Entry" button.
 * @var string $add_button_message The markup for situational messages related to the "Add Entry" button.
 */
?>
<div class="gpnf-nested-entries-container ginput_container">

	<table class="gpnf-nested-entries">

		<thead>
		<tr>
			<?php foreach ( $nested_fields as $nested_field ) : ?>
				<th class="gpnf-field-<?php echo $nested_field['id']; ?>">
					<?php echo GFCommon::get_label( $nested_field ); ?>
				</th>
			<?php endforeach; ?>
			<th class="gpnf-row-actions"><span class="screen-reader-text"><?php esc_html_e( 'Actions', 'gp-nested-forms' ); ?></span></th>
		</tr>
		</thead>

		<tbody data-bind="visible: entries().length, foreach: entries">
		<tr data-bind="attr: { 'data-entryid': id }">
			<?php foreach ( $nested_fields as $nested_field ) : ?>
				<td class="gpnf-field"
					data-bind="html: f<?php echo $nested_field['id']; ?>.label, attr: { 'data-value': f<?php echo $nested_field['id']; ?>.label }"
					data-heading="<?php echo GFCommon::get_label( $nested_field ); ?>"
				>&nbsp;</td>
			<?php endforeach; ?>
			<td class="gpnf-row-actions" style="display: none;" data-bind="visible: true">
				<ul>
					<li class="edit"><button type="button" class="edit-button gform-theme-button--secondary" data-bind="click: $parent.editEntry, attr: { 'aria-label': '<?php echo esc_js( $aria_labels['edit_entry'] ); ?>'.gformFormat( $index() + 1, f<?php echo $nested_fields[0]['id']; ?>.label ) }"><?php echo $labels['edit_entry']; ?></button></li>
					<?php if ( $enable_duplication ) : ?>
						<li class="duplicate" data-bind="visible: ! $parent.isMaxed()"><button type="button" class="duplicate-button gform-theme-button--secondary" href="#" data-bind="click: $parent.duplicateEntry, attr: { 'aria-label': '<?php echo esc_js( $aria_labels['duplicate_entry'] ); ?>'.gformFormat( $index() + 1, f<?php echo $nested_fields[0]['id']; ?>.label ) }"><?php echo $labels['duplicate_entry']; ?></button></li>
					<?php endif; ?>
					<li class="delete"><button type="button" class="delete-button gform-theme-button--simple gform-theme-button--size-md" data-bind="click: $parent.deleteEntry, attr: { 'aria-label': '<?php echo esc_js( $aria_labels['delete_entry'] ); ?>'.gformFormat( $index() + 1, f<?php echo $nested_fields[0]['id']; ?>.label ) }"><?php echo $labels['delete_entry']; ?></button></li>
				</ul>
			</td>
		</tr>
		</tbody>

		<tbody data-bind="visible: entries().length <= 0">
		<tr class="gpnf-no-entries" data-bind="visible: entries().length <= 0" style="display: none;">
			<td colspan="<?php echo $column_count; ?>">
				<?php echo $labels['no_entries']; ?>
			</td>
		</tr>
		</tbody>

	</table>

	<?php echo $add_button; ?>
	<?php echo $add_button_message; ?>

</div>
