<?php if( isset( $chartObj ) ) { ?>
<script type='text/javascript'>var editing_chart_data= {render_data: <?php echo json_encode( $chartObj->getRenderData() ); ?>, engine: "<?php echo $chartObj->getEngine();?>", type: "<?php echo $chartObj->getType(); ?>", selected_columns: <?php echo json_encode( $chartObj->getSelectedColumns() ) ?>, range_type: "<?php echo $chartObj->getRangeType() ?>"<?php if( $chartObj->getRangeType() == 'picked_range' ){ ?>, row_range: <?php echo json_encode( $chartObj->getRowRange() ); } ?>, title: "<?php echo $chartObj->getTitle(); ?>", follow_filtering: <?php echo (int) $chartObj->getFollowFiltering(); ?>, wpdatatable_id: <?php echo $chartObj->getwpDataTableId(); ?>, show_chart_title: <?php echo (int) $chartObj->getShowTitle(); ?> };</script>
<?php } ?>

<div class="wpDataTables metabox-holder">
    <div id="wdtPreloadLayer" class="overlayed">
    </div>
    
    <div class="wrap">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder">
                    <div id="postbox-container-1" class="postbox-container">
                        <img src="<?php echo dirname(plugin_dir_url(__FILE__)); ?>/assets/img/wpdatatables-logo.png" class="wpdatatables_logo" />
                        <p><i><?php _e('Please refer to','wpdatatables');?> <a href="http://wpdatatables.com/documentation/wpdatacharts/"><?php _e('wpDataTables documentation on Charts','wpdatatables');?></a> <?php _e('if you have some questions or problems with the plugin.','wpdatatables'); ?></i></p>
                        <h2><?php _e('wpDataTables Chart Wizard','wpdatatables'); ?></h2>

                        <input type="hidden" id="wpDataChartId" value="<?php echo $chart_id ?>" />
                        <input type="hidden" id="wdtBrowseChartsURL" value="<?php echo admin_url( 'admin.php?page=wpdatatables-charts' ); ?>" />
                        
                        <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                            <div class="postbox">
                                <div class="handlediv" title="<?php _e('Click to toggle','wpdatatables'); ?>"><br/></div>
                                <h3 class="hndle">
                                    <span><div class="dashicons dashicons-edit"></div> <?php _e('Chart Creation Wizard','wpdatatables'); ?></span>
                                </h3>
                                <div class="inside">
                                    
                                    <div class="chart_wizard_breadcrumbs">
                                        <div class="chart_wizard_breadcrumbs_block active step1">
                                            <?php _e( 'Chart title & type', 'wpdatatables' ); ?>
                                        </div>
                                        <span class="chart_wizard_breadcrumbs_separator"> &gt; </span>
                                        <div class="chart_wizard_breadcrumbs_block step2">
                                            <?php _e( 'Data source', 'wpdatatables' ); ?>
                                        </div>
                                        <span class="chart_wizard_breadcrumbs_separator"> &gt; </span>
                                        <div class="chart_wizard_breadcrumbs_block step3">
                                            <?php _e( 'Data range', 'wpdatatables' ); ?>
                                        </div>
                                        <span class="chart_wizard_breadcrumbs_separator"> &gt; </span>
                                        <div class="chart_wizard_breadcrumbs_block step4">
                                            <?php _e( 'Formatting', 'wpdatatables' ); ?>
                                        </div>
                                        <span class="chart_wizard_breadcrumbs_separator"> &gt; </span>
                                        <div class="chart_wizard_breadcrumbs_block step5">
                                            <?php _e( 'Preview', 'wpdatatables' ); ?>
                                        </div>
                                        <span class="chart_wizard_breadcrumbs_separator"> &gt; </span>
                                        <div class="chart_wizard_breadcrumbs_block step6">
                                            <?php _e( 'Save and get shortcode', 'wpdatatables' ); ?>
                                        </div>
                                    </div>

                                    <div class="steps">

                                        <div class="chartWizardStep step1" data-step="step1">
                                            <h3><?php _e('Chart title, rendering engine and type','wpdatatables'); ?></h3>
                                            <fieldset style="margin: 10px;">
                                                    <table>
                                                    <tr>
                                                            <td style="width: 250px">
                                                                    <label for="chart_name"><span><strong><?php _e('Chart name','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('What is the title of the chart that you will use to identify it?','wpdatatables');?>.</small></span>
                                                            </td>
                                                            <td>
                                                                    <input id="chart_name" type="text" value="<?php echo empty( $chart_id ) ? __( 'New wpDataTable Chart', 'wpdatatables' ) : $chartObj->getTitle(); ?>" />
                                                            </td>
                                                    </tr>
                                                    <tr>
                                                            <td>
                                                                    <label for="table_name"><span><strong><?php _e('Chart render engine','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Please choose the render engine.','wpdatatables');?> <strong><?php _e('Please note that HighCharts license is NOT included with wpDataTables and you must purchase the license separately on','wpdatatables');?> <a http://highcharts.com/>http://highcharts.com</a></strong></small></span>
                                                            </td>
                                                            <td>
                                                                <select id="chart_render_engine">
                                                                    <option value="" <?php echo empty( $chart_id ) ? 'selected="selected"' : ''; ?> ><?php _e('Pick the render engine','wpdatatables'); ?></option>
                                                                    <option value="google" <?php if( !empty( $chart_id ) && ( $chartObj->getEngine() == 'google' ) ){ ?>selected="selected"<?php } ?> >Google Charts</option>
                                                                    <option value="highcharts" <?php if( !empty( $chart_id ) && ( $chartObj->getEngine() == 'highcharts' ) ){ ?>selected="selected"<?php } ?> >HighCharts</option>
                                                                </select>
                                                            </td>
                                                    </tr>
                                                    <tr class="charts_type google_charts_type" style="display: none">
                                                        <td colspan="2">
                                                            <label for="google_chart_type"><span><strong><?php _e('Pick a Google chart type','wpdatatables');?></strong></span></label><br/><br/>
                                                            <select id="google_chart_type" style="display: none !important;">
                                                                <option value="google_column_chart" data-min_columns="2" data-max_columns="0" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/google_column_chart.jpg"><?php _e( 'Column chart', 'wpdatatables' ); ?></option>
                                                                <option value="google_histogram" data-min_columns="2" data-max_columns="0" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/google_histogram.jpg"><?php _e( 'Histogram', 'wpdatatables' ); ?></option>
                                                                <option value="google_bar_chart" data-min_columns="2" data-max_columns="0" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/google_bar_chart.jpg"><?php _e( 'Bar chart', 'wpdatatables' ); ?></option>
                                                                <option value="google_area_chart" data-min_columns="2" data-max_columns="0" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/google_area_chart.jpg"><?php _e( 'Area chart', 'wpdatatables' ); ?></option>
                                                                <option value="google_stepped_area_chart" data-min_columns="2" data-max_columns="0" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/google_stepped_area_chart.jpg"><?php _e( 'Stepped area chart', 'wpdatatables' ); ?></option>
                                                                <option value="google_line_chart" data-min_columns="2" data-max_columns="0" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/google_line_chart.jpg"><?php _e( 'Line chart', 'wpdatatables' ); ?></option>
                                                                <option value="google_pie_chart" data-min_columns="2" data-max_columns="2" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/google_pie_chart.jpg"><?php _e( 'Pie chart', 'wpdatatables' ); ?></option>
                                                                <option value="google_bubble_chart" data-min_columns="2" data-max_columns="0" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/google_bubble_chart.jpg"><?php _e( 'Bubble chart', 'wpdatatables' ); ?></option>
                                                                <option value="google_donut_chart" data-min_columns="2" data-max_columns="0" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/google_donut_chart.jpg"><?php _e( 'Donut chart', 'wpdatatables' ); ?></option>
                                                                <option value="google_gauge_chart" data-min_columns="1" data-max_columns="2" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/google_gauge_chart.jpg"><?php _e( 'Gauge chart', 'wpdatatables' ); ?></option>
                                                                <option value="google_scatter_chart" data-min_columns="2" data-max_columns="0" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/google_scatter_chart.jpg"><?php _e( 'Scatter chart', 'wpdatatables' ); ?></option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr class="charts_type highcharts_charts_type" style="display: none !important;">
                                                        <td colspan="2">
                                                            <label for="highcharts_chart_type"><span><strong><?php _e('Pick a Highcharts chart type','wpdatatables');?></strong></span></label><br/><br/>
                                                            <select id="highcharts_chart_type" style="display: none !important;">
                                                                <option value="highcharts_line_chart"  data-min_columns="2" data-max_columns="0" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/highcharts_line_chart.jpg"><?php _e( 'Line chart', 'wpdatatables' ); ?></option>
                                                                <option value="highcharts_basic_area_chart" data-min_columns="2" data-max_columns="0" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/highcharts_basic_area_chart.jpg"><?php _e( 'Basic area chart', 'wpdatatables' ); ?></option>
                                                                <option value="highcharts_stacked_area_chart" data-min_columns="2" data-max_columns="0" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/highcharts_stacked_area_chart.jpg"><?php _e( 'Stacked area chart', 'wpdatatables' ); ?></option>
                                                                <option value="highcharts_basic_bar_chart" data-min_columns="2" data-max_columns="0" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/highcharts_basic_bar_chart.jpg"><?php _e( 'Basic bar chart', 'wpdatatables' ); ?></option>
                                                                <option value="highcharts_stacked_bar_chart" data-min_columns="2" data-max_columns="0" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/highcharts_stacked_bar_chart.jpg"><?php _e( 'Stacked bar chart', 'wpdatatables' ); ?></option>
                                                                <option value="highcharts_basic_column_chart" data-min_columns="2" data-max_columns="0" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/highcharts_basic_column_chart.jpg"><?php _e( 'Basic column chart', 'wpdatatables' ); ?></option>
                                                                <option value="highcharts_stacked_column_chart" data-min_columns="2" data-max_columns="0" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/highcharts_stacked_column_chart.jpg"><?php _e( 'Stacked column chart', 'wpdatatables' ); ?></option>
                                                                <option value="highcharts_pie_chart" data-min_columns="2" data-max_columns="2" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/highcharts_pie_chart.jpg"><?php _e( 'Pie chart', 'wpdatatables' ); ?></option>
                                                                <option value="highcharts_pie_with_gradient_chart" data-min_columns="2" data-max_columns="2" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/highcharts_pie_with_gradient_chart.jpg"><?php _e( 'Pie with gradient chart', 'wpdatatables' ); ?></option>
                                                                <option value="highcharts_donut_chart" data-min_columns="2" data-max_columns="2" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/highcharts_donut_chart.jpg"><?php _e( 'Donut chart', 'wpdatatables' ); ?></option>
                                                                <option value="highcharts_scatter_plot" data-min_columns="2" data-max_columns="0" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/highcharts_scatter_plot.jpg"><?php _e( 'Scatter plot', 'wpdatatables' ); ?></option>
                                                                <option value="highcharts_3d_column_chart" data-min_columns="2" data-max_columns="0" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/highcharts_3d_column_chart.jpg"><?php _e( '3D column chart', 'wpdatatables' ); ?></option>
                                                                <option value="highcharts_3d_pie_chart" data-min_columns="2" data-max_columns="2" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/highcharts_3d_pie_chart.jpg"><?php _e( '3D pie chart', 'wpdatatables' ); ?></option>
                                                                <option value="highcharts_3d_donut_chart" data-min_columns="2" data-max_columns="2" data-img-src="<?php echo WDT_ASSETS_PATH?>img/chart_thumbs/highcharts_3d_donut_chart.jpg"><?php _e( '3D donut chart', 'wpdatatables' ); ?></option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    </table>
                                            </fieldset>
                                        </div>

                                        <div class="chartWizardStep step2" data-step="step2" style="display: none">
                                            <h3><?php _e('Data source','wpdatatables'); ?></h3>
                                            <fieldset style="margin: 10px;">
                                                    <table>
                                                    <tr>
                                                            <td style="width: 250px">
                                                                    <label for="wpdatatables_chart_source"><span><strong><?php _e('wpDataTable Data source','wpdatatables');?></strong></span></label><br/>
                                                                    <span class="description"><small><?php _e('Please pick a wpDataTable which will be used as a data source','wpdatatables');?>.</small></span>
                                                            </td>
                                                            <td>
                                                                <select id="wpdatatables_chart_source">
                                                                    <option value=""><?php _e('Pick a wpDataTable','wpdatatables'); ?></option>
                                                                    <?php foreach( wdt_get_all_tables_nonpaged() as $table ){ ?>
                                                                    <option value="<?php echo $table['id'] ?>"><?php echo $table['title'] ?> (id: <?php echo $table['id']; ?>)</option>
                                                                    <?php } ?>
                                                                </select>
                                                            </td>
                                                    </tr>
                                                    </table>
                                            </fieldset>
                                        </div>
                                        
                                        <div class="chartWizardStep step3" data-step="step3" style="display: none">
                                            <fieldset style="margin: 10px;">
                                                    <table style="width: 100%">
                                                    <tr>
                                                            <td colspan="2">
                                                                <h3><?php _e('Column range','wpdatatables'); ?></h3>
                                                                <div class="chart_column_picker_container">
                                                                    <div class="existing_columns">
                                                                        <h3><?php _e('Columns in the data source','wpdatatables'); ?></h3>
                                                                        <div class="columns_block_selecter_buttons"><small><?php _e('Select', 'wpdatatables'); ?> <a href="#" class="select_all_columns"><?php _e('all','wpdatatables'); ?></a> | <a href="#" class="deselect_all_columns"><?php _e('none','wpdatatables'); ?></a></small></div>
                                                                        <div class="existing_columns_container">
                                                                        </div>
                                                                    </div>
                                                                    <div class="picker_column">
                                                                        <button class="button" id="wdtAddAllChartColumns"><?php _e('Add all','wpdatatables'); ?> &gt;&gt;</button>
                                                                        <button class="button" id="wdtAddChartColumns"><?php _e('Add','wpdatatables'); ?> &gt;&gt;</button>
                                                                        <button class="button" id="wdtRemoveChartColumns">&lt;&lt; <?php _e('Remove','wpdatatables'); ?></button>
                                                                        <button class="button" id="wdtRemoveAllChartColumns">&lt;&lt; <?php _e('Remove all','wpdatatables'); ?></button>
                                                                    </div>
                                                                    <div class="chosen_columns">
                                                                        <h3><?php _e('Columns used in the chart','wpdatatables'); ?></h3>
                                                                        <div class="columns_block_selecter_buttons"><small><?php _e('Select', 'wpdatatables'); ?> <a href="#" class="select_all_columns"><?php _e('all','wpdatatables'); ?></a> | <a href="#" class="deselect_all_columns"><?php _e('none','wpdatatables'); ?></a></small></div>
                                                                        <div class="strings_error" style="display:none"><?php _e( 'Please do not add more then one string (image, email, URL) column since only one can be used as a label','wpdatatables' ); ?></div>
                                                                        <div class="min_columns_error" style="display:none"><?php _e( 'Minimum count of columns for this chart type is ','wpdatatables' ); ?><span class="columns"></span></div>
                                                                        <div class="max_columns_error" style="display:none"><?php _e( 'Maximum count of columns for this chart type is ','wpdatatables' ); ?><span class="columns"></span></div>
                                                                        <div class="chosen_columns_container">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                    </tr>
                                                    <tr>
                                                            <td colspan="2">
                                                                <h3><?php _e('Row range','wpdatatables'); ?></h3>
                                                                <small><?php _e('If you do not want data from all the table rows to be in the chart, you can pick the range manually. Please note that if the data set is large the range picker can load slowly or even cause an out of memory error.','wpdatatables');?>.</small><br/>
                                                                <select id="wdt_chart_row_range_type">
                                                                    <option value="all_rows"><?php _e('All rows (default)','wpdatatables'); ?></option>
                                                                    <option value="pick_rows"><?php _e('Pick range (slow on large datasets)','wpdatatables'); ?></option>
                                                                </select>
                                                                <button class="button" id="openRangePickerBtn" style="display:none"><?php _e('Range picker...','wpdatatables'); ?></button><br/>
                                                                <div id="rangePickedInfo"><?php _e('Rows picked','wpdatatables'); ?>: <span class="rowspicked"><?php _e('All','wpdatatables');?></span></div>
                                                            </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">
                                                            <h3><?php _e('Charts data filtering','wpdatatables'); ?></h3>
                                                            <input type="checkbox" id="followTableFiltering" /> <label for="followTableFiltering"><?php _e('Follow table filtering','wpdatatables'); ?></label><br/>
                                                            <small><?php _e('Set this to enabled if you would like the data disaplyed in the chart to update when you filter the table','wpdatatables'); ?></small>
                                                        </td>
                                                    </tr>
                                                    </table>
                                            </fieldset>
                                        </div>
                                     
                                        <div class="chartWizardStep step4" data-step="step4" style="display: none">
                                            <fieldset style="margin: 10px;">
                                                <h3><?php _e( 'Formatting options','wpdatatables' ); ?></h3>
                                                <table style="width: 100%">
                                                <tr>
                                                    <td style="width: 250px">
                                                        <label for="chart_title"><span><strong><?php _e('Chart title','wpdatatables');?></strong></span></label><br/>
                                                        <span class="description"><small><?php _e('Do you want to show the chart title on the page?','wpdatatables');?>.</small></span>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" id="show_chart_title" checked="checked" /> <label for="show_chart_title"><?php _e('Show chart title on the page','wpdatatables'); ?></label><br/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="chart_width"><span><strong><?php _e('Chart width','wpdatatables');?></strong></span></label><br/>
                                                        <span class="description"><small><?php _e('The width of the chart','wpdatatables');?>.</small></span>
                                                    </td>
                                                    <td>
                                                        <input type="number" id="chart_width" value="400" /><br/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="chart_height"><span><strong><?php _e('Chart height','wpdatatables');?></strong></span></label><br/>
                                                        <span class="description"><small><?php _e('The height of the chart','wpdatatables');?>.</small></span>
                                                    </td>
                                                    <td>
                                                        <input type="number" id="chart_height" value="400" /><br/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="chart_subtitle"><span><strong><?php _e('Series settings','wpdatatables');?></strong></span></label><br/>
                                                        <span class="description"><small><?php _e('If you want to redefine the series labels and colors you can do it here','wpdatatables');?>.</small></span>
                                                    </td>
                                                    <td>
                                                        <div id="series_settings_container">
                                                            
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="show_grid"><span><strong><?php _e('Axes settings','wpdatatables');?></strong></span></label>
                                                    </td>
                                                    <td>
                                                        <label for="horizontal_axis_label"><?php _e('Horizontal axis label','wpdatatables'); ?></label> <input type="text" id="horizontal_axis_label" value="" /><br/>
                                                        <label for="vertical_axis_label"><?php _e('Vertical axis label','wpdatatables'); ?></label> <input type="text" id="vertical_axis_label" value="" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="show_grid"><span><strong><?php _e('Grid settings','wpdatatables');?></strong></span></label>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" id="show_grid" checked="checked" /> <label for="show_grid"><?php _e('Show grid','wpdatatables'); ?></label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="show_legend"><span><strong><?php _e('Show legend','wpdatatables');?></strong></span></label>
                                                    </td>
                                                    <td>
                                                        <input type="checkbox" id="show_legend" checked="checked" /> <label for="show_legend"><?php _e('Show legend','wpdatatables'); ?></label>
                                                    </td>
                                                </tr>                                                
                                                </table>
                                            </fieldset>
                                        </div>
                                     
                                        <div class="chartWizardStep step5" data-step="step5" style="display: none">
                                            <fieldset style="margin: 10px;">
                                                <table style="width: 100%">
                                                <tr>
                                                    <td colspan="2">
                                                            <div id="googleChartContainer">
                                                            </div>
                                                    </td>
                                                </tr>
                                                </table>
                                            </fieldset>
                                        </div>
                                        
                                        <div class="chartWizardStep step6" data-step="step6" style="display: none">
                                            <fieldset style="margin: 10px;">
                                                    <label><?php _e('Paste this shortcode to render this chart','wpdatatables'); ?></label>
                                                    <div id="wdt_chart_shortcode_container">
                                                    </div>
                                                </table>
                                            </fieldset>
                                        </div>
                                        
                                    </div>
                                    
                                    <button class="button" style="display:none;" id="prevStep"><?php _e('&lt;&lt; Previous','wpdatatables'); ?></button>
                                    <button class="button" style="display:none;" id="nextStep"><?php _e('Next &gt;&gt;','wpdatatables'); ?></button>
                                    <button class="button" style="display:none;" id="finishButton"><?php _e('Finish','wpdatatables'); ?></button>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

<!-- Range picker  -->
<div class="remodal wpDataTables wdtRemodal pickRange">

    <h1><?php _e('Pick Range','wpdatatable'); ?></h1>
        
        <div id="pickRangeTableContainer">
        </div>
    
        <button class="btn" id="cancelPickRange"><?php _e( 'Cancel', 'wpdatatables' ); ?></button>
        <button class="btn" id="submitPickRange"><?php _e( 'OK', 'wpdatatables' ); ?></button>
    
</div>

<script id="wdtChartColumnBlock" type="text/x-jsrender">
    {{for columns}}
        <div class="chart_column_block {{:column_type}}" data-column_id="{{:id}}" data-orig_header="{{:orig_header}}"><strong>{{:display_header}}</strong> ({{:column_type}})</div>
    {{/for}}
</script>

<script id="wdtChartSeriesSettingBlock" type="text/x-jsrender">
    {{for series}}
        <div class="chart_series_block" data-orig_header="{{>orig_header}}">
            <div class="chart_series_label">
                <?php _e('Label','wpdatatables'); ?>: <input type="text" class="series_label" value="{{>label}}" /> 
            </div>
            <div class="chart_series_color">
                <?php _e('Color','wpdatatables'); ?>: <input type="text" class="series_color" value="{{>color}}" style="display: none !important"/> 
            </div>
        </div>
    {{/for}}
</script>

<script id="rangePickerBlock" type="text/x-jsrender">
    <table class="rangePickerTable">
         <thead>
            <tr>
               <th>
               </th>
               {{for columnHeaders}}
                <th data-column_header="{{:header}}" data-column_id={{:id}}>
                    {{:header}}<br/>
                   <input type="checkbox" class="pickColumnRange" {{if checked}}checked="checked"{{/if}} />
                </th>
               {{/for}}
            </tr>
         </thead>
         <tbody>
            {{for tableData}}
            <tr data-index={{:#index}}>
                <td class="pickRow">
                    <input type="checkbox" class="addRowToRange" />
                </td>
                {{props :}}
                <td data-column_header="{{>key}}">{{>prop}}</td>
                {{/props}}
            </tr>
            {{/for}}
        </tbody>
    </table>
</script>