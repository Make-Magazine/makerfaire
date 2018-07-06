<?php
/**
 * The header for the datatable output.
 *
 * @global stdClass $gravityview (\GV\View $gravityview::$view, \GV\View_Template $gravityview::$template)
 */
?>
<?php gravityview_before( $gravityview ); ?>
<div id="gv-datatables-<?php echo $gravityview->view->ID; ?>" class="<?php gv_container_class( 'gv-datatables-container', true, $gravityview ); ?>">
<table data-viewid="<?php echo $gravityview->view->ID; ?>" class="gv-datatables <?php echo esc_attr( apply_filters( 'gravityview_datatables_table_class', 'display dataTable' ) ); ?>">
	<thead>
		<?php gravityview_header(); ?>
		<tr>
			<?php $gravityview->template->the_columns(); ?>
		</tr>
	</thead>
