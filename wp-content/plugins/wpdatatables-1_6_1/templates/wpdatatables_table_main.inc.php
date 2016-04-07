<?php
/**
* Template file for the plain HTML table
* wpDataTables Module
* 
* @author cjbug@ya.ru
* @since 10.10.2012
*
**/
?>
<?php if($wpDataTable->getFilteringForm()) { ?>
<?php do_action('wpdatatables_before_filtering_form', $wpDataTable->getWpId()); ?>
<div class="wpDataTables wpDataTablesFilter">
	<div id="filterBox_<?php echo $wpDataTable->getId()?>" class="wpDataTableFilterBox">
	    <?php foreach( $wpDataTable->getColumns() as $key=>$dataColumn) { ?>
	    	<?php if($dataColumn->getFilterType()->type != 'null') { ?>
	    	<div class="wpDataTableFilterSection" id="<?php echo $wpDataTable->getId().'_'.$key.'_filter' ?>_sections">
		    	<label><?php echo $dataColumn->getTitle() ?>:</label>
                            <div id="<?php echo $wpDataTable->getId().'_'.$key.'_filter' ?>"></div>
			</div>
			<?php } ?>
	    <?php } ?>
	</div>
</div>
<?php do_action('wpdatatables_after_filtering_form', $wpDataTable->getWpId()); ?>
<?php } ?>

<?php $advancedFilterPosition = get_option('wdtRenderFilter'); ?>
<?php $sumColumnValues = array(); ?>
<?php do_action('wpdatatables_before_table', $wpDataTable->getWpId()); ?>
<input type="hidden" id="<?php echo $wpDataTable->getId() ?>_desc" value='<?php echo $wpDataTable->getJsonDescription(); ?>' />
<table id="<?php echo $wpDataTable->getId() ?>" class="<?php if ($wpDataTable->isScrollable()) { ?>scroll<?php } ?> display responsive nowrap <?php echo $wpDataTable->getCSSClasses() ?> wpDataTable" style="<?php echo $wpDataTable->getCSSStyle() ?>" data-described-by='<?php echo $wpDataTable->getId() ?>_desc' data-wpdatatable_id="<?php echo $wpDataTable->getWpId(); ?>">
    <thead>
	<?php if( !$wpDataTable->getFilteringForm() && $wpDataTable->advancedFilterEnabled() && $advancedFilterPosition == 'header'  ) { ?>
	<tr>
            <?php do_action('wpdatatables_before_header', $wpDataTable->getWpId()); ?>
            <?php $expandShown = false; ?>
	    <?php foreach($wpDataTable->getColumns() as $dataColumn) { ?><th <?php if(!$expandShown && $dataColumn->isVisibleOnMobiles()){ ?>data-class="expand"<?php $expandShown = true; } ?> <?php if($dataColumn->getHiddenAttr()) { ?>data-hide="<?php echo $dataColumn->getHiddenAttr() ?>"<?php } ?> class="header <?php if( $dataColumn->sortEnabled() ) { ?>sort<?php } ?> <?php echo $dataColumn->getCSSClasses(); ?>" style="<?php echo $dataColumn->getCSSStyle(); ?>"><?php echo ( $dataColumn->getFilterType()->type != 'null') ? $dataColumn->getTitle() : '' ?></th><?php } ?>
            <?php do_action('wpdatatables_after_header', $wpDataTable->getWpId()); ?>
	</tr>
	<?php } ?>
	<tr>
            <?php do_action('wpdatatables_before_header', $wpDataTable->getWpId()); ?>
            <?php $expandShown = false; ?>
	    <?php foreach($wpDataTable->getColumns() as $dataColumn) { ?><th <?php if(!$expandShown && $dataColumn->isVisibleOnMobiles()){ ?>data-class="expand"<?php $expandShown = true; } ?> <?php if($dataColumn->getHiddenAttr()) { ?>data-hide="<?php echo $dataColumn->getHiddenAttr() ?>"<?php } ?> class="header <?php if( $dataColumn->sortEnabled() ) { ?>sort<?php } ?> <?php echo $dataColumn->getCSSClasses(); ?>" style="<?php echo $dataColumn->getCSSStyle(); ?>"><?php  echo $dataColumn->getTitle() ?></th><?php } ?>
            <?php do_action('wpdatatables_after_header', $wpDataTable->getWpId()); ?>
	</tr>
    </thead>
    <tbody>
    <?php do_action('wpdatatables_before_first_row', $wpDataTable->getWpId()); ?>
	<?php foreach( $wpDataTable->getDataRows() as $wdtRowIndex => $wdtRowDataArr) { ?>
	<?php do_action('wpdatatables_before_row', $wpDataTable->getWpId(), $wdtRowIndex); ?>
	<tr id="table_<?php echo $wpDataTable->getWpId() ?>_row_<?php echo $wdtRowIndex; ?>">
	    <?php foreach( $wpDataTable->getColumnsByHeaders() as $dataColumnHeader => $dataColumn ) { ?>
			<td style="<?php echo $dataColumn->getCSSStyle();?>"><?php echo apply_filters( 'wpdatatables_filter_cell_output', $wpDataTable->returnCellValue( $wdtRowDataArr[ $dataColumnHeader ], $dataColumnHeader ), $wpDataTable->getWpId(), $dataColumnHeader ); ?></td>
			<?php if( in_array( $dataColumnHeader, $wpDataTable->getSumColumns() ) ) {
				if( !isset( $sumColumnValues[$dataColumnHeader] ) ){
					$sumColumnValues[$dataColumnHeader] = (float) $wdtRowDataArr[ $dataColumnHeader ];
				}else{
					$sumColumnValues[$dataColumnHeader] += (float) $wdtRowDataArr[ $dataColumnHeader ];
				}
			} ?>
	    <?php } ?>
	</tr>
	<?php do_action('wpdatatables_after_row', $wpDataTable->getWpId(), $wdtRowIndex); ?>
	<?php } ?>
	<?php do_action('wpdatatables_after_last_row', $wpDataTable->getWpId()); ?>
    </tbody>
    <?php if( ( $wpDataTable->advancedFilterEnabled() && (get_option('wdtRenderFilter') == 'footer') ) 
            || !empty( $sumColumnValues ) ) { ?>
    <tfoot>
	<?php do_action('wpdatatables_before_footer', $wpDataTable->getWpId()); ?>
	<tr <?php if($wpDataTable->getFilteringForm()) { ?>style="display: none"<?php } ?>>
	    <?php foreach( $wpDataTable->getColumns() as $dataColumn) { ?><td class="header <?php if( $dataColumn->sortEnabled() ) { ?>sort<?php } ?> <?php echo $dataColumn->getCSSClasses(); ?>" style="<?php echo $dataColumn->getCSSStyle(); ?>"><?php echo $dataColumn->getTitle(); ?></td><?php } ?>
	</tr>
        <?php if( !empty( $sumColumnValues ) ){ ?>
	<tr class="sum_row">
	    <?php foreach( $wpDataTable->getColumnsByHeaders() as $dataColumnHeader => $dataColumn ) { ?>
            <td class="sum_cell" data-column_header="<?php echo $dataColumnHeader; ?>" style="<?php echo $dataColumn->getCSSStyle(); ?>">
                	 <?php if( !empty( $sumColumnValues[$dataColumnHeader] ) ){ 
                    echo '&#8721; = '. $wpDataTable->returnCellValue( $sumColumnValues[$dataColumnHeader], $dataColumnHeader );                   
                } ?>
            </td>
            <?php } ?>
	</tr>
        <?php } ?>
	<?php do_action('wpdatatables_after_footer', $wpDataTable->getWpId()); ?>
    </tfoot>
    <?php } ?>
    
</table>
<?php do_action('wpdatatables_after_table', $wpDataTable->getWpId()); ?>

<br/><br/>

<?php if($wpDataTable->isEditable()) { ?>

<div id="<?php echo $wpDataTable->getId() ?>_edit_dialog" style="display: none; text-align: center" title="<?php _e('Edit','wpdatatables');?>">
<?php do_action('wpdatatables_before_editor_dialog', $wpDataTable->getWpId()); ?>
<div class="data_validation_notify" style="display: none"></div>
<div class="data_saved_notify" style="display: none"><?php _e('Data saved!','wpdatatables'); ?></div>
<table>
	<thead>
	<tr>
	<th style="width: 20%"></th>
	<th style="width: 80%"></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach( $wpDataTable->getColumnsByHeaders() as $dataColumn_key=>$dataColumn ) { ?>
	<tr <?php if( ($dataColumn_key == $wpDataTable->getIdColumnKey()) || ($dataColumn->getInputType() == 'none') || ( ( $wpDataTable->getUserIdColumn() != '' ) && ( $dataColumn_key == $wpDataTable->getUserIdColumn() ) ) ) { ?>style="display: none" <?php if($dataColumn_key == $wpDataTable->getIdColumnKey()) { ?>class="idRow"<?php } } ?>>
                <td><label for="<?php echo $wpDataTable->getId() ?>_<?php echo $dataColumn_key ?>"><?php echo $dataColumn->getTitle(); ?>:<?php if( $dataColumn->getNotNull() ){ ?> * <?php } ?></label></td>
		<?php $possibleValues = $dataColumn->getPossibleValues(); ?>
		<td>
		<?php if($dataColumn->getInputType() == 'textarea') { ?>
			<textarea data-input_type="<?php echo $dataColumn->getInputType();?>" class="editDialogInput <?php if( $dataColumn->getNotNull() ){ ?>mandatory<?php } ?>" id="<?php echo $wpDataTable->getId() ?>_<?php echo $dataColumn_key ?>" data-key="<?php echo $dataColumn_key ?>" rows="3" columns="50" data-column_header="<?php echo $dataColumn->getTitle();?>"></textarea>
		<?php } elseif(($dataColumn->getInputType() == 'selectbox') || ($dataColumn->getInputType() == 'multi-selectbox')) { ?>
			<select id="<?php echo $wpDataTable->getId() ?>_<?php echo $dataColumn_key ?>" data-input_type="<?php echo $dataColumn->getInputType();?>" data-key="<?php echo $dataColumn_key ?>" class="editDialogInput <?php if( $dataColumn->getNotNull() ){ ?>mandatory<?php } ?>" <?php if($dataColumn->getInputType() == 'multi-selectbox') { ?>multiple="multiple"<?php } ?> data-column_header="<?php echo $dataColumn->getTitle();?>" >
                            <?php foreach($possibleValues as $possibleValue) { ?>
                            <option value="<?php echo $possibleValue ?>"><?php echo $possibleValue ?></option>
                            <?php } ?>
			</select>
		<?php } elseif($dataColumn->getInputType() == 'attachment') { ?>
		    <span class="fileinput-button">
		        <button id="<?php echo $wpDataTable->getId() ?>_<?php echo $dataColumn_key ?>_button" data-column_type="<?php echo $dataColumn->getDataType();?>" data-input_type="<?php echo $dataColumn->getInputType(); ?>" data-rel_input="<?php echo $wpDataTable->getId() ?>_<?php echo $dataColumn_key ?>" class="btn fileupload_<?php echo $wpDataTable->getId() ?>"><?php _e('Browse','wpdatatables');?></button>
		        <input type="hidden" id="<?php echo $wpDataTable->getId() ?>_<?php echo $dataColumn_key ?>" data-key="<?php echo $dataColumn_key ?>" data-input_type="<?php echo $dataColumn->getInputType();?>" class="editDialogInput" />
		    </span>
		    <div id="files_<?php echo $wpDataTable->getId() ?>_<?php echo $dataColumn_key ?>" class="files" style="width: 250px;"></div>
		<?php } else { ?>
			<input type="text" 
					value="" 
					id="<?php echo $wpDataTable->getId() ?>_<?php echo $dataColumn_key ?>" 
					data-key="<?php echo $dataColumn_key ?>" 
					data-column_type="<?php echo $dataColumn->getDataType();?>"
                    data-column_header="<?php echo $dataColumn->getTitle();?>"
					data-input_type="<?php echo $dataColumn->getInputType();?>" 
					class="editDialogInput  <?php if( $dataColumn->getNotNull() ){ ?>mandatory<?php } ?> 
					<?php if($dataColumn->getDataType() == 'float') { ?>maskMoney<?php } ?> 
					<?php if($dataColumn->getInputType() == 'date') { ?>datepicker<?php } ?>" 
			/>
		<?php } ?>
		</td>
	</tr>
	<?php } ?>
	</tbody>
	</table>
	<button id="<?php echo $wpDataTable->getId() ?>_close_edit_dialog" class="btn"><?php _e('Cancel','wpdatatables');?></button>
	<button id="<?php echo $wpDataTable->getId() ?>_prev_edit_dialog" class="btn">&lt;&lt; <?php _e('Prev','wpdatatables');?></button>
	<button id="<?php echo $wpDataTable->getId() ?>_next_edit_dialog" class="btn"><?php _e('Next','wpdatatables');?> &gt;&gt;</button>
	<button id="<?php echo $wpDataTable->getId() ?>_apply_edit_dialog" class="btn"><?php _e('Apply','wpdatatables');?></button>
	<button id="<?php echo $wpDataTable->getId() ?>_ok_edit_dialog" class="btn"><?php _e('OK','wpdatatables');?></button>
<?php do_action('wpdatatables_after_editor_dialog', $wpDataTable->getWpId()); ?>
</div>
<?php } ?>