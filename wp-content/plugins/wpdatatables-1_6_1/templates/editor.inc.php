<div class="wpDataTables metabox-holder toplevel_page_wpdatatables-administration">
    <div id="wdtPreloadLayer" class="overlayed">
    </div>

    <div class="wrap">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder">
                <div id="postbox-container-1" class="postbox-container">
                    <img src="<?php echo dirname(plugin_dir_url(__FILE__)); ?>/assets/img/wpdatatables-logo.png" class="wpdatatables_logo" />
                    <p><i><?php _e('Please refer to','wpdatatables');?> <a href="http://wpdatatables.com/documentation/front-end-editing/back-end-editor/"><?php _e('wpDataTables documentation on the back-end editor','wpdatatables');?></a> <?php _e('if you have some questions or problems.','wpdatatables'); ?></i></p>
                    <h2>
                        <?php _e('wpDataTables manual editor','wpdatatables'); ?>
                        <?php if (!empty($table_id)) { ?>
                        <a href="admin.php?page=wpdatatables-administration&action=delete&table_id=<?php echo $table_id ?>" class="add-new-h2 submitdelete"><?php _e('Delete','wpdatatables');?></a>
                        <?php } ?>
                    </h2>
                    <div id="message" class="updated" <?php if (empty($table_id)) { ?>style="display: none;"<?php } ?> >
                        <p id="wdtScId"><?php _e('To insert the table on your page use the shortcode','wpdatatables');?>: <strong>[wpdatatable id=<?php if (!empty($table_id)) {echo $table_id; } ?>]</strong></p>
                    </div>
                    
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                        <div class="postbox">
                            
                            <input type="hidden" id="wpdatatables_table_id" value="<?php echo $table_id ?>" />

                            <div class="handlediv" title="<?php _e('Click to toggle','wpdatatables'); ?>"><br/></div>
                            <h3 class="hndle" style="height: 27px">
                                <span><div class="dashicons dashicons-feedback"></div> <?php _e('Edit the table content','wpdatatables');?></span>
                                <div class="pull-right" style="margin-right: 5px">
                                    <button class="button addColumn"><span class="dashicons dashicons-plus-alt" style="margin-top: 3px"></span> <?php _e('Add column','wpdatatables'); ?></button>
                                    <button class="button removeColumn"><span class="dashicons dashicons-dismiss" style="margin-top: 3px"></span> <?php _e('Remove column','wpdatatables'); ?></button>
                                    |
                                    <a href="/wp-admin/admin.php?page=wpdatatables-administration&action=edit&table_id=<?php echo $table_id ?>" name="changeWdtStructure" class="button-primary"><?php _e('Go to table settings','wpdatatables');?></a>
                                    <button class="button-primary closeButton"><?php _e('Close','wpdatatables');?></button>
                                </div>
                            </h3>

                            <div class="inside">
                                
                                <?php echo $table_to_edit; ?>
                                
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<div class="remodal wpDataTables wdtRemodal addColumn">

    <h1><?php _e('Add Column','wpdatatable'); ?></h1>
    
    <div class="columnBlock">
        <table>
            <tr>
                <td>
                    <label><span><strong><?php _e('Column header','wpdatatables'); ?></strong></span>:</label>
                </td>
                <td>
                    <div class="columnName">
                            <input type="text" value="" /><br/>
                            <span class="error" style="display: none"><?php _e( 'Column header cannot be empty!' ); ?></span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label><span><strong><?php _e('Type','wpdatatables'); ?></strong></span>:</label>
                </td>    
                <td>
                    <div class="columnType">
                        <select class="columnType">
                            <?php foreach( WDTTools::getPossibleColumnTypes() as $columnTypeKey => $columnTypeName ) { ?>
                            <option value="<?php echo $columnTypeKey ?>"><?php echo $columnTypeName ?></option>
                            <?php }?>
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label><span><strong><?php _e('Insert after','wpdatatables'); ?></strong></span>:</label>
                </td>    
                <td>
                    <div class="insertAfter">
                        <select class="insertAfter">
                            <option value="%%beginning%%"><?php _e('Beginning of table','wpdatatables'); ?></option>
                            <option value="%%end%%"><?php _e('End of table','wpdatatables'); ?></option>
                            <?php foreach( $column_data as $column ){ ?>
                                <?php if( $column->orig_header == 'wdt_ID' ) { continue; } ?>
                                <option value="<?php echo $column->orig_header; ?>"><?php echo $column->display_header; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </td>
            </tr>
            <tr class="columnPossibleValuesBlock" style="display: none">
                <td>
                    <label><span><strong><?php _e('Possible values','wpdatatables'); ?></strong></span>:</label><br/>
                    <span class="description"><small><?php _e('Separate with comma','wpdatatables');?></small>.</span>

                </td>
                <td>
                    <div class="columnPossibleValues">
                        <input type="text" value="" />
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label><span><strong><?php _e('Default value','wpdatatables'); ?></strong></span>:</label></br>
                    <span class="description"><small><?php _e('(Optional)','wpdatatables'); ?></small></span>
                </td>
                <td>
                    <div class="columnDefaultValue">
                        <input type="text" value="" />
                    </div>
                </td>
            </tr>
            <tr>
                <td><span><strong><?php _e('Fill with default value?','wpdatatables'); ?></strong></span>:</label></td>
                <td>
                    <input type="checkbox" checked="checked" class="columnFillDefault" />
                </td>
            </tr>
        </table>
    </div>
    
    <button class="btn" id="cancelNewColumn"><?php _e( 'Cancel', 'wpdatatables' ); ?></button>
    <button class="btn" id="submitNewColumn"><?php _e( 'OK', 'wpdatatables' ); ?></button>
    
</div>

<div class="remodal wpDataTables wdtRemodal removeColumn">

    <h1><?php _e('Remove Column','wpdatatable'); ?></h1>
    
        <table>
            <tr>
                <td>
                    <label><span><strong><?php _e('Delete column','wpdatatables'); ?></strong></span>:</label>
                </td>
                <td>
                    <div class="columnName">
                        <select id="wdtDeleteColumnSelect">
                            <?php foreach( $column_data as $column ){ ?>
                                <?php if( $column->orig_header == 'wdt_ID' ) { continue; } ?>
                                <option value="<?php echo $column->orig_header; ?>"><?php echo $column->display_header; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label><span><strong><?php _e('Confirm','wpdatatables'); ?></strong></span>:</label>
                </td>    
                <td class="wdtDeleteColumnConfirmation">
                    <label for="wdtDeleteColumnConfirm"><input type="checkbox" id="wdtDeleteColumnConfirm" /> <?php _e('Are you sure? There is no undo!','wpdatatables'); ?></label><br/>
                    <span class="error" style="display: none"><?php _e( 'Please confirm column deletion!' ); ?></span>
                </td>
            </tr>
        </table>
    
        <button class="btn"><?php _e( 'Cancel', 'wpdatatables' ); ?></button>
        <button class="btn" id="submitDeleteColumn"><?php _e( 'OK', 'wpdatatables' ); ?></button>
    
</div>