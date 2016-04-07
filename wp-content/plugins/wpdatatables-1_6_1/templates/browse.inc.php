<div id="wdtPreloadLayer" class="overlayed">
</div>

<div class="wrap">
    <img src="<?php echo dirname(plugin_dir_url(__FILE__)); ?>/assets/img/wpdatatables-logo.png" class="wpdatatables_logo" style="margin: 10px" />
	<p><i><?php _e('Please refer to','wpdatatables');?> <a href="http://wpdatatables.com/documentation/"><?php _e('wpDataTables documentation','wpdatatables');?></a> <?php _e('if you have some questions or problems with the plugin.','wpdatatables'); ?></i></p>    <h2>wpDataTables <a href="admin.php?page=wpdatatables-addnew" class="add-new-h2"><?php _e('Add new','wpdatatables');?></a></h2>

	<form method="post" action="<?php echo admin_url('admin.php?page=wpdatatables-administration'); ?>" id="wpDataTablesBrowseForm">
		<?php echo $tableHTML; ?>
	</form>
	
</div>

<div id="newTableName" style="display: none;">
	<label><?php _e('New table title','wpdatatables');?></label>
	<input type="text" value="" class="wdtDuplicateTableName" />
</div>

<script type="text/javascript">
var duplicate_table_id = '';

jQuery(document).ready(function(){
	jQuery('a.submitdelete').click(function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            if(confirm("<?php _e('Are you sure','wpdatatables'); ?>?")){
                    window.location = jQuery(this).attr('href');
            }
	})
	
	jQuery('button.wpDataTablesDuplicateTable').click(function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            duplicate_table_id = jQuery(this).data('table_id');
            wdtAlertDialog(jQuery('#newTableName').html(),'<?php _e('Duplicate table','wpdatatables') ?>');
            jQuery('input.wdtDuplicateTableName').val(jQuery(this).data('table_name')+'_<?php _e('copy','wpdatatables'); ?>');
	});
        
        jQuery('button.wpDataTablesManualEdit').click(function(e){
            e.preventDefault();
            var url = '/wp-admin/admin.php?page=wpdatatables-editor&table_id='+jQuery(this).data('table_id');
            window.location = url;
        });
	
	jQuery(document).on('click','button.remodal-confirm',function(e){
            jQuery('#wdtPreloadLayer').show();
            var new_table_name = jQuery(this).parent().find('input.wdtDuplicateTableName').val();
            jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                            action: 'wpdatatables_duplicate_table',
                            table_id: duplicate_table_id,
                            new_table_name: new_table_name
                    },
                    success: function(){
                            window.location.reload();
                    }
            });
            jQuery('.wdtRemodal').remodal().close();
	});
	
	jQuery('#doaction').click(function(e){
            e.preventDefault();

            if(jQuery('#bulk-action-selector-top').val() == ''){ return; }
            if(jQuery('#wpDataTablesBrowseForm table.widefat input[type="checkbox"]:checked').length == 0){ return; }

            if(confirm("<?php _e('Are you sure','wpdatatables'); ?>?")){
                    jQuery('#wpDataTablesBrowseForm').submit();
            }
	});
	
});
</script>
