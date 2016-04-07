<table class="wdt_file_based_preview">
    <tr>
            <td style="width: 250px">
                    <label for="file_table_name"><span><strong><?php _e('Table name','wpdatatables');?></strong></span></label><br/>
                    <span class="description"><small><?php _e('What is the header of the table that will be visible to the site visitors?','wpdatatables');?></small></span>
            </td>
            <td>
                    <input id="file_table_name" type="text" value="<?php _e('New wpDataTable','wpdatatable');?>" />
            </td>
    </tr>
    <tr>
            <td>
                    <label for="table_columns"><span><strong><?php _e('Column names and types','wpdatatables');?></strong></span></label><br/>
                    <span class="description"><small><?php _e('Drag and drop to reorder columns','wpdatatables');?></small>.</span>

            </td>
            <td class="columnsContainer">
                
                <?php foreach( $headingsArray as $header ) { ?>
                <div class="columnBlock" data-header="<?php echo $header ?>">
                    <table>
                        <tr>
                            <td colspan="2" class="columnBlockHeader">
                                <button class="button removeColumnBlock" style=""><span class="dashicons dashicons-no-alt"></span></button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label><span><strong><?php _e('Column header','wpdatatables'); ?></strong></span>:</label>
                            </td>
                            <td>
                                <div class="columnName">
                                        <input type="text" value="<?php echo $header ?>" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label><span><strong><?php _e('Type','wpdatatables'); ?></strong></span>:</label>
                            </td>    
                            <td>
                                <div class="columnType">
                                    <select class="columnType" data-header="<?php echo $header?>">
                                        <?php foreach( $possibleColumnTypes as $columnTypeKey => $columnTypeName ) { ?>
                                        <option value="<?php echo $columnTypeKey ?>" <?php if($columnTypeKey == $columnTypeArray[$header]) { ?>selected="selected"<?php } ?> ><?php echo $columnTypeName ?></option>
                                        <?php }?>
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
                            <td><span><strong><?php _e('Data preview','wpdatatables'); ?></strong></span>:</label></td>
                            <td>
                                <table>
                                    <?php foreach( $namedDataArray as $row ){ ?>
                                    <tr>
                                        <td><?php echo $row[$header] ?></td>
                                    </tr>
                                    <?php } ?>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php } ?>                

            </td>
    </tr>
    <tr>
        <td>
        </td>
        <td>
                <button class="addColumnBlock button"><span class="dashicons dashicons-plus"></span></button>
        </td>
    </tr>
    
</table>    
