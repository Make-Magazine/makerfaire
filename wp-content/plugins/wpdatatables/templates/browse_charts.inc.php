<div id="wdtPreloadLayer" class="overlayed">
</div>

<div class="wrap">
    <img src="<?php echo dirname(plugin_dir_url(__FILE__)); ?>/assets/img/wpdatatables-logo.png" class="wpdatatables_logo" style="margin: 10px" />
	<p><i><?php _e('Please refer to','wpdatatables');?> <a href="http://wpdatatables.com/documentation/wpdatacharts/"><?php _e('wpDataTables documentation on Charts','wpdatatables');?></a> <?php _e('if you have some questions or problems with the plugin.','wpdatatables'); ?></i></p>    
        <h2><?php _e('wpDataCharts','wpdatatables');?><a href="admin.php?page=wpdatatables-chart-wizard" class="add-new-h2"><?php _e('Add new chart','wpdatatables');?></a></h2>

	<form method="post" action="<?php echo admin_url('admin.php?page=wpdatatables-charts'); ?>" id="wpDataChartsBrowseForm">
		<?php echo $tableHTML; ?>
	</form>
	
</div>

<script type="text/javascript">
var duplicate_table_id = '';

jQuery(document).ready(function(){
    
	jQuery('#doaction').click(function(e){
            e.preventDefault();

            if(jQuery('#bulk-action-selector-top').val() == ''){ return; }
            if(jQuery('#wpDataChartsBrowseForm table.widefat input[type="checkbox"]:checked').length == 0){ return; }

            if(confirm("<?php _e('Are you sure','wpdatatables'); ?>?")){
                    jQuery('#wpDataChartsBrowseForm').submit();
            }
	});
	
});
</script>