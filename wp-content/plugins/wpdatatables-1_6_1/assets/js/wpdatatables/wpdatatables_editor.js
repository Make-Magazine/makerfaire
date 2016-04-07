(function($){
    
    // Init Add Column remodal
    $('div.addColumn').remodal({
                    type: 'inline',
                    preloader: false,
                    modal: true
                });
                
    // Init Remove Column remodal
    $('div.removeColumn').remodal({
                    type: 'inline',
                    preloader: false,
                    modal: true
                });
            
    /**
     * Open 'Add column' modal
     */
    $('button.addColumn').click(function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        $.remodal.lookup[$('div.addColumn').data('remodal')].open();
    })

    /**
     * Open 'Remove column' modal
     */
    $('button.removeColumn').click(function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        $.remodal.lookup[$('div.removeColumn').data('remodal')].open();
    })
    
    /**
     * Change column type for a new column
     */
    $('div.addColumn select.columnType').change(function(e){
              var $columnBlock = $(this).closest('div.columnBlock');
              if( ( $(this).val() == 'select' ) || ( $(this).val() == 'multiselect' ) ){
                  $columnBlock.find('tr.columnPossibleValuesBlock').show();
                  if( $(this).closest('div.columnBlock').find('tr.columnPossibleValuesBlock div.tagsinput').length > 0 ){ return; }
                  $columnBlock.find('div.columnDefaultValue').find('input').replaceWith('<select><option value=""></option></select>');
                  $columnBlock.find('div.columnPossibleValues input').tagsInput({
                      defaultText: '+',
                      width: 195,
                      height: 50,
                      delimiter: [',',';','|'],
                      onAddTag: function(tag){
                         $(this).closest('div.columnBlock').find('div.columnDefaultValue select').append('<option value="'+tag+'">'+tag+'</option>');
                      },
                      onRemoveTag: function(tag){
                         $(this).closest('div.columnBlock').find('div.columnDefaultValue select option[value="'+tag+'"]').remove();
                      }
                  }).hide();
              }else{
                  $(this).closest('div.columnBlock').find('tr.columnPossibleValuesBlock').hide();
                  $(this).closest('div.columnBlock').find('div.columnDefaultValue').find('select').replaceWith('<input type="text" />');
              }
    });
    
    $('#submitNewColumn').click(function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        
        // Validation
        var valid = true;
        if( $('div.addColumn div.columnName input').val() == '' ){
            valid = false;
            $('div.addColumn div.columnName span.error').show(200);
        }else{
            $('div.addColumn div.columnName span.error').hide(200);
        }
        
        if( !valid ){
            return;
        }
        
        var newColumnData = {
            name: $('div.addColumn div.columnName input').val(),
            type: $('div.addColumn div.columnType select').val(),
            insert_after: $('div.insertAfter select').val(),
            possible_values: $('div.columnPossibleValues input').val(),
            default_value: $('div.addColumn div.columnDefaultValue select').length > 0 ? 
                $('div.addColumn div.columnDefaultValue select').val() : $('div.addColumn div.columnDefaultValue input').val(),
            fill_default: $('div.addColumn input.columnFillDefault').is(':checked') ? 1 : 0
        }
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wpdatatables_add_new_manual_column',
                table_id: $('#wpdatatables_table_id').val(),
                column_data: newColumnData
            },
            success: function(){
                window.location.reload( true );
            }
        });
        
    })
    
    /**
     * Close "Add new column" popup
     */
    $('#cancelNewColumn').click(function(e){
        e.preventDefault();
        $.remodal.lookup[$('div.addColumn').data('remodal')].close();
    })
    
    /**
     * Delete a column
     */
    $('#submitDeleteColumn').click(function(e){
        e.preventDefault();
        // Validation
        var valid = true;
        if( $('#wdtDeleteColumnConfirm').is(':checked') == false){
            valid = false;
            $('td.wdtDeleteColumnConfirmation span.error').show(200);
        }else{
            $('td.wdtDeleteColumnConfirmation span.error').hide(200);
        }
        if( !valid ){
            return;
        }
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wpdatatables_delete_manual_column',
                table_id: $('#wpdatatables_table_id').val(),
                column_name: $('#wdtDeleteColumnSelect').val()
            },
            success: function(){
                window.location.reload( true );
            }
        });        
        
    });
    
    
})(jQuery);