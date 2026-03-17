<?php
/**
 * The header for the datatable output.
 *
 * @global GV\Template_Context $gravityview
 */
?>
<?php gravityview_before( $gravityview ); ?>
<div id="gv-datatables-<?php echo $gravityview->view->ID; ?>" class="<?php gv_container_class( 'gv-datatables-container', true, $gravityview ); ?>">
<?php
/**
 * Filter the CSS class for the DataTables table element
 *
 * @since 1.0
 *
 * @param string $class The CSS class for the table.
 */
?>
<table data-viewid="<?php echo $gravityview->view->ID; ?>" class="gv-datatables <?php echo esc_attr( apply_filters( 'gravityview_datatables_table_class', 'display dataTable' ) ); ?>">
	<thead>
		<?php gravityview_header( $gravityview ); ?>
		<tr>
			<?php $gravityview->template->the_columns(); ?>
		</tr>
	</thead>
