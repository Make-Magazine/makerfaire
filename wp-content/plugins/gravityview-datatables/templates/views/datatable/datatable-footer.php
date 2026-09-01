<?php
/**
 * The footer for the output table.
 *
 * @global stdClass $gravityview (\GV\View $gravityview::$view, \GV\View_Template $gravityview::$template)
 */
?>
	<tfoot>
		<tr>
			<?php $gravityview->template->the_columns( 'foot' ); ?>
		</tr>
		<?php gravityview_footer( $gravityview ); ?>
	</tfoot>
</table>
</div><!-- end .gv-datatables-container -->
<?php gravityview_after( $gravityview ); ?>
