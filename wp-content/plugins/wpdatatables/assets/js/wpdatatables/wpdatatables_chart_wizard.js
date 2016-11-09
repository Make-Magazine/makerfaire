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
                    $( ".highcharts" ).hide();
                    $( ".google" ).show();

                    $('#curve_type_row').hide();
                    $('#three_d_row').hide();
                    $('#background_color_row').show();
                    $('#border_width_row').show();
                    $('#border_color_row').show();
                    $('#border_radius_row').show();
                    $('#plot_background_color_row').show();
                    $('#plot_border_width_row').show();
                    $('#plot_border_color_row').show();
                    $('#font_size_row').show();
                    $('#font_name_row').show();
                    $('.axes').show();
                    $('#show_grid_row').show();
                    $('#horizontal_axis_crosshair_row').show();
                    $('#vertical_axis_crosshair_row').show();
                    $('.title').show();
                    $('#title_floating_row').show();
                    $('.tooltip').show();
                    $('.legend').show();
                    $('#inverted_row').show();

                    constructedChartData.chart_type = $('#google_chart_type').val();
                    switch (constructedChartData.chart_type) {
                        case 'google_column_chart':
                            $('#horizontal_axis_crosshair_row').hide();
                            $('#vertical_axis_crosshair_row').hide();
                            break;
                        case 'google_histogram':
                            $('#horizontal_axis_crosshair_row').hide();
                            $('#vertical_axis_crosshair_row').hide();
                            break;
                        case 'google_bar_chart':
                            $('#horizontal_axis_crosshair_row').hide();
                            $('#vertical_axis_crosshair_row').hide();
                            break;
                        case 'google_line_chart':
                            $('#curve_type_row').show();
                            break;
                        case 'google_stepped_area_chart':
                            $('#horizontal_axis_crosshair_row').hide();
                            $('#vertical_axis_crosshair_row').hide();
                            break;
                        case 'google_pie_chart':
                            $('#plot_background_color_row').hide();
                            $('#plot_border_width_row').hide();
                            $('#plot_border_color_row').hide();
                            $('#three_d_row').show();
                            $('.axes').hide();
                            $('#title_floating_row').hide();
                            break;
                        case 'google_donut_chart':
                            $('#plot_background_color_row').hide();
                            $('#plot_border_width_row').hide();
                            $('#plot_border_color_row').hide();
                            $('.axes').hide();
                            $('#title_floating_row').hide();
                            break;
                        case 'google_gauge_chart':
                            $('#background_color_row').hide();
                            $('#border_width_row').hide();
                            $('#border_color_row').hide();
                            $('#border_radius_row').hide();
                            $('#plot_background_color_row').hide();
                            $('#plot_border_width_row').hide();
                            $('#plot_border_color_row').hide();
                            $('#font_size_row').hide();
                            $('#font_name_row').hide();
                            $('#show_grid_row').hide();
                            $('.axes').hide();
                            $('.title').hide();
                            $('.tooltip').hide();
                            $('.legend').hide();
                            break;
                        case 'google_scatter_chart':
                            $('#inverted_row').hide();
                            break;
                    }
                    constructedChartData.min_columns = parseInt( $('#google_chart_type option:selected').data('min_columns') );
                    constructedChartData.max_columns = parseInt( $('#google_chart_type option:selected').data('max_columns') );
                }else if( $('#chart_render_engine').val() == 'highcharts' ){
                    $( ".google" ).hide();
                    $( ".highcharts" ).show();

                    $('#border_width_row').show();
                    $('#border_color_row').show();
                    $('#border_radius_row').show();
                    $('#zoom_type_row').show();
                    $('#panning_row').show();
                    $('#pan_key_row').show();
                    $('#plot_border_width_row').show();
                    $('#plot_border_color_row').show();
                    $('.axes').show();
                    $('.legend').show();

                    constructedChartData.chart_type = $('#highcharts_chart_type').val();
                    if(constructedChartData.chart_type == 'highcharts_pie_chart' || constructedChartData.chart_type == 'highcharts_pie_with_gradient_chart' || constructedChartData.chart_type == 'highcharts_3d_pie_chart' || constructedChartData.chart_type == 'highcharts_donut_chart' || constructedChartData.chart_type == 'highcharts_3d_donut_chart')  {
                        $('#border_width_row').hide();
                        $('#border_color_row').hide();
                        $('#border_radius_row').hide();
                        $('#zoom_type_row').hide();
                        $('#panning_row').hide();
                        $('#pan_key_row').hide();
                        $('#plot_border_width_row').hide();
                        $('#plot_border_color_row').hide();
                        $('.axes').hide();
                        $('.legend').hide();

                    }
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

                if( $( 'div.chosen_columns div.chart_column_block.string,' +
                        'div.chosen_columns div.chart_column_block.date,' +
                        'div.chosen_columns div.chart_column_block.datetime,' +
                        'div.chosen_columns div.chart_column_block.time' ).length
                    && ( !$('div.chosen_columns div.chart_column_block:eq(0)').hasClass('float')
                        || !$('div.chosen_columns div.chart_column_block:eq(0)').hasClass('int') ) ){
                    $( 'div.chosen_columns div.chart_column_block.string,' +
                        'div.chosen_columns div.chart_column_block.date,' +
                        'div.chosen_columns div.chart_column_block.datetime,' +
                        'div.chosen_columns div.chart_column_block.time' )
                        .eq(0)
                        .prependTo('div.chosen_columns_container')
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
                        success: function (data) {
                            $('div.chartWizardStep.step4').show(200);
                            $('div.chart_wizard_breadcrumbs_block.step4').addClass('active');

                            var seriesBlockTemplate = $.templates("#wdtChartSeriesSettingBlock");
                            var seriesBlockTemplateHtml = seriesBlockTemplate.render({series: data.series});

                            $('#series_settings_container').html(seriesBlockTemplateHtml);
                            for (var i in data.options.series) {
                                $('#series_settings_container div.chart_series_block:eq(' + i + ')').find('div.chart_series_color input').val(data.options.series[i].color);
                            }
                            $('#series_settings_container input.series_color').wpColorPicker();

                            if ($('#wpDataChartId').val() == '') {
                                $('#horizontal_axis_label').val(data.axes.major.label);
                                $('#vertical_axis_label').val(data.axes.minor.label);
                            }

                            // Chart
                            $('#background_color_container input.background_color').wpColorPicker();
                            $('#border_color_container input.border_color').wpColorPicker();
                            $('#plot_background_color_container input.plot_background_color').wpColorPicker();
                            $('#plot_border_color_container input.plot_border_color').wpColorPicker();
                            // Tooltip
                            $('#tooltip_background_color_container input.tooltip_background_color').wpColorPicker();
                            $('#tooltip_border_color_container input.tooltip_border_color').wpColorPicker();
                            // Legend
                            $('#legend_background_color_container input.legend_background_color').wpColorPicker();
                            $('#legend_border_color_container input.legend_border_color').wpColorPicker();
                            // Exporting
                            $('#exporting_button_color_container input.exporting_button_color').wpColorPicker();

                            $('#wdtPreloadLayer').hide();
                        }
                    });
                break;
            case 'step4':
                // Preview
                $('#wdtPreloadLayer').show();

                //Chart
                constructedChartData.width = parseInt( $('#chart_width').val() );
                constructedChartData.responsive_width = $('#chart_responsive_width').is(':checked') ? 1 : 0;
                constructedChartData.height = parseInt( $('#chart_height').val() );
                constructedChartData.group_chart = $('#group_chart').is(':checked') ? 1 : 0;
                constructedChartData.background_color = $('input.background_color').val();
                constructedChartData.border_width = $('#border_width').val();
                constructedChartData.border_color = $('input.border_color').val();
                constructedChartData.border_radius = $('#border_radius').val();
                constructedChartData.zoom_type = $('#zoom_type').val();
                constructedChartData.panning = $('#panning').is(':checked') ? 1 : 0;
                constructedChartData.pan_key = $('#pan_key').val();
                constructedChartData.plot_background_color = $('input.plot_background_color').val();
                constructedChartData.plot_background_image = $('#plot_background_image').val();
                constructedChartData.plot_border_width = $('#plot_border_width').val();
                constructedChartData.plot_border_color = $('input.plot_border_color').val();
                constructedChartData.font_size = $('#font_size').val();
                constructedChartData.font_name = $('#font_name').val();
                // Series
                if( typeof constructedChartData.series_data == 'undefined' ){
                    constructedChartData.series_data = {};
                }
                $('div.chart_series_block').each(function(e){
                    constructedChartData.series_data[$(this).data('orig_header')] = {
                        label: $(this).find('input.series_label').val(),
                        color: $(this).find('input.series_color').val()
                    }
                });
                constructedChartData.curve_type = $('#curve_type').is(':checked') ? 1 : 0;
                constructedChartData.three_d = $('#three_d').is(':checked') ? 1 : 0;
                // Axes
                constructedChartData.show_grid = $('#show_grid').is(':checked') ? 1 : 0;
                constructedChartData.highcharts_line_dash_style = $('#highcharts_line_dash_style').val();
                constructedChartData.horizontal_axis_label = $('#horizontal_axis_label').val();
                constructedChartData.horizontal_axis_crosshair = $('#horizontal_axis_crosshair').is(':checked') ? 1 : 0;
                constructedChartData.horizontal_axis_direction = $('#horizontal_axis_direction').val();
                constructedChartData.vertical_axis_label = $('#vertical_axis_label').val();
                constructedChartData.vertical_axis_crosshair = $('#vertical_axis_crosshair').is(':checked') ? 1 : 0;
                constructedChartData.vertical_axis_direction = $('#vertical_axis_direction').val();
                constructedChartData.vertical_axis_min = $('#vertical_axis_min').val();
                constructedChartData.vertical_axis_max = $('#vertical_axis_max').val();
                constructedChartData.inverted = $('#inverted').is(':checked') ? 1 : 0;
                // Title
                constructedChartData.show_title = $('#show_chart_title').is(':checked') ? 1 : 0;
                constructedChartData.title_floating = $('#title_floating').is(':checked') ? 1 : 0;
                constructedChartData.title_align = $('#title_align').val();
                constructedChartData.subtitle = $('#subtitle').val();
                constructedChartData.subtitle_align = $('#subtitle_align').val();
                // Tooltip
                constructedChartData.tooltip_enabled = $('#tooltip_enabled').is(':checked') ? 1 : 0;
                constructedChartData.tooltip_background_color = $('input.tooltip_background_color').val();
                constructedChartData.tooltip_border_width = $('#tooltip_border_width').val();
                constructedChartData.tooltip_border_color = $('input.tooltip_border_color').val();
                constructedChartData.tooltip_border_radius = $('#tooltip_border_radius').val();
                constructedChartData.tooltip_shared = $('#tooltip_shared').is(':checked') ? 1 : 0;
                constructedChartData.tooltip_value_prefix = $('#tooltip_value_prefix').val();
                constructedChartData.tooltip_value_suffix = $('#tooltip_value_suffix').val();
                // Legend
                constructedChartData.show_legend = $('#show_legend').is(':checked') ? 1 : 0;
                constructedChartData.legend_position = $('#legend_position').val();
                constructedChartData.legend_background_color = $('input.legend_background_color').val();
                constructedChartData.legend_title = $('#legend_title').val();
                constructedChartData.legend_layout = $('#legend_layout').val();
                constructedChartData.legend_align = $('#legend_align').val();
                constructedChartData.legend_vertical_align = $('#legend_vertical_align').val();
                constructedChartData.legend_border_width = $('#legend_border_width').val();
                constructedChartData.legend_border_color = $('input.legend_border_color').val();
                constructedChartData.legend_border_radius = $('#legend_border_radius').val();
                // Exporting
                constructedChartData.exporting = $('#exporting').is(':checked') ? 1 : 0;
                constructedChartData.exporting_data_labels = $('#exporting_data_labels').is(':checked') ? 1 : 0;
                constructedChartData.exporting_file_name = $('#exporting_file_name').val();
                constructedChartData.exporting_width = $('#exporting_width').val();
                constructedChartData.exporting_button_align = $('#exporting_button_align').val();
                constructedChartData.exporting_button_vertical_align = $('#exporting_button_vertical_align').val();
                constructedChartData.exporting_button_color = $('input.exporting_button_color').val();
                constructedChartData.exporting_button_text = $('#exporting_button_text').val();
                // Credits
                constructedChartData.credits = $('#credits').is(':checked') ? 1 : 0;
                constructedChartData.credits_href = $('#credits_href').val();
                constructedChartData.credits_text = $('#credits_text').val();

                    $.ajax({
                        url: ajaxurl,
                        data: {
                            action: 'wpdatatable_show_chart_from_data',
                            chart_data: constructedChartData
                        },
                        dataType: 'json',
                        type: 'post',
                        success: function (data) {
                            $('div.chartWizardStep.step5').show(200);
                            $('div.chart_wizard_breadcrumbs_block.step5').addClass('active');
                            if (constructedChartData.engine == 'google') {
                                var wdtChart = new wpDataTablesGoogleChart();
                                wdtChart.setType(data.type);
                                wdtChart.setColumns(data.columns);
                                wdtChart.setRows(data.rows);
                                wdtChart.setOptions(data.options);
                                wdtChart.setContainer('googleChartContainer');
                                wdtChart.setColumnIndexes(data.column_indexes);
                            } else if (constructedChartData.engine == 'highcharts') {
                                var wdtChart = new wpDataTablesHighchart();
                                wdtChart.setOptions(data.options);
                                wdtChart.setType(data.type);
                                wdtChart.setWidth(data.width);
                                wdtChart.setHeight(data.height);
                                wdtChart.setColumnIndexes(data.column_indexes);
                                wdtChart.setContainer('#googleChartContainer');
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
                        success: function (data) {
                            $('div.chartWizardStep.step6').show(200);
                            $('div.chart_wizard_breadcrumbs_block.step6').addClass('active');
                            $('#wdt_chart_shortcode_container').html(data.shortcode);
                            constructedChartData.chart_id = data.id;
                            $('#wpDataChartId').val(data.id)
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
     * Responsive width checkbox
     */
    $('#chart_responsive_width').change(function(e){
        if( $(this).is(':checked') ){
            $('#chart_width').val('0');
            $('#chart_width').prop('readonly','readonly');
        }else{
            $('#chart_width').prop('readonly','');
            $('#chart_width').val('400');
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
                || $(this).hasClass('date')
                || $(this).hasClass('datetime')
                || $(this).hasClass('time')
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
            // General settings
            if( editing_chart_data.engine == 'google' ){
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

            if( editing_chart_data.follow_filtering == 1 ){
                $('#followTableFiltering').prop('checked','checked');
            }else{
                $('#followTableFiltering').prop('checked','');
            }

            // Chart
            if( typeof editing_chart_data.render_data.options.width !== 'undefined' ){
                $('#chart_width').val( editing_chart_data.render_data.options.width );
            }else{
                $('#chart_responsive_width').prop('checked','checked');
                $('#chart_width').val(0);
                $('#chart_width').prop('readonly','readonly');
            }
            $('#chart_height').val( editing_chart_data.render_data.options.height );

            if( editing_chart_data.render_data.group_chart) {
                $('#group_chart').prop('checked','checked');
            } else {
                $('#group_chart').prop('checked','');
            }

            // Axes
            if (editing_chart_data.render_data.show_grid == null) {
                $('#show_grid').prop('checked', 'checked');
            } else {
                if (editing_chart_data.render_data.show_grid) {
                    $('#show_grid').prop('checked', 'checked');
                } else {
                    $('#show_grid').prop('checked', '');
                }
            }
            $('#horizontal_axis_label').val( editing_chart_data.render_data.options.hAxis.title );
            $('#vertical_axis_label').val( editing_chart_data.render_data.options.vAxis.title );

            // Title
            if( editing_chart_data.render_data.options.title ){
                $('#show_chart_title').prop('checked','checked');
            }else{
                $('#show_chart_title').prop('checked','');
            }

            if (editing_chart_data.engine == 'google') {
                // Chart
                if (editing_chart_data.render_data.options.backgroundColor == null) {
                    $('input.background_color').val('');
                    $('#border_width').val('');
                    $('input.border_color').val('');
                    $('#border_radius').val('');

                } else {
                    $('input.background_color').val(editing_chart_data.render_data.options.backgroundColor.fill);
                    $('#border_width').val(editing_chart_data.render_data.options.backgroundColor.strokeWidth);
                    $('input.border_color').val(editing_chart_data.render_data.options.backgroundColor.stroke);
                    $('#border_radius').val(editing_chart_data.render_data.options.backgroundColor.rx);
                }

                if (editing_chart_data.render_data.options.chartArea == null) {
                    $('input.plot_background_color').val('');
                    $('#plot_border_width').val('');
                    $('input.plot_border_color').val('');
                } else {
                    $('input.plot_background_color').val(editing_chart_data.render_data.options.chartArea.backgroundColor.fill);
                    $('#plot_border_width').val(editing_chart_data.render_data.options.chartArea.backgroundColor.strokeWidth);
                    $('input.plot_border_color').val(editing_chart_data.render_data.options.chartArea.backgroundColor.stroke);
                }

                if (editing_chart_data.render_data.options.fontSize == null) {
                    $('#font_size').val('');
                } else {
                    $('#font_size').val(editing_chart_data.render_data.options.fontSize);
                }
                if (editing_chart_data.render_data.options.fontName == null) {
                    $('#font_name').val('Arial');
                } else {
                    $('#font_name').val(editing_chart_data.render_data.options.fontName);
                }

                // Series
                if (editing_chart_data.render_data.options.is3D){
                    $('#three_d').prop('checked','checked');
                } else {
                    $('#three_d').prop('checked','');
                }

                // Axes
                if (editing_chart_data.render_data.options.crosshair == null) {
                    $('#horizontal_axis_crosshair').prop('checked', '');
                    $('#vertical_axis_crosshair').prop('checked', '');
                } else {
                    if (editing_chart_data.render_data.options.crosshair.orientation == 'both') {
                        $('#horizontal_axis_crosshair').prop('checked', 'checked');
                        $('#vertical_axis_crosshair').prop('checked', 'checked');
                    } else if (editing_chart_data.render_data.options.crosshair.orientation == 'horizontal') {
                        $('#horizontal_axis_crosshair').prop('checked', 'checked');
                        $('#vertical_axis_crosshair').prop('checked', '');
                    } else if (editing_chart_data.render_data.options.crosshair.orientation == 'vertical') {
                        $('#horizontal_axis_crosshair').prop('checked', '');
                        $('#vertical_axis_crosshair').prop('checked', 'checked');
                    }
                }


                if (editing_chart_data.render_data.options.hAxis.direction == null) {
                    $('#horizontal_axis_direction').val(1);
                } else {
                    $('#horizontal_axis_direction').val(editing_chart_data.render_data.options.hAxis.direction);
                }

                if (editing_chart_data.render_data.options.vAxis.direction == null) {
                    $('#vertical_axis_direction').val(1);
                } else {
                    $('#vertical_axis_direction').val(editing_chart_data.render_data.options.vAxis.direction);
                }

                if (editing_chart_data.render_data.options.vAxis.viewWindow == null) {
                    $('#vertical_axis_min').val('');
                    $('#vertical_axis_max').val('');
                } else {
                    $('#vertical_axis_min').val(editing_chart_data.render_data.options.vAxis.viewWindow.min);
                    $('#vertical_axis_max').val(editing_chart_data.render_data.options.vAxis.viewWindow.max);
                }

                if (editing_chart_data.render_data.options.orientation == null) {
                    $('#inverted').prop('checked', '');
                } else {
                    if (editing_chart_data.render_data.options.orientation == 'vertical') {
                        $('#inverted').prop('checked', 'checked');
                    }
                }

                // Title
                if (editing_chart_data.render_data.options.titlePosition == null) {
                    $('#title_floating').prop('checked', '');
                } else {
                    if (editing_chart_data.render_data.options.titlePosition == 'in') {
                        $('#title_floating').prop('checked', 'checked');
                    }
                }

                // Tooltip

                if (editing_chart_data.render_data.options.tooltip == null) {
                    $('#tooltip_enabled').prop('checked', 'checked');
                } else {
                    if (editing_chart_data.render_data.options.tooltip.trigger == 'none') {
                        $('#tooltip_enabled').prop('checked', '');
                    }
                }

                // Legend
                if (editing_chart_data.render_data.options.legend == null) {
                    $('#legend_position').val('right');
                    $('#legend_vertical_align').val("bottom");
                } else {
                    $('#legend_position').val(editing_chart_data.render_data.options.legend.position);
                    if (editing_chart_data.render_data.options.legend.alignment == 'end') {
                        $('#legend_vertical_align').val("bottom");
                    } else if (editing_chart_data.render_data.options.legend.alignment == 'center') {
                        $('#legend_vertical_align').val("middle");
                    } else {
                        $('#legend_vertical_align').val("top");
                    }
                }
            }

            else if (editing_chart_data.engine == 'highcharts') {


                if (editing_chart_data.highcharts_render_data == null) {
                    // Chart
                    $('input.background_color').val('');
                    $('#border_width').val(0);
                    $('input.border_color').val('');
                    $('#border_radius').val(0);
                    $('#zoom_type').val('none');
                    $('#panning').prop('checked','');
                    $('#pan_key').val('shift');
                    $('input.plot_background_color').val('');
                    $('#plot_background_image').val('');
                    $('#plot_border_width').val(0);
                    $('input.plot_border_color').val('');

                    // Axes
                    $('#highcharts_line_dash_style').val('solid');
                    $('#horizontal_axis_crosshair').prop('checked','');
                    $('#vertical_axis_crosshair').prop('checked','');
                    $('#vertical_axis_min').val('');
                    $('#vertical_axis_max').val('');
                    $('#inverted').prop('checked','');

                    // Title
                    $('#title_floating').prop('checked','');
                    $('#title_align').val('center');
                    $('#subtitle').val('');
                    $('#subtitle_align').val('center');

                    // Tooltip
                    $('#tooltip_enabled').prop('checked','checked');
                    $('input.tooltip_background_color').val('');
                    $('#tooltip_border_width').val(1);
                    $('input.tooltip_border_color').val('');
                    $('#tooltip_border_radius').val(3);
                    $('#tooltip_shared').prop('checked','');
                    $('#tooltip_value_prefix').val('');
                    $('#tooltip_value_suffix').val('');

                    // Legend
                    $('#show_legend').prop('checked','checked');
                    $('input.legend_background_color').val('');
                    $('#legend_title').val('');
                    $('#legend_layout').val('horizontal');
                    $('#legend_align').val('center');
                    $('#legend_vertical_align').val('bottom');
                    $('#legend_border_width').val(0);
                    $('input.legend_border_color').val('');
                    $('#legend_border_radius').val(0);

                    // Exporting
                    $('#exporting').prop('checked','checked');
                    $('#exporting_data_labels').prop('checked','');
                    $('#exporting_file_name').val('');
                    $('#exporting_width').val('');
                    $('#exporting_button_align').val('right');
                    $('#exporting_button_vertical_align').val('top');
                    $('input.exporting_button_color').val('');
                    $('#exporting_button_text').val('');

                    // Credits
                    $('#credits').prop('checked','checked');
                    $('#credits_href').val('http://www.highcharts.com');
                    $('#credits_text').val('Highcharts.com');


                } else {
                    // Chart
                    $('input.background_color').val(editing_chart_data.highcharts_render_data.options.chart.backgroundColor);
                    $('#border_width').val(editing_chart_data.highcharts_render_data.options.chart.borderWidth);
                    $('input.border_color').val(editing_chart_data.highcharts_render_data.options.chart.borderColor);
                    $('#border_radius').val(editing_chart_data.highcharts_render_data.options.chart.borderRadius);
                    $('#zoom_type').val(editing_chart_data.highcharts_render_data.options.chart.zoomType);
                    if( editing_chart_data.highcharts_render_data.options.chart.panning ) {
                        $('#panning').prop('checked','checked');
                    } else {
                        $('#panning').prop('checked','');
                    }
                    $('#pan_key').val(editing_chart_data.highcharts_render_data.options.chart.panKey);
                    $('input.plot_background_color').val(editing_chart_data.highcharts_render_data.options.chart.plotBackgroundColor);
                    $('#plot_background_image').val(editing_chart_data.highcharts_render_data.options.chart.plotBackgroundImage);
                    $('#plot_border_width').val(editing_chart_data.highcharts_render_data.options.chart.plotBorderWidth);
                    $('input.plot_border_color').val(editing_chart_data.highcharts_render_data.options.chart.plotBorderColor);
                    if( editing_chart_data.highcharts_render_data.options.credits.enabled ) {
                        $('#credits').prop('checked','checked');
                    } else {
                        $('#credits').prop('checked','');
                    }

                    // Axes
                    $('#highcharts_line_dash_style').val(editing_chart_data.highcharts_render_data.options.yAxis.gridLineDashStyle);
                    if( editing_chart_data.highcharts_render_data.options.xAxis.crosshair ) {
                        $('#horizontal_axis_crosshair').prop('checked','checked');
                    } else {
                        $('#horizontal_axis_crosshair').prop('checked','');
                    }
                    if( editing_chart_data.highcharts_render_data.options.yAxis.crosshair ) {
                        $('#vertical_axis_crosshair').prop('checked','checked');
                    } else {
                        $('#vertical_axis_crosshair').prop('checked','');
                    }
                    $('#vertical_axis_min').val(editing_chart_data.highcharts_render_data.options.yAxis.min);
                    $('#vertical_axis_max').val(editing_chart_data.highcharts_render_data.options.yAxis.max);
                    if( editing_chart_data.highcharts_render_data.options.chart.inverted ) {
                        $('#inverted').prop('checked','checked');
                    } else {
                        $('#inverted').prop('checked','');
                    }
                    // Title
                    if( editing_chart_data.highcharts_render_data.options.title.floating ){
                        $('#title_floating').prop('checked','checked');
                    } else{
                        $('#title_floating').prop('checked','');
                    }
                    $('#title_align').val(editing_chart_data.highcharts_render_data.options.title.align);
                    $('#subtitle').val(editing_chart_data.highcharts_render_data.options.subtitle.text);
                    $('#subtitle_align').val(editing_chart_data.highcharts_render_data.options.subtitle.align);

                    // Tooltip
                    if( editing_chart_data.highcharts_render_data.options.tooltip.enabled ){
                        $('#tooltip_enabled').prop('checked','checked');
                    } else {
                        $('#tooltip_enabled').prop('checked','');
                    }
                    $('input.tooltip_background_color').val(editing_chart_data.highcharts_render_data.options.tooltip.backgroundColor);
                    $('#tooltip_border_width').val(editing_chart_data.highcharts_render_data.options.tooltip.borderWidth);
                    $('input.tooltip_border_color').val(editing_chart_data.highcharts_render_data.options.tooltip.borderColor);
                    $('#tooltip_border_radius').val(editing_chart_data.highcharts_render_data.options.tooltip.borderRadius);
                    if( editing_chart_data.highcharts_render_data.options.tooltip.shared ){
                        $('#tooltip_shared').prop('checked','checked');
                    } else {
                        $('#tooltip_shared').prop('checked','');
                    }
                    $('#tooltip_value_prefix').val(editing_chart_data.highcharts_render_data.options.tooltip.valuePrefix);
                    $('#tooltip_value_suffix').val(editing_chart_data.highcharts_render_data.options.tooltip.valueSuffix);

                    // Legend
                    if( editing_chart_data.highcharts_render_data.options.legend.enabled ){
                        $('#show_legend').prop('checked','checked');
                    } else {
                        $('#show_legend').prop('checked','');
                    }
                    $('input.legend_background_color').val(editing_chart_data.highcharts_render_data.options.legend.backgroundColor);
                    $('#legend_title').val(editing_chart_data.highcharts_render_data.options.legend.title.text);
                    $('#legend_layout').val(editing_chart_data.highcharts_render_data.options.legend.layout);
                    $('#legend_align').val(editing_chart_data.highcharts_render_data.options.legend.align);
                    $('#legend_vertical_align').val(editing_chart_data.highcharts_render_data.options.legend.verticalAlign);
                    $('#legend_border_width').val(editing_chart_data.highcharts_render_data.options.legend.borderWidth);
                    $('input.legend_border_color').val(editing_chart_data.highcharts_render_data.options.legend.borderColor);
                    $('#legend_border_radius').val(editing_chart_data.highcharts_render_data.options.legend.borderRadius);

                    // Exporting
                    if( editing_chart_data.highcharts_render_data.options.exporting.enabled ){
                        $('#exporting').prop('checked','checked');
                    } else {
                        $('#exporting').prop('checked','');
                    }
                    if( editing_chart_data.highcharts_render_data.options.exporting.chartOptions.plotOptions.series.dataLabels.enabled ){
                        $('#exporting_data_labels').prop('checked','checked');
                    } else {
                        $('#exporting_data_labels').prop('checked','');
                    }
                    $('#exporting_file_name').val(editing_chart_data.highcharts_render_data.options.exporting.filename);
                    $('#exporting_width').val(editing_chart_data.highcharts_render_data.options.exporting.width);
                    $('#exporting_button_align').val(editing_chart_data.highcharts_render_data.options.exporting.buttons.contextButton.align);
                    $('#exporting_button_vertical_align').val(editing_chart_data.highcharts_render_data.options.exporting.buttons.contextButton.verticalAlign);
                    $('input.exporting_button_color').val(editing_chart_data.highcharts_render_data.options.exporting.buttons.contextButton.symbolStroke);
                    $('#exporting_button_text').val(editing_chart_data.highcharts_render_data.options.exporting.buttons.contextButton.text);

                    // Credits
                    if( editing_chart_data.highcharts_render_data.options.credits.enabled ) {
                        $('#credits').prop('checked','checked');
                    } else {
                        $('#credits').prop('checked','');
                    }
                    $('#credits_href').val(editing_chart_data.highcharts_render_data.options.credits.href);
                    $('#credits_text').val(editing_chart_data.highcharts_render_data.options.credits.text);
                }


            }

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