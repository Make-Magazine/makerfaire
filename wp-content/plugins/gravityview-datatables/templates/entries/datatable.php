<?php
/**
 * Display a single entry when using a table template
 *
 * @global \GV\Template_Context $gravityview
 */

\GV\Mocks\Legacy_Context::push( array( 'view' => $gravityview->view ) );

?>

<?php gravityview_before( $gravityview ); ?>


<p class="gv-back-link"><?php echo gravityview_back_link( $gravityview ); ?></p>

<div class="<?php gv_container_class( 'gv-table-view gv-table-container gv-table-single-container', true, $gravityview ); ?>">
	<table class="gv-table-view-content">
		<?php

        if ( $gravityview->fields->by_position( 'single_table-columns' )->by_visible()->count() ): ?>
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
