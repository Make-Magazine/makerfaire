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
            width: 400,
            height: 400,
            animation: {
                duration: 1000,
                easing: 'out',
                startup: true
            }
        },
        setRows: function( rows ){
            this.rows = rows;
        },
        detectDates: function(){
            for( var i in this.columns ){
                if( this.columns[i].type == 'date' ){
                    for( var j in this.rows ){
                        var remDate = Date.parse( this.rows[j][i] )
                        if( isNaN(remDate) ){
                            this.rows[j][i] = new Date();
                        }else{
                            this.rows[j][i] = new Date(remDate);
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
                    this.options.bars = 'horizontal';
                    this.chart = new google.charts.Bar( document.getElementById( this.containerId ) );
                    break;
                case 'google_area_chart':
                    this.chart = new google.visualization.AreaChart( document.getElementById( this.containerId ) );
                    break;
                case 'google_stepped_area_chart':
                    this.options.isStacked = true;
                    this.chart = new google.visualization.SteppedAreaChart( document.getElementById( this.containerId ) );
                    break;
                case 'google_line_chart':
                    this.options.curveType = 'function';
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
                    this.chart = new google.charts.Scatter( document.getElementById( this.containerId ) );
                    this.options = google.charts.Scatter.convertOptions( this.options );
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
                    var numberFormat = jQuery.parseJSON( jQuery( '#'+obj.connectedWPDataTable.data('described-by') ).val() ).number_format;

                    for( var i in filteredData ){

                        var rowEntry = [];
                        for( var j in obj.columnIndexes ){
                            if( obj.columns[j].type == 'number' ){
                                var value = filteredData[i][obj.columnIndexes[j]];
                                if( isNaN( value ) ) {
                                    if (numberFormat == 1) {
                                        value = value.replace(/\./g, '').replace(',', '.');
                                    } else {
                                        value = value.replace(/,/g, '');
                                    }
                                }
                                rowEntry.push( parseFloat( value ) );
                            }else if( obj.columns[j].type == 'date' ){
                                rowEntry.push( jQuery.datepicker.parseDate( datepickFormat, filteredData[i][obj.columnIndexes[j]] ) );
                            }else{
                                rowEntry.push( filteredData[i][obj.columnIndexes[j]] );
                            }
                        }
                        rowsToRender.push( rowEntry );
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