/** New JS controller for wpDataTables **/

var wpDataTables = {};
var wpDataTableDialogs = {};
var wpDataTablesSelRows = {};
var wpDataTablesFunctions = {};
var wpDataTablesUpdatingFlags = {};
var wpDataTablesResponsiveHelpers = {};
var wdtBreakpointDefinition = {
    tablet: 1024,
    phone: 480
};
var wdtCustomUploader = null;

(function ($) {
    $(function () {

        $('table.wpDataTable').each(function () {
            var tableDescription = $.parseJSON($('#' + $(this).data('described-by')).val());

            // Parse the DataTable init options
            var dataTableOptions = tableDescription.dataTableParams;

            // Responsive-mode related stuff
            if (tableDescription.responsive) {
                wpDataTablesResponsiveHelpers[tableDescription.tableId] = false;
                dataTableOptions.fnPreDrawCallback = function () {
                    if (!wpDataTablesResponsiveHelpers[tableDescription.tableId]) {
                        if (typeof tableDescription.mobileWidth !== 'undefined') {
                            wdtBreakpointDefinition.phone = parseInt(tableDescription.mobileWidth);
                        }
                        if (typeof tableDescription.tabletWidth !== 'undefined') {
                            wdtBreakpointDefinition.tablet = parseInt(tableDescription.tabletWidth);
                        }
                        wpDataTablesResponsiveHelpers[tableDescription.tableId] = new ResponsiveDatatablesHelper($(tableDescription.selector).dataTable(), wdtBreakpointDefinition);
                    }
                    wdtAddOverlay('#' + tableDescription.tableId);
                }
                dataTableOptions.fnRowCallback = function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    wpDataTablesResponsiveHelpers[tableDescription.tableId].createExpandIcon(nRow);
                }
                if (!tableDescription.editable) {
                    dataTableOptions.fnDrawCallback = function () {
                        wpDataTablesResponsiveHelpers[tableDescription.tableId].respond();
                        wdtRemoveOverlay('#' + tableDescription.tableId);
                    }
                }
            } else {
                dataTableOptions.fnPreDrawCallback = function () {
                    wdtAddOverlay('#' + tableDescription.tableId);
                }
            }

            if (tableDescription.editable) {

                if (typeof wpDataTablesFunctions[tableDescription.tableId] === 'undefined') {
                    wpDataTablesFunctions[tableDescription.tableId] = {};
                }

                wpDataTablesSelRows[tableDescription.tableId] = -1;
                dataTableOptions.fnDrawCallback = function () {
                    wdtRemoveOverlay('#' + tableDescription.tableId);
                    if (tableDescription.responsive) {
                        wpDataTablesResponsiveHelpers[tableDescription.tableId].respond();
                    }

                    if (wpDataTablesSelRows[tableDescription.tableId] == -2) {
                        // -2 means select first row on "next" page
                        var sel_row_index = wpDataTables[tableDescription.tableId].fnSettings()._iDisplayLength - 1;
                        $(tableDescription.selector + ' > tbody > tr').removeClass('selected');
                        wpDataTablesSelRows[tableDescription.tableId] = wpDataTables[tableDescription.tableId].fnGetPosition($(tableDescription.selector + ' > tbody > tr:eq(' + sel_row_index + ')').get(0));
                        $(tableDescription.selector + ' > tbody > tr:eq(' + sel_row_index + ')').addClass('selected');
                    } else if (wpDataTablesSelRows[tableDescription.tableId] == -3) {
                        var sel_row_index = 0;
                        $(tableDescription.selector + ' > tbody > tr').removeClass('selected');
                        wpDataTablesSelRows[tableDescription.tableId] = wpDataTables[tableDescription.tableId].fnGetPosition($(tableDescription.selector + ' > tbody > tr:eq(' + sel_row_index + ')').get(0));
                        $(tableDescription.selector + ' > tbody > tr:eq(' + sel_row_index + ')').addClass('selected');
                    }

                    if ($(tableDescription.selector + '_edit_dialog').is(':visible')) {
                        var data = wpDataTables[tableDescription.tableId].fnGetData(wpDataTablesSelRows[tableDescription.tableId]);
                        wpDataTablesFunctions[tableDescription.tableId].applyData(data);
                    }
                    $(tableDescription.selector + '_edit_dialog').parent().removeClass('overlayed');

                    wpDataTablesUpdatingFlags[tableDescription.tableId] = false;
                }

                // Data apply function for editable tables
                wpDataTablesFunctions[tableDescription.tableId].applyData = function (data) {
                    $(data).each(function (index, el) {
                        if (el) {
                            var val = el.toString();
                        } else {
                            var val = '';
                        }
                        if (val.indexOf('span') != -1) {
                            val = val.replace(/<span>/g, '').replace(/<\/span>/g, '');
                        }
                        if (val.indexOf('<br/>') != -1) {
                            val = val.replace(/<br\/>/g, "\n");
                        }
                        var $inputElement = $('#' + tableDescription.tableId + '_edit_dialog .editDialogInput:eq(' + index + ')');
                        var inputElementType = $inputElement.data('input_type');
                        if (inputElementType == 'multi-selectbox') {
                            $inputElement.find('option').removeAttr('selected');
                            var values = val.split(', ');
                            $inputElement.val(values);
                            $inputElement.selecter('refresh');
                        } else {
                            if (inputElementType == 'attachment') {
                                if (val != '') {
                                    if ($(val).attr('src') != undefined) {
                                        val = $(val).attr('src');
                                    } else if ($(val).attr('href') != undefined) {
                                        val = $(val).attr('href');
                                    }

                                    $inputElement.parent().parent().find('div.files').html('<p>' + val.split('/').pop() + ' [<a href="#" data-key="' + $inputElement.attr('id') + '" class="wdtdeleteFile">' + wpdatatables_frontend_strings.detach_file + '</a>]</p>')
                                } else {
                                    $inputElement.parent().parent().find('div.files').html('');
                                }
                            } else {
                                if (val.indexOf('<a ') != -1) {
                                    if ($.inArray(inputElementType, ['text', 'link', 'textarea', 'email']) !== -1) {
                                        $link = $(val);
                                        if ($link.attr('href').indexOf($link.html()) === -1) {
                                            val = $link.attr('href').replace('mailto:', '') + '||' + $link.html();
                                        } else {
                                            val = $link.html();
                                        }
                                    } else {
                                        val = $(val).html();
                                    }
                                }
                            }
                            if( $inputElement.data('column_type') =='int' ){
                                val = val.replace(/\,/g, '').replace(/\./g,'');
                            }
                            $inputElement.val(val).css('border', '');
                            if (inputElementType == 'selectbox') {
                                $inputElement.val(val);
                                $inputElement.selecter('destroy').selecter();
                            }
                        }
                    });
                }

                // Saving of the table data for frontend 
                wpDataTablesFunctions[tableDescription.tableId].saveTableData = function (forceRedraw, closeDialog) {
                    if (typeof (forceRedraw) === undefined) {
                        forceRedraw = false;
                    }
                    if (typeof (closeDialog) === undefined) {
                        closeDialog = false;
                    }
                    $(tableDescription.selector + '_edit_dialog').parent().addClass('overlayed');
                    wpDataTablesUpdatingFlags[tableDescription.tableId] = true;
                    var formdata = {table_id: tableDescription.tableWpId};
                    var aoData = [];
                    var valid = true;
                    var validation_message = '';
                    if ( tableDescription.popoverTools ) {
                        $('.wpDataTablesPopover.editTools').hide();
                    }
                    $(tableDescription.selector + '_edit_dialog .editDialogInput').each(function () {
                        // validation
                        if ($(this).data('input_type') == 'email') {
                            if ($(this).val() != '') {
                                var field_valid = wdtValidateEmail($(this).val());
                                if (!field_valid) {
                                    valid = false;
                                    $(this).addClass('error');
                                    validation_message += '<li>' + wpdatatables_frontend_strings.invalid_email + ' ' + $(this).data('column_header') + '</li>'
                                } else {
                                    $(this).removeClass('error')
                                }
                            }
                        } else if ($(this).data('input_type') == 'link') {
                            if ($(this).val() != '') {
                                var field_valid = wdtValidateURL($(this).val());
                                if (!field_valid) {
                                    valid = false;
                                    $(this).addClass('error');
                                    validation_message += '<li>' + wpdatatables_frontend_strings.invalid_link + ' ' + $(this).data('column_header') + '</li>'
                                } else {
                                    $(this).removeClass('error');
                                }
                            }
                        }
                        if ($(this).hasClass('mandatory')) {
                            if ($(this).val() == '') {
                                $(this).addClass('error');
                                valid = false;
                                validation_message += '<li>' + $(this).data('column_header') + ' ' + wpdatatables_frontend_strings.cannot_be_empty + '</li>'
                            } else {
                                if (valid) {
                                    $(this).removeClass('error');
                                }
                            }
                        }
                        if ($(this).hasClass('datepicker')) {
                            formdata[$(this).data('key')] = $.datepicker.formatDate(tableDescription.datepickFormat, $.datepicker.parseDate(tableDescription.datepickFormat, $(this).val()));
                        } else if ($(this).data('input_type') == 'multi-selectbox') {
                            if ($(this).val()) {
                                formdata[$(this).data('key')] = $(this).val().join(', ');
                            }
                        } else {
                            formdata[$(this).data('key')] = $(this).val();
                        }
                        aoData.push(formdata[$(this).data('key')]);
                    });
                    if (!valid) {
                        $(tableDescription.selector + '_edit_dialog').parent().removeClass('overlayed');
                        $(tableDescription.selector + '_edit_dialog div.data_validation_notify').html('<ul>' + validation_message + '</ul>').fadeIn('300');
                        setTimeout(function () {
                            $(tableDescription.selector + '_edit_dialog div.data_validation_notify').fadeOut('300');
                        }, 5000);
                        return false;
                    }
                    wpDataTablesUpdatingFlags[tableDescription.tableId] = true;
                    $.ajax({
                        url: tableDescription.adminAjaxBaseUrl,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'wdt_save_table_frontend',
                            formdata: formdata
                        },
                        success: function (return_data) {
                            $(tableDescription.selector + '_edit_dialog').parent().removeClass('overlayed');
                            if (return_data.error == '') {
                                var insert_id = return_data.success;
                                if (return_data.is_new) {
                                    forceRedraw = true;
                                }
                                if (insert_id) {
                                    $(tableDescription.selector + '_edit_dialog tr.idRow .editDialogInput').val(insert_id);
                                    if (forceRedraw) {
                                        wpDataTables[tableDescription.tableId].fnDraw(false);
                                        $('.edit_table[aria-controls="' + tableDescription.tableId + '"]').addClass('disabled');
                                    }
                                } else {
                                    wpDataTables[tableDescription.tableId].fnDraw(false);
                                    $('.edit_table[aria-controls="' + tableDescription.tableId + '"]').addClass('disabled');
                                }
                                $(tableDescription.selector + '_edit_dialog div.data_saved_notify').fadeIn('300');
                                setTimeout(function () {
                                    $(tableDescription.selector + '_edit_dialog div.data_saved_notify').fadeOut('300');
                                }, 5000);
                                if (!return_data.is_new && $(tableDescription.selector + ' > tbody > tr.selected').length) {
                                    var cursor = wpDataTables[tableDescription.tableId].fnGetPosition($(tableDescription.selector + ' > tbody > tr.selected').get(0));
                                    wpDataTables[tableDescription.tableId].fnSettings().aoData[cursor]._aData = aoData;
                                }
                                if (closeDialog) {
                                    $.remodal.lookup[wpDataTableDialogs[tableDescription.tableId].data('remodal')].close();
                                }
                            } else {
                                $(tableDescription.selector + '_edit_dialog div.data_validation_notify').html(return_data.error).fadeIn('300');
                                setTimeout(function () {
                                    $(tableDescription.selector + '_edit_dialog div.data_validation_notify').fadeOut('300');
                                }, 5000);
                            }
                        }
                    });
                    return true;
                }
            }

            // Remove overlay if the table is not responsive nor editable
            if (!tableDescription.responsive
                    && !tableDescription.editable) {
                dataTableOptions.fnDrawCallback = function () {
                    wdtRemoveOverlay('#' + tableDescription.tableId);
                }
            }
            
            // Apply the selecter to show entries
            dataTableOptions.fnInitComplete = function( oSettings, json ) {
                jQuery('#' + tableDescription.tableId + '_length select').selecter();
            }
            // Init the DataTable itself
            wpDataTables[tableDescription.tableId] = $(tableDescription.selector).dataTable(dataTableOptions);

            // Enable auto-refresh if defined
            if( tableDescription.serverSide ){
                if( parseInt( tableDescription.autoRefreshInterval ) > 0 ){
                    setInterval( function(){
                            wpDataTables[tableDescription.tableId].fnDraw(false)
                        },
                        parseInt( tableDescription.autoRefreshInterval) * 1000
                    );
                }
            }

            // Add the draw callback
            wpDataTables[tableDescription.tableId].addOnDrawCallback = function (callback) {
                if (typeof callback !== 'function') {
                    return;
                }

                var index = wpDataTables[tableDescription.tableId].fnSettings().aoDrawCallback.length + 1;

                wpDataTables[tableDescription.tableId].fnSettings().aoDrawCallback.push({
                    sName: 'user_callback_' + index,
                    fn: callback
                });

            }
            
            // Sum row callback
            if( tableDescription.has_sum_columns ){
                if( tableDescription.serverSide ){
                // Case with server-side table
                    wpDataTables[tableDescription.tableId].fnSettings().aoDrawCallback.push({
                        sName: 'updateSumRow',
                        fn: function( oSettings ){
                            var api = oSettings.oInstance.api();
                            for( var column_name in api.ajax.json().sum_columns_values ){
                                $( '#'+tableDescription.tableId+ ' tfoot tr.sum_row td.sum_cell[data-column_header="'+column_name+'"]' ).html( '&#8721; = ' + api.ajax.json().sum_columns_values[column_name] );
                            }
                        }
                    });
                }else{
                // Case with client-side table
                    wpDataTables[tableDescription.tableId].fnSettings().aoDrawCallback.push({
                        sName: 'updateSumRow',
                        fn: function( oSettings ){
                            var api = oSettings.oInstance.api();
                            for( var i in tableDescription.sum_columns ){
                                var columnData = api.column( tableDescription.sum_columns[i] + ':name', { search: 'applied' } ).data();
                                var columnType = oSettings.aoColumns[ api.column( tableDescription.sum_columns[i] + ':name' ).index() ].wdtType;
                                var thousandsSeparator = tableDescription.number_format == 1 ? '.' : ',';
                                var decimalSeparator = tableDescription.number_format == 1 ? ',' : '.';
                                if( columnData.length > 0 ){
                                    var sum = columnData.reduce(function(a,b){
                                        if( columnType == 'int' ){
                                            return parseInt( a.toString().replace( thousandsSeparator, '' ) ) + parseInt( b.toString().replace( thousandsSeparator, '' ) );
                                        }else{
                                            return parseFloat( a.toString().replace( thousandsSeparator, '' ).replace( ',', '.' ) ) + parseFloat( b.toString().replace( thousandsSeparator, '' ).replace( ',', '.' ) );
                                        }
                                    });
                                }else{
                                    sum = 0;
                                }
                                if( columnType == 'int' ){
                                    var sum_str = wdtFormatNumber( sum, 0, decimalSeparator, thousandsSeparator );
                                }else{
                                    var sum_str = wdtFormatNumber( sum, tableDescription.decimal_places, decimalSeparator, thousandsSeparator );
                                }
                                
                                $( '#'+tableDescription.tableId+ ' tfoot tr.sum_row td.sum_cell[data-column_header="'+tableDescription.sum_columns[i]+'"]' ).html( '&#8721; = ' + sum_str );
                               
                            }
                        }
                    });
                    
                }
            }

            // Conditional formatting
            if( tableDescription.conditional_formatting_columns ){
                wpDataTables[tableDescription.tableId].fnSettings().aoDrawCallback.push({
                    sName: 'updateConditionalFormatting',
                    fn: function( oSettings ){
                        for( var i in tableDescription.conditional_formatting_columns ) {
                            var column =  oSettings.oInstance.api().column(tableDescription.conditional_formatting_columns[i] + ':name', {search: 'applied'});
                            var conditionalFormattingRules = oSettings.aoColumns[column.index()].conditionalFormattingRules;
                            var columnType = oSettings.aoColumns[column.index()].wdtType;
                            var thousandsSeparator = tableDescription.number_format == 1 ? /\./g : /\,/g;
                            var decimalSeparator = tableDescription.number_format == 1 ? /\,/g : /\./g;
                            var dateFormat = tableDescription.datepickFormat;
                            for( var j in conditionalFormattingRules ){
                                var nodes = column.nodes();
                                column.nodes().to$().each( function(){
                                    var ruleMatched = false;
                                    if( ( columnType == 'int' ) || ( columnType == 'float' ) ){
                                        // Process numeric comparison
                                        var cellVal = parseFloat( $(this).html().replace( thousandsSeparator, '' ).replace( ',', '.' ) );
                                        var ruleVal = conditionalFormattingRules[j].cellVal;
                                    }else if( columnType == 'date' ){
                                        // Process date comparison with datepicker methods
                                        var cellVal = $.datepicker.parseDate(dateFormat, $(this).html());
                                        var ruleVal = $.datepicker.parseDate(dateFormat, conditionalFormattingRules[j].cellVal);
                                    }else{
                                        // Process string comparison
                                        var cellVal = $(this).html();
                                        var ruleVal = conditionalFormattingRules[j].cellVal;
                                    }
                                    switch( conditionalFormattingRules[j].ifClause ){
                                        case 'lt':
                                            ruleMatched = cellVal < ruleVal;
                                        break;
                                        case 'lteq':
                                            ruleMatched = cellVal <= ruleVal;
                                        break;
                                        case 'eq':
                                            if( columnType == 'date' ){
                                                cellVal = cellVal != null ? cellVal.getTime() : null;
                                                ruleVal = ruleVal != null ? ruleVal.getTime() : null;
                                            }
                                            ruleMatched = cellVal == ruleVal;
                                        break;
                                        case 'neq':
                                            if( columnType == 'date' ){
                                                cellVal = cellVal != null ? cellVal.getTime() : null;
                                                ruleVal = ruleVal != null ? ruleVal.getTime() : null;
                                            }
                                            ruleMatched = cellVal != ruleVal;
                                            break;
                                        case 'gteq':
                                            ruleMatched = cellVal >= ruleVal;
                                        break;
                                        case 'gt':
                                            ruleMatched = cellVal > ruleVal;
                                        break;
                                        case 'contains':
                                            ruleMatched = cellVal.indexOf( ruleVal ) !== -1;
                                        break;
                                        case 'contains_not':
                                            ruleMatched = cellVal.indexOf( ruleVal ) == -1;
                                        break;
                                    }
                                    if( ruleMatched ){
                                        wdtApplyCellAction( $(this), conditionalFormattingRules[j].action, conditionalFormattingRules[j].setVal  );
                                    }
                                });
                            }
                        }
                    }
                });
                if( !tableDescription.serverSide ) {
                    wpDataTables[tableDescription.tableId].fnDraw();
                }
            }
                
            // Init the callback for checking if the selected row is first/last in the dataset
            wpDataTables[tableDescription.tableId].checkSelectedLimits = function () {
                if (wpDataTablesUpdatingFlags[tableDescription.tableId]) {
                    return;
                }
                var sel_row_index = $(tableDescription.selector + ' > tbody > tr.selected').index();
                if (sel_row_index + wpDataTables[tableDescription.tableId].fnSettings()._iDisplayStart == wpDataTables[tableDescription.tableId].fnSettings()._iRecordsDisplay - 1) {
                    $(tableDescription.selector + '_next_edit_dialog').prop('disabled', true)
                } else {
                    $(tableDescription.selector + '_next_edit_dialog').prop('disabled', false)
                }
                if ((sel_row_index == 0) && (wpDataTables[tableDescription.tableId].fnSettings()._iDisplayStart == 0)) {
                    $(tableDescription.selector + '_prev_edit_dialog').prop('disabled', true)
                } else {
                    $(tableDescription.selector + '_prev_edit_dialog').prop('disabled', false)
                }
            }

            // Init row grouping if enabled
            if ((tableDescription.columnsFixed == 0) && (tableDescription.groupingEnabled)) {
                wpDataTables[tableDescription.tableId].rowGrouping({iGroupingColumnIndex: tableDescription.groupingColumnIndex});
            }

            // Init the advanced filtering if enabled
            if (tableDescription.advancedFilterEnabled) {
                $('#'+tableDescription.tableId).dataTable().columnFilter(tableDescription.advancedFilterOptions);
                $.datepicker.regional[""].dateFormat = tableDescription.datepickFormat;
                $.datepicker.setDefaults($.datepicker.regional['']);
            }

            if (tableDescription.editable) {
                /**
                 * Init edit dialog on page load
                 */
                wpDataTableDialogs[tableDescription.tableId] = wdtDialog('', 'Edit');
                wpDataTableDialogs[tableDescription.tableId].addClass('wdtEditDialog');
                $(tableDescription.selector + '_edit_dialog').appendTo(wpDataTableDialogs[tableDescription.tableId]).show();
                $(tableDescription.selector + '_edit_dialog select').selecter();

                /**
                 * Close button in edit dialog
                 */
                $(tableDescription.selector + '_close_edit_dialog').click(function (e) {
                    e.preventDefault();
                    $.remodal.lookup[wpDataTableDialogs[tableDescription.tableId].data('remodal')].close();
                });

                /**
                 * Prev button in edit dialog
                 */
                $(tableDescription.selector + '_prev_edit_dialog').click(function (e) {
                    e.preventDefault();
                    var sel_row_index = $(tableDescription.selector + ' > tbody > tr.selected').index();
                    if (sel_row_index > 0) {
                        $(tableDescription.selector + ' > tbody > tr.selected').removeClass('selected');
                        $(tableDescription.selector + ' > tbody > tr:eq(' + (sel_row_index - 1) + ')').addClass('selected', 300);
                        wpDataTablesSelRows[tableDescription.tableId] = wpDataTables[tableDescription.tableId].fnGetPosition($(tableDescription.selector + ' > tbody > tr.selected').get(0));
                        var data = wpDataTables[tableDescription.tableId].fnGetData(wpDataTablesSelRows[tableDescription.tableId]);
                        wpDataTablesFunctions[tableDescription.tableId].applyData(data);
                    } else {
                        var cur_page = Math.ceil(wpDataTables[tableDescription.tableId].fnSettings()._iDisplayStart / wpDataTables[tableDescription.tableId].fnSettings()._iDisplayLength) + 1;
                        if (cur_page == 1)
                            return;
                        wpDataTablesSelRows[tableDescription.tableId] = -2;
                        wpDataTablesUpdatingFlags[tableDescription.tableId] = true;
                        wpDataTables[tableDescription.tableId].fnPageChange('previous');
                        $(tableDescription.selector + '_edit_dialog').parent().addClass('overlayed');
                    }
                    wpDataTables[tableDescription.tableId].checkSelectedLimits();
                });

                /**
                 * Next button in edit dialog
                 */
                $(tableDescription.selector + '_next_edit_dialog').click(function (e) {
                    e.preventDefault();
                    var sel_row_index = $(tableDescription.selector + ' > tbody > tr.selected').index();
                    if (sel_row_index < wpDataTables[tableDescription.tableId].fnSettings()._iDisplayLength - 1) {
                        $(tableDescription.selector + ' > tbody > tr.selected').removeClass('selected');
                        $(tableDescription.selector + ' > tbody > tr:eq(' + (sel_row_index + 1) + ')').addClass('selected', 300);
                        wpDataTablesSelRows[tableDescription.tableId] = wpDataTables[tableDescription.tableId].fnGetPosition($(tableDescription.selector + ' > tbody > tr.selected').get(0));
                        var data = wpDataTables[tableDescription.tableId].fnGetData(wpDataTablesSelRows[tableDescription.tableId]);
                        wpDataTablesFunctions[tableDescription.tableId].applyData(data);
                    } else {
                        var cur_page = Math.ceil(wpDataTables[tableDescription.tableId].fnSettings()._iDisplayStart / wpDataTables[tableDescription.tableId].fnSettings()._iDisplayLength) + 1;
                        var total_pages = Math.ceil(wpDataTables[tableDescription.tableId].fnSettings()._iRecordsTotal / wpDataTables[tableDescription.tableId].fnSettings()._iDisplayLength);
                        if (cur_page == total_pages)
                            return;
                        wpDataTablesSelRows[tableDescription.tableId] = -3;
                        wpDataTablesUpdatingFlags[tableDescription.tableId] = true;
                        wpDataTables[tableDescription.tableId].fnPageChange('next');
                        wpDataTables[tableDescription.tableId].fnDraw(false);
                        $(tableDescription.selector + '_edit_dialog').parent().addClass('overlayed');
                    }
                    wpDataTables[tableDescription.tableId].checkSelectedLimits();
                });

                /**
                 * Apply button in edit dialog
                 */
                $(tableDescription.selector + '_apply_edit_dialog').click(function (e) {
                    e.preventDefault();
                    wpDataTablesFunctions[tableDescription.tableId].saveTableData();
                });

                /**
                 * OK button in edit dialog
                 */
                $(tableDescription.selector + '_ok_edit_dialog').click(function (e) {
                    e.preventDefault();
                    wpDataTablesFunctions[tableDescription.tableId].saveTableData(true, true);
                });

                // Toggle OK when enter pressed in inputs (but not selectboxes or textareas)
                $(tableDescription.selector + '_edit_dialog input').keyup(function (e) {
                    if (e.which == 13) {
                        $(tableDescription.selector + '_ok_edit_dialog').click();
                    }
                });

                /**
                 * Apply maskmoney
                 */
                if (tableDescription.number_format == 1) {
                    $(tableDescription.selector + '_edit_dialog input.maskMoney').maskMoney({
                        thousands: '.',
                        decimal: ',',
                        precision: parseInt(tableDescription.decimal_places),
                        allowNegative: true
                    });
                } else {
                    $(tableDescription.selector + '_edit_dialog input.maskMoney').maskMoney({
                        thousands: ',',
                        decimal: '.',
                        precision: parseInt(tableDescription.decimal_places),
                        allowNegative: true
                    });
                }

                /**
                 * Apply pickadate
                 */
                var dateFormat = tableDescription.datepickFormat.replace(/y/g, 'yy').replace(/Y/g, 'yyyy').replace(/M/g, 'mmm');
                var datePickerInit = function (selector, additional_params, state) {
                    var input = $(selector).pickadate({
                        format: dateFormat,
                        formatSubmit: dateFormat,
                        selectYears: 20,
                        selectMonths: true,
                        container: '.wpDataTablesWrapper',
                        onClose: additional_params
                    });

                    var picker = input.pickadate('picker');

                    if (state == 'opened') {
                        picker.open();
                    }
                }

                datePickerInit(tableDescription.selector + '_edit_dialog input.datepicker');

                /**
                 * Apply fileuploaders
                 */
                var fileUploadInit = function (selector) {
                    if ($('.fileupload_' + selector).length) {

                        // Extend the wp.media object
                        wdtCustomUploader = wp.media({
                            title: wpdatatables_frontend_strings.select_upload_file,
                            button: {
                                text: wpdatatables_frontend_strings.choose_file
                            },
                            multiple: false
                        });


                        $('button.fileupload_' + selector).click(function (e) {
                            e.preventDefault();
                            var $button = $(this);
                            var $relInput = $('#' + $button.data('rel_input'));
                            if( $button.data('column_type') == 'icon' ){
                                wdtCustomUploader = wp.media({
                                        title: wpdatatables_frontend_strings.select_upload_file,
                                        button: {
                                            text: wpdatatables_frontend_strings.choose_file
                                        },
                                        multiple: false,
                                        library: {
                                            type: 'image'
                                        }
                                    });
                                wdtCustomUploader.off('select').on('select', function () {
                                    attachment = wdtCustomUploader.state().get('selection').first().toJSON();
                                    $relInput.val( attachment.sizes.thumbnail.url + '||' +  attachment.url );
                                    $('#files_' + $button.data('rel_input')).html('<p>' + attachment.filename + ' [<a href="#" data-key="' + $button.data('rel_input') + '" class="wdtdeleteFile">' + wpdatatables_frontend_strings.detach_file + '</a>]</p>');
                                });
                            }else{
                                // For other files hen a file is selected, grab the URL and set it as the text field's value
                                wdtCustomUploader.off('select').on('select', function () {
                                    attachment = wdtCustomUploader.state().get('selection').first().toJSON();
                                    $relInput.val(attachment.url);
                                    $('#files_' + $button.data('rel_input')).html('<p>' + attachment.filename + ' [<a href="#" data-key="' + $button.data('rel_input') + '" class="wdtdeleteFile">' + wpdatatables_frontend_strings.detach_file + '</a>]</p>');
                                });
                            }
                            // Open the uploader dialog
                            wdtCustomUploader.open();


                        });
                    }
                };
                fileUploadInit(tableDescription.tableId);


                /**
                 * Show edit dialog
                 */
                $('.edit_table[aria-controls="' + tableDescription.tableId + '"]').click(function () {
                    if ($(this).hasClass('disabled'))
                        return false;

                    var row = $(tableDescription.selector + ' tr.selected').get(0);
                    var data = wpDataTables[tableDescription.tableId].fnGetData(row);
                    wpDataTablesFunctions[tableDescription.tableId].applyData(data);
                    wpDataTables[tableDescription.tableId].checkSelectedLimits();
                    $.remodal.lookup[wpDataTableDialogs[tableDescription.tableId].data('remodal')].open();
                });


                if (tableDescription.inlineEditing) {
                    new inlineEditClass(tableDescription, dataTableOptions, $);
                }

                /**
                 * Add new entry dialog
                 */
                $('.new_table_entry[aria-controls="' + tableDescription.tableId + '"]').click(function () {
                    $(tableDescription.selector + '_edit_dialog .editDialogInput').val('').css('border', '');
                    $(tableDescription.selector + '_edit_dialog tr.idRow .editDialogInput').val('0');

                    $('#' + tableDescription.tableId + '_edit_dialog .editDialogInput').each(function (index) {

                        if ($(this).is('select')) {
                            $(this).find('option:first').attr('selected', 'selected');
                            $(this).selecter('refresh');
                        }
                    });

                    // Set the default values
                    if (tableDescription.advancedFilterEnabled) {
                        for (var i in tableDescription.advancedFilterOptions.aoColumns) {
                            var defaultValue = tableDescription.advancedFilterOptions.aoColumns[i].defaultValue;
                            if (defaultValue != '') {
                                $('#' + tableDescription.tableId + '_edit_dialog .editDialogInput:eq(' + i + ')').val(defaultValue).change();
                                if ($('#' + tableDescription.tableId + '_edit_dialog .editDialogInput:eq(' + i + ')').is('select')) {
                                    $('#' + tableDescription.tableId + '_edit_dialog .editDialogInput:eq(' + i + ')').val(defaultValue);
                                    $('#' + tableDescription.tableId + '_edit_dialog .editDialogInput:eq(' + i + ')').selecter('refresh');
                                }
                            }
                        }
                    }

                    $.remodal.lookup[wpDataTableDialogs[tableDescription.tableId].data('remodal')].open();
                    if ($('.fileupload_' + tableDescription.tableId).length) {
                        var $fileupload_el = $('.fileupload_' + tableDescription.tableId);
                        var id_key = $('#' + tableDescription.tableId + '_edit_dialog tr.idRow .editDialogInput').data('key');
                        var id_val = $('#' + tableDescription.tableId + '_edit_dialog tr.idRow .editDialogInput').val();
                        $('#' + tableDescription.tableId + '_edit_dialog input.editDialogInput[data-input_type="attachment"]').val();
                        $('#' + tableDescription.tableId + '_edit_dialog div.files').html('');
                    }

                });

                /**
                 * Delete an entry dialog
                 */
                $('.delete_table_entry[aria-controls="' + tableDescription.tableId + '"]').click(function () {
                    if ($(this).hasClass('disabled')){
                        return false;
                    }

                    if ( tableDescription.popoverTools ) {
                        $('.wpDataTablesPopover.editTools').hide();
                    }
                    var confirm_dialog_str = '<div id="delete_dialog_' + tableDescription.tableId + '">Delete this entry?</div>';
                    $deleteDialog = wdtDialog(confirm_dialog_str, 'Are you sure?');
                    $deleteDialog.append('<button class="btn deleteBtn">Delete</button>');
                    $deleteDialog.append('<button class="btn cancelBtn">Cancel</button>');
                    $deleteDialog.find('.deleteBtn').click(function (e) {
                        e.preventDefault();
                        var row = $(tableDescription.selector + ' tr.selected').get(0);
                        var data = wpDataTables[tableDescription.tableId].fnGetData(row);
                        var id_val = data[tableDescription.idColumnIndex];
                        var that = this;
                        $.ajax({
                            url: tableDescription.adminAjaxBaseUrl,
                            type: 'POST',
                            data: {
                                action: 'wdt_delete_table_row',
                                id_key: tableDescription.idColumnKey,
                                id_val: id_val,
                                table_id: tableDescription.tableWpId
                            },
                            success: function () {
                                wpDataTables[tableDescription.tableId].fnDraw(false);
                                $.remodal.lookup[$deleteDialog.data('remodal')].close();
                                $deleteDialog.remove();
                            }
                        });
                    });
                    $deleteDialog.find('.cancelBtn').click(function (e) {
                        $.remodal.lookup[$deleteDialog.data('remodal')].close();
                        $deleteDialog.remove();
                    });
                    $.remodal.lookup[$deleteDialog.data('remodal')].open();
                });

                // Add a popover that include edit elements
                if (tableDescription.popoverTools) {
                    $(tableDescription.selector + '_wrapper').css('position', 'relative');
                    $('<div class="wpDataTablesPopover editTools"></div>').prependTo(tableDescription.selector + '_wrapper').hide();
                    $('.new_table_entry[aria-controls="' + tableDescription.tableId + '"]').prependTo(tableDescription.selector + '_wrapper .wpDataTablesPopover.editTools').css('float', 'right');
                    $('.edit_table[aria-controls="' + tableDescription.tableId + '"]').prependTo(tableDescription.selector + '_wrapper .wpDataTablesPopover.editTools').css('float', 'right');
                    $('.delete_table_entry[aria-controls="' + tableDescription.tableId + '"]').prependTo(tableDescription.selector + '_wrapper .wpDataTablesPopover.editTools').css('float', 'right');
                }

                var clickEvent = function (e) {
                    // Set controls popover position
                    var popoverVerticalPosition = $(this).offset().top - $(tableDescription.selector + '_wrapper').offset().top - $('.wpDataTablesPopover.editTools').outerHeight() - 7;
                    // Check a cell is edited
                    var editedRow = ($(this).children('').hasClass('editing')) ? true : false;

                    if ($(this).hasClass('selected')) {
                        $(tableDescription.selector + ' tbody tr').removeClass('selected');
                        wpDataTablesSelRows[tableDescription.tableId] = -1;
                    } else {
                        $(tableDescription.selector + '  tbody tr').removeClass('selected');
                        $(this).addClass('selected');
                        wpDataTablesSelRows[tableDescription.tableId] = wpDataTables[tableDescription.tableId].fnGetPosition($(tableDescription.selector + ' tbody tr.selected').get(0));
                    }
                    if ($(tableDescription.selector + ' tbody tr.selected').length > 0) {
                        $('.edit_table[aria-controls="' + tableDescription.tableId + '"]').removeClass('disabled');
                        $('.delete_table_entry[aria-controls="' + tableDescription.tableId + '"]').removeClass('disabled');
                        if (tableDescription.popoverTools) {
                            if (!editedRow) {
                                $('.wpDataTablesPopover.editTools').show().css('top', popoverVerticalPosition);
                            } else {
                                return false;
                            }
                        }
                    } else {
                        $('.edit_table[aria-controls="' + tableDescription.tableId + '"]').addClass('disabled');
                        $('.delete_table_entry[aria-controls="' + tableDescription.tableId + '"]').addClass('disabled');
                        if (tableDescription.popoverTools) {
                            $('.wpDataTablesPopover.editTools').hide();
                        }
                    }
                }

                var ua = navigator.userAgent,
                        event = (ua.match(/iPad/i)) ? "touchstart" : "click";

                $(document).on(event, tableDescription.selector + ' tbody tr', clickEvent);

                /**
                 * Detached the chosen attachment
                 */
                $(document).on('click', tableDescription.selector + '_edit_dialog a.wdtdeleteFile, a.wdtdeleteFile', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    $('#' + $(this).data('key')).val('');
                    $(this).closest('div.files').html('');
                });

            }

            // Show the filter box if enabled in the widget if it is present
            if (tableDescription.externalFilter == true) {
                if ($('#wdtFilterWidget').length) {
                    $('.wpDataTablesFilter').appendTo('#wdtFilterWidget');
                }
            }

            $(window).load(function () {
                // Show table if it was hidden
                if (tableDescription.hideBeforeLoad) {
                    $(tableDescription.selector).show(300);
                }
            });

        });

        /**
         * Charts
         */
        if (typeof wpDataTablesCharts !== 'undefined') {
            google.load("visualization", "1", {packages: ["corechart"], callback: function () {
                    for (var chartId in wpDataTablesCharts) {
                        switch (wpDataTablesCharts[chartId].type) {
                            case 'Line':
                                var chart = new google.visualization.LineChart(document.getElementById(wpDataTablesCharts[chartId].container));
                                break;
                            case 'Area':
                                var chart = new google.visualization.AreaChart(document.getElementById(wpDataTablesCharts[chartId].container));
                                break;
                            case 'Bar':
                                var chart = new google.visualization.BarChart(document.getElementById(wpDataTablesCharts[chartId].container));
                                break;
                            case 'Column':
                                var chart = new google.visualization.ColumnChart(document.getElementById(wpDataTablesCharts[chartId].container));
                                break;
                            case 'Pie':
                                var chart = new google.visualization.PieChart(document.getElementById(wpDataTablesCharts[chartId].container));
                                break;
                        }
                        chart.draw(google.visualization.arrayToDataTable(wpDataTablesCharts[chartId].values), wpDataTablesCharts[chartId].options);
                    }
                }
            });
        }

    })

    /**
     * Clear filters button
     */
    $('button.wdtClearFilters').click(function (e) {
        e.preventDefault();
        $('.filter_column input:text').val('');
        $('.filter_column select').val('').selecter('update');
        $('.filter_column input:checkbox').removeAttr('checked').iCheck('update');
        for (var i in wpDataTables) {
            wpDataTables[i].fnFilterClear();
        }
    });

})(jQuery);

function wdtApplyCellAction( $cell, action, setVal ){
    switch( action ){
        case 'setCellColor':
                $cell.css( 'background-color', setVal );
            break;
        case 'defaultCellColor':
            $cell.css( 'background-color', '' );
            break;
        case 'setCellContent':
                $cell.html( setVal );
            break;
        case 'setCellClass':
            $cell.addClass(setVal);
            break;
        case 'removeCellClass':
            $cell.removeClass(setVal);
            break;
        case 'setRowColor':
            $cell.closest('tr').find('td').css('background-color', setVal);
            break;
        case 'defaultRowColor':
            $cell.closest('tr').find('td').css('background-color', '');
            break;
        case 'setRowClass':
            $cell.closest('tr').addClass(setVal);
            break;
        case 'addColumnClass':
            var index = $cell.index()+1;
            $cell
                .closest('table.wpDataTable')
                .find('tbody td:nth-child('+index+')')
                .addClass(setVal);
            break;
        case 'setColumnColor':
            var index = $cell.index()+1;
            $cell
                .closest('table.wpDataTable')
                .find('tbody td:nth-child('+index+')')
                .css('background-color', setVal);
            break;
    }
}

function wdtDialog(str, title) {
    var dialogId = Math.floor((Math.random() * 1000) + 1);
    var dialog_str = '<div class="remodal wpDataTables wdtRemodal" id="remodal-' + dialogId + '"><h1>' + title + '</h1>';
    dialog_str += str;
    dialog_str += '</div>';
    jQuery(dialog_str).remodal({
        type: 'inline',
        preloader: false
    });
    return jQuery('#remodal-' + dialogId);
}

function wdtAddOverlay(table_selector) {
    jQuery(table_selector).addClass('overlayed');
}

function wdtRemoveOverlay(table_selector) {
    jQuery(table_selector).removeClass('overlayed');
}

jQuery.fn.dataTableExt.oStdClasses.sWrapper = "wpDataTables wpDataTablesWrapper";