var wpDataTablesHighchart = function(){

    var obj = {
        container: '#wdtHighChartContainer',
        columnIndexes: [],
        numberFormat: 1,
        connectedWPDataTable: null,
        renderCallback: null,
        chart: null,
        setContainer: function(container){
            this.container = container;
            this.options.chart.renderTo = container.replace('#','');
        },
        pieChartTypes: [
            'highcharts_pie_chart',
            'highcharts_pie_with_gradient_chart',
            'highcharts_3d_pie_chart',
            'highcharts_donut_chart',
            'highcharts_3d_donut_chart'
        ],
        getContainer: function(){
            return this.container;
        },
        setWidth: function( width ){
            this.options.chart.width = parseInt( width );
        },
        getWidth: function(){
            return this.options.chart.width;
        },
        setHeight: function( height ){
            this.options.chart.height = parseInt( height );
        },
        getHeight: function(){
            return this.options.chart.height;
        },
        setRenderCallback: function( callback ){
            this.renderCallback = callback;
        },
        options: {
            chart: {
                type: 'line',
                width: 400,
                height: 400
            },
            title: {
                text: 'Chart title',
                x: -20
            },
            xAxis: {

            },
            yAxis: {
                title: {
                    "text": ""
                },
                plotLines: [
                    {
                        "value": 0,
                        "width": 1,
                        "color": "#808080"
                    }
                ]
            },
            legend: {
                layout: 'horizontal',
                align: 'right'
            },
            series: [

            ],
            plotOptions: {

            }
        },
        setOptions: function( options ){
            for( var property in options ){
                this.options[property] = options[property];
            }
        },
        getOptions: function(){
            return this.options;
        },
        render: function(){
            if( this.renderCallback !== null ){
                this.renderCallback( this );
            }
            this.chart = new Highcharts.Chart( this.options );
        },
        setType: function( type ){
            switch( type ){
                case 'highcharts_basic_area_chart':
                    this.options.chart.type = 'area';
                    break;
                case 'highcharts_stacked_area_chart':
                    this.options.chart.type = 'area';
                    this.options.plotOptions = { area: { stacking: 'normal' } };
                    break;
                case 'highcharts_basic_bar_chart':
                    this.options.chart.type = 'bar';
                    break;
                case 'highcharts_scatter_plot':
                    this.options.chart.type = 'scatter';
                    break;
                case 'highcharts_stacked_bar_chart':
                    this.options.chart.type = 'bar';
                    this.options.plotOptions = { series: { stacking: 'normal' } };
                    break;
                case 'highcharts_basic_column_chart':
                    this.options.chart.type = 'column';
                    break;
                case 'highcharts_3d_column_chart':
                    this.options.chart.type = 'column';
                    this.options.chart.margin = 75;
                    this.options.chart.options3d = {
                        enabled: true,
                        alpha: 15,
                        beta: 15,
                        viewDistance: 25,
                        depth: 40
                    };
                    this.options.plotOptions = { column: { depth: 25 } };
                    break;
                case 'highcharts_stacked_column_chart':
                    this.options.chart.type = 'column';
                    this.options.plotOptions = { column: { stacking: 'normal' } };
                    break;
                case 'highcharts_pie_chart':
                    this.options.chart.type = 'pie';
                    this.options.plotOptions = {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    };
                    this.options.tooltip = {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    };
                    break;
                case 'highcharts_3d_pie_chart':
                    this.options.chart.type = 'pie';
                    this.options.plotOptions = {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            },
                            depth: 35
                        }
                    };
                    this.options.tooltip = {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    };
                    this.options.chart.options3d = {
                        enabled: true,
                        alpha: 15,
                        beta: 15,
                        viewDistance: 25,
                        depth: 40
                    };
                    break;
                case 'highcharts_pie_with_gradient_chart':
                    this.options.chart.type = 'pie';
                    // Radialize the colors
                    Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function (color) {
                        return {
                            radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
                            stops: [
                                [0, color],
                                [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
                            ]
                        };
                    });
                    this.options.plotOptions = {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    };
                    this.options.tooltip = {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    };
                    break;
                case 'highcharts_donut_chart':
                    this.options.chart.type = 'pie';
                    this.options.plotOptions = {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    };
                    this.options.series[0].innerSize = '80%';
                    this.options.tooltip = {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    };
                    break;
                case 'highcharts_3d_donut_chart':
                    this.options.chart.type = 'pie';
                    this.options.chart.options3d = {
                        enabled: true,
                        alpha: 15,
                        beta: 15,
                        viewDistance: 25,
                        depth: 40
                    };
                    this.options.plotOptions = {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            },
                            depth: 35
                        }
                    };
                    this.options.series[0].innerSize = '80%';
                    this.options.tooltip = {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    };
                    break;
                case 'highcharts_gauge_chart':
                    this.options.chart.type = 'gauge';
                    break;
                case 'highcharts_solid_gauge_chart':
                    this.options.chart.type = 'gauge';
                    break;
                case 'highcharts_line_chart':
                default:
                    this.options.chart.type = 'line';
                    break;
            }
        },
        refresh: function(){
            this.chart.redraw();
        },
        setColumnIndexes: function( columnIndexes ){
            this.columnIndexes = columnIndexes;
        },
        getColumnIndexes: function(){
            return this.columnIndexes;
        },
        setConnectedWPDataTable: function( wpDataTable ){
            this.connectedWPDataTable = wpDataTable;
        },
        getConnectedWPDataTable: function(){
            return this.connectedWPDataTable;
        },
        enableFollowFiltering: function(){
            if( this.connectedWPDataTable == null ){ return; }
            this.options.plotOptions.series = {animation: false};
            this.numberFormat = jQuery.parseJSON( jQuery( '#'+this.connectedWPDataTable.data('described-by') ).val() ).number_format;
            this.connectedWPDataTable.fnSettings().aoDrawCallback.push({
                sName: 'chart_filter_follow',
                fn: function( oSettings ){
                    obj.options.xAxis.categories = [];
                    var serieIndex = 0;
                    var filteredData = obj.connectedWPDataTable._('tr', {"filter": "applied"}).toArray();
                    for( var j in obj.columnIndexes ){
                        var seriesDataEntry = [];
                        if( ( obj.columnIndexes.length > 0 )
                            && ( j == 0 ) ){
                            for( var i in filteredData ){
                                obj.options.xAxis.categories.push( filteredData[i][obj.columnIndexes[j]] );
                            }
                        }else{
                            for( var i in filteredData ){
                                var entry = filteredData[i][obj.columnIndexes[j]];
                                if( obj.pieChartTypes.indexOf( obj.type ) !== -1 ){
                                    if( obj.numberFormat == 1 ){
                                        seriesDataEntry.push( parseFloat( entry.replace( '.','' ) ) );
                                    }else{
                                        seriesDataEntry.push( parseFloat( entry.replace( ',','' ) ) );
                                    }
                                }else{
                                    if( obj.numberFormat == 1 ){
                                        seriesDataEntry.push({
                                            name: obj.options.xAxis.categories[i],
                                            y: parseFloat( entry.replace( '.','' ) )
                                        });
                                    }else{
                                        seriesDataEntry.push({
                                            name: obj.options.xAxis.categories[i],
                                            y: parseFloat(entry.replace(',', ''))
                                        });
                                    }
                                }
                            }
                            obj.options.series[serieIndex].data = seriesDataEntry;
                            serieIndex++;
                        }
                    }
                    if( obj.chart !== null ){
                        obj.chart.destroy();
                    }
                    if( obj.renderCallback !== null ){
                        obj.renderCallback( obj );
                    }
                    obj.chart = new Highcharts.Chart( obj.options );
                }
            });
        }
    }

    return obj;

};