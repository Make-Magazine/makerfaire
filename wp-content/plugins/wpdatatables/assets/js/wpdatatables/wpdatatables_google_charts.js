google.load('visualization', '1', {packages: ['corechart', 'bar', 'gauge', 'scatter']});

var wpDataTablesGoogleChart = function(){

    var obj = {
        rows:  [],
        columns: [],
        type: 'column',
        containerId: 'googleChartContainer',
        columnIndexes: [],
        connectedWPDataTable: null,
        chart: null,
        googleDataTable: null,
        renderCallback: null,
        options: {
            animation: {
                duration: 1000,
                easing: 'out',
                startup: true
            },
            backgroundColor: {
                fill: '#FFFFFF',
                strokeWidth: 0,
                stroke: '#666',
                rx: 0
            },
            chartArea: {
                backgroundColor: {

                }

            },
            crosshair: {

            },
            curveType: 'none',
            fontName: 'Arial',
            fontSize: null,
            hAxis: {
                direction: 1
            },
            height: 400,
            legend: {
                position: 'right'
            },
            orientation: 'horizontal',
            titlePosition: 'out',
            tooltip: {
                trigger: 'focus'
            },
            vAxis: {
                direction: 1,
                viewWindow: {

                }
            }
        },
        setRows: function( rows ){
            this.rows = rows;
        },
        enableDateTimeAxis: function(){
            this.options.hAxis.gridlines = {
                count: -1,
                units: {
                    days: {format: ['MMM dd']},
                    hours: {format: ['HH:mm', 'ha']}
                }
            }
        },
        detectDates: function(){
            for( var i in this.columns ){
                if( this.columns[i].type == 'date' || this.columns[i].type == 'datetime' ){
                    for( var j in this.rows ){
                        var remDate = Date.parse( this.rows[j][i] );
                        if( isNaN(remDate) ){
                            this.rows[j][i] = new Date();
                        }else{
                            this.rows[j][i] = new Date(remDate);
                            if( this.connectedWPDataTable == null ){
                                this.rows[j][i].setTime( this.rows[j][i].getTime() + this.rows[j][i].getTimezoneOffset()*60*1000 );
                            } else {
                                this.rows[j][i].setTime( this.rows[j][i].getTime() );
                            }
                        }
                        if( this.columns[i].type == 'datetime' ){
                            this.enableDateTimeAxis();
                        }
                    }
                }
            }
        },
        setColumns: function( columns ){
            this.columns = columns;
        },
        getColumns: function(){
            return this.columns;
        },
        setOptions: function( options ){
            for( var i in options ){
                if( i == 'responsive_width' && options[i] == '1' ){
                    obj.options.animation = false;
                    jQuery(window).resize(function(){
                        obj.chart.draw( obj.googleDataTable, obj.options );
                    });
                    continue;
                }
                this.options[i] = options[i];
            }
        },
        getOptions: function(){
            return this.options;
        },
        setType: function( type ){
            this.type = type;
        },
        getType: function(){
            return this.type;
        },
        setGrouping: function( group_chart ){
            this.group_chart = group_chart;
        },
        setContainer: function( containerId ){
            this.containerId = containerId;
        },
        getContainer: function(){
            return this.containerId;
        },
        setRenderCallback: function( callback ){
            this.renderCallback = callback;
        },
        render: function(){
            this.googleDataTable = new google.visualization.DataTable();
            for( var i in this.columns ){
                this.googleDataTable.addColumn( this.columns[i] );
            }
            this.detectDates();

            this.googleDataTable.addRows( this.rows );
            switch( this.type ){
                case 'google_column_chart':
                    this.chart = new google.visualization.ColumnChart( document.getElementById( this.containerId ) );
                    break;
                case 'google_histogram':
                    this.chart = new google.visualization.Histogram( document.getElementById( this.containerId ) );
                    break;
                case 'google_bar_chart':
                    this.options.orientation = 'vertical';
                    this.chart = new google.visualization.BarChart( document.getElementById( this.containerId ) );
                    break;
                case 'google_area_chart':
                    this.chart = new google.visualization.AreaChart( document.getElementById( this.containerId ) );
                    break;
                case 'google_stepped_area_chart':
                    this.options.isStacked = true;
                    this.chart = new google.visualization.SteppedAreaChart( document.getElementById( this.containerId ) );
                    break;
                case 'google_line_chart':
                    this.chart = new google.visualization.LineChart( document.getElementById( this.containerId ) );
                    break;
                case 'google_pie_chart':
                    this.chart = new google.visualization.PieChart( document.getElementById( this.containerId ) );
                    break;
                case 'google_bubble_chart':
                    this.chart = new google.visualization.BubbleChart( document.getElementById( this.containerId ) );
                    break;
                case 'google_donut_chart':
                    this.options.pieHole = 0.4;
                    this.chart = new google.visualization.PieChart( document.getElementById( this.containerId ) );
                    break;
                case 'google_gauge_chart':
                    this.options.redFrom = 90;
                    this.options.redTo = 100;
                    this.options.yellowFrom = 75;
                    this.options.yellowTo = 90;
                    this.options.minorTicks = 5;
                    this.chart = new google.visualization.Gauge( document.getElementById( this.containerId ) );
                    break;
                case 'google_scatter_chart':
                    this.chart = new google.visualization.ScatterChart( document.getElementById( this.containerId ) );
                    break;
                case 'google_candlestick_chart':
                    this.chart = new google.visualization.CandlestickChart( document.getElementById( this.containerId ) );
                    break;
            }
            if( this.renderCallback !== null ){
                this.renderCallback( this );
            }
            this.chart.draw( this.googleDataTable, this.options );
        },
        refresh: function(){
            this.googleDataTable = new google.visualization.DataTable();
            for( var i in this.columns ){
                this.googleDataTable.addColumn( this.columns[i] );
            }
            this.detectDates();
            this.googleDataTable.addRows( this.rows );
            if( this.renderCallback !== null ){
                this.renderCallback( this );
            }
            this.chart.draw( this.googleDataTable, this.options );
        },
        setConnectedWPDataTable: function( wpDataTable ){
            this.connectedWPDataTable = wpDataTable;
        },
        getConnectedWPDataTable: function(){
            return this.connectedWPDataTable;
        },
        enableFollowFiltering: function(){
            if( this.connectedWPDataTable == null ){ return; }

            this.connectedWPDataTable.fnSettings().aoDrawCallback.push({
                sName: 'chart_filter_follow',
                fn: function( oSettings ){
                    var rowsToRender = [];

                    obj.options.animation = false;

                    var filteredData = obj.connectedWPDataTable._('tr', {"filter": "applied"}).toArray();
                    var datepickFormat = jQuery.parseJSON( jQuery( '#'+obj.connectedWPDataTable.data('described-by') ).val() ).datepickFormat;
                    var momentDateTimeFormat = datepickFormat.replace(/dd/,'DD').replace(/Y/g, 'YYYY').replace(/y/g,'YY').replace(/M/g,'mmm').replace(/mm/g, 'MM');
                    momentDateTimeFormat += ' '+ jQuery.parseJSON( jQuery( '#'+obj.connectedWPDataTable.data('described-by') ).val() ).timeFormat.replace('i','mm');
                    var numberFormat = jQuery.parseJSON( jQuery( '#'+obj.connectedWPDataTable.data('described-by') ).val() ).number_format;

                    for( var i in filteredData ){

                        var rowEntry = [];
                        for( var j in obj.columnIndexes ) {
                            if (obj.columns[j].type == 'number') {
                                var value = filteredData[i][obj.columnIndexes[j]];
                                if (isNaN(value)) {
                                    if (numberFormat == 1) {
                                        var thousandsSeparator = '.';
                                        var decimalSeparator = ',';
                                    } else {
                                        var thousandsSeparator = ',';
                                        var decimalSeparator = '.';
                                    }
                                    value = wdtUnformatNumber(value, thousandsSeparator, decimalSeparator, true);
                                }
                                rowEntry.push(parseFloat(value));
                            } else if (obj.columns[j].type == 'date') {
                                rowEntry.push(jQuery.datepicker.parseDate(datepickFormat, filteredData[i][obj.columnIndexes[j]]));
                            } else if( obj.columns[j].type == 'datetime' ){
                                rowEntry.push( moment( filteredData[i][obj.columnIndexes[j]], momentDateTimeFormat ).toDate() );
                                obj.enableDateTimeAxis();
                            } else {
                                rowEntry.push( filteredData[i][obj.columnIndexes[j]] );
                            }
                        }
                        rowsToRender.push( rowEntry );
                    }

                    if ( obj.group_chart == 1 ) {
                        var output = [];
                        for (var i in rowsToRender){
                            if (typeof output !== 'undefined' && output.length > 0) {
                                var value_key = 'none';
                                for(var j in output){
                                    if(value_key === 'none'){
                                        if(output[j][0] == rowsToRender[i][0]){
                                            value_key = j;
                                        }
                                    }
                                }
                                if (value_key === 'none') {
                                    output.push(rowsToRender[i]);
                                } else {
                                    for(var n in rowsToRender[i]) {
                                        if( n != 0){
                                            output[value_key][n] += rowsToRender[i][n];
                                        }
                                    }
                                }
                            } else {
                                output.push(rowsToRender[i]);
                            }
                        }
                        rowsToRender = output;
                    }
                    obj.rows = rowsToRender;
                    obj.refresh();
                }
            });
        },
        setColumnIndexes: function( columnIndexes ){
            this.columnIndexes = columnIndexes;
        },
        getColumnIndexes: function(){
            return this.columnIndexes;
        }

    }

    return obj;

}
