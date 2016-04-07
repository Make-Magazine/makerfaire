if( typeof constructedChartData == 'undefined' ){
    var constructedChartData = {};
}

var wdtChartColumnsData = {};

(function($){
    var wdtChartPickerDragStart = 0;
    var wdtChartPickerDragEnd = 0;
    var wdtChartPickerIsDragging = false;

    // Init selecter
    $('#chart_render_engine, #wpdatatables_chart_source, #wdt_chart_row_range_type, #wdt_chart_series_setting').selecter();
    
    // Init remodal popup
    $('div.pickRange').remodal({
        type: 'inline',
        preloader: false,
        modal: true        
    })
    
    $('#google_chart_type').imagepicker({ show_label: true });
    $('#highcharts_chart_type').imagepicker({ show_label: true });
    
    /**
     * Steps switcher (Next)
     */
    $('#nextStep').click(function(e){
        e.preventDefault();
        
        var curStep = $('div.chartWizardStep:visible').data('step');
        $('div.chartWizardStep').hide(200);
        $('div.chart_wizard_breadcrumbs_block').removeClass('active');
        
        switch( curStep ){
            case 'step1':
                // Data source
                $('div.chartWizardStep.step2').show(200);
                $('div.chart_wizard_breadcrumbs_block.step2').addClass('active');
                constructedChartData.chart_title = $('#chart_name').val();
                constructedChartData.engine = $('#chart_render_engine').val();
                if( $('#chart_render_engine').val() == 'google' ){
                    constructedChartData.chart_type = $('#google_chart_type').val();
                    constructedChartData.min_columns = parseInt( $('#google_chart_type option:selected').data('min_columns') );
                    constructedChartData.max_columns = parseInt( $('#google_chart_type option:selected').data('max_columns') );
                }else if( $('#chart_render_engine').val() == 'highcharts' ){
                    constructedChartData.chart_type = $('#highcharts_chart_type').val();
                    constructedChartData.min_columns = parseInt( $('#highcharts_chart_type option:selected').data('min_columns') );
                    constructedChartData.max_columns = parseInt( $('#highcharts_chart_type option:selected').data('max_columns') );
                }
                $('#prevStep').show();
                $('#wpdatatables_chart_source').change();
            break;
        case 'step2':
                // Data range
                $('#wdtPreloadLayer').show();
                constructedChartData.wpdatatable_id = $('#wpdatatables_chart_source').val();
                $('div.chartWizardStep.step3').show(200);
                $('div.chart_wizard_breadcrumbs_block.step3').addClass('active');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        action: 'wpdatatables_get_columns_data_by_table_id',
                        table_id: constructedChartData.wpdatatable_id
                    },
                    success: function( columns ){
                        wdtChartColumnsData = columns;
                        var columnChartTemplate = $.templates("#wdtChartColumnBlock");
                        var columnChartBlockHtml = columnChartTemplate.render({columns: columns});
                        $('div.chart_column_picker_container div.existing_columns_container').html(columnChartBlockHtml)
                        
                        if( ( typeof constructedChartData.selected_columns == 'undefined' ) 
                                && ( typeof editing_chart_data !== 'undefined' ) ){
                            for( var i in editing_chart_data.selected_columns ) {
                                $('div.chart_column_picker_container div.existing_columns_container div.chart_column_block[data-orig_header="'+editing_chart_data.selected_columns[i]+'"]')
                                        .appendTo( 'div.chart_column_picker_container div.chosen_columns_container' );
                            }
                        }
                        $('#wdtAddChartColumns').click();
                        $('#wdtPreloadLayer').hide();
                    }
                })
            break;
        case 'step3':
            // Formatting
            constructedChartData.follow_filtering = $('#followTableFiltering').is(':checked') ? 1 : 0;
            $('#wdtPreloadLayer').show();
            if( typeof constructedChartData.selected_columns == 'undefined' ){
                constructedChartData.selected_columns = {};
            }

            $('div.chosen_columns_container div.chart_column_block').each(function(){
                constructedChartData.selected_columns[ parseInt( $(this).index() ) ] = $(this).data('orig_header');
            });
            
            $('#wdt_chart_row_range_type').change();
            $.ajax({
                url: ajaxurl,
                data: {
                    action: 'wpdatatable_get_chart_axes_and_series',
                    chart_data: constructedChartData
                },
                dataType: 'json',
                type: 'post',
                success: function( data ){
                    $('div.chartWizardStep.step4').show(200);
                    $('div.chart_wizard_breadcrumbs_block.step4').addClass('active');
                    
                    var seriesBlockTemplate = $.templates("#wdtChartSeriesSettingBlock");
                    var seriesBlockTemplateHtml = seriesBlockTemplate.render({ series: data.series });
                    
                    $('#series_settings_container').html( seriesBlockTemplateHtml );
                    for( var i in data.options.series ){
                        $('#series_settings_container div.chart_series_block:eq('+i+')').find('div.chart_series_color input').val( data.options.series[i].color );
                    }
                    $('#series_settings_container input.series_color').wpColorPicker();
                    
                    if( $('#wpDataChartId').val() == '' ){
                        $('#horizontal_axis_label').val( data.axes.major.label );
                        $('#vertical_axis_label').val( data.axes.minor.label );
                    }
                    
                    $('#wdtPreloadLayer').hide();
                }
            });
            break;
        case 'step4':
            // Preview
            $('#wdtPreloadLayer').show();
            constructedChartData.show_title = $('#show_chart_title').is(':checked') ? 1 : 0;
            constructedChartData.width = parseInt( $('#chart_width').val() );
            constructedChartData.height = parseInt( $('#chart_height').val() );
            constructedChartData.show_grid = $('#show_grid').is(':checked') ? 1 : 0;
            constructedChartData.show_legend = $('#show_legend').is(':checked') ? 1 : 0;
            
            constructedChartData.horizontal_axis_label = $('#horizontal_axis_label').val();
            constructedChartData.vertical_axis_label = $('#vertical_axis_label').val();
            
            if( typeof constructedChartData.series_data == 'undefined' ){
                constructedChartData.series_data = {};
            }
            $('div.chart_series_block').each(function(e){
                constructedChartData.series_data[$(this).data('orig_header')] = {
                    label: $(this).find('input.series_label').val(),
                    color: $(this).find('input.series_color').val()
                }
            });
            $.ajax({
                url: ajaxurl,
                data: {
                    action: 'wpdatatable_show_chart_from_data',
                    chart_data: constructedChartData
                },
                dataType: 'json',
                type: 'post',
                success: function( data ){

                    $('div.chartWizardStep.step5').show(200);
                    $('div.chart_wizard_breadcrumbs_block.step5').addClass('active');
                    if( constructedChartData.engine == 'google' ){
                        var wdtChart = new wpDataTablesGoogleChart();
                        wdtChart.setType( data.type );
                        wdtChart.setColumns( data.columns );
                        wdtChart.setRows( data.rows );
                        wdtChart.setOptions( data.options );
                        wdtChart.setContainer( 'googleChartContainer' );
                        wdtChart.setColumnIndexes( data.column_indexes );
                    }else{
                        var wdtChart = new wpDataTablesHighchart();
                        wdtChart.setOptions( data.options );
                        wdtChart.setType( data.type );
                        wdtChart.setWidth( data.width );
                        wdtChart.setHeight( data.height );
                        wdtChart.setColumnIndexes( data.column_indexes );
                        wdtChart.setContainer( '#googleChartContainer' );
                    }
                    wdtChart.render();
                    $('#wdtPreloadLayer').hide();
                    
                }
            });
            break;
            case 'step5':
            // Save and get shortcode
            $('#wdtPreloadLayer').show();
            $.ajax({
                url: ajaxurl,
                data: {
                    action: 'wpdatatable_save_chart_get_shortcode',
                    chart_data: constructedChartData
                },
                type: 'post',
                dataType: 'json',
                success: function( data ){

                    $('div.chartWizardStep.step6').show(200);
                    $('div.chart_wizard_breadcrumbs_block.step6').addClass('active');
                    $('#wdt_chart_shortcode_container').html( data.shortcode );
                    constructedChartData.chart_id = data.id;
                    $('#wpDataChartId').val( data.id )
                    $('#wdtPreloadLayer').hide();
                    $('#nextStep').hide();
                    $('#finishButton').show();
                    
                }
            });
            break;
        }
    })
    
    /**
     * Steps switcher (Prev)
     */
    $('#prevStep').click(function(e){
        e.preventDefault();
        
        var curStep = $('div.chartWizardStep:visible').data('step');
        
        switch( curStep ){
            case 'step2':
                $('div.chartWizardStep.step1').show(200);
                $('div.chartWizardStep.step2').hide(200);
                $('div.chart_wizard_breadcrumbs_block.step2').removeClass('active');
                $('div.chart_wizard_breadcrumbs_block.step1').addClass('active');
                $('#chart_render_engine').change();
            break;
            case 'step3':
                $('div.chartWizardStep.step2').show(200);
                $('div.chartWizardStep.step3').hide(200);
                $('div.chart_wizard_breadcrumbs_block.step3').removeClass('active');
                $('div.chart_wizard_breadcrumbs_block.step2').addClass('active');
            break;
            case 'step4':
                $('div.chartWizardStep.step3').show(200);
                $('div.chartWizardStep.step4').hide(200);
                $('div.chart_wizard_breadcrumbs_block.step4').removeClass('active');
                $('div.chart_wizard_breadcrumbs_block.step3').addClass('active');
            break;
            case 'step5':
                $('div.chartWizardStep.step4').show(200);
                $('div.chartWizardStep.step5').hide(200);
                $('div.chart_wizard_breadcrumbs_block.step5').removeClass('active');
                $('div.chart_wizard_breadcrumbs_block.step4').addClass('active');
            break;
            case 'step6':
                $('div.chartWizardStep.step5').show(200);
                $('div.chartWizardStep.step6').hide(200);
                $('div.chart_wizard_breadcrumbs_block.step6').removeClass('active');
                $('div.chart_wizard_breadcrumbs_block.step5').addClass('active');
                $('#nextStep').show();
                $('#finishButton').hide();
            break;
        }
    });
    
    /**
     * Open chart browser on finish
     */
    $('#finishButton').click(function(e){
        e.preventDefault();
        window.location = $('#wdtBrowseChartsURL').val();
    });
    
    /**
     * Pick the chart type
     */
    $('#chart_render_engine').change(function(e){
        e.preventDefault();
        $('tr.charts_type').hide();
        if($(this).val() != ''){
            constructedChartData.chart_engine = $(this).val();
            if( $(this).val() == 'google' ){
                $('tr.google_charts_type').show();
            }
            if( $(this).val() == 'highcharts' ){
                $('tr.highcharts_charts_type').show();
            }
            $('#nextStep').show();
        }else{
            $('#nextStep').hide();
        }
    });
    
    /**
     * Pick the data type
     */
    $('#wpdatatables_chart_source').change(function(e){
        e.preventDefault();
        if( $(this).val() == '' ){
            $('#nextStep').hide();
        }else{
            $('#nextStep').show();
        }
    });
    
    /**
     * Select all columns in the column selecter
     */
    $('div.chart_column_picker_container a.select_all_columns').click(function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        $(this).parent().parent().parent().find('div.chart_column_block').addClass('selected');
    })

    /**
     * Deselect all columns in the column selecter
     */
    $('div.chart_column_picker_container a.deselect_all_columns').click(function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        $(this).parent().parent().parent().find('div.chart_column_block').removeClass('selected');
    })
    
    /**
     * Select a column in chart row range picker
     */
    $(document).on('click','div.chart_column_picker_container div.chart_column_block', function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        if( $(this).hasClass('selected') ){
            $(this).removeClass('selected');
        }else{
            $(this).addClass('selected');
        }
    });
    
    /**
     * Check for limit of string columns
     */
    function checkColumnsLimit(){
        // 1 - Checking for string columns
        var string_columns = 0;
        var valid = true;
        $('div.chosen_columns_container div.chart_column_block').each(function(){
            if(
                $(this).hasClass('string') 
                || $(this).hasClass('link') 
                || $(this).hasClass('email') 
                || $(this).hasClass('image') 
                ){
                    string_columns++;
                }
        });
        if( string_columns > 1 ){
            $('div.chosen_columns div.strings_error').show();
            valid = false;
        }else{
            $('div.chosen_columns div.strings_error').hide();
        } 
        // 2 - Checking for min and max columns limit
        var totalColumnCount = $('div.chosen_columns_container div.chart_column_block').length;
        if( totalColumnCount < constructedChartData.min_columns ){
            $('div.chosen_columns div.min_columns_error').show();
            $('div.chosen_columns div.min_columns_error span.columns').html(constructedChartData.min_columns);
            valid = false;
        }else{
            $('div.chosen_columns div.min_columns_error').hide();
        }
        if( ( constructedChartData.max_columns > 0 ) 
                && ( totalColumnCount > constructedChartData.max_columns ) ){
            $('div.chosen_columns div.max_columns_error').show();
            $('div.chosen_columns div.max_columns_error span.columns').html(constructedChartData.max_columns);
            valid = false;
        }else{
            $('div.chosen_columns div.max_columns_error').hide();
        }
        if( !valid ){
            $('#nextStep').hide();
        }else{
            $('#nextStep').show();
        }
    }
        
    /**
     * Add columns to chart
     */
    $('#wdtAddChartColumns').click(function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        $('div.chart_column_picker_container div.existing_columns_container div.chart_column_block.selected').each(function(){
            $(this).appendTo('div.chart_column_picker_container div.chosen_columns_container');
        });
        checkColumnsLimit();
        $('div.chart_column_picker_container div.chosen_columns_container').sortable();
    });
    
    /**
     * Add all columns to chart
     */
    $('#wdtAddAllChartColumns').click(function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        $('div.chart_column_picker_container div.existing_columns_container div.chart_column_block').addClass('selected');
        $('#wdtAddChartColumns').click();
        $('div.chart_column_picker_container div.chosen_columns_container div.chart_column_block').removeClass('selected');
    });
    
    /**
     * Remove columns from chart series
     */
    $('#wdtRemoveChartColumns').click(function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        $('div.chart_column_picker_container div.chosen_columns_container div.chart_column_block.selected').each(function(){
            $(this).appendTo('div.chart_column_picker_container div.existing_columns_container ');
        });
        checkColumnsLimit();
    });
    
    /**
     * Remove all columns from chart
     */
    $('#wdtRemoveAllChartColumns').click(function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        $('div.chart_column_picker_container div.chosen_columns_container div.chart_column_block').addClass('selected');
        $('#wdtRemoveChartColumns').click();
        $('div.chart_column_picker_container div.existing_columns_container div.chart_column_block').removeClass('selected');
    });
    
    /**
     * Change the range type
     */
    $('#wdt_chart_row_range_type').change(function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        if( $(this).val() == 'all_rows' ){
            constructedChartData.range_type = 'all_rows';
            $('#rangePickedInfo span').html( 'All' );
            $( '#openRangePickerBtn' ).hide(200);
            $('#followTableFiltering').prop( 'disabled', false );
        }else{
            constructedChartData.range_type = 'picked_range';
            $( '#openRangePickerBtn' ).show(200);
            if( typeof constructedChartData.range_data == 'undefined' ){
                constructedChartData.range_data = [];
            }
            $('#followTableFiltering').prop( 'checked', false );
            $('#followTableFiltering').prop( 'disabled', true );
        }
    });
    
    /**
     * Update the picked range
     */
    var wdtUpdateChartRange = function(){
        $('table.rangePickerTable td').removeClass( 'selected' );
        $('table.rangePickerTable tbody tr').each(function(){
            if( $(this).find('td.pickRow input.addRowToRange').is(':checked') ){
                $(this).find('td').not('.pickRow').each(function(){
                    if( $('table.rangePickerTable thead th:eq('+$(this).index()+') input.pickColumnRange:checked').length ){
                        $(this).addClass('selected');
                    }else{
                        $(this).removeClass('selected');
                    }
                });
            }
        });
    }
    
    /**
     * Open the range picker
     */
    $('#openRangePickerBtn').click(function(e){
        e.preventDefault();
        if( typeof constructedChartData.selected_columns == 'undefined' ){
            constructedChartData.selected_columns = {};
        }
        $('#wdtPreloadLayer').show();
        $.ajax({
            url: ajaxurl,
            type: 'post',
            dataType: 'json',
            data: {
                action: 'wpdatatables_get_complete_table_json_by_id',
                table_id: constructedChartData.wpdatatable_id
            },
            success: function(tableData){
                // Extract the column headers
                if( tableData.length > 0 ){
                    var columnHeaders = [];
                    for( var columnHeader in tableData[0] ){
                        for( var i in wdtChartColumnsData  ){
                            if( wdtChartColumnsData[i].orig_header == columnHeader ){
                                var checked = 0;
                                if( typeof constructedChartData.selected_columns[wdtChartColumnsData[i].id] != 'undefined'){
                                    checked = 1;
                                }
                                columnHeaders.push({
                                    header: columnHeader,
                                    id: wdtChartColumnsData[i].id,
                                    checked: checked
                                });
                                break;
                            }
                        }
                    }
                    var rangePickerTemplate = $.templates("#rangePickerBlock");
                    var rangePickerHTML = rangePickerTemplate.render({
                            columnHeaders: columnHeaders,
                            tableData: tableData
                        });
                    $('#pickRangeTableContainer').html( rangePickerHTML );
                    $('#wdtPreloadLayer').hide();
                    $.remodal.lookup[$('div.pickRange').data('remodal')].open();
                }
            }
        });
    });
    
    /**
     * Add/remove row to range
     */
    $(document).on( 'change', 'div.pickRange table input.addRowToRange, div.pickRange table input.pickColumnRange', function(e){
        e.preventDefault();
        wdtUpdateChartRange();
    });
    
    $(document).on( 'click', 'div.pickRange table input.addRowToRange', function(e){ 
        e.stopImmediatePropagation();
    });
               
    function wdtRangePickerMouseDown(e) {
        if( e.target.nodeName == 'INPUT' ){
            return;
        };
        if (isRightClick(e)) {
            return false;
        } else {
            var allCells = $("div.pickRange table tbody td");
            wdtChartPickerDragStart = allCells.index($(this));
            wdtChartPickerIsDragging = true;

            if (typeof e.preventDefault != 'undefined') { e.preventDefault(); } 
            document.documentElement.onselectstart = function () { return false; };
        } 
    }

    function wdtRangePickerMouseUp(e) {
        if( e.target.nodeName == 'INPUT' ){
            wdtUpdateChartRange();
            return;
        };
        if (isRightClick(e)) {
            return false;
        } else {
            var allCells = $("div.pickRange table tbody td");
            wdtChartPickerDragEnd = allCells.index($(this));

            wdtChartPickerIsDragging = false;
            if (wdtChartPickerDragEnd != 0) {
                wdtRangePickerSelectRange();
            }

            document.documentElement.onselectstart = function () { return true; }; 
        }
    }

    function wdtRangePickerMouseMove(e) {
        if(wdtChartPickerIsDragging) {
            var allCells = $("div.pickRange table tbody td");
            wdtChartPickerDragEnd = allCells.index($(this));
            wdtRangePickerSelectRange();
        }            
    }

    function wdtRangePickerSelectRange() {
        
        $firstSelected = $("div.pickRange table tbody td").eq(wdtChartPickerDragStart);
        $lastSelected = $("div.pickRange table tbody td").eq(wdtChartPickerDragEnd);
        // Reset all the selected columns and rows
        $('div.pickRange input.pickColumnRange').prop('checked', false);
        $('div.pickRange input.addRowToRange').prop('checked', false);

        // Get the selected columns indexes
        var startColumnIndex = $firstSelected.index();
        var endColumnIndex = $lastSelected.index();
        
        if( startColumnIndex < endColumnIndex+1 ){
            $('div.pickRange table thead th').slice( startColumnIndex, endColumnIndex+1 ).find('input.pickColumnRange').prop('checked',true);
        }else{
            $('div.pickRange table thead th').slice( endColumnIndex, startColumnIndex+1 ).find('input.pickColumnRange').prop('checked',true);
        }
        
        // Get the selected rows indexes
        var startRowIndex = $firstSelected.parent().index();
        var endRowIndex = $lastSelected.parent().index();
        
        if( startRowIndex < endRowIndex+1 ){
            $('div.pickRange table tbody tr').slice( startRowIndex, endRowIndex+1 ).find('input.addRowToRange').prop('checked',true);
        }else{
            $('div.pickRange table tbody tr').slice( endRowIndex, startRowIndex+1 ).find('input.addRowToRange').prop('checked',true);
        }
        
        wdtUpdateChartRange();
    }
               
               
    $(document)
        .on( 'mousedown','div.pickRange table tbody td',wdtRangePickerMouseDown )
        .on( 'mouseup','div.pickRange table tbody td', wdtRangePickerMouseUp )
        .on( 'mousemove','div.pickRange table tbody td', wdtRangePickerMouseMove );
    
    /**
     * Submit the pick range
     */
    $('#submitPickRange').click( function(e){
        e.preventDefault();
        // First update the picked columns range
        // Remove all columns
        $('#wdtRemoveAllChartColumns').click();
        // Deselect all columns
        $('div.existing_columns_container div.chart_column_block').removeClass('selected');
        // Select the columns picked in the range picker
        $('div.pickRange table input.pickColumnRange:checked').each(function(){
            var column_id = $(this).parent().data('column_id');
            $('div.existing_columns_container div.chart_column_block[data-column_id="'+column_id+'"]').addClass('selected');
        });
        // Add the columns
        $('#wdtAddChartColumns').click();
        // Add the selected row indexes
        var selectedIndexes = [];
        $('div.pickRange table input.addRowToRange:checked').each(function(){
            selectedIndexes.push( $(this).closest('tr').data('index') );
        });
        constructedChartData.range_data = selectedIndexes;
        // Update the counter in the row range data
        $('#rangePickedInfo span').html( selectedIndexes.length );
        $.remodal.lookup[$('div.pickRange').data('remodal')].close();
    });
    
    $('#cancelPickRange').click(function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        $.remodal.lookup[$('div.pickRange').data('remodal')].close();
    });
    
    /**
     * Load data for editing existing charts
     */
    $(document).ready(function(){
        if( $('#wpDataChartId').val() != '' ){
            
            $('#chart_render_engine').change();
            constructedChartData.chart_id = $('#wpDataChartId').val();
            constructedChartData.chart_title = editing_chart_data.title;
            if( constructedChartData.engine == 'google' ){
                $('#google_chart_type').val( editing_chart_data.type ).change()
            }else{
                $('#highcharts_chart_type').val( editing_chart_data.type ).change()
            }
            $('#wpdatatables_chart_source').val( editing_chart_data.wpdatatable_id );
            
            if( editing_chart_data.range_type == 'picked_range' ){
                $('#wdt_chart_row_range_type').val( 'pick_rows' ).change();
                constructedChartData.range_data = editing_chart_data.row_range;
                $('#rangePickedInfo span').html( constructedChartData.range_data.length );
            }
            
            $('#chart_width').val( editing_chart_data.render_data.options.width );
            $('#chart_height').val( editing_chart_data.render_data.options.height );
            $('#horizontal_axis_label').val( editing_chart_data.render_data.options.hAxis.title );
            $('#vertical_axis_label').val( editing_chart_data.render_data.options.vAxis.title );
            
        }
    });
    
    
})(jQuery);

/**
 * Helper func to check if right mousebutton was clicked
 */
function isRightClick(e) {
    if (e.which) {
        return (e.which == 3);
    } else if (e.button) {
        return (e.button == 2);
    }
    return false;
}