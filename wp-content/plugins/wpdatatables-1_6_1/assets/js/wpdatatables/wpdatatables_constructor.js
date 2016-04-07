var constructedTableData = {
	name: '',
	method: '',
	columnCount: 0,
	columns: []
};

var columnTypes = [
	{ name: 'input', value: 'One-line string' },
	{ name: 'memo', value: 'Multi-line string' },
	{ name: 'select', value: 'One-line selectbox' },
	{ name: 'multiselect', value: 'Multi-line selectbox' },
	{ name: 'integer', value: 'Integer' },
	{ name: 'float', value: 'Float' },
	{ name: 'date', value: 'Date' },
	{ name: 'link', value: 'URL link' },
	{ name: 'email', value: 'E-mail link' },
	{ name: 'image', value: 'Image' },
	{ name: 'file', value: 'Attachment' }
];

var defaultPostColumns = [
    'ID',
    'post_date',
    'post_date_gmt',
    'post_author',
    'post_title',
    'title_with_link_to_post',
    'thumbnail_with_link_to_post',
    'post_content',
    'post_content_limited_100_chars',
    'post_excerpt',
    'post_status',
    'comment_status',
    'ping_status',
    'post_password',
    'post_name',
    'to_ping',
    'pinged',
    'post_modified',
    'post_modified_gmt',
    'post_content_filtered',
    'post_parent',
    'guid',
    'menu_order',
    'post_type',
    'post_mime_type',
    'comment_count'
]

var metaKeysByPostTypes = {};

var aceEditor = null;

(function($){
	
	var custom_uploader;
	
	var defaultColumnData = {
		'name': wdt_constructor_strings.new_column_name,
		'type': 'input'
	}
	
        /**
         * Sortable handler for the columns
         */
        function applySortable(){
            $('td.columnsContainer').sortable({
            });
        }
        
	/**
	 * Next step handler
	 */
	$('#nextStep').click(function(e){
		e.preventDefault();
		e.stopImmediatePropagation();
		
		var $curStepBlock = $('div.constructorStep:visible:eq(0)');
		var curStep = $curStepBlock.data('step');
		
		switch(curStep){
			case 1:
				// Table input method selection
				if(!$('input[name="wpdatatables_type_input"]:checked').length) { return; }
				$curStepBlock.hide(300);
				$('#prevStep').show();
				var inputMethod = $('input[name="wpdatatables_type_input"]:checked').val();
				constructedTableData.method = inputMethod;
				switch(inputMethod){
					case 'manual':
						$('#table_columns').change();
						$('#table_name').change();
						$('div.step11').show(300);
						$('#nextStep').hide();
						$('#createAndEdit').show();
                                                applySortable();
					break;
					case 'file':
						$('div.step12').show(300);
					break;
					case 'wp':
						$('#wpdatatables_post_type').selecter();
                                                $('#wpdatatables_post_columns').selecter();
						$('div.step13').show(300);
					break;
					case 'mysql':
						$('#wpdatatables_mysql_tables').selecter();
                                                $('#wpdatatables_mysql_tables_columns').selecter();
						$('div.step14').show(300);
					break;
				}
			break;
                        case '1-2':
                                // Validation
                                if( !$('#wpdatatables_wizzard_fileupload').val() ){
                                    $('#fileupload_file_empty_error').html( wdt_constructor_strings.fileupload_empty_file );
                                    $('#fileupload_file_empty_error').show();
                                    return;
                                }else{
                                    $('#fileupload_file_empty_error').hide();
                                }
                                constructedTableData.file = $('#wpdatatables_wizzard_fileupload').val();
                                $curStepBlock.hide(300);
                                $('#wdtPreloadLayer').fadeIn(300)
                                generateAndPreviewFileTable();
                                $('#nextStep').hide();
                                $('#createAndEdit').show();
                        break;
                        case '1-3':
                                // Validation
                                if( !$('#wpdatatables_post_columns').val() ){
                                    $('#wpdatatables_post_columns').parent().addClass('error');
                                    return;
                                }else{
                                    $('#wpdatatables_post_columns').parent().removeClass('error');
                                }
                                // Go to preview of WP query
                                $curStepBlock.hide(300);
                                $('#wdtPreloadLayer').fadeIn(300)
                                generateAndPreviewWPQuery();
                        break;
                        case '1-4':
                                // Validation
                                if( !$('#wpdatatables_mysql_tables_columns').val() ){
                                    $('#wpdatatables_mysql_tables_columns').parent().addClass('error');
                                    return;
                                }else{
                                    $('#wpdatatables_mysql_tables_columns').parent().removeClass('error');
                                }
                                // Go to preview of MySQL query
                                $curStepBlock.hide(300);
                                $('#wdtPreloadLayer').fadeIn(300)
                                generateAndPreviewMySQLQuery();
                        break;
		}
	});
	
	/**
	 * Previous step handler
	 */
	$('#prevStep').click(function(e){
		e.preventDefault();
		e.stopImmediatePropagation();
		
		var $curStepBlock = $('div.constructorStep:visible:eq(0)');
		var curStep = $curStepBlock.data('step');

		if((curStep == '1-1') || (curStep == '1-2')){
			// Table input method selection
			$curStepBlock.hide(300);
			$('div.step1').show(300);
			$('#nextStep').show();
			$('#createAndEdit').hide();
		}
                
                if(curStep == '2-2'){
                    $curStepBlock.hide(300);
                    
                    $('div.step12').show(300);
                    $('#createAndEdit').hide();
                    $('#saveAndOpen').hide(300);
                    $('#nextStep').show(300);
                }
                
                if(curStep == '2-3'){
                    $curStepBlock.hide(300);
                    if( constructedTableData.method == 'wp' ){
                        $('div.step13').show(300);
                    }else if( constructedTableData.method == 'mysql' ){
                        $('div.step14').show(300);
                    }
                    $('#saveAndOpen').hide(300);
                    $('#nextStep').show(300);
                }
                
                if((curStep == '1-4') || (curStep == '1-3')){
                    $curStepBlock.hide(300);
                    $('div.step1').show(300);
                    $('#nextStep').show(300);                    
                }
                
	});
	
	/**
	 * Change column count for manual tables
	 */
	 $('#table_columns').change(function(e){
            e.preventDefault();
            newColumnCount = parseInt($(this).val());

            if(newColumnCount > constructedTableData.columnCount){
                // We need to add more columns
                for(var i = constructedTableData.columnCount; i < newColumnCount; i++){
                   $( 'td.columnsContainer' ).append( getColumnHtml( defaultColumnData ) );
                }
            }else if(newColumnCount < constructedTableData.columnCount){
                // We need to remove some columns
                for(var i = constructedTableData.columnCount-1; i > newColumnCount-1; i--){
                    $('td.columnsContainer div.columnBlock:eq('+i+')').remove();
                }
            }
            constructedTableData.columnCount = newColumnCount;
	 });
         
         /**
          * Add a column with "+"
          */
         $(document).on('click', 'button.addColumnBlock', function(e){
             e.preventDefault();
             e.stopImmediatePropagaion;
             $('#table_columns').val( parseInt($('#table_columns').val()) + 1 ).change();
         });
         
         /**
          * Remove a column with "X"
          */
         $(document).on('click','button.removeColumnBlock',function(e){
             e.preventDefault();
             e.stopImmediatePropagation();
             $(this).closest('div.columnBlock').remove();
             $('#table_columns').val( parseInt( $('#table_columns').val() ) - 1 );
             constructedTableData.columnCount = parseInt( $('#table_columns').val() );
         })
	 
	 /**
	  * Change table name for manual tables
	  */
	  $('#table_name').change(function(e){
	  	e.preventDefault()
		constructedTableData.name = $(this).val();
	  });
          
	 /**
	  * Change table name for tables imported from files
	  */
	  $(document).on('change','#file_table_name',function(e){
	  	e.preventDefault()
		constructedTableData.name = $(this).val();
	  });          
          
          /**
           * Show the "possible values" tagger for selectbox-type inputs
           */
          $(document).on('change','div.columnType select',function(e){
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

        /** 
	 * Get HTML for a column block
	 */
	 var getColumnHtml = function(columnData){
	 	var columnTemplate = $.templates("#columnBlockTmpl");
	 	var columnBlockHtml = columnTemplate.render(columnData, { columnTypes: columnTypes });
	 	$('.columnType select',columnBlockHtml).val(columnData.type);
	 	return columnBlockHtml;
	 }
	 
	 /** 
	  * Handler which creates the table and opens it in editor
	  */
 	 $('#createAndEdit').click(function(e){
 	 	e.preventDefault();
 	 	if(constructedTableData.method == 'manual'){
 	 		// Collect the metadata on columns
 	 		constructedTableData.columns = [];
 	 		$('div.columnBlock').each(function(){
 	 			constructedTableData.columns.push({
                                    name: $(this).find('div.columnName input').val(),
                                    type: $(this).find('div.columnType select').val(),
                                    possible_values: $(this).find('div.columnPossibleValues input').val(),
                                    default_value: $(this).find('div.columnDefaultValue select').length > 0 ? 
                                                        $(this).find('div.columnDefaultValue select').val() 
                                                        : $(this).find('div.columnDefaultValue input').val()
 	 			});
 	 		});
	 	 	$.ajax({
	 	 		url: ajaxurl,
	 	 		type: 'POST',
	 	 		data: {
                                    action: 'wpdatatables_create_and_open_in_editor',
                                    table_data: constructedTableData
	 	 		},
                                success: function(link){
                                    window.location = link;
                                }
	 	 	})
 	 	}
 	 	
 	 	if(constructedTableData.method == 'file'){
                        // Validation
                        var valid = true;
                        $('table.wdt_file_based_preview thead input.columnName').each(function(){
                            if($(this).val() == ''){
                                $(this).addClass('error');
                                valid = false;
                            }else{
                                $(this).removeClass('error');
                            }
                        });
                        if( valid ){
                            $('div.step22').hide(300);
                            $('#wdtPreloadLayer').fadeIn(300);
                            $('table.wdt_file_based_preview td.columnsContainer div.columnBlock').each(function(){

                                if( $(this).data('header') !== '' ){
                                    constructedTableData.columns.push({
                                        orig_header: typeof $(this).data('header') !== 'undefined' ? $(this).data('header') : '%%NEW_COLUMN%%',
                                        name: $(this).find('div.columnName input').val(),
                                        type: $(this).find('div.columnType select').val(),
                                        possible_values: $(this).find('div.columnPossibleValues input').val(),
                                        default_value: $(this).find('div.columnDefaultValue input').length > 0 
                                            ? $(this).find('div.columnDefaultValue input').val() : $(this).find('div.columnDefaultValue select').val()
                                    });
                                }

                            });
                            $('#file_table_name').change();

                            $('#wdtPreloadLayer').fadeIn(300)
                            readFileDataAndEditTable();
                        }
 	 	}
 	 	
 	 });
 	 
        /**
         * Show Possible Values editor for the selectbox-inputs
         */ 
        
 	 
 	/**
 	 * Uploader
 	 */
	$('#wpdatatables_wizzard_fileupload_button').click(function(e) {
 
        e.preventDefault();
 
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        // Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: wdt_constructor_strings.select_excel_csv,
            button: {
                text: wdt_constructor_strings.choose_file
            },
            multiple: false,
            library: {
            	type: 'application/vnd.ms-excel,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            }
        });
 
        // When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#wpdatatables_wizzard_fileupload').val(attachment.url);
        });
 
        //Open the uploader dialog
        custom_uploader.open();
 
    });
    
    
    /**
     * Add post types from the selecter
     */
     $('#wpdatatables_post_type').change(function(e){
     	e.preventDefault;
     	constructedTableData.post_types = $(this).val();
        
        var availablePostColumns = [];
        
        for(var i in constructedTableData.post_types){
            for(var j in defaultPostColumns){
                availablePostColumns.push(constructedTableData.post_types[i]+'.'+defaultPostColumns[j]);
            }
            if( typeof wdt_post_meta_by_post_types[constructedTableData.post_types[i]] !== 'undefined'){
                for(var j in wdt_post_meta_by_post_types[constructedTableData.post_types[i]]){
                     availablePostColumns.push(constructedTableData.post_types[i]+'.meta.'+wdt_post_meta_by_post_types[constructedTableData.post_types[i]][j]);
                }
            }
            if( typeof wdt_taxonomies_by_post_types[constructedTableData.post_types[i]] !== 'undefined'){
                for(var j in wdt_taxonomies_by_post_types[constructedTableData.post_types[i]]){
                     availablePostColumns.push(constructedTableData.post_types[i]+'.taxonomy.'+wdt_taxonomies_by_post_types[constructedTableData.post_types[i]][j]);
                }
            }
        }
        
        if( constructedTableData.post_types
                && ( constructedTableData.post_types.length > 1 ) 
                && ( constructedTableData.post_types.indexOf('all') !== -1 ) ){
            $(this).val('all').change().selecter('update');
        }
        
        if(constructedTableData.post_types && constructedTableData.post_types.length > 1){
            $('.wdt_handle_post_types').fadeIn(300);
            $('#wpdatatables_posts_relations_join').attr('checked','checked').change();
        }else{
            $('.wdt_handle_post_types').fadeOut(300).removeAttr('checked').change();
            $('.wdt_define_relations').fadeOut(300);
        }
        
        var postColumnTemplate = $.templates("#postColumnTemplate");
        var postColumnHtml = postColumnTemplate.render({ availablePostColumns: availablePostColumns });
        $('#wpdatatables_post_columns').html(postColumnHtml);
        $('#wpdatatables_post_columns').selecter('update');
        
     });
     
     /**
      * Helper function to return array of available columns by post type
      */
     function wdt_get_columns_by_post_type(post_type, include_post_type_name, include_meta_and_tax){
         var arr = [];
         if (typeof include_post_type_name == 'undefined'){
             include_post_type_name = false;
         }
         if (typeof include_meta_and_tax == 'undefined'){
             include_meta_and_tax = true
         }
         var prefix = include_post_type_name ? post_type+'.' : '';
        for(var j in defaultPostColumns){
            arr.push(prefix+defaultPostColumns[j]);
        }
        if( include_meta_and_tax ){
            if( typeof wdt_post_meta_by_post_types[post_type] !== 'undefined' ){
                for(var j in wdt_post_meta_by_post_types[post_type]){
                     arr.push(prefix+'meta.'+wdt_post_meta_by_post_types[post_type][j]);
                }
            }
            if( typeof wdt_taxonomies_by_post_types[post_type] !== 'undefined'){
                for(var j in wdt_taxonomies_by_post_types[post_type]){
                    arr.push(prefix+'taxonomy.'+wdt_taxonomies_by_post_types[post_type][j]);
                }
            }
        }
        return arr;
     }
     
     /**
      * Add the columns to table
      */
     $('#wpdatatables_post_columns').change(function(e){
     	e.preventDefault;
        constructedTableData.post_columns = $(this).val();
        
        $('.wdt_define_relations div.relationsContainer').html('');
        
        // Generate HTML block for relations constructor
        for(var i in constructedTableData.post_types){
            var post_type_block = { post_type: constructedTableData.post_types[i], post_type_columns: [], other_post_type_columns: [] };
            post_type_block.post_type_columns = wdt_get_columns_by_post_type(constructedTableData.post_types[i], false, false);
            for(var j in constructedTableData.post_types){
                if(constructedTableData.post_types[i] == constructedTableData.post_types[j]){
                    continue;
                }
                post_type_block.other_post_type_columns = post_type_block.other_post_type_columns.concat(wdt_get_columns_by_post_type(constructedTableData.post_types[j],true, true));
            }
            var relationBlockTemplate = $.templates("#relationBlockTemplate");
            var relationBlockHtml = relationBlockTemplate.render(post_type_block);
            $('.wdt_define_relations div.relationsContainer').append(relationBlockHtml);
        }
        $('.wdt_define_relations div.relationsContainer select').selecter();
     });
     
     /**
      * Set the WP-based wpDataTable to use a view to store the query
      */
     $('#wpdatatables_create_view_use_serverside').change(function(e){
         e.preventDefault();
         e.stopImmediatePropagation();
         constructedTableData.prepare_view = $(this).is(':checked') ? '1' : '0';
     });
     
     /**
      * Show the relations constructor when needed
      */
     $('input[name="wpdatatables_posts_relations"]').change(function(e){
         e.preventDefault();
         if($('#wpdatatables_posts_relations_join').is(':checked')){
             $('.wdt_define_relations').fadeIn(300);
         }else{
             $('.wdt_define_relations').fadeOut(300);
         }
     });
     
     /**
      * Add a "WHERE" condition to the WP POSTS based table
      */
     $('#wdt_posts_add_where_condition').click(function(e){
        e.preventDefault();
        
        // Generate HTML block for relations constructor
        var where_block = { post_type_columns: [] };
        for(var i in constructedTableData.post_types){
            where_block.post_type_columns = where_block.post_type_columns.concat( wdt_get_columns_by_post_type( constructedTableData.post_types[i], true ) );
        }
        var whereBlockTemplate = $.templates("#whereConditionTemplate");
        var whereBlockHtml = whereBlockTemplate.render(where_block);
        $('.wdt_define_conditions div.conditionsContainer').append(whereBlockHtml);
        $('.wdt_define_conditions div.conditionsContainer select').selecter();

     });
     
     /**
      * Delete a "WHERE" condition
      */
     $(document).on('click','button.deleteConditionPosts',function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        $(this).closest('div.post_where_blocks').remove();
     });
     
     /**
      * Add a grouping rule to the WP POSTS based table
      */
     $('#wdt_posts_add_grouping_rule').click(function(e){
         e.preventDefault();
         
         // Generate HTML block for the grouping rule constructor
        var grouping_rule_block = { post_type_columns: [] };
        
        grouping_rule_block.post_type_columns = $('#wpdatatables_post_columns').val();
        
        var groupingRuleBlockTemplate = $.templates("#groupingRuleTemplate");
        var groupingRuleHtml = groupingRuleBlockTemplate.render(grouping_rule_block);
        
        $('.wdt_define_grouping div.groupingContainer').append(groupingRuleHtml);
        $('.wdt_define_grouping div.groupingContainer select').selecter();
         
     });
     
     /**
      * Delete a grouping rule
      */
     $(document).on('click','button.deleteGroupingRulePosts',function(e){
         e.preventDefault();
         $(this).closest('div.post_grouping_rule_blocks').remove();
     })
     
     /**
      * Preview a table based on the file
      */
    function generateAndPreviewFileTable(){
        
        $.ajax({
            url: ajaxurl,
            data: {
                action: 'wpdatatables_preview_file_table',
                table_data: constructedTableData
            },
            type: 'post',
            dataType: 'json',
            success: function(data){
                if ( data.result == 'error' ) {
                    $('#wdtPreloadLayer').hide();
                    $('div.step12').show(300);
                    $('#createAndEdit').hide();
                    $('#nextStep').show();
                    $('#fileupload_file_empty_error').html( data.message );
                    $('#fileupload_file_empty_error').show();
                } else {
                    $('#fileupload_file_empty_error').hide();
                    $('#wdtPreloadLayer').hide();
                    $('div.previewFileTable').html(data.message);
                    $('#table_columns').val(parseInt($('div.columnBlock').length));
                    constructedTableData.columnCount = parseInt($('div.columnBlock').length);
                    $('div.step22').show(300);
                    applySortable();
                }
                
            }
        })        
        
    };
     
     /**
      * Generate a query to WP database and preview it
      */
    function generateAndPreviewWPQuery(){
        
        constructedTableData.handle_post_types = $('#wpdatatables_posts_relations_join').is(':checked') ? 'join' : 'union';
        if(constructedTableData.handle_post_types == 'join'){
            constructedTableData.join_rules = [];
            
            $('div.relationsContainer div.post_blocks').each(function(){
                var join_rule = {};
                join_rule.initiator_post_type = $(this).find('select.relationInitiatorColumn').data('post_type');
                join_rule.initiator_column = $(this).find('select.relationInitiatorColumn').val();
                join_rule.connected_column = $(this).find('select.relationConnectedColumn').val();
                join_rule.type = $(this).find('input[type="checkbox"]').is(':checked') ? 'inner' : 'left';
                constructedTableData.join_rules.push(join_rule);
            });
            
        }
        
        constructedTableData.where_conditions = [];
        
        $('div.conditionsContainer div.post_where_blocks').each(function(){
            var where_condition = {};
            where_condition.column = $(this).find('select.whereConditionColumn').val();
            where_condition.operator = $(this).find('select.whereOperator').val();
            where_condition.value = $(this).find('input[type="text"]').val();
            constructedTableData.where_conditions.push(where_condition);
        });
        
        constructedTableData.grouping_rules = [];
        
        $('div.groupingContainer div.post_grouping_rule_blocks select').each(function(){
            constructedTableData.grouping_rules.push( $(this).val() ); 
        });
        
        $.ajax({
            url: ajaxurl,
            data: {
                action: 'wpdatatables_generate_wp_based_query',
                table_data: constructedTableData
            },
            type: 'post',
            dataType: 'json',
            success: function(data){
                aceEditor.setValue(data.query);
                constructedTableData.query = data.query;
                $('div.previewWPTable').html(data.preview);
                $('#wdtPreloadLayer').fadeOut(300)
                $('div.step23').show(300);
                $('#nextStep').hide(300);
                $('#saveAndOpen').show(300);
            }
        })
        
    }
    
    $('button.refreshWpQuery').click(function(e){
        e.preventDefault();
        $('#wdtPreloadLayer').fadeIn(300)
        $.ajax({
            url: ajaxurl,
            data: {
                action: 'wpdatatables_refresh_wp_query_preview',
                query: aceEditor.getValue()
            },
            type: 'post',
            success: function(data){
                $('div.previewWPTable').html(data);
                $('#wdtPreloadLayer').fadeOut(300)
            }
        })
        
    });
    
     /**
      * Generate a query to MySQL database and preview it
      */
    function generateAndPreviewMySQLQuery(){
        
        constructedTableData.join_rules = [];
        constructedTableData.where_conditions = [];
        constructedTableData.grouping_rules = [];
        
        
        /**
         * Join rules
         */
        $('div.mysqlRelationsContainer div.mysql_table_blocks').each(function(){
            var join_rule = {};
            join_rule.initiator_table = $(this).find('select.relationInitiatorColumn').data('table');
            join_rule.initiator_column = $(this).find('select.relationInitiatorColumn').val();
            join_rule.connected_column = $(this).find('select.relationConnectedColumn').val();
            join_rule.type = $(this).find('input[type="checkbox"]').is(':checked') ? 'inner' : 'left';
            constructedTableData.join_rules.push( join_rule );
        });
        
        /**
         * Where block
         */
        $('div.mysqlConditionsContainer div.post_where_blocks').each(function(){
            var where_condition = {};
            where_condition.column = $(this).find('select.whereConditionColumn').val();
            where_condition.operator = $(this).find('select.whereOperator').val();
            where_condition.value = $(this).find('input[type="text"]').val();
            constructedTableData.where_conditions.push(where_condition);
        });
        
        /**
         * Grouping rules
         */
        $('div.mysqlGroupingContainer div.post_grouping_rule_blocks select').each(function(){
            constructedTableData.grouping_rules.push( $(this).val() ); 
        });
        
        $.ajax({
            url: ajaxurl,
            data: {
                action: 'wpdatatables_generate_mysql_based_query',
                table_data: constructedTableData
            },
            type: 'post',
            dataType: 'json',
            success: function(data){
                aceEditor.setValue(data.query);
                constructedTableData.query = data.query;
                $('div.previewWPTable').html(data.preview);
                $('#wdtPreloadLayer').fadeOut(300)
                $('div.step23').show(300);
                $('#nextStep').hide(300);
                $('#saveAndOpen').show(300);
            }
        })        
        
    }    
    
    
    /**
     * Save the current query and open in wpDataTable configurator
     */
    $('#saveAndOpen').click(function(e){
        e.preventDefault();
        $('#wdtPreloadLayer').fadeIn(300);
        $.ajax({
            url: ajaxurl,
            data: {
                action: 'wpdatatables_constructor_generate_wdt',
                table_data: constructedTableData
            },
            type: 'post',
            success: function(data){
                window.location = data;
            }
        })
        
    })
    
    /**
     * Get the column list for selected tables
     */
    $('#wpdatatables_mysql_tables').change(function(e){
        e.preventDefault();
        var tables = $(this).val();
        var availableTableColumns = [];
        
        if( ( tables != null ) && ( tables.length > 1 ) ){
            $('.wdt_define_mysql_relations').show(300);
        }else{
            $('.wdt_define_mysql_relations').hide(300);
        }
        
        $.ajax({
            url: ajaxurl,
            data: {
                action: 'wpdatatables_constructor_get_mysql_table_columns',
                tables: tables
            },
            type: 'post',
            dataType: 'json',
            success: function(availableTableColumns){
                
                var mysqlTableColumnTemplate = $.templates("#mysqlTableColumnTemplate");
                var mysqlTableColumnHtml = mysqlTableColumnTemplate.render({ availableTableColumns: availableTableColumns.all_columns });

                $('#wpdatatables_mysql_tables_columns').html(mysqlTableColumnHtml);
                $('#wpdatatables_mysql_tables_columns').selecter('update');
                
                $('.wdt_define_mysql_relations div.mysqlRelationsContainer').html('');

                // Generate HTML block for relations constructor
                for( var i in availableTableColumns.sorted_columns ){
                    var mysql_table_block = { table: i, columns: [], other_table_columns: [] };
                    for( var j in availableTableColumns.sorted_columns ){
                        if( i == j ){ 
                            for( var k in availableTableColumns.sorted_columns[i] ){
                                mysql_table_block.columns.push( availableTableColumns.sorted_columns[i][k].replace( i+'.', '' ) );
                            }
                            continue; 
                        }
                        for( var k in availableTableColumns.sorted_columns[j] ){
                            mysql_table_block.other_table_columns.push( availableTableColumns.sorted_columns[j][k] );
                        }
                    }
                    var relationBlockTemplate = $.templates("#mysqlRelationBlockTemplate");
                    var relationBlockHtml = relationBlockTemplate.render( mysql_table_block );
                    $('.wdt_define_mysql_relations div.mysqlRelationsContainer').append(relationBlockHtml);
                    $('.wdt_define_mysql_relations div.mysqlRelationsContainer select').selecter();
                }
                
            }
        })
        
    });
    
    /**
     * Generate the Excel/CSV based table and go to editor
     */
    function readFileDataAndEditTable(){
        $.ajax({
            url: ajaxurl,
            data: {
                action: 'wpdatatables_constructor_read_file_data',
                table_data: constructedTableData
            },
            type: 'post',
            success: function(link){
                window.location = link;
            }
        })
    }
    
    /**
     * Add the selected MySQL columns to the constructed table data
     */
    $('#wpdatatables_mysql_tables_columns').change(function(e){
        e.preventDefault();
        constructedTableData.mysql_columns = $(this).val();
    });
    
    /**
     * Add "Where" block for MySQL based tables
     */
    $('#wdt_mysql_add_where_condition').click(function(e){
        e.preventDefault();
        
        // Generate HTML block for relations constructor
        var where_block = { post_type_columns: $("#wpdatatables_mysql_tables_columns option").map(function() { return $(this).val(); }).toArray() };
        
        var whereBlockTemplate = $.templates("#whereConditionTemplate");
        var whereBlockHtml = whereBlockTemplate.render(where_block);
        $('.wdt_define_mysql_conditions div.mysqlConditionsContainer').append(whereBlockHtml);
        $('.wdt_define_mysql_conditions div.mysqlConditionsContainer select').selecter();
        
    })
    
    /**
     * Add a grouping rule for MySQL based tables
     */
    $('#wdt_mysql_add_grouping_rule').click(function(e){
        e.preventDefault();

         // Generate HTML block for the grouping rule constructor
        var grouping_rule_block = { post_type_columns: [] };
        
        grouping_rule_block.post_type_columns = $('#wpdatatables_mysql_tables_columns').val();
        
        var groupingRuleBlockTemplate = $.templates("#groupingRuleTemplate");
        var groupingRuleHtml = groupingRuleBlockTemplate.render(grouping_rule_block);
        
        $('.wdt_define_mysql_grouping div.mysqlGroupingContainer').append(groupingRuleHtml);
        $('.wdt_define_mysql_grouping div.mysqlGroupingContainer select').selecter();
        
    });
    
    /**
     * Apply syntax highlighter
     */
    aceEditor = ace.edit('previewWPQuery');
    aceEditor.getSession().setMode("ace/mode/sql");
    aceEditor.setTheme("ace/theme/idle_fingers");    
    
    
})(jQuery);