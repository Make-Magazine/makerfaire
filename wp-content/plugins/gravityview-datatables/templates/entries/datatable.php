<?php
/**
 * Display a single entry when using a table template
 *
 * @global \GV\Template_Context $gravityview
 */

\GV\Mocks\Legacy_Context::push( array( 'view' => $gravityview->view ) );

?>

<?php gravityview_before( $gravityview ); ?>

<?php
// Matches core's single-entry table template (`templates/entries/table.php`): omit the
// link entirely when there is none, rather than an empty `<p>`.
if ( $link = gravityview_back_link( $gravityview ) ) :
	?>
	<nav class="gv-back-link" aria-label="<?php esc_attr_e( 'Back to entries', 'gv-datatables' ); ?>"><?php echo $link; ?></nav>
<?php endif; ?>

<div class="<?php gv_container_class( 'gv-table-view gv-table-container gv-table-single-container', true, $gravityview ); ?>">
	<table class="gv-table-view-content">
		<?php

        if ( $gravityview->fields->by_position( 'single_table-columns' )->by_visible( $gravityview->view )->count() ): ?>
			<thead>
				<?php gravityview_header( $gravityview ); ?>
			</thead>
			<tbody>
				<?php
					$gravityview->template->the_entry();
				?>
			</tbody>
			<tfoot>
				<?php gravityview_footer( $gravityview ); ?>
			</tfoot>
		<?php endif; ?>
	</table>
</div>
<?php gravityview_after( $gravityview ); ?>

<?php

\GV\Mocks\Legacy_Context::pop();
