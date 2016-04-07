(function($){
    $(window).load(function(){

        for( var chart_id in wpDataCharts ){

            if( wpDataCharts[chart_id].engine == 'google' ){
                var wdtChart = new wpDataTablesGoogleChart();
                wdtChart.setType( wpDataCharts[chart_id].render_data.type );
                wdtChart.setColumns( wpDataCharts[chart_id].render_data.columns );
                wdtChart.setRows( wpDataCharts[chart_id].render_data.rows );
                wdtChart.setOptions( wpDataCharts[chart_id].render_data.options );
                wdtChart.setContainer( wpDataCharts[chart_id].container );
                wdtChart.setColumnIndexes( wpDataCharts[chart_id].render_data.column_indexes );
                if( typeof wpDataChartsCallbacks !== 'undefined' && typeof wpDataChartsCallbacks[chart_id] !== 'undefined' ){
                    wdtChart.setRenderCallback( wpDataChartsCallbacks[chart_id] );
                }
                wdtChart.render();
            }else{
                var wdtChart = new wpDataTablesHighchart();
                wdtChart.setOptions( wpDataCharts[chart_id].render_data.options );
                wdtChart.setType( wpDataCharts[chart_id].render_data.type );
                wdtChart.setWidth( wpDataCharts[chart_id].render_data.width );
                wdtChart.setHeight( wpDataCharts[chart_id].render_data.height );
                wdtChart.setColumnIndexes( wpDataCharts[chart_id].render_data.column_indexes );
                wdtChart.setContainer( '#'+wpDataCharts[chart_id].container );
                if( typeof wpDataChartsCallbacks !== 'undefined' && typeof wpDataChartsCallbacks[chart_id] !== 'undefined' ){
                    wdtChart.setRenderCallback( wpDataChartsCallbacks[chart_id] );
                }
                if( wpDataCharts[chart_id].follow_filtering != 1 ) {
                    wdtChart.render();
                }
            }

            if( wpDataCharts[chart_id].follow_filtering == 1 ){
                // Find the wpDataTable object
                var $wdtable = $('table.wpDataTable[data-wpdatatable_id='+wpDataCharts[chart_id].wpdatatable_id+']');
                if( $wdtable.length > 0 ){
                    var wdtObj = wpDataTables[$wdtable.get(0).id];
                    wdtChart.setConnectedWPDataTable( wdtObj );
                    wdtChart.enableFollowFiltering();
                    wdtObj.fnDraw();
                }else{
                    wdtChart.render();
                }
            }

        }

    })

})(jQuery);