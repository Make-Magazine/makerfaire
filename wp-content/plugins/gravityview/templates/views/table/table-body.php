<?php
/**
 * The entry loop for the table output.
 *
 * @global \GV\Template_Context $gravityview
 */

if ( ! isset( $gravityview ) || empty( $gravityview->template ) ) {
	gravityview()->log->error( '{file} template loaded without context', array( 'file' => __FILE__ ) );
	return;
}

/** @type \GV\View_Table_Template $template */
$template = $gravityview->template;

if ( 1 === (int) $gravityview->view->settings->get( 'no_entries_options', '0' ) ) {
	$no_results_css_class = 'gv-no-results gv-no-results-form';
} else {
	$no_results_css_class = 'gv-no-results gv-no-results-text';
}

$column_count = $gravityview->fields->by_position( 'directory_table-columns' )->by_visible( $gravityview->view )->count();

/**
 * Filters the number of table columns used for colspan calculations.
 *
 * @since 3.0.0
 *
 * @param int                  $column_count Number of visible table columns.
 * @param \GV\Template_Context $gravityview  The template context.
 */
$column_count = apply_filters( 'gravityview/template/table/columns/count', $column_count, $gravityview );

?>
	<tbody>
		<?php

		/** @action `gravityview/template/table/body/before` */
		$template::body_before( $gravityview );

		if ( ! $gravityview->entries->count() ) {
			?>
			<tr>
				<?php

				/** @action `gravityview/template/table/tr/before` */
				$template::tr_before( $gravityview );

				?>
				<td colspan="<?php echo $column_count ? (int) $column_count : ''; ?>" class="<?php echo esc_attr( $no_results_css_class ); ?>" role="status">
					<?php echo gv_no_results( true, $gravityview ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- No-results output may contain markup and is run through wp_kses_post() by the renderer. ?>
				</td>
				<?php

				/** @action `gravityview/template/table/tr/after` */
				$template::tr_after( $gravityview );

				?>
			</tr>
			<?php
		} else {
			foreach ( $gravityview->entries->all() as $entry ) {

				// Add `alt` class to alternate rows.
				$alt = empty( $alt ) ? 'alt' : '';

				/** @filter `gravityview/template/table/entry/class` */
				$class = $template::entry_class( $alt, $entry, $gravityview );

				$attributes = array(
					'class' => $class,
				);

				$template->the_entry( $entry, $attributes );
			}
		}

		/** @action `gravityview/template/table/body/after` */
		$template::body_after( $gravityview );
		?>
	</tbody>
